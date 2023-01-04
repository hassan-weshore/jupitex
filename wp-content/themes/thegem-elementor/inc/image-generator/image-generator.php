<?php

function thegem_get_image_regenerated_option_key() {
	return 'thegem_image_regenerated';
}

function thegem_get_attachment_relative_path( $file ) {
	$dirname = dirname( $file );
	$uploads = wp_upload_dir();

	if ( '.' === $dirname ) {
		return '';
	}

	return str_replace($uploads['basedir'], '', $dirname);
}

if(!function_exists('thegem_generate_thumbnail_src')) {

	function thegem_generate_thumbnail_src($attachment_id, $size) {
		$data = thegem_image_cache_get($attachment_id, $size);
		if ($data) {
			return $data;
		}

		$data = thegem_get_thumbnail_src($attachment_id, $size);
		thegem_image_cache_set($attachment_id, $size, $data);
		return $data;
	}

	function thegem_get_thumbnail_src($attachment_id, $size) {
		$thegem_image_sizes = thegem_image_sizes();

		if(isset($thegem_image_sizes[$size])) {
			$attachment_path = get_attached_file($attachment_id);
			if (!$attachment_path || !file_exists($attachment_path)) {
				$default_img = wp_get_attachment_image_src($attachment_id, $thegem_image_sizes[$size]);
				if(is_array($default_img)) {
					$default_img['not_generated'] = true;
				}
				return $default_img;
			}

			$dummy_image_editor = new TheGem_Dummy_WP_Image_Editor($attachment_path);
			$attachment_thumb_path = $dummy_image_editor->generate_filename($size);
			$attachment_thumb_path = apply_filters('thegem_attachment_thumbnail_path', $attachment_thumb_path, $attachment_id);

			if (!file_exists($attachment_thumb_path)) {
				$image_editor = wp_get_image_editor($attachment_path);
				if (!is_wp_error($image_editor) && !is_wp_error($image_editor->resize($thegem_image_sizes[$size][0], $thegem_image_sizes[$size][1], $thegem_image_sizes[$size][2]))) {
					$attachment_resized = $image_editor->save($attachment_thumb_path);
					if (!is_wp_error($attachment_resized) && $attachment_resized) {
						do_action('thegem_thumbnail_generated', array('/'._wp_relative_upload_path($attachment_thumb_path)));
						return thegem_build_image_result($attachment_resized['path'], $attachment_resized['width'], $attachment_resized['height']);
					} else {
						return thegem_build_image_data($attachment_path);
					}
				} else {
					return thegem_build_image_data($attachment_path);
				}
			}
			return thegem_build_image_data($attachment_thumb_path);
		}
		return wp_get_attachment_image_src($attachment_id, $size);
	}

	function thegem_build_image_data($path) {
		$editor = new TheGem_Dummy_WP_Image_Editor($path);
		$size = $editor->get_size();
		if (!$size) {
			return null;
		}
		return thegem_build_image_result($path, $size['width'], $size['height']);
	}

	function thegem_image_cache_get($attachment_id, $size) {
		global $thegem_image_src_cache, $thegem_image_regenerated;

		if (!$thegem_image_src_cache) {
			$thegem_image_src_cache = array();
		}

		if (isset($thegem_image_regenerated[$attachment_id]) &&
				isset($thegem_image_src_cache[$attachment_id][$size]['time']) &&
				$thegem_image_regenerated[$attachment_id] >= $thegem_image_src_cache[$attachment_id][$size]['time']) {
			return false;
		}

		if (!empty($thegem_image_src_cache[$attachment_id][$size])) {
			$data = $thegem_image_src_cache[$attachment_id][$size];
			unset($data['time']);
			return $data;
		}
		return false;
	}

	function thegem_image_cache_set($attachment_id, $size, $data) {
		global $thegem_image_src_cache, $thegem_image_src_cache_changed;

		if (!$thegem_image_src_cache) {
			$thegem_image_src_cache = array();
		}

		$data['time'] = time();
		$thegem_image_src_cache[$attachment_id][$size] = $data;
		$thegem_image_src_cache_changed = true;
	}

	function thegem_build_image_result($file, $width, $height) {
		$uploads = wp_upload_dir();
		$url = trailingslashit( $uploads['baseurl'] . thegem_get_attachment_relative_path( $file ) ) . basename( $file );
		if ( is_ssl() && ! is_admin() && 'wp-login.php' !== $GLOBALS['pagenow'] ) {
			$url = set_url_scheme( $url );
		}
		return array($url, $width, $height);
	}

	function thegem_get_image_cache_option_key_prefix() {
		return 'thegem_image_cache_';
	}

	function thegem_get_image_cache_option_key($url = '') {
		$url = preg_replace('%\?.*$%', '', empty($url) ? $_SERVER['REQUEST_URI'] : $url);
		return thegem_get_image_cache_option_key_prefix() . sha1($url);
	}

	function thegem_image_generator_cache_init() {
		global $thegem_image_src_cache, $thegem_image_src_cache_changed, $thegem_image_regenerated;

		$thegem_image_regenerated = get_option(thegem_get_image_regenerated_option_key());
		$thegem_image_regenerated = !empty($thegem_image_regenerated) ? (array) $thegem_image_regenerated : array();

		$cache = get_option(thegem_get_image_cache_option_key());
		$thegem_image_src_cache = !empty($cache) ? (array) $cache : array();

		$uploads = wp_upload_dir();
		if ( is_ssl() && ! is_admin() && 'wp-login.php' !== $GLOBALS['pagenow'] ) {
			$uploads['baseurl'] = set_url_scheme( $uploads['baseurl'] );
		}

		foreach ($thegem_image_src_cache as $attachment_id => $sizes) {
			if (!is_array($sizes)) {
				continue;
			}
			foreach ($sizes as $size => $size_data) {
				if (!is_array($size_data) || empty($size_data[0])) {
					continue;
				}
				$thegem_image_src_cache[$attachment_id][$size][0] = (empty($size_data['not_generated']) ? $uploads['baseurl'] : '') . $size_data[0];
			}
		}
		$thegem_image_src_cache_changed = false;
	}
	add_action('init', 'thegem_image_generator_cache_init');

	function thegem_image_generator_cache_save() {
		global $thegem_image_src_cache, $thegem_image_src_cache_changed;

		if (is_404() || !isset($thegem_image_src_cache_changed) || !$thegem_image_src_cache_changed) {
			return;
		}

		$uploads = wp_upload_dir();
		if ( is_ssl() && ! is_admin() && 'wp-login.php' !== $GLOBALS['pagenow'] ) {
			$uploads['baseurl'] = set_url_scheme( $uploads['baseurl'] );
		}

		foreach ($thegem_image_src_cache as $attachment_id => $sizes) {
			if (!is_array($sizes)) {
				continue;
			}
			foreach ($sizes as $size => $size_data) {
				if (!is_array($size_data) || empty($size_data[0])) {
					continue;
				}
				$thegem_image_src_cache[$attachment_id][$size][0] = str_replace($uploads['baseurl'], '', $size_data[0]);
			}
		}

		update_option(thegem_get_image_cache_option_key(), $thegem_image_src_cache, 'no');
	}
	add_action('wp_footer', 'thegem_image_generator_cache_save', 9999);

	function thegem_thumbnails_generator_page_row_actions($actions, $post) {
		if ( current_user_can( 'manage_options' ) ) {
			$actions = array_merge( $actions, array(
					'thegem_flush_post_thumbnails_cache' => sprintf( '<a href="%s">' . __( 'Purge Thumbnails Cache', 'thegem' ) . '</a>', wp_nonce_url( sprintf( 'admin.php?page=thegem-thumbnails&thegem_flush_post_thumbnails_cache&post_id=%d', $post->ID ), 'thegem-thumbnails-cache-flush' ) )
				) );
		}
		return $actions;
	}
	add_filter('page_row_actions', 'thegem_thumbnails_generator_page_row_actions', 0, 2);
	add_filter('post_row_actions', 'thegem_thumbnails_generator_page_row_actions', 0, 2);

	function thegem_theme_add_thumbnails_generator_page() {
		add_submenu_page(null, esc_html__('TheGem thumbnails','thegem'), esc_html__('TheGem thumbnails','thegem'), 'manage_options', 'thegem-thumbnails', 'thegem_thumbnails_generator_page');
	}
	add_action('admin_menu', 'thegem_theme_add_thumbnails_generator_page', 50);

	function thegem_thumbnails_generator_page() {
		global $wpdb;

		if ($_GET['page'] != 'thegem-thumbnails') {
			exit;
		}

		if (isset($_GET['thegem_flush_post_thumbnails_cache'])) {
			if (!empty($_GET['post_id']) && $url=get_permalink($_GET['post_id'])) {
				if (wp_verify_nonce($_GET['_wpnonce'], 'thegem-thumbnails-cache-flush')) {
					$option_key = thegem_get_image_cache_option_key(str_replace(home_url(), '', $url));
					delete_option($option_key);
					thegem_thumbnails_generator_redirect(array(
						'thegem_note' => 'flush-success'
					));
				} {
					thegem_thumbnails_generator_redirect(array(
						'thegem_note' => 'nonce-error'
					));
				}
			} else {
				thegem_thumbnails_generator_redirect(array(
					'thegem_note' => 'empty-post'
				));
			}
		}

		if (isset($_GET['thegem_flush_thumbnails_cache'])) {
			if (wp_verify_nonce($_GET['_wpnonce'], 'thegem-thumbnails-cache-flush-all')) {
				$prefix = thegem_get_image_cache_option_key_prefix();
				$wpdb->query("DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE '%{$prefix}%'");
				thegem_thumbnails_generator_redirect(array(
					'thegem_note' => 'flush-all-success'
				));
			} else {
				thegem_thumbnails_generator_redirect(array(
					'thegem_note' => 'nonce-error'
				));
			}
		}
	}
	add_action('load-admin_page_thegem-thumbnails', 'thegem_thumbnails_generator_page');

	function thegem_admin_bar_thumbnails_generator($wp_admin_bar) {
		if (!is_user_logged_in() || (!is_user_member_of_blog() && !is_super_admin())) {
			return;
		}

		$wp_admin_bar->add_menu(array(
			'id'	=> 'thegem-thumbnails-generator',
			'title' => 'Purge All Thumbnails Cache',
			'href'  => esc_url(admin_url(wp_nonce_url('admin.php?page=thegem-thumbnails&thegem_flush_thumbnails_cache', 'thegem-thumbnails-cache-flush-all'))),
		));
	}
	//add_action('admin_bar_menu', 'thegem_admin_bar_thumbnails_generator', 101);

	function thegem_thumbnails_generator_redirect($params = array()) {
		if (!empty($_SERVER['HTTP_REFERER'])) {
			$url = $_SERVER['HTTP_REFERER'];
		} else {
			$url = '/wp-admin/index.php';
		}
		$url = add_query_arg($params, $url);
		@header( 'Location: ' . $url );
		exit;
	}

	function thegem_thumbnails_generator_notes() {
		$notes = array(
			'flush-success' => array(
				'class' => 'updated',
				'notice' => __( 'Cached post thumbnails have been deleted successfully!', 'thegem' )
			),
			'flush-all-success' => array(
				'class' => 'updated',
				'notice' => __( 'All cached thumbnails have been deleted successfully!', 'thegem' )
			),
			'nonce-error' => array(
				'class' => 'error',
				'notice' => __( 'Nonce verification is faield!', 'thegem' )
			),
			'empty-post' => array(
				'class' => 'error',
				'notice' => __( 'Post not found', 'thegem' )
			)
		);

		if (!empty($_GET['thegem_note']) && !empty($notes[$_GET['thegem_note']])) {
			?>
			<div class="<?php echo $notes[$_GET['thegem_note']]['class']; ?>">
				<p><?php echo $notes[$_GET['thegem_note']]['notice']; ?></p>
			</div>
			<?php
		}
	}
	add_action('admin_notices', 'thegem_thumbnails_generator_notes');

}

?>
