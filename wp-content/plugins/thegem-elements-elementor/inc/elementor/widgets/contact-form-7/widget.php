<?php

namespace TheGem_Elementor\Widgets\ContactForm7;

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
 * Elementor widget for Contact Form 7.
 */
class TheGem_ContactForm7 extends Widget_Base {

	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */

	public function __construct($data = [], $args = null) {

		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_CONTACTFORM7_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_CONTACTFORM7_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_CONTACTFORM7_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_CONTACTFORM7_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-contact-form7', THEGEM_ELEMENTOR_WIDGET_CONTACTFORM7_URL . '/assets/css/thegem-cf7.css', array(), NULL);
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-contact-form7';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Contact Form 7', 'thegem');
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
		return ['thegem-contact-form7'];
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
	 * Get Contact Form 7 [ if exists ]
	 */
	protected function select_contact_form() {
		$options = array();

		if (function_exists('wpcf7')) {
			$wpcf7_form_list = get_posts(array(
				'post_type' => 'wpcf7_contact_form',
				'showposts' => 999,
			));
			$options[0] = esc_html__('Select a Contact Form', 'thegem');
			if (!empty($wpcf7_form_list) && !is_wp_error($wpcf7_form_list)) {
				foreach ($wpcf7_form_list as $post) {
					$options[$post->ID] = $post->post_title;
				}
			} else {
				$options[0] = esc_html__('Create a Form First', 'thegem');
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

		if (!function_exists('wpcf7')) {
			$this->start_controls_section(
				'section_cf7_warning',
				[
					'label' => __('Warning!', 'thegem'),
				]
			);

			$this->add_control(
				'cf7_warning_text',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __('<strong>Contact Form 7</strong> is not installed/activated on your site. Please install and activate <strong>Contact Form 7</strong> first.', 'thegem'),
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
					'label' => __('Contact Form', 'thegem'),
				]
			);

			$this->add_control(
				'form_id',
				[
					'label' => esc_html__('Select Form', 'thegem'),
					'type' => Controls_Manager::SELECT,
					'label_block' => true,
					'options' => $this->select_contact_form(),
					'default' => '0',
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

		/* Form Checkbox Style */
		$this->form_checkbox_style($control);

		/* Form Submit Button Style */
		$this->form_submit_button_style($control);

		/* Form Errors Style */
		$this->form_errors_style($control);
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
					'{{WRAPPER}} .wpcf7-form-control-wrap' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .label-top label' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .label-bottom label' => 'width: {{SIZE}}{{UNIT}};',
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
				'default' => [
					'size' => '24',
					'unit' => 'px',
				],
				'selectors' => [

					'{{WRAPPER}} .wpcf7-form-control-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thegem-cf7 .with-label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
//					'{{WRAPPER}} .thegem-cf7.label-left .with-label label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
//					'{{WRAPPER}} .thegem-cf7.label-right .with-label label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
//					'{{WRAPPER}} .thegem-cf7.label-bottom .with-label label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .combobox-wrapper .combobox-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .combobox-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'textarea_height',
			[
				'label' => __('Textarea Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wpcf7-form-control-wrap textarea' => 'height:  {{SIZE}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio), {{WRAPPER}} .combobox-wrapper',
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'field_box_shadow',
				'selector' => '{{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio), {{WRAPPER}} .combobox-wrapper',
			]
		);

		$control->add_control(
			'field_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio)' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .combobox-wrapper' => 'background-color: {{VALUE}}',
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
				'selector' => '{{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):focus',
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'field_focus_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):focus',
			]
		);

		$control->add_control(
			'field_focus_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):focus' => 'background-color: {{VALUE}}',
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
				'selector' => '{{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio), {{WRAPPER}} .combobox-wrapper .combobox-text',
				'scheme' => Schemes\Typography::TYPOGRAPHY_3
			]
		);

		$control->add_control(
			'field_input_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio)' => 'color: {{VALUE}}',
					'{{WRAPPER}} .combobox-wrapper .combobox-text' => 'color: {{VALUE}}',
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
				'selector' => '{{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit):placeholder-shown',
				'scheme' => Schemes\Typography::TYPOGRAPHY_3
			]
		);

		$control->add_control(
			'field_placeholder_color',
			[
				'label' => __('Placeholder Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ::-webkit-input-placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} ::-moz-placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} ::-ms-input-placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit):placeholder-shown' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'field_placeholder_icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpcf7-form .wpcf7-form-control-wrap:after' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .with-label .wpcf7-form-control-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .with-label .wpcf7-form-control-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .thegem-cf7.label-left .with-label .wpcf7-form-control-wrap:not(:first-child)' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thegem-cf7.label-right .with-label .wpcf7-form-control-wrap:not(:first-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} label, {{WRAPPER}} .wpcf7-form-control.wpcf7-checkbox, {{WRAPPER}} .wpcf7-form-control.wpcf7-radio',
				'scheme' => Schemes\Typography::TYPOGRAPHY_3
			]
		);

		$control->add_control(
			'label_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} label' => 'color: {{VALUE}}',
					'{{WRAPPER}} .wpcf7-form-control.wpcf7-checkbox' => 'color: {{VALUE}}',
					'{{WRAPPER}} .wpcf7-form-control.wpcf7-radio' => 'color: {{VALUE}}',
				],
			]
		);

		$control->end_controls_section();
	}


	/**
	 * Form Checkbox Style
	 * @access protected
	 */
	protected function form_checkbox_style($control) {

		$control->start_controls_section(
			'section_radio_checkbox_style',
			[
				'label' => __('Radio & Checkbox', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'custom_radio_checkbox',
			[
				'label' => __('Custom Styles', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'thegem'),
				'label_off' => __('No', 'thegem'),
				'return_value' => 'yes',
			]
		);

		$control->add_responsive_control(
			'radio_checkbox_margin',
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
				'default' => [
					'size' => '24',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .with-radio .wpcf7-form-control-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'radio_checkbox_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '30',
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 80,
						'step' => 1,
					],
				],
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .checkbox-sign, {{WRAPPER}} .radio-sign' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$control->start_controls_tabs('tabs_radio_checkbox_style');

		$control->start_controls_tab(
			'radio_checkbox_normal',
			[
				'label' => __('Normal', 'thegem'),
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$control->add_control(
			'radio_checkbox_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .checkbox-sign, {{WRAPPER}} .radio-sign' => 'background: {{VALUE}}',
				],
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'radio_checkbox_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 15,
						'step' => 1,
					],
				],
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .checkbox-sign, {{WRAPPER}} .radio-sign' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$control->add_control(
			'radio_checkbox_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .checkbox-sign, {{WRAPPER}} .radio-sign' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$control->add_control(
			'checkbox_heading',
			[
				'label' => __('Checkbox', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$control->add_control(
			'checkbox_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .checkbox-sign, {{WRAPPER}} .checkbox-sign:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$control->add_control(
			'radio_heading',
			[
				'label' => __('Radio Buttons', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$control->add_control(
			'radio_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .radio-sign, {{WRAPPER}} .radio-sign:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'radio_checkbox_checked',
			[
				'label' => __('Checked', 'thegem'),
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$control->add_control(
			'radio_checkbox_color_checked',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .checkbox-sign.checked, {{WRAPPER}} .radio-sign.checked' => 'background: {{VALUE}}',
				],
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$control->add_control(
			'radio_checkbox_sign_color_checked',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .checkbox-sign.checked:before' => 'color: {{VALUE}}',
					'{{WRAPPER}} .radio-sign.checked:before' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'radio_checkbox_sign_size',
			[
				'label' => __('Checkbox Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '29',
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 80,
						'step' => 1,
					],
				],
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .checkbox-sign.checked:before' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'radio_checkbox_radio_checked_size',
			[
				'label' => __('Radio Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '15',
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 80,
						'step' => 1,
					],
				],
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .radio-sign.checked:before' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

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
					'flex-start' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'thegem'),
						'icon' => 'eicon-h-align-center',
					],
					'flex-end' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'default' => 'flex-start',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-form p:nth-last-of-type(1)' => 'align-items: {{VALUE}};',
					'{{WRAPPER}} .wpcf7-form .submit-outer' => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'submit_button_width',
			[
				'label' => __('Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1200,
						'step' => 1,
					],
				],
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .wpcf7-submit' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->add_responsive_control(
			'submit_button_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .wpcf7-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
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
					'{{WRAPPER}} .wpcf7-submit' => 'border-style: {{VALUE}};',
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
					'{{WRAPPER}} .wpcf7-submit' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
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
					'{{WRAPPER}} .wpcf7-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
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
					'{{WRAPPER}} .wpcf7-submit' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'submit_button_typography',
				'selector' => '{{WRAPPER}} .wpcf7-submit',
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
					'{{WRAPPER}} .wpcf7-submit' => 'background-color: {{VALUE}} !important;',
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
					'{{WRAPPER}} .wpcf7-submit' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'submit_button_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .wpcf7-submit',
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
					'{{WRAPPER}} .wpcf7-submit:hover' => 'color: {{VALUE}} !important;',

				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'submit_button_typography_hover',
				'selector' => '{{WRAPPER}} .wpcf7-submit:hover',
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
					'{{WRAPPER}} .wpcf7-submit:hover' => 'background-color: {{VALUE}} !important;',
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
					'{{WRAPPER}} .wpcf7-submit:hover' => 'border-color: {{VALUE}} !important;',
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
					'{{WRAPPER}} .wpcf7-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Form Errors Style
	 * @access protected
	 */
	protected function form_errors_style($control) {

		$control->start_controls_section(
			'form_errors_style_section',
			[
				'label' => __('Errors Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'error_messages_heading',
			[
				'label' => __('Error Messages', 'thegem'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$control->start_controls_tabs('tabs_error_messages_style');

		$control->start_controls_tab(
			'tab_error_messages_alert',
			[
				'label' => __('Alert', 'thegem'),
			]
		);

		$control->add_control(
			'error_alert_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-not-valid-tip' => 'color: {{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'error_alert_spacing',
			[
				'label' => __('Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .wpcf7-not-valid-tip' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'tab_error_messages_fields',
			[
				'label' => __('Fields', 'thegem'),
			]
		);

		$control->add_control(
			'error_field_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-form-control-wrap .wpcf7-form-control.wpcf7-not-valid' => 'background: {{VALUE}}',
				],
			]
		);

		$control->add_control(
			'error_field_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-form-control-wrap .wpcf7-form-control.wpcf7-not-valid' => 'color: {{VALUE}}',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'error_field_border',
				'label' => __('Border', 'thegem'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .wpcf7-form-control-wrap .wpcf7-form-control.wpcf7-not-valid',
				'separator' => 'before',
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->add_control(
			'validation_errors_heading',
			[
				'label' => __('Validation Errors', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'validation_errors_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-validation-errors' => 'background: {{VALUE}}',
				],
			]
		);

		$control->add_control(
			'validation_errors_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-validation-errors' => 'color: {{VALUE}}',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'validation_errors_typography',
				'label' => __('Typography', 'thegem'),
				'scheme' => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .wpcf7-validation-errors',
				'separator' => 'before',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'validation_errors_border',
				'label' => __('Border', 'thegem'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .wpcf7-validation-errors',
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'validation_errors_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .wpcf7-validation-errors' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'validation_errors_margin',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .wpcf7-validation-errors' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->end_controls_section();
	}

	protected function render() {
		if (!function_exists('wpcf7')) {
			return;
		}

		$settings = $this->get_settings_for_display();

		if (empty ($settings['form_id'])) { ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo __('Please select contact form in "Contact Form" section', 'thegem') ?>
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

		if (!empty($settings['form_id'])) {
			echo do_shortcode('[contact-form-7 id="' . $settings['form_id'] . '" html_class="thegem-cf7 ' . $html_class . '" ]');

			if (is_admin() && Plugin::$instance->editor->is_edit_mode()):
				echo '<p class="not-empty"></p>';
			endif;
		} ?>

		<script>
			(function ($) {
				$(document).ready(function () {
					$('.thegem-cf7.form-<?= $form_uid ?> select').combobox();
					$('.thegem-cf7.form-<?= $form_uid ?> input[type="checkbox"]').checkbox();
					$('.thegem-cf7.form-<?= $form_uid ?> input[type="radio"]').checkbox();
					labelReplace();
					labelEqualWidth();
					$('.thegem-cf7.form-<?= $form_uid ?> input[type="submit"]').parent().addClass('submit-outer');
				});

				function labelReplace() {
					$('.thegem-cf7.form-<?= $form_uid ?> p').each(function () {
						if ($(this).find('.wpcf7-radio').length || $(this).find('.wpcf7-checkbox').length) {
							$(this).addClass('with-radio');
						} else if (!$(this).hasClass('with-label') ) {
							var label = $(this).find('label');
							if (label.length) {
								$(this).addClass('with-label');
								$(this).append(label.find('.wpcf7-form-control-wrap'));
							}
						}
					});
				}

				function labelEqualWidth() {
					var max_width = 0;
					$('.thegem-cf7.form-<?= $form_uid ?>.label-left p.with-label label').removeAttr("style");
					$('.thegem-cf7.form-<?= $form_uid ?>.label-left p.with-label label').each(function () {
						if ($(this).width() > max_width) {
							max_width = $(this).width() + 1;
						}
					});
					$('.thegem-cf7.form-<?= $form_uid ?>.label-left p.with-label label').css('width', max_width)
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

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new TheGem_ContactForm7());