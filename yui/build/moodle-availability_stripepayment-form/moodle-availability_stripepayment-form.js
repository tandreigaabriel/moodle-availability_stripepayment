YUI.add('moodle-availability_stripepayment-form', function (Y, NAME) {

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
 * Legacy YUI module for the Stripe availability condition form editor.
 *
 * This file is retained for reference only. The active implementation has
 * been migrated to the AMD module at amd/src/form.js, loaded via
 * frontend.php::include_javascript().
 *
 * @module     moodle-availability_stripepayment-form
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.availability_stripepayment = M.availability_stripepayment || {};

/**
 * @class M.availability_stripepayment.form
 * @extends M.core_availability.plugin
 */
M.availability_stripepayment.form = Y.Object(M.core_availability.plugin);

/**
 * Initialises this plugin.
 *
 * @method initInner
 * @param {Array} currencies Array of currency_code => localised string
 */
M.availability_stripepayment.form.initInner = function(currencies) {
    this.currencies = currencies;
};

M.availability_stripepayment.form.getNode = function(json) {
    var selected_string = '';
    var currencies_options = '';
    for (var curr in this.currencies) {
        if (json.currency === curr) {
            selected_string = ' selected="selected" ';
        } else {
            selected_string = '';
        }
        currencies_options += '<option value="' + curr + '" ' + selected_string + ' >';
        currencies_options += this.currencies[curr];
        currencies_options += '</option>';
    }

    var html = '<div class="container-fluid">';
    html += '<div class="row mt-3">';
    html += '<div class="col"><label for="stripecurrency">';
    html += M.util.get_string('currency', 'availability_stripepayment');
    html += '</label></div><div class="col"><select class="form-control" name="currency" id="stripecurrency" />' +
            currencies_options + '</select></div></div>';

    html += '<div class="row mt-3">';
    html += '<div class="col"><label for="stripeamount">';
    html += M.util.get_string('amount', 'availability_stripepayment');
    html += '</label></div><div class="col"><input class="form-control" name="amount" type="text" id="stripeamount" /></div></div>';

    html += '<div class="row mt-3">';
    html += '<div class="col"><label for="stripeitemname">';
    html += M.util.get_string('itemname', 'availability_stripepayment');
    html += '</label></div><div class="col"><input class="form-control" name="itemname"' +
            ' type="text" id="stripeitemname" /></div></div>';
    html += '</div>';

    var node = Y.Node.create('<span>' + html + '</span>');

    // Set initial values based on the value from the JSON data in Moodle
    // database. This will have values undefined if creating a new one.
    if (json.amount) {
        node.one('input[name=amount]').set('value', json.amount);
    }
    if (json.itemname) {
        node.one('input[name=itemname]').set('value', json.itemname);
    }

    // Add event handlers (first time only).
    if (!M.availability_stripepayment.form.addedEvents) {
        M.availability_stripepayment.form.addedEvents = true;

        var root = Y.one('.availability-field');
        root.delegate('change', function() {
            M.core_availability.form.update();
        }, '.availability_stripepayment select[name=currency]');

        root.delegate('change', function() {
                // The key point is this update call. This call will update
                // the JSON data in the hidden field in the form, so that it
                // includes the new value of the checkbox.
                M.core_availability.form.update();
        }, '.availability_stripepayment input');
    }

    return node;
};

M.availability_stripepayment.form.fillValue = function(value, node) {
    // This function gets passed the node (from above) and a value
    // object. Within that object, it must set up the correct values
    // to use within the JSON data in the form. Should be compatible
    // with the structure used in the __construct and save functions
    // within condition.php.
    value.currency = node.one('select[name=currency]').get('value');
    value.amount = this.getValue('amount', node);
    value.itemname = node.one('input[name=itemname]').get('value');
};

/**
 * Gets the numeric value of an input field. Supports decimal points (using
 * dot or comma).
 *
 * @method getValue
 * @return {Number|String} Value of field as number or string if not valid
 */
M.availability_stripepayment.form.getValue = function(field, node) {
    // Get field value.
    var value = node.one('input[name=' + field + ']').get('value');

    // If it is not a valid positive number, return false.
    if (!(/^[0-9]+([.,][0-9]+)?$/.test(value))) {
        return value;
    }

    // Replace comma with dot and parse as floating-point.
    var result = parseFloat(value.replace(',', '.'));
    return result;
};

M.availability_stripepayment.form.fillErrors = function(errors, node) {
    var value = {};
    this.fillValue(value, node);

    if ((value.amount !== undefined && typeof(value.amount) === 'string') || value.amount <= 0 ) {
        errors.push('availability_stripepayment:error_amount_required');
    }
    if (value.itemname === '') {
        errors.push('availability_stripepayment:error_itemname_required');
    }
};


}, '@VERSION@', {"requires": ["base", "node", "event", "moodle-core_availability-form"]});
