(function($) {
	$(function() {

		$('.widget-portfolio-carousel-grid:not(.carousel-disabled)').each(function() {

			var $portfoliosCarouselElement = $(this);

			var $portfoliosItems = $('.widget-portfolio-carousel-slide', $portfoliosCarouselElement);

			var $portfoliosItemsWrap = $('<div class="widget-portfolio-carousel-wrap"/>')
				.appendTo($portfoliosCarouselElement);
			var $portfoliosItemsCarousel = $('<div class="widget-portfolio-carousel"/>')
				.appendTo($portfoliosItemsWrap);
			var $portfoliosItemsPagination = $('<div class="widget-portfolio-pagination gem-mini-pagination"/>')
				.appendTo($portfoliosItemsWrap);
			$portfoliosItems.appendTo($portfoliosItemsCarousel);

		});


		$('.gem_portfolio_carousel-items').each(function () {

			var $portfoliosElement = $(this);

			var $portfolios = $('.widget-gem-portfolio-item', $portfoliosElement);

			var $portfoliosWrap = $('<div class="widget-portfolio-carousel-wrap"/>')
				.appendTo($portfoliosElement);
			var $portfoliosCarousel = $('<div class="gem-portfolio-carousel"/>')
				.appendTo($portfoliosWrap);
			var $portfoliosNavigation = $('<div class="portfolio-carousel-navigation"/>')
				.appendTo($portfoliosWrap);
			var $portfoliosPrev = $('<a href="#" class="gem-prev gem-portfolio-prev"/></a>')
				.appendTo($portfoliosNavigation);
			var $portfoliosNext = $('<a href="#" class="gem-next gem-portfolio-next"/></a>')
				.appendTo($portfoliosNavigation);
			$portfolios.appendTo($portfoliosCarousel);

		});

		$('body').updateportfoliosGrid();
		$('.gem_tab').on('tab-update', function() {
			$(this).updateportfoliosGrid();
		});
	});

	$.fn.updateportfoliosGrid = function() {
		$('.widget-portfolio-carousel-grid:not(.carousel-disabled)', this).each(function() {
			var $portfoliosCarouselElement = $(this);

			var $portfoliosItemsCarousel = $('.widget-portfolio-carousel', $portfoliosCarouselElement);
			var $portfoliosItemsPagination = $('.gem-mini-pagination', $portfoliosCarouselElement);

			var autoscroll = $portfoliosCarouselElement.data('autoscroll') > 0 ? $portfoliosCarouselElement.data('autoscroll') : false;

			$portfoliosCarouselElement.thegemPreloader(function() {

				var $portfoliosGridCarousel = $portfoliosItemsCarousel.carouFredSel({
					auto: autoscroll,
					circular: false,
					infinite: true,
					items: 1,
					responsive: true,
					height: 'auto',
					align: 'center',
					pagination: $portfoliosItemsPagination,
					scroll: {
					  pauseOnHover: true
					}
				});

			});
		});
	}

})(jQuery);
