<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
?>
<div class="quickfinder-icon-container">

	<div class="connector-container">
		<span></span>
	</div>

	<div <?php echo $this->get_render_attribute_string( 'qf_icon_image' ); ?>>
		<?php if ( 'image' === $item['qf_item_icon_image_select'] ) : ?>
			<div class="gem-image">
				<span>
					<?php echo Group_Control_Image_Size::get_attachment_image_html( $item, 'qf_item_graphic_image' ); ?>
				</span>
			</div>


		<?php elseif ( 'icon' === $item['qf_item_icon_image_select'] && ( ! empty( $item['icon'] ) || ! empty( $item['qf_item_selected_icon'] ) ) ) : ?>

			<div class="gem-icon gem-icon-pack-fontawesome gem-icon-size-<?php echo $settings['icon_size']; ?> <?php echo esc_attr( $settings['icon_color_split'] ); ?> gem-icon-shape-<?php echo esc_attr( $settings['icon_shape'] ); ?><?php if(in_array( $settings['thegem_elementor_preset'], array('iconed') )) { echo ' bordered-box'; }; ?>">

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
										<?php if ( ! empty( $item['qf_item_selected_icon'] ) ) { \Elementor\Icons_Manager::render_icon( $item['qf_item_selected_icon'], [ 'aria-hidden' => 'true' ] ); } ?>
									</span>
								</span>
								<?php } else { ?>
								<span class="gem-icon-half-1">
									<span class="back-angle">
										<?php if ( ! empty( $item['qf_item_selected_icon'] ) ) { \Elementor\Icons_Manager::render_icon( $item['qf_item_selected_icon'], [ 'aria-hidden' => 'true' ] ); } ?>
									</span>
								</span>
								<span class="gem-icon-half-2">
									<span class="back-angle">
										<?php if ( ! empty( $item['qf_item_selected_icon'] ) ) { \Elementor\Icons_Manager::render_icon( $item['qf_item_selected_icon'], [ 'aria-hidden' => 'true' ] ); } ?>
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

		<?php if ( 'custom' === $item['qf_item_icon_image_link_to'] || 'file' === $item['qf_item_icon_image_link_to'] && empty( $item['qf_item_link']['url'] )) :
			$link_image_key = 'link_' . $index;
			$this->add_render_attribute( $link_image_key, 'class', 'quickfinder-item-link' );
			if ( 'custom' === $item['qf_item_icon_image_link_to'] ) {
				$this->add_link_attributes( $link_image_key, $item['qf_item_icon_image_link'] );
			} 
			if ( 'file' === $item['qf_item_icon_image_link_to'] && 'image' === $item['qf_item_icon_image_select']) {
				$this->add_link_attributes( $link_image_key, $item['qf_item_graphic_image'] );
			} ?>
			<a <?php echo $this->get_render_attribute_string( $link_image_key ); ?>></a>
		<?php endif; ?>
	</div>

	<div class="connector-container">
		<span></span>
	</div>

</div>

