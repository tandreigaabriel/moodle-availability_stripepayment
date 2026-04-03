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
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
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
function availability_stripepayment_extend_navigation_course($navigation, $course, $context)
{
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
function availability_stripepayment_has_paid($userid, $cmid)
{
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
function availability_stripepayment_find_condition($tree)
{
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
function availability_stripepayment_find_in_availability_tree($tree)
{
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
function availability_stripepayment_find_in_children(array $children)
{
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
function availability_stripepayment_create_payment($userid, $cmid, $courseid, $sessionid, $amount, $currency)
{
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
 * Build an HTML email body.
 *
 * @param string      $title   Email title shown in the header bar.
 * @param string      $heading Introductory heading text (HTML allowed).
 * @param array       $rows    Array of [label, value] pairs for the detail table.
 * @param string|null $footer  Optional footer HTML appended below the table.
 * @return string HTML email body.
 */
function availability_stripepayment_email_html($title, $heading, array $rows, $footer = null)
{
    $tablerows = '';
    foreach ($rows as [$label, $value]) {
        $tablerows .= '<tr>'
            . '<td style="padding:6px 12px;font-weight:bold;white-space:nowrap;color:#495057;">'
            . htmlspecialchars($label) . '</td>'
            . '<td style="padding:6px 12px;color:#212529;">'
            . htmlspecialchars($value) . '</td>'
            . '</tr>';
    }

    $footerhtml = $footer ? '<div style="margin-top:16px;">' . $footer . '</div>' : '';

    return '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;background:#f8f9fa;margin:0;padding:20px;">'
        . '<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:6px;overflow:hidden;'
        . 'box-shadow:0 1px 4px rgba(0,0,0,.1);">'
        . '<div style="background:#4f46e5;padding:20px 24px;">'
        . '<h2 style="margin:0;color:#fff;font-size:18px;">' . htmlspecialchars($title) . '</h2>'
        . '</div>'
        . '<div style="padding:24px;">'
        . '<p style="margin:0 0 16px;">' . $heading . '</p>'
        . '<table style="width:100%;border-collapse:collapse;background:#f8f9fa;border-radius:4px;">'
        . $tablerows
        . '</table>'
        . $footerhtml
        . '</div></div></body></html>';
}

/**
 * Send internal email notifications after a successful payment.
 *
 * @param \stdClass $payment The payment record from the database.
 * @param \Stripe\Checkout\Session $session The Stripe checkout session object.
 * @return bool True on success, false if required data is missing.
 */
function availability_stripepayment_send_payment_notifications($payment, $session)
{
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

    $accountssubject = get_string('email_accounts_subject', 'availability_stripepayment', $amountdisplay);

    $accountsmessage = get_string('email_accounts_intro', 'availability_stripepayment') . "\n\n"
        . get_string('student', 'availability_stripepayment') . ': ' . fullname($user) . ' (' . $user->email . ")\n"
        . get_string('email_accounts_row_activity', 'availability_stripepayment') . ': ' . $cm->name . "\n"
        . get_string('email_accounts_row_course', 'availability_stripepayment') . ': ' . $course->fullname . "\n"
        . get_string('amount', 'availability_stripepayment') . ': ' . $amountdisplay . "\n"
        . get_string('payment_detail_id', 'availability_stripepayment') . ': ' . $session->id . "\n"
        . get_string('email_accounts_row_date', 'availability_stripepayment') . ': ' . userdate(time()) . "\n\n"
        . get_string('email_accounts_note', 'availability_stripepayment');

    $rows = [
        [get_string('student', 'availability_stripepayment'), fullname($user) . ' (' . $user->email . ')'],
        [get_string('email_accounts_row_activity', 'availability_stripepayment'), $cm->name],
        [get_string('email_accounts_row_course', 'availability_stripepayment'), $course->fullname],
        [get_string('amount', 'availability_stripepayment'), $amountdisplay],
        [get_string('payment_detail_id', 'availability_stripepayment'), $session->id],
        [get_string('email_accounts_row_date', 'availability_stripepayment'), userdate(time())],
    ];

    $accountshtml = availability_stripepayment_email_html(
        get_string('email_accounts_html_title', 'availability_stripepayment'),
        get_string('email_accounts_html_heading', 'availability_stripepayment'),
        $rows,
        '<p style="margin:0;color:#6c757d;font-size:13px;">'
        . get_string('email_accounts_html_footer', 'availability_stripepayment') . '</p>'
    );

    $accountsuser = $DB->get_record('user', ['email' => $accountsemail, 'deleted' => 0]) ?: get_admin();
    email_to_user($accountsuser, core_user::get_noreply_user(), $accountssubject, $accountsmessage, $accountshtml);

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
function availability_stripepayment_send_email($to, $subject, $message, $htmlmessage = null)
{
    global $CFG;

    $from = get_admin();
    $touser = core_user::get_user_by_email($to);

    if (!$touser) {
        debugging('availability_stripepayment_send_email: no Moodle user found for ' . $to, DEBUG_DEVELOPER);
        return false;
    }

    $result = email_to_user($touser, $from, $subject, $message, $htmlmessage);

    if (!$result) {
        debugging('email_to_user failed for ' . $to, DEBUG_DEVELOPER);
    }

    return (bool) $result;
}
