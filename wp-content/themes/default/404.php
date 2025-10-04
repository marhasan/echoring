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
					<table border="0" width="602" cellpadding="2" cellspacing="0" style="border-collapse: collapse; border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-top-style: solid; border-top-width: 1; border-bottom-width: 0" bordercolor="#111111">
						<tbody>
							<tr>
								<td height="16px">
									<font face="verdana" size="1"><b>Error 404</b></font>
								</td>
							</tr>
						</tbody>
					</table>  
					<table border="1" width="602" cellpadding="4" cellspacing="0" valign="TOP" style="border-collapse: collapse; border-left-style:dashed; border-left-width:0; border-right-style:dashed; border-right-width:0; border-top-width:0; border-bottom-style:dashed; border-bottom-width:0" bordercolor="#111111">
						<tbody>
							<tr>
								<td valign="top" bgcolor="#EEEEEE">
									<font face="verdana" size="1">
										<?php _e('Page Not Found', 'kubrick'); ?>
									</font>
								</td>
							</tr>
						</tbody>
					</table>     
				</center>
			</div> 	  
			<div align="center">
				<center>  	  
		
<?php get_footer(); ?>