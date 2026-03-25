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

namespace availability_stripepayment;

/**
 * Table shown at the Stripe assignment payments report.
 *
 * @package     availability_stripepayment
 * @copyright   2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class transactions_table extends \table_sql {
    /**
     * Table setup.
     */
    public function __construct() {
        global $PAGE;

        parent::__construct('availability_stripepayment-transactions');

        $columns = ['id', 'item', 'fullname'];
        $headers = [
            get_string('id', 'availability_stripepayment'),
            get_string('activity'),
            get_string('fullname'),
        ];

        foreach (\core_user\fields::get_identity_fields($PAGE->context, false) as $field) {
            $columns[] = $field;
            $headers[] = \core_user\fields::get_display_name($field);
        }

        $columns = array_merge($columns, ['amount', 'currency', 'status', 'stripe_session_id', 'timecreated', 'stripe_link']);
        $headers = array_merge($headers, [
            get_string('amount', 'availability_stripepayment'),
            get_string('currency', 'availability_stripepayment'),
            get_string('status'),
            get_string('transactionid', 'availability_stripepayment'),
            get_string('time'),
            'Stripe',
        ]);

        $this->define_columns($columns);
        $this->define_headers($headers);

        $userfields = \core_user\fields::for_name()->with_identity($PAGE->context)->get_sql('u');

        $fields = 'p.id, p.userid, p.courseid, p.cmid, p.stripe_session_id, ' .
            'p.amount, p.currency, p.status, p.timecreated, p.timemodified, ' .
            'c.shortname as course_shortname, m.name as modname_raw, cm.instance as cm_instance' .
            $userfields->selects;

        $from = '{availability_stripepayment_payments} p
                 JOIN {user} u ON p.userid = u.id
                 JOIN {course} c ON p.courseid = c.id
                 LEFT JOIN {course_modules} cm ON cm.id = p.cmid
                 LEFT JOIN {modules} m ON m.id = cm.module';

        $where = '1=1';

        $this->set_sql($fields, $from, $where);

        $this->define_baseurl($PAGE->url);

        $this->sortable(true, 'id', SORT_DESC);
    }

    /**
     * Resolve the activity instance name directly from the module table.
     * Uses modname_raw (e.g. "assign") + cm_instance to do a single targeted lookup.
     *
     * @param object $data Row data from the report query.
     * @return string Activity name, or localised "unknown activity" string.
     */
    private function get_activity_name($data): string {
        global $DB;

        if (!empty($data->modname_raw) && !empty($data->cm_instance)) {
            try {
                $name = $DB->get_field($data->modname_raw, 'name', ['id' => $data->cm_instance]);
                if ($name !== false && $name !== '') {
                    return $name;
                }
            } catch (\Exception $ex) {
                // Table may not exist for an uninstalled module — fall through.
                debugging('availability_stripepayment: module table lookup failed: ' . $ex->getMessage(), DEBUG_DEVELOPER);
            }
        }

        return get_string('unknownactivity', 'availability_stripepayment');
    }

    /**
     * Format the activity name with course context.
     *
     * @param object $data Row data.
     * @return string Formatted cell content.
     */
    public function col_item($data) {
        global $PAGE;

        $itemname = $this->get_activity_name($data);
        $coursepart = $data->course_shortname . ' / ';

        if ($this->is_downloading()) {
            return $coursepart . $itemname;
        }

        $itemclass = '';
        if ($PAGE->url->get_param('courseid') && $PAGE->url->get_param('courseid') != $data->courseid) {
            $itemclass = 'dimmed_text';
        }

        $modname = $data->modname_raw ?? '';
        if ($modname && $data->cmid) {
            $url = new \moodle_url('/mod/' . $modname . '/view.php', ['id' => $data->cmid]);
            $link = \html_writer::link($url, $itemname);
        } else {
            $link = $itemname;
        }

        return \html_writer::span($coursepart . $link, $itemclass);
    }

    /**
     * Format the user name with profile link.
     *
     * @param object $data Row data.
     * @return string Formatted cell content.
     */
    public function col_fullname($data) {
        global $PAGE;

        $name = fullname($data);

        if ($this->is_downloading()) {
            return $name;
        }

        if ($courseid = $PAGE->url->get_param('courseid')) {
            $profileurl = new \moodle_url('/user/view.php', ['id' => $data->userid, 'course' => $courseid]);
        } else {
            $profileurl = new \moodle_url('/user/profile.php', ['id' => $data->userid]);
        }

        return \html_writer::link($profileurl, $name);
    }

    /**
     * Format the payment amount.
     *
     * @param object $data Row data.
     * @return string Formatted amount.
     */
    public function col_amount($data) {
        $zerodecimal = ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];
        $decimals = in_array(strtoupper($data->currency), $zerodecimal) ? 0 : 2;
        return number_format($data->amount, $decimals);
    }

    /**
     * Format the currency.
     *
     * @param object $data Row data.
     * @return string Uppercase currency code.
     */
    public function col_currency($data) {
        return strtoupper($data->currency);
    }

    /**
     * Format the payment status with badges (HTML) or plain text (download).
     *
     * @param object $data Row data.
     * @return string Formatted status badge or plain text.
     */
    public function col_status($data) {
        if ($this->is_downloading()) {
            return ucfirst($data->status);
        }

        // Bootstrap 5 badge classes: badge bg-{colour}.
        switch ($data->status) {
            case 'completed':
                return \html_writer::span(get_string('completed', 'availability_stripepayment'), 'badge bg-success');
            case 'pending':
                return \html_writer::span(get_string('pending', 'availability_stripepayment'), 'badge bg-info text-dark');
            case 'failed':
                return \html_writer::span(get_string('failed', 'availability_stripepayment'), 'badge bg-danger');
            case 'cancelled':
                return \html_writer::span(get_string('cancelled', 'availability_stripepayment'), 'badge bg-secondary');
            case 'expired':
                return \html_writer::span(get_string('expired', 'availability_stripepayment'), 'badge bg-secondary');
            default:
                return \html_writer::span(ucfirst($data->status), 'badge bg-warning text-dark');
        }
    }

    /**
     * Format the Stripe session ID.
     * Downloads get the full ID; screen gets a shortened, copyable version.
     *
     * @param object $data Row data.
     * @return string Formatted session ID or dash if empty.
     */
    public function col_stripe_session_id($data) {
        if (empty($data->stripe_session_id)) {
            return '-';
        }

        if ($this->is_downloading()) {
            return $data->stripe_session_id;
        }

        $shortid = substr($data->stripe_session_id, 0, 8) . '…' . substr($data->stripe_session_id, -8);

        $copybutton = \html_writer::tag('button', '📋', [
            'class' => 'btn btn-sm btn-outline-secondary ms-1',
            'onclick' => "navigator.clipboard.writeText('" . s($data->stripe_session_id) . "'); this.innerText='✅'; setTimeout(() => this.innerText='📋', 1000);",
            'title' => get_string('copytransactionid', 'availability_stripepayment'),
            'type' => 'button',
        ]);

        return \html_writer::span($shortid, 'font-monospace', ['title' => $data->stripe_session_id]) . $copybutton;
    }

    /**
     * Format the time.
     *
     * @param object $data Row data.
     * @return string Formatted date/time string.
     */
    public function col_timecreated($data) {
        return userdate($data->timecreated, get_string('strftimedatetime', 'langconfig'));
    }

    /**
     * Stripe dashboard link (screen only; plain URL for downloads).
     *
     * @param object $data Row data.
     * @return string Link HTML or plain URL string.
     */
    public function col_stripe_link($data) {
        if (empty($data->stripe_session_id)) {
            return '-';
        }

        $istest = strpos($data->stripe_session_id, 'cs_test_') === 0;

        if (strpos($data->stripe_session_id, 'cs_') === 0) {
            $stripeurl = $istest
                ? 'https://dashboard.stripe.com/test/checkout/sessions/' . $data->stripe_session_id
                : 'https://dashboard.stripe.com/checkout/sessions/' . $data->stripe_session_id;
        } else {
            $base = $istest
                ? 'https://dashboard.stripe.com/test/payments/'
                : 'https://dashboard.stripe.com/payments/';
            $stripeurl = $base . $data->stripe_session_id;
        }

        if ($this->is_downloading()) {
            return $stripeurl;
        }

        return \html_writer::link($stripeurl, get_string('stripelink', 'availability_stripepayment'), [
            'target' => '_blank',
            'class' => 'btn btn-sm btn-outline-primary',
            'title' => get_string('viewinstripe', 'availability_stripepayment'),
        ]);
    }

    /**
     * Override to add summary cards above the table.
     *
     * @return void
     */
    public function start_output() {
        global $DB;

        $totalpayments = $DB->count_records('availability_stripepayment_payments');
        $completedpayments = $DB->count_records('availability_stripepayment_payments', ['status' => 'completed']);
        $pendingpayments = $DB->count_records('availability_stripepayment_payments', ['status' => 'pending']);

        $revenuerows = $DB->get_records_sql(
            "SELECT currency, SUM(amount) AS total
               FROM {availability_stripepayment_payments}
              WHERE status = 'completed'
              GROUP BY currency
              ORDER BY currency"
        );

        $zerodecimal = ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];

        // Row 1: three metric cards (always equal thirds).
        echo \html_writer::start_div('row g-3 mb-3');

        // Total payments card.
        echo \html_writer::start_div('col-sm-6 col-lg-4');
        echo \html_writer::start_div('card h-100 border-primary');
        echo \html_writer::div(get_string('totalpayments', 'availability_stripepayment'), 'card-header bg-primary text-white fw-semibold');
        echo \html_writer::start_div('card-body text-center');
        echo \html_writer::tag('h3', $totalpayments, ['class' => 'card-title text-primary mb-0']);
        echo \html_writer::end_div();
        echo \html_writer::end_div();
        echo \html_writer::end_div();

        // Completed card.
        echo \html_writer::start_div('col-sm-6 col-lg-4');
        echo \html_writer::start_div('card h-100 border-success');
        echo \html_writer::div(get_string('completed', 'availability_stripepayment'), 'card-header bg-success text-white fw-semibold');
        echo \html_writer::start_div('card-body text-center');
        echo \html_writer::tag('h3', $completedpayments, ['class' => 'card-title text-success mb-0']);
        echo \html_writer::end_div();
        echo \html_writer::end_div();
        echo \html_writer::end_div();

        // Pending card.
        echo \html_writer::start_div('col-sm-6 col-lg-4');
        echo \html_writer::start_div('card h-100 border-info');
        echo \html_writer::div(get_string('pending', 'availability_stripepayment'), 'card-header bg-info text-dark fw-semibold');
        echo \html_writer::start_div('card-body text-center');
        echo \html_writer::tag('h3', $pendingpayments, ['class' => 'card-title text-info mb-0']);
        echo \html_writer::end_div();
        echo \html_writer::end_div();
        echo \html_writer::end_div();

        echo \html_writer::end_div(); // row 1

        // Row 2: single "Revenue by currency" card.
        echo \html_writer::start_div('row g-3 mb-4');
        echo \html_writer::start_div('col-12');
        echo \html_writer::start_div('card border-warning');
        echo \html_writer::div(get_string('totalrevenue', 'availability_stripepayment'), 'card-header bg-warning text-dark fw-semibold');
        echo \html_writer::start_div('card-body py-2');

        if ($revenuerows) {
            echo \html_writer::start_tag('ul', ['class' => 'list-unstyled d-flex flex-wrap gap-4 mb-0']);
            foreach ($revenuerows as $rev) {
                $currency = strtoupper($rev->currency);
                $decimals = in_array($currency, $zerodecimal) ? 0 : 2;
                $amount = number_format($rev->total, $decimals);
                echo \html_writer::tag(
                    'li',
                    \html_writer::tag('span', $currency, ['class' => 'badge bg-warning text-dark me-1']) .
                    \html_writer::tag('strong', $amount . ' ' . $currency, ['class' => 'text-warning fs-5'])
                );
            }
            echo \html_writer::end_tag('ul');
        } else {
            echo \html_writer::tag('p', '—', ['class' => 'text-muted mb-0']);
        }

        echo \html_writer::end_div(); // card-body
        echo \html_writer::end_div(); // card
        echo \html_writer::end_div(); // col-12
        echo \html_writer::end_div(); // row 2

        parent::start_output();
    }
}
