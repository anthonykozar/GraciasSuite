<?php get_header(); ?>
	<div id="container">
		<section id="content" <?php pinboard_content_class(); ?>>
			<?php if( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
					<div class="entry">
						<header class="entry-header">
							<<?php pinboard_title_tag( 'post' ); ?> class="entry-title"><?php the_title(); ?></<?php pinboard_title_tag( 'post' ); ?>>
							<?php gracias_subtitle(); ?>
							<?php pinboard_entry_meta(); ?>
						</header><!-- .entry-header -->
						<div class="entry-content">
							<?php if( has_post_format( 'audio' ) ) : ?>
								<p><?php pinboard_post_audio(); ?></p>
							<?php elseif( has_post_format( 'video' ) ) : ?>
								<p><?php pinboard_post_video(); ?></p>
							<?php endif; ?>
							<?php gracias_featured_image(); ?>
							<?php the_content(); ?>
							<div class="clear"></div>
						</div><!-- .entry-content -->
						<footer class="entry-utility">
							<?php wp_link_pages( array( 'before' => '<p class="post-pagination">' . __( 'Pages:', 'pinboard' ), 'after' => '</p>' ) ); ?>
							<?php the_tags( '<div class="entry-tags">', ' ', '</div>' ); ?>
							<?php pinboard_social_bookmarks(); ?>
							<?php pinboard_post_author(); ?>
							<?php gracias_related_content(); ?>
						</footer><!-- .entry-utility -->
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