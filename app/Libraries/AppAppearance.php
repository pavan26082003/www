<?php

namespace App\Libraries;

/**
 * Account Profile → Appearance preference.
 *
 * Single resolver for Home, Profile, GRID, IoT, and Device UI SDUI surfaces. Values:
 *   light | dark | system
 *
 * Default is system (follow phone) until the user saves a choice in settings.
 * Mobile clients must:
 *   light  → force light (ignore OS)
 *   dark   → force dark (ignore OS)
 *   system → follow phone OS
 * Never collapse system to light/dark on the server.
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

        $fromHome = self::fromAppHomeLayout($userId);
        if ($fromHome !== null) {
            return $fromHome;
        }

        return 'system';
    }

    /**
     * Persist account appearance for mobile schemas.
     *
     * @param mixed $value
     */
    public static function save(int $userId, $value): bool
    {
        if ($userId <= 0) {
            return false;
        }

        $mode = self::normalize($value);
        $ok = false;

        if (self::writeUsersTable($userId, $mode)) {
            $ok = true;
        }
        if (self::writeUserSettings($userId, $mode)) {
            $ok = true;
        }

        // App Home layout.profilePage.appearance is written by the caller
        // (web Profile settings / profile-preferences) to avoid save recursion.
        return $ok;
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

    /**
     * App Home Profile web setting (layout.profilePage.appearance).
     *
     * @return string|null
     */
    private static function fromAppHomeLayout(int $userId): ?string
    {
        try {
            if (!class_exists(\App\Models\AppHomePageModel::class)) {
                return null;
            }
            $model = new \App\Models\AppHomePageModel();
            $layout = $model->getLayout($userId);
            if (!is_array($layout) || empty($layout['profilePage']) || !is_array($layout['profilePage'])) {
                return null;
            }
            if (!array_key_exists('appearance', $layout['profilePage'])) {
                return null;
            }
            $raw = $layout['profilePage']['appearance'];
            if ($raw === null || $raw === '') {
                return null;
            }
            return self::normalize($raw);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function writeUsersTable(int $userId, string $mode): bool
    {
        if (!class_exists(\Config\Database::class)) {
            return false;
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
                return false;
            }
            foreach ($candidates as $col) {
                if (!$db->fieldExists($col, 'users')) {
                    continue;
                }
                $db->table('users')->where('id', $userId)->update(array($col => $mode));
                return true;
            }
        } catch (\Throwable $e) {
            return false;
        }
        return false;
    }

    private static function writeUserSettings(int $userId, string $mode): bool
    {
        if (!class_exists(\Config\Database::class)) {
            return false;
        }
        $tables = array(
            array('table' => 'user_settings', 'user' => 'user_id', 'key' => 'setting_name', 'value' => 'setting_value'),
            array('table' => 'user_meta', 'user' => 'user_id', 'key' => 'meta_key', 'value' => 'meta_value'),
            array('table' => 'users_settings', 'user' => 'user_id', 'key' => 'setting_name', 'value' => 'setting_value'),
        );
        try {
            $db = \Config\Database::connect('default', false);
            foreach ($tables as $spec) {
                if (!$db->tableExists($spec['table'])) {
                    continue;
                }
                $existing = $db->table($spec['table'])
                    ->where($spec['user'], $userId)
                    ->where($spec['key'], 'appearance')
                    ->get()
                    ->getRowArray();
                if (is_array($existing)) {
                    $db->table($spec['table'])
                        ->where($spec['user'], $userId)
                        ->where($spec['key'], 'appearance')
                        ->update(array($spec['value'] => $mode));
                } else {
                    $db->table($spec['table'])->insert(array(
                        $spec['user'] => $userId,
                        $spec['key'] => 'appearance',
                        $spec['value'] => $mode,
                    ));
                }
                return true;
            }
        } catch (\Throwable $e) {
            return false;
        }
        return false;
    }

}
