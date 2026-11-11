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
 * Language strings for availability_stripepayment.
 *
 * @package    availability_stripepayment
 * @category   string
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['accounts_email'] = 'Accounts email';
$string['accounts_email_desc'] = 'Email address to receive internal payment notifications (student receipts are sent automatically by Stripe)';
$string['activity_not_found'] = 'Activity not found.';
$string['activitypaymentreport'] = 'View payment report';
$string['allcourses'] = 'All courses';
$string['already_paid'] = 'You have already paid for this activity.';
$string['amount'] = 'Amount';
$string['backtocourse'] = 'Back to course';
$string['cancelled'] = 'Cancelled';
$string['clearfilter'] = 'Clear filter';
$string['completed'] = 'Completed';
$string['completedpayments'] = 'Completed payments';
$string['continue_to_activity'] = 'Continue to {$a}';
$string['copytransactionid'] = 'Copy full transaction ID';
$string['currency'] = 'Currency';
$string['description'] = 'Require students to make a payment via Stripe before accessing the activity.';
$string['dot'] = '.';
$string['downloadcsv'] = 'Download CSV';
$string['email_accounts_html_footer'] = 'Customer receipt has been sent automatically by Stripe.';
$string['email_accounts_html_heading'] = '&#x2705; New payment received via Stripe.';
$string['email_accounts_html_title'] = 'New Payment Received';
$string['email_accounts_intro'] = 'New payment received via Stripe:';
$string['email_accounts_note'] = 'Note: Customer receipt sent automatically by Stripe.';
$string['email_accounts_row_activity'] = 'Activity';
$string['email_accounts_row_course'] = 'Course';
$string['email_accounts_row_date'] = 'Date';
$string['email_accounts_subject'] = 'New Payment - {$a}';
$string['email_student_body'] = 'Your payment was successful! You now have access to the activity.';
$string['email_student_subject'] = 'Payment confirmed - {$a}';
$string['enable'] = 'Enable Stripe payments';
$string['enable_desc'] = 'Allow activities to require Stripe payments for access';
$string['error_amount_required'] = 'Please enter a valid amount greater than 0.';
$string['error_itemname_required'] = 'Please enter an item name.';
$string['error_not_configured'] = 'Stripe payment is not properly configured.';
$string['event_payment_completed'] = 'Stripe payment completed';
$string['event_payment_refunded'] = 'Stripe payment refunded';
$string['expired'] = 'Expired';
$string['failed'] = 'Failed';
$string['filteractive'] = 'Filtered by:';
$string['grantaccess'] = 'Grant access';
$string['grantaccessconfirm'] = 'Grant {$a} access to this activity without requiring payment?';
$string['grantaccessdesc'] = 'Manually mark a student as paid without going through Stripe (e.g. a cash payment, scholarship, or goodwill access).';
$string['grantedaccessto'] = 'Access granted to {$a}.';
$string['grantedby'] = 'Granted by';
$string['id'] = 'ID';
$string['invalid_amount'] = 'Invalid payment amount';
$string['invalid_amount_admin'] = 'Invalid payment amount configured. Please contact administrator.';
$string['invalid_currency_admin'] = 'Invalid currency configured. Please contact administrator.';
$string['itemname'] = 'Item name';
$string['itemname_help'] = 'The name shown to users during payment';
$string['make_payment'] = 'Make payment';
$string['managetransactions'] = 'Manage Stripe payment transactions';
$string['no_condition_found'] = 'No Stripe payment condition found for this activity. Please contact administrator.';
$string['nopayments'] = 'No payments recorded for this activity yet.';
$string['nostudentstogrant'] = 'All enrolled students already have access.';
$string['not_paid'] = 'You have not paid for access to this activity.';
$string['pay_now'] = 'Pay now';
$string['payingstudents'] = 'Paying students';
$string['payment_cancelled'] = 'Payment cancelled';
$string['payment_completed'] = 'Payment completed';
$string['payment_config_error'] = 'Payment configuration error. Please contact support.';
$string['payment_detail_amount'] = 'Amount';
$string['payment_detail_id'] = 'Payment ID';
$string['payment_detail_item'] = 'Item';
$string['payment_details'] = 'Payment details';
$string['payment_failed'] = 'Payment failed. Please try again.';
$string['payment_failed_declined'] = 'Payment failed: card declined.';
$string['payment_in_progress'] = 'A payment is already in progress. Please wait a moment and then try again.';
$string['payment_not_found'] = 'Payment record not found.';
$string['payment_required'] = 'Payment required';
$string['payment_required_desc'] = 'Access to "{$a->item}" requires a payment of {$a->amount} {$a->currency}.';
$string['payment_success_notification'] = 'Payment successful! You now have access to this content.';
$string['payment_successful'] = 'Payment successful! Redirecting...';
$string['payment_successful_title'] = 'Payment successful';
$string['paymentreport'] = 'Payment report';
$string['payments'] = 'Payments';
$string['pending'] = 'Pending';
$string['pluginname'] = 'Stripe payment restriction';
$string['privacy:metadata:payments'] = 'Stripe payment records store data about payments made by users to access course activities.';
$string['privacy:metadata:payments:amount'] = 'The payment amount charged.';
$string['privacy:metadata:payments:cmid'] = 'The activity (course module) the payment unlocks.';
$string['privacy:metadata:payments:courseid'] = 'The course the payment relates to.';
$string['privacy:metadata:payments:grantedby'] = 'The ID of the staff member who manually granted access, if this payment was not made via Stripe.';
$string['privacy:metadata:payments:sessionid'] = 'The Stripe Checkout session ID for the transaction.';
$string['privacy:metadata:payments:source'] = 'Whether the payment was made via Stripe or manually granted by a teacher or admin.';
$string['privacy:metadata:payments:status'] = 'The current status of the payment (e.g. completed, pending).';
$string['privacy:metadata:payments:timecreated'] = 'The time the payment record was created.';
$string['privacy:metadata:payments:userid'] = 'The ID of the user who made the payment.';
$string['processing'] = 'Processing payment...';
$string['redirecting_prefix'] = 'Automatically redirecting in';
$string['refund'] = 'Refund';
$string['refundconfirm'] = 'Refund this payment via Stripe? The student will immediately lose access to this activity.';
$string['refunded'] = 'Refunded';
$string['refundfailed'] = 'Refund failed: {$a}';
$string['refundnotavailable'] = 'This payment cannot be refunded automatically (no linked Stripe charge).';
$string['refundsuccess'] = 'Payment refunded successfully.';
$string['second'] = 'second';
$string['seconds'] = 'seconds';
$string['selectstudent'] = 'Select student';
$string['settings_transactions_admin'] = 'Stripe payment transactions';
$string['settings_transactions_heading'] = 'Payment Transactions';
$string['settings_transactions_link'] = 'View all payment transactions';
$string['source'] = 'Source';
$string['sourcemanual'] = 'Manual';
$string['sourcestripe'] = 'Stripe';
$string['status_ok'] = 'ok';
$string['stripe_not_configured'] = 'Stripe is not properly configured. Please contact an administrator.';
$string['stripe_publishable_key'] = 'Stripe publishable key';
$string['stripe_publishable_key_desc'] = 'Your Stripe publishable key from the Stripe dashboard';
$string['stripe_secret_key'] = 'Stripe secret key';
$string['stripe_secret_key_desc'] = 'Your Stripe secret key from the Stripe dashboard';
$string['stripedashboard'] = '⧉ Stripe Dashboard';
$string['stripelink'] = '🔗 Stripe';
$string['stripepayment:managetransactions'] = 'Manage Stripe payment transactions';
$string['stripepayment:pay'] = 'Pay for course activities using Stripe';
$string['student'] = 'Student';
$string['studentalreadyhasaccess'] = 'This student already has access to this activity.';
$string['tablenotexist'] = 'Database table {$a} does not exist';
$string['task_cleanup_pending'] = 'Clean up expired pending payments';
$string['title'] = 'Stripe payment';
$string['totalpayments'] = 'Total payments';
$string['totalrevenue'] = 'Total revenue';
$string['transactionid'] = 'Transaction ID';
$string['transactionsreport'] = 'Stripe payment transactions';
$string['unknownactivity'] = 'Unknown activity';
$string['viewinstripe'] = 'View in Stripe dashboard';
$string['webhook_already_processed'] = 'Already processed';
$string['webhook_empty_payload'] = 'Empty payload';
$string['webhook_error'] = 'Webhook error';
$string['webhook_invalid_payload'] = 'Invalid payload';
$string['webhook_invalid_signature'] = 'Invalid signature';
$string['webhook_method_not_allowed'] = 'Method not allowed';
$string['webhook_missing_signature'] = 'Missing Stripe signature';
$string['webhook_payment_not_completed'] = 'Payment not completed';
$string['webhook_secret'] = 'Webhook endpoint secret';
$string['webhook_secret_desc'] = 'The webhook endpoint secret from Stripe';
$string['webhook_secret_not_configured'] = 'Webhook secret not configured';
