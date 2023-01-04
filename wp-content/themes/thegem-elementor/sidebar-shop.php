<?php

if(!is_active_sidebar('shop-sidebar')) {
	return;
}

?>
<div class="page-sidebar widget-area" role="complementary">
		<?php dynamic_sidebar('shop-sidebar'); ?>
</div><!-- .shop-sidebar -->