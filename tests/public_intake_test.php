<?php
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk;
use local_supportdesk\local\public_intake;
use local_supportdesk\local\turnstile;
use local_supportdesk\local\access;
defined('MOODLE_INTERNAL') || die();

/** Deterministic verification, identity boundaries, abuse limits and privacy. */
final class public_intake_test extends \advanced_testcase {
    private function setup_intake(): array {
        global $DB, $SESSION;
        $this->resetAfterTest();
        $this->setGuestUser();
        $this->redirectMessages();
        set_config('enabled', 1, 'local_supportdesk');
        set_config('publiccreate', 1, 'local_supportdesk');
        set_config('visitormode', 'anonymous', 'local_supportdesk');
        set_config('turnstilesitekey', 'test-site-key', 'local_supportdesk');
        set_config('turnstilesecret', 'test-secret-key', 'local_supportdesk');
        set_config('support_email', '', 'local_supportdesk');
        $SESSION->supportdesk_publicclaims = [];
        $SESSION->supportdesk_publiclast = 0;
        $category = $DB->insert_record('local_supportdesk_categories', (object)['title' => 'Test area']);
        $intake = new class extends public_intake {
            public static bool $pass = true;
            protected static function verify(string $token): void {
                if (!self::$pass) {throw new \moodle_exception('public_verification_failed', 'local_supportdesk');}
            }
        };
        $intake::$pass = true;
        return [$intake, ['name' => 'Visitor', 'email' => 'visitor@example.invalid',
            'title' => 'Login problem', 'description' => '<script>alert(1)</script>', 'category_id' => $category]];
    }
    private function rejects(callable $fn, string $code): void {
        try {$fn(); $this->fail('Submission unexpectedly accepted');}
        catch (\moodle_exception $e) {$this->assertSame($code, $e->errorcode);}
    }
    public function test_creation_is_explicitly_opt_in_and_fails_closed(): void {
        global $DB;
        [$intake, $data] = $this->setup_intake();
        set_config('visitormode', 'login', 'local_supportdesk');
        $this->assertFalse(public_intake::enabled());
        $this->rejects(fn() => $intake::submit($data, [], 'token'), 'public_unavailable');
        set_config('publiccreate', 1, 'local_supportdesk');
        set_config('visitormode', 'anonymous', 'local_supportdesk');
        unset_config('turnstilesecret', 'local_supportdesk');
        $this->assertFalse(public_intake::enabled());
        set_config('turnstilesecret', 'test-secret', 'local_supportdesk');
        $intake::$pass = false;
        $this->rejects(fn() => $intake::submit($data, [], 'token'), 'public_verification_failed');
        $this->assertFalse($DB->record_exists('local_supportdesk_tickets', []));
    }
    public function test_visitor_email_never_assigns_an_existing_account_or_grants_access(): void {
        global $DB;
        [$intake, $data] = $this->setup_intake();
        $owner = $this->getDataGenerator()->create_user(['email' => $data['email']]);
        $before = $DB->count_records('user');
        $id = $intake::submit($data, [], 'token');
        $ticket = $DB->get_record('local_supportdesk_tickets', ['id' => $id]);
        $this->assertSame(0, (int)$ticket->userid);
        $this->assertSame(0, (int)$ticket->created_by);
        $this->assertStringNotContainsString('<script>', $ticket->description);
        $this->assertSame($before, $DB->count_records('user'));
        $this->assertFalse(access::can_view($ticket, (int)$owner->id));
        $this->assertFalse(access::can_view($ticket, 0));
        $this->assertFalse(access::can_download('attachment', $id, (int)$owner->id));
    }
    public function test_claim_requires_same_session_proof_email_and_confirmed_login(): void {
        global $DB, $SESSION;
        [$intake, $data] = $this->setup_intake();
        $id = $intake::submit($data, [], 'token');
        $proof = $SESSION->supportdesk_publicclaims[$id];
        $this->rejects(fn() => public_intake::claim($id), 'public_claim_failed');
        $wrong = $this->getDataGenerator()->create_user(['email' => 'other@example.invalid']);
        $this->setUser($wrong);
        // PHPUnit setUser resets SESSION; retain the original browser proof explicitly.
        $SESSION->supportdesk_publicclaims = [$id => $proof];
        $this->rejects(fn() => public_intake::claim($id), 'public_claim_failed');
        $owner = $this->getDataGenerator()->create_user(['email' => $data['email']]);
        $this->setUser($owner);
        $SESSION->supportdesk_publicclaims = [$id => $proof];
        unset($SESSION->supportdesk_publicclaims[$id]);
        $this->rejects(fn() => public_intake::claim($id), 'public_claim_failed');
        $SESSION->supportdesk_publicclaims[$id] = 'wrong-proof';
        $this->rejects(fn() => public_intake::claim($id), 'public_claim_failed');
        $SESSION->supportdesk_publicclaims[$id] = $proof;
        public_intake::claim($id);
        $ticket = $DB->get_record('local_supportdesk_tickets', ['id' => $id]);
        $this->assertSame((int)$owner->id, (int)$ticket->userid);
        $this->assertTrue(access::can_view($ticket, (int)$owner->id));
        $this->assertSame('', $DB->get_field('local_supportdesk_contacts', 'claimhash', ['ticketid' => $id]));
        $this->rejects(fn() => public_intake::claim($id), 'public_claim_failed');
        $context = new \core_privacy\local\request\approved_contextlist($owner, 'local_supportdesk', [\context_system::instance()->id]);
        \local_supportdesk\privacy\provider::delete_data_for_user($context);
        $this->assertFalse($DB->record_exists('local_supportdesk_contacts', ['ticketid' => $id]));
    }
    public function test_expired_claim_does_not_assign_ticket(): void {
        global $DB, $SESSION;
        [$intake, $data] = $this->setup_intake();
        $id = $intake::submit($data, [], 'token');
        $proof = $SESSION->supportdesk_publicclaims[$id];
        $this->setUser($this->getDataGenerator()->create_user(['email' => $data['email']]));
        $SESSION->supportdesk_publicclaims = [$id => $proof];
        $DB->set_field('local_supportdesk_contacts', 'timecreated', time() - DAYSECS - 1, ['ticketid' => $id]);
        $this->rejects(fn() => public_intake::claim($id), 'public_claim_failed');
        $this->assertSame(0, (int)$DB->get_field('local_supportdesk_tickets', 'userid', ['id' => $id]));
    }
    public function test_expected_claim_refusal_is_a_safe_ui_outcome(): void {
        global $DB, $SESSION;
        [$intake, $data] = $this->setup_intake();
        $id = $intake::submit($data, [], 'token');
        $proof = $SESSION->supportdesk_publicclaims[$id];
        $this->setUser($this->getDataGenerator()->create_user(['email' => 'wrong@example.invalid']));
        $SESSION->supportdesk_publicclaims = [$id => $proof];
        $this->assertFalse(\local_supportdesk\local\public_ui::try_claim($id));
        $this->assertSame(0, (int)$DB->get_field('local_supportdesk_tickets', 'userid', ['id' => $id]));
        $this->setUser($this->getDataGenerator()->create_user(['email' => $data['email']]));
        $SESSION->supportdesk_publicclaims = [$id => $proof];
        $this->assertTrue(\local_supportdesk\local\public_ui::try_claim($id));
        $this->assertFalse(\local_supportdesk\local\public_ui::try_claim($id));
    }
    public function test_submission_limit_and_honeypot_leave_no_extra_ticket(): void {
        global $DB;
        [$intake, $data] = $this->setup_intake();
        $this->rejects(fn() => $intake::submit($data + ['website' => 'bot'], [], 'token'), 'public_invalid');
        for ($i = 0; $i < 5; $i++) {$intake::submit($data, [], 'token');}
        $this->rejects(fn() => $intake::submit($data, [], 'token'), 'public_rate_limit');
        $this->assertSame(5, $DB->count_records('local_supportdesk_tickets'));
    }
    public function test_public_priority_choice_is_saved_and_invalid_values_are_rejected(): void {
        global $DB;
        [$intake, $data] = $this->setup_intake();
        foreach (['low', 'medium', 'high', 'urgent'] as $priority) {
            $id = $intake::submit($data + ['priority' => $priority], [], 'token');
            $this->assertSame($priority, $DB->get_field('local_supportdesk_tickets', 'priority', ['id' => $id]));
        }
        $this->rejects(fn() => $intake::submit($data + ['priority' => 'high1'], [], 'token'), 'public_invalid');
        $this->assertSame(4, $DB->count_records('local_supportdesk_tickets'));
    }
    public function test_turnstile_checks_hostname_action_and_strict_success_without_sending_content(): void {
        global $CFG;
        $this->setup_intake();
        $verifier = new class extends turnstile {
            public static array $reply = [];
            protected static function request(string $token): array {return self::$reply;}
        };
        $good = ['success' => true, 'hostname' => parse_url($CFG->wwwroot, PHP_URL_HOST), 'action' => 'supportdesk_create'];
        foreach ([[], ['success' => true], array_replace($good, ['hostname' => 'other.invalid']),
                array_replace($good, ['hostname' => ['malformed']]),
                array_replace($good, ['action' => 'login']), array_replace($good, ['success' => 'true']),
                ['success' => false, 'error-codes' => ['timeout-or-duplicate']]] as $bad) {
            $verifier::$reply = $bad;
            $this->rejects(fn() => $verifier::verify('token'), 'public_verification_failed');
        }
        $verifier::$reply = $good;
        $verifier::verify('token');
        $this->rejects(fn() => $verifier::verify(''), 'public_verification_failed');
        $this->rejects(fn() => $verifier::verify(str_repeat('x', 2049)), 'public_verification_failed');
        $this->assertTrue(turnstile::configured());
    }
}
