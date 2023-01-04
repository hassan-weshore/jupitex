<?php

namespace Sellkit\Admin\Funnel\Importer;

use Sellkit\Admin\Funnel\Importer\Page_Builder\Importer_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Step Importer.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @since 1.1.0
 */
class Step_Importer {

	/**
	 * Importer object.
	 *
	 * @var object
	 */
	public static $importer;

	/**
	 * Step data.
	 *
	 * @var array
	 */
	public $step_data;

	/**
	 * Step_Importer constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		self::$importer = new Importer_Base();
	}

	/**
	 * Importing template.
	 *
	 * @since 1.1.0
	 * @param object $data Funnel id.
	 * @param string $new_step_id New step id.
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function import_template( $data, $new_step_id ) {
		self::$importer->push_to_queue( [
			'data' => $data,
			'new_step_id' => $new_step_id,
		] );
	}

	/**
	 * Runs all importing.
	 *
	 * @since 1.1.0
	 */
	public function run() {
		self::$importer->save()->dispatch();
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

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) && 200 === (int) $response_code ) {
			$step_data = json_decode( $response['body'] );

			return $step_data;
		}

		new \WP_Error( 'sellkit_funnel_step_importing_data_error', esc_html__( 'Something went wrong in importing data from API.', 'sellkit' ) );
	}

	/**
	 * Imports data to funnel.
	 *
	 * @since 1.1.0
	 * @param object $data Step data.
	 * @param int    $funnel_id Funnel id.
	 * @param int    $origin_node Origin node id.
	 * @param int    $target_node Target node id.
	 * @param int    $target_index Target index.
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	public function import_to_funnel( $data, $funnel_id, $origin_node = null, $target_node = null, $target_index = 0 ) {
		$meta_data        = (array) $data->meta;
		$funnel_step_data = get_post_meta( $funnel_id, 'sellkit_steps', true );
		$funnel_nodes     = get_post_meta( $funnel_id, 'nodes', true );

		if ( empty( $meta_data['nodes'] ) ) {
			$step_data = unserialize( $meta_data['step_data'] ); //phpcs:ignore
		}

		if ( ! empty( $meta_data['nodes'] ) ) {
			$step_data = unserialize( $meta_data['nodes'] ); //phpcs:ignore
		}

		if ( ! empty( $funnel_nodes ) ) {
			$funnel_step_data = get_post_meta( $funnel_id, 'nodes', true );
		}

		unset( $step_data['funnel_id'] );
		unset( $step_data['number'] );
		unset( $step_data['data'] );
		unset( $step_data['targets'] );

		if ( ! empty( $target_node ) ) {
			$current_target = [ 'nodeId' => $target_node ];
		}

		$funnel_step_data = ! empty( $funnel_step_data ) ? $funnel_step_data : [];
		$new_step_data    = $this->create_new_step_page( $step_data );

		if ( ! empty( $current_target ) ) {
			$new_step_data['targets'] = ! is_null( $current_target ) ? [ $current_target ] : [];
		}

		// Adding current target info the the step.
		$new_step_data['current_target_index'] = $target_index;

		if ( 'last-node' === $origin_node ) {
			$first_end_path_node_key = \Sellkit_Admin_Steps::get_first_end_path_node_key( $funnel_step_data );

			$new_step_data['origin_node'] = 'last-node';

			if ( $first_end_path_node_key ) {
				unset( $funnel_step_data[ $first_end_path_node_key ]['origin_node'] );
			}
		}

		$old_node_keys        = array_keys( (array) $funnel_step_data );
		$new_funnel_step_data = (array) $funnel_step_data;
		$new_step_id          = intval( end( $old_node_keys ) ) + 1;

		if (
			'decision' === $new_step_data['type']['key'] && ! is_null( $target_node ) ||
			'upsell' === $new_step_data['type']['key'] && ! is_null( $target_node ) ||
			'downsell' === $new_step_data['type']['key'] && ! is_null( $target_node )
		) {
			$new_step_data['targets'] = [
				$current_target,
				$current_target
			];
		}

		$new_funnel_step_data[ $new_step_id ] = $new_step_data;

		if ( 'none' === $origin_node && 'none' === $target_node ) {
			$new_funnel_step_data = [ 1 => $new_step_data ];
		}

		if ( 'last-node' !== $origin_node && is_object( $new_funnel_step_data[ $origin_node ]['type'] ) ) {
			$new_funnel_step_data[ $origin_node ]['type'] = (array) $new_funnel_step_data[ $origin_node ]['type'];
		}

		if (
			'last-node' !== $origin_node &&
			'none' !== $origin_node &&
			'decision' !== $new_funnel_step_data[ $origin_node ]['type']['key'] &&
			'upsell' !== $new_funnel_step_data[ $origin_node ]['type']['key'] &&
			'downsell' !== $new_funnel_step_data[ $origin_node ]['type']['key']
		) {
			$new_funnel_step_data[ $origin_node ]['targets'] = [ [ 'nodeId' => strval( $new_step_id ) ] ];
		}

		if (
			'last-node' !== $origin_node &&
			(
				'decision' === $new_funnel_step_data[ $origin_node ]['type']['key'] ||
				'upsell' === $new_funnel_step_data[ $origin_node ]['type']['key'] ||
				'downsell' === $new_funnel_step_data[ $origin_node ]['type']['key']
			)
		) {
			if ( is_array( $new_funnel_step_data[ $origin_node ]['targets'] ) && empty( $new_funnel_step_data[ $origin_node ]['targets'][0] ) ) {
				$new_funnel_step_data[ $origin_node ]['targets'][0] = 'none' !== $target_node ? [ 'nodeId' => $target_node ] : null;
			}

			if ( is_array( $new_funnel_step_data[ $origin_node ]['targets'] ) && empty( $new_funnel_step_data[ $origin_node ]['targets'][1] ) ) {
				$new_funnel_step_data[ $origin_node ]['targets'][1] = 'none' !== $target_node ? [ 'nodeId' => $target_node ] : null;
			}

			$new_funnel_step_data[ $origin_node ]['targets'][ (int) $target_index ] = [ 'nodeId' => strval( $new_step_id ) ];
		}

		if ( empty( $funnel_nodes ) && 'none' !== $origin_node && 'none' !== $target_node ) {
			update_post_meta( $funnel_id, 'sellkit_steps', $new_funnel_step_data );
		}

		if ( ! empty( $funnel_nodes ) || ( 'none' === $origin_node && 'none' === $target_node ) ) {
			update_post_meta( $funnel_id, 'nodes', $new_funnel_step_data );
		}

		$args = array(
			'ID'           => $new_step_data['page_id'],
			'post_content' => $data->post_content,
		);

		wp_update_post( $args );

		foreach ( $data->meta as $meta_key => $meta_value ) {
			if ( '_elementor_data' === $meta_key ) {

				if ( ! is_array( $meta_value ) ) {
					$elementor_data = add_magic_quotes( $meta_value );
					$elementor_data = json_decode( $elementor_data, true );
				}
				update_post_meta( $new_step_data['page_id'], $meta_key, $elementor_data );
				continue;
			}

			if ( '_elementor_css' === $meta_key ) {
				continue;
			}

			if ( is_serialized( $meta_value, true ) ) {
				$meta_value = maybe_unserialize( stripslashes( $meta_value ) );
			}

			if ( 'step_data' === $meta_key ) {
				$meta_value['funnel_id'] = intval( $funnel_id );
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
