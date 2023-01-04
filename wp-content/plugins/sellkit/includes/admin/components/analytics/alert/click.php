<?php

namespace Sellkit\Admin\Components\Analytics\Alert;

use Sellkit\Admin\Components\Analytics;
use Sellkit\Admin\Components\Analytics\Analytics_Base;
use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Components class.
 *
 * @since 1.1.0
 */
class Click extends Analytics_Base {

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 * @param int $rule_id Rule id.
	 */
	public function __construct( $rule_id ) {
		$this->target_id = $rule_id;

		$this->set_alert_details();
	}

	/**
	 * Set discount details.
	 *
	 * @since 1.1.0
	 */
	public function set_alert_details() {
		global $wpdb;

		$start_date = time() - ( 60 * 60 * 24 * Analytics::$date_range );

		$sellkit_prefix = Database::DATABASE_PREFIX;
		$alert_table    = "{$wpdb->prefix}{$sellkit_prefix}applied_alert";

		// phpcs:disable
		$alerts = $wpdb->get_results(
			$wpdb->prepare( "SELECT FROM_UNIXTIME(applied_at, '%%b_%%d') as `day`, SUM(click) as total FROM {$alert_table}
				where applied_at > {$start_date} and rule_id " . Analytics::target_id_condition( $this->target_id ) . " %d
				GROUP BY `day` ORDER BY `day` ASC", $this->target_id ),
			ARRAY_A );
		// phpcs:enable

		$prepared_alert = [];
		foreach ( $alerts as $alert ) {
			$prepared_alert[ $alert['day'] ] = intval( $alert['total'] );
		}

		$this->output = $prepared_alert;
	}
}
