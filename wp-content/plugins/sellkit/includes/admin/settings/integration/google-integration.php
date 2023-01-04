<?php

namespace Sellkit\Admin\Settings\Integration;

defined( 'ABSPATH' ) || die();

/**
 * Class Google Analytics integration.
 *
 * @package Sellkit\Admin\Settings\Integration\Settings_Integration
 * @since 1.1.0
 */
class Google extends Settings_Integration {
	/**
	 * The class instance.
	 *
	 * @var Object Class instance.
	 * @since 1.1.0
	 */
	public static $instance = null;

	/**
	 * Class Instance.
	 *
	 * @since 1.1.0
	 * @return Sellkit_Funnel|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		parent::__construct();

		$this->analytics();
	}

	/**
	 * Get google analyticts scripts.
	 *
	 * @since 1.1.0
	 */
	public function analytics() {
		$google_tracking_id = esc_attr( sellkit_get_option( 'google_analytics_id' ) );

		if ( empty( $google_tracking_id ) || empty( sellkit_get_option( 'google_analytics' ) ) ) {
			return;
		}

		// phpcs:disable
		$google_script =
			'<!-- Google Analytics Script By Sellkit -->
				<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_js( $google_tracking_id ) . '"></script>

				<script>
					window.dataLayer = window.dataLayer || [];
					function gtag(){dataLayer.push(arguments);}
					gtag( "js", new Date() );
					gtag("config","' . esc_js( $google_tracking_id ) . '");
				</script>
				<!-- Google Analytics Script By Sellkit -->
			';
		// phpcs:enable

		$google_script .= $this->google_analytics_events( self::$post, $google_tracking_id );

		echo $google_script;
	}

	/**
	 * Get google analyticts events scripts.
	 *
	 * @since 1.1.0
	 * @param array  $post_meta sellkit pages data.
	 * @param string $tracking_id google tracking id.
	 */
	private function google_analytics_events( $post_meta, $tracking_id ) {
		$google_events = sellkit_get_option( 'google_analytics_events' );

		if ( empty( $google_events ) ) {
			return;
		}

		$sellkit_page_meta    = $post_meta[0]['type']['key'];
		$google_events_script = '';

		foreach ( $google_events as $event ) {
			$function_name = "google_analytics_{$event}";

			$google_events_script .= $this->$function_name( $sellkit_page_meta, $tracking_id );
		}

		return $google_events_script;
	}

	/**
	 * Get google analyticts begin_checkout data.
	 *
	 * @since 1.1.0
	 * @param string $sellkit_page_meta sellkit pages type.
	 * @param string $tracking_id Filter google tracking id.
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function google_analytics_begin_checkout( $sellkit_page_meta, $tracking_id ) {
		if ( 'checkout' !== $sellkit_page_meta ) {
			return;
		}

		$cart_content = $this->get_cart_data( $tracking_id );

		$google_events_script =
			"<script type='text/javascript'>
				gtag( 'event', 'begin_checkout', $cart_content );
			</script>";

		return $google_events_script;
	}

	/**
	 * Get google analyticts add_to_cart data.
	 *
	 * @since 1.1.0
	 * @param string $sellkit_page_meta sellkit pages type.
	 * @param string $tracking_id Filter google tracking id.
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function google_analytics_add_to_cart( $sellkit_page_meta, $tracking_id ) {
		if ( 'checkout' !== $sellkit_page_meta ) {
			return;
		}

		$cart_content = $this->get_cart_data( $tracking_id );

		$google_events_script =
			"<script type='text/javascript'>
				gtag( 'event', 'add_to_cart', $cart_content );
			</script>";

		return $google_events_script;
	}

	/**
	 * Get google analyticts add_payment_info data.
	 *
	 * @since 1.1.0
	 * @param string $sellkit_page_meta sellkit pages type.
	 * @param string $tracking_id Filter google tracking id.
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function google_analytics_add_payment_info( $sellkit_page_meta, $tracking_id ) {
		if ( 'checkout' !== $sellkit_page_meta ) {
			return;
		}

		$cart_content = $this->get_cart_data( $tracking_id );

		self::$localized_data['GoogleAddPaymentInfo'] = $cart_content;
	}

	/**
	 * Get google analyticts purchase data.
	 *
	 * @since 1.1.0
	 * @param string $sellkit_page_meta sellkit pages type.
	 * @param string $tracking_id Filter google tracking id.
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function google_analytics_purchase( $sellkit_page_meta, $tracking_id ) {
		if ( 'thankyou' !== $sellkit_page_meta ) {
			return;
		}

		$cart_content = $this->get_order_data( $tracking_id );

		$google_events_script =
			"<script type='text/javascript'>
				gtag( 'event', 'purchase', $cart_content );
			</script>";

		return $google_events_script;
	}

	/**
	 * Get order data.
	 *
	 * @since 1.1.0
	 * @param string $tracking_id Filter google tracking id.
	 */
	private function get_order_data( $tracking_id ) {
		$order_data = [];
		$products   = [];

		if ( empty( self::$order_key ) ) {
			return;
		}

		$order_id = wc_get_order_id_by_order_key( self::$order_key );
		$order    = wc_get_order( $order_id );

		$products = $this->get_products_data( $order->get_items(), 'google' );

		$order_data = [
			'send_to'         => $tracking_id,
			'event_category'  => 'Enhanced-Ecommerce',
			'transaction_id'  => $order_id,
			'affiliation'     => get_bloginfo( 'name' ),
			'value'           => number_format( $order->get_total(), wc_get_price_decimals(), '.', '' ),
			'currency'        => $order->get_currency(),
			'tax'             => number_format( $order->get_total_tax(), wc_get_price_decimals(), '.', '' ),
			'shipping'        => number_format( $order->get_shipping_total() + $order->get_shipping_tax(), wc_get_price_decimals(), '.', '' ),
			'coupon'          => $order->get_coupon_codes(),
			'non_interaction' => true,
			'products'        => wp_json_encode( $products ),
		];

		return wp_json_encode( $order_data );
	}

	/**
	 * Get cart content.
	 *
	 * @since 1.1.0
	 * @param string $tracking_id Filter google tracking id.
	 */
	private function get_cart_data( $tracking_id ) {
		if ( ! sellkit()->has_valid_dependencies() ) {
			return wp_json_encode( [] );
		}

		$products_in_cart = WC()->cart->get_cart();
		$products         = [];
		$cart_data        = [];

		$products = $this->get_products_data( $products_in_cart, 'google' );

		$price = WC()->cart->cart_contents_total + WC()->cart->tax_total;

		$cart_data = [
			'send_to'         => $tracking_id,
			'event_category'  => 'Enhanced-Ecommerce',
			'currency'        => get_woocommerce_currency(),
			'coupon'          => WC()->cart->get_applied_coupons(),
			'value'           => number_format( floatval( $price ), wc_get_price_decimals(), '.', '' ),
			'products'        => wp_json_encode( $products ),
			'non_interaction' => true,
		];

		return wp_json_encode( $cart_data );
	}
}

Google::get_instance();
