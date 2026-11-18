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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/availability/condition/stripepayment/lib.php');
require_once($CFG->libdir . '/tablelib.php');

/**
 * Amounts, refund and grant permissions, and report totals.
 *
 * @package    availability_stripepayment
 * @copyright  2026 Andrei Toma
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     ::availability_stripepayment_to_minor_units
 * @covers     ::availability_stripepayment_can_refund
 * @covers     ::availability_stripepayment_can_grant
 * @covers     \availability_stripepayment\transactions_table::summary
 */
final class lib_test extends \advanced_testcase {
    /**
     * Prices as typed by a teacher and what Stripe must charge.
     *
     * @return array
     */
    public static function amounts_provider(): array {
        return [
            'whole pounds' => [35, 'gbp', 3500],
            'pence are kept' => [9.99, 'gbp', 999],
            'below one unit' => [0.5, 'usd', 50],
            'float noise' => [19.99, 'eur', 1999],
            'zero-decimal currency' => [1000, 'JPY', 1000],
        ];
    }

    /**
     * Stripe receives the exact price, not the price with the decimals cut off.
     *
     * @dataProvider amounts_provider
     * @param float $amount
     * @param string $currency
     * @param int $expected
     */
    public function test_to_minor_units(float $amount, string $currency, int $expected): void {
        $this->assertSame($expected, availability_stripepayment_to_minor_units($amount, $currency));
    }

    /**
     * A payment record keeps the price with its decimals.
     */
    public function test_payment_record_keeps_decimals(): void {
        global $DB;
        $this->resetAfterTest();
        $id = availability_stripepayment_create_payment(2, 3, 4, 'cs_test_x', 9.99, 'GBP');
        $this->assertEquals(9.99, (float) $DB->get_field('availability_stripepayment_payments', 'amount', ['id' => $id]));
    }

    /**
     * A course, a page activity and users with roles.
     *
     * @return array [course, context_module, teacher, student, manager, outsider]
     */
    private function setup_course(): array {
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $page = $gen->create_module('page', ['course' => $course->id]);
        $context = \context_module::instance($page->cmid);
        $teacher = $gen->create_and_enrol($course, 'editingteacher');
        $student = $gen->create_and_enrol($course, 'student');
        $manager = $gen->create_user();
        $gen->role_assign('manager', $manager->id, \context_system::instance()->id);
        $outsider = $gen->create_user();
        return [$course, $context, $teacher, $student, $manager, $outsider];
    }

    /**
     * Teachers cannot refund; site managers can.
     */
    public function test_only_site_managers_can_refund(): void {
        [, , $teacher, $student, $manager] = $this->setup_course();

        $this->setUser($teacher);
        $this->assertFalse(availability_stripepayment_can_refund());
        $this->setUser($student);
        $this->assertFalse(availability_stripepayment_can_refund());
        $this->setUser($manager);
        $this->assertTrue(availability_stripepayment_can_refund());
    }

    /**
     * Free access can be granted by a teacher, and only to users enrolled in the course.
     */
    public function test_grant_only_to_enrolled_users(): void {
        [, $context, $teacher, $student, , $outsider] = $this->setup_course();

        $this->setUser($teacher);
        $this->assertTrue(availability_stripepayment_can_grant($context, (int) $student->id));
        $this->assertFalse(availability_stripepayment_can_grant($context, (int) $outsider->id));

        $this->setUser($student);
        $this->assertFalse(availability_stripepayment_can_grant($context, (int) $student->id));
    }

    /**
     * The totals on a course report count that course only.
     */
    public function test_summary_is_limited_to_the_course(): void {
        global $DB;
        [$course] = $this->setup_course();
        $other = $this->getDataGenerator()->create_course();
        $now = time();
        $rows = [[$course->id, 'completed', 10], [$course->id, 'pending', 10], [$other->id, 'completed', 500]];
        foreach ($rows as [$cid, $st, $am]) {
            $DB->insert_record('availability_stripepayment_payments', (object) [
                'userid' => 2, 'cmid' => 1, 'courseid' => $cid, 'stripe_session_id' => 'cs_' . random_string(8),
                'amount' => $am, 'currency' => 'GBP', 'status' => $st, 'source' => 'stripe',
                'timecreated' => $now, 'timemodified' => $now,
            ]);
        }

        $summary = transactions_table::summary((int) $course->id);
        $this->assertSame([2, 1, 1], [$summary->total, $summary->completed, $summary->pending]);
        $this->assertEquals(10, (float) reset($summary->revenue)->total);

        $all = transactions_table::summary(0);
        $this->assertSame(3, $all->total);
    }
}
