<?php

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Product_Quantity_Module extends Sellkit_Elementor_Base_Module {
	public static function is_active() {
		return function_exists( 'WC' );
	}

	public function get_widgets() {
		return [ 'product-quantity' ];
	}
}
