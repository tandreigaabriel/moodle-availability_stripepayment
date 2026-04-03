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
 * YUI module for the Stripe availability condition form editor.
 *
 * Registers M.availability_stripepayment.form so that the core_availability
 * framework can call getNode/fillValue/fillErrors on it.
 *
 * NOTE: The plugin loads this via AMD (amd/src/form.js) using the
 * include_javascript() override in frontend.php. This YUI source file
 * exists for completeness and for any future YUI-based build.
 *
 * @module     moodle-availability_stripepayment-form
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.availability_stripepayment = M.availability_stripepayment || {};

M.availability_stripepayment.form = Y.Object(M.core_availability.plugin);

/**
 * Initialise the form plugin with the currency list.
 *
 * Called by M.core_availability.form.init() via get_javascript_init_params().
 * Strings are pre-loaded into M.str via get_javascript_strings() and
 * accessed through M.util.get_string() in getNode().
 *
 * @param {Object} currencies Map of currency code -> display name.
 */
M.availability_stripepayment.form.initInner = function(currencies) {
    this.currencies = currencies;
};

/**
 * Build and return the YUI Node for this condition's form fields.
 *
 * @param {Object} json Existing condition data from the database.
 * @return {Y.Node}
 */
M.availability_stripepayment.form.getNode = function(json) {
    var self = this;

    // Build currency dropdown options.
    var options = '';
    Y.Object.each(self.currencies, function(name, code) {
        var selected = (json.currency === code) ? ' selected="selected"' : '';
        options += '<option value="' + code + '"' + selected + '>'
            + name + '</option>';
    });

    var html = '<span><div class="container-fluid">'

        // Currency selector.
        + '<div class="row mt-3">'
        + '<div class="col"><label for="stripecurrency">'
        + M.util.get_string('currency', 'availability_stripepayment') + '</label></div>'
        + '<div class="col">'
        + '<select class="form-control" name="currency" id="stripecurrency">'
        + options + '</select>'
        + '</div></div>'

        // Amount input.
        + '<div class="row mt-3">'
        + '<div class="col"><label for="stripeamount">'
        + M.util.get_string('amount', 'availability_stripepayment') + '</label></div>'
        + '<div class="col">'
        + '<input class="form-control" name="amount" type="text" id="stripeamount" />'
        + '</div></div>'

        // Item name input.
        + '<div class="row mt-3">'
        + '<div class="col"><label for="stripeitemname">'
        + M.util.get_string('itemname', 'availability_stripepayment') + '</label></div>'
        + '<div class="col">'
        + '<input class="form-control" name="itemname" type="text" id="stripeitemname" />'
        + '</div></div>'

        + '</div></span>';

    var node = Y.Node.create(html);

    // Populate existing values.
    if (json.amount !== undefined) {
        node.one('input[name=amount]').set('value', json.amount);
    }
    if (json.itemname !== undefined) {
        node.one('input[name=itemname]').set('value', json.itemname);
    }

    // Attach change listeners once per page load.
    if (!M.availability_stripepayment.form.addedEvents) {
        M.availability_stripepayment.form.addedEvents = true;
        var root = Y.one('#fitem_id_availabilityconditionsjson');
        root.delegate('change', function() {
            M.core_availability.form.update();
        }, '.availability_stripepayment select, .availability_stripepayment input');
    }

    return node;
};

/**
 * Populate the JSON value object from the form node's current input values.
 *
 * @param {Object} value Object to populate with condition data.
 * @param {Y.Node} node  The form node returned by getNode().
 */
M.availability_stripepayment.form.fillValue = function(value, node) {
    value.currency = node.one('select[name=currency]').get('value');

    var amountStr = node.one('input[name=amount]').get('value');
    if (/^[0-9]+([.,][0-9]+)?$/.test(amountStr)) {
        value.amount = parseFloat(amountStr.replace(',', '.'));
    } else {
        value.amount = amountStr;
    }

    value.itemname = node.one('input[name=itemname]').get('value');
};

/**
 * Validate form inputs and push error identifiers for any invalid fields.
 *
 * @param {Array}  errors List to push error strings into.
 * @param {Y.Node} node   The form node returned by getNode().
 */
M.availability_stripepayment.form.fillErrors = function(errors, node) {
    var value = {};
    this.fillValue(value, node);

    if ((value.amount !== undefined && typeof value.amount === 'string') || value.amount <= 0) {
        errors.push('availability_stripepayment:error_amount_required');
    }

    if (value.itemname === '') {
        errors.push('availability_stripepayment:error_itemname_required');
    }
};
