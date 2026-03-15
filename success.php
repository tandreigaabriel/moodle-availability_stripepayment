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
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once(__DIR__ . '/vendor/autoload.php');

$session_id = required_param('session_id', PARAM_ALPHANUMEXT);
$cmid       = optional_param('cmid', 0, PARAM_INT);

require_login();

global $DB, $USER;

$payment = $DB->get_record('availability_stripepayment_payments', [
    'stripe_session_id' => $session_id,
    'userid'            => $USER->id,
]);

if (!$payment) {
    redirect(new moodle_url('/my'),
             'Payment record not found.',
             null,
             \core\output\notification::NOTIFY_ERROR);
}

$cm = get_coursemodule_from_id('', $payment->cmid);
if (!$cm) {
    redirect(new moodle_url('/course/view.php', ['id' => $payment->courseid]),
             'Activity not found.',
             null,
             \core\output\notification::NOTIFY_ERROR);
}

// If the webhook hasn't fired yet, confirm payment directly with Stripe
// and mark it complete immediately so the redirect works straight away.
if ($payment->status !== 'completed') {
    $config = get_config('availability_stripepayment');
    if (!empty($config->stripe_secret_key)) {
        try {
            \Stripe\Stripe::setApiKey($config->stripe_secret_key);
            $stripe_session = \Stripe\Checkout\Session::retrieve($session_id);

            if ($stripe_session->payment_status === 'paid') {
                $payment->status       = 'completed';
                $payment->timemodified = time();
                $DB->update_record('availability_stripepayment_payments', $payment);

                // Clear the availability cache so access is granted on redirect.
                try {
                    $cache = \cache::make('core', 'coursemodinfo');
                    $cache->delete($payment->courseid);
                } catch (Exception $e) {
                    // Non-critical — cache will expire naturally.
                }
            }
        } catch (Exception $e) {
            // Stripe verification failed — the webhook will handle it.
            error_log('[availability_stripepayment] Success page Stripe verify error: ' . $e->getMessage());
        }
    }
}

$course = $DB->get_record('course', ['id' => $payment->courseid]);

$PAGE->set_url('/availability/condition/stripepayment/success.php', ['session_id' => $session_id]);
$PAGE->set_context(context_course::instance($payment->courseid));
$PAGE->set_title('Payment Successful');
$PAGE->set_heading($course->fullname);
$PAGE->requires->css(new moodle_url('/availability/condition/stripepayment/styles.css'));

$redirect_url = new moodle_url('/mod/' . $cm->modname . '/view.php', ['id' => $cm->id]);

echo $OUTPUT->header();

echo $OUTPUT->notification('Payment successful! You now have access to this content.', 'success');

echo html_writer::start_div('payment-success-details');
echo html_writer::tag('h3', 'Payment Details');
echo html_writer::start_tag('ul');
echo html_writer::tag('li', '<strong>Item:</strong> ' . s($cm->name));
echo html_writer::tag('li', '<strong>Amount:</strong> ' . number_format($payment->amount, 2) . ' ' . s($payment->currency));
echo html_writer::tag('li', '<strong>Payment ID:</strong> ' . s($session_id));
echo html_writer::end_tag('ul');
echo html_writer::end_div();

echo html_writer::div(
    html_writer::link($redirect_url, 'Continue to ' . s($cm->name), ['class' => 'btn btn-primary btn-lg']),
    'payment-continue-button'
);

echo html_writer::div('', 'payment-redirect-countdown', ['id' => 'stripe-countdown']);

echo html_writer::script("
    var countdown = 5;
    var el = document.getElementById('stripe-countdown');
    var redirectUrl = " . json_encode($redirect_url->out(false)) . ";

    function updateCountdown() {
        el.textContent = 'Automatically redirecting in ' + countdown + ' second' + (countdown !== 1 ? 's' : '') + '...';
        if (countdown <= 0) {
            window.location.href = redirectUrl;
        } else {
            countdown--;
            setTimeout(updateCountdown, 1000);
        }
    }
    updateCountdown();
");

echo $OUTPUT->footer();
