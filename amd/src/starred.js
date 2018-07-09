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
 * This file contains an AMD/jQuery module to handle starring/unstarring of courses.
 *
 * @package    block_filtered_course_list
 * @copyright  2018 CLAMP
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/str'], function($, ajax, mstr) {
    return {
        init: function() {
            var SC = {}; // Namespace, basically.
            SC.str = {};

            /**
             * Set element class to reflect current course state
             * @param {bool} starred Display as starred or unstarred?
             */
            SC.setClass = function(starred) {
                $(SC.starlink).removeClass('course-starred course-unstarred');
                if (starred) {
                    $(SC.starlink).addClass('course-starred');
                } else {
                    $(SC.starlink).addClass('course-unstarred');
                }
            };

            /**
             * Make icon display as starred or unstarred.
             * @param {bool} starred Display as starred or unstarred?
             */
            SC.iconDisplay = function(starred) {
                var icon = $(SC.starlink).find('img.icon');
                var src = $(icon).attr('src');
                var name = src.match(/^(.*\/)(star|star-o)$/);
                var newsrc = src;
                if (starred) {
                    newsrc = name[1] + 'star';
                } else {
                    newsrc = name[1] + 'star-o';
                }
                icon.attr('src', newsrc);
            };

            /**
             * Make text display action (eg "star this course" or "unstar this course").
             */
            SC.textAction = function() {
                var linktext = SC.courseState ? SC.str.unstar : SC.str.star;
                $(SC.starlink).find('span.media-body').text(linktext);
            };

            /**
             * Make text display course state.
             */
            SC.textToBase = function() {
                var linktext = SC.courseState ? SC.str.starred : SC.str.unstarred;
                $(SC.starlink).find('span.media-body').text(linktext);
            };

            /**
             * Make icon display the reverse of the current course state.
             */
            SC.iconReverse = function() {
                SC.iconDisplay(!SC.courseState);
            };

            /**
             * Make icon display the current course state.
             */
            SC.iconToBase = function() {
                SC.iconDisplay(SC.courseState);
            };

            /**
             * Get the current starred state of the course.
             * @return {bool|null} Whether the course is starred or unstarred, or null on failure.
             */
            SC.getCourseState = function() {
                var icon = $(SC.starlink).find('img.icon');
                var src = $(icon).attr('src');
                var name = src.match(/^(.*\/)(star|star-o)$/);
                var linktext = $(SC.starlink).find('span.media-body').text();
                if (name[2] === 'star' && linktext === SC.str.starred) {
                    // Course is starred.
                    return true;
                } else if (name[2] === 'star-o'  && linktext === SC.str.unstarred) {
                    // Course is starred.
                    return false;
                }
                return null;
            };

            /**
             * Set the state of the course to starred or unstarred.
             * @param {bool} starred Display as starred or unstarred?
             */
            SC.setCourseState = function(starred) {
                SC.courseState = starred;
                SC.setClass(starred);
                SC.iconToBase();
                SC.textToBase();
            };

            /**
             * Main functionality. Has to wait on strings (see below).
             */
            SC.main = function() {
                // Set up some variables.
                SC.starlink = $("a[data-key='starlink']");
                SC.currentCourseID = $('body').attr('class').match(/(^|\s)course-(\d+)($|\s)/)[2];
                SC.ajaxActive = false; // Checking this will prevent state flickering.

                // Set initial course state based on state from backend.
                SC.setCourseState(SC.getCourseState());

                // Event listeners.
                $("a[data-key='starlink']").on({
                    'mouseenter': function() {
                        SC.iconReverse();
                        SC.textAction();
                    },
                    'mouseleave': function() {
                        if (!SC.ajaxActive) {
                            SC.iconToBase();
                            SC.textToBase();
                        }
                    },
                    'click': function(e) {
                        SC.ajaxActive = true;
                        ajax.call([{
                            methodname: 'local_starred_courses_toggle_starred',
                            args: {courseid: SC.currentCourseID},
                            done: function(r) {
                                SC.setCourseState(r);
                                SC.ajaxActive = false;
                            },
                            fail: function() {
                                SC.ajaxActive = false;
                            }
                        }]);
                        e.stopPropagation();
                    }
                });
            };

            /**
             * Checks that all of our strings are set before running main function.
             */
            SC.maybeMain = function() {
                if ((typeof SC.str.star !== 'undefined' && SC.str.star !== "")
                && (typeof SC.str.unstar !== 'undefined' && SC.str.unstar !== "")
                && (typeof SC.str.starred !== 'undefined' && SC.str.starred !== "")
                && (typeof SC.str.unstarred !== 'undefined' && SC.str.unstarred !== "")) {
                    SC.main();
                }
            };

            // Get strings.
            mstr.get_string('starlink:actionstar', 'local_starred_courses').done(function(r) {
                SC.str.star = r;
                SC.maybeMain();
            });
            mstr.get_string('starlink:actionunstar', 'local_starred_courses').done(function(r) {
                SC.str.unstar = r;
                SC.maybeMain();
            });
            mstr.get_string('starlink:statestarred', 'local_starred_courses').done(function(r) {
                SC.str.starred = r;
                SC.maybeMain();
            });
            mstr.get_string('starlink:stateunstarred', 'local_starred_courses').done(function(r) {
                SC.str.unstarred = r;
                SC.maybeMain();
            });
        }
    };
});
