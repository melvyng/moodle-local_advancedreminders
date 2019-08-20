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
 * @copyright  2019 INDES-IDB (https://indes.iadb.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$url = new moodle_url('/local/advancedreminders/test.php');
$PAGE->set_url($url);

require_login();

require_capability('moodle/site:config', context_system::instance());

$section = 'local_advancedreminders';
$strheading = get_string('admincrontest', 'local_advancedreminders');
$pluginname = get_string('pluginname', 'local_advancedreminders');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/admin/settings.php', array('section' => $section));
$PAGE->set_pagetype("admin-setting-$section");
$PAGE->set_pagelayout('admin');
$PAGE->navigation->clear_cache();
$PAGE->navbar->add($strheading);
if(isset($SITE->fullname)) $PAGE->set_heading($SITE->fullname);
$PAGE->set_title($strheading);
navigation_node::require_admin_tree();

$returl = new moodle_url('/admin/settings.php', array('section' => $section));

echo $OUTPUT->header();

echo "<h2>$pluginname - $strheading</h2>";

$class_advancedreminders = new \local_advancedreminders\class_advancedreminders();
$class_advancedreminders->test = true;
$class_advancedreminders->cron();

echo "<br /><br />";
echo $class_advancedreminders->back_button($returl);
echo $OUTPUT->footer();
