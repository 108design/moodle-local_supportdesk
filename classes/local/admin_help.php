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
 * File documentation for classes/local/admin_help.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Copyright (C) 2026 Andreas Giesen. See LICENSE.
namespace local_supportdesk\local;

defined('MOODLE_INTERNAL') || die();

/** Contextual settings help, following the standalone Frontpage panel contract. */
final class admin_help {
    private const TOPICS = ['overview', 'delivery', 'tickets', 'handling'];
    private static array $topics = [];
    private static bool $prepared = false;

    /** Register only the permitted settings page before its header is rendered. */
    public static function prepare(\core\hook\output\before_http_headers $hook): void {
        global $CFG, $PAGE;
        if (self::$prepared || (defined('CLI_SCRIPT') && CLI_SCRIPT)) {
            return;
        }
        if (admin_branding::page_capability() !== null) {
            self::$topics = ['overview', 'delivery'];
        } else {
            $context = \context_system::instance();
            if (!self::is_ticket_page($PAGE->url) || !isloggedin() || isguestuser()
                    || get_config('local_supportdesk', 'enabled') === '0'
                    || (!access::can_manage() && !has_any_capability([
                        'local/supportdesk:viewticket', 'local/supportdesk:addticket'], $context))) {
                return;
            }
            self::$topics = ['tickets'];
            if (access::can_manage()) {
                self::$topics[] = 'handling';
            }
        }
        $PAGE->add_body_class('local-supportdesk-admin-help-page');
        self::$prepared = true;
        $PAGE->requires->css(new \moodle_url('/local/supportdesk/styles/admin_help.css', [
            'rev' => get_config('local_supportdesk', 'version'),
            'theme' => theme_get_revision(),
        ]));
        $module = (int)$CFG->version < 2025041400 ? 'admin_help_legacy' : 'admin_help';
        $PAGE->requires->js_call_amd('local_supportdesk/admin_help_form', 'init');
        $PAGE->requires->js_call_amd('local_supportdesk/' . $module, 'init', [[
            'topic' => optional_param('supportdeskhelp', '', PARAM_ALPHA),
        ]]);
    }

    /** Match native ticket routes, including installations below a URL subdirectory. */
    private static function is_ticket_page(\moodle_url $url): bool {
        foreach (['index.php', 'add.php', 'view.php'] as $page) {
            if ($url->get_path() === (new \moodle_url('/local/supportdesk/' . $page))->get_path()) {
                return true;
            }
        }
        return false;
    }

    /** Native popover with concise guidance and a direct link to the extended topic. */
    public static function icon(string $key): string {
        global $CFG, $PAGE;
        if (!in_array($key, self::TOPICS, true)) {
            throw new \coding_exception('Unknown Administration help topic.');
        }
        if (defined('CLI_SCRIPT') && CLI_SCRIPT) {
            return '';
        }
        $url = new \moodle_url($PAGE->url);
        $url->param('supportdeskhelp', $key);
        $help = new \help_icon('adminhelp_' . $key, 'local_supportdesk', $url->out(false));
        $renderer = $PAGE->get_renderer('core');
        $data = (array)$help->export_for_template($renderer);
        $data['completedoclink'] = \html_writer::link($url, get_string('adminhelpmore', 'local_supportdesk'));
        $data['legacy'] = (int)$CFG->version < 2025041400;
        $data['infoicon'] = (int)$CFG->version < 2026100500 ? 'i/info' : 'i/circleinfo';
        return $renderer->render_from_template('local_supportdesk/settings_help_icon', $data);
    }

    /** Match the panel name to its audience. */
    private static function title(): string {
        return get_string(in_array('tickets', self::$topics, true) ? 'tickethelp' : 'adminhelp', 'local_supportdesk');
    }

    /** Always available in the native Topnav user area on the registered page. */
    public static function navbar(\renderer_base $renderer): string {
        global $CFG;
        if (!self::$prepared) {
            return '';
        }
        $label = self::title();
        return \html_writer::tag('button', $renderer->pix_icon(
            (int)$CFG->version < 2026100500 ? 'i/info' : 'i/circleinfo', ''), [
                'type' => 'button', 'class' => 'nav-link icon-no-margin local-supportdesk-help-navbar',
                'data-supportdesk-help-open' => '', 'aria-label' => $label, 'title' => $label,
                'aria-controls' => 'local-supportdesk-context-help', 'aria-expanded' => 'false',
            ]);
    }

    /** Same header, topic chooser and article layout as the Frontpage reference. */
    public static function render(): string {
        global $PAGE;
        if (!self::$prepared) {
            return '';
        }
        $title = self::title();
        $close = \html_writer::tag('button', \html_writer::span('&#215;', '', ['aria-hidden' => 'true']), [
            'type' => 'button', 'class' => 'btn btn-secondary btn-sm local-supportdesk-help-close',
            'aria-label' => get_string('closebuttontitle', 'moodle'), 'data-supportdesk-help-close' => '1',
        ]);
        $options = '';
        $articles = '';
        foreach (self::$topics as $topic) {
            $label = get_string('adminhelp_' . $topic, 'local_supportdesk');
            $options .= \html_writer::tag('option', $label, ['value' => $topic]);
            $articles .= \html_writer::tag('article', \html_writer::tag('h3', $label, ['class' => 'h5', 'tabindex' => '-1'])
                . get_string('adminhelp_' . $topic . '_article', 'local_supportdesk'), [
                    'data-supportdesk-help-article' => $topic, 'hidden' => 'hidden',
                ]);
        }
        $choice = \html_writer::tag('label', get_string('adminhelptopics', 'local_supportdesk'), [
            'for' => 'local-supportdesk-help-topic', 'class' => 'form-label',
        ]) . \html_writer::tag('select', $options, [
            'id' => 'local-supportdesk-help-topic', 'class' => 'form-select custom-select',
        ]);
        // Shared help-panel identity: no link or action; close remains beside the topic selector.
        $brand = \html_writer::tag('span', \html_writer::empty_tag('img', [
            'src' => $PAGE->get_renderer('core')->image_url('helpbrand', 'local_supportdesk')->out(false),
            'alt' => '', 'width' => 25, 'height' => 24,
        ]), [
            'class' => 'local-supportdesk-help-brand', 'role' => 'img', 'aria-label' => '108design',
            'title' => '108design', 'tabindex' => '0', 'data-toggle' => 'tooltip', 'data-bs-toggle' => 'tooltip',
        ]);
        $header = \html_writer::tag('header', \html_writer::tag('h2', $title, [
            'id' => 'local-supportdesk-context-help-title', 'class' => 'sr-only visually-hidden', 'tabindex' => '-1',
        ]) . \html_writer::div($choice, 'local-supportdesk-help-choices') . $brand . $close,
            ['class' => 'local-supportdesk-help-header local-supportdesk-help-header-branded']);
        return \html_writer::tag('aside', $header
            . \html_writer::div($articles, 'local-supportdesk-help-content', ['tabindex' => '0']), [
                'id' => 'local-supportdesk-context-help', 'class' => 'local-supportdesk-context-help',
                'aria-labelledby' => 'local-supportdesk-context-help-title', 'hidden' => 'hidden',
                'data-supportdesk-help-url' => $PAGE->url->out(false),
            ]);
    }

    /** Append the registered panel without changing the page's controls. */
    public static function footer(\core\hook\output\before_standard_footer_html_generation $hook): void {
        $hook->add_html(self::render());
    }
}
