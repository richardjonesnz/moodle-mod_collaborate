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
 * Prints a collaborate grading page
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
use context_module;

/**
 * Create a new grading form page.
 */

class grading implements renderable, templatable {

    protected $submission;
    protected $context;
    protected $sid;

    public function __construct($submission, $cmid, $sid) {

        $this->submission = $submission;
        $this->context = context_module::instance($cmid);
        $this->sid = $sid;
    }
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {

        $this->submission->pageheader = get_string('gradingheader', 'mod_collaborate');

        // Submission.
        $content = file_rewrite_pluginfile_urls($this->submission->submission,
                'pluginfile.php', $this->context->id, 'mod_collaborate',
                'submission', $this->sid);

        // Format submission.
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->overflowdiv = true;
        $formatoptions->context = $this->context;
        $format = FORMAT_HTML;

        $this->submission->submission = format_text($content, $format, $formatoptions);

        return $this->submission;
    }
}