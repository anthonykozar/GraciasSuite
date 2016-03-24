<?php

// Load extra stylesheets including the original Pinboard styles
function gracias_enqueue_styles() {
	wp_enqueue_style( 'original-pinboard', get_template_directory_uri() . '/styles/pinboard.css' );
}

add_action( 'wp_enqueue_scripts', 'gracias_enqueue_styles' );


/* NEW FEATURES */

/**
 * Registers child theme widget areas for the site
 *
 * Based on pinboard_widgets_init() from the Pinboard theme.
 * @since Pinboard 1.0
 */
function  gracias_widgets_init() {
	$title_tag = pinboard_get_option( 'widget_title_tag' );
	
	register_sidebar(
		array(
			'id'   => 'gracias-playbook',
			'name' => 'Playbook Sidebar',
			'description' => 'Replaces the main sidebar on pages that use the Land Bank Playbook template.',
			'before_widget' => '<div class="column onecol"><aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside><!-- .widget --></div>',
			'before_title' => '<' . $title_tag . ' class="widget-title">',
			'after_title' => '</' . $title_tag . '>'
		)
	);
}

// Priority 11 ensures that our sidebar(s) are added after those of the Pinboard theme, 
// preventing the Pinboard sidebars from being renumbered (they lack string IDs).
add_action('widgets_init', 'gracias_widgets_init', 11);


// use built-in Tags with additional post types

function gracias_add_tags_to_post_types() {
    register_taxonomy_for_object_type('post_tag', 'page', 11);
    register_taxonomy_for_object_type('post_tag', 'simple_link', 11);
    register_taxonomy_for_object_type('post_tag', 'job_listing', 11);
}

add_action('init', 'gracias_add_tags_to_post_types');


// add custom post types & WP Pages to tag archives and search results

function gracias_add_custom_types_to_queries($query)
{
	if (!is_admin() && $query->is_main_query()) {
		// $curtypes = $query->get('post_type');
		if ($query->is_tag || $query->is_search) {
			$post_types = array('post', 'page', 'tribe_events', 'simple_link', 'job_listing',
								'grcs_landbank', 'grcs_proprty_listing', 'grcs_publication', 
								'grcs_staff', 'grcs_board_member', 'glossary');
			// override the original list of post types
			$query->set('post_type', $post_types);
		}
	}
	
	// Codex says that $query is passed by reference and no return value is needed
	// return $query;
}

add_action('pre_get_posts', 'gracias_add_custom_types_to_queries');


// prefix to identify "home" URLs in text
define('HOME_PREFIX', 'home-url=');

// Change URL "homes" for office locations and some categories.
function gracias_filter_category_links($url, $term, $taxonomy)
{
	switch ($taxonomy) {
		case 'grcs_locations':
		case 'category':
		case 'grcs_property_type':
			$descr = trim(strip_tags($term->description));	// remove <p></p> on admin screens
			if (!empty($descr)) {
				// if the term's description begins with HOME_PREFIX, we expect the
				// rest of the description to be the URL to return
				if (substr($descr, 0, strlen(HOME_PREFIX)) == HOME_PREFIX) {
					$newurl = substr($descr, strlen(HOME_PREFIX));
					return $newurl;
				}
			}
			break;
			
		default:
			// don't replace URLs for other taxonomies
			break;
	}
	
	return $url;
}

add_filter('term_link', 'gracias_filter_category_links', 10, 3);


/* CUSTOM SHORTCODES */

// [gracias_clear]{contents}{[/gracias_clear]}
function gracias_do_clear_shortcode($atts, $content = "", $tag)
{
	if ($content == null) $content = "";
	
	// enclose $content in a <div class="clear">
	return '<div class="clear">'. do_shortcode($content) . '</div>';
}
	
add_shortcode('gracias_clear', 'gracias_do_clear_shortcode');

// [gracias_column col="(1|2|3|4)"]{contents}[/gracias_column]
function gracias_do_column_shortcode($atts, $content = "NO CONTENT PASSED", $tag)
{
	if (!is_array($atts)) {
		// $atts is not an array if no attributes are given
		return $content;
	}
	
	// check for the 'col' attribute
	if (array_key_exists('col', $atts)) {
		// allowed values for 'col' attribute are "1","2","3","4"
		switch ($atts['col']) {
			case '1':	$classes = 'gracias-column column onecol'; break;
			case '2':	$classes = 'gracias-column column twocol'; break;
			case '3':	$classes = 'gracias-column column threecol'; break;
			case '4':	$classes = 'gracias-column column fourcol'; break;
			default:	$classes = ''; break;
		}
		if ($classes != '') {
			// enclose $content in a <div> with the specified column classes
			return "<div class=\"$classes\">". do_shortcode($content) . '</div>';
		}
	}
	
	// do nothing if no valid attributes ?
	return $content;
}

// duplicating the shortcode allows nested columns
add_shortcode('gracias_column', 'gracias_do_column_shortcode');
add_shortcode('gracias_column_2', 'gracias_do_column_shortcode');
add_shortcode('gracias_column_3', 'gracias_do_column_shortcode');


/* PLUGIN MODIFICATIONS */

function gracias_disable_plugin_features()
{
	// prevent CP Related Posts output during the_content() call
	// Note: this could fail if the plugin changes the priority of its filter!
	remove_filter('the_content', 'cprp_content', 99);
}

add_action('after_setup_theme', 'gracias_disable_plugin_features');


/* ADMIN CUSTOMIZATIONS */

// customize admin menus
function gracias_register_admin_menu_items()
{
	// remove the comments menu
	remove_menu_page('edit-comments.php');
}

add_action('admin_menu', 'gracias_register_admin_menu_items');


// Customize the built-in editor boxes for our custom post types.
// Thanks to Josh Leuze's Meteor Slides plugin for illustrating how to do this!
function gracias_slide_imagebox_callback($post, $metabox)
{
     echo '<p>The image size should be 1140x342 pixels.</p>';
     // call built-in function for WP featured image box
     post_thumbnail_meta_box($post, $metabox);
}

function gracias_customize_admin_editors()
{
	// Rename and move the featured image box for Slides post type.
	remove_meta_box('postimagediv', 'grcs_slide', 'side');
	add_meta_box('postimagediv', 'Slide Image', 'gracias_slide_imagebox_callback',
				 'grcs_slide', 'normal', 'high');
		
	// remove Types "marketing" box
	remove_meta_box('wpcf-marketing', 'grcs_staff', 'side');
	remove_meta_box('wpcf-marketing', 'grcs_board_member', 'side');
	remove_meta_box('wpcf-marketing', 'grcs_landbank', 'side');
	remove_meta_box('wpcf-marketing', 'grcs_landbank_member', 'side');
	remove_meta_box('wpcf-marketing', 'grcs_nav_boxes', 'side');
	remove_meta_box('wpcf-marketing', 'grcs_publication', 'side');
	remove_meta_box('wpcf-marketing', 'grcs_proprty_listing', 'side');
	remove_meta_box('wpcf-marketing', 'grcs_slide', 'side');
}

add_action('do_meta_boxes', 'gracias_customize_admin_editors');

function gracias_label_property_listing_editor()
{
	// Check that we are on the "Add New" or "Edit Property Listing" page:
	//     /wp-admin/post-new.php?post_type=grcs_proprty_listing
	//     /wp-admin/post.php?post=###&action=edit
	$screen = get_current_screen();
	if ($screen->base == 'post' && $screen->post_type == 'grcs_proprty_listing') {
		// Add a title and help text just before the editor box for Property Listings post type.
		echo '<h3 style="margin-top: 24px; padding: 0px;">Property Description</h3>' . "\n";
		echo '<p style="margin-bottom: 0px;"><i>Enter descriptive text for the property here, ' .
			"but use the appropriate fields below for address, contact, etc.</i></p>\n";
	}
}

add_action('edit_form_after_title', 'gracias_label_property_listing_editor');

// Set the slug for Landing Boxes to "nav-boxes-ID"
// instead of using the post name to avoid potential conflicts with Pages
function gracias_nav_boxes_set_slug($slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug)
{
	if ($post_type == 'grcs_nav_boxes') {
		return "nav-boxes-$post_ID";
	}
	else return $slug;
}

add_filter('wp_unique_post_slug', 'gracias_nav_boxes_set_slug', 10, 6);


// Customize the list view columns for our custom post types.
// Thanks to Josh Leuze's Meteor Slides plugin for illustrating how to do this!
function gracias_slide_admin_columns($cols)
{
	// specify the columns for Slides post type.
	$cols = array(
		'cb'				=>	'<input type="checkbox" />',
		'title'				=>	'Slide Title',
		'author'			=>	'Author',
		'gracias-slide-image'	=>	'Slide Image',
		'gracias-slide-link'	=>	'Slide Link',
		'date'				=>	'Date',
	);
	
	return $cols;
}

function gracias_nav_boxes_admin_columns($cols)
{
	// specify the columns for Landing Boxes post type.
	$cols = array(
		'cb'				=>	'<input type="checkbox" />',
		'title'				=>	'Title',
		'author'			=>	'Author',
		'taxonomy-grcs_nav_box_group'	=>	"Landing Box Groups",
		'gracias-link'		=>	'Link',
		'gracias-order'		=>	'Order',
		'date'				=>	'Date',
	);
	
	return $cols;
}

function gracias_staff_admin_columns($cols)
{
	// specify the columns for Staff post type.
	$cols = array(
		'cb'				=>	'<input type="checkbox" />',
		'title'				=>	'Name',
		'author'			=>	'Author',
		'tags'				=>	'Tags',
		'taxonomy-grcs_locations'	=>	"Office Locations",
		'taxonomy-grcs_team'		=>	"Teams",
		'gracias-order'		=>	'Order',
		'date'				=>	'Date',
	);
	
	return $cols;
}

function gracias_board_member_admin_columns($cols)
{
	// specify the columns for Board Members post type.
	$cols = array(
		'cb'				=>	'<input type="checkbox" />',
		'title'				=>	'Name',
		'author'			=>	'Author',
		'tags'				=>	'Tags',
		'taxonomy-grcs_board'		=>	"Boards",
		'gracias-order'		=>	'Order',
		'date'				=>	'Date',
	);
	
	return $cols;
}

function gracias_display_custom_columns($column_id, $post_id)
{
	// handle displaying columns for all of our post types.
	switch($column_id) {
		case 'gracias-slide-image':
			echo the_post_thumbnail('featured-slide-thumb');
			break;
		
		case 'gracias-slide-link':
		case 'gracias-link':
			$link_url = get_post_meta($post_id, "wpcf-link-url", true);
			if ($link_url) {
				echo '<a href="' . $link_url . '">' . $link_url . '</a>';
			}
			else echo "No link set";
			break;
		
		case 'gracias-order':
			echo get_post_field('menu_order', $post_id, 'display');
			break;
		
		default:
			break;
	}
}

add_action('manage_edit-grcs_slide_columns', 'gracias_slide_admin_columns');
add_action('manage_edit-grcs_nav_boxes_columns', 'gracias_nav_boxes_admin_columns');
add_action('manage_edit-grcs_staff_columns', 'gracias_staff_admin_columns');
add_action('manage_edit-grcs_board_member_columns', 'gracias_board_member_admin_columns');
// manage_posts_custom_column might not be the "correct" hook for custom types? (see WP code)
add_action('manage_posts_custom_column', 'gracias_display_custom_columns', 10, 2);


// Add thumbnail image size for the Slides type list view
// Thanks to Josh Leuze's Meteor Slides plugin for illustrating how to do this!
function gracias_add_thumbnail_sizes()
{
	add_image_size('featured-slide-thumb', 250, 9999);
	add_image_size('landing-box', 385, 9999);
	
	// workaround bug in CP Related Posts plugin
	add_image_size('single-post-thumbnail', 150, 150, true);	// cropped
}

// '11' ensures this runs after Pinboard calls add_theme_support('post-thumbnails') (needed?)
add_action('after_setup_theme', 'gracias_add_thumbnail_sizes', 11);


/* TEMPLATE FUNCTIONS */

// Displays the subtitle (if any) on posts and pages.
function gracias_subtitle() {
	$subtitle = get_post_meta(get_the_ID(), 'wpcf-page-subtitle', true);
	if ($subtitle) {
		echo '<h2 class="subtitle">' . $subtitle . "</h2>\n";
	}
}

// Displays a list of posts related to the current post
function gracias_related_content()
{
	echo "<div class=\"related-content\">\n";
	
	if (function_exists('cprp_content')) {
		echo cprp_content('');
	}
	
	echo "</div> <!-- .related-content -->\n";
}

// Displays the featured image on posts (and pages?).
function gracias_featured_image() {
	if (has_post_thumbnail()) {
		$attach_id = get_post_thumbnail_id();
		gracias_display_image($attach_id, 'medium', 'featured-image', true, true, true);
	}
}

// Displays any image attachment
function gracias_display_image($attachment_id, $size = 'medium', $classes = 'featured-image', $link_to_full_size_image = true, 
							$show_caption = true, $use_post_thumbnail = false)
{
	// get image URL and caption (if any)
	$link = NULL;
	if ($link_to_full_size_image) {
		$image_attr = wp_get_attachment_image_src($attachment_id, 'full');
		if ($image_attr)  $link = $image_attr[0];
	}
	if ($show_caption) {
		$caption = get_post_field('post_excerpt', $attachment_id, 'display');
		if (is_wp_error($caption))  $caption = "";
	}
	else $caption = "";
	// display the image (and caption) in a manner similar to WP's [caption]
	echo '<figure id="attachment_' . $attachment_id . '"'
		 . ' class="entry-thumbnail alignright' . ($caption ? ' wp-caption">' :  '">') . "\n";
	// link to full-size image using Pinboard's lightbox class
	if ($link) echo '<a class="colorbox" href="' . $link . '">';
	if ($use_post_thumbnail) {
		// the_post_thumbnail() calls some extra filters (and ignores $attachment_id !)
		the_post_thumbnail($size, array('class' => $classes));
	}
	else {
		echo wp_get_attachment_image($attachment_id, $size, false, array('class' => $classes));
	}
	if ($link) echo '</a>';
	echo "\n";
	if ($caption) echo '<figcaption class="wp-caption-text">' . $caption . "</figcaption>\n";
	echo "</figure>\n";
}

// Displays the featured image on staff & board member pages.
// Adapted from pinboard_post_thumbnail().
function gracias_staff_image() {
	if ( has_post_thumbnail() ) : ?>
		<figure class="entry-thumbnail">
			<?php the_post_thumbnail('medium', array('class' => 'staff-image')); ?>
		</figure>
	<?php endif;
}

// Displays a person's title/position on staff & board member pages.
function gracias_staff_position($html_tag = 'h2') {
	$position = get_post_meta(get_the_ID(), 'wpcf-position', true);
	if ($position) {
		echo '<' . $html_tag . ' class="staff-position">' . $position . '</' . $html_tag . ">\n";
	}
}

// Displays a person's email on staff pages.
function gracias_staff_email() {
	// don't display on paged archives due to a conflict between Email Encoder Bundle & AJAX code
	if (!is_paged()) {
		$email = get_post_meta(get_the_ID(), 'wpcf-email', true);
		if ($email) {
			echo '<p class="staff-email"><b>Email: </b>' . gracias_make_mailto_link($email) . "</p>\n";
		}
	}
}

// Displays a person's office locations on staff pages.
function gracias_staff_locations() {
	$terms = get_the_terms(get_the_ID(), 'grcs_locations');
	if ($terms && !is_wp_error($terms)) {
		echo '<p class="staff-locations"><b>Works in: </b>';
		the_terms(get_the_ID(), 'grcs_locations');
		echo "</p>\n";
	}
}

// Displays a person's focus area(s) on staff pages.
function gracias_staff_focus_areas() {
	$focus_areas = get_post_meta(get_the_ID(), 'wpcf-focus-area', false);
	if (count($focus_areas) > 0 && $focus_areas[0] != "") {
		echo (count($focus_areas) > 1) ? '<p><b>Focus areas: </b>' : '<p><b>Focus area: </b>';
		echo join(', ', $focus_areas) . "</p>\n";
	}
}

// Displays a person's bio on staff & board member pages.
// Roughly based on the_content().
function gracias_staff_bio() {
	$content = get_post_meta(get_the_ID(), 'wpcf-bio', true);
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	echo $content;
}

// Displays a person's social media links on staff pages.
function gracias_staff_social_links() {
	$twitter = get_post_meta(get_the_ID(), 'wpcf-twitter', true);
	$linkedin = get_post_meta(get_the_ID(), 'wpcf-linkedin', true);
	if ($linkedin != '' || $twitter != '') {
		echo '<div class="staff-social-icons">';
		if ($linkedin != '') {
			echo '<a class="social-media-icon linkedin" href="' . esc_url($linkedin) . '">LinkedIn</a>';
		}
		if ($twitter != '') {
			echo '<a class="social-media-icon twitter" href="' . esc_url($twitter) . '">Twitter</a>';
		}
		echo '</div>';
	}
}

// Displays the property type(s) on property listing pages and entries.
function gracias_property_types($html_tag = 'p', $classes = 'entry-property-type') {
	global $template;	// the complete path of the main template file selected by WP

	// don't display type for entries on property type archive or "home" pages
	if (basename($template) != 'template-property-type-home.php' && !is_tax('grcs_property_type')) {
		$terms = get_the_terms(get_the_ID(), 'grcs_property_type');
		if ($terms && !is_wp_error($terms)) {
			echo '<' . $html_tag . ' class="'. $classes . '">';
			the_terms(get_the_ID(), 'grcs_property_type');
			echo '</' . $html_tag . ">\n";
		}
	}
}

// Displays an event's date(s) on search result entries.
function gracias_tribe_event_date($event = null) {
	if (tribe_event_is_multiday()) {
		// show start date without year and end date with year (don't show time)
		$startdate = tribe_get_start_date($event, false, tribe_get_date_format(false)); // no year
		if ($startdate) {
			echo $startdate;
		}
		$enddate = tribe_get_end_date($event, false, tribe_get_date_format(true)); // with year
		if ($enddate) {
			echo ' - ' . $enddate;
		}
	}
	elseif (tribe_event_is_all_day()) {
		// show only the start date without time
		$startdate = tribe_get_start_date($event, false, tribe_get_date_format(true)); // with year
		if ($startdate) {
			echo $startdate;
		}
	}
	else {
		// show the date (and year) with start and end times
		$startdate = tribe_get_start_date($event, true, tribe_get_datetime_format(true)); // with year
		if ($startdate) {
			echo $startdate;
		}
		$enddate = tribe_get_end_date($event, true, tribe_get_time_format()); // only show time
		if ($enddate) {
			echo ' - ' . $enddate;
		}
	}
}

// Displays the text (or number) stored in the custom field $field_id
function gracias_text_field($label, $field_id, $before = '', $after = '', $multiline = false, $after_label = '') {
	$text = get_post_meta(get_the_ID(), $field_id, true);
	if (!empty($text) || is_numeric($text)) {
		// convert newlines to <br> if $multiline is true
		if ($multiline)  $text = str_replace("\n", '<br />', $text);
		if (!empty($label)) {
			echo $before . '<b>' . $label . '</b> ' . $after_label . $text . $after;
		}
		else {
			echo $before . $text . $after;
		}
	}
}

// Displays the dollar amount stored in the custom field $field_id
function gracias_price_field($label, $field_id, $before = '', $after = '') {
	$price = get_post_meta(get_the_ID(), $field_id, true);
	if (!empty($price) || is_numeric($price)) {
		if (!empty($label)) {
			echo $before . '<b>' . $label . '</b> $' . number_format($price) . $after;
		}
		else {
			echo $before . '$' . number_format($price) . $after;
		}
	}
}

// Displays the date stored in the custom field $field_id
function gracias_date_field($label, $field_id, $before = '', $after = '', $format = 'n/j/Y') {
	$timestamp = get_post_meta(get_the_ID(), $field_id, true);
	if ($timestamp) {
		// format Unix timestamp into a human-readable date
		$date = date($format, $timestamp);
		if (!empty($label)) {
			echo $before . '<b>' . $label . '</b> ' . $date . $after;
		}
		else {
			echo $before . $date . $after;
		}
	}
}

// Displays the email address stored in the custom field $field_id
function gracias_email_field($label = 'Email:', $field_id = 'wpcf-email', $before = '', $after = '', $after_label = '') {
	$email = get_post_meta(get_the_ID(), $field_id, true);
	if ($email) {
		if (!empty($label)) {
			echo $before . '<b>' . $label . '</b> ' . $after_label . gracias_make_mailto_link($email) . $after;
		}
		else {
			echo $before . gracias_make_mailto_link($email) . $after;
		}
	}
}

// Displays the URL stored in the custom field $field_id
function gracias_url_field($label = 'Website:', $field_id = 'wpcf-link-url', $before = '', $after = '', $after_label = '') {
	$link_url = get_post_meta(get_the_ID(), $field_id, true);
	if ($link_url) {
		if (!empty($label)) {
			echo $before . '<b>' . $label . '</b> ' . $after_label . '<a href="' . $link_url . '">' . $link_url . '</a>' . $after;
		}
		else {
			echo $before . '<a href="' . $link_url . '">' . $link_url . '</a>' . $after;
		}
	}
}

// Displays multiple URLs stored in the custom field $field_id
function gracias_multiple_url_field($label = 'Website:', $field_id = 'wpcf-link-url', $before = '', $between = '', $after = '') {
	$url_array = get_post_meta(get_the_ID(), $field_id, false);
	$num_links = count($url_array);
	if ($num_links > 0) {
		echo $before;
		if (!empty($label)) {
			echo '<b>' . $label . '</b> ';
		}
		for ($i = 0; $i < $num_links; $i++) {
			if ($url_array[$i]) {
				echo '<a href="' . $url_array[$i] . '">' . $url_array[$i] . '</a>';
				if ($i < ($num_links-1))  echo $between;
			}
		}
		echo $after;
	}
}

// comparison function for land bank board members
function gracias_lbbm_compare($member1, $member2) {
	if ($member1->fields['lb-member-order'] < $member2->fields['lb-member-order'])
		return -1;
	else if ($member1->fields['lb-member-order'] == $member2->fields['lb-member-order'])
		return 0;
	else return 1;
}

// Display land bank board members for the current land bank post in individual paragraphs
function gracias_lbbm_paragraphs() {
	$board_members = types_child_posts('grcs_landbank_member');
	if (!usort($board_members, 'gracias_lbbm_compare')) {
		echo '<p>Warning: usort($board_members) failed.</p>';
	}
	//var_dump($board_members);
	
	foreach ($board_members as $member) {
		$name = $member->fields['lb-member-name'];
		if (!empty($name)) {
			$title = $member->fields['lb_title'];
			$email = $member->fields['email'];
			echo '<p class="lb-board-member"><span class="lb-member-name">' . $name;
			if (!empty($title))  echo '</span>, <span class="lb-member-title">' . $title;
			echo '</span><br /><span class="lb-member-email">';
			if (!empty($email)) {
				echo gracias_make_mailto_link($email);
			}
			echo "</span></p>\n";
		}
	}
}

// Display post type for individual entries on search result and tag archive pages
function gracias_post_type_label()
{
	if (is_search() || is_tag() || is_home()) {
		// determine a label based on the post type or post category
		$post_type = get_post_type();
		if ($post_type == 'post') {
			// use the name of the first category if there is one
			$cats = get_the_category();
			if ($cats && array_key_exists(0, $cats)) {
				 $label = $cats[0]->name;
			}
			else $label = 'Article';
		}
		else if ($post_type == 'page') {
			$label = 'General Information';
		}
		else {
			// retrieve properties of the current post type
			$pto = get_post_type_object($post_type);
			$label = $pto->labels->singular_name;
		}
		
		// display the label
		echo '<p class="entry-type">' . $label . "</p>\n";
	}
}

function gracias_add_thumbnail_class($classes)
{
	// modified from pinboard_post_class()
	if(gracias_is_multipost_page() && has_post_thumbnail() && !has_post_format('gallery')
	   && !has_post_format('image') && !has_post_format('status') && !has_post_format('video'))
		$classes[] = 'has-thumbnail';
	return $classes;
}


// Remember the number of columns when set:
// 'null' means to use the default Pinboard logic/options.
$GRACIAS_NUMBER_COLUMNS = null;


/*	gracias_set_number_columns()
	
	Sets the number of content columns for all post entries that call
	post_class() after the point at which it is called.
	
	Allows the Pinboard "content columns" option to be overridden and
	also allows archive-style, multi-column layouts for multiple posts
	being displayed in a custom Page template.
	
	$numcols should be between 1 and 4 (inclusive).
	
	REQUIRES PHP 5.3.0 or later!
 */
function gracias_set_number_columns($numcols)
{
	// set a post_class() filter based on $numcols requested
	if ($numcols >= 1 && $numcols <= 4) {
		global $GRACIAS_NUMBER_COLUMNS;
		
		$GRACIAS_NUMBER_COLUMNS = $numcols;
		$colclasses = array('', 'onecol', 'twocol', 'threecol', 'fourcol');
		$colclass = $colclasses[$numcols];
		$pcfilter = function ($classes, $class, $post_id) use ($colclass) {
						$classes[] = 'column';
						$classes[] = $colclass;
						$classes = gracias_add_thumbnail_class($classes);
						return $classes;
		};
		
		add_filter('post_class', $pcfilter, 10, 3);
	
		// prevent Pinboard from setting its own column/post classes
		remove_filter('post_class', 'pinboard_post_class', 10);
	}
}

$MASONRY_SELECTORS = array('useMasonry' => true);

/*	gracias_set_masonry_selectors(

	Sets the selectors for HTML elements to be loaded by AJAX and/or
	arranged by Masonry.
	
	Setting $useMasonry to false disables Masonry, but AJAX can still 
	be used to load more posts.
	
	The default value for all other parameters is null which causes the
	original Pinboard values to be used by the javascript code.
	
	NOTE: This function must be called before get_header() because
	the values are used by pinboard_call_scripts() during the
	wp_head filter.
 */
function gracias_set_masonry_selectors($columnWidth = null, $pageContentSelector = null, $ajaxContentSelector = null, 
									$ajaxAppendToElement = null, $useMasonry = true, $itemSelector = null)
{
	global $MASONRY_SELECTORS;
	
	$MASONRY_SELECTORS['pageContent']    = $pageContentSelector;
	$MASONRY_SELECTORS['ajaxContent']    = $ajaxContentSelector;
	$MASONRY_SELECTORS['ajaxAppendTo']   = $ajaxAppendToElement;
	$MASONRY_SELECTORS['useMasonry']     = $useMasonry;
	$MASONRY_SELECTORS['itemSelector']   = $itemSelector;
	$MASONRY_SELECTORS['columnWidth']    = $columnWidth;
	
}

/*	gracias_get_masonry_selector(

	Gets a specific selector for HTML elements to be loaded by AJAX
	and/or arranged by Masonry.
	
	The original Pinboard default values are used if the selector
	has not been set.
 */
function gracias_get_masonry_selector($selector)
{
	global $MASONRY_SELECTORS;
	
	switch ($selector) {
		case 'pageContent':
			if ($MASONRY_SELECTORS['pageContent']) return $MASONRY_SELECTORS['pageContent'];
			else return '.entries';
			break;
		case 'ajaxContent':
			if ($MASONRY_SELECTORS['ajaxContent']) return $MASONRY_SELECTORS['ajaxContent'];
			else return '#content .entries';
			break;
		case 'ajaxAppendTo':
			if ($MASONRY_SELECTORS['ajaxAppendTo']) return $MASONRY_SELECTORS['ajaxAppendTo'];
			else return '.entries';
			break;
		case 'useMasonry':
			return (bool) $MASONRY_SELECTORS['useMasonry'];
			break;
		case 'itemSelector':
			if ($MASONRY_SELECTORS['itemSelector']) return $MASONRY_SELECTORS['itemSelector'];
			else return '.hentry, #infscr-loading';
			break;
		case 'columnWidth':
			if ($MASONRY_SELECTORS['columnWidth']) return $MASONRY_SELECTORS['columnWidth'];
			else return '.' . pinboard_teaser_class();
			break;
		default:
			return '';
			break;
	}
	
	return '';
}


/* UTILITY FUNCTIONS */

/*	gracias_url_extension()
	
	Parses a URL and checks for a file extension at the end of the path.
	Returns the extension if found, otherwise returns false.
 */
function gracias_url_extension($url)
{
	// check if URL has a document type extension
	$url_parts = parse_url($url);
	if ($url_parts) {
		$ext = pathinfo($url_parts['path'], PATHINFO_EXTENSION);
		if (!empty($ext))	return $ext;
		else return false;
	}
	
	return false;
}

// Common extensions for web pages and web applications
$GRACIAS_WEB_EXTS = array('html', 'htm', 'xhtml', 'php', 'jsp', 'jspx', 'asp', 'aspx', 
					   'axd', 'asx', 'asmx', 'ashx', 'jhtml', 'cgi', 'dll', 'js', 
					   'php4', 'php3', 'pl', 'py', 'rb', 'cfm', 'do', 'action', 
					   'phtml', 'rhtml', 'xml', 'rss', 'svg', 'swf', 'wss');

/*	gracias_is_url_web_page_or_doc()
	
	Tries to determine whether a URL is a web page or a document file.
	First checks for a file extension and, if found, compares the
	extension against known web page extensions.
	
	Returns an array with two keys:
		'is_web_page'	=>	true or false,
		'extension'		=>	the extension or an empty string
 */
function gracias_is_url_web_page_or_doc($url)
{
	global $GRACIAS_WEB_EXTS;
	
	// assume the URL is for a web page
	$result = array('is_web_page' => true, 'extension' => '');
	$ext = gracias_url_extension($url);
	if ($ext) {
		// always save the extension
		$result['extension'] = strtolower($ext);
		if (!in_array($result['extension'], $GRACIAS_WEB_EXTS)) {
			// assume the extension is for a non-web document
			$result['is_web_page'] = false;
		}
	}
	
	return $result;
}

/* gracias_make_mailto_link()

	Takes an email address as input and returns a link of the form
		<a href="mailto:email@example.com">email@example.com</a>
	
	The $link_text can optionally be specified.
	
	Obfuscates the email address when the Email Encoder Bundle 
	plugin is active.
 */
function gracias_make_mailto_link($email, $link_text = null)
{
	// check for Email Encoder Bundle plugin
	if (function_exists('eeb_email'))  return eeb_email($email, $link_text);
	else return '<a href="mailto:' . $email . '">' . ($link_text ? $link_text : $email) . '</a>';
	
}

// list of additional template files that we consider "multi-post" pages
$GRACIAS_MULTIPOST_TEMPLATES = array(
	'template-board-home.php',
	'template-category-home.php',
	'template-custom-type-home.php',
	'template-landing-page.php',
	'template-property-type-home.php',
	'template-staff-team-home.php',
	'page-links.php',
	
	/* these are original Pinboard templates */
	/*'template-blog.php',
	'template-blog-full-width.php',
	'template-blog-four-col.php',
	'template-blog-left-sidebar.php',
	'template-blog-no-sidebars.php',
	'template-portfolio.php',
	'template-portfolio-right-sidebar.php',
	'template-portfolio-four-col.php',
	'template-portfolio-left-sidebar.php',
	'template-portfolio-no-sidebars.php',*/
);

/*	gracias_is_multipost_page_template() tests whether the current page template
	is one of our (or Pinboard's) "multi-post" page templates.
 */
function gracias_is_multipost_page_template()
{
	global $template;	// the complete path of the main template file selected by WP
	global $GRACIAS_MULTIPOST_TEMPLATES;
	
	return in_array(basename($template), $GRACIAS_MULTIPOST_TEMPLATES);
}

/*	gracias_is_multipost_page() is the logical opposite of is_singular()
	but recognizes that some of our custom page templates should be
	considered "multi-post", not singular, pages.
 */
function gracias_is_multipost_page()
{
	if (!is_singular())	return true;
	else return gracias_is_multipost_page_template();
}


/* OVERRIDES FOR PINBOARD TEMPLATE TAGS */

/**
 * Display information about the current archive page
 *
 * @since Pinboard 1.0
 */
function pinboard_current_location() {
	global $pinboard_page_template;
	
	if (!(is_front_page() && !is_paged()) && !is_singular() || isset($pinboard_page_template)) {	// FIXME ?  Should be true only when we are on an archive or search results page ?
		if (is_home()) {
			$archive = 'Article archive';
			$title = 'All Articles';
		}
		elseif (is_search()) {
			$archive = __( 'Search results for', 'pinboard' );
			$title = get_search_query();
		}
		elseif (is_category() || is_tag() || is_tax()) {
			$taxobj = get_queried_object();
			// formatted_dump($taxobj);
			switch ($taxobj->taxonomy) {
				case 'post_tag':			$archive = 'Tag archive'; break;
				case 'category':			$archive = 'Article archive'; break;
				case 'grcs_pub_type':		$archive = 'Publication archive'; break;
				case 'grcs_property_type':	$archive = 'Property Listing archive'; break;
				case 'grcs_locations':		$archive = 'Staff archive'; break;
				case 'grcs_team':			$archive = 'Staff archive'; break;
				case 'grcs_nav_box_group':	$archive = 'What are you doing here?'; break;
				default:					$archive = 'Unknown taxonomy archive'; break;
			}
			$title = $taxobj->name;
		}
		elseif (is_year()) {
			$archive = 'Yearly archive';
			$title = get_query_var('year');
		}
		elseif (is_month()) {
			$archive = 'Monthly archive';
			$title = get_the_time('F Y');
		}
		elseif (is_day()) {
			$archive = 'Date archive';
			$title = get_the_time('F j, Y');
		}
		elseif (is_author()) {
			$archive = 'Author archive';
			$author = get_userdata(get_query_var('author'));
			$title = $author->display_name;
		}
		elseif (is_post_type_archive()) {
			$archive = 'Archive';
			$title = post_type_archive_title('', false);	// don't echo the title
		}
		elseif (isset($pinboard_page_template)) {	// ???
			$archive = '';
			$title = get_the_title();
		}
		else {
			$archive = 'Archive';
			$title = wp_title(',', false, 'right');
			// $title = should remove site name from the title (is there a better way to get the name of the current page?)
		}
		
		// add a page number if on page 2 or later
		if(is_paged()) {
			global $page, $paged;
			$title = $title . ', ' . sprintf(__( 'page %d', 'pinboard' ), get_query_var('paged'));
		} ?>
		<hgroup id="current-location">
			<h6 class="prefix-text"><?php echo $archive; ?></h6>
			<<?php pinboard_title_tag( 'location' ); ?> class="page-title"><?php echo $title ?></<?php pinboard_title_tag( 'location' ); ?>>
			<?php if(is_category() || is_tag() || is_tax()):
				$descr = term_description();
				$trimmed = trim(strip_tags($descr));	// remove <p></p>
				if (!empty($trimmed)) {
					// don't show the term's description if it begins with HOME_PREFIX
					if (substr($trimmed, 0, strlen(HOME_PREFIX)) != HOME_PREFIX) {
						echo '<div class="category-description">' . $descr . '</div>';
					}
				}
			endif; ?>
		</hgroup>
		<?php
	}
}

/**
 * Displays the tag selected in SEO options
 *
 * @param $tag string Title for which to display the tag
 * @since Pinboard 1.0
 */
function pinboard_title_tag( $tag ) {
	global $pinboard_page_template;
	if( isset( $pinboard_page_template ) )
		echo pinboard_get_option( 'archive_' . $tag . '_title_tag' );
	elseif( is_front_page() && ! is_paged() )
		echo pinboard_get_option( 'home_' . $tag . '_title_tag' );
	elseif( is_singular() )
		echo pinboard_get_option( 'single_' . $tag . '_title_tag' );
	else
		echo pinboard_get_option( 'archive_' . $tag . '_title_tag' );
}

/**
 * Modified replacement for pinboard_is_teaser().
 * Checks whether displayed post is a teaser (an entry on a multi-column layout)
 *
 * This version tests for use of our multi-post page templates.
 *
 * @since Pinboard 1.0
 */
function pinboard_is_teaser() {
	// Check first for a non-archive page templates using multiple columns
	if (gracias_is_multipost_page_template()) {
		global $GRACIAS_NUMBER_COLUMNS;
		return (isset($GRACIAS_NUMBER_COLUMNS) && $GRACIAS_NUMBER_COLUMNS > 1);
	}
	// Check for single-column layout
	if( 1 == pinboard_get_option( 'layout_columns' ) )
		return false;
	if( ! is_singular() ) {
		// Posts on archive pages can be either teasers or full posts as Pinboard has
		// an option to show $offset full posts before the teasers on some archives.
		if( is_category( pinboard_get_option( 'portfolio_cat' ) ) || ( is_category() && cat_is_ancestor_of( pinboard_get_option( 'portfolio_cat' ), get_queried_object() ) ) ) {
			if( is_paged() )
				$offset = pinboard_get_option( 'portfolio_archive_excerpts' );
			else
				$offset = pinboard_get_option( 'portfolio_excerpts' );
		} elseif( is_home() && ! is_paged() )
			$offset = pinboard_get_option( 'home_page_excerpts' );
		else
			$offset = pinboard_get_option( 'archive_excerpts' );
		global $pinboard_count;
		if( ! isset( $pinboard_count ) )
			$pinboard_count = 0;
		$count = $pinboard_count;
		if ( $pinboard_count > $offset )
			return true;
	}
	return false;
}

/**
 * Modified replacement for pinboard_entry_meta().
 * This version ignores the portfolio category feature and
 * does not output the post author.
 *
 * @since Pinboard 1.0
 */
function pinboard_entry_meta() {
	if( ! pinboard_is_teaser() ) : ?>
		<aside class="entry-meta">
			<?php if( ! is_singular() ) : ?>
				<span class="entry-date"><a href="<?php echo get_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_time( get_option( 'date_format' ) ); ?></a></span>
			<?php else : ?>
				<span class="entry-date"><?php the_time(get_option('date_format')); ?></span>
			<?php endif; ?>
			<?php if( ! is_attachment() ) :
				if (get_post_type() == 'grcs_proprty_listing'):
					gracias_property_types('span', 'entry-category');
				else: ?>
					<span class="entry-category"><?php the_category( ', ' ); ?></span>
				<?php endif; ?>
			<?php elseif( wp_attachment_is_image() ) : ?>
				<span class="attachment-size"><a href="<?php echo wp_get_attachment_url(); ?>" title="<?php _e( 'Link to full-size image', 'pinboard' ); ?>"><?php $metadata = wp_get_attachment_metadata(); echo $metadata['width']; ?> &times; <?php echo $metadata['height']; ?></a> <?php _e( 'pixels', 'pinboard' ); ?></span>
			<?php endif; ?>
			<?php edit_post_link( __( 'Edit', 'pinboard' ), '<span class="edit-link">', '</span>' ); ?>
			<?php if( ! is_singular() ) : ?>
				<span class="entry-permalink"><a href="<?php echo get_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">Permalink</a></span>
			<?php endif; ?>
			<div class="clear"></div>
		</aside><!-- .entry-meta -->
	<?php endif;
}

// This check is needed to avoid a conflict with an identically-named
// function in Pinboards's theme-options.php.
if (!is_admin()):
/**
 * Display next & previous posts links
 *
 * Customized to accept an optional WP_Query object and link labels choice.
 * $link_labels can be any of the switch strings or null to use the value
 * from Pinboard theme options.
 *
 * @since Pinboard 1.0
 */
function pinboard_posts_nav($query = null, $link_labels = null) {
	global $wp_query;
	
	// use the global WP_Query object if none was passed
	if (is_null($query))  $query = $wp_query;
	
	if ( $query->max_num_pages > 1 ) {
		// use theme option if no labels choice given
		if (is_null($link_labels))  $link_labels = pinboard_get_option('posts_nav_labels');
		switch($link_labels) {
			case 'next/prev' :
				$prev_label = __( 'Previous Page', 'pinboard' );
				$next_label = __( 'Next Page', 'pinboard' );
				break;
			case 'older/newer' :
				$prev_label = __( 'Newer Posts', 'pinboard' );
				$next_label = __( 'Older Posts', 'pinboard' );
				break;
			case 'earlier/later' :
				$prev_label = __( 'Later Posts', 'pinboard' );
				$next_label = __( 'Earlier Posts', 'pinboard' );
				break;
			case 'numbered' :
				$big = 999999999; // need an unlikely integer
				$args = array(
					'base' => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
					'format' => '?paged=%#%',
					'current' => max( 1, get_query_var('paged') ),
					'total' => $query->max_num_pages,
					'prev_text' => '&larr; <span class="text">' . __( 'Previous Page', 'pinboard' ) . '</span>',
					'next_text' => '<span class="text">' . __( 'Next Page', 'pinboard' ) . '</span> &rarr;'
				);
				break;
		}
		if ($link_labels == 'numbered'): ?>
			<div id="posts-nav" class="navigation">
				<?php if( function_exists( 'wp_pagenavi' ) ) : ?>
					<?php wp_pagenavi(); ?> 
				<?php else : ?>
					<?php echo paginate_links( $args ); ?>
				<?php endif; ?>
			</div><!-- #posts-nav -->
		<?php else : ?>
			<div id="posts-nav" class="navigation">
				<div class="nav-prev"><?php previous_posts_link( '&larr; ' . $prev_label ); ?></div>
				<?php if( is_home() && ! is_paged() ) : ?>
					<div class="nav-all"><?php next_posts_link( __( 'Read all Articles', 'pinboard' ) . ' &rarr;' ); ?></div>
				<?php else : ?>
					<div class="nav-next"><?php next_posts_link( $next_label . ' &rarr;', $query->max_num_pages ); ?></div>
				<?php endif; ?>
				<div class="clear"></div>
			</div><!-- #posts-nav -->
		<?php endif;
	}
}
endif;

define('GRACIAS_404_IMAGE_ID', 4257);

/**
 * Display notification no posts were found
 *
 * @since Pinboard 1.0
 */
function pinboard_404() { ?>
	<article class="post hentry column onecol" id="post-0">
		<div class="entry">
		<?php if (is_search()): ?>
			<header class="entry-header">
				<h2 class="entry-title">No results found</h2>
			</header><!-- .entry-header -->
			<div class="entry-content">
				<?php gracias_display_image(GRACIAS_404_IMAGE_ID, 'medium', 'featured-image', false, false, false); ?>
				<p>We didn't find any matches for &quot;<?php echo get_search_query(); ?>&quot;.</p>
				<p>Feel free to start at our <a href="/">home page</a> to find what you are looking for
				   or ask us a question through our <a href="/resources/contact/">contact form</a>.</p>
		<?php elseif (is_category() || is_tag() || is_tax()): 
			$taxobj = get_queried_object();
			$title = $taxobj->name;
		?>
			<header class="entry-header">
				<h2 class="entry-title">Oops!</h2>
			</header><!-- .entry-header -->
			<div class="entry-content">
				<?php gracias_display_image(GRACIAS_404_IMAGE_ID, 'medium', 'featured-image', false, false, false); ?>
				<p>It looks like we haven't added any content to this area yet.</p>
				<p>It might help to <a href="/?s=<?php echo urlencode($title); ?>">search for &quot;<?php echo $title; ?>&quot;</a>.
				   Or feel free to start at our <a href="/">home page</a> to find what you are looking for.</p>
				<p>Please help us improve our site by telling us through our <a href="/resources/contact/">contact form</a> 
				   which page sent you here.</p>
		<?php else: ?>
			<header class="entry-header">
				<h1 class="entry-title">We are so sorry!</h1>
				<h2 class="subtitle">The page you are looking for could not be found.</h2>
			</header><!-- .entry-header -->
			<div class="entry-content">
				<?php gracias_display_image(GRACIAS_404_IMAGE_ID, 'medium', 'featured-image', false, false, false); ?>
				<p>Please use the search box (magnifying glass) in the upper right corner of this page to search our site.
				   Or feel free to start at our <a href="/">home page</a> to find what you are looking for.</p>
				<p>Please let us know what was missing through our <a href="/resources/contact/">contact form</a>.</p>
		<?php endif; ?>
				<?php if (is_active_sidebar(7)) { 
					echo "<p>Alternatively, use the resources below to find what you're looking for:</p>";
					dynamic_sidebar( 7 );
				}
				?>
				<div class="clear"></div>
			</div><!-- .entry-content -->
		</div><!-- .entry -->
	</article><!-- .post -->
<?php
}


/* OTHER PINBOARD OVERRIDES */

/**
 * The Pinboard post_class filter:
 *   Adds column classes to posts
 *   Adds class has-thumbnail to posts that have a thumbnail set
 *
 * Modified to ignore Tribe Events Calendar requests
 * (except when they are on our own archive/search pages)
 *
 * @since Pinboard 1.0
 */
function pinboard_post_class( $classes, $class, $post_id ) {
	global $pinboard_count;
	
	// ignore Tribe Events Calendar requests
	if (function_exists('tribe_is_event_query') && tribe_is_event_query())
		return $classes;
	
	if( ! isset( $pinboard_count ) )
		$pinboard_count = 0;
	$pinboard_count++;
	$classes[] = 'column';
	if( pinboard_is_teaser() ) {
		$classes[] = pinboard_teaser_class();
	} else {
		$classes[] = 'onecol';
	}
	if( ! is_singular() && has_post_thumbnail() && ! has_post_format( 'gallery' ) && ! has_post_format( 'image' ) && ! has_post_format( 'status' ) && ! has_post_format( 'video' )  )
		$classes[] = 'has-thumbnail';
	return $classes;
}

/**
	Modified replacements for pinboard_call_scripts(), pinboard_enqueue_scripts().
	These versions use gracias_is_multipost_page() instead of Pinboard's logic to 
	decide when the current page is a "multi-post" page.  This ensures that 
	multi-post functionality like "packing" entries and AJAX loading of
	additional posts are included on our custom Page templates that act like 
	archives.
 */

/**
 * Enqueue theme scripts
 *
 * @uses wp_enqueue_scripts() To enqueue scripts
 *
 * @since Pinboard 1.0
 */
function pinboard_enqueue_scripts() {
	wp_enqueue_script( 'ios-orientationchange-fix' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'flexslider' );
	wp_enqueue_script( 'fitvids' );
	wp_enqueue_script( 'wp-mediaelement' );
	if ('infinite' == pinboard_get_option('posts_nav') && gracias_is_multipost_page() && ! is_paged())
		wp_enqueue_script( 'infinitescroll' );
	if (gracias_is_multipost_page())
		wp_enqueue_script( 'masonry' );
	if( pinboard_get_option( 'lightbox' ) )
		wp_enqueue_script( 'colorbox' );
	if ( is_singular() && get_option( 'thread_comments' ) )	// FIXME ? use ! gracias_is_multipost_page() ?
		wp_enqueue_script( 'comment-reply' );
}

/**
 * Call script functions in document head
 *
 * @since Pinboard 1.0
 */
function pinboard_call_scripts() { ?>
<script>
/* <![CDATA[ */
	jQuery(window).load(function() {
		<?php // FIXME ? template-landing-page.php below refers to the Pinboard template
		if( ( is_home() && ! is_paged() ) || ( is_front_page() && ! is_home() ) || is_page_template( 'template-landing-page.php' ) ) : ?>
			jQuery('#slider').flexslider({
				selector: '.slides > li',
				video: true,
				prevText: '&larr;',
				nextText: '&rarr;',
				pausePlay: true,
				pauseText: '||',
				playText: '>',
				before: function() {
					jQuery('#slider .entry-title').hide();
				},
				after: function() {
					jQuery('#slider .entry-title').fadeIn();
				}
			});
		<?php endif; ?>
	});
	jQuery(document).ready(function($) {
		$('#access .menu > li > a').each(function() {
			var title = $(this).attr('title');
			if(typeof title !== 'undefined' && title !== false) {
				$(this).append('<br /> <span>'+title+'</span>');
				$(this).removeAttr('title');
			}
		});
		function pinboard_move_elements(container) {
			if( container.hasClass('onecol') ) {
				var thumb = $('.entry-thumbnail', container);
				if('undefined' !== typeof thumb)
					$('.entry-container', container).before(thumb);
				var video = $('.entry-attachment', container);
				if('undefined' !== typeof video)
					$('.entry-container', container).before(video);
				var gallery = $('.post-gallery', container);
				if('undefined' !== typeof gallery)
					$('.entry-container', container).before(gallery);
				var meta = $('.entry-meta', container);
				if('undefined' !== typeof meta)
					$('.entry-container', container).after(meta);
			}
		}
		function pinboard_restore_elements(container) {
			if( container.hasClass('onecol') ) {
				var thumb = $('.entry-thumbnail', container);
				if('undefined' !== typeof thumb)
					$('.entry-header', container).after(thumb);
				var video = $('.entry-attachment', container);
				if('undefined' !== typeof video)
					$('.entry-header', container).after(video);
				var gallery = $('.post-gallery', container);
				if('undefined' !== typeof gallery)
					$('.entry-header', container).after(gallery);
				var meta = $('.entry-meta', container);
				if('undefined' !== typeof meta)
					$('.entry-header', container).append(meta);
				else
					$('.entry-header', container).html(meta.html());
			}
		}
		if( ($(window).width() > 960) || ($(document).width() > 960) ) {
			// Viewport is greater than tablet: portrait
		} else {
			$('#content .hentry').each(function() {
				pinboard_move_elements($(this));
			});
		}
		$(window).resize(function() {
			if( ($(window).width() > 960) || ($(document).width() > 960) ) {
				<?php if( is_category( pinboard_get_option( 'portfolio_cat' ) ) || ( is_category() && cat_is_ancestor_of( pinboard_get_option( 'portfolio_cat' ), get_queried_object() ) ) ) : ?>
					$('#content .hentry').each(function() {
						pinboard_restore_elements($(this));
					});
				<?php else : ?>
					$('.page-template-template-full-width-php #content .hentry, .page-template-template-blog-full-width-php #content .hentry, .page-template-template-blog-four-col-php #content .hentry').each(function() {
						pinboard_restore_elements($(this));
					});
				<?php endif; ?>
			} else {
				$('#content .hentry').each(function() {
					pinboard_move_elements($(this));
				});
			}
			if( ($(window).width() > 760) || ($(document).width() > 760) ) {
				var maxh = 0;
				$('#access .menu > li > a').each(function() {
					if(parseInt($(this).css('height'))>maxh) {
						maxh = parseInt($(this).css('height'));
					}
				});
				$('#access .menu > li > a').css('height', maxh);
			} else {
				$('#access .menu > li > a').css('height', 'auto');
			}
		});
		if( ($(window).width() > 760) || ($(document).width() > 760) ) {
			var maxh = 0;
			$('#access .menu > li > a').each(function() {
				var title = $(this).attr('title');
				if(typeof title !== 'undefined' && title !== false) {
					$(this).append('<br /> <span>'+title+'</span>');
					$(this).removeAttr('title');
				}
				if(parseInt($(this).css('height'))>maxh) {
					maxh = parseInt($(this).css('height'));
				}
			});
			$('#access .menu > li > a').css('height', maxh);
			<?php if( pinboard_get_option( 'fancy_dropdowns' ) ) : ?>
				$('#access li').mouseenter(function() {
					$(this).children('ul').css('display', 'none').stop(true, true).fadeIn(250).css('display', 'block').children('ul').css('display', 'none');
				});
				$('#access li').mouseleave(function() {
					$(this).children('ul').stop(true, true).fadeOut(250).css('display', 'block');
				});
			<?php endif; ?>
		} else {
			$('#access li').each(function() {
				if($(this).children('ul').length)
					$(this).append('<span class="drop-down-toggle"><span class="drop-down-arrow"></span></span>');
			});
			$('.drop-down-toggle').click(function() {
				$(this).parent().children('ul').slideToggle(250);
			});
		}
		<?php if (gracias_is_multipost_page()) : ?>
			// begin code for multi-post pages
			var $content = $('<?php echo gracias_get_masonry_selector('pageContent'); ?>');
			<?php if (gracias_get_masonry_selector('useMasonry')): ?>
				$content.imagesLoaded(function() {
					$content.masonry({
						itemSelector : '<?php echo gracias_get_masonry_selector('itemSelector'); ?>',
						columnWidth : container.querySelector('<?php echo gracias_get_masonry_selector('columnWidth'); ?>'),
					});
				});
			<?php endif; ?>
			<?php if (!is_paged()) : ?>
				// begin code for AJAX loading of additional posts
				<?php if( 'ajax' == pinboard_get_option( 'posts_nav' ) ) : ?>
					var nav_link = $('#posts-nav .nav-all a');
					if(!nav_link.length)
						var nav_link = $('#posts-nav .nav-next a');
					if(nav_link.length) {
						nav_link.addClass('ajax-load');
						nav_link.html('Load more posts');
						nav_link.click(function() {
							var href = $(this).attr('href');
							nav_link.html('<img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" style="float: none; vertical-align: middle;" /> Loading more posts &#8230;');
							$.get(href, function(data) {
								var helper = document.createElement('div');
								helper = $(helper);
								helper.html(data);
								var content = $('<?php echo gracias_get_masonry_selector('ajaxContent'); ?>', helper);
								var $entries = $(content.html()).css({ opacity: 0 });
								$('<?php echo gracias_get_masonry_selector('ajaxAppendTo'); ?>').append($entries);
								$content.imagesLoaded(function(){
									$entries.animate({ opacity: 1 });
									<?php if (gracias_get_masonry_selector('useMasonry')): ?>
										$content.masonry( 'appended', $entries, true );
									<?php endif; ?>
								});
								if( ($(window).width() > 960) || ($(document).width() > 960) ) {
									// Viewport is greater than tablet: portrait
								} else {
									$('#content .hentry').each(function() {
										pinboard_move_elements($(this));
									});
								}
								$('.wp-audio-shortcode, .wp-video-shortcode').css('visibility', 'visible');
								$(".entry-attachment, .entry-content").fitVids({ customSelector: "iframe[src*='wordpress.tv'], iframe[src*='www.dailymotion.com'], iframe[src*='blip.tv'], iframe[src*='www.viddler.com']"});
								<?php if( pinboard_get_option( 'lightbox' ) ) : ?>
									$('.entry-content a[href$=".jpg"],.entry-content a[href$=".jpeg"],.entry-content a[href$=".png"],.entry-content a[href$=".gif"],a.colorbox').colorbox({
										maxWidth: '100%',
										maxHeight: '100%',
									});
								<?php endif; ?>
								var nav_url = $('#posts-nav .nav-next a', helper).attr('href');
								if(typeof nav_url !== 'undefined') {
									nav_link.attr('href', nav_url);
									nav_link.html('Load more posts');
								} else {
									$('#posts-nav').html('<span class="ajax-load">There are no more posts to display.</span>');
								}
							});
							return false;
						});
					}
				<?php elseif( 'infinite' == pinboard_get_option( 'posts_nav' ) ) : ?>
					$('#content .entries').infinitescroll({
						loading: {
							finishedMsg: "<?php _e( 'There are no more posts to display.', 'pinboard' ); ?>",
							img:         ( window.devicePixelRatio > 1 ? "<?php echo get_template_directory_uri(); ?>/images/ajax-loading_2x.gif" : "<?php echo get_template_directory_uri(); ?>/images/ajax-loading.gif" ),
							msgText:     "<?php _e( 'Loading more posts &#8230;', 'pinboard' ); ?>",
							selector:    "#content",
						},
						nextSelector    : "#posts-nav .nav-all a, #posts-nav .nav-next a",
						navSelector     : "#posts-nav",
						contentSelector : "#content .entries",
						itemSelector    : "#content .entries .hentry",
					}, function(entries){
						var $entries = $( entries ).css({ opacity: 0 });
						$entries.imagesLoaded(function(){
							$entries.animate({ opacity: 1 });
							$content.masonry( 'appended', $entries, true );
						});
						if( ($(window).width() > 960) || ($(document).width() > 960) ) {
							// Viewport is greater than tablet: portrait
						} else {
							$('#content .hentry').each(function() {
								pinboard_move_elements($(this));
							});
						}
						$('.wp-audio-shortcode, .wp-video-shortcode').css('visibility', 'visible');
						$(".entry-attachment, .entry-content").fitVids({ customSelector: "iframe[src*='wordpress.tv'], iframe[src*='www.dailymotion.com'], iframe[src*='blip.tv'], iframe[src*='www.viddler.com']"});
						<?php if( pinboard_get_option( 'lightbox' ) ) : ?>
							$('.entry-content a[href$=".jpg"],.entry-content a[href$=".jpeg"],.entry-content a[href$=".png"],.entry-content a[href$=".gif"],a.colorbox').colorbox({
								maxWidth: '100%',
								maxHeight: '100%',
							});
						<?php endif; ?>
					});
				<?php endif; // 'ajax' == pinboard_get_option( 'posts_nav' ) ?>
			<?php endif; // !is_paged() ?>
		<?php endif; // gracias_is_multipost_page() ?>
		$('.entry-attachment audio, .entry-attachment video').mediaelementplayer({
			videoWidth: '100%',
			videoHeight: '100%',
			audioWidth: '100%',
			alwaysShowControls: true,
			features: ['playpause','progress','tracks','volume'],
			videoVolume: 'horizontal'
		});
		$(".entry-attachment, .entry-content").fitVids({ customSelector: "iframe[src*='wordpress.tv'], iframe[src*='www.dailymotion.com'], iframe[src*='blip.tv'], iframe[src*='www.viddler.com']"});
	});
	jQuery(window).load(function() {
		<?php if( pinboard_get_option( 'lightbox' ) ) : ?>
			jQuery('.entry-content a[href$=".jpg"],.entry-content a[href$=".jpeg"],.entry-content a[href$=".png"],.entry-content a[href$=".gif"],a.colorbox').colorbox({
				maxWidth: '100%',
				maxHeight: '100%',
			});
		<?php endif; ?>
	});
/* ]]> */
</script>
<?php
}

/**
 * Custom style declarations
 *
 * Outputs CSS declarations generated by theme options
 * and custom user defined CSS in the document <head>
 *
 * Modified to fix an output bug and replace incorrect(?) 640px with 760px 
 * in several media queries.
 *
 * @since Pinboard 1.0
 */
function pinboard_custom_styles() {
	$default_options = pinboard_default_options();
	$fonts = pinboard_available_fonts(); ?>
<style type="text/css">
	<?php if( '' == pinboard_get_option( 'facebook_link' ) && '' == pinboard_get_option( 'twitter_link' ) && '' == pinboard_get_option( 'pinterest_link' ) && '' == pinboard_get_option( 'vimeo_link' ) && '' == pinboard_get_option( 'youtube_link' ) && '' == pinboard_get_option( 'flickr_link' ) && '' == pinboard_get_option( 'googleplus_link' ) && '' == pinboard_get_option( 'dribble_link' ) && '' == pinboard_get_option( 'linkedin_link' ) ) : ?>
		#header input#s {
			width:168px;
			box-shadow:inset 1px 1px 5px 1px rgba(0, 0, 0, .1);
			text-indent: 0;
		}
	<?php endif; ?>
	<?php if( is_category( pinboard_get_option( 'portfolio_cat' ) ) || ( is_category() && cat_is_ancestor_of( pinboard_get_option( 'portfolio_cat' ), get_queried_object() ) ) ) : ?>
		.post.onecol .entry-header {
			float:left;
			width:27.6%;
		}
		.post.onecol .entry-summary {
			float:right;
			width:69.5%;
		}
		.post.onecol .wp-post-image,
		.post.onecol .entry-attachment,
		.post.onecol .post-gallery {
			float:right;
			max-width:69.5%;
		}
		.post.onecol .entry-attachment,
		.post.onecol .post-gallery {
			width:69.5%;
		}
		.twocol .entry-title,
		.threecol .entry-title,
		.fourcol .entry-title {
			margin: 0;
			text-align: center;
		}
		@media screen and (max-width: 960px) {
			.post.onecol .wp-post-image,
			.post.onecol .entry-attachment,
			.post.onecol .post-gallery {
				float:none;
				max-width:100%;
				margin:0;
			}
			.post.onecol .entry-attachment,
			.post.onecol .post-gallery {
				width:100%;
			}
			.post.onecol .entry-header,
			.post.onecol .entry-summary {
				float:none;
				width:auto;
			}
		}
	<?php endif; ?>
	<?php if( pinboard_get_option( 'hide_sidebar' ) ) : ?>
		@media screen and (max-width: 760px) {
			#sidebar {
				display: none;
			}
		}
	<?php endif; ?>
	<?php if( pinboard_get_option( 'hide_footer_area' ) ) : ?>
		@media screen and (max-width: 760px) {
			#footer-area {
				display: none;
			}
		}
	<?php endif; ?>
	<?php if( $default_options['page_background'] != pinboard_get_option( 'page_background' ) ) : ?>
		#wrapper {
			background: <?php echo esc_attr( pinboard_get_option( 'page_background' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['menu_background'] != pinboard_get_option( 'menu_background' ) ) : ?>
		#header {
			border-color: <?php echo esc_attr( pinboard_get_option( 'menu_background' ) ); ?>;
		}
		#access {
			background: <?php echo esc_attr( pinboard_get_option( 'menu_background' ) ); ?>;
		}
		@media screen and (max-width: 760px) {
			#access {
				background: none;
			}
		}
	<?php endif; ?>
	<?php if( $default_options['submenu_background'] != pinboard_get_option( 'submenu_background' ) ) : ?>
		#access li li {
			background: <?php echo esc_attr( pinboard_get_option( 'submenu_background' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['sidebar_wide_background'] != pinboard_get_option( 'sidebar_wide_background' ) ) : ?>
		#sidebar-wide,
		#sidebar-footer-wide,
		#current-location {
			background: <?php echo esc_attr( pinboard_get_option( 'sidebar_wide_background' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['content_background'] != pinboard_get_option( 'content_background' ) ) : ?>
		.entry,
		#comments,
		#respond,
		#posts-nav {
			background: <?php echo esc_attr( pinboard_get_option( 'content_background' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['post_meta_background'] != pinboard_get_option( 'post_meta_background' ) ) : ?>
		.home .entry-meta,
		.blog .entry-meta,
		.archive .entry-meta,
		.search .entry-meta {
			background: <?php echo esc_attr( pinboard_get_option( 'post_meta_background' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['footer_area_background'] != pinboard_get_option( 'footer_area_background' ) ) : ?>
		#footer-area {
			background: <?php echo esc_attr( pinboard_get_option( 'footer_area_background' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['footer_background'] != pinboard_get_option( 'footer_background' ) ) : ?>
		#copyright {
			background: <?php echo esc_attr( pinboard_get_option( 'footer_background' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['body_font'] != pinboard_get_option( 'body_font' ) ) : ?>
		body,
		#slider .entry-title,
		.page-title,
		#sidebar-wide .widget-title,
		#sidebar-boxes .widget-title,
		#sidebar-footer-wide .widget-title {
			font-family:<?php echo $fonts[pinboard_get_option( 'body_font' )]; ?>;
		}
		h1, h2, h3, h4, h5, h6,
		#site-title,
		#site-description,
		.entry-title,
		#comments-title,
		#reply-title,
		.widget-title {
			font-family:<?php echo $fonts[pinboard_get_option( 'headings_font' )]; ?>;
		}
		.entry-content {
			font-family:<?php echo $fonts[pinboard_get_option( 'content_font' )]; ?>;
		}
	<?php else : ?>
		<?php if( $default_options['headings_font'] != pinboard_get_option( 'headings_font' ) ) : ?>
			h1, h2, h3, h4, h5, h6 {
				font-family:<?php echo $fonts[pinboard_get_option( 'headings_font' )]; ?>;
			}
		<?php endif; ?>
		<?php if( $default_options['content_font'] != pinboard_get_option( 'content_font' ) ) : ?>
			.entry-content {
				font-family:<?php echo $fonts[pinboard_get_option( 'content_font' )]; ?>;
			}
		<?php endif; ?>
	<?php endif; ?>
	<?php if( $default_options['body_font_size'] != pinboard_get_option( 'body_font_size' ) ) : ?>
		body {
			font-size:<?php echo pinboard_get_option( 'body_font_size' ) . pinboard_get_option( 'body_font_size_unit' ); ?>;
			line-height:<?php echo pinboard_get_option( 'body_line_height' ) . pinboard_get_option( 'body_line_height_unit' ); ?>;
		}
	<?php elseif( $default_options['body_line_height'] != pinboard_get_option( 'body_line_height' ) ) : ?>
		body {
			line-height:<?php echo pinboard_get_option( 'body_line_height' ) . pinboard_get_option( 'body_line_height_unit' ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['h1_font_size'] != pinboard_get_option( 'h1_font_size' ) ) : ?>
		h1,
		.single .entry-title,
		.page .entry-title,
		.error404 .entry-title {
			font-size:<?php echo pinboard_get_option( 'h1_font_size' ) . pinboard_get_option( 'h1_font_size_unit' ); ?>;
			line-height:<?php echo pinboard_get_option( 'headings_line_height' ) . pinboard_get_option( 'headings_line_height_unit' ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['h2_font_size'] != pinboard_get_option( 'h2_font_size' ) ) : ?>
		h2,
		.entry-title {
			font-size:<?php echo pinboard_get_option( 'h2_font_size' ) . pinboard_get_option( 'h2_font_size_unit' ); ?>;
			line-height:<?php echo pinboard_get_option( 'headings_line_height' ) . pinboard_get_option( 'headings_line_height_unit' ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['h3_font_size'] != pinboard_get_option( 'h3_font_size' ) ) : ?>
		h3,
		.twocol .entry-title,
		.threecol .entry-title {
			font-size:<?php echo pinboard_get_option( 'h3_font_size' ) . pinboard_get_option( 'h3_font_size_unit' ); ?>;
			line-height:<?php echo pinboard_get_option( 'headings_line_height' ) . pinboard_get_option( 'headings_line_height_unit' ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['h4_font_size'] != pinboard_get_option( 'h4_font_size' ) ) : ?>
		h4,
		.fourcol .entry-title {
			font-size:<?php echo pinboard_get_option( 'h4_font_size' ) . pinboard_get_option( 'h4_font_size_unit' ); ?>;
			line-height:<?php echo pinboard_get_option( 'headings_line_height' ) . pinboard_get_option( 'headings_line_height_unit' ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['headings_line_height'] != pinboard_get_option( 'headings_line_height' ) ) : ?>
		h1, h2, h3, h4, h5, h6 {
			line-height:<?php echo pinboard_get_option( 'headings_line_height' ) . pinboard_get_option( 'headings_line_height_unit' ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['content_font_size'] != pinboard_get_option( 'content_font_size' ) ) : ?>
		.entry-content {
			font-size:<?php echo pinboard_get_option( 'content_font_size' ) . pinboard_get_option( 'content_font_size_unit' ); ?>;
			line-height:<?php echo pinboard_get_option( 'content_line_height' ) . pinboard_get_option( 'content_line_height_unit' ); ?>;
		}
		@media screen and (max-width: 760px) {
			.entry-content {
				font-size:<?php echo pinboard_get_option( 'mobile_font_size' ) . pinboard_get_option( 'content_font_size_unit' ); ?>;
				line-height:<?php echo pinboard_get_option( 'mobile_line_height' ) . pinboard_get_option( 'content_line_height_unit' ); ?>;
			}
		}
	<?php elseif( $default_options['content_line_height'] != pinboard_get_option( 'content_line_height' ) ) : ?>
		.entry-content {
			line-height:<?php echo pinboard_get_option( 'content_line_height' ) . pinboard_get_option( 'content_line_height_unit' ); ?>;
		}
		@media screen and (max-width: 760px) {
			.entry-content {
				font-size:<?php echo pinboard_get_option( 'mobile_font_size' ) . pinboard_get_option( 'mobile_font_size_unit' ); ?>;
				line-height:<?php echo pinboard_get_option( 'mobile_line_height' ) . pinboard_get_option( 'mobile_line_height_unit' ); ?>;
			}
		}
	<?php elseif( $default_options['mobile_font_size'] != pinboard_get_option( 'mobile_font_size' ) ) : ?>
		@media screen and (max-width: 760px) {
			.entry-content {
				font-size:<?php echo pinboard_get_option( 'mobile_font_size' ) . pinboard_get_option( 'mobile_font_size_unit' ); ?>;
				line-height:<?php echo pinboard_get_option( 'mobile_line_height' ) . pinboard_get_option( 'mobile_line_height_unit' ); ?>;
			}
		}
	<?php elseif( $default_options['mobile_line_height'] != pinboard_get_option( 'mobile_line_height' ) ) : ?>
		@media screen and (max-width: 760px) {
			.entry-content {
				line-height:<?php echo pinboard_get_option( 'mobile_line_height' ) . pinboard_get_option( 'mobile_line_height_unit' ); ?>;
			}
		}
	<?php endif; ?>
	<?php if( $default_options['body_color'] != pinboard_get_option( 'body_color' ) ) : ?>
		body {
			color:<?php echo esc_attr( pinboard_get_option( 'body_color' ) ); ?>;
		}
		h1, h2, h3, h4, h5, h6,
		.entry-title,
		.entry-title a {
			color:<?php echo esc_attr( pinboard_get_option( 'headings_color' ) ); ?>;
		}
		.entry-content {
			color:<?php echo pinboard_get_option( 'content_color' ); ?>;
		}
	<?php else : ?>
		<?php if( $default_options['headings_color'] != pinboard_get_option( 'headings_color' ) ) : ?>
			h1, h2, h3, h4, h5, h6,
			.entry-title,
			.entry-title a {
				color:<?php echo esc_attr( pinboard_get_option( 'headings_color' ) ); ?>;
			}
		<?php endif; ?>
		<?php if( $default_options['content_color'] != pinboard_get_option( 'content_color' ) ) : ?>
			.entry-content {
				color:<?php echo esc_attr( pinboard_get_option( 'content_color' ) ); ?>;
			}
		<?php endif; ?>
	<?php endif; ?>
	<?php if( $default_options['links_color'] != pinboard_get_option( 'links_color' ) ) : ?>
		a {
			color:<?php echo esc_attr( pinboard_get_option( 'links_color' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['links_hover_color'] != pinboard_get_option( 'links_hover_color' ) ) : ?>
		a:hover {
			color:<?php echo esc_attr( pinboard_get_option( 'links_hover_color' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['menu_color'] != pinboard_get_option( 'menu_color' ) ) : ?>
		#access a {
			color:<?php echo esc_attr( pinboard_get_option( 'menu_color' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['menu_hover_color'] != pinboard_get_option( 'menu_hover_color' ) ) : ?>
		#access a:hover,
		#access li.current_page_item > a,
		#access li.current-menu-item > a {
			color:<?php echo esc_attr( pinboard_get_option( 'menu_hover_color' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['sidebar_color'] != pinboard_get_option( 'sidebar_color' ) ) : ?>
		#sidebar,
		#sidebar-left,
		#sidebar-right {
			color:<?php echo esc_attr( pinboard_get_option( 'sidebar_color' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['sidebar_title_color'] != pinboard_get_option( 'sidebar_title_color' ) ) : ?>
		.widget-title {
			color:<?php echo esc_attr( pinboard_get_option( 'sidebar_title_color' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['sidebar_links_color'] != pinboard_get_option( 'sidebar_links_color' ) ) : ?>
		.widget-area a {
			color:<?php echo esc_attr( pinboard_get_option( 'sidebar_links_color' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['footer_color'] != pinboard_get_option( 'footer_color' ) ) : ?>
		#footer-area {
			color:<?php echo esc_attr( pinboard_get_option( 'footer_color' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['footer_title_color'] != pinboard_get_option( 'footer_title_color' ) ) : ?>
		#footer-area .widget-title {
			color:<?php echo esc_attr( pinboard_get_option( 'footer_title_color' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['copyright_color'] != pinboard_get_option( 'copyright_color' ) ) : ?>
		#copyright {
			color:<?php echo esc_attr( pinboard_get_option( 'copyright_color' ) ); ?>;
		}
	<?php endif; ?>
	<?php if( $default_options['copyright_links_color'] != pinboard_get_option( 'copyright_links_color' ) ) : ?>
		#copyright a {
			color:<?php echo esc_attr( pinboard_get_option( 'copyright_links_color' ) ); ?>;
		}
	<?php endif; ?>
	<?php echo pinboard_get_option( 'user_css' ); ?>
</style>
<?php
}

// include Pinboard theme functions
require_once 'functions-pinboard.php';

/* DEBUG FUNCTIONS */

function formatted_dump($var)
{
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
}


// show main template file selected by WP in the page <title> tag 
function gracias_temp_title()
{
	global $template;
	return $template;
}

// add_filter('wp_title', 'gracias_temp_title', 99);


function gracias_dump_rewrite_rules()
{
	global $wp_rewrite;
	
	// borrowed from WP->parse_request() in class-wp.php
	$rewrite = $wp_rewrite->wp_rewrite_rules();
	formatted_dump($rewrite);
	
	return true;
}

// run our function just before the request is parsed
// add_filter('do_parse_request', 'gracias_dump_rewrite_rules', 10);

?>