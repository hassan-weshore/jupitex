<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Elementor\Icons_Manager;

if ( 'yes' === ( $settings['show_readmore_button'] ) ) : ?>
	<div <?php echo $button_container_attributes; ?>>
		<a <?php echo $readmore_button; ?> href="<?php echo get_the_permalink(); ?>">
			<span class="gem-inner-wrapper-btn">
				<?php if(!empty($settings['readmore_button_icon']['value'])) : ?>
					<span class="gem-button-icon">
						<?php \Elementor\Icons_Manager::render_icon( $settings['readmore_button_icon'], [ 'aria-hidden' => 'true' ] ); ?>
					</span>
				<?php endif; ?>
				<span <?php echo $readmore_button_text; ?>>
					<?php echo wp_kses( $settings[ 'readmore_button_text' ], 'post' ); ?>
				</span>
			</span>
		</a>
	</div>
<?php endif; ?>