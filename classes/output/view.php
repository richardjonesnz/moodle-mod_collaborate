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
 * @copyright  202 Richard Jones richardnz@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_simplemod
 * @see https://github.com/justinhunt/moodle-mod_simplemod
 */

namespace mod_simplemod\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Simplemod: Create a new view page renderable object
 *
 * @param string title - intro page title.
 * @param int height - course module id.
 * @copyright  2020 Richard Jones <richardnz@outlook.com>
 */

class view implements renderable, templatable {

    protected $simplemod;
    protected $id;

    public function __construct($simplemod, $id) {

        $this->simplemod = $simplemod;
        $this->id = $id;
    }
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {

        $data = new stdClass();

        $data->title = $this->simplemod->title;
        // Moodle handles processing of std intro field.
        $data->body = format_module_intro('simplemod',
                $this->simplemod, $this->id);

        return $data;
    }
}