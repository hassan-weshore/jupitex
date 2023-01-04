<?php

class TheGemGdpr {

	CONST PLUGIN_NAME = 'TheGem Privacy & GDPR';
	CONST PLUGIN_ID = 'thegem-gdpr';
	CONST PLUGIN_VERSION = '1.0';
	CONST OPTION_GROUP = 'thegem_gdpr';

	CONST TYPE_CONSENT_BAR = 'consent_bar';
	CONST TYPE_INTEGRATIONS = 'integrations';
	CONST TYPE_SETTINGS = 'settings';

	CONST NAME_UPLOAD_DIR = 'thegem-gdpr';
	CONST OPTION_CUSTOM_CSS_FILENAME = 'thegem_gdpr_custom_css_file';

	CONST COOKIE_NAME_CONSENT_BAR = 'thegem_consent_bar';
	CONST COOKIE_NAME_CONSENTS = 'thegem_gdpr_consents';
	CONST USER_META_NAME_CONSENTS = 'thegem_consents';

	CONST CONSENT_NAME_PRIVACY_POLICY = 'privacy-policy';
	CONST CONSENT_NAME_GOOGLE_FONTS = 'google-fonts';
	CONST CONSENT_NAME_GOOGLE_MAPS = 'google-maps';
	CONST CONSENT_NAME_TRACKING = 'tracking';
	CONST CONSENT_NAME_YOUTUBE = 'youtube';
	CONST CONSENT_NAME_VIMEO = 'vimeo';
	CONST CONSENT_NAME_REQUIRED = 'required';
	CONST CONSENT_NAME_INSTAGRAM = 'instagram';
	CONST CONSENT_NAME_FACEBOOK = 'facebook';
	CONST CONSENT_NAME_TWITTER = 'twitter';
	CONST CONSENT_NAME_FLICKR = 'flickr';

	CONST CONSENT_BAR_POSITION_TOP = 'top';
	CONST CONSENT_BAR_POSITION_BOTTOM = 'bottom';

	public $options;
	public $consents;

	public function __construct() {
		$this->options = $this->get_options();
		$this->get_consents();
	}

	public static function getInstance() {
		static $instance;

		if (!$instance) {
			$instance=new static;
		}

		return $instance;
	}

	public function run() {
		$this->load_classes();
		add_shortcode('gem_privacy_settings_link', array($this, 'privacy_settings_link_shortcode'));
	}

	public function load_classes() {
		require_once plugin_dir_path(__FILE__).'thegem-gdpr-admin.php';
		require_once plugin_dir_path(__FILE__).'thegem-gdpr-public.php';
		require_once plugin_dir_path(__FILE__).'thegem-gdpr-integration.php';

		$theGemGdprAdmin = new TheGemGdprAdmin();
		$theGemGdprAdmin->options = $this->options;
		$theGemGdprAdmin->init();

		$theGemGdprPublic = new TheGemGdprPublic();
		$theGemGdprPublic->options = $this->options;
		$theGemGdprPublic->init();

		$theGemGdprIntegration = new TheGemGdprIntegration();
		$theGemGdprIntegration->options = $this->options;
		$theGemGdprIntegration->init();
	}

	public static function get_plugin_data() {
		return get_plugin_data(THEGEM_GDPR_PLUGIN_ROOT_FILE);
	}

	public function get_consents() {
		$consents = array();
		if (!empty($this->options[TheGemGdpr::TYPE_SETTINGS])) {
			$options = $this->options[TheGemGdpr::TYPE_SETTINGS];
			$consents = is_array($options['consents']) ? $options['consents'] : array();
		}
		$this->consents = $consents;
	}

	public static function get_types_list() {
		return array(
			TheGemGdpr::TYPE_CONSENT_BAR,
			TheGemGdpr::TYPE_INTEGRATIONS,
			TheGemGdpr::TYPE_SETTINGS,
		);
	}

	public static function get_consent_bar_position_list() {
		return array(
			static::CONSENT_BAR_POSITION_BOTTOM => __('Bottom', 'thegem'),
			static::CONSENT_BAR_POSITION_TOP => __('Top', 'thegem'),
		);
	}

	public static function get_options() {
		$options = array();
		foreach (static::get_types_list() as $type) {
		    $data = get_option(TheGemGdpr::OPTION_GROUP.'_'.$type);

		    if (is_string($data)) {
		        $data = unserialize($data);
            }

			$options[$type] = $data ? $data : null;
		}
		return $options;
	}

	public static function is_empty_options() {
		$result = true;
		foreach (static::get_types_list() as $type) {
			if (get_option(TheGemGdpr::OPTION_GROUP.'_'.$type)) {
				$result = false;
			}
		}
		return $result;
	}

	public static function get_allowed_html_tags() {
		return array(
			'a' => array(
				'class' => array(),
				'href' => array(),
				'title' => array(),
				'target' => array(),
				'rel' => array(),
			),
			'br' => array(),
			'em' => array(),
			'strong' => array(),
			'u' => array(),
			'span' => array(
				'class' => array(),
			)
		);
	}

	public static function get_allowed_html_tags_output() {
		$allowed_tags = TheGemGdpr::get_allowed_html_tags();
		$tags = '';
		foreach ($allowed_tags as $tag => $attributes) {
			$tags .= '<' . $tag;
			if (!empty($attributes)) {
				foreach ($attributes as $attribute => $data) {
					$tags .= ' ' . $attribute . '=""';
				}
			}
			$tags .= '>';
		}

		return '<pre>'.esc_html($tags).'</pre>';
	}

	public function privacy_settings_link_shortcode($atts, $content = null) {
		$atts = shortcode_atts( array(
			'content' => $content ? esc_html($content) : __('Privacy Preferences', 'thegem'),
		), $atts, 'gem_privacy_settings_link');

		return '<a href="#" class="btn-gdpr-preferences">'.esc_html($atts['content']).'</a>';
	}

	public static function get_consent_type_list() {
		return array(
			static::CONSENT_NAME_PRIVACY_POLICY=>__('Privacy Policy', 'thegem'),
			static::CONSENT_NAME_GOOGLE_FONTS => __('Google Fonts', 'thegem'),
			static::CONSENT_NAME_GOOGLE_MAPS => __('Google Maps', 'thegem'),
			static::CONSENT_NAME_TRACKING => __('Tracking', 'thegem'),
			static::CONSENT_NAME_YOUTUBE => __('YouTube', 'thegem'),
			static::CONSENT_NAME_VIMEO => __('Vimeo', 'thegem'),
			static::CONSENT_NAME_REQUIRED => __('Required', 'thegem'),
			//TheGemGdpr::CONSENT_NAME_INSTAGRAM => __('Instagram', 'thegem'),
			//TheGemGdpr::CONSENT_NAME_FACEBOOK => __('Facebook', 'thegem'),
			//TheGemGdpr::CONSENT_NAME_TWITTER => __('Twitter', 'thegem'),
			//TheGemGdpr::CONSENT_NAME_FLICKR => __('Flickr', 'thegem')
		);
	}

	public function has_consent($consent) {
		$is_state_default = $this->is_consent_state_default($consent);

		if (is_user_logged_in()) {
			$user = wp_get_current_user();
			$consents = array_map('sanitize_text_field', (array) json_decode(wp_unslash(get_user_meta($user->ID, TheGemGdpr::USER_META_NAME_CONSENTS, true)), true));
		} else if (isset($_COOKIE[TheGemGdpr::COOKIE_NAME_CONSENTS]) && !empty($_COOKIE[TheGemGdpr::COOKIE_NAME_CONSENTS])) {
			$consents = array_map('sanitize_text_field', (array) json_decode(wp_unslash($_COOKIE[TheGemGdpr::COOKIE_NAME_CONSENTS]), true));
		} else {
			$consents = array();
		}

		if (!empty($consents) && array_key_exists($consent, $consents)) {
			return boolval($consents[$consent]);
		} else if ($is_state_default) {
			return true;
		}

		return false;
	}

	public function is_consent_state_default($consent) {
		if (array_key_exists($consent, $this->consents) && $this->consents[$consent]['state']) {
			return true;
		}
		return false;
	}

	public function is_allow_consent($consent) {
		if (thegem_is_plugin_active('js_composer/js_composer.php')) {
			global $vc_manager;
			if ($vc_manager->mode() == 'admin_frontend_editor' || $vc_manager->mode() == 'admin_page' || $vc_manager->mode() == 'page_editable') {
				return true;
			}
		}

		if (empty($this->consents)) return true;

		if (array_key_exists($consent, $this->consents)) {
			if ($this->consents[$consent]['required']) {
				return true;
			}
			return $this->has_consent($consent);
		}

		return true;
	}

	public function get_consent_poster($consent) {
		if (!empty($this->consents[$consent]) && !empty($this->consents[$consent]['poster'])) {
			return esc_attr($this->consents[$consent]['poster']);
		}

		$poster = false;
		switch ($consent) {
			case static::CONSENT_NAME_YOUTUBE:
				$poster = plugins_url('/assets/img/bg-youtube.jpg', THEGEM_GDPR_PLUGIN_ROOT_FILE);
				break;
			case static::CONSENT_NAME_VIMEO:
				$poster = plugins_url('/assets/img/bg-vimeo.jpg', THEGEM_GDPR_PLUGIN_ROOT_FILE);
				break;
			case static::CONSENT_NAME_GOOGLE_MAPS:
				$poster = plugins_url('/assets/img/bg-google-maps.jpg', THEGEM_GDPR_PLUGIN_ROOT_FILE);
				break;
		}
		return $poster;
	}

	public function get_consent_icon($consent) {
		$icon = false;
		switch ($consent) {
			case static::CONSENT_NAME_YOUTUBE:
				$icon = '<i class="gem-consent-icon-youtube"></i>';
				break;
			case static::CONSENT_NAME_VIMEO:
				$icon = '<i class="gem-consent-icon-vimeo"></i>';
				break;
			case static::CONSENT_NAME_GOOGLE_MAPS:
				$icon = '<i class="gem-consent-icon-google-maps"></i>';
				break;
		}
		return $icon;
	}

	public function get_notice_text() {
		$notice = esc_html__('This content is blocked. Please review your [gem_privacy_settings_link]Privacy Settings[/gem_privacy_settings_link].', 'thegem');
		if (!empty($this->options[TheGemGdpr::TYPE_SETTINGS]['notice_text'])) {
			$notice = $this->options[TheGemGdpr::TYPE_SETTINGS]['notice_text'];
		}
		return $notice;
	}

	public function replace_disallowed_content($output, $consent, $options = array()) {
		if ($this->is_allow_consent($consent)) {
			return $output;
		}

		$notice = $this->get_notice_text();

		$wrapStyles = array();
		if (!empty($options['width'])) {
			$wrapStyles[] = 'width:'.$options['width'];
		}

		if (!empty($options['ratio_style'])) {
			$ratio_style = $options['ratio_style'];
			$wrapStyles[] = $ratio_style;
			unset($options['height']);
		}

		if (!empty($options['height'])) {
			$wrapStyles[] = 'height:'.$options['height'];
		}

		if ($poster = $this->get_consent_poster($consent)) {
			$wrapStyles[] = 'background-image:url('.$poster.')';
		}

		$output  = '<div class="gem-gdpr-no-consent-wrap" '.(!empty($wrapStyles) ? 'style="'.implode(';', $wrapStyles).'"':'').'>';
		$output .= '<div class="gem-gdpr-no-consent-inner">';
		$output .= '<div class="gem-gdpr-no-consent-notice-text">'.$this->get_consent_icon($consent).wp_kses_post(do_shortcode($notice)).'</div>';
		$output .= '</div>';
		$output .= '</div>';
		return $output;
	}

	public function disallowed_portfolio_type_video($types) {
		foreach ($types as $k=>$item) {
			switch ($item['type']) {
				case 'youtube';
					if (!$this->is_allow_consent(static::CONSENT_NAME_YOUTUBE)) {
						unset($types[$k]);
					}
					break;
				case 'vimeo';
					if (!$this->is_allow_consent(static::CONSENT_NAME_VIMEO)) {
						unset($types[$k]);
					}
					break;
			}
		}
		return $types;
	}

}
