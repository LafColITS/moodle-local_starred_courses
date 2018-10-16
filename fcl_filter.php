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
 * This file contains a filter class to be used by block_filtered_course_list to display starred courses.
 *
 * @package    local_starred_courses
 * @copyright  2018 Lafayette College
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('lib.php');

/**
 * A class to construct rubrics based on starred courses from local_starred_courses
 *
 * @package    block_filtered_course_list
 * @copyright  2018 CLAMP
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class starred_fcl_filter extends \block_filtered_course_list\filter {

    /** @var string The shortname of this filter (e.g. shortname, category) */
    public static $shortname = 'starred';
    /** @var string The fullname of this filter (e.g. Shortname, Course Category ) */
    public static $fullname = 'Starred Courses';
    /** @var string The component of this filter (e.g. block_filtered_course_list) */
    public static $component = 'local_starred_courses';

    /**
     * Validate the line
     *
     * @param array $line The array of line elements that has been passed to the constructor
     * @return array A fixed-up line array
     */
    public function validate_line($line) {
        $keys = array('expanded', 'label');
        $values = array_map(function($item) {
            return trim($item);
        }, explode('|', $line[1], 2));
        $this->validate_expanded(0, $values);
        if (!array_key_exists(1, $values)) {
            $values[2] = get_string('starred_default_label', 'block_filtered_course_list');
        }
        return array_combine($keys, $values);
    }

    /**
     * Populate the array of rubrics for this filter type
     *
     * @return array The list of rubric objects corresponding to the filter
     */
    public function get_rubrics() {
        global $USER;
        $courselist = get_starred_courses($USER->id);

        if (empty($courselist)) {
            return null;
        }

        $this->rubrics[] = new \block_filtered_course_list_rubric($this->line['label'],
                            $courselist, $this->config, $this->line['expanded']);
        return $this->rubrics;
    }
}