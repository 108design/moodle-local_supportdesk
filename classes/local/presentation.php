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
 * File documentation for classes/local/presentation.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk\local;
defined('MOODLE_INTERNAL') || die();

/** Moodle owns the page shell, navigation, icons, dialogs and block rendering. */
class presentation {
    public static function setup(): void {
        global $PAGE;
        $PAGE->set_pagelayout('standard');
        $PAGE->set_primary_active_tab('supportdesk_nav');
        // Static plugin assets need a release key as well as Moodle's theme/JS cache revision.
        $PAGE->requires->css(new \moodle_url('/local/supportdesk/styles/style.css',
            ['v' => (int)get_config('local_supportdesk', 'version')]));
        $PAGE->navbar->add(get_string('pluginname', 'local_supportdesk'), new \moodle_url('/local/supportdesk/index.php'));
        $current = basename($PAGE->url->get_path());
        if ($current !== 'index.php') {
            $PAGE->navbar->add(get_string($current === 'add.php' ? 'add_ticket' : ($current === 'add_category.php' ? 'support_departments' : 'viewticket'), 'local_supportdesk'));
        }
    }

    /** Compact, locale-aware dates in the user's timezone. */
    public static function date(int $timestamp): string {
        return userdate($timestamp, get_string('date_format', 'local_supportdesk'));
    }

    /** One shared native radio group for both authenticated and anonymous creation. */
    public static function priority_choices(string $selected = 'medium'): array {
        $choices = [];
        foreach (['low', 'medium', 'high', 'urgent'] as $value) {
            $choices[] = badges::priority($value) + ['value' => $value, 'selected' => $value === $selected];
        }
        return $choices;
    }

    /** Number before a correctly inflected, translated count label. */
    public static function statistics(array $counts): array {
        $rows = [];
        foreach ($counts as $kind => $count) {
            $rows[] = ['count' => (int)$count, 'label' => get_string('stats_' . $kind . ((int)$count === 1 ? '_one' : '_many'), 'local_supportdesk')];
        }
        return $rows;
    }

    /** Legacy callers do not inject plugin colour overrides into the theme. */
    public static function colours(): string { return ''; }

    public static function page(string $template, array $data, array $sidebars = []): void {
        global $PAGE, $OUTPUT;
        if (in_array($template, ['index', 'add_page', 'view'], true)) {
            $data['contexthelp'] = admin_help::icon('tickets');
        }
        $data['allowvoicenotes'] = (bool)get_config('local_supportdesk', 'allowvoicenotes');
        $fallback = '';
        $regions = $PAGE->blocks->get_regions();
        $region = in_array('side-pre', $regions, true) ? 'side-pre' : $PAGE->blocks->get_default_region();
        foreach ($sidebars as $name => $title) {
            $block = new \block_contents();
            $block->title = get_string($title, 'local_supportdesk');
            $block->content = $OUTPUT->render_from_template('local_supportdesk/' . $name, $data);
            $block->attributes['class'] = 'block block_supportdesk supportdesk-block';
            $block->attributes['id'] = 'supportdesk-block-' . $name;
            $block->controls = [];
            if ($region && in_array($region, $regions, true)) {
                $PAGE->blocks->add_fake_block($block, $region);
            } else {
                $fallback .= $OUTPUT->block($block);
            }
        }
        echo $OUTPUT->header();
        $context = \context_system::instance();
        $tabs = [new \tabobject('index.php', new \moodle_url('/local/supportdesk/index.php'), get_string('all_tickets', 'local_supportdesk'))];
        $tabs[0]->linkedwhenselected = true;
        if (has_capability('local/supportdesk:addticket', $context)) {
            $tabs[] = new \tabobject('add.php', new \moodle_url('/local/supportdesk/add.php'), get_string('add_ticket', 'local_supportdesk'));
        }
        if (has_capability('local/supportdesk:addcategory', $context)) {
            $tabs[] = new \tabobject('add_category.php', new \moodle_url('/local/supportdesk/add_category.php'), get_string('support_departments', 'local_supportdesk'));
        }
        $current = basename($PAGE->url->get_path());
        echo $OUTPUT->tabtree($tabs, $current === 'view.php' ? 'index.php' : $current);
        echo $OUTPUT->render_from_template('local_supportdesk/' . $template, $data);
        if ($fallback) { echo \html_writer::div($fallback, 'supportdesk-fallback'); }
        echo $OUTPUT->footer();
    }
}
