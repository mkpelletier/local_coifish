<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_coifish;

/**
 * Tests for the lecturer API.
 *
 * @package    local_coifish
 * @copyright  2026 South African Theological Seminary (ict@sats.ac.za)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_coifish\lecturer_api
 */
final class lecturer_api_test extends \advanced_testcase {
    /**
     * Test that get_lecturer_profile returns empty when no profile exists.
     */
    public function test_no_profile(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        $result = lecturer_api::get_lecturer_profile($user->id);
        $this->assertEmpty($result);
    }

    /**
     * Test that get_lecturer_profile returns formatted data.
     */
    public function test_profile_with_data(): void {
        global $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $DB->insert_record('local_coifish_lecturer', (object)[
            'userid' => $user->id,
            'coursecount' => 4,
            'avgfeedbackquality' => 72,
            'avgcoverage' => 80,
            'avgdepth' => 65,
            'avgpersonalisation' => 70,
            'avgturnarounddays' => 3.5,
            'totalinterventions' => 10,
            'interventionsimproved' => 6,
            'interventioneffectiveness' => 60,
            'avgforumpostspw' => 2.5,
            'avgstudentgrade' => 68.2,
            'studentgradetrend' => 'improving',
            'hours_marking' => 15.5,
            'hours_communication' => 3.2,
            'hours_livesessions' => 8.0,
            'hours_total' => 26.7,
            'strengths' => json_encode(['feedback_quality', 'feedback_coverage']),
            'focusareas' => json_encode(['forum_engagement']),
            'timemodified' => time(),
        ]);

        $result = lecturer_api::get_lecturer_profile($user->id);
        $this->assertTrue($result['hasprofile']);
        $this->assertEquals(4, $result['coursecount']);
        $this->assertEquals(72, $result['avgfeedbackquality']);
        $this->assertEquals(3.5, $result['avgturnarounddays']);
        $this->assertEquals(60, $result['interventioneffectiveness']);
        $this->assertEquals(15.5, $result['hours_marking']);
        $this->assertTrue($result['hashours']);
        $this->assertTrue($result['hasstrengths']);
        $this->assertTrue($result['hasfocusareas']);
    }

    /**
     * Test get_all_lecturer_profiles returns all profiles when unfiltered.
     */
    public function test_all_profiles_unfiltered(): void {
        global $DB;
        $this->resetAfterTest();

        $teacher1 = $this->getDataGenerator()->create_user();
        $teacher2 = $this->getDataGenerator()->create_user();

        foreach ([$teacher1->id, $teacher2->id] as $uid) {
            $DB->insert_record('local_coifish_lecturer', (object)[
                'userid' => $uid,
                'coursecount' => 2,
                'avgfeedbackquality' => 50,
                'avgturnarounddays' => 5.0,
                'totalinterventions' => 0,
                'interventionsimproved' => 0,
                'avgstudentgrade' => 60.0,
                'studentgradetrend' => 'stable',
                'hours_marking' => 0,
                'hours_communication' => 0,
                'hours_livesessions' => 0,
                'hours_total' => 0,
                'strengths' => '[]',
                'focusareas' => '[]',
                'timemodified' => time(),
            ]);
        }

        $result = lecturer_api::get_all_lecturer_profiles();
        $this->assertCount(2, $result);
    }

    /**
     * Test get_all_lecturer_profiles with explicit user ID filter.
     */
    public function test_all_profiles_filtered_by_userids(): void {
        global $DB;
        $this->resetAfterTest();

        $teacher1 = $this->getDataGenerator()->create_user();
        $teacher2 = $this->getDataGenerator()->create_user();
        $teacher3 = $this->getDataGenerator()->create_user();

        foreach ([$teacher1->id, $teacher2->id, $teacher3->id] as $uid) {
            $DB->insert_record('local_coifish_lecturer', (object)[
                'userid' => $uid,
                'coursecount' => 1,
                'totalinterventions' => 0,
                'interventionsimproved' => 0,
                'studentgradetrend' => 'unknown',
                'hours_marking' => 0,
                'hours_communication' => 0,
                'hours_livesessions' => 0,
                'hours_total' => 0,
                'strengths' => '[]',
                'focusareas' => '[]',
                'timemodified' => time(),
            ]);
        }

        // Filter to only two teachers.
        $result = lecturer_api::get_all_lecturer_profiles(0, [$teacher1->id, $teacher3->id]);
        $this->assertCount(2, $result);
        $ids = array_map('intval', array_column($result, 'userid'));
        $this->assertContains((int)$teacher1->id, $ids);
        $this->assertContains((int)$teacher3->id, $ids);
        $this->assertNotContains((int)$teacher2->id, $ids);
    }

    /**
     * Test self-view flag is set correctly.
     */
    public function test_self_view_flag(): void {
        global $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $DB->insert_record('local_coifish_lecturer', (object)[
            'userid' => $user->id,
            'coursecount' => 1,
            'totalinterventions' => 0,
            'interventionsimproved' => 0,
            'studentgradetrend' => 'unknown',
            'hours_marking' => 0,
            'hours_communication' => 0,
            'hours_livesessions' => 0,
            'hours_total' => 0,
            'strengths' => '[]',
            'focusareas' => '[]',
            'timemodified' => time(),
        ]);

        $selfresult = lecturer_api::get_lecturer_profile($user->id, true);
        $otherresult = lecturer_api::get_lecturer_profile($user->id, false);
        $this->assertTrue($selfresult['isself']);
        $this->assertFalse($otherresult['isself']);
    }
}
