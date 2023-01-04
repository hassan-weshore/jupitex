<?php

if ( ! function_exists( 'sellkit_update_option' ) ) {
	/**
	 * Update option from options storage.
	 *
	 * @param string $option Option name.
	 * @param mixed  $value  Update value.
	 *
	 * @return boolean False if value was not updated and true if value was updated.
	 */
	function sellkit_update_option( $option, $value ) {
		$options = get_option( 'sellkit', [] );

		// No need to update the same value.
		if ( isset( $options[ $option ] ) && $value === $options[ $option ] ) {
			return false;
		}

		// Update the option.
		$options[ $option ] = $value;
		update_option( 'sellkit', $options );

		return true;
	}
}

if ( ! function_exists( 'sellkit_get_option' ) ) {
	/**
	 * Get option from options storage.
	 *
	 * @param string  $option  Option name.
	 * @param boolean $default Default value.
	 *
	 * @return mixed Value set for the option.
	 */
	function sellkit_get_option( $option, $default = false ) {
		$options = get_option( 'sellkit', [] );

		if ( ! isset( $options[ $option ] ) ) {
			return $default;
		}

		return $options[ $option ];
	}
}

if ( ! function_exists( 'sellkit_htmlspecialchars' ) ) {
	/**
	 * Sellkit html special chars is created because FILTER_SANITIZE_STRING is deprecated.
	 *
	 * @since 1.2.1
	 * @param string $input_type Type of input.
	 * @param string $param Parameter key.
	 */
	function sellkit_htmlspecialchars( $input_type, $param ) {
		$value = '';

		if ( INPUT_GET === $input_type ) {
			$value = ! empty( $_GET[ $param ] ) ? htmlspecialchars( $_GET[ $param ] ) : ''; //phpcs:ignore
		}

		if ( INPUT_POST === $input_type ) {
			$value = ! empty( $_POST[ $param ] ) ? htmlspecialchars( $_POST[ $param ] ) : ''; //phpcs:ignore
		}

		if ( INPUT_COOKIE === $input_type ) {
			$value = ! empty( $_COOKIE[ $param ] ) ? htmlspecialchars( $_COOKIE[ $param ] ) : ''; //phpcs:ignore
		}

		return $value;
	}
}

