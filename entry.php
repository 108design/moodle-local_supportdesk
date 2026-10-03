<?php
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
require_once(__DIR__ . '/../../config.php');
use local_supportdesk\local\visitor_access;
use local_supportdesk\local\access;
access::require_enabled();
$target = visitor_access::target(optional_param('next', '/local/supportdesk/add.php', PARAM_RAW_TRIMMED));
if (isloggedin() && !isguestuser()) {redirect($target);}
if (!visitor_access::enabled()) {
    $SESSION->wantsurl = $target->out(false);
    redirect(get_login_url());
}
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/supportdesk/entry.php', ['next' => $target->out_as_local_url(false)]);
$PAGE->set_pagelayout('login');
$PAGE->set_title(get_string('visitor_heading', 'local_supportdesk'));
$PAGE->set_heading(get_string('pluginname', 'local_supportdesk'));
$PAGE->requires->css(new moodle_url('/local/supportdesk/styles/style.css'));
$SESSION->wantsurl = $target->out(false);
header('Cache-Control: no-store, private');
header('Referrer-Policy: no-referrer');
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_supportdesk/entry', [
    'magiclinkurl' => (new moodle_url('/auth/magiclink/login.php', ['returnurl' => $target->out_as_local_url(false)]))->out(false),
    'loginurl' => get_login_url(),
]);
echo $OUTPUT->footer();
