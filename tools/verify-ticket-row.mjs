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
 * File documentation for tools/verify-ticket-row.mjs.
 *
 * @package   local_supportdesk
 * @copyright 2025 learn-ix support@learn-ix.com
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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
