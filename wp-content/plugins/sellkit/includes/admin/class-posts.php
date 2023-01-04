<?php

defined( 'ABSPATH' ) || die();

/**
 * Forms class.
 *
 * @since 1.1.0
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Sellkit_Admin_Posts {

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var Sellkit_Admin_Posts
	 */
	private static $instance = null;

	/**
	 * Sub Action.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $sub_action = '';

	/**
	 * Get a class instance.
	 *
	 * @since 1.1.0
	 *
	 * @return Sellkit_Admin_Posts Class instance.
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
		add_action( 'wp_ajax_sellkit_post_save', [ $this, 'save_post' ] );
		add_action( 'wp_ajax_sellkit_post_get', [ $this, 'get_post' ] );
		add_action( 'wp_ajax_sellkit_post_remove', [ $this, 'remove_post' ] );
	}

	/**
	 * Remove post.
	 *
	 * @since 1.1.0
	 */
	public function remove_post() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$post_id = sellkit_htmlspecialchars( INPUT_POST, 'post_id' );

		$result = wp_delete_post( $post_id );

		if ( empty( $result ) ) {
			wp_send_json_error( __( 'something went wrong', 'sellkit' ) );
		}

		wp_send_json_success( __( 'The process has been completed.', 'sellkit' ) );
	}

	/**
	 * Get post data.
	 *
	 * @since 1.1.0
	 */
	public function get_post() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$post_id = sellkit_htmlspecialchars( INPUT_GET, 'post_id' );

		$post_data = get_post_meta( $post_id );

		// Get post meta (without key) returns array by default, Now converts them to singles.
		$post_data = array_map( function( $meta ) {
			return $meta[0];
		}, $post_data );

		if ( ! empty( $post_data['conditions'] ) ) {
			$post_data['conditions'] = unserialize( $post_data['conditions'] ); // phpcs:ignore WordPress.PHP
		}

		if ( ! empty( $post_data['filters'] ) ) {
			$post_data['filters'] = unserialize( $post_data['filters'] ); // phpcs:ignore WordPress.PHP
		}

		if ( ! empty( $post_data['sellkit_steps'] ) ) {
			$post_data['sellkit_steps'] = unserialize( $post_data['sellkit_steps'] ); // phpcs:ignore WordPress.PHP
		}

		if ( ! empty( $post_data['nodes'] ) ) {
			delete_post_meta( $post_id, 'sellkit_steps' );
			unset( $post_data['sellkit_steps'] );

			$post_data['nodes'] = (object) unserialize( $post_data['nodes'] ); // phpcs:ignore WordPress.PHP
		}

		$post_data['data_is_converted'] = false;

		if ( empty( $post_data['nodes'] ) && ! empty( $post_data['sellkit_steps'] ) ) {
			$nodes = self::convert_steps_to_nodes( $post_data['sellkit_steps'] );

			update_post_meta( $post_id, 'nodes', $nodes );
			delete_post_meta( $post_id, 'sellkit_steps' );
			unset( $post_data['sellkit_steps'] );

			$post_data['nodes']             = $nodes;
			$post_data['data_is_converted'] = true;
		}

		$post_data['sellkit_post_title']  = html_entity_decode( get_the_title( $post_id ) );
		$post_data['sellkit_post_slug']   = get_post_field( 'post_name', $post_id );
		$post_data['sellkit_post_status'] = get_post_status( $post_id );

		wp_send_json_success( [ 'post_data' => $post_data ] );
	}

	/**
	 * Converts steps to nodes.
	 *
	 * @since 1.5.0
	 * @param array $steps Steps.
	 */
	public static function convert_steps_to_nodes( $steps ) {
		$nodes       = [];
		$final_steps = $steps;

		if ( empty( $steps[0]['targets'] ) ) {
			$final_steps = self::add_decision_node_before_upsells( $steps );
		}

		foreach ( $final_steps as $key => $step ) {
			$nodes[ $key ] = $step;

			if ( ! empty( $step['type']->key ) && 'decision' !== $step['type']->key ) {
				$nodes[ $key ]['targets'] = count( $final_steps ) > $key + 1 ? [ [ 'nodeId' => strval( $key + 1 ) ] ] : [];
			}

			if ( ! empty( $step['type']->key ) && 'upsell' === $step['type']->key && count( $final_steps ) > $key + 1 ) {
				$nodes[ $key ]['targets'] = [
					[
						'nodeId' => strval( $key + 1 )
					],
					[
						'nodeId' => strval( $key + 1 )
					]
				];
			}

			if ( ! empty( $step['type']->key ) && 'decision' === $step['type']->key ) {
				if ( is_object( $step['targets'][0] ) ) {
					$step['targets'][0] = (array) $step['targets'][0];
				}

				if ( is_object( $step['targets'][1] ) ) {
					$step['targets'][1] = (array) $step['targets'][1];
				}

				if ( empty( $final_steps[ $step['targets'][0]['nodeId'] ] ) ) {
					$nodes[ $key ]['targets'][0] = '';
				}

				if ( empty( $final_steps[ $step['targets'][1]['nodeId'] ] ) ) {
					$nodes[ $key ]['targets'][1] = '';
				}
			}

			$nodes[ $key ]['jsx'] = $step['title'];
		}

		return (object) $nodes;
	}

	/**
	 * Adds decision node before upsells.
	 *
	 * @since 1.5.0
	 * @param array $steps Steps.
	 */
	public static function add_decision_node_before_upsells( $steps ) {
		$final_steps = [];
		$upsell_num  = 0;

		foreach ( $steps as $key => $step ) {
			$step['type'] = (object) $step['type'];
			$step['data'] = (object) $step['data'];

			if ( ! empty( $step['type']->key ) && 'upsell' === $step['type']->key ) {
				$new_node_id = wp_insert_post( [
					'post_type' => Sellkit_Admin_Steps::SELLKIT_STEP_POST_TYPE,
					'post_title' => __( 'Decision', 'sellkit' ),
					'post_name' => sanitize_title( __( 'Decision', 'sellkit' ) ),
					'post_status' => 'publish',
				] );

				$type_object        = new stdClass();
				$type_object->title = esc_html__( 'Decision', 'sellkit' );
				$type_object->key   = 'decision';

				$decision_node = [
					'title' => esc_html__( 'Decision', 'sellkit' ),
					'type' => $type_object,
					'current_target_index' => 0,
					'page_id' => $new_node_id,
					'slug' => get_post_field( 'post_name', $new_node_id ),
					'funnel_id' => $step['funnel_id'],
					'targets' => [
						[
							'nodeId' => strval( $key + 1 + $upsell_num )
						],
						[
							'nodeId' => strval( $key + 2 + $upsell_num )
						]
					],
					'data' => [
						'conditions' => ! empty( $step['data']->conditions ) ? $step['data']->conditions : []
					]
				];

				update_post_meta( $new_node_id, 'step_data', $decision_node );

				$final_steps[] = $decision_node;

				$upsell_num++;
			}

			$final_steps[] = $step;
		}

		return $final_steps;
	}

	/**
	 * Save post.
	 *
	 * @since 1.1.0
	 */
	public function save_post() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$fields           = sellkit_post( 'fields' );
		$post_id          = sellkit_htmlspecialchars( INPUT_POST, 'post_id' );
		$post_type        = sellkit_htmlspecialchars( INPUT_POST, 'post_type' );
		$this->sub_action = sellkit_htmlspecialchars( INPUT_POST, 'sub_action' );

		$sanitized_fields = $this->sanitize_fields( $fields );

		$this->validate_fields( $sanitized_fields );

		$target_post_id = $this->handle_save_post( $sanitized_fields, $post_type, $post_id );

		if ( empty( $target_post_id ) ) {
			wp_send_json_error( [ __( 'something went wrong', 'sellkit' ) ] );
		}

		if ( $post_id ) {
			wp_send_json_success( [
				'post_id' => $post_id,
				'message' => __( 'The update process has been completed.', 'sellkit' ),
			] );
		}

		wp_send_json_success( [
			'post_id' => $target_post_id,
			'message' => __( 'The creation process has been completed.', 'sellkit' ),
		] );
	}

	/**
	 * Update or add new post.
	 *
	 * @param array  $data All data.
	 * @param string $post_type The post type.
	 * @param bool   $post_id The post Id.
	 * @return bool|integer
	 *
	 * @since 1.1.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	private function handle_save_post( $data, $post_type, $post_id = false ) {
		$title       = sanitize_text_field( $data['sellkit_post_title'] );
		$slug        = ! empty( $data['sellkit_post_slug'] ) ? sanitize_text_field( $data['sellkit_post_slug'] ) : '';
		$post_status = 'publish' === $data['sellkit_post_status'] ? 'publish' : 'draft';

		$post_args = [
			'post_title' => $title,
			'post_name' => $slug ?: sanitize_title( $title ),
			'post_type' => $post_type,
			'post_status' => $post_status,
		];

		if ( ! $post_id ) {
			$new_post_id = wp_insert_post( $post_args );
		}

		if ( $post_id ) {
			$post_args   = array_merge( $post_args, [ 'ID' => $post_id ] );
			$new_post_id = wp_update_post( $post_args );

			do_action( "sellkit_posts_after_saving_$post_type", $post_id );
		}

		if ( empty( $new_post_id ) || is_wp_error( $new_post_id ) ) {
			return false;
		}

		foreach ( $data as $name => $value ) {
			if ( 'sellkit_post_title' === $name || 'sellkit_post_status' === $name ) {
				continue;
			}

			update_post_meta( $new_post_id, $name, $value );

			if ( 'sellkit_steps' === $name || 'nodes' === $name ) {
				do_action( 'sellkit_funnels_update_steps', $value, $new_post_id );
			}
		}

		if ( empty( $data['conditions'] ) ) {
			delete_post_meta( $new_post_id, 'conditions' );
		}

		do_action( "sellkit_funnels_after_$this->sub_action", $post_id );

		if ( empty( $data['sellkit_steps'] ) ) {
			delete_post_meta( $new_post_id, 'sellkit_steps' );
		}

		if ( empty( $data['nodes'] ) ) {
			delete_post_meta( $new_post_id, 'nodes' );
		}

		return $new_post_id;
	}

	/**
	 * Sanitize fields.
	 *
	 * @param array $fields Fields.
	 *
	 * @return array
	 *
	 * @since 1.1.0
	 */
	private function sanitize_fields( $fields ) {
		$new_fields = [];

		foreach ( $fields as $key => $field ) {
			if ( is_array( $fields[ $key ] ) ) {
				$new_fields[ $key ] = $this->sanitize_fields( $fields[ $key ] );
			}

			$field_value = sanitize_meta( $key, $field, 'post' );

			if ( ! is_array( $fields[ $key ] ) ) {
				$new_fields[ $key ] = $field_value;
			}
		}

		if ( ! empty( $new_fields['conditions'] ) ) {
			$new_fields['conditions'] = sellkit_condition_row_sanitization( $new_fields['conditions'] );
		}

		if ( ! empty( $new_fields['filters'] ) ) {
			$new_fields['filters'] = sellkit_filter_row_sanitization( $new_fields['filters'] );
		}

		return $new_fields;
	}

	/**
	 * Validate fields.
	 *
	 * @param array $fields Fields.
	 *
	 * @since 1.1.0
	 */
	public function validate_fields( $fields ) {
		$errors = [];
		if ( empty( $fields['sellkit_post_title'] ) ) {
			$errors[] = __( 'Post title should not be empty.', 'sellkit' );
		}

		if ( empty( $errors ) ) {
			return;
		}

		wp_send_json_error( $errors );
	}
}

Sellkit_Admin_Posts::get_instance();
