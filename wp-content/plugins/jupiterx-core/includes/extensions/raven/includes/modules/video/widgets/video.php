<?php
namespace JupiterX_Core\Raven\Modules\Video\Widgets;

use JupiterX_Core\Raven\Base\Base_Widget;
use JupiterX_Core\Raven\Utils;
use Elementor\Plugin as Elementor;

defined( 'ABSPATH' ) || die();

/**
 * Temporary suppressed.
 *
 * @SuppressWarnings(PHPMD)
 */
class Video extends Base_Widget {

	public function get_name() {
		return 'raven-video';
	}

	public function get_title() {
		return __( 'Video', 'jupiterx-core' );
	}

	public function get_icon() {
		return 'raven-element-icon raven-element-icon-video';
	}

	protected function register_controls() {
		$this->register_section_video();
		$this->register_section_image_overlay();
		$this->register_section_device_frame();
		$this->register_section_settings();
		$this->register_section_icon();
	}

	private function register_section_video() {
		$this->start_controls_section(
			'section_video',
			[
				'label' => __( 'Video', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'video_type',
			[
				'label' => __( 'Video Type', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'youtube',
				'frontend_available' => true,
				'options' => [
					'youtube' => __( 'YouTube', 'jupiterx-core' ),
					'vimeo' => __( 'Vimeo', 'jupiterx-core' ),
					'hosted' => __( 'Self Hosted', 'jupiterx-core' ),
				],
			]
		);

		$this->add_control(
			'youtube_link',
			[
				'label' => __( 'Link', 'jupiterx-core' ),
				'type' => 'text',
				'placeholder' => __( 'Enter your YouTube link', 'jupiterx-core' ),
				'default' => 'https://www.youtube.com/watch?v=GuAL8OhcbNk',
				'label_block' => true,
				'condition' => [
					'video_type' => 'youtube',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'vimeo_link',
			[
				'label' => __( 'Link', 'jupiterx-core' ),
				'type' => 'text',
				'placeholder' => __( 'Enter your Vimeo link', 'jupiterx-core' ),
				'default' => 'https://vimeo.com/235215203',
				'label_block' => true,
				'condition' => [
					'video_type' => 'vimeo',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'hosted_link',
			[
				'label' => __( 'Upload Video - MP4', 'jupiterx-core' ),
				'type' => 'raven_media',
				'placeholder' => __( 'https://your-link.com', 'jupiterx-core' ),
				'label_block' => true,
				'query' => [
					'type' => 'video/mp4',
				],
				'condition' => [
					'video_type' => 'hosted',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'hosted_link_webm',
			[
				'label' => __( 'Upload Video - WebM', 'jupiterx-core' ),
				'type' => 'raven_media',
				'placeholder' => __( 'https://your-link.com', 'jupiterx-core' ),
				'label_block' => true,
				'query' => [
					'type' => 'video/webm',
				],
				'condition' => [
					'video_type' => 'hosted',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_section_image_overlay() {
		$this->start_controls_section(
			'section_image_overlay',
			[
				'label' => __( 'Image Overlay', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'show_image_overlay',
			[
				'label' => __( 'Image Overlay', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_off' => __( 'Hide', 'jupiterx-core' ),
				'label_on' => __( 'Show', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'image_overlay',
			[
				'label' => __( 'Image', 'jupiterx-core' ),
				'type' => 'media',
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'show_image_overlay' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'show_play_icon',
			[
				'label' => __( 'Play Icon', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'yes',
				'label_off' => __( 'No', 'jupiterx-core' ),
				'label_on' => __( 'Yes', 'jupiterx-core' ),
				'condition' => [
					'show_image_overlay' => 'yes',
					'image_overlay[url]!' => '',
				],
			]
		);

		$this->add_control(
			'play_icon_new',
			[
				'label' => __( 'Icon', 'jupiterx-core' ),
				'type' => 'icons',
				'fa4compatibility' => 'play_icon',
				'default' => [
					'value' => 'fas fa-play',
					'library' => 'fa-solid',
				],
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'use_lightbox',
			[
				'label' => __( 'Lightbox', 'jupiterx-core' ),
				'type' => 'switcher',
				'frontend_available' => true,
				'label_off' => __( 'Off', 'jupiterx-core' ),
				'label_on' => __( 'On', 'jupiterx-core' ),
				'condition' => [
					'show_image_overlay' => 'yes',
					'image_overlay[url]!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_section_device_frame() {
		$this->start_controls_section(
			'section_device_frame',
			[
				'label' => __( 'Device Mockup Frame', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'show_device_frame',
			[
				'label' => __( 'Frame this video in Device Mockup', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_off' => __( 'Hide', 'jupiterx-core' ),
				'label_on' => __( 'Show', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'device_frame',
			[
				'label' => __( 'Device Type', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'desktop',
				'options' => [
					'desktop' => __( 'Desktop', 'jupiterx-core' ),
					'laptop' => __( 'Laptop', 'jupiterx-core' ),
				],
				'condition' => [
					'show_device_frame' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_section_settings() {
		$this->start_controls_section(
			'section_settings',
			[
				'label' => __( 'Settings', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'video_aspect_ratio',
			[
				'label' => __( 'Aspect Ratio', 'jupiterx-core' ),
				'type' => 'select',
				'options' => [
					'169' => '16:9',
					'43' => '4:3',
					'32' => '3:2',
				],
				'default' => '169',
				'prefix_class' => 'elementor-aspect-ratio-',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'youtube_autoplay',
			[
				'label' => __( 'Autoplay', 'jupiterx-core' ),
				'type' => 'switcher',
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'youtube_loop',
			[
				'label' => __( 'Loop', 'jupiterx-core' ),
				'type' => 'switcher',
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'youtube_rel',
			[
				'label' => __( 'Suggested Videos', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_off' => __( 'Hide', 'jupiterx-core' ),
				'label_on' => __( 'Show', 'jupiterx-core' ),
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'youtube_controls',
			[
				'label' => __( 'Player Control', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_off' => __( 'Hide', 'jupiterx-core' ),
				'label_on' => __( 'Show', 'jupiterx-core' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'youtube_showinfo',
			[
				'label' => __( 'Player Title & Actions', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_off' => __( 'Hide', 'jupiterx-core' ),
				'label_on' => __( 'Show', 'jupiterx-core' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'youtube_mute',
			[
				'label' => __( 'Mute', 'jupiterx-core' ),
				'type' => 'switcher',
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'youtube_privacy',
			[
				'label' => __( 'Privacy Mode', 'jupiterx-core' ),
				'type' => 'switcher',
				'description' => __( 'When you turn on privacy mode, YouTube won\'t store information about visitors on your website unless they play the video.', 'jupiterx-core' ),
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'vimeo_autoplay',
			[
				'label' => __( 'Autoplay', 'jupiterx-core' ),
				'type' => 'switcher',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_loop',
			[
				'label' => __( 'Loop', 'jupiterx-core' ),
				'type' => 'switcher',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_title',
			[
				'label' => __( 'Intro Title', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_off' => __( 'Hide', 'jupiterx-core' ),
				'label_on' => __( 'Show', 'jupiterx-core' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_portrait',
			[
				'label' => __( 'Intro Portrait', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_off' => __( 'Hide', 'jupiterx-core' ),
				'label_on' => __( 'Show', 'jupiterx-core' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_byline',
			[
				'label' => __( 'Intro Byline', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_off' => __( 'Hide', 'jupiterx-core' ),
				'label_on' => __( 'Show', 'jupiterx-core' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_color',
			[
				'label' => __( 'Controls Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'hosted_autoplay',
			[
				'label' => __( 'Autoplay', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'off',
				'condition' => [
					'video_type' => 'hosted',
				],
			]
		);

		$this->add_control(
			'hosted_autoplay_notice',
			[
				'raw' => __( 'To enable autoplay, you need to Mute the video due to new browsers policy.', 'jupiterx-core' ),
				'type' => 'raw_html',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition' => [
					'hosted_autoplay!' => '',
				],
			]
		);

		$this->add_control(
			'hosted_muted',
			[
				'label' => __( 'Mute', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'off',
				'condition' => [
					'video_type' => 'hosted',
				],
			]
		);

		$this->add_control(
			'hosted_loop',
			[
				'label' => __( 'Loop', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'off',
				'condition' => [
					'video_type' => 'hosted',
				],
			]
		);

		$this->add_control(
			'hosted_controls',
			[
				'label' => __( 'Player Controls', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'off',
				'condition' => [
					'video_type' => 'hosted',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_section_icon() {
		$this->start_controls_section(
			'section_icon',
			[
				'label' => __( 'Icon', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'play_icon_color',
			[
				'label' => __( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-video-play i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-video-play > svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'play_icon_size',
			[
				'label' => __( 'Size', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-video-play i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-video-play > svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'text-shadow',
			[
				'name' => 'play_icon_shadow',
				'fields_options' => [
					'text_shadow_type' => [
						'label' => _x( 'Shadow', 'Text Shadow Control', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-video-play i',
				'selector' => '{{WRAPPER}} .raven-video-play > svg',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$migration_allowed = Elementor::$instance->icons_manager->is_migration_allowed();
		$migrated          = isset( $settings['__fa4_migrated']['icon_new'] );
		$is_new            = empty( $settings['icon'] ) && $migration_allowed;

		$video_link       = '';
		$video_html       = '';
		$embed_params     = [];
		$embed_options    = [];
		$lightbox_options = [];

		$video_link = $this->get_video_link();

		if ( empty( $video_link ) ) {
			return;
		}

		if ( 'hosted' === $settings['video_type'] ) {

			$video_html = $this->get_hosted_shortcode();

		} else {
			$embed_params = $this->get_embed_params();

			$embed_options = [
				'privacy' => $settings['youtube_privacy'],
				'lazy_load' => false,
			];

			$embed_attrs = [
				'allow' => 'autoplay',
			];

			$video_html = \Elementor\Embed::get_embed_html( $video_link, $embed_params, $embed_options, $embed_attrs );
		}

		if ( empty( $video_html ) ) {
			echo esc_url( $video_link );
			return;
		}

		$this->add_render_attribute( 'video', 'class', 'raven-video raven-video-' . ( $settings['use_lightbox'] ? 'lightbox' : 'inline' ) );

		if ( ! $settings['use_lightbox'] ) {
			$this->add_render_attribute( 'video', 'class', 'elementor-fit-aspect-ratio' );
		}
		?>
		<div class="raven-widget-wrapper">
			<?php if ( $settings['show_device_frame'] ) : ?>
				<div class="raven-frame raven-frame-<?php echo $settings['device_frame']; ?>">
					<div class="raven-frame-image">
						<?php include Utils::get_svg( 'frame-' . $settings['device_frame'] ); ?>
					</div>
			<?php endif; ?>

					<div <?php echo $this->get_render_attribute_string( 'video' ); ?>>
						<?php
						if ( ! $settings['use_lightbox'] && class_exists( '\Elementor\Utils' ) ) {
							\Elementor\Utils::print_unescaped_internal_string( $video_html ); // XSS ok.
						}

						if ( $this->has_image_overlay() ) {
							$this->add_render_attribute( 'image-overlay', 'class', 'raven-video-thumbnail' );

							if ( $settings['use_lightbox'] ) {
								$lightbox_options = $this->get_lightbox_options( $video_link, $video_html, $embed_params, $embed_options );

								$this->add_render_attribute( 'image-overlay', [
									'class' => 'elementor-clickable',
									'data-elementor-open-lightbox' => 'yes',
									'data-elementor-lightbox' => wp_json_encode( $lightbox_options ),
								] );
							} else {
								$this->add_render_attribute( 'image-overlay', 'style', 'background-image: url(' . $settings['image_overlay']['url'] . ');' );
							}
							?>
							<div <?php echo $this->get_render_attribute_string( 'image-overlay' ); ?>>
								<?php if ( $settings['use_lightbox'] ) : ?>
									<img class="raven-video-thumbnail-image" src="<?php echo $settings['image_overlay']['url']; ?>">
								<?php endif; ?>
								<?php if ( 'yes' === $settings['show_play_icon'] && ( ! empty( $settings['play_icon'] ) || $is_new || $migrated ) ) : ?>
									<div class="raven-video-play">
									<?php
									if ( $is_new || $migrated ) :
										Elementor::$instance->icons_manager->render_icon( $settings['play_icon_new'], [ 'aria-hidden' => 'true' ] );
									else :
										?>
										<i <?php echo $this->get_render_attribute_string( 'play_icon' ); ?>></i>
									<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						<?php } ?>
					</div>

			<?php if ( $settings['show_device_frame'] ) : ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	public function render_plain_content() {
		echo esc_url( $this->get_video_link() );
	}

	private function get_video_link() {
		$settings = $this->get_active_settings();
		$url      = '';

		switch ( $settings['video_type'] ) {
			case 'youtube':
				$url = $settings['youtube_link'];
				break;

			case 'vimeo':
				$url = $settings['vimeo_link'];
				break;

			case 'hosted':
				$url = $settings['hosted_link']['url'];
				break;
		}

		return $url;
	}

	private function get_embed_params() {
		$settings = $this->get_active_settings();
		$type     = $settings['video_type'];
		$options  = [ 'autoplay', 'loop', 'title', 'portrait', 'byline', 'rel', 'controls', 'showinfo', 'mute', 'muted' ];
		$params   = [];

		foreach ( $options as $option ) {
			if ( 'autoplay' === $option && $this->has_image_overlay() ) {
				$params['autoplay'] = '0';
				continue;
			}

			$key = $type . '_' . $option;

			if ( isset( $settings[ $key ] ) && ! is_null( $settings[ $key ] ) ) {
				$value             = ( 'yes' === $settings[ $key ] ) ? '1' : '0';
				$params[ $option ] = $value;
			}
		}

		if ( 'youtube' === $type ) {
			$params['wmode']       = 'opaque';
			$params['enablejsapi'] = '1';

			if ( $settings['youtube_loop'] ) {
				$video_properties = \Elementor\Embed::get_video_properties( $settings['youtube_link'] );

				$params['playlist'] = $video_properties['video_id'];
			}
		}

		if ( 'vimeo' === $type ) {
			$params['color']     = str_replace( '#', '', $settings['vimeo_color'] );
			$params['autopause'] = '0';
		}

		if ( 'hosted' === $type ) {
			$params['width']  = '0';
			$params['height'] = '0';
			$params['src']    = $settings['hosted_link']['url'];
			$params['webm']   = $settings['hosted_link_webm']['url'];
		}

		return $params;
	}

	private function get_hosted_shortcode() {
		$hosted_params = $this->get_embed_params();
		$params        = '';

		foreach ( $hosted_params as $param => $setting ) {
			if ( in_array( $param, [ 'src', 'webm' ], true ) ) {
				continue;
			}

			if ( empty( $setting ) ) {
				continue;
			}

			$params .= ' ' . $param;
		}

		return '<video ' . $params . '>
			<source src="' . $hosted_params['src'] . '" type="video/mp4">
			<source src="' . $hosted_params['webm'] . '" type="video/ogg">
			Your browser does not support the video tag.
		</video>';
	}

	private function get_lightbox_options( $video_link = '', $video_html = '', $embed_params = [], $embed_options = [] ) {
		$settings = $this->get_active_settings();
		$options  = [];

		if ( 'youtube' === $settings['video_type'] || 'vimeo' === $settings['video_type'] ) {
			$options = [
				'type' => 'video',
				'url' => \Elementor\Embed::get_embed_url( $video_link, $embed_params, $embed_options ),
				'modalOptions' => [
					'id' => 'elementor-lightbox-' . $this->get_id(),
					'videoAspectRatio' => $settings['video_aspect_ratio'],
				],
			];
		}

		if ( 'hosted' === $settings['video_type'] ) {
			$options = [
				'type' => 'html',
				'html' => '<div class="elementor-video-container elementor-lightbox-prevent-close elementor-aspect-ratio-' . $settings['video_aspect_ratio'] . '"><div class="elementor-fit-aspect-ratio">' . $video_html . '</div></div>',
				'modalOptions' => [
					'id' => 'elementor-lightbox-' . $this->get_id(),
				],
			];
		}

		return $options;
	}

	protected function has_image_overlay() {
		$settings = $this->get_settings_for_display();

		return ! empty( $settings['image_overlay']['url'] ) && 'yes' === $settings['show_image_overlay'];
	}
}
