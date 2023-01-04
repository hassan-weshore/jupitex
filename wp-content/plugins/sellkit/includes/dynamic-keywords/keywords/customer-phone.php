<?php

class Customer_Phone extends Tag_Base {
	/**
	 * Get class id.
	 *
	 * @return string
	 */
	public function get_id() {
		return '_customer_phone';
	}

	/**
	 * Get class title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Customer Phone', 'sellkit' );
	}

	/**
	 * Render true content.
	 *
	 * @param array $atts array of shortcode arguments.
	 * @return string
	 */
	public function render_content( $atts ) {
		$this->get_data();

		if ( empty( self::$order ) ) {
			return $this->shortcode_content( $atts );
		}

		$order_data = self::$order->get_data();

		if ( empty( $order_data['billing']['phone'] ) ) {
			return $this->shortcode_content( $atts );
		}

		return $order_data['billing']['phone'];
	}
}
