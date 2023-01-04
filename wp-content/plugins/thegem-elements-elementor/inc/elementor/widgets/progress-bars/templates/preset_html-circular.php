<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div <?php echo $this->get_render_attribute_string( $diagram_wrapper ) ?>>
	<div class="box-wrapper">
		<div class="box"></div>
	</div>
	<div class="skills">
		<?php foreach ( $skills as $i => $skill ):
			$item = 'skill-arc-' . $i;
			$this->add_render_attribute( $item, 'class', [
				'skill-arc',
				'elementor-repeater-item-' . $skill['_id']
			] );
			if ( 'yes' === $skill['content_items_want_customize'] ) {
				$this->add_render_attribute( $item, 'data-base-color', [ $skill['content_items_base_color'] ] );
			}

			$level_color   = 'yes' === $skill['content_items_want_customize'] ? $skill['content_items_level_color'] : $settings['bars_level_color'];
			$name_color    = 'yes' === $skill['content_items_want_customize'] ? $skill['content_items_name_color'] : $settings['name_style_color'];
			$percent_color = 'yes' === $skill['content_items_want_customize'] ? $skill['content_items_percentage_color'] : $settings['percentage_style_color'];

			?>
			<div <?php echo $this->get_render_attribute_string( $item ) ?>>
				<span class="title"><?php echo $skill['content_items_skill']; ?></span>
				<input type="hidden" class="percent" value="<?php echo esc_attr( $skill['content_items_level']['size'] ); ?>"/>
				<input type="hidden" class="color" value="<?php echo esc_attr( $level_color ); ?>"/>
				<input type="hidden" class="title_color" value="<?php echo esc_attr( $name_color ); ?>"/>
				<input type="hidden" class="percent_color" value="<?php echo esc_attr( $percent_color ); ?>"/>
			</div>
		<?php endforeach; ?>
	</div>
</div>
