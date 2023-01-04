<?php

if(!is_active_sidebar('shop-widget-area')) {
	return;
}
?>
<section id="shop-widget-area" class="shop-widget-area default-background" role="complementary">
	<div class="container"><div class="row inline-row">
		<?php dynamic_sidebar('shop-widget-area'); ?>
	</div></div>
</section><!-- #shop-widget-area -->