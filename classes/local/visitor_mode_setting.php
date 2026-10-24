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
 * File documentation for classes/local/visitor_mode_setting.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk\local;
defined('MOODLE_INTERNAL') || die();

/** Native administration owns the mode selector; unavailable providers cannot be activated. */
class visitor_mode_setting extends \admin_setting_configselect {
    public function get_setting() {
        $mode = visitor_mode::selected();
        // Display the effective standard-login fallback without rewriting stored configuration.
        return $mode === 'magiclink' && !visitor_access::is_activated() ? 'login' : $mode;
    }

    public function write_setting($data) {
        if ((string)$data === 'magiclink' && !visitor_access::is_activated()) {
            return get_string('invalidaccessmode', 'local_supportdesk');
        }
        if ((string)$data === 'magiclink' && !visitor_access::availability()['ready']) {
            return get_string('visitor_unavailable', 'local_supportdesk');
        }
        return parent::write_setting($data);
    }
}
