<?php
/**
 * Template for displaying single site posts
 * This template is loaded by the EchoRing Sites plugin
 */

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
                        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                        <table border="0" width="602" cellpadding="2" cellspacing="0" style="border-collapse: collapse; border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-top-style: solid; border-top-width: 1; border-bottom-width: 0" bordercolor="#111111">
                            <tbody>
                                <tr>
                                    <td height="16px">
                                        <b>EchoRing Website Details</b>
                                        <?php if (current_user_can('manage_options')) : ?>
                                        &nbsp;&nbsp;<small><a href="<?php echo admin_url('post.php?post=' . get_the_ID() . '&action=edit'); ?>" style="color: #666;">[Edit Site]</a></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <?php
                        if (is_user_logged_in()) {
                            $current_user = wp_get_current_user();
                            $webmaster = EchoRingSites::get_webmaster();
                            $is_webmaster = ($current_user->user_login === $webmaster || $current_user->user_email === $webmaster);
                            if ($is_webmaster) {
                                $site_id = get_the_ID();
                                $site_url = get_post_meta($site_id, '_site_url', true);
                                $rating = get_post_meta($site_id, '_rating', true);
                                $games = get_post_meta($site_id, '_games', true);
                                $apps = get_post_meta($site_id, '_apps', true);
                                $the_good = get_post_meta($site_id, '_the_good', true);
                                $the_bad = get_post_meta($site_id, '_the_bad', true);
                                if (!is_array($the_good)) $the_good = array('');
                                if (!is_array($the_bad)) $the_bad = array('');
                                $success = false;
                                if (isset($_POST['echoring_update_site_nonce']) && wp_verify_nonce($_POST['echoring_update_site_nonce'], 'echoring_update_site')) {
                                    // Save fields as pending update (for admin review)
                                    update_post_meta($site_id, '_pending_update', 1);
                                    update_post_meta($site_id, '_pending_site_url', sanitize_url($_POST['site_url']));
                                    update_post_meta($site_id, '_pending_rating', sanitize_text_field($_POST['rating']));
                                    update_post_meta($site_id, '_pending_games', sanitize_text_field($_POST['games']));
                                    update_post_meta($site_id, '_pending_apps', sanitize_text_field($_POST['apps']));
                                    $pending_good = array_map('sanitize_textarea_field', $_POST['the_good']);
                                    $pending_bad = array_map('sanitize_textarea_field', $_POST['the_bad']);
                                    update_post_meta($site_id, '_pending_the_good', $pending_good);
                                    update_post_meta($site_id, '_pending_the_bad', $pending_bad);
                                    $success = true;
                                }
                                if ($success) {
                                    echo '<div class="notice updated"><p>Your update has been submitted for review.</p></div>';
                                } else {
                        ?>
                        <div class="echoring-site-edit-form" style="margin: 2em 0; padding: 1em; border: 1px solid #ccc; background: #f9f9f9;">
                            <h3>Edit Your Site</h3>
                            <form method="post">
                                <p>
                                    <label for="site_url"><b>Site URL:</b></label><br>
                                    <input type="url" name="site_url" id="site_url" value="<?php echo esc_attr($site_url); ?>" style="width: 100%; max-width: 400px;" required>
                                </p>
                                <p>
                                    <label for="rating"><b>Rating:</b></label><br>
                                    <input type="text" name="rating" id="rating" value="<?php echo esc_attr($rating); ?>" style="width: 100px;">
                                </p>
                                <p>
                                    <label for="games"><b>Games:</b></label><br>
                                    <input type="text" name="games" id="games" value="<?php echo esc_attr($games); ?>" style="width: 100px;">
                                </p>
                                <p>
                                    <label for="apps"><b>Apps:</b></label><br>
                                    <input type="text" name="apps" id="apps" value="<?php echo esc_attr($apps); ?>" style="width: 100px;">
                                </p>
                                <p><b>The Good:</b><br>
                                    <?php foreach ($the_good as $i => $good) { ?>
                                        <textarea name="the_good[]" rows="2" style="width: 100%; max-width: 400px;"><?php echo esc_textarea($good); ?></textarea><br>
                                    <?php } ?>
                                </p>
                                <p><b>The Bad:</b><br>
                                    <?php foreach ($the_bad as $i => $bad) { ?>
                                        <textarea name="the_bad[]" rows="2" style="width: 100%; max-width: 400px;"><?php echo esc_textarea($bad); ?></textarea><br>
                                    <?php } ?>
                                </p>
                                <?php wp_nonce_field('echoring_update_site', 'echoring_update_site_nonce'); ?>
                                <p><input type="submit" value="Submit Update" class="button button-primary"></p>
                            </form>
                        </div>
                        <?php
                                }
                            }
                        }
                        ?>
                        <table border="1" width="602" cellpadding="4" cellspacing="0" valign="TOP" style="border-collapse: collapse; border-left-style:dashed; border-left-width:0; border-right-style:dashed; border-right-width:0; border-top-width:0; border-bottom-style:dashed; border-bottom-width:0" bordercolor="#111111">
                            <tbody>
                                <tr>
                                    <td valign="top" bgcolor="#EEEEEE">
                                        <table align="center" border="0">
                                            <tbody>
                                                <tr>
                                                    <td width="325px">
                                                        <?php 
                                                        $screenshots = EchoRingSites::get_screenshots(); 
                                                        if ($screenshots && is_array($screenshots) && get_option('echoring_enable_screenshots', 1)):
                                                            $shown = 0;
                                                            foreach ($screenshots as $screenshot) {
                                                                if ($shown >= 2) break;
                                                                $img_url = $screenshot['url'];
                                                                echo '<img src="' . esc_url($img_url) . '" height="150px" width="150px" border="1" style="margin-right:4px;" alt="' . esc_attr($screenshot['alt'] ?: 'Screenshot') . '" />';
                                                                $shown++;
                                                            }
                                                        endif;
                                                        ?>
                                                    </td>
                                                    <td width="245px" align="left" valign="middle">
                                                            <a href="<?php echo esc_url(EchoRingSites::get_site_url()); ?>" target="_blank"><?php the_title(); ?></a>
                                                            <div>
                                                                <b>Webmaster:</b> <?php echo esc_html(EchoRingSites::get_webmaster()); ?>
                                                            </div>
                                                        <?php if (get_option('echoring_enable_ratings', 1)): ?>
                                                        <div><b>Rating:&nbsp;</b>
                                                        <?php 
                                                        $rating = EchoRingSites::get_rating(); 
                                                        if ($rating): 
                                                            $plugin_img_dir = plugins_url('images', dirname(__FILE__));
                                                            if (preg_match('/([1-5])/', $rating, $matches)) {
                                                                $rating_num = $matches[1];
                                                                if (file_exists(WP_PLUGIN_DIR . '/echoring-sites/images/' . $rating_num . '.png')) {
                                                                    echo '<img src="' . esc_url($plugin_img_dir . '/' . $rating_num . '.png') . '" alt="' . esc_attr($rating) . '" style="vertical-align:middle;" />';
                                                                } else {
                                                                    echo esc_html($rating);
                                                                }
                                                            } else {
                                                                echo esc_html($rating);
                                                            }
                                                        endif; 
                                                        ?></div>
                                                        <?php endif; ?>
                                                        <?php if (get_option('echoring_enable_types', 1)): ?>
                                                        <div style="margin-bottom: 8px;"><b>Type:</b> 
                                                        <?php 
                                                        $types = get_the_terms(get_the_ID(), 'site_type'); 
                                                        if ($types && !is_wp_error($types)):
                                                            foreach ($types as $type):
                                                                $type_img = EchoRingSites::get_type_image_url($type->term_id, 'thumbnail');
                                                                if ($type_img) {
                                                                    echo '<img src="' . esc_url($type_img) . '" alt="' . esc_attr($type->name) . '" title="' . esc_attr($type->name) . '" style="vertical-align:middle;max-width:32px;max-height:32px;margin-right:4px;" />';
                                                                } else {
                                                                    echo esc_html($type->name) . ' ';
                                                                }
                                                            endforeach;
                                                        endif;
                                                        ?></div>
                                                        <?php endif; ?>
                                                        <?php if (get_option('echoring_enable_languages', 1)): ?>
                                                        <div><b>Language:</b> 
                                                        <?php 
                                                        $languages = get_the_terms(get_the_ID(), 'site_language'); 
                                                        if ($languages && !is_wp_error($languages)):
                                                            foreach ($languages as $language):
                                                                $lang_img = EchoRingSites::get_language_image_url($language->term_id, 'thumbnail');
                                                                if ($lang_img) {
                                                                    echo '<img src="' . esc_url($lang_img) . '" alt="' . esc_attr($language->name) . '" title="' . esc_attr($language->name) . '" style="vertical-align:middle;max-width:32px;max-height:32px;margin-right:4px;" />';
                                                                } else {
                                                                    echo esc_html($language->name) . ' ';
                                                                }
                                                            endforeach;
                                                        endif;
                                                        ?></div>
                                                        <?php endif; ?>
                                                        <?php if (get_option('echoring_enable_games', 1)): ?>
                                                        <div><b>Games:</b> <?php echo esc_html(EchoRingSites::get_games()); ?></div>
                                                        <?php endif; ?>
                                                        <?php if (get_option('echoring_enable_apps', 1)): ?>
                                                        <div><b>Apps:</b> <?php echo esc_html(EchoRingSites::get_apps()); ?></div>
                                                        <?php endif; ?>
                                                        <?php if (get_option('echoring_enable_features', 1)): ?>
                                                        <b>Features:</b> 
                                                        <?php 
                                                        $features = get_the_terms(get_the_ID(), 'site_feature'); 
                                                        if ($features && !is_wp_error($features)):
                                                            foreach ($features as $feature):
                                                                $feature_img = EchoRingSites::get_feature_image_url($feature->term_id, 'thumbnail');
                                                                if ($feature_img) {
                                                                    echo '<img src="' . esc_url($feature_img) . '" alt="' . esc_attr($feature->name) . '" title="' . esc_attr($feature->name) . '" style="vertical-align:middle;max-width:32px;max-height:32px;margin-right:8px;" />';
                                                                } else {
                                                                    echo esc_html($feature->name) . ' ';
                                                                }
                                                            endforeach;
                                                        endif;
                                                        ?>
                                                        <br /><br />
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                            <?php the_content(); ?>
                                                    </td>
                                                </tr>
                                                <?php if (get_option('echoring_enable_reviews', 1)): ?>
                                                <tr>
                                                    <?php 
                                                    $the_good = EchoRingSites::get_the_good(); 
                                                    if ($the_good && is_array($the_good)): 
                                                    ?>
                                                    <td valign="top">

                                                            <center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.gif" height="12px"></center>
                                                            <b>The Good</b> 
                                                            <ul>
                                                                <?php foreach ($the_good as $item): ?>
                                                                <li>
                                                                    <?php echo esc_html($item); ?>
                                                                </li>
                                                                <?php endforeach; ?>
                                                            </ul>

                                                    </td>
                                                    <?php endif; ?>
                                                    <?php 
                                                    $the_bad = EchoRingSites::get_the_bad(); 
                                                    if ($the_bad && is_array($the_bad)): 
                                                    ?>
                                                    <td valign="top">
                                                            <center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.gif" height="12px"></center>
                                                            <b>The Bad</b>
                                                            <ul>
                                                                <?php foreach ($the_bad as $item): ?>
                                                                <li>
                                                                    <?php echo esc_html($item); ?>
                                                                </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                    </td>
                                                    <?php endif; ?>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <?php endwhile; endif; ?>      
                    </center>
                </div> 
                <center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="12px"></center>
                <div align="center">
                    <center>      
                        <table border="0" width="602" cellpadding="2" cellspacing="0" style="border-collapse: collapse; border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-top-style: solid; border-top-width: 1; border-bottom-width: 0" bordercolor="#111111">
                            <tbody>
                                <tr>
                                    <td height="16px">
                                        <b>EchoRing Comments</b> <?php the_title(); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table border="1" width="602" cellpadding="4" cellspacing="0" valign="TOP" style="border-collapse: collapse; border-left-style:dashed; border-left-width:0; border-right-style:dashed; border-right-width:0; border-top-width:0; border-bottom-style:dashed; border-bottom-width:0" bordercolor="#111111">
                            <tbody>
                                <tr>
                                    <td valign="top" bgcolor="#EEEEEE">
                                        <?php comments_template(); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>    
                    </center>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<?php get_footer(); ?>