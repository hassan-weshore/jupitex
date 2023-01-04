<?php

namespace Sellkit\Contact_Segmentation;

defined( 'ABSPATH' ) || die();

/**
 * Class Contact_Segmentation
 *
 * @package Sellkit\Contact_Segmentation
 * @since 1.1.0
 */
class Operators {

	/**
	 * Operator names.
	 *
	 * @var array
	 * @since 1.1.0
	 */
	public static $names = [];

	/**
	 * Operator names.
	 *
	 * @var array
	 * @since 1.1.0
	 */
	public static $condition_operator_names = [];

	/**
	 * Operator names.
	 *
	 * @var array
	 * @since 1.1.0
	 */
	public static $operators = [];

	/**
	 * Conditions constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$this->load_operators();
	}

	/**
	 * Loads all of the conditions.
	 *
	 * @since 1.1.0
	 */
	public function load_operators() {
		sellkit()->load_files( [
			'contact-segmentation/operators/operator-base',
		] );

		$path       = trailingslashit( sellkit()->plugin_dir() . 'includes/contact-segmentation/operators' );
		$file_paths = glob( $path . '*.php' );

		foreach ( $file_paths as $file_path ) {
			if ( ! file_exists( $file_path ) ) {
				continue;
			}

			require_once $file_path;

			$file_name      = str_replace( '.php', '', basename( $file_path ) );
			$operator_class = str_replace( '-', ' ', $file_name );
			$operator_class = str_replace( ' ', '_', ucwords( $operator_class ) );
			$operator_class = "Sellkit\Contact_Segmentation\Operators\\{$operator_class}";

			if ( ! class_exists( $operator_class ) || 'operator-base' === $file_name ) {
				continue;
			}

			$operator = new $operator_class();

			$new_conditions                           = array_fill_keys( $operator->get_conditions(), $operator->get_name() );
			self::$condition_operator_names           = array_merge_recursive( self::$condition_operator_names, $new_conditions );
			self::$names[ $operator->get_name() ]     = $operator->get_title();
			self::$operators[ $operator->get_name() ] = $operator;
		}
	}
}

new Operators();
