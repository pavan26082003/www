<?php

namespace App\Models;

class AppHomePageModel
{
    /**
     * Bottom navigation is the same four slots for every user: Home, IoT, Grid, Profile.
     * The third slot is not configurable. Older per-user values (alerts, reports, rooms, …)
     * are ignored so one saved layout cannot swap Grid for Alerts.
     */
    public const BOTTOM_NAV_CATEGORY_DEFAULT = 'grid';

    public const BOTTOM_NAV_CATEGORY_TARGETS = ['grid'];

    /**
     * Third slot is always Grid. Saved per-user targets are not applied.
     */
    public static function normalizeBottomNavCategoryTarget($value): string
    {
        return self::BOTTOM_NAV_CATEGORY_DEFAULT;
    }

    /**
     * Force Home / IoT / Grid / Profile onto a layout, ignoring any saved third-slot target.
     *
     * @param array<string, mixed> $layout
     * @return array<string, mixed>
     */
    public static function applySharedBottomNav(array $layout): array
    {
        $active = 'home';
        if (!empty($layout['activePage']) && is_string($layout['activePage'])) {
            $active = $layout['activePage'];
        }
        $layout['bottomNavCategoryTarget'] = self::BOTTOM_NAV_CATEGORY_DEFAULT;
        $canonical = self::buildCanonicalBottomNav(self::BOTTOM_NAV_CATEGORY_DEFAULT, $active);

        // Keep the fixed 4 tabs, but preserve user label/icon edits from the studio.
        $byId = array();
        if (!empty($layout['bottomNav']) && is_array($layout['bottomNav'])) {
            foreach ($layout['bottomNav'] as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $id = trim((string) ($item['id'] ?? ''));
                if ($id !== '') {
                    $byId[$id] = $item;
                }
            }
        }
        foreach ($canonical as $i => $slot) {
            $id = (string) ($slot['id'] ?? '');
            if ($id === '' || empty($byId[$id]) || !is_array($byId[$id])) {
                continue;
            }
            $label = trim((string) ($byId[$id]['label'] ?? ''));
            if ($label !== '') {
                $canonical[$i]['label'] = $label;
            }
            $icon = trim((string) ($byId[$id]['icon'] ?? ''));
            if ($icon !== '') {
                $canonical[$i]['icon'] = $icon;
            }
        }

        $layout['bottomNav'] = $canonical;

        return $layout;
    }

    /** Canonical four-slot bottom nav (Home, IoT, Grid, Profile) for every user. */
    public static function buildCanonicalBottomNav(string $categoryTarget, string $activePage = 'home'): array
    {
        $target = self::BOTTOM_NAV_CATEGORY_DEFAULT;
        $slot = array('icon' => 'fa-border-all', 'label' => 'Grid');
        $activePage = strtolower(trim($activePage));
        if ($activePage === 'settings') {
            $activePage = 'profile';
        }
        if ($activePage === 'category') {
            $activePage = 'grid';
        }

        return array(
            array('id' => 'home', 'icon' => 'fa-home', 'label' => 'Home', 'active' => $activePage === 'home'),
            array('id' => 'devices', 'icon' => 'fa-th', 'label' => 'IoT', 'active' => $activePage === 'devices'),
            array('id' => $target, 'icon' => $slot['icon'], 'label' => $slot['label'], 'active' => $activePage === $target),
            array('id' => 'profile', 'icon' => 'fa-user', 'label' => 'Profile', 'active' => $activePage === 'profile'),
        );
    }

    private $db;

    public function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
        $this->db = db_connect('default');
    }

    public function getDefaultLayout(string $userEmail = '', int $userId = 0): array
    {
        return array(
            'header' => array(
                'title' => 'eFactor · IoE',
                'subtitle' => 'Smart insights & tools',
            ),
            'welcome' => array(
                'greeting' => 'Welcome back',
                'email' => $userEmail ?: 'user@example.com',
                'caption' => 'Manage your ecosystem',
            ),
            'alerts' => array(
                'count' => 0,
                'newUsers' => 0,
                'badgeText' => 'real-time',
                'showBadge' => true,
            ),
            'theme' => array(
                'accentColor' => '#7c3aed',
                'accentGreen' => '#22c55e',
                'surfaceColor' => '#151b2e',
                'welcomeStart' => '#7c3aed',
                'welcomeEnd' => '#4f46e5',
                'phoneBg' => '#0b0f1a',
                'alertBg' => '#151b2e',
                'textPrimary' => '#ffffff',
                'textSecondary' => '#e2e8f0',
                'textMuted' => '#64748b',
                'welcomeTextColor' => '#ffffff',
                'navActiveColor' => '#3b82f6',
                'navBg' => '#000000',
                'bgStyle' => 'dark',
                'gridGap' => 4,
                'gridColumns' => 3,
                'gridRows' => 6,
                // ~375dp design width / 3 cols / gap 4 → square 1x1 cells; Flutter still prefers sizeFormula=slotWidth.
                'gridRowHeight' => 122,
                'squareTiles' => true,
                'cardBorderRadius' => 0,
                'cardSpeed' => 16,
                'logoIcon' => 'fa-crosshairs',
                'showWelcome' => true,
                'showAlerts' => true,
                'showBottomNav' => true,
                'homeTemplate' => 'default',
            ),
            'homeTemplate' => 'default',
            'cards' => $this->getDefaultStarterCards(),
            'activePage' => 'home',
            // Same four tabs for every user. Saved layouts cannot replace Grid.
            'bottomNav' => array(
                array('id' => 'home', 'icon' => 'fa-home', 'label' => 'Home', 'active' => true),
                array('id' => 'devices', 'icon' => 'fa-th', 'label' => 'IoT', 'active' => false),
                array('id' => 'grid', 'icon' => 'fa-border-all', 'label' => 'Grid', 'active' => false),
                array('id' => 'profile', 'icon' => 'fa-user', 'label' => 'Profile', 'active' => false),
            ),
            'bottomNavCategoryTarget' => self::BOTTOM_NAV_CATEGORY_DEFAULT,
            'gridPage' => array(
                'id' => 'gridPage',
                'routeId' => 'grid',
                'logoUrl' => 'https://efactor.tech/assets/images/GRID_Logo.png',
                'header' => array(
                    'title' => 'Grid',
                    'subtitle' => 'Your professional network space',
                    'logoUrl' => 'https://efactor.tech/assets/images/GRID_Logo.png',
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
            ),
            'profilePage' => array(
                'name' => 'Profile',
                'email' => $userEmail ?: 'person@efactor.app',
                'phone' => '+91 123 456 7890',
                'memberId' => $userId > 0 ? ('EF-' . $userId) : 'EF-177',
                'language' => 'English (India)',
                'languageOptions' => array(
                    'English (India)',
                    'English (US)',
                    'Hindi',
                    'Arabic',
                    'French',
                    'Spanish',
                ),
                'notificationsEnabled' => true,
                'privacyPolicyText' => 'We collect account details (name, email, phone) to provide IoE device control, Grid networking, and support.'
                    . "\n\n"
                    . 'Your data is used to authenticate sessions, sync preferences (language, notifications, appearance), and improve reliability.'
                    . "\n\n"
                    . 'You can update profile details, notification preferences, and language any time from this Profile tab.'
                    . "\n\n"
                    . 'For questions about security or data controls, contact support@efactor.tech.',
            ),
            'devicesPage' => array(
                'header' => array(
                    'title' => 'devices · iot',
                    'subtitle' => 'Tap a cube to control.',
                    'leftIcon' => 'fa-qrcode',
                    'rightIcon' => 'fa-ellipsis-h',
                ),
                'status' => array(
                    'online' => 0,
                    'offline' => 0,
                ),
                'devices' => array(),
            ),
        );
    }

    /**
     * Public https photo for the account header.
     * Grid profile photo first, then users.image.
     * Empty means the app should show initials, not a placeholder image.
     */
    private function resolveAccountAvatarUrl(int $userId, string $usersImage): string
    {
        $candidates = array(
            $this->readGridProfileAvatar($userId),
            trim($usersImage),
        );
        foreach ($candidates as $raw) {
            $url = $this->publicAvatarUrl($raw);
            if ($url !== '') {
                return $url;
            }
        }

        return '';
    }

    private function readGridProfileAvatar(int $userId): string
    {
        if ($userId <= 0) {
            return '';
        }
        try {
            if (!$this->db->tableExists('grid_profiles') || !$this->db->fieldExists('avatar', 'grid_profiles')) {
                return '';
            }
            $row = $this->db->table('grid_profiles')
                ->select('avatar')
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();

            return trim((string) ($row['avatar'] ?? ''));
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Absolute avatar URL, or empty so the app shows initials.
     * A missing, relative, or localhost photo was rendering as a broken icon.
     */
    private function publicAvatarUrl(string $image): string
    {
        $image = trim($image);
        if ($image === '' || strcasecmp($image, 'system_bot') === 0) {
            return '';
        }

        $fileName = '';
        if ($image[0] === '{' || $image[0] === '[') {
            $json = json_decode($image, true);
            if (is_array($json) && !empty($json['file_name'])) {
                $fileName = trim((string) $json['file_name']);
            }
        }
        if ($fileName === '') {
            $decoded = @unserialize($image);
            if (is_array($decoded) && !empty($decoded['file_name'])) {
                $fileName = trim((string) $decoded['file_name']);
            }
        }
        if ($fileName === '' && strpos(ltrim($image), 'a:') !== 0) {
            $fileName = $image;
        }

        return $this->canonicalizeProfileImageUrl($fileName);
    }

    private function canonicalizeProfileImageUrl(string $fileName): string
    {
        $fileName = trim(html_entity_decode($fileName, ENT_QUOTES));
        if ($fileName === '' || preg_match('#^(data:|blob:)#i', $fileName)) {
            return '';
        }
        if (preg_match('#(^|/)avatar\.jpg(\?|$)#i', $fileName) || preg_match('#/assets/images/avatar\.jpg(\?|$)#i', $fileName)) {
            return '';
        }

        if (preg_match('#^https?://#i', $fileName)) {
            $host = strtolower((string) parse_url($fileName, PHP_URL_HOST));
            $path = (string) parse_url($fileName, PHP_URL_PATH);
            if ($host === '' || in_array($host, array('localhost', '127.0.0.1', '::1'), true)) {
                return $this->publicFilesUrl($path);
            }
            if (stripos($fileName, 'http://') === 0) {
                return 'https://' . substr($fileName, 7);
            }

            return $fileName;
        }

        return $this->publicFilesUrl($fileName);
    }

    private function publicFilesUrl(string $path): string
    {
        $path = str_replace('\\', '/', trim($path));
        $path = preg_replace('#^https?://[^/]+#i', '', $path) ?? $path;
        $path = preg_replace('#^/app(?=/)#i', '', $path) ?? $path;
        $path = ltrim($path, '/');
        if ($path === '' || preg_match('#(^|/)avatar\.jpg$#i', $path)) {
            return '';
        }
        if (strpos($path, '/') === false) {
            $path = 'files/profile_images/' . $path;
        }

        return 'https://efactor.tech/' . $path;
    }

    /**
     * Merge smartphone tiers so 0–599dp is uniformly compact (P2 home schema fix).
     * Keeps medium/expanded keys; does not invent new schema parameters.
     *
     * @param array<string, mixed> $schema
     * @return array<string, mixed>
     */
    public function normalizeSmartphoneBreakpoints(array $schema): array
    {
        if (isset($schema['responsive']) && is_array($schema['responsive'])) {
            $schema['responsive'] = $this->mergeSmartphoneBreakpointBlock($schema['responsive']);
        }
        if (isset($schema['schema']['responsive']) && is_array($schema['schema']['responsive'])) {
            $schema['schema']['responsive'] = $this->mergeSmartphoneBreakpointBlock($schema['schema']['responsive']);
        }

        return $schema;
    }

    /**
     * @param array<string, mixed> $responsive
     * @return array<string, mixed>
     */
    private function mergeSmartphoneBreakpointBlock(array $responsive): array
    {
        if (!isset($responsive['breakpoints']) || !is_array($responsive['breakpoints'])) {
            return $responsive;
        }

        $bp = $responsive['breakpoints'];
        $hasNarrow = isset($bp['narrow']) && is_array($bp['narrow']);
        $compactMin = isset($bp['compact']['minimumWidth']) ? (int) $bp['compact']['minimumWidth'] : 0;
        if (!$hasNarrow && $compactMin <= 0) {
            return $responsive;
        }

        $compact = is_array($bp['compact'] ?? null) ? $bp['compact'] : array();
        $compactMax = isset($compact['maximumWidth']) ? (int) $compact['maximumWidth'] : 599;
        if ($compactMax <= 0) {
            $compactMax = 599;
        }
        $description = trim((string) ($compact['description'] ?? ''));
        if ($description === '' || stripos($description, 'phone') === false) {
            $description = 'Smartphones';
        }

        unset($bp['narrow']);
        $bp['compact'] = array_merge($compact, array(
            'minimumWidth' => 0,
            'maximumWidth' => $compactMax,
            'description' => $description,
        ));
        if (!isset($bp['medium']) || !is_array($bp['medium'])) {
            $bp['medium'] = array(
                'minimumWidth' => 600,
                'maximumWidth' => 1023,
                'description' => 'Tablets',
            );
        }
        if (!isset($bp['expanded']) || !is_array($bp['expanded'])) {
            $bp['expanded'] = array(
                'minimumWidth' => 1024,
                'description' => 'Desktop',
            );
        }
        $responsive['breakpoints'] = $bp;

        return $responsive;
    }

    private function usersTableHasGridId(string $usersTable): bool
    {
        try {
            return $this->db->fieldExists('grid_id', 'users') || $this->db->fieldExists('grid_id', $usersTable);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Avoid constructing Grid_profile_model when grid_db is missing — a failed
     * db_connect('grid') leaves sticky mysqli connect_errno that can false-fail saves.
     */
    private function isGridDbAvailable(): bool
    {
        static $available = null;
        if ($available !== null) {
            return $available;
        }

        try {
            $grid = \Config\Database::connect('grid', false);
            $available = is_object($grid) && $grid->connID;
        } catch (\Throwable $e) {
            $available = false;
        }

        return (bool) $available;
    }

    private function resolveGridIdForUser(int $userId, string $name, bool $hasUsersGridId, string $usersTable): string
    {
        $gridId = '';
        try {
            if ($this->db->tableExists('grid_profiles')) {
                $profile = $this->db->table('grid_profiles')
                    ->select('grid_id')
                    ->where('user_id', $userId)
                    ->get()
                    ->getRowArray();
                $gridId = trim((string) ($profile['grid_id'] ?? ''));
            }
        } catch (\Throwable $e) {
            $gridId = '';
        }

        if ($gridId === '' && $this->isGridDbAvailable()) {
            try {
                $profileModel = model('App\\Models\\GRID_MODEL\\Grid_profile_model');
                if ($profileModel && method_exists($profileModel, 'provision_grid_id')) {
                    $gridId = trim((string) $profileModel->provision_grid_id($userId, $name));
                }
            } catch (\Throwable $e) {
                $gridId = '';
            }
        }

        if ($gridId !== '' && $hasUsersGridId) {
            try {
                $this->db->table($usersTable)->where('id', $userId)->update(array('grid_id' => $gridId));
            } catch (\Throwable $e) {
            }
        }

        return $gridId;
    }

    private function stripUnwantedHomeHeader(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        if (preg_match('/quick\s*mart/i', $value)) {
            return '';
        }
        if (preg_match('/\b\d+\s*minutes?\b/i', $value) && preg_match('/nearby/i', $value)) {
            return '';
        }

        return $value;
    }

    /**
     * Live account fields from users table (not from saved layout JSON).
     */
    public function getUserProfile(int $userId): array
    {
        if ($userId <= 0) {
            return array(
                'name' => 'Guest',
                'email' => '',
                'phone' => '',
                'memberId' => 'EF-0',
                'avatarUrl' => '',
            );
        }

        helper('general');

        $usersTable = $this->db->prefixTable('users');
        $hasGridId = $this->usersTableHasGridId($usersTable);
        $select = 'id, first_name, last_name, email, phone, image';
        if ($hasGridId) {
            $select .= ', grid_id';
        }
        $row = $this->db->table($usersTable)
            ->select($select)
            ->where('id', $userId)
            ->where('deleted', 0)
            ->get()
            ->getRowArray();

        if (!$row) {
            return array(
                'name' => 'Guest',
                'email' => '',
                'phone' => '',
                'memberId' => 'EF-' . $userId,
                'avatarUrl' => '',
            );
        }

        $first = trim((string) ($row['first_name'] ?? ''));
        $last = trim((string) ($row['last_name'] ?? ''));
        $name = trim($first . ' ' . $last);
        if ($name === '') {
            $email = trim((string) ($row['email'] ?? ''));
            if ($email !== '' && strpos($email, '@') !== false) {
                $name = (string) strstr($email, '@', true);
            } else {
                $name = 'Guest';
            }
        }

        $image = trim((string) ($row['image'] ?? ''));
        $gridId = $hasGridId ? trim((string) ($row['grid_id'] ?? '')) : '';
        if ($gridId === '') {
            $gridId = $this->resolveGridIdForUser($userId, $name, $hasGridId, $usersTable);
        }

        return array(
            'name' => $name,
            'email' => trim((string) ($row['email'] ?? '')),
            'phone' => trim((string) ($row['phone'] ?? '')),
            'memberId' => $gridId !== '' ? $gridId : ('EF-' . (int) ($row['id'] ?? $userId)),
            'gridId' => $gridId,
            'avatarUrl' => $this->resolveAccountAvatarUrl($userId, $image),
        );
    }

    public function getLayout(int $userId): array
    {
        $this->ensureTable();
        $record = $this->getRecord($userId);

        if (!$record || empty($record['layout_json'])) {
            return array();
        }

        $data = json_decode($record['layout_json'], true);

        return is_array($data) ? $data : array();
    }

    public function mergeWithDefaults(array $default, array $saved): array
    {
        if (empty($saved)) {
            return $default;
        }

        $merged = array_replace_recursive($default, $saved);

        foreach (array('header', 'welcome', 'alerts') as $section) {
            if (!empty($saved[$section]) && is_array($saved[$section])) {
                $merged[$section] = array_replace_recursive($default[$section] ?? array(), $saved[$section]);
            }
        }

        if (($merged['header']['title'] ?? '') === 'Welcome to SmartHome!'
            || ($merged['header']['title'] ?? '') === 'Welcome to Efactor!') {
            $merged['header']['title'] = 'eFactor · IoE';
            if (empty($merged['header']['subtitle']) || ($merged['header']['subtitle'] ?? '') === 'Control your connected home.') {
                $merged['header']['subtitle'] = 'Smart insights & tools';
            }
        }

        if (!empty($saved['theme']) && is_array($saved['theme'])) {
            $merged['theme'] = array_merge($default['theme'] ?? array(), $saved['theme']);
            foreach ($saved['theme'] as $key => $value) {
                $merged['theme'][$key] = $value;
            }
        }

        // Legacy layouts were saved with showWelcome off by default — turn the banner on unless explicitly disabled in schema prefs.
        if (($merged['theme']['showWelcome'] ?? null) === false
            && ($merged['theme']['showAlerts'] ?? null) === false) {
            $merged['theme']['showWelcome'] = true;
        }

        if (array_key_exists('cards', $saved) && is_array($saved['cards'])) {
            $normalizedCards = $this->normalizeSavedCards($saved['cards']);
            $merged['cards'] = !empty($normalizedCards)
                ? $normalizedCards
                : ($default['cards'] ?? array());
        } else {
            $merged['cards'] = $default['cards'] ?? array();
        }

        // Ignore any saved third-slot target (alerts, reports, …). Every user gets Grid.
        $activeForNav = !empty($saved['activePage']) && is_string($saved['activePage'])
            ? $saved['activePage']
            : (string) ($merged['activePage'] ?? 'home');
        $merged['activePage'] = $activeForNav;
        $merged = self::applySharedBottomNav($merged);

        if (empty($merged['gridPage']) || !is_array($merged['gridPage'])) {
            $merged['gridPage'] = $default['gridPage'] ?? array();
        } else {
            $merged['gridPage'] = array_replace_recursive($default['gridPage'] ?? array(), $merged['gridPage']);
        }

        if (!empty($saved['profilePage']) && is_array($saved['profilePage'])) {
            if (!isset($merged['profilePage']) || !is_array($merged['profilePage'])) {
                $merged['profilePage'] = array();
            }
            $merged['profilePage'] = array_replace_recursive($default['profilePage'] ?? array(), $saved['profilePage']);
        }

        if (!empty($saved['devicesPage']) && is_array($saved['devicesPage'])) {
            if (!isset($merged['devicesPage']) || !is_array($merged['devicesPage'])) {
                $merged['devicesPage'] = array();
            }
            $merged['devicesPage'] = array_replace_recursive($default['devicesPage'] ?? array(), $saved['devicesPage']);
            if (!empty($saved['devicesPage']['devices']) && is_array($saved['devicesPage']['devices'])) {
            $merged['devicesPage']['devices'] = $saved['devicesPage']['devices'];
            }
        }

        if (!empty($saved['activePage']) && is_string($saved['activePage'])) {
            $merged['activePage'] = $saved['activePage'] === 'settings' ? 'profile' : $saved['activePage'];
            if ($merged['activePage'] === 'category') {
                $merged['activePage'] = 'grid';
            }
        } else {
            $merged['activePage'] = 'home';
        }

        return $merged;
    }

    /**
     * Factory home mosaic for new accounts and Reset.
     * Loaded from Config/app_home_default_cards.json (present studio UI).
     */
    private function getDefaultStarterCards(): array
    {
        $fromFile = $this->loadDefaultStarterCardsFromFile();
        if ($fromFile !== array()) {
            return $fromFile;
        }

        // Fallback if the JSON file is missing — keep a minimal mosaic.
        return array(
            array(
                'id' => 'user-modules',
                'type' => 'stack',
                'gridArea' => 'user-modules',
                'title' => '67',
                'label' => '67',
                'icon' => 'fa-th-large',
                'color' => '#7c3aed',
                'textColor' => '#ffffff',
                'iconColor' => '#ffffff',
                'col' => 1,
                'row' => 1,
                'colSpan' => 1,
                'rowSpan' => 2,
                'borderRadius' => 0,
                'defaultFace' => 0,
                'rotationDir' => 'ltr',
                'orientation' => 'vertical',
                'sameFaceColor' => true,
                'faces' => array(
                    array('type' => 'stack', 'title' => '67', 'label' => '67', 'subtitle' => 'User Modules', 'icon' => 'fa-th-large', 'color' => '#7c3aed', 'textColor' => '#ffffff', 'iconColor' => '#ffffff'),
                    array('type' => 'stack', 'title' => 'Online', 'icon' => 'fa-chart-bar', 'color' => '#7c3aed', 'textColor' => '#ffffff', 'iconColor' => '#ffffff'),
                    array('type' => 'stack', 'title' => 'Active', 'icon' => 'fa-chart-line', 'color' => '#7c3aed', 'textColor' => '#ffffff', 'iconColor' => '#ffffff'),
                    array('type' => 'stack', 'title' => 'Users', 'icon' => 'fa-th', 'color' => '#7c3aed', 'textColor' => '#ffffff', 'iconColor' => '#ffffff'),
                ),
            ),
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadDefaultStarterCardsFromFile(): array
    {
        $candidates = array();
        if (defined('APPPATH')) {
            $candidates[] = rtrim(APPPATH, '/\\') . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'app_home_default_cards.json';
        }
        $candidates[] = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'app_home_default_cards.json';

        $path = null;
        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                $path = $candidate;
                break;
            }
        }
        if ($path === null) {
            log_message('error', 'App home default cards JSON not found.');
            return array();
        }

        $raw = @file_get_contents($path);
        if (!is_string($raw) || trim($raw) === '') {
            log_message('error', 'App home default cards JSON empty: ' . $path);
            return array();
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded) || $decoded === array()) {
            log_message('error', 'App home default cards JSON invalid: ' . $path);
            return array();
        }

        // File may be a bare cards array or { "cards": [...] }.
        if (isset($decoded['cards']) && is_array($decoded['cards'])) {
            $decoded = $decoded['cards'];
        }

        $cards = array();
        foreach ($decoded as $card) {
            if (!is_array($card) || empty($card['id'])) {
                continue;
            }
            if (empty($card['gridArea'])) {
                $card['gridArea'] = (string) $card['id'];
            }
            $cards[] = $card;
        }

        return $cards;
    }

    /**
     * Normalize saved cards only — no default/demo card injection.
     */
    private function normalizeSavedCards(array $savedCards): array
    {
        $merged = array();

        foreach ($savedCards as $savedCard) {
            if (!is_array($savedCard)) {
                continue;
            }

            $card = $savedCard;
            $id = isset($card['id']) ? (string) $card['id'] : '';
            if ($id === '') {
                $card['id'] = 'card-' . (int) round(microtime(true) * 1000);
            }

            $savedFaces = (isset($card['faces']) && is_array($card['faces'])) ? $card['faces'] : array();
            if (empty($savedFaces)) {
                $card['faces'] = $this->emptyFacesForCard($card);
            } else {
                $faces = array();
                $padFaces = $this->emptyFacesForCard($card);
                for ($fi = 0; $fi < 4; $fi++) {
                    $sf = (isset($savedFaces[$fi]) && is_array($savedFaces[$fi])) ? $savedFaces[$fi] : array();
                    // Empty slots get distinct placeholders — never blank {} that inherits card title on every side.
                    if ($this->isBlankHomeFace($sf)) {
                        $sf = isset($padFaces[$fi]) && is_array($padFaces[$fi]) ? $padFaces[$fi] : $sf;
                    }
                    $faces[] = $sf;
                }
                $card['faces'] = $this->diversifyDuplicateHomeFaces($card, $faces);
            }

            $merged[] = $card;
        }

        return $merged;
    }

    /**
     * Distinct accent colors for cube faces (purple / navy / teal / green).
     *
     * @return array<int, string>
     */
    private function distinctHomeFaceColors(): array
    {
        return array('#7c3aed', '#1e3a8a', '#0d9488', '#16a34a');
    }

    private function isBlankHomeFace(array $face): bool
    {
        $title = trim((string) ($face['title'] ?? $face['label'] ?? ''));
        $subtitle = trim((string) ($face['subtitle'] ?? ''));
        $value = array_key_exists('value', $face) ? trim((string) $face['value']) : '';
        $image = trim((string) ($face['image'] ?? ''));

        return $title === '' && $subtitle === '' && $value === '' && $image === '';
    }

    private function homeFaceContentKey(array $face): string
    {
        return strtolower(trim((string) ($face['title'] ?? $face['label'] ?? '')))
            . '|' . strtolower(trim((string) ($face['subtitle'] ?? '')))
            . '|' . strtolower(trim((string) (array_key_exists('value', $face) ? $face['value'] : '')))
            . '|' . trim((string) ($face['image'] ?? ''));
    }

    /**
     * Repair faces that were cloned identically across all 4 sides (looks static while rotating).
     *
     * @param array<string, mixed> $card
     * @param array<int, array<string, mixed>> $faces
     * @return array<int, array<string, mixed>>
     */
    private function diversifyDuplicateHomeFaces(array $card, array $faces): array
    {
        if (count($faces) < 2) {
            return $faces;
        }

        $palette = $this->distinctHomeFaceColors();
        $icons = array('fa-cube', 'fa-star', 'fa-bolt', 'fa-heart');
        $sameFaceColor = !empty($card['sameFaceColor']);
        $primaryKey = $this->homeFaceContentKey(isset($faces[0]) && is_array($faces[0]) ? $faces[0] : array());

        for ($fi = 1; $fi < 4; $fi++) {
            $face = (isset($faces[$fi]) && is_array($faces[$fi])) ? $faces[$fi] : array();
            if ($this->isBlankHomeFace($face) || $this->homeFaceContentKey($face) === $primaryKey) {
                $faces[$fi] = array(
                    'type' => $face['type'] ?? ($card['type'] ?? 'link'),
                    'title' => 'Face ' . ($fi + 1),
                    'label' => 'Face ' . ($fi + 1),
                    'subtitle' => '',
                    'icon' => $icons[$fi] ?? ($face['icon'] ?? ($card['icon'] ?? 'fa-cube')),
                    'color' => $sameFaceColor
                        ? ($face['color'] ?? ($card['color'] ?? ($palette[$fi] ?? '#6366f1')))
                        : ($palette[$fi] ?? ($card['color'] ?? '#6366f1')),
                    'textColor' => $face['textColor'] ?? ($card['textColor'] ?? '#ffffff'),
                    'iconColor' => $face['iconColor'] ?? ($card['iconColor'] ?? '#ffffff'),
                );
            }
        }

        return $faces;
    }

    private function emptyFacesForCard(array $card): array
    {
        $label = $card['label'] ?? $card['title'] ?? 'Face 1';
        $icon = $card['icon'] ?? 'fa-cube';
        $color = $card['color'] ?? '#6366f1';
        $type = $card['type'] ?? 'link';
        $palette = $this->distinctHomeFaceColors();
        $sameFaceColor = !empty($card['sameFaceColor']);
        $faceColor = static function (int $fi) use ($sameFaceColor, $color, $palette): string {
            if ($fi === 0) {
                return $color;
            }
            return $sameFaceColor ? $color : ($palette[$fi] ?? $color);
        };

        if ($type === 'stat') {
            return array(
                array('value' => $card['value'] ?? '0', 'label' => $label, 'icon' => $icon, 'color' => $faceColor(0)),
                array('title' => 'Face 2', 'icon' => 'fa-star', 'color' => $faceColor(1)),
                array('title' => 'Face 3', 'icon' => 'fa-bolt', 'color' => $faceColor(2)),
                array('title' => 'Face 4', 'icon' => 'fa-heart', 'color' => $faceColor(3)),
            );
        }

        if ($type === 'image') {
            $image = trim((string) ($card['image'] ?? ''));
            return array(
                array('title' => $label, 'subtitle' => $card['subtitle'] ?? '', 'image' => $image, 'color' => $faceColor(0)),
                // Secondary faces stay distinct — do not clone the same image onto every side.
                array('title' => 'Face 2', 'icon' => 'fa-star', 'color' => $faceColor(1)),
                array('title' => 'Face 3', 'icon' => 'fa-bolt', 'color' => $faceColor(2)),
                array('title' => 'Face 4', 'icon' => 'fa-heart', 'color' => $faceColor(3)),
            );
        }

        return array(
            array('title' => $label, 'subtitle' => $card['subtitle'] ?? '', 'icon' => $icon, 'color' => $faceColor(0)),
            array('title' => 'Face 2', 'icon' => 'fa-star', 'color' => $faceColor(1)),
            array('title' => 'Face 3', 'icon' => 'fa-bolt', 'color' => $faceColor(2)),
            array('title' => 'Face 4', 'icon' => 'fa-heart', 'color' => $faceColor(3)),
        );
    }

    /** @deprecated Use normalizeSavedCards() */
    private function mergeCardsWithDefaults(array $defaultCards, array $savedCards): array
    {
        return $this->normalizeSavedCards($savedCards);
    }

    public function saveLayout(int $userId, array $layout): bool
    {
        return $this->saveSettings($userId, $layout, null);
    }

    public function getSchema(int $userId): array
    {
        $this->ensureTable();
        $this->ensureUserDefaults($userId);
        $this->syncSchemaFromLayout($userId);
        $record = $this->getRecord($userId);

        if (!$record || $record['schema_json'] === null || $record['schema_json'] === '') {
            return array();
        }

        $data = json_decode($record['schema_json'], true);
        if (!is_array($data) || empty($data)) {
            return array();
        }

        // Live identity + Recent Activity on top of the saved Home schema.
        $data = $this->applyLiveUserProfileToSchema($data, $userId);
        if (class_exists(\App\Libraries\GridSduiSchemaBuilder::class)) {
            $ssoToken = '';
            if ($userId > 0 && class_exists(\App\Libraries\GridWebSso::class)) {
                $ssoToken = \App\Libraries\GridWebSso::issue($userId);
            }
            $data = \App\Libraries\GridSduiSchemaBuilder::patchRecentActivity($data, $userId, $ssoToken);
        }

        $schema = $this->attachGridDataSource($this->normalizeSmartphoneBreakpoints($data), $userId);
        $schema = $this->prepareRatingOpenUrlForClient($schema, $userId);

        return \App\Libraries\AppHomeSchemaSigner::attachSignature($schema);
    }

    /**
     * Replace stale identity values in a saved mobile schema with the current
     * users-table values. Only existing schema keys are updated; no component,
     * parameter, binding, or schema structure is added or removed.
     *
     * @param array<string, mixed> $schema
     * @return array<string, mixed>
     */
    public function applyLiveUserProfileToSchema(array $schema, int $userId): array
    {
        if ($userId <= 0 || $schema === array()) {
            return $schema;
        }

        $profile = $this->getUserProfile($userId);
        $name = trim((string) ($profile['name'] ?? ''));
        $email = trim((string) ($profile['email'] ?? ''));
        $phone = trim((string) ($profile['phone'] ?? ''));
        $memberId = trim((string) ($profile['memberId'] ?? ''));
        $avatarUrl = trim((string) ($profile['avatarUrl'] ?? ''));
        $initials = $this->profileInitials($name, $email);

        if (isset($schema['state']['session']['user']) && is_array($schema['state']['session']['user'])) {
            $this->replaceExistingValues($schema['state']['session']['user'], array(
                'email' => $email,
                'displayName' => $name,
                'greetingName' => $name,
                'firstName' => $this->profileFirstName($name, $email),
                'initials' => $initials,
                'avatarUrl' => $avatarUrl,
                'phone' => $phone,
                'memberId' => $memberId,
            ));
        }

        if (isset($schema['components']['profile-header']) && is_array($schema['components']['profile-header'])) {
            $header =& $schema['components']['profile-header'];
            $this->replaceExistingValues($header, array(
                'name' => $name,
                'title' => $name,
                'fullName' => $name,
                'displayName' => $name,
                'email' => $email,
                'phone' => $phone,
                'memberId' => $memberId,
                'eyebrow' => $memberId,
                'meta' => $memberId,
                'chip' => $phone,
                'phoneChip' => $phone,
                'avatarText' => $initials,
                'avatarUrl' => $avatarUrl,
                'imageUrl' => $avatarUrl,
            ));

            if (isset($header['avatar']) && is_array($header['avatar'])) {
                $this->replaceExistingValues($header['avatar'], array(
                    'initials' => $initials,
                    'imageUrl' => $avatarUrl,
                ));
                $this->replaceBindingFallback($header['avatar'], 'imageUrlBinding', $avatarUrl);
            }

            if (isset($header['fields']) && is_array($header['fields'])) {
                $fieldValues = array(
                    'memberId' => $memberId,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                );
                foreach ($fieldValues as $field => $value) {
                    if (!isset($header['fields'][$field]) || !is_array($header['fields'][$field])) {
                        continue;
                    }
                    if (array_key_exists('value', $header['fields'][$field])) {
                        $header['fields'][$field]['value'] = $value;
                    }
                    $this->replaceBindingFallback($header['fields'][$field], 'binding', $value);
                }
            }

            $fallbacks = array(
                'nameBinding' => $name,
                'titleBinding' => $name,
                'emailBinding' => $email,
                'phoneBinding' => $phone,
                'memberIdBinding' => $memberId,
                'eyebrowBinding' => $memberId,
                'avatarUrlBinding' => $avatarUrl,
                'imageUrlBinding' => $avatarUrl,
                'avatarTextBinding' => $initials,
            );
            foreach ($fallbacks as $binding => $value) {
                $this->replaceBindingFallback($header, $binding, $value);
            }
            unset($header);
        }

        if ($avatarUrl === '' && class_exists(\App\Libraries\GridSduiSchemaBuilder::class)) {
            $liveHeader = \App\Libraries\GridSduiSchemaBuilder::liveHeaderProfile($userId);
            $avatarUrl = trim((string) ($liveHeader['imageUrl'] ?? ''));
            if ($initials === '' && trim((string) ($liveHeader['initial'] ?? '')) !== '') {
                $initials = trim((string) $liveHeader['initial']);
            }
        }
        if (isset($schema['components']['grid-page-header']['actions']) && is_array($schema['components']['grid-page-header']['actions'])) {
            foreach ($schema['components']['grid-page-header']['actions'] as $actionIndex => $headerAction) {
                if (!is_array($headerAction)) {
                    continue;
                }
                $isAvatar = (string) ($headerAction['id'] ?? '') === 'grid-header-avatar'
                    || (string) ($headerAction['type'] ?? '') === 'avatarButton';
                if (!$isAvatar || $avatarUrl === '') {
                    continue;
                }
                $schema['components']['grid-page-header']['actions'][$actionIndex]['imageUrl'] = $avatarUrl;
                $schema['components']['grid-page-header']['actions'][$actionIndex]['avatarUrl'] = $avatarUrl;
                $schema['components']['grid-page-header']['actions'][$actionIndex]['image'] = $avatarUrl;
                if (!isset($schema['components']['grid-page-header']['actions'][$actionIndex]['appearance']) || !is_array($schema['components']['grid-page-header']['actions'][$actionIndex]['appearance'])) {
                    $schema['components']['grid-page-header']['actions'][$actionIndex]['appearance'] = array();
                }
                $schema['components']['grid-page-header']['actions'][$actionIndex]['appearance']['imageUrl'] = $avatarUrl;
            }
        }
        if (isset($schema['content']['header']) && is_array($schema['content']['header']) && $avatarUrl !== '') {
            $schema['content']['header']['avatarUrl'] = $avatarUrl;
        }

        foreach (array('profile', 'profilePage') as $profileKey) {
            if (!isset($schema[$profileKey]) || !is_array($schema[$profileKey])) {
                continue;
            }
            $this->replaceExistingValues($schema[$profileKey], array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'memberId' => $memberId,
                'avatarUrl' => $avatarUrl,
            ));
        }

        return $schema;
    }

    /**
     * @param array<string, mixed> $target
     * @param array<string, mixed> $values
     */
    private function replaceExistingValues(array &$target, array $values): void
    {
        foreach ($values as $key => $value) {
            if (array_key_exists($key, $target)) {
                $target[$key] = $value;
            }
        }
    }

    /**
     * @param array<string, mixed> $target
     */
    private function replaceBindingFallback(array &$target, string $key, string $value): void
    {
        if (isset($target[$key]) && is_array($target[$key]) && array_key_exists('fallback', $target[$key])) {
            $target[$key]['fallback'] = $value;
        }
    }

    private function profileFirstName(string $name, string $email): string
    {
        $name = trim($name);
        if ($name !== '' && strcasecmp($name, 'Guest') !== 0) {
            $parts = preg_split('/\s+/', $name);

            return trim((string) ($parts[0] ?? $name));
        }

        return strpos($email, '@') !== false ? (string) strstr($email, '@', true) : $email;
    }

    private function profileInitials(string $name, string $email): string
    {
        $name = trim($name);
        if ($name === '' || strcasecmp($name, 'Guest') === 0) {
            $name = $this->profileFirstName('', $email);
        }
        $parts = array_values(array_filter(preg_split('/\s+/', $name) ?: array()));
        if ($parts === array()) {
            return 'U';
        }
        $first = mb_substr((string) $parts[0], 0, 1);
        $last = count($parts) > 1 ? mb_substr((string) $parts[count($parts) - 1], 0, 1) : '';

        return strtoupper($first . $last);
    }

    /**
     * Keep profile-rating as open-url (same keys). Refresh ?grid_sso= so the
     * phone WebView can open the web Rating UI (stars + comment) signed in.
     *
     * @param array<string, mixed> $schema
     * @return array<string, mixed>
     */
    public function prepareRatingOpenUrlForClient(array $schema, int $userId): array
    {
        if ($userId < 1) {
            return $schema;
        }

        if (!isset($schema['actionRegistry']) || !is_array($schema['actionRegistry'])) {
            return $schema;
        }

        $action = $schema['actionRegistry']['profile-rating'] ?? null;
        if (!is_array($action)) {
            return $schema;
        }

        // Always rebuild a clean public HTML URL — never reuse a path that was
        // accidentally joined onto /v8/api_creation.php during schema serve/build.
        $ratingUrl = $this->publicRatingPageUrl($userId);

        // Same open-url shape the app already handles — only refresh url values.
        $action['type'] = 'open-url';
        $action['url'] = $ratingUrl;
        $action['route'] = $ratingUrl;
        $action['title'] = $action['title'] ?? 'Rating';
        $action['openIn'] = $action['openIn'] ?? 'inAppWebView';
        if (!isset($action['webView']) || !is_array($action['webView'])) {
            $action['webView'] = array();
        }
        $action['webView'] = array_merge(array(
            'javaScriptEnabled' => true,
            'sharedCookies' => true,
            'clearCache' => false,
            'showAppBar' => false,
            'showToolbar' => false,
            'hideAppBar' => true,
            'displayMode' => 'fullscreen',
        ), $action['webView']);
        if (!isset($action['destination']) || !is_array($action['destination'])) {
            $action['destination'] = array();
        }
        $action['destination']['url'] = $ratingUrl;
        $action['destination']['openIn'] = $action['destination']['openIn'] ?? 'inAppWebView';
        $action['destination']['title'] = $action['destination']['title'] ?? 'Rating';
        $action['destination']['sharedCookies'] = true;
        $action['destination']['showAppBar'] = false;
        $action['destination']['showToolbar'] = false;
        $action['destination']['hideAppBar'] = true;

        $schema['actionRegistry']['profile-rating'] = $action;

        return $schema;
    }

    /**
     * Absolute HTML Rating page for in-app WebView.
     * Always /index.php/app-home-page/rating — never /v8/api_creation.php/...
     */
    public function publicRatingPageUrl(int $userId = 0): string
    {
        $url = $this->publicAppHomeWebUrl('app-home-page/rating');
        if ($userId > 0 && class_exists(\App\Libraries\GridWebSso::class)) {
            $sso = \App\Libraries\GridWebSso::issue($userId);
            if ($sso !== '') {
                $url = \App\Libraries\GridWebSso::appendToken($url, $sso);
            }
        }

        return $url;
    }

    /**
     * Absolute App Home JSON API base (.../index.php/app-home-page/api).
     */
    public function publicAppHomeApiBaseUrl(): string
    {
        return $this->publicAppHomeWebUrl('app-home-page/api');
    }

    /**
     * Build a public app-home web URL that cannot be rooted on a v8 API script.
     */
    public function publicAppHomeWebUrl(string $path): string
    {
        $path = trim(str_replace('\\', '/', $path), '/');
        if ($path === '') {
            $path = 'app-home-page';
        }

        $url = '';
        if (class_exists(\App\Libraries\GridWebSso::class)) {
            $url = \App\Libraries\GridWebSso::publicWebUrl($path);
        } elseif (function_exists('site_url')) {
            helper('url');
            $url = (string) site_url($path);
        }

        $url = $this->sanitizeAppHomePublicUrl($url, $path);
        if (class_exists(\App\Libraries\GridWebSso::class)) {
            $url = \App\Libraries\GridWebSso::canonicalizePublicUrl($url);
        }

        return rtrim($url, '/');
    }

    /**
     * Drop accidental /v8/api_creation.php (etc.) prefixes from app-home URLs.
     */
    public function sanitizeAppHomePublicUrl(string $url, string $path): string
    {
        $url = trim($url);
        $path = trim(str_replace('\\', '/', $path), '/');
        $poison = '#/(?:v8/)?(?:api_creation|grid_api_creation|iot_api_creation)\.php(?=/|\?|$)#i';

        if ($url !== '' && !preg_match($poison, $url) && stripos($url, $path) !== false) {
            return $url;
        }

        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443)
            || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
        $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
        if ($host === '') {
            $host = 'efactor.tech';
            $https = true;
        }
        $hostName = preg_replace('/:\d+$/', '', $host) ?: $host;
        if (!in_array($hostName, array('localhost', '127.0.0.1', '::1'), true)) {
            $https = true;
        }

        // If we had a usable host from a poisoned URL, keep that host.
        if ($url !== '') {
            $parts = parse_url($url);
            if (is_array($parts) && !empty($parts['host'])) {
                $host = (string) $parts['host'];
                if (!empty($parts['port'])) {
                    $host .= ':' . (int) $parts['port'];
                }
                $scheme = strtolower((string) ($parts['scheme'] ?? ''));
                if ($scheme === 'https') {
                    $https = true;
                } elseif ($scheme === 'http') {
                    $hostOnly = preg_replace('/:\d+$/', '', strtolower((string) $parts['host'])) ?: '';
                    if (!in_array($hostOnly, array('localhost', '127.0.0.1', '::1'), true)) {
                        $https = true;
                    } else {
                        $https = false;
                    }
                }
            }
        }

        return ($https ? 'https' : 'http') . '://' . $host . '/index.php/' . $path;
    }

    /**
     * Home schema only stores a Grid stub. The app loads the real page from
     * get_grid. Without this data source it keeps the last disk-cached Grid
     * schema (old scanner action, old QR).
     *
     * @param array<string, mixed> $schema
     * @return array<string, mixed>
     */
    public function attachGridDataSource(array $schema, int $userId): array
    {
        if ($userId < 1) {
            return $schema;
        }

        $endpoint = $this->getPublicGridApiBaseUrl();
        $endpoint = rtrim($endpoint, '/');
        if (stripos($endpoint, 'action=get_grid') === false) {
            $endpoint .= (strpos($endpoint, '?') === false ? '?' : '&') . 'action=get_grid';
        }

        if (!isset($schema['dataSources']) || !is_array($schema['dataSources'])) {
            $schema['dataSources'] = array();
        }

        $schema['dataSources']['grid'] = array(
            'type' => 'rest',
            'endpoint' => $endpoint,
            'method' => 'GET',
            'queryParameters' => array(
                'user_id' => $userId,
                'schema_rev' => 'scanner-qr-7',
            ),
            'headers' => array('Accept' => 'application/json'),
            'responseMapping' => array(
                'schema' => '$.schema',
                'page' => '$.schema.page',
                'components' => '$.schema.components',
                'actions' => '$.schema.actions',
                'iconRegistry' => '$.schema.iconRegistry',
                'content' => '$.schema.content',
                'stats' => '$.schema.content.stats',
            ),
            'cachePolicy' => 'network-first',
            'cacheTtlSec' => 60,
            'timeoutMs' => 30000,
            'published' => true,
            'errorPolicy' => array(
                'useCachedData' => true,
                'componentId' => 'grid-error',
                'clearHostSession' => false,
                'navigateToLogin' => false,
            ),
        );

        return $schema;
    }

    /**
     * Rebuild and persist App Home schema when layout/builder sources drift.
     * Failures leave the existing schema untouched.
     *
     * @return array{synced:bool,changed:bool,saved:bool,reason?:string}
     */
    public function syncSchemaFromLayout(int $userId, string $userEmail = ''): array
    {
        static $syncing = false;
        if ($syncing) {
            return array('synced' => false, 'changed' => false, 'saved' => false, 'reason' => 'reentrant');
        }
        if ($userId <= 0) {
            return array('synced' => false, 'changed' => false, 'saved' => false, 'reason' => 'invalid_user');
        }

        $syncing = true;
        try {
            $this->ensureTable();
            $record = $this->getRecord($userId);
            if (!$record || empty($record['layout_json'])) {
                return array('synced' => false, 'changed' => false, 'saved' => false, 'reason' => 'no_layout');
            }

            $layout = json_decode((string) $record['layout_json'], true);
            if (!is_array($layout) || $layout === array()) {
                return array('synced' => false, 'changed' => false, 'saved' => false, 'reason' => 'invalid_layout');
            }

            $navBefore = json_encode(array(
                $layout['bottomNavCategoryTarget'] ?? null,
                $layout['bottomNav'] ?? null,
            ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $layout = self::applySharedBottomNav($layout);
            $navAfter = json_encode(array(
                $layout['bottomNavCategoryTarget'] ?? null,
                $layout['bottomNav'] ?? null,
            ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $persistLayout = ($navBefore !== $navAfter)
                ? json_decode(json_encode($layout), true)
                : null;
            if (!is_array($persistLayout)) {
                $persistLayout = null;
            }

            if ($userEmail === '') {
                $profile = $this->getUserProfile($userId);
                $userEmail = $profile['email'] ?? '';
            }

            $headerBefore = json_encode(array(
                'header' => $layout['header'] ?? null,
                'homeTemplate' => $layout['homeTemplate'] ?? null,
                'cardBorderRadius' => $layout['theme']['cardBorderRadius'] ?? null,
                'showWelcome' => $layout['theme']['showWelcome'] ?? null,
                'themeHomeTemplate' => $layout['theme']['homeTemplate'] ?? null,
                'squareTiles' => $layout['theme']['squareTiles'] ?? null,
                'gridRowHeight' => $layout['theme']['gridRowHeight'] ?? null,
                'cardsRadii' => $this->layoutCardsRadiiFingerprint($layout),
            ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $layout = $this->applyUserProfileToLayout($layout, $userId);
            $layout = $this->sanitizeLayoutForSchema($layout);
            $headerAfter = json_encode(array(
                'header' => $layout['header'] ?? null,
                'homeTemplate' => $layout['homeTemplate'] ?? null,
                'cardBorderRadius' => $layout['theme']['cardBorderRadius'] ?? null,
                'showWelcome' => $layout['theme']['showWelcome'] ?? null,
                'themeHomeTemplate' => $layout['theme']['homeTemplate'] ?? null,
                'squareTiles' => $layout['theme']['squareTiles'] ?? null,
                'gridRowHeight' => $layout['theme']['gridRowHeight'] ?? null,
                'cardsRadii' => $this->layoutCardsRadiiFingerprint($layout),
            ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($headerBefore !== $headerAfter) {
                $persistLayout = json_decode(json_encode($layout), true);
                if (!is_array($persistLayout)) {
                    $persistLayout = null;
                }
            }
            $positionsBefore = $this->cardPositionFingerprint($layout);
            $layout = $this->alignAccountCardPositions($userId, $layout);
            if ($positionsBefore !== $this->cardPositionFingerprint($layout)) {
                $persistLayout = json_decode(json_encode($layout), true);
                if (!is_array($persistLayout)) {
                    $persistLayout = null;
                }
            }
            $cfg = $this->buildSchemaConfig($userId, $userEmail);
            $fingerprint = $this->layoutSourceFingerprint($layout, $cfg);

            $existingSchema = array();
            if (!empty($record['schema_json'])) {
                $decoded = json_decode((string) $record['schema_json'], true);
                if (is_array($decoded)) {
                    $existingSchema = $decoded;
                }
            }

            $storedFp = '';
            if ($existingSchema !== array()) {
                $storedFp = (string) ($existingSchema['sourceFingerprint']
                    ?? ($existingSchema['meta']['sourceFingerprint']
                    ?? ($existingSchema['meta']['layoutFingerprint'] ?? '')));
            }

            if ($fingerprint !== '' && $storedFp !== '' && $fingerprint === $storedFp && $existingSchema !== array()) {
                if (is_array($persistLayout)) {
                    $this->saveSettings($userId, $persistLayout, null);
                }

                return array('synced' => true, 'changed' => false, 'saved' => false);
            }

            $schema = \App\Libraries\AppHomeSchemaBuilder::build($layout, $cfg);
            if (empty($schema)) {
                log_message('error', 'App home schema auto-sync build failed for user_id=' . $userId);
                if (is_array($persistLayout)) {
                    $this->saveSettings($userId, $persistLayout, null);
                }

                return array(
                    'synced' => $existingSchema !== array(),
                    'changed' => false,
                    'saved' => false,
                    'reason' => 'build_failed',
                );
            }

            if ($fingerprint !== '') {
                $schema['sourceFingerprint'] = $fingerprint;
                if (!isset($schema['meta']) || !is_array($schema['meta'])) {
                    $schema['meta'] = array();
                }
                $schema['meta']['sourceFingerprint'] = $fingerprint;
                $schema['meta']['layoutFingerprint'] = $fingerprint;
                $schema['meta']['mappingSyncedAt'] = date('c');
            }

            $beforeHash = $this->schemaContentHash($existingSchema);
            $afterHash = $this->schemaContentHash($schema);
            if ($beforeHash !== '' && $beforeHash === $afterHash) {
                if (is_array($persistLayout)) {
                    $this->saveSettings($userId, $persistLayout, null);
                }

                return array('synced' => true, 'changed' => false, 'saved' => false);
            }

            $saved = $this->saveSettings($userId, array(), $schema);
            if ($saved && is_array($persistLayout)) {
                $this->saveSettings($userId, $persistLayout, null);
            }
            if (!$saved) {
                return array(
                    'synced' => false,
                    'changed' => true,
                    'saved' => false,
                    'reason' => 'save_failed',
                );
            }

            return array('synced' => true, 'changed' => true, 'saved' => true);
        } catch (\Throwable $exception) {
            log_message('error', 'App home schema auto-sync failed: ' . $exception->getMessage());

            return array(
                'synced' => false,
                'changed' => false,
                'saved' => false,
                'reason' => 'exception',
            );
        } finally {
            $syncing = false;
        }
    }

    /**
     * @param array<string, mixed> $layout
     * @param array<string, mixed> $cfg
     */
    public function layoutSourceFingerprint(array $layout, array $cfg = array()): string
    {
        $scriptPath = \App\Libraries\AppHomeSchemaBuilder::resolveScriptPath();
        $scriptHash = ($scriptPath && is_file($scriptPath)) ? (string) hash_file('sha256', $scriptPath) : '';
        $jsPath = null;
        if (defined('FCPATH')) {
            $candidate = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'app_home_schema.js';
            if (is_file($candidate)) {
                $jsPath = $candidate;
            }
        }
        $jsHash = ($jsPath && is_file($jsPath)) ? (string) hash_file('sha256', $jsPath) : '';

        $payload = array(
            'layout' => $layout,
            'cfg' => array(
                'userId' => $cfg['userId'] ?? null,
                'iotApiUrl' => $cfg['iotApiUrl'] ?? '',
                'schemaApiUrl' => $cfg['schemaApiUrl'] ?? '',
                'logoUrl' => $cfg['logoUrl'] ?? '',
                'filesBase' => $cfg['filesBase'] ?? '',
            ),
            'builderScriptHash' => $scriptHash,
            'studioBuilderHash' => $jsHash,
        );
        $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $encoded === false ? '' : hash('sha256', $encoded);
    }

    /**
     * @param array<string, mixed> $schema
     */
    private function schemaContentHash(array $schema): string
    {
        if ($schema === array()) {
            return '';
        }
        $copy = $schema;
        unset(
            $copy['generatedAt'],
            $copy['updatedAt'],
            $copy['savedAt'],
            $copy['signature'],
            $copy['sourceFingerprint']
        );
        if (isset($copy['meta']) && is_array($copy['meta'])) {
            unset(
                $copy['meta']['mappingSyncedAt'],
                $copy['meta']['sourceFingerprint'],
                $copy['meta']['layoutFingerprint'],
                $copy['meta']['signature']
            );
        }
        $encoded = json_encode($copy, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $encoded === false ? '' : hash('sha256', $encoded);
    }

    /**
     * Persist default layout + SDUI schema for new users (no studio visit required).
     * Safe to call repeatedly — skips when layout and schema already exist.
     */
    public function ensureUserDefaults(int $userId, string $userEmail = ''): bool
    {
        if ($userId <= 0) {
            return false;
        }

        $this->ensureTable();
        $record = $this->getRecord($userId);
        $hasLayout = $record && !empty($record['layout_json']);
        $hasSchema = $record && !empty($record['schema_json']);

        if ($hasLayout && $hasSchema) {
            return false;
        }

        if ($userEmail === '') {
            $profile = $this->getUserProfile($userId);
            $userEmail = $profile['email'] ?? '';
        }

        $defaultLayout = $this->getDefaultLayout($userEmail, $userId);
        $defaultLayout = $this->applyUserProfileToLayout($defaultLayout, $userId);

        $savedLayout = array();
        if ($hasLayout) {
            $decoded = json_decode((string) $record['layout_json'], true);
            if (is_array($decoded)) {
                $savedLayout = $decoded;
            }
        }

        $layout = $this->mergeWithDefaults($defaultLayout, $savedLayout);
        $layout = $this->applyUserProfileToLayout($layout, $userId);
        $layout = $this->sanitizeLayoutForSchema($layout);

        $schemaPayload = null;

        if (!$hasSchema) {
            $schema = \App\Libraries\AppHomeSchemaBuilder::build(
                $layout,
                $this->buildSchemaConfig($userId, $userEmail)
            );

            if (empty($schema)) {
                log_message('error', 'App home auto-provision: schema build failed for user_id=' . $userId);
                if ($hasLayout) {
                    return false;
                }

                return $this->saveSettings($userId, $layout, null);
            }

            $cfg = $this->buildSchemaConfig($userId, $userEmail);
            $fingerprint = $this->layoutSourceFingerprint($layout, $cfg);
            if ($fingerprint !== '') {
                $schema['sourceFingerprint'] = $fingerprint;
                if (!isset($schema['meta']) || !is_array($schema['meta'])) {
                    $schema['meta'] = array();
                }
                $schema['meta']['sourceFingerprint'] = $fingerprint;
                $schema['meta']['layoutFingerprint'] = $fingerprint;
            }

            $schemaPayload = $schema;
        }

        if ($hasLayout && $schemaPayload === null) {
            return false;
        }

        if (!$hasLayout && $schemaPayload === null) {
            return $this->saveSettings($userId, $layout, null);
        }

        return $this->saveSettings($userId, $hasLayout ? array() : $layout, $schemaPayload);
    }

    /**
     * Schema builder cfg mirror of AHP_CONFIG in the studio view.
     */
    public function buildSchemaConfig(int $userId, string $userEmail = ''): array
    {
        helper('url');

        $iotApiUrl = 'https://efactor.tech/v8/iot_api_creation.php';
        try {
            $iotModel = new IotDashboardModel();
            $iotApiUrl = $iotModel->getPublicApiBaseUrl();
        } catch (\Throwable $e) {
            log_message('debug', 'App home schema cfg: IoT API URL fallback used.');
        }

        $iotApiUrl = $this->normalizeAppHomeEndpoint(
            $iotApiUrl,
            'iot_api_creation.php',
            'https://efactor.tech/v8/iot_api_creation.php'
        );

        if ($userEmail === '') {
            $profile = $this->getUserProfile($userId);
            $userEmail = $profile['email'] ?? '';
        } else {
            $profile = $this->getUserProfile($userId);
        }

        $circleConnectUrl = 'https://efactor.tech/index.php/grid-chat';
        if (class_exists(\App\Libraries\GridWebSso::class)) {
            $circleConnectUrl = \App\Libraries\GridWebSso::publicWebUrl('grid-chat');
        }

        // Same web Rating UI (stars + comment). Clean public URL + optional SSO.
        // Never join onto /v8/api_creation.php (that returns JSON, not HTML).
        $ratingPageUrl = $this->publicRatingPageUrl($userId);

        return array(
            'userId' => $userId,
            'userEmail' => $userEmail,
            'loginUserProfile' => $profile,
            'logoUrl' => 'https://efactor.tech/files/home/Logo_new.png',
            'schemaApiUrl' => $this->getSchemaApiUrl($userId),
            'apiBase' => $this->publicAppHomeApiBaseUrl(),
            'ratingPageUrl' => $ratingPageUrl,
            'filesBase' => rtrim(base_url('files'), '/'),
            'iotApiUrl' => $iotApiUrl,
            'circleConnectUrl' => $circleConnectUrl,
        );
    }

    /**
     * Overlay live users-table fields onto layout profile / welcome email.
     */
    public function applyUserProfileToLayout(array $layout, int $userId): array
    {
        $profile = $this->getUserProfile($userId);

        if (!isset($layout['profilePage']) || !is_array($layout['profilePage'])) {
            $layout['profilePage'] = array();
        }

        $language = $layout['profilePage']['language'] ?? 'English (India)';
        $notificationsEnabled = array_key_exists('notificationsEnabled', $layout['profilePage'])
            ? (bool) $layout['profilePage']['notificationsEnabled']
            : true;
        $languageOptions = $layout['profilePage']['languageOptions'] ?? null;
        $privacyPolicyText = $layout['profilePage']['privacyPolicyText'] ?? ($layout['profilePage']['privacyPolicy'] ?? null);

        $layout['profilePage']['name'] = $profile['name'];
        $layout['profilePage']['email'] = $profile['email'];
        $layout['profilePage']['phone'] = $profile['phone'];
        $layout['profilePage']['memberId'] = $profile['memberId'];
        $layout['profilePage']['avatarUrl'] = $profile['avatarUrl'] ?? '';
        $layout['profilePage']['language'] = $language;
        $layout['profilePage']['notificationsEnabled'] = $notificationsEnabled;
        if (is_array($languageOptions) && $languageOptions !== array()) {
            $layout['profilePage']['languageOptions'] = $languageOptions;
        }
        if (is_string($privacyPolicyText) && trim($privacyPolicyText) !== '') {
            $layout['profilePage']['privacyPolicyText'] = trim($privacyPolicyText);
        }

        if ($profile['email'] !== '') {
            if (!isset($layout['welcome']) || !is_array($layout['welcome'])) {
                $layout['welcome'] = array();
            }
            $layout['welcome']['email'] = $profile['email'];
        }

        return $layout;
    }

    /**
     * Normalize layout fields before Node schema build (greeting encoding + unique gridArea).
     *
     * @param array<string, mixed> $layout
     * @return array<string, mixed>
     */
    public function sanitizeLayoutForSchema(array $layout): array
    {
        if (!isset($layout['welcome']) || !is_array($layout['welcome'])) {
            $layout['welcome'] = array();
        }

        $greeting = trim((string) ($layout['welcome']['greeting'] ?? ''));
        $greeting = preg_replace('/\x{FFFD}+/u', '', $greeting) ?? $greeting;
        $greeting = preg_replace('/^\?{2,}\s*/u', '', $greeting) ?? $greeting;
        $greeting = trim($greeting);
        if ($greeting === '' || preg_match('/^\?+$/', $greeting) === 1) {
            $greeting = 'Welcome back';
        }
        $layout['welcome']['greeting'] = $greeting;

        if (!isset($layout['header']) || !is_array($layout['header'])) {
            $layout['header'] = array();
        }
        // Shared defaults for every account — never leave required header fields empty
        // (QuickMart and similar templates previously saved blank title/subtitle).
        $layout['header']['title'] = $this->stripUnwantedHomeHeader((string) ($layout['header']['title'] ?? ''));
        $layout['header']['subtitle'] = $this->stripUnwantedHomeHeader((string) ($layout['header']['subtitle'] ?? ''));
        if ($layout['header']['title'] === '') {
            $layout['header']['title'] = 'eFactor · IoE';
        }
        if ($layout['header']['subtitle'] === '') {
            $layout['header']['subtitle'] = 'Smart insights & tools';
        }

        if (!isset($layout['theme']) || !is_array($layout['theme'])) {
            $layout['theme'] = array();
        }
        $staticUi = trim((string) ($layout['staticUiTemplate'] ?? ($layout['theme']['staticUiTemplate'] ?? '')));
        if ($staticUi !== '') {
            $layout['staticUiTemplate'] = $staticUi;
            $layout['theme']['staticUiTemplate'] = $staticUi;
        } else {
            $layout['staticUiTemplate'] = null;
            $layout['theme']['staticUiTemplate'] = null;
        }

        if (isset($layout['cards']) && is_array($layout['cards'])) {
            $used = array();
            foreach ($layout['cards'] as $i => $card) {
                if (!is_array($card)) {
                    continue;
                }
                $id = trim((string) ($card['id'] ?? ''));
                if ($id === '') {
                    $id = 'card-' . $i;
                    $layout['cards'][$i]['id'] = $id;
                }
                $area = $id;
                if (isset($used[$area])) {
                    $area = $id . '-' . $i;
                }
                $used[$area] = true;
                $layout['cards'][$i]['gridArea'] = $area;
                if (array_key_exists('borderRadius', $card)) {
                    $layout['cards'][$i]['borderRadius'] = $this->publishedCardRadius($card['borderRadius']);
                }
            }
        }

        $layout = $this->calibratePublishedHomeCanvas($layout);

        return self::applySharedBottomNav($layout);
    }

    /**
     * Present studio mosaic coordinates (default + Reset).
     * Values are col, row, colSpan, rowSpan.
     *
     * @return array<string, array{0:int,1:int,2:int,3:int}>
     */
    private function studioPreviewCardPositions(): array
    {
        return array(
            'user-modules' => array(1, 1, 1, 2),
            'grid' => array(2, 1, 1, 1),
            'appid' => array(3, 1, 1, 1),
            'recipe' => array(2, 2, 2, 1),
            'leads' => array(1, 3, 1, 1),
            'id-card' => array(2, 3, 1, 1),
            'hrms' => array(3, 3, 1, 1),
            'efactor-grid' => array(1, 4, 3, 1),
            'wifi' => array(1, 5, 1, 2),
            'biryani-banner' => array(2, 5, 2, 2),
        );
    }

    /**
     * Keep the studio mosaic coordinates on published layouts.
     * Top row: tall user-modules + GRID/HRMS, second row ID/AppID.
     *
     * @param array<string, mixed> $layout
     * @return array<string, mixed>
     */
    private function alignAccountCardPositions(int $userId, array $layout): array
    {
        // Do not force studio mosaic coordinates over user edits.
        // Re-snapping on sync/save was undoing Block size, placement, and related settings on refresh.
        // Fresh accounts already get the correct mosaic from getDefaultStarterCards(); Reset restores it.
        return $layout;
    }

    /**
     * @param array<string, mixed> $layout
     */
    private function cardPositionFingerprint(array $layout): string
    {
        $cards = array();
        foreach ($layout['cards'] ?? array() as $card) {
            if (!is_array($card)) {
                continue;
            }
            $cards[] = array(
                (string) ($card['id'] ?? ''),
                (int) ($card['col'] ?? 0),
                (int) ($card['row'] ?? 0),
                (int) ($card['colSpan'] ?? 0),
                (int) ($card['rowSpan'] ?? 0),
            );
        }

        $encoded = json_encode(array(
            'cards' => $cards,
            'gridRows' => (int) ($layout['theme']['gridRows'] ?? 0),
        ));

        return $encoded === false ? '' : $encoded;
    }

    /**
     * Same range as the studio slider (0–72). Default 0 = sharp square corners.
     */
    private function publishedCardRadius($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }
        $radius = (int) $value;
        if ($radius < 0) {
            return 0;
        }
        if ($radius === 0) {
            return 0;
        }
        if ($radius > 72) {
            return 72;
        }

        return $radius;
    }

    /**
     * @param array<string, mixed> $layout
     * @return array<int, int|null>
     */
    private function layoutCardsRadiiFingerprint(array $layout): array
    {
        $out = array();
        foreach ($layout['cards'] ?? array() as $card) {
            if (!is_array($card)) {
                $out[] = null;
                continue;
            }
            $out[] = array_key_exists('borderRadius', $card) ? $this->publishedCardRadius($card['borderRadius']) : null;
        }

        return $out;
    }

    private function themeIsDarkCanvas(array $theme): bool
    {
        if (strtolower(trim((string) ($theme['bgStyle'] ?? ''))) === 'dark') {
            return true;
        }
        $bg = strtolower(trim((string) ($theme['phoneBg'] ?? '')));

        return in_array($bg, array('#0b0f1a', '#0b1220', '#111827', '#151b2e', '#000000'), true);
    }

    /**
     * The studio live canvas is the dark bento. A leftover light kit id
     * (QuickMart / Market) makes the app ignore that canvas.
     *
     * @param array<string, mixed> $layout
     * @return array<string, mixed>
     */
    private function calibratePublishedHomeCanvas(array $layout): array
    {
        if (!isset($layout['theme']) || !is_array($layout['theme'])) {
            $layout['theme'] = array();
        }
        $theme = $layout['theme'];
        $staticUi = trim((string) ($layout['staticUiTemplate'] ?? ($theme['staticUiTemplate'] ?? '')));
        $kit = strtolower(trim((string) ($theme['homeTemplate'] ?? ($layout['homeTemplate'] ?? ''))));
        $lightKits = array('efactor-quickmart', 'efactor-market', 'efactor-deals', 'efactor-shop', 'efactor-style');
        if ($staticUi === '' && $this->themeIsDarkCanvas($theme) && in_array($kit, $lightKits, true)) {
            $layout['homeTemplate'] = 'default';
            $theme['homeTemplate'] = 'default';
            $theme['showWelcome'] = true;
        }
        if ($staticUi === '' && !array_key_exists('showWelcome', $theme)) {
            $theme['showWelcome'] = true;
        }

        // Metro / every account: 1x1 cells are square with sharp corners.
        $theme = $this->applySquareTilesTheme($theme);

        if (isset($layout['cards']) && is_array($layout['cards'])) {
            foreach ($layout['cards'] as $i => $card) {
                if (!is_array($card)) {
                    continue;
                }
                // squareTiles=true → sharp metro tiles (0). Clears leftover 12/16/18/20 from older layouts.
                $layout['cards'][$i]['borderRadius'] = 0;
            }
        }
        $theme['cardBorderRadius'] = 0;
        $layout['theme'] = $theme;
        if ($staticUi === '') {
            $layout['homeTemplate'] = (string) ($theme['homeTemplate'] ?? ($layout['homeTemplate'] ?? 'default'));
        }

        return $layout;
    }

    /**
     * Force square 1x1 phone cells for every account (column width ≈ row height).
     * Metro contract: squareTiles + cardBorderRadius 0 (sharp corners).
     *
     * @param array<string, mixed> $theme
     * @return array<string, mixed>
     */
    private function applySquareTilesTheme(array $theme): array
    {
        // Always on for every account — 1x1 cells stay square with sharp corners.
        $theme['squareTiles'] = true;
        $cols = (int) ($theme['gridColumns'] ?? 3);
        if ($cols < 1) {
            $cols = 1;
        }
        if ($cols > 12) {
            $cols = 12;
        }
        $gap = (int) ($theme['gridGap'] ?? 4);
        if ($gap < 0) {
            $gap = 0;
        }
        if ($gap > 24) {
            $gap = 24;
        }
        // Match Flutter phone.designWidth (~375dp). App still uses sizeFormula=slotWidth at runtime.
        $square = (int) round((375 - ($gap * max(0, $cols - 1))) / $cols);
        if ($square < 40) {
            $square = 40;
        }
        if ($square > 160) {
            $square = 160;
        }
        $theme['gridRowHeight'] = $square;
        $theme['cardBorderRadius'] = 0;

        return $theme;
    }

    public function saveSchema(int $userId, array $schema): bool
    {
        $record = $this->getRecord($userId);
        $layout = array();

        if ($record && !empty($record['layout_json'])) {
            $decoded = json_decode($record['layout_json'], true);
            if (is_array($decoded)) {
                $layout = $decoded;
            }
        }

        return $this->saveSettings($userId, $layout, $schema);
    }

    public function saveSettings(int $userId, array $layout, ?array $schema): bool
    {
        $this->ensureTable();

        $now = date('Y-m-d H:i:s');
        $existing = $this->getRecord($userId);

        $data = array(
            'updated_at' => $now,
        );

        $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
            $jsonFlags |= JSON_INVALID_UTF8_SUBSTITUTE;
        }

        if (!empty($layout)) {
            $layoutJson = json_encode($layout, $jsonFlags);
            if ($layoutJson === false) {
                log_message('error', 'App home layout json_encode failed: ' . json_last_error_msg());
                return false;
            }
            $data['layout_json'] = $layoutJson;
        }

        if ($schema !== null) {
            if (empty($schema)) {
                $data['schema_json'] = null;
                $data['schema_version'] = '1.0.0';
            } else {
                // Stamp layout fingerprint when studio/API provides a schema with the current layout.
                if (!empty($layout)) {
                    $cfg = $this->buildSchemaConfig($userId);
                    $fingerprint = $this->layoutSourceFingerprint($layout, $cfg);
                    if ($fingerprint !== '') {
                        $schema['sourceFingerprint'] = $fingerprint;
                        if (!isset($schema['meta']) || !is_array($schema['meta'])) {
                            $schema['meta'] = array();
                        }
                        $schema['meta']['sourceFingerprint'] = $fingerprint;
                        $schema['meta']['layoutFingerprint'] = $fingerprint;
                    }
                }
                $schema = \App\Libraries\AppHomeSchemaSigner::attachSignature($schema);
                $schemaJson = json_encode($schema, $jsonFlags);
                if ($schemaJson === false) {
                    log_message('error', 'App home schema json_encode failed: ' . json_last_error_msg());
                    return false;
                }
                $data['schema_json'] = $schemaJson;
                $version = isset($schema['schemaVersion']) ? (string) $schema['schemaVersion'] : '1.0.0';
                $data['schema_version'] = substr($version, 0, 32);
            }
        }

        if ($existing) {
            $updated = $this->db->table('app_home_page_settings')
                ->where('user_id', $userId)
                ->update($data);

            // Only treat real query failures as errors. CI4 MySQLi error() can surface a
            // sticky connect_errno from an unrelated failed db_connect('grid') in the same request.
            if ($updated === false && $this->isQueryFailure()) {
                $error = $this->db->error();
                log_message('error', 'App home settings update failed: ' . ($error['message'] ?? 'unknown'));
                return false;
            }

            return true;
        }

        $data['user_id'] = $userId;
        $data['created_at'] = $now;

        if (!isset($data['layout_json'])) {
            $data['layout_json'] = null;
        }
        if (!isset($data['schema_json'])) {
            $data['schema_json'] = null;
        }
        if (!isset($data['schema_version'])) {
            $data['schema_version'] = '1.0.0';
        }

        $inserted = $this->db->table('app_home_page_settings')->insert($data);
        if ($inserted === false && $this->isQueryFailure()) {
            $error = $this->db->error();
            log_message('error', 'App home settings insert failed: ' . ($error['message'] ?? 'unknown'));
            return false;
        }
        if ($inserted === false) {
            // Insert returned false but no live query errno — verify row exists before failing.
            $verify = $this->getRecord($userId);
            if ($verify) {
                return true;
            }
            log_message('error', 'App home settings insert returned false with no query errno for user_id=' . $userId);
            return false;
        }

        return true;
    }

    /**
     * True only when the active default connection has a real query errno.
     * Ignores sticky mysqli connect_errno left by a failed secondary DB group (e.g. grid_db).
     */
    private function isQueryFailure(): bool
    {
        try {
            $connId = $this->db->connID ?? null;
            if (is_object($connId) && isset($connId->errno) && (int) $connId->errno > 0) {
                return true;
            }
        } catch (\Throwable $e) {
            return true;
        }

        return false;
    }

    public function getSettingsMeta(int $userId): array
    {
        $this->ensureTable();
        $record = $this->getRecord($userId);

        if (!$record) {
            return array();
        }

        return array(
            'user_id' => (int) $record['user_id'],
            'schema_version' => $record['schema_version'] ?? '1.0.0',
            'has_layout' => !empty($record['layout_json']),
            'has_schema' => !empty($record['schema_json']),
            'created_at' => $record['created_at'] ?? null,
            'updated_at' => $record['updated_at'] ?? null,
        );
    }

    /**
     * Public schema fetch URL used by the mobile app (v8/api_creation.php).
     */
    public function getSchemaApiUrl(int $userId): string
    {
        helper('url');

        $base = $this->normalizeAppHomeEndpoint(
            $this->getPublicApiBaseUrl(),
            'api_creation.php',
            'https://efactor.tech/v8/api_creation.php'
        );

        return $base . '?action=get_schema&user_id=' . max(1, $userId);
    }

    /**
     * Dedicated Grid page schema fetch URL (separate from App Home get_schema).
     * Mirrors IoT: grid_api_creation.php?action=get_grid
     */
    public function getGridApiUrl(int $userId): string
    {
        helper('url');

        $base = $this->normalizeAppHomeEndpoint(
            $this->getPublicGridApiBaseUrl(),
            'grid_api_creation.php',
            'https://efactor.tech/v8/grid_api_creation.php'
        );

        return $base . '?action=get_grid&user_id=' . max(1, $userId);
    }

    /**
     * Resolve standalone Grid API script URL (root or /v8/).
     */
    public function getPublicGridApiBaseUrl(): string
    {
        helper('url');

        $envUrl = trim((string) (getenv('GRID_API_URL') ?: ''));
        if ($envUrl !== '') {
            return $this->normalizeAppHomeEndpoint(
                $envUrl,
                'grid_api_creation.php',
                'https://efactor.tech/v8/grid_api_creation.php'
            );
        }

        $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
        if ($docRoot !== '' && is_file($docRoot . '/grid_api_creation.php')) {
            return rtrim(base_url('grid_api_creation.php'), '/');
        }
        if ($docRoot !== '' && is_file($docRoot . '/v8/grid_api_creation.php')) {
            return rtrim(base_url('v8/grid_api_creation.php'), '/');
        }

        return 'https://efactor.tech/v8/grid_api_creation.php';
    }

    /**
     * Resolve standalone App Home API script URL (root or /v8/ on different servers).
     */
    public function getPublicApiBaseUrl(): string
    {
        helper('url');

        $envUrl = trim((string) (getenv('APP_HOME_API_URL') ?: ''));
        if ($envUrl !== '') {
            return $this->normalizeAppHomeEndpoint(
                $envUrl,
                'api_creation.php',
                'https://efactor.tech/v8/api_creation.php'
            );
        }

        $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
        if ($docRoot !== '' && is_file($docRoot . '/api_creation.php')) {
            return $this->normalizeAppHomeEndpoint(
                base_url('api_creation.php'),
                'api_creation.php',
                'https://efactor.tech/v8/api_creation.php'
            );
        }
        if ($docRoot !== '' && is_file($docRoot . '/v8/api_creation.php')) {
            return $this->normalizeAppHomeEndpoint(
                base_url('v8/api_creation.php'),
                'api_creation.php',
                'https://efactor.tech/v8/api_creation.php'
            );
        }

        return $this->normalizeAppHomeEndpoint(
            base_url('v8/api_creation.php'),
            'api_creation.php',
            'https://efactor.tech/v8/api_creation.php'
        );
    }

    /**
     * Collapse duplicated endpoint joins from misconfigured baseURL / env vars.
     * Fixes: .../api_creation.php/v8/api_creation.php and .../api_creation.php/v8/iot_api_creation.php
     */
    public function normalizeAppHomeEndpoint(string $url, string $scriptFile, string $fallback): string
    {
        $url = trim($url);
        if ($url === '') {
            return rtrim($fallback, '/');
        }

        $parts = parse_url($url);
        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? '';
        $path = (string) ($parts['path'] ?? '');

        if ($host === '') {
            // Relative / opaque — strip duplicate script segments then return.
            $cleaned = $this->collapseDuplicateScriptPath($url, $scriptFile);
            return rtrim($cleaned !== '' ? $cleaned : $fallback, '/');
        }

        $path = $this->collapseDuplicateScriptPath($path, $scriptFile);
        if ($path === '' || stripos($path, $scriptFile) === false) {
            $path = '/v8/' . ltrim($scriptFile, '/');
        }

        $port = isset($parts['port']) ? (':' . (int) $parts['port']) : '';

        return rtrim($scheme . '://' . $host . $port . $path, '/');
    }

    private function collapseDuplicateScriptPath(string $path, string $scriptFile): string
    {
        $script = preg_quote($scriptFile, '/');
        // .../api_creation.php/v8/api_creation.php → /v8/api_creation.php
        $path = preg_replace(
            '#/(?:v8/)?' . $script . '/+(?:v8/)?' . $script . '#i',
            '/v8/' . $scriptFile,
            $path
        ) ?? $path;
        // .../api_creation.php/v8/iot_api_creation.php when normalizing schema base wrongly used as IoT parent
        $path = preg_replace(
            '#/(?:v8/)?api_creation\.php/+(?:v8/)?iot_api_creation\.php#i',
            '/v8/iot_api_creation.php',
            $path
        ) ?? $path;
        $path = preg_replace(
            '#/(?:v8/)?iot_api_creation\.php/+(?:v8/)?api_creation\.php#i',
            '/v8/api_creation.php',
            $path
        ) ?? $path;

        return $path;
    }

    public function clearSchema(int $userId): bool
    {
        $this->ensureTable();
        $existing = $this->getRecord($userId);

        if (!$existing) {
            return true;
        }

        return (bool) $this->db->table('app_home_page_settings')
            ->where('user_id', $userId)
            ->update(array(
                'schema_json' => null,
                'schema_version' => '1.0.0',
                'updated_at' => date('Y-m-d H:i:s'),
            ));
    }

    private function getRecord(int $userId): ?array
    {
        $row = $this->db->table('app_home_page_settings')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    private function ensureTable(): void
    {
        $sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS `app_home_page_settings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `layout_json` LONGTEXT NULL,
    `schema_json` LONGTEXT NULL,
    `schema_version` VARCHAR(32) NOT NULL DEFAULT '1.0.0',
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;

        if (!$this->db->query($sql)) {
            log_message('error', 'Failed to ensure app_home_page_settings table exists.');
        }

        $this->ensureMediaTable();
        $this->ensureHelpFeedbackTable();
    }

    public function recordFaceImage(
        int $userId,
        string $fileName,
        string $filePath,
        string $publicUrl,
        ?string $cardId,
        ?int $faceIndex,
        ?string $mimeType,
        ?int $fileSize
    ): bool {
        $this->ensureMediaTable();

        $inserted = $this->db->table('app_home_page_media')->insert(array(
            'user_id' => $userId,
            'card_id' => $cardId,
            'face_index' => $faceIndex,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'public_url' => $publicUrl,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'created_at' => date('Y-m-d H:i:s'),
        ));

        if (!$inserted) {
            $error = $this->db->error();
            if (!empty($error['code'])) {
                log_message('error', 'App home media insert failed: ' . ($error['message'] ?? 'unknown'));
            }
            return false;
        }

        return true;
    }

    private function ensureMediaTable(): void
    {
        $sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS `app_home_page_media` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `card_id` VARCHAR(128) NULL,
    `face_index` TINYINT UNSIGNED NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(512) NOT NULL,
    `public_url` VARCHAR(512) NOT NULL,
    `mime_type` VARCHAR(64) NULL,
    `file_size` INT UNSIGNED NULL,
    `created_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;

        if (!$this->db->query($sql)) {
            log_message('error', 'Failed to ensure app_home_page_media table exists.');
        }
    }

    /**
     * Load uploaded face image bytes for public media delivery.
     */
    public function getMediaForServe(int $mediaId): ?array
    {
        $this->ensureMediaTable();

        $row = $this->db->table('app_home_page_media')
            ->where('id', $mediaId)
            ->get()
            ->getRowArray();

        if (!$row) {
            return null;
        }

        $filePath = trim((string) ($row['file_path'] ?? ''));
        if ($filePath === '' || !is_readable($filePath)) {
            return null;
        }

        $fileData = file_get_contents($filePath);
        if ($fileData === false) {
            return null;
        }

        return array(
            'file_name' => $row['file_name'] ?? 'image',
            'mime_type' => $row['mime_type'] ?? '',
            'file_data' => $fileData,
        );
    }

    /**
     * Star labels stored with an Account rating.
     * The typed comment is saved as the user wrote it.
     *
     * @return array
     */
    public function helpFeedbackCatalog(): array
    {
        return array(
            'areas' => array(
                'home' => 'Home screen',
                'profile' => 'Profile and account',
                'photo' => 'Profile photo',
                'devices' => 'IoT devices',
                'grid' => 'Grid',
                'notifications' => 'Notifications',
                'appearance' => 'Appearance',
                'login' => 'Login and security',
                'performance' => 'Speed or crashes',
                'other' => 'Something else',
            ),
            'comments' => array(
                'blank' => 'The screen does not load. It stays blank, on a spinner, or shows an error.',
                'tap' => 'I tap a button, menu, or card and nothing happens.',
                'wrong' => 'The information is wrong. The name, photo, number, or status is not correct.',
                'photo' => 'My profile photo does not appear, or it is an old or placeholder image.',
                'crash' => 'The app is slow, freezes, or closes by itself.',
                'other' => 'Something else is wrong in this area and needs a closer look.',
            ),
            'when' => array(
                'now' => 'It just happened for the first time.',
                'today' => 'It started today. It was working before.',
                'always' => 'It happens every time I open this.',
                'sometimes' => 'It happens sometimes, then it works again.',
            ),
            'ratings' => array(
                1 => 'Blocked - I cannot complete what I came to do.',
                2 => 'Poor - Hard to use. I had to stop.',
                3 => 'Okay - I can continue, but something is wrong.',
                4 => 'Good - Usable, with a minor issue.',
                5 => 'Excellent - Only a small note.',
            ),
        );
    }

    /**
     * @param array $input
     * @param array $profile
     * @return array
     */
    public function saveHelpFeedback(int $userId, array $input, array $profile): array
    {
        if ($userId <= 0) {
            return array('success' => false, 'message' => 'Not signed in');
        }

        $catalog = $this->helpFeedbackCatalog();
        $ratings = isset($catalog['ratings']) && is_array($catalog['ratings']) ? $catalog['ratings'] : array();
        $rating = (int) ($input['rating'] ?? 0);
        $comment = trim(strip_tags((string) ($input['comment'] ?? $input['note'] ?? '')));
        if (function_exists('mb_substr')) {
            $comment = mb_substr($comment, 0, 2000);
        } else {
            $comment = substr($comment, 0, 2000);
        }

        if ($rating < 1 || $rating > 5 || !isset($ratings[$rating])) {
            return array('success' => false, 'message' => 'Select a star rating from 1 to 5.');
        }
        if ($comment === '') {
            return array('success' => false, 'message' => 'Write a comment about the issue.');
        }

        $this->ensureHelpFeedbackTable();
        $since = date('Y-m-d H:i:s', time() - 86400);
        $recent = $this->db->table('app_home_help_feedback')
            ->where('user_id', $userId)
            ->where('created_at >=', $since)
            ->countAllResults();
        if ($recent >= 20) {
            return array(
                'success' => false,
                'message' => 'You have already sent several reports today. Please try again tomorrow.',
            );
        }

        $ratingLabel = (string) $ratings[$rating];
        $reportText = 'Rating: ' . $rating . '/5 (' . $ratingLabel . ')' . "\n" . 'Comment: ' . $comment;

        $now = date('Y-m-d H:i:s');
        $memberId = isset($profile['memberId']) ? trim((string) $profile['memberId']) : '';
        $userName = isset($profile['name']) ? trim((string) $profile['name']) : '';
        $email = isset($profile['email']) ? trim((string) $profile['email']) : '';
        $phone = isset($profile['phone']) ? trim((string) $profile['phone']) : '';
        if (function_exists('mb_substr')) {
            $memberId = mb_substr($memberId, 0, 64);
            $userName = mb_substr($userName, 0, 191);
            $email = mb_substr($email, 0, 191);
            $phone = mb_substr($phone, 0, 32);
        } else {
            $memberId = substr($memberId, 0, 64);
            $userName = substr($userName, 0, 191);
            $email = substr($email, 0, 191);
            $phone = substr($phone, 0, 32);
        }

        $inserted = $this->db->table('app_home_help_feedback')->insert(array(
            'user_id' => $userId,
            'member_id' => $memberId,
            'user_name' => $userName,
            'email' => $email,
            'phone' => $phone,
            'issue_area' => 'rating',
            'issue_label' => 'App rating',
            'rating' => $rating,
            'comment_id' => 'message',
            'comment' => $comment,
            'happened_when' => 'unspecified',
            'when_label' => '',
            'note' => $comment,
            'report_text' => $reportText,
            'created_at' => $now,
        ));
        if (!$inserted) {
            $error = $this->db->error();
            log_message('error', 'Help feedback insert failed: ' . ($error['message'] ?? 'unknown'));

            return array('success' => false, 'message' => 'Could not save your report. Please try again.');
        }

        return array(
            'success' => true,
            'message' => 'Thanks. Your rating was sent.',
            'id' => (int) $this->db->insertID(),
        );
    }

    private function ensureHelpFeedbackTable(): void
    {
        $sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS `app_home_help_feedback` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `member_id` VARCHAR(64) NULL,
    `user_name` VARCHAR(191) NULL,
    `email` VARCHAR(191) NULL,
    `phone` VARCHAR(32) NULL,
    `issue_area` VARCHAR(64) NOT NULL,
    `issue_label` VARCHAR(191) NOT NULL,
    `rating` TINYINT UNSIGNED NOT NULL,
    `comment_id` VARCHAR(64) NOT NULL,
    `comment` TEXT NOT NULL,
    `happened_when` VARCHAR(64) NOT NULL,
    `when_label` VARCHAR(255) NOT NULL,
    `note` TEXT NULL,
    `report_text` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_help_user` (`user_id`),
    KEY `idx_help_area` (`issue_area`),
    KEY `idx_help_rating` (`rating`),
    KEY `idx_help_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;

        if (!$this->db->query($sql)) {
            log_message('error', 'Failed to ensure app_home_help_feedback table exists.');
        }
    }
}
