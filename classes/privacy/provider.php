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
 * Privacy provider for the Stripe availability condition.
 *
 * @package    availability_stripepayment
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_stripepayment\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy provider for the Stripe availability condition.
 *
 * @package    availability_stripepayment
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * Returns metadata about the data stored by this plugin.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'availability_stripepayment_payments',
            [
                'userid' => 'privacy:metadata:payments:userid',
                'courseid' => 'privacy:metadata:payments:courseid',
                'cmid' => 'privacy:metadata:payments:cmid',
                'stripe_session_id' => 'privacy:metadata:payments:sessionid',
                'amount' => 'privacy:metadata:payments:amount',
                'currency' => 'privacy:metadata:payments:currency',
                'status' => 'privacy:metadata:payments:status',
                'timecreated' => 'privacy:metadata:payments:timecreated',
            ],
            'privacy:metadata:payments',
        );

        return $collection;
    }

    /**
     * Get contexts for a user.
     *
     * @param int $userid
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        $contextlist->add_from_sql(
            "SELECT ctx.id
               FROM {context} ctx
               JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel
               JOIN {availability_stripepayment_payments} p ON p.cmid = cm.id
              WHERE p.userid = :userid",
            ['contextlevel' => CONTEXT_MODULE, 'userid' => $userid]
        );

        return $contextlist;
    }

    /**
     * Get users in context.
     *
     * @param \core_privacy\local\request\userlist $userlist
     * @return void
     */
    public static function get_users_in_context(userlist $userlist): void {
        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        $userlist->add_from_sql(
            'userid',
            "SELECT p.userid
               FROM {availability_stripepayment_payments} p
              WHERE p.cmid = :cmid",
            ['cmid' => $context->instanceid]
        );
    }

    /**
     * Export user data.
     *
     * @param approved_contextlist $contextlist
     * @return void
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        if ($contextlist->count() === 0) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_module) {
                continue;
            }

            $payments = $DB->get_records('availability_stripepayment_payments', [
                'userid' => $userid,
                'cmid' => $context->instanceid,
            ]);

            if (!$payments) {
                continue;
            }

            $data = [];

            foreach ($payments as $p) {
                $data[] = (object) [
                    'courseid' => $p->courseid,
                    'cmid' => $p->cmid,
                    'stripe_session_id' => $p->stripe_session_id,
                    'amount' => $p->amount,
                    'currency' => $p->currency,
                    'status' => $p->status,
                    'timecreated' => transform::datetime($p->timecreated),
                ];
            }

            writer::with_context($context)->export_data(
                [get_string('pluginname', 'availability_stripepayment')],
                (object) ['payments' => $data]
            );
        }
    }

    /**
     * Delete all users data in context.
     *
     * @param \context $context
     * @return void
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;

        if (!$context instanceof \context_module) {
            return;
        }

        $DB->delete_records('availability_stripepayment_payments', [
            'cmid' => $context->instanceid,
        ]);
    }

    /**
     * Delete user data.
     *
     * @param approved_contextlist $contextlist
     * @return void
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        if ($contextlist->count() === 0) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_module) {
                continue;
            }

            $DB->delete_records('availability_stripepayment_payments', [
                'userid' => $userid,
                'cmid' => $context->instanceid,
            ]);
        }
    }

    /**
     * Delete multiple users.
     *
     * @param approved_userlist $userlist
     * @return void
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        global $DB;

        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        [$usersql, $params] = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);

        $DB->delete_records_select(
            'availability_stripepayment_payments',
            "cmid = :cmid AND userid {$usersql}",
            array_merge(['cmid' => $context->instanceid], $params)
        );
    }
}
