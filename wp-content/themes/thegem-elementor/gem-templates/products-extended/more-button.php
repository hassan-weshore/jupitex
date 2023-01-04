<?php
if (!defined('ABSPATH')) exit; ?>

<div class="<?php echo $classes_container; ?>">

	<?php if ($separator_enabled) { ?>
	<div class="<?php echo $classes_separator; ?>">
		<?php include 'more-button-separator.php'; ?>
		<div class="gem-button-separator-button">
			<?php } ?>

			<button class="<?php echo $classes_button; ?>">
				<span class="gem-inner-wrapper-btn">
					<?php if (!empty($params['more_icon']['value'])) : ?>
						<span class="gem-button-icon">
							<?php \Elementor\Icons_Manager::render_icon($params['more_icon'], ['aria-hidden' => 'true']); ?>
						</span>
					<?php endif; ?>
                	<span class="gem-text-button">
						<?php echo '<span>' . wp_kses($params['more_button_text'], 'post') . '</span>'; ?>
					</span>
				</span>
			</button>

			<?php if ($separator_enabled) { ?>
		</div>
		<?php include 'more-button-separator.php'; ?>
	</div>
<?php } ?>

</div>

















