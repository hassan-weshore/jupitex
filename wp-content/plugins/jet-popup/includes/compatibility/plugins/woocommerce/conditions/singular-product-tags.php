<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Woo_Singular_Product_Tags extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'singular-product-tags';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Product from Tag', 'jet-popup' );
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
		return 29;
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
			'type'        => 'f-search-select',
			'placeholder' => __( 'Select tag', 'jet-popup' ),
		];
	}

	/**
	 * [ajax_action description]
	 * @return [type] [description]
	 */
	public function ajax_action() {
		return [
			'action' => 'get-product-tags',
			'params' => [],
		];
	}

	/**
	 * [get_label_by_value description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_label_by_value( $value = '' ) {

		if ( in_array( 'all', $value ) ) {
			$result[] = __( 'All', 'jet-popup' );
		}

		foreach ( $value as $id ) {
			$result[] = get_term_by( 'id', $id, 'product_tag' )->name;
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

		if ( in_array( 'all', $arg ) ) {
			return has_term( [], 'product_cat', $post );
		}

		foreach ( $arg as $id ) {
			$tag_obj = get_term_by( 'id', $id, 'product_tag' );

			$is_product_tag = has_term( $tag_obj->slug, 'product_tag', $post );

			if ( $is_product_tag ) {
				return true;
			}
		}

		return false;
	}

}
