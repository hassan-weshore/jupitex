<?php

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Optin_Field_Textarea extends Sellkit_Elementor_Optin_Field_Base {

	public static function get_field_type() {
		return 'textarea';
	}

	public function get_input_type() {
		return 'textarea';
	}

	public function add_field_render_attribute() {
		parent::add_field_render_attribute();

		$this->widget->add_render_attribute( 'field-' . $this->get_id(), 'rows', $this->field['rows'] );
	}

	public function render_content() {
		$attrs = $this->widget->get_render_attribute_string( 'field-' . $this->get_id() );

		?>
		<textarea <?php echo $attrs; ?>><?php echo $this->field['field_value']; ?></textarea>
		<?php
	}

	public static function get_additional_controls() {
		$commons = parent::get_common_controls();

		return [
			'label' => $commons['label'],
			'field_value' => $commons['field_value'],
			'rows' => [
				'label'      => esc_html__( 'Rows', 'sellkit' ),
				'type'       => 'number',
				'default'    => 5,
				'conditions' => [ 'terms' => [ parent::get_type_condition() ] ],
			],
			'required' => $commons['required'],
			'width_responsive' => $commons['width_responsive'],
		];
	}
}
