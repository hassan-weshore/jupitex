<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Woo_Product_Tags extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'woo-product-tags';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Product Tags', 'jet-popup' );
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
		return 'woocommerce-archive';
	}

	/**
	 * @return int
	 */
	public  function get_priority() {
		return 8;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_products_archive';
	}

	/**
	 * [get_control description]
	 * @return [type] [description]
	 */
	public function get_control() {
		return [
			'type'        => 'f-search-select',
			'placeholder' => __( 'Select page', 'jet-popup' ),
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
	public function check( $arg ) {

		if ( empty( $arg ) ) {
			return is_product_tag();
		}

		if ( in_array( 'all', $arg ) ) {
			return is_product_tag();
		}

		foreach ( $arg as $id ) {
			$tag_obj = get_term_by( 'id', $id, 'product_tag' );

			$is_product_tag = is_product_tag( $tag_obj->slug );

			if ( $is_product_tag ) {
				return true;
			}
		}

		return false;
	}

}
