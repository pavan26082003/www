<?php

namespace App\Libraries;

/**
 * Standalone Grid network page SDUI schema (not part of App Home get_schema).
 * Flutter / studio fetch this via grid_api_creation.php?action=get_grid.
 */
class GridSduiSchemaBuilder
{
    public const GRID_LOGO_URL = 'https://efactor.tech/assets/images/GRID_Logo.png';

    private static function publicOrigin(): string
    {
        $host = trim((string) ($_SERVER['HTTP_HOST'] ?? ''));
        if ($host === '') {
            $host = 'efactor.tech';
        }
        $hostName = strtolower((string) preg_replace('/:\d+$/', '', $host));
        // Mobile blocks cleartext http:// for QR/logo assets — always https off localhost.
        if (!in_array($hostName, array('localhost', '127.0.0.1', '::1'), true)) {
            return 'https://' . $host;
        }
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443)
            || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
        return ($https ? 'https://' : 'http://') . $host;
    }

    private static function qrImageEndpoint(): string
    {
        return rtrim(self::publicOrigin(), '/') . '/v8/qr_image.php';
    }

    private static function qrImageUrlTemplate(): string
    {
        return self::qrImageEndpoint() . '?data={data}&mode={mode}&size=200';
    }

    private static function qrImageUrl($payload, $mode = ''): string
    {
        $payload = trim((string) $payload);
        $mode = trim((string) $mode);
        $query = 'data=' . rawurlencode($payload) . '&size=200';
        if ($mode !== '') {
            $query .= '&mode=' . rawurlencode($mode);
        }
        return self::qrImageEndpoint() . '?' . $query;
    }

    private static function initialsImageUrl(string $initials, string $color): string
    {
        $letters = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $initials) ?: 'S', 0, 2));
        $hex = strtolower((string) preg_replace('/[^a-f0-9]/i', '', ltrim($color, '#')));
        if (strlen($hex) !== 6) {
            $hex = '5b6cff';
        }
        return rtrim(self::publicOrigin(), '/') . '/v8/grid_initials.php?t=' . rawurlencode($letters) . '&c=' . rawurlencode($hex);
    }

    /**
     * Scan destination for one profile. Same shareQr fields; only the URL value changes.
     * Professional: /grid/connect?mode=professional&grid_id={their grid id}&user_id={their user id}
     * Circle: /grid-chat/connect?mode=circle&grid_id={their grid id}&user_id={their user id}
     * Ids come from the signed-in account. Example ids are never written in.
     * Share QR is scanned by other phones — never encode localhost/loopback.
     */
    private static function connectUrl(string $mode, int $userId, string $gridId = ''): string
    {
        $mode = $mode === 'circle' ? 'circle' : 'professional';
        $gridId = trim($gridId);
        $path = $mode === 'circle' ? 'grid-chat/connect' : 'grid/connect';
        $base = class_exists(GridWebSso::class)
            ? GridWebSso::publicWebUrl($path)
            : 'https://efactor.tech/index.php/' . $path;
        $host = strtolower((string) (parse_url($base, PHP_URL_HOST) ?: ''));
        if ($host === '' || in_array($host, array('localhost', '127.0.0.1', '::1'), true)) {
            $base = 'https://efactor.tech/index.php/' . $path;
        }
        $query = 'mode=' . rawurlencode($mode);
        if ($gridId !== '') {
            $query .= '&grid_id=' . rawurlencode($gridId);
        }
        if ($userId > 0) {
            $query .= '&user_id=' . $userId;
        }
        return $base . '?' . $query;
    }

    /**
     * Public grid id for this account. Reads users.grid_id, then grid_profiles.
     * If the account has none yet, stores one new id on that user row.
     */
    public static function resolveGridId(int $userId): string
    {
        if ($userId <= 0) {
            return '';
        }
        $gridId = self::resolveGridIdFromCi($userId);
        if ($gridId === '') {
            $gridId = self::resolveGridIdFromMysqli($userId);
        }
        if ($gridId === '') {
            $gridId = self::assignGridId($userId);
        }
        return $gridId;
    }

    private static function resolveGridIdFromCi(int $userId): string
    {
        if (!class_exists(\Config\Database::class)) {
            return '';
        }
        foreach (array('default', 'grid') as $group) {
            try {
                $db = \Config\Database::connect($group, false);
            } catch (\Throwable $e) {
                continue;
            }
            $gridId = self::readGridIdFromConnection($db, $userId);
            if ($gridId !== '') {
                return $gridId;
            }
        }
        return '';
    }

    private static function readGridIdFromConnection($db, int $userId): string
    {
        try {
            $tables = array('users');
            try {
                $prefixed = (string) $db->prefixTable('users');
                if ($prefixed !== '' && $prefixed !== 'users') {
                    array_unshift($tables, $prefixed);
                }
            } catch (\Throwable $e) {
            }
            foreach ($tables as $table) {
                if (!$db->tableExists($table) || !$db->fieldExists('grid_id', $table)) {
                    continue;
                }
                $row = $db->table($table)->select('grid_id')->where('id', $userId)->get()->getRowArray();
                $gridId = trim((string) ($row['grid_id'] ?? ''));
                if ($gridId !== '') {
                    return $gridId;
                }
            }
            if ($db->tableExists('grid_profiles') && $db->fieldExists('grid_id', 'grid_profiles')) {
                $row = $db->table('grid_profiles')->select('grid_id')->where('user_id', $userId)->get()->getRowArray();
                return trim((string) ($row['grid_id'] ?? ''));
            }
        } catch (\Throwable $e) {
            return '';
        }
        return '';
    }

    private static function assignGridId(int $userId): string
    {
        $gridId = self::assignGridIdFromCi($userId);
        if ($gridId !== '') {
            return $gridId;
        }
        return self::assignGridIdFromMysqli($userId);
    }

    private static function assignGridIdFromCi(int $userId): string
    {
        if (!class_exists(\Config\Database::class)) {
            return '';
        }
        try {
            $db = \Config\Database::connect('default');
            if (!$db->tableExists('users')) {
                return '';
            }
            if (!$db->fieldExists('grid_id', 'users')) {
                $db->query('ALTER TABLE `users` ADD COLUMN `grid_id` VARCHAR(48) NULL');
            }
            $row = $db->query('SELECT grid_id FROM users WHERE id = ? LIMIT 1', array($userId))->getRowArray();
            $existing = trim((string) ($row['grid_id'] ?? ''));
            if ($existing !== '') {
                return $existing;
            }
            for ($attempt = 0; $attempt < 5; $attempt++) {
                $candidate = strtoupper(bin2hex(random_bytes(10)));
                $taken = $db->query('SELECT id FROM users WHERE grid_id = ? LIMIT 1', array($candidate))->getRowArray();
                if ($taken) {
                    continue;
                }
                $db->query(
                    "UPDATE users SET grid_id = ? WHERE id = ? AND (grid_id IS NULL OR grid_id = '')",
                    array($candidate, $userId)
                );
                $saved = $db->query('SELECT grid_id FROM users WHERE id = ? LIMIT 1', array($userId))->getRowArray();
                $gridId = trim((string) ($saved['grid_id'] ?? ''));
                if ($gridId !== '') {
                    return $gridId;
                }
            }
        } catch (\Throwable $e) {
            return '';
        }
        return '';
    }

    private static function resolveGridIdFromMysqli(int $userId): string
    {
        $db = self::openMysqli();
        if (!$db) {
            return '';
        }
        $gridId = '';
        if (self::mysqliHasColumn($db, 'users', 'grid_id')) {
            $gridId = self::mysqliGridId($db, 'SELECT grid_id FROM users WHERE id = ? LIMIT 1', $userId);
        }
        if ($gridId === '' && self::mysqliHasColumn($db, 'grid_profiles', 'grid_id')) {
            $gridId = self::mysqliGridId($db, 'SELECT grid_id FROM grid_profiles WHERE user_id = ? LIMIT 1', $userId);
        }
        $db->close();
        return $gridId;
    }

    private static function assignGridIdFromMysqli(int $userId): string
    {
        $db = self::openMysqli();
        if (!$db) {
            return '';
        }
        if (!self::mysqliHasColumn($db, 'users', 'grid_id')) {
            $db->query('ALTER TABLE `users` ADD COLUMN `grid_id` VARCHAR(48) NULL');
        }
        if (!self::mysqliHasColumn($db, 'users', 'grid_id')) {
            $db->close();
            return '';
        }
        $existing = self::mysqliGridId($db, 'SELECT grid_id FROM users WHERE id = ? LIMIT 1', $userId);
        if ($existing !== '') {
            $db->close();
            return $existing;
        }
        $gridId = '';
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $candidate = strtoupper(bin2hex(random_bytes(10)));
            $taken = self::mysqliGridId($db, 'SELECT grid_id FROM users WHERE grid_id = ? LIMIT 1', 0, $candidate);
            if ($taken !== '') {
                continue;
            }
            $stmt = $db->prepare("UPDATE users SET grid_id = ? WHERE id = ? AND (grid_id IS NULL OR grid_id = '')");
            if (!$stmt) {
                break;
            }
            $stmt->bind_param('si', $candidate, $userId);
            $stmt->execute();
            $stmt->close();
            $gridId = self::mysqliGridId($db, 'SELECT grid_id FROM users WHERE id = ? LIMIT 1', $userId);
            if ($gridId !== '') {
                break;
            }
        }
        $db->close();
        return $gridId;
    }

    private static function openMysqli(): ?\mysqli
    {
        if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_NAME')) {
            return null;
        }
        $db = @new \mysqli(
            (string) DB_HOST,
            (string) DB_USER,
            defined('DB_PASS') ? (string) DB_PASS : '',
            (string) DB_NAME
        );
        if ($db->connect_errno) {
            return null;
        }
        $db->set_charset('utf8mb4');
        return $db;
    }

    private static function mysqliHasColumn(\mysqli $db, string $table, string $column): bool
    {
        $table = str_replace('`', '', $table);
        $column = $db->real_escape_string(str_replace('`', '', $column));
        $result = $db->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
        if (!$result) {
            return false;
        }
        $ok = $result->num_rows > 0;
        $result->free();
        return $ok;
    }

    private static function mysqliGridId(\mysqli $db, string $sql, int $userId, string $gridId = ''): string
    {
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return '';
        }
        if ($gridId !== '') {
            $stmt->bind_param('s', $gridId);
        } else {
            $stmt->bind_param('i', $userId);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        if (!is_array($row)) {
            return '';
        }
        return trim((string) ($row['grid_id'] ?? ''));
    }

    /**
     * Header photo for this account. grid_profiles.avatar when it is set,
     * otherwise the account photo in users.image. The letter stays only when neither exists.
     *
     * @return array{initial:string,imageUrl:string}
     */
    private static function resolveHeaderProfile(int $userId): array
    {
        $empty = array('initial' => '', 'imageUrl' => '');
        if ($userId <= 0) {
            return $empty;
        }
        $row = self::readHeaderProfileFromCi($userId);
        if ($row === null) {
            $row = self::readHeaderProfileFromMysqli($userId);
        }
        if (!is_array($row)) {
            return $empty;
        }

        $first = trim((string) ($row['first_name'] ?? ''));
        $last = trim((string) ($row['last_name'] ?? ''));
        $initial = '';
        if ($first !== '') {
            $initial = strtoupper(substr($first, 0, 1));
        } elseif ($last !== '') {
            $initial = strtoupper(substr($last, 0, 1));
        }

        // GRID profile photo first. The account photo in users.image is the
        // picture already stored for this user when GRID has not saved one.
        $imageUrl = self::storedPhotoUrl((string) ($row['grid_avatar'] ?? ''));
        if ($imageUrl === '') {
            $imageUrl = self::storedPhotoUrl((string) ($row['image'] ?? ''));
        }

        return array(
            'initial' => $initial,
            'imageUrl' => $imageUrl,
        );
    }

    private static function readHeaderProfileFromCi(int $userId): ?array
    {
        if (!class_exists(\Config\Database::class)) {
            return null;
        }
        try {
            $db = \Config\Database::connect('default', false);
            if (!$db->tableExists('users')) {
                return null;
            }
            $row = $db->table('users')->select('first_name, last_name, image')->where('id', $userId)->get()->getRowArray();
            if (!is_array($row)) {
                return null;
            }
            $row['grid_avatar'] = '';
            // GRID profiles live in the dedicated `grid` connection, not the
            // account database. Reading the default connection could show a
            // stale or unrelated account image.
            try {
                $gridDb = \Config\Database::connect('grid', false);
                if ($gridDb->tableExists('grid_profiles') && $gridDb->fieldExists('avatar', 'grid_profiles')) {
                    $profile = $gridDb->table('grid_profiles')->select('avatar')->where('user_id', $userId)->get()->getRowArray();
                    $row['grid_avatar'] = trim((string) ($profile['avatar'] ?? ''));
                }
            } catch (\Throwable $e) {
                // users.image below remains the safe account-level fallback.
            }
            return $row;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function readHeaderProfileFromMysqli(int $userId): ?array
    {
        $db = self::openMysqli();
        if (!$db || !self::mysqliHasColumn($db, 'users', 'image')) {
            if ($db) {
                $db->close();
            }
            return null;
        }
        $stmt = $db->prepare('SELECT first_name, last_name, image FROM users WHERE id = ? LIMIT 1');
        if (!$stmt) {
            $db->close();
            return null;
        }
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        if (!is_array($row)) {
            $db->close();
            return null;
        }
        $row['grid_avatar'] = '';
        if (self::mysqliHasColumn($db, 'grid_profiles', 'avatar') && self::mysqliHasColumn($db, 'grid_profiles', 'user_id')) {
            $profileStmt = $db->prepare('SELECT avatar FROM grid_profiles WHERE user_id = ? LIMIT 1');
            if ($profileStmt) {
                $profileStmt->bind_param('i', $userId);
                $profileStmt->execute();
                $profileResult = $profileStmt->get_result();
                $profile = $profileResult ? $profileResult->fetch_assoc() : null;
                $profileStmt->close();
                if (is_array($profile)) {
                    $row['grid_avatar'] = trim((string) ($profile['avatar'] ?? ''));
                }
            }
        }
        $db->close();
        return $row;
    }

    public static function liveHeaderProfile(int $userId): array
    {
        return self::resolveHeaderProfile($userId);
    }

    private static function storedPhotoUrl(string $photo): string
    {
        $photo = trim($photo);
        if ($photo === '' || $photo === 'system_bot') {
            return '';
        }
        if (function_exists('helper')) {
            try {
                helper('general');
            } catch (\Throwable $e) {
            }
        }
        if (function_exists('get_avatar')) {
            $url = trim((string) get_avatar($photo));
            if ($url !== '' && !preg_match('#/assets/images/avatar\.jpg(\?|$)#i', $url)) {
                return self::canonicalizeAvatarUrl($url);
            }
        }
        return self::publicAvatarUrl($photo);
    }

    private static function publicAvatarUrl(string $image): string
    {
        $image = trim($image);
        if ($image === '' || $image === 'system_bot') {
            return '';
        }

        $fileName = '';
        $decoded = @unserialize($image);
        if (is_array($decoded) && !empty($decoded['file_name'])) {
            $fileName = trim((string) $decoded['file_name']);
        }
        if ($fileName === '' && preg_match('#^https?://#i', $image)) {
            return self::canonicalizeAvatarUrl($image);
        }
        if ($fileName === '') {
            $fileName = $image;
        }
        if (preg_match('#^https?://#i', $fileName)) {
            return self::canonicalizeAvatarUrl($fileName);
        }

        $fileName = ltrim(str_replace('\\', '/', $fileName), '/');
        if ($fileName === '' || preg_match('#(^|/)avatar\.jpg$#i', $fileName)) {
            return '';
        }
        if (strpos($fileName, '/') === false) {
            $fileName = 'files/profile_images/' . $fileName;
        }

        return self::canonicalizeAvatarUrl(rtrim(self::publicOrigin(), '/') . '/' . $fileName);
    }

    private static function canonicalizeAvatarUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '' || preg_match('#/assets/images/avatar\.jpg(\?|$)#i', $url)) {
            return '';
        }
        if (class_exists(GridWebSso::class)) {
            return GridWebSso::preferHttps($url);
        }
        return $url;
    }

    private static function fillShareQrImages(array $shareQr, int $userId = 0, string $gridId = ''): array
    {
        $selectedMode = (string) ($shareQr['selectedMode'] ?? 'professional');
        if ($selectedMode !== 'circle') {
            $selectedMode = 'professional';
        }
        $gridId = trim($gridId);
        if ($gridId === '' && $userId > 0) {
            $gridId = self::resolveGridId($userId);
        }
        $shareQr['selectedMode'] = $selectedMode;
        $shareQr['qrPayload'] = self::connectUrl($selectedMode, $userId, $gridId);
        $shareQr['qrImageUrlTemplate'] = self::qrImageUrlTemplate();
        $modes = is_array($shareQr['modes'] ?? null) ? $shareQr['modes'] : array();
        $byMode = array();
        foreach ($modes as $mode) {
            if (!is_array($mode)) {
                continue;
            }
            $modeId = trim((string) ($mode['id'] ?? ''));
            if ($modeId !== 'professional' && $modeId !== 'circle') {
                continue;
            }
            $byMode[$modeId] = self::qrImageUrl(self::connectUrl($modeId, $userId, $gridId), $modeId);
        }
        if ($byMode === array()) {
            $byMode[$selectedMode] = self::qrImageUrl($shareQr['qrPayload'], $selectedMode);
        }
        $shareQr['qrImageUrlsByMode'] = $byMode;
        $shareQr['qrImageUrl'] = $byMode[$selectedMode] ?? self::qrImageUrl($shareQr['qrPayload'], $selectedMode);
        // Null, not omitted. The app draws the QR share button when this key is missing.
        $shareQr['shareActionId'] = null;
        unset($shareQr['showShareButton'], $shareQr['actionId']);
        return $shareQr;
    }

    private static function gridLayoutMetrics($columns, $gap, $rowHeight, array $extra = array()): array
    {
        return array_merge(array(
            'columns' => (int) $columns,
            'columnGap' => (int) $gap,
            'rowGap' => (int) $gap,
            'rowHeight' => (int) $rowHeight,
            'minRowHeight' => (int) $rowHeight,
            // 0 lets cells divide the real width. A fixed minimum overflows narrow phones.
            'minimumCellWidthDp' => 0,
            'fit' => 'fill',
            'width' => 'fill',
            'maxWidth' => '100%',
            'minWidth' => 0,
            'allowShrink' => true,
            'overflowX' => 'hidden',
        ), $extra);
    }

    /**
     * Tokens the Flutter avatarButton checker requires:
     * designTokens.colors.surface (or onAccent) and action.fontSize.
     */
    private static function designTokens(): array
    {
        return array(
            'colors' => array(
                'textPrimary' => '#2e1065',
                'textSecondary' => '#6b7280',
                'border' => '#e5e7eb',
                'surface' => '#ffffff',
                'onSurface' => '#111827',
                'onAccent' => '#ffffff',
                'pageBackground' => '#f7f8fc',
                'accent' => '#7c3aed',
            ),
            'action' => array(
                'fontSize' => 15,
                'fontWeight' => 700,
                'lineHeight' => 1.2,
                'color' => '#ffffff',
            ),
            'typography' => array(
                'pageTitle' => array(
                    'size' => 26,
                    'fontSize' => 26,
                    'weight' => 800,
                    'fontWeight' => 800,
                    'letterSpacing' => -0.02,
                    'maxLines' => 1,
                    'overflow' => 'ellipsis',
                ),
                'pageSubtitle' => array(
                    'size' => 13,
                    'fontSize' => 13,
                    'weight' => 500,
                    'fontWeight' => 500,
                    'maxLines' => 2,
                    'overflow' => 'ellipsis',
                ),
                'action' => array(
                    'size' => 15,
                    'fontSize' => 15,
                    'weight' => 700,
                    'fontWeight' => 700,
                    'lineHeight' => 1.2,
                ),
            ),
            'radius' => array(
                'card' => 22,
                'explore' => 18,
                'button' => 999,
            ),
        );
    }

    public static function defaultContent(): array
    {
        $defaults = array(
            'id' => 'gridPage',
            'routeId' => 'grid',
            'logoUrl' => self::GRID_LOGO_URL,
            'header' => array(
                'title' => 'Grid',
                'subtitle' => 'Your professional network space',
                'logoUrl' => self::GRID_LOGO_URL,
                'searchIconToken' => 'scanner',
                'avatarInitial' => 'S',
                'avatarColor' => '#7c3aed',
            ),
            'featureCards' => array(
                array(
                    'id' => 'professional-space',
                    'title' => 'Professional Space',
                    'subtitle' => 'Discover people, opportunities and grow your career.',
                    'iconToken' => 'user',
                    'image' => '',
                    'imageUrl' => '',
                    'url' => '',
                    'route' => '',
                    'color' => '#5b6cff',
                    'gradientStart' => '#dce4ff',
                    'gradientEnd' => '#b7c4fb',
                    'textColor' => '#312e81',
                    'actionId' => 'grid-open-professional-space',
                ),
                array(
                    'id' => 'circle-connect',
                    'title' => 'Circle Connect',
                    'subtitle' => 'Meaningful conversations. Stronger circles.',
                    'iconToken' => 'users',
                    'image' => '',
                    'imageUrl' => '',
                    'url' => '',
                    'route' => '',
                    'color' => '#ea580c',
                    'gradientStart' => '#ffe0cc',
                    'gradientEnd' => '#f4b496',
                    'textColor' => '#9a3412',
                    'actionId' => 'grid-open-circle-connect',
                ),
            ),
            'stats' => array(
                array('id' => 'professionals', 'label' => 'Professionals', 'value' => 248),
                array('id' => 'opportunities', 'label' => 'Opportunities', 'value' => 36),
                array('id' => 'active-circles', 'label' => 'Active Circles', 'value' => 12),
            ),
            'shareQr' => array(
                'title' => 'Share Profile QR',
                'subtitle' => 'Share your professional or circle profile with anyone.',
                'iconToken' => 'grid',
                'selectedMode' => 'professional',
                'modes' => array(
                    array('id' => 'professional', 'label' => 'Professional'),
                    array('id' => 'circle', 'label' => 'Circle'),
                ),
                'qrCaption' => 'Scan to connect',
                'qrPayload' => '',
                'qrImageUrl' => '',
                'qrImageUrlTemplate' => '',
                'qrImageUrlsByMode' => array(),
                'shareActionId' => 'grid-share-qr',
                'selectModeActionId' => 'grid-select-qr-mode',
            ),
            'exploring' => array(
                'title' => 'Recent Activity',
                'seeAllLabel' => 'See all',
                'seeAllActionId' => 'grid-see-all-exploring',
                'items' => array(),
            ),
            'quote' => array(
                'text' => 'Connections turn potential into progress.',
                'iconToken' => 'quote',
            ),
        );
        $defaults['shareQr'] = self::fillShareQrImages($defaults['shareQr']);
        return $defaults;
    }

    public static function mergeContent(?array $override): array
    {
        $defaults = self::defaultContent();
        if (!is_array($override) || $override === array()) {
            return $defaults;
        }

        $merged = array_replace_recursive($defaults, $override);
        if (empty($override['featureCards']) || !is_array($override['featureCards'])) {
            $merged['featureCards'] = $defaults['featureCards'];
        } else {
            $merged['featureCards'] = self::mergeFeatureCardsById(
                $defaults['featureCards'],
                $override['featureCards']
            );
        }
        if (empty($override['stats']) || !is_array($override['stats'])) {
            $merged['stats'] = $defaults['stats'];
        }
        $savedExplore = $override['exploring']['items'] ?? null;
        $savedExploreIsLegacy = false;
        if (is_array($savedExplore)) {
            foreach ($savedExplore as $savedItem) {
                if (!is_array($savedItem)) {
                    continue;
                }
                $savedId = (string) ($savedItem['id'] ?? '');
                if ($savedId === 'find-talent' || $savedId === 'join-circles') {
                    $savedExploreIsLegacy = true;
                    break;
                }
            }
        }
        if ($savedExploreIsLegacy || empty($savedExplore) || !is_array($savedExplore)) {
            $merged['exploring']['title'] = 'Recent Activity';
            $merged['exploring']['items'] = $defaults['exploring']['items'];
        }
        if (empty($override['shareQr']) || !is_array($override['shareQr'])) {
            $merged['shareQr'] = $defaults['shareQr'];
        } else {
            $merged['shareQr'] = array_replace_recursive($defaults['shareQr'], $override['shareQr']);
            if (empty($override['shareQr']['modes']) || !is_array($override['shareQr']['modes'])) {
                $merged['shareQr']['modes'] = $defaults['shareQr']['modes'];
            }
        }
        if (empty($merged['logoUrl'])) {
            $merged['logoUrl'] = self::GRID_LOGO_URL;
        }
        if (empty($merged['header']['logoUrl'])) {
            $merged['header']['logoUrl'] = self::GRID_LOGO_URL;
        }
        $merged['shareQr'] = self::fillShareQrImages(is_array($merged['shareQr'] ?? null) ? $merged['shareQr'] : array(), 0);

        return $merged;
    }

    /**
     * Merge saved feature cards onto defaults by id so open-URLs persist.
     * Icons always come from iconToken (no remote image URLs).
     */
    private static function mergeFeatureCardsById(array $defaults, array $override): array
    {
        $byId = array();
        foreach ($override as $card) {
            if (!is_array($card)) {
                continue;
            }
            $id = (string) ($card['id'] ?? '');
            if ($id === '') {
                continue;
            }
            $byId[$id] = $card;
        }

        $merged = array();
        foreach ($defaults as $defaultCard) {
            if (!is_array($defaultCard)) {
                continue;
            }
            $id = (string) ($defaultCard['id'] ?? '');
            $saved = ($id !== '' && isset($byId[$id]) && is_array($byId[$id])) ? $byId[$id] : array();
            $card = array_replace_recursive($defaultCard, $saved);
            $card = self::normalizeSoftCard($card);
            $merged[] = $card;
            unset($byId[$id]);
        }
        foreach ($byId as $extra) {
            if (is_array($extra)) {
                $merged[] = self::normalizeSoftCard($extra);
            }
        }
        return $merged;
    }

    /**
     * Soft pastel cards (icon chip, title, subtitle, arrow).
     * Migrates Windows Start tiles; never uses image URLs for icons.
     */
    private static function normalizeSoftCard(array $card): array
    {
        $id = (string) ($card['id'] ?? '');

        if ($id === 'professional-space') {
            $card['color'] = '#5b6cff';
            $card['gradientStart'] = '#dce4ff';
            $card['gradientEnd'] = '#b7c4fb';
            $card['textColor'] = '#312e81';
            if (trim((string) ($card['iconToken'] ?? '')) === '') {
                $card['iconToken'] = 'user';
            }
        }
        if ($id === 'circle-connect') {
            $card['color'] = '#ea580c';
            $card['gradientStart'] = '#ffe0cc';
            $card['gradientEnd'] = '#f4b496';
            $card['textColor'] = '#9a3412';
            if (trim((string) ($card['iconToken'] ?? '')) === '') {
                $card['iconToken'] = 'users';
            }
        }

        $card['image'] = '';
        $card['imageUrl'] = '';
        $url = trim((string) ($card['url'] ?? $card['route'] ?? ''));
        $card['url'] = $url;
        $card['route'] = $url;
        return $card;
    }

    public static function buildComponents(array $content, int $userId = 0, string $gridId = ''): array
    {
        $header = is_array($content['header'] ?? null) ? $content['header'] : array();
        $exploring = is_array($content['exploring'] ?? null) ? $content['exploring'] : array();
        $quote = is_array($content['quote'] ?? null) ? $content['quote'] : array();
        $shareQr = is_array($content['shareQr'] ?? null) ? $content['shareQr'] : array();
        $featureCards = is_array($content['featureCards'] ?? null) ? $content['featureCards'] : array();
        $exploreItems = is_array($exploring['items'] ?? null) ? $exploring['items'] : array();
        $logoUrl = (string) ($header['logoUrl'] ?? $content['logoUrl'] ?? self::GRID_LOGO_URL);

        $components = array();
        $featureChildIds = array();
        foreach ($featureCards as $index => $card) {
            if (!is_array($card)) {
                continue;
            }
            $id = 'grid-feature-' . (string) ($card['id'] ?? $index);
            $featureChildIds[] = $id;
            $actionId = $card['actionId'] ?? null;
            $card = self::normalizeSoftCard($card);
            $tileColor = (string) ($card['color'] ?? $card['gradientStart'] ?? '#5b6cff');
            $textColor = (string) ($card['textColor'] ?? '#312e81');
            $url = trim((string) ($card['url'] ?? $card['route'] ?? ''));
            $components[$id] = array(
                'id' => $id,
                'type' => 'featureCard',
                'style' => 'softCard',
                'title' => (string) ($card['title'] ?? ''),
                'subtitle' => (string) ($card['subtitle'] ?? ''),
                'iconToken' => self::knownIconToken($card['iconToken'] ?? 'user', 'user'),
                'actionArrowIconToken' => 'chevron-right',
                'image' => null,
                'imageUrl' => null,
                'color' => $tileColor,
                'gradientStart' => (string) ($card['gradientStart'] ?? $tileColor),
                'gradientEnd' => (string) ($card['gradientEnd'] ?? $tileColor),
                'textColor' => $textColor,
                'url' => $url !== '' ? $url : null,
                'route' => $url !== '' ? $url : null,
                'openIn' => $url !== '' ? 'inAppWebView' : null,
                // Card title stays on the Grid page tile; do not reuse it as WebView chrome.
                'webViewTitle' => '',
                'webView' => $url !== '' ? array(
                    'javaScriptEnabled' => true,
                    'sharedCookies' => true,
                    'clearCache' => false,
                    'showAppBar' => false,
                    'showToolbar' => false,
                    'hideAppBar' => true,
                    'displayMode' => 'fullscreen',
                ) : null,
                'actionId' => $actionId,
                'events' => !empty($actionId) ? array('tap' => array('actionId' => $actionId)) : null,
                'layout' => array(
                    'columnSpan' => 1,
                    'rowSpan' => 1,
                    'minHeight' => 168,
                    'minWidth' => 0,
                    'width' => 'fill',
                    'maxWidth' => '100%',
                ),
                'appearance' => array(
                    'borderRadius' => 24,
                    'backgroundColor' => (string) ($card['gradientStart'] ?? $tileColor),
                    'backgroundGradient' => array(
                        'start' => (string) ($card['gradientStart'] ?? $tileColor),
                        'end' => (string) ($card['gradientEnd'] ?? $tileColor),
                    ),
                    'iconPosition' => 'topLeft',
                    'titlePosition' => 'belowIcon',
                    'showSubtitle' => true,
                    'showActionArrow' => true,
                    'iconSize' => 36,
                    'titleFontSize' => 16,
                    'subtitleFontSize' => 12,
                    'titlePadding' => 12,
                    'titleMaxLines' => 2,
                    'subtitleMaxLines' => 3,
                    'titleOverflow' => 'ellipsis',
                    'subtitleOverflow' => 'ellipsis',
                    'width' => 'fill',
                    'minWidth' => 0,
                ),
            );
        }

        $exploreChildIds = array();
        foreach ($exploreItems as $index => $item) {
            if (!is_array($item)) {
                continue;
            }
            $id = 'grid-explore-' . (string) ($item['id'] ?? $index);
            $exploreChildIds[] = $id;
            $itemUrl = trim((string) ($item['url'] ?? $item['route'] ?? ''));
            $initials = (string) ($item['initials'] ?? '');
            $avatarColor = (string) ($item['avatarColor'] ?? '#5b6cff');
            $tint = (string) ($item['backgroundColor'] ?? '#eef2ff');
            $time = (string) ($item['time'] ?? '');
            $badge = (string) ($item['badge'] ?? '');
            $avatarUrl = self::initialsImageUrl($initials, $avatarColor);
            $avatar = array(
                'type' => 'avatarButton',
                'label' => $initials,
                'initial' => $initials,
                'color' => $avatarColor,
                'backgroundColor' => $avatarColor,
                'foregroundColor' => '#ffffff',
                'imageUrl' => $avatarUrl,
                'fontSize' => 14,
                'appearance' => array(
                    'size' => 42,
                    'width' => 42,
                    'height' => 42,
                    'borderRadius' => 21,
                    'backgroundColor' => $avatarColor,
                    'foregroundColor' => '#ffffff',
                    'fontSize' => 14,
                    'fontWeight' => 700,
                    'imageUrl' => $avatarUrl,
                ),
            );
            // Keep initials only on leading/avatar. Card-level label/initial were
            // concatenated with title+badge into one line (e.g. "MMessagesProfessionalNo").
            $previewText = (string) ($item['preview'] ?? $item['subtitle'] ?? '');
            $kind = strtolower(trim((string) ($item['kind'] ?? 'message')));
            $iconTokenFallback = ($kind === 'call') ? 'phone' : 'message';
            $iconToken = self::knownIconToken($item['iconToken'] ?? $iconTokenFallback, $iconTokenFallback);
            $components[$id] = array(
                'id' => $id,
                'type' => 'featureCard',
                'style' => 'softCard',
                'title' => (string) ($item['title'] ?? ''),
                'subtitle' => implode(' · ', array_values(array_filter(array(
                    $previewText,
                    $time,
                ), function ($part) {
                    return trim((string) $part) !== '';
                }))),
                'label' => '',
                'initial' => '',
                // Top-level iconToken for web/mobile fallback next to avatarButton icon.
                'iconToken' => $iconToken,
                'image' => $avatarUrl,
                'imageUrl' => $avatarUrl,
                'icon' => $avatar,
                'leading' => $avatar,
                'color' => $avatarColor,
                'badge' => $badge,
                'chip' => $badge !== '' ? array('label' => $badge) : null,
                'time' => $time,
                'trailing' => $time !== '' ? array('label' => $time, 'color' => '#94a3b8') : null,
                'initials' => $initials,
                'space' => (string) ($item['space'] ?? ''),
                'preview' => $previewText,
                'unread' => !empty($item['unread']),
                'backgroundColor' => $tint,
                'avatarColor' => $avatarColor,
                'gradientStart' => $tint,
                'gradientEnd' => $tint,
                'textColor' => '#1e1b4b',
                'url' => $itemUrl !== '' ? $itemUrl : null,
                'route' => $itemUrl !== '' ? $itemUrl : null,
                'openIn' => $itemUrl !== '' ? 'inAppWebView' : null,
                'actionId' => $item['actionId'] ?? null,
                'events' => !empty($item['actionId']) ? array('tap' => array('actionId' => $item['actionId'])) : null,
                'layout' => array(
                    'direction' => 'horizontal',
                    'alignItems' => 'center',
                    'crossAxisAlignment' => 'center',
                    'mainAxisSize' => 'max',
                    'columnSpan' => 1,
                    'rowSpan' => 1,
                    'gap' => 12,
                    'height' => 72,
                    'minHeight' => 72,
                    'maxHeight' => 76,
                    'minWidth' => 0,
                    'width' => 'fill',
                    'maxWidth' => '100%',
                    'overflowX' => 'hidden',
                    'titleFlex' => 1,
                    'titleMinWidth' => 0,
                ),
                'appearance' => array(
                    'borderRadius' => 16,
                    'backgroundColor' => $tint,
                    'backgroundGradient' => array(
                        'start' => $tint,
                        'end' => $tint,
                    ),
                    'iconPosition' => 'leading',
                    'titlePosition' => 'besideIcon',
                    'showIcon' => true,
                    'showAvatar' => true,
                    'iconBorderRadius' => 21,
                    'showSubtitle' => true,
                    'showActionArrow' => false,
                    'showBadge' => $badge !== '',
                    'badgePlacement' => 'trailing',
                    'iconSize' => 42,
                    'titleFontSize' => 15,
                    'subtitleFontSize' => 12,
                    'titlePadding' => 0,
                    'titleMaxLines' => 1,
                    'subtitleMaxLines' => 1,
                    'titleOverflow' => 'ellipsis',
                    'subtitleOverflow' => 'ellipsis',
                    'padding' => 12,
                    'height' => 72,
                    'minHeight' => 72,
                    'maxHeight' => 76,
                    'minWidth' => 0,
                    'width' => 'fill',
                ),
            );
        }

        $components['grid-page'] = array(
            'id' => 'grid-page',
            'type' => 'scrollPage',
            'style' => 'gridNetwork',
            'layout' => array(
                'direction' => 'vertical',
                'safeArea' => true,
                'safeAreaEdges' => array('top', 'left', 'right', 'bottom'),
                'overflowX' => 'hidden',
                'overflowY' => 'auto',
                'width' => 'fill',
                'maxWidth' => '100%',
                'minWidth' => 0,
                'reservedBottomInset' => 108,
                'padding' => array('left' => 16, 'right' => 16, 'top' => 12, 'bottom' => 108),
                'gap' => 16,
                'responsive' => array(
                    'narrow' => array('padding' => array('left' => 12, 'right' => 12, 'top' => 8, 'bottom' => 108), 'gap' => 12),
                    'compact' => array('padding' => array('left' => 12, 'right' => 12, 'top' => 10, 'bottom' => 108), 'gap' => 14),
                    'regular' => array('padding' => array('left' => 16, 'right' => 16, 'top' => 12, 'bottom' => 108), 'gap' => 16),
                    'medium' => array('padding' => array('left' => 20, 'right' => 20, 'top' => 12, 'bottom' => 108), 'gap' => 18),
                    'expanded' => array('padding' => array('left' => 28, 'right' => 28, 'top' => 16, 'bottom' => 108), 'gap' => 22, 'maxWidth' => 720),
                ),
            ),
            'children' => array(
                'grid-page-header',
                'grid-feature-row',
                'grid-share-qr',
                'grid-explore-section',
                'grid-quote-block',
            ),
        );

        $avatarInitial = (string) ($header['avatarInitial'] ?? 'S');
        $avatarColor = (string) ($header['avatarColor'] ?? '#7c3aed');
        $avatarUrl = trim((string) ($header['avatarUrl'] ?? ''));
        $components['grid-page-header'] = array(
            'id' => 'grid-page-header',
            'type' => 'pageHeader',
            'title' => (string) ($header['title'] ?? 'Grid'),
            'subtitle' => (string) ($header['subtitle'] ?? 'Your professional network space'),
            'logoUrl' => $logoUrl,
            'image' => $logoUrl,
            'designTokens' => self::designTokens(),
            'layout' => array(
                'direction' => 'horizontal',
                'alignItems' => 'center',
                'crossAxisAlignment' => 'center',
                'gap' => 10,
                'overflowX' => 'hidden',
                'width' => 'fill',
                'maxWidth' => '100%',
                'minWidth' => 0,
                'titleFlex' => 1,
                'titleMinWidth' => 0,
                'titleSubtitleDirection' => 'vertical',
                'subtitlePlacement' => 'belowTitle',
                'actionsFlex' => 0,
                'responsive' => array(
                    'narrow' => array('gap' => 8, 'titleFontSize' => 22, 'subtitleFontSize' => 12, 'logoHeight' => 28),
                    'compact' => array('gap' => 10, 'titleFontSize' => 24, 'subtitleFontSize' => 13, 'logoHeight' => 32),
                    'regular' => array('gap' => 12, 'titleFontSize' => 28, 'subtitleFontSize' => 13, 'logoHeight' => 36),
                    'medium' => array('gap' => 12, 'titleFontSize' => 30, 'subtitleFontSize' => 14, 'logoHeight' => 36),
                    'expanded' => array('gap' => 16, 'titleFontSize' => 34, 'subtitleFontSize' => 15, 'logoHeight' => 40),
                ),
            ),
            'appearance' => array(
                'logoHeight' => 32,
                'logoMaxWidth' => 96,
            ),
            'typography' => array(
                'titleToken' => 'typography.pageTitle',
                'subtitleToken' => 'typography.pageSubtitle',
                'title' => array(
                    'fontSize' => 24,
                    'fontWeight' => 800,
                    'maxLines' => 1,
                    'overflow' => 'ellipsis',
                ),
                'subtitle' => array(
                    'fontSize' => 13,
                    'fontWeight' => 500,
                    'maxLines' => 2,
                    'overflow' => 'ellipsis',
                ),
            ),
            'actions' => array(
                array(
                    'id' => 'grid-header-scanner',
                    'iconToken' => 'scanner',
                    'actionId' => 'grid-open-scanner',
                    'layout' => array(
                        'width' => 40,
                        'height' => 40,
                        'flex' => 0,
                        'shrink' => 0,
                    ),
                    'appearance' => array(
                        'size' => 40,
                        'iconSize' => 22,
                    ),
                ),
                array(
                    'id' => 'grid-header-avatar',
                    'type' => 'avatarButton',
                    'label' => $avatarInitial,
                    'initial' => $avatarInitial,
                    'color' => $avatarColor,
                    'backgroundColor' => $avatarColor,
                    'foregroundColor' => '#ffffff',
                    'actionId' => 'navigate-profile',
                    'fontSize' => 15,
                    'action' => array(
                        'fontSize' => 15,
                        'fontWeight' => 700,
                    ),
                    'colorToken' => 'colors.onAccent',
                    'backgroundColorToken' => 'colors.accent',
                    'fontSizeToken' => 'action.fontSize',
                    'designTokens' => array(
                        'colors' => array(
                            'surface' => '#ffffff',
                            'onAccent' => '#ffffff',
                            'accent' => $avatarColor,
                        ),
                        'action' => array(
                            'fontSize' => 15,
                            'fontWeight' => 700,
                        ),
                    ),
                    'layout' => array(
                        'width' => 40,
                        'height' => 40,
                        'flex' => 0,
                        'shrink' => 0,
                    ),
                    'style' => array(
                        'width' => 40,
                        'height' => 40,
                        'fontSize' => 15,
                        'fontWeight' => 700,
                        'backgroundColor' => $avatarColor,
                        'color' => '#ffffff',
                    ),
                    'appearance' => array(
                        'size' => 40,
                        'width' => 40,
                        'height' => 40,
                        'borderRadius' => 20,
                        'backgroundColor' => $avatarColor,
                        'foregroundColor' => '#ffffff',
                        'fontSize' => 15,
                        'fontWeight' => 700,
                        'imageUrl' => $avatarUrl,
                    ),
                    'imageUrl' => $avatarUrl,
                    'avatarUrl' => $avatarUrl,
                    'image' => $avatarUrl,
                ),
            ),
        );
        if ($avatarUrl === '') {
            foreach ($components['grid-page-header']['actions'] as $actionIndex => $headerAction) {
                if (!is_array($headerAction) || (string) ($headerAction['id'] ?? '') !== 'grid-header-avatar') {
                    continue;
                }
                unset(
                    $components['grid-page-header']['actions'][$actionIndex]['imageUrl'],
                    $components['grid-page-header']['actions'][$actionIndex]['avatarUrl'],
                    $components['grid-page-header']['actions'][$actionIndex]['image'],
                    $components['grid-page-header']['actions'][$actionIndex]['appearance']['imageUrl']
                );
            }
        }

        $components['grid-feature-row'] = array(
            'id' => 'grid-feature-row',
            'type' => 'responsiveGrid',
            'layout' => array_merge(self::gridLayoutMetrics(2, 12, 188), array(
                'independentScroll' => false,
                'responsive' => array(
                    'narrow' => self::gridLayoutMetrics(2, 10, 176),
                    'compact' => self::gridLayoutMetrics(2, 8, 188),
                    'regular' => self::gridLayoutMetrics(2, 12, 188),
                    'medium' => self::gridLayoutMetrics(2, 12, 200),
                    'expanded' => self::gridLayoutMetrics(2, 16, 210, array('maximumContentWidth' => 720)),
                ),
            )),
            'layouts' => array(
                'narrow' => self::gridLayoutMetrics(2, 10, 176),
                'compact' => self::gridLayoutMetrics(2, 8, 188),
                'regular' => self::gridLayoutMetrics(2, 12, 188),
                'medium' => self::gridLayoutMetrics(2, 12, 200),
                'expanded' => self::gridLayoutMetrics(2, 16, 210, array('maximumContentWidth' => 720)),
            ),
            'overflowPolicy' => 'reflow',
            'preserveChildOrder' => true,
            'children' => $featureChildIds,
        );

        $gridId = trim($gridId);
        if ($gridId === '') {
            $gridId = self::resolveGridId($userId);
        }
        $shareQr = self::fillShareQrImages($shareQr, $userId, $gridId);
        $selectedMode = (string) ($shareQr['selectedMode'] ?? 'professional');
        $qrPayload = (string) ($shareQr['qrPayload'] ?? self::connectUrl($selectedMode, $userId, $gridId));
        $qrImageUrl = (string) ($shareQr['qrImageUrl'] ?? '');
        $qrImageUrlTemplate = (string) ($shareQr['qrImageUrlTemplate'] ?? self::qrImageUrlTemplate());
        $qrImageUrlsByMode = is_array($shareQr['qrImageUrlsByMode'] ?? null) ? $shareQr['qrImageUrlsByMode'] : array();
        $modeItems = array();
        $modes = is_array($shareQr['modes'] ?? null) ? $shareQr['modes'] : array();
        foreach ($modes as $mode) {
            if (!is_array($mode)) {
                continue;
            }
            $modeId = (string) ($mode['id'] ?? '');
            $modeItems[] = array(
                'id' => $modeId,
                'label' => (string) ($mode['label'] ?? $modeId),
                'selected' => $modeId !== '' && $modeId === $selectedMode,
                'actionId' => (string) ($shareQr['selectModeActionId'] ?? 'grid-select-qr-mode'),
            );
        }

        $components['grid-share-qr'] = array(
            'id' => 'grid-share-qr',
            'type' => 'shareQrCard',
            'title' => (string) ($shareQr['title'] ?? 'Share Profile QR'),
            'subtitle' => (string) ($shareQr['subtitle'] ?? 'Share your professional or circle profile with anyone.'),
            'iconToken' => self::knownIconToken($shareQr['iconToken'] ?? 'qrcode', 'qrcode'),
            'selectedMode' => $selectedMode,
            'modes' => $modeItems,
            'qrCaption' => (string) ($shareQr['qrCaption'] ?? 'Scan to connect'),
            'qrPayload' => $qrPayload,
            // Client-side QR fallback (same value as qrPayload).
            'data' => $qrPayload,
            // Flutter shareQrCard reads imageUrl; keep image + qrImageUrl for web.
            'image' => $qrImageUrl,
            'imageUrl' => $qrImageUrl,
            'qrImageUrl' => $qrImageUrl,
            'qrImageUrlTemplate' => $qrImageUrlTemplate,
            'qrImageUrlsByMode' => $qrImageUrlsByMode,
            'shareActionId' => null,
            'layout' => array(
                'direction' => 'horizontal',
                'alignItems' => 'center',
                'crossAxisAlignment' => 'center',
                'gap' => 16,
                'width' => 'fill',
                'maxWidth' => '100%',
                'minWidth' => 0,
                'overflowX' => 'hidden',
                'qrSize' => 140,
                'responsive' => array(
                    'narrow' => array('direction' => 'vertical', 'gap' => 14, 'alignItems' => 'stretch', 'qrSize' => 160),
                    'compact' => array('direction' => 'horizontal', 'gap' => 14, 'qrSize' => 132),
                    'regular' => array('direction' => 'horizontal', 'gap' => 16, 'qrSize' => 140),
                    'medium' => array('direction' => 'horizontal', 'gap' => 18, 'qrSize' => 148),
                    'expanded' => array('direction' => 'horizontal', 'gap' => 20, 'qrSize' => 160),
                ),
            ),
            'appearance' => array(
                'backgroundColor' => '#ffffff',
                'borderColor' => '#e5e7eb',
                'borderWidth' => 1,
                'borderRadius' => 20,
                'padding' => 16,
                'titleFontSize' => 16,
                'titleFontWeight' => 800,
                'titleColor' => '#1e1b4b',
                'subtitleFontSize' => 12,
                'subtitleColor' => '#6b7280',
                'qrSize' => 140,
                'qrPadding' => 10,
                'qrBorderRadius' => 14,
                'qrBackgroundColor' => '#ffffff',
                'qrBorderColor' => '#e5e7eb',
                'captionFontSize' => 11,
                'captionColor' => '#94a3b8',
                'modeChipBorderRadius' => 999,
                'modeChipPaddingHorizontal' => 12,
                'modeChipPaddingVertical' => 8,
                'modeChipFontSize' => 12,
                'modeChipBackground' => '#f3f4f6',
                'modeChipColor' => '#475569',
                'modeChipSelectedBackground' => '#7c3aed',
                'modeChipSelectedColor' => '#ffffff',
            ),
        );

        $filters = is_array($exploring['filters'] ?? null) ? $exploring['filters'] : array();
        $filterModes = array();
        foreach ($filters as $filter) {
            if (!is_array($filter)) {
                continue;
            }
            $filterLabel = (string) ($filter['label'] ?? '');
            if (isset($filter['count']) && $filter['count'] !== '') {
                $filterLabel .= ' ' . $filter['count'];
            }
            $filterModes[] = array(
                'id' => (string) ($filter['id'] ?? ''),
                'label' => $filterLabel,
                'selected' => !empty($filter['selected']),
            );
        }
        $components['grid-explore-section'] = array(
            'id' => 'grid-explore-section',
            'type' => 'section',
            'title' => (string) ($exploring['title'] ?? 'Recent Activity'),
            'badge' => (string) ($exploring['badge'] ?? ''),
            'headerBadge' => array(
                'label' => (string) ($exploring['badge'] ?? ''),
                'backgroundColor' => '#dcfce7',
                'color' => '#16a34a',
            ),
            'filters' => $filters,
            'modes' => $filterModes,
            'trailing' => array(
                'label' => (string) ($exploring['seeAllLabel'] ?? 'See all'),
                'actionId' => (string) ($exploring['seeAllActionId'] ?? 'grid-see-all-exploring'),
            ),
            'children' => array('grid-explore-row'),
        );

        $components['grid-explore-row'] = array(
            'id' => 'grid-explore-row',
            'type' => 'responsiveGrid',
            'layout' => array_merge(self::gridLayoutMetrics(1, 8, 76), array(
                'independentScroll' => false,
                'responsive' => array(
                    'narrow' => self::gridLayoutMetrics(1, 8, 76),
                    'compact' => self::gridLayoutMetrics(1, 8, 76),
                    'regular' => self::gridLayoutMetrics(1, 8, 76),
                    'medium' => self::gridLayoutMetrics(1, 8, 76),
                    'expanded' => self::gridLayoutMetrics(1, 10, 76, array('maximumContentWidth' => 720)),
                ),
            )),
            'layouts' => array(
                'narrow' => self::gridLayoutMetrics(1, 8, 76),
                'compact' => self::gridLayoutMetrics(1, 8, 76),
                'regular' => self::gridLayoutMetrics(1, 8, 76),
                'medium' => self::gridLayoutMetrics(1, 8, 76),
                'expanded' => self::gridLayoutMetrics(1, 10, 76, array('maximumContentWidth' => 720)),
            ),
            'overflowPolicy' => 'reflow',
            'preserveChildOrder' => true,
            'children' => $exploreChildIds,
        );

        $components['grid-quote-block'] = array(
            'id' => 'grid-quote-block',
            'type' => 'quoteBlock',
            'title' => (string) ($quote['text'] ?? 'Connections turn potential into progress.'),
            'iconToken' => self::knownIconToken($quote['iconToken'] ?? 'quote', 'quote'),
            'appearance' => array(
                'backgroundColor' => '#f5f3ff',
                'textColor' => '#312e81',
                'borderRadius' => 20,
            ),
        );

        $components['grid-loading'] = array('id' => 'grid-loading', 'type' => 'skeleton', 'variant' => 'home-dashboard');
        $components['grid-empty'] = array(
            'id' => 'grid-empty',
            'type' => 'emptyState',
            'iconToken' => 'grid',
            'title' => 'Grid is empty',
            'subtitle' => 'Your network space will appear here.',
        );
        $components['grid-error'] = array(
            'id' => 'grid-error',
            'type' => 'errorState',
            'title' => 'Unable to load Grid',
            'retryActionId' => 'refresh-grid',
        );

        return $components;
    }

    /**
     * Camera page. Grid only accepts open-url / host-action / navigate.
     * type "native" is rejected by the app ("Unsupported Grid action type").
     */
    private static function scanPageUrl(): string
    {
        // Canonical scanner path used by the app (must not 404).
        // grid-dashboard/scanner remains as a studio alias.
        return class_exists(GridWebSso::class)
            ? GridWebSso::publicWebUrl('grid/qr')
            : 'https://efactor.tech/index.php/grid/qr';
    }

    private static function scanPageUrlWithSso(string $ssoToken): string
    {
        $url = self::scanPageUrl();
        $ssoToken = trim($ssoToken);
        if ($ssoToken !== '' && class_exists(GridWebSso::class)) {
            return GridWebSso::appendToken($url, $ssoToken);
        }
        return $url;
    }

    private static function scannerAction(array $webView, string $scanUrl): array
    {
        $webView['showAppBar'] = true;
        $webView['showToolbar'] = true;
        $webView['hideAppBar'] = false;
        $webView['displayMode'] = 'standard';
        return array(
            'type' => 'open-url',
            'url' => $scanUrl,
            'route' => $scanUrl,
            'title' => 'Scan QR',
            'openIn' => 'inAppWebView',
            'webView' => $webView,
            'destination' => array(
                'url' => $scanUrl,
                'openIn' => 'inAppWebView',
                'title' => 'Scan QR',
                'sharedCookies' => true,
                'showAppBar' => true,
                'showToolbar' => true,
                'hideAppBar' => false,
                'webView' => $webView,
            ),
        );
    }

    /**
     * In-app web view action. Matches Professional Space and Circle Connect.
     */
    private static function inAppWebViewAction(string $url, array $webView): array
    {
        return array(
            'type' => 'open-url',
            'url' => $url,
            'route' => $url,
            'title' => '',
            'openIn' => 'inAppWebView',
            'webView' => $webView,
            'destination' => array(
                'url' => $url,
                'openIn' => 'inAppWebView',
                'title' => '',
                'sharedCookies' => true,
                'showAppBar' => false,
                'showToolbar' => false,
                'hideAppBar' => true,
                'webView' => $webView,
            ),
        );
    }

    private static function signedWebUrl(string $path, string $ssoToken): string
    {
        $url = class_exists(GridWebSso::class)
            ? GridWebSso::publicWebUrl($path)
            : 'https://efactor.tech/index.php/' . trim($path, '/');
        if ($url === '') {
            $url = 'https://efactor.tech/index.php/' . trim($path, '/');
        }
        if ($ssoToken !== '' && class_exists(GridWebSso::class)) {
            $url = GridWebSso::appendToken($url, $ssoToken);
        }
        return $url;
    }

    public static function buildActions(array $content = array()): array
    {
        $featureCards = is_array($content['featureCards'] ?? null) ? $content['featureCards'] : array();
        $proUrl = '';
        $circleUrl = '';
        foreach ($featureCards as $card) {
            if (!is_array($card)) {
                continue;
            }
            $id = (string) ($card['id'] ?? '');
            $url = trim((string) ($card['url'] ?? $card['route'] ?? ''));
            if ($id === 'professional-space') {
                $proUrl = $url;
            }
            if ($id === 'circle-connect') {
                $circleUrl = $url;
            }
        }

        // prepareContentForClient() already canonicalized URLs and appended ?grid_sso=.
        // Do not call resolveCardUrl() here — it strips the token and breaks auto-login.
        $ssoToken = '';
        foreach (array($proUrl, $circleUrl) as $candidate) {
            if ($candidate === '' || strpos($candidate, 'grid_sso=') === false) {
                continue;
            }
            parse_str((string) (parse_url($candidate, PHP_URL_QUERY) ?? ''), $q);
            $ssoToken = trim((string) ($q['grid_sso'] ?? ''));
            if ($ssoToken !== '') {
                break;
            }
        }

        $proOk = $proUrl !== ''
            && preg_match('~/(index\.php/)?grid([/?#]|$)~i', $proUrl)
            && !preg_match('#grid-(chat|dashboard)|app-web-entry#i', $proUrl);
        if (!$proOk) {
            $proUrl = class_exists(GridWebSso::class)
                ? (string) (GridWebSso::defaultCardUrls()['professional-space'] ?? '')
                : 'https://efactor.tech/index.php/grid';
            if ($proUrl === '') {
                $proUrl = 'https://efactor.tech/index.php/grid';
            }
            if ($ssoToken !== '' && class_exists(GridWebSso::class)) {
                $proUrl = GridWebSso::appendToken($proUrl, $ssoToken);
            }
        }

        $circleOk = $circleUrl !== ''
            && stripos($circleUrl, 'app-web-entry') === false
            && (stripos($circleUrl, 'grid-chat') !== false || stripos($circleUrl, 'grid_chat') !== false);
        if (!$circleOk) {
            $circleUrl = class_exists(GridWebSso::class)
                ? (string) (GridWebSso::defaultCardUrls()['circle-connect'] ?? '')
                : 'https://efactor.tech/index.php/grid-chat';
            if ($circleUrl === '') {
                $circleUrl = 'https://efactor.tech/index.php/grid-chat';
            }
            if ($ssoToken !== '' && class_exists(GridWebSso::class)) {
                $circleUrl = GridWebSso::appendToken($circleUrl, $ssoToken);
            }
        }

        // No host WebView chrome (back / title / refresh). Grid & Grid-chat
        // already have their own UI; the Flutter bar was schema-driven via title.
        $hiddenWebView = array(
            'javaScriptEnabled' => true,
            'sharedCookies' => true,
            'clearCache' => false,
            'showAppBar' => false,
            'showToolbar' => false,
            'hideAppBar' => true,
            'displayMode' => 'fullscreen',
        );

        $proAction = self::inAppWebViewAction($proUrl, $hiddenWebView);
        $circleAction = self::inAppWebViewAction($circleUrl, $hiddenWebView);
        // Same signed in-app shape as Professional Space and Circle Connect.
        // Network is the web people hub. Circles live in grid-chat.
        // See all opens that people hub, which is the web "show all" list.
        $findTalentAction = self::inAppWebViewAction(self::signedWebUrl('grid/network', $ssoToken), $hiddenWebView);
        $joinCirclesAction = self::inAppWebViewAction(self::signedWebUrl('grid-chat', $ssoToken), $hiddenWebView);
        $seeAllAction = self::inAppWebViewAction(self::signedWebUrl('grid/network', $ssoToken), $hiddenWebView);
        $notificationActions = array();
        $exploreItems = is_array($content['exploring']['items'] ?? null) ? $content['exploring']['items'] : array();
        foreach ($exploreItems as $item) {
            if (!is_array($item)) {
                continue;
            }
            $actionId = trim((string) ($item['actionId'] ?? ''));
            $itemUrl = trim((string) ($item['url'] ?? $item['route'] ?? ''));
            if ($actionId === '' || $itemUrl === '' || isset($notificationActions[$actionId])) {
                continue;
            }
            $notificationActions[$actionId] = self::inAppWebViewAction($itemUrl, $hiddenWebView);
        }
        // Avatar image is display-only on the header button. Tapping must open
        // the profile route — never the raw PNG in a WebView.
        $profileAction = array(
            'type' => 'navigate',
            'destination' => array('routeId' => 'profile'),
        );

        return array(
            'navigate-grid' => array('type' => 'navigate', 'destination' => array('routeId' => 'grid')),
            'navigate-profile' => $profileAction,
            'refresh-grid' => array(
                'type' => 'sequence',
                'actions' => array(
                    array('type' => 'set-state', 'path' => 'refreshing', 'value' => true),
                    array('type' => 'refresh-data-source', 'dataSourceId' => 'grid'),
                    array('type' => 'set-state', 'path' => 'refreshing', 'value' => false),
                ),
            ),
            'grid-open-scanner' => self::scannerAction($hiddenWebView, self::scanPageUrlWithSso($ssoToken)),
            'grid-open-professional-space' => $proAction,
            'grid-open-circle-connect' => $circleAction,
            'grid-open-find-talent' => $findTalentAction,
            'grid-open-join-circles' => $joinCirclesAction,
            'grid-see-all-exploring' => $seeAllAction,
            'grid-select-qr-mode' => array('type' => 'host-action', 'actionId' => 'select-grid-qr-mode'),
        ) + $notificationActions;
    }

    /**
     * Apply studio open-URL inputs onto Professional Space / Circle Connect.
     * Image URL arguments are ignored — icons always come from iconToken.
     */
    public static function applyFeatureCardUrls(
        array $content,
        string $professionalSpaceUrl,
        string $circleConnectUrl,
        string $professionalSpaceImage = '',
        string $circleConnectImage = ''
    ): array {
        $content = self::mergeContent($content);
        $cards = is_array($content['featureCards'] ?? null) ? $content['featureCards'] : array();
        foreach ($cards as $i => $card) {
            if (!is_array($card)) {
                continue;
            }
            $id = (string) ($card['id'] ?? '');
            if ($id === 'professional-space') {
                $url = trim($professionalSpaceUrl);
                $cards[$i]['url'] = $url;
                $cards[$i]['route'] = $url;
                $cards[$i]['image'] = '';
                $cards[$i]['imageUrl'] = '';
                $cards[$i] = self::normalizeSoftCard($cards[$i]);
            }
            if ($id === 'circle-connect') {
                $url = trim($circleConnectUrl);
                $cards[$i]['url'] = $url;
                $cards[$i]['route'] = $url;
                $cards[$i]['image'] = '';
                $cards[$i]['imageUrl'] = '';
                $cards[$i] = self::normalizeSoftCard($cards[$i]);
            }
        }
        $content['featureCards'] = $cards;
        return $content;
    }

    private static function knownIconToken($token, $fallback = 'grid'): string
    {
        $token = strtolower(trim((string) $token));
        $token = preg_replace('/^(fas|far|fab|fa-solid|fa-regular|fa-brands)\s+/', '', $token) ?? $token;
        if (strpos($token, 'fa-') === 0) {
            $token = substr($token, 3);
        }
        $map = array(
            'user' => 'user',
            'user-circle' => 'user',
            'users' => 'users',
            'user-friends' => 'users',
            'user-tie' => 'users',
            'grid' => 'grid',
            'border-all' => 'grid',
            'search' => 'search',
            'magnifying-glass' => 'search',
            'qrcode' => 'scanner',
            'qr-code' => 'scanner',
            'qr_code' => 'scanner',
            'scan' => 'scanner',
            'scanner' => 'scanner',
            'qr-code-scanner' => 'scanner',
            'quote' => 'quote',
            'quote-left' => 'quote',
            'chevron-right' => 'chevron-right',
            'profile' => 'profile',
            'user-pen' => 'profile',
            'message' => 'message',
            'comment' => 'message',
            'phone' => 'phone',
            'call' => 'phone',
        );
        if (isset($map[$token])) {
            return $map[$token];
        }
        $known = array('grid', 'search', 'qrcode', 'scanner', 'user', 'users', 'quote', 'chevron-right', 'profile', 'message', 'phone');
        return in_array($token, $known, true) ? $token : (string) $fallback;
    }

    public static function buildIconRegistry(): array
    {
        return array(
            'grid' => array('flutter' => 'borderAll', 'web' => 'border-all'),
            'search' => array('flutter' => 'magnifyingGlass', 'web' => 'magnifying-glass'),
            // Material "scanner" is a flatbed doc icon; QR header needs qr_code_scanner.
            'scanner' => array('flutter' => 'qrCodeScanner', 'web' => 'qrcode'),
            'qrcode' => array('flutter' => 'qrCode', 'web' => 'qrcode'),
            'user' => array('flutter' => 'user', 'web' => 'user'),
            'users' => array('flutter' => 'users', 'web' => 'users'),
            'quote' => array('flutter' => 'quoteLeft', 'web' => 'quote-left'),
            'chevron-right' => array('flutter' => 'chevronRight', 'web' => 'chevron-right'),
            'profile' => array('flutter' => 'userPen', 'web' => 'user-pen'),
            'message' => array('flutter' => 'chatBubble', 'web' => 'comment'),
            'phone' => array('flutter' => 'phone', 'web' => 'phone'),
        );
    }

    /**
     * Replace Find Talent / Join Circles with this account's recent
     * Professional Space and Circle Connect messages and calls.
     * Same exploring item fields the section already renders.
     */
    private static function applyRecentNotifications(array $content, int $userId, string $ssoToken): array
    {
        if (!class_exists(GridActivityNotifications::class)) {
            return $content;
        }
        if (!isset($content['exploring']) || !is_array($content['exploring'])) {
            $content['exploring'] = array();
        }
        $content['exploring']['title'] = 'Recent Activity';
        $items = GridActivityNotifications::forUser($userId, $ssoToken);
        $unread = 0;
        $professionalCount = 0;
        $circleCount = 0;
        foreach ($items as $item) {
            if (!is_array($item) || (string) ($item['id'] ?? '') === 'professional-message') {
                continue;
            }
            if (!empty($item['unread'])) {
                $unread++;
            }
            if ((string) ($item['space'] ?? '') === 'circle') {
                $circleCount++;
            } else {
                $professionalCount++;
            }
        }
        $content['exploring']['badge'] = $unread > 0 ? ($unread . ' new') : '';
        $content['exploring']['filters'] = array(
            array('id' => 'all', 'label' => 'All', 'selected' => true),
            array('id' => 'professional', 'label' => 'Professional', 'count' => $professionalCount),
            array('id' => 'circle', 'label' => 'Circle Connect', 'count' => $circleCount),
        );
        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                continue;
            }
            $id = strtolower(trim((string) ($item['id'] ?? $index)));
            $id = preg_replace('/[^a-z0-9\-]+/', '-', $id) ?? '';
            $id = trim($id, '-');
            $base = trim((string) ($item['actionId'] ?? 'grid-open-notification'));
            if ($id !== '' && substr($base, -strlen('-' . $id)) !== '-' . $id) {
                $item['actionId'] = $base . '-' . $id;
            }
            $items[$index] = $item;
        }
        $content['exploring']['items'] = $items;
        return $content;
    }

    /**
     * The app paints the Grid section stored in the saved home schema.
     * Replace only that section with the live Recent Activity rows.
     */
    public static function patchRecentActivity(array $schema, int $userId, string $ssoToken = ''): array
    {
        if (!isset($schema['components']) || !is_array($schema['components'])) {
            return $schema;
        }
        $fresh = self::build(array(
            'userId' => $userId,
            'ssoToken' => $ssoToken,
        ));
        $freshComponents = is_array($fresh['components'] ?? null) ? $fresh['components'] : array();
        foreach (array_keys($schema['components']) as $key) {
            if ($key === 'grid-explore-section' || $key === 'grid-explore-row' || strpos((string) $key, 'grid-explore-') === 0) {
                unset($schema['components'][$key]);
            }
        }
        foreach ($freshComponents as $key => $component) {
            if ($key === 'grid-explore-section' || $key === 'grid-explore-row' || strpos((string) $key, 'grid-explore-') === 0) {
                $schema['components'][$key] = $component;
            }
        }
        if (!isset($schema['actions']) || !is_array($schema['actions'])) {
            $schema['actions'] = array();
        }
        $freshActions = is_array($fresh['actions'] ?? null) ? $fresh['actions'] : array();
        foreach ($schema['components'] as $component) {
            if (!is_array($component)) {
                continue;
            }
            $actionId = (string) ($component['actionId'] ?? '');
            if ($actionId !== '' && isset($freshActions[$actionId])) {
                $schema['actions'][$actionId] = $freshActions[$actionId];
            }
        }
        if (isset($fresh['content']['exploring']) && is_array($fresh['content']['exploring'])) {
            if (!isset($schema['content']) || !is_array($schema['content'])) {
                $schema['content'] = array();
            }
            $schema['content']['exploring'] = $fresh['content']['exploring'];
        }
        if (isset($fresh['iconRegistry']) && is_array($fresh['iconRegistry'])) {
            if (!isset($schema['iconRegistry']) || !is_array($schema['iconRegistry'])) {
                $schema['iconRegistry'] = array();
            }
            foreach (array('message', 'phone') as $iconKey) {
                if (isset($fresh['iconRegistry'][$iconKey])) {
                    $schema['iconRegistry'][$iconKey] = $fresh['iconRegistry'][$iconKey];
                }
            }
        }
        return $schema;
    }

    /**
     * @param array $options userId, apiBase, content override, endpoint
     */
    public static function build(array $options = array()): array
    {
        $userId = max(0, (int) ($options['userId'] ?? 0));
        $gridId = trim((string) ($options['gridId'] ?? ''));
        if ($gridId === '') {
            $gridId = self::resolveGridId($userId);
        }
        $content = self::mergeContent(isset($options['content']) && is_array($options['content']) ? $options['content'] : null);
        if (!isset($content['header']) || !is_array($content['header'])) {
            $content['header'] = array();
        }
        $content['header']['searchIconToken'] = 'scanner';
        $headerProfile = self::resolveHeaderProfile($userId);
        if ($headerProfile['initial'] !== '') {
            $content['header']['avatarInitial'] = $headerProfile['initial'];
        }
        // Always replace the saved value. Otherwise an account without a photo
        // inherits the avatar cached in gridPage by the previously active account.
        $content['header']['avatarUrl'] = $headerProfile['imageUrl'];
        $content['shareQr'] = self::fillShareQrImages(
            is_array($content['shareQr'] ?? null) ? $content['shareQr'] : array(),
            $userId,
            $gridId
        );
        $ssoToken = trim((string) ($options['ssoToken'] ?? ''));
        if (class_exists(GridWebSso::class)) {
            $content = GridWebSso::prepareContentForClient($content, $ssoToken);
        }
        $content = self::applyRecentNotifications($content, $userId, $ssoToken);
        $endpoint = trim((string) ($options['endpoint'] ?? 'grid_api_creation.php'));
        if ($endpoint === '') {
            $endpoint = 'grid_api_creation.php';
        }

        $components = self::buildComponents($content, $userId, $gridId);

        return array(
            'schemaType' => 'server-driven-mobile-app',
            'schemaVersion' => '3.0.0',
            'pageId' => 'grid',
            'routeId' => 'grid',
            'rootComponentId' => 'grid-page',
            'replaceAppShell' => false,
            'reuseHostAppShell' => true,
            'keepHostBottomNavigation' => true,
            'route' => array('path' => '/grid', 'pageId' => 'grid'),
            'appShell' => array(
                'reuseHostShell' => true,
                'replaceOnNestedSchema' => false,
                'keepHostBottomNavigation' => true,
                'bottomNavigationFixed' => true,
                'bottomNavigationOutsidePageScroll' => true,
                'bottomNavigationPosition' => 'fixed',
                'contentAvoidsBottomNavigation' => true,
                'reservedBottomInset' => 108,
            ),
            'page' => array(
                'id' => 'grid',
                'routeId' => 'grid',
                'rootComponentId' => 'grid-page',
                'dataSourceId' => 'grid',
                'presentationStates' => array(
                    'loadingComponentId' => 'grid-loading',
                    'emptyComponentId' => 'grid-empty',
                    'errorComponentId' => 'grid-error',
                ),
            ),
            'components' => $components,
            'actions' => self::buildActions($content),
            'dataSources' => array(
                'grid' => array(
                    'type' => 'rest',
                    'endpoint' => $endpoint,
                    'method' => 'GET',
                    'queryParameters' => array_filter(array(
                        'action' => 'get_grid',
                        'user_id' => $userId > 0 ? $userId : null,
                    )),
                    'responseMapping' => array(
                        'schema' => '$.schema',
                        'page' => '$.schema.page',
                        'components' => '$.schema.components',
                        'actions' => '$.schema.actions',
                        'iconRegistry' => '$.schema.iconRegistry',
                        'content' => '$.schema.content',
                    ),
                    'published' => true,
                ),
            ),
            'content' => $content,
            'iconRegistry' => self::buildIconRegistry(),
            'action' => array(
                'fontSize' => 15,
                'fontWeight' => 700,
            ),
            'theme' => array(
                'colors' => array(
                    'surface' => '#ffffff',
                    'onAccent' => '#ffffff',
                    'onSurface' => '#111827',
                    'accent' => '#7c3aed',
                    'textPrimary' => '#2e1065',
                    'textSecondary' => '#6b7280',
                    'pageBackground' => '#f7f8fc',
                ),
            ),
            'typography' => array(
                'action' => array(
                    'fontSize' => 15,
                    'fontWeight' => 700,
                    'lineHeight' => 1.2,
                ),
                'pageTitle' => array(
                    'fontSize' => 26,
                    'fontWeight' => 800,
                    'maxLines' => 1,
                    'overflow' => 'ellipsis',
                ),
                'pageSubtitle' => array(
                    'fontSize' => 13,
                    'fontWeight' => 500,
                    'maxLines' => 2,
                    'overflow' => 'ellipsis',
                ),
            ),
            'designTokens' => self::designTokens(),
            'layoutPolicy' => array(
                'overflowPolicy' => 'reflow',
                'overflowX' => 'hidden',
                'width' => 'fill',
                'maxWidth' => '100%',
                'reservedBottomInset' => 108,
                'responsiveBreakpoints' => array(
                    'narrow' => 0,
                    'compact' => 320,
                    'regular' => 480,
                    'medium' => 600,
                    'expanded' => 840,
                ),
                'responsive' => array(
                    'defaultBreakpoint' => 'compact',
                    'breakpoints' => array(
                        'narrow' => array('maxWidth' => 319),
                        'compact' => array('minWidth' => 320, 'maxWidth' => 599),
                        'medium' => array('minWidth' => 600, 'maxWidth' => 839),
                        'expanded' => array('minWidth' => 840),
                    ),
                    'grid' => array(
                        'narrowScreenThresholdDp' => 340,
                        'narrowScreenMaxColumns' => 2,
                        'minimumCellWidthDp' => 0,
                        'clampColumnSpanToColumns' => true,
                        'preserveMetroColumnsOnNarrow' => false,
                    ),
                    'overflowPolicy' => array(
                        'headerText' => 'ellipsis',
                        'default' => 'ellipsis',
                    ),
                ),
            ),
            'security' => array(
                'requireHttps' => true,
                'allowedAssetDomains' => array_values(array_unique(array_filter(array(
                    'efactor.tech',
                    'www.efactor.tech',
                    preg_replace('/:\d+$/', '', (string) parse_url(self::publicOrigin(), PHP_URL_HOST)),
                )))),
            ),
        );
    }
}
