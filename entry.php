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
 * File documentation for entry.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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
