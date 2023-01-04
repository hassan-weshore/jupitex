<?php
namespace JupiterX_Core\Raven\Modules\Advanced_Posts;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Base\Module_Base;
use JupiterX_Core\Raven\Utils;

class Module extends Module_Base {
	public function __construct() {
		parent::__construct();

		add_action( 'wp_ajax_raven_render_advanced_posts', [ $this, 'render_posts' ] );
		add_action( 'wp_ajax_nopriv_raven_render_advanced_posts', [ $this, 'render_posts' ] );
	}

	public function get_widgets() {
		return [ 'advanced-posts' ];
	}

	public function render_posts() {
		check_ajax_referer( 'jupiterx-core-raven', 'nonce' );

		$post_id       = filter_input( INPUT_POST, 'post_id' );
		$model_id      = filter_input( INPUT_POST, 'model_id' );
		$paged         = filter_input( INPUT_POST, 'paged' );
		$category      = filter_input( INPUT_POST, 'category' );
		$archive_query = filter_input( INPUT_POST, 'archive_query' );

		if ( false !== $archive_query ) {
			$archive_query          = json_decode( $archive_query, true );
			$archive_query['paged'] = $paged;
		}

		if ( empty( $post_id ) ) {
			wp_send_json_error( new \WP_Error( 'no_post_id', 'No post_id defined.' ) );
		}

		if ( empty( $model_id ) ) {
			wp_send_json_error( new \WP_Error( 'no_model_id', 'No model_id defined.' ) );
		}

		$elementor = \Elementor\Plugin::$instance;

		$meta = $elementor->documents->get( $post_id )->get_elements_data();

		$posts_data = Utils::find_element_recursive( $meta, $model_id );

		if ( ! empty( $paged ) ) {
			$posts_data['settings']['paged'] = intval( $paged );
		}

		if ( ! empty( $category ) ) {
			$posts_data['settings']['category'] = intval( $category );
		}

		$widget = $elementor->elements_manager->create_element_instance( $posts_data );

		if ( ! $widget ) {
			wp_send_json_error();
		}

		$archive_query['post_status'] = 'publish';

		$queried_posts = $widget->ajax_get_queried_posts( $archive_query );

		wp_send_json_success( $queried_posts );
	}

	public static function get_post_types() {
		$post_types = get_post_types( [ 'show_in_nav_menus' => true ], 'objects' );

		foreach ( $post_types as $post_type ) {
			if ( 'product' === $post_type->name ) {
				continue;
			}

			$items[ $post_type->name ] = $post_type->labels->name;
		}

		return $items;
	}
}
