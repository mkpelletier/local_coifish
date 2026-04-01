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
 * Navigation hooks for the CoIFish longitudinal profile plugin.
 *
 * @package    local_coifish
 * @copyright  2026 South African Theological Seminary (ict@sats.ac.za)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Extend the global navigation to add CoIFish reports for coordinators.
 *
 * Adds links in two places:
 * 1. Under Site Reports (for admin users who can see that section).
 * 2. As a top-level "CoIFish" node (for non-admin users with the right capabilities).
 *
 * @param global_navigation $navigation The global navigation object.
 */
function local_coifish_extend_navigation(global_navigation $navigation): void {
    $systemcontext = context_system::instance();

    $canviewprofiles = has_capability('local/coifish:viewfullprofile', $systemcontext)
        || has_capability('local/coifish:viewlecturerprofile', $systemcontext);

    if (!$canviewprofiles) {
        return;
    }

    // Try adding under Site Reports for admin users.
    $reportsnode = $navigation->find('sitereports', navigation_node::TYPE_CONTAINER);
    if ($reportsnode) {
        $reportsnode->add(
            get_string('risk_overview_title', 'local_coifish'),
            new moodle_url('/local/coifish/index.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            'coifish_riskoverview',
            new pix_icon('i/risk_xss', '')
        );
        $reportsnode->add(
            get_string('lecturer_profiles_title', 'local_coifish'),
            new moodle_url('/local/coifish/lecturerprofile.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            'coifish_lecturerprofiles',
            new pix_icon('i/grades', '')
        );
    }

    // Always add a top-level CoIFish node for users with the capability.
    // This ensures non-admin coordinators can find the reports.
    $coifishnode = $navigation->add(
        get_string('pluginname', 'local_coifish'),
        null,
        navigation_node::TYPE_CUSTOM,
        null,
        'local_coifish',
        new pix_icon('i/report', '')
    );
    $coifishnode->showinflatnavigation = true;

    if (has_capability('local/coifish:viewfullprofile', $systemcontext)) {
        $coifishnode->add(
            get_string('risk_overview_title', 'local_coifish'),
            new moodle_url('/local/coifish/index.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            'coifish_nav_riskoverview'
        );
    }

    $coifishnode->add(
        get_string('lecturer_profiles_title', 'local_coifish'),
        new moodle_url('/local/coifish/lecturerprofile.php'),
        navigation_node::TYPE_CUSTOM,
        null,
        'coifish_nav_lecturerprofiles'
    );
}
