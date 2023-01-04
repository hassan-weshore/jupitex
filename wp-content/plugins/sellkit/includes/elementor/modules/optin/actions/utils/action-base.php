<?php

defined( 'ABSPATH' ) || die();

use Sellkit_Elementor_Optin_Ajaxhandler as AjaxHandler;

/**
 * An abstract class to register new optin action.
 *
 * @since 1.5.0
 * @abstract
 */
abstract class Sellkit_Elementor_Optin_Action_Base {

	/**
	 * Initializes action base for Optin widget.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		add_action( 'elementor/element/sellkit-optin/section_settings/after_section_end', [ $this, 'add_action_section_controls' ] );
	}

	/**
	 * Get name of this action.
	 *
	 * @since 1.5.0
	 * @access public
	 * @abstract
	 */
	abstract public function get_name();

	/**
	 * Get title of this action.
	 *
	 * @since 1.5.0
	 * @access public
	 * @abstract
	 */
	abstract public function get_title();

	/**
	 * Called by hook, and injects controls section relating to this action.
	 *
	 * @param object $widget widget instance passed by hook.
	 * @since 1.5.0
	 * @access public
	 */
	public function add_action_section_controls( $widget ) {
		$action = $this->get_name();

		$widget->start_controls_section( "section_{$action}",
			[
				'label'     => $this->get_title(),
				'condition' => [ 'crm_actions' => $action ],
			]
		);

		$this->add_controls( $widget );

		$widget->end_controls_section();
	}

	/**
	 * Add action controls.
	 *
	 * @param object $widget instance.
	 * @return void
	 *
	 * @since 1.5.0
	 * @access public
	 * @abstract
	 */
	abstract public function add_controls( $widget );

	/**
	 * Run the main functionality of the action.
	 *
	 * @param AjaxHandler $ajax_handler Ajax handler instance.
	 *
	 * @since 1.5.0
	 * @access public
	 * @abstract
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function run( AjaxHandler $ajax_handler ) {}
}
