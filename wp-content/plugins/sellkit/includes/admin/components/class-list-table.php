<?php

defined( 'ABSPATH' ) || die();

use Elementor\Plugin;
use Sellkit\Admin\Funnel\Importer\Ajax_Handler;

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

		if ( is_array( $search_args ) && ! empty( $search_args ) ) {
			foreach ( $search_args as $search_arg ) {
				$args[ $search_arg['param'] ] = $search_arg['value'];
			}
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

		$post['post_title']  = sprintf( '%1s - %2s', esc_html( $post['post_title'] ), esc_html__( 'Clone', 'sellkit' ) );
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
		do_action( 'sellkit_funnels_after_delete_posts_' . $post['post_type'], $post );
		// Delete post.
		$result = wp_delete_post( $post['ID'] );

		// Handle error.
		if ( ! is_object( $result ) ) {
			wp_send_json_error( $result );
		}

		// Send success response.
		wp_send_json_success( $result );
	}

	/**
	 * Duplicate funnel item.
	 *
	 * @since 1.5.0
	 * @param array $post Post Array.
	 */
	private function handle_post_duplicate_single_funnel( $post ) {
		$post_id      = $post['ID'];
		$parent_title = sprintf( '%1s - %2s', esc_html( $post['post_title'] ), esc_html__( 'Clone', 'sellkit' ) );
		$funnel_data  = get_post_meta( $post_id, 'nodes', true );
		$nodes        = true;

		if ( empty( $funnel_data ) ) {
			$funnel_data = get_post_meta( $post_id, 'sellkit_steps', true );
			$nodes       = false;
		}

		$funnel_slug = get_post_meta( $post_id, 'sellkit_post_slug', true );

		// Let's create parent funnel post first.
		$new_funnel_id = wp_insert_post( [
			'post_title'  => $parent_title,
			'post_status' => 'draft',
			'post_type'   => 'sellkit-funnels',
			'meta_input'   => [
				'sellkit_post_slug' => $funnel_slug,
			],
		] );

		// Loop through old parent data and create steps posts of new funnel.
		foreach ( $funnel_data as $key => $funnel_item ) {
			$old_id = $funnel_item['page_id'];

			// Create steps.
			$title = $funnel_item['title'];
			$args  = [
				'post_type'   => 'sellkit_step',
				'post_title'  => esc_html( $title ),
				'post_status' => get_post_status( $old_id ),
			];

			$args = $this->handle_post_content( $args, $old_id );

			$new_id = wp_insert_post( $args );

			// Update new parent funnel data with new steps id.
			$funnel_data[ $key ]['page_id']   = $new_id;
			$funnel_data[ $key ]['funnel_id'] = $new_funnel_id;

			// Add old steps meta data to new steps.
			$old_data = get_post_meta( $old_id );

			foreach ( $old_data as $key => $data ) {
				if ( 'step_data' === $key ) {
					$data[0] = str_replace( $post_id, $new_funnel_id, $data[0] );
				}

				update_post_meta( $new_id, $key, maybe_unserialize( $data[0] ) );
			}

			if ( class_exists( 'Elementor\Plugin' ) ) {
				// Also save post in elementor way. after all it's elementor post.
				$document = Plugin::$instance->documents->get( $new_id );
				$document->save( [] );
			}
		}

		// At the end update parent main meta.
		if ( false === $nodes ) {
			update_post_meta( $new_funnel_id, 'sellkit_steps', $funnel_data );
		} else {
			update_post_meta( $new_funnel_id, 'nodes', $funnel_data );
		}

		wp_send_json_success();
	}

	/**
	 * Handle post content for templates without elementor.
	 *
	 * @param array   $args   funnel nodes arguments.
	 * @param integer $old_id node id.
	 * @since 1.5.0
	 * @return array
	 */
	private function handle_post_content( $args, $old_id ) {
		if ( empty( $old_id ) ) {
			return $args;
		}

		$is_elementor = get_post_meta( $old_id, '_elementor_data', true );

		if ( ! empty( $is_elementor ) ) {
			return $args;
		}

		$post_content = get_post( $old_id );
		$content      = $post_content->post_content;

		return array_merge( $args, [ 'post_content' => $content ] );
	}

	/**
	 * Export single funnel.
	 *
	 * @since 1.5.0
	 * @param array $post Post Array.
	 */
	private function export_single_funnel( $post ) {
		$post_id                         = $post['ID'];
		$data['funnel']['id']            = $post_id;
		$data['funnel']['name']          = $post['post_name'];
		$data['funnel']['title']         = $post['post_title'];
		$data['funnel']['post_type']     = $post['post_type'];
		$data['funnel']['post_status']   = $post['post_status'];
		$data['funnel']['sellkit_steps'] = get_post_meta( $post_id, 'nodes', true );

		// Integration with flowchart.
		if ( empty( $data['funnel']['sellkit_steps'] ) ) {
			$data['funnel']['sellkit_steps'] = get_post_meta( $post_id, 'sellkit_steps', true );
		}

		// Check for steps.
		if ( ! is_array( $data['funnel']['sellkit_steps'] ) ) {
			return $data;
		}

		foreach ( $data['funnel']['sellkit_steps'] as $step ) {
			$id          = $step['page_id'];
			$post        = get_post( $id );
			$export_data = get_post_meta( $id );

			$data['step'][ $id ]['ID']           = $post->ID;
			$data['step'][ $id ]['name']         = $post->post_name;
			$data['step'][ $id ]['title']        = $post->post_title;
			$data['step'][ $id ]['post_type']    = $post->post_type;
			$data['step'][ $id ]['post_status']  = $post->post_status;
			$data['step'][ $id ]['post_content'] = $post->post_content;
			$data['step'][ $id ]['page_id']      = $id;

			foreach ( $export_data as $key => $meta ) {
				if ( '_elementor_data' === $key && defined( 'ELEMENTOR_VERSION' ) ) {
					$document      = Plugin::$instance->documents->get( $id );
					$template_data = $document->get_export_data();

					$data['step'][ $id ]['meta'][ $key ] = wp_json_encode( $template_data['content'] );
					continue;
				}

				$data['step'][ $id ]['meta'][ $key ] = $meta[0];
			}
		}

		return $data;
	}

	/**
	 * Handle exporting single funnel by ajax.
	 *
	 * @since 1.5.0
	 * @param array $post Post Array.
	 */
	private function handle_post_export_single_funnel( $post ) {
		$data         = [];
		$data['type'] = 'single';
		$data['data'] = $this->export_single_funnel( $post );

		wp_send_json_success( $data );
	}

	/**
	 * Handle exporting single funnel by ajax.
	 *
	 * @since 1.5.0
	 */
	private function handle_post_export_all_funnel() {
		$data         = [];
		$data['type'] = 'multi';

		$args = [
			'post_type'      => 'sellkit-funnels',
			'posts_per_page' => -1,
		];

		$posts = new \WP_Query( $args );
		$posts = $posts->posts;

		if ( empty( $posts ) ) {
			wp_send_json_error();
		}

		foreach ( $posts as $post ) {
			$post = (array) $post;

			$data['data'][] = $this->export_single_funnel( $post );
		}

		wp_send_json_success( $data );
	}

	/**
	 * Handle import funnel.
	 *
	 * @since 1.5.0
	 */
	private function handle_post_import_funnel() {
		$json = filter_input( INPUT_POST, 'funnel', FILTER_DEFAULT );
		$data = json_decode( $json );

		$ajax_handler = new Ajax_Handler();

		if ( 'single' === $data->type ) {
			$funnel_data = $data->data->funnel;
			$steps_data  = $data->data->step;
			$ajax_handler->import_funnel_by_data( $funnel_data, $steps_data );
		}

		if ( 'multi' === $data->type ) {
			foreach ( $data->data as $item ) {
				$funnel_data = $item->funnel;
				$steps_data  = $item->step;
				$ajax_handler->import_funnel_by_data( $funnel_data, $steps_data );
			}
		}
	}
}

Sellkit_Admin_List_Table::get_instance();
