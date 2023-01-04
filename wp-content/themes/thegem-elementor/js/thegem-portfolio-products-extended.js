(function ($) {
	$(function () {

		window.defaultSortPortfolioDataNew = {
			date: '[data-sort-date] parseInt',
			popularity: '[data-sort-popularity] parseInt',
			rating: '[data-sort-rating] parseInt',
			price: '[data-sort-price] parseInt',
			title: '.title'
		};

		var localCache = {
			data: {},
			remove: function (url) {
				delete localCache.data[url];
			},
			exist: function (url) {
				return localCache.data.hasOwnProperty(url) && localCache.data[url] !== null;
			},
			get: function (url) {
				return localCache.data[url];
			},
			set: function (url, cachedData, callback) {
				localCache.remove(url);
				localCache.data[url] = cachedData;
				if ($.isFunction(callback)) callback(cachedData);
			}
		};

		function portfolio_images_loaded($box, image_selector, immediately, callback) {
			if (immediately) {
				return callback();
			}

			function check_image_loaded(img) {
				return img.complete && img.naturalWidth !== undefined && img.naturalWidth != 0;
			}

			var $images = $(image_selector, $box).filter(function () {
					return !check_image_loaded(this);
				}),
				images_count = $images.length;

			if (images_count == 0) {
				return callback();
			}

			if (window.gemBrowser.name == 'ie' && !isNaN(parseInt(window.gemBrowser.version)) && parseInt(window.gemBrowser.version) <= 10) {
				function image_load_event() {
					images_count--;
					if (images_count == 0) {
						callback();
					}
				}

				$images.each(function () {
					if (check_image_loaded(this)) {
						return;
					}

					var proxyImage = new Image();
					proxyImage.addEventListener('load', image_load_event);
					proxyImage.addEventListener('error', image_load_event);
					proxyImage.src = this.src;
				});
				return;
			}

			$images.on('load error', function () {
				images_count--;
				if (images_count == 0) {
					callback();
				}
			});
		}

		function init_prev_next_navigator_buttons($portfolio) {
			var current_page = $portfolio.data('current-page');
			var pages_count = $portfolio.data('pages-count');
			if (current_page <= 1) {
				if ($portfolio.hasClass('portfolio-pagination-normal')) {
					$('.portfolio-navigator a.prev', $portfolio).css('display', 'none');
				} else {
					$('.portfolio-navigator a.prev', $portfolio).addClass('disabled');
				}
			} else {
				if ($portfolio.hasClass('portfolio-pagination-normal')) {
					$('.portfolio-navigator a.prev', $portfolio).css('display', 'flex');
				} else {
					$('.portfolio-navigator a.prev', $portfolio).removeClass('disabled');
				}
			}

			if (current_page >= pages_count) {
				if ($portfolio.hasClass('portfolio-pagination-normal')) {
					$('.portfolio-navigator a.next', $portfolio).css('display', 'none');
				} else {
					$('.portfolio-navigator a.next', $portfolio).addClass('disabled');
				}
			} else {
				if ($portfolio.hasClass('portfolio-pagination-normal')) {
					$('.portfolio-navigator a.next', $portfolio).css('display', 'flex');
				} else {
					$('.portfolio-navigator a.next', $portfolio).removeClass('disabled');
				}
			}
		}

		function init_portfolio_pages_extended($portfolio) {
			var $activeItems = $('.portfolio-set .portfolio-item', $portfolio);
			var current_page = $portfolio.data('current-page');
			var pages_count = $portfolio.data('pages-count');

			if ($portfolio.hasClass('portfolio-pagination-normal')) {
				if ($('.portfolio-navigator', $portfolio).length && pages_count > 1) {
					var pagenavigator = '';
					for (var i = 0; i < pages_count; i++)
						pagenavigator += '<a href="#" data-page="' + (i + 1) + '">' + (i + 1) + '</a>';
					$('.portfolio-navigator', $portfolio).find('.pages').html(pagenavigator);
					$('.portfolio-navigator', $portfolio).show();
					$('.portfolio-set', $portfolio).css('margin-bottom', '');
					$('.portfolio-navigator a[data-page="' + current_page + '"]', $portfolio).addClass('current');
				} else {
					$('.portfolio-navigator .pages', $portfolio).html('');
					$('.portfolio-navigator', $portfolio).hide();
					$('.portfolio-set', $portfolio).css('margin-bottom', 0);
				}
			}
			init_prev_next_navigator_buttons($portfolio);

			$('.portfolio-set .portfolio-item', $portfolio).removeClass('item-even');

			$.each($activeItems, function (i, item) {
				if (i % 2 != 0) {
					$(item).addClass('item-even');
				}
			});

			$('.portfolio-navigator', $portfolio).off('click', 'a');
			$('.portfolio-navigator', $portfolio).on('click', 'a', function () {
				if ($(this).hasClass('current'))
					return false;
				// var current_page = $(this).parents('.portfolio-navigator ').find('.current:first').data('page');
				var current_page = $portfolio.data('current-page');
				var page;
				if ($(this).hasClass('prev')) {
					page = current_page - 1;
				} else if ($(this).hasClass('next')) {
					page = current_page + 1
				} else {
					page = $(this).data('page');
				}
				if (page < 1)
					page = 1;
				if (page > pages_count)
					page = pages_count;
				$portfolio.data('next-page', page);
				$(this).parents('.portfolio-navigator ').find('a').removeClass('current');
				$(this).parents('.portfolio-navigator ').find('a[data-page="' + page + '"]').addClass('current');
				$portfolio.data('current-page', page);
				$portfolio.addClass('hide-loader');
				portfolio_load_core_request($portfolio);
				init_prev_next_navigator_buttons($portfolio);
				$("html, body").animate({scrollTop: $portfolio.offset().top - 200}, 600);
				return false;
			});
		}

		function init_products_new_sorting($portfolio) {
			if (!$('.portfolio-sorting-select', $portfolio).length)
				return false;

			$('.portfolio-sorting-select', $portfolio).on('mouseover', function () {
				$(this).addClass('active');
			}).on('mouseout', function () {
				$(this).removeClass('active');
			});

			$('.portfolio-sorting-select li', $portfolio).on('click', function (e) {
				$('.portfolio-sorting-select li', $portfolio).removeClass('portfolio-sorting-select-current');
				$('.portfolio-sorting-select div.portfolio-sorting-select-current .portfolio-sorting-select-name', $portfolio).html($(this).html());
				$(this).addClass('portfolio-sorting-select-current');
				var selected = $(this).data('value');
				$('.portfolio-sorting-select', $portfolio).mouseout();

				$portfolio.data('next-page', 1);
				$portfolio.data('current-page', 1);
				portfolio_load_core_request($portfolio);

				e.preventDefault();
				return false;
			});
		}

		function portfolio_load_core_request($portfolio) {
			var $set = $('.portfolio-set:not(.sub-categories)', $portfolio);
			var uid = $portfolio.data('portfolio-uid');
			var queryParams = new URLSearchParams(window.location.search);
			var shopUrl, delArr = [], cacheId = uid;
			for (var p of queryParams) {
				if (p[0].includes(uid)) {
					delArr.push(p[0]);
				}
				if (p[0] == 'post_type' && p[1] == 'product') {
					shopUrl = $('input#shop-page-url').val();
				}
				// if (p[0] == 's') {
				// 	shopUrl = $('input#shop-page-url').val();
				// 	delArr.push(p[0]);
				// }
			}

			for (var del of delArr) {
				queryParams.delete(del);
			}
			var is_processing_request = $set.data('request-process') || false;
			if (is_processing_request)
				return false;
			$set.data('request-process', true);
			var data = $.extend(true, {}, window['thegem_portfolio_ajax_' + uid]);
			if ($.isEmptyObject(data)) {
				data = $.extend(true, {}, window['thegem_portfolio_ajax']);
				data['data'] = widget_settings[uid];
				// data['data'] = {...widget_settings[uid]};
				data['action'] = data['data']['action'];
			}
			if (uid != '') {
				uid += '-';
			} else {
				cacheId = 'shop'
			}
			if ($('.portfolio-count select', $portfolio).size() > 0)
				data['data']['more_count'] = $('.portfolio-count select', $portfolio).val();

			data['data']['items_per_page'] = $portfolio.data('per-page');

			data['data']['more_page'] = $portfolio.data('next-page') || 1;
			if (data['data']['more_page'] == 0)
				return false;

			if ($portfolio.data('portfolio-filter') && $portfolio.data('portfolio-filter').length !== 0) {
				if ($portfolio.hasClass('news-grid')) {
					data['data']['categories'] = [$portfolio.data('portfolio-filter')];
				} else if ($portfolio.hasClass('products')) {
					data['data']['content_products_cat'] = [$portfolio.data('portfolio-filter')].toString();
				} else {
					data['data']['content_portfolios_cat'] = [$portfolio.data('portfolio-filter')];
				}
				data['data']['content_products_cat_filter'] = true;
				if (!$('.portfolio-filters-list', $portfolio).hasClass('native')
					&& !$('.portfolio-filter-item.cats', $portfolio).hasClass('reload')
					&& !$('.portfolio-filter-tabs', $portfolio).length) {
					queryParams.set(uid + 'category', $portfolio.data('portfolio-filter'));
					cacheId += 'category=' + $portfolio.data('portfolio-filter');
				}
			} else {
				data['data']['content_products_cat_filter'] = false;
			}
			if (data['data']['content_products_cat']) {
				data['data']['content_products_cat'] = data['data']['content_products_cat'].toString();
			}

			if ($portfolio.data('portfolio-filter-attributes')) {
				data['data']['content_products_attr_filter'] = true;
				data['data']['content_products_attr'] = [];
				var i = 0;
				for (var key in $portfolio.data('portfolio-filter-attributes')) {
					data['data']['content_products_attr'].push(key);
					data['data']['content_products_attr_val_' + key] = $portfolio.data('portfolio-filter-attributes')[key].toString();
					queryParams.set(uid + 'filter_' + key, $portfolio.data('portfolio-filter-attributes')[key]);
					cacheId += 'filter_' + key + '=' + $portfolio.data('portfolio-filter-attributes')[key];
					i++;
				}
				data['data']['content_products_attr'] = data['data']['content_products_attr'].toString();
				if (i === 0) {
					data['data']['content_products_attr_filter'] = false;
				}
			} else {
				data['data']['content_products_attr_filter'] = false;
			}

			if ($portfolio.data('portfolio-filter-status')) {
				data['data']['content_products_status_filter'] = $portfolio.data('portfolio-filter-status');
				if (!$('.portfolio-filter-tabs', $portfolio).length) {
					queryParams.set(uid + 'status', $portfolio.data('portfolio-filter-status'));
					cacheId += 'status=' + $portfolio.data('portfolio-filter-status');
				}
			}

			if ($portfolio.data('portfolio-filter-price')) {
				data['data']['content_products_price_filter'] = $portfolio.data('portfolio-filter-price');
				queryParams.set(uid + 'min_price', $portfolio.data('portfolio-filter-price')[0]);
				queryParams.set(uid + 'max_price', $portfolio.data('portfolio-filter-price')[1]);
				cacheId += 'min_price=' + $portfolio.data('portfolio-filter-price')[0];
				cacheId += 'max_price=' + $portfolio.data('portfolio-filter-price')[1];
			}

			if ($portfolio.data('portfolio-filter-search')) {
				data['data']['content_products_search_filter'] = $portfolio.data('portfolio-filter-search');
				queryParams.set(uid + 'product-search', $portfolio.data('portfolio-filter-search'));
				cacheId += 'product-search=' + $portfolio.data('portfolio-filter-search');
			}

			if ($portfolio.data('current-tab')) {
				queryParams.set(uid + 'tab', $portfolio.data('current-tab'));
				cacheId += 'tab=' + $portfolio.data('current-tab');
			}

			if ($('.portfolio-filters-list', $portfolio).length > 0 && !$('.portfolio-filters-list', $portfolio).hasClass('native')) {
				// checkFilters($portfolio);
			}

			if ($('.portfolio-sorting', $portfolio).length > 0) {
				data['data']['orderby'] = $('.portfolio-sorting .orderby .sorting-switcher', $portfolio).data('current');
				data['data']['order'] = $('.portfolio-sorting .order .sorting-switcher', $portfolio).data('current');
			} else if ($('.portfolio-sorting-select', $portfolio).length > 0) {
				data['data']['orderby'] = $('.portfolio-sorting-select li.portfolio-sorting-select-current', $portfolio).data('orderby');
				data['data']['order'] = $('.portfolio-sorting-select li.portfolio-sorting-select-current', $portfolio).data('order');

				if (!$('.portfolio-sorting-select li.default', $portfolio).hasClass('portfolio-sorting-select-current')) {
					queryParams.set(uid + 'orderby', $('.portfolio-sorting-select li.portfolio-sorting-select-current', $portfolio).data('orderby'));
					cacheId += 'orderby' + $('.portfolio-sorting-select li.portfolio-sorting-select-current', $portfolio).data('orderby');
				}
			} else {
				if ($portfolio.hasClass('news-grid')) {
					data['data']['orderby'] = 'menu_order date';
					data['data']['order'] = 'DESC';
				} else if (!$portfolio.hasClass('extended-products-grid')) {
					data['data']['orderby'] = 'menu_order ID';
					data['data']['order'] = 'ASC';
				}
			}

			if ($portfolio.hasClass('portfolio-pagination-more') && data['data']['more_page'] != 1) {
				$('.portfolio-load-more .gem-button', $portfolio).before('<div class="loading"><div class="preloader-spin"></div></div>');
			} else if ($portfolio.hasClass('portfolio-pagination-scroll') && data['data']['more_page'] != 1) {
				$('.portfolio-scroll-pagination', $portfolio).addClass('active').html('<div class="loading"><div class="preloader-spin"></div></div>');
			} else {
				var pos_t;
				if ($portfolio.find('.portfolio-filters-list').hasClass('scroll-top') || $portfolio.hasClass('hide-loader')) {
					if ($portfolio.offset().top + $portfolio.height() < $portfolio.offset().top - 200 + window.innerHeight) {
						pos_t = $portfolio.height() / 2;
					} else {
						pos_t = ($portfolio.offset().top - 200 + window.innerHeight - $portfolio.offset().top) / 2;
					}
				} else {
					if ($portfolio.offset().top + $portfolio.height() < $(window).scrollTop() + window.innerHeight) {
						pos_t = $portfolio.height() / 2;
					} else {
						pos_t = ($(window).scrollTop() + window.innerHeight - $('.portfolio-row-outer', $portfolio).offset().top) / 2;
					}
				}
				$('.portfolio-row-outer', $portfolio).prepend('<div class="preloader-new"><div class="preloader-spin"></div></div>');
				$('.portfolio-row-outer .preloader-spin', $portfolio).css('top', pos_t);
				if ($('.portfolio-filters-list', $portfolio).hasClass('style-sidebar')) {
					var pos_r = ($('.portfolio-top-panel', $portfolio).outerWidth()) / 2;
					if ($portfolio.closest('.panel-sidebar-position-right').length) {
						$('.portfolio-row-outer .preloader-spin', $portfolio).css('left', pos_r);
					} else {
						$('.portfolio-row-outer .preloader-spin', $portfolio).css('right', pos_r);
					}
				}

				if ($portfolio.hasClass('portfolio-pagination-normal') || $portfolio.hasClass('portfolio-pagination-arrows')) {
					if (data['data']['more_page'] > 1) {
						queryParams.set(uid + 'page', data['data']['more_page']);
					}
					setTimeout(function () {
						$portfolio.removeClass('hide-loader');
					}, 600);
				}
			}
			cacheId += 'page' + data['data']['more_page'];

			if ($('.portfolio-filters-list', $portfolio).length > 0 && ($('.portfolio-filters-list', $portfolio).hasClass('style-hidden') || $(window).width() < 992)) {
				$('.progress-bar .striped', $portfolio).show();
				$('.progress-bar', $portfolio).fadeIn('slow');
			}

			if (queryParams.toString().length > 0) {
				if (shopUrl) {
					window.location.href = shopUrl + "?" + queryParams.toString();
				} else {
					history.replaceState(null, null, "?" + queryParams.toString());
				}
			} else {
				if (shopUrl) {
					window.location.href = shopUrl;
				} else {
					history.replaceState(null, null, location.href.split("?")[0]);
				}
			}

			data['data'] = JSON.stringify(data['data']);
			$.ajax({
				type: 'post',
				dataType: 'json',
				url: data.url,
				data: data,
				beforeSend: function () {
					if (localCache.exist(cacheId)) {
						ajaxSuccess($portfolio, localCache.get(cacheId));
						return false;
					}
					return true;
				},
				success: function (response) {
					if (response.status == 'success') {
						ajaxSuccess($portfolio, response);
						localCache.set(cacheId, response);
					} else {
						alert(response.message);
						$('.portfolio-load-more .gem-button .loading', $portfolio).remove();
						$('.portfolio-scroll-pagination', $portfolio).removeClass('active').html('');
					}
				}
			});
		}

		function ajaxSuccess($portfolio, response) {
			var $set = $('.portfolio-set:not(.sub-categories)', $portfolio);
			var minZIndex = $('.portfolio-item:last', $set).css('z-index') - 1;
			var $newItems = $(response.html);
			if ($newItems.hasClass('woocommerce')) {
				$newItems = $newItems.find('>div');
			}
			$('.portfolio-item', $newItems).addClass('paginator-page-1');
			$('.portfolio-item', $newItems).each(function () {
				$(this).css('z-index', minZIndex--);
			});
			var current_page = $newItems.data('page');
			var next_page = $newItems.data('next-page');
			$portfolio.data('current-page', current_page);
			$portfolio.data('pages-count', $newItems.data('pages-count'));
			var $inserted_data = $($newItems.html());
			if ($portfolio.hasClass('loading-animation')) {
				if ($portfolio.itemsAnimations('instance').getAnimationName() != 'disabled') {
					$inserted_data.addClass('item-animations-not-inited');
				} else {
					$inserted_data.removeClass('item-animations-not-inited');
				}
			}

			if (response.counts) {
				$('.portfolio-filter-item a', $portfolio).removeClass('disable');
				for (var key in response.counts) {
					$('.portfolio-filter-item a[data-filter-id=' + key + '] .count', $portfolio).html(response.counts[key]);
					if (response.counts[key] == 0) {
						$('.portfolio-filter-item a[data-filter-id=' + key + ']', $portfolio).addClass('disable');
					}
				}
			}

			var immediately = false;
			if ($portfolio.hasClass('portfolio-style-justified')) {
				immediately = true;
			}

			portfolio_images_loaded($newItems, '.image-inner img', immediately, function () {

				if (current_page === 1 || $portfolio.hasClass('portfolio-pagination-normal') || $portfolio.hasClass('portfolio-pagination-arrows')) {
					if ($portfolio.hasClass('loading-animation')) {
						$portfolio.itemsAnimations('instance').clear();
					}
					$set.html('');
					if (!$portfolio.hasClass('disable-isotope')) {
						$set.isotope('reloadItems');
					}
				}

				if (!$portfolio.hasClass('disable-isotope')) {
					$set.isotope('insert', $inserted_data);
					imageSizesFix($portfolio);
					$set.isotope('layout');
				} else {
					$set.append($inserted_data);
				}

				init_circular_overlay($portfolio, $set);
				if ($portfolio.hasClass('loading-animation')) {
					$portfolio.itemsAnimations('instance').show($inserted_data);
				}
				$('img.image-hover', $portfolio).show();

				$portfolio.data('next-page', next_page);
				if ($portfolio.hasClass('portfolio-pagination-more')) {
					$('.portfolio-load-more .loading', $portfolio).remove();

					if (next_page > 0) {
						$('.portfolio-load-more', $portfolio).show();
					} else {
						$('.portfolio-load-more', $portfolio).hide();
					}
				} else if ($portfolio.hasClass('portfolio-pagination-scroll')) {
					$('.portfolio-scroll-pagination', $portfolio).removeClass('active').html('');
				} else if ($portfolio.hasClass('portfolio-pagination-normal') || $portfolio.hasClass('portfolio-pagination-arrows')) {
					if (current_page === 1) {
						init_portfolio_pages_extended($portfolio);
					}
				}
				$('.portfolio-row-outer', $portfolio).find('.preloader-new').remove();

				$('.progress-bar .striped', $portfolio).fadeOut('slow');

				$portfolio.initPortfolioFancybox();
				$set.data('request-process', false);
				customExtendedIcons($portfolio);
				categoryFilterClick($portfolio);
			});
		}

		function checkFilters($portfolio) {
			var postsList = $('.portfolio-close-filters', $portfolio).data('posts');
			var postFilter = JSON.parse(JSON.stringify($('.portfolio-close-filters', $portfolio).data('posts')));
			var postFilterArr = [];
			var filtersList = JSON.parse(JSON.stringify($('.portfolio-close-filters', $portfolio).data('filters-list')));

			$('.portfolio-filter-item a', $portfolio).removeClass('disable');

			if ($portfolio.data('portfolio-filter') && $portfolio.data('portfolio-filter') !== '') {
				postFilterArr['category'] = postFilter.filter(function (currentValue) {
					return currentValue['category'].includes($portfolio.data('portfolio-filter'));
				});
			}

			if ($portfolio.data('portfolio-filter-attributes') && $portfolio.data('portfolio-filter-attributes') !== '') {

				for (var key in $portfolio.data('portfolio-filter-attributes')) {
					postFilterArr[key] = postFilter.filter(function (currentValue, index, arr) {
						return $portfolio.data('portfolio-filter-attributes')[key].some(function (attr) {
							if (currentValue[key]) {
								return currentValue[key].includes(attr);
							} else {
								return true;
							}

						});
					});
				}
			}

			if ($portfolio.data('portfolio-filter-status') && $portfolio.data('portfolio-filter-status') !== '') {
				postFilterArr['status'] = postFilter.filter(function (currentValue, index, arr) {
					return $portfolio.data('portfolio-filter-status').every(function (attr) {
						return currentValue['status'].includes(attr);
					});
				});
			}

			for (var key in filtersList) {
				var resultArr = [];

				for (var key2 in postFilterArr) {
					if (key2 === key) {
						continue;
					}

					if (resultArr.length) {
						resultArr = resultArr.filter(function (currentValue, index, arr) {
							return postFilterArr[key2].includes(currentValue);
						})
					} else {
						resultArr = postFilterArr[key2];
					}
				}

				if (!resultArr.length) {
					resultArr = postFilter;
				}

				filtersList[key].forEach(function (val) {
					var count = 0;

					resultArr.forEach(function (val2) {
						if (val2[key].includes(val)) {
							count++;
						}
					});


					$('.portfolio-filter-item a[data-attr=' + key + '][data-filter=' + val + ']', $portfolio).find('.count').html(count);
					$('.portfolio-filter-item a[data-filter-type=' + key + '][data-filter=' + val + ']', $portfolio).find('.count').html(count);
					if (count == 0) {
						$('.portfolio-filter-item a[data-attr=' + key + '][data-filter=' + val + ']', $portfolio).addClass('disable');
						$('.portfolio-filter-item a[data-filter-type=' + key + '][data-filter=' + val + ']', $portfolio).addClass('disable');
					}
				});
			}
		}

		function init_portfolio_scroll_next_page($portfolio) {
			if ($('.portfolio-scroll-pagination', $portfolio).length == 0) {
				return false;
			}

			var $pagination = $('.portfolio-scroll-pagination', $portfolio);
			var watcher = scrollMonitor.create($pagination[0]);
			watcher.enterViewport(function () {
				if ($portfolio.data('next-page') != 0) {
					portfolio_load_core_request($portfolio);
				}
			});
		}

		$('.portfolio-count select').combobox();

		function init_circular_overlay($portfolio, $set) {
			if (!$portfolio.hasClass('hover-circular') && !$portfolio.hasClass('hover-new-circular') && !$portfolio.hasClass('hover-default-circular')) {
				return;
			}

			$('.portfolio-item', $set).on('mouseenter touchstart', function () {
				var overlayWidth = $('.overlay', this).width(),
					overlayHeight = $('.overlay', this).height(),
					$overlayCircle = $('.overlay-circle', this),
					maxSize = 0;

				if (overlayWidth > overlayHeight) {
					maxSize = overlayWidth;
					$overlayCircle.height(overlayWidth)
				} else {
					maxSize = overlayHeight;
					$overlayCircle.width(overlayHeight);
				}
				maxSize += overlayWidth * 0.3;

				$overlayCircle.css({
					marginLeft: -maxSize / 2,
					marginTop: -maxSize / 2
				});
			});
		}

		function fixItemHiddenContent(items) {
			items.forEach(function (item) {
				var $hiddenContent = $('.slide-content-hidden', item.element);

				if (!$hiddenContent.length) {
					return;
				}

				$hiddenContent.css('margin-bottom', -$hiddenContent.outerHeight() + 'px');
			});
		}

		function fixHorizontalSlidingAuthor(items) {
			items.forEach(function (item) {
				var $visibleContent = $('.slide-content-visible', item.element),
					$hiddenContent = $('.slide-content-hidden', item.element),
					$authorContent = $('.caption .author', item.element);

				if (!$authorContent.length || !$visibleContent.length || !$hiddenContent.length) {
					return;
				}

				$authorContent.css('top', ($visibleContent.outerHeight() - $hiddenContent.outerHeight() - $authorContent.outerHeight()) + 'px');
			});
		}


		function filterPortfolioExtended($portfolio, filterValue) {
			$portfolio.data('portfolio-filter', filterValue || '');
			$portfolio.data('next-page', 1);
			portfolio_load_core_request($portfolio);
		}

		function filterPortfolioAttributes($portfolio, filterValue) {
			$portfolio.data('portfolio-filter-attributes', filterValue || '');
			$portfolio.data('next-page', 1);
			portfolio_load_core_request($portfolio);
		}

		function filterPortfolioStatus($portfolio, filterValue) {
			$portfolio.data('portfolio-filter-status', filterValue || '');
			$portfolio.data('next-page', 1);
			portfolio_load_core_request($portfolio);
		}

		function filterPortfolioPrice($portfolio, filterValue) {
			$portfolio.data('portfolio-filter-price', filterValue || '');
			$portfolio.data('next-page', 1);
			portfolio_load_core_request($portfolio);
		}

		function filterPortfolioSearch($portfolio, filterValue) {
			$portfolio.data('portfolio-filter-search', filterValue || '');
			$portfolio.data('next-page', 1);
			portfolio_load_core_request($portfolio);
		}

		function clearFilters($portfolio) {
			$portfolio.data('portfolio-filter', '');
			$portfolio.data('portfolio-filter-attributes', '');
			$portfolio.data('portfolio-filter-status', '');
			$portfolio.data('portfolio-filter-price', '');
			$portfolio.data('portfolio-filter-search', '');
			$portfolio.data('next-page', 1);
			portfolio_load_core_request($portfolio);
		}

		function hasOnlyDoubleItems($set) {
			var $items = $('.portfolio-item', $set);
			return $items.length == $items.filter('.double-item-squared, .double-item-horizontal').length;
		}

		function fixPortfolioWithDoubleItems($portfolio, needFix) {
			if (needFix) {
				$portfolio.addClass('porfolio-even-columns');
			} else {
				$portfolio.removeClass('porfolio-even-columns');
			}
		}

		function imageSizesFix($portfolio) {
			if ($portfolio.hasClass('portfolio-style-masonry')) {
				return;
			}
			$('.portfolio-item .image', $portfolio).css('height', '');
			$('.portfolio-item .wrap > .caption', $portfolio).css('height', '');

			var maxImageHeight = 0,
				maxCaptionHeight = 0,
				maxImageHeightVertical;

			$('.portfolio-item', $portfolio).each(function () {
				if ($(this).find('.wrap > .caption').outerHeight() > maxCaptionHeight) {
					maxCaptionHeight = $(this).find('.wrap > .caption').outerHeight();
				}
			});

			$('.portfolio-item .wrap > .caption', $portfolio).css('height', maxCaptionHeight);

			if ($portfolio.hasClass('portfolio-style-metro')) {
				if ($('.portfolio-set', $portfolio).data('isotope')) {
					$('.portfolio-set', $portfolio).isotope('layout');
				}
				return;
			}

			var singleInnerWidth = $('.portfolio-item-size-container .portfolio-item', $portfolio).innerWidth();
			var singleWidth = $('.portfolio-item-size-container .portfolio-item', $portfolio).width();
			var gaps = singleInnerWidth - singleWidth;

			if ($($portfolio).hasClass('aspect-ratio-portrait')) {
				maxImageHeight = parseInt(singleWidth * 1.25);
			} else {
				maxImageHeight = parseInt(singleWidth);
			}

			maxImageHeightVertical = 2 * maxImageHeight + gaps + maxCaptionHeight;

			$('.portfolio-item', $portfolio).not('.not-found, .double-item-squared, .double-item-vertical').find('.image').css('height', maxImageHeight);
			$('.portfolio-item.double-item-squared, .portfolio-item.double-item-vertical', $portfolio).find('.image').css('height', maxImageHeightVertical);

			if ($('.portfolio-set', $portfolio).data('isotope')) {
				$('.portfolio-set', $portfolio).isotope('layout');
			}
		}

		function initExtendedProductsGrid() {
			if (window.tgpLazyItems !== undefined) {
				var isShowed = window.tgpLazyItems.checkGroupShowed(this, function (node) {
					initExtendedProductsGrid.call(node);
				});
				if (!isShowed) {
					return;
				}
			}

			var $portfolio = $(this);
			if ($portfolio.hasClass('inited')) {
				return;
			}
			$portfolio.addClass('inited');
			setTimeout(function () {
				$portfolio.addClass('ready');
			}, 500);
			var $set = $('.portfolio-set', this);

			if ($(window).outerWidth() < 768 && $portfolio.data('pages-count-mobile')) {
				$portfolio.data('per-page', $portfolio.data('per-page-mobile'));
				$portfolio.data('next-page', $portfolio.data('next-page-mobile'));
				$portfolio.data('pages-count', $portfolio.data('pages-count-mobile'));
			} else if ($(window).outerWidth() < 992 && $portfolio.data('pages-count-tablet')) {
				$portfolio.data('per-page', $portfolio.data('per-page-tablet'));
				$portfolio.data('next-page', $portfolio.data('next-page-tablet'));
				$portfolio.data('pages-count', $portfolio.data('pages-count-tablet'));
			}
			if ($portfolio.data('per-page') != -1) {
				var removeItems = parseInt($portfolio.data('per-page')) + 1;
				$('.portfolio-item:nth-child(n+' + removeItems + ')', $portfolio).remove();
			}

			if ($portfolio.data('next-page') == 0) {
				$('.portfolio-load-more', $portfolio).hide();
			}

			init_products_new_sorting($portfolio);

			if ($portfolio.hasClass('portfolio-pagination-normal') || $portfolio.hasClass('portfolio-pagination-arrows')) {
				init_portfolio_pages_extended($portfolio);
			}

			initPriceSlider($portfolio);

			var immediately = false;
			if ($portfolio.hasClass('portfolio-style-justified')) {
				immediately = true;
			}

			portfolio_images_loaded($set, '.image-inner img', immediately, function () {

				if ($portfolio.hasClass('loading-animation')) {
					var itemsAnimations = $portfolio.itemsAnimations({
						itemSelector: '.portfolio-item',
						scrollMonitor: true
					});
				}

				init_circular_overlay($portfolio, $set);

				customExtendedIcons($portfolio);

				if (!$portfolio.hasClass('disable-isotope')) {
					imageSizesFix($portfolio);

					var layoutMode = 'masonry-custom';
					var portfolioStyle = 'justified';

					if ($portfolio.hasClass('portfolio-style-masonry')) {
						portfolioStyle = 'masonry';
					}

					if ($portfolio.hasClass('portfolio-style-metro')) {
						layoutMode = 'metro';
						portfolioStyle = 'metro';
					}

					if (portfolioStyle != 'metro') {
						fixPortfolioWithDoubleItems($portfolio, hasOnlyDoubleItems($set));
					}

					var size_container = $('.portfolio-item-size-container .portfolio-item', $portfolio);
					var isotope_options = {
						gridType: 'portfolio',
						itemSelector: '.portfolio-item',
						layoutMode: layoutMode,
						itemImageWrapperSelector: '.image-inner',
						'masonry-custom': {
							columnWidth: (size_container.length > 0) ? size_container[0] : '.portfolio-item:not(.double-item)'
						},
						transitionDuration: 0
					};

					$set
						.on('layoutComplete', function (event, laidOutItems) {
							layoutComplete($portfolio, $set, laidOutItems);
						})
						.on('arrangeComplete', function (event, filteredItems) {
							arrangeComplete($portfolio, $set, portfolioStyle);
						})
						.isotope(isotope_options);

					if (!window.gemSettings.lasyDisabled) {
						var elems = $('.portfolio-item:visible', $set);
						var items = [];
						for (var i = 0; i < elems.length; i++)
							items.push($set.isotope('getItem', elems[i]));
						$set.isotope('reveal', items);
					}

					if ($set.closest('.gem_tab').size() > 0) {
						$set.closest('.gem_tab').bind('tab-update', function () {
							if ($set.data('isotope')) {
								$set.isotope('layout');
							}
						});
					}

					$(document).on('gem.show.vc.tabs', '[data-vc-accordion]', function () {
						var $tab = $(this).data('vc.accordion').getTarget();
						if ($tab.find($set).length) {
							if ($set.data('isotope')) {
								$set.isotope('layout');
							}
						}
					});

					$(document).on('gem.show.vc.accordion', '[data-vc-accordion]', function () {
						var $tab = $(this).data('vc.accordion').getTarget();
						if ($tab.find($set).length) {
							if ($set.data('isotope')) {
								$set.isotope('layout');
							}
						}
					});
				} else {
					var items = $('.portfolio-item', $set);
					layoutComplete($portfolio, $set, items);
					arrangeComplete($portfolio, $set, portfolioStyle);
				}

				if (!$portfolio.hasClass('disable-isotope')) {
					$(window).on('resize', function () {
						imageSizesFix($portfolio);
					});
				}

				if ($('.portfolio-filters', $portfolio).length) {
					$('.portfolio-filters, .portfolio-filters-resp ul li', $portfolio).on('click', 'a', function () {
						var thisFilter = $(this).data('filter');

						$('.portfolio-filters a.active, .portfolio-filters-resp ul li a.active', $portfolio).removeClass('active');
						$('.portfolio-filters a[data-filter="' + thisFilter + '"], .portfolio-filters-resp ul li a[data-filter="' + thisFilter + '"]', $portfolio).addClass('active');

						filterPortfolioExtended($portfolio, thisFilter.substr(1));

						if ($('.portfolio-filters-resp', $portfolio).size() > 0 && typeof $.fn.dlmenu === 'function') {
							$('.portfolio-filters-resp', $portfolio).dlmenu('closeMenu');
						}

						return false;
					});
				}

				if ($('.portfolio-filter-tabs', $portfolio).length) {

					if ($('.portfolio-filter-tabs-list-tab.active', $portfolio).data('filter') == 'categories') {
						$portfolio.data('portfolio-filter', $('.portfolio-filter-tabs-list-tab.active', $portfolio).data('filter-cat'));
					} else {
						$portfolio.data('portfolio-filter-status', [$('.portfolio-filter-tabs-list-tab.active', $portfolio).data('filter')]);
					}

					$('.portfolio-filter-tabs-list-tab', $portfolio).on('click', function (e) {
						if (!$(this).hasClass('active')) {
							$('.portfolio-filter-tabs-list-tab', $portfolio).removeClass('active');
							$(this).addClass('active');
							if ($(this).data('filter') == 'categories') {
								$portfolio.data('portfolio-filter', $(this).data('filter-cat'));
								$portfolio.data('portfolio-filter-status', '');
							} else {
								$portfolio.data('portfolio-filter', '');
								$portfolio.data('portfolio-filter-status', [$(this).data('filter')]);
							}
							$portfolio.data('current-tab', $(this).data('num'));
							$portfolio.data('next-page', 1);
							var uid = $portfolio.data('portfolio-uid');
							portfolio_load_core_request($portfolio);
						}
					});
				}

				if ($('.portfolio-filters-list', $portfolio).length) {
					if ($(window).outerWidth() < 992) {
						if ($('.portfolio-filters-list', $portfolio).hasClass('style-standard')) {
							$('.portfolio-filters-list', $portfolio).addClass('style-standard-mobile');
						}
						if ($('.portfolio-filters-list', $portfolio).hasClass('style-sidebar')) {
							$('.portfolio-filters-list', $portfolio).addClass('style-sidebar-mobile');
						}
					} else {
						if ($('.portfolio-filters-list', $portfolio).hasClass('style-standard')) {
							$('.portfolio-filters-list', $portfolio).removeClass('style-standard-mobile');
						}
						if ($('.portfolio-filters-list', $portfolio).hasClass('style-sidebar')) {
							$('.portfolio-filters-list', $portfolio).removeClass('style-sidebar-mobile');
						}
					}

					$(window).on('resize', function () {
						if ($(window).outerWidth() < 992) {
							if ($('.portfolio-filters-list', $portfolio).hasClass('style-standard')) {
								$('.portfolio-filters-list', $portfolio).addClass('style-standard-mobile');
							}
							if ($('.portfolio-filters-list', $portfolio).hasClass('style-sidebar')) {
								$('.portfolio-filters-list', $portfolio).addClass('style-sidebar-mobile');
							}
						} else {
							if ($('.portfolio-filters-list', $portfolio).hasClass('style-standard')) {
								$('.portfolio-filters-list', $portfolio).removeClass('style-standard-mobile');
							}
							if ($('.portfolio-filters-list', $portfolio).hasClass('style-sidebar')) {
								$('.portfolio-filters-list', $portfolio).removeClass('style-sidebar-mobile');
							}
						}
					});

					$('.portfolio-filters-list:not(.native) .portfolio-filter-item', $portfolio).on('click', 'a', function (e) {
						e.preventDefault();
						var thisItem = $(this).parents('.portfolio-filter-item');
						if (($(this).hasClass('active') && !thisItem.hasClass('multiple')) || ($(this).hasClass('disable'))) {
							return;
						}
						var typeFilter = $(this).data('filter-type');
						var thisFilter = $(this).data('filter');
						var selectedFilters = $('.portfolio-selected-filters', $portfolio);

						if (typeFilter === 'category') {
							if (thisItem.hasClass('reload')) {
								var shopUrl = $('input#shop-page-url').val();
								if (thisFilter !== '*') {
									shopUrl = $(this).attr("href");
								}
								var href = window.location.href;
								if (href.indexOf('?') > 1) {
									var queryParams = new URLSearchParams(href.split("?")[1]);
									queryParams.delete('page');
									shopUrl += '?' + queryParams.toString();
								}

								shopUrl = shopUrl.replace(/\/page\/\d+/, "").replace("&amp;", '&').replace("%2C", ',');
								window.location.href = shopUrl;
								return;
							} else {
								thisItem.find('a').removeClass('active');
								thisItem.find('a[data-filter="' + thisFilter + '"]', $portfolio).addClass('active').parents('li').find(' > a').addClass('active');
								selectedFilters.find('.portfolio-selected-filter-item.category').remove();
								if (thisFilter !== '*') {
									if (!$(this).parents('.portfolio-filters-list').hasClass('single-filter')) {
										selectedFilters.append('<div class="portfolio-selected-filter-item category" data-filter="' + thisFilter + '">' + $(this).find('.title').html() + '<i class="delete-filter"></i></div>');
									}
									filterPortfolioExtended($portfolio, thisFilter);
								} else {
									filterPortfolioExtended($portfolio, '');
								}
							}
						} else if (typeFilter === 'attribute') {
							var attr = $(this).data('attr');
							var attrData = $portfolio.data('portfolio-filter-attributes');
							if (!attrData) {
								attrData = []
							}
							if (!attrData[attr]) {
								attrData[attr] = [];
							}

							if ($(this).hasClass('all')) {
								delete attrData[attr];
								thisItem.find('a').removeClass('active');
								$(this).addClass('active');
								selectedFilters.find('.portfolio-selected-filter-item[data-attr="' + attr + '"]').remove();
							} else if ($(this).hasClass('active')) {
								$(this).removeClass('active');
								if (attrData[attr]) {
									const index = attrData[attr].indexOf(thisFilter);
									if (index > -1) {
										attrData[attr].splice(index, 1);
									}
									if (attrData[attr].length === 0) {
										delete attrData[attr];
										thisItem.find('a.all').addClass('active');
									}
								}
								selectedFilters.find('.portfolio-selected-filter-item[data-attr="' + attr + '"][data-filter="' + thisFilter + '"]').remove();
							} else {
								if (!thisItem.hasClass('multiple')) {
									thisItem.find('a').removeClass('active');
									attrData[attr] = [];
									selectedFilters.find('.portfolio-selected-filter-item[data-attr="' + attr + '"]').remove();
								} else {
									thisItem.find('a.all').removeClass('active');
								}
								$(this).addClass('active');
								attrData[attr].push(thisFilter);
								selectedFilters.append('<div class="portfolio-selected-filter-item attribute" data-attr="' + attr + '" data-filter="' + thisFilter + '">' + $(this).find('.title').html() + '<i class="delete-filter"></i></div>');
							}
							filterPortfolioAttributes($portfolio, attrData);
						} else if (typeFilter === 'status') {
							var statusData = $portfolio.data('portfolio-filter-status');
							if (!statusData) {
								statusData = []
							}
							if ($(this).hasClass('all')) {
								statusData = '';
								thisItem.find('a').removeClass('active');
								$(this).addClass('active');
								selectedFilters.find('.portfolio-selected-filter-item.status').remove();
							} else if ($(this).hasClass('active')) {
								$(this).removeClass('active');
								const index = statusData.indexOf(thisFilter);
								if (index > -1) {
									statusData.splice(index, 1);
								}
								if (statusData.length === 0) {
									thisItem.find('a.all').addClass('active');
									statusData = '';
								}
								selectedFilters.find('.portfolio-selected-filter-item.status[data-filter="' + thisFilter + '"]').remove();
							} else {
								thisItem.find('a.all').removeClass('active');
								$(this).addClass('active');
								statusData.push(thisFilter);
								selectedFilters.append('<div class="portfolio-selected-filter-item status" data-filter="' + thisFilter + '">' + $(this).find('.title').html() + '<i class="delete-filter"></i></div>');
							}
							filterPortfolioStatus($portfolio, statusData);
						}

						if ($(this).parents('.portfolio-filters-list').hasClass('scroll-top')) {
							$("html, body").animate({scrollTop: $portfolio.offset().top - 200}, 600);
						}

						return false;
					});

					$('.portfolio-filters-list', $portfolio).on('click', '.portfolio-show-filters-button', function () {
						$portfolio.addClass('filters-opened');
						$(this).next().addClass('visible');
					}).on('click', '.portfolio-close-filters', function () {
						closeFiltersPopup($portfolio);
					}).on('click', '.portfolio-filters-outer', function () {
						closeFiltersPopup($portfolio);
					}).on('click', '.portfolio-filters-area', function (e) {
						e.stopPropagation();
					});

					$('.progress-bar', $portfolio).on('click', '.show', function () {
						closeFiltersPopup($portfolio);
					});

					if (!$('.portfolio-filters-list', $portfolio).hasClass('native')) {
						// checkFilters($portfolio);
					}

					if ($('.portfolio-filters-list', $portfolio).hasClass('native-ajax-filters')) {

						$('.widget_layered_nav, .widget_rating_filter, .widget_layered_nav_filters', $portfolio).off('click', 'a').on('click', 'a', function (e) {
							e.preventDefault();
							var href = $(this).attr('href');
							nativeAjaxFiltering(href);
						});

						$('.widget_layered_nav select').off('change').on('change', function (e) {
							e.preventDefault();

							var $this = $(this),
								name = $this.closest('form').find('input[type=hidden]').length ? $this.closest('form').find('input[type=hidden]').attr('name').replace('filter_', '') : $this.attr('class').replace('dropdown_layered_nav_', ''),
								slug = $this.val(),
								href;

							href = window.location.href;
							href = href.replace(/\/page\/\d+/, "").replace("&amp;", '&').replace("%2C", ',');

							href = updateUrlParams(href, 'filter_' + name, slug);

							nativeAjaxFiltering(href);
						});

						$('.widget_product_search, .wc-block-product-search').on('submit', 'form', function (e) {
							e.preventDefault();

							var $this = $(this),
								search = $this.find('input[type=search]').val(),
								href;

							href = window.location.href;
							href = href.replace(/\/page\/\d+/, "").replace("&amp;", '&').replace("%2C", ',');

							href = updateUrlParams(href, 'product-search', search);

							nativeAjaxFiltering(href);
						});


						$('.widget_price_filter .price_slider_wrapper').off('click', '.button').on('click', '.button', function (e) {
							e.preventDefault();

							var form = $(this).closest('form'),
								action = form.attr('action'),
								href = action + (-1 === action.indexOf('?') ? '?' : '&') + form.serialize();
							$('.widget_price_filter').removeClass('yith-wcan-list-price-filter');

							nativeAjaxFiltering(href);
						});

						$('.portfolio-selected-filters', $portfolio).on('click', '.delete-filter', function () {

							var $this = $(this),
								href;

							href = window.location.href;
							href = href.replace(/\/page\/\d+/, "").replace("&amp;", '&').replace("%2C", ',');

							var filtersList = $('.portfolio-filters-list', $portfolio);
							var selectedFilters = $('.portfolio-selected-filters', $portfolio);
							var item = $this.closest('.portfolio-selected-filter-item');
							if (item.hasClass('category')) {
								// href = updateUrlParams(href, 'category', '');
								// var params = '',
								// shopUrl = $('input.shop-page-url').val();
								// if (href.indexOf('?') > 1) {
								// 	params = '?'+href.split("?")[1];
								// }
								// nativeAjaxFiltering(shopUrl+params);
							} else if (item.hasClass('attribute')) {

								var attr = 'filter_' + item.data("attr"),
									filter = item.data("filter"),
									params,
									queryParams = new URLSearchParams(window.location.search);
								for (var p of queryParams) {
									if (p[0].includes(attr)) {
										params = p[1].split(",");
										var index = params.indexOf(filter);
										if (index > -1) {
											params.splice(index, 1);
										}
									}
								}
								href = updateUrlParams(href, attr, params.toString());
								nativeAjaxFiltering(href);

							} else if (item.hasClass('price')) {
								href = updateUrlParams(href, 'min_price', '');
								href = updateUrlParams(href, 'max_price', '');
								nativeAjaxFiltering(href);
							} else if (item.hasClass('search')) {
								href = updateUrlParams(href, 'product-search', '');
								nativeAjaxFiltering(href);
							}

							if ($(this).parents('.portfolio-filters-list').hasClass('scroll-top')) {
								$("html, body").animate({scrollTop: $portfolio.offset().top - 200}, 600);
							}
						}).on('click', '.clear-filters', function (e) {
							var shopUrl = $('input#shop-page-url').val();
							nativeAjaxFiltering(shopUrl);
							if ($(this).parents('.portfolio-filters-list').hasClass('scroll-top')) {
								$("html, body").animate({scrollTop: $portfolio.offset().top - 200}, 600);
							}
						});
					} else {
						$('.portfolio-selected-filters', $portfolio).on('click', '.delete-filter', function () {
							var filtersList = $('.portfolio-filters-list', $portfolio);
							var selectedFilters = $('.portfolio-selected-filters', $portfolio);
							var item = $(this).closest('.portfolio-selected-filter-item');
							if (item.hasClass('category')) {
								if (filtersList.find('a.all[data-filter-type="category"]').length) {
									filtersList.find('a.all[data-filter-type="category"]').click();
								} else {
									selectedFilters.find('.portfolio-selected-filter-item.category').remove();
									clearFilters($portfolio);
								}
							} else if (item.hasClass('attribute')) {
								if (filtersList.find('.portfolio-selected-filter-item.' + item.data("attr")).hasClass('multiple')) {
									filtersList.find('a[data-attr="' + item.data("attr") + '"][data-filter="' + item.data("filter") + '"]').click();
								} else {
									filtersList.find('a.all[data-attr="' + item.data("attr") + '"]').click();
								}
							} else if (item.hasClass('price')) {
								var min = $portfolio.find(".slider-range").slider("option", "min");
								var max = $portfolio.find(".slider-range").slider("option", "max");
								$portfolio.find(".slider-range").slider("values", [min, max]);
								selectedFilters.find('.portfolio-selected-filter-item.price').remove();
								filterPortfolioPrice($portfolio, '');
							} else if (item.hasClass('status')) {
								filtersList.find('a[data-filter-type="status"][data-filter="' + item.data("filter") + '"]').click();
							} else if (item.hasClass('search')) {
								$portfolio.find(".portfolio-search-filter .portfolio-search-filter-form input").val('');
								selectedFilters.find('.portfolio-selected-filter-item.search').remove();
								filterPortfolioSearch($portfolio, '');
							}

							if ($(this).parents('.portfolio-filters-list').hasClass('scroll-top')) {
								$("html, body").animate({scrollTop: $portfolio.offset().top - 200}, 600);
							}
						}).on('click', '.clear-filters', function () {
							var filtersList = $('.portfolio-filters-list', $portfolio);
							filtersList.find('.portfolio-filter-item:not(.reload) a').removeClass('active');
							filtersList.find('.portfolio-filter-item:not(.reload) a.all').addClass('active');
							if ($portfolio.find(".slider-range").length) {
								var min = $portfolio.find(".slider-range").slider("option", "min");
								var max = $portfolio.find(".slider-range").slider("option", "max");
								$portfolio.find(".slider-range").slider("values", [min, max]);
							}
							$portfolio.find(".portfolio-search-filter input").val('');
							$portfolio.find(".portfolio-selected-filters .portfolio-selected-filter-item:not(.clear-filters)").remove();
							clearFilters($portfolio);
							if ($(this).parents('.portfolio-filters-list').hasClass('scroll-top')) {
								$("html, body").animate({scrollTop: $portfolio.offset().top - 200}, 600);
							}
						});
					}
				}


				if ($('.portfolio-filters-list:not(.native) .portfolio-search-filter', $portfolio).length) {
					$('.portfolio-search-filter', $portfolio).on('click', '.portfolio-search-filter-button', function () {
						if ($(this).parents('.portfolio-search-filter').hasClass('active')) {
							$(this).parents('.portfolio-search-filter').submit();
						} else {
							$(this).parents('.portfolio-search-filter').addClass('active');
						}
					}).on('change', 'input', function () {
						$(this).parents('.portfolio-search-filter').addClass('changed');
					}).on('mouseenter', function () {
						if ("ontouchstart" in document.documentElement) {
						} else {
							$(this).addClass('active');
						}
					}).on('mouseleave', function (e) {
						$(this).removeClass('active');
					}).on('submit', function (e) {
						e.preventDefault();
						if ($(this).hasClass('changed')) {
							var value = $(this).find('input').val();
							$('.portfolio-selected-filters', $portfolio).find('.portfolio-selected-filter-item.search').remove();
							if (value != '') {
								$('.portfolio-selected-filters', $portfolio).append('<div class="portfolio-selected-filter-item search">' + value + '<i class="delete-filter"></i></div>');
							}
							filterPortfolioSearch($portfolio, value);
							$('.portfolio-search-filter', $portfolio).removeClass('changed');
							closeFiltersPopup($portfolio);
						}
						$(this).find('input').blur();
						$('.portfolio-search-filter', $portfolio).removeClass('active');
					});
				}

				categoryFilterClick($portfolio);

				$portfolio.on('click', '.info a:not(.zilla-likes)', function () {
					var slug = $(this).data('slug') || '';

					if ($('.portfolio-filters', $portfolio).length) {
						$('.portfolio-filters a[data-filter=".' + slug + '"]').click();
					} else {
						filterPortfolioExtended($portfolio, slug);
					}
					return false;
				});

				$('.portfolio-load-more', $portfolio).on('click', function () {
					portfolio_load_core_request($portfolio);
				});

				if ($portfolio.hasClass('portfolio-pagination-scroll')) {
					init_portfolio_scroll_next_page($portfolio);
				}

				if (!$portfolio.hasClass('news-grid')) {
					$portfolio.on('click', '.portfolio-item .image .overlay'/*, .portfolio-item .wrap > .caption'*/, function (event) {

						if ($portfolio.hasClass('caption-position-hover') && $(this).parents('.portfolio-item').hasClass('hover-effect')) {
							if (!$(this).parents('.portfolio-item').hasClass('hover-effect-active')) {
								return
							}
						}
						var $target = $(event.target),
							$icons = $target.closest('.portfolio-item').find('.portfolio-icons'),
							$product_link = $target.closest('.portfolio-item').find('.product-link');
						if ($target.hasClass('product-link') || $target.hasClass('add_to_cart_button') || $target.closest('.add_to_cart_button').length || $target.closest('.icon').length || $target.closest('.socials-sharing').length || !$icons.length || $target.closest('.quick-view-button').length) {
							return;
						}

						if (window.gemSettings.isTouch) {
							if ($(this).children('.product-link').length) {
								window.location.href = $product_link.attr('href');
								return false;
							}
							if (!$target.closest('.portfolio-item').hasClass('touch-hover')) {
								$target.closest('.portfolio-item').addClass('touch-hover');
								$('*').one('click', function (event) {
									if (!$(event.target).closest('.portfolio-item').is($target.closest('.portfolio-item'))) {
										$target.closest('.portfolio-item').removeClass('touch-hover');
									}
								});
								return false;
							}
						}

						if ($('.icon.self-link', $icons).length) {
							window.open(
								$('.icon.self-link', $icons).attr('href'),
								$('.icon.self-link', $icons).attr('target')
							);
						} else if ($('.icon.bottom-product-link', $icons).length) {
							window.open($('.icon.bottom-product-link', $icons).attr('href'), "_self");
						} else if ($product_link.length) {
							window.open($product_link.attr('href'), "_self");
						} else {
							var $firstIcon = $('.icon', $icons).first();

							if ($firstIcon.hasClass('inner-link') || $firstIcon.hasClass('outer-link')) {
								window.open(
									$firstIcon.attr('href'),
									$firstIcon.attr('target')
								);
							} else {
								$firstIcon.click();
							}
						}
					});
				}
			});

			if (typeof $.fn.dlmenu === 'function') {
				$('.portfolio-filters-resp', $portfolio).dlmenu({
					animationClasses: {
						classin: 'dl-animate-in',
						classout: 'dl-animate-out'
					}
				});
			}
		}


		function layoutComplete($portfolio, $set, laidOutItems) {
			var setWidth = $set[0].offsetWidth;

			for (var i = 0; i < laidOutItems.length; i++) {
				var item = laidOutItems[i],
					element;
				if (item.element) {
					element = item.element;
				} else {
					element = item;
				}
				var itemWrapWidth = element.querySelector('.wrap').offsetWidth,
					itemPadding = parseFloat($(element).css('padding-left'));

				if (isNaN(itemPadding)) {
					itemPadding = 0;
				}

				if ($portfolio.hasClass('hover-title') && $portfolio.hasClass('item-separator')) {
					element.style.zIndex = laidOutItems.length - i;
				}

				if (item.position) {
					if (item.position.x === 0) {
						element.classList.add('left-item');
					} else {
						element.classList.remove('left-item');
					}

					if (item.position.y === 0) {
						element.classList.add('top-item');
					} else {
						element.classList.remove('top-item');
					}

					if (item.position.x + itemWrapWidth + 2 * itemPadding > setWidth - 4) {
						element.classList.add('right-item');
					} else {
						element.classList.remove('right-item');
					}
				}

				if (itemWrapWidth < 320) {
					element.classList.add('item-small-size');
				} else {
					element.classList.remove('item-small-size');
				}
			}
		}

		function arrangeComplete($portfolio, $set, portfolioStyle) {
			if ($portfolio.hasClass('title-on-hover') || $portfolio.hasClass('hover-gradient') || $portfolio.hasClass('hover-circular')) {
				$('.portfolio-item .portfolio-icons-inner > a:not(.added_to_cart)', $portfolio).addClass('icon');
			}

			var wishlistTarget;
			$('body').on('click', '.yith-icon a', function () {
				wishlistTarget = $(this).parents('.extended-products-grid');
			});

			$('body').on('added_to_cart', function (e, fragments, cart_hash, this_button) {
				var cartPopup = this_button.parents('.extended-products-grid').find('.thegem-popup-notification.cart');
				$('.thegem-popup-notification').removeClass('visible');
				cartPopup.addClass('visible');
				setTimeout(function () {
					cartPopup.removeClass('visible');
				}, cartPopup.data('timing'));
			});

			$('body').on('added_to_wishlist', function (t, el_wrap) {
				customExtendedIcons($portfolio);
				if (wishlistTarget) {
					$('.thegem-popup-notification').removeClass('visible');
					var wishlistPopupAdd = wishlistTarget.find('.thegem-popup-notification.wishlist-add');
					wishlistPopupAdd.addClass('visible');
					setTimeout(function () {
						wishlistPopupAdd.removeClass('visible');
					}, wishlistPopupAdd.data('timing'));
				}
			});

			$('body').on('removed_from_wishlist', function (t, el_wrap) {
				customExtendedIcons($portfolio);
				if (wishlistTarget) {
					$('.thegem-popup-notification').removeClass('visible');
					var wishlistPopupRemove = wishlistTarget.find('.thegem-popup-notification.wishlist-remove');
					wishlistPopupRemove.addClass('visible');
					setTimeout(function () {
						wishlistPopupRemove.removeClass('visible');
					}, wishlistPopupRemove.data('timing'));
				}
			});

			$(document).on('yith_wcwl_fragments_loaded', function () {
				customExtendedIcons($portfolio);
			});

			$('.portfolio-item .product-bottom .yith-wcwl-wishlistexistsbrowse a', $portfolio).addClass('icon wishlist');

			// Fix problem with YITH loaded by ajax
			$('.portfolio-item .product-bottom .yith-wcwl-add-to-wishlist', $portfolio).each(function () {
				var wishlistItem = $(this);
				var classList = $(this).attr('class').split(/\s+/);
				$.each(classList, function (index, item) {
					var a = item.indexOf("wishlist-fragment");
					if (a !== -1 && a > 0) {
						var res = item.slice(0, a);
						wishlistItem.removeClass(item).addClass(res + ' wishlist-fragment');
					}

				});
			});


			if ($set.closest('.fullwidth-block').size() > 0) {
				$set.closest('.fullwidth-block').bind('fullwidthUpdate', function () {
					if ($set.data('isotope')) {
						$set.isotope('layout');
						return false;
					}
				});
			} else {
				if ($set.closest('.vc_row[data-vc-stretch-content="true"]').length > 0) {
					$set.closest('.vc_row[data-vc-stretch-content="true"]').bind('VCRowFullwidthUpdate', function () {
						if ($set.data('isotope')) {
							$set.isotope('layout');
							return false;
						}
					});
				}
			}

			if (portfolioStyle != 'metro') {
				var onlyDoubleItems = hasOnlyDoubleItems($set);

				if (onlyDoubleItems != $portfolio.hasClass('porfolio-even-columns')) {
					fixPortfolioWithDoubleItems($portfolio, onlyDoubleItems);

					if ($set.data('isotope')) {
						$set.isotope('layout');
					}
				}
			}

			if ($portfolio.hasClass('loading-animation')) {
				$portfolio.itemsAnimations('instance').show($('.portfolio-item', $portfolio));
			}
			$('img.image-hover', $portfolio).show();

			if (window.tgpLazyItems !== undefined) {
				window.tgpLazyItems.scrollHandle();
			}
			$portfolio.closest('.portfolio-preloader-wrapper').prev('.preloader').remove();

		}

		function nativeAjaxFiltering(href) {
			href = updateUrlParams(href, 'page', '');
			href = href.replace(/\/page\/\d+/, "").replace("&amp;", '&').replace("%2C", ',');
			var pos_t,
				$portfolio = $('.portfolio.extended-products-grid');
			if ($portfolio.find('.portfolio-filters-list').hasClass('scroll-top') || $portfolio.hasClass('hide-loader')) {
				if ($portfolio.offset().top + $portfolio.height() < $portfolio.offset().top - 200 + window.innerHeight) {
					pos_t = $portfolio.height() / 2;
				} else {
					pos_t = ($portfolio.offset().top - 200 + window.innerHeight - $portfolio.offset().top) / 2;
				}
			} else {
				if ($portfolio.offset().top + $portfolio.height() < $(window).scrollTop() + window.innerHeight) {
					pos_t = $portfolio.height() / 2;
				} else {
					pos_t = ($(window).scrollTop() + window.innerHeight - $('.portfolio-row-outer', $portfolio).offset().top) / 2;
				}
			}
			$('.portfolio-row-outer', $portfolio).prepend('<div class="preloader-new"><div class="preloader-spin"></div></div>');
			$('.portfolio-row-outer .preloader-spin', $portfolio).css('top', pos_t);
			if ($('.portfolio-filters-list', $portfolio).hasClass('style-sidebar')) {
				var pos_r = ($('.portfolio-top-panel', $portfolio).width()) / 2;
				if ($portfolio.closest('.panel-sidebar-position-right').length) {
					$('.portfolio-row-outer .preloader-spin', $portfolio).css('left', pos_r);
				} else {
					$('.portfolio-row-outer .preloader-spin', $portfolio).css('right', pos_r);
				}
			}
			if ($portfolio.find('.portfolio-filters-list').hasClass('scroll-top')) {
				$("html, body").animate({scrollTop: $portfolio.offset().top - 200}, 600);
			}
			$.ajax({
				url: href,
				type: "POST",
				success: function (response) {
					var $response = $(response);
					if ($response.find('.extended-products-grid').length) {
						$('.portfolio-preloader-wrapper').html($response.find('.extended-products-grid'))
							.find('.count').each(function () {
							$(this).html($(this).html().replace('(', '').replace(')', '')).css('opacity', 1);
						});
						$('.extended-products-grid').initExtendedProductsGrids();
						refreshPriceSlider();
						refreshSelect2();
						history.replaceState(null, null, href);
					}
				}
			});
		}

		function updateUrlParams(uri, key, value) {
			var params = '';
			if (uri.indexOf('?') > 1) {
				params = uri.split("?")[1];
			}
			var queryParams = new URLSearchParams(params);
			if (value !== '') {
				queryParams.set(key, value)
			} else {
				queryParams.delete(key);
			}

			if (queryParams.toString().length > 0) {
				return uri.split("?")[0] + "?" + queryParams.toString();
			} else {
				return uri.split("?")[0];
			}
		}

		function refreshSelect2() {
			var select2 = $('select.woocommerce-widget-layered-nav-dropdown');
			if (select2.length && jQuery().selectWoo) {
				select2.each(function () {
					$(this).selectWoo({
						placeholder: $(this).find('option').eq(0).text(),
						minimumResultsForSearch: 5,
						width: '100%',
						allowClear: typeof $(this).attr('multiple') != 'undefined' && $(this).attr('multiple') == 'multiple' ? 'false' : 'true'
					});
				});
			}
			$('body').children('span.select2-container').remove();
		}

		function refreshPriceSlider() {
			var price_slider = $('.price_slider');
			if (price_slider.length) {
				if (typeof woocommerce_price_slider_params === 'undefined') {
					return false;
				}

				$('input#min_price, input#max_price').hide();
				$('.price_slider, .price_label').show();

				var min_price = $('.price_slider_amount #min_price').data('min'),
					max_price = $('.price_slider_amount #max_price').data('max'),
					current_min_price = parseInt($('.price_slider_amount #min_price').val() ? $('.price_slider_amount #min_price').val() : min_price, 10),
					current_max_price = parseInt($('.price_slider_amount #max_price').val() ? $('.price_slider_amount #max_price').val() : max_price, 10);

				$('.price_slider').slider({
					range: true,
					animate: true,
					min: min_price,
					max: max_price,
					values: [current_min_price, current_max_price],
					create: function () {
						$('.price_slider_amount #min_price').val(current_min_price);
						$('.price_slider_amount #max_price').val(current_max_price);
						$(document.body).trigger('price_slider_create', [current_min_price, current_max_price]);
					},
					slide: function (event, ui) {
						$('input#min_price').val(ui.values[0]);
						$('input#max_price').val(ui.values[1]);
						$(document.body).trigger('price_slider_slide', [ui.values[0], ui.values[1]]);
					},
					change: function (event, ui) {
						$(document.body).trigger('price_slider_change', [ui.values[0], ui.values[1]]);
					},
					stop: function (event, ui) {
						var href = window.location.href;
						href = href.replace(/\/page\/\d+/, "").replace("&amp;", '&').replace("%2C", ',');

						href = updateUrlParams(href, 'min_price', ui.values[0]);
						href = updateUrlParams(href, 'max_price', ui.values[1]);

						$('.widget_price_filter').removeClass('yith-wcan-list-price-filter');
						if ($('.portfolio-filters-list').hasClass('native-ajax-filters')) {
							nativeAjaxFiltering(href);
						} else {
							window.location.href = href;
						}
					}
				});
			}
			$('.price_slider_amount .button').addClass('gem-button gem-button-style-outline gem-button-size-tiny');
		}

		function categoryFilterClick($portfolio) {
			$('.categories', $portfolio).on('click', 'a', function (e) {
				e.preventDefault();
				var thisFilter = $(this).data('filter');
				var thisItem = $('.portfolio-filters-area', $portfolio).find('a[data-filter-type="category"][data-filter="' + thisFilter + '"]');

				if (thisItem.length) {
					thisItem.click();
				} else {
					var selectedFilters = $('.portfolio-selected-filters', $portfolio);
					selectedFilters.find('.portfolio-selected-filter-item.category').remove();
					if (thisFilter !== '*') {
						selectedFilters.append('<div class="portfolio-selected-filter-item category" data-filter="' + thisFilter + '">' + $(this).html() + '<i class="delete-filter"></i></div>');
						filterPortfolioExtended($portfolio, thisFilter);
					} else {
						filterPortfolioExtended($portfolio, '');
					}
				}
			});
		}

		function closeFiltersPopup($portfolio) {
			$portfolio.removeClass('filters-opened');
			$('.portfolio-filters-outer', $portfolio).addClass('close-animation').removeClass('visible');
			setTimeout(function () {
				$('.portfolio-filters-outer', $portfolio).removeClass('close-animation');
				$('.progress-bar', $portfolio).hide();
			}, 300);

		}

		function toggleNewsGridSharing(button) {
			var $meta = $(button).closest('.grid-post-meta-inner'),
				$likes = $('.grid-post-meta-comments-likes', $meta),
				$icons = $('.portfolio-sharing-pane', $meta);

			if ($meta.hasClass('active')) {
				$meta.removeClass('active');

				$('.socials-sharing', $meta).animate({
					width: 'toggle'
				}, 300, function () {
					$meta.removeClass('animation');
				});
			} else {
				$meta.css('min-width', $meta.outerWidth());

				$meta.addClass('active animation');

				$('.socials-sharing', $meta).animate({
					width: 'toggle'
				}, 200);
			}
		}

		function customExtendedIcons($portfolio) {
			$portfolio.find('.post-meta-likes').each(function () {
				if ($(this).find('i').length) {
					if (!$(this).find('a').children('i').length) {
						var icon = $(this).children('i');
						$(this).find('a').prepend(icon.clone());
					}
				} else if ($(this).find('svg').length) {
					if (!$(this).find('a').children('svg').length) {
						var icon_svg = $(this).children('svg');
						$(this).find('a').prepend(icon_svg.clone());
					}
				}
			});

			$portfolio.find('.comments-link').each(function () {
				if ($(this).find('i').length) {
					var icon = $(this).find('i');
					$(this).find('i').remove();
					$(this).find('a').prepend(icon);
				} else if ($(this).find('svg').length) {
					var icon_svg = $(this).find('svg');
					$(this).find('svg').remove();
					$(this).find('a').prepend(icon_svg);
				}
			});

			$portfolio.find('.portfolio-likes').each(function () {
				if ($(this).find('i').length) {
					if (!$(this).find('a').children('i').length) {
						var icon = $(this).children('i');
						$(this).find('a').prepend(icon.clone());
					}
				} else if ($(this).find('svg').length) {
					if (!$(this).find('a').children('svg').length) {
						var icon_svg = $(this).children('svg');
						$(this).find('a').prepend(icon_svg.clone());
					}
				}
			});

			$portfolio.find('.portfolio-list-likes').each(function () {
				if ($(this).find('i').length) {
					if (!$(this).find('a').children('i').length) {
						var icon = $(this).children('i');
						$(this).find('a').prepend(icon.clone());
					}
				} else if ($(this).find('svg').length) {
					if (!$(this).find('a').children('svg').length) {
						var icon_svg = $(this).children('svg');
						$(this).find('a').prepend(icon_svg.clone());
					}
				}
			});

			if ($portfolio.hasClass('extended-products-grid')) {
				$portfolio.find('.yith-icon').each(function () {
					var addIcon = $(this).children('.add-wishlist-icon').clone();
					var addedIcon = $(this).children('.added-wishlist-icon').clone();
					$(this).find('a i').remove();
					$(this).find('a svg').remove();
					$(this).find('.yith-wcwl-add-button a:not(.delete_item)').prepend(addIcon);
					$(this).find('.yith-wcwl-add-button a.delete_item').prepend(addedIcon);
					$(this).find('.yith-wcwl-wishlistexistsbrowse a').prepend(addedIcon);
					$(this).find('a').addClass('icon');
					$(this).find('a.gem-button').removeAttr('class').removeAttr('style').removeAttr('onmouseleave').removeAttr('onmouseenter').addClass('icon');
					$(this).find('.yith-wcwl-wishlistaddedbrowse a').prepend(addedIcon);
				});
			} else {
				$portfolio.find('.yith-icon').each(function () {
					if ($(this).find('i').length) {
						if (!$(this).find('.yith-wcwl-add-button a').children('i').length) {
							var icon = $(this).children('i');
							$(this).find('.yith-wcwl-add-button a').prepend(icon.clone());
						}
					} else if ($(this).find('svg').length) {
						if (!$(this).find('.yith-wcwl-add-button a').children('svg').length) {
							var icon_svg = $(this).children('svg');
							$(this).find('.yith-wcwl-add-button a').prepend(icon_svg.clone());
						}
					}
				});
			}

		}

		function initPriceSlider($portfolio) {
			var range = $portfolio.find(".slider-range");
			if (range.length === 0) return;
			var amount = $portfolio.find(".slider-amount .slider-amount-value");
			var currency = range.data('currency');
			var selectedFilters = $('.portfolio-selected-filters', $portfolio);
			var values = $portfolio.data('portfolio-filter-price');
			if (values == null) {
				values = [parseFloat(range.data('min')), parseFloat(range.data('max'))];
			}
			range.slider({
				range: true,
				min: parseFloat(range.data('min')),
				max: parseFloat(range.data('max')),
				values: values,
				slide: function (event, ui) {
					amount.html(currency + ui.values[0] + " - " + currency + ui.values[1]);
				},
				stop: function (event, ui) {
					filterPortfolioPrice($portfolio, ui.values);
					selectedFilters.find('.portfolio-selected-filter-item.price').remove();
					selectedFilters.append('<div class="portfolio-selected-filter-item price">' + currency + ui.values[0] + " - " + currency + ui.values[1] + '<i class="delete-filter"></i></div>');
					if ($portfolio.find('.portfolio-filters-list').hasClass('scroll-top')) {
						$("html, body").animate({scrollTop: $portfolio.offset().top - 200}, 600);
					}
				},
				change: function (event, ui) {
					amount.html(currency + ui.values[0] + " - " + currency + ui.values[1]);
				}
			});
			amount.html(currency + range.slider("values", 0) +
				" - " + currency + range.slider("values", 1));
		}

		$('body').on('click', '.portfolio.portfolio-grid.extended-products-grid a.icon.share', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).next('.sharing-popup').removeClass('right').removeClass('left');
			var offsetLeft = $(this).offset().left;
			var offsetRight = ($(window).width() - (offsetLeft + $(this).outerWidth()));
			if (offsetLeft < 100) {
				$(this).next('.sharing-popup').addClass('left');
			} else if (offsetRight < 100) {
				$(this).next('.sharing-popup').addClass('right');
			}
			if ($(this).closest('.links').find('.portfolio-sharing-pane').hasClass('active') ||
				$(this).closest('.post-footer-sharing').find('.sharing-popup').hasClass('active')) {
				$('.portfolio-sharing-pane').removeClass('active');
				$('.sharing-popup').removeClass('active');
			} else {
				$('.portfolio-sharing-pane').removeClass('active');
				$('.sharing-popup').removeClass('active');
				$(this).closest('.links').find('.portfolio-sharing-pane').addClass('active');
				$(this).closest('.post-footer-sharing').find('.sharing-popup').addClass('active');
			}
			return false;
		});

		$('body').on('click', function () {
			$('.portfolio-sharing-pane').removeClass('active');
			$('.sharing-popup').removeClass('active');
		}).on('touchstart', function (e) {
			$('.extended-products-grid .portfolio-item').removeClass('hover-effect');
			$('.extended-products-grid .portfolio-item').removeClass('hover-effect-active');
		});

		$('.extended-products-grid .portfolio-item').on('touchstart', function (e) {
			// e.preventDefault();
			e.stopPropagation();
			$('.extended-products-grid .portfolio-item').not(this).removeClass('hover-effect');
			$('.extended-products-grid .portfolio-item').not(this).removeClass('hover-effect-active');
			$(this).addClass('hover-effect');
			let item = $(this);
			setTimeout(function () {
				item.addClass('hover-effect-active');
			}, 500);
		});

		/*$('body').on('DOMSubtreeModified', '.zilla-likes', function () {
			if (!$(this).children('i').length) {
				var icon = $(this).siblings('i');
				$(this).prepend(icon.clone());
			}
		});

		$('body').on('click', '.portfolio.portfolio-grid:not(.news-grid) a.icon.share', function (e) {
			e.preventDefault();
			$(this).closest('.links').find('.portfolio-sharing-pane').toggleClass('active');
			$(this).closest('.post-footer-sharing').find('.sharing-popup').toggleClass('active');
			return false;
		});

		$('body').on('click', '.portfolio.news-grid a.icon.share', function (e) {
			e.preventDefault();

			if ($(this).closest('.portfolio').hasClass('version-new') ||
				($(this).closest('.portfolio').hasClass('version-default') &&
					$(this).closest('.portfolio').hasClass('title-on-hover'))
			) {
				toggleNewsGridSharing(this);
			} else {
				$(this).closest('.links').find('.portfolio-sharing-pane').toggleClass('active');
			}
			return false;
		});

		$('body').on('mouseleave', '.portfolio.portfolio-grid .portfolio-item', function () {
			$('.portfolio-sharing-pane').removeClass('active');
		});

		$('body').on('click', '.portfolio.portfolio-grid .portfolio-item', function () {
			$(this).mouseover();
		});*/

		$.fn.initExtendedProductsGrids = function () {
			$(this).each(initExtendedProductsGrid);
		};

		$(document).ready(function () {
			$('body:not(.elementor-editor-active) .extended-products-grid:not(.extended-products-grid-carousel)').initExtendedProductsGrids();

			// $('.portfolio-filters-list .widget_layered_nav, .portfolio-filters-list .widget_product_categories').find('.count').each(function () {
			// 	$(this).html($(this).html().replace('(', '').replace(')', ''));
			// });

			refreshPriceSlider();
		});

		setTimeout(function () {
			if ($('body:not(.elementor-editor-active) .preloader + .portfolio-preloader-wrapper').length) {
				$('.extended-products-grid:not(.extended-products-grid-carousel)').initExtendedProductsGrids();
			}
		}, 2000);
		
		$(window).on('load', function () {
			if (window.tgpLazyItems !== undefined) {
				window.tgpLazyItems.scrollHandle();
			}
		})
	});
})(jQuery);