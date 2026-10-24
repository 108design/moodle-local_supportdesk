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
 * File documentation for claim.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
require_once(__DIR__ . '/../../config.php');
\local_supportdesk\local\access::require_enabled();
$id = required_param('id', PARAM_INT);
$PAGE->set_url('/local/supportdesk/claim.php', ['id' => $id]);
require_login(null, false);
\local_supportdesk\local\public_ui::prepare('public_claim');
$failed = empty($SESSION->supportdesk_publicclaims[$id]);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_sesskey();
    if (\local_supportdesk\local\public_ui::try_claim($id)) {
        redirect(new moodle_url('/local/supportdesk/view.php', ['id' => $id]));
    }
    $failed = true;
}
\local_supportdesk\local\public_ui::page('public_outcome', [
    'heading' => get_string('public_claim', 'local_supportdesk'),
    'notice' => get_string($failed ? 'public_claim_failed' : 'public_claim_help', 'local_supportdesk'),
    'failed' => $failed, 'canclaim' => !$failed, 'sesskey' => sesskey(), 'actionurl' => $PAGE->url->out(false),
    'retryurl' => !$failed || !empty($SESSION->supportdesk_publicclaims[$id]) ? $PAGE->url->out(false) : '',
    'ticketsurl' => (new moodle_url('/local/supportdesk/index.php'))->out(false),
]);
