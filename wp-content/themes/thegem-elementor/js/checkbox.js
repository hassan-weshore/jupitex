(function($) {
	$.fn.checkbox = function() {
		$(this).each(function() {
			var $el = $(this);
			var typeClass = $el.attr('type');
			$el.hide();
			$el.next('.'+typeClass+'-sign').remove();
			var $checkbox = $('<span class="'+typeClass+'-sign" />').insertAfter($el);
			$checkbox.click(function() {
				if($el.attr('type') == 'radio') {
					$el.prop('checked', true).trigger('change').trigger('click');
				} else {
					$el.prop('checked', !($el.is(':checked'))).trigger('change');
				}
			});
			$el.change(function() {
				$('input[name="'+$el.attr('name')+'"]').each(function() {
					if($(this).is(':checked')) {
						$(this).next('.'+$(this).attr('type')+'-sign').addClass('checked');
					} else {
						$(this).next('.'+$(this).attr('type')+'-sign').removeClass('checked');
					}
				});
			});
			if($el.is(':checked')) {
				$checkbox.addClass('checked');
			} else {
				$checkbox.removeClass('checked');
			}
		});
	}
})(jQuery);
