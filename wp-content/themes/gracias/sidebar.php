<?php
/*  sidebar.php

	Modified main sidebar with an additional menu above the
	other widget areas.

	Part of:  Gracias WordPress theme by Anthony Kozar
	License:  GNU General Public License v2 or later
	Created:  June 13, 2015

	Customized from the "sidebar.php" templates of the original
	Pinboard theme by One Designs, Inc.
	
*/
?>
<div id="sidebar" <?php pinboard_sidebar_class(); ?>>
	<?php
		if (is_singular()):
			// Don't display sidebar menu on archives and search result pages
			// (multi-post Pages are OK).
			
			// The value of 'wpcf-selectable-menu' should be the human-readable
			// version of the menu name as it is also displayed as the menu title.
			$menu_name = get_post_meta(get_the_ID(), 'wpcf-selectable-menu', true);
			if ($menu_name):
				// pretend to be a widget area and widget! ?>
				<div id="sidebar-selectable-menu" class="widget-area" role="complementary">
					<div class="column onecol">
						<aside id="selectable-menu-1" class="widget widget_nav_menu">
							<h3 class="widget-title"><?php echo $menu_name; ?></h3>
							<?php wp_nav_menu(array('menu' => $menu_name)); ?>
						</aside><!-- .widget -->
					</div>
				</div><!-- #sidebar-playbook -->
			<?php endif; ?>
		<?php endif; ?>
	<?php get_sidebar( 'top' ); ?>
	<?php get_sidebar( 'left' ); ?>
	<?php get_sidebar( 'right' ); ?>
	<?php get_sidebar( 'bottom' ); ?>
</div><!-- #sidebar -->