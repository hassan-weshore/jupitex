(function($) {
	$(function() {

		$('.related-products-carousel').each(function() {

			var $relatedProductsElement = $(this);

			var $relatedProducts = $('.product', $relatedProductsElement);

			var $relatedProductsWrap = $('<div class="related-products-carousel-wrap"/>')
				.appendTo($relatedProductsElement);
			var $relatedProductsCarousel = $('<div class="related-products-carousel-carousel"/>')
				.appendTo($relatedProductsWrap);
			if($relatedProductsElement.hasClass('fullwidth-block')) {
				$relatedProductsCarousel.wrap('<div class="container" />');
			}
			var $relatedProductsNavigation = $('<div class="related-products-navigation"/>')
				.appendTo($relatedProductsWrap);
			var $relatedProductsPrev = $('<a href="#" class="gem-prev related-products-prev"/></a>')
				.appendTo($relatedProductsNavigation);
			var $relatedProductsNext = $('<a href="#" class="gem-next related-products-next"/></a>')
				.appendTo($relatedProductsNavigation);
			$relatedProducts.appendTo($relatedProductsCarousel);

		});

		$('body').updateRelatedProductsCarousel();

		$('.fullwidth-block').on('fullwidthUpdate', function() {
			$(this).updateRelatedProductsCarousel();
		});

	});

	$.fn.updateRelatedProductsCarousel = function() {
		$('.related-products-carousel', this).add($(this).filter('.related-products-carousel')).each(function() {
			var $relatedProductsElement = $(this);

			var $relatedProductsCarousel = $('.related-products-carousel-carousel', $relatedProductsElement);
			var $relatedProducts = $('.product', $relatedProductsCarousel);
			var $relatedProductsPrev = $('.related-products-prev');
			var $relatedProductsNext = $('.related-products-next');

			$relatedProductsElement.thegemPreloader(function() {

				var $relatedProductsView = $relatedProductsCarousel.carouFredSel({
					auto: false,
					circular: true,
					infinite: true,
					width: '100%',
					height: 'variable',
					align: 'center',
					prev: $relatedProductsPrev,
					next: $relatedProductsNext
				});

			});
		});
	}

})(jQuery);