(function($, elementor) {
	"use strict";

	$(window).on('elementor/frontend/init', function () {

		let FrontEndExtended = elementorModules.frontend.handlers.Base.extend({

			generateParallaxConfig: function() {
				var parallaxConfig = {};
				if(this.getElementSettings('thegem_parallax_activate') === 'yes') {
					parallaxConfig['parallax'] = true;
					parallaxConfig['mobile'] = this.getElementSettings('thegem_parallax_activate_mobile') === 'yes';
					parallaxConfig['type'] = this.getElementSettings('thegem_parallax_type') ? this.getElementSettings('thegem_parallax_type') : 'vertical';
				}
				if(Object.keys(parallaxConfig).length !== 0) {
					return parallaxConfig;
				}
				return false;
			},

			onInit: function () {
				var editMode = Boolean(elementor.isEditMode()),
					parallaxConfig = false;
				if(!editMode || thegem_section_parallax[this.getID()] != undefined) {
					parallaxConfig = thegem_section_parallax[this.getID()] || false;
				} else {
					parallaxConfig = this.generateParallaxConfig();
				}
				if (parallaxConfig) {
					$( '.thegem-section-parallax-background', this.$element ).remove();
					var $backgroundWrap = $('<div class="thegem-section-parallax"></div>');
					var $background = $('<div class="thegem-section-parallax-background"></div>');
					$backgroundWrap.prependTo(this.$element);
					$background.prependTo($backgroundWrap);

					var parallax_type = parallaxConfig['type'] ? parallaxConfig['type'] : 'vertical';
					$backgroundWrap.addClass('thegem-section-parallax-' + parallax_type);

					var mobile_enabled = parallaxConfig['mobile'] ? '1' : '0',
						is_custom_title = $backgroundWrap.hasClass('custom-title-background'),
						backgroundImageCss = $background.css('background-image') || '';

					if (!window.gemSettings.isTouch || mobile_enabled === '1') {
						if ($backgroundWrap.hasClass('thegem-section-parallax-vertical')) {
							var parallaxOptions = {};
							if (is_custom_title) {
								parallaxOptions.position = 'top';
							}

							if (backgroundImageCss == 'none' || backgroundImageCss == '') {
								$background.on('tgpliVisible', function() {
									$(this).parallaxVertical('50%', parallaxOptions);
								});

								return;
							}

							$background.parallaxVertical('50%', parallaxOptions);

						} else if ($backgroundWrap.hasClass('thegem-section-parallax-horizontal')) {

							if (window.gemSettings.parallaxDisabled) {
								return;
							}

							if (backgroundImageCss == 'none' || backgroundImageCss == '') {
								$background.on('tgpliVisible', function() {
									$(this).parallaxHorizontal();
								});

								return;
							}

							$background.parallaxHorizontal();
						}
					} else {
						$background.css({backgroundAttachment: 'scroll'});
					}

				}
			},
		});

		elementor.hooks.addAction( 'frontend/element_ready/section', function ($element) {
			new FrontEndExtended({
				$element: $element
			});
		} );
		elementor.hooks.addAction( 'frontend/element_ready/column', function ($element) {
			new FrontEndExtended({
				$element: $element
			});
		} );

		if (window.gemSettings.parallaxDisabled) {
			var head  = document.getElementsByTagName('head')[0],
				link  = document.createElement('style');
			link.rel  = 'stylesheet';
			link.type = 'text/css';
			link.innerHTML = ".thegem-section-parallax.thegem-section-parallax-fixed .thegem-section-parallax-background { background-attachment: scroll !important; }";
			head.appendChild(link);
		}
	});


	$.fn.parallaxVertical = function(xpos, options) {
		var $window = $(window),
			$page = $('#page');

		var windowHeight = $window.height(),
			perspectiveOpened = false;

		options = options || {};

		this.each(function() {
			var $this = $(this),
				speedFactor,
				offsetFactor = 0,
				getHeight,
				topOffset = 0,
				containerHeight = 0,
				containerWidth = 0,
				disableParallax = false,
				parallaxIsDisabled = false,
				baseImgHeight = 0,
				baseImgWidth = 0,
				isBgCover = ($this.css('background-size') == 'cover'),
				curImgHeight = 0,
				reversed = $this.hasClass('parallax-reversed'),
				baseSpeedFactor = reversed ? -0.1 : 0.3,
				outerHeight = true;

			if (xpos === undefined) xpos = "50%";

			if (outerHeight){
				getHeight = function(jqo){
					return jqo.parent().outerHeight(true);
				};
			} else {
				getHeight = function(jqo){
					return jqo.parent().height();
				};
			}

			function getBackgroundSize(callback){
				var img = new Image(),
					// here we will place image's width and height
					width, height,
					// here we get the size of the background and split it to array
					backgroundSize = ($this.css('background-size') || ' ').split(' ');

				// checking if width was set to pixel value
				if (/px/.test(backgroundSize[0])) width = parseInt(backgroundSize[0]);
				// checking if width was set to percent value
				if (/%/.test(backgroundSize[0])) width = $this.parent().width() * (parseInt(backgroundSize[0]) / 100);
				// checking if height was set to pixel value
				if (/px/.test(backgroundSize[1])) height = parseInt(backgroundSize[1]);
				// checking if height was set to percent value
				if (/%/.test(backgroundSize[1])) height = $this.parent().height() * (parseInt(backgroundSize[0]) / 100);

				if (width !== undefined && height !== undefined){
					// Image is not needed
					return callback({ width: width, height: height });
				}

				// Image is needed
				img.onload = function () {
					// check if width was set earlier, if not then set it now
					if (typeof width == 'undefined') width = this.width;
					// do the same with height
					if (typeof height == 'undefined') height = this.height;
					// call the callback
					callback({ width: width, height: height });
				};
				// extract image source from css using one, simple regex
				// src should be set AFTER onload handler
				img.src = ($this.css('background-image') || '').replace(/url\(['"]*(.*?)['"]*\)/g, '$1');
			}

			function update(){
				if (disableParallax){
					if ( ! parallaxIsDisabled){
						$this.css('backgroundPosition', '');
						if(!$this.is('.page-title-block')) {
							$this.removeClass('fullwidth-block-parallax-vertical');
						}
						parallaxIsDisabled = true;
					}
					return;
				}else{
					if (parallaxIsDisabled){
						if(!$this.is('.page-title-block')) {
							$this.addClass('fullwidth-block-parallax-vertical');
						}
						parallaxIsDisabled = false;
					}
				}
				if (isNaN(speedFactor))
					return;

				var pos = getScrollTop();

				// Check if totally above or totally below viewport
				if ((topOffset + containerHeight < pos) || (pos < topOffset - windowHeight)) return;

				var ypos = offsetFactor + speedFactor * (topOffset - pos);

				if (ypos > 0) {
					ypos = 0;
				} else if (curImgHeight + ypos < containerHeight) {
					ypos = containerHeight - curImgHeight;
				}

				if (curImgHeight > 0) {
					$this.css('height', curImgHeight+'px');
				}

				var translate = 'translate3d(0, ' + ypos + 'px' + ', 0)';
				$this[0].style['-webkit-transform'] = translate;
				$this[0].style['-moz-transform'] = translate;
				$this[0].style['transform'] = translate;
			}

			function getScrollTop() {
				return perspectiveOpened ? $page.scrollTop() : $window.scrollTop();
			}

			function resize(){
				setTimeout(function(){
					if (perspectiveOpened) {
						windowHeight = $page.height();
					} else {
						windowHeight = $window.height();
					}

					containerHeight = getHeight($this);
					containerWidth = $this.parent().width();

					if (isBgCover){
						if (baseImgWidth / baseImgHeight <= containerWidth / containerHeight){
							curImgHeight = baseImgHeight * (containerWidth / baseImgWidth);
						} else {
							curImgHeight = containerHeight*1.2;
						}
					}

					topOffset = $this.parent().offset().top;

					// Improving speed factor to prevent showing image limits
					if (curImgHeight !== 0) {
						if (baseSpeedFactor >= 0) {
							if (options.position !== undefined && options.position == 'top') {
								speedFactor = Math.min(0.25, (curImgHeight - containerHeight) / (containerHeight + topOffset));
								offsetFactor = -speedFactor * topOffset;
							} else {
								speedFactor = -Math.min(baseSpeedFactor, (curImgHeight - containerHeight) / (windowHeight + containerHeight));
								offsetFactor = containerHeight * speedFactor;
							}
						} else {
							speedFactor = Math.min(baseSpeedFactor, (windowHeight - containerHeight) / (windowHeight + containerHeight));
							offsetFactor = Math.max(0, speedFactor * containerHeight);
						}
					} else {
						speedFactor = baseSpeedFactor;
						offsetFactor = 0;
					}

					update();
				}, 10);
			}

			getBackgroundSize(function(sz){
				curImgHeight = baseImgHeight = sz.height;
				baseImgWidth = sz.width;
				resize();
			});

			$window.on({scroll: update, load: resize, resize: resize, touchmove: update});

			var tgpliVisibleTimeout = false;
			$window.on('tgpliVisible', function(e) {
				if (tgpliVisibleTimeout) {
					clearTimeout(tgpliVisibleTimeout);
				}

				tgpliVisibleTimeout = setTimeout(function() {
					resize();
				}, 100);
			});
			//$page.bind({scroll: update, resize: resize});
			resize();
		});

		$(window).on('perspective-modalview-opened', function() {
			perspectiveOpened = true;
			windowHeight = $page.height();
		});
		$(window).on('perspective-modalview-closed', function() {
			perspectiveOpened = false;
			windowHeight = $window.height();
		});
	};



	$.fn.parallaxHorizontal = function(options) {
		this.each(function(){
			return new ParallaxHorizontal(this, options);
		});
	};

	function ParallaxHorizontal(el, options) {
		var that = this;
		this.$window = $(window);
		this.el = el;
		this.$el = $(el);
		this.$parent = $(el).parent();
		this.$section = $(el).closest('.elementor-section');

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
				that.$section
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
	}

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

}(jQuery, window.elementorFrontend));