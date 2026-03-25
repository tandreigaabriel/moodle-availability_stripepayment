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
 *
 * @param \navigation_node $navigation The navigation node to extend.
 * @param \stdClass $course The course object.
 * @param \context $context The course context.
 * @return void
 */
function availability_stripepayment_extend_navigation_course($navigation, $course, $context) {
    if (
        !has_capability('availability/stripepayment:managetransactions', $context) &&
        !has_capability('moodle/course:manageactivities', $context)
    ) {
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
 * @param int $userid The user ID.
 * @param int $cmid The course module ID.
 * @return bool True if a completed payment exists, false otherwise.
 */
function availability_stripepayment_has_paid($userid, $cmid) {
    global $DB;

    return $DB->record_exists('availability_stripepayment_payments', [
        'userid' => $userid,
        'cmid' => $cmid,
        'status' => 'completed',
    ]);
}

/**
 * Find the Stripe condition object inside a Moodle availability tree.
 *
 * @param mixed $tree The availability tree or condition node.
 * @return \availability_stripepayment\condition|null The condition object, or null if not found.
 */
function availability_stripepayment_find_condition($tree) {
    if (!$tree) {
        return null;
    }

    if (is_a($tree, 'core_availability\\tree')) {
        return availability_stripepayment_find_in_availability_tree($tree);
    }

    if (is_object($tree) && isset($tree->type) && $tree->type === 'stripepayment') {
        return $tree;
    }

    if (is_object($tree) && isset($tree->c)) {
        return availability_stripepayment_find_in_children($tree->c);
    }

    return null;
}

/**
 * Search for the Stripe condition inside a core_availability\tree via reflection.
 *
 * @param \core_availability\tree $tree The availability tree.
 * @return \availability_stripepayment\condition|null The condition object, or null if not found.
 */
function availability_stripepayment_find_in_availability_tree($tree) {
    $reflection = new ReflectionClass($tree);
    $prop = $reflection->getProperty('children');
    $prop->setAccessible(true);
    $children = $prop->getValue($tree);

    if (!$children || !is_array($children)) {
        return null;
    }

    return availability_stripepayment_find_in_children($children);
}

/**
 * Iterate over an array of child nodes and return the first Stripe condition found.
 *
 * @param array $children Array of availability condition nodes.
 * @return \availability_stripepayment\condition|null The condition object, or null if not found.
 */
function availability_stripepayment_find_in_children(array $children) {
    foreach ($children as $child) {
        if (is_a($child, 'availability_stripepayment\\condition')) {
            return $child;
        }

        $result = availability_stripepayment_find_condition($child);
        if ($result) {
            return $result;
        }
    }

    return null;
}

/**
 * Insert a new pending payment record.
 *
 * @param int $userid The user ID.
 * @param int $cmid The course module ID.
 * @param int $courseid The course ID.
 * @param string $sessionid The Stripe checkout session ID.
 * @param int $amount The payment amount.
 * @param string $currency The ISO 4217 currency code (uppercase).
 * @return int The new record ID.
 */
function availability_stripepayment_create_payment($userid, $cmid, $courseid, $sessionid, $amount, $currency) {
    global $DB;

    $payment = new stdClass();
    $payment->userid = $userid;
    $payment->cmid = $cmid;
    $payment->courseid = $courseid;
    $payment->stripe_session_id = $sessionid;
    $payment->amount = $amount;
    $payment->currency = $currency;
    $payment->status = 'pending';
    $payment->timecreated = time();
    $payment->timemodified = time();

    return $DB->insert_record('availability_stripepayment_payments', $payment);
}

/**
 * Send internal email notifications after a successful payment.
 *
 * @param \stdClass $payment The payment record from the database.
 * @param \Stripe\Checkout\Session $session The Stripe checkout session object.
 * @return bool True on success, false if required data is missing.
 */
function availability_stripepayment_send_payment_notifications($payment, $session) {
    global $DB, $CFG;

    $user = $DB->get_record('user', ['id' => $payment->userid]);
    $cm = get_coursemodule_from_id('', $payment->cmid);
    $course = $DB->get_record('course', ['id' => $payment->courseid]);

    if (!$user || !$cm || !$course) {
        debugging('Missing data for payment ' . $payment->id, DEBUG_DEVELOPER);
        return false;
    }

    $amountdisplay = number_format($payment->amount, 2) . ' ' . $payment->currency;

    // Notification to the accounts team.
    $accountsemail = get_config('availability_stripepayment', 'accounts_email')
        ?: 'accounts@' . parse_url($CFG->wwwroot, PHP_URL_HOST);

    $accountssubject = 'New Payment - ' . $amountdisplay;

    $accountsmessage = "New payment received via Stripe:\n\n"
        . "Student: {$user->firstname} {$user->lastname} ({$user->email})\n"
        . "Activity: {$cm->name}\n"
        . "Course: {$course->fullname}\n"
        . "Amount: {$amountdisplay}\n"
        . "Payment ID: {$session->id}\n"
        . "Date: " . userdate(time()) . "\n\n"
        . "Note: Customer receipt sent automatically by Stripe.";

    $rows = [
        ['Student', fullname($user) . ' (' . $user->email . ')'],
        ['Activity', $cm->name],
        ['Course', $course->fullname],
        ['Amount', $amountdisplay],
        ['Payment ID', $session->id],
        ['Date', userdate(time())],
    ];

    $accountshtml = availability_stripepayment_email_html(
        'New Payment Received',
        '&#x2705; New payment received via Stripe.',
        $rows,
        '<p style="margin:0;color:#6c757d;font-size:13px;">Customer receipt has been sent automatically by Stripe.</p>'
    );

    availability_stripepayment_send_email($accountsemail, $accountssubject, $accountsmessage, $accountshtml);

    // Notification to site admins.
    foreach (get_admins() as $admin) {
        $adminsubject = 'Student Payment - ' . $cm->name;

        $adminmessage = "A student has paid for activity access:\n\n"
            . "Student: {$user->firstname} {$user->lastname} ({$user->email})\n"
            . "Activity: {$cm->name}\n"
            . "Course: {$course->fullname}\n"
            . "Amount: {$amountdisplay}\n"
            . "Payment ID: {$session->id}\n"
            . "Date: " . userdate(time()) . "\n\n"
            . "View student: {$CFG->wwwroot}/user/profile.php?id={$user->id}\n"
            . "View activity: {$CFG->wwwroot}/mod/{$cm->modname}/view.php?id={$cm->id}";

        $adminhtml = availability_stripepayment_email_html(
            'Student Payment Notification',
            '&#x1F4B3; A student has completed payment.',
            $rows
        );

        availability_stripepayment_send_email($admin->email, $adminsubject, $adminmessage, $adminhtml);
    }

    return true;
}

/**
 * Send an email using Moodle's email system.
 *
 * @param string $to Recipient email address.
 * @param string $subject Email subject.
 * @param string $message Plain-text message body.
 * @param string|null $htmlmessage Optional HTML message body.
 * @return bool True if the email was sent successfully.
 */
function availability_stripepayment_send_email($to, $subject, $message, $htmlmessage = null) {
    global $CFG;

    $from = get_admin();
    $touser = core_user::get_user_by_email($to);

    if (!$touser) {
        $touser = new stdClass();
        $touser->id = -99;
        $touser->email = $to;
        $touser->firstname = 'Accounts';
        $touser->lastname = '';
        $touser->username = 'availability_stripepayment_notify';
        $touser->auth = 'manual';
        $touser->confirmed = 1;
        $touser->deleted = 0;
        $touser->suspended = 0;
        $touser->emailstop = 0;
        $touser->mailformat = 1;
        $touser->lang = $CFG->lang ?? 'en';
        $touser->timezone = 99;
        $touser->mnethostid = $CFG->mnet_localhost_id ?? 1;
    }

    $result = email_to_user($touser, $from, $subject, $message, $htmlmessage);

    if (!$result) {
        debugging('email_to_user failed for ' . $to, DEBUG_DEVELOPER);
    }

    return (bool) $result;
}
