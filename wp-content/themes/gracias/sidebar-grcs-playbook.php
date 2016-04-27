<?php
/*  sidebar-grcs-alternate.php

	Replacement main sidebar for use with the Alternate Sidebar
	template.

	Part of:  Gracias WordPress theme by Anthony Kozar
	License:  GNU General Public License v2 or later
	Created:  April 23, 2015

	A minor tweak of the "sidebar.php" and "sidebar-top.php" templates
	of the original Pinboard theme by One Designs, Inc.
	
*/
?>
<div id="sidebar" <?php pinboard_sidebar_class(); ?>>
<?php if(is_active_sidebar('gracias-alternate')): ?>
	<div id="sidebar-alternate" class="widget-area" role="complementary">
		<?php dynamic_sidebar('gracias-alternate'); ?>
	</div><!-- #sidebar-alternate -->
<?php endif; ?>
</div><!-- #sidebar -->