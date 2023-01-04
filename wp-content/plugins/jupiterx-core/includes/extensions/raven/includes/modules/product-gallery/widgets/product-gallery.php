<?php
namespace JupiterX_Core\Raven\Modules\Product_Gallery\Widgets;

use JupiterX_Core\Raven\Base\Base_Widget;
use Elementor\Plugin as Elementor;
use Elementor\Group_Control_Image_Size;
use Elementor\Control_Media;
use JupiterX_Core\Raven\Utils;


defined( 'ABSPATH' ) || die();

/**
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Product_Gallery extends Base_Widget {

	public function get_name() {
		return 'raven-product-gallery';
	}

	public function get_title() {
		return esc_html__( 'Product Gallery', 'jupiterx-core' );
	}

	public function get_icon() {
		return 'raven-element-icon raven-element-icon-product-gallery';
	}

	public function get_categories() {
		return [ 'jupiterx-core-raven-woo-elements' ];
	}

	public function get_script_depends() {
		return [
			'wc-single-product',
			'flexslider',
			'zoom',
			'photoswipe',
			'photoswipe-ui-default',
		];
	}

	public function get_style_depends() {
		return [ 'photoswipe-default-skin' ];
	}

	protected function register_controls() {
		$this->register_section_content();
		$this->register_section_product_image();
		$this->register_section_product_thumbnail();
	}

	private function register_section_content() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'video_enable',
			[
				'type' => 'hidden',
				'default' => jupiterx_core_get_option( 'enable_media_controls' ),
				'frontend_available' => 'true',
			]
		);

		$this->add_control(
			'gallery_layout',
			[
				'label' => esc_html__( 'Gallery Layout', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'standard',
				'options' => [
					'standard' => esc_html__( 'Standard (Slider)', 'jupiterx-core' ),
					'stack' => esc_html__( 'Stack', 'jupiterx-core' ),
				],
				'frontend_available' => 'true',
			]
		);

		$this->add_control(
			'thumbnails',
			[
				'label' => esc_html__( 'Thumbnails', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'horizontal',
				'options' => [
					'horizontal' => esc_html__( 'Horizontal', 'jupiterx-core' ),
					'left' => esc_html__( 'Vertical (Left)', 'jupiterx-core' ),
					'right' => esc_html__( 'Vertical (Right)', 'jupiterx-core' ),
				],
				'condition' => [
					'gallery_layout' => 'standard',
				],
				'frontend_available' => 'true',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '2',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => esc_html__( '1', 'jupiterx-core' ),
					'2' => esc_html__( '2', 'jupiterx-core' ),
					'3' => esc_html__( '3', 'jupiterx-core' ),
				],
				'condition' => [
					'gallery_layout' => 'stack',
				],
				'frontend_available' => true,
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-stack-wrapper' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		$this->add_control(
			'lightbox', [
				'label'        => esc_html__( 'Lightbox', 'jupiterx-core' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off'    => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'true',
				'default'      => 'true',
				'frontend_available' => 'true',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'zoom', [
				'label'        => esc_html__( 'Zoom', 'jupiterx-core' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off'    => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'true',
				'default'      => 'true',
				'frontend_available' => 'true',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'carousel', [
				'label'        => esc_html__( 'Carousel', 'jupiterx-core' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off'    => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'true',
				'default'      => '',
				'frontend_available' => 'true',
				'condition' => [
					'gallery_layout' => 'standard',
				],
				'render_type' => 'template',
			]
		);

		$this->add_group_control(
			'image-size',
			[
				'name' => 'image',
				'default' => 'woocommerce_thumbnail',
				'condition' => [
					'gallery_layout' => 'stack',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_section_product_image() {
		$this->start_controls_section(
			'section_product_image',
			[
				'label' => esc_html__( 'Product image', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_responsive_control(
			'spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'separator' => 'after',
				'size_units' => [ 'px' ],
				'default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-stack-wrapper' => 'grid-gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'gallery_layout' => 'stack',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .raven-product-gallery-stack-wrapper li, {{WRAPPER}} .raven-product-gallery-standard .flex-viewport',
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-stack-wrapper li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					'{{WRAPPER}} .raven-product-gallery-standard .flex-viewport' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'standard_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-left .flex-viewport' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-product-gallery-left .flex-direction-nav' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-product-gallery-right .flex-viewport' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-product-gallery-right .flex-direction-nav' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-product-gallery-horizontal .flex-viewport' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-product-gallery-horizontal > .flex-direction-nav' => 'top:calc(48.5% - {{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'gallery_layout' => 'standard',
				],
				'frontend_available' => true,
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'image_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .raven-product-gallery-stack-wrapper li',
				'condition' => [
					'gallery_layout' => 'stack',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_section_product_thumbnail() {
		$this->start_controls_section(
			'section_product_thumbnail',
			[
				'label' => esc_html__( 'Thumbnail', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'thumbnail_border',
				'selector' => '{{WRAPPER}} .raven-product-gallery-standard .flex-control-thumbs li',
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->add_control(
			'thumbnail_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-standard .flex-control-thumbs li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					'{{WRAPPER}} .raven-product-gallery-standard .flex-control-thumbs li img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->add_responsive_control(
			'standard_gallery_image_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-wrapper .raven-product-gallery-horizontal .flex-control-thumbs li' => 'margin-right:{{SIZE}}{{UNIT}}!important;',
					'{{WRAPPER}} .raven-product-gallery-wrapper .raven-product-gallery-horizontal .flex-control-thumbs .slick-track' => 'margin-right:-{{SIZE}}{{UNIT}}!important;',
					'{{WRAPPER}} .raven-product-gallery-wrapper .raven-product-gallery-left .flex-control-thumbs li' => 'margin-bottom:{{SIZE}}{{UNIT}}!important;',
					'{{WRAPPER}} .raven-product-gallery-wrapper .raven-product-gallery-left .flex-control-thumbs li:last-child' => 'margin-bottom:0 !important;',
					'{{WRAPPER}} .raven-product-gallery-wrapper .raven-product-gallery-right .flex-control-thumbs li' => 'margin-bottom:{{SIZE}}{{UNIT}}!important;',
					'{{WRAPPER}} .raven-product-gallery-wrapper .raven-product-gallery-right .flex-control-thumbs li:last-child' => 'margin-bottom:0 !important;',
				],
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$options_class = '';

		if ( ! empty( $settings['lightbox'] ) ) {
			$options_class .= 'raven-product-gallery-lightbox ';
		}

		if ( ! empty( $settings['zoom'] ) ) {
			$options_class .= 'raven-product-gallery-zoom ';
		}

		$this->add_render_attribute(
			'wrapper',
			'class',
			'raven-product-gallery-wrapper raven-product-gallery-' . $settings['gallery_layout'] . ' ' . $options_class
		);

		$this->add_render_attribute( 'images', [
			'class' => 'elementor-clickable',
			'data-elementor-open-lightbox' => $settings['lightbox'],
		] );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
		<?php
			if ( 'standard' === $settings['gallery_layout'] ) {
				$thumbnails = $settings['thumbnails'];
				echo '<div class="woocommerce-product-gallery-raven-widget raven-product-gallery-' . $thumbnails . '">';
				$this->render_standard( $settings );
				echo '</div>';
			} else {
				$this->render_stack( $settings );
			}
		?>
		</div>
		<?php
	}

	/**
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	private function render_standard( $settings ) {
		Utils::get_product();

		global $product;

		if ( empty( $product ) ) {
			return;
		}

		$required_options = [];

		if ( ! empty( $settings['zoom'] ) && empty( current_theme_supports( 'wc-product-gallery-zoom' ) ) ) {
			$required_options['data-wc-disable-zoom'] = 1;
		}

		if ( ! empty( $settings['lightbox'] ) && empty( current_theme_supports( 'wc-product-gallery-lightbox' ) ) ) {
			$required_options['data-wc-disable-lightbox'] = 1;

			add_action( 'wp_footer', function() {
				wc_get_template( 'single-product/photoswipe.php' );
			} );
		}

		$this->add_render_attribute(
			'_wrapper',
			$required_options
		);

		$columns           = 1;
		$post_thumbnail_id = $product->get_image_id();
		$wrapper_classes   = apply_filters(
			'woocommerce_single_product_image_gallery_classes',
			[
				'raven-product-gallery-slider-wrapper',
				'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
				'woocommerce-product-gallery--columns-1',
				'images',
			]
		);
		?>
		<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
			<figure class="woocommerce-product-gallery__wrapper">
				<?php
				if ( $post_thumbnail_id ) {
					$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
				} else {
					$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
					$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'noks-core' ) );
					$html .= '</div>';
				}

				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped

				$attachment_ids = $product->get_gallery_image_ids();

				if ( $attachment_ids && $product->get_image_id() ) {
					foreach ( $attachment_ids as $attachment_id ) {
						echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html( $attachment_id ), $attachment_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
					}
				}
				?>
			</figure>
		</div>
		<?php
	}

	private function render_stack( $settings ) {
		$images = $this->get_images();

		if ( empty( $images ) ) {
			return;
		}

		$this->add_render_attribute( 'columns', [
			'class' => 'raven-product-gallery-stack-wrapper',
		] );

		$html = '<ul ' . $this->get_render_attribute_string( 'columns' ) . '>';
		foreach ( $images as $image ) {
			$media = $this->render_media( $image, $settings );
			$html .= sprintf(
				'<li class="%1$s">%2$s</li>',
				esc_attr( 'jupiterx-product-gallery-stack-' . $media['type'] ),
				$media['content']
			);
		}

		$html .= '</ul>';

		echo $html;
	}

	private function get_images() {
		Utils::get_product();

		global $product;

		$images = [];

		if ( empty( $product ) ) {
			return;
		}

		if ( ! empty( $product->get_image_id() ) ) {
			$images[] = $product->get_image_id();
		}

		$gallery_ids = $product->get_gallery_image_ids();

		if ( empty( $gallery_ids ) ) {
			return $images;
		}

		foreach ( $gallery_ids as $id ) {
			$images[] = $id;
		}

		if ( $product->is_type( 'variable' ) && ! empty( $product->get_available_variations() ) ) {
			foreach ( $product->get_available_variations() as $variation ) {
				$img = $variation['image_id'];

				if ( ! in_array( $img, $images, true ) ) {
					$images[] = $img;
				}
			}
		}

		return array_unique( $images );
	}

	private function render_media( $image_id, $settings ) {
		if ( empty( jupiterx_get_option( 'enable_media_controls', 0 ) ) ) {
			return [
				'content' => $this->get_thumbnail( $image_id, $settings ),
				'type' => 'image',
			];
		}

		$data = get_post_meta( $image_id, '_jupiterx_attachment_meta', true );

		if ( empty( $data ) ) {
			return [
				'content' => $this->get_thumbnail( $image_id, $settings ),
				'type' => 'image',
			];
		}

		return [
			'content' => $this->get_video( $image_id, $data, $settings ),
			'type' => 'video',
		];
	}

	private function get_thumbnail( $image, $settings ) {
		$image_src = Group_Control_Image_Size::get_attachment_image_src( $image, 'image', $settings );

		$image_tag = sprintf(
			'<img class="%4$s" src="%1$s" title="%2$s" alt="%3$s" %5$s />',
			esc_attr( $image_src ),
			Control_Media::get_image_title( $image ),
			Control_Media::get_image_alt( $image ),
			'raven-product-gallery-stack-image',
			$this->get_image_size( $image, $settings )
		);

		if ( ! empty( $settings['lightbox'] ) ) {
			$result = sprintf(
				'<a %1$s href="%2$s">%3$s</a>',
				$this->get_render_attribute_string( 'images' ),
				$this->get_thumbnail_src( $image ),
				$image_tag
			);

			return $result;
		}

		return $image_tag;
	}

	private function get_video( $image, $data, $settings ) {
		if ( ! class_exists( 'JupiterX_Core_Product_Gallery_Video' ) ) {
			return $this->get_thumbnail( $image, $settings );
		}

		$video_gallery = new \JupiterX_Core_Product_Gallery_Video();
		$video_content = $video_gallery->get_meta_data( $data, $image );

		if ( empty( $video_content ) ) {
			return $this->get_thumbnail( $image, $settings );
		}

		return $video_content['content'];
	}

	private function get_thumbnail_src( $image ) {
		if ( empty( $image ) ) {
			return;
		}

		$image = wp_get_attachment_image_src( $image, 'full' );

		return $image[0];
	}

	private function get_image_size( $image, $settings ) {
		$size      = $settings['image_size'];
		$dimension = [];

		$original_image = [
			'data-src' => wp_get_attachment_image_src( $image, 'full' )[0],
			'data-large_image_width' => wp_get_attachment_image_src( $image, 'full' )[1],
			'data-large_image_height' => wp_get_attachment_image_src( $image, 'full' )[2],
		];

		if ( 'custom' === $size ) {
			$image_size = $settings['image_custom_dimension'];

			$dimension = [
				'width' => ! empty( $image_size['width'] ) ? $image_size['width'] : 0,
				'height' => ! empty( $image_size['height'] ) ? $image_size['width'] : 0,
			];
		} else {
			$image_size = image_downsize( $image, $size );

			$dimension = [
				'width' => ! empty( $image_size[1] ) ? $image_size[1] : 0,
				'height' => ! empty( $image_size[2] ) ? $image_size[2] : 0,
			];
		}

		$dimension = array_merge( $dimension, $original_image );

		if ( empty( $dimension ) ) {
			return;
		}

		$attribute = '';

		foreach ( $dimension as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			$attribute .= $key . '="' . $value . '" ';
		}

		return $attribute;
	}
}
