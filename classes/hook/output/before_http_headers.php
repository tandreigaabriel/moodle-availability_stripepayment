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
 * Hook callback fired before HTTP headers are sent.
 *
 * @package    availability_stripepayment
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_stripepayment\hook\output;

defined('MOODLE_INTERNAL') || die();

use core\hook\output\before_http_headers as hook;

class before_http_headers extends hook {
    public static function callback(): void {
        global $PAGE;

        // Only inject on course view pages.
        if (strpos($PAGE->pagetype, 'course-view') !== 0) {
            return;
        }

        // Load plugin stylesheet. The AMD payment module is loaded on demand
        // by condition::get_either_description() only when a payment button
        // is actually rendered — no need to load it globally here.
        $PAGE->requires->css(new \moodle_url('/availability/condition/stripepayment/styles.css'));
    }
}
