<?php

namespace Sellkit_Pro\Contact_Segmentation;

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

		add_filter( 'sellkit_contact_segmentation_conditions', [ $this, 'get_contact_segmentation_conditions' ] );
		add_filter( 'sellkit_contact_segmentation_conditions_data', [ $this, 'get_contact_segmentation_conditions_data' ] );
	}

	/**
	 * Loads all of the conditions.
	 *
	 * @since 1.1.0
	 */
	public function load_conditions() {
		sellkit_pro()->load_files( [
			'contact-segmentation/conditions/condition-base',
		] );

		$path       = trailingslashit( sellkit_pro()->plugin_dir() . 'includes/contact-segmentation/conditions' );
		$file_paths = glob( $path . '*.php' );

		foreach ( $file_paths as $file_path ) {
			if ( ! file_exists( $file_path ) ) {
				continue;
			}

			require_once $file_path;

			$file_name       = str_replace( '.php', '', basename( $file_path ) );
			$condition_class = str_replace( '-', ' ', $file_name );
			$condition_class = str_replace( ' ', '_', ucwords( $condition_class ) );
			$condition_class = "Sellkit_Pro\Contact_Segmentation\Conditions\\{$condition_class}";

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
			];
		}
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
	 * Get condition's options.
	 *
	 * @since 1.1.0
	 */
	public function get_options() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$condition = sellkit_htmlspecialchars( INPUT_GET, 'condition' );

		if ( empty( $condition ) ) {
			wp_send_json_error( __( 'Please send a condition name', 'sellkit-pro' ) );
		}

		$options = $this->get_condition( $condition )->get_options();

		wp_send_json_success( $options );
	}


	/**
	 * Gets contact seg conditions.
	 *
	 * @since 1.1.0
	 * @param array $conditions Conditions.
	 * @return array
	 */
	public function get_contact_segmentation_conditions( $conditions ) {
		return array_merge( $conditions, self::$conditions );
	}

	/**
	 * Gets contact seg condition's data.
	 *
	 * @since 1.1.0
	 * @param array $data Data.
	 * @return array
	 */
	public function get_contact_segmentation_conditions_data( $data ) {
		return array_merge( $data, self::$data );
	}
}
