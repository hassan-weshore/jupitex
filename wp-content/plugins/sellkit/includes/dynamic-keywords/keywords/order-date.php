<?php

class Order_Date extends Tag_Base {
	/**
	 * Get class id.
	 *
	 * @return string
	 */
	public function get_id() {
		return '_order_date';
	}

	/**
	 * Get class title.
	 *
	 * @param array $atts array of shortcode arguments.
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Order Date', 'sellkit' );
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

		$order_data = self::$order->get_data();

		return $order_data['date_created']->date( 'Y-m-d' );
	}
}
