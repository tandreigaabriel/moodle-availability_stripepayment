<?php
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
 * Upgrade steps for the Stripe availability condition.
 *
 * @package    availability_stripepayment
 * @copyright  2026 TAG Digital Solutions Ltd <https://tandreig.com>
 * @author     Andrei Toma
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute plugin upgrade.
 *
 * @param int $oldversion Version we are upgrading from.
 * @return bool True on success.
 */
function xmldb_availability_stripepayment_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026071200) {
        $table = new xmldb_table('availability_stripepayment_payments');

        $field = new xmldb_field('source', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'stripe', 'status');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('grantedby', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'source');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026071200, 'availability', 'stripepayment');
    }

    if ($oldversion < 2026111900) {
        // Prices like 9.99 were truncated to 9: store the amount with two decimals. Existing values are unchanged.
        $table = new xmldb_table('availability_stripepayment_payments');
        $field = new xmldb_field('amount', XMLDB_TYPE_NUMBER, '12, 2', null, XMLDB_NOTNULL, null, null, 'stripe_payment_intent');
        $dbman->change_field_type($table, $field);

        upgrade_plugin_savepoint(true, 2026111900, 'availability', 'stripepayment');
    }

    return true;
}
