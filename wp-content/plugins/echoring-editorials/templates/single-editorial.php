<?php
/**
 * Template for single editorial posts
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
					<font face="verdana" size="1"><b>EchoRing <?php echo esc_html(get_post_meta(get_the_ID(), '_editorial_type', true)); ?></b> <?php the_title(); ?></font>
				</td>
			</tr>
		</tbody>
	</table>  
	<table border="1" width="602" cellpadding="4" cellspacing="0" valign="TOP" style="border-collapse: collapse; border-left-style:dashed; border-left-width:0; border-right-style:dashed; border-right-width:0; border-top-width:0; border-bottom-style:dashed; border-bottom-width:0" bordercolor="#111111">
		<tbody>
			<tr>
				<td valign="top" bgcolor="#EEEEEE">
					<font face="verdana" size="1">
						<?php the_content(); ?>
					</font>
				</td>
			</tr>
		</tbody>
	</table>
	<?php endwhile; endif; ?>      
</center>
</div> 	  
<div align="center">
<center>
			</td>
		</tr>
	</tbody>
</table>
<?php get_footer(); ?>