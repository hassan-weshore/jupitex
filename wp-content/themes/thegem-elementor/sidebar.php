<?php
/**
 * The Sidebar containing the main widget area
 */

if ( ! is_active_sidebar( 'page-sidebar' ) ) {
	return;
}
?>
<div class="page-sidebar widget-area" role="complementary">
	<?php dynamic_sidebar( 'page-sidebar' ); ?>
</div><!-- #page-sidebar -->
