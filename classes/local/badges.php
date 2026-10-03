<?php
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.
namespace local_supportdesk\local;
defined('MOODLE_INTERNAL') || die();

/** Semantic badges and validated department colours with readable text. */
class badges {
    public static function validate_color(string $color): string {
        if (!preg_match('/^#[0-9a-fA-F]{6}$/D', $color)) {
            throw new \moodle_exception('invalidbadgecolor', 'local_supportdesk');
        }
        return strtolower($color);
    }
    public static function department(string $label, string $color = '#64748b'): array {
        $color = self::validate_color($color);
        $luminance = 0;
        foreach ([0.2126, 0.7152, 0.0722] as $i => $weight) {
            $channel = hexdec(substr($color, 1 + $i * 2, 2)) / 255;
            $luminance += $weight * ($channel <= 0.04045 ? $channel / 12.92 : (($channel + 0.055) / 1.055) ** 2.4);
        }
        $text = $luminance > 0.179 ? '#000000' : '#ffffff';
        return ['label' => $label, 'class' => 'supportdesk-badge-department',
            'style' => 'background-color:' . $color . ';color:' . $text . ';border-color:' . $color];
    }
    public static function status(string $status): array {
        $styles = ['open' => ['info', 'fa-inbox'], 'in progress' => ['warning', 'fa-clock-o'],
            'admin reply' => ['purple', 'fa-comment'], 'student reply' => ['orange', 'fa-reply'],
            'closed' => ['success', 'fa-check-circle']];
        access::validate_status($status);
        [$color, $icon] = $styles[$status];
        return ['label' => get_string('status_' . str_replace(' ', '_', $status), 'local_supportdesk'),
            'class' => 'supportdesk-badge-' . $color, 'icon' => $icon];
    }
    public static function priority(string $priority): array {
        $styles = ['low' => ['neutral', 'fa-arrow-down'], 'medium' => ['info', 'fa-minus'],
            'high' => ['warning', 'fa-arrow-up'], 'urgent' => ['danger', 'fa-exclamation-circle']];
        access::validate_priority($priority);
        [$color, $icon] = $styles[$priority];
        return ['label' => get_string('priority_' . $priority, 'local_supportdesk'),
            'class' => 'supportdesk-badge-' . $color, 'icon' => $icon];
    }
}
