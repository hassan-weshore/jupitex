(function($) {
	$(document).ready(function() {

		$('#categories').tabs();
		$('.subcategories').accordion({
			collapsible: true,
			header: '> div > h3',
			heightStyle: 'content'
		});

		$('body').on('change', '#logo_width', function() {
			$('.option.logo_field .image img').width($(this).val());
			$('.option.logo_light_field .image img').width($(this).val());
		});

		$('body').on('change', '#small_logo_width', function() {
			$('.option.small_logo_field .image img').width($(this).val());
			$('.option.small_logo_light_field .image img').width($(this).val());
		});

		$(window).on('load', function() {
			$('.option.logo_field .image img').width($('#logo_width').val());
			$('.option.logo_light_field .image img').width($('#logo_width').val());
			$('.option.small_logo_field .image img').width($('#small_logo_width').val());
			$('.option.small_logo_light_field .image img').width($('#small_logo_width').val());
		});

		$('.fixed-number input').each(function(){
			var min = $(this).attr('data-min-value');
			var max = $(this).attr('data-max-value');
			var value = $(this).val();
			var input = $(this);
			$('<div class="slider"></div>').insertAfter(input).slider({
				min: parseInt(min),
				max: parseInt(max),
				range: "min",
				value: parseInt(value),
				slide: function( event, ui ) {
					input.val(ui.value).trigger('change');
					$(this).prev('.value').text(ui.value);
				}
			});
		});

		$('.select select').combobox();
		$('.font-select select').combobox();

		function thegem_updateAdminFonts(root) {
			var fonts = [];
			$(root).find('select').each(function() {
				var font = $(this).val();
				if (font != '') {
					if ($.inArray(font, fonts) == -1)
						fonts.push(font);
				} else {
					if ($('#' + $this.attr('id').replace('font_family', 'font_style')).length)
						$('#' + $this.attr('id').replace('font_family', 'font_style')).combobox();
				}
			});

			if (fonts.length == 0)
				return false;

			var data = {
				security: theme_options_object.security,
				action: 'thegem_get_font_data',
				fonts: fonts
			}

			if ($(window).data('ajax-count') == undefined) {
				$(window).data('ajax-count', 0);
			}

			$(window).data('ajax-count', $(window).data('ajax-count')+1);
			thegem_ajaxOverlay();

			$.post(ajaxurl, data, function(response) {
				if (response != -1) {
					var fonts = JSON.parse(response);
					$(root).find('select').each(function() {
						var font = $(this).val();
						if (font == '')
							return;
						var font_data = fonts[font] || false;
						if (!font_data)
							return;
						if ($('#' + $(this).attr('id').replace('font_family', 'font_style')).length)
							$('#' + $(this).attr('id').replace('font_family','font_style')).fontStyle(font_data);
							
					});
				}
				$(window).data('ajax-count', $(window).data('ajax-count')-1);
				thegem_ajaxOverlay();
			});
		}

		$('.font-select select').each(function() {
			var $this = $(this);
			$this.change(function(e) {
				thegem_updateAdminFonts($(this).closest('.font-select'));
			});
		});

		thegem_updateAdminFonts($('.font-select'));

		$('.font-sets input').each(function() {
			var $this = $(this);
			$this.val($this.data('value'));
			var button = $('<a href="javascript:void(0);">'+theme_options_object.text1+'</a>')
				.insertAfter($this)
				.click(function() {
					var $fontSel = $('#'+$this.attr('id').replace('font_sets','font_family'));
					if($fontSel.val() != '') {
						var data = {
							security: theme_options_object.security,
							action: 'thegem_get_font_data',
							fonts: [$fontSel.val()]
						}
						if($(window).data('ajax-count') == undefined) {
							$(window).data('ajax-count', 0);
						}
						$(window).data('ajax-count', $(window).data('ajax-count')+1);
						thegem_ajaxOverlay();
						$.post(ajaxurl, data, function(response) {
							if(response != -1) {
								var fonts = JSON.parse(response);
								var font = $fontSel.val();
								if (font == '')
									return;
								var font_data = fonts[font] || false;
								if (!font_data)
									return;
								$this.val(font_data['subsets'].join(','));
								$(window).data('ajax-count', $(window).data('ajax-count')-1);
								thegem_ajaxOverlay();
							}
						});
					}
				});
		});

		$('.checkbox input').checkbox();

		$('.image input').imageSelector();
		$('.file input').fileSelector();

		String.prototype.endsWith = function(suffix) {
			return this.indexOf(suffix, this.length - suffix.length) !== -1;
		};

		var skins_defaults = JSON.parse(theme_options_object.thegem_color_skin_defaults);
		$('#page_color_style:input').change(function() {
			if(confirm('Change colors, backgrounds and fonts options?')){
				for(item in skins_defaults[$(this).val()]) {
					$('#'+item+':input').val(skins_defaults[$(this).val()][item]);
					if (!item.endsWith('font_family')) {
						$('#'+item+':input').trigger('change');
					} else {
						var $el = $('#'+item+':input');
						var $text = $el.siblings('.combobox-text:first');
						$text.text($('option:selected', $el).text());
					}
				}
				thegem_updateAdminFonts($('.font-select'));
			}
		});

		function thegem_ajaxOverlay() {
			if($(window).data('ajax-count') > 0 && $('.ajax-count-overlay').length == 0) {
				$('<div class="ajax-count-overlay" />').appendTo($('body'));
				$('.ajax-count-overlay').animate({'opacity': 'show'});
				$('#theme-options-form .submit_buttons').css({ visibility: 'hidden' });
			}
			if($(window).data('ajax-count') == 0 && $('.ajax-count-overlay').length > 0) {
				$('.ajax-count-overlay').animate({'opacity': 'hide'}, function() {
					$(this).remove();
					$('#theme-options-form .submit_buttons').css({ visibility: 'visible' });
				});
			}
		}

		$('form#theme-options-form button[name="action"]').click(function(event) {
			if($(this).val() == 'reset' && !confirm(theme_options_object.text2)) {
				event.preventDefault();
			}
			if($(this).val() == 'backup' && !confirm(theme_options_object.text3)) {
				event.preventDefault();
			}
			if($(this).val() == 'restore' && !confirm(theme_options_object.text4)) {
				event.preventDefault();
			}
			if($(this).val() == 'import' && !confirm(theme_options_object.text5)) {
				event.preventDefault();
			}
		});

		$('form#theme-options-form').submit(function() {
			if($('.ajax-count-overlay').length == 0) {
				$('<div class="ajax-count-overlay" />').appendTo($('body'));
				$('.ajax-count-overlay').animate({'opacity': 'show'});
			}
		});

		$('form#theme-options-form .image-select a').click(function(event) {
			event.preventDefault();
			$('#'+$(this).attr('data-target')).val($(this).attr('href')).trigger('change');
		});

	});
})(jQuery);
