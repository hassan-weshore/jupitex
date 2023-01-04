<?php

function thegem_footer_post_type_init() {
	$labels = array(
		'name'               => __('TheGem Custom Footers', 'thegem'),
		'singular_name'      => __('Footer', 'thegem'),
		'menu_name'          => __('Custom Footers', 'thegem'),
		'name_admin_bar'     => __('Footer', 'thegem'),
		'add_new'            => __('Add New', 'thegem'),
		'add_new_item'       => __('Add New Footer', 'thegem'),
		'new_item'           => __('New Footer', 'thegem'),
		'edit_item'          => __('Edit Footer', 'thegem'),
		'view_item'          => __('View Footer', 'thegem'),
		'all_items'          => __('All Footers', 'thegem'),
		'search_items'       => __('Search Footers', 'thegem'),
		'not_found'          => __('No footers found.', 'thegem'),
		'not_found_in_trash' => __('No footers found in Trash.', 'thegem')
	);

	$args = array(
		'labels'               => $labels,
		'public'               => true,
		'exclude_from_search'  => true,
		'publicly_queryable'   => true,
		'show_ui'              => true,
		'query_var'            => false,
		'hierarchical'         => false,
		'supports'             => array('title', 'editor'),
		'menu_position'        => 39
	);

	register_post_type('thegem_footer', $args);
}
add_action('init', 'thegem_footer_post_type_init', 5);

function thegem_force_type_private($post) {
	if ($post['post_type'] == 'thegem_footer' && $post['post_status'] != 'trash' && $post['post_status'] != 'auto-draft') {
		$post['post_status'] = 'private';
	}
	return $post;
}
add_filter('wp_insert_post_data', 'thegem_force_type_private');
