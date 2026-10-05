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

    public static function send(\stdClass $ticket, int $actorid, string $event, bool $toowner = false,
            ?\stdClass $reply = null): void {
        global $DB;
        $a = (object)['id' => $ticket->id, 'title' => $ticket->title,
            'category' => $DB->get_field('local_supportdesk_categories', 'title', ['id' => $ticket->category_id]) ?: '',
            'url' => (new \moodle_url('/local/supportdesk/view.php', ['id' => $ticket->id]))->out(false)];
        $subject = get_string('notify_' . $event, 'local_supportdesk', $a);
        $text = get_string('notify_body', 'local_supportdesk', $a);
        $maildestinations = [];
        // Anonymous contacts have no Moodle message recipient. Only send the exact public staff reply.
        if ($toowner && $event === 'reply' && empty($ticket->userid) && $reply
                && (int)$reply->ticket_id === (int)$ticket->id && (int)$reply->userid === $actorid) {
            $contact = $DB->get_record('local_supportdesk_contacts', ['ticketid' => $ticket->id, 'userid' => 0]);
            if ($contact && validate_email(trim($contact->email))) {
                $a->reply = html_to_text($reply->message, 0, false);
                $a->url = (new \moodle_url('/local/supportdesk/claim.php', ['id' => $ticket->id]))->out(false);
                try {
                    if (static::deliver_contact(trim($contact->email), $subject,
                            get_string('public_reply_body', 'local_supportdesk', $a), $a->url)) {
                        $maildestinations[\core_text::strtolower(trim($contact->email))] = true;
                    } else {debugging('Support Desk contact email delivery failed', DEBUG_DEVELOPER);}
                } catch (\Throwable $e) {debugging('Support Desk contact email delivery failed', DEBUG_DEVELOPER);}
            }
        }
        foreach (self::recipients($ticket, $actorid, $toowner) as $user) {
            $message = new \core\message\message();
            $message->component = 'local_supportdesk';
            $message->name = $toowner ? 'ticket_reply' : 'department_ticket';
            $message->userfrom = \core_user::get_noreply_user();
            $message->userto = $user;
            $message->subject = $subject;
            $message->fullmessage = $text;
            $message->fullmessageformat = FORMAT_PLAIN;
            $message->fullmessagehtml = self::email_html($message->fullmessage, $a->url);
            $message->notification = 1;
            $message->contexturl = $a->url;
            $message->contexturlname = get_string('view_ticket', 'local_supportdesk');
            try {
                if (message_send($message)) {
                    $destination = static::native_email_destination($user, $message->name);
                    if ($destination !== '') {$maildestinations[$destination] = true;}
                }
            } catch (\Throwable $e) {debugging('Support Desk notification delivery failed', DEBUG_DEVELOPER);}
        }
        $fallback = self::fallback_address($ticket, $toowner);
        if ($fallback !== '' && !isset($maildestinations[\core_text::strtolower(trim($fallback))])) {
            try {
                if (!static::deliver_fallback($fallback, $subject, $text, $a->url)) {
                    debugging('Support Desk fallback email delivery failed', DEBUG_DEVELOPER);
                }
            } catch (\Throwable $e) {debugging('Support Desk fallback email delivery failed', DEBUG_DEVELOPER);}
        }
    }

    /** Resolve native email routing; an in-app notification alone never replaces the mailbox copy. */
    protected static function native_email_destination(\stdClass $user, string $provider): string {
        global $CFG;
        require_once($CFG->dirroot . '/message/lib.php');
        if (!empty($user->deleted) || !empty($user->suspended) || $user->auth === 'nologin') {return '';}
        $processor = get_message_processors(true)['email'] ?? null;
        if (!$processor || !$processor->object->is_user_configured($user)) {return '';}
        $defaults = get_message_output_default_preferences();
        $base = 'local_supportdesk_' . $provider;
        $lockkey = 'email_provider_' . $base . '_locked';
        if (!empty($defaults->{$base . '_disable'}) || !isset($defaults->$lockkey)) {return '';}
        $prefkey = 'message_provider_' . $base . '_enabled';
        // Match Moodle 4.5/5.2 message_send: locked defaults override user preferences/emailstop.
        if (!empty($defaults->$lockkey)) {
            $outputs = $defaults->$prefkey ?? '';
        } else {
            if (!empty($user->emailstop)) {return '';}
            $outputs = get_user_preferences($prefkey, null, $user) ?: ($defaults->$prefkey ?? '');
        }
        if (!in_array('email', explode(',', $outputs), true)) {return '';}
        $recipient = clone $user;
        if (!empty($CFG->messagingallowemailoverride)) {
            $override = clean_param(get_user_preferences('message_processor_email_email', null, $user), PARAM_EMAIL);
            if ($override !== '') {$recipient->email = $override;}
        }
        $address = trim((string)$recipient->email);
        if (!validate_email($address) || over_bounce_threshold($recipient)) {return '';}
        return \core_text::strtolower($address);
    }

    /** Contact replies carry no access token or attachment and use the configured support Reply-To. */
    protected static function deliver_contact(string $email, string $subject, string $text, string $url): bool {
        global $CFG;
        $recipient = (object)['id' => -1, 'email' => $email, 'firstname' => get_string('pluginname', 'local_supportdesk'),
            'lastname' => '', 'firstnamephonetic' => '', 'lastnamephonetic' => '', 'middlename' => '', 'alternatename' => '',
            'auth' => 'manual', 'mnethostid' => (int)$CFG->mnet_localhost_id,
            'mailformat' => 1, 'maildisplay' => 0, 'emailstop' => 0, 'deleted' => 0, 'suspended' => 0];
        return email_to_user($recipient, \core_user::get_noreply_user(), $subject, $text,
            self::email_html($text, $url), '', '', true, self::support_address(),
            get_string('pluginname', 'local_supportdesk'));
    }

    /** Shared mailbox delivery does not create a Moodle user or grant access. */
    protected static function deliver_fallback(string $email, string $subject, string $text, string $url): bool {
        global $CFG;
        $recipient = (object)['id' => -1, 'email' => $email, 'firstname' => get_string('pluginname', 'local_supportdesk'),
            'lastname' => '', 'firstnamephonetic' => '', 'lastnamephonetic' => '', 'middlename' => '', 'alternatename' => '',
            'auth' => 'manual', 'mnethostid' => (int)$CFG->mnet_localhost_id,
            'mailformat' => 1, 'maildisplay' => 0, 'emailstop' => 0, 'deleted' => 0, 'suspended' => 0];
        return email_to_user($recipient, \core_user::get_noreply_user(), $subject, $text,
            self::email_html($text, $url));
    }

    /** Preserve plain message text; only the generated ticket URL becomes an HTML link. */
    private static function email_html(string $text, string $url): string {
        return str_replace(s($url), \html_writer::link($url, s($url)),
            text_to_html(s($text), false, false, true));
    }
}
