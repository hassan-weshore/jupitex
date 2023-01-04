<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class CPT_Archive {

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
		$obj = get_post_type_object( $value );

		return $obj->labels->singular_name;
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

			if ( is_tax( $taxonomy, '' ) ) {
				return true;
			}
		}

		if ( is_a( $queried_object, 'WP_Post_Type' ) ) {
			$post_type = $queried_object->name;

			return is_post_type_archive( $post_type );
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
