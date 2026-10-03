// SPDX-License-Identifier: GPL-3.0-or-later; 108design, 2026-10-03.
import {get_string as getString} from 'core/str';
const text = key => getString(key, 'local_supportdesk');
export const attachRecorder = async region => {
    if (region.dataset.initialised) { return; }
    region.dataset.initialised = '1';
    const element = key => region.querySelector(`[data-record="${key}"]`);
    const start = element('start'), stop = element('stop'), clear = element('clear');
    const status = element('status'), preview = element('preview'), file = element('file');
    const keys = ['recording_now', 'ready', 'click_to_record', 'mic_access_denied', 'recording_unsupported'];
    const values = await Promise.all(keys.map(text));
    const labels = Object.fromEntries(keys.map((key, i) => [key, values[i]]));
    if (!navigator.mediaDevices?.getUserMedia || !window.MediaRecorder || !window.DataTransfer) {
        start.disabled = true; status.textContent = labels.recording_unsupported; return;
    }
    let recorder, stream, objectUrl, cancelled = false, requesting = false;
    const release = () => { stream?.getTracks().forEach(track => track.stop()); stream = undefined; };
    const discard = () => {
        preview.pause(); preview.removeAttribute('src'); preview.hidden = true;
        if (objectUrl) { window.URL.revokeObjectURL(objectUrl); objectUrl = undefined; }
        file.value = ''; clear.hidden = true;
    };
    start.addEventListener('click', async () => {
        if (requesting || recorder?.state === 'recording') { return; }
        requesting = true; start.disabled = true; cancelled = false;
        try {
            stream = await navigator.mediaDevices.getUserMedia({audio: true});
            if (cancelled) { release(); return; }
            const mimeType = ['audio/webm;codecs=opus', 'audio/ogg;codecs=opus', 'audio/mp4']
                .find(type => window.MediaRecorder.isTypeSupported(type));
            recorder = mimeType ? new window.MediaRecorder(stream, {mimeType}) : new window.MediaRecorder(stream);
            const chunks = [];
            recorder.addEventListener('dataavailable', event => { if (event.data.size) { chunks.push(event.data); } });
            recorder.addEventListener('error', () => {
                cancelled = true; release(); delete region.dataset.recording;
                start.hidden = false; stop.hidden = true; status.textContent = labels.mic_access_denied;
            });
            recorder.addEventListener('stop', () => {
                release(); delete region.dataset.recording; start.hidden = false; stop.hidden = true;
                if (cancelled || !chunks.length) { return; }
                discard();
                const type = recorder.mimeType || chunks[0].type || 'audio/webm';
                const extension = type.includes('mp4') ? 'm4a' : (type.includes('ogg') ? 'ogg' : 'webm');
                const blob = new window.Blob(chunks, {type});
                const transfer = new window.DataTransfer();
                transfer.items.add(new window.File([blob], `voice_note.${extension}`, {type}));
                file.files = transfer.files; objectUrl = window.URL.createObjectURL(blob);
                preview.src = objectUrl; preview.hidden = false; clear.hidden = false; status.textContent = labels.ready;
            });
            recorder.start(); region.dataset.recording = '1'; start.hidden = true; stop.hidden = false;
            clear.hidden = true; preview.hidden = true; status.textContent = labels.recording_now;
        } catch (error) { release(); status.textContent = labels.mic_access_denied; }
        finally { requesting = false; start.disabled = false; }
    });
    stop.addEventListener('click', () => { if (recorder?.state === 'recording') { recorder.stop(); } });
    clear.addEventListener('click', () => { discard(); status.textContent = labels.click_to_record; });
    window.addEventListener('pagehide', () => {
        cancelled = true; if (recorder?.state === 'recording') { recorder.stop(); } release(); discard();
    });
};
export const attachFiles = async region => {
    if (region.dataset.initialised) { return; } region.dataset.initialised = '1';
    const input = region.querySelector('input[type="file"]'), list = region.querySelector('[data-file-list]');
    const drop = region.querySelector('[data-file-drop]'), removeLabel = await text('remove_file');
    let files = [];
    const render = () => {
        list.replaceChildren();
        files.forEach((file, index) => {
            const item = document.createElement('li'), label = document.createElement('span');
            label.textContent = file.name;
            if (window.DataTransfer) {
                const remove = document.createElement('button'); remove.type = 'button';
                remove.className = 'btn btn-link btn-sm'; remove.textContent = removeLabel;
                remove.setAttribute('aria-label', `${removeLabel}: ${file.name}`);
                remove.addEventListener('click', () => { files.splice(index, 1); sync(); }); item.append(label, remove);
            } else { item.append(label); }
            list.append(item);
        });
    };
    const sync = () => {
        const transfer = new window.DataTransfer(); files.forEach(file => transfer.items.add(file));
        input.files = transfer.files; render();
    };
    input.addEventListener('change', () => {
        if (window.DataTransfer) {
            for (const file of input.files) {
                if (!files.some(existing => existing.name === file.name && existing.size === file.size
                        && existing.lastModified === file.lastModified)) { files.push(file); }
            } sync();
        } else { files = Array.from(input.files); render(); }
    });
    drop.addEventListener('dragover', event => { event.preventDefault(); drop.dataset.dragging = '1'; });
    drop.addEventListener('dragleave', () => { delete drop.dataset.dragging; });
    drop.addEventListener('drop', event => {
        event.preventDefault(); delete drop.dataset.dragging;
        if (window.DataTransfer) { files.push(...event.dataTransfer.files); sync(); }
    });
    // Keep pasted screenshots in the same multipart attachment input; no immediate upload.
    let pasteSequence = 0;
    if (window.DataTransfer && window.File) {
        region.closest('form')?.querySelectorAll('[data-supportdesk-paste]').forEach(field => {
            field.addEventListener('paste', event => {
                const clipboard = event.clipboardData;
                const images = Array.from(clipboard?.items || [])
                    .filter(item => item.kind === 'file' && /^image\/(png|jpeg|webp|gif|bmp)$/.test(item.type))
                    .map(item => item.getAsFile()).filter(Boolean);
                if (!images.length) { return; }
                images.forEach(image => {
                    const extension = { 'image/png': 'png', 'image/jpeg': 'jpg', 'image/webp': 'webp',
                        'image/gif': 'gif', 'image/bmp': 'bmp' }[image.type] || 'png';
                    files.push(new window.File([image], `screenshot-${Date.now()}-${++pasteSequence}.${extension}`,
                        {type: image.type, lastModified: Date.now()}));
                });
                sync();
                // Preserve normal text when the clipboard contains text and an image together.
                if (!clipboard.getData('text/plain')) { event.preventDefault(); }
            });
        });
    }
};
export const init = () => {
    document.querySelectorAll('[data-supportdesk-voice]').forEach(region => attachRecorder(region).catch(() => undefined));
    document.querySelectorAll('[data-supportdesk-files]').forEach(region => attachFiles(region).catch(() => undefined));
    document.querySelectorAll('.supportdesk-form').forEach(form => {
        if (form.dataset.initialised) { return; } form.dataset.initialised = '1';
        form.addEventListener('submit', event => {
            if (form.querySelector('[data-recording="1"]')) {
                event.preventDefault(); text('stop_before_sending').then(value => {
                    form.querySelector('[data-record="status"]').textContent = value;
                }).catch(() => undefined);
            }
        });
    });
};
