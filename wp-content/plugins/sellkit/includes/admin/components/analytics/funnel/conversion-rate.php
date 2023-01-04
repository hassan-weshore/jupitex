<?php

namespace Sellkit\Admin\Components\Analytics\Funnel;

use Sellkit\Admin\Components\Analytics;
use Sellkit\Admin\Components\Analytics\Analytics_Base;
use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Conversion Rate class.
 *
 * @since 1.1.0
 */
class Conversion_Rate extends Analytics_Base {

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 * @param int $funnel_id Rule id.
	 */
	public function __construct( $funnel_id ) {
		$this->target_id = $funnel_id;

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
		$funnel_table   = "{$wpdb->prefix}{$sellkit_prefix}applied_funnel";

		// phpcs:disable
		$results = $wpdb->get_results(
			$wpdb->prepare( "SELECT FROM_UNIXTIME(applied_at, '%%b_%%d') as `day`,
       			SUM(is_started_number) as total_started, SUM(is_finished_number) as total_finished
       			FROM {$funnel_table}
				where applied_at > {$start_date} and funnel_id " . Analytics::target_id_condition( $this->target_id ) . " %d
				GROUP BY `day` ORDER BY `day` ASC;", $this->target_id ),
			ARRAY_A );
		// phpcs:enable

		$neat_data = [];

		foreach ( $results as $result ) {
			$conversion_rate = ( ( $result['total_finished'] * 100 ) / $result['total_started'] );

			$neat_data[ $result['day'] ] = floatval( number_format( $conversion_rate, 2 ) );
		}

		$this->output = $neat_data;
	}
}
