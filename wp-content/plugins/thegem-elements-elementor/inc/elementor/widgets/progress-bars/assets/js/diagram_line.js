(function ($) {
	"use strict";

	function LineDiagram(element) {
		this.el = element;
		this.$el = jQuery(element);

		var self = this;
		var $diagram = this.$el;

		$(document).on('gem.show.vc.tabs', '[data-vc-accordion]', function() {
			var $tab = $(this).data('vc.accordion').getTarget();
			if($tab.find($diagram).length) {
				if (!$tab.find($diagram).hasClass('shown')) {
					$tab.find($diagram).addClass('shown');
					self.reinit();
				}
			}
		});
		$(document).on('gem.show.vc.accordion', '[data-vc-accordion]', function() {
			var $tab = $(this).data('vc.accordion').getTarget();
			if($tab.find($diagram).length) {
				if (!$tab.find($diagram).hasClass('shown')) {
					$tab.find($diagram).addClass('shown');
					self.reinit();
				}
			}
		});
		this.start();
	}

	LineDiagram.prototype = {
		start: function () {
			var self = this;
			if (!this.$el.hasClass('digram-line-box')) return;
			if (!this.$el.is('.lazy-loading-item')) {
				$('.skill-element', this.$el).each(function (i, el) {
					var $progress = $('.skill-line div', el),
						$skillAmount = $(el).find('.skill-amount'),
						amount = parseFloat($progress.data('amount'));
					$progress.css('width', amount + '%');
					$skillAmount.html(amount + '%');
				})
			} else {
				$('.skill-element', this.$el).each(function (i, el) {
					setTimeout(function () {
						self.showLines(el);
					}, 200 * i);
				});
			}
		},

		reinit: function() {
			this.$el.find('.skill-line div').removeClass('animation').css('width', '0%');
			this.$el.find('.skill-amount').html('0%');
			this.start();
		},

		showLines: function ($skill) {
			var $progress = $('.skill-line div', $skill),
				$skillAmount = $('.skill-amount', $skill),
				amount = parseFloat($progress.data('amount'));
			if ($($skill).closest('.digram-line-box').is('.lazy-loading-item')) {
				$progress.addClass('animation').css('width', amount + '%');
				$({countNum: 0}).animate({countNum: amount}, {
					duration: 1600,
					easing: 'easeOutQuart',
					step: function () {
						var count = parseFloat(this.countNum);
						var pct = Math.ceil(count) + '%';
						$skillAmount.html(pct);
					}
				});
			} else {
				$progress.css('width', amount + '%');
				$skillAmount.html(amount + '%');
			}
		},
	};

	jQuery.fn.thegem_start_line_digram = function () {
		return new LineDiagram(this.get(0));
	}

})(jQuery);

function thegem_show_diagram_line_mobile($box) {
	jQuery('.skill-element', $box).each(function () {
		jQuery('.skill-line div', this).width(jQuery('.skill-line div', this).data('amount') + '%');
	});
}

function thegem_start_line_digram(element) {
	jQuery(element).thegem_start_line_digram();
}

jQuery(window).on('elementor/frontend/init', function () {
	elementorFrontend.hooks.addAction('frontend/element_ready/thegem-diagram.default', function ($scope, $) {
		$scope.find('.digram-line-box').each(function () {
			var self = this;
			if ($scope.hasClass('elementor-element-edit-mode') || $scope.parents('.elementor-element-edit-mode').length || !$(self).data('ll-action-func') || window.gemSettings.lasyDisabled) {
				$(self).thegem_start_line_digram();
			}
		});
	});
});
