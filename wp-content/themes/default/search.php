<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>

	<div id="content" class="narrowcolumn" role="main">
		
	<h2><?php _e('Search Results', 'kubrick'); ?></h2>

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div <?php post_class(); ?>>
				<div id="post-<?php the_ID(); ?>">
					- <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'kubrick'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a>
				</div>
			</div>

		<?php endwhile; ?>

		<? the_posts_pagination( array(
		    'mid_size'=>3,
			'prev_text' => _( '« Previous'),
			'next_text' => _( 'Next »'),
			) ); 
		?>

	<?php else : ?>

		<h2 class="center"><?php _e('No posts found. Try a different search?', 'kubrick'); ?></h2>
		<?php get_search_form(); ?>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
