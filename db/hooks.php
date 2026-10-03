<?php
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.
defined('MOODLE_INTERNAL') || die();
$callbacks = [
    ['hook' => \core\hook\navigation\primary_extend::class,
     'callback' => [\local_supportdesk\local\navigation::class, 'primary_extend']],
];
