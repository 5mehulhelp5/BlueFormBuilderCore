define([
    'jquery',
    'jquery/validate',
    'mage/translate'
], function ($) {
    'use strict';

    $.validator.addMethod(
        'validate-phone', function (value, element) {
            if (value.trim() && window.intlTelInputInstances) {
                return window.intlTelInputInstances[element.getAttribute('id')].isValidNumber();
            }
            return true;
        }, $.mage.__('Please enter a valid phone number.'));
});