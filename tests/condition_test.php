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
 * Tests payment-based availability.
 *
 * @package availability_stripepayment
 * @copyright 2026 Andrei Toma
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \availability_stripepayment\condition
 */
final class condition_test extends \advanced_testcase {
    /**
     * Test payment status, user and activity isolation, including negation.
     */
    public function test_availability(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $page = $generator->create_module('page', ['course' => $course->id]);
        $otherpage = $generator->create_module('page', ['course' => $course->id]);
        $user = $generator->create_user();
        $otheruser = $generator->create_user();
        $info = new \core_availability\info_module(get_fast_modinfo($course)->get_cm($page->cmid));
        $condition = new condition(condition::get_json(10, 'GBP', 'Test'));
        $this->assertFalse($condition->is_available(false, $info, false, $user->id));
        $this->assertTrue($condition->is_available(true, $info, false, $user->id));
        $payment = (object) [
            'userid' => $user->id, 'courseid' => $course->id, 'cmid' => $otherpage->cmid,
            'stripe_session_id' => 'cs_synthetic', 'amount' => 10, 'currency' => 'GBP',
            'status' => 'completed', 'timecreated' => time(), 'timemodified' => time(),
        ];
        $payment->id = $DB->insert_record('availability_stripepayment_payments', $payment);
        $this->assertFalse($condition->is_available(false, $info, false, $user->id));
        $payment->cmid = $page->cmid;
        $payment->status = 'pending';
        $DB->update_record('availability_stripepayment_payments', $payment);
        $this->assertFalse($condition->is_available(false, $info, false, $user->id));
        $payment->status = 'completed';
        $DB->update_record('availability_stripepayment_payments', $payment);
        $this->assertTrue($condition->is_available(false, $info, false, $user->id));
        $this->assertFalse($condition->is_available(true, $info, false, $user->id));
        $this->assertFalse($condition->is_available(false, $info, false, $otheruser->id));
        $payment->status = 'refunded';
        $DB->update_record('availability_stripepayment_payments', $payment);
        $this->assertFalse($condition->is_available(false, $info, false, $user->id));
    }
}
