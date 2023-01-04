<?php

function thegem_testimonials_post_type_init() {
	$labels = array(
		'name'               => __('Testimonials', 'thegem'),
		'singular_name'      => __('Testimonial', 'thegem'),
		'menu_name'          => __('Testimonials', 'thegem'),
		'name_admin_bar'     => __('Testimonial', 'thegem'),
		'add_new'            => __('Add New', 'thegem'),
		'add_new_item'       => __('Add New Testimonial', 'thegem'),
		'new_item'           => __('New Testimonial', 'thegem'),
		'edit_item'          => __('Edit Testimonial', 'thegem'),
		'view_item'          => __('View Testimonial', 'thegem'),
		'all_items'          => __('All Testimonials', 'thegem'),
		'search_items'       => __('Search Testimonials', 'thegem'),
		'not_found'          => __('No testimonials found.', 'thegem'),
		'not_found_in_trash' => __('No testimonials found in Trash.', 'thegem')
	);

	$args = array(
		'labels'               => $labels,
		'public'               => false,
		'exclude_from_search'  => true,
		'publicly_queryable'   => false,
		'show_ui'              => true,
		'query_var'            => false,
		'hierarchical'         => false,
		'supports'             => array('title', 'editor', 'thumbnail', 'page-attributes'),
		'register_meta_box_cb' => 'thegem_testimonials_register_meta_box',
		'taxonomies'           => array('thegem_testimonials_sets'),
		'menu_position'        => 37
	);

	register_post_type('thegem_testimonial', $args);

	$labels = array(
		'name'                       => __('Testimonials Sets', 'thegem'),
		'singular_name'              => __('Testimonials Set', 'thegem'),
		'search_items'               => __('Search Testimonials Sets', 'thegem'),
		'popular_items'              => __('Popular Testimonials Sets', 'thegem'),
		'all_items'                  => __('All Testimonials Sets', 'thegem'),
		'edit_item'                  => __('Edit Testimonials Set', 'thegem'),
		'update_item'                => __('Update Testimonials Set', 'thegem'),
		'add_new_item'               => __('Add New Testimonials Set', 'thegem'),
		'new_item_name'              => __('New Testimonials Set Name', 'thegem'),
		'separate_items_with_commas' => __('Separate Testimonials Sets with commas', 'thegem'),
		'add_or_remove_items'        => __('Add or remove Testimonials Sets', 'thegem'),
		'choose_from_most_used'      => __('Choose from the most used Testimonials Sets', 'thegem'),
		'not_found'                  => __('No testimonials sets found.', 'thegem'),
		'menu_name'                  => __('Testimonials Sets', 'thegem'),
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

	register_taxonomy('thegem_testimonials_sets', 'thegem_testimonial', $args);
}
add_action('init', 'thegem_testimonials_post_type_init');

/* CLIENT POST META BOX */

function thegem_testimonials_register_meta_box($post) {
	add_meta_box('thegem_testimonial_settings', __('Testimonial Settings', 'thegem'), 'thegem_testimonial_settings_box', 'thegem_testimonial', 'normal', 'high');
}

function thegem_testimonial_settings_box($post) {
	wp_nonce_field('thegem_testimonial_settings_box', 'thegem_testimonial_settings_box_nonce');
	$testimonial_data = thegem_get_sanitize_testimonial_data($post->ID);
?>
<p class="meta-options">
	<label for="testimonial_name"><?php _e('Name', 'thegem'); ?>:</label><br />
	<input name="thegem_testimonial_data[name]" type="text" id="testimonial_name" value="<?php echo esc_attr($testimonial_data['name']); ?>" size="60" /><br />
	<br />
	<label for="testimonial_company"><?php _e('Company', 'thegem'); ?>:</label><br />
	<input name="thegem_testimonial_data[company]" type="text" id="testimonial_company" value="<?php echo esc_attr($testimonial_data['company']); ?>" size="60" /><br />
	<br />
	<label for="testimonial_position"><?php _e('Position', 'thegem'); ?>:</label><br />
	<input name="thegem_testimonial_data[position]" type="text" id="testimonial_position" value="<?php echo esc_attr($testimonial_data['position']); ?>" size="60" /><br />
	<br />
	<?php /*<label for="testimonial_phone"><?php _e('Phone', 'thegem'); ?>:</label><br />
	<input name="thegem_testimonial_data[phone]" type="text" id="testimonial_phone" value="<?php echo $testimonial_data['phone']; ?>" size="60" /><br />
	<br />*/ ?>
	<label for="testimonial_link"><?php _e('Link', 'thegem'); ?>:</label><br />
	<input name="thegem_testimonial_data[link]" type="text" id="testimonial_link" value="<?php echo esc_attr($testimonial_data['link']); ?>" size="60" /><br />
	<br />
	<label for="testimonial_link_target"><?php _e('Link target', 'thegem'); ?>:</label><br />
	<?php thegem_print_select_input(array('_blank' => 'Blank', '_self' => 'Self'), $testimonial_data['link_target'], 'thegem_testimonial_data[link_target]', 'testimonial_link_target'); ?>
</p>
<?php
}

function thegem_testimonial_save_meta_box_data($post_id) {
	if(!isset($_POST['thegem_testimonial_settings_box_nonce'])) {
		return;
	}
	if(!wp_verify_nonce($_POST['thegem_testimonial_settings_box_nonce'], 'thegem_testimonial_settings_box')) {
		return;
	}
	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if(isset($_POST['post_type']) && 'thegem_testimonial' == $_POST['post_type']) {
		if(!current_user_can('edit_page', $post_id)) {
			return;
		}
	} else {
		if(!current_user_can('edit_post', $post_id)) {
			return;
		}
	}

	if(!isset($_POST['thegem_testimonial_data']) || !is_array($_POST['thegem_testimonial_data'])) {
		return;
	}

	$testimonial_data = thegem_get_sanitize_testimonial_data(0, $_POST['thegem_testimonial_data']);
	update_post_meta($post_id, 'thegem_testimonial_data', $testimonial_data);
}
add_action('save_post', 'thegem_testimonial_save_meta_box_data');

function thegem_get_sanitize_testimonial_data($post_id = 0, $item_data = array()) {
	$testimonial_data = array(
		'name' => '',
		'company' => '',
		'position' => '',
		'link' => '',
		'link_target' => ''
	);
	if(is_array($item_data) && !empty($item_data)) {
		$testimonial_data = array_merge($testimonial_data, $item_data);
	} elseif($post_id != 0 && function_exists('thegem_get_post_data')) {
		$testimonial_data = thegem_get_post_data($testimonial_data, 'testimonial', $post_id);
	}
	$testimonial_data['name'] = sanitize_text_field($testimonial_data['name']);
	$testimonial_data['company'] = sanitize_text_field($testimonial_data['company']);
	$testimonial_data['position'] = sanitize_text_field($testimonial_data['position']);
	$testimonial_data['link'] = esc_url($testimonial_data['link']);
	$testimonial_data['link_target'] = thegem_check_array_value(array('_blank', '_self'), $testimonial_data['link_target'], '_blank');
	return $testimonial_data;
}