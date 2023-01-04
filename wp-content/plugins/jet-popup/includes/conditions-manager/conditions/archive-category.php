<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Archive_Category extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'archive-category';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Post categories', 'jet-popup' );
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
	 * @return string
	 */
	public function get_sub_group() {
		return 'post-archive';
	}

	/**
	 * @return int
	 */
	public  function get_priority() {
		return 50;
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
			'placeholder' => __( 'Select category', 'jet-popup' ),
		];
	}

	/**
	 * [ajax_action description]
	 * @return [type] [description]
	 */
	public function ajax_action() {
		return [
			'action' => 'get-post-categories',
			'params' => []
		];
	}

	/**
	 * [get_label_by_value description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_label_by_value( $value = '' ) {

		if ( 'all' === $value ) {
			return __( 'All', 'jet-popup' );
		}

		$terms = get_terms( array(
			'include'    => $value,
			'taxonomy'   => 'category',
			'hide_empty' => false,
		) );

		$label = '';

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $key => $term ) {
				$label .= $term->name;
			}
		}

		return $label;
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $arg = '' ) {

		if ( empty( $arg ) || 'all' === $arg ) {
			return is_category();
		}

		return is_category( $arg );
	}

}
