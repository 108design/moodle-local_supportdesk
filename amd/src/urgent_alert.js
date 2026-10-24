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
 * File documentation for amd/src/urgent_alert.js.
 *
 * @package   local_supportdesk
 * @copyright 2025 learn-ix support@learn-ix.com
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later
// Derived from learn-ix Academic Ticket System, copyright 2026 learn-ix.
// Modified 2026-10-03 by 108design: scoped, safe rendering, localised and no external audio.
import ajax from 'core/ajax';
import {get_string as getString} from 'core/str';

let started = false;

export const init = (userid) => {
    if (started) {return;}
    started = true;
    const storagekey = `supportdesk_seen_tickets_${userid}`;
    const seen = () => {
        try {
            const value = JSON.parse(window.localStorage.getItem(storagekey) || '[]');
            return Array.isArray(value) ? value : [];
        } catch (error) {return [];}
    };
    const render = async data => {
        if (document.getElementById('supportdesk-urgent-toast') || seen().includes(data.id)) {return;}
        const button = document.createElement('button');
        button.type = 'button';
        button.id = 'supportdesk-urgent-toast';
        button.className = 'alert alert-warning';
        button.style.cssText = 'position:fixed;top:85px;right:25px;z-index:1050;max-width:360px;text-align:start';
        const heading = document.createElement('strong');
        heading.textContent = await getString('urgentnotification', 'local_supportdesk');
        heading.style.display = 'block';
        const text = document.createElement('span');
        text.textContent = `${data.user}: ${data.title}`;
        button.append(heading, text);
        button.addEventListener('click', () => {
            try {
                window.localStorage.setItem(storagekey, JSON.stringify([...seen(), data.id].slice(-200)));
            } catch (error) { /* Storage may be unavailable; ticket access still works. */ }
            button.remove();
            window.location.assign(data.url);
        });
        document.body.appendChild(button);
    };
    const lastcheck = Math.floor(Date.now() / 1000) - 3600;
    const check = () => {
        if (document.hidden) {return;}
        ajax.call([{methodname:'local_supportdesk_check_urgent', args:{lastcheck}}])[0]
            .then(data => data.status === 'found' ? render(data) : undefined)
            .catch(() => undefined);
    };
    check();
    window.setInterval(check, 30000);
};
