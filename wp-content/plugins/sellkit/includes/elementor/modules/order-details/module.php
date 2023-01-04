<?php

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Order_Details_Module extends Sellkit_Elementor_Base_Module {

	public static $field_types = [];

	public function __construct() {
		parent::__construct();

		$this->register_field_types();

	}

	public static function is_active() {
		return function_exists( 'WC' );
	}

	/**
	 * Register module widgets.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_widgets() {
		return [ 'order-details' ];
	}

	public static function get_field_types() {
		return [
			'order_number' => esc_html__( 'Order Number', 'sellkit' ),
			'order_status' => esc_html__( 'Order Status', 'sellkit' ),
			'payment_method' => esc_html__( 'Payment Method', 'sellkit' ),
			'contact_details' => esc_html__( 'Contact Details', 'sellkit' ),
			'shipping_method' => esc_html__( 'Shipping Method', 'sellkit' ),
			'trachking_number' => esc_html__( 'Tracking Number', 'sellkit' ),
			'shipping_address' => esc_html__( 'Shipping Address', 'sellkit' ),
			'bank_transfer' => esc_html__( 'Bank Details', 'sellkit' ),
		];
	}

	public static function render_field( $widget, $field ) {
		self::$field_types[ $field['type'] ]->render( $widget, $field );
	}

	/**
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	private function register_field_types() {
		foreach ( self::get_field_types() as $field_key => $field_value ) {
			$class_name = 'Sellkit\Elementor\Modules\Order_Details\Items\\' . $field_key;

			self::$field_types[ $field_key ] = new $class_name();
		}
	}
}
