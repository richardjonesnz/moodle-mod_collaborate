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
 * @copyright  2018 Richard Jones <richardnz@outlook.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_newmodule
 * @see https://github.com/justinhunt/moodle-mod_widget
 */
use \mod_widget\local\debugging;
defined('MOODLE_INTERNAL') || die();

/**
 * Renderer for widget mod.
 */
class mod_widget_renderer extends plugin_renderer_base {

    /**
     * Returns the main content.
     *
     * @param $widget the widget instance std Object
     * @param $cm the course module std Object
     * @return $output, text/html to display content
     */
    public function fetch_view_page_content($widget, $cm) {

        $data = new stdClass();

        $data->heading = $widget->title;
        // Moodle handles processing of std intro field.
        $data->body = format_module_intro('widget',
                $widget, $cm->id);
        return $this->render_from_template('mod_widget/view', $data);
    }
}