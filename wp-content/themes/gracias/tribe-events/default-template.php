<?php
/**
 * Default Events Template
 * This file is the basic wrapper template for all the views if 'Default Events Template'
 * is selected in Events -> Settings -> Template -> Events Template.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/default-template.php
 *
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

get_header(); ?>
	<div id="container">
		<section id="content" <?php pinboard_content_class(); ?>>
			<div id="tribe-events-pg-template">
				<?php tribe_events_before_html(); ?>
				<?php tribe_get_view(); ?>
				<?php tribe_events_after_html(); ?>
			</div> <!-- #tribe-events-pg-template -->
			<footer class="entry-utility">
				<?php // is there a better way to test for a single event (or venue/organizer) page ?
					$is_single = !(tribe_is_day() || tribe_is_month() || tribe_is_list_view() || tribe_is_event_category());
					if ($is_single) the_tags( '<div class="entry-tags">', ' ', '</div>' );
					pinboard_social_bookmarks();
					if ($is_single) gracias_related_content();
				?>
			</footer><!-- .entry-utility -->
		</section><!-- #content -->
		<?php if( ( 'no-sidebars' != pinboard_get_option( 'layout' ) ) && ( 'full-width' != pinboard_get_option( 'layout' ) ) ) : ?>
			<?php get_sidebar(); ?>
		<?php endif; ?>
		<div class="clear"></div>
	</div><!-- #container -->
<?php get_footer(); ?>