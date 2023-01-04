<?php

use Elementor\Controls_Manager;

class TheGem_Section_Interactions {
	private static $instance = null;

	public $interactions = array();

	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;

	}

	public function __construct() {
		if (!defined('THEGEM_ELEMENTOR_SECTION_INTERACTIONS_DIR')) {
			define('THEGEM_ELEMENTOR_SECTION_INTERACTIONS_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_SECTION_INTERACTIONS_URL')) {
			define('THEGEM_ELEMENTOR_SECTION_INTERACTIONS_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

//		add_action('elementor/element/widget/thegem_options/before_section_end', array($this, 'before_options_section_end'), 10, 2);
		add_action('elementor/element/before_section_end', array($this, 'before_options_section_end'), 10, 3);
//		add_action( 'elementor/element/section/section_background/after_section_end', array( $this, 'after_section_end' ), 10, 2 );
//		add_action('elementor/frontend/widget/before_render', array($this, 'before_render'));
//		add_action('elementor/frontend/section/before_render', array($this, 'before_render'));
		add_action('elementor/frontend/before_render', array($this, 'before_render'));
		add_action('elementor/frontend/before_enqueue_scripts', array($this, 'enqueue_scripts'), 9);

	}

	public function before_options_section_end($element, $section_id, $args) {

		if ($section_id === 'thegem_options') {
			$element->add_control(
				'thegem_interactions_heading',
				[
					'label' => esc_html__('Interactions', 'thegem'),
					'type' => Controls_Manager::HEADING,
				]
			);

			$element->add_control(
				'thegem_interaction_vertical_scroll',
				[
					'label' => __('Vertical Scroll', 'thegem'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'return_value' => 'yes',
					'default' => '',
					'frontend_available' => true,
					'render_type' => 'none',
				]
			);

			$element->add_control(
				'thegem_interaction_vertical_scroll_direction',
				[
					'label' => __('Direction', 'thegem'),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'' => __('Up', 'thegem'),
						'negative' => __('Down', 'thegem'),
					],
					'condition' => ['thegem_interaction_vertical_scroll' => 'yes'],
					'frontend_available' => true,
					'render_type' => 'none',
				]
			);

			$element->add_control(
				'thegem_interaction_vertical_scroll_speed',
				[
					'label' => __('Speed', 'thegem'),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 4,
					],
					'range' => [
						'px' => [
							'max' => 10,
							'step' => 0.1,
						],
					],
					'condition' => ['thegem_interaction_vertical_scroll' => 'yes'],
					'frontend_available' => true,
					'render_type' => 'none',
				]
			);

			$element->add_control(
				'thegem_interaction_vertical_scroll_range',
				[
					'label' => __('Viewport', 'thegem'),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'sizes' => [
							'start' => 0,
							'end' => 100,
						],
						'unit' => '%',
					],
					'labels' => [
						__('Bottom', 'thegem'),
						__('Top', 'thegem'),
					],
					'scales' => 1,
					'handles' => 'range',
					'condition' => ['thegem_interaction_vertical_scroll' => 'yes'],
					'frontend_available' => true,
					'render_type' => 'none',
				]
			);

			$element->add_control(
				'thegem_interaction_horizontal_scroll',
				[
					'label' => __('Horizontal Scroll', 'thegem'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'return_value' => 'yes',
					'default' => '',
					'frontend_available' => true,
					'render_type' => 'none',
					'separator' => 'before',
				]
			);

			$element->add_control(
				'thegem_interaction_horizontal_scroll_direction',
				[
					'label' => __('Direction', 'thegem'),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'' => __('Left', 'thegem'),
						'negative' => __('Right', 'thegem'),
					],
					'condition' => ['thegem_interaction_horizontal_scroll' => 'yes'],
					'frontend_available' => true,
					'render_type' => 'none',
				]
			);

			$element->add_control(
				'thegem_interaction_horizontal_scroll_speed',
				[
					'label' => __('Speed', 'thegem'),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 4,
					],
					'range' => [
						'px' => [
							'max' => 10,
							'step' => 0.1,
						],
					],
					'condition' => ['thegem_interaction_horizontal_scroll' => 'yes'],
					'frontend_available' => true,
					'render_type' => 'none',
				]
			);

			$element->add_control(
				'thegem_interaction_horizontal_scroll_range',
				[
					'label' => __('Viewport', 'thegem'),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'sizes' => [
							'start' => 0,
							'end' => 100,
						],
						'unit' => '%',
					],
					'labels' => [
						__('Bottom', 'thegem'),
						__('Top', 'thegem'),
					],
					'scales' => 1,
					'handles' => 'range',
					'condition' => ['thegem_interaction_horizontal_scroll' => 'yes'],
					'frontend_available' => true,
					'render_type' => 'none',
				]
			);

			$element->add_control(
				'thegem_interaction_mouse',
				[
					'label' => __('Mouse Effects', 'thegem'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'default' => '',
					'return_value' => 'yes',
					'separator' => 'before',
					'frontend_available' => true,
					'render_type' => 'none',
				]
			);

			$element->add_control(
				'thegem_interaction_mouse_direction',
				[
					'label' => __('Direction', 'thegem'),
					'type' => Controls_Manager::SELECT,
					'default' => '',
					'options' => [
						'' => __('Opposite', 'thegem'),
						'negative' => __('Direct', 'thegem'),
					],
					'condition' => ['thegem_interaction_mouse' => 'yes'],
					'frontend_available' => true,
					'render_type' => 'none',
				]
			);

			$element->add_control(
				'thegem_interaction_mouse_speed',
				[
					'label' => __('Speed', 'thegem'),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 1,
					],
					'range' => [
						'px' => [
							'max' => 10,
							'step' => 0.1,
						],
					],
					'condition' => ['thegem_interaction_mouse' => 'yes'],
					'frontend_available' => true,
					'render_type' => 'none',
				]
			);

			$element->add_control(
				'thegem_interaction_devices',
				[
					'label' => __( 'Apply Effects On', 'thegem' ),
					'type' => Controls_Manager::SELECT2,
					'multiple' => true,
					'label_block' => 'true',
					'default' => [ 'desktop', 'tablet', 'mobile' ],
					'options' => [
						'desktop' => __( 'Desktop', 'thegem' ),
						'tablet' => __( 'Tablet', 'thegem' ),
						'mobile' => __( 'Mobile', 'thegem' ),
					],
					'conditions'   => [
						'relation' => 'or',
						'terms' => [
							[
								'name'	 => 'thegem_interaction_mouse',
								'operator' => ' == ',
								'value'	=> 'yes',
							],
							[
								'name'	 => 'thegem_interaction_vertical_scroll',
								'operator' => ' == ',
								'value'	=> 'yes',
							],
							[
								'name'	 => 'thegem_interaction_horizontal_scroll',
								'operator' => ' == ',
								'value'	=> 'yes',
							],
						]
					],
					'frontend_available' => true,
					'render_type' => 'none',
				]
			);
		}

	}


	public function before_render($obj) {
		$data = $obj->get_data();
		$settings = $data['settings'];

		if (isset($settings['thegem_interaction_vertical_scroll']) && $settings['thegem_interaction_vertical_scroll'] == 'yes') {
			$this->interactions[$data['id']]['vertical_scroll'] = 'yes';
			$this->interactions[$data['id']]['vertical_scroll_direction'] = (isset($settings['thegem_interaction_vertical_scroll_direction']) && $settings['thegem_interaction_vertical_scroll_direction'] === 'negative') ? -1 : 1;
			$this->interactions[$data['id']]['vertical_scroll_speed'] = isset($settings['thegem_interaction_vertical_scroll_speed']['size']) ? $settings['thegem_interaction_vertical_scroll_speed']['size'] : 4;
			$this->interactions[$data['id']]['vertical_viewport_bottom'] = isset($settings['thegem_interaction_vertical_scroll_range']['sizes']['start']) ? $settings['thegem_interaction_vertical_scroll_range']['sizes']['start'] : 0;
			$this->interactions[$data['id']]['vertical_viewport_top'] = isset($settings['thegem_interaction_vertical_scroll_range']['sizes']['end']) ? $settings['thegem_interaction_vertical_scroll_range']['sizes']['end'] : 100;
			$this->interactions[$data['id']]['devices'] = isset($settings['thegem_interaction_devices']) ? $settings['thegem_interaction_devices'] : '';
		}

		if (isset($settings['thegem_interaction_horizontal_scroll']) && $settings['thegem_interaction_horizontal_scroll'] == 'yes') {
			$this->interactions[$data['id']]['horizontal_scroll'] = 'yes';
			$this->interactions[$data['id']]['horizontal_scroll_direction'] = (isset($settings['thegem_interaction_horizontal_scroll_direction']) && $settings['thegem_interaction_horizontal_scroll_direction'] === 'negative') ? -1 : 1;
			$this->interactions[$data['id']]['horizontal_scroll_speed'] = isset($settings['thegem_interaction_horizontal_scroll_speed']['size']) ? $settings['thegem_interaction_horizontal_scroll_speed']['size'] : 4;
			$this->interactions[$data['id']]['horizontal_viewport_bottom'] = isset($settings['thegem_interaction_horizontal_scroll_range']['sizes']['start']) ? $settings['thegem_interaction_horizontal_scroll_range']['sizes']['start'] : 0;
			$this->interactions[$data['id']]['horizontal_viewport_top'] = isset($settings['thegem_interaction_horizontal_scroll_range']['sizes']['end']) ? $settings['thegem_interaction_horizontal_scroll_range']['sizes']['end'] : 100;
			$this->interactions[$data['id']]['devices'] = isset($settings['thegem_interaction_devices']) ? $settings['thegem_interaction_devices'] : '';
		}

		if (isset($settings['thegem_interaction_mouse']) && $settings['thegem_interaction_mouse'] == 'yes') {
			$this->interactions[$data['id']]['mousemove'] = 'yes';
			$this->interactions[$data['id']]['mouse_direction'] = isset($settings['thegem_interaction_mouse_direction']) && $settings['thegem_interaction_mouse_direction'] === 'negative' ? 1 : -1;
			$this->interactions[$data['id']]['mouse_speed'] = isset($settings['thegem_interaction_mouse_speed']) ? $settings['thegem_interaction_mouse_speed']['size'] : 1;
			$this->interactions[$data['id']]['mouse_speed'] = isset($settings['thegem_interaction_mouse_speed']) ? $settings['thegem_interaction_mouse_speed']['size'] : 1;
			$this->interactions[$data['id']]['devices'] = isset($settings['thegem_interaction_devices']) ? $settings['thegem_interaction_devices'] : '';
		}
	}

	public function enqueue_scripts() {

		if (!empty($this->interactions) || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
			wp_enqueue_script( 'rellax', THEGEM_ELEMENTOR_SECTION_INTERACTIONS_URL . '/assets/js/rellax.min.js', array(), null, true);
			wp_enqueue_script('thegem-interactions', THEGEM_ELEMENTOR_SECTION_INTERACTIONS_URL . '/assets/js/interactions.js', array('jquery', 'elementor-frontend', 'rellax'), null, true);
			wp_localize_script(
				'thegem-interactions',
				'thegem_interactions',
				$this->interactions
			);
		}
	}
}

TheGem_Section_Interactions::instance();