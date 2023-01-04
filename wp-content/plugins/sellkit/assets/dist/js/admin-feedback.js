(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

var adminFeedback = function adminFeedback() {
  var $ = jQuery;

  var showPopup = function showPopup() {
    var boxWrapper = '<div class="sellkit-deactivation-feedback-box-wrapper"></div>';
    $(boxWrapper).insertAfter('body');
    $('#sellkit-deactivate-feedback-dialog-wrapper').appendTo('.sellkit-deactivation-feedback-box-wrapper');
    $('.sellkit-deactivate-feedback-dialog-input-wrapper .sellkit-deactivate-feedback-dialog-input').on('click', function () {
      $('.sellkit-deactivate-feedback-dialog-input-wrapper').removeClass('active');
      $(this).parents('.sellkit-deactivate-feedback-dialog-input-wrapper').addClass('active');
    });
    $('.sellkit-deactivate-feedback-cancel-button').on('click', function () {
      hidePopup();
    });
    $('.sellkit-deactivation-feedback-box-wrapper').on('click', function (e) {
      if (e.target.className === 'sellkit-deactivation-feedback-box-wrapper') {
        hidePopup();
      }
    });
    $('#sellkit-deactivate-feedback-dialog-form').on('submit', function (e) {
      e.preventDefault();
      $('.sellkit-deactivate-feedback-footer-spinner-icon').removeClass('sellkit-hide');
      $('.sellkit-deactivate-feedback-footer').find('input').attr('disabled', 'disabled');
      wp.ajax.post('sellkit_deactivation_feedback', $('#sellkit-deactivate-feedback-dialog-form').serialize()).done(function () {
        window.location.href = $('#deactivate-sellkit').attr('href');
      });
    });
  };

  var hidePopup = function hidePopup() {
    $('.sellkit-deactivation-feedback-box-wrapper').addClass('sellkit-hide');
  };

  $('#deactivate-sellkit').on('click', function (e) {
    e.preventDefault();
    showPopup();
  });
};

document.addEventListener('DOMContentLoaded', function () {
  adminFeedback();
});

},{}]},{},[1]);
