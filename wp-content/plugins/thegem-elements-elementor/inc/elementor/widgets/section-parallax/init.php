<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;

class TheGem_Section_Parallax {

	private static $instance = null;

	public $parallax_sections = array();

	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;

	}

	public function __construct() {

		if (!defined('THEGEM_ELEMENTOR_SECTION_PARALLAX_DIR')) {
			define('THEGEM_ELEMENTOR_SECTION_PARALLAX_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_SECTION_PARALLAX_URL')) {
			define('THEGEM_ELEMENTOR_SECTION_PARALLAX_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		add_action('elementor/element/section/section_background/after_section_end', array($this, 'after_section_end'), 10, 2);
		add_action('elementor/element/column/section_style/after_section_end', array($this, 'after_column_end'), 10, 2);
		add_action('elementor/frontend/section/before_render', array($this, 'section_before_render'));
		add_action('elementor/frontend/column/before_render', array($this, 'section_before_render'));
		add_action('elementor/frontend/before_enqueue_scripts', array($this, 'enqueue_scripts'), 9);
	}

	public function after_section_end($obj, $args) {

		$obj->start_controls_section(
			'thegem_section_parallax',
			array(
				'label' => esc_html__('TheGem Section Parallax', 'thegem'),
				'tab' => Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->parallax_controls($obj);

		$obj->end_controls_section();
	}

	public function after_column_end($obj, $args) {
		$obj->start_controls_section(
			'thegem_column_parallax',
			array(
				'label' => esc_html__('TheGem Column Parallax', 'thegem'),
				'tab' => Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->parallax_controls($obj);

		$obj->end_controls_section();
	}

	public function parallax_controls($obj) {

		$obj->add_control(
			'thegem_parallax_activate',
			[
				'label' => 'Parallax Background',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$obj->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'thegem_parallax_background',
				'label' => 'Parallax Background',
				'selector' => '{{WRAPPER}} .thegem-section-parallax-background',
				'condition' => [
					'thegem_parallax_activate' => 'yes',
				]
			]
		);

		$obj->add_control(
			'thegem_parallax_type',
			[
				'label' => __('Parallax type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'vertical' => __('Vertical', 'thegem'),
					'horizontal' => __('Horizontal', 'thegem'),
					'fixed' => __('Fixed', 'thegem'),
				],
				'default' => 'vertical',
				'frontend_available' => true,
				'condition' => [
					'thegem_parallax_activate' => 'yes',
				]
			]
		);


		$obj->add_control(
			'thegem_parallax_activate_mobile',
			[
				'label' => 'Parallax on Mobiles',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'thegem_parallax_activate' => 'yes',
					'thegem_parallax_type' => 'vertical'
				]
			]
		);
	}

	public function section_before_render($obj) {
		$data = $obj->get_data();
		$type = isset($data['elType']) ? $data['elType'] : 'section';
		$settings = $data['settings'];

		if ('section' === $type || 'column' === $type) {

			if (isset($settings['thegem_parallax_activate']) && $settings['thegem_parallax_activate'] == 'yes') {
				$this->parallax_sections[$data['id']] = array(
					'parallax' => true,
					'mobile' => isset($settings['thegem_parallax_activate_mobile']) && $settings['thegem_parallax_activate_mobile'] == 'yes',
					'type' => isset($settings['thegem_parallax_type']) ? $settings['thegem_parallax_type'] : 'vertical',
				);
			}
		}
	}

	public function enqueue_scripts() {

		if (!empty($this->parallax_sections) || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
			wp_enqueue_style('thegem-section-parallax', THEGEM_ELEMENTOR_SECTION_PARALLAX_URL . '/assets/css/thegem-section-parallax.css');
			wp_enqueue_script('thegem-section-parallax', THEGEM_ELEMENTOR_SECTION_PARALLAX_URL . '/assets/js/thegem-section-parallax.js', array('jquery', 'elementor-frontend'), null, true);
			wp_localize_script(
				'thegem-section-parallax',
				'thegem_section_parallax',
				$this->parallax_sections
			);
		}
	}

}

TheGem_Section_Parallax::instance();