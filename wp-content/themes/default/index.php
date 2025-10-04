<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
?>
			<tr>
				<td>
					<table border="0" cellpadding="0" cellspacing="0" width="765">
						<tbody>
							<tr>
								<?php get_sidebar(); ?>
								<td background="<?php echo get_template_directory_uri(); ?>/images/echobeta_r3_c3.gif" width="641" height="427" valign="top">
		<center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="12px"></center>
<div align="center">
<center>
	<?php $query = new WP_Query( array(
		'post_type' => 'post',
		'posts_per_page' => 1
		) );
		while ($query->have_posts()) : $query->the_post(); ?>
	<table class="news-header" border="0" width="602" cellpadding="2" cellspacing="0" style="border-collapse: collapse;">
		<tbody>
			<tr>
				<td height="20px">
					<font face="verdana" size="1">
						<b>News Update:</b> <?php the_title(); ?>
					</font>
				</td>
				<td align="right">
					<font face="verdana" size="1">
						<b>Posted By:</b> <?php the_author(); ?>&nbsp;<b>Rating:</b> <?php echo display_post_rating(get_the_ID()); ?>
					</font>
				</td>
			</tr>
		</tbody>
	</table>  
	<table class="news-content" border="0" width="602" cellpadding="4" cellspacing="0" valign="TOP" style="border-collapse: collapse;">
		<tbody>
			<tr>
				<td width="85px" bgcolor="#eeeeee" valign="top">
					<center>
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 75 ); ?>
					</center>
					<center>
						<img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="5px">
					</center>
					<font face="verdana" size="1">
						<center><b>Rate Entry</b></center>
						<center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="8px"></center>
						<center>
							<select name="rating" onchange="return submitRating(this);" size="1" style="background: #e2e4f3; font-family: verdana; font-size: 10px">
                        <option value="">-------</option>
                        <option value="5" data-post-id="<?php echo get_the_ID(); ?>">5 Stars</option>
                        <option value="4" data-post-id="<?php echo get_the_ID(); ?>">4 Stars</option>
                        <option value="3" data-post-id="<?php echo get_the_ID(); ?>">3 Stars</option>
                        <option value="2" data-post-id="<?php echo get_the_ID(); ?>">2 Stars</option>
                        <option value="1" data-post-id="<?php echo get_the_ID(); ?>">1 Stars</option>
                    </select>
						</center>
						<center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="8px"></center>
						<b></b><a href="<?php comments_link(); ?>">Comments (<?php echo $post->comment_count; ?>)</a>
						<center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="8px"></center>
						<b>-</b><a href="<?php echo get_post_type_archive_link('post'); ?>">The Archive</a>
						<center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="8px"></center>
						<b>-</b><a href="template.php?page=thestaff.php">Team Echo</a>
					</font>
				</td>
				<td valign="top" bgcolor="#EEEEEE">
					<font face="verdana" size="1">
						<?php the_content('<p align="justify">' . __('Read the rest of this entry &raquo;', 'kubrick') . '</p>'); ?>
					</font>
				</td>
			</tr>
		</tbody>
	</table>
	<?php endwhile; ?>	      
</center>
</div>
<center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="12px"></center>  	  
<div align="center">
<center>  	  
<table class="tableone" border="0" width="602" cellpadding="0" cellspacing="0" valign="TOP" style="border-collapse: collapse;">
	<tbody>
		<tr>
			<td width="130px" height="20px" bgcolor="#eeeeee">
				<font face="verdana" size="1">&nbsp;<b>Name:</b></font>
			</td>
			<td width="65px" bgcolor="#eeeeee">
				<font face="verdana" size="1" width="80px"><b>Owner:</b></font>
			</td>
			<td width="70px" bgcolor="#eeeeee">
				<font face="verdana" size="1"><b>Language:</b></font>
			</td>
			<td bgcolor="#eeeeee">
				<font face="verdana" size="1" width="47px"><b>Type:</b></font>
			</td>
			<td width="50px" bgcolor="#eeeeee">
				<font face="verdana" size="1"><b>Games:</b></font>
			</td>
			<td width="45px" bgcolor="#eeeeee">
				<font face="verdana" size="1"><b>Apps:</b></font>
			</td>
			<td width="60px" bgcolor="#eeeeee">
				<font face="verdana" size="1"><b>Features:</b></font>
			</td>
			<td width="65px" bgcolor="#eeeeee">
				<font face="verdana" size="1"><b>Rating:</b></font>
			</td>
			<td width="55px" bgcolor="#eeeeee">
				<font face="verdana" size="1"><b>Status:</b></font>
			</td>
		</tr>
		<?php
			// query args
			$args = array(
					'posts_per_page'        => '-1',
					'post_type'             => 'site',
					'post_status'           => 'publish',
					'orderby'               => 'modified',
					'order'                 => 'DESC',
					'ignore_sticky_posts'   => '1',
					'caller_get_posts'      => 1
			);
		
			// query
			$updated = new WP_Query($args);
			
			while($updated->have_posts()) : $updated->the_post();
					
			// Use the new WordPress update management system
			if (EchoRingSites::should_show_in_updates()) : 
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
				
				if ($is_new_site) : ?>
					<img src="<?php echo get_template_directory_uri(); ?>/images/site_new.png">
				<?php elseif ($is_updated) : ?>
					<img src="<?php echo get_template_directory_uri(); ?>/images/site_updated.png"> 
				<?php else : ?>
					<img src="<?php echo get_template_directory_uri(); ?>/images/site_updated.png"> 
				<?php endif; ?>
			</td>
		</tr>
		<?php endif; endwhile; ?>
	</tbody>
</table>
	
	<font face="verdana" size="1">
		<center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="12px"></center>
		<center><a href="<?php echo get_site_url(); ?>/the-listing/"><b>Click Here To View The Entire Listing</b></a></center>
		<center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="12px"></center>
	</font>
		
	<table border="0" width="602" cellpadding="2" cellspacing="0" style="border-collapse: collapse; border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-top-style: solid; border-top-width: 1; border-bottom-width: 0" bordercolor="#111111">
		<tbody>
			<tr>
				<td height="20px">
					<font face="verdana" size="1"><b>EchoRing Interactive</b> Weekly poll and monthly site awards!</font>
				</td>
			</tr>
		</tbody>
	</table>  
	
	<table border="1" width="602" cellpadding="4" cellspacing="0" valign="TOP" style="border-collapse: collapse; border-left-style:dashed; border-left-width:0; border-right-style:dashed; border-right-width:0; border-top-width:0; border-bottom-style:dashed; border-bottom-width:0" bordercolor="#111111">
		<tbody>
			<tr>
				<td valign="top" bgcolor="#EEEEEE">
					<table border="0" cellspacing="0" cellpadding="0" width="585">
						<tbody>
							<tr>
								<td>
									<table border="0" cellspacing="0" cellpadding="2">
										<tbody>
											<tr>
												<td colspan="2">
													<font face="verdana" size="1"><center><b>December 2003 EchoAwards</b></center><br></font>
												</td>
											</tr>
											<tr>
												<td>
													<font face="verdana" size="1">&nbsp; <b>Fastest Loading:</b> <a href="#" target="_blank">Download Free Games</a></font>
												</td>
												<td>
													<font face="verdana" size="1"><b>Votes:</b> 3/3 100%</font>
												</td>
											</tr>
											<tr>
												<td>
													<font face="verdana" size="1">&nbsp; <b>Best New Member:</b> <a href="#" target="_blank">Syntax Error</a></font>
												</td>
												<td>
													<font face="verdana" size="1"><b>Votes:</b>  3/3 100%</font>
												</td>
											</tr>
											<tr>
												<td>
													<font face="verdana" size="1">&nbsp; <b>Most Dedicated:</b> <a href="#" target="_blank">Classic Games</a></font>
												</td>
												<td>
													<font face="verdana" size="1"><b>Votes:</b>  3/3 100%</font>
												</td>
											</tr>
											<tr>
												<td>
													<font face="verdana" size="1">&nbsp; <b>Most Underrated:</b> <a href="#" target="_blank">Force For Good</a></font>
												</td>
												<td>
													<font face="verdana" size="1"><b>Votes:</b>  2/4 50%</font>
												</td>
											</tr>
											<tr>
												<td colspan="2">
													<font face="verdana" size="1"><br><p align="JUSTIFY">Winners are decided on by a majority vote of our staff. The awards are issued once every month. The categories will change from month to month. The awards are completely impartial. If you didn't win this time, just do your best and I'm sure you'll walk away with a trophy next month. <b>-The Staff</b></p>
													</font>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
				
								<td align="right">
									<form method="post" action="#">
										<table border="0" cellspacing="1" cellpadding="0" width="150">
											<tbody>
												<tr>
													<td width="100%">
														<table cellspacing="0" cellpadding="1" width="100%" style="border: 1px solid #eeeeee; " bgcolor="#eeeeee">
															<tbody>
																<tr>
																	<td width="100%" valign="middle">
																		<font color="black" size="1" face="Verdana">
																			<b>How many computers do you have?<br></b>
																		</font>
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>

												<tr>	
													<td width="100%">
														<table cellspacing="0" cellpadding="1" width="100%" style="border: 1px solid #eeeeee; " bgcolor="#eeeeee">
															<tbody>
																<tr>
																	<td width="10" valign="top" align="left">
																		<input type="radio" name="cid" value="1">
																	</td>
																	<td width="100%" valign="middle" align="left">
																		<font color="black" face="Verdana" size="1">Only One</font>
																	</td>
																</tr>
			
																<tr>
																	<td width="10" valign="top" align="left">
																		<input type="radio" name="cid" value="2">
																	</td>
																	<td width="100%" valign="middle" align="left">
																		<font color="black" face="Verdana" size="1">Two</font>
																	</td>
																</tr>
			
																<tr>
																	<td width="10" valign="top" align="left">
																		<input type="radio" name="cid" value="3">
																	</td>
																	<td width="100%" valign="middle" align="left">
																		<font color="black" face="Verdana" size="1">Three</font>
																	</td>
																</tr>
			
																<tr>
																	<td width="10" valign="top" align="left">
																		<input type="radio" name="cid" value="4">
																	</td>
																	<td width="100%" valign="middle" align="left">
																		<font color="black" face="Verdana" size="1">Four or more</font>
																	</td>
																</tr>
																
																<tr>
																	<td width="10" valign="top" align="left">
																		<input type="radio" name="cid" value="5">
																	</td>
																	<td width="100%" valign="middle" align="left">
																		<font color="black" face="Verdana" size="1">I lost count...</font>
																	</td>
																</tr>
																<tr>
																	<td width="100%" colspan="2">
																		<font color="#000000" size="1" face="Verdana">&nbsp;
																			<input type="submit" name="s_boom" value="Vote!" style="font-size: 10px">
																		</font>
																		<font size="1" face="Verdana">
																			<a href="#" target="_self">Results</a>
																		</font>
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
											</tbody>
										</table>
										<input type="hidden" name="pid" value="1">
										<input type="hidden" name="ref" value="#">
									</form>
								</td>
							</tr>
						</tbody>
					</table> 
				</td>
			</tr>
		</tbody>
	</table>

	<center>
						<img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="12px">
					</center>  
	
	<table border="0" cellpadding="0" cellspacing="0" width="602">
	<tr>
				<td colspan="2">
					
					<center>
						<table border="0" width="602" cellpadding="2" cellspacing="0" style="border-collapse: collapse; border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-top-style: solid; border-top-width: 1; border-bottom-width: 0" bordercolor="#111111">
							<tbody>
					<tr>
						<td height="20px">
							<font face="verdana" size="1"><b>EchoRing Search</b> A simple way to find your favorite titles!</font>
						</td>
					</tr>
				</tbody>
						</table>  
						<table border="1" width="602" cellpadding="4" cellspacing="0" valign="TOP" style="border-collapse: collapse; border-left-style:dashed; border-left-width:0; border-right-style:dashed; border-right-width:0; border-top-width:0; border-bottom-style:dashed; border-bottom-width:0" bordercolor="#111111">
							<tbody>
								<tr>
									<form action="https://web.archive.org/web/20031229020059/http://www.abandongames.com/search.php" method="post"></form>
									<td bgcolor="#EEEEEE">
										<table border="0" cellpadding="0" cellspacing="0">
											<tbody>
												<tr>
													<td>
														<table border="0">
															<tbody>
																<tr>
																	<td colspan="2">
																		<img src="<?php echo get_template_directory_uri(); ?>/images/echosearch.gif">
																	</td>
																</tr>
																<tr>
																	<td>
																		<input type="text" name="search" size="37" style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1; background-color: #E2E4F3">
																	</td>
																	<td>
																		<input type="image" src="<?php echo get_template_directory_uri(); ?>/images/search.gif" alt="Search">
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
													<td>
														<font face="verdana" size="1">
															<p align="JUSTIFY">Echo and AbandonGames have teamed up to provide you with one of the most comprehensive Abandonware searches on the web. If its Abandonware, you'll find it using this search. EchoRing is not accountable for the contents of the results provided by AbandonGames.com.</p>
														</font>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</center>
				</td>
			</tr>
		</table>
<?php get_footer(); ?>