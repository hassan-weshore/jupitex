<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Plugin;

$link_icon_image = $this->get_icon_image_link_url( $settings );
$textbox_container_link = $this->get_textbox_link_url( $settings );

	if ( $link_icon_image ) {

		$this->add_link_attributes( 'content_textbox_icon_image_link', $link_icon_image );

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$this->add_render_attribute( 'content_textbox_icon_image_link', [
				'class' => 'elementor-clickable',
			] );
		}
	}

	$this->add_render_attribute( 'textbox_content', 'class', 'gem-textbox-content' );

	if(!in_array($settings['thegem_elementor_preset'], array('box', 'capsule', 'minimal'))) {
		$this->add_render_attribute( 'textbox_content', 'class', 'default-background' );
	}

	if(in_array($settings['thegem_elementor_preset'], array('box', 'capsule', 'minimal'))) {
		$this->add_render_attribute( 'textbox_content', 'class', 'bordered-box' );
	}

	if( ! empty( $settings['textbox_content_icon_image_vertical_position'] ) ) {
		$this->add_render_attribute( 'textbox_content', 'class', $settings['textbox_content_icon_image_vertical_position'] );
	}
	if( ! empty( $settings['textbox_content_icon_image_horizontal_position'] ) ) {
		$this->add_render_attribute( 'textbox_content', 'class', 'gem-textbox-icon-horizontal-'.$settings['textbox_content_icon_image_horizontal_position'] );
	}
	if( ! empty( $settings['textbox_content_button_position'] )) {
		$this->add_render_attribute( 'textbox_content', 'class', $settings['textbox_content_button_position'] );
	}
	if( ! empty( $settings['textbox_content_icon_image_text_wrapping'] ) ) {
		$this->add_render_attribute( 'textbox_content', 'class', 'gem-textbox-icon-image-wrapping-'.$settings['textbox_content_icon_image_text_wrapping'] );
	}

?>

<div class="gem-textbox <?php echo ! empty( $settings['thegem_elementor_preset'] ) ? 'styled-textbox-' . esc_attr( $settings['thegem_elementor_preset'] ) : '' ?>">

	<?php if ( ! empty( $settings['textbox_container_top_shape'] ) ) :
		$top_style = $settings['textbox_container_top_shape']; ?>

		<div class="gem-textbox-top gem-textbox-top-<?php echo esc_attr( $top_style ); ?> default-fill">
			<?php

			if ( $top_style == 'flag' ) {
				echo '<svg viewBox="0 0 1000 20" preserveAspectRatio="none" width="100%" height="20"><path d="M 0,20.5 0,0 500,20.5 1000,0 1000,20.5" /></svg>';
			}
			if ( $top_style == 'shield' ) {
				echo '<svg viewBox="0 0 1000 50" preserveAspectRatio="none" width="100%" height="50"><path d="M 0,50.5 500,0 1000,50.5" /></svg>';
			}
			if ( $top_style == 'ticket' ) {
				$pattern_id = 'pattern-'.time().'-'.rand(0, 100);
				echo '<svg width="100%" height="14"><defs><pattern id="'. esc_attr( $pattern_id ) .'" x="16" y="0" width="32" height="16" patternUnits="userSpaceOnUse" ><path d="M 0,14.5 16,-0.5 32,14.5" /></pattern></defs><rect x="0" y="0" width="100%" height="14" style="fill: url(#'. esc_attr( $pattern_id ) .');" /></svg>';
			}
			if ( $top_style == 'sentence' ) {
				echo '<svg width="100" height="50"><path d="M 0,51 Q 45,45 50,0 Q 55,45 100,51" /></svg>';
			}
			if ( $top_style == 'note-1' ) {
				$pattern_id = 'pattern-'.time().'-'.rand(0, 100);
				echo '<svg width="100%" height="31"><defs><pattern id="'. esc_attr( $pattern_id ) .'" x="11" y="0" width="23" height="32" patternUnits="userSpaceOnUse" ><path d="M20,9h3V0H0v9h3c2.209,0,4,1.791,4,4v6c0,2.209-1.791,4-4,4H0v9h23v-9h-3c-2.209,0-4-1.791-4-4v-6C16,10.791,17.791,9,20,9z" /></pattern></defs><rect x="0" y="0" width="100%" height="32" style="fill: url(#'. esc_attr( $pattern_id ) .');" /></svg>';
			}
			if ( $top_style == 'note-2' ) {
				$pattern_id = 'pattern-'.time().'-'.rand(0, 100);
				echo '<svg width="100%" height="27"><defs><pattern id="'. esc_attr( $pattern_id ) .'" x="10" y="0" width="20" height="28" patternUnits="userSpaceOnUse" ><path d="M20,8V0H0v8c3.314,0,6,2.687,6,6c0,3.313-2.686,6-6,6v8h20v-8c-3.313,0-6-2.687-6-6C14,10.687,16.687,8,20,8z" /></pattern></defs><rect x="0" y="0" width="100%" height="28" style="fill: url(#' . esc_attr( $pattern_id ) . ');" /></svg>';
			} ?>
		</div>
	<?php endif; ?>

	<div class="gem-textbox-inner">

		<div <?php echo $this->get_render_attribute_string( 'textbox_content' ); ?>>

			<?php if ( !empty( $settings['content_textbox_icon_image_select'] ) && ( ! empty( $settings['icon'] ) || ! empty( $settings['content_textbox_selected_icon'] ) ) || 'image' === $settings['content_textbox_icon_image_select'] && ! empty( $settings['content_textbox_graphic_image']['url'] ) ) : ?>
				<div class="gem-texbox-icon-image-wrapper">

					<?php if ( 'image' === $settings['content_textbox_icon_image_select'] && ! empty( $settings['content_textbox_graphic_image']['url'] ) ) : ?>
						<div class="gem-image">
							<span>
								<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'content_textbox_graphic_image' ); ?>
							</span>
						</div>

					<?php elseif ( 'icon' === $settings['content_textbox_icon_image_select'] && ( ! empty( $settings['icon'] ) || ! empty( $settings['content_textbox_selected_icon'] ) ) ) : ?>
						<div class="<?php echo ! empty( $settings['textbox_content_icon_image_vertical_position'] ) ? esc_attr( $settings['textbox_content_icon_image_vertical_position'] ) : ''?> <?php echo ! empty( $settings['textbox_content_icon_image_horizontal_position'] ) ? esc_attr( $settings['textbox_content_icon_image_horizontal_position'] ) : ''?>">
							<div class="gem-textbox-icon">
								<div class="elementor-icon">
									<?php Icons_Manager::render_icon( $settings['content_textbox_selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<?php
					if ( $link_icon_image && empty( $textbox_container_link )) :

						$this->add_link_attributes( 'link', $link_icon_image );
						$this->add_render_attribute( 'link', 'class', 'gem-styled-textbox-icon-image-link' ); ?>
						<a <?php echo ( $this->get_render_attribute_string( 'link' ) ) ?>></a>

					<?php endif; ?>

				</div>
			<?php endif; ?>

			<div class="gem-texbox-text-wrapper">

				<?php if ( $settings['source'] === 'editor' ) : ?>
					<?php if ( ! empty( $settings['content_textbox_title'] ) ) :

						$title_tag = $settings['content_textbox_title_html_tag'];

						$this->add_render_attribute( 'content_textbox_title', 'class', 'gem-textbox-title' );

						$this->add_inline_editing_attributes( 'content_textbox_title', 'basic' );

						$this->add_render_attribute( 'content_textbox_title', 'class', 'title-'.$settings['content_textbox_title_html_tag'] );

						if( ! empty( $settings['content_textbox_title_html_tag_weight'] ) ) {
							$this->add_render_attribute( 'content_textbox_title', 'class', $settings['content_textbox_title_html_tag_weight'] );
						}
						if ( 'yes' === ( $settings['content_textbox_title_html_tag_h_disable'] ) ) {
							$title_tag = 'div';
						}
						?>

						<<?php echo esc_attr( $title_tag ) . ' ' . $this->get_render_attribute_string( 'content_textbox_title' ); ?>>
							<?php echo wp_kses( $settings['content_textbox_title'], 'post'  ); ?>
						</<?php echo esc_attr( $title_tag ); ?>>

					<?php endif; ?>

					<?php if ( ! empty( $settings['content_textbox_text'] ) ) :
						$this->add_render_attribute( 'content_textbox_text', 'class', 'gem-textbox-description' );
						$this->add_render_attribute( 'content_textbox_text', 'class', 'gem-text-output' );
						$this->add_inline_editing_attributes( 'content_textbox_text', 'basic' );
						?>

						<div <?php echo $this->get_render_attribute_string( 'content_textbox_text' ); ?>>
							<?php echo wp_kses( $settings['content_textbox_text'], 'post' ); ?>
						</div>

					<?php endif; ?>

				<?php elseif ( $settings['source'] === 'template' && $settings['template'] ) :
					echo Plugin::instance()->frontend->get_builder_content_for_display( $settings['template'] );
					if ( is_admin() && Plugin::$instance->editor->is_edit_mode() ) {
						$link = add_query_arg(
							array(
								'elementor' => '',
							),
							get_permalink( $settings['template'] )
						);
						echo sprintf( '<a class="gem-tta-template-edit gem-button gem-button-size-small gem-button-style-flat gem-button-text-weight-thin" data-tta-template-edit-link="%s">%s</a>', $link, esc_html__( 'Edit Template', 'thegem' ) );
					}
				endif; ?>

				<?php if ( 'yes' === ( $settings['content_textbox_show_button'] ) ) : ?>
					<?php
						wp_enqueue_style( 'thegem-button' );

						$this->add_render_attribute( 'button_container', 'class', ['gem-button-container', 'gem-widget-button'] );

						$this->add_render_attribute( 'content_textbox_button_text', 'class', 'gem-text-button' );
						$this->add_inline_editing_attributes( 'content_textbox_button_text', 'none' );

						$this->add_render_attribute( 'textbox_button', 'class', ['gem-button', 'gem-button-size-'.esc_attr( $settings['textbox_content_button_size'] ), 'gem-button-text-weight-'.esc_attr( $settings['content_textbox_button_text_weight'] ) ] );

						if( ! empty( $settings['textbox_content_button_icon_align'] ) && $settings['textbox_content_button_icon_align'] === 'right' ) {
							$this->add_render_attribute( 'textbox_button', 'class', 'gem-button-icon-position-right' );
						}
						if( ! empty( $settings['textbox_content_button_type'] ) ) {
							$this->add_render_attribute( 'textbox_button', 'class', 'gem-button-style-'.$settings['textbox_content_button_type'] );
						}
						if ( !empty ( $settings['content_textbox_link']['url'] ) ) {
							$this->add_render_attribute( 'textbox_button', 'class', 'item-linked' );
						}
					?>
					<div <?php echo $this->get_render_attribute_string( 'button_container' ); ?>>
						<?php        
						if ( empty( $textbox_container_link ) && !  empty( $settings['content_textbox_button_link']['url'] ) ) {

							if ( $settings['content_textbox_button_link']['is_external'] ) {
								$this->add_render_attribute( 'textbox_button', 'target', '_blank' );
							}

							if ( $settings['content_textbox_button_link']['nofollow'] ) {
								$this->add_render_attribute( 'textbox_button', 'rel', 'nofollow' );
							}

							$this->add_inline_editing_attributes( 'content_textbox_button_text', 'none' );

							$this->add_render_attribute( 'textbox_button', 'href', $settings['content_textbox_button_link']['url'] );
						}
						?>
						<a <?php echo $this->get_render_attribute_string( 'textbox_button' ); ?>>
							<span class="gem-inner-wrapper-btn">
								<?php if(!empty($settings['content_textbox_button_icon']['value'])) : ?>
									<span class="gem-button-icon">
										<?php \Elementor\Icons_Manager::render_icon( $settings['content_textbox_button_icon'], [ 'aria-hidden' => 'true' ] ); ?>
									</span>
								<?php endif; ?>
								<span <?php echo $this->get_render_attribute_string( 'content_textbox_button_text' ); ?>>
									<?php echo wp_kses( $settings[ 'content_textbox_button_text' ], 'post' ); ?>
								</span>
							</span>
						</a>
					</div>
				<?php endif; ?>

			</div>

		</div>

	</div>

	<?php
	if ( ! empty( $settings[ 'textbox_container_bottom_shape' ] ) ) :
		$bottom_style = $settings[ 'textbox_container_bottom_shape' ]; ?>

		<div class="gem-textbox-bottom gem-textbox-bottom-<?php echo esc_attr( $bottom_style ); ?> default-fill">
		<?php

			if ( $bottom_style == 'flag' ) {
				echo '<svg viewBox="0 0 1000 20" preserveAspectRatio="none" width="100%" height="20"><path d="M 0,-0.5 0,20 500,0 1000,20 1000,-0.5" /></svg>';
			}
			if ( $bottom_style == 'shield' ) {
				echo '<svg viewBox="0 0 1000 50" preserveAspectRatio="none" width="100%" height="50"><path d="M 0,-0.5 500,50 1000,-0.5" /></svg>';
			}
			if ( $bottom_style == 'ticket' ) {
				$pattern_id = 'pattern-'.time().'-'.rand(0, 100);
				echo '<svg width="100%" height="14"><defs><pattern id="'. esc_attr( $pattern_id ) .'" x="16" y="-1" width="32" height="16" patternUnits="userSpaceOnUse" ><path d="M 0,-0.5 16,14.5 32,-0.5" /></pattern></defs><rect x="0" y="-1" width="100%" height="14" style="fill: url(#'. esc_attr( $pattern_id ) .');" /></svg>';
			}
			if ( $bottom_style == 'sentence' ) {
				echo '<svg width="100" height="50"><path d="M 0,-1 Q 45,5 50,50 Q 55,5 100,-1" /></svg>';
			}
			if ( $bottom_style == 'note-1' ) {
				$pattern_id = 'pattern-'.time().'-'.rand(0, 100);
				echo '<svg width="100%" height="32"><defs><pattern id="'. esc_attr( $pattern_id ) .'" x="11" y="-1" width="23" height="32" patternUnits="userSpaceOnUse" ><path d="M20,9h3V0H0v9h3c2.209,0,4,1.791,4,4v6c0,2.209-1.791,4-4,4H0v9h23v-9h-3c-2.209,0-4-1.791-4-4v-6C16,10.791,17.791,9,20,9z" /></pattern></defs><rect x="0" y="-1" width="100%" height="32" style="fill: url(#'. esc_attr( $pattern_id ) .');" /></svg>';
			}
			if ( $bottom_style == 'note-2' ) {
				$pattern_id = 'pattern-'.time().'-'.rand(0, 100);
				echo '<svg width="100%" height="28"><defs><pattern id="'. esc_attr( $pattern_id ) .'" x="10" y="-1" width="20" height="28" patternUnits="userSpaceOnUse" ><path d="M20,8V0H0v8c3.314,0,6,2.687,6,6c0,3.313-2.686,6-6,6v8h20v-8c-3.313,0-6-2.687-6-6C14,10.687,16.687,8,20,8z" /></pattern></defs><rect x="0" y="-1" width="100%" height="28" style="fill: url(#'. esc_attr( $pattern_id ) .');" /></svg>';
			} ?>
		</div>
	<?php endif; ?>

	<?php
	if ( $textbox_container_link ) :

		$this->add_link_attributes( 'link', $textbox_container_link );
		$this->add_render_attribute( 'link', 'class', 'gem-styled-textbox-link' ); ?>
		<a <?php echo ( $this->get_render_attribute_string( 'link' ) ) ?>></a>

	<?php endif; ?>

</div>
