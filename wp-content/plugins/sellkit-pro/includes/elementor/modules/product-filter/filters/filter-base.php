<?php
/**
 * Add Filter Base.
 *
 * @package JupiterX_Core\sellkit
 * @since 1.1.0
 */

namespace Sellkit_Pro\Elementor\Modules\Product_Filter\Filters;

defined( 'ABSPATH' ) || die();

use Elementor\Settings;
use Elementor\Icons_Manager;

/**
 * Filter Base.
 *
 * An abstract class to register new product filters.
 *
 * @since 1.1.0
 * @abstract
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
abstract class Filter_Base {

	/**
	 * Product Filter widget.
	 *
	 * Holds the form widget instance.
	 *
	 * @access public
	 *
	 * @var array
	 */
	public $widget;

	/**
	 * Product Filter.
	 *
	 * Holds all the Filter.
	 *
	 * @access public
	 *
	 * @var array
	 */
	public $filter;

	/**
	 * Count parameter types.
	 *
	 * Product Filter validate Count parameter types.
	 *
	 * @access public
	 *
	 * @var array
	 */
	const VALID_TYPES = [
		'category',
		'tag',
	];

	/**
	 * Filters activate values.
	 *
	 * @access public
	 *
	 * @var array
	 */
	const FILTER_ACTIVATE_VALUES = [
		'links' => 'active-link-load active-link',
		'dropdown' => 'selected',
		'button' => 'active-button',
		'button-checked' => 'checked',
		'image' => 'active-image-load active-image',
		'color' => 'active-color-load active-color',
	];

	/**
	 * Get Filter ID.
	 *
	 * Retrieve the Filter type.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return string Filter ID.
	 */
	public function get_id() {
		return $this->filter['_id'];
	}

	/**
	 * Get filter type.
	 *
	 * Retrieve the filter type.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return string filter type.
	 */
	public function get_type() {
		return $this->filter['type'];
	}

	/**
	 * Get filter class.
	 *
	 * Retrieve the filter class.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return string filter class.
	 */
	public function get_class() {
		return 'sellkit-product-filter';
	}

	/**
	 * Update controls.
	 *
	 * Add controls in Filter Item.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param object $widget Widget instance.
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function update_controls( $widget ) {}

	/**
	 * Render the filter content.
	 *
	 * @since 1.1.0
	 * @access public
	 * @abstract
	 *
	 * @return string The field content.
	 */
	abstract public function render_content();

	/**
	 * Check filters.
	 *
	 * Check filters are valid to add product count.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param array $term The term.
	 * @param array $filter The array to filter data.
	 *
	 * @return string The products count or empty.
	 */
	public function check_product_count( $term, $filter ) {
		$filter_count = '';

		if ( in_array( $this->get_type(), self::VALID_TYPES, true ) ) {
			$filter_count = 'yes' === $filter[ $this->get_type() . '_count' ] ? '<span class="sellkit-product-filter-count">(' . $term->count . ')</span>' : '';
		}

		return $filter_count;
	}

	/**
	 * Get filters id as json .
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param object $term The term.
	 *
	 * @return string Get id of each terms or taxonomies.
	 */
	public function get_filter_id( $term ) {
		$product_data = [
			0 => strval( $term->term_id ),
			1 => $term->slug,
		];

		return wp_json_encode( $product_data, JSON_FORCE_OBJECT );
	}

	/**
	 * Render Radio structure.
	 *
	 * Render html structure for Radio type.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param array $terms The terms.
	 * @param array $filter The array to filter data.
	 *
	 * @return string The Radio structure.
	 *
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function render_radio( $terms, $filter ) {
		$radio = '';

		$layout  = $this->get_type() . '_layout';
		$columns = '';

		if ( isset( $filter[ $layout ] ) && 'columns' === $filter[ $layout ] ) {
			$columns = $this->get_type() . '_cloumns';
			$columns = isset( $filter[ $columns ] ) ? '-' . $filter[ $columns ] : '';
		}

		$filter_layout = isset( $filter[ $layout ] ) ? $filter[ $layout ] : 'default';

		$radio .= sprintf(
			'<div class="sellkit-product-filter-radio %s">',
			'sellkit-product-filter-layout-' . $filter_layout . $columns
		);

		$type = ! empty( $filter['custom_attribute_select'] ) ? $filter['custom_attribute_select'] : $filter['filter_type'];

		foreach ( $terms as $key => $term ) {
			$condition          = 'product_cat' === $term->taxonomy && 0 !== $term->parent ? true : false;
			$childern_condition = ! empty( get_term_children( $term->term_id, 'product_cat' ) ) ? true : false;

			$radio .= sprintf(
				'<div class="sellkit-product-filter-radio-wrapper product-filter-item %7$s" %8$s %9$s><input id="%1$s-%4$s-%2$s" class="product-filter-item-radio" type="radio" name="%1$s" value="%5$s" data-filter=%2$s data-type="%1$s" data-products=%3$s autocomplete="off" %10$s><label for="%1$s-%4$s-%2$s">%5$s %6$s</label></div>',
				esc_attr( $type ),
				esc_attr( $filter['_id'] ),
				esc_attr( $this->get_filter_id( $term ) ),
				esc_attr( $term->term_id ),
				esc_attr( $term->name ),
				$this->check_product_count( $term, $filter ),
				$condition ? esc_attr( 'sub-item' ) : '',
				$condition ? esc_attr( 'data-parent=' . $term->parent . '' ) : '',
				$childern_condition ? esc_attr( ' data-term= ' . $term->term_id . '' ) : '',
				$this->get_query_params( $type, $term->slug, $filter[ $this->get_type() . '_display' ] )
			);
		}

		$radio .= '</div>';

		return $radio;
	}

	/**
	 * Render Checkbox structure.
	 *
	 * Render html structure for Checkbox type.
	 *
	 * @since 1.1.0
	 * @access public
	 * @param array $terms The terms.
	 * @param array $filter The array to filter data.
	 *
	 * @return string The Checkbox structure.
	 *
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function render_checkbox( $terms, $filter ) {
		$checkbox = '';

		$layout  = $this->get_type() . '_layout';
		$columns = '';

		if ( isset( $filter[ $layout ] ) && 'columns' === $filter[ $layout ] ) {
			$columns = $this->get_type() . '_cloumns';
			$columns = isset( $filter[ $columns ] ) ? '-' . $filter[ $columns ] : '';
		}

		$filter_layout = isset( $filter[ $layout ] ) ? $filter[ $layout ] : 'default';

		$logic = $this->get_type() . '_logic';

		if ( isset( $filter[ $logic ] ) ) {
			$logic = $filter[ $logic ];
		}

		$checkbox .= sprintf(
			'<div class="sellkit-product-filter-checkbox %s">',
			'sellkit-product-filter-layout-' . $filter_layout . $columns
		);

		$checkbox .= '';

		$type = ! empty( $filter['custom_attribute_select'] ) ? $filter['custom_attribute_select'] : $filter['filter_type'];

		foreach ( $terms as $key => $term ) {
			$condition          = 'product_cat' === $term->taxonomy && 0 !== $term->parent ? true : false;
			$childern_condition = ! empty( get_term_children( $term->term_id, 'product_cat' ) ) ? true : false;

			$checkbox .= sprintf(
				'<div class="sellkit-product-filter-checkbox-wrapper product-filter-item %8$s" %9$s %10$s><input id="%1$s-%4$s-%2$s" class="product-filter-item-checkbox" type="checkbox" name="%1$s" value="%5$s" data-type="%1$s" data-filter="%2$s" data-products=%3$s data-logic=%7$s autocomplete="off" %11$s><label for="%1$s-%4$s-%2$s">%5$s %6$s</label></div>',
				esc_attr( $type ),
				esc_attr( $filter['_id'] ),
				esc_attr( $this->get_filter_id( $term ) ),
				esc_attr( $term->term_id ),
				esc_attr( $term->name ),
				$this->check_product_count( $term, $filter ),
				esc_attr( $logic ),
				$condition ? esc_attr( 'sub-item' ) : '',
				$condition ? esc_attr( 'data-parent=' . $term->parent . '' ) : '',
				$childern_condition ? esc_attr( ' data-term= ' . $term->term_id . '' ) : '',
				$this->get_query_params( $type, $term->slug, $filter[ $this->get_type() . '_display' ] )
			);
		}

		$checkbox .= '</div>';

		return $checkbox;
	}

	/**
	 * Render Links structure.
	 *
	 * Render html structure for Links type.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param array $terms The terms.
	 * @param array $filter The array to filter data.
	 *
	 * @return string The Links structure.
	 *
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function render_links( $terms, $filter ) {
		$links = '';

		$layout  = $this->get_type() . '_layout';
		$columns = '';

		if ( isset( $filter[ $layout ] ) && 'columns' === $filter[ $layout ] ) {
			$columns = $this->get_type() . '_cloumns';
			$columns = isset( $filter[ $columns ] ) ? '-' . $filter[ $columns ] : '';
		}

		$filter_layout = isset( $filter[ $layout ] ) ? $filter[ $layout ] : 'default';

		$links .= sprintf(
			'<div class="sellkit-product-filter-links"><ul class="%s">',
			'sellkit-product-filter-layout-' . $filter_layout . $columns
		);

		$type = ! empty( $filter['custom_attribute_select'] ) ? $filter['custom_attribute_select'] : $filter['filter_type'];

		foreach ( $terms as $key => $term ) {
			$condition          = 'product_cat' === $term->taxonomy && 0 !== $term->parent ? true : false;
			$childern_condition = ! empty( get_term_children( $term->term_id, 'product_cat' ) ) ? true : false;

			$links .= sprintf(
				'<li class="product-filter-item"><a id="%1$s-%4$s-%2$s" class="product-filter-item-links %7$s %10$s" %8$s %9$s href="#" data-value="%5$s" data-filter="%2$s" data-type="%1$s" data-products=%3$s>%5$s %6$s</a></li>',
				esc_attr( $type ),
				esc_attr( $filter['_id'] ),
				esc_attr( $this->get_filter_id( $term ) ),
				esc_attr( $term->term_id ),
				esc_attr( $term->name ),
				$this->check_product_count( $term, $filter ),
				$condition ? esc_attr( 'sub-item' ) : '',
				$condition ? esc_attr( 'data-parent=' . $term->parent . '' ) : '',
				$childern_condition ? esc_attr( ' data-term= ' . $term->term_id . '' ) : '',
				$this->get_query_params( $type, $term->slug, $filter[ $this->get_type() . '_display' ] )
			);
		}

		$links .= '</div></ul>';

		return $links;
	}

	/**
	 * Render Dropdown structure.
	 *
	 * Render html structure for Dropdown type.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param array $terms The terms.
	 * @param array $filter The array to filter data.
	 *
	 * @return string The Dropdown structure.
	 */
	public function render_dropdown( $terms, $filter ) {
		$settings = $this->widget->get_settings_for_display();
		$dropdown = '';
		$type     = ! empty( $filter['custom_attribute_select'] ) ? $filter['custom_attribute_select'] : $filter['filter_type'];

		$select_id = $type . '-' . $filter['_id'] . '-wrapper';

		$dropdown .= '<div class="sellkit-product-filter-dropdown product-filter-item">';
		$dropdown .= '<div class="sellkit-product-filter-dropdown-wrapper">';
		$dropdown .= '<div class="product-filter-item-select-label">';
		$dropdown .= $this->convert_elementor_icon_to_var( $settings['dropdown_icon_new'] );
		$dropdown .= '<select id="' . $select_id . '" class="product-filter-item-select">';

		$dropdown .= sprintf(
			'<option id="%1$s-%2$s" value=%3$s data-filter="%2$s" data-type="%1$s" data-products=%3$s>%4$s</option>',
			esc_attr( $type ),
			esc_attr( $filter['_id'] ),
			esc_attr( 'all' ),
			esc_html__( 'Select one...', 'sellkit-pro' )
		);

		foreach ( $terms as $key => $term ) {
			$dropdown .= sprintf(
				'<option id="%1$s-%4$s-%2$s" value=%5$s data-filter="%2$s" data-type="%1$s" data-products=%3$s %7$s>%5$s %6$s</option>',
				esc_attr( $type ),
				esc_attr( $filter['_id'] ),
				esc_attr( $this->get_filter_id( $term ) ),
				esc_attr( $term->term_id ),
				esc_attr( $term->name ),
				$this->check_product_count( $term, $filter ),
				$this->get_query_params( $type, $term->slug, $filter[ $this->get_type() . '_display' ] )
			);
		}

		$dropdown .= '</select>';
		$dropdown .= '</div>';
		$dropdown .= '</div>';
		$dropdown .= '</div>';

		return $dropdown;
	}

	/**
	 * Render Custom Range structure.
	 *
	 * Render html structure for Custom Range.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param array $filter The array to filter data.
	 *
	 * @return string The Custom Range structure.
	 */
	public function render_custom_range( $filter ) {
		$custom_range = '';

		$range_id = 'custom-range-' . $filter['_id'];
		$logic    = isset( $filter['price_logic'] ) ? $filter['price_logic'] : 'null';

		$query_string_parameter = $this->get_query_params( 'custom_range', 'custom_range', '' );
		$price_range            = [];

		if ( ! empty( $query_string_parameter ) ) {
			$item = explode( '-', $query_string_parameter );

			$price_range = [
				'min' => intval( $item[0] ),
				'max' => intval( $item[1] ),
			];
		}

		$custom_range .= '<div class="sellkit-product-filter-custom-range sellkit-product-filter-form-type product-filter-item">';
		$custom_range .= sprintf(
			'<form id="%1$s" data-type="%2$s" data-filter=%1$s data-logic="%3$s" data-products="%4$s" data-value="%4$s" class="sellkit-product-filter-custom-range-form" >',
			esc_attr( $range_id ),
			esc_attr( $filter['filter_type'] ),
			esc_attr( $logic ),
			esc_attr( wp_json_encode( [ $query_string_parameter, $query_string_parameter ] ) )
		);
		$custom_range .= sprintf(
			'<input type="number" name="custom_range_min" value="%s" pattern="[0-9]*" autocomplete="off" required/>',
			isset( $price_range['min'] ) ? $price_range['min'] : ''
		);

		$custom_range .= sprintf(
			'<input type="number" name="custom_range_max" value="%s" pattern="[0-9]*" autocomplete="off" required/>',
			isset( $price_range['max'] ) ? $price_range['max'] : ''
		);

		$custom_range_icon = $this->convert_elementor_icon_to_var( [
			'value' => 'fas fa-search',
			'library' => 'fa-solid',
		] );

		$custom_range .= sprintf(
			'<button type="submit" form="%1$s">%2$s</button>',
			esc_attr( $range_id ),
			$custom_range_icon
		);

		$custom_range .= '</form>';
		$custom_range .= '</div>';

		return $custom_range;
	}

	/**
	 * Render Button structure.
	 *
	 * Render html structure for Button type.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param array $terms The terms.
	 * @param array $filter The array to filter data.
	 *
	 * @return string The Button structure.
	 */
	public function render_button( $terms, $filter ) {
		$button = '';

		$layout  = $this->get_type() . '_layout';
		$columns = '';

		if ( isset( $filter[ $layout ] ) && 'columns' === $filter[ $layout ] ) {
			$columns = $this->get_type() . '_cloumns';
			$columns = isset( $filter[ $columns ] ) ? '-' . $filter[ $columns ] : '';
		}

		$filter_layout = isset( $filter[ $layout ] ) ? $filter[ $layout ] : 'default';

		$logic = $this->get_type() . '_logic';

		if ( isset( $filter[ $logic ] ) ) {
			$logic = $filter[ $logic ];
		}

		$button .= sprintf(
			'<div class="sellkit-product-filter-button %s">',
			'sellkit-product-filter-layout-' . $filter_layout . $columns
		);

		$type = ! empty( $filter['custom_attribute_select'] ) ? $filter['custom_attribute_select'] : $filter['filter_type'];

		foreach ( $terms as $key => $term ) {
			$button .= sprintf(
				'<label for="%1$s-%4$s-%2$s" class="product-filter-item %8$s"><input id="%1$s-%4$s-%2$s" type="checkbox" class="product-filter-item-button" value="%5$s" data-filter="%2$s" data-type="%1$s" data-products=%3$s data-logic=%7$s autocomplete="off" %9$s><span>%5$s %6$s</span></label>',
				esc_attr( $type ),
				esc_attr( $filter['_id'] ),
				esc_attr( $this->get_filter_id( $term ) ),
				esc_attr( $term->term_id ),
				esc_attr( $term->name ),
				$this->check_product_count( $term, $filter ),
				esc_attr( $logic ),
				$this->get_query_params( $type, $term->slug, $filter[ $this->get_type() . '_display' ] ),
				$this->get_query_params( $type, $term->slug, 'button-checked' )
			);
		}

		$button .= '</div>';

		return $button;
	}

	/**
	 * Render Image swatch structure.
	 *
	 * Render html structure for Image swatch type.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param array $terms The terms.
	 * @param array $filter The array to filter data.
	 *
	 * @return string The Image swatch structure.
	 */
	public function render_image( $terms, $filter ) {
		$image_swatch = '';

		$layout         = $this->get_type() . '_layout';
		$columns        = '';
		$attribute_id   = str_replace( 'attribute-image-', '', $filter['custom_attribute_select'] );
		$attribute_meta = $this->get_custom_attributes_data( $attribute_id );

		if ( isset( $filter[ $layout ] ) && 'columns' === $filter[ $layout ] ) {
			$columns = $this->get_type() . '_cloumns';
			$columns = isset( $filter[ $columns ] ) ? '-' . $filter[ $columns ] : '';
		}

		$filter_layout = isset( $filter[ $layout ] ) ? $filter[ $layout ] : 'default';

		$image_swatch .= sprintf(
			'<div class="sellkit-product-filter-image-swatch %s">',
			'sellkit-product-filter-layout-' . $filter_layout . $columns
		);

		$type = ! empty( $filter['custom_attribute_select'] ) ? $filter['custom_attribute_select'] : $filter['filter_type'];

		foreach ( $terms as $key => $term ) {
			$term_value           = $this->get_custom_attribute_terms_data( $term->term_id, $filter['image_swatches_display'] );
			$image_swatch_content = '';

			if ( ! empty( $term_value ) ) {
				$image_swatch_content = sprintf(
					'<span id="%1$s-%4$s-%1$s" class="product-filter-item-image %8$s" data-filter="%2$s" data-value="%3$s" data-type="%1$s" data-products=%5$s style="width:%6$s; height: %6$s;">%7$s</span>',
					esc_attr( $type ),
					esc_attr( $filter['_id'] ),
					esc_attr( $term->name ),
					esc_attr( $term->slug ),
					$this->get_filter_id( $term ),
					! empty( $attribute_meta->image_size ) ? $attribute_meta->image_size . 'px' : '30px',
					! empty( wp_get_attachment_image( $term_value ) ) ? wp_get_attachment_image( $term_value ) : '',
					$this->get_query_params( $type, $term->slug, $filter['image_swatches_display'] )
				);
			}

			$image_swatch .= sprintf(
				'<span class="product-filter-item">%1$s %2$s</span>',
				$image_swatch_content,
				$term->name
			);
		}

		$image_swatch .= '</div>';

		return $image_swatch;
	}

	/**
	 * Render Color swatch structure.
	 *
	 * Render html structure for Color swatch type.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param array $terms The terms.
	 * @param array $filter The array to filter data.
	 *
	 * @return string The Color swatch structure.
	 */
	public function render_color( $terms, $filter ) {
		$color_swatch = '';

		$layout         = $this->get_type() . '_layout';
		$columns        = '';
		$attribute_id   = str_replace( 'attribute-color-', '', $filter['custom_attribute_select'] );
		$attribute_meta = $this->get_custom_attributes_data( $attribute_id );

		if ( isset( $filter[ $layout ] ) && 'columns' === $filter[ $layout ] ) {
			$columns = $this->get_type() . '_cloumns';
			$columns = isset( $filter[ $columns ] ) ? '-' . $filter[ $columns ] : '';
		}

		$filter_layout = isset( $filter[ $layout ] ) ? $filter[ $layout ] : 'default';

		$color_swatch .= sprintf(
			'<div class="sellkit-product-filter-color-swatch %s">',
			'sellkit-product-filter-layout-' . $filter_layout . $columns
		);

		$type = ! empty( $filter['custom_attribute_select'] ) ? $filter['custom_attribute_select'] : $filter['filter_type'];

		foreach ( $terms as $key => $term ) {
			$term_value           = $this->get_custom_attribute_terms_data( $term->term_id, $filter['color_swatches_display'] );
			$color_swatch_content = '';

			if ( ! empty( $term_value ) ) {
				$color_swatch_content = sprintf(
					'<span id="%1$s-%4$s-%2$s" class="product-filter-item-color %8$s" data-filter="%2$s" data-value="%3$s" data-type="%1$s" data-products=%5$s style="width:%6$s; height: %6$s;background-color: %7$s;"></span>',
					esc_attr( $type ),
					esc_attr( $filter['_id'] ),
					esc_attr( $term->name ),
					esc_attr( $term->slug ),
					$this->get_filter_id( $term ),
					! empty( $attribute_meta->color_size ) ? $attribute_meta->color_size . 'px' : '30px',
					esc_attr( $term_value ),
					$this->get_query_params( $type, $term->slug, $filter['color_swatches_display'] )
				);
			}

			$color_swatch .= sprintf(
				'<span class="product-filter-item"> %1$s %2$s</span>',
				$color_swatch_content,
				$term->name
			);
		}

		$color_swatch .= '</div>';

		return $color_swatch;
	}

	/**
	 * Render Heading.
	 *
	 * Render Heading for filters.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param string $filter The array to filter data.
	 *
	 * @return string The Heading structure.
	 */
	public function render_filter_heading( $filter ) {
		$settings = $this->widget->get_settings_for_display();
		$heading  = '';
		$toggle   = $this->render_filter_toggle_html();

		$heading = sprintf(
			'<h3 class="product-filter-item-heading %1$s">%2$s %3$s</h3>',
			'yes' === $settings['allow_toggle_able'] ? 'sellkit-toggle-able-heading sellkit-toggle-expanded' : '',
			$filter,
			'yes' === $settings['allow_toggle_able'] ? $toggle : ''
		);

		return $heading;
	}

	/**
	 * Render toggle structure.
	 *
	 * Render toggle structure html for filters.
	 *
	 * @since 1.1.0
	 * @access public
	 * @param string $toggle toggle html codes.
	 *
	 * @return string The Heading structure.
	 */
	public function render_filter_toggle_html( $toggle = '' ) {
		$settings = $this->widget->get_settings_for_display();

		$toggle .= '<a class="sellkit-toggle-able">';
		$toggle .= '<span class="sellkit-toggle-able-collapsed">' . $this->convert_elementor_icon_to_var( $settings['filter_group_vertical_icon_collapsed'] ) . '</span>';
		$toggle .= '<span class="sellkit-toggle-able-expanded">' . $this->convert_elementor_icon_to_var( $settings['filter_group_vertical_icon_expanded'] ) . '</span>';
		$toggle .= '</a>';

		return $toggle;
	}

	/**
	 * Inject controls after a specific control.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param array  $array The source array.
	 * @param array  $new   The array to insert.
	 * @param string $key   The key.
	 */
	public function inject_field_controls( array $array, array $new, $key = 'filter_type' ) {
		$keys  = array_keys( $array );
		$index = array_search( $key, $keys, true );
		$pos   = false === $index ? count( $array ) : $index + 1;

		return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
	}

	/**
	 * Render filter.
	 *
	 * Render filter label and content.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param object $widget Widget instance.
	 * @param array  $filter filter.
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function render( $widget, $filter ) {
		$this->widget = $widget;
		$this->field  = $filter;

		$settings = $this->widget->get_settings_for_display();

		$this->widget->add_render_attribute(
			'filter-type-' . $this->field['_id'],
			'class',
			'sellkit-product-filter-content sellkit-product-filter-content-' . $this->field['_id']
		);

		$filter_heading = $this->field['filter_type'];

		if ( 'custom_attribute' === $filter_heading ) {
			$filter_heading = $this->field['custom_attribute_select'];
		}
		?>
		<div <?php echo $this->widget->get_render_attribute_string( 'filter-type-' . $this->field['_id'] ); ?>>
			<?php
			if ( 'horizontal' === $settings['content_style'] ) {
				$this->render_horizental_selector( $filter_heading );
			}

			$this->render_content();
			?>
		</div>
		<?php
	}

	/**
	 * Render horizental selector.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param string $filter_name Filter type name.
	 */
	public function render_horizental_selector( $filter_name ) {
		$filter_name = str_replace( '_', ' ', $filter_name );
		$filter_name = $this->translate_filter_types( $filter_name );

		if ( strpos( $filter_name, 'attribute-' ) !== false ) {
			$filter_id   = preg_replace( '/[^0-9]/', '', $filter_name );
			$attribute   = wc_get_attribute( $filter_id );
			$filter_name = $attribute->name;
		}

		$vertical = sprintf(
			'<h4 class="product-filter-selector">%s</h4>',
			esc_html( $filter_name )
		);

		echo $vertical;
	}

	/**
	 * Translate filter types.
	 *
	 * @param string $filter_name filter name.
	 * @since 1.2.4
	 * @return string
	 */
	private function translate_filter_types( $filter_name ) {
		switch ( $filter_name ) {
			case 'category':
				return esc_html__( 'Category', 'sellkit-pro' );
			case 'tag':
				return esc_html__( 'Tag', 'sellkit-pro' );
			case 'price':
				return esc_html__( 'Price', 'sellkit-pro' );
			case 'rating':
				return esc_html__( 'Rating', 'sellkit-pro' );
			case 'search text':
				return esc_html__( 'Search Text', 'sellkit-pro' );
			case 'stock status':
				return esc_html__( 'Stock Status', 'sellkit-pro' );
			case 'on sale':
				return esc_html__( 'On Sale', 'sellkit-pro' );
			default:
				return $filter_name;
		}
	}

	/**
	 * Generate Terms.
	 *
	 * Generate Terms for exclude.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param string $taxonomy taxonomy.
	 */
	public function get_taxonomy( $taxonomy ) {
		$taxonomy_map = [
			'category' => 'product_cat',
			'tag' => 'product_tag',
		];

		$terms = [];

		foreach ( get_terms( $taxonomy_map[ $taxonomy ], [ 'hide_empty' => false ] ) as $term ) {
			$terms[ $term->term_id ] = $term->name;
		}

		return $terms;
	}

	/**
	 * Generate Attributes.
	 *
	 * Generate attribribute for select.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function get_attributes() {
		$attributes = wc_get_attribute_taxonomies();

		$attributes_list = [];

		foreach ( $attributes as $attribute ) {
			$custom_swatches = get_option( 'artbees_wc_attributes-' . $attribute->attribute_id . '' );
			$custom_swatches = json_decode( $custom_swatches );

			$attribute_key = 'attribute-simple-' . $attribute->attribute_id;

			if ( ! empty( $custom_swatches ) && ! empty( $custom_swatches->attribute_type ) ) {
				$attribute_key = 'attribute-' . $custom_swatches->attribute_type . '-' . $attribute->attribute_id;
			}

			$attributes_list[ $attribute_key ] = $attribute->attribute_label;
		}

		return $attributes_list;
	}

	/**
	 * Generate Color Attributes.
	 *
	 * Generate Color attribribute for display condition.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function get_color_swatch_attributes() {
		$attributes = wc_get_attribute_taxonomies();

		$attributes_list = [];

		foreach ( $attributes as $attribute ) {
			$custom_swatches = $this->get_custom_attributes_data( $attribute->attribute_id );

			if (
				! empty( $custom_swatches ) &&
				! empty( $custom_swatches->attribute_type ) &&
				'color' === $custom_swatches->attribute_type
			) {
				$attribute_key     = 'attribute-' . $custom_swatches->attribute_type . '-' . $attribute->attribute_id;
				$attributes_list[] = $attribute_key;
			}
		}

		return $attributes_list;
	}

	/**
	 * Generate Image Attributes.
	 *
	 * Generate Image attribribute for display condition.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function get_image_swatch_attributes() {
		$attributes = wc_get_attribute_taxonomies();

		$attributes_list = [];

		foreach ( $attributes as $attribute ) {
			$custom_swatches = $this->get_custom_attributes_data( $attribute->attribute_id );

			if (
				! empty( $custom_swatches ) &&
				! empty( $custom_swatches->attribute_type ) &&
				'image' === $custom_swatches->attribute_type
			) {
				$attribute_key     = 'attribute-' . $custom_swatches->attribute_type . '-' . $attribute->attribute_id;
				$attributes_list[] = $attribute_key;
			}
		}

		return $attributes_list;
	}

	/**
	 * Get Custom attributes data.
	 *
	 * Get Custom attributes data from database.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param string $id attribute id.
	 */
	public function get_custom_attributes_data( $id ) {
		$data = get_option( 'artbees_wc_attributes-' . $id . '' );
		$data = json_decode( $data );

		return $data;
	}

	/**
	 * Get Custom attribute terms data.
	 *
	 * Get Custom attribute term data from database.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param string $id attribute id.
	 * @param string $type attribute swatch type.
	 */
	public function get_custom_attribute_terms_data( $id, $type ) {
		$data = get_term_meta( $id, 'artbees_was_term_meta' );

		if ( empty( $data ) ) {
			return [];
		}

		return $data[0][ $type ];
	}

	/**
	 * Save elementor render_icon output content in variable.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param string $settings icon setting data.
	 */
	public function convert_elementor_icon_to_var( $settings ) {
		$icon = '';

		ob_start();
		Icons_Manager::render_icon( $settings );
		$icon .= ob_get_clean();

		return $icon;
	}

	/**
	 * Get query params.
	 *
	 * @since 1.1.0
	 * @param string $type filter type name.
	 * @param string $slug term's slug.
	 * @param string $display filter display type.
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function get_query_params( $type, $slug, $display ) {
		$nonce = sellkit_htmlspecialchars( INPUT_GET, 'nonce' );

		wp_verify_nonce( $nonce, 'sellkit_elementor' );

		$sellkit_filters_keys = [];

		$filters = filter_var_array( $_GET );

		if ( empty( $filters ) ) {
			return;
		}

		foreach ( $filters as $key => $param ) {
			if ( 'sellkit_filters' === $key ) {
				continue;
			}

			if ( 'products_cat' === $key ) {
				$key = 'category';
			}

			if ( 'products_tag' === $key ) {
				$key = 'tag';
			}

			$sellkit_filters_keys[ $key ] = explode( ' ', $param );
		}

		if ( ! isset( $sellkit_filters_keys[ $type ] ) ) {
			return;
		}

		if ( in_array( $type, [ 'search_text', 'custom_range' ], true ) ) {
			if ( isset( $sellkit_filters_keys[ $type ][1] ) ) {
				$sellkit_filters_keys[ $type ][0] = implode( ' ', $sellkit_filters_keys[ $type ] );
			}

			return $sellkit_filters_keys[ $type ][0];
		}

		$value = 'checked';

		if ( array_key_exists( (string) $display, self::FILTER_ACTIVATE_VALUES ) ) {
			$value = self::FILTER_ACTIVATE_VALUES[ $display ];
		}

		if ( in_array( (string) $slug, $sellkit_filters_keys[ $type ], true ) ) {
			return $value;
		}

	}

	/**
	 * Filter base constructor.
	 *
	 * Initializing the filter base class by hooking in widgets controls.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'elementor/element/sellkit-product-filter/filter_layout/before_section_end', [ $this, 'update_controls' ] );
		add_action( 'pre_get_posts', [ __NAMESPACE__ . '\Search_Text', 'search_query' ] );
	}
}
