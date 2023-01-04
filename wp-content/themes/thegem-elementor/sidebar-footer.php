<?php

if(!is_active_sidebar('footer-widget-area')) {
	return;
}
wp_enqueue_script('isotope-js');
?>

<div class="row inline-row footer-widget-area" role="complementary">
	<?php dynamic_sidebar('footer-widget-area'); ?>
</div><!-- .footer-widget-area -->
