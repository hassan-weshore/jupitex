<?php


use Elementor\Icons_Manager;
use TheGem_Elementor\Widgets\Extended_BlogGrid\TheGem_Extended_BlogGrid;

function thegem_news_post_type_init() {
	if(!thegem_get_option('activate_news_posttype')) return ;
	$labels = array(
		'name'               => __('News', 'thegem'),
		'singular_name'      => __('News', 'thegem'),
		'menu_name'          => __('News', 'thegem'),
		'name_admin_bar'     => __('News', 'thegem'),
		'add_new'            => __('Add New', 'thegem'),
		'add_new_item'       => __('Add New News', 'thegem'),
		'new_item'           => __('New News', 'thegem'),
		'edit_item'          => __('Edit News', 'thegem'),
		'view_item'          => __('View News', 'thegem'),
		'all_items'          => __('All News', 'thegem'),
		'search_items'       => __('Search News', 'thegem'),
		'not_found'          => __('No news found.', 'thegem'),
		'not_found_in_trash' => __('No news found in Trash.', 'thegem')
	);


	$args = array(
		'labels'               => $labels,
		'public'               => true,
		'publicly_queryable'   => true,
		'show_ui'              => true,
		'query_var'            => true,
		'hierarchical'         => false,
		'register_meta_box_cb' => 'thegem_post_items_register_meta_box',
		'taxonomies'           => array('thegem_news_sets'),
		'rewrite' => array('slug' => apply_filters('thegem_news_rewrite_slug', 'news'), 'with_front' => false),
		'capability_type' => 'post',
		'supports'             => array('title', 'author', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'comments', 'post-formats'),
		'menu_position'        => 38
	);

	register_post_type('thegem_news', $args);

	$labels = array(
		'name'                       => __('News Sets', 'thegem'),
		'singular_name'              => __('News Set', 'thegem'),
		'search_items'               => __('Search News Sets', 'thegem'),
		'popular_items'              => __('Popular News Sets', 'thegem'),
		'all_items'                  => __('All News Sets', 'thegem'),
		'edit_item'                  => __('Edit News Set', 'thegem'),
		'update_item'                => __('Update News Set', 'thegem'),
		'add_new_item'               => __('Add New News Set', 'thegem'),
		'new_item_name'              => __('New News Set Name', 'thegem'),
		'separate_items_with_commas' => __('Separate News Sets with commas', 'thegem'),
		'add_or_remove_items'        => __('Add or remove News Sets', 'thegem'),
		'choose_from_most_used'      => __('Choose from the most used News Sets', 'thegem'),
		'not_found'                  => __('No news sets found.', 'thegem'),
		'menu_name'                  => __('News Sets', 'thegem'),
	);

	$args = array(
		'hierarchical'          => true,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array('slug' => 'news_sets'),
	);

	register_taxonomy('thegem_news_sets', 'thegem_news', $args);
}
add_action('init', 'thegem_news_post_type_init', 5);

function thegem_options_rewrite_slug($value) {
	if(sanitize_title(thegem_get_option('news_rewrite_slug'))) {
		$value = sanitize_title(thegem_get_option('news_rewrite_slug'));
	}
	return $value;
}
add_filter('thegem_news_rewrite_slug', 'thegem_options_rewrite_slug');

function thegem_post_items_register_meta_box($post) {
	add_meta_box('thegem_news_item_settings', __('News Item Settings', 'thegem'), 'thegem_post_item_settings_box', 'thegem_news', 'normal', 'high');
}

add_action('wp_ajax_blog_load_more', 'blog_load_more_callback');
add_action('wp_ajax_nopriv_blog_load_more', 'blog_load_more_callback');
function blog_load_more_callback() {
	$response = array();
	$data = isset($_POST['data']) ? $_POST['data'] : array();
	$data['is_ajax'] = true;
	$response = array('status' => 'success');
	ob_start();
	thegem_blog($data);
	$response['html'] = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
	$response = json_encode($response);
	// header( "Content-Type: application/json" );
	echo $response;
	exit;
}

function news_grid_load_more_callback() {
	$response = array();
	$data = isset($_POST['data']) ? $_POST['data'] : array();
	$data['is_ajax'] = true;
	$response = array('status' => 'success');
	ob_start();
	thegem_news_grid($data);
	$response['html'] = trim(preg_replace('/\s\s+/', '', ob_get_clean()));
	$response = json_encode($response);
	header( "Content-Type: application/json" );
	echo $response;
	exit;
}
add_action('wp_ajax_news_grid_load_more', 'news_grid_load_more_callback');
add_action('wp_ajax_nopriv_news_grid_load_more', 'news_grid_load_more_callback');
