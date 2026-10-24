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
 * File documentation for amd/src/admin_help_form.js.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Copyright (C) 2026 Andreas Giesen. See LICENSE.

/** Align help in our registered forms without changing fields or their values. */
export const init = () => {
    const navigation = document.querySelector('#usernavigation');
    const alignNavbarHelp = () => {
        if (!navigation) return;
        const help = navigation.querySelector('.local-supportdesk-help-navbar');
        const children = [...navigation.children];
        const userMenu = children.find(node => node.matches('[data-region="usermenu"], .usermenu-container'));
        const userIndex = userMenu ? children.indexOf(userMenu) : children.length;
        // Older Moodle places its divider after the user menu, outside the help insertion area.
        const anchor = children.find((node, index) => index < userIndex &&
            node.classList.contains('divider')) || userMenu;
        if (help && anchor && (help.parentElement !== navigation || help.nextElementSibling !== anchor)) {
            navigation.insertBefore(help, anchor);
        }
    };
    // Navbar callbacks can finish in any order; keep help immediately before the user-area divider.
    alignNavbarHelp();
    if (navigation) new MutationObserver(alignNavbarHelp).observe(navigation, {childList: true});

    // Keep linked information beside its native control, retaining labels and defaults.
    document.querySelectorAll('.form-item .form-text .local-supportdesk-context-help-icon').forEach(icon => {
        const setting = icon.closest('.form-item')?.querySelector('.form-setting');
        const field = setting?.querySelector('input:not([type="hidden"]), select, textarea');
        if (!field) return;
        let control = field;
        while (control.parentElement !== setting) control = control.parentElement;
        const row = document.createElement('div');
        row.className = 'local-supportdesk-setting-control';
        control.before(row);
        row.append(control, icon);
    });

};
