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
 * File documentation for db/hooks.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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

$callbacks[] = [
    'hook' => \core\hook\output\before_footer_html_generation::class,
    'callback' => [\local_supportdesk\hook\footer_injector::class, 'add_urgent_alert'],
];
