<?php
if ( ! function_exists( 'sellkit_update_option' ) ) {
	/**
	 * Update option from options storage.
	 *
	 * @param string $option Option name.
	 * @param mixed  $value Update value.
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
	 * @param string  $option Option name.
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

/**
 * Delete option from options storage.
 *
 * @param string $option Option name.
 *
 * @return boolean False if value was not deleted and true if value was deleted.
 */
function sellkit_delete_option( $option ) {
	$options = get_option( 'sellkit', [] );

	// Option not exist.
	if ( ! isset( $options[ $option ] ) ) {
		return false;
	}

	// Remove the option.
	unset( $options[ $option ] );
	update_option( 'sellkit', $options );

	return true;
}

/**
 * Get multi-select values.
 *
 * @since 1.1.0
 * @param array $array Multi select data.
 * @return array
 */
function sellkit_get_multi_select_values( $array ) {
	$values = [];

	foreach ( $array as $item ) {
		$values[] = $item['value'];
	}

	return $values;
}

/**
 * Checks conditions validation.
 *
 * @since 1.1.0
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @param array $conditions Conditions data.
 */
function sellkit_conditions_validation( $conditions ) {
	$is_valid = true;

	if ( empty( $conditions ) ) {
		return true;
	}

	$first_condition = reset( $conditions );
	$condition_type  = ! empty( $first_condition['type'] ) ? $first_condition['type'] : 'and';

	if ( 'or' === $condition_type ) {
		$is_valid = false;
	}

	foreach ( $conditions as $condition ) {
		if ( is_array( $condition['condition_value'] ) && ! empty( $condition['condition_value'][0]['value'] ) ) {
			$condition['condition_value'] = sellkit_get_multi_select_values( $condition['condition_value'] );
		}

		$result = sellkit_condition_match( $condition['condition_subject'], $condition['condition_operator'], $condition['condition_value'] );

		if ( is_wp_error( $result ) ) {
			continue;
		}

		if ( ! $result ) {
			$is_valid = false;
		}

		if ( $result && 'or' === $condition_type ) {
			$is_valid = true;
			break;
		}
	}

	if ( true === $is_valid ) {
		return true;
	}

	return false;
}

if ( ! function_exists( 'array_key_last' ) ) {
	/**
	 * Adds support for array_key_last function for lower php than 7.3.0 & WP lower than 5.9.0.
	 *
	 * @param array $array array.
	 */
	function array_key_last( $array ) {
		if ( empty( $array ) ) {
			return null;
		}

		end( $array );
		return key( $array );
	}
}
