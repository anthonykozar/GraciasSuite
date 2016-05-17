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

						Add 8 to the display mode to show headings for child boards (such as committees).
						This simplistic solution outputs a heading whenever the first board assignment
						changes between two consecutive board members.  This will probably be less than 
						ideal if the board members are not sorted by child boards or have multiple board
						assignments.
				 */
				$display_mode = (int) get_post_meta(get_the_ID(), 'wpcf-display-mode', true);
				// if ($display_mode == 0)	$display_mode = 7;	// show all by default
				if ($display_mode >= 8) {
					$display_mode -= 8;
					$show_headings = true;
				}
				else $show_headings = false;
				if ($display_mode >= 4) {
					$display_mode -= 4;
					$link_names = true;
				}
				else $link_names = false;
				
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
				<?php
					if ($show_headings) {
						// get the first board term for the first board member
						$last_board = '';
						$items->the_post();
						$terms = get_the_terms(get_the_ID(), 'grcs_board');
						if ($terms && !is_wp_error($terms)) {
							$last_board = $terms[0]->slug;
							if ($last_board != $board_slug) {
								echo '<h2 class="subboard">' . $terms[0]->name . "</h2>\n";
							}
						}
						$items->rewind_posts();
					}
				?>
				<ul class="board-member-list">
				<?php while( $items->have_posts() ) : $items->the_post(); ?>
					<?php
						if ($show_headings) {
							// get the first board term for this board member
							$terms = get_the_terms(get_the_ID(), 'grcs_board');
							if ($terms && !is_wp_error($terms)) {
								$board = $terms[0]->slug;
								if ($board != $last_board) {
									// output a heading and begin a new list if the board has changed
									echo "</ul>\n";
									if ($board != $board_slug) {
										echo '<h2 class="subboard">' . $terms[0]->name . "</h2>\n";
									}
									else echo '<h2 class="subboard">Board Members</h2>' . "\n";
									echo '<ul class="board-member-list">' . "\n";
									$last_board = $board;
								}
							}
						}
					?>
					<li class="bm-entry">
						<span class="bm-title">
							<?php if ($link_names) : ?>
								<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
							<?php else :
								the_title();
								endif;
							?>
							<?php if ($display_mode == 0) gracias_text_field('', 'wpcf-bm-position', '<br />', '', false); ?>
						</span>
						<?php
							if ($display_mode < 2) {
								gracias_text_field('', 'wpcf-bm-company-title', '<span class="bm-col-2">', '</span>', false, '', true);
								gracias_text_field('', 'wpcf-bm-company', '<span class="bm-col-3">', '</span>', false, '', true);
							}
							else if ($display_mode == 2) {
								gracias_text_field('', 'wpcf-bm-position', '<span class="bm-col-2">', '</span>', false, '', true);
								gracias_dbl_text_field('', 'wpcf-bm-company-title', 'wpcf-bm-company', '<span class="bm-col-3">', ', ', '</span>', false, '', true);
							}
							else if ($display_mode == 3) {
								gracias_text_field('', 'wpcf-bm-position', '<span class="bm-col-2">', '</span>', false, '', true);
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