<?php

namespace Sellkit\Dynamic_Keywords\Contact_Segmentation;

/**
 * Class Products in cart.
 *
 * @package Sellkit\Dynamic_Keywords\Contact_Segmentation
 * @since 1.1.0
 */
class Products_In_Cart extends Contact_Segmentation_Base {

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
		return '_products_in_cart';
	}

	/**
	 * Get class title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Products in Cart', 'sellkit' );
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

		$products = [];

		foreach ( self::$cart_items as $product ) {
			$products[] = get_the_title( $product['product_id'] );
		}

		$products = $this->get_result( $products );

		return implode( ', ', $products );
	}
}
