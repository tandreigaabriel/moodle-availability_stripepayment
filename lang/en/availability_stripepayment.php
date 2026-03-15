i
<?php
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Restriction by Stripe payment';
$string['title'] = 'Stripe payment';
$string['description'] = 'Require students to make a payment via Stripe before accessing the activity.';

// Admin settings
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

// Condition configuration
$string['amount'] = 'Amount';
$string['currency'] = 'Currency';
$string['itemname'] = 'Item name';
$string['itemname_help'] = 'The name shown to users during payment';

// User messages
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

// Errors
$string['stripe_not_configured'] = 'Stripe is not properly configured. Please contact an administrator.';
$string['payment_failed'] = 'Payment failed. Please try again.';
$string['error_not_configured'] = 'Stripe payment is not properly configured.';
$string['error_amount_required'] = 'Please enter a valid amount greater than 0.';
$string['error_itemname_required'] = 'Please enter an item name.';
$string['invalid_amount'] = 'Invalid payment amount';

// Admin interface
$string['transactionsreport'] = 'Stripe payment transactions';
$string['managetransactions'] = 'Manage Stripe transactions';
$string['id'] = 'ID';
$string['transactionid'] = 'Transaction ID';
$string['totalpayments'] = 'Total payments';
$string['viewinstripe'] = 'View in Stripe Dashboard';
$string['copytransactionid'] = 'Copy full transaction ID';
$string['filteractive'] = 'Filtered by:';

// Capabilities
$string['stripe:managetransactions'] = 'Manage Stripe payment transactions';

// Misc
$string['unknownactivity'] = 'Unknown Activity';
$string['tablenotexist'] = 'Database table {$a} does not exist';
$string['payments'] = 'payments';

// Activity payment report
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

// Privacy metadata
$string['privacy:metadata:payments'] = 'Stripe payment records store data about payments made by users to access course activities.';
$string['privacy:metadata:payments:userid'] = 'The ID of the user who made the payment.';
$string['privacy:metadata:payments:courseid'] = 'The course the payment relates to.';
$string['privacy:metadata:payments:cmid'] = 'The activity (course module) the payment unlocks.';
$string['privacy:metadata:payments:sessionid'] = 'The Stripe Checkout session ID for the transaction.';
$string['privacy:metadata:payments:amount'] = 'The payment amount charged.';
$string['privacy:metadata:payments:currency'] = 'The currency used for the payment.';
$string['privacy:metadata:payments:status'] = 'The current status of the payment (e.g. completed, pending).';
$string['privacy:metadata:payments:timecreated'] = 'The time the payment record was created.';

// Scheduled task
$string['task_cleanup_pending'] = 'Clean up expired pending payments';

// Duplicate session prevention
$string['payment_in_progress'] = 'A payment is already in progress. Please wait a moment and then try again.';
