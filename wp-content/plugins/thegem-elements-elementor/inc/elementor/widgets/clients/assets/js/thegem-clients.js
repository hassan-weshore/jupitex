(function($) {

	function initClientsGrid() {
		if (window.tgpLazyItems !== undefined) {
			var isShowed = window.tgpLazyItems.checkGroupShowed(this, function(node) {
				initClientsGrid.call(node);
			});
			if (!isShowed) {
				return;
			}
		}

		var $clientsCarouselElement = $(this);

		var $clientsItemsCarousel = $('.gem-clients-grid-carousel', $clientsCarouselElement);
		var $clientsItemsPagination = $('.gem-mini-pagination', $clientsCarouselElement);

		var autoscroll = $clientsCarouselElement.data('autoscroll') > 0 ? $clientsCarouselElement.data('autoscroll') : false;

		$clientsCarouselElement.thegemPreloader(function() {

			var $clientsGridCarousel = $clientsItemsCarousel.carouFredSel({
				auto: autoscroll,
				circular: false,
				infinite: true,
				width: '100%',
				items: 1,
				responsive: true,
				height: 'auto',
				align: 'center',
				pagination: $clientsItemsPagination,
				scroll: {
					pauseOnHover: true
				}
			});

		});
	}

	function initClientsCarousel() {
		if (window.tgpLazyItems !== undefined) {
			var isShowed = window.tgpLazyItems.checkGroupShowed(this, function(node) {
				initClientsCarousel.call(node);
			});
			if (!isShowed) {
				return;
			}
		}

		var $clientsElement = $(this);

		var $clientsCarousel = $('.gem-client-carousel', $clientsElement);
		var $clientsPrev = $('.gem-client-prev', $clientsElement);
		var $clientsNext = $('.gem-client-next', $clientsElement);

		var autoscroll = $clientsElement.data('autoscroll') > 0 ? $clientsElement.data('autoscroll') : false;

		$clientsElement.thegemPreloader(function() {

			var $clientsView = $clientsCarousel.carouFredSel({
				auto: autoscroll,
				circular: true,
				infinite: false,
				scroll: {
					items: 1
				},
				width: '100%',
				responsive: false,
				height: 'auto',
				align: 'center',
				prev: $clientsPrev,
				next: $clientsNext
			});

		});
	}

	$.fn.updateClientsGrid = function() {
		$(this).each(initClientsGrid);
	};

	$.fn.updateClientsCarousel = function() {
		$(this).each(initClientsCarousel);
	};

	$('.gem-clients-type-carousel-grid').updateClientsGrid();
	$('.gem_client_carousel-items').updateClientsCarousel();
	$('.gem_client-carousel.fullwidth-block').each(function() {
		$(this).on('updateClientsCarousel', function() {
			$(this).updateClientsCarousel();
		});
	});

	$('.gem_tab').on('tab-update', function() {
		$(this).updateClientsGrid();
	});
	$(document).on('gem.show.vc.tabs', '[data-vc-accordion]', function() {
		$(this).data('vc.accordion').getTarget().find('.gem-clients-type-carousel-grid').updateClientsGrid();
	});
	$(document).on('gem.show.vc.accordion', '[data-vc-accordion]', function() {
		$(this).data('vc.accordion').getTarget().find('.gem-clients-type-carousel-grid').updateClientsGrid();
	});

})(jQuery);



jQuery(window).on( 'elementor/frontend/init', function() {

	elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope, $) {
		setTimeout(()=>{
			$scope.find('.gem-clients-container .preloader').remove();
		},1000);

	});

});