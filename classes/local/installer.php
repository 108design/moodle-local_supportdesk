<?php
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.

namespace local_supportdesk\local;

defined('MOODLE_INTERNAL') || die();

/** Idempotent plugin-owned support role; never reuse a generic custom role. */
class installer {
    public static function install_role(): int {
        global $DB;
        $role = $DB->get_record('role', ['shortname' => 'supportdeskagent']);
        if (!$role) {
            $id = create_role(get_string('rolespecialist', 'local_supportdesk'), 'supportdeskagent',
                get_string('rolespecialistdescription', 'local_supportdesk'), '');
            set_config('ownedroleid', $id, 'local_supportdesk');
            set_role_contextlevels($id, [CONTEXT_SYSTEM]);
        } else {
            $id = (int)$role->id;
            if ((int)get_config('local_supportdesk', 'ownedroleid') !== $id) {
                // A collision is not authority to grant access to an existing role.
                throw new \moodle_exception('rolecollision', 'local_supportdesk');
            }
        }
        $context = \context_system::instance();
        foreach (['manageticket', 'specialist', 'viewticket', 'download', 'addticket', 'viewownoverviews'] as $capability) {
            assign_capability('local/supportdesk:' . $capability, CAP_ALLOW, $id, $context->id, true);
        }
        return (int)$id;
    }
}
