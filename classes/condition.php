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
 * Stripe payment availability condition.
 *
 * @package    availability_stripepayment
 * @copyright  2025 Andrei Toma
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_stripepayment;

/**
 * Availability condition for Stripe payments.
 */
class condition extends \core_availability\condition
{
    /** @var float Payment amount */
    public $amount;

    /** @var string Currency code */
    public $currency;

    /** @var string Item name */
    public $itemname;

    /**
     * Constructor.
     *
     * @param \stdClass $structure Data structure.
     */
    public function __construct($structure)
    {
        if (isset($structure->amount)) {
            $this->amount = $structure->amount;
        }
        if (isset($structure->currency)) {
            $this->currency = $structure->currency;
        }
        if (isset($structure->itemname)) {
            $this->itemname = $structure->itemname;
        }
    }

    /**
     * Save condition data.
     *
     * @return \stdClass
     */
    public function save()
    {
        $result = (object) ['type' => 'stripepayment'];
        if ($this->amount) {
            $result->amount = $this->amount;
        }
        if ($this->currency) {
            $result->currency = $this->currency;
        }
        if ($this->itemname) {
            $result->itemname = $this->itemname;
        }
        return $result;
    }

    /**
     * Get JSON structure.
     *
     * @param float  $amount
     * @param string $currency
     * @param string $itemname
     * @return \stdClass
     */
    public static function get_json($amount, $currency, $itemname)
    {
        return (object) [
            'type' => 'stripepayment',
            'amount' => $amount,
            'currency' => $currency,
            'itemname' => $itemname,
        ];
    }

    /**
     * Check if condition is available for a user.
     *
     * @param bool                    $not        True if negated.
     * @param \core_availability\info $info       Availability info.
     * @param bool                    $grabthelot True to grab all users.
     * @param int                     $userid     User ID to check.
     * @return bool
     */
    public function is_available($not, \core_availability\info $info, $grabthelot, $userid)
    {
        global $DB;

        $allow = false;
        if ($info instanceof \core_availability\info_module) {
            $allow = $DB->record_exists('availability_stripepayment_payments', [
                'userid' => $userid,
                'cmid' => $info->get_course_module()->id,
                'status' => 'completed',
            ]);
        }
        if ($not) {
            $allow = !$allow;
        }
        return $allow;
    }

    /**
     * Get description of condition for editing UI.
     *
     * @param bool                    $full True for full description.
     * @param bool                    $not  True if negated.
     * @param \core_availability\info $info Availability info.
     * @return string
     */
    public function get_description($full, $not, \core_availability\info $info)
    {
        return $this->get_either_description($not, !$full, $info);
    }

    /**
     * Get the condition description for both full and standalone contexts.
     *
     * @param bool                    $not        True if negated.
     * @param bool                    $standalone True when shown standalone (not in list).
     * @param \core_availability\info $info       Availability info.
     * @return string
     */
    protected function get_either_description($not, $standalone, $info)
    {
        global $USER, $DB, $OUTPUT, $PAGE;

        $cm = $info->get_course_module();
        $context = $info->get_context();

        if (
            has_capability('moodle/course:manageactivities', $context, $USER->id) ||
            has_capability('moodle/site:config', \context_system::instance(), $USER->id)
        ) {
            $reporturl = new \moodle_url('/availability/condition/stripepayment/activity_report.php', ['cmid' => $cm->id]);
            $reportlink = \html_writer::link(
                $reporturl,
                get_string('activitypaymentreport', 'availability_stripepayment'),
                ['class' => 'btn btn-sm btn-outline-info ms-2']
            );

            return get_string('already_paid', 'availability_stripepayment') . ' ' . $reportlink;
        }

        if ($not) {
            return get_string('not_paid', 'availability_stripepayment');
        }

        if (
            $DB->record_exists('availability_stripepayment_payments', [
                'userid' => $USER->id,
                'cmid' => $cm->id,
                'status' => 'completed',
            ])
        ) {
            return get_string('already_paid', 'availability_stripepayment');
        }

        $formatted = $this->format_amount_for_display();
        $item = $this->itemname ?: $cm->name;

        $description = get_string('payment_required_desc', 'availability_stripepayment', (object) [
            'item' => s($item),
            'amount' => s($formatted),
            'currency' => s(strtoupper($this->currency)),
        ]);

        $url = new \moodle_url('/availability/condition/stripepayment/payment.php', [
            'cmid' => $cm->id,
            'sesskey' => sesskey(),
        ]);

        if ($standalone) {
            return \html_writer::tag('span', $description, ['class' => 'd-block small mb-1']) .
                \html_writer::link(
                    $url,
                    get_string('pay_now', 'availability_stripepayment'),
                    ['class' => 'btn btn-sm btn-primary']
                );
        }

        $PAGE->requires->js_call_amd('availability_stripepayment/payment', 'init');

        return $OUTPUT->render_from_template('availability_stripepayment/payment_button', [
            'payurl' => $url->out(false),
            'description' => $description,
        ]);
    }

    /**
     * Format amount for display.
     *
     * @return string
     */
    private function format_amount_for_display()
    {
        $zd = ['JPY', 'KRW', 'VND', 'XAF', 'XOF', 'XPF']; // Zero decimal currencies.

        $symbol = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
        ][strtoupper($this->currency)] ?? strtoupper($this->currency) . ' ';

        if (in_array(strtoupper($this->currency), $zd)) {
            return $symbol . number_format($this->amount, 0);
        }

        return $symbol . number_format($this->amount, 2);
    }

    /**
     * Get a short debug string for unit testing.
     *
     * @return string
     */
    protected function get_debug_string()
    {
        return $this->currency . ' ' . $this->amount . ' (' . $this->itemname . ')';
    }
}
