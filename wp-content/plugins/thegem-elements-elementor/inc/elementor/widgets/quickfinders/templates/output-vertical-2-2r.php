<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! empty( $settings['thegem_elementor_preset'] ) ) {
	if ( 'vertical-2' === $settings['thegem_elementor_preset']  ) {
		$this->add_render_attribute( 'main_wrap', [
			'class' => [ 'quickfinder', 'quickfinder-style-vertical', 'bubbles', 'quickfinder-alignment-left', 'quickfinder-style-' . esc_attr( $settings['thegem_elementor_preset'] ) ],
			]
		);
	}
	if ( 'vertical-2-right-align' === $settings['thegem_elementor_preset']  ) {
		$this->add_render_attribute( 'main_wrap', [
			'class' => [ 'quickfinder', 'quickfinder-style-vertical', 'bubbles', 'quickfinder-style-vertical-2', 'quickfinder-alignment-right' ],
			]
		);
	}
}
$quickfinder_item_rotation = 'odd';
?>

<div <?php echo $this->get_render_attribute_string( 'main_wrap' ); ?>>
	<?php foreach ( $settings['qf_list'] as $index => $item ) :
		$quickfinder_item_rotation = $quickfinder_item_rotation == 'odd' ? 'even' : 'odd';
	?>
		<div class="quickfinder-item odd<?php echo ('yes' === $settings['lazy'] ? ' lazy-loading' : ''); ?>">

			<?php if ( 'vertical-2-right-align' === $settings['thegem_elementor_preset']  ) : ?>
				<div class="quickfinder-item-info-wrapper">
					<?php if ( ! empty( $qf_item_info ) && file_exists( $qf_item_info ) ) : include $qf_item_info; endif; ?>

					<svg <?php echo $this->get_render_attribute_string( 'qf_svg_arrow_right' ); ?>>
							<use xlink:href="<?php echo get_template_directory_uri() . '/css/post-arrow.svg' ?>#dec-post-arrow"></use>
					</svg>

				</div>
			<?php endif; ?>	

			<?php if ( ! empty( $item['qf_item_icon_image_select'] )) : ?>
				<?php if ( ! empty( $qf_icon_image ) && file_exists( $qf_icon_image ) ) : include $qf_icon_image; endif; ?>
			<?php endif; ?>	

			<?php if ( 'vertical-2' === $settings['thegem_elementor_preset']  ) : ?>
				<div class="quickfinder-item-info-wrapper"> 

					<svg <?php echo $this->get_render_attribute_string( 'qf_svg_arrow_left' ); ?>>
						<use xlink:href="<?php echo get_template_directory_uri() . '/css/post-arrow.svg' ?>#dec-post-arrow"></use>
					</svg>

					<?php if ( ! empty( $qf_item_info ) && file_exists( $qf_item_info ) ) : include $qf_item_info; endif; ?>
					
				</div>
			<?php endif; ?>	
			<?php if ( ! empty( $qf_item_link ) && file_exists( $qf_item_link) ) : include $qf_item_link; endif; ?>
		</div>
	<?php endforeach; ?>
</div>