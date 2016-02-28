<?php // $sticky = get_option( 'sticky_posts' ); ?>
<?php // if( ! empty( $sticky ) ) : ?>
	<?php $slider = new WP_Query( array( 'post_type' => 'grcs_slide', 'posts_per_page' => 99 ) ); ?>
	<?php if( $slider->have_posts() ) : ?>
		<section id="slider">
			<ul class="slides">
				<?php while( $slider->have_posts() ) : $slider->the_post(); ?>
					<li>
						<article class="post hentry">
							<?php if( has_post_format( 'video' ) ) : ?>
								<?php pinboard_post_video(); ?>
							<?php else : ?>
								<?php $link_url = get_post_meta(get_the_ID(), 'wpcf-link-url', true); ?>
								<?php if( has_post_thumbnail() ) : ?>
									<?php if ($link_url): ?>
										<a href="<?php echo $link_url; ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
									<?php endif; ?>
									<?php the_post_thumbnail( 'slider-thumb' ); ?>
									<?php if ($link_url):
											echo '</a>';
										endif;
									?>
								<?php endif; ?>
								<h2 class="entry-title">
								<?php if ($link_url): ?>
									<a href="<?php echo $link_url; ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a>
								<?php
									else: 
										the_title();
									endif;
								?>
								</h2><!-- .entry-title -->
							<?php endif; ?>
							<div class="clear"></div>
						</article><!-- .post -->
					</li>
				<?php endwhile; ?>
			</ul>
			<div class="clear"></div>
		</section><!-- #slider -->
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>
<?php // endif; ?>