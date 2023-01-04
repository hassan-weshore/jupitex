<?php
namespace Jet_Popup\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Update_Popup_Settings extends Base {

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
		return 'update-popup-settings';
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
			'settings' => array(
				'default'    => array(),
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
		$settings = $args['settings'];

		jet_popup()->settings->update_popup_settings( $popup_id, $settings );

		return rest_ensure_response( [
			'success' => true,
			'message' => __( 'Settings have been saved', 'jet-popup' ),
			'data' => [
				'verboseHtml' => jet_popup()->post_type->popup_settings_verbose( $popup_id ),
			],
		] );
	}

}
