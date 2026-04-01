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
 * Student drill-down profile page.
 *
 * Shows full longitudinal detail for a single student, including
 * profile summary, recommendations, and course history.
 *
 * @package    local_coifish
 * @copyright  2026 South African Theological Seminary (ict@sats.ac.za)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$userid = required_param('userid', PARAM_INT);

require_login();
$context = context_system::instance();
require_capability('local/coifish:viewfullprofile', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/coifish/studentprofile.php', ['userid' => $userid]));

$user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
$PAGE->set_title(get_string('student_profile_title', 'local_coifish', fullname($user)));
$PAGE->set_heading(get_string('student_profile_title', 'local_coifish', fullname($user)));
$PAGE->set_pagelayout('report');

$PAGE->navbar->add(get_string('risk_overview_title', 'local_coifish'), new moodle_url('/local/coifish/index.php'));
$PAGE->navbar->add(fullname($user));

$renderable = new \local_coifish\output\student_drilldown($userid);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_coifish/student_drilldown', $renderable->export_for_template($OUTPUT));
echo $OUTPUT->footer();
