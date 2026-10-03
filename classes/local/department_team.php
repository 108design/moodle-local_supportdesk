<?php
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk\local;
defined('MOODLE_INTERNAL') || die();

/** Notification routing only: membership never grants or removes ticket access. */
class department_team {
    public static function eligible_users(): array {
        global $DB;
        $users = $DB->get_records_sql("SELECT DISTINCT u.* FROM {user} u
            JOIN {role_assignments} ra ON ra.userid = u.id JOIN {role} r ON r.id = ra.roleid
            WHERE r.shortname = :role AND ra.contextid = :contextid
                AND u.deleted = 0 AND u.suspended = 0 AND u.confirmed = 1 AND u.auth <> :nologin
            ORDER BY u.lastname, u.firstname, u.id", ['role' => 'supportdeskagent',
                'contextid' => \context_system::instance()->id, 'nologin' => 'nologin']);
        return array_filter($users, static fn($user) => access::can_manage((int)$user->id) && !isguestuser($user));
    }

    public static function members(int $categoryid): array {
        global $DB;
        $ids = $DB->get_records('local_supportdesk_members', ['categoryid' => $categoryid], '', 'userid, id');
        return array_intersect_key(self::eligible_users(), $ids);
    }

    public static function validate_members(array $ids): array {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        $eligible = self::eligible_users();
        foreach ($ids as $id) {
            if ($id <= 0 || !isset($eligible[$id])) {throw new \moodle_exception('invalidteammember', 'local_supportdesk');}
        }
        return $ids;
    }

    /** Caller owns category-admin capability, sesskey and transaction. */
    public static function replace_members(int $categoryid, array $ids): void {
        global $DB;
        $ids = self::validate_members($ids);
        $DB->get_record('local_supportdesk_categories', ['id' => $categoryid], 'id', MUST_EXIST);
        $transaction = $DB->start_delegated_transaction();
        $DB->delete_records('local_supportdesk_members', ['categoryid' => $categoryid]);
        foreach ($ids as $id) {
            $DB->insert_record('local_supportdesk_members', (object)['categoryid' => $categoryid, 'userid' => $id]);
        }
        $transaction->allow_commit();
    }

    public static function has_memberships(int $userid): bool {
        global $DB;
        return isset(self::eligible_users()[$userid]) && $DB->record_exists('local_supportdesk_members', ['userid' => $userid]);
    }
}
