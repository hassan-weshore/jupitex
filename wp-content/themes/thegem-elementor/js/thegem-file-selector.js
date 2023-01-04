(function($) {
	$.fn.fileSelector = function() {
		$(this).each(function() {
			var $el = $(this);
			$el.hide();
			var $fileWrapper = $('<a href="javascript:void(0);" class="file-wrapper" />').insertAfter($el);
			var $remove = $('<a href="javascript:void(0);" class="remove-file" >Remove File</a>').insertAfter($fileWrapper);
			$el.change(function() {
				if($el.val() == '') {
					$remove.hide();
					$fileWrapper.text('Click here to select file');
				} else {
					$remove.show();
					$fileWrapper.html($el.val());
				}
			}).trigger('change');

			var file_selector_frame;
			$fileWrapper.click(function(event) {
					event.preventDefault();
					var mediaType = $el.data('type');
					var mediaTypeTitle = 'EOT';

					// Create the media frame.
					file_selector_frame = wp.media.frames.fileSelector = wp.media({
						// Set the title of the modal.
						title: 'Select '+mediaTypeTitle,
						button: {
							text: 'Insert '+mediaTypeTitle,
						},
						library: {
							type: mediaType
						}
					});
					// When an image is selected, run a callback.
					file_selector_frame.on( 'select', function() {
						var attachment = file_selector_frame.state().get('selection').first();
						attachment = attachment.toJSON();
						if(attachment.url) {
							$el.val(attachment.url).trigger('change');
						}
					});
						// Finally, open the modal.
					file_selector_frame.open();
			});

		});
	}
})(jQuery);