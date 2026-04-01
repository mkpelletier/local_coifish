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

/**
 * External function to get a student's course-by-course snapshot history.
 *
 * @package    local_coifish
 * @copyright  2026 South African Theological Seminary (ict@sats.ac.za)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coifish\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_coifish\api;

/**
 * Get a student's course-by-course snapshot history.
 */
class get_course_history extends external_api {
    /**
     * Define parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'userid' => new external_value(PARAM_INT, 'Student user ID'),
        ]);
    }

    /**
     * Execute the function.
     *
     * @param int $userid
     * @return array
     */
    public static function execute(int $userid): array {
        $params = self::validate_parameters(self::execute_parameters(), [
            'userid' => $userid,
        ]);

        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('local/coifish:viewfullprofile', $context);

        $apienabled = get_config('local_coifish', 'api_enabled');
        if ($apienabled === '0') {
            return [];
        }

        return api::get_course_history($params['userid']);
    }

    /**
     * Define return structure.
     *
     * @return external_multiple_structure
     */
    public static function execute_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure([
                'courseid' => new external_value(PARAM_INT, 'Course ID'),
                'coursename' => new external_value(PARAM_TEXT, 'Course name'),
                'finalgrade' => new external_value(PARAM_FLOAT, 'Final grade %', VALUE_OPTIONAL),
                'engagement' => new external_value(PARAM_INT, 'Engagement rate'),
                'social' => new external_value(PARAM_INT, 'Social presence rate'),
                'selfregulation' => new external_value(PARAM_INT, 'Self-regulation composite'),
                'feedbackpct' => new external_value(PARAM_INT, 'Feedback review %'),
                'interventioncount' => new external_value(PARAM_INT, 'Intervention count'),
                'interventionsimproved' => new external_value(PARAM_INT, 'Interventions with improvement'),
                'courseenddate' => new external_value(PARAM_INT, 'Course end timestamp'),
            ])
        );
    }
}
