//Old sticky
(function ($) {

    function getScrollY(elem) {
        return window.pageYOffset || document.documentElement.scrollTop;
    }

    function Sticky(el, options) {
        var self = this;
        this.el = el;
        this.$el = $(el);

        this.options = {};
        $.extend(this.options, options);

        self.init();
    }

    $.fn.scSticky = function (options) {
        $(this).each(function () {
            return new Sticky(this, options);
        });
    }

    Sticky.prototype = {
        init: function () {
            var self = this;

            this.$wrapper = false;

            this.$parent = this.getParent();

            $(window).scroll(function () {
                if (self.useSticky()) {
                    self.wrap();
                    self.scroll();
                } else {
                    self.unwrap();
                }
            });

            $(window).resize(function () {
                if (self.useSticky()) {
                    self.wrap();
                    self.scroll();
                } else {
                    self.unwrap();
                }
            });
        },

        wrap: function () {
            if (!this.$wrapper)
                this.$wrapper = this.$el.wrap('<div />').parent();

            this.$wrapper.attr('class', this.$el.attr('class')).addClass('gem-sticky-block').css({
                padding: 0,
                height: this.$el.outerHeight()
            });

            this.$el.css({
                width: this.$wrapper.outerWidth(),
                margin: 0
            });
        },

        getParent: function () {
            return this.$el.parent();
        },

        useSticky: function () {
            var is_sidebar = true;
            if (this.$el.hasClass('sidebar')) {
                if (this.$wrapper) {
                    if (this.$wrapper.outerHeight() > this.$wrapper.siblings('.panel-center:first').outerHeight())
                        is_sidebar = false;
                } else {
                    if (this.$el.outerHeight() > this.$el.siblings('.panel-center:first').outerHeight())
                        is_sidebar = false;
                }
            }

            return $(window).width() > 1000 && is_sidebar;
        },

        unwrap: function () {
            if (this.$el.parent().is('.gem-sticky-block')) {
                this.$el.unwrap();
                this.$wrapper = false;
            }
            this.$el.css({
                width: "",
                top: "",
                bottom: "",
                margin: ""
            });
        },

        scroll: function () {
            var top_offset = parseInt($('html').css('margin-top'));

            var $header = $('#site-header');
            if ($header.hasClass('fixed')) {
                top_offset += $header.outerHeight();
            }

            var scroll = getScrollY();
            var offset = this.$wrapper.offset();
            var parent_offset = this.$parent.offset();
            var parent_bottom = parent_offset.top + this.$parent.outerHeight() - scroll;
            var bottom = $(window).height() - parent_bottom;

            if ((top_offset + this.$el.outerHeight()) >= parent_bottom) {
                this.$el.addClass('sticky-fixed').css({
                    top: "",
                    bottom: bottom,
                    left: offset.left
                });
                return;
            }

            if ((scroll + top_offset) > offset.top) {
                this.$el.addClass('sticky-fixed').css({
                    top: top_offset,
                    bottom: "",
                    left: offset.left
                });
            } else {
                this.$el.removeClass('sticky-fixed').css({
                    top: "",
                    bottom: "",
                    left: ""
                });
            }
        }
    };

}(jQuery));

//New sticky
(function ($) {
    var StickyObject = function (element, userSettings) {
        var $element,
            isSticky = false,
            isFollowingParent = false,
            isReachedEffectsPoint = false,
            elements = {},
            settings;

        var defaultSettings = {
            to: 'top',
            offset: 0,
            effectsOffset: 0,
            parent: false,
            classes: {
                sticky: 'sticky-element',
                stickyActive: 'sticky-element-active',
                stickyEffects: 'sticky-element-effects',
                spacer: 'sticky-element-spacer',
            },
        };

        var initElements = function () {
            $element = $(element).addClass(settings.classes.sticky);

            elements.$window = $(window);

            if (settings.parent) {
                if ('parent' === settings.parent) {
                    elements.$parent = $element.parent();
                } else {
                    elements.$parent = $element.closest(settings.parent);
                }
            }
        };

        var initSettings = function () {
            settings = jQuery.extend(true, defaultSettings, userSettings);
        };

        var bindEvents = function () {
            elements.$window.on({
                scroll: onWindowScroll,
                resize: onWindowResize,
            });
        };

        var unbindEvents = function () {
            elements.$window
            .off('scroll', onWindowScroll)
            .off('resize', onWindowResize);
        };

        var init = function () {
            initSettings();

            initElements();

            bindEvents();

            checkPosition();
        };

        var backupCSS = function ($elementBackupCSS, backupState, properties) {
            var css = {},
                elementStyle = $elementBackupCSS[0].style;

            properties.forEach(function (property) {
                css[property] = undefined !== elementStyle[property] ? elementStyle[property] : '';
            });

            $elementBackupCSS.data('css-backup-' + backupState, css);
        };

        var getCSSBackup = function ($elementCSSBackup, backupState) {
            return $elementCSSBackup.data('css-backup-' + backupState);
        };

        var addSpacer = function () {
            elements.$spacer = $element.clone()
            .addClass(settings.classes.spacer)
            .css({
                visibility: 'hidden',
                transition: 'none',
                animation: 'none',
            });

            $element.after(elements.$spacer);
        };

        var removeSpacer = function () {
            elements.$spacer.remove();
        };

        var stickElement = function () {
            backupCSS($element, 'unsticky', ['position', 'width', 'margin-top', 'margin-bottom', 'top', 'bottom']);

            var css = {
                position: 'fixed',
                width: getElementOuterSize($element, 'width'),
                marginTop: 0,
                marginBottom: 0,
            };

            css[settings.to] = settings.offset;

            css['top' === settings.to ? 'bottom' : 'top'] = '';

            $element
            .css(css)
            .addClass(settings.classes.stickyActive);
        };

        var unstickElement = function () {
            $element
            .css(getCSSBackup($element, 'unsticky'))
            .removeClass(settings.classes.stickyActive);
        };

        var followParent = function () {
            backupCSS(elements.$parent, 'childNotFollowing', ['position']);

            elements.$parent.css('position', 'relative');

            backupCSS($element, 'notFollowing', ['position', 'top', 'bottom']);

            var css = {
                position: 'absolute',
            };

            css[settings.to] = '';

            css['top' === settings.to ? 'bottom' : 'top'] = 0;

            $element.css(css);

            isFollowingParent = true;
        };

        var unfollowParent = function () {
            elements.$parent.css(getCSSBackup(elements.$parent, 'childNotFollowing'));

            $element.css(getCSSBackup($element, 'notFollowing'));

            isFollowingParent = false;
        };

        var getElementOuterSize = function ($elementOuterSize, dimension, includeMargins) {
            var computedStyle = getComputedStyle($elementOuterSize[0]),
                elementSize = parseFloat(computedStyle[dimension]),
                sides = 'height' === dimension ? ['top', 'bottom'] : ['left', 'right'],
                propertiesToAdd = [];

            if ('border-box' !== computedStyle.boxSizing) {
                propertiesToAdd.push('border', 'padding');
            }

            if (includeMargins) {
                propertiesToAdd.push('margin');
            }

            propertiesToAdd.forEach(function (property) {
                sides.forEach(function (side) {
                    elementSize += parseFloat(computedStyle[property + '-' + side]);
                });
            });

            return elementSize;
        };

        var getElementViewportOffset = function ($elementViewportOffset) {
            var windowScrollTop = elements.$window.scrollTop(),
                elementHeight = getElementOuterSize($elementViewportOffset, 'height'),
                viewportHeight = innerHeight,
                elementOffsetFromTop = $elementViewportOffset.offset().top,
                distanceFromTop = elementOffsetFromTop - windowScrollTop,
                topFromBottom = distanceFromTop - viewportHeight;

            return {
                top: {
                    fromTop: distanceFromTop,
                    fromBottom: topFromBottom,
                },
                bottom: {
                    fromTop: distanceFromTop + elementHeight,
                    fromBottom: topFromBottom + elementHeight,
                },
            };
        };

        var stick = function () {
            addSpacer();

            stickElement();

            isSticky = true;

            $element.trigger('sticky:stick');
        };

        var unstick = function () {
            unstickElement();

            removeSpacer();

            isSticky = false;

            $element.trigger('sticky:unstick');
        };

        var checkParent = function () {
            var elementOffset = getElementViewportOffset($element),
                isTop = 'top' === settings.to;

            if (isFollowingParent) {
                var isNeedUnfollowing = isTop ? elementOffset.top.fromTop > settings.offset : elementOffset.bottom.fromBottom < -settings.offset;

                if (isNeedUnfollowing) {
                    unfollowParent();
                }
            } else {
                var parentOffset = getElementViewportOffset(elements.$parent),
                    parentStyle = getComputedStyle(elements.$parent[0]),
                    borderWidthToDecrease = parseFloat(parentStyle[isTop ? 'borderBottomWidth' : 'borderTopWidth']),
                    parentViewportDistance = isTop ? parentOffset.bottom.fromTop - borderWidthToDecrease : parentOffset.top.fromBottom + borderWidthToDecrease,
                    isNeedFollowing = isTop ? parentViewportDistance <= elementOffset.bottom.fromTop : parentViewportDistance >= elementOffset.top.fromBottom;

                if (isNeedFollowing) {
                    followParent();
                }
            }
        };

        var checkEffectsPoint = function (distanceFromTriggerPoint) {
            if (isReachedEffectsPoint && -distanceFromTriggerPoint < settings.effectsOffset) {
                $element.removeClass(settings.classes.stickyEffects);

                isReachedEffectsPoint = false;
            } else if (!isReachedEffectsPoint && -distanceFromTriggerPoint >= settings.effectsOffset) {
                $element.addClass(settings.classes.stickyEffects);

                isReachedEffectsPoint = true;
            }
        };

        var checkPosition = function () {
            var offset = settings.offset,
                distanceFromTriggerPoint;

            if (isSticky) {
                var spacerViewportOffset = getElementViewportOffset(elements.$spacer);

                distanceFromTriggerPoint = 'top' === settings.to ? spacerViewportOffset.top.fromTop - offset : -spacerViewportOffset.bottom.fromBottom - offset;

                if (settings.parent) {
                    checkParent();
                }

                if (distanceFromTriggerPoint > 0) {
                    unstick();
                }
            } else {
                var elementViewportOffset = getElementViewportOffset($element);

                distanceFromTriggerPoint = 'top' === settings.to ? elementViewportOffset.top.fromTop - offset : -elementViewportOffset.bottom.fromBottom - offset;

                if (distanceFromTriggerPoint <= 0) {
                    stick();

                    if (settings.parent) {
                        checkParent();
                    }
                }
            }

            checkEffectsPoint(distanceFromTriggerPoint);
        };

        var onWindowScroll = function () {
            checkPosition();
        };

        var onWindowResize = function () {
            if (!isSticky) {
                return;
            }

            unstickElement();

            stickElement();

            if (settings.parent) {
                // Force recalculation of the relation between the element and its parent
                isFollowingParent = false;

                checkParent();
            }
        };

        this.destroy = function () {
            if (isSticky) {
                unstick();
            }

            unbindEvents();

            $element.removeClass(settings.classes.sticky);
        };

        init();
    };

    $.fn.sticky = function (settings) {
        var isCommand = 'string' === typeof settings;

        this.each(function () {
            var $this = $(this);

            if (!isCommand) {
                $this.data('sticky', new StickyObject(this, settings));

                return;
            }

            var instance = $this.data('sticky');

            if (!instance) {
                throw Error('Trying to perform the `' + settings + '` method prior to initialization');
            }

            if (!instance[settings]) {
                throw ReferenceError('Method `' + settings + '` not found in sticky instance');
            }

            instance[settings].apply(instance, Array.prototype.slice.call(arguments, 1));

            if ('destroy' === settings) {
                $this.removeData('sticky');
            }
        });

        return this;
    };

    window.StickyObject = StickyObject;
})(jQuery);