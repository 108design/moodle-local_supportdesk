<?php
// SPDX-License-Identifier: GPL-3.0-or-later
// Derived from learn-ix Academic Ticket System, copyright 2026 learn-ix.
// Modified 2026-10-03 by 108design.
defined('MOODLE_INTERNAL') || die();
function xmldb_local_supportdesk_install() {
    set_config('visitoraccess', 0, 'local_supportdesk');
    global $DB, $USER;
    // Moodle registers plugin capabilities after the install callback by default.
    // Register them now so the new role can receive its explicit permissions.
    update_capabilities('local_supportdesk');
    \local_supportdesk\local\installer::install_role();
    $now = time();
    $DB->insert_record('local_supportdesk_categories', (object)[
        'title' => 'Support', 'description' => '', 'created_by' => (int)$USER->id,
        'created_at' => $now, 'updated_at' => $now,
    ]);
    return true;
}
