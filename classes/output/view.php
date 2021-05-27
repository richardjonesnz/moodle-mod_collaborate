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
 * Prints a particular instance of collaborate
 *
 * @package    mod_collaborate
 * @copyright  202 Richard Jones richardnz@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_collaborate
 * @see https://github.com/justinhunt/moodle-mod_collaborate
 */

namespace mod_collaborate\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;
use moodle_url;

/**
 * Simplemod: Create a new view page renderable object
 *
 * @param object collaboate - collaborate instance
 * @param int id - course module id
 * @copyright  2020 Richard Jones <richardnz@outlook.com>
 */

class view implements renderable, templatable {

    protected $collaborate;
    protected $id;
    protected $reports;

    public function __construct($collaborate, $id, $reportstab) {

        $this->collaborate = $collaborate;
        $this->id = $id;
        $this->reports = $reportstab;
    }
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {

        $data = new stdClass();

        $data->title = $this->collaborate->title;
        // Moodle handles processing of std intro field.
        $data->body = format_module_intro('collaborate',
                $this->collaborate, $this->id);
        $data->message = get_string('welcome', 'mod_collaborate');

        // Set up the user page URLs.
        $a = new \moodle_url('/mod/collaborate/showpage.php', ['cid' => $this->collaborate->id, 'page' => 'a']);
        $b = new \moodle_url('/mod/collaborate/showpage.php', ['cid' => $this->collaborate->id, 'page' => 'b']);
        $data->url_a = $a->out(false);
        $data->url_b = $b->out(false);

        // Add links to reports tabs, if enabled.

        if ($this->reports) {
            $data->reports = $this->reports;
            $r = new moodle_url('/mod/collaborate/reports.php',
                    ['cid' => $this->collaborate->id]);
            $v = new moodle_url('/mod/collaborate/view.php', ['id' => $this->id]);
            $data->url_reports = $r->out(false);
            $data->url_view = $v->out(false);
        }

        return $data;
    }
}
