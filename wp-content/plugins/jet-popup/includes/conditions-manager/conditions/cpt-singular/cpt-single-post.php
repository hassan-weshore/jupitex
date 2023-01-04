<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class CPT_Single_Post {

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

		if ( in_array( 'all', $value ) ) {
			return __( 'All', 'jet-popup' );
		}

		$result = [];

		if ( is_array( $value ) ) {

			foreach ( $value as $post_id ) {
				$obj = get_post( $post_id );

				if ( isset( $obj ) ) {
					$result[] = $obj->post_title;
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
	public function check( $arg = '', $sub_group = false ) {

		$post_type = get_post_type();
		$custom_post_type = str_replace('cpt-single-', '', $sub_group );

		if ( empty( $arg ) ) {
			return is_singular( [ $post_type ] );
		}

		if ( in_array( 'all', $arg ) ) {
			return is_singular( [ $post_type ] ) && $post_type === $custom_post_type;
		}

		foreach ( $arg as $id ) {
			$is_single = is_single( $id );

			if ( $is_single ) {
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
