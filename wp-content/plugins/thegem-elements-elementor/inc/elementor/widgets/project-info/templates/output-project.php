<?php if ( ! defined('ABSPATH') ) exit; ?>

<div class="gem-project-info <?php echo $gem_gaps_container; ?> <?php echo $class_project_style; ?>">

	<?php foreach ($settings['pi_list_items'] as $index => $item ): ?>

		<div class="elementor-repeater-item-<?php echo $item['_id'] ?> gem-project-info-item <?php echo $gem_gaps; ?>">

			<div class="icon <?php echo ( ! empty( $item['pi_icon']['value']['url'] ) ? 'gem-svg-icon' : '' ); ?>">
				<?php Elementor\Icons_Manager::render_icon( $item['pi_icon'], [ 'aria-hidden' => 'true' ] ) ?>
			</div>

			<div class="gem-wrapper-project-info">
			<?php
				if ( ! empty( $item['pi_item_title'] ) ) :
					$tag_h = $item["pi_item_title_html_tag"];
					$weight_title = $item["pi_item_title_html_tag_weight"];

					$title_setting_key = $this->get_repeater_setting_key( 'pi_item_title', 'pi_list_items', $index );
					$this->add_inline_editing_attributes( $title_setting_key );
					$this->add_render_attribute( $title_setting_key, 'class', 'title title-'. $tag_h . ' '.$weight_title);

					if ( 'yes' === ( $item['pi_item_title_html_tag_h_disable'] ) ) {
						$tag_h = 'div';
					}
			?>

				<<?php echo esc_attr( $tag_h ); ?> <?php echo $this->get_render_attribute_string( $title_setting_key ); ?>>
					<?php echo wp_kses( $item['pi_item_title'], 'post' ); ?>
				</<?php echo esc_attr( $tag_h ); ?>>

			<?php endif; ?>

			<?php
				if ( ! empty( $item['pi_item_description'] ) ) :
					$content_setting_key = $this->get_repeater_setting_key( 'pi_item_description', 'pi_list_items', $index );
					$this->add_inline_editing_attributes( $content_setting_key );
					$this->add_render_attribute( $content_setting_key, 'class', 'description');
			?>
				<div <?php echo $this->get_render_attribute_string( $content_setting_key ); ?>>
					<?php echo $this->parse_text_editor( $item["pi_item_description"], 'strip' ); ?>
				</div>

			<?php endif; ?>

			</div>

			<?php
				if ( ! empty( $item['pi_item_link']['url'] ) ) {
					include __DIR__ . '/parts/pi_item_link.php';
				}
			?>

		</div>

	<?php endforeach; ?>

</div>