<?php

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Optin_Field_Time extends Sellkit_Elementor_Optin_Field_Base {

	public static function get_field_type() {
		return 'time';
	}

	public function get_input_type() {
		return $this->field['native_html5'] ? 'time' : 'text';
	}

	public function get_class() {
		return 'sellkit-field flatpickr';
	}

	public function get_style_depends() {
		return [ 'flatpickr' ];
	}

	public function get_script_depends() {
		return [ 'flatpickr' ];
	}

	public function add_field_render_attribute() {
		parent::add_field_render_attribute();

		$this->widget->add_render_attribute( 'field-' . $this->get_id(), [
			$this->field['native_html5'] ? 'min' : 'data-min-time' => $this->field['min_time'],
			$this->field['native_html5'] ? 'max' : 'data-max-time' => $this->field['max_time'],
			'data-enable-time' => 'true',
			'data-no-calendar' => 'true',
			'data-time_24hr' => 'true',
		] );
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
			'min_time' => [
				'label'          => esc_html__( 'Min Time', 'sellkit' ),
				'type'           => 'date_time',
				'label_block'    => false,
				'conditions' => [ 'terms' => [ parent::get_type_condition() ] ],
				'picker_options' => [
					'enableTime' => true,
					'noCalendar' => true,
					'time_24hr'  => true,
				],
			],
			'max_time' => [
				'label'          => esc_html__( 'Max Time', 'sellkit' ),
				'type'           => 'date_time',
				'label_block'    => false,
				'conditions' => [ 'terms' => [ parent::get_type_condition() ] ],
				'picker_options' => [
					'enableTime' => true,
					'noCalendar' => true,
					'time_24hr'  => true,
				],
			],
			'native_html5' => [
				'label'        => esc_html__( 'Native HTML5', 'sellkit' ),
				'type'         => 'switcher',
				'return_value' => 'true',
				'conditions' => [ 'terms' => [ parent::get_type_condition() ] ],
			],
			'required' => $commons['required'],
			'width_responsive' => $commons['width_responsive'],
		];
	}
}
