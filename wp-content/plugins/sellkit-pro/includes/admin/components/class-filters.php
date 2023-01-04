<?php

defined( 'ABSPATH' ) || die();

/**
 * Filters component class.
 *
 * @since 1.1.0
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 */
class Sellkit_Admin_Filters {

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var Sellkit_Admin_Filters
	 */
	private static $instance = null;

	/**
	 * Get a class instance.
	 *
	 * @since 1.1.0
	 *
	 * @return Sellkit_Admin_Filters Class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_sellkit_filters_options', [ $this, 'get_options' ] );
	}

	/**
	 * Get Conditions options
	 *
	 * @since 1.1.0
	 */
	public function get_options() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$filter_type = sellkit_htmlspecialchars( INPUT_GET, 'filter_type' );
		$input_value = sellkit_htmlspecialchars( INPUT_GET, 'input_value' );

		$options = call_user_func( [ $this, "get_filtered_{$filter_type}" ], $input_value );

		wp_send_json_success( $options );
	}

	/**
	 * Get Filtered products.
	 *
	 * @since 1.1.0
	 * @param string $input_value Input value.
	 */
	private function get_filtered_products( $input_value ) {
		$filtered_products = [];
		$args              = [
			'post_type' => 'product',
			'post_status' => 'any',
			's' => $input_value,
		];

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$filtered_products[] = [
					'label' => html_entity_decode( get_the_title() ),
					'value' => get_the_ID(),
				];
			}
		}

		return $filtered_products;
	}

	/**
	 * Get Filtered products.
	 *
	 * @since 1.1.0
	 * @param string $input_value Input value.
	 */
	private function get_filtered_tags( $input_value ) {
		$filtered_tags = [];

		$terms = get_terms( [
			'taxonomy' => 'product_tag',
			'hide_empty' => false,
			'search' => $input_value,
		] );

		foreach ( $terms as $tag ) {
			$filtered_tags[] = [
				'label' => html_entity_decode( $tag->name ),
				'value' => $tag->term_id,
			];
		}

		return $filtered_tags;
	}

	/**
	 * Get Filtered products.
	 *
	 * @since 1.1.0
	 * @param string $input_value Input value.
	 */
	private function get_filtered_categories( $input_value ) {
		$filtered_tags = [];

		$terms = get_terms( [
			'taxonomy' => 'product_cat',
			'hide_empty' => false,
			'search' => $input_value,
		] );

		foreach ( $terms as $tag ) {
			$filtered_tags[] = [
				'label' => htmlspecialchars_decode( $tag->name ),
				'value' => $tag->term_id,
			];
		}

		return $filtered_tags;
	}
}

Sellkit_Admin_Filters::get_instance();
