<?php

namespace Sellkit\Dynamic_Keywords\Contact_Segmentation;

/**
 * Class User Role.
 *
 * @package Sellkit\Dynamic_Keywords\Contact_Segmentation
 * @since 1.1.0
 */
class User_Role extends Contact_Segmentation_Base {

	/**
	 * Constructor.
	 *
	 * @since 1.1.0
	 * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get class id.
	 *
	 * @return string
	 */
	public function get_id() {
		return '_user_role';
	}

	/**
	 * Get class title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'User Role', 'sellkit' );
	}

	/**
	 * Render content.
	 *
	 * @param array $atts array of shortcode arguments.
	 * @return string
	 */
	public function render_content( $atts ) {
		global $current_user;

		if ( empty( $current_user->roles ) ) {
			return $this->shortcode_content( $atts );
		}

		return $current_user->roles[0];
	}
}
