(function ($) {

    'use strict';

    var isTouch = window.gemSettings.isTouch,
        isMobile = $(window).width() < 768 && /Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ? true : false,
        isTabletPortrait = $(window).width() === 768 && isTouch && window.matchMedia("(orientation: portrait)") ? true : false,
        isSticky = $('.single-product-content').attr('data-sticky') === 'yes' ? true : false,
        isAjaxLoad = $('.single-product-content').attr('data-ajax-load') === 'yes' ? true : false,

        productPageScripts = {
            // Initialization the functions
            init: function () {
                productPageScripts.classes();
                productPageScripts.tabs();
                productPageScripts.accordion();
                productPageScripts.stickyColumn();
                productPageScripts.rating();
                //productPageScripts.combobox();
                productPageScripts.productVariable();
                productPageScripts.productQuantity();
                if (isAjaxLoad) {
                    productPageScripts.ajaxAddToCart();
                }
                productPageScripts.ajaxAddToWishlist();
                productPageScripts.ajaxRemoveFromWishlist();
                productPageScripts.onResize();
            },

            classes: function () {
                let $notifyWrap = $('.thegem-popup-notification-wrap');

                $notifyWrap.css('display', 'block');
            },

            // Product page tabs
            tabs: function () {
                let $tabs = $('.thegem-tabs'),
                    $tabNavItem = $('.thegem-tabs__nav-item'),
                    $tabNavItemActive = $('.thegem-tabs__nav-item--active'),
                    $line = $('.thegem-tabs__nav-slide'),
                    isVertical = $tabs.attr("data-type") === 'vertical',
                    activeClass = 'thegem-tabs__nav-item--active',
                    $accItemTitle = $('.thegem-accordion__item-title'),
                    $accItemBody = $('.thegem-accordion__item-body'),
                    accActiveClass = 'thegem-accordion__item--active',

                    getPosition = ($target) => {
                        let $position = {
                            top: $target.offsetTop,
                            left: $target.offsetLeft
                        };
                        return $position;
                    },

                    animateLine = ($target, $ln) => {
                        let currentWidth = $target.offsetWidth,
                            currentHeight = $target.offsetHeight,
                            currentPos = getPosition($target);

                        if (isVertical) {
                            $ln[0].style.top = currentPos.top + 'px';
                            $ln[0].style.height = currentHeight + 'px';
                        } else {
                            $ln[0].style.left = currentPos.left + 'px';
                            $ln[0].style.width = currentWidth + 'px';
                        }
                    },

                    onLoadLine = () => {
                        animateLine($tabNavItemActive[0], $line);
                    };

                $tabNavItem.each(function (index, el) {
                    onLoadLine();

                    setTimeout(function () {
                        $line[0].style.transition = '0.25s ease';
                    }, 200);
                });

                $tabNavItem.on('click', function (e) {
                    let currentAttrvalue = '#' + $(this).attr('data-id');

                    $tabNavItem.removeClass(activeClass);
                    $(this).addClass(activeClass);
                    $accItemTitle.removeClass(accActiveClass);
                    $accItemBody.filter(currentAttrvalue).prev().addClass(accActiveClass);
                    $accItemBody.hide().filter(currentAttrvalue).show();

                    animateLine(e.currentTarget, $line);
                    if (window.tgpLazyItems !== undefined) {
                        window.tgpLazyItems.scrollHandle();
                    }
                });
            },

            // Product page accordion
            accordion: function () {
                let $accItem = $('.thegem-accordion__item'),
                    $accItemTitle = $('.thegem-accordion__item-title'),
                    $accItemBody = $('.thegem-accordion__item-body'),
                    $tabNavItem = $('.thegem-tabs__nav-item'),
                    activeClass = 'thegem-accordion__item--active',
                    tabActiveClass = 'thegem-tabs__nav-item--active';

                $accItemTitle.click(function(e){
                    let current = $(this).attr('data-id'),
                        currentAttrvalue = '#' + $(this).attr('data-id');

                    if($(e.target).is('.thegem-accordion__item--active')){
                        $(this).removeClass(activeClass);
                        $('.thegem-accordion__item-body:visible').slideUp(300);
                    } else {
                        $accItemTitle.removeClass(activeClass).filter(this).addClass(activeClass);
                        $accItemBody.slideUp(300).filter(currentAttrvalue).slideDown(300);
                    }

                    $tabNavItem.removeClass(tabActiveClass);
                    $('.thegem-tabs__nav-item[data-id=' + current + ']').addClass(tabActiveClass);
                    if (window.tgpLazyItems !== undefined) {
                        window.tgpLazyItems.scrollHandle();
                    }
                });
            },

            // Product page sticky
            stickyColumn: function () {
                let $wrapper = $('.single-product-content'),
                    $leftColumn = $('.product-page__left-column', $wrapper),
                    $rightColumn = $('.product-page__right-column', $wrapper),

                    stickyInit = (el) => {
                        $(el).sticky({
                            to: 'top',
                            offset: 150,
                            effectsOffset: 0,
                            parent: $wrapper
                        })
                    };

                $(window).load(function () {
                    let $leftColumnHeight = $leftColumn.height(),
                        $rightColumnHeight = $rightColumn.height();

                    if (isSticky && !isMobile && !isTabletPortrait) {
                        $leftColumnHeight > $rightColumnHeight ? stickyInit($rightColumn) : stickyInit($leftColumn);
                    }
                });
            },

            // Product page combobox
            combobox: function () {
                $(".thegem-select").each(function () {
                    let template = '<div class="thegem-combobox">';
                        template += '<div class="thegem-combobox__trigger">' + $('option:selected', this).text() + '</div>';
                        template += '<div class="thegem-combobox__options">';
                        $(this).find("option").each(function () {
                            template += '<div class="thegem-combobox__options-item" data-value="' + $(this).attr("value") + '">' + $(this).html() + '</div>';
                        });
                        template += '</div></div>';

                    if ($(this).parents(".thegem-combobox-wrap").length === 0){
                        $(this).wrap('<div class="thegem-combobox-wrap"></div>');
                    }
                    //$(this).hide();
                    $(this).after(template);
                });

                $(".thegem-combobox__options-item:first-of-type").hover(function () {
                    $(this).parents(".thegem-combobox__options").addClass("hover");
                }, function () {
                    $(this).parents(".thegem-combobox__options").removeClass("hover");
                });

                $(".thegem-combobox__trigger").on("click", function (e) {
                    e.stopPropagation();

                    if ($(this).parents(".thegem-combobox.opened").length != 0) {
                        $(".thegem-combobox").removeClass("opened");
                        return;
                    }

                    $('html').one('click', function () {
                        $(".thegem-combobox").removeClass("opened");
                        return;
                    });

                    $(".thegem-combobox").removeClass("opened");

                    $(this).parents(".thegem-combobox").toggleClass("opened");
                });

                $(".thegem-combobox__options-item").on("click", function () {
                    $(this).parents(".thegem-combobox-wrap").find("select").val($(this).data("value")).change();
                    $(this).parents(".thegem-combobox__options").find(".thegem-combobox__options-item").removeClass("selection");
                    $(this).addClass("selection");
                    $(this).parents(".thegem-combobox").removeClass("opened");
                    $(this).parents(".thegem-combobox").find(".thegem-combobox__trigger").text($(this).text());
                });
            },

            // Product page rating
            rating: function () {
                let isSelected = false,
                    $star = $("#reviews.woocommerce-Reviews .stars a");

                $star.click(function(e){
                    isSelected = true;
                    $(this).prevAll().andSelf().addClass('rating-on');
                    $(this).nextAll().removeClass('rating-on');
                });
                $star.hover(function(){
                    $(this).prevAll().andSelf().addClass('rating-on');
                    $(this).nextAll().removeClass('rating-on');
                });
                $star.mouseout(function(){
                    if (!isSelected) {
                        $star.removeClass('rating-on');
                    }
                    isSelected = false;
                });
            },

            // Product page variable
            productVariable: function () {
                let $column = $('table.variations tr'),
                    $variationForm = $(".variations_form"),
                    $select = $(".thegem-select"),
                    $combobox = $(".thegem-combobox"),
                    $reset = $('.reset_variations'),

                    comboboxInit = () => {
                        productPageScripts.combobox();
                    },

                    comboboxRefresh = () => {
                        comboboxInit();
                        $(".thegem-combobox-wrap").find(".thegem-combobox:last-of-type").remove();
                    };

                $column.each(function (index) {
                    $(this).css('z-index', $column.length - index);
                });

                $variationForm.each(function () {
                    comboboxInit();

                    $(this).on("click", '.thegem-combobox__options-item', function () {
                        comboboxRefresh();
                    });
                });

                $reset.on('click', function(){
                    $variationForm.each(function () {
                        $(this).on('change', '.variations select', function () {
                            comboboxRefresh();

                            let text = $('.thegem-combobox__options-item').eq(0).text();
                            $combobox.find('.thegem-combobox__trigger').text(text);
                        });
                    });
                });

                //huck for double submit form
                if ( window.history.replaceState ) {
                    window.history.replaceState( null, null, window.location.href );
                }
            },

            // Product page quantity
            productQuantity: function () {
                let $form = $('form.cart');

                $('div.quantity:not(.buttons_added)', $form).addClass('buttons_added')
                .append('<button type="button" class="plus" >+</button>')
                .prepend('<button type="button" class="minus" >-</button>');
            },

            // Product page ajax add to cart
            ajaxAddToCart: function () {
                let $wrapper =  $('.single-product-content');

                $wrapper.on('click', '.single_add_to_cart_button', function (e, fragments, cart_hash) {
                    e.preventDefault();

                    let $thisbutton = $(this),
                        $form = $thisbutton.closest('form.cart'),
                        id = $thisbutton.val(),
                        product_qty = $form.find('input[name=quantity]').val() || 1,
                        product_id = $form.find('input[name=product_id]').val() || id,
                        variation_id = $form.find('input[name=variation_id]').val() || 0;

                    let data = {
                        action: 'woocommerce_ajax_add_to_cart',
                        product_id: product_id,
                        product_sku: '',
                        quantity: product_qty,
                        variation_id: variation_id,
                    };

                    if ( $form.find('input[name=variation_id]').length > 0 && variation_id == 0 ) {
                        return false;
                    }

                    $(document.body).trigger('adding_to_cart', [$thisbutton, data]);

                    $.ajax({
                        type: 'post',
                        url: wc_add_to_cart_params.ajax_url,
                        data: data,
                        success: function (response) {
                            if (response.error && response.product_url) {
                                window.location = response.product_url;
                                return;
                            } else {
                                let $addToCartTarget = $(e.currentTarget).parents('.single-product-content');

                                if ($addToCartTarget) {
                                    $('.thegem-popup-notification', $wrapper).removeClass('show');

                                    let $cartPopupAdd = $addToCartTarget.find('.thegem-popup-notification.cart');
                                    $cartPopupAdd.addClass('show');
                                    setTimeout(function () {
                                        $cartPopupAdd.removeClass('show');
                                    }, $cartPopupAdd.data('timing'));
                                }

                                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
                                $('.added_to_cart').hide();
                            }
                        },
                    });

                    return false;
                });
            },

            // Product page ajax add to wishlist
            ajaxAddToWishlist: function () {
                let $wrapper =  $('.single-product-content');

                $wrapper.on('click', '.add_to_wishlist', function () {
                    let $wishlistTarget = $(this).parents('.single-product-content');

                    if ($wishlistTarget) {
                        $('.thegem-popup-notification', $wrapper).removeClass('show');

                        let $wishlistPopupAdd = $wishlistTarget.find('.thegem-popup-notification.wishlist-add');
                        $wishlistPopupAdd.addClass('show');
                        setTimeout(function () {
                            $wishlistPopupAdd.removeClass('show');
                        }, $wishlistPopupAdd.data('timing'));
                    }
                });
            },

            // Product page ajax remove from wishlist
            ajaxRemoveFromWishlist: function () {
                let $wrapper =  $('.single-product-content');

                $wrapper.on('click', '.remove_from_wishlist', function (e, fragments, cart_hash) {
                    let $wishlistTarget = $(this).parents('.single-product-content');

                    if ($wishlistTarget) {
                        $('.thegem-popup-notification', $wrapper).removeClass('show');

                        let $wishlistPopupRemove = $wishlistTarget.find('.thegem-popup-notification.wishlist-remove');
                        $wishlistPopupRemove.addClass('show');
                        setTimeout(function () {
                            $wishlistPopupRemove.removeClass('show');
                        }, $wishlistPopupRemove.data('timing'));
                    }
                });
            },

            // Product page resize
            onResize: function () {
                let $accItem = $('.thegem-accordion__item'),
                    tabView = 'thegem-accordion__item--tab-view',

                    initMobileTabs = () => {
                        $accItem.removeClass(tabView);
                    },

                    revertMobileTabs = () => {
                        $accItem.addClass(tabView);
                    };

                if (isMobile) {
                    initMobileTabs();
                } else {
                    productPageScripts.tabs();
                    revertMobileTabs();
                }

                window.addEventListener("resize", function () {
                    isMobile = window.outerWidth < 768 ? true : false;

                    if (isMobile) {
                        initMobileTabs();
                    } else {
                        productPageScripts.tabs();
                        revertMobileTabs();
                    }
                }, false);
            },
        };

    // Run the function
    $(function () {
        productPageScripts.init();
    });

})(jQuery);