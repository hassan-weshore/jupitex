<?php

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Accept_Reject_Button_Module extends Sellkit_Elementor_Base_Module {

	public static function is_active() {
		return function_exists( 'WC' );
	}

	public function get_widgets() {
		return [ 'accept-reject-button' ];
	}
}
