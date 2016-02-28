<?php gracias_set_masonry_selectors(null, '.publication-list', '#content .publication-list', '.publication-list', $useMasonry = false
									/* , $itemSelector = '.pub-entry, #infscr-loading' */); ?>
<?php get_header(); ?>
	<?php
		if (!is_home() && pinboard_get_option('location')) {
			pinboard_current_location();
		}
	?>
	<div id="container">
		<section id="content" <?php pinboard_content_class(); ?>>
			<?php if ( have_posts() ) : ?>
				<?php // FIXME: id "term-id" and classes "grcs_pub_type-term term-id" should be dynamically customized ?>
				<article id="term-id" class="archive post-type-archive post-type-archive-grcs_publication hentry grcs_pub_type-term term-id column onecol">
					<div class="entry">
						<div class="entry-content">
							<!-- just make a list of links to the publications -->
							<ul class="publication-list">
								<?php 
									while( have_posts() ) {
										the_post();
										// link to the publication document (URL in custom field), not the publication post
										$link_url = get_post_meta(get_the_ID(), 'wpcf-publication-url', true);
										echo '<li class="pub-entry">';
										echo '<span class="pub-title">';
										// only wrap the title in an <a> tag if we have a URL
										if ($link_url) echo '<a href="' . $link_url . '" rel="bookmark" title="' . the_title_attribute('echo=0') . '">';
										the_title();
										if ($link_url) echo '</a>';
										echo '</span>';
										
										// publication metadata
										echo '<span class="pub-date">' . get_the_time('M j, Y') . '</span>';
										if ($link_url) {
											// show the publication type
											$res = gracias_is_url_web_page_or_doc($link_url);
											if ($res['is_web_page'])  $pub_type = 'Link';
											else $pub_type = strtoupper($res['extension']);
											echo '<span class="pub-type">Type: ' . $pub_type . '</span>';
										}
										else echo '<span class="pub-type">(missing publication link)</span>';
										echo "</li>\n";
									}
								?>
							</ul>
							<div class="clear"></div>
						</div><!-- .entry-content -->
					</div><!-- .entry -->
				</article><!-- .post -->

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