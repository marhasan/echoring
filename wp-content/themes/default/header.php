<html>
<head>
<title>EchoRing Version 4.0B FINAL</title>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css" type="text/css" media="screen" />
<script language="JavaScript1.2">

var ns6=document.getElementById&&!document.all
var ie=document.all

function changeto(e,highlightcolor){
source=ie? event.srcElement : e.target
if (source.tagName=="TR"||source.tagName=="TABLE")
return
while(source.tagName!="TD"&&source.tagName!="HTML")
source=ns6? source.parentNode : source.parentElement
if (source.style.backgroundColor!=highlightcolor&&source.id!="ignore")
source.style.backgroundColor=highlightcolor
}

function contains_ns6(master, slave) { //check if slave is contained by master
if (!slave) return false; // Add null check
while (slave.parentNode)
if ((slave = slave.parentNode) == master)
return true;
return false;
}

function changeback(e,originalcolor){
if
(ie&&(event.fromElement.contains(event.toElement)||source.contains(event.toElement)||source.id=="ignore")||source.tagName=="TR"||source.tagName=="TABLE")
return
else if (ns6&&(contains_ns6(source, e.relatedTarget)||source.id=="ignore"))
return
if (ie&&event.toElement!=source||ns6&&e.relatedTarget!=source)
source.style.backgroundColor=originalcolor
}

</script>
<?php wp_head(); ?>
</head>
<body bgcolor="#ffffff" leftmargin="2" topmargin="2" link="darkblue" vlink="darkblue" alink="darkblue">
	<map name="m_echobeta_r2_c2">
		<area shape="rect" coords="1,0,399,94" href="<?php echo get_site_url(); ?>" title="EchoRing Version 4.0!" alt="EchoRing Version 4.0!">
	</map>
	<table border="0" cellpadding="0" cellspacing="0" width="765">
		<tbody>
			<tr>
				<td>
					<table border="0" cellpadding="0" cellspacing="0" width="765">
						<tbody>
							<tr>
								<td>
									<img name="echobeta_r1_c1" src="<?php echo get_template_directory_uri(); ?>/images/echobeta_r1_c1.gif" width="103" height="123" border="0" alt="">
								</td>
								<td>
									<table border="0" cellpadding="0" cellspacing="0" width="399">
										<tbody>
											<tr>
												<td background="<?php echo get_template_directory_uri(); ?>/images/echobeta_r1_c2.png" width="399" height="23">
													<div align="center">
														<center>
															<font face="verdana" size="1">
															<b><font color="darkblue">Total Websites:</font></b> 
															<?php // Get total number of posts in post-type-name
																if (post_type_exists('site')) {
																	$count_posts = wp_count_posts('site');
																	$total_posts = isset($count_posts->publish) ? $count_posts->publish : 0;
																	echo $total_posts;
																} else {
																	echo '0';
																}
															?>
															<b><font color="darkblue">Updates:</font></b>
															<?php
																	if (post_type_exists('site')) {
																		$args = array(
																				'post_type' => 'site',
																				'post_status' => 'publish',
																				'posts_per_page' => -1,
																				'fields' => 'ids',
																				'ignore_sticky_posts' => true
																		);
																		$site_ids = get_posts($args);
																		$updates_count = 0;
																		$latest_update = '';
																		foreach ($site_ids as $site_id) {
																			if (class_exists('EchoRingSites') && EchoRingSites::should_show_in_updates($site_id)) {
																				$updates_count++;
																			}
																			// Check for the most recent last updated date from all sites
																			if (class_exists('EchoRingSites')) {
																				$lu = EchoRingSites::get_last_updated($site_id);
																				if ($lu && ($latest_update === '' || strtotime($lu) > strtotime($latest_update))) {
																					$latest_update = $lu;
																				}
																			}
																		}
																		echo $updates_count;
																	} else {
																		echo '0';
																	}
																	?>
															<b><font color="darkblue">Last Updated:</font></b>
															<?php
																if (post_type_exists('site')) {
																	if (!empty($latest_update)) {
																		echo date('d.m.Y', strtotime($latest_update));
																	} else {
																		echo 'No post updated yet';
																	}
																} else {
																	echo 'N/A';
																}
															?>
															</font>
															
														</center>
														<font face="verdana" size="1"></font>
													</div>
													<font face="verdana" size="1"></font>
												</td>
											</tr>
											<tr>
												<td>
													<img name="echobeta_r2_c2" src="<?php echo get_template_directory_uri(); ?>/images/echobeta_r2_c2.gif" width="399" height="100" border="0" usemap="#m_echobeta_r2_c2" alt="">
												</td>
											</tr>
										</tbody>
									</table>
								</td>   
								<td background="<?php echo get_template_directory_uri(); ?>/images/echobeta_r1_c4.png" width="242" height="123">
								<?php if (is_user_logged_in()): ?>
									<table width="210" align="CENTER" border="0" cellpadding="0" cellspacing="0">
										<tbody>
											<tr><td colspan="2"><br><font face="verdana" size="1" color="darkblue"><center><b>Welcome!</b></center></font><br></td></tr>
											<tr><td colspan="2" align="center"><font face="verdana" size="1"><?php 
												$current_user = wp_get_current_user();
												echo 'Hello, ' . esc_html($current_user->display_name) . '!';
											?></font></td></tr>
											<tr><td colspan="2" align="center"><br>
													<?php if (current_user_can('manage_options')): ?>
														<a href="<?php echo admin_url(); ?>" style="font-family: Verdana; font-size: 8pt; color: darkblue;">Dashboard</a> | 
													<?php else: ?>
														<a href="<?php echo get_site_url(); ?>/webmaster-profile" style="font-family: Verdana; font-size: 8pt; color: darkblue;">My Profile</a> | 
													<?php endif; ?>
													<a href="<?php echo wp_logout_url(get_site_url()); ?>" style="font-family: Verdana; font-size: 8pt; color: darkblue;">Logout</a>
												</td></tr>
										</tbody>
									</table>
								<?php else: ?>
									<form action="<?php echo wp_login_url(get_site_url()); ?>" method="post">
										<table width="210" align="CENTER" border="0" cellpadding="0" cellspacing="0">
											<tbody>
												<tr><td colspan="2"><br><font face="verdana" size="1" color="darkblue"><center><b>Webmaster Login</b></center></font><br></td></tr>
												<tr><td><font face="verdana" size="1"><b>Username:</b></font></td><td>
											<input type="text" name="log" size="15" tabindex="1" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1"></td></tr>
										<tr><td colspan="2"><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="4"></td></tr>
										<tr><td><font face="verdana" size="1"><b>Password:</b></font></td><td>
											<input type="password" name="pwd" size="15" tabindex="2" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1"></td></tr>
												<tr><td></td><td><input type="image" src="<?php echo get_template_directory_uri(); ?>/images/login.gif" alt="Log In">
													<a href="/signup-site/"><img src="<?php echo get_template_directory_uri(); ?>/images/register.gif" width="60" height="12" border="0"></a></td></tr>
											</tbody>
										</table>
									</form>
								<?php endif; ?>
							</td>	   	   
								<td>
									<img name="echobeta_r1_c5" src="<?php echo get_template_directory_uri(); ?>/images/echobeta_r1_c5.gif" width="21" height="123" border="0" alt="">
	        					</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>