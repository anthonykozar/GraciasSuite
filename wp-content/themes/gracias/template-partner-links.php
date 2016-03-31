<?php
/*  Template Name: Partner Links Page

	Custom template that combines the content of a Page with a
	grid of images & links of the Simple Links post type.

	Part of:  Gracias WordPress theme by Anthony Kozar
	License:  GNU General Public License v2 or later
	Created:  March 31, 2016

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
							<div class="clear"></div>

			<!-- Display each image and its title/link in floating DIVs -->
			<?php 
				// Code from <http://codex.wordpress.org/Function_Reference/next_posts_link#Usage_when_querying_the_loop_with_WP_Query>
				// set the "paged" parameter (use 'page' if the query is on a static front page)
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

				$linkcat_slug = get_post_meta(get_the_ID(), 'wpcf-link-category', true);
				$tax_parms = array(
								array(
									'taxonomy' => 'simple_link_category',
									'field'    => 'slug',
									'terms'    => $linkcat_slug
								)
							);
				$posts_per_page = get_post_meta(get_the_ID(), 'wpcf-posts-per-page', true);
				$items = new WP_Query(array('post_type' => 'simple_link', 'tax_query' => $tax_parms, 'paged' => $paged, 
											'orderby' => 'title', 'order' => 'ASC', 'posts_per_page' => $posts_per_page));
			?>
			<?php if( $items->have_posts() ) : ?>
				<div class="links-grid">
				<?php while( $items->have_posts() ) : $items->the_post(); ?>
					<div class="links-entry">
						<?php
							// link directly to the link's URL (not the post)
							$link_url = get_post_meta(get_the_ID(), 'web_address', true);
							// only wrap the image in an <a> tag if we have a URL
							if ($link_url) echo '<a href="' . $link_url . '" title="' . the_title_attribute('echo=0') . '">';
							// this code block customized from function pinboard_post_thumbnail()
							if (has_post_thumbnail()) {
								echo '<figure class="entry-thumbnail">';
								the_post_thumbnail('thumbnail');
								echo '</figure>';
							}
							echo '<p>';
							the_title();
							echo '</p>';
							if ($link_url) echo '</a>';
						?>
					</div>
				<?php endwhile; ?>
				</div>
				<div class="clear"></div>
				<?php pinboard_posts_nav($items); ?>
			<?php else : ?>
				<p>No links found.</p>
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