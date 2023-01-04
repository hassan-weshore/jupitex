<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php
	/**
	 * woocommerce_before_single_product hook.
	 *
	 * @hooked wc_print_notices - 10
	 */
	//do_action( 'woocommerce_before_single_product' );

	if ( post_password_required() ) {
		echo get_the_password_form();
		return;
	}
?>

<div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="single-product-content row">
		<div class="single-product-content-left col-sm-5 col-xs-12">
			<?php do_action('thegem_woocommerce_single_product_quick_view_left'); ?>
		</div>

		<div class="single-product-content-right col-sm-7 col-xs-12">
			<?php do_action('thegem_woocommerce_single_product_quick_view_right'); ?>
		</div>

	</div>
</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action('thegem_woocommerce_single_product_quick_view_bottom'); ?>