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
