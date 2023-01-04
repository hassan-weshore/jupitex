<?php

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Optin_Field_Text extends Sellkit_Elementor_Optin_Field_Base {

	public static function get_field_type() {
		return 'text';
	}

	public function get_input_type() {
		return 'text';
	}

	public function render_content() {
		$attrs = $this->widget->get_render_attribute_string( 'field-' . $this->get_id() );

		?>
		<input <?php echo $attrs; ?>>
		<?php
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
