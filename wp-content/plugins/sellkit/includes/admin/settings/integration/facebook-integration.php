<?php

namespace Sellkit\Admin\Settings\Integration;

defined( 'ABSPATH' ) || die();

/**
 * Class Facebook Pixel integration.
 *
 * @package Sellkit\Admin\Settings\Integration\Settings_Integration
 * @since 1.1.0
 */
class Facebook extends Settings_Integration {
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
	 * Get Facebook analyticts scripts.
	 *
	 * @since 1.1.0
	 */
	public function analytics() {
		$fb_tracking_id = esc_attr( sellkit_get_option( 'facebook_pixel_id' ) );

		if ( empty( $fb_tracking_id ) || empty( sellkit_get_option( 'facebook_pixel' ) ) ) {
			return;
		}

		// phpcs:disable
		$facebook_script =
			"<!-- Facebook Pixel Script By Sellkit -->
				<script type='text/javascript'>
					!function(f,b,e,v,n,t,s)
					{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
					n.callMethod.apply(n,arguments):n.queue.push(arguments)};
					if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
					n.queue=[];t=b.createElement(e);t.async=!0;
					t.src=v;s=b.getElementsByTagName(e)[0];
					s.parentNode.insertBefore(t,s)}(window, document,'script',
					'https://connect.facebook.net/en_US/fbevents.js');
				</script>

				<noscript>
					<img height='1' width='1' style='display:none' src='https://www.facebook.com/tr?id=" . esc_js( $fb_tracking_id ) . "&ev=PageView&noscript=1'/>
				</noscript>

				<script type='text/javascript'>
					fbq('init', " . esc_js( $fb_tracking_id ) . ");
					fbq('track', 'PageView', {'plugin': 'Sellkit'});
				</script>
			<!-- Facebook Pixel Script By Sellkit -->
			";
		// phpcs:enable

		$facebook_script .= $this->facebook_pixel_events( self::$post, $fb_tracking_id );

		echo $facebook_script;
	}

	/**
	 * Facebook pixel events scripts.
	 *
	 * @since 1.1.0
	 * @param array  $post_meta sellkit pages data.
	 * @param string $tracking_id fb pixel tracking id.
	 */
	private function facebook_pixel_events( $post_meta, $tracking_id ) {
		$fb_pixel_events = sellkit_get_option( 'facebook_pixel_events' );

		if ( empty( $fb_pixel_events ) ) {
			return;
		}

		$sellkit_page_meta = $post_meta[0]['type']['key'];
		$fb_events_script  = '';

		foreach ( $fb_pixel_events as $event ) {
			$function_name = "fb_pixel_{$event}";

			$fb_events_script .= $this->$function_name( $sellkit_page_meta, $tracking_id );
		}

		return $fb_events_script;
	}

	/**
	 * Get facebook pixel initiate_checkout data.
	 *
	 * @since 1.1.0
	 * @param string $sellkit_page_meta sellkit pages type.
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function fb_pixel_initiate_checkout( $sellkit_page_meta ) {
		if ( 'checkout' !== $sellkit_page_meta ) {
			return;
		}

		$cart_content      = $this->get_cart_data( 'add_to_cart' );
		$initiate_checkout = $this->get_cart_data( 'initiate_checkout' );

		$fb_events_script =
			"<script type='text/javascript'>
			fbq( 'track', 'AddToCart', $cart_content );
				fbq( 'track', 'InitiateCheckout', $initiate_checkout );
			</script>";

		return $fb_events_script;
	}

	/**
	 * Get facebook pixel add_payment_info data.
	 *
	 * @since 1.1.0
	 * @param string $sellkit_page_meta sellkit pages type.
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function fb_pixel_add_payment_info( $sellkit_page_meta ) {
		if ( 'checkout' !== $sellkit_page_meta ) {
			return;
		}

		$cart_content = $this->get_cart_data( 'add_payment_info' );

		self::$localized_data['fbAddPaymentInfo'] = $cart_content;
	}

	/**
	 * Get facebook pixel purchage_complete data.
	 *
	 * @since 1.1.0
	 * @param string $sellkit_page_meta sellkit pages type.
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function fb_pixel_purchace_complete( $sellkit_page_meta ) {
		if ( 'thankyou' !== $sellkit_page_meta ) {
			return;
		}

		$order_content = $this->get_order_data();

		$fb_events_script =
			"<script type='text/javascript'>
				fbq( 'track', 'Purchase', $order_content );
			</script>";

		return $fb_events_script;
	}

	/**
	 * Get cart data.
	 *
	 * @since 1.1.0
	 * @param string $event Filter fb pixel tracking id.
	 */
	private function get_cart_data( $event ) {
		$cart_data = [];
		$products  = [];

		if ( ! sellkit()->has_valid_dependencies() ) {
			return wp_json_encode( [] );
		}

		$cart_total = WC()->cart->cart_contents_total + WC()->cart->tax_total;

		$products      = $this->get_products_data( WC()->cart->get_cart(), 'fb' );
		$product_name  = isset( $products['products_name'] ) ? substr( $products['products_name'], 2 ) : '';
		$category_name = isset( $products['categories_name'] ) ? substr( $products['categories_name'], 2 ) : '';
		$content_name  = isset( $products['cart_contents'] ) ? wp_json_encode( $products['cart_contents'] ) : '';

		$cart_data = [
			'content_type'     => 'product',
			'plugin'           => 'Sellkit',
			'value'            => number_format( floatval( $cart_total ), wc_get_price_decimals(), '.', '' ),
			'content_name'     => $product_name,
			'content_category' => $category_name,
			'contents'         => $content_name,
			'currency'         => get_woocommerce_currency(),
			'user_roles'       => implode( ', ', wp_get_current_user()->roles ),
		];

		if ( 'add_to_cart' === $event ) {
			$cart_items_count = WC()->cart->get_cart_contents_count();

			$cart_data = array_merge(
				$cart_data,
				[
					'num_items' => $cart_items_count,
					'domain'    => get_site_url(),
					'language'  => get_bloginfo( 'language' ),
					'userAgent' => wp_unslash( $_SERVER['HTTP_USER_AGENT'] ), //phpcs:ignore
				]
			);

			return wp_json_encode( $cart_data );
		}

		return wp_json_encode( $cart_data );
	}

	/**
	 * Get purchase data.
	 *
	 * @since 1.1.0
	 */
	private function get_order_data() {
		if ( empty( self::$order_key ) ) {
			return;
		}

		$order_id = wc_get_order_id_by_order_key( self::$order_key );
		$order    = wc_get_order( $order_id );
		$products = $this->get_products_data( $order->get_items(), 'fb' );

		$purchase_data = [
			'transaction_id' => $order_id,
			'content_type'   => 'product',
			'currency'       => $order->get_currency(),
			'userAgent'      => wp_unslash( $_SERVER['HTTP_USER_AGENT'] ), //phpcs:ignore
			'plugin'         => 'Sellkit',
			'value'          => number_format( $order->get_total(), wc_get_price_decimals(), '.', '' ),
		];

		if ( ! empty( $products ) ) {
			$purchase_data = array_merge(
				$purchase_data,
				[
					'content_ids' => $products['content_ids'],
					'content_names' => $products['products_name'],
					'content_category' => $products['categories_name'],
				]
			);
		}

		return wp_json_encode( $purchase_data );
	}
}

Facebook::get_instance();
