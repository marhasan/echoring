<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
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
                                    <td height="20px">
                                        <font face="verdana" size="1"><b><?php the_title(); ?></b> Complete Listing of All Our Members!</font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table border="1" width="602" cellpadding="4" cellspacing="0" valign="TOP" style="border-collapse: collapse; border-left-style:dashed; border-left-width:0; border-right-style:dashed; border-right-width:0; border-top-width:0; border-bottom-style:dashed; border-bottom-width:0" bordercolor="#111111">
                            <tbody>
                                <tr>
                                    <td valign="top" bgcolor="#EEEEEE">
                                        <font face="verdana" size="1">
                                            <?php the_content('<p align="justify">' . __('Read the rest of this entry &raquo;', 'kubrick') . '</p>'); ?>
                                        </font>
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
                        <table class="tableone" border="0" width="602" cellpadding="0" cellspacing="0" valign="TOP" style="border-collapse: collapse; border-style: solid; border-width: 1" bordercolor="#111111">
                            <tbody>
                                <tr class="tableheader">
                                    <td width="130px" height="20px" bgcolor="#ffffff" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
                                        <font face="verdana" size="1"><b>Name:</b></font>
                                    </td>
                                    <td width="65px" bgcolor="#ffffff" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
                                        <font face="verdana" size="1" width="80px"><b>Owner:</b></font>
                                    </td>
                                    <td width="70px" bgcolor="#ffffff" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
                                        <font face="verdana" size="1"><b>Language:</b></font>
                                    </td>
                                    <td bgcolor="#ffffff" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
                                        <font face="verdana" size="1" width="47px"><b>Type:</b></font>
                                    </td>
                                    <td width="50px" bgcolor="#ffffff" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
                                        <font face="verdana" size="1"><b>Games:</b></font>
                                    </td>
                                    <td width="45px" bgcolor="#ffffff" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
                                        <font face="verdana" size="1"><b>Apps:</b></font>
                                    </td>
                                    <td width="60px" bgcolor="#ffffff" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
                                        <font face="verdana" size="1"><b>Features:</b></font>
                                    </td>
                                    <td width="65px" bgcolor="#ffffff" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
                                        <font face="verdana" size="1"><b>Rating:</b></font>
                                    </td>
                                    <td width="55px" bgcolor="#ffffff" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
                                        <font face="verdana" size="1"><b>Status:</b></font>
                                    </td>
                                </tr>
                                <?php 
                                // query args
                                $args = array(
                                'posts_per_page'        => '-1',
                                'post_type'             => 'site',
                                'post_status'           => 'publish',
                                'orderby'               => 'name',
                                'order'                 => 'ASC',
                                'ignore_sticky_posts'   => '1',
                                'caller_get_posts'      => 1
                                );

                                // query
                                $updated = new WP_Query($args);
                                        
                                // loop
                                while($updated->have_posts()) : $updated->the_post(); 
                                        
                                $today = current_time('Y-m-d'); // current date a.k.a. TODAY
                                $pub = get_the_time('Y-m-d', $updated->ID); // date when post was published
                                $mod = get_the_modified_time('Y-m-d', $updated->ID); // date when post was last modified
                                ?>
                                <tr height="25px">
                                    <td>
                                        <font face="verdana" size="1">&nbsp;<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></font>
                                    </td>
                                    <td>
                                        <font face="verdana" size="1"><?php echo esc_html(EchoRingSites::get_webmaster(get_the_ID())); ?></font>
                                    </td>
                                    <td>
                                        <?php
                                            $languages = get_the_terms(get_the_ID(), 'site_language');
                                            if ($languages && !is_wp_error($languages)):
                                                foreach ($languages as $language):
                                                    $lang_img = EchoRingSites::get_language_image_url($language->term_id, 'thumbnail');
                                                    if ($lang_img) {
                                                        echo '<img src="' . esc_url($lang_img) . '" alt="' . esc_attr($language->name) . '" title="' . esc_attr($language->name) . '" style="vertical-align:middle;max-width:24px;max-height:24px;margin-right:2px;" />';
                                                    } else {
                                                        echo esc_html($language->name) . ' ';
                                                    }
                                                endforeach;
                                            endif;
                                        ?>
                                    </td>
                                    <td>
                                        <font face="verdana" size="1">
                                        <?php
                                            $types = get_the_terms(get_the_ID(), 'site_type');
                                            if ($types && !is_wp_error($types)):
                                                foreach ($types as $type):
                                                    $type_img = EchoRingSites::get_type_image_url($type->term_id, 'thumbnail');
                                                    if ($type_img) {
                                                        echo '<img src="' . esc_url($type_img) . '" alt="' . esc_attr($type->name) . '" title="' . esc_attr($type->name) . '" style="vertical-align:middle;margin-right:2px;" />';
                                                    } else {
                                                        echo esc_html($type->name) . ' ';
                                                    }
                                                endforeach;
                                            endif;
                                        ?>
                                        </font>
                                    </td>
                                    <td>
                                        <font face="verdana" size="1">&nbsp; <?php echo esc_html(EchoRingSites::get_games(get_the_ID())); ?></font>
                                    </td>
                                    <td>
                                        <font face="verdana" size="1">&nbsp; <?php echo esc_html(EchoRingSites::get_apps(get_the_ID())); ?></font>
                                    </td>
                                    <td>
                                        <?php
                                            $features = get_the_terms(get_the_ID(), 'site_feature');
                                            if ($features && !is_wp_error($features)):
                                                foreach ($features as $feature):
                                                    $feature_img = EchoRingSites::get_feature_image_url($feature->term_id, 'thumbnail');
                                                    if ($feature_img) {
                                                        echo '<img src="' . esc_url($feature_img) . '" alt="' . esc_attr($feature->name) . '" title="' . esc_attr($feature->name) . '" style="vertical-align:middle;margin-right:2px;" />';
                                                    } else {
                                                        echo esc_html($feature->name) . ' ';
                                                    }
                                                endforeach;
                                            endif;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            $rating = EchoRingSites::get_rating(get_the_ID());
                                            if ($rating && preg_match('/([1-5])/', $rating, $matches)) {
                                                $rating_num = $matches[1];
                                                if (file_exists(WP_PLUGIN_DIR . '/echoring-sites/images/' . $rating_num . '.png')) {
                                                    echo '<img src="' . esc_url(EchoRingSites::get_plugin_image_url($rating_num . '.png')) . '" alt="' . esc_attr($rating) . '" title="' . esc_attr($rating) . '" style="vertical-align:middle;" />';
                                                } else {
                                                    echo esc_html($rating);
                                                }
                                            } else {
                                                echo '<font face="verdana" size="1">N/A</font>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            $is_new_site = get_post_meta(get_the_ID(), '_is_new_site', true);
                                            $is_updated = get_post_meta(get_the_ID(), '_is_updated', true);
                                            if ($is_new_site) {
                                                echo '<img src="' . get_template_directory_uri() . '/images/site_new.png" alt="New Site">';
                                            } elseif ($is_updated) {
                                                echo '<img src="' . get_template_directory_uri() . '/images/site_updated.png" alt="Updated">';
                                            } else {
                                                echo '<img src="' . get_template_directory_uri() . '/images/site_active.png" alt="Active">';
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </center>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<?php get_footer(); ?>