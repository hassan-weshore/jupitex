<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class CPT_Single_Post_Term {

	/**
	 * @var array|object
	 */
	public $args = [];

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->args['id'];
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return $this->args['label'];
	}

	/**
	 * Condition group
	 *
	 * @return string
	 */
	public function get_group() {
		return $this->args['group'];
	}

	/**
	 * @return mixed
	 */
	public function get_sub_group() {
		return $this->args['sub_group'];
	}

	/**
	 * @return int
	 */
	public function get_priority() {
		return $this->args['priority'];
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return $this->args['body_structure'];
	}

	/**
	 * [get_control description]
	 * @return [type] [description]
	 */
	public function get_control() {
		return $this->args['value_control'];
	}

	/**
	 * [ajax_action description]
	 * @return [type] [description]
	 */
	public function ajax_action() {
		return $this->args['ajax_action'];
	}

	/**
	 * @return mixed
	 */
	public function get_avaliable_options() {
		return $this->args['value_options'];
	}

	/**
	 * [get_label_by_value description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_label_by_value( $value = '' ) {
		$result = [];

		if ( is_array( $value ) ) {

			if ( in_array( 'all', $value ) ) {
				return __( 'All', 'jet-popup' );
			}

			foreach ( $value as $term_id ) {

				if ( empty( $term_id ) ) {
					continue;
				}

				$obj = get_term( $term_id );

				if ( isset( $obj ) ) {
					$result[] = $obj->name;
				}
			}
		}

		return implode( ', ', $result );
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $arg = '' ) {

		if ( ! is_single() ) {
			return false;
		}

		global $post;

		$taxonomy = str_replace('cpt-post-term-', '', $this->args['id'] );

		if ( in_array( 'all', $arg ) ) {
			return has_term( [], $taxonomy, $post );
		}

		foreach ( $arg as $id ) {

			if ( empty( $id ) ) {
				continue;
			}

			$term_obj = get_term( $id );
			$is_term = has_term( $term_obj->slug, $term_obj->taxonomy, $post );

			if ( $is_term ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * CPT_Archive constructor.
	 *
	 * @param array $arg
	 */
	public function __construct( $arg = [] ) {
		$default_args = [
			'id'             => false,
			'label'          => false,
			'group'          => false,
			'sub_group'      => false,
			'priority'       => 100,
			'body_structure' => 'page',
			'value_control'  => false,
			'value_options'  => false,
			'ajax_action'    => false,
		];

		$this->args = wp_parse_args( $arg, $default_args );
	}

}
