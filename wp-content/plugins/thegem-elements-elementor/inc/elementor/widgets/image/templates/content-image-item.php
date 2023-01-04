<?php

use Elementor\Control_Media;
use Elementor\Icons_Manager;
?>

<div class="styled-image-wrapper">
	<?php
	if ('centered' === $settings['image_position']) : ?>
	<div class="centered-box gem-image-centered-box">
		<?php endif; ?>
		<div <?php echo $this->get_render_attribute_string('main-wrap'); ?>>
			<div class="gem-wrapbox-inner <?php echo('yes' === $settings['lazy'] ? ' lazy-loading-item' : ''); ?>" <?php echo('yes' === $settings['lazy'] ? ' data-ll-effect="move-up"' : ''); ?>>
				<?php if ($link) : ?>
				<a <?php echo($this->get_render_attribute_string('link')); ?>>
					<?php endif; ?>
					<img class="gem-wrapbox-element img-responsive <?php echo esc_attr('style-11' === $settings['thegem_elementor_preset'] ? ' img-circle' : ''); ?>"
						 src="<?php echo esc_url($image_url); ?>"
						 alt="<?php echo Control_Media::get_image_alt($settings['image']); ?>">
					<?php if ($link) : ?>
					<?php if (!empty($settings['icon']['value'])) {
						Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);
					} ?>
				</a>
			<?php endif; ?>
			</div>
		</div>
		<?php if ('centered' === $settings['image_position']) : ?>
	</div>
<?php endif; ?>
</div>
