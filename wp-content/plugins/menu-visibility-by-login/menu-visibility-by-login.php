<?php
/**
 * Plugin Name: Menu Visibility by Login Status
 * Plugin URI: https://echoring.com
 * Description: Adds a toggle to menu items to control visibility based on user login status
 * Version: 1.0.0
 * Author: EchoRing
 * Author URI: https://echoring.com
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
        add_filter('wp_setup_nav_menu_item', array($this, 'add_custom_nav_fields'));
        
        // Save menu custom fields
        add_action('wp_update_nav_menu_item', array($this, 'update_custom_nav_fields'), 10, 3);
        
        // Edit menu walker
        add_filter('wp_edit_nav_menu_walker', array($this, 'edit_walker'), 10, 2);
        
        // Filter menu items on display
        add_filter('wp_get_nav_menu_items', array($this, 'filter_menu_items'), 10, 3);
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
        } else {
            delete_post_meta($menu_item_db_id, '_menu_item_visibility_mode');
        }
    }
    
    /**
     * Define new menu walker
     */
    public function edit_walker($walker, $menu_id) {
        return 'Menu_Visibility_Walker_Edit';
    }
    
    /**
     * Filter menu items based on login status
     */
    public function filter_menu_items($items, $menu, $args) {
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

/**
 * Custom walker for menu editor
 */
class Menu_Visibility_Walker_Edit extends Walker_Nav_Menu_Edit {
    
    /**
     * Start the element output
     */
    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $item_output = '';
        parent::start_el($item_output, $item, $depth, $args, $id);
        
        $visibility_mode = get_post_meta($item->ID, '_menu_item_visibility_mode', true);
        
        // Add custom field to menu item settings
        $custom_fields = '
        <p class="field-visibility-mode description description-wide">
            <label for="edit-menu-item-visibility-mode-' . $item->ID . '">
                Visibility Mode<br />
                <select name="menu-item-visibility-mode[' . $item->ID . ']" id="edit-menu-item-visibility-mode-' . $item->ID . '" class="widefat">
                    <option value="" ' . selected($visibility_mode, '', false) . '>Always Show</option>
                    <option value="logged_in" ' . selected($visibility_mode, 'logged_in', false) . '>Show Only When Logged In</option>
                    <option value="logged_out" ' . selected($visibility_mode, 'logged_out', false) . '>Show Only When Logged Out</option>
                </select>
            </label>
        </p>';
        
        // Insert custom field before the "move" links
        $item_output = preg_replace('/(?=<div class="menu-item-actions)/', $custom_fields, $item_output);
        
        $output .= $item_output;
    }
}

// Initialize the plugin
new Menu_Visibility_By_Login();
