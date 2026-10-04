// SPDX-License-Identifier: GPL-3.0-or-later; 108design, 2026-10-04.
import fs from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';
const source = fs.readFileSync(new URL('../amd/src/attachment_gallery.js', import.meta.url), 'utf8')
    .replace(/^import .*;$/m, '').replaceAll('export const ', 'const ');
const element = () => ({children: [], events: {}, attributes: {}, offsetLeft: 10, offsetWidth: 64, clientWidth: 200,
    append(...children) {this.children.push(...children);},
    setAttribute(key, value) {this.attributes[key] = value;},
    addEventListener(key, handler) {this.events[key] = handler;}, focus() {this.focused = true;}});
const context = vm.createContext({document: {createElement: element}});
vm.runInContext(source + '\nthis.addThumbnails = addThumbnails;', context);
const items = [{alt: '<unsafe name>.png', msrc: '/protected/original?preview=thumb'},
    {alt: 'reply.png', msrc: '/protected/reply?preview=thumb'}];
const viewer = {currIndex: 0, events: {}, on(key, callback) {this.events[key] = callback;},
    goTo(index) {this.currIndex = index; this.events.change();}};
const host = element();
context.addThumbnails(viewer, host, items, {gallery: 'Ticket images', thumbnail: 'Image {$a}'});
const [caption, strip] = host.children, [first, second] = strip.children;
assert.equal(caption.textContent, '<unsafe name>.png', 'filename stays text, never HTML');
assert.equal(first.attributes['aria-current'], 'true');
assert.equal(second.children[0].src, items[1].msrc);
second.events.click();
assert.equal(caption.textContent, 'reply.png', 'original and reply share one collection');
assert.equal(second.attributes['aria-current'], 'true');
assert.equal(first.attributes['aria-current'], 'false');
let handled = 0;
const key = name => ({key: name, preventDefault() {handled++;}, stopPropagation() {handled++;}});
second.events.keydown(key('Home'));
assert.equal(viewer.currIndex, 0); assert.equal(first.focused, true);
first.events.keydown(key('End'));
assert.equal(viewer.currIndex, 1); assert.equal(second.focused, true);
second.events.keydown(key('ArrowRight')); assert.equal(viewer.currIndex, 1);
second.events.keydown(key('ArrowLeft')); assert.equal(viewer.currIndex, 0);
const previous = handled;
first.events.keydown(key('Tab')); assert.equal(handled, previous, 'native focus traversal is retained');
console.log('Gallery controls passed: ticket-wide collection, safe filenames, active thumbnail, click and keyboard navigation.');
