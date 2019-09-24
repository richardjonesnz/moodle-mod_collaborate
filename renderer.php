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
 * Custom renderer for output of pages
 *
 * @package    mod_simplelesson
 * @copyright  2019 Richard Jones <richardnz@outlook.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_simplemod
 * @see https://github.com/justinhunt/moodle-mod_simplemod
 */
use \mod_simplemod\local\debugging;
defined('MOODLE_INTERNAL') || die();

/**
 * Renderer for simplemod mod.
 */
class mod_simplemod_renderer extends plugin_renderer_base {

    /**
     * Displays the main view page content.
     *
     * @param $simplemod the simplemod instance std Object
     * @param $cm the course module std Object
     * @return none
     */
    public function render_view_page_content($simplemod, $cm) {

        $data = new stdClass();

        $data->heading = $simplemod->title;
        // Moodle handles processing of std intro field.
        $data->body = format_module_intro('simplemod',
                $simplemod, $cm->id);

        // Display the view page content.
        echo $this->output->header();
        echo $this->render_from_template('mod_simplemod/view', $data);
        echo $this->output->footer();
    }
}