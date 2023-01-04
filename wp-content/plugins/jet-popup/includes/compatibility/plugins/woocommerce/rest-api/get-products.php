<?php
namespace Jet_Popup\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Get_Products extends Base {

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
		return 'get-products';
	}

	/**
	 * @return array[]
	 */
	public function get_args() {
		return array(
			'query' => array(
				'default'    => '',
				'required'   => false,
			),
			'values' => array(
				'default'    => [],
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
		$query = isset( $args['query'] ) ? $args['query'] : '';
		$values = isset( $args['values'] ) ? $args['values'] : [];
		$products = \Jet_Popup_Utils::get_posts_by_type( 'product', $query, $values );

		return rest_ensure_response( $products );
	}

}
