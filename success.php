<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Payment success page — verifies payment and redirects to the activity.
 *
 * @package    availability_stripepayment
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/lib.php');

$session_id = required_param('session_id', PARAM_ALPHANUMEXT);
$cmid = optional_param('cmid', 0, PARAM_INT);

require_login();

global $DB, $USER;

$payment = $DB->get_record('availability_stripepayment_payments', [
    'stripe_session_id' => $session_id,
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
require_capability('moodle/course:view', $context);

// Stripe verification fallback
if ($payment->status !== 'completed') {
    $config = get_config('availability_stripepayment');
    if (!empty($config->stripe_secret_key)) {
        try {
            \Stripe\Stripe::setApiKey($config->stripe_secret_key);
            $stripe_session = \Stripe\Checkout\Session::retrieve($session_id);

            if ($stripe_session->payment_status === 'paid') {
                $payment->status = 'completed';
                $payment->timemodified = time();
                $DB->update_record('availability_stripepayment_payments', $payment);

                try {
                    $cache = \cache::make('core', 'coursemodinfo');
                    $cache->delete($payment->courseid);
                } catch (Exception $e) {
                }

                availability_stripepayment_send_payment_notifications($payment, $stripe_session);
            }
        } catch (Exception $e) {
            error_log('[availability_stripepayment] Stripe verify error: ' . $e->getMessage());
        }
    }
}

$course = $DB->get_record('course', ['id' => $payment->courseid]);

$PAGE->set_url('/availability/condition/stripepayment/success.php', ['session_id' => $session_id]);
$PAGE->set_title(get_string('payment_successful_title', 'availability_stripepayment'));
$PAGE->set_heading($course->fullname);

$redirect_url = new moodle_url('/mod/' . $cm->modname . '/view.php', ['id' => $cm->id]);

echo $OUTPUT->header();

// ROOT WRAPPER (CSS FIX)
echo html_writer::start_div('availability_stripepayment');

echo $OUTPUT->notification(get_string('payment_success_notification', 'availability_stripepayment'), 'success');

echo html_writer::start_div('availability-stripepayment-success-details');
echo html_writer::tag('h3', get_string('payment_details', 'availability_stripepayment'));
echo html_writer::start_tag('ul');
echo html_writer::tag('li', '<strong>' . get_string('payment_detail_item', 'availability_stripepayment') . ':</strong> ' . s($cm->name));
echo html_writer::tag('li', '<strong>' . get_string('payment_detail_amount', 'availability_stripepayment') . ':</strong> ' . number_format($payment->amount, 2) . ' ' . s($payment->currency));
echo html_writer::tag('li', '<strong>' . get_string('payment_detail_id', 'availability_stripepayment') . ':</strong> ' . s($session_id));
echo html_writer::end_tag('ul');
echo html_writer::end_div();

echo html_writer::div(
    html_writer::link(
        $redirect_url,
        get_string('continue_to_activity', 'availability_stripepayment', s($cm->name)),
        ['class' => 'btn btn-primary btn-lg']
    ),
    'availability-stripepayment-continue-button'
);

echo html_writer::div('', 'availability-stripepayment-redirect-countdown', ['id' => 'stripe-countdown']);

$str_second = get_string('second', 'availability_stripepayment');
$str_seconds = get_string('seconds', 'availability_stripepayment');
$str_prefix = get_string('redirecting_prefix', 'availability_stripepayment');
$str_dot = get_string('dot', 'availability_stripepayment');

echo html_writer::script("
    var countdown = 5;
    var el = document.getElementById('stripe-countdown');
    var redirectUrl = " . json_encode($redirect_url->out(false)) . ";
    var strSecond = " . json_encode($str_second) . ";
    var strSeconds = " . json_encode($str_seconds) . ";
    var strPrefix = " . json_encode($str_prefix) . ";
    var strDot = " . json_encode($str_dot) . ";

    function updateCountdown() {
        el.textContent = strPrefix + ' ' + countdown + ' ' + (countdown !== 1 ? strSeconds : strSecond) + strDot;
        if (countdown <= 0) {
            window.location.href = redirectUrl;
        } else {
            countdown--;
            setTimeout(updateCountdown, 1000);
        }
    }
    updateCountdown();
");

// close wrapper
echo html_writer::end_div();

echo $OUTPUT->footer();
