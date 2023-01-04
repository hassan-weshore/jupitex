<?php

namespace Sellkit\Dynamic_Keywords\Contact_Segmentation;

/**
 * Class Viewed Categories.
 *
 * @package Sellkit\Dynamic_Keywords\Contact_Segmentation
 * @since 1.1.0
 */
class Viewed_Categories extends Contact_Segmentation_Base {

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
		return '_viewed_categories';
	}

	/**
	 * Get class title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Viewed Categories', 'sellkit' );
	}

	/**
	 * Render content.
	 *
	 * @param array $atts array of shortcode arguments.
	 * @return string
	 */
	public function render_content( $atts ) {
		if ( empty( self::$contact_segmentation ) ) {
			$this->get_data();
		}

		if ( ! isset( self::$contact_segmentation['viewed_category'] ) ) {
			return $this->shortcode_content( $atts );
		}

		$categories = self::$contact_segmentation['viewed_category'];

		if ( empty( $categories ) ) {
			return $this->shortcode_content( $atts );
		}

		$result = [];

		foreach ( $categories as $category ) {
			$value = get_term_by( 'id', $category, 'product_cat' );

			if ( ! empty( $value ) ) {
				$result[] = $value->name;
			}
		}

		$result = $this->get_result( $result );

		return implode( ', ', $result );
	}
}
