<?php

namespace Sellkit_Pro\Contact_Segmentation\Conditions;

use Sellkit_Pro\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Upsell.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.5.0
 */
class Upsell extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.5.0
	 */
	public function get_name() {
		return 'upsell';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.5.0
	 */
	public function get_title() {
		return __( 'Upsell', 'sellkit' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.5.0
	 */
	public function get_type() {
		return self::SELLKIT_REACT_SELECT_CONDITION_VALUE;
	}

	/**
	 * Get the options
	 *
	 * @since 1.5.0
	 * @return array
	 */
	public function get_options() {
		$input_value = sellkit_htmlspecialchars( INPUT_GET, 'input_value' );

		return $this->get_upsells( $input_value );
	}

	/**
	 * Gets upsell.
	 *
	 * @since 1.5.0
	 * @param string $input_value Search input value.
	 */
	public function get_upsells( $input_value ) {
		$filtered_products = [];
		$args              = [
			'post_type' => 'sellkit_step',
			'post_status' => 'any',
			'posts_per_page' => 10,
			's' => sanitize_text_field( $input_value ),
			'meta_query' => [ // phpcs:ignore
				[
					'key'     => 'step_data',
					'value'   => 's:3:"key";s:6:"upsell"',
					'compare' => 'REGEXP',
				],
			],
		];

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$filtered_products[ get_the_ID() ] = html_entity_decode( get_the_title() ) . ' (ID: ' . get_the_ID() . ')';
			}
		}

		return $filtered_products;
	}

	/**
	 * It is pro feature or not.
	 *
	 * @since 1.5.0
	 */
	public function is_pro() {
		return true;
	}

	/**
	 * All the conditions are not searchable by default.
	 *
	 * @return false
	 * @since 1.5.0
	 */
	public function is_searchable() {
		return true;
	}

	/**
	 * Gets upsell value.
	 *
	 * @since 1.5.0
	 * @return mixed|string|void
	 */
	public function get_value() {
		return get_funnel_contact_value_by_column( 'upsell' );
	}
}
