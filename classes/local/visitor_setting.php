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
 * File documentation for classes/local/visitor_setting.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk\local;
defined('MOODLE_INTERNAL') || die();

/** Native disabled setting plus server-side availability check. */
class visitor_setting extends \admin_setting_configcheckbox {
    public function is_readonly(): bool {
        // A previously enabled option must remain switchable off when its provider becomes unavailable.
        return parent::is_readonly() || (get_config('local_supportdesk', 'visitoraccess') !== '1'
            && !visitor_access::availability()['ready']);
    }
    public function write_setting($data) {
        if ((string)$data === '1' && !visitor_access::availability()['ready']) {
            return get_string('visitor_unavailable', 'local_supportdesk');
        }
        return parent::write_setting($data);
    }
}
