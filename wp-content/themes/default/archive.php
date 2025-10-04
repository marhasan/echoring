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
	<?php if (have_posts()) : ?>
	<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
	<table border="0" width="602" cellpadding="2" cellspacing="0" style="border-collapse: collapse; border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-top-style: solid; border-top-width: 1; border-bottom-width: 0" bordercolor="#111111">
		<tbody>
			<tr>
				<td height="16px">
					<font face="verdana" size="1"><b>
					<?php /* If this is a category archive */ if (is_category()) { ?>
						<?php printf(__('Archive for the &#8216;%s&#8217; Category', 'kubrick'), single_cat_title('', false)); ?>
					<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
						<?php printf(__('Posts Tagged &#8216;%s&#8217;', 'kubrick'), single_tag_title('', false) ); ?>
					<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
						<?php printf(_c('Archive for %s|Daily archive page', 'kubrick'), get_the_time(__('F jS, Y', 'kubrick'))); ?>
					<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
						<?php printf(_c('Archive for %s|Monthly archive page', 'kubrick'), get_the_time(__('F, Y', 'kubrick'))); ?>
					<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
						<?php printf(_c('Archive for %s|Yearly archive page', 'kubrick'), get_the_time(__('Y', 'kubrick'))); ?>
					<?php /* If this is an author archive */ } elseif (is_author()) { ?>
						<?php _e('Author Archive', 'kubrick'); ?>
					<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
						<?php _e('Blog Archives', 'kubrick'); ?>
					<?php } ?>
					</b> Complete Archive Listing!</font>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="tableone" border="0" width="602" cellpadding="0" cellspacing="0" valign="TOP" style="border-collapse: collapse; border-style: solid; border-width: 1" bordercolor="#111111">
		<tbody>
			<tr>
				<td width="300px" height="20px" bgcolor="#eeeeee" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
					<font face="verdana" size="1">&nbsp;<b>Title:</b></font>
				</td>
				<td width="150px" bgcolor="#eeeeee" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
					<font face="verdana" size="1"><b>Date:</b></font>
				</td>
				<td width="152px" bgcolor="#eeeeee" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
					<font face="verdana" size="1"><b>Category:</b></font>
				</td>
			</tr>
			<?php while (have_posts()) : the_post(); ?>
			<tr height="25px">
				<td>
					<font face="verdana" size="1">&nbsp;<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'kubrick'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></font>
				</td>
				<td>
					<font face="verdana" size="1"><?php the_time(__('F jS, Y', 'kubrick')) ?></font>
				</td>
				<td>
					<font face="verdana" size="1"><?php printf(__('%s', 'kubrick'), get_the_category_list(', ')); ?></font>
				</td>
			</tr>
			<?php endwhile; ?>
		</tbody>
	</table>
	<br>
	<div class="navigation">
		<font face="verdana" size="1">
			<?php next_posts_link(__('&laquo; Older Entries', 'kubrick')); ?>
			&nbsp;&nbsp;&nbsp;
			<?php previous_posts_link(__('Newer Entries &raquo;', 'kubrick')); ?>
		</font>
	</div>
	<?php else : ?>
		<table border="0" width="602" cellpadding="2" cellspacing="0" style="border-collapse: collapse; border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-top-style: solid; border-top-width: 1; border-bottom-width: 0" bordercolor="#111111">
			<tbody>
				<tr>
					<td height="16px">
						<font face="verdana" size="1"><b>No Posts Found</b></font>
					</td>
				</tr>
			</tbody>
		</table>
		<table border="1" width="602" cellpadding="4" cellspacing="0" valign="TOP" style="border-collapse: collapse; border-left-style:dashed; border-left-width:0; border-right-style:dashed; border-right-width:0; border-top-width:0; border-bottom-style:dashed; border-bottom-width:0" bordercolor="#111111">
			<tbody>
				<tr>
					<td valign="top" bgcolor="#EEEEEE">
						<font face="verdana" size="1">
							<?php
							if ( is_category() ) { // If this is a category archive
								printf(__("Sorry, but there aren't any posts in the %s category yet.", 'kubrick'), single_cat_title('',false));
							} else if ( is_date() ) { // If this is a date archive
								echo(__("Sorry, but there aren't any posts with this date.", 'kubrick'));
							} else if ( is_author() ) { // If this is a category archive
								$userdata = get_userdatabylogin(get_query_var('author_name'));
								printf(__("Sorry, but there aren't any posts by %s yet.", 'kubrick'), $userdata->display_name);
							} else {
								echo(__('No posts found.', 'kubrick'));
							}
							?>
							<br><br>
							<?php get_search_form(); ?>
						</font>
					</td>
				</tr>
			</tbody>
		</table>
	<?php endif; ?>
</center>
</div>
<center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="12px"></center>
		</td>
	</tr>
    </tbody>
</table>
		
<?php get_footer(); ?>
