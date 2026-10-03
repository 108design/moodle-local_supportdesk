<?php
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
