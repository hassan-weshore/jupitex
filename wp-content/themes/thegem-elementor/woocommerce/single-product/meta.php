<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $thegem_product_data;

$isLegacy = $thegem_product_data['product_page_layout'] == 'legacy';

$isSku = $thegem_product_data['product_page_elements_sku'];
$skuTitle = $thegem_product_data['product_page_elements_sku_title'];
$isCategories = $thegem_product_data['product_page_elements_categories'];
$categoriesTitle = $thegem_product_data['product_page_elements_categories_title'];
$isTags = $thegem_product_data['product_page_elements_tags'];
$tagsTitle = $thegem_product_data['product_page_elements_tags_title'];

?>

<div class="product-meta product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) )) : ?>
        <?php if ($isLegacy || (!$isLegacy && $isSku)): ?>
	        <?php if (!$isLegacy): ?>
		        <div class="sku_wrapper"><?php if ($skuTitle): ?><span class="date-color"><?= esc_html_e( $skuTitle ) ?>: </span><?php endif; ?> <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span>.</div>
			<?php else:; ?>
                <div class="sku_wrapper"><span class="date-color"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?></span> <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span>.</div>
			<?php endif; ?>
        <?php endif; ?>
	<?php endif; ?>

	<?php if ($isLegacy || (!$isLegacy && $isCategories)): ?>
        <?php if (!$isLegacy): $title = $categoriesTitle ? '<span class="date-color">'.esc_html($categoriesTitle).': </span>' : null?>
		    <?php echo wc_get_product_category_list( $product->get_id(), ', ', '<div class="posted_in">'.$title, '</div>' ); ?>
		<?php else:; ?>
            <?php echo wc_get_product_category_list( $product->get_id(), ', ', '<div class="posted_in"><span class="date-color">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . '</span> ', '.</div>' ); ?>
	    <?php endif; ?>
    <?php endif; ?>

	<?php if ($isLegacy || (!$isLegacy && $isTags)): ?>
	    <?php if (!$isLegacy): $title = $tagsTitle ? '<span class="date-color">'.esc_html($tagsTitle).': </span><span class="post-tags-list">' : '<span class="post-tags-list">'?>
			<?php echo wc_get_product_tag_list( $product->get_id(), ' ', '<div class="tagged_as">'.$title, '</span></div>' ); ?>
		<?php else:; ?>
			<?php echo wc_get_product_tag_list( $product->get_id(), ' ', '<div class="tagged_as"><span class="date-color">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'woocommerce' ) . '</span> <span class="post-tags-list">', '</span></div>' ); ?>
		<?php endif; ?>
	<?php endif; ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>