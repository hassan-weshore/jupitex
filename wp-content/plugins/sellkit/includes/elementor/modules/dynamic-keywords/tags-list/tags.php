<?php
/**
 * Gets all tags.
 *
 * @since 1.1.0
 */
function get_tags_title() {
	$dynamic_tags = new Sellkit_Dynamic_Keywords();

	$dynamic_tags_item = '';

	foreach ( $dynamic_tags::$keywords['order_keyword'] as $key => $title ) {
		$dynamic_tags_item .= '<li>';
		$dynamic_tags_item .= '<h5>' . esc_attr( $title ) . '</h5>';
		$dynamic_tags_item .= '<input id = "' . esc_attr( $key ) . '" type = "text"  value = "[' . esc_attr( $key ) . ']" readonly>';
		$dynamic_tags_item .= '<button value = "' . esc_attr( $key ) . '" >Copy</button>';
		$dynamic_tags_item .= '</li>';
	}

	echo $dynamic_tags_item;
}


