jQuery(function($){
	setup_image_swatch_fields();
	setup_color_swatch_fields();

	$( '.artbees-form-field' ).each( function( index, field ) {
		if ( $( field ).data( 'conditional' ) !== $('#attribute_type').val() ) {
			return;
		}

		$( field ).find( 'input' ).removeAttr( 'disabled' );
		$( field ).find( 'select' ).removeAttr( 'disabled' );
		$( field ).addClass( 'active' );
	} );

	$( '#attribute_type' ).on( 'change', function () {
		var optionSelected = $( 'option:selected', this );
		var valueSelected = this.value;

		$( '.artbees-form-field' ).removeClass( 'active' );
		$( '.artbees-form-field' ).find( 'input' ).attr( 'disabled', 'disabled' );
		$( '.artbees-form-field' ).find( 'select' ).attr( 'disabled', 'disabled' );

		$( '.artbees-form-field' ).each( function( index, field ) {
			if ( $( field ).data( 'conditional' ) !== valueSelected ) {
				return;
			}

			$( field ).addClass( 'active' );
			$( field ).find( 'input' ).removeAttr( 'disabled' );
			$( field ).find( 'select' ).removeAttr( 'disabled' );
		} )
	} );

	$( '.artbees-was-select' ).on( 'change', function ( event ) {
		if ( typeof $( event.currentTarget ).data( 'is-parent' ) === 'undefined' ) {
			return;
		}

		var optionSelected = $( 'option:selected', this );
		var valueSelected = this.value;

		$( '.artbees-form-field' ).each( function( index, field ) {
			if ( typeof $( field ).data( 'parent' ) === 'undefined' ) {
				return;
			}

			$( field ).removeClass( 'active' );

			if (
				valueSelected > 0 &&
				$( event.currentTarget ).parents( '.artbees-form-field' ).data( 'conditional' ) === $( field ).data( 'conditional' )
			) {
				$( field ).addClass( 'active' );
			}
		} )
	} );

	// Product Edit Page Types Control
	$( '.artbees-was-attribute-wrapper' ).each( function( index, wrapper ) {
		$( wrapper ).find( '.artbees-form-field' ).each( function( index, field ) {
			if (
				$( field ).data( 'conditional' ) === $( wrapper ).find('#product_attribute_type').val() &&
				$( field ).parents( '.artbees-was-attribute-wrapper' ).data( 'taxonomy' ) === $( field ).data( 'taxonomy' )
			) {
				$( field ).addClass( 'active' );
			}
		} );

		$( wrapper ).find( '#product_attribute_type' ).on( 'change', function () {
			var optionSelected = $( 'option:selected', this );
			var valueSelected = this.value;

			$( wrapper ).find( '.artbees-form-field' ).removeClass( 'active' );
			$( wrapper ).find( '.artbees-form-field' ).find( 'input' ).attr( 'disabled', 'disabled' );
			$( wrapper ).find( '.artbees-form-field' ).find( 'select' ).attr( 'disabled', 'disabled' );

			$( wrapper ).find( '.artbees-form-field' ).each( function( index, field ) {
				if ( $( field ).data( 'conditional' ) !== valueSelected ) {
					return;
				}

				$( field ).addClass( 'active' );
				$( field ).find( 'input' ).removeAttr( 'disabled' );
				$( field ).find( 'select' ).removeAttr( 'disabled' );
			} );

			const taxonomy  = $( wrapper ).data( 'taxonomy' );
			const produtcId = $( wrapper ).data( 'product-id' );
			get_product_terms_options( valueSelected, wrapper, taxonomy, produtcId );
		} );

		$( wrapper ).find( '.artbees-was-product-select' ).on( 'change', function ( event ) {
			if ( typeof $( event.currentTarget ).data( 'is-parent' ) === 'undefined' ) {
				return;
			}

			var optionSelected = $( 'option:selected', this );
			var valueSelected = this.value;

			$( wrapper ).find( '.artbees-form-field' ).each( function( index, field ) {
				if ( typeof $( field ).data( 'parent' ) === 'undefined' ) {
					return;
				}

				$( field ).removeClass( 'active' );

				if (
					valueSelected > 0 &&
					$( event.currentTarget ).parents( '.artbees-form-field' ).data( 'conditional' ) === $( field ).data( 'conditional' ) &&
					$( wrapper ).data( 'taxonomy' ) === $( field ).data( 'taxonomy' )
				) {
					$( field ).addClass( 'active' );
				}
			} )
		} );
	} );

	// Image Upload
	function setup_image_swatch_fields() {

		// Uploading files
		var file_frame;

		$( '.artbees-was-image-picker__upload' ).on( 'click', function( event ) {

			event.preventDefault();

			var $image_swatch_upload = $( this ),
				$image_swatch_wrapper = $image_swatch_upload.closest( '.artbees-was-image-picker' ),
				$image_swatch_field = $image_swatch_wrapper.find( '.artbees-was-image-picker__field' ),
				$image_swatch_preview = $image_swatch_wrapper.find( '.artbees-was-image-picker__preview' ),
				$image_swatch_remove = $image_swatch_wrapper.find( '.artbees-was-image-picker__remove' );

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media( {
				title: $( this ).data( 'title' ),
				button: {
					text: $( this ).data( 'button-text' ),
				},
				multiple: false  // Set to true to allow multiple files to be selected
			} );

			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {

				// We set multiple to false so only get one image from the uploader
				attachment = file_frame.state().get( 'selection' ).first().toJSON();
				attachment_url = typeof attachment.sizes.thumbnail !== "undefined" ? attachment.sizes.thumbnail.url : attachment.url;

				$image_swatch_field.val( attachment.id );
				$image_swatch_preview.html( '<img src="' + attachment_url + '" class="attachment-thumbnail size-thumbnail">' );
				$image_swatch_upload.addClass( 'artbees-was-image-picker__upload--edit' );
				$image_swatch_remove.show();

			} );

			// Finally, open the modal
			file_frame.open();

		} );

		$( '.artbees-was-image-picker__remove' ).on( 'click', function( event ) {

			event.preventDefault();

			var $image_swatch_wrapper = $( this ).closest( '.artbees-was-image-picker' ),
				$image_swatch_field = $image_swatch_wrapper.find( '.artbees-was-image-picker__field' ),
				$image_swatch_preview = $image_swatch_wrapper.find( '.artbees-was-image-picker__preview' ),
				$image_swatch_upload = $image_swatch_wrapper.find( '.artbees-was-image-picker__upload' );

			$image_swatch_field.val( '' );
			$image_swatch_preview.html( '' );
			$image_swatch_upload.removeClass( 'artbees-was-image-picker__upload--edit' );
			$( this ).hide();

		} );
	}

	function setup_color_swatch_fields() {
		if ( $( '.artbees-color-swatch-picker' ).length !== 0 ) {
			$( '.artbees-color-swatch-picker' ).wpColorPicker();
		}
	}

	function get_product_terms_options( value, wrapper, taxonomy, produtcId ) {
		$.ajax( {
			type: 'POST',
			url: was.ajaxurl,
			data: {
				action: 'artbees_product_swatches_generate_product_options',
				swatch_type: value,
				terms_taxonomy: taxonomy,
				product_id: produtcId
			},
			success: function( data ) {
				$( wrapper ).find( '#artbees-was-terms-options' ).html( data.data );
				setup_color_swatch_fields();
				setup_image_swatch_fields();
			},
		} );
	}
} );
