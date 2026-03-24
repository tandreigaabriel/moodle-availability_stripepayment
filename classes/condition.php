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

/**
 * Stripe payment availability condition.
 *
 * @package availability_stripepayment
 */
class condition extends \core_availability\condition
{

    /** @var float|null */
    public $amount;

    /** @var string|null */
    public $currency;

    /** @var string|null */
    public $itemname;

    /**
     * Constructor.
     *
     * @param \stdClass $structure
     */
    public function __construct(\stdClass $structure)
    {
        $this->amount = $structure->amount ?? null;
        $this->currency = $structure->currency ?? null;
        $this->itemname = $structure->itemname ?? null;
    }

    /**
     * Save condition.
     *
     * @return \stdClass
     */
    public function save(): \stdClass
    {
        $result = (object) ['type' => 'stripepayment'];

        if ($this->amount !== null) {
            $result->amount = $this->amount;
        }
        if ($this->currency !== null) {
            $result->currency = $this->currency;
        }
        if ($this->itemname !== null) {
            $result->itemname = $this->itemname;
        }

        return $result;
    }

    /**
     * Get JSON representation.
     *
     * @param float $amount
     * @param string $currency
     * @param string $itemname
     * @return \stdClass
     */
    public static function get_json(float $amount, string $currency, string $itemname): \stdClass
    {
        return (object) [
            'type' => 'stripepayment',
            'amount' => $amount,
            'currency' => $currency,
            'itemname' => $itemname,
        ];
    }

    /**
     * Check availability.
     *
     * @param bool $not
     * @param \core_availability\info $info
     * @param bool $grabthelot
     * @param int $userid
     * @return bool
     */
    public function is_available(bool $not, \core_availability\info $info, bool $grabthelot, int $userid): bool
    {
        global $DB;

        unset($grabthelot);

        $allow = false;

        if ($info instanceof \core_availability\info_module) {
            $allow = $DB->record_exists('availability_stripepayment_payments', [
                'userid' => $userid,
                'cmid' => $info->get_course_module()->id,
                'status' => 'completed',
            ]);
        }

        return $not ? !$allow : $allow;
    }

    /**
     * Get description.
     *
     * @param bool $full
     * @param bool $not
     * @param \core_availability\info $info
     * @return string
     */
    public function get_description(bool $full, bool $not, \core_availability\info $info): string
    {
        return $this->get_either_description($not, !$full, $info);
    }
}
