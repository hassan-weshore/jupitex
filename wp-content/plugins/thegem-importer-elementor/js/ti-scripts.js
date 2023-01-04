(function($) {
	$(function() {

		var thegem_importer = {

			init: function() {
				this.content = $('.thegem-importer-content');
				if($('.error-message', this.content).length) return false;
				this.data = thegem_importer_data;
				this.import_active = false;
				this.pluginCheckRepeater = false;
				var that = this;
				$.fancybox.defaults.slideClass = 'thegem-importer-popup';
				$.fancybox.defaults.clickSlide = false;
				$.fancybox.defaults.clickOutside = false;
				that.check_purchase_code(function() {
					that.get_imports_list();
				});
				$(document).on('click', '.imports-list .import-item .import-link', function(e) {
					e.preventDefault();
					that.importClick($(this));
				});
				$(document).on('click', '.thegem-importer-wrap .plugins-proceed-import', function(e) {
					e.preventDefault();
					$.fancybox.close();
					that.pluginCheckRepeater = false;
					var $link = $('<a>');
					$link.data('import', that.import);
					$link.data('ignore-plugins', 1);
					that.importClick($link);
				});
				$(document).on('submit', '.import-select-type', function(e) {
					e.preventDefault();
					that.importTypeSelect($(this));
				});
				$(document).on('submit', '.import-data-select', function(e) {
					e.preventDefault();
					that.importDataSelect($(this));
				});
				$(document).on('click', '.submit-buttons .cancel-import, .thegem-importer-filesystem-credentials-form-wrap #request-filesystem-credentials-form .cancel-button', function(e) {
					e.preventDefault();
					that.importCancel();
				});
				$(document).on('change', '.import-data-select input[name="import_theme_options"]', function(e) {
					var $input = $(this);
					if($input.is(':checked')) {
						$('.description', $input.closest('.import-data-wrap')).show();
					} else {
						$('.description', $input.closest('.import-data-wrap')).hide();
					}
				});
				$(document).on('change', '.import-select-type input[name="import_type"]', function(e) {
					var $input = $(this);
					$('.import-select-type .import-type-wrap').removeClass('active');
					if($input.is(':checked')) {
						$input.closest('.import-type-wrap').addClass('active');
					}
				});
				$(document).on('change', '.import-data-select input[name="select_all"]', function(e) {
					var $input = $(this);
					var $checkboxes = $('input[type="checkbox"]', $input.closest('.import-data-part-column')).not($input);
					$checkboxes.prop('checked', $input.is(':checked')).trigger('change');
				});
				$(document).on('submit', '.thegem-importer-filesystem-credentials-form-wrap form', function(e) {
					e.preventDefault();
					that.importGenerateCSS($(this));
				});
				$(document).on('click', '.thegem-importer-remover a', function(e) {
					e.preventDefault();
					that.removeDemoContentConfirm();
				});
				$(document).on('click', '.remove-demo-confirm', function(e) {
					e.preventDefault();
					that.removeDemoContent();
				});
				$(document).on('click', '.imports-categories .imports-category > a', function(e) {
					e.preventDefault();
					var $link = $(this);
					$('.imports-categories .imports-category-other-list').removeClass('visible');
					if($link.data('category') == 'other') {
						$('.imports-categories .imports-category-other-list').addClass('visible');
						return ;
					}
					var $showElements = $('.imports-list .import-item');
					if($link.data('category') != 'all') {
						$showElements = $('.imports-list .import-item[data-category="' + $link.data('category') + '"]');
					}
					var $hideElements = $('.imports-list .import-item').not($showElements);
					$showElements.removeClass('hidden');
					$hideElements.addClass('hidden');
					$('.imports-categories .imports-category a').removeClass('active');
					$link.addClass('active');
					if($link.closest('.imports-category-other-list').length) {
						$('.imports-category-other > a').addClass('active');
					}
				});
				$(document).on('click', '.imports-category-other-close', function(e) {
					e.preventDefault();
					$('.imports-categories .imports-category-other-list').removeClass('visible');
				});
				$(document).on('submit', '.imports-search-form', function(e) {
					e.preventDefault();
					var $form = $(this);
					var keyword = $('input', $form).val().trim();
					if(keyword == '') return ;
					$('.imports-categories .imports-category.imports-category-all a').trigger('click');
					var $showElements = $('.imports-list .import-item[data-keywords*="' + keyword + '"]');
					var $hideElements = $('.imports-list .import-item').not($showElements);
					$showElements.removeClass('hidden');
					$hideElements.addClass('hidden');
				});
			},

			check_purchase_code: function(callback) {
				var that = this;
				$.ajax({
					url: that.data.ajax_url,
					data: {action: 'thegem_importer_check_purchase_code'},
					method: 'POST',
				}).done(function(msg) {
					msg = jQuery.parseJSON(msg);
					if(msg && msg.content) {
						that.content.html(msg.content);
					}
					if(msg.status && msg.status == 200) {
						callback();
					}
				}).fail(function() {
					that.content.html(that.data.ajax_error_content);
				});
			},

			get_imports_list: function() {
				var that = this;
				that.content.html(that.data.get_imports_list_msg);
				$.ajax({
					url: that.data.ajax_url,
					data: {action: 'thegem_importer_get_imports_list'},
					method: 'POST',
				}).done(function(msg) {
					msg = jQuery.parseJSON(msg);
					if(msg && msg.content) {
						that.content.html(msg.content);
						var availableValues = $('.imports-search .autocomplete-field').data('keywords').split(' ');
						$('.imports-search .autocomplete-field').autocomplete({
							source: availableValues,
							select: function( event, ui ) {
								$('.imports-search .imports-search-form').trigger('submit');
							},
							minLength: 3
						});
					}
				}).fail(function() {
					that.content.html(that.data.ajax_error_content);
				});
			},

			importClick: function(link) {
				var that = this;
				var ajax_data;
				if(!link.data('import')) return;
				this.import = link.data('import');
				$.fancybox.open(that.data.load_import_step_1_msg);
				var fbInstance = $.fancybox.getInstance();
				fbInstance.current.$smallBtn.remove();
				fbInstance.current.$smallBtn = null;
				if(link.data('ignore-plugins')) {
					ajax_data = {action: 'thegem_importer_get_import_step_1', import: that.import, ignore_plugins: 1};
				} else {
					ajax_data = {action: 'thegem_importer_get_import_step_1', import: that.import};
				}
				$.ajax({
					url: that.data.ajax_url,
					data: ajax_data,
					method: 'POST',
				}).done(function(msg) {
					msg = jQuery.parseJSON(msg);
					if(msg && msg.content) {
						fbInstance.setContent(fbInstance.current, msg.content);
						if(msg.status == 100) {
							that.pluginCheckRepeater = true;
							that.startPluginsCheck();
						}
					}
				}).fail(function() {
					fbInstance.setContent(fbInstance.current, that.data.ajax_error_content);
				});
			},

			startPluginsCheck: function() {
				var that = this;
				setTimeout(function(){
					$.ajax({
						url: that.data.ajax_url,
						data: {action: 'thegem_importer_check_plugins', import: that.import},
						method: 'POST',
					}).done(function(msg) {
						msg = jQuery.parseJSON(msg);
						if(msg.content_table) {
							$('.thegem-importer-wrap .import-select-woocommerce-required-table').replaceWith($(msg.content_table));
						}
						if(msg.content_buttons) {
							$('.thegem-importer-wrap .submit-buttons').replaceWith($(msg.content_buttons));
						}
						if(msg.status == 100 && that.pluginCheckRepeater && $('.thegem-importer-wrap').length) {
							that.startPluginsCheck();
						}
						if(msg.status == 200) {
							that.pluginCheckRepeater = false;
						}
					}).fail(function() {
						that.startPluginsCheck();
					});
				}, 3000)
			},

			importTypeSelect: function(form) {
				var that = this;
				$.fancybox.close();
				$.fancybox.open(that.data.load_import_step_2_msg);
				var fbInstance = $.fancybox.getInstance();
				fbInstance.current.$smallBtn.remove();
				fbInstance.current.$smallBtn = null;
				var import_data = form.serialize();
				import_data = import_data + '&action=thegem_importer_get_import_step_2';
				$.ajax({
					url: that.data.ajax_url,
					data: import_data,
					method: 'POST',
				}).done(function(msg) {
					msg = jQuery.parseJSON(msg);
					if(msg && msg.content) {
						fbInstance.setContent(fbInstance.current, msg.content);
					}
				}).fail(function() {
					fbInstance.setContent(fbInstance.current, that.data.ajax_error_content);
				});
			},

			importDataSelect: function(form) {
				var that = this;
				$.fancybox.close();
				$.fancybox.open(that.data.load_import_step_3_msg);
				var fbInstance = $.fancybox.getInstance();
				fbInstance.current.$smallBtn.remove();
				fbInstance.current.$smallBtn = null;
				var import_data = form.serialize();
					import_data = import_data + '&action=thegem_importer_get_import_step_3';
				$.ajax({
					url: that.data.ajax_url,
					data: import_data,
					method: 'POST',
				}).done(function(msg) {
					msg = jQuery.parseJSON(msg);
					if(msg && msg.content) {
						fbInstance.setContent(fbInstance.current, msg.content);
						that.import_active = true;
						that.importFailCounter = 0;
						that.importProcess();
						that.importProgress();
					}
				}).fail(function() {
					fbInstance.setContent(fbInstance.current, that.data.ajax_error_content);
				});
			},

			importFailCounter: 0,

			importProcess: function() {
				var that = this;
				var fbInstance = $.fancybox.getInstance();
				$.ajax({
					url: that.data.ajax_url,
					data: {action: 'thegem_importer_process'},
					method: 'POST',
				}).done(function(msg) {
					that.importFailCounter = 0;
					msg = jQuery.parseJSON(msg);
					if(msg && msg.content) {
						if(msg.status == 10) {
							that.importProcess();
						}
						if(msg.status == 200) {
							that.import_active = false;
							that.importFinalize();
						}
					}
				}).fail(function() {
					that.importFailCounter++;
					if (that.importFailCounter>=3) {
						fbInstance.setContent(fbInstance.current, that.data.ajax_error_content);
					} else {
						setTimeout(function() {
							that.importProcess();
						},60*1000);
					}
				});
			},

			importProgress: function() {
				var that = this;
				var fbInstance = $.fancybox.getInstance();
				if(that.import_active) {
					$.ajax({
						url: that.data.ajax_url,
						data: {action: 'thegem_importer_progress'},
						method: 'POST',
					}).done(function(msg) {
						msg = jQuery.parseJSON(msg);
						if(msg && msg.percent >= 0) {
							var percent = $('.import-progress-bar-line').data('percent') < msg.percent ? msg.percent : $('.import-progress-bar-line').data('percent');
							$('.import-progress-bar-line').data('percent', percent)
							$('.import-progress-bar-line').width(percent+'%');
							$('.import-progress-bar-percents .number').text(percent);
							setTimeout(function(){
								that.importProgress();
							}, 5000);
						}
					}).fail(function() {
						setTimeout(function(){
							that.importProgress();
						}, 5000);
					});
				} else {
					$('.import-progress-bar-line').width('100%');
					$('.import-progress-bar-percents .number').text('100');
				}
			},

			importFinalize: function() {
				var that = this;
				var fbInstance = $.fancybox.getInstance();
				$('.import-progress-bar').remove();
				$('.import-select-data-desription').html(that.data.load_import_step_finalize_msg);
				import_data = 'action=thegem_importer_get_import_step_finalize';
				$.ajax({
					url: that.data.ajax_url,
					data: import_data,
					method: 'POST',
				}).done(function(msg) {
					msg = jQuery.parseJSON(msg);
					if(msg && msg.content) {
						$('.import-select-data-desription').text(msg.content);
						if(msg.status == 10) {
							that.importFinalize();
						}
						if(msg.status == 200) {
							that.importGenerateCSS(false);
						}
					}
				}).fail(function() {
					fbInstance.setContent(fbInstance.current, that.data.ajax_error_content);
				});
			},

			importGenerateCSS: function(form, error = '') {
				var that = this;
				$.fancybox.close();
				$.fancybox.open(that.data.load_import_step_css_msg);
				var fbInstance = $.fancybox.getInstance();
				fbInstance.current.$smallBtn.remove();
				fbInstance.current.$smallBtn = null;
				var import_data = 'action=thegem_importer_generate_css';
				if(form) {
					import_data = import_data + '&' + form.serialize();
				}
				if(error) {
					import_data = import_data + '&error=' + error;
				}
				$.ajax({
					url: that.data.ajax_url,
					data: import_data,
					method: 'POST',
				}).done(function(msg) {
					msg = jQuery.parseJSON(msg);
					if(msg && msg.content) {
						if(msg.status == 10) {
							fbInstance.setContent(fbInstance.current, msg.content);
						}
						if(msg.status == 20) {
							that.importGenerateCSS(false, msg.content);
						}
						if(msg.status == 200) {
							that.importFinish();
						}
					}
				}).fail(function() {
					fbInstance.setContent(fbInstance.current, that.data.ajax_error_content);
				});
			},

			importFinish: function() {
				var that = this;
				$.fancybox.close();
				$.fancybox.open(that.data.load_import_step_4_msg);
				var fbInstance = $.fancybox.getInstance();
				fbInstance.current.$smallBtn.remove();
				fbInstance.current.$smallBtn = null;
				import_data = 'action=thegem_importer_get_import_step_4';
				$.ajax({
					url: that.data.ajax_url,
					data: import_data,
					method: 'POST',
				}).done(function(msg) {
					msg = jQuery.parseJSON(msg);
					if(msg && msg.content) {
						fbInstance.setContent(fbInstance.current, msg.content);
					}
				}).fail(function() {
					fbInstance.setContent(fbInstance.current, that.data.ajax_error_content);
				});
			},

			importCancel: function() {
				var that = this;
				that.pluginCheckRepeater = false;
				$.fancybox.close();
				$.fancybox.open(that.data.load_import_cancel);
				var fbInstance = $.fancybox.getInstance();
				import_data = 'action=thegem_importer_get_import_cancel'
				$.ajax({
					url: that.data.ajax_url,
					data: import_data,
					method: 'POST',
				}).done(function(msg) {
					$.fancybox.close();
				}).fail(function() {
					$.fancybox.close();
				});
			},

			removeDemoContentConfirm: function() {
				var that = this;
				$.fancybox.open(that.data.load_remove_demo_msg);
				var fbInstance = $.fancybox.getInstance();
				fbInstance.current.$smallBtn.remove();
				fbInstance.current.$smallBtn = null;
				var import_data = 'action=thegem_importer_remove_demo_confirm';
				$.ajax({
					url: that.data.ajax_url,
					data: import_data,
					method: 'POST',
				}).done(function(msg) {
					msg = jQuery.parseJSON(msg);
					if(msg && msg.content) {
						if(msg.status == 200) {
							fbInstance.setContent(fbInstance.current, msg.content);
						}
					}
				}).fail(function() {
					fbInstance.setContent(fbInstance.current, that.data.ajax_error_content);
				});
			},

			removeDemoContent: function() {
				var that = this;
				$.fancybox.close();
				$.fancybox.open(that.data.load_remove_demo_msg);
				var fbInstance = $.fancybox.getInstance();
				fbInstance.current.$smallBtn.remove();
				fbInstance.current.$smallBtn = null;
				var import_data = 'action=thegem_importer_remove_demo';
				$.ajax({
					url: that.data.ajax_url,
					data: import_data,
					method: 'POST',
				}).done(function(msg) {
					msg = jQuery.parseJSON(msg);
					if(msg && msg.content) {
						if(msg.status == 200) {
							fbInstance.setContent(fbInstance.current, msg.content);
						}
					}
				}).fail(function() {
					fbInstance.setContent(fbInstance.current, that.data.ajax_error_content);
				});
			}

		}

		thegem_importer.init();
	});

})(jQuery);
