<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Elementor\Plugin;
?>
		<?php if ( $settings['source'] === 'editor' ) : ?>
			<?php if ( ! empty( $settings['title'] ) ) :

				$title_tag = $settings['title_html_tag'];

				$this->add_inline_editing_attributes( 'title', 'basic' );

				$this->add_render_attribute( 'title', 'class', 'gem-cta-title' );

				$this->add_render_attribute( 'title', 'class', 'title-'.$settings['title_html_tag'] );

				if( ! empty( $settings['title_html_tag_weight'] ) ) {
					$this->add_render_attribute( 'title', 'class', $settings['title_html_tag_weight'] );
				}
				if ( 'yes' === ( $settings['title_html_tag_h_disable'] ) ) {
					$title_tag = 'div';
				}
				?>

				<<?php echo esc_attr( $title_tag ) . ' ' . $this->get_render_attribute_string( 'title' ); ?>>
					<?php echo wp_kses( $settings['title'], 'post'  ); ?>
				</<?php echo esc_attr( $title_tag ); ?>>

			<?php endif; ?>


			<?php if ( ! empty( $settings['text'] ) ) :

				$this->add_inline_editing_attributes( 'text', 'basic' );

				$this->add_render_attribute( 'text', 'class', 'gem-cta-description' );
				$this->add_render_attribute( 'text', 'class', 'gem-text-output' );
				?>

				<div <?php echo $this->get_render_attribute_string( 'text' ); ?>>
					<?php echo wp_kses( $settings['text'], 'post' ); ?>
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