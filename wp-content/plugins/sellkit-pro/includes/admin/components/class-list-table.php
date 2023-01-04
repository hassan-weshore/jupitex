<?php

defined( 'ABSPATH' ) || die();

/**
 * List Table component class.
 *
 * @since 1.1.0
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 */
class Sellkit_Admin_List_Table {

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var Sellkit_Admin_List_Table
	 */
	private static $instance = null;

	/**
	 * Get a class instance.
	 *
	 * @since 1.1.0
	 *
	 * @return Sellkit_Admin_List_Table Class instance.
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
		add_action( 'wp_ajax_sellkit_list_table_posts', [ $this, 'get_posts' ] );
		add_action( 'wp_ajax_sellkit_list_table_post', [ $this, 'handle_post' ] );
	}

	/**
	 * Get posts.
	 *
	 * @since 1.1.0
	 */
	public function get_posts() {
		check_ajax_referer( 'sellkit', 'nonce' );

		// Sanitize.
		$post_type      = sellkit_htmlspecialchars( INPUT_POST, 'post_type' );
		$paged          = filter_input( INPUT_POST, 'paged', FILTER_SANITIZE_NUMBER_INT );
		$posts_per_page = filter_input( INPUT_POST, 'posts_per_page', FILTER_SANITIZE_NUMBER_INT );
		$search_args    = filter_input( INPUT_POST, 'args', FILTER_DEFAULT, FILTER_FORCE_ARRAY );

		/**
		 * Filter List Table query arguments.
		 *
		 * @since 1.1.0
		 *
		 * @param array $args The query arguments.
		 */
		$args = apply_filters( "sellkit_list_table_{$post_type}_args", [
			'post_type' => $post_type,
			'paged' => $paged,
			'posts_per_page' => ! empty( $posts_per_page ) ? $posts_per_page : 20,
			'orderby' => 'ID',
			'order' => 'DESC',
		] );

		foreach ( $search_args as $search_arg ) {
			$args[ $search_arg['param'] ] = $search_arg['value'];
		}

		// Query.
		$query = new \WP_Query( $args );

		/**
		 * Filter List Table query posts.
		 *
		 * @since 1.1.0
		 *
		 * @param array $args The taxonomy arguments.
		 */
		$posts = apply_filters( "sellkit_list_table_{$post_type}_posts", $query->posts );

		/**
		 * Filter List Table columns.
		 *
		 * @since 1.1.0
		 *
		 * @param array $args The columns headings and values.
		 */
		$columns = apply_filters( "sellkit_list_table_{$post_type}_columns", [
			'labels' => [],
			'values' => [],
		], $posts );

		// Send response.
		wp_send_json_success( [
			'posts' => $posts,
			'max_num_pages' => $query->max_num_pages,
			'columns' => $columns,
		] );
	}

	/**
	 * Handle post actions.
	 *
	 * @since 1.1.0
	 */
	public function handle_post() {
		check_ajax_referer( 'sellkit', 'nonce' );

		// Sanitize.
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_FORCE_ARRAY );
		$sub_action = sellkit_htmlspecialchars( INPUT_POST, 'sub_action' );

		call_user_func( [ $this, "handle_post_{$sub_action}" ], $post );
	}

	/**
	 * Handle post toggle status.
	 *
	 * @since 1.1.0
	 * @param array $post Post Array.
	 */
	private function handle_post_toggle_status( $post ) {
		// Format proper post status.
		$post_status = ( 'publish' === $post['post_status'] ) ? 'draft' : 'publish';

		// Update post status.
		$result = wp_update_post( [
			'ID' => $post['ID'],
			'post_status' => $post_status,
		], true );

		do_action( "sellkit_after_toggle_status_{$post['post_type']}", $post['ID'], $post_status );

		// Handle error.
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result );
		}

		// Send success response.
		wp_send_json_success( $result );
	}

	/**
	 * Duplicate post.
	 *
	 * @since 1.1.0
	 * @param array $post Post Array.
	 */
	private function handle_post_duplicate_post( $post ) {
		$main_post_id = $post['ID'];

		unset( $post['ID'] );

		$post['post_title']  = sprintf( '%1s - %2s', esc_html( $post['post_title'] ), esc_html__( 'Clone', 'sellkit-pro' ) );
		$post['post_status'] = 'draft';

		$post_id = wp_insert_post( $post );

		$main_post_meta = get_post_custom( $main_post_id );

		foreach ( $main_post_meta as $key => $value ) {
			update_post_meta( $post_id, $key, maybe_unserialize( $value[0] ) );
		}

		// Handle error.
		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( $post_id );
		}

		// Send success response.
		wp_send_json_success( $post_id );
	}

	/**
	 * Handle post remove.
	 *
	 * @since 1.1.0
	 * @param array $post Post array.
	 */
	private function handle_post_remove( $post ) {
		// Delete post.
		$result = wp_delete_post( $post['ID'] );

		// Handle error.
		if ( ! is_object( $result ) ) {
			wp_send_json_error( $result );
		}

		// Send success response.
		wp_send_json_success( $result );
	}
}

Sellkit_Admin_List_Table::get_instance();
