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
 * Manually refund a completed Stripe payment and revoke access.
 *
 * @package    availability_stripepayment
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/lib.php');

$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$payment = $DB->get_record('availability_stripepayment_payments', ['id' => $id], '*', MUST_EXIST);
$course = $DB->get_record('course', ['id' => $payment->courseid], '*', MUST_EXIST);
$context = context_course::instance($payment->courseid);

require_login($course);

if (
    !has_capability('availability/stripepayment:managetransactions', context_system::instance()) &&
    !has_capability('moodle/course:manageactivities', $context)
) {
    require_capability('availability/stripepayment:managetransactions', context_system::instance());
}

$returnurl = new moodle_url('/availability/condition/stripepayment/transactions.php');

$PAGE->set_url('/availability/condition/stripepayment/refund.php', ['id' => $id]);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('refund', 'availability_stripepayment'));

if ($payment->status !== 'completed' || $payment->source !== 'stripe' || empty($payment->stripe_payment_intent)) {
    redirect(
        $returnurl,
        get_string('refundnotavailable', 'availability_stripepayment'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

if (!$confirm) {
    echo $OUTPUT->header();
    echo $OUTPUT->confirm(
        get_string('refundconfirm', 'availability_stripepayment'),
        new moodle_url('/availability/condition/stripepayment/refund.php', ['id' => $id, 'confirm' => 1, 'sesskey' => sesskey()]),
        $returnurl
    );
    echo $OUTPUT->footer();
    exit;
}

require_sesskey();

$config = get_config('availability_stripepayment');

try {
    \Stripe\Stripe::setApiKey($config->stripe_secret_key);
    \Stripe\Refund::create(['payment_intent' => $payment->stripe_payment_intent]);

    $payment->status = 'refunded';
    $payment->timemodified = time();
    $DB->update_record('availability_stripepayment_payments', $payment);

    $cache = \cache::make('core', 'coursemodinfo');
    $cache->delete($payment->courseid);

    \availability_stripepayment\event\payment_refunded::create([
        'objectid' => $payment->id,
        'context' => context_module::instance($payment->cmid),
        'relateduserid' => $payment->userid,
    ])->trigger();

    redirect(
        $returnurl,
        get_string('refundsuccess', 'availability_stripepayment'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
} catch (Exception $e) {
    debugging('[availability_stripepayment] Refund error: ' . $e->getMessage(), DEBUG_DEVELOPER);

    redirect(
        $returnurl,
        get_string('refundfailed', 'availability_stripepayment', $e->getMessage()),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}
