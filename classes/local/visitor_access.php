<?php
// SPDX-License-Identifier: GPL-3.0-or-later; Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk\local;
defined('MOODLE_INTERNAL') || die();

/** Optional verified-email entry; authentication and accounts remain owned by auth_magiclink. */
class visitor_access {
    public static function availability(): array {
        global $CFG;
        $installed = (bool)get_config('auth_magiclink', 'version')
            && class_exists('\\auth_magiclink\\local\\magic_link_service')
            && class_exists('\\auth_magiclink\\local\\licensing\\gate');
        $reason = 'visitor_missing';
        if ($installed) {
            $reason = 'visitor_authdisabled';
            if (is_enabled_auth('magiclink')) {
                $reason = 'visitor_activationrequired';
                try {
                    if (\auth_magiclink\local\licensing\gate::allows()) {
                        $newaccounts = get_config('auth_magiclink', 'allownewaccounts');
                        $reason = empty($CFG->authpreventaccountcreation) && ($newaccounts === false || !empty($newaccounts))
                            ? '' : 'visitor_newaccountsdisabled';
                    }
                } catch (\Throwable $e) {
                    $reason = 'visitor_unavailable';
                }
            }
        }
        return ['installed' => $installed, 'ready' => $reason === '', 'reason' => $reason];
    }

    public static function enabled(): bool {
        return get_config('local_supportdesk', 'enabled') !== '0'
            && visitor_mode::selected() === 'magiclink'
            && static::availability()['ready'];
    }

    /** Allow only read-only Supportdesk destinations; discard action/query payloads. */
    public static function target(string $raw): \moodle_url {
        $parts = parse_url($raw);
        if (!$parts || isset($parts['host']) || isset($parts['scheme']) || str_contains($raw, '\\')) {
            return new \moodle_url('/local/supportdesk/add.php');
        }
        $base = '/local/supportdesk/';
        $path = $parts['path'] ?? '';
        if ($path === $base . 'index.php' || $path === $base . 'add.php') {return new \moodle_url($path);}
        if ($path === $base . 'view.php') {
            parse_str($parts['query'] ?? '', $query);
            if (isset($query['id']) && is_string($query['id']) && ctype_digit($query['id']) && (int)$query['id'] > 0) {
                return new \moodle_url($path, ['id' => (int)$query['id']]);
            }
        }
        return new \moodle_url('/local/supportdesk/add.php');
    }

    public static function require_user(\moodle_url $target): void {
        access::require_enabled();
        if ((!isloggedin() || isguestuser()) && public_intake::enabled()) {
            if (basename($target->get_path()) === 'add.php') {
                redirect(new \moodle_url('/local/supportdesk/public.php'));
            }
            redirect(new \moodle_url('/local/supportdesk/entry.php', ['next' => $target->out_as_local_url(false)]));
        }
        if ((!isloggedin() || isguestuser()) && static::enabled()) {
            redirect(new \moodle_url('/local/supportdesk/entry.php', ['next' => $target->out_as_local_url(false)]));
        }
        require_login(null, false);
    }
}
