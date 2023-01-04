<?php

defined( 'ABSPATH' ) || die();

use Sellkit_Elementor_Optin_Tab_Content as Content_Controls;
use Sellkit_Elementor_Optin_Tab_Style as Style_Controls;
use Sellkit_Elementor_Optin_Module as Module;
use Elementor\Plugin as Elementor;

class Sellkit_Elementor_Optin_Widget extends Sellkit_Elementor_Base_Widget {

	public function get_name() {
		return 'sellkit-optin';
	}

	public function get_title() {
		return esc_html__( 'Opt-In', 'sellkit' );
	}

	public function get_icon() {
		return 'sellkit-element-icon sellkit-optin-icon';
	}

	protected function register_controls() {
		new Content_Controls( $this );
		new Style_Controls( $this );
	}

	protected function render() {
		$settings    = $this->get_settings_for_display();
		$fields      = $settings['fields'];
		$has_address = false;

		$attr_messages = '';
		if ( 'yes' === $settings['messages_custom'] ) {
			$messages = esc_attr( wp_json_encode( [
				'error'    => esc_attr( $settings['messages_error'] ),
				'required' => esc_attr( $settings['messages_required'] ),
			] ) );

			$attr_messages = "data-messages=\"{$messages}\"";
		}

		?>
		<form
			novalidate
			method="POST"
			name="optin-<?php echo $this->get_id(); ?>"
			onsubmit="(e)=>e.preventDefault()"
			class="sellkit-optin sellkit-flex sellkit-flex-wrap sellkit-flex-bottom"
			<?php echo $attr_messages; ?>>
			<input type="hidden" name="post_id" value="<?php echo self::get_current_post_id(); ?>" />
			<input type="hidden" name="form_id" value="<?php echo $this->get_id(); ?>" />
			<?php

			foreach ( $fields as $field ) {
				if ( 'address' === $field['type'] ) {
					$has_address = true;
				}

				Module::render_field( $this, $field );
			}

			?>
			<div <?php echo $this->button_wrapper_attributes( $settings ); ?>>
				<button type="submit" class="sellkit-submit-button">
					<?php Module::render_icon( $settings['submit_button_icon'] ); ?>
					<div class="sellkit-submit-button-texts">
						<span class="sellkit-submit-button-text">
							<?php echo esc_html( $settings['submit_button_text'] ); ?>
						</span>
						<span class="sellkit-submit-button-subtext">
							<?php echo esc_html( $settings['submit_button_subtext'] ); ?>
						</span>
					</div>
				</button>
			</div>
		</form>
		<?php

		if ( $has_address ) {
			$this->enqueue_google_places_api();
		}
	}

	private function button_wrapper_attributes( $settings ) {
		$this->add_render_attribute(
			'button-wrapper',
			'class',
			'sellkit-field-group sellkit-field-type-submit-button elementor-column elementor-col-' . $settings['submit_button_width']
		);

		// We add responsive widths as css variables and apply them according to "data-elementor-device-mode" attribute of <body> element.
		foreach ( Module::get_active_breakpoints() as $device => $value ) {
			if ( 'desktop' !== $device && empty( $settings[ "submit_button_width_{$device}" ] ) ) {
				continue;
			}

			$setting_key = 'desktop' === $device ? 'submit_button_width' : "submit_button_width_{$device}";

			$this->add_render_attribute(
				'button-wrapper',
				'style',
				"--sellkit-field-width-{$device}:" . $settings[ $setting_key ] . '%;'
			);
		}

		return $this->get_render_attribute_string( 'button-wrapper' );
	}

	private static function get_current_post_id() {
		if ( isset( Elementor::$instance->documents ) && ! empty( Elementor::$instance->documents->get_current() ) ) {
			return Elementor::$instance->documents->get_current()->get_main_id();
		}

		return get_the_ID();
	}

	private function enqueue_google_places_api() {
		$options = get_option( 'sellkit' );

		if ( empty( $options ) || empty( $options['google_api_key_optin'] ) ) {
			return;
		}

		$api_key = $options['google_api_key_optin'];

		wp_enqueue_script(
			'sellkit_google_places_api',
			"https://maps.googleapis.com/maps/api/js?key={$api_key}&libraries=places",
			[],
			'1.0.0',
			false
		);
	}

}
