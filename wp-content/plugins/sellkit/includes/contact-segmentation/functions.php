<?php

use Sellkit\Contact_Segmentation\Conditions;

/**
 * Condition match wrapper.
 *
 * @since 1.1.0
 * @param string $condition_name  Condition name.
 * @param string $operator_name   Condition operator.
 * @param mixed  $condition_value Condition value.
 */
function sellkit_condition_match( $condition_name, $operator_name, $condition_value ) {
	$condition_value = apply_filters( "sellkit_cs_conditions_value_{$condition_name}", $condition_value );

	return Conditions::match( $condition_name, $operator_name, $condition_value );
}

/**
 * Get all of the products, it's searchable.
 *
 * @since 1.1.0
 * @param string $input_value The input value for searching.
 */
function sellkit_get_products( $input_value ) {
	if ( ! sellkit()->has_valid_dependencies() ) {
		return [];
	}

	$filtered_products = [];
	$args              = [
		'post_type' => 'product',
		'post_status' => 'any',
		's' => $input_value,
	];

	$query = new \WP_Query( $args );

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$filtered_products[ get_the_ID() ] = html_entity_decode( get_the_title() );
		}
	}

	return $filtered_products;
}

/**
 * Gets the products categories.
 *
 * @since 1.1.0
 * @param string $taxonomy The taxonomy.
 * @param string $input_value The input value.
 */
function sellkit_get_terms( $taxonomy, $input_value ) {
	if ( ! sellkit()->has_valid_dependencies() ) {
		return [];
	}

	$filtered_terms = [];

	$args = array(
		'taxonomy'   => $taxonomy,
		'number'     => 20,
		'orderby'    => 'ID',
		'order'      => 'DESC',
		'hide_empty' => false,
		'name__like' => $input_value,
	);

	$terms = get_terms( $args );

	foreach ( $terms as $term ) {
		$filtered_terms[ $term->term_id ] = $term->name;
	}

	return $filtered_terms;
}

/**
 * Sellkit filter array based on a specific string.
 *
 * @since 1.1.0
 * @param array  $array Array of data.
 * @param string $string Required string.
 */
function sellkit_filter_array( $array, $string ) {
	return array_filter( $array, function ( $country ) use ( $string ) {
		return strpos( strtolower( $country ), strtolower( $string ) ) !== false;
	} );
}

/**
 * Gets Ip.
 *
 * @since 1.1.0
 * @return string
 * phpcs:disable WordPress.Security.ValidatedSanitizedInput
 */
function sellkit_get_ip() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		return $_SERVER['HTTP_CLIENT_IP'];
	}

	if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	}

	return $_SERVER['REMOTE_ADDR'];
}

/**
 * Gets contact's column value.
 *
 * @since 1.5.0
 * @param string $column Column name.
 * @return mixed|string|void
 */
function get_funnel_contact_value_by_column( $column ) {
	$results  = [];
	$contacts = sellkit()->db->get( 'funnel_contact', [
		'user_id' => get_current_user_id()
	] );

	foreach ( $contacts as $contact ) {
		if ( ! empty( $contact[ $column ] ) ) {
			$results[] = unserialize( $contact[ $column ] )[0]; // phpcs:ignore
		}
	}

	return $results;
}
