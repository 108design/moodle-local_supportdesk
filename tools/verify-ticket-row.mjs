// SPDX-License-Identifier: GPL-3.0-or-later; 108design, 2026-10-03.
import fs from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';
const source = fs.readFileSync(new URL('../amd/src/ticket_filter.js', import.meta.url), 'utf8').replaceAll('export const ', 'const ');
let listener, selection = '', navigations = [];
const row = {dataset:{}, addEventListener:(name, fn) => {listener = fn;}, querySelector:() => ({href:'https://example.test/local/supportdesk/view.php?id=1'})};
const scope = vm.createContext({document:{querySelectorAll:()=>[row]}, window:{getSelection:()=>({toString:()=>selection}), location:{assign:url=>navigations.push(url)}}});
vm.runInContext(source + '\nthis.attachTicketRows = attachTicketRows;', scope);
scope.attachTicketRows();
const click = options => listener({button:0, target:{closest:()=>null}, ...options});
click({}); assert.equal(navigations.length, 1, 'ordinary noninteractive cell navigates');
for (const type of ['action cell', 'link', 'checkbox', 'button', 'input', 'editable content']) {
    click({target:{closest:()=>({type})}});
}
for (const flag of ['ctrlKey','metaKey','shiftKey','altKey','defaultPrevented']) {click({[flag]:true});}
click({button:1}); selection = 'selected ticket text'; click({});
assert.equal(navigations.length,1,'actions, current/future controls, modifiers and text selection do not navigate');
scope.attachTicketRows(); assert.equal(row.dataset.linkInitialised,'1');
console.log('Ticket row checks passed: main cells navigate; action/control cells, modifiers and selection excluded.');
