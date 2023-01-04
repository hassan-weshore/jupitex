(function($) {
	$(function() {
		var $form = $('#fonts-manager-form');

		$form.find('.file_url input').fileSelector();

		$form.on('click', '.add-new', function() {
			var $newPane = $(this).closest('#fonts-manager-wrap').find('.font-pane-template').clone();
			var paneItem = $('#fonts-manager-form').find('.font-pane').length;
			$newPane.removeClass('font-pane-template').addClass('font-pane');
			$newPane.attr('data-item', paneItem);
			$newPane.find('.fallback-fonts-elements-items-box').html('');
			var newPaneHtml = $newPane[0].outerHTML;
			newPaneHtml = newPaneHtml.replace(/{{i}}/g, paneItem);
			$('.fonts-manager-form-fields').append(newPaneHtml);
			$('.file_url input', $form.find('[data-item='+paneItem+']')).fileSelector();
		});

		$form.on('click', '.font-pane .remove a', function() {
			if (confirm('Are you sure you want to delete this font?')) {
				$(this).closest('.font-pane').remove();
			}
			});

		$form.on('change', '.input-checkbox-is-fallback', function() {
			var $parentField = $(this).closest('.field');
			$parentField.find('input[type=hidden]').val(this.checked ? 1 : '');
			$parentField.find('.fallback-fonts-elements-box').toggleClass('hide');
		});

		$form.on('click', '.fallback-fonts-elements-add button', function() {
			var $pane = $(this).closest('.font-pane');
			var $elementsBox = $pane.find('.fallback-fonts-elements-box');
			var $field = $elementsBox.find('.fallback-fonts-elements-add > select');
			var title = $field.find(":selected").text();
			var value = $field.val();
			var isFontOnly = $field.find(":selected").attr('data-font-only')!==undefined;
			var paneItem = $pane.attr('data-item');
			var elItem = $elementsBox.find('.fallback-fonts-elements-item').length;

			if ($field.val()==='') {
				return false;
			}

			if ($elementsBox.find('[data-id='+value+']').length > 0) {
				alert(title+' already exists.');
				return false;
			}

			var $template = $('#fonts-manager-wrap .font-pane-template').find('.fallback-fonts-elements-item').clone();
			$template.find('.fallback-fonts-elements-item-title input[type=hidden]').val(value);
			$template.find('.fallback-fonts-elements-item-title label').text(title);
			$template.attr('data-id', value);

			if (isFontOnly) {
				$template.find('.fallback-fonts-elements-item-body').remove();
			} else {
				var fontSize = $field.find(":selected").attr('data-font-size');
				var lineHeight = $field.find(":selected").attr('data-line-height');
				$template.find('.fonts-elements-item-font-size').attr('value',fontSize);
				$template.find('.fonts-elements-item-line-height').attr('value',lineHeight);
			}

			var templateHtml = $template[0].outerHTML;
			templateHtml = templateHtml.replace(/{{i}}/g, paneItem);
			templateHtml = templateHtml.replace(/{{el}}/g, elItem);
			templateHtml = templateHtml.replace(/{{element_title}}/g, title);
			$elementsBox.find('.fallback-fonts-elements-items-box').append(templateHtml);
			initFixedNumber($elementsBox.find('[data-id='+value+']'));
			$field.val('');
		});

		$form.on('click', '.fallback-fonts-elements-item-header button', function() {
			if (confirm('Are you sure you want to delete this item?')) {
				$(this).closest('.fallback-fonts-elements-item').remove();
			}
		});

		$form.on('submit', function() {
			var isRequiredFontName = false;
			$form.find('.field-font-name').each(function() {
				var value = this.value.trim();
				isRequiredFontName = value===undefined || value==='';
			});

			if (isRequiredFontName) {
				alert('Field Font name is required.');
				return false;
			}
		});

		function initFixedNumber(el) {
			el.find('.fixed-number input').each(function() {
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
		}

		initFixedNumber($form);


	});
})(jQuery);
