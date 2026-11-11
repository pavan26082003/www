<?php

namespace App\Libraries;

/**
 * Account Profile → Appearance preference.
 *
 * Single resolver for GRID and other SDUI surfaces. Values:
 *   light | dark | system
 *
 * Mobile GRID must follow this value via schema theme.defaultMode.
 * The GRID Web Dashboard may keep a local preview override and must not
 * write back through this resolver unless a dedicated save API exists.
 */
class AppAppearance
{
    /**
     * @return string 'light'|'dark'|'system'
     */
    public static function resolve(int $userId): string
    {
        if ($userId <= 0) {
            return 'system';
        }

        $fromUsers = self::fromUsersTable($userId);
        if ($fromUsers !== null) {
            return $fromUsers;
        }

        $fromSettings = self::fromUserSettings($userId);
        if ($fromSettings !== null) {
            return $fromSettings;
        }

        return 'system';
    }

    /**
     * @param mixed $value
     * @return string 'light'|'dark'|'system'
     */
    public static function normalize($value): string
    {
        $v = strtolower(trim((string) $value));
        $v = str_replace(array('_', '-'), ' ', $v);
        $v = preg_replace('/\s+/', ' ', $v) ?? $v;
        if ($v === 'dark' || $v === 'dark mode' || $v === 'night') {
            return 'dark';
        }
        if ($v === 'light' || $v === 'light mode' || $v === 'day') {
            return 'light';
        }
        // System Default / auto / device — clients must follow OS brightness.
        // Never collapse this to light or dark on the server.
        if (
            $v === ''
            || $v === 'system'
            || $v === 'system default'
            || $v === 'default'
            || $v === 'auto'
            || $v === 'device'
            || $v === 'follow system'
            || $v === 'follow device'
        ) {
            return 'system';
        }
        return 'system';
    }

    /**
     * @return string|null
     */
    private static function fromUsersTable(int $userId): ?string
    {
        if (!class_exists(\Config\Database::class)) {
            return null;
        }
        $candidates = array(
            'appearance',
            'theme_mode',
            'theme_preference',
            'color_scheme',
            'ui_appearance',
            'grid_appearance',
        );
        try {
            $db = \Config\Database::connect('default', false);
            if (!$db->tableExists('users')) {
                return null;
            }
            foreach ($candidates as $col) {
                if (!$db->fieldExists($col, 'users')) {
                    continue;
                }
                $row = $db->table('users')->select($col)->where('id', $userId)->get()->getRowArray();
                if (!is_array($row) || !array_key_exists($col, $row)) {
                    continue;
                }
                $raw = $row[$col];
                if ($raw === null || $raw === '') {
                    continue;
                }
                return self::normalize($raw);
            }
        } catch (\Throwable $e) {
            return null;
        }
        return null;
    }

    /**
     * Optional key/value user settings tables used by some account builds.
     *
     * @return string|null
     */
    private static function fromUserSettings(int $userId): ?string
    {
        if (!class_exists(\Config\Database::class)) {
            return null;
        }
        $tables = array(
            array('table' => 'user_settings', 'user' => 'user_id', 'key' => 'setting_name', 'value' => 'setting_value'),
            array('table' => 'user_meta', 'user' => 'user_id', 'key' => 'meta_key', 'value' => 'meta_value'),
            array('table' => 'users_settings', 'user' => 'user_id', 'key' => 'setting_name', 'value' => 'setting_value'),
        );
        $keys = array('appearance', 'theme_mode', 'themeMode', 'theme_preference', 'color_scheme', 'ui_appearance');
        try {
            $db = \Config\Database::connect('default', false);
            foreach ($tables as $spec) {
                if (!$db->tableExists($spec['table'])) {
                    continue;
                }
                foreach ($keys as $key) {
                    $row = $db->table($spec['table'])
                        ->select($spec['value'])
                        ->where($spec['user'], $userId)
                        ->where($spec['key'], $key)
                        ->get()
                        ->getRowArray();
                    if (!is_array($row) || !array_key_exists($spec['value'], $row)) {
                        continue;
                    }
                    $raw = $row[$spec['value']];
                    if ($raw === null || $raw === '') {
                        continue;
                    }
                    return self::normalize($raw);
                }
            }
        } catch (\Throwable $e) {
            return null;
        }
        return null;
    }
}
