<?php
/**
 * Single Product Up-Sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/up-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see		 https://docs.woocommerce.com/document/template-structure/
 * @package	 WooCommerce\Templates
 * @version	 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $thegem_product_data;

$isLegacy = $thegem_product_data['product_page_layout'] === 'legacy';
$isUpsells = $thegem_product_data['product_page_elements_upsell'];
$isFullWidth = $thegem_product_data['product_page_elements_upsell_columns_desktop'] == '100%';

if ( !$isLegacy && !$isUpsells ) {
	return;
}

if(intval($thegem_product_data['product_page_elements_upsell_items']) > -1) {
	$upsells = array_slice($upsells, 0, intval($thegem_product_data['product_page_elements_upsell_items']));
}

if ( $upsells ) : ?>

	<section class="up-sells upsells products">
		<?php if ($isLegacy): $heading = apply_filters( 'woocommerce_product_upsells_products_heading', __( 'You may also like&hellip;', 'woocommerce' ) ); ?>
			<?php if ($heading): ?>
				<div class="gem-button-separator gem-button-separator-type-soft-double">
					<div class="gem-button-separator-holder">
						<div style="border-color: #b6c6c9;" class="gem-button-separator-line"></div>
					</div>
					<div class="gem-button-separator-button">
						<h2 class="light"><?php echo esc_html( $heading ); ?></h2>
					</div>
					<div class="gem-button-separator-holder">
						<div style="border-color: #b6c6c9;" class="gem-button-separator-line"></div>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if (!$isLegacy): $heading = $thegem_product_data['product_page_elements_upsell_title']; ?>
			<?php if ($heading): ?>
				<div class="product-page__elements-title <?=$isFullWidth ? 'fullwidth-block' : null?>" <?php if ($isFullWidth): ?>style="width: 100vw; left: calc(50% - 50vw);"<?php endif;?>>
					<div class="<?=$isFullWidth ? 'container-fullwidth' : 'container row'?>">
						<div class="title-h4 light"><?php echo esc_html( $heading ); ?></div>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( thegem_get_option('product_archive_type') != 'grid' ) : ?>
			<?php woocommerce_product_loop_start(); ?>

				<?php foreach ( $upsells as $upsell ) : ?>

					<?php
					$post_object = get_post( $upsell->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

					wc_get_template_part( 'content', 'product' );
					?>

				<?php endforeach; ?>

			<?php woocommerce_product_loop_end(); ?>
		<?php else :
			$args = array(
				'columns_desktop' => $thegem_product_data['product_page_elements_upsell_columns_desktop'],
				'columns_tablet' => $thegem_product_data['product_page_elements_upsell_columns_tablet'],
				'columns_mobile' => $thegem_product_data['product_page_elements_upsell_columns_mobile'],
				'columns_100' => $thegem_product_data['product_page_elements_upsell_columns_100'],
			);
			thegem_woocommerce_short_grid_content($upsells, $args);
		endif; ?>

	</section>

	<?php
endif;

wp_reset_postdata();
