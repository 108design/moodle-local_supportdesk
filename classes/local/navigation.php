<?php
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
