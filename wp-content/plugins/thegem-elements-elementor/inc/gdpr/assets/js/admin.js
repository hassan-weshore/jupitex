(function($) {
	'use strict';

	$(function() {
		const ajaxUrl = thegem_gdpr_admin_options.ajax_url;
		const security = thegem_gdpr_admin_options.security;
		const consentsFormData = thegem_gdpr_admin_options.consentsFormData;

		$('.thegem-gdpr-color-picker').wpColorPicker();

		$('.thegem-gdpr-active-consent-bar').on('change', function() {
			$('.thegem-gdpr-consent-bar-fields').toggleClass('active');
		});

		$('.thegem-gdpr-use-consent-bar-custom-styles').on('change', function() {
			$('.thegem-gdpr-consent-bar-custom-styles-box').toggleClass('active');
		});

		$('.thegem-gdpr-use-overlay-custom-styles').on('change', function() {
			$('.thegem-gdpr-overlay-custom-styles-box').toggleClass('active');
		});

		$('.thegem-gdpr-forms-item-enabled').on('change', function () {
			$(this).closest('.thegem-gdpr-forms-item').toggleClass('active');
		});

		$('.thegem-gdpr-consent-add').click(function() {
			var selectItem = $('#thegem-gdpr-consent-type-select');
			if (selectItem.val()==='') {
				alert('Please select consent!');
				return;
			}

			var prefixId = selectItem.attr('data-prefix');
			var wrapItems =  $('.thegem-gdpr-consent-type-items');
			var title = $(':selected', selectItem).attr("data-title");
			var type = selectItem.val();

			var $addItemId = $('#'+prefixId+'-'+type);

			if ($('.thegem-gdpr-consent-type-item').is($addItemId)) {
				alert(title+' already exists');
				return;
			}

			var template = $('#thegem-gdpr-consent-type-item-template').clone();

			if (consentsFormData.posterField.indexOf(type) === -1) {
				template.find('.thegem-gdpr-consent-poster-field').remove();
			}

			if (consentsFormData.stateField.indexOf(type) === -1) {
				template.find('.thegem-gdpr-consent-state-field').remove();
			}

			if (consentsFormData.requiredField.indexOf(type) === -1) {
				template.find('.thegem-gdpr-consent-required-field').remove();
			}

			if (consentsFormData.titleField.indexOf(type) === -1) {
				template.find('.thegem-gdpr-consent-title-field').remove();
			}

			if (type != consentsFormData.consentGoogleFonts && template.find('.thegem-gdpr-consent-google-fonts-notice').length > 0) {
				template.find('.thegem-gdpr-consent-google-fonts-notice').remove();
			}

			if (consentsFormData.defaultDescriptions[type]!=undefined) {
				template.find('.thegem-gdpr-consent-description-value').html(consentsFormData.defaultDescriptions[type]);
			}

			var templateHtml = template.html();
			templateHtml = templateHtml.replace(/{{type}}/g, type);
			templateHtml = templateHtml.replace(/{{title}}/g, title);
			wrapItems.append(templateHtml);

			selectItem.val('');
		});

		$(document).on('click', '.thegem-gdpr-consent-type-delete', function() {
			if (confirm('Are you sure you want to delete this item?')) {
				$(this).closest('.thegem-gdpr-consent-type-item').remove();
			}
		});

		$(document).on('change', '.thegem-gdpr-consent-required-field > input', function() {
			var container = $(this).closest('.thegem-gdpr-consent-type-body');
			var stateBox = container.find('.thegem-gdpr-consent-state-field');
			stateBox.toggleClass('hide');
			stateBox.find('input').prop('checked', false);
		});

		$('.thegem-gdpr-consent-type-items').sortable();

		$('.thegem-gdpr-select-font-family').on('change', function() {
			var parent = $(this).closest('.thegem-gdpr-field-box');
			var fontStyleField = parent.find('.thegem-gdpr-select-font-style');
			var fontSetsField = parent.find('.thegem-gdpr-select-font-sets');

			if (this.value) {
				var data = {
					security: security,
					action: 'thegem_gdpr_get_font_data',
					font: this.value
				};
				$.post(ajaxUrl, data, function(response) {
					if (response !== -1) {
						var font = JSON.parse(response);
						var selectOptions = '<option value=""></option>';

						if (Object.keys(font.variants).length > 0) {
							for (var idx in font.variants) {
								selectOptions += '<option value="'+idx+'" '+(fontStyleField.val() === idx ? "selected" : "")+'>'+font.variants[idx]+'</option>';
							}
						}
						fontStyleField.html(selectOptions);
						fontSetsField.attr('data-value', font.subsets);
					}
				});
			} else {
				fontStyleField.val('');
			}

			fontSetsField.val('');
		});

		$(document).on('click', '.btn-get-all-sets-font', function() {
			var parent = $(this).closest('.thegem-gdpr-field-box');
			var fontSetsField = parent.find('.thegem-gdpr-select-font-sets');
			fontSetsField.val(fontSetsField.attr('data-value'));
		});

		$('.thegem-gdpr-field-box').find('.fixed-number input').each(function() {
			var min = $(this).attr('data-min-value');
			var max = $(this).attr('data-max-value');
			var value = $(this).val();
			var input = $(this);
			$('<div class="slider"></div>').insertAfter(input).slider({
				min: parseInt(min),
				max: parseInt(max),
				range: 'min',
				value: parseInt(value),
				slide: function( event, ui ) {
					input.val(ui.value).trigger('change');
				}
			});
		});


	});
})(jQuery);
