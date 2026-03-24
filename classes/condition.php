<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Stripe payment availability condition.
 *
 * @package    availability_stripepayment
 * @copyright  2025 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_stripepayment;

defined('MOODLE_INTERNAL') || die();

class condition extends \core_availability\condition
{

    public $amount;
    public $currency;
    public $itemname;

    /**
     * Constructor
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
     * Saves tree data back to a structure object
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
     * Returns a JSON object which corresponds to a condition of this type
     */
    public static function get_json($amount, $currency, $itemname)
    {
        return (object) [
            'type' => 'stripepayment',
            'amount' => $amount,
            'currency' => $currency,
            'itemname' => $itemname
        ];
    }

    /**
     * Determines whether a particular item is currently available
     */
    public function is_available($not, \core_availability\info $info, $grabthelot, $userid)
    {
        global $DB;

        // Required by Moodle API but not used.
        unset($grabthelot);

        $allow = false;

        if (is_a($info, '\core_availability\info_module')) {
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
     * Obtains a string describing this restriction
     */
    public function get_description($full, $not, \core_availability\info $info)
    {
        return $this->get_either_description($not, !$full, $info);
    }

    protected function get_either_description($not, $standalone, $info)
    {
        global $USER, $DB, $OUTPUT, $PAGE;

        $cm = $info->get_course_module();
        $context = $info->get_context();

        // Admins/teachers see report link instead of payment
        if (
            has_capability('moodle/course:manageactivities', $context, $USER->id) ||
            has_capability('moodle/site:config', \context_system::instance(), $USER->id)
        ) {

            $reporturl = new \moodle_url(
                '/availability/condition/stripepayment/activity_report.php',
                ['cmid' => $cm->id]
            );

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

        $haspaid = $DB->record_exists('availability_stripepayment_payments', [
            'userid' => $USER->id,
            'cmid' => $cm->id,
            'status' => 'completed',
        ]);

        if ($haspaid) {
            return get_string('already_paid', 'availability_stripepayment');
        }

        $formattedamount = $this->format_amount_for_display();
        $itemname = $this->itemname ?: $cm->name;

        $description = get_string(
            'payment_required_desc',
            'availability_stripepayment',
            (object) [
                'item' => s($itemname),
                'amount' => s($formattedamount),
                'currency' => s(strtoupper($this->currency)),
            ]
        );

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
     * Format amount for display
     */
    private function format_amount_for_display()
    {
        // Shortened variable name (fixes PHPMD warning)
        $zerodecimals = [
            'BIF',
            'CLP',
            'DJF',
            'GNF',
            'JPY',
            'KMF',
            'KRW',
            'MGA',
            'PYG',
            'RWF',
            'UGX',
            'VND',
            'VUV',
            'XAF',
            'XOF',
            'XPF'
        ];

        $displayamount = $this->amount;

        $currencysymbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'AED' => 'AED ',
        ];

        $symbol = $currencysymbols[strtoupper($this->currency)] ?? strtoupper($this->currency) . ' ';

        if (in_array(strtoupper($this->currency), $zerodecimals)) {
            return $symbol . number_format($displayamount, 0);
        }

        return $symbol . number_format($displayamount, 2);
    }

    /**
     * Debug string
     */
    protected function get_debug_string()
    {
        return $this->currency . ' ' . $this->amount . ' (' . $this->itemname . ')';
    }
}
