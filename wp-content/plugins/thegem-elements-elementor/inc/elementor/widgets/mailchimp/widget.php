<?php

namespace TheGem_Elementor\Widgets\MailChimp;

use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;

if (!defined('ABSPATH')) exit;


/**
 * Elementor widget for Mailchimp Opt-In.
 */
class TheGem_MailChimp extends Widget_Base {

	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */

	public function __construct($data = [], $args = null) {

		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_MAILCHIMP_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_MAILCHIMP_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_MAILCHIMP_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_MAILCHIMP_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-mailchimp', THEGEM_ELEMENTOR_WIDGET_MAILCHIMP_URL . '/assets/css/thegem-mailchimp.css', array(), NULL);
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-mailchimp';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Mailchimp Opt-In', 'thegem');
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return str_replace('thegem-', 'thegem-eicon thegem-eicon-', $this->get_name());
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['thegem_elements'];
	}

	public function get_style_depends() {
		return ['thegem-mailchimp'];
	}

	public function get_script_depends() {
		return ['thegem-form-elements'];
	}

	/*Show reload button*/
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Create presets options for Select
	 *
	 * @access protected
	 * @return array
	 */
	protected function get_presets_options() {
		$out = array(
			'default' => __('Default', 'thegem'),
			'white' => __('White', 'thegem'),
			'white-simple' => __('White Simple Line', 'thegem'),
			'dark' => __('Dark', 'thegem'),
			'dark-simple' => __('Dark Simple Line', 'thegem'),
		);
		return $out;
	}


	/**
	 * Get default presets options for Select
	 *
	 * @param int $index
	 *
	 * @access protected
	 * @return string
	 */
	protected function set_default_presets_options() {
		return 'default';
	}

	/**
	 * Get Mailchimp Opt-In Form [ if exists ]
	 */
	protected function select_optin_form() {
		$options = array();

		if (function_exists('yikes_easy_mailchimp_extender_get_form_interface')) {
			$interface = yikes_easy_mailchimp_extender_get_form_interface();
			$all_forms = $interface->get_all_forms();
			$options[0] = esc_html__('Select a Mailchimp Form', 'thegem');
			if ( ! empty( $all_forms ) ) {
				foreach ( $all_forms as $id => $form ) {
					$options[$id] = $form['form_name'];
				}
			} else {
				$options[0] = esc_html__('Please Import Some Mailchimp Lists First', 'thegem');
			}
		}
		return $options;
	}


	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		if (!function_exists('yikes_easy_mailchimp_extender_get_form_interface')) {
			$this->start_controls_section(
				'section_mailchimp_warning',
				[
					'label' => __('Warning!', 'thegem'),
				]
			);

			$this->add_control(
				'mailchimp_warning_text',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __('<strong>Easy Forms for Mailchimp</strong> is not installed/activated on your site. Please install and activate <strong>Easy Forms for Mailchimp</strong> first.', 'thegem'),
					'content_classes' => 'elementor-descriptor',
				]
			);

			$this->end_controls_section();
		} else {

			$this->start_controls_section(
				'section_layout',
				[
					'label' => __('Layout', 'thegem'),
				]
			);

			$this->add_control(
				'thegem_elementor_preset',
				[
					'label' => __('Skin', 'thegem'),
					'type' => Controls_Manager::SELECT,
					'options' => $this->get_presets_options(),
					'default' => $this->set_default_presets_options(),
					'frontend_available' => true,
					'render_type' => 'none',
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_content',
				[
					'label' => __('Opt-In Form', 'thegem'),
				]
			);

			$this->add_control(
				'form_id',
				[
					'label' => esc_html__('Select Form', 'thegem'),
					'type' => Controls_Manager::SELECT,
					'label_block' => true,
					'options' => $this->select_optin_form(),
					'default' => '0',
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_title',
				[
					'label' => __('Title & Description ', 'thegem'),
				]
			);

			$this->add_control(
				'show_title',
				[
					'label' => __('Show Title', 'thegem'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'return_value' => 'yes',
				]
			);

			$this->add_control(
				'show_description',
				[
					'label' => __('Show Description', 'thegem'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'return_value' => 'yes',
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_submit',
				[
					'label' => __('Submit Button ', 'thegem'),
				]
			);

			$this->add_control(
				'submit_text',
				[
					'label' => __('Text', 'thegem'),
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'default' => __('Submit', 'thegem'),
				]
			);

			$this->add_control(
				'submit_show_icon',
				[
					'label' => 'Add Icon',
					'default' => '',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'submit_icon',
				[
					'label' => __( 'Icon', 'thegem' ),
					'type' => Controls_Manager::ICONS,
					'default' => [
						'value' => 'fas fa-star',
						'library' => 'fa-solid',
					],
					'condition' => [
						'submit_show_icon'	=> [ 'yes' ]
					],
				]
			);

			$this->end_controls_section();

			$this->add_styles_controls($this);
		}

	}

	/**
	 * Controls call
	 * @access public
	 */
	public function add_styles_controls($control) {

		$this->control = $control;

		/* Form Fields Style */
		$this->form_fields_style($control);

		/* Form Fields Content Style */
		$this->form_fields_content_style($control);

		/* Form Label Style */
		$this->form_label_style($control);

		/* Form Submit Button Style */
		$this->form_submit_button_style($control);

		/* Form Title & Description Style */
		$this->form_title_description_style($control);
	}

	/**
	 * Form Fields Style
	 * @access protected
	 */
	protected function form_fields_style($control) {

		$control->start_controls_section(
			'form_fields_style_section',
			[
				'label' => __('Form Fields Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'field_alignment',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'thegem'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
//				'toggle' => false,
//				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp label' => 'margin-right: auto; margin-left: auto;',
					'{{WRAPPER}} .thegem-mailchimp.label-top label' => 'margin-{{VALUE}}: 0;',
					'{{WRAPPER}} .thegem-mailchimp.label-bottom label' => 'margin-{{VALUE}}: 0;',
				],
			]
		);

		$control->add_responsive_control(
			'field_width',
			[
				'label' => __('Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => ['%', 'px'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp:not(.label-top):not(.label-bottom) form:not(.yikes-mailchimp-form-inline) input' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thegem-mailchimp.label-top form:not(.yikes-mailchimp-form-inline) label' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thegem-mailchimp.label-bottom form:not(.yikes-mailchimp-form-inline) label' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thegem-mailchimp label.label-inline' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'field_margin',
			[
				'label' => __('Spacing Bottom', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'field_padding_right',
			[
				'label' => __('Spacing Right', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} label.label-inline' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'field_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp .yikes-mailchimp-container form.yikes-easy-mc-form input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'field_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp .yikes-mailchimp-container form.yikes-easy-mc-form input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs('tabs_field_state');

		$control->start_controls_tab(
			'tab_field_normal',
			[
				'label' => __('Normal', 'thegem'),
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'field_border',
				'selector' => '{{WRAPPER}} .thegem-mailchimp .yikes-mailchimp-container form.yikes-easy-mc-form input',
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'field_box_shadow',
				'selector' => '{{WRAPPER}} .thegem-mailchimp .yikes-mailchimp-container form.yikes-easy-mc-form input',
			]
		);

		$control->add_control(
			'field_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp .yikes-mailchimp-container form.yikes-easy-mc-form input' => 'background-color: {{VALUE}}',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'tab_field_focus',
			[
				'label' => __('Focus', 'thegem'),
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'field_focus_border',
				'selector' => '{{WRAPPER}} .thegem-mailchimp .yikes-mailchimp-container form.yikes-easy-mc-form input:focus',
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'field_focus_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .thegem-mailchimp .yikes-mailchimp-container form.yikes-easy-mc-form input:focus',
			]
		);

		$control->add_control(
			'field_focus_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp .yikes-mailchimp-container form.yikes-easy-mc-form input:focus' => 'background-color: {{VALUE}}',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Form Fields Content Style
	 * @access protected
	 */
	protected function form_fields_content_style($control) {

		$control->start_controls_section(
			'form_fields_content_style_section',
			[
				'label' => __('Fields Content Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'field_input_heading',
			[
				'label' => __('Input Text', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'field_input_typography',
				'label' => __('Typography', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-mailchimp .yikes-mailchimp-container form.yikes-easy-mc-form input',
				'scheme' => Schemes\Typography::TYPOGRAPHY_3
			]
		);

		$control->add_control(
			'field_input_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp .yikes-mailchimp-container form.yikes-easy-mc-form input' => 'color: {{VALUE}}',
				],
			]
		);

		$control->add_control(
			'field_placeholder_heading',
			[
				'label' => __('Placeholder Text', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'field_placeholder_typography',
				'label' => __('Typography', 'thegem'),
				'selector' => '{{WRAPPER}} .yikes-easy-mc-form input::placeholder, {{WRAPPER}} .yikes-easy-mc-form input::-webkit-input-placeholder',
				'scheme' => Schemes\Typography::TYPOGRAPHY_3
			]
		);

		$control->add_control(
			'field_placeholder_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .yikes-easy-mc-form input::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .yikes-easy-mc-form input::-webkit-input-placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .yikes-easy-mc-form input::-ms-input-placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'field_description_heading',
			[
				'label' => __('Field Description', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'field_description_typography',
				'label' => __('Typography', 'thegem'),
				'selector' => '{{WRAPPER}} .form-field-description',
				'scheme' => Schemes\Typography::TYPOGRAPHY_3
			]
		);

		$control->add_control(
			'field_description_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .form-field-description' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Form Label Style
	 * @access protected
	 */
	protected function form_label_style($control) {

		$control->start_controls_section(
			'form_label_style_section',
			[
				'label' => __('Label Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'label_position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'top' => [
						'title' => __('Top', 'thegem'),
						'icon' => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => __('Bottom', 'thegem'),
						'icon' => 'eicon-v-align-bottom',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'default' => 'top',
			]
		);

		$control->add_responsive_control(
			'label_alignment',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'thegem'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'label_position',
							'operator' => '=',
							'value' => 'top',
						],
						[
							'name' => 'label_position',
							'operator' => '=',
							'value' => 'bottom',
						],
					],
				],
				'toggle' => false,
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} label' => 'text-align: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'label_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} label > span' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'label_position' => 'top',
				],
			]
		);

		$control->add_responsive_control(
			'label_top_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} label > span' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'label_position' => 'bottom',
				],
			]
		);

		$control->add_responsive_control(
			'label_horizontal_spacing',
			[
				'label' => __('Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.label-left label > span:not(.empty-label)' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thegem-mailchimp.label-right label > span:not(.empty-label)' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'label_position',
							'operator' => '=',
							'value' => 'left',
						],
						[
							'name' => 'label_position',
							'operator' => '=',
							'value' => 'right',
						],
					],
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'label' => __('Typography', 'thegem'),
				'selector' => 'label > span',
				'scheme' => Schemes\Typography::TYPOGRAPHY_3
			]
		);

		$control->add_control(
			'label_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} label > span' => 'color: {{VALUE}}',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Form Submit Button Style
	 * @access protected
	 */
	protected function form_submit_button_style($control) {

		$control->start_controls_section(
			'form_submit_button_style_section',
			[
				'label' => __('Submit Button Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'submit_button_alignment',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'thegem'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
//				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form form:not(.yikes-mailchimp-form-inline) .yikes-easy-mc-submit-button' => 'margin-{{VALUE}}: 0;',
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form form:not(.yikes-mailchimp-form-inline) .submit-button-inline-label' => 'flex: 1',
					'{{WRAPPER}} .thegem-mailchimp label.submit-button-inline-label' => 'margin-left: auto; margin-right: auto; margin-{{VALUE}}: 0;',
				],
			]
		);

		$this->add_responsive_control(
			'submit_button_width',
			[
				'label' => __('Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => ['%', 'px'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp form:not(.yikes-mailchimp-form-inline) .yikes-easy-mc-submit-button' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thegem-mailchimp label.submit-button-inline-label' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'disable_inline',
			[
				'label' => __('Disable Inline', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'your-plugin' ),
				'label_off' => __( 'No', 'your-plugin' ),
				'return_value' => 'yes',
				'default' => '',
				'devices' => [ 'mobile' ],
			]
		);


		$control->add_responsive_control(
			'submit_button_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'submit_button_border_type',
			[
				'label' => __('Border Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'none' => __('None', 'thegem'),
					'solid' => __('Solid', 'thegem'),
					'double' => __('Double', 'thegem'),
					'dotted' => __('Dotted', 'thegem'),
					'dashed' => __('Dashed', 'thegem'),
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button' => 'border-style: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'submit_button_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'submit_button_border_type!' => 'none',
				],
			]
		);

		$control->add_responsive_control(
			'submit_button_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs('submit_button_tabs');
		$control->start_controls_tab('submit_button_tab_normal', ['label' => __('Normal', 'thegem')]);

		$control->add_control(
			'submit_button_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button i' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'submit_button_typography',
				'selector' => '{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button span',
				'scheme' => Schemes\Typography::TYPOGRAPHY_4,
			]
		);

		$control->add_responsive_control(
			'submit_button_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'submit_button_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'submit_button_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('submit_button_tab_hover', ['label' => __('Hover', 'thegem')]);

		$control->add_control(
			'submit_button_text_color_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button:hover span' => 'color: {{VALUE}};',

				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'submit_button_typography_hover',
				'selector' => '{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button:hover span',
				'scheme' => Schemes\Typography::TYPOGRAPHY_4,
			]
		);

		$control->add_responsive_control(
			'submit_button_bg_color_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'submit_button_border_color_hover',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'submit_button_shadow_hover',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .wpcf7-submit:hover',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_responsive_control(
			'submit_margin',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'submit_button_icon_align',
			[
				'label' => __('Icon Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'row',
					'right' => 'row-reverse',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button' => 'flex-direction: {{VALUE}};',
				],
				'condition' => [
					'submit_show_icon' => 'yes',
				],

			]
		);

		$control->add_responsive_control(
			'submit_button_icon_spacing',
			[
				'label' => __('Icon Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'rem' => [
						'min' => 1,
						'max' => 100,
					],
					'em' => [
						'min' => 1,
						'max' => 100,
					],
				],

				'default' => [
					'size' => 5,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button .space' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'submit_show_icon' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'submit_button_icon_size',
			[
				'label' => __('Icon Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'rem' => [
						'min' => 1,
						'max' => 100,
					],
					'em' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thegem-mailchimp.wpcf7-form .yikes-easy-mc-form .yikes-easy-mc-submit-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'submit_show_icon' => 'yes',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Title & Description Style
	 * @access protected
	 */
	protected function form_title_description_style($control) {


		$control->start_controls_section(
			'form_title_description_style_section',
			[
				'label' => __('Title & Description Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'show_title',
							'operator' => '==',
							'value' => 'yes',
						],
						[
							'name' => 'show_description',
							'operator' => '==',
							'value' => 'yes',
						],
					]
				]
			]
		);

		$control->add_control(
			'title_heading',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __('Typography', 'thegem'),
				'scheme' => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .yikes-mailchimp-form-title',
				'separator' => 'before',
			]
		);

		$control->add_control(
			'title_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .yikes-mailchimp-form-title' => 'color: {{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'title_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 20,
					'unit' => 'px',
				],
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .yikes-mailchimp-form-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->add_control(
			'description_heading',
			[
				'label' => __('Description', 'thegem'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'label' => __('Typography', 'thegem'),
				'scheme' => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .yikes-mailchimp-form-description',
				'separator' => 'before',
			]
		);

		$control->add_control(
			'description_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .yikes-mailchimp-form-description' => 'color: {{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'description_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .yikes-mailchimp-form-description' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->end_controls_section();
	}

	protected function render() {
		if (!function_exists('yikes_easy_mailchimp_extender_get_form_interface')) {
			return;
		}

		$settings = $this->get_settings_for_display();

		if (empty ($settings['form_id'])) { ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo __('Please select Mailchimp opt-in form in "Opt-In Form" section', 'thegem') ?>
			</div>
			<?php return;
		}

		switch ($settings['thegem_elementor_preset']) {
			case 'white':
				$html_class = 'gem-contact-form-white';
				break;
			case 'white-simple' :
				$html_class = 'gem-contact-form-white gem-contact-form-simple-line';
				break;
			case 'dark' :
				$html_class = 'gem-contact-form-dark';
				break;
			case 'dark-simple' :
				$html_class = 'gem-contact-form-dark gem-contact-form-simple-line';
				break;
			default      :
				$html_class = '';
				break;
		}

		$form_uid = $this->get_id();

		$html_class .= ' form-' . $form_uid;

		$html_class .= ' label-' . $settings['label_position'];

		if ($settings['show_title'] == 'yes') {
			$title = 1;
		} else {
			$title = 0;
		}

		if ($settings['disable_inline_mobile'] && $settings['disable_inline_mobile'] == 'yes') {
			$html_class .= ' mobile-column';
		}

		if ($settings['show_description'] == 'yes') {
			$description = 1;
		} else {
			$description = 0;
		}

		if ($settings['submit_text'] == '') {
			$submit = ' ';
		} else {
			$submit = $settings['submit_text'];
		}

		if (!empty($settings['form_id'])) {
			echo '<div class="thegem-mailchimp wpcf7-form ' . $html_class . '">';
			echo do_shortcode('[yikes-mailchimp form="'.$settings['form_id'].'" title="'.$title.'" description="'.$description.'" submit="'.$submit.'"]');
			if ($settings['submit_icon'] && $settings['submit_icon']['value']) {
				Icons_Manager::render_icon($settings['submit_icon'], ['aria-hidden' => 'true']);
			}
			echo '</div>';

			if (is_admin() && Plugin::$instance->editor->is_edit_mode()):
				echo '<p class="not-empty"></p>';
			endif;

		} ?>

		<script>
			(function ($) {
				$(document).ready(function () {
					var icon = $('.thegem-mailchimp.form-<?= $form_uid ?> > i');
					var icon_svg = $('.thegem-mailchimp.form-<?= $form_uid ?> > svg');
					if (icon.length) {
						$('.thegem-mailchimp.form-<?= $form_uid ?> .yikes-easy-mc-submit-button').prepend('<span class="space"></span>').prepend(icon);
					} else if (icon_svg.length) {
						$('.thegem-mailchimp.form-<?= $form_uid ?> .yikes-easy-mc-submit-button').prepend('<span class="space"></span>').prepend(icon_svg);
					}

					var inputs = $('.thegem-mailchimp.form-<?= $form_uid ?> .label-inline:visible').length;
					if (inputs > 1) {
						$('.thegem-mailchimp.form-<?= $form_uid ?>').addClass('mobile-column');
					}

					labelEqualWidth();
				});

				function labelEqualWidth() {
					var max_width = 0;
					$('.thegem-mailchimp.form-<?= $form_uid ?>.label-left label span').css('width', 'auto');
					$('.thegem-mailchimp.form-<?= $form_uid ?>.label-left label span').each(function () {
						if ($(this).width() > max_width) {
							max_width = $(this).width() + 1;
						}
					});
					$('.thegem-mailchimp.form-<?= $form_uid ?>.label-left label span').css('width', max_width)
				}

				<?php
				if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>

				setTimeout(labelEqualWidth, 100);

				elementor.channels.editor.on('change', function (view) {
					var changed = view.elementSettingsModel.changed;

					if (changed.label_typography_typography !== undefined || changed.label_typography_font_family !== undefined || changed.label_typography_font_size !== undefined || changed.label_typography_font_weight !== undefined || changed.label_typography_text_transform !== undefined || changed.label_typography_letter_spacing !== undefined) {
						labelEqualWidth();
					}
				});

				<?php endif; ?>
			})(jQuery);
		</script>


	<?php }
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new TheGem_Mailchimp());