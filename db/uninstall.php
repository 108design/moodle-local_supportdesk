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
 * File documentation for db/uninstall.php.
 *
 * @package   local_supportdesk
 * @copyright 2025 learn-ix support@learn-ix.com
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later
// Derived from learn-ix Academic Ticket System, copyright 2026 learn-ix.
// Modified 2026-10-03 by 108design.
defined('MOODLE_INTERNAL') || die();
function xmldb_local_supportdesk_uninstall() {
    global $DB;
    $id = (int)get_config('local_supportdesk', 'ownedroleid');
    $role = $id ? $DB->get_record('role', ['id' => $id, 'shortname' => 'supportdeskagent']) : false;
    if ($role) {
        $foreign = $DB->record_exists_select('role_capabilities',
            'roleid = :id AND capability NOT LIKE :prefix', ['id' => $id, 'prefix' => 'local/supportdesk:%']);
        if (!$foreign) {
            delete_role($id);
        }
    }
    return true;
}
