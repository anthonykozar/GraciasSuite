<?php
/*  Custom content template that displays one link from the
	Simple Links plugin on an archive or search results page.

	Part of:  Gracias WordPress theme by Anthony Kozar
	License:  GNU General Public License v2 or later
	Created:  June 6, 2015

	Customized from the "content.php" template of the original
	Pinboard theme by One Designs, Inc.
	
*/
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<div class="entry link">
		<?php
			// link directly to the link's URL (not the post)
			$link_url = get_post_meta(get_the_ID(), 'web_address', true);
			// this code block customized from function pinboard_post_thumbnail()
			if (has_post_thumbnail()) {
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
				<?php if (!$link_url) echo '<p>(missing link)</p>'; ?>
			</header><!-- .entry-header -->
			<div class="entry-summary">
				<?php
					// display the link description if there is one
					$link_desc = get_post_meta(get_the_ID(), 'description', true);
					if ($link_desc) {
						$link_desc = apply_filters('the_content', $link_desc);
						$link_desc = str_replace(']]>', ']]&gt;', $link_desc);
						echo $link_desc;
					}
				?>
			</div><!-- .entry-summary -->
			<div class="clear"></div>
		</div><!-- .entry-container -->
	</div><!-- .entry -->
</article><!-- .post -->
