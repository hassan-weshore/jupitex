<?php
namespace Jet_Popup\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Get_Elementor_Icon_Html extends Base {

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
		return 'get-elementor-icon-html';
	}

	/**
	 * Returns arguments config
	 *
	 * @return [type] [description]
	 */
	public function get_args() {
		return array(
			'iconData' => array(
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

		if ( is_wp_error( $request ) ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( 'Server Error', 'jet-popup' ),
				'data'    => [],
			) );
		}

		$args = $request->get_params();
		$iconData = $args['iconData'];

		ob_start();
		\Elementor\Icons_Manager::render_icon( $iconData );
		$icon_html = ob_get_clean();

		return rest_ensure_response( [
			'success' => true,
			'message' => __( 'Icon Rendered', 'jet-popup' ),
			'data'    => [
				'iconHtml' => $icon_html,
			],
		] );
	}

}
