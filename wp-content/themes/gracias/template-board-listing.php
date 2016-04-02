<?php
/*  Template Name: Board Listing

	Custom template that combines the content of a Page with a
	table-like display of all board members or just the
	members of a particular board.

	Part of:  Gracias WordPress theme by Anthony Kozar
	License:  GNU General Public License v2 or later
	Created:  April 1, 2016

	Customized from the "page.php" and "index.php" templates of the original
	Pinboard theme by One Designs, Inc.
	
*/
?>
<?php gracias_set_masonry_selectors(null, '.board-member-list', '#content .board-member-list', '.board-member-list', $useMasonry = false
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

			<!-- Display board members in a table (similar to Publications type) -->
			<?php 
				// Code from <http://codex.wordpress.org/Function_Reference/next_posts_link#Usage_when_querying_the_loop_with_WP_Query>
				// set the "paged" parameter (use 'page' if the query is on a static front page)
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

				/*	The 'wpcf-display-mode' parameter controls which fields are displayed
					in which columns.  The possible values and layouts are:
					
						mode	column 1		column 2		column 3
						----	--------		--------		--------
						0		name			title			company
								board position
						1		name			title			company
						2		name			board position	title, company
						3		name			board position
						
						Add 4 to the display mode to enable linking to the individual board member pages.
				 */
				$display_mode = (int) get_post_meta(get_the_ID(), 'wpcf-display-mode', true);
				// if ($display_mode == 0)	$display_mode = 7;	// show all by default
				
				$board_slug = get_post_meta(get_the_ID(), 'wpcf-board', true);
				$tax_parms = array(
								array(
									'taxonomy' => 'grcs_board',
									'field'    => 'slug',
									'terms'    => $board_slug
								)
							);
				$posts_per_page = get_post_meta(get_the_ID(), 'wpcf-posts-per-page', true);
				$items = new WP_Query(array('post_type' => 'grcs_board_member', 'tax_query' => $tax_parms, 'orderby' => 'menu_order', 
											'order' => 'ASC', 'paged' => $paged, 'posts_per_page' => $posts_per_page));
			?>
			<?php if( $items->have_posts() ) : ?>
				<ul class="board-member-list">
				<?php while( $items->have_posts() ) : $items->the_post(); ?>
					<li class="bm-entry">
						<span class="bm-title">
							<?php if ($display_mode >= 4) : $display_mode -= 4; ?>
								<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
							<?php else :
								the_title();
								endif;
							?>
							<?php if ($display_mode == 0) gracias_text_field('', 'wpcf-bm-position', '<br />', '', false); ?>
						</span>
						<?php
							if ($display_mode < 2) {
								gracias_text_field('', 'wpcf-bm-company-title', '<span class="bm-col-2">', '</span>', false);
								gracias_text_field('', 'wpcf-bm-company', '<span class="bm-col-3">', '</span>', false);
							}
							else if ($display_mode == 2) {
								gracias_text_field('', 'wpcf-bm-position', '<span class="bm-col-2">', '</span>', false);
								gracias_dbl_text_field('', 'wpcf-bm-company-title', 'wpcf-bm-company', '<span class="bm-col-3">', ', ', '</span>');
							}
							else if ($display_mode == 3) {
								gracias_text_field('', 'wpcf-bm-position', '<span class="bm-col-2">', '</span>', false);
							}	
						?>
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