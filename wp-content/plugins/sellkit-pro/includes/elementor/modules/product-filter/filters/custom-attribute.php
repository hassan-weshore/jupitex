<?php
/**
 * Add Product Custom Attribute Filter.
 *
 * @package JupiterX_Core\sellkit
 * @since 1.1.0
 */

namespace Sellkit_Pro\Elementor\Modules\Product_Filter\Filters;

defined( 'ABSPATH' ) || die();

use Elementor\Plugin as Elementor;

/**
 * Custom Attribute.
 *
 * Initializing the Custom Attribute by extending item base abstract class.
 *
 * @since 1.1.0
 */
class Custom_Attribute extends Filter_Base {

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
		return 'custom_attribute';
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

		$attributes_list        = $this->get_attributes();
		$color_attributes_list  = $this->get_color_swatch_attributes();
		$image_attributes_list  = $this->get_image_swatch_attributes();
		$custom_attributes_list = array_merge( $image_attributes_list, $color_attributes_list );

		$filter_controls = [
			'custom_attribute_select' => [
				'name' => 'custom_attribute_select',
				'label' => esc_html__( 'Select attribute', 'sellkit-pro' ),
				'type' => 'select',
				'default' => array_key_first( $attributes_list ),
				'options' => $attributes_list,
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'custom_attribute_display' => [
				'name' => 'custom_attribute_display',
				'label' => esc_html__( 'Display as', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'dropdown',
				'options' => [
					'dropdown' => esc_html__( 'Dropdown', 'sellkit-pro' ),
					'button' => esc_html__( 'Button', 'sellkit-pro' ),
					'links' => esc_html__( 'Links', 'sellkit-pro' ),
					'radio' => esc_html__( 'Radio', 'sellkit-pro' ),
					'checkbox' => esc_html__( 'Checkbox', 'sellkit-pro' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
					'custom_attribute_select!' => $custom_attributes_list,
				],
			],
			'color_swatches_display' => [
				'name' => 'color_swatches_display',
				'label' => esc_html__( 'Display as', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'dropdown',
				'options' => [
					'color' => esc_html__( 'Color Swatch', 'sellkit-pro' ),
					'dropdown' => esc_html__( 'Dropdown', 'sellkit-pro' ),
					'button' => esc_html__( 'Button', 'sellkit-pro' ),
					'links' => esc_html__( 'Links', 'sellkit-pro' ),
					'radio' => esc_html__( 'Radio', 'sellkit-pro' ),
					'checkbox' => esc_html__( 'Checkbox', 'sellkit-pro' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
					'custom_attribute_select' => $color_attributes_list,
					'custom_attribute_select!' => $image_attributes_list,
				],
			],
			'image_swatches_display' => [
				'name' => 'image_swatches_display',
				'label' => esc_html__( 'Display as', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'dropdown',
				'options' => [
					'image' => esc_html__( 'Image Swatch', 'sellkit-pro' ),
					'dropdown' => esc_html__( 'Dropdown', 'sellkit-pro' ),
					'button' => esc_html__( 'Button', 'sellkit-pro' ),
					'links' => esc_html__( 'Links', 'sellkit-pro' ),
					'radio' => esc_html__( 'Radio', 'sellkit-pro' ),
					'checkbox' => esc_html__( 'Checkbox', 'sellkit-pro' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
					'custom_attribute_select' => $image_attributes_list,
					'custom_attribute_select!' => $color_attributes_list,
				],
			],
			'custom_attribute_logic' => [
				'name' => 'custom_attribute_logic',
				'label' => esc_html__( 'Logic', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'and',
				'options' => [
					'and' => esc_html__( 'And', 'sellkit-pro' ),
					'or' => esc_html__( 'Or', 'sellkit-pro' ),
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'terms' => [
								[
									'name' => 'filter_type',
									'operator' => '==',
									'value' => $this->get_type(),
								],
							],
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'custom_attribute_display',
									'operator' => 'in',
									'value' => [ 'checkbox', 'button' ],
								],
								[
									'name' => 'image_swatches_display',
									'operator' => 'in',
									'value' => [ 'checkbox', 'button', 'image' ],
								],
								[
									'name' => 'color_swatches_display',
									'operator' => 'in',
									'value' => [ 'checkbox', 'button', 'color' ],
								],
							],
						],
					],
				],
			],
			'custom_attribute_orderby' => [
				'name' => 'custom_attribute_orderby',
				'label' => esc_html__( 'Order by', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'name',
				'options' => [
					'name' => esc_html__( 'Name', 'sellkit-pro' ),
					'count' => esc_html__( 'Count', 'sellkit-pro' ),
					'id' => esc_html__( 'ID', 'sellkit-pro' ),
					'custom' => esc_html__( 'Custom', 'sellkit-pro' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'custom_attribute_order' => [
				'name' => 'custom_attribute_order',
				'label' => esc_html__( 'Order', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'asc',
				'options' => [
					'asc' => esc_html__( 'ASC', 'sellkit-pro' ),
					'desc' => esc_html__( 'DESC', 'sellkit-pro' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'custom_attribute_layout' => [
				'name' => 'custom_attribute_layout',
				'label' => esc_html__( 'Layout', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'horizontal',
				'options' => [
					'horizontal' => esc_html__( 'Horizontal', 'sellkit-pro' ),
					'vertical' => esc_html__( 'Vertical', 'sellkit-pro' ),
					'columns' => esc_html__( 'Columns', 'sellkit-pro' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'custom_attribute_cloumns' => [
				'name' => 'custom_attribute_cloumns',
				'label' => esc_html__( 'Coulmns', 'sellkit-pro' ),
				'type' => 'select',
				'default' => '2',
				'options' => [
					'2' => esc_html__( '2', 'sellkit-pro' ),
					'3' => esc_html__( '3', 'sellkit-pro' ),
					'4' => esc_html__( '4', 'sellkit-pro' ),
					'5' => esc_html__( '5', 'sellkit-pro' ),
					'6' => esc_html__( '6', 'sellkit-pro' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
					'custom_attribute_layout' => 'columns',
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
	 *
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function render_content() {
		$field              = $this->field;
		$selected_attribute = '';
		$args               = [];
		$terms              = [];
		$html               = '';

		if ( strpos( $field['custom_attribute_select'], 'attribute-simple-' ) !== false ) {
			$selected_attribute = str_replace( 'attribute-simple-', '', $field['custom_attribute_select'] );
		}

		if ( strpos( $field['custom_attribute_select'], 'attribute-image-' ) !== false ) {
			$selected_attribute = str_replace( 'attribute-image-', '', $field['custom_attribute_select'] );
		}

		if ( strpos( $field['custom_attribute_select'], 'attribute-color-' ) !== false ) {
			$selected_attribute = str_replace( 'attribute-color-', '', $field['custom_attribute_select'] );
		}

		if ( strpos( $field['custom_attribute_select'], 'attribute-radio-' ) !== false ) {
			$selected_attribute = str_replace( 'attribute-radio-', '', $field['custom_attribute_select'] );
		}

		if ( strpos( $field['custom_attribute_select'], 'attribute-text-' ) !== false ) {
			$selected_attribute = str_replace( 'attribute-text-', '', $field['custom_attribute_select'] );
		}

		$attribute = wc_get_attribute( $selected_attribute );

		if ( empty( $attribute ) ) {
			return;
		}

		$args = [
			'taxonomy'   => $attribute->slug,
			'orderby'    => $field['custom_attribute_orderby'],
			'order'      => $field['custom_attribute_order'],
			'hide_empty' => true,
		];

		$terms = get_terms( $args );

		if ( empty( $terms ) ) {
			return;
		}

		$selected_attribute_data = $this->get_custom_attributes_data( $selected_attribute );

		$display_type = $field['custom_attribute_display'];

		if ( ! empty( $selected_attribute_data ) ) {
			if ( 'color' === $selected_attribute_data->attribute_type ) {
				$display_type = $field['color_swatches_display'];
			}

			if ( 'image' === $selected_attribute_data->attribute_type ) {
				$display_type = $field['image_swatches_display'];
			}
		}

		$html .= '<div class="sellkit-product-filter-item-wrapper sellkit-product-filter-custom-attributes">';

		$render_function = 'render_' . $display_type;
		$attribute_name  = ! empty( $attribute->name ) ? $attribute->name : null;

		$html .= $this->render_filter_heading( $attribute_name );
		$html .= $this->$render_function( $terms, $field );

		$html .= '</div>';

		echo $html;
	}
}
