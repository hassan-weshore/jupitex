<?php

namespace Sellkit\Elementor\Modules\Checkout\Fields;

defined( 'ABSPATH' ) || die();

/**
 * Class base.
 *
 * @since 1.1.0
 */
abstract class Base {
	/**
	 * Type of supported fields.
	 *
	 * @return array
	 * @since 1.1.0
	 */
	public function supported_fields() {
		return [
			'text',
			'select',
			'multiselect',
			'checkbox',
			'radio',
			'textarea',
			'tel',
			'hidden',
			'email',
		];
	}

	/**
	 * Return type of field.
	 *
	 * @return string
	 * @since 1.1.0
	 */
	abstract public function type();

	/**
	 * Type of fields that don't get / don't need placeholder as label.
	 *
	 * @return array
	 * @since 1.1.0
	 */
	protected function field_without_placeholder() {
		return [
			'radio',
			'checkbox',
		];
	}

	/**
	 * Print out field label using placeholder argument.
	 *
	 * @param array $args checkout fields options.
	 * @return void
	 * @since 1.1.0
	 */
	protected function placeholder( $args ) {
		if ( ! array_key_exists( 'label', $args ) ) {
			return;
		}

		$label = $args['label'];

		if ( $this->placeholder_required_value( $args ) && 'multiselect' === $args['type'] ) {
			$label .= ' *';
		}

		?>
			<div style="position:relative">
				<span class="mini-title">
					<?php echo esc_html( $label ); ?>
				</span>
			</div>
		<?php
	}

	/**
	 * Print out field label for those fields without placeholder attribute.
	 *
	 * @param array $args checkout fields options.
	 * @return void
	 * @since 1.1.0
	 */
	protected function label( $args ) {
		if ( ! in_array( $args['type'], $this->field_without_placeholder(), true ) ) {
			$this->placeholder( $args );
			return;
		}

		$label = $this->placeholder_required_value( $args );

		?>
			<label class="free_label"><?php echo esc_html( $label ); ?></label>
		<?php
	}

	/**
	 * Display error if required field is empty.
	 *
	 * @param array $args checkout fields options.
	 * @return void
	 * @since 1.1.0
	 */
	protected function required_validation( $args ) {
		if ( ! array_key_exists( 'required', $args ) || false === $args['required'] || 0 === $args['required'] ) {
			return;
		}

		?>
			<div class="sellkit-required-validation">
				<span class="required-alarm">
					<?php echo __( 'This field is required.', 'sellkit' ); ?>
				</span>
			</div>
		<?php
	}

	/**
	 * Empty element, is used to display postcode, phone etc validation errors.
	 *
	 * @param bool $enable enable or disable this element per field.
	 * @return void
	 * @since 1.1.0
	 */
	protected function global_errors( $enable ) {
		if ( false === $enable ) {
			return;
		}
		?>
			<div
				style="position:relative"
				class="sellkit-checkout-widget-d-none sellkit-checkout-field-global-errors"
			>
			</div>
		<?php
	}


	/**
	 * Adds additional class for fields if required
	 *
	 * @param array  $args checkout fields options.
	 * @param string $key field key.
	 * @return array
	 * @since 1.1.0
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	protected function additional_class( $args, $key ) {
		return $args;
	}

	/**
	 * Adds required validation class for fields.
	 *
	 * @param array $args checkout fields options.
	 * @return array
	 * @since 1.1.0
	 */
	private function set_required_class( $args ) {
		if ( array_key_exists( 'required', $args ) && ( true === $args['required'] || 1 === $args['required'] ) ) {
			$args['class'][] = 'validate-required';
		}

		return $args;
	}

	/**
	 * Check if this field is required.
	 *
	 * @param array $args field arguments.
	 * @since 1.3.1
	 * @return boolean
	 */
	protected function is_this_required( $args ) {
		if ( array_key_exists( 'required', $args ) && ( true === $args['required'] || 1 === $args['required'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Prepare placeholder value and add * to it for required fields.
	 *
	 * @param array $args fields arguments.
	 * @since 1.3.1
	 * @return string
	 */
	protected function placeholder_required_value( $args ) {
		$placeholder = ( array_key_exists( 'label', $args ) ) ? $args['label'] : '';

		if ( $this->is_this_required( $args ) ) {
			$placeholder .= ' *';
		}

		return $placeholder;
	}

	/**
	 * We make sure our fields basic class exists in class array, in order to not have any style issue.
	 *
	 * @param array $args checkout field option.
	 * @since 1.1.0
	 * @return array
	 */
	private function make_sure_sellkit_native_classes_exists( $args ) {
		$classes     = $args['class'];
		$defaults    = [ 'sellkit-widget-checkout-fields', 'sellkit-checkout-fields-wrapper' ];
		$new_classes = [];
		$is_width    = true;

		foreach ( $defaults as $def ) {
			if ( ! in_array( $def, $classes, true ) ) {
				$new_classes[] = $def;
			}
		}

		// Assign w-100 class to those fields that have not width class.
		foreach ( $classes as $class ) {
			if ( 'form-row-wide' === $class ) {
				continue;
			}

			if ( false !== strpos( $class, 'w-' ) ) {
				$is_width = false;
			}

			$new_classes[] = $class;
		}

		if ( true === $is_width ) {
			$new_classes[] = 'w-100';
		}

		$args['class'] = $new_classes;

		return $args;
	}

	/**
	 * Gets fields default value from database if exists.
	 *
	 * @param array  $args checkout fields options.
	 * @param string $key checkout field key.
	 * @since 1.1.0
	 * @return array
	 */
	private function try_to_set_default_value( $args, $key ) {
		$user = get_current_user_id();

		$default = get_user_meta( $user, $key, true );

		if ( ! empty( $default ) ) {
			$args['default'] = $default;
		}

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
	abstract public function field( $field, $args, $key );

	/**
	 * Field final html structure.
	 *
	 * @param string $field html string.
	 * @param array  $args checkout fields options.
	 * @param string $key key of field.
	 * @return void
	 * @since 1.1.0
	 */
	public function final_html_structure( $field, $args, $key ) {
		$args  = $this->additional_class( $args, $key );
		$args  = $this->set_required_class( $args );
		$args  = $this->make_sure_sellkit_native_classes_exists( $args );
		$args  = $this->try_to_set_default_value( $args, $key );
		$class = implode( ' ', $args['class'] );

		// Remove all classes of fields.
		$field = preg_replace( '/class=".*?"/', '', $field, 1 );
		// Remove fields label.
		$field = preg_replace( '~<label(.*?)</label>~Usi', '', $field );

		?>
			<div class="<?php echo $class; ?> sellkit-checkout-fields-wrapper" id="wrapper-<?php echo $key; ?>">
				<?php
					$this->label( $args );
					$this->field( $field, $args, $key );
					$this->required_validation( $args );
					$this->global_errors( true );
				?>
			</div>
		<?php
	}
}
