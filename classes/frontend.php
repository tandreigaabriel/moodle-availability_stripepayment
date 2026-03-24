<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation.

/**
 * Front-end class.
 *
 * @package    availability_stripepayment
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_stripepayment;

defined('MOODLE_INTERNAL') || die();

class frontend extends \core_availability\frontend
{

    /**
     * Return true always — any teacher/admin can add this condition.
     *
     * @param stdClass $course
     * @param \cm_info|null $cm
     * @param \section_info|null $section
     * @return bool
     */
    protected function allow_add($course, \cm_info $cm = null, \section_info $section = null)
    {
        // Required by Moodle API but not used.
        unset($course, $cm, $section);

        return true;
    }

    /**
     * Load the AMD form module instead of the legacy YUI module.
     *
     * @param \stdClass $course
     * @param \cm_info|null $cm
     * @param \section_info|null $section
     */
    public function include_javascript($course, \cm_info $cm = null, \section_info $section = null)
    {
        global $PAGE;

        // Required by Moodle API but not used.
        unset($course, $cm, $section);

        $currencies = \get_string_manager()->get_list_of_currencies();

        $PAGE->requires->strings_for_js(
            ['currency', 'amount', 'itemname'],
            'availability_stripepayment'
        );

        $PAGE->requires->js_call_amd(
            'availability_stripepayment/form',
            'init',
            [$currencies]
        );
    }
}
