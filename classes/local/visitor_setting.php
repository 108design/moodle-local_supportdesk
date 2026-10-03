<?php
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
