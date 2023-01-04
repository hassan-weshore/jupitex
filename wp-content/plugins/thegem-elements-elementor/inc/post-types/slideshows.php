<?php

function thegem_slides_post_type_init() {
	if(!thegem_get_option('activate_nivoslider')) return ;
	$labels = array(
		'name'               => __('Slides', 'thegem'),
		'singular_name'      => __('Slideshow Slide', 'thegem'),
		'menu_name'          => __('NivoSlider', 'thegem'),
		'name_admin_bar'     => __('Slideshow Slide', 'thegem'),
		'add_new'            => __('Add New', 'thegem'),
		'add_new_item'       => __('Add New Slide', 'thegem'),
		'new_item'           => __('New Slide', 'thegem'),
		'edit_item'          => __('Edit Slide', 'thegem'),
		'view_item'          => __('View Slide', 'thegem'),
		'all_items'          => __('All Slides', 'thegem'),
		'search_items'       => __('Search Slides', 'thegem'),
		'not_found'          => __('No slides found.', 'thegem'),
		'not_found_in_trash' => __('No slides found in Trash.', 'thegem')
	);

	$args = array(
		'labels'               => $labels,
		'public'               => false,
		'exclude_from_search'  => true,
		'publicly_queryable'   => false,
		'show_ui'              => true,
		'query_var'            => false,
		'hierarchical'         => false,
		'supports'             => array('title', 'thumbnail', 'excerpt', 'page-attributes'),
		'register_meta_box_cb' => 'thegem_slides_register_meta_box',
		'taxonomies'           => array('thegem_slideshows'),
		'menu_position'        => 40
	);

	register_post_type('thegem_slide', $args);

	$labels = array(
		'name'                       => __('Slideshows', 'thegem'),
		'singular_name'              => __('Slideshow', 'thegem'),
		'search_items'               => __('Search Slideshows', 'thegem'),
		'popular_items'              => __('Popular Slideshows', 'thegem'),
		'all_items'                  => __('All Slideshows', 'thegem'),
		'edit_item'                  => __('Edit Slideshow', 'thegem'),
		'update_item'                => __('Update Slideshow', 'thegem'),
		'add_new_item'               => __('Add New Slideshow', 'thegem'),
		'new_item_name'              => __('New Slideshow Name', 'thegem'),
		'separate_items_with_commas' => __('Separate Slideshows with commas', 'thegem'),
		'add_or_remove_items'        => __('Add or remove Slideshows', 'thegem'),
		'choose_from_most_used'      => __('Choose from the most used Slideshows', 'thegem'),
		'not_found'                  => __('No slideshows found.', 'thegem'),
		'menu_name'                  => __('Slideshows', 'thegem'),
	);

	$args = array(
		'hierarchical'          => true,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => false,
		'public'                => false,
		'rewrite'               => false,
	);

	register_taxonomy('thegem_slideshows', 'thegem_slide', $args);
}
add_action('init', 'thegem_slides_post_type_init');

/* SLIDE POST META BOX */

function thegem_slides_register_meta_box($post) {
	remove_meta_box('postimagediv', 'thegem_slide', 'side');
	add_meta_box('postimagediv', __('Slide Image', 'thegem'), 'post_thumbnail_meta_box', 'thegem_slide', 'normal', 'high');
	add_meta_box('thegem_slide_settings', __('Slide Settings', 'thegem'), 'thegem_slide_settings_box', 'thegem_slide', 'normal', 'high');
}

function thegem_slide_settings_box($post) {
	wp_nonce_field('thegem_slide_settings_box', 'thegem_slide_settings_box_nonce');
	$slide_data = thegem_get_sanitize_slide_data($post->ID);
	$slide_text_position_options = array('' => __('None', 'thegem'), 'left' => __('Left', 'thegem'), 'right' => __('Right', 'thegem'));
?>
<p class="meta-options">
	<label for="slide_link"><?php _e('Link', 'thegem'); ?>:</label><br />
	<input name="thegem_slide_data[link]" type="text" id="slide_link" value="<?php echo esc_attr($slide_data['link']); ?>" size="60" /><br />
	<br />
	<label for="slide_link_target"><?php _e('Link target', 'thegem'); ?>:</label><br />
	<?php thegem_print_select_input(array('_self' => 'Self', '_blank' => 'Blank'), $slide_data['link_target'], 'thegem_slide_data[link_target]', 'slide_link_target'); ?><br />
	<br />
	<label for="slide_text_position"><?php _e('Caption position', 'thegem'); ?>:</label><br />
	<?php thegem_print_select_input($slide_text_position_options, $slide_data['text_position'], 'thegem_slide_data[text_position]', 'slide_text_position'); ?>
</p>
<?php
}

function thegem_slide_save_meta_box_data($post_id) {
	if(!isset($_POST['thegem_slide_settings_box_nonce'])) {
		return;
	}
	if(!wp_verify_nonce($_POST['thegem_slide_settings_box_nonce'], 'thegem_slide_settings_box')) {
		return;
	}
	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if(isset($_POST['post_type']) && 'thegem_slide' == $_POST['post_type']) {
		if(!current_user_can('edit_page', $post_id)) {
			return;
		}
	} else {
		if(!current_user_can('edit_post', $post_id)) {
			return;
		}
	}

	if(!isset($_POST['thegem_slide_data']) || !is_array($_POST['thegem_slide_data'])) {
		return;
	}

	$slide_data = thegem_get_sanitize_slide_data(0, $_POST['thegem_slide_data']);
	update_post_meta($post_id, 'thegem_slide_data', $slide_data);
}
add_action('save_post', 'thegem_slide_save_meta_box_data');

function thegem_get_sanitize_slide_data($post_id = 0, $item_data = array()) {
	$slide_data = array(
		'link' => '',
		'link_target' => '',
		'text_position' => ''
	);
	if(is_array($item_data) && !empty($item_data)) {
		$slide_data = array_merge($slide_data, $item_data);
	} elseif($post_id != 0 && function_exists('thegem_get_post_data')) {
		$slide_data = thegem_get_post_data($slide_data, 'slide', $post_id);
	}
	$slide_data['link'] = esc_url($slide_data['link']);
	$slide_data['link_target'] = thegem_check_array_value(array('_blank', '_self'), $slide_data['link_target'], '_self');
	$slide_data['text_position'] = thegem_check_array_value(array('', 'left', 'right'), $slide_data['text_position'], '');
	return $slide_data;
}