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

require_once('../../config.php');
require_once('../../lib/formslib.php');

// The form class.

class grading_form extends \moodleform {

    public function definition() {
        global $CFG;
        $mform = $this->_form;

        // grades available.
        $grades = array();
        for ($m = 0; $m <= $this->_customdata['maxgrade']; $m++) {
            $grades[$m] = '' . $m;
        }
        $mform->addElement('select', 'grade',
                get_string('allocate_grade', 'mod_collaborate'),
                $grades);

        $mform->addElement('hidden', 'cid',
                $this->_customdata['cid']);
        $mform->addElement('hidden', 'sid',
                $this->_customdata['sid']);

        $mform->setType('cid', PARAM_INT);
        $mform->setType('sid', PARAM_INT);

        $this->add_action_buttons();
    }
}