<?php
/**
 * Temporary file to create the webmaster profile page
 * Run this file once to create the profile page, then delete it
 */

// Check if webmaster profile page exists
$profile_page = get_page_by_path('webmaster-profile');

if (!$profile_page) {
    // Create the profile page
    $page_data = array(
        'post_title'    => 'Webmaster Profile',
        'post_name'     => 'webmaster-profile',
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_author'   => 1,
    );
    
    $page_id = wp_insert_post($page_data);
    
    if (!is_wp_error($page_id)) {
        // Set the page template
        update_post_meta($page_id, '_wp_page_template', 'webmaster-profile.php');
        echo "Webmaster profile page created successfully!";
    } else {
        echo "Error creating profile page: " . $page_id->get_error_message();
    }
} else {
    echo "Webmaster profile page already exists!";
}

// Flush rewrite rules
flush_rewrite_rules();
echo "\nRewrite rules flushed!";
?>