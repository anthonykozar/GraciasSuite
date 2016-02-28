<?php get_header(); ?>
	<?php
		if (!is_home() && pinboard_get_option('location')) {
			pinboard_current_location();
		}
	?>
	<div id="container">
		<section id="content" <?php pinboard_content_class(); ?>>
			<?php if ( have_posts() ) : ?>
				<div class="entries">
					<?php while( have_posts() ) : the_post(); ?>
						<?php
							// choose an appropriate content template based on the post type & format
							$ptype = get_post_type();
							if ($ptype == 'post')  get_template_part('content', get_post_format());
							else get_template_part('content', $ptype);
						?>
					<?php endwhile; ?>
				</div><!-- .entries -->
				<?php pinboard_posts_nav(); ?>
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