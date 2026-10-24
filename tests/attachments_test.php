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
 * File documentation for tests/attachments_test.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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
