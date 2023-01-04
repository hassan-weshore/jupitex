(function($) {
	$.fn.fontStyle = function(font) {
		var variants_text = {
			'100': 'Thin',
			'200': 'Extra-Light',
			'300': 'Light',
			'regular': 'Normal',
			'500': 'Medium',
			'600': 'Semi-Bold',
			'700': 'Bold',
			'800': 'Extra-Bold',
			'900': 'Ultra-Bold',
			'100italic': 'Thin Italic',
			'200italic': 'Extra-Light Italic',
			'300italic': 'Light Italic',
			'italic': 'Normal Italic',
			'500italic': 'Medium Italic',
			'600italic': 'Semi-Bold Italic',
			'700italic': 'Bold Italic',
			'800italic': 'Extra-Bold Italic',
			'900italic': 'Ultra-Bold Italic',
		};
		$(this).children().remove();
		for(i in font['variants']) {
			var $option = $('<option />');
			$option.attr('value', font['variants'][i])
				.text(variants_text[font['variants'][i]])
				.appendTo(this);
		}
		if($(this).attr('data-value') != undefined) {
			$(this).val($(this).attr('data-value'));
		}
		$(this).combobox();
		$(this).change(function() {
			$(this).attr('data-value', $(this).val());
		});
	}
})(jQuery);