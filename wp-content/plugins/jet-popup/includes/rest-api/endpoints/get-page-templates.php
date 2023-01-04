<?php
namespace Jet_Popup\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Get_Page_Templates extends Base {

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
		return 'get-page-templates';
	}

	/**
	 * [callback description]
	 * @param  [type]   $request [description]
	 * @return function          [description]
	 */
	public function callback( $request ) {

		$options = array();

		$templates = wp_get_theme()->get_page_templates();

		if ( ! empty( $templates ) ) {
			foreach ( $templates as $template => $label ) {
				$options[] = array(
					'value'   => $template,
					'label' => $label,
				);
			}
		}

		return rest_ensure_response( $options );
	}

}
