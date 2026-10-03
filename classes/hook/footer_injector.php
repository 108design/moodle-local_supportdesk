<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Footer injector hook listener.
 *
 * @package    local_supportdesk
 * @copyright  2026 learn-ix support@learn-ix.com
 * Modified 2026-10-03 by 108design: Supportdesk consolidation.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_supportdesk\hook;

/**
 * Class footer_injector for injecting urgent alerts.
 *
 * @package    local_supportdesk
 * @copyright  2026 learn-ix support@learn-ix.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class footer_injector {

    /**
     * Injects urgent alerts before the footer HTML is generated.
     *
     * @param \core\hook\output\before_footer_html_generation $hook The hook instance.
     * @return void
     */
    public static function add_urgent_alert(\core\hook\output\before_footer_html_generation $hook): void {
        global $PAGE, $USER;

        if (!isloggedin() || isguestuser() || get_config('local_supportdesk', 'enabled') === '0' || !get_config('local_supportdesk', 'version')) {
            return;
        }

        $context = \context_system::instance();

        if ((int)get_config('local_supportdesk', 'version') >= 2026100305
                && has_capability('local/supportdesk:specialist', $context)
                && \local_supportdesk\local\access::can_manage()
                && \local_supportdesk\local\department_team::has_memberships((int)$USER->id)) {
            $PAGE->requires->js_call_amd('local_supportdesk/urgent_alert', 'init', [(int)$USER->id]);
        }
    }
}
