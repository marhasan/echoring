<?php
/**
 * Plugin Name: EchoRing Editorials
 * Description: Custom post type and templates for managing editorial interviews
 * Version: 1.0.0
 * Author: EchoRing
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class EchoRing_Editorials {
    
    public function __construct() {
        add_action('init', array($this, 'register_editorial_post_type'));
        add_action('add_meta_boxes', array($this, 'add_editorial_meta_boxes'));
        add_action('save_post', array($this, 'save_editorial_meta'));
        add_filter('single_template', array($this, 'editorial_single_template'));
        add_filter('archive_template', array($this, 'editorial_archive_template'));
        add_shortcode('editorials_table', array($this, 'editorials_table_shortcode'));
    }
    
    public function register_editorial_post_type() {
        $labels = array(
            'name' => 'Editorials',
            'singular_name' => 'Editorial',
            'menu_name' => 'Editorials',
            'name_admin_bar' => 'Editorial',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Editorial',
            'new_item' => 'New Editorial',
            'edit_item' => 'Edit Editorial',
            'view_item' => 'View Editorial',
            'all_items' => 'All Editorials',
            'search_items' => 'Search Editorials',
            'parent_item_colon' => 'Parent Editorials:',
            'not_found' => 'No editorials found.',
            'not_found_in_trash' => 'No editorials found in Trash.'
        );
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'editorial'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 5,
            'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
            'menu_icon' => 'dashicons-format-chat',
        );
        
        register_post_type('editorial', $args);
    }
    
    public function add_editorial_meta_boxes() {
        add_meta_box(
            'editorial_details',
            'Editorial Details',
            array($this, 'editorial_details_callback'),
            'editorial',
            'normal',
            'high'
        );
    }
    
    public function editorial_details_callback($post) {
        wp_nonce_field('editorial_details_nonce', 'editorial_details_nonce_field');
        
        $interviewer = get_post_meta($post->ID, '_editorial_interviewer', true);
        $editorial_type = get_post_meta($post->ID, '_editorial_type', true);
        $download_link = get_post_meta($post->ID, '_editorial_download', true);
        
        echo '<p><label for="editorial_interviewer"><strong>Interviewer:</strong></label><br>';
        echo '<input type="text" id="editorial_interviewer" name="editorial_interviewer" value="' . esc_attr($interviewer) . '" size="50" placeholder="Enter interviewer name"></p>';
        
        echo '<p><label for="editorial_type"><strong>Editorial Type:</strong></label><br>';
        echo '<input type="text" id="editorial_type" name="editorial_type" value="' . esc_attr($editorial_type) . '" size="50" placeholder="e.g., Interview, Review"></p>';
        
        echo '<p><label for="editorial_download"><strong>Download Link:</strong></label><br>';
        echo '<input type="text" id="editorial_download" name="editorial_download" value="' . esc_url($download_link) . '" size="50" placeholder="Optional download link"></p>';
    }
    
    public function save_editorial_meta($post_id) {
        if (!isset($_POST['editorial_details_nonce_field']) || 
            !wp_verify_nonce($_POST['editorial_details_nonce_field'], 'editorial_details_nonce')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (isset($_POST['editorial_interviewer'])) {
            update_post_meta($post_id, '_editorial_interviewer', sanitize_text_field($_POST['editorial_interviewer']));
        }
        
        if (isset($_POST['editorial_type'])) {
            update_post_meta($post_id, '_editorial_type', sanitize_text_field($_POST['editorial_type']));
        }
        
        if (isset($_POST['editorial_download'])) {
            update_post_meta($post_id, '_editorial_download', esc_url_raw($_POST['editorial_download']));
        }
    }
    
    public function editorial_single_template($template) {
        if (get_post_type() === 'editorial') {
            $plugin_template = plugin_dir_path(__FILE__) . 'templates/single-editorial.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        return $template;
    }
    
    public function editorial_archive_template($template) {
        if (is_post_type_archive('editorial')) {
            $plugin_template = plugin_dir_path(__FILE__) . 'templates/archive-editorial.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        return $template;
    }
    
    public function editorials_table_shortcode($atts) {
        $atts = shortcode_atts(array(
            'count' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        ), $atts);
        
        ob_start();
        ?>
        <table border="0" align="CENTER" cellpadding="2" cellspacing="2">
            <tbody>
                <?php
                $query = new WP_Query(array(
                    'post_type' => 'editorial',
                    'posts_per_page' => $atts['count'],
                    'orderby' => $atts['orderby'],
                    'order' => $atts['order']
                ));
                
                if ($query->have_posts()) : 
                    while ($query->have_posts()) : $query->the_post(); 
                        $interviewer = get_post_meta(get_the_ID(), '_editorial_interviewer', true);
                        ?>
                        <tr>
                            <td><font face="verdana" size="1"><b>Interviewer:</b> <?php echo esc_html($interviewer ? $interviewer : 'N/A'); ?></font></td>
                            <td><font face="verdana" size="1"><b>Subject:</b> <?php the_title(); ?></font></td>
                            <td><font face="verdana" size="1"><b>Date:</b> <?php the_time('F Y'); ?></font></td>
                            <td><font face="verdana" size="1"><a href="<?php the_permalink(); ?>">View Interview</a></font></td>
                        </tr>
                        <?php 
                    endwhile;
                    wp_reset_postdata();
                else : 
                    echo '<tr><td colspan="4"><font face="verdana" size="1">No editorials found.</font></td></tr>';
                endif;
                ?>
            </tbody>
        </table>
        <?php
        return ob_get_clean();
    }
}

new EchoRing_Editorials();

// Flush rewrite rules on activation
function editorials_plugin_activate() {
    $editorials = new EchoRing_Editorials();
    $editorials->register_editorial_post_type();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'editorials_plugin_activate');

function editorials_plugin_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'editorials_plugin_deactivate');