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
 * File documentation for tests/uninstall_test.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk;

use local_supportdesk\local\installer;

defined('MOODLE_INTERNAL') || die();

/** Uninstall must not remove roles with foreign ownership or added permissions. */
final class uninstall_test extends \advanced_testcase {
    public function test_uninstall_removes_owned_plugin_only_role(): void {
        global $CFG, $DB;
        $this->resetAfterTest();
        require_once($CFG->dirroot . '/local/supportdesk/db/uninstall.php');
        $id = installer::install_role();
        $this->assertTrue(xmldb_local_supportdesk_uninstall());
        $this->assertFalse($DB->record_exists('role', ['id' => $id]));
        $this->assertTrue(xmldb_local_supportdesk_uninstall());
    }

    public function test_uninstall_preserves_owned_role_with_foreign_permissions(): void {
        global $CFG, $DB;
        $this->resetAfterTest();
        require_once($CFG->dirroot . '/local/supportdesk/db/uninstall.php');
        $id = installer::install_role();
        assign_capability('moodle/site:config', CAP_ALLOW, $id, \context_system::instance()->id);
        $this->assertTrue(xmldb_local_supportdesk_uninstall());
        $this->assertTrue($DB->record_exists('role', ['id' => $id]));
        $this->assertTrue($DB->record_exists('role_capabilities', ['roleid' => $id, 'capability' => 'moodle/site:config']));
    }

    public function test_uninstall_preserves_unowned_role_with_same_shortname(): void {
        global $CFG, $DB;
        $this->resetAfterTest();
        require_once($CFG->dirroot . '/local/supportdesk/db/uninstall.php');
        $owned = $DB->get_record('role', ['shortname' => 'supportdeskagent']);
        if ($owned) {delete_role($owned->id);}
        unset_config('ownedroleid', 'local_supportdesk');
        $foreignid = create_role('Unrelated support', 'supportdeskagent', 'Foreign administrator-owned role');
        $this->assertTrue(xmldb_local_supportdesk_uninstall());
        $this->assertTrue($DB->record_exists('role', ['id' => $foreignid]));
    }
}
