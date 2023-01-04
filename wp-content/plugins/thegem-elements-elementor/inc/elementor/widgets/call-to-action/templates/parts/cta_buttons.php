<?php
if ( ! defined( 'ABSPATH' ) ) exit;

?>

			<?php if ( 'yes' === ( $settings['show_button_1'] ) ) : ?>
				<?php
					wp_enqueue_style( 'thegem-button' );

					$this->add_render_attribute( 'button_container_1', 'class', ['gem-button-container-1', 'gem-widget-button'] );

					$this->add_render_attribute( 'button_1_text', 'class', 'gem-text-button' );
					$this->add_inline_editing_attributes( 'button_1_text', 'none' );

					$this->add_render_attribute( 'button_1', 'class', ['gem-button', 'gem-button-size-'.esc_attr( $settings['button_1_size'] ), 'gem-button-text-weight-'.esc_attr( $settings['button_1_text_weight'] ) ] );

					if( ! empty( $settings['button_1_icon_align'] ) && $settings['button_1_icon_align'] === 'right' ) {
						$this->add_render_attribute( 'button_1', 'class', 'gem-button-icon-position-right' );
					}
					if( ! empty( $settings['button_1_type'] ) ) {
						$this->add_render_attribute( 'button_1', 'class', 'gem-button-style-'.$settings['button_1_type'] );
					}
				?>
				<div <?php echo $this->get_render_attribute_string( 'button_container_1' ); ?>>
					<?php        
					if ( empty( $container_link ) && !  empty( $settings['button_1_link']['url'] ) ) {

						if ( $settings['button_1_link']['is_external'] ) {
							$this->add_render_attribute( 'button_1', 'target', '_blank' );
						}

						if ( $settings['button_1_link']['nofollow'] ) {
							$this->add_render_attribute( 'button_1', 'rel', 'nofollow' );
						}

						$this->add_render_attribute( 'button_1', 'href', $settings['button_1_link']['url'] );
					}
					?>
					<a <?php echo $this->get_render_attribute_string( 'button_1' ); ?>>
						<span class="gem-inner-wrapper-btn">
							<?php if(!empty($settings['button_1_icon']['value'])) : ?>
								<span class="gem-button-icon">
									<?php \Elementor\Icons_Manager::render_icon( $settings['button_1_icon'], [ 'aria-hidden' => 'true' ] ); ?>
								</span>
							<?php endif; ?>
							<span <?php echo $this->get_render_attribute_string( 'button_1_text' ); ?>>
								<?php echo wp_kses( $settings[ 'button_1_text' ], 'post' ); ?>
							</span>
						</span>
					</a>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === ( $settings['show_button_2'] ) ) : ?>
				<?php
					$this->add_render_attribute( 'button_container_2', 'class', ['gem-button-container-2', 'gem-widget-button'] );

					$this->add_render_attribute( 'button_2_text', 'class', 'gem-text-button' );
					$this->add_inline_editing_attributes( 'button_2_text', 'none' );

					$this->add_render_attribute( 'button_2', 'class', ['gem-button', 'gem-button-size-'.esc_attr( $settings['button_2_size'] ), 'gem-button-text-weight-'.esc_attr( $settings['button_2_text_weight'] ) ] );

					if( ! empty( $settings['button_2_icon_align'] ) && $settings['button_2_icon_align'] === 'right' ) {
						$this->add_render_attribute( 'button_2', 'class', 'gem-button-icon-position-right' );
					}
					if( ! empty( $settings['button_2_type'] ) ) {
						$this->add_render_attribute( 'button_2', 'class', 'gem-button-style-'.$settings['button_2_type'] );
					}
				?>
				<div <?php echo $this->get_render_attribute_string( 'button_container_2' ); ?>>
					<?php        
					if ( empty( $container_link ) && !  empty( $settings['button_2_link']['url'] ) ) {

						if ( $settings['button_2_link']['is_external'] ) {
							$this->add_render_attribute( 'button_2', 'target', '_blank' );
						}

						if ( $settings['button_2_link']['nofollow'] ) {
							$this->add_render_attribute( 'button_2', 'rel', 'nofollow' );
						}

						$this->add_render_attribute( 'button_2', 'href', $settings['button_2_link']['url'] );
					}
					?>
					<a <?php echo $this->get_render_attribute_string( 'button_2' ); ?>>
						<span class="gem-inner-wrapper-btn">
							<?php if(!empty($settings['button_2_icon']['value'])) : ?>
								<span class="gem-button-icon">
									<?php \Elementor\Icons_Manager::render_icon( $settings['button_2_icon'], [ 'aria-hidden' => 'true' ] ); ?>
								</span>
							<?php endif; ?>
							<span <?php echo $this->get_render_attribute_string( 'button_2_text' ); ?>>
								<?php echo wp_kses( $settings[ 'button_2_text' ], 'post' ); ?>
							</span>
						</span>
					</a>
				</div>
			<?php endif; ?>