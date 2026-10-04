<?php
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk;
defined('MOODLE_INTERNAL') || die();

/** Only valid raster files become images; preview URLs keep their original access boundary. */
final class attachments_test extends \advanced_testcase {
    public function test_original_and_reply_images_keep_protected_urls(): void {
        $this->resetAfterTest();
        foreach (['attachment', 'reply_attachment'] as $area) {
            $file = get_file_storage()->create_file_from_string([
                'contextid' => \context_system::instance()->id, 'component' => 'local_supportdesk',
                'filearea' => $area, 'itemid' => 42, 'filepath' => '/', 'filename' => 'screenshot.png',
            ], base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aSUQAAAAASUVORK5CYII='));
            $data = \local_supportdesk\local\attachments::export($file);
            $this->assertTrue($data['is_image']);
            $this->assertEquals(1, $data['width']);
            $this->assertStringContainsString('/local_supportdesk/' . $area . '/42/', $data['url']);
            $this->assertStringContainsString('preview=thumb', $data['thumbnail_url']);
            $this->assertStringContainsString('forcedownload=1', $data['download_url']);
        }
    }

    public function test_active_or_invalid_images_are_downloads(): void {
        $this->resetAfterTest();
        foreach (['danger.svg' => '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>',
                'broken.png' => 'Not image data', 'document.html' => '<script>alert(1)</script>'] as $name => $body) {
            $file = get_file_storage()->create_file_from_string([
                'contextid' => \context_system::instance()->id, 'component' => 'local_supportdesk',
                'filearea' => 'attachment', 'itemid' => 42, 'filepath' => '/', 'filename' => $name,
            ], $body);
            $data = \local_supportdesk\local\attachments::export($file);
            $this->assertFalse($data['is_image']);
            $this->assertEmpty($data['thumbnail_url']);
        }
    }
}
