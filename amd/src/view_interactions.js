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
 * File documentation for amd/src/view_interactions.js.
 *
 * @package   local_supportdesk
 * @copyright 2025 learn-ix support@learn-ix.com
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later; 108design, 2026-10-03.
import {init as initGallery} from 'local_supportdesk/attachment_gallery';
export const init = () => {
    initGallery();
    const search = document.querySelector('[data-filter-assignees]');
    const select = document.getElementById('supportdesk-assignee');
    if (search && select) {
        search.addEventListener('input', () => {
            const term = search.value.trim().toLocaleLowerCase();
            Array.from(select.options).forEach(option => {
                option.hidden = option.value !== '0' && !option.selected
                    && !(option.dataset.search || option.textContent).toLocaleLowerCase().includes(term);
            });
        });
    }
};
