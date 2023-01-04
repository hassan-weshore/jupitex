(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var SwatchesFrontend = function SwatchesFrontend() {
  jQuery(function ($) {
    var compatibleWithThemes = function compatibleWithThemes(form) {
      var row = form.find('tr');

      if ($('body').hasClass('theme-claue')) {
        row = form.find('.product-variable');
      }

      return row;
    };

    var addDefaultLabel = function addDefaultLabel() {
      var form = $('.variations_form'),
          row = compatibleWithThemes(form);
      row.each(function (index, field) {
        var selectedValue = $(field).find('select').val(),
            selecteAttribute = $(field).find(".artbees-was-swatch[data-term=\"".concat(selectedValue, "\"]"));
        var attributeName = selecteAttribute.data('attribute_name');

        if (typeof attributeName === 'undefined') {
          attributeName = selectedValue;
        }

        $(field).find('.artbees-was-chosen-attribute').text(attributeName);

        if ($(field).find('select').val() === '') {
          var noSelectionText = $(field).find('.artbees-was-chosen-attribute').data('no-selection');
          $(field).find('.artbees-was-chosen-attribute').append('<span class="no-selection">' + noSelectionText + '</span>');
        }
      });
    };

    var addDefaultOption = function addDefaultOption() {
      var form = $('.variations_form'),
          row = compatibleWithThemes(form);
      row.each(function (index, current) {
        var selected = $(current).find('select').val(),
            options = form.find('.artbees-was-swatch'),
            wasFields = $(current).find('.artbees-was-swatch'),
            wasSelect = $(current).find('select');

        if ($(wasFields).find('.artbees-was-content').length === 0) {
          $(wasSelect).addClass('enabled-fields');
        }

        options.each(function (indexOption, option) {
          if ($(option).data('term').toString() === selected.toString()) {
            $(option).find('.artbees-was-content').addClass('selected-attribute');
          }
        });
      });
    };

    var removeExtraFields = function removeExtraFields() {
      var form = $('.variations_form'),
          row = compatibleWithThemes(form),
          wasFields = $(row).find('.artbees-was-swatch');
      wasFields.each(function (index, fields) {
        if ($(fields).find('.artbees-was-content').length === 0) {
          $(fields).parent().addClass('hidden-fields');
        }
      });
    };

    addDefaultLabel();
    addDefaultOption();
    removeExtraFields();
    $('.artbees-was-swatch').on('click', function (event) {
      if (typeof $(event.currentTarget).data('catalog') === 'undefined' || $(event.currentTarget).data('catalog') === 'image') {
        event.preventDefault();
        event.stopPropagation();
      }

      var swatch = $(this),
          term = swatch.data('term'),
          attribute = swatch.data('attribute'),
          parent = swatch.parents('.value').length !== 0 ? swatch.parents('.value') : swatch.parents('.product'),
          select = parent.find(attribute),
          selectedAttribute = swatch.find('.artbees-was-content'),
          otherAttribute = parent.find('.artbees-was-content');

      if (selectedAttribute.hasClass('selected-attribute')) {
        selectedAttribute.removeClass('selected-attribute');
        select.val('').trigger('change');
        select.trigger('click');
        select.trigger('focusin');
        return;
      }

      select.val(term).trigger('change');
      select.trigger('click');
      select.trigger('focusin');
      addDefaultLabel();
      otherAttribute.removeClass('selected-attribute');
      selectedAttribute.addClass('selected-attribute');
    });

    var shopPageSwatch = function shopPageSwatch(event) {
      if (typeof $(event.currentTarget).data('catalog') === 'undefined' || $(event.currentTarget).data('catalog') !== 'image') {
        return;
      }

      event.preventDefault();
      event.stopPropagation();
      var swatch = $(event.currentTarget),
          href = swatch.attr('href'),
          parent = swatch.parents('.product'),
          wcImageWrapper = parent.find('.jupiterx-wc-loop-product-image'),
          wcImage = wcImageWrapper.find('img');

      if (href.length === 0) {
        return;
      }

      wcImage.attr('src', href);
    };

    $(document).on('click', '.artbees-was-swatch', function (event) {
      shopPageSwatch(event);
    });
    $('.artbees-was-swatch').on('click', function (event) {
      shopPageSwatch(event);
    });
    $('.artbees-was-reset-options a').on('click', function () {
      $('.artbees-was-content').removeClass('selected-attribute');
    });
    $('.variations_form select').on('change', function () {
      addDefaultLabel();
    });
    $('.variations_form').on('woocommerce_update_variation_values', function () {
      var form = $(this),
          selects = form.find('select');
      selects.each(function (index, select) {
        var current = $(select),
            options = current.find('option'),
            selectParent = current.parent(),
            allSwatches = selectParent.find('a.artbees-was-swatch');
        allSwatches.addClass('artbees-was-swatch-disable');
        options.each(function (indexOptions, option) {
          var currentOption = $(option),
              parent = currentOption.parents('.variations_form'),
              items = parent.find('.artbees-was-swatches-item');
          var selected = null;

          if (!_.isEmpty(currentOption.attr('value'))) {
            selected = currentOption.attr('value');
            var validItems = items.find("[data-term=\"".concat(selected.toString(), "\"]"));

            if (validItems.length > 0) {
              validItems.removeClass('artbees-was-swatch-disable');
            }
          }
        });
      });
    });
  });
};

var _default = SwatchesFrontend;
exports["default"] = _default;
SwatchesFrontend();

},{}]},{},[1]);
