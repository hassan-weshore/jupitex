(function($) {
	$(function() {

	

	$.fn.updateBlogSlider = function() {
		$('.gem-blog-slider', this).each(function() {
			var $newsCarouselElement = $(this);
			var $newsItemsCarousel = $('.gem-blog-slider-carousel', $newsCarouselElement);
			var $newsItems = $('article', $newsItemsCarousel);
			var $newsItemsNavigation = $('.gem-blog-slider-navigation', $newsCarouselElement);
			var $newsItemsPrev = $('.gem-blog-slider-prev', $newsCarouselElement);
			var $newsItemsNext = $('.gem-blog-slider-next', $newsCarouselElement);

			$newsCarouselElement.thegemPreloader(function() {

				var $newsCarousel = $newsItemsCarousel.carouFredSel({
					auto: ($newsCarouselElement.data('autoscroll') > 0 ? $newsCarouselElement.data('autoscroll') : false),
					circular: true,
					infinite: true,
					responsive: true,
					width: '100%',
					height: 'auto',
					align: 'center',
					items: 1,
					swipe: true,
					prev: $newsItemsPrev,
					next: $newsItemsNext,
					scroll: {
						pauseOnHover: true,
						items: 1
					},
					onCreate: function() {
						$(window).on('resize', function() {
							var heights = $newsItems.map(function() { return $(this).height(); });
							$newsCarousel.parent().add($newsCarousel).height(Math.max.apply(null, heights));
						});
					}
				});

			});
		});
	}

	$.fn.prepareBlogSlider = function() {
		$('.gem-blog-slider').each(function() {

			var $newsCarouselElement = $(this);

			var $newsItems = $('article', $newsCarouselElement);

			var $newsItemsWrap = $('<div class="gem-blog-slider-carousel-wrap"/>')
				.appendTo($newsCarouselElement);
			var $newsItemsCarousel = $('<div class="gem-blog-slider-carousel"/>')
				.appendTo($newsItemsWrap);
			$newsItems.appendTo($newsItemsCarousel);

		});
	}

	$('body').prepareBlogSlider();
	$('body').updateBlogSlider();	
});
})(jQuery);