<?php get_header(); ?>
	<div id="container">
		<section id="content" <?php pinboard_content_class(); ?>>
			<?php if( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
					<div class="entry">
						<?php gracias_staff_image(); ?>
						<header class="entry-header">
							<<?php pinboard_title_tag( 'post' ); ?> class="entry-title staff-name"><?php the_title(); ?></<?php pinboard_title_tag( 'post' ); ?>>
							<?php gracias_staff_social_links(); ?>
							<p class="staff-edit-link"><?php edit_post_link( __( 'Edit', 'pinboard' ), '<span class="edit-link">', '</span>' ); ?></p>
							<div class="clear clear-left"></div>
							<?php gracias_staff_position(); ?>
							<?php gracias_staff_email(); ?>
							<?php gracias_staff_locations(); ?>
							<?php gracias_staff_focus_areas(); ?>
						</header><!-- .entry-header -->
						<div class="entry-content">
							<?php gracias_staff_bio(); ?>
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