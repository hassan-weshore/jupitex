<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="diagram-item">
	<div <?php echo $this->get_render_attribute_string( $diagram_wrapper ); ?>>
		<div <?php echo $this->get_render_attribute_string( $lazy_wrapper ) ?>>
			<div <?php echo $this->get_render_attribute_string( $lazy_item ) ?>>
				<?php foreach ( $skills as $i => $skill ):
					$item = 'skill-element-' . $i;
					$this->add_render_attribute( $item, 'class', [
						'skill-element',
						'elementor-repeater-item-' . $skill['_id']
					] );
					?>
					<div <?php echo $this->get_render_attribute_string( $item ); ?>>
						<div class="skill-title">
							<span class="diagram-skill-title"><?php echo $skill['content_items_skill']; ?></span>
							<?php if ( $settings['content_style'] === 'style-3' && $settings['show_percentage'] === 'yes' ) : ?>
								<span class="diagram-skill-amount"><?php echo esc_html($skill['content_items_level']['size']); ?>%</span>
							<?php endif; ?>
						</div>
						<div class="clearfix">
							<?php if ( $settings['show_percentage'] === 'yes' ) : ?>
								<div class="skill-amount diagram-skill-amount"><?php echo $skill['content_items_level']['size'] ?>%</div>
							<?php endif ?>
							<div class="skill-line">
								<div data-amount="<?php echo $skill['content_items_level']['size'] ?>" style="width: 0;"></div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
