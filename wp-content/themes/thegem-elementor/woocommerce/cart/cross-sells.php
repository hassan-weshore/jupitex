<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

$isLegacy = thegem_get_option('cart_layout') !== 'modern';
$isCrossSells = thegem_get_option('cart_elements_cross_sells');

if ( !$isLegacy && !$isCrossSells ) {
	return;
}

if(intval(thegem_get_option('cart_elements_cross_sells_items')) > -1) {
	$cross_sells = array_slice($cross_sells, 0, intval(thegem_get_option('cart_elements_cross_sells_items', 4)));
}

if ( $cross_sells ) : ?>

	<div class="cross-sells">
		<?php
		$heading = apply_filters( 'woocommerce_product_cross_sells_products_heading', $isLegacy || empty(thegem_get_option('cart_elements_cross_sells_title')) ?__( 'You may be interested in&hellip;', 'woocommerce' ) : thegem_get_option('cart_elements_cross_sells_title') );

		if ( $heading ) :
			?>
			<?php if($isLegacy) : ?><div class="gem-button-separator gem-button-separator-type-soft-double"><div class="gem-button-separator-holder"><div style="border-color: #b6c6c9;" class="gem-button-separator-line"></div></div><div class="gem-button-separator-button"><?php endif; ?>
				<<?php echo ($isLegacy ? 'h2' : 'h3');?> class="light"><?php echo esc_html( $heading ); ?></<?php echo ($isLegacy ? 'h2' : 'h3');?>>
			<?php if($isLegacy) : ?></div><div class="gem-button-separator-holder"><div style="border-color: #b6c6c9;" class="gem-button-separator-line"></div></div></div><?php endif; ?>
		<?php endif; ?>

		<?php if ( thegem_get_option('product_archive_type') != 'grid' ) : ?>
			<?php woocommerce_product_loop_start(); ?>

				<?php foreach ( $cross_sells as $cross_sell ) : ?>

					<?php
						$post_object = get_post( $cross_sell->get_id() );

						setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

						wc_get_template_part( 'content', 'product' );
					?>

				<?php endforeach; ?>

			<?php woocommerce_product_loop_end(); ?>
		<?php else :
			$args = array(
				'columns_desktop' => thegem_get_option('cart_elements_cross_sells_columns_desktop', 4),
				'columns_tablet' => thegem_get_option('cart_elements_cross_sells_columns_tablet', 4),
				'columns_mobile' => thegem_get_option('cart_elements_cross_sells_columns_mobile', 2),
				'columns_100' => thegem_get_option('cart_elements_cross_sells_columns_100', 4),
			);
			thegem_woocommerce_short_grid_content($cross_sells, $args);
		endif; ?>

	</div>
	<?php
endif;

wp_reset_postdata();
