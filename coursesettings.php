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
 * Plugin course settings.
 *
 * @package    local_advancedreminders
 * @author     Rodrigo Devolder <rodrigodevolder@gmail.com>
 * @copyright  2019 INDES-IDB (https://indes.iadb.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/local/advancedreminders/coursesettings_form.php');
require_once($CFG->libdir.'/editor/atto/lib.php');
require_once($CFG->dirroot.'/local/advancedreminders/editor/atto.php');

$courseid = required_param('courseid', PARAM_INT);

$return = new moodle_url('/course/view.php', ['id' => $courseid]);

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$coursesettings = $DB->get_record('local_advancedreminders_cs', ['courseid' => $courseid]);

if(empty($coursesettings)) {
	$coursesettings = new stdClass();
} else {
	$arr = [];
	foreach(explode(',', $coursesettings->allowedroles) as $allowedrole) {
		if(empty($allowedrole)) continue;
		$arr[$allowedrole] = $allowedrole;
	}
	$coursesettings->allowedroles = $arr;
}

$coursesettings->courseid = $courseid;
$coursecontext = context_course::instance($course->id);

require_login($course);
require_capability('moodle/course:update', $coursecontext);

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/advancedreminders/coursesettings.php', ['courseid' => $courseid]);
$PAGE->set_title(get_string('admintreelabel', 'local_advancedreminders'));
$PAGE->set_heading($course->fullname);

$dyn_atto = new dynamic_atto_texteditor();
$dyn_atto->page_dynamic_js();

$mform = new local_advancedreminders_coursesettings_edit_form(null, [$coursesettings]);

if ($mform->is_cancelled()) {
    redirect($return);
} elseif($data = $mform->get_data()) {
	$data->allowedroles = trim(preg_replace('/\,{2,}/', ',', implode(',', $data->allowedroles)), ',');

    if(isset($coursesettings->id)) {
        $data->id = $coursesettings->id;
        $DB->update_record('local_advancedreminders_cs', $data);
    } else {
        $DB->insert_record('local_advancedreminders_cs', $data);
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('admintreelabel', 'local_advancedreminders'));

$mform->display();

echo $OUTPUT->footer();
