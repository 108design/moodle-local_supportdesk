<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

namespace local_supportdesk;

use local_supportdesk\local\department_team;
use local_supportdesk\local\installer;

/**
 * Reviewer regressions for the actual footer hook and urgent-ticket audience.
 *
 * @package local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class reviewer_test extends \advanced_testcase {
    /** Build only synthetic staff, department and recent ticket state. */
    private function fixture(): array {
        global $DB;
        $staff = $this->getDataGenerator()->create_user();
        $owner = $this->getDataGenerator()->create_user();
        $role = installer::install_role();
        $context = \context_system::instance();
        role_assign($role, $staff->id, $context->id);
        $category = $DB->insert_record('local_supportdesk_categories', (object)[
            'title' => 'Reviewer team', 'description' => '', 'color' => '#663399',
        ]);
        department_team::replace_members($category, [$staff->id]);
        $ticket = $DB->insert_record('local_supportdesk_tickets', (object)[
            'userid' => $owner->id, 'assigned_to' => 0, 'category_id' => $category,
            'title' => 'Reviewer urgent ticket', 'description' => 'Synthetic issue', 'status' => 'open',
            'created_at' => time(), 'updated_at' => time(), 'created_by' => $owner->id,
            'ip_address' => '192.0.2.1', 'priority' => 'urgent',
        ]);
        set_config('enabled', 1, 'local_supportdesk');
        $this->setUser($staff);
        return [$staff, $role, $category, $ticket];
    }

    /** Dispatch through Core's registry rather than calling an unregistered listener. */
    private function footer_code(): string {
        global $PAGE;
        $PAGE = new \moodle_page();
        $PAGE->set_context(\context_system::instance());
        $PAGE->set_url('/my/index.php');
        $renderer = $PAGE->get_renderer('core');
        \core\di::get(\core\hook\manager::class)->dispatch(
            new \core\hook\output\before_footer_html_generation($renderer)
        );
        return $PAGE->requires->get_end_code();
    }

    public function test_registered_footer_reaches_eligible_staff_only(): void {
        global $DB;
        $this->resetAfterTest();
        [$staff, $role, $category] = $this->fixture();
        $this->assertStringContainsString('local_supportdesk/urgent_alert', $this->footer_code());
        $DB->delete_records('local_supportdesk_members', ['userid' => $staff->id]);
        $this->assertStringNotContainsString('local_supportdesk/urgent_alert', $this->footer_code());
        department_team::replace_members($category, [$staff->id]);
        assign_capability('local/supportdesk:specialist', CAP_PROHIBIT, $role, \context_system::instance()->id, true);
        $this->assertEquals(CAP_PROHIBIT, $DB->get_field('role_capabilities', 'permission', [
            'roleid' => $role, 'contextid' => \context_system::instance()->id,
            'capability' => 'local/supportdesk:specialist',
        ]));
        // Match a fresh native request after changing stored role permissions.
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertStringNotContainsString('local_supportdesk/urgent_alert', $this->footer_code());
        assign_capability('local/supportdesk:specialist', CAP_ALLOW, $role, \context_system::instance()->id, true);
        accesslib_clear_all_caches_for_unit_testing();
        set_config('enabled', 0, 'local_supportdesk');
        $this->assertStringNotContainsString('local_supportdesk/urgent_alert', $this->footer_code());
        set_config('enabled', 1, 'local_supportdesk');
        $this->setGuestUser();
        $this->assertStringNotContainsString('local_supportdesk/urgent_alert', $this->footer_code());
        $this->assertFalse($DB->record_exists('capabilities', ['name' => 'local/supportdesk:viewownoverviews']));
    }

    public function test_alert_service_filters_membership_and_requires_specialist(): void {
        global $DB;
        $this->resetAfterTest();
        [$staff, $role, $category, $ticket] = $this->fixture();
        $result = \local_supportdesk\external\check_urgent::execute(time() - 60);
        $this->assertSame('found', $result['status']);
        $this->assertSame((int)$ticket, $result['id']);
        $DB->delete_records('local_supportdesk_members', ['userid' => $staff->id]);
        $this->assertSame('none', \local_supportdesk\external\check_urgent::execute(time() - 60)['status']);
        department_team::replace_members($category, [$staff->id]);
        assign_capability('local/supportdesk:specialist', CAP_PROHIBIT, $role, \context_system::instance()->id, true);
        $this->assertEquals(CAP_PROHIBIT, $DB->get_field('role_capabilities', 'permission', [
            'roleid' => $role, 'contextid' => \context_system::instance()->id,
            'capability' => 'local/supportdesk:specialist',
        ]));
        // Match a fresh native request after changing stored role permissions.
        accesslib_clear_all_caches_for_unit_testing();
        $this->expectException(\required_capability_exception::class);
        \local_supportdesk\external\check_urgent::execute(time() - 60);
    }
}
