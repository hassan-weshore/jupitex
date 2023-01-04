(function ($, elementor) {
	"use strict";

	$(window).on('elementor/frontend/init', function () {

		var FrontEndExtended = elementorModules.frontend.handlers.Base.extend({

			generateInteractionsConfig: function () {
				// console.log('generateInteractionsConfig');
				this.interactionsConfig = {};
				if (this.getElementSettings('thegem_interaction_vertical_scroll') == 'yes') {
					this.interactionsConfig['vertical_scroll'] = 'yes';
					this.interactionsConfig['vertical_scroll_direction'] = 1; //1 - Up; -1 - Down;
					if (this.getElementSettings('thegem_interaction_vertical_scroll_direction') === 'negative') {
						this.interactionsConfig['vertical_scroll_direction'] = -1;
					}
					this.interactionsConfig['vertical_scroll_speed'] = this.getElementSettings('thegem_interaction_vertical_scroll_speed')['size']; //From 0 to 10
					this.interactionsConfig['vertical_viewport_bottom'] = this.getElementSettings('thegem_interaction_vertical_scroll_range')['sizes']['start'];
					this.interactionsConfig['vertical_viewport_top'] = this.getElementSettings('thegem_interaction_vertical_scroll_range')['sizes']['end'];
				} else {
					this.interactionsConfig['vertical_scroll'] = '';
				}

				if (this.getElementSettings('thegem_interaction_mouse') == 'yes') {
					this.interactionsConfig['mousemove'] = 'yes';
					this.interactionsConfig['mouse_direction'] = -1; //1 - Direct; -1 - Opposite
					if (this.getElementSettings('thegem_interaction_mouse_direction') === 'negative') {
						this.interactionsConfig['mouse_direction'] = 1;
					}
					this.interactionsConfig['mouse_speed'] = this.getElementSettings('thegem_interaction_mouse_speed')['size']; //From 0 to 10
				} else {
					this.interactionsConfig['mousemove'] = '';
				}
			},

			getVisibilityValues: function () {
				// console.log('getVisibilityValues');

				let start_element_visibility = this.$element.offset().top - $(window).height();
				this.interactionsConfig['start_element_animation'] = start_element_visibility + ($(window).height() + this.$element.height()) / 100 * this.interactionsConfig['viewport_bottom'];
				this.interactionsConfig['finish_element_animation'] = start_element_visibility + ($(window).height() + this.$element.height()) / 100 * this.interactionsConfig['viewport_top'];

				this.interactionsConfig['start_animation_value'] = (50 - this.interactionsConfig['viewport_bottom']) * this.interactionsConfig['scroll_speed'] * this.interactionsConfig['scroll_direction'];
				this.interactionsConfig['finish_animation_value'] = (50 - this.interactionsConfig['viewport_top']) * this.interactionsConfig['scroll_speed'] * this.interactionsConfig['scroll_direction'];
			},

			getWindowValues: function () {
				// console.log('getWindowValues');
				this.interactionsConfig['window_width'] = $(window).width();
				this.interactionsConfig['window_height'] = $(window).height();
			},

			setVerticalScrollValue: function () {
				if (this.interactionsConfig['stop_scroll_calc'] != true) {
					// console.log('setVerticalScrollValue');
					let visibility_percent = 100 * ($(window).scrollTop() - this.interactionsConfig['start_element_visibility']) / (this.interactionsConfig['finish_element_visibility'] - this.interactionsConfig['start_element_visibility']);

					if (visibility_percent > this.interactionsConfig['viewport_top']) {
						visibility_percent = this.interactionsConfig['viewport_top'];
					} else if (visibility_percent < this.interactionsConfig['viewport_bottom']) {
						visibility_percent = this.interactionsConfig['viewport_bottom'];
					}

					let y_pos = (visibility_percent - 50) * this.interactionsConfig['scroll_speed'] * this.interactionsConfig['scroll_direction'];
					this.$element.children(':not(.elementor-element-overlay)').css("transform", "translateY(" + y_pos + "px)");
					this.interactionsConfig['stop_scroll_calc'] = true;
					let element = this;
					setTimeout(function () {
						element.interactionsConfig['stop_scroll_calc'] = false;
					}, 20)
				}
			},

			setMouseValue: function (e) {
				if (this.interactionsConfig['stop_mouse_calc'] != true) {
					let x_pos = (e.pageX / this.interactionsConfig['window_width'] - 0.5) * 100 * this.interactionsConfig['mouse_speed'] * this.interactionsConfig['mouse_direction'];
					let y_pos = ((e.pageY - $(window).scrollTop()) / this.interactionsConfig['window_height'] - 0.5) * 100 * this.interactionsConfig['mouse_speed'] * this.interactionsConfig['mouse_direction'];

					this.$element.children(':not(.elementor-element-overlay)').css("transform", "translateX(" + x_pos + "px) translateY(" + y_pos + "px)");
					this.interactionsConfig['stop_mouse_calc'] = true;
					let element = this;
					setTimeout(function () {
						element.interactionsConfig['stop_mouse_calc'] = false;
					}, 20)
				}

			},

			bindEventResize: function () {
				// console.log('bindEventResize');
				let element = this;
				elementorFrontend.elements.$window.on('resize', function () {
					if (element.interactionsConfig['vertical_scroll'] === 'yes') {
						element.getVisibilityValues();
					}
					if (element.interactionsConfig['mousemove'] === 'yes') {
						element.getWindowValues();
					}
				});
			},

			bindEventScroll: function () {
				// console.log('bindEventScroll');
				let element = this;
				element.getVisibilityValues();
				elementorFrontend.elements.$window.on('load', function (e) {
					if (element.interactionsConfig['vertical_scroll'] === 'yes') {
						element.getVisibilityValues();
					}
				});
				elementorFrontend.elements.$window.on('scroll', function (e) {
					console.log($(window).scrollTop());
					// if (element.interactionsConfig['vertical_scroll'] === 'yes') {
					// 	element.setVerticalScrollValue(e);
					// }
				});
			},

			bindEventMouse: function () {
				// console.log('bindEventMouse');
				let element = this;
				element.getWindowValues();
				elementorFrontend.elements.$window.on('load', function (e) {
					if (element.interactionsConfig['mousemove'] === 'yes') {
						element.getWindowValues();
					}
				});
				elementorFrontend.elements.$window.on('mousemove', function (e) {
					if (element.interactionsConfig['mousemove'] === 'yes') {
						element.setMouseValue(e);
					}
				});
			},

			startInteractions: function () {
				// console.log('startInteractions');
				if (this.interactionsConfig['vertical_scroll'] === 'yes') {
					this.$element.addClass('thegem-interaction-element');
					// this.setVerticalScrollValue();
					// this.bindEventScroll();
					// this.bindEventResize();
					this.getVisibilityValues();

					var rellax = new Rellax(this.$element[0], {
						speed:this.interactionsConfig['vertical_scroll_speed']*this.interactionsConfig['vertical_scroll_direction'],
						center:true
					});
				}
				if (this.interactionsConfig['mousemove'] === 'yes') {
					this.$element.addClass('thegem-interaction-element');
					this.bindEventMouse();
					this.bindEventResize();
				}
				if (this.interactionsConfig['vertical_scroll'] != 'yes' && this.interactionsConfig['mousemove'] != 'yes') {
					if (this.$element.hasClass('thegem-interaction-element')) {
						this.$element.removeClass('thegem-interaction-element');
						this.$element.children().css('transform', 'none');
					}
				}
			},

			onElementChange: function (option) {
				console.log('onElementChange');
				this.generateInteractionsConfig();
				this.startInteractions();
			},

			onInit: function () {
				// console.log('onInit');
				let editMode = Boolean(elementor.isEditMode());
				this.interactionsConfig = false;
				if (!editMode || thegem_interactions[this.getID()] != undefined) {
					this.interactionsConfig = thegem_interactions[this.getID()] || false;
				} else {
					this.generateInteractionsConfig();
				}
				if (this.interactionsConfig) {
					this.startInteractions();
				}
			},

			onDestroy: function () {
			}
		});

		elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($element) {
			new FrontEndExtended({
				$element: $element
			});
		});
	});

}(jQuery, window.elementorFrontend));