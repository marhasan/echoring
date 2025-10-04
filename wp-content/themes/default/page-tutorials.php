<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
?>
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
					<font face="verdana" size="1"><b><?php the_title(); ?></b></font>
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
		<tr>
			<td width="130px" height="20px" bgcolor="#eeeeee" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
				<font face="verdana" size="1">&nbsp;<b>Tutorial:</b></font>
			</td>
			<td width="100px" bgcolor="#eeeeee" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
				<font face="verdana" size="1" width="80px"><b>Author:</b></font>
			</td>
			<td width="100px" bgcolor="#eeeeee" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
				<font face="verdana" size="1"><b>Type:</b></font>
			</td>
			<td width="100px" bgcolor="#eeeeee" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
				<font face="verdana" size="1" width="47px"><b>Class:</b></font>
			</td>
			<td width="100px" bgcolor="#eeeeee" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
				<font face="verdana" size="1"><b>Date:</b></font>
			</td>
		</tr>
		<?php $query = new WP_Query( array(
                'post_type' => 'tutorial',
                'posts_per_page' => -1
            ) );
            while ($query->have_posts()) : $query->the_post(); ?>
		<tr height="25px">
			<td>
				<font face="verdana" size="1">&nbsp;<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></font>
			</td>
			<td>
				<font face="verdana" size="1">
					<?php the_author(); ?>
				</font>
			</td>
			<td>
				<font face="verdana" size="1">Snippet</font>
			</td>
			<td>
				<font face="verdana" size="1">Wordpress</font>
			</td>
			<td>
				<font face="verdana" size="1"><?php the_date( 'F Y' ); ?></font>
			</td>
		</tr>
		<?php endwhile; ?>
	</tbody>
</table>  
		
<?php get_footer(); ?>