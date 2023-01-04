<?php
/**
 * Add Product Category Filter.
 *
 * @package JupiterX_Core\sellkit
 * @since 1.1.0
 */

namespace Sellkit_Pro\Elementor\Modules\Product_Filter\Filters;

defined( 'ABSPATH' ) || die();

use Elementor\Plugin as Elementor;

/**
 * Product Category.
 *
 * Initializing the Product Category by extending item base abstract class.
 *
 * @since 1.1.0
 */
class Category extends Filter_Base {

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
		return 'category';
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
		return __( 'Category', 'sellkit-pro' );
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
			'category_count' => [
				'name' => 'category_count',
				'label' => esc_html__( 'Show product count', 'sellkit-pro' ),
				'type' => 'switcher',
				'label_off' => esc_html__( 'No', 'sellkit-pro' ),
				'label_on' => esc_html__( 'Yes', 'sellkit-pro' ),
				'default' => 'yes',
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'category_display' => [
				'name' => 'category_display',
				'label' => esc_html__( 'Display as', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'radio',
				'options' => [
					'radio' => esc_html__( 'Radio List', 'sellkit-pro' ),
					'checkbox' => esc_html__( 'Checkbox List', 'sellkit-pro' ),
					'links' => esc_html__( 'Links', 'jsellkit' ),
					'dropdown' => esc_html__( 'Dropdown', 'sellkit-pro' ),
					'button' => esc_html__( 'Button', 'sellkit-pro' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'category_childern' => [
				'name' => 'category_childern',
				'label' => esc_html__( 'Include children', 'sellkit-pro' ),
				'type' => 'switcher',
				'label_off' => esc_html__( 'No', 'sellkit-pro' ),
				'label_on' => esc_html__( 'Yes', 'sellkit-pro' ),
				'default' => 'yes',
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'category_sub_categories' => [
				'name' => 'category_sub_categories',
				'label' => esc_html__( 'Show/hide sub-categories', 'sellkit-pro' ),
				'type' => 'switcher',
				'label_off' => esc_html__( 'Hide', 'sellkit-pro' ),
				'label_on' => esc_html__( 'Show', 'sellkit-pro' ),
				'default' => 'yes',
				'condition' => [
					'filter_type' => $this->get_type(),
					'category_childern' => 'yes',
					'category_display' => [
						'radio',
						'checkbox',
						'links',
					],
				],
			],
			'category_childern_hierarchy' => [
				'name' => 'category_childern_hierarchy',
				'label' => esc_html__( 'Show category hierarchy', 'sellkit-pro' ),
				'type' => 'switcher',
				'label_off' => esc_html__( 'No', 'sellkit-pro' ),
				'label_on' => esc_html__( 'Yes', 'sellkit-pro' ),
				'default' => 'yes',
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'category_logic' => [
				'name' => 'category_logic',
				'label' => esc_html__( 'Logic', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'and',
				'options' => [
					'and' => esc_html__( 'And', 'sellkit-pro' ),
					'or' => esc_html__( 'Or', 'sellkit-pro' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
					'category_display' => [ 'checkbox', 'button' ],
				],
			],
			'category_orderby' => [
				'name' => 'category_orderby',
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
			'category_order' => [
				'name' => 'category_order',
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
			'category_layout' => [
				'name' => 'category_layout',
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
					'category_display!' => 'dropdown',
				],
			],
			'category_cloumns' => [
				'name' => 'category_cloumns',
				'label' => __( 'Coulmns', 'sellkit-pro' ),
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
					'category_layout' => 'columns',
					'category_display!' => 'dropdown',
				],
			],
			'category_empty' => [
				'name' => 'category_empty',
				'label' => esc_html__( 'Show items without products', 'sellkit-pro' ),
				'type' => 'switcher',
				'label_off' => esc_html__( 'No', 'sellkit-pro' ),
				'label_on' => esc_html__( 'Yes', 'sellkit-pro' ),
				'default' => 'yes',
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'category_exclude' => [
				'name' => 'category_exclude',
				'label' => esc_html__( 'Exclude', 'sellkit-pro' ),
				'type' => 'select2',
				'multiple' => true,
				'options' => $this->get_taxonomy( 'category' ),
				'label_block' => true,
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
	 *
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function render_content() {
		$field   = $this->field;
		$heading = $this->get_title();

		$args = [
			'taxonomy' => 'product_cat',
			'orderby'  => $field['category_orderby'],
			'order'    => $field['category_order'],
			'hide_empty' => true,
		];

		if ( 'yes' === $field['category_childern_hierarchy'] ) {
			$args['hierarchical'] = true;
		}

		if ( 'yes' === $field['category_empty'] ) {
			$args['hide_empty'] = false;
		}

		if ( 'yes' !== $field['category_childern'] ) {
			$args['parent'] = 0;
		}

		if ( ! empty( $field['category_exclude'] ) ) {
			$args['exclude'] = implode( ',', $field['category_exclude'] );
		}

		$terms = get_terms( $args );

		$sorted_terms = $terms;

		if ( 'yes' === $field['category_childern'] ) {
			$sorted_terms = [];

			foreach ( $terms as $key => $term ) {
				if ( 0 === $term->parent ) {
					$sorted_terms[] = $term;
				}

				if ( ! empty( get_term_children( $term->term_id, 'product_cat' ) ) ) {
					foreach ( get_term_children( $term->term_id, 'product_cat' ) as $child ) {
						$sorted_terms[] = get_term_by( 'id', $child, 'product_cat' );
					}
				}
			}
		}

		$toggle = '';

		if (
			'yes' === $field['category_sub_categories'] &&
			'vertical' === $field['category_layout']
		) {
			$toggle = 'sellkit-category-filter-toggle';
		}

		$html = '<div class="sellkit-product-filter-item-wrapper sellkit-product-filter-category ' . $toggle . '">';

		$render_function = 'render_' . $field['category_display'];

		$html .= $this->render_filter_heading( $heading );
		$html .= $this->$render_function( $sorted_terms, $field );

		$html .= '</div>';

		echo $html;
	}
}
