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
 * File documentation for classes/local/visitor_mode.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk\local;
defined('MOODLE_INTERNAL') || die();

/** One explicit visitor path; ordinary Moodle sign-in remains available in every mode. */
class visitor_mode {
    public static function selected(): string {
        $mode = get_config('local_supportdesk', 'visitormode');
        if ($mode === false) {
            // Preserve an existing opt-in; if both legacy switches were set, prefer verified access.
            return get_config('local_supportdesk', 'visitoraccess') === '1' ? 'magiclink'
                : (get_config('local_supportdesk', 'publiccreate') === '1' ? 'anonymous' : 'login');
        }
        return in_array($mode, ['login', 'anonymous', 'magiclink'], true) ? $mode : 'login';
    }

}
