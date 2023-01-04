<?php

class TheGemGdprIntegration {

	CONST FIELD_NAME = 'thegemgdpr';

	public $options;

	public function init() {
		$theGemGdprWP = new TheGemGdprWP();
		$theGemGdprWP->init();

		foreach (static::get_activated_plugins_ids() as $plugin_id) {
			switch ($plugin_id) {
				case TheGemGdprCF7::ID:
					$theGemGdprCF7 = new TheGemGdprCF7();
					$theGemGdprCF7->init();
					break;
			}
		}
	}

	public static function get_list_supported_plugins() {
		return array(
			array(
				'id' => TheGemGdprCF7::ID,
				'supported_version' => TheGemGdprCF7::SUPPORTED_VERSION,
				'file' => TheGemGdprCF7::FILE,
				'name' => __('Contact Form 7', 'thegem'),
				'description' => __('A GDPR checkbox will be automatically added to every form you activate.', 'thegem'),
			)
		);
	}

	public static function get_active_plugins() {
		$plugins = get_option('active_plugins');
		$current_plugin = plugin_basename(THEGEM_GDPR_PLUGIN_ROOT_FILE);

		if ($key = array_search($current_plugin, $plugins)) {
			unset($plugins[$key]);
		}
		return array_values($plugins);
	}

	public static function get_activated_plugins_ids() {
		$ids = array();
		foreach (static::get_list_supported_plugins() as $plugin) {
			if (in_array($plugin['file'], static::get_active_plugins())) {
				$ids[]=$plugin['id'];
			}
		}
		return $ids;
	}

	public static function get_supported_plugins() {
		$supported_plugins = array();
		foreach (static::get_list_supported_plugins() as $plugin) {
			if (in_array($plugin['file'], static::get_active_plugins())) {
				if (is_admin()) {
					$plugin['is_supported'] = true;
					if (isset($plugin['supported_version'])) {
						$pluginData = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin['file']);
						if (!empty($pluginData['Version']) && $pluginData['Version'] < $plugin['supported_version']) {
							$plugin['is_supported'] = false;
						}
					}
					$supported_plugins[]=$plugin;
				}
			}
		}

		return $supported_plugins;
	}

	public static function get_default_checkbox_text() {
		return __('By using this form you agree with the storage and handling of your data by this website.', 'thegem');
	}

	public static function get_default_error_message() {
		return __('Please accept the privacy checkbox.', 'thegem');
	}

}