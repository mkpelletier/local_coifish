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
 * Renderable for the lecturer list overview.
 *
 * @package    local_coifish
 * @copyright  2026 South African Theological Seminary (ict@sats.ac.za)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coifish\output;

use renderable;
use templatable;
use renderer_base;
use local_coifish\filter_helper;

/**
 * Prepares lecturer list data for the lecturer list template.
 */
class lecturer_list implements renderable, templatable {
    /** @var int Filter ID (category or cohort, depending on mode). */
    protected int $filterid;

    /**
     * Constructor.
     *
     * @param int $filterid Filter ID (category or cohort).
     */
    public function __construct(int $filterid = 0) {
        $this->filterid = $filterid;
    }

    /**
     * Export data for the mustache template.
     *
     * @param renderer_base $output The renderer.
     * @return \stdClass Template data.
     */
    public function export_for_template(renderer_base $output): \stdClass {
        $data = new \stdClass();

        $mode = filter_helper::get_mode();

        // Get filtered lecturers based on organisation mode.
        if ($mode === 'cohort') {
            $lecturerids = filter_helper::get_filtered_lecturer_ids($this->filterid);
            $lecturers = \local_coifish\lecturer_api::get_all_lecturer_profiles(0, $lecturerids);
        } else {
            $lecturers = \local_coifish\lecturer_api::get_all_lecturer_profiles($this->filterid);
        }

        $data->lecturers = $lecturers;
        $data->haslecturers = !empty($lecturers);

        // Filter dropdown (adapts to mode).
        $filter = filter_helper::get_filter_options($this->filterid);
        $data->filteroptions = $filter['options'];
        $data->filterlabel = $filter['label'];
        $data->filteralllabel = $filter['alllabel'];
        $data->filterparamname = $filter['paramname'];
        $data->selectedfilter = $this->filterid;

        // Form action URL.
        $data->formurl = (new \moodle_url('/local/coifish/lecturerprofile.php'))->out(false);

        // Export URL.
        $data->exporturl = (new \moodle_url('/local/coifish/export.php'))->out(false);

        return $data;
    }
}
