<?php
/**
 * Plugin Name: Menu Visibility by Login Status
 * Plugin URI: https://echoring.martinhasan.com
 * Description: Adds a toggle to menu items to control visibility based on user login status
 * Version: 1.0.0
 * Author: EchoRing
 * Author URI: https://echoring.martinhasan.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Menu_Visibility_By_Login {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Add custom fields to menu items
        add_filter('wp_setup_nav_menu_item', array($this, 'add_custom_nav_fields'), 10, 1);

        // Save menu custom fields
        add_action('wp_update_nav_menu_item', array($this, 'update_custom_nav_fields'), 10, 3);

        // Add custom fields to menu item form
        add_action('wp_nav_menu_item_custom_fields', array($this, 'add_custom_fields_to_menu_item'), 20, 4);

        add_filter('wp_get_nav_menu_items', array($this, 'ensure_custom_fields_loaded'), 5, 3);

        // Filter menu items on display
        add_filter('wp_get_nav_menu_items', array($this, 'filter_menu_items'), 10, 3);
    }

    /**
     * Ensure custom fields are loaded for menu items (for AJAX contexts)
     */
    public function ensure_custom_fields_loaded($items, $menu, $args) {
        // Only apply in admin when dealing with menu management
        if (!is_admin() || (isset($_GET['page']) && $_GET['page'] !== 'nav-menus')) {
            return $items;
        }

        foreach ($items as $item) {
            if (!isset($item->visibility_mode)) {
                $item->visibility_mode = get_post_meta($item->ID, '_menu_item_visibility_mode', true);
            }
        }

        return $items;
    }

    /**
     * Add custom fields to menu items
     */
    public function add_custom_nav_fields($menu_item) {
        $menu_item->visibility_mode = get_post_meta($menu_item->ID, '_menu_item_visibility_mode', true);
        return $menu_item;
    }

    /**
     * Save custom field value
     */
    public function update_custom_nav_fields($menu_id, $menu_item_db_id, $args) {
        if (isset($_POST['menu-item-visibility-mode'][$menu_item_db_id])) {
            $visibility_value = sanitize_text_field($_POST['menu-item-visibility-mode'][$menu_item_db_id]);
            update_post_meta($menu_item_db_id, '_menu_item_visibility_mode', $visibility_value);

            // Clear post cache to ensure fresh data on next load
            clean_post_cache($menu_item_db_id);
        } else {
            delete_post_meta($menu_item_db_id, '_menu_item_visibility_mode');
            clean_post_cache($menu_item_db_id);
        }
    }

    /**
     * Add custom fields to menu item form
     */
    public function add_custom_fields_to_menu_item($item_id, $item, $depth, $args) {
        // Use the item object that WordPress has already processed with our custom field
        $visibility_mode = isset($item->visibility_mode) ? $item->visibility_mode : '';

        // Fallback to post meta if the item object doesn't have our custom field
        if (empty($visibility_mode)) {
            $visibility_mode = get_post_meta($item_id, '_menu_item_visibility_mode', true);
        }

        // Ensure we have a valid value for the selected() function
        $visibility_mode = !empty($visibility_mode) ? $visibility_mode : '';

        ?>
        <p class="field-visibility-mode description description-wide">
            <label for="edit-menu-item-visibility-mode-<?php echo $item_id; ?>">
                <?php _e('Visibility Mode'); ?><br />
                <select name="menu-item-visibility-mode[<?php echo $item_id; ?>]" id="edit-menu-item-visibility-mode-<?php echo $item_id; ?>" class="widefat">
                    <option value="" <?php selected($visibility_mode, ''); ?>>Always Show</option>
                    <option value="logged_in" <?php selected($visibility_mode, 'logged_in'); ?>>Show Only When Logged In</option>
                    <option value="logged_out" <?php selected($visibility_mode, 'logged_out'); ?>>Show Only When Logged Out</option>
                </select>
            </label>
        </p>
        <?php
    }

    /**
     * Enqueue scripts for menu editor
     */
    public function enqueue_menu_scripts($hook) {
        if ($hook !== 'nav-menus.php') {
            return;
        }

        wp_add_inline_script('nav-menu', "
        (function($) {
            $(document).on('menu-item-added', function() {
                // Refresh custom field values after menu item is added
                setTimeout(function() {
                    $('.field-visibility-mode select').each(function() {
                        var \$select = $(this);
                        var item_id = \$select.attr('id').replace('edit-menu-item-visibility-mode-', '');
                        if (item_id) {
                            // Trigger change to refresh the selected option
                            \$select.trigger('change');
                        }
                    });
                }, 100);
            });
        })(jQuery);
        ");
    }
    
    /**
     * Filter menu items based on login status
     */
    public function filter_menu_items($items, $menu, $args) {
        // Only filter on frontend, not in admin
        if (is_admin()) {
            return $items;
        }

        $logged_in = is_user_logged_in();

        foreach ($items as $key => $item) {
            $visibility_mode = get_post_meta($item->ID, '_menu_item_visibility_mode', true);

            // If visibility mode is set
            if (!empty($visibility_mode)) {
                // Hide if user is logged in and mode is 'logged_out'
                if ($logged_in && $visibility_mode === 'logged_out') {
                    unset($items[$key]);
                }
                // Hide if user is logged out and mode is 'logged_in'
                elseif (!$logged_in && $visibility_mode === 'logged_in') {
                    unset($items[$key]);
                }
            }
        }

        return $items;
    }
}

// Initialize the plugin
new Menu_Visibility_By_Login();
