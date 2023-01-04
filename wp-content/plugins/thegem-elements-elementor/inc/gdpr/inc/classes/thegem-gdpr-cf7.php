<?php

class TheGemGdprCF7 {

	CONST SUPPORTED_VERSION = '5.1.1';
	CONST ID = 'contact-form-7';
	CONST FILE = 'contact-form-7/wp-contact-form-7.php';
	CONST OPTION_NAME = 'contact_form_7';

	public $options;

	public function init() {
		add_action('update_option_'.TheGemGdpr::OPTION_GROUP.'_'.TheGemGdpr::TYPE_INTEGRATIONS, array($this, 'integration'));
		add_action('wpcf7_init', array($this, 'add_form_tag_support'));
		add_filter('wpcf7_validate_'.TheGemGdprIntegration::FIELD_NAME, array($this, 'validate_field'), 10, 2);
	}

	public function integration() {
		$this->get_options();
		$this->clean();
		foreach ($this->get_enabled_forms() as $form_id=>$form) {
			if ($form['state']) {
				$tag = '['.TheGemGdprIntegration::FIELD_NAME.']'.$this->get_checkbox_text($form_id).'[/'.TheGemGdprIntegration::FIELD_NAME.']';
				$output = get_post_meta($form_id, '_form', true);
				$pattern = '/(\['.TheGemGdprIntegration::FIELD_NAME.'\].*?\[\/'.TheGemGdprIntegration::FIELD_NAME.'\])/';
				preg_match($pattern, $output, $matches);
				if (!empty($matches)) {
					$output = str_replace($matches[0], $tag, $output);
				} else {
					$pattern = '/(\[submit?.*\])/';
					preg_match($pattern, $output, $matches);
					if (!empty($matches)) {
						$output = preg_replace($pattern, "$tag\n\n" . $matches[0], $output);
					} else {
						$output = $output . "\n\n$tag";
					}
				}
				update_post_meta($form_id, '_form', $output);
			}
		}
	}

	public function clean() {
		foreach ($this->get_form_ids() as $form_id) {
			$output = get_post_meta($form_id, '_form', true);
			$pattern = '/(\['.TheGemGdprIntegration::FIELD_NAME.'\].*?\[\/'.TheGemGdprIntegration::FIELD_NAME.'\])\n?\n?/';
			preg_match($pattern, $output, $matches);
			if (!empty($matches)) {
				$output = preg_replace($pattern, '', $output);
				update_post_meta($form_id, '_form', $output);
			}
		}
	}

	public function get_enabled_forms() {
		if (empty($this->options) || !$this->options['enabled']) {
			return array();
		}

		$forms = array();
		if ($this->options['enabled'] && !empty($this->options['forms'])) {
			foreach ($this->options['forms'] as $id=>$form) {
				$forms[$id] = $form;
			}
		}
		return $forms;
	}

	public function get_options() {
        $options = get_option(TheGemGdpr::OPTION_GROUP.'_'.TheGemGdpr::TYPE_INTEGRATIONS);
        if (is_string($options)) {
            $options = unserialize($options);
        }
        $this->options = $options ? $options[static::ID] : array();
	}

	public static function get_form_ids() {
		return get_posts(array(
			'post_type' => 'wpcf7_contact_form',
			'posts_per_page' => -1,
			'fields' => 'ids'
		));
	}

	public function add_form_tag_support() {
		wpcf7_add_form_tag(TheGemGdprIntegration::FIELD_NAME, array($this, 'add_form_tag_handler'));
	}

	public function add_form_tag_handler($tag) {
		$tag = (is_array($tag)) ? new \WPCF7_FormTag($tag) : $tag;
		$tag->name = TheGemGdprIntegration::FIELD_NAME;
		$label = (!empty($tag->content)) ?  wp_kses(wp_unslash($tag->content), TheGemGdpr::get_allowed_html_tags()) : $this->get_checkbox_text();
		$class = wpcf7_form_controls_class($tag->type, 'wpcf7-validates-as-required');
		$validation_error = wpcf7_get_validation_error($tag->name);
		if ($validation_error) {
			$class .= ' wpcf7-not-valid';
		}
		$label_first = $tag->has_option('label_first');
		$use_label_element = $tag->has_option('use_label_element');
		$atts = wpcf7_format_atts(array(
			'class' => $tag->get_class_option($class),
			'id' => $tag->get_id_option(),
		));
		$item_atts = wpcf7_format_atts(array(
			'type' => 'checkbox',
			'name' => $tag->name,
			'value' => 1,
			'tabindex' => $tag->get_option('tabindex', 'signed_int', true),
			'aria-required' => 'true',
			'aria-invalid' => ($validation_error) ? 'true' : 'false',
			'class'=>'gem-checkbox'
		));

		if ($label_first) {
			$output = sprintf('<span class="wpcf7-list-item-label">%1$s</span><input %2$s />', $label, $item_atts);
		} else {
			$output = sprintf('<input %2$s /><span class="wpcf7-list-item-label">%1$s</span>', $label, $item_atts);
		}

		if ($use_label_element) {
			$output = '<label>' . $output . '</label>';
		}

		$output = '<span class="wpcf7-list-item">' . $output . '</span>';
		$output = sprintf('<span class="wpcf7-form-control-wrap %1$s"><span %2$s>%3$s</span>%4$s</span>', sanitize_html_class($tag->name), $atts, $output, $validation_error);
		return $output;
	}

	public function validate_field(\WPCF7_Validation $result, $tag) {
		$this->get_options();
		$tag = (gettype($tag) == 'array') ? new \WPCF7_FormTag($tag) : $tag;
		$form_id = (isset($_POST['_wpcf7']) && is_numeric($_POST['_wpcf7'])) ? (int)$_POST['_wpcf7'] : 0;
		$tag->name = TheGemGdprIntegration::FIELD_NAME;
		$name = $tag->name;
		$value = (isset($_POST[$name])) ? filter_var($_POST[$name], FILTER_VALIDATE_BOOLEAN) : false;
		if ($value === false) {
			$result->invalidate($tag, $this->get_error_message($form_id));
		}
		return $result;
	}

	public function get_checkbox_text($form_id=null) {
		$form = $this->options['forms'][$form_id];
		if (!$form || $form['checkbox_text']=='') {
			return TheGemGdprIntegration::get_default_checkbox_text();
		}
		$output = wp_kses(wp_unslash($form['checkbox_text']), TheGemGdpr::get_allowed_html_tags());
		return $output;
	}

	public function get_error_message($form_id=null) {
		$form = $this->options['forms'][$form_id];
		if (!$form || $form['error_message']=='') {
			return TheGemGdprIntegration::get_default_error_message();
		}
		$output = esc_html($form['error_message']);
		return $output;
	}

}