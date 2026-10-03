<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library functions for local_supportdesk.
 *
 * @package    local_supportdesk
 * @copyright  2026 learn-ix support@learn-ix.com
 * Modified 2026-10-03 by 108design: Supportdesk consolidation.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Extends the navigation to include the ticket system.
 * Adds a native node; the primary navigation hook exposes it in Boost themes.
 *
 * @param global_navigation $navigation The navigation object
 * @return void
 */
function local_supportdesk_extend_navigation(global_navigation $navigation) {
    global $DB, $USER;

    if (
        !get_config('local_supportdesk', 'enabled') ||
        !get_config('local_supportdesk', 'add_to_navbar')
    ) {
        return;
    }

    if (!isloggedin() || isguestuser()) {
        return;
    }

    $url = new moodle_url('/local/supportdesk/index.php');
    $title = get_string('pluginname', 'local_supportdesk');

    $pendingcount = 0;
    $context = \context_system::instance();
    $canmanage = has_capability('local/supportdesk:manageticket', $context);

    try {
        if ($canmanage) {
            $pendingcount = $DB->count_records('local_supportdesk_tickets', ['status' => 'student reply']);
        } else {
            $pendingcount = $DB->count_records('local_supportdesk_tickets', [
                'userid' => $USER->id,
                'status' => 'admin reply',
            ]);
        }
    } catch (\Exception $e) {
        $pendingcount = 0;
    }

    if ($pendingcount > 0) {
        $title .= " (" . $pendingcount . ")";
    }

    $node = $navigation->add($title, $url, navigation_node::TYPE_CUSTOM, null,
        'supportdesk_nav', new pix_icon('i/message', ''));
    $node->showinflatnavigation = true;
}

/**
 * Serves ticket attachments and reply attachments.
 *
 * @param \stdClass $course The course object
 * @param \stdClass $cm The course module object
 * @param \context $context The context object
 * @param string $filearea The file area
 * @param array $args Extra arguments
 * @param bool $forcedownload Whether or not to force download
 * @param array $options Additional options
 * @return void
 */
function local_supportdesk_pluginfile($course, $cm, $context, $filearea, $args,
                                                 $forcedownload, array $options = []) {
    global $DB;

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        send_file_not_found();
    }

    require_login();
    \local_supportdesk\local\access::require_enabled();

    $validareas = ['attachment', 'reply_attachment'];
    if (!in_array($filearea, $validareas)) {
        send_file_not_found();
    }

    $itemid = (int)array_shift($args);
    if (!\local_supportdesk\local\access::can_download($filearea, $itemid)) {
        send_file_not_found();
    }
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'local_supportdesk', $filearea, $itemid, $filepath, $filename);

    if (!$file) {
        send_file_not_found();
    }

    // Active content such as HTML/SVG must not execute in the Moodle origin.
    $inline = preg_match('~^(image/(png|jpeg|gif|webp)|audio/|video/|application/pdf$)~', $file->get_mimetype());
    send_stored_file($file, 0, 0, $forcedownload || !$inline, $options);
}

/**
 * Returns CSS classes based on ticket status.
 *
 * @param string $status The ticket status
 * @return string The CSS classes for the status badge
 */
function local_supportdesk_get_class($status) {
    return 'supportdesk-status';
}

/**
 * Logs a ticket action to the database.
 *
 * @param int $tid The ticket ID
 * @param int $uid The user ID performing the action
 * @param string $act The action name
 * @param mixed $old The old value (optional)
 * @param mixed $new The new value (optional)
 * @return int|bool The new record ID or false on failure
 */
function local_supportdesk_log_action($tid, $uid, $act, $old = null, $new = null) {
    global $DB;
    $log = (object)[
        'ticketid' => $tid,
        'userid' => $uid,
        'actionname' => $act,
        'oldvalue' => $old,
        'newvalue' => $new,
        'timemodified' => time(),
    ];
    return $DB->insert_record('local_supportdesk_logs', $log);
}
