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
 * Forms to the plugin course settings.
 *
 * @package    local_advancedreminders
 * @author     Rodrigo Devolder <rodrigodevolder@gmail.com>
 * @copyright  2019 INDES-IDB (https://indes.iadb.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

class local_advancedreminders_coursesettings_edit_form extends moodleform {

	function definition() {
        $mform = $this->_form;
        list($coursesettings) = $this->_customdata;

        //===================================
		$mform->addElement('header', 'header', get_string('headercoursesettings', 'local_advancedreminders'));

		//Habilitar/Deshabilitar el plugin
        $mform->addElement('advcheckbox', 'courseenabled', get_string('courseenabled', 'local_advancedreminders'), get_string('courseenabled_help', 'local_advancedreminders'));
        $mform->setDefault('courseenabled', 0);

		// load all roles in the moodle
		$typeitem = [];
		$defaultrolesforactivity = ['student', 'editingteacher'];
		$systemcontext = context_system::instance();
		$allroles = role_fix_names(get_all_roles(), $systemcontext, ROLENAME_ORIGINAL);
		foreach($allroles as $arole) {
			$typeitem[] = &$mform->createElement('advcheckbox', $arole->shortname, '', $arole->localname, ['name' => $arole->shortname, 'group' => 1], $arole->shortname);
			if(in_array($arole->shortname, $defaultrolesforactivity)) $mform->setDefault($arole->shortname, true);
		}
		$mform->addGroup($typeitem, 'allowedroles', get_string('allowedroles', 'local_advancedreminders'));
		$mform->addElement('html', "<div style='margin-left: 265px;'>". get_string('allowedroles_help', 'local_advancedreminders') ."</div>");

		//===================================
		$mform->addElement('header', 'header', get_string('headerinactivitysettings', 'local_advancedreminders'));

		//Definición de cuántos días de inatitud se enviará email
        $mform->addElement('text', 'mininactivity', get_string('mininactivity', 'local_advancedreminders'));
        $mform->setType('mininactivity', PARAM_INT);
		$mform->addHelpButton('mininactivity', 'mininactivity', 'local_advancedreminders');
        $mform->setDefault('mininactivity', '7');

		//Intervalo para inactividad
        $mform->addElement('text', 'intervalinactivity', get_string('intervalinactivity', 'local_advancedreminders'));
        $mform->setType('intervalinactivity', PARAM_INT);
		$mform->addHelpButton('intervalinactivity', 'intervalinactivity', 'local_advancedreminders');
        $mform->setDefault('intervalinactivity', '7');

		//Texto de inactividad por idioma
        $mform->addElement('textarea', 'textinactivity', get_string('textinactivity', 'local_advancedreminders'));
        $mform->setType('textinactivity', PARAM_CLEANHTML);
		$mform->addHelpButton('textinactivity', 'textinactivity', 'local_advancedreminders');
        $mform->setDefault('textinactivity', '');

		//===================================
		$mform->addElement('header', 'header', get_string('headeractivitiessettings', 'local_advancedreminders'));

		//Fecha inicial para recordatorios de actividades
        $mform->addElement('text', 'minactivities', get_string('minactivities', 'local_advancedreminders'));
        $mform->setType('minactivities', PARAM_INT);
		$mform->addHelpButton('minactivities', 'minactivities', 'local_advancedreminders');
        $mform->setDefault('minactivities', '7');

		//Intervalo para recordatorios de actividades
        $mform->addElement('text', 'intervalactivities', get_string('intervalactivities', 'local_advancedreminders'));
        $mform->setType('intervalactivities', PARAM_INT);
		$mform->addHelpButton('intervalactivities', 'intervalactivities', 'local_advancedreminders');
        $mform->setDefault('intervalactivities', '7');

		//Texto para recordatorios de actividades por idioma
        $mform->addElement('textarea', 'textactivities', get_string('textactivities', 'local_advancedreminders'));
        $mform->setType('textactivities', PARAM_CLEANHTML);
		$mform->addHelpButton('textactivities', 'textactivities', 'local_advancedreminders');
        $mform->setDefault('textactivities', '');

        //===================================
		$mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons(true);

        $this->set_data($coursesettings);
    }
}
