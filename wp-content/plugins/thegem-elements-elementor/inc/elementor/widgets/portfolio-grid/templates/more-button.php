<?php
if (!defined('ABSPATH')) exit; ?>

<div <?php echo $this->get_render_attribute_string('attr_container'); ?>>

	<?php if ($separator_enabled) { ?>
	<div <?php echo $this->get_render_attribute_string('attr_separator'); ?>>
		<?php include 'more-button-separator.php'; ?>
		<div class="gem-button-separator-button">
			<?php } ?>

			<button <?php echo $this->get_render_attribute_string('button-wrap'); ?>>
				<span class="gem-inner-wrapper-btn">
					<?php if (!empty($settings['more_icon']['value'])) : ?>
						<span class="gem-button-icon">
							<?php \Elementor\Icons_Manager::render_icon($settings['more_icon'], ['aria-hidden' => 'true']); ?>
						</span>
					<?php endif; ?>
                	<span class="gem-text-button">
						<?php echo '<span>' . wp_kses($settings['more_button_text'], 'post') . '</span>'; ?>
					</span>
				</span>
			</button>

			<?php if ($separator_enabled) { ?>
		</div>
		<?php include 'more-button-separator.php'; ?>
	</div>
<?php } ?>

</div>

















