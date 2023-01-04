function update_price_order_code(elem) {
	if ( jQuery( elem ).data( 'order_button_text' ) ) {
		jQuery( '#place_order' ).html( jQuery( elem ).data( 'order_button_text' ) );
	} else {
		jQuery( '#place_order' ).html( jQuery( '#place_order' ).data( 'value' ) );
	}
}

(function($) {
	$(function() {
		$('.checkout-as-guest button').click(function() {
			$('.checkout-steps .checkout-step[data-tab-id="checkout-billing"]').click();
			$('input#createaccount').prop('checked', false).trigger('change');
		});

		$('.woocommerce .create-account-popup').click(function(event) {
			if($(event.target).is('a')) return ;
			event.preventDefault();
			return false;
		});

		$('.woocommerce .checkout-create-account-button button').click(function(event) {
			$('.checkout-steps .checkout-step[data-tab-id="checkout-billing"]').click();
			$('.woocommerce .create-account-popup').hide();
			event.preventDefault();
			return false;
		});

		$('.checkout-cancel-create-account-button button').click(function(event) {
			$('.woocommerce .create-account-popup').hide();
			event.preventDefault();
			$('input#createaccount').prop('checked', false).trigger('change');
			$('div.create-account input').val('').trigger('change');
			return false;
		});

		$('.woocommerce .create-account-inner input').keypress(function (e) {
			if (e.which == 13) {
				event.preventDefault();
				return false;
			}
		});

		$('.checkout-create-account button').click(function(event) {
			$('input#createaccount').prop('checked', true).trigger('change');
			$('.woocommerce .create-account-popup').css('opacity', '0').show().animate({
				opacity: 1
			}, 400);
			$('body')
				.unbind('click.checkout-create-account')
				.bind('click.checkout-create-account', function() {
					$('input#createaccount').prop('checked', false).trigger('change');
					$('.woocommerce .create-account-popup').animate({
						opacity: 0
					}, 400, function() {
						$(this).hide();
					});
				});
			event.preventDefault();
			return false;
		});

		$('.checkout-show-login-popup').click(function() {
			$.fancybox.open({
				src  : '#checkout-login-popup',
				type : 'inline',
				touch: {
					vertical: false,
					momentum: false
				}
			});
			return false;
		});

		function init_checkout_navigation() {
			$('.checkout-navigation-buttons .checkout-prev-step').unbind('click').click(function() {
				var $active = $('.checkout-steps .checkout-step.active');
				if ($active.length == 0) {
					$('.checkout-steps .checkout-step:first').click();
				}

				$active.prev('.checkout-step').click();
			});

			$('.checkout-navigation-buttons .checkout-next-step').unbind('click').click(function() {
				var $active = $('.checkout-steps .checkout-step.active');
				if ($active.length == 0) {
					$('.checkout-steps .checkout-step:first').click();
				}

				$active.next('.checkout-step').click();
			});
		}
		window.init_checkout_navigation = init_checkout_navigation;
		init_checkout_navigation();
	});
})(jQuery);
