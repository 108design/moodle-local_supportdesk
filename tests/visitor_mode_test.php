<?php
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
}
