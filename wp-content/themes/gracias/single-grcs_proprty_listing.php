<?php get_header(); ?>
	<div id="container">
		<section id="content" <?php pinboard_content_class(); ?>>
			<?php if( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
					<div class="entry">
						<header class="entry-header">
							<<?php pinboard_title_tag( 'post' ); ?> class="entry-title"><?php the_title(); ?></<?php pinboard_title_tag( 'post' ); ?>>
							<?php pinboard_entry_meta(); ?>
						</header><!-- .entry-header -->
						<div class="entry-content">
							<?php gracias_featured_image(); ?>

							<h2>Property Details</h2>
							<p><?php gracias_price_field('Listing price:', 'wpcf-listing-price'); ?></p>
							<p><b>Location:</b><br />
								<?php gracias_text_field('', 'wpcf-street-address', '', "<br />\n"); ?>
								<?php gracias_text_field('', 'wpcf-city'); ?><?php gracias_text_field('', 'wpcf-state', ', '); ?><?php gracias_text_field('', 'wpcf-zip-code', ' '); ?><br />
							</p>
							<p>
								<?php gracias_text_field('County:', 'wpcf-county', '', "<br />\n"); ?>
								<?php gracias_text_field('Parcel:', 'wpcf-parcel-number', '', "<br />\n"); ?>
							</p>
							
							<?php the_content(); ?>

							<h2>Contact</h2>
							<p>
								<?php gracias_text_field('', 'wpcf-contact-name', '', "<br />\n"); ?>
								<?php gracias_text_field('', 'wpcf-contact-company', '', "<br />\n"); ?>
								<?php gracias_text_field('', 'wpcf-contact-address', '', "<br />\n", true); ?>
								<?php gracias_text_field('Phone:', 'wpcf-contact-phone', '', "<br />\n"); ?>
								<?php gracias_email_field('Email:', 'wpcf-contact-email', '', "<br />\n"); ?>
								<?php gracias_url_field('Website:', 'wpcf-contact-website', '', "<br />\n"); ?>
							</p>
							
							<h2>Further Information</h2>
							<p>
								<?php gracias_url_field('Realtor Listing:', 'wpcf-listing-url', '', "<br />\n"); ?>
								<?php gracias_multiple_url_field('', 'wpcf-attachment', '', "<br />\n", ''); ?>
							</p>
							
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