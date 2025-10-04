<?php
/**
 * Template Name: Webmaster Profile
 */

get_header(); 

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

$current_user = wp_get_current_user();

// Handle profile updates
if (isset($_POST['action']) && $_POST['action'] === 'update_profile' && wp_verify_nonce($_POST['profile_nonce'], 'update_profile')) {
    $user_id = $current_user->ID;
    
    $userdata = array(
        'ID' => $user_id,
        'display_name' => sanitize_text_field($_POST['display_name']),
        'user_email' => sanitize_email($_POST['user_email']),
        'description' => sanitize_textarea_field($_POST['description'])
    );
    
    $result = wp_update_user($userdata);
    
    if (!is_wp_error($result)) {
        $success_message = 'Profile updated successfully!';
    } else {
        $error_message = $result->get_error_message();
    }
}

// Handle website update submission
if (isset($_POST['action']) && $_POST['action'] === 'submit_update' && wp_verify_nonce($_POST['update_nonce'], 'submit_update')) {
    $site_id = intval($_POST['site_id']);
    $site = get_post($site_id);
    
    if ($site && $site->post_author == $current_user->ID) {
        // Store update request as post meta
        $update_data = array(
            'games' => sanitize_text_field($_POST['games_count']),
            'apps' => sanitize_text_field($_POST['apps_count']),
            'features' => sanitize_textarea_field($_POST['new_features']),
            'webmaster_notes' => sanitize_textarea_field($_POST['update_description']),
            'submitted_date' => current_time('mysql'),
            'status' => 'pending'
        );
        
        // Mark site as having a pending update
        update_post_meta($site_id, '_has_pending_update', '1');
        update_post_meta($site_id, '_pending_update_data', $update_data);
        update_post_meta($site_id, '_update_submitted_date', current_time('mysql'));
        
        // Reset the updated status so it appears in admin updates
        update_post_meta($site_id, '_is_updated', '0');
        
        $update_success = 'Update request submitted successfully! It will be reviewed by an admin.';
    } else {
        $update_error = 'Invalid site selection or you do not own this site.';
    }
}

// Handle site resubmission
if (isset($_POST['action']) && $_POST['action'] === 'resubmit_site' && wp_verify_nonce($_POST['resubmit_nonce'], 'resubmit_site')) {
    $site_id = intval($_POST['site_id']);
    $site = get_post($site_id);
    
    if ($site && $site->post_author == $current_user->ID) {
        // Clear rejection status
        delete_post_meta($site_id, '_is_rejected');
        delete_post_meta($site_id, '_rejection_date');
        
        // Set status back to pending
        wp_update_post(array(
            'ID' => $site_id,
            'post_status' => 'pending'
        ));
        
        $resubmit_success = 'Site resubmitted successfully! It will be reviewed by an admin.';
    } else {
        $resubmit_error = 'Invalid site selection or you do not own this site.';
    }
}

// Get user's submitted sites (including pending approval and rejected)
$user_sites = get_posts(array(
    'post_type' => 'site',
    'author' => $current_user->ID,
    'post_status' => array('publish', 'pending', 'draft'),
    'posts_per_page' => -1
));

// Get sites with pending updates
$sites_with_pending_updates = get_posts(array(
    'post_type' => 'site',
    'author' => $current_user->ID,
    'post_status' => array('publish', 'pending'),
    'meta_query' => array(
        array(
            'key' => '_has_pending_update',
            'value' => '1',
            'compare' => '='
        )
    ),
    'posts_per_page' => -1
));

?>
<table border="0" cellpadding="0" cellspacing="0" width="765">
    <tbody>
        <tr>
            <?php get_sidebar(); ?>
            <td background="<?php echo get_template_directory_uri(); ?>/images/echobeta_r3_c3.gif" width="641" height="427" valign="top">
                <center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="12px"></center>
                <div align="center">
                    <center>
                        <table border="0" width="602" cellpadding="2" cellspacing="0" style="border-collapse: collapse; border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-top-style: solid; border-top-width: 1; border-bottom-width: 0" bordercolor="#111111">
                            <tbody>
                                <tr>
                                    <td height="20px">
                                        <font face="verdana" size="1"><b>Webmaster Profile</b></font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table border="1" width="602" cellpadding="4" cellspacing="0" valign="TOP" style="border-collapse: collapse; border-left-style:dashed; border-left-width:0; border-right-style:dashed; border-right-width:0; border-top-width:0; border-bottom-style:dashed; border-bottom-width:0" bordercolor="#111111">
                            <tbody>
                                <tr>
                                    <td valign="top" bgcolor="#EEEEEE">
                                        <font face="verdana" size="1">
                                            <div style="max-width: 540px; margin: 0 auto;">
                                                <?php if (isset($success_message)): ?>
                                                    <div style="background: #e2ffe2; border: 1px solid #b2d8b2; padding: 16px; margin-bottom: 24px;">
                                                        <strong><?php echo esc_html($success_message); ?></strong>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if (isset($error_message)): ?>
                                                    <div style="background: #ffe2e2; border: 1px solid #d8b2b2; padding: 16px; margin-bottom: 24px;">
                                                        <strong><?php echo esc_html($error_message); ?></strong>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if (isset($update_success)): ?>
                                            <div style="background: #e2ffe2; border: 1px solid #b2d8b2; padding: 16px; margin-bottom: 24px;">
                                                <strong><?php echo esc_html($update_success); ?></strong>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($resubmit_success)): ?>
                                            <div style="background: #e2ffe2; border: 1px solid #b2d8b2; padding: 16px; margin-bottom: 24px;">
                                                <strong><?php echo esc_html($resubmit_success); ?></strong>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($resubmit_error)): ?>
                                            <div style="background: #ffe2e2; border: 1px solid #d8b2b2; padding: 16px; margin-bottom: 24px;">
                                                <strong><?php echo esc_html($resubmit_error); ?></strong>
                                            </div>
                                        <?php endif; ?>
                                                
                                                <?php if (isset($update_error)): ?>
                                                    <div style="background: #ffe2e2; border: 1px solid #d8b2b2; padding: 16px; margin-bottom: 24px;">
                                                        <strong><?php echo esc_html($update_error); ?></strong>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <table width="100%" cellpadding="2" cellspacing="0">
                                                    <tr>
                                                        <td width="50%" valign="top">
                                                            <font face="verdana" size="1"><b>Edit Profile</b></font><br><br>
                                                            <form method="post" action="">
                                                                <?php wp_nonce_field('update_profile', 'profile_nonce'); ?>
                                                                <input type="hidden" name="action" value="update_profile">
                                                                
                                                                <font face="verdana" size="1">Display Name:</font><br>
                                                                <input type="text" name="display_name" value="<?php echo esc_attr($current_user->display_name); ?>" size="25" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3"><br><br>
                                                                
                                                                <font face="verdana" size="1">Email:</font><br>
                                                                <input type="email" name="user_email" value="<?php echo esc_attr($current_user->user_email); ?>" size="25" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3"><br><br>
                                                                
                                                                <font face="verdana" size="1">Bio:</font><br>
                                                                <textarea name="description" rows="4" cols="25" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3"><?php echo esc_textarea($current_user->description); ?></textarea><br><br>
                                                                
                                                                <input type="submit" value="Update Profile" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3">
                                                            </form>
                                                        </td>
                                                        
                                                        <td width="50%" valign="top">
                                                            <font face="verdana" size="1"><b>Submit Website Update</b></font><br><br>
                                                            <?php if (empty($user_sites)): ?>
                                                                <div style="background: #fff3cd; color: #856404; padding: 10px; margin-bottom: 20px; border: 1px solid #ffeaa7;">
                                                                    <font face="verdana" size="1">You don't have any sites submitted yet. Please submit your site first through the <a href="/signup-site/" style="color: #856404;">sign up form</a>.</font>
                                                                </div>
                                                            <?php else: ?>
                                                                <form method="post" action="">
                                                                    <?php wp_nonce_field('submit_update', 'update_nonce'); ?>
                                                                    <input type="hidden" name="action" value="submit_update">
                                                                    
                                                                    <font face="verdana" size="1">Select Your Site:</font><br>
                                                                    <select name="site_id" required style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3">
                                                                        <option value="">Choose a site...</option>
                                                                        <?php foreach ($user_sites as $site): ?>
                                                                            <option value="<?php echo $site->ID; ?>"><?php echo esc_html($site->post_title); ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select><br><br>
                                                                    

                                                                    
                                                                    <font face="verdana" size="1">Games Count:</font><br>
                                                                    <input type="number" name="games_count" min="0" size="5" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3" placeholder="Leave blank if no change"><br><br>
                                                                    
                                                                    <font face="verdana" size="1">Apps Count:</font><br>
                                                                    <input type="number" name="apps_count" min="0" size="5" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3" placeholder="Leave blank if no change"><br><br>
                                                                    
                                                                    <font face="verdana" size="1">New Features (if any):</font><br>
                                                                    <textarea name="new_features" rows="3" cols="25" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3" placeholder="List any new features or changes..."></textarea><br><br>
                                                                    
                                                                    <font face="verdana" size="1">Description of Changes:</font><br>
                                                                    <textarea name="update_description" rows="4" cols="25" required style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3" placeholder="Describe the changes you want to make..."></textarea><br><br>
                                                                    

                                                                    
                                                                    <input type="submit" value="Submit Update" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3">
                                                                </form>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                                
                                                <br><br>
                                                <font face="verdana" size="1"><b>Your Submitted Sites</b></font><br><br>
                                                <?php if (!empty($user_sites)): ?>
                                                    <table class="tableone" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border-style: solid; border-width: 1" bordercolor="#111111">
                                                        <tr>
                                                            <td height="20px" bgcolor="white" style="border-bottom: 1px solid #111111;">
                                                                <font face="verdana" size="1">&nbsp;<b>Site Name</b></font>
                                                            </td>
                                                            <td height="20px" bgcolor="white" style="border-bottom: 1px solid #111111;">
                                                                <font face="verdana" size="1"><b>Status</b></font>
                                                            </td>
                                                            <td height="20px" bgcolor="white" style="border-bottom: 1px solid #111111;">
                                                                <font face="verdana" size="1"><b>Last Updated</b></font>
                                                            </td>
                                                            <td height="20px" bgcolor="white" style="border-bottom: 1px solid #111111;">
                                                                <font face="verdana" size="1"><b>Action</b></font>
                                                            </td>
                                                        </tr>
                                                        <?php foreach ($user_sites as $site): 
                                                            $has_pending = get_post_meta($site->ID, '_has_pending_update', true);
                                                            $last_updated = get_post_meta($site->ID, '_last_updated', true);
                                                            $is_updated = get_post_meta($site->ID, '_is_updated', true);
                                                            $is_new_site = get_post_meta($site->ID, '_is_new_site', true);
                                                            $is_rejected = get_post_meta($site->ID, '_is_rejected', true);
                                                            
                                                            // Determine site status with proper priority
                                                            if ($site->post_status == 'pending') {
                                                                $status = 'Pending Approval';
                                                                $status_color = '#ff9800'; // Orange
                                                            } elseif ($is_rejected) {
                                                                $status = 'Rejected';
                                                                $status_color = '#f44336'; // Red
                                                            } elseif ($has_pending) {
                                                                $status = 'Update Pending';
                                                                $status_color = '#ff9800'; // Orange
                                                            } elseif ($site->post_status == 'publish') {
                                                                if ($is_new_site) {
                                                                    $status = 'Approved (New)';
                                                                    $status_color = '#4caf50'; // Green
                                                                } elseif ($is_updated) {
                                                                    $status = 'Approved (Updated)';
                                                                    $status_color = '#4caf50'; // Green
                                                                } else {
                                                                    $status = 'Approved';
                                                                    $status_color = '#4caf50'; // Green
                                                                }
                                                            } else {
                                                                $status = 'Unknown';
                                                                $status_color = '#757575'; // Gray
                                                            }
                                                        ?>
                                                            <tr height="25px">
                                                                <td>
                                                                    <font face="verdana" size="1">&nbsp;<a href="<?php echo get_permalink($site->ID); ?>"><?php echo esc_html($site->post_title); ?></a></font>
                                                                </td>
                                                                <td>
                                                                    <font face="verdana" size="1" style="color: <?php echo $status_color; ?>; font-weight: bold;"><?php echo esc_html($status); ?></font>
                                                                </td>
                                                                <td>
                                                                    <font face="verdana" size="1"><?php echo $last_updated ? date('M j, Y', strtotime($last_updated)) : get_the_date('M j, Y', $site->ID); ?></font>
                                                                </td>
                                                                <td>
                                                                    <?php if ($is_rejected): ?>
                                                                        <form method="post" action="" style="display: inline;">
                                                                            <?php wp_nonce_field('resubmit_site', 'resubmit_nonce'); ?>
                                                                            <input type="hidden" name="action" value="resubmit_site">
                                                                            <input type="hidden" name="site_id" value="<?php echo $site->ID; ?>">
                                                                            <input type="submit" value="Resubmit" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding: 2px 8px; background-color: #4caf50; color: white; cursor: pointer;" onclick="return confirm('Are you sure you want to resubmit this site for review?');">
                                                                        </form>
                                                                    <?php else: ?>
                                                                        <font face="verdana" size="1">-</font>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </table>
                                                <?php else: ?>
                                                    <font face="verdana" size="1">You haven't submitted any sites yet.</font>
                                                <?php endif; ?>
                                                
                                                <br><br>
                                                <font face="verdana" size="1"><b>Sites with Pending Updates</b></font><br><br>
                                                <?php if (!empty($sites_with_pending_updates)): ?>
                                                    <table class="tableone" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border-style: solid; border-width: 1" bordercolor="#111111">
                                                        <tr>
                                                            <td height="20px" bgcolor="white" style="border-bottom: 1px solid #111111;">
                                                                <font face="verdana" size="1">&nbsp;<b>Site</b></font>
                                                            </td>
                                                            <td height="20px" bgcolor="white" style="border-bottom: 1px solid #111111;">
                                                                <font face="verdana" size="1"><b>Submitted</b></font>
                                                            </td>
                                                        </tr>
                                                        <?php foreach ($sites_with_pending_updates as $site): 
                                                            $update_data = get_post_meta($site->ID, '_pending_update_data', true);
                                                        ?>
                                                            <tr height="25px">
                                                                <td>
                                                                    <font face="verdana" size="1">&nbsp;<a href="<?php echo get_permalink($site->ID); ?>"><?php echo esc_html($site->post_title); ?></a></font>
                                                                </td>
                                                                <td>
                                                                    <font face="verdana" size="1"><?php echo date('Y-m-d H:i', strtotime($update_data['submitted_date'])); ?></font>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </table>
                                                <?php else: ?>
                                                    <font face="verdana" size="1">No sites have pending updates.</font>
                                                <?php endif; ?>
                                            </div>
                                        </font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </center>
                </div>
                <center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="12px"></center>
            </td>
        </tr>
    </tbody>
</table>
<?php get_footer(); ?>