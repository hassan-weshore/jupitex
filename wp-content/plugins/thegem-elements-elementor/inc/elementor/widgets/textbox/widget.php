<?php
namespace TheGem_Elementor\Widgets\StyledTextBox;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Plugin;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Schemes;


if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Elementor widget for StyledTextBox.
 */
class TheGem_StyledTextBox extends Widget_Base {

	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		if( ! defined( 'THEGEM_ELEMENTOR_WIDGET_STYLED_TEXTBOX_DIR' ) ){
			define( 'THEGEM_ELEMENTOR_WIDGET_STYLED_TEXTBOX_DIR', rtrim( __DIR__, ' /\\' ) );
		}

		if ( ! defined( 'THEGEM_ELEMENTOR_WIDGET_STYLED_TEXTBOX_URL' ) ) {
			define( 'THEGEM_ELEMENTOR_WIDGET_STYLED_TEXTBOX_URL', rtrim( plugin_dir_url( __FILE__ ), ' /\\' ) );
		}
		wp_register_style( 'thegem-styled-textbox', THEGEM_ELEMENTOR_WIDGET_STYLED_TEXTBOX_URL . '/assets/css/thegem-styled-textbox.css', array(), null );
	}


	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-styled-textbox';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'TextBox', 'thegem' );
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
				'thegem-styled-textbox',
				'thegem-button'];
		}
		return [ 'thegem-styled-textbox' ];
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
			'basic'			   => __( 'Basic Centered', 'thegem' ),
			'basic-left-aligned'  => __( 'Basic Left Align', 'thegem' ),
			'basic-right-aligned' => __( 'Basic Right Align', 'thegem' ),
			'rounded'			 => __( 'Rounded', 'thegem' ),
			'box'				 => __( 'Box', 'thegem' ),
			'capsule'			 => __( 'Capsule', 'thegem' ),
			'tag'				 => __( 'Tag', 'thegem' ),
			'flag'				=> __( 'Flag', 'thegem' ),
			'shield'			  => __( 'Shield', 'thegem' ),
			'ticket'			  => __( 'Ticket', 'thegem' ),
			'note'				=> __( 'Note', 'thegem' ),
			'profile'			 => __( 'Profile', 'thegem' ),
			'sentence'			=> __( 'Sentence', 'thegem' ),
			'minimal'			 => __( 'Minimal', 'thegem' ),
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
		return 'basic';
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
				'type'	=> Controls_Manager::SELECT,
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
			'content_textbox_title',
			[
				'label' => __( 'Title Text', 'thegem' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Lorem Impsum Dolor', 'thegem' ),
				'placeholder' => __( 'Type your title here', 'thegem' ),
				'label_block' => 'true',
				'condition' => [ 
					'source' => 'editor'
				],
			]
		);

		$this->add_control(
			'content_textbox_title_html_tag',
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
				'default' => 'h4',
				'condition' => [ 
					'source' => 'editor'
				],
			]
		);

		$this->add_control(
			'content_textbox_title_html_tag_weight',
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
			'content_textbox_title_html_tag_h_disable',
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
			'content_textbox_text',
			[
				'label' => __( 'Content Text', 'thegem' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'thegem' ),
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
			'content_textbox_icon_image_select',
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
			'content_textbox_graphic_image',
			[
				'label' => __( 'Choose Image', 'thegem' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				],
				'show_label' => false,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'content_textbox_graphic_image', 
				'default' => 'thumbnail',
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
					'content_textbox_graphic_image[id]!' => '',
				],
			]
		);

		$this->add_control(
			'content_textbox_selected_icon',
			[
				'label' => __( 'Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'far fa-bell',
					'library' => 'fa-regular',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				],
			]
		);

		$this->add_control(
			'content_textbox_icon_image_link_to',
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
			'content_textbox_icon_image_link',
			[
				'label' => __( 'Link', 'thegem' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'thegem' ),
				'condition' => [
					'content_textbox_icon_image_link_to' => 'custom',

				],
				'show_label' => false,
			]
		);

		$this->add_control(
			'content_textbox_icon_image_open_lightbox',
			[
				'label' => __( 'Lightbox', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'yes',
				'options' => [
					'yes' => __( 'Yes', 'thegem' ),
					'no' => __( 'No', 'thegem' ),
				],
				'condition' => [
					'content_textbox_icon_image_link_to' => 'file',

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
			'content_textbox_show_button',
			[
				'label' => __( 'Show Button', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'content_textbox_button_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Learn More', 'thegem'),
				'condition' => [ 
					'content_textbox_show_button' => 'yes'
				],
			]
		);

		$this->add_control(
			'content_textbox_button_text_weight',
			[
				'label' => __('Text Weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'bold',
				'options' => [
					'bold' => __('Bold', 'thegem'),
					'thin' => __('Thin', 'thegem'),
				],
				'condition' => [ 
					'content_textbox_show_button' => 'yes'
				],
			]
		);

		$this->add_control(
			'content_textbox_button_link',
			[
				'label' => __('Button Link', 'thegem'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('#', 'thegem'),
				'condition' => [ 
					'content_textbox_show_button' => 'yes'
				],
			]
		);

		$this->add_control(
			'content_textbox_button_icon',
			[
				'label' => __( 'Button Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'far fa-star',
					'library' => 'solid',
				],
				'condition' => [ 
					'content_textbox_show_button' => 'yes'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'heading_block_textbox_link',
			[
				'label' => __( 'Textbox Link', 'thegem' ),
			]
		);

		$this->add_control(
			'content_textbox_link',
			[
				'label' => __( 'Link', 'thegem' ),
				'type' => Controls_Manager::URL,
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
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

		/*Textbox Container Styles*/
		$this->textbox_container_styles( $control );

		/* TextBox Title and Description Styles */
		$this->textbox_title_description_styles( $control );

		/* TextBox Icon Image Styles */
		$this->textbox_icon_image_styles( $control );

		/* TextBox Button Styles */
		$this->textbox_button_styles( $control );

	}

	/**
	 * TextBox Container Styles
	 * @access protected
	 */
	protected function textbox_container_styles( $control ) {


		$control->start_controls_section(
			'textbox_container',
			[
				'label' => __( 'Container Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->start_controls_tabs( 'textbox_container_tabs' );
		$control->start_controls_tab( 'textbox_container_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'textbox_container_bg_color',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .gem-textbox-content',
			]
		);

		$control->add_responsive_control(
			'textbox_container_radius',
			[
				'label' => __( 'Radius', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'textbox_container_border',
				'label' => __( 'Border', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-textbox-content',
			]
		);

		$control->add_responsive_control(
			'textbox_container_align',
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
					'{{WRAPPER}} .gem-textbox-content, {{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper' => '{{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_container_padding',
			[
				'label' => __( 'Padding', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'default' => [
					'top' => '45',
					'right' => '30',
					'bottom' => '50',
					'left' => '30',
					'unit' => 'px',
					'isLinked' => false,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'textbox_container_top_shape',
			[
				'label' => __('Top Shape', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'none',
				'options' => [
					'none'	 => __('None', 'thegem'),
					'flag'	 => __('Flag', 'thegem'),
					'shield'   => __('Shield', 'thegem'),
					'ticket'   => __('Ticket', 'thegem'),
					'sentence' => __('Sentence', 'thegem'),
					'note-1'   => __('Note 1', 'thegem'),
					'note-2'   => __('Note 2', 'thegem'),
				],
			]
		);

		$control->add_responsive_control(
			'textbox_container_top_shape_color',
			[
				'label' => __( 'Top Shape Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-top svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'textbox_container_top_shape!' => 'none',
				],
			]
		);

		$control->add_control(
			'textbox_container_bottom_shape',
			[
				'label' => __('Bottom Shape', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'none',
				'options' => [
					'none'	 => __( 'None', 'thegem' ),
					'flag'	 => __( 'Flag', 'thegem' ),
					'shield'   => __( 'Shield', 'thegem' ),
					'ticket'   => __( 'Ticket', 'thegem' ),
					'sentence' => __( 'Sentence', 'thegem' ),
					'note-1'   => __( 'Note 1', 'thegem' ),
					'note-2'   => __( 'Note 2', 'thegem' ),
				],
			]
		);

		$control->add_responsive_control(
			'textbox_container_bottom_shape_color',
			[
				'label' => __( 'Bottom Shape Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-bottom svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'textbox_container_bottom_shape!' => 'none',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'textbox_container_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-textbox-content',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'textbox_container_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'textbox_container_bg_color_hover',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .gem-textbox:hover .gem-textbox-content',
			]
		);

		$control->add_responsive_control(
			'textbox_container_border_color_hover',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-content' => 'border-color: {{VALUE}};',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'textbox_container_shadow_hover',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-textbox:hover .gem-textbox-content',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();

	}


	/**
	 * TextBox Title and Description Styles
	 * @access protected
	 */
	protected function textbox_title_description_styles( $control ) {

		$control->start_controls_section(
			'textbox_block_title_description',
			[
				'label' => __( 'Title & Description Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'textbox_content_title_heading',
			[
				'label' => __( 'Title', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		$control->start_controls_tabs( 'textbox_content_title_tabs' );
		$control->start_controls_tab( 'textbox_content_title_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_responsive_control(
			'textbox_content_title_bottom_spacing',
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
					'{{WRAPPER}} .gem-textbox .gem-textbox-content .gem-textbox-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_title_top_spacing',
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
					'{{WRAPPER}} .gem-textbox .gem-textbox-content .gem-textbox-title' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'textbox_content_icon_image_vertical_position',
							'operator' => '==',
							'value' => 'icon-top',
						],
						[
							'name' => 'textbox_content_icon_image_horizontal_position',
							'operator' => '!=',
							'value' => 'center',
						],
					],
				],
			]
		);

		$control->add_control(
			'textbox_content_title_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox .gem-textbox-content .gem-textbox-title' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( 
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'textbox_content_title_typography',
				'selector' => '{{WRAPPER}} .gem-textbox .gem-textbox-content .gem-textbox-title',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);	
			
		$control->end_controls_tab();

		$control->start_controls_tab( 'textbox_content_title_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_control(
			'textbox_content_title_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-textbox-title' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( 
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'textbox_content_title_typography_hover',
				'selector' => '{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-textbox-title',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);	

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'textbox_content_descr_heading',
			[
				'label' => __( 'Description', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		$control->start_controls_tabs( 'textbox_content_descr_tabs' );
		$control->start_controls_tab( 'textbox_content_descr_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_responsive_control(
			'textbox_content_descr_bottom_spacing',
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
				'default'	=> [
					'unit' => 'px',
					'size' => 25,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-textbox .gem-textbox-description' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'textbox_content_descr_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox .gem-textbox-description p, {{WRAPPER}} a .gem-textbox .gem-textbox-description p' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .gem-textbox .gem-textbox-description, {{WRAPPER}} a .gem-textbox .gem-textbox-description' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .gem-textbox .gem-textbox-description span p, {{WRAPPER}} a .gem-textbox .gem-textbox-description span p' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .gem-textbox .gem-textbox-description span, {{WRAPPER}} a .gem-textbox .gem-textbox-description span' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'textbox_content_descr_typography',
				'selector' => '{{WRAPPER}} .gem-textbox .gem-textbox-description',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);	

		$control->end_controls_tab();

		$control->start_controls_tab( 'textbox_content_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_control(
			'textbox_content_descr_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-description' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-description p' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-description span p' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-description span' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
		[
			'label' => __( 'Typography', 'thegem' ),
			'name' => 'textbox_content_descr_typography_hover',
			'selector' => '{{WRAPPER}} .gem-textbox:hover .gem-textbox-description',
			'scheme' => Schemes\Typography::TYPOGRAPHY_1,
		]
	);

		$control->end_controls_tab();
		$control->end_controls_tabs();	

		$control->end_controls_section();
	}

	/**
	 * TextBox Icon Image Styles
	 * @access protected
	 */
	protected function textbox_icon_image_styles( $control ) {

		$control->start_controls_section(
			'textbox_block_icon_image',
			[
				'label' => __( 'Icon / Image Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'content_textbox_icon_image_select!' => 'none',
				],
			]
		);

		$control->add_control(
			'textbox_content_icon_image_heading',
			[
				'label' => __( 'Icon / Image', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);		

		$control->add_control(
			'textbox_content_icon_image_vertical_position',
			[
				'label' => __('Vertical Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'icon-top',
				'options' => [
					'icon-top' => __('Top', 'thegem'),
					'icon-bottom' => __('Bottom', 'thegem'),
				],
			]
		);

		$control->add_control(
			'textbox_content_icon_image_horizontal_position',
			[
				'label' => __('Horizontal Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				// 'default' => 'center',
				'options' => [
					'right' => __('Right', 'thegem'),
					'center' => __('Center', 'thegem'),
					'left' => __('Left', 'thegem'),
				],
			]
		);

		$this->add_control(
			'textbox_content_icon_image_text_wrapping',
			[
				'label' => __( 'Text Wrapping', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => __('None', 'thegem'),
					'inline' => __('Inline, no wrap', 'thegem'),
					'wrap' => __('Wrap Text', 'thegem'),
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'textbox_content_icon_image_horizontal_position',
							'operator' => '!=',
							'value' => 'center',
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_size',
			[
				'label' => __( 'Size', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon i' => 'font-size: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon svg' => 'width: {{SIZE}}{{UNIT}} !important;height: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .gem-textbox-content .gem-image img, {{WRAPPER}} .gem-textbox-content a .gem-image img' => 'width: {{SIZE}}{{UNIT}};max-width: {{SIZE}}{{UNIT}};height: auto;',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_padding',
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
					'{{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gem-textbox-content .gem-image span, {{WRAPPER}} .gem-textbox-content a .gem-image span' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .gem-textbox-content a .gem-texbox-icon-image-wrapper .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .gem-textbox-content .gem-image span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .gem-textbox-content .gem-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'textbox_content_icon_border_type',
			[
				'label' => __('Border Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'none',
				'options' => [
					'none' => __('None', 'thegem'),
					'solid' => __('Solid', 'thegem'),
					'double' => __('Double', 'thegem'),
					'dotted' => __('Dotted', 'thegem'),
					'dashed' => __('Dashed', 'thegem'),
				],
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'border-style: {{VALUE}};',
					'{{WRAPPER}} .gem-textbox-content .gem-image span' => 'border-style: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'textbox_content_icon_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .gem-textbox-content .gem-image span' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '30',
					'left' => '0',
					'unit' => 'px',
					'isLinked' => false,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .gem-textbox-content .gem-image span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'textbox_content_icon_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon',
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'textbox_content_img_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-textbox-content .gem-image span',
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				]
			]
		);

		$control->start_controls_tabs( 'textbox_content_icon_image_tabs' );
		$control->start_controls_tab( 'textbox_content_icon_image_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'textbox_content_icon_bg_color',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon',
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_border_color',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .gem-textbox-content .gem-image span' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_color',
			[
				'label' => __( 'Icon Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#2c2e3d',
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->add_control(
			'textbox_content_img_opacity',
			[
				'label' => __( 'Opacity', 'thegem' ),
				'type' => Controls_Manager::NUMBER,
				'step' => '0.1',
				'min' => '0',
				'max' => '1',
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-image img' => 'opacity: {{VALUE}} !important;',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'textbox_content_img_css_filters',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-textbox-content .gem-image img',
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				]
			]
		);

		$control->add_control(
			'textbox_content_img_blend_mode',
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
					'{{WRAPPER}} .gem-textbox-content .gem-image img' => 'mix-blend-mode: {{VALUE}}',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				]
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_rotate',
			[
				'label' => __( 'Rotate Icon', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon i, {{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_box_rotate',
			[
				'label' => __( 'Rotate Box', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'textbox_content_icon_image_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'textbox_content_icon_bg_color_hover',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon',
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_border_color_hover',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-image span' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_color_hover',
			[
				'label' => __( 'Icon Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon i' => 'color: {{VALUE}};',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_rotate_hover',
			[
				'label' => __( 'Rotate Icon', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon i, {{WRAPPER}} .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);
		
		$control->add_responsive_control(
			'textbox_content_icon_box_rotate_hover',
			[
				'label' => __( 'Rotate Box', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);
		
		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'textbox_content_img_overlay_color_hover',
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
				'selector' => '{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-image span::before',
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				]
			]
		);

		$control->remove_control('textbox_content_img_overlay_color_hover_image');

		$control->add_control(
			'textbox_content_img_opacity_opacity_hover',
			[
				'label' => __( 'Overlay Opacity', 'thegem' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '0.5',
				'step' => '0.1',
				'min' => '0',
				'max' => '1',
				'selectors' => [
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-image > span::before' => 'opacity: {{VALUE}} !important;',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'textbox_content_img_css_filters_hover',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-image img',
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				]
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();
	}


	/**
	 * TextBox Button Styles
	 * @access protected
	 */
	protected function textbox_button_styles( $control ) {

		$control->start_controls_section(
			'textbox_block_button',
			[
				'label' => __( 'Button Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'content_textbox_show_button' => 'yes',
				],
			]
		);
		$control->add_control(
			'textbox_content_button_heading',
			[
				'label' => __( 'Button', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);	

		$control->add_control(
			'textbox_content_button_position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'centered-box button-bottom',
				'options' => [
					'centered-box button-top' => __('Top', 'thegem'),
					'centered-box button-bottom' => __('Bottom', 'thegem'),
				],
			]
		);

		$control->add_control(
			'textbox_content_button_type',
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
			'textbox_content_button_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'small',
				'options' => [
					'tiny' => __('Tiny', 'thegem' ),
					'small' => __('Small', 'thegem' ),
					'medium' => __('Medium', 'thegem'),
					'large' => __('Large', 'thegem' ),
					'giant'  => __('Giant', 'thegem'),
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_button_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-button-container .gem-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'textbox_content_button_border_type',
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
					'{{WRAPPER}} .gem-textbox-content .gem-button-container .gem-button' => 'border-style: {{VALUE}};',
				],
			]
		);
		
		$control->add_control(
			'textbox_content_button_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-button-container .gem-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_button_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-button-container .gem-inner-wrapper-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs( 'textbox_content_button_tabs' );
		$control->start_controls_tab( 'textbox_content_button_tab_normal', [ 'label' => __( 'Normal', 'thegem' ),'condition' => [ 'content_textbox_show_button' => 'yes'], ] );

		$control->add_control(
			'textbox_content_button_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-button-container .gem-button .gem-text-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-textbox-content .gem-button-container .gem-button .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-textbox-content .gem-button-container .gem-button .gem-button-icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_group_control( 
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'textbox_content_button_typography',
				'selector' => '{{WRAPPER}} .gem-textbox .gem-textbox-content .gem-button-container .gem-button',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'textbox_content_button_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-button-container .gem-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_button_border_color',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox-content .gem-button-container .gem-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'textbox_content_button_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-textbox-content .gem-button-container .gem-button',
			]
		);

		$control->end_controls_tab();
		
		$control->start_controls_tab( 'textbox_content_button_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), 'condition' => [ 'content_textbox_show_button' => 'yes'],] );

		$control->add_control(
			'textbox_content_button_text_color_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox .gem-textbox-content .gem-button-container .gem-button:hover .gem-text-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-textbox .gem-textbox-content .gem-button-container .gem-button:hover .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-textbox .gem-textbox-content .gem-button-container .gem-button:hover .gem-button-icon svg' => 'fill:{{VALUE}};',
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-button-container .gem-button.item-linked .gem-text-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-button-container .gem-button.item-linked .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-button-container .gem-button.item-linked .gem-button-icon svg' => 'fill:{{VALUE}};',

				],
			]
		);

		$control->add_group_control( 
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'textbox_content_button_typography_hover',
				'selector' => '{{WRAPPER}} .gem-textbox .gem-textbox-content .gem-button-container .gem-button:hover span, {{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-button-container .gem-button.item-linked span',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'textbox_content_button_bg_color_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox .gem-textbox-content .gem-button-container .gem-button:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-button-container .gem-button.item-linked' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_button_border_color_hover',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-textbox .gem-textbox-content .gem-button-container .gem-button:hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-button-container .gem-button.item-linked' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'textbox_content_button_shadow_hover',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-textbox .gem-textbox-content .gem-button-container .gem-button:hover, {{WRAPPER}} .gem-textbox:hover .gem-textbox-content .gem-button-container .gem-button.item-linked',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_responsive_control(
			'textbox_content_button_icon_align',
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
					'{{WRAPPER}} .gem-textbox .gem-inner-wrapper-btn' => '{{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_button_icon_spacing_right',
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
					'{{WRAPPER}} .gem-textbox-content .gem-button-container .gem-inner-wrapper-btn .gem-button-icon' => 'margin-left:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'textbox_content_button_icon_align' => ['right'],
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_button_icon_spacing_left',
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
					'{{WRAPPER}} .gem-textbox-content .gem-button-container .gem-inner-wrapper-btn .gem-button-icon' => 'margin-right:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'textbox_content_button_icon_align' => ['left'],
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
		if ( 'none' === $settings['content_textbox_icon_image_link_to'] ) {
			return false;
		}

		if ( 'custom' === $settings['content_textbox_icon_image_link_to'] ) {
			if ( empty( $settings['content_textbox_icon_image_link']['url'] ) ) {
				return false;
			}

			return $settings['content_textbox_icon_image_link'];
		}

		return [
			'url' => !empty($settings['content_textbox_graphic_image']['url']) ? $settings['content_textbox_graphic_image']['url'] : '',
		];
	}

	/**
	 * Retrieve image textbox link URL.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array $settings
	 *
	 * @return array|string|false An array/string containing the link URL, or false if no link.
	 */
	private function get_textbox_link_url( $settings ) {

		if ( empty( $settings['content_textbox_link']['url'] ) ) {
			return false;
		}

		return [
			'url'		 => $settings['content_textbox_link']['url'],
			'nofollow'	=> $settings['content_textbox_link']['nofollow'],
			'is_external' => $settings['content_textbox_link']['is_external'],
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
			'basic' => array(
				'textbox_container_padding' => ['top' => '45', 'right' => '30', 'bottom' => '30', 'left' => '30', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_color' => '#2c2e3d',
				'textbox_content_icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '30', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'content_textbox_title_html_tag_weight' => 'bold',
				'textbox_content_descr_bottom_spacing' => ['size' => 25,'unit' => 'px'],
			),
			'rounded' => array(
				'textbox_container_padding' => ['top' => '50', 'right' => '30', 'bottom' => '30', 'left' => '30', 'unit' => 'px', 'isLinked' => false],
				'textbox_container_radius' => ['top' => '70', 'right' => '70', 'bottom' => '70', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_container_align' => 'left',
				'textbox_content_icon_border_radius' => ['top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => '%', 'isLinked' => true],
				'textbox_content_icon_size' => ['size' => 30,'unit' => 'px'],
				'textbox_content_icon_bg_color_background' => 'classic',
				'textbox_content_icon_bg_color_color' => '#FFFFFF',
				'textbox_content_icon_padding' => ['size' => 20,'unit' => 'px'],
				'textbox_content_icon_color' => '#ff6b4e',
				'textbox_content_icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '30', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_title_bottom_spacing' => ['size' => 20,'unit' => 'px'],
				'textbox_content_descr_bottom_spacing' => ['size' => 20,'unit' => 'px'],
				'content_textbox_title_html_tag_weight' => 'bold',
			),
			'box' => array(
				'textbox_container_padding' => ['top' => '56', 'right' => '30', 'bottom' => '34', 'left' => '30', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_border_radius' => ['top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => '%', 'isLinked' => true],
				'textbox_content_icon_size' => ['size' => 35,'unit' => 'px'], 
				'textbox_content_icon_padding' => ['size' => 20,'unit' => 'px'],
				'textbox_content_icon_color' => '#FFFFFF',
				'textbox_content_icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '30', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_bg_color_background' => 'classic',
				'textbox_content_icon_bg_color_color' => '#f44336',
				'content_textbox_title_html_tag_weight' => 'light',
				'textbox_content_descr_bottom_spacing' => ['size' => 25,'unit' => 'px'],
			),
			'capsule' => array(
				'textbox_container_padding' => ['top' => '55', 'right' => '30', 'bottom' => '34', 'left' => '30', 'unit' => 'px', 'isLinked' => false],
				'textbox_container_radius' => ['top' => '55', 'right' => '55', 'bottom' => '55', 'left' => '55', 'unit' => 'px', 'isLinked' => true],
				'textbox_content_icon_border_radius' => ['top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => '%', 'isLinked' => true],
				'textbox_content_icon_size' => ['size' => 35,'unit' => 'px'],
				'textbox_content_icon_padding' => ['size' => 20,'unit' => 'px'],
				'textbox_content_icon_color' => '#FFFFFF',
				'textbox_content_icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '30', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_bg_color_background' => 'classic',
				'textbox_content_icon_bg_color_color' => '#b6c6c9',
				'content_textbox_title_html_tag_weight' => 'bold',
				'textbox_content_descr_bottom_spacing' => ['size' => 25,'unit' => 'px'],
			),
			'tag' => array(
				'textbox_container_padding' => ['top' => '50', 'right' => '30', 'bottom' => '57', 'left' => '30', 'unit' => 'px', 'isLinked' => false],
				'textbox_container_radius' => ['top' => '500', 'right' => '500', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_border_radius' => ['top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => '%', 'isLinked' => true],
				'textbox_content_icon_size' => ['size' => 85,'unit' => 'px'],
				'textbox_content_icon_padding' => ['size' => 35,'unit' => 'px'],
				'textbox_content_icon_color' => '#3c3950',
				'textbox_content_icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '30', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_bg_color_background' => 'classic',
				'textbox_content_icon_bg_color_color' => '#FFFFFF',
				'content_textbox_title_html_tag_weight' => 'bold',
				'textbox_content_descr_bottom_spacing' => ['size' => 25,'unit' => 'px'],
			),
			'flag' => array(
				'textbox_container_padding' => ['top' => '50', 'right' => '30', 'bottom' => '56', 'left' => '30', 'unit' => 'px', 'isLinked' => false],
				'textbox_container_bottom_shape' => 'flag',
				'textbox_content_icon_border_radius' => ['top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => '%', 'isLinked' => true],
				'textbox_content_icon_size' => ['size' => 38,'unit' => 'px'],
				'textbox_content_icon_padding' => ['size' => 20,'unit' => 'px'],
				'textbox_content_icon_color' => '#dcff62',
				'textbox_content_icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '30', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_bg_color_background' => 'classic',
				'textbox_content_icon_bg_color_color' => '#46485c',
				'content_textbox_title_html_tag_weight' => 'bold',
				'textbox_content_descr_bottom_spacing' => ['size' => 25,'unit' => 'px'],
			),
			'shield' => array(
				'textbox_container_padding' => ['top' => '55', 'right' => '30', 'bottom' => '46', 'left' => '30', 'unit' => 'px', 'isLinked' => false],
				'textbox_container_bottom_shape' => 'shield',
				'textbox_container_border_border' => 'solid',
				'textbox_container_border_width' => ['top' => '10', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_container_border_color' => '#A3E7F0',
				'textbox_content_icon_border_radius' => ['top' => '3', 'right' => '3', 'bottom' => '3', 'left' => '3', 'unit' => 'px', 'isLinked' => true],
				'textbox_content_icon_size' => ['size' => 30,'unit' => 'px'],
				'textbox_content_icon_padding' => ['size' => 10,'unit' => 'px'],
				'textbox_content_icon_color' => '#3c3950',
				'textbox_content_icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '30', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_border_type' => 'solid',
				'textbox_content_icon_border_width' => ['top' => '3', 'right' => '3', 'bottom' => '3', 'left' => '3', 'unit' => 'px', 'isLinked' => true],
				'textbox_content_icon_border_color' => '#3c3950',
				'textbox_content_descr_typography_typography' => 'custom',
				'textbox_content_descr_typography_font_size' => ['size' => 24,'unit' => 'px'],
				'textbox_content_descr_typography_line_height' => ['size' => 37,'unit' => 'px'],
				'content_textbox_title_html_tag_weight' => 'bold',
				'textbox_content_descr_bottom_spacing' => ['size' => 25,'unit' => 'px'],
			),
			'ticket' => array(
				'textbox_container_padding' => ['top' => '50', 'right' => '30', 'bottom' => '50', 'left' => '30', 'unit' => 'px', 'isLinked' => false],
				'textbox_container_bottom_shape' => 'ticket',
				'textbox_content_icon_border_type' => 'solid',
				'textbox_content_icon_border_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px', 'isLinked' => true],
				'textbox_content_icon_border_color' => '#f0f3f2',
				'textbox_content_icon_border_radius' => ['top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => '%', 'isLinked' => true],
				'textbox_content_icon_size' => ['size' => 30,'unit' => 'px'],
				'textbox_content_icon_padding' => ['size' => 10,'unit' => 'px'],
				'textbox_content_icon_color' => '#3c3950',
				'textbox_content_icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '30', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_border_type' => 'solid',
				'textbox_content_icon_border_width' => ['top' => '3', 'right' => '3', 'bottom' => '3', 'left' => '3', 'unit' => 'px', 'isLinked' => true],
				'textbox_content_icon_border_color' => '#3c3950',
				'content_textbox_title_html_tag_weight' => 'bold',
				'textbox_content_descr_bottom_spacing' => ['size' => 25,'unit' => 'px'],
			),
			'note' => array(
				'textbox_container_padding' => ['top' => '70', 'right' => '30', 'bottom' => '21', 'left' => '30', 'unit' => 'px', 'isLinked' => false],
				'textbox_container_bottom_shape' => 'note-2',
				'textbox_content_icon_size' => ['size' => 35,'unit' => 'px'],
				'textbox_content_icon_color' => '#f44336',
				'textbox_content_icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '30', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'content_textbox_title_html_tag_weight' => 'bold',
				'textbox_content_descr_bottom_spacing' => ['size' => 25,'unit' => 'px'],
			),
			'profile' => array(
				'textbox_container_padding' => ['top' => '47', 'right' => '30', 'bottom' => '36', 'left' => '30', 'unit' => 'px', 'isLinked' => false],
				'textbox_container_radius' => ['top' => '57', 'right' => '57', 'bottom' => '57', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_container_align' => 'left',
				'textbox_content_icon_border_radius' => ['top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => '%', 'isLinked' => true],
				'textbox_content_icon_padding' => ['size' => 12,'unit' => 'px'],
				'textbox_content_icon_image_horizontal_position' => 'right',
				'textbox_content_icon_color' => '#3c3950',
				'textbox_content_icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '30', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_size' => ['size' => 30,'unit' => 'px'],
				'textbox_content_icon_bg_color_background' => 'classic',
				'textbox_content_icon_bg_color_color' => '#bce784',
				'textbox_content_title_top_spacing' => ['size' => 60,'unit' => 'px'],
				'content_textbox_title_html_tag_weight' => 'light',
				'textbox_content_descr_bottom_spacing' => ['size' => 25,'unit' => 'px'],
			),
			'sentence' => array(
				'textbox_container_padding' => ['top' => '55', 'right' => '30', 'bottom' => '36', 'left' => '30', 'unit' => 'px', 'isLinked' => false],
				'textbox_container_bottom_shape' => 'sentence',
				'textbox_content_icon_border_radius' => ['top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => '%', 'isLinked' => true],
				'textbox_content_icon_size' => ['size' => 30,'unit' => 'px'],
				'textbox_content_icon_padding' => ['size' => 10,'unit' => 'px'],
				'textbox_content_icon_color' => '#3c3950',
				'textbox_content_icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '30', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_border_type' => 'solid',
				'textbox_content_icon_border_width' => ['top' => '3', 'right' => '3', 'bottom' => '3', 'left' => '3', 'unit' => 'px', 'isLinked' => true],
				'textbox_content_icon_border_color' => '#3c3950',
				'content_textbox_title_html_tag_weight' => 'light',
				'textbox_content_descr_bottom_spacing' => ['size' => 25,'unit' => 'px'],
			),
			'minimal' => array(
				'textbox_container_padding' => ['top' => '55', 'right' => '30', 'bottom' => '34', 'left' => '30', 'unit' => 'px', 'isLinked' => false],
				'textbox_container_align' => 'left',
				'textbox_content_icon_image_horizontal_position' => 'right',
				'textbox_content_icon_image_vertical_position' => 'icon-bottom',
				'textbox_content_icon_color' => '#c0dc6e',
				'textbox_content_icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '30', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_size' => ['size' => 35,'unit' => 'px'],
				'textbox_content_descr_bottom_spacing' => ['size' => 60,'unit' => 'px'],
				'content_textbox_title_html_tag_weight' => 'bold',
			),
			'basic-left-aligned' => array(
				'textbox_container_padding' => ['top' => '55', 'right' => '30', 'bottom' => '34', 'left' => '30', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_image_horizontal_position' => 'right',
				'textbox_container_align' => 'left',
				'textbox_content_icon_margin' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '40',	'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_color' => '#2c2e3d',
				'textbox_content_icon_image_text_wrapping' => 'inline',
				'content_textbox_title_html_tag_weight' => 'bold',
				'textbox_content_descr_bottom_spacing' => ['size' => 25,'unit' => 'px'],
			),
			'basic-right-aligned' => array(
				'textbox_container_padding' => ['top' => '55', 'right' => '30', 'bottom' => '34', 'left' => '30', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_image_horizontal_position' => 'left',
				'textbox_container_align' => 'right',
				'textbox_content_icon_color' => '#2c2e3d',
				'textbox_content_icon_margin' => ['top' => '0', 'right' => '40', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_color' => '#2c2e3d',
				'textbox_content_icon_image_text_wrapping' => 'inline',
				'content_textbox_title_html_tag_weight' => 'bold',
				'textbox_content_descr_bottom_spacing' => ['size' => 25,'unit' => 'px'],
			),
		);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new TheGem_StyledTextBox() );