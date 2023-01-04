<?php

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Optin_Field_Number extends Sellkit_Elementor_Optin_Field_Text {

	public static function get_field_type() {
		return 'number';
	}

	public function get_input_type() {
		return 'number';
	}

	public function add_field_render_attribute() {
		parent::add_field_render_attribute();

		if ( ! empty( $this->field['min'] ) ) {
			$this->widget->add_render_attribute( 'field-' . $this->get_id(), 'min', $this->field['min'] );
		}

		if ( ! empty( $this->field['max'] ) ) {
			$this->widget->add_render_attribute( 'field-' . $this->get_id(), 'max', $this->field['max'] );
		}
	}

	public static function get_additional_controls() {
		$commons = parent::get_common_controls();

		return [
			'label' => $commons['label'],
			'field_value' => $commons['field_value'],
			'placeholder' => $commons['placeholder'],
			'min' => [
				'label'     => esc_html__( 'Min Value', 'sellkit' ),
				'type'      => 'number',
				'conditions' => [ 'terms' => [ parent::get_type_condition() ] ],
			],
			'max' => [
				'label'     => esc_html__( 'Max Value', 'sellkit' ),
				'type'      => 'number',
				'conditions' => [ 'terms' => [ parent::get_type_condition() ] ],
			],
			'required' => $commons['required'],
			'width_responsive' => $commons['width_responsive'],
		];
	}
}
