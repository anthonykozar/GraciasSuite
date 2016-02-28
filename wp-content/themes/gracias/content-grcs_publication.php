<?php
/*  Custom content template that displays one publication on
	an archive or search results page.

	Part of:  Gracias WordPress theme by Anthony Kozar
	License:  GNU General Public License v2 or later
	Created:  May 26, 2015

	Customized from the "content.php" template of the original
	Pinboard theme by One Designs, Inc.
	
*/
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<div class="entry publication">
		<?php
			// link to the publication document (URL in custom field), not the publication post
			$link_url = get_post_meta(get_the_ID(), 'wpcf-publication-url', true);
			// this code block customized from function pinboard_post_thumbnail()
			if(has_post_thumbnail()) {
				echo '<figure class="entry-thumbnail">';
				// only wrap the image in an <a> tag if we have a URL
				if ($link_url) echo '<a href="' . $link_url . '" rel="bookmark" title="' . the_title_attribute('echo=0') . '">';
				the_post_thumbnail((pinboard_is_teaser() ? 'teaser-thumb' : 'blog-thumb'));
				if ($link_url) echo '</a>';
				echo '</figure>';
			}
		?>
		<div class="entry-container">
			<header class="entry-header">
				<?php gracias_post_type_label(); ?>
				<<?php pinboard_title_tag('post'); ?> class="entry-title">
				<?php
					// only wrap the title in an <a> tag if we have a URL
					if ($link_url) echo '<a href="' . $link_url . '" rel="bookmark" title="' . the_title_attribute('echo=0') . '">';
					the_title();
					if ($link_url) echo '</a>';
				?>
				</<?php pinboard_title_tag('post'); ?>>
				<p class="entry-date-no-icon"><?php the_time( get_option( 'date_format' ) ); ?></p>
			</header><!-- .entry-header -->
			<div class="entry-summary">
				<?php 
					if ($link_url) {
						// link to the publication again with "user-friendly" text
						$res = gracias_is_url_web_page_or_doc($link_url);
						if ($res['is_web_page'])  $link_text = 'Read publication';
						else $link_text = 'Download ' . strtoupper($res['extension']) . ' document';
						echo '<p><a href="' . $link_url . '" rel="bookmark" title="' . the_title_attribute('echo=0') . '">';
						echo $link_text . '</a></p>';
					}
					else echo '<p>(missing publication link)</p>';
				?>
			</div><!-- .entry-summary -->
			<div class="clear"></div>
		</div><!-- .entry-container -->
	</div><!-- .entry -->
</article><!-- .post -->
