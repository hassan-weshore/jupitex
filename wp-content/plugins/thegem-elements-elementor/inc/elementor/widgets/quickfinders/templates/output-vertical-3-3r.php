<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! empty( $settings['thegem_elementor_preset'] ) ) {
	if ( 'vertical-3' === $settings['thegem_elementor_preset']  ) {
		$this->add_render_attribute( 'main_wrap', [
			'class' => [ 'quickfinder', 'quickfinder-style-vertical', 'basic', 'quickfinder-alignment-left', 'quickfinder-style-' . esc_attr( $settings['thegem_elementor_preset'] ) ],
			]
		);
	}
	if ( 'vertical-3-right-align' === $settings['thegem_elementor_preset']  ) {
		$this->add_render_attribute( 'main_wrap', [
			'class' => [ 'quickfinder', 'quickfinder-style-vertical', 'basic', 'quickfinder-style-vertical-3', 'quickfinder-alignment-right' ],
			]
		);
	}
}
?>

<div <?php echo $this->get_render_attribute_string( 'main_wrap' ); ?>>
	<?php foreach ( $settings['qf_list'] as $index => $item ) :	?>
		<div class="quickfinder-item odd<?php echo ('yes' === $settings['lazy'] ? ' lazy-loading' : ''); ?>">

			<?php if ( 'vertical-3' === $settings['thegem_elementor_preset']  ) : ?>
				<?php if ( ! empty( $item['qf_item_icon_image_select'] )) : ?>
					<?php if ( ! empty( $qf_icon_image ) && file_exists( $qf_icon_image ) ) : include $qf_icon_image; endif; ?>
				<?php endif; ?>
			<?php endif; ?>

			<div class="quickfinder-item-info-wrapper"> 
				<?php if ( ! empty( $qf_item_info ) && file_exists( $qf_item_info ) ) : include $qf_item_info; endif; ?>
			</div>
			<?php if ( ! empty( $qf_item_link ) && file_exists( $qf_item_link) ) : include $qf_item_link; endif; ?>

			<?php if ( 'vertical-3-right-align' === $settings['thegem_elementor_preset']  ) : ?>
				<?php if ( ! empty( $item['qf_item_icon_image_select'] )) : ?>
					<?php if ( ! empty( $qf_icon_image ) && file_exists( $qf_icon_image ) ) : include $qf_icon_image; endif; ?>
				<?php endif; ?>
			<?php endif; ?>

		</div>
	<?php endforeach; ?>
</div>