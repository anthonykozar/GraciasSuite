<?php
/*  Template Name: Staff/Team Home

	Custom template that combines the content of a Page with a
	multi-column, archive-style display of all staff members or just the
	staff of a particular team.

	Part of:  Gracias WordPress theme by Anthony Kozar
	License:  GNU General Public License v2 or later
	Created:  March 22, 2016

	Customized from the "page.php" and "index.php" templates of the original
	Pinboard theme by One Designs, Inc.
	
*/
?>
<?php gracias_set_masonry_selectors('.threecol'); ?>
<?php get_header(); ?>
	<?php if( is_front_page() ) : ?>
		<?php if( pinboard_get_option( 'slider' ) ) : ?>
			<?php get_template_part( 'slider' ); ?>
		<?php endif; ?>
		<?php get_sidebar( 'wide' ); ?>
		<?php get_sidebar( 'boxes' ); ?>
	<?php endif; ?>
	<div id="container">
		<div id="content-column" <?php pinboard_content_class(); ?>>
		<!-- Intro text and titles from the Page -->
		<section id="intro" class="column onecol">
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
						</div><!-- .entry-content -->
						<?php wp_link_pages( array( 'before' => '<footer class="entry-utility"><p class="post-pagination">' . __( 'Pages:', 'pinboard' ), 'after' => '</p></footer><!-- .entry-utility -->' ) ); ?>
					</div><!-- .entry -->
					<?php comments_template(); ?>
				</article><!-- .post -->
			<?php else : ?>
				<?php pinboard_404(); ?>
			<?php endif; ?>
		</section><!-- #intro -->

		<!-- Display staff members per the assigned team(s) -->
		<section id="content" class="column onecol">
			<?php 
				// Code from <http://codex.wordpress.org/Function_Reference/next_posts_link#Usage_when_querying_the_loop_with_WP_Query>
				// set the "paged" parameter (use 'page' if the query is on a static front page)
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

				$team_slug = get_post_meta(get_the_ID(), 'wpcf-team', true);
				$tax_parms = array(
								array(
									'taxonomy' => 'grcs_team',
									'field'    => 'slug',
									'terms'    => $team_slug
								)
							);
				$posts_per_page = get_post_meta(get_the_ID(), 'wpcf-posts-per-page', true);
				$boxes = new WP_Query(array('post_type' => 'grcs_staff', 'tax_query' => $tax_parms, 'orderby' => 'menu_order', 
											'order' => 'ASC', 'paged' => $paged, 'posts_per_page' => $posts_per_page));
			?>
			<?php if( $boxes->have_posts() ) : ?>
				<?php gracias_set_number_columns(3); ?>
				<div class="entries">
					<?php while( $boxes->have_posts() ) : $boxes->the_post(); ?>
						<?php get_template_part('content', 'grcs_staff'); ?>
					<?php endwhile; ?>
				</div><!-- .entries -->
				<?php pinboard_posts_nav($boxes); ?>
			<?php else : ?>
				<?php pinboard_404(); ?>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</section><!-- #content -->
		</div><!-- #content-column -->

		<?php if( ( 'no-sidebars' != pinboard_get_option( 'layout' ) ) && ( 'full-width' != pinboard_get_option( 'layout' ) ) ) : ?>
			<?php get_sidebar(); ?>
		<?php endif; ?>
		<div class="clear"></div>
	</div><!-- #container -->
<?php get_footer(); ?>