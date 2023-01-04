<?php

defined( 'ABSPATH' ) || die();

use Sellkit_Elementor_Optin_Module as Module;

class Sellkit_Elementor_Optin_Field_Select extends Sellkit_Elementor_Optin_Field_Base {

	public static function get_field_type() {
		return 'select';
	}

	public function get_input_type() {
		return 'select';
	}

	public function add_field_render_attribute() {
		parent::add_field_render_attribute();

		if ( 'true' === $this->field['multiple_selection'] ) {
			$this->widget->add_render_attribute( 'field-' . $this->get_id(), 'multiple' );

			if ( ! empty( $this->field['select_rows'] ) ) {
				$this->widget->add_render_attribute( 'field-' . $this->get_id(), 'size', $this->field['select_rows'] );
			}
		}
	}

	public function render_content() {
		$field   = $this->field;
		$options = preg_split( '/\\r\\n|\\r|\\n/', $field['field_options'], -1, PREG_SPLIT_NO_EMPTY );
		$attrs   = $this->widget->get_render_attribute_string( 'field-' . $this->get_id() );

		if ( empty( $options ) ) {
			return;
		}

		?>
		<div class="sellkit-field-subgroup">
			<?php if ( empty( $this->field['multiple_selection'] ) ) : ?>
				<div class="sellkit-field-select-arrow">
					<?php Module::render_icon( $this->widget->get_settings_for_display()['select_arrow_icon'] ); ?>
				</div>
			<?php endif ?>
			<select <?php echo $attrs; ?>>
				<?php $this->render_options( $options ); ?>
			</select>
		</div>
		<?php
	}

	private function render_options( $options ) {
		foreach ( $options as $key => $option ) {
			$option_id    = $this->get_id() . $key;
			$option_label = $option;
			$option_value = $option;

			if ( false !== strpos( $option, '|' ) ) {
				list( $option_label, $option_value ) = explode( '|', $option );
			}

			$option_args = [ 'value' => $option_value ];

			if ( $this->field['field_value'] === $option_value ) {
				$option_args['selected'] = 'selected';
			}

			$this->widget->add_render_attribute( $option_id, $option_args );

			?>
			<option <?php echo $this->widget->get_render_attribute_string( $option_id ); ?>>
				<?php echo esc_html( $option_label ); ?>
			</option>
			<?php
		}
	}

	public static function get_additional_controls() {
		$commons = parent::get_common_controls();

		return [
			'label' => $commons['label'],
			'field_value' => $commons['field_value'],
			'field_options' => [
				'label'      => esc_html__( 'Options', 'sellkit' ),
				'type'       => 'textarea',
				'default'    => '',
				'conditions' => [ 'terms' => [ parent::get_type_condition() ] ],
				'description' => esc_html__( 'Enter each option in a separate line. To differentiate between label and value, separate them with a pipe char ("|"). For example: First Name|f_name', 'sellkit' ),
			],
			'multiple_selection' => [
				'label'        => esc_html__( 'Multiple Selection', 'sellkit' ),
				'type'         => 'switcher',
				'return_value' => 'true',
				'conditions' => [ 'terms' => [ parent::get_type_condition() ] ],
			],
			'select_rows' => [
				'label'      => esc_html__( 'Rows', 'sellkit' ),
				'type'       => 'number',
				'default'    => 5,
				'conditions' => [
					'terms' => [
						parent::get_type_condition(),
						[
							'name'     => 'multiple_selection',
							'operator' => '===',
							'value'    => 'true',
						],
					],
				],
			],
			'required' => $commons['required'],
			'width_responsive' => $commons['width_responsive'],
		];
	}
}
