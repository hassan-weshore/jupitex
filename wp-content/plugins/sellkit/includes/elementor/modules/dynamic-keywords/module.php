<?php

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Dynamic_Keywords_Module extends Sellkit_Elementor_Base_Module {
	public function __construct() {
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'insert_modal_file_to_footer' ] );
		add_action( 'elementor/editor/init', array( $this, 'sellkit_register_widget_message' ) );

		$this->insert_tags_file();
	}

	/**
	 * Get page template fiter callback for elementor preview mode
	 *
	 * @return string
	 */
	public function sellkit_register_widget_message() {
		add_action( 'wp_footer', [ $this, 'sellkit_print_inline_script' ], 9999 );
	}

	//Add note to top of element.
	public function sellkit_print_inline_script() {

		?>
		<script>
			( function ( $ ) {
				"use strict";

				var sellkitSupportWidgets =<?php echo wp_json_encode( $this->sellkit_get_merge_tags_supported_widgets() ); ?>;

				elementor.hooks.addAction('panel/open_editor/widget', function ( panel, model, view ) {
					if ( sellkitSupportWidgets.indexOf( model.get( 'widgetType') ) === -1 ) {
						return;
					}

					var html = '<div class="sellkit-customize-note">' +
						'<div class="elementor-panel-alert elementor-panel-alert-info"><?php esc_html_e( 'You can also add personalization tags to this element using shortcodes. ', 'sellkit' ); ?><a style="text-decoration: underline;" href="javascript:void(0)"><?php esc_html_e( 'Click here to show the available shortcodes', 'sellkit' ); ?></a></div>'
						'</div>';
					$ ( '.elementor-panel-navigation' ).eq( 0 ).after( html );

					$( '.sellkit-customize-note a' ).on( 'click', function( e ) {
						$( '.sellkit-popup-box' ).addClass( 'sellkit-popup-active' );
						$( '.sellkit-popup-box' ).removeClass( 'sellkit-popup-dark-theme sellkit-popup-light-theme sellkit-popup-auto-theme' );

						var uiTheme = elementor.settings.editorPreferences.model.get( 'ui_theme' );

						$( '.sellkit-popup-box' ).addClass( `sellkit-popup-${uiTheme}-theme` );

						e.stopPropagation();
					} );

					$( '.sellkit-close-button' ).on( 'click', function() {
						$( '.sellkit-popup-box' ).removeClass( 'sellkit-popup-active' );
					} );

					$( document ).on( 'click', function( e ) {
						if ( ! $( e.target ).closest( '.sellkit-popup-list' ).length )  {
							$( '.sellkit-popup-box' ).removeClass( 'sellkit-popup-active' );
						}
					});

					$( '.sellkit-popup-box button' ).on( 'click', function() {
						var inputeId = $( this ).attr( 'value' );
						var copyText = document.getElementById( inputeId );
						copyText.select();
						copyText.setSelectionRange( 0, 99999 );
						document.execCommand( "copy" );
					} );
				} );

			} )( jQuery );
		</script>
		<?php
	}

	/**
	 * Add supported widgets.
	 *
	 * @return array
	 */
	public function sellkit_get_merge_tags_supported_widgets() {
		return [ 'heading', 'text-editor', 'google_maps', 'raven-heading', 'sellkit-order-details' ];
	}

	/**
	 * Include modal file.
	 *
	 * @since 1.1.0
	 */
	public function insert_modal_file_to_footer() {
		include_once 'template/modal.php';
	}

	/**
	 * Include tags file.
	 *
	 * @since 1.1.0
	 */
	public function insert_tags_file() {
		include_once 'tags-list/tags.php';
	}

}
