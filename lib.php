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
 * Plugin lib.
 *
 * @package    local_advancedreminders
 * @author     Rodrigo Devolder <rodrigodevolder@gmail.com>
 * @copyright  2020 INDES-IDB (https://indes.iadb.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/local/advancedreminders/classes/class_advancedreminders.php');

DEFINE('ADVANCEDREMINDERS_INACTIVITY', 0);
DEFINE('ADVANCEDREMINDERS_ACTIVITIES', 1);
DEFINE('ADVANCEDREMINDERS_NOCOMPLETION', 2);
DEFINE('ADVANCEDREMINDERS_SEND_AS_NO_REPLY', 0);
DEFINE('ADVANCEDREMINDERS_SEND_AS_ADMIN', 1);

function local_advancedreminders_extend_settings_navigation($settingsnav, $context) {
    global $PAGE;

    // Only add this settings item on non-site course pages.
    if(!$PAGE->course || $PAGE->course->id == 1) return;

    // Only let users with the appropriate capability see this settings item.
    if(!has_capability('moodle/course:update', context_course::instance($PAGE->course->id))) return;

    if($settingnode = $settingsnav->find('courseadmin', navigation_node::TYPE_COURSE)) {
        $name = get_string('admintreelabel', 'local_advancedreminders');
        $url = new moodle_url('/local/advancedreminders/coursesettings.php', ['courseid' => $PAGE->course->id]);
        $navnode = navigation_node::create(
            $name,
            $url,
            navigation_node::NODETYPE_LEAF,
            'advancedreminders',
            'advancedreminders',
            new pix_icon('i/calendar', $name)
        );
        if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
            $navnode->make_active();
        }
        $settingnode->add_node($navnode);
    }
}
