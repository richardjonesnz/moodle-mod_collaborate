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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>;;.
/**
 * Form for student submissions.
 *
 * @package   mod_collaborate
 * @copyright 2018 Richard Jones https://richardnz.net
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\local;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/filelib.php');

class submission_form extends \moodleform {
    public function definition() {
        global $DB;
        $mform = $this->_form;
        $context = $this->_customdata['context'];
        $options = collaborate_editor::get_editor_options($context);
        $mform->addElement('editor', 'submission_editor', get_string('submission', 'mod_collaborate'), null, $options);
        // Remember stick with this naming style.
        $mform->setType('submission_editor', PARAM_RAW);
        $mform->addElement('hidden', 'cid', $this->_customdata['cid']);
        $mform->setType('cid', PARAM_INT);
        $mform->addElement('hidden', 'page', $this->_customdata['page']);
        $mform->setType('page', PARAM_TEXT);
        // Add a save button.
        $this->add_action_buttons(false, get_string('submissionsave', 'mod_collaborate'));
    }
}