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
 * Plugin settings.
 *
 * @package    local_advancedreminders
 * @author     Rodrigo Devolder <rodrigodevolder@gmail.com>
 * @copyright  2020 INDES-IDB (https://indes.iadb.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    require_once($CFG->dirroot .'/local/advancedreminders/lib.php');

    $settings = new admin_settingpage('local_advancedreminders', get_string('admintreelabel', 'local_advancedreminders'));
    $ADMIN->add('localplugins', $settings);

	$actionurl = new moodle_url('/local/advancedreminders/test.php');
	$link = html_writer::link($actionurl, get_string('admincrontest_link', 'local_advancedreminders'));
	$settings->add(new admin_setting_heading('local_advancedreminders_admincrontest',
			get_string('admincrontest', 'local_advancedreminders'),
			get_string('admincrontest_help', 'local_advancedreminders') ."<br />$link<br /><br />"));

	$settings->add(new admin_setting_heading('local_advancedreminders_settings_heading',
			get_string('settings'),
			''));

    //Habilitar/Deshabilitar el plugin
    $settings->add(new admin_setting_configcheckbox('local_advancedreminders_enabled', 
            get_string('enabled', 'local_advancedreminders'), 
            get_string('enabled_help', 'local_advancedreminders'), 0));

    //Intervalo máximo de identificación de inactividad
	$settings->add(new admin_setting_configtext('local_advancedreminders_maxinactivity',
            get_string('maxinactivity', 'local_advancedreminders'),
            get_string('maxinactivity_help', 'local_advancedreminders'), '365'));

    //Prefijo del título de los mensajes
	$settings->add(new admin_setting_configtext('local_advancedreminders_titleprefix',
            get_string('titleprefix', 'local_advancedreminders'),
            get_string('titleprefix_help', 'local_advancedreminders'), 'Moodle Reminder'));

    //Enviar como de los emails
    $arr = array(ADVANCEDREMINDERS_SEND_AS_NO_REPLY => get_string('sendas_noreply', 'local_advancedreminders'),
                 ADVANCEDREMINDERS_SEND_AS_ADMIN    => get_string('sendas_admin',   'local_advancedreminders'));
	$settings->add(new admin_setting_configselect('local_advancedreminders_sendas',
        get_string('sendas', 'local_advancedreminders'),
        get_string('sendas_help', 'local_advancedreminders'),
        ADVANCEDREMINDERS_SEND_AS_NO_REPLY, $arr));

    //Nombre de los emails "No Reply"
	$settings->add(new admin_setting_configtext('local_advancedreminders_sendasname',
        get_string('sendasname', 'local_advancedreminders'),
        get_string('sendasname_help', 'local_advancedreminders'), 'No Reply'));
}
