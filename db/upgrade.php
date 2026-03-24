<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License.

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
 * @param int $oldversion Version we are upgrading from.
 * @return bool True on success.
 */
function xmldb_availability_stripepayment_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025061703) {

        $table = new xmldb_table('availability_stripepayment_payments');

        // Add stripe_payment_intent field.
        $field = new xmldb_field(
            'stripe_payment_intent',
            XMLDB_TYPE_CHAR,
            '255',
            null,
            null,
            null,
            null,
            'stripe_session_id'
        );

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Index on cmid.
        $index = new xmldb_index('cmid', XMLDB_INDEX_NOTUNIQUE, ['cmid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Index on status.
        $index = new xmldb_index('status', XMLDB_INDEX_NOTUNIQUE, ['status']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, 2025061703, 'availability', 'stripepayment');
    }

    if ($oldversion < 2026031501) {

        foreach (['stripe', 'tagstripe'] as $oldtype) {

            $DB->execute(
                "UPDATE {course_modules}
                    SET availability = REPLACE(availability, :find, :replace)
                  WHERE availability LIKE :like",
                [
                    'find' => '"type":"' . $oldtype . '"',
                    'replace' => '"type":"stripepayment"',
                    'like' => '%"type":"' . $oldtype . '"%',
                ]
            );

            $DB->execute(
                "UPDATE {course_sections}
                    SET availability = REPLACE(availability, :find, :replace)
                  WHERE availability LIKE :like",
                [
                    'find' => '"type":"' . $oldtype . '"',
                    'replace' => '"type":"stripepayment"',
                    'like' => '%"type":"' . $oldtype . '"%',
                ]
            );
        }

        upgrade_plugin_savepoint(true, 2026031501, 'availability', 'stripepayment');
    }

    if ($oldversion < 2026031503) {

        $configkeys = [
            'enabled',
            'stripe_publishable_key',
            'stripe_secret_key',
            'webhook_secret',
            'accounts_email',
        ];

        foreach (['availability_stripe', 'availability_tagstripe'] as $oldplugin) {
            foreach ($configkeys as $key) {

                $oldvalue = get_config($oldplugin, $key);

                if ($oldvalue !== false && $oldvalue !== '') {

                    $newvalue = get_config('availability_stripepayment', $key);

                    if ($newvalue === false || $newvalue === '') {
                        set_config($key, $oldvalue, 'availability_stripepayment');
                    }
                }
            }
        }

        upgrade_plugin_savepoint(true, 2026031503, 'availability', 'stripepayment');
    }

    if ($oldversion < 2026031900) {
        upgrade_plugin_savepoint(true, 2026031900, 'availability', 'stripepayment');
    }

    if ($oldversion < 2026031901) {
        upgrade_plugin_savepoint(true, 2026031901, 'availability', 'stripepayment');
    }

    return true;
}
