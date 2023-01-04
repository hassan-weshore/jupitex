(function($){
	$.fn.parallaxHorizontal = function(options) {
		this.each(function(){
			return new ParallaxHorizontal(this, options);
		});
	}

	function ParallaxHorizontal(el, options){
		var that = this;
		this.$window = $(window);
		this.el = el;
		this.$el = $(el);
		this.$parent = $(el).parent();

		this.defaults = {
			fps: 60,
			basePoint: .5,
			duration: 500,
			speed: 278 / 500,
			easing: 'swing'// 'easeOutElastic'
		};

		// Apply options
		if (el.onclick != undefined){
			options = $.extend({}, el.onclick() || {}, typeof options == 'object' && options);
			this.$el.removeProp('onclick');
		}
		options = $.extend({}, this.defaults, typeof options == 'object' && options);
		this.options = options;
		// Count sizes
		this.containerWidth = this.$el.outerWidth();
		this.containerHeight = this.$el.outerHeight();
		this.windowHeight = this.$window.height();
		// Count frame rate
		this._frameRate = Math.round(1000 / this.options.fps);
		// To fix IE bug that handles mousemove before mouseenter
		this.mouseInside = false;

		var img = new Image();
		img.onload = function () {
			that.bgWidth = this.width;

			// Mouse events for desktop browsers
			if ( ! ('ontouchstart' in window) || ! ('DeviceOrientationEvent' in window)){
				that.$parent
					.mouseenter(function(e){
						that.mouseInside = true;
						var offset = that.bgPosition(),
							coord = (e.pageX - offset.left) / that.bgWidth;
						that.cancel();
						that._hoverAnimation = true;
						that._hoverFrom = that.now;
						that._hoverTo = coord;
						that.start(that._hoverTo);
					})
					.mousemove(function(e){
						// To fix IE bug that handles mousemove before mouseenter
						if ( ! that.mouseInside) return;
						// Reducing processor load for too frequent event calls
						if (that._lastFrame + that._frameRate > Date.now()) return;
						var offset = that.bgPosition(),
							coord = (e.pageX - offset.left) / that.bgWidth;
						// Handle hover animation
						if (that._hoverAnimation){
							that._hoverTo = coord;
							return;
						}
						that.set(coord);
						that._lastFrame = Date.now();
					})
					.mouseleave(function(e){
						that.mouseInside = false;
						that.cancel();
						that.start(that.options.basePoint);
					});
			}
			// Handle resize
			that.$window.resize(function(){ that.handleResize(); });
			// Device orientation events for touch devices
			that._orientationDriven = ('ontouchstart' in window && 'DeviceOrientationEvent' in window);
			if (that._orientationDriven){
				// Check if container is visible
				that._checkIfVisible();
				window.addEventListener("deviceorientation", function(e){
					// Reducing processor load for too frequent event calls
					if ( ! that.visible || that._lastFrame + that._frameRate > Date.now()) return;
					that._deviceOrientationChange(e);
					that._lastFrame = Date.now();
				});
				that.$window.resize(function(){ that._checkIfVisible(); });
				that.$window.scroll(function(){ that._checkIfVisible(); });
			}
			// Set to basepoint
			that.set(that.options.basePoint);
			that._lastFrame = Date.now();
		};

		img.src = (this.$el.css('background-image') || '').replace(/url\(['"]*(.*?)['"]*\)/g, '$1');
	};

	ParallaxHorizontal.prototype = {
		_deviceOrientationChange: function(e){
			var gamma = e.gamma,
				beta = e.beta,
				x, y;
			switch (window.orientation){
				case -90:
					beta = Math.max(-45, Math.min(45, beta));
					x = (beta + 45) / 90;
					break;
				case 90:
					beta = Math.max(-45, Math.min(45, beta));
					x = (45 - beta) / 90;
					break;
				case 180:
					gamma = Math.max(-45, Math.min(45, gamma));
					x = (gamma + 45) / 90;
					break;
				case 0:
				default:
					// Upside down
					if (gamma < -90 || gamma > 90) gamma = Math.abs(e.gamma)/e.gamma * (180 - Math.abs(e.gamma));
					gamma = Math.max(-45, Math.min(45, gamma));
					x = (45 - gamma) / 90;
					break;
			}
			this.set(x);
		},

		bgPosition: function() {
			var position = this.$el.css('background-position');
			var posList = position.split(' ');
			var left = posList[0];

			if (left == 'center') {
				left = '50%';
			}

			if (left.match(/\d+\%/)) {
				left = this.containerWidth * (parseFloat(left) / 100) - this.bgWidth / 2;
			} else if (left.match(/\d+px/)) {
				left = parseFloat(left);
			}

			return {
				left: left
			};
		},

		handleResize: function()
		{
			this.containerWidth = this.$el.outerWidth();
			this.containerHeight = this.$el.outerHeight();
			this.windowHeight = this.$window.height();
			this.set(this.now);
		},

		_checkIfVisible: function()
		{
			var scrollTop = this.$window.scrollTop(),
				containerTop = this.$el.offset().top;
			this.visible = (containerTop + this.containerHeight > scrollTop && containerTop < scrollTop + this.windowHeight);
		},

		set: function(x)
		{
			this.$el.css('background-position', ((this.containerWidth - this.bgWidth) * x) + 'px center');
			this.now = x;
			return this;
		},

		compute: function(from, to, delta)
		{
			if (this._hoverAnimation) return (this._hoverTo - this._hoverFrom) * delta + this._hoverFrom;
			return (to - from) * delta + from;
		},

		start: function(to)
		{
			var from = this.now,
				that = this,
				fromPosition = (this.bgWidth - this.containerWidth) * from,
				toPosition = (this.bgWidth - this.containerWidth) * to,
				duration = Math.abs(toPosition - fromPosition) / 0.1;

			this.$el
				.css('delta', 0)
				.animate({
					delta: 1
				}, {
					duration: duration,
					easing: this.options.easing,
					complete: function(){
						that._hoverAnimation = false;
					},
					step: function(delta){
						that.set(that.compute(from, to, delta));
					},
					queue: false
				});
			return this;
		},

		cancel: function()
		{
			this._hoverAnimation = false;
			this.$el.stop(true, false);
			return this;
		}


	};

})(jQuery);
