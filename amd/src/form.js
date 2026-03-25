// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation.

/**
 * AMD module for the Stripe availability condition form editor.
 *
 * This module defines the UI for configuring the Stripe payment restriction.
 * It replaces the legacy YUI module and integrates with core_availability.
 *
 * IMPORTANT:
 * We use core/str to load language strings dynamically instead of relying
 * on PHP-passed strings, which can fail in some contexts.
 *
 * @module     availability_stripepayment/form
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/str'], function(Str) {
    'use strict';

    return {

        /**
         * Initialise the Stripe availability form.
         *
         * @param {Object} currencies Map of currency codes to names
         */
        init: function(currencies) {

            // Ensure namespace exists.
            M.availability_stripepayment = M.availability_stripepayment || {};

            /**
             * Form object used by Moodle core_availability.
             */
            M.availability_stripepayment.form = {

                currencies: currencies,
                addedEvents: false,

                /**
                 * Build and return the form node.
                 *
                 * @param {Object} json Existing condition data
                 * @return {Promise<HTMLElement>}
                 */
                getNode: function(json) {
                    var self = this;

                    // Load language strings safely using Moodle API.
                    return Str.get_strings([
                        {key: 'currency', component: 'availability_stripepayment'},
                        {key: 'amount', component: 'availability_stripepayment'},
                        {key: 'itemname', component: 'availability_stripepayment'}
                    ]).then(function(strings) {

                        var currencyLabel = strings[0];
                        var amountLabel   = strings[1];
                        var itemnameLabel = strings[2];

                        // Build currency dropdown options.
                        var options = '';
                        for (var code in self.currencies) {
                            if (!Object.prototype.hasOwnProperty.call(self.currencies, code)) {
                                continue;
                            }

                            var selected = (json.currency === code) ? ' selected="selected"' : '';
                            options += '<option value="' + code + '"' + selected + '>'
                                + self.currencies[code] + '</option>';
                        }

                        // Build HTML form structure.
                        var html = '<span><div class="container-fluid">'

                            // Currency
                            + '<div class="row mt-3">'
                            + '<div class="col"><label>' + currencyLabel + '</label></div>'
                            + '<div class="col">'
                            + '<select class="form-control" name="currency">'
                            + options + '</select>'
                            + '</div></div>'

                            // Amount
                            + '<div class="row mt-3">'
                            + '<div class="col"><label>' + amountLabel + '</label></div>'
                            + '<div class="col">'
                            + '<input class="form-control" name="amount" type="text" />'
                            + '</div></div>'

                            // Item name
                            + '<div class="row mt-3">'
                            + '<div class="col"><label>' + itemnameLabel + '</label></div>'
                            + '<div class="col">'
                            + '<input class="form-control" name="itemname" type="text" />'
                            + '</div></div>'

                            + '</div></span>';

                        // Convert HTML string to DOM node.
                        var wrapper = document.createElement('div');
                        wrapper.innerHTML = html;
                        var node = wrapper.firstChild;

                        // Populate existing values.
                        if (json.amount !== undefined) {
                            node.querySelector('input[name=amount]').value = json.amount;
                        }
                        if (json.itemname !== undefined) {
                            node.querySelector('input[name=itemname]').value = json.itemname;
                        }

                        // Attach change listeners once.
                        if (!self.addedEvents) {
                            self.addedEvents = true;

                            document.addEventListener('change', function(e) {
                                var target = e.target;

                                if (!target.closest('.availability_stripepayment')) {
                                    return;
                                }

                                if (target.matches('select[name=currency]')
                                    || target.matches('input[name=amount]')
                                    || target.matches('input[name=itemname]')) {

                                    // Notify Moodle form to update state.
                                    M.core_availability.form.update();
                                }
                            });
                        }

                        return node;
                    });
                },

                /**
                 * Populate JSON value from form inputs.
                 *
                 * @param {Object} value
                 * @param {HTMLElement} node
                 */
                fillValue: function(value, node) {
                    value.currency = node.querySelector('select[name=currency]').value;
                    value.amount = this.getValue('amount', node);
                    value.itemname = node.querySelector('input[name=itemname]').value;
                },

                /**
                 * Parse numeric input safely.
                 *
                 * @param {string} field
                 * @param {HTMLElement} node
                 * @return {number|string}
                 */
                getValue: function(field, node) {
                    var value = node.querySelector('input[name=' + field + ']').value;

                    // Validate number format.
                    if (!(/^[0-9]+([.,][0-9]+)?$/.test(value))) {
                        return value;
                    }

                    return parseFloat(value.replace(',', '.'));
                },

                /**
                 * Validate form values and push errors.
                 *
                 * @param {Array} errors
                 * @param {HTMLElement} node
                 */
                fillErrors: function(errors, node) {
                    var value = {};
                    this.fillValue(value, node);

                    if ((value.amount !== undefined && typeof value.amount === 'string')
                        || value.amount <= 0) {
                        errors.push('availability_stripepayment:error_amount_required');
                    }

                    if (value.itemname === '') {
                        errors.push('availability_stripepayment:error_itemname_required');
                    }
                }
            };
        }
    };
});
