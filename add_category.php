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
 * Page to add new ticket categories/departments.
 *
 * @package    local_supportdesk
 * @copyright  2026 learn-ix support@learn-ix.com
 * Modified 2026-10-03 by 108design: Supportdesk consolidation.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
defined('MOODLE_INTERNAL') || die();
global $CFG, $PAGE, $OUTPUT, $DB, $USER;
require_login();
\local_supportdesk\local\access::require_enabled();
require_capability('local/supportdesk:addcategory', context_system::instance());
$PAGE->set_url(new moodle_url('/local/supportdesk/add_category.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('add_new_department', 'local_supportdesk'));
$PAGE->set_heading(get_string('support_departments', 'local_supportdesk'));
\local_supportdesk\local\presentation::setup();
$customcss = \local_supportdesk\local\presentation::colours();
$alert = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_sesskey();
    $action = optional_param('action', 'add', PARAM_ALPHANUMEXT);
    if ($action === 'delete') {
        $id = required_param('id', PARAM_INT);
        if (!optional_param('confirm', false, PARAM_BOOL)) {
            echo $OUTPUT->header();
            $continue = new single_button(new moodle_url($PAGE->url, [
                'action' => 'delete', 'id' => $id, 'confirm' => 1, 'sesskey' => sesskey(),
            ]), get_string('delete', 'local_supportdesk'), 'post');
            echo $OUTPUT->confirm(get_string('delete_department_confirm', 'local_supportdesk'),
                $continue, $PAGE->url);
            echo $OUTPUT->footer();
            exit;
        }
        try {
            if ($DB->record_exists('local_supportdesk_tickets', ['category_id' => $id])) {
                throw new \moodle_exception('categoryinuse', 'local_supportdesk');
            }
            $transaction = $DB->start_delegated_transaction();
            $DB->delete_records('local_supportdesk_members', ['categoryid' => $id]);
            $DB->delete_records('local_supportdesk_categories', ['id' => $id]);
            $transaction->allow_commit();
            $alert = [
                'type' => 'success',
                'title' => get_string('success', 'local_supportdesk'),
                'message' => get_string('department_deleted', 'local_supportdesk'),
                'redirect' => $PAGE->url->out(false)
            ];
        } catch (Exception $e) {
            if (isset($transaction)) {
                try {$transaction->rollback($e);} catch (Exception $rolledback) {}
                unset($transaction);
            }
            $alert = [
                'type' => 'error',
                'title' => get_string('error', 'local_supportdesk'),
                'message' => get_string('deletion_failed', 'local_supportdesk'),
                'redirect' => ''
            ];
        }
    } elseif ($action === 'edit') {
        $id = required_param('id', PARAM_INT);
        $title = required_param('title', PARAM_TEXT);
        $description = optional_param('description', '', PARAM_TEXT);
        $color = \local_supportdesk\local\badges::validate_color(required_param('color', PARAM_RAW_TRIMMED));
        $members = \local_supportdesk\local\department_team::validate_members(optional_param_array('members', [], PARAM_INT));
        $categorydata = (object) [
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'color' => $color,
            'updated_at' => time(),
        ];
        try {
            $transaction = $DB->start_delegated_transaction();
            $DB->update_record('local_supportdesk_categories', $categorydata);
            \local_supportdesk\local\department_team::replace_members($id, $members);
            $transaction->allow_commit();
            $alert = [
                'type' => 'success',
                'title' => get_string('success', 'local_supportdesk'),
                'message' => get_string('department_updated', 'local_supportdesk'),
                'redirect' => $PAGE->url->out(false)
            ];
        } catch (Exception $e) {
            if (isset($transaction)) {
                try {$transaction->rollback($e);} catch (Exception $rolledback) {}
                unset($transaction);
            }
            $alert = [
                'type' => 'error',
                'title' => get_string('error', 'local_supportdesk'),
                'message' => get_string('update_failed', 'local_supportdesk'),
                'redirect' => ''
            ];
        }
    } else {
        $title = required_param('title', PARAM_TEXT);
        $description = optional_param('description', '', PARAM_TEXT);
        $color = \local_supportdesk\local\badges::validate_color(required_param('color', PARAM_RAW_TRIMMED));
        $ip = getremoteaddr();
        $members = \local_supportdesk\local\department_team::validate_members(optional_param_array('members', [], PARAM_INT));
        $categorydata = (object) [
            'title' => $title,
            'description' => $description,
            'color' => $color,
            'created_by' => $USER->id,
            'ip_address' => $ip,
            'created_at' => time(),
            'updated_at' => time(),
        ];
        try {
            $transaction = $DB->start_delegated_transaction();
            $categoryid = $DB->insert_record('local_supportdesk_categories', $categorydata);
            \local_supportdesk\local\department_team::replace_members($categoryid, $members);
            $transaction->allow_commit();
            $alert = [
                'type' => 'success',
                'title' => get_string('success', 'local_supportdesk'),
                'message' => get_string('department_created', 'local_supportdesk'),
                'redirect' => (new moodle_url('/local/supportdesk/index.php'))->out(false)
            ];
        } catch (Exception $e) {
            if (isset($transaction)) {
                try {$transaction->rollback($e);} catch (Exception $rolledback) {}
                unset($transaction);
            }
            $alert = [
                'type' => 'error',
                'title' => get_string('error', 'local_supportdesk'),
                'message' => get_string('creation_failed', 'local_supportdesk'),
                'redirect' => ''
            ];
        }
    }
}
if ($alert && $alert['type'] === 'success') {
    redirect($PAGE->url, $alert['message'], null, \core\output\notification::NOTIFY_SUCCESS);
}
$categories = $DB->get_records('local_supportdesk_categories');
$categorylist = [];
if ($categories) {
    $userids = [];
    foreach ($categories as $category) {
        if (!empty($category->created_by)) {
            $userids[$category->created_by] = $category->created_by;
        }
    }
    $users = [];
    if (!empty($userids)) {
        list($inorsql, $inparams) = $DB->get_in_or_equal($userids);
        $users = $DB->get_records_select('user', "id $inorsql", $inparams, '', 'id, firstname, lastname');
    }
    foreach ($categories as $category) {
        $creator = isset($users[$category->created_by]) ? clone $users[$category->created_by] : null;
        if ($creator) {
            $creator->firstnamephonetic = '';
            $creator->lastnamephonetic = '';
            $creator->middlename = '';
            $creator->alternatename = '';
        }
        $categorylist[] = [
            'id' => $category->id,
            'title' => $category->title,
            'description' => $category->description,
            'created_by' => $creator ? fullname($creator) : get_string('unknown_user', 'local_supportdesk'),
            'created_at' => \local_supportdesk\local\presentation::date($category->created_at),
            'team_names' => implode(', ', array_map('fullname', \local_supportdesk\local\department_team::members((int)$category->id))),
            'badge' => \local_supportdesk\local\badges::department($category->title, $category->color),
        ];
    }
}
$templatecontext = [
    'categories' => $categorylist,
    'action_url' => $PAGE->url->out(false),
    'index_url' => (new moodle_url('/local/supportdesk/index.php'))->out(false),
    'sesskey' => sesskey(),
    'has_alert' => $alert !== null,
    'alert' => $alert
];
$sidebars = [];
$editid = optional_param('editid', 0, PARAM_INT);
$templatecontext['editing'] = $editid > 0;
$templatecontext['edit'] = $editid ? $DB->get_record('local_supportdesk_categories', ['id' => $editid], '*', MUST_EXIST) : null;
$templatecontext['color'] = $templatecontext['edit']->color ?? '#64748b';
$selectedmembers = $editid ? \local_supportdesk\local\department_team::members($editid) : [];
$templatecontext['team_options'] = [];
foreach (\local_supportdesk\local\department_team::eligible_users() as $member) {
    $templatecontext['team_options'][] = ['id' => $member->id, 'name' => fullname($member), 'selected' => isset($selectedmembers[$member->id])];
}
$templatecontext['has_team_options'] = !empty($templatecontext['team_options']);
$templatecontext['alert_success'] = $alert && $alert['type'] === 'success';
\local_supportdesk\local\presentation::page('add_category', $templatecontext, $sidebars);
