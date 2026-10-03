<?php
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.

namespace local_supportdesk\local;

defined('MOODLE_INTERNAL') || die();

/** Shared ticket and attachment authorisation. */
class access {
    public static function require_enabled(): void {
        if (get_config('local_supportdesk', 'enabled') === '0') {
            throw new \moodle_exception('disabled', 'local_supportdesk');
        }
    }

    public static function can_manage(?int $userid = null): bool {
        $context = \context_system::instance();
        return has_capability('local/supportdesk:manageticket', $context, $userid);
    }

    public static function can_view(\stdClass $ticket, ?int $userid = null): bool {
        global $USER;
        $userid = $userid ?? (int)$USER->id;
        if ($userid <= 0 || isguestuser($userid)) {
            return false;
        }
        if (self::can_manage($userid)) {
            return true;
        }
        return has_capability('local/supportdesk:viewticket', \context_system::instance(), $userid)
            && ((int)$ticket->userid === $userid || (int)$ticket->assigned_to === $userid);
    }

    /** Resolve reply attachments to their parent ticket before checking access. */
    public static function can_download(string $filearea, int $itemid, ?int $userid = null): bool {
        global $DB;
        if (!in_array($filearea, ['attachment', 'reply_attachment'], true)) {
            return false;
        }
        $ticketid = $itemid;
        if ($filearea === 'reply_attachment') {
            $ticketid = (int)$DB->get_field('local_supportdesk_replies', 'ticket_id', ['id' => $itemid]);
        }
        $ticket = $DB->get_record('local_supportdesk_tickets', ['id' => $ticketid]);
        return $ticket && self::can_view($ticket, $userid)
            && has_capability('local/supportdesk:download', \context_system::instance(), $userid);
    }

    /** Only configured system roles with explicit support capabilities can receive tickets. */
    public static function assignable_users(): array {
        global $DB;
        $roles = array_filter(explode(',', get_config('local_supportdesk', 'assignable_roles') ?: 'supportdeskagent'));
        if (!$roles) {
            return [];
        }
        [$in, $params] = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED);
        $params['contextid'] = \context_system::instance()->id;
        $users = $DB->get_records_sql("SELECT DISTINCT u.* FROM {user} u
            JOIN {role_assignments} ra ON ra.userid = u.id
            JOIN {role} r ON r.id = ra.roleid
            WHERE r.shortname $in AND ra.contextid = :contextid AND u.deleted = 0 AND u.suspended = 0", $params);
        return array_filter($users, static fn($user) => self::can_manage((int)$user->id));
    }

    public static function validate_status(string $status): void {
        if (!in_array($status, ['open', 'in progress', 'admin reply', 'student reply', 'closed'], true)) {
            throw new \invalid_parameter_exception('Invalid ticket status');
        }
    }

    public static function validate_priority(string $priority): void {
        if (!in_array($priority, ['low', 'medium', 'high', 'urgent'], true)) {
            throw new \invalid_parameter_exception('Invalid ticket priority');
        }
    }
}
