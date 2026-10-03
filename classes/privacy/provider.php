<?php
// SPDX-License-Identifier: GPL-3.0-or-later
// Derived from learn-ix Academic Ticket System, copyright 2026 learn-ix.
// Modified 2026-10-03 by 108design: actual schema, files, bulk requests and erasure.
namespace local_supportdesk\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
defined('MOODLE_INTERNAL') || die();

class provider implements \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider, \core_privacy\local\request\core_userlist_provider {
    private const USERFIELDS = [
        'tickets' => ['userid', 'created_by', 'assigned_to'], 'replies' => ['userid'],
        'comments' => ['userid'], 'logs' => ['userid'], 'feedback' => ['userid'],
        'categories' => ['created_by'], 'members' => ['userid'],
    ];
    public static function get_metadata(collection $collection): collection {
        $fields = [
            'tickets' => ['userid', 'created_by', 'assigned_to', 'title', 'description', 'ip_address', 'created_at', 'updated_at'],
            'replies' => ['userid', 'ticket_id', 'message', 'created_at'],
            'comments' => ['userid', 'ticketid', 'content', 'created_at'],
            'logs' => ['userid', 'ticketid', 'actionname', 'oldvalue', 'newvalue', 'timemodified'],
            'feedback' => ['userid', 'ticketid', 'rating', 'comment', 'created_at'],
            'categories' => ['created_by', 'title', 'description', 'ip_address', 'created_at'],
            'members' => ['userid', 'categoryid'],
        ];
        foreach ($fields as $table => $names) {
            $collection->add_database_table('local_supportdesk_' . $table,
                array_fill_keys($names, 'privacy:metadata:field'), 'privacy:metadata:' . $table);
        }
        $collection->add_subsystem_link('core_files', [], 'privacy:metadata:files');
        $collection->add_subsystem_link('core_message', [], 'privacy:metadata:messages');
        $collection->add_external_location_link('support_mailbox', array_fill_keys(
            ['ticketid', 'title', 'category', 'url'], 'privacy:metadata:field'), 'privacy:metadata:fallback_email');
        return $collection;
    }
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;
        $list = new contextlist();
        foreach (self::USERFIELDS as $table => $fields) {
            foreach ($fields as $field) {
                if ($DB->record_exists('local_supportdesk_' . $table, [$field => $userid])) {
                    $list->add_system_context();
                    return $list;
                }
            }
        }
        return $list;
    }
    public static function get_users_in_context(userlist $userlist): void {
        if ($userlist->get_context()->contextlevel !== CONTEXT_SYSTEM) {return;}
        foreach (self::USERFIELDS as $table => $fields) {
            foreach ($fields as $field) {
                $userlist->add_from_sql($field, "SELECT $field FROM {local_supportdesk_$table} WHERE $field > 0", []);
            }
        }
    }
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;
        $context = \context_system::instance();
        if (!in_array($context->id, $contextlist->get_contextids())) {return;}
        $userid = (int)$contextlist->get_user()->id;
        $export = writer::with_context($context);
        foreach (self::USERFIELDS as $table => $fields) {
            $params = []; $clauses = [];
            foreach ($fields as $i => $field) {
                $params['u' . $i] = $userid;
                $clauses[] = "$field = :u$i";
            }
            $records = $DB->get_records_select('local_supportdesk_' . $table, implode(' OR ', $clauses), $params);
            foreach ($records as $record) {
                $path = [get_string('pluginname', 'local_supportdesk'), $table, (string)$record->id];
                $export->export_data($path, $record);
                if ($table === 'tickets' && (int)$record->userid === $userid) {
                    $export->export_area_files($path, 'local_supportdesk', 'attachment', $record->id);
                } else if ($table === 'replies') {
                    $export->export_area_files($path, 'local_supportdesk', 'reply_attachment', $record->id);
                }
            }
        }
    }
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;
        if ($context->contextlevel !== CONTEXT_SYSTEM) {return;}
        get_file_storage()->delete_area_files($context->id, 'local_supportdesk');
        foreach (self::USERFIELDS as $table => $fields) {$DB->delete_records('local_supportdesk_' . $table);}
    }
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        if (in_array(\context_system::instance()->id, $contextlist->get_contextids())) {
            self::erase_user((int)$contextlist->get_user()->id);
        }
    }
    public static function delete_data_for_users(approved_userlist $userlist): void {
        if ($userlist->get_context()->contextlevel === CONTEXT_SYSTEM) {
            foreach ($userlist->get_userids() as $userid) {self::erase_user((int)$userid);}
        }
    }
    /** Erase this user's content, retain other participants' independent records. */
    private static function erase_user(int $userid): void {
        global $DB;
        $DB->delete_records('local_supportdesk_members', ['userid' => $userid]);
        $context = \context_system::instance(); $fs = get_file_storage();
        foreach ($DB->get_records('local_supportdesk_tickets', ['userid' => $userid]) as $ticket) {
            $fs->delete_area_files($context->id, 'local_supportdesk', 'attachment', $ticket->id);
            $DB->update_record('local_supportdesk_tickets', (object)[
                'id' => $ticket->id, 'userid' => 0, 'title' => get_string('erasedticket', 'local_supportdesk'),
                'description' => '', 'ip_address' => '', 'attachment' => null,
            ]);
            $DB->delete_records('local_supportdesk_logs', ['ticketid' => $ticket->id]);
        }
        foreach ($DB->get_records('local_supportdesk_replies', ['userid' => $userid]) as $reply) {
            $fs->delete_area_files($context->id, 'local_supportdesk', 'reply_attachment', $reply->id);
        }
        foreach (['replies', 'comments', 'feedback', 'logs'] as $table) {
            $DB->delete_records('local_supportdesk_' . $table, ['userid' => $userid]);
        }
        foreach (['created_by', 'assigned_to'] as $field) {
            $DB->set_field('local_supportdesk_tickets', $field, 0, [$field => $userid]);
        }
        foreach ($DB->get_records('local_supportdesk_categories', ['created_by' => $userid]) as $category) {
            $DB->update_record('local_supportdesk_categories', (object)[
                'id' => $category->id, 'created_by' => 0, 'ip_address' => null,
            ]);
        }
    }
}
