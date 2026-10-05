<?php
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk\local;
defined('MOODLE_INTERNAL') || die();

/** Native administration owns the mode selector; unavailable providers cannot be activated. */
class visitor_mode_setting extends \admin_setting_configselect {
    public function get_setting() {
        return visitor_mode::selected();
    }

    public function write_setting($data) {
        if ((string)$data === 'magiclink' && !visitor_access::availability()['ready']) {
            return get_string('visitor_unavailable', 'local_supportdesk');
        }
        return parent::write_setting($data);
    }
}
