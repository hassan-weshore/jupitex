<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;

?>
<?php if ( ! empty( $settings['icon_image_select'] ) ) : ?>
	<div <?php echo $this->get_render_attribute_string( 'cta_icon_image' ); ?>>
		<?php if ( 'image' === $settings['icon_image_select'] ) : ?>
			<div class="gem-image">
				<span>
					<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'graphic_image' ); ?>
				</span>
			</div>


		<?php elseif ( 'icon' === $settings['icon_image_select'] && ( ! empty( $settings['icon'] ) || ! empty( $settings['selected_icon'] ) ) ) : ?>

			<div class="gem-icon gem-icon-pack-fontawesome gem-icon-size-<?php echo $settings['icon_size']; ?> <?php echo esc_attr( $settings['icon_color_split'] ); ?> gem-icon-shape-<?php echo esc_attr( $settings['icon_shape'] ); ?>">

				<?php if ( 'hexagon' === $settings['icon_shape'] ) : ?>
					<div class="gem-icon-shape-hexagon-back">
						<div class="gem-icon-shape-hexagon-back-inner">
							<div class="gem-icon-shape-hexagon-back-inner-before"></div>
						</div>
					</div>

					<div class="gem-icon-shape-hexagon-top">
						<div class="gem-icon-shape-hexagon-top-inner">
							<div class="gem-icon-shape-hexagon-top-inner-before"></div>
						</div>
					</div>
				<?php endif; ?>

				<div class="icon-hover-bg <?php echo esc_attr ( ! empty( $settings['icon_hover_effect'] ) ? $settings['icon_hover_effect'] : 'fade' ); ?>"></div>

				<div class="gem-icon-inner">
					<?php if ( 'romb' === $settings['icon_shape'] ) : ?>
					<div class="romb-icon-conteiner">
						<?php endif; ?>

						<?php if ( 'hexagon' !== $settings['icon_shape'] ) : ?>
						<div class="padding-box-inner">
							<?php endif; ?>

							<?php if ( 'gradient' === $settings['icon_color_split'] ) { ?>
								<span class="gem-icon-style-gradient">
									<span class="back-angle">
										<?php if ( ! empty( $settings['selected_icon'] ) ) { \Elementor\Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); } ?>
									</span>
								</span>
								<?php } else { ?>
								<span class="gem-icon-half-1">
									<span class="back-angle">
										<?php if ( ! empty( $settings['selected_icon'] ) ) { \Elementor\Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); } ?>
									</span>
								</span>
								<span class="gem-icon-half-2">
									<span class="back-angle">
										<?php if ( ! empty( $settings['selected_icon'] ) ) { \Elementor\Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); } ?>
									</span>
								</span>
							<?php } ?>

						</div>

						<?php if ( 'romb' === $settings['icon_shape'] ) : ?>
					</div>
					<?php endif; ?>

					<?php if ( 'hexagon' !== $settings['icon_shape'] ) : ?>
				</div>
				<?php endif; ?>

			</div>

		<?php endif; ?>

		<?php
		if ( $link_icon_image ) :

			$this->add_link_attributes( 'link', $link_icon_image );
			$this->add_render_attribute( 'link', 'class', 'gem-alert-box-icon-image-link' ); ?>
			<a <?php echo ( $this->get_render_attribute_string( 'link' ) ) ?>></a>

		<?php endif; ?>
	</div>
<?php endif; ?>