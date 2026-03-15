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
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Run upgrade steps.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_availability_stripepayment_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025061703) {
        $table = new xmldb_table('availability_stripepayment_payments');

        // Add stripe_payment_intent field (nullable).
        $field = new xmldb_field(
            'stripe_payment_intent',
            XMLDB_TYPE_CHAR,
            '255',
            null,   // unsigned (not used for CHAR)
            null,   // notnull = false (nullable)
            null,   // sequence
            null,   // default
            'stripe_session_id' // after this field
        );
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add index on cmid alone (helps queries filtering by activity).
        $index = new xmldb_index('cmid', XMLDB_INDEX_NOTUNIQUE, ['cmid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Add index on status (helps queries counting by status).
        $index = new xmldb_index('status', XMLDB_INDEX_NOTUNIQUE, ['status']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, 2025061703, 'availability', 'stripepayment');
    }

    if ($oldversion < 2026031501) {
        // Migrate availability JSON stored in course_modules and course_sections.
        // Previous plugin names were availability_stripe (type="stripe") and
        // availability_tagstripe (type="tagstripe"). Update both to "stripepayment".
        foreach (['stripe', 'tagstripe'] as $oldtype) {
            $DB->execute(
                "UPDATE {course_modules}
                    SET availability = REPLACE(availability, :find, :replace)
                  WHERE availability LIKE :like",
                ['find' => '"type":"' . $oldtype . '"', 'replace' => '"type":"stripepayment"', 'like' => '%"type":"' . $oldtype . '"%']
            );
            $DB->execute(
                "UPDATE {course_sections}
                    SET availability = REPLACE(availability, :find, :replace)
                  WHERE availability LIKE :like",
                ['find' => '"type":"' . $oldtype . '"', 'replace' => '"type":"stripepayment"', 'like' => '%"type":"' . $oldtype . '"%']
            );
        }

        upgrade_plugin_savepoint(true, 2026031501, 'availability', 'stripepayment');
    }

    if ($oldversion < 2026031503) {
        // Migrate plugin config from previous component names to availability_stripepayment.
        // Settings were stored under 'availability_stripe' and 'availability_tagstripe'.
        $config_keys = ['enabled', 'stripe_publishable_key', 'stripe_secret_key', 'webhook_secret', 'accounts_email'];
        foreach (['availability_stripe', 'availability_tagstripe'] as $old_plugin) {
            foreach ($config_keys as $key) {
                $old_value = get_config($old_plugin, $key);
                if ($old_value !== false && $old_value !== '') {
                    // Only copy if the new plugin doesn't already have a value set.
                    $new_value = get_config('availability_stripepayment', $key);
                    if ($new_value === false || $new_value === '') {
                        set_config($key, $old_value, 'availability_stripepayment');
                    }
                }
            }
        }

        upgrade_plugin_savepoint(true, 2026031503, 'availability', 'stripepayment');
    }

    return true;
}
