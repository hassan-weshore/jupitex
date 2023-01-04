<?php

class TheGemGdprPublic {

	CONST PUBLIC_CONSENT_BAR_ID = 'gdpr-consent-bar';
	CONST PUBLIC_CONSENT_BAR_TEXT_ID = 'gdpr-consent-bar-text';
	CONST PUBLIC_CONSENT_BAR_LINK_ID = 'gdpr-consent-bar-link';
	CONST PUBLIC_CONSENT_BAR_BUTTON_ID = 'gdpr-consent-bar-button';

	CONST PUBLIC_OVERLAY_ID = 'gdpr-privacy-preferences';
	CONST PUBLIC_OVERLAY_TITLE_ID = 'gdpr-privacy-preferences-title';
	CONST PUBLIC_OVERLAY_TEXT_ID = 'gdpr-privacy-preferences-text';
	CONST PUBLIC_OVERLAY_BUTTON_ID = 'gdpr-privacy-preferences-save-button';
	CONST PUBLIC_OVERLAY_CONSENT_CHECKBOX_ID = 'gdpr-consent-checkbox';

	public $options;

	public function init() {
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action('wp_footer', array($this, 'generate_html'));
		add_action('admin_post_thegem_gdpr_update_privacy_preferences', array($this, 'update_privacy_preferences'));
		add_action('admin_post_nopriv_thegem_gdpr_update_privacy_preferences', array($this, 'update_privacy_preferences'));
	}

	public function enqueue_scripts() {
        if (!TheGemGdpr::is_empty_options()) {
			wp_enqueue_style(TheGemGdpr::PLUGIN_ID, plugin_dir_url(dirname(__DIR__)).'assets/css/public.css', array(), TheGemGdpr::PLUGIN_VERSION);
			wp_enqueue_script(TheGemGdpr::PLUGIN_ID, plugin_dir_url(dirname(__DIR__)).'assets/js/public.js', array('jquery'), TheGemGdpr::PLUGIN_VERSION, true);
			wp_localize_script(TheGemGdpr::PLUGIN_ID, 'thegem_gdpr_options', array(
				'consent_bar_cookie_name'=>TheGemGdpr::COOKIE_NAME_CONSENT_BAR
			));
		}

		if ($custom_styles = $this->get_custom_styles()) {
			wp_add_inline_style(TheGemGdpr::PLUGIN_ID, $custom_styles);
		}

		if ($fonts_url = $this->get_google_fonts_url()) {
			wp_enqueue_style(TheGemGdpr::PLUGIN_ID.'-google-fonts', $fonts_url);
		}

		if (!TheGemGdpr::getInstance()->is_allow_consent(TheGemGdpr::CONSENT_NAME_GOOGLE_FONTS) && TheGemGdprAdmin::is_selected_fallback_fonts()) {
			$custom_css_filename = get_option(TheGemGdpr::OPTION_CUSTOM_CSS_FILENAME);
			if ($custom_css_filename) {
				$wp_upload_dir = wp_upload_dir();
				$custom_css_file_url = $wp_upload_dir['baseurl'].'/'.TheGemGdpr::NAME_UPLOAD_DIR.'/'.$custom_css_filename;
				wp_enqueue_style(TheGemGdpr::PLUGIN_ID.'-custom', $custom_css_file_url, array(), TheGemGdpr::PLUGIN_VERSION);
			}
		}
	}

	public function generate_html() {

		if ($this->is_active_consent_bar()) {
			include plugin_dir_path(__DIR__).'views/public/consent-bar.php';
		}

		if (!empty($this->options[TheGemGdpr::TYPE_SETTINGS])) {
			include plugin_dir_path(__DIR__).'views/public/privacy-preferences.php';
		}
	}

	public function update_privacy_preferences() {
		if (!isset($_POST[ 'update-privacy-preferences-nonce' ]) || ! wp_verify_nonce(sanitize_key($_POST[ 'update-privacy-preferences-nonce']), 'thegem_gdpr_update_privacy_preferences')) {
			wp_die(__( 'We could not verify the the security key. Please try again.', 'thegem'));
		}

		if (isset($_POST['consents'])) {
			$consents = array_map('boolval', (array)$_POST['consents']);
			$consents_json = json_encode($consents);
			setcookie(TheGemGdpr::COOKIE_NAME_CONSENTS, $consents_json, time()+YEAR_IN_SECONDS, '/');

			if (is_user_logged_in()) {
				$user = wp_get_current_user();
				update_user_meta($user->ID, TheGemGdpr::USER_META_NAME_CONSENTS, $consents_json);
			}
		}

		wp_safe_redirect(esc_url_raw(wp_get_referer()));
		exit;
	}

	public function is_active_consent_bar() {
		if (!empty($this->options[TheGemGdpr::TYPE_CONSENT_BAR]['active']) &&
			$this->options[TheGemGdpr::TYPE_CONSENT_BAR]['active'] &&
			empty($_COOKIE[TheGemGdpr::COOKIE_NAME_CONSENT_BAR])) {
			return true;
		}

		return false;
	}

	public static function get_background_color($color, $opacity = null) {
		$rgb_color = static::hex_to_rgb($color);
		if (is_array($rgb_color)) {
			return 'rgba('.implode(",", static::hex_to_rgb($color)).','.($opacity ? $opacity/100 : 1).')';
		}
		return '';
	}

	public static function hex_to_rgb($color) {
		if (strpos($color, '#') === 0) {
			$color = substr($color, 1);
			if (strlen($color) == 3) {
				return array(hexdec($color[0]), hexdec($color[1]), hexdec($color[2]));
			} elseif (strlen($color) == 6) {
				return array(hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
			}
		}
		return $color;
	}

	public static function get_font_weight($font_style) {
		return str_replace(array('italic', 'regular'), array('', 'normal'), $font_style);
	}

	public static function get_styles($styles) {
		if (!is_array($styles)) {
			return false;
		}

		$style = '';
		$styles = array_diff($styles, array(''));

		if (!empty($styles)) {
			foreach ($styles as $k=>$value) {
				$style.=$k.':'.$value.';';
			}
			$style .= '';
		}

		return $style;
	}

	private function get_consent_bar_style() {
		$custom_styles = $this->options[TheGemGdpr::TYPE_CONSENT_BAR]['custom_styles'];
		$styles = array(
			'background-color'=> static::get_background_color(esc_attr($custom_styles['background_color']), esc_attr($custom_styles['background_opacity']))
		);
		return static::get_styles($styles);
	}

	public function get_consent_bar_text_style() {
		$custom_styles = $this->options['consent_bar']['custom_styles'];
		$text_font = $custom_styles['text_font'];

		$styles = array(
			'color'=>esc_attr($custom_styles['text_color']),
			'font-family'=>'\''.esc_attr($text_font['font_family']).'\'',
			'font-weight'=>static::get_font_weight(esc_attr($text_font['font_style'])),
			'font-size'=>esc_attr($text_font['font_size']).'px',
			'line-height'=>esc_attr($text_font['line_height']).'px',
		);

		if (strpos(esc_attr($text_font['font_style']), 'italic') !== false) {
			$styles['font-style']='italic';
		}

		return static::get_styles($styles);
	}

	public function get_consent_bar_link_style() {
		$custom_styles = $this->options[TheGemGdpr::TYPE_CONSENT_BAR]['custom_styles'];
		$link_font = $custom_styles['link_font'];

		$styles = array(
			'color'=>esc_attr($custom_styles['link_color']),
			'font-family'=>'\''.esc_attr($link_font['font_family']).'\'',
			'font-weight'=>static::get_font_weight(esc_attr($link_font['font_style'])),
			'font-size'=>esc_attr($link_font['font_size']).'px',
			'line-height'=>esc_attr($link_font['line_height']).'px',
		);

		if (strpos(esc_attr($link_font['font_style']), 'italic') !== false) {
			$styles['font-style']='italic';
		}

		return static::get_styles($styles);
	}

	private function get_consent_bar_button_style() {
		$custom_styles = $this->options[TheGemGdpr::TYPE_CONSENT_BAR]['custom_styles'];
		$styles = array(
			'color'=>esc_attr($custom_styles['button_text_color']),
			'background-color'=>esc_attr($custom_styles['button_background_color']),
			'border-color'=>esc_attr($custom_styles['button_border_color'])
		);
		return static::get_styles($styles);
	}

	private function get_consent_bar_link_hover_style() {
		$custom_styles = $this->options[TheGemGdpr::TYPE_CONSENT_BAR]['custom_styles'];
		$styles = array(
			'color'=>esc_attr($custom_styles['link_hover_color']),
		);
		return static::get_styles($styles);
	}

	private function get_consent_bar_button_hover_style() {
		$custom_styles = $this->options[TheGemGdpr::TYPE_CONSENT_BAR]['custom_styles'];
		$styles = array(
			'color'=>esc_attr($custom_styles['button_text_hover_color']),
			'background-color'=>esc_attr($custom_styles['button_background_hover_color']),
			'border-color'=>esc_attr($custom_styles['button_border_hover_color'])
		);
		return static::get_styles($styles);
	}

	private function get_overlay_style() {
		$overlay_styles = $this->options[TheGemGdpr::TYPE_SETTINGS]['overlay_styles'];
		$styles = array(
			'background-color'=> static::get_background_color(esc_attr($overlay_styles['background_color']), esc_attr($overlay_styles['background_opacity']))
		);
		return static::get_styles($styles);
	}

	private function get_overlay_box_style() {
		$overlay_styles = $this->options[TheGemGdpr::TYPE_SETTINGS]['overlay_styles'];
		$styles = array(
			'background-color'=> esc_attr($overlay_styles['overlay_background_color'])
		);
		return static::get_styles($styles);
	}

	private function get_overlay_title_style() {
		$overlay_styles = $this->options[TheGemGdpr::TYPE_SETTINGS]['overlay_styles'];
		$title_font = $overlay_styles['title_font'];

		$styles = array(
			'color'=>esc_attr($overlay_styles['title_color']),
			'font-family'=>'\''.esc_attr($title_font['font_family']).'\'',
			'font-weight'=>static::get_font_weight(esc_attr($title_font['font_style'])),
			'font-size'=>esc_attr($title_font['font_size']).'px',
			'line-height'=>esc_attr($title_font['line_height']).'px',
		);

		if (strpos(esc_attr($title_font['font_style']), 'italic') !== false) {
			$styles['font-style']='italic';
		}

		return static::get_styles($styles);
	}

	private function get_overlay_title_icon_style() {
		$overlay_styles = $this->options[TheGemGdpr::TYPE_SETTINGS]['overlay_styles'];
        $styles = array(
			'color'=>esc_attr($overlay_styles['title_icon_color']),
		);
        return static::get_styles($styles);
	}

	private function get_overlay_text_style() {
		$overlay_styles = $this->options[TheGemGdpr::TYPE_SETTINGS]['overlay_styles'];
		$text_font = $overlay_styles['text_font'];

		$styles = array(
			'color'=>esc_attr($overlay_styles['text_color']),
			'font-family'=>'\''.esc_attr($text_font['font_family']).'\'',
			'font-weight'=>static::get_font_weight(esc_attr($text_font['font_style'])),
			'font-size'=>esc_attr($text_font['font_size']).'px',
			'line-height'=>esc_attr($text_font['line_height']).'px',
		);

		if (strpos(esc_attr($text_font['font_style']), 'italic') !== false) {
			$styles['font-style']='italic';
		}
		return static::get_styles($styles);
	}

	private function get_overlay_button_style() {
		$overlay_styles = $this->options[TheGemGdpr::TYPE_SETTINGS]['overlay_styles'];
		$styles = array(
			'color'=>esc_attr($overlay_styles['button_text_color']),
			'background-color'=>esc_attr($overlay_styles['button_background_color']),
			'border-color'=>esc_attr($overlay_styles['button_border_color'])
		);
		return static::get_styles($styles);
	}

	private function get_overlay_button_hover_style() {
		$overlay_styles = $this->options[TheGemGdpr::TYPE_SETTINGS]['overlay_styles'];
		$styles = array(
			'color'=>esc_attr($overlay_styles['button_text_hover_color']),
			'background-color'=>esc_attr($overlay_styles['button_background_hover_color']),
			'border-color'=>esc_attr($overlay_styles['button_border_hover_color'])
		);
		return static::get_styles($styles);
	}

	private function get_overlay_switch_disabled_style() {
		$overlay_styles = $this->options[TheGemGdpr::TYPE_SETTINGS]['overlay_styles'];
		$styles = array(
			'background-color'=>$overlay_styles['switch_disabled_background_color']
		);
		return static::get_styles($styles);
	}

	private function get_overlay_switch_enabled_style() {
		$overlay_styles = $this->options[TheGemGdpr::TYPE_SETTINGS]['overlay_styles'];
		$styles = array(
			'background-color'=>$overlay_styles['switch_enabled_background_color']
		);
		return static::get_styles($styles);
	}

	private function get_overlay_footer_style() {
		$overlay_styles = $this->options[TheGemGdpr::TYPE_SETTINGS]['overlay_styles'];
		$styles = array(
			'background-color'=>$overlay_styles['bottom_bar_background_color']
		);
		return static::get_styles($styles);
	}

	private function get_overlay_link_style() {
		$overlay_styles = $this->options[TheGemGdpr::TYPE_SETTINGS]['overlay_styles'];
		$styles = array(
			'color'=>$overlay_styles['link_color']
		);
		return static::get_styles($styles);
	}

	private function get_overlay_link_hover_style() {
		$overlay_styles = $this->options[TheGemGdpr::TYPE_SETTINGS]['overlay_styles'];
		$styles = array(
			'color'=>$overlay_styles['link_hover_color']
		);
		return static::get_styles($styles);
	}

	private function get_overlay_item_box() {
		$overlay_styles = $this->options[TheGemGdpr::TYPE_SETTINGS]['overlay_styles'];
		$styles = array(
			'border-bottom-color'=>$overlay_styles['separator_color']
		);
		return static::get_styles($styles);
	}

	public function get_custom_styles() {
		$items = array();

		if ($this->is_active_consent_bar() && $this->options[TheGemGdpr::TYPE_CONSENT_BAR]['use_custom_styles']) {
			$items = array(
				'.gdpr-consent-bar'=>$this->get_consent_bar_style(),
				'.gdpr-consent-bar-text'=>$this->get_consent_bar_text_style(),
				'.btn-gdpr-preferences-open'=>$this->get_consent_bar_link_style(),
				'.btn-gdpr-preferences-open:hover'=>$this->get_consent_bar_link_hover_style(),
				'.btn-gdpr-agreement'=>$this->get_consent_bar_button_style(),
				'.btn-gdpr-agreement:hover'=>$this->get_consent_bar_button_hover_style(),
			);
		}

		if (!empty($this->options[TheGemGdpr::TYPE_SETTINGS]['use_overlay_custom_styles'])) {
			$items = array_merge($items, array(
				'.gdpr-privacy-preferences'=>$this->get_overlay_style(),
				'.gdpr-privacy-preferences-box'=>$this->get_overlay_box_style(),
				'.gdpr-privacy-preferences-consent-item'=>$this->get_overlay_item_box(),
				'.gdpr-privacy-preferences-title'=>$this->get_overlay_title_style(),
				'.gdpr-privacy-preferences-title:before'=>$this->get_overlay_title_icon_style(),
				'.gdpr-privacy-preferences-text'=>$this->get_overlay_text_style(),
				'.btn-gdpr-privacy-save-preferences'=>$this->get_overlay_button_style(),
				'.btn-gdpr-privacy-save-preferences:hover'=>$this->get_overlay_button_hover_style(),
				'.gdpr-privacy-checkbox .gdpr-privacy-checkbox-check'=>$this->get_overlay_switch_disabled_style(),
				'.gdpr-privacy-checkbox input:checked ~ .gdpr-privacy-checkbox-check'=>$this->get_overlay_switch_enabled_style(),
				'.gdpr-privacy-preferences-footer'=>$this->get_overlay_footer_style(),
				'.gdpr-privacy-preferences-footer-links a'=>$this->get_overlay_link_style(),
				'.gdpr-privacy-preferences-footer-links a:hover'=>$this->get_overlay_link_hover_style(),
			));
		}

		$items = array_diff($items, array(''));

		if (empty($items)) {
			return false;
		}

		$output = '';
		foreach ($items as $k=>$item) {
			$output .= $k.'{'.$item.'}'.PHP_EOL;
		}

		return $output;
	}

	public function get_google_fonts_url() {
		$theGemGdpr = new TheGemGdpr();
		if (!$theGemGdpr->is_allow_consent(TheGemGdpr::CONSENT_NAME_GOOGLE_FONTS)) {
			return false;
		}

		$used_fonts_data = array();

		if ($this->is_active_consent_bar() && $this->options[TheGemGdpr::TYPE_CONSENT_BAR]['use_custom_styles']) {
			$custom_styles = $this->options[TheGemGdpr::TYPE_CONSENT_BAR]['custom_styles'];
			$used_fonts_data[]=$custom_styles['text_font'];
			$used_fonts_data[]=$custom_styles['link_font'];
		}

		if (!empty($this->options[TheGemGdpr::TYPE_SETTINGS]['use_overlay_custom_styles'])) {
			$overlay_custom_styles = $this->options[TheGemGdpr::TYPE_SETTINGS]['overlay_styles'];
			$used_fonts_data[]=$overlay_custom_styles['title_font'];
			$used_fonts_data[]=$overlay_custom_styles['text_font'];
		}

		$used_fonts = array();
		$font_styles = array();
		$font_sets = array();
		foreach ($used_fonts_data as $used_fonts_item) {
			$used_fonts[]=$used_fonts_item['font_family'];
			$font_styles[$used_fonts_item['font_family']][]=$used_fonts_item['font_style'];
			$font_sets[$used_fonts_item['font_family']][]=$used_fonts_item['font_sets'];
		}
		$used_fonts = array_unique($used_fonts);

        if (!function_exists('thegem_get_theme_options')) {
            return false;
        }

		$font_elements = function_exists('thegem_get_font_options_list') ? thegem_get_font_options_list() : array();

		$exclude_fonts = array();
		foreach ($font_elements as $element) {
			if (($font = thegem_get_option($element.'_family')) && in_array($font, $used_fonts) && !in_array($font, $exclude_fonts)) {
				$exclude_fonts[] = $font;
			}
		}

		$include_fonts = array_diff($used_fonts, $exclude_fonts);

		if (count($include_fonts) > 0) {
			$inc_fonts = '';
			$subset = array();
			foreach ($include_fonts as $k=>$item) {
				if ('off' !== _x('on', $item.' font: on or off', 'thegem')) {
					if ($k > 0) {
						$inc_fonts .= '|';
					}

					$inc_fonts .= $item;

					if (!empty($font_styles[$item])) {
						$inc_fonts .= ':'.implode(',', $font_styles[$item]);
					}

					if (!empty($font_sets[$item])) {
						foreach ($font_sets[$item] as $set) {
							$subset = array_merge($subset, explode(',', $set));
						}
					}
				}
			}

			$query_args['family'] = urlencode($inc_fonts);

			if (!empty($subset)) {
				$query_arg['subset'] = urlencode(implode(',', array_unique($subset)));
			}

			$fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );
			return esc_url_raw( $fonts_url );
		}

		return false;
	}
}
