<?php
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk\local;
defined('MOODLE_INTERNAL') || die();

/** Validate raw multipart uploads before creating tickets or replies. */
class uploads {
    /** Dedicated microphone submissions must obey the setting on the server as well. */
    public static function validate_voice(array $upload): void {
        if ($upload && (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE || !empty($upload['name']))
                && !get_config('local_supportdesk', 'allowvoicenotes')) {
            throw new \moodle_exception('voicenotes_disabled', 'local_supportdesk');
        }
        self::validate($upload);
    }

    public static function validate(array $upload, bool $multiple = false): void {
        global $CFG;
        if (!$upload) {return;}
        $names = $multiple ? ($upload['name'] ?? []) : [$upload['name'] ?? ''];
        $errors = $multiple ? ($upload['error'] ?? []) : [$upload['error'] ?? UPLOAD_ERR_NO_FILE];
        $sizes = $multiple ? ($upload['size'] ?? []) : [$upload['size'] ?? 0];
        $paths = $multiple ? ($upload['tmp_name'] ?? []) : [$upload['tmp_name'] ?? ''];
        if (!is_array($names) || !is_array($errors) || !is_array($sizes) || !is_array($paths) || count($names) > 10) {
            throw new \invalid_parameter_exception('Invalid upload or more than 10 attachments');
        }
        $maxbytes = min(5 * 1024 * 1024, get_max_upload_file_size($CFG->maxbytes ?? 0));
        foreach ($names as $i => $name) {
            $error = $errors[$i] ?? UPLOAD_ERR_NO_FILE;
            if ($error === UPLOAD_ERR_NO_FILE) {continue;}
            if (!is_string($name) || clean_param($name, PARAM_FILE) === '' || $error !== UPLOAD_ERR_OK
                    || !isset($sizes[$i], $paths[$i]) || $sizes[$i] > $maxbytes || !is_uploaded_file($paths[$i])) {
                throw new \invalid_parameter_exception('Invalid upload, upload failure, or attachment exceeds 5 MB/site limit');
            }
        }
    }
}
