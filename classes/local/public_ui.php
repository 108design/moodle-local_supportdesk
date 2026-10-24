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
 * File documentation for classes/local/public_ui.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk\local;
defined('MOODLE_INTERNAL') || die();

/** Shared native layout for entry, anonymous creation, receipt and claim outcomes. */
class public_ui {
    public static function prepare(string $titlekey, bool $form = false): void {
        global $PAGE;
        $PAGE->set_context(\context_system::instance());
        $PAGE->set_pagelayout('login');
        $PAGE->set_title(get_string($titlekey, 'local_supportdesk'));
        $PAGE->set_heading(get_string('pluginname', 'local_supportdesk'));
        $PAGE->add_body_class('supportdesk-access-page');
        if ($form) {$PAGE->add_body_class('supportdesk-public-form-page');}
        $PAGE->requires->css(new \moodle_url('/local/supportdesk/styles/style.css',
            ['v' => (int)get_config('local_supportdesk', 'version')]));
        header('Cache-Control: no-store, private');
        header('Referrer-Policy: no-referrer');
    }

    /** Render with the optional Frontpage login host, otherwise use the active theme's login layout. */
    public static function page(string $template, array $data): void {
        global $PAGE, $OUTPUT;
        $PAGE->set_cacheable(false);
        $render = static function() use ($template, $data): string {
            global $OUTPUT;
            return $OUTPUT->render_from_template('local_supportdesk/' . $template, $data);
        };
        if (class_exists('\\local_frontpage\\local\\login_surface')
                && method_exists('\\local_frontpage\\local\\login_surface', 'render_panel')
                && \local_frontpage\local\login_surface::render_panel('local_supportdesk', $render)) {
            return;
        }
        echo $OUTPUT->header();
        echo \html_writer::div($render(), 'login-container supportdesk-access-panel');
        echo $OUTPUT->footer();
    }

    /** Expected claim refusal is a normal outcome; never expose ticket or contact details. */
    public static function try_claim(int $ticketid): bool {
        try {
            public_intake::claim($ticketid);
            return true;
        } catch (\moodle_exception $e) {
            if ($e->errorcode !== 'public_claim_failed' || $e->module !== 'local_supportdesk') {throw $e;}
            return false;
        }
    }
}
