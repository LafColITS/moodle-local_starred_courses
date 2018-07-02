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
 * Starred Courses external web service functions.
 *
 * @package    local_starred_courses
 * @copyright  2018 onwards Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . "/externallib.php");
require_once('lib.php');

class local_starred_courses_external extends external_api {

    /**
     * Returns description of toggle_starred parameters.
     * @return external_function_parameters
     */
    public static function toggle_starred_parameters() {
        global $COURSE, $USER;

        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The user id we are starring for', VALUE_DEFAULT, $USER->id),
                'courseid' => new external_value(PARAM_INT, 'The course we are starring or unstarring', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Star or unstar a course.
     * @return bool|null Is the course now starred or unstarred? (or null on failure)
     */
    public static function toggle_starred($userid, $courseid) {
        $isstarred = course_is_starred($userid, $courseid);

        initialize_starred_courses_user_preference($userid);

        if ($isstarred) {
            if (unstar_course($userid, $courseid)) {
                return false;
            }
        } else {
            if (star_course($userid, $courseid)) {
                return true;
            }
        }
        return null;
    }

    /**
     * Returns description of toggle_starred return value.
     * @return external_description
     */
    public static function toggle_starred_returns() {
        return new external_value(PARAM_BOOL, 'Is the course now starred or unstarred?');
    }
}
