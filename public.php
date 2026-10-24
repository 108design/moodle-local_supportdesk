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
 * File documentation for public.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
require_once(__DIR__ . '/../../config.php');
use local_supportdesk\local\public_intake;
\local_supportdesk\local\access::require_enabled();
if (isloggedin() && !isguestuser()) {redirect(new moodle_url('/local/supportdesk/add.php'));}
$PAGE->set_url('/local/supportdesk/public.php');
\local_supportdesk\local\public_ui::prepare('public_heading', true);
if (!public_intake::enabled()) {
    \local_supportdesk\local\public_ui::page('public_outcome', [
        'heading' => get_string('public_heading', 'local_supportdesk'),
        'notice' => get_string('public_unavailable', 'local_supportdesk'), 'loginurl' => get_login_url(),
    ]);
    exit;
}
$data = ['name' => '', 'email' => '', 'title' => '', 'description' => '', 'category_id' => 0, 'priority' => 'medium'];
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_sesskey();
    foreach (['name', 'title'] as $field) {$data[$field] = required_param($field, PARAM_TEXT);}
    $data['email'] = required_param('email', PARAM_RAW_TRIMMED);
    $data['description'] = required_param('description', PARAM_TEXT);
    $data['category_id'] = required_param('category_id', PARAM_INT);
    $data['priority'] = optional_param('priority', 'medium', PARAM_RAW_TRIMMED);
    $data['website'] = optional_param('website', '', PARAM_RAW_TRIMMED);
    try {
        public_intake::submit($data, $_FILES['attachments'] ?? [], optional_param('cf-turnstile-response', '', PARAM_RAW_TRIMMED));
        redirect(new moodle_url('/local/supportdesk/public.php', ['submitted' => 1]));
    } catch (moodle_exception $e) {
        $error = get_string(in_array($e->errorcode, ['public_invalid', 'public_rate_limit', 'public_verification_failed'])
            ? $e->errorcode : 'public_invalid', 'local_supportdesk');
    }
}
$submitted = optional_param('submitted', 0, PARAM_BOOL) && !empty($SESSION->supportdesk_publiclast);
$categories = [];
foreach ($DB->get_records('local_supportdesk_categories', null, 'title ASC') as $category) {
    $categories[] = ['id' => $category->id, 'title' => $category->title, 'selected' => $category->id == $data['category_id']];
}
if (!$submitted) {$PAGE->requires->js(new moodle_url('https://challenges.cloudflare.com/turnstile/v0/api.js'), true);}
\local_supportdesk\local\public_ui::page('public_page', $data + [
    'error' => $error, 'submitted' => $submitted, 'ticketid' => $SESSION->supportdesk_publiclast ?? 0,
    'categories' => $categories, 'sesskey' => sesskey(), 'action_url' => $PAGE->url->out(false),
    'priorities' => \local_supportdesk\local\presentation::priority_choices($data['priority']),
    'login_url' => get_login_url(), 'claim_url' => (new moodle_url('/local/supportdesk/claim.php',
        ['id' => $SESSION->supportdesk_publiclast ?? 0]))->out(false),
    'sitekey' => get_config('local_supportdesk', 'turnstilesitekey'),
    'file_field' => 'attachments[]', 'file_id' => 'public-attachments',
]);
