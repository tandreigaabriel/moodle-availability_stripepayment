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
 * Payment success page — verifies payment and redirects to the activity.
 *
 * @package    availability_stripepayment
 * @copyright  2025 Andrei Toma
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/lib.php');

$sessionid = required_param('sessionid', PARAM_ALPHANUMEXT);
$cmid = optional_param('cmid', 0, PARAM_INT);

require_login();

global $DB, $USER, $PAGE, $OUTPUT;

$payment = $DB->get_record('availability_stripepayment_payments', [
    'stripe_session_id' => $sessionid,
    'userid' => $USER->id,
]);

if (!$payment) {
    redirect(
        new moodle_url('/my'),
        get_string('payment_not_found', 'availability_stripepayment'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

$cm = get_coursemodule_from_id('', $payment->cmid);

if (!$cm) {
    redirect(
        new moodle_url('/course/view.php', ['id' => $payment->courseid]),
        get_string('activity_not_found', 'availability_stripepayment'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

$context = context_course::instance($payment->courseid);
$PAGE->set_context($context);
require_course_login($payment->courseid);

// Stripe verification fallback (if status is not yet completed).
if ($payment->status !== 'completed') {
    $config = get_config('availability_stripepayment');

    if (!empty($config->stripe_secret_key)) {
        try {
            \Stripe\Stripe::setApiKey($config->stripe_secret_key);
            $stripesession = \Stripe\Checkout\Session::retrieve($sessionid);

            if ($stripesession->payment_status === 'paid') {
                $payment->status = 'completed';
                $payment->timemodified = time();
                $DB->update_record('availability_stripepayment_payments', $payment);

                // Clear course modinfo cache.
                rebuild_course_cache($payment->courseid, true);

                availability_stripepayment_send_payment_notifications($payment, $stripesession);
            }
        } catch (\Exception $e) {
            debugging('Stripe verify error: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }
}

$course = $DB->get_record('course', ['id' => $payment->courseid]);

$PAGE->set_url('/availability/condition/stripepayment/success.php', ['sessionid' => $sessionid]);
$PAGE->set_title(get_string('payment_successful_title', 'availability_stripepayment'));
$PAGE->set_heading($course->fullname);

$redirecturl = new moodle_url('/mod/' . $cm->modname . '/view.php', ['id' => $cm->id]);

echo $OUTPUT->header();

echo html_writer::start_div('availability_stripepayment');

echo $OUTPUT->notification(
    get_string('payment_success_notification', 'availability_stripepayment'),
    'success'
);

echo html_writer::start_div('availability-stripepayment-success-details');
echo html_writer::tag('h3', get_string('payment_details', 'availability_stripepayment'));
echo html_writer::start_tag('ul');

echo html_writer::tag(
    'li',
    '<strong>' . get_string('payment_detail_item', 'availability_stripepayment') .
    ':</strong> ' . s($cm->name)
);

echo html_writer::tag(
    'li',
    '<strong>' . get_string('payment_detail_amount', 'availability_stripepayment') .
    ':</strong> ' . number_format($payment->amount, 2) . ' ' . s($payment->currency)
);

echo html_writer::tag(
    'li',
    '<strong>' . get_string('payment_detail_id', 'availability_stripepayment') .
    ':</strong> ' . s($sessionid)
);

echo html_writer::end_tag('ul');
echo html_writer::end_div();

echo html_writer::div(
    html_writer::link(
        $redirecturl,
        get_string('continue_to_activity', 'availability_stripepayment', s($cm->name)),
        ['class' => 'btn btn-primary btn-lg']
    ),
    'availability-stripepayment-continue-button'
);

echo html_writer::div('', 'availability-stripepayment-redirect-countdown', [
    'id' => 'stripe-countdown',
]);

$PAGE->requires->js_call_amd('availability_stripepayment/success', 'init', [
    $redirecturl->out(false),
    get_string('second', 'availability_stripepayment'),
    get_string('seconds', 'availability_stripepayment'),
    get_string('redirecting_prefix', 'availability_stripepayment'),
    get_string('dot', 'availability_stripepayment'),
]);

echo html_writer::end_div();
echo $OUTPUT->footer();
