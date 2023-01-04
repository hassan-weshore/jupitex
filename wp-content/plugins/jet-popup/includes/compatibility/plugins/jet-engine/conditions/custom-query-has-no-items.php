<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Engine_Custom_Query_Has_No_Items extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'jet-engine-custom-query-has-no-items';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Query Has No Items', 'jet-popup' );
	}

	/**
	 * Condition group
	 *
	 * @return string
	 */
	public function get_group() {
		return 'jet-engine';
	}

	/**
	 * @return string
	 */
	public function get_sub_group() {
		return 'jet-engine-custom-query';
	}

	/**
	 * @return int
	 */
	public function get_priority() {
		return 100;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_page';
	}

	/**
	 * [get_control description]
	 * @return [type] [description]
	 */
	public function get_control() {
		return [
			'type'        => 'select',
			'placeholder' => __( 'Select query', 'jet-popup' ),
		];
	}

	/**
	 * @return array|false
	 */
	public function get_avaliable_options() {
		return \Jet_Engine\Query_Builder\Manager::instance()->get_queries_for_options( true, null, true );
	}

	/**
	 * [get_label_by_value description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_label_by_value( $value = '' ) {
		$query = \Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $value );

		if ( ! $query ) {
			return __( 'Query not found', 'jet-popup' );
		}

		return $query->name;
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $args ) {

		if ( empty( $args ) ) {
			return false;
		}

		$has_no_items = false;

		$query = \Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $args );

		if ( $query ) {
			$has_no_items = ! $query->has_items();
		}

		return $has_no_items;
	}

}
