<?php
/**
 * Add Product Price Filter.
 *
 * @package JupiterX_Core\sellkit
 * @since 1.1.0
 */

namespace Sellkit_Pro\Elementor\Modules\Product_Filter\Filters;

defined( 'ABSPATH' ) || die();

use Elementor\Plugin as Elementor;
use Elementor\Controls_Manager;

/**
 * Price.
 *
 * Initializing the Price by extending item base abstract class.
 *
 * @since 1.1.0
 */
class Price extends Filter_Base {

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
		return 'price';
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
		return __( 'Price', 'sellkit-pro' );
	}

	/**
	 * Update filter.
	 *
	 * Update filter Settings.
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
			'price_display' => [
				'name' => 'price_display',
				'label' => __( 'Display as', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'checkbox',
				'options' => [
					'checkbox' => __( 'Checkbox List', 'sellkit-pro' ),
					'links' => __( 'Links', 'sellkit-pro' ),
					'button' => __( 'Button', 'sellkit-pro' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'price_custom_range' => [
				'name' => 'price_custom_range',
				'label' => __( 'Include custom range', 'sellkit-pro' ),
				'type' => 'switcher',
				'label_off' => __( 'No', 'sellkit-pro' ),
				'label_on' => __( 'Yes', 'sellkit-pro' ),
				'default' => 'yes',
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'price_distance' => [
				'name' => 'price_distance',
				'label' => __( 'Range distance', 'sellkit-pro' ),
				'type' => 'number',
				'min' => 0,
				'max' => 999999,
				'default' => 1000,
				'frontend_available' => true,
				'condition' => [
					'filter_type' => $this->get_type(),
				],
			],
			'price_logic' => [
				'name' => 'price_logic',
				'label' => __( 'Logic', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'and',
				'options' => [
					'and' => __( 'And', 'sellkit-pro' ),
					'or' => __( 'Or', 'sellkit-pro' ),
				],
				'condition' => [
					'filter_type' => $this->get_type(),
					'price_display' => [ 'checkbox', 'button' ],
				],
			],
		];

		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $filter_controls );
		$widget->update_control( 'filters', $control_data );
	}


	/**
	 * Get min and max prices.
	 *
	 * Get min and max prices in all products.
	 *
	 * @since 1.1.0
	 * @access private
	 */
	private function get_filtered_price() {
		global $wpdb;

		$args = wc()->query->get_main_query();

		$tax_query  = isset( $args->tax_query->queries ) ? $args->tax_query->queries : [];
		$meta_query = isset( $args->query_vars['meta_query'] ) ? $args->query_vars['meta_query'] : [];

		foreach ( $meta_query + $tax_query as $key => $query ) {
			if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
				unset( $meta_query[ $key ] );
			}
		}

		$meta_query = new \WP_Meta_Query( $meta_query );
		$tax_query  = new \WP_Tax_Query( $tax_query );

		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

		$sql  = "SELECT min( FLOOR( price_meta.meta_value ) ) as min_price, max( CEILING( price_meta.meta_value ) ) as max_price FROM {$wpdb->posts} ";// phpcs:ignore
		$sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'] . $meta_query_sql['join'];// phpcs:ignore
		$sql .= " 	WHERE {$wpdb->posts}.post_type IN ('product')
				AND {$wpdb->posts}.post_status = 'publish'
				AND price_meta.meta_key IN ('_price')
				AND price_meta.meta_value > '' ";// phpcs:ignore
		$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];// phpcs:ignore

		// phpcs:ignore
		$prices = $wpdb->get_row( $sql );

		return [
			'min' => floor( $prices->min_price ),
			'max' => ceil( $prices->max_price ),
		];
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

		$prices          = $this->get_filtered_price();
		$steps           = $prices['min'];
		$modified_prices = [];
		$terms           = [];

		$modified_prices[] = $prices['min'];

		while ( $steps < $prices['max'] - $field['price_distance'] ) {
			$steps            += $field['price_distance'];
			$modified_prices[] = $steps;
		}

		$modified_prices[] = $prices['max'];

		$modified_prices_count = count( $modified_prices ) - 1;

		for ( $i = 0; $i < $modified_prices_count; ++$i ) {
			$term    = $modified_prices[ $i ] . '-' . $modified_prices[ $i + 1 ];
			$terms[] = (object) [
				'taxonomy' => 'price',
				'term_id' => $term,
				'slug' => $term,
				'name' => $modified_prices[ $i ] . ' - ' . $modified_prices[ $i + 1 ],
			];
		}

		$html = '<div class="sellkit-product-filter-item-wrapper sellkit-product-filter-price">';

		$html .= $this->render_filter_heading( $heading );

		$render_function = 'render_' . $field['price_display'];

		$html .= '<div class="sellkit-product-filter-price-wrapper">';

		if ( 'yes' === $field['price_custom_range'] ) {
			$html .= $this->render_custom_range( $field );
		}

		$html .= $this->$render_function( $terms, $field );

		$html .= '</div>';
		$html .= '</div>';

		echo $html;
	}
}
