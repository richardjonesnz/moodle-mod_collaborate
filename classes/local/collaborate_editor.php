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
 * Class providing custom editors
 *
 * @package   mod_collaborate
 * @copyright 2018 Richard Jones https://richardnz.net
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\local;
defined('MOODLE_INTERNAL') || die();

class collaborate_editor {

    /**
     * Editor attributes for mform
     *
     * @param object $mform form to add the editor to.
     * @param object $context, the module context.
     * @param string $name the name of the editor to add.
     * @return none.
     */
    public static function add_editor($mform, $context, $name) {
        // Extract the partner label from name (a or b).
        $partner = strtoupper(substr($name, -1));
        // Editor names should be formed like this by Moodle convention.
        $name = $name.'_editor';
        $mform->addElement('editor', $name, get_string('texteditor', 'mod_collaborate', $partner),
                null, self::get_editor_options($context));
        $mform->setType($name, PARAM_RAW);
    }

    /**
     * Names of the custom editors.
     *
     * @return string array of editor names.
     */
    public static function get_editor_names() {
      return ['instructionsa', 'instructionsb'];
    }
    /**
     * Editor options.
     *
     * @param object $context, the module context
     * @return mixed array of editor options.
     */
    public static function get_editor_options($context) {
        global $CFG;
        return ['subdirs' => true,
                      'maxbytes' => $CFG->maxbytes,
                      'maxfiles' => -1,
                      'changeformat' => 1,
                      'context' => $context,
                      'noclean' => true,
                      'trusttext' => false];
    }
}