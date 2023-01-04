<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Archive_Tag extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'archive-tag';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Post tags', 'jet-popup' );
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
	public function get_priority() {
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
			'placeholder' => __( 'Select tag', 'jet-popup' ),
		];
	}

	/**
	 * [ajax_action description]
	 * @return [type] [description]
	 */
	public function ajax_action() {
		return [
			'action' => 'get-post-tags',
			'params' => []
		];
	}

	/**
	 * [get_label_by_value description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_label_by_value( $value = '' ) {

		$terms = get_terms( array(
			'include'    => $value,
			'taxonomy'   => 'post_tag',
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

		if ( empty( $arg ) ) {
			return is_tag();
		}

		return is_tag( $arg );
	}

}
