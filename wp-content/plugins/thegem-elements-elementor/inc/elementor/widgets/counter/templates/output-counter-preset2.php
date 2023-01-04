<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div data-number-format="<?php echo esc_attr($settings['numbers_format' ]); ?>" class="gem-counter-box">
	<div class="gem-counter">
		<div class="gem-counter-inner">
			<?php if(!empty($settings['counter_icon']['value'])) : ?>
				<div class="gem-counter-icon">
					<div class="gem-counter-icon-circle-1 bordred-box">
						<div class="gem-counter-icon-circle-2 bordred-box">
							<div class="gem-icon gem-icon-pack-material gem-icon-size-medium  gem-icon-shape-circle gem-simple-icon <?php echo ('yes' === $settings['counter_animation_enabled'] ? ' lazy-loading-item' : ''); ?> <?php echo ('yes' === $settings['counter_animation_enabled'] ? ' lazy-loading-item-fading' : ''); ?>">
								<div class="gem-icon-inner">
									<?php \Elementor\Icons_Manager::render_icon($settings['counter_icon'], ['aria-hidden' => 'true']); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<div class="gem-counter-number thin-font">
				<div class="gem-counter-odometer odometer odometer-auto-theme" data-to="<?php echo esc_attr($settings['counter_ending_number' ]); ?>">
					<?php if ( 'yes' !== $settings['counter_animation_enabled'] ) : ?>
						<?php echo $settings['counter_ending_number' ]; ?>
					<?php else: ?>
						<?php echo $settings['counter_starting_number' ]; ?>
					<?php endif; ?>
				</div>

				<?php if( ! empty($settings['counter_number_suffix' ]) ):?>
					<div class="gem-counter-suffix <?php echo ('yes' === $settings['counter_spacing_suffix'])?'gem-counter-suffix-spacing':''?>">
						<?php echo esc_html($settings['counter_number_suffix' ]); ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="gem-counter-text styled-subtitle">
				<?php echo ( ! empty($settings['counter_description' ]) ) ? esc_html($settings['counter_description' ]) : 'Counters'; ?>
			</div>
		</div>
	</div>
	<?php
		$link = $this->get_link_url( $settings );
		if ( $link ) :
			$this->add_link_attributes( 'link', $link );
			$this->add_render_attribute( 'link', 'class', 'gem-counter-link' );
		?>

			<a <?php echo ($this->get_render_attribute_string( 'link' )) ?>></a>

	<?php endif; ?>
</div>