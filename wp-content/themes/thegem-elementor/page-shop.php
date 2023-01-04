<?php
/**
 * Template Name: Woocommerce Shop Page
 *
 * @package TheGem
 */

$thegem_page_data = get_post_meta(get_the_ID(), 'thegem_page_data', TRUE);
$thegem_slideshow_params = array_merge(array('slideshow_type' => '', 'slideshow_slideshow' => '', 'slideshow_layerslider' => '', 'slideshow_revslider' => ''), $thegem_page_data);

get_header(); ?>

<div id="main-content" class="main-content">

<?php
	if($thegem_slideshow_params['slideshow_type']) {
		thegem_slideshow_block(array('slideshow_type' => $thegem_slideshow_params['slideshow_type'], 'slideshow' => $thegem_slideshow_params['slideshow_slideshow'], 'lslider' => $thegem_slideshow_params['slideshow_layerslider'], 'slider' => $thegem_slideshow_params['slideshow_revslider']));
	}
?>
<?php echo thegem_page_title(); ?>

<?php woocommerce_content(); ?>

<?php
	while ( have_posts() ) : the_post();
		get_template_part( 'content', 'shop' );
	endwhile;
?>

</div><!-- #main-content -->

<?php
get_footer();
