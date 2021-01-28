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
 * Plugin version information.
 *
 * @package    local_advancedreminders
 * @author     Rodrigo Devolder <rodrigodevolder@gmail.com>
 * @copyright  2019 INDES-IDB (https://indes.iadb.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_advancedreminders_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if($oldversion < 2020030600) {
        $table = new xmldb_table('local_advancedreminders_cs');

		$field = new xmldb_field('minnocompletion', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'textactivities');
		if(!$dbman->field_exists($table, $field)) $dbman->add_field($table, $field);

		$field = new xmldb_field('intervalnocompletion', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'minnocompletion');
		if(!$dbman->field_exists($table, $field)) $dbman->add_field($table, $field);

		$field = new xmldb_field('textnocompletion', XMLDB_TYPE_TEXT, null, null, null, null, null, 'intervalnocompletion');
		if(!$dbman->field_exists($table, $field)) $dbman->add_field($table, $field);

        upgrade_plugin_savepoint(true, 2020030600, 'local', 'advancedreminders');
    }
    return true;
}
