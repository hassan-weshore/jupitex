<?php
/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $order && ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}

if ( $order ) {
	$product_thumbnail_url = get_the_post_thumbnail_url( $item['product_id'] );
	$qty                   = $item->get_quantity();
	$refunded_qty          = $order->get_qty_refunded_for_item( $item_id );

	if ( $refunded_qty ) {
		$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
	} else {
		$qty_display = esc_html( $qty );
	}

	$qty_display = apply_filters( 'woocommerce_order_item_quantity_html', "<strong class='product-quantity'>{$qty_display}</strong>", $item );
}

if ( ! $order ) {
	$product_thumbnail_url = $item['product_image'];
	$qty_display           = $item['quantity'];
}
?>
<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order ) ); ?>">
	<td class="woocommerce-table__product-name product-name">
		<?php if ( ! empty( $product_thumbnail ) ) : ?>
			<div class="sellkit-order-cart-detail-items-thumbnail">
				<div class="sellkit-order-cart-detail-items-thumbnail-image-wrapper">
				<?php if ( ! empty( $product_thumbnail_url ) ) : ?>
					<img src="<?php echo $product_thumbnail_url; ?>" alt="">
				<?php endif; ?>
				</div>
				<div class="sellkit-order-cart-detail-items-thumbnail-quantity">
					<?php echo $qty_display; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php

		if ( $order ) {
			$is_visible        = $product && $product->is_visible();
			$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

			echo apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a class="sellkit-order-cart-detail-items-product-title" href="%s">%s</a>', $product_permalink, $item->get_name() ) : $item->get_name(), $item, $is_visible ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

			wc_display_item_meta( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
		}

		if ( ! $order ) :
			?>
				<div class="sellkit-order-cart-detail-items-product-title">
					<?php echo apply_filters( 'woocommerce_order_item_name', $item['product_name'], $item ); ?>
				</div>
			<?php
		endif;
		?>
	</td>

	<td class="woocommerce-table__product-total product-total">
		<?php
		if ( $order ) {
			echo $order->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		if ( ! $order ) {
			?>
			<div class="sellkit-order-cart-detail-items-price">
			<?php echo $item['product_price']; ?>
			</div>
			<?php
		}
		?>
	</td>
</tr>

<?php if ( $order && $show_purchase_note && $purchase_note ) : ?>
	<tr class="woocommerce-table__product-purchase-note product-purchase-note">
		<td colspan="2"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
	</tr>
<?php endif; ?>
