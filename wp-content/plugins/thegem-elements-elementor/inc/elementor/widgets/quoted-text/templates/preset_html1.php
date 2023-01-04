<?php
if ( ! defined( 'ABSPATH' ) ) exit;

?>
<div class="gem-quote <?php echo ! empty( $settings['thegem_elementor_preset'] ) ? 'gem-quote-'.esc_attr( $settings['thegem_elementor_preset'] ) : '' ?> default-background">
	<blockquote <?php echo $this->get_render_attribute_string( 'quoted_text' ); ?>><?php echo wp_kses( $settings[ 'quoted_text' ], 'post' ); ?></blockquote>
</div>
