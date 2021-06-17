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
 * Define all the restore steps that will be used by the restore_collaborate_activity_task
 *
 * @package   mod_collaborate
 * @category  backup
 * @copyright 2019 Richard Jones richardnz@outlook.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_collaborate
 * @see https://github.com/justinhunt/moodle-mod_collaborate
 */

/**
 * Structure step to restore one collaborate activity
 *
 * @package   mod_collaborate
 * @category  backup
 * @copyright 2019 Richard Jones richardnz@outlook.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_collaborate_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines structure of path elements to be processed during the restore
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');
        $paths[] = new restore_path_element('collaborate', '/activity/collaborate');

        if ($userinfo) {

            $paths[] = new restore_path_element('collaborate_submissions',
                    '/activity/collaborate/submissions/submission');
        }


        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the given restore path element data
     *
     * @param array $data parsed element data
     */
    protected function process_collaborate($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        if (empty($data->timecreated)) {
            $data->timecreated = time();
        }

        if (empty($data->timemodified)) {
            $data->timemodified = time();
        }

        // Create the collaborate instance.
        $newitemid = $DB->insert_record('collaborate', $data);
        $this->apply_activity_instance($newitemid);
    }

    // Process the submission data.
    protected function process_collaborate_submissions($data) {
        global $DB;
        $data = (object) $data;
        $oldid = $data->id;
        $data->collaborateid = $this->get_new_parentid('collaborate');

        $newitemid = $DB->insert_record('collaborate_submissions', $data);
        $this->set_mapping('collaborate_submission', $oldid, $newitemid, true);

    }
    /**
     * Post-execution actions
     */
    protected function after_execute() {

        // Add collaborate related files for editor areas.
        $this->add_related_files('mod_collaborate', 'intro', null);
        $this->add_related_files('mod_collaborate', 'instructionsa', 'collaborate');
        $this->add_related_files('mod_collaborate', 'instructionsb', 'collaborate');
        $this->add_related_files('mod_collaborate', 'submission', 'collaborate_submission');

        // If anything else needed fixing up we could add it here.
    }
}