<?php

function thegem_title_post_type_init() {
	$labels = array(
		'name'               => __('TheGem Custom Titles', 'thegem'),
		'singular_name'      => __('Title', 'thegem'),
		'menu_name'          => __('Custom Titles', 'thegem'),
		'name_admin_bar'     => __('Title', 'thegem'),
		'add_new'            => __('Add New', 'thegem'),
		'add_new_item'       => __('Add New Title', 'thegem'),
		'new_item'           => __('New Title', 'thegem'),
		'edit_item'          => __('Edit Title', 'thegem'),
		'view_item'          => __('View Title', 'thegem'),
		'all_items'          => __('All Titles', 'thegem'),
		'search_items'       => __('Search Titles', 'thegem'),
		'not_found'          => __('No titles found.', 'thegem'),
		'not_found_in_trash' => __('No titles found in Trash.', 'thegem')
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

	register_post_type('thegem_title', $args);
}
add_action('init', 'thegem_title_post_type_init', 5);

function thegem_force_title_type_private($post) {
	if ($post['post_type'] == 'thegem_title' && $post['post_status'] != 'trash' && $post['post_status'] != 'auto-draft') {
		$post['post_status'] = 'private';
	}
	return $post;
}
add_filter('wp_insert_post_data', 'thegem_force_title_type_private');
