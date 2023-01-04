<?php

class Coupon_Expiry_Date extends Tag_Base {
	/**
	 * Get class id.
	 *
	 * @return string
	 */
	public function get_id() {
		return '_coupon_expiry_date';
	}

	/**
	 * Get class title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Coupon Expiry Date', 'sellkit' );
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

		$order_data = $this->get_coupon_expire_date_data( self::$order );

		if ( empty( $order_data ) ) {
			return $this->shortcode_content( $atts );
		}

		return $order_data;
	}

	/**
	 * Get coupon expire date data.
	 *
	 * @since 1.1.0
	 * @param object $order object of order parameters.
	 * @return array
	 */
	public function get_coupon_expire_date_data( $order ) {
		if ( empty( $order->get_items( 'coupon' ) ) ) {
			return;
		}

		foreach ( $order->get_items( 'coupon' ) as $coupon_id => $coupon ) {
			$current_coupon_obj = get_page_by_title( $coupon->get_name(), OBJECT, 'shop_coupon' );
			$current_coupon_id  = $current_coupon_obj->ID;
			$current_coupon     = new WC_Coupon( $current_coupon_id );

			if ( empty( $current_coupon->get_date_expires() ) ) {
				continue;
			}

			$order_data = $current_coupon->get_date_expires()->date( 'Y-m-d H:i:s' );
		}

		return $order_data;
	}
}
