<?php

defined( 'ABSPATH' ) || die();

use Elementor\Element_Base;
use Elementor\Widget_Base;

/**
 * Sellkit Elementor Base Widget.
 *
 * @since 1.1.0
 */
abstract class Sellkit_Elementor_Base_Widget extends Widget_Base {

	/**
	 * Check If the widget is active.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public static function is_active() {
		return true;
	}

	/**
	 * Get all the cargories.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_categories() {
		return [ 'sellkit' ];
	}

	/**
	 * Get all of the keywords.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_keywords() {
		return [ 'sellkit' ];
	}

}
