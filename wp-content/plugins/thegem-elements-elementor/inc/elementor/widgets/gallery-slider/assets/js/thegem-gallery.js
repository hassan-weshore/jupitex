(function($) {
	$(function() {

		function gallery_images_loaded($box, image_selector, callback) {
			function check_image_loaded(img) {
				return img.complete && img.naturalWidth !== undefined && img.naturalWidth != 0;
			}

			var $images = $(image_selector, $box).filter(function() {
					return !check_image_loaded(this);
				}),
				images_count = $images.length;

			if (images_count == 0) {
				return callback();
			}

			if (window.gemBrowser.name == 'ie' && !isNaN(parseInt(window.gemBrowser.version)) && parseInt(window.gemBrowser.version) <= 10) {
				function image_load_event() {
					images_count--;
					if (images_count == 0) {
						callback();
					}
				}

				$images.each(function() {
					if (check_image_loaded(this)) {
						return;
					}

					var proxyImage = new Image();
					proxyImage.addEventListener( 'load', image_load_event );
					proxyImage.addEventListener( 'error', image_load_event );
					proxyImage.src = this.src;
				});
				return;
			}

			$images.on('load error', function() {
				images_count--;
				if (images_count == 0) {
					callback();
				}
			});
		}

		function init_circular_overlay($gallery) {
			if (!$gallery.hasClass('gem-gallery-hover-circular')) {
				return;
			}

			$('.gem-gallery-item').on('mouseenter', function() {
				var overlayWidth = $('.overlay', this).width(),
					overlayHeight = $('.overlay', this).height(),
					$overlayCircle = $('.overlay-circle', this),
					maxSize = 0;

				if (overlayWidth > overlayHeight) {
					maxSize = overlayWidth;
					$overlayCircle.height(overlayWidth)
				} else {
					maxSize = overlayHeight;
					$overlayCircle.width(overlayHeight);
				}
				maxSize += overlayWidth * 0.3;

				$overlayCircle.css({
					marginLeft: -maxSize / 2,
					marginTop: -maxSize / 2
				});
			});
		}

		function initGallery() {
			if (window.tgpLazyItems !== undefined) {
				var isShowed = window.tgpLazyItems.checkGroupShowed(this, function(node) {
					initGallery.call(node);
				});
				if (!isShowed) {
					return;
				}
			}

			var $galleryElement = $(this);

			var $thumbItems = $('.gem-gallery-item', $galleryElement);

			var $galleryPreviewWrap = $('<div class="gem-gallery-preview-carousel-wrap"/>')
				.appendTo($galleryElement);
			var $galleryPreviewCarousel = $('<div class="gem-gallery-preview-carousel "/>')
				.appendTo($galleryPreviewWrap);
			var $galleryPreviewNavigation = $('<div class="gem-gallery-preview-navigation"/>')
				.appendTo($galleryPreviewWrap);
			var $galleryPreviewPrev = $('<a href="#" class="gem-prev gem-gallery-preview-prev"></a>')
				.appendTo($galleryPreviewNavigation);
			var $galleryPreviewNext = $('<a href="#" class="gem-next gem-gallery-preview-next"></a>')
				.appendTo($galleryPreviewNavigation);
			if($galleryElement.hasClass('with-pagination')) {
				// var $galleryPreviewPagination = $('<div class="gem-gallery-preview-pagination gem-mini-pagination"/>')
				// 	.appendTo($galleryPreviewWrap);
				var $galleryPreviewPagination = $('<div class="gem-gallery-preview-pagination gem-mini-pagination"/>')
					.insertAfter($galleryPreviewWrap);
			}
			var $previewItems = $thumbItems.clone(true, true);
			$previewItems.appendTo($galleryPreviewCarousel);
			$previewItems.each(function() {
				$('img', this).attr('src', $('a', this).attr('href'));
				$('a', this)
					.attr('href', $('a', this)
					.data('full-image-url'))
					.attr('data-fancybox', $('a', this).data('fancybox-group'))
					.addClass('fancy-gallery');
			});

			$galleryPreviewCarousel.initGalleryFancybox();

			var $galleryThumbsWrap = $('<div class="gem-gallery-thumbs-carousel-wrap"/>')
				.appendTo($galleryElement);
			var $galleryThumbsCarousel = $('<div class="gem-gallery-thumbs-carousel"/>')
				.appendTo($galleryThumbsWrap);
			var $galleryThumbsNavigation = $('<div class="gem-gallery-thumbs-navigation"/>')
				.appendTo($galleryThumbsWrap);
			var $galleryThumbsPrev = $('<a href="#" class="gem-prev gem-gallery-thumbs-prev"></a>')
				.appendTo($galleryThumbsNavigation);
			var $galleryThumbsNext = $('<a href="#" class="gem-next gem-gallery-thumbs-next"></a>')
				.appendTo($galleryThumbsNavigation);
			$thumbItems.appendTo($galleryThumbsCarousel);
			$thumbItems.each(function(index) {
				$(this).data('gallery-item-num', index);
			});

		}

		function updateGallery() {
			if (window.tgpLazyItems !== undefined) {
				var isShowed = window.tgpLazyItems.checkGroupShowed(this, function(node) {
					updateGallery.call(node);
				});
				if (!isShowed) {
					return;
				}
			}

			var $galleryElement = $(this);

			var $galleryPreviewCarousel = $('.gem-gallery-preview-carousel', $galleryElement);
			var $galleryThumbsWrap = $('.gem-gallery-thumbs-carousel-wrap', $galleryElement);
			var $galleryThumbsCarousel = $('.gem-gallery-thumbs-carousel', $galleryElement);
			var $thumbItems = $('.gem-gallery-item', $galleryThumbsCarousel);
			var $galleryPreviewPrev = $('.gem-gallery-preview-prev', $galleryElement);
			var $galleryPreviewNext = $('.gem-gallery-preview-next', $galleryElement);
			var $galleryPreviewPagination = $('.gem-gallery-preview-pagination', $galleryElement);
			var $galleryThumbsPrev = $('.gem-gallery-thumbs-prev', $galleryElement);
			var $galleryThumbsNext = $('.gem-gallery-thumbs-next', $galleryElement);

			$galleryElement.thegemPreloader(function() {

				var $galleryThumbs = $galleryThumbsCarousel, $galleryPreview = $galleryPreviewCarousel;

				$galleryPreview = $galleryPreviewCarousel.carouFredSel({
					auto: $galleryElement.data('autoscroll') ? $galleryElement.data('autoscroll') : false,
					circular: true,
					infinite: true,
					responsive: true,
					width: '100%',
					height: 'auto',
					items: 1,
					align: 'center',
					prev: $galleryPreviewPrev,
					next: $galleryPreviewNext,
					pagination: $galleryElement.hasClass('with-pagination') ? $galleryPreviewPagination : false,
					swipe: true,
					scroll: {
						pauseOnHover: true,
						items: 1,
						onBefore: function(data) {
							var current = $(this).triggerHandler('currentPage');
							var thumbCurrent = $galleryThumbs.triggerHandler('slice', [current, current+1]);
							var thumbsVisible = $galleryThumbs.triggerHandler('currentVisible');
							$thumbItems.filter('.active').removeClass('active');
							if(thumbsVisible.index(thumbCurrent) === -1) {
								$galleryThumbs.trigger('slideTo', current);
							}
							$('a', thumbCurrent).trigger('gemActivate');
						}
					},
					onCreate: function () {
						$(window).on('resize', function () {
							// console.log($galleryPreviewCarousel.children().first().height());
							$galleryPreviewCarousel.parent().add($galleryPreviewCarousel).height($galleryPreviewCarousel.children().first().height());
						}).trigger('resize');
					}
				});

				$galleryThumbs = $galleryThumbsCarousel.carouFredSel({
					auto: false,
					circular: true,
					infinite: true,
					width: '100%',
					height: 'variable',
					align: 'center',
					prev: $galleryThumbsPrev,
					next: $galleryThumbsNext,
					swipe: true,
					onCreate: function(data) {
						$('a', $thumbItems).on('gemActivate', function(e) {
							$thumbItems.filter('.active').removeClass('active');
							$(this).closest('.gem-gallery-item').addClass('active');
							$galleryPreview.trigger('slideTo', $(this).closest('.gem-gallery-item').data('gallery-item-num'));
						});
						$('a', $thumbItems).click(function(e) {
							e.preventDefault();
							$(this).trigger('gemActivate');
						});
					}
				});

				if($thumbItems.filter('.active').length) {
					$thumbItems.filter('.active').eq(0).find('a').trigger('click');
				} else {
					$thumbItems.eq(0).find('a').trigger('gemActivate');
				}

				if($thumbItems.length < 2) {
					$galleryThumbsWrap.hide();
				}

			});

			// if ($('.gem_tab').size() > 0) {
			// 	$('.gem_tab').on('tab-update', function() {
			// 		$(this).updateGalleries();
			// 	});
			// }

			// $(document).on('gem.show.vc.tabs', '[data-vc-accordion]', function() {
			// 	var $tab = $(this).data('vc.tabs').getTarget();
			// 	if ($tab.find($galleryElement).length) {
			// 		$(this).data('vc.accordion').getTarget().updateGalleries();
			// 	}				
			// });

			// $(document).on('gem.show.vc.accordion', '[data-vc-accordion]', function() {
			// 	var $acc = $(this).data('vc.accordion').getTarget();
			// 	if ($acc.find($galleryElement).length) {
			// 		$(this).data('vc.accordion').getTarget().updateGalleries();
			// 	}					
			// });
		}

		$.fn.initGalleries = function() {
			$(this).each(initGallery);
		};

		$.fn.updateGalleries = function() {
			$('.gem-gallery', this).each(updateGallery);
		};

		$('.gem-gallery').initGalleries();
		$('body').updateGalleries();

		$('.gem_tab').on('tab-update', function() {
			$(this).updateGalleries();
		});
		$(document).on('gem.show.vc.tabs', '[data-vc-accordion]', function() {
			$(this).data('vc.accordion').getTarget().updateGalleries();
		});
		$(document).on('gem.show.vc.accordion', '[data-vc-accordion]', function() {
			$(this).data('vc.accordion').getTarget().updateGalleries();
		});

	});
})(jQuery);


