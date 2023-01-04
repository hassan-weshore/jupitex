<?php

use Sellkit_Pro\Elementor\Base\Sellkit_Elementor_Base_Module;

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Personalised_Coupons_Module extends Sellkit_Elementor_Base_Module {

	public static function is_active() {
		return function_exists( 'WC' );
	}

	public function get_widgets() {
		return [ 'personalised-coupons' ];
	}
}
