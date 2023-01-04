<?php

namespace Sellkit\Contact_Segmentation;

defined( 'ABSPATH' ) || die();

/**
 * Class Conditions
 *
 * @package Sellkit\Contact_Segmentation\Base
 * @since 1.1.0
 */
class Conditions {

	/**
	 * Operations array, the key is the conditions name and value is the operators name.
	 *
	 * @var array
	 * @since 1.1.0
	 */
	public static $operators = [];

	/**
	 * The key is the conditions name and value is the condition's data.
	 *
	 * @var array
	 * @since 1.1.0
	 */
	public static $data = [];

	/**
	 * Conditions instances, the key is conditions name the value is the condition instance.
	 *
	 * @var array
	 * @since 1.1.0
	 */
	public static $conditions = [];

	/**
	 * Conditions constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$this->load_conditions();

		add_action( 'wp_ajax_sellkit_conditions_get_options', [ $this, 'get_options' ] );
	}

	/**
	 * Loads all of the conditions.
	 *
	 * @since 1.1.0
	 */
	public function load_conditions() {
		sellkit()->load_files( [
			'contact-segmentation/conditions/condition-base',
		] );

		$path       = trailingslashit( sellkit()->plugin_dir() . 'includes/contact-segmentation/conditions' );
		$file_paths = glob( $path . '*.php' );

		foreach ( $file_paths as $file_path ) {
			if ( ! file_exists( $file_path ) ) {
				continue;
			}

			require_once $file_path;

			$file_name       = str_replace( '.php', '', basename( $file_path ) );
			$condition_class = str_replace( '-', ' ', $file_name );
			$condition_class = str_replace( ' ', '_', ucwords( $condition_class ) );
			$condition_class = "Sellkit\Contact_Segmentation\Conditions\\{$condition_class}";

			if ( ! class_exists( $condition_class ) || 'condition-base' === $file_name ) {
				continue;
			}

			$condition = new $condition_class();

			if ( false === $condition->is_active() ) {
				continue;
			}

			self::$conditions[ $condition->get_name() ] = $condition;
			self::$data[ $condition->get_name() ]       = [
				'title'        => $condition->get_title(),
				'type'         => $condition->get_type(),
				'isSearchable' => $condition->is_searchable(),
				'openMenuOnClick' => method_exists( $condition, 'open_menu_on_click' ) ? $condition->open_menu_on_click() : false,
			];
		}

		self::$conditions = apply_filters( 'sellkit_contact_segmentation_conditions', self::$conditions );
		self::$data       = apply_filters( 'sellkit_contact_segmentation_conditions_data', self::$data );
	}

	/**
	 * Getting all conditions names.
	 *
	 * @return array
	 * @since 1.1.0
	 */
	public function get_names() {
		return self::$names;
	}

	/**
	 * Gets all operators based on the conditions name.
	 *
	 * @since 1.1.0
	 * @param string $condition_name Condition name.
	 * @return mixed
	 */
	public function get_condition( $condition_name ) {
		return self::$conditions[ $condition_name ];
	}

	/**
	 * Check condition validation.
	 *
	 * @since 1.1.0
	 * @param string $condition_name Condition name.
	 * @param string $operator_name       Condition operator.
	 * @param mixed  $condition_value Condition value.
	 * @return bool|void|\WP_Error
	 */
	public static function match( $condition_name, $operator_name, $condition_value ) {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}

		$condition       = self::$conditions[ $condition_name ];
		$operator        = Operators::$operators[ $operator_name ];
		$extracted_value = $condition->get_value();

		if ( ! is_user_logged_in() && ! isset( $extracted_value ) ) {
			return new \WP_Error( 'conditions_has_no_value', __( 'Condition has no value', 'sellkit' ) );
		}

		if ( method_exists( $condition, 'is_valid' ) ) {
			return $condition->is_valid( $condition_value, $operator_name );
		}

		if ( empty( $operator->is_valid( $condition->get_value(), $condition_value ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get condition's options.
	 *
	 * @since 1.1.0
	 */
	public function get_options() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$condition = sellkit_htmlspecialchars( INPUT_GET, 'condition' );

		if ( empty( $condition ) ) {
			wp_send_json_error( __( 'Please send a condition name', 'sellkit' ) );
		}

		$options = $this->get_condition( $condition )->get_options();

		wp_send_json_success( $options );
	}
}
