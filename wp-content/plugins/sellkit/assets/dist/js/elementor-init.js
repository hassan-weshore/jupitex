(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = _default;

var _i18n = require("@wordpress/i18n");

/* eslint no-undef: 0 */
// Temporary turned off no undefined variable for localStorage
var $ = jQuery;
var wcCountries = JSON.parse(wc_country_select_params.countries);

function _default() {
  $('body').addClass('contains-sellkit-checkout'); // General class to handle compatibility with other themes.

  var parent = $('#sellkit-checkout-multistep-inner-wrap');
  var sidebar = $('.sellkit-checkout-right-column');
  var first = $('.sellkit-multistep-checkout-first');
  var second = $('.sellkit-multistep-checkout-second');
  var third = $('.sellkit-multistep-checkout-third');
  var breadcrumb = $('.sellkit-checkout-widget-breadcrumb-desktop, .sellkit-checkout-widget-breadcrumb-mobile');

  if ('1' !== sellkit_elementor.wcNeedShipping || typeof sellkitCheckoutShipping !== 'undefined' && true === sellkitCheckoutShipping) {
    third.show();
    $('.sellkit-one-page-checkout-place-order').css('justify-content', 'flex-end');
  }

  $('.go-to-shipping').on('click', function () {
    // Checks if required fields are filled.
    var requiredFieldsError = checkRequiredFields();

    if (true === requiredFieldsError) {
      return;
    }

    secondStepHeader();
    var secondTrigger = true;
    first.hide();

    if ($('.sellkit-one-page-shipping-methods').children().length > 0) {
      second.show();
    } else {
      third.show();
      secondTrigger = false;
    }

    if ($(window).width() > 600 && secondTrigger) {
      sidebar.css('background-color', '#effdf5');
    } //Fix Header & wrapper height.


    parent.css('height', 'auto');
    second.css('height', 'auto');
    second.css('min-height', $('.sellkit-checkout-left-column').height());
    $('.multistep-headers').css('height', 'auto'); // Manage Breadcrumb.

    breadcrumb.find('.information').removeClass('current').addClass('blue-line');
    breadcrumb.find('.shipping').addClass('current').removeClass('inactive');
  });
  $('.go-to-first').on('click', function () {
    first.show();
    third.hide();
    second.hide();
    parent.css('height', 'auto');
    first.css('height', 'auto');
    sidebar.css('background-color', 'transparent'); // Manage Breadcrumb.

    breadcrumb.find('.information').removeClass('blue-line').addClass('current');
    breadcrumb.find('.shipping, .payment').removeClass('current').removeClass('blue-line').addClass('inactive');
  });
  $('.go-to-payment').on('click', function () {
    // Checks if required fields are filled.
    var requiredFieldsError = checkRequiredFields();

    if (true === requiredFieldsError) {
      return;
    }

    secondStepHeader();
    second.hide();
    first.hide();
    third.show();
    $('.sellkit-one-page-checkout-payment-heading').css('margin-top', '0px');
    sidebar.css('background-color', 'transparent'); //Fix Header & wrapper height.

    parent.css('height', 'auto');
    third.css('height', 'auto');
    $('.multistep-headers').css('height', 'auto'); // Manage Breadcrumb.

    breadcrumb.find('.shipping').addClass('blue-line').removeClass('current');
    breadcrumb.find('.payment').addClass('current').removeClass('inactive');
  });
  $('.go-to-second , .go-to-second-header').on('click', function () {
    // Checks if required fields are filled.
    var requiredFieldsError = checkRequiredFields();

    if (true === requiredFieldsError) {
      return;
    }

    var secondTrigger = true;

    if ($('.sellkit-one-page-shipping-methods').children().length > 0) {
      second.show();
      first.hide();
    } else {
      second.hide();
      first.show();
      secondTrigger = false;
    }

    third.hide();
    parent.css('height', 'auto');
    second.css('height', 'auto');
    second.css('height', $('.sellkit-checkout-left-column').height());
    $('.multistep-headers').css('height', 'auto');

    if ($(window).width() > 600 && secondTrigger) {
      sidebar.css('background-color', '#effdf5');
    } // Manage Breadcrumb.


    breadcrumb.find('.shipping').addClass('current').removeClass('blue-line');
    breadcrumb.find('.payment').removeClass('current').addClass('inactive');
  }); //Inject login wrapper & functionality.

  emailProcess();
  LoginProcess(); // Billing method toggle.

  manageBillingMethod(); // Mobile summary.

  mobileSummary(); // Breadcrumb click management.

  breadcrumbLinks(); // Fix shipping & billing fields space issue.

  fixFieldSpaceIssue(); // Fields focus.

  organizeFieldsonLoad();
  fieldFocus();
  jQuery(document).ajaxComplete(function () {
    // Run after ajax
    sellkitCheckoutUpdateCartItem();
    applyCoupon();
    couponToggle();
    fixGatewayListUi();
  }); // Fix the payment gateways divider issue.

  fixGatewayListUi(); //Run when page loads.

  sellkitCheckoutUpdateCartItem(); // Apply coupon.

  applyCoupon(); // Coupon toggle.

  couponToggle(); // Postal code autocomplete.

  sellkitPostalCodeAutocomplete(); // Klarma checkout integration.

  sellkitKlarmaIntegration(); // Fix state change issue on load.

  fixStateIssueOnLoad(); // Shipping & billing country & state update on change

  fixClientCountryOnReload(); // Configure bundled products.

  sellkitCheckoutConfigureBundleProducts(); // Order bump.

  sellkitCheckoutOrderBump(); // Sellkit after ajax call is completed.

  sellkitAfterAjaxComplete();
}

var emailProcess = function emailProcess() {
  var emailField = $('#billing_email');
  var searchIcon = $('.jupiter-checkout-widget-email-search');
  var errorText = $('.sellkit-checkout-widget-email-error');
  var emptyError = $('.sellkit-checkout-widget-email-empty');
  var passwordWrap = $('.sellkit-checkout-widget-password-field');
  var passwordField = passwordWrap.find('#register_pass');
  var usernameWrap = $('.sellkit-checkout-widget-username-field');
  var usernameField = usernameWrap.find('input');
  var loginBtn = $('.login-wrapper');
  var createBox = $('.create-desc');
  var createCheck = $('#createaccount');
  emailField.on('keyup', function () {
    var _this = this;

    setTimeout(function () {
      var emailAddress = $(_this).val();

      if (_.isEmpty(emailAddress)) {
        emptyError.show().css('display', 'inline-block');
        errorText.hide();
        createBox.css('display', 'none');
        passwordWrap.addClass('login_hidden_section');
        usernameWrap.addClass('login_hidden_section');
        loginBtn.addClass('login_hidden_section');
        searchIcon.hide();
        return;
      }

      var check = validateEmailAddress(emailAddress);
      $('#createaccount').prop('checked', false);

      if (false === check) {
        errorText.show().css('display', 'inline-block');
        emptyError.hide();
        createBox.css('display', 'none');
        passwordWrap.addClass('login_hidden_section');
        usernameWrap.addClass('login_hidden_section');
        loginBtn.addClass('login_hidden_section');
        searchIcon.hide();
        return;
      }

      emptyError.hide();
      errorText.hide();
      searchIcon.show().css('display', 'inline-block');
      wp.ajax.post({
        action: 'sellkit_checkout_ajax_handler',
        sub_action: 'search_for_email',
        email: emailAddress,
        dataType: 'json',
        nonce: sellkit_elementor.nonce
      }).done(function () {
        successResultEmailCheck();
      }).fail(function () {
        errorResultEmailCheck();
      });
    }, 500);
  });
  usernameField.on('keyup', function () {
    var _this2 = this;

    setTimeout(function () {
      var userValue = $(_this2).val();
      wp.ajax.post({
        action: 'sellkit_checkout_ajax_handler',
        sub_action: 'search_for_username',
        user: userValue,
        dataType: 'json',
        nonce: sellkit_elementor.nonce
      }).done(function () {
        $('.sellkit-checkout-widget-username-error').hide();
      }).fail(function () {
        $('.sellkit-checkout-widget-username-error').show();
      });
    }, 500);
  }); // If user is going to create account show password and username fields if exists.

  createCheck.on('click', function () {
    if ($(this).is(':checked')) {
      if (passwordField.length > 0) {
        passwordWrap.removeClass('login_hidden_section');
      }

      if (usernameField.length > 0) {
        usernameWrap.removeClass('login_hidden_section');
      }
    } else {
      usernameWrap.addClass('login_hidden_section');
      passwordWrap.addClass('login_hidden_section');
    }
  });

  var successResultEmailCheck = function successResultEmailCheck() {
    createBox.css('display', 'none');
    passwordWrap.removeClass('login_hidden_section');
    usernameWrap.addClass('login_hidden_section');
    loginBtn.removeClass('login_hidden_section');
    searchIcon.hide();
  };

  var errorResultEmailCheck = function errorResultEmailCheck() {
    createBox.css('display', 'flex');
    passwordWrap.addClass('login_hidden_section');
    usernameWrap.addClass('login_hidden_section');
    loginBtn.addClass('login_hidden_section');
    searchIcon.hide();
  };
};

var validateEmailAddress = function validateEmailAddress(emailAddress) {
  var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
  return pattern.test(emailAddress);
};

var LoginProcess = function LoginProcess() {
  var submitBtn = $('.login-submit');
  var email = $('.login-mail');
  var pass = $('.login-pass');
  var result = $('.login-result');
  submitBtn.on('click', function () {
    if ('' === email.val() || '' === pass.val()) {
      result.text((0, _i18n.__)('Both Field required.', 'sellkit')).css({
        color: 'red'
      });
      return;
    }

    wp.ajax.post({
      beforeSend: function beforeSend() {
        $('.login-submit').css('opacity', '0.5');
      },
      action: 'sellkit_checkout_ajax_handler',
      sub_action: 'auth_user',
      email: email.val(),
      pass: pass.val(),
      nonce: sellkit_elementor.nonce
    }).done(function () {
      location.reload();
    }).fail(function (response) {
      $('.login-submit').css('opacity', '1');
      $('.login-result').html(response).css({
        color: 'red'
      });
    });
  });
};

var secondStepHeader = function secondStepHeader() {
  var email = $('#billing_email');
  $('.multistep-headers > .info-a > div > .mail').text(email.val());
  var countryValue = $('#shipping_country option:selected').text();
  var state = $('#sellkit-shipping_state');
  var postcode = $('#shipping_postcode');
  var city = $('#shipping_city');
  var addressA = $('#shipping_address_1');
  var addressB = $('#shipping_address_2');
  var finalAddress = postcode.val() ? ', ' + postcode.val() : '';
  finalAddress += addressB.val() ? ', ' + addressB.val() : '';
  finalAddress += addressA.val() ? ', ' + addressA.val() : '';
  finalAddress += city.val() ? ', ' + city.val() : '';
  finalAddress += state.val() ? ', ' + state.val() : '';
  finalAddress += countryValue && !countryValue.includes('/') ? ', ' + countryValue : '';

  if (',' === finalAddress.charAt(0)) {
    finalAddress = finalAddress.substring(1);
  }

  $('.multistep-headers > .info-b > div > .address').text(finalAddress);
  var method = $('#shipping_method').find('input[type=radio]:checked');
  var methodTxt = method.siblings('.labels').text();
  $('.multistep-headers > .info-c > div > .method').text(methodTxt);
};

var manageBillingMethod = function manageBillingMethod() {
  var billingWrap = $('.sellkit-one-page-checkout-billing');
  var methodA = billingWrap.find('.method-a');
  var methodB = billingWrap.find('.method-b'); // Get shipping values.

  var name = $('#shipping_first_name');
  var last = $('#shipping_last_name');
  var addressA = $('#shipping_address_1');
  var addressB = $('#shipping_address_2');
  var country = $('#shipping_country');
  var state = $('#sellkit-shipping_state');
  var postcode = $('#shipping_postcode');
  var city = $('#shipping_city');
  methodA.on('click', function () {
    billingWrap.find('.woocommerce-billing-fields__field-wrapper').hide();
    $('#billing_first_name').val(name.val());
    $('#billing_last_name').val(last.val());
    $('#billing_address_1').val(addressA.val());
    $('#billing_address_2').val(addressB.val());
    $('#billing_country').val(country.val()).trigger('change');
    $('#sellkit-billing_state').val(state.val()).trigger('change');
    $('#billing_postcode').val(postcode.val());
    $('#billing_city').val(city.val());
  });
  methodB.on('click', function () {
    $('.inner_wrapper').css('height', 'auto');
    billingWrap.find('.woocommerce-billing-fields__field-wrapper').show();
  });
};

var mobileSummary = function mobileSummary() {
  if ($(window).width() < 600) {
    $('#order_review').addClass('sellkit-mobile-multistep-order-summary');
  }

  var toggleBtn = $('.summary_toggle > .title, .summary_toggle > i, .summary_toggle > .icon');
  var wrap = $('.summary_toggle');
  toggleBtn.on('click', function () {
    $('#order_review').toggle();

    if ('Hide order summary' === toggleBtn.text()) {
      wrap.find('.title').text((0, _i18n.__)('Show order summary', 'sellkit'));
      wrap.find('i').addClass('fa-chevron-down').removeClass('fa-chevron-up');
      wrap.css('border-bottom-width', '0px');
    } else {
      wrap.find('.title').text((0, _i18n.__)('Hide order summary', 'sellkit'));
      wrap.find('i').addClass('fa-chevron-up').removeClass('fa-chevron-down');
      wrap.css('border-bottom-width', '1px');
    }

    var parent = $('#sellkit-checkout-multistep-inner-wrap');
    parent.css('height', 'auto');
  });
};

var fieldFocus = function fieldFocus() {
  var $fields = $('.sellkit-checkout-local-fields').find('input, select, hidden, textarea, #sellkit-billing_state ,#sellkit-shipping_state, .validate-email');
  $fields.each(function () {
    var _this3 = this;

    var miniTitle = $(this).parent().parent().parent().find('.mini-title');
    var $thisValue = $(this).val(); // On load.

    if (!_.isEmpty($thisValue)) {
      $(this).addClass('filled').removeClass('empty');
      miniTitle.css({
        display: 'flex'
      });
    } // On change/focusOut


    $(this).on('change input focusout', function (e) {
      var changedValue = $(_this3).val();

      if (changedValue || $(_this3).find('option').length) {
        $(_this3).addClass('filled');
        $(_this3).removeClass('empty');
        $(_this3).parents('.sellkit-widget-checkout-fields').find('.mini-title').css('display', 'flex');
      } else {
        $(_this3).addClass('empty');
        $(_this3).removeClass('filled');
        $(_this3).parents('.sellkit-widget-checkout-fields').find('.mini-title').hide();
      } // On focusout validate fields.


      if ('focusout' === e.type) {
        FieldsMiniTitles($(_this3), 'focusout');
        var parent = $(_this3).parent().parent().parent(); // Required validation. return if required field rule is not followed.

        if (parent.hasClass('validate-required')) {
          if ('INPUT' === _this3.nodeName && 'checkbox' === $(_this3).attr('type')) {
            if (!_this3.checked) {
              parent.find('.sellkit-required-validation').css('display', 'inline-flex');
              return;
            }
          }

          if (_.isEmpty($(_this3).val())) {
            parent.find('.sellkit-required-validation').css('display', 'inline-flex');
            return;
          }

          parent.find('.sellkit-required-validation').css('display', 'none');
        } // Required validation of this field if exists, passed successfully. now postcode validation.


        if (parent.hasClass('sellkit-checkout-fields-validation-postcode')) {
          var postcodeVal = $(_this3).val();

          if (_.isEmpty(postcodeVal)) {
            return;
          } // Check field type to get related country value. we can't validate without country code.


          var postcodeName = $(_this3).attr('name');
          var postcodeCountry = $('#billing_country');

          if (postcodeName.includes('shipping')) {
            postcodeCountry = $('#shipping_country');
          } // Keep validating only if country field is present.


          if (!postcodeCountry.length) {
            return;
          }

          var countryCode = postcodeCountry.val();

          if (_.isEmpty(countryCode)) {
            parent.find('.sellkit-checkout-field-global-errors').show().text((0, _i18n.__)('Please select a country.', 'sellkit'));
            return;
          } else {
            // eslint-disable-line
            parent.find('.sellkit-checkout-field-global-errors').hide().text('');
          }

          parent.find('.sellkit-checkout-field-global-errors').hide().text('');
          postcodeValidation(postcodeVal, countryCode, $(_this3));
        } // Phone validation using woocommerce way.


        if (parent.hasClass('sellkit-checkout-fields-validation-phone')) {
          var phone = $(_this3).val();

          if (_.isEmpty(phone)) {
            return;
          }

          phoneNumberValidation(phone, $(_this3));
        }
      }

      if ('change' === e.type && ('billing_country' === $(_this3).attr('id') || 'shipping_country' === $(_this3).attr('id'))) {
        var _countryCode = $(_this3).val();

        var states = wcCountries[_countryCode];
        var state = 'sellkit-shipping_state';

        if ('billing_country' === $(_this3).attr('id')) {
          state = 'sellkit-billing_state';
        }

        var stateField = document.getElementById(state);

        if (_.isNull(stateField)) {
          return;
        }

        $(stateField).empty();

        for (var keys in states) {
          var option = document.createElement('option');
          option.value = keys;
          option.innerHTML = states[keys];
          stateField.appendChild(option);
        }
      }
    });
  });
};

var breadcrumbLinks = function breadcrumbLinks() {
  var $parent = $('.sellkit-checkout-widget-breadcrumb-mobile, .sellkit-checkout-widget-breadcrumb-desktop');
  var detector = $('.sellkit-multistep-checkout-first');
  $parent.find('.information').on('click', function () {
    $('.go-to-first').click();
  });
  $parent.find('.shipping').on('click', function () {
    var display = detector.css('display');

    if ('none' === display) {
      $('.go-to-second').click();
    } else {
      $('.go-to-shipping').click();
    }
  });
  $parent.find('.payment').on('click', function () {
    $('.go-to-payment').click();
    $('.information').removeClass('current').addClass('inactive, blue-line');
  });
};

var fixFieldSpaceIssue = function fixFieldSpaceIssue() {
  $(function () {
    var shippingFields = $('#customer_details .sellkit-widget-checkout-fields');
    shippingFields.each(function (index) {
      if ($(shippingFields[index + 1]).length) {
        var next = $(shippingFields[index + 1]).offset().top;
        var current = $(shippingFields[index]).offset().top;

        if (next > current) {
          $(shippingFields[index]).addClass('sellkit-checkout-excluded-wrapper-fields');
        }
      } else {
        $(shippingFields[index]).addClass('sellkit-checkout-excluded-wrapper-fields');
      }
    });
  });
};

var organizeFieldsonLoad = function organizeFieldsonLoad() {
  $(function () {
    var fields = $('.sellkit-widget-checkout-fields').find('input, select, textarea');
    fields.each(function () {
      var tag = this.nodeName;

      if ('SELECT' === tag) {
        $(this).parent().parent().parent().addClass('sellkit-checkout-field-select');
      }

      FieldsMiniTitles($(this), 'load');
    });
  });
  $(document).on('mousemove change', function (e) {
    var fields = $('.sellkit-widget-checkout-fields').find('input, select, textarea');
    fields.each(function () {
      var tag = this.nodeName;

      if ('SELECT' === tag) {
        $(this).parent().parent().parent().addClass('sellkit-checkout-field-select');
      }

      FieldsMiniTitles($(this), e.type);
    });
  });
};

var FieldsMiniTitles = function FieldsMiniTitles(field, event) {
  if (field.attr('multiple')) {
    field.addClass('filled');
    field.removeClass('empty');
    field.parents('.sellkit-widget-checkout-fields').find('.mini-title').css('display', 'flex');
    return;
  }

  if (_.isEmpty(field.val())) {
    field.addClass('empty');
    field.removeClass('filled');
    field.parents('.sellkit-widget-checkout-fields').find('.mini-title').css('display', 'none');
  } else {
    field.addClass('filled');
    field.removeClass('empty');
    field.parents('.sellkit-widget-checkout-fields').find('.mini-title').css('display', 'flex');
  } // Hide wrapper if field is hidden


  if ('hidden' === field.attr('type')) {
    field.parents('.sellkit-widget-checkout-fields').addClass('sellkit-hide-completely');
  }

  if ('change' !== event) {
    return;
  } // Hide Billing state wrapper when these fields are hidden.


  if ('billing_country' === field.attr('id')) {
    var $state = document.getElementById('sellkit-billing_state');

    if (!_.isNull($state) && 'SELECT' === $state.nodeName && $('#sellkit-billing_state option').length < 1) {
      var stateField = $('#sellkit-billing_state');
      var parent = stateField.parent().parent().parent();
      parent.removeClass('sellkit-checkout-field-select');
      var placeholder = stateField.attr('placeholder');
      var stateInput = document.createElement('input');
      stateInput.setAttribute('type', 'text');
      stateInput.setAttribute('id', 'sellkit-billing_state');
      stateInput.setAttribute('name', 'billing_state');
      stateInput.setAttribute('placeholder', placeholder); // Replace select field with text.

      stateField.remove();
      parent.find('.woocommerce-input-wrapper').append(stateInput);
    }

    var countryCode = field.val();
    var states = wcCountries[countryCode];

    if (!_.isNull($state) && 'INPUT' === $state.nodeName && !_.isEmpty(states)) {
      var _stateField = $('#sellkit-billing_state');

      var _placeholder = _stateField.attr('placeholder');

      var _parent = _stateField.parent().parent().parent();

      _parent.addClass('sellkit-checkout-field-select');

      if (_.isUndefined(_placeholder) || _.isEmpty(_placeholder)) {
        _placeholder = (0, _i18n.__)('State', 'sellkit');
      } // Replace text field with select field.


      _stateField.remove();

      var stateSelect = document.createElement('select');
      stateSelect.setAttribute('name', 'billing_state');
      stateSelect.setAttribute('id', 'sellkit-billing_state');
      stateSelect.setAttribute('placeholder', _placeholder);
      stateSelect.addEventListener('change', function () {
        sellkitSetAddressDetails();
      });

      for (var keys in states) {
        var option = document.createElement('option');
        option.value = keys;
        option.innerHTML = states[keys];
        stateSelect.appendChild(option);
      }

      _parent.find('.woocommerce-input-wrapper').append(stateSelect);
    }
  } // Remove select icon when field is text for the shipping/billing state fields.


  if ('shipping_country' === field.attr('id')) {
    var _$state = document.getElementById('sellkit-shipping_state');

    if (!_.isNull(_$state) && 'SELECT' === _$state.nodeName && $('#sellkit-shipping_state option').length < 1) {
      var _stateField2 = $('#sellkit-shipping_state');

      var _parent2 = _stateField2.parent().parent().parent();

      _parent2.removeClass('sellkit-checkout-field-select');

      var _placeholder2 = _stateField2.attr('placeholder');

      var _stateInput = document.createElement('input');

      _stateInput.setAttribute('type', 'text');

      _stateInput.setAttribute('id', 'sellkit-shipping_state');

      _stateInput.setAttribute('name', 'shipping_state');

      _stateInput.setAttribute('placeholder', _placeholder2); // Replace select field with text.


      _stateField2.remove();

      _parent2.find('.woocommerce-input-wrapper').append(_stateInput);
    }

    var _countryCode2 = field.val();

    var _states = wcCountries[_countryCode2];

    if (!_.isNull(_$state) && 'INPUT' === _$state.nodeName && !_.isEmpty(_states)) {
      var _stateField3 = $('#sellkit-shipping_state');

      var _placeholder3 = _stateField3.attr('placeholder');

      var _parent3 = _stateField3.parent().parent().parent();

      _parent3.addClass('sellkit-checkout-field-select');

      if (_.isUndefined(_placeholder3) || _.isEmpty(_placeholder3)) {
        _placeholder3 = (0, _i18n.__)('State', 'sellkit');
      } // Replace text field with select field.


      _stateField3.remove();

      var _stateSelect = document.createElement('select');

      _stateSelect.setAttribute('name', 'shipping_state');

      _stateSelect.setAttribute('id', 'sellkit-shipping_state');

      _stateSelect.setAttribute('placeholder', _placeholder3);

      _stateSelect.addEventListener('change', function () {
        sellkitSetAddressDetails();
      });

      for (var _keys in _states) {
        var _option = document.createElement('option');

        _option.value = _keys;
        _option.innerHTML = _states[_keys];

        _stateSelect.appendChild(_option);
      }

      _parent3.find('.woocommerce-input-wrapper').append(_stateSelect);
    }
  }
};
/**
 * Change cart item quantity.
 *
 * @since 1.2.5
 */


var sellkitCheckoutUpdateCartItem = function sellkitCheckoutUpdateCartItem() {
  $('.sellkit-one-page-checkout-product-qty').off('change').on('change', function () {
    $(this).attr('readonly', true);
    wp.ajax.post({
      action: 'sellkit_checkout_ajax_handler',
      sub_action: 'change_cart_item_qty',
      qty: $(this).val(),
      id: $(this).attr('data-id'),
      mode: 'edit',
      related_checkout: $('#sellkit_current_page_id').val(),
      nonce: sellkit_elementor.nonce
    }).always(function () {
      $(document.body).trigger('update_checkout');
      $('.sellkit-one-page-checkout-product-qty').attr('readonly', false);
    });
  });
  var paymentMethod = $('.sellkit-one-page-checkout-payment-methods').find('input[name=payment_method]:checked');
  paymentMethod.parent().parent().next().show();
  $('.sellkit-one-page-pay-method').on('click', function () {
    $('.sellkit_payment_box').hide();
    $(this).parent().parent().next().show();
  });
  var count = $('.sellkit-checkout-widget-order-summary-tfoot').children().length;

  if (count <= 3) {
    $('.cart-subtotal td').css('padding-bottom', '8px');
    $('.cart-subtotal th').css('padding-bottom', '8px');
  }
};
/**
 * Apply a coupon.
 *
 * @since 1.2.5
 */


var applyCoupon = function applyCoupon() {
  $('.sellkit-apply-coupon').off('click').on('click', function () {
    wp.ajax.post({
      beforeSend: function beforeSend() {
        $('.jx-apply-coupon').css('opacity', 0.5);
      },
      action: 'sellkit_checkout_ajax_handler',
      sub_action: 'apply_coupon',
      code: jQuery('.sellkit-custom-coupon-form').find('.jx-coupon').val(),
      nonce: sellkit_elementor.nonce
    }).done(function () {
      $(document.body).trigger('update_checkout');
      $('.jx-apply-coupon').css('opacity', 1);
    }).fail(function () {
      $('.jx-apply-coupon').css('opacity', 1);
    });
  });
};
/**
 * Toggle coupon form on click event.
 *
 * @since 1.2.5
 */


var couponToggle = function couponToggle() {
  if ($('.sellkit-coupon-toggle').length) {
    $('.sellkit-custom-coupon-form').css('display', 'none');
  }

  $('.sellkit-coupon-toggle').off('click').on('click', function () {
    var direction = 'row';
    var status = $('.sellkit-custom-coupon-form').css('display');
    var displayValue = '';

    if ($(window).width() < 600) {
      direction = 'column';
    }

    if ('none' === status) {
      displayValue = 'inline-flex';
    } else {
      displayValue = 'none';
    }

    $('.sellkit-custom-coupon-form').css({
      display: displayValue,
      flexDirection: direction
    });
  });
};
/**
 * Validate postcode in WooCommerce way.
 *
 * @param {string} postcode
 * @param {string} country
 * @param {Object} element
 */


var postcodeValidation = function postcodeValidation(postcode, country, element) {
  var parentElement = element.attr('id');
  wp.ajax.post({
    action: 'sellkit_checkout_ajax_handler',
    sub_action: 'validate_postcode',
    post_code: postcode,
    country_code: country,
    parent: parentElement,
    nonce: sellkit_elementor.nonce
  }).done(function (response) {
    var field = $('#' + response);
    var parent = field.parent().parent().parent();
    parent.find('.sellkit-checkout-field-global-errors').hide().text('');
  }).fail(function (response) {
    var field = $('#' + response);
    var parent = field.parent().parent().parent();
    parent.find('.sellkit-checkout-field-global-errors').show().text((0, _i18n.__)('Postcode is not valid.', 'sellkit'));
  });
};
/**
 * Validate phone number in WooCommerce way.
 *
 * @param {string} phone
 * @param {Object} element
 */


var phoneNumberValidation = function phoneNumberValidation(phone, element) {
  var parentElement = element.attr('id');
  wp.ajax.post({
    action: 'sellkit_checkout_ajax_handler',
    sub_action: 'validate_phone_number',
    phone_number: phone,
    parent: parentElement,
    nonce: sellkit_elementor.nonce
  }).done(function (response) {
    var field = $('#' + response);
    var parent = field.parent().parent().parent();
    parent.find('.sellkit-checkout-field-global-errors').hide().text('');
  }).fail(function (response) {
    var field = $('#' + response);
    var parent = field.parent().parent().parent();
    parent.find('.sellkit-checkout-field-global-errors').show().text((0, _i18n.__)('Phone number is not valid.', 'sellkit'));
  });
};
/**
 * Auto populate city and state fields using postcode by getting information from third api : https://api.zippopotam.us/.
 *
 * @since 1.2.5
 */


var sellkitPostalCodeAutocomplete = function sellkitPostalCodeAutocomplete() {
  $('.post_code_autocomplete').find('input').on('paste focusout', function () {
    var postcode = $(this).val();
    var country = $('#shipping_country').val();
    var state = $('#sellkit-shipping_state');
    var city = $('#shipping_city');
    var parent = $(this).parent().parent().parent();

    if ('billing_postcode' === $(this).attr('id')) {
      country = $('#billing_country').val();
      state = $('#sellkit-billing_state');
      city = $('#billing_city');
    }

    if (_.isEmpty(postcode) || _.isEmpty(country)) {
      return;
    }

    wp.ajax.post({
      action: 'sellkit_checkout_ajax_handler',
      sub_action: 'sellkit_state_lookup_by_postcode',
      country_value: country,
      postcode_value: postcode,
      nonce: sellkit_elementor.nonce
    }).done(function (response) {
      setError('');
      var body = response;
      var placeCity = body.places[0]['place name'];
      var placeState = body.places[0]['state abbreviation'];
      var countryResponse = body['country abbreviation'];

      if (_.isEmpty(placeState)) {
        placeState = body.places[0].state;
      }

      var countries = ['DE', 'TR', 'BD', 'DO', 'GB', 'GT', 'JP', 'TH', 'ZA'];

      if (countries.includes(countryResponse)) {
        placeState = fixConflictWooCommerceStatesWithApi(countryResponse, placeState, body);
      }

      setFields(placeCity, placeState);
    }).fail(function (response) {
      setError(response);
    });

    var setFields = function setFields(placeCity, placeState) {
      state.val(placeState).addClass('filled');
      city.val(placeCity).addClass('filled');

      if (null === state.val() && 'SELECT' === state[0].nodeName) {
        state.prop('selectedIndex', 0);
      }
    };

    var setError = function setError(response) {
      parent.find('.sellkit-checkout-field-global-errors').text(response);
    };
  });
};
/**
 * Fix some countries states conflict between WooCommerce and https://api.zippopotam.us/.
 *
 * @param {string} country response country.
 * @param {string} state response state.
 * @param {Object} body response body.
 * @return {string} state.
 */


var fixConflictWooCommerceStatesWithApi = function fixConflictWooCommerceStatesWithApi(country, state, body) {
  switch (country) {
    case 'TR':
      // Turkey.
      state = 'TR' + state;
      break;

    case 'DE':
      // Germany.
      state = 'DE-' + state;
      break;

    case 'BD':
      // Bangladesh.
      state = sellkitCheckoutCountryFixAutoPopulate(state, country, body);
      break;

    case 'DO':
      // Dominican Republic. Api has no proper response. we select first option
      state = 'DO-01';
      break;

    case 'GB':
      // United kingdom.
      state = body.places[0].state;
      break;

    case 'GT':
      // Guatemala.
      state = 'GT-AV';
      break;

    case 'JP':
      // Japan.
      state = sellkitCheckoutCountryFixAutoPopulate(state, country, body);
      break;

    case 'TH':
      // Thailand.
      state = sellkitCheckoutCountryFixAutoPopulate(state, country, body);
      break;

    case 'ZA':
      // South Africa. API has no proper response. we select first option.
      state = 'EC';
      break;

    default:
      return state;
  }

  return state;
};
/**
 * Integration with Klarma checkout gateway.
 *
 * @since 1.2.5
 */


var sellkitKlarmaIntegration = function sellkitKlarmaIntegration() {
  $(document).ready(function () {
    $('#sellkit-klarna-pay-button').on('click', function () {
      $('#payment_method_kco').trigger('click');
    });
  });
};
/**
 * Save client state and country on change event.
 *
 * @since 1.2.5
 */


var fixClientCountryOnReload = function fixClientCountryOnReload() {
  $('#shipping_country, #billing_country, #sellkit-shipping_state, #sellkit-billing_state').on('change', function () {
    sellkitSetAddressDetails();
  });
};
/**
 * Ajax to save user state and country for both shipping and billing section if those fields are present.
 *
 * @since 1.2.5
 */


var sellkitSetAddressDetails = function sellkitSetAddressDetails() {
  wp.ajax.post({
    action: 'sellkit_checkout_ajax_handler',
    sub_action: 'set_customer_details_ajax',
    country: document.querySelector('#billing_country') ? document.getElementById('billing_country').value : '',
    state: document.querySelector('#sellkit-billing_state') ? document.getElementById('sellkit-billing_state').value : '',
    shipping_country: document.querySelector('#shipping_country') ? document.getElementById('shipping_country').value : '',
    shipping_state: document.querySelector('#sellkit-shipping_state') ? document.getElementById('sellkit-shipping_state').value : '',
    nonce: sellkit_elementor.nonce
  }).always(function () {
    $(document.body).trigger('update_checkout');
  });
};
/**
 * On page load set shipping and billing state & country values.
 *
 * @since 1.2.5
 */


var fixStateIssueOnLoad = function fixStateIssueOnLoad() {
  var primaryValue = '';

  if (document.querySelector('#shipping_country')) {
    primaryValue = $('#shipping_country').val();
    $('#shipping_country').val($('#shipping_country option:eq(1)').val());
    $('#shipping_country').val(primaryValue).trigger('change');
  }

  if (document.querySelector('#billing_country')) {
    primaryValue = $('#billing_country').val();
    $('#billing_country').val($('#billing_country option:eq(1)').val());
    $('#billing_country').val(primaryValue).trigger('change');
  }
};
/**
 * Modify cart items when bundle products are present.
 *
 * @since 1.2.5
 */


var sellkitCheckoutConfigureBundleProducts = function sellkitCheckoutConfigureBundleProducts() {
  bundleProductOnQuantityChange();
  var radioProducts = $('.sellkit-checkout-bundle-item');
  radioProducts.off('change').on('change', function () {
    var addType = $(this).attr('type'); // On bundle product change, modify bump too.

    $('.sellkit-checkout-bump-order-products').each(function () {
      if ($(this).is(':checked') && 'radio' === addType) {
        $(this).trigger('click');
      }
    });
    var product = $(this).val();
    var quantity = $(this).parent().parent().find('.sellkit-checkout-single-bundle-item-quantity').val();
    var checkout = $('#sellkit_current_page_id').val();
    var modifyType = 'add';

    if (false === $(this).is(':checked')) {
      modifyType = 'remove';
    }

    wp.ajax.post({
      action: 'sellkit_checkout_ajax_handler',
      sub_action: 'sellkit_checkout_modify_cart_by_bundle_products',
      id: product,
      qty: quantity,
      checkout_id: checkout,
      type: addType,
      modify: modifyType,
      nonce: sellkit_elementor.nonce
    }).always(function () {
      $(document.body).trigger('update_checkout');
    });
  });
};
/**
 * Change cart item quantity on bundle product quantity change.
 *
 * @since 1.3.1
 */


var bundleProductOnQuantityChange = function bundleProductOnQuantityChange() {
  $('.sellkit-checkout-single-bundle-item-quantity').on('change', function () {
    var productCartKey = $(this).attr('data-id');
    var quantity = $(this).val();

    if (false === $(this).parent().parent().find('.sellkit-checkout-bundle-item').is(':checked')) {
      return;
    }

    wp.ajax.post({
      action: 'sellkit_checkout_ajax_handler',
      sub_action: 'sellkit_checkout_modify_cart_by_bundle_products',
      key: productCartKey,
      qty: quantity,
      nonce: sellkit_elementor.nonce
    }).always(function () {
      $(document.body).trigger('update_checkout');
    });
  });
};
/**
 * Manage to add or remove a bump product from cart.
 *
 * @since 1.2.5
 */


var sellkitCheckoutOrderBump = function sellkitCheckoutOrderBump() {
  $('.sellkit-checkout-bump-order-products').on('click', function () {
    var subAction;

    if ($(this).is(':checked')) {
      subAction = 'add';
    } else {
      subAction = 'remove';
    }

    wp.ajax.post({
      action: 'sellkit_checkout_ajax_handler',
      sub_action: 'change_cart_item_qty',
      qty: $(this).attr('data-qty'),
      id: $(this).val(),
      mode: subAction,
      related_checkout: $('#sellkit_current_page_id').val(),
      nonce: sellkit_elementor.nonce
    }).always(function () {
      $(document.body).trigger('update_checkout');
    });
  });
};
/**
 * Checks if a required field is not filled.
 *
 * @return {boolean} state of error.
 * @since 1.2.5
 */


var checkRequiredFields = function checkRequiredFields() {
  var fields = document.querySelectorAll('#sellkit-checkout-widget-shipping-fields > .validate-required');
  var error = false; // Let's check email field first.

  if (_.isEmpty($('#billing_email').val())) {
    $('.sellkit-checkout-widget-email-empty').css('display', 'block');
    return true;
  } // Hide warning after filling email field.


  $('.sellkit-checkout-widget-email-empty').css('display', 'none');

  if (fields.length < 1) {
    return error;
  }

  fields.forEach(function (item) {
    var target = $(item).find('input, select, textarea').val();

    if ('' === target) {
      error = true;
      $(item).find('.sellkit-required-validation').css('display', 'inline-flex');
    }
  });
  return error;
};
/**
 * Loop through WooCommerce states and replace api returned value.
 *
 * @param {string} state
 * @param {string} countryAbbreviated
 * @param {Object} body
 * @return {string} state value.
 * @since 1.2.5
 */


var sellkitCheckoutCountryFixAutoPopulate = function sellkitCheckoutCountryFixAutoPopulate(state, countryAbbreviated, body) {
  var value = '';
  var country = '';

  if ('BD' === countryAbbreviated) {
    country = wcCountries.BD;
  }

  if ('JP' === countryAbbreviated) {
    country = wcCountries.JP;
    state = state.substring(0, state.length - 3);
  }

  if ('TH' === countryAbbreviated) {
    value = 'TH-37';
    country = wcCountries.TH;
    state = body.places[0].state;
  }

  for (var key in country) {
    if (state === country[key]) {
      value = key;
    }
  }

  return value;
};

var sellkitAfterAjaxComplete = function sellkitAfterAjaxComplete() {
  $('#place_order').on('click', function () {
    var count = 0;
    var transfer = setInterval(function () {
      count += 1;

      if ($('.wc_payment_method > .woocommerce-NoticeGroup-checkout').length > 0) {
        // Remove notice from payment area and move it to top of page( default error area ).
        var notice = $('.wc_payment_method').find('.woocommerce-NoticeGroup-checkout').html();
        $('.wc_payment_method').find('.woocommerce-NoticeGroup-checkout').remove();
        $('.woocommerce-notices-wrapper').first().append(notice);
        $(document.body).animate({
          scrollTop: $('.woocommerce-notices-wrapper').offset().top - 100
        }, 500); // Stop interval.

        clearInterval(transfer);
      }

      if (count > 20) {
        clearInterval(transfer);
      }
    }, 500);
  });
};
/**
 * Hide payment gateways <hr> divider properly after ajax calls.
 * This could not be done with css.
 *
 * @since 1.5.4
 */


var fixGatewayListUi = function fixGatewayListUi() {
  $('.wc_payment_methods hr.sellkit-checkout-widget-divider').each(function (index, item) {
    $(item).css('display', $(item).prevAll('li').first().css('display'));
  });
};

},{"@wordpress/i18n":15}],2:[function(require,module,exports){
"use strict";

(function ($) {
  var SellkitFrontend = function SellkitFrontend() {
    var widgets = {
      'sellkit-product-images.default': require('./product-images')["default"],
      'sellkit-checkout.default': require('./checkout')["default"],
      'sellkit-optin.default': require('./optin/optin')["default"]
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

},{"./checkout":1,"./optin/optin":3,"./product-images":6}],3:[function(require,module,exports){
"use strict";

var _interopRequireDefault = require("@babel/runtime/helpers/interopRequireDefault");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = _default;

var _intelligentTel = _interopRequireDefault(require("./submodules/intelligent-tel"));

var _fieldValidation = _interopRequireDefault(require("./submodules/field-validation"));

var _i18n = require("@wordpress/i18n");

var Optin = elementorModules.frontend.handlers.Base.extend({
  form: null,
  onInit: function onInit() {
    this.form = this.$element.find('form.sellkit-optin'); // Register submit listener.

    this.form.on('submit', this.onSubmit.bind(this)); // Initialize ITI for tel fields.

    _intelligentTel["default"].initializeITI(this.form); // AutoComplete address fields.


    jQuery(document).ready(this.autoCompleteAddress.bind(this)); // Initialize flatpickr for data and time fields.

    this.initializeFlatpickr();
  },
  onSubmit: function onSubmit(event) {
    var _this = this;

    event.preventDefault();

    if (!this.checkSaveState()) {
      return;
    }

    this.clearAllNotices();
    this.form.css('opacity', 0.5);

    var validated = _fieldValidation["default"].validateFormFields(this.form);

    if (!validated) {
      this.form.css('opacity', 1);
      return;
    }

    wp.ajax.send('sellkit_optin_frontend', {
      data: this.prepareFormData(),
      type: 'POST',
      dataType: 'json',
      processData: false,
      contentType: false,
      success: this.onSuccess,
      error: this.onFailure,
      complete: function complete() {
        return _this.form.css('opacity', 1);
      }
    });
  },
  checkSaveState: function checkSaveState() {
    if (!this.isEdit) {
      return true;
    }

    var btn = jQuery(elementor.panel.el).find('button#elementor-panel-saver-button-publish');

    if (!btn.length || btn.hasClass('elementor-disabled')) {
      return true;
    }

    var adminErrorsNode = "\n\t\t\t<div class=\"sellkit-optin-admin-alert\" role=\"alert\">\n\t\t\t\t<span class=\"title\">\n\t\t\t\t\tPlease first update/publish the changes.\n\t\t\t\t</span>\n\t\t\t</div>\n\t\t";
    this.form.before(adminErrorsNode);
    return false;
  },
  clearAllNotices: function clearAllNotices() {
    var form = this.form; // Reset everything.

    form.parent().find('.sellkit-optin-response').remove();
    form.parent().find('.sellkit-optin-admin-alert').remove();
    form.parent().removeClass('sellkit-optin-error');
    form.parent().removeClass('sellkit-optin-success');
  },
  prepareFormData: function prepareFormData() {
    _intelligentTel["default"].fixTelBeforeSubmit(this.form);

    var formData = new FormData(this.form[0]);
    var entries = Array.from(formData.entries()); // Unify nonsingular formData entries ( happens for checkbox or multiselection select fields ) .

    entries.forEach(function (entry, i) {
      var value = entry[1];
      entries.forEach(function (_entry, _i) {
        if (_i !== i && _entry[0] === entry[0]) {
          value += ", ".concat(_entry[1]);
          formData["delete"](_entry[0]);
        }
      });
      formData.set(entry[0], value);
    });
    formData.append('referrer', location.toString());
    formData.append('action', 'sellkit_optin_frontend');
    formData.append('nonce', window.sellkit_elementor.nonce); // To make _POST content compatible with sellkit_funnel() functionality and let it find the funnel data.

    formData.append('sellkit_current_page_id', formData.get('post_id'));
    return formData;
  },
  onSuccess: function onSuccess(response) {
    var form = this.form; // Success Message.

    form.trigger('reset');
    form.parent().addClass('sellkit-optin-success');
    form.after("<div class=\"sellkit-optin-response\">".concat(response.message, "</div>")); // Admin Error Messages.

    this.printAdminErrors(response.admin_errors); // Download.

    if (response.downloadURL) {
      window.open(response.downloadURL, '_blank');
    } // Redirect.


    if (!jQuery.isEmptyObject(response.redirectURL)) {
      window.location.href = response.redirectURL;
    }
  },
  onFailure: function onFailure(response) {
    var form = this.form;
    form.parent().removeClass('sellkit-optin-success');
    form.parent().addClass('sellkit-optin-error'); // Handle user error messages.

    if (!_.isEmpty(response.errors)) {
      _.each(response.errors, function (error) {
        form.after("<div class=\"sellkit-optin-response\">".concat(error, "</div>"));
      });
    } // Admin Error Messages.


    this.printAdminErrors(response.admin_errors);
  },
  printAdminErrors: function printAdminErrors(errors) {
    if (_.isEmpty(errors)) {
      return;
    }

    var errorList = '';

    _.each(errors, function (error) {
      errorList += "<li>".concat(error, "</li>");
    });

    var adminErrorsNode = "\n\t\t\t<div class=\"sellkit-optin-admin-alert\" role=\"alert\">\n\t\t\t\t<span class=\"title\">\n\t\t\t\t\t".concat((0, _i18n.__)('Following messages are visible only for admin users.', 'sellkit'), "\n\t\t\t\t</span>\n\t\t\t\t<div class=\"description\">\n\t\t\t\t\t<ul> ").concat(errorList, " </ul>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t");
    this.form.before(adminErrorsNode);
  },
  initializeFlatpickr: function initializeFlatpickr() {
    var dateField = this.form.find('.flatpickr[type=text]');
    var customLocale = dateField.data('locale');
    var locale = {
      firstDayOfWeek: 1
    };

    if (!_.isUndefined(customLocale) && 'default' !== customLocale) {
      locale = customLocale;
    }

    dateField.flatpickr({
      locale: locale,
      minuteIncrement: 1
    });
  },
  autoCompleteAddress: function autoCompleteAddress() {
    var addressFields = this.form.find('input[data-type="address"]');
    var google = this.isEdit ? window.parent.google : window.google;

    if (!addressFields.length || !google) {
      return;
    }

    _.each(addressFields, function (input) {
      new google.maps.places.Autocomplete(input, {
        types: ['geocode'],
        fields: ['address_components']
      });
    });
  }
});

function _default($scope) {
  new Optin({
    $element: $scope
  });
}

},{"./submodules/field-validation":4,"./submodules/intelligent-tel":5,"@babel/runtime/helpers/interopRequireDefault":8,"@wordpress/i18n":15}],4:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;
var _default = {
  messageRequired: '',
  messageError: '',
  validateFormFields: function validateFormFields(form) {
    var _this = this;

    this.getCustomMessages(form);
    var formData = form.find('[name^="fields"]');
    var result = true;

    _.each(formData, function (field) {
      var message = _this.getValidationMessage(field);

      if (!_.isEmpty(message)) {
        result = false;
      }

      _this.toggleFieldMessage(field, message);
    });

    this.toggleFinalMessage(form, result);
    return result;
  },
  getValidationMessage: function getValidationMessage(target) {
    var type = target.dataset.type;
    var validity = target.validity;
    var i18n = window.sellkitOptinValidationsTranslations;
    var message = '';

    if (validity.valueMissing || target.required && _.isEmpty(target.value)) {
      message = this.messageRequired;
      return message;
    }

    switch (type) {
      case 'email':
        if (validity.typeMismatch || validity.patternMismatch) {
          message = i18n.general.invalidEmail;
        }

        break;

      case 'tel':
        var isITI = target.hasAttribute('data-iti-tel');

        if (isITI) {
          var validationMessages = i18n.itiValidation;
          var inputItiInstance = window.intlTelInputGlobals.getInstance(target);
          var itiValidationCode = inputItiInstance.getValidationError();
          var areaCodeRequired = target.hasAttribute('data-iti-area-required');
          var mustBeTelType = target.getAttribute('data-iti-tel-type');
          var telType = "".concat(inputItiInstance.getNumberType());

          switch (itiValidationCode) {
            case 1:
              message = validationMessages.invalidCountryCode;
              break;

            case 2:
              message = validationMessages.tooShort;
              break;

            case 3:
              message = validationMessages.tooLong;
              break;

            case 4:
              message = areaCodeRequired ? validationMessages.areaCodeMissing : '';
              break;

            case 5:
              message = validationMessages.invalidLength;
              break;

            case -99:
              message = validationMessages.invalidGeneral;
              break;

            case 0:
            default:
              if ('all' !== mustBeTelType && telType !== mustBeTelType) {
                message = validationMessages.typeMismatch[mustBeTelType];
              }

          }

          break;
        }

        if (validity.typeMismatch || validity.patternMismatch) {
          message = i18n.general.invalidPhone;
        }

        break;

      case 'number':
        if (validity.typeMismatch || validity.patternMismatch) {
          message = i18n.general.invalidNumber;
          break;
        }

        if (validity.rangeOverflow) {
          message = i18n.general.invalidMaxValue.replace('MAX_VALUE', target.max);
          break;
        }

        if (validity.rangeUnderflow) {
          message = i18n.general.invalidMinValue.replace('MIN_VALUE', target.min);
        }

        break;
    }

    return message;
  },
  toggleFieldMessage: function toggleFieldMessage(field, message) {
    var fieldGroup = jQuery(field).closest('.sellkit-field-group');
    fieldGroup.removeClass('sellkit-field-invalid');
    fieldGroup.find('small').remove();

    if (!_.isEmpty(message)) {
      fieldGroup.addClass('sellkit-field-invalid');
      fieldGroup.append("<small class=\"sellkit-optin-text\">".concat(message, "</small>"));
    }
  },
  toggleFinalMessage: function toggleFinalMessage(form, validationResult) {
    form.parent().toggleClass('sellkit-optin-error', !validationResult);
    form.parent().toggleClass('sellkit-optin-success', validationResult);
    form.parent().find('.sellkit-optin-response').remove();

    if (true === validationResult) {
      return;
    }

    form.after("<div class=\"sellkit-optin-response\">".concat(this.messageError, "</div>"));
  },
  getCustomMessages: function getCustomMessages(form) {
    var messagesRaw = form.attr('data-messages');

    if (!_.isEmpty(messagesRaw)) {
      var messages = JSON.parse(messagesRaw);
      this.messageError = messages.error;
      this.messageRequired = messages.required;
      return;
    }

    this.messageError = window.sellkitOptinValidationsTranslations.general.errorExists;
    this.messageRequired = window.sellkitOptinValidationsTranslations.general.required;
  }
};
exports["default"] = _default;

},{}],5:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;
var _default = {
  initializeITI: function initializeITI(form) {
    var _this = this;

    var itiTelFields = form.find('input[data-iti-tel]');

    if (!itiTelFields.length) {
      return;
    }

    if (!window.itiCountry) {
      jQuery.get('https://ipwho.is/', function () {}, 'json').always(function (response) {
        window.itiCountry = response && response.country_code ? response.country_code.toLowerCase() : 'us';

        _this.setupTelFields(itiTelFields);
      });
    } else {
      this.setupTelFields(itiTelFields);
    }
  },
  setupTelFields: function setupTelFields(itiTelFields) {
    var iti = require('intl-tel-input');

    if (!iti) {
      return;
    }

    var allCountries = window.intlTelInputGlobals.getCountryData().map(function (item) {
      return item.iso2;
    });
    var numberTypes = ['FIXED_LINE', 'MOBILE', 'FIXED_LINE_OR_MOBILE', 'TOLL_FREE', 'PREMIUM_RATE', 'SHARED_COST', 'VOIP', 'PERSONAL_NUMBER', 'PAGER', 'UAN', 'VOICEMAIL'];

    _.each(itiTelFields, function (input) {
      var allowDropdown = input.hasAttribute('data-iti-allow-dropdown');
      var countriesAttr = input.getAttribute('data-iti-country-include');
      var countries = countriesAttr ? countriesAttr.split(' ') : null;
      var isCountriesSet = countries && countries.length;
      var ipDetect = input.hasAttribute('data-iti-ip-detect');
      var placeHolderAttr = input.getAttribute('data-iti-tel-type');
      var args = {
        allowDropdown: allowDropdown,
        utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.16/js/utils.min.js',
        separateDialCode: true,
        placeholderNumberType: 'all' === placeHolderAttr ? 'MOBILE' : numberTypes[+placeHolderAttr],
        onlyCountries: isCountriesSet ? countries : allCountries,
        initialCountry: '',
        geoIpLookup: null
      };

      if (ipDetect) {
        args.initialCountry = isCountriesSet && !countries.includes(window.itiCountry) ? countries[0] : 'auto';

        args.geoIpLookup = function (success) {
          return success(window.itiCountry);
        };
      }

      iti(input, args);
    });
  },
  fixTelBeforeSubmit: function fixTelBeforeSubmit(form) {
    var itiTelFields = form.find('input[data-iti-tel]');

    _.each(itiTelFields, function (input) {
      var internationalize = input.hasAttribute('data-iti-internationalize');

      if (internationalize) {
        var itiInstance = window.intlTelInputGlobals.getInstance(input);
        input.value = itiInstance.getNumber();
      }
    });
  }
};
exports["default"] = _default;

},{"intl-tel-input":19}],6:[function(require,module,exports){
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

},{}],7:[function(require,module,exports){
function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

module.exports = _defineProperty;
},{}],8:[function(require,module,exports){
function _interopRequireDefault(obj) {
  return obj && obj.__esModule ? obj : {
    "default": obj
  };
}

module.exports = _interopRequireDefault;
},{}],9:[function(require,module,exports){
'use strict';

function _interopDefault (ex) { return (ex && (typeof ex === 'object') && 'default' in ex) ? ex['default'] : ex; }

var postfix = _interopDefault(require('@tannin/postfix'));
var evaluate = _interopDefault(require('@tannin/evaluate'));

/**
 * Given a C expression, returns a function which can be called to evaluate its
 * result.
 *
 * @example
 *
 * ```js
 * import compile from '@tannin/compile';
 *
 * const evaluate = compile( 'n > 1' );
 *
 * evaluate( { n: 2 } );
 * // ⇒ true
 * ```
 *
 * @param {string} expression C expression.
 *
 * @return {(variables?:{[variable:string]:*})=>*} Compiled evaluator.
 */
function compile( expression ) {
	var terms = postfix( expression );

	return function( variables ) {
		return evaluate( terms, variables );
	};
}

module.exports = compile;

},{"@tannin/evaluate":10,"@tannin/postfix":12}],10:[function(require,module,exports){
'use strict';

/**
 * Operator callback functions.
 *
 * @type {Object}
 */
var OPERATORS = {
	'!': function( a ) {
		return ! a;
	},
	'*': function( a, b ) {
		return a * b;
	},
	'/': function( a, b ) {
		return a / b;
	},
	'%': function( a, b ) {
		return a % b;
	},
	'+': function( a, b ) {
		return a + b;
	},
	'-': function( a, b ) {
		return a - b;
	},
	'<': function( a, b ) {
		return a < b;
	},
	'<=': function( a, b ) {
		return a <= b;
	},
	'>': function( a, b ) {
		return a > b;
	},
	'>=': function( a, b ) {
		return a >= b;
	},
	'==': function( a, b ) {
		return a === b;
	},
	'!=': function( a, b ) {
		return a !== b;
	},
	'&&': function( a, b ) {
		return a && b;
	},
	'||': function( a, b ) {
		return a || b;
	},
	'?:': function( a, b, c ) {
		if ( a ) {
			throw b;
		}

		return c;
	},
};

/**
 * Given an array of postfix terms and operand variables, returns the result of
 * the postfix evaluation.
 *
 * @example
 *
 * ```js
 * import evaluate from '@tannin/evaluate';
 *
 * // 3 + 4 * 5 / 6 ⇒ '3 4 5 * 6 / +'
 * const terms = [ '3', '4', '5', '*', '6', '/', '+' ];
 *
 * evaluate( terms, {} );
 * // ⇒ 6.333333333333334
 * ```
 *
 * @param {string[]} postfix   Postfix terms.
 * @param {Object}   variables Operand variables.
 *
 * @return {*} Result of evaluation.
 */
function evaluate( postfix, variables ) {
	var stack = [],
		i, j, args, getOperatorResult, term, value;

	for ( i = 0; i < postfix.length; i++ ) {
		term = postfix[ i ];

		getOperatorResult = OPERATORS[ term ];
		if ( getOperatorResult ) {
			// Pop from stack by number of function arguments.
			j = getOperatorResult.length;
			args = Array( j );
			while ( j-- ) {
				args[ j ] = stack.pop();
			}

			try {
				value = getOperatorResult.apply( null, args );
			} catch ( earlyReturn ) {
				return earlyReturn;
			}
		} else if ( variables.hasOwnProperty( term ) ) {
			value = variables[ term ];
		} else {
			value = +term;
		}

		stack.push( value );
	}

	return stack[ 0 ];
}

module.exports = evaluate;

},{}],11:[function(require,module,exports){
'use strict';

function _interopDefault (ex) { return (ex && (typeof ex === 'object') && 'default' in ex) ? ex['default'] : ex; }

var compile = _interopDefault(require('@tannin/compile'));

/**
 * Given a C expression, returns a function which, when called with a value,
 * evaluates the result with the value assumed to be the "n" variable of the
 * expression. The result will be coerced to its numeric equivalent.
 *
 * @param {string} expression C expression.
 *
 * @return {Function} Evaluator function.
 */
function pluralForms( expression ) {
	var evaluate = compile( expression );

	return function( n ) {
		return +evaluate( { n: n } );
	};
}

module.exports = pluralForms;

},{"@tannin/compile":9}],12:[function(require,module,exports){
'use strict';

var PRECEDENCE, OPENERS, TERMINATORS, PATTERN;

/**
 * Operator precedence mapping.
 *
 * @type {Object}
 */
PRECEDENCE = {
	'(': 9,
	'!': 8,
	'*': 7,
	'/': 7,
	'%': 7,
	'+': 6,
	'-': 6,
	'<': 5,
	'<=': 5,
	'>': 5,
	'>=': 5,
	'==': 4,
	'!=': 4,
	'&&': 3,
	'||': 2,
	'?': 1,
	'?:': 1,
};

/**
 * Characters which signal pair opening, to be terminated by terminators.
 *
 * @type {string[]}
 */
OPENERS = [ '(', '?' ];

/**
 * Characters which signal pair termination, the value an array with the
 * opener as its first member. The second member is an optional operator
 * replacement to push to the stack.
 *
 * @type {string[]}
 */
TERMINATORS = {
	')': [ '(' ],
	':': [ '?', '?:' ],
};

/**
 * Pattern matching operators and openers.
 *
 * @type {RegExp}
 */
PATTERN = /<=|>=|==|!=|&&|\|\||\?:|\(|!|\*|\/|%|\+|-|<|>|\?|\)|:/;

/**
 * Given a C expression, returns the equivalent postfix (Reverse Polish)
 * notation terms as an array.
 *
 * If a postfix string is desired, simply `.join( ' ' )` the result.
 *
 * @example
 *
 * ```js
 * import postfix from '@tannin/postfix';
 *
 * postfix( 'n > 1' );
 * // ⇒ [ 'n', '1', '>' ]
 * ```
 *
 * @param {string} expression C expression.
 *
 * @return {string[]} Postfix terms.
 */
function postfix( expression ) {
	var terms = [],
		stack = [],
		match, operator, term, element;

	while ( ( match = expression.match( PATTERN ) ) ) {
		operator = match[ 0 ];

		// Term is the string preceding the operator match. It may contain
		// whitespace, and may be empty (if operator is at beginning).
		term = expression.substr( 0, match.index ).trim();
		if ( term ) {
			terms.push( term );
		}

		while ( ( element = stack.pop() ) ) {
			if ( TERMINATORS[ operator ] ) {
				if ( TERMINATORS[ operator ][ 0 ] === element ) {
					// Substitution works here under assumption that because
					// the assigned operator will no longer be a terminator, it
					// will be pushed to the stack during the condition below.
					operator = TERMINATORS[ operator ][ 1 ] || operator;
					break;
				}
			} else if ( OPENERS.indexOf( element ) >= 0 || PRECEDENCE[ element ] < PRECEDENCE[ operator ] ) {
				// Push to stack if either an opener or when pop reveals an
				// element of lower precedence.
				stack.push( element );
				break;
			}

			// For each popped from stack, push to terms.
			terms.push( element );
		}

		if ( ! TERMINATORS[ operator ] ) {
			stack.push( operator );
		}

		// Slice matched fragment from expression to continue match.
		expression = expression.substr( match.index + operator.length );
	}

	// Push remainder of operand, if exists, to terms.
	expression = expression.trim();
	if ( expression ) {
		terms.push( expression );
	}

	// Pop remaining items from stack into terms.
	return terms.concat( stack.reverse() );
}

module.exports = postfix;

},{}],13:[function(require,module,exports){
"use strict";

var _interopRequireDefault = require("@babel/runtime/helpers/interopRequireDefault");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.createI18n = void 0;

var _defineProperty2 = _interopRequireDefault(require("@babel/runtime/helpers/defineProperty"));

var _tannin = _interopRequireDefault(require("tannin"));

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * @typedef {Record<string,any>} LocaleData
 */

/**
 * Default locale data to use for Tannin domain when not otherwise provided.
 * Assumes an English plural forms expression.
 *
 * @type {LocaleData}
 */
var DEFAULT_LOCALE_DATA = {
  '': {
    /** @param {number} n */
    plural_forms: function plural_forms(n) {
      return n === 1 ? 0 : 1;
    }
  }
};
/**
 * An i18n instance
 *
 * @typedef {Object} I18n
 * @property {Function} setLocaleData Merges locale data into the Tannin instance by domain. Accepts data in a
 *                                    Jed-formatted JSON object shape.
 * @property {Function} __            Retrieve the translation of text.
 * @property {Function} _x            Retrieve translated string with gettext context.
 * @property {Function} _n            Translates and retrieves the singular or plural form based on the supplied
 *                                    number.
 * @property {Function} _nx           Translates and retrieves the singular or plural form based on the supplied
 *                                    number, with gettext context.
 * @property {Function} isRTL         Check if current locale is RTL.
 */

/**
 * Create an i18n instance
 *
 * @param {LocaleData} [initialData]    Locale data configuration.
 * @param {string}     [initialDomain]  Domain for which configuration applies.
 * @return {I18n}                       I18n instance
 */

var createI18n = function createI18n(initialData, initialDomain) {
  /**
   * The underlying instance of Tannin to which exported functions interface.
   *
   * @type {Tannin}
   */
  var tannin = new _tannin.default({});
  /**
   * Merges locale data into the Tannin instance by domain. Accepts data in a
   * Jed-formatted JSON object shape.
   *
   * @see http://messageformat.github.io/Jed/
   *
   * @param {LocaleData} [data]   Locale data configuration.
   * @param {string}     [domain] Domain for which configuration applies.
   */

  var setLocaleData = function setLocaleData(data) {
    var domain = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'default';
    tannin.data[domain] = _objectSpread({}, DEFAULT_LOCALE_DATA, {}, tannin.data[domain], {}, data); // Populate default domain configuration (supported locale date which omits
    // a plural forms expression).

    tannin.data[domain][''] = _objectSpread({}, DEFAULT_LOCALE_DATA[''], {}, tannin.data[domain]['']);
  };
  /**
   * Wrapper for Tannin's `dcnpgettext`. Populates default locale data if not
   * otherwise previously assigned.
   *
   * @param {string|undefined} domain   Domain to retrieve the translated text.
   * @param {string|undefined} context  Context information for the translators.
   * @param {string}           single   Text to translate if non-plural. Used as
   *                                    fallback return value on a caught error.
   * @param {string}           [plural] The text to be used if the number is
   *                                    plural.
   * @param {number}           [number] The number to compare against to use
   *                                    either the singular or plural form.
   *
   * @return {string} The translated string.
   */


  var dcnpgettext = function dcnpgettext() {
    var domain = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'default';
    var context = arguments.length > 1 ? arguments[1] : undefined;
    var single = arguments.length > 2 ? arguments[2] : undefined;
    var plural = arguments.length > 3 ? arguments[3] : undefined;
    var number = arguments.length > 4 ? arguments[4] : undefined;

    if (!tannin.data[domain]) {
      setLocaleData(undefined, domain);
    }

    return tannin.dcnpgettext(domain, context, single, plural, number);
  };
  /**
   * Retrieve the translation of text.
   *
   * @see https://developer.wordpress.org/reference/functions/__/
   *
   * @param {string} text     Text to translate.
   * @param {string} [domain] Domain to retrieve the translated text.
   *
   * @return {string} Translated text.
   */


  var __ = function __(text, domain) {
    return dcnpgettext(domain, undefined, text);
  };
  /**
   * Retrieve translated string with gettext context.
   *
   * @see https://developer.wordpress.org/reference/functions/_x/
   *
   * @param {string} text     Text to translate.
   * @param {string} context  Context information for the translators.
   * @param {string} [domain] Domain to retrieve the translated text.
   *
   * @return {string} Translated context string without pipe.
   */


  var _x = function _x(text, context, domain) {
    return dcnpgettext(domain, context, text);
  };
  /**
   * Translates and retrieves the singular or plural form based on the supplied
   * number.
   *
   * @see https://developer.wordpress.org/reference/functions/_n/
   *
   * @param {string} single   The text to be used if the number is singular.
   * @param {string} plural   The text to be used if the number is plural.
   * @param {number} number   The number to compare against to use either the
   *                          singular or plural form.
   * @param {string} [domain] Domain to retrieve the translated text.
   *
   * @return {string} The translated singular or plural form.
   */


  var _n = function _n(single, plural, number, domain) {
    return dcnpgettext(domain, undefined, single, plural, number);
  };
  /**
   * Translates and retrieves the singular or plural form based on the supplied
   * number, with gettext context.
   *
   * @see https://developer.wordpress.org/reference/functions/_nx/
   *
   * @param {string} single   The text to be used if the number is singular.
   * @param {string} plural   The text to be used if the number is plural.
   * @param {number} number   The number to compare against to use either the
   *                          singular or plural form.
   * @param {string} context  Context information for the translators.
   * @param {string} [domain] Domain to retrieve the translated text.
   *
   * @return {string} The translated singular or plural form.
   */


  var _nx = function _nx(single, plural, number, context, domain) {
    return dcnpgettext(domain, context, single, plural, number);
  };
  /**
   * Check if current locale is RTL.
   *
   * **RTL (Right To Left)** is a locale property indicating that text is written from right to left.
   * For example, the `he` locale (for Hebrew) specifies right-to-left. Arabic (ar) is another common
   * language written RTL. The opposite of RTL, LTR (Left To Right) is used in other languages,
   * including English (`en`, `en-US`, `en-GB`, etc.), Spanish (`es`), and French (`fr`).
   *
   * @return {boolean} Whether locale is RTL.
   */


  var isRTL = function isRTL() {
    return 'rtl' === _x('ltr', 'text direction');
  };

  if (initialData) {
    setLocaleData(initialData, initialDomain);
  }

  return {
    setLocaleData: setLocaleData,
    __: __,
    _x: _x,
    _n: _n,
    _nx: _nx,
    isRTL: isRTL
  };
};

exports.createI18n = createI18n;

},{"@babel/runtime/helpers/defineProperty":7,"@babel/runtime/helpers/interopRequireDefault":8,"tannin":22}],14:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.isRTL = exports._nx = exports._n = exports._x = exports.__ = exports.setLocaleData = void 0;

var _createI18n = require("./create-i18n");

/**
 * Internal dependencies
 */
var i18n = (0, _createI18n.createI18n)();
/*
 * Comments in this file are duplicated from ./i18n due to
 * https://github.com/WordPress/gutenberg/pull/20318#issuecomment-590837722
 */

/**
 * @typedef {import('./create-i18n').LocaleData} LocaleData
 */

/**
 * Merges locale data into the Tannin instance by domain. Accepts data in a
 * Jed-formatted JSON object shape.
 *
 * @see http://messageformat.github.io/Jed/
 *
 * @param {LocaleData} [data]   Locale data configuration.
 * @param {string}     [domain] Domain for which configuration applies.
 */

var setLocaleData = i18n.setLocaleData.bind(i18n);
/**
 * Retrieve the translation of text.
 *
 * @see https://developer.wordpress.org/reference/functions/__/
 *
 * @param {string} text     Text to translate.
 * @param {string} [domain] Domain to retrieve the translated text.
 *
 * @return {string} Translated text.
 */

exports.setLocaleData = setLocaleData;

var __ = i18n.__.bind(i18n);
/**
 * Retrieve translated string with gettext context.
 *
 * @see https://developer.wordpress.org/reference/functions/_x/
 *
 * @param {string} text     Text to translate.
 * @param {string} context  Context information for the translators.
 * @param {string} [domain] Domain to retrieve the translated text.
 *
 * @return {string} Translated context string without pipe.
 */


exports.__ = __;

var _x = i18n._x.bind(i18n);
/**
 * Translates and retrieves the singular or plural form based on the supplied
 * number.
 *
 * @see https://developer.wordpress.org/reference/functions/_n/
 *
 * @param {string} single   The text to be used if the number is singular.
 * @param {string} plural   The text to be used if the number is plural.
 * @param {number} number   The number to compare against to use either the
 *                          singular or plural form.
 * @param {string} [domain] Domain to retrieve the translated text.
 *
 * @return {string} The translated singular or plural form.
 */


exports._x = _x;

var _n = i18n._n.bind(i18n);
/**
 * Translates and retrieves the singular or plural form based on the supplied
 * number, with gettext context.
 *
 * @see https://developer.wordpress.org/reference/functions/_nx/
 *
 * @param {string} single   The text to be used if the number is singular.
 * @param {string} plural   The text to be used if the number is plural.
 * @param {number} number   The number to compare against to use either the
 *                          singular or plural form.
 * @param {string} context  Context information for the translators.
 * @param {string} [domain] Domain to retrieve the translated text.
 *
 * @return {string} The translated singular or plural form.
 */


exports._n = _n;

var _nx = i18n._nx.bind(i18n);
/**
 * Check if current locale is RTL.
 *
 * **RTL (Right To Left)** is a locale property indicating that text is written from right to left.
 * For example, the `he` locale (for Hebrew) specifies right-to-left. Arabic (ar) is another common
 * language written RTL. The opposite of RTL, LTR (Left To Right) is used in other languages,
 * including English (`en`, `en-US`, `en-GB`, etc.), Spanish (`es`), and French (`fr`).
 *
 * @return {boolean} Whether locale is RTL.
 */


exports._nx = _nx;
var isRTL = i18n.isRTL.bind(i18n);
exports.isRTL = isRTL;

},{"./create-i18n":13}],15:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
var _exportNames = {
  sprintf: true,
  setLocaleData: true,
  __: true,
  _x: true,
  _n: true,
  _nx: true,
  isRTL: true
};
Object.defineProperty(exports, "sprintf", {
  enumerable: true,
  get: function get() {
    return _sprintf.sprintf;
  }
});
Object.defineProperty(exports, "setLocaleData", {
  enumerable: true,
  get: function get() {
    return _defaultI18n.setLocaleData;
  }
});
Object.defineProperty(exports, "__", {
  enumerable: true,
  get: function get() {
    return _defaultI18n.__;
  }
});
Object.defineProperty(exports, "_x", {
  enumerable: true,
  get: function get() {
    return _defaultI18n._x;
  }
});
Object.defineProperty(exports, "_n", {
  enumerable: true,
  get: function get() {
    return _defaultI18n._n;
  }
});
Object.defineProperty(exports, "_nx", {
  enumerable: true,
  get: function get() {
    return _defaultI18n._nx;
  }
});
Object.defineProperty(exports, "isRTL", {
  enumerable: true,
  get: function get() {
    return _defaultI18n.isRTL;
  }
});

var _sprintf = require("./sprintf");

var _createI18n = require("./create-i18n");

Object.keys(_createI18n).forEach(function (key) {
  if (key === "default" || key === "__esModule") return;
  if (Object.prototype.hasOwnProperty.call(_exportNames, key)) return;
  Object.defineProperty(exports, key, {
    enumerable: true,
    get: function get() {
      return _createI18n[key];
    }
  });
});

var _defaultI18n = require("./default-i18n");

},{"./create-i18n":13,"./default-i18n":14,"./sprintf":16}],16:[function(require,module,exports){
"use strict";

var _interopRequireDefault = require("@babel/runtime/helpers/interopRequireDefault");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.sprintf = sprintf;

var _memize = _interopRequireDefault(require("memize"));

var _sprintfJs = _interopRequireDefault(require("sprintf-js"));

/**
 * External dependencies
 */

/**
 * Log to console, once per message; or more precisely, per referentially equal
 * argument set. Because Jed throws errors, we log these to the console instead
 * to avoid crashing the application.
 *
 * @param {...*} args Arguments to pass to `console.error`
 */
var logErrorOnce = (0, _memize.default)(console.error); // eslint-disable-line no-console

/**
 * Returns a formatted string. If an error occurs in applying the format, the
 * original format string is returned.
 *
 * @param {string}    format The format of the string to generate.
 * @param {...*} args Arguments to apply to the format.
 *
 * @see http://www.diveintojavascript.com/projects/javascript-sprintf
 *
 * @return {string} The formatted string.
 */

function sprintf(format) {
  try {
    for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      args[_key - 1] = arguments[_key];
    }

    return _sprintfJs.default.sprintf.apply(_sprintfJs.default, [format].concat(args));
  } catch (error) {
    logErrorOnce('sprintf error: \n\n' + error.toString());
    return format;
  }
}

},{"@babel/runtime/helpers/interopRequireDefault":8,"memize":20,"sprintf-js":17}],17:[function(require,module,exports){
/* global window, exports, define */

!function() {
    'use strict'

    var re = {
        not_string: /[^s]/,
        not_bool: /[^t]/,
        not_type: /[^T]/,
        not_primitive: /[^v]/,
        number: /[diefg]/,
        numeric_arg: /[bcdiefguxX]/,
        json: /[j]/,
        not_json: /[^j]/,
        text: /^[^\x25]+/,
        modulo: /^\x25{2}/,
        placeholder: /^\x25(?:([1-9]\d*)\$|\(([^)]+)\))?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-gijostTuvxX])/,
        key: /^([a-z_][a-z_\d]*)/i,
        key_access: /^\.([a-z_][a-z_\d]*)/i,
        index_access: /^\[(\d+)\]/,
        sign: /^[+-]/
    }

    function sprintf(key) {
        // `arguments` is not an array, but should be fine for this call
        return sprintf_format(sprintf_parse(key), arguments)
    }

    function vsprintf(fmt, argv) {
        return sprintf.apply(null, [fmt].concat(argv || []))
    }

    function sprintf_format(parse_tree, argv) {
        var cursor = 1, tree_length = parse_tree.length, arg, output = '', i, k, ph, pad, pad_character, pad_length, is_positive, sign
        for (i = 0; i < tree_length; i++) {
            if (typeof parse_tree[i] === 'string') {
                output += parse_tree[i]
            }
            else if (typeof parse_tree[i] === 'object') {
                ph = parse_tree[i] // convenience purposes only
                if (ph.keys) { // keyword argument
                    arg = argv[cursor]
                    for (k = 0; k < ph.keys.length; k++) {
                        if (arg == undefined) {
                            throw new Error(sprintf('[sprintf] Cannot access property "%s" of undefined value "%s"', ph.keys[k], ph.keys[k-1]))
                        }
                        arg = arg[ph.keys[k]]
                    }
                }
                else if (ph.param_no) { // positional argument (explicit)
                    arg = argv[ph.param_no]
                }
                else { // positional argument (implicit)
                    arg = argv[cursor++]
                }

                if (re.not_type.test(ph.type) && re.not_primitive.test(ph.type) && arg instanceof Function) {
                    arg = arg()
                }

                if (re.numeric_arg.test(ph.type) && (typeof arg !== 'number' && isNaN(arg))) {
                    throw new TypeError(sprintf('[sprintf] expecting number but found %T', arg))
                }

                if (re.number.test(ph.type)) {
                    is_positive = arg >= 0
                }

                switch (ph.type) {
                    case 'b':
                        arg = parseInt(arg, 10).toString(2)
                        break
                    case 'c':
                        arg = String.fromCharCode(parseInt(arg, 10))
                        break
                    case 'd':
                    case 'i':
                        arg = parseInt(arg, 10)
                        break
                    case 'j':
                        arg = JSON.stringify(arg, null, ph.width ? parseInt(ph.width) : 0)
                        break
                    case 'e':
                        arg = ph.precision ? parseFloat(arg).toExponential(ph.precision) : parseFloat(arg).toExponential()
                        break
                    case 'f':
                        arg = ph.precision ? parseFloat(arg).toFixed(ph.precision) : parseFloat(arg)
                        break
                    case 'g':
                        arg = ph.precision ? String(Number(arg.toPrecision(ph.precision))) : parseFloat(arg)
                        break
                    case 'o':
                        arg = (parseInt(arg, 10) >>> 0).toString(8)
                        break
                    case 's':
                        arg = String(arg)
                        arg = (ph.precision ? arg.substring(0, ph.precision) : arg)
                        break
                    case 't':
                        arg = String(!!arg)
                        arg = (ph.precision ? arg.substring(0, ph.precision) : arg)
                        break
                    case 'T':
                        arg = Object.prototype.toString.call(arg).slice(8, -1).toLowerCase()
                        arg = (ph.precision ? arg.substring(0, ph.precision) : arg)
                        break
                    case 'u':
                        arg = parseInt(arg, 10) >>> 0
                        break
                    case 'v':
                        arg = arg.valueOf()
                        arg = (ph.precision ? arg.substring(0, ph.precision) : arg)
                        break
                    case 'x':
                        arg = (parseInt(arg, 10) >>> 0).toString(16)
                        break
                    case 'X':
                        arg = (parseInt(arg, 10) >>> 0).toString(16).toUpperCase()
                        break
                }
                if (re.json.test(ph.type)) {
                    output += arg
                }
                else {
                    if (re.number.test(ph.type) && (!is_positive || ph.sign)) {
                        sign = is_positive ? '+' : '-'
                        arg = arg.toString().replace(re.sign, '')
                    }
                    else {
                        sign = ''
                    }
                    pad_character = ph.pad_char ? ph.pad_char === '0' ? '0' : ph.pad_char.charAt(1) : ' '
                    pad_length = ph.width - (sign + arg).length
                    pad = ph.width ? (pad_length > 0 ? pad_character.repeat(pad_length) : '') : ''
                    output += ph.align ? sign + arg + pad : (pad_character === '0' ? sign + pad + arg : pad + sign + arg)
                }
            }
        }
        return output
    }

    var sprintf_cache = Object.create(null)

    function sprintf_parse(fmt) {
        if (sprintf_cache[fmt]) {
            return sprintf_cache[fmt]
        }

        var _fmt = fmt, match, parse_tree = [], arg_names = 0
        while (_fmt) {
            if ((match = re.text.exec(_fmt)) !== null) {
                parse_tree.push(match[0])
            }
            else if ((match = re.modulo.exec(_fmt)) !== null) {
                parse_tree.push('%')
            }
            else if ((match = re.placeholder.exec(_fmt)) !== null) {
                if (match[2]) {
                    arg_names |= 1
                    var field_list = [], replacement_field = match[2], field_match = []
                    if ((field_match = re.key.exec(replacement_field)) !== null) {
                        field_list.push(field_match[1])
                        while ((replacement_field = replacement_field.substring(field_match[0].length)) !== '') {
                            if ((field_match = re.key_access.exec(replacement_field)) !== null) {
                                field_list.push(field_match[1])
                            }
                            else if ((field_match = re.index_access.exec(replacement_field)) !== null) {
                                field_list.push(field_match[1])
                            }
                            else {
                                throw new SyntaxError('[sprintf] failed to parse named argument key')
                            }
                        }
                    }
                    else {
                        throw new SyntaxError('[sprintf] failed to parse named argument key')
                    }
                    match[2] = field_list
                }
                else {
                    arg_names |= 2
                }
                if (arg_names === 3) {
                    throw new Error('[sprintf] mixing positional and named placeholders is not (yet) supported')
                }

                parse_tree.push(
                    {
                        placeholder: match[0],
                        param_no:    match[1],
                        keys:        match[2],
                        sign:        match[3],
                        pad_char:    match[4],
                        align:       match[5],
                        width:       match[6],
                        precision:   match[7],
                        type:        match[8]
                    }
                )
            }
            else {
                throw new SyntaxError('[sprintf] unexpected placeholder')
            }
            _fmt = _fmt.substring(match[0].length)
        }
        return sprintf_cache[fmt] = parse_tree
    }

    /**
     * export to either browser or node.js
     */
    /* eslint-disable quote-props */
    if (typeof exports !== 'undefined') {
        exports['sprintf'] = sprintf
        exports['vsprintf'] = vsprintf
    }
    if (typeof window !== 'undefined') {
        window['sprintf'] = sprintf
        window['vsprintf'] = vsprintf

        if (typeof define === 'function' && define['amd']) {
            define(function() {
                return {
                    'sprintf': sprintf,
                    'vsprintf': vsprintf
                }
            })
        }
    }
    /* eslint-enable quote-props */
}(); // eslint-disable-line

},{}],18:[function(require,module,exports){
/*
 * International Telephone Input v17.0.16
 * https://github.com/jackocnr/intl-tel-input.git
 * Licensed under the MIT license
 */

// wrap in UMD
(function(factory) {
    if (typeof module === "object" && module.exports) module.exports = factory(); else window.intlTelInput = factory();
})(function(undefined) {
    "use strict";
    return function() {
        // Array of country objects for the flag dropdown.
        // Here is the criteria for the plugin to support a given country/territory
        // - It has an iso2 code: https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
        // - It has it's own country calling code (it is not a sub-region of another country): https://en.wikipedia.org/wiki/List_of_country_calling_codes
        // - It has a flag in the region-flags project: https://github.com/behdad/region-flags/tree/gh-pages/png
        // - It is supported by libphonenumber (it must be listed on this page): https://github.com/googlei18n/libphonenumber/blob/master/resources/ShortNumberMetadata.xml
        // Each country array has the following information:
        // [
        //    Country name,
        //    iso2 code,
        //    International dial code,
        //    Order (if >1 country with same dial code),
        //    Area codes
        // ]
        var allCountries = [ [ "Afghanistan (‫افغانستان‬‎)", "af", "93" ], [ "Albania (Shqipëri)", "al", "355" ], [ "Algeria (‫الجزائر‬‎)", "dz", "213" ], [ "American Samoa", "as", "1", 5, [ "684" ] ], [ "Andorra", "ad", "376" ], [ "Angola", "ao", "244" ], [ "Anguilla", "ai", "1", 6, [ "264" ] ], [ "Antigua and Barbuda", "ag", "1", 7, [ "268" ] ], [ "Argentina", "ar", "54" ], [ "Armenia (Հայաստան)", "am", "374" ], [ "Aruba", "aw", "297" ], [ "Ascension Island", "ac", "247" ], [ "Australia", "au", "61", 0 ], [ "Austria (Österreich)", "at", "43" ], [ "Azerbaijan (Azərbaycan)", "az", "994" ], [ "Bahamas", "bs", "1", 8, [ "242" ] ], [ "Bahrain (‫البحرين‬‎)", "bh", "973" ], [ "Bangladesh (বাংলাদেশ)", "bd", "880" ], [ "Barbados", "bb", "1", 9, [ "246" ] ], [ "Belarus (Беларусь)", "by", "375" ], [ "Belgium (België)", "be", "32" ], [ "Belize", "bz", "501" ], [ "Benin (Bénin)", "bj", "229" ], [ "Bermuda", "bm", "1", 10, [ "441" ] ], [ "Bhutan (འབྲུག)", "bt", "975" ], [ "Bolivia", "bo", "591" ], [ "Bosnia and Herzegovina (Босна и Херцеговина)", "ba", "387" ], [ "Botswana", "bw", "267" ], [ "Brazil (Brasil)", "br", "55" ], [ "British Indian Ocean Territory", "io", "246" ], [ "British Virgin Islands", "vg", "1", 11, [ "284" ] ], [ "Brunei", "bn", "673" ], [ "Bulgaria (България)", "bg", "359" ], [ "Burkina Faso", "bf", "226" ], [ "Burundi (Uburundi)", "bi", "257" ], [ "Cambodia (កម្ពុជា)", "kh", "855" ], [ "Cameroon (Cameroun)", "cm", "237" ], [ "Canada", "ca", "1", 1, [ "204", "226", "236", "249", "250", "289", "306", "343", "365", "387", "403", "416", "418", "431", "437", "438", "450", "506", "514", "519", "548", "579", "581", "587", "604", "613", "639", "647", "672", "705", "709", "742", "778", "780", "782", "807", "819", "825", "867", "873", "902", "905" ] ], [ "Cape Verde (Kabu Verdi)", "cv", "238" ], [ "Caribbean Netherlands", "bq", "599", 1, [ "3", "4", "7" ] ], [ "Cayman Islands", "ky", "1", 12, [ "345" ] ], [ "Central African Republic (République centrafricaine)", "cf", "236" ], [ "Chad (Tchad)", "td", "235" ], [ "Chile", "cl", "56" ], [ "China (中国)", "cn", "86" ], [ "Christmas Island", "cx", "61", 2, [ "89164" ] ], [ "Cocos (Keeling) Islands", "cc", "61", 1, [ "89162" ] ], [ "Colombia", "co", "57" ], [ "Comoros (‫جزر القمر‬‎)", "km", "269" ], [ "Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo)", "cd", "243" ], [ "Congo (Republic) (Congo-Brazzaville)", "cg", "242" ], [ "Cook Islands", "ck", "682" ], [ "Costa Rica", "cr", "506" ], [ "Côte d’Ivoire", "ci", "225" ], [ "Croatia (Hrvatska)", "hr", "385" ], [ "Cuba", "cu", "53" ], [ "Curaçao", "cw", "599", 0 ], [ "Cyprus (Κύπρος)", "cy", "357" ], [ "Czech Republic (Česká republika)", "cz", "420" ], [ "Denmark (Danmark)", "dk", "45" ], [ "Djibouti", "dj", "253" ], [ "Dominica", "dm", "1", 13, [ "767" ] ], [ "Dominican Republic (República Dominicana)", "do", "1", 2, [ "809", "829", "849" ] ], [ "Ecuador", "ec", "593" ], [ "Egypt (‫مصر‬‎)", "eg", "20" ], [ "El Salvador", "sv", "503" ], [ "Equatorial Guinea (Guinea Ecuatorial)", "gq", "240" ], [ "Eritrea", "er", "291" ], [ "Estonia (Eesti)", "ee", "372" ], [ "Eswatini", "sz", "268" ], [ "Ethiopia", "et", "251" ], [ "Falkland Islands (Islas Malvinas)", "fk", "500" ], [ "Faroe Islands (Føroyar)", "fo", "298" ], [ "Fiji", "fj", "679" ], [ "Finland (Suomi)", "fi", "358", 0 ], [ "France", "fr", "33" ], [ "French Guiana (Guyane française)", "gf", "594" ], [ "French Polynesia (Polynésie française)", "pf", "689" ], [ "Gabon", "ga", "241" ], [ "Gambia", "gm", "220" ], [ "Georgia (საქართველო)", "ge", "995" ], [ "Germany (Deutschland)", "de", "49" ], [ "Ghana (Gaana)", "gh", "233" ], [ "Gibraltar", "gi", "350" ], [ "Greece (Ελλάδα)", "gr", "30" ], [ "Greenland (Kalaallit Nunaat)", "gl", "299" ], [ "Grenada", "gd", "1", 14, [ "473" ] ], [ "Guadeloupe", "gp", "590", 0 ], [ "Guam", "gu", "1", 15, [ "671" ] ], [ "Guatemala", "gt", "502" ], [ "Guernsey", "gg", "44", 1, [ "1481", "7781", "7839", "7911" ] ], [ "Guinea (Guinée)", "gn", "224" ], [ "Guinea-Bissau (Guiné Bissau)", "gw", "245" ], [ "Guyana", "gy", "592" ], [ "Haiti", "ht", "509" ], [ "Honduras", "hn", "504" ], [ "Hong Kong (香港)", "hk", "852" ], [ "Hungary (Magyarország)", "hu", "36" ], [ "Iceland (Ísland)", "is", "354" ], [ "India (भारत)", "in", "91" ], [ "Indonesia", "id", "62" ], [ "Iran (‫ایران‬‎)", "ir", "98" ], [ "Iraq (‫العراق‬‎)", "iq", "964" ], [ "Ireland", "ie", "353" ], [ "Isle of Man", "im", "44", 2, [ "1624", "74576", "7524", "7924", "7624" ] ], [ "Israel (‫ישראל‬‎)", "il", "972" ], [ "Italy (Italia)", "it", "39", 0 ], [ "Jamaica", "jm", "1", 4, [ "876", "658" ] ], [ "Japan (日本)", "jp", "81" ], [ "Jersey", "je", "44", 3, [ "1534", "7509", "7700", "7797", "7829", "7937" ] ], [ "Jordan (‫الأردن‬‎)", "jo", "962" ], [ "Kazakhstan (Казахстан)", "kz", "7", 1, [ "33", "7" ] ], [ "Kenya", "ke", "254" ], [ "Kiribati", "ki", "686" ], [ "Kosovo", "xk", "383" ], [ "Kuwait (‫الكويت‬‎)", "kw", "965" ], [ "Kyrgyzstan (Кыргызстан)", "kg", "996" ], [ "Laos (ລາວ)", "la", "856" ], [ "Latvia (Latvija)", "lv", "371" ], [ "Lebanon (‫لبنان‬‎)", "lb", "961" ], [ "Lesotho", "ls", "266" ], [ "Liberia", "lr", "231" ], [ "Libya (‫ليبيا‬‎)", "ly", "218" ], [ "Liechtenstein", "li", "423" ], [ "Lithuania (Lietuva)", "lt", "370" ], [ "Luxembourg", "lu", "352" ], [ "Macau (澳門)", "mo", "853" ], [ "North Macedonia (Македонија)", "mk", "389" ], [ "Madagascar (Madagasikara)", "mg", "261" ], [ "Malawi", "mw", "265" ], [ "Malaysia", "my", "60" ], [ "Maldives", "mv", "960" ], [ "Mali", "ml", "223" ], [ "Malta", "mt", "356" ], [ "Marshall Islands", "mh", "692" ], [ "Martinique", "mq", "596" ], [ "Mauritania (‫موريتانيا‬‎)", "mr", "222" ], [ "Mauritius (Moris)", "mu", "230" ], [ "Mayotte", "yt", "262", 1, [ "269", "639" ] ], [ "Mexico (México)", "mx", "52" ], [ "Micronesia", "fm", "691" ], [ "Moldova (Republica Moldova)", "md", "373" ], [ "Monaco", "mc", "377" ], [ "Mongolia (Монгол)", "mn", "976" ], [ "Montenegro (Crna Gora)", "me", "382" ], [ "Montserrat", "ms", "1", 16, [ "664" ] ], [ "Morocco (‫المغرب‬‎)", "ma", "212", 0 ], [ "Mozambique (Moçambique)", "mz", "258" ], [ "Myanmar (Burma) (မြန်မာ)", "mm", "95" ], [ "Namibia (Namibië)", "na", "264" ], [ "Nauru", "nr", "674" ], [ "Nepal (नेपाल)", "np", "977" ], [ "Netherlands (Nederland)", "nl", "31" ], [ "New Caledonia (Nouvelle-Calédonie)", "nc", "687" ], [ "New Zealand", "nz", "64" ], [ "Nicaragua", "ni", "505" ], [ "Niger (Nijar)", "ne", "227" ], [ "Nigeria", "ng", "234" ], [ "Niue", "nu", "683" ], [ "Norfolk Island", "nf", "672" ], [ "North Korea (조선 민주주의 인민 공화국)", "kp", "850" ], [ "Northern Mariana Islands", "mp", "1", 17, [ "670" ] ], [ "Norway (Norge)", "no", "47", 0 ], [ "Oman (‫عُمان‬‎)", "om", "968" ], [ "Pakistan (‫پاکستان‬‎)", "pk", "92" ], [ "Palau", "pw", "680" ], [ "Palestine (‫فلسطين‬‎)", "ps", "970" ], [ "Panama (Panamá)", "pa", "507" ], [ "Papua New Guinea", "pg", "675" ], [ "Paraguay", "py", "595" ], [ "Peru (Perú)", "pe", "51" ], [ "Philippines", "ph", "63" ], [ "Poland (Polska)", "pl", "48" ], [ "Portugal", "pt", "351" ], [ "Puerto Rico", "pr", "1", 3, [ "787", "939" ] ], [ "Qatar (‫قطر‬‎)", "qa", "974" ], [ "Réunion (La Réunion)", "re", "262", 0 ], [ "Romania (România)", "ro", "40" ], [ "Russia (Россия)", "ru", "7", 0 ], [ "Rwanda", "rw", "250" ], [ "Saint Barthélemy", "bl", "590", 1 ], [ "Saint Helena", "sh", "290" ], [ "Saint Kitts and Nevis", "kn", "1", 18, [ "869" ] ], [ "Saint Lucia", "lc", "1", 19, [ "758" ] ], [ "Saint Martin (Saint-Martin (partie française))", "mf", "590", 2 ], [ "Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)", "pm", "508" ], [ "Saint Vincent and the Grenadines", "vc", "1", 20, [ "784" ] ], [ "Samoa", "ws", "685" ], [ "San Marino", "sm", "378" ], [ "São Tomé and Príncipe (São Tomé e Príncipe)", "st", "239" ], [ "Saudi Arabia (‫المملكة العربية السعودية‬‎)", "sa", "966" ], [ "Senegal (Sénégal)", "sn", "221" ], [ "Serbia (Србија)", "rs", "381" ], [ "Seychelles", "sc", "248" ], [ "Sierra Leone", "sl", "232" ], [ "Singapore", "sg", "65" ], [ "Sint Maarten", "sx", "1", 21, [ "721" ] ], [ "Slovakia (Slovensko)", "sk", "421" ], [ "Slovenia (Slovenija)", "si", "386" ], [ "Solomon Islands", "sb", "677" ], [ "Somalia (Soomaaliya)", "so", "252" ], [ "South Africa", "za", "27" ], [ "South Korea (대한민국)", "kr", "82" ], [ "South Sudan (‫جنوب السودان‬‎)", "ss", "211" ], [ "Spain (España)", "es", "34" ], [ "Sri Lanka (ශ්‍රී ලංකාව)", "lk", "94" ], [ "Sudan (‫السودان‬‎)", "sd", "249" ], [ "Suriname", "sr", "597" ], [ "Svalbard and Jan Mayen", "sj", "47", 1, [ "79" ] ], [ "Sweden (Sverige)", "se", "46" ], [ "Switzerland (Schweiz)", "ch", "41" ], [ "Syria (‫سوريا‬‎)", "sy", "963" ], [ "Taiwan (台灣)", "tw", "886" ], [ "Tajikistan", "tj", "992" ], [ "Tanzania", "tz", "255" ], [ "Thailand (ไทย)", "th", "66" ], [ "Timor-Leste", "tl", "670" ], [ "Togo", "tg", "228" ], [ "Tokelau", "tk", "690" ], [ "Tonga", "to", "676" ], [ "Trinidad and Tobago", "tt", "1", 22, [ "868" ] ], [ "Tunisia (‫تونس‬‎)", "tn", "216" ], [ "Turkey (Türkiye)", "tr", "90" ], [ "Turkmenistan", "tm", "993" ], [ "Turks and Caicos Islands", "tc", "1", 23, [ "649" ] ], [ "Tuvalu", "tv", "688" ], [ "U.S. Virgin Islands", "vi", "1", 24, [ "340" ] ], [ "Uganda", "ug", "256" ], [ "Ukraine (Україна)", "ua", "380" ], [ "United Arab Emirates (‫الإمارات العربية المتحدة‬‎)", "ae", "971" ], [ "United Kingdom", "gb", "44", 0 ], [ "United States", "us", "1", 0 ], [ "Uruguay", "uy", "598" ], [ "Uzbekistan (Oʻzbekiston)", "uz", "998" ], [ "Vanuatu", "vu", "678" ], [ "Vatican City (Città del Vaticano)", "va", "39", 1, [ "06698" ] ], [ "Venezuela", "ve", "58" ], [ "Vietnam (Việt Nam)", "vn", "84" ], [ "Wallis and Futuna (Wallis-et-Futuna)", "wf", "681" ], [ "Western Sahara (‫الصحراء الغربية‬‎)", "eh", "212", 1, [ "5288", "5289" ] ], [ "Yemen (‫اليمن‬‎)", "ye", "967" ], [ "Zambia", "zm", "260" ], [ "Zimbabwe", "zw", "263" ], [ "Åland Islands", "ax", "358", 1, [ "18" ] ] ];
        // loop over all of the countries above, restructuring the data to be objects with named keys
        for (var i = 0; i < allCountries.length; i++) {
            var c = allCountries[i];
            allCountries[i] = {
                name: c[0],
                iso2: c[1],
                dialCode: c[2],
                priority: c[3] || 0,
                areaCodes: c[4] || null
            };
        }
        "use strict";
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value" in descriptor) descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps) _defineProperties(Constructor.prototype, protoProps);
            if (staticProps) _defineProperties(Constructor, staticProps);
            return Constructor;
        }
        var intlTelInputGlobals = {
            getInstance: function getInstance(input) {
                var id = input.getAttribute("data-intl-tel-input-id");
                return window.intlTelInputGlobals.instances[id];
            },
            instances: {},
            // using a global like this allows us to mock it in the tests
            documentReady: function documentReady() {
                return document.readyState === "complete";
            }
        };
        if (typeof window === "object") window.intlTelInputGlobals = intlTelInputGlobals;
        // these vars persist through all instances of the plugin
        var id = 0;
        var defaults = {
            // whether or not to allow the dropdown
            allowDropdown: true,
            // if there is just a dial code in the input: remove it on blur
            autoHideDialCode: true,
            // add a placeholder in the input with an example number for the selected country
            autoPlaceholder: "polite",
            // modify the parentClass
            customContainer: "",
            // modify the auto placeholder
            customPlaceholder: null,
            // append menu to specified element
            dropdownContainer: null,
            // don't display these countries
            excludeCountries: [],
            // format the input value during initialisation and on setNumber
            formatOnDisplay: true,
            // geoIp lookup function
            geoIpLookup: null,
            // inject a hidden input with this name, and on submit, populate it with the result of getNumber
            hiddenInput: "",
            // initial country
            initialCountry: "",
            // localized country names e.g. { 'de': 'Deutschland' }
            localizedCountries: null,
            // don't insert international dial codes
            nationalMode: true,
            // display only these countries
            onlyCountries: [],
            // number type to use for placeholders
            placeholderNumberType: "MOBILE",
            // the countries at the top of the list. defaults to united states and united kingdom
            preferredCountries: [ "us", "gb" ],
            // display the country dial code next to the selected flag so it's not part of the typed number
            separateDialCode: false,
            // specify the path to the libphonenumber script to enable validation/formatting
            utilsScript: ""
        };
        // https://en.wikipedia.org/wiki/List_of_North_American_Numbering_Plan_area_codes#Non-geographic_area_codes
        var regionlessNanpNumbers = [ "800", "822", "833", "844", "855", "866", "877", "880", "881", "882", "883", "884", "885", "886", "887", "888", "889" ];
        // utility function to iterate over an object. can't use Object.entries or native forEach because
        // of IE11
        var forEachProp = function forEachProp(obj, callback) {
            var keys = Object.keys(obj);
            for (var i = 0; i < keys.length; i++) {
                callback(keys[i], obj[keys[i]]);
            }
        };
        // run a method on each instance of the plugin
        var forEachInstance = function forEachInstance(method) {
            forEachProp(window.intlTelInputGlobals.instances, function(key) {
                window.intlTelInputGlobals.instances[key][method]();
            });
        };
        // this is our plugin class that we will create an instance of
        // eslint-disable-next-line no-unused-vars
        var Iti = /*#__PURE__*/
        function() {
            function Iti(input, options) {
                var _this = this;
                _classCallCheck(this, Iti);
                this.id = id++;
                this.telInput = input;
                this.activeItem = null;
                this.highlightedItem = null;
                // process specified options / defaults
                // alternative to Object.assign, which isn't supported by IE11
                var customOptions = options || {};
                this.options = {};
                forEachProp(defaults, function(key, value) {
                    _this.options[key] = customOptions.hasOwnProperty(key) ? customOptions[key] : value;
                });
                this.hadInitialPlaceholder = Boolean(input.getAttribute("placeholder"));
            }
            _createClass(Iti, [ {
                key: "_init",
                value: function _init() {
                    var _this2 = this;
                    // if in nationalMode, disable options relating to dial codes
                    if (this.options.nationalMode) this.options.autoHideDialCode = false;
                    // if separateDialCode then doesn't make sense to A) insert dial code into input
                    // (autoHideDialCode), and B) display national numbers (because we're displaying the country
                    // dial code next to them)
                    if (this.options.separateDialCode) {
                        this.options.autoHideDialCode = this.options.nationalMode = false;
                    }
                    // we cannot just test screen size as some smartphones/website meta tags will report desktop
                    // resolutions
                    // Note: for some reason jasmine breaks if you put this in the main Plugin function with the
                    // rest of these declarations
                    // Note: to target Android Mobiles (and not Tablets), we must find 'Android' and 'Mobile'
                    this.isMobile = /Android.+Mobile|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                    if (this.isMobile) {
                        // trigger the mobile dropdown css
                        document.body.classList.add("iti-mobile");
                        // on mobile, we want a full screen dropdown, so we must append it to the body
                        if (!this.options.dropdownContainer) this.options.dropdownContainer = document.body;
                    }
                    // these promises get resolved when their individual requests complete
                    // this way the dev can do something like iti.promise.then(...) to know when all requests are
                    // complete
                    if (typeof Promise !== "undefined") {
                        var autoCountryPromise = new Promise(function(resolve, reject) {
                            _this2.resolveAutoCountryPromise = resolve;
                            _this2.rejectAutoCountryPromise = reject;
                        });
                        var utilsScriptPromise = new Promise(function(resolve, reject) {
                            _this2.resolveUtilsScriptPromise = resolve;
                            _this2.rejectUtilsScriptPromise = reject;
                        });
                        this.promise = Promise.all([ autoCountryPromise, utilsScriptPromise ]);
                    } else {
                        // prevent errors when Promise doesn't exist
                        this.resolveAutoCountryPromise = this.rejectAutoCountryPromise = function() {};
                        this.resolveUtilsScriptPromise = this.rejectUtilsScriptPromise = function() {};
                    }
                    // in various situations there could be no country selected initially, but we need to be able
                    // to assume this variable exists
                    this.selectedCountryData = {};
                    // process all the data: onlyCountries, excludeCountries, preferredCountries etc
                    this._processCountryData();
                    // generate the markup
                    this._generateMarkup();
                    // set the initial state of the input value and the selected flag
                    this._setInitialState();
                    // start all of the event listeners: autoHideDialCode, input keydown, selectedFlag click
                    this._initListeners();
                    // utils script, and auto country
                    this._initRequests();
                }
            }, {
                key: "_processCountryData",
                value: function _processCountryData() {
                    // process onlyCountries or excludeCountries array if present
                    this._processAllCountries();
                    // process the countryCodes map
                    this._processCountryCodes();
                    // process the preferredCountries
                    this._processPreferredCountries();
                    // translate countries according to localizedCountries option
                    if (this.options.localizedCountries) this._translateCountriesByLocale();
                    // sort countries by name
                    if (this.options.onlyCountries.length || this.options.localizedCountries) {
                        this.countries.sort(this._countryNameSort);
                    }
                }
            }, {
                key: "_addCountryCode",
                value: function _addCountryCode(iso2, countryCode, priority) {
                    if (countryCode.length > this.countryCodeMaxLen) {
                        this.countryCodeMaxLen = countryCode.length;
                    }
                    if (!this.countryCodes.hasOwnProperty(countryCode)) {
                        this.countryCodes[countryCode] = [];
                    }
                    // bail if we already have this country for this countryCode
                    for (var i = 0; i < this.countryCodes[countryCode].length; i++) {
                        if (this.countryCodes[countryCode][i] === iso2) return;
                    }
                    // check for undefined as 0 is falsy
                    var index = priority !== undefined ? priority : this.countryCodes[countryCode].length;
                    this.countryCodes[countryCode][index] = iso2;
                }
            }, {
                key: "_processAllCountries",
                value: function _processAllCountries() {
                    if (this.options.onlyCountries.length) {
                        var lowerCaseOnlyCountries = this.options.onlyCountries.map(function(country) {
                            return country.toLowerCase();
                        });
                        this.countries = allCountries.filter(function(country) {
                            return lowerCaseOnlyCountries.indexOf(country.iso2) > -1;
                        });
                    } else if (this.options.excludeCountries.length) {
                        var lowerCaseExcludeCountries = this.options.excludeCountries.map(function(country) {
                            return country.toLowerCase();
                        });
                        this.countries = allCountries.filter(function(country) {
                            return lowerCaseExcludeCountries.indexOf(country.iso2) === -1;
                        });
                    } else {
                        this.countries = allCountries;
                    }
                }
            }, {
                key: "_translateCountriesByLocale",
                value: function _translateCountriesByLocale() {
                    for (var i = 0; i < this.countries.length; i++) {
                        var iso = this.countries[i].iso2.toLowerCase();
                        if (this.options.localizedCountries.hasOwnProperty(iso)) {
                            this.countries[i].name = this.options.localizedCountries[iso];
                        }
                    }
                }
            }, {
                key: "_countryNameSort",
                value: function _countryNameSort(a, b) {
                    return a.name.localeCompare(b.name);
                }
            }, {
                key: "_processCountryCodes",
                value: function _processCountryCodes() {
                    this.countryCodeMaxLen = 0;
                    // here we store just dial codes
                    this.dialCodes = {};
                    // here we store "country codes" (both dial codes and their area codes)
                    this.countryCodes = {};
                    // first: add dial codes
                    for (var i = 0; i < this.countries.length; i++) {
                        var c = this.countries[i];
                        if (!this.dialCodes[c.dialCode]) this.dialCodes[c.dialCode] = true;
                        this._addCountryCode(c.iso2, c.dialCode, c.priority);
                    }
                    // next: add area codes
                    // this is a second loop over countries, to make sure we have all of the "root" countries
                    // already in the map, so that we can access them, as each time we add an area code substring
                    // to the map, we also need to include the "root" country's code, as that also matches
                    for (var _i = 0; _i < this.countries.length; _i++) {
                        var _c = this.countries[_i];
                        // area codes
                        if (_c.areaCodes) {
                            var rootCountryCode = this.countryCodes[_c.dialCode][0];
                            // for each area code
                            for (var j = 0; j < _c.areaCodes.length; j++) {
                                var areaCode = _c.areaCodes[j];
                                // for each digit in the area code to add all partial matches as well
                                for (var k = 1; k < areaCode.length; k++) {
                                    var partialDialCode = _c.dialCode + areaCode.substr(0, k);
                                    // start with the root country, as that also matches this dial code
                                    this._addCountryCode(rootCountryCode, partialDialCode);
                                    this._addCountryCode(_c.iso2, partialDialCode);
                                }
                                // add the full area code
                                this._addCountryCode(_c.iso2, _c.dialCode + areaCode);
                            }
                        }
                    }
                }
            }, {
                key: "_processPreferredCountries",
                value: function _processPreferredCountries() {
                    this.preferredCountries = [];
                    for (var i = 0; i < this.options.preferredCountries.length; i++) {
                        var countryCode = this.options.preferredCountries[i].toLowerCase();
                        var countryData = this._getCountryData(countryCode, false, true);
                        if (countryData) this.preferredCountries.push(countryData);
                    }
                }
            }, {
                key: "_createEl",
                value: function _createEl(name, attrs, container) {
                    var el = document.createElement(name);
                    if (attrs) forEachProp(attrs, function(key, value) {
                        return el.setAttribute(key, value);
                    });
                    if (container) container.appendChild(el);
                    return el;
                }
            }, {
                key: "_generateMarkup",
                value: function _generateMarkup() {
                    // if autocomplete does not exist on the element and its form, then
                    // prevent autocomplete as there's no safe, cross-browser event we can react to, so it can
                    // easily put the plugin in an inconsistent state e.g. the wrong flag selected for the
                    // autocompleted number, which on submit could mean wrong number is saved (esp in nationalMode)
                    if (!this.telInput.hasAttribute("autocomplete") && !(this.telInput.form && this.telInput.form.hasAttribute("autocomplete"))) {
                        this.telInput.setAttribute("autocomplete", "off");
                    }
                    // containers (mostly for positioning)
                    var parentClass = "iti";
                    if (this.options.allowDropdown) parentClass += " iti--allow-dropdown";
                    if (this.options.separateDialCode) parentClass += " iti--separate-dial-code";
                    if (this.options.customContainer) {
                        parentClass += " ";
                        parentClass += this.options.customContainer;
                    }
                    var wrapper = this._createEl("div", {
                        "class": parentClass
                    });
                    this.telInput.parentNode.insertBefore(wrapper, this.telInput);
                    this.flagsContainer = this._createEl("div", {
                        "class": "iti__flag-container"
                    }, wrapper);
                    wrapper.appendChild(this.telInput);
                    // selected flag (displayed to left of input)
                    this.selectedFlag = this._createEl("div", {
                        "class": "iti__selected-flag",
                        role: "combobox",
                        "aria-controls": "iti-".concat(this.id, "__country-listbox"),
                        "aria-owns": "iti-".concat(this.id, "__country-listbox"),
                        "aria-expanded": "false"
                    }, this.flagsContainer);
                    this.selectedFlagInner = this._createEl("div", {
                        "class": "iti__flag"
                    }, this.selectedFlag);
                    if (this.options.separateDialCode) {
                        this.selectedDialCode = this._createEl("div", {
                            "class": "iti__selected-dial-code"
                        }, this.selectedFlag);
                    }
                    if (this.options.allowDropdown) {
                        // make element focusable and tab navigable
                        this.selectedFlag.setAttribute("tabindex", "0");
                        this.dropdownArrow = this._createEl("div", {
                            "class": "iti__arrow"
                        }, this.selectedFlag);
                        // country dropdown: preferred countries, then divider, then all countries
                        this.countryList = this._createEl("ul", {
                            "class": "iti__country-list iti__hide",
                            id: "iti-".concat(this.id, "__country-listbox"),
                            role: "listbox",
                            "aria-label": "List of countries"
                        });
                        if (this.preferredCountries.length) {
                            this._appendListItems(this.preferredCountries, "iti__preferred", true);
                            this._createEl("li", {
                                "class": "iti__divider",
                                role: "separator",
                                "aria-disabled": "true"
                            }, this.countryList);
                        }
                        this._appendListItems(this.countries, "iti__standard");
                        // create dropdownContainer markup
                        if (this.options.dropdownContainer) {
                            this.dropdown = this._createEl("div", {
                                "class": "iti iti--container"
                            });
                            this.dropdown.appendChild(this.countryList);
                        } else {
                            this.flagsContainer.appendChild(this.countryList);
                        }
                    }
                    if (this.options.hiddenInput) {
                        var hiddenInputName = this.options.hiddenInput;
                        var name = this.telInput.getAttribute("name");
                        if (name) {
                            var i = name.lastIndexOf("[");
                            // if input name contains square brackets, then give the hidden input the same name,
                            // replacing the contents of the last set of brackets with the given hiddenInput name
                            if (i !== -1) hiddenInputName = "".concat(name.substr(0, i), "[").concat(hiddenInputName, "]");
                        }
                        this.hiddenInput = this._createEl("input", {
                            type: "hidden",
                            name: hiddenInputName
                        });
                        wrapper.appendChild(this.hiddenInput);
                    }
                }
            }, {
                key: "_appendListItems",
                value: function _appendListItems(countries, className, preferred) {
                    // we create so many DOM elements, it is faster to build a temp string
                    // and then add everything to the DOM in one go at the end
                    var tmp = "";
                    // for each country
                    for (var i = 0; i < countries.length; i++) {
                        var c = countries[i];
                        var idSuffix = preferred ? "-preferred" : "";
                        // open the list item
                        tmp += "<li class='iti__country ".concat(className, "' tabIndex='-1' id='iti-").concat(this.id, "__item-").concat(c.iso2).concat(idSuffix, "' role='option' data-dial-code='").concat(c.dialCode, "' data-country-code='").concat(c.iso2, "' aria-selected='false'>");
                        // add the flag
                        tmp += "<div class='iti__flag-box'><div class='iti__flag iti__".concat(c.iso2, "'></div></div>");
                        // and the country name and dial code
                        tmp += "<span class='iti__country-name'>".concat(c.name, "</span>");
                        tmp += "<span class='iti__dial-code'>+".concat(c.dialCode, "</span>");
                        // close the list item
                        tmp += "</li>";
                    }
                    this.countryList.insertAdjacentHTML("beforeend", tmp);
                }
            }, {
                key: "_setInitialState",
                value: function _setInitialState() {
                    // fix firefox bug: when first load page (with input with value set to number with intl dial
                    // code) and initialising plugin removes the dial code from the input, then refresh page,
                    // and we try to init plugin again but this time on number without dial code so get grey flag
                    var attributeValue = this.telInput.getAttribute("value");
                    var inputValue = this.telInput.value;
                    var useAttribute = attributeValue && attributeValue.charAt(0) === "+" && (!inputValue || inputValue.charAt(0) !== "+");
                    var val = useAttribute ? attributeValue : inputValue;
                    var dialCode = this._getDialCode(val);
                    var isRegionlessNanp = this._isRegionlessNanp(val);
                    var _this$options = this.options, initialCountry = _this$options.initialCountry, nationalMode = _this$options.nationalMode, autoHideDialCode = _this$options.autoHideDialCode, separateDialCode = _this$options.separateDialCode;
                    // if we already have a dial code, and it's not a regionlessNanp, we can go ahead and set the
                    // flag, else fall back to the default country
                    if (dialCode && !isRegionlessNanp) {
                        this._updateFlagFromNumber(val);
                    } else if (initialCountry !== "auto") {
                        // see if we should select a flag
                        if (initialCountry) {
                            this._setFlag(initialCountry.toLowerCase());
                        } else {
                            if (dialCode && isRegionlessNanp) {
                                // has intl dial code, is regionless nanp, and no initialCountry, so default to US
                                this._setFlag("us");
                            } else {
                                // no dial code and no initialCountry, so default to first in list
                                this.defaultCountry = this.preferredCountries.length ? this.preferredCountries[0].iso2 : this.countries[0].iso2;
                                if (!val) {
                                    this._setFlag(this.defaultCountry);
                                }
                            }
                        }
                        // if empty and no nationalMode and no autoHideDialCode then insert the default dial code
                        if (!val && !nationalMode && !autoHideDialCode && !separateDialCode) {
                            this.telInput.value = "+".concat(this.selectedCountryData.dialCode);
                        }
                    }
                    // NOTE: if initialCountry is set to auto, that will be handled separately
                    // format - note this wont be run after _updateDialCode as that's only called if no val
                    if (val) this._updateValFromNumber(val);
                }
            }, {
                key: "_initListeners",
                value: function _initListeners() {
                    this._initKeyListeners();
                    if (this.options.autoHideDialCode) this._initBlurListeners();
                    if (this.options.allowDropdown) this._initDropdownListeners();
                    if (this.hiddenInput) this._initHiddenInputListener();
                }
            }, {
                key: "_initHiddenInputListener",
                value: function _initHiddenInputListener() {
                    var _this3 = this;
                    this._handleHiddenInputSubmit = function() {
                        _this3.hiddenInput.value = _this3.getNumber();
                    };
                    if (this.telInput.form) this.telInput.form.addEventListener("submit", this._handleHiddenInputSubmit);
                }
            }, {
                key: "_getClosestLabel",
                value: function _getClosestLabel() {
                    var el = this.telInput;
                    while (el && el.tagName !== "LABEL") {
                        el = el.parentNode;
                    }
                    return el;
                }
            }, {
                key: "_initDropdownListeners",
                value: function _initDropdownListeners() {
                    var _this4 = this;
                    // hack for input nested inside label (which is valid markup): clicking the selected-flag to
                    // open the dropdown would then automatically trigger a 2nd click on the input which would
                    // close it again
                    this._handleLabelClick = function(e) {
                        // if the dropdown is closed, then focus the input, else ignore the click
                        if (_this4.countryList.classList.contains("iti__hide")) _this4.telInput.focus(); else e.preventDefault();
                    };
                    var label = this._getClosestLabel();
                    if (label) label.addEventListener("click", this._handleLabelClick);
                    // toggle country dropdown on click
                    this._handleClickSelectedFlag = function() {
                        // only intercept this event if we're opening the dropdown
                        // else let it bubble up to the top ("click-off-to-close" listener)
                        // we cannot just stopPropagation as it may be needed to close another instance
                        if (_this4.countryList.classList.contains("iti__hide") && !_this4.telInput.disabled && !_this4.telInput.readOnly) {
                            _this4._showDropdown();
                        }
                    };
                    this.selectedFlag.addEventListener("click", this._handleClickSelectedFlag);
                    // open dropdown list if currently focused
                    this._handleFlagsContainerKeydown = function(e) {
                        var isDropdownHidden = _this4.countryList.classList.contains("iti__hide");
                        if (isDropdownHidden && [ "ArrowUp", "Up", "ArrowDown", "Down", " ", "Enter" ].indexOf(e.key) !== -1) {
                            // prevent form from being submitted if "ENTER" was pressed
                            e.preventDefault();
                            // prevent event from being handled again by document
                            e.stopPropagation();
                            _this4._showDropdown();
                        }
                        // allow navigation from dropdown to input on TAB
                        if (e.key === "Tab") _this4._closeDropdown();
                    };
                    this.flagsContainer.addEventListener("keydown", this._handleFlagsContainerKeydown);
                }
            }, {
                key: "_initRequests",
                value: function _initRequests() {
                    var _this5 = this;
                    // if the user has specified the path to the utils script, fetch it on window.load, else resolve
                    if (this.options.utilsScript && !window.intlTelInputUtils) {
                        // if the plugin is being initialised after the window.load event has already been fired
                        if (window.intlTelInputGlobals.documentReady()) {
                            window.intlTelInputGlobals.loadUtils(this.options.utilsScript);
                        } else {
                            // wait until the load event so we don't block any other requests e.g. the flags image
                            window.addEventListener("load", function() {
                                window.intlTelInputGlobals.loadUtils(_this5.options.utilsScript);
                            });
                        }
                    } else this.resolveUtilsScriptPromise();
                    if (this.options.initialCountry === "auto") this._loadAutoCountry(); else this.resolveAutoCountryPromise();
                }
            }, {
                key: "_loadAutoCountry",
                value: function _loadAutoCountry() {
                    // 3 options:
                    // 1) already loaded (we're done)
                    // 2) not already started loading (start)
                    // 3) already started loading (do nothing - just wait for loading callback to fire)
                    if (window.intlTelInputGlobals.autoCountry) {
                        this.handleAutoCountry();
                    } else if (!window.intlTelInputGlobals.startedLoadingAutoCountry) {
                        // don't do this twice!
                        window.intlTelInputGlobals.startedLoadingAutoCountry = true;
                        if (typeof this.options.geoIpLookup === "function") {
                            this.options.geoIpLookup(function(countryCode) {
                                window.intlTelInputGlobals.autoCountry = countryCode.toLowerCase();
                                // tell all instances the auto country is ready
                                // TODO: this should just be the current instances
                                // UPDATE: use setTimeout in case their geoIpLookup function calls this callback straight
                                // away (e.g. if they have already done the geo ip lookup somewhere else). Using
                                // setTimeout means that the current thread of execution will finish before executing
                                // this, which allows the plugin to finish initialising.
                                setTimeout(function() {
                                    return forEachInstance("handleAutoCountry");
                                });
                            }, function() {
                                return forEachInstance("rejectAutoCountryPromise");
                            });
                        }
                    }
                }
            }, {
                key: "_initKeyListeners",
                value: function _initKeyListeners() {
                    var _this6 = this;
                    // update flag on keyup
                    this._handleKeyupEvent = function() {
                        if (_this6._updateFlagFromNumber(_this6.telInput.value)) {
                            _this6._triggerCountryChange();
                        }
                    };
                    this.telInput.addEventListener("keyup", this._handleKeyupEvent);
                    // update flag on cut/paste events (now supported in all major browsers)
                    this._handleClipboardEvent = function() {
                        // hack because "paste" event is fired before input is updated
                        setTimeout(_this6._handleKeyupEvent);
                    };
                    this.telInput.addEventListener("cut", this._handleClipboardEvent);
                    this.telInput.addEventListener("paste", this._handleClipboardEvent);
                }
            }, {
                key: "_cap",
                value: function _cap(number) {
                    var max = this.telInput.getAttribute("maxlength");
                    return max && number.length > max ? number.substr(0, max) : number;
                }
            }, {
                key: "_initBlurListeners",
                value: function _initBlurListeners() {
                    var _this7 = this;
                    // on blur or form submit: if just a dial code then remove it
                    this._handleSubmitOrBlurEvent = function() {
                        _this7._removeEmptyDialCode();
                    };
                    if (this.telInput.form) this.telInput.form.addEventListener("submit", this._handleSubmitOrBlurEvent);
                    this.telInput.addEventListener("blur", this._handleSubmitOrBlurEvent);
                }
            }, {
                key: "_removeEmptyDialCode",
                value: function _removeEmptyDialCode() {
                    if (this.telInput.value.charAt(0) === "+") {
                        var numeric = this._getNumeric(this.telInput.value);
                        // if just a plus, or if just a dial code
                        if (!numeric || this.selectedCountryData.dialCode === numeric) {
                            this.telInput.value = "";
                        }
                    }
                }
            }, {
                key: "_getNumeric",
                value: function _getNumeric(s) {
                    return s.replace(/\D/g, "");
                }
            }, {
                key: "_trigger",
                value: function _trigger(name) {
                    // have to use old school document.createEvent as IE11 doesn't support `new Event()` syntax
                    var e = document.createEvent("Event");
                    e.initEvent(name, true, true);
                    // can bubble, and is cancellable
                    this.telInput.dispatchEvent(e);
                }
            }, {
                key: "_showDropdown",
                value: function _showDropdown() {
                    this.countryList.classList.remove("iti__hide");
                    this.selectedFlag.setAttribute("aria-expanded", "true");
                    this._setDropdownPosition();
                    // update highlighting and scroll to active list item
                    if (this.activeItem) {
                        this._highlightListItem(this.activeItem, false);
                        this._scrollTo(this.activeItem, true);
                    }
                    // bind all the dropdown-related listeners: mouseover, click, click-off, keydown
                    this._bindDropdownListeners();
                    // update the arrow
                    this.dropdownArrow.classList.add("iti__arrow--up");
                    this._trigger("open:countrydropdown");
                }
            }, {
                key: "_toggleClass",
                value: function _toggleClass(el, className, shouldHaveClass) {
                    if (shouldHaveClass && !el.classList.contains(className)) el.classList.add(className); else if (!shouldHaveClass && el.classList.contains(className)) el.classList.remove(className);
                }
            }, {
                key: "_setDropdownPosition",
                value: function _setDropdownPosition() {
                    var _this8 = this;
                    if (this.options.dropdownContainer) {
                        this.options.dropdownContainer.appendChild(this.dropdown);
                    }
                    if (!this.isMobile) {
                        var pos = this.telInput.getBoundingClientRect();
                        // windowTop from https://stackoverflow.com/a/14384091/217866
                        var windowTop = window.pageYOffset || document.documentElement.scrollTop;
                        var inputTop = pos.top + windowTop;
                        var dropdownHeight = this.countryList.offsetHeight;
                        // dropdownFitsBelow = (dropdownBottom < windowBottom)
                        var dropdownFitsBelow = inputTop + this.telInput.offsetHeight + dropdownHeight < windowTop + window.innerHeight;
                        var dropdownFitsAbove = inputTop - dropdownHeight > windowTop;
                        // by default, the dropdown will be below the input. If we want to position it above the
                        // input, we add the dropup class.
                        this._toggleClass(this.countryList, "iti__country-list--dropup", !dropdownFitsBelow && dropdownFitsAbove);
                        // if dropdownContainer is enabled, calculate postion
                        if (this.options.dropdownContainer) {
                            // by default the dropdown will be directly over the input because it's not in the flow.
                            // If we want to position it below, we need to add some extra top value.
                            var extraTop = !dropdownFitsBelow && dropdownFitsAbove ? 0 : this.telInput.offsetHeight;
                            // calculate placement
                            this.dropdown.style.top = "".concat(inputTop + extraTop, "px");
                            this.dropdown.style.left = "".concat(pos.left + document.body.scrollLeft, "px");
                            // close menu on window scroll
                            this._handleWindowScroll = function() {
                                return _this8._closeDropdown();
                            };
                            window.addEventListener("scroll", this._handleWindowScroll);
                        }
                    }
                }
            }, {
                key: "_getClosestListItem",
                value: function _getClosestListItem(target) {
                    var el = target;
                    while (el && el !== this.countryList && !el.classList.contains("iti__country")) {
                        el = el.parentNode;
                    }
                    // if we reached the countryList element, then return null
                    return el === this.countryList ? null : el;
                }
            }, {
                key: "_bindDropdownListeners",
                value: function _bindDropdownListeners() {
                    var _this9 = this;
                    // when mouse over a list item, just highlight that one
                    // we add the class "highlight", so if they hit "enter" we know which one to select
                    this._handleMouseoverCountryList = function(e) {
                        // handle event delegation, as we're listening for this event on the countryList
                        var listItem = _this9._getClosestListItem(e.target);
                        if (listItem) _this9._highlightListItem(listItem, false);
                    };
                    this.countryList.addEventListener("mouseover", this._handleMouseoverCountryList);
                    // listen for country selection
                    this._handleClickCountryList = function(e) {
                        var listItem = _this9._getClosestListItem(e.target);
                        if (listItem) _this9._selectListItem(listItem);
                    };
                    this.countryList.addEventListener("click", this._handleClickCountryList);
                    // click off to close
                    // (except when this initial opening click is bubbling up)
                    // we cannot just stopPropagation as it may be needed to close another instance
                    var isOpening = true;
                    this._handleClickOffToClose = function() {
                        if (!isOpening) _this9._closeDropdown();
                        isOpening = false;
                    };
                    document.documentElement.addEventListener("click", this._handleClickOffToClose);
                    // listen for up/down scrolling, enter to select, or letters to jump to country name.
                    // use keydown as keypress doesn't fire for non-char keys and we want to catch if they
                    // just hit down and hold it to scroll down (no keyup event).
                    // listen on the document because that's where key events are triggered if no input has focus
                    var query = "";
                    var queryTimer = null;
                    this._handleKeydownOnDropdown = function(e) {
                        // prevent down key from scrolling the whole page,
                        // and enter key from submitting a form etc
                        e.preventDefault();
                        // up and down to navigate
                        if (e.key === "ArrowUp" || e.key === "Up" || e.key === "ArrowDown" || e.key === "Down") _this9._handleUpDownKey(e.key); else if (e.key === "Enter") _this9._handleEnterKey(); else if (e.key === "Escape") _this9._closeDropdown(); else if (/^[a-zA-ZÀ-ÿа-яА-Я ]$/.test(e.key)) {
                            // jump to countries that start with the query string
                            if (queryTimer) clearTimeout(queryTimer);
                            query += e.key.toLowerCase();
                            _this9._searchForCountry(query);
                            // if the timer hits 1 second, reset the query
                            queryTimer = setTimeout(function() {
                                query = "";
                            }, 1e3);
                        }
                    };
                    document.addEventListener("keydown", this._handleKeydownOnDropdown);
                }
            }, {
                key: "_handleUpDownKey",
                value: function _handleUpDownKey(key) {
                    var next = key === "ArrowUp" || key === "Up" ? this.highlightedItem.previousElementSibling : this.highlightedItem.nextElementSibling;
                    if (next) {
                        // skip the divider
                        if (next.classList.contains("iti__divider")) {
                            next = key === "ArrowUp" || key === "Up" ? next.previousElementSibling : next.nextElementSibling;
                        }
                        this._highlightListItem(next, true);
                    }
                }
            }, {
                key: "_handleEnterKey",
                value: function _handleEnterKey() {
                    if (this.highlightedItem) this._selectListItem(this.highlightedItem);
                }
            }, {
                key: "_searchForCountry",
                value: function _searchForCountry(query) {
                    for (var i = 0; i < this.countries.length; i++) {
                        if (this._startsWith(this.countries[i].name, query)) {
                            var listItem = this.countryList.querySelector("#iti-".concat(this.id, "__item-").concat(this.countries[i].iso2));
                            // update highlighting and scroll
                            this._highlightListItem(listItem, false);
                            this._scrollTo(listItem, true);
                            break;
                        }
                    }
                }
            }, {
                key: "_startsWith",
                value: function _startsWith(a, b) {
                    return a.substr(0, b.length).toLowerCase() === b;
                }
            }, {
                key: "_updateValFromNumber",
                value: function _updateValFromNumber(originalNumber) {
                    var number = originalNumber;
                    if (this.options.formatOnDisplay && window.intlTelInputUtils && this.selectedCountryData) {
                        var useNational = !this.options.separateDialCode && (this.options.nationalMode || number.charAt(0) !== "+");
                        var _intlTelInputUtils$nu = intlTelInputUtils.numberFormat, NATIONAL = _intlTelInputUtils$nu.NATIONAL, INTERNATIONAL = _intlTelInputUtils$nu.INTERNATIONAL;
                        var format = useNational ? NATIONAL : INTERNATIONAL;
                        number = intlTelInputUtils.formatNumber(number, this.selectedCountryData.iso2, format);
                    }
                    number = this._beforeSetNumber(number);
                    this.telInput.value = number;
                }
            }, {
                key: "_updateFlagFromNumber",
                value: function _updateFlagFromNumber(originalNumber) {
                    // if we're in nationalMode and we already have US/Canada selected, make sure the number starts
                    // with a +1 so _getDialCode will be able to extract the area code
                    // update: if we dont yet have selectedCountryData, but we're here (trying to update the flag
                    // from the number), that means we're initialising the plugin with a number that already has a
                    // dial code, so fine to ignore this bit
                    var number = originalNumber;
                    var selectedDialCode = this.selectedCountryData.dialCode;
                    var isNanp = selectedDialCode === "1";
                    if (number && this.options.nationalMode && isNanp && number.charAt(0) !== "+") {
                        if (number.charAt(0) !== "1") number = "1".concat(number);
                        number = "+".concat(number);
                    }
                    // update flag if user types area code for another country
                    if (this.options.separateDialCode && selectedDialCode && number.charAt(0) !== "+") {
                        number = "+".concat(selectedDialCode).concat(number);
                    }
                    // try and extract valid dial code from input
                    var dialCode = this._getDialCode(number, true);
                    var numeric = this._getNumeric(number);
                    var countryCode = null;
                    if (dialCode) {
                        var countryCodes = this.countryCodes[this._getNumeric(dialCode)];
                        // check if the right country is already selected. this should be false if the number is
                        // longer than the matched dial code because in this case we need to make sure that if
                        // there are multiple country matches, that the first one is selected (note: we could
                        // just check that here, but it requires the same loop that we already have later)
                        var alreadySelected = countryCodes.indexOf(this.selectedCountryData.iso2) !== -1 && numeric.length <= dialCode.length - 1;
                        var isRegionlessNanpNumber = selectedDialCode === "1" && this._isRegionlessNanp(numeric);
                        // only update the flag if:
                        // A) NOT (we currently have a NANP flag selected, and the number is a regionlessNanp)
                        // AND
                        // B) the right country is not already selected
                        if (!isRegionlessNanpNumber && !alreadySelected) {
                            // if using onlyCountries option, countryCodes[0] may be empty, so we must find the first
                            // non-empty index
                            for (var j = 0; j < countryCodes.length; j++) {
                                if (countryCodes[j]) {
                                    countryCode = countryCodes[j];
                                    break;
                                }
                            }
                        }
                    } else if (number.charAt(0) === "+" && numeric.length) {
                        // invalid dial code, so empty
                        // Note: use getNumeric here because the number has not been formatted yet, so could contain
                        // bad chars
                        countryCode = "";
                    } else if (!number || number === "+") {
                        // empty, or just a plus, so default
                        countryCode = this.defaultCountry;
                    }
                    if (countryCode !== null) {
                        return this._setFlag(countryCode);
                    }
                    return false;
                }
            }, {
                key: "_isRegionlessNanp",
                value: function _isRegionlessNanp(number) {
                    var numeric = this._getNumeric(number);
                    if (numeric.charAt(0) === "1") {
                        var areaCode = numeric.substr(1, 3);
                        return regionlessNanpNumbers.indexOf(areaCode) !== -1;
                    }
                    return false;
                }
            }, {
                key: "_highlightListItem",
                value: function _highlightListItem(listItem, shouldFocus) {
                    var prevItem = this.highlightedItem;
                    if (prevItem) prevItem.classList.remove("iti__highlight");
                    this.highlightedItem = listItem;
                    this.highlightedItem.classList.add("iti__highlight");
                    if (shouldFocus) this.highlightedItem.focus();
                }
            }, {
                key: "_getCountryData",
                value: function _getCountryData(countryCode, ignoreOnlyCountriesOption, allowFail) {
                    var countryList = ignoreOnlyCountriesOption ? allCountries : this.countries;
                    for (var i = 0; i < countryList.length; i++) {
                        if (countryList[i].iso2 === countryCode) {
                            return countryList[i];
                        }
                    }
                    if (allowFail) {
                        return null;
                    }
                    throw new Error("No country data for '".concat(countryCode, "'"));
                }
            }, {
                key: "_setFlag",
                value: function _setFlag(countryCode) {
                    var prevCountry = this.selectedCountryData.iso2 ? this.selectedCountryData : {};
                    // do this first as it will throw an error and stop if countryCode is invalid
                    this.selectedCountryData = countryCode ? this._getCountryData(countryCode, false, false) : {};
                    // update the defaultCountry - we only need the iso2 from now on, so just store that
                    if (this.selectedCountryData.iso2) {
                        this.defaultCountry = this.selectedCountryData.iso2;
                    }
                    this.selectedFlagInner.setAttribute("class", "iti__flag iti__".concat(countryCode));
                    // update the selected country's title attribute
                    var title = countryCode ? "".concat(this.selectedCountryData.name, ": +").concat(this.selectedCountryData.dialCode) : "Unknown";
                    this.selectedFlag.setAttribute("title", title);
                    if (this.options.separateDialCode) {
                        var dialCode = this.selectedCountryData.dialCode ? "+".concat(this.selectedCountryData.dialCode) : "";
                        this.selectedDialCode.innerHTML = dialCode;
                        // offsetWidth is zero if input is in a hidden container during initialisation
                        var selectedFlagWidth = this.selectedFlag.offsetWidth || this._getHiddenSelectedFlagWidth();
                        // add 6px of padding after the grey selected-dial-code box, as this is what we use in the css
                        this.telInput.style.paddingLeft = "".concat(selectedFlagWidth + 6, "px");
                    }
                    // and the input's placeholder
                    this._updatePlaceholder();
                    // update the active list item
                    if (this.options.allowDropdown) {
                        var prevItem = this.activeItem;
                        if (prevItem) {
                            prevItem.classList.remove("iti__active");
                            prevItem.setAttribute("aria-selected", "false");
                        }
                        if (countryCode) {
                            // check if there is a preferred item first, else fall back to standard
                            var nextItem = this.countryList.querySelector("#iti-".concat(this.id, "__item-").concat(countryCode, "-preferred")) || this.countryList.querySelector("#iti-".concat(this.id, "__item-").concat(countryCode));
                            nextItem.setAttribute("aria-selected", "true");
                            nextItem.classList.add("iti__active");
                            this.activeItem = nextItem;
                            this.selectedFlag.setAttribute("aria-activedescendant", nextItem.getAttribute("id"));
                        }
                    }
                    // return if the flag has changed or not
                    return prevCountry.iso2 !== countryCode;
                }
            }, {
                key: "_getHiddenSelectedFlagWidth",
                value: function _getHiddenSelectedFlagWidth() {
                    // to get the right styling to apply, all we need is a shallow clone of the container,
                    // and then to inject a deep clone of the selectedFlag element
                    var containerClone = this.telInput.parentNode.cloneNode();
                    containerClone.style.visibility = "hidden";
                    document.body.appendChild(containerClone);
                    var flagsContainerClone = this.flagsContainer.cloneNode();
                    containerClone.appendChild(flagsContainerClone);
                    var selectedFlagClone = this.selectedFlag.cloneNode(true);
                    flagsContainerClone.appendChild(selectedFlagClone);
                    var width = selectedFlagClone.offsetWidth;
                    containerClone.parentNode.removeChild(containerClone);
                    return width;
                }
            }, {
                key: "_updatePlaceholder",
                value: function _updatePlaceholder() {
                    var shouldSetPlaceholder = this.options.autoPlaceholder === "aggressive" || !this.hadInitialPlaceholder && this.options.autoPlaceholder === "polite";
                    if (window.intlTelInputUtils && shouldSetPlaceholder) {
                        var numberType = intlTelInputUtils.numberType[this.options.placeholderNumberType];
                        var placeholder = this.selectedCountryData.iso2 ? intlTelInputUtils.getExampleNumber(this.selectedCountryData.iso2, this.options.nationalMode, numberType) : "";
                        placeholder = this._beforeSetNumber(placeholder);
                        if (typeof this.options.customPlaceholder === "function") {
                            placeholder = this.options.customPlaceholder(placeholder, this.selectedCountryData);
                        }
                        this.telInput.setAttribute("placeholder", placeholder);
                    }
                }
            }, {
                key: "_selectListItem",
                value: function _selectListItem(listItem) {
                    // update selected flag and active list item
                    var flagChanged = this._setFlag(listItem.getAttribute("data-country-code"));
                    this._closeDropdown();
                    this._updateDialCode(listItem.getAttribute("data-dial-code"), true);
                    // focus the input
                    this.telInput.focus();
                    // put cursor at end - this fix is required for FF and IE11 (with nationalMode=false i.e. auto
                    // inserting dial code), who try to put the cursor at the beginning the first time
                    var len = this.telInput.value.length;
                    this.telInput.setSelectionRange(len, len);
                    if (flagChanged) {
                        this._triggerCountryChange();
                    }
                }
            }, {
                key: "_closeDropdown",
                value: function _closeDropdown() {
                    this.countryList.classList.add("iti__hide");
                    this.selectedFlag.setAttribute("aria-expanded", "false");
                    // update the arrow
                    this.dropdownArrow.classList.remove("iti__arrow--up");
                    // unbind key events
                    document.removeEventListener("keydown", this._handleKeydownOnDropdown);
                    document.documentElement.removeEventListener("click", this._handleClickOffToClose);
                    this.countryList.removeEventListener("mouseover", this._handleMouseoverCountryList);
                    this.countryList.removeEventListener("click", this._handleClickCountryList);
                    // remove menu from container
                    if (this.options.dropdownContainer) {
                        if (!this.isMobile) window.removeEventListener("scroll", this._handleWindowScroll);
                        if (this.dropdown.parentNode) this.dropdown.parentNode.removeChild(this.dropdown);
                    }
                    this._trigger("close:countrydropdown");
                }
            }, {
                key: "_scrollTo",
                value: function _scrollTo(element, middle) {
                    var container = this.countryList;
                    // windowTop from https://stackoverflow.com/a/14384091/217866
                    var windowTop = window.pageYOffset || document.documentElement.scrollTop;
                    var containerHeight = container.offsetHeight;
                    var containerTop = container.getBoundingClientRect().top + windowTop;
                    var containerBottom = containerTop + containerHeight;
                    var elementHeight = element.offsetHeight;
                    var elementTop = element.getBoundingClientRect().top + windowTop;
                    var elementBottom = elementTop + elementHeight;
                    var newScrollTop = elementTop - containerTop + container.scrollTop;
                    var middleOffset = containerHeight / 2 - elementHeight / 2;
                    if (elementTop < containerTop) {
                        // scroll up
                        if (middle) newScrollTop -= middleOffset;
                        container.scrollTop = newScrollTop;
                    } else if (elementBottom > containerBottom) {
                        // scroll down
                        if (middle) newScrollTop += middleOffset;
                        var heightDifference = containerHeight - elementHeight;
                        container.scrollTop = newScrollTop - heightDifference;
                    }
                }
            }, {
                key: "_updateDialCode",
                value: function _updateDialCode(newDialCodeBare, hasSelectedListItem) {
                    var inputVal = this.telInput.value;
                    // save having to pass this every time
                    var newDialCode = "+".concat(newDialCodeBare);
                    var newNumber;
                    if (inputVal.charAt(0) === "+") {
                        // there's a plus so we're dealing with a replacement (doesn't matter if nationalMode or not)
                        var prevDialCode = this._getDialCode(inputVal);
                        if (prevDialCode) {
                            // current number contains a valid dial code, so replace it
                            newNumber = inputVal.replace(prevDialCode, newDialCode);
                        } else {
                            // current number contains an invalid dial code, so ditch it
                            // (no way to determine where the invalid dial code ends and the rest of the number begins)
                            newNumber = newDialCode;
                        }
                    } else if (this.options.nationalMode || this.options.separateDialCode) {
                        // don't do anything
                        return;
                    } else {
                        // nationalMode is disabled
                        if (inputVal) {
                            // there is an existing value with no dial code: prefix the new dial code
                            newNumber = newDialCode + inputVal;
                        } else if (hasSelectedListItem || !this.options.autoHideDialCode) {
                            // no existing value and either they've just selected a list item, or autoHideDialCode is
                            // disabled: insert new dial code
                            newNumber = newDialCode;
                        } else {
                            return;
                        }
                    }
                    this.telInput.value = newNumber;
                }
            }, {
                key: "_getDialCode",
                value: function _getDialCode(number, includeAreaCode) {
                    var dialCode = "";
                    // only interested in international numbers (starting with a plus)
                    if (number.charAt(0) === "+") {
                        var numericChars = "";
                        // iterate over chars
                        for (var i = 0; i < number.length; i++) {
                            var c = number.charAt(i);
                            // if char is number (https://stackoverflow.com/a/8935649/217866)
                            if (!isNaN(parseInt(c, 10))) {
                                numericChars += c;
                                // if current numericChars make a valid dial code
                                if (includeAreaCode) {
                                    if (this.countryCodes[numericChars]) {
                                        // store the actual raw string (useful for matching later)
                                        dialCode = number.substr(0, i + 1);
                                    }
                                } else {
                                    if (this.dialCodes[numericChars]) {
                                        dialCode = number.substr(0, i + 1);
                                        // if we're just looking for a dial code, we can break as soon as we find one
                                        break;
                                    }
                                }
                                // stop searching as soon as we can - in this case when we hit max len
                                if (numericChars.length === this.countryCodeMaxLen) {
                                    break;
                                }
                            }
                        }
                    }
                    return dialCode;
                }
            }, {
                key: "_getFullNumber",
                value: function _getFullNumber() {
                    var val = this.telInput.value.trim();
                    var dialCode = this.selectedCountryData.dialCode;
                    var prefix;
                    var numericVal = this._getNumeric(val);
                    if (this.options.separateDialCode && val.charAt(0) !== "+" && dialCode && numericVal) {
                        // when using separateDialCode, it is visible so is effectively part of the typed number
                        prefix = "+".concat(dialCode);
                    } else {
                        prefix = "";
                    }
                    return prefix + val;
                }
            }, {
                key: "_beforeSetNumber",
                value: function _beforeSetNumber(originalNumber) {
                    var number = originalNumber;
                    if (this.options.separateDialCode) {
                        var dialCode = this._getDialCode(number);
                        // if there is a valid dial code
                        if (dialCode) {
                            // in case _getDialCode returned an area code as well
                            dialCode = "+".concat(this.selectedCountryData.dialCode);
                            // a lot of numbers will have a space separating the dial code and the main number, and
                            // some NANP numbers will have a hyphen e.g. +1 684-733-1234 - in both cases we want to get
                            // rid of it
                            // NOTE: don't just trim all non-numerics as may want to preserve an open parenthesis etc
                            var start = number[dialCode.length] === " " || number[dialCode.length] === "-" ? dialCode.length + 1 : dialCode.length;
                            number = number.substr(start);
                        }
                    }
                    return this._cap(number);
                }
            }, {
                key: "_triggerCountryChange",
                value: function _triggerCountryChange() {
                    this._trigger("countrychange");
                }
            }, {
                key: "handleAutoCountry",
                value: function handleAutoCountry() {
                    if (this.options.initialCountry === "auto") {
                        // we must set this even if there is an initial val in the input: in case the initial val is
                        // invalid and they delete it - they should see their auto country
                        this.defaultCountry = window.intlTelInputGlobals.autoCountry;
                        // if there's no initial value in the input, then update the flag
                        if (!this.telInput.value) {
                            this.setCountry(this.defaultCountry);
                        }
                        this.resolveAutoCountryPromise();
                    }
                }
            }, {
                key: "handleUtils",
                value: function handleUtils() {
                    // if the request was successful
                    if (window.intlTelInputUtils) {
                        // if there's an initial value in the input, then format it
                        if (this.telInput.value) {
                            this._updateValFromNumber(this.telInput.value);
                        }
                        this._updatePlaceholder();
                    }
                    this.resolveUtilsScriptPromise();
                }
            }, {
                key: "destroy",
                value: function destroy() {
                    var form = this.telInput.form;
                    if (this.options.allowDropdown) {
                        // make sure the dropdown is closed (and unbind listeners)
                        this._closeDropdown();
                        this.selectedFlag.removeEventListener("click", this._handleClickSelectedFlag);
                        this.flagsContainer.removeEventListener("keydown", this._handleFlagsContainerKeydown);
                        // label click hack
                        var label = this._getClosestLabel();
                        if (label) label.removeEventListener("click", this._handleLabelClick);
                    }
                    // unbind hiddenInput listeners
                    if (this.hiddenInput && form) form.removeEventListener("submit", this._handleHiddenInputSubmit);
                    // unbind autoHideDialCode listeners
                    if (this.options.autoHideDialCode) {
                        if (form) form.removeEventListener("submit", this._handleSubmitOrBlurEvent);
                        this.telInput.removeEventListener("blur", this._handleSubmitOrBlurEvent);
                    }
                    // unbind key events, and cut/paste events
                    this.telInput.removeEventListener("keyup", this._handleKeyupEvent);
                    this.telInput.removeEventListener("cut", this._handleClipboardEvent);
                    this.telInput.removeEventListener("paste", this._handleClipboardEvent);
                    // remove attribute of id instance: data-intl-tel-input-id
                    this.telInput.removeAttribute("data-intl-tel-input-id");
                    // remove markup (but leave the original input)
                    var wrapper = this.telInput.parentNode;
                    wrapper.parentNode.insertBefore(this.telInput, wrapper);
                    wrapper.parentNode.removeChild(wrapper);
                    delete window.intlTelInputGlobals.instances[this.id];
                }
            }, {
                key: "getExtension",
                value: function getExtension() {
                    if (window.intlTelInputUtils) {
                        return intlTelInputUtils.getExtension(this._getFullNumber(), this.selectedCountryData.iso2);
                    }
                    return "";
                }
            }, {
                key: "getNumber",
                value: function getNumber(format) {
                    if (window.intlTelInputUtils) {
                        var iso2 = this.selectedCountryData.iso2;
                        return intlTelInputUtils.formatNumber(this._getFullNumber(), iso2, format);
                    }
                    return "";
                }
            }, {
                key: "getNumberType",
                value: function getNumberType() {
                    if (window.intlTelInputUtils) {
                        return intlTelInputUtils.getNumberType(this._getFullNumber(), this.selectedCountryData.iso2);
                    }
                    return -99;
                }
            }, {
                key: "getSelectedCountryData",
                value: function getSelectedCountryData() {
                    return this.selectedCountryData;
                }
            }, {
                key: "getValidationError",
                value: function getValidationError() {
                    if (window.intlTelInputUtils) {
                        var iso2 = this.selectedCountryData.iso2;
                        return intlTelInputUtils.getValidationError(this._getFullNumber(), iso2);
                    }
                    return -99;
                }
            }, {
                key: "isValidNumber",
                value: function isValidNumber() {
                    var val = this._getFullNumber().trim();
                    var countryCode = this.options.nationalMode ? this.selectedCountryData.iso2 : "";
                    return window.intlTelInputUtils ? intlTelInputUtils.isValidNumber(val, countryCode) : null;
                }
            }, {
                key: "setCountry",
                value: function setCountry(originalCountryCode) {
                    var countryCode = originalCountryCode.toLowerCase();
                    // check if already selected
                    if (!this.selectedFlagInner.classList.contains("iti__".concat(countryCode))) {
                        this._setFlag(countryCode);
                        this._updateDialCode(this.selectedCountryData.dialCode, false);
                        this._triggerCountryChange();
                    }
                }
            }, {
                key: "setNumber",
                value: function setNumber(number) {
                    // we must update the flag first, which updates this.selectedCountryData, which is used for
                    // formatting the number before displaying it
                    var flagChanged = this._updateFlagFromNumber(number);
                    this._updateValFromNumber(number);
                    if (flagChanged) {
                        this._triggerCountryChange();
                    }
                }
            }, {
                key: "setPlaceholderNumberType",
                value: function setPlaceholderNumberType(type) {
                    this.options.placeholderNumberType = type;
                    this._updatePlaceholder();
                }
            } ]);
            return Iti;
        }();
        /********************
 *  STATIC METHODS
 ********************/
        // get the country data object
        intlTelInputGlobals.getCountryData = function() {
            return allCountries;
        };
        // inject a <script> element to load utils.js
        var injectScript = function injectScript(path, handleSuccess, handleFailure) {
            // inject a new script element into the page
            var script = document.createElement("script");
            script.onload = function() {
                forEachInstance("handleUtils");
                if (handleSuccess) handleSuccess();
            };
            script.onerror = function() {
                forEachInstance("rejectUtilsScriptPromise");
                if (handleFailure) handleFailure();
            };
            script.className = "iti-load-utils";
            script.async = true;
            script.src = path;
            document.body.appendChild(script);
        };
        // load the utils script
        intlTelInputGlobals.loadUtils = function(path) {
            // 2 options:
            // 1) not already started loading (start)
            // 2) already started loading (do nothing - just wait for the onload callback to fire, which will
            // trigger handleUtils on all instances, invoking their resolveUtilsScriptPromise functions)
            if (!window.intlTelInputUtils && !window.intlTelInputGlobals.startedLoadingUtilsScript) {
                // only do this once
                window.intlTelInputGlobals.startedLoadingUtilsScript = true;
                // if we have promises, then return a promise
                if (typeof Promise !== "undefined") {
                    return new Promise(function(resolve, reject) {
                        return injectScript(path, resolve, reject);
                    });
                }
                injectScript(path);
            }
            return null;
        };
        // default options
        intlTelInputGlobals.defaults = defaults;
        // version
        intlTelInputGlobals.version = "17.0.16";
        // convenience wrapper
        return function(input, options) {
            var iti = new Iti(input, options);
            iti._init();
            input.setAttribute("data-intl-tel-input-id", iti.id);
            window.intlTelInputGlobals.instances[iti.id] = iti;
            return iti;
        };
    }();
});
},{}],19:[function(require,module,exports){
/**
 * Exposing intl-tel-input as a component
 */
module.exports = require("./build/js/intlTelInput");

},{"./build/js/intlTelInput":18}],20:[function(require,module,exports){
(function (process){
/**
 * Memize options object.
 *
 * @typedef MemizeOptions
 *
 * @property {number} [maxSize] Maximum size of the cache.
 */

/**
 * Internal cache entry.
 *
 * @typedef MemizeCacheNode
 *
 * @property {?MemizeCacheNode|undefined} [prev] Previous node.
 * @property {?MemizeCacheNode|undefined} [next] Next node.
 * @property {Array<*>}                   args   Function arguments for cache
 *                                               entry.
 * @property {*}                          val    Function result.
 */

/**
 * Properties of the enhanced function for controlling cache.
 *
 * @typedef MemizeMemoizedFunction
 *
 * @property {()=>void} clear Clear the cache.
 */

/**
 * Accepts a function to be memoized, and returns a new memoized function, with
 * optional options.
 *
 * @template {Function} F
 *
 * @param {F}             fn        Function to memoize.
 * @param {MemizeOptions} [options] Options object.
 *
 * @return {F & MemizeMemoizedFunction} Memoized function.
 */
function memize( fn, options ) {
	var size = 0;

	/** @type {?MemizeCacheNode|undefined} */
	var head;

	/** @type {?MemizeCacheNode|undefined} */
	var tail;

	options = options || {};

	function memoized( /* ...args */ ) {
		var node = head,
			len = arguments.length,
			args, i;

		searchCache: while ( node ) {
			// Perform a shallow equality test to confirm that whether the node
			// under test is a candidate for the arguments passed. Two arrays
			// are shallowly equal if their length matches and each entry is
			// strictly equal between the two sets. Avoid abstracting to a
			// function which could incur an arguments leaking deoptimization.

			// Check whether node arguments match arguments length
			if ( node.args.length !== arguments.length ) {
				node = node.next;
				continue;
			}

			// Check whether node arguments match arguments values
			for ( i = 0; i < len; i++ ) {
				if ( node.args[ i ] !== arguments[ i ] ) {
					node = node.next;
					continue searchCache;
				}
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if ( node !== head ) {
				// As tail, shift to previous. Must only shift if not also
				// head, since if both head and tail, there is no previous.
				if ( node === tail ) {
					tail = node.prev;
				}

				// Adjust siblings to point to each other. If node was tail,
				// this also handles new tail's empty `next` assignment.
				/** @type {MemizeCacheNode} */ ( node.prev ).next = node.next;
				if ( node.next ) {
					node.next.prev = node.prev;
				}

				node.next = head;
				node.prev = null;
				/** @type {MemizeCacheNode} */ ( head ).prev = node;
				head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		// Create a copy of arguments (avoid leaking deoptimization)
		args = new Array( len );
		for ( i = 0; i < len; i++ ) {
			args[ i ] = arguments[ i ];
		}

		node = {
			args: args,

			// Generate the result from original function
			val: fn.apply( null, args ),
		};

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if ( head ) {
			head.prev = node;
			node.next = head;
		} else {
			// If no head, follows that there's no tail (at initial or reset)
			tail = node;
		}

		// Trim tail if we're reached max size and are pending cache insertion
		if ( size === /** @type {MemizeOptions} */ ( options ).maxSize ) {
			tail = /** @type {MemizeCacheNode} */ ( tail ).prev;
			/** @type {MemizeCacheNode} */ ( tail ).next = null;
		} else {
			size++;
		}

		head = node;

		return node.val;
	}

	memoized.clear = function() {
		head = null;
		tail = null;
		size = 0;
	};

	if ( process.env.NODE_ENV === 'test' ) {
		// Cache is not exposed in the public API, but used in tests to ensure
		// expected list progression
		memoized.getCache = function() {
			return [ head, tail, size ];
		};
	}

	// Ignore reason: There's not a clear solution to create an intersection of
	// the function with additional properties, where the goal is to retain the
	// function signature of the incoming argument and add control properties
	// on the return value.

	// @ts-ignore
	return memoized;
}

module.exports = memize;

}).call(this,require('_process'))
},{"_process":21}],21:[function(require,module,exports){
// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;

process.listeners = function (name) { return [] }

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };

},{}],22:[function(require,module,exports){
'use strict';

function _interopDefault (ex) { return (ex && (typeof ex === 'object') && 'default' in ex) ? ex['default'] : ex; }

var pluralForms = _interopDefault(require('@tannin/plural-forms'));

/**
 * Tannin constructor options.
 *
 * @typedef {Object} TanninOptions
 *
 * @property {string}   [contextDelimiter] Joiner in string lookup with context.
 * @property {Function} [onMissingKey]     Callback to invoke when key missing.
 */

/**
 * Domain metadata.
 *
 * @typedef {Object} TanninDomainMetadata
 *
 * @property {string}            [domain]       Domain name.
 * @property {string}            [lang]         Language code.
 * @property {(string|Function)} [plural_forms] Plural forms expression or
 *                                              function evaluator.
 */

/**
 * Domain translation pair respectively representing the singular and plural
 * translation.
 *
 * @typedef {[string,string]} TanninTranslation
 */

/**
 * Locale data domain. The key is used as reference for lookup, the value an
 * array of two string entries respectively representing the singular and plural
 * translation.
 *
 * @typedef {{[key:string]:TanninDomainMetadata|TanninTranslation,'':TanninDomainMetadata|TanninTranslation}} TanninLocaleDomain
 */

/**
 * Jed-formatted locale data.
 *
 * @see http://messageformat.github.io/Jed/
 *
 * @typedef {{[domain:string]:TanninLocaleDomain}} TanninLocaleData
 */

/**
 * Default Tannin constructor options.
 *
 * @type {TanninOptions}
 */
var DEFAULT_OPTIONS = {
	contextDelimiter: '\u0004',
	onMissingKey: null,
};

/**
 * Given a specific locale data's config `plural_forms` value, returns the
 * expression.
 *
 * @example
 *
 * ```
 * getPluralExpression( 'nplurals=2; plural=(n != 1);' ) === '(n != 1)'
 * ```
 *
 * @param {string} pf Locale data plural forms.
 *
 * @return {string} Plural forms expression.
 */
function getPluralExpression( pf ) {
	var parts, i, part;

	parts = pf.split( ';' );

	for ( i = 0; i < parts.length; i++ ) {
		part = parts[ i ].trim();
		if ( part.indexOf( 'plural=' ) === 0 ) {
			return part.substr( 7 );
		}
	}
}

/**
 * Tannin constructor.
 *
 * @class
 *
 * @param {TanninLocaleData} data      Jed-formatted locale data.
 * @param {TanninOptions}    [options] Tannin options.
 */
function Tannin( data, options ) {
	var key;

	/**
	 * Jed-formatted locale data.
	 *
	 * @name Tannin#data
	 * @type {TanninLocaleData}
	 */
	this.data = data;

	/**
	 * Plural forms function cache, keyed by plural forms string.
	 *
	 * @name Tannin#pluralForms
	 * @type {Object<string,Function>}
	 */
	this.pluralForms = {};

	/**
	 * Effective options for instance, including defaults.
	 *
	 * @name Tannin#options
	 * @type {TanninOptions}
	 */
	this.options = {};

	for ( key in DEFAULT_OPTIONS ) {
		this.options[ key ] = options !== undefined && key in options
			? options[ key ]
			: DEFAULT_OPTIONS[ key ];
	}
}

/**
 * Returns the plural form index for the given domain and value.
 *
 * @param {string} domain Domain on which to calculate plural form.
 * @param {number} n      Value for which plural form is to be calculated.
 *
 * @return {number} Plural form index.
 */
Tannin.prototype.getPluralForm = function( domain, n ) {
	var getPluralForm = this.pluralForms[ domain ],
		config, plural, pf;

	if ( ! getPluralForm ) {
		config = this.data[ domain ][ '' ];

		pf = (
			config[ 'Plural-Forms' ] ||
			config[ 'plural-forms' ] ||
			// Ignore reason: As known, there's no way to document the empty
			// string property on a key to guarantee this as metadata.
			// @ts-ignore
			config.plural_forms
		);

		if ( typeof pf !== 'function' ) {
			plural = getPluralExpression(
				config[ 'Plural-Forms' ] ||
				config[ 'plural-forms' ] ||
				// Ignore reason: As known, there's no way to document the empty
				// string property on a key to guarantee this as metadata.
				// @ts-ignore
				config.plural_forms
			);

			pf = pluralForms( plural );
		}

		getPluralForm = this.pluralForms[ domain ] = pf;
	}

	return getPluralForm( n );
};

/**
 * Translate a string.
 *
 * @param {string}      domain   Translation domain.
 * @param {string|void} context  Context distinguishing terms of the same name.
 * @param {string}      singular Primary key for translation lookup.
 * @param {string=}     plural   Fallback value used for non-zero plural
 *                               form index.
 * @param {number=}     n        Value to use in calculating plural form.
 *
 * @return {string} Translated string.
 */
Tannin.prototype.dcnpgettext = function( domain, context, singular, plural, n ) {
	var index, key, entry;

	if ( n === undefined ) {
		// Default to singular.
		index = 0;
	} else {
		// Find index by evaluating plural form for value.
		index = this.getPluralForm( domain, n );
	}

	key = singular;

	// If provided, context is prepended to key with delimiter.
	if ( context ) {
		key = context + this.options.contextDelimiter + singular;
	}

	entry = this.data[ domain ][ key ];

	// Verify not only that entry exists, but that the intended index is within
	// range and non-empty.
	if ( entry && entry[ index ] ) {
		return entry[ index ];
	}

	if ( this.options.onMissingKey ) {
		this.options.onMissingKey( singular, domain );
	}

	// If entry not found, fall back to singular vs. plural with zero index
	// representing the singular value.
	return index === 0 ? singular : plural;
};

module.exports = Tannin;

},{"@tannin/plural-forms":11}]},{},[2]);
