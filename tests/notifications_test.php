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
            protected static function deliver_fallback(string $email, string $subject, string $text): bool {
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
