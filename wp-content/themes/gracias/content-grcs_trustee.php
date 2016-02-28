<?php
/*  Custom content template that displays one trustee on
	an archive or search results page.

	Part of:  Gracias WordPress theme by Anthony Kozar
	License:  GNU General Public License v2 or later
	Created:  June 4, 2015

	Customized from the "content.php" template of the original
	Pinboard theme by One Designs, Inc.
	
*/
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<div class="entry trustee">
		<?php pinboard_post_thumbnail(); ?>
		<div class="entry-container">
			<header class="entry-header">
				<?php gracias_post_type_label(); ?>
				<<?php pinboard_title_tag( 'post' ); ?> class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></<?php pinboard_title_tag( 'post' ); ?>>
			</header><!-- .entry-header -->
			<div class="entry-summary">
				<?php gracias_staff_position('p'); ?>
			</div><!-- .entry-summary -->
			<div class="clear"></div>
		</div><!-- .entry-container -->
	</div><!-- .entry -->
</article><!-- .post -->
