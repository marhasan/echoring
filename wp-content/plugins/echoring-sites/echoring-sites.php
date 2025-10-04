<?php
/**
 * Plugin Name: EchoRing Sites
 * Plugin URI: https://echoring.com
 * Description: Custom post type for managing website reviews and details
 * Version: 1.1.0
 * Author: EchoRing
 * License: GPL v2 or later
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class EchoRingSites {
    
    public function __construct() {
        add_action('init', array($this, 'register_post_type'));
        if (get_option('echoring_enable_types', 1)) {
            add_action('init', array($this, 'register_site_type_taxonomy'));
            add_action('add_site_type_form_fields', array($this, 'site_type_add_form_fields'));
            add_action('site_type_edit_form_fields', array($this, 'site_type_edit_form_fields'), 10, 2);
            add_action('created_site_type', array($this, 'save_site_type_image'), 10, 2);
            add_action('edited_site_type', array($this, 'save_site_type_image'), 10, 2);
            add_action('admin_enqueue_scripts', array($this, 'site_type_admin_scripts'));
        }
        if (get_option('echoring_enable_languages', 1)) {
            add_action('init', array($this, 'register_site_language_taxonomy'));
            add_action('add_site_language_form_fields', array($this, 'site_language_add_form_fields'));
            add_action('site_language_edit_form_fields', array($this, 'site_language_edit_form_fields'), 10, 2);
            add_action('created_site_language', array($this, 'save_site_language_image'), 10, 2);
            add_action('edited_site_language', array($this, 'save_site_language_image'), 10, 2);
            add_action('admin_enqueue_scripts', array($this, 'site_language_admin_scripts'));
        }
        if (get_option('echoring_enable_features', 1)) {
            add_action('init', array($this, 'register_site_feature_taxonomy'));
            add_action('add_site_feature_form_fields', array($this, 'site_feature_add_form_fields'));
            add_action('site_feature_edit_form_fields', array($this, 'site_feature_edit_form_fields'), 10, 2);
            add_action('created_site_feature', array($this, 'save_site_feature_image'), 10, 2);
            add_action('edited_site_feature', array($this, 'save_site_feature_image'), 10, 2);
            add_action('admin_enqueue_scripts', array($this, 'site_feature_admin_scripts'));
        }
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_menu', array($this, 'add_update_management_pages'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Add custom template for single site posts
        add_filter('single_template', array($this, 'load_single_template'));
        
        // Add AJAX handlers for update management
        add_action('wp_ajax_mark_site_updated', array($this, 'ajax_mark_site_updated'));
        add_action('wp_ajax_reset_site_updates', array($this, 'ajax_reset_site_updates'));
        add_action('wp_ajax_approve_pending_update', array($this, 'ajax_approve_pending_update'));
        add_action('wp_ajax_reject_pending_update', array($this, 'ajax_reject_pending_update'));
        add_action('wp_ajax_get_pending_update', array($this, 'ajax_get_pending_update'));
        add_action('wp_ajax_mark_as_new_site', array($this, 'ajax_mark_as_new_site'));
        add_action('wp_ajax_reset_new_site_status', array($this, 'ajax_reset_new_site_status'));
        add_action('wp_ajax_reject_site_submission', array($this, 'ajax_reject_site_submission'));
        add_action('wp_ajax_approve_site_submission', array($this, 'ajax_approve_site_submission'));
    }
    
    public function register_post_type() {
        $labels = array(
            'name' => 'Sites',
            'singular_name' => 'Site',
            'menu_name' => 'Sites',
            'add_new' => 'Add New Site',
            'add_new_item' => 'Add New Site',
            'edit_item' => 'Edit Site',
            'new_item' => 'New Site',
            'view_item' => 'View Site',
            'search_items' => 'Search Sites',
            'not_found' => 'No sites found',
            'not_found_in_trash' => 'No sites found in trash'
        );
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'site'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-admin-links',
            'supports' => array('title', 'editor', 'thumbnail', 'comments')
        );
        
        register_post_type('site', $args);
    }

    public function register_site_type_taxonomy() {
        $labels = array(
            'name'              => _x('Types', 'taxonomy general name', 'echoring-sites'),
            'singular_name'     => _x('Type', 'taxonomy singular name', 'echoring-sites'),
            'search_items'      => __('Search Types', 'echoring-sites'),
            'all_items'         => __('All Types', 'echoring-sites'),
            'parent_item'       => __('Parent Type', 'echoring-sites'),
            'parent_item_colon' => __('Parent Type:', 'echoring-sites'),
            'edit_item'         => __('Edit Type', 'echoring-sites'),
            'update_item'       => __('Update Type', 'echoring-sites'),
            'add_new_item'      => __('Add New Type', 'echoring-sites'),
            'new_item_name'     => __('New Type Name', 'echoring-sites'),
            'menu_name'         => __('Types', 'echoring-sites'),
        );

        $args = array(
            'hierarchical'      => true, // changed from false to true
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'site-type'),
        );

        register_taxonomy('site_type', array('site'), $args);
    }

    public function register_site_language_taxonomy() {
        $labels = array(
            'name'              => _x('Languages', 'taxonomy general name', 'echoring-sites'),
            'singular_name'     => _x('Language', 'taxonomy singular name', 'echoring-sites'),
            'search_items'      => __('Search Languages', 'echoring-sites'),
            'all_items'         => __('All Languages', 'echoring-sites'),
            'parent_item'       => __('Parent Language', 'echoring-sites'),
            'parent_item_colon' => __('Parent Language:', 'echoring-sites'),
            'edit_item'         => __('Edit Language', 'echoring-sites'),
            'update_item'       => __('Update Language', 'echoring-sites'),
            'add_new_item'      => __('Add New Language', 'echoring-sites'),
            'new_item_name'     => __('New Language Name', 'echoring-sites'),
            'menu_name'         => __('Languages', 'echoring-sites'),
        );
        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'site-language'),
        );
        register_taxonomy('site_language', array('site'), $args);
    }

    public function register_site_feature_taxonomy() {
        $labels = array(
            'name'              => _x('Features', 'taxonomy general name', 'echoring-sites'),
            'singular_name'     => _x('Feature', 'taxonomy singular name', 'echoring-sites'),
            'search_items'      => __('Search Features', 'echoring-sites'),
            'all_items'         => __('All Features', 'echoring-sites'),
            'parent_item'       => __('Parent Feature', 'echoring-sites'),
            'parent_item_colon' => __('Parent Feature:', 'echoring-sites'),
            'edit_item'         => __('Edit Feature', 'echoring-sites'),
            'update_item'       => __('Update Feature', 'echoring-sites'),
            'add_new_item'      => __('Add New Feature', 'echoring-sites'),
            'new_item_name'     => __('New Feature Name', 'echoring-sites'),
            'menu_name'         => __('Features', 'echoring-sites'),
        );
        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'site-feature'),
            'meta_box_cb'       => false, // Disable default meta box
        );
        register_taxonomy('site_feature', array('site'), $args);
    }
    
    public function add_meta_boxes() {
        add_meta_box(
            'site_details',
            'Site Details',
            array($this, 'site_details_meta_box'),
            'site',
            'normal',
            'high'
        );
        
        add_meta_box(
            'site_ratings',
            'Site Ratings & Categories',
            array($this, 'site_ratings_meta_box'),
            'site',
            'side',
            'default'
        );
        
        add_meta_box(
            'site_content',
            'Site Content & Features',
            array($this, 'site_content_meta_box'),
            'site',
            'normal',
            'default'
        );
        
        add_meta_box(
            'site_reviews',
            'Site Reviews',
            array($this, 'site_reviews_meta_box'),
            'site',
            'normal',
            'default'
        );
        
        add_meta_box(
            'site_update_status',
            'Update Status',
            array($this, 'site_update_status_meta_box'),
            'site',
            'side',
            'low'
        );
    }
    
    public function site_details_meta_box($post) {
        wp_nonce_field('site_details_nonce', 'site_details_nonce');
        
        $site_url = get_post_meta($post->ID, '_site_url', true);
        $webmaster = get_post_meta($post->ID, '_webmaster', true);
        $screenshots = get_post_meta($post->ID, '_screenshots', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="site_url">Site URL</label></th>
                <td>
                    <input type="url" id="site_url" name="site_url" value="<?php echo esc_attr($site_url); ?>" class="regular-text" />
                    <p class="description">Enter the full URL of the website (e.g., https://example.com)</p>
                </td>
            </tr>
            <tr>
                <th><label for="webmaster">Webmaster</label></th>
                <td>
                    <input type="text" id="webmaster" name="webmaster" value="<?php echo esc_attr($webmaster); ?>" class="regular-text" />
                    <p class="description">Name of the webmaster or site owner</p>
                </td>
            </tr>
            <tr>
                <th><label for="screenshots">Screenshots</label></th>
                <td>
                    <div id="screenshots_container">
                        <?php
                        if ($screenshots && is_array($screenshots)) {
                            foreach ($screenshots as $index => $screenshot_id) {
                                $image_url = wp_get_attachment_image_url($screenshot_id, 'thumbnail');
                                $image_full = wp_get_attachment_image_url($screenshot_id, 'full');
                                if ($image_url) {
                                    echo '<div class="screenshot-item" data-attachment-id="' . esc_attr($screenshot_id) . '">';
                                    echo '<img src="' . esc_url($image_url) . '" alt="Screenshot" style="max-width: 150px; height: auto; margin-bottom: 5px;" />';
                                    echo '<input type="hidden" name="screenshots[]" value="' . esc_attr($screenshot_id) . '" />';
                                    echo '<button type="button" class="button remove-screenshot">Remove</button>';
                                    echo '</div>';
                                }
                            }
                        }
                        ?>
                    </div>
                    <button type="button" id="add_screenshot" class="button">Upload Screenshot</button>
                    <p class="description">Upload screenshot images for the website</p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    public function site_ratings_meta_box($post) {
        $rating = get_post_meta($post->ID, '_rating', true);
        // Get available options from settings
        $rating_options = get_option('echoring_rating_options', array());
        ?>
        <p>
            <label for="rating"><strong>Rating:</strong></label><br>
            <select id="rating" name="rating">
                <option value="">Select Rating</option>
                <?php foreach ($rating_options as $option): ?>
                    <option value="<?php echo esc_attr($option); ?>" <?php selected($rating, $option); ?>>
                        <?php echo esc_html($option); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }
    
    public function site_content_meta_box($post) {
        $games = get_post_meta($post->ID, '_games', true);
        $apps = get_post_meta($post->ID, '_apps', true);
        
        // Get current site features from taxonomy terms
        $current_features = wp_get_object_terms($post->ID, 'site_feature', array('fields' => 'ids'));
        if (is_wp_error($current_features)) {
            $current_features = array();
        }
        ?>
        <table class="form-table">
            <?php if (get_option('echoring_enable_games', 1)): ?>
            <tr>
                <th><label for="games">Games</label></th>
                <td>
                    <input type="text" id="games" name="games" value="<?php echo esc_attr($games); ?>" class="regular-text" />
                    <p class="description">Number of games or game-related content</p>
                </td>
            </tr>
            <?php endif; ?>
            <?php if (get_option('echoring_enable_apps', 1)): ?>
            <tr>
                <th><label for="apps">Apps</label></th>
                <td>
                    <input type="text" id="apps" name="apps" value="<?php echo esc_attr($apps); ?>" class="regular-text" />
                    <p class="description">Number of applications or app-related content</p>
                </td>
            </tr>
            <?php endif; ?>
            <?php if (get_option('echoring_enable_features', 1)): ?>
            <tr>
                <td colspan="2">
                    <h4>Site Features</h4>
                    <p class="description">Does your site have the following features?</p>
                    <?php
                    $features = get_terms(array(
                        'taxonomy' => 'site_feature',
                        'hide_empty' => false,
                    ));
                    if (!is_wp_error($features) && !empty($features)) {
                        echo '<table class="widefat">';
                        foreach ($features as $feature) {
                            $is_selected = in_array($feature->term_id, $current_features);
                            $current_value = $is_selected ? 'yes' : 'no';
                            echo '<tr>';
                            echo '<td style="width: 200px;"><strong>' . esc_html($feature->name) . '</strong></td>';
                            echo '<td>';
                            echo '<select name="site_features[' . esc_attr($feature->term_id) . ']" data-feature-id="' . esc_attr($feature->term_id) . '">';
                            echo '<option value="no"' . selected($current_value, 'no', false) . '>No</option>';
                            echo '<option value="yes"' . selected($current_value, 'yes', false) . '>Yes</option>';
                            echo '</select>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    } else {
                        echo '<p><em>No features configured. <a href="' . admin_url('edit-tags.php?taxonomy=site_feature&post_type=site') . '">Manage Features</a></em></p>';
                    }
                    ?>
                </td>
            </tr>
            <?php endif; ?>
        </table>
        <?php
    }
    
    public function site_reviews_meta_box($post) {
        $the_good = get_post_meta($post->ID, '_the_good', true);
        $the_bad = get_post_meta($post->ID, '_the_bad', true);
        
        if (!is_array($the_good)) $the_good = array();
        if (!is_array($the_bad)) $the_bad = array();
        
        ?>
        <h4>The Good</h4>
        <div id="the_good_container">
            <?php
            if ($the_good) {
                foreach ($the_good as $index => $item) {
                    echo '<div class="review-item">';
                    echo '<textarea name="the_good[]" rows="2" class="large-text">' . esc_textarea($item) . '</textarea>';
                    echo '<button type="button" class="button remove-review-item">Remove</button>';
                    echo '</div>';
                }
            }
            ?>
        </div>
        <button type="button" id="add_good_item" class="button">Add Good Point</button>
        
        <h4>The Bad</h4>
        <div id="the_bad_container">
            <?php
            if ($the_bad) {
                foreach ($the_bad as $index => $item) {
                    echo '<div class="review-item">';
                    echo '<textarea name="the_bad[]" rows="2" class="large-text">' . esc_textarea($item) . '</textarea>';
                    echo '<button type="button" class="button remove-review-item">Remove</button>';
                    echo '</div>';
                }
            }
            ?>
        </div>
        <button type="button" id="add_bad_item" class="button">Add Bad Point</button>
        <?php
    }
    
    public function site_update_status_meta_box($post) {
        $is_updated = get_post_meta($post->ID, '_is_updated', true);
        $last_updated = get_post_meta($post->ID, '_last_updated', true);
        $is_new_site = get_post_meta($post->ID, '_is_new_site', true);
        ?>
        <p>
            <strong>Last Updated:</strong><br>
            <small class="last-updated-text"><?php echo esc_html($last_updated ? date('Y-m-d H:i', strtotime($last_updated)) : ''); ?></small>
        </p>
        <p>
            <button type="button" class="button button-primary mark-updated-now" data-site-id="<?php echo esc_attr($post->ID); ?>" <?php if ($is_updated) echo 'disabled'; ?>>
                Mark as Updated Now
            </button>
            <button type="button" class="button button-secondary reset-updates-now" data-site-id="<?php echo esc_attr($post->ID); ?>" <?php if (!$is_updated) echo 'disabled'; ?>>
                Reset Updates
            </button>
        </p>
        <p>
            <button type="button" class="button button-primary mark-new-now" data-site-id="<?php echo esc_attr($post->ID); ?>" <?php if ($is_new_site) echo 'disabled'; ?>>
                Mark as New Now
            </button>
            <button type="button" class="button button-secondary reset-new-now" data-site-id="<?php echo esc_attr($post->ID); ?>" <?php if (!$is_new_site) echo 'disabled'; ?>>
                Reset New Status
            </button>
        </p>
        <?php
    }
    
    public function save_meta_boxes($post_id) {
        // Check nonce
        if (!isset($_POST['site_details_nonce']) || !wp_verify_nonce($_POST['site_details_nonce'], 'site_details_nonce')) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save site details
        if (isset($_POST['site_url'])) {
            update_post_meta($post_id, '_site_url', sanitize_url($_POST['site_url']));
        }
        
        if (isset($_POST['webmaster'])) {
            update_post_meta($post_id, '_webmaster', sanitize_text_field($_POST['webmaster']));
        }
        
        if (isset($_POST['screenshots']) && is_array($_POST['screenshots'])) {
            $screenshots = array_map('intval', $_POST['screenshots']);
            $screenshots = array_filter($screenshots); // Remove empty values
            update_post_meta($post_id, '_screenshots', $screenshots);
        }
        
        // Save ratings
        if (isset($_POST['rating'])) {
            update_post_meta($post_id, '_rating', sanitize_text_field($_POST['rating']));
        }
        
        // Save content fields
        if (isset($_POST['games'])) {
            update_post_meta($post_id, '_games', sanitize_text_field($_POST['games']));
        }
        
        if (isset($_POST['apps'])) {
            update_post_meta($post_id, '_apps', sanitize_text_field($_POST['apps']));
        }
        
        // Save reviews
        if (isset($_POST['the_good']) && is_array($_POST['the_good'])) {
            $the_good = array_map('sanitize_textarea_field', $_POST['the_good']);
            $the_good = array_filter($the_good); // Remove empty values
            update_post_meta($post_id, '_the_good', $the_good);
        }
        
        if (isset($_POST['the_bad']) && is_array($_POST['the_bad'])) {
            $the_bad = array_map('sanitize_textarea_field', $_POST['the_bad']);
            $the_bad = array_filter($the_bad); // Remove empty values
            update_post_meta($post_id, '_the_bad', $the_bad);
        }
        
        // Save site features as taxonomy terms
        // Always process site_features to handle clearing when all are set to 'no'
        $feature_terms = array();
        
        if (isset($_POST['site_features']) && is_array($_POST['site_features'])) {
            foreach ($_POST['site_features'] as $feature_id => $value) {
                $feature_id = intval($feature_id);
                $value = sanitize_text_field($value);
                if ($feature_id > 0 && $value === 'yes') {
                    $feature_terms[] = $feature_id;
                }
            }
        }
        
        // Set the taxonomy terms (only 'yes' features are assigned, empty array clears all)
        wp_set_object_terms($post_id, $feature_terms, 'site_feature');
        
        // Note: Update status and new site status are now managed via AJAX actions only
        // No automatic status changes on post save to prevent unwanted status updates
    }
    
    public function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=site',
            'Site Settings',
            'Settings',
            'manage_options',
            'echoring-sites-settings',
            array($this, 'settings_page')
        );
    }

    public function add_update_management_pages() {
        add_submenu_page(
            'edit.php?post_type=site',
            'Update Management',
            'Updates',
            'manage_options',
            'echoring-sites-updates',
            array($this, 'update_management_page')
        );
    }
    
    public function register_settings() {
        register_setting('echoring_sites_settings', 'echoring_rating_options', array($this, 'sanitize_options_array'));
        register_setting('echoring_sites_settings', 'echoring_language_options', array($this, 'sanitize_options_array'));
        register_setting('echoring_sites_settings', 'echoring_enable_screenshots');
        register_setting('echoring_sites_settings', 'echoring_enable_ratings');
        register_setting('echoring_sites_settings', 'echoring_enable_languages');
        register_setting('echoring_sites_settings', 'echoring_enable_games');
        register_setting('echoring_sites_settings', 'echoring_enable_apps');
        register_setting('echoring_sites_settings', 'echoring_enable_features');
        register_setting('echoring_sites_settings', 'echoring_enable_reviews');
        // Removed echoring_use_improved_admin - always use improved interface
        register_setting('echoring_sites_settings', 'echoring_feature_options', array($this, 'sanitize_options_array'));
    }
    
    public function sanitize_options_array($input) {
        if (is_string($input)) {
            $lines = explode("\n", $input);
            $lines = array_map('trim', $lines);
            $lines = array_filter($lines);
            return $lines;
        }
        return $input;
    }
    
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>EchoRing Sites Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('echoring_sites_settings'); ?>
                
                <h2>Feature Toggles</h2>
                <table class="form-table">
                    <tr>
                        <th>Enable Screenshots</th>
                        <td>
                            <input type="checkbox" name="echoring_enable_screenshots" value="1" 
                                   <?php checked(get_option('echoring_enable_screenshots', 1)); ?> />
                        </td>
                    </tr>
                    <tr>
                        <th>Enable Ratings</th>
                        <td>
                            <input type="checkbox" name="echoring_enable_ratings" value="1" 
                                   <?php checked(get_option('echoring_enable_ratings', 1)); ?> />
                        </td>
                    </tr>
                    <tr>
                        <th>Enable Languages <a href="edit-tags.php?taxonomy=site_language&post_type=site">(Manage)</a></th>
                        <td>
                            <input type="checkbox" name="echoring_enable_languages" value="1" 
                                   <?php checked(get_option('echoring_enable_languages', 1)); ?> />
                        </td>
                    </tr>
                    <tr>
                        <th>Enable Games</th>
                        <td>
                            <input type="checkbox" name="echoring_enable_games" value="1" 
                                   <?php checked(get_option('echoring_enable_games', 1)); ?> />
                        </td>
                    </tr>
                    <tr>
                        <th>Enable Apps</th>
                        <td>
                            <input type="checkbox" name="echoring_enable_apps" value="1" 
                                   <?php checked(get_option('echoring_enable_apps', 1)); ?> />
                        </td>
                    </tr>
                    <tr>
                        <th>Enable Features <a href="edit-tags.php?taxonomy=site_feature&post_type=site">(Manage)</a></th>
                        <td>
                            <input type="checkbox" name="echoring_enable_features" value="1" 
                                   <?php checked(get_option('echoring_enable_features', 1)); ?> />
                        </td>
                    </tr>
                    <tr>
                        <th>Enable Reviews</th>
                        <td>
                            <input type="checkbox" name="echoring_enable_reviews" value="1" 
                                   <?php checked(get_option('echoring_enable_reviews', 1)); ?> />
                        </td>
                    </tr>
                </table>
                
                <!-- Admin Interface section removed - always using improved interface -->
                
                <h2>Options Configuration</h2>
                <table class="form-table">
                    <tr>
                        <th>Rating Options</th>
                        <td>
                            <textarea name="echoring_rating_options" rows="5" cols="50" class="large-text"><?php 
                                $rating_options = get_option('echoring_rating_options', array('1-star', '2-star', '3-star', '4-star', '5-star'));
                                echo esc_textarea(is_array($rating_options) ? implode("\n", $rating_options) : $rating_options);
                            ?></textarea>
                            <p class="description">Enter one rating option per line</p>
                        </td>
                    </tr>
                </table>

                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function update_management_page() {
        // Always use the improved admin interface
        include plugin_dir_path(__FILE__) . 'templates/admin-updates-improved.php';
    }
    
    public function enqueue_scripts() {
        // Frontend scripts if needed
    }
    
    public function admin_enqueue_scripts($hook) {
        global $post_type;
        
        if ($post_type === 'site' || $hook === 'site_page_echoring-sites-updates') {
            wp_enqueue_media(); // Enable WordPress media uploader
            wp_enqueue_script('echoring-sites-admin', plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery'), '1.0.0', true);
            wp_enqueue_style('echoring-sites-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), '1.0.0');
            
            // Add AJAX nonce for update management
            wp_localize_script('echoring-sites-admin', 'echoring_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('echoring_sites_nonce')
            ));
        }
    }
    
    public function load_single_template($template) {
        if (is_singular('site')) {
            $new_template = plugin_dir_path(__FILE__) . 'templates/single-site.php';
            if (file_exists($new_template)) {
                return $new_template;
            }
        }
        return $template;
    }
    
    // Add image field to add form
    public function site_type_add_form_fields($taxonomy) {
        ?>
        <div class="form-field term-group">
            <label for="site_type_image_id">Image</label>
            <input type="hidden" id="site_type_image_id" name="site_type_image_id" value="" />
            <div id="site_type_image_wrapper"></div>
            <button type="button" class="button button-secondary" id="site_type_image_upload">Upload/Add image</button>
        </div>
        <script>
        jQuery(document).ready(function($){
            var frame;
            $('#site_type_image_upload').on('click', function(e){
                e.preventDefault();
                if(frame){ frame.open(); return; }
                frame = wp.media({
                    title: 'Select or Upload Image',
                    button: { text: 'Use this image' },
                    multiple: false
                });
                frame.on('select', function(){
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#site_type_image_id').val(attachment.id);
                    $('#site_type_image_wrapper').html('<img src="'+attachment.sizes.thumbnail.url+'" style="max-width:100px;" />');
                });
                frame.open();
            });
        });
        </script>
        <?php
    }

    // Add image field to edit form
    public function site_type_edit_form_fields($term, $taxonomy) {
        $image_id = get_term_meta($term->term_id, 'site_type_image_id', true);
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
        ?>
        <tr class="form-field term-group-wrap">
            <th scope="row"><label for="site_type_image_id">Image</label></th>
            <td>
                <input type="hidden" id="site_type_image_id" name="site_type_image_id" value="<?php echo esc_attr($image_id); ?>" />
                <div id="site_type_image_wrapper">
                    <?php if ($image_url): ?><img src="<?php echo esc_url($image_url); ?>" style="max-width:100px;" /><?php endif; ?>
                </div>
                <button type="button" class="button button-secondary" id="site_type_image_upload">Upload/Add image</button>
            </td>
        </tr>
        <script>
        jQuery(document).ready(function($){
            var frame;
            $('#site_type_image_upload').on('click', function(e){
                e.preventDefault();
                if(frame){ frame.open(); return; }
                frame = wp.media({
                    title: 'Select or Upload Image',
                    button: { text: 'Use this image' },
                    multiple: false
                });
                frame.on('select', function(){
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#site_type_image_id').val(attachment.id);
                    $('#site_type_image_wrapper').html('<img src="'+attachment.sizes.thumbnail.url+'" style="max-width:100px;" />');
                });
                frame.open();
            });
        });
        </script>
        <?php
    }

    // Save image field
    public function save_site_type_image($term_id, $tt_id) {
        if (isset($_POST['site_type_image_id'])) {
            update_term_meta($term_id, 'site_type_image_id', intval($_POST['site_type_image_id']));
        }
    }

    // Enqueue media scripts for taxonomy admin
    public function site_type_admin_scripts($hook) {
        if (isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'site_type') {
            wp_enqueue_media();
        }
    }

    // Add image field to add form (language)
    public function site_language_add_form_fields($taxonomy) {
        ?>
        <div class="form-field term-group">
            <label for="site_language_image_id">Image</label>
            <input type="hidden" id="site_language_image_id" name="site_language_image_id" value="" />
            <div id="site_language_image_wrapper"></div>
            <button type="button" class="button button-secondary" id="site_language_image_upload">Upload/Add image</button>
        </div>
        <script>
        jQuery(document).ready(function($){
            var frame;
            $('#site_language_image_upload').on('click', function(e){
                e.preventDefault();
                if(frame){ frame.open(); return; }
                frame = wp.media({
                    title: 'Select or Upload Image',
                    button: { text: 'Use this image' },
                    multiple: false
                });
                frame.on('select', function(){
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#site_language_image_id').val(attachment.id);
                    $('#site_language_image_wrapper').html('<img src="'+attachment.sizes.thumbnail.url+'" style="max-width:100px;" />');
                });
                frame.open();
            });
        });
        </script>
        <?php
    }

    // Add image field to edit form (language)
    public function site_language_edit_form_fields($term, $taxonomy) {
        $image_id = get_term_meta($term->term_id, 'site_language_image_id', true);
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
        ?>
        <tr class="form-field term-group-wrap">
            <th scope="row"><label for="site_language_image_id">Image</label></th>
            <td>
                <input type="hidden" id="site_language_image_id" name="site_language_image_id" value="<?php echo esc_attr($image_id); ?>" />
                <div id="site_language_image_wrapper">
                    <?php if ($image_url): ?><img src="<?php echo esc_url($image_url); ?>" style="max-width:100px;" /><?php endif; ?>
                </div>
                <button type="button" class="button button-secondary" id="site_language_image_upload">Upload/Add image</button>
            </td>
        </tr>
        <script>
        jQuery(document).ready(function($){
            var frame;
            $('#site_language_image_upload').on('click', function(e){
                e.preventDefault();
                if(frame){ frame.open(); return; }
                frame = wp.media({
                    title: 'Select or Upload Image',
                    button: { text: 'Use this image' },
                    multiple: false
                });
                frame.on('select', function(){
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#site_language_image_id').val(attachment.id);
                    $('#site_language_image_wrapper').html('<img src="'+attachment.sizes.thumbnail.url+'" style="max-width:100px;" />');
                });
                frame.open();
            });
        });
        </script>
        <?php
    }

    // Save image field (language)
    public function save_site_language_image($term_id, $tt_id) {
        if (isset($_POST['site_language_image_id'])) {
            update_term_meta($term_id, 'site_language_image_id', intval($_POST['site_language_image_id']));
        }
    }

    // Enqueue media scripts for taxonomy admin (language)
    public function site_language_admin_scripts($hook) {
        if (isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'site_language') {
            wp_enqueue_media();
        }
    }

    // Add image field to add form (feature)
    public function site_feature_add_form_fields($taxonomy) {
        ?>
        <div class="form-field term-group">
            <label for="site_feature_image_id">Image</label>
            <input type="hidden" id="site_feature_image_id" name="site_feature_image_id" value="" />
            <div id="site_feature_image_wrapper"></div>
            <button type="button" class="button button-secondary" id="site_feature_image_upload">Upload/Add image</button>
        </div>
        <script>
        jQuery(document).ready(function($){
            var frame;
            $('#site_feature_image_upload').on('click', function(e){
                e.preventDefault();
                if(frame){ frame.open(); return; }
                frame = wp.media({
                    title: 'Select or Upload Image',
                    button: { text: 'Use this image' },
                    multiple: false
                });
                frame.on('select', function(){
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#site_feature_image_id').val(attachment.id);
                    $('#site_feature_image_wrapper').html('<img src="'+attachment.sizes.thumbnail.url+'" style="max-width:100px;" />');
                });
                frame.open();
            });
        });
        </script>
        <?php
    }

    // Add image field to edit form (feature)
    public function site_feature_edit_form_fields($term, $taxonomy) {
        $image_id = get_term_meta($term->term_id, 'site_feature_image_id', true);
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
        ?>
        <tr class="form-field term-group-wrap">
            <th scope="row"><label for="site_feature_image_id">Image</label></th>
            <td>
                <input type="hidden" id="site_feature_image_id" name="site_feature_image_id" value="<?php echo esc_attr($image_id); ?>" />
                <div id="site_feature_image_wrapper">
                    <?php if ($image_url): ?><img src="<?php echo esc_url($image_url); ?>" style="max-width:100px;" /><?php endif; ?>
                </div>
                <button type="button" class="button button-secondary" id="site_feature_image_upload">Upload/Add image</button>
            </td>
        </tr>
        <script>
        jQuery(document).ready(function($){
            var frame;
            $('#site_feature_image_upload').on('click', function(e){
                e.preventDefault();
                if(frame){ frame.open(); return; }
                frame = wp.media({
                    title: 'Select or Upload Image',
                    button: { text: 'Use this image' },
                    multiple: false
                });
                frame.on('select', function(){
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#site_feature_image_id').val(attachment.id);
                    $('#site_feature_image_wrapper').html('<img src="'+attachment.sizes.thumbnail.url+'" style="max-width:100px;" />');
                });
                frame.open();
            });
        });
        </script>
        <?php
    }

    // Save image field (feature)
    public function save_site_feature_image($term_id, $tt_id) {
        if (isset($_POST['site_feature_image_id'])) {
            update_term_meta($term_id, 'site_feature_image_id', intval($_POST['site_feature_image_id']));
        }
    }

    // Enqueue media scripts for taxonomy admin (feature)
    public function site_feature_admin_scripts($hook) {
        if (isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'site_feature') {
            wp_enqueue_media();
        }
    }

    // Helper: get image URL for a type term
    public static function get_type_image_url($term_id, $size = 'thumbnail') {
        $image_id = get_term_meta($term_id, 'site_type_image_id', true);
        if ($image_id) {
            return wp_get_attachment_image_url($image_id, $size);
        }
        return false;
    }

    // Helper: get image URL for a language term
    public static function get_language_image_url($term_id, $size = 'thumbnail') {
        $image_id = get_term_meta($term_id, 'site_language_image_id', true);
        if ($image_id) {
            return wp_get_attachment_image_url($image_id, $size);
        }
        return false;
    }

    // Helper: get image URL for a feature term
    public static function get_feature_image_url($term_id, $size = 'thumbnail') {
        $image_id = get_term_meta($term_id, 'site_feature_image_id', true);
        if ($image_id) {
            return wp_get_attachment_image_url($image_id, $size);
        }
        return false;
    }
    
    // Helper: get plugin image URL
    public static function get_plugin_image_url($filename) {
        return plugins_url('images/' . $filename, __FILE__);
    }
    
    // Helper functions for template use
    public static function get_site_url($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        return get_post_meta($post_id, '_site_url', true);
    }
    
    public static function get_webmaster($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        return get_post_meta($post_id, '_webmaster', true);
    }
    
    public static function get_screenshots($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        $screenshot_ids = get_post_meta($post_id, '_screenshots', true);
        
        if (!$screenshot_ids || !is_array($screenshot_ids)) {
            return array();
        }
        
        $screenshots = array();
        foreach ($screenshot_ids as $attachment_id) {
            $attachment = get_post($attachment_id);
            if ($attachment && $attachment->post_type === 'attachment') {
                $screenshots[] = array(
                    'id' => $attachment_id,
                    'url' => wp_get_attachment_image_url($attachment_id, 'full'),
                    'thumbnail' => wp_get_attachment_image_url($attachment_id, 'thumbnail'),
                    'title' => $attachment->post_title,
                    'alt' => get_post_meta($attachment_id, '_wp_attachment_image_alt', true)
                );
            }
        }
        
        return $screenshots;
    }
    
    public static function get_rating($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        return get_post_meta($post_id, '_rating', true);
    }
    
    public static function get_languages($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        return get_post_meta($post_id, '_languages', true);
    }
    
    public static function get_games($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        return get_post_meta($post_id, '_games', true);
    }
    
    public static function get_apps($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        return get_post_meta($post_id, '_apps', true);
    }
    
    public static function get_features($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        return get_post_meta($post_id, '_features', true);
    }
    
    public static function get_the_good($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        return get_post_meta($post_id, '_the_good', true);
    }
    
    public static function get_the_bad($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        return get_post_meta($post_id, '_the_bad', true);
    }
    
    // Helper functions for update management
    public static function is_site_updated($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        return get_post_meta($post_id, '_is_updated', true);
    }
    
    public static function is_site_rejected($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        return get_post_meta($post_id, '_is_rejected', true);
    }
    
    public static function is_new_site($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        return get_post_meta($post_id, '_is_new_site', true);
    }
    
    public static function has_pending_update($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        return get_post_meta($post_id, '_has_pending_update', true);
    }
    
    public static function get_last_updated($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        return get_post_meta($post_id, '_last_updated', true);
    }
    
    public static function mark_site_updated($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        update_post_meta($post_id, '_is_updated', '1');
        update_post_meta($post_id, '_last_updated', current_time('mysql'));
        return true;
    }
    
    public static function reset_site_updates($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        update_post_meta($post_id, '_is_updated', '0');
        // Preserve the last updated date - only reset the status
        update_post_meta($post_id, '_manual_update', '0');
        return true;
    }
    
    public static function should_show_in_updates($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        
        $today = current_time('Y-m-d');
        $pub_date = get_the_time('Y-m-d', $post_id);
        $mod_date = get_the_modified_time('Y-m-d', $post_id);
        $is_manually_updated = get_post_meta($post_id, '_is_updated', true);
        $is_new_site = get_post_meta($post_id, '_is_new_site', true);
        $last_updated = self::get_last_updated($post_id);
        
        // Show if:
        // 1. Marked as new site (regardless of date)
        // 2. Published today AND manually marked as updated (new site)
        // 3. Modified today AND manually marked as updated
        // 4. Manually marked as updated today
        return ($is_new_site) || 
               ($pub_date === $today && $is_manually_updated) || 
               ($mod_date === $today && $is_manually_updated) || 
               ($is_manually_updated && $last_updated && date('Y-m-d', strtotime($last_updated)) === $today);
    }

    public function ajax_mark_site_updated() {
        check_ajax_referer('echoring_sites_nonce', 'nonce');

        $site_id = intval($_POST['site_id']);
        if ($site_id) {
            update_post_meta($site_id, '_is_updated', 1);
            update_post_meta($site_id, '_last_updated', current_time('mysql'));
            wp_send_json_success('Site marked as updated.');
        } else {
            wp_send_json_error('Invalid site ID.');
        }
        wp_die();
    }

    public function ajax_reset_site_updates() {
        check_ajax_referer('echoring_sites_nonce', 'nonce');

        $site_id = intval($_POST['site_id']);
        if ($site_id) {
            update_post_meta($site_id, '_is_updated', 0);
            // Preserve the last updated date - only reset the status
            update_post_meta($site_id, '_manual_update', 0);
            wp_send_json_success('Site updates reset.');
        } else {
            wp_send_json_error('Invalid site ID.');
        }
        wp_die();
    }

    public function ajax_approve_pending_update() {
        check_ajax_referer('echoring_sites_nonce', 'nonce');

        $site_id = intval($_POST['site_id']);
        if (!$site_id) {
            wp_send_json_error('Invalid site ID.');
            wp_die();
        }

        // Get pending update data
        $pending_data = get_post_meta($site_id, '_pending_update_data', true);
        if (!$pending_data) {
            wp_send_json_error('No pending update found.');
            wp_die();
        }

        // Update site with new data
        $update_data = array();
        if (!empty($pending_data['games'])) {
            update_post_meta($site_id, '_games', sanitize_text_field($pending_data['games']));
        }
        if (!empty($pending_data['apps'])) {
            update_post_meta($site_id, '_apps', sanitize_text_field($pending_data['apps']));
        }
        if (!empty($pending_data['features'])) {
            update_post_meta($site_id, '_features', sanitize_textarea_field($pending_data['features']));
        }

        // Mark as updated and clear pending data
        update_post_meta($site_id, '_is_updated', '1');
        update_post_meta($site_id, '_last_updated', current_time('mysql'));
        delete_post_meta($site_id, '_has_pending_update');
        delete_post_meta($site_id, '_pending_update_data');
        delete_post_meta($site_id, '_update_submitted_date');

        wp_send_json_success('Update approved and site marked as updated.');
        wp_die();
    }

    public function ajax_reject_pending_update() {
        check_ajax_referer('echoring_sites_nonce', 'nonce');

        $site_id = intval($_POST['site_id']);
        if (!$site_id) {
            wp_send_json_error('Invalid site ID.');
            wp_die();
        }

        // Clear pending update data
        delete_post_meta($site_id, '_has_pending_update');
        delete_post_meta($site_id, '_pending_update_data');
        delete_post_meta($site_id, '_update_submitted_date');

        wp_send_json_success('Pending update rejected.');
        wp_die();
    }

    public function ajax_get_pending_update() {
        check_ajax_referer('echoring_sites_nonce', 'nonce');

        $site_id = intval($_POST['site_id']);
        if (!$site_id) {
            wp_send_json_error('Invalid site ID.');
            wp_die();
        }

        $pending_data = get_post_meta($site_id, '_pending_update_data', true);
        if (!$pending_data) {
            wp_send_json_error('No pending update found.');
            wp_die();
        }

        // Get current values for comparison
        $current_games = get_post_meta($site_id, '_games', true);
        $current_apps = get_post_meta($site_id, '_apps', true);
        $current_features = get_post_meta($site_id, '_features', true);

        $response = array(
            'success' => true,
            'data' => array(
                'update_type' => $pending_data['update_type'] ?: 'Webmaster Update',
                'priority' => $pending_data['priority'] ?: 'Normal',
                'current_games' => $current_games ?: '0',
                'new_games' => $pending_data['games'] ?: '0',
                'current_apps' => $current_apps ?: '0',
                'new_apps' => $pending_data['apps'] ?: '0',
                'current_features' => $current_features ?: '',
                'new_features' => $pending_data['features'] ?: '',
                'webmaster_notes' => $pending_data['webmaster_notes'] ?: '',
                'submitted_date' => get_post_meta($site_id, '_update_submitted_date', true)
            )
        );

        wp_send_json_success($response['data']);
        wp_die();
    }

    public function ajax_mark_as_new_site() {
        check_ajax_referer('echoring_sites_nonce', 'nonce');

        $site_id = intval($_POST['site_id']);
        if ($site_id) {
            update_post_meta($site_id, '_is_new_site', '1');
            wp_send_json_success('Site marked as new site.');
        } else {
            wp_send_json_error('Invalid site ID.');
        }
        wp_die();
    }

    public function ajax_reset_new_site_status() {
        check_ajax_referer('echoring_sites_nonce', 'nonce');

        $site_id = intval($_POST['site_id']);
        if ($site_id) {
            // Delete the meta key entirely and clean up any invalid values
            delete_post_meta($site_id, '_is_new_site');
            
            // Also clean up any '0' or empty values that might exist
            global $wpdb;
            $wpdb->delete(
                $wpdb->postmeta,
                array(
                    'post_id' => $site_id,
                    'meta_key' => '_is_new_site',
                    'meta_value' => '0'
                )
            );
            
            wp_send_json_success('New site status reset.');
        } else {
            wp_send_json_error('Invalid site ID.');
        }
        wp_die();
    }

    public function ajax_reject_site_submission() {
        check_ajax_referer('echoring_sites_nonce', 'nonce');

        $site_id = intval($_POST['site_id']);
        if (!$site_id) {
            wp_send_json_error('Invalid site ID.');
            wp_die();
        }

        // Mark site as rejected
        update_post_meta($site_id, '_is_rejected', '1');
        update_post_meta($site_id, '_rejection_date', current_time('mysql'));
        
        // Change post status to draft so it's not publicly visible
        wp_update_post(array(
            'ID' => $site_id,
            'post_status' => 'draft'
        ));

        wp_send_json_success('Site submission rejected.');
        wp_die();
    }

    public function ajax_approve_site_submission() {
        check_ajax_referer('echoring_sites_nonce', 'nonce');

        $site_id = intval($_POST['site_id']);
        if (!$site_id) {
            wp_send_json_error('Invalid site ID.');
            wp_die();
        }

        // Clear any rejection status
        delete_post_meta($site_id, '_is_rejected');
        delete_post_meta($site_id, '_rejection_date');
        
        // Mark as new site and publish
        update_post_meta($site_id, '_is_new_site', '1');
        update_post_meta($site_id, '_approval_date', current_time('mysql'));
        
        // Change post status to published
        wp_update_post(array(
            'ID' => $site_id,
            'post_status' => 'publish'
        ));

        wp_send_json_success('Site submission approved and published.');
        wp_die();
    }
}

// Initialize the plugin
new EchoRingSites();

// Activation hook
register_activation_hook(__FILE__, 'echoring_sites_activate');
function echoring_sites_activate() {
    // Set default options
    if (!get_option('echoring_rating_options')) {
        update_option('echoring_rating_options', array('1-star', '2-star', '3-star', '4-star', '5-star'));
    }
    if (!get_option('echoring_language_options')) {
        update_option('echoring_language_options', array('English', 'Spanish', 'French', 'German', 'Italian', 'Portuguese', 'Russian', 'Chinese', 'Japanese', 'Korean'));
    }
    if (!get_option('echoring_feature_options')) {
        update_option('echoring_feature_options', array('Chat', 'Forum', 'Blog', 'Gallery', 'Download', 'Search', 'Newsletter', 'Social Media'));
    }
    
    // Enable all features by default
    update_option('echoring_enable_screenshots', 1);
    update_option('echoring_enable_ratings', 1);
    update_option('echoring_enable_languages', 1);
    update_option('echoring_enable_games', 1);
    update_option('echoring_enable_apps', 1);
    update_option('echoring_enable_features', 1);
    update_option('echoring_enable_reviews', 1);
    
    // Clean up any _is_new_site meta values that are '0' or empty
    global $wpdb;
    $wpdb->delete(
        $wpdb->postmeta,
        array(
            'meta_key' => '_is_new_site',
            'meta_value' => '0'
        )
    );
    
    $wpdb->query(
        "DELETE FROM {$wpdb->postmeta} 
         WHERE meta_key = '_is_new_site' 
         AND (meta_value = '' OR meta_value IS NULL)"
    );
    
    // Migrate existing URL-based screenshots to attachment IDs
    echoring_migrate_screenshots();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

// Migration function for screenshots
function echoring_migrate_screenshots() {
    $sites = get_posts(array(
        'post_type' => 'site',
        'numberposts' => -1,
        'post_status' => 'any'
    ));
    
    foreach ($sites as $site) {
        $screenshots = get_post_meta($site->ID, '_screenshots', true);
        
        // Check if screenshots are URLs (old format) or attachment IDs (new format)
        if ($screenshots && is_array($screenshots)) {
            $needs_migration = false;
            $new_screenshots = array();
            
            foreach ($screenshots as $screenshot) {
                // If it's a URL, we need to migrate
                if (filter_var($screenshot, FILTER_VALIDATE_URL)) {
                    $needs_migration = true;
                    // For now, we'll skip URL-based screenshots during migration
                    // Users will need to re-upload them manually
                } else {
                    // It's already an attachment ID, keep it
                    $new_screenshots[] = intval($screenshot);
                }
            }
            
            if ($needs_migration) {
                update_post_meta($site->ID, '_screenshots', $new_screenshots);
            }
        }
    }
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'echoring_sites_deactivate');
function echoring_sites_deactivate() {
    flush_rewrite_rules();
}