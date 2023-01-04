<?php

defined( 'ABSPATH' ) || die();

/**
 * Sellkit Elementor Upsell Base Widget.
 *
 * @since 1.1.0
 */
abstract class Sellkit_Elementor_Upsell_Base_Widget extends Sellkit_Elementor_Base_Widget {

	/**
	 * Step data which was sent from funnel.
	 *
	 * @var array
	 */
	public $step_data;

	/**
	 * Sellkit_Elementor_Upsell_Base_Widget constructor.
	 *
	 * @since 1.1.0
	 * @param array $data Data.
	 * @param null  $args Args.
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		global $post;

		if ( empty( $post->ID ) ) {
			return;
		}

		$this->step_data = get_post_meta( $post->ID, 'step_data', true );
	}

	/**
	 * It's used for getting product object data which was defined in funnels panel.
	 *
	 * @return false|WC_Product
	 */
	public function get_product_object() {
		if ( empty( $this->step_data['data']['products']['list'] ) || count( $this->step_data['data']['products']['list'] ) === 0 ) {
			return false;
		}

		$products   = $this->step_data['data']['products']['list'];
		$product_id = key( $products );

		return wc_get_product( $product_id );
	}

	/**
	 * Only show the widgets when the page is an upsell page.
	 *
	 * @return bool
	 */
	public function show_in_panel() {
		if ( empty( $this->step_data['type']['key'] ) ) {
			return false;
		}

		if ( 'upsell' !== $this->step_data['type']['key'] && 'downsell' !== $this->step_data['type']['key'] ) {
			return false;
		}

		return true;
	}
}
