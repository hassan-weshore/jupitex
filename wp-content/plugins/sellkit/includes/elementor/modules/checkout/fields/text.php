<?php

namespace Sellkit\Elementor\Modules\Checkout\Fields;

defined( 'ABSPATH' ) || die();

use Sellkit\Elementor\Modules\Checkout\Fields\Base;

/**
 * Class text.
 *
 * @since 1.1.0
 */
class Text extends Base {
	/**
	 * Type of field.
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function type() {
		return 'text';
	}

	/**
	 * Customized html per field.
	 *
	 * @param string $field field html string.
	 * @param array  $args checkout fields options.
	 * @param string $key key of field.
	 * @return void
	 * @since 1.1.0
	 */
	public function field( $field, $args, $key ) {
		$placeholder = $this->placeholder_required_value( $args );

		?>
			<p id="<?php echo $key; ?>_field" data-priority="">
				<span class="woocommerce-input-wrapper">
					<input
						type="text"
						class="input-text empty"
						name="<?php echo esc_attr( $key ); ?>"
						id="<?php echo esc_attr( $key ); ?>"
						placeholder="<?php echo esc_attr( $placeholder ); ?>"
						value="<?php echo ( array_key_exists( 'default', $args ) ) ? esc_attr( $args['default'] ) : ''; ?>"
					>
				</span>
			</p>
		<?php
	}
}
