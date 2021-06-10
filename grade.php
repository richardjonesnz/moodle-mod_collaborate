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
 * Redirect the user to the appropriate submission related page.
 *
 * @package    mod_collaborate
 * @copyright  2019 Richard Jones richardnz@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_collaborate
 * @see https://github.com/justinhunt/moodle-mod_collaborate
 */
require_once(__DIR__ . "../../../config.php");
$id = required_param('id', PARAM_INT);// Course module ID.

$cm = get_coursemodule_from_id('collaborate', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course),
        '*', MUST_EXIST);
$collaborate = $DB->get_record('collaborate',
            array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, false, $cm);

$modulecontext = context_module::instance($cm->id);
// Re-direct the user.
if (has_capability('mod/collaborate:gradesubmission', $modulecontext)) {
    $url = new moodle_url('reports.php', ['cid' => $collaborate->id]);
} else {
    $url = new moodle_url('view.php', array('id' => $id));
}
redirect($url);