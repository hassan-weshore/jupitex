<?php

namespace Sellkit\Funnel\Steps;

use Sellkit\Funnel\Analytics\Data_Updater;
use Sellkit\Funnel\Contacts\Base_Contacts;
use Sellkit_Funnel;

defined( 'ABSPATH' ) || die();

/**
 * Class Sellkit_Upsell.
 *
 * @since 1.1.0
 */
class Upsell {
	/**
	 * Upsell constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$this->funnel = Sellkit_Funnel::get_instance();

		add_action( 'wp_ajax_sellkit_upsell_operations', [ $this, 'upsell_handler' ] );
		add_action( 'wp_ajax_nopriv_sellkit_upsell_operations', [ $this, 'upsell_handler' ] );
	}

	/**
	 * Ajax function to handle accepted offer actions.
	 *
	 * @since 1.1.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function upsell_handler() {
		$order_key  = sellkit_htmlspecialchars( INPUT_POST, 'order_key' );
		$offer_type = sellkit_htmlspecialchars( INPUT_POST, 'offer_type' );

		if ( empty( $order_key ) ) {
			wp_send_json_error( __( 'Each upsell needs a main order.', 'sellkit' ) );
		}

		$order_id   = wc_get_order_id_by_order_key( $order_key );
		$main_order = new \WC_Order( $order_id );

		if (
			empty( $this->funnel->next_no_step_data['page_id'] ) &&
			empty( $this->funnel->next_step_data['page_id'] ) &&
			! empty( $this->funnel->end_node_step_data['page_id'] )
		) {
			$next_step_link = add_query_arg( [ 'order-key' => $main_order->get_order_key() ], get_permalink( $this->funnel->end_node_step_data['page_id'] ) );

			wp_send_json_success( $next_step_link );
		}

		if ( 'reject' === $offer_type && ! empty( $this->funnel->next_no_step_data['page_id'] ) ) {
			$next_step_link = add_query_arg( [ 'order-key' => $main_order->get_order_key() ], get_permalink( $this->funnel->next_no_step_data['page_id'] ) );

			wp_send_json_success( $next_step_link );
		}

		Base_Contacts::step_is_passed();

		$billing_address  = $main_order->get_address();
		$shipping_address = $main_order->get_address( 'shipping' );
		$products         = $this->funnel->current_step_data['data']['products']['list'];

		$order = wc_create_order();

		$order_total = 0;

		foreach ( $products as $product_id => $product ) {
			$product_object = wc_get_product( $product_id );
			$regular_price  = $product_object->get_regular_price();
			$quantity       = ! empty( $product['quantity'] ) ? $product['quantity'] : 1;
			$discount       = ! empty( $product['discount'] ) ? $product['discount'] : 0;
			$sale_price     = self::calculate_sale_price( $regular_price, $discount, $product['discountType'] );
			$order_total    = floatval( $order_total ) + intval( $quantity ) * floatval( $sale_price );

			$product_object->set_price( $sale_price );
			$order->add_product( $product_object, $quantity );
		}

		$order->set_address( $billing_address );
		$order->set_address( $shipping_address, 'shipping' );
		$order->set_payment_method( $main_order->get_payment_method() );
		$order->set_total( $order_total );
		$order->set_customer_id( get_current_user_id() );
		$order->update_meta_data( 'sellkit_funnel_id', $this->funnel->funnel_id );
		$order->update_meta_data( 'sellkit_main_order_id', $main_order->get_id() );

		if ( ! empty( $this->funnel->next_step_data ) ) {
			$order->update_meta_data( 'sellkit_funnel_next_step_data', $this->funnel->next_step_data );
		}

		if (
			empty( $this->funnel->next_step_data ) &&
			! empty( $this->funnel->current_step_data['targets'][0]['nodeId'] ) &&
			'none' === $this->funnel->current_step_data['targets'][0]['nodeId'] &&
			! empty( $this->funnel->end_node_step_data )
		) {
			$order->update_meta_data( 'sellkit_funnel_next_step_data', $this->funnel->end_node_step_data );
		}

		$order->save();

		$analytics_updater = new Data_Updater();

		// Adding order logs.
		$analytics_updater->set_funnel_id( $this->funnel->funnel_id );
		$analytics_updater->add_new_order_log( $order, 'upsell' );

		$main_order->update_meta_data( 'sellkit_upsell_order_id', $order->get_id() );
		$main_order->save();

		$gateways = WC()->payment_gateways->payment_gateways();

		if ( 'ppcp-gateway' !== $main_order->get_payment_method() ) {
			$payment_process = $gateways[ $main_order->get_payment_method() ]->process_payment( $order->get_id() );
		}

		if ( 'ppcp-gateway' === $main_order->get_payment_method() ) {
			$payment_process = self::ppcp_process_payment( $order->get_id() );
		}

		if ( 'success' === $payment_process['result'] && ! empty( $payment_process['redirect'] ) ) {
			wp_send_json_success( $payment_process['redirect'] );
		}

		wp_send_json_error( __( 'Something went wrong', 'sellkit' ) );
	}

	/**
	 * Calculate the sale price.
	 *
	 * @param string $price Main price.
	 * @param string $discount Discount value.
	 * @param string $discount_type Discount type.
	 */
	public static function calculate_sale_price( $price, $discount, $discount_type ) {
		if ( $discount <= 0 ) {
			return $price;
		}

		if ( 'fixed' === $discount_type ) {
			return floatval( $price ) - floatval( $discount );
		}

		if ( 'percentage' === $discount_type ) {
			return floatval( $price ) - ( ( floatval( $price ) * floatval( $discount ) ) / 100 );
		}
	}

	/**
	 * Upsell actions.
	 *
	 * @since 1.1.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function do_actions() {
		$conditions = ! empty( $this->funnel->current_step_data['data']['conditions'] ) ? $this->funnel->current_step_data['data']['conditions'] : [];
		$is_valid   = sellkit_conditions_validation( $conditions );

		$args = [];

		if ( ! empty( $_GET['order-key'] ) ) { // phpcs:ignore
			$args['order-key'] = $_GET['order-key']; // phpcs:ignore
		}

		if ( ! $is_valid && ! empty( $this->funnel->next_step_data['page_id'] ) ) {
			wp_safe_redirect( add_query_arg( $args, get_permalink( $this->funnel->next_step_data['page_id'] ) ) );
			exit;
		}
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 * @since 1.1.0
	 */
	public static function ppcp_process_payment( $order_id ) {
		if ( ! class_exists( 'WC_Gateway_Paypal' ) ) {
			return false;
		}

		$paypal = new \WC_Gateway_Paypal();

		return $paypal->process_payment( $order_id );
	}
}
