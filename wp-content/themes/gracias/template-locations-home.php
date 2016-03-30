<?php
/*  Template Name: Locations Home

	Custom template that combines the content of a Page with a
	table-like display of locations with contact info and hours.

	Part of:  Gracias WordPress theme by Anthony Kozar
	License:  GNU General Public License v2 or later
	Created:  March 29, 2016

	Customized from the "page.php" and "index.php" templates of the original
	Pinboard theme by One Designs, Inc.
	
*/
?>
<?php gracias_set_masonry_selectors(null, '.location-list', '#content .location-list', '.location-list', $useMasonry = false
									/* , $itemSelector = '.pub-entry, #infscr-loading' */); ?>
<?php get_header(); ?>
	<?php if( is_front_page() ) : ?>
		<?php if( pinboard_get_option( 'slider' ) ) : ?>
			<?php get_template_part( 'slider' ); ?>
		<?php endif; ?>
		<?php get_sidebar( 'wide' ); ?>
		<?php get_sidebar( 'boxes' ); ?>
	<?php endif; ?>
	<div id="container">
		<section id="content" <?php pinboard_content_class(); ?>>
			<!-- Intro text and titles from the Page -->
			<?php if( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
					<div class="entry">
						<?php if(! is_front_page()): // display the title except on the front page ?>
							<header class="entry-header">
								<<?php pinboard_title_tag( 'post' ); ?> class="entry-title"><?php the_title(); ?></<?php pinboard_title_tag( 'post' ); ?>>
								<?php gracias_subtitle(); ?>
							</header><!-- .entry-header -->
						<?php endif; ?>
						<div class="entry-content">
							<?php gracias_featured_image(); ?>
							<?php the_content(); ?>

			<!-- Display locations in a table (similar to Publications type) -->
			<?php 
				// Code from <http://codex.wordpress.org/Function_Reference/next_posts_link#Usage_when_querying_the_loop_with_WP_Query>
				// set the "paged" parameter (use 'page' if the query is on a static front page)
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

				$posts_per_page = get_post_meta(get_the_ID(), 'wpcf-posts-per-page', true);
				$items = new WP_Query(array('post_type' => 'grcs_location', 'paged' => $paged, 'posts_per_page' => $posts_per_page));
			?>
			<?php if( $items->have_posts() ) : ?>
				<ul class="location-list">
				<?php while( $items->have_posts() ) : $items->the_post(); ?>
					<li class="loc-entry">
						<span class="loc-title">
							<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
							<?php gracias_text_field('', 'wpcf-location-address', '<br />', '', true); ?>
						</span>
						<?php gracias_text_field('', 'wpcf-location-phone', '<span class="loc-phone">', '</span>', false); ?>
						<?php gracias_text_field('', 'wpcf-location-hours', '<span class="loc-hours">', '</span>', true); ?>
					</li>
					<?php endwhile; ?>
				</ul>
				<div class="clear"></div>
				<?php pinboard_posts_nav($items); ?>
			<?php else : ?>
				<p>No locations found.</p>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>

							<div class="clear"></div>
						</div><!-- .entry-content -->
						<?php wp_link_pages( array( 'before' => '<footer class="entry-utility"><p class="post-pagination">' . __( 'Pages:', 'pinboard' ), 'after' => '</p></footer><!-- .entry-utility -->' ) ); ?>
					</div><!-- .entry -->
					<?php comments_template(); ?>
				</article><!-- .post -->
			<?php else : ?>
				<?php pinboard_404(); ?>
			<?php endif; ?>
		</section><!-- #content -->

		<?php if( ( 'no-sidebars' != pinboard_get_option( 'layout' ) ) && ( 'full-width' != pinboard_get_option( 'layout' ) ) ) : ?>
			<?php get_sidebar(); ?>
		<?php endif; ?>
		<div class="clear"></div>
	</div><!-- #container -->
<?php get_footer(); ?>