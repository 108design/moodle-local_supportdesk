<?php
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
require_once(__DIR__ . '/../../config.php');
use local_supportdesk\local\visitor_access;
use local_supportdesk\local\access;
access::require_enabled();
$target = visitor_access::target(optional_param('next', '/local/supportdesk/add.php', PARAM_RAW_TRIMMED));
if (isloggedin() && !isguestuser()) {redirect($target);}
if (!visitor_access::enabled() && !\local_supportdesk\local\public_intake::enabled()) {
    $SESSION->wantsurl = $target->out(false);
    redirect(get_login_url());
}
$PAGE->set_url('/local/supportdesk/entry.php', ['next' => $target->out_as_local_url(false)]);
\local_supportdesk\local\public_ui::prepare('visitor_heading');
$SESSION->wantsurl = $target->out(false);
\local_supportdesk\local\public_ui::page('entry', [
    'publicurl' => \local_supportdesk\local\public_intake::enabled() ? (new moodle_url('/local/supportdesk/public.php'))->out(false) : '',
    'hasmagiclink' => visitor_access::enabled(),
    'magiclinkurl' => (new moodle_url('/auth/magiclink/login.php', ['returnurl' => $target->out_as_local_url(false)]))->out(false),
    'loginurl' => get_login_url(),
]);
