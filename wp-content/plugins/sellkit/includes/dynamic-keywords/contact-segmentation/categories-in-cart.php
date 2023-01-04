<?php

namespace Sellkit\Dynamic_Keywords\Contact_Segmentation;

/**
 * Class Categeory in cart.
 *
 * @package Sellkit\Dynamic_Keywords\Contact_Segmentation
 * @since 1.1.0
 */
class Categories_In_Cart extends Contact_Segmentation_Base {

	/**
	 * Constructor.
	 *
	 * @since 1.1.0
	 * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get class id.
	 *
	 * @return string
	 */
	public function get_id() {
		return '_categories_in_cart';
	}

	/**
	 * Get class title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Categories in cart', 'sellkit' );
	}

	/**
	 * Render content.
	 *
	 * @param array $atts array of shortcode arguments.
	 * @return string
	 */
	public function render_content( $atts ) {
		if ( empty( self::$cart_items ) ) {
			return $this->shortcode_content( $atts );
		}

		$categories = [];

		foreach ( self::$cart_items as $product ) {
			$terms = wp_get_post_terms( $product['product_id'], 'product_cat', [ 'fields' => 'names' ] );
			$count = count( $terms );

			if ( $count < 2 ) {
				$categories[] = isset( $terms[0] ) ? $terms[0] : '';
			}

			if ( $count > 1 ) {
				foreach ( $terms as $term ) {
					$categories[] = $term;
				}
			}
		}

		$categories = $this->get_result( array_unique( $categories ) );

		return implode( ', ', $categories );
	}
}
