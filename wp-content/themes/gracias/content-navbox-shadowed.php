<?php
/*  Custom content template that displays one "landing box" on a 
	"landing page" for visitor navigation.

	This variation of content-navbox.php template displays landing
	boxes with colored shadows instead of colored text boxes.
	
	TEST FILE: Rather than duplicating nearly all of the code from 
	content-navbox.php, it would be better to refactor and
	parameterize this code somehow.
	
	Part of:  Gracias WordPress theme by Anthony Kozar
	License:  GNU General Public License v2 or later
	Created:  March 28, 2016

	Customized from the "content.php" template of the original
	Pinboard theme by One Designs, Inc.
	
*/
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<?php
		// get the box's accent color
		$color = get_post_meta(get_the_ID(), 'wpcf-nav-box-color', true);
	?>
	<div class="entry nav-box" <?php if ($color) echo "style=\"box-shadow: 10px 10px 5px $color;\"" ?>>
		<?php
			// link to our custom field, not the landing box itself
			$link_url = get_post_meta(get_the_ID(), 'wpcf-link-url', true);
			// but only wrap the box in an <a> tag if we have a URL
			if ($link_url) echo '<a href="' . $link_url . '" rel="bookmark" title="' . the_title_attribute('echo=0') . '">';
			// this code block customized from function pinboard_post_thumbnail()
			if(has_post_thumbnail()) {
				echo '<figure class="entry-thumbnail">';
				the_post_thumbnail('landing-box');	// use size defined in function.php
				echo '</figure>';
			}
		?>
		<div class="entry-container">
			<header class="entry-header">
				<<?php pinboard_title_tag('post'); ?> class="entry-title">
				<?php the_title(); ?>
				</<?php pinboard_title_tag('post'); ?>>
			</header><!-- .entry-header -->
			<div class="entry-summary">
				<?php the_content(); ?>
			</div><!-- .entry-summary -->
			<div class="clear"></div>
		</div><!-- .entry-container -->
		<?php if ($link_url) echo '</a>'; ?>
	</div><!-- .entry -->
</article><!-- .post -->
