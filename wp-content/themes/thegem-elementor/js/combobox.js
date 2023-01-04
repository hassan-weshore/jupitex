(function($) {
	$.fn.combobox = function() {
		$(this).each(function() {
			var $el = $(this);
			$el.insertBefore($el.parent('.combobox-wrapper'));
			$el.next('.combobox-wrapper').remove();
			$el.css({
				'opacity': 0,
				'position': 'absolute',
				'left': 0,
				'right': 0,
				'top': 0,
				'bottom': 0
			});
			var $comboWrap = $('<span class="combobox-wrapper" />').insertAfter($el);
			var $text = $('<span class="combobox-text" />').appendTo($comboWrap);
			var $button = $('<span class="combobox-button" />').appendTo($comboWrap);
			$el.appendTo($comboWrap);
			$el.change(function() {
				$text.text($('option:selected', $el).text());
			});
			$text.text($('option:selected', $el).text());
			$el.comboWrap = $comboWrap;
		});
	}
})(jQuery);