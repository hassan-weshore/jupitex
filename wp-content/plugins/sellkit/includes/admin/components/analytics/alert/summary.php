<?php

namespace Sellkit\Admin\Components\Analytics\Alert;

use Sellkit\Admin\Components\Analytics;
use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Components class.
 *
 * @since 1.1.0
 */
class Summary {

	/**
	 * All valid discounts.
	 *
	 * @var $data array Discounts.
	 */
	public $data;

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var Summary
	 */
	private static $instance = null;

	/**
	 * Gets class instance.
	 *
	 * @since 1.1.0
	 * @return Summary
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
		$this->set_alert_details();
	}

	/**
	 * Set discount details.
	 *
	 * @since 1.1.0
	 */
	public function set_alert_details() {
		global $wpdb;

		$rule_id = sellkit_htmlspecialchars( INPUT_GET, 'target_id' );

		$start_date = time() - ( 60 * 60 * 24 * Analytics::$date_range );

		$sellkit_prefix = Database::DATABASE_PREFIX;
		$alert_table    = "{$wpdb->prefix}{$sellkit_prefix}applied_alert";

		// phpcs:disable
		$query_result = $wpdb->get_results(
			$wpdb->prepare( "SELECT SUM(impression) as impression, SUM(click) as click FROM {$alert_table}
				where applied_at > {$start_date} and rule_id " . Analytics::target_id_condition( $rule_id ) . " %d", $rule_id ),
			ARRAY_A );

		$this->data['impression'] = ! empty( $query_result[0]['impression'] ) ? $query_result[0]['impression'] : 0;
		$this->data['click']      = ! empty( $query_result[0]['click'] ) ? $query_result[0]['click'] : 0;
		// phpcs:enable

		$this->data['target_title'] = get_the_title( $rule_id );
	}
}
