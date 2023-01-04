<?php
/**
 * Add Product Search Text Filter.
 *
 * @package JupiterX_Core\sellkit
 * @since 1.1.0
 */

namespace Sellkit_Pro\Elementor\Modules\Product_Filter\Filters;

defined( 'ABSPATH' ) || die();

use Elementor\Plugin as Elementor;

/**
 * Search Text.
 *
 * Initializing the Search Text by extending item base abstract class.
 *
 * @since 1.1.0
 */
class Search_Text extends Filter_Base {
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
		return 'search_text';
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
		return __( 'Search', 'sellkit-pro' );
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
			'search_text_label' => [
				'name' => 'search_text_label',
				'label' => __( 'Input label', 'sellkit-pro' ),
				'type' => 'text',
				'default' => __( 'Search for something', 'sellkit-pro' ),
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'search_text_placeholder' => [
				'name' => 'search_text_placeholder',
				'label' => __( 'Input placeholder', 'sellkit-pro' ),
				'type' => 'text',
				'default' => __( 'Type a keyword', 'sellkit-pro' ),
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'search_text_searchby' => [
				'name' => 'search_text_searchby',
				'label' => __( 'Search by', 'sellkit-pro' ),
				'type' => 'select2',
				'multiple' => true,
				'label_block' => true,
				'options' => [
					'title' => __( 'Title', 'sellkit-pro' ),
					'content' => __( 'Content', 'sellkit-pro' ),
					'categories' => __( 'Categories', 'sellkit-pro' ),
					'tags' => __( 'Tags', 'sellkit-pro' ),
					'attributes' => __( 'Attributes', 'sellkit-pro' ),
				],
				'default' => [ 'title' ],
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'search_text_icon_location' => [
				'name' => 'search_text_icon_location',
				'label' => __( 'Search icon location', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'right',
				'options' => [
					'right' => __( 'Right', 'sellkit-pro' ),
					'left' => __( 'Left', 'sellkit-pro' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'search_text_icon' => [
				'name' => 'search_text_icon',
				'label' => __( 'Icon', 'sellkit-pro' ),
				'type' => 'icons',
				'fa4compatibility' => 'icons',
				'default' => [
					'value' => 'fa fa-search',
					'library' => 'solid',
				],
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

		$placeholder = $field['search_text_placeholder'];
		$label       = $field['search_text_label'];

		$search_by = [];

		if ( ! empty( $field['search_text_searchby'] ) ) {
			$search_by = $field['search_text_searchby'];
		}

		$search_query_value = $this->get_query_params( 'search_text', '0', '' );

		$search_icon_align = "sellkit-product-filter-search-{$field['search_text_icon_location']}";

		$icon = $this->convert_elementor_icon_to_var( $field['search_text_icon'] );

		$html = '<div class="sellkit-product-filter-item-wrapper sellkit-product-filter-search product-filter-item">';

		$html .= $this->render_filter_heading( $heading );

		$html .= "<div class='sellkit-product-filter-search-text-wrapper'>";

		$html .= "<label>$label</label>";
		$html .= "<div class='sellkit-product-filter-search-wrapper sellkit-product-filter-form-type {$search_icon_align}'>";
		$html .= sprintf(
			'<form id="search-text-%2$s" class="sellkit-product-filter-search-text" data-logic="%1$s" data-type="%2$s" data-filter=%3$s >',
			esc_attr( 'or' ),
			esc_attr( $field['filter_type'] ),
			esc_attr( $field['_id'] )
		);
		$html .= "<input type='text' name='search_name' value='$search_query_value' placeholder='$placeholder' autocomplete='off' required />";

		if ( in_array( 'title', $search_by, true ) ) {
			$html .= '<input type="hidden" name="product-filter-title" value="1" />';
		}

		if ( in_array( 'content', $search_by, true ) ) {
			$html .= '<input type="hidden" name="product-filter-content" value="1" />';
		}

		if ( in_array( 'categories', $search_by, true ) ) {
			$html .= '<input type="hidden" name="product-filter-categories" value="1" />';
		}

		if ( in_array( 'tags', $search_by, true ) ) {
			$html .= '<input type="hidden" name="product-filter-tags" value="1" />';
		}

		if ( in_array( 'attributes', $search_by, true ) ) {
			$html .= '<input type="hidden" name="product-filter-attributes" value="1" />';
		}

		$html .= '<button type="submit" form="search-text-' . esc_attr( $field['filter_type'] ) . '">' . $icon . '</button>';
		$html .= '</form>';
		$html .= '</div>';

		$html .= '</div>';
		$html .= '</div>';

		echo $html;
	}

	/**
	 * Change Search query.
	 *
	 * Change Search query for product filter Search Text.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param object $query Default query.
	 */
	public static function search_query( $query ) {
		if ( empty( sellkit_htmlspecialchars( INPUT_GET, 'product-filter-only' ) ) ) {
			return $query;
		}

		if ( $query->is_search() ) {
			$query->set( 'post_type', 'product' );
		}
	}
}
