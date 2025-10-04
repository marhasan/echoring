<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>
<td valign="top" background="<?php echo get_template_directory_uri(); ?>/images/bgslice.png" width="124">
	<table border="0" cellpadding="0" cellspacing="0" width="124" valign="top">
		<tbody>
			<tr>
				<td valign="top">
					<img name="echobeta_r3_c1" src="<?php echo get_template_directory_uri(); ?>/images/echobeta_r3_c1.png" width="124" height="91" border="0" alt="">
				</td>
			</tr>
			<tr>
				<td valign="top" background="<?php echo get_template_directory_uri(); ?>/images/echobeta_r4_c1.png" width="124" height="301">
					<center>
						<img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="12">
					</center>
									
									<?php
									// Get the primary menu
									$menu_name = 'primary';
									$locations = get_nav_menu_locations();
									$menu = null;
									
									// Check if the primary location exists and has a menu assigned
									if (isset($locations[$menu_name]) && $locations[$menu_name]) {
										$menu = wp_get_nav_menu_object($locations[$menu_name]);
									}
									
									if ($menu) {
										$menu_items = wp_get_nav_menu_items($menu->term_id);
										
										foreach ($menu_items as $key => $menu_item) {
											// Check if this is a separator (you can add custom class 'separator' to menu items)
											if (in_array('separator', $menu_item->classes) || $menu_item->title == '---') {
												echo '<center><img src="' . get_template_directory_uri() . '/images/spacer.png" height="12"></center>';
												continue;
											}
											
											// Add spacer before each menu item (except the first one)
											if ($key > 0) {
												echo '<center><img src="' . get_template_directory_uri() . '/images/spacer.png" height="3"></center>';
											}
											?>
											<table width="100px" align="center" style="border-collapse: collapse; border-style: solid; border-width: 1" bordercolor="#111111" cellpadding="0" cellspacing="0" onmouseover="changeto(event, '#FAEBD7')" onmouseout="changeback(event, 'white')">
												<tbody>
													<tr>
														<td bgcolor="#ffffff" style="background-color: white;">
															<font face="verdana" size="1">
																<b>::</b> <a href="<?php echo $menu_item->url; ?>"<?php echo $menu_item->target ? ' target="' . $menu_item->target . '"' : ''; ?>><?php echo $menu_item->title; ?></a>
															</font>
														</td>
													</tr>
												</tbody>
											</table>
											<?php
										}
									}
									?>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<img name="echobeta_r5_c1" src="<?php echo get_template_directory_uri(); ?>/images/echobeta_r5_c1.png" width="124" height="35" border="0" alt="">
								</td>
							</tr>
						</tbody>
					</table>
</td>