<?php

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Optin_Field_Email extends Sellkit_Elementor_Optin_Field_Text {

	public static function get_field_type() {
		return 'email';
	}

	public function get_input_type() {
		return 'email';
	}

	public static function get_additional_controls() {
		$commons = parent::get_common_controls();

		return [
			'label' => $commons['label'],
			'field_value' => $commons['field_value'],
			'placeholder' => $commons['placeholder'],
			'required' => $commons['required'],
			'width_responsive' => $commons['width_responsive'],
		];
	}
}
