(function($) {
	$(function() {

		$('.gem-news-type-carousel').each(function() {

			var $newsCarouselElement = $(this);

			var $newsItems = $('.gem-news-item', $newsCarouselElement);

			var $newsItemsWrap = $('<div class="gem-news-carousel-wrap"/>')
				.appendTo($newsCarouselElement);
			var $newsItemsCarousel = $('<div class="gem-news-carousel"/>')
				.appendTo($newsItemsWrap);
			var $newsItemsPagination = $('<div class="gem-news-pagination gem-mini-pagination"/>')
				.appendTo($newsItemsWrap);
			$newsItems.appendTo($newsItemsCarousel);

		});

		$('.gem-blog-slider').each(function() {

			var $newsCarouselElement = $(this);

			var $newsItems = $('article', $newsCarouselElement);

			var $newsItemsWrap = $('<div class="gem-blog-slider-carousel-wrap"/>')
				.appendTo($newsCarouselElement);
			var $newsItemsCarousel = $('<div class="gem-blog-slider-carousel"/>')
				.appendTo($newsItemsWrap);
			var $newsItemsNavigation = $('<div class="gem-blog-slider-navigation"/>')
				.appendTo($newsItemsWrap);
			var $newsItemsPrev = $('<a href="#" class="gem-blog-slider-prev gem-button gem-button-size-tiny"><i class="gem-print-icon gem-icon-pack-thegem-icons gem-icon-prev"></i></a>')
				.appendTo($newsItemsNavigation);
			var $newsItemsNext = $('<a href="#" class="gem-blog-slider-next gem-button gem-button-size-tiny"><i class="gem-print-icon gem-icon-pack-thegem-icons gem-icon-next"></i></a>')
				.appendTo($newsItemsNavigation);
			$newsItems.appendTo($newsItemsCarousel);
			$newsItemsNavigation.appendTo($newsItems.find('.gem-slider-item-overlay'));

		});

		$('body').updateNews();
		$('body').updateNewsSlider();

	});

	$.fn.updateNews = function() {
		$('.gem-news-type-carousel', this).each(function() {
			var $newsCarouselElement = $(this);

			var $newsItemsCarousel = $('.gem-news-carousel', $newsCarouselElement);
			var $newsItems = $('.gem-news-item', $newsItemsCarousel);
			var $newsItemsPagination = $('.gem-mini-pagination', $newsCarouselElement);

			$newsCarouselElement.thegemPreloader(function() {

				var $newsCarousel = $newsItemsCarousel.carouFredSel({
					auto: 10000,
					circular: false,
					infinite: true,
					width: '100%',
					height: 'variable',
					align: 'center',
					pagination: $newsItemsPagination
				});

			});
		});
	}

	$.fn.updateNewsSlider = function() {
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

})(jQuery);