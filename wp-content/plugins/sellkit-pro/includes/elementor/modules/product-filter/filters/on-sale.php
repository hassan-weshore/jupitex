<?php
/**
 * Add Product on Sale Filter.
 *
 * @package JupiterX_Core\sellkit
 * @since 1.1.0
 */

namespace Sellkit_Pro\Elementor\Modules\Product_Filter\Filters;

defined( 'ABSPATH' ) || die();

use Elementor\Plugin as Elementor;

/**
 * On Sale.
 *
 * Initializing the on Sale by extending item base abstract class.
 *
 * @since 1.1.0
 */
class On_Sale extends Filter_Base {

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
		return 'on_sale';
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
		return __( 'On Sale', 'sellkit-pro' );
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
			'on_sale_switch' => [
				'name' => 'on_sale_switch',
				'label' => __( 'Label', 'sellkit-pro' ),
				'type' => 'text',
				'placeholder' => __( 'Enter your text...', 'sellkit-pro' ),
				'default' => __( 'Show only on sales', 'sellkit-pro' ),
				'condition' => [
					'filter_type' => $this->get_type(),
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

		$html = '<div class="sellkit-product-filter-item-wrapper sellkit-product-filter-on-sale">';

		$term = (object) [
			'term_id' => 'sale',
			'slug' => 'sale',
			'taxonomy' => 'on_sale',
		];

		$html .= $this->render_filter_heading( $heading );

		$html .= '<div class="sellkit-product-filter-on-sale-wrapper product-filter-item">';

		$html .= sprintf(
			'<label for="control-on-sale-checkbox-%1$s">%2$s<input id="control-on-sale-checkbox-%1$s" type="checkbox" class="product-filter-item-toggle" value="on sale" data-logic="null" data-filter="%1$s" data-type="%4$s" data-products=%3$s autocomplete="off" %5$s><span class="control-on-sale-checkbox"><span class="control-on-sale-checkbox-handler"></span></span></label>',
			esc_attr( $field['_id'] ),
			esc_attr( $field['on_sale_switch'] ),
			esc_attr( $this->get_filter_id( $term ) ),
			esc_attr( $field['filter_type'] ),
			$this->get_query_params( $term->taxonomy, $term->slug, '' )
		);

		$html .= '</div>';

		$html .= '</div>';

		echo $html;
	}
}
