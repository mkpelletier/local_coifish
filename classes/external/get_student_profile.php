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
 * External function to get a student's longitudinal profile.
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
 * Get a student's longitudinal profile.
 */
class get_student_profile extends external_api {
    /**
     * Define parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'userid' => new external_value(PARAM_INT, 'Student user ID'),
            'courseid' => new external_value(PARAM_INT, 'Course context for capability check', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Execute the function.
     *
     * @param int $userid
     * @param int $courseid
     * @return array
     */
    public static function execute(int $userid, int $courseid = 0): array {
        $params = self::validate_parameters(self::execute_parameters(), [
            'userid' => $userid,
            'courseid' => $courseid,
        ]);

        if ($params['courseid'] > 0) {
            $context = \context_course::instance($params['courseid']);
            self::validate_context($context);
            require_capability('local/coifish:viewprofile', $context);
        } else {
            $context = \context_system::instance();
            self::validate_context($context);
            require_capability('local/coifish:viewfullprofile', $context);
        }

        $profile = api::get_student_profile($params['userid'], $params['courseid']);
        if (empty($profile)) {
            return ['hasprofile' => false];
        }

        return $profile;
    }

    /**
     * Define return structure.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'hasprofile' => new external_value(PARAM_BOOL, 'Whether a profile exists'),
            'coursescompleted' => new external_value(PARAM_INT, 'Number of completed courses', VALUE_OPTIONAL),
            'risklevel' => new external_value(PARAM_ALPHA, 'Risk level: low, moderate, high', VALUE_OPTIONAL),
            'risklabel' => new external_value(PARAM_TEXT, 'Localised risk label', VALUE_OPTIONAL),
            'isriskhigh' => new external_value(PARAM_BOOL, '', VALUE_OPTIONAL),
            'isriskmoderate' => new external_value(PARAM_BOOL, '', VALUE_OPTIONAL),
            'isrisklow' => new external_value(PARAM_BOOL, '', VALUE_OPTIONAL),
            'riskfactors' => new external_multiple_structure(
                new external_value(PARAM_TEXT, 'Risk factor label'),
                'Risk factor labels',
                VALUE_OPTIONAL
            ),
            'hasriskfactors' => new external_value(PARAM_BOOL, '', VALUE_OPTIONAL),
            'engagementpattern' => new external_value(PARAM_ALPHANUMEXT, 'Engagement pattern key', VALUE_OPTIONAL),
            'engagementpatternlabel' => new external_value(PARAM_TEXT, 'Localised engagement pattern', VALUE_OPTIONAL),
            'gradetrend' => new external_value(PARAM_ALPHA, 'Grade trend', VALUE_OPTIONAL),
            'gradetrendlabel' => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
            'socialtrend' => new external_value(PARAM_ALPHA, '', VALUE_OPTIONAL),
            'socialtrendlabel' => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
            'selfregtrend' => new external_value(PARAM_ALPHA, '', VALUE_OPTIONAL),
            'selfregtrendlabel' => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
            'interventionresponse' => new external_value(PARAM_ALPHANUMEXT, '', VALUE_OPTIONAL),
            'interventionresponselabel' => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
            'totalinterventions' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
            'avggrade' => new external_value(PARAM_FLOAT, 'Average grade %', VALUE_OPTIONAL),
            'avgsocial' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
            'avgselfregulation' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
            'avgfeedbackpct' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
            'interventionsimproved' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
            'coursesinprogress' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
        ]);
    }
}
