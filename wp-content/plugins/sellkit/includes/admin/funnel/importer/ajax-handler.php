<?php

namespace Sellkit\Admin\Funnel\Importer;

defined( 'ABSPATH' ) || die();

/**
 * Class Ajax handler.
 *
 * @since 1.1.0
 */
class Ajax_Handler {

	/**
	 * Step importer object.
	 *
	 * @since 1.1.0
	 * @var Step_Importer
	 */
	public $step_importer;

	/**
	 * Ajax_Handler constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$this->step_importer = new Step_Importer();

		add_action( 'wp_ajax_sellkit_import_step_data', [ $this, 'import_steps_data' ] );
		add_action( 'wp_ajax_sellkit_funnel_import_funnel', [ $this, 'import_funnel' ] );
		add_action( 'wp_ajax_sellkit_funnel_get_funnel_data', [ $this, 'get_funnel_data' ] );
		add_action( 'wp_ajax_sellkit_funnel_get_steps', [ $this, 'get_steps' ] );
		add_action( 'wp_ajax_sellkit_funnel_get_funnels', [ $this, 'get_funnels' ] );
	}

	/**
	 * Imports step data.
	 *
	 * @since 1.1.0
	 */
	public function import_steps_data() {
		$nonce        = sellkit_htmlspecialchars( INPUT_GET, 'nonce' );
		$step_id      = sellkit_htmlspecialchars( INPUT_GET, 'step_id' );
		$funnel_id    = sellkit_htmlspecialchars( INPUT_GET, 'funnel_id' );
		$origin_node  = filter_input( INPUT_GET, 'origin_node', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$target_node  = filter_input( INPUT_GET, 'target_node', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$target_index = filter_input( INPUT_GET, 'target_index', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$origin_node  = isset( $origin_node ) ? $origin_node : 'none';
		$target_node  = ! empty( $target_node ) ? $target_node : 'none';

		wp_verify_nonce( $nonce, 'sellkit' );

		$data = $this->step_importer->import_from_api( $step_id );

		if ( empty( $data ) || is_wp_error( $data ) ) {
			wp_send_json_error( __( 'Something went wrong.', 'sellkit' ) );
		}

		$new_step_id = $this->step_importer->import_to_funnel( $data, $funnel_id, $origin_node, $target_node, $target_index );

		$this->step_importer->import_template( $data, $new_step_id );
		$this->step_importer->run();

		sleep( 3 );

		wp_send_json_success( esc_html__( 'The data has been imported successfully.' ) );
	}

	/**
	 * Gets steps.
	 *
	 * @since 1.1.0
	 */
	public function get_steps() {
		$nonce = sellkit_htmlspecialchars( INPUT_GET, 'nonce' );
		$type  = sellkit_htmlspecialchars( INPUT_GET, 'type' );

		wp_verify_nonce( $nonce, 'sellkit' );

		$response = wp_remote_get( "https://templates.getsellkit.com/wp-json/sellkit/v1/steps/type={$type}", [
			'timeout' => 10,
		] );

		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
			$step_data = json_decode( $response['body'] );

			wp_send_json_success( $step_data );
		}

		wp_send_json_error( esc_html__( 'Something went wrong', 'sellkit' ) );
	}

	/**
	 * Gets Funnels.
	 *
	 * @since 1.1.0
	 */
	public function get_funnels() {
		$nonce = sellkit_htmlspecialchars( INPUT_GET, 'nonce' );

		wp_verify_nonce( $nonce, 'sellkit' );

		$response = wp_remote_get( 'https://templates.getsellkit.com/wp-json/sellkit/v1/funnels', [
			'timeout' => 10,
		] );

		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
			$funnel_data = json_decode( $response['body'] );

			wp_send_json_success( $funnel_data );
		}

		wp_send_json_error( esc_html__( 'Something went wrong', 'sellkit' ) );
	}

	/**
	 * Gets funnel data.
	 *
	 * @since 1.1.0
	 */
	public function get_funnel_data() {
		$nonce     = sellkit_htmlspecialchars( INPUT_GET, 'nonce' );
		$funnel_id = sellkit_htmlspecialchars( INPUT_GET, 'funnel_id' );

		wp_verify_nonce( $nonce, 'sellkit' );

		$funnel_data = $this->import_funnel_data_from_api( $funnel_id );

		if ( ! empty( $funnel_data ) ) {
			wp_send_json_success( $funnel_data );
		}

		wp_send_json_error( esc_html__( 'Something went wrong.', 'sellkit' ) );
	}

	/**
	 * Imports step data.
	 *
	 * @since 1.1.0
	 */
	public function import_funnel() {
		$nonce     = sellkit_htmlspecialchars( INPUT_GET, 'nonce' );
		$funnel_id = sellkit_htmlspecialchars( INPUT_GET, 'funnel_id' );

		wp_verify_nonce( $nonce, 'sellkit' );

		$funnel_data = $this->import_funnel_data_from_api( $funnel_id );

		if ( empty( $funnel_data ) ) {
			wp_send_json_error( __( 'something went wrong', 'sellkit' ) );
		}

		$new_funnel_id = $this->import_funnel_by_data( $funnel_data );

		wp_send_json_success( [ 'funnelId' => $new_funnel_id ] );
	}

	/**
	 * Import funnel.
	 *
	 * @since 1.5.0
	 * @param object         $funnel_data funnel data.
	 * @param boolean|object $steps_data steps data.
	 */
	public function import_funnel_by_data( $funnel_data, $steps_data = false ) {
		$new_funnel_id = $this->create_new_funnel( $funnel_data );
		$steps         = $funnel_data->sellkit_steps;
		$new_step_data = [];

		foreach ( $steps as $step ) {

			if ( false !== $steps_data ) {
				$step_id = $step->page_id;
				$data    = $steps_data->$step_id;
			}

			if ( false === $steps_data ) {
				$data = $this->step_importer->import_from_api( $step->page_id );
			}

			if ( is_wp_error( $data ) ) {
				wp_send_json_error( __( 'Something went wrong.', 'sellkit' ) );
			}

			$new_step_id = $this->step_importer->import_to_funnel( $data, $new_funnel_id );

			$this->step_importer->import_template( $data, $new_step_id );

			$step->page_id   = $new_step_id;
			$step->funnel_id = $new_funnel_id;

			if ( ! empty( $step->data->products->list ) ) {
				unset( $step->data->products->list );
			}

			if ( ! empty( $step->bump[0]->data->products->list ) ) {
				unset( $step->bump[0]->data->products->list );
			}

			$new_step_data[] = (array) $step;
		}

		if ( false === $steps_data ) {
			update_post_meta( $new_funnel_id, 'sellkit_steps', $new_step_data );
		} else {
			update_post_meta( $new_funnel_id, 'nodes', $new_step_data );
		}

		$this->step_importer->run();

		return $new_funnel_id;
	}

	/**
	 * It creates new funnel.
	 *
	 * @param object $funnel_data Funnel data.
	 */
	public function create_new_funnel( $funnel_data ) {
		$new_funnel_id = wp_insert_post( [
			'post_type' => $funnel_data->post_type,
			'post_title' => $funnel_data->title,
			'post_name' => sanitize_title( $funnel_data->title ),
			'post_status' => 'draft',
		] );

		return $new_funnel_id;
	}

	/**
	 * Imports funnel data.
	 *
	 * @since 1.1.0
	 * @param int $funnel_id Funnel Id.
	 */
	public function import_funnel_data_from_api( $funnel_id ) {
		$response = wp_remote_get( "https://templates.getsellkit.com/wp-json/sellkit/v1/funnel/{$funnel_id}", [
			'timeout' => 10,
		] );

		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
			$funnel_data = json_decode( $response['body'] );

			return $funnel_data;
		}

		return false;
	}
}

new Ajax_Handler();
