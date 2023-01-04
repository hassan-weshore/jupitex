<?php if (!defined('ABSPATH')) exit; ?>


<?php $fullwidth = ! empty( $settings['stretch_full_width'] ) ? 'fullwidth-block' : ''; ?>

<div class="gem-clients gem_client-carousel <?php echo $fullwidth; ?> <?php echo ( 'yes' === $settings['lazy_loading'] ) ? 'lazy-loading' : ''; ?>" data-ll-item-delay="0">
	<div class="gem_client_carousel-items" data-autoscroll="<?php echo ! empty( $settings['autoscroll_speed'] ) ?  esc_attr( $settings['autoscroll_speed'] ) : 0; ?>">
		<div class="gem-client-carousel-item-wrap">
			<div class="gem-client-carousel">
				<?php $this->show_carousel_clients( $settings ); ?>
			</div>
			<?php if( 'yes' === $settings['navigation_arrows'] ): ?>
				<div class="gem-client-carousel-navigation">
					<a href="#" class="gem-prev gem-client-prev"/></a>
					<a href="#" class="gem-next gem-client-next"/></a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

















