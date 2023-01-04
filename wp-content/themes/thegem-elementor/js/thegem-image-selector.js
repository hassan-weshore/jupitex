(function($) {
	$.fn.imageSelector = function() {
		$(this).each(function() {
			var $el = $(this);
			$el.hide();
			var $imageWrapper = $('<a href="javascript:void(0);" class="image-wrapper" />').insertAfter($el);
			var $remove = $('<a href="javascript:void(0);" class="remove-image" >Remove image</a>').insertAfter($imageWrapper);
			$el.change(function() {
				if($el.val() == '') {
					$remove.hide();
					$imageWrapper.text('Click here to select image');
				} else {
					$remove.show();
					$imageWrapper.html('<img src="'+$el.val()+'" alt="No image found" />');
				}
			}).trigger('change');

			var images_selector_frame;

			$imageWrapper.click(function( event ) {

				var $this = $(this);
				event.preventDefault();

				if ( images_selector_frame ) {
					images_selector_frame.open();
					return;
				}
				// Create the media frame.
				images_selector_frame = wp.media.frames.imageSelector = wp.media({
					// Set the title of the modal.
					title: this.$i18n.t('Select Image'),
					button: {
						text: 'Insert Image',
					},
					states : [
						new wp.media.controller.Library({
							title: 'Select Image',
							filterable : 'image',
							multiple: false,
						})
					]
				});
				// When an image is selected, run a callback.
				images_selector_frame.on( 'select', function() {
					var attachment = images_selector_frame.state().get('selection').first();
					attachment = attachment.toJSON();
					if(attachment.id) {
						$el.val(attachment.url).trigger('change');
					}
				});
					// Finally, open the modal.
				images_selector_frame.open();
			});

			$remove.click(function() {
				$el.val('').trigger('change');
			});
		});
	}
})(jQuery);