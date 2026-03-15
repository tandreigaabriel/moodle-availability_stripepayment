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
 * Library functions for the Stripe availability condition.
 *
 * @package    availability_stripepayment
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Add a "Stripe payments" link to the course Reports section in the navigation.
 * Called automatically by Moodle core for all plugins that define this function.
 *
 * @param navigation_node $navigation  The course navigation node
 * @param stdClass        $course
 * @param context         $context     Course context
 */
function availability_stripepayment_extend_navigation_course($navigation, $course, $context) {
    if (!has_capability('availability/stripepayment:managetransactions', $context) &&
        !has_capability('moodle/course:manageactivities', $context)) {
        return;
    }

    $url = new moodle_url('/availability/condition/stripepayment/transactions.php', ['courseid' => $course->id]);
    $navigation->add(
        get_string('transactionsreport', 'availability_stripepayment'),
        $url,
        navigation_node::TYPE_SETTING,
        null,
        'stripetransactions',
        new pix_icon('i/report', '')
    );
}

/**
 * Check if a user has a completed payment for a course module.
 *
 * @param int $userid
 * @param int $cmid
 * @return bool
 */
function availability_stripepayment_has_paid($userid, $cmid) {
    global $DB;
    return $DB->record_exists('availability_stripepayment_payments', [
        'userid' => $userid,
        'cmid'   => $cmid,
        'status' => 'completed',
    ]);
}

/**
 * Find the Stripe condition object inside a Moodle availability tree.
 *
 * @param mixed $tree
 * @return \availability_stripepayment\condition|null
 */
function availability_stripepayment_find_condition($tree) {
    if (!$tree) {
        return null;
    }

    if (is_a($tree, 'core_availability\\tree')) {
        $reflection = new ReflectionClass($tree);
        $prop = $reflection->getProperty('children');
        $prop->setAccessible(true);
        $children = $prop->getValue($tree);

        if ($children && is_array($children)) {
            foreach ($children as $child) {
                if (is_a($child, 'availability_stripepayment\\condition')) {
                    return $child;
                }
                $result = availability_stripepayment_find_condition($child);
                if ($result) {
                    return $result;
                }
            }
        }
    }

    if (is_object($tree) && isset($tree->type) && $tree->type === 'stripepayment') {
        return $tree;
    }

    if (is_object($tree) && isset($tree->c)) {
        foreach ($tree->c as $child) {
            $result = availability_stripepayment_find_condition($child);
            if ($result) {
                return $result;
            }
        }
    }

    return null;
}

/**
 * Insert a new pending payment record.
 *
 * @param int    $userid
 * @param int    $cmid
 * @param int    $courseid
 * @param string $session_id  Stripe Checkout session ID
 * @param int    $amount      Amount in major currency units (e.g. 35 = £35.00)
 * @param string $currency    ISO 4217 currency code (uppercase)
 * @return int  New record ID
 */
function availability_stripepayment_create_payment($userid, $cmid, $courseid, $session_id, $amount, $currency) {
    global $DB;

    $payment = new stdClass();
    $payment->userid           = $userid;
    $payment->cmid             = $cmid;
    $payment->courseid         = $courseid;
    $payment->stripe_session_id = $session_id;
    $payment->amount           = $amount;
    $payment->currency         = $currency;
    $payment->status           = 'pending';
    $payment->timecreated      = time();
    $payment->timemodified     = time();

    return $DB->insert_record('availability_stripepayment_payments', $payment);
}

/**
 * Send internal email notifications after a successful payment.
 * The customer receipt is sent automatically by Stripe.
 *
 * @param stdClass $payment  Payment DB record
 * @param object   $session  Stripe Checkout Session object
 * @return bool
 */
function availability_stripepayment_send_payment_notifications($payment, $session) {
    global $DB, $CFG;

    $user   = $DB->get_record('user', ['id' => $payment->userid]);
    $cm     = get_coursemodule_from_id('', $payment->cmid);
    $course = $DB->get_record('course', ['id' => $payment->courseid]);

    if (!$user || !$cm || !$course) {
        error_log('[availability_stripepayment] send_payment_notifications: missing data for payment ' . $payment->id);
        return false;
    }

    $amount_display = number_format($payment->amount, 2) . ' ' . $payment->currency;

    // Notification to the accounts team.
    $accounts_email = get_config('availability_stripepayment', 'accounts_email')
        ?: 'accounts@' . parse_url($CFG->wwwroot, PHP_URL_HOST);

    $accounts_subject = 'New Payment - ' . $amount_display;
    $accounts_message = "New payment received via Stripe:\n\n"
        . "Student: {$user->firstname} {$user->lastname} ({$user->email})\n"
        . "Activity: {$cm->name}\n"
        . "Course: {$course->fullname}\n"
        . "Amount: {$amount_display}\n"
        . "Payment ID: {$session->id}\n"
        . "Date: " . userdate(time()) . "\n\n"
        . "Note: Customer receipt sent automatically by Stripe.";

    $accounts_html = '<p><strong>New payment received via Stripe:</strong></p>'
        . '<table>'
        . '<tr><td><strong>Student:</strong></td><td>' . fullname($user) . ' (' . $user->email . ')</td></tr>'
        . '<tr><td><strong>Activity:</strong></td><td>' . $cm->name . '</td></tr>'
        . '<tr><td><strong>Course:</strong></td><td>' . $course->fullname . '</td></tr>'
        . '<tr><td><strong>Amount:</strong></td><td>' . $amount_display . '</td></tr>'
        . '<tr><td><strong>Payment ID:</strong></td><td>' . $session->id . '</td></tr>'
        . '<tr><td><strong>Date:</strong></td><td>' . userdate(time()) . '</td></tr>'
        . '</table>'
        . '<p>Note: Customer receipt sent automatically by Stripe.</p>';

    availability_stripepayment_send_email($accounts_email, $accounts_subject, $accounts_message, $accounts_html);

    // Notification to site admins.
    foreach (get_admins() as $admin) {
        $admin_subject  = 'Student Payment - ' . $cm->name;
        $admin_message  = "A student has paid for activity access:\n\n"
            . "Student: {$user->firstname} {$user->lastname} ({$user->email})\n"
            . "Activity: {$cm->name}\n"
            . "Course: {$course->fullname}\n"
            . "Amount: {$amount_display}\n"
            . "Payment ID: {$session->id}\n"
            . "Date: " . userdate(time()) . "\n\n"
            . "View student: {$CFG->wwwroot}/user/profile.php?id={$user->id}\n"
            . "View activity: {$CFG->wwwroot}/mod/{$cm->modname}/view.php?id={$cm->id}";

        $admin_html = '<p><strong>A student has paid for activity access:</strong></p>'
            . '<table>'
            . '<tr><td><strong>Student:</strong></td><td>' . fullname($user) . ' (' . $user->email . ')</td></tr>'
            . '<tr><td><strong>Activity:</strong></td><td>' . $cm->name . '</td></tr>'
            . '<tr><td><strong>Course:</strong></td><td>' . $course->fullname . '</td></tr>'
            . '<tr><td><strong>Amount:</strong></td><td>' . $amount_display . '</td></tr>'
            . '<tr><td><strong>Payment ID:</strong></td><td>' . $session->id . '</td></tr>'
            . '<tr><td><strong>Date:</strong></td><td>' . userdate(time()) . '</td></tr>'
            . '</table>'
            . '<p><a href="' . $CFG->wwwroot . '/user/profile.php?id=' . $user->id . '">View student profile</a> | '
            . '<a href="' . $CFG->wwwroot . '/mod/' . $cm->modname . '/view.php?id=' . $cm->id . '">View activity</a></p>';

        availability_stripepayment_send_email($admin->email, $admin_subject, $admin_message, $admin_html);
    }

    return true;
}

/**
 * Send an email using Moodle's email system.
 *
 * @param string      $to           Recipient email address
 * @param string      $subject
 * @param string      $message      Plain-text body
 * @param string|null $html_message HTML body (optional)
 * @return bool
 */
function availability_stripepayment_send_email($to, $subject, $message, $html_message = null) {
    global $CFG;

    $from    = get_admin();
    $to_user = core_user::get_user_by_email($to);

    if (!$to_user) {
        // Build a clean synthetic user object that email_to_user() will accept.
        // Do NOT use get_noreply_user() as a base — it may carry emailstop=1, a
        // restrictive auth type, or other fields that silently block delivery.
        $to_user                    = new stdClass();
        $to_user->id                = -99;
        $to_user->email             = $to;
        $to_user->firstname         = 'Accounts';
        $to_user->lastname          = '';
        $to_user->firstnamephonetic = '';
        $to_user->lastnamephonetic  = '';
        $to_user->middlename        = '';
        $to_user->alternatename     = '';
        $to_user->username          = 'availability_stripepayment_notify';
        $to_user->auth              = 'manual';
        $to_user->confirmed         = 1;
        $to_user->deleted           = 0;
        $to_user->suspended         = 0;
        $to_user->emailstop         = 0;
        $to_user->maildisplay       = 0;
        $to_user->mailformat        = 1;
        $to_user->lang              = $CFG->lang ?? 'en';
        $to_user->timezone          = 99;
        $to_user->mnethostid        = $CFG->mnet_localhost_id ?? 1;
    } else {
        $to_user->mailformat = 1;
    }

    $result = email_to_user($to_user, $from, $subject, $message, $html_message);

    if (!$result) {
        error_log('[availability_stripepayment] email_to_user() returned false.'
            . ' To: ' . $to
            . ' | SMTP: ' . ($CFG->smtphost ?? '(not set, using PHP mail())')
            . ' | noemailever: ' . (!empty($CFG->noemailever) ? 'YES — all mail suppressed' : 'no')
            . ' | divertto: ' . ($CFG->divertallemailsto ?? 'none'));
    }

    return (bool) $result;
}
