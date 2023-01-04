(function($) {
	$(function() {
		$('#socials-manager-form .social-pane:not(.empty) .file_url input').iconsPicker();
		addColorControl($('#socials-manager-form .social-pane:not(.empty) .color-picker'));
		$('#socials-manager-form .add-new').click(function() {
			var $newPane = $('#socials-manager-form .social-pane.empty').clone();
			$('input.icons-picker', $newPane).iconsPicker();
			addColorControl($('input.color-picker', $newPane));
			$('input.icons-picker', $newPane).data('iconpack', $('.icon-pack-select select', $newPane).val());
			$newPane.removeClass('empty')
				.insertBefore(this)
				.animate({opacity: 'show'});
		});
		$('#socials-manager-form').on('click', '.social-pane .remove a', function() {
			$(this).closest('.social-pane').animate({opacity: 'hide'}, function() {
				$(this).remove();
			});
		});
		$('#socials-manager-form').on('change', '.icon-pack-select select', function() {
			$(this).closest('.social-pane').find('input.icons-picker').data('iconpack', $(this).val());
		}).trigger('change');
	});

	var addColorControl = function(el) {
		el.each(function(){
			$('<span class="color-picker-button"></span>')
				.insertAfter($(this))
				.css('backgroundColor', $(this).val());
		});
		el.ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) {
				$(el).val('#' + hex);
				$(el).ColorPickerHide();
				$(el).change();
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
			}
		});
		el.each(function() {
			$(this).on('change', function(){
				$(this).next('.color-picker-button').css('backgroundColor', $(this).val());
			});
			$(this).next('.color-picker-button').on('click', function(){
				$(this).prev('input').ColorPickerShow();
			});
		});
	}

})(jQuery);