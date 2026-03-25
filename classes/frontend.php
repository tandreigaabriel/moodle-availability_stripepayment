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
 * Front-end class for Stripe availability condition.
 *
 * @package    availability_stripepayment
 * @copyright  2025 Andrei Toma
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_stripepayment;

/**
 * Frontend handler for Stripe availability condition.
 */
class frontend extends \core_availability\frontend {
    /**
     * Pre-load language strings into M.str so M.util.get_string() works in the YUI module.
     *
     * Called by core_availability::include_all_javascript() for all enabled plugins.
     *
     * @return string[]
     */
    protected function get_javascript_strings() {
        return ['currency', 'amount', 'itemname'];
    }

    /**
     * Return parameters passed to the YUI module's initInner() function.
     *
     * The first (and only) element is the full currency list, which the YUI
     * module's initInner(currencies) stores and uses to build the dropdown.
     *
     * @param \stdClass $course Course object.
     * @param \cm_info|null $cm Course module.
     * @param \section_info|null $section Section info.
     * @return array
     */
    protected function get_javascript_init_params($course, ?\cm_info $cm = null, ?\section_info $section = null) {
        unset($course, $cm, $section);
        return [\get_string_manager()->get_list_of_currencies()];
    }

    /**
     * Allow adding this condition.
     *
     * @param \stdClass $course Course object.
     * @param \cm_info|null $cm Course module.
     * @param \section_info|null $section Section info.
     * @return bool
     */
    protected function allow_add($course, ?\cm_info $cm = null, ?\section_info $section = null) {
        unset($course, $cm, $section);

        return true;
    }

    /**
     * Include AMD JavaScript.
     *
     * @param \stdClass $course Course object.
     * @param \cm_info|null $cm Course module.
     * @param \section_info|null $section Section info.
     */
    public function include_javascript($course, ?\cm_info $cm = null, ?\section_info $section = null) {
        global $PAGE;

        unset($course, $cm, $section);

        $currencies = \get_string_manager()->get_list_of_currencies();

        $PAGE->requires->js_call_amd(
            'availability_stripepayment/form',
            'init',
            [
                $currencies,
                [
                    'currency' => get_string('currency', 'availability_stripepayment'),
                    'amount' => get_string('amount', 'availability_stripepayment'),
                    'itemname' => get_string('itemname', 'availability_stripepayment'),
                ],
            ]
        );
    }
}
