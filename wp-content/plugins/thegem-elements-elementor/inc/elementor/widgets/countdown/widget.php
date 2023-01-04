<?php

namespace TheGem_Elementor\Widgets\Countdown;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Elementor widget for Countdown.
 */
class TheGem_Countdown extends Widget_Base {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		if ( ! defined( 'THEGEM_ELEMENTOR_WIDGET_COUNTDOWN_DIR' ) ) {
			define( 'THEGEM_ELEMENTOR_WIDGET_COUNTDOWN_DIR', rtrim( __DIR__, ' /\\' ) );
		}

		if ( ! defined( 'THEGEM_ELEMENTOR_WIDGET_COUNTDOWN_URL' ) ) {
			define( 'THEGEM_ELEMENTOR_WIDGET_COUNTDOWN_URL', rtrim( plugin_dir_url( __FILE__ ), ' /\\' ) );
		}

		wp_register_style( 'thegem-countdown', THEGEM_ELEMENTOR_WIDGET_COUNTDOWN_URL . '/assets/css/thegem-countdown.css', array(), null );
		wp_register_script( 'thegem-countdown', THEGEM_ELEMENTOR_WIDGET_COUNTDOWN_URL . '/assets/js/thegem-countdown.js', array( 'jquery', 'raphael', 'odometr' ), null, true );

	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-countdown';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Countdown', 'thegem' );
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

	public function get_keywords() {
		return [ 'countdown', 'number', 'timer', 'time', 'date', 'evergreen' ];
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'thegem_elements' ];
	}

	public function get_style_depends() {
		return [ 'thegem-countdown' ];
	}

	public function get_script_depends() {
		return [ 'thegem-countdown' ];
	}

	/*Show reload button*/
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		$this->content_settings();

		$this->style_settings();

	}

	/**
	 * Content Settings
	 * @access protected
	 */
	protected function content_settings() {
		$this->start_controls_section(
			'layout_settings',
			[
				'label' => __( 'Layout', 'thegem' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'content_layout',
			[
				'label'              => __( 'Skin', 'thegem' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'boxes',
				'multiple'           => false,
				'options'            => [
					'boxes'     => __( 'Boxes', 'thegem' ),
					'elegant'   => __( 'Elegant', 'thegem' ),
					'cross'     => __( 'Cross', 'thegem' ),
					'days_only' => __( 'Days Only', 'thegem' ),
					'circles'   => __( 'Circles', 'thegem' ),
				],
				'frontend_available' => true,
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'content_countdown',
			[
				'label' => __( 'Countdown', 'thegem' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'due_date',
			[
				'label' => __( 'Due Date', 'plugin-domain' ),
				'type'  => Controls_Manager::DATE_TIME,
			]
		);
		$this->add_control(
			'starting_date',
			[
				'label'     => __( 'Starting Date', 'plugin-domain' ),
				'type'      => Controls_Manager::DATE_TIME,
				'condition' => [
					'content_layout' => 'circles',
				]
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'content_labels',
			[
				'label' => __( 'Labels', 'thegem' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'content_labels_show_days',
			[
				'label'     => __( 'Show Days', 'thegem' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'content_layout!' => 'days_only',
				],
			]
		);
		$this->add_control(
			'content_labels_days_label',
			[
				'label'     => __( 'Days Label', 'thegem' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Days', 'thegem' ),
				'condition' => [
					'content_labels_show_days' => 'yes',
					'content_layout!'          => 'days_only'
				]
			]
		);
		$this->add_control(
			'content_labels_days_label_days_only',
			[
				'label'     => __( 'Days Label', 'thegem' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'My Countdown Text', 'thegem' ),
				'condition' => [ 'content_layout' => 'days_only' ]
			]
		);
		$this->add_control(
			'content_labels_show_hours',
			[
				'label'     => __( 'Show Hours', 'thegem' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'content_layout!' => 'days_only',
				],
			]
		);
		$this->add_control(
			'content_labels_hours_label',
			[
				'label'     => __( 'Hours Label', 'thegem' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Hours', 'thegem' ),
				'condition' => [
					'content_layout!'           => 'days_only',
					'content_labels_show_hours' => 'yes',
				],
			]
		);
		$this->add_control(
			'content_labels_show_minutes',
			[
				'label'     => __( 'Show Minutes', 'thegem' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'content_layout!' => 'days_only',
				],
			]
		);
		$this->add_control(
			'content_labels_minutes_label',
			[
				'label'     => __( 'Minutes Label', 'thegem' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Minutes', 'thegem' ),
				'condition' => [
					'content_layout!'             => 'days_only',
					'content_labels_show_minutes' => 'yes',
				],
			]
		);
		$this->add_control(
			'content_labels_show_seconds',
			[
				'label'     => __( 'Show Seconds', 'thegem' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'content_layout!' => 'days_only',
				],
			]
		);
		$this->add_control(
			'content_labels_seconds_label',
			[
				'label'     => __( 'Seconds Label', 'thegem' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Seconds', 'thegem' ),
				'condition' => [
					'content_layout!'             => 'days_only',
					'content_labels_show_seconds' => 'yes',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'content_link',
			[
				'tab'   => Controls_Manager::TAB_CONTENT,
				'label' => __( 'Link', 'thegem' ),
			]
		);
		$this->add_control(
			'content_link_url',
			[
				'label' => __( 'Link', 'thegem' ),
				'type'  => Controls_Manager::URL,
			]
		);
		$this->end_controls_section();
	}

	/**
	 * Style Settings
	 */
	protected function style_settings() {
		$this->container_style();
		$this->numbers_style();
		$this->labels_style();
		$this->times_style( 'days_style_', __( 'Days Style (Custom)', 'thegem' ), '.count-1' );
		$this->times_style( 'hours_style_', __( 'Hours Style (Custom)', 'thegem' ), '.count-2' );
		$this->times_style( 'minutes_style_', __( 'Minutes Style (Custom)', 'thegem' ), '.count-3' );
		$this->times_style( 'seconds_style_', __( 'Seconds Style (Custom)', 'thegem' ), '.count-4' );
	}

	protected function container_style() {
		$this->start_controls_section(
			'style_container_settings',
			[
				'label' => __( 'Container Style', 'thegem' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'container_width',
			[
				'label'          => __( 'Container Width', 'thegem' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'unit' => '%',
					'size' => 100,
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units'     => [ '%', 'px' ],
				'condition'      => [
					'content_layout' => [ 'boxes' ]
				],
				'selectors'      => [
					'{{WRAPPER}} .countdown-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'space_between',
			[
				'label'          => __( 'Space Between', 'thegem' ),
				'type'           => Controls_Manager::SLIDER,
				'condition'      => [
					'content_layout' => [ 'boxes' ]
				],
				'default'        => [
					'size' => 49,
				],
				'mobile_default' => [
					'size' => 4
				],
				'range'          => [
					'px' => [
						'min' => -1,
						'max' => 200,
					],
				],
				'description' => __('Use -1 to collapse the borders', 'thegem'),
				'selectors'      => [
//					'{{WRAPPER}} .countdown-style-3 .countdown-wrapper .countdown-item:not(:first-of-type)' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
//					'{{WRAPPER}} .countdown-style-3 .countdown-wrapper .countdown-item:not(:last-of-type)'  => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .countdown-style-3 .countdown-wrapper .countdown-item:not(:last-of-type)'  => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'space_between_circles',
			[
				'label'          => __( 'Space Between', 'thegem' ),
				'type'           => Controls_Manager::SLIDER,
				'condition'      => [
					'content_layout' => [ 'circles' ]
				],
				'default'        => [
					'size' => 74,
				],
				'tablet_default' => [
					'size' => 33
				],
				'mobile_default' => [
					'size' => 0
				],
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'      => [
					'body[data-elementor-device-mode=tablet] {{WRAPPER}} .countdown-style-5 .countdown-wrapper .countdown-item'                    => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'body[data-elementor-device-mode=mobile] {{WRAPPER}} .countdown-style-5 .countdown-wrapper .countdown-item'                    => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'body[data-elementor-device-mode=desktop] {{WRAPPER}} .countdown-style-5 .countdown-wrapper .countdown-item' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'body[data-elementor-device-mode=desktop] {{WRAPPER}} .countdown-style-5 .countdown-wrapper .countdown-item'  => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
				],
			]
		);
		$this->add_responsive_control(
			'space_bottom_circles',
			[
				'label'          => __( 'Space Bottom', 'thegem' ),
				'type'           => Controls_Manager::SLIDER,
				'condition'      => [
					'content_layout' => [ 'circles' ]
				],
				'default'        => [
					'size' => 30,
				],
				'tablet_default' => [
					'size' => 30
				],
				'mobile_default' => [
					'size' => 40
				],
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'      => [
					'{{WRAPPER}} .countdown-style-5 .countdown-wrapper .countdown-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .countdown-style-5 .countdown-wrapper' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'radius_normal',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Radius', 'thegem' ),
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
				'condition'  => [ 'content_layout' => 'boxes' ]
			]
		);
		$this->add_control(
			'border_type_boxes',
			[
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Border Type', 'thegem' ),
				'options'   => [
					''       => __( 'None', 'thegem' ),
					'solid'  => _x( 'Solid', 'Border Control', 'thegem' ),
					'double' => _x( 'Double', 'Border Control', 'thegem' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'thegem' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'thegem' ),
				],
				'default'   => 'solid',
				'selectors' => [
					'{{WRAPPER}} .countdown-style-3 .wrap' => 'border-style: {{VALUE}};',
				],
				'condition' => [ 'content_layout' => 'boxes' ]
			]
		);
		$this->add_control(
			'border_width_boxes',
			[
				'label'      => __( 'Border Width', 'thegem' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .countdown-style-3 .wrap' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'border_type_boxes!' => '',
					'content_layout'     => 'boxes',
				],
				'default'    => [
					'top'      => '1',
					'right'    => '1',
					'bottom'   => '1',
					'left'     => '1',
					'isLinked' => true,
				],
			]
		);
		$this->add_responsive_control(
			'padding',
			[
				'label'          => __( 'Padding', 'thegem' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'condition'      => [
					'content_layout' => 'boxes'
				],
				'size_units'     => [ 'px', '%', 'em' ],
				'default'        => [
					'top'      => '65',
					'right'    => '20',
					'bottom'   => '65',
					'left'     => '20',
					'isLinked' => false,
				],
				'tablet_default' => [
					'top'      => '25',
					'right'    => '10',
					'bottom'   => '25',
					'left'     => '10',
					'isLinked' => false,
				],
				'mobile_default' => [
					'top'      => '10',
					'right'    => '10',
					'bottom'   => '10',
					'left'     => '10',
					'isLinked' => true,
				],
				'selectors'      => [
					'{{WRAPPER}} .countdown-item .wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// For Boxes
		$this->start_controls_tabs(
			'background_tabs_boxes',
			[
				'condition' => [
					'content_layout' => 'boxes'
				]
			]
		);
		$this->start_controls_tab(
			'background_normal_boxes',
			[
				'label' => __( 'Normal', 'thegem' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'background_normal_boxes',
				'label'    => __( 'Background Type', 'thegem' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .countdown-item .wrap',
			]
		);
		$this->add_control(
			'border_color_normal',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Border Color', 'thegem' ),
				'default'   => '#333333',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .countdown-style-3 .wrap' => 'border-color: {{VALUE}};'
				],
				'condition' => [ 'border_type_boxes!' => '' ]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'        => 'shadow_normal_boxes',
				'label'       => __( 'Shadow', 'thegem' ),
				'render_type' => 'template',
				'selector'    => '{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .wrap',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'background_hover_boxes',
			[
				'label' => __( 'Hover', 'thegem' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'        => 'background_hover_boxes',
				'label'       => __( 'Background Type', 'thegem' ),
				'types'       => [ 'classic', 'gradient' ],
				'render_type' => 'template',
				'selector'    => '{{WRAPPER}} .countdown-style-3 .countdown-wrapper.countdown-info .countdown-item .wrap:hover',
			]
		);
		$this->add_control(
			'border_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Border Color', 'thegem' ),
				'selectors' => [
					'{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .wrap:hover' => 'border-color: {{VALUE}};'
				],
				'condition' => [ 'border_type_boxes!' => '' ]
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'        => 'shadow_hover_boxes',
				'label'       => __( 'Shadow', 'thegem' ),
				'render_type' => 'template',
				'selector'    => '{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .wrap:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		// For Circles
		$this->start_controls_tabs(
			'background_tabs_circles',
			[
				'condition' => [
					'content_layout' => 'circles'
				]
			]
		);
		$this->start_controls_tab(
			'background_tab_normal_circles',
			[
				'label' => __( 'Normal', 'thegem' ),
			]
		);
		$this->add_control(
			'background_normal_circles',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'thegem' ),
				'default'   => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .countdown-style-5 circle.inner-circle' => 'fill: {{VALUE}};transition:fill .3s linear;'
				]
			]
		);
		$this->add_control(
			'base_color_normal',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Base Color', 'thegem' ),
				'default'   => 'rgba(51, 51, 51, 0.3)',
				'selectors' => [
					'{{WRAPPER}} .countdown-style-5 circle.base-circle' => 'stroke: {{VALUE}};transition: stroke .3s linear;'
				]

			]
		);
		$this->add_control(
			'level_color_normal',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Level Color', 'thegem' ),
				'default'   => 'rgb(51, 51, 51)',
				'selectors' => [
					'{{WRAPPER}} .countdown-style-5 path' => 'stroke: {{VALUE}};transition: stroke .3s linear;'
				]
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_background_hover_circles',
			[
				'label' => __( 'Hover', 'thegem' ),
			]
		);
		$this->add_control(
			'background_hover_circles',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'thegem' ),
				'selectors' => [
					'{{WRAPPER}} .countdown-style-5 .countdown-item:hover circle.inner-circle' => 'fill: {{VALUE}};'
				]
			]
		);
		$this->add_control(
			'base_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Base Color', 'thegem' ),
				'selectors' => [
					'{{WRAPPER}} .countdown-style-5 .countdown-item:hover circle.base-circle' => 'stroke: {{VALUE}};'
				]
			]
		);
		$this->add_control(
			'level_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Level Color', 'thegem' ),
				'selectors' => [
					'{{WRAPPER}} .countdown-style-5 .countdown-item:hover path' => 'stroke: {{VALUE}};'
				]
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		// position
		$this->add_control(
			'position',
			[
				'type'      => Controls_Manager::CHOOSE,
				'label'     => __( 'Position', 'thegem' ),
				'options'   => [
					'flex-start' => [
						'title' => __( 'Left', 'thegem' ),
						'icon'  => 'fa fa-align-left',
					],
					'center'     => [
						'title' => __( 'Center', 'thegem' ),
						'icon'  => 'fa fa-align-center',
					],
					'flex-end'   => [
						'title' => __( 'Right', 'thegem' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => 'center',
				'condition' => [
					'content_layout' => [ 'elegant', 'cross', 'days_only' ]
				]
			]
		);
		$this->add_control(
			'separator_width',
			[
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Separator Width', 'thegem' ),
				'range'       => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default'     => [
					'unit' => 'px',
					'size' => 1,
				],
				'render_type' => 'template',
				'selectors'   => [
					'{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .wrap'                                       => 'border-width: {{SIZE}}{{UNIT}};',
					"{{WRAPPER}} .countdown-style-6 .countdown-wrapper.countdown-info .countdown-item .countdown-item-border-1" => 'height: {{SIZE}}px;',
					"{{WRAPPER}} .countdown-style-6 .countdown-wrapper.countdown-info .countdown-item .countdown-item-border-2" => 'width: {{SIZE}}px;',
					"{{WRAPPER}} .countdown-style-6 .countdown-wrapper.countdown-info .countdown-item .countdown-item-border-3" => 'width: {{SIZE}}px;',
					"{{WRAPPER}} .countdown-style-6 .countdown-wrapper.countdown-info .countdown-item .countdown-item-border-4" => 'height: {{SIZE}}px;',
				],
				'condition'   => [
					'content_layout' => [ 'elegant', 'cross' ]
				]
			]
		);
		$this->add_control(
			'separator_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Separator Color', 'thegem' ),
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .wrap'                                     => 'border-color: {{VALUE}};',
					"{{WRAPPER}} .countdown-style-6 .countdown-wrapper.countdown-info .countdown-item .countdown-item-border" => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'content_layout' => [ 'elegant', 'cross' ]
				]
			]
		);
		$this->add_control(
			'circles_size',
			[
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Circles Size', 'thegem' ),
				'render_type' => 'template',
				'size_units'  => [ 'px' ],
				'range'       => [
					'px' => [
						'min' => 0,
						'max' => 600,
					],
				],
				'default'     => [
					'unit' => 'px',
					'size' => 216,
				],
				'condition'   => [
					'content_layout' => 'circles'
				],
				'selectors'   => [
					'{{WRAPPER}} .countdown-style-5 .countdown-item'         => 'width: {{SIZE}}px;',
					'{{WRAPPER}} .countdown-style-5 .circle-raphael-days'    => 'width: {{SIZE}}px;height:{{SIZE}}px;',
					'{{WRAPPER}} .countdown-style-5 .circle-raphael-hours'   => 'width: {{SIZE}}px;height:{{SIZE}}px;',
					'{{WRAPPER}} .countdown-style-5 .circle-raphael-minutes' => 'width: {{SIZE}}px;height:{{SIZE}}px;',
					'{{WRAPPER}} .countdown-style-5 .circle-raphael-seconds' => 'width: {{SIZE}}px;height:{{SIZE}}px;',
					'{{WRAPPER}} .countdown-style-5 .wrap'                   => 'width: {{SIZE}}px;height:{{SIZE}}px;',
				]
			]
		);
		$this->add_control(
			'circles_weight',
			[
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Circles Weight', 'thegem' ),
				'render_type' => 'template',
				'size_units'  => [ 'px' ],
				'range'       => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'     => [
					'unit' => 'px',
					'size' => 8,
				],
				'condition'   => [
					'content_layout' => 'circles'
				]
			]
		);
		$this->add_control(
			'container_style_customize_separately',
			[
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Customize separately', 'thegem' ),
				'default'   => '',
				'label_on'  => __( 'On', 'thegem' ),
				'label_off' => __( 'Off', 'thegem' ),
				'condition' => [ 'content_layout!' => 'days_only' ]
			]
		);
		$this->end_controls_section();
	}

	protected function numbers_style() {
		$this->start_controls_section(
			'numbers_style',
			[
				'label' => __( 'Numbers Style', 'thegem' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);
		$this->add_control(
			'numbers_style_bottom_spacing',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Bottom Spacing', 'thegem' ),
				'condition'  => [
					'content_layout!' => 'cross'
				],
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'%'   => [
						'min' => - 100,
						'max' => 100,
					],
					'px'  => [
						'min' => - 400,
						'max' => 400,
					],
					'em'  => [
						'min' => - 100,
						'max' => 100,
					],
					'rem' => [
						'min' => - 100,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .countdown-style-3 .item-count'                 => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .countdown-style-4 .item-count'                 => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .countdown-style-5 .item-title.styled-subtitle' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .countdown-style-7 .wrap'                       => 'margin-bottom: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->start_controls_tabs(
			'number_tabs', []
		);
		$this->start_controls_tab(
			'number_tabs_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'number_typography_normal',
				'label'    => __( 'Typography', 'thegem' ),
				'selector' => '{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .item-count',
			]
		);
		$this->add_control(
			'number_color_normal',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'thegem' ),
				'selectors' => [
					'{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .item-count' => 'color:{{VALUE}};',
					'{{WRAPPER}} .countdown-style-7 .wrap'                                      => 'color:{{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'number_tabs_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'number_typography_hover',
				'label'    => __( 'Typography', 'thegem' ),
				'selector' => '{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item:hover .item-count',
			]
		);
		$this->add_control(
			'number_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'thegem' ),
				'selectors' => [
					'{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item:hover .item-count' => 'color:{{VALUE}};',
					'{{WRAPPER}} .countdown-style-7 .wrap:hover .item-count'                          => 'color:{{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'number_style_customize_separately',
			[
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Customize separately', 'thegem' ),
				'default'   => '',
				'label_on'  => __( 'On', 'thegem' ),
				'label_off' => __( 'Off', 'thegem' ),
				'condition' => [ 'content_layout!' => 'days_only' ]
			]
		);
		$this->end_controls_section();
	}

	protected function labels_style() {
		$this->start_controls_section(
			'labels_style',
			[
				'label' => __( 'Labels Style', 'thegem' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);
		$this->add_control(
			'numbers_style_top_spacing',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Top Spacing', 'thegem' ),
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 120,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 15,
				],
				'condition'  => [
					'content_layout' => 'cross'
				],
				'selectors'  => [
					'{{WRAPPER}} .countdown-style-6 .item-title' => 'top: {{SIZE}}{{UNIT}};'
				]
			]
		);
		$this->start_controls_tabs(
			'labels_tabs', []
		);
		$this->start_controls_tab(
			'labels_tabs_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'labels_typography_normal',
				'label'    => __( 'Typography', 'thegem' ),
				'selector' => '{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .item-title, {{WRAPPER}} .countdown-style-7 .countdown-text',
			]
		);
		$this->add_control(
			'labels_color_normal',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'thegem' ),
				'selectors' => [
					'{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .item-title' => 'color:{{VALUE}};',
					'{{WRAPPER}} .countdown-style-7 .countdown-text'                            => 'color:{{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'labels_tabs_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'labels_typography_hover',
				'label'    => __( 'Typography', 'thegem' ),
				'selector' => '{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item:hover .item-title, {{WRAPPER}} .countdown-style-7:hover .countdown-text',
			]
		);
		$this->add_control(
			'labels_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'thegem' ),
				'selectors' => [
					'{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item:hover .item-title' => 'color:{{VALUE}};',
					'{{WRAPPER}} .countdown-style-7:hover .countdown-text'                            => 'color:{{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'labels_style_customize_separately',
			[
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Customize separately', 'thegem' ),
				'default'   => '',
				'label_on'  => __( 'On', 'thegem' ),
				'label_off' => __( 'Off', 'thegem' ),
				'condition' => [ 'content_layout!' => 'days_only' ]
			]
		);
		$this->end_controls_section();
	}

	/**
	 * content_layout == boxes || circles
	 * @return array
	 */
	protected function _condition_boxes_or_circles() {
		return [
			'relation' => 'or',
			'terms'    => [
				[
					'name'     => 'content_layout',
					'operator' => '==',
					'value'    => 'boxes'
				],
				[
					'name'     => 'content_layout',
					'operator' => '==',
					'value'    => 'circles'
				]
			]
		];
	}

	protected function times_style( $section_id, $section_title, $item_class ) {
		/*
		 * Container
		 */
		$this->start_controls_section(
			$section_id,
			[
				'label'      => $section_title,
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'  => 'container_style_customize_separately',
							'value' => 'yes'
						],
						[
							'name'  => 'number_style_customize_separately',
							'value' => 'yes'
						],
						[
							'name'  => 'labels_style_customize_separately',
							'value' => 'yes'
						],
					]
				]
			]
		);

		// For Boxes
		$this->start_controls_tabs(
			$section_id . 'background_tabs_boxes',
			[
				'conditions' => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'container_style_customize_separately',
							'operator' => '==',
							'value'    => 'yes'
						],
						[
							'name'     => 'content_layout',
							'operator' => '==',
							'value'    => 'boxes'
						]
					]
				]
			]
		);
		$this->start_controls_tab(
			$section_id . 'background_normal_boxes',
			[
				'label' => __( 'Normal', 'thegem' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => $section_id . 'background_normal_boxes',
				'label'    => __( 'Background Type', 'thegem' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => "{{WRAPPER}} .countdown-style-3 .countdown-wrapper.countdown-info .countdown-item{$item_class} .wrap",
			]
		);
		$this->add_control(
			$section_id . 'radius_normal',
			[
				'type'      => Controls_Manager::DIMENSIONS,
				'label'     => __( 'Radius', 'thegem' ),
				'selectors' => [
					"{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class} .wrap" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => $section_id . 'border_normal',
				'label'    => __( 'Border', 'thegem' ),
				'selector' => "{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class} .wrap",
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => $section_id . 'shadow_normal_boxes',
				'label'     => __( 'Shadow', 'thegem' ),
				'selector'  => "{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class} .wrap",
				'condition' => [ 'content_layout!' => 'circles' ]
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			$section_id . 'background_hover_boxes',
			[
				'label' => __( 'Hover', 'thegem' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => $section_id . 'background_hover_boxes',
				'label'    => __( 'Background Type', 'thegem' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => "{{WRAPPER}} .countdown-style-3 .countdown-wrapper.countdown-info .countdown-item{$item_class}:hover .wrap",
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => $section_id . 'border_hover',
				'label'    => __( 'Border', 'thegem' ),
				'selector' => "{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class}:hover .wrap",
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => $section_id . 'shadow_hover_boxes',
				'label'     => __( 'Shadow', 'thegem' ),
				'selector'  => "{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class}:hover .wrap",
				'condition' => [ 'content_layout!' => 'circles' ]
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		// For Circles
		$this->start_controls_tabs(
			$section_id . 'background_tabs_circles',
			[
				'conditions' => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'container_style_customize_separately',
							'operator' => '==',
							'value'    => 'yes'
						],
						[
							'name'     => 'content_layout',
							'operator' => '==',
							'value'    => 'circles'
						]
					]
				]
			]
		);
		$this->start_controls_tab(
			$section_id . 'tab_background_normal_circles',
			[
				'label' => __( 'Normal', 'thegem' ),
			]
		);
		$this->add_control(
			$section_id . 'background_normal_circles',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'thegem' ),
				'selectors' => [
					"{{WRAPPER}} .countdown-style-5 {$item_class} circle.inner-circle" => 'fill: {{VALUE}};transition:fill .3s linear;'
				]
			]
		);
		$this->add_control(
			$section_id . 'base_color_normal',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Base Color', 'thegem' ),
				'selectors' => [
					"{{WRAPPER}} .countdown-style-5 {$item_class} circle.base-circle" => 'stroke: {{VALUE}};'
				]

			]
		);
		$this->add_control(
			$section_id . 'level_color_normal',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Level Color', 'thegem' ),
				'selectors' => [
					"{{WRAPPER}} .countdown-style-5 {$item_class} path" => 'stroke: {{VALUE}};'
				]
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			$section_id . 'tab_background_hover_circles',
			[
				'label' => __( 'Hover', 'thegem' ),
			]
		);
		$this->add_control(
			$section_id . 'background_hover_circles',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'thegem' ),
				'selectors' => [
					"{{WRAPPER}} .countdown-style-5 {$item_class}:hover circle.inner-circle" => 'fill: {{VALUE}};transition:fill .3s linear;'
				]
			]
		);
		$this->add_control(
			$section_id . 'base_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Base Color', 'thegem' ),
				'selectors' => [
					"{{WRAPPER}} .countdown-style-5 {$item_class}:hover circle.base-circle" => 'stroke: {{VALUE}};'
				]

			]
		);
		$this->add_control(
			$section_id . 'level_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Level Color', 'thegem' ),
				'selectors' => [
					"{{WRAPPER}} .countdown-style-5 {$item_class}:hover path" => 'stroke: {{VALUE}};'
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			$section_id . 'position',
			[
				'type'      => Controls_Manager::CHOOSE,
				'label'     => __( 'Position', 'thegem' ),
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'thegem' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'thegem' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'thegem' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'condition' => [ 'container_style_customize_separately' => 'yes', 'content_layout' => 'days_only' ],
			]
		);
		$this->add_control(
			$section_id . 'separator_width',
			[
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Separator Width', 'thegem' ),
				'render_type' => 'template',
				'size_units'  => 'px',
				'range'       => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'     => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors'   => [
					"{{WRAPPER}} .countdown-style-4 .countdown-wrapper.countdown-info .countdown-item{$item_class} .wrap"                    => 'border-width: {{SIZE}}px;',
					"{{WRAPPER}} .countdown-style-6 .countdown-wrapper.countdown-info .countdown-item{$item_class} .countdown-item-border-1" => 'height: {{SIZE}}px;',
					"{{WRAPPER}} .countdown-style-6 .countdown-wrapper.countdown-info .countdown-item{$item_class} .countdown-item-border-2" => 'width: {{SIZE}}px;',
					"{{WRAPPER}} .countdown-style-6 .countdown-wrapper.countdown-info .countdown-item{$item_class} .countdown-item-border-3" => 'width: {{SIZE}}px;',
					"{{WRAPPER}} .countdown-style-6 .countdown-wrapper.countdown-info .countdown-item{$item_class} .countdown-item-border-4" => 'height: {{SIZE}}px;',
				],
				'conditions'  => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'container_style_customize_separately',
							'operator' => '==',
							'value'    => 'yes'
						],
						[
							'name'     => 'content_layout',
							'operator' => 'in',
							'value'    => [ 'elegant', 'cross' ]
						]
					]
				]
			]
		);
		$this->add_control(
			$section_id . 'separator_color',
			[
				'type'        => Controls_Manager::COLOR,
				'label'       => __( 'Separator Color', 'thegem' ),
				'render_type' => 'template',
				'selectors'   => [
					"{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class} .wrap" => 'border-color: {{VALUE}};',
				],
				'conditions'  => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'container_style_customize_separately',
							'operator' => '==',
							'value'    => 'yes'
						],
						[
							'name'     => 'content_layout',
							'operator' => 'in',
							'value'    => [ 'elegant', 'cross' ]
						]
					]
				]
			]
		);

		/*
		 * Numbers
		 */
		$this->add_control(
			$section_id . 'numbers_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Numbers', 'thegem' ),
				'condition' => [ 'number_style_customize_separately' => 'yes' ]
			]
		);
		$this->add_control(
			$section_id . 'numbers_style_bottom_spacing',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Bottom Spacing', 'thegem' ),
				'conditions' => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'number_style_customize_separately',
							'operator' => '==',
							'value'    => 'yes'
						],
						[
							'name'     => 'content_layout',
							'operator' => '!==',
							'value'    => 'cross'
						]
					]
				],
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'%'   => [
						'min' => - 100,
						'max' => 100,
					],
					'px'  => [
						'min' => - 400,
						'max' => 400,
					],
					'em'  => [
						'min' => - 100,
						'max' => 100,
					],
					'rem' => [
						'min' => - 100,
						'max' => 100,
					],
				],
				'selectors'  => [
					"{{WRAPPER}} .countdown-style-3 .countdown-wrapper.countdown-info .countdown-item{$item_class} .item-count" => 'margin-bottom: {{SIZE}}{{UNIT}};',
					"{{WRAPPER}} .countdown-style-5 .countdown-item{$item_class} .item-title.styled-subtitle"                   => 'margin-top: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->start_controls_tabs(
			$section_id . 'number_tabs',
			[
				'condition' => [ 'number_style_customize_separately' => 'yes' ]
			]
		);
		$this->start_controls_tab(
			$section_id . 'number_tabs_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $section_id . 'number_typography_normal',
				'label'    => __( 'Typography', 'thegem' ),
				'selector' => "{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class} .item-count",
			]
		);
		$this->add_control(
			$section_id . 'number_color_normal',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'thegem' ),
				'selectors' => [
					"{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class} .item-count" => 'color: {{VALUE}};'
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			$section_id . 'number_tabs_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $section_id . 'number_typography_hover',
				'label'    => __( 'Typography', 'thegem' ),
				'selector' => "{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class}:hover .item-count",
			]
		);
		$this->add_control(
			$section_id . 'number_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'thegem' ),
				'selectors' => [
					"{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class}:hover .item-count" => 'color: {{VALUE}};'
				]
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		/*
		 * Label
		 */
		$this->add_control(
			$section_id . 'label',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Label', 'thegem' ),
				'condition' => [ 'labels_style_customize_separately' => 'yes' ]
			]
		);
		$this->start_controls_tabs(
			$section_id . 'labels_tabs', [
				'condition' => [ 'labels_style_customize_separately' => 'yes' ]
			]
		);
		$this->start_controls_tab(
			$section_id . 'labels_tabs_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $section_id . 'labels_typography_normal',
				'label'    => __( 'Typography', 'thegem' ),
				'selector' => "{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class} .item-title",
			]
		);
		$this->add_control(
			$section_id . 'labels_color_normal',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'thegem' ),
				'selectors' => [
					"{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class} .item-title" => 'color: {{VALUE}};'
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			$section_id . 'labels_tabs_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $section_id . 'labels_typography_hover',
				'label'    => __( 'Typography', 'thegem' ),
				'selector' => "{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class}:hover .item-title",
			]
		);
		$this->add_control(
			$section_id . 'labels_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'thegem' ),
				'selectors' => [
					"{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class}:hover .item-title" => 'color: {{VALUE}};'
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}


	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	public function render() {
		$settings = $this->get_settings_for_display();

		$template = __DIR__ . "/templates/template-{$settings['content_layout']}.php";

		$show_link           = isset( $settings['content_link_url'] );
		$countdown_wrap_link = 'countdown-wrap-link';
		if ( $show_link ) {
			$this->add_link_attributes( $countdown_wrap_link, $settings['content_link_url'] );
			$this->add_render_attribute( $countdown_wrap_link, 'class', 'countdown-wrap-link' );
		}

		if ( ! empty( $template ) && file_exists( $template ) ) {
			include $template;
		}

	}
}

Plugin::instance()->widgets_manager->register_widget_type( new TheGem_Countdown() );
