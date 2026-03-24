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
 * Front-end class.
 *
 * @package    availability_stripepayment
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_stripepayment;

defined('MOODLE_INTERNAL') || die();

class frontend extends \core_availability\frontend {

    /**
     * Return true always — any teacher/admin can add this condition.
     *
     * @param stdClass $course
     * @param \cm_info $cm
     * @param \section_info $section
     * @return bool
     */
    protected function allow_add($course, \cm_info $cm = null, \section_info $section = null) {
        return true;
    }

    /**
     * Load the AMD form module instead of the legacy YUI module.
     *
     * Overrides \core_availability\frontend::include_javascript() to use
     * availability_stripepayment/form (amd/src/form.js) so that the plugin
     * does not depend on YUI.
     *
     * @param \stdClass       $course
     * @param \cm_info|null   $cm
     * @param \section_info|null $section
     */
    public function include_javascript($course, \cm_info $cm = null, \section_info $section = null) {
        global $PAGE;

        $currencies = \get_string_manager()->get_list_of_currencies();
        $PAGE->requires->strings_for_js(['currency', 'amount', 'itemname'], 'availability_stripepayment');
        $PAGE->requires->js_call_amd('availability_stripepayment/form', 'init', [$currencies]);
    }
}
