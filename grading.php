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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>;.
/**
 * Prints the grading submissions page.
 *
 * @package    mod_collaborate
 * @copyright  2019 Richard Jones richardnz@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_collaborate
 * @see https://github.com/justinhunt/moodle-mod_collaborate */

use mod_collaborate\output\grading;
use mod_collaborate\local\submissions;
use mod_collaborate\local\grading_form;

use core\output\notification;

require_once('../../config.php');

// The submission id and the collaborate instance id.
$sid = required_param('sid', PARAM_TEXT);
$cid = required_param('cid', PARAM_INT);

// Get the information required to check the user can access this page.
$collaborate = $DB->get_record('collaborate', ['id' => $cid], '*', MUST_EXIST);
$courseid = $collaborate->course;
$cm = get_coursemodule_from_instance('collaborate', $cid, $courseid, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

// Set the page URL.
$PAGE->set_url('/mod/collaborate/grading.php', ['cid' => $cid, 'sid' => $sid]);

// Check the user is logged on.
require_login($course, true, $cm);

// Check permissions.
require_capability('mod/collaborate:gradesubmission', $context);

// Set the page information.
$PAGE->set_title(format_string($collaborate->name));
$PAGE->set_heading(format_string($course->fullname));

// Get the submission information.
$submission = submissions::get_submission_to_grade($collaborate, $sid);

// Instantiate the form and set the return url.
$form = new grading_form(null, ['cid' => $cid, 'sid' => $sid]);
$reportsurl = new moodle_url('/mod/collaborate/reports.php', ['cid' => $cid]);

// Check if cancelled.
if ($form->is_cancelled()) {
    redirect($reportsurl, get_string('cancelled'), 2, notification::NOTIFY_INFO);
}

// Do we have any data - save it and notify the user.
if ($data = $form->get_data()) {
    // Save the data here.
    submissions::update_grade($sid, $data->grade);
    redirect ($reportsurl, get_string('submissiongraded', 'mod_collaborate'), 2, notification::NOTIFY_SUCCESS);
}

if ($data) {
    $form->set_data($data);
}

// Start output to browser.
echo $OUTPUT->header();

// Create output object and render it using the template.
echo $OUTPUT->render(new grading($submission, $cm->id, $sid));

// Show the form on the page.
$form->display();

// End output to browser.
echo $OUTPUT->footer();