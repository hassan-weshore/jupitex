<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! empty( $settings['thegem_elementor_preset'] ) ) {
	if ( 'vertical-1' === $settings['thegem_elementor_preset']  ) {
		$this->add_render_attribute( 'main_wrap', [
			'class' => [ 'quickfinder', 'quickfinder-style-vertical', 'bubbles', 'quickfinder-style-' . esc_attr( $settings['thegem_elementor_preset'] ) ],
			]
		);
	}
	if ( 'vertical-4' === $settings['thegem_elementor_preset']  ) {
		$this->add_render_attribute( 'main_wrap', [
			'class' => [ 'quickfinder', 'quickfinder-style-vertical', 'basic', 'quickfinder-style-' . esc_attr( $settings['thegem_elementor_preset'] ) ],
			]
		);
	}
}

$this->add_render_attribute( 'qf_item_info', 'class', $settings['content_position_vertical'] );

$quickfinder_item_rotation = 'even';
?>

<div <?php echo $this->get_render_attribute_string( 'main_wrap' ); ?>>
	<?php foreach ( $settings['qf_list'] as $index => $item ) :
		$quickfinder_item_rotation = $quickfinder_item_rotation == 'odd' ? 'even' : 'odd';
	?>
		<div class="quickfinder-item <?php echo esc_attr ( $quickfinder_item_rotation ); ?><?php echo ('yes' === $settings['lazy'] ? ' lazy-loading' : ''); ?>">
			<?php if( $quickfinder_item_rotation == 'odd' ) : ?>
				<div class="quickfinder-item-info-wrapper"> 

					<?php if ( 'vertical-1' === $settings['thegem_elementor_preset']  ) : ?>
						<svg <?php echo $this->get_render_attribute_string( 'qf_svg_arrow_right' ); ?>>
							<use xlink:href="<?php echo get_template_directory_uri() . '/css/post-arrow.svg' ?>#dec-post-arrow"></use>
						</svg>
					<?php endif; ?>

					<?php if ( ! empty( $qf_item_info ) && file_exists( $qf_item_info ) ) : include $qf_item_info; endif; ?>

				</div>

				<?php if ( ! empty( $qf_item_link ) && file_exists( $qf_item_link ) ) : include $qf_item_link; endif; ?>

			<?php endif; ?>

			<?php if ( ! empty( $item['qf_item_icon_image_select'] )) : ?>
				<?php if ( ! empty( $qf_icon_image ) && file_exists( $qf_icon_image ) ) : include $qf_icon_image; endif; ?>
			<?php endif; ?>

			<?php if( $quickfinder_item_rotation == 'even' ) : ?>
				<div class="quickfinder-item-info-wrapper"> 
				
					<?php if ( 'vertical-1' === $settings['thegem_elementor_preset']  ) : ?>
						<svg <?php echo $this->get_render_attribute_string( 'qf_svg_arrow_left' ); ?>>
							<use xlink:href="<?php echo get_template_directory_uri() . '/css/post-arrow.svg' ?>#dec-post-arrow"></use>
						</svg>
					<?php endif; ?>

					<?php if ( ! empty( $qf_item_info ) && file_exists( $qf_item_info ) ) : include $qf_item_info; endif; ?>
				</div>
				<?php if ( ! empty( $qf_item_link ) && file_exists( $qf_item_link ) ) : include $qf_item_link; endif; ?>

			<?php endif; ?>

		</div>
	<?php endforeach; ?>
</div>