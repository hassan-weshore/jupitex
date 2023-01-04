<?php
namespace Jet_Popup\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Get_Posts extends Base {

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
		return 'get-posts';
	}

	/**
	 * @return array[]
	 */
	public function get_args() {
		return array(
			'post_type' => array(
				'default'    => 'post',
				'required'   => false,
			),
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

		$post_type = isset( $args['post_type'] ) ? $args['post_type'] : 'post';
		$query = isset( $args['query'] ) ? $args['query'] : '';
		$values = isset( $args['values'] ) ? $args['values'] : [];
		$posts = \Jet_Popup_Utils::get_posts_by_type( $post_type, $query, $values );

		return rest_ensure_response( $posts );
	}

}
