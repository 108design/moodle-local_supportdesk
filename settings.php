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
 * Settings for the Supportdesk.
 *
 * @package    local_supportdesk
 * @copyright  2026 learn-ix support@learn-ix.com
 * Modified 2026-10-03 by 108design: Supportdesk consolidation.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $settings = new admin_settingpage(
        'local_supportdesk',
        get_string('pluginname', 'local_supportdesk')
    );

    $settings->add(new admin_setting_configcheckbox(
        'local_supportdesk/enabled',
        get_string('enable', 'local_supportdesk'),
        get_string('enable_desc', 'local_supportdesk'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_supportdesk/add_to_navbar',
        get_string('add_to_navbar', 'local_supportdesk'),
        get_string('add_to_navbar_desc', 'local_supportdesk'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_supportdesk/allowvoicenotes',
        get_string('allowvoicenotes', 'local_supportdesk'),
        get_string('allowvoicenotes_desc', 'local_supportdesk'),
        0
    ));

    // Temporarily hidden until 108design Magic Link is publicly available/activatable.
    // Keep the setting and its guards intact; restore this flag for the public integration release.
    $showvisitoraccesssetting = false;
    if ($showvisitoraccesssetting) {
        $visitorstatus = \local_supportdesk\local\visitor_access::availability();
        $visitordesc = get_string('visitoraccess_desc', 'local_supportdesk');
        if (!$visitorstatus['ready']) {
            $visitordesc .= ' ' . get_string($visitorstatus['reason'], 'local_supportdesk');
        }
        $settings->add(new \local_supportdesk\local\visitor_setting(
            'local_supportdesk/visitoraccess', get_string('visitoraccess', 'local_supportdesk'), $visitordesc, 0
        ));
    }

    $settings->add(new admin_setting_configtext(
        'local_supportdesk/support_email',
        get_string('support_email', 'local_supportdesk'),
        get_string('support_email_desc', 'local_supportdesk'),
        \local_supportdesk\local\notifications::default_support_address(),
        PARAM_EMAIL
    ));



    $settings->add(new admin_setting_configtext(
        'local_supportdesk/system_name',
        get_string('system_name', 'local_supportdesk'),
        get_string('system_name_desc', 'local_supportdesk'),
        'Support',
        PARAM_TEXT
    ));

    $allroles = [];
    $excludedroles = [
        'student',
        'guest',
        'user',
        'frontpage',
        'teacher'
    ];
    foreach (role_get_names() as $role) {
        if (!in_array($role->shortname, $excludedroles)) {
            $allroles[$role->shortname] = $role->localname;
        }
    }

    $settings->add(new admin_setting_configmultiselect(
        'local_supportdesk/assignable_roles',
        get_string('assignable_roles', 'local_supportdesk'),
        get_string('assignable_roles_desc', 'local_supportdesk'),
        ['supportdeskagent'],
        $allroles
    ));

    $ADMIN->add('localplugins', $settings);
}
