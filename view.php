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
 * Prints a particular instance of simplemod
 *
 * @package    mod_simplemod
 * @copyright  2019 Richard Jones richardnz@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_simplemod
 * @see https://github.com/justinhunt/moodle-mod_simplemod */

use mod_simplemod\output\view;
require_once('../../config.php');

// We need the course module id (id) or
// the simplemod instance id (n).
$id = optional_param('id', 0, PARAM_INT);
$n  = optional_param('n', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('simplemod', $id, 0, false,
            MUST_EXIST);
    $course = $DB->get_record('course',
            array('id' => $cm->course), '*', MUST_EXIST);
    $simplemod = $DB->get_record('simplemod',
            array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $simplemod = $DB->get_record('simplemod', array('id' => $n), '*',
            MUST_EXIST);
    $course = $DB->get_record('course',
            array('id' => $simplemod->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('simplemod', $simplemod->id,
            $course->id, false, MUST_EXIST);
}

// Print the page header.
$PAGE->set_url('/mod/simplemod/view.php', array('id' => $cm->id));

require_login($course, true, $cm);

$PAGE->set_title(format_string($simplemod->name));
$PAGE->set_heading(format_string($course->fullname));

// Check for intro page content.
if (!$simplemod->intro) {
    $simplemod->intro = '';
}
// Start output to browser.
echo $OUTPUT->header();

// Call classes/output/view and view.mustache to create output.
echo $OUTPUT->render(new view($simplemod, $cm->id));

// End output to browser.
echo $OUTPUT->footer();