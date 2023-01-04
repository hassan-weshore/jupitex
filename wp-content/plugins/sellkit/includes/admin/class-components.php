<?php

defined( 'ABSPATH' ) || die();

/**
 * Components class.
 *
 * @since 1.1.0
 */
class Sellkit_Admin_Components {

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var Sellkit_Admin_Components
	 */
	private static $instance = null;

	/**
	 * Get a class instance.
	 *
	 * @since 1.1.0
	 *
	 * @return Sellkit_Admin_Components Class instance.
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
		add_filter( 'sellkit_list_table_page_columns', [ $this, 'modify_page_list_table_columns' ], 10, 2 );
		add_filter( 'sellkit_list_table_sellkit-coupons_columns', [ $this, 'modify_coupon_list_table_columns' ], 10, 2 );
		add_filter( 'sellkit_list_table_sellkit-discount_columns', [ $this, 'modify_discount_list_table_columns' ], 10, 2 );
		add_filter( 'sellkit_list_table_sellkit-alert_columns', [ $this, 'modify_alert_list_table_columns' ], 10, 2 );
		add_filter( 'sellkit_list_table_sellkit-funnels_columns', [ $this, 'modify_funnel_list_table_columns' ], 10, 2 );
	}

	/**
	 * Modify page list table columns.
	 *
	 * @since 1.1.0
	 * @param array $columns Columns.
	 * @param array $posts Posts.
	 */
	public function modify_page_list_table_columns( $columns, $posts ) {
		$columns['labels'] = [
			__( 'Header Type (Meta option)', 'sellkit' ),
			__( 'Header Behavior (Meta option)', 'sellkit' ),
		];

		foreach ( $posts as $post ) {
			$columns['values'][ "post_{$post->ID}" ] = [
				get_field( 'jupiterx_header_type', $post->ID ),
				get_field( 'jupiterx_header_behavior', $post->ID ),
			];
		}

		return $columns;
	}

	/**
	 * Modify coupon list table columns.
	 *
	 * @since 1.1.0
	 * @param array $columns Columns.
	 * @param array $posts Posts.
	 */
	public function modify_coupon_list_table_columns( $columns, $posts ) {
		$columns['labels'] = [
			esc_html__( 'Author', 'sellkit' ),
			esc_html__( 'Created at', 'sellkit' ),
		];

		foreach ( $posts as $post ) {
			$author_id                               = get_post_field( 'post_author', $post->ID );
			$columns['values'][ "post_{$post->ID}" ] = [
				[
					'name' => get_the_author_meta( 'display_name', $author_id ),
					'param' => 'author__in',
					'type' => 'search',
					'value' => $author_id,
				],
				get_the_date( 'Y/m/d h:i A', $post->ID ),
			];
		}

		return $columns;
	}

	/**
	 * Modify discount list table columns.
	 *
	 * @since 1.1.0
	 * @param array $columns Columns.
	 * @param array $posts Posts.
	 */
	public function modify_discount_list_table_columns( $columns, $posts ) {
		$columns['labels'] = [
			__( 'Author', 'sellkit' ),
			__( 'Created at', 'sellkit' ),
		];

		foreach ( $posts as $post ) {
			$author_id                               = get_post_field( 'post_author', $post->ID );
			$columns['values'][ "post_{$post->ID}" ] = [
				[
					'name' => get_the_author_meta( 'display_name', $author_id ),
					'param' => 'author__in',
					'type' => 'search',
					'value' => $author_id,
				],
				get_the_date( 'Y/m/d h:i A', $post->ID ),
			];
		}

		return $columns;
	}

	/**
	 * Modify alert list table columns.
	 *
	 * @since 1.1.0
	 * @param array $columns Columns.
	 * @param array $posts Posts.
	 */
	public function modify_alert_list_table_columns( $columns, $posts ) {
		$columns['labels'] = [
			esc_html__( 'Author', 'sellkit' ),
			esc_html__( 'Created at', 'sellkit' ),
		];

		foreach ( $posts as $post ) {
			$author_id                               = get_post_field( 'post_author', $post->ID );
			$columns['values'][ "post_{$post->ID}" ] = [
				[
					'name' => get_the_author_meta( 'display_name', $author_id ),
					'param' => 'author__in',
					'type' => 'search',
					'value' => $author_id,
				],
				get_the_date( 'Y/m/d h:i A', $post->ID ),
			];
		}

		return $columns;
	}


	/**
	 * Modify discount list table columns.
	 *
	 * @since 1.1.0
	 * @param array $columns Columns.
	 * @param array $posts Posts.
	 */
	public function modify_funnel_list_table_columns( $columns, $posts ) {
		$columns['labels'] = [
			__( 'Author', 'sellkit' ),
			__( 'Created at', 'sellkit' ),
		];

		foreach ( $posts as $post ) {
			$author_id                               = get_post_field( 'post_author', $post->ID );
			$columns['values'][ "post_{$post->ID}" ] = [
				[
					'name' => get_the_author_meta( 'display_name', $author_id ),
					'param' => 'author__in',
					'type' => 'search',
					'value' => $author_id,
				],
				get_the_date( 'Y/m/d h:i A', $post->ID ),
			];
		}

		return $columns;
	}
}

Sellkit_Admin_Components::get_instance();
