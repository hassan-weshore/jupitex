<?php if (!defined('ABSPATH')) exit; ?>

<div class="gem-clients gem-clients-type-carousel-grid <?php echo ( 'yes' === $settings['lazy_loading'] ? 'lazy-loading' : '' ) ?>" data-ll-item-delay="0" data-autoscroll="<?php echo ! empty( $settings['autoscroll_speed'] ) ?  esc_attr( $settings['autoscroll_speed'] ) : 0; ?>">
	<div class="gem-clients-grid-carousel-wrap">
		<div class="gem-clients-grid-carousel">
			<?php $this->show_grid_clients( $settings ); ?>
		</div>
		<?php if( 'yes' === $settings['navigation_dots'] ): ?>
			<div class="gem-clients-grid-pagination gem-mini-pagination"></div>
		<?php endif; ?>
	</div>
</div>