<?php
/**
 * Condition row sanitization.
 *
 * @param array $condition_fields Conditions.
 *
 * @return mixed
 *
 * @since 1.1.0
 */
function sellkit_condition_row_sanitization( $condition_fields ) {
	foreach ( $condition_fields as $key => $condition_field ) {
		if (
			empty( $condition_field['type'] ) ||
			empty( $condition_field['condition_subject'] ) ||
			empty( $condition_field['condition_operator'] ) ||
			( empty( $condition_field['condition_value'] ) && '0' !== $condition_field['condition_value'] )
		) {
			unset( $condition_fields[ $key ] );
		}
	}

	return $condition_fields;
}

/**
 * Filter row sanitization.
 *
 * @since 1.1.0
 * @return mixed
 * @param array $filter_fields Filter fields.
 */
function sellkit_filter_row_sanitization( $filter_fields ) {

	foreach ( $filter_fields as $key => $filter_field ) {
		if (
			empty( $filter_field['subject'] ) ||
			empty( $filter_field['operator'] ) ||
			empty( $filter_field['value'] )
		) {
			unset( $filter_fields[ $key ] );
		}
	}

	return $filter_fields;
}
