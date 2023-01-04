<?php

class Order_Shipping_Method extends Tag_Base {
	/**
	 * Get class id.
	 *
	 * @param array $atts array of shortcode arguments.
	 * @return string
	 */
	public function get_id() {
		return '_order_shipping_method';
	}

	/**
	 * Get class title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Order Shipping Method', 'sellkit' );
	}

	/**
	 * Render true content.
	 *
	 * @return string
	 */
	public function render_content( $atts ) {
		$this->get_data();

		if ( empty( self::$order ) ) {
			return $this->shortcode_content( $atts );
		}

		return self::$order->get_shipping_method();
	}
}
