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
 * File documentation for classes/local/access.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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

    /** Whether the site limits support staff to their departments and assignments. */
    public static function team_visibility_enabled(): bool {
        return (bool)get_config('local_supportdesk', 'teamvisibility');
    }

    /** In team mode, only site administrators have unrestricted access. */
    public static function can_manage_all(?int $userid = null): bool {
        return self::can_manage($userid) && (!self::team_visibility_enabled() || is_siteadmin($userid));
    }

    /** Editing memberships can grant access to all tickets in a department. */
    public static function can_manage_departments(): bool {
        return has_capability('local/supportdesk:addcategory', \context_system::instance())
            && (!self::team_visibility_enabled() || is_siteadmin());
    }

    /** Ticket ownership alone does not grant access to internal support tools. */
    public static function can_manage_ticket(\stdClass $ticket, ?int $userid = null): bool {
        global $USER;
        $userid = $userid ?? (int)$USER->id;
        if ($userid <= 0 || isguestuser($userid)) {
            return false;
        }
        if (self::can_manage_all($userid)) {
            return true;
        }
        $assigned = (int)$ticket->assigned_to === $userid;
        if (!self::team_visibility_enabled()) {
            return $assigned && has_capability('local/supportdesk:viewticket', \context_system::instance(), $userid);
        }
        return self::can_manage($userid)
            && ($assigned || in_array((int)$ticket->category_id, department_team::category_ids($userid), true));
    }

    public static function can_view(\stdClass $ticket, ?int $userid = null): bool {
        global $USER;
        $userid = $userid ?? (int)$USER->id;
        if ($userid <= 0 || isguestuser($userid)) {
            return false;
        }
        if (self::can_manage_ticket($ticket, $userid)) {
            return true;
        }
        return has_capability('local/supportdesk:viewticket', \context_system::instance(), $userid)
            && (int)$ticket->userid === $userid;
    }

    /**
     * Scope ticket queries before pagination, aggregation or limiting results.
     *
     * @param string $alias SQL alias of the ticket table, or an empty string
     * @param bool $staffonly omit requester-only tickets from the staff queue
     * @return array SQL predicate and named parameters
     */
    public static function visibility_sql(string $alias = '', bool $staffonly = false): array {
        global $DB, $USER;
        $userid = (int)$USER->id;
        if ($userid <= 0 || isguestuser($userid)) {
            return ['1 = 0', []];
        }
        if (self::can_manage_all($userid)) {
            return ['1 = 1', []];
        }

        $prefix = $alias === '' ? '' : $alias . '.';
        $canview = has_capability('local/supportdesk:viewticket', \context_system::instance(), $userid);
        $canmanage = self::can_manage($userid);
        $parts = [];
        $params = [];
        if (!$staffonly && $canview) {
            $parts[] = $prefix . 'userid = :sdowner';
            $params['sdowner'] = $userid;
        }
        if ($canmanage || (!$staffonly && $canview && !self::team_visibility_enabled())) {
            $parts[] = $prefix . 'assigned_to = :sdassignee';
            $params['sdassignee'] = $userid;
        }
        $categories = $canmanage ? department_team::category_ids($userid) : [];
        if ($categories) {
            [$in, $categoryparams] = $DB->get_in_or_equal($categories, SQL_PARAMS_NAMED, 'sddept');
            $parts[] = $prefix . 'category_id ' . $in;
            $params += $categoryparams;
        }
        return [$parts ? '(' . implode(' OR ', $parts) . ')' : '1 = 0', $params];
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

    /** Scoped staff can only assign tickets to staff already in the ticket's department. */
    public static function ticket_assignees(\stdClass $ticket): array {
        $users = self::assignable_users();
        return self::team_visibility_enabled() && !self::can_manage_all()
            ? array_intersect_key($users, department_team::members((int)$ticket->category_id))
            : $users;
    }

    public static function validate_status(string $status): void {
        if (!in_array($status, ['open', 'in progress', 'admin reply', 'student reply', 'closed'], true)) {
            throw new \moodle_exception('invalidstatus', 'local_supportdesk');
        }
    }

    public static function validate_priority(string $priority): void {
        if (!in_array($priority, ['low', 'medium', 'high', 'urgent'], true)) {
            throw new \moodle_exception('invalidpriority', 'local_supportdesk');
        }
    }
}
