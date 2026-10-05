<?php
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
