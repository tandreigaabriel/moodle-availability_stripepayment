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

namespace availability_stripepayment\privacy;

use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Tests the privacy provider.
 *
 * @package availability_stripepayment
 * @copyright 2026 Andrei Toma
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \availability_stripepayment\privacy\provider
 */
final class provider_test extends \core_privacy\tests\provider_testcase {
    /**
     * Export and delete one student's payments without touching another student's data.
     */
    public function test_export_and_delete(): void {
        global $DB;
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $page = $gen->create_module('page', ['course' => $course->id]);
        $context = \context_module::instance($page->cmid);
        $a = $gen->create_user();
        $b = $gen->create_user();
        foreach ([$a, $b] as $user) {
            $DB->insert_record('availability_stripepayment_payments', (object) [
                'userid' => $user->id, 'courseid' => $course->id, 'cmid' => $page->cmid,
                'stripe_session_id' => 'cs_synthetic_' . $user->id, 'amount' => 10, 'currency' => 'GBP',
                'status' => 'completed', 'timecreated' => time(), 'timemodified' => time(),
            ]);
        }
        $this->assertEquals([$context->id], provider::get_contexts_for_userid($a->id)->get_contextids());
        $users = new userlist($context, 'availability_stripepayment');
        provider::get_users_in_context($users);
        $this->assertEqualsCanonicalizing([$a->id, $b->id], $users->get_userids());
        $approved = new approved_contextlist($a, 'availability_stripepayment', [$context->id]);
        provider::export_user_data($approved);
        $data = writer::with_context($context)->get_data([get_string('pluginname', 'availability_stripepayment')]);
        $this->assertCount(1, $data->payments);
        $this->assertEquals('cs_synthetic_' . $a->id, $data->payments[0]->stripe_session_id);
        provider::delete_data_for_user($approved);
        $this->assertFalse($DB->record_exists('availability_stripepayment_payments', ['userid' => $a->id]));
        $this->assertTrue($DB->record_exists('availability_stripepayment_payments', ['userid' => $b->id]));
    }
}
