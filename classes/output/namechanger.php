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
 * This was part of a one page wonder name changing class
 * Created by Justin Hunt for an earlier version of this course
 * Modified by Richard Jones
 *
 * @package    mod_collaborate
 * @copyright  2015 Flash Gordon http://www.flashgordon.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;
use moodle_url;
use format_string;

/**
 * Create a new namechanger renderable object.
 */

class namechanger implements renderable, templatable {

    protected $course;

    public function __construct($course) {

        $this->course = $course;

    }
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $PAGE;

        $data = new stdClass();

        // Check if this course has collaborate modules present.
        if (!$collaborates = get_all_instances_in_course('collaborate', $this->course)) {
            $data->heading = get_string('no_collaborates', 'mod_collaborate');
            return;
        } else {
            $data->heading = get_string('modulenameplural', 'mod_collaborate');
        }

        // Table headers.
        $headers = array();

        if ($this->course->format == 'weeks') {
            $headers[] = get_string('week');

        } else if ($this->course->format == 'topics') {

            $headers[] = get_string('topic');
        } else {

            $headers[] = ' ';
        }

        $headers[] = get_string('name');
        $headers[] = get_string('action');
        $data->headers = $headers;

        $data->rows = array();

        // Table rows.
        foreach ($collaborates as $collaborate) {
            $row = array();
            $row['name'] = format_string($collaborate->name, true);
            // Dim link if hidden item.
            $row['class'] = (!$collaborate->visible) ? 'text-muted' : ' ';
            // Section number or week may be present.
            $row['section'] = $collaborate->section;
            // Link to edit form.
            $editlink = new moodle_url($PAGE->url, ['action' => 'edit', 'actionitem' => $collaborate->id]);
            $row['editurl'] = $editlink->out(false);

            $data->rows[] = $row;
        }

        return $data;
    }
}