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
 * Plugin class.
 *
 * @package    local_advancedreminders
 * @author     Rodrigo Devolder <rodrigodevolder@gmail.com>
 * @copyright  2019 INDES-IDB (https://indes.iadb.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_advancedreminders;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir .'/completionlib.php');
require_once($CFG->dirroot .'/local/advancedreminders/lib.php');

class class_advancedreminders {

	public $test = false;
	public $now = 0;
	public $loglevel = 0;
	public $inactivity = true;

	public function cron () {
		global $CFG, $DB;

		if(!isset($CFG->local_advancedreminders_enabled) || empty($CFG->local_advancedreminders_enabled)) {
			return $this->set_log('Not enabled on Moodle');
		}

		$this->loglevel = optional_param('loglevel', 0, PARAM_INT);
		$paraminactivity = optional_param('inactivity', true, PARAM_BOOL);
		if($this->test && $this->loglevel == 1 && $paraminactivity == false) $this->inactivity = false;

		$this->now = optional_param('now', 0, PARAM_INT);
		if(!$this->test || $this->now <= 0) $this->now = time();
		$this->set_log("now = {$this->now}<br />", 1);

		$coursesettings_records = $DB->get_records('local_advancedreminders_cs', ['courseenabled' => 1]);
		if(empty($coursesettings_records)) return $this->set_log('not enabled on any course');
		foreach($coursesettings_records as $coursesettings) {
			$this->cron_course($coursesettings);
		}

		$this->set_log('<br />Finished.');
    }

	private function cron_course ($coursesettings) {
		global $CFG, $DB;

		$this->set_log("Start course {$coursesettings->courseid}");

		$course = get_course($coursesettings->courseid);
		if(empty($course)) return $this->set_log('nonexistent course');
		if(empty($course->visible)) return $this->set_log('invisible course');
		if(empty($course->enablecompletion)) return $this->set_log('not enable completion on course');
		if($this->now < $course->startdate) return $this->set_log('course has not started');
		if(!empty($course->enddate) && $this->now > $course->enddate) return $this->set_log('course finalized');

		$arr_allowed_users = $this->get_allowed_users($coursesettings);
		if(empty($arr_allowed_users)) return $this->set_log('no enabled roles', 1);

		foreach($arr_allowed_users as $userid) {
			$user = $this->get_user($userid);
			if(empty($user)) continue;

			$lastaccess = $DB->get_field('user_lastaccess', 'timeaccess', ['courseid' => $coursesettings->courseid, 'userid' => $userid]);
			if(empty($lastaccess)) $lastaccess = $this->get_min_date($course, $userid);

			if($this->test && $this->loglevel == 1) {
				echo "<br />userid = $userid<br />";
				echo 'get_min_date = '. $this->get_min_date($course, $userid) .'<br />';
				echo 'lastaccess = '. (empty($lastaccess) ? 'N/A' : $lastaccess) .'<br />';
				echo 'c1 = '. (($this->now > $lastaccess + (86400 * $coursesettings->mininactivity)) ? 'y' : 'n') .'<br />';
				echo 'c2 = '. ((empty($CFG->local_advancedreminders_maxinactivity) || $this->now < $lastaccess + (86400 * $CFG->local_advancedreminders_maxinactivity)) ? 'y' : 'n') .'<br />';
			}

			if(!$this->inactivity) {
				$this->set_log('inactivity disabled', 1);

			} elseif(
				($this->now > $lastaccess + (86400 * $coursesettings->mininactivity)) &&
				(empty($CFG->local_advancedreminders_maxinactivity) || $this->now < $lastaccess + (86400 * $CFG->local_advancedreminders_maxinactivity))
			){
				$this->set_log('send_email inactivity', 1);
				$this->send_email(ADVANCEDREMINDERS_INACTIVITY, $course, $userid, $coursesettings);
				continue;
			} else {
				$this->set_log('NOT inactivity', 1);
			}

			$activities_not_completed = $this->activities_not_completed($course, $userid, $coursesettings);
			if(!empty($activities_not_completed)) {
				$this->set_log('send_email activities', 1);
				$this->send_email(ADVANCEDREMINDERS_ACTIVITIES, $course, $userid, $coursesettings, $activities_not_completed);
			} else {
				$this->set_log('NOT activities', 1);
			}
		}
    }

	private function get_allowed_users ($coursesettings) {
		global $DB;

		$arr_roles_enabled = [];
		$arr_roles_explode = explode(',', $coursesettings->allowedroles);
		$arr_roles = $DB->get_records('role', null, '', 'id,shortname');
		foreach($arr_roles as $roleid => $arr_value) {
			if(in_array($arr_value->shortname, $arr_roles_explode)) $arr_roles_enabled[] = $roleid;
		}

		$users = [];
		$arr_users = $this->enrol_get_course_users_roles($coursesettings->courseid);
		foreach($arr_users as $userid => $arr_value) {
			foreach($arr_value as $roleid => $value) {
				if(!in_array($roleid, $arr_roles_enabled)) continue;
				$users[] = $userid;
				break;
			}
		}
		return $users;
	}

	/**
	 * Returns list of roles per users into course.
	 *
	 * @param int $courseid Course id.
	 * @return array Array[$userid][$roleid] = role_assignment.
	 */
	private function enrol_get_course_users_roles($courseid) {
		global $DB;

		$context = \context_course::instance($courseid);

		$roles = array();

		$records = $DB->get_recordset('role_assignments', array('contextid' => $context->id));
		foreach ($records as $record) {
			if (isset($roles[$record->userid]) === false) {
				$roles[$record->userid] = array();
			}
			$roles[$record->userid][$record->roleid] = $record;
		}
		$records->close();

		return $roles;
	}

	private function activities_not_completed ($course, $userid, $coursesettings) {
        global $DB;

		$completion = new \completion_info($course);
        $activities = $completion->get_activities();

		$html = '';
        foreach($activities as $activity) {
            // Check if this activity is visible
            if(!$activity->visible || !$activity->uservisible) continue;

			$this->set_log("<br />activity = {$activity->id}", 1);

			$instance = $DB->get_record($activity->modname, ['id' => $activity->instance]);
			if(isset($instance->cutoffdate) && !empty($instance->cutoffdate)) {
				$ini = $instance->cutoffdate;
				$this->set_log("ini = cutoffdate = $ini", 1);
			} elseif(isset($instance->duedate) && !empty($instance->duedate)) {
				$ini = $instance->duedate;
				$this->set_log("ini = duedate = $ini", 1);
			} elseif(isset($instance->allowsubmissionsfromdate) && !empty($instance->allowsubmissionsfromdate)) {
				$ini = $instance->allowsubmissionsfromdate;
				$this->set_log("ini = allowsubmissionsfromdate = $ini", 1);
			} else {
				$ini = $this->get_min_date($course, $userid);
				$this->set_log("ini = get_min_date = $ini", 1);
			}

			if($this->now < $ini + (86400 * $coursesettings->minactivities)) continue;

			$this->set_log('time ok', 1);

            // Get progress information and state
            $data = $completion->get_data($activity, false, $userid);
			if($data->completionstate != COMPLETION_INCOMPLETE) continue;

			$this->set_log('has incomplete', 1);

			$html .= '<li>'. \html_writer::link($activity->url, "<b>{$activity->name}</b>") .'</li>';
		}
		if(!empty($html)) $html = "<ul>$html</ul>";

		return $html;
	}

	private function get_min_date ($course, $userid) {
		return max($course->startdate, $this->first_enrolment($course->id, $userid));
	}

	private function get_user ($userid) {
		global $DB;
		$user = $DB->get_record('user', ['id' => $userid]);

		if(empty($user)) return $this->set_log(get_string('invaliduser', 'error'));
		if($user->deleted) return $this->set_log(get_string('userdeleted'));
		if(empty($user->confirmed)) return $this->set_log(get_string('usernotconfirmed', 'moodle', $user->email));
		if(isguestuser($user)) return $this->set_log(get_string('guestsarenotallowed', 'error'));
		if($user->suspended) return $this->set_log(get_string('suspended', 'auth'));
		if($user->auth == 'nologin') return $this->set_log('nologin auth');
		return $user;
	}

	private function insert_sent_emails ($courseid, $userid, $type) {
		global $DB;

		$log = new \stdClass();
		$log->courseid = $courseid;
		$log->userid = $userid;
		$log->type = $type;
		$log->time = $this->now;
		$DB->insert_record('local_advancedreminders_se', $log);
	}

	private function send_email ($type, $course, $userid, $coursesettings, $htmlrows = '') {
		global $CFG, $DB;

		$typestr = ['Inactivity', 'Activities'];
		$user = $DB->get_record('user', ['id' => $userid]);
		$ignoresent = optional_param('ignoresent', false, PARAM_BOOL);

		//Verifica se o tempo minimo foi respeitado
		$typessettings = [$coursesettings->mininactivity, $coursesettings->minactivities];
		$last_records = $DB->get_records('local_advancedreminders_se', ['courseid' => $course->id, 'userid' => $userid, 'type' => $type], 'time');
		$last_row = array_pop($last_records);
		if((!$this->test || !$ignoresent) && !empty($last_row->time) && $this->now < $last_row->time + (86400 * $typessettings[$type])) {
			return $this->set_log("interval min not completed - type {$typestr[$type]} - user {$user->id} &lt;{$user->email}&gt;");
		}

		$typessettings = [$coursesettings->textinactivity, $coursesettings->textactivities];
		$arr_text = json_decode($typessettings[$type], true);
		foreach($arr_text as $key => $value) {
			if(empty(preg_replace('/\s/', '', strip_tags($value)))) unset($arr_text[$key]);
		}

		if(count($arr_text) == 1) {
			$text = array_shift($arr_text);
		} else {
			if(isset($arr_text[$user->lang])) {
				$text = $arr_text[$user->lang];
			} else {
				$text = array_shift($arr_text);
			}
		}

		if(!empty($htmlrows)) {
			$text = str_replace('=LIST=', $htmlrows, $text);
			$text = str_replace('=USER=', fullname($user), $text);
			$text = str_replace('=LINK=', "<a href='{$CFG->wwwroot}/course/view.php?id={$course->id}'>{$course->fullname}</a>", $text);
			$text = str_replace('=COURSE=', $course->fullname, $text);
		}

		$fromuser = \core_user::get_noreply_user();
		if(isset($CFG->local_reminders_sendas) && $CFG->local_reminders_sendas == ADVANCEDREMINDERS_SEND_AS_ADMIN) {
			//mtrace("  [Local Reminder] Sending all reminders as Admin User...");
			$fromuser = get_admin();

		} elseif(isset($CFG->local_advancedreminders_sendasname) && !empty($CFG->local_advancedreminders_sendasname)) {
			$fromuser->firstname = $CFG->local_advancedreminders_sendasname;
		}

        $subjectprefix = get_string('titlesubjectprefix', 'local_reminders');
        if (isset($CFG->local_advancedreminders_titleprefix) && !empty($CFG->local_advancedreminders_titleprefix)) {
            $subjectprefix = $CFG->local_advancedreminders_titleprefix;
        }

		if($this->test) {
			echo "<style>.advancedreminders_test{border:2px solid #808080;margin:10px 0;}.advancedreminders_email{padding:10px;}.advancedreminders_footer{padding:10px;background-color:#e6e6e6;}</style>";
			echo "<div class='advancedreminders_test'>";
			echo "<div class='advancedreminders_footer'><u><b>Email would be sent</b></u><br />User: {$user->id} &lt;{$user->email}&gt;<br />Type: {$typestr[$type]}</div>";
			echo "<div class='advancedreminders_email'>$text</div>";
			echo "</div>";
		} else {

			$eventdata = new \core\message\message();
			$eventdata->component         = 'local_advancedreminders';
			$eventdata->name              = 'local_advancedreminders';
			$eventdata->userfrom          = $fromuser;
			$eventdata->userto            = $userid;
			$eventdata->subject           = "[$subjectprefix] {$eventdata->name}";
			$eventdata->fullmessage       = strip_tags($text);
			$eventdata->fullmessageformat = FORMAT_HTML;
			$eventdata->fullmessagehtml   = $text;
			$eventdata->smallmessage      = "{$eventdata->name} - $text";
			$eventdata->notification      = 1;

			$mailresult = message_send($eventdata);
			$this->set_log("Mail Result: $mailresult");

			//email_to_user($user, $fromuser, $subjectprefix, $text, $text);

			$this->insert_sent_emails($course->id, $userid, $type);
		}
	}

	private function first_enrolment ($courseid, $userid) {
		global $DB;
		$sql = "SELECT ue.id, ue.timecreated
				  FROM {user_enrolments} ue
				  JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
				  JOIN {user} u ON u.id = ue.userid
				 WHERE ue.userid = :userid AND ue.status = :active AND e.status = :enabled AND u.deleted = 0";
		$params = ['enabled' => ENROL_INSTANCE_ENABLED, 'active' => ENROL_USER_ACTIVE, 'userid' => $userid, 'courseid' => $courseid];
		$enrolments = $DB->get_records_sql($sql, $params);
		if(empty($enrolments)) return 0;

		$arr = [];
		foreach($enrolments as $ue) {
			$arr[] = $ue->timecreated;
		}
		asort($arr);
		return array_shift($arr);
	}

	public function back_button($url) {
        global $OUTPUT;

		if (!($url instanceof \moodle_url)) {
            $url = new moodle_url($url);
        }
        $button = new \single_button($url, get_string('back'), 'get', true);
        $button->class = 'continuebutton';

        return $OUTPUT->render($button);
	}

	private function set_log ($log, $level = 0) {
		if($this->test && $level <= $this->loglevel) {
			echo "$log<br />";
		} elseif(!$this->test && $level == 0) {
			mtrace("  [Local Advanced Reminders] $log");
		}
		return false;
	}
}
