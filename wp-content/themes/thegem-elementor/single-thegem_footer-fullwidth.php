<?php

/**
 * Template Name: TheGem Full Width
 * Template Post Type: thegem_footer
 *
 * @package TheGem
 */

get_header(); ?>

<div id="main-content" class="main-content">
	<div class="block-content">
		<div class="container">
			<div class="panel row">
				<div class="col-xs-12">
					<p><?php _e('Content area. This is a dummy page content for footer builder. This content will not be displayed anywhere, it serves for testing purposes only to showcase the appearance of your footer template on some page.', 'thegem'); ?></p>
				</div>
			</div>
		</div><!-- .container -->
	</div><!-- .block-content -->
</div><!-- #main-content -->

<?php
get_footer('test');
