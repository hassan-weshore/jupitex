(function($){
	$.fn.stickyColumn = function (options) {
		var defaults = {
				mobileSize: 768
			},
			settings = $.extend({}, defaults, options);


		return this.each(function () {
			var $this = $(this),
				$thisWaypoint = null,
				offset = parseInt($this.data('sticky-offset')),
				heightContainer = $this.outerHeight(),
				topContainer = $this.offset().top,
				stickyElement = $this.children('.wpb_wrapper'),
				stickyElementHeight = stickyElement.outerHeight(),
				isSticky = $this.hasClass('sticky'),
				isMobile = $(window).width() < settings.mobileSize,
				$page = $('#page');

			var setPosition = function () {
				var pageScrollTop = $page.scrollTop(),
					scrollVal = $(window).scrollTop() + pageScrollTop,
					stickyElementPos = scrollVal + stickyElementHeight,
					containerPos = topContainer + heightContainer;
					heightContainer = $this.outerHeight();

				if (isSticky && !isMobile) {
					var pos = heightContainer - (scrollVal - topContainer + stickyElementHeight + offset);
					if (pos < 0) {
						stickyElement.css({
							top: offset + pos + pageScrollTop
						});
					} else {
						stickyElement.css({
							top: offset + pageScrollTop
						});
					}
				}
			}

			var destroy = function () {
				if (isSticky) {
					$this.removeClass('sticky');
					stickyElement.css({
						top: '',
						width: ''
					});
				}
			}
			var reinit = function () {
				if (isSticky) {
					heightContainer = $this.outerHeight();
					topContainer = $this.offset().top;
					stickyElementHeight = stickyElement.outerHeight();

					$this.removeClass('sticky');
					stickyElement.css({
						top: '',
						width: ''
					});
					stickyElement.css({
						top: offset,
						width: stickyElement.width()
					});
					$this.addClass('sticky');
					setPosition();

				}
			}
			var init = function () {
				$thisWaypoint = $this.waypoint(function (direction) {
					if (!isMobile) {
						stickyElement.css({
							top: direction === 'down' ? offset : '',
							width: direction === 'down' ? stickyElement.width() : ''
						});

						$this.toggleClass('sticky', direction === 'down');
						isSticky = $this.hasClass('sticky');
						setPosition();
					}

				}, {
					offset: offset + 'px'
				});
			}

			init();

			$(window).on('scroll', function () {
				setPosition();
			});

			$(window).on('resize', function () {
				isMobile = $(window).width() < settings.mobileSize;
				if (isMobile) {
					destroy();
				} else {
					reinit();
				}
			});

		});
	}

	$(window).on('load', function () {
		$('[data-sticky-offset]').stickyColumn();
	});

})(jQuery);
