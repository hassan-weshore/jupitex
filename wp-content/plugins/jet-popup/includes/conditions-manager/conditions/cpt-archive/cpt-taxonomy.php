<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class CPT_Taxonomy {

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

		if ( in_array( 'all', $value )) {
			return __( 'All', 'jet-popup' );
		}

		foreach ( $value as $term_id ) {
			$obj = get_term( $term_id );
			$result[] = $obj->name;
		}

		return implode( ', ', $result );
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $arg = '', $subgroup = false ) {

		$taxonomy = str_replace( 'cpt-taxonomy-', '', $subgroup );

		if ( in_array( 'all', $arg ) || empty( $arg ) ) {
			return is_tax( $taxonomy, '' );
		}

		foreach ( $arg as $id ) {
			$category_obj = get_term_by( 'id', $id, $taxonomy );

			if ( ! $category_obj ) {
				continue;
			}

			$is_category = is_tax( $taxonomy, $category_obj->slug );

			if ( $is_category ) {
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
