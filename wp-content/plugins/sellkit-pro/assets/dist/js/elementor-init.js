(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = _default;

/* eslint no-undef: 0 */
// Temporary turned off no undefined variable for localStorage
var $ = jQuery;

function _default() {
  preFilledFormForAbandonedUsers();
}
/**
 * Pre-filled form for abandoned users.
 */


var preFilledFormForAbandonedUsers = function preFilledFormForAbandonedUsers() {
  var $fields = $('.sellkit-checkout-local-fields').find('input, select, hidden, textarea, #sellkit-billing_state ,#sellkit-shipping_state, .validate-email');
  $fields.each(function () {
    var fieldType = $(this).attr('type');
    var $thisId = $(this).attr('id');
    var $thisValue = $(this).val(); // Check if field is empty , check localstorage for value if exists , set value.

    if (_.isEmpty($thisValue)) {
      var localStorageName = 'sellkit-pro' + '-' + $thisId;
      var localStorageValue = localStorage.getItem(localStorageName);

      if (localStorageValue) {
        var $newValue = JSON.parse(localStorageValue);
        $(this).val($newValue.value);
        $thisValue = $newValue.value; // Trigger keyup if fields has value.

        $(this).trigger('keyup');
      }
    }

    $(this).on('change input focusout', function () {
      // Save all fields value in localStorage except password fields and createaccount checkbox.
      if ('password' !== fieldType || 'createaccount' !== $thisId) {
        saveFieldsInLocalStorage($thisId);
      }
    });
  });
};

var saveFieldsInLocalStorage = function saveFieldsInLocalStorage(fieldId) {
  var field = document.getElementById(fieldId);
  var name = 'sellkit-pro' + '-' + fieldId;

  if (_.isNull(field) || _.isEmpty(field.value)) {
    localStorage.removeItem(name);
    return;
  }

  var object = {
    value: field.value,
    expiry: 7 * 86400
  };
  localStorage.setItem(name, JSON.stringify(object));
};

},{}],2:[function(require,module,exports){
"use strict";

(function ($) {
  var SellkitFrontend = function SellkitFrontend() {
    var widgets = {
      'sellkit-product-images.default': require('./product-images')["default"],
      'sellkit-product-filter.default': require('./product-filter')["default"],
      'sellkit-checkout.default': require('./checkout')["default"],
      'sellkit-personalised-coupons.default': require('./personalised-coupons')["default"]
    };

    function elementorInit() {
      for (var widget in widgets) {
        elementorFrontend.hooks.addAction("frontend/element_ready/".concat(widget), widgets[widget]);
      }
    }

    this.init = function () {
      $(window).on('elementor/frontend/init', elementorInit);
    };

    this.init();
  };

  window.sellkitFrontend = new SellkitFrontend();
})(jQuery);

},{"./checkout":1,"./personalised-coupons":3,"./product-filter":4,"./product-images":5}],3:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = _default;
var PersonalisedCoupons = elementorModules.frontend.handlers.Base.extend({
  onInit: function onInit() {
    if (document.body.classList.contains('elementor-editor-active')) {
      return;
    }

    this.checkNewCoupon();
  },
  checkNewCoupon: function checkNewCoupon() {
    var couponCookie = this.getCookie('sellkit_personalised_coupon');
    var savedCoupon = '';

    if (!_.isEmpty(couponCookie)) {
      savedCoupon = JSON.parse(couponCookie);
      this.checkCookieCoupon(savedCoupon);
      return;
    }

    this.getCoupon();
  },
  getCookie: function getCookie(cname) {
    var name = cname + '=';
    var decodedCookie = decodeURIComponent(window.document.cookie);
    var Cookies = decodedCookie.split(';');

    for (var i = 0; i < Cookies.length; i++) {
      var cookie = Cookies[i];

      while (cookie.charAt(0) === ' ') {
        cookie = cookie.substring(1);
      }

      if (cookie.indexOf(name) === 0) {
        return cookie.substring(name.length, cookie.length);
      }
    }

    return false;
  },
  applyCoupon: function applyCoupon(coupon) {
    this.$element.find('.sellkit-personalised-coupons-wrap').addClass('sellkit_has_coupon');
    this.$element.find('.sellkit-personalised-coupons-expiration-date-value').html(coupon.expiration_date);
    this.$element.find('.sellkit-personalised-coupons-code-box').html(coupon.code);

    if (_.isEmpty(coupon.expiration_date)) {
      this.$element.find('.sellkit-personalised-coupons-expiration-date').remove();
    }
  },
  checkCookieCoupon: function checkCookieCoupon(couponCookie) {
    var _this = this;

    wp.ajax.send('sellkit_check_personalised_coupon', {
      type: 'GET',
      data: {
        rule_id: couponCookie.rule_id,
        nonce: window.sellkit_elementor.nonce
      },
      dataType: 'json',
      success: function success(result) {
        if (result.coupon_is_valid.toString() === 'true') {
          _this.applyCoupon(couponCookie);
        }
      },
      error: function error(data) {
        // eslint-disable-next-line no-console
        console.error(data);
      }
    });
  },
  getCoupon: function getCoupon() {
    var _this2 = this;

    wp.ajax.send('sellkit_get_personalised_coupons', {
      type: 'GET',
      data: {
        nonce: window.sellkit_elementor.nonce,
        display_type: 'add-to-content'
      },
      dataType: 'json',
      success: function success(result) {
        if (_.isUndefined(result) || _.isEmpty(result.code)) {
          return;
        }

        _this2.applyCoupon(result);
      },
      error: function error(data) {
        // eslint-disable-next-line no-console
        console.error(data);
      }
    });
  }
});

function _default($scope) {
  new PersonalisedCoupons({
    $element: $scope
  });
}

},{}],4:[function(require,module,exports){
"use strict";

var _interopRequireDefault = require("@babel/runtime/helpers/interopRequireDefault");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = _default;

var _slicedToArray2 = _interopRequireDefault(require("@babel/runtime/helpers/slicedToArray"));

var $ = jQuery;
var ProductFilter = elementorModules.frontend.handlers.Base.extend({
  getDefaultSettings: function getDefaultSettings() {
    return {
      selectors: {
        ProductFilter: '.sellkit-product-filter',
        ProductFilterForm: '.sellkit-product-filter-form',
        ProductFilterContent: '.sellkit-product-filter-content',
        ProductFilterRadio: '.product-filter-item-radio',
        ProductFilterCheckbox: '.product-filter-item-checkbox',
        ProductFilterLinks: '.product-filter-item-links',
        ProductFilterSelect: '.product-filter-item-select',
        ProductFilterButton: '.product-filter-item-button',
        ProductFilterImage: '.product-filter-item-image',
        ProductFilterColor: '.product-filter-item-color',
        ProductFilterToggle: '.product-filter-item-toggle',
        ProductFilterSelector: '.product-filter-selector',
        ProductFilterRange: '.sellkit-product-filter-custom-range-form',
        ProductFilterSearch: '.sellkit-product-filter-search-text',
        ProductFilterVerticalToggle: '.sellkit-toggle-able-heading',
        ProductFilterCategoryToggle: '.sellkit-category-filter-toggle .product-filter-item',
        ProductFilterItems: '.product-filter-item'
      }
    };
  },
  getDefaultElements: function getDefaultElements() {
    var selectors = this.getSettings('selectors');
    return {
      $productFilter: this.$element.find(selectors.ProductFilter),
      $productFilterForm: this.$element.find(selectors.ProductFilterForm),
      $productFilterContent: this.$element.find(selectors.ProductFilterContent),
      $productFilterRadio: this.$element.find(selectors.ProductFilterRadio),
      $productFilterCheckbox: this.$element.find(selectors.ProductFilterCheckbox),
      $productFilterLinks: this.$element.find(selectors.ProductFilterLinks),
      $productFilterSelect: this.$element.find(selectors.ProductFilterSelect),
      $productFilterButton: this.$element.find(selectors.ProductFilterButton),
      $productFilterImage: this.$element.find(selectors.ProductFilterImage),
      $productFilterColor: this.$element.find(selectors.ProductFilterColor),
      $productFilterToggle: this.$element.find(selectors.ProductFilterToggle),
      $productFilterSelector: this.$element.find(selectors.ProductFilterSelector),
      $productFilterRange: this.$element.find(selectors.ProductFilterRange),
      $productFilterSearch: this.$element.find(selectors.ProductFilterSearch),
      $productFilterVerticalToggle: this.$element.find(selectors.ProductFilterVerticalToggle),
      $productFilterCategoryToggle: this.$element.find(selectors.ProductFilterCategoryToggle),
      $productFilterItems: this.$element.find(selectors.ProductFilterItems),
      $ajaxResult: {},
      $loadMoreClickCount: 0,
      $wcSortValue: null,
      $wcSortField: null,
      $wooWrapper: null,
      $isShop: null,
      $preLoader: null,
      $isElementor: true,
      $isArchive: true,
      $isSingel: true
    };
  },
  onInit: function onInit() {
    elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
    var self = this;
    this.$isElementor = $('body').hasClass('elementor-page');
    this.$isArchive = ['archive', 'product-archive', 'section', 'jet-woo-builder-archive'].includes($('main .elementor').data('elementor-type')) && $('body').hasClass('archive');
    this.$isSingel = ['single', 'product', 'product-single', 'section', 'jet-woo-builder'].includes($('main .elementor').data('elementor-type')) && $('body').hasClass('single');
    this.$isShop = $(document).find('.sellkit-is-default-shop');
    this.$wooWrapper = document;
    this.$preLoader = 'body';
    var wooWrapper = $(this.$wooWrapper).find('.elementor-shortcode, .raven-wc-products-wrapper').find('.woocommerce');

    if ((this.$isShop.length === 1 && !this.$isElementor || !this.$isArchive || !this.$isSingel) && wooWrapper.length === 0) {
      this.$wooWrapper = 'main.site-main';
      wooWrapper = $('main.site-main').find('ul.products');
    }

    if (wooWrapper.length > 1) {
      wooWrapper = wooWrapper.eq(0);
    }

    wooWrapper.parent().addClass('sellkit-active-wrapper');
    this.handleWooWrapper(wooWrapper);

    if (typeof window.elementor === 'undefined') {
      return;
    }

    window.elementor.channels.editor.on('change', function (controlView) {
      self.onElementChange(controlView.model.get('name'), controlView);
    });
  },
  onElementChange: function onElementChange(propertyName, controlView) {
    if ('reset_text' === propertyName) {
      var resetText = controlView.container.settings.get('reset_text');
      $('.sellkit-product-filter-clear').text(resetText);
    }

    if ('content_location' === propertyName) {
      $('.sellkit-product-selected-filter').remove();
    }

    if ('content_style' === propertyName) {
      $('.sellkit-product-selected-filter').remove();
    }
  },
  handleWooWrapper: function handleWooWrapper(wooWrapper) {
    var _this = this;

    if ($('.sellkit-product-selected-filter').length !== 0) {
      return;
    }

    if ('topOfFilters' === this.getElementSettings('content_location') || 'horizontal' === this.getElementSettings('content_style')) {
      wooWrapper = $('.sellkit-product-filter');
    }

    if (wooWrapper) {
      var elementId = 'elementor-element-' + $('.elementor-widget-sellkit-product-filter').data('id');

      if ('horizontal' === this.getElementSettings('content_style')) {
        wooWrapper.after("<div class=\"elementor-element ".concat(elementId, " sellkit-product-selected-filter\"><div class=\"sellkit-product-selected-filter-wrapper\"></div></div>"));
      } else {
        wooWrapper.before("<div class=\"elementor-element ".concat(elementId, " sellkit-product-selected-filter\"><div class=\"sellkit-product-selected-filter-wrapper\"></div></div>"));
      }

      $('.sellkit-product-selected-filter-wrapper').append('<a href="#" class="sellkit-product-filter-clear"></a>');
      $('.sellkit-product-filter-clear').text(this.getElementSettings('reset_text'));
      $('.sellkit-product-filter-clear').hide();

      if (!wooWrapper.parent().hasClass('.woocommerce')) {
        $('.sellkit-product-selected-filter').hide();
      }
    }

    $('.sellkit-product-filter-clear').on('click', function (event) {
      event.preventDefault();
      var self = _this;
      var filterWrapper = $(event.currentTarget).parents('.sellkit-product-selected-filter-wrapper');
      var filterWrapperItems = filterWrapper.find('.sellkit-product-selected-filter-item');

      _this.handleAjax($(event.currentTarget));

      filterWrapperItems.each(function (i, item) {
        var filterId = $(item).attr('class').replace('sellkit-product-selected-filter-item ', '');
        var filterItem = $('#' + filterId);

        if (filterItem.hasClass('product-filter-item-button')) {
          filterItem.parent().removeClass('active-button');
        }

        if (filterItem.hasClass('sellkit-product-filter-custom-range-form') || filterItem.hasClass('sellkit-product-filter-search-text')) {
          filterItem.find('input[type!="hidden"]').val('');
        }

        filterItem.removeClass('active-link active-image active-color');
        filterItem.prop('checked', false);
        self.detectActiveFilter(filterItem, true);
      });
      filterWrapperItems.remove();

      _this.elements.$productFilterContent.removeClass('sellkit-filter-has-data');

      $(event.currentTarget).hide();
    });
  },
  bindEvents: function bindEvents() {
    var selectors = this.getSettings('selectors');
    this.handleQueryOnLoad();
    this.handleCheckbox();
    this.handleSelect();
    this.handleRadio();
    this.handleButton();
    this.handleToggle();
    this.handlePriceRange();
    this.handleSearchText();
    this.handleVerticalToggle();
    this.handleCategoryToggle();
    this.handleLinks(selectors);
    this.handleImage(selectors);
    this.handleColor(selectors);
    this.controlSelector();
  },
  handleCategoryToggle: function handleCategoryToggle() {
    this.elements.$productFilterCategoryToggle.on('click', function (event) {
      var filters = $(event.currentTarget).parent().find('.product-filter-item');
      $(event.currentTarget).toggleClass('sellkit-category-toggle');

      if (!$(event.currentTarget).hasClass('sub-item') && $(event.currentTarget).data('term')) {
        filters.each(function (i, filter) {
          if ($(filter).hasClass('sub-item') && $(event.currentTarget).data('term') === $(filter).data('parent')) {
            $(filter).slideToggle(200);
          }
        });
      }
    });
  },
  handleVerticalToggle: function handleVerticalToggle() {
    this.elements.$productFilterVerticalToggle.on('click', function (event) {
      event.preventDefault();
      $(event.currentTarget).toggleClass('sellkit-toggle-expanded sellkit-toggle-collapsed');
      $(event.currentTarget).next('.sellkit-product-filter-layout-vertical ' + ',.sellkit-product-filter-layout-horizontal ' + ',.sellkit-product-filter-layout-default ' + ',.sellkit-product-filter-price-wrapper ' + ',.sellkit-product-filter-search-text-wrapper ' + ',.sellkit-product-filter-on-sale-wrapper ' + ',.sellkit-product-filter-dropdown ' + ',div[class*="sellkit-product-filter-layout-columns-"]' + ',.sellkit-product-filter-links').slideToggle(200);
    });
  },
  handleRadio: function handleRadio() {
    var _this2 = this;

    this.elements.$productFilterRadio.on('change', function (event) {
      _this2.handleAjax($(event.currentTarget));

      if (!$(event.currentTarget).is(':checked')) {
        _this2.removeSelectedFilters($(event.currentTarget), 'radio');

        _this2.detectActiveFilter($(event.currentTarget), true);

        return;
      }

      _this2.addSelectedFilters($(event.currentTarget), 'radio');

      _this2.detectActiveFilter($(event.currentTarget), false);
    });
  },
  handleCheckbox: function handleCheckbox() {
    var _this3 = this;

    this.elements.$productFilterCheckbox.on('change', function (event) {
      _this3.handleAjax($(event.currentTarget));

      if (!$(event.currentTarget).is(':checked')) {
        _this3.removeSelectedFilters($(event.currentTarget), 'checkbox');

        _this3.detectActiveFilter($(event.currentTarget), true);

        return;
      }

      _this3.addSelectedFilters($(event.currentTarget), 'checkbox');

      _this3.detectActiveFilter($(event.currentTarget), false);
    });
  },
  handleLinks: function handleLinks(selectors) {
    var _this4 = this;

    this.elements.$productFilterLinks.on('click', function (event) {
      event.preventDefault();
      $(event.currentTarget).removeClass('active-link-load');

      _this4.handleAjax($(event.currentTarget));

      var allTags = $(event.currentTarget).parents(selectors.ProductFilterContent).find(selectors.ProductFilterLinks);

      if ($(event.currentTarget).hasClass('active-link')) {
        _this4.removeSelectedFilters($(event.currentTarget), 'links');

        allTags.removeClass('active-link active-link-load');

        _this4.detectActiveFilter($(event.currentTarget), true);

        return;
      }

      allTags.removeClass('active-link active-link-load');
      $(event.currentTarget).addClass('active-link');

      _this4.addSelectedFilters($(event.currentTarget), 'links');

      _this4.detectActiveFilter($(event.currentTarget), false);
    });
  },
  handleSelect: function handleSelect() {
    var _this5 = this;

    this.elements.$productFilterSelect.on('change', function (event) {
      _this5.handleAjax($(event.currentTarget));

      if ('all' === $(event.currentTarget).find(':selected').data('products')) {
        _this5.removeSelectedFilters($(event.currentTarget), 'select');

        _this5.detectActiveFilter($(event.currentTarget), true);

        return;
      }

      _this5.addSelectedFilters($(event.currentTarget), 'select');

      _this5.detectActiveFilter($(event.currentTarget), false);
    });
  },
  handleButton: function handleButton() {
    var _this6 = this;

    this.elements.$productFilterButton.on('click', function (event) {
      var button = $(event.currentTarget);

      _this6.handleAjax(button);

      button.parent().toggleClass('active-button');

      if (!button.parent().hasClass('active-button')) {
        _this6.removeSelectedFilters(button, 'button');

        button.parent().removeClass('active-button');

        _this6.detectActiveFilter($(event.currentTarget), true);

        return;
      }

      _this6.addSelectedFilters(button, 'button');

      _this6.detectActiveFilter($(event.currentTarget), false);
    });
  },
  handleImage: function handleImage(selectors) {
    var _this7 = this;

    this.elements.$productFilterImage.on('click', function (event) {
      var allTags = $(event.currentTarget).parents(selectors.ProductFilterContent).find(selectors.ProductFilterImage);
      $(event.currentTarget).removeClass('active-image-load');

      _this7.handleAjax($(event.currentTarget));

      if ($(event.currentTarget).hasClass('active-image')) {
        _this7.removeSelectedFilters($(event.currentTarget), 'image');

        allTags.removeClass('active-image active-image-load');

        _this7.detectActiveFilter($(event.currentTarget), true);

        return;
      }

      allTags.removeClass('active-image active-image-load');
      $(event.currentTarget).addClass('active-image');

      _this7.addSelectedFilters($(event.currentTarget), 'image');

      _this7.detectActiveFilter($(event.currentTarget), false);
    });
  },
  handleColor: function handleColor(selectors) {
    var _this8 = this;

    this.elements.$productFilterColor.on('click', function (event) {
      var allTags = $(event.currentTarget).parents(selectors.ProductFilterContent).find(selectors.ProductFilterColor);
      $(event.currentTarget).removeClass('active-color-load');

      _this8.handleAjax($(event.currentTarget));

      if ($(event.currentTarget).hasClass('active-color')) {
        _this8.removeSelectedFilters($(event.currentTarget), 'color');

        allTags.removeClass('active-color active-color-load');

        _this8.detectActiveFilter($(event.currentTarget), true);

        return;
      }

      allTags.removeClass('active-color active-color-load');
      $(event.currentTarget).addClass('active-color');

      _this8.addSelectedFilters($(event.currentTarget), 'color');

      _this8.detectActiveFilter($(event.currentTarget), false);
    });
  },
  handleToggle: function handleToggle() {
    var _this9 = this;

    this.elements.$productFilterToggle.on('click', function (event) {
      _this9.handleAjax($(event.currentTarget));

      if (!$(event.currentTarget).is(':checked')) {
        _this9.removeSelectedFilters($(event.currentTarget), 'toggle');

        _this9.detectActiveFilter($(event.currentTarget), true);

        return;
      }

      _this9.addSelectedFilters($(event.currentTarget), 'toggle');

      _this9.detectActiveFilter($(event.currentTarget), false);
    });
  },
  handlePriceRange: function handlePriceRange() {
    var _this10 = this;

    this.elements.$productFilterRange.on('submit', function (event) {
      event.preventDefault();

      if (event.isTrigger) {
        $(event.currentTarget).removeData('value');
        $(event.currentTarget).removeData('products');
        $(event.currentTarget).removeAttr('data-value');
        $(event.currentTarget).removeAttr('data-products');
        $(event.currentTarget).find('input[type="number"]').val('');

        _this10.handleAjax($(event.currentTarget));

        _this10.removeSelectedFilters($(event.currentTarget), 'range');

        _this10.detectActiveFilter($(event.currentTarget), true);

        return;
      }

      var filter = $(event.currentTarget).data('filter');
      var Prices = $(event.currentTarget).serializeArray();
      var dataProducts = {};
      dataProducts[filter] = [];
      var priceData = Prices[0].value + '-' + Prices[1].value;
      dataProducts[filter].push(priceData, priceData);
      $(event.currentTarget).data('products', dataProducts[filter]);
      $(event.currentTarget).data('value', priceData);

      _this10.handleAjax($(event.currentTarget));

      _this10.addSelectedFilters($(event.currentTarget), 'range');

      _this10.detectActiveFilter($(event.currentTarget), false);
    });
  },
  handleSearchText: function handleSearchText() {
    var _this11 = this;

    this.elements.$productFilterSearch.on('submit', function (event) {
      event.preventDefault();

      if (event.isTrigger) {
        $(event.currentTarget).removeData('products');
        $(event.currentTarget).find('input[type="text"]').val('');

        _this11.removeSelectedFilters($(event.currentTarget), 'search-text');

        _this11.handleAjax($(event.currentTarget));

        _this11.detectActiveFilter($(event.currentTarget), true);

        return;
      }

      var searchData = $(event.currentTarget).serializeArray();
      $(searchData).each(function (i, field) {
        if (field.name === 'search_name') {
          $(event.currentTarget).data('products', field.value);
        }

        $(event.currentTarget).data(field.name, field.value);
      });
      var filterLabel = 'Search For: ' + $(event.currentTarget).find('input[type="text"]').val();
      $(event.currentTarget).data('value', filterLabel);

      _this11.handleAjax($(event.currentTarget));

      _this11.addSelectedFilters($(event.currentTarget), 'search-text');

      _this11.detectActiveFilter($(event.currentTarget), false);
    });
  },
  controlSelector: function controlSelector() {
    var _this12 = this;

    this.elements.$productFilterSelector.on('click', function (event) {
      var container = _this12.elements.$productFilterSelector.parents('.sellkit-product-filter-content');

      var element = _this12.elements.$productFilterSelector.parents('.elementor-widget-sellkit-product-filter');

      var content = _this12.elements.$productFilterSelector.parents('.sellkit-product-filter-form-horizontal');

      var wrapper = $(event.currentTarget).next('.sellkit-product-filter-item-wrapper');
      container.find('.sellkit-product-filter-item-wrapper').hide(200);

      if (element.parents('aside').length !== 0) {
        element.css('height', content.outerHeight());
        var top = $(event.currentTarget).offset().top;

        if (element.outerWidth() < wrapper.outerWidth()) {
          wrapper.css('width', element.outerWidth());
        }

        if (top - element.offset().top + wrapper.outerHeight() > element.outerHeight()) {
          element.css('height', wrapper.outerHeight() + element.outerHeight());
        }
      }

      if (wrapper.css('display') === 'none') {
        wrapper.show(200);
      }
    });
    $(document).on('mouseup', function (event) {
      var container = _this12.elements.$productFilterSelector.parents('.sellkit-product-filter-content');

      if (!container.is(event.target) && container.has(event.target).length === 0) {
        container.find('.sellkit-product-filter-item-wrapper').hide(200);
      }
    });
  },
  addSelectedFilters: function addSelectedFilters(filter, type) {
    var multiSelects = ['button', 'checkbox'];
    var withoutValue = ['links', 'image', 'color', 'range', 'search-text'];
    var filterId = filter.attr('id');
    var filtervalue = filter.val();
    var dataFilter = filter.data('filter');

    if (withoutValue.includes(type)) {
      filtervalue = filter.data('value');
    }

    if ('select' === type) {
      var option = filter.find(':selected');
      filterId = option.attr('id');
      dataFilter = option.data('filter');
    }

    var wooWrapper = $(this.$wooWrapper).find('.sellkit-product-selected-filter-wrapper');
    var selectedFilter = '<span class="sellkit-product-selected-filter-item ' + filterId + '" data-filter="' + dataFilter + '">' + filtervalue + '</span>';

    if (!multiSelects.includes(type)) {
      wooWrapper.find("[data-filter='".concat(dataFilter, "']")).remove();
      $(wooWrapper).prepend(selectedFilter);
      this.controlSelectedFilters(filter);
      this.controlClearAll(wooWrapper);
      return;
    }

    $(wooWrapper).prepend(selectedFilter);
    this.controlClearAll(wooWrapper);
    this.controlSelectedFilters(filter);
  },
  removeSelectedFilters: function removeSelectedFilters(filter, type) {
    var filterId = filter.attr('id');
    var dataFilter = filter.data('filter');
    var multiSelects = ['button', 'checkbox', 'color'];

    if ('select' === type) {
      var option = filter.find(':selected');
      filterId = option.attr('id');
      dataFilter = option.data('filter');
    }

    var wooWrapper = $(this.$wooWrapper).find('.sellkit-product-selected-filter');
    var addedFilter = wooWrapper.find('.' + filterId + "[data-filter='".concat(dataFilter, "']"));

    if (!multiSelects.includes(type)) {
      wooWrapper.find("[data-filter='".concat(dataFilter, "']")).remove();
      this.controlClearAll(wooWrapper);
      return;
    }

    if (addedFilter.length !== 0) {
      addedFilter.remove();
    }

    this.controlClearAll(wooWrapper);
  },
  controlSelectedFilters: function controlSelectedFilters(filter) {
    $(this.$wooWrapper).find('.sellkit-product-selected-filter-item').on('click', function (event) {
      $(event.currentTarget).remove();
      var filterId = $(event.currentTarget).attr('class').replace('sellkit-product-selected-filter-item ', '');
      var filterData = $(event.currentTarget).data('filter');
      var filterItemId = filter.attr('id');
      var filterItemData = filter.data('filter');

      if (filter.hasClass('product-filter-item-select')) {
        filterItemId = filter.find(':selected').attr('id');
        filterItemData = filter.find(':selected').data('filter');
      }

      if (filterItemId === filterId && filterItemData === filterData) {
        $('#' + filterId).parents('.product-filter-item-select').val('all');
        $('#' + filterId).trigger('click');
        $('#' + filterId).trigger('submit');
        $('#' + filterId).prop('checked', false).trigger('change');
      }
    });
  },
  controlClearAll: function controlClearAll(wooWrapper) {
    if (wooWrapper.find('.sellkit-product-selected-filter-item').length > 0) {
      $('.sellkit-product-filter-clear').show();
      return;
    }

    $('.sellkit-product-filter-clear').hide();
  },
  controlSwatches: function controlSwatches() {
    $('.artbees-was-swatch').on('click', function (event) {
      if (typeof $(event.currentTarget).data('catalog') === 'undefined' && $(event.currentTarget).data('catalog') !== 'image' || $(event.currentTarget).data('catalog') === 'link') {
        return;
      }

      event.preventDefault();
      event.stopPropagation();
      var swatch = $(this),
          href = swatch.attr('href'),
          parent = swatch.parents('.product'),
          wcImageWrapper = parent.find('.jupiterx-wc-loop-product-image'),
          wcImage = wcImageWrapper.find('img'),
          selectedAttribute = swatch.find('.artbees-was-content'),
          otherAttribute = parent.find('.artbees-was-content');

      if (href.length === 0) {
        return;
      }

      wcImage.attr('src', href);
      otherAttribute.removeClass('selected-attribute');
      selectedAttribute.addClass('selected-attribute');
    });
  },
  handleLogic: function handleLogic(parameter) {
    if (parameter.hasClass('sellkit-product-filter-clear')) {
      this.elements.$ajaxResult = {};
      return this.elements.$ajaxResult;
    }

    var logic = parameter.data('logic');
    var type = parameter.data('type');
    var filter = parameter.data('filter');
    var data = parameter.data('products');

    if (parameter.hasClass('sellkit-product-filter-search-text')) {
      data = this.searchPrameters(parameter);
      this.elements.$ajaxResult[filter] = [];
    }

    if (parameter.hasClass('sellkit-product-filter-custom-range-form')) {
      this.elements.$ajaxResult[filter] = [];
    }

    if (parameter.hasClass('product-filter-item-select')) {
      filter = parameter.find(':selected').data('filter');
      data = parameter.find(':selected').data('products');
      type = parameter.find(':selected').data('type');

      if ('all' === data) {
        this.elements.$ajaxResult[filter] = [];
        return this.elements.$ajaxResult;
      }
    }

    if (!parameter.is(':checked') && (parameter.hasClass('product-filter-item-checkbox') || parameter.hasClass('product-filter-item-button'))) {
      for (var i in Object.keys(this.elements.$ajaxResult[filter])) {
        if (JSON.stringify(this.elements.$ajaxResult[filter][i]) === JSON.stringify(Object.values(data))) {
          this.elements.$ajaxResult[filter][i] = null;
          this.elements.$ajaxResult[filter] = this.elements.$ajaxResult[filter].filter(function (element) {
            return element;
          });
        }
      }

      return this.elements.$ajaxResult;
    }

    if (!this.elements.$ajaxResult[filter]) {
      this.elements.$ajaxResult[filter] = [];
    }

    if (!this.elements.$ajaxResult[filter].includes(logic)) {
      this.elements.$ajaxResult[filter].push(logic);
    }

    if (!this.elements.$ajaxResult[filter].includes(type)) {
      this.elements.$ajaxResult[filter].push(type);
    }

    if (parameter.hasClass('product-filter-item-checkbox') || parameter.hasClass('product-filter-item-button')) {
      this.elements.$ajaxResult[filter].push(Object.values(data));
      return this.elements.$ajaxResult;
    }

    if (!this.elements.$ajaxResult[filter][0]) {
      this.elements.$ajaxResult[filter] = [0];
    }

    if (!this.elements.$ajaxResult[filter].includes(type)) {
      this.elements.$ajaxResult[filter].push(type);
    }

    if (parameter.hasClass('product-filter-item-links') && parameter.hasClass('active-link') && !parameter.hasClass('active-link-load') || !parameter.is(':checked') && parameter.hasClass('product-filter-item-toggle') || !parameter.is(':checked') && parameter.hasClass('product-filter-item-radio') || parameter.hasClass('active-color') && !parameter.hasClass('active-color-load') && parameter.hasClass('product-filter-item-color') || parameter.hasClass('active-image') && !parameter.hasClass('active-image-load') && parameter.hasClass('product-filter-item-image')) {
      this.elements.$ajaxResult[filter] = [];
      return this.elements.$ajaxResult;
    }

    if (data) {
      this.elements.$ajaxResult[filter].push(Object.values(data));
    }

    return this.elements.$ajaxResult;
  },
  searchPrameters: function searchPrameters(parameter) {
    var searchData = {},
        searchParameters = [];

    if ('undefined' === typeof parameter.data('products')) {
      return;
    }

    searchData[parameter.data('filter')] = [];

    if (parameter.data('product-filter-title')) {
      searchParameters.push(this.getSearchData('product-filter-title'));
    }

    if (parameter.data('product-filter-content')) {
      searchParameters.push(this.getSearchData('product-filter-content'));
    }

    if (parameter.data('product-filter-categories')) {
      searchParameters.push(this.getSearchData('product-filter-categories'));
    }

    if (parameter.data('product-filter-tags')) {
      searchParameters.push(this.getSearchData('product-filter-tags'));
    }

    if (parameter.data('product-filter-attributes')) {
      searchParameters.push(this.getSearchData('product-filter-attributes'));
    }

    searchData[parameter.data('filter')].push(parameter.data('products'), JSON.stringify(searchParameters));
    return searchData;
  },
  getSearchData: function getSearchData(key) {
    var parameters = {
      'product-filter-title': 'title',
      'product-filter-content': 'content',
      'product-filter-categories': 'categories',
      'product-filter-tags': 'tags',
      'product-filter-attributes': 'attributes'
    };
    return parameters[key];
  },
  CompatibleWithInfiniteLoad: function CompatibleWithInfiniteLoad() {
    var _this13 = this;

    var jxProducts = $('.elementor-widget-raven-wc-products');

    if (jxProducts.length === 0) {
      return;
    }

    var jxProductsWrapper = jxProducts.find('.raven-wc-products-wrapper'),
        paginationType = jxProducts.data('settings').pagination_type || '';

    if ($('.raven-wc-products-wrapper').length !== 0 && $('.raven-wc-products-wrapper').find('.jupiterx-wc-loadmore-wrapper, .woocommerce-pagination').length === 0 && 'infinite_load' === paginationType) {
      elementorFrontend.waypoint(jxProducts, function () {
        var value = "jx-load-more-".concat(++_this13.elements.$loadMoreClickCount);

        if (_this13.elements.$loadMoreClickCount > jxProductsWrapper.data('settings').total_pages - 1) {
          return;
        }

        _this13.handlePagination(_this13.elements.$ajaxResult, value, _this13.elements.$wcSortValue);
      }, {
        offset: 'bottom-in-view',
        triggerOnce: true
      });
    }
  },
  controlWcPgaination: function controlWcPgaination() {
    var _this14 = this;

    this.CompatibleWithInfiniteLoad();

    if ($('.elementor-shortcode, .raven-wc-products-wrapper').find('.woocommerce-pagination, .jupiterx-wc-loadmore-wrapper').length === 0 && this.$isShop.length !== 1 && $(this.$wooWrapper).find('ul.products').length === 0) {
      return;
    }

    var wrapperClasses = '.elementor-shortcode, .raven-wc-products-wrapper';

    if ((this.$isShop.length === 1 && !this.$isElementor || !this.$isArchive || !this.$isSingel) && $('body').find(wrapperClasses).length === 0) {
      wrapperClasses = "".concat(wrapperClasses, ", ").concat(this.$wooWrapper);
    }

    var paginationLinks = $("".concat(wrapperClasses)).find('a.page-numbers, .jupiterx-wc-load-more, .raven-load-more');
    paginationLinks.each(function (i, link) {
      if (paginationLinks.hasClass('jupiterx-wc-load-more')) {
        var jxProducts = $(link).parents('.elementor-widget-raven-wc-products');

        if (jxProducts.length === 0) {
          return;
        }

        $('.jupiterx-wc-loadmore-wrapper').addClass('raven-load-more');
        $('.jupiterx-wc-load-more').addClass('raven-load-more-button');
        $('.jupiterx-wc-load-more').removeClass('button btn-info');
        $('.jupiterx-wc-load-more').attr('href', '#');
        $('.jupiterx-wc-load-more').text('');
        var settings = jxProducts.data('settings');
        var text = settings.classic_load_more_text || settings.load_more_text;
        $('.jupiterx-wc-load-more').append("<span>".concat(text, "</span>"));
        return;
      }

      var pageNumber = $(link).attr('href').split('?product-page=').pop();
      $(link).attr('href', pageNumber);
    });
    paginationLinks.on('click', function (event) {
      event.preventDefault();
      var value = $(event.currentTarget).attr('href');

      if (!$(event.currentTarget).hasClass('jupiterx-wc-load-more')) {
        _this14.handlePaginationAndSortSearchQuery(value, 'page');
      }

      if ($(event.currentTarget).hasClass('jupiterx-wc-load-more')) {
        value = "jx-load-more-".concat(++_this14.elements.$loadMoreClickCount);
      }

      _this14.handlePagination(_this14.elements.$ajaxResult, value, _this14.elements.$wcSortValue);
    });
  },
  controlWcSort: function controlWcSort() {
    var _this15 = this;

    if (this.elements.$wcSortField === null || this.elements.$wcSortField.length === 0) {
      return;
    }

    this.elements.$wcSortField.find('.orderby').on('change', function (event) {
      event.preventDefault();
      _this15.elements.$wcSortValue = $(event.currentTarget).val();

      _this15.handlePaginationAndSortSearchQuery(_this15.elements.$wcSortValue, 'order');

      _this15.handleSort(_this15.elements.$ajaxResult, $(event.currentTarget).val());
    });
  },
  checkWcSortSelected: function checkWcSortSelected(orderby) {
    if (!orderby) {
      return;
    }

    this.elements.$wcSortField.find('.orderby').val(orderby);
  },
  handlePaginationAndSortSearchQuery: function handlePaginationAndSortSearchQuery(data, type) {
    var href = window.location.href;
    var param = "&".concat(type, "=").concat(data);
    var url = new URL(window.location.href);

    if (url.searchParams.get(type)) {
      url.searchParams["delete"](type);
    }

    href = url + param;
    window.history.pushState({
      path: href
    }, '', href);
  },
  getJxProductSettings: function getJxProductSettings() {
    if (document.body.classList.contains('elementor-editor-active') || $(this.$wooWrapper).find('.raven-wc-products-wrapper').length === 0) {
      return;
    }

    var productWrapper = $(this.$wooWrapper).find('.raven-wc-products-wrapper').parents('.elementor-widget-raven-wc-products');

    if (productWrapper.length !== 0) {
      return this.productsSettings(productWrapper);
    }
  },
  productsSettings: function productsSettings(wrapper) {
    var productSettings = wrapper.data('settings'),
        productWrapperSettings = $(this.$wooWrapper).find('.raven-wc-products-wrapper').data('settings'),
        productsColumn = productSettings.columns || productSettings.columns_custom,
        productsRow = productSettings.rows,
        productsPagination = productSettings.show_pagination,
        productsPaginationType = productSettings.pagination_type,
        productsAllProduct = productSettings.show_all_products,
        productLayout = productWrapperSettings.layout,
        productSwapEffect = productSettings.swap_effect,
        productsImageSize = productWrapperSettings.image_size,
        productsAtcButton = productSettings.pc_atc_button_location,
        productsAtcButtonIcon = productSettings.pc_atc_button_icon,
        productsWishlist = productSettings.wishlist,
        productsData = [];
    productsData.push(productsColumn, productsRow, productsPagination, productsPaginationType, productsAllProduct, productLayout, productSwapEffect, productsImageSize, productsAtcButton, productsAtcButtonIcon, productsWishlist);
    return productsData;
  },
  handleSort: function handleSort(parameter, orderby) {
    var _this16 = this;

    wp.ajax.send('sellkit_get_products', {
      type: 'POST',
      url: _wpUtilSettings.ajax.url,
      data: {
        filter: JSON.stringify(parameter),
        nonce: window.sellkit_elementor.nonce,
        jxProductsSettings: JSON.stringify(this.getJxProductSettings()),
        orderby: orderby
      },
      beforeSend: function beforeSend() {
        $(_this16.$preLoader).addClass('sellkit-preloader');
      },
      complete: function complete() {
        $(_this16.$preLoader).delay(200).removeClass('sellkit-preloader');
      },
      success: function success(data) {
        _this16.elements.$loadMoreClickCount = 0;

        _this16.showProducts(data);

        _this16.checkWcSortSelected(orderby);
      }
    });
  },
  handlePagination: function handlePagination(parameter, pagination, orderby) {
    var _this17 = this;

    wp.ajax.send('sellkit_get_products', {
      type: 'POST',
      url: _wpUtilSettings.ajax.url,
      data: {
        filter: JSON.stringify(parameter),
        nonce: window.sellkit_elementor.nonce,
        jxProductsSettings: JSON.stringify(this.getJxProductSettings()),
        pagination: pagination,
        orderby: orderby
      },
      beforeSend: function beforeSend() {
        $(_this17.$preLoader).addClass('sellkit-preloader');
      },
      complete: function complete() {
        $(_this17.$preLoader).delay(200).removeClass('sellkit-preloader');
      },
      success: function success(data) {
        _this17.showProducts(data);
      }
    });
  },
  controlJxWcWidgetEffects: function controlJxWcWidgetEffects(wrapper) {
    var settings = this.getJxProductSettings();

    if (_.isEmpty(settings)) {
      return;
    }

    if ('zoom_hover' === settings[6]) {
      wrapper.find('.jupiterx-wc-loop-product-image').zoom();
    }

    var controlNav = false;
    var directionNav = false;

    if ('gallery_arrows' === settings[6]) {
      directionNav = true;
    }

    if ('gallery_pagination' === settings[6]) {
      controlNav = true;
    }

    if ('gallery_pagination' === settings[6] || 'gallery_arrows' === settings[6]) {
      wrapper.find('.jupiterx-wc-loop-product-image').flexslider({
        selector: '.raven-swap-effect-gallery-slides > li',
        animation: 'slide',
        slideshow: false,
        controlNav: controlNav,
        directionNav: directionNav,
        prevText: '<svg version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 7.2 12" style="enable-background:new 0 0 7.2 12;" xml:space="preserve"><path class="st0" d="M2.4,6l4.5-4.3c0.4-0.4,0.4-1,0-1.4c-0.4-0.4-1-0.4-1.4,0l-5.2,5C0.1,5.5,0,5.7,0,6s0.1,0.5,0.3,0.7l5.2,5	C5.7,11.9,6,12,6.2,12c0.3,0,0.5-0.1,0.7-0.3c0.4-0.4,0.4-1,0-1.4L2.4,6z"></path></svg>',
        nextText: '<svg version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 7.2 12" style="enable-background:new 0 0 7.2 12;" xml:space="preserve"><path class="st0" d="M4.8,6l-4.5,4.3c-0.4,0.4-0.4,1,0,1.4c0.4,0.4,1,0.4,1.4,0l5.2-5C7.1,6.5,7.2,6.3,7.2,6S7.1,5.5,6.9,5.3l-5.2-5C1.5,0.1,1.2,0,1,0C0.7,0,0.5,0.1,0.3,0.3c-0.4,0.4-0.4,1,0,1.4L4.8,6z"></path></svg>',
        init: function init() {
          wrapper.addClass('raven-swap-effect-gallery-loaded');
        }
      });
    }
  },
  handleAjax: function handleAjax(parameter) {
    var _this18 = this;

    var dataWithLogic = this.handleLogic(parameter);
    var href = new URL(window.location.href);
    wp.ajax.send('sellkit_get_products', {
      type: 'POST',
      url: _wpUtilSettings.ajax.url,
      data: {
        filter: JSON.stringify(dataWithLogic),
        nonce: window.sellkit_elementor.nonce,
        jxProductsSettings: JSON.stringify(this.getJxProductSettings()),
        orderby: href.searchParams.get('orderby')
      },
      beforeSend: function beforeSend() {
        $(_this18.$preLoader).addClass('sellkit-preloader');
      },
      complete: function complete() {
        $(_this18.$preLoader).delay(200).removeClass('sellkit-preloader');
        $('.sellkit-product-selected-filter').show();
      },
      success: function success(data) {
        _this18.elements.$loadMoreClickCount = 0;
        _this18.elements.$wcSortValue = null;

        if (document.body.classList.contains('elementor-editor-active')) {
          return;
        }

        _this18.showProducts(data);

        _this18.handleQueryString(dataWithLogic);
      }
    });
  },
  showProducts: function showProducts(data) {
    var widgetElement = $(document).find('.elementor-shortcode, .raven-wc-products-wrapper').find('.woocommerce'),
        bodyClass = 'body';

    if (widgetElement.length > 1) {
      widgetElement = widgetElement.eq(0);
    }

    if (this.$wooWrapper === 'main.site-main') {
      bodyClass = '';
    }

    if (this.$isShop.length !== 1 && this.$isElementor || this.$isArchive || this.$isSingel) {
      widgetElement.delay(200).replaceWith(data);
      this.elements.$wcSortField = $('.elementor-shortcode, .raven-wc-products-wrapper').find('.woocommerce-ordering');
    }

    if (this.$isShop.length === 1 && $(this.$wooWrapper).find('.woocommerce').length === 0) {
      $(this.$wooWrapper).find('.woocommerce-pagination, .woocommerce-ordering, .woocommerce-result-count, .woocommerce-notices-wrapper, .jupiterx-wc-loadmore-wrapper').remove();
      $(this.$wooWrapper).find('ul.products').delay(200).replaceWith(data);
      this.elements.$wcSortField = $(this.$wooWrapper).find('.woocommerce-ordering');
    }

    if (this.$isShop.length === 1 && $(this.$wooWrapper).find('.woocommerce').length !== 0) {
      $(this.$wooWrapper).find("".concat(bodyClass, " .woocommerce")).delay(200).replaceWith(data);
      this.elements.$wcSortField = $(this.$wooWrapper).find('.woocommerce-ordering');
    }

    this.controlSwatches();
    this.controlWcPgaination();
    this.controlWcSort();
    this.controlJxWcWidgetEffects($(this.$wooWrapper));
  },
  handleQueryString: function handleQueryString(data) {
    var _this19 = this;

    var currentUrl = window.location.protocol + '//' + window.location.host + window.location.pathname + '?sellkit_filters=1';
    var href = new URL(window.location.href);
    var url = window.location.href;
    var page = url.split('/page/')[1];
    var orderby = href.searchParams.get('orderby');

    if (page) {
      currentUrl = url.split('/page/')[0] + '/?sellkit_filters=1';
    }

    if (!data) {
      window.history.pushState({
        path: currentUrl
      }, '', currentUrl);
      return;
    }

    Object.entries(data).forEach(function (_ref) {
      var _ref2 = (0, _slicedToArray2["default"])(_ref, 2),
          key = _ref2[0],
          value = _ref2[1];

      if (value[1] && value[2]) {
        var param = "&".concat(_this19.handleQueryStringTerms(value[1], key), "=").concat(_this19.handleQueryStringValues(value[1], value[2]));

        if (value.length > 3) {
          var termValues = [];
          Object.entries(value).forEach(function (_ref3) {
            var _ref4 = (0, _slicedToArray2["default"])(_ref3, 2),
                itemKey = _ref4[0],
                // eslint-disable-line
            item = _ref4[1];

            if (Array.isArray(item)) {
              termValues.push(_this19.handleQueryStringValues(value[1], item));
            }
          });
          param = "&".concat(_this19.handleQueryStringTerms(value[1], key), "=").concat(termValues.join('+'));
        }

        currentUrl = currentUrl + param;
      }
    });

    if (page) {
      currentUrl = currentUrl + "&page=".concat(page.match(/^\d*/)[0]);
    }

    if (orderby) {
      this.elements.$wcSortField.find('.orderby').val(orderby);
      currentUrl = currentUrl + "&order=".concat(orderby);
    }

    window.history.pushState({
      path: currentUrl
    }, '', currentUrl);
  },
  handleQueryStringTerms: function handleQueryStringTerms(type, key) {
    if ('category' === type) {
      return 'products_cat';
    }

    if ('tag' === type) {
      return 'products_tag';
    }

    if (key.startsWith('custom-range-')) {
      type = 'custom_range';
    }

    return type;
  },
  handleQueryStringValues: function handleQueryStringValues(type, value) {
    if ('search_text' === type) {
      return value[0][0];
    }

    return value[1];
  },
  handleQueryOnLoad: function handleQueryOnLoad() {
    var url = new URL(window.location.href);

    if ('1' !== url.searchParams.get('sellkit_filters')) {
      return;
    }

    var params = new URLSearchParams(window.location.search);

    if (Object.keys(Object.fromEntries(params.entries())).length < 2) {
      return;
    }

    var self = this;
    this.elements.$productFilterItems.each(function (i, item) {
      var checkbox = $(item).find('.product-filter-item-checkbox'),
          button = $(item).find('.product-filter-item-button'),
          radio = $(item).find('.product-filter-item-radio'),
          onSale = $(item).find('.product-filter-item-toggle'),
          select = $(item).find('.product-filter-item-select'),
          links = $(item).find('.active-link-load'),
          image = $(item).find('.active-image-load'),
          color = $(item).find('.active-color-load'),
          searchText = $(item).find('.sellkit-product-filter-search-text'),
          priceRange = $(item).find('.sellkit-product-filter-custom-range-form');

      if (checkbox.length !== 0 && checkbox.is(':checked')) {
        self.handleLogic(checkbox);
        self.setTimeoutFilters(self, checkbox, 'checkbox');
        self.detectActiveFilter(checkbox, false);
      }

      if (button.length !== 0 && button.is(':checked')) {
        self.handleLogic(button);
        self.setTimeoutFilters(self, button, 'button');
        self.detectActiveFilter(button, false);
      }

      if (onSale.length !== 0 && onSale.is(':checked')) {
        self.handleLogic(onSale);
        self.setTimeoutFilters(self, onSale, 'toggle');
        self.detectActiveFilter(onSale, false);
      }

      if (radio.length !== 0 && radio.is(':checked')) {
        self.handleLogic(radio);
        self.setTimeoutFilters(self, radio, 'radio');
        self.detectActiveFilter(radio, false);
      }

      if (links.length !== 0 && links.hasClass('active-link-load')) {
        self.handleLogic(links);
        self.setTimeoutFilters(self, links, 'links');
        self.detectActiveFilter(links, false);
      }

      if (image.length !== 0 && image.hasClass('active-image-load')) {
        self.handleLogic(image);
        self.setTimeoutFilters(self, image, 'image');
        self.detectActiveFilter(image, false);
      }

      if (color.length !== 0 && color.hasClass('active-color-load')) {
        self.handleLogic(color);
        self.setTimeoutFilters(self, color, 'color');
        self.detectActiveFilter(color, false);
      }

      if (select.length !== 0 && 'all' !== select.val()) {
        self.handleLogic(select);
        self.setTimeoutFilters(self, select, 'select');
        self.detectActiveFilter(select, false);
      }

      if (searchText.length !== 0 && searchText.find('input[type="text"]').val()) {
        var searchData = searchText.serializeArray();
        $(searchData).each(function (index, field) {
          if (field.name === 'search_name') {
            searchText.data('products', field.value);
          }

          searchText.data(field.name, field.value);
        });
        var filterLabel = 'Search For: ' + searchText.find('input[type="text"]').val();
        searchText.data('value', filterLabel);
        self.handleLogic(searchText);
        self.setTimeoutFilters(self, searchText, 'search-text');
        self.detectActiveFilter(searchText, false);
      }

      if (priceRange.length !== 0 && priceRange.find('input[type="number"]').val()) {
        var Prices = priceRange.serializeArray();
        var priceData = Prices[0].value + '-' + Prices[1].value;
        priceRange.data('value', priceData);
        self.handleLogic(priceRange);
        self.setTimeoutFilters(self, priceRange, 'range');
        self.detectActiveFilter(priceRange, false);
      }
    });

    if (this.elements.$ajaxResult.length === 0) {
      return;
    }

    setTimeout(function () {
      wp.ajax.send('sellkit_get_products', {
        type: 'POST',
        url: _wpUtilSettings.ajax.url,
        data: {
          filter: JSON.stringify(self.elements.$ajaxResult),
          nonce: window.sellkit_elementor.nonce,
          jxProductsSettings: JSON.stringify(self.getJxProductSettings()),
          pagination: url.searchParams.get('page'),
          orderby: url.searchParams.get('order')
        },
        beforeSend: function beforeSend() {
          $(self.$preLoader).addClass('sellkit-preloader');
        },
        complete: function complete() {
          $(self.$preLoader).delay(200).removeClass('sellkit-preloader');
          $('.sellkit-product-selected-filter').show();
        },
        success: function success(data) {
          self.showProducts(data);

          if (url.searchParams.get('order')) {
            $(document).find('.orderby').val(url.searchParams.get('order'));
          }
        }
      });
    }, 100);
  },
  setTimeoutFilters: function setTimeoutFilters(self, element, type) {
    setTimeout(function () {
      self.addSelectedFilters(element, type);
    }, 300);
  },
  detectActiveFilter: function detectActiveFilter(filter, data) {
    if (filter.hasClass('sellkit-product-filter-custom-range-form')) {
      if (data) {
        filter.parents('.sellkit-product-filter-content').removeClass('sellkit-price-range-has-data');
        return;
      }

      filter.parents('.sellkit-product-filter-content').addClass('sellkit-price-range-has-data');
      return;
    }

    var content = this.elements.$ajaxResult[filter.data('filter')];

    if ('undefined' !== typeof content && data && 'undefined' === typeof content[2]) {
      filter.parents('.sellkit-product-filter-content').removeClass('sellkit-filter-has-data');
      return;
    }

    filter.parents('.sellkit-product-filter-content').addClass('sellkit-filter-has-data');
  }
});

function _default($scope) {
  new ProductFilter({
    $element: $scope
  });
}

},{"@babel/runtime/helpers/interopRequireDefault":8,"@babel/runtime/helpers/slicedToArray":11}],5:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = _default;
var ProductImages = elementorModules.frontend.handlers.Base.extend({
  onInit: function onInit() {
    elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);

    if (document.body.classList.contains('elementor-editor-active')) {
      this.$element.find('.woocommerce-product-gallery').wc_product_gallery();
    }

    var self = this;

    if (typeof window.elementor === 'undefined') {
      return;
    }

    window.elementor.channels.editor.on('change', function (controlView) {
      self.onElementChange(controlView.model.get('name'), controlView);
    });
    this.handleThumbnailBorderRadius(this.getElementSettings('thumbnail_border_radius'));
  },
  onElementChange: function onElementChange(propertyName, controlView) {
    if ('thumbnail_border_radius' === propertyName) {
      var borderRadius = controlView.container.settings.get('thumbnail_border_radius');
      this.handleThumbnailBorderRadius(borderRadius);
    }
  },
  handleThumbnailBorderRadius: function handleThumbnailBorderRadius(borderRadius) {
    var unit = borderRadius.unit;
    this.$element.find('.flex-control-nav li').css({
      'border-radius': borderRadius.top + unit + ' ' + borderRadius.right + unit + ' ' + borderRadius.bottom + unit + ' ' + borderRadius.left + unit
    });
  },
  bindEvents: function bindEvents() {
    this.$element.find('.woocommerce-product-gallery__image a').on('click', function (e) {
      e.stopImmediatePropagation();
      e.preventDefault();
    });
  }
});

function _default($scope) {
  new ProductImages({
    $element: $scope
  });
}

},{}],6:[function(require,module,exports){
function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;

  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }

  return arr2;
}

module.exports = _arrayLikeToArray;
},{}],7:[function(require,module,exports){
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

module.exports = _arrayWithHoles;
},{}],8:[function(require,module,exports){
function _interopRequireDefault(obj) {
  return obj && obj.__esModule ? obj : {
    "default": obj
  };
}

module.exports = _interopRequireDefault;
},{}],9:[function(require,module,exports){
function _iterableToArrayLimit(arr, i) {
  if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return;
  var _arr = [];
  var _n = true;
  var _d = false;
  var _e = undefined;

  try {
    for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
      _arr.push(_s.value);

      if (i && _arr.length === i) break;
    }
  } catch (err) {
    _d = true;
    _e = err;
  } finally {
    try {
      if (!_n && _i["return"] != null) _i["return"]();
    } finally {
      if (_d) throw _e;
    }
  }

  return _arr;
}

module.exports = _iterableToArrayLimit;
},{}],10:[function(require,module,exports){
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

module.exports = _nonIterableRest;
},{}],11:[function(require,module,exports){
var arrayWithHoles = require("./arrayWithHoles");

var iterableToArrayLimit = require("./iterableToArrayLimit");

var unsupportedIterableToArray = require("./unsupportedIterableToArray");

var nonIterableRest = require("./nonIterableRest");

function _slicedToArray(arr, i) {
  return arrayWithHoles(arr) || iterableToArrayLimit(arr, i) || unsupportedIterableToArray(arr, i) || nonIterableRest();
}

module.exports = _slicedToArray;
},{"./arrayWithHoles":7,"./iterableToArrayLimit":9,"./nonIterableRest":10,"./unsupportedIterableToArray":12}],12:[function(require,module,exports){
var arrayLikeToArray = require("./arrayLikeToArray");

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return arrayLikeToArray(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return arrayLikeToArray(o, minLen);
}

module.exports = _unsupportedIterableToArray;
},{"./arrayLikeToArray":6}]},{},[2]);
