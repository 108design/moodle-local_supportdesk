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
 * View ticket page.
 *
 * @package    local_supportdesk
 * @copyright  2026 learn-ix support@learn-ix.com
 * Modified 2026-10-03 by 108design: Supportdesk consolidation.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/editorlib.php');
require_once(__DIR__ . '/lib.php');

\local_supportdesk\local\visitor_access::require_user(new moodle_url('/local/supportdesk/view.php', ['id' => required_param('id', PARAM_INT)]));
global $USER, $DB, $PAGE, $OUTPUT, $CFG;

/**
 * Get status string.
 *
 * @param string $val
 * @return string
 */
function get_status_str($val) {
    $val = strtolower(trim($val));
    $map = [
        'in progress'   => 'in_progress',
        'inprogress'    => 'in_progress',
        'admin reply'   => 'adminreply',
        'adminreply'    => 'adminreply',
        'student reply' => 'studentreply',
        'studentreply'  => 'studentreply'
    ];
    $cleanval = isset($map[$val]) ? $map[$val] : str_replace(' ', '_', $val);
    $key = 'status_' . $cleanval;
    if (get_string_manager()->string_exists($key, 'local_supportdesk')) {
        return get_string($key, 'local_supportdesk');
    }
    return ucfirst($val);
}

$id = required_param('id', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/supportdesk/view.php', ['id' => $id]);
$PAGE->set_title(get_string('viewticket', 'local_supportdesk'));
$PAGE->set_heading(get_string('pluginname', 'local_supportdesk'));
\local_supportdesk\local\presentation::setup();

$customcss = \local_supportdesk\local\presentation::colours();

$sql = "SELECT t.*, c.title as category_name, c.color as category_color
        FROM {local_supportdesk_tickets} t
        LEFT JOIN {local_supportdesk_categories} c ON t.category_id = c.id
        WHERE t.id = ?";
$ticketrecord = $DB->get_record_sql($sql, [$id]);

if (!$ticketrecord) {
    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('local_supportdesk/error_notfound', [
        'ticketid' => $id,
        'home_url' => new moodle_url('/local/supportdesk/index.php'),
    ]);
    echo $OUTPUT->footer();
    die();
}

$ismanager = has_capability('local/supportdesk:manageticket', $context);
$isspecialist = has_capability('local/supportdesk:specialist', $context);
$currentuserid = $USER->id;
$ticketuserid = $ticketrecord->userid;
$assignedto = $ticketrecord->assigned_to;
$isassignedtome = (!empty($assignedto) && $assignedto == $currentuserid);
$isowner = ($ticketuserid == $currentuserid);

$isadminview = ($ismanager || $isassignedtome);
$showadminpanel = $isadminview;

if (!\local_supportdesk\local\access::can_view($ticketrecord)) {
    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('local_supportdesk/error_denied', [
        'home_url' => new moodle_url('/local/supportdesk/index.php'),
    ]);
    echo $OUTPUT->footer();
    die();
}

$canreply = ($isadminview || $isowner);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_sesskey();
    if (isset($_POST['reply_message'])) {
        if ($canreply) {
            $message = required_param('reply_message', PARAM_CLEANHTML);
            if (trim(strip_tags($message)) === '') {
                throw new \invalid_parameter_exception('A reply is required');
            }
            \local_supportdesk\local\uploads::validate($_FILES['reply_files'] ?? [], true);
            \local_supportdesk\local\uploads::validate($_FILES['reply_voice'] ?? []);
            $reply = (object)[
                'ticket_id' => $id,
                'userid' => $USER->id,
                'message' => $message,
                'created_at' => time(),
            ];
            $replyid = $DB->insert_record('local_supportdesk_replies', $reply);
            if ($replyid) {
                $fs = get_file_storage();
                if (!empty($_FILES['reply_files']['name'][0])) {
                    foreach ($_FILES['reply_files']['name'] as $key => $name) {
                        if ($_FILES['reply_files']['error'][$key] === UPLOAD_ERR_OK) {
                            $filerecord = [
                                'contextid' => $context->id,
                                'component' => 'local_supportdesk',
                                'filearea' => 'reply_attachment',
                                'itemid' => $replyid,
                                'filepath' => '/',
                                'filename' => clean_param($name, PARAM_FILE),
                                'userid' => $USER->id
                            ];
                            $fs->create_file_from_pathname($filerecord, $_FILES['reply_files']['tmp_name'][$key]);
                        }
                    }
                }
                if (!empty($_FILES['reply_voice']['name']) && $_FILES['reply_voice']['error'] === UPLOAD_ERR_OK) {
                    $filerecord = [
                        'contextid' => $context->id,
                        'component' => 'local_supportdesk',
                        'filearea' => 'reply_attachment',
                        'itemid' => $replyid,
                        'filepath' => '/',
                        'filename' => clean_param($_FILES['reply_voice']['name'], PARAM_FILE),
                        'userid' => $USER->id
                    ];
                    try {
                        $fs->create_file_from_pathname($filerecord, $_FILES['reply_voice']['tmp_name']);
                    } catch (Exception $e) {
                        debugging($e->getMessage());
                    }
                }
            }
            $oldstatus = $ticketrecord->status;
            $newstatus = $isadminview ? 'admin reply' : 'student reply';
            $DB->set_field('local_supportdesk_tickets', 'status', $newstatus, ['id' => $id]);
            $a = new stdClass();
            $a->user = fullname($USER);
            $a->old = get_status_str($oldstatus);
            $a->new = get_status_str($newstatus);
            $logmessage = get_string('log_status_changed_from_to', 'local_supportdesk', $a);
            local_supportdesk_log_action($id, $USER->id, $logmessage, $oldstatus, $newstatus);
            \local_supportdesk\local\notifications::send($ticketrecord, (int)$USER->id, 'reply', !$isowner);
        }
        redirect($PAGE->url);
    }
    if ($action === 'update_status' && $showadminpanel) {
        $status = required_param('status', PARAM_TEXT);
        \local_supportdesk\local\access::validate_status($status);
        $oldstatus = $ticketrecord->status;
        $DB->set_field('local_supportdesk_tickets', 'status', $status, ['id' => $id]);
        $a = new stdClass();
        $a->user = fullname($USER);
        $a->old = get_status_str($oldstatus);
        $a->new = get_status_str($status);
        $logmessage = get_string('log_status_changed_from_to', 'local_supportdesk', $a);
        local_supportdesk_log_action($id, $USER->id, $logmessage, $oldstatus, $status);
        redirect($PAGE->url);
    }
    if ($action === 'assign_user' && $showadminpanel) {
        $assignedtoparam = required_param('assigned_to', PARAM_INT);
        $assignees = \local_supportdesk\local\access::assignable_users();
        if ($assignedtoparam < 0 || ($assignedtoparam > 0 && !isset($assignees[$assignedtoparam]))) {
            throw new \invalid_parameter_exception('Invalid support assignee');
        }
        if ($assignedtoparam > 0) {
            $assigneduserrec = $DB->get_record('user', ['id' => $assignedtoparam]);
            $logmessage = get_string('log_assigned', 'local_supportdesk', fullname($assigneduserrec));
        } else {
            $unassignedstr = get_string('unassigned', 'local_supportdesk');
            $logmessage = get_string('log_assigned', 'local_supportdesk', $unassignedstr);
        }
        $DB->set_field('local_supportdesk_tickets', 'assigned_to', $assignedtoparam, ['id' => $id]);
        local_supportdesk_log_action($id, $USER->id, $logmessage, '', $assignedtoparam);
        redirect($PAGE->url);
    }
    if ($action === 'update_category' && $showadminpanel) {
        $catid = required_param('categoryid', PARAM_INT);
        $category = $DB->get_record('local_supportdesk_categories', ['id' => $catid], 'id, title', MUST_EXIST);
        $DB->set_field('local_supportdesk_tickets', 'category_id', $catid, ['id' => $id]);
        $logmessage = get_string('log_category_changed', 'local_supportdesk', $category->title);
        local_supportdesk_log_action($id, $USER->id, $logmessage, $ticketrecord->category_id, $catid);
        $ticketrecord->category_id = $catid;
        \local_supportdesk\local\notifications::send($ticketrecord, (int)$USER->id, 'moved');
        redirect($PAGE->url);
    }
    if ($action === 'add_internal_comment' && $showadminpanel) {
        $commenttext = required_param('internal_note', PARAM_TEXT);
        $note = (object)[
            'ticketid' => $id,
            'userid' => $USER->id,
            'content' => $commenttext,
            'created_at' => time()
        ];
        $DB->insert_record('local_supportdesk_comments', $note);
        $logmessage = get_string('log_internal_note_added', 'local_supportdesk');
        local_supportdesk_log_action($id, $USER->id, $logmessage, '', '');
        redirect($PAGE->url);
    }
    if ($action === 'submit_feedback' && !$isadminview && $ticketrecord->userid == $USER->id) {
        $rating = required_param('rating', PARAM_INT);
        if ($ticketrecord->status !== 'closed' || $rating < 1 || $rating > 5) {
            throw new \invalid_parameter_exception('Invalid ticket feedback');
        }
        $comment = optional_param('feedback_comment', '', PARAM_TEXT);
        if (!$DB->record_exists('local_supportdesk_feedback', ['ticketid' => $id])) {
            $feedback = (object)[
                'ticketid' => $id,
                'userid' => $USER->id,
                'rating' => $rating,
                'comment' => $comment,
                'created_at' => time()
            ];
            $DB->insert_record('local_supportdesk_feedback', $feedback);
            $logmessage = get_string('log_feedback_submitted', 'local_supportdesk', $rating);
            local_supportdesk_log_action($id, $USER->id, $logmessage, '', $rating . ' Stars');
        }
        redirect($PAGE->url);
    }
}

$fs = get_file_storage();
$ticketfiles = $fs->get_area_files(
    $context->id,
    'local_supportdesk',
    'attachment',
    $id,
    "filename",
    false
);

$ticketattachments = [];
foreach ($ticketfiles as $f) {
    $ticketattachments[] = \local_supportdesk\local\attachments::export($f);
}
$PAGE->requires->css(new moodle_url('/local/supportdesk/thirdparty/photoswipe/photoswipe.css'));

$repliesrecords = $DB->get_records(
    'local_supportdesk_replies',
    ['ticket_id' => $id],
    'created_at ASC'
);

$userids = [];
foreach ($repliesrecords as $r) {
    $userids[$r->userid] = $r->userid;
}
$users = [];
if (!empty($userids)) {
    list($inorsql, $inparams) = $DB->get_in_or_equal($userids);
    $users = $DB->get_records_select('user', "id $inorsql", $inparams);
}

$repliesdata = [];
foreach ($repliesrecords as $r) {
    $ruser = isset($users[$r->userid]) ? clone $users[$r->userid] : null;
    $ismanagerreply = false;
    if ($ruser) {
        $ruser->firstnamephonetic = '';
        $ruser->lastnamephonetic = '';
        $ruser->middlename = '';
        $ruser->alternatename = '';

        $canmngtkt = has_capability('local/supportdesk:manageticket', $context, $r->userid);
        $canspectkt = has_capability('local/supportdesk:specialist', $context, $r->userid);
        $isasgnd = !empty($ticketrecord->assigned_to) && $r->userid == $ticketrecord->assigned_to;

        if ($canmngtkt || $canspectkt || $isasgnd) {
            $ismanagerreply = true;
        }
    }

    $rfiles = $fs->get_area_files(
        $context->id,
        'local_supportdesk',
        'reply_attachment',
        $r->id,
        "filename",
        false
    );

    $rattachments = [];
    foreach ($rfiles as $rf) {
        $rattachments[] = \local_supportdesk\local\attachments::export($rf);
    }

    $repliesdata[] = [
        'id' => $r->id,
        'user' => $ruser ? fullname($ruser) : get_string('unknown_user', 'local_supportdesk'),
        'message' => format_text($r->message, FORMAT_HTML),
        'time' => \local_supportdesk\local\presentation::date($r->created_at),
        'is_manager_reply' => $ismanagerreply,
        'has_reply_attachments' => !empty($rattachments),
        'reply_attachments' => $rattachments,
    ];
}

$ticketlog = [];
$assignableusers = [];
$latesttickets = [];
$internalnotes = [];
if ($showadminpanel) {
    if ($DB->get_manager()->table_exists('local_supportdesk_logs')) {
        $logs = $DB->get_records(
            'local_supportdesk_logs',
            ['ticketid' => $id],
            'timemodified DESC'
        );
        $loguserids = [];
        foreach ($logs as $l) { $loguserids[$l->userid] = $l->userid; }
        $logusers = [];
        if (!empty($loguserids)) {
            list($inorsql, $inparams) = $DB->get_in_or_equal($loguserids);
            $logusers = $DB->get_records_select(
                'user',
                "id $inorsql",
                $inparams,
                '',
                'id, firstname, lastname'
            );
        }
        foreach ($logs as $l) {
            $luser = isset($logusers[$l->userid]) ? clone $logusers[$l->userid] : null;
            if ($luser) {
                $luser->firstnamephonetic = '';
                $luser->lastnamephonetic = '';
                $luser->middlename = '';
                $luser->alternatename = '';
            }
            $ticketlog[] = [
                'message' => ($luser ? fullname($luser) : 'System') . ": " . $l->actionname,
                'time' => \local_supportdesk\local\presentation::date($l->timemodified),
            ];
        }
    }

    $assignees = \local_supportdesk\local\access::assignable_users();

    foreach ($assignees as $assignee) {
        $assignableusers[] = [
            'id' => $assignee->id,
            'fullname' => fullname($assignee),
            'email' => $assignee->email,
            'username' => $assignee->username,
            'is_selected' => ($assignee->id == $ticketrecord->assigned_to),
        ];
    }

    $latestselsql = "SELECT * FROM {local_supportdesk_tickets}
                     WHERE userid = ? AND id != ?
                     ORDER BY created_at DESC";
    $recs = $DB->get_records_sql($latestselsql, [$ticketrecord->userid, $id], 0, 5);
    foreach ($recs as $rec) {
        $latesttickets[] = [
            'id' => $rec->id,
            'title' => format_string($rec->title),
            'status' => get_status_str($rec->status),
            'status_badge' => \local_supportdesk\local\badges::status($rec->status),
            'status_class' => local_supportdesk_get_class($rec->status),
        ];
    }

    if ($DB->get_manager()->table_exists('local_supportdesk_comments')) {
        $notesrecords = $DB->get_records(
            'local_supportdesk_comments',
            ['ticketid' => $id],
            'created_at DESC'
        );
        $noteuserids = [];
        foreach ($notesrecords as $note) { $noteuserids[$note->userid] = $note->userid; }
        $noteusers = [];
        if (!empty($noteuserids)) {
            list($inorsql, $inparams) = $DB->get_in_or_equal($noteuserids);
            $noteusers = $DB->get_records_select(
                'user',
                "id $inorsql",
                $inparams,
                '',
                'id, firstname, lastname'
            );
        }
        foreach ($notesrecords as $note) {
            $noteuser = isset($noteusers[$note->userid]) ? clone $noteusers[$note->userid] : null;
            if ($noteuser) {
                $noteuser->firstnamephonetic = '';
                $noteuser->lastnamephonetic = '';
                $noteuser->middlename = '';
                $noteuser->alternatename = '';
            }
            $internalnotes[] = [
                'user_name' => $noteuser ? fullname($noteuser) : 'Unknown',
                'content' => format_text($note->content, FORMAT_HTML),
                'time' => \local_supportdesk\local\presentation::date($note->created_at),
                'is_me' => ($note->userid == $USER->id),
            ];
        }
    }
}

$categories = $DB->get_records('local_supportdesk_categories');
$catlist = [];
foreach ($categories as $cat) {
    $catlist[] = ['id' => $cat->id, 'title' => $cat->title, 'is_selected' => ($cat->id == $ticketrecord->category_id)];
}

$feedbackrecord = $DB->get_record('local_supportdesk_feedback', ['ticketid' => $id]);
$isticketclosed = ($ticketrecord->status === 'closed');
$canrate = ($isticketclosed && !$isadminview && !$feedbackrecord);
$ticketowner = $DB->get_record('user', ['id' => $ticketrecord->userid]);
if (!$ticketowner) {
    $ticketowner = (object)['id' => 0, 'firstname' => get_string('deleteduser', 'local_supportdesk'), 'lastname' => '', 'email' => '', 'picture' => 0, 'imagealt' => '', 'firstnamephonetic' => '', 'lastnamephonetic' => '', 'middlename' => '', 'alternatename' => ''];
}

$assigneduser = null;
if ($ticketrecord->assigned_to) {
    $assigneduser = $DB->get_record('user', ['id' => $ticketrecord->assigned_to]);
}

$priority = $ticketrecord->priority;

$feedbackdata = null;
if ($feedbackrecord) {
    $feedbackdata = [
        'rating' => $feedbackrecord->rating,
        'comment' => $feedbackrecord->comment,
        'stars' => array_fill(0, $feedbackrecord->rating, true)
    ];
}

$creatorprofileurl = new moodle_url('/user/profile.php', ['id' => $ticketowner->id]);
$homeurl = new moodle_url('/local/supportdesk/index.php');

$templatedata = [
    'ticketid' => $id,
    'currentuserid' => $USER->id,
    'ticket' => [
        'id' => $id,
        'title' => format_string($ticketrecord->title),
        'description' => format_text($ticketrecord->description, FORMAT_HTML),
        'status' => $ticketrecord->status,
        'translated_status' => get_status_str($ticketrecord->status),
        'status_class' => local_supportdesk_get_class($ticketrecord->status),
        'category' => $ticketrecord->category_name ?: get_string('general', 'local_supportdesk'),
        'category_id' => $ticketrecord->category_id,
        'priority' => $priority,
        'creator_profile_url' => $creatorprofileurl->out(false),
        'priority_label' => get_string('priority_' . $priority, 'local_supportdesk'),
        'created_by' => fullname($ticketowner),
        'created_at' => \local_supportdesk\local\presentation::date($ticketrecord->created_at),
        'status_badge' => \local_supportdesk\local\badges::status($ticketrecord->status),
        'priority_badge' => \local_supportdesk\local\badges::priority($priority),
        'category_badge' => \local_supportdesk\local\badges::department($ticketrecord->category_name ?: get_string('general', 'local_supportdesk'), $ticketrecord->category_color ?? '#64748b'),
        'status_closed' => ($isticketclosed || !$canreply),
        'has_attachments' => !empty($ticketattachments),
        'attachments' => $ticketattachments,
        'priority_urgent' => ($priority === 'urgent'),
        'priority_high'   => ($priority === 'high'),
        'priority_medium' => ($priority === 'medium'),
        'priority_low'    => ($priority === 'low'),
    ],
    'can_rate' => $canrate,
    'has_feedback' => (bool)$feedbackrecord,
    'feedback_data' => $feedbackdata,
    'rating_options' => [['val' => 5], ['val' => 4], ['val' => 3], ['val' => 2], ['val' => 1]],
    'replies' => $repliesdata,
    'has_manage_capability' => $showadminpanel,
    'categories' => $catlist,
    'assignable_users' => $assignableusers,
    'assigned_user_name' => $assigneduser ? fullname($assigneduser) : get_string('unassigned', 'local_supportdesk'),
    'assigned_user_id' => $ticketrecord->assigned_to ?: 0,
    'is_assigned' => (bool)$ticketrecord->assigned_to,
    'latest_tickets' => $latesttickets,
    'ticket_log' => $ticketlog,
    'has_ticket_log' => !empty($ticketlog),
    'sesskey' => sesskey(),
    'internal_notes' => $internalnotes,
    'has_internal_notes' => !empty($internalnotes),
    'home_url' => $homeurl,
];

$templatedata['statuses'] = [];
foreach (['open', 'in progress', 'admin reply', 'student reply', 'closed'] as $statusvalue) {
    $templatedata['statuses'][] = ['value' => $statusvalue, 'label' => get_status_str($statusvalue), 'selected' => $statusvalue === $ticketrecord->status];
}
$templatedata += ['voice_field' => 'reply_voice', 'file_field' => 'reply_files[]', 'file_id' => 'reply_file_input'];
$sidebars = ['ticket_information' => 'ticket_information'];
if ($showadminpanel) {
    $sidebars += ['management' => 'ticket_management', 'notes' => 'internal_notes_heading', 'related' => 'recent_tickets_heading'];
    if ($ticketlog) { $sidebars['history'] = 'ticket_log'; }
}
\local_supportdesk\local\presentation::page('view', $templatedata, $sidebars);
