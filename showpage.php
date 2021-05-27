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
 * Shows Student instructions page(s).
 *
 * @package    mod_collaborate
 * @copyright  2019 Richard Jones richardnz@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_collaborate
 * @see https://github.com/justinhunt/moodle-mod_collaborate */

use \core\output\notification;
use \mod_collaborate\local\submissions;
use \mod_collaborate\local\submission_form;
use \mod_collaborate\local\collaborate_editor;
use \mod_collaborate\output\showpage;

require_once('../../config.php');

// The user page id and the collaborate instance id.
$page = required_param('page', PARAM_TEXT);
$cid = required_param('cid', PARAM_INT);

// Get the information required to check the user can access this page.
$collaborate = $DB->get_record('collaborate', ['id' => $cid], '*', MUST_EXIST);
$courseid = $collaborate->course;
$cm = get_coursemodule_from_instance('collaborate', $cid, $courseid, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

// Set the page URL.
$PAGE->set_url('/mod/collaborate/showpage.php', ['cid' => $cid, 'page' => $page]);

// Check the user is logged on.
require_login($course, true, $cm);

// Set the page information.
$PAGE->set_title(format_string($collaborate->name));
$PAGE->set_heading(format_string($course->fullname));

// Instantiate the form and set the return url.
$form = new submission_form(null, ['context' => $context, 'cid' => $cid, 'page' => $page]);
$returnurl = new moodle_url('/mod/collaborate/showpage.php', ['cid' => $cid, 'page' => $page]);

// Do we have any data - save it and notify the user.
if ($data = $form->get_data()) {
    // Save the data here.
    submissions::save_submission($data, $context, $cid, $page);
    redirect ($returnurl, get_string('submissionupdated', 'mod_collaborate'), null, notification::NOTIFY_SUCCESS);
}

// Set the saved data (if any) to the form.
$data = new stdClass();
$data = submissions::get_submission($cid, $USER->id, $page);
if ($data) {
    $options = collaborate_editor::get_editor_options($context);
    $data = file_prepare_standard_editor($data, 'submission', $options, $context, 'mod_collaborate', 'submission',
            $data->id);
    $form->set_data($data);
}

// Start output to browser.
echo $OUTPUT->header();

// Create output object and render it using the template.
echo $OUTPUT->render(new showpage($collaborate, $cm, $page));

// Show the form on the page.
$form->display();

// End output to browser.
echo $OUTPUT->footer();