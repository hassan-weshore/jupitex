<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

if ( $order ) {
	$show_purchase_note = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
	$downloads          = $order->get_downloadable_items();
	$show_downloads     = $order->has_downloadable_item() && $order->is_download_permitted();
}

if ( ! empty( $show_downloads ) ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>
	<section class="woocommerce-order-details sellkit-order-cart-detail">
		<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

		<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
			<?php if ( ! empty( $heading ) ) : ?>
			<thead>
			<tr>
				<th class="woocommerce-table__product-name product-name sellkit-cart-detail-heading"><?php echo $label; ?></th>
				<th class="woocommerce-table__product-table product-total"></th>
			</tr>
			</thead>
			<?php endif; ?>
			<tbody>
			<?php
			do_action( 'woocommerce_order_details_before_order_table_items', $order );

			if ( $cart_items ) {
				foreach ( $order_items as $item_id => $item ) {
					if ( $order ) {
						$product       = $item->get_product();
						$purchase_note = $product ? $product->get_purchase_note() : '';
					}

					require sellkit()->plugin_dir() . 'includes/elementor/modules/order-cart-details/templates/order-details-item.php';
				}
			}

			do_action( 'woocommerce_order_details_after_order_table_items', $order );
			?>
			</tbody>
			<tfoot>
			<?php
			foreach ( $order_items_total as $key => $total ) {
				?>
				<tr>
					<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
					<td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
				</tr>
				<?php
			}
			?>
			<?php if ( $order && $order->get_customer_note() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
					<td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
			<?php endif; ?>
			</tfoot>
		</table>

		<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
	</section>
