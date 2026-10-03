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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * External API for checking urgent tickets.
 *
 * @package     local_supportdesk
 * @copyright   2025 learn-ix support@learn-ix.com
 * Modified 2026-10-03 by 108design: Supportdesk consolidation.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_supportdesk\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;
use moodle_url;

/**
 * Class check_urgent to handle checking for urgent tickets via AJAX.
 */
class check_urgent extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'lastcheck' => new external_value(PARAM_INT, '', VALUE_DEFAULT, 0)
        ]);
    }

    /**
     * Executes the API call to check for urgent tickets.
     *
     * @param int $lastcheck Timestamp of the last check.
     * @return array Ticket information and status.
     */
    public static function execute($lastcheck) {
        global $DB, $USER;

        $params = self::validate_parameters(self::execute_parameters(), ['lastcheck' => $lastcheck]);
        $lastcheck = $params['lastcheck'];

        $context = \context_system::instance();
        self::validate_context($context);
        \local_supportdesk\local\access::require_enabled();
        if (!\local_supportdesk\local\access::can_manage()) {
            throw new \required_capability_exception($context, 'local/supportdesk:manageticket', 'nopermissions', '');
        }

        if (!\local_supportdesk\local\department_team::has_memberships((int)$USER->id)) {
            return ['status' => 'none', 'id' => 0, 'title' => '', 'user' => '', 'url' => ''];
        }
        if ($lastcheck <= 0) {
            $lastcheck = time() - 86400;
        }

        $sql = "SELECT t.id, t.title, u.firstname, u.lastname
                FROM {local_supportdesk_tickets} t
                JOIN {user} u ON u.id = t.userid
                JOIN {local_supportdesk_members} m ON m.categoryid = t.category_id
                WHERE (t.priority = 'urgent' OR t.priority = 'high')
                AND m.userid = ?
                AND t.status = 'open'
                AND t.created_at > ?
                ORDER BY t.created_at DESC";

        $tickets = $DB->get_records_sql($sql, [(int)$USER->id, $lastcheck], 0, 1);

        if ($tickets) {
            $ticket = reset($tickets);
            $url = new moodle_url('/local/supportdesk/view.php', ['id' => $ticket->id]);
            return [
                'status' => 'found',
                'id' => (int)$ticket->id,
                'title' => (string)$ticket->title,
                'user' => $ticket->firstname . ' ' . $ticket->lastname,
                'url' => $url->out(false)
            ];
        }

        return [
            'status' => 'none',
            'id' => 0,
            'title' => '',
            'user' => '',
            'url' => ''
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, ''),
            'id'     => new external_value(PARAM_INT, ''),
            'title'  => new external_value(PARAM_TEXT, ''),
            'user'   => new external_value(PARAM_TEXT, ''),
            'url'    => new external_value(PARAM_URL, '')
        ]);
    }
}
