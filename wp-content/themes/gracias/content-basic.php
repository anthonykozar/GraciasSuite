<?php
/*  Custom content template that displays one CUSTOM_POST_TYPE on
	an archive or search results page.

	Part of:  Gracias WordPress theme by Anthony Kozar
	License:  GNU General Public License v2 or later
	Created:  June 3, 2015

	Customized from the "content.php" template of the original
	Pinboard theme by One Designs, Inc.
	
*/
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<div class="entry CUSTOM_POST_CLASS">
		<?php pinboard_post_thumbnail(); ?>
		<div class="entry-container">
			<header class="entry-header">
				<?php gracias_post_type_label(); ?>
				<<?php pinboard_title_tag( 'post' ); ?> class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></<?php pinboard_title_tag( 'post' ); ?>>
				<p class="entry-date-no-icon"><?php the_time( get_option( 'date_format' ) ); ?></p>
			</header><!-- .entry-header -->
			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div><!-- .entry-summary -->
			<div class="clear"></div>
		</div><!-- .entry-container -->
	</div><!-- .entry -->
</article><!-- .post -->
