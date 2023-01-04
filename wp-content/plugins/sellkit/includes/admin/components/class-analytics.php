<?php

namespace Sellkit\Admin\Components;

use Sellkit\Admin\Components\Analytics\Discount\summary;

defined( 'ABSPATH' ) || die();

/**
 * Components class.
 *
 * @since 1.1.0
 */
class Analytics {

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var Analytics
	 */
	private static $instance = null;

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var $date_range
	 */
	public static $date_range;

	/**
	 * Get a class instance.
	 *
	 * @since 1.1.0
	 *
	 * @return Analytics Class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		self::$date_range = 7;

		add_action( 'wp_ajax_sellkit_get_chart_data', [ $this, 'get_chart_data' ] );
		add_action( 'wp_ajax_sellkit_get_summary_chart_data', [ $this, 'get_summary' ] );
	}

	/**
	 * Gets analytics data.
	 *
	 * @since 1.1.0
	 */
	public function get_chart_data() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$feature    = sellkit_htmlspecialchars( INPUT_GET, 'feature' );
		$class      = sellkit_htmlspecialchars( INPUT_GET, 'type' );
		$date_range = sellkit_htmlspecialchars( INPUT_GET, 'date_range' );
		$target_id  = sellkit_htmlspecialchars( INPUT_GET, 'target_id' );

		if ( 'thirty-days' === $date_range ) {
			self::$date_range = 30;
		}

		$file_name = str_replace( '_', '-', $class );

		sellkit()->load_files( [
			'admin/components/analytics/class',
			"admin/components/analytics/{$feature}/base",
			"admin/components/analytics/{$feature}/{$file_name}",
		] );

		$feature = ucfirst( $feature );
		$class   = __NAMESPACE__ . "\Analytics\\$feature\\" . $class;

		$analytics = new $class( $target_id );

		wp_send_json_success( $analytics->get_data() );
	}

	/**
	 * Gets summary data.
	 *
	 * @since 1.1.0
	 */
	public function get_summary() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$feature    = sellkit_htmlspecialchars( INPUT_GET, 'feature' );
		$date_range = sellkit_htmlspecialchars( INPUT_GET, 'date_range' );

		if ( 'thirty-days' === $date_range ) {
			self::$date_range = 30;
		}

		sellkit()->load_files( [ "admin/components/analytics/{$feature}/summary" ] );

		$class = __NAMESPACE__ . "\Analytics\\$feature\\summary";

		wp_send_json_success( $class::get_instance()->data );
	}

	/**
	 * The condition which is related to the target id.
	 *
	 * @since 1.1.0
	 * @return string
	 * @param int $target_id Target id.
	 */
	public static function target_id_condition( $target_id ) {
		$target_id_condition = '=';

		if ( empty( $target_id ) ) {
			$target_id_condition = '!=';
		}

		return $target_id_condition;
	}
}

Analytics::get_instance();
