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
 * File documentation for classes/local/navigation.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk\local;
defined('MOODLE_INTERNAL') || die();

/** Expose the permission-aware global node through Moodle's primary navigation API. */
class navigation {
    public static function primary_extend(\core\hook\navigation\primary_extend $hook): void {
        global $PAGE;
        $source = $PAGE->navigation->find('supportdesk_nav', \navigation_node::TYPE_CUSTOM);
        if ($source && !$hook->get_primaryview()->find('supportdesk_nav', \navigation_node::TYPE_CUSTOM)) {
            $hook->get_primaryview()->add($source->text, $source->action(), \navigation_node::TYPE_CUSTOM,
                null, 'supportdesk_nav', $source->icon);
        }
    }
}
