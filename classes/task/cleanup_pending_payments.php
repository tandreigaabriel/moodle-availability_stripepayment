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
 * Scheduled task to expire stale pending payments.
 *
 * @package    availability_stripepayment
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_stripepayment\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Marks pending payment records older than 48 hours as 'expired'.
 *
 * Stripe checkout sessions expire after 24 hours. Any record still 'pending'
 * after 48 hours was abandoned and will never be completed.
 */
class cleanup_pending_payments extends \core\task\scheduled_task {

    /**
     * Return the task name shown in the admin scheduled-tasks UI.
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_cleanup_pending', 'availability_stripepayment');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;

        $cutoff = time() - (48 * HOURSECS);

        $count = $DB->count_records_select(
            'availability_stripepayment_payments',
            "status = 'pending' AND timecreated < :cutoff",
            ['cutoff' => $cutoff]
        );

        if ($count === 0) {
            mtrace('availability_stripepayment cleanup: no stale pending payments found.');
            return;
        }

        $DB->set_field_select(
            'availability_stripepayment_payments',
            'status',
            'expired',
            "status = 'pending' AND timecreated < :cutoff",
            ['cutoff' => $cutoff]
        );

        mtrace("availability_stripepayment cleanup: marked {$count} stale pending payment(s) as expired.");
    }
}
