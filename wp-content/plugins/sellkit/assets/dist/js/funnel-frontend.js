(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

var _interopRequireDefault = require("@babel/runtime/helpers/interopRequireDefault");

var _salesPage = _interopRequireDefault(require("./steps/sales-page"));

var _upsell = _interopRequireDefault(require("./steps/upsell"));

var funnelFrontend = function funnelFrontend() {
  switch (window.funnel.currentStep.type.key) {
    case 'sales-page':
      (0, _salesPage["default"])();
      break;

    case 'upsell':
      (0, _upsell["default"])();
      break;

    case 'downsell':
      (0, _upsell["default"])();
      break;

    case 'optin':
      (0, _salesPage["default"])();
      break;
  }
};

funnelFrontend();

},{"./steps/sales-page":2,"./steps/upsell":3,"@babel/runtime/helpers/interopRequireDefault":4}],2:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var SalesPage = function SalesPage() {
  var anchors = document.querySelectorAll('a[href$="?sellkit-next-link=yes"]');
  var nextStepLink = '#';

  if (typeof window.funnel.nextStep !== 'undefined') {
    nextStepLink = window.funnel.nextStepLink;
  }

  anchors.forEach(function (element) {
    element.setAttribute('href', nextStepLink);
  });
};

var _default = SalesPage;
exports["default"] = _default;

},{}],3:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var Upsell = function Upsell() {
  var $ = jQuery;
  var acceptButton = $('.sellkit-upsell-accept-button');
  var rejectButton = $('.sellkit-upsell-reject-button');
  var orderKey = acceptButton.data('order-key');

  if (_.isEmpty(orderKey)) {
    orderKey = rejectButton.data('order-key');
  }

  if (!_.isEmpty(orderKey)) {
    acceptButton.on('click', function (e) {
      $(e.target).parents('.sellkit-accept-reject-button-widget').find('.sellkit-upsell-popup').addClass('active');
      sendUpsellRequest('accept');
    });
    rejectButton.on('click', function (e) {
      $(e.target).parents('.sellkit-accept-reject-button-widget').find('.sellkit-upsell-popup').addClass('active');
      sendUpsellRequest('reject');
    });
  }

  $('.sellkit-upsell-popup').on('click', function (event) {
    closePopup(event);
  });
  $('.sellkit-upsell-popup-header img').on('click', function (event) {
    closePopup(event);
  });

  var closePopup = function closePopup(event) {
    if ($(event.target).hasClass('sellkit-upsell-popup-body')) {
      return;
    }

    $('.sellkit-upsell-popup').removeClass('active');
  };

  var sendUpsellRequest = function sendUpsellRequest(offerType) {
    wp.ajax.post('sellkit_upsell_operations', {
      sellkit_current_page_id: window.funnel.currentStep.page_id,
      order_key: orderKey,
      offer_type: offerType
    }).done(function (redirectUrl) {
      if (offerType === 'accept') {
        $('.sellkit-upsell-accepted').addClass('active');
        $('.sellkit-upsell-updating').removeClass('active');
      }

      window.location.href = redirectUrl;
    }).fail(function (data) {
      $('.sellkit-upsell-popup').trigger('click'); // eslint-disable-next-line no-console

      console.error(data);
    });
  };
};

var _default = Upsell;
exports["default"] = _default;

},{}],4:[function(require,module,exports){
function _interopRequireDefault(obj) {
  return obj && obj.__esModule ? obj : {
    "default": obj
  };
}

module.exports = _interopRequireDefault;
},{}]},{},[1]);
