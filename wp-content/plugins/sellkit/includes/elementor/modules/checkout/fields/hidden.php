<?php

namespace Sellkit\Elementor\Modules\Checkout\Fields;

defined( 'ABSPATH' ) || die();

use Sellkit\Elementor\Modules\Checkout\Fields\Base;

/**
 * Class select.
 *
 * @since 1.1.0
 */
class Hidden extends Base {
	/**
	 * Type of field.
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function type() {
		return 'hidden';
	}

	/**
	 * Adds additional class for fields if required.
	 *
	 * @param array  $args checkout fields options.
	 * @param string $key field key.
	 * @return array
	 * @since 1.1.0
	 */
	protected function additional_class( $args, $key ) {
		$args['class'][] = 'sellkit-hide-completely';

		return $args;
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
		?>
			<input
				type="hidden"
				class="input-text"
				name="<?php echo esc_attr( $key ); ?>"
				id="<?php echo esc_attr( $key ); ?>"
				value="<?php echo ( array_key_exists( 'default', $args ) ) ? esc_attr( $args['default'] ) : ''; ?>"
			>
		<?php
	}
}
