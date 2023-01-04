<?php

class TheGemGdprAdmin {

	CONST PAGE_URL = 'thegem-gdpr-settings';
	CONST SUBMENU_PAGE_PARENT_SLUG = 'thegem-dashboard-welcome';

	public $type;
	public $page_options;
	public $options;

	public function init() {
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action('admin_menu', array($this, 'add_menu'), 40);
		add_action('admin_init', array($this, 'register_settings'));
		add_action('wp_ajax_thegem_gdpr_get_font_data', array($this, 'get_font_data'));
		add_action('update_option_thegem_additionals_fonts', array($this, 'generate_custom_css'));
	}

	public function enqueue_scripts() {
		wp_enqueue_style(TheGemGdpr::PLUGIN_ID, plugin_dir_url(THEGEM_GDPR_PLUGIN_ROOT_FILE).'assets/css/admin.css', array('wp-color-picker'), TheGemGdpr::PLUGIN_VERSION);
		wp_enqueue_script(TheGemGdpr::PLUGIN_ID, plugin_dir_url(THEGEM_GDPR_PLUGIN_ROOT_FILE).'assets/js/admin.js', array('jquery','wp-color-picker','jquery-ui-sortable'), TheGemGdpr::PLUGIN_VERSION, true);
		wp_localize_script(TheGemGdpr::PLUGIN_ID, 'thegem_gdpr_admin_options', $this->get_options_object_js());
	}

	public function add_menu() {
		add_submenu_page(static::SUBMENU_PAGE_PARENT_SLUG, TheGemGdpr::PLUGIN_NAME, __('Privacy & GDPR', 'thegem'), 'manage_options', static::PAGE_URL, array($this, 'generate_page'));
	}

	public function register_settings() {
		$settings = array(
			TheGemGdpr::TYPE_CONSENT_BAR=>array($this,'sanitize_consent_bar_data'),
			TheGemGdpr::TYPE_INTEGRATIONS=>array($this, 'sanitize_integrations_data'),
			TheGemGdpr::TYPE_SETTINGS=>array($this, 'sanitize_settings_data')
		);

		foreach ($settings as $setting=>$callback) {
			register_setting(TheGemGdpr::OPTION_GROUP.'_'.$setting, TheGemGdpr::OPTION_GROUP.'_'.$setting, array('sanitize_callback'=>$callback));
		}

		add_action('update_option_'.TheGemGdpr::OPTION_GROUP.'_'.TheGemGdpr::TYPE_INTEGRATIONS, array((new TheGemGdprCF7), 'integration'));
		add_action('update_option_'.TheGemGdpr::OPTION_GROUP.'_'.TheGemGdpr::TYPE_SETTINGS, array($this, 'update_settings_options'));
		add_action('update_option_'.TheGemGdpr::OPTION_GROUP.'_'.TheGemGdpr::TYPE_CONSENT_BAR, array($this, 'update_consent_bar_options'));
	}

	public function sanitize_consent_bar_data($data) {
		if (!is_array($data)) {
			return sanitize_text_field($data);
		}

		$consent_bar = array(
			'active'=>!empty($data['active']) ? boolval($data['active']) : 0,
			'text'=>wp_kses(wp_unslash($data['text']), TheGemGdpr::get_allowed_html_tags()),
			'privacy_preferences_link_text' => sanitize_text_field($data['privacy_preferences_link_text']),
			'i_agree_button_text' => sanitize_text_field($data['i_agree_button_text']),
			'position' => sanitize_text_field($data['position']),
			'use_custom_styles' => !empty($data['use_custom_styles']) ? boolval($data['use_custom_styles']) : 0,
		);

		$data_consent_bar_custom_styles = $data['custom_styles'];
		$consent_bar_custom_styles = array(
			'background_color' => sanitize_text_field($data_consent_bar_custom_styles['background_color']),
			'background_opacity' => sanitize_text_field($data_consent_bar_custom_styles['background_opacity']),
			'text_font' => array(
				'font_family' => sanitize_text_field($data_consent_bar_custom_styles['text_font']['font_family']),
				'font_style' => sanitize_text_field($data_consent_bar_custom_styles['text_font']['font_style']),
				'font_sets' => sanitize_text_field($data_consent_bar_custom_styles['text_font']['font_sets']),
				'font_size' => sanitize_text_field($data_consent_bar_custom_styles['text_font']['font_size']),
				'line_height' => sanitize_text_field($data_consent_bar_custom_styles['text_font']['line_height'])
			),
			'text_color' => sanitize_text_field($data_consent_bar_custom_styles['text_color']),
			'link_font' => array(
				'font_family' => sanitize_text_field($data_consent_bar_custom_styles['link_font']['font_family']),
				'font_style' => sanitize_text_field($data_consent_bar_custom_styles['link_font']['font_style']),
				'font_sets' => sanitize_text_field($data_consent_bar_custom_styles['link_font']['font_sets']),
				'font_size' => sanitize_text_field($data_consent_bar_custom_styles['link_font']['font_size']),
				'line_height' => sanitize_text_field($data_consent_bar_custom_styles['link_font']['line_height'])
			),
			'link_color' => sanitize_text_field($data_consent_bar_custom_styles['link_color']),
			'link_hover_color' => sanitize_text_field($data_consent_bar_custom_styles['link_hover_color']),
			'button_text_color' => sanitize_text_field($data_consent_bar_custom_styles['button_text_color']),
			'button_text_hover_color' => sanitize_text_field($data_consent_bar_custom_styles['button_text_hover_color']),
			'button_background_color' => sanitize_text_field($data_consent_bar_custom_styles['button_background_color']),
			'button_background_hover_color' => sanitize_text_field($data_consent_bar_custom_styles['button_background_hover_color']),
			'button_border_color' => sanitize_text_field($data_consent_bar_custom_styles['button_border_color']),
			'button_border_hover_color' => sanitize_text_field($data_consent_bar_custom_styles['button_border_hover_color']),
		);
		$consent_bar['custom_styles'] = $consent_bar_custom_styles;

		if ($consent_bar['active'] && empty($this->options[TheGemGdpr::TYPE_SETTINGS])) {
			$settings = $this->get_default_value_for_settings();
			update_option(TheGemGdpr::OPTION_GROUP.'_'.TheGemGdpr::TYPE_SETTINGS, $this->sanitize_settings_data($settings));
		}

		return $consent_bar;
	}

	public function sanitize_integrations_data($data) {
		if (!is_array($data)) {
			return sanitize_text_field($data);
		}

		$integrations = array();
		$plugins = TheGemGdprIntegration::get_supported_plugins();

		if (!empty($plugins)) {
			foreach ($plugins as $plugin) {
				if ($plugin['is_supported']) {
					$integrations[$plugin['id']]['enabled'] = !empty($data[$plugin['id']]['enabled']) ? boolval($data[$plugin['id']]['enabled']) : 0;

					switch ($plugin['id']) {
						case TheGemGdprCF7::ID:
							$form_ids = TheGemGdprCF7::get_form_ids();
							if (!empty($form_ids)) {
								foreach ($form_ids as $form_id) {
									$data_form = $data[$plugin['id']]['forms'][$form_id];
									$integrations[$plugin['id']]['forms'][$form_id]['state'] = !empty($data_form['state']) ? boolval($data_form['state']) : 0;
									$integrations[$plugin['id']]['forms'][$form_id]['checkbox_text'] = wp_kses(wp_unslash($data_form['checkbox_text']), TheGemGdpr::get_allowed_html_tags());
									$integrations[$plugin['id']]['forms'][$form_id]['error_message'] = sanitize_text_field($data_form['error_message']);
								}
							}
							break;
					}

				}
			}
		}

		$integrations[TheGemGdprWP::ID] = array(
			'enabled' => !empty($data[TheGemGdprWP::ID]['enabled']) ? boolval($data[TheGemGdprWP::ID]['enabled']) : 0,
			'checkbox_text' => wp_kses(wp_unslash($data[TheGemGdprWP::ID]['checkbox_text']), TheGemGdpr::get_allowed_html_tags()),
			'error_message' => sanitize_text_field($data[TheGemGdprWP::ID]['error_message']),
		);

		return $integrations;
	}


	public function sanitize_settings_data($data) {
		if (!is_array($data)) {
			return sanitize_text_field($data);
		}

		$settings = array (
			'privacy_policy_page' => intval($data['privacy_policy_page']),
			'privacy_policy_link_text' => sanitize_text_field($data['privacy_policy_link_text']),
			'cookies_policy_page' => intval($data['cookies_policy_page']),
			'cookies_policy_link_text' => sanitize_text_field($data['cookies_policy_link_text']),
			'modal_title' => sanitize_text_field($data['modal_title']),
			'excerpt_text' => sanitize_textarea_field($data['excerpt_text']),
			'notice_text' => sanitize_textarea_field($data['notice_text']),
			'save_preferences_button_text' => sanitize_text_field($data['save_preferences_button_text']),
			'use_overlay_custom_styles' => !empty($data['use_overlay_custom_styles']) ? boolval($data['use_overlay_custom_styles']) : 0,
		);

		$data_overlay_styles = $data['overlay_styles'];
		$overlay_styles = array (
			'background_color' => sanitize_text_field($data_overlay_styles['background_color']),
			'background_opacity' => sanitize_text_field($data_overlay_styles['background_opacity']),
			'overlay_background_color' => sanitize_text_field($data_overlay_styles['overlay_background_color']),
			'bottom_bar_background_color' => sanitize_text_field($data_overlay_styles['bottom_bar_background_color']),
			'switch_disabled_background_color' => sanitize_text_field($data_overlay_styles['switch_disabled_background_color']),
			'switch_enabled_background_color' => sanitize_text_field($data_overlay_styles['switch_enabled_background_color']),
			'title_font' => array(
				'font_family' => sanitize_text_field($data_overlay_styles['title_font']['font_family']),
				'font_style' => sanitize_text_field($data_overlay_styles['title_font']['font_style']),
				'font_sets' => sanitize_text_field($data_overlay_styles['title_font']['font_sets']),
				'font_size' => sanitize_text_field($data_overlay_styles['title_font']['font_size']),
				'line_height' => sanitize_text_field($data_overlay_styles['title_font']['line_height'])
			),
			'title_color' => sanitize_text_field($data_overlay_styles['title_color']),
			'title_icon_color' => sanitize_text_field($data_overlay_styles['title_icon_color']),
			'text_font' => array(
				'font_family' => sanitize_text_field($data_overlay_styles['text_font']['font_family']),
				'font_style' => sanitize_text_field($data_overlay_styles['text_font']['font_style']),
				'font_sets' => sanitize_text_field($data_overlay_styles['text_font']['font_sets']),
				'font_size' => sanitize_text_field($data_overlay_styles['text_font']['font_size']),
				'line_height' => sanitize_text_field($data_overlay_styles['text_font']['line_height'])
			),
			'text_color' => sanitize_text_field($data_overlay_styles['text_color']),
			'separator_color' => sanitize_text_field($data_overlay_styles['separator_color']),
			'button_text_color' => sanitize_text_field($data_overlay_styles['button_text_color']),
			'button_text_hover_color' => sanitize_text_field($data_overlay_styles['button_text_hover_color']),
			'button_background_color' => sanitize_text_field($data_overlay_styles['button_background_color']),
			'button_background_hover_color' => sanitize_text_field($data_overlay_styles['button_background_hover_color']),
			'button_border_color' => sanitize_text_field($data_overlay_styles['button_border_color']),
			'button_border_hover_color' => sanitize_text_field($data_overlay_styles['button_border_hover_color']),
			'link_color' => sanitize_text_field($data_overlay_styles['link_color']),
			'link_hover_color' => sanitize_text_field($data_overlay_styles['link_hover_color']),
		);

		$settings['overlay_styles'] = $overlay_styles;

		$consents = array();
		foreach ($data['consents'] as $type=>$consent_item) {
			$consent['required'] = !empty($consent_item['required']) ? boolval($consent_item['required']) : 0;
			if (!in_array($type, $this->get_consents_with_field_required())) {
				$consent['required'] = 1;
			}

			$consent['description'] = wp_kses(wp_unslash($consent_item['description']), TheGemGdpr::get_allowed_html_tags());
			$consent_type_list = TheGemGdpr::get_consent_type_list();
			$consent['title']=!empty($consent_item['title']) ? sanitize_text_field($consent_item['title']) : $consent_type_list[$type];

			if (in_array($type, $this->get_consents_with_field_poster())) {
				$consent['poster']=sanitize_text_field($consent_item['poster']);
			}

			if (in_array($type, $this->get_consents_with_field_state())) {
				$consent['state']=!empty($consent_item['state']) ? boolval($consent_item['state']) : 0;
			}

			$consents[$type]=$consent;
		}

		$settings['consents'] = $consents;

		return $settings;
	}

	public function generate_page() {
		$type = (isset($_REQUEST['type'])) ? esc_html($_REQUEST['type']) : false;
		include plugin_dir_path(__DIR__).'views/admin/index.php';
	}

	public function get_options_object_js() {
		return array(
			'ajax_url' => esc_url(admin_url('admin-ajax.php')),
			'security' => wp_create_nonce('thegem_gdpr_ajax_security'),
			'consentsFormData' => array(
				'posterField'=>$this->get_consents_with_field_poster(),
				'stateField'=>$this->get_consents_with_field_state(),
				'requiredField'=>$this->get_consents_with_field_required(),
				'titleField'=>$this->get_consents_with_field_title(),
				'consentGoogleFonts'=>TheGemGdpr::CONSENT_NAME_GOOGLE_FONTS,
				'defaultDescriptions'=>static::get_consents_description_default_value()
			)
		);
	}


	public function update_consent_bar_options() {

	}

	public static function get_consents_description_default_value() {
		return array(
			TheGemGdpr::CONSENT_NAME_PRIVACY_POLICY=>__('You have read and agreed to our privacy policy', 'thegem'),
			TheGemGdpr::CONSENT_NAME_YOUTUBE=>__('This website uses YouTube service to provide video content streaming.', 'thegem'),
			TheGemGdpr::CONSENT_NAME_VIMEO=>__('This website includes videos, provided by Vimeo - a video content streaming service.', 'thegem'),
			TheGemGdpr::CONSENT_NAME_GOOGLE_FONTS=>__('This site uses so-called web fonts provided by Google for the uniform font representation.', 'thegem'),
			TheGemGdpr::CONSENT_NAME_GOOGLE_MAPS=>__('This website uses Google Maps service, allowing to display interactive maps.', 'thegem'),
			TheGemGdpr::CONSENT_NAME_TRACKING=>__('This website uses functions of the web analytics service Google Analytics.', 'thegem')
		);
	}

	public function get_field_name($field) {
		$prefix = TheGemGdpr::OPTION_GROUP.'_'.$this->get_type();

		if (!is_array($field)) {
			$name = $prefix.'['.$field.']';
		} else {
			$name = $prefix;
			foreach ($field as $item) {
				$name.='['.$item.']';
			}
		}

		return $name;
	}

	public function get_field_id($field) {
		$prefix = TheGemGdpr::OPTION_GROUP.'_'.$this->get_type();

		if (!is_array($field)) {
			$id = $prefix.'_'.$field;
		} else {
			$id = $prefix;
			foreach ($field as $item) {
				$id.='_'.$item;
			}
		}

		return str_replace('_', '-', $id);
	}

	public function get_field_value($field, $default_value = null) {
		$options = $this->get_page_options();

		if (!$options) {
			return $default_value;
		}

		if (!is_array($field)) {
			$value = $options[$field];
		} else {
			$value = array();
			foreach ($field as $item) {
				if (empty($value)) {
					if (empty($options[$item])) {
						return $default_value;
					}
					$value = $options[$item];
				} else {
					if (!empty($value[$item])) {
						$value = $value[$item];
					} else {
						$value = null;
						break;
					}
				}
			}
		}

		return $value;
	}

	public function get_plugin_url($type=null) {
		$args = array(
			'page' => static::PAGE_URL
		);

		if (!empty($type)) {
			$args['type'] = esc_html($type);
		}

	$parent = static::SUBMENU_PAGE_PARENT_SLUG;
	if ( false === strpos( $parent, '.php' ) ) {
		$parent = 'admin.php';
	}

		$url = add_query_arg($args, admin_url($parent));
		return $url;
	}

	public function set_type($type) {
		$this->type = $type;
	}

	public function get_type() {
		return $this->type;
	}

	public function get_page_options() {
		return $this->page_options;
	}

	public function init_page($type) {
		$this->set_type($type);
		$this->page_options = $this->options[$type];
	}

	public function get_supported_plugin_options($plugin_id) {
		$output = '';
		switch ($plugin_id) {
			case TheGemGdprCF7::ID:
				$form_ids = TheGemGdprCF7::get_form_ids();
				if (!empty($form_ids)) {
					$output .= '<div class="thegem-gdpr-form-box">';
					foreach ($form_ids as $form_id) {
						$output .= '<div class="thegem-gdpr-cf7-item-box">';
						$output .= '<div class="thegem-gdpr-cf7-item-title">'.sprintf(__('Form: %s', 'thegem'), get_the_title($form_id)).'</div>';

						$field = array(TheGemGdprCF7::ID, 'forms', $form_id, 'state');						
						$output .= '<div class="thegem-gdpr-field-box">';
						$output .= '<label for="'.esc_attr($this->get_field_id($field)).'">'.__('Activate for this form', 'thegem').':</label>';
						$output .= '<div class="field"><input name="'.esc_attr($this->get_field_name($field)).'" id="'.esc_attr($this->get_field_id($field)).'" type="checkbox" '.(esc_attr($this->get_field_value($field)) ? 'checked' : '').' ></div>';
						$output .= '</div>';

						$field = array(TheGemGdprCF7::ID, 'forms', $form_id, 'checkbox_text');
						$default_value = __('By using this form you agree with the storage and handling of your data by this website.', 'thegem');
						$output .= '<div class="thegem-gdpr-field-box">';
						$output .= '<label for="'.esc_attr($this->get_field_id($field)).'">'.__('Checkbox text', 'thegem').'</label>';
						$output .= '<div class="field"><textarea name="'.esc_attr($this->get_field_name($field)).'" id="'.esc_attr($this->get_field_id($field)).'" rows="3">'.esc_attr($this->get_field_value($field, $default_value)).'</textarea></div>';
						$output .= '</div>';
						$output .= '<div class="thegem-gdpr-allowed-tag">'.__('You can use', 'thegem').': '.TheGemGdpr::get_allowed_html_tags_output().'</div>';

						$field = array(TheGemGdprCF7::ID, 'forms', $form_id, 'error_message');
						$default_value = __('Please accept the privacy checkbox.', 'thegem');
						$output .= '<div class="thegem-gdpr-field-box">';
						$output .= '<label for="'.esc_attr($this->get_field_id($field)).'">'.__('Error message', 'thegem').':</label>';
						$output .= '<div class="field"><input name="'.esc_attr($this->get_field_name($field)).'" id="'.esc_attr($this->get_field_id($field)).'" type="text" value="'.esc_attr($this->get_field_value($field, $default_value)).'"></div>';
						$output .= '</div>';

						$output .= '</div>';
					}
					$output .= '</div>';
				}
				break;
		}

		return $output;
	}

	public static function get_fonts_list($full = false) {
		return function_exists('thegem_fonts_list') ? thegem_fonts_list($full) : array();
	}

	public static function get_font_styles_list() {
		return array(
			'100'=>'Thin',
			'200'=>'Extra-Light',
			'300'=>'Light',
			'regular'=>'Normal',
			'500'=>'Medium',
			'600'=>'Semi-Bold',
			'700'=>'Bold',
			'800'=>'Extra-Bold',
			'900'=>'Ultra-Bold',
			'100italic'=>'Thin Italic',
			'200italic'=>'Extra-Light Italic',
			'300italic'=>'Light Italic',
			'italic'=>'Normal Italic',
			'500italic'=>'Medium Italic',
			'600italic'=>'Semi-Bold Italic',
			'700italic'=>'Bold Italic',
			'800italic'=>'Extra-Bold Italic',
			'900italic'=>'Ultra-Bold Italic',
		);
	}

	public function get_font_data() {
		if (isset($_REQUEST['security']) && wp_verify_nonce(sanitize_key($_REQUEST['security']), 'thegem_gdpr_ajax_security') && isset($_REQUEST['font'])) {
			$fontsList = static::get_fonts_list(true);
			$font = $_REQUEST['font'];
			$data = array();

			if (!empty($fontsList[$font])) {
				$data = $fontsList[$font];
				foreach ($data['variants'] as $k=>$variant) {
					$font_styles_list = static::get_font_styles_list();
					$data['variants'][$variant] = $font_styles_list[$variant];
					unset($data['variants'][$k]);
				}
			}

			echo json_encode($data);
			exit;
		}

		die(-1);
	}

	public static function get_font_styles($font) {
		$fontsList = static::get_fonts_list(true);
		$data = array();
		if (!empty($fontsList[$font])) {
			foreach ($fontsList[$font]['variants'] as $variant) {
				$font_styles_list = static::get_font_styles_list();
				$data[$variant] = $font_styles_list[$variant];
			}
		}
		return $data;
	}

	public static function get_font_sets($font) {
		$fontsList = static::get_fonts_list(true);
		$data = array();
		if (!empty($fontsList[$font])) {
			$data = $fontsList[$font]['subsets'];
		}
		return $data;
	}

	public static function is_selected_fallback_fonts() {
		$additionals_fonts = thegem_additionals_fonts();
		if (!empty($additionals_fonts)) {
			foreach ($additionals_fonts as $additionals_font) {
				if (isset($additionals_font['font_is_fallback']) && $additionals_font['font_is_fallback']) {
					return true;
				}
			}
		}

		return false;
	}

	public function get_fallback_fonts_data() {
		$font_elements = function_exists('thegem_get_font_options_list') ? thegem_get_font_options_list() : array();
		$additional_fonts = thegem_additionals_fonts();
		$fallback_items = array();
		$custom_fonts = array();
		$fallback_fonts = array();

		foreach ($additional_fonts as $additional_font) {
			if (!empty($additional_font['font_is_fallback'])) {
				if (empty($additional_font['font_fallback_elements'])) {
					$fallback_items['default']['font_name'] = $additional_font['font_name'];
				} else {
					foreach ($additional_font['font_fallback_elements'] as $font_fallback_element) {
						$fallback_items[$font_fallback_element['name']] = $font_fallback_element;
						$fallback_items[$font_fallback_element['name']]['font_name'] = $additional_font['font_name'];
					}
				}

				unset($additional_font['font_is_fallback']);
				unset($additional_font['font_fallback_elements']);
				$custom_fonts[$additional_font['font_name']] = $additional_font;
			}
                        $fallback_fonts[]=$additional_font['font_name'];
		}

		$exclude_items = array();
		foreach ($font_elements as $element) {
			if (($font = thegem_get_option($element.'_family')) && !in_array($font, $fallback_fonts) && array_key_exists($font, $custom_fonts) && !in_array($font, $exclude_items)) {
				$exclude_items[$element] = $font;
				unset($custom_fonts[$font]);
			}
		}

		$exclude_items = (array_diff(array_keys($exclude_items), array_keys($fallback_items)));
		$custom_fonts = array_values($custom_fonts);

		$data = array(
			'fallback_items'=>$fallback_items,
			'exclude_items'=>$exclude_items,
			'custom_fonts'=>$custom_fonts
		);

		return $data;
	}

	public function generate_custom_css() {
		$settings = $this->options[TheGemGdpr::TYPE_SETTINGS];
		if (!empty($settings['consents']) && $settings['consents'][TheGemGdpr::CONSENT_NAME_GOOGLE_FONTS] && static::is_selected_fallback_fonts()) {
			$this->build_custom_css(TheGemGdpr::OPTION_GROUP.'_'.TheGemGdpr::TYPE_SETTINGS);
		}
	}

	public function update_settings_options() {
		$this->options = TheGemGdpr::get_options();
		$this->generate_custom_css();
	}

	public function get_custom_css() {
		ob_start();
		require plugin_dir_path(__DIR__).'custom-css.php';
		$css = ob_get_clean();
		return $css;
	}

	public function build_custom_css($option_page) {
		global $wp_filesystem;

		$status = true;
		if (!$wp_filesystem || !is_object($wp_filesystem)) {
			require_once(ABSPATH.'/wp-admin/includes/file.php');
			$status = WP_Filesystem(false, false, true);
		}

		if (!$status) {
			include_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
                        include_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
                        $wp_filesystem = new WP_Filesystem_Direct('');
		}

		if (false === ($upload_dir = static::check_create_upload_dir($wp_filesystem, $option_page))) {
			return true;
		}

		$file_name = static::get_custom_css_name();
		$file_url = $upload_dir.'/'.$file_name;
		$css_string = $this->get_custom_css();

		if (!$wp_filesystem->put_contents($file_url, $css_string, FS_CHMOD_FILE)) {
			if (is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code()) {
				add_settings_error($option_page, $wp_filesystem->errors->get_error_code(), __('Something went wrong: custom.css could not be created.', 'thegem') . $wp_filesystem->errors->get_error_message(), 'error');
			} elseif (!$wp_filesystem->connect()) {
				add_settings_error($option_page, $wp_filesystem->errors->get_error_code(), __('File could not be created. Connection error.', 'thegem'), 'error');
			} elseif (!$wp_filesystem->is_writable($file_url)) {
				add_settings_error($option_page, $wp_filesystem->errors->get_error_code(), sprintf(__('File could not be created. Cannot write custom css to "%s".', 'thegem'), $file_url), 'error');
			} else {
				add_settings_error($option_page, $wp_filesystem->errors->get_error_code(), __('File could not be created. Problem with access.', 'thegem'), 'error');
			}

			return false;
		}

		$old_file_name = get_option(TheGemGdpr::OPTION_CUSTOM_CSS_FILENAME);
		$old_file_url = $upload_dir.'/'.$old_file_name;
		if ($old_file_url && file_exists($old_file_url)) {
			$wp_filesystem->delete($old_file_url);
		}

		update_option(TheGemGdpr::OPTION_CUSTOM_CSS_FILENAME, $file_name);

		return true;
	}

	public static function check_create_upload_dir($wp_filesystem, $option_page) {
		$upload_dir = static::get_upload_dir();
		if (!$wp_filesystem->is_dir($upload_dir)) {
			if (!$wp_filesystem->mkdir($upload_dir, 0777)) {
				add_settings_error($option_page, $wp_filesystem->errors->get_error_code(), sprintf(__('te %s directory in uploads directory.', 'thegem'), $upload_dir), 'error');
				return false;
			}
		}

		return $upload_dir;
	}

	public static function get_upload_dir() {
		global $wp_filesystem;
		$upload_dir = wp_upload_dir();
		return $wp_filesystem->find_folder($upload_dir['basedir']).TheGemGdpr::NAME_UPLOAD_DIR;
	}

	public static function get_custom_css_name() {
		$name = 'custom-'.wp_generate_password(8, false);
		return $name.'.css';
	}

	public function get_consents_with_field_poster() {
		return array(
			TheGemGdpr::CONSENT_NAME_YOUTUBE,
			TheGemGdpr::CONSENT_NAME_VIMEO,
			TheGemGdpr::CONSENT_NAME_GOOGLE_MAPS
		);
	}

	public function get_consents_with_field_state() {
		return array(
			TheGemGdpr::CONSENT_NAME_YOUTUBE,
			TheGemGdpr::CONSENT_NAME_VIMEO,
			TheGemGdpr::CONSENT_NAME_GOOGLE_MAPS,
			TheGemGdpr::CONSENT_NAME_GOOGLE_FONTS,
			TheGemGdpr::CONSENT_NAME_TRACKING
		);
	}

	public function get_consents_with_field_required() {
		return array(
			TheGemGdpr::CONSENT_NAME_YOUTUBE,
			TheGemGdpr::CONSENT_NAME_VIMEO,
			TheGemGdpr::CONSENT_NAME_GOOGLE_MAPS,
			TheGemGdpr::CONSENT_NAME_GOOGLE_FONTS,
			TheGemGdpr::CONSENT_NAME_TRACKING
		);
	}

	public function get_consents_with_field_title() {
		return array(
			TheGemGdpr::CONSENT_NAME_REQUIRED
		);
	}

	private function get_default_value_for_settings() {
		return array (
			'privacy_policy_page' => '',
			'privacy_policy_link_text' => __('Privacy Policy', 'thegem'),
			'cookies_policy_page' => '',
			'cookies_policy_link_text' => __('Cookies Policy', 'thegem'),
			'modal_title' => __('Privacy Preferences', 'thegem'),
			'excerpt_text' => __('When you visit our website, it may store information through your browser from specific services, usually in form of cookies. Here you can change your privacy preferences. Please note that blocking some types of cookies may impact your experience on our website and the services we offer.', 'thegem'),
			'notice_text' => __('This content is blocked. Please review your [gem_privacy_settings_link]Privacy Preferences[/gem_privacy_settings_link].', 'thegem'),
			'save_preferences_button_text' => __('Save Preferences', 'thegem'),
			'consents' => array (
				'privacy-policy' => array (
					'description' => __('You have read and agreed to our privacy policy', 'thegem')
				)
			),
			'overlay_styles' => array(
				'background_color' => '#393D50',
				'background_opacity' => 80,
				'overlay_background_color' => '#ffffff',
				'bottom_bar_background_color' => '#f0f3f2',
				'title_font' => array (
					'font_family' => 'Montserrat UltraLight',
					'font_style' => 'regular',
					'font_sets' => '',
					'font_size' => 24,
					'line_height' => 38
				),
				'title_color' => '#3c3950',
				'title_icon_color' => '#00bcd4',
				'text_font' => array (
					'font_family' => 'Source Sans Pro',
					'font_style' => 'regular',
					'font_sets' => '',
					'font_size' => 14,
					'line_height' => 23
				),
				'text_color' => '#5f727f',
				'separator_color' => '#dfe5e8',
				'switch_disabled_background_color' => '#b6c6c9',
				'switch_enabled_background_color' => '#00bcd4',
				'button_text_color' => '#00bcd4',
				'button_text_hover_color' => '#f0f3f2',
				'button_background_color' => '#f0f3f2',
				'button_background_hover_color' => '#00bcd4',
				'button_border_color' => '#00bcd4',
				'button_border_hover_color' => '#00bcd4',
				'link_color' => '#00bcd4',
				'link_hover_color' => '#00bcd4'
			)
		);
	}
}


