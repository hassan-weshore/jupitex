(function ($) {
	"use strict";

	function Diagram(el, options) {
		var self = this;
		this.el = el;
		this.$el = $(el);
		this.$box = this.$el.find('.box');
		this.$skills_box = this.$el.find('.skills');
		this.$skills = this.$skills_box.find('.skill-arc');
		this.skills_count = this.$skills.size();
		this.diagram_rebuild_handler = false;

		this.$box.html('');
		this.$box.css({
			width: 'auto',
			height: 'auto'
		});

		function diagram_resize() {
			if (self.diagram_rebuild_handler)
				clearTimeout(self.diagram_rebuild_handler);
			self.diagram_rebuild_handler = setTimeout(function() {
				self.reinit();
			}, 50);
		}

		$(window).on('resize', function () {
			diagram_resize();
		});
		if (this.$el.closest('.gem_tab').size() > 0)
			this.$el.closest('.gem_tab').bind('tab-update', diagram_resize);
		var $diagram = this.$el;
		$(document).on('gem.show.vc.tabs', '[data-vc-accordion]', function() {
			var $tab = $(this).data('vc.accordion').getTarget();
			if($tab.find($diagram).length) {
				diagram_resize();
			}
		});
		$(document).on('gem.show.vc.accordion', '[data-vc-accordion]', function() {
			var $tab = $(this).data('vc.accordion').getTarget();
			if($tab.find($diagram).length) {
				diagram_resize();
			}
		});

		this.init();

	}

	$.fn.reverse = [].reverse;

	$.fn.circleDiagram = function (options) {
		if(this.length) {
			return new Diagram(this.get(0), options);
		}
	};

	Diagram.prototype = {
		init: function () {
			this.defaultText = '<span class="title diagram-summary-title">' + this.$el.data('title') + '</span><span class="summary diagram-summary-summary">' + this.$el.data('summary') + '</span>';
			this.width = this.$box.parent().width();
			if (this.width === 0)
				this.width = parseInt(this.$box.closest('.box-wrapper').css('max-width'));
			this.height = this.width;
			this.$box.width(this.width);
			this.$box.height(this.height);

			this.max_font_size = this.$el.data('max-font-size') || -1;

			this.$el.find('.text').remove();
			this.$title = $('<div class="text"><div>' + this.defaultText + '</div></div>');
			this.$title_content = this.$title.find('div');
			this.$box.after(this.$title);

			this.default_color = this.$el.data('base-color');

			this.$el.find('.diagram-legend').remove();
			var legend = '<div class="diagram-legend">';
			this.$skills.each(function () {
				var t = $(this),
					color = t.find('.color').val(),
					text = t.find('.title').text(),
					title_color = t.find('.title_color').val();
				legend += '<div class="legend-element clearfix"><span class="color" style="background: ' + color + ';"></span><span class="title diagram-legend-title" style="color: ' + title_color + '">' + text + '</span></div>';
			});
			legend += '</div>';
			this.$legend = $(legend);

			if (this.$el.data('show-legend') === 'yes')
				this.$box.parent().after(this.$legend);


			this.diagram();
		},

		reinit: function() {
			this.$box.html('');
			this.$box.css({
				width: 'auto',
				height: 'auto',
			});
			this.init();
		},

		random: function (l, u) {
			return Math.floor((Math.random() * (u - l + 1)) + l);
		},

		diagram: function () {
			var self = this;

			var max_stroke = 20;

			var center_radius = this.width / (3 * 1.5) - 0.2 * parseInt(this.skills_count / 5);
			var one_stroke = (this.width / 2 - center_radius - 20) * 0.6 / (this.skills_count);
			var stroke = one_stroke;
			if (stroke > max_stroke)
				stroke = max_stroke;
			var offset = one_stroke / 0.6 - stroke;
			var diametr = 2 * (center_radius + (stroke + offset) * this.skills_count) + 2 * stroke;
			var center_w = diametr / 2;
			var center_h = diametr / 2;

			this.width = diametr;
			this.height = diametr;
			this.$box.height(this.height);

			this.$title_content.width(center_radius * 2 - 10);
			this.$title_content.height(center_radius * 2);
			this.$title_content.css({
				'border-radius': center_radius,
				'-moz-border-radius': center_radius,
				'-webkit-border-radius': center_radius
			});
			this.$title.css({
				left: center_w - center_radius + 5,
				top: center_h - center_radius,
				fontSize: center_radius / 7 + 10,
				'border-radius': center_radius,
				'-moz-border-radius': center_radius,
				'-webkit-border-radius': center_radius
			});

			var legend_width = this.$el.width() - (this.width + 20);

			if (legend_width > 200 && this.$el.data('legend-position') === 'right') {
				this.$legend.css({
					position: 'absolute',
					top: '50%',
					marginTop: -(this.$legend.height() / 2),
					left: this.width + 20,
					width: legend_width
				});
				this.$box.closest('.box-wrapper').css('margin', '0');
			} else {
				this.$legend.css({
					position: 'static',
					display: 'inline-block'
				});
				this.$box.closest('.box-wrapper').css('margin', 'auto');
				if (this.$el.data('show-legend') === 'yes')
					this.$box.parent().after(this.$legend);
			}

			$('.legend-element', this.$legend).css({
				marginBottom: center_w / 11
			});

			self.raphael = Raphael(this.$box[0], this.width, this.height);
			var rad = center_radius + stroke * 0.67;
			var speed = 250;

			self.raphael.circle(center_w, center_h, center_radius).attr({stroke: 'none', fill: '#ffffff', opacity: 0});

			self.raphael.customAttributes.arc = function (value, color, rad) {
				var v = 3.6 * value,
					alpha = v === 360 ? 359.99 : v,
					random = 260,
					a = (random - alpha) * Math.PI / 180,
					b = random * Math.PI / 180,
					sx = center_w + rad * Math.cos(b),
					sy = center_h - rad * Math.sin(b),
					x = center_w + rad * Math.cos(a),
					y = center_h - rad * Math.sin(a),
					path = [['M', sx, sy], ['A', rad, rad, 0, +(alpha > 180), 1, x, y]];
				return {path: path, stroke: color}
			};

			this.$skills.each(function (i) {
				var t = $(this),
					color = t.find('.color').val(),
					value = t.find('.percent').val(),
					text = t.find('.title').text(),
					title_color = t.find('.title_color').val(),
					percent_color = t.find('.percent_color').val();

				var back_percent = 94.5;
				var draw_value = value * back_percent / 100;
				var base_color = t.data('base-color');
				if (typeof base_color === 'undefined') {
					base_color = self.default_color;
				}
				var total = self.raphael.path().attr({arc: [back_percent, base_color, rad, i], 'stroke-width': stroke});
				var dia = self.raphael.path().attr({arc: [draw_value, color, rad, i], 'stroke-width': stroke});
				rad += stroke + offset;
				dia.mouseover(function () {
					this.animate({'stroke-width': stroke * 1.5, opacity: 0.75}, 1000, 'elastic');
					total.animate({'stroke-width': stroke * 1.5, opacity: 0.75}, 1000, 'elastic');
					if (Raphael.type !== 'VML') { //solves IE problem
						this.toFront();
						dia.toFront();
					}
					self.$title_content.stop().animate({opacity: 0}, speed, function () {
						self.$title_content.css({paddingTop: 5}).html('<span class="hover-title diagram-skill-title" style="color: ' + title_color + '">' + text + '</span><span class="hover-amount diagram-skill-amount" style="color: ' + percent_color + ';">' + value + '%</span>').animate({opacity: 1}, speed);
					});
				}).mouseout(function () {
					this.stop().animate({'stroke-width': stroke, opacity: 1}, speed * 4, 'elastic');
					total.stop().animate({'stroke-width': stroke, opacity: 1}, speed * 4, 'elastic');
					self.$title_content.stop().animate({opacity: 0}, speed, function () {
						self.$title_content.css({paddingTop: 0}).html(self.defaultText).animate({opacity: 1}, speed);
					});
				});
				total.mouseover(function () {
					dia.animate({'stroke-width': stroke * 1.5, opacity: 0.75}, 1000, 'elastic');
					this.animate({'stroke-width': stroke * 1.5, opacity: 0.75}, 1000, 'elastic');
					if (Raphael.type !== 'VML') { //solves IE problem
						this.toFront();
						dia.toFront();
					}
					self.$title_content.stop().animate({opacity: 0}, speed, function () {
						self.$title_content.css({paddingTop: 5}).html('<span class="hover-title diagram-skill-title" style="color: ' + title_color + '">' + text + '</span><span class="hover-amount diagram-skill-amount" style="color: ' + percent_color + ';">' + value + '%</span>').animate({opacity: 1}, speed);
					});
				}).mouseout(function () {
					this.stop().animate({'stroke-width': stroke, opacity: 1}, speed * 4, 'elastic');
					dia.stop().animate({'stroke-width': stroke, opacity: 1}, speed * 4, 'elastic');
					self.$title_content.stop().animate({opacity: 0}, speed, function () {
						self.$title_content.css({paddingTop: 0}).html(self.defaultText).animate({opacity: 1}, speed);
					});
				});
			});
		}
	};

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/thegem-diagram.default', function ($scope, $) {
			$('.diagram-circle', $scope).circleDiagram();
		});
	});

}(jQuery));

