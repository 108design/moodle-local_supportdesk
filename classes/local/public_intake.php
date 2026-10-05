<?php
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk\local;
defined('MOODLE_INTERNAL') || die();

/** Anonymous creation never grants anonymous reading or trusts a submitted account email. */
class public_intake {
    public static function enabled(): bool {
        return get_config('local_supportdesk', 'enabled') !== '0'
            && visitor_mode::selected() === 'anonymous' && turnstile::configured();
    }
    public static function submit(array $data, array $files, string $token): int {
        global $DB, $SESSION;
        if (!self::enabled()) {throw new \moodle_exception('public_unavailable', 'local_supportdesk');}
        foreach (['name' => 120, 'email' => 254, 'title' => 255, 'description' => 20000] as $field => $max) {
            if (trim($data[$field] ?? '') === '' || \core_text::strlen($data[$field]) > $max) {
                throw new \moodle_exception('public_invalid', 'local_supportdesk');
            }
        }
        if (!validate_email($data['email']) || !empty($data['website'])) {
            throw new \moodle_exception('public_invalid', 'local_supportdesk');
        }
        $priority = $data['priority'] ?? 'medium';
        try {
            access::validate_priority($priority);
        } catch (\invalid_parameter_exception $e) {
            throw new \moodle_exception('public_invalid', 'local_supportdesk');
        }
        $DB->get_record('local_supportdesk_categories', ['id' => (int)$data['category_id']], 'id', MUST_EXIST);
        uploads::validate($files, true);
        $ip = getremoteaddr();
        $lock = \core\lock\lock_config::get_lock_factory('local_supportdesk')->get_lock('public:' . hash('sha256', $ip), 5);
        if (!$lock) {throw new \moodle_exception('public_rate_limit', 'local_supportdesk');}
        try {
            if ($DB->count_records_select('local_supportdesk_tickets',
                    'created_by = 0 AND ip_address = :ip AND created_at > :since', ['ip' => $ip, 'since' => time() - 600]) >= 5) {
                throw new \moodle_exception('public_rate_limit', 'local_supportdesk');
            }
            static::verify($token);
            $transaction = $DB->start_delegated_transaction();
            $ticket = (object)['userid' => 0, 'created_by' => 0, 'assigned_to' => 0,
                'category_id' => (int)$data['category_id'], 'title' => $data['title'],
                'description' => nl2br(s($data['description'])), 'priority' => $priority, 'status' => 'open',
                'created_at' => time(), 'updated_at' => time(), 'ip_address' => $ip];
            $ticket->id = $DB->insert_record('local_supportdesk_tickets', $ticket);
            $claim = bin2hex(random_bytes(32));
            $DB->insert_record('local_supportdesk_contacts', (object)['ticketid' => $ticket->id,
                'userid' => 0, 'name' => $data['name'], 'email' => trim($data['email']),
                'claimhash' => hash('sha256', $claim), 'timecreated' => time()]);
            $fs = get_file_storage();
            $ordinal = 0;
            foreach ($files['name'] ?? [] as $i => $name) {
                if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {continue;}
                $name = clean_param($name, PARAM_FILE);
                $fs->create_file_from_pathname(['contextid' => \context_system::instance()->id,
                    'component' => 'local_supportdesk', 'filearea' => 'attachment', 'itemid' => $ticket->id,
                    'filepath' => '/', 'filename' => ++$ordinal . '-' . $name, 'userid' => 0], $files['tmp_name'][$i]);
            }
            $transaction->allow_commit();
            $pending = $SESSION->supportdesk_publicclaims ?? [];
            $pending[$ticket->id] = $claim;
            $SESSION->supportdesk_publicclaims = array_slice($pending, -10, null, true);
            $SESSION->supportdesk_publiclast = $ticket->id;
            try {notifications::send($ticket, 0, 'new');}
            catch (\Throwable $e) {debugging('Support Desk public ticket notification failed', DEBUG_DEVELOPER);}
            return (int)$ticket->id;
        } finally {$lock->release();}
    }
    /** Test seam for deterministic verification without a live provider or email. */
    protected static function verify(string $token): void {turnstile::verify($token);}

    public static function claim(int $ticketid): void {
        global $DB, $USER, $SESSION;
        access::require_enabled();
        if (!isloggedin() || isguestuser() || empty($USER->confirmed) || !empty($USER->suspended)
                || !empty($USER->deleted) || $USER->auth === 'nologin'
                || !has_capability('local/supportdesk:viewticket', \context_system::instance())) {
            throw new \moodle_exception('public_claim_failed', 'local_supportdesk');
        }
        $lock = \core\lock\lock_config::get_lock_factory('local_supportdesk')->get_lock('claim:' . $ticketid, 5);
        if (!$lock) {throw new \moodle_exception('public_claim_failed', 'local_supportdesk');}
        try {
            $contact = $DB->get_record('local_supportdesk_contacts', ['ticketid' => $ticketid]);
            $proof = $SESSION->supportdesk_publicclaims[$ticketid] ?? '';
            if (!$contact || $contact->userid || $contact->timecreated < time() - DAYSECS || $proof === ''
                    || !hash_equals($contact->claimhash, hash('sha256', $proof))
                    || \core_text::strtolower($contact->email) !== \core_text::strtolower($USER->email)) {
                throw new \moodle_exception('public_claim_failed', 'local_supportdesk');
            }
            $ticket = $DB->get_record('local_supportdesk_tickets', ['id' => $ticketid, 'userid' => 0]);
            if (!$ticket) {throw new \moodle_exception('public_claim_failed', 'local_supportdesk');}
            $transaction = $DB->start_delegated_transaction();
            $DB->set_field('local_supportdesk_tickets', 'userid', $USER->id, ['id' => $ticket->id]);
            $DB->update_record('local_supportdesk_contacts', (object)['id' => $contact->id,
                'userid' => $USER->id, 'claimhash' => '']);
            $transaction->allow_commit();
            unset($SESSION->supportdesk_publicclaims[$ticketid]);
        } finally {$lock->release();}
    }
}
