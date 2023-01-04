(function($){

	$.pageScroller = {

		items: $('.scroller-block'),
		navigationPane: null,
		activeItem: null,

		init: function() {
			var that = this;
			$('body').css({overflow: 'hidden'});
			if(that.items.length) {
				that.navigationPave = $('<div class="page-scroller-nav-pane"></div>');
				that.navigationPave.appendTo($('body'));
				that.items.each(function(index) {
					var $target = $(this);
					$('<a href="javascript:void(0);" class="page-scroller-nav-item"></a>')
						.appendTo(that.navigationPave)
						.data('scroller-target', $target)
						.on('click', function(e) {
							e.preventDefault();
							that.goTo(index);
						});
				});
			}
			$('html, body').scrollTop(0);
			$(window).on('resize', function() {
				that.update();
			});
			$('html, body').on('scroll', function() {
				that.update();
			});
		},

		update: function() {
			this.goTo(this.getActiveNum());
		},

		getScrollY: function() {
			return window.pageYOffset || document.documentElement.scrollTop;
		},

		getActive: function() {
			var that = this;
			that.items.each(function() {
				var target_top = $(this).offset().top;
				if(that.getScrollY() + 1 >= target_top && that.getScrollY() + 1 <= target_top + $(this).outerHeight()) {
					that.activeItem = $(this);
				}
			});
			if(that.activeItem) {
				$('.page-scroller-nav-item.active', that.navigationPane).removeClass('active');
				$('.page-scroller-nav-item', that.navigationPane).eq(that.items.index(that.activeItem)).addClass('active');
			}
			return that.activeItem;
		},

		getActiveNum: function() {
			if(this.getActive()) {
				return this.items.index(this.getActive());
			}
			return -1;
		},

		next: function() {
			this.goTo(this.getActiveNum() + 1);
		},

		prev: function() {
			this.goTo(this.getActiveNum() - 1);
		},

		goTo: function(num) {
			var that = this;
			if(num == -1 || num >= this.items.length) return;
			target_top = this.items.eq(num).offset().top;
			if(!$('html, body').is(':animated')) {
				$('html, body').stop(true, true).animate({scrollTop:target_top}, 1000, function() {
					that.getActive();
				});
			}
		},

	};

	$(function() {
		if(!$('body').hasClass('compose-mode')) {
			$.pageScroller.init();
			$('body').on('mousewheel', function(event, delta, deltaX, deltaY) {
				event.preventDefault();
				if(delta > 0) {
					$.pageScroller.prev();
				} else {
					$.pageScroller.next();
				}
			});
		}
	});

})(jQuery);