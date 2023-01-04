<?php
namespace Jet_Popup\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Get_Popup_Conditions extends Base {

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
		return 'get-popup-conditions';
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
		$popup_conditions = jet_popup()->conditions_manager->get_popup_conditions( $popup_id );

		return rest_ensure_response( [
			'success' => true,
			'message' => __( 'Success', 'jet-popup' ),
			'data'   => [
				'conditions'   => $popup_conditions['conditions'],
				'relationType' => $popup_conditions['relationType'],
			],
		] );
	}

}
