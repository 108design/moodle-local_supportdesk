<?php
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk\local;
defined('MOODLE_INTERNAL') || die();

/** Explicit routing: department team, ticket assignee, then configured support mailbox. */
class notifications {
    /** Resolve assignments before excluding the actor; self-actions never activate the mailbox fallback. */
    public static function staff_members(\stdClass $ticket): array {
        $users = department_team::members((int)$ticket->category_id);
        if ($users) {return $users;}
        $assigneeid = (int)($ticket->assigned_to ?? 0);
        if (!$assigneeid) {return [];}
        $user = access::assignable_users()[$assigneeid] ?? null;
        return $user && !empty($user->confirmed) && $user->auth !== 'nologin' && !isguestuser($user)
            ? [$assigneeid => $user] : [];
    }

    /** Only Moodle's explicitly configured support contact is a default; never invent an address. */
    public static function default_support_address(): string {
        global $CFG;
        $address = trim((string)($CFG->supportemail ?? ''));
        return validate_email($address) ? $address : '';
    }

    public static function support_address(): string {
        $configured = get_config('local_supportdesk', 'support_email');
        if ($configured === false || $configured === 'noreply@example.com') {
            return self::default_support_address();
        }
        $address = trim((string)$configured);
        return validate_email($address) ? $address : '';
    }

    public static function fallback_address(\stdClass $ticket, bool $toowner = false): string {
        // Opt-in copies include staff replies; the address is sent only once per event.
        if (get_config('local_supportdesk', 'copyallsupportemail')) {
            return self::support_address();
        }
        return $toowner || self::staff_members($ticket) ? '' : self::support_address();
    }

    public static function recipients(\stdClass $ticket, int $actorid, bool $toowner = false): array {
        global $DB;
        if (!$toowner) {
            $users = self::staff_members($ticket);
            unset($users[$actorid]);
            return $users;
        }
        $user = $DB->get_record('user', ['id' => $ticket->userid, 'deleted' => 0, 'suspended' => 0, 'confirmed' => 1]);
        return $user && (int)$user->id !== $actorid && !isguestuser($user) && access::can_view($ticket, (int)$user->id)
            ? [$user->id => $user] : [];
    }

    public static function send(\stdClass $ticket, int $actorid, string $event, bool $toowner = false): void {
        global $DB;
        $a = (object)['id' => $ticket->id, 'title' => $ticket->title,
            'category' => $DB->get_field('local_supportdesk_categories', 'title', ['id' => $ticket->category_id]) ?: '',
            'url' => (new \moodle_url('/local/supportdesk/view.php', ['id' => $ticket->id]))->out(false)];
        $subject = get_string('notify_' . $event, 'local_supportdesk', $a);
        $text = get_string('notify_body', 'local_supportdesk', $a);
        foreach (self::recipients($ticket, $actorid, $toowner) as $user) {
            $message = new \core\message\message();
            $message->component = 'local_supportdesk';
            $message->name = $toowner ? 'ticket_reply' : 'department_ticket';
            $message->userfrom = \core_user::get_noreply_user();
            $message->userto = $user;
            $message->subject = $subject;
            $message->fullmessage = $text;
            $message->fullmessageformat = FORMAT_PLAIN;
            $message->fullmessagehtml = text_to_html($message->fullmessage, false, false, true);
            $message->notification = 1;
            $message->contexturl = $a->url;
            $message->contexturlname = get_string('view_ticket', 'local_supportdesk');
            try {message_send($message);} catch (\Throwable $e) {debugging('Support Desk notification delivery failed', DEBUG_DEVELOPER);}
        }
        $fallback = self::fallback_address($ticket, $toowner);
        if ($fallback !== '') {
            try {
                if (!static::deliver_fallback($fallback, $subject, $text)) {
                    debugging('Support Desk fallback email delivery failed', DEBUG_DEVELOPER);
                }
            } catch (\Throwable $e) {debugging('Support Desk fallback email delivery failed', DEBUG_DEVELOPER);}
        }
    }

    /** Shared mailbox delivery does not create a Moodle user or grant access. */
    protected static function deliver_fallback(string $email, string $subject, string $text): bool {
        global $CFG;
        $recipient = (object)['id' => -1, 'email' => $email, 'firstname' => get_string('pluginname', 'local_supportdesk'),
            'lastname' => '', 'auth' => 'manual', 'mnethostid' => (int)$CFG->mnet_localhost_id,
            'mailformat' => 1, 'maildisplay' => 0, 'emailstop' => 0, 'deleted' => 0, 'suspended' => 0];
        return email_to_user($recipient, \core_user::get_noreply_user(), $subject, $text,
            text_to_html($text, false, false, true));
    }
}
