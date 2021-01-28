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
 * Strings for the package.
 *
 * @package    local_advancedreminders
 * @author     Rodrigo Devolder <rodrigodevolder@gmail.com>
 * @copyright  2020 INDES-IDB (https://indes.iadb.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string["pluginname"] = "Advanced Reminders";
$string["admintreelabel"] = "Advanced Reminders";
$string["advancedreminderstask"] = "Advanced Reminders";

$string["admincrontest"] = "Cron test";
$string["admincrontest_link"] = "Start cron test";
$string["admincrontest_help"] = "Click the link below to start a test of sending notifications through cron.";

$string["enabled"] = "Enabled";
$string["enabled_help"] = "Enable or disable the Advanced Reminders plugin. It's necessary to enable each course too.";
$string["maxinactivity"] = "Maximum time of inactivity (in days)";
$string["maxinactivity_help"] = "Maximum amount of days to consider a user as inactive.";
$string["titleprefix"] = "Message title prefix";
$string["titleprefix_help"] = "This text will be inserted as a prefix (within square brackets) to the title of every message is being sent.";
$string["sendas"] = "Send as";
$string["sendas_admin"] = "As site admin";
$string["sendas_noreply"] = "No reply address";
$string["sendas_help"] = "Specify as who these reminder mails should be sent.";
$string["sendasname"] = "No reply name";
$string["sendasname_help"] = "Specify display user name for reminder mails when them are sent as No Reply user.";

$string["headercoursesettings"] = "Course Settings";
$string["courseenabled"] = "Course enabled";
$string["courseenabled_help"] = "Enable or disable the Advanced Reminders plugin in this course";
$string["allowedroles"] = "Allowed roles";
$string["allowedroles_help"] = "In addition to the roles, the user must be enrolled in this course, with confirmed email and with free access to this course.";

$string["headerinactivitysettings"] = "Inactivity Settings";
$string["inactivity_help"] = "Users who don't have completed the course and who have exceeded a minimum number of days without accessing the course are considered inactives. If never accessed, the starting date is the shortest date between the beginning of the course and the user's enrollment in the course.";
$string["mininactivity"] = "Minimum time of inactivity (in days)";
$string["mininactivity_help"] = "Minimum amount of days to consider a user as inactive.";
$string["intervalinactivity"] = "Interval of inactivity (in days)";
$string["intervalinactivity_help"] = "Number of days a user will receive only one notification of inactivity. That is, if a notification with inactivity reminder was sent today, another reminder will be sent only after the interval established in this field.";
$string["textinactivity"] = "Text to inactive users";
$string["textinactivity_help"] = "Text to be sent to the user considered inactive.<br /><br />The text will be sent in the language configured in the user profile. If text is not set in the user's language, the first text set in this field will be sent. Obeying the order.<br /><br />In the body of the text use the following codes:<br /><b>=USER=</b> for the user's full name<br /><b>=LINK=</b> for the name of the course with the link to the course<br /><b>=COURSE=</b> for the name of the course without the link to the course.";

$string["headeractivitiessettings"] = "Unfinished Activities Settings";
$string["minactivities"] = "Minimum time of unfinished activities (in days)";
$string["minactivities_help"] = "Minimum amount of days to consider an unfinished activity beginning on the first date that is setted in this list:<br />(1) Cut-off date of activity<br />(2) Due date of activity<br />(3) Date that the activity is allowed to be sent<br />(4) The highest date between the beginning of the course and the user's enrollment.";
$string["intervalactivities"] = "Interval of unfinished activities (in days)";
$string["intervalactivities_help"] = "Number of days a user will receive only a notification with a list of unfinished activities. That is, if a reminder notification of unfinished activity was sent today, another reminder will be sent only after the interval established in this field.";
$string["textactivities"] = "Text to users with unfinished activities";
$string["textactivities_help"] = "Text to be sent to the user that contains unfinished activities.<br /><br />The text will be sent in the language configured in the user profile. If text is not set in the user's language, the first text set in this field will be sent. Obeying the order.<br /><br />In the body of the text use the following codes:<br /><b>=LIST=</b> for the list of unfinished activities<br /><b>=USER=</b> for the user's full name<br /><b>=LINK=</b> for the name of the course with the link to the course<br /><b>=COURSE=</b> for the name of the course without the link to the course";

$string["headernocompletionsettings"] = "No Finished Activities Settings";
$string["minnocompletion"] = "Minimum time of no finished activities (in days)";
$string["minnocompletion_help"] = "Minimum amount of days to consider an no finished activity beginning on the first date that is setted in this list:<br />(1) Cut-off date of activity<br />(2) Due date of activity<br />(3) Date that the activity is allowed to be sent<br />(4) The highest date between the beginning of the course and the user's enrollment.";
$string["intervalnocompletion"] = "Interval of no finished activities (in days)";
$string["intervalnocompletion_help"] = "Number of days a user will receive only a notification with a list of no finished activities. That is, if a reminder notification of no finished activity was sent today, another reminder will be sent only after the interval established in this field.";
$string["textnocompletion"] = "Text to users with no finished activities";
$string["textnocompletion_help"] = "Text to be sent to the user that contains no finished activities.<br /><br />The text will be sent in the language configured in the user profile. If text is not set in the user's language, the first text set in this field will be sent. Obeying the order.<br /><br />In the body of the text use the following codes:<br /><b>=LIST=</b> for the list of no finished activities<br /><b>=USER=</b> for the user's full name<br /><b>=LINK=</b> for the name of the course with the link to the course<br /><b>=COURSE=</b> for the name of the course without the link to the course";

$string["runcron"] = "Run the cron";
$string["sureruncron"] = "Are you sure you want to run the cron?";
