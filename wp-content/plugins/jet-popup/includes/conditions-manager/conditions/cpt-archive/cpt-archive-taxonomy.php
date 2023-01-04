<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class CPT_Archive_Taxonomy extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'archive-tax';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'CPT Taxonomy', 'jet-popup' );
	}

	/**
	 * Condition group
	 *
	 * @return string
	 */
	public function get_group() {
		return 'archive';
	}

	/**
	 * @return int
	 */
	public  function get_priority() {
		return 45;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_archive';
	}

	/**
	 * [get_control description]
	 * @return [type] [description]
	 */
	public function get_control() {
		return [
			'type'        => 'f-select',
			'placeholder' => __( 'Select Taxonomy', 'jet-popup' ),
		];
	}

	/**
	 * [get_avaliable_options description]
	 * @return [type] [description]
	 */
	public function get_avaliable_options() {
		return \Jet_Popup_Utils::get_taxonomies_options();
	}

	/**
	 * [get_label_by_value description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_label_by_value( $value = '' ) {

		if ( empty( $value ) ) {
			return '';
		}

		$value = ! is_array( $value ) ? explode(' ', $value ) : $value;

		$result = [];

		foreach ( $value as $taxonomy ) {
			$obj = get_taxonomy( $taxonomy );

			$result[] = $obj->label;
		}

		return implode( ', ', $result );
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $arg = '' ) {
		$queried_object = get_queried_object();

		if ( is_a( $queried_object, 'WP_Term' ) ) {
			$taxonomy = $queried_object->taxonomy;

			if ( in_array( $taxonomy, $arg ) ) {
				return true;
			}
		}

		return false;
	}

}
