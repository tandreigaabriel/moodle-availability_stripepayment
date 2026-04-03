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
 * AMD module for the payment success page countdown redirect.
 *
 * @module     availability_stripepayment/success
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    'use strict';

    return {
        /**
         * Start a countdown and redirect to the activity when it reaches zero.
         *
         * @param {string} redirect_url   URL to redirect to after countdown
         * @param {string} str_second     Localised singular "second"
         * @param {string} str_seconds    Localised plural "seconds"
         * @param {string} str_prefix     Localised prefix text (e.g. "Redirecting in")
         * @param {string} str_dot        Localised trailing character (e.g. ".")
         */
        init: function(redirect_url, str_second, str_seconds, str_prefix, str_dot) {
            var countdown = 5;
            var el = document.getElementById('stripe-countdown');

            if (!el) {
                return;
            }

            var tick = function() {
                el.textContent = str_prefix + ' ' + countdown + ' '
                    + (countdown !== 1 ? str_seconds : str_second) + str_dot;

                if (countdown <= 0) {
                    window.location.href = redirect_url;
                } else {
                    countdown--;
                    setTimeout(tick, 1000);
                }
            };

            tick();
        }
    };
});
