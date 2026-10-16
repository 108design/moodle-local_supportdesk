<?php
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.
defined('MOODLE_INTERNAL') || die();
$callbacks = [
    ['hook' => \core\hook\navigation\primary_extend::class,
     'callback' => [\local_supportdesk\local\navigation::class, 'primary_extend']],
];

// Decorative product identity is restricted to the native administration surfaces.
$callbacks[] = [
    'hook' => \core\hook\output\before_standard_head_html_generation::class,
    'callback' => [\local_supportdesk\local\admin_branding::class, 'head'],
];
$callbacks[] = [
    'hook' => \core\hook\output\before_http_headers::class,
    'callback' => [\local_supportdesk\local\admin_branding::class, 'prepare'],
];

$callbacks[] = [
    'hook' => \core\hook\output\before_http_headers::class,
    'callback' => [\local_supportdesk\local\admin_help::class, 'prepare'],
];
$callbacks[] = [
    'hook' => \core\hook\output\before_standard_footer_html_generation::class,
    'callback' => [\local_supportdesk\local\admin_help::class, 'footer'],
];
