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
 * Prints submission reports.
 *
 * @package    mod_collaborate
 * @copyright  2019 Richard Jones richardnz@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_collaborate
 * @see https://github.com/justinhunt/moodle-mod_collaborate */

require_once('../../config.php');

// The collaborate instance id.
$cid = required_param('cid', PARAM_INT);
$collaborate = $DB->get_record('collaborate', ['id' => $cid], '*', MUST_EXIST);
$courseid = $collaborate->course;
$cm = get_coursemodule_from_instance('collaborate', $cid, $courseid, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

// Set the page URL.
$PAGE->set_url('/mod/collaborate/reports.php', ['cid' => $cid]);

// Check the user is logged on (do this after set url).
require_login($course, true, $cm);

// Set the page information.
$PAGE->set_title(format_string($collaborate->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_pagelayout('course');

// Prevent direct acess to the url.
require_capability('mod/collaborate:viewreportstab', $context);

$OUTPUT->header();
echo 'reports';
$OUTPUT->footer();