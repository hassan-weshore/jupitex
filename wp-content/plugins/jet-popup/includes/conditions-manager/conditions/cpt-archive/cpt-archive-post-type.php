<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class CPT_Archive_Post_Type extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'archive-post-type';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'CPT Archive', 'jet-popup' );
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
			'type'        => 'select',
			'placeholder' => __( 'Select post type', 'jet-popup' ),
		];
	}

	/**
	 * [ajax_action description]
	 * @return [type] [description]
	 */
	public function ajax_action() {
		return [
			'action' => 'get-post-types',
			'params' => []
		];
	}

	/**
	 * [get_label_by_value description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_label_by_value( $value = '' ) {

		if ( empty( $value ) ) {
			return __( 'Not selected', 'jet-popup' );
		}

		$obj = get_post_type_object( $value );

		return $obj->labels->singular_name;
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $arg = '' ) {

		if ( empty( $arg ) ) {
			return is_post_type_archive();
		}

		if ( 'post' === $arg && 'post' === get_post_type() ) {
			return is_archive() || is_home();
		}

		return is_post_type_archive( $arg ) || ( is_tax() && $arg === get_post_type() );
	}

}
