<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
?>

	<?php get_sidebar(); ?>

	<div id="content" class="narrowcolumn" role="main">

		<h2>
			Games Released in 
			<?php
			$current_term = get_queried_object();
			$taxonomy = get_taxonomy($current_term->taxonomy);
			echo $current_term->name;
			?>
		</h2>
		<table>
			<thead>
				<td>Game Title</td>
				<td>Year</td>
				<td>Genre</td>
				<td>Platform</td>
			</thead>
			<tbody>
				<?php //*The Query*//
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				query_posts($query_string . '&post_type=webs&posts_per_page=15&orderby=title&order=asc&paged='.$paged);
				if(have_posts()) : while(have_posts()) : the_post();
				?>
				<tr <?php post_class(); ?>>
					<td width="50%"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
					<td width="10%"> <?php echo get_the_term_list( $post->ID, 'years'); ?></td>
					<td width="10%"><?php echo get_the_term_list( $post->ID, 'genre', '',', '); ?></td>
					<td width="10%"><?php echo get_the_term_list( $post->ID, 'platform', '',', '); ?></td>
				</tr>
				<?php endwhile; ?>
			 </tbody>
		 </table>

		<?php else :
	
			if ( is_category() ) { // If this is a category archive
				printf("<h2 class='center'>".__("Sorry, but there aren't any posts in the %s category yet.", 'kubrick').'</h2>', single_cat_title('',false));
			} else if ( is_date() ) { // If this is a date archive
				echo('<h2>'.__("Sorry, but there aren't any posts with this date.", 'kubrick').'</h2>');
			} else if ( is_author() ) { // If this is a category archive
				$userdata = get_userdatabylogin(get_query_var('author_name'));
				printf("<h2 class='center'>".__("Sorry, but there aren't any posts by %s yet.", 'kubrick')."</h2>", $userdata->display_name);
			} else {
				echo("<h2 class='center'>".__('No games found.', 'kubrick').'</h2>');
			}
		  get_search_form();
		endif;
		?>
		<p class="clear">
					
				</p>
		<?php if(function_exists('pagenavi')) { pagenavi(); } ?>
	</div>

<?php get_footer(); ?>
