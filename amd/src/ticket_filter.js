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
 * File documentation for amd/src/ticket_filter.js.
 *
 * @package   local_supportdesk
 * @copyright 2025 learn-ix support@learn-ix.com
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later; 108design, 2026-10-03.
export const attachTicketRows = () => {
    document.querySelectorAll('[data-ticket-row]').forEach(row => {
        if (row.dataset.linkInitialised) { return; } row.dataset.linkInitialised = '1';
        row.addEventListener('click', event => {
            if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey
                    || event.altKey || window.getSelection()?.toString()) { return; }
            if (event.target.closest('.supportdesk-actions, [data-no-row-link], a, button, input, select, textarea, label, [role="button"], [contenteditable]')) { return; }
            const link = row.querySelector('[data-ticket-link]');
            if (link) { window.location.assign(link.href); }
        });
    });
};
export const init = () => {
    attachTicketRows();
    const input = document.getElementById('ticketSearch'), table = document.getElementById('ticketsTable');
    if (!input || !table) { return; }
    const rows = Array.from(table.querySelectorAll('tbody tr')).filter(row => row.querySelector('a'));
    input.addEventListener('input', () => {
        const term = input.value.trim().toLocaleLowerCase();
        rows.forEach(row => { row.hidden = !row.textContent.toLocaleLowerCase().includes(term); });
        const hint = document.getElementById('supportdesk-no-results');
        if (hint) { hint.hidden = !rows.length || rows.some(row => !row.hidden); }
    });
};
