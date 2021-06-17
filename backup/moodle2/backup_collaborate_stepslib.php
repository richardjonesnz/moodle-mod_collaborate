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
 * Define all the backup steps that will be used by the backup_collaborate_activity_task
 *
 * @package   mod_collaborate
 * @category  backup
 * @copyright 2019 Richard Jones richardnz@outlook.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define the complete collaborate structure for backup, with file and id annotations
 *
 * @package   mod_collaborate
 * @category  backup
 * @copyright 2019 Richard Jones richardnz@outlook.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_collaborate
 * @see https://github.com/justinhunt/moodle-mod_collaborate
 */
class backup_collaborate_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the backup structure of the module
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        // Get know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define the root element describing the collaborate instance.
        $collaborate = new backup_nested_element('collaborate',
                array('id'), array('course', 'name', 'intro',
                'introformat', 'title', 'timecreated',
                'timemodified', 'grade'));

        // Define the child element.
        $submissions = new backup_nested_element('submissions');
        $submission = new backup_nested_element('submission', array('id'),
                array('collaborateid', 'page', 'userid', 'submission', 'submissionformat',
                'timecreated', 'timemodified', 'grade'));

        // Build the tree.
        $collaborate->add_child($submissions);
        $submissions->add_child($submission);

        // Define data sources.
        $collaborate->set_source_table('collaborate', array('id' => backup::VAR_ACTIVITYID));

        // Backup submissions table if backing up user data.
        if ($userinfo) {

            $submission->set_source_table('collaborate_submissions',
                    array('collaborateid' => backup::VAR_PARENTID));
        }

        // Define file annotations. (For editor areas).
        $collaborate->annotate_files('mod_collaborate', 'intro', null);
        $collaborate->annotate_files('mod_collaborate', 'instructionsa', null);
        $collaborate->annotate_files('mod_collaborate', 'instructionsb', null);
        $submission->annotate_files('mod_collaborate', 'submission', 'id');

        // Return the root element (collaborate), wrapped into standard activity structure.
        return $this->prepare_activity_structure($collaborate);
    }
}
