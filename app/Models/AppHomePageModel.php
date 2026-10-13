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
        $layout['bottomNav'] = self::buildCanonicalBottomNav(self::BOTTOM_NAV_CATEGORY_DEFAULT, $active);

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
                'gridRows' => 7,
                'gridRowHeight' => 96,
                'cardBorderRadius' => 20,
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
                    'searchIconToken' => 'search',
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
                    'qrPayload' => 'https://efactor.tech/grid/connect?mode=professional',
                    'shareActionId' => 'grid-share-qr',
                    'selectModeActionId' => 'grid-select-qr-mode',
                ),
                'exploring' => array(
                    'title' => 'Continue Exploring',
                    'seeAllLabel' => 'See all',
                    'seeAllActionId' => 'grid-see-all-exploring',
                    'items' => array(
                        array(
                            'id' => 'find-talent',
                            'title' => 'Find Talent',
                            'subtitle' => 'People with skills you need',
                            'iconToken' => 'user',
                            'color' => '#8b5cf6',
                            'actionId' => 'grid-open-find-talent',
                        ),
                        array(
                            'id' => 'join-circles',
                            'title' => 'Join Circles',
                            'subtitle' => "Communities you'll love",
                            'iconToken' => 'users',
                            'color' => '#14b8a6',
                            'actionId' => 'grid-open-join-circles',
                        ),
                    ),
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
     * Absolute avatar URL, or empty so the app shows initials.
     * A missing or relative photo was rendering as a broken icon on some accounts.
     */
    private function publicAvatarUrl(string $image): string
    {
        $image = trim($image);
        if ($image === '') {
            return '';
        }

        helper('general');
        $url = trim((string) get_avatar($image));
        $absolute = $this->toPublicHttpsUrl($url);
        if ($absolute !== '' && !preg_match('#/assets/images/avatar\.jpg(\?|$)#i', $absolute)) {
            return $absolute;
        }

        $fileName = '';
        $decoded = @unserialize($image);
        if (is_array($decoded) && !empty($decoded['file_name'])) {
            $fileName = trim((string) $decoded['file_name']);
        } elseif (strpos(ltrim($image), 'a:') !== 0) {
            $fileName = $image;
        }
        if ($fileName === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $fileName)) {
            return $this->toPublicHttpsUrl($fileName);
        }
        $fileName = ltrim(str_replace('\\', '/', $fileName), '/');
        if (strpos($fileName, '/') === false) {
            $fileName = 'files/profile_images/' . $fileName;
        }

        return 'https://efactor.tech/' . $fileName;
    }

    private function toPublicHttpsUrl(string $url): string
    {
        $url = trim(html_entity_decode($url, ENT_QUOTES));
        if ($url === '' || preg_match('#^(data:|blob:)#i', $url)) {
            return '';
        }
        if (preg_match('#^https?://#i', $url)) {
            if (stripos($url, 'http://') === 0) {
                $host = strtolower((string) parse_url($url, PHP_URL_HOST));
                if ($host !== '' && !in_array($host, array('localhost', '127.0.0.1', '::1'), true)) {
                    $url = 'https://' . substr($url, 7);
                }
            }
            return $url;
        }
        if ($url[0] !== '/') {
            $url = '/' . $url;
        }

        return 'https://efactor.tech' . $url;
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
            'avatarUrl' => $this->publicAvatarUrl($image),
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
     * Same card keys (id, type, faces, col, row, colSpan, rowSpan, …) — positions match the default phone layout.
     */
    private function getDefaultStarterCards(): array
    {
        $recipeImage = 'https://images.unsplash.com/photo-1563379091339-03246963d96a?w=400&h=400&fit=crop';
        $gridLogo = 'https://efactor.tech/assets/images/GRID_Logo.png';

        return array(
            array(
                'id' => 'signal',
                'type' => 'link',
                'gridArea' => 'signal',
                'title' => '',
                'label' => '',
                'icon' => 'fa-signal',
                'color' => '#6d28d9',
                'textColor' => '#ffffff',
                'col' => 1,
                'row' => 1,
                'colSpan' => 1,
                'rowSpan' => 1,
                'borderRadius' => 12,
                'defaultFace' => 0,
                'rotationDir' => 'ltr',
                'faces' => array(
                    array('title' => '', 'icon' => 'fa-signal', 'color' => '#6d28d9'),
                    array('title' => 'Signal', 'icon' => 'fa-wifi', 'color' => '#5b21b6'),
                    array('title' => 'Network', 'icon' => 'fa-broadcast-tower', 'color' => '#7c3aed'),
                    array('title' => 'Status', 'icon' => 'fa-chart-bar', 'color' => '#4c1d95'),
                ),
            ),
            array(
                'id' => 'grid',
                'type' => 'link',
                'gridArea' => 'grid',
                'title' => 'Share',
                'label' => 'Share',
                'subtitle' => '',
                'icon' => 'fa-share-alt',
                'color' => '#f97316',
                'textColor' => '#ffffff',
                'col' => 2,
                'row' => 1,
                'colSpan' => 1,
                'rowSpan' => 1,
                'borderRadius' => 12,
                'defaultFace' => 0,
                'rotationDir' => 'ltr',
                'faces' => array(
                    array('title' => 'Share', 'icon' => 'fa-share-alt', 'color' => '#f97316'),
                    array('title' => 'GRID', 'subtitle' => 'Share & connect', 'icon' => 'fa-border-all', 'color' => '#ea580c'),
                    array('title' => 'Network', 'subtitle' => '12 nodes', 'icon' => 'fa-project-diagram', 'color' => '#fb923c'),
                    array('title' => 'Connect', 'subtitle' => 'Peers online', 'icon' => 'fa-link', 'color' => '#fdba74'),
                ),
            ),
            array(
                'id' => 'hrms',
                'type' => 'link',
                'gridArea' => 'hrms',
                'title' => 'Leave',
                'label' => 'Leave',
                'subtitle' => '',
                'icon' => 'fa-calendar',
                'color' => '#2dd4bf',
                'textColor' => '#ffffff',
                'col' => 3,
                'row' => 1,
                'colSpan' => 1,
                'rowSpan' => 1,
                'borderRadius' => 12,
                'defaultFace' => 0,
                'rotationDir' => 'ltr',
                'faces' => array(
                    array('title' => 'Leave', 'icon' => 'fa-calendar', 'color' => '#2dd4bf'),
                    array('title' => 'HRMS', 'subtitle' => 'Employees & payroll', 'icon' => 'fa-user-tie', 'color' => '#14b8a6'),
                    array('title' => 'Payroll', 'subtitle' => 'This month', 'icon' => 'fa-money-bill', 'color' => '#0d9488'),
                    array('title' => 'Team', 'subtitle' => '24 active', 'icon' => 'fa-users', 'color' => '#5eead4'),
                ),
            ),
            array(
                'id' => 'user-modules',
                'type' => 'link',
                'gridArea' => 'user-modules',
                'title' => 'Online',
                'label' => 'Online',
                'icon' => 'fa-fingerprint',
                'color' => '#7c3aed',
                'textColor' => '#ffffff',
                'col' => 1,
                'row' => 2,
                'colSpan' => 1,
                'rowSpan' => 2,
                'tall' => true,
                'borderRadius' => 12,
                'defaultFace' => 0,
                'rotationDir' => 'ltr',
                'faces' => array(
                    array('title' => 'Online', 'icon' => 'fa-fingerprint', 'color' => '#7c3aed'),
                    array('title' => 'Active', 'icon' => 'fa-chart-line', 'color' => '#6d28d9'),
                    array('title' => 'Modules', 'icon' => 'fa-th-large', 'color' => '#5b21b6'),
                    array('title' => 'Users', 'icon' => 'fa-th', 'color' => '#8b5cf6'),
                ),
            ),
            array(
                'id' => 'id-card',
                'type' => 'link',
                'gridArea' => 'id-card',
                'title' => 'Verify',
                'label' => 'Verify',
                'subtitle' => '',
                'icon' => 'fa-braille',
                'color' => '#ec4899',
                'textColor' => '#ffffff',
                'col' => 2,
                'row' => 2,
                'colSpan' => 1,
                'rowSpan' => 1,
                'borderRadius' => 12,
                'defaultFace' => 0,
                'rotationDir' => 'ltr',
                'faces' => array(
                    array('title' => 'Verify', 'icon' => 'fa-braille', 'color' => '#ec4899'),
                    array('title' => 'ID', 'subtitle' => 'Efactor ID', 'icon' => 'fa-fingerprint', 'color' => '#db2777'),
                    array('title' => 'Profile', 'subtitle' => 'Your card', 'icon' => 'fa-id-card', 'color' => '#f472b6'),
                    array('title' => 'Badge', 'subtitle' => 'Access pass', 'icon' => 'fa-shield-alt', 'color' => '#be185d'),
                ),
            ),
            array(
                'id' => 'appid',
                'type' => 'link',
                'gridArea' => 'appid',
                'title' => 'New',
                'label' => 'New',
                'subtitle' => '',
                'icon' => 'fa-plus',
                'color' => '#3b82f6',
                'textColor' => '#ffffff',
                'col' => 3,
                'row' => 2,
                'colSpan' => 1,
                'rowSpan' => 1,
                'borderRadius' => 12,
                'defaultFace' => 0,
                'rotationDir' => 'ltr',
                'faces' => array(
                    array('title' => 'New', 'icon' => 'fa-plus', 'color' => '#3b82f6'),
                    array('title' => 'AppID', 'subtitle' => 'Generation', 'icon' => 'fa-key', 'color' => '#2563eb'),
                    array('title' => 'Active', 'subtitle' => '8 apps', 'icon' => 'fa-mobile-alt', 'color' => '#60a5fa'),
                    array('title' => 'Keys', 'subtitle' => 'Manage', 'icon' => 'fa-lock', 'color' => '#1d4ed8'),
                ),
            ),
            array(
                'id' => 'leads',
                'type' => 'link',
                'gridArea' => 'leads',
                'title' => 'Hot',
                'label' => 'Hot',
                'icon' => 'fa-th',
                'color' => '#22c55e',
                'textColor' => '#ffffff',
                'col' => 3,
                'row' => 3,
                'colSpan' => 1,
                'rowSpan' => 1,
                'borderRadius' => 12,
                'defaultFace' => 0,
                'rotationDir' => 'ltr',
                'faces' => array(
                    array('title' => 'Hot', 'icon' => 'fa-th', 'color' => '#22c55e'),
                    array('title' => 'Leads', 'value' => '52', 'icon' => 'fa-users', 'color' => '#16a34a'),
                    array('title' => 'Converted', 'icon' => 'fa-check', 'color' => '#15803d'),
                    array('title' => 'Follow-up', 'icon' => 'fa-phone', 'color' => '#4ade80'),
                ),
            ),
            array(
                'id' => 'efactor-grid',
                'type' => 'image',
                'gridArea' => 'efactor-grid',
                'title' => '',
                'label' => '',
                'tag' => '',
                'color' => '#ffffff',
                'textColor' => '#111827',
                'image' => $gridLogo,
                'imageFit' => 'contain',
                'imagePosition' => 'center',
                'col' => 1,
                'row' => 4,
                'colSpan' => 3,
                'rowSpan' => 1,
                'borderRadius' => 12,
                'defaultFace' => 0,
                'rotationDir' => 'ltr',
                'faces' => array(
                    array('title' => '', 'image' => $gridLogo, 'imageFit' => 'contain', 'color' => '#ffffff'),
                    array('title' => 'GRID', 'icon' => 'fa-border-all', 'color' => '#111827'),
                    array('title' => 'Featured', 'icon' => 'fa-star', 'color' => '#334155'),
                    array('title' => 'Explore', 'icon' => 'fa-compass', 'color' => '#475569'),
                ),
            ),
            array(
                'id' => 'add-device',
                'type' => 'link',
                'gridArea' => 'add-device',
                'title' => 'Add image',
                'label' => 'Add image',
                'icon' => 'fa-heart',
                'color' => '#0f172a',
                'textColor' => '#ffffff',
                'col' => 1,
                'row' => 5,
                'colSpan' => 2,
                'rowSpan' => 2,
                'tall' => true,
                'borderRadius' => 12,
                'defaultFace' => 0,
                'rotationDir' => 'ltr',
                'faces' => array(
                    array('title' => 'Add image', 'icon' => 'fa-heart', 'color' => '#0f172a'),
                    array('title' => 'Upload', 'icon' => 'fa-image', 'color' => '#1e293b'),
                    array('title' => 'Gallery', 'icon' => 'fa-images', 'color' => '#334155'),
                    array('title' => 'Camera', 'icon' => 'fa-camera', 'color' => '#475569'),
                ),
            ),
            array(
                'id' => 'gallery-tile',
                'type' => 'link',
                'gridArea' => 'gallery-tile',
                'title' => '',
                'label' => '',
                'icon' => 'fa-image',
                'color' => '#111827',
                'textColor' => '#e2e8f0',
                'col' => 3,
                'row' => 5,
                'colSpan' => 1,
                'rowSpan' => 2,
                'tall' => true,
                'borderRadius' => 12,
                'defaultFace' => 0,
                'rotationDir' => 'ltr',
                'faces' => array(
                    array('title' => '', 'icon' => 'fa-image', 'color' => '#111827'),
                    array('title' => 'Photos', 'icon' => 'fa-images', 'color' => '#1e293b'),
                    array('title' => 'Albums', 'icon' => 'fa-folder', 'color' => '#334155'),
                    array('title' => 'Camera', 'icon' => 'fa-camera', 'color' => '#475569'),
                ),
            ),
            array(
                'id' => 'wifi',
                'type' => 'link',
                'gridArea' => 'wifi',
                'title' => 'WIFI',
                'label' => 'WIFI',
                'icon' => 'fa-wifi',
                'color' => '#0f172a',
                'textColor' => '#ffffff',
                'col' => 1,
                'row' => 7,
                'colSpan' => 1,
                'rowSpan' => 1,
                'borderRadius' => 12,
                'defaultFace' => 0,
                'rotationDir' => 'ltr',
                'faces' => array(
                    array('title' => 'WIFI', 'icon' => 'fa-wifi', 'color' => '#0f172a'),
                    array('title' => 'Network', 'icon' => 'fa-signal', 'color' => '#1e293b'),
                    array('title' => 'Devices', 'icon' => 'fa-broadcast-tower', 'color' => '#334155'),
                    array('title' => 'Setup', 'icon' => 'fa-plus', 'color' => '#475569'),
                ),
            ),
            array(
                'id' => 'recipe',
                'type' => 'image',
                'gridArea' => 'recipe',
                'title' => 'Biryani',
                'label' => 'Biryani',
                'subtitle' => '',
                'color' => '#78350f',
                'textColor' => '#ffffff',
                'image' => $recipeImage,
                'imageFit' => 'cover',
                'imagePosition' => 'center',
                'col' => 2,
                'row' => 7,
                'colSpan' => 1,
                'rowSpan' => 1,
                'borderRadius' => 12,
                'defaultFace' => 0,
                'rotationDir' => 'ltr',
                'faces' => array(
                    array('title' => 'Biryani', 'image' => $recipeImage, 'color' => '#78350f'),
                    array('title' => 'Ingredients', 'icon' => 'fa-list', 'color' => '#92400e'),
                    array('title' => 'Steps', 'icon' => 'fa-clipboard-list', 'color' => '#a16207'),
                    array('title' => 'Timer', 'icon' => 'fa-hourglass-half', 'color' => '#b45309'),
                ),
            ),
            array(
                'id' => 'gallery',
                'type' => 'link',
                'gridArea' => 'gallery',
                'title' => 'Gallery',
                'label' => 'Gallery',
                'icon' => 'fa-image',
                'color' => '#e5e7eb',
                'textColor' => '#334155',
                'iconColor' => '#334155',
                'col' => 3,
                'row' => 7,
                'colSpan' => 1,
                'rowSpan' => 1,
                'borderRadius' => 12,
                'defaultFace' => 0,
                'rotationDir' => 'ltr',
                'faces' => array(
                    array('title' => 'Gallery', 'icon' => 'fa-image', 'color' => '#e5e7eb', 'textColor' => '#334155', 'iconColor' => '#334155'),
                    array('title' => 'Photos', 'icon' => 'fa-images', 'color' => '#cbd5e1', 'textColor' => '#1e293b'),
                    array('title' => 'Albums', 'icon' => 'fa-folder', 'color' => '#94a3b8', 'textColor' => '#0f172a'),
                    array('title' => 'Camera', 'icon' => 'fa-camera', 'color' => '#64748b', 'textColor' => '#ffffff'),
                ),
            ),
        );
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
                for ($fi = 0; $fi < 4; $fi++) {
                    $sf = (isset($savedFaces[$fi]) && is_array($savedFaces[$fi])) ? $savedFaces[$fi] : array();
                    $faces[] = $sf;
                }
                $card['faces'] = $faces;
            }

            $merged[] = $card;
        }

        return $merged;
    }

    private function emptyFacesForCard(array $card): array
    {
        $label = $card['label'] ?? $card['title'] ?? 'Face 1';
        $icon = $card['icon'] ?? 'fa-cube';
        $color = $card['color'] ?? '#6366f1';
        $type = $card['type'] ?? 'link';

        if ($type === 'stat') {
            return array(
                array('value' => $card['value'] ?? '0', 'label' => $label, 'icon' => $icon, 'color' => $color),
                array('title' => 'Face 2', 'icon' => 'fa-star', 'color' => $color),
                array('title' => 'Face 3', 'icon' => 'fa-bolt', 'color' => $color),
                array('title' => 'Face 4', 'icon' => 'fa-heart', 'color' => $color),
            );
        }

        if ($type === 'image') {
            $image = trim((string) ($card['image'] ?? ''));
            return array(
                array('title' => $label, 'subtitle' => $card['subtitle'] ?? '', 'image' => $image, 'color' => $color),
                array('title' => 'Face 2', 'icon' => 'fa-star', 'color' => $color, 'image' => $image),
                array('title' => 'Face 3', 'icon' => 'fa-bolt', 'color' => $color, 'image' => $image),
                array('title' => 'Face 4', 'icon' => 'fa-heart', 'color' => $color, 'image' => $image),
            );
        }

        return array(
            array('title' => $label, 'subtitle' => $card['subtitle'] ?? '', 'icon' => $icon, 'color' => $color),
            array('title' => 'Face 2', 'icon' => 'fa-star', 'color' => $color),
            array('title' => 'Face 3', 'icon' => 'fa-bolt', 'color' => $color),
            array('title' => 'Face 4', 'icon' => 'fa-heart', 'color' => $color),
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

        return $this->normalizeSmartphoneBreakpoints($data);
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

            $headerBefore = json_encode($layout['header'] ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $layout = $this->applyUserProfileToLayout($layout, $userId);
            $layout = $this->sanitizeLayoutForSchema($layout);
            $headerAfter = json_encode($layout['header'] ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($headerBefore !== $headerAfter) {
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

        return array(
            'userId' => $userId,
            'userEmail' => $userEmail,
            'loginUserProfile' => $profile,
            'logoUrl' => 'https://efactor.tech/files/home/Logo_new.png',
            'schemaApiUrl' => $this->getSchemaApiUrl($userId),
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
            }
        }

        return self::applySharedBottomNav($layout);
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
        $sql = "
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
        ";

        if (!$this->db->query($sql)) {
            log_message('error', 'Failed to ensure app_home_page_settings table exists.');
        }

        $this->ensureMediaTable();
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
        $sql = "
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
        ";

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
}
