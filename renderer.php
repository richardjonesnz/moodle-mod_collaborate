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
 * @see https://github.com/justinhunt/moodle-mod_pairwork
 */
use \mod_pairwork\local\debugging;
defined('MOODLE_INTERNAL') || die();

/**
 * Renderer for Pairwork mod.
 */
class mod_pairwork_renderer extends plugin_renderer_base {

    /**
     * Returns the main content.
     *
     */
    public function fetch_page_content($pairwork, $cm) {

        $output = $this->output->header();
        $output .= $this->output->heading($pairwork->title);
        $output .= $this->output->box(
                format_module_intro('pairwork', $pairwork, $cm->id),
                'generalbox mod_introbox', 'pairworkintro');
        // Local debug/information/error logging.
        debugging::logit('Pairwork object: ', $pairwork);
        debugging::logit('Course module object: ', $cm);

        return $output;
    }
}