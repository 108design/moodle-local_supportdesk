// SPDX-License-Identifier: GPL-3.0-or-later; 108design, 2026-10-04.
// Standalone ticket viewer. PhotoSwipe is MIT licensed and loaded only on first use.
import {getStrings} from 'core/str';

/** A focusable thumbnail strip, shared by every original/reply image in the ticket. */
export const addThumbnails = (viewer, host, items, labels) => {
    const caption = document.createElement('p');
    caption.className = 'supportdesk-lightbox-caption';
    caption.setAttribute('aria-live', 'polite');
    const strip = document.createElement('div');
    strip.className = 'supportdesk-lightbox-strip';
    strip.setAttribute('role', 'group');
    strip.setAttribute('aria-label', labels.gallery);
    const buttons = items.map((item, index) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.setAttribute('aria-label', labels.thumbnail.replace('{$a}', String(index + 1)) + ': ' + item.alt);
        const image = document.createElement('img');
        image.src = item.msrc; image.alt = ''; image.loading = 'lazy'; image.draggable = false;
        button.append(image);
        button.addEventListener('click', () => viewer.goTo(index));
        button.addEventListener('keydown', event => {
            const target = {ArrowLeft: index - 1, ArrowRight: index + 1, Home: 0, End: items.length - 1}[event.key];
            if (target === undefined) return;
            event.preventDefault(); event.stopPropagation();
            const next = Math.max(0, Math.min(items.length - 1, target));
            viewer.goTo(next); buttons[next].focus();
        });
        strip.append(button);
        return button;
    });
    host.append(caption, strip);
    host.addEventListener('pointerdown', event => event.stopPropagation());
    const refresh = () => {
        caption.textContent = items[viewer.currIndex].alt;
        buttons.forEach((button, index) => button.setAttribute('aria-current', String(index === viewer.currIndex)));
        const active = buttons[viewer.currIndex];
        strip.scrollLeft = active.offsetLeft - strip.offsetLeft - (strip.clientWidth - active.offsetWidth) / 2;
    };
    viewer.on('change', refresh);
    refresh();
};

export const init = () => {
    document.querySelectorAll('.supportdesk[data-ticket-id]').forEach(root => {
        const links = Array.from(root.querySelectorAll('[data-ticket-image]'));
        if (!links.length || root.dataset.galleryReady) return;
        root.dataset.galleryReady = '1';
        const items = links.map(link => ({src: link.href, msrc: link.querySelector('img').src,
            width: Number(link.dataset.imageWidth), height: Number(link.dataset.imageHeight),
            alt: link.dataset.imageName, element: link}));
        let pending;
        let opening = false;
        const load = () => pending || (pending = Promise.all([
            new Promise((resolve, reject) => require(['local_supportdesk/photoswipe_core',
                'local_supportdesk/photoswipe_lightbox'], (core, lightbox) => resolve({core, lightbox}), reject)),
            getStrings(['close', 'image_previous', 'image_next', 'image_zoom', 'image_error',
                'image_gallery', 'image_thumbnail'].map(key => ({key, component: 'local_supportdesk'})))
        ]).then(([vendor, text]) => {
            const [close, previous, next, zoom, error, gallery, thumbnail] = text;
            const lightbox = new vendor.lightbox.default({dataSource: items, pswpModule: vendor.core.default,
                mainClass: 'pswp--supportdesk', closeTitle: close, arrowPrevTitle: previous,
                arrowNextTitle: next, zoomTitle: zoom, errorMsg: error, returnFocus: true,
                paddingFn: size => ({top: 48, bottom: size.x < 600 ? 140 : 126, left: 12, right: 12})});
            lightbox.on('uiRegister', () => lightbox.pswp.ui.registerElement({name: 'ticket-images',
                appendTo: 'root', className: 'supportdesk-lightbox-footer',
                onInit: host => addThumbnails(lightbox.pswp, host, items, {gallery, thumbnail})}));
            lightbox.init();
            return lightbox;
        }).catch(error => {pending = null; throw error;}));
        links.forEach((link, index) => link.addEventListener('click', async event => {
            if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            if (opening) return;
            opening = true;
            try {
                const lightbox = await load();
                link.focus({preventScroll: true});
                lightbox.loadAndOpen(index);
            } catch {
                window.location.assign(link.href);
            } finally {
                opening = false;
            }
        }));
    });
};
