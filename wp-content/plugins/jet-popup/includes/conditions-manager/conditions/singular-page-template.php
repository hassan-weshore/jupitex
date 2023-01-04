<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Page_Template extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'singular-page-template';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Page Template', 'jet-popup' );
	}

	/**
	 * Condition group
	 *
	 * @return string
	 */
	public function get_group() {
		return 'singular';
	}

	/**
	 * @return string
	 */
	public function get_sub_group() {
		return 'page-singular';
	}

	/**
	 * @return int
	 */
	public  function get_priority() {
		return 50;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_page';
	}

	/**
	 * [get_control description]
	 * @return [type] [description]
	 */
	public function get_control() {
		return [
			'type'        => 'select',
			'placeholder' => __( 'Select template', 'jet-popup' ),
		];
	}

	/**
	 * [ajax_action description]
	 * @return [type] [description]
	 */
	public function ajax_action() {
		return [
			'action' => 'get-page-templates',
			'params' => []
		];
	}

	/**
	 * [get_label_by_value description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_label_by_value( $value = '' ) {
		$template_label = '';

		$templates = wp_get_theme()->get_page_templates();

		if ( ! empty( $templates ) ) {
			foreach ( $templates as $template => $label ) {

				if ( $template === $value ) {
					$template_label = $template;
				}
			}
		}

		return $template_label;
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $arg = '' ) {

		if ( empty( $arg ) ) {
			return false;
		}

		if ( ! is_page() ) {
			return false;
		}

		global $post;

		$page_template_slug = get_page_template_slug( $post->ID );

		return $page_template_slug === $arg;
	}

}
