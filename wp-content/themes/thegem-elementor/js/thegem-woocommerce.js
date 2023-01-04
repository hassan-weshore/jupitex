(function ($) {
	$('.variations_form').each(function () {
		$form = $(this)
		.on('change', '.variations select', function (event) {
			var $text = $(this).closest('.combobox-wrapper').find('.combobox-text');
			$text.text($('option:selected', $(this)).text());
		});
	});

	$( document ).on('click', '.shipping-calculator-button', function(e) {
		e.preventDefault();
		$(this).toggleClass('active');
	});

	$('body').on('updated_checkout', function () {
		$('input.gem-checkbox').checkbox();
		$('select.shipping_method').combobox();
		window.init_checkout_navigation();
	});

	$('body').on('updated_shipping_method', function () {
		$('input.gem-checkbox').checkbox();
		$('select.shipping_method').combobox();
	});

	$('.remove_from_wishlist_resp').on('click', function (e) {
		$(this).closest('.cart-item').find('.wishlist_table .product-remove .remove_from_wishlist').click();
		e.preventDefault();
		return false;
	});

	$(function () {
		$('.price_slider_amount .button').addClass('gem-button gem-button-style-outline gem-button-size-tiny');
	});

	// Quantity buttons
	$('form:not(.cart) div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)').addClass('buttons_added').append('<button type="button" class="plus" >+</button>').prepend('<button type="button" class="minus" >-</button>');

	$(document).on('click', '.plus, .minus', function () {

		// Get values
		var $qty = $(this).closest('.quantity').find('.qty'),
			currentVal = parseFloat($qty.val()),
			max = parseFloat($qty.attr('max')),
			min = parseFloat($qty.attr('min')),
			step = $qty.attr('step');

		// Format values
		if (!currentVal || currentVal === '' || currentVal === 'NaN') currentVal = 0;
		if (max === '' || max === 'NaN') max = '';
		if (min === '' || min === 'NaN') min = 0;
		if (step === 'any' || step === '' || step === undefined || parseFloat(step) === 'NaN') step = 1;

		// Change the value
		if ($(this).is('.plus')) {

			if (max && (max == currentVal || currentVal > max)) {
				$qty.val(max);
			} else {
				$qty.val(currentVal + parseFloat(step));
			}

		} else {

			if (min && (min == currentVal || currentVal < min)) {
				$qty.val(min);
			} else if (currentVal > 0) {
				$qty.val(currentVal - parseFloat(step));
			}

		}
		$qty.trigger('change');
	});

	$(document).on('click', '.product-bottom a.add_to_cart_button', function () {
		if ($(this).parents('.extended-products-grid').length) {
			return
		}
		$(this).closest('.product-bottom').find('a, .yith-wcwl-add-to-wishlist').hide();
	});

	$(document).on('click', '.product-bottom a.add_to_wishlist', function () {
		if ($(this).parents('.extended-products-grid').length) {
			return
		}
		var current_product = $(this).data('product-id');
		$('a.add_to_wishlist[data-product-id=' + current_product + ']').each(function () {
			$(this).closest('.product-bottom').find('a').hide();
			$(this).parent().addClass('ajax');
		})
	});

	$('body').on('added_to_wishlist', function (t, el_wrap) {
		$('.yith-wcwl-wishlistaddedbrowse').parents('.yith-wcwl-add-to-wishlist').addClass('icon');
	});


	$(document).on('click', '.woocommerce-review-link', function (e) {
		$('.gem-woocommerce-tabs').find('a[data-vc-accordion][href="#tab-reviews"]').trigger('click');
	});

	$(function () {
		if (typeof wc_add_to_cart_variation_params !== 'undefined') {
			$('.variations_form').each(function () {
				$(this).on('show_variation', function (event, variation) {
					if (variation.image_id) {
						var $product_content = $(this).closest('.single-product-content');
						var $gallery = $product_content.find('.gem-gallery').eq(0);
						if ($gallery.length) {
							var $gallery_item = $gallery.find('.gem-gallery-thumbs-carousel .gem-gallery-item[data-image-id="' + variation.image_id + '"] a');
							$gallery_item.closest('.gem-gallery-item').addClass('active');
							$gallery_item.trigger('click');
						}
					}
				});
			});
		}
	});

	$(function () {
		if (typeof wc_add_to_cart_variation_params !== 'undefined') {
			$('.variations_form').each(function () {
				$(this).on('show_variation', function (event, variation) {
					if (variation.image_id) {
						var $product_content = $(this).closest('.single-product-content');
						var $gallery = $product_content.find('.product-gallery').eq(0);
						var $mainCarousel = $gallery.find('.product-gallery-slider');
						if ($gallery.length) {
							var $gallery_item = $gallery.find('.product-gallery-slider .product-gallery-slider-item[data-image-id="' + variation.image_id + '"]').parent('.owl-item').index();
							$mainCarousel.trigger('to.owl.carousel', [$gallery_item, 300, true]);
						}
					}
				});
			});
		}
	});

	$(document.body).on('updated_wc_div applied_coupon removed_coupon', function () {
		$('.shop_table.cart').closest('form').eq(0).nextAll('.woocommerce-message').remove();
		$('.shop_table.cart').closest('form').eq(0).nextAll('.woocommerce-info').remove();
		$('.shop_table.cart').closest('form').eq(0).nextAll('.woocommerce-error').remove();
		$('.shop_table.cart').closest('form').eq(1).nextAll('form').remove();
		$('input.gem-checkbox').checkbox();
		$('select.shipping_method').combobox();
		$('form:not(.cart) div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)').addClass('buttons_added').append('<button type="button" class="plus" >+</button>').prepend('<button type="button" class="minus" >-</button>');

		if($('body').hasClass('woocommerce-cart-layout-classic')) {
			$.ajax({
				type: 'POST',
				url: thegem_woo_data.ajax_url,
				data: {
					action: 'thegem_cart_items_html',
				},
				dataType: 'html',
				success: function (response) {
					$('.cart-short-info').replaceWith($(response));
				}
			});
		}
	});

	$(document.body).on('updated_wc_div updated_shipping_method', function() {
		$( 'select.country_to_state, input.country_to_state' ).trigger( 'change' );
		$( document.body ).trigger( 'country_to_state_changed' );
	});

	$(function () {
		$('.gem-product-load-more').each(function () {
			if ($.fn.itemsAnimations !== undefined) {
				var $products_parent = $(this).siblings('.products');
				if (!$products_parent.hasClass('item-animation-move-up')) {
					$products_parent.addClass('item-animation-move-up');
				}
				$products_parent.itemsAnimations({
					itemSelector: '.product'
				});
			}
			$(this).on('click', 'button', function () {
				products_load_core_request($(this).closest('.gem-product-load-more'));
			});
		});

		$('.gem-product-scroll-pagination').each(function () {
			var $this = $(this),
				watcher = scrollMonitor.create(this);
			watcher.enterViewport(function () {
				products_load_core_request($this);
			});

			if ($.fn.itemsAnimations !== undefined) {
				var $products_parent = $(this).siblings('.products');
				if (!$products_parent.hasClass('item-animation-move-up')) {
					$products_parent.addClass('item-animation-move-up');
				}
				$products_parent.itemsAnimations({
					itemSelector: '.product'
				});
			}
		});
	});

	function products_load_core_request($pagination) {
		var current = parseInt($pagination.data('pagination-current')),
			total = parseInt($pagination.data('pagination-total')),
			base_url = $pagination.data('pagination-base'),
			is_processing_request = $pagination.data('request-process') || false,
			next_page = current + 1,
			next_page_url = base_url.replace('%#%', next_page);

		if (is_processing_request || next_page > total) {
			return false;
		}
		$pagination.data('request-process', true);
		if ($pagination.hasClass('gem-product-load-more')) {
			$('.gem-button', $pagination).before('<div class="loading"><div class="preloader-spin"></div></div>');
		}
		if ($pagination.hasClass('gem-product-scroll-pagination')) {
			$pagination.addClass('active').html('<div class="loading"><div class="preloader-spin"></div></div>');
		}

		$.ajax({
			url: next_page_url,
			data: {thegem_products_ajax: 1},
			success: function (response) {
				if ($pagination.hasClass('gem-product-load-more')) {
					$('.gem-button .loading', $pagination).remove();
				}
				if ($pagination.hasClass('gem-product-scroll-pagination')) {
					$pagination.removeClass('active').html('');
				}

				var $response = $(response),
					$products = $('.products .product', $response);

				if ($products.length) {
					var $products_parent = $pagination.siblings('.products');
					$products_parent.append($products);
					if ($.fn.itemsAnimations !== undefined) {
						var itemsAnimations = $products_parent.itemsAnimations('instance');
						if (itemsAnimations) {
							itemsAnimations.show($products);
						}
					}
					$pagination.data('pagination-current', next_page);
					if (next_page >= total) {
						$pagination.hide().remove();
					}
				}
				if ($pagination.hasClass('gem-product-load-more')) {
					$('.loading', $pagination).remove();
				}
				if ($pagination.hasClass('gem-product-scroll-pagination')) {
					$pagination.removeClass('active').html('');
				}
				$pagination.data('request-process', false);
			}
		});
	}

	$(function () {
		let getScrollY = (elem) =>{
			return window.pageYOffset || document.documentElement.scrollTop;
		};

		$(window).scroll(function() {
			if(getScrollY() > 0) {
				$('.page__top-shadow').removeClass('visible');
			} else {
				$('.page__top-shadow').addClass('visible');
			}
		}).scroll();
	});

})(jQuery);
