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
 * starred_courses block settings
 *
 * @package    local_starred_courses
 * @copyright  2018 onwards Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

define('STARRED_COURSES_USER_PREFERENCE_NAME', 'starred_courses');

/**
 * Extends course navigation menu to include the star toggle link.
 * @param global_navigation $navigation
 */
function local_starred_courses_extend_navigation($navigation) {
    global $COURSE, $USER, $CFG, $PAGE;

    // Check that we're supposed to be displaying the link.
    if (!$CFG->local_starred_courses_display_toggle) {
        return;
    }

    // Check that the user has permission.
    $context = \context_course::instance($COURSE->id);
    if (!has_capability('local/starred_courses:canstar', $context)) {
        return;
    }

    // If we're in a course...
    $coursenode = $navigation->find($COURSE->id, navigation_node::TYPE_COURSE);
    // And it's a regular course (ie, not the front page course)...
    $participants = $navigation->find('participants', navigation_node::TYPE_CONTAINER);

    if ($coursenode && $participants) {

        // Require JS.
        $PAGE->requires->js_call_amd('local_starred_courses/starred', 'init');

        // Check if course is starred.
        $starred = course_is_starred($USER->id, $COURSE->id);
        $linktext = $starred
            ? get_string('starlink:statestarred', 'local_starred_courses')
            : get_string('starlink:stateunstarred', 'local_starred_courses');
        $icon = $starred ? 'star' : 'star-o';

        // Make the navigation node.
        $node = $navigation->create(
            $linktext, // Link text.
            '#', // Link (we're using Ajax. If we leave blank, the page reloads.)
            navigation_node::TYPE_SETTING, // Node type.
            null, // "Shorttext".
            'starlink', // Key.
            new pix_icon($icon, '', 'local_starred_courses') // Icon.
        );
        $add = $coursenode->add_node($node, 'participants');
    }
}

/**
 * Check that the user preference has been set, and initialize it if not.
 * Otherwise trying to read the preference to check for starred courses
 * throws errors.
 * @param int $userid The id of the user we're intializing for.
 */
function initialize_starred_courses_user_preference($userid) {
    if (! get_user_preferences(STARRED_COURSES_USER_PREFERENCE_NAME, false, $userid)) {
        set_user_preference(STARRED_COURSES_USER_PREFERENCE_NAME, '', $userid);
    }
}

/**
 * Check whether or not a course is starred.
 * @param int $userid The id of the user we're checking for
 * @param int $courseid The id of the course we're checking for
 * @return bool Whether or not the course is starred
 */
function course_is_starred($userid, $courseid) {
    if ($starred = get_starred_course_ids($userid)) {
        return in_array($courseid, $starred);
    }
    return false;
}

/**
 * Star a course.
 * @param int $userid The id of the user we're starring for
 * @param int $courseid The id of the course we're starring
 * @return bool Whether or not the course was successfully starred
 */
function star_course($userid, $courseid) {
    $context = \context_course::instance($courseid);
    $hascap = has_capability('local/starred_courses:canstar', $context);
    if ($starred = get_starred_course_ids($userid) && $hascap) {
        if (! in_array($courseid, $starred)) {
            $starred[] = $courseid;
            $starred = implode(',', array_filter($starred));
            return set_user_preference(STARRED_COURSES_USER_PREFERENCE_NAME, $starred, $userid);
        }
    }
    return false;
}

/**
 * Unstar a course.
 * @param int $userid The id of the user we're unstarring for
 * @param int $courseid The id of the course we're unstarring
 * @return bool Whether or not the course was successfully unstarred
 */
function unstar_course($userid, $courseid) {
    $context = \context_course::instance($courseid);
    $hascap = has_capability('local/starred_courses:canstar', $context);
    if ($starred = get_starred_course_ids($userid) && $hascap) {
        if (($key = array_search($courseid, $starred)) !== false) {
            unset($starred[$key]);
            $starred = implode(',', array_filter($starred));
            return set_user_preference(STARRED_COURSES_USER_PREFERENCE_NAME, $starred, $userid);
        }
    }
    return false;
}

/**
 * Retrieve the ids of starred courses for the user.
 * @param int $userid The id of the user
 * @return array|bool The ids of the courses, or false on failure
 */
function get_starred_course_ids($userid) {
    $starred = get_user_preferences(STARRED_COURSES_USER_PREFERENCE_NAME, false, $userid);
    if ($starred = explode(',', $starred)) {
        return $starred;
    }
    return false;
}

/**
 * Retrieve course objects for each starred course for a given user.
 * @param int $userid The id of the user
 * @return array|bool Array of course objects, or false on failure
 */
function get_starred_courses($userid) {
    global $DB;

    $starred_courses = array();
    if ($starred_ids = get_starred_course_ids($userid)) {
        foreach ($starred_ids as $courseid) {
            $course = $DB->get_record('course', array('id' => $courseid));
            $starred_courses[] = $course;
        }
        return $starred_courses;
    }
    return false;
}
