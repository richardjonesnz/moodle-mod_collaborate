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
 * Export submissions for a particular instance of Collaborate.
 *
 * @package    mod_collaborate
 * @copyright  2019 Richard Jones richardnz@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_collaborate
 * @see https://github.com/justinhunt/moodle-mod_collaborate */

use mod_collaborate\local\submissions;
use core\dataformat;
require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');

// The collaborate instance id.
$cid = required_param('cid', PARAM_INT);
$collaborate = $DB->get_record('collaborate', ['id' => $cid], '*', MUST_EXIST);
$courseid = $collaborate->course;
$cm = get_coursemodule_from_instance('collaborate', $cid, $courseid, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

// Check the user is logged on and can download submissions data.
require_login($course, true, $cm);
require_capability('mod/collaborate:exportsubmissions', $context);

// Get the data and set up an iterator for the pdf export.
$records = submissions::get_export_data($collaborate, $context);
$downloadsubmissions = new ArrayObject($records);
$iterator = $downloadsubmissions->getIterator();
$fields = submissions::get_export_headers();
$dataformat = 'pdf';
$filename = clean_filename('export_submissions' . time());
dataformat::download_data($filename, $dataformat, $fields, $iterator);

// We are actually only showing a dialog box.
echo $OUTPUT->download_dataformat_selector(get_string('download', 'admin'), 'reports.php');