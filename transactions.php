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
 * Stripe payment transactions report page.
 *
 * @package    availability_stripepayment
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/tablelib.php');

$courseid = optional_param('courseid', 0, PARAM_INT);
$status   = optional_param('status', '', PARAM_ALPHA);
$perpage  = optional_param('perpage', 25, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);

// If accessed with a course context, set up as a course report.
if ($courseid) {
    $course  = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    $context = context_course::instance($courseid);
    $PAGE->set_course($course);
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('incourse');
} else {
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
}

$PAGE->set_url(new moodle_url('/availability/condition/stripepayment/transactions.php', array_filter([
    'courseid' => $courseid ?: null,
    'status'   => $status ?: null,
    'perpage'  => $perpage,
])));

$PAGE->set_title(get_string('transactionsreport', 'availability_stripepayment'));
$PAGE->set_heading(isset($course) ? $course->fullname : get_string('transactionsreport', 'availability_stripepayment'));

require_login($courseid ?: null);

// Site admins use system context; course teachers use course context.
if (!has_capability('availability/stripepayment:managetransactions', context_system::instance()) &&
    !has_capability('moodle/course:manageactivities', $context)) {
    require_capability('availability/stripepayment:managetransactions', $context);
}

// Build WHERE clause from active filters.
$where  = '1=1';
$params = [];

if ($courseid) {
    $where              .= ' AND p.courseid = :courseid';
    $params['courseid']  = $courseid;
}

$valid_statuses = ['completed', 'pending', 'failed', 'cancelled', 'expired'];
if ($status && in_array($status, $valid_statuses)) {
    $where           .= ' AND p.status = :status';
    $params['status'] = $status;
}

// Handle CSV/Excel export.
if ($download) {
    $table = new \availability_stripepayment\transactions_table();
    $table->set_sql($table->sql->fields, $table->sql->from, $where, $params);
    $table->is_downloading($download, 'stripe_payments_' . date('Y-m-d'), get_string('transactionsreport', 'availability_stripepayment'));
    $table->out($perpage, false);
    exit;
}

echo $OUTPUT->header();

// Page heading row with export buttons.
echo html_writer::start_div('d-flex justify-content-between align-items-center mb-4');
echo html_writer::tag('h2', get_string('transactionsreport', 'availability_stripepayment'), ['class' => 'mb-0 h4']);

echo html_writer::start_div('d-flex gap-2');
$export_url = new moodle_url($PAGE->url, ['download' => 'csv']);
echo html_writer::link($export_url, '⬇ ' . get_string('downloadcsv', 'table'), ['class' => 'btn btn-sm btn-outline-success']);

echo html_writer::link('https://dashboard.stripe.com/payments', '⧉ Stripe Dashboard', [
    'class'  => 'btn btn-sm btn-outline-primary',
    'target' => '_blank',
]);
echo html_writer::end_div();
echo html_writer::end_div();

// --- Filter form (Bootstrap 5) ---
$courses_with_payments = $DB->get_records_sql(
    "SELECT DISTINCT c.id, c.fullname
       FROM {course} c
       JOIN {availability_stripepayment_payments} p ON p.courseid = c.id
      ORDER BY c.fullname"
);

$base_url = new moodle_url('/availability/condition/stripepayment/transactions.php');

echo html_writer::start_tag('form', [
    'method' => 'get',
    'action' => $base_url->out(false),
    'class'  => 'd-flex flex-wrap align-items-end gap-3 mb-4 p-3 bg-light border rounded',
]);

// Course filter.
echo html_writer::start_div('');
echo html_writer::tag('label', get_string('course'), [
    'for'   => 'filter_courseid',
    'class' => 'form-label mb-1 small fw-semibold',
]);
$course_options = [0 => get_string('allcourses', 'core')];
foreach ($courses_with_payments as $c) {
    $course_options[$c->id] = format_string($c->fullname);
}
echo html_writer::select($course_options, 'courseid', $courseid, false, [
    'id'    => 'filter_courseid',
    'class' => 'form-select form-select-sm',
    'style' => 'min-width:180px',
]);
echo html_writer::end_div();

// Status filter.
echo html_writer::start_div('');
echo html_writer::tag('label', get_string('status'), [
    'for'   => 'filter_status',
    'class' => 'form-label mb-1 small fw-semibold',
]);
$status_options = [
    ''          => get_string('all'),
    'completed' => get_string('completed', 'availability_stripepayment'),
    'pending'   => get_string('pending',   'availability_stripepayment'),
    'failed'    => get_string('failed',    'availability_stripepayment'),
    'cancelled' => get_string('cancelled', 'availability_stripepayment'),
    'expired'   => get_string('expired',   'availability_stripepayment'),
];
echo html_writer::select($status_options, 'status', $status, false, [
    'id'    => 'filter_status',
    'class' => 'form-select form-select-sm',
]);
echo html_writer::end_div();

// Buttons.
echo html_writer::start_div('d-flex gap-2 align-items-end');
echo html_writer::tag('button', get_string('filter'), ['type' => 'submit', 'class' => 'btn btn-sm btn-primary']);
echo html_writer::link($base_url, get_string('clearfilter', 'availability_stripepayment'), ['class' => 'btn btn-sm btn-outline-secondary']);
echo html_writer::end_div();

echo html_writer::end_tag('form');

// Show active filter notice.
if ($courseid || $status) {
    $active = [];
    if ($courseid && isset($course)) {
        $active[] = get_string('course') . ': ' . format_string($course->fullname);
    }
    if ($status) {
        $active[] = get_string('status') . ': ' . ucfirst($status);
    }
    echo html_writer::div(
        get_string('filteractive', 'availability_stripepayment') . ' ' . implode(' &bull; ', $active) . ' &mdash; ' .
        html_writer::link($base_url, get_string('clearfilter', 'availability_stripepayment'), ['class' => 'alert-link']),
        'alert alert-info py-2'
    );
}

$table = new \availability_stripepayment\transactions_table();
$table->set_sql($table->sql->fields, $table->sql->from, $where, $params);

$table->out($perpage, true);

if ($table->totalrows) {
    $options = [];
    foreach ([25, 50, 100, 500, TABLE_SHOW_ALL_PAGE_SIZE] as $showperpage) {
        $options[$showperpage] = get_string('showperpage', 'core', $showperpage);
    }

    echo html_writer::start_div('my-3 d-flex justify-content-between align-items-center');
    echo $OUTPUT->single_select($PAGE->url, 'perpage', $options, $perpage, null, 'perpageform');
    echo html_writer::div(
        $table->totalrows . ' ' . get_string('payments', 'availability_stripepayment'),
        'text-muted small'
    );
    echo html_writer::end_div();
}

echo $OUTPUT->footer();
