<?php

namespace Sellkit\Elementor\Modules\Checkout\Fields;

defined( 'ABSPATH' ) || die();

use Sellkit\Elementor\Modules\Checkout\Fields\Base;

/**
 * Class radio.
 *
 * @since 1.1.0
 */
class Radio extends Base {
	/**
	 * Type of field.
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function type() {
		return 'radio';
	}

	/**
	 * Radio field wrapper classes.
	 *
	 * @param array $args checkout field option.
	 * @since 1.1.0
	 * @return string
	 */
	private function wrapper_class( $args ) {
		$class = 'radio_wrapper';

		if ( 'list' === $args['mode'] ) {
			$class .= ' radio-wrapper-w-100';
		}

		return $class;
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
		$default = '';
		$i       = 1;

		if ( is_array( $args['options'] ) ) {
			$default = array_key_first( $args['options'] );
		}

		if ( ! empty( $args['default'] ) ) {
			$default = $args['default'];
		}

		$default = trim( $default );

		foreach ( $args['options'] as $value => $label ) {
			$checked = '';
			$value   = trim( $value );
			if ( (string) $default === (string) $value ) {
				$checked = 'checked="checked"';
			}

			$unique_id = $key . '-' . $i;
			?>
				<div class="<?php echo esc_attr( $this->wrapper_class( $args ) ); ?>">
					<input id="<?php echo esc_attr( $unique_id ); ?>" type="radio" value="<?php echo esc_attr( $value ); ?>" <?php echo $checked; ?> name="<?php echo esc_attr( $key ); ?>">
					<label for="<?php echo esc_attr( $unique_id ); ?>"><?php echo esc_html( $label ); ?></label>
				</div>
			<?php

			$i++;
		}
	}
}
