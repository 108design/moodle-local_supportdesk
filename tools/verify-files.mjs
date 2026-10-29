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
 * File documentation for tools/verify-files.mjs.
 *
 * @package   local_supportdesk
 * @copyright 2025 learn-ix support@learn-ix.com
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later; 108design, 2026-10-03.
// Clipboard images use the existing multipart file input without immediate network traffic.
import fs from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';
const source = fs.readFileSync(new URL('../amd/src/forms.js', import.meta.url), 'utf8')
    .replace(/^import .*;$/m, '').replaceAll('export const ', 'const ');
const element = () => ({children: [], events: {}, append(...nodes) {this.children.push(...nodes);},
    replaceChildren() {this.children = [];}, setAttribute() {}, addEventListener(name, fn) {this.events[name] = fn;}});
const input = element(), list = element(), drop = {...element(), dataset: {}}, field = element();
input.files = [];
class TestFile extends Blob {
    constructor(parts, name, options) {super(parts, options); this.name = name; this.lastModified = options.lastModified || 1;}
}
class Transfer {constructor() {this.files = []; this.items = {add: f => this.files.push(f)};}}
const region = {dataset: {}, querySelector: s => ({'input[type="file"]': input,
    '[data-file-list]': list, '[data-file-drop]': drop}[s]), closest: () => ({querySelectorAll: () => [field]})};
const context = vm.createContext({window: {DataTransfer: Transfer, File: TestFile},
    document: {createElement: element}, getString: async key => key, Date, console});
vm.runInContext(source + '\nthis.attachFiles = attachFiles;', context);
await context.attachFiles(region);
const firstHandler = field.events.paste;
await context.attachFiles(region);
assert.equal(field.events.paste, firstHandler, 'one paste listener per field');
const paste = (items, text = '') => {
    let prevented = false;
    field.events.paste({clipboardData: {items, getData: () => text}, preventDefault: () => {prevented = true;}});
    return prevented;
};
const image = type => ({kind: 'file', type, getAsFile: () => new TestFile(['artificial image'], 'image', {type})});
assert.equal(paste([{kind: 'string', type: 'text/plain'}], 'ordinary text'), false);
assert.equal(input.files.length, 0);
assert.equal(paste([image('image/png')]), true);
assert.equal(input.files[0].type, 'image/png');
assert.match(input.files[0].name, /^screenshot-\d+-1\.png$/);
assert.equal(list.children.length, 1);
assert.equal(paste([image('image/jpeg')], 'keep mixed text'), false);
assert.match(input.files[1].name, /-2\.jpg$/);
assert.notEqual(input.files[0].name, input.files[1].name);
assert.equal(paste([image('image/svg+xml')]), false);
assert.equal(paste([{kind: 'file', type: 'image/png', getAsFile: () => null}]), false);
assert.equal(input.files.length, 2);
list.children[0].children[1].events.click();
assert.equal(input.files.length, 1, 'remove pasted file from multipart input');
const selected = new TestFile(['selected'], 'selected.txt', {type: 'text/plain'});
input.files = [selected]; input.events.change();
assert.equal(input.files.length, 2, 'chooser retains existing pasted file');
let dropped = false;
drop.events.drop({dataTransfer: {files: [new TestFile(['drop'], 'drop.txt', {type:'text/plain'})]},
    preventDefault: () => {dropped = true;}});
assert.equal(dropped, true); assert.equal(input.files.length, 3);
assert.equal(drop.dataset.dragging, undefined);
console.log('Attachment checks passed: image paste, unique names, mixed/plain text, removal, chooser/drop parity, idempotence.');
