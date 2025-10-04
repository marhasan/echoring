<?php
/**
 * Template for editorial archive page
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
	<table border="0" width="602" cellpadding="2" cellspacing="0" style="border-collapse: collapse; border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-top-style: solid; border-top-width: 1; border-bottom-width: 0" bordercolor="#111111">
		<tbody>
			<tr>
				<td height="16px">
					<font face="verdana" size="1"><b>Editorials</b> Complete Listing of All Our Editorials!</font>
				</td>
			</tr>
		</tbody>
	</table>  
	<table border="1" width="602" cellpadding="4" cellspacing="0" valign="TOP" style="border-collapse: collapse; border-left-style:dashed; border-left-width:0; border-right-style:dashed; border-right-width:0; border-top-width:0; border-bottom-style:dashed; border-bottom-width:0" bordercolor="#111111">
		<tbody>
			<tr>
				<td valign="top" bgcolor="#EEEEEE">
					<font face="verdana" size="1">
						Welcome to our editorials archive. Below you'll find a complete listing of all our editorial interviews.
					</font>
				</td>
			</tr>
		</tbody>
	</table>
</center>
</div> 	  
<center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="12px"></center>
<div align="center">
<center>
<table class="tableone" border="0" width="602" cellpadding="0" cellspacing="0" valign="TOP" style="border-collapse: collapse; border-style: solid; border-width: 1" bordercolor="#111111">
	<tbody>
		<tr>
			<td width="130px" height="20px" bgcolor="#eeeeee" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
				<font face="verdana" size="1">&nbsp;<b>Subject:</b></font>
			</td>
			<td width="100px" bgcolor="#eeeeee" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
				<font face="verdana" size="1" width="80px"><b>Interviewer:</b></font>
			</td>
			<td width="100px" bgcolor="#eeeeee" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
				<font face="verdana" size="1"><b>Type:</b></font>
			</td>
			<td width="100px" bgcolor="#eeeeee" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
				<font face="verdana" size="1" width="47px"><b>Date:</b></font>
			</td>
			<td width="100px" bgcolor="#eeeeee" style="border-left-width: 1; border-right-width: 1; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
				<font face="verdana" size="1"><b>Download:</b></font>
			</td>
		</tr>
		 <?php 
		 $query = new WP_Query(array(
			'post_type' => 'editorial',
			'posts_per_page' => -1,
			'orderby' => 'date',
			'order' => 'DESC'
		));
		
		if ($query->have_posts()) : 
			while ($query->have_posts()) : $query->the_post(); 
				$interviewer = get_post_meta(get_the_ID(), '_editorial_interviewer', true);
				$editorial_type = get_post_meta(get_the_ID(), '_editorial_type', true);
				$download_link = get_post_meta(get_the_ID(), '_editorial_download', true);
		?>
		<tr height="25px">
			<td>
				<font face="verdana" size="1">&nbsp;<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></font>
			</td>
			<td>
				<font face="verdana" size="1">
					<?php echo esc_html($interviewer); ?>
				</font>
			</td>
			<td>
				<font face="verdana" size="1"><?php echo esc_html($editorial_type); ?></font>
			</td>
			<td>
				<font face="verdana" size="1"><?php echo get_the_date('F Y'); ?></font>
			</td>
			<td>
				<font face="verdana" size="1">
					<?php if ($download_link) : ?>
						<a href="<?php echo esc_url($download_link); ?>" target="_blank">Download</a>
					<?php else: ?>
						&nbsp;
					<?php endif; ?>
				</font>
			</td>
		</tr>
		<?php 
			endwhile; 
			wp_reset_postdata();
		endif; 
		?>
	</tbody>
</table>  
</center>
</div>
		</td>
	</tr>
    </tbody>
</table>
		
<?php get_footer(); ?>