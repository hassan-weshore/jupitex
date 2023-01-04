<?php

function thegem_get_theme_options() {
	_deprecated_function( __FUNCTION__, '4.6.0', 'thegem_get_font_options_list()' );
	return array('fonts' => array('subcats'=> array_fill_keys(thegem_get_font_options_list(), 1)));
}

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

function thegem_get_pages_list() {
	$pages = array('' => __('Default', 'thegem'));
	$pages_list = get_pages();
	foreach ($pages_list as $page) {
		$pages[$page->ID] = $page->post_title . ' (ID = ' . $page->ID . ')';
	}
	return $pages;
}

function thegem_color_skin_defaults() {
	$options = thegem_first_install_settings();
	$exclude = array(
		/* CONTACTS */
		'contacts_address', 'contacts_email', 'contacts_fax', 'contacts_phone',
		'contacts_website', 'footer_html', 'top_area_contacts_address',

		/* LOGO & FAVICON */
		'logo', 'logo_light', 'logo_light_selected_img_width', 'logo_selected_img_width', 'logo_width',
		'small_logo', 'small_logo_light', 'small_logo_light_selected_img_width', 'small_logo_selected_img_width', 'small_logo_width',
		'favicon',

		/* SOCIALS */
		'show_social_icons',
		'add_new_social',
		'askfm_active',
		'askfm_link',
		'blogger_active',
		'blogger_link',
		'delicious_active',
		'delicious_link',
		'deviantart_active',
		'deviantart_link',
		'discord_active',
		'discord_link',
		'dribbble_active',
		'dribbble_link',
		'facebook_active',
		'facebook_link',
		'flickr_active',
		'flickr_link',
		'googledrive_active',
		'googledrive_link',
		'instagram_active',
		'instagram_link',
		'linkedin_active',
		'linkedin_link',
		'meetup_active',
		'meetup_link',
		'myspace_active',
		'myspace_link',
		'ok_active',
		'ok_link',
		'picassa_active',
		'picassa_link',
		'pinterest_active',
		'pinterest_link',
		'qzone_active',
		'qzone_link',
		'reddit_active',
		'reddit_link',
		'rss_active',
		'rss_link',
		'share_active',
		'share_link',
		'skype_active',
		'skype_link',
		'slack_active',
		'slack_link',
		'soundcloud_active',
		'soundcloud_link',
		'spotify_active',
		'spotify_link',
		'stumbleupon_active',
		'stumbleupon_link',
		'telegram_active',
		'telegram_link',
		'tumblr_active',
		'tumblr_link',
		'twitter_active',
		'twitter_link',
		'viber_active',
		'viber_link',
		'vimeo_active',
		'vimeo_link',
		'vk_active',
		'vk_link',
		'weibo_active',
		'weibo_link',
		'whatsapp_active',
		'whatsapp_link',
		'wordpress_active',
		'wordpress_link',
		'youtube_active',
		'youtube_link',
		'tiktok_active',
		'tiktok_link',

		/* CUSTOM JS/CSS */
		'custom_css', 'custom_footer', 'custom_footer_enable', 'custom_js', 'custom_js_header',

		/* OTHER*/
		'news_rewrite_slug', 'portfolio_rewrite_slug', 'purchase_code',
	);
	foreach($exclude as $item) {
		unset($options[$item]);
	}
	$options = apply_filters('thegem_reset_defaults_options', $options);
	return $options;
}

function thegem_first_install_settings() {
	return apply_filters('thegem_default_theme_options', array(
		'404_page' => '',
		'activate_news_posttype' => '',
		'activate_nivoslider' => '',
		'active_link_color' => '#00bcd4',
		'add_new_social' => '',
		'askfm_active' => '',
		'askfm_link' => '#',
		'basic_outer_background_color' => '#f0f3f2',
		'basic_outer_background_gradient_angle' => '90',
		'basic_outer_background_gradient_point1_color' => '#181828FF',
		'basic_outer_background_gradient_point1_position' => '0',
		'basic_outer_background_gradient_point2_color' => '#474B62FF',
		'basic_outer_background_gradient_point2_position' => '100',
		'basic_outer_background_gradient_position' => 'center center',
		'basic_outer_background_gradient_type' => 'linear',
		'basic_outer_background_image' => '',
		'basic_outer_background_image_color' => '',
		'basic_outer_background_image_overlay' => '',
		'basic_outer_background_image_repeat' => '0',
		'basic_outer_background_pattern' => '',
		'basic_outer_background_position_x' => 'center',
		'basic_outer_background_position_y' => 'center',
		'basic_outer_background_size' => 'auto',
		'basic_outer_background_type' => 'color',
		'blockquote_icon_blockquotes' => '#A3E7F0FF',
		'blockquote_icon_testimonials' => '#A3E7F0FF',
		'blog_hide_author' => '',
		'blog_hide_categories' => '',
		'blog_hide_comments' => '',
		'blog_hide_date' => '',
		'blog_hide_date_in_blog_cat' => '',
		'blog_hide_likes' => '',
		'blog_hide_navigation' => '',
		'blog_hide_realted' => '',
		'blog_hide_socials' => '',
		'blog_hide_tags' => '',
		'blog_hide_social_sharing' => '0',
		'blogger_active' => '',
		'blogger_link' => '#',
		'body_color' => '#5f727f',
		'body_font_family' => 'Source Sans Pro',
		'body_font_sets' => '',
		'body_font_size' => '16',
		'body_font_style' => 'regular',
		'body_letter_spacing' => '0',
		'body_line_height' => '25',
		'body_text_transform' => '',
		'box_border_color' => '#dfe5e8',
		'breadcrumbs_active_color' => '#E7FF89FF',
		'breadcrumbs_default_color' => '#FFFFFFFF',
		'breadcrumbs_hover_color' => '#E7FF89FF',
		'bullets_symbol_color' => '#5f727f',
		'button_background_basic_color' => '#b6c6c9',
		'button_background_hover_color' => '#3c3950',
		'button_font_family' => 'Montserrat',
		'button_font_sets' => 'latin',
		'button_font_size' => '',
		'button_font_style' => '700',
		'button_letter_spacing' => '',
		'button_line_height' => '',
		'button_outline_border_basic_color' => '#00bcd4',
		'button_outline_text_basic_color' => '#00bcd4',
		'button_outline_text_hover_color' => '#ffffff',
		'button_text_basic_color' => '#ffffff',
		'button_text_hover_color' => '#ffffff',
		'button_text_transform' => 'uppercase',
		'button_thin_font_family' => 'Montserrat UltraLight',
		'button_thin_font_sets' => '',
		'button_thin_font_size' => '',
		'button_thin_font_style' => 'regular',
		'button_thin_letter_spacing' => '',
		'button_thin_line_height' => '',
		'button_thin_text_transform' => 'uppercase',
		'cart_form_labels_color' => '#5f727f',
		'cart_label_count' => '0',
		'cart_table_header_background_color' => '#B6C6C9FF',
		'cart_table_header_color' => '#FFFFFFFF',
		'catalog_view' => '',
		'checkout_step_background_active_color' => '#FFD453FF',
		'checkout_step_background_color' => '#E9F0EFFF',
		'checkout_step_title_active_color' => '#3C3950FF',
		'checkout_step_title_color' => '#99A9B5FF',
		'checkout_type' => 'multi-step',
		'circular_overlay_hover_angle' => '90',
		'circular_overlay_hover_point1_color' => 'rgba(0, 188, 212,0.75)',
		'circular_overlay_hover_point1_position' => '0',
		'circular_overlay_hover_point2_color' => 'rgba(53, 64, 147,0.75)',
		'circular_overlay_hover_point2_position' => '100',
		'circular_overlay_hover_position' => '',
		'circular_overlay_hover_type' => 'linear',
		'contact_form_dark_button_background_color' => '#3C3950FF',
		'contact_form_dark_button_border' => '0',
		'contact_form_dark_button_border_color' => '',
		'contact_form_dark_button_corner' => '0',
		'contact_form_dark_button_hover_background_color' => '#B6C6C9FF',
		'contact_form_dark_button_hover_border_color' => '',
		'contact_form_dark_button_hover_text_color' => '#FFFFFFFF',
		'contact_form_dark_button_position' => 'fullwidth',
		'contact_form_dark_button_size' => 'medium',
		'contact_form_dark_button_style' => 'flat',
		'contact_form_dark_button_text_color' => '#FFFFFFFF',
		'contact_form_dark_button_text_weight' => 'normal',
		'contact_form_dark_button_text_style' => '',
		'contact_form_dark_button_text_transform' => '',
		'contact_form_dark_custom_styles' => '1',
		'contact_form_dark_input_background_color' => '#181828FF',
		'contact_form_dark_input_border_color' => '#394050FF',
		'contact_form_dark_input_color' => '#5F727FFF',
		'contact_form_dark_input_icon_color' => '#46485CFF',
		'contact_form_dark_input_placeholder_color' => '',
		'contact_form_dark_label_color' => '#5F727FFF',
		'contact_form_light_button_background_color' => '#B6C6C9FF',
		'contact_form_light_button_border' => '0',
		'contact_form_light_button_border_color' => '',
		'contact_form_light_button_corner' => '0',
		'contact_form_light_button_hover_background_color' => '#3C3950FF',
		'contact_form_light_button_hover_border_color' => '',
		'contact_form_light_button_hover_text_color' => '#FFFFFFFF',
		'contact_form_light_button_position' => 'fullwidth',
		'contact_form_light_button_size' => 'medium',
		'contact_form_light_button_style' => 'flat',
		'contact_form_light_button_text_color' => '#FFFFFFFF',
		'contact_form_light_button_text_weight' => 'normal',
		'contact_form_light_button_text_style' => '',
		'contact_form_light_button_text_transform' => '',
		'contact_form_light_custom_styles' => '1',
		'contact_form_light_input_background_color' => '#FFFFFFFF',
		'contact_form_light_input_border_color' => '#DFE5E8FF',
		'contact_form_light_input_color' => '#5F727FFF',
		'contact_form_light_input_icon_color' => '#B6C6C9FF',
		'contact_form_light_input_placeholder_color' => '',
		'contact_form_light_label_color' => '#5F727FFF',
		'contacts_address' => '908 New Hampshire Avenue #100, Washington, DC 20037, United States',
		'contacts_email' => 'info@domain.tld',
		'contacts_fax' => '+1 916-875-2235',
		'contacts_phone' => '+1 916-875-2235',
		'contacts_website' => 'www.codex-themes.com',
		'copyright_link_color' => '#00bcd4',
		'copyright_text_color' => '#99a9b5',
		'counter_custom_responsive_fonts' => '1',
		'counter_font_family' => 'Montserrat',
		'counter_font_sets' => '',
		'counter_font_size' => '50',
		'counter_font_size_mobile' => '36',
		'counter_font_size_tablet' => '36',
		'counter_font_style' => '700',
		'counter_letter_spacing' => '0',
		'counter_line_height' => '69',
		'counter_line_height_mobile' => '53',
		'counter_line_height_tablet' => '53',
		'counter_text_transform' => 'uppercase',
		'custom_css' => '',
		'custom_footer' => '',
		'custom_footer_enable' => '',
		'custom_js' => '',
		'custom_js_header' => '',
		'tracking_js' => '',
		'content_preloader_line_1' => '',
		'content_preloader_line_2' => '',
		'content_preloader_line_3' => '',
		'content_preloader_select_style' => 'normal',
		'content_preloader_style' => 'preloader-4',
		'date_filter_subtitle_color' => '#99a9b5',
		'delicious_active' => '',
		'delicious_link' => '#',
		'deviantart_active' => '',
		'deviantart_link' => '',
		'disable_fixed_header' => '0',
		'disable_og_tags' => '1',
		'disable_scroll_top_button' => '0',
		'disable_smooth_scroll' => '0',
		'disable_uppercase_font' => '',
		'discord_active' => '',
		'discord_link' => '#',
		'divider_default_color' => '#dfe5e8',
		'dribbble_active' => '',
		'dribbble_link' => '#',
		'enable_mobile_lazy_loading' => '',
		'enable_page_preloader' => '',
		'excerpt_length' => '20',
		'facebook_active' => '1',
		'facebook_link' => '#',
		'favicon' => get_template_directory_uri() . '/images/favicon.ico',
		'flickr_active' => '',
		'flickr_link' => '#',
		'footer' => '1',
		'footer_active' => '1',
		'footer_apply_all_existing' => '0',
		'footer_background_color' => '#181828',
		'footer_background_gradient_angle' => '90',
		'footer_background_gradient_point1_color' => '#474B62FF',
		'footer_background_gradient_point1_position' => '0',
		'footer_background_gradient_point2_color' => '#181828FF',
		'footer_background_gradient_point2_position' => '100',
		'footer_background_gradient_position' => '',
		'footer_background_gradient_type' => 'linear',
		'footer_background_image' => '',
		'footer_background_image_color' => '',
		'footer_background_image_overlay' => '',
		'footer_background_image_repeat' => '0',
		'footer_background_pattern' => '',
		'footer_background_position_x' => 'center',
		'footer_background_position_y' => 'center',
		'footer_background_size' => 'auto',
		'footer_background_type' => 'color',
		'footer_bottom_area_fullwidth' => '',
		'footer_html' => '2021 &copy; Copyrights CodexThemes',
		'footer_menu_color' => '#99A9B5FF',
		'footer_menu_hover_color' => '#00BCD4FF',
		'footer_menu_separator_color' => '#333146FF',
		'footer_parallax' => '',
		'footer_text_color' => '#99A9B5FF',
		'footer_top_border_color' => '#313646FF',
		'footer_widget_active_link_color' => '#00bcd4',
		'footer_widget_area_background_color' => '#212331',
		'footer_widget_area_background_gradient_angle' => '90',
		'footer_widget_area_background_gradient_point1_color' => '#474B62FF',
		'footer_widget_area_background_gradient_point1_position' => '0',
		'footer_widget_area_background_gradient_point2_color' => '#181828FF',
		'footer_widget_area_background_gradient_point2_position' => '100',
		'footer_widget_area_background_gradient_position' => '',
		'footer_widget_area_background_gradient_type' => 'linear',
		'footer_widget_area_background_image' => '',
		'footer_widget_area_background_image_color' => '',
		'footer_widget_area_background_image_overlay' => '',
		'footer_widget_area_background_image_repeat' => '0',
		'footer_widget_area_background_pattern' => '',
		'footer_widget_area_background_position_x' => 'center',
		'footer_widget_area_background_position_y' => 'top',
		'footer_widget_area_background_size' => 'cover',
		'footer_widget_area_background_type' => 'color',
		'footer_widget_area_fullwidth' => '',
		'footer_widget_area_hide' => '0',
		'footer_widget_hover_link_color' => '#00bcd4',
		'footer_widget_link_color' => '#99a9b5',
		'footer_widget_text_color' => '#99a9b5',
		'footer_widget_title_color' => '#feffff',
		'footer_widget_triangle_color' => '',
		'custom_footer_background_color' => '',
		'custom_footer_background_gradient_angle' => '90',
		'custom_footer_background_gradient_point1_color' => '#474B62FF',
		'custom_footer_background_gradient_point1_position' => '0',
		'custom_footer_background_gradient_point2_color' => '#181828FF',
		'custom_footer_background_gradient_point2_position' => '100',
		'custom_footer_background_gradient_position' => '',
		'custom_footer_background_gradient_type' => 'linear',
		'custom_footer_background_image' => '',
		'custom_footer_background_image_color' => '',
		'custom_footer_background_image_overlay' => '',
		'custom_footer_background_image_repeat' => '0',
		'custom_footer_background_pattern' => '',
		'custom_footer_background_position_x' => 'center',
		'custom_footer_background_position_y' => 'center',
		'custom_footer_background_size' => 'auto',
		'custom_footer_background_type' => 'color',
		'form_elements_background_color' => '#f4f6f7',
		'form_elements_border_color' => '#dfe5e8',
		'form_elements_text_color' => '#3c3950',
		'gallery_caption_background_color' => '#000000',
		'gallery_description_color' => '#ffffff',
		'gallery_description_font_family' => 'Source Sans Pro',
		'gallery_description_font_sets' => '',
		'gallery_description_font_size' => '17',
		'gallery_description_font_style' => '300',
		'gallery_description_letter_spacing' => '',
		'gallery_description_line_height' => '24',
		'gallery_description_text_transform' => '',
		'gallery_title_bold_font_family' => 'Montserrat',
		'gallery_title_bold_font_sets' => 'latin,latin-ext',
		'gallery_title_bold_font_size' => '24',
		'gallery_title_bold_font_style' => '700',
		'gallery_title_bold_letter_spacing' => '',
		'gallery_title_bold_line_height' => '31',
		'gallery_title_bold_text_transform' => '',
		'gallery_title_color' => '#ffffff',
		'gallery_title_font_family' => 'Montserrat UltraLight',
		'gallery_title_font_sets' => '',
		'gallery_title_font_size' => '24',
		'gallery_title_font_style' => 'regular',
		'gallery_title_letter_spacing' => '',
		'gallery_title_line_height' => '30',
		'gallery_title_text_transform' => '',
		'global_hide_breadcrumbs' => '',
		'global_settings_apply_blog' => '',
		'global_settings_apply_default' => '',
		'global_settings_apply_portfolio' => '',
		'global_settings_apply_post' => '',
		'global_settings_apply_product' => '',
		'global_settings_apply_product_categories' => '',
		'global_settings_apply_search' => '',
		'googledrive_active' => '',
		'googledrive_link' => '#',
		'gradient_hover_angle' => '90',
		'gradient_hover_point1_color' => 'rgba(255,43,88,0.8)',
		'gradient_hover_point1_position' => '0',
		'gradient_hover_point2_color' => 'rgba(255,216,0,0.8)',
		'gradient_hover_point2_position' => '100',
		'gradient_hover_position' => '',
		'gradient_hover_type' => 'linear',
		'h1_color' => '#3c3950',
		'h1_custom_responsive_fonts' => '1',
		'h1_font_family' => 'Montserrat',
		'h1_font_sets' => '',
		'h1_font_size' => '50',
		'h1_font_size_mobile' => '36',
		'h1_font_size_tablet' => '38',
		'h1_font_style' => '700',
		'h1_letter_spacing' => '2.5',
		'h1_line_height' => '69',
		'h1_line_height_mobile' => '48',
		'h1_line_height_tablet' => '53',
		'h1_text_transform' => 'uppercase',
		'h2_color' => '#3c3950',
		'h2_custom_responsive_fonts' => '1',
		'h2_font_family' => 'Montserrat',
		'h2_font_sets' => '',
		'h2_font_size' => '36',
		'h2_font_size_mobile' => '30',
		'h2_font_size_tablet' => '32',
		'h2_font_style' => '700',
		'h2_letter_spacing' => '1.8',
		'h2_line_height' => '53',
		'h2_line_height_mobile' => '40',
		'h2_line_height_tablet' => '42',
		'h2_text_transform' => 'uppercase',
		'h3_color' => '#3c3950',
		'h3_custom_responsive_fonts' => '1',
		'h3_font_family' => 'Montserrat',
		'h3_font_sets' => '',
		'h3_font_size' => '28',
		'h3_font_size_mobile' => '24',
		'h3_font_size_tablet' => '26',
		'h3_font_style' => '700',
		'h3_letter_spacing' => '1.4',
		'h3_line_height' => '42',
		'h3_line_height_mobile' => '34',
		'h3_line_height_tablet' => '38',
		'h3_text_transform' => 'uppercase',
		'h4_color' => '#3c3950',
		'h4_custom_responsive_fonts' => '1',
		'h4_font_family' => 'Montserrat',
		'h4_font_sets' => '',
		'h4_font_size' => '24',
		'h4_font_size_mobile' => '24',
		'h4_font_size_tablet' => '22',
		'h4_font_style' => '700',
		'h4_letter_spacing' => '1.2',
		'h4_line_height' => '38',
		'h4_line_height_mobile' => '30',
		'h4_line_height_tablet' => '36',
		'h4_text_transform' => 'uppercase',
		'h5_color' => '#3c3950',
		'h5_custom_responsive_fonts' => '1',
		'h5_font_family' => 'Montserrat',
		'h5_font_sets' => '',
		'h5_font_size' => '19',
		'h5_font_size_mobile' => '19',
		'h5_font_size_tablet' => '19',
		'h5_font_style' => '700',
		'h5_letter_spacing' => '0.95',
		'h5_line_height' => '30',
		'h5_line_height_mobile' => '26',
		'h5_line_height_tablet' => '30',
		'h5_text_transform' => 'uppercase',
		'h6_color' => '#3c3950',
		'h6_custom_responsive_fonts' => '1',
		'h6_font_family' => 'Montserrat',
		'h6_font_sets' => '',
		'h6_font_size' => '16',
		'h6_font_size_mobile' => '16',
		'h6_font_size_tablet' => '16',
		'h6_font_style' => '700',
		'h6_letter_spacing' => '0.7',
		'h6_line_height' => '25',
		'h6_line_height_mobile' => '23',
		'h6_line_height_tablet' => '25',
		'h6_text_transform' => 'uppercase',
		'hamburger_menu_cart_position' => '1',
		'hamburger_menu_icon_color' => '#3c3950',
		'hamburger_menu_icon_light_color' => '#ffffff',
		'hamburger_menu_icon_size' => '',
		'header' => true,
		'header_layout' => 'default',
		'header_show' => '1',
		'header_style' => '3',
		'header_width' => 'normal',
		'hide_card_icon' => '0',
		'hide_search_icon' => '0',
		'hover_effect_default_color' => '#00bcd4',
		'hover_effect_horizontal_sliding_color' => '#46485c',
		'hover_effect_vertical_sliding_color' => '#f44336',
		'hover_effect_zooming_blur_color' => '#ffffff',
		'hover_link_color' => '#384554',
		'icons_portfolio_gallery_hover_color' => '#ffffff',
		'icons_symbol_color' => '#91a0ac',
		'instagram_active' => '1',
		'instagram_link' => '#',
		'light_title_font_family' => 'Montserrat UltraLight',
		'light_title_font_sets' => '',
		'light_title_font_size' => '',
		'light_title_font_style' => 'regular',
		'light_title_letter_spacing' => '',
		'light_title_line_height' => '',
		'light_title_text_transform' => '',
		'link_color' => '#00bcd4',
		'linkedin_active' => '1',
		'linkedin_link' => '#',
		'logo' => get_template_directory_uri() . '/images/default-logo.png',
		'logo_light' => get_template_directory_uri() . '/images/default-logo-light.png',
		'logo_light_selected_img_width' => 328,
		'logo_position' => 'left',
		'logo_selected_img_width' => 328,
		'logo_width' => '164',
		'mailchimp_content_button_background_color' => '#B6C6C9FF',
		'mailchimp_content_button_hover_background_color' => '#3C3950FF',
		'mailchimp_content_button_hover_text_color' => '#FFFFFFFF',
		'mailchimp_content_button_text_color' => '#FFFFFFFF',
		'mailchimp_content_custom_styles' => '1',
		'mailchimp_content_input_background_color' => '#F4F6F7FF',
		'mailchimp_content_input_border_color' => '#DFE5E8FF',
		'mailchimp_content_input_color' => '#3C3950FF',
		'mailchimp_content_input_placeholder_color' => '',
		'mailchimp_content_label_color' => '#5F727FFF',
		'mailchimp_content_text_color' => '',
		'mailchimp_footer_background_color' => '#394050FF',
		'mailchimp_footer_button_background_color' => '#394050FF',
		'mailchimp_footer_button_hover_background_color' => '#3C3950FF',
		'mailchimp_footer_button_hover_text_color' => '#FFFFFFFF',
		'mailchimp_footer_button_text_color' => '#99A9B5FF',
		'mailchimp_footer_custom_styles' => '1',
		'mailchimp_footer_input_background_color' => '#181828FF',
		'mailchimp_footer_input_border_color' => '#394050FF',
		'mailchimp_footer_input_color' => '#5F727FFF',
		'mailchimp_footer_input_placeholder_color' => '',
		'mailchimp_footer_label_color' => '#99A9B5FF',
		'mailchimp_footer_text_color' => '',
		'mailchimp_sidebars_background_color' => '#DFE5E8FF',
		'mailchimp_sidebars_button_background_color' => '#B6C6C9FF',
		'mailchimp_sidebars_button_hover_background_color' => '#3C3950FF',
		'mailchimp_sidebars_button_hover_text_color' => '#FFFFFFFF',
		'mailchimp_sidebars_button_text_color' => '#FFFFFFFF',
		'mailchimp_sidebars_custom_styles' => '1',
		'mailchimp_sidebars_input_background_color' => '#FFFFFFFF',
		'mailchimp_sidebars_input_border_color' => '#DFE5E8FF',
		'mailchimp_sidebars_input_color' => '#99A9B5FF',
		'mailchimp_sidebars_input_placeholder_color' => '',
		'mailchimp_sidebars_label_color' => '#5F727FFF',
		'mailchimp_sidebars_text_color' => '',
		'main_background_color' => '#ffffff',
		'main_background_gradient_angle' => '90',
		'main_background_gradient_point1_color' => '#E9ECDAFF',
		'main_background_gradient_point1_position' => '0',
		'main_background_gradient_point2_color' => '#D5F6FAFF',
		'main_background_gradient_point2_position' => '100',
		'main_background_gradient_position' => '',
		'main_background_gradient_type' => 'linear',
		'main_background_image' => '',
		'main_background_image_color' => '',
		'main_background_image_overlay' => '',
		'main_background_image_repeat' => '0',
		'main_background_pattern' => '',
		'main_background_position_x' => 'center',
		'main_background_position_y' => 'center',
		'main_background_size' => 'auto',
		'main_background_type' => 'color',
		'main_menu_font_family' => 'Montserrat',
		'main_menu_font_sets' => '',
		'main_menu_font_size' => '14',
		'main_menu_font_style' => '700',
		'main_menu_letter_spacing' => '',
		'main_menu_level1_active_background_color' => '#3c3950',
		'main_menu_level1_active_color' => '#3c3950',
		'main_menu_level1_background_color' => '',
		'main_menu_level1_color' => '#3c3950',
		'main_menu_level1_hover_background_color' => '',
		'main_menu_level1_hover_color' => '#00bcd4',
		'main_menu_level1_light_active_color' => '#ffffff',
		'main_menu_level1_light_color' => '#ffffff',
		'main_menu_level1_light_hover_color' => '#00bcd4',
		'main_menu_level2_active_background_color' => '#ffffff',
		'main_menu_level2_active_color' => '#3c3950',
		'main_menu_level2_background_color' => '#f4f6f7',
		'main_menu_level2_border_color' => '#dfe5e8',
		'main_menu_level2_color' => '#5f727f',
		'main_menu_level2_hover_background_color' => '#ffffff',
		'main_menu_level2_hover_color' => '#3c3950',
		'main_menu_level3_active_background_color' => '#ffffff',
		'main_menu_level3_active_color' => '#00bcd4',
		'main_menu_level3_background_color' => '#ffffff',
		'main_menu_level3_color' => '#5f727f',
		'main_menu_level3_hover_background_color' => '#494c64',
		'main_menu_level3_hover_color' => '#ffffff',
		'main_menu_line_height' => '25',
		'main_menu_mega_column_title_active_color' => '#00bcd4',
		'main_menu_mega_column_title_color' => '#3c3950',
		'main_menu_mega_column_title_hover_color' => '#00bcd4',
		'main_menu_text_transform' => 'uppercase',
		'meetup_active' => '',
		'meetup_link' => '#',
		'mega_menu_icons_color' => '#5F727FFF',
		'menu_appearance_tablet_landscape' => 'centered',
		'menu_appearance_tablet_portrait' => 'responsive',
		'menu_opacity' => 50,
		'menu_use_light_menu_logo' => '',
		'mini_pagination_active_color' => '#00bcd4',
		'mini_pagination_color' => '#b6c6c9',
		'minicart_amount_label_color' => '#00bcd4',
		'mobile_cart_position' => 'top',
		'mobile_menu_background_color' => '#ffffff',
		'mobile_menu_border_color' => '#dfe5e8',
		'mobile_menu_button_color' => '#3c3950',
		'mobile_menu_button_light_color' => '#ffffff',
		'mobile_menu_font_family' => 'Source Sans Pro',
		'mobile_menu_font_sets' => '',
		'mobile_menu_font_size' => '16',
		'mobile_menu_font_style' => 'regular',
		'mobile_menu_hide_color' => '',
		'mobile_menu_layout' => 'default',
		'mobile_menu_layout_style' => 'light',
		'mobile_menu_letter_spacing' => '',
		'mobile_menu_level1_active_background_color' => '#ffffff',
		'mobile_menu_level1_active_color' => '#3c3950',
		'mobile_menu_level1_background_color' => '#f4f6f7',
		'mobile_menu_level1_color' => '#5f727f',
		'mobile_menu_level2_active_background_color' => '#ffffff',
		'mobile_menu_level2_active_color' => '#3c3950',
		'mobile_menu_level2_background_color' => '#f4f6f7',
		'mobile_menu_level2_color' => '#5f727f',
		'mobile_menu_level3_active_background_color' => '#ffffff',
		'mobile_menu_level3_active_color' => '#3c3950',
		'mobile_menu_level3_background_color' => '#f4f6f7',
		'mobile_menu_level3_color' => '#5f727f',
		'mobile_menu_line_height' => '20',
		'mobile_menu_social_icon_color' => '',
		'mobile_menu_text_transform' => 'none',
		'myspace_active' => '',
		'myspace_link' => '#',
		'news_rewrite_slug' => '',
		'ok_active' => '',
		'ok_link' => '#',
		'options_sticky_header' => false,
		'overlay_menu_active_color' => '#00bcd4',
		'overlay_menu_background_color' => '#212331',
		'overlay_menu_color' => '#ffffff',
		'overlay_menu_font_family' => 'Montserrat',
		'overlay_menu_font_sets' => '',
		'overlay_menu_font_size' => '32',
		'overlay_menu_font_style' => '700',
		'overlay_menu_hover_color' => '#00bcd4',
		'overlay_menu_letter_spacing' => '',
		'overlay_menu_line_height' => '64',
		'overlay_menu_text_transform' => 'uppercase',
		'page_404_custom' => '',
		'page_default_sidebar' => true,
		'page_default_title_breadcrumbs' => true,
		'page_default_title_style' => '1',
		'page_layout_style' => 'fullwidth',
		'page_padding_bottom' => '10',
		'page_padding_left' => '10',
		'page_padding_locked' => false,
		'page_padding_right' => '10',
		'page_padding_top' => '10',
		'page_layout_breadcrumbs' => '1',
		'page_layout_breadcrumbs_default_color' => '#99A9B5FF',
		'page_layout_breadcrumbs_active_color' => '#3C3950FF',
		'page_layout_breadcrumbs_hover_color' => '#3C3950FF',
		'page_layout_breadcrumbs_alignment' => 'left',
		'page_layout_breadcrumbs_bottom_spacing' => '0',
		'page_layout_breadcrumbs_shop_category' => '0',
		'pagespeed_lazy_images_desktop_enable' => '1',
		'pagespeed_lazy_images_mobile_enable' => '',
		'pagespeed_lazy_images_page_cache_enabled' => '',
		'pagespeed_lazy_images_visibility_offset' => '300',
		'pagination_active_color' => '#3c3950',
		'pagination_basic_background_color' => '#ffffff',
		'pagination_basic_color' => '#99a9b5',
		'pagination_hover_color' => '#00bcd4',
		'picassa_active' => '',
		'picassa_link' => '#',
		'pinterest_active' => '1',
		'pinterest_link' => '#',
		'portfolio_arrow_background_color' => '#B6C6C9FF',
		'portfolio_arrow_background_hover_color' => '#00BCD4FF',
		'portfolio_arrow_color' => '#FFFFFFFF',
		'portfolio_arrow_hover_color' => '#FFFFFFFF',
		'portfolio_date_color' => '#99a9b5',
		'portfolio_description_color' => '#5f727f',
		'portfolio_description_font_family' => 'Source Sans Pro',
		'portfolio_description_font_sets' => '',
		'portfolio_description_font_size' => '16',
		'portfolio_description_font_style' => 'regular',
		'portfolio_description_letter_spacing' => '',
		'portfolio_description_line_height' => '24',
		'portfolio_description_text_transform' => '',
		'portfolio_filter_button_active_background_color' => '#00BCD4FF',
		'portfolio_filter_button_active_color' => '#FFFFFFFF',
		'portfolio_filter_button_background_color' => '#DFE5E8FF',
		'portfolio_filter_button_color' => '#5F727FFF',
		'portfolio_filter_button_hover_background_color' => '#B6C6C9FF',
		'portfolio_filter_button_hover_color' => '#FFFFFFFF',
		'portfolio_hide_bottom_navigation' => '',
		'portfolio_hide_date' => '',
		'portfolio_hide_likes' => '',
		'portfolio_hide_sets' => '',
		'portfolio_hide_socials' => '',
		'portfolio_hide_top_navigation' => '',
		'portfolio_rewrite_slug' => '',
		'portfolio_sorting_background_color' => '#B6C6C9FF',
		'portfolio_sorting_controls_color' => '#3C3950FF',
		'portfolio_sorting_separator_color' => '#B6C6C9FF',
		'portfolio_sorting_switch_color' => '#FFFFFFFF',
		'portfolio_title_color' => '#5f727f',
		'portfolio_title_font_family' => 'Montserrat',
		'portfolio_title_font_sets' => '',
		'portfolio_title_font_size' => '16',
		'portfolio_title_font_style' => '700',
		'portfolio_title_letter_spacing' => '',
		'portfolio_title_line_height' => '24',
		'portfolio_title_text_transform' => '',
		'preloader' => '',
		'preloader_line_1' => '#B9B7FFFF',
		'preloader_line_2' => '#00BCD4FF',
		'preloader_line_3' => '#A3E7F0FF',
		'preloader_page_background' => '#2C2E3DFF',
		'preloader_style' => 'preloader-4',
		'preloader_type' => 'css',
		'product_categories_price_page_color' => '',
		'product_title_show' => '0',
		'product_header_separator' => '1',
		'product_content_padding_top' => '0',
		'product_content_padding_top_tablet' => '0',
		'product_content_padding_top_mobile' => '0',
		'product_archive_type' => 'grid',
		'product_archive_layout' => 'justified',
		'product_archive_columns_desktop' => '4x',
		'product_archive_columns_tablet' => '3x',
		'product_archive_columns_mobile' => '2x',
		'product_archive_columns_100' => '5',
		'product_archive_size_desktop' => '42',
		'product_archive_size_tablet' => '42',
		'product_archive_size_mobile' => '42',
		'product_archive_image_aspect_ratio' => 'portrait',
		'product_archive_preset_type' => 'below',
		'product_archive_preset_below' => 'default-cart-button',
		'product_archive_preset_on_image' => '',
		'product_archive_preset_on_hover' => '',
		'product_archive_quick_view' => '0',
		'product_archive_quick_view_text' => 'Quick View',
		'product_archive_orderby' => 'default',
		'product_archive_order' => 'asc',
		'product_archive_show_sorting' => '0',
		'product_archive_category_description_position' => 'above',
		'product_archive_show_categories_desktop' => '1',
		'product_archive_show_categories_tablet' => '1',
		'product_archive_show_categories_mobile' => '0',
		'product_archive_show_title' => '1',
		'product_archive_show_price' => '1',
		'product_archive_show_reviews_desktop' => '1',
		'product_archive_show_reviews_tablet' => '1',
		'product_archive_show_reviews_mobile' => '0',
		'product_archive_show_add_to_cart' => '1',
		'product_archive_add_to_cart_type' => 'button',
		'product_archive_cart_button_show_icon' => '1',
		'product_archive_cart_button_text' => 'Add To Cart',
		'product_archive_cart_icon' => '',
		'product_archive_cart_icon_pack' => '',
		'product_archive_select_options_button_text' => 'Select Options',
		'product_archive_select_options_icon' => '',
		'product_archive_select_options_icon_pack' => '',
		'product_archive_show_wishlist' => '1',
		'product_archive_add_wishlist_icon' => '',
		'product_archive_add_wishlist_icon_pack' => '',
		'product_archive_added_wishlist_icon' => '',
		'product_archive_added_wishlist_icon_pack' => '',
		'product_archive_items_per_page_desktop' => '12',
		'product_archive_items_per_page_tablet' => '12',
		'product_archive_items_per_page_mobile' => '12',
		'product_archive_show_pagination' => '1',
		'product_archive_pagination_type' => 'normal',
		'product_archive_more_button_text' => 'Load More',
		'product_archive_more_icon' => '',
		'product_archive_more_icon_pack' => '',
		'product_archive_more_stretch_full_width' => '',
		'product_archive_more_show_separator' => '',
		'product_archive_labels' => '1',
		'product_archive_label_sale' => '1',
		'product_archive_label_new' => '1',
		'product_archive_label_out_stock' => '1',
		'product_archive_filters_type' => 'disabled',
		'product_archive_filters_ajax' => '0',
		'product_archive_scroll_to_top' => '1',
		'product_archive_remove_attr_counts' => '0',
		'product_archive_filters_style' => 'standard',
		'product_archive_filters_style_native' => 'sidebar',
		'product_archive_filter_by_categories' => '1',
		'product_archive_filter_by_categories_hierarchy' => '0',
		'product_archive_filter_by_categories_count' => '0',
		'product_archive_filter_by_categories_title' => 'Categories',
		'product_archive_filter_by_categories_order_by' => 'name',
		'product_archive_filter_by_price' => '0',
		'product_archive_filter_by_price_title' => 'Price',
		'product_archive_filter_by_attribute' => '0',
		'product_archive_filter_by_attribute_count' => '0',
		'product_archive_filter_by_attribute_hide_empty' => '0',
		'product_archive_filter_by_attribute_data' => '',
		'product_archive_filter_by_status' => '0',
		'product_archive_filter_by_status_title' => 'Status',
		'product_archive_filter_by_status_sale' => '1',
		'product_archive_filter_by_status_sale_text' => 'On Sale',
		'product_archive_filter_by_status_stock' => '1',
		'product_archive_filter_by_status_stock_text' => 'In Stock',
		'product_archive_filter_by_status_count' => '0',
		'product_archive_filter_by_search' => '0',
		'product_archive_filters_text_labels_all_text' => 'Show All',
		'product_archive_filters_text_labels_clear_text' => 'Clear Filters',
		'product_archive_filters_text_labels_search_text' => 'Search by Product',
		'product_archive_filter_buttons_hidden_show_text' => 'Show filters',
		'product_archive_filter_buttons_hidden_sidebar_title' => 'Filter',
		'product_archive_filter_buttons_hidden_filter_by_text' => 'Filter By',
		'product_archive_added_cart_text' => 'Item added to cart',
		'product_archive_added_wishlist_text' => 'Item added to wishlist',
		'product_archive_removed_wishlist_text' => 'Item removed from wishlist',
		'product_archive_view_cart_button_text' => 'View Cart',
		'product_archive_checkout_button_text' => 'Checkout',
		'product_archive_view_wishlist_button_text' => 'View Wishlist',
		'product_archive_not_found_text' => 'No items were found matching your selection.',
		'product_archive_loading_animation' => '0',
		'product_archive_animation_effect' => 'move-up',
		'product_archive_ignore_highlights' => '1',
		'product_archive_skeleton_loader' => '0',
		'product_archive_featured_only' => '0',
		'product_archive_sale_only' => '0',
		'product_archive_social_sharing' => '0',
		'product_archive_customize_styles' => '0',
		'product_archive_image_hover_effect_image' => 'fade',
		'product_archive_image_hover_effect_page' => 'fade',
		'product_archive_image_hover_effect_hover' => 'fade',
		'product_archive_image_hover_effect_fallback' => 'zooming',
		'product_archive_caption_container_preset' => 'transparent',
		'product_archive_caption_container_alignment_desktop' => '',
		'product_archive_caption_container_alignment_tablet' => '',
		'product_archive_caption_container_alignment_mobile' => '',
		'product_archive_caption_container_alignment_hover' => '',
		'product_archive_caption_container_background_color' => '',
		'product_archive_caption_container_background_color_hover' => '',
		'product_archive_caption_container_separator' => '',
		'product_archive_caption_container_separator_color' => '',
		'product_archive_caption_container_separator_color_hover' => '',
		'product_archive_caption_container_separator_width' => '',
		'product_archive_caption_container_preset_hover' => 'light',
		'product_archive_caption_container_preset_hover_background_color' => '',
		'product_archive_caption_container_size_desktop' => '',
		'product_archive_caption_container_size_tablet' => '',
		'product_archive_caption_container_size_mobile' => '',
		'product_archive_stay_visible' => '4000',
		'product_gallery' => 'enabled',
		'product_gallery_type' => 'horizontal',
		'product_gallery_column_position' => 'left',
		'product_gallery_column_width' => '50',
		'product_gallery_show_image' => 'hover',
		'product_gallery_zoom' => '1',
		'product_gallery_lightbox' => '1',
		'product_gallery_labels' => '1',
		'product_gallery_label_sale' => '1',
		'product_gallery_label_new' => '1',
		'product_gallery_label_out_stock' => '1',
		'product_gallery_auto_height' => '1',
		'product_gallery_elements_color' => '',
		'product_gallery_retina_ready' => '0',
		'product_grid_title_legacy' => '0',
		'product_grid_title_font_family' => 'Montserrat',
		'product_grid_title_font_size' => '14',
		'product_grid_title_font_style' => '700normal',
		'product_grid_title_font_sets' => '',
		'product_grid_title_line_height' => '18.2',
		'product_grid_title_text_transform' => 'none',
		'product_grid_title_letter_spacing' => '0',
		'product_grid_title_color' => '#5F727FFF',
		'product_grid_title_color_hover' => '#3C3950FF',
		'product_grid_title_color_on_image' => '#212227FF',
		'product_grid_category_font_family' => 'Montserrat',
		'product_grid_category_font_size' => '9',
		'product_grid_category_font_style' => '500normal',
		'product_grid_category_font_sets' => '',
		'product_grid_category_line_height' => '10.8',
		'product_grid_category_text_transform' => 'uppercase',
		'product_grid_category_letter_spacing' => '0.45',
		'product_grid_category_color' => '#99A9B5FF',
		'product_grid_category_color_hover' => '#00BCD4FF',
		'product_grid_category_color_on_image' => '#FFFFFFFF',
		'product_grid_price_color' => '#5F727FFF',
		'product_grid_price_color_on_image' => '#212227FF',
		'product_grid_button_border_width' => '1',
		'product_grid_button_border_radius' => '30',
		'product_grid_button_add_to_cart_color' => '#5F727FFF',
		'product_grid_button_add_to_cart_color_hover' => '#FFFFFFFF',
		'product_grid_button_add_to_cart_background' => '#DFE5E8FF',
		'product_grid_button_add_to_cart_background_hover' => '#00BCD4FF',
		'product_grid_button_add_to_cart_border_color' => '#DFE5E8FF',
		'product_grid_button_add_to_cart_border_color_hover' => '#00BCD4FF',
		'product_grid_button_select_options_color' => '#5F727FFF',
		'product_grid_button_select_options_color_hover' => '#FFFFFFFF',
		'product_grid_button_select_options_background' => '',
		'product_grid_button_select_options_background_hover' => '#00BCD4FF',
		'product_grid_button_select_options_border_color' => '#5F727FFF',
		'product_grid_button_select_options_border_color_hover' => '#00BCD4FF',
		'product_grid_icons_border_width' => '0',
		'product_grid_icons_border_radius' => '20',
		'product_grid_icons_color' => '#5F727FFF',
		'product_grid_icons_color_hover' => '#FFFFFFFF',
		'product_grid_icons_caption_image_color' => '#212227FF',
		'product_grid_icons_caption_image_color_hover' => '#FFFFFFFF',
		'product_grid_icons_background' => '#DFE5E8FF',
		'product_grid_icons_background_hover' => '#00BCD4FF',
		'product_grid_icons_border_color' => '',
		'product_grid_icons_border_color_hover' => '',
		'product_grid_quick_view_color' => '#FFFFFFFF',
		'product_grid_quick_view_background' => '#00BCD4A6',
		'product_labels_font_family' => 'Montserrat',
		'product_labels_font_size' => '7',
		'product_labels_font_style' => '700normal',
		'product_labels_font_sets' => '',
		'product_labels_line_height' => '8.4',
		'product_labels_text_transform' => 'uppercase',
		'product_labels_letter_spacing' => '0.35',
		'product_labels_text_color' => '#FFFFFFFF',
		'product_labels_sale_background' => '#00BCD4FF',
		'product_labels_new_background' => '#393D50FF',
		'product_labels_out_of_stock_background' => '#F44336FF',
		'product_labels_style' => '1',
		'product_label_new_text' => 'New',
		'product_label_sale_text' => 'On Sale',
		'product_label_out_of_stock_text' => 'Out of stock',
		'product_label_sale_type' => 'percentage',
		'product_label_sale_prefix' => '-',
		'product_label_sale_suffix' => '%',
		'product_page_layout' => 'default',
		'product_page_layout_style' => 'horizontal_tabs',
		'product_page_layout_preset' => 'col-50-50',
		'product_page_layout_fullwidth' => '0',
		'product_page_layout_sticky' => '0',
		'product_page_layout_title_area' => 'disabled',
		'product_page_ajax_add_to_cart' => '1',
		'product_page_desc_review_source' => 'page_builder',
		'product_page_desc_review_layout' => 'tabs',
		'product_page_desc_review_layout_tabs_style' => 'horizontal',
		'product_page_desc_review_layout_tabs_alignment' => 'left',
		'product_page_desc_review_layout_acc_position' => 'below_gallery',
		'product_page_desc_review_layout_one_by_one_description_background' => '#F4F6F7FF',
		'product_page_desc_review_layout_one_by_one_additional_info_background' => '#FFFFFFFF',
		'product_page_desc_review_layout_one_by_one_reviews_background' => '#F4F6F7FF',
		'product_page_desc_review_description' => '1',
		'product_page_desc_review_description_title' => 'Description',
		'product_page_desc_review_additional_info' => '1',
		'product_page_desc_review_additional_info_title' => 'Additional Info',
		'product_page_desc_review_reviews' => '1',
		'product_page_desc_review_reviews_title' => 'Reviews',
		'product_page_button_add_to_cart_text' => 'Add to Cart',
		'product_page_button_add_to_cart_icon' => 'f1e7',
		'product_page_button_add_to_cart_icon_pack' => 'material',
		'product_page_button_add_to_cart_icon_position' => 'left',
		'product_page_button_add_to_cart_border_width' => '',
		'product_page_button_add_to_cart_border_radius' => '',
		'product_page_button_add_to_cart_color' => '',
		'product_page_button_add_to_cart_color_hover' => '',
		'product_page_button_add_to_cart_background' => '',
		'product_page_button_add_to_cart_background_hover' => '',
		'product_page_button_add_to_cart_border_color' => '',
		'product_page_button_add_to_cart_border_color_hover' => '',
		'product_page_button_add_to_wishlist_icon' => 'f37b',
		'product_page_button_add_to_wishlist_icon_pack' => 'material',
		'product_page_button_add_to_wishlist_color' => '',
		'product_page_button_add_to_wishlist_color_hover' => '',
		'product_page_button_add_to_wishlist_color_filled' => '',
		'product_page_button_added_to_wishlist_icon' => 'f377',
		'product_page_button_added_to_wishlist_icon_pack' => 'material',
		'product_page_button_clear_attributes_text' => 'Clear selection',
		'product_page_elements_prev_next' => '1',
		'product_page_elements_preview_on_hover' => '1',
		'product_page_elements_back_to_shop' => '1',
		'product_page_elements_back_to_shop_link' => 'main_shop',
		'product_page_elements_back_to_shop_link_custom_url' => '',
		'product_page_elements_title' => '1',
		'product_page_elements_attributes' => '0',
		'product_page_elements_attributes_data' => '',
		'product_page_elements_reviews' => '1',
		'product_page_elements_reviews_text' => 'customer reviews',
		'product_page_elements_price' => '1',
		'product_page_elements_price_strikethrough' => '1',
		'product_page_elements_description' => '1',
		'product_page_elements_stock_amount' => '1',
		'product_page_elements_stock_amount_text' => 'Products in stock',
		'product_page_elements_size_guide' => '1',
		'product_page_elements_sku' => '1',
		'product_page_elements_sku_title' => 'SKU',
		'product_page_elements_categories' => '1',
		'product_page_elements_categories_title' => 'Categories',
		'product_page_elements_tags' => '1',
		'product_page_elements_tags_title' => 'Tags',
		'product_page_elements_share' => '1',
		'product_page_elements_share_title' => 'Share',
		'product_page_elements_upsell' => '1',
		'product_page_elements_upsell_title' => 'You may also like',
		'product_page_elements_upsell_items' => '-1',
		'product_page_elements_upsell_columns_desktop' => '4x',
		'product_page_elements_upsell_columns_tablet' => '3x',
		'product_page_elements_upsell_columns_mobile' => '2x',
		'product_page_elements_upsell_columns_100' => '5',
		'product_page_elements_related' => '1',
		'product_page_elements_related_title' => 'Related Products',
		'product_page_elements_related_items' => '4',
		'product_page_elements_related_columns_desktop' => '4x',
		'product_page_elements_related_columns_tablet' => '3x',
		'product_page_elements_related_columns_mobile' => '2x',
		'product_page_elements_related_columns_100' => '5',
		'product_hide_social_sharing' => '0',
		'cart_elements_cross_sells' => '1',
		'cart_elements_cross_sells_columns_100' => '5',
		'cart_elements_cross_sells_columns_desktop' => '4x',
		'cart_elements_cross_sells_columns_mobile' => '2x',
		'cart_elements_cross_sells_columns_tablet' => '3x',
		'cart_elements_cross_sells_items' => '-1',
		'cart_elements_cross_sells_title' => 'You may be interested in',
		'cart_layout' => 'modern',
		'modern_cart_steps' => '1',
		'modern_cart_steps_position' => 'title_area',
		'cart_empty_text' => 'No products in the cart.',
		'cart_icon' => '',
		'cart_icon_pack' => '',
		'checkout_thank_you_default' => '1',
		'checkout_thank_you_extra' => '',
		'footer_widget_woocommerce' => '1',
		'product_price_cart_color' => '#3c3950',
		'product_price_cart_font_family' => 'Source Sans Pro',
		'product_price_cart_font_sets' => 'latin,latin-ext',
		'product_price_cart_font_size' => '24',
		'product_price_cart_font_style' => '300',
		'product_price_cart_letter_spacing' => '',
		'product_price_cart_line_height' => '30',
		'product_price_cart_text_transform' => '',
		'product_price_listing_font_family' => 'Source Sans Pro',
		'product_price_listing_font_sets' => 'latin,latin-ext',
		'product_price_listing_font_size' => '18',
		'product_price_listing_font_style' => 'regular',
		'product_price_listing_letter_spacing' => '',
		'product_price_listing_line_height' => '18',
		'product_price_listing_text_transform' => '',
		'product_price_listing_color' => '#00BCD4FF',
		'product_price_page_color' => '#3c3950',
		'product_price_page_font_family' => 'Source Sans Pro',
		'product_price_page_font_sets' => 'latin,latin-ext',
		'product_price_page_font_size' => '36',
		'product_price_page_font_style' => '300',
		'product_price_page_letter_spacing' => '',
		'product_price_page_line_height' => '36',
		'product_price_page_text_transform' => '',
		'product_price_widget_color' => '#3c3950',
		'product_price_widget_font_family' => 'Source Sans Pro',
		'product_price_widget_font_sets' => 'latin,latin-ext',
		'product_price_widget_font_size' => '20',
		'product_price_widget_font_style' => '300',
		'product_price_widget_letter_spacing' => '',
		'product_price_widget_line_height' => '30',
		'product_price_widget_text_transform' => '',
		'product_quick_view' => '',
		'product_separator_listing_color' => '#000000',
		'product_title_cart_color' => '#00bcd4',
		'product_title_cart_font_family' => 'Source Sans Pro',
		'product_title_cart_font_sets' => 'latin,latin-ext',
		'product_title_cart_font_size' => '16',
		'product_title_cart_font_style' => 'regular',
		'product_title_cart_letter_spacing' => '',
		'product_title_cart_line_height' => '25',
		'product_title_cart_text_transform' => '',
		'product_title_checkout_color' => '#3C3950FF',
		'product_title_listing_color' => '#5f727f',
		'product_title_listing_font_family' => 'Montserrat',
		'product_title_listing_font_sets' => 'latin,latin-ext',
		'product_title_listing_font_size' => '16',
		'product_title_listing_font_style' => '700',
		'product_title_listing_letter_spacing' => '',
		'product_title_listing_line_height' => '25',
		'product_title_listing_text_transform' => '',
		'product_title_page_color' => '#3c3950',
		'product_title_page_font_family' => 'Montserrat UltraLight',
		'product_title_page_font_sets' => 'latin,latin-ext',
		'product_title_page_font_size' => '28',
		'product_title_page_font_style' => 'regular',
		'product_title_page_letter_spacing' => '1.7',
		'product_title_page_line_height' => '42',
		'product_title_page_text_transform' => 'uppercase',
		'product_title_widget_color' => '#5f727f',
		'product_title_widget_font_family' => 'Source Sans Pro',
		'product_title_widget_font_sets' => 'latin,latin-ext',
		'product_title_widget_font_size' => '16',
		'product_title_widget_font_style' => 'regular',
		'product_title_widget_letter_spacing' => '',
		'product_title_widget_line_height' => '25',
		'product_title_widget_text_transform' => '',
		'products_pagination' => 'normal',
		'purchase_code' => '',
		'quickfinder_description_color' => '#5f727f',
		'quickfinder_description_font_family' => 'Source Sans Pro',
		'quickfinder_description_font_sets' => '',
		'quickfinder_description_font_size' => '16',
		'quickfinder_description_font_style' => 'regular',
		'quickfinder_description_letter_spacing' => '',
		'quickfinder_description_line_height' => '25',
		'quickfinder_description_text_transform' => '',
		'quickfinder_title_color' => '#4c5867',
		'quickfinder_title_font_family' => 'Montserrat',
		'quickfinder_title_font_sets' => 'latin',
		'quickfinder_title_font_size' => '24',
		'quickfinder_title_font_style' => '700',
		'quickfinder_title_letter_spacing' => '',
		'quickfinder_title_line_height' => '38',
		'quickfinder_title_text_transform' => '',
		'quickfinder_title_thin_font_family' => 'Montserrat UltraLight',
		'quickfinder_title_thin_font_sets' => 'latin,latin-ext',
		'quickfinder_title_thin_font_size' => '24',
		'quickfinder_title_thin_font_style' => 'regular',
		'quickfinder_title_thin_letter_spacing' => '',
		'quickfinder_title_thin_line_height' => '38',
		'quickfinder_title_thin_text_transform' => '',
		'qzone_active' => '',
		'qzone_link' => '#',
		'reddit_active' => '',
		'reddit_link' => '#',
		'rss_active' => '',
		'rss_link' => '#',
		'sidebar_show' => '0',
		'search_page_custom_settings' => '0',
		'share_active' => '',
		'share_link' => '#',
		'show_author' => '1',
		'show_social_icons' => '1',
		'size_guide_image' => '',
		'skype_active' => '',
		'skype_link' => '#',
		'slack_active' => '',
		'slack_link' => '#',
		'slider_animSpeed' => '5',
		'slider_boxCols' => '8',
		'slider_boxRows' => '4',
		'slider_controlNav' => '1',
		'slider_directionNav' => '1',
		'slider_effect' => 'random',
		'slider_pauseTime' => '20',
		'slider_slices' => '15',
		'sliders_arrow_background_color' => '#DFE5E8FF',
		'sliders_arrow_background_hover_color' => '#00bcd4',
		'sliders_arrow_color' => '#3c3950',
		'sliders_arrow_hover_color' => '#ffffff',
		'slideshow_arrow_background' => '#394050',
		'slideshow_arrow_color' => '#ffffff',
		'slideshow_arrow_hover_background' => '#00bcd4',
		'slideshow_description_font_family' => 'Source Sans Pro',
		'slideshow_description_font_sets' => '',
		'slideshow_description_font_size' => '16',
		'slideshow_description_font_style' => 'regular',
		'slideshow_description_line_height' => '25',
		'slideshow_title_font_family' => 'Montserrat',
		'slideshow_title_font_sets' => '',
		'slideshow_title_font_size' => '50',
		'slideshow_title_font_style' => '700',
		'slideshow_title_line_height' => '69',
		'small_logo' => get_template_directory_uri() . '/images/default-logo-small.png',
		'small_logo_light' => get_template_directory_uri() . '/images/default-logo-light-small.png',
		'small_logo_light_selected_img_width' => 264,
		'small_logo_selected_img_width' => 264,
		'small_logo_width' => '132',
		'socials_colors_footer' => '',
		'socials_colors_posts' => '#99A9B5FF',
		'socials_colors_top_area' => '#5F727FFF',
		'socials_colors_woocommerce' => '#99A9B5FF',
		'soundcloud_active' => '',
		'soundcloud_link' => '#',
		'spotify_active' => '',
		'spotify_link' => '#',
		'sticky_header' => '1',
		'sticky_header_on_mobile' => '',
		'stumbleupon_active' => '',
		'stumbleupon_link' => '#',
		'styled_elements_background_color' => '#f4f6f7',
		'styled_elements_color_1' => '#00bcd4',
		'styled_elements_color_2' => '#99a9b5',
		'styled_elements_color_3' => '#f44336',
		'styled_elements_color_4' => '#393d50',
		'styled_subtitle_custom_responsive_fonts' => '1',
		'styled_subtitle_font_family' => 'Source Sans Pro',
		'styled_subtitle_font_sets' => '',
		'styled_subtitle_font_size' => '24',
		'styled_subtitle_font_size_mobile' => '22',
		'styled_subtitle_font_size_tablet' => '20',
		'styled_subtitle_font_style' => '300',
		'styled_subtitle_letter_spacing' => '0',
		'styled_subtitle_line_height' => '37',
		'styled_subtitle_line_height_mobile' => '27',
		'styled_subtitle_line_height_tablet' => '34',
		'styled_subtitle_text_transform' => '',
		'submenu_font_family' => 'Source Sans Pro',
		'submenu_font_sets' => '',
		'submenu_font_size' => '16',
		'submenu_font_style' => 'regular',
		'submenu_letter_spacing' => '',
		'submenu_line_height' => '20',
		'submenu_text_transform' => 'none',
		'system_icons_font' => '#99a3b0',
		'system_icons_font_2' => '#b6c6c9',
		'tabs_title_font_family' => 'Montserrat',
		'tabs_title_font_sets' => 'latin,latin-ext',
		'tabs_title_font_size' => '14',
		'tabs_title_font_style' => '700',
		'tabs_title_letter_spacing' => '0.7',
		'tabs_title_line_height' => '25',
		'tabs_title_text_transform' => 'uppercase',
		'tabs_title_thin_font_family' => 'Montserrat UltraLight',
		'tabs_title_thin_font_sets' => 'latin,latin-ext',
		'tabs_title_thin_font_size' => '14',
		'tabs_title_thin_font_style' => 'regular',
		'tabs_title_thin_letter_spacing' => '0.7',
		'tabs_title_thin_line_height' => '25',
		'tabs_title_thin_text_transform' => 'uppercase',
		'telegram_active' => '',
		'telegram_link' => '#',
		'testimonial_arrow_background_color' => '#DFE5E8FF',
		'testimonial_arrow_background_hover_color' => '#00BCD4FF',
		'testimonial_arrow_color' => '#3C3950FF',
		'testimonial_arrow_hover_color' => '#FFFFFFFF',
		'testimonial_font_family' => 'Source Sans Pro',
		'testimonial_font_sets' => '',
		'testimonial_font_size' => '24',
		'testimonial_font_style' => '300',
		'testimonial_letter_spacing' => '',
		'testimonial_line_height' => '36',
		'testimonial_text_transform' => '',
		'testimonial_company_font_family' => 'Source Sans Pro',
		'testimonial_company_font_sets' => '',
		'testimonial_company_font_size' => '14',
		'testimonial_company_font_style' => '300normal',
		'testimonial_company_letter_spacing' => '',
		'testimonial_company_line_height' => '25',
		'testimonial_company_text_transform' => 'none',
		'testimonial_name_font_family' => 'Montserrat',
		'testimonial_name_font_sets' => '',
		'testimonial_name_font_size' => '14',
		'testimonial_name_font_style' => '700normal',
		'testimonial_name_letter_spacing' => '',
		'testimonial_name_line_height' => '25',
		'testimonial_name_text_transform' => 'uppercase',
		'testimonial_position_font_family' => 'Source Sans Pro',
		'testimonial_position_font_sets' => '',
		'testimonial_position_font_size' => '14',
		'testimonial_position_font_style' => '300normal',
		'testimonial_position_letter_spacing' => '',
		'testimonial_position_line_height' => '25',
		'testimonial_position_text_transform' => 'none',
		'testimonial_title_font_style' => 'regular',
		'testimonial_title_letter_spacing' => 0,
		'testimonial_title_text_transform' => '',
		'theme_version' => wp_get_theme(wp_get_theme()->get('Template'))->get('Version'),
		'title_bar_background_color' => '#333144',
		'title_bar_text_color' => '#ffffff',
		'title_excerpt_custom_responsive_fonts' => '1',
		'title_excerpt_font_family' => 'Source Sans Pro',
		'title_excerpt_font_sets' => '',
		'title_excerpt_font_size' => '24',
		'title_excerpt_font_size_mobile' => '20',
		'title_excerpt_font_size_tablet' => '22',
		'title_excerpt_font_style' => '300',
		'title_excerpt_letter_spacing' => '0',
		'title_excerpt_line_height' => '37',
		'title_excerpt_line_height_mobile' => '27',
		'title_excerpt_line_height_tablet' => '34',
		'title_excerpt_text_transform' => '',
		'top_area_alignment' => 'justified',
		'top_area_background_color' => '#f4f6f7',
		'top_area_background_gradient_angle' => '90',
		'top_area_background_gradient_point1_color' => '#D6EEEDFF',
		'top_area_background_gradient_point1_position' => '0',
		'top_area_background_gradient_point2_color' => '#F2D8E9FF',
		'top_area_background_gradient_point2_position' => 100,
		'top_area_background_gradient_position' => '',
		'top_area_background_gradient_type' => 'linear',
		'top_area_background_image' => '',
		'top_area_background_image_color' => '',
		'top_area_background_image_overlay' => '',
		'top_area_background_image_repeat' => '0',
		'top_area_background_pattern' => '',
		'top_area_background_position_x' => 'center',
		'top_area_background_position_y' => 'center',
		'top_area_background_size' => 'auto',
		'top_area_background_type' => 'color',
		'top_area_border_color' => '#00bcd4',
		'top_area_button' => true,
		'top_area_button_background_color' => '#494c64',
		'top_area_button_border_color' => '',
		'top_area_button_hover_background_color' => '#00bcd4',
		'top_area_button_hover_border_color' => '',
		'top_area_button_hover_text_color' => '#ffffff',
		'top_area_button_link' => '#',
		'top_area_button_text' => 'Join Now',
		'top_area_button_text_color' => '#FFFFFFFF',
		'top_area_contacts' => '1',
		'top_area_contacts_address' => '19th Ave New York, NY 95822, USA',
		'top_area_contacts_address_icon' => '',
		'top_area_contacts_address_icon_color' => '#5F727FFF',
		'top_area_contacts_address_icon_pack' => 'elegant',
		'top_area_contacts_email' => '',
		'top_area_contacts_email_icon' => '',
		'top_area_contacts_email_icon_color' => '#5F727FFF',
		'top_area_contacts_email_icon_pack' => 'elegant',
		'top_area_contacts_fax' => '',
		'top_area_contacts_fax_icon' => '',
		'top_area_contacts_fax_icon_color' => '#5F727FFF',
		'top_area_contacts_fax_icon_pack' => 'elegant',
		'top_area_contacts_phone' => '',
		'top_area_contacts_phone_icon' => '',
		'top_area_contacts_phone_icon_color' => '#5F727FFF',
		'top_area_contacts_phone_icon_pack' => 'elegant',
		'top_area_contacts_website' => '',
		'top_area_contacts_website_icon' => '',
		'top_area_contacts_website_icon_color' => '#5F727FFF',
		'top_area_contacts_website_icon_pack' => 'elegant',
		'top_area_disable_fixed' => '1',
		'top_area_disable_mobile' => '1',
		'top_area_disable_tablet' => '',
		'top_area_link_color' => '#5f727f',
		'top_area_link_hover_color' => '#00bcd4',
		'top_area_opacity' => 37,
		'top_area_separator_color' => '#dfe5e8',
		'top_area_show' => '1',
		'top_area_socials' => '1',
		'top_area_style' => '1',
		'top_area_text_color' => '#5f727f',
		'top_area_transparency' => false,
		'top_area_width' => 'normal',
		'top_background_color' => '#ffffff',
		'top_background_gradient_angle' => '90',
		'top_background_gradient_point1_color' => '#E9ECDAFF',
		'top_background_gradient_point1_position' => '0',
		'top_background_gradient_point2_color' => '#D5F6FAFF',
		'top_background_gradient_point2_position' => '90',
		'top_background_gradient_position' => '',
		'top_background_gradient_type' => 'linear',
		'top_background_image' => '',
		'top_background_image_color' => '',
		'top_background_image_overlay' => '',
		'top_background_image_repeat' => '0',
		'top_background_pattern' => '',
		'top_background_position_x' => 'center',
		'top_background_position_y' => 'center',
		'top_background_size' => 'auto',
		'top_background_type' => 'color',
		'navigation_background_color' => '#ffffff',
		'navigation_background_gradient_angle' => '90',
		'navigation_background_gradient_point1_color' => '#E9ECDAFF',
		'navigation_background_gradient_point1_position' => '0',
		'navigation_background_gradient_point2_color' => '#D5F6FAFF',
		'navigation_background_gradient_point2_position' => '90',
		'navigation_background_gradient_position' => '',
		'navigation_background_gradient_type' => 'linear',
		'navigation_background_image' => '',
		'navigation_background_image_color' => '',
		'navigation_background_image_overlay' => '',
		'navigation_background_image_repeat' => '0',
		'navigation_background_pattern' => '',
		'navigation_background_position_x' => 'center',
		'navigation_background_position_y' => 'center',
		'navigation_background_size' => 'auto',
		'navigation_background_type' => 'color',
		'tumblr_active' => '',
		'tumblr_link' => '#',
		'twitter_active' => '1',
		'twitter_link' => '#',
		'use_light_menu_logo' => false,
		'viber_active' => '',
		'viber_link' => '#',
		'vimeo_active' => '',
		'vimeo_link' => '#',
		'vk_active' => '',
		'vk_link' => '#',
		'weibo_active' => '',
		'weibo_link' => '#',
		'whatsapp_active' => '',
		'whatsapp_link' => '#',
		'widget_active_link_color' => '#384554',
		'widget_hover_link_color' => '#00bcd4',
		'widget_link_color' => '#5f727f',
		'widget_title_color' => '#3c3950',
		'widget_title_font_family' => 'Montserrat',
		'widget_title_font_sets' => '',
		'widget_title_font_size' => '16',
		'widget_title_font_style' => '700',
		'widget_title_letter_spacing' => '0.8',
		'widget_title_line_height' => '30',
		'widget_title_text_transform' => 'uppercase',
		'woocommerce_activate_images_sizes' => '1',
		'woocommerce_catalog_image_height' => '652',
		'woocommerce_catalog_image_width' => '522',
		'woocommerce_price_font_family' => 'Montserrat',
		'woocommerce_price_font_sets' => '',
		'woocommerce_price_font_size' => '26',
		'woocommerce_price_font_style' => 'regular',
		'woocommerce_price_letter_spacing' => '',
		'woocommerce_price_line_height' => '36',
		'woocommerce_price_text_transform' => '',
		'woocommerce_product_image_height' => '744',
		'woocommerce_product_image_width' => '564',
		'woocommerce_thumbnail_image_height' => '160',
		'woocommerce_thumbnail_image_width' => '160',
		'wordpress_active' => '',
		'wordpress_link' => '#',
		'xlarge_custom_responsive_fonts' => '1',
		'xlarge_font_size_mobile' => '36',
		'xlarge_font_size_tablet' => '50',
		'xlarge_line_height_mobile' => '53',
		'xlarge_line_height_tablet' => '69',
		'xlarge_title_font_family' => 'Montserrat',
		'xlarge_title_font_sets' => '',
		'xlarge_title_font_size' => '80',
		'xlarge_title_font_style' => '700',
		'xlarge_title_letter_spacing' => '4',
		'xlarge_title_line_height' => '90',
		'xlarge_title_text_transform' => 'uppercase',
		'youtube_active' => '1',
		'youtube_link' => '#',
		'tiktok_active' => '',
		'tiktok_link' => '#',
	));
}

/* Update new options */
function thegem_version_update_options() {
	$newOptions = apply_filters('thegem_version_update_options_array', array (
		'3.0.0' => array(
			'page_padding_top' => '10',
			'page_padding_bottom' => '10',
			'page_padding_left' => '10',
			'page_padding_right' => '10',
			'mobile_menu_font_family' => 'Source Sans Pro',
			'mobile_menu_font_style' => 'regular',
			'mobile_menu_font_sets' => '',
			'mobile_menu_font_size' => '16',
			'mobile_menu_line_height' => '20',
			'styled_elements_color_4' => '#393d50',
			'mobile_menu_background_color' => '',
			'mobile_menu_level1_color' => '#5f727f',
			'mobile_menu_level1_background_color' => '#f4f6f7',
			'mobile_menu_level1_active_color' => '#3c3950',
			'mobile_menu_level1_active_background_color' => '#ffffff',
			'mobile_menu_level2_color' => '#5f727f',
			'mobile_menu_level2_background_color' => '#f4f6f7',
			'mobile_menu_level2_active_color' => '#3c3950',
			'mobile_menu_level2_active_background_color' => '#ffffff',
			'mobile_menu_level3_color' => '#5f727f',
			'mobile_menu_level3_background_color' => '#f4f6f7',
			'mobile_menu_level3_active_color' => '#3c3950',
			'mobile_menu_level3_active_background_color' => '#ffffff',
			'mobile_menu_border_color' => '#dfe5e8',
			'mobile_menu_social_icon_color' => '',
			'mobile_menu_hide_color' => '',
			'product_title_listing_font_family' => 'Montserrat',
			'product_title_listing_font_style' => '700',
			'product_title_listing_font_sets' => 'latin,latin-ext',
			'product_title_listing_font_size' => '16',
			'product_title_listing_line_height' => '25',
			'product_title_page_font_family' => 'Montserrat UltraLight',
			'product_title_page_font_style' => 'regular',
			'product_title_page_font_sets' => 'latin,latin-ext',
			'product_title_page_font_size' => '28',
			'product_title_page_line_height' => '42',
			'product_title_widget_font_family' => 'Source Sans Pro',
			'product_title_widget_font_style' => 'regular',
			'product_title_widget_font_sets' => 'latin,latin-ext',
			'product_title_widget_font_size' => '16',
			'product_title_widget_line_height' => '25',
			'product_title_cart_font_family' => 'Source Sans Pro',
			'product_title_cart_font_style' => 'regular',
			'product_title_cart_font_sets' => 'latin,latin-ext',
			'product_title_cart_font_size' => '16',
			'product_title_cart_line_height' => '25',
			'product_price_listing_font_family' => 'Source Sans Pro',
			'product_price_listing_font_style' => 'regular',
			'product_price_listing_font_sets' => 'latin,latin-ext',
			'product_price_listing_font_size' => '18',
			'product_price_listing_line_height' => '18',
			'product_price_page_font_family' => 'Source Sans Pro',
			'product_price_page_font_style' => '300',
			'product_price_page_font_sets' => 'latin,latin-ext',
			'product_price_page_font_size' => '36',
			'product_price_page_line_height' => '36',
			'product_price_widget_font_family' => 'Source Sans Pro',
			'product_price_widget_font_style' => '300',
			'product_price_widget_font_sets' => 'latin,latin-ext',
			'product_price_widget_font_size' => '20',
			'product_price_widget_line_height' => '30',
			'product_price_cart_font_family' => 'Source Sans Pro',
			'product_price_cart_font_style' => '300',
			'product_price_cart_font_sets' => 'latin,latin-ext',
			'product_price_cart_font_size' => '24',
			'product_price_cart_line_height' => '30',
			'product_title_listing_color' => '#5f727f',
			'product_title_page_color' => '#3c3950',
			'product_title_widget_color' => '#5f727f',
			'product_title_cart_color' => '#00bcd4',
			'product_price_listing_color' => '#00BCD4FF',
			'product_price_page_color' => '#3c3950',
			'product_price_widget_color' => '#3c3950',
			'product_price_cart_color' => '#3c3950',
			'product_separator_listing_color' => '#000000',
		),
		'3.1.0' => array(
			'woocommerce_activate_images_sizes' => '1',
			'woocommerce_catalog_image_width' => '522',
			'woocommerce_catalog_image_height' => '652',
			'woocommerce_product_image_width' => '564',
			'woocommerce_product_image_height' => '744',
			'woocommerce_thumbnail_image_width' => '160',
			'woocommerce_thumbnail_image_height' => '160',
		),
		'3.8.4' => array(
			'title_excerpt_font_family' => 'Source Sans Pro',
			'title_excerpt_font_style' => '300',
			'title_excerpt_font_sets' => '',
			'title_excerpt_font_size' => '24',
			'title_excerpt_line_height' => '37',
			'title_excerpt_font_size_tablet' => '24',
			'title_excerpt_line_height_tablet' => '37',
			'title_excerpt_font_size_mobile' => '24',
			'title_excerpt_line_height_mobile' => '37',
		),
		'5.0.0' => array(
			'basic_outer_background_type' => 'color',
			'body_letter_spacing' => '',
			'body_text_transform' => 'none',
			'counter_letter_spacing' => '',
			'counter_text_transform' => 'uppercase',
			'footer' => 1,
			'footer_background_type' => 'color',
			'footer_widget_area_background_type' => 'color',
			'footer_widget_area_background_position_x' => 'center',
			'footer_widget_area_background_position_y' => 'top',
			'footer_widget_area_background_size' => 'cover',
			'main_background_type' => 'color',
			'main_background_position_x' => 'left',
			'main_background_position_y' => 'top',
			'main_background_size' => 'auto',
			'main_background_image_repeat' => '1',
			'gallery_description_letter_spacing' => '',
			'gallery_description_text_transform' => '',
			'gallery_title_letter_spacing' => '',
			'gallery_title_text_transform' => 'uppercase',
			'h1_letter_spacing' => '',
			'h1_text_transform' => 'uppercase',
			'h2_letter_spacing' => '',
			'h2_text_transform' => 'uppercase',
			'h3_letter_spacing' => '',
			'h3_text_transform' => 'uppercase',
			'h4_letter_spacing' => '',
			'h4_text_transform' => 'uppercase',
			'h5_letter_spacing' => '',
			'h5_text_transform' => 'uppercase',
			'h6_letter_spacing' => '',
			'h6_text_transform' => 'uppercase',
			'xlarge_title_letter_spacing' => '',
			'xlarge_title_text_transform' => 'uppercase',
			'product_title_page_letter_spacing' => '',
			'product_title_page_text_transform' => 'uppercase',
			'main_background_type' => 'color',
			'main_menu_letter_spacing' => '',
			'main_menu_text_transform' => 'uppercase',
			'mobile_menu_letter_spacing' => '',
			'mobile_menu_text_transform' => 'none',
			'overlay_menu_letter_spacing' => '',
			'overlay_menu_text_transform' => 'uppercase',
			'quickfinder_description_letter_spacing' => '',
			'quickfinder_description_text_transform' => 'none',
			'quickfinder_title_letter_spacing' => '',
			'quickfinder_title_text_transform' => 'uppercase',
			'quickfinder_title_thin_letter_spacing' => '',
			'quickfinder_title_thin_text_transform' => 'uppercase',
			'styled_subtitle_letter_spacing' => '',
			'styled_subtitle_text_transform' => 'none',
			'submenu_letter_spacing' => '',
			'submenu_text_transform' => 'none',
			'tabs_title_letter_spacing' => '',
			'tabs_title_text_transform' => 'uppercase',
			'tabs_title_thin_letter_spacing' => '',
			'tabs_title_thin_text_transform' => 'uppercase',
			'testimonial_letter_spacing' => '',
			'testimonial_text_transform' => 'none',
			'title_excerpt_letter_spacing' => '',
			'title_excerpt_text_transform' => 'none',
			'top_area_background_type' => 'color',
			'top_area_button' => true,
			'top_background_type' => 'color',
			'widget_title_letter_spacing' => '',
			'widget_title_text_transform' => 'uppercase',
			'global_settings_apply_blog' => '',
			'global_settings_apply_default' => '',
			'global_settings_apply_portfolio' => '',
			'global_settings_apply_post' => '',
			'global_settings_apply_product' => '',
			'global_settings_apply_product_categories' => '',
			'global_settings_apply_search' => '',
			'preloader' => '',
			'gradient_hover_angle' => '90',
			'gradient_hover_point1_color' => 'rgba(255,43,88,0.8)',
			'gradient_hover_point1_position' => '0',
			'gradient_hover_point2_color' => 'rgba(255,216,0,0.8)',
			'gradient_hover_point2_position' => '100',
			'gradient_hover_position' => '',
			'gradient_hover_type' => 'linear',
			'circular_overlay_hover_angle' => '90',
			'circular_overlay_hover_point1_color' => 'rgba(0, 188, 212,0.75)',
			'circular_overlay_hover_point1_position' => '0',
			'circular_overlay_hover_point2_color' => 'rgba(53, 64, 147,0.75)',
			'circular_overlay_hover_point2_position' => '100',
			'circular_overlay_hover_position' => '',
			'circular_overlay_hover_type' => 'linear',
			'show_menu_socials' => '1',
			'show_menu_socials_mobile' => '1',
			'product_gallery' => 'legacy',
			'product_gallery_type' => 'horizontal',
			'product_gallery_show_image' => 'hover',
			'product_gallery_zoom' => '1',
			'product_gallery_lightbox' => '1',
			'product_gallery_labels' => '1',
			'product_gallery_label_sale' => '1',
			'product_gallery_label_new' => '1',
			'product_gallery_label_out_stock' => '1',
			'product_gallery_auto_height' => '1',
			'product_gallery_elements_color' => '',
			'product_gallery_retina_ready' => '0',
			'widget_triangle_color' => thegem_get_option('widget_triangle_color') ? thegem_get_option('widget_triangle_color') : thegem_get_option('styled_elements_color_3'),
		),
		'5.0.2' => array(
			'product_grid_title_legacy' => '0',
			'product_grid_title_font_family' => 'Montserrat',
			'product_grid_title_font_size' => '14',
			'product_grid_title_font_style' => '700normal',
			'product_grid_title_font_sets' => '',
			'product_grid_title_line_height' => '18.2',
			'product_grid_title_text_transform' => 'none',
			'product_grid_title_letter_spacing' => '0',
			'product_grid_title_color' => '#5F727FFF',
			'product_grid_title_color_hover' => '#3C3950FF',
			'product_grid_title_color_on_image' => '#212227FF',
			'product_grid_category_font_family' => 'Montserrat',
			'product_grid_category_font_size' => '9',
			'product_grid_category_font_style'  => '500normal',
			'product_grid_category_font_sets' => '',
			'product_grid_category_line_height' => '10.8',
			'product_grid_category_text_transform' => 'uppercase',
			'product_grid_category_letter_spacing' => '0.45',
			'product_grid_category_color' => '#99A9B5FF',
			'product_grid_category_color_hover' => '#00BCD4FF',
			'product_grid_category_color_on_image' => '#FFFFFFFF',
			'product_grid_price_color' => '#5F727FFF',
			'product_grid_price_color_on_image' => '#212227FF',
			'product_grid_button_border_width' => '1',
			'product_grid_button_border_radius' => '30',
			'product_grid_button_add_to_cart_color' => '#5F727FFF',
			'product_grid_button_add_to_cart_color_hover' => '#FFFFFFFF',
			'product_grid_button_add_to_cart_background' => '#DFE5E8FF',
			'product_grid_button_add_to_cart_background_hover' => '#00BCD4FF',
			'product_grid_button_add_to_cart_border_color' => '#DFE5E8FF',
			'product_grid_button_add_to_cart_border_color_hover' => '#00BCD4FF',
			'product_grid_button_select_options_color' => '#5F727FFF',
			'product_grid_button_select_options_color_hover' => '#FFFFFFFF',
			'product_grid_button_select_options_background' => '',
			'product_grid_button_select_options_background_hover' => '#00BCD4FF',
			'product_grid_button_select_options_border_color' => '#5F727FFF',
			'product_grid_button_select_options_border_color_hover' => '#00BCD4FF',
			'product_grid_icons_border_width' => '0',
			'product_grid_icons_border_radius' => '20',
			'product_grid_icons_color' => '#5F727FFF',
			'product_grid_icons_color_hover' => '#FFFFFFFF',
			'product_grid_icons_caption_image_color' => '#212227FF',
			'product_grid_icons_caption_image_color_hover' => '#FFFFFFFF',
			'product_grid_icons_background' => '#DFE5E8FF',
			'product_grid_icons_background_hover' => '#00BCD4FF',
			'product_grid_icons_border_color' => '',
			'product_grid_icons_border_color_hover' => '',
			'product_grid_quick_view_color' => '#FFFFFFFF',
			'product_grid_quick_view_background' => '#00BCD4A6',
			'product_labels_font_family' => thegem_get_option('h6_font_family') ? thegem_get_option('h6_font_family') : 'Montserrat',
			'product_labels_font_size' => '7',
			'product_labels_font_style' => thegem_get_option('h6_font_style') ? thegem_get_option('h6_font_style') : '700normal',
			'product_labels_font_sets' => thegem_get_option('h6_font_sets') ? thegem_get_option('h6_font_sets') : '',
			'product_labels_line_height' => '8.4',
			'product_labels_text_transform' => thegem_get_option('h6_text_transform') ? thegem_get_option('h6_text_transform') : 'uppercase',
			'product_labels_letter_spacing' => '0.35',
			'product_labels_text_color' => thegem_get_option('main_background_color') ? thegem_get_option('main_background_color') : '#FFFFFFFF',
			'product_labels_sale_background' => thegem_get_option('styled_elements_color_1') ? thegem_get_option('styled_elements_color_1') : '#00BCD4FF',
			'product_labels_new_background' => thegem_get_option('styled_elements_color_4') ? thegem_get_option('styled_elements_color_4') : '#393D50FF',
			'product_labels_out_of_stock_background' => '#F44336FF',
			'hide_card_icon' => thegem_get_option('hide_card_icon') == '0' || thegem_get_option('hide_card_icon') == '' ? '0' : '1',
		),
		'5.1.0' => array(
			'page_layout_breadcrumbs' => '0',
			'page_layout_breadcrumbs_default_color' => '#99A9B5FF',
			'page_layout_breadcrumbs_active_color' => '#3C3950FF',
			'page_layout_breadcrumbs_hover_color' => '#3C3950FF',
			'page_layout_breadcrumbs_alignment' => 'left',
			'page_layout_breadcrumbs_bottom_spacing' => '0',
			'page_layout_breadcrumbs_shop_category' => '0',
			'product_gallery_column_width' => '50',
			'product_labels_style' => '1',
			'product_label_new_text' => 'New',
			'product_label_sale_text' => 'On Sale',
			'product_label_out_of_stock_text' => 'Out of stock',
			'product_label_sale_type' => 'percentage',
			'product_label_sale_prefix' => '-',
			'product_label_sale_suffix' => '%',
			'product_title_show' => '1',
			'product_header_separator' => '0',
			'product_content_padding_top' => '70',
			'product_content_padding_top_tablet' => '',
			'product_content_padding_top_mobile' => '',
			'product_archive_type' => 'legacy',
			'product_archive_layout' => 'justified',
			'product_archive_columns_desktop' => '4x',
			'product_archive_columns_tablet' => '3x',
			'product_archive_columns_mobile' => '2x',
			'product_archive_columns_100' => '5',
			'product_archive_size_desktop' => '42',
			'product_archive_size_tablet' => '42',
			'product_archive_size_mobile' => '42',
			'product_archive_image_aspect_ratio' => 'portrait',
			'product_archive_preset_type' => 'below',
			'product_archive_preset_below' => 'default-cart-button',
			'product_archive_preset_on_image' => '',
			'product_archive_preset_on_hover' => '',
			'product_archive_quick_view' => '0',
			'product_archive_quick_view_text' => 'Quick View',
			'product_archive_orderby' => 'default',
			'product_archive_order' => 'asc',
			'product_archive_show_sorting' => '0',
			'product_archive_category_description_position' => 'above',
			'product_archive_show_categories_desktop' => '1',
			'product_archive_show_categories_tablet' => '1',
			'product_archive_show_categories_mobile' => '0',
			'product_archive_show_title' => '1',
			'product_archive_show_price' => '1',
			'product_archive_show_reviews_desktop' => '1',
			'product_archive_show_reviews_tablet' => '1',
			'product_archive_show_reviews_mobile' => '0',
			'product_archive_show_add_to_cart' => '1',
			'product_archive_add_to_cart_type' => 'button',
			'product_archive_cart_button_show_icon' => '1',
			'product_archive_cart_button_text' => 'Add To Cart',
			'product_archive_cart_icon' => '',
			'product_archive_cart_icon_pack' => '',
			'product_archive_select_options_button_text' => 'Select Options',
			'product_archive_select_options_icon' => '',
			'product_archive_select_options_icon_pack' => '',
			'product_archive_show_wishlist' => '1',
			'product_archive_add_wishlist_icon' => '',
			'product_archive_add_wishlist_icon_pack' => '',
			'product_archive_added_wishlist_icon' => '',
			'product_archive_added_wishlist_icon_pack' => '',
			'product_archive_items_per_page_desktop' => '12',
			'product_archive_items_per_page_tablet' => '12',
			'product_archive_items_per_page_mobile' => '12',
			'product_archive_show_pagination' => '1',
			'product_archive_pagination_type' => 'normal',
			'product_archive_more_button_text' => 'Load More',
			'product_archive_more_icon' => '',
			'product_archive_more_icon_pack' => '',
			'product_archive_more_stretch_full_width' => '',
			'product_archive_more_show_separator' => '',
			'product_archive_labels' => '1',
			'product_archive_label_sale' => '1',
			'product_archive_label_new' => '1',
			'product_archive_label_out_stock' => '1',
			'product_archive_filters_type' => 'disabled',
			'product_archive_filters_ajax' => '0',
			'product_archive_scroll_to_top' => '1',
			'product_archive_remove_attr_counts' => '0',
			'product_archive_filters_style' => 'standard',
			'product_archive_filters_style_native' => 'sidebar',
			'product_archive_filter_by_categories' => '1',
			'product_archive_filter_by_categories_hierarchy' => '0',
			'product_archive_filter_by_categories_count' => '0',
			'product_archive_filter_by_categories_title' => 'Categories',
			'product_archive_filter_by_categories_order_by' => 'name',
			'product_archive_filter_by_price' => '0',
			'product_archive_filter_by_price_title' => 'Price',
			'product_archive_filter_by_attribute' => '0',
			'product_archive_filter_by_attribute_count' => '0',
			'product_archive_filter_by_attribute_hide_empty' => '0',
			'product_archive_filter_by_attribute_data' => '',
			'product_archive_filter_by_status' => '0',
			'product_archive_filter_by_status_title' => 'Status',
			'product_archive_filter_by_status_sale' => '1',
			'product_archive_filter_by_status_sale_text' => 'On Sale',
			'product_archive_filter_by_status_stock' => '1',
			'product_archive_filter_by_status_stock_text' => 'In Stock',
			'product_archive_filter_by_status_count' => '0',
			'product_archive_filter_by_search' => '0',
			'product_archive_filters_text_labels_all_text' => 'Show All',
			'product_archive_filters_text_labels_clear_text' => 'Clear Filters',
			'product_archive_filters_text_labels_search_text' => 'Search by Product',
			'product_archive_filter_buttons_hidden_show_text' => 'Show filters',
			'product_archive_filter_buttons_hidden_sidebar_title' => 'Filter',
			'product_archive_filter_buttons_hidden_filter_by_text' => 'Filter By',
			'product_archive_added_cart_text' => 'Item added to cart',
			'product_archive_added_wishlist_text' => 'Item added to wishlist',
			'product_archive_removed_wishlist_text' => 'Item removed from wishlist',
			'product_archive_view_cart_button_text' => 'View Cart',
			'product_archive_checkout_button_text' => 'Checkout',
			'product_archive_view_wishlist_button_text' => 'View Wishlist',
			'product_archive_not_found_text' => 'No items were found matching your selection.',
			'product_archive_loading_animation' => '0',
			'product_archive_animation_effect' => 'move-up',
			'product_archive_ignore_highlights' => '1',
			'product_archive_skeleton_loader' => '0',
			'product_archive_featured_only' => '0',
			'product_archive_sale_only' => '0',
			'product_archive_social_sharing' => '0',
			'product_archive_customize_styles' => '0',
			'product_archive_image_hover_effect_image' => 'fade',
			'product_archive_image_hover_effect_page' => 'fade',
			'product_archive_image_hover_effect_hover' => 'fade',
			'product_archive_image_hover_effect_fallback' => 'zooming',
			'product_archive_caption_container_preset' => 'transparent',
			'product_archive_caption_container_alignment_desktop' => '',
			'product_archive_caption_container_alignment_tablet' => '',
			'product_archive_caption_container_alignment_mobile' => '',
			'product_archive_caption_container_alignment_hover' => '',
			'product_archive_caption_container_background_color' => '',
			'product_archive_caption_container_background_color_hover' => '',
			'product_archive_caption_container_separator' => '',
			'product_archive_caption_container_separator_color' => '',
			'product_archive_caption_container_separator_color_hover' => '',
			'product_archive_caption_container_separator_width' => '',
			'product_archive_caption_container_preset_hover' => 'light',
			'product_archive_caption_container_preset_hover_background_color' => '',
			'product_archive_caption_container_size_desktop' => '',
			'product_archive_caption_container_size_tablet' => '',
			'product_archive_caption_container_size_mobile' => '',
			'product_archive_stay_visible' => '4000',
			'product_page_layout' => 'legacy',
			'product_page_layout_style' => 'horizontal_tabs',
			'product_page_layout_preset' => 'col-50-50',
			'product_page_layout_fullwidth' => '0',
			'product_page_layout_sticky' => '0',
			'product_page_layout_title_area' => 'disabled',
			'product_page_ajax_add_to_cart' => '1',
			'product_page_desc_review_source' => 'extra_description',
			'product_page_desc_review_layout' => 'tabs',
			'product_page_desc_review_layout_tabs_style' => 'horizontal',
			'product_page_desc_review_layout_tabs_alignment' => 'left',
			'product_page_desc_review_layout_acc_position' => 'below_gallery',
			'product_page_desc_review_layout_one_by_one_description_background' => '#F4F6F7FF',
			'product_page_desc_review_layout_one_by_one_additional_info_background' => '#FFFFFFFF',
			'product_page_desc_review_layout_one_by_one_reviews_background' => '#F4F6F7FF',
			'product_page_desc_review_description' => '1',
			'product_page_desc_review_description_title' => 'Description',
			'product_page_desc_review_additional_info' => '1',
			'product_page_desc_review_additional_info_title' => 'Additional Info',
			'product_page_desc_review_reviews' => '1',
			'product_page_desc_review_reviews_title' => 'Reviews',
			'product_page_button_add_to_cart_text' => 'Add to Cart',
			'product_page_button_add_to_cart_icon' => 'f1e7',
			'product_page_button_add_to_cart_icon_pack' => 'material',
			'product_page_button_add_to_cart_icon_position' => 'left',
			'product_page_button_add_to_cart_border_width' => '',
			'product_page_button_add_to_cart_border_radius' => '',
			'product_page_button_add_to_cart_color' => '',
			'product_page_button_add_to_cart_color_hover' => '',
			'product_page_button_add_to_cart_background' => '',
			'product_page_button_add_to_cart_background_hover' => '',
			'product_page_button_add_to_cart_border_color' => '',
			'product_page_button_add_to_cart_border_color_hover' => '',
			'product_page_button_add_to_wishlist_icon' => 'f37b',
			'product_page_button_add_to_wishlist_icon_pack' => 'material',
			'product_page_button_add_to_wishlist_color' => '',
			'product_page_button_add_to_wishlist_color_hover' => '',
			'product_page_button_add_to_wishlist_color_filled' => '',
			'product_page_button_added_to_wishlist_icon' => 'f377',
			'product_page_button_added_to_wishlist_icon_pack' => 'material',
			'product_page_button_clear_attributes_text' => 'Clear selection',
			'product_page_elements_prev_next' => '1',
			'product_page_elements_preview_on_hover' => '1',
			'product_page_elements_back_to_shop' => '1',
			'product_page_elements_back_to_shop_link' => 'main_shop',
			'product_page_elements_back_to_shop_link_custom_url' => '',
			'product_page_elements_title' => '1',
			'product_page_elements_attributes' => '0',
			'product_page_elements_attributes_data' => '',
			'product_page_elements_reviews' => '1',
			'product_page_elements_reviews_text' => 'customer reviews',
			'product_page_elements_price' => '1',
			'product_page_elements_price_strikethrough' => '1',
			'product_page_elements_description' => '1',
			'product_page_elements_stock_amount' => '1',
			'product_page_elements_stock_amount_text' => 'Products in stock',
			'product_page_elements_size_guide' => '1',
			'product_page_elements_sku' => '1',
			'product_page_elements_sku_title' => 'SKU',
			'product_page_elements_categories' => '1',
			'product_page_elements_categories_title' => 'Categories',
			'product_page_elements_tags' => '1',
			'product_page_elements_tags_title' => 'Tags',
			'product_page_elements_share' => '1',
			'product_page_elements_share_title' => 'Share',
			'product_page_elements_upsell' => '1',
			'product_page_elements_upsell_title' => 'You may also like',
			'product_page_elements_upsell_items' => '-1',
			'product_page_elements_upsell_columns_desktop' => '4x',
			'product_page_elements_upsell_columns_tablet' => '3x',
			'product_page_elements_upsell_columns_mobile' => '2x',
			'product_page_elements_upsell_columns_100' => '5',
			'product_page_elements_related' => '1',
			'product_page_elements_related_title' => 'Related Products',
			'product_page_elements_related_items' => '4',
			'product_page_elements_related_columns_desktop' => '4x',
			'product_page_elements_related_columns_tablet' => '3x',
			'product_page_elements_related_columns_mobile' => '2x',
			'product_page_elements_related_columns_100' => '5',
			'product_hide_social_sharing' => '0',
			'product_title_checkout_color' => thegem_get_option('hover_link_color') ? thegem_get_option('hover_link_color') : '#393D50FF',
			'cart_layout' => 'classic',
			'cart_elements_cross_sells' => '1',
			'cart_elements_cross_sells_columns_100' => '5',
			'cart_elements_cross_sells_columns_desktop' => '4x',
			'cart_elements_cross_sells_columns_mobile' => '2x',
			'cart_elements_cross_sells_columns_tablet' => '3x',
			'cart_elements_cross_sells_items' => '-1',
			'cart_elements_cross_sells_title' => 'You may be interested in',
			'cart_empty_text' => 'No products in the cart.',
			'cart_icon' => '',
			'cart_icon_pack' => '',
			'checkout_thank_you_default' => '1',
			'checkout_thank_you_extra' => '',
			'modern_cart_steps' => '1',
			'modern_cart_steps_position' => 'title_area',
			'footer_widget_woocommerce' => '1',
		),
	));
	$theme_options = get_option('thegem_theme_options');
	$thegem_theme = wp_get_theme(wp_get_theme()->get('Template'));
	foreach($newOptions as $version => $values) {
		if(version_compare($version, thegem_get_option('theme_version')) > 0) {
			foreach($values as $optionName => $value) {
				$theme_options[$optionName] = $value;
			}
		}
	}
	$theme_options['theme_version'] = $thegem_theme->get('Version');
	update_option('thegem_theme_options', $theme_options);
}

function thegem_migrate_new_options() {
	$old_options = get_option('thegem_theme_options');
	ksort($old_options);
	$newOptions = array();
	update_option('thegem_theme_options_old', $old_options);
	if(!empty($old_options['disable_uppercase_font'])) {
		$old_options['h1_text_transform'] = 'none';
		$old_options['h2_text_transform'] = 'none';
		$old_options['h3_text_transform'] = 'none';
		$old_options['h4_text_transform'] = 'none';
		$old_options['h5_text_transform'] = 'none';
		$old_options['h6_text_transform'] = 'none';
		$old_options['widget_title_text_transform'] = 'none';
		$old_options['product_title_page_text_transform'] = 'none';
		$old_options['main_menu_text_transform'] = 'none';
		$old_options['quickfinder_title_text_transform'] = 'none';
		$old_options['quickfinder_title_thin_text_transform'] = 'none';
		$old_options['overlay_menu_text_transform'] = 'none';
	} elseif($old_options['mobile_menu_layout'] == 'overlay') {
		$old_options['mobile_menu_text_transform'] = 'uppercase';
	}
	$old_options['button_text_transform'] = 'uppercase';
	$old_options['button_thin_text_transform'] = 'uppercase';
	$global_settings = thegem_theme_options_get_page_settings('global');
	$global_settings['title_background_color'] = $old_options['title_bar_background_color'];
	$global_settings['title_background_image_color'] = $old_options['title_bar_background_color'];
	$global_settings['title_text_color'] = $old_options['title_bar_text_color'];
	$global_settings['title_excerpt_text_color'] = $old_options['title_bar_text_color'];
	$global_settings['title_breadcrumbs'] = !empty($old_options['global_hide_breadcrumbs']);
	thegem_theme_options_set_page_settings('global', $global_settings);
	$default_settings = thegem_theme_options_get_page_settings('default');
	$post_settings = $default_settings;
	$portfolio_settings = $default_settings;
	thegem_theme_options_set_page_settings('post', $post_settings);
	thegem_theme_options_set_page_settings('portfolio', $portfolio_settings);
	foreach($old_options as $option => $value) {
		switch ($option) {
			case 'basic_outer_background_color':
				$newOptions['basic_outer_background_color'] = $old_options[$option];
				$newOptions['basic_outer_background_image_color'] = $old_options[$option];
				$newOptions['basic_outer_background_type'] = 'color';
				$old_options['basic_outer_background_type'] = 'color';
				break;
			case 'basic_outer_background_image':
				if(!empty($old_options[$option])) {
					$newOptions['basic_outer_background_image'] = $old_options[$option];
					$newOptions['basic_outer_background_type'] = 'image';
					$old_options['basic_outer_background_type'] = 'image';
				}
				break;
			case 'custom_footer':
				if(!empty($old_options[$option])) {
					$newOptions['custom_footer_enable'] = '1';
				} else {
					$newOptions['custom_footer_enable'] = '';
				}
				$newOptions['custom_footer'] = $old_options[$option];
				break;
			case 'footer_background_color':
				$newOptions['footer_background_color'] = $old_options[$option];
				$newOptions['footer_background_image_color'] = $old_options[$option];
				$newOptions['footer_background_type'] = 'color';
				$old_options['footer_background_type'] = 'color';
				break;
			case 'footer_background_image':
				if(!empty($old_options[$option])) {
					$newOptions['footer_background_image'] = $old_options[$option];
					$newOptions['footer_background_type'] = 'image';
					$old_options['footer_background_type'] = 'image';
				}
				break;
			case 'footer_widget_area_background_color':
				$newOptions['footer_widget_area_background_color'] = $old_options[$option];
				$newOptions['footer_widget_area_background_image_color'] = $old_options[$option];
				$newOptions['footer_widget_area_background_type'] = 'color';
				$old_options['footer_widget_area_background_type'] = 'color';
				break;
			case 'footer_widget_area_background_image':
				if(!empty($old_options[$option])) {
					$newOptions['footer_widget_area_background_image'] = $old_options[$option];
					$newOptions['footer_widget_area_background_type'] = 'image';
					$old_options['footer_widget_area_background_type'] = 'image';
				}
				break;
			case 'main_background_color':
				$newOptions['main_background_color'] = $old_options[$option];
				$newOptions['main_background_image_color'] = $old_options[$option];
				$newOptions['main_background_type'] = 'color';
				$old_options['main_background_type'] = 'color';
				break;
			case 'main_background_image':
				if(!empty($old_options[$option])) {
					$newOptions['main_background_image'] = $old_options[$option];
					$newOptions['main_background_type'] = 'image';
					$old_options['main_background_type'] = 'image';
				}
				break;
			case 'top_area_background_color':
				$newOptions['top_area_background_color'] = $old_options[$option];
				$newOptions['top_area_background_image_color'] = $old_options[$option];
				$newOptions['top_area_background_type'] = 'color';
				$old_options['top_area_background_type'] = 'color';
				break;
			case 'top_area_background_image':
				if(!empty($old_options[$option])) {
					$newOptions['top_area_background_image'] = $old_options[$option];
					$newOptions['top_area_background_type'] = 'image';
					$old_options['top_area_background_type'] = 'image';
				}
				break;
			case 'top_area_style':
				$newOptions['top_area_style'] = $old_options[$option];
				if(empty($old_options[$option])) {
					$newOptions['top_area_show'] = '';
					$newOptions['top_area_style'] = '1';
					$newOptions['top_area_disable_mobile'] = '1';
					$newOptions['top_area_disable_tablet'] = '1';
				} else {
					$newOptions['top_area_show'] = '1';
				}
				break;
			case 'top_area_button_text':
				$newOptions['top_area_button_text'] = $old_options[$option];
				if(!empty($old_options[$option])) {
					$newOptions['top_area_button'] = true;
				}
				break;
			case 'top_background_color':
				$newOptions['top_background_color'] = $old_options[$option];
				$newOptions['top_background_image_color'] = $old_options[$option];
				$newOptions['top_background_type'] = 'color';
				$old_options['top_background_type'] = 'color';
				$newOptions['navigation_background_color'] = $old_options[$option];
				$newOptions['navigation_background_image_color'] = $old_options[$option];
				$newOptions['navigation_background_type'] = 'color';
				$old_options['navigation_background_type'] = 'color';
				break;
			case 'top_background_image':
				if(!empty($old_options[$option])) {
					$newOptions['top_background_image'] = $old_options[$option];
					$newOptions['top_background_type'] = 'image';
					$old_options['top_background_type'] = 'image';
				}
				break;
			case 'enable_page_preloader':
					$newOptions['preloader'] = $old_options[$option];
				break;
			case 'header_layout':
					$newOptions['header_layout'] = $old_options[$option];
					$newOptions['header_width'] = 'normal';
					if($newOptions['header_layout'] == 'fullwidth') {
						$newOptions['header_layout'] = 'default';
						$newOptions['header_width'] = 'full';
					}
					if($newOptions['header_layout'] == 'fullwidth_hamburger' || $newOptions['header_layout'] == 'overlay' || $newOptions['header_layout'] == 'perspective') {
						$newOptions['header_width'] = 'full';
					}
				break;
			case 'page_padding_left':
			case 'page_padding_top':
			case 'page_padding_right':
			case 'page_padding_bottom':
				$newOptions[$option] = $old_options[$option];
				if(intval($old_options[$option]) > 0) {
					$newOptions['page_layout_style'] = 'body-frame';
				}
			default:
				$newOptions[$option] = $old_options[$option];
		}
	}
	if(empty($old_options['disable_smooth_scroll'])) {
		$newOptions['disable_smooth_scroll'] = 0;
	}
	if(empty($old_options['disable_scroll_top_button'])) {
		$newOptions['disable_scroll_top_button'] = 0;
	}
	if(empty($old_options['footer_widget_area_hide'])) {
		$newOptions['footer_widget_area_hide'] = 0;
	}

	if(function_exists('wc_get_page_id') && $shop_page = get_page(wc_get_page_id('shop'))) {
		$page_data = get_post_meta($shop_page->ID, 'thegem_page_data', true);
		if(is_array($page_data) && !isset($page_data['title_show'])) {
			update_option('thegem_options_page_settings_product_categories', $page_data);
			$newOptions['global_settings_apply_product_categories'] = '1';
		}
	}

	$newOptions['global_settings_apply_blog'] = '1';
	$newOptions['global_settings_apply_search'] = '1';
	update_option('thegem_theme_options', $newOptions);
	thegem_get_option(false, false, false, true);

	foreach(array('blog', 'default', 'portfolio', 'post', 'product', 'product_categories', 'search') as $type) {
		$old_options = get_option('thegem_options_page_settings_'.$type);
		update_option('thegem_options_page_settings_'.$type.'_old', $old_options);
		$new_options = array();
		if(!is_array($old_options)) continue;
		foreach($old_options as $option => $value) {
			switch ($option) {
				case 'title_style':
					if($old_options[$option] == 0) {
						$new_options['title_style'] = 1;
						$new_options['title_show'] = '';
					} else {
						$new_options['title_style'] = $old_options[$option];
						$new_options['title_show'] = '1';
					}
					break;
				case 'title_alignment':
					$new_options['title_alignment'] = $old_options[$option];
					$new_options['title_breadcrumbs_alignment'] = $old_options[$option];
					break;
				case 'title_background_color':
					if(empty($old_options['title_background_image'])) {
						$new_options['title_background_type'] = 'color';
					}
					$new_options['title_background_color'] = $old_options[$option];
					$new_options['title_background_image_color'] = $old_options[$option];
					break;
				case 'title_background_image':
					if(!empty($old_options[$option])) {
						$new_options['title_background_image'] = $old_options[$option];
						$new_options['title_background_type'] = 'image';
					}
					break;
				case 'title_video_background':
					if(!empty($old_options[$option])) {
						$new_options['title_background_type'] = 'video';
						$new_options['title_background_video'] = $old_options[$option];
					}
					break;
				case 'title_video_type':
					if(!empty($old_options[$option])) {
						$new_options['title_background_video_type'] = $old_options[$option];
					}
					break;
				case 'title_video_aspect_ratio':
					if(!empty($old_options[$option])) {
						$new_options['title_background_video_aspect_ratio'] = $old_options[$option];
					}
					break;
				case 'title_video_poster':
					if(!empty($old_options[$option])) {
						$new_options['title_background_video_poster'] = $old_options[$option];
					}
					break;
				case 'title_video_overlay_color':
					if(!empty($old_options[$option])) {
						$new_options['title_background_video_overlay'] = thegem_migrate_update_color($old_options[$option]).str_pad(dechex(ceil($old_options['title_video_overlay_opacity']*255)), 2, '0', STR_PAD_LEFT);
					}
					break;
				case 'title_padding_top':
						$new_options['title_padding_top'] = $old_options['title_padding_top'];
						$new_options['title_padding_top_mobile'] = $old_options['title_padding_top'];
						$new_options['title_padding_top_tablet'] = $old_options['title_padding_top'];
					break;
				case 'title_padding_bottom':
						$new_options['title_padding_bottom'] = $old_options['title_padding_bottom'];
						$new_options['title_padding_bottom_mobile'] = $old_options['title_padding_bottom'];
						$new_options['title_padding_bottom_tablet'] = $old_options['title_padding_bottom'];
					break;
				case 'title_breadcrumbs':
					if(empty($old_options[$option])) {
						$new_options['title_breadcrumbs'] = thegem_get_option('global_hide_breadcrumbs');
					} else {
						$new_options['title_breadcrumbs'] = $old_options[$option];
					}
					break;
				case 'header_hide_top_area':
					if(!empty($old_options[$option])) {
						$new_options['header_hide_top_area'] = '1';
					} else {
						$new_options['header_hide_top_area'] = !thegem_get_option('top_area_show');
					}
					break;
				case 'footer_hide_default':
					if(!empty($old_options[$option])) {
						$new_options['footer_hide_default'] = '1';
					} else {
						$new_options['footer_hide_default'] = !thegem_get_option('footer_active');
					}
					break;
				case 'footer_hide_widget_area':
					if(!empty($old_options[$option])) {
						$new_options['footer_hide_widget_area'] = '1';
					} else {
						$new_options['footer_hide_widget_area'] = thegem_get_option('footer_widget_area_hide');
					}
					break;
				case 'effects_hide_header':
					if(!empty($old_options[$option])) {
						$new_options['effects_hide_header'] = '1';
					} else {
						$new_options['effects_hide_header'] = '0';
					}
					break;
				case 'effects_hide_footer':
					if(!empty($old_options[$option])) {
						$new_options['effects_hide_footer'] = '1';
					} else {
						$new_options['effects_hide_footer'] = !thegem_get_option('footer');
					}
					break;
				case 'sidebar_position':
					if(!empty($old_options[$option])) {
						$new_options['sidebar_show'] = '1';
					} else {
						$new_options['sidebar_show'] = '0';
					}
					$new_options['sidebar_position'] = $old_options[$option];
					break;
				case 'slideshow_type':
					if(!empty($old_options[$option])) {
						$new_options['title_style'] = 3;
						$new_options['title_show'] = '1';
					}
					$new_options['slideshow_type'] = $old_options[$option];
					break;
				case 'footer_custom':
					if(!empty($old_options[$option])) {
						$new_options['footer_custom_show'] = '1';
						$new_options['footer_custom'] = $old_options[$option];
					} else {
						$new_options['footer_custom_show'] = thegem_get_option('custom_footer_enable');
						$new_options['footer_custom'] = thegem_get_option('custom_footer');
					}
					break;
				case 'title_background_parallax':
					if(!empty($old_options[$option])) {
						$new_options['title_background_effect'] = 'parallax';
					}
					break;
				case 'effects_no_top_margin':
					if(!empty($old_options[$option])) {
						$new_options['content_padding_top'] = '0';
					}
					break;
				case 'effects_no_bottom_margin':
					if(!empty($old_options[$option])) {
						$new_options['content_padding_bottom'] = '0';
					}
					break;
				case 'title_top_margin':
					if(empty($old_options[$option])) {
						$new_options['title_top_margin'] = '';
					} else {
						$new_options['title_top_margin'] = $old_options[$option];
					}
					break;
				default:
					$new_options[$option] = $old_options[$option];
			}
		}
		$global_settings = thegem_theme_options_get_page_settings('global');
		if($new_options['title_background_type'] == 'color' && empty($new_options['title_background_color']) && !empty($global_settings['title_background_color']) && $new_options['title_style'] != 2) {
			$new_options['title_background_color'] = $global_settings['title_background_color'];
		}
		if(empty($new_options['title_text_color']) && !empty($global_settings['title_text_color']) && $new_options['title_style'] != 2) {
			$new_options['title_text_color'] = $global_settings['title_text_color'];
		}
		if(empty($new_options['title_excerpt_text_color']) && !empty($global_settings['title_excerpt_text_color']) && $new_options['title_style'] != 2) {
			$new_options['title_excerpt_text_color'] = $global_settings['title_excerpt_text_color'];
		}
		if(!empty($new_options['title_xlarge']) && $new_options['title_style'] == 2) {
			$new_options['title_xlarge_custom_migrate'] = $new_options['title_xlarge'];
		}
		if(empty($new_options['effects_hide_footer'])) {
			$new_options['effects_hide_footer'] = '0';
		} else {
			$new_options['effects_hide_footer'] = '1';
		}
		thegem_theme_options_set_page_settings($type, $new_options);
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

/* Get theme option*/
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

function thegem_generate_custom_css() {
	thegem_get_option(false, false, false, true);
	ob_start();
	thegem_custom_fonts();
	require get_template_directory() . '/inc/custom-css.php';
	if(file_exists(get_stylesheet_directory() . '/inc/custom-css.php') && get_stylesheet_directory() != get_template_directory()) {
		require get_stylesheet_directory() . '/inc/custom-css.php';
	}
	$custom_css = ob_get_clean();
	ob_start();
	require get_template_directory() . '/inc/style-editor-css.php';
	$editor_css = ob_get_clean();
	$action = array('action');
	$url = wp_nonce_url('admin.php?page=thegem-theme-options','thegem-theme-options');
	if (false === ($creds = request_filesystem_credentials($url, '', false, get_stylesheet_directory() . '/css/', $action) ) ) {
		return 'generate_css_continue';
	}
	if(!WP_Filesystem($creds)) {
		request_filesystem_credentials($url, '', true, get_stylesheet_directory() . '/css/', $action);
		return 'generate_css_continue';
	}
	global $wp_filesystem;
	$old_name = thegem_get_custom_css_filename();
	$new_name = thegem_generate_custom_css_filename();
	if(!$wp_filesystem->put_contents($wp_filesystem->find_folder(get_stylesheet_directory()) . 'css/'.$new_name.'.css', $custom_css)) {
		update_option('thegem_genearte_css_error', '1');
?>
	<div class="error">
		<p><?php printf(esc_html__('TheGem\'s styles cannot be customized because file "%s" cannot be modified. Please check your server\'s settings. Then click "Save Changes" button.', 'thegem'), get_stylesheet_directory() . '/css/custom.css'); ?></p>
	</div>
<?php
	} else {
		$wp_filesystem->put_contents($wp_filesystem->find_folder(get_template_directory()) . 'css/style-editor.css', $editor_css);
		$custom_css_files = glob(get_template_directory().'/css/custom-*.css');
		foreach($custom_css_files as $file) {
			if(basename($file, '.css') != $new_name) {
				$wp_filesystem->delete($wp_filesystem->find_folder(get_stylesheet_directory()) . 'css/'.basename($file, '.css').'.css', $custom_css);
			}
		}
		thegem_save_custom_css_filename($new_name);
		delete_option('thegem_genearte_css_error');
		delete_option('thegem_generate_empty_custom_css_fail');
	}
}

function thegem_genearte_css_error() {
	if(isset($_GET['page']) && $_GET['page'] == 'thegem-theme-options' && get_option('thegem_genearte_css_error')) {
?>
	<div class="error">
		<p><?php printf(esc_html__('TheGem\'s styles cannot be customized because file "%s" cannot be modified. Please check your server\'s settings. Then click "Save Changes" button.', 'thegem'), get_stylesheet_directory() . '/css/custom.css'); ?></p>
	</div>
<?php
	}
}
add_action('admin_notices', 'thegem_genearte_css_error');

function thegem_activate() {
	global $pagenow;
	if(is_admin() && 'themes.php' == $pagenow && isset($_GET['activated'])) {
		wp_redirect(admin_url('admin.php?page=thegem-dashboard-welcome'));
		exit;
	}
}
add_action('after_setup_theme', 'thegem_activate', 11);

add_action('wp_ajax_thegem_submit_activation', 'thegem_submit_activation');

function thegem_get_activation_info() {
	$thegem_theme = wp_get_theme(wp_get_theme()->get('Template'));
	return $thegem_theme->get('Version');
}

function thegem_submit_activation() {
	delete_option('thegem_activation');
	if(!empty($_REQUEST['purchase_code'])) {
		$theme_options = get_option('thegem_theme_options');
		$theme_options['purchase_code'] = $_REQUEST['purchase_code'];
		update_option('thegem_theme_options', $theme_options);
		$response_p = wp_remote_get(add_query_arg(array('code' => $_REQUEST['purchase_code'], 'info'=>thegem_get_activation_info(), 'site_url' => get_site_url(), 'type' => 'elementor'), esc_url('http://democontent.codex-themes.com/av_validate_code.php')), array('timeout' => 20));

		if(is_wp_error($response_p)) {
			echo json_encode(array('status' => 0, 'message' => esc_html__('Some troubles with connecting to TheGem server.', 'thegem')));
		} else {
			$rp_data = json_decode($response_p['body'], true);
			if(is_array($rp_data) && isset($rp_data['result']) && $rp_data['result'] && isset($rp_data['item_id']) && $rp_data['item_id'] === '16061685') {
				$plugin_button_html = '<div class="activation-plugin-button">'.wp_kses(sprintf(__('<a href="%s">Begin installing plugins</a>', 'thegem'), admin_url('admin.php?page=install-required-plugins')), array('a' => array('href' => array(), 'class' => array()))).'</div>';
				echo json_encode(array('status' => 1, 'message' => esc_html__('Thank you, your purchase code is valid. TheGem has been activated.', 'thegem'), 'button' => $plugin_button_html));
				update_option('thegem_activation', 1);
				update_option('thegem_print_google_code', 1);
			} else {
				echo json_encode(array('status' => 0, 'message' => isset($rp_data['message']) ? $rp_data['message'] : esc_html__('The purchase code you have entered is not valid. TheGem has not been activated.', 'thegem')));
			}
		}
	} else {
		echo json_encode(array('status' => 0, 'message' => esc_html__('Purchase code is empty.', 'thegem')));
	}
	die(-1);
}

function thegem_check_activation($theme_options) {
	if(get_option('thegem_activation')) {
		if(empty($theme_options['purchase_code'])) {
			delete_option('thegem_activation');
		} elseif($theme_options['purchase_code'] !== thegem_get_option('purchase_code')) {
			delete_option('thegem_activation');

			$response_p = wp_remote_get(add_query_arg(array('code' => $theme_options['purchase_code'], 'info'=>thegem_get_activation_info(), 'site_url' => get_site_url(), 'type' => 'elementor'), esc_url('http://democontent.codex-themes.com/av_validate_code.php')), array('timeout' => 20));
			if(!is_wp_error($response_p)) {
				$rp_data = json_decode($response_p['body'], true);
				if(is_array($rp_data) && isset($rp_data['result']) && $rp_data['result'] && isset($rp_data['item_id']) && $rp_data['item_id'] === '16061685') {
					update_option('thegem_activation', 1);
				}
			}
		}
	} elseif(!empty($theme_options['purchase_code'])) {
		$response_p = wp_remote_get(add_query_arg(array('code' => $theme_options['purchase_code'], 'info'=>thegem_get_activation_info(), 'site_url' => get_site_url(), 'type' => 'elementor'), esc_url('http://democontent.codex-themes.com/av_validate_code.php')), array('timeout' => 20));
		if(!is_wp_error($response_p)) {
			$rp_data = json_decode($response_p['body'], true);
			if(is_array($rp_data) && isset($rp_data['result']) && $rp_data['result'] && isset($rp_data['item_id']) && $rp_data['item_id'] === '16061685') {
				update_option('thegem_activation', 1);
			}
		}
	}
}

function thegem_auto_check_activation_after_update() {
	$thegem_theme = wp_get_theme(wp_get_theme()->get('Template'));
	if (get_option('thegem_auto_check_activation_after_update',0)!=$thegem_theme->get('Version')) {
		$theme_options = get_option('thegem_theme_options');

		if (!is_array($theme_options) || !isset($theme_options['purchase_code'])) {
			return;
		}

		delete_option('thegem_activation');
		$response_p = wp_remote_get(add_query_arg(array('code' => $theme_options['purchase_code'], 'info'=>thegem_get_activation_info(), 'site_url' => get_site_url(), 'type' => 'elementor'), esc_url('http://democontent.codex-themes.com/av_validate_code.php')), array('timeout' => 20));
		if(!is_wp_error($response_p)) {
			$rp_data = json_decode($response_p['body'], true);
			if(is_array($rp_data) && isset($rp_data['result']) && $rp_data['result'] && isset($rp_data['item_id']) && $rp_data['item_id'] === '16061685') {
				update_option('thegem_activation', 1);
			}
		}

		update_option('thegem_auto_check_activation_after_update',$thegem_theme->get('Version'));
	}
}

thegem_auto_check_activation_after_update();

function thegem_activation_notice() {
	if(empty( $_COOKIE['thegem_activation'] )) return ;
	if(get_option('thegem_activation')) return ;
	if(defined('ENVATO_HOSTED_SITE') && thegem_get_purchase()) return ;
?>
<style>
	.thegem_license-activation-notice {
		position: relative;
	}
</style>
<script type="text/javascript">
(function ( $ ) {
	var setCookie = function ( c_name, value, exdays ) {
		var exdate = new Date();
		exdate.setDate( exdate.getDate() + exdays );
		var c_value = encodeURIComponent( value ) + ((null === exdays) ? "" : "; expires=" + exdate.toUTCString());
		document.cookie = c_name + "=" + c_value;
	};
	$( document ).on( 'click.thegem-notice-dismiss', '.thegem-notice-dismiss', function ( e ) {
		e.preventDefault();
		var $el = $( this ).closest('#thegem_license-activation-notice' );
		$el.fadeTo( 100, 0, function () {
			$el.slideUp( 100, function () {
				$el.remove();
			} );
		} );
		setCookie( 'thegem_activation', '1', 30 );
	} );
})( window.jQuery );
</script>
<?php
	if(!defined('ENVATO_HOSTED_SITE')) {
		echo '<div class="updated thegem_license-activation-notice" id="thegem_license-activation-notice"><p>' . sprintf( wp_kses(__( 'Welcome to TheGem! Would you like to import our awesome demos and take advantage of our amazing features? Please <a href="%s">activate</a> your copy of TheGem.', 'thegem' ), array('a' => array('href' => array()))), esc_url(admin_url('admin.php?page=thegem-dashboard-welcome')) ) . '</p>' . '<button type="button" class="notice-dismiss thegem-notice-dismiss"><span class="screen-reader-text">' . __( 'Dismiss this notice.', 'default' ) . '</span></button></div>';
	} else {
		echo '<div class="updated thegem_license-activation-notice" id="thegem_license-activation-notice"><p>' . sprintf( wp_kses(__( 'Welcome to TheGem! Would you like to import our awesome demos and take advantage of our amazing features? led. Please install "Envato WordPress Toolkit" plugin and fill <a href="%s">Envato "User Account Information"</a>.', 'thegem' ), array('a' => array('href' => array()))), esc_url(admin_url('admin.php?page=envato-wordpress-toolkit')) ) . '</p>' . '<button type="button" class="notice-dismiss thegem-notice-dismiss"><span class="screen-reader-text">' . __( 'Dismiss this notice.', 'default' ) . '</span></button></div>';
	}
}
add_action('admin_notices', 'thegem_activation_notice');

function thegem_theme_options_get_page_settings($type) {
	$page_data = thegem_get_sanitize_options_page_data(get_option('thegem_options_page_settings_'.$type), $type);
	return array_map('stripslashes', $page_data);
}

function thegem_theme_options_set_page_settings($type, $data) {
	$page_data = thegem_get_sanitize_options_page_data($data, $type);
	update_option('thegem_options_page_settings_'.$type, $page_data);
}

function thegem_get_sanitize_options_page_data($data, $type = 'default') {
	$page_data = apply_filters('thegem_options_page_data_defaults', array(
		'title_show' => '1',
		'title_style' => '1',
		'title_template' => '',
		'title_xlarge' => '0',
		'title_use_page_settings' => '0',
		'title_background_type' => 'color',
		'title_background_image' => '',
		'title_background_image_repeat' => '',
		'title_background_position_x' => 'center',
		'title_background_position_y' => 'top',
		'title_background_size' => 'cover',
		'title_background_image_color' => '',
		'title_background_image_overlay' => '',
		'title_background_gradient_type' => 'linear',
		'title_background_gradient_angle' => '90',
		'title_background_gradient_position' => 'center center',
		'title_background_gradient_point1_color' => '#00BCD4BF',
		'title_background_gradient_point1_position' => '0',
		'title_background_gradient_point2_color' => '#354093BF',
		'title_background_gradient_point2_position' => '100',
		'title_background_parallax' => '',
		'title_background_color' => '#333144FF',
		'title_background_video_type' => '',
		'title_background_video' => '',
		'title_background_video_aspect_ratio' => '',
		'title_background_video_overlay_color' => '',
		'title_background_video_overlay_opacity' => '',
		'title_background_video_poster' => '',
		'title_menu_on_video' => '',
		'title_text_color' => '#FFFFFFFF',
		'title_excerpt_text_color' => '#FFFFFFFF',
		'title_title_width' => '',
		'title_excerpt_width' => '',
		'title_padding_top' => '80',
		'title_padding_top_tablet' => '80',
		'title_padding_top_mobile' => '80',
		'title_padding_bottom' => '80',
		'title_padding_bottom_tablet' => '80',
		'title_padding_bottom_mobile' => '80',
		'title_padding_left' => '0',
		'title_padding_left_tablet' => '0',
		'title_padding_left_mobile' => '0',
		'title_padding_right' => '0',
		'title_padding_right_tablet' => '0',
		'title_padding_right_mobile' => '0',
		'title_top_margin' => '0',
		'title_top_margin_tablet' => '0',
		'title_top_margin_mobile' => '0',
		'title_excerpt_top_margin' => '18',
		'title_excerpt_top_margin_tablet' => '18',
		'title_excerpt_top_margin_mobile' => '18',
		'title_breadcrumbs' => thegem_get_option('global_hide_breadcrumbs'),
		'title_alignment' => 'center',
		'breadcrumbs_default_color' => thegem_get_option('breadcrumbs_default_color'),
		'breadcrumbs_active_color' => thegem_get_option('breadcrumbs_active_color'),
		'breadcrumbs_hover_color' => thegem_get_option('breadcrumbs_hover_color'),
		'title_breadcrumbs_alignment' => 'center',
		'header_transparent' => '',
		'header_opacity' => '50',
		'header_menu_logo_light' => '',
		'header_hide_top_area' => !thegem_get_option('top_area_show'),
		'header_hide_top_area_tablet' => thegem_get_option('top_area_disable_tablet'),
		'header_hide_top_area_mobile' => thegem_get_option('top_area_disable_mobile'),
		'menu_show' => '1',
		'show_menu_socials' => '1',
		'show_menu_socials_mobile' => '1',
		'submenu_highlighter_color' => '',
		'header_top_area_transparent' => '0',
		'header_top_area_opacity' => '50',
		'content_padding_top' => '135',
		'content_padding_top_tablet' => '',
		'content_padding_top_mobile' => '',
		'content_padding_bottom' => '110',
		'content_padding_bottom_tablet' => '',
		'content_padding_bottom_mobile' => '',
		'footer_custom_show' => thegem_get_option('custom_footer_enable'),
		'footer_custom' => thegem_get_option('custom_footer'),
		'footer_hide_default' => !thegem_get_option('footer_active'),
		'footer_hide_widget_area' => thegem_get_option('footer_widget_area_hide'),
		'main_background_color' => thegem_get_option('main_background_color'),
		'main_background_gradient_angle' => '90',
		'main_background_gradient_point1_color' => '#E9ECDAFF',
		'main_background_gradient_point1_position' => '0',
		'main_background_gradient_point2_color' => '#D5F6FAFF',
		'main_background_gradient_point2_position' => '100',
		'main_background_gradient_position' => '',
		'main_background_gradient_type' => 'linear',
		'main_background_image' => '',
		'main_background_image_color' => '',
		'main_background_image_overlay' => '',
		'main_background_image_repeat' => '0',
		'main_background_pattern' => '',
		'main_background_position_x' => 'center',
		'main_background_position_y' => 'center',
		'main_background_size' => 'auto',
		'main_background_type' => 'color',

		'effects_disabled' => '0',
		'effects_parallax_footer' => '',
		'effects_hide_header' => '0',
		'effects_hide_footer' => !thegem_get_option('footer'),

		'enable_page_preloader' => thegem_get_option('preloader'),

		'sidebar_show' => '0',
		'sidebar_position' => 'left',
		'sidebar_sticky' => '0',
		'product_header_separator' => '1',
	), $type);

	if($type == 'post') {
		$page_data['show_featured_content'] = 1;
	}

	if($type == 'product') {
		$page_data['title_show'] = '0';
		$page_data['content_padding_top'] = '0';
		$page_data['sidebar_show'] = '0';
	}

	if($type == 'product_category') {
		$page_data['title_show'] = '0';
		$page_data['content_padding_top'] = '0';
		$page_data['sidebar_show'] = '0';
	}

	if(is_array($data)) {
		$page_data = array_merge($page_data, $data);
	}

	$page_data['title_xlarge'] = $page_data['title_xlarge'] ? 1 : 0;
	$page_data['title_show'] = $page_data['title_show'] ? 1 : 0;
	$page_data['title_style'] = thegem_check_array_value(array('1', '2'), $page_data['title_style'], '1');
	$page_data['title_template'] = intval($page_data['title_template']) >= 0 ? intval($page_data['title_template']) : 0;
	$page_data['title_use_page_settings'] = $page_data['title_use_page_settings'] ? 1 : 0;
	$page_data['title_background_type'] = thegem_check_array_value(array('color', 'image', 'video', 'gradient'), $page_data['title_background_type'], 'color');
	$page_data['title_background_image'] = esc_url($page_data['title_background_image']);
	$page_data['title_background_parallax'] = $page_data['title_background_parallax'] ? 1 : 0;
	$page_data['title_background_color'] = sanitize_text_field($page_data['title_background_color']);
	$page_data['title_background_image_color'] = sanitize_text_field($page_data['title_background_image_color']);
	$page_data['title_background_image_overlay'] = sanitize_text_field($page_data['title_background_image_overlay']);
	$page_data['title_background_image_repeat'] = $page_data['title_background_image_repeat'] ? 1 : 0;
	$page_data['title_background_size'] = thegem_check_array_value(array('auto', 'cover', 'contain'), $page_data['title_background_size'], 'cover');
	$page_data['title_background_position_x'] = thegem_check_array_value(array('center', 'left', 'right'), $page_data['title_background_position_x'], 'center');
	$page_data['title_background_position_y'] = thegem_check_array_value(array('center', 'top', 'bottom'), $page_data['title_background_position_y'], 'top');
	$page_data['title_background_gradient_type'] = thegem_check_array_value(array('linear', 'circular'), $page_data['title_background_gradient_type'], 'linear');
	$page_data['title_background_gradient_angle'] = intval($page_data['title_background_gradient_angle']) >= 0 ? intval($page_data['title_background_gradient_angle']) : 0;
	$page_data['title_background_gradient_point1_color'] = sanitize_text_field($page_data['title_background_gradient_point1_color']);
	$page_data['title_background_gradient_point2_color'] = sanitize_text_field($page_data['title_background_gradient_point2_color']);
	$page_data['title_background_gradient_point1_position'] = intval($page_data['title_background_gradient_point1_position']) >= 0 ? intval($page_data['title_background_gradient_point1_position']) : 0;
	$page_data['title_background_gradient_point2_position'] = intval($page_data['title_background_gradient_point2_position']) >= 0 ? intval($page_data['title_background_gradient_point2_position']) : 100;
	$page_data['title_background_video_type'] = thegem_check_array_value(array('', 'youtube', 'vimeo', 'self'), $page_data['title_background_video_type'], '');
	$page_data['title_background_video'] = sanitize_text_field($page_data['title_background_video']);
	$page_data['title_background_video_aspect_ratio'] = sanitize_text_field($page_data['title_background_video_aspect_ratio']);
	$page_data['title_background_video_overlay_color'] = sanitize_text_field($page_data['title_background_video_overlay_color']);
	$page_data['title_background_video_overlay_opacity'] = sanitize_text_field($page_data['title_background_video_overlay_opacity']);
	$page_data['title_background_video_poster'] = esc_url($page_data['title_background_video_poster']);
	$page_data['title_text_color'] = sanitize_text_field($page_data['title_text_color']);
	$page_data['title_excerpt_text_color'] = sanitize_text_field($page_data['title_excerpt_text_color']);
	$page_data['title_title_width'] = intval($page_data['title_title_width']) >= 0 && $page_data['title_title_width'] !== '' ? intval($page_data['title_title_width']) : '';
	$page_data['title_excerpt_width'] = intval($page_data['title_excerpt_width']) >= 0 && $page_data['title_excerpt_width'] !== '' ? intval($page_data['title_excerpt_width']) : '';
	$page_data['title_top_margin'] = $page_data['title_top_margin'] !== '' ? intval($page_data['title_top_margin']) : '';
	$page_data['title_top_margin_tablet'] = $page_data['title_top_margin_tablet'] !== '' ? intval($page_data['title_top_margin_tablet']) : '';
	$page_data['title_top_margin_mobile'] = $page_data['title_top_margin_mobile'] !== '' ? intval($page_data['title_top_margin_mobile']) : '';
	$page_data['title_excerpt_top_margin'] = $page_data['title_excerpt_top_margin'] !== '' ? intval($page_data['title_excerpt_top_margin']) : '';
	$page_data['title_excerpt_top_margin_tablet'] = $page_data['title_excerpt_top_margin_tablet'] !== '' ? intval($page_data['title_excerpt_top_margin_tablet']) : '';
	$page_data['title_excerpt_top_margin_mobile'] = $page_data['title_excerpt_top_margin_mobile'] !== '' ? intval($page_data['title_excerpt_top_margin_mobile']) : '';
	$page_data['title_breadcrumbs'] = $page_data['title_breadcrumbs'] ? 1 : 0;
	$page_data['title_padding_top'] = intval($page_data['title_padding_top']) >= 0 ? intval($page_data['title_padding_top']) : 0;
	$page_data['title_padding_top_tablet'] = intval($page_data['title_padding_top_tablet']) >= 0 ? intval($page_data['title_padding_top_tablet']) : 0;
	$page_data['title_padding_top_mobile'] = intval($page_data['title_padding_top_mobile']) >= 0 ? intval($page_data['title_padding_top_mobile']) : 0;
	$page_data['title_padding_bottom'] = intval($page_data['title_padding_bottom']) >= 0 ? intval($page_data['title_padding_bottom']) : 0;
	$page_data['title_padding_bottom_tablet'] = intval($page_data['title_padding_bottom_tablet']) >= 0 ? intval($page_data['title_padding_bottom_tablet']) : 0;
	$page_data['title_padding_bottom_mobile'] = intval($page_data['title_padding_bottom_mobile']) >= 0 ? intval($page_data['title_padding_bottom_mobile']) : 0;
	$page_data['title_padding_left'] = intval($page_data['title_padding_left']) >= 0 ? intval($page_data['title_padding_left']) : 0;
	$page_data['title_padding_left_tablet'] = intval($page_data['title_padding_left_tablet']) >= 0 ? intval($page_data['title_padding_left_tablet']) : 0;
	$page_data['title_padding_left_mobile'] = intval($page_data['title_padding_left_mobile']) >= 0 ? intval($page_data['title_padding_left_mobile']) : 0;
	$page_data['title_padding_right'] = intval($page_data['title_padding_right']) >= 0 ? intval($page_data['title_padding_right']) : 0;
	$page_data['title_padding_right_tablet'] = intval($page_data['title_padding_right_tablet']) >= 0 ? intval($page_data['title_padding_right_tablet']) : 0;
	$page_data['title_padding_right_mobile'] = intval($page_data['title_padding_right_mobile']) >= 0 ? intval($page_data['title_padding_right_mobile']) : 0;
	$page_data['title_alignment'] = thegem_check_array_value(array('', 'center', 'left', 'right'), $page_data['title_alignment'], '');
	$page_data['breadcrumbs_default_color'] = sanitize_text_field($page_data['breadcrumbs_default_color']);
	$page_data['breadcrumbs_active_color'] = sanitize_text_field($page_data['breadcrumbs_active_color']);
	$page_data['breadcrumbs_hover_color'] = sanitize_text_field($page_data['breadcrumbs_hover_color']);
	$page_data['title_breadcrumbs_alignment'] = thegem_check_array_value(array('center', 'left', 'right'), $page_data['title_breadcrumbs_alignment'], 'center');
	$page_data['header_transparent'] = $page_data['header_transparent'] ? 1 : 0;
	$page_data['header_opacity'] = intval($page_data['header_opacity']) >= 0 && intval($page_data['header_opacity']) <= 100 ? intval($page_data['header_opacity']) : 0;
	$page_data['header_top_area_transparent'] = $page_data['header_top_area_transparent'] ? 1 : 0;
	$page_data['header_top_area_opacity'] = intval($page_data['header_top_area_opacity']) >= 0 && intval($page_data['header_top_area_opacity']) <= 100 ? intval($page_data['header_top_area_opacity']) : 0;
	$page_data['header_menu_logo_light'] = $page_data['header_menu_logo_light'] ? 1 : 0;
	$page_data['header_hide_top_area'] = $page_data['header_hide_top_area'] ? 1 : 0;
	$page_data['header_hide_top_area_tablet'] = $page_data['header_hide_top_area_tablet'] ? 1 : 0;
	$page_data['header_hide_top_area_mobile'] = $page_data['header_hide_top_area_mobile'] ? 1 : 0;
	$page_data['menu_show'] = $page_data['menu_show'] ? 1 : 0;
	$page_data['content_padding_top'] = intval($page_data['content_padding_top']) >= 0 && $page_data['content_padding_top'] !== '' ? intval($page_data['content_padding_top']) : '';
	$page_data['content_padding_top_tablet'] = intval($page_data['content_padding_top_tablet']) >= 0 && $page_data['content_padding_top_tablet'] !== '' ? intval($page_data['content_padding_top_tablet']) : '';
	$page_data['content_padding_top_mobile'] = intval($page_data['content_padding_top_mobile']) >= 0 && $page_data['content_padding_top_mobile'] !== '' ? intval($page_data['content_padding_top_mobile']) : '';
	$page_data['content_padding_bottom'] = intval($page_data['content_padding_bottom']) >= 0 && $page_data['content_padding_bottom'] !== '' ? intval($page_data['content_padding_bottom']) : '';
	$page_data['content_padding_bottom_tablet'] = intval($page_data['content_padding_bottom_tablet']) >= 0 && $page_data['content_padding_bottom_tablet'] !== '' ? intval($page_data['content_padding_bottom_tablet']) : '';
	$page_data['content_padding_bottom_mobile'] = intval($page_data['content_padding_bottom_mobile']) >= 0 && $page_data['content_padding_bottom_mobile'] !== '' ? intval($page_data['content_padding_bottom_mobile']) : '';
	$page_data['footer_custom_show'] = $page_data['footer_custom_show'] ? 1 : 0;
	$page_data['footer_custom'] = intval($page_data['footer_custom']) >= 0 ? intval($page_data['footer_custom']) : 0;
	$page_data['footer_hide_default'] = $page_data['footer_hide_default'] ? 1 : 0;
	$page_data['footer_hide_widget_area'] = $page_data['footer_hide_widget_area'] ? 1 : 0;
	$page_data['main_background_type'] = thegem_check_array_value(array('color', 'image', 'pattern', 'gradient'), $page_data['main_background_type'], 'color');
	$page_data['main_background_image'] = esc_url($page_data['main_background_image']);
	$page_data['main_background_color'] = sanitize_text_field($page_data['main_background_color']);
	$page_data['main_background_image_color'] = sanitize_text_field($page_data['main_background_image_color']);
	$page_data['main_background_image_overlay'] = sanitize_text_field($page_data['main_background_image_overlay']);
	$page_data['main_background_image_repeat'] = $page_data['main_background_image_repeat'] ? 1 : 0;
	$page_data['main_background_size'] = thegem_check_array_value(array('auto', 'cover', 'contain'), $page_data['main_background_size'], 'auto');
	$page_data['main_background_position_x'] = thegem_check_array_value(array('center', 'left', 'right'), $page_data['main_background_position_x'], 'center');
	$page_data['main_background_position_y'] = thegem_check_array_value(array('center', 'top', 'bottom'), $page_data['main_background_position_y'], 'center');
	$page_data['main_background_gradient_type'] = thegem_check_array_value(array('linear', 'circular'), $page_data['main_background_gradient_type'], 'linear');
	$page_data['main_background_gradient_angle'] = intval($page_data['main_background_gradient_angle']) >= 0 ? intval($page_data['main_background_gradient_angle']) : 0;
	$page_data['main_background_gradient_point1_color'] = sanitize_text_field($page_data['main_background_gradient_point1_color']);
	$page_data['main_background_gradient_point2_color'] = sanitize_text_field($page_data['main_background_gradient_point2_color']);
	$page_data['main_background_gradient_point1_position'] = intval($page_data['main_background_gradient_point1_position']) >= 0 ? intval($page_data['main_background_gradient_point1_position']) : 0;
	$page_data['main_background_gradient_point2_position'] = intval($page_data['main_background_gradient_point2_position']) >= 0 ? intval($page_data['main_background_gradient_point2_position']) : 100;
	$page_data['main_background_pattern'] = esc_url($page_data['main_background_pattern']);
	$page_data['effects_disabled'] = $page_data['effects_disabled'] ? 1 : 0;
	$page_data['effects_parallax_footer'] = $page_data['effects_parallax_footer'] ? 1 : 0;
	$page_data['effects_hide_header'] = $page_data['effects_hide_header'] ? 1 : 0;
	$page_data['effects_hide_footer'] = $page_data['effects_hide_footer'] ? 1 : 0;
	$page_data['enable_page_preloader'] = $page_data['enable_page_preloader'] ? 1 : 0;
	$page_data['sidebar_show'] = $page_data['sidebar_show'] ? 1 : 0;
	$page_data['sidebar_position'] = thegem_check_array_value(array('left', 'right'), $page_data['sidebar_position'], 'left');
	$page_data['sidebar_sticky'] = $page_data['sidebar_sticky'] ? 1 : 0;
	$page_data['product_header_separator'] = $page_data['product_header_separator'] ? 1 : 0;

	if($type == 'post') {
		$page_data['show_featured_content'] = $page_data['show_featured_content'] ? 1 : 0;
	}

	return apply_filters('thegem_options_page_data', $page_data, $type);
}

function thegem_generate_empty_custom_css() {
	thegem_get_option(false, false, false, true);
	ob_start();
	thegem_custom_fonts();
	require get_template_directory() . '/inc/custom-css.php';
	if(file_exists(get_stylesheet_directory() . '/inc/custom-css.php') && get_stylesheet_directory() != get_template_directory()) {
		require get_stylesheet_directory() . '/inc/custom-css.php';
	}
	$custom_css = ob_get_clean();
	ob_start();
	require get_template_directory() . '/inc/style-editor-css.php';
	$editor_css = ob_get_clean();
	$action = array('action');
	$url = wp_nonce_url('admin.php?page=thegem-theme-options','thegem-theme-options');
	if(WP_Filesystem()) {
		global $wp_filesystem;
		$old_name = thegem_get_custom_css_filename();
		$new_name = thegem_generate_custom_css_filename();
		if(!$wp_filesystem->put_contents($wp_filesystem->find_folder(get_stylesheet_directory()) . 'css/'.$new_name.'.css', $custom_css) && get_option('thegem_custom_css_filename')) {
			update_option('thegem_generate_empty_custom_css_fail', 1);
		} else {
			$wp_filesystem->put_contents($wp_filesystem->find_folder(get_template_directory()) . 'css/style-editor.css', $editor_css);
			$custom_css_files = glob(get_template_directory().'/css/custom-*.css');
			foreach($custom_css_files as $file) {
				if(basename($file, '.css') != $new_name) {
					$wp_filesystem->delete($wp_filesystem->find_folder(get_stylesheet_directory()) . 'css/'.basename($file, '.css').'.css', $custom_css);
				}
			}
			thegem_save_custom_css_filename($new_name);
			delete_option('thegem_generate_empty_custom_css_fail');
		}
	} elseif(get_option('thegem_custom_css_filename')) {
		update_option('thegem_generate_empty_custom_css_fail', 1);
	}
}

function thegem_generate_empty_custom_css_notice() {
	if(get_option('thegem_generate_empty_custom_css_fail', 0)) {
?>
	<div class="error thegem-custom-css-regenerate-message" id="thegem-custom-css-generation-error">
		<p><?php printf(wp_kses(__('WARNING: custom.css file is missing in your TheGem installation. Custom.css is important for proper functioning of TheGem. <a href="'.admin_url('admin.php?page=thegem-theme-options').'#/extras/panel.extra_options:regenerateCss">Please regenerate it now.</a> All your settings will remain, this action will not affect your setup.', 'thegem'), array('a' => array('href' => array(), 'onclick' => array(''))))); ?></p>
	</div>
<?php
	$thegem_theme = wp_get_theme(wp_get_theme()->get('Template'));

		if (get_option('thegem_generate_empty_css_forced_redirect_done',0)!=$thegem_theme->get('Version')) {
			$regenerateUrl = admin_url('admin.php?page=thegem-theme-options').'#/extras/panel.extra_options:regenerateEmptyCss';
			wp_register_script( 'thegem_generate_empty_css_forced_redirect', '');
			wp_enqueue_script( 'thegem_generate_empty_css_forced_redirect' );
			wp_add_inline_script('thegem_generate_empty_css_forced_redirect','window.location.href="'.$regenerateUrl.'";');
		}
	/*
	$emptyCssUrl = admin_url('admin.php?page=thegem-theme-options2').'#/extras/panel.extra_options:regenerateEmptyCss';
		wp_add_inline_script();
	*/
	}
}
add_action('admin_notices', 'thegem_generate_empty_custom_css_notice');

add_filter( 'template_include', 'thegem_header_test_template', 99 );

function thegem_header_test_template( $template ) {
	if( !empty($_REQUEST['thegem_header_test'])) {
		$new_template = locate_template( array( 'header-test.php' ) );
		if ( '' != $new_template ) {
			return $new_template ;
		}
	}

	return $template;
}

add_filter( 'show_admin_bar', 'thegem_header_test_hide_admin_bar' );
function thegem_header_test_hide_admin_bar( $show_admin_bar ) {
	if( !is_admin() && !empty($_REQUEST['thegem_header_test'])) {
		$show_admin_bar = false;
	}

	return $show_admin_bar;
}

function thegem_get_preview_option( $options, $option, $default = false ) {
	return isset($options[$option]) ? $options[$option] : thegem_get_option($option, $default);
}

function thegem_preview_menu_html($options = array()) {
?>
<ul id="primary-menu" class="nav-menu styled no-responsive<?php echo(thegem_get_preview_option($options, 'mobile_menu_layout') == 'default' ? ' dl-menu' : ''); ?>">
	<li class="menu-item current-menu-ancestor current-menu-parent menu-item-has-children menu-item-parent megamenu-first-element menu-item-current">
		<a href="#">Some</a><span class="menu-item-parent-toggle"></span>
		<ul class="sub-menu styled dl-submenu">
			<li class="menu-item current-menu-ancestor current-menu-parent menu-item-has-children menu-item-parent megamenu-first-element menu-item-current">
				<a href="#">Level 2 Item #1</a><span class="menu-item-parent-toggle"></span>
				<ul class="sub-menu styled dl-submenu-disabled">
					<li class="dl-back"><a href="#">Back</a></li>
					<li class="menu-item megamenu-first-element"><a href="#">Level 3 Item #1</a></li>
					<li class="menu-item current-menu-item megamenu-first-element menu-item-active"><a href="#">Level 3 Item #2</a></li>
					<li class="menu-item megamenu-first-element"><a href="#">Level 3 Item #3</a></li>
				</ul>
			</li>
			<li class="menu-item megamenu-first-element"><a href="#">Level 2 Item #2</a></li>
			<li class="menu-item megamenu-first-element"><a href="#">Level 2 Item #3</a></li>
			<li class="menu-item megamenu-first-element"><a href="#">Level 2 Item #4</a></li>
		</ul>
	</li>
	<li class="menu-item megamenu-first-element"><a href="#">Dummy</a></li>
	<?php if(thegem_get_preview_option($options, 'logo_position') == 'menu_center' && thegem_get_preview_option($options, 'header_layout') == 'default') : ?>
		<li class="menu-item-logo"><?php thegem_preview_logo_html(thegem_get_preview_option($options, 'header_style')); ?></li>
	<?php endif; ?>
	<li class="menu-item megamenu-first-element"><a href="#">Menu</a></li>
	<li class="menu-item megamenu-first-element"><a href="#">Items</a></li>
	<?php if(thegem_get_preview_option($options, 'header_layout') == 'fullwidth_hamburger') : ?>
		<li class="menu-item menu-item-widgets">
			<div class="vertical-minisearch">
				<form role="search" id="searchform" class="sf" action="#" method="GET">
					<input id="searchform-input" class="sf-input" type="text" placeholder="<?php esc_html_e('Search...', 'thegem'); ?>" name="s">
					<span class="sf-submit-icon"></span>
					<input id="searchform-submit" class="sf-submit" type="submit" value="">
				</form>
			</div>
			<div class="menu-item-socials socials-colored">
				<div class="socials inline-inside">
					<a class="socials-item" href="#" target="_blank" title="Facebook"><i class="socials-item-icon facebook social-item-rounded"></i></a>
					<a class="socials-item" href="#" target="_blank" title="LinkedIn"><i class="socials-item-icon linkedin social-item-rounded"></i></a>
					<a class="socials-item" href="#" target="_blank" title="Twitter"><i class="socials-item-icon twitter social-item-rounded"></i></a>
					<a class="socials-item" href="#" target="_blank" title="Instagram"><i class="socials-item-icon instagram social-item-rounded"></i></a>
					<a class="socials-item" href="#" target="_blank" title="Pinterest"><i class="socials-item-icon pinterest social-item-rounded"></i></a>
					<a class="socials-item" href="#" target="_blank" title="YouTube"><i class="socials-item-icon youtube social-item-rounded"></i></a>
				</div>
			</div>
		</li>
	<?php endif; ?>
</ul>
<?php
}
function thegem_preview_logo_html($header_style = false) {
?>
<div class="site-logo" style="width:164px;">
	<a href="#" rel="home">
		<span class="logo"><img src="<?php echo esc_url(get_template_directory_uri().'/images/'.($header_style == 4 ? 'default-logo-light' : 'default-logo').'.svg'); ?>" alt="<?php echo esc_attr(get_bloginfo( 'name', 'display' )); ?>" style="width:164px;" class="default"/></span>
	</a>
</div>
<?php
}
function thegem_before_perspective_nav_menu_preview($options) {
	if (thegem_get_preview_option($options, 'mobile_menu_layout') == 'overlay') {
		echo '<div class="overlay-menu-wrapper"><div class="overlay-menu-table"><div class="overlay-menu-row"><div class="overlay-menu-cell">';
	}
	if (thegem_get_preview_option($options, 'mobile_menu_layout') == 'slide-horizontal') {
		echo '<div class="mobile-menu-slide-wrapper left"><button class="mobile-menu-slide-close"></button>';
	}
	if (thegem_get_preview_option($options, 'mobile_menu_layout') == 'slide-vertical') {
		echo '<div class="mobile-menu-slide-wrapper top"><button class="mobile-menu-slide-close"></button>';
	}
	echo '<button class="perspective-menu-close'.(thegem_get_preview_option($options, 'hamburger_menu_icon_size') ? ' toggle-size-small' : '').'"></button>';
}
function thegem_after_perspective_nav_menu_preview($options) {
	if (thegem_get_preview_option($options, 'mobile_menu_layout') == 'overlay') {
		echo '</div></div></div></div>';
	}
	if (thegem_get_preview_option($options, 'mobile_menu_layout') == 'slide-horizontal') {
		echo '</div>';
	}
	if (thegem_get_preview_option($options, 'mobile_menu_layout') == 'slide-vertical') {
		echo '</div>';
	}
}
function thegem_before_header_preview($options) {
	if (thegem_get_preview_option($options, 'header_layout') == 'overlay' || thegem_get_preview_option($options, 'mobile_menu_layout') == 'overlay') {
		echo '<div class="menu-overlay"></div>';
	}
}
function thegem_perspective_menu_buttons_preview($options) {
	echo '<div id="perspective-menu-buttons" class="primary-navigation">';
	$minicart_items = '';
	echo '<div class="hamburger-group'.(thegem_get_preview_option($options, 'hamburger_menu_icon_size') ? ' hamburger-size-small hamburger-size-small-original' : '').(thegem_get_preview_option($options, 'hamburger_menu_cart_position') ? ' hamburger-with-cart' : '').'">';
	echo '<button class="perspective-toggle'.(thegem_get_preview_option($options, 'hamburger_menu_icon_size') ? ' toggle-size-small toggle-size-small-original' : '').'">' . esc_html('Primary Menu', 'thegem') . '<span class="menu-line-1"></span><span class="menu-line-2"></span><span class="menu-line-3"></span></button>';
	if(thegem_get_preview_option($options, 'logo_position') == 'right' && $minicart_items) {
		echo $minicart_items;
	}
	echo '<button class="menu-toggle dl-trigger">' . esc_html('Primary Menu', 'thegem') . '<span class="menu-line-1"></span><span class="menu-line-2"></span><span class="menu-line-3"></span></button>';
	echo '</div>';
	echo '</div>';
}
function thegem_before_nav_menu_preview($options) {
	echo '<button class="menu-toggle dl-trigger">' . esc_html('Primary Menu', 'thegem') . '<span class="menu-line-1"></span><span class="menu-line-2"></span><span class="menu-line-3"></span></button>';

	if (thegem_get_preview_option($options, 'header_layout') == 'fullwidth_hamburger' || thegem_get_preview_option($options, 'header_layout') == 'overlay') {
		$minicart_items = '';
		echo '<div class="hamburger-group'.(thegem_get_preview_option($options, 'hamburger_menu_icon_size') ? ' hamburger-size-small hamburger-size-small-original' : '').(thegem_get_preview_option($options, 'hamburger_menu_cart_position') ? ' hamburger-with-cart' : '').'">';
		if (thegem_get_preview_option($options, 'header_layout') == 'fullwidth_hamburger') {
			echo '<button class="hamburger-toggle">' . esc_html('Primary Menu', 'thegem') . '<span class="menu-line-1"></span><span class="menu-line-2"></span><span class="menu-line-3"></span></button>';
		}
		if (thegem_get_preview_option($options, 'header_layout') == 'overlay') {
			echo '<button class="overlay-toggle '.(thegem_get_preview_option($options, 'hamburger_menu_icon_size') ? ' toggle-size-small toggle-size-small-original' : '').'">' . esc_html('Primary Menu', 'thegem') . '<span class="menu-line-1"></span><span class="menu-line-2"></span><span class="menu-line-3"></span></button>';
		}
		if($minicart_items && thegem_get_preview_option($options, 'logo_position') == 'right') {
			echo $minicart_items;
		}
		echo '</div>';
	}
	if (thegem_get_preview_option($options, 'header_layout') == 'overlay' || thegem_get_preview_option($options, 'mobile_menu_layout') == 'overlay') {
		echo '<div class="overlay-menu-wrapper"><div class="overlay-menu-table"><div class="overlay-menu-row"><div class="overlay-menu-cell">';
	}
	if (thegem_get_preview_option($options, 'header_layout') == 'perspective') {
		echo '<button class="perspective-toggle'.(thegem_get_preview_option($options, 'hamburger_menu_icon_size') ? ' toggle-size-small toggle-size-small-original' : '').'">' . esc_html('Primary Menu', 'thegem') . '<span class="menu-line-1"></span><span class="menu-line-2"></span><span class="menu-line-3"></span></button>';
	}
	if (thegem_get_preview_option($options, 'mobile_menu_layout') == 'slide-horizontal') {
		echo '<div class="mobile-menu-slide-wrapper left"><button class="mobile-menu-slide-close"></button>';
	}
	if (thegem_get_preview_option($options, 'mobile_menu_layout') == 'slide-vertical') {
		echo '<div class="mobile-menu-slide-wrapper top"><button class="mobile-menu-slide-close"></button>';
	}
}
function thegem_after_nav_menu_preview($options) {
	if (thegem_get_preview_option($options, 'header_layout') == 'overlay' || thegem_get_preview_option($options, 'mobile_menu_layout') == 'overlay') {
		echo '</div></div></div></div>';
	}
	if (thegem_get_preview_option($options, 'mobile_menu_layout') == 'slide-horizontal') {
		echo '</div>';
	}
	if (thegem_get_preview_option($options, 'mobile_menu_layout') == 'slide-vertical') {
		echo '</div>';
	}
}

function thegem_header_preview_scripts() {
	if(!is_admin() && !empty($_REQUEST['thegem_header_test'])) {
		wp_dequeue_style('thegem-custom');
		wp_enqueue_style('thegem-custom', get_template_directory_uri() . '/css/header-custom.css', array('thegem-style'));
		wp_register_style('thegem-preview-custom-menu-1', get_template_directory_uri() . '/css/header-preview/menu-colors-1.css');
		wp_register_style('thegem-preview-custom-menu-2', get_template_directory_uri() . '/css/header-preview/menu-colors-2.css');
		wp_register_style('thegem-preview-custom-menu-3', get_template_directory_uri() . '/css/header-preview/menu-colors-3.css');
		wp_register_style('thegem-preview-custom-menu-4', get_template_directory_uri() . '/css/header-preview/menu-colors-4.css');
		wp_register_style('thegem-preview-custom-menu-overlay', get_template_directory_uri() . '/css/header-preview/menu-colors-overlay.css');
		wp_register_style('thegem-preview-custom-top-area-1', get_template_directory_uri() . '/css/header-preview/top-area-colors-1.css');
		wp_register_style('thegem-preview-custom-top-area-2', get_template_directory_uri() . '/css/header-preview/top-area-colors-2.css');
		wp_register_style('thegem-preview-custom-top-area-3', get_template_directory_uri() . '/css/header-preview/top-area-colors-3.css');
		wp_register_style('thegem-layout-perspective', get_template_directory_uri() . '/css/thegem-layout-perspective.css');
		wp_register_style('thegem-preview-mobile-default-dark', get_template_directory_uri() . '/css/header-preview/mobile-default-styles-dark.css');
		wp_register_style('thegem-preview-mobile-slide-light', get_template_directory_uri() . '/css/header-preview/mobile-slide-styles-light.css');
		wp_register_style('thegem-preview-mobile-slide-dark', get_template_directory_uri() . '/css/header-preview/mobile-slide-styles-dark.css');
		wp_register_style('thegem-preview-mobile-overlay-light', get_template_directory_uri() . '/css/header-preview/mobile-overlay-styles-light.css');
		wp_register_style('thegem-preview-mobile-overlay-dark', get_template_directory_uri() . '/css/header-preview/mobile-overlay-styles-dark.css');
		wp_enqueue_script('jquery-dlmenu', get_template_directory_uri() . '/js/jquery.dlmenu.js', array('jquery'), false, true);
	}
}
add_action('wp_enqueue_scripts', 'thegem_header_preview_scripts', 4);

function thegem_header_preview_hide_vc_styles($css) {
	if(!is_admin() && !empty($_REQUEST['thegem_header_test'])) {
		$css = '';
	}
	return $css;
}
add_action('vc_shortcodes_custom_css', 'thegem_header_preview_hide_vc_styles');
add_action('vc_post_custom_css', 'thegem_header_preview_hide_vc_styles');


function thegem_apply_options_page_settings($type, $options, $offset = null, $workEndTime = null) {
	if (!$workEndTime) {
		$workEndTime = time() + 20;
	}

	if (!$offset) {
		$offset = 0;
	}

	$workChunkSize = 50;

	if(in_array($type, array('page', 'post', 'thegem_pf_item', 'product'))) {
		$posts = get_posts(array(
			'numberposts' => $workChunkSize,
			'post_type' => $type,
			'orderby' => 'ID',
			'offset' => $offset
		));

		if (empty($posts)) {
			return false;
		}

		foreach($posts as $post) {
			$meta = thegem_get_sanitize_admin_page_data($post->ID);
			$meta = thegem_update_page_data_from_options($meta, $options);
			update_post_meta($post->ID, 'thegem_page_data', $meta);
			if($type == 'post') {
				$meta = thegem_get_sanitize_admin_post_elements_data($post->ID);
				$meta = thegem_update_post_page_elements_data_from_options($meta);
				update_post_meta($post->ID, 'thegem_post_page_elements_data', $meta);
				$meta = thegem_get_sanitize_admin_post_data($post->ID);
				$meta['show_featured_content'] = 'default';
				update_post_meta($post->ID, 'thegem_post_general_item_data', $meta);
			}
			if($type == 'thegem_pf_item') {
				$meta = thegem_get_sanitize_pf_item_elements_data($post->ID);
				$meta = thegem_update_pf_item_page_elements_data_from_options($meta);
				update_post_meta($post->ID, 'thegem_pf_item_page_elements_data', $meta);
			}

			$offset++;
			if (time()>=$workEndTime) {
				return $offset;
			}
		}

		unset($posts);
	}

	if($type == 'product_layout') {
		$posts = get_posts(array(
			'numberposts' => $workChunkSize,
			'post_type' => 'product',
			'orderby' => 'ID',
			'offset' => $offset
		));

		if (empty($posts)) {
			return false;
		}

		foreach($posts as $post) {
			$meta = thegem_get_sanitize_product_page_data($post->ID);
			$meta = thegem_update_product_page_elements_data_from_options($meta);
			update_post_meta($post->ID, 'thegem_product_page_data', $meta);

			$offset++;
			if (time()>=$workEndTime) {
				return $offset;
			}
		}

		unset($posts);
	}

	if($type == 'cats') {
		$terms = get_terms(array(
			'taxonomy' => array('post_tag', 'category'),
			'hide_empty' => false,
			'orderby' => 'id',
			'offset' => $offset,
			'number' => $workChunkSize
		));

		if (empty($terms)) {
			return false;
		}

		foreach($terms as $term) {
			$meta = thegem_get_sanitize_admin_page_data($term->term_id, array(), 'term');
			$meta = thegem_update_page_data_from_options($meta, $options);
			update_term_meta($term->term_id, 'thegem_page_data', $meta);

			$offset++;
			if (time()>=$workEndTime) {
				return $offset;
			}
		}

		unset($terms);
	}

	if($type == 'product_cats') {
		$terms = get_terms(array(
			'taxonomy' => array('product_cat', 'product_tag'),
			'hide_empty' => false,
			'orderby' => 'id',
			'offset' => $offset,
			'number' => $workChunkSize
		));

		if (empty($terms)) {
			return false;
		}

		foreach($terms as $term) {
			$meta = thegem_get_sanitize_admin_page_data($term->term_id, array(), 'term');
			$meta = thegem_update_page_data_from_options($meta, $options);
			update_term_meta($term->term_id, 'thegem_page_data', $meta);

			$offset++;
			if (time()>=$workEndTime) {
				return $offset;
			}
		}

		unset($terms);
	}

	return thegem_apply_options_page_settings($type, $options, $offset, $workEndTime);
}

function thegem_update_page_data_from_options($data, $options) {
	foreach($options as $option => $value) {
		switch ($option) {
			case 'title_show':
				$data[$option] = 'default';
				break;
			case 'header_hide_top_area':
				$data[$option] = 'default';
				break;
			case 'menu_show':
				$data[$option] = 'default';
				break;
			case 'footer_custom_show':
				$data[$option] = 'default';
				break;
			case 'footer_hide_default':
				$data[$option] = 'default';
				break;
			case 'footer_hide_widget_area':
				$data[$option] = 'default';
				break;
			case 'effects_hide_header':
				$data[$option] = 'default';
				break;
			case 'effects_hide_footer':
				$data[$option] = 'default';
				break;
			case 'sidebar_show':
				$data[$option] = 'default';
				break;
			case 'enable_page_preloader':
				$data[$option] = 'default';
				break;
			default:
				$data[$option] = $value;
		}
	}
	return $data;
}

function thegem_update_post_page_elements_data_from_options($data) {
	$data = array(
		'post_elements' => 'default',
		'show_author' => thegem_get_option('show_author'),
		'blog_hide_author' => thegem_get_option('blog_hide_author'),
		'blog_hide_date' => thegem_get_option('blog_hide_date'),
		'blog_hide_date_in_blog_cat' => thegem_get_option('blog_hide_date_in_blog_cat'),
		'blog_hide_categories' => thegem_get_option('blog_hide_categories'),
		'blog_hide_tags' => thegem_get_option('blog_hide_tags'),
		'blog_hide_comments' => thegem_get_option('blog_hide_comments'),
		'blog_hide_likes' => thegem_get_option('blog_hide_likes'),
		'blog_hide_navigation' => thegem_get_option('blog_hide_navigation'),
		'blog_hide_socials' => thegem_get_option('blog_hide_socials'),
		'blog_hide_realted' => thegem_get_option('blog_hide_realted'),
	);
	return $data;
}

function thegem_update_pf_item_page_elements_data_from_options($data) {
	$data = array(
		'portfolio_page_elements' => 'default',
		'portfolio_hide_top_navigation' => thegem_get_option('portfolio_hide_top_navigation'),
		'portfolio_hide_date' => thegem_get_option('portfolio_hide_date'),
		'portfolio_hide_sets' => thegem_get_option('portfolio_hide_sets'),
		'portfolio_hide_likes' => thegem_get_option('portfolio_hide_likes'),
		'portfolio_hide_socials' => thegem_get_option('portfolio_hide_socials'),
		'portfolio_hide_bottom_navigation' => thegem_get_option('portfolio_hide_bottom_navigation'),
	);
	return $data;
}

function thegem_update_product_page_elements_data_from_options($data) {
	$data = array(
		'product_layout_settings' => 'default',
	);
	return $data;
}
