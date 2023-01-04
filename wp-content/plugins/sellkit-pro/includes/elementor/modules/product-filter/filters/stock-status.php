<?php
/**
 * Add Product Stock Status Filter.
 *
 * @package JupiterX_Core\sellkit
 * @since 1.1.0
 */

namespace Sellkit_Pro\Elementor\Modules\Product_Filter\Filters;

defined( 'ABSPATH' ) || die();

use Elementor\Plugin as Elementor;

/**
 * Stock Status.
 *
 * Initializing the Stock Status by extending item base abstract class.
 *
 * @since 1.1.0
 */
class Stock_Status extends Filter_Base {

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
		return 'stock_status';
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
		return __( 'Stock Status', 'sellkit-pro' );
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
			'stock_status_display' => [
				'name' => 'stock_status_display',
				'label' => __( 'Display as', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'checkbox',
				'options' => [
					'checkbox' => __( 'Checkbox List', 'sellkit-pro' ),
					'links' => __( 'Links', 'jsellkit' ),
					'button' => __( 'Button', 'sellkit-pro' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'stock_status_logic' => [
				'name' => 'stock_status_logic',
				'label' => __( 'Logic', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'and',
				'options' => [
					'and' => __( 'And', 'sellkit-pro' ),
					'or' => __( 'Or', 'sellkit-pro' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
					'stock_status_display' => [ 'checkbox', 'button' ],
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

		$stock_status = [
			'instock' => __( 'In Stock', 'sellkit-pro' ),
			'outofstock' => __( 'Out of Stock', 'sellkit-pro' ),
			'onbackorder' => __( 'On Backorder', 'sellkit-pro' ),
		];

		foreach ( $stock_status as $key => $status ) {
			$terms[ $key ] = (object) [
				'taxonomy' => '_stock_status',
				'term_id' => $key,
				'slug' => $key,
				'name' => $status,
			];
		}

		$html = '<div class="sellkit-product-filter-item-wrapper sellkit-product-filter-stock-status">';

		$render_function = 'render_' . $field['stock_status_display'];

		$html .= $this->render_filter_heading( $heading );
		$html .= $this->$render_function( $terms, $field );

		$html .= '</div>';

		echo $html;
	}
}
