<?php

defined( 'ABSPATH' ) || die();

use Sellkit\Elementor\Modules\Checkout\Classes\Global_Hooks;

class Sellkit_Elementor_Checkout_Module extends Sellkit_Elementor_Base_Module {

	public function get_widgets() {
		return [ 'checkout' ];
	}

	public function __construct() {
		parent::__construct();

		new Global_Hooks();
	}

	public static function is_active() {
		return function_exists( 'WC' );
	}

	public static function templates_path() {
		return plugin_dir_path( __FILE__ );
	}
}
