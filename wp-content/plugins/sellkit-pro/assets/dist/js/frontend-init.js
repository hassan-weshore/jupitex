(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

var _interopRequireDefault = require("@babel/runtime/helpers/interopRequireDefault");

var _smartAlert = _interopRequireDefault(require("./pages/smart-alert"));

var FrontendInit = function FrontendInit() {
  var loadPagesScripts = function loadPagesScripts() {
    var pages = window.sellkit_pro_frontend.pages;

    if (pages.length.isArray && pages.length.length === 0) {
      return;
    }

    if (pages.includes('smart-alert')) {
      (0, _smartAlert["default"])();
    }
  };

  loadPagesScripts();
};

FrontendInit();

},{"./pages/smart-alert":2,"@babel/runtime/helpers/interopRequireDefault":3}],2:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var smartAlert = function smartAlert() {
  jQuery(document).on('click', '.sellkit-smart-alert-button', function () {
    var ruleId = jQuery(this).parents('div[data-source=sellkit_smart_alert_notice], li[data-source=sellkit_smart_alert_notice]').data('rule_id');
    wp.ajax.post('sellkit_smart_alert_send_click_log', {
      nonce: window.sellkit_pro_frontend.nonce,
      rule_id: ruleId
    });
  });
};

var _default = smartAlert;
exports["default"] = _default;

},{}],3:[function(require,module,exports){
function _interopRequireDefault(obj) {
  return obj && obj.__esModule ? obj : {
    "default": obj
  };
}

module.exports = _interopRequireDefault;
},{}]},{},[1]);
