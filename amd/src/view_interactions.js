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
