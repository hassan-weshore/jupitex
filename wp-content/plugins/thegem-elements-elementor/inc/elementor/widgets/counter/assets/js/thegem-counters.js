var $ = jQuery;

function init_odometer(el) {
	if ($('.gem-counter-odometer', el).size() == 0)
		return;
	var odometer = $('.gem-counter-odometer', el).get(0);
	var format = $(el).closest('.gem-counter-box').data('number-format');

	format = format ? format : '(ddd).ddd';

	var od = new Odometer({
		el: odometer,
		value: $(odometer).text(),
		format: format
	});

	setTimeout(function () {
		od.update($(odometer).data('to'));
	}, 700);
}


$(document).ready(function () {

	window.odetrs_ar = [];

	$('.gem-counter').each(function (index) {
		if ($('.gem-counter-odometer', this).size() === 0)
			return;
		var odometer = $('.gem-counter-odometer', this).get(0);
		var format = $($('.gem-counter')[index]).parent().data('number-format');
		format = format ? format : '(ddd).ddd';
		window.odetrs_ar.push({
			od: new Odometer({
				el: odometer,
				value: $(odometer).text(),
				format: format
			}),
			i: index, el: $('.gem-counter')[index]
		});
	});

	var handler = function (entries, observer) {
		for (entry of entries) {
			if (entry.isIntersecting) {
				for (var i = 0; i < window.odetrs_ar.length; i++) {
					if (window.odetrs_ar[i].el == entries[0].target) {
						window.odetrs_ar[i].od.update($(window.odetrs_ar[i].el).find('.gem-counter-odometer').data('to'));

					}
				}
			}
		}
	};

	for (var i = 0; i < window.odetrs_ar.length; i++) {
		let observer = new IntersectionObserver(handler);
		observer.observe(document.querySelectorAll(".gem-counter")[window.odetrs_ar[i].i]);
	}

});

$(window).on( 'elementor/frontend/init', function() {

	elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope, $) {
		setTimeout(()=>{
			$scope.find('.gem-counter-container .preloader').remove();
		},1000);

	});

});