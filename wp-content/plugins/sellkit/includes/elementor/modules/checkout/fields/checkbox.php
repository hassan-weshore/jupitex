<?php

namespace Sellkit\Elementor\Modules\Checkout\Fields;

defined( 'ABSPATH' ) || die();

use Sellkit\Elementor\Modules\Checkout\Fields\Base;

/**
 * Class checkbox.
 *
 * @since 1.1.0
 */
class Checkbox extends Base {
	/**
	 * Type of field.
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function type() {
		return 'checkbox';
	}

	/**
	 * Print out field label.
	 *
	 * @param array $args checkout fields options.
	 * @return void
	 * @since 1.1.0
	 */
	protected function label( $args ) {

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
		$args['class'][] = 'sellkit-checkout-checkbox-wrapper';

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
				<?php
					$checked = ( 'true' === $args['checked'] ) ? 'checked="checked"' : '';
				?>
					<input id="<?php echo esc_attr( $key ); ?>" style="width:fit-content" name="<?php esc_attr( $key ); ?>" class="checkbox_margin" type="checkbox" <?php echo $checked; ?> >
					<label for="<?php echo esc_attr( $key ); ?>" class="checkbox_label"><?php echo esc_html( $args['label'] ); ?></label>
				</span>
			</p>
		<?php
	}
}
