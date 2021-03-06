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
 * This file defines web services used by the Starred Courses plugin.
 *
 * @package    local_starred_courses
 * @copyright  2018 onwards Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(
    'local_starred_courses_toggle_starred' => array(
        'classname' => 'local_starred_courses_external',
        'methodname' => 'toggle_starred',
        'classpath' => 'local/starred_courses/externallib.php',
        'description' => get_string('external:description', 'local_starred_courses'),
        'type' => 'write',
        'ajax' => true
    ),
);
