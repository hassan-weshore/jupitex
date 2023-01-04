<?php
namespace Jet_Popup\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Create_Popup extends Base {

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
		return 'create-popup';
	}

	/**
	 * Returns arguments config
	 *
	 * @return [type] [description]
	 */
	public function get_args() {
		return array(
			'name' => array(
				'default'    => '',
				'required'   => false,
			),
			'contentType' => array(
				'default'    => 'default',
				'required'   => false,
			),
			'preset' => array(
				'default'    => false,
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
				'message' => __( 'Server Error', 'jet-theme-core' ),
				'data'    => [],
			) );
		}

		$name = $args['name'];
		$preset = $args['preset'];
		$content_type = $args['contentType'];

		$creating_data = jet_popup()->post_type->create_popup( $preset, $content_type, $name );

		return rest_ensure_response( [
			'success' => 'success' === $creating_data['type'] ? true : false,
			'message' => $creating_data['message'],
			'data' => [
				'redirect'      => $creating_data['redirect'],
				'newPopupId' => $creating_data['newPopupId'],
			]
		] );
	}

}
