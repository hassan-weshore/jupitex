<?php
namespace TheGem_Elementor\Widgets\CTA;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Plugin;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Schemes;


if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Elementor widget for CTA.
 */
class TheGem_CTA extends Widget_Base {

	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		if( ! defined( 'THEGEM_ELEMENTOR_WIDGET_CTA_DIR' ) ){
			define( 'THEGEM_ELEMENTOR_WIDGET_CTA_DIR', rtrim( __DIR__, ' /\\' ) );
		}

		if ( ! defined( 'THEGEM_ELEMENTOR_WIDGET_CTA_URL' ) ) {
			define( 'THEGEM_ELEMENTOR_WIDGET_CTA_URL', rtrim( plugin_dir_url( __FILE__ ), ' /\\' ) );
		}
		wp_register_style( 'thegem-cta', THEGEM_ELEMENTOR_WIDGET_CTA_URL . '/assets/css/thegem-cta.css', array('thegem-icon-css'), null );
	}


	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-cta';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Call To Action', 'thegem' );
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
		return [ 'thegem_elements' ];
	}

	public function get_style_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return [
				'thegem-cta',
				'thegem-button'];
		}		
		return [ 'thegem-cta' ];
	}

	/*Show reload button*/
	public function is_reload_preview_required() {
		return true;
	}


	/**
	 * Retrieve the value setting
	 * @access public
	 *
	 * @param string $control_id Control id
	 * @param string $control_sub Control value name (size, unit)
	 *
	 * @return string
	 */
	public function get_val( $control_id, $control_sub = null ) {
		if ( empty( $control_sub ) ) {
			return $this->get_settings()[ $control_id ];
		} else {
			return $this->get_settings()[ $control_id ][ $control_sub ];
		}
	}

	/**
	 * Create presets options for Select
	 *
	 * @access protected
	 * @return array
	 */
	protected function get_presets_options() {
		$out = array(
			'solid-box'     => __( 'Solid Box', 'thegem' ),
			'outlined-bold' => __( 'Outlined Bold', 'thegem' ),
			'outlined-thin' => __( 'Outlined Thin', 'thegem' ),
			'left-aligned'  => __( 'Left Aligned', 'thegem' ),
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
		return 'solid-box';
	}

	/**
	 * Get the list of all local section templates
	 *
	 * @access protected
	 * @return array
	 */
	protected function get_local_section_templates()
	{
		$items = Plugin::instance()->templates_manager->get_source('local')->get_items();
		if (!empty($items)) {
			$items = wp_list_filter($items, ['type' => 'section']);
			$items = wp_list_pluck($items, 'title', 'template_id');
			return $items;
		}
		return [];
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Layout', 'thegem' ),
			]
		);

		$this->add_control(
			'thegem_elementor_preset',
			[
				'label'   => __( 'Skin', 'thegem' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_presets_options(),
				'default' => $this->set_default_presets_options(),
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_description_settings',
			[
				'label' => __( 'Title & Description', 'thegem' ),
			]
		);

		$this->add_control(
			'source',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __('Content Source', 'thegem'),
				'default' => 'editor',
				'separator' => 'before',
				'options' => [
					'editor' => __('Editor', 'thegem'),
					'template' => __('Template', 'thegem'),
				]
			]
		);

		$this->add_control(
			'template',
			[
				'label' => __('Section Template', 'thegem'),
				'placeholder' => __('Select a section template for as tab content', 'thegem'),
				'label_block' => true,
				'description' => sprintf(
					__('Wondering what is section template or need to create one? Please click %1$shere%2$s ', 'thegem'),
					'<a target="_blank" href="' . esc_url(admin_url('/edit.php?post_type=elementor_library&tabs_group=library&elementor_library_type=section')) . '">',
					'</a>'
				),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_local_section_templates(),
				'condition' => [
					'source' => 'template',
				]
			]
		);

		$this->add_control(
			'heading_block_title',
			[
				'label' => __( 'Title', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 
					'source' => 'editor'
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title Text', 'thegem' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'CALL TO ACTION BOX', 'thegem' ),
				'placeholder' => __( 'Type your title here', 'thegem' ),
				'label_block' => 'true',
				'condition' => [ 
					'source' => 'editor'
				],
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label' => __( 'Title HTML Tag', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default' => 'h5',
				'condition' => [ 
					'source' => 'editor'
				],
			]
		);

		$this->add_control(
			'title_html_tag_weight',
			[
				'label' => __( 'Title Weight', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'bold',
				'options' => [
					'bold' => __( 'Bold', 'thegem' ),
					'light' => __( 'Thin', 'thegem' ),
				],
				'condition' => [ 
					'source' => 'editor'
				],
			]
		);

		$this->add_control(
			'title_html_tag_h_disable',
			[
				'label' => __( 'Disable H-tag', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'thegem' ),
				'label_off' => __( 'No', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [ 
					'source' => 'editor'
				],
			]
		);
		$this->add_control(
			'heading_block_description',
			[
				'label' => __( 'Description', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 
					'source' => 'editor'
				],
			]
		);

		$this->add_control(
			'text',
			[
				'label' => __( 'Content Text', 'thegem' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt', 'thegem' ),
				'placeholder' => __( 'Type your text here', 'thegem' ),
				'condition' => [ 
					'source' => 'editor'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'heading_block_icon_image',
			[
				'label' => __( 'Icon / Image', 'thegem' ),
			]
		);

		$this->add_control(
			'icon_image_select',
			[
				'label' => __( 'Icon / Image', 'thegem' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'none' => [
						'title' => __( 'None', 'thegem' ),
						'icon' => 'eicon-ban',
					],
					'image' => [
						'title' => __( 'Image', 'thegem' ),
						'icon' => 'fa fa-picture-o',
					],
					'icon' => [
						'title' => __( 'Icon', 'thegem' ),
						'icon' => 'eicon-star',
					],
				],
				'default' => 'icon',
			]
		);

		$this->add_control(
			'graphic_image',
			[
				'label' => __( 'Choose Image', 'thegem' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'icon_image_select' => 'image',
				],
				'show_label' => false,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'graphic_image',
				'default' => 'thumbnail',
				'condition' => [
					'icon_image_select' => 'image',
					'graphic_image[id]!' => '',
				],
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label' => __( 'Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-exclamation-circle', 
					'library' => 'fa-solid',
				],
				'condition' => [
					'icon_image_select' => 'icon',
				],
			]
		);

		$this->add_control(
			'icon_image_link_to',
			[
				'label' => __( 'Link', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => __( 'None', 'thegem' ),
					'file' => __( 'Media File', 'thegem' ),
					'custom' => __( 'Custom URL', 'thegem' ),
				],
			]
		);

		$this->add_control(
			'icon_image_link',
			[
				'label' => __( 'Link', 'thegem' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'thegem' ),
				'condition' => [
					'icon_image_link_to' => 'custom',

				],
				'show_label' => false,
			]
		);

		$this->add_control(
			'icon_image_open_lightbox',
			[
				'label' => __( 'Lightbox', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'yes',
				'options' => [
					'yes' => __( 'Yes', 'thegem' ),
					'no' => __( 'No', 'thegem' ),
				],
				'condition' => [
					'icon_image_link_to' => 'file',

				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'heading_block_button',
			[
				'label' => __( 'Button', 'thegem' ),
			]
		);

		$this->add_control(
			'show_button_1',
			[
				'label' => __( 'Show Button', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'button_1_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Buy Now!', 'thegem'),
				'condition' => [
					'show_button_1' => 'yes'
				],
			]
		);

		$this->add_control(
			'button_1_text_weight',
			[
				'label' => __('Text Weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'bold',
				'options' => [
					'bold' => __('Bold', 'thegem'),
					'thin' => __('Thin', 'thegem'),
				],
				'condition' => [ 
					'show_button_1' => 'yes'
				],
			]
		);

		$this->add_control(
			'button_1_link',
			[
				'label' => __('Button Link', 'thegem'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('#', 'thegem'),
				'condition' => [
					'show_button_1' => 'yes'
				],
			]
		);

		$this->add_control(
			'button_1_icon',
			[
				'label' => __( 'Button Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => '',
					'library' => '',
				],
				'condition' => [
					'show_button_1' => 'yes'
				],
			]
		);

		$this->add_control(
			'show_button_2',
			[
				'label' => __( 'Show Second Button', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'separator' => 'before',
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'button_2_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Features', 'thegem'),
				'condition' => [
					'show_button_2' => 'yes'
				],
			]
		);

		$this->add_control(
			'button_2_text_weight',
			[
				'label' => __('Text Weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'bold',
				'options' => [
					'bold' => __('Bold', 'thegem'),
					'thin' => __('Thin', 'thegem'),
				],
				'condition' => [ 
					'show_button_2' => 'yes'
				],
			]
		);

		$this->add_control(
			'button_2_link',
			[
				'label' => __('Button Link', 'thegem'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('#', 'thegem'),
				'condition' => [
					'show_button_2' => 'yes'
				],
			]
		);

		$this->add_control(
			'button_2_icon',
			[
				'label' => __( 'Button Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => '',
					'library' => '',
				],
				'condition' => [
					'show_button_2' => 'yes'
				],
			]
		);

		$this->end_controls_section();

		$this->add_styles_controls( $this );
	}

	/**
	 * Controls call
	 * @access public
	 */
	public function add_styles_controls($control) {

		$this->control = $control;

		/* Container Styles*/
		$this->container_styles( $control );

		/* Title and Description Styles */
		$this->title_description_styles( $control );

		/* Icon/Image Styles */
		$this->icon_image_styles( $control );

		/* First Button Styles */
		$this->button_1_styles( $control );

		/* Second Button Styles */
		$this->button_2_styles( $control );

	}

    /**
	 * Container Styles
	 * @access protected
	 */
	protected function container_styles( $control ) {

		$control->start_controls_section(
			'container',
			[
				'label' => __( 'Container Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'container_bg_color',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .gem-alert-inner',
			]
		);

		$control->add_responsive_control(
			'container_radius',
			[
				'label' => __( 'Radius', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-alert-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'label' => __( 'Border', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-alert-inner',
			]
		);

		$control->add_responsive_control(
			'container_align',
			[
				'label' => __( 'Content Align', 'thegem' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'center',
				'label_block' => false,
				'separator' => 'before',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'thegem' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'thegem' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'text-align: left;',
					'center' => 'text-align: center;',
					'right' => 'text-align: right;',

				],
				'selectors' => [
					'{{WRAPPER}} .gem-alert-inner, {{WRAPPER}} .gem-alert-inner .gem-texbox-icon-image-wrapper' => '{{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'container_padding',
			[
				'label' => __( 'Padding', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'default' => [
					'top' => '55',
					'right' => '20',
					'bottom' => '55',
					'left' => '20',
					'unit' => 'px',
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-alert-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'container_top_shape',
			[
				'label' => __('Top Shape', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'none',
				'options' => [
					'none'     => __('None', 'thegem'),
					'flag'     => __('Flag', 'thegem'),
					'shield'   => __('Shield', 'thegem'),
					'ticket'   => __('Ticket', 'thegem'),
					'sentence' => __('Sentence', 'thegem'),
					'note-1'   => __('Note 1', 'thegem'),
					'note-2'   => __('Note 2', 'thegem'),
				],
			]
		);

		$control->add_responsive_control(
			'container_top_shape_color',
			[
				'label' => __( 'Top Shape Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box-top svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'container_top_shape!' => 'none',
				],
			]
		);

		$control->add_control(
			'container_bottom_shape',
			[
				'label' => __('Bottom Shape', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'none',
				'options' => [
					'none'     => __( 'None', 'thegem' ),
					'flag'     => __( 'Flag', 'thegem' ),
					'shield'   => __( 'Shield', 'thegem' ),
					'ticket'   => __( 'Ticket', 'thegem' ),
					'sentence' => __( 'Sentence', 'thegem' ),
					'note-1'   => __( 'Note 1', 'thegem' ),
					'note-2'   => __( 'Note 2', 'thegem' ),
				],
			]
		);

		$control->add_responsive_control(
			'container_bottom_shape_color',
			[
				'label' => __( 'Bottom Shape Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box-bottom svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'container_bottom_shape!' => 'none',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-alert-inner',
			]
		);

		$control->end_controls_section();

	}

    /**
	 *  Title and Description Styles
	 * @access protected
	 */
	protected function title_description_styles( $control ) {

        $control->start_controls_section(
			'block_title_description',
			[
				'label' => __( 'Title & Description Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'title_heading',
			[
				'label' => __( 'Title', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'title_bottom_spacing',
			[
				'label' => __( 'Bottom Spacing', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box .gem-alert-box-content .gem-cta-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'title_top_spacing',
			[
				'label' => __( 'Top Spacing', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box .gem-alert-box-content .gem-cta-title' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'icon_vertical_position',
							'operator' => '==',
							'value' => 'icon-top',
						],
						[
							'name' => 'icon_horizontal_position',
							'operator' => '!=',
							'value' => 'center',
						],
					],
				],
			]
		);

		$control->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box .gem-alert-box-content .gem-cta-title' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .gem-alert-box .gem-alert-box-content .gem-cta-title',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_control(
			'descr_heading',
			[
				'label' => __( 'Description', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'descr_bottom_spacing',
			[
				'label' => __( 'Bottom Spacing', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box .gem-cta-description' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'descr_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box .gem-cta-description, {{WRAPPER}} a .gem-alert-box .gem-cta-description' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .gem-alert-box .gem-cta-description p, {{WRAPPER}} a .gem-alert-box .gem-cta-description p' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .gem-alert-box .gem-cta-description span p, {{WRAPPER}} a .gem-alert-box .gem-cta-description span p' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .gem-alert-box .gem-cta-description span, {{WRAPPER}} a .gem-alert-box .gem-cta-description span' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'descr_typography',
				'selector' => '{{WRAPPER}} .gem-alert-box .gem-cta-description',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->end_controls_section();

	}


    /**
	 * Icon Image Styles
	 * @access protected
	 */
	protected function icon_image_styles( $control ) {

		$control->start_controls_section(
			'block_icon',
			[
				'label' => __( 'Icon/Image Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'icon_image_select!' => 'none',
				],
			]
		);

		$control->add_control(
			'icon_shape',
			[
				'label' => __('Shape', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'default' => __('Default', 'thegem'),
					'circle'  => __('Circle', 'thegem'),
					'square'  => __('Square', 'thegem'),
					'romb'    => __('Diamond', 'thegem'),
					'hexagon' => __('Hexagon', 'thegem'),
				],
				'default' => 'circle',
				'condition' => [
					'icon_image_select' => 'icon',
				]
			]
		);

		$control->add_control(
			'icon_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'small'  => __('Small', 'thegem'),
					'medium' => __('Medium', 'thegem'),
					'large'  => __('Large', 'thegem'),
					'xlarge' => __('Xlarge', 'thegem'),
				],
				'default' => 'medium',
				'condition' => [
					'icon_image_select' => 'icon',
				]
			]
		);

		$control->add_control(
			'icon_color_split',
			[
				'label' => __('Color Split', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					''              => __('None', 'thegem'),
					'gradient'      => __('Gradient', 'thegem'),
					'angle-45deg-r' => __('45 degree Right', 'thegem'),
					'angle-45deg-l' => __('45 degree Left', 'thegem'),
					'angle-90deg'   => __('90 degree', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'icon_image_select' => 'icon',
				]
			]
		);

		$control->add_control(
			'icon_vertical_position',
			[
				'label' => __('Vertical Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'separator' => 'before',
				'default' => 'icon-top',
				'options' => [
					'icon-top' => __('Top', 'thegem'),
					'icon-bottom' => __('Bottom', 'thegem'),
				],
			]
		);

		$control->add_control(
			'icon_horizontal_position',
			[
				'label' => __('Horizontal Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'right' => __('Right', 'thegem'),
					'center' => __('Center', 'thegem'),
					'left' => __('Left', 'thegem'),
				],
			]
		);

		$control->add_control(
			'icon_text_wrapping',
			[
				'label' => __( 'Text Wrapping', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'   => __('None', 'thegem'),
					'inline' => __('Inline, no wrap', 'thegem'),
					'wrap'   => __('Wrap Text', 'thegem'),
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'icon_vertical_position',
							'operator' => '==',
							'value' => 'icon-top',
						],
						[
							'name' => 'icon_horizontal_position',
							'operator' => '!=',
							'value' => 'center',
						],
						[
							'name' => 'icon_horizontal_position',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'icon_size_custom',
			[
				'label' => __( 'Size', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 92,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon' => 'font-size: {{SIZE}}px  !important;',
					'{{WRAPPER}} .gem-cta-icon-image-wrapper  .gem-icon-inner .padding-box-inner' => 'width: calc(1.3 * {{SIZE}}px) !important;height: calc(1.3 * {{SIZE}}px) !important;line-height: calc(1.3 * {{SIZE}}px) !important;',
					'{{WRAPPER}} .gem-alert-box .gem-image img, {{WRAPPER}} .gem-alert-box a .gem-image img' => 'width: {{SIZE}}{{UNIT}};max-width:  {{SIZE}}{{UNIT}};height: auto;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'icon_size_small',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 36,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'small',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon' => 'font-size: {{SIZE}}px;',
                ],
			]
		);

		$control->add_responsive_control(
			'icon_size_medium',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 58,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'medium',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon' => 'font-size: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'icon_size_large',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 114,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'large',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon' => 'font-size: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'icon_size_xlarge',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 164,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'xlarge',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon' => 'font-size: {{SIZE}}px;',
				],	
			]
		);

		$control->add_responsive_control(
			'icon_box_padding',
			[
				'label' => __('Box Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 25,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon' => 'padding: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'icon_box_padding_small',
			[
				'label' => __('Box Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'small',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper  .gem-icon-shape-hexagon-top' => 'margin-top: calc({{SIZE}}px * 2); margin-left: calc({{SIZE}}px * 2);
					left: calc(0px - {{SIZE}}px);top: calc(0px - {{SIZE}}px);width: calc(59px - {{SIZE}}px * 2);height: calc(49px - {{SIZE}}px * 2);',
				],
			]
		);

		$control->add_responsive_control(
			'icon_box_padding_medium',
			[
				'label' => __('Box Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 30,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'medium',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 3,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper  .gem-icon-shape-hexagon-top' => 'margin-top: calc({{SIZE}}px * 2); margin-left: calc({{SIZE}}px * 2);
					left: calc(0px - {{SIZE}}px);top: calc(0px - {{SIZE}}px);width: calc(91px - {{SIZE}}px * 2);height: calc(77px - {{SIZE}}px * 2);',
				],
			]
		);

		$control->add_responsive_control(
			'icon_box_padding_large',
			[
				'label' => __('Box Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'large',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon-shape-hexagon-top' => 'margin-top: calc({{SIZE}}px * 2); margin-left: calc({{SIZE}}px * 2);
					left: calc(0px - {{SIZE}}px);top: calc(0px - {{SIZE}}px);width: calc(181px - {{SIZE}}px * 2);height: calc(153px - {{SIZE}}px * 2);',
				],
			]
		);

		$control->add_responsive_control(
			'icon_box_padding_xlarge',
			[
				'label' => __('Box Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'xlarge',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
												[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon-shape-hexagon-top' => 'margin-top: calc({{SIZE}}px * 2); margin-left: calc({{SIZE}}px * 2);
					left: calc(0px - {{SIZE}}px);top: calc(0px - {{SIZE}}px);width: calc(267px - {{SIZE}}px * 2);height: calc(227px - {{SIZE}}px * 2);',
				],
			]
		);

		$control->add_responsive_control(
			'image_padding',
			[
				'label' => __( 'Padding', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-image img, {{WRAPPER}} .gem-textbox-content a .gem-image img' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'icon_image_select' => 'image',
				],
			]
		);

		$control->add_control(
			'icon_border_type',
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
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon' => 'border-style: {{VALUE}};',
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-image span' => 'border-style: {{VALUE}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
			]
		);

		$control->add_control(
			'icon_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-image span' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'icon_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-image span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
			]
		);
		$control->add_responsive_control(
			'icon_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '40',
					'left' => '0',
					'unit' => 'px',
					'isLinked' => false,
				],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-image span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

				],
			]
		);

		$control->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'icon_shadow',
				'label' => __( 'Icon Shadow', 'thegem' ),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'fields_options' => [
					'text_shadow' => [
						'selectors' => [
							'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon .back-angle i' => 'text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
							'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon svg' => 'filter: drop-shadow({{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}});',
						],
					]
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'img_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-image span',
				'condition' => [
					'icon_image_select' => 'image',
				]
			]
		);

		$control->start_controls_tabs( 'icon_tabs' );
		$control->start_controls_tab( 'icon_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'default' => '#FFFFFF',
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon',
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_background_hexagon',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon-shape-hexagon-top-inner-before',
			]
		);

		$control->add_responsive_control(
			'icon_border_color',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon .gem-icon-shape-hexagon-back-inner-before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-image span' => 'border-color: {{VALUE}};',

				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
					],
				],
			]
		);


		$control->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#b6c6c9',
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '=',
							'value' => '',
						],
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => 'gradient',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-1 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-alert-box .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-1 svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .gem-alert-box .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-2 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-alert-box .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-2 svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'color_gradient',
				'types' => ['gradient'],
				'default' => 'gradient',
				'fields_options' => [
					'background' => [
						'label' => _x('Icon Color', 'Background Control', 'thegem'),
					],
					'gradient_angle' => [
						'selectors' => [
							'{{SELECTOR}}' => '-webkit-background-clip: text; -webkit-text-fill-color: transparent; background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
						],
					],
					'color' => [
						'default' => '#91a0ac',
					],
					'color_b' => [
						'default' => '#65707e',
					]
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '=',
							'value' => 'gradient',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon .back-angle i',
			]
		);

		$control->add_control(
			'icon_color_1',
			[
				'label' => __( 'Icon Color 1', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => 'gradient',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-1 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-alert-box .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-1 svg' => 'fill: {{VALUE}};',
				],
			]
		);


		$control->add_control(
			'icon_color_2',
			[
				'label' => __( 'Icon Color 2', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => 'gradient',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'default' => '#65707e',
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-2 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-alert-box .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-2 svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'icon_rotate',
			[
				'label' => __( 'Rotate Icon', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'condition' => [
					'icon_image_select' => 'icon',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon .back-angle i' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$control->add_responsive_control(
			'icon_box_rotate',
			[
				'label' => __( 'Rotate Box', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',

				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'icon_box_shadow',
				'label' => __('Box Shadow', 'thegem'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-icon',
			]
		);

		$control->add_control(
			'img_opacity',
			[
				'label' => __( 'Opacity', 'thegem' ),
				'type' => Controls_Manager::NUMBER,
				'step' => '0.1',
				'min' => '0',
				'max' => '1',
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-image img' => 'opacity: {{VALUE}} !important;',
				],
				'condition' => [
					'icon_image_select' => 'image',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'img_css_filters',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-image img',
				'condition' => [
					'icon_image_select' => 'image',
				]
			]
		);

		$control->add_control(
			'img_blend_mode',
			[
				'label' => __( 'Blend Mode', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Normal', 'thegem' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'color-burn' => 'Color Burn',
					'hue' => 'Hue',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'exclusion' => 'Exclusion',
					'luminosity' => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-cta-icon-image-wrapper .gem-image img' => 'mix-blend-mode: {{VALUE}}',
				],
				'condition' => [
					'icon_image_select' => 'image',
				]
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'icon_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

        $control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_background_hover',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .icon-hover-bg',
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_background_hexagon_hover',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-icon-shape-hexagon-top-inner-before',
			]
		);

		$control->add_responsive_control(
			'icon_border_color_hover',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box:hover .gem-icon' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-image span' => 'border-color: {{VALUE}};',

				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
					],
				],
			]
		);


		$control->add_control(
			'icon_color_hover',
			[
				'label' => __( 'Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '=',
							'value' => '',
						],
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => 'gradient',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-1 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-1 svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-2 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-2 svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'color_gradient_hover',
				'types' => ['gradient'],
				'default' => 'gradient',
				'fields_options' => [
					'background' => [
						'label' => _x('Icon Color', 'Background Control', 'thegem'),
					],
					'gradient_angle' => [
						'selectors' => [
							'{{SELECTOR}}' => '-webkit-background-clip: text; -webkit-text-fill-color: transparent; background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
						],
					]
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '=',
							'value' => 'gradient',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-icon .back-angle i',
			]
		);

		$control->add_control(
			'icon_color_1_hover',
			[
				'label' => __( 'Icon Color 1', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => 'gradient',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'default' => '#65707e',
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-1 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-1 svg' => 'fill: {{VALUE}};',
				],
			]
		);


		$control->add_control(
			'icon_color_2_hover',
			[
				'label' => __( 'Icon Color 2', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => 'gradient',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'default' => '#65707e',
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-2 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-icon .gem-icon-half-2 svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'icon_rotate_hover',
			[
				'label' => __( 'Rotate Icon', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'condition' => [
					'icon_image_select' => 'icon',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box:hover .gem-icon .back-angle i' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$control->add_responsive_control(
			'icon_box_rotate_hover',
			[
				'label' => __( 'Rotate Box', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-icon' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',

				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'img_overlay_color_hover',
				'label' => __( 'Overlay Color Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'default' => 'classic',
				'fields_options' => [
					'background' => [
						'label' => _x('Overlay Color Type', 'Background Control', 'thegem'),
					],
					'color' => [
						'default' => thegem_get_option('styled_elements_background_color'),
					],
				],
				'selector' => '{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-image span::before',
				'condition' => [
					'icon_image_select' => 'image',
				]
			]
		);

		$control->remove_control('img_overlay_color_hover_image');

		$control->add_control(
			'img_opacity_opacity_hover',
			[
				'label' => __( 'Overlay Opacity', 'thegem' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '0.5',
				'step' => '0.1',
				'min' => '0',
				'max' => '1',
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-image > span::before' => 'opacity: {{VALUE}} !important;',
				],
				'condition' => [
					'icon_image_select' => 'image',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'img_css_filters_hover',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-image img',
				'condition' => [
					'icon_image_select' => 'image',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'icon_box_shadow_hover',
				'label' => __('Box Shadow', 'thegem'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_image_select',
							'operator' => '=',
							'value' => 'icon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-alert-box:hover .gem-cta-icon-image-wrapper .gem-icon',
			]
		);

		$control->add_control(
			'icon_hover_effect',
			[
				'label' => __('Hover Effect', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'fade'     => __('Fade', 'thegem'),
					'fill-out' => __('Fill Out', 'thegem'),
				],
				'default' => 'fade',
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'icon_background_hover_background',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name'  => 'icon_background_hexagon_hover_background',
							'operator' => '!=',
							'value' => '',
						],
					]
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * First Button Styles
	 * @access protected
	 */
	protected function button_1_styles( $control ) {

		$control->start_controls_section(
			'block_button_1',
			[
				'label' => __( 'Button Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_button_1' => 'yes',
				],
			]
		);

		$control->add_control(
			'button_1_heading',
			[
				'label' => __( 'Button', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'button_position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'button-bottom',
				'options' => [
					'button-bottom'       => __('Bottom', 'thegem'),
					'button-right-inline' => __('Right inline', 'thegem'),
					'button-left-inline'  => __('Left inline', 'thegem'),
				],
			]
		);

		$control->add_control(
			'button_1_type',
			[
				'label' => __('Button Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'flat',
				'options' => [
					'flat' => __('Flat', 'thegem'),
					'outline' => __('Outline', 'thegem'),
				],
			]
		);

		$control->add_control(
			'button_1_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'tiny',
				'options' => [
					'tiny' => __('Tiny', 'thegem' ),
					'small' => __('Small', 'thegem' ),
					'medium' => __('Medium', 'thegem'),
					'large' => __('Large', 'thegem' ),
					'giant' => __('Giant', 'thegem' ),
				],
			]
		);

		$control->add_responsive_control(
			'button_horizontal_spacing',
			[
				'label' => __( 'Spacing', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 35,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-2' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_button_2' => 'yes',
					'button_position' => 'button-bottom',
				],
			]
		);

		$control->add_responsive_control(
			'button_vertical_spacing',
			[
				'label' => __( 'Spacing', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-2' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_button_2' => 'yes',
					'button_position!' => 'button-bottom',
				],
			]
		);

		$control->add_responsive_control(
			'button_1_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-1 .gem-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'button_1_border_type',
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
					'{{WRAPPER}} .gem-button-container-1 .gem-button' => 'border-style: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'button_1_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-1 .gem-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'button_1_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box-buttons .gem-button-container-1 .gem-inner-wrapper-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'button_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
					'isLinked' => true,
				],
				'mobile_default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
					'isLinked' => false,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box .gem-alert-box-buttons .gem-button-container-1' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$control->start_controls_tabs( 'button_1_tabs' );
		$control->start_controls_tab( 'button_1_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_control(
			'button_1_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-1 .gem-button .gem-text-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-button-container-1 .gem-button .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-button-container-1 .gem-button .gem-button-icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'button_1_typography',
				'selector' => '{{WRAPPER}} .gem-button-container-1 .gem-button',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'button_1_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#63cc8b',
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-1 .gem-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'button_1_border_color',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-1 .gem-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_1_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-button-container-1 .gem-button',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'button_1_tab_hover', [ 'label' => __( 'Hover', 'thegem' ),] );

		$control->add_control(
			'button_1_text_color_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box .gem-button-container-1 .gem-button:hover .gem-text-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-alert-box .gem-button-container-1 .gem-button:hover .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-alert-box .gem-button-container-1 .gem-button:hover .gem-button-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'button_1_typography_hover',
				'selector' => '{{WRAPPER}} .gem-alert-box .gem-button-container-1:hover .gem-button span',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'button_1_bg_color_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#363547',
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box .gem-button-container-1:hover .gem-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'button_1_border_color_hover',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box .gem-button-container-1:hover .gem-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_1_shadow_hover',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-alert-box .gem-button-container-1:hover .gem-button',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_responsive_control(
			'button_1_icon_align',
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
					'left' => 'flex-direction: row;',
					'right' => 'flex-direction: row-reverse;',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-1 .gem-inner-wrapper-btn' => '{{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'button_1_icon_spacing_right',
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
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-1 .gem-inner-wrapper-btn .gem-button-icon' => 'margin-left:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'button_1_icon_align' => ['right'],
				],
			]
		);

		$control->add_responsive_control(
			'button_1_icon_spacing_left',
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
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-1 .gem-inner-wrapper-btn .gem-button-icon' => 'margin-right:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'button_1_icon_align' => ['left'],
				],
			]
		);

		$control->end_controls_section();

	}

	/**
	 * Second Button Styles
	 * @access protected
	 */
	protected function button_2_styles( $control ) {
		$control->start_controls_section(
			'block_button_2',
			[
				'label' => __( 'Second Button Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_button_2' => 'yes',
				],
			]
		);

		$control->add_control(
			'button_2_heading',
			[
				'label' => __( 'Second Button', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'button_2_type',
			[
				'label' => __('Button Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'flat',
				'options' => [
					'flat' => __('Flat', 'thegem'),
					'outline' => __('Outline', 'thegem'),
				],
			]
		);

		$control->add_control(
			'button_2_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'tiny',
				'options' => [
					'tiny' => __('Tiny', 'thegem' ),
					'small' => __('Small', 'thegem' ),
					'medium' => __('Medium', 'thegem'),
					'large' => __('Large', 'thegem' ),
					'giant' => __('Giant', 'thegem' ),
				],
			]
		);

		$control->add_responsive_control(
			'button_2_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-2 .gem-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'button_2_border_type',
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
					'{{WRAPPER}} .gem-button-container-2 .gem-button' => 'border-style: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'button_2_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-2 .gem-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'button_2_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box-buttons .gem-button-container-2 .gem-inner-wrapper-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs( 'button_2_tabs' );
		$control->start_controls_tab( 'button_2_tab_normal', [ 'label' => __( 'Normal', 'thegem' ),] );

		$control->add_control(
			'button_2_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-2 .gem-button .gem-text-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-button-container-2 .gem-button .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-button-container-2 .gem-button .gem-button-icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'button_2_typography',
				'selector' => '{{WRAPPER}} .gem-button-container-2 .gem-button',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'button_2_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#ff6f91',
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-2 .gem-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'button_2_border_color',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-2 .gem-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_2_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-button-container-2 .gem-button',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'button_2_tab_hover', [ 'label' => __( 'Hover', 'thegem' ),] );

		$control->add_control(
			'button_2_text_color_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box .gem-button-container-2 .gem-button:hover .gem-text-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-alert-box .gem-button-container-2 .gem-button:hover .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-alert-box .gem-button-container-2 .gem-button:hover .gem-button-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'button_2_typography_hover',
				'selector' => '{{WRAPPER}} .gem-alert-box .gem-button-container-2:hover .gem-button span',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'button_2_bg_color_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#363547',
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box .gem-button-container-2:hover .gem-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'button_2_border_color_hover',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-alert-box .gem-button-container-2:hover .gem-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_2_shadow_hover',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-alert-box .gem-button-container-2:hover .gem-button',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_responsive_control(
			'button_2_icon_align',
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
					'left' => 'flex-direction: row;',
					'right' => 'flex-direction: row-reverse;',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-2 .gem-inner-wrapper-btn' => '{{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'button_2_icon_spacing_right',
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
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-2 .gem-inner-wrapper-btn .gem-button-icon' => 'margin-left:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'button_2_icon_align' => ['right'],
				],
			]
		);

		$control->add_responsive_control(
			'button_2_icon_spacing_left',
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
				'selectors' => [
					'{{WRAPPER}} .gem-button-container-2 .gem-inner-wrapper-btn .gem-button-icon' => 'margin-right:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'button_2_icon_align' => ['left'],
				],
			]
		);

		$control->end_controls_section();

	}


	/**
	 * Helper check in array value
	 * @access protected
	 * @return string
	 */
	function is_in_array_value( $array = array(), $value = '', $default = '' ) {
		if ( in_array( $value, $array ) ) {
			return $value;
		}
		return $default;
	}

	protected function get_setting_preset( $val ) {
		if( empty( $val ) ) {
			return '';
		}

		return $val;
	}

	protected function get_presets_arg( $val ) {
		if ( empty( $val ) ) {
			return null;
		}

		return json_decode( $val, true );
	}

	/**
	 * Retrieve image widget link URL for Icon Image
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array $settings
	 *
	 * @return array|string|false An array/string containing the link URL, or false if no link.
	 */
	private function get_icon_image_link_url( $settings ) {
		if ( 'none' === $settings['icon_image_link_to'] ) {
			return false;
		}

		if ( 'custom' === $settings['icon_image_link_to'] ) {
			if ( empty( $settings['icon_image_link']['url'] ) ) {
				return false;
			}

			return $settings['icon_image_link'];
		}

		return [
			'url' => $settings['graphic_image']['url'],
		];
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

		$preset_path = __DIR__ . '/templates/preset_html1.php';

		if ( !empty( $preset_path) && file_exists( $preset_path) ){
			include( $preset_path );
		}

	}

	public function get_preset_data() {

		return array(
			/** Solid Box */
			'solid-box' => array(
				'button_1_bg_color' => '#63cc8b',
				'button_1_bg_color_hover' => '#363547',
				'button_1_icon' => ['value' => '', 'library' => ''],
				'button_1_size' => 'tiny',
				'button_1_text_color' => '#FFFFFF',
				'button_2_bg_color' => '#ff6f91',
				'button_2_bg_color_hover' => '#363547',
				'button_2_icon' => ['value' => '', 'library' => ''],
				'button_2_size' => 'tiny',
				'button_2_text_color' => '#FFFFFF',
				'button_horizontal_spacing' => ['size' =>35,'unit' => 'px'],
				'button_margin' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'container_padding' => ['top' => '55', 'right' => '20', 'bottom' => '55', 'left' => '20', 'unit' => 'px', 'isLinked' => false],
				'descr_bottom_spacing' => ['size' => 40,'unit' => 'px'],
				'icon_background_background' => 'classic',
				'icon_background_color' => '#FFFFFF',
				'icon_border_color' => '#FFFFFF',
				'icon_box_padding' => ['size' => 25,'unit' => 'px'],
				'icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '40', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'icon_shape' => 'circle',
				'icon_size_custom' => ['size' => 92,'unit' => 'px'],
				'selected_icon' => ['value' => 'fas fa-exclamation-circle', 'library' => 'fa-solid'],
				'show_button_1' => 'yes',
				'show_button_2' => 'yes',
				'title_html_tag' => 'h5',
			),
			/** Outlined Bold */
			'outlined-bold' => array(
				'button_1_bg_color' => '#FFFFFF',
				'button_1_border_color' => '#99a9b5',
				'button_1_border_color_hover' => '#b6c6c9',
				'button_1_bg_color_hover' => '#b6c6c9',
				'button_1_icon' => ['value' => '', 'library' => ''],
				'button_1_size' => 'tiny',
				'button_1_text_color' => '#99a9b5',
				'button_1_text_color_hover' => '#FFFFFF',
				'button_1_type' => 'outline',
				'button_margin' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'container_bg_color_background' => 'classic',
				'container_bg_color_color' => '',
				'container_border_border' => 'solid',
				'container_border_color' => '#b6c6c9',
				'container_border_width' => ['top' => '3', 'right' => '3', 'bottom' => '3', 'left' => '3', 'unit' => 'px', 'isLinked' => true],
				'container_padding' => ['top' => '93', 'right' => '20', 'bottom' => '53', 'left' => '20', 'unit' => 'px', 'isLinked' => false],
				'descr_bottom_spacing' => ['size' => 40,'unit' => 'px'],
				'icon_background_background' => 'classic',
				'icon_background_color' => '',
				'icon_border_color' => '#00bcd4',
				'icon_box_padding' => ['size' => '','unit' => 'px'],
				'icon_color' => '#00bcd4',
				'icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '84', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'icon_shape' => 'circle',
				'icon_size_custom' => ['size' => 65,'unit' => 'px'],
				'selected_icon' => ['value' => 'gem-mdi mdi-lightbulb-outline', 'library' => 'thegem-mdi'],
				'show_button_1' => 'yes',
				'show_button_2' => 'no',
				'title_html_tag_weight' => 'bold',
				'title_html_tag' => 'h5',
			),
			/** Outlined Thin */
			'outlined-thin' => array(
				'button_1_bg_color' => '#46485c',
				'button_1_bg_color_hover' => '#00bcd4',
				'button_1_border_radius' => ['top' => '15', 'right' => '15', 'bottom' => '15', 'left' => '15', 'unit' => 'px', 'isLinked' => true],
				'button_1_icon' => ['value' => '', 'library' => ''],
				'button_1_size' => 'tiny',
				'button_1_text_color' => '#FFFFFF',
				'button_2_bg_color' => '#7e91ff',
				'button_2_bg_color_hover' => '#363547',
				'button_2_border_radius' => ['top' => '15', 'right' => '15', 'bottom' => '15', 'left' => '15', 'unit' => 'px', 'isLinked' => true],
				'button_2_icon' => ['value' => '', 'library' => ''],
				'button_2_size' => 'tiny',
				'button_2_text_color' => '#FFFFFF',
				'button_margin' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'container_bg_color_background' => 'classic',
				'container_bg_color_color' => '',
				'container_padding' => ['top' => '75', 'right' => '20', 'bottom' => '55', 'left' => '20', 'unit' => 'px', 'isLinked' => false],
				'descr_bottom_spacing' => ['size' => 40,'unit' => 'px'],
				'icon_background_background' => 'classic',
				'icon_background_color' => '',
				'icon_box_padding' => ['size' => '','unit' => 'px'],
				'icon_color_1' => '#7ebeff',
				'icon_color_2' => '#7e91ff',
				'icon_color_split' => 'angle-90deg',
				'icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '65', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'icon_size_custom' => ['size' => 96,'unit' => 'px'],
				'selected_icon' => ['value' => 'gem-mdi mdi-memory', 'library' => 'thegem-mdi'],
				'show_button_1' => 'yes',
				'show_button_2' => 'yes',
				'title_html_tag_weight' => 'bold',
				'title_html_tag' => 'h5',
			),
			/** Left Aligned */
			'left-aligned' => array(
				'button_1_bg_color' => '#00bcd4',
				'button_1_bg_color_hover' => '#363547',
				'button_1_icon' => ['value' => '', 'library' => ''],
				'button_1_size' => 'medium',
				'button_1_text_color' => '#FFFFFF',
				'button_margin' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '45', 'unit' => 'px', 'isLinked' => false],
				'button_margin_mobile' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'button_position' => 'button-right-inline',
				'container_align' => 'left',
				'container_bg_color_background' => 'classic',
				'container_bg_color_color' => '#f0f3f2',
				'container_padding' => ['top' => '25', 'right' => '50', 'bottom' => '25', 'left' => '50', 'unit' => 'px', 'isLinked' => false],
				'icon_background_background' => 'classic',
				'icon_background_color' => '#FFFFFF',
				'icon_border_color' => '#FFFFFF',
				'icon_box_padding' => ['size' => 25,'unit' => 'px'],
				'icon_color' => '#99a9b5',
				'icon_horizontal_position' => 'left',
				'icon_margin' => ['top' => '0', 'right' => '30', 'bottom' => '10', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'icon_shape' => 'circle',
				'icon_size_custom' => ['size' => 80,'unit' => 'px'],
				'icon_text_wrapping' => 'inline',
				'selected_icon' => ['value' => 'gem-mdi mdi-engine', 'library' => 'thegem-mdi'],
				'show_button_1' => 'yes',
				'show_button_2' => 'no',
				'title_bottom_spacing' => ['size' => 18,'unit' => 'px'],
				'title_html_tag_weight' => 'light',
				'title_html_tag' => 'h2',			),
		);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new TheGem_CTA() );
