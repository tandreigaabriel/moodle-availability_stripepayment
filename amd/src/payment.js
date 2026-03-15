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
 * AMD module for the Stripe availability payment button.
 *
 * Adds a visual loading state when the pay button is clicked so students
 * get immediate feedback and cannot double-click to create duplicate sessions.
 *
 * @module     availability_stripepayment/payment
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {

    var initialized = false;

    return {
        /**
         * Initialise the click handler. Safe to call multiple times per page
         * (e.g. when several activities have the Stripe condition) — the handler
         * is only attached once via the `initialized` guard.
         */
        init: function() {
            if (initialized) {
                return;
            }
            initialized = true;

            document.addEventListener('click', function(e) {
                var btn = e.target.closest('[data-availability-stripe-pay]');
                if (!btn) {
                    return;
                }
                // Disable the button to prevent duplicate clicks / sessions.
                btn.classList.add('disabled');
                btn.setAttribute('aria-disabled', 'true');
                btn.setAttribute('aria-busy', 'true');
            });
        }
    };
});
