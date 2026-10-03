// SPDX-License-Identifier: GPL-3.0-or-later; 108design, 2026-10-03.
// Exercise the browser recorder contract without microphone access or user recordings.
import fs from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';
import {fileURLToPath} from 'node:url';
const source = fs.readFileSync(fileURLToPath(new URL('../amd/src/forms.js', import.meta.url)), 'utf8')
    .replace(/^import .*;$/m, '').replaceAll('export const ', 'const ');
const run = async ({supported = true, denied = false} = {}) => {
    const events = {}, pageEvents = {}, elements = {};
    let stopped = 0, revoked = 0, recorder;
    for (const key of ['start', 'stop', 'clear', 'status', 'preview', 'file']) {
        elements[key] = {hidden: false, value: '', pause() {}, removeAttribute(name) {delete this[name];},
            addEventListener(event, fn) { events[key + event] = fn; }};
    }
    class Recorder {
        static isTypeSupported(type) { return type === 'audio/mp4'; }
        constructor(stream, options) { this.mimeType = options.mimeType; this.state = 'inactive'; this.events = {}; recorder = this; }
        addEventListener(event, fn) {this.events[event] = fn;}
        start() {this.state = 'recording';}
        stop() {this.state = 'inactive'; this.events.dataavailable({data: new Blob(['test audio'], {type:this.mimeType})}); this.events.stop();}
    }
    class File extends Blob { constructor(parts, name, options) {super(parts, options); this.name = name;} }
    class Transfer { constructor() {this.files = []; this.items = {add: file => this.files.push(file)};} }
    const region = {dataset: {}, querySelector: selector => elements[selector.match(/"(.*?)"/)[1]]};
    const window = {MediaRecorder: supported ? Recorder : undefined, DataTransfer: Transfer, Blob, File,
        URL: {createObjectURL: () => 'blob:test', revokeObjectURL: () => revoked++},
        addEventListener: (event, fn) => {pageEvents[event] = fn;}};
    const context = vm.createContext({window, navigator: {mediaDevices:{getUserMedia: async () => {
        if (denied) {throw new Error('denied');} return {getTracks: () => [{stop: () => stopped++}]};
    }}}, getString: async key => key, document:{}, console});
    vm.runInContext(source + '\nthis.attachRecorder = attachRecorder;', context);
    await context.attachRecorder(region);
    if (!supported) {assert.equal(elements.start.disabled, true); assert.equal(elements.status.textContent, 'recording_unsupported'); return;}
    await events.startclick();
    if (denied) {assert.equal(elements.status.textContent, 'mic_access_denied'); assert.equal(elements.start.disabled, false); return;}
    assert.equal(region.dataset.recording, '1'); assert.equal(elements.stop.hidden, false);
    events.stopclick();
    assert.equal(stopped, 1); assert.equal(region.dataset.recording, undefined);
    assert.equal(elements.file.files[0].name, 'voice_note.m4a');
    assert.equal(elements.file.files[0].type, 'audio/mp4'); assert.equal(elements.preview.hidden, false);
    events.clearclick(); assert.equal(revoked, 1); assert.equal(elements.preview.hidden, true);
    await events.startclick(); pageEvents.pagehide();
    assert.equal(stopped, 2); assert.equal(elements.preview.hidden, true); assert.equal(recorder.state, 'inactive');
};
await run(); await run({denied:true}); await run({supported:false});
console.log('Recorder API checks passed: codec/file parity, preview, clear, permission failure, unsupported browser, navigation cleanup.');
