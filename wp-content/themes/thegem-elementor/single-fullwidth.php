<?php

/**
 * Template Name: TheGem Full Width
 * Template Post Type: post,thegem_pf_item
 *
 * @package TheGem
 */

get_header(); ?>

<div id="main-content" class="main-content">

<?php
	while ( have_posts() ) : the_post();
		if(in_array(get_post_type(), array_merge(array('post', 'thegem_pf_item', 'thegem_news'), thegem_get_available_po_custom_post_types()), true)) {
			get_template_part( 'content', 'page-fullwidth' );
		} else {
			get_template_part( 'content', get_post_format() );
		}
	endwhile;
?>

</div><!-- #main-content -->

<?php
get_footer();
