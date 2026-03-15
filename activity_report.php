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
 * Per-activity payment report accessible to course teachers.
 *
 * @package    availability_stripepayment
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');

$cmid = required_param('cmid', PARAM_INT);

$cm      = get_coursemodule_from_id('', $cmid, 0, false, MUST_EXIST);
$course  = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$context = context_module::instance($cmid);

$PAGE->set_url(new moodle_url('/availability/condition/stripepayment/activity_report.php', ['cmid' => $cmid]));
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title($cm->name . ': ' . get_string('paymentreport', 'availability_stripepayment'));
$PAGE->set_heading($course->fullname);

require_login($course, false, $cm);
require_capability('moodle/course:manageactivities', $context);

// --- Statistics ---
$completed = $DB->count_records('availability_stripepayment_payments', ['cmid' => $cmid, 'status' => 'completed']);
$pending   = $DB->count_records('availability_stripepayment_payments', ['cmid' => $cmid, 'status' => 'pending']);

$revenue_rows = $DB->get_records_sql(
    "SELECT currency, SUM(amount) AS total
       FROM {availability_stripepayment_payments}
      WHERE cmid = :cmid AND status = 'completed'
      GROUP BY currency",
    ['cmid' => $cmid]
);

// --- All payments for this activity ---
$payments = $DB->get_records_sql(
    "SELECT p.id, p.userid, p.amount, p.currency, p.status, p.timecreated,
            u.firstname, u.lastname, u.email
       FROM {availability_stripepayment_payments} p
       JOIN {user} u ON p.userid = u.id
      WHERE p.cmid = :cmid
      ORDER BY p.timecreated DESC",
    ['cmid' => $cmid]
);

$zero_decimal = ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];

echo $OUTPUT->header();

// Back link.
echo html_writer::link(
    new moodle_url('/course/view.php', ['id' => $course->id]),
    '&laquo; ' . get_string('backtocourse', 'availability_stripepayment'),
    ['class' => 'd-inline-block mb-3']
);

echo html_writer::tag('h2', s($cm->name) . ': ' . get_string('paymentreport', 'availability_stripepayment'), ['class' => 'mb-4']);

// --- Stats cards (Bootstrap 5) ---
echo html_writer::start_div('row g-3 mb-4');

echo html_writer::start_div('col-sm-6 col-lg-4');
echo html_writer::start_div('card h-100 border-success');
echo html_writer::div(get_string('completedpayments', 'availability_stripepayment'), 'card-header bg-success text-white fw-semibold');
echo html_writer::start_div('card-body text-center');
echo html_writer::tag('h3', $completed, ['class' => 'card-title text-success mb-0']);
echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::start_div('col-sm-6 col-lg-4');
echo html_writer::start_div('card h-100 border-info');
echo html_writer::div(get_string('pending', 'availability_stripepayment'), 'card-header bg-info text-dark fw-semibold');
echo html_writer::start_div('card-body text-center');
echo html_writer::tag('h3', $pending, ['class' => 'card-title text-info mb-0']);
echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

foreach ($revenue_rows as $rev) {
    $decimals = in_array(strtoupper($rev->currency), $zero_decimal) ? 0 : 2;
    echo html_writer::start_div('col-sm-6 col-lg-4');
    echo html_writer::start_div('card h-100 border-warning');
    echo html_writer::div(
        get_string('totalrevenue', 'availability_stripepayment') . ' (' . strtoupper(s($rev->currency)) . ')',
        'card-header bg-warning text-dark fw-semibold'
    );
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('h3', number_format($rev->total, $decimals), ['class' => 'card-title text-warning mb-0']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();
}

echo html_writer::end_div(); // row

// --- Payments table ---
echo html_writer::tag('h3', get_string('payingstudents', 'availability_stripepayment'), ['class' => 'mt-2 mb-3']);

if ($payments) {
    $table = new html_table();
    $table->head = [
        get_string('student', 'availability_stripepayment'),
        get_string('email'),
        get_string('amount', 'availability_stripepayment'),
        get_string('currency', 'availability_stripepayment'),
        get_string('status'),
        get_string('time'),
    ];
    $table->attributes['class'] = 'generaltable table table-striped table-hover';

    foreach ($payments as $p) {
        $profileurl = new moodle_url('/user/view.php', ['id' => $p->userid, 'course' => $course->id]);
        $name       = html_writer::link($profileurl, fullname($p));
        $decimals   = in_array(strtoupper($p->currency), $zero_decimal) ? 0 : 2;

        // Bootstrap 5 badge classes.
        switch ($p->status) {
            case 'completed':
                $badge = html_writer::span(get_string('completed', 'availability_stripepayment'), 'badge bg-success');
                break;
            case 'pending':
                $badge = html_writer::span(get_string('pending', 'availability_stripepayment'), 'badge bg-info text-dark');
                break;
            case 'failed':
                $badge = html_writer::span(get_string('failed', 'availability_stripepayment'), 'badge bg-danger');
                break;
            case 'cancelled':
                $badge = html_writer::span(get_string('cancelled', 'availability_stripepayment'), 'badge bg-secondary');
                break;
            case 'expired':
                $badge = html_writer::span(get_string('expired', 'availability_stripepayment'), 'badge bg-secondary');
                break;
            default:
                $badge = html_writer::span(ucfirst(s($p->status)), 'badge bg-warning text-dark');
        }

        $table->data[] = [
            $name,
            s($p->email),
            number_format($p->amount, $decimals),
            strtoupper(s($p->currency)),
            $badge,
            userdate($p->timecreated),
        ];
    }

    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('nopayments', 'availability_stripepayment'), 'info');
}

echo $OUTPUT->footer();
