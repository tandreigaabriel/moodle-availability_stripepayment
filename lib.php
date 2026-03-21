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
 * @param int $userid
 * @param int $cmid
 * @return bool
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
 * @param mixed $tree
 * @return \availability_stripepayment\condition|null
 */
function availability_stripepayment_find_condition($tree)
{
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
function availability_stripepayment_create_payment($userid, $cmid, $courseid, $session_id, $amount, $currency)
{
    global $DB;

    $payment = new stdClass();
    $payment->userid = $userid;
    $payment->cmid = $cmid;
    $payment->courseid = $courseid;
    $payment->stripe_session_id = $session_id;
    $payment->amount = $amount;
    $payment->currency = $currency;
    $payment->status = 'pending';
    $payment->timecreated = time();
    $payment->timemodified = time();

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
function availability_stripepayment_send_payment_notifications($payment, $session)
{
    global $DB, $CFG;

    $user = $DB->get_record('user', ['id' => $payment->userid]);
    $cm = get_coursemodule_from_id('', $payment->cmid);
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

    $rows = [
        ['Student',    fullname($user) . ' (' . $user->email . ')'],
        ['Activity',   $cm->name],
        ['Course',     $course->fullname],
        ['Amount',     $amount_display],
        ['Payment ID', $session->id],
        ['Date',       userdate(time())],
    ];

    $accounts_html = availability_stripepayment_email_html(
        'New Payment Received',
        '&#x2705; New payment received via Stripe.',
        $rows,
        '<p style="margin:0;color:#6c757d;font-size:13px;">Customer receipt has been sent automatically by Stripe.</p>'
    );

    availability_stripepayment_send_email($accounts_email, $accounts_subject, $accounts_message, $accounts_html);

    // Notification to site admins.
    foreach (get_admins() as $admin) {
        $admin_subject = 'Student Payment - ' . $cm->name;
        $admin_message = "A student has paid for activity access:\n\n"
            . "Student: {$user->firstname} {$user->lastname} ({$user->email})\n"
            . "Activity: {$cm->name}\n"
            . "Course: {$course->fullname}\n"
            . "Amount: {$amount_display}\n"
            . "Payment ID: {$session->id}\n"
            . "Date: " . userdate(time()) . "\n\n"
            . "View student: {$CFG->wwwroot}/user/profile.php?id={$user->id}\n"
            . "View activity: {$CFG->wwwroot}/mod/{$cm->modname}/view.php?id={$cm->id}";

        $links = '<a href="' . $CFG->wwwroot . '/user/profile.php?id=' . $user->id . '" '
            . 'style="display:inline-block;padding:8px 16px;background:#0d6efd;color:#fff;text-decoration:none;border-radius:4px;font-size:13px;margin-right:8px;">'
            . 'View Student</a>'
            . '<a href="' . $CFG->wwwroot . '/mod/' . $cm->modname . '/view.php?id=' . $cm->id . '" '
            . 'style="display:inline-block;padding:8px 16px;background:#6c757d;color:#fff;text-decoration:none;border-radius:4px;font-size:13px;">'
            . 'View Activity</a>';

        $admin_html = availability_stripepayment_email_html(
            'Student Payment Notification',
            '&#x1F4B3; A student has completed payment for activity access.',
            $rows,
            $links
        );

        availability_stripepayment_send_email($admin->email, $admin_subject, $admin_message, $admin_html);
    }

    return true;
}

/**
 * Build a branded HTML email using an inline-CSS Bootstrap-style layout.
 *
 * @param string $title    Heading shown in the email header band
 * @param string $intro    Short intro sentence shown below the header
 * @param array  $rows     Array of [label, value] pairs rendered as a details table
 * @param string $footer   Optional HTML block rendered below the table (buttons, notes, etc.)
 * @return string          Full HTML document ready for email_to_user()
 */
function availability_stripepayment_email_html($title, $intro, array $rows, $footer = '')
{
    global $CFG;

    $site_name = format_string(get_site()->fullname);
    $site_url  = $CFG->wwwroot;

    // Table rows.
    $rows_html = '';
    foreach ($rows as [$label, $value]) {
        $rows_html .= '
            <tr>
                <td style="padding:10px 16px;width:140px;color:#6c757d;font-size:13px;font-weight:600;white-space:nowrap;border-bottom:1px solid #f0f0f0;vertical-align:top;">'
                    . htmlspecialchars($label, ENT_QUOTES) .
                '</td>
                <td style="padding:10px 16px;font-size:14px;color:#212529;border-bottom:1px solid #f0f0f0;word-break:break-word;">'
                    . $value .
                '</td>
            </tr>';
    }

    return '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>' . htmlspecialchars($title, ENT_QUOTES) . '</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,\'Helvetica Neue\',Arial,sans-serif;">

  <!-- Outer wrapper -->
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:32px 16px;">
    <tr>
      <td align="center">

        <!-- Card -->
        <table role="presentation" width="600" cellpadding="0" cellspacing="0"
               style="max-width:600px;width:100%;background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);">

          <!-- Header band -->
          <tr>
            <td style="background:#0d6efd;padding:28px 32px;">
              <p style="margin:0;font-size:20px;font-weight:700;color:#ffffff;letter-spacing:.3px;">'
                . htmlspecialchars($site_name, ENT_QUOTES) .
              '</p>
              <p style="margin:6px 0 0;font-size:13px;color:rgba(255,255,255,.75);">Stripe Payment System</p>
            </td>
          </tr>

          <!-- Intro -->
          <tr>
            <td style="padding:24px 32px 8px;">
              <h2 style="margin:0 0 8px;font-size:18px;font-weight:600;color:#212529;">'
                . htmlspecialchars($title, ENT_QUOTES) .
              '</h2>
              <p style="margin:0;font-size:14px;color:#495057;">' . $intro . '</p>
            </td>
          </tr>

          <!-- Details table -->
          <tr>
            <td style="padding:16px 32px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                     style="border:1px solid #e9ecef;border-radius:6px;overflow:hidden;">
                ' . $rows_html . '
              </table>
            </td>
          </tr>

          <!-- Footer content (buttons / notes) -->
          ' . ($footer ? '<tr><td style="padding:8px 32px 24px;">' . $footer . '</td></tr>' : '') . '

          <!-- Bottom bar -->
          <tr>
            <td style="background:#f8f9fa;padding:16px 32px;border-top:1px solid #e9ecef;">
              <p style="margin:0;font-size:12px;color:#adb5bd;text-align:center;">
                This is an automated notification from
                <a href="' . $site_url . '" style="color:#0d6efd;text-decoration:none;">'
                  . htmlspecialchars($site_name, ENT_QUOTES) .
                '</a>. Please do not reply to this email.
              </p>
            </td>
          </tr>

        </table>
        <!-- /Card -->

      </td>
    </tr>
  </table>

</body>
</html>';
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
function availability_stripepayment_send_email($to, $subject, $message, $html_message = null)
{
    global $CFG;

    $from = get_admin();
    $to_user = core_user::get_user_by_email($to);

    if (!$to_user) {
        // Build a clean synthetic user object that email_to_user() will accept.
        // Do NOT use get_noreply_user() as a base — it may carry emailstop=1, a
        // restrictive auth type, or other fields that silently block delivery.
        $to_user = new stdClass();
        $to_user->id = -99;
        $to_user->email = $to;
        $to_user->firstname = 'Accounts';
        $to_user->lastname = '';
        $to_user->firstnamephonetic = '';
        $to_user->lastnamephonetic = '';
        $to_user->middlename = '';
        $to_user->alternatename = '';
        $to_user->username = 'availability_stripepayment_notify';
        $to_user->auth = 'manual';
        $to_user->confirmed = 1;
        $to_user->deleted = 0;
        $to_user->suspended = 0;
        $to_user->emailstop = 0;
        $to_user->maildisplay = 0;
        $to_user->mailformat = 1;
        $to_user->lang = $CFG->lang ?? 'en';
        $to_user->timezone = 99;
        $to_user->mnethostid = $CFG->mnet_localhost_id ?? 1;
    } else {
        $to_user->mailformat = 1;
        $to_user->emailstop = 0;
        $to_user->suspended = 0;
        $to_user->deleted = 0;
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
