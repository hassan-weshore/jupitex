<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( 'iconed' === $settings['thegem_elementor_preset'] ) {
	$this->add_render_attribute( 'qf_content', 'class', 'quickfinder-item-box' );
}
?>

<div <?php echo $this->get_render_attribute_string( 'main_wrap' ); ?>>
	<?php foreach ( $settings['qf_list'] as $index => $item ) : ?>
		<div <?php echo $this->get_render_attribute_string( 'qf_item' ); ?>>

			<?php if ( 'iconed' === $settings['thegem_elementor_preset'] ) : ?>
				<div <?php echo $this->get_render_attribute_string( 'qf_content' ); ?>>
			<?php endif; ?>

				<div <?php echo $this->get_render_attribute_string( 'qf_item_inner' ); ?>> 

					<?php if ( 'icon' === $item['qf_item_icon_image_select'] && ( ! empty( $item['icon'] ) || ! empty( $item['qf_item_selected_icon'] ) ) || 'image' === $item['qf_item_icon_image_select'] ) : ?>
						<?php if ( ! empty( $qf_icon_image ) && file_exists( $qf_icon_image ) ) : include $qf_icon_image; endif; ?>
					<?php endif; ?>

					<div class="quickfinder-item-info-wrapper"> 
						<?php if ( ! empty( $qf_item_info ) && file_exists( $qf_item_info ) ) : include $qf_item_info; endif; ?>
					</div>

					<?php if ( ! empty( $qf_item_link ) && file_exists( $qf_item_link ) ) : include $qf_item_link; endif; ?>

				</div>

			<?php if ( 'iconed' === $settings['thegem_elementor_preset'] ) : ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
