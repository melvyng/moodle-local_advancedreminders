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
 * Extends atto class.
 *
 * @package    local_advancedreminders
 * @author     Rodrigo Devolder <rodrigodevolder@gmail.com>
 * @copyright  2020 INDES-IDB (https://indes.iadb.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class dynamic_atto_texteditor extends atto_texteditor {

    public function page_dynamic_js () {
		global $CFG, $PAGE;

        $PAGE->requires->strings_for_js(array(
                'editor_command_keycode',
                'editor_control_keycode',
                'plugin_title_shortcut',
                'textrecovered',
                'autosavefailed',
                'autosavesucceeded',
                'errortextrecovery'
            ), 'editor_atto');
        $PAGE->requires->strings_for_js(array(
                'warning',
                'info',
				'delete'
            ), 'moodle');
        $PAGE->requires->strings_for_js(array(
                'privacy:metadata:messages:subject'
            ), 'message');
			

		list($modules, $arguments) = $this->dynamic_use_editor('=ELEMENTID=', []);

		$arr = get_string_manager()->get_list_of_translations();
		$langs = [];
		foreach($arr as $key => $value) {
			$langs[$key] = substr($value, 0, strpos($value, '(') - 4);
		}

		$str_map = join(',', array_map('json_encode', convert_to_array($modules)));
		$str_call = preg_replace('/[\n\r\t]+/', '', str_replace('"=ELEMENTID="', 'elementid', js_writer::function_call('Y.M.editor_atto.Editor.init', [$arguments])));

        $PAGE->requires->js_amd_inline("require(['local_advancedreminders/main'],function(amd){amd.init(function(elementid){Y.use( $str_map, function(){ $str_call });}, ". json_encode($langs) .");});");
    }

    /**
     * Use this editor for given element.
     *
     * Available Atto-specific options:
     *   atto:toolbar - set to a string to override the system config editor_atto/toolbar
     *
     * Available general options:
     *   context - set to the current context object
     *   enable_filemanagement - set false to get rid of the managefiles plugin
     *   autosave - true/false to control autosave
     *
     * Options are also passed through to the plugins.
     *
     * @param string $elementid
     * @param array $options
     * @param null $fpoptions
     */
    public function dynamic_use_editor($elementid, array $options=null, $fpoptions=null) {
        global $PAGE;

        if (array_key_exists('atto:toolbar', $options)) {
            $configstr = $options['atto:toolbar'];
        } else {
            $configstr = get_config('editor_atto', 'toolbar');
        }

        $grouplines = explode("\n", $configstr);

        $groups = array();

        foreach ($grouplines as $groupline) {
            $line = explode('=', $groupline);
            if (count($line) > 1) {
                $group = trim(array_shift($line));
                $plugins = array_map('trim', explode(',', array_shift($line)));
                $groups[$group] = $plugins;
            }
        }

        $modules = array('moodle-editor_atto-editor');
        $options['context'] = empty($options['context']) ? context_system::instance() : $options['context'];

        $jsplugins = array();
        foreach ($groups as $group => $plugins) {
            $groupplugins = array();
            foreach ($plugins as $plugin) {
                // Do not die on missing plugin.
                if (!core_component::get_component_directory('atto_' . $plugin))  {
                    continue;
                }

                // Remove manage files if requested.
                if ($plugin == 'managefiles' && isset($options['enable_filemanagement']) && !$options['enable_filemanagement']) {
                    continue;
                }

                $jsplugin = array();
                $jsplugin['name'] = $plugin;
                $jsplugin['params'] = array();
                $modules[] = 'moodle-atto_' . $plugin . '-button';

                component_callback('atto_' . $plugin, 'strings_for_js');
                $extra = component_callback('atto_' . $plugin, 'params_for_js', array($elementid, $options, $fpoptions));

                if ($extra) {
                    $jsplugin = array_merge($jsplugin, $extra);
                }
                // We always need the plugin name.
                $PAGE->requires->string_for_js('pluginname', 'atto_' . $plugin);
                $groupplugins[] = $jsplugin;
            }
            $jsplugins[] = array('group'=>$group, 'plugins'=>$groupplugins);
        }

        /*$PAGE->requires->strings_for_js(array(
                'editor_command_keycode',
                'editor_control_keycode',
                'plugin_title_shortcut',
                'textrecovered',
                'autosavefailed',
                'autosavesucceeded',
                'errortextrecovery'
            ), 'editor_atto');
        $PAGE->requires->strings_for_js(array(
                'warning',
                'info'
            ), 'moodle');
        $PAGE->requires->yui_module($modules,
                                    'Y.M.editor_atto.Editor.init',
                                    array($this->get_init_params($elementid, $options, $fpoptions, $jsplugins)));*/

		return [$modules, $this->get_init_params($elementid, $options, $fpoptions, $jsplugins)];
    }
}
