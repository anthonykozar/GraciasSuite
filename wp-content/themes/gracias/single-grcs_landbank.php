<?php get_header(); ?>
	<div id="container">
		<section id="content" <?php pinboard_content_class(); ?>>
			<?php if( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
					<div class="entry">
						<header class="entry-header">
							<<?php pinboard_title_tag( 'post' ); ?> class="entry-title staff-name"><?php the_title(); ?></<?php pinboard_title_tag( 'post' ); ?>>
							<p class="staff-edit-link"><?php edit_post_link( __( 'Edit', 'pinboard' ), '<span class="edit-link">', '</span>' ); ?></p>
							<div class="clear clear-left"></div>
							<h2 class="subtitle">Land Bank Information</h2>
						</header><!-- .entry-header -->
						<div class="entry-content">
							<?php gracias_featured_image(); ?>
							<p class="lb-general">
								<?php gracias_date_field('Incorporation date:', 'wpcf-lb-incorporation-date', '', "<br />\n"); ?>
								<?php gracias_text_field('DTAC funding:', 'wpcf-lb-dtac-funding', '', "%<br />\n"); ?>
								<?php gracias_date_field('', 'wpcf-lb-last-updated', '(as of ', ")<br />\n"); ?>
							</p>
							
							<?php gracias_text_field('Notes:', 'wpcf-lb-notes', '<p class="lb-notes">', "</p>\n", true); ?>
							
							<h2>Contact</h2>
							<p class="lb-contact">
								<?php gracias_text_field('', 'wpcf-lb-contact-name', '<span class="lb-contact-name">', '</span>');
									  gracias_text_field('', 'wpcf-lb-contact-title', ', <span class="lb-contact-title">', '</span>'); ?><br />
								<?php gracias_text_field('', 'wpcf-lb-address', '<span class="lb-contact-address">', "</span><br />\n", true); ?>
								<?php gracias_text_field('Phone:', 'wpcf-lb-phone', '', "</span><br />\n", false, '<span class="lb-contact-phone">'); ?>
								<?php gracias_email_field('Email:', 'wpcf-lb-email', '', "</span><br />\n", '<span class="lb-contact-email">'); ?>
								<?php gracias_url_field('Website:', 'wpcf-lb-website', '', "</span><br />\n", '<span class="lb-contact-website">'); ?>
							</p>

							<div class="clear"></div>
							<h2>Board Members</h2>
								<?php gracias_lbbm_paragraphs() ?>
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