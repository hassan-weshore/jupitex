<?php

/**
 * Template Name: TheGem Full Width
 *
 * @package TheGem
 */

get_header(); ?>

<div id="main-content" class="main-content">

<?php
	while ( have_posts() ) : the_post();
		get_template_part( 'content', 'page-fullwidth' );
	endwhile;
?>

</div><!-- #main-content -->

<?php
get_footer();
