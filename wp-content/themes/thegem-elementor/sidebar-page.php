<?php
/**
 * The Content Sidebar
 */

if(!is_active_sidebar('page-sidebar')) {
	return;
}
?>
<div class="widget-area">
	<?php dynamic_sidebar('page-sidebar'); ?>
</div>
