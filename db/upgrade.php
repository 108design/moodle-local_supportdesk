<?php
// SPDX-License-Identifier: GPL-3.0-or-later
// Derived from learn-ix Academic Ticket System, copyright 2026 learn-ix.
// Modified 2026-10-03 by 108design: independent component, no ATS data migration.
defined('MOODLE_INTERNAL') || die();
function xmldb_local_supportdesk_upgrade($oldversion) {
    if ($oldversion < 2026100300) {
        update_capabilities('local_supportdesk');
        \local_supportdesk\local\installer::install_role();
        upgrade_plugin_savepoint(true, 2026100300, 'local', 'supportdesk');
    }
    if ($oldversion < 2026100301) {
        // Runtime dialog asset correction; no database or role changes.
        upgrade_plugin_savepoint(true, 2026100301, 'local', 'supportdesk');
    }
    if ($oldversion < 2026100302) {
        // Native title replaces an unsupported Bootstrap module; no data changes.
        upgrade_plugin_savepoint(true, 2026100302, 'local', 'supportdesk');
    }
    if ($oldversion < 2026100303) {
        // Native Moodle UI and assets only; existing tickets/roles/configuration retained.
        upgrade_plugin_savepoint(true, 2026100303, 'local', 'supportdesk');
    }
    if ($oldversion < 2026100304) {
        global $DB;
        $dbman = $DB->get_manager();
        $table = new xmldb_table('local_supportdesk_categories');
        $field = new xmldb_field('color', XMLDB_TYPE_CHAR, '7', null, XMLDB_NOTNULL, null, '#64748b', 'description');
        if (!$dbman->field_exists($table, $field)) { $dbman->add_field($table, $field); }
        // Live Viewing held only transient viewer information; the feature is retired.
        $presence = new xmldb_table('local_supportdesk_presence');
        if ($dbman->table_exists($presence)) { $dbman->drop_table($presence); }
        upgrade_plugin_savepoint(true, 2026100304, 'local', 'supportdesk');
    }
    if ($oldversion < 2026100305) {
        global $DB;
        $table = new xmldb_table('local_supportdesk_members');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('categoryid', XMLDB_KEY_FOREIGN, ['categoryid'], 'local_supportdesk_categories', ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_index('categoryuser', XMLDB_INDEX_UNIQUE, ['categoryid', 'userid']);
        if (!$DB->get_manager()->table_exists($table)) {$DB->get_manager()->create_table($table);}
        // No inferred team assignments and no automatic opt-in to visitor entry.
        if (get_config('local_supportdesk', 'visitoraccess') === false) {set_config('visitoraccess', 0, 'local_supportdesk');}
        upgrade_plugin_savepoint(true, 2026100305, 'local', 'supportdesk');
    }
    if ($oldversion < 2026100306) {
        // Replace only the former fictitious default; retain deliberately configured addresses and blanks.
        if (get_config('local_supportdesk', 'support_email') === 'noreply@example.com') {
            unset_config('support_email', 'local_supportdesk');
        }
        upgrade_plugin_savepoint(true, 2026100306, 'local', 'supportdesk');
    }
    return true;
}
