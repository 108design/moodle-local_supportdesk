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
