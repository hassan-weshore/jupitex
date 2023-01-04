<?php
/**
 * Add Action Base.
 *
 * @package JupiterX_Core\Raven
 * @since 1.0.0
 */

namespace JupiterX_Core\Raven\Modules\Forms\Actions;

defined( 'ABSPATH' ) || die();

use Elementor\Settings;

/**
 * Action Base.
 *
 * An abstract class to register new form action.
 *
 * @since 1.0.0
 * @abstract
 */
abstract class Action_Base {

	/**
	 * Action base constructor.
	 *
	 * Initializing the action base class by hooking in widgets controls.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'elementor/element/raven-form/section_settings/after_section_end', [ $this, 'update_controls' ] );
		add_action( 'elementor/element/raven-register/section_settings/after_section_end', [ $this, 'update_controls' ] );

		if ( is_admin() ) {
			add_action( 'elementor/admin/after_create_settings/' . Settings::PAGE_ID, [ $this, 'register_admin_fields' ], 20 );
		}
	}

	/**
	 * Get name.
	 *
	 * Get name of this action.
	 *
	 * @since 1.19.0
	 * @access public
	 * @abstract
	 */
	abstract public function get_name();

	/**
	 * Get title.
	 *
	 * Get title of this action.
	 *
	 * @since 1.19.0
	 * @access public
	 * @abstract
	 */
	abstract public function get_title();

	/**
	 * Is private.
	 *
	 * Determine if this action is private.
	 *
	 * @since 2.0.0
	 * @access public
	 * @abstract
	 */
	public function is_private() {
		return false;
	}

	/**
	 * Update controls.
	 *
	 * Add, remove and sort the controls in the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @abstract
	 *
	 * @param object $widget Ajax handler instance.
	 *
	 * @return void
	 */
	abstract public function update_controls( $widget );

	/**
	 * Run action.
	 *
	 * Run the main functionality of the action.
	 *
	 * @since 1.0.0
	 * @access public
	 * @abstract
	 *
	 * @param object $ajax_handler Ajax handler instance.
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function run( $ajax_handler ) {}

	/**
	 * Register admin fields.
	 *
	 * Register required admin settings for the field.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $settings Settings.
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function register_admin_fields( $settings ) {}
}
