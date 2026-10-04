<?php
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk;
defined('MOODLE_INTERNAL') || die();

/** Microphone uploads are opt-in, including crafted multipart requests. */
final class uploads_test extends \advanced_testcase {
    public function test_missing_setting_rejects_a_voice_submission(): void {
        $this->resetAfterTest();
        unset_config('allowvoicenotes', 'local_supportdesk');
        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('voicenotes_disabled', 'local_supportdesk'));
        \local_supportdesk\local\uploads::validate_voice(['name' => 'recording.webm', 'error' => UPLOAD_ERR_OK]);
    }
    public function test_no_voice_file_remains_optional_when_disabled(): void {
        $this->resetAfterTest();
        set_config('allowvoicenotes', 0, 'local_supportdesk');
        \local_supportdesk\local\uploads::validate_voice([]);
        \local_supportdesk\local\uploads::validate_voice(['name' => '', 'error' => UPLOAD_ERR_NO_FILE]);
        $this->assertFalse((bool)get_config('local_supportdesk', 'allowvoicenotes'));
    }
    public function test_enabled_recording_still_requires_a_valid_upload(): void {
        $this->resetAfterTest();
        set_config('allowvoicenotes', 1, 'local_supportdesk');
        $this->expectException(\invalid_parameter_exception::class);
        \local_supportdesk\local\uploads::validate_voice(['name' => 'recording.webm', 'error' => UPLOAD_ERR_OK,
            'size' => 1, 'tmp_name' => '/does-not-exist']);
    }
}
