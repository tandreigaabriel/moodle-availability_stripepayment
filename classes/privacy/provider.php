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
 * Privacy provider for availability_stripepayment.
 *
 * @package    availability_stripepayment
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_stripepayment\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;

/**
 * Privacy provider implementation.
 */
class provider implements \core_privacy\local\metadata\provider {

    /**
     * Returns metadata about stored user data.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {

        $collection->add_database_table(
            'availability_stripepayment_payments',
            [
                'userid'            => 'privacy:metadata:payments:userid',
                'courseid'          => 'privacy:metadata:payments:courseid',
                'cmid'              => 'privacy:metadata:payments:cmid',
                'stripe_session_id' => 'privacy:metadata:payments:sessionid',
                'amount'            => 'privacy:metadata:payments:amount',
                'currency'          => 'privacy:metadata:payments:currency',
                'status'            => 'privacy:metadata:payments:status',
                'timecreated'       => 'privacy:metadata:payments:timecreated',
            ],
            'privacy:metadata:payments'
        );

        return $collection;
    }
}
