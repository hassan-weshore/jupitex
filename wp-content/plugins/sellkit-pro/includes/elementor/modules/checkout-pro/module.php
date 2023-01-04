<?php

defined( 'ABSPATH' ) || die();

use Sellkit_Pro\Elementor\Base\Sellkit_Elementor_Base_Module;

class Sellkit_Elementor_Checkout_Pro_Module extends Sellkit_Elementor_Base_Module {
	/**
	 * Add custom role field to checkout shipping & billing field.
	 *
	 * @param array $field checkout field type array.
	 * @since 1.1.0
	 * @return array
	 */
	public static function checkout_custom_fields( $fields ) {
		$fields['custom_role'] = esc_html__( 'Custom Field', 'sellkit-pro' );

		return $fields;
	}

	/**
	 * Add google address autocomplete to checkout.
	 *
	 * @param array $settings checkout widget settings.
	 * @since 1.1.0
	 * @return void
	 */
	public static function checkout_google_autocomplete_address( $settings ) {
		if ( ! array_key_exists( 'active_google_autocomplete', $settings ) || 'yes' !== $settings['active_google_autocomplete'] ) {
			return;
		}

		$api = get_option( 'sellkit' );

		if ( empty( $api ) || ! array_key_exists( 'google_api_key', $api ) ) {
			return;
		}

		$api = $api['google_api_key'];

		if ( empty( $api ) ) {
			return;
		}

		?>
			<script>
				function sellkitCheckoutInitAutocomplete() {
					shippingAutoField = document.querySelector( '#shipping_address_1' );
					billingAutoField = document.querySelector( '#billing_address_1' );

					if ( shippingAutoField ) {
						shippingAutocomplete = new google.maps.places.Autocomplete( shippingAutoField, {
							fields: [ 'address_components', 'geometry' ],
							types: [ 'address' ],
						});

						shippingAutocomplete.addListener( 'place_changed', function() {
							<?php if ( 'yes' === $settings['google_autopopulate_state'] ) : ?>
							sellkitCheckoutFillInAddress( 'shipping' )
							<?php else : ?>
							return;
							<?php endif; ?>
						} );
					}

					if ( billingAutoField ) {
						billingAutocomplete = new google.maps.places.Autocomplete( billingAutoField, {
							fields: [ 'address_components', 'geometry' ],
							types: [ 'address' ],
						});

						// When the user selects an address from the drop-down, populate the
						// address fields in the form.
						billingAutocomplete.addListener( 'place_changed', function() {
							<?php if ( 'yes' === $settings['google_autopopulate_state'] ) : ?>
							sellkitCheckoutFillInAddress( 'billing' )
							<?php else : ?>
							return;
							<?php endif; ?>
						} );
					}
				}

				function sellkitCheckoutFillInAddress( type ) {
					// Get the place details from the autocomplete object.
					var place;
					if ( type == 'shipping' ) {
						place = shippingAutocomplete.getPlace();
					} else {
						place = billingAutocomplete.getPlace();
					}

					let address1 = '';
					let address2 = '';
					let state = '';
					let country = '';
					let city = '';
					let postcode = '';

					// Get each component of the address from the place details,
					// and then fill-in the corresponding field on the form.
					// place.address_components are google.maps.GeocoderAddressComponent objects
					// which are documented at http://goo.gle/3l5i5Mr.
					for ( const component of place.address_components) {
						const componentType = component.types[0];

						switch (componentType) {
							case "country":
								country = component.short_name;
								break;
							case 'administrative_area_level_1':
								state = component.short_name;
								break;
							case "postal_code":
								postcode = component.short_name;
								break;
							case 'administrative_area_level_2':
								//address1 = component.short_name;
								break;
							case 'locality':
								city = component.short_name;
								break;
							case 'street_number':
								if ( '' === address1 ) {
									address2 += component.long_name;
								} else {
									address1 += ' ' + component.long_name;
								}
								break;
							case 'route':
								if ( '' === address1 ) {
									address1 += component.long_name;
								} else {
									address1 += ' ' + component.long_name;
								}
								break;
							case 'postal_code_suffix':
								postcode = component.long_name + postcode;
								break
						}
					}

					$( '#' + type + '_country' ).val( country ).trigger( 'change' );
					$( '#' + type + '_address_2' ).val( address2 );
					$( '#' + type + '_address_1' ).val( address1 );
					$( '#' + type + '_postcode' ).val( postcode );
					$( '#' + type + '_city' ).val( city );
					$( '#sellkit-' + type + '_state' ).val( state ).trigger( 'change' );
				}
			</script>
			<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $api; ?>&callback=sellkitCheckoutInitAutocomplete&libraries=places&v=weekly" async ></script><?php //phpcs:ignore ?>
		<?php
	}

	/**
	 * Adds express checkout control to checkout widget.
	 *
	 * @param object $widget checkout widget.
	 * @return void
	 * @since 1.1.0
	 */
	public static function express_checkout_control( $widget ) {
		$widget->add_control(
			'show_express_checkout',
			[
				'label'        => esc_html__( 'Express Checkout', 'sellkit-pro' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Show', 'sellkit-pro' ),
				'label_off'    => esc_html__( 'Hide', 'sellkit-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);
	}

	/**
	 * Add google address autocomplete to checkout
	 *
	 * @param object $widget checkout widget.
	 * @return void
	 * @since 1.1.0
	 */
	public static function google_address_autocomplete( $widget ) {
		$widget->start_controls_section(
			'google_autocomplete_settings',
			[
				'label' => esc_html__( 'Address Autocomplete', 'sellkit-pro' ),
			]
		);

		$widget->add_control(
			'active_google_autocomplete',
			[
				'label'        => esc_html__( 'Google Address Autocomplete', 'sellkit-pro' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Enable', 'sellkit-pro' ),
				'label_off'    => esc_html__( 'Disable', 'sellkit-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		// phpcs:disable
		$widget->add_control(
			'important_note',
			[
				'type' =>'raw_html',
				/** Translators: %1$s : <br> tag %2$s : <b> tag %3$s : </b> tag */
				'raw' =>
				sprintf(
					esc_html__(
						'Display automated address suggestions to your customers as they are entering their shipping/billing address. %1$s
						You need to obtain a %2$s Google Places API %3$s to be able to use this feature.
						%4$s Click here %5$s to add your API to Sellkit settings page', 'sellkit-pro'
					),
					'<br>',
					'<b>',
					'</b>',
					'<a href="' . admin_url( 'admin.php?page=sellkit-settings#/' ) . '" target="_blank">',
					'</a>'
				),
				'content_classes' => 'elementor-control-field-description',
			]
		);
		// phpcs:enable

		$widget->add_control(
			'google_autopopulate_state',
			[
				'label'        => esc_html__( 'Auto-populate State', 'sellkit-pro' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Enable', 'sellkit-pro' ),
				'label_off'    => esc_html__( 'Disable', 'sellkit-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$widget->add_control(
			'state_lookup_by_postcode',
			[
				'label'        => esc_html__( 'State And City Lookup By Postcode', 'sellkit-pro' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Enable', 'sellkit-pro' ),
				'label_off'    => esc_html__( 'Disable', 'sellkit-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$widget->end_controls_section();
	}
}
