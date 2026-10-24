<?php
/**
 * This file is part of Moodle - https://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle. If not, see <https://www.gnu.org/licenses/>.
 *
 * File documentation for classes/local/installer.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.

namespace local_supportdesk\local;

defined('MOODLE_INTERNAL') || die();

/** Idempotent plugin-owned support role; never reuse a generic custom role. */
class installer {
    public static function install_role(): int {
        global $DB;
        $role = $DB->get_record('role', ['shortname' => 'supportdeskagent']);
        if (!$role) {
            $id = create_role(get_string('rolespecialist', 'local_supportdesk'), 'supportdeskagent',
                get_string('rolespecialistdescription', 'local_supportdesk'), '');
            set_config('ownedroleid', $id, 'local_supportdesk');
            set_role_contextlevels($id, [CONTEXT_SYSTEM]);
        } else {
            $id = (int)$role->id;
            if ((int)get_config('local_supportdesk', 'ownedroleid') !== $id) {
                // A collision is not authority to grant access to an existing role.
                throw new \moodle_exception('rolecollision', 'local_supportdesk');
            }
        }
        $context = \context_system::instance();
        foreach (['manageticket', 'specialist', 'viewticket', 'download', 'addticket'] as $capability) {
            assign_capability('local/supportdesk:' . $capability, CAP_ALLOW, $id, $context->id, true);
        }
        return (int)$id;
    }
}
