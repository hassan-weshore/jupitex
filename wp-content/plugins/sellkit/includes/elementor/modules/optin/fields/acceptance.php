<?php

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Optin_Field_Acceptance extends Sellkit_Elementor_Optin_Field_Base {

	public static function get_field_type() {
		return 'acceptance';
	}

	public function get_input_type() {
		return 'checkbox';
	}

	public function add_field_render_attribute() {
		parent::add_field_render_attribute();

		if ( 'true' === $this->field['checked_by_default'] ) {
			$this->widget->add_render_attribute( 'field-' . $this->get_id(), 'checked' );
		}
	}

	public function render_content() {
		$attrs = $this->widget->get_render_attribute_string( 'field-' . $this->get_id() );

		?>
		<div class="sellkit-field-subgroup">
			<span class="sellkit-field-option sellkit-field-option-checkbox">
				<input <?php echo $attrs; ?>>
				<label
					for="optin-field-<?php echo $this->get_id(); ?>"
					class="sellkit-field-label">
					<?php echo $this->field['acceptance_text']; ?>
					<?php if ( isset( $this->field['required'] ) && 'true' === $this->field['required'] ) : ?>
						<span class="required-mark-label"></span>
					<?php endif ?>
				</label>
			</span>
		</div>
		<?php
	}

	public static function get_additional_controls() {
		$commons = parent::get_common_controls();

		return [
			'label' => $commons['label'],
			'acceptance_text' => [
				'label'     => esc_html__( 'Acceptance Text', 'sellkit' ),
				'default'   => esc_html__( 'I agree to terms.', 'sellkit' ),
				'type'      => 'textarea',
				'conditions' => [ 'terms' => [ parent::get_type_condition() ] ],
			],
			'checked_by_default' => [
				'label'        => esc_html__( 'Checked by Default', 'sellkit' ),
				'type'         => 'switcher',
				'return_value' => 'true',
				'conditions' => [ 'terms' => [ parent::get_type_condition() ] ],
			],
			'required' => $commons['required'],
			'width_responsive' => $commons['width_responsive'],
		];
	}
}
