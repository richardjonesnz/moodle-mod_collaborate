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
 * Library of interface functions and constants for module collaborate
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * All the collaborate specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_collaborate
 * @copyright  2019 Richard Jones richardnz@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_collaborate
 * @see https://github.com/justinhunt/moodle-mod_collaborate */

use mod_collaborate\local\collaborate_editor;
use mod_collaborate\local\submissions;

defined('MOODLE_INTERNAL') || die();

/* Moodle core API */

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@link plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function collaborate_supports($feature) {

    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the collaborate into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $collaborate Submitted data from the form in mod_form.php
 * @param mod_collaborate_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted collaborate record
 */
function collaborate_add_instance(stdClass $collaborate, mod_collaborate_mod_form $mform = null) {
    global $DB;

    $collaborate->timecreated = time();

    // Add new instance with dummy data for the editor fields.
    $collaborate->instructionsa ='a';
    $collaborate->instructionsaformat = FORMAT_HTML;
    $collaborate->instructionsb ='b';
    $collaborate->instructionsbformat = FORMAT_HTML;

    $collaborate->id = $DB->insert_record('collaborate', $collaborate);

    // Call std Moodle file_postupdate_standard editor to save files,
    // and prepare editor content for saving in database.
    $cmid = $collaborate->coursemodule;
    $context = context_module::instance($cmid);
    $options = collaborate_editor::get_editor_options($context);
    $names = collaborate_editor::get_editor_names();

    foreach ($names as $name) {
        $collaborate =  file_postupdate_standard_editor($collaborate, $name, $options,
                $context, 'mod_collaborate', $name, $collaborate->id);
    }

    // OK editor data processed into two fields for database, update record.
    $DB->update_record('collaborate', $collaborate);

    // Update gradebook.
    collaborate_grade_item_update($collaborate);

    return $collaborate->id;
}

/**
 * Updates an instance of the collaborate in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $collaborate An object from the form in mod_form.php
 * @param mod_collaborate_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function collaborate_update_instance(stdClass $collaborate, mod_collaborate_mod_form $mform = null) {
    global $DB;

    $collaborate->timemodified = time();
    $collaborate->id = $collaborate->instance;

    // Save files and process editor content.
    $cmid        = $collaborate->coursemodule;
    $context = context_module::instance($cmid);
    $options = collaborate_editor::get_editor_options($context);
    $names = collaborate_editor::get_editor_names();

    foreach ($names as $name) {
        $collaborate =  file_postupdate_standard_editor($collaborate, $name, $options,
                $context, 'mod_collaborate', $name, $collaborate->id);

    }

    // Update gradebook.
    collaborate_grade_item_update($collaborate);

    // Update the database.
    return $DB->update_record('collaborate', $collaborate);
}

/**
 * This standard function will check all instances of this module
 * and make sure there are up-to-date events created for each of them.
 * If courseid = 0, then every collaborate event in the site is checked, else
 * only collaborate events belonging to the course specified are checked.
 * This is only required if the module is generating calendar events.
 *
 * @param int $courseid Course ID
 * @return bool
 */
function collaborate_refresh_events($courseid = 0) {
    global $DB;

    if ($courseid == 0) {
        if (!$collaborates = $DB->get_records('collaborate')) {
            return true;
        }
    } else {
        if (!$collaborates = $DB->get_records('collaborate', array('course' => $courseid))) {
            return true;
        }
    }

    foreach ($collaborates as $collaborate) {
        // Create a function such as the one below to deal with updating calendar events.
        // collaborate_update_events($collaborate);
    }

    return true;
}

/**
 * Removes an instance of the collaborate from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function collaborate_delete_instance($id) {
    global $DB;

    if (! $collaborate = $DB->get_record('collaborate', array('id' => $id))) {
        return false;
    }

    // Delete any dependent records here.
    $DB->delete_records('collaborate', array('id' => $collaborate->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 *
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param stdClass $course The course record
 * @param stdClass $user The user record
 * @param cm_info|stdClass $mod The course module info object or record
 * @param stdClass $collaborate The collaborate instance record
 * @return stdClass|null
 */
function collaborate_user_outline($course, $user, $mod, $collaborate) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * It is supposed to echo directly without returning a value.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $collaborate the module instance record
 */
function collaborate_user_complete($course, $user, $mod, $collaborate) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in collaborate activities and print it out.
 *
 * @param stdClass $course The course record
 * @param bool $viewfullnames Should we display full names
 * @param int $timestart Print activity since this timestamp
 * @return boolean True if anything was printed, otherwise false
 */
function collaborate_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link collaborate_print_recent_mod_activity()}.
 *
 * Returns void, it adds items into $activities and increases $index.
 *
 * @param array $activities sequentially indexed array of objects with added 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 */
function collaborate_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@link collaborate_get_recent_mod_activity()}
 *
 * @param stdClass $activity activity record with added 'cmid' property
 * @param int $courseid the id of the course we produce the report for
 * @param bool $detail print detailed report
 * @param array $modnames as returned by {@link get_module_types_names()}
 * @param bool $viewfullnames display users' full names
 */
function collaborate_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * Note that this has been deprecated in favour of scheduled task API.
 *
 * @return boolean
 */
function collaborate_cron () {
    return true;
}
/**
 * A task called from adhoc
 *
 * @param progress_trace trace object
 * @param $data - form data to update a database record
 */
function collaborate_do_adhoc_task(progress_trace $trace, $data) {
    global $DB;
    $trace->output('executing dotask');
    if ($DB->record_exists('collaborate', array('id' => $data->id))) {
        $DB->update_record('collaborate', $data);
    }
}
/**
 * Returns all other caps used in the module
 *
 * For example, this could be array('moodle/site:accessallgroups') if the
 * module uses that capability.
 *
 * @return array
 */
function collaborate_get_extra_capabilities() {
    return array();
}

/* Gradebook API */
/**
 * Is a given scale used by the instance of collaborate?
 *
 * This function returns if a scale is being used by one collaborate
 * if it has support for grading and scales.
 *
 * @param int $collaborateid ID of an instance of this module
 * @param int $scaleid ID of the scale
 * @return bool true if the scale is used by the given collaborate instance
 */
function collaborate_scale_used($collaborateid, $scaleid) {
    global $DB;
    if ($scaleid and $DB->record_exists('collaborate', array('id' => $collaborateid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}
/**
 * Checks if scale is being used by any instance of collaborate.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid ID of the scale
 * @return boolean true if the scale is used by any collaborate instance
 */
function collaborate_scale_used_anywhere($scaleid) {
    global $DB;
    if ($scaleid and $DB->record_exists('collaborate', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}
/**
 * Creates or updates grade item for the given collaborate instance
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $collaborate instance object with extra cmidnumber and modname property
 * @param bool $reset reset grades in the gradebook
 * @return void
 */
function collaborate_grade_item_update(stdClass $collaborate, $grades = null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $item = array();
    $item['itemname'] = clean_param($collaborate->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    if ($collaborate->grade > 0) {
        $item['gradetype'] = GRADE_TYPE_VALUE;
        $item['grademax']  = $collaborate->grade;
        $item['grademin']  = 0;
    } else if ($collaborate->grade < 0) {
        $item['gradetype'] = GRADE_TYPE_SCALE;
        $item['scaleid']   = -$collaborate->grade;
    } else {
        $item['gradetype'] = GRADE_TYPE_NONE;
    }
    // Implementing reset is covered in the documentation. https://docs.moodle.org/dev/Gradebook_API.
    if ($grades === 'reset') {
        $item['reset'] = true;
        $grades=null;
    }
    // Dropped return value into status to help debugging.
    $status = grade_update('mod/collaborate', $collaborate->course, 'mod', 'collaborate',
            $collaborate->id, 0, $grades, $item);
    return $status;
}
/**
 * Delete grade item for given collaborate instance
 *
 * @param stdClass $collaborate instance object
 * @return grade_item
 */
function collaborate_grade_item_delete($collaborate) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');
    return grade_update('mod/collaborate', $collaborate->course, 'mod', 'collaborate',
            $collaborate->id, 0, null, array('deleted' => 1));
}
/**
 * Update collaborate grades in the gradebook
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $collaborate instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 */
function collaborate_update_grades(stdClass $collaborate, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    // Populate array of grade objects indexed by userid.
    $grades = collaborate_get_user_grades($collaborate, $userid);

    // Do we have grades?
    if ($grades) {
        collaborate_grade_item_update($collaborate, $grades);
    } elseif ($userid) {

        // Single user specified, create initial grade item.
        $grade = new stdClass();
        $grade->userid = $userid;
        $grade->rawgrade = NULL;
        collaborate_grade_item_update($collaborate, $grade);
    } else {
        collaborate_grade_item_update($collaborate);
    }
}
function collaborate_get_user_grades($collaborate, $userid = 0) {
    global $CFG, $DB;

    $grades = array();
    if (empty($userid)) {
        // All user attempts for this collaborate instance are in the submissions table.
        $sql = "SELECT a.id, a.collaborateid,
                       a.userid, a.grade,
                       a.timecreated
                  FROM {collaborate_submissions} a
                 WHERE a.collaborateid = :cid
              GROUP BY a.userid";

        $slusers = $DB->get_records_sql($sql, ['cid' => $collaborate->id]);
        if ($slusers) {
            foreach ($slusers as $sluser) {
                $grades[$sluser->userid] = new stdClass();
                $grades[$sluser->userid]->id = $sluser->id;
                $grades[$sluser->userid]->userid = $sluser->userid;

                // Get this users attempts.
                $sql = "SELECT a.id, a.collaborateid,
                       a.userid, a.grade,
                       a.timecreated
                  FROM {collaborate_submissions} a
            INNER JOIN {user} u
                    ON u.id = a.userid
                 WHERE a.collaborateid = :cid
                   AND u.id = :uid";
                $attempts = $DB->get_records_sql($sql, ['cid' => $collaborate->id, 'uid' => $sluser->userid]);
                // Apply grading method.
                $grades[$sluser->userid]->rawgrade = submissions::grade_user($attempts);
            }
        } else {
            return false;
        }

    } else {
        // User grade for userid.
        $sql = "SELECT a.id, a.collaborateid,
                       a.userid, a.grade,
                       a.timecreated
                  FROM {collaborate_submissions} a
            INNER JOIN {user} u
                    ON u.id = a.userid
                 WHERE a.collaborateid = :cid
                   AND u.id = :uid";

        $attempts = $DB->get_records_sql($sql,
                array('cid' => $collaborate->id,
                      'uid' => $userid));
        if (!$attempts) {
            return false; // No attempt yet.
        }
        // Update grades for user.
        $grades[$userid] = new stdClass();
        $grades[$userid]->id = $collaborate->id;
        $grades[$userid]->userid = $userid;
        // Using selected grading strategy here.
        $grades[$userid]->rawgrade = submissions::grade_user($attempts);
    }
    return $grades;
}

/* File API */

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function collaborate_get_file_areas($course, $cm, $context) {
    return ['instructionsa' => 'Instructions for partner A',
            'instructionsb' => 'Instructions for partner B',
            'submissions' => 'Student submissions'];
}

/**
 * File browsing support for collaborate file areas
 *
 * @package mod_collaborate
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function collaborate_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the collaborate file areas
 *
 * @package mod_collaborate
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the collaborate's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function collaborate_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload,
        array $options=array()) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_collaborate/$filearea/$relativepath";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }
    // Finally send the file.
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/* Navigation API */

/**
 * Extends the global navigation tree by adding collaborate nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the collaborate module instance
 * @param stdClass $course current course record
 * @param stdClass $module current collaborate instance record
 * @param cm_info $cm course module information
 */
function collaborate_extend_navigation(navigation_node $navref, stdClass $course, stdClass $module, cm_info $cm) {
    // TODO Delete this function and its docblock, or implement it.
}

/**
 * Extends the settings navigation with the collaborate settings
 *
 * This function is called when the context for the page is a collaborate module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav complete settings navigation tree
 * @param navigation_node $collaboratenode collaborate administration node
 */
function collaborate_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $collaboratenode=null) {
    global $PAGE;

    // Extend the settings nav with the namechenger page url.
    $namechangeurl = new moodle_url('/mod/collaborate/namechanger.php', ['courseid' => $PAGE->course->id]);
    $collaboratenode->add(get_string('namechange', 'mod_collaborate'), $namechangeurl);
}