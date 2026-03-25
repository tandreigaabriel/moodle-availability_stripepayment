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
 * Language strings are pre-loaded by frontend.php via js_call_amd and passed
 * to init() as the second argument, ensuring synchronous access in getNode().
 *
 * @module     availability_stripepayment/form
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {
    'use strict';

    return {

        /**
         * Initialise the Stripe availability form.
         *
         * @param {Object} currencies Map of currency codes to names
         * @param {Object} strings    Map of label strings pre-loaded from PHP
         */
        init: function(currencies, strings) {

            // Ensure namespace exists.
            M.availability_stripepayment = M.availability_stripepayment || {};

            /**
             * Form object used by Moodle core_availability.
             */
            M.availability_stripepayment.form = {

                currencies: currencies,
                strings: strings,
                addedEvents: false,

                /**
                 * Build and return the form node synchronously.
                 *
                 * @param {Object} json Existing condition data
                 * @return {HTMLElement}
                 */
                getNode: function(json) {
                    var self = this;

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

                    // Build HTML form structure using pre-loaded strings.
                    var html = '<span><div class="container-fluid">'

                        // Currency
                        + '<div class="row mt-3">'
                        + '<div class="col"><label for="stripecurrency">' + self.strings.currency + '</label></div>'
                        + '<div class="col">'
                        + '<select class="form-control" name="currency" id="stripecurrency">'
                        + options + '</select>'
                        + '</div></div>'

                        // Amount
                        + '<div class="row mt-3">'
                        + '<div class="col"><label for="stripeamount">' + self.strings.amount + '</label></div>'
                        + '<div class="col">'
                        + '<input class="form-control" name="amount" type="text" id="stripeamount" />'
                        + '</div></div>'

                        // Item name
                        + '<div class="row mt-3">'
                        + '<div class="col"><label for="stripeitemname">' + self.strings.itemname + '</label></div>'
                        + '<div class="col">'
                        + '<input class="form-control" name="itemname" type="text" id="stripeitemname" />'
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
