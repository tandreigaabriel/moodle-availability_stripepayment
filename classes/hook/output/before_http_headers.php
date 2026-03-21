<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License...

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

class before_http_headers extends hook
{
    public static function callback(): void
    {
        // Intentionally left empty.
        // Moodle automatically loads styles.css via plugin system.
    }
}
