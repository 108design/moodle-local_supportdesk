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
 * File documentation for db/install.php.
 *
 * @package   local_supportdesk
 * @copyright 2025 learn-ix support@learn-ix.com
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later
// Derived from learn-ix Academic Ticket System, copyright 2026 learn-ix.
// Modified 2026-10-03 by 108design.
defined('MOODLE_INTERNAL') || die();
function xmldb_local_supportdesk_install() {
    set_config('visitoraccess', 0, 'local_supportdesk');
    set_config('visitormode', 'login', 'local_supportdesk');
    global $DB, $USER;
    // Moodle registers plugin capabilities after the install callback by default.
    // Register them now so the new role can receive its explicit permissions.
    update_capabilities('local_supportdesk');
    \local_supportdesk\local\installer::install_role();
    $now = time();
    $DB->insert_record('local_supportdesk_categories', (object)[
        'title' => 'Support', 'description' => '', 'created_by' => (int)$USER->id,
        'created_at' => $now, 'updated_at' => $now,
    ]);
    return true;
}
