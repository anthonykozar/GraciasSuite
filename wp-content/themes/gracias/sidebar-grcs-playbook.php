<?php
/*  sidebar-gracias-playbook.php

	Replacement main sidebar for use with the Land Bank Playbook
	template.

	Part of:  Gracias WordPress theme by Anthony Kozar
	License:  GNU General Public License v2 or later
	Created:  April 23, 2015

	A minor tweak of the "sidebar.php" and "sidebar-top.php" templates
	of the original Pinboard theme by One Designs, Inc.
	
*/
?>
<div id="sidebar" <?php pinboard_sidebar_class(); ?>>
<?php if(is_active_sidebar('gracias-playbook')): ?>
	<div id="sidebar-playbook" class="widget-area" role="complementary">
		<?php dynamic_sidebar('gracias-playbook'); ?>
	</div><!-- #sidebar-playbook -->
<?php endif; ?>
</div><!-- #sidebar -->