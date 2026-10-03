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
 * Index page for the Ticket System with Pagination.
 *
 * @package    local_supportdesk
 * @copyright  2026 learn-ix support@learn-ix.com
 * Modified 2026-10-03 by 108design: Supportdesk consolidation.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
defined('MOODLE_INTERNAL') || die();
global $DB, $USER, $PAGE, $OUTPUT;
\local_supportdesk\local\visitor_access::require_user(new moodle_url('/local/supportdesk/index.php'));
$context = context_system::instance();
$PAGE->set_context($context);
$url = new moodle_url('/local/supportdesk/index.php');
$PAGE->set_url($url);
$PAGE->set_title(get_string('pluginname', 'local_supportdesk'));
$PAGE->set_heading(get_string('pluginname', 'local_supportdesk'));
\local_supportdesk\local\presentation::setup();
$customcss = \local_supportdesk\local\presentation::colours();
$page = max(0, optional_param('page', 0, PARAM_INT));
$perpage = 20;
$offset = $page * $perpage;
$ismanager = has_capability('local/supportdesk:manageticket', $context);
$isspecialist = has_capability('local/supportdesk:specialist', $context);
$canmanage = \local_supportdesk\local\access::can_manage();
$canaddticket = has_capability('local/supportdesk:addticket', $context);
$canaddcategory = has_capability('local/supportdesk:addcategory', $context);
$userselects1 = "u.firstname, u.lastname, u.firstnamephonetic";
$userselects2 = "u.lastnamephonetic, u.middlename, u.alternatename";

if ($canmanage) {
    $totalcount = $DB->count_records('local_supportdesk_tickets');
    $sql = "SELECT t.*, c.title AS category_title, c.color AS category_color, $userselects1, $userselects2
            FROM {local_supportdesk_tickets} t
            LEFT JOIN {local_supportdesk_categories} c ON t.category_id = c.id
            LEFT JOIN {user} u ON t.userid = u.id
            ORDER BY t.created_at DESC";
    $tickets = $DB->get_records_sql($sql, [], $offset, $perpage);
} else {
    $totalcount = $DB->count_records_select(
        'local_supportdesk_tickets',
        'userid = ? OR assigned_to = ?',
        [$USER->id, $USER->id]
    );
    $sql = "SELECT t.*, c.title AS category_title, c.color AS category_color, $userselects1, $userselects2
            FROM {local_supportdesk_tickets} t
            LEFT JOIN {local_supportdesk_categories} c ON t.category_id = c.id
            LEFT JOIN {user} u ON t.userid = u.id
            WHERE t.userid = ? OR t.assigned_to = ?
            ORDER BY t.created_at DESC";
    $tickets = $DB->get_records_sql($sql, [$USER->id, $USER->id], $offset, $perpage);
}

$hasmyassignment = false;
$assignedusersids = [];
foreach ($tickets as $ticket) {
    if (!empty($ticket->assigned_to)) {
        $assignedusersids[$ticket->assigned_to] = $ticket->assigned_to;
    }
    if ($ticket->assigned_to == $USER->id) {
        $hasmyassignment = true;
    }
}

$assignedusers = [];
if (!empty($assignedusersids)) {
    list($inorsql, $inparams) = $DB->get_in_or_equal($assignedusersids);
    $userfields = 'id, firstname, lastname, firstnamephonetic, lastnamephonetic, middlename, alternatename';
    $assignedusers = $DB->get_records_select('user', "id $inorsql", $inparams, '', $userfields);
}

[$visibilitysql, $visibilityparams] = $canmanage ? ['1=1', []] : ['(userid = :owner OR assigned_to = :assignee)', ['owner' => $USER->id, 'assignee' => $USER->id]];
$totalclosedglobal = $DB->count_records_select('local_supportdesk_tickets', $visibilitysql . ' AND status = :closed', $visibilityparams + ['closed' => 'closed']);
$totalopenglobal = $DB->count_records_select('local_supportdesk_tickets', $visibilitysql . ' AND status <> :closed', $visibilityparams + ['closed' => 'closed']);
$ticketsdata = [];

foreach ($tickets as $ticket) {
    $isactionneeded = $ticket->status === 'student reply' && ($canmanage || $ticket->assigned_to == $USER->id);

    $creatorname = fullname($ticket);
    $assignedname = '-';
    if (!empty($ticket->assigned_to) && isset($assignedusers[$ticket->assigned_to])) {
        $assignedname = fullname($assignedusers[$ticket->assigned_to]);
    }

    $statusstr = str_replace(' ', '_', $ticket->status);
    $statustranslated = get_string('status_' . $statusstr, 'local_supportdesk');
    $viewurl = new moodle_url('/local/supportdesk/view.php', ['id' => $ticket->id]);

    $ticketsdata[] = [
        'id'               => $ticket->id,
        'title'            => $ticket->title,
        'priority'         => get_string('priority_' . $ticket->priority, 'local_supportdesk'),
        'status'           => $statustranslated,
        'category_title'   => $ticket->category_title ?: get_string('general', 'local_supportdesk'),
        'created_at'       => \local_supportdesk\local\presentation::date($ticket->created_at),
        'status_badge'     => \local_supportdesk\local\badges::status($ticket->status),
        'priority_badge'   => \local_supportdesk\local\badges::priority($ticket->priority),
        'category_badge'   => \local_supportdesk\local\badges::department($ticket->category_title ?: get_string('general', 'local_supportdesk'), $ticket->category_color ?? '#64748b'),
        'created_by'       => $creatorname,
        'assigned_to'      => $assignedname,
        'view_url'         => $viewurl->out(false),
        'is_action_needed' => $isactionneeded,
        'is_assigned_to_me' => ($ticket->assigned_to == $USER->id)
    ];
}

$categoriesdata = [];
if ($canmanage) {
    $categories = $DB->get_records('local_supportdesk_categories');
    $catstats = [];
    if (!empty($categories)) {
        $sqlstats = "SELECT category_id,
                            SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) AS closed_count,
                            SUM(CASE WHEN status != 'closed' THEN 1 ELSE 0 END) AS open_count
                     FROM {local_supportdesk_tickets}
                     WHERE category_id IS NOT NULL
                     GROUP BY category_id";
        $statsrecords = $DB->get_records_sql($sqlstats);
        foreach ($statsrecords as $stat) {
            $catstats[$stat->category_id] = $stat;
        }
    }
    foreach ($categories as $cat) {
        $catopen = isset($catstats[$cat->id]) ? $catstats[$cat->id]->open_count : 0;
        $catclosed = isset($catstats[$cat->id]) ? $catstats[$cat->id]->closed_count : 0;
        $categoriesdata[] = [
            'title'          => $cat->title,
            'badge'          => \local_supportdesk\local\badges::department($cat->title, $cat->color),
            'statistics' => \local_supportdesk\local\presentation::statistics(['open' => $catopen, 'closed' => $catclosed]),
            'open_tickets'   => $catopen,
            'closed_tickets' => $catclosed,
        ];
    }
}

$paginationdata = [];
$totalpages = ceil($totalcount / $perpage);
if ($totalpages > 1) {
    for ($i = 0; $i < $totalpages; $i++) {
        $pagurl = new moodle_url($PAGE->url, ['page' => $i]);
        $paginationdata[] = [
            'pagnumber' => $i + 1,
            'pagurl'    => $pagurl->out(false),
            'active'    => ($i == $page),
        ];
    }
}

$prevurl = false;
if ($page > 0) {
    $prevurl = (new moodle_url($PAGE->url, ['page' => $page - 1]))->out(false);
}

$nexturl = false;
if ($page < $totalpages - 1) {
    $nexturl = (new moodle_url($PAGE->url, ['page' => $page + 1]))->out(false);
}

$templatedata = [
    'user_name'             => fullname($USER),
    'currentyear'           => date('Y'),
    'has_manage_capability' => $canmanage,
    'has_addtick_capability' => $canaddticket,
    'has_addCat_capability' => $canaddcategory,
    'total_tickets'         => $totalcount,
    'statistics' => \local_supportdesk\local\presentation::statistics(['total' => $totalcount, 'open' => $totalopenglobal, 'closed' => $totalclosedglobal]),
    'total_closed_tickets'  => $totalclosedglobal,
    'total_open_tickets'    => $totalopenglobal,
    'categories'            => $categoriesdata,
    'tickets'               => $ticketsdata,
    'has_pagination'        => ($totalpages > 1),
    'pagination'            => $paginationdata,
    'prev_url'              => $prevurl,
    'next_url'              => $nexturl,
    'showing_from'          => $totalcount > 0 ? $offset + 1 : 0,
    'showing_to'            => min($offset + $perpage, $totalcount),
    'total_records'         => $totalcount,
    'show_assignment_column' => ($canmanage || $hasmyassignment),
];

$sidebars = $canmanage ? ['statistics' => 'all_tickets_stats', 'department_statistics' => 'support_departments'] : [];
\local_supportdesk\local\presentation::page('index', $templatedata, $sidebars);
