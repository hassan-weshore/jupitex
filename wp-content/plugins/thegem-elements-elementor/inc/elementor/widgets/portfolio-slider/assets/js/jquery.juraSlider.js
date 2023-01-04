(function ($) {

	function Slider(el, options) {
		var self = this;
		this.el = el;
		this.$el = $(el);

		this.options = {
			element: 'li',
			margin: 0,
			delay: 100,
			duration: 200,
			nextPageDelay: 300,
			prevButton: false,
			nextButton: false,
			loop: true,
			afterInit: false,
			autoscroll: false,
			type: 'dynamic'
		};
		$.extend(this.options, options);
		self.initialize(true);
	}

	$.fn.reverse = [].reverse;

	$.fn.juraSlider = function(options) {
		return new Slider(this.get(0), options);
	}

	Slider.prototype = {
		initialize: function(first_init) {
			var self = this;

			if (first_init == undefined) {
				first_init = false;
			}

			this.afterAnimation(false);

			var first_element_height = this.$el.find(this.options.element + ':first').outerHeight();			

			var padding_left = parseInt(this.$el.parent().css('padding-left'));
			var padding_right = parseInt(this.$el.parent().css('padding-right'));

			this.$el.css({
				whiteSpace: 'nowrap',
				left: padding_left,
				right: padding_right,
				top: 0,
				bottom: 0,
				height: first_element_height,
				position: 'absolute',
				clip: 'rect(auto, auto, ' + (first_element_height + 60) + 'px, auto)'
			});

			this.$el.parent().css({
				height: first_element_height,
				position: 'relative'
			});

			this.$el.find(this.options.element).css({
				margin: 0,
				position: 'absolute',
				left: this.$el.outerWidth(),
				top: 0,
				zIndex: 1
			}).removeClass('leftPosition currentPosition currentPosition-first currentPosition-last').addClass('rightPosition');

			if (first_init && this.options.nextButton)
				this.options.nextButton.click(function() {
					self.triggerNext(false);
				});

			if (first_init && this.options.prevButton)
				this.options.prevButton.click(function() {
					self.triggerPrev();
				});

			if (first_init) {
				$(window).resize(function() {
					self.initialize(false);
				});
			}

			if (first_init && $.isFunction(this.options.afterInit))
				this.options.afterInit();

			this.triggerNext(true, this.options.type == 'one' ? true : !first_init);

			this.$el.hover(
				function() {
					self.afterAnimation(false);
				},
				function(){
					self.afterAnimation();
				}
			);

		},

		beforeAnimation: function() {
			this.is_animation = true;
			if (this.autoscrollHandler) {
				clearInterval(this.autoscrollHandler);
				this.autoscrollHandler = false;
			}
		},

		afterAnimation: function(start) {
			var self = this;

			start = start === undefined ? true : false;
			this.is_animation = false;
			if (this.autoscrollHandler) {
				clearInterval(this.autoscrollHandler);
				this.autoscrollHandler = false;
			}
			if (start && this.options.autoscroll) {
				this.autoscrollHandler = setInterval(function() {
					self.triggerNext(false);
				}, this.options.autoscroll);
			}
		},

		getNextCount: function() {
			var self = this;
			var count = 0;
			var next_width = 0;
			var index = 0;
			var el_width = parseFloat(getComputedStyle(this.el, '').getPropertyValue('width'));
			var new_width = 0;
			this.$el.find(this.options.element + '.rightPosition').each(function() {
				var width = parseFloat(getComputedStyle(this, '').getPropertyValue('width'));
				if (index > 0)
					width += self.options.margin;
				new_width = next_width + width;
				if (new_width > el_width)
					return false;
				next_width = next_width + width;
				count += 1;
				index += 1;
			});
			if (this.options.loop && new_width < el_width) {
				this.$el.find(this.options.element + '.leftPosition').each(function() {
					var width = parseFloat(getComputedStyle(this, '').getPropertyValue('width'));
					if (index > 0)
						width += self.options.margin;
					new_width = next_width + width;
					if (new_width > el_width)
						return false;
					$(this).css({left: el_width}).removeClass('leftPosition').addClass('rightPosition').appendTo(self.$el);
					next_width = next_width + width;
					count += 1;
					index += 1;
				});
			}
			return [count, next_width];
		},

		getNextRightItem: function() {
			var firstRightItem = this.$el.find(this.options.element + '.rightPosition:first');
			if (firstRightItem.length) {
				return firstRightItem[0];
			}
			if (!this.options.loop) {
				return null;
			}

			var firstLeftItem = this.$el.find(this.options.element + '.leftPosition:first');
			if (!firstLeftItem.length) {
				return null;
			}

			firstLeftItem
				.addClass('rightPosition')
				.removeClass('leftPosition')
				.css('left', this.$el.outerWidth());

			this.$el.append(firstLeftItem);

			return firstLeftItem[0];
		},

		triggerNextOne: function() {
			var app = this,
				$currentItems = this.$el.find(this.options.element + '.currentPosition'),
				firstCurrentItemWidth = parseFloat($currentItems.first().outerWidth())

			console.log($currentItems  + firstCurrentItemWidth);

			var rightItem = this.getNextRightItem();
			if (rightItem == null) {
				return false;
			}

			$currentItems.push(rightItem);
			var count = $currentItems.length;

			app.beforeAnimation();

			$currentItems.removeClass('currentPosition-last').each(function(index) {
				var itemLeft = parseFloat($(this).css('left'));
				$(this).addClass('slider-animation' + (index == count - 1 ? ' currentPosition-last' : ''))
					.animate({left: itemLeft - firstCurrentItemWidth}, {
						duration: app.options.duration,
						queue: false,
						complete: function() {
							$(this).removeClass('slider-animation');

							if (index == 0) {
								$(this).removeClass('currentPosition currentPosition-first').addClass('leftPosition');
							}

							if (index == 1) {
								$(this).addClass('currentPosition-first');
							}

							if (index == count - 1) {
								$(this).removeClass('rightPosition').addClass('currentPosition');
								app.afterAnimation();
							}
						}
					});
			});
		},

		triggerNext: function(init, without_transition) {
			if (this.is_animation)
				return false;

			if (without_transition == undefined) {
				without_transition = false;
			}

			if (!init && this.options.type == 'one') {
				this.triggerNextOne();
				return false;
			}

			var self = this;
			var info = this.getNextCount();
			if (init && info[0] == this.$el.find(this.options.element).size()) {
				if (this.options.nextButton)
					this.options.nextButton.hide();
				if (this.options.prevButton)
					this.options.prevButton.hide();
			}
			if (info[0] < 1)
				return false;
				this.beforeAnimation();

			this.hideLeft();

			setTimeout(function() {
				self.showNext(info, without_transition);
			}, without_transition ? 1 : this.options.nextPageDelay);
		},

		hideLeft: function() {
			var delay = 0;
			var app = this;
			app.$el.find(app.options.element + '.currentPosition').each(function() {
				var self = this;
				setTimeout(function() {
					var offset = $(self).outerWidth();
					$(self).addClass('slider-animation').animate({left: -offset}, {
						duration: app.options.duration,
						queue: false,
						complete: function() {
							$(this).removeClass('currentPosition slider-animation currentPosition-first currentPosition-last').addClass('leftPosition');
						}
					});
				}, delay);
				delay += app.options.delay;
			});
		},

		showNext: function(info, without_transition) {
			var app = this;
			if (info[0] < 1)
				return false;

			var offset = (app.$el.width() - info[1]) / 2;
			var delay = 0;
			var index = 0;
			app.$el.find(app.options.element + '.rightPosition:lt(' + info[0] + ')').each(function() {
				var self = this;
				if (without_transition) {
					$(self)
						.removeClass('leftPosition rightPosition')
						.addClass('currentPosition')
						.css({
							left: offset
						});
				} else {
					app.showElement(self, offset, delay, index, info[0]);
				}
				delay += app.options.delay;
				offset += $(self).outerWidth() + app.options.margin;
				index += 1;
			});

			if (without_transition) {
				app.afterAnimation();
			}
		},

		showElement: function(element, offset, delay, index, count) {
			var app = this;
			setTimeout(function() {
				$(element).addClass('slider-animation' + (index == 0 ? ' currentPosition-first' : '') + (index == count - 1 ? ' currentPosition-last' : '')).animate({left: offset}, {
					duration: app.options.duration,
					queue: false,
					complete: function() {
						$(this).removeClass('rightPosition leftPosition slider-animation').addClass('currentPosition');
						if (index == count - 1) {
							app.afterAnimation();
						}
					}
				});
			}, delay);
		},

		getPrevCount: function() {
			var self = this;
			var count = 0;
			var prev_width = 0;
			var index = 0;
			var el_width = parseFloat(getComputedStyle(this.el, '').getPropertyValue('width'));
			var new_width = 0;
			this.$el.find(this.options.element + '.leftPosition').reverse().each(function() {
				var width = parseFloat(getComputedStyle(this, '').getPropertyValue('width'));
				if (index > 0)
					width += self.options.margin;
				new_width = prev_width + width;
				if (new_width > el_width)
					return false;
				prev_width = prev_width + width;
				count += 1;
				index += 1;
			});
			if (this.options.loop && new_width < el_width) {
				this.$el.find(this.options.element + '.rightPosition').reverse().each(function() {
					var width = parseFloat(getComputedStyle(this, '').getPropertyValue('width'));
					if (index > 0)
						width += self.options.margin;
					new_width = prev_width + width;
					if (new_width > el_width)
						return false;
					$(this).css({left: -width}).removeClass('rightPosition').addClass('leftPosition').prependTo(self.$el);
					prev_width = prev_width + width;
					count += 1;
					index += 1;
				});
			}
			return [count, prev_width];
		},

		getPrevLeftItem: function() {
			var lastLeftItem = this.$el.find(this.options.element + '.leftPosition:last');
			if (lastLeftItem.length) {
				return lastLeftItem[0];
			}
			if (!this.options.loop) {
				return null;
			}

			var lastRightItem = this.$el.find(this.options.element + '.rightPosition:last');
			if (!lastRightItem.length) {
				return null;
			}

			lastRightItem
				.removeClass('rightPosition')
				.addClass('leftPosition')
				.css('left', -lastRightItem.outerWidth());

			this.$el.prepend(lastRightItem);

			return lastRightItem[0];
		},

		triggerPrevOne: function() {
			var app = this,
				$currentItems = this.$el.find(this.options.element + '.currentPosition'),
				lastCurrentItemWidth = parseFloat($currentItems.last().outerWidth())

			var leftItem = this.getPrevLeftItem();
			if (leftItem == null) {
				return false;
			}

			$currentItems.reverse();
			$currentItems.push(leftItem);
			var count = $currentItems.length;

			app.beforeAnimation();

			$currentItems.removeClass('currentPosition-first').each(function(index) {
				var itemLeft = parseFloat($(this).css('left'));
				$(this).addClass('slider-animation' + (index == count - 1 ? ' currentPosition-first' : ''))
					.animate({left: itemLeft + lastCurrentItemWidth}, {
						duration: app.options.duration,
						queue: false,
						complete: function() {
							$(this).removeClass('slider-animation');

							if (index == 0) {
								$(this).removeClass('currentPosition currentPosition-last').addClass('rightPosition');
							}

							if (index == 1) {
								$(this).addClass('currentPosition-last');
							}

							if (index == count - 1) {
								$(this).removeClass('leftPosition').addClass('currentPosition');
								app.afterAnimation();
							}
						}
					});
			});
		},

		triggerPrev: function() {
			if (this.is_animation) {
				return false;
			}

			if (this.options.type == 'one') {
				this.triggerPrevOne();
				return false;
			}

			var self = this;
			var info = this.getPrevCount();
			if (info[0] < 1)
				return false;

			this.beforeAnimation();

			this.hideRight();

			setTimeout(function() {
				self.showPrev(info);
			}, this.options.nextPageDelay);
		},

		hideRight: function() {
			var delay = 0;
			var app = this;
			var offset = app.$el.width();
			app.$el.find(app.options.element + '.currentPosition').reverse().each(function() {
				var self = this;
				setTimeout(function() {
					$(self).addClass('slider-animation').animate({left: offset}, {
						duration: app.options.duration,
						queue: false,
						complete: function() {
							$(this).removeClass('currentPosition slider-animation currentPosition-first currentPosition-last').addClass('rightPosition');
						}
					});
				}, delay);
				delay += app.options.delay;
			});
		},

		showPrev: function(info) {
			var app = this;
			if (info[0] < 1)
				return false;

			var offset = info[1] + (app.$el.width() - info[1]) / 2;
			var delay = 0;
			var index = 0;

			app.$el.find(app.options.element + '.leftPosition').slice(-info[0]).reverse().each(function() {
				var self = this;
				offset -= $(self).outerWidth();
				if (index > 0)
					offset -= app.options.margin;
				app.showElement(self, offset, delay, index, info[0]);
				delay += app.options.delay;
				index += 1;
			});
		}
	};

}(jQuery));
