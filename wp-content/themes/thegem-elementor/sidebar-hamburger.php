<?php

if(!is_active_sidebar('hamburger')) {
	return;
}

?>
<div class="hamburger-widgets widget-area" role="complementary">
	<?php dynamic_sidebar('hamburger'); ?>
</div><!-- .hamburger-widgets -->