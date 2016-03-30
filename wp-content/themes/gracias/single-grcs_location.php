<?php get_header(); ?>
	<div id="container">
		<section id="content" <?php pinboard_content_class(); ?>>
			<?php if( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
					<div class="entry">
						<?php gracias_featured_image(); ?>
						<header class="entry-header">
							<<?php pinboard_title_tag( 'post' ); ?> class="entry-title location-name"><?php the_title(); ?></<?php pinboard_title_tag( 'post' ); ?>>
							<p class="staff-edit-link"><?php edit_post_link( __( 'Edit', 'pinboard' ), '<span class="edit-link">', '</span>' ); ?></p>
							<div class="clear clear-left"></div>
						</header><!-- .entry-header -->
						<div class="entry-content">
							<p>
								<?php gracias_text_field('', 'wpcf-location-address', '', "<br />\n", true); ?>
								<?php gracias_text_field('Phone:', 'wpcf-location-phone', '', "<br />\n"); ?>
								<?php gracias_text_field('Fax:', 'wpcf-location-fax', '', "<br />\n"); ?>
							</p>

							<?php gracias_text_field('', 'wpcf-location-hours', '<h2>Hours</h2><p>', '</p>', true); ?>

							<?php the_content(); ?>
							<div class="clear"></div>
						</div><!-- .entry-content -->
						<footer class="entry-utility">
							<?php wp_link_pages( array( 'before' => '<p class="post-pagination">' . __( 'Pages:', 'pinboard' ), 'after' => '</p>' ) ); ?>
							<?php the_tags( '<div class="entry-tags">', ' ', '</div>' ); ?>
							<?php pinboard_social_bookmarks(); ?>
							<?php gracias_related_content(); ?>
						</footer><!-- .entry-utility -->
					</div><!-- .entry -->
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