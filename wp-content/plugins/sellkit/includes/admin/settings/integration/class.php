<?php

namespace Sellkit\Admin\Settings\Integration;

defined( 'ABSPATH' ) || die();

/**
 * Class Google Analytics and Facebook Pixel integration.
 *
 * @package Sellkit\Admin\Settings\Integration\Settings_Integration
 * @since 1.1.0
 */
class Settings_Integration {
	/**
	 * The class instance.
	 *
	 * @var Object Class instance.
	 * @since 1.1.0
	 */
	public static $instance = null;

	/**
	 * Post data.
	 *
	 * @var array
	 * @since 1.1.0
	 */
	public static $post = [];

	/**
	 * Order key.
	 *
	 * @var string
	 * @since 1.1.0
	 */
	public static $order_key = [];

	/**
	 * Localized data.
	 *
	 * @var array localized data.
	 * @since 1.1.0
	 */
	public static $localized_data = [];

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
		add_action( 'wp_head', [ $this, 'google_facebook_analytics' ] );
	}

	/**
	 * Get google analyticts scripts.
	 *
	 * @since 1.1.0
	 */
	public function google_facebook_analytics() {
		global $post;

		if (
			empty( $post ) ||
			empty( get_post_meta( $post->ID, 'step_data' ) ) ||
			$this->is_elementor_preview()
		) {
			return;
		}

		self::$post      = get_post_meta( $post->ID, 'step_data' );
		self::$order_key = sellkit_htmlspecialchars( INPUT_GET, 'order-key' );

		if ( ! sellkit()->has_valid_dependencies() ) {
			self::$order_key = null;
		}

		sellkit()->load_files(
			[
				'admin/settings/integration/google-integration',
				'admin/settings/integration/facebook-integration',
			]
		);

		wp_localize_script( 'funnel-settings-variables', 'sellkitSettings', self::$localized_data );
	}

	/**
	 * Check is elementor preview.
	 *
	 * @since 1.1.0
	 */
	private function is_elementor_preview() {
		if ( class_exists( '\Elementor\Plugin' ) ) {

			if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get product data.
	 *
	 * @since 1.1.0
	 * @param array  $products_list list of the products.
	 * @param string $event event type name.
	 */
	public function get_products_data( $products_list, $event ) {
		if ( empty( $products_list ) ) {
			return;
		}

		$products         = [];
		$products_id      = [];
		$products_name    = '';
		$categories_names = '';

		foreach ( $products_list as $product ) {
			$product_data = wc_get_product( $product['product_id'] );

			if ( $product_data->is_type( 'variable' ) && isset( $product['variation_id'] ) ) {
				$product_data = wc_get_product( $product['variation_id'] );
			}

			if ( ! empty( $product_data ) ) {
				$products_id[]    = (string) $product_data->get_id();
				$products_name    = $products_name . ', ' . $product_data->get_name();
				$categories_names = $categories_names . ', ' . wp_strip_all_tags( wc_get_product_category_list( $product_data->get_id() ) );

				$products[] = [
					'id'       => $product_data->get_id(),
					'name'     => $product_data->get_name(),
					'sku'      => $product_data->get_sku(),
					'category' => wp_strip_all_tags( wc_get_product_category_list( $product_data->get_id() ) ),
					'quantity' => $product['quantity'],
				];
			}
		}

		if ( 'fb' === $event ) {
			$fb_products = [
				'cart_contents'   => $products,
				'content_ids'     => $products_id,
				'products_name'   => $products_name,
				'categories_name' => $categories_names,
			];

			return $fb_products;
		}

		return $products;
	}
}

Settings_Integration::get_instance();
