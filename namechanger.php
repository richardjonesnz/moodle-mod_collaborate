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
 * Prints a list of collaborates and allows name to be changed.
 *
 * This was a one page wonder name changing page
 * Created by Justin Hunt for an earlier version of this course
 * Modified by Richard Jones
 *
 * @package    mod_collaborate
 * @copyright  2015 Flash Gordon http://www.flashgordon.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \core\output\notification;
use \mod_collaborate\local\namechanger_form;
use \mod_collaborate\output\namechanger;
require_once('../../config.php');

// Fetch URL parameters.
$courseid = required_param('courseid', PARAM_INT);   // course.
$action = optional_param('action', 'list', PARAM_TEXT);
$actionitem = optional_param('actionitem', 0, PARAM_INT);

// Set course related variables.
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
require_course_login($course);
$coursecontext = context_course::instance($course->id);

// Set up the page.
$PAGE->set_url('/mod/collaborate/namechanger.php', array('courseid' => $courseid));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($coursecontext);
$PAGE->set_pagelayout('course');

// Get the name_changer form.
$mform = new namechanger_form();

// Cancelled, redirect.
if ($mform->is_cancelled()) {
    redirect($PAGE->url, get_string('cancelled'), 2, notification::NOTIFY_WARNING);
    exit;
}

//if we have data, then our job here is to save it and return.
if ($data = $mform->get_data()) {

        // $DB->update_record('collaborate',$data);

        // Replace update with call to ad_hoc task
        $updatetask = new \mod_collaborate\task\collaborate_adhoc();
        $updatetask->set_custom_data($data);
        \core\task\manager::queue_adhoc_task($updatetask);
        redirect($PAGE->url,get_string('updated','core',$data->name),2);
}
// Start output to browser.
echo $OUTPUT->header();

// If the edit link was clicked, show the form.
if ($action == "edit") {
    // Create some data for our form.
    $data = new stdClass();
    $data->courseid = $courseid;
    $collaborate = $DB->get_record('collaborate', ['id' => $actionitem]);
    if (!$collaborate) {
        redirect($PAGE->url,'nodata', 2);
    }
    $data->id = $collaborate->id;
    $data->name = $collaborate->name;

    // Set data to form.
    $mform->set_data($data);
    $mform->display();
}

// Create output object and render it using the template.
echo $OUTPUT->render(new namechanger($course));

// End output to browser.
echo $OUTPUT->footer();