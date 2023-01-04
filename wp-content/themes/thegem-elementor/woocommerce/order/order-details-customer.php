<?php
/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.6.0
 */

defined( 'ABSPATH' ) || exit;

$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>

<div class="row order-customer-details">
	<div class="<?php if ( $show_shipping ) : ?>col-sm-12 col-md-4<?php else: ?>col-sm-6 col-xs-12<?php endif; ?>">
		<header><h3><span class="light"><?php esc_html_e( 'Customer details', 'woocommerce' ); ?></span></h3></header>
		<?php if ( $order->get_customer_note() ) : ?>
			<?php esc_html_e( 'Note:', 'woocommerce' ); ?>
			<?php echo wptexturize( $order->get_customer_note() ); ?>
			<br/>
		<?php endif; ?>

		<?php if ( $order->get_billing_email() ) : ?>
			<?php esc_html_e( 'Email:', 'woocommerce' ); ?>
			<strong><?php echo esc_html( $order->get_billing_email() ); ?></strong>
			<br/>
		<?php endif; ?>

		<?php if ( $order->get_billing_phone() ) : ?>
			<?php esc_html_e( 'Phone:', 'woocommerce' ); ?>
			<strong><?php echo esc_html( $order->get_billing_phone() ); ?></strong>
			<br/>
		<?php endif; ?>

		<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>
	</div>

	<div class="<?php if ( $show_shipping ) : ?>col-sm-12 col-md-4<?php else: ?>col-sm-6 col-xs-12<?php endif; ?>">
		<header class="title">
			<h3><span class="light"><?php esc_html_e( 'Billing address', 'woocommerce' ); ?></span></h3>
		</header>
		<address>
			<?php echo ( $address = $order->get_formatted_billing_address() ) ? $address : __( 'N/A', 'woocommerce' ); ?>
		</address>
	</div>

	<?php if ( $show_shipping ) : ?>
		<div class="col-sm-12 col-md-4">
			<header class="title">
				<h3><span class="light"><?php esc_html_e( 'Shipping address', 'woocommerce' ); ?></span></h3>
			</header>
			<address>
				<?php echo wp_kses_post( $order->get_formatted_shipping_address( __( 'N/A', 'woocommerce' ) ) ); ?>
			</address>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>

</div>
