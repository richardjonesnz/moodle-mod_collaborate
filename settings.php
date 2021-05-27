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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>;.
/**
 * The settings form for the module (available to admins)
 *
 * @package    mod_collaborate
 * @copyright  2018 Richard Jones <richardnz@outlook.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_newmodule
 * @see https://github.com/justinhunt/moodle-mod_pairwork
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/mod/collaborate/lib.php');

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configcheckbox('mod_collaborate/enablereports',
        get_string('enablereports', 'mod_collaborate'),
        get_string('enablereports_desc', 'mod_collaborate'), 0));
}