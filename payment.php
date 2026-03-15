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
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
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

require_course_login($cm->course);

$PAGE->set_url('/availability/condition/stripepayment/payment.php', ['cmid' => $cmid]);
$PAGE->set_context(context_module::instance($cmid));
$PAGE->set_pagelayout('incourse');

// Check if Stripe is configured
$config = get_config('availability_stripepayment');
if (empty($config->stripe_secret_key)) {
    redirect(new moodle_url('/course/view.php', ['id' => $cm->course]), 
             'Stripe is not configured. Please contact administrator.', 
             null, 
             \core\output\notification::NOTIFY_ERROR);
}

// Check if already paid
if (availability_stripepayment_has_paid($USER->id, $cmid)) {
    redirect(new moodle_url('/course/view.php', ['id' => $cm->course]),
             'You have already paid for this content.',
             null,
             \core\output\notification::NOTIFY_SUCCESS);
}

// Prevent duplicate Stripe sessions: if a pending record was created in the
// last 2 minutes the user probably double-clicked or opened two tabs.
$recent_pending = $DB->record_exists_select(
    'availability_stripepayment_payments',
    'userid = :userid AND cmid = :cmid AND status = :status AND timecreated > :cutoff',
    [
        'userid' => $USER->id,
        'cmid'   => $cmid,
        'status' => 'pending',
        'cutoff' => time() - 120,
    ]
);
if ($recent_pending) {
    redirect(
        new moodle_url('/course/view.php', ['id' => $cm->course]),
        get_string('payment_in_progress', 'availability_stripepayment'),
        null,
        \core\output\notification::NOTIFY_WARNING
    );
}

// Get availability condition details
$modinfo = get_fast_modinfo($cm->course);
$info = new \core_availability\info_module($modinfo->get_cm($cmid));
$tree = $info->get_availability_tree();

$stripe_condition = availability_stripepayment_find_condition($tree);
if (!$stripe_condition) {
    redirect(new moodle_url('/course/view.php', ['id' => $cm->course]), 
             'No Stripe payment condition found for this activity. Please contact administrator.', 
             null, 
             \core\output\notification::NOTIFY_ERROR);
}

$amount = (int) $stripe_condition->amount;
$currency = strtolower($stripe_condition->currency ?? 'usd');
$itemname = $stripe_condition->itemname ?: $cm->name;

// Validate amount
if ($amount <= 0) {
    redirect(new moodle_url('/course/view.php', ['id' => $cm->course]),
             'Invalid payment amount configured. Please contact administrator.',
             null,
             \core\output\notification::NOTIFY_ERROR);
}

// Validate currency (basic 3-letter ISO check)
if (!preg_match('/^[a-z]{3}$/', $currency)) {
    redirect(new moodle_url('/course/view.php', ['id' => $cm->course]),
             'Invalid currency configured. Please contact administrator.',
             null,
             \core\output\notification::NOTIFY_ERROR);
}

// Amount is stored in major currency units (e.g. 30 = £30.00).
// Stripe expects the smallest unit (cents/pence), so multiply by 100 for
// standard currencies. Zero-decimal currencies (JPY etc.) are sent as-is.
$zero_decimal_currencies = ['BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','UGX','VND','VUV','XAF','XOF','XPF'];
$stripe_unit_amount = in_array(strtoupper($currency), $zero_decimal_currencies) ? $amount : $amount * 100;

// Create Stripe checkout session
\Stripe\Stripe::setApiKey($config->stripe_secret_key);

try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => $currency,
                'product_data' => [
                    'name' => $itemname,
                    'description' => 'Access to ' . $cm->name . ' in ' . $COURSE->fullname,
                ],
                'unit_amount' => $stripe_unit_amount,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $CFG->wwwroot . '/availability/condition/stripepayment/success.php?session_id={CHECKOUT_SESSION_ID}&cmid=' . $cmid,
        'cancel_url' => $CFG->wwwroot . '/course/view.php?id=' . $cm->course,
        'metadata' => [
            'userid' => $USER->id,
            'cmid' => $cmid,
            'courseid' => $cm->course,
            'moodle_site' => $CFG->wwwroot,
            'purpose' => 'assignment',
        ],
        'customer_email' => $USER->email,
    ]);
    
    // Save payment record
    availability_stripepayment_create_payment($USER->id, $cmid, $cm->course, $session->id, $amount, strtoupper($currency));
    
    // Redirect to Stripe
    redirect($session->url);
    
} catch (\Stripe\Exception\CardException $e) {
    error_log('Stripe card error: ' . $e->getMessage());
    redirect(new moodle_url('/course/view.php', ['id' => $cm->course]), 
             'Payment failed: Card declined', 
             null, 
             \core\output\notification::NOTIFY_ERROR);
             
} catch (\Stripe\Exception\InvalidRequestException $e) {
    error_log('Stripe invalid request: ' . $e->getMessage());
    redirect(new moodle_url('/course/view.php', ['id' => $cm->course]), 
             'Payment configuration error. Please contact support.', 
             null, 
             \core\output\notification::NOTIFY_ERROR);
             
} catch (Exception $e) {
    error_log('Stripe general error: ' . $e->getMessage());
    redirect(new moodle_url('/course/view.php', ['id' => $cm->course]), 
             'Payment failed. Please try again.', 
             null, 
             \core\output\notification::NOTIFY_ERROR);
}
