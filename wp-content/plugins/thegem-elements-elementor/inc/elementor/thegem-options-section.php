<?php
use Elementor\Controls_Manager;

class TheGem_Options_Section {

	private static $instance = null;

	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;

	}

	public function __construct() {
		add_action('elementor/element/parse_css', [$this, 'add_post_css'], 10, 2);
		add_action('elementor/element/after_section_end', array($this, 'add_thegem_options_section'), 10, 3);
		if (!version_compare(ELEMENTOR_VERSION, '3.0.0', '>=') || version_compare(ELEMENTOR_VERSION, '3.0.5', '>=')) {
			add_action('elementor/element/column/thegem_options/after_section_start', array($this, 'add_custom_breackpoints_option'), 10, 2);
		}
		add_action('elementor/element/section/section_background/before_section_end', array($this, 'before_section_background_end'), 10, 2);
		add_action('elementor/frontend/section/before_render', array($this, 'section_before_render'));
		//add_filter( 'elementor/section/print_template', array( $this, 'print_template'), 10, 2);
	}

	public function add_thegem_options_section($element, $section_id, $args) {

		if ($section_id === '_section_responsive') {

			$element->start_controls_section(
				'thegem_options',
				array(
					'label' => esc_html__('TheGem Options', 'thegem'),
					'tab' => Controls_Manager::TAB_ADVANCED,
				)
			);

			$element->add_control(
				'thegem_custom_css_heading',
				[
					'label' => esc_html__('Custom CSS', 'thegem'),
					'type' => Controls_Manager::HEADING,
				]
			);

			$element->add_control(
				'thegem_custom_css_before_decsription',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __('Add your own custom CSS here', 'thegem'),
					'content_classes' => 'elementor-descriptor',
				]
			);

			$element->add_control(
				'thegem_custom_css',
				[
					'type' => Controls_Manager::CODE,
					'label' => __('Custom CSS', 'thegem'),
					'language' => 'css',
					'render_type' => 'none',
					'frontend_available' => true, 'frontend_available' => true,
					'show_label' => false,
					'separator' => 'none',
				]
			);

			$element->add_control(
				'thegem_custom_css_after_decsription',
				[
					'raw' => __('Use "selector" to target wrapper element. Examples:<br>selector {color: red;} // For main element<br>selector .child-element {margin: 10px;} // For child element<br>.my-class {text-align: center;} // Or use any custom selector', 'thegem'),
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-descriptor',
				]
			);

			$element->end_controls_section();

		}

	}

	public function add_custom_breackpoints_option($element, $args) {

		$element->add_control(
			'thegem_column_breakpoints_heading',
			[
				'label' => esc_html__('Custom Breakpoints', 'thegem'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$element->add_control(
			'thegem_column_breakpoints_decsritpion',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __('Add custom breakpoints and extended responsive column options', 'thegem'),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'media_min_width',
			[
				'label' => esc_html__('Min Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
			]
		);

		$repeater->add_control(
			'media_max_width',
			[
				'label' => esc_html__('Max Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
			]
		);

		$repeater->add_control(
			'column_visibility',
			[
				'label' => esc_html__('Column Visibility', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'default' => 'yes',
			]
		);

		$repeater->add_control(
			'column_width',
			[
				'label' => esc_html__('Column Width', 'thegem') . ' (%)',
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'required' => false,
				'condition' => [
					'column_visibility' => 'yes',
				]
			]
		);

		$repeater->add_control(
			'column_margin',
			[
				'label' => esc_html__('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'condition' => [
					'column_visibility' => 'yes',
				]
			]
		);

		$repeater->add_control(
			'column_padding',
			[
				'label' => esc_html__('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'condition' => [
					'column_visibility' => 'yes',
				]
			]
		);

		$repeater->add_control(
			'column_order',
			[
				'label' => esc_html__('Order', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => -20,
				'max' => 20,
				'condition' => [
					'column_visibility' => 'yes',
				]
			]
		);

		$element->add_control(
			'thegem_column_breakpoints_list',
			[
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => 'Min: {{{ media_min_width.size }}} - Max: {{{ media_max_width.size }}}',
				'prevent_empty' => false,
				'separator' => 'after',
				'show_label' => false,
			]
		);

	}


	/**
	 * @param $post_css Post
	 * @param $element  Element_Base
	 */
	public function add_post_css($post_css, $element) {
		if ($post_css instanceof Dynamic_CSS) {
			return;
		}

		if ($element->get_type() === 'section') {

			$output_css = '';
			$section_selector = $post_css->get_element_unique_selector($element);


			foreach ($element->get_children() as $child) {

				if ($child->get_type() === 'column') {

					$settings = $child->get_settings();

					if (!empty($settings['thegem_column_breakpoints_list'])) {

						$column_selector = $post_css->get_element_unique_selector($child);

						foreach ($settings['thegem_column_breakpoints_list'] as $breakpoint) {

							$media_min_width = !empty($breakpoint['media_min_width']) && !empty($breakpoint['media_min_width']['size']) ? intval($breakpoint['media_min_width']['size']) : 0;
							$media_max_width = !empty($breakpoint['media_max_width']) && !empty($breakpoint['media_max_width']['size']) ? intval($breakpoint['media_max_width']['size']) : 0;

							if ($media_min_width > 0 || $media_max_width > 0) {

								$media_query = array();
								if ($media_max_width > 0) {
									$media_query[] = '(max-width:' . $media_max_width . 'px)';
								}
								if ($media_min_width > 0) {
									$media_query[] = '(min-width:' . $media_min_width . 'px)';
								}

								if ($css = $this->generate_breakpoint_css($column_selector, $breakpoint)) {
									$css = $section_selector . ' > .elementor-container > .elementor-row{flex-wrap: wrap;}' . $css;
									$output_css .= '@media ' . implode(' and ', $media_query) . '{' . $css . '}';
								}

							}

						}

					}

				}

			}

			if (!empty($output_css)) {
				$post_css->get_stylesheet()->add_raw_css($output_css);
			}

		}

		$element_settings = $element->get_settings();
		if (empty($element_settings['thegem_custom_css'])) {
			return;
		}
		$custom_css = trim($element_settings['thegem_custom_css']);
		if (empty($custom_css)) {
			return;
		}
		$custom_css = str_replace('selector', $post_css->get_element_unique_selector($element), $custom_css);
		$post_css->get_stylesheet()->add_raw_css($custom_css);
	}

	public function generate_breakpoint_css($selector, $breakpoint = array()) {
		$css = '';
		$column_visibility = !empty($breakpoint['column_visibility']) && $breakpoint['column_visibility'] !== 'no';
		if ($column_visibility) {
			$column_width = !empty($breakpoint['column_width']) ? intval($breakpoint['column_width']) : -1;
			if ($column_width >= 0) {
				$css .= 'width: ' . $column_width . '% !important;';
			}
			if (!empty($breakpoint['column_order'])) {
				$css .= 'order : ' . $breakpoint['column_order'] . ';';
			}
			if (!empty($css)) {
				$css = $selector . '{' . $css . '}';
			}
			$paddings = array();
			$margins = array();
			foreach (array('top', 'right', 'bottom', 'left') as $side) {
				if ($breakpoint['column_padding'][$side] !== '') {
					$paddings[] = intval($breakpoint['column_padding'][$side]) . $breakpoint['column_padding']['unit'];
				}
				if ($breakpoint['column_margin'][$side] !== '') {
					$margins[] = intval($breakpoint['column_margin'][$side]) . $breakpoint['column_margin']['unit'];
				}
			}
			$dimensions_css = !empty($paddings) ? 'padding: ' . implode(' ', $paddings) . ' !important;' : '';
			$dimensions_css .= !empty($margins) ? 'margin: ' . implode(' ', $margins) . ' !important;' : '';
			$css .= !empty($dimensions_css) ? $selector . ' > .elementor-element-populated{' . $dimensions_css . '}' : '';
		} else {
			$css .= $selector . '{display: none;}';
		}
		return $css;
	}

	public function before_section_background_end($element, $args) {

		$element->update_control(
			'background_video_link',
			[
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$element->update_control(
			'background_video_fallback',
			[
				'dynamic' => [
					'active' => true,
				],
			]
		);

	}

	/*	public function print_template($template, $element) {

			if('section' === $element->get_name()) {
				$old_template = 'if ( settings.background_video_link ) {';
				$new_template = 'if ( settings.background_background === "video" && settings.background_video_link) {';
				$template = str_replace( $old_template, $new_template, $template );
			}

			return $template;
		}*/

	public function section_before_render($element) {
		if ('section' === $element->get_name()) {
			$settings = $element->get_settings_for_display();
			$element->set_settings('background_video_link', $settings['background_video_link']);
			$element->set_settings('background_video_fallback', $settings['background_video_fallback']);
		}
	}

}

TheGem_Options_Section::instance();