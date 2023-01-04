<?php

namespace Sellkit\Admin\Components\Analytics;

use Sellkit\Admin\Components\Analytics;

defined( 'ABSPATH' ) || die();

/**
 * Components class.
 *
 * @since 1.1.0
 */
abstract class Analytics_Base {

	/**
	 * Output data.
	 *
	 * @var $output array Analytics data.
	 */
	public $output;

	/**
	 * Target id.
	 *
	 * @var $target_id int Target id.
	 */
	public $target_id;

	/**
	 * Getting chart data.
	 *
	 * @since 1.1.0
	 */
	public function get_data() {
		$chart_data = [];

		for ( $i = Analytics::$date_range; $i >= 0; $i-- ) {
			$time = strtotime( "-{$i} days" );
			$date = date( 'M_d', $time );

			$chart_data[] = [
				'date' => str_replace( '_', ' ', $date ),
				'value' => ! empty( $this->output[ $date ] ) ? floatval( $this->output[ $date ] ) : 0,
			];
		}

		return $chart_data;
	}
}
