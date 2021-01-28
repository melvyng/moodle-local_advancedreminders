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
 * Plugin test.
 *
 * @package    local_advancedreminders
 * @author     Rodrigo Devolder <rodrigodevolder@gmail.com>
 * @copyright  2020 INDES-IDB (https://indes.iadb.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

$paramsesskey = optional_param('sesskey', null, PARAM_RAW);
$strheading = get_string('cron', 'admin');
$pluginname = get_string('pluginname', 'local_advancedreminders');
$runcron = get_string('runcron', 'local_advancedreminders');
$sureruncron = get_string('sureruncron', 'local_advancedreminders');
$sesskey = sesskey();

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/advancedreminders/cron.php');
$PAGE->set_title($strheading);

echo "<h2>$pluginname - $strheading</h2><br />";

if(empty($paramsesskey) || $paramsesskey !== $sesskey) {
	echo <<<EOF
<h3>$sureruncron</h3>
<form action="{$CFG->wwwroot}/local/advancedreminders/cron.php" method="POST">
    <input type="hidden" name='sesskey' value='$sesskey' />
	<input type="submit" value="$runcron" />
</form>
EOF;

} else {
	echo "<pre>";

	$class_advancedreminders = new \local_advancedreminders\class_advancedreminders();
	$class_advancedreminders->cron();

	echo "</pre><br /><br />";
}
