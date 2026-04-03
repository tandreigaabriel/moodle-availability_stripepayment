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
 * Initiates a Stripe Checkout session for an activity payment.
 *
 * @package    availability_stripepayment
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/lib.php');

$cmid = required_param('cmid', PARAM_INT);

require_login();
require_sesskey();

$cm = get_coursemodule_from_id('', $cmid);
if (!$cm) {
    throw new moodle_exception('invalidcoursemodule');
}

$context = context_module::instance($cmid);
require_course_login($cm->course);

$context = context_module::instance($cmid);

// Custom capability check.
if (!has_capability('availability/stripepayment:pay', $context, $USER)) {
    redirect(new moodle_url('/course/view.php', ['id' => $cm->course]));
}
$PAGE->set_url('/availability/condition/stripepayment/payment.php', ['cmid' => $cmid]);
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');

$config = get_config('availability_stripepayment');
if (empty($config->stripe_secret_key)) {
    redirect(
        new moodle_url('/course/view.php', ['id' => $cm->course]),
        get_string('stripe_not_configured', 'availability_stripepayment'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

if (availability_stripepayment_has_paid($USER->id, $cmid)) {
    redirect(
        new moodle_url('/course/view.php', ['id' => $cm->course]),
        get_string('already_paid', 'availability_stripepayment'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$recentpending = $DB->record_exists_select(
    'availability_stripepayment_payments',
    'userid = :userid AND cmid = :cmid AND status = :status AND timecreated > :cutoff',
    [
        'userid' => $USER->id,
        'cmid' => $cmid,
        'status' => 'pending',
        'cutoff' => time() - 120,
    ]
);

if ($recentpending) {
    redirect(
        new moodle_url('/course/view.php', ['id' => $cm->course]),
        get_string('payment_in_progress', 'availability_stripepayment'),
        null,
        \core\output\notification::NOTIFY_WARNING
    );
}

$modinfo = get_fast_modinfo($cm->course);
$info = new \core_availability\info_module($modinfo->get_cm($cmid));
$tree = $info->get_availability_tree();

$stripecondition = availability_stripepayment_find_condition($tree);
if (!$stripecondition) {
    redirect(
        new moodle_url('/course/view.php', ['id' => $cm->course]),
        get_string('no_condition_found', 'availability_stripepayment'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

$amount = (int) $stripecondition->amount;
$currency = strtolower($stripecondition->currency ?? 'usd');
$itemname = $stripecondition->itemname ?: $cm->name;

if ($amount <= 0) {
    redirect(new moodle_url('/course/view.php', ['id' => $cm->course]));
}

if (!preg_match('/^[a-z]{3}$/', $currency)) {
    redirect(new moodle_url('/course/view.php', ['id' => $cm->course]));
}

$zerodecimalcurrencies = [
    'BIF',
    'CLP',
    'DJF',
    'GNF',
    'JPY',
    'KMF',
    'KRW',
    'MGA',
    'PYG',
    'RWF',
    'UGX',
    'VND',
    'VUV',
    'XAF',
    'XOF',
    'XPF',
];

$stripeunitamount = in_array(strtoupper($currency), $zerodecimalcurrencies)
    ? $amount
    : $amount * 100;

\Stripe\Stripe::setApiKey($config->stripe_secret_key);

try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [
            [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $itemname,
                    ],
                    'unit_amount' => $stripeunitamount,
                ],
                'quantity' => 1,
            ],
        ],
        'mode' => 'payment',
        'success_url' => $CFG->wwwroot .
            '/availability/condition/stripepayment/success.php?sessionid={CHECKOUT_SESSION_ID}&cmid=' . $cmid,
        'cancel_url' => $CFG->wwwroot . '/course/view.php?id=' . $cm->course,
        'metadata' => [
            'userid' => $USER->id,
            'cmid' => $cmid,
            'courseid' => $cm->course,
        ],
        'customer_email' => $USER->email,
    ]);

    availability_stripepayment_create_payment(
        $USER->id,
        $cmid,
        $cm->course,
        $session->id,
        $amount,
        strtoupper($currency)
    );

    redirect($session->url);
} catch (Exception $e) {
    debugging('Stripe error: ' . $e->getMessage(), DEBUG_DEVELOPER);

    redirect(
        new moodle_url('/course/view.php', ['id' => $cm->course]),
        get_string('payment_failed', 'availability_stripepayment'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}
