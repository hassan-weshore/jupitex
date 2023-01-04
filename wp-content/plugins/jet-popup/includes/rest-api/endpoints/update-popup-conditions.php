<?php
namespace Jet_Popup\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Update_Popup_Conditions extends Base {

	/**
	 * [get_method description]
	 * @return [type] [description]
	 */
	public function get_method() {
		return 'POST';
	}

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'update-popup-conditions';
	}

	/**
	 * Returns arguments config
	 *
	 * @return [type] [description]
	 */
	public function get_args() {
		return array(
			'popup_id' => array(
				'default'    => '',
				'required'   => false,
			),
			'conditions' => array(
				'default'    => array(),
				'required'   => false,
			),
			'relation_type' => array(
				'default'    => 'or',
				'required'   => false,
			),
		);
	}

	/**
	 * [callback description]
	 * @param  [type]   $request [description]
	 * @return function          [description]
	 */
	public function callback( $request ) {

		$args = $request->get_params();

		if ( is_wp_error( $request ) ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( 'Server Error', 'jet-popup' ),
			) );
		}

		if ( empty( $args['popup_id'] ) ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( 'Server Error', 'jet-popup' ),
			) );
		}

		$popup_id = $args['popup_id'];
		$conditions = $args['conditions'];
		$relation_tpe = $args['relation_type'];

		jet_popup()->conditions_manager->update_popup_conditions( $popup_id, $conditions, $relation_tpe );

		return rest_ensure_response( [
			'success' => true,
			'message' => __( 'Conditions have been saved', 'jet-popup' ),
			'data' => [
				'verboseHtml' => jet_popup()->conditions_manager->popup_conditions_verbose( $popup_id ),
			],
		] );
	}

}
