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
 * File documentation for classes/local/attachments.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk\local;
defined('MOODLE_INTERNAL') || die();

/** Presentation metadata; all URLs use the existing authorised pluginfile route. */
class attachments {
    public static function export(\stored_file $file): array {
        $url = \moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(),
            $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        $mime = $file->get_mimetype();
        $safeimage = in_array($mime, ['image/png', 'image/jpeg', 'image/gif', 'image/webp'], true);
        $info = $safeimage ? $file->get_imageinfo() : false;
        $isimage = $info && !empty($info['width']) && !empty($info['height']);
        return [
            'name' => $file->get_filename(),
            'url' => $url->out(false),
            'download_url' => $url->out(false, ['forcedownload' => 1]),
            'thumbnail_url' => $isimage ? $url->out(false, ['preview' => 'thumb']) : '',
            'width' => $isimage ? $info['width'] : 0,
            'height' => $isimage ? $info['height'] : 0,
            'is_image' => (bool)$isimage,
            // Moodle may identify a microphone-only WebM container as video/webm.
            'is_audio' => strpos($mime, 'audio/') === 0 || in_array(strtolower(pathinfo(
                $file->get_filename(), PATHINFO_EXTENSION)), ['mp3', 'wav', 'ogg', 'webm', 'm4a'], true),
        ];
    }
}
