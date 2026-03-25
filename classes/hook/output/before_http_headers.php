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
 * @copyright  2025 Andrei Toma
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_stripepayment\hook\output;

use core\hook\output\before_http_headers as hook;

/**
 * Hook handler for before HTTP headers.
 */
class before_http_headers extends hook {
    /**
     * Callback executed before HTTP headers are sent.
     *
     * @return void
     */
    public static function callback(): void {
        // Intentionally left empty.
        // Moodle automatically loads styles.css via plugin system.
    }
}
