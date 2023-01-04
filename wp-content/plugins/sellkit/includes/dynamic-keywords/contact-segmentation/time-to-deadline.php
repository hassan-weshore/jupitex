<?php

namespace Sellkit\Dynamic_Keywords\Contact_Segmentation;

/**
 * Class Time to deadline.
 *
 * @package Sellkit\Dynamic_Keywords\Contact_Segmentation
 * @since 1.1.0
 */
class Time_To_Deadline extends Contact_Segmentation_Base {

	/**
	 * Constructor.
	 *
	 * @since 1.1.0
	 * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get class id.
	 *
	 * @return string
	 */
	public function get_id() {
		return '_time_to_deadline';
	}

	/**
	 * Get class title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Time to Deadline', 'sellkit' );
	}

	/**
	 * Render content.
	 *
	 * @param array $atts array of shortcode arguments.
	 * @return string
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function render_content( $atts ) {
		$values = $this->get_content_meta( 'time-deadline' );

		if (
			empty( $values ) ||
			( empty( $values['time_deadline'] ) && empty( $values['day_deadline'] ) )
		) {
			return $this->shortcode_content( $atts );
		}

		$time_deadline = strtotime( $values['time_deadline'] );
		$current_time  = strtotime( current_time( 'H:i' ) );

		$result = [];

		if ( ! in_array( strtolower( date( 'l' ) ), $values['day_deadline'], true ) ) {
			return;
		}

		if ( $current_time > $time_deadline ) {
			return;
		}

		date_default_timezone_set( 'UTC' ); // phpcs:ignore

		$remain_hour = date( 'H', $time_deadline ) - date( 'H', $current_time );
		$hour_string = 1 === $remain_hour ? esc_html__( 'Hour', 'sellkit' ) : esc_html__( 'Hours', 'sellkit' );

		$remain_mins = date( 'i', $time_deadline ) - date( 'i', $current_time );
		$mins_string = 1 === $remain_mins ? esc_html__( 'Minute', 'sellkit' ) : esc_html__( 'Minutes', 'sellkit' );

		if ( $remain_mins < 0 ) {
			$remain_mins = 60 + $remain_mins;
			--$remain_hour;
		}

		if ( $remain_hour > 0 ) {
			$result['hour'] = $remain_hour . ' ' . $hour_string;
		}

		if ( $remain_mins > 0 ) {
			$result['mins'] = $remain_mins . ' ' . $mins_string;
		}

		return implode( ' - ', $result );
	}
}
