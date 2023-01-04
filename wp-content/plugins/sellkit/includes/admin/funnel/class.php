<?php

namespace Sellkit\Admin\Funnel;

defined( 'ABSPATH' ) || die();

/**
 * Class Funnel.
 *
 * @since 1.1.0
 */
class Funnel {

	const SELLKIT_FUNNELS_TEMPLATE_SOURCE = 'https://templates.getsellkit.com';

	/**
	 * Funnel_Importer constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		if ( class_exists( 'Elementor\Plugin' ) ) {
			sellkit()->load_files( [
				'admin/funnel/importer/page-builders/elementor-importer',
			] );
		}

		sellkit()->load_files(
			[
				'admin/funnel/importer/page-builders/importer-base',
				'admin/funnel/importer/step-importer',
				'admin/funnel/importer/ajax-handler',
			]
		);

		add_action( 'wp_ajax_sellkit_steps_bump_get_product_data', [ $this, 'get_product_data' ] );
	}

	/**
	 * Gets products data.
	 *
	 * @since 1.1.0
	 */
	public function get_product_data() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$product_id = filter_input( INPUT_GET, 'product_id', FILTER_SANITIZE_NUMBER_INT );

		if ( ! sellkit()->has_valid_dependencies() ) {
			wp_send_json_error( __( 'Please install and activate WooCommerce.', 'sellkit' ) );
		}

		$product = wc_get_product( $product_id );

		if ( empty( $product ) ) {
			wp_send_json_error( __( 'Product does not exist', 'sellkit' ) );
		}

		wp_send_json_success( [
			'thumbnail' => get_the_post_thumbnail_url( $product_id ),
			'regularPrice' => $product->get_regular_price(),
		] );
	}
}

new Funnel();
