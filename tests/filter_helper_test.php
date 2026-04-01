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
 * Tests for the filter helper.
 *
 * @package    local_coifish
 * @copyright  2026 South African Theological Seminary (ict@sats.ac.za)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_coifish\filter_helper
 */
final class filter_helper_test extends \advanced_testcase {
    /**
     * Test get_mode returns category by default.
     */
    public function test_default_mode_is_category(): void {
        $this->resetAfterTest();
        $this->assertEquals('category', filter_helper::get_mode());
    }

    /**
     * Test get_mode returns configured value.
     */
    public function test_configured_mode(): void {
        $this->resetAfterTest();
        set_config('organisation_mode', 'cohort', 'local_coifish');
        $this->assertEquals('cohort', filter_helper::get_mode());
    }

    /**
     * Test get_included_cohort_ids returns empty when not configured.
     */
    public function test_included_cohorts_empty(): void {
        $this->resetAfterTest();
        $result = filter_helper::get_included_cohort_ids();
        $this->assertEmpty($result);
    }

    /**
     * Test get_included_cohort_ids parses config correctly.
     */
    public function test_included_cohorts_configured(): void {
        $this->resetAfterTest();
        $config = json_encode([
            '1' => ['enabled' => true, 'pattern' => '^THE'],
            '3' => ['enabled' => true, 'pattern' => '^BIB'],
            '5' => ['enabled' => true, 'pattern' => ''],
        ]);
        set_config('cohort_programmes', $config, 'local_coifish');
        $result = filter_helper::get_included_cohort_ids();
        $this->assertEquals([1, 3, 5], $result);
    }

    /**
     * Test get_filter_options returns category options in category mode.
     */
    public function test_category_filter_options(): void {
        $this->resetAfterTest();
        set_config('organisation_mode', 'category', 'local_coifish');

        $filter = filter_helper::get_filter_options();
        $this->assertEquals('category', $filter['mode']);
        $this->assertEquals('categoryid', $filter['paramname']);
    }

    /**
     * Test get_filter_options returns cohort options in cohort mode.
     */
    public function test_cohort_filter_options(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        set_config('organisation_mode', 'cohort', 'local_coifish');

        // Create a cohort and include it via the new JSON format.
        $cohort = $this->getDataGenerator()->create_cohort(['contextid' => \context_system::instance()->id]);
        $config = json_encode([
            $cohort->id => ['enabled' => true, 'pattern' => '^TEST'],
        ]);
        set_config('cohort_programmes', $config, 'local_coifish');

        $filter = filter_helper::get_filter_options();
        $this->assertEquals('cohort', $filter['mode']);
        $this->assertEquals('cohortid', $filter['paramname']);
        // Admin should see the included cohort.
        $this->assertNotEmpty($filter['options']);
    }
}
