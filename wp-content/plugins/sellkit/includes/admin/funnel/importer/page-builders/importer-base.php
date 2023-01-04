<?php

namespace Sellkit\Admin\Funnel\Importer\Page_Builder;

use Elementor\Plugin;

defined( 'ABSPATH' ) || die();

/**
 * Class Funnel Importer.
 *
 * @since 1.1.0
 */
class Importer_Base extends \WP_Background_Process {

	/**
	 * Importing Process
	 *
	 * @var string
	 */
	protected $action = 'sellkit_import_template_process';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Ids data.
	 * @return mixed
	 */
	public function task( $args ) {
		$data        = $args['data'];
		$new_step_id = $args['new_step_id'];

		if ( class_exists( 'Elementor\Plugin' ) ) {
			new Elementor_Importer( $data, $new_step_id );
		}

		return false;
	}

	/**
	 * Imports the step
	 *
	 * @since 1.1.0
	 * @param string $step_id Step id.
	 */
	public function import_from_api( $step_id ) {
		if ( empty( $step_id ) ) {
			return new \WP_Error( 'sellkit_step_importer_has_no_step_id', esc_html__( 'Does not have any step id for importing', 'sellkit' ) );
		}

		$response = wp_remote_get( "https://templates.getsellkit.com/wp-json/sellkit/v1/step/{$step_id}" );

		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
			$step_data = json_decode( $response['body'] );

			return $step_data;
		}

		new \WP_Error( 'sellkit_funnel_step_importing_data_error', esc_html__( 'Something went wrong in importing data from API.', 'sellkit' ) );
	}

	/**
	 * Imports data to funnel.
	 *
	 * @since 1.1.0
	 * @param object $data Data.
	 * @param int    $funnel_id Funnel id.
	 */
	public function import_to_funnel( $data, $funnel_id ) {
		$meta_data = (array) $data->meta;
		$step_data = unserialize( $meta_data['step_data'] ); //phpcs:ignore

		unset( $step_data['funnel_id'] );
		unset( $step_data['number'] );
		unset( $step_data['data'] );

		$funnel_step_data     = get_post_meta( $funnel_id, 'sellkit_steps', true );
		$funnel_step_data     = ! empty( $funnel_step_data ) ? $funnel_step_data : [];
		$new_step_data        = $this->create_new_step_page( $step_data );
		$new_funnel_step_data = array_merge( $funnel_step_data, [ $new_step_data ] );

		update_post_meta( $funnel_id, 'sellkit_steps', $new_funnel_step_data );

		$args = array(
			'ID'           => $new_step_data['page_id'],
			'post_content' => $data->post_content,
		);

		wp_update_post( $args );

		foreach ( $data->meta as $meta_key => $meta_value ) {
			if ( '_elementor_data' === $meta_data ) {
				continue;
			}

			if ( is_serialized( $meta_value, true ) ) {
				$meta_value = maybe_unserialize( stripslashes( $meta_value ) );
			}

			update_post_meta( $new_step_data['page_id'], $meta_key, $meta_value );
		}

		return $new_step_data['page_id'];
	}

	/**
	 * Filter step data before saving.
	 *
	 * @since 1.1.0
	 * @param array $last_step Step data.
	 */
	public function create_new_step_page( $last_step ) {
		$new_step_id = wp_insert_post( [
			'post_type' => \Sellkit_Admin_Steps::SELLKIT_STEP_POST_TYPE,
			'post_title' => $last_step['title'],
			'post_name' => sanitize_title( $last_step['title'] ),
			'post_status' => 'publish',
		] );

		$last_step['page_id'] = $new_step_id;
		$last_step['slug']    = get_post_field( 'post_name', $new_step_id );

		return $last_step;
	}
}
