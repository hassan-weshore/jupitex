<?php

class Customer_Full_Name extends Tag_Base {
	/**
	 * Get class id.
	 *
	 * @return string
	 */
	public function get_id() {
		return '_customer_full_name';
	}

	/**
	 * Get class title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Customer Full Name', 'sellkit' );
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

		$full_name = [];

		$first_name = isset( $order_data['billing']['first_name'] ) ? $order_data['billing']['first_name'] : '';
		$last_name  = isset( $order_data['billing']['last_name'] ) ? $order_data['billing']['last_name'] : '';

		$full_name = [ $first_name, $last_name ];

		if ( empty( $first_name ) && empty( $last_name ) ) {
			return $this->shortcode_content( $atts );
		}

		return implode( ' ', $full_name );
	}
}
