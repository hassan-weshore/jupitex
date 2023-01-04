<?php
if ( ! defined( 'ABSPATH' ) ) exit;

?>
	<div class="pricing-table <?php echo esc_attr('pricing-table-style-'.$preset); ?> button-icon-default">
		<div class="pricing-column-wrapper <?php if ( 'yes' === $settings['highlighted'] ) { echo "highlighted"; } ?>">
			
			<?php if ( 'yes' === $settings['content_label_show'] ) : ?>
				<div class="pricing-column-top-choice <?php echo esc_attr('label-align-' . $settings['style_label_3d_align']); ?>">
					<div <?php echo $this->get_render_attribute_string( 'content_label_text' ); ?>><?php if ( ! empty( $settings['content_label_text'] ) ) { echo wp_kses( $settings[ 'content_label_text' ], 'post' ); } ?></div>
				</div>
			<?php endif; ?>

			<div class="pricing-column">
				<div class="pricing-price-row ">

				<?php if ( 'yes' === $settings['content_pricing_show'] ) : ?>
					<div class="pricing-price-wrapper">
						<div class="pricing-price">
						<?php if ( ! empty( $settings['content_pricing_price'] ) ) : ?>
							<?php echo thegem_get_data( $settings, 'content_pricing_price', '', '<div ' . $this->get_render_attribute_string( 'content_pricing_price' ) . '>', '</div>' ); ?>
						<?php endif; ?>
						<?php if ( ! empty( $settings['content_pricing_period'] ) ) : ?>
							<?php echo thegem_get_data( $settings, 'content_pricing_period', '', '<div ' . $this->get_render_attribute_string( 'content_pricing_period' ) . '>', '</div>' ); ?>
						<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
				</div>

				<?php if ( 'yes' === $settings['content_header_show'] ) : ?>
					<div class="pricing-row pricing-row-title">
						<?php if ( ! empty( $settings['content_header_title'] ) ) : ?>
							<?php echo thegem_get_data( $settings, 'content_header_title', '', '<div class="pricing_row_title"><span ' .  $this->get_render_attribute_string( 'content_header_title' )  . '>', '</span></div>' ); ?>
						<?php endif; ?>

					</div>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['content_features_show'] ) : ?>
					<div>
				<?php foreach ( $settings['features_list'] as $index => $item ) : 
					$repeater_setting_key = $this->get_repeater_setting_key( 'feature_text', 'features_list', $index );
					$this->add_inline_editing_attributes( $repeater_setting_key ); ?>
					<?php if ( ! empty( $item['feature_text'] ) ) : ?>
					<figure class="pricing-row <?php if ( 'yes' === $item['feature_na'] ) { echo "strike"; } ?>">
						<?php if ( ! empty( $settings['style_feature_icon']['value'] ) ) { \Elementor\Icons_Manager::render_icon( $settings['style_feature_icon'], [ 'aria-hidden' => 'true' ] ); }?>
						<span <?php echo $this->get_render_attribute_string( $repeater_setting_key ); ?>>
							<?php echo wp_kses( $item[ 'feature_text' ], 'post' ); ?>
						</span>
					</figure>
					<?php
						else :
							echo '&nbsp;';
					endif;?>
				<?php endforeach; ?>
				</div>
				<?php endif; ?>
				
				<?php if ( 'yes' === $settings['content_footer_show'] ) : ?>
				<div class="pricing-footer">
					<div class="gem-button-container gem-button-position-center">
						<a class="gem-button gem-button-text-weight-normal <?php if ( !empty( $settings['style_footer_button_icon']['value'] )) 
						{ echo ( 'right' === $settings['style_footer_button_iconalign'] ) ? ' gem-button-icon-position-right ' : ' gem-button-icon-position-left ';}
						else { echo ' gem-button-icon-position-left ' ;} ?>
						<?php if ( !empty( $settings['style_footer_button_type'] )) {
							echo $settings['style_footer_button_type'];
						} else{
							echo ' gem-button-style-flat ';
						} if ( !empty( $settings['style_footer_button_size'] )) {
							echo $settings['style_footer_button_size'];
						} else {
							echo ' gem-button-size-small ';
						} ?>" 
					href="<?php if ( ! empty( $settings['content_footer_button_link'] ) ) : echo $settings['content_footer_button_link']['url']; ?>" 
					rel="<?php if ( ! empty( $settings['content_footer_button_link']['is_external'] ) ) { echo 'nofollow'; } ?>" 
					target="<?php if ( ! empty( $settings['content_footer_button_link']['nofollow'] ) ) { echo '_blank'; } endif;?>">
					
					<?php if ( 'right' === $settings['style_footer_button_iconalign']  ) { echo '<span ' . $this->get_render_attribute_string( 'content_footer_button_text' ) .'>' . wp_kses( $settings[ 'content_footer_button_text' ], 'post' ) . '</span>'; } ?>
					<?php if ( ! empty( $settings['style_footer_button_icon']['value'] ) ) { \Elementor\Icons_Manager::render_icon( $settings['style_footer_button_icon'], [ 'aria-hidden' => 'true' ] ); }?>
					<?php if ( 'left' === $settings['style_footer_button_iconalign']  ) { echo '<span ' . $this->get_render_attribute_string( 'content_footer_button_text' ) .'>' . wp_kses( $settings[ 'content_footer_button_text' ], 'post' ) . '</span>'; } ?>
					</a>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>