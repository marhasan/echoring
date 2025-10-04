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
 	 	 	<?php
 	 	 	$current_term = get_queried_object();
 	 	 	$taxonomy = get_taxonomy($current_term->taxonomy);
 	 	 	echo $current_term->name;
 	 	 	?>
 	 	 </h2>

		<?php //*The Query*//
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			query_posts($query_string . '&post_type=game&posts_per_page=20&orderby=title&order=asc&paged='.$paged);
			if(have_posts()) : while(have_posts()) : the_post();
		?>
		<div <?php post_class(); ?>>
				- <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
		</div>
		<?php endwhile; ?>

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
		<? the_posts_pagination( array(
		    'mid_size'=>3,
			'prev_text' => _( '« Previous'),
			'next_text' => _( 'Next »'),
			) ); 
		?>
	</div>

<?php get_footer(); ?>
