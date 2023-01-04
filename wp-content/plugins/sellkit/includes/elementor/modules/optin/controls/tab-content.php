<?php

defined( 'ABSPATH' ) || die();

use Sellkit_Elementor_Optin_Module as Module;
use \Elementor\Repeater as Repeater;

/**
 * Holds methods for adding Optin widget's content tab controls.
 */
class Sellkit_Elementor_Optin_Tab_Content {

	public function __construct( $widget ) {
		$this->add_section_form_fields( $widget );
		$this->add_section_submit_button( $widget );
		$this->add_section_settings( $widget );
		$this->add_section_after_submit_actions( $widget );
		$this->add_section_messages( $widget );
	}

	private function add_section_form_fields( $widget ) {
		$widget->start_controls_section(
			'section_form_fields',
			[
				'label' => esc_html__( 'Form Fields', 'sellkit' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'type',
			[
				'label'   => esc_html__( 'Type', 'sellkit' ),
				'type'    => 'select',
				'options' => Module::get_field_types(),
				'default' => 'text',
			]
		);

		foreach ( Sellkit_Elementor_Optin_Field_Base::get_fields_controls() as $key => $props ) {
			if ( strpos( $key, 'responsive' ) ) {
				$repeater->add_responsive_control( str_replace( '_responsive', '', $key ), $props );
				continue;
			}
			$repeater->add_control( $key, $props );
		}

		$widget->add_control(
			'fields',
			[
				'type'        => 'repeater',
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'label'       => esc_html__( 'Name', 'sellkit' ),
						'type'        => 'text',
						'placeholder' => 'Name',
					],
					[
						'label'       => esc_html__( 'Email', 'sellkit' ),
						'type'        => 'email',
						'placeholder' => 'Email',
						'required'    => 'true',
					],
				],
				'title_field' => '{{{ label }}}',
			]
		);

		$widget->end_controls_section();
	}

	private function add_section_submit_button( $widget ) {
		$widget->start_controls_section(
			'section_submit_button',
			[
				'label' => esc_html__( 'Submit Button', 'sellkit' ),
			]
		);

		$widget->add_control(
			'submit_button_text',
			[
				'label'   => esc_html__( 'Text', 'sellkit' ),
				'type'    => 'text',
				'default' => esc_html__( 'Subscribe', 'sellkit' ),
			]
		);

		$widget->add_control(
			'submit_button_subtext',
			[
				'label' => esc_html__( 'Sub Text', 'sellkit' ),
				'type'  => 'text',
			]
		);

		$widget->add_control(
			'submit_button_icon',
			[
				'label'       => esc_html__( 'Icon', 'sellkit' ),
				'type'        => 'icons',
				'skin'        => 'inline',
				'label_block' => false,
			]
		);

		$widget->add_responsive_control(
			'submit_button_width',
			[
				'label'   => esc_html__( 'Column Width', 'sellkit' ),
				'type'    => 'select',
				'default' => '100',
				'options' => [
					'100' => '100%',
					'80'  => '80%',
					'75'  => '75%',
					'66'  => '66%',
					'60'  => '60%',
					'50'  => '50%',
					'40'  => '40%',
					'33'  => '33%',
					'25'  => '25%',
					'20'  => '20%',
				],
			]
		);

		$widget->end_controls_section();
	}

	private function add_section_settings( $widget ) {
		$widget->start_controls_section(
			'section_settings',
			[
				'label' => esc_html__( 'Settings', 'sellkit' ),
			]
		);

		$widget->add_control(
			'label',
			[
				'label'     => esc_html__( 'Label', 'sellkit' ),
				'type'      => 'switcher',
				'label_on'  => esc_html__( 'Show', 'sellkit' ),
				'label_off' => esc_html__( 'Hide', 'sellkit' ),
				'default'   => 'yes',
			]
		);

		$widget->add_control(
			'required_mark',
			[
				'label'        => esc_html__( 'Required Mark', 'sellkit' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Show', 'sellkit' ),
				'label_off'    => esc_html__( 'Hide', 'sellkit' ),
				'return_value' => 'show',
				'prefix_class' => 'label-required-mark-',
			]
		);

		$widget->add_control(
			'crm_actions',
			[
				'label'       => esc_html__( 'CRM Integrations', 'sellkit' ),
				'type'        => 'select2',
				'multiple'    => true,
				'options'     => Module::get_action_types(),
				'label_block' => true,
				'render_type' => 'ui',
			]
		);

		$widget->end_controls_section();
	}

	private function add_section_after_submit_actions( $widget ) {
		$widget->start_controls_section(
			'section_after_submit_actions',
			[
				'label' => esc_html__( 'After Submit Actions', 'sellkit' ),
			]
		);

		$widget->add_control(
			'download_source',
			[
				'label'   => esc_html__( 'Download a File', 'sellkit' ),
				'type'    => 'select',
				'default' => '',
				'options' => [
					''     => esc_html__( 'None', 'sellkit' ),
					'file' => esc_html__( 'File', 'sellkit' ),
					'url'  => esc_html__( 'URL', 'sellkit' ),
				],
			]
		);

		$widget->add_control(
			'download_url',
			[
				'label'         => esc_html__( 'Download URL', 'sellkit' ),
				'type'          => 'url',
				'placeholder'   => esc_html__( 'https: //your-link.com', 'sellkit' ),
				'options'       => false,
				'default'       => [ 'url' => '' ],
				'condition'     => [ 'download_source' => 'url' ],
			]
		);

		$widget->add_control(
			'download_file',
			[
				'label'     => esc_html__( 'File', 'sellkit' ),
				'type'      => 'sellkit_file_uploader',
				'condition' => [ 'download_source' => 'file' ],
			]
		);

		$widget->add_control(
			'redirect_to',
			[
				'label'   => esc_html__( 'Redirect to', 'sellkit' ),
				'type'    => 'select',
				'default' => 'funnel',
				'options' => [
					'funnel' => esc_html__( 'Next Funnel Step', 'sellkit' ),
					'custom' => esc_html__( 'Custom URL', 'sellkit' ),
				],
			]
		);

		$widget->add_control(
			'redirect_url',
			[
				'label'         => esc_html__( 'Redirect URL', 'sellkit' ),
				'type'          => 'url',
				'placeholder'   => esc_html__( 'https: //your-link.com', 'sellkit' ),
				'options'       => false,
				'default'       => [ 'url' => '' ],
				'condition'     => [
					'redirect_to' => 'custom',
				]
			]
		);

		$widget->end_controls_section();
	}

	private function add_section_messages( $widget ) {
		$widget->start_controls_section(
			'section_messages',
			[
				'label' => esc_html__( 'Feedback Messages', 'sellkit' ),
			]
		);

		$widget->add_control(
			'messages_custom',
			[
				'label'       => esc_html__( 'Custom Messages', 'sellkit' ),
				'type'        => 'switcher',
				'render_type' => 'template',
			]
		);

		$widget->add_control(
			'messages_success',
			[
				'label'       => esc_html__( 'Success Message', 'sellkit' ),
				'type'        => 'text',
				'default'     => Module::$messages['success'],
				'label_block' => true,
				'render_type' => 'ui',
				'condition'   => [
					'messages_custom' => 'yes',
				],
			]
		);

		$widget->add_control(
			'messages_error',
			[
				'label'       => esc_html__( 'Error Message', 'sellkit' ),
				'type'        => 'text',
				'default'     => Module::$messages['error'],
				'label_block' => true,
				'render_type' => 'template',
				'condition'   => [
					'messages_custom' => 'yes',
				],
			]
		);

		$widget->add_control(
			'messages_required',
			[
				'label'       => esc_html__( 'Required Message', 'sellkit' ),
				'type'        => 'text',
				'default'     => Module::$messages['required'],
				'label_block' => true,
				'render_type' => 'template',
				'condition'   => [
					'messages_custom' => 'yes',
				],
			]
		);

		$widget->end_controls_section();
	}
}
