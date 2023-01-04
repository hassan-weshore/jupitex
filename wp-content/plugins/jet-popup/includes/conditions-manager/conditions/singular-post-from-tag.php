<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Post_From_Tag extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'singular-post-from-tag';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'In Post tags', 'jet-popup' );
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
			return false;
		}

		if ( ! is_single() ) {
			return false;
		}

		global $post;

		if ( 'all' === $arg ) {
			return has_tag( '', $post );
		}

		return has_tag( $arg, $post );
	}

}
