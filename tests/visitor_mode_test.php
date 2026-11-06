<?php
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
 * File documentation for tests/visitor_mode_test.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk;
defined('MOODLE_INTERNAL') || die();

use local_supportdesk\local\public_intake;
use local_supportdesk\local\visitor_mode;

/** Visitor options cannot activate each other or run simultaneously. */
final class visitor_mode_test extends \advanced_testcase {
    public function test_default_and_legacy_opt_in_resolution(): void {
        $this->resetAfterTest();
        foreach (['visitormode', 'visitoraccess', 'publiccreate'] as $key) {unset_config($key, 'local_supportdesk');}
        $this->assertSame('login', visitor_mode::selected());
        set_config('publiccreate', 1, 'local_supportdesk');
        $this->assertSame('anonymous', visitor_mode::selected());
        set_config('visitoraccess', 1, 'local_supportdesk');
        $this->assertSame('magiclink', visitor_mode::selected());
        $this->assertFalse(public_intake::enabled());
    }

    public function test_explicit_mode_always_overrides_both_legacy_switches(): void {
        $this->resetAfterTest();
        set_config('publiccreate', 1, 'local_supportdesk');
        set_config('visitoraccess', 1, 'local_supportdesk');
        foreach (['login', 'anonymous', 'magiclink'] as $mode) {
            set_config('visitormode', $mode, 'local_supportdesk');
            $this->assertSame($mode, visitor_mode::selected());
            if ($mode !== 'anonymous') {$this->assertFalse(public_intake::enabled());}
        }
        set_config('visitormode', 'unexpected', 'local_supportdesk');
        $this->assertSame('login', visitor_mode::selected());
    }
    public function test_unavailable_integration_keeps_stored_choice_but_displays_login(): void {
        global $CFG;
        $this->resetAfterTest();
        require_once($CFG->libdir . '/adminlib.php');
        set_config('visitormode', 'magiclink', 'local_supportdesk');
        $setting = new \local_supportdesk\local\visitor_mode_setting('local_supportdesk/visitormode',
            'Access', '', 'login', ['login' => 'Sign-in', 'anonymous' => 'Guest ticket']);
        $this->assertFalse(\local_supportdesk\local\visitor_access::is_activated());
        $this->assertSame('login', $setting->get_setting());
        $this->assertSame('magiclink', get_config('local_supportdesk', 'visitormode'));
        $this->assertSame(get_string('invalidaccessmode', 'local_supportdesk'), $setting->write_setting('magiclink'));
        $this->assertSame('magiclink', get_config('local_supportdesk', 'visitormode'));
        set_config('visitormode', 'anonymous', 'local_supportdesk');
        $this->assertSame('anonymous', $setting->get_setting());
    }

    public function test_settings_explain_magic_link_even_when_unavailable(): void {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();
        require_once($CFG->libdir . '/adminlib.php');
        foreach ([false, 2026101705] as $installedversion) {
            if ($installedversion === false || !class_exists('\\auth_magiclink\\local\\magic_link_service')) {
                unset_config('version', 'auth_magiclink');
            } else {
                // A database version alone is not evidence of an installed, usable or activated provider.
                set_config('version', $installedversion, 'auth_magiclink');
            }
            $ADMIN = new \admin_root(true);
            $ADMIN->add('root', new \admin_category('localplugins', 'Local plugins'));
            $hassiteconfig = true;
            include($CFG->dirroot . '/local/supportdesk/settings.php');
            $html = '';
            foreach ($settings->settings as $setting) {
                $html .= $setting->output_html($setting->get_setting());
            }
            if ($installedversion === false || !class_exists('\\auth_magiclink\\local\\magic_link_service')) {
                $this->assertStringNotContainsString('value="magiclink"', $html);
            } else {
                $this->assertStringContainsString('value="magiclink"', $html);
            }
            $this->assertStringContainsString('Magic Link', $html);
            $this->assertStringContainsString('value="login"', $html);
            $this->assertStringContainsString('value="anonymous"', $html);
        }
    }

    public function test_installed_marketplace_provider_can_be_selected_without_activation(): void {
        global $CFG;
        $this->resetAfterTest();
        if (!class_exists('\\auth_magiclink\\local\\licensing\\gate')
                || !\auth_magiclink\local\licensing\gate::allows()) {
            $this->markTestSkipped('This optional integration case requires the activation-free Magic Link distribution.');
        }
        require_once($CFG->libdir . '/adminlib.php');
        set_config('version', 2026010100, 'auth_magiclink');
        set_config('auth', 'manual,magiclink');
        set_config('allownewaccounts', 1, 'auth_magiclink');
        $CFG->authpreventaccountcreation = false;
        $this->assertTrue(\local_supportdesk\local\visitor_access::availability()['ready']);
        $setting = new \local_supportdesk\local\visitor_mode_setting('local_supportdesk/visitormode',
            'Access', '', 'login', ['login' => 'Sign-in', 'magiclink' => 'Magic Link']);
        $this->assertSame('', $setting->write_setting('magiclink'));
        $this->assertSame('magiclink', $setting->get_setting());
        $this->assertTrue(\local_supportdesk\local\visitor_access::enabled());

        set_config('allownewaccounts', 0, 'auth_magiclink');
        $this->assertFalse(\local_supportdesk\local\visitor_access::enabled());
        $this->assertSame('visitor_newaccountsdisabled',
            \local_supportdesk\local\visitor_access::availability()['reason']);
        $this->assertSame('login', $setting->get_setting());
        $this->assertSame('magiclink', get_config('local_supportdesk', 'visitormode'));
        $this->assertNotSame('', $setting->write_setting('magiclink'));

        set_config('allownewaccounts', 1, 'auth_magiclink');
        $CFG->authpreventaccountcreation = true;
        $this->assertFalse(\local_supportdesk\local\visitor_access::availability()['ready']);
        $CFG->authpreventaccountcreation = false;
        set_config('auth', 'manual');
        $this->assertSame('visitor_authdisabled',
            \local_supportdesk\local\visitor_access::availability()['reason']);
        $this->assertFalse(\local_supportdesk\local\visitor_access::enabled());
    }
}
