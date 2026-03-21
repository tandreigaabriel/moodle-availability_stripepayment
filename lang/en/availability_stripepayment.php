<?php
// This file is part of Moodle - http://moodle.org/
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
 * Language strings for availability_stripepayment.
 *
 * @package    availability_stripepayment
 * @category   string
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Stripe payment restriction';
$string['title'] = 'Stripe payment';
$string['description'] = 'Require students to make a payment via Stripe before accessing the activity.';

// Admin settings.
$string['enable'] = 'Enable Stripe payments';
$string['enable_desc'] = 'Allow activities to require Stripe payments for access';
$string['stripe_publishable_key'] = 'Stripe publishable key';
$string['stripe_publishable_key_desc'] = 'Your Stripe publishable key from the Stripe dashboard';
$string['stripe_secret_key'] = 'Stripe secret key';
$string['stripe_secret_key_desc'] = 'Your Stripe secret key from the Stripe dashboard';
$string['webhook_secret'] = 'Webhook endpoint secret';
$string['webhook_secret_desc'] = 'The webhook endpoint secret from Stripe';
$string['accounts_email'] = 'Accounts email';
$string['accounts_email_desc'] = 'Email address to receive internal payment notifications (student receipts are sent automatically by Stripe)';
$string['settings_transactions_heading'] = 'Payment Transactions';
$string['settings_transactions_link'] = 'View all payment transactions';
$string['settings_transactions_admin'] = 'Stripe payment transactions';
$string['status_ok'] = 'ok';
$string['dot'] = '.';
// Condition configuration.
$string['amount'] = 'Amount';
$string['currency'] = 'Currency';
$string['itemname'] = 'Item name';
$string['itemname_help'] = 'The name shown to users during payment';

// User messages.
$string['not_paid'] = 'You have not paid for access to this activity.';
$string['payment_required_desc'] = 'Access to "{$a->item}" requires a payment of {$a->amount} {$a->currency}.';
$string['payment_required'] = 'Payment required';
$string['payment_completed'] = 'Payment completed';
$string['make_payment'] = 'Make payment';
$string['pay_now'] = 'Pay now';
$string['processing'] = 'Processing payment...';
$string['payment_successful'] = 'Payment successful! Redirecting...';
$string['payment_cancelled'] = 'Payment cancelled';
$string['already_paid'] = 'You have already paid for this activity.';

// Success page.
$string['payment_successful_title'] = 'Payment successful';
$string['payment_success_notification'] = 'Payment successful! You now have access to this content.';
$string['payment_details'] = 'Payment details';
$string['payment_detail_item'] = 'Item';
$string['payment_detail_amount'] = 'Amount';
$string['payment_detail_id'] = 'Payment ID';
$string['continue_to_activity'] = 'Continue to {$a}';

// Errors.
$string['stripe_not_configured'] = 'Stripe is not properly configured. Please contact an administrator.';
$string['payment_failed'] = 'Payment failed. Please try again.';
$string['error_not_configured'] = 'Stripe payment is not properly configured.';
$string['error_amount_required'] = 'Please enter a valid amount greater than 0.';
$string['error_itemname_required'] = 'Please enter an item name.';
$string['invalid_amount'] = 'Invalid payment amount';
$string['payment_not_found'] = 'Payment record not found.';
$string['activity_not_found'] = 'Activity not found.';
$string['no_condition_found'] = 'No Stripe payment condition found for this activity. Please contact administrator.';
$string['invalid_amount_admin'] = 'Invalid payment amount configured. Please contact administrator.';
$string['invalid_currency_admin'] = 'Invalid currency configured. Please contact administrator.';
$string['payment_failed_declined'] = 'Payment failed: card declined.';
$string['payment_config_error'] = 'Payment configuration error. Please contact support.';
$string['webhook_method_not_allowed'] = 'Method not allowed';
$string['webhook_empty_payload'] = 'Empty payload';
$string['webhook_missing_signature'] = 'Missing Stripe signature';
$string['webhook_secret_not_configured'] = 'Webhook secret not configured';
$string['webhook_invalid_payload'] = 'Invalid payload';
$string['webhook_invalid_signature'] = 'Invalid signature';
$string['webhook_error'] = 'Webhook error';
$string['webhook_already_processed'] = 'Already processed';
$string['webhook_payment_not_completed'] = 'Payment not completed';

// Admin interface.
$string['transactionsreport'] = 'Stripe payment transactions';
$string['managetransactions'] = 'Manage Stripe payment transactions';
$string['id'] = 'ID';
$string['transactionid'] = 'Transaction ID';
$string['totalpayments'] = 'Total payments';
$string['viewinstripe'] = 'View in Stripe dashboard';
$string['copytransactionid'] = 'Copy full transaction ID';
$string['filteractive'] = 'Filtered by:';
$string['downloadcsv'] = 'Download CSV';
$string['allcourses'] = 'All courses';

// Capability

$string['stripepayment:managetransactions'] = 'Manage Stripe payment transactions';

// Misc.
$string['unknownactivity'] = 'Unknown activity';
$string['tablenotexist'] = 'Database table {$a} does not exist';
$string['payments'] = 'Payments';

// Activity payment report.
$string['paymentreport'] = 'Payment report';
$string['backtocourse'] = 'Back to course';
$string['completedpayments'] = 'Completed payments';
$string['pending'] = 'Pending';
$string['completed'] = 'Completed';
$string['failed'] = 'Failed';
$string['cancelled'] = 'Cancelled';
$string['expired'] = 'Expired';
$string['clearfilter'] = 'Clear filter';
$string['totalrevenue'] = 'Total revenue';
$string['payingstudents'] = 'Paying students';
$string['nopayments'] = 'No payments recorded for this activity yet.';
$string['student'] = 'Student';
$string['activitypaymentreport'] = 'View payment report';

// Privacy metadata.
$string['privacy:metadata:payments'] = 'Stripe payment records store data about payments made by users to access course activities.';
$string['privacy:metadata:payments:userid'] = 'The ID of the user who made the payment.';
$string['privacy:metadata:payments:courseid'] = 'The course the payment relates to.';
$string['privacy:metadata:payments:cmid'] = 'The activity (course module) the payment unlocks.';
$string['privacy:metadata:payments:sessionid'] = 'The Stripe Checkout session ID for the transaction.';
$string['privacy:metadata:payments:amount'] = 'The payment amount charged.';
$string['privacy:metadata:payments:currency'] = 'The currency used for the payment.';
$string['privacy:metadata:payments:status'] = 'The current status of the payment (e.g. completed, pending).';
$string['privacy:metadata:payments:timecreated'] = 'The time the payment record was created.';

// Scheduled task.
$string['task_cleanup_pending'] = 'Clean up expired pending payments';

// Duplicate session prevention.
$string['payment_in_progress'] = 'A payment is already in progress. Please wait a moment and then try again.';

// Countdown redirect.
$string['redirecting_prefix'] = 'Automatically redirecting in';
$string['second'] = 'second';
$string['seconds'] = 'seconds';
