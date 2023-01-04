(function($) {

	$.fn.iconsPicker = function() {
		$(this).each(function() {
			var $el = $(this);
			if ($(this).next('.icons-picker-button').length == 0) {
				$('<button class="icons-picker-button">'+thegem_iconsPickerData.buttonTitle+'</button>').insertAfter(this);
			}
			if ($(this).prev('.icons-picker-selected').length == 0) {
				$('<span class="icons-picker-selected icon-'+$el.data('iconpack')+'">'+($el.val() ? '&#x'+$el.val()+';' : '')+'</span>').insertBefore(this);
			}
			var $button = $(this).next('.icons-picker-button');
			var $icon = $(this).prev('.icons-picker-selected');
			$el.off('change');
			$el.on('change', function(e) {
				$icon.removeClass('icon-elegant icon-material icon-fontawesome icon-userpack').addClass('icon-'+$el.data('iconpack')).html($el.val() ? '&#x'+$el.val()+';' : '');
			}).trigger('change');
			$button.off('click');
			$button.on('click', function(e) {
				e.preventDefault();
				var width = $(window).width(),
					H = $(window).height(),
					W = ( 833 < width ) ? 833 : width,
					adminbar_height = 0;

				if ( $('#wpadminbar').length ) {
					adminbar_height = parseInt( $('#wpadminbar').css('height'), 10 );
				}

				tb_show(thegem_iconsPickerData.buttonTitle, thegem_iconsPickerData.ajax_url +'?'+ $.param({security:thegem_iconsPickerData.ajax_nonce, action:'thegem_icon_list', iconpack:$el.data('iconpack'), width: W - 80, height: H - 85 - adminbar_height}));
				$(document).off('click', '.icons-list li');
				$(document).one('click', '.icons-list li', function() {
					$el.val($('.code', this).text()).trigger('change');
					tb_remove();
				});
			});
		});
	};
	$(function() {
		$('.icons-picker').iconsPicker();
	});
})(jQuery);
