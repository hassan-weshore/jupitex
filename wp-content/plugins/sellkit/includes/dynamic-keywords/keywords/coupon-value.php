<?php

class Coupon_Value extends Tag_Base {
	/**
	 * Get class id.
	 *
	 * @return string
	 */
	public function get_id() {
		return '_coupon_value';
	}

	/**
	 * Get class title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Coupon Value', 'sellkit' );
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

		$order_data = $this->get_coupon_data( self::$order );

		if ( empty( $order_data ) ) {
			return $this->shortcode_content( $atts );
		}

		return $order_data;
	}

	/**
	 * Get coupon data.
	 *
	 * @since 1.1.0
	 * @param object $order object of order parameters.
	 * @return array
	 */
	public function get_coupon_data( $order ) {
		if ( empty( $order->get_items( 'coupon' ) ) ) {
			return;
		}

		foreach ( $order->get_items( 'coupon' ) as $coupon_id => $coupon ) {
			$current_coupon_obj = get_page_by_title( $coupon->get_name(), OBJECT, 'shop_coupon' );
			$current_coupon_id  = $current_coupon_obj->ID;
			$current_coupon     = new WC_Coupon( $current_coupon_id );

			$order_data = $current_coupon->get_data();
		}

		$indicator = '%';
		$amount    = $order_data['amount'] . '' . $indicator;

		if ( 'percent' !== $order_data['discount_type'] ) {
			$amount = wc_price( $order_data['amount'] );
		}

		return $amount;
	}
}
