<?php

defined( 'ABSPATH' ) || die();

use Sellkit_Elementor_Optin_Module as Module;

/**
 * An abstract class to register new optin field.
 *
 * @since 1.5.0
 * @abstract
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexitys)
 */
abstract class Sellkit_Elementor_Optin_Field_Base {

	/**
	 * Optin widget.
	 *
	 * Holds the optin widget instance.
	 *
	 * @access public
	 *
	 * @var object
	 */
	public $widget;

	/**
	 * Optin form field.
	 *
	 * Holds all the fields attributes.
	 *
	 * @access public
	 *
	 * @var array
	 */
	public $field;

	/**
	 * Depended scripts.
	 *
	 * Holds all the element depended scripts to enqueue.
	 *
	 * @since 1.5.0
	 * @access private
	 *
	 * @var array
	 */
	private $depended_scripts = [];

	/**
	 * Depended styles.
	 *
	 * Holds all the element depended styles to enqueue.
	 *
	 * @since 1.5.0
	 * @access private
	 *
	 * @var array
	 */
	private $depended_styles = [];

	public function __construct() {
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles' ] );
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Ready made array of type condition to be used in childs.
	 *
	 * @return array
	 *
	 * @since 1.5.0
	 * @access public
	 * @static
	 * @final
	 */
	final public static function get_type_condition() {
		return [
			'name'     => 'type',
			'operator' => 'in',
			'value'    => [ static::get_field_type() ],
		];
	}

	/**
	 * Get field ID.
	 *
	 * Retrieve the field type.
	 *
	 * @return string Field ID.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function get_id() {
		return $this->field['_id'];
	}

	/**
	 * Get input type of the field.
	 *
	 * The type that appears inside \<input\> tag.
	 * By default it equals the name of field type, except in select, acceptance etc. which should be overridden.
	 *
	 * @return string Field type.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function get_input_type() {
		return $this->field['type'];
	}

	/**
	 * Get the real name of field type.
	 *
	 * @return string Field type.
	 *
	 * @since 1.5.0
	 * @access public
	 * @static
	 * @abstract
	 */
	abstract public static function get_field_type();

	/**
	 * Get field class.
	 *
	 * @return string Field class.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function get_class() {
		return 'sellkit-field';
	}

	/**
	 * Retrieve the list of script dependencies the element requires.
	 *
	 * @return array Element scripts dependencies.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function get_script_depends() {
		return $this->depended_scripts;
	}

	/**
	 * Registers all the scripts defined as element dependencies and enqueues
	 * them. Use `get_script_depends()` method to add custom script dependencies.
	 *
	 * @since 1.5.0
	 * @access public
	 * @final
	 */
	final public function enqueue_scripts() {
		foreach ( $this->get_script_depends() as $script ) {
			wp_enqueue_script( $script );
		}

	}

	/**
	 * Retrieve the list of style dependencies the element requires.
	 *
	 * @return array Element styles dependencies.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function get_style_depends() {
		return $this->depended_styles;
	}

	/**
	 * Registers all the styles defined as element dependencies and enqueues
	 * them. Use `get_style_depends()` method to add custom style dependencies.
	 *
	 * @since 1.5.0
	 * @access public
	 * @final
	 */
	final public function enqueue_styles() {
		foreach ( $this->get_style_depends() as $style ) {
			wp_enqueue_style( $style );
		}
	}

	/**
	 * Render field label and content.
	 *
	 * @param object $widget Widget instance.
	 * @param array  $field  Field.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function render( $widget, $field ) {
		$this->widget = $widget;
		$this->field  = $field;

		$this->add_field_render_attribute();
		$this->add_field_group_render_attribute();

		?>
		<div <?php echo $this->widget->get_render_attribute_string( 'field-group-' . $this->get_id() ); ?>>
			<?php
			$this->render_label();
			$this->render_content();
			?>
		</div>
		<?php
	}

	/**
	 * Add render attributes for each field based on the settings.\
	 * Some field classes may override it if they have additional attributes.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function add_field_render_attribute() {
		$attributes = [
			'type'        => $this->get_input_type(),
			'name'        => 'fields[' . $this->get_id() . ']',
			'id'          => 'optin-field-' . $this->get_id(),
			'class'       => $this->get_class(),
			'data-type'   => $this->field['type'],
		];

		if ( isset( $this->field['required'] ) && 'true' === $this->field['required'] ) {
			$attributes['required'] = 'required';
		}

		if ( ! empty( $this->field['placeholder'] ) ) {
			$attributes['placeholder'] = $this->field['placeholder'];
		}

		if (
			! empty( $this->field['field_value'] ) &&
			! in_array( $this->get_input_type(), [ 'textarea', 'select' ], true )
		) {
			$attributes['value'] = $this->field['field_value'];
		}

		$this->widget->add_render_attribute( 'field-' . $this->get_id(), $attributes );
	}

	/**
	 * Add render attributes div wrapper of the field.\
	 * Handles the responsive "width" control by adding a CSS var.
	 *
	 * @since 1.5.0
	 * @access public
	 * @final
	 */
	final public function add_field_group_render_attribute() {

		$this->widget->add_render_attribute(
			'field-group-' . $this->get_id(),
			[
				'id'    => 'sellkit-field-group-' . $this->get_id(),
				'class' => 'sellkit-flex-wrap sellkit-field-group sellkit-field-type-' . $this->get_field_type() . ' elementor-column elementor-col-' . $this->field['width'],
			]
		);

		// We add responsive widths as css variables and apply them according to "data-elementor-device-mode" attribute of <body> element.
		foreach ( Module::get_active_breakpoints() as $device => $value ) {
			if ( 'desktop' !== $device && empty( $this->field[ "width_{$device}" ] ) ) {
				continue;
			}

			$setting_key = 'desktop' === $device ? 'width' : "width_{$device}";

			$this->widget->add_render_attribute(
				'field-group-' . $this->get_id(),
				'style',
				"--sellkit-field-width-{$device}:" . $this->field[ $setting_key ] . '%;'
			);
		}
	}

	/**
	 * Render the label for each field.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function render_label() {
		$settings = $this->widget->get_settings_for_display();

		if (
			empty( $this->field['label'] ) ||
			'yes' !== $settings['label'] ||
			in_array( $this->get_field_type(), [ 'hidden', 'acceptance' ], true )
		) {
			return;
		}

		?>
		<label
			for="optin-field-<?php echo $this->get_id(); ?>"
			class="sellkit-field-label">
			<?php echo $this->field['label']; ?>
			<?php if ( isset( $this->field['required'] ) && 'true' === $this->field['required'] ) : ?>
				<span class="required-mark-label"></span>
			<?php endif ?>
		</label>
		<?php
	}

	/**
	 * Render the field content.
	 *
	 * @since 1.5.0
	 * @access public
	 * @abstract
	 */
	abstract public function render_content();

	/**
	 * Ready made array of controls that are common among many fields, to be used in childs.
	 *
	 * @return array
	 *
	 * @since 1.5.0
	 * @access public
	 * @static
	 * @final
	 */
	final public static function get_common_controls() {
		return [
			'label' => [
				'label' => esc_html__( 'Label', 'sellkit' ),
				'type'  => 'text',
				'conditions' => [ 'terms' => [ static::get_type_condition() ] ],
			],
			'field_value' => [
				'label'      => esc_html__( 'Default Value', 'sellkit' ),
				'type'       => 'text',
				'dynamic'    => [ 'active' => true ],
				'conditions' => [ 'terms' => [ static::get_type_condition() ] ],
			],
			'placeholder' => [
				'label'      => esc_html__( 'Placeholder', 'sellkit' ),
				'type'       => 'text',
				'conditions' => [ 'terms' => [ static::get_type_condition() ] ],
			],
			'required' => [
				'label'        => esc_html__( 'Required', 'sellkit' ),
				'type'         => 'switcher',
				'return_value' => 'true',
				'conditions' => [ 'terms' => [ static::get_type_condition() ] ],
			],
			'width_responsive' => [
				'label'      => esc_html__( 'Column Width', 'sellkit' ),
				'type'       => 'select',
				'default'    => '100',
				'conditions' => [ 'terms' => [ static::get_type_condition() ] ],
				'options'    => [
					'100' => '100%',
					'80'  => '80%',
					'75'  => '75%',
					'66'  => '66%',
					'60'  => '60%',
					'50'  => '50%',
					'40'  => '40%',
					'33'  => '33%',
					'25'  => '25%',
					'20'  => '20%',
				],
			],
		];
	}

	/**
	 * Get array of field controls params. Child classes must override it
	 * and to include common controls, they must use "get_common_controls"
	 * method inside this method.
	 *
	 * ►For responsive controls, please add a "_responsive" suffix to the name of control.\
	 * ►For group controls, please add a "_group" suffix to the name of control.
	 *
	 * @return array
	 *
	 * @since 1.5.0
	 * @access public
	 * @static
	 * @abstract
	 */
	abstract public static function get_additional_controls();

	/**
	 * Get final set of controls, ready to feed to field repeater's add control functions.
	 *
	 * @return array
	 *
	 * @since 1.5.0
	 * @access public
	 * @static
	 * @final
	 */
	final public static function get_fields_controls() {
		$controls    = [];
		$sort_helper = [];

		foreach ( Module::$field_types as $type_class ) {
			$controls = self::merge_controls( $controls, $type_class::get_additional_controls(), $sort_helper );
		}

		$controls = self::get_sorted_controls( $controls, $sort_helper );

		return $controls;
	}

	// From here on, methods are private and are only used in
	// process of preparing array of fields controls.
	// phpcs:ignore
	private static function merge_controls( array $accumulative, array $new_array, &$sort_helper ) {
		$sort_helper[] = [];

		foreach ( $new_array as $control => $props ) {
			$sort_helper[ count( $sort_helper ) - 1 ][] = $control;

			if ( ! array_key_exists( $control, $accumulative ) ) {
				$accumulative[ $control ] = $props;
				continue;
			}

			// Merge conditions.
			$accum_type_key    = array_search( 'type', array_column( $accumulative[ $control ]['conditions']['terms'], 'name' ), true );
			$accum_type_values = $accumulative[ $control ]['conditions']['terms'][ $accum_type_key ]['value'];

			$new_type_key   = array_search( 'type', array_column( $props['conditions']['terms'], 'name' ), true );
			$new_type_value = $props['conditions']['terms'][ $new_type_key ]['value'];

			$accumulative
				[ $control ]
				['conditions']
				['terms']
				[ $accum_type_key ]
				['value'] = array_merge( $accum_type_values, $new_type_value );
		}

		return $accumulative;
	}

	private static function get_sorted_controls( $controls, $sort_helper ) {
		$unique = array_unique( self::flatten( $sort_helper ) );

		self::sort_recursive( $unique, $sort_helper );

		foreach ( $unique as $control ) {
			$result[ $control ] = $controls[ $control ];
		}

		return $result;
	}

	private static function sort_recursive( &$uniques, $sort_helper ) {
		foreach ( $uniques as $rank => $control ) {

			foreach ( $sort_helper as $group ) {

				$pos = array_search( $control, $group, true );

				if ( false === $pos ) {
					continue;
				}

				foreach ( $group as $control_pos => $control_key ) {

					$comp_rank = array_search( $control_key, $uniques, true );

					if ( ( $pos < $control_pos && $rank > $comp_rank ) ) {
						list( $uniques[ $rank ], $uniques[ $comp_rank ] ) = array( $uniques[ $comp_rank ], $uniques[ $rank ] );
						return self::sort_recursive( $uniques, $sort_helper );
					}
				}
			}
		}
	}

	private static function flatten( array $array ) {
		$return = array();
		array_walk_recursive( $array, function( $a ) use ( &$return ) {
			$return[] = $a;
		} );

		return $return;
	}
}
