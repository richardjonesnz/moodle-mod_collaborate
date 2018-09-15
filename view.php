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
 * Prints a particular instance of pairwork
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_pairwork
 * @copyright  2018 Richard Jones richardnz@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_newmodule
 * @see https://github.com/justinhunt/moodle-mod_pairwork */

require_once('../../config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... pairwork instance ID - it should be named as the first character of the module.

if ($id) {
    $cm         = get_coursemodule_from_id('pairwork', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $pairwork  = $DB->get_record('pairwork', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $pairwork  = $DB->get_record('pairwork', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $pairwork->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('pairwork', $pairwork->id, $course->id, false, MUST_EXIST);
} else {
    // Moodle Developer debugging called.
    debugging('Internal error: No course_module ID or instance ID',
            DEBUG_DEVELOPER);
}

require_login($course, true, $cm);

// Record the module viewed event for logging.
$event = \mod_pairwork\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $pairwork);
$event->trigger();

// Print the page header.
$PAGE->set_url('/mod/pairwork/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($pairwork->name));
$PAGE->set_heading(format_string($course->fullname));

// The renderer performs output to the page.
$renderer = $PAGE->get_renderer('mod_pairwork');

// Check for intro page content.
if (!$pairwork->intro) {
    $pairwork->intro = '';
}
// Start the page, call renderer to show content and
// finish the page.
echo $OUTPUT->header();
echo $renderer->fetch_view_page_content($pairwork, $cm);
echo $OUTPUT->footer();