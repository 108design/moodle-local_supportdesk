<?php
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
