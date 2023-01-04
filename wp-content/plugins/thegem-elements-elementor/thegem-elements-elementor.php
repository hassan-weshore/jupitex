<?php
/*
Plugin Name: TheGem Theme Elements (for Elementor)
Plugin URI: http://codex-themes.com/thegem/
Description: Extended features and functionality for TheGem theme, including rich collection of page elements for WPBakery Page Builder.
Author: Codex Themes
Version: 5.1.1
Author URI: http://codex-themes.com/thegem/
TextDomain: thegem
DomainPath: /languages
Elementor tested up to: 3.2.3
*/

if ( ! defined( 'THEGEM_ELEMENTS_ELEMENTOR' ) ) {
	define( 'THEGEM_ELEMENTS_ELEMENTOR', 1 );
}

add_action( 'plugins_loaded', 'thegem_load_textdomain' );
function thegem_load_textdomain() {
	load_plugin_textdomain( 'thegem', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

if(!function_exists('thegem_is_plugin_active')) {
	function thegem_is_plugin_active($plugin) {
		include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		return is_plugin_active($plugin);
	}
}

if(!function_exists('thegem_user_icons_info_link')) {
function thegem_user_icons_info_link($pack = '') {
	return esc_url(apply_filters('thegem_user_icons_info_link', get_template_directory_uri().'/fonts/icons-list-'.$pack.'.html', $pack));
}
}

/* Get theme option*/
if(!function_exists('thegem_get_current_language')) {
function thegem_get_current_language() {
	static $result;

	if (isset($result)) {
		return $result;
	}

	if(thegem_is_plugin_active('sitepress-multilingual-cms/sitepress.php') && defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE) {
		$result = ICL_LANGUAGE_CODE;
		return $result;
	}
	if(thegem_is_plugin_active('polylang/polylang.php') && pll_current_language('slug')) {
		$result = pll_current_language('slug');
		return $result;
	}

	$result = false;
	return $result;
}
}

if(!function_exists('thegem_get_default_language')) {
function thegem_get_default_language() {
	static $result;

	if (isset($result)) {
		return $result;
	}

	if(thegem_is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
		global $sitepress;
		if(is_object($sitepress) && $sitepress->get_default_language()) {
			$result = $sitepress->get_default_language();
			return $result;
		}
	}
	if(thegem_is_plugin_active('polylang/polylang.php') && pll_default_language('slug')) {
		$result = pll_default_language('slug');
		return $result;
	}

	$result = false;
	return $result;
}
}

if (!function_exists('thegem_lazy_loading_enqueue')) {
	function thegem_lazy_loading_enqueue() {
		wp_enqueue_script('thegem-lazy-loading');
		wp_enqueue_style('thegem-lazy-loading-animations');
	}
}

if (!function_exists('thegem_translated_options')) {
	function thegem_translated_options() {
		return apply_filters('thegem_translated_options', array(
			'footer_html', 'top_area_button_text', 'top_area_button_link', 'contacts_address', 'contacts_phone', 'contacts_fax', 'contacts_email', 'contacts_website', 'top_area_contacts_address', 'top_area_contacts_phone', 'top_area_contacts_fax', 'top_area_contacts_email', 'top_area_contacts_website', 'custom_footer',
			'product_archive_quick_view_text', 'product_archive_cart_button_text', 'product_archive_select_options_button_text', 'product_archive_more_button_text', 'product_archive_filter_by_categories_title', 'product_archive_filter_by_price_title', 'product_archive_filter_by_status_title', 'product_archive_filter_by_status_sale_text', 'product_archive_filter_by_status_stock_text', 'product_archive_filters_text_labels_all_text', 'product_archive_filters_text_labels_clear_text', 'product_archive_filters_text_labels_search_text', 'product_archive_filter_buttons_hidden_show_text', 'product_archive_filter_buttons_hidden_sidebar_title', 'product_archive_filter_buttons_hidden_filter_by_text', 'product_archive_added_cart_text', 'product_archive_added_wishlist_text', 'product_archive_removed_wishlist_text', 'product_archive_view_cart_button_text', 'product_archive_checkout_button_text', 'product_archive_view_wishlist_button_text', 'product_archive_not_found_text',
			'product_page_desc_review_description_title', 'product_page_desc_review_additional_info_title', 'product_page_desc_review_reviews_title', 'product_page_button_add_to_cart_text', 'product_page_button_clear_attributes_text', 'product_page_elements_reviews_text', 'product_page_elements_sku_title', 'product_page_elements_categories_title', 'product_page_elements_tags_title', 'product_page_elements_share_title', 'product_page_elements_upsell_title', 'product_page_elements_related_title'
		));
	}
}

if(!function_exists('thegem_get_option')) {
function thegem_get_option($name, $default = false, $ml_full = false, $clearCache = false) {
	static $ref_options;
	static $cache = [];

	if ($clearCache) {
		$ref_options = null;
		$cache = [];
	}
	$cacheKey = $name.'_'.$default.'_'.$ml_full;

	if (isset($cache[$cacheKey])) {
		return $cache[$cacheKey];
	}

	if (!isset($ref_options)) {
		$ref_options = get_option('thegem_theme_options');
	}
	$options = $ref_options;

	if(isset($options[$name])) {
		$ml_options = thegem_translated_options();
		if(in_array($name, $ml_options) && is_array($options[$name]) && !$ml_full) {
			if(thegem_get_current_language()) {
				if(isset($options[$name][thegem_get_current_language()])) {
					$options[$name] = $options[$name][thegem_get_current_language()];
				} elseif(thegem_get_default_language() && isset($options[$name][thegem_get_default_language()])) {
					$options[$name] = $options[$name][thegem_get_default_language()];
				} else {
					$options[$name] = '';
				}
			}else {
				$options[$name] = reset($options[$name]);
			}
		}
		$result = apply_filters('thegem_option_'.$name, $options[$name]);
		$cache[$cacheKey] = $result;
		return $result;
	}
	$result = apply_filters('thegem_option_'.$name, $default);
	$cache[$cacheKey] = $result;
	return $result;
}
}

/* USER ICON PACK */

if(!function_exists('thegem_icon_userpack_enabled')) {
function thegem_icon_userpack_enabled() {
	return apply_filters('thegem_icon_userpack_enabled', false);
}
}

if(!function_exists('thegem_icon_packs_select_array')) {
function thegem_icon_packs_select_array() {
	$packs = array('elegant' => __('Elegant', 'thegem'), 'material' => __('Material Design', 'thegem'), 'fontawesome' => __('FontAwesome', 'thegem'));
	if(thegem_icon_userpack_enabled()) {
		$packs['userpack'] = __('UserPack', 'thegem');
	}
	return $packs;
}
}

if(!function_exists('thegem_icon_packs_infos')) {
function thegem_icon_packs_infos() {
	ob_start();
?>
<?php _e('Enter icon code', 'thegem'); ?>.
<a class="gem-icon-info gem-icon-info-elegant" href="<?php echo thegem_user_icons_info_link('elegant'); ?>" onclick="tb_show('<?php _e('Icons info', 'thegem'); ?>', this.href+'?TB_iframe=true'); return false;"><?php _e('Show Elegant Icon Codes', 'thegem'); ?></a>
<a class="gem-icon-info gem-icon-info-material" href="<?php echo thegem_user_icons_info_link('material'); ?>" onclick="tb_show('<?php _e('Icons info', 'thegem'); ?>', this.href+'?TB_iframe=true'); return false;"><?php _e('Show Material Design Icon Codes', 'thegem'); ?></a>
<a class="gem-icon-info gem-icon-info-fontawesome" href="<?php echo thegem_user_icons_info_link('fontawesome'); ?>" onclick="tb_show('<?php _e('Icons info', 'thegem'); ?>', this.href+'?TB_iframe=true'); return false;"><?php _e('Show FontAwesome Icon Codes', 'thegem'); ?></a>
<?php if(thegem_icon_userpack_enabled()) : ?>
<a class="gem-icon-info gem-icon-info-userpack" href="<?php echo thegem_user_icons_info_link('userpack'); ?>" onclick="tb_show('<?php _e('Icons info', 'thegem'); ?>', this.href+'?TB_iframe=true'); return false;"><?php _e('Show UserPack Icon Codes', 'thegem'); ?></a>
<?php endif; ?>
<?php
	return ob_get_clean();
}
}


/* META BOXES */

if(!function_exists('thegem_print_select_input')) {
function thegem_print_select_input($values = array(), $value = '', $name = '', $id = '') {
	if(!is_array($values)) {
		$values = array();
	}
?>
	<select name="<?php echo esc_attr($name) ?>" id="<?php echo esc_attr($id); ?>" class="thegem-combobox">
		<?php foreach($values as $key => $title) : ?>
			<option value="<?php echo esc_attr($key); ?>" <?php selected($key, $value); ?>><?php echo esc_html($title); ?></option>
		<?php endforeach; ?>
	</select>
<?php
}
}

if(!function_exists('thegem_print_checkboxes')) {
function thegem_print_checkboxes($values = array(), $value = array(), $name = '', $id_prefix = '', $after = '') {
	if(!is_array($values)) {
		$values = array();
	}
	if(!is_array($value)) {
		$value = array();
	}
?>
	<?php foreach($values as $key => $title) : ?>
		<input name="<?php echo esc_attr($name); ?>" type="checkbox" id="<?php echo esc_attr($id_prefix.'-'.$key); ?>" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $value), 1); ?> />
		<label for="<?php echo esc_attr($id_prefix.'-'.$key); ?>"><?php echo esc_html($title); ?></label>
		<?php echo $after; ?>
	<?php endforeach; ?>
<?php
}
}

if(!function_exists('thegem_add_srcset_rule')) {
	function thegem_add_srcset_rule(&$srcset, $condition, $size, $id=false) {
		if (!$id) {
			$id = get_post_thumbnail_id();
		}
		$im = thegem_generate_thumbnail_src($id, $size);
		$srcset[$condition] = $im[0];
	}
}

if(!function_exists('thegem_srcset_generate_urls')) {
	function thegem_srcset_generate_urls($attachment_id, $srcset) {
		$result = array();
		$thegem_sizes = array_keys(thegem_image_sizes());
		foreach ($srcset as $condition => $size) {
			if (!in_array($size, $thegem_sizes)) {
				continue;
			}
			$im = thegem_generate_thumbnail_src($attachment_id, $size);
			$result[$condition] = esc_url($im[0]);
		}
		return $result;
	}
}

if(!function_exists('thegem_srcset_list_to_string')) {
	function thegem_srcset_list_to_string($srcset) {
		if (count($srcset) == 0) {
			return '';
		}
		$srcset_condtions = array();
		foreach ($srcset as $condition => $url) {
			$srcset_condtions[] = $url . ' ' . $condition;
		}
		return implode(', ', $srcset_condtions);
	}
}

if(!function_exists('thegem_generate_picture_sources')) {
	function thegem_generate_picture_sources($attachment_id, $sources) {
		if (!$sources) {
			return '';
		}
		?>
		<?php foreach ($sources as $source): ?>
			<?php
				$srcset = thegem_srcset_generate_urls($attachment_id, $source['srcset']);
				if (!$srcset) {
					continue;
				}
			?>
			<source srcset="<?php echo thegem_srcset_list_to_string($srcset); ?>" <?php if(!empty($source['media'])): ?>media="<?php echo esc_attr($source['media']); ?>"<?php endif; ?> <?php if(!empty($source['type'])): ?>type="<?php echo esc_attr($source['type']); ?>"<?php endif; ?> sizes="<?php echo !empty($source['sizes']) ? esc_attr($source['sizes']) : '100vw'; ?>">
		<?php endforeach; ?>
		<?php
	}
}

if(!function_exists('thegem_generate_picture')) {
	function thegem_generate_picture($attachment_id, $default_size, $sources=array(), $attrs=array(), $return_info=false) {
		if (!$attachment_id || !in_array($default_size, array_keys(thegem_image_sizes()))) {
			return '';
		}
		$default_image = thegem_generate_thumbnail_src($attachment_id, $default_size);
		if (!$default_image) {
			return '';
		}
		list($src, $width, $height) = $default_image;
		$hwstring = image_hwstring($width, $height);

		$default_attrs = array('class' => "attachment-$default_size");
		if (empty($attrs['alt'])) {
			$attachment = get_post($attachment_id);
			$attrs['alt'] = trim(strip_tags(get_post_meta($attachment_id, '_wp_attachment_image_alt', true)));
			if(empty($default_attr['alt']))
				$attrs['alt'] = trim(strip_tags($attachment->post_excerpt));
			if(empty($default_attr['alt']))
				$attrs['alt'] = trim(strip_tags($attachment->post_title));
		}

		$attrs = wp_parse_args($attrs, $default_attrs);
		$attrs = array_map('esc_attr', $attrs);
		$attrs_set = array();
		foreach ($attrs as $attr_key => $attr_value) {
			$attrs_set[] = $attr_key . '="' . $attr_value . '"';
		}
		?>
		<picture>
			<?php thegem_generate_picture_sources($attachment_id, $sources); ?>
			<img src="<?php echo $src; ?>" <?php echo $hwstring; ?> <?php echo implode(' ', $attrs_set); ?> />
		</picture>
		<?php
		if ($return_info) {
			return array(
				'default' => $default_image
			);
		}
	}
}

add_filter('wp_lazy_loading_enabled', function() { return false; } );

/*if(!function_exists('thegem_check_array_value')) {
function thegem_check_array_value($array = array(), $value = '', $default = '') {
	if(in_array($value, $array)) {
		return $value;
	}
	return $default;
}
}*/

/* FONTS MANAGER */

function thegem_fonts_allowed_mime_types( $existing_mimes = array() ) {
	$existing_mimes['ttf'] = 'application/x-font-ttf';
	$existing_mimes['eot'] = 'application/vnd.ms-fontobject';
	$existing_mimes['woff'] = 'application/x-font-woff';
	$existing_mimes['svg'] = 'image/svg+xml';
	$existing_mimes['json'] = 'application/json';
	return $existing_mimes;
}
add_filter('upload_mimes', 'thegem_fonts_allowed_mime_types');

function thegem_modify_post_mime_types( $post_mime_types ) {
	$post_mime_types['application/x-font-ttf'] = array(esc_html__('TTF Font', 'thegem'), esc_html__( 'Manage TTFs', 'thegem' ), _n_noop( 'TTF <span class="count">(%s)</span>', 'TTFs <span class="count">(%s)</span>', 'thegem' ) );
	$post_mime_types['application/vnd.ms-fontobject'] = array(esc_html__('EOT Font', 'thegem'), esc_html__( 'Manage EOTs', 'thegem' ), _n_noop( 'EOT <span class="count">(%s)</span>', 'EOTs <span class="count">(%s)</span>', 'thegem' ) );
	$post_mime_types['application/x-font-woff'] = array(esc_html__('WOFF Font', 'thegem'), esc_html__( 'Manage WOFFs', 'thegem' ), _n_noop( 'WOFF <span class="count">(%s)</span>', 'WOFFs <span class="count">(%s)</span>', 'thegem' ) );
	$post_mime_types['image/svg+xml'] = array(esc_html__('SVG Font', 'thegem'), esc_html__( 'Manage SVGs', 'thegem' ), _n_noop( 'SVG <span class="count">(%s)</span>', 'SVGs <span class="count">(%s)</span>', 'thegem' ) );
	return $post_mime_types;
}
add_filter('post_mime_types', 'thegem_modify_post_mime_types');

/* SCRTIPTs & STYLES */

function thegem_elements_scripts() {
	$theme_uri = get_template_directory_uri();
	wp_register_style('thegem-portfolio-products', $theme_uri . '/css/thegem-portfolio-products.css');

	wp_register_style('thegem-news-grid-version-new-hovers-default', $theme_uri . '/css/thegem-news-grid-version-new/default.css');
	wp_register_style('thegem-news-grid-version-new-hovers-zooming-blur', $theme_uri . '/css/thegem-news-grid-version-new/zooming-blur.css');
	wp_register_style('thegem-news-grid-version-new-hovers-horizontal-sliding', $theme_uri . '/css/thegem-news-grid-version-new/horizontal-sliding.css');
	wp_register_style('thegem-news-grid-version-new-hovers-vertical-sliding', $theme_uri . '/css/thegem-news-grid-version-new/vertical-sliding.css');
	wp_register_style('thegem-news-grid-version-new-hovers-gradient', $theme_uri . '/css/thegem-news-grid-version-new/gradient.css');
	wp_register_style('thegem-news-grid-version-new-hovers-circular', $theme_uri . '/css/thegem-news-grid-version-new/circular.css');

	wp_register_style('thegem-news-grid-version-default-hovers-default', $theme_uri . '/css/thegem-news-grid-version-default/default.css');
	wp_register_style('thegem-news-grid-version-default-hovers-zooming-blur', $theme_uri . '/css/thegem-news-grid-version-default/zooming-blur.css');
	wp_register_style('thegem-news-grid-version-default-hovers-horizontal-sliding', $theme_uri . '/css/thegem-news-grid-version-default/horizontal-sliding.css');
	wp_register_style('thegem-news-grid-version-default-hovers-vertical-sliding', $theme_uri . '/css/thegem-news-grid-version-default/vertical-sliding.css');
	wp_register_style('thegem-news-grid-version-default-hovers-gradient', $theme_uri . '/css/thegem-news-grid-version-default/gradient.css');
	wp_register_style('thegem-news-grid-version-default-hovers-circular', $theme_uri . '/css/thegem-news-grid-version-default/circular.css');

	wp_register_script('raphael', $theme_uri . '/js/raphael.js', array('jquery'), false, true);
	wp_register_script('thegem-news-carousel', $theme_uri . '/js/news-carousel.js', array('jquery', 'jquery-carouFredSel'), false, true);
	wp_register_script('thegem-portfolio-grid-carousel', $theme_uri . '/js/portfolio-grid-carousel.js', array('jquery', 'jquery-carouFredSel'), false, true);
	wp_register_script('thegem-widgets', $theme_uri . '/js/widgets.js', array('jquery', 'jquery-carouFredSel'), false, true);
	wp_register_script('jquery-restable', $theme_uri . '/js/jquery.restable.js', array('jquery'), false, true);
	wp_register_script('thegem-parallax-vertical', $theme_uri . '/js/jquery.parallaxVertical.js', array('jquery'), false, true);
	wp_register_script('thegem-parallax-horizontal', $theme_uri . '/js/jquery.parallaxHorizontal.js', array('jquery'), false, true);
	wp_register_style('nivo-slider', $theme_uri . '/css/nivo-slider.css', array());
	wp_register_script('jquery-nivoslider', $theme_uri . '/js/jquery.nivo.slider.pack.js', array('jquery'));
	wp_register_script('thegem-nivoslider-init-script', $theme_uri . '/js/nivoslider-init.js', array('jquery', 'jquery-nivoslider'));
	wp_localize_script('thegem-nivoslider-init-script', 'thegem_nivoslider_options', array(
		'effect' => thegem_get_option('slider_effect') ? thegem_get_option('slider_effect') : 'random',
		'slices' => thegem_get_option('slider_slices') ? thegem_get_option('slider_slices') : 15,
		'boxCols' => thegem_get_option('slider_boxCols') ? thegem_get_option('slider_boxCols') : 8,
		'boxRows' => thegem_get_option('slider_boxRows') ? thegem_get_option('slider_boxRows') : 4,
		'animSpeed' => thegem_get_option('slider_animSpeed') ? thegem_get_option('slider_animSpeed')*100 : 500,
		'pauseTime' => thegem_get_option('slider_pauseTime') ? thegem_get_option('slider_pauseTime')*1000 : 3000,
		'directionNav' => thegem_get_option('slider_directionNav') ? true : false,
		'controlNav' => thegem_get_option('slider_controlNav') ? true : false,
	));
	wp_register_script('thegem-isotope-metro', $theme_uri . '/js/isotope_layout_metro.js', array('isotope-js'), '', true);
	wp_register_script('thegem-isotope-masonry-custom', $theme_uri . '/js/isotope-masonry-custom.js', array('isotope-js'), '', true);
	wp_register_script('jquery-waypoints', $theme_uri . '/js/jquery.waypoints.js', array('jquery'), false, true);
	wp_register_script('thegem-stickyColumn', $theme_uri . '/js/thegem-stickyColumn.js', array('jquery', 'jquery-waypoints'), '', true);
}
add_action('wp_enqueue_scripts', 'thegem_elements_scripts', 6);

function thegem_nonce_life() {
	return 31536000;
}
add_filter('nonce_life', 'thegem_nonce_life');

function thegem_fix_pw_init() {
	if(defined('PAGE_WIDGET_VERSION')) {
		remove_action('admin_print_scripts', 'pw_print_scripts');
		add_action('admin_print_scripts', 'thegem_pw_print_scripts');
	}
}
add_filter('init', 'thegem_fix_pw_init');

function thegem_pw_print_scripts() {

	global $pagenow, $typenow;

	$plugins_url = plugins_url();

	if (function_exists('pw_backend_check_allow_continue_process') && pw_backend_check_allow_continue_process()) {

		do_action( 'admin_print_scripts-widgets.php' );

		/* Plugin support */

		// Image widget support
		if (is_plugin_active('image-widget/image-widget.php')) {
			wp_enqueue_script('tribe-image-widget', $plugins_url . '/image-widget/resources/js/image-widget.js', array('jquery', 'media-upload', 'media-views'), false, true);
			wp_localize_script( 'tribe-image-widget', 'TribeImageWidget', array(
			'frame_title' => __( 'Select an Image', 'image_widget' ),
			'button_title' => __( 'Insert Into Widget', 'image_widget' ),
			) );
		}

		// Simple Link List Widget plugin support/
		if (is_plugin_active('simple-link-list-widget/simple-link-list-widget.php')) {
			wp_enqueue_script( 'sllw-sort-js', $plugins_url .'/simple-link-list-widget/js/sllw-sort.js');
		}

		// Easy releated posts and Simple social icons support.
		if (
			is_plugin_active('easy-related-posts/easy_related_posts.php')
			||
			is_plugin_active('simple-social-icons/simple-social-icons.php')
		) {
			wp_enqueue_script( 'wp-color-picker');
		}


		wp_enqueue_script('pw-widgets', $plugins_url . '/wp-page-widget/assets/js/page-widgets.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable'), rand(), true);


		/*
		* Add pwTextWidgets extend from wp.textWidgets
		* Add pwMediaWidgets extend from wp.mediaWidgets
		*/
		if( version_compare( get_bloginfo('version'), '4.7.9', '>' ) ) {
			wp_enqueue_script('pw-extend-text-widgets', $plugins_url . '/wp-page-widget/assets/js/pw-text-widgets-extend-wp-text-widgets.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable'), PAGE_WIDGET_VERSION, true);
			wp_enqueue_script('pw-extend-media-widgets', $plugins_url . '/wp-page-widget/assets/js/pw-media-widgets-extend-wp-media-widgets.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable'), PAGE_WIDGET_VERSION, true);
		}

		/*
		* Add pwCustomHTML extend from wp.customHtmlWidgets
		*/
		if( version_compare( get_bloginfo('version'), '4.8.5', '>' ) ) {
			wp_enqueue_script('pw-extend-custom-html', $plugins_url . '/wp-page-widget/assets/js/pw-custom-html-extend-wp-custom-html.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable'), PAGE_WIDGET_VERSION, true);

			$settings = wp_enqueue_code_editor( array(
				'type' => 'text/html',
				'codemirror' => array(
					'indentUnit' => 2,
					'tabSize' => 2,
				),
			));

			if ( empty( $settings ) ) {
				$settings = array(
					'disabled' => true,
				);
			}
			wp_add_inline_script( 'pw-extend-custom-html', sprintf( 'pwCustomHTML.init( %s );', wp_json_encode( $settings ) ), 'after' );
		}

		wp_localize_script( 'pw-widgets', 'wp_page_widgets', array(
			'remove_inactive_widgets_text'  => __('Press the following button will remove all of these inactive widgets', 'wp-page-widgets'),
			'remove_inactive_widgets' => __( 'Remove inactive widgets', 'wp-page-widgets' ),
		) );
	}
}

function thegem_info_message_notice() {
	if ( !current_user_can('update_themes' ) )
		return false;

	$message_data = get_option('thegem_info_message', array());
	$notice_class = '';
	$notice_html = '';
	$notice_until = 0;
	if(is_array($message_data) and !empty($message_data['last_check'])) {
		$last_check = intval($message_data['last_check']);
	} else {
		$last_check = 0;
	}

	if((time()-$last_check)/3600 > 24) {
		$last_check = time();
		$response = wp_remote_get('http://democontent.codex-themes.com/plugins/thegem/theme/message.json', array('timeout' => 5));
		if ( is_wp_error( $response ) ) {
			update_option('thegem_info_message', array('last_check' => $last_check));
			return false;
		}
		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, 1);
		if ( ! isset( $data['class'] ) ) {
			update_option('thegem_info_message', array('last_check' => $last_check));
			return false;
		}
		$notice_class = $data['class'];
		$notice_html = isset($data['html']) ? $data['html'] : '';
		$notice_until = isset($data['until']) ? intval($data['until']) : $last_check+24*60*60;
	} else {
		if(!empty($message_data['class']) && !empty($message_data['html'])) {
			$notice_class = $message_data['class'];
			$notice_html = $message_data['html'];
			$notice_until = isset($message_data['until']) ? intval($message_data['until']) : $last_check+24*60*60;
		}
	}

	update_option('thegem_info_message', array(
		'last_check' => $last_check,
		'class' => $notice_class,
		'html' => $notice_html,
		'until' => $notice_until,
	));

	if(!empty($notice_class) && !empty($notice_html) && $notice_until > time()) {
		echo '<div class="thegem-news-notice notice '.esc_attr($notice_class).' is-dismissible">';
		echo wp_kses_post($notice_html);
		echo '</div>';
	}
}

function thegem_plugin_update_notice() {
	if ( !current_user_can('update_themes' ) )
		return false;
	if ( !isset($themes_update) )
		$themes_update = get_site_transient('update_themes');
	if ( isset($themes_update->response['thegem-elementor']) ) {
		$update = $themes_update->response['thegem-elementor'];
		$theme = wp_prepare_themes_for_js( array( wp_get_theme('thegem-elementor') ) );
		$details_url = add_query_arg(array(), $update['url']);
		$update_url = wp_nonce_url( admin_url( 'update.php?action=upgrade-theme&amp;theme=' . urlencode( 'thegem-elementor' ) ), 'upgrade-theme_thegem-elementor' );
		if(isset($theme[0]) && isset($theme[0]['hasUpdate']) && $theme[0]['hasUpdate'] && version_compare( $theme[0]['version'], '5.0.0', '<' )) {
			wp_enqueue_script('jquery-fancybox');
			wp_enqueue_style('jquery-fancybox');
			echo '<div class="thegem-update-notice notice notice-warning is-dismissible">';
			echo '<p>'.sprintf(wp_kses(__('There is a new version of TheGem theme available. Your current version is <strong>%s</strong>. Update to <strong>%s</strong>.', 'thegem'), array('strong' => array())), $theme[0]['version'], $update['new_version']).'</p>';
			echo '<p>'.sprintf(wp_kses(__('<strong><a href="%s" class="thegem-view-details-link">View update details</a></strong> or <strong><a href="%s" class="thegem-update-link">Update now</a></strong>.', 'thegem'), array('strong' => array(), 'a' => array('href' => array(), 'class' => array()))), $details_url, $update_url).'</p>';
			echo '</div>';
		}
	}
}
add_action('admin_notices', 'thegem_plugin_update_notice');

$thegem_plugin_file = __FILE__;

add_action('admin_notices', 'thegem_info_message_notice');

require_once(plugin_dir_path( __FILE__ ) . 'inc/content.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/remote_media_upload.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/diagram.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/post-types/init.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/widgets/init.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/gdpr/gdpr.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/blocks-helper/index.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/elementor/elementor.php');
