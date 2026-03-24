// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * AMD module for the Stripe availability condition form editor.
 *
 * Registers M.availability_stripepayment.form so that the Moodle
 * core_availability form can call getNode / fillValue / fillErrors on it.
 * This replaces the legacy YUI module in yui/src/form/js/form.js.
 *
 * @module     availability_stripepayment/form
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    'use strict';

    return {
        /**
         * Initialise the plugin form object.
         *
         * Called by frontend.php via $PAGE->requires->js_call_amd().
         *
         * @param {Object} currencies Map of ISO code => localised currency name
         */
        init: function(currencies) {
            M.availability_stripepayment = M.availability_stripepayment || {};

            M.availability_stripepayment.form = {
                currencies: currencies,
                addedEvents: false,

                /**
                 * Return the form node for this condition.
                 *
                 * @param {Object} json Saved condition data
                 * @return {HTMLElement}
                 */
                getNode: function(json) {
                    var options = '';
                    for (var code in this.currencies) {
                        if (!Object.prototype.hasOwnProperty.call(this.currencies, code)) {
                            continue;
                        }
                        var selected = (json.currency === code) ? ' selected="selected"' : '';
                        options += '<option value="' + code + '"' + selected + '>'
                            + this.currencies[code] + '</option>';
                    }

                    var html = '<span><div class="container-fluid">'
                        + '<div class="row mt-3">'
                        + '<div class="col"><label for="stripecurrency">'
                        + M.util.get_string('currency', 'availability_stripepayment')
                        + '</label></div>'
                        + '<div class="col">'
                        + '<select class="form-control" name="currency" id="stripecurrency">'
                        + options + '</select>'
                        + '</div></div>'
                        + '<div class="row mt-3">'
                        + '<div class="col"><label for="stripeamount">'
                        + M.util.get_string('amount', 'availability_stripepayment')
                        + '</label></div>'
                        + '<div class="col">'
                        + '<input class="form-control" name="amount" type="text" id="stripeamount" />'
                        + '</div></div>'
                        + '<div class="row mt-3">'
                        + '<div class="col"><label for="stripeitemname">'
                        + M.util.get_string('itemname', 'availability_stripepayment')
                        + '</label></div>'
                        + '<div class="col">'
                        + '<input class="form-control" name="itemname" type="text" id="stripeitemname" />'
                        + '</div></div>'
                        + '</div></span>';

                    var wrapper = document.createElement('div');
                    wrapper.innerHTML = html;
                    var node = wrapper.firstChild;

                    if (json.amount !== undefined) {
                        node.querySelector('input[name=amount]').value = json.amount;
                    }
                    if (json.itemname !== undefined) {
                        node.querySelector('input[name=itemname]').value = json.itemname;
                    }

                    if (!M.availability_stripepayment.form.addedEvents) {
                        M.availability_stripepayment.form.addedEvents = true;
                        document.addEventListener('change', function(e) {
                            var target = e.target;
                            if (!target.closest('.availability_stripepayment')) {
                                return;
                            }
                            if (target.matches('select[name=currency]')
                                || target.matches('input[name=amount]')
                                || target.matches('input[name=itemname]')) {
                                M.core_availability.form.update();
                            }
                        });
                    }

                    return node;
                },

                /**
                 * Populate the JSON value object from the form node.
                 *
                 * @param {Object}      value JSON value to populate
                 * @param {HTMLElement} node  Form node
                 */
                fillValue: function(value, node) {
                    value.currency = node.querySelector('select[name=currency]').value;
                    value.amount = this.getValue('amount', node);
                    value.itemname = node.querySelector('input[name=itemname]').value;
                },

                /**
                 * Read and parse a numeric input field.
                 *
                 * @param  {string}      field Field name
                 * @param  {HTMLElement} node  Form node
                 * @return {number|string} Parsed number or raw string if invalid
                 */
                getValue: function(field, node) {
                    var value = node.querySelector('input[name=' + field + ']').value;
                    if (!(/^[0-9]+([.,][0-9]+)?$/.test(value))) {
                        return value;
                    }
                    return parseFloat(value.replace(',', '.'));
                },

                /**
                 * Push validation errors for any invalid fields.
                 *
                 * @param {Array}       errors Array to push error strings to
                 * @param {HTMLElement} node   Form node
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
