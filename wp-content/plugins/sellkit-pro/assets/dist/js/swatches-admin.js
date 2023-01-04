(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var swatchesAdmin = function swatchesAdmin() {
  jQuery(function ($) {
    // Image Upload
    var imageSwatchFields = function imageSwatchFields() {
      // Uploading files
      var frame;
      $('.artbees-was-image-picker__upload').on('click', function (event) {
        event.preventDefault();
        var $imageSwatchUpload = $(this),
            $imageSwatchWrapper = $imageSwatchUpload.closest('.artbees-was-image-picker'),
            $imageSwatchField = $imageSwatchWrapper.find('.artbees-was-image-picker__field'),
            $imageSwatchPreview = $imageSwatchWrapper.find('.artbees-was-image-picker__preview'),
            $imageSwatchRemove = $imageSwatchWrapper.find('.artbees-was-image-picker__remove'); // Create the media frame.

        frame = wp.media.frames.frame = wp.media({
          title: $(this).data('title'),
          button: {
            text: $(this).data('button-text')
          },
          multiple: false
        }); // When an image is selected, run a callback.

        frame.on('select', function () {
          // We set multiple to false so only get one image from the uploader
          var attachment = frame.state().get('selection').first().toJSON(),
              attachmentUrl = typeof attachment.sizes.thumbnail !== 'undefined' ? attachment.sizes.thumbnail.url : attachment.url;
          $imageSwatchField.val(attachment.id);
          $imageSwatchPreview.html('<img src="' + attachmentUrl + '" class="attachment-thumbnail size-thumbnail">');
          $imageSwatchUpload.addClass('artbees-was-image-picker__upload--edit');
          $imageSwatchRemove.show();
        }); // Finally, open the modal

        frame.open();
      });
      $('.artbees-was-image-picker__remove').on('click', function (event) {
        event.preventDefault();
        var $imageSwatchWrapper = $(this).closest('.artbees-was-image-picker'),
            $imageSwatchField = $imageSwatchWrapper.find('.artbees-was-image-picker__field'),
            $imageSwatchPreview = $imageSwatchWrapper.find('.artbees-was-image-picker__preview'),
            $imageSwatchUpload = $imageSwatchWrapper.find('.artbees-was-image-picker__upload');
        $imageSwatchField.val('');
        $imageSwatchPreview.html('');
        $imageSwatchUpload.removeClass('artbees-was-image-picker__upload--edit');
        $(this).hide();
      });
    };

    function setupColorSwatchFields() {
      if ($('.artbees-color-swatch-picker').length !== 0) {
        $('.artbees-color-swatch-picker').wpColorPicker();
        $('.artbees-color-swatch').find('input').show();
      }
    }

    var CheckFieldWithParent = function CheckFieldWithParent(field) {
      var parent = $(field).find('.artbees-was-select, .artbees-was-product-select');

      if (!parent.data('is-parent') || parent.data('is-parent') && parent.val() === '1' || parent.data('is-parent') && parent.val() === 'column') {
        return;
      }

      var element = '.artbees-form-field[data-parent="' + parent.data('is-parent') + '"]';
      $(element).addClass('swatch-collapsed');
    };

    imageSwatchFields();
    setupColorSwatchFields();
    $('.artbees-form-field').each(function (index, field) {
      if ($(field).data('conditional') !== $('#attribute_type').val()) {
        return;
      }

      $(field).find('input').removeAttr('disabled');
      $(field).find('select').removeAttr('disabled');
      $(field).addClass('active');
      CheckFieldWithParent(field);
    });
    $('#attribute_type').on('change', function () {
      var valueSelected = this.value;
      $('.artbees-form-field').removeClass('active');
      $('.artbees-form-field').find('input').attr('disabled', 'disabled');
      $('.artbees-form-field').find('select').attr('disabled', 'disabled');
      $('.artbees-form-field').each(function (index, field) {
        if ($(field).data('conditional') !== valueSelected) {
          return;
        }

        $(field).addClass('active');
        $(field).find('input').removeAttr('disabled');
        $(field).find('select').removeAttr('disabled');
        CheckFieldWithParent(field);
      });
    });
    $('.artbees-was-select').on('change', function (event) {
      if (typeof $(event.currentTarget).data('is-parent') === 'undefined') {
        return;
      }

      var valueSelected = this.value;
      $('.artbees-form-field').each(function (index, field) {
        if (typeof $(field).data('parent') === 'undefined' || $(event.currentTarget).data('is-parent') !== $(field).data('parent')) {
          return;
        }

        $(field).removeClass('active');
        $(field).addClass('swatch-collapsed');

        if ((valueSelected > 0 || 'column' === valueSelected) && $(event.currentTarget).parents('.artbees-form-field').data('conditional') === $(field).data('conditional')) {
          $(field).addClass('active');
          $(field).removeClass('swatch-collapsed');
        }
      });
    }); // Product Edit Page Types Control

    $('.artbees-was-attribute-wrapper').each(function (index, wrapper) {
      $(wrapper).find('.artbees-form-field').each(function (indexField, field) {
        if ($(field).data('conditional') === $(wrapper).find('#product_attribute_type').val() && $(field).parents('.artbees-was-attribute-wrapper').data('taxonomy') === $(field).data('taxonomy')) {
          $(field).addClass('active');
          CheckFieldWithParent(field);
        }
      });
      $(wrapper).find('#product_attribute_type').on('change', function () {
        var valueSelected = this.value;
        $(wrapper).find('.artbees-form-field').removeClass('active');
        $(wrapper).find('.artbees-form-field').find('input').attr('disabled', 'disabled');
        $(wrapper).find('.artbees-form-field').find('select').attr('disabled', 'disabled');
        $(wrapper).find('.artbees-form-field').each(function (indexField, field) {
          if ($(field).data('conditional') !== valueSelected) {
            return;
          }

          $(field).addClass('active');
          $(field).find('input').removeAttr('disabled');
          $(field).find('select').removeAttr('disabled');
          CheckFieldWithParent(field);
        });
        var taxonomy = $(wrapper).data('taxonomy'),
            produtcId = $(wrapper).data('product-id');
        getProductTermsOptions(valueSelected, wrapper, taxonomy, produtcId);
      });
      $(wrapper).find('.artbees-was-product-select').on('change', function (event) {
        if (typeof $(event.currentTarget).data('is-parent') === 'undefined') {
          return;
        }

        var valueSelected = this.value;
        $(wrapper).find('.artbees-form-field').each(function (indexField, field) {
          if (typeof $(field).data('parent') === 'undefined' || $(event.currentTarget).data('is-parent') !== $(field).data('parent')) {
            return;
          }

          $(field).removeClass('active');
          $(field).addClass('swatch-collapsed');

          if ((valueSelected > 0 || 'column' === valueSelected) && $(event.currentTarget).parents('.artbees-form-field').data('conditional') === $(field).data('conditional') && $(wrapper).data('taxonomy') === $(field).data('taxonomy')) {
            $(field).addClass('active');
            $(field).removeClass('swatch-collapsed');
          }
        });
      });
    });

    function getProductTermsOptions(value, wrapper, taxonomy, produtcId) {
      wp.ajax.post({
        action: 'artbees_product_swatches_generate_product_options',
        swatch_type: value,
        terms_taxonomy: taxonomy,
        product_id: produtcId
      }).done(function (data) {
        $(wrapper).find('#artbees-was-terms-options').html(data);
        setupColorSwatchFields();
        imageSwatchFields();
      });
    }

    var swatchesClearCache = function swatchesClearCache() {
      caches.keys().then( // eslint-disable-line
      function (keyList) {
        return Promise.all(keyList.map(function (key) {
          return caches["delete"](key);
        } // eslint-disable-line
        ));
      });
    };

    $('.edit-tags-php #submit').on('click', function () {
      setTimeout(function () {
        if ($('.artbees-color-swatch button').length !== 0) {
          $('.artbees-color-swatch button').removeAttr('style');
          swatchesClearCache();
        }

        if ($('.artbees-was-image-picker .artbees-was-image-picker__preview').length !== 0) {
          var imagePickerWrapper = $('.artbees-was-image-picker'),
              imagePicker = imagePickerWrapper.find('.artbees-was-image-picker__preview');
          imagePicker.find('img').remove();
          imagePickerWrapper.find('.artbees-was-image-picker__upload--edit').removeClass('artbees-was-image-picker__upload--edit');
          imagePickerWrapper.find('.artbees-was-image-picker__remove').hide();
          swatchesClearCache();
        }
      }, 500);
    });
  });
};

var _default = swatchesAdmin;
exports["default"] = _default;
swatchesAdmin();

},{}]},{},[1]);
