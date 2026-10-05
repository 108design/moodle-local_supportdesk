<?php
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
