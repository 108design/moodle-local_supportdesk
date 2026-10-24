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
 * File documentation for amd/src/category_alert.js.
 *
 * @package   local_supportdesk
 * @copyright 2025 learn-ix support@learn-ix.com
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later; 108design, 2026-10-03.
import Notification from 'core/notification';
import {get_string as getString} from 'core/str';
export const init = () => {
    document.querySelectorAll('[data-confirm-delete]').forEach(form => {
        form.addEventListener('submit', async event => {
            if (form.querySelector('[name="confirm"]')?.value === '1') { return; }
            event.preventDefault();
            try {
                const strings = await Promise.all(['delete_department', 'delete_department_confirm', 'delete', 'cancel']
                    .map(key => getString(key, 'local_supportdesk')));
                Notification.confirm(...strings, () => {
                    const confirmed = document.createElement('input');
                    confirmed.type = 'hidden'; confirmed.name = 'confirm'; confirmed.value = '1';
                    form.append(confirmed); form.requestSubmit();
                });
            } catch (error) { Notification.exception(error); }
        });
    });
};
