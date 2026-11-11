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
 * Manually grant a student access to an activity without going through Stripe.
 *
 * @package    availability_stripepayment
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/lib.php');

$cmid = required_param('cmid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);

require_sesskey();

$cm = get_coursemodule_from_id('', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$context = context_module::instance($cmid);
$reporturl = new moodle_url('/availability/condition/stripepayment/activity_report.php', ['cmid' => $cmid]);

require_login($course, false, $cm);
require_capability('moodle/course:manageactivities', $context);

$student = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

if (availability_stripepayment_has_paid($userid, $cmid)) {
    redirect(
        $reporturl,
        get_string('studentalreadyhasaccess', 'availability_stripepayment'),
        null,
        \core\output\notification::NOTIFY_WARNING
    );
}

$modinfo = get_fast_modinfo($course);
$info = new \core_availability\info_module($modinfo->get_cm($cmid));
$tree = $info->get_availability_tree();
$stripecondition = availability_stripepayment_find_condition($tree);

$amount = $stripecondition ? (int) $stripecondition->amount : 0;
$currency = $stripecondition ? strtoupper($stripecondition->currency ?? 'usd') : 'USD';

$payment = new stdClass();
$payment->userid = $userid;
$payment->cmid = $cmid;
$payment->courseid = $course->id;
$payment->stripe_session_id = 'manual-' . bin2hex(random_bytes(16));
$payment->stripe_payment_intent = null;
$payment->amount = $amount;
$payment->currency = $currency;
$payment->status = 'completed';
$payment->source = 'manual';
$payment->grantedby = $USER->id;
$payment->timecreated = time();
$payment->timemodified = time();

$paymentid = $DB->insert_record('availability_stripepayment_payments', $payment);

$cache = \cache::make('core', 'coursemodinfo');
$cache->delete($course->id);

\availability_stripepayment\event\payment_completed::create([
    'objectid' => $paymentid,
    'context' => $context,
    'relateduserid' => $userid,
    'other' => ['source' => 'manual'],
])->trigger();

redirect(
    $reporturl,
    get_string('grantedaccessto', 'availability_stripepayment', fullname($student)),
    null,
    \core\output\notification::NOTIFY_SUCCESS
);
