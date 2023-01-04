<?php
/**
 * Add Rating Filter.
 *
 * @package JupiterX_Core\sellkit
 * @since 1.1.0
 */

namespace Sellkit_Pro\Elementor\Modules\Product_Filter\Filters;

defined( 'ABSPATH' ) || die();

use Elementor\Plugin as Elementor;

/**
 * Rating.
 *
 * Initializing the Rating by extending item base abstract class.
 *
 * @since 1.1.0
 */
class Rating extends Filter_Base {

	/**
	 * Get Filter type.
	 *
	 * Retrieve the Filter type.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return string Filter type.
	 */
	public function get_type() {
		return 'rating';
	}

	/**
	 * Get Filter Heading.
	 *
	 * Retrieve the Filter Heading.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return string Filter Heading.
	 */
	public function get_title() {
		return __( 'Rating', 'sellkit-pro' );
	}

	/**
	 * Update filter.
	 *
	 * Update filter settings.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param object $widget Widget instance.
	 */
	public function update_controls( $widget ) {
		$control_data = Elementor::$instance->controls_manager->get_control_from_stack(
			$widget->get_unique_name(),
			'filters'
		);

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$filter_controls = [
			'rating_display' => [
				'name' => 'rating_display',
				'label' => __( 'Display as', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'radio',
				'options' => [
					'radio' => __( 'Radio List', 'sellkit-pro' ),
					'checkbox' => __( 'Checkbox List', 'sellkit-pro' ),
					'links' => __( 'Links', 'jsellkit' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'rating_star' => [
				'name' => 'rating_star',
				'label' => __( 'Enable stars', 'sellkit-pro' ),
				'type' => 'switcher',
				'label_off' => __( 'No', 'sellkit-pro' ),
				'label_on' => __( 'Yes', 'sellkit-pro' ),
				'default' => 'yes',
				'condition' => [
					'filter_type' => $this->get_type(),
					'rating_display' => 'checkbox',
				],
			],
			'rating_logic' => [
				'name' => 'rating_logic',
				'label' => __( 'Logic', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'and',
				'options' => [
					'and' => __( 'And', 'sellkit-pro' ),
					'or' => __( 'Or', 'sellkit-pro' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
					'rating_display' => [ 'checkbox', 'button' ],
				],
			],
		];

		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $filter_controls );
		$widget->update_control( 'filters', $control_data );
	}

	/**
	 * Render filter content.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function render_content() {
		$field   = $this->field;
		$heading = $this->get_title();
		$terms   = [];

		$ratings = [
			5 => __( '5 only', 'sellkit-pro' ),
			4 => __( '4 and up', 'sellkit-pro' ),
			3 => __( '3 and up', 'sellkit-pro' ),
			2 => __( '2 and up', 'sellkit-pro' ),
			1 => __( '1 and up', 'sellkit-pro' ),
		];

		$html = sprintf(
			'<div class="sellkit-product-filter-item-wrapper sellkit-product-filter-rating %s">',
			'yes' === $field['rating_star'] ? 'sellkit-product-filter-rating-stars' : ''
		);

		foreach ( $ratings as $key => $rating ) {
			$terms[ $rating ] = (object) [
				'taxonomy' => 'rating',
				'term_id' => $key,
				'slug' => $key,
				'name' => $rating,
			];
		}

		$render_function = 'render_' . $field['rating_display'];

		$html .= $this->render_filter_heading( $heading );
		$html .= $this->$render_function( $terms, $field );

		$html .= '</div>';

		echo $html;
	}
}
