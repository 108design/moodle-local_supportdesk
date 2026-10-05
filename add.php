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
 * Add ticket page (Custom UI).
 *
 * @package    local_supportdesk
 * @copyright  2026 learn-ix support@learn-ix.com
 * Modified 2026-10-03 by 108design: Supportdesk consolidation.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

defined('MOODLE_INTERNAL') || die();

global $DB, $USER, $PAGE, $OUTPUT, $CFG, $SITE;

\local_supportdesk\local\visitor_access::require_user(new moodle_url('/local/supportdesk/add.php'));

$context = context_system::instance();
require_capability('local/supportdesk:addticket', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/supportdesk/add.php'));
$PAGE->set_title(get_string('add_ticket', 'local_supportdesk'));
$PAGE->set_heading(get_string('add_ticket', 'local_supportdesk'));
$customcss = \local_supportdesk\local\presentation::colours();
\local_supportdesk\local\presentation::setup();
$PAGE->add_body_class('supportdesk-create-page');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_sesskey();

    $title = required_param('title', PARAM_TEXT);
    $categoryid = required_param('category_id', PARAM_INT);
    $description = required_param('description', PARAM_CLEANHTML);
    $priority = required_param('priority', PARAM_ALPHA);
    \local_supportdesk\local\access::validate_priority($priority);
    $DB->get_record('local_supportdesk_categories', ['id' => $categoryid], 'id', MUST_EXIST);
    if (trim($title) === '' || core_text::strlen($title) > 255 || trim(strip_tags($description)) === '') {
        throw new \invalid_parameter_exception('A subject and description are required');
    }

    \local_supportdesk\local\uploads::validate($_FILES['attachments'] ?? [], true);
    \local_supportdesk\local\uploads::validate_voice($_FILES['voice_note'] ?? []);
    $newticket = new stdClass();
    $newticket->title = $title;
    $newticket->description = $description;
    $newticket->category_id = $categoryid;
    $newticket->userid = $USER->id;
    $newticket->status = 'open';
    $newticket->priority = $priority;
    $newticket->created_at = time();
    $newticket->updated_at = time();
    $newticket->created_by = $USER->id;
    $newticket->ip_address = getremoteaddr();

    $ticketid = $DB->insert_record('local_supportdesk_tickets', $newticket);

    if ($ticketid) {
        if (!empty($_FILES['attachments'])) {
            $fs = get_file_storage();
            $files = $_FILES['attachments'];

            foreach ($files['name'] as $key => $name) {
                if ($files['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                    $filerecord = [
                        'contextid' => $context->id,
                        'component' => 'local_supportdesk',
                        'filearea'  => 'attachment',
                        'itemid'    => $ticketid,
                        'filepath'  => '/',
                        'filename'  => clean_param($name, PARAM_FILE),
                        'userid'    => $USER->id,
                        'timecreated' => time(),
                        'timemodified' => time(),
                    ];

                    try {
                        $fs->create_file_from_pathname($filerecord, $files['tmp_name'][$key]);
                    } catch (Exception $e) {
                        debugging($e->getMessage());
                    }
                }
            }
        }
        if (!empty($_FILES['voice_note']) && $_FILES['voice_note']['error'] === UPLOAD_ERR_OK) {
            $filerecord = [
                'contextid'    => $context->id,
                'component'    => 'local_supportdesk',
                'filearea'     => 'attachment',
                'itemid'       => $ticketid,
                'filepath'     => '/',
                'filename'     => clean_param($_FILES['voice_note']['name'], PARAM_FILE),
                'userid'       => $USER->id,
                'timecreated'  => time(),
                'timemodified' => time(),
            ];

            try {
                $fs = get_file_storage();
                $fs->create_file_from_pathname($filerecord, $_FILES['voice_note']['tmp_name']);
            } catch (Exception $e) {
                debugging('Error saving voice note: ' . $e->getMessage());
            }
        }

        $ticketurl = new moodle_url('/local/supportdesk/view.php', ['id' => $ticketid]);
        $categorytitle = $DB->get_field('local_supportdesk_categories', 'title', ['id' => $categoryid]);

        $a = new stdClass();
        $a->firstname = $USER->firstname;
        $a->id        = $ticketid;
        $a->title     = format_string($title);
        $a->category  = $categorytitle;
        $a->status    = get_string('status_open', 'local_supportdesk');
        $a->date      = userdate(time(), get_string('strftimedate', 'langconfig'));
        $a->url       = $ticketurl->out(false);
        $a->site      = $SITE->fullname;

        $eventdata = new \core\message\message();
        $eventdata->component         = 'local_supportdesk';
        $eventdata->name              = 'ticket_confirmation';
        $eventdata->userfrom          = \core_user::get_noreply_user();
        $eventdata->userto            = $USER;
        $eventdata->subject           = get_string('email_confirm_subject', 'local_supportdesk', $a);
        $eventdata->fullmessage       = get_string('email_confirm_body_plain', 'local_supportdesk', $a);
        $eventdata->fullmessageformat = FORMAT_HTML;
        $eventdata->fullmessagehtml   = get_string('email_confirm_body', 'local_supportdesk', $a);
        $eventdata->notification      = 1;

        try {
            message_send($eventdata);
        } catch (Exception $e) {
            debugging($e->getMessage());
        }

        $newticket->id = $ticketid;
        \local_supportdesk\local\notifications::send($newticket, (int)$USER->id, 'new');

        redirect(
            new moodle_url('/local/supportdesk/index.php'),
            get_string('success', 'local_supportdesk'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}

$categories = $DB->get_records('local_supportdesk_categories');
$categorieslist = [];
if ($categories) {
    foreach ($categories as $cat) {
        $categorieslist[] = ['id' => $cat->id, 'title' => $cat->title];
    }
}
$templatedata = [
    'title' => get_string('add_ticket', 'local_supportdesk'),
    'sesskey' => sesskey(),
    'categories' => $categorieslist,
    'priorities' => \local_supportdesk\local\presentation::priority_choices(),
    'action_url' => $PAGE->url->out(false),
    'return_url' => (new moodle_url('/local/supportdesk/index.php'))->out(false),
];
$sidebars = [];
$templatedata += ['voice_field' => 'voice_note', 'file_field' => 'attachments[]', 'file_id' => 'file_attachments'];
\local_supportdesk\local\presentation::page('add_page', $templatedata, $sidebars);
