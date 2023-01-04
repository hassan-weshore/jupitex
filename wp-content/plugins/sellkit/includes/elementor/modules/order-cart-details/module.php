<?php

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Order_Cart_Details_Module extends Sellkit_Elementor_Base_Module {

	public static function is_active() {
		return class_exists( 'woocommerce' );
	}

	public function get_widgets() {
		return [ 'order-cart-details' ];
	}
}
