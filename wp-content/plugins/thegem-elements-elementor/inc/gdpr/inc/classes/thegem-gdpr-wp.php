<?php

class TheGemGdprWP {

	CONST ID = 'wordpress-comments';
	public $options;

	public function init() {
		$this->get_options();
		if ($this->options && $this->options['enabled']) {
			add_filter('comment_form_submit_field', array($this, 'integration'), 999);
			add_action('pre_comment_on_post', array($this, 'on_post'));
		}
	}

	public function get_options() {
        $options = get_option(TheGemGdpr::OPTION_GROUP.'_'.TheGemGdpr::TYPE_INTEGRATIONS);
        if (is_string($options)) {
            $options = unserialize($options);
        }
        $this->options = $options ? $options[static::ID] : array();
	}

	public function integration($submit_field = '') {
		$field  = '<div class="thegem-gdpr-field">';
		$field .= '<input name="'.TheGemGdprIntegration::FIELD_NAME.'" type="checkbox" class="gem-checkbox">';
		$field .= '<label>'.$this->get_checkbox_text().'</label>';
		$field .= '</div>';
		echo $field . $submit_field;
	}

	private function get_checkbox_text() {
		if (empty($this->options['checkbox_text'])) {
			return TheGemGdprIntegration::get_default_checkbox_text();
		}
		$output = wp_kses(wp_unslash($this->options['checkbox_text']), TheGemGdpr::get_allowed_html_tags());
		return $output;
	}

	private function get_error_message() {
		if ($this->options['error_message']=='') {
			return TheGemGdprIntegration::get_default_error_message();
		}
		$output = esc_html($this->options['error_message']);
		return $output;
	}

	public function on_post() {
		if (!isset($_POST[TheGemGdprIntegration::FIELD_NAME])) {
			wp_die(
				'<p>'.sprintf(__('<strong>ERROR</strong>: %s'), $this->get_error_message()).'</p>',
				__('Comment Submission Failure', 'thegem'),
				array('back_link' => true)
			);
		}
	}


}