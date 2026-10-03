<?php
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk;

use local_supportdesk\local\access;
use local_supportdesk\local\installer;
use local_supportdesk\privacy\provider;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/** Regression coverage for permission boundaries, installation and real privacy data. */
final class baseline_test extends \core_privacy\tests\provider_testcase {
    private function ticket(int $userid, int $assignee = 0): \stdClass {
        global $DB;
        $ticket = (object)[
            'userid' => $userid, 'assigned_to' => $assignee, 'category_id' => 0,
            'title' => 'Private subject', 'description' => 'Private description', 'status' => 'open',
            'created_at' => time(), 'updated_at' => time(), 'created_by' => $userid,
            'ip_address' => '192.0.2.1', 'priority' => 'medium',
        ];
        $ticket->id = $DB->insert_record('local_supportdesk_tickets', $ticket);
        return $ticket;
    }
    private function attachment(string $area, int $itemid, int $userid): \stored_file {
        return get_file_storage()->create_file_from_string([
            'contextid' => \context_system::instance()->id, 'component' => 'local_supportdesk',
            'filearea' => $area, 'itemid' => $itemid, 'filepath' => '/', 'filename' => 'private.txt',
            'userid' => $userid,
        ], 'private content');
    }
    public function test_ticket_access_owner_assignee_staff_and_outsider(): void {
        $this->resetAfterTest();
        $owner = $this->getDataGenerator()->create_user();
        $other = $this->getDataGenerator()->create_user();
        $assigned = $this->getDataGenerator()->create_user();
        $staff = $this->getDataGenerator()->create_user();
        $ticket = $this->ticket($owner->id, $assigned->id);
        $this->setUser($owner);
        $this->assertTrue(access::can_view($ticket));
        $this->assertTrue(access::can_download('attachment', $ticket->id));
        $this->setUser($other);
        $this->assertFalse(access::can_view($ticket));
        $this->assertFalse(access::can_download('attachment', $ticket->id));
        $this->setUser($assigned);
        $this->assertTrue(access::can_view($ticket));
        $roleid = installer::install_role();
        role_assign($roleid, $staff->id, \context_system::instance()->id);
        $this->setUser($staff);
        $this->assertTrue(access::can_view($ticket));
        $this->assertTrue(access::can_download('attachment', $ticket->id));
        $this->setGuestUser();
        $this->assertFalse(access::can_view($ticket));
        $this->setUser(null);
        $this->assertFalse(access::can_view($ticket));
    }
    public function test_reply_attachment_resolves_its_parent_ticket(): void {
        global $DB;
        $this->resetAfterTest();
        $owner = $this->getDataGenerator()->create_user();
        $other = $this->getDataGenerator()->create_user();
        $ticket = $this->ticket($owner->id);
        $replyid = $DB->insert_record('local_supportdesk_replies', (object)[
            'ticket_id' => $ticket->id, 'userid' => $other->id, 'message' => 'Reply', 'created_at' => time(),
        ]);
        $this->attachment('reply_attachment', $replyid, $other->id);
        $this->setUser($owner);
        $this->assertTrue(access::can_download('reply_attachment', $replyid));
        $this->assertFalse(access::can_download('reply_attachment', $replyid + 10000));
        $this->assertFalse(access::can_download('unknown', $replyid));
        $this->setUser($other);
        $this->assertFalse(access::can_download('reply_attachment', $replyid));
    }
    public function test_alert_permission_alone_does_not_grant_ticket_management(): void {
        $this->resetAfterTest();
        $owner = $this->getDataGenerator()->create_user();
        $alertuser = $this->getDataGenerator()->create_user();
        $ticket = $this->ticket($owner->id);
        $role = $this->getDataGenerator()->create_role();
        assign_capability('local/supportdesk:specialist', CAP_ALLOW, $role, \context_system::instance()->id);
        role_assign($role, $alertuser->id, \context_system::instance()->id);
        $this->setUser($alertuser);
        $this->assertFalse(access::can_manage());
        $this->assertFalse(access::can_view($ticket));
    }
    public function test_download_requires_capability_even_for_owner(): void {
        $this->resetAfterTest();
        $owner = $this->getDataGenerator()->create_user();
        $ticket = $this->ticket($owner->id);
        $role = $this->getDataGenerator()->create_role();
        assign_capability('local/supportdesk:download', CAP_PROHIBIT, $role, \context_system::instance()->id);
        role_assign($role, $owner->id, \context_system::instance()->id);
        $this->setUser($owner);
        $this->assertTrue(access::can_view($ticket));
        $this->assertFalse(access::can_download('attachment', $ticket->id));
    }
    public function test_support_role_is_idempotent_and_limited_to_system(): void {
        global $DB;
        $this->resetAfterTest();
        $id = installer::install_role();
        $this->assertSame($id, installer::install_role());
        $this->assertSame('Support', $DB->get_field('role', 'name', ['id' => $id]));
        $this->assertEquals([CONTEXT_SYSTEM], array_values(get_role_contextlevels($id)));
        $caps = $DB->get_records('role_capabilities', ['roleid' => $id]);
        $this->assertNotEmpty($caps);
        foreach ($caps as $cap) {$this->assertStringStartsWith('local/supportdesk:', $cap->capability);}
        $this->assertArrayHasKey('local/supportdesk:specialist', array_column($caps, 'permission', 'capability'));
    }
    public function test_role_collision_does_not_grant_foreign_role_permissions(): void {
        global $DB;
        $this->resetAfterTest();
        $existing = $DB->get_record('role', ['shortname' => 'supportdeskagent']);
        if ($existing) {delete_role($existing->id);}
        unset_config('ownedroleid', 'local_supportdesk');
        $id = create_role('Unrelated', 'supportdeskagent', 'Unrelated existing role');
        try {
            installer::install_role();
            $this->fail('Expected collision');
        } catch (\moodle_exception $e) {
            $this->assertSame('rolecollision', $e->errorcode);
        }
        $this->assertFalse($DB->record_exists('role_capabilities', ['roleid' => $id]));
    }
    public function test_assignees_need_system_role_and_support_permission(): void {
        $this->resetAfterTest();
        $agent = $this->getDataGenerator()->create_user();
        $courseagent = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $id = installer::install_role();
        role_assign($id, $agent->id, \context_system::instance()->id);
        role_assign($id, $courseagent->id, \context_course::instance($course->id)->id);
        $this->setAdminUser();
        $users = access::assignable_users();
        $this->assertArrayHasKey($agent->id, $users);
        $this->assertArrayNotHasKey($courseagent->id, $users);
    }
    public function test_privacy_export_uses_actual_tables_and_attachments(): void {
        $this->resetAfterTest();
        $owner = $this->getDataGenerator()->create_user();
        $ticket = $this->ticket($owner->id);
        $this->attachment('attachment', $ticket->id, $owner->id);
        $context = \context_system::instance();
        // Database drivers may return numeric identifiers as strings.
        $contextids = provider::get_contexts_for_userid($owner->id)->get_contextids();
        $this->assertSame([(int)$context->id], array_map('intval', $contextids));
        $users = new userlist($context, 'local_supportdesk');
        provider::get_users_in_context($users);
        $this->assertContains((int)$owner->id, array_map('intval', $users->get_userids()));
        $approved = new approved_contextlist($owner, 'local_supportdesk', [$context->id]);
        provider::export_user_data($approved);
        $export = writer::with_context($context);
        $data = $export->get_data(['Support Desk', 'tickets', (string)$ticket->id]);
        $this->assertSame('Private subject', $data->title);
        $this->assertNotEmpty($export->get_files(['Support Desk', 'tickets', (string)$ticket->id]));
    }
    public function test_privacy_erases_user_without_removing_other_contributions(): void {
        global $DB;
        $this->resetAfterTest();
        $owner = $this->getDataGenerator()->create_user();
        $staff = $this->getDataGenerator()->create_user();
        $ticket = $this->ticket($owner->id, $staff->id);
        $this->attachment('attachment', $ticket->id, $owner->id);
        $reply = $DB->insert_record('local_supportdesk_replies', (object)[
            'ticket_id' => $ticket->id, 'userid' => $staff->id, 'message' => 'Staff contribution', 'created_at' => time(),
        ]);
        provider::delete_data_for_user(new approved_contextlist($owner, 'local_supportdesk', [\context_system::instance()->id]));
        $erased = $DB->get_record('local_supportdesk_tickets', ['id' => $ticket->id]);
        $this->assertEquals(0, $erased->userid);
        $this->assertEquals(0, $erased->created_by);
        $this->assertSame('', $erased->description);
        $this->assertSame('', $erased->ip_address);
        $this->assertEquals($staff->id, $erased->assigned_to);
        $this->assertTrue($DB->record_exists('local_supportdesk_replies', ['id' => $reply]));
        $this->assertEmpty(get_file_storage()->get_area_files(\context_system::instance()->id, 'local_supportdesk', 'attachment', $ticket->id));
        $this->assertEmpty(provider::get_contexts_for_userid($owner->id)->get_contextids());
    }
    public function test_bulk_erasure_and_wrong_context_guard(): void {
        global $DB;
        $this->resetAfterTest();
        $owner = $this->getDataGenerator()->create_user();
        $ticket = $this->ticket($owner->id);
        $course = $this->getDataGenerator()->create_course();
        provider::delete_data_for_all_users_in_context(\context_course::instance($course->id));
        $this->assertTrue($DB->record_exists('local_supportdesk_tickets', ['id' => $ticket->id]));
        provider::delete_data_for_users(new approved_userlist(\context_system::instance(), 'local_supportdesk', [$owner->id]));
        $this->assertEmpty(provider::get_contexts_for_userid($owner->id)->get_contextids());
        provider::delete_data_for_all_users_in_context(\context_system::instance());
        $this->assertEquals(0, $DB->count_records('local_supportdesk_tickets'));
    }
    public function test_status_and_priority_reject_unrecognised_values(): void {
        access::validate_status('closed');
        access::validate_priority('urgent');
        foreach (['status' => '<script>', 'priority' => 'anything'] as $type => $value) {
            try {
                $type === 'status' ? access::validate_status($value) : access::validate_priority($value);
                $this->fail('Expected rejection');
            } catch (\invalid_parameter_exception $e) {
                $this->assertNotEmpty($e->getMessage());
            }
        }
    }
    public function test_disabled_plugin_and_unused_legacy_colours(): void {
        $this->resetAfterTest();
        set_config('primary_color', '#fff;} body{display:none', 'local_supportdesk');
        $css = \local_supportdesk\local\presentation::colours();
        // Legacy colour settings must not override Moodle theme styling.
        $this->assertSame('', $css);
        set_config('enabled', '0', 'local_supportdesk');
        $this->expectException(\moodle_exception::class);
        access::require_enabled();
    }
}

