// SPDX-License-Identifier: GPL-3.0-or-later; 108design, 2026-10-03.
import Modal from 'core/modal';
import Notification from 'core/notification';
export const init = () => {
    document.querySelectorAll('[data-preview-image]').forEach(button => {
        button.addEventListener('click', async () => {
            try {
                const image = document.createElement('img'); image.src = button.dataset.previewImage;
                image.alt = button.dataset.previewTitle; image.className = 'supportdesk-image-preview';
                const title = document.createElement('span'); title.textContent = button.dataset.previewTitle;
                await Modal.create({title: title.innerHTML, body: image.outerHTML,
                    show: true, removeOnClose: true});
            } catch (error) { Notification.exception(error); }
        });
    });
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
