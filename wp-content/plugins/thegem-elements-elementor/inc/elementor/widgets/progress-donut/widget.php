<?php

namespace TheGem_Elementor\Widgets\ProgressDonut;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Elementor widget for Progress Donut.
 */
class TheGem_ProgressDonut extends Widget_Base {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		if ( ! defined( 'THEGEM_ELEMENTOR_WIDGET_PROGRESSDONUT_DIR' ) ) {
			define( 'THEGEM_ELEMENTOR_WIDGET_PROGRESSDONUT_DIR', rtrim( __DIR__, ' /\\' ) );
		}

		if ( ! defined( 'THEGEM_ELEMENTOR_WIDGET_PROGRESSDONUT_URL' ) ) {
			define( 'THEGEM_ELEMENTOR_WIDGET_PROGRESSDONUT_URL', rtrim( plugin_dir_url( __FILE__ ), ' /\\' ) );
		}

		wp_register_style( 'thegem-donut', THEGEM_ELEMENTOR_WIDGET_PROGRESSDONUT_URL . '/assets/css/thegem-donut.css', array(), null );
		wp_register_script( 'thegem-resize', THEGEM_ELEMENTOR_WIDGET_PROGRESSDONUT_URL . '/assets/js/ResizeSensor.js', array( 'jquery' ), null, true );
		wp_register_script( 'thegem-circle', THEGEM_ELEMENTOR_WIDGET_PROGRESSDONUT_URL . '/assets/js/thegem-circle.js', array( 'thegem-resize' ), null, true );
		wp_register_script( 'thegem-donut', THEGEM_ELEMENTOR_WIDGET_PROGRESSDONUT_URL . '/assets/js/thegem-donut.js', array( 'thegem-circle' ), null, true );

	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-progressdonut';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Progress Donut', 'thegem' );
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
		return [ 'thegem-donut' ];
	}

	public function get_script_depends() {
		return [ 'thegem-donut' ];
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
			'values_settings',
			[
				'label' => __( 'Value & Label', 'thegem' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_responsive_control(
			'value',
			[
				'label' => __('Value', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 33,
				'description' => 'Select range from 0 to 100',
			]
		);

		$this->add_control(
			'show_label',
			[
				'label'     => __( 'Show Label', 'thegem' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);

		$this->add_control(
			'label',
			[
				'label' => __('Label', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'description' => 'leaving empty will set value from "Value" field',
				'condition' => [
					'show_label' => 'yes',
				],
			]
		);		

		$this->add_control(
			'units',
			[
				'label' => __('Unit', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'description' => '​Units like %, px, $ etc.',
				'condition' => [
					'show_label' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'show_label' => 'yes',
				],
			]
		);

		$this->end_controls_section();		
	}

	/**
	 * Style Settings
	 */
	protected function style_settings() {
		$this->bar_style();
		$this->text_style();
		$this->icon_style();
	}

	protected function bar_style() {
		$this->start_controls_section(
			'bar_style',
			[
				'label' => __( 'Bar Style', 'thegem' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'bar_width',
			[
				'label' => __('Bar Weight', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 5,
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .gem_chart .gem_chart_back' => 'border-width: calc({{SIZE}}px + 1px);',
				],
			]
		);

		$this->add_responsive_control(
			'base_color',
			[
				'label' => __('Base Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem_chart .gem_chart_back' => 'border-color: {{VALUE}};',
				],
			]
		);	

		$this->add_responsive_control(
			'level_color',
			[
				'label' => __('Level Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default'     => '#3c3950',
				'render_type' => 'template',
			]
		);	

		$this->end_controls_section();
	}

	protected function text_style() {
		$this->start_controls_section(
			'text_style',
			[
				'label' => __( 'Text Style', 'thegem' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_label' => 'yes',
					'icon[value]' => '',
				],
			]
		);

		$this->add_responsive_control(
			'text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem_chart .gem_chart_value' => 'color: {{VALUE}};',
				],
			]
		);	

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'text_typo',
				'selector' => '{{WRAPPER}} .gem_chart .gem_chart_value',
			]
		);
		
		$this->end_controls_section();
	}

	protected function icon_style() {

		$this->start_controls_section(
			'icon_style',
			[
				'label' => __( 'Icon Style', 'thegem' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_label' => 'yes',
					'icon[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __('Icon Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						// 'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem_chart .gem_chart_icon i' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .gem_chart .gem_chart_icon svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				],
			]
		);
		
		$this->add_responsive_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem_chart .gem_chart_icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem_chart .gem_chart_icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_rotate',
			[
				'label' => __('Rotate Icon', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem_chart .gem_chart_icon' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'icon_shadow',
				'label' => __('Icon Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .gem_chart .gem_chart_icon i',
			]
		);
		
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

		$this->add_render_attribute( 'main-donut-wrap', 'class', 'gem_chart');
		$this->add_render_attribute( 'main-donut-wrap', 'data-pie-value', $settings['value']);
		$this->add_render_attribute( 'main-donut-wrap', 'data-pie-label-value', 
			($settings['label'] ? $settings['label'] : $settings['value']));
		$this->add_render_attribute( 'main-donut-wrap', 'data-pie-units', $settings['units']);
		$this->add_render_attribute( 'main-donut-wrap', 'data-pie-width', $settings['bar_width']['size']);
		$this->add_render_attribute( 'main-donut-wrap', 'data-pie-color', $settings['level_color']);

		?>

		<div <?php echo $this->get_render_attribute_string( 'main-donut-wrap' ); ?>>
			<div class="gem_wrapper">
				<span class="gem_chart_back"></span>
				<span class="gem_chart_value" style="<?php echo ($settings['icon']['value'] ? 'display: none;' : ''); ?>">
				</span>
				<?php if ( ! empty( $settings['icon']['value'] ) ) { ?>
					<span class="gem_chart_icon">
						<?php Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true'] ); ?>
					</span>
					<?php } ?>
				<canvas width="101" height="101"></canvas>
			</div>
		</div>
		<?php if(is_admin() && Plugin::$instance->editor->is_edit_mode() ): ?>
			<script type="text/javascript">
				gemDonut();
				new ResizeSensor(jQuery('.gem_chart'), function(){
				    gemDonut();
				});
			</script>
		<?php endif;
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new TheGem_ProgressDonut() );
