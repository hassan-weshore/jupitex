<div <?php echo $this->get_render_attribute_string( 'qf_item_info' ); ?>>
	<?php if ( ! empty( $item['qf_item_title'] ) ) : 	
		$title_tag = $item['qf_item_title_html_tag'];
		$repeater_qf_item_title = $this->get_repeater_setting_key( 'qf_item_title', 'qf_list', $index );
		$this->add_inline_editing_attributes( $repeater_qf_item_title ); 
		$this->add_render_attribute( $repeater_qf_item_title, 'class', 'quickfinder-title' );
		$this->add_render_attribute( $repeater_qf_item_title, 'class', 'title-' . $item['qf_item_title_html_tag'] );

		if( ! empty( $item['qf_item_title_html_tag_weight'] ) ) {
			$this->add_render_attribute( $repeater_qf_item_title, 'class', $item['qf_item_title_html_tag_weight'] );
		}
		if ( 'yes' === ( $item['qf_item_title_html_tag_h_disable'] ) ) {
			$title_tag = 'div';
		}
		?>

		<<?php echo esc_attr( $title_tag ) . ' ' . $this->get_render_attribute_string( $repeater_qf_item_title ); ?>>
			<?php echo wp_kses( $item['qf_item_title'], 'post' ); ?>
		</<?php echo esc_attr( $title_tag ); ?>>
	<?php endif; ?>

	<?php if ( ! empty( $item['qf_item_description'] ) ) :
		$repeater_qf_item_description = $this->get_repeater_setting_key( 'qf_item_description', 'qf_list', $index );
		$this->add_inline_editing_attributes( $repeater_qf_item_description );
		$this->add_render_attribute( $repeater_qf_item_description, 'class', 'quickfinder-description' );
		$this->add_render_attribute( $repeater_qf_item_description, 'class', 'gem-text-output' );
		?>

		<div <?php echo $this->get_render_attribute_string( $repeater_qf_item_description ); ?>>
			<?php echo $this->parse_text_editor( $item['qf_item_description'] ); ?>
		</div>
	<?php endif; ?>

	<?php if ( 'yes' === ( $item['qf_item_show_button'] ) ) : ?>
		<?php
		wp_enqueue_style( 'thegem-button' );
		$this->add_render_attribute( 'button_container', 'class', ['gem-button-container', 'gem-widget-button'] );
		$button_link_key = 'qf_button_' . $index;

		$this->add_render_attribute( 'qf_item_button_text', 'class', 'gem-text-button' );
		$this->add_inline_editing_attributes( 'qf_item_button_text', 'none' );

		if ( !empty ( $item['qf_item_link']['url'] ) ) {
			$this->add_render_attribute( $button_link_key, 'class', 'item-linked' );
		}
		
		$this->add_render_attribute( $button_link_key, 'class', ['gem-button', 'gem-button-size-'.esc_attr( $settings['button_size'] ), 'gem-button-text-weight-'.esc_attr( $item['qf_item_button_text_weight'] )] );

		if( ! empty( $settings['button_icon_align'] ) && $settings['button_icon_align'] === 'right' ) {
			$this->add_render_attribute( $button_link_key, 'class', 'gem-button-icon-position-right' );
		}
		if( ! empty( $settings['button_type'] ) ) {
			$this->add_render_attribute( $button_link_key, 'class', 'gem-button-style-'.$settings['button_type'] );
		}
		if ( empty ($item['qf_item_link']['url'] ) && ! empty( $item['qf_item_button_link']['url'] ) ) {
			$this->add_link_attributes( $button_link_key, $item['qf_item_button_link'] );
		}
		if ( 'yes' === $settings['lazy'] ) {
			$this->add_render_attribute( $button_link_key, 'class', 'lazy-loading-item' );
			$this->add_render_attribute( $button_link_key, 'data-ll-effect', 'drop-right-without-wrap' );
		}	
		?>
		<div <?php echo $this->get_render_attribute_string( 'button_container' ); ?>>
			<a <?php echo $this->get_render_attribute_string( $button_link_key ); ?>>
				<span class="gem-inner-wrapper-btn">
					<?php if(!empty($item['qf_item_button_icon']['value'])) : ?>
						<span class="gem-button-icon">
							<?php \Elementor\Icons_Manager::render_icon( $item['qf_item_button_icon'], [ 'aria-hidden' => 'true' ] ); ?>
						</span>
					<?php endif; ?>
					<span <?php echo $this->get_render_attribute_string( 'qf_item_button_text' ); ?>>
						<?php echo wp_kses( $item[ 'qf_item_button_text' ], 'post' ); ?>
					</span>
				</span>
			</a>			
		</div>
	<?php endif; ?>
</div>