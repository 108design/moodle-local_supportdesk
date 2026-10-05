<?php
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk;

use local_supportdesk\local\department_team;
use local_supportdesk\local\installer;
use local_supportdesk\local\notifications;

defined('MOODLE_INTERNAL') || die();

/** Protect team/assignee/fallback routing and the opt-in mailbox copy. */
final class notifications_test extends \advanced_testcase {
    public function test_default_routing_excludes_actor_without_activating_fallback(): void {
        global $DB;
        $this->resetAfterTest();
        [$ticket, $owner, $agent] = $this->fixture();
        unset_config('copyallsupportemail', 'local_supportdesk');
        set_config('support_email', 'qa@example.invalid', 'local_supportdesk');
        $this->assertSame([(int)$agent->id], array_map('intval', array_keys(notifications::recipients($ticket, $owner->id))));
        $this->assertSame([], notifications::recipients($ticket, $agent->id));
        $this->assertSame('', notifications::fallback_address($ticket));
        $this->assertSame('', notifications::fallback_address($ticket, true));
        $ticket->category_id = 0;
        $this->assertSame([(int)$agent->id], array_map('intval', array_keys(notifications::recipients($ticket, $owner->id))));
        $this->assertSame([], notifications::recipients($ticket, $agent->id));
        $this->assertSame('', notifications::fallback_address($ticket));
        $DB->set_field('user', 'suspended', 1, ['id' => $agent->id]);
        $this->assertSame([], notifications::recipients($ticket, $owner->id));
        $this->assertSame('qa@example.invalid', notifications::fallback_address($ticket));
    }

    public function test_opt_in_copies_each_event_once_without_changing_native_recipients(): void {
        $this->resetAfterTest();
        [$ticket, $owner, $agent] = $this->fixture();
        $sink = $this->redirectMessages();
        $captured = new class extends notifications {
            public static array $deliveries = [];
            protected static function deliver_fallback(string $email, string $subject, string $text, string $url): bool {
                self::$deliveries[] = [$email, $subject, $text];
                return true;
            }
        };
        set_config('support_email', 'qa@example.invalid', 'local_supportdesk');
        set_config('copyallsupportemail', 1, 'local_supportdesk');
        $captured::send($ticket, $owner->id, 'new');
        $captured::send($ticket, $agent->id, 'reply', true);
        $ticket->category_id = 0;
        $ticket->assigned_to = 0;
        $captured::send($ticket, $owner->id, 'moved');
        $this->assertCount(3, $captured::$deliveries);
        foreach ($captured::$deliveries as $delivery) {
            $this->assertSame('qa@example.invalid', $delivery[0]);
            $this->assertStringContainsString('/local/supportdesk/view.php?id=' . $ticket->id, $delivery[2]);
        }
        $this->assertSame([(int)$agent->id, (int)$owner->id],
            array_map('intval', array_column($sink->get_messages(), 'useridto')));
        set_config('support_email', '', 'local_supportdesk');
        $captured::send($ticket, $owner->id, 'new');
        set_config('support_email', "invalid\r\nBcc: qa@example.invalid", 'local_supportdesk');
        $captured::send($ticket, $owner->id, 'new');
        $this->assertCount(3, $captured::$deliveries);
        $sink->close();
    }

    public function test_same_mailbox_is_sent_once_for_team_assignee_and_buffered_notification(): void {
        global $DB;
        [$ticket, $owner, $agent, $sink] = $this->email_fixture();
        set_config('support_email', ' ' . strtoupper($agent->email) . ' ', 'local_supportdesk');
        notifications::send($ticket, $owner->id, 'new');
        $this->assertCount(1, $sink->get_messages());
        $this->assert_ticket_link($sink->get_messages()[0], '/local/supportdesk/view.php', (int)$ticket->id);
        $this->assertTrue($DB->record_exists('notifications', ['component' => 'local_supportdesk', 'useridto' => $agent->id]));
        $ticket->category_id = 0;
        notifications::send($ticket, $owner->id, 'moved');
        $this->assertCount(2, $sink->get_messages());
        $transaction = $DB->start_delegated_transaction();
        notifications::send($ticket, $owner->id, 'new');
        $this->assertCount(2, $sink->get_messages());
        $transaction->allow_commit();
        $this->assertCount(3, $sink->get_messages());
        $ticket->assigned_to = 0;
        notifications::send($ticket, $owner->id, 'new');
        $this->assertCount(4, $sink->get_messages());
        $this->assert_ticket_link($sink->get_messages()[3], '/local/supportdesk/view.php', (int)$ticket->id);
        $sink->close();
    }

    public function test_popup_disabled_email_and_failed_native_send_keep_mailbox_copy(): void {
        global $DB;
        [$ticket, $owner, $agent, $sink] = $this->email_fixture();
        set_config('support_email', $agent->email, 'local_supportdesk');
        set_user_preference('message_provider_local_supportdesk_department_ticket_enabled', 'popup', $agent);
        notifications::send($ticket, $owner->id, 'new');
        $this->assertCount(1, $sink->get_messages());
        $this->assertTrue($DB->record_exists('notifications', ['component' => 'local_supportdesk', 'useridto' => $agent->id]));
        set_user_preference('message_provider_local_supportdesk_department_ticket_enabled', 'email', $agent);
        $DB->set_field('user', 'emailstop', 1, ['id' => $agent->id]);
        notifications::send($ticket, $owner->id, 'new');
        $this->assertCount(2, $sink->get_messages());
        // Forced email is selected by Moodle even when personal notifications are disabled.
        set_config('email_provider_local_supportdesk_department_ticket_locked', 1, 'message');
        notifications::send($ticket, $owner->id, 'new');
        $this->assertCount(3, $sink->get_messages());
        set_config('message_provider_local_supportdesk_department_ticket_enabled', 'popup', 'message');
        notifications::send($ticket, $owner->id, 'new');
        $this->assertCount(4, $sink->get_messages());
        set_config('local_supportdesk_department_ticket_disable', 1, 'message');
        notifications::send($ticket, $owner->id, 'new');
        $this->assertCount(5, $sink->get_messages());
        \core_message\api::update_processor_status($DB->get_record('message_processors', ['name' => 'email']), 0);
        get_message_processors(false, true);
        unset_config('local_supportdesk_department_ticket_disable', 'message');
        notifications::send($ticket, $owner->id, 'new');
        $this->assertCount(6, $sink->get_messages());
        $sink->close();
    }

    public function test_notification_email_override_owner_and_excluded_actor_are_respected(): void {
        global $CFG;
        [$ticket, $owner, $agent, $sink] = $this->email_fixture();
        $CFG->messagingallowemailoverride = 1;
        set_user_preference('message_processor_email_email', 'shared@example.com', $agent);
        set_config('support_email', 'shared@example.com', 'local_supportdesk');
        notifications::send($ticket, $owner->id, 'new');
        $this->assertCount(1, $sink->get_messages());
        $this->assertSame('shared@example.com', $sink->get_messages()[0]->to);
        $CFG->messagingallowemailoverride = 0;
        notifications::send($ticket, $owner->id, 'new');
        $this->assertCount(3, $sink->get_messages());
        set_config('support_email', $owner->email, 'local_supportdesk');
        notifications::send($ticket, $agent->id, 'reply', true);
        $this->assertCount(4, $sink->get_messages());
        set_config('support_email', $agent->email, 'local_supportdesk');
        notifications::send($ticket, $agent->id, 'new');
        $this->assertCount(5, $sink->get_messages());
        $sink->close();
    }

    /** Anonymous replies use the contact address without creating or authenticating an account. */
    public function test_anonymous_contact_receives_public_reply_with_support_reply_to(): void {
        global $DB;
        [$ticket, $owner, $agent, $sink] = $this->email_fixture();
        $ticket->userid = 0;
        $DB->set_field('local_supportdesk_tickets', 'userid', 0, ['id' => $ticket->id]);
        $DB->insert_record('local_supportdesk_contacts', (object)['ticketid' => $ticket->id, 'userid' => 0,
            'name' => 'Visitor', 'email' => 'visitor@example.com', 'claimhash' => 'PRIVATE_PROOF', 'timecreated' => time()]);
        set_config('support_email', 'central@example.com', 'local_supportdesk');
        set_config('copyallsupportemail', 0, 'local_supportdesk');
        $reply = (object)['ticket_id' => $ticket->id, 'userid' => $agent->id, 'message' => '<p>Public staff answer</p>'];
        notifications::send($ticket, $agent->id, 'reply', true, $reply);
        $this->assertCount(1, $sink->get_messages());
        $mail = $sink->get_messages()[0];
        $this->assert_ticket_link($mail, '/local/supportdesk/claim.php', (int)$ticket->id);
        $this->assertSame('visitor@example.com', $mail->to);
        $this->assertStringContainsString('Public staff answer', quoted_printable_decode($mail->body));
        $this->assertStringNotContainsString('PRIVATE_PROOF', $mail->body);
        $this->assertStringContainsString('Reply-To:', $mail->header);
        $this->assertStringContainsString('central@example.com', $mail->header);
        set_config('copyallsupportemail', 1, 'local_supportdesk');
        notifications::send($ticket, $agent->id, 'reply', true, $reply);
        $this->assertCount(3, $sink->get_messages());
        set_config('support_email', 'VISITOR@example.com', 'local_supportdesk');
        notifications::send($ticket, $agent->id, 'reply', true, $reply);
        $this->assertCount(4, $sink->get_messages());
        notifications::send($ticket, $agent->id, 'reply', true);
        $this->assertCount(5, $sink->get_messages()); // Mailbox event only; no guest reply without exact content.
        $sink->close();
    }

    /** Inspect the HTML MIME part, rather than relying on a mail client's auto-linking. */
    private function assert_ticket_link(\stdClass $mail, string $path, int $id): void {
        $url = (new \moodle_url($path, ['id' => $id]))->out(false);
        $this->assertStringContainsString('href="' . s($url) . '"', quoted_printable_decode($mail->body));
    }

    /** Exercise real Moodle email output with PHPUnit interception, never SMTP. */
    private function email_fixture(): array {
        global $CFG, $DB;
        $this->resetAfterTest();
        $this->preventResetByRollback();
        [$ticket, $owner, $agent] = $this->fixture();
        $CFG->noemailever = false;
        $CFG->handlebounces = false;
        $CFG->noreplyaddress = 'noreply@example.com';
        \core_message\api::update_processor_status($DB->get_record('message_processors', ['name' => 'email']), 1);
        get_message_processors(false, true);
        foreach (['department_ticket', 'ticket_reply'] as $provider) {
            set_config('email_provider_local_supportdesk_' . $provider . '_locked', 0, 'message');
            set_config('message_provider_local_supportdesk_' . $provider . '_enabled', 'email', 'message');
        }
        set_config('copyallsupportemail', 1, 'local_supportdesk');
        return [$ticket, $owner, $agent, $this->redirectEmails()];
    }

    private function fixture(): array {
        global $DB;
        $this->setAdminUser();
        set_config('enabled', 1, 'local_supportdesk');
        $owner = $this->getDataGenerator()->create_user();
        $agent = $this->getDataGenerator()->create_user();
        role_assign(installer::install_role(), $agent->id, \context_system::instance()->id);
        $categoryid = $DB->insert_record('local_supportdesk_categories', (object)[
            'title' => 'Artificial area', 'created_at' => time(), 'updated_at' => time(),
        ]);
        department_team::replace_members($categoryid, [$agent->id]);
        $ticket = (object)['userid' => $owner->id, 'assigned_to' => $agent->id, 'category_id' => $categoryid,
            'title' => 'Artificial ticket', 'description' => 'Artificial description', 'status' => 'open',
            'priority' => 'medium', 'created_by' => $owner->id, 'created_at' => time(), 'updated_at' => time()];
        $ticket->id = $DB->insert_record('local_supportdesk_tickets', $ticket);
        return [$ticket, $owner, $agent];
    }
}
