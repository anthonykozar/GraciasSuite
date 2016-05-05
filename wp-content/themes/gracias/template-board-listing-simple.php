<?php
/*  Template Name: Board Listing (Simple)

	Custom template that combines the content of a Page with a
	simple multi-column display of all board members or just the
	members of a particular board.

	Part of:  Gracias WordPress theme by Anthony Kozar
	License:  GNU General Public License v2 or later
	Created:  April 29, 2016

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

			<!-- Display board members in a multi-column list -->
			<?php 
				// Code from <http://codex.wordpress.org/Function_Reference/next_posts_link#Usage_when_querying_the_loop_with_WP_Query>
				// set the "paged" parameter (use 'page' if the query is on a static front page)
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

				/*	The 'wpcf-display-mode' parameter controls which fields are displayed
					for each board member.  The possible values are:
							0	show name & board position
							1	show just the name
					
						Add 4 to the display mode to enable linking to the individual board member pages.
				 */
				$display_mode = (int) get_post_meta(get_the_ID(), 'wpcf-display-mode', true);
				if ($display_mode >= 4) {
					$display_mode -= 4;
					$link_names = true;
				}
				else $link_names = false;
				
				// The 'wpcf-num-columns' parameter can be between 0 and 4
				$num_columns = (int) get_post_meta(get_the_ID(), 'wpcf-num-columns', true);
				switch ($num_columns) {
					case 1:		$classes = 'gracias-column column onecol'; break;
					case 2:		$classes = 'gracias-column column twocol'; break;
					case 3:		$classes = 'gracias-column column threecol'; break;
					case 4:		$classes = 'gracias-column column fourcol'; break;
					default:	$num_columns = 2; $classes = 'gracias-column column twocol'; break;
				}
		
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
			<?php if ($items->have_posts()):
				// display items in $num_columns columns
				$numposts = $items->post_count;
				$posts_per_col = $numposts / $num_columns;	// this can be a float, but that ensures that the last column is the shortest
				$start_column = true;
				$posts_output = 0;
				while ($items->have_posts()) {
					$items->the_post();
					if ($start_column) {
						echo "<div class=\"$classes\">\n";
						echo '<ul class="board-member-list-simple">' . "\n";
						$start_column = false;
						$closed_column = false;
					}
					echo '<li class="bm-entry-simple"><span class="bm-name">';
					if ($link_names):
						echo '<a href="' . get_permalink() . '" rel="bookmark" title="' . the_title_attribute('echo=0') . '">' . get_the_title() . '</a>';
					else:
						the_title();
					endif;
					echo '</span>';
					if ($display_mode != 1) gracias_text_field('', 'wpcf-bm-position', ', <span class="bm-position">', '</span>', false);
					echo "</li>\n";
					
					$posts_output++;
					if ($posts_output >= $posts_per_col) {
						// end current column
						echo "</ul>\n";
						echo "</div><!-- .gracias-column.column -->\n";
						$closed_column = true;
						// reset for next column (if any)
						$start_column = true;
						$posts_output = 0;
					}
				}
				if ($closed_column == false) {
					// end last column
					echo "</ul>\n";
					echo "</div><!-- .gracias-column.column -->\n";
					$closed_column = true;
				} ?>
				<div class="clear"></div>
				<?php pinboard_posts_nav($items); ?>
			<?php else : ?>
				<p>No board members found.</p>
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