<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;

$cta_picture = __DIR__ . '/parts/cta_picture.php';
$cta_content = __DIR__ . '/parts/cta_content.php';
$cta_buttons = __DIR__ . '/parts/cta_buttons.php';

$link_icon_image = $this->get_icon_image_link_url( $settings );

	if ( $link_icon_image ) {

		$this->add_link_attributes( 'icon_image_link', $link_icon_image );

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$this->add_render_attribute( 'icon_image_link', [
				'class' => 'elementor-clickable',
			] );
		}
	}

$this->add_render_attribute( 'cta_wrapper', 'class', 'gem-alert-box' );
$this->add_render_attribute( 'cta_inner', 'class', 'gem-alert-inner' );

if(in_array($settings['thegem_elementor_preset'], array('solid-box'))) {
	$this->add_render_attribute( 'cta_inner', 'class', 'default-background' );
}

if(in_array($settings['thegem_elementor_preset'], array('outlined-thin'))) {
	$this->add_render_attribute( 'cta_inner', 'class', 'bordered-box' );
}

$this->add_render_attribute( 'cta_picture', 'class', 'gem-alert-box-picture' );
$this->add_render_attribute( 'cta_content', 'class', 'gem-alert-box-content' );
$this->add_render_attribute( 'cta_buttons', 'class', 'gem-alert-box-buttons' );

$this->add_render_attribute( 'cta_icon_image', 'class', 'gem-cta-icon-image-wrapper' );

if ( ! empty( $settings['icon_vertical_position'] ) ) {
	$this->add_render_attribute( 'cta_inner', 'class', $settings['icon_vertical_position'] );
}
if ( ! empty( $settings['icon_horizontal_position'] ) ) {
	$this->add_render_attribute( 'cta_inner', 'class', 'gem-alert-box-icon-horizontal-' . $settings['icon_horizontal_position'] );
}
if ( ! empty( $settings['icon_text_wrapping'] ) ) {
	$this->add_render_attribute( 'cta_inner', 'class', 'gem-alert-box-icon-image-wrapping-' . $settings['icon_text_wrapping'] );
}
if ( ! empty( $settings['button_position'] ) ) {
	$this->add_render_attribute( 'cta_inner', 'class', $settings['button_position'] );
} else {
	$this->add_render_attribute( 'cta_inner', 'class', 'no-button' );
}

?>

<div <?php echo $this->get_render_attribute_string( 'cta_wrapper' ); ?>>

	<?php if ( ! empty( $settings['container_top_shape'] ) ) :
		$top_style = $settings['container_top_shape']; ?>

		<div class="gem-alert-box-top gem-alert-box-top-<?php echo esc_attr( $top_style ); ?> default-fill">
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

	<div <?php echo $this->get_render_attribute_string( 'cta_inner' ); ?>>

		<?php if ( 'inline' === ( $settings['icon_text_wrapping'] ) &&  ('left' === ( $settings['icon_horizontal_position'] ) && 'button-left-inline' === ( $settings['button_position'] ) ) ||  ( 'right' === ( $settings['icon_horizontal_position'] ) && 'button-right-inline' === ( $settings['button_position'] ) ) ) : ?>

			<div class="picture-button-wrapper">

				<?php if ( !empty( $settings['icon_image_select'] ) && ( ! empty( $settings['icon'] ) || ! empty( $settings['selected_icon'] ) ) || 'image' === $settings['icon_image_select'] && ! empty( $settings['graphic_image']['url'] ) ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'cta_picture' ); ?>>
						<?php if ( ! empty( $cta_picture ) && file_exists( $cta_picture) ) : include $cta_picture; endif; ?>
					</div>
				<?php endif; ?>

				<div <?php echo $this->get_render_attribute_string( 'cta_buttons' ); ?>>
					<?php if ( ! empty( $cta_buttons ) && file_exists( $cta_buttons ) ): include $cta_buttons; endif; ?>
				</div>
			</div>

			<div <?php echo $this->get_render_attribute_string( 'cta_content' ); ?>>
				<?php if ( ! empty( $cta_content ) && file_exists( $cta_content ) ) : include $cta_content; endif; ?>
			</div>

		<?php elseif ( 'inline' === ( $settings['icon_text_wrapping'] ) && ( 'left' === ( $settings['icon_horizontal_position'] ) && 'button-bottom' === ( $settings['button_position'] ) ) || ( 'right' === ( $settings['icon_horizontal_position'] ) && 'button-bottom' === ( $settings['button_position'] ) ) ) : ?>

			<?php if ( !empty( $settings['icon_image_select'] ) && ( ! empty( $settings['icon'] ) || ! empty( $settings['selected_icon'] ) ) || 'image' === $settings['icon_image_select'] && ! empty( $settings['graphic_image']['url'] ) ) : ?>
				<div <?php echo $this->get_render_attribute_string( 'cta_picture' ); ?>>
					<?php if ( ! empty( $cta_picture ) && file_exists( $cta_picture) ) : include $cta_picture; endif; ?>
				</div>
			<?php endif; ?>

			<div class="text-button-wrapper">
				<div <?php echo $this->get_render_attribute_string( 'cta_content' ); ?>>
					<?php if ( ! empty( $cta_content ) && file_exists( $cta_content ) ) : include $cta_content; endif; ?>
				</div>

				<div <?php echo $this->get_render_attribute_string( 'cta_buttons' ); ?>>
					<?php if ( ! empty( $cta_buttons ) && file_exists( $cta_buttons ) ): include $cta_buttons; endif; ?>
				</div>
			</div>

		<?php elseif ( 'inline' === ( $settings['icon_text_wrapping'] ) && ('left' === ( $settings['icon_horizontal_position'] ) && 'button-right-inline' === ( $settings['button_position'] ) ) || ( 'right' === ( $settings['icon_horizontal_position'] ) && 'button-left-inline' === ( $settings['button_position'] ) ) ) : ?>

			<?php if ( !empty( $settings['icon_image_select'] ) && ( ! empty( $settings['icon'] ) || ! empty( $settings['selected_icon'] ) ) || 'image' === $settings['icon_image_select'] && ! empty( $settings['graphic_image']['url'] ) ) : ?>
				<div <?php echo $this->get_render_attribute_string( 'cta_picture' ); ?>>
					<?php if ( ! empty( $cta_picture ) && file_exists( $cta_picture) ) : include $cta_picture; endif; ?>
				</div>
			<?php endif; ?>

			<div <?php echo $this->get_render_attribute_string( 'cta_content' ); ?>>
				<?php if ( ! empty( $cta_content ) && file_exists( $cta_content ) ) : include $cta_content; endif; ?>
			</div>
	
			<div <?php echo $this->get_render_attribute_string( 'cta_buttons' ); ?>>
				<?php if ( ! empty( $cta_buttons ) && file_exists( $cta_buttons ) ): include $cta_buttons; endif; ?>
			</div>

		<?php else: ?>	

			<?php if ( !empty( $settings['icon_image_select'] ) && ( ! empty( $settings['icon'] ) || ! empty( $settings['selected_icon'] ) ) || 'image' === $settings['icon_image_select'] && ! empty( $settings['graphic_image']['url'] ) ) : ?>
				<div <?php echo $this->get_render_attribute_string( 'cta_picture' ); ?>>
					<?php if ( ! empty( $cta_picture ) && file_exists( $cta_picture) ) : include $cta_picture; endif; ?>
				</div>
			<?php endif; ?>

			<div <?php echo $this->get_render_attribute_string( 'cta_content' ); ?>>
				<?php if ( ! empty( $cta_content ) && file_exists( $cta_content ) ) : include $cta_content ; endif; ?>
			</div>

			<div <?php echo $this->get_render_attribute_string( 'cta_buttons' ); ?>>
				<?php if ( ! empty( $cta_buttons ) && file_exists( $cta_buttons ) ) : include $cta_buttons ; endif; ?>
			</div>

		<?php endif; ?>	

	</div>

	<?php if ( ! empty( $settings['container_bottom_shape'] ) ) :
		$bottom_style = $settings[ 'container_bottom_shape' ]; ?>

		<div class="gem-alert-box-bottom gem-alert-box-bottom-<?php echo esc_attr( $bottom_style ); ?> default-fill">
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
</div>