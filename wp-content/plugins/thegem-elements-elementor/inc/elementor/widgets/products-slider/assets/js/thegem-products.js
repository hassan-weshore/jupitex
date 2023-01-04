var $ = jQuery;

function portfolio_images_loaded($box, image_selector, callback) {
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

function init_circular_overlay($portfolio, $set) {
	if (!$portfolio.hasClass('hover-circular') && !$portfolio.hasClass('hover-new-circular') && !$portfolio.hasClass('hover-default-circular')) {
		return;
	}

	$('.portfolio-item', $set).on('mouseenter', function () {
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

function update_slider_paddings($portfolio) {
	var first_item_height = $('.portfolio-item:first .image-inner', $portfolio).outerHeight(),
		button_height = $('.portolio-slider-prev span', $portfolio).outerHeight(),
		itemPadding = parseFloat($('.portfolio-item:first', $portfolio).css('padding-top'));

	if (isNaN(itemPadding)) {
		itemPadding = 0;
	}

	$('.portolio-slider-prev', $portfolio).css('padding-top', (first_item_height - button_height) / 2 + itemPadding);
	$('.portolio-slider-next', $portfolio).css('padding-top', (first_item_height - button_height) / 2 + itemPadding);
}

function initPortfolioSlider() {
	if (window.tgpLazyItems !== undefined) {
		var isShowed = window.tgpLazyItems.checkGroupShowed(this, function (node) {
			initPortfolioSlider.call(node);
		});
		if (!isShowed) {
			return;
		}
	}

	var $portfolio = $(this);
	var $set = $('.portfolio-set', this);
	var $prev = $('.portolio-slider-prev span', $portfolio);
	var $next = $('.portolio-slider-next span', $portfolio);

	if ($portfolio.hasClass('title-on-hover') || $portfolio.hasClass('hover-gradient') || $portfolio.hasClass('hover-circular')) {
		$('.portfolio-item .portfolio-icons-inner > a:not(.added_to_cart)', $portfolio).addClass('icon');
	}
	$('.portfolio-item .product-bottom .yith-wcwl-wishlistexistsbrowse a', $portfolio).addClass('icon wishlist');

	$portfolio.find('.portfolio-likes').each(function () {
		var icon = $(this).find('i');
		$(this).find('i').remove();
		$(this).find('a').prepend(icon);
	});
	$portfolio.find('.yith-icon').each(function () {
		if ($(this).find('i').length) {
			if (!$(this).find('a').children('i').length) {
				var icon = $(this).children('i');
				$(this).find('a').prepend(icon.clone());
			}
		} else if ($(this).find('svg').length) {
			if (!$(this).find('a').children('svg').length) {
				var icon_svg = $(this).children('svg');
				$(this).find('a').prepend(icon_svg.clone());
			}
		}
	});

	portfolio_images_loaded($set, '.image-inner img', function () {
		init_circular_overlay($portfolio, $set);
		if ($portfolio.hasClass('gem-slider-animation-dynamic')) {
			$set.juraSlider({
				type: 'dynamic',
				element: '.portfolio-item',
				prevButton: $prev,
				nextButton: $next,
				nextPageDelay: $portfolio.hasClass('columns-2') ? 200 : 300,
				afterInit: function () {
					$portfolio.prev('.preloader').remove();
				},
				autoscroll: $set.data('autoscroll') ? $set.data('autoscroll') : false
			});
		}
		if ($portfolio.hasClass('gem-slider-animation-one')) {
			$set.juraSlider({
				type: 'one',
				duration: 500,
				element: '.portfolio-item',
				prevButton: $prev,
				nextButton: $next,
				nextPageDelay: 0,
				afterInit: function () {
					$portfolio.prev('.preloader').remove();
				},
				autoscroll: $set.data('autoscroll') ? $set.data('autoscroll') : false
			});
		}
		update_slider_paddings($portfolio);
		setTimeout(function () {
			update_slider_paddings($portfolio);
		}, 100);

		$portfolio.on('click', '.portfolio-item .image .overlay, .portfolio-item .wrap > .caption', function(event) {
			var $target = $(event.target),
				$icons = $target.closest('.portfolio-item').find('.portfolio-icons');

			if ($target.closest('.icon').length || $target.closest('.socials-sharing').length || !$icons.length) {
				return;
			}

			if(window.gemSettings.isTouch) {
				if(!$target.closest('.portfolio-item').hasClass('touch-hover')) {
					$target.closest('.portfolio-item').addClass('touch-hover');
					$('*').one('click', function(event){
						if(!$(event.target).closest('.portfolio-item').is($target.closest('.portfolio-item'))) {
							$target.closest('.portfolio-item').removeClass('touch-hover');
						}
					});
					return false;
				}
			}
			if ($('.icon.bottom-product-link', $icons).length) {
				window.open($('.icon.bottom-product-link', $icons).attr('href'), "_self");
			}
		});
	});
}

function toggleNewsGridSharing(button) {
	var $meta = $(button).closest('.grid-post-meta-inner'),
		$likes = $('.grid-post-meta-comments-likes', $meta),
		$icons = $('.portfolio-sharing-pane', $meta);

	if ($meta.hasClass('active')) {
		$meta.removeClass('active');

		$('.socials-sharing', $meta).animate({
			width: 'toggle'
		}, 300, function () {
			$meta.removeClass('animation');
		});
	} else {
		$meta.css('min-width', $meta.outerWidth());

		$meta.addClass('active animation');

		$('.socials-sharing', $meta).animate({
			width: 'toggle'
		}, 200);
	}
}

$('body').on('click', '.portfolio.products-slider a.icon.share', function (e) {
	e.preventDefault();
	$(this).closest('.links').find('.portfolio-sharing-pane').toggleClass('active');
	$(this).closest('.post-footer-sharing').find('.sharing-popup').toggleClass('active');
	return false;
});


$('body').on('mouseleave', '.portfolio-slider .portfolio-item', function () {
	$('.portfolio-sharing-pane').removeClass('active');
});

$('body').on('click', '.portfolio-slider .portfolio-item', function () {
	$(this).mouseover();
});

$.fn.initPortfoliosSlider = function () {
	$(this).each(initPortfolioSlider);
};

$(window).on('load', function () {
	$('.portfolio.portfolio-slider').initPortfoliosSlider();
});
