<?php if (!defined('ABSPATH')) exit; ?>

<div <?php echo $this->get_render_attribute_string( 'loadmore_button_container' ); ?>>

	<?php if( $separator_enabled ) : ?>
		<div <?php echo $this->get_render_attribute_string( 'attr_separator' ); ?>>
			<?php include __DIR__ . '/bl_button_separator.php'; ?>
			<div class="gem-button-separator-button">
	<?php endif; ?>
	<a <?php echo $this->get_render_attribute_string( 'loadmore_button' ); ?>>
		<span class="gem-inner-wrapper-btn">
			<?php if(!empty($settings['loadmore_button_icon']['value'])) : ?>
				<span class="gem-button-icon">
					<?php \Elementor\Icons_Manager::render_icon( $settings['loadmore_button_icon'], [ 'aria-hidden' => 'true' ] ); ?>
				</span>
			<?php endif; ?>
			<span <?php echo $this->get_render_attribute_string( 'loadmore_button_text' ); ?>>
				<?php echo wp_kses( $settings[ 'loadmore_button_text' ], 'post' ); ?>
			</span>
		</span>
	</a>

	<?php if( $separator_enabled ) : ?>
			</div>
			<?php include __DIR__ . '/bl_button_separator.php'; ?>
		</div>
	<?php endif; ?>

</div>