<?php

namespace Sellkit\Dynamic_Keywords\Contact_Segmentation;

/**
 * Class Remaining subtoal amount.
 *
 * @package Sellkit\Dynamic_Keywords\Contact_Segmentation
 * @since 1.1.0
 */
class Remaining_Subtotal_Amount extends Contact_Segmentation_Base {

	/**
	 * Constructor.
	 *
	 * @since 1.1.0
	 * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get class id.
	 *
	 * @return string
	 */
	public function get_id() {
		return '_remaining_subtotal_amount';
	}

	/**
	 * Get class title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Remaining subtotal amount', 'sellkit' );
	}

	/**
	 * Render content.
	 *
	 * @param array $atts array of shortcode arguments.
	 * @return string
	 */
	public function render_content( $atts ) {
		if ( empty( WC()->session->cart_totals ) ) {
			return $this->shortcode_content( $atts );
		}

		$cart_total = WC()->session->cart_totals;
		$value      = $this->get_content_meta( 'cart-subtotal' );

		if ( empty( $value ) || ! is_array( $value ) ) {
			return $this->shortcode_content( $atts );
		}

		$theshold = 0;

		if ( ! isset( $value['threshold_range'] ) || ! isset( $value['target_subtotal_amount'] ) ) {
			return $this->shortcode_content( $atts );
		}

		if ( $cart_total['subtotal'] > $value['threshold_range'] && $cart_total['subtotal'] < $value['target_subtotal_amount'] ) {
			$theshold = $value['target_subtotal_amount'] - $cart_total['subtotal'];
		}

		return $theshold;
	}
}
