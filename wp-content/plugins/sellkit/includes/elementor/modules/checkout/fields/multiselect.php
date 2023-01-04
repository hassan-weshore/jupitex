<?php

namespace Sellkit\Elementor\Modules\Checkout\Fields;

defined( 'ABSPATH' ) || die();

use Sellkit\Elementor\Modules\Checkout\Fields\Base;

/**
 * Class multiselect.
 *
 * @since 1.1.0
 */
class Multiselect extends Base {
	/**
	 * Type of field.
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function type() {
		return 'multiselect';
	}

	/**
	 * Adds additional class for fields if required
	 *
	 * @param array  $args checkout fields options.
	 * @param string $key field key.
	 * @return array
	 * @since 1.1.0
	 */
	protected function additional_class( $args, $key ) {
		$args['class'][] = 'sellkit-checkout-multiselect-wrapper';
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
			<p>
				<span class="woocommerce-input-wrapper checkbox_wrapper">
					<select multiple name="<?php echo esc_attr( $key ); ?>" >
						<?php foreach ( $args['options'] as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</span>
			</p>
		<?php
	}
}
