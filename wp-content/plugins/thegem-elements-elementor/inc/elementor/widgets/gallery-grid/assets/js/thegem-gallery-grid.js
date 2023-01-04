(function ($) {
	$(function () {

		function gallery_images_loaded($box, image_selector, callback) {
			function check_image_loaded(img) {
				return img.complete && img.naturalWidth !== undefined && img.naturalWidth != 0;
			}

			var $images = $(image_selector, $box).filter(function () {
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

				$images.each(function () {
					if (check_image_loaded(this)) {
						return;
					}

					var proxyImage = new Image();
					proxyImage.addEventListener('load', image_load_event);
					proxyImage.addEventListener('error', image_load_event);
					proxyImage.src = this.src;
				});
				return;
			}

			$images.on('load error', function () {
				images_count--;
				if (images_count == 0) {
					callback();
				}
			});
		}

		function init_circular_overlay($gallery, $set) {
			if (!$gallery.hasClass('hover-circular')) {
				return;
			}

			$('.gallery-item', $set).on('mouseenter', function () {
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

		function initGalleryGrid() {
			var layoutMode = 'masonry-custom';
			if ($(this).hasClass('metro')) {
				layoutMode = 'metro'
			}

			if (window.tgpLazyItems !== undefined) {
				var isShowed = window.tgpLazyItems.checkGroupShowed(this, function (node) {
					initGalleryGrid.call(node);
				});
				if (!isShowed) {
					return;
				}
			}

			var $gallery = $(this);
			var $set = $('.gallery-set', this);

			gallery_images_loaded($set, '.image-wrap img', function () {
				$gallery.closest('.gallery-preloader-wrapper').prev('.preloader').remove();

				init_circular_overlay($gallery, $set);

				var itemsAnimations = $gallery.itemsAnimations({
					itemSelector: '.gallery-item',
					scrollMonitor: true
				});
				var init_gallery = true;

				$set
					.on('arrangeComplete', function (event, filteredItems) {
						if (init_gallery) {
							init_gallery = false;

							var items = [];
							filteredItems.forEach(function (item) {
								items.push(item.element);
							});

							itemsAnimations.show($(items));
						}
					})
					.isotope({
						itemSelector: '.gallery-item',
						itemImageWrapperSelector: '.image-wrap',
						fixHeightDoubleItems: $gallery.hasClass('gallery-style-justified'),
						layoutMode: layoutMode,
						'masonry-custom': {
							columnWidth: '.gallery-item:not(.double-item)'
						}
					});
			});

			if ($set.closest('.gem_tab').size() > 0) {
				$set.closest('.gem_tab').bind('tab-update', function () {
					$set.isotope('layout');
				});
			}
			$(document).on('show.vc.tab', '[data-vc-tabs]', function () {
				var $tab = $(this).data('vc.tabs').getTarget();
				if ($tab.find($set).length) {
					$set.isotope('layout');
				}
			});
		}

		$.fn.initGalleriesGrid = function () {
			$(this).each(initGalleryGrid);
		};

		$(document).ready(function() {
			$('body:not(.elementor-editor-active) .gem-gallery-grid').initGalleriesGrid();
		});

		setTimeout(function () {
			if ($('body:not(.elementor-editor-active) .preloader + .gallery-preloader-wrapper').length) {
				$('.gem-gallery-grid').initGalleriesGrid();
			}
		}, 2000);

		$('.gem-gallery-grid').on('click', '.gallery-item', function () {
			$(this).mouseover();
		});
	});
})(jQuery);