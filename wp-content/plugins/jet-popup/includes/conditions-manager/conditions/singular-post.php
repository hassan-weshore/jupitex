<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Post extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'singular-post';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Posts', 'jet-popup' );
	}

	/**
	 * Condition group
	 *
	 * @return string
	 */
	public function get_group() {
		return 'singular';
	}

	/**
	 * @return string
	 */
	public function get_sub_group() {
		return 'post-singular';
	}

	/**
	 * @return int
	 */
	public  function get_priority() {
		return 30;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_single';
	}

	/**
	 * [get_control description]
	 * @return [type] [description]
	 */
	public function get_control() {
		return [
			'type'        => 'select',
			'placeholder' => __( 'Select post', 'jet-popup' ),
		];
	}

	/**
	 * [ajax_action description]
	 * @return [type] [description]
	 */
	public function ajax_action() {
		return [
			'action' => 'get-posts',
			'params' => []
		];
	}

	/**
	 * [get_label_by_value description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_label_by_value( $value = '' ) {
		return get_the_title( $value );
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $arg = '' ) {

		if ( empty( $arg ) || 'all' === $arg ) {
			return is_singular() && 'post' === get_post_type();
		}

		return is_single( $arg );
	}

}
