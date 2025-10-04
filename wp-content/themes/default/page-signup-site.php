<?php
/*
Template Name: Webmaster Sign Up & Site Submission
*/

// Process form submission first (before any output)
$errors = array();
$success = false;

// Register 'webmaster' role if not exists
if (!get_role('webmaster')) {
    add_role('webmaster', 'Webmaster', array(
        'read' => true,
        'edit_posts' => false,
        'delete_posts' => false,
    ));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup_nonce']) && wp_verify_nonce($_POST['signup_nonce'], 'signup_site')) {
    // Collect and sanitize input
    $site_name = sanitize_text_field($_POST['site_name'] ?? '');
    $site_url = esc_url_raw($_POST['siteurl'] ?? '');
    $webmaster = sanitize_text_field($_POST['site_webmaster'] ?? '');
    $webmaster_email = sanitize_email($_POST['webmaster_email'] ?? '');
    $password = $_POST['password'] ?? '';
    $site_games = sanitize_text_field($_POST['site_games'] ?? '');
    $site_apps = sanitize_text_field($_POST['site_apps'] ?? '');
    $site_rules = sanitize_textarea_field($_POST['body'] ?? '');

    // Validate required fields
    if (!$site_name || !$site_url || !$webmaster || !$webmaster_email || !$password) {
        $errors[] = 'Please fill in all required fields.';
    }
    if (!is_email($webmaster_email)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (email_exists($webmaster_email)) {
        $errors[] = 'This email is already registered.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (empty($errors)) {
        // Create user
        $user_id = wp_create_user($webmaster_email, $password, $webmaster_email);
        if (is_wp_error($user_id)) {
            $errors[] = $user_id->get_error_message();
        } else {
            // Set role and meta
            $user = new WP_User($user_id);
            $user->set_role('webmaster');
            update_user_meta($user_id, 'display_name', $webmaster);
            
            // Log user in
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);

            // Create site post (pending)
            $post_id = wp_insert_post(array(
                'post_title' => $site_name,
                'post_type' => 'site',
                'post_status' => 'pending',
                'post_author' => $user_id,
                'meta_input' => array(
                    '_site_url' => $site_url,
                    '_webmaster' => $webmaster,
                    '_games' => $site_games,
                    '_apps' => $site_apps,
                ),
            ));
            
            if ($post_id) {
                // Set taxonomy terms if they exist (using IDs)
                if (!empty($_POST['site_type']) && get_option('echoring_enable_types', 1)) {
                    $site_type_value = $_POST['site_type'];
                    
                    // Handle special "Abandonware & Emulation" option
                    if ($site_type_value === 'abandonware_emulation') {
                        // Get the term IDs for Abandonware and Emulation
                        $abandonware_term = get_term_by('name', 'Abandonware', 'site_type');
                        $emulation_term = get_term_by('name', 'Emulation', 'site_type');
                        
                        $term_ids = array();
                        if ($abandonware_term && !is_wp_error($abandonware_term)) {
                            $term_ids[] = $abandonware_term->term_id;
                        }
                        if ($emulation_term && !is_wp_error($emulation_term)) {
                            $term_ids[] = $emulation_term->term_id;
                        }
                        
                        if (!empty($term_ids)) {
                            wp_set_object_terms($post_id, $term_ids, 'site_type');
                        }
                    } else {
                        // Regular single type selection
                        wp_set_object_terms($post_id, intval($site_type_value), 'site_type');
                    }
                }
                
                $lang_terms = array();
                if (!empty($_POST['site_lang1']) && get_option('echoring_enable_languages', 1)) {
                    $lang_terms[] = intval($_POST['site_lang1']);
                }
                if (!empty($_POST['site_lang2']) && $_POST['site_lang2'] !== 'None' && get_option('echoring_enable_languages', 1)) {
                    $lang_terms[] = intval($_POST['site_lang2']);
                }
                if (!empty($lang_terms)) {
                    wp_set_object_terms($post_id, $lang_terms, 'site_language');
                }
                
                // Set features if they exist (using IDs)
                if (get_option('echoring_enable_features', 1) && !empty($_POST['site_features']) && is_array($_POST['site_features'])) {
                    $feature_ids = array_map('intval', $_POST['site_features']);
                    wp_set_object_terms($post_id, $feature_ids, 'site_feature');
                }
                
                $success = true;
            } else {
                $errors[] = 'Could not create site. Please try again.';
            }
        }
    }
}

get_header();
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
                                        <font face="verdana" size="1"><b>Webmaster Sign Up & Submit Your Site</b></font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table border="1" width="602" cellpadding="4" cellspacing="0" valign="TOP" style="border-collapse: collapse; border-left-style:dashed; border-left-width:0; border-right-style:dashed; border-right-width:0; border-top-width:0; border-bottom-style:dashed; border-bottom-width:0" bordercolor="#111111">
                            <tbody>
                                <tr>
                                    <td valign="top" bgcolor="#EEEEEE">
                                        <div style="max-width: 540px; margin: 0 auto;">
<?php if ($success): ?>
    <div style="background: #e2ffe2; border: 1px solid #b2d8b2; padding: 16px; margin-bottom: 24px;">
        <strong>Thank you!</strong> Your site has been submitted and is pending approval by an admin.
    </div>
<?php else: ?>
    <?php if ($errors): ?>
        <div style="background: #ffe2e2; border: 1px solid #d8b2b2; padding: 16px; margin-bottom: 24px;">
            <?php foreach ($errors as $err): ?>
                <div><?php echo esc_html($err); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form name="formcheck" action="" method="POST">
        <?php wp_nonce_field('signup_site', 'signup_nonce'); ?>
        <table align="CENTER" cellpadding="4" style="vertical-align: middle;">
            <tr><td style="vertical-align: middle; padding: 4px;"><font face="verdana" size="1"><b>Site Name:</b></font></td><td style="vertical-align: middle; padding: 4px;"><input type="text" name="site_name" size="25" tabindex="1" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3; margin: 0; vertical-align: middle;"></td></tr>
            <tr><td style="vertical-align: middle; padding: 4px;"><font face="verdana" size="1"><b>Site URL:</b></font></td><td style="vertical-align: middle; padding: 4px;"><input type="text" name="siteurl" value="http://www." size="25" tabindex="2" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3; margin: 0; vertical-align: middle;"></td></tr>
            <tr><td style="vertical-align: middle; padding: 4px;"><font face="verdana" size="1"><b>Webmaster:</b></font></td><td style="vertical-align: middle; padding: 4px;"><input type="text" name="site_webmaster" size="25" tabindex="3" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3; margin: 0; vertical-align: middle;"></td></tr>
            <tr><td style="vertical-align: middle; padding: 4px;"><font face="verdana" size="1"><b>Webmaster Email:</b></font></td><td style="vertical-align: middle; padding: 4px;"><input type="text" name="webmaster_email" size="25" tabindex="4" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3; margin: 0; vertical-align: middle;"></td></tr>
            <tr><td style="vertical-align: middle; padding: 4px;"><font face="verdana" size="1"><b>Password:</b></font></td><td style="vertical-align: middle; padding: 4px;"><input type="password" name="password" size="8" maxlength="32" tabindex="5" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3; margin: 0; vertical-align: middle;"></td></tr>
            <?php if (get_option('echoring_enable_types', 1)): ?>
            <tr><td style="vertical-align: middle; padding: 4px;"><font face="verdana" size="1"><b>Site Type:</b></font></td>
            <td style="vertical-align: middle; padding: 4px;">
            <select name="site_type" tabindex="6" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3; margin: 0; vertical-align: middle;">
            <option value="">Select Type</option>
            <?php
            $types = get_terms(array(
                'taxonomy' => 'site_type',
                'hide_empty' => false,
            ));
            if (!is_wp_error($types)) {
                // Add the special combined option first
                echo '<option value="abandonware_emulation">Abandonware & Emulation</option>';
                
                // Then add the regular individual options
                foreach ($types as $type) {
                    echo '<option value="' . esc_attr($type->term_id) . '">' . esc_html($type->name) . '</option>';
                }
            }
            ?>
            </select></td></tr>
            <?php endif; ?>
            <?php if (get_option('echoring_enable_games', 1)): ?>
            <tr><td style="vertical-align: middle; padding: 4px;"><font face="verdana" size="1"><b>Game Count:</b></font></td><td style="vertical-align: middle; padding: 4px;"><input type="text" name="site_games" size="5" tabindex="7" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3; margin: 0; vertical-align: middle;"></td></tr>
            <?php endif; ?>
            <?php if (get_option('echoring_enable_apps', 1)): ?>
            <tr><td style="vertical-align: middle; padding: 4px;"><font face="verdana" size="1"><b>Apps Count:</b></font></td><td style="vertical-align: middle; padding: 4px;"><input type="text" name="site_apps" size="5" tabindex="8" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3; margin: 0; vertical-align: middle;"></td></tr>
            <?php endif; ?>
            <?php if (get_option('echoring_enable_languages', 1)): ?>
            <tr><td style="vertical-align: middle; padding: 4px;"><font face="verdana" size="1"><b>Main Language:</b></font></td>
            <td style="vertical-align: middle; padding: 4px;">
            <select name="site_lang1" tabindex="9" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3; margin: 0; vertical-align: middle;">
            <option value="">Select Language</option>
            <?php
            $languages = get_terms(array(
                'taxonomy' => 'site_language',
                'hide_empty' => false,
            ));
            if (!is_wp_error($languages)) {
                foreach ($languages as $language) {
                    echo '<option value="' . esc_attr($language->term_id) . '">' . esc_html($language->name) . '</option>';
                }
            }
            ?>
            </select></td></tr>
            <tr><td style="vertical-align: middle; padding: 4px;"><font face="verdana" size="1"><b>Other Language:</b></font></td>
            <td style="vertical-align: middle; padding: 4px;">
            <select name="site_lang2" tabindex="10" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3; margin: 0; vertical-align: middle;">
            <option value="None">None</option>
            <?php
            if (!is_wp_error($languages)) {
                foreach ($languages as $language) {
                    echo '<option value="' . esc_attr($language->term_id) . '">' . esc_html($language->name) . '</option>';
                }
            }
            ?>
            </select></td></tr>
            <?php endif; ?>
            <?php if (get_option('echoring_enable_features', 1)): ?>
            <tr><td colspan="2" valign="middle"><font face="verdana" size="1"><b>Does your site have the following features?</b></font></td></tr>
            <?php
            $features = get_terms(array(
                'taxonomy' => 'site_feature',
                'hide_empty' => false,
            ));
            if (!is_wp_error($features)) {
                foreach ($features as $feature) {
                    echo '<tr>';
                    echo '<td style="vertical-align: middle; padding: 4px;"><font face="verdana" size="1"><b>' . esc_html($feature->name) . '?:</b></font></td>';
                    echo '<td style="vertical-align: middle; padding: 4px;"><select name="site_features[' . esc_attr($feature->term_id) . ']" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3; margin: 0; vertical-align: middle;">';
                    echo '<option value="yes" selected>Yes</option>';
                    echo '<option value="no">No</option>';
                    echo '</select></td>';
                    echo '</tr>';
                }
            }
            ?>
            <?php endif; ?>
            <tr><td colspan="2" valign="middle"><font face="verdana" size="1" color="darkred">
            <br>
            <b>EchoRing 4.0B Rules and Regulations</b></font>
            <br>
            <textarea name="body" cols="45" rows="6" wrap="virtual" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3">By joining EchoRing 4.0B, you adhere to the following rules and regulations of quality:

-Your website may not contain any software titles which are not Abandonware (including ROMs of games which are still sold).

-Your website may not contain any pornographic advertisements.

-Your website may not contain an excess of 1 pop-up per page.

-Your website may not contain any forced voting portals or other systems which force the user to click your banners prior to being able to download the content.

-Your website may not link to any pornographic or warez material.

-Your website may not be a member of or be affiliated in any way with any "top sites". 

-You may not use reviews or images from other websites without permission (this practice is known as content theft).

-You must have an EchoRing button visible on your index page linking back to EchoRing.

Failure to adhere to these standards will result in immediate suspension from the listing and can result in a ban from the ring unless proper measures are taken.

By submitting this form you certify that the information provided above is accurate and that the content on your website is original and is your own work.
</textarea></td></tr>
            <tr><td colspan="2" valign="middle"><font face="verdana" size="1">By submitting this form I agree to abide by the rules.</font></td></tr>
            <tr><td colspan="2" valign="middle">
            <br>
            <input type="submit" value="Submit Application" tabindex="14" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3"></td></tr>
        </table>
    </form>
<?php endif; ?>
                                        </div>
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