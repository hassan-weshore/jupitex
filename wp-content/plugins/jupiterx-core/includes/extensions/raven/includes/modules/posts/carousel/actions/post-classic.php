<?php
/**
 * @codingStandardsIgnoreFile
 */

namespace JupiterX_Core\Raven\Modules\Posts\Carousel\Actions;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Modules\Posts\Classes\Post_Base;

class Post_Classic extends Post_Base {

	protected function register_action_hooks() {
		add_action( 'elementor/element/raven-posts-carousel/section_sort_filter/after_section_end', [ $this, 'register_action_controls' ] );
	}

	public function register_action_controls( \Elementor\Widget_Base $widget ) {
		$this->skin = $widget->get_skin( 'classic' );

		$this->register_controls();
		$this->inject_controls();
	}

	protected function register_controls() {
		$this->register_container_controls();
		$this->register_image_controls();
		$this->register_icons_controls();
		$this->register_title_controls();
		$this->register_meta_controls();
		$this->register_excerpt_controls();
		$this->register_button_controls();
	}

	protected function inject_controls() {
		$this->skin->start_injection( [
			'at' => 'after',
			'of' => $this->skin->get_control_id( 'slides_scroll' ),
		] );

		$this->register_image_size_control();

		$this->register_settings_controls();

		$this->skin->end_injection();

		$this->skin->start_injection( [
			'at' => 'before',
			'of' => $this->skin->get_control_id( 'post_padding' ),
		] );

		$this->skin->add_responsive_control(
			'parent_padding',
			[
				'label' => __( 'Parent Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'render_type' => 'template',
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .swiper-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->skin->add_responsive_control(
			'columns_height',
			[
				'label' => __( 'Height', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->skin->add_responsive_control(
			'columns_space_between',
			[
				'label' => __( 'Space Between', 'jupiterx-core' ),
				'frontend_available' => true,
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'desktop_default' => [
						'size' => 30,
						'unit' => 'px',
				],
				'tablet_default' => [
						'size' => 20,
						'unit' => 'px',
				],
				'mobile_default' => [
						'size' => 10,
						'unit' => 'px',
				],
			]
		);

		$this->skin->end_injection();
	}

	public function render_post( $instance ) {
		$this->skin = $instance;

		$show_image = $this->skin->get_instance_value( 'show_image' );

		$image_position = $this->skin->get_instance_value( 'post_image_position' );

		$hover_effect = $this->skin->get_instance_value( 'post_hover_effect' );

		$post_classes = [ 'raven-post' ];

		if ( 'yes' === $show_image && 'top' !== $image_position ) {
			$post_classes[] = 'raven-post-inline raven-post-inline-' . $image_position;
		}

		if ( ! empty( $hover_effect ) ) {
			$post_classes[] = 'elementor-animation-' . $hover_effect;
		}

		?>

		<div class="swiper-slide">
			<div class="raven-post-wrapper">
				<div class="<?php echo esc_attr( implode( ' ', $post_classes ) ); ?>">
					<?php $this->render_image(); ?>

					<div class="raven-post-content">
						<?php
						$this->render_ordered_content();
						$this->render_button();
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render_image() {
		if ( ! $this->skin->get_instance_value( 'show_image' ) ) {
			return;
		}

		$settings = [
			'image_size' => $this->skin->get_instance_value( 'post_image_size_size' ),
			'image' => [
				'id' => get_post_thumbnail_id(),
			],
			'image_custom_dimension' => $this->skin->get_instance_value( 'post_image_size_custom_dimension' ),
		];

		$image_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings );

		if ( empty( $image_html ) ) {
			return;
		}

		$classes = [
			'raven-post-image',
			'raven-image-fit',
		];

		$html_tag = $this->has_overlay() && ! $this->disabled_overlay() ? 'span' : 'a';

		?>
		<div class="raven-post-image-wrap">
			<?php
			printf(
				'<%1$s class="%2$s" %3$s>%4$s <span class="raven-post-image-overlay">%5$s</span></%1$s>',
				$html_tag,
				implode( ' ', $classes ),
				( 'a' === $html_tag ) ? 'href="' . get_permalink() . '"' : '',
				$image_html,
				$this->get_render_overlay()
			);
			?>
		</div>
		<?php
	}
}
