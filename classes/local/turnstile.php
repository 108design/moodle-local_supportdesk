<?php
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk\local;
defined('MOODLE_INTERNAL') || die();

/** Server-side, fail-closed Cloudflare verification. No visitor content is sent. */
class turnstile {
    public static function configured(): bool {
        return trim((string)get_config('local_supportdesk', 'turnstilesitekey')) !== ''
            && trim((string)get_config('local_supportdesk', 'turnstilesecret')) !== '';
    }
    public static function verify(string $token): void {
        global $CFG;
        if (!self::configured() || $token === '' || strlen($token) > 2048) {
            throw new \moodle_exception('public_verification_failed', 'local_supportdesk');
        }
        try {$result = static::request($token);} catch (\Throwable $e) {$result = [];}
        $host = strtolower((string)parse_url($CFG->wwwroot, PHP_URL_HOST));
        if (($result['success'] ?? false) !== true || !is_string($result['hostname'] ?? null)
                || strtolower($result['hostname']) !== $host
                || ($result['action'] ?? '') !== 'supportdesk_create') {
            throw new \moodle_exception('public_verification_failed', 'local_supportdesk');
        }
    }
    protected static function request(string $token): array {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        $curl = new \curl();
        $body = $curl->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => trim((string)get_config('local_supportdesk', 'turnstilesecret')), 'response' => $token,
        ], ['CURLOPT_CONNECTTIMEOUT' => 5, 'CURLOPT_TIMEOUT' => 10]);
        if ($curl->get_errno() || (int)($curl->get_info()['http_code'] ?? 0) !== 200) {return [];}
        $result = json_decode($body, true);
        return is_array($result) ? $result : [];
    }
}
