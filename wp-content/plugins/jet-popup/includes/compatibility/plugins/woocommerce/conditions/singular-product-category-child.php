<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Woo_Singular_Product_Category_Child extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'singular-product-category-child';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'In Child Product Categories', 'jet-popup' );
	}

	/**
	 * Condition group
	 *
	 * @return string
	 */
	public function get_group() {
		return 'woocommerce';
	}

	/**
	 * @return string
	 */
	public function get_sub_group() {
		return 'woocommerce-single';
	}

	/**
	 * @return int
	 */
	public  function get_priority() {
		return 28;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_single_product';
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
			'action' => 'get-product-categories',
			'params' => [],
		];
	}

	/**
	 * [get_label_by_value description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_label_by_value( $value = '' ) {
		return get_term_by( 'id', $value, 'product_cat' )->name;
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

		if ( 'all' === $arg ) {
			return has_term( [], 'product_cat', $post );
		}

		$terms = get_the_terms( $post, 'product_cat' );

		if ( empty( $terms ) ) {
			return false;
		}

		$term_ids = wp_list_pluck( $terms, 'term_id' );

		if ( in_array( $arg, $term_ids ) ) {
			return false;
		}

		$termchildren = get_term_children( $arg, 'product_cat' );

		foreach ( $termchildren as $child ) {
			$child_obj = get_term_by( 'id', $child, 'product_cat' );

			if ( $arg === $child_obj->parent && in_array( $child, $term_ids )  ) {
				return true;
			}
		}

		return false;
	}

}
