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

	if ($portfolio.hasClass('products') && ($portfolio.hasClass('title-on-hover') || $portfolio.hasClass('hover-gradient') || $portfolio.hasClass('hover-circular'))) {
		$('.portfolio-item .portfolio-icons-inner > a:not(.added_to_cart), .portfolio-item .portfolio-icons-inner .yith-wcwl-add-to-wishlist, .portfolio-item .portfolio-icons-inner .yith-wcwl-add-to-wishlist div:not(.yith-wcwl-wishlistaddedbrowse) a', $portfolio).addClass('icon');
	}

	$portfolio.find('.portfolio-likes').each(function () {
		var icon = $(this).find('i');
		$(this).find('i').remove();
		$(this).find('a').prepend(icon);
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

// window.onresize = () => { console.log('onresize'); };

// $(window).onresize(function () {
	// console.log('onresize');
	// var $portfolio = $(this);
	// setTimeout(function () {
	// 	update_slider_paddings($portfolio);
	// }, 10);
// });

$(window).resize(function() {
	$('.portfolio-slider').each(function() {
		var $portfolio = $(this);
		setTimeout(function() {
			update_slider_paddings($portfolio);
		}, 10);
	});
});

$('body').on('click', '.portfolio-slider a.icon.share', function (e) {
	e.preventDefault();

	if ($(this).closest('.portfolio-slider').hasClass('version-new') ||
		($(this).closest('.portfolio-slider').hasClass('version-default') &&
			$(this).closest('.portfolio-slider').hasClass('title-on-hover'))
	) {
		toggleNewsGridSharing(this);
	} else {
		$(this).closest('.links').find('.portfolio-sharing-pane').toggleClass('active');
	}
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

$(document).ready(function () {
	$('.portfolio.portfolio-slider').initPortfoliosSlider();
});
