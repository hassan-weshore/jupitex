(function($) {

    if (typeof window.CustomEvent !== "function") {
        function CustomEvent( event, params ) {
            params = params || { bubbles: false, cancelable: false, detail: undefined };
            var evt = document.createEvent( 'CustomEvent' );
            evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
            return evt;
        }
        CustomEvent.prototype = window.Event.prototype;
        window.CustomEvent = CustomEvent;
    }

    function initTheGemFullpage() {
        window.gemSettings.fullpageEnabled = true;

        let fullpageId = '#thegem-fullpage';
        let sectionSelector = '.thegem-fp-section';
        let $fullpage = $(fullpageId);

        // Init sections
        $('section.elementor-top-section', $fullpage).addClass(sectionSelector.replace('.', ''));

        let anchorAttrName = 'data-anchor',
            $body = $('body'),
            $page = $('#page'),
            isDisabledDots = $body.hasClass('thegem-fp-disabled-dots'),
            isDisabledTooltips = $body.hasClass('thegem-fp-disabled-tooltips'),
            isEnableAnchor = $(sectionSelector+'['+anchorAttrName+']').length !== 0,
            isFixedBackground = $body.hasClass('thegem-fp-fixed-background'),
            isDisabledMobile = $body.hasClass('thegem-fp-disabled-mobile'),
            isEnableContinuous = $body.hasClass('thegem-fp-enable-continuous'),
            isEnabledParallax = $body.hasClass('thegem-fp-parallax'),
            isPagePadding = $body.hasClass('thegem-fp-page-padding'),
            menuSelector = '#primary-menu',
            isResponsive = false,
            $footer = $('footer').remove().clone();

        // Fullpage options
        let options = {
            sectionSelector: sectionSelector,
            verticalCentered: false,
            navigation: !isDisabledDots,
            autoScrolling: true,
            navigationTooltips: isDisabledTooltips ? [''] : [],
            anchors: [],
            lockAnchors: !isEnableAnchor,
            css3: !isFixedBackground,
            responsiveWidth: isDisabledMobile ? 769 : 0,
            continuousVertical: isEnableContinuous,
            scrollingSpeed: 900,
            licenseKey: ''
        };

        if (isEnabledParallax) {
            options.scrollingSpeed = 1000;

            if ($(window).outerWidth() >= options.responsiveWidth) {
                $page.css('height', $(window).innerHeight()+'px');
            }
        }

        if (isEnableAnchor) {
            let anchorItems = [];
            $fullpage.find(sectionSelector).each(function(idx, item) {
                let anchor = $(item).attr(anchorAttrName);
                if (anchor===undefined || anchor===$(item).attr('id')) {
                    $(item).attr(anchorAttrName, 'section-'+(idx+1));
                }
                anchorItems.push($(item).attr(anchorAttrName));
            });

            $('li', menuSelector).each(function(idx, item) {
                let link = $('a', item);
                if (link.length) {
                    let anchor = link.attr('href').replace('#','');
                    if (anchorItems.indexOf(anchor)!==-1) {
                        $(item).attr('data-menuanchor', anchor);
                    }
                }
            });

            options.menu = menuSelector;
            options.anchors = anchorItems;
        }

        options.onLeave = function(origin, destination, direction) {
            setTimeout(function () { sendScrollEvent(); }, 100);

            if (isEnableAnchor) {
                activateMenuElement(menuSelector, destination);
            }

            if (isEnabledParallax && direction) {
                if (direction === 'up') {
                    if (!$(origin.item).hasClass('fp-thegem-footer')) {
                        $(origin.item).addClass('fp-prev-down');
                        $(destination.item).addClass('fp-next-down');
                    } else {
                        $(destination.item).css('transform', 'translateY(0)');
                        $('.fp-thegem-footer-inner', origin.item).css('transform', 'translateY(0)');
                    }
                }
                if (direction === 'down') {
                    if (!$(destination.item).hasClass('fp-thegem-footer')) {
                        $(origin.item).addClass('fp-prev-up');
                        $(destination.item).addClass('fp-next-up');
                    } else {
                        let footerHeight = $footer.height();
                        $(origin.item).css({'transition': 'transform 1s ease', 'transform': 'translateY(-'+(footerHeight * 0.5)+'px)'});
                        $('.fp-thegem-footer-inner', destination.item).css('transform', 'translateY(-'+footerHeight+'px)');
                    }
                }
            }

            if ($('.portfolio-grid', destination.item).length) {
                $('.portfolio-grid', destination.item).each(function (index, item) {
                    if (item.className.indexOf('item-animation')===-1) {
                        setTimeout(function () {
                            $(item).itemsAnimations('instance').reinitItems($('.portfolio-set .portfolio-item', $(item)));
                            $(item).initPortfoliosGrid();
                        });
                    }
                });
            }

            // init thegem gallery grid
            if ($('.gem-gallery-grid', destination.item).length) {
                $('.gem-gallery-grid', destination.item).each(function (index, item) {
                    setTimeout(function () {
                        $(item).itemsAnimations('instance').reinitItems($('.gallery-set .gallery-item', $(item)));
                        $(item).initGalleriesGrid();
                    });
                });
            }

            if (!$(destination.item).hasClass('fp-section-initialized')) {
                if ($('.gem-clients-grid-carousel', destination.item).length > 0 && isEnabledParallax) {
                    $(destination.item).updateClientsGrid();
                }
            }

        };

        options.afterLoad = function(origin, destination, direction) {
            if (destination.index === 0 && !$(destination.item).hasClass('fp-section-initialized')) {
                $(destination.item).addClass('fp-section-initialized');
            }

            if (isEnableAnchor) {
                activateMenuElement(menuSelector, destination);
            }

            if (isEnabledParallax && direction) {
                $(sectionSelector).removeClass('fp-prev-down fp-next-down fp-prev-up fp-next-up');
            }

            if (destination.index > 0 && !$(destination.item).hasClass('fp-section-initialized')) {
                $(destination.item).addClass('fp-section-initialized');

                // init elementor items
                if (window.elementorFrontend) {
                    Waypoint.refreshAll();
                }

                // init thegem lazyLoading
                $('.lazy-loading', destination.item).each(function(index, item) {
                    $.lazyLoading();
                });

                if ($('.portfolio-grid', destination.item).length) {
                    $('.portfolio-grid', destination.item).each(function (index, item) {
                        if (item.className.indexOf('item-animation')!==-1) {
                            $(item).itemsAnimations('instance').reinitItems($('.portfolio-set .portfolio-item', $(item)));
                            $(item).initPortfoliosGrid();
                        }
                    });
                }

                if ($('.blog:not(body,.blog-style-timeline_new,.blog-style-masonry)', destination.item).length) {
                    $('.blog:not(body,.blog-style-timeline_new,.blog-style-masonry)', destination.item).each(function (index, item) {
                        $(item).itemsAnimations('instance').reinitItems($('article', $(item)));
                        setTimeout(function () {
                            $(item).initBlogGrid();
                        });
                    });
                }

            }

            initVideoBackground(destination);
        };

        options.afterResponsive = function (state) {
            isResponsive = state;

            window.gemSettings.fullpageEnabled = isResponsive && !isDisabledMobile;

            if (isResponsive && isEnabledParallax) {
                isEnabledParallax = false;
            }
        };

        if ($fullpage.find(sectionSelector).length > 0) {
            if (isPagePadding) {
                fixHeightSection(isEnabledParallax, options);
            }

            appendFooter();

            new window.fullpage(fullpageId, options);

            if (isPagePadding && parseInt($page.css('margin-right')) > 0) {
                fixNavPosition();
            }

            $(window).on('resize', function () {
                fixNavPosition();

                if (isEnabledParallax || isPagePadding) {
                    fixHeightSection(isEnabledParallax, options);
                }
            });
        }

        function appendFooter() {
            if ($footer.length && !isEnableContinuous) {
                let $fullpageFooter = $('<div/>', {class: sectionSelector.replace('.', ' ') + ' fp-section'});

                if (!isEnabledParallax) {
                    $fullpageFooter.addClass('fp-auto-height');
                } else {
                    $fullpageFooter.addClass('fp-thegem-footer');
                    $footer = $('<div/>', {class: 'fp-thegem-footer-inner'}).append($footer);
                }

                $fullpageFooter.append($footer);
                $('.elementor-section-wrap', $fullpage).append($fullpageFooter);
            }
        }

    }

    function sendScrollEvent() {
        document.dispatchEvent(new window.CustomEvent('fullpage-updated'));
    }

    function activateMenuElement(menuSelector, destination){
        $('li', menuSelector).removeClass('menu-item-active');
        $(menuSelector).find('[data-menuanchor="'+destination.anchor+'"]', 'li').addClass('menu-item-active');
    }

    function initVideoBackground(destination) {
        let $gemVideoBackground = $('video', destination.item);
        if ($gemVideoBackground.length && $gemVideoBackground[0].paused) {
            $gemVideoBackground[0].play();
        }
    }

    function fixNavPosition() {
        $('#fp-nav').css('margin-right', $('#page').css('margin-right'));
    }

    function fixHeightSection(isEnabledParallax, options) {
        if ($(window).outerWidth() >= options.responsiveWidth) {
            let $page = $('#page'),
                pageHeight =  $(window).innerHeight(),
                pageMarginTop = parseInt($page.css('margin-top')),
                pageMarginBottom = parseInt($page.css('margin-bottom'));

            pageHeight = pageHeight - (pageMarginTop + pageMarginBottom);
            $page.css('height', pageHeight+'px');

            if (isEnabledParallax) {
                $('.fp-section').css('height', pageHeight+'px');
            }
        }
    }

    initTheGemFullpage();

})(window.jQuery);
