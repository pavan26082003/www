<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Waiter Dashboard View — Enterprise Layout
| Mirrors manager.php structure: collapsible sidebar + fixed topbar + sections
|--------------------------------------------------------------------------
*/

$brandId  = (string)($brandId  ?? '');
$branchId = (string)($branchId ?? '');
$userId   = (string)($userId   ?? '');

$context         = is_array($context ?? null)         ? $context         : [];
$permissions     = is_array($permissions ?? null)     ? $permissions     : [];
$tables          = is_array($tables ?? null)          ? $tables          : [];
$serviceRequests = is_array($serviceRequests ?? null) ? $serviceRequests : [];
$pickupOrders    = is_array($pickupOrders ?? null)    ? $pickupOrders    : [];
$summary         = is_array($summary ?? null)         ? $summary         : [];
$quickActions    = is_array($quickActions ?? null)    ? $quickActions    : [];
$shiftMetrics    = is_array($shiftMetrics ?? null)    ? $shiftMetrics    : [];
$notifications   = is_array($notifications ?? null)  ? $notifications   : [];
$pendingBills    = is_array($pendingBills ?? null)    ? $pendingBills    : [];
$responseMetrics = is_array($responseMetrics ?? null) ? $responseMetrics : [];
$userSettings    = is_array($userSettings ?? null)    ? $userSettings    : [];
$defaultNotificationSound = (string)($defaultNotificationSound ?? 'RestaurantAssets/AlertSounds/Sound1.mpeg');

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/
if (!function_exists('waiter_resolve_asset_url')) {
    function waiter_resolve_asset_url(?string $path, string $fallback): string
    {
        $path = trim((string)$path);

        if ($path === '') {
            return rs_html_escape(rs_base_url($fallback));
        }

        if (
            strpos($path, 'http://') === 0 ||
            strpos($path, 'https://') === 0 ||
            strpos($path, '//') === 0 ||
            strpos($path, 'data:') === 0
        ) {
            return rs_html_escape($path);
        }

        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        // already full asset-relative path
        if (stripos($normalized, 'RestaurantAssets/') === 0) {
            return rs_html_escape(rs_base_url($normalized));
        }

        // DB often stores brand asset paths like: brands/<brand-id>/logo/logo.svg
        if (stripos($normalized, 'brands/') === 0) {
            return rs_html_escape(rs_base_url('RestaurantAssets/' . $normalized));
        }

        // DB may also store other restaurant asset folders directly
        $restaurantAssetFolders = [
            'default/',
            'css/',
            'js/',
            'images/',
            'videos/',
            'favicon/',
            'bg-images/',
            'logo/',
            'menu-items/',
            'offers-banner/',
            'User-profile/',
            'user-profile/'
        ];

        foreach ($restaurantAssetFolders as $prefix) {
            if (stripos($normalized, $prefix) === 0) {
                return rs_html_escape(rs_base_url('RestaurantAssets/' . $normalized));
            }
        }

        return rs_html_escape(rs_base_url($normalized));
    }
}

if (!function_exists('rs_html_escape')) {
    function rs_html_escape($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('rs_base_url')) {
    function rs_base_url(string $path = ''): string
    {
        $path = ltrim($path, '/');
        $scriptName = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
        $baseDir = rtrim(str_replace('/index.php', '', $scriptName), '/');
        return $baseDir . ($path !== '' ? '/' . $path : '');
    }
}

if (!function_exists('waiter_e')) {
    function waiter_e($value): string
    {
        return rs_html_escape($value);
    }
}

if (!function_exists('waiter_money')) {
    function waiter_money($amount): string
    {
        return '₹' . number_format((float)$amount, 0);
    }
}

if (!function_exists('waiter_can')) {
    function waiter_can(array $permissions, string $key, bool $default = false): bool
    {
        return array_key_exists($key, $permissions) ? (bool)$permissions[$key] : $default;
    }
}

if (!function_exists('waiter_time_ago')) {
    function waiter_time_ago(?string $datetime, string $fallback = 'Now'): string
    {
        $datetime = trim((string)$datetime);
        if ($datetime === '') return $fallback;
        $timestamp = strtotime($datetime);
        if ($timestamp === false) return $fallback;
        $diff = time() - $timestamp;
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . ' min ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hr ago';
        return floor($diff / 86400) . ' day ago';
    }
}

if (!function_exists('waiter_format_shift')) {
    function waiter_format_shift(?string $start, ?string $end): string
    {
        $start = trim((string)$start);
        $end = trim((string)$end);
        if ($start === '' || $end === '') return 'Shift Not Set';
        $startTs = strtotime($start);
        $endTs = strtotime($end);
        if ($startTs === false || $endTs === false) return 'Shift Not Set';
        return date('g:i A', $startTs) . ' – ' . date('g:i A', $endTs);
    }
}

if (!function_exists('waiter_normalize_status')) {
    function waiter_normalize_status($status, string $type = 'generic'): string
    {
        $status = strtolower(trim(str_replace([' ', '-'], '_', (string)$status)));

        if ($type === 'request') {
            if ($status === 'pending' || $status === 'requested' || $status === '') return 'new';
            if ($status === 'accepted') return 'approved';
            if ($status === 'resolved' || $status === 'done' || $status === 'completed') return 'closed';
            return $status;
        }

        if ($type === 'bill') {
            if ($status === 'requested' || $status === 'generated' || $status === 'pending' || $status === '') return 'new';
            return $status;
        }

        if ($type === 'payment') {
            if ($status === 'awaiting_collection' || $status === 'processing' || $status === 'pending' || $status === 'initiated' || $status === 'payment_pending') return 'in_processing';
            if ($status === '') return 'unpaid';
            return $status;
        }

        if ($type === 'order') {
            if ($status === 'placed' || $status === 'pending' || $status === '') return 'new';
            if ($status === 'closed' || $status === 'paid') return 'completed';
            return $status;
        }

        return $status;
    }
}

if (!function_exists('waiter_status_label')) {
    function waiter_status_label($status, string $type = 'generic'): string
    {
        $status = waiter_normalize_status($status, $type);
        if ($status === 'in_processing') return 'In Processing';
        return ucwords(str_replace('_', ' ', $status));
    }
}

if (!function_exists('waiter_badge_class')) {
    function waiter_badge_class(string $status): string
    {
        $status = strtolower(trim(str_replace([' ', '-'], '_', $status)));
        if (in_array($status, ['active', 'available', 'paid', 'ready', 'resolved', 'accepted', 'approved', 'served', 'completed', 'closed', 'open', 'in_stock'], true)) return 'success';
        if (in_array($status, ['pending', 'new', 'preparing', 'processing', 'in_processing', 'billing', 'partial', 'unpaid', 'waiting', 'payment_pending', 'in_progress', 'occupied', 'reserved', 'low_stock'], true)) return 'warning';
        if (in_array($status, ['cancelled', 'rejected', 'failed', 'inactive', 'blocked', 'maintenance', 'out_of_stock'], true)) return 'danger';
        return 'neutral';
    }
}

if (!function_exists('waiter_get_table_state_class')) {
    function waiter_get_table_state_class(string $status): string
    {
        $status = strtolower(trim($status));
        switch ($status) {
            case 'available':
                return 'available';
            case 'occupied':
                return 'occupied';
            case 'waiting':
                return 'waiting';
            case 'reserved':
                return 'reserved';
            case 'maintenance':
                return 'maintenance';
            case 'payment':
            case 'payment_pending':
            case 'in_processing':
                return 'payment';
            default:
                return 'available';
        }
    }
}

/*
|--------------------------------------------------------------------------
| View-safe values
|--------------------------------------------------------------------------
*/
$brand        = is_array($brand ?? null) ? $brand : [];
$brandFavicon = waiter_resolve_asset_url($brand['favicon'] ?? null, 'RestaurantAssets/default/default_favicon.svg');
$brandLogo    = waiter_resolve_asset_url($brand['logo'] ?? $brand['photo'] ?? null, 'RestaurantAssets/default/default_logo.svg');
$brandName    = (string)($brand['name'] ?? $context['brand_name'] ?? 'Restaurant');

$branchName    = (string)($context['branch_name']        ?? 'Branch');
$branchCity    = (string)($context['branch_city']        ?? '');
$branchStatus  = (string)($context['branch_status']      ?? 'active');
$branchDesc    = (string)($context['branch_address']     ?? $context['branch_description'] ?? '');

$waiterName         = (string)($waiterName        ?? $context['full_name']       ?? 'Waiter');
$waiterRole         = (string)($context['role']           ?? 'waiter');
$waiterEmail        = (string)($context['email']          ?? '');
$waiterPhone        = (string)($context['phone']          ?? '');
$waiterEmployeeCode = (string)($context['employee_code']  ?? '');
$waiterProfilePic   = waiter_resolve_asset_url(
    $context['profile_pic'] ?? $context['photo'] ?? null,
    'RestaurantAssets/default/default-avatar.svg'
);
$waiterStatus = (string)($context['waiter_status'] ?? 'active');
$shiftName    = (string)($shiftName ?? waiter_format_shift(
    $context['shift_start_time'] ?? null,
    $context['shift_end_time']   ?? null
));
$currentTime  = date('h:i A');

$summary = array_merge([
    'active_tables'        => 0,
    'waiting_calls'        => 0,
    'ready_pickups'        => 0,
    'pending_bills'        => 0,
    'unread_notifications' => 0,
], $summary);

$shiftMetrics = array_merge([
    'tables_handled'   => count($tables),
    'orders_closed'    => 0,
    'average_response' => '—',
    'pending_actions'  => 0,
], $shiftMetrics);

$userSettings = array_merge([
    'theme'                   => 'light',
    'lite_notification_sound' => null,
    'sound_alerts_enabled'    => 1,
], $userSettings);

$activeTheme = in_array((string)$userSettings['theme'], ['light', 'dark'], true)
    ? (string)$userSettings['theme']
    : 'light';
$soundAlertsEnabled = (int)$userSettings['sound_alerts_enabled'] === 1;
$defaultNotificationSoundUrl = waiter_resolve_asset_url($defaultNotificationSound, 'RestaurantAssets/AlertSounds/Sound1.mpeg');
$selectedNotificationSound = waiter_resolve_asset_url(
    $userSettings['lite_notification_sound'] ?? null,
    $defaultNotificationSound
);

$hasEnabledActions = false;
foreach ($quickActions as $qa) {
    if (!empty($qa['enabled'])) {
        $hasEnabledActions = true;
        break;
    }
}

/* Counts for nav badges */
$pendingRequestsCount = (int)$summary['waiting_calls'];
$assignedTablesCount  = (int)($summary['assigned_tables'] ?? count($tables));
$actionItemsCount     = (int)($summary['action_items'] ?? count(array_filter($quickActions, static function ($action) {
    return !empty($action['enabled']);
})));
$readyPickupsCount    = (int)$summary['ready_pickups'];
$pendingBillsCount    = (int)$summary['pending_bills'];
$unreadNotifs         = (int)(
    $summary['unread_notifications'] > 0
    ? $summary['unread_notifications']
    : count(array_filter($notifications, static fn($n) => is_array($n) && !(int)($n['is_read'] ?? 0)))
);

if ($quickActions === []) {
    $quickActions = [
        ['key' => 'collect_payment',        'label' => 'Collect Payment', 'icon' => 'fa-hand-holding-dollar', 'enabled' => waiter_can($permissions, 'collect_payment', true)],
        ['key' => 'call_manager',           'label' => 'Call Manager',    'icon' => 'fa-user-tie',            'enabled' => $waiterRole === 'waiter'],
    ];
}

$apiBase = function_exists('site_url')
    ? rtrim((string)site_url('/restaurant-system/waiter/api'), '/')
    : rtrim(rs_base_url('/restaurant-system/waiter/api'), '/');

$pageTitle = 'Waiter – ' . $branchName;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= waiter_e($pageTitle) ?></title>
    <link rel="icon" href="<?= $brandFavicon ?>">
    <link rel="stylesheet" href="<?= waiter_e(rs_base_url('RestaurantAssets/css/global.css')) ?>">
    <link rel="stylesheet" href="<?= waiter_e(rs_base_url('RestaurantAssets/css/waiter.css')) ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body
    class="waiter-page theme-waiter <?= $activeTheme === 'dark' ? 'waiter-dark-mode' : '' ?>"
    data-brand-id="<?= waiter_e($brandId) ?>"
    data-branch-id="<?= waiter_e($branchId) ?>"
    data-user-id="<?= waiter_e($userId) ?>"
    data-theme="<?= waiter_e($activeTheme) ?>">

    <!-- Alert audio -->
    <audio id="waiterAlertAudio" preload="auto">
        <source src="<?= waiter_e($selectedNotificationSound) ?>" type="audio/mpeg">
    </audio>

    <!-- Toast container -->
    <div class="waiter-toast-container" id="waiterToastContainer" aria-live="polite"></div>

    <!-- Loading overlay -->
    <div class="waiter-loading-overlay" id="waiterLoadingOverlay" aria-hidden="true">
        <div class="waiter-loading-spinner" aria-hidden="true"></div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════
         MAIN LAYOUT
         ═══════════════════════════════════════════════════════════════════════ -->
    <div class="waiter-main-shell" id="waiterMainShell">

        <!-- ═══ SIDEBAR ═══ -->
        <aside class="waiter-sidebar" id="waiterSidebar">

          
            <!-- Hotel / Branch Profile Card -->
            <?php
            /*
                 * The hotel card pulls all content dynamically from the backend
                 * via the variables defined above. Never hard‑code text or
                 * labels here. If a value is missing, it simply falls back
                 * to an empty string. This card is always interactive and
                 * opens the branch info modal when clicked. A short
                 * description is shown below the title and location.
                 */
            ?>
            <button type="button" class="waiter-hotel-card" data-open-modal="branchInfoModal">
                <div class="hotel-logo-wrap">
                    <img src="<?= $brandLogo ?>" alt="<?= waiter_e($brandName) ?>" onerror="this.style.opacity='0'">
                </div>
                <div class="hotel-profile-info">
                    <h2><?= waiter_e($brandName) ?></h2>
                    <p><?= waiter_e($branchName) ?><?= $branchCity !== '' ? ' · ' . waiter_e($branchCity) : '' ?></p>

                    <span class="meta-pill status-pill-<?= waiter_e(waiter_badge_class($branchStatus)) ?>"><?= waiter_e(ucfirst($branchStatus)) ?></span>
                </div>
            </button>

            <!-- Navigation -->
            <nav class="waiter-sidebar-nav" role="navigation" aria-label="Waiter navigation">
                <button type="button" class="waiter-nav-item active" data-section-target="overview">
                    <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span>
                    <span class="nav-label">Overview</span>
                </button>
                <?php if (waiter_can($permissions, 'manage_service_request', true)): ?>
                    <button type="button" class="waiter-nav-item" data-section-target="requests">
                        <span class="nav-icon"><i class="fa-solid fa-hand"></i></span>
                        <span class="nav-label">Requests</span>
                        <span class="waiter-nav-badge" id="reqNavBadge" <?= $pendingRequestsCount > 0 ? '' : 'style="display:none;"' ?>><?= waiter_e((string)$pendingRequestsCount) ?></span>
                    </button>
                <?php endif; ?>
                <?php if ($hasEnabledActions): ?>
                    <button type="button" class="waiter-nav-item" data-section-target="actions">
                        <span class="nav-icon"><i class="fa-solid fa-bolt"></i></span>
                        <span class="nav-label">Actions</span>
                    </button>
                <?php endif; ?>
                <?php // Table management shown if manage_tables permission exists or defaults to true 
                ?>
                <?php if (waiter_can($permissions, 'manage_tables', true)): ?>
                    <button type="button" class="waiter-nav-item" data-section-target="tables">
                        <span class="nav-icon"><i class="fa-solid fa-table-cells-large"></i></span>
                        <span class="nav-label">Table Management</span>
                    </button>
                <?php endif; ?>
                <?php if (waiter_can($permissions, 'collect_payment', false) || waiter_can($permissions, 'generate_bill', false)): ?>
                    <button type="button" class="waiter-nav-item" data-section-target="bills">
                        <span class="nav-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                        <span class="nav-label">Pending Bills</span>
                        <span class="waiter-nav-badge waiter-nav-badge--bill" id="billNavBadge" <?= $pendingBillsCount > 0 ? '' : 'style="display:none;"' ?>><?= waiter_e((string)$pendingBillsCount) ?></span>
                    </button>
                <?php endif; ?>
                <?php if (waiter_can($permissions, 'mark_served', true)): ?>
                    <button type="button" class="waiter-nav-item" data-section-target="pickups">
                        <span class="nav-icon"><i class="fa-solid fa-bell-concierge"></i></span>
                        <span class="nav-label">Pickups</span>
                        <span class="waiter-nav-badge waiter-nav-badge--pickup" id="pickupNavBadge" <?= $readyPickupsCount > 0 ? '' : 'style="display:none;"' ?>><?= waiter_e((string)$readyPickupsCount) ?></span>
                    </button>
                <?php endif; ?>
                <button type="button" class="waiter-nav-item" data-section-target="settings">
                    <span class="nav-icon"><i class="fa-solid fa-sliders"></i></span>
                    <span class="nav-label">Settings</span>
                </button>
            </nav>

            <!-- Waiter Profile Card (bottom) -->
            <?php
            /*
                 * The waiter profile card shows the currently authenticated
                 * staff member’s details. All values are pulled from the
                 * backend (context array) and never hard‑coded. Shift
                 * information is displayed when available to help staff
                 * quickly identify their working hours. The avatar uses
                 * correctUrlResolver logic via waiter_resolve_asset_url.
                 */
            ?>
            <button
                type="button"
                class="waiter-profile-card-btn"
                id="waiterProfileBtn"
                data-open-modal="userProfileModal"
                aria-haspopup="dialog"
                aria-controls="userProfileModal">
                <div class="waiter-sidebar-avatar">
                    <img
                        src="<?= $waiterProfilePic ?>"
                        alt="<?= waiter_e($waiterName) ?>"
                        id="sidebarWaiterAvatar"
                        onerror="this.style.display='none';document.getElementById('sidebarAvatarFallback').style.display='grid'">
                    <span class="waiter-avatar-fallback" id="sidebarAvatarFallback" style="display:none">
                        <?= waiter_e(strtoupper(substr($waiterName, 0, 1))) ?>
                    </span>
                </div>

                <div class="waiter-profile-info">
                    <h3 id="sidebarWaiterName"><?= waiter_e($waiterName) ?></h3>
                    <p>
                        <?= waiter_e(ucfirst($waiterRole)) ?>
                        <?= $waiterEmployeeCode !== '' ? ' · ' . waiter_e($waiterEmployeeCode) : '' ?>
                    </p>

                </div>

                <span class="waiter-profile-arrow"><i class="fa-solid fa-angle-right"></i></span>
            </button>

              <!-- Toggle Button -->

            <button class="waiter-sidebar-toggle" id="waiterSidebarToggle" type="button" aria-label="Toggle sidebar">
                <i class="fa-solid fa-ellipsis"></i>
            </button>

        </aside><!-- /waiter-sidebar -->

        <!-- ═══ RIGHT CONTENT ═══ -->
        <div class="waiter-right-main" id="waiterRightMain">

            <!-- Fixed Topbar -->
            <div class="waiter-topbar" id="waiterTopbar">
                <button class="waiter-topbar-hamburger" id="waiterMobileMenuBtn" type="button" aria-label="Open sidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="waiter-topbar-search">
                    <span class="waiter-search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="search" id="waiterGlobalSearch" placeholder="Search tables, requests, orders..." autocomplete="off">
                </div>

                <div class="waiter-topbar-actions">
                    <?php if ($waiterRole === 'waiter'): ?>
                        <button type="button" class="waiter-topbar-btn" data-open-modal="notificationsModal" title="Notifications" aria-label="Open notifications">
                            <i class="fa-solid fa-bell"></i>
                            <span class="waiter-topbar-badge" id="notifBadge" <?= $unreadNotifs > 0 ? '' : 'style="display:none;"' ?>><?= waiter_e((string)$unreadNotifs) ?></span>
                        </button>
                    <?php endif; ?>
                    <button type="button" class="waiter-topbar-btn <?= $soundAlertsEnabled ? 'is-active' : '' ?>" id="soundToggleBtn" aria-pressed="<?= $soundAlertsEnabled ? 'true' : 'false' ?>" title="Toggle Sound">
                        <i class="fa-solid <?= $soundAlertsEnabled ? 'fa-volume-high' : 'fa-volume-xmark' ?>"></i>
                    </button>
                    <button type="button" class="waiter-topbar-btn" id="waiterRefreshBtn" title="Refresh dashboard" aria-label="Refresh dashboard">
                        <i class="fa-solid fa-rotate"></i>
                    </button>
                </div>

                <div class="waiter-topbar-time" id="waiterLiveClock"><?= waiter_e($currentTime) ?></div>
            </div>

            <!-- Scrollable Body -->
            <div class="waiter-main-body">

                <!-- ═══════════════ OVERVIEW ═══════════════ -->
                <section class="waiter-main-section is-visible" data-waiter-section="overview">
                    <div class="waiter-section-head">
                        <div class="waiter-section-head-left">
                            <span class="waiter-section-kicker">Overview</span>
                            <h2>Waiter Dashboard</h2>
                        </div>
                    </div>

                    <!-- KPI Grid -->
                    <div class="waiter-kpi-grid">
                        <button type="button" class="waiter-kpi-card waiter-kpi-card--interactive" data-section-modal="requests">
                            <span class="waiter-kpi-label">Requests</span>
                            <strong class="waiter-kpi-value" id="kpiRequests"><?= (int)$pendingRequestsCount ?></strong>
                            <small>Open assigned-table requests</small>
                        </button>
                        <button type="button" class="waiter-kpi-card waiter-kpi-card--interactive" data-section-modal="actions">
                            <span class="waiter-kpi-label">Actions</span>
                            <strong class="waiter-kpi-value" id="kpiActions"><?= (int)$actionItemsCount ?></strong>
                            <small>Role-focused quick actions</small>
                        </button>
                        <button type="button" class="waiter-kpi-card waiter-kpi-card--interactive" data-section-modal="tables">
                            <span class="waiter-kpi-label">Table Management</span>
                            <strong class="waiter-kpi-value" id="kpiTableManagement"><?= (int)$assignedTablesCount ?></strong>
                            <small>Your assigned tables</small>
                        </button>
                        <button type="button" class="waiter-kpi-card waiter-kpi-card--interactive" data-section-modal="bills">
                            <span class="waiter-kpi-label">Bills</span>
                            <strong class="waiter-kpi-value" id="kpiBills"><?= (int)$pendingBillsCount ?></strong>
                            <small>Awaiting payment</small>
                        </button>
                        <button type="button" class="waiter-kpi-card waiter-kpi-card--interactive" data-section-modal="pickups">
                            <span class="waiter-kpi-label">Pickups</span>
                            <strong class="waiter-kpi-value" id="kpiPickups"><?= (int)$readyPickupsCount ?></strong>
                            <small>Ready at kitchen pass</small>
                        </button>
                    </div>

                    <!-- Shift Snapshot -->
                    <div class="waiter-content-card">
                        <div class="waiter-card-kicker-row">
                            <span class="waiter-section-kicker">Shift Snapshot</span>
                        </div>
                        <div class="waiter-shift-metrics">
                            <div class="waiter-metric-row">
                                <span>Tables handled</span>
                                <strong><?= (int)$shiftMetrics['tables_handled'] ?></strong>
                            </div>
                            <div class="waiter-metric-row">
                                <span>Orders closed</span>
                                <strong><?= (int)$shiftMetrics['orders_closed'] ?></strong>
                            </div>
                            <div class="waiter-metric-row">
                                <span>Average response</span>
                                <div style="display:flex;align-items:center;gap:12px;justify-content:space-between;flex-wrap:wrap;">
                                    <strong id="waiterResponseMetricValue"><?= waiter_e((string)$shiftMetrics['average_response']) ?></strong>
                                    <div class="waiter-filter-pills" id="waiterResponseRangeControls" aria-label="Average response time filters">
                                        <button type="button" class="waiter-filter-pill active" data-response-range="today">Today</button>
                                        <button type="button" class="waiter-filter-pill" data-response-range="week">Week</button>
                                        <button type="button" class="waiter-filter-pill" data-response-range="month">Month</button>
                                    </div>
                                </div>
                            </div>
                            <div class="waiter-metric-row">
                                <span>Response samples</span>
                                <strong id="waiterResponseMetricSamples"><?= (int)($shiftMetrics['average_response_sample_count'] ?? 0) ?></strong>
                            </div>
                            <div class="waiter-metric-row">
                                <span>Pending actions</span>
                                <strong><?= (int)$shiftMetrics['pending_actions'] ?></strong>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ═══════════════ REQUESTS ═══════════════ -->
                <section class="waiter-main-section" data-waiter-section="requests">
                    <div class="waiter-section-head">
                        <div class="waiter-section-head-left">
                            <span class="waiter-section-kicker">Requests</span>
                            <h2>Service Requests</h2>
                        </div>
                        <div class="waiter-section-head-right">
                            <div class="waiter-filter-pills">
                                <button type="button" class="waiter-filter-pill active" data-req-filter="all">All</button>
                                <button type="button" class="waiter-filter-pill" data-req-filter="new">New</button>
                                <button type="button" class="waiter-filter-pill" data-req-filter="new">New</button>
                                <button type="button" class="waiter-filter-pill" data-req-filter="resolved">Resolved</button>
                            </div>
                        </div>
                    </div>

                    <div class="waiter-request-grid" id="waiterRequestsGrid">
                        <?php if (!empty($serviceRequests)): ?>
                            <?php foreach ($serviceRequests as $row): ?>
                                <?php
                                $reqId     = (string)($row['id'] ?? '');
                                $reqType   = (string)($row['type'] ?? 'Service Request');
                                $tblCode   = (string)($row['table'] ?? '-');
                                $reqStatus = waiter_normalize_status($row['status'] ?? 'new', 'request');
                                $reqNote   = (string)($row['message'] ?? '');
                                $reqAt     = (string)($row['time'] ?? waiter_time_ago($row['requested_at'] ?? null));
                                $reqPrio   = (string)($row['priority'] ?? 'normal');
                                ?>
                                <div class="waiter-content-card waiter-req-card" data-req-status="<?= waiter_e($reqStatus) ?>" data-request-id="<?= waiter_e($reqId) ?>">
                                    <div class="waiter-req-top">
                                        <strong><?= waiter_e($reqType) ?></strong>
                                        <span class="waiter-status-badge waiter-status-badge--<?= waiter_e(waiter_badge_class($reqStatus)) ?>"><?= waiter_e(waiter_status_label($reqStatus, 'request')) ?></span>
                                    </div>
                                    <?php if ($reqNote !== ''): ?>
                                        <p class="waiter-req-note"><?= waiter_e($reqNote) ?></p>
                                    <?php endif; ?>
                                    <div class="waiter-req-meta">
                                        <span><i class="fa-solid fa-chair"></i> Table <?= waiter_e($tblCode) ?></span>
                                        <span><?= waiter_e($reqAt) ?></span>
                                    </div>
                                    <div class="waiter-req-actions">
                                        <button
                                            type="button"
                                            class="waiter-tbl-btn js-accept-request"
                                            data-request-id="<?= waiter_e($reqId) ?>"
                                            <?= waiter_can($permissions, 'manage_service_request', true) ? '' : 'disabled' ?>>
                                            Accept
                                        </button>
                                        <button
                                            type="button"
                                            class="waiter-tbl-btn waiter-tbl-btn--primary js-resolve-request"
                                            data-request-id="<?= waiter_e($reqId) ?>"
                                            <?= waiter_can($permissions, 'manage_service_request', true) ? '' : 'disabled' ?>>
                                            Resolve
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="waiter-content-card waiter-empty-card">
                                <i class="fa-solid fa-circle-check waiter-empty-icon"></i>
                                <strong>No open requests</strong>
                                <p>Service requests from assigned tables will appear here.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- ═══════════════ ACTIONS ═══════════════ -->
                <section class="waiter-main-section" data-waiter-section="actions">
                    <div class="waiter-section-head">
                        <div class="waiter-section-head-left">
                            <span class="waiter-section-kicker">Actions</span>
                            <h2>Quick Actions</h2>
                        </div>
                    </div>

                    <div class="waiter-content-card">
                        <p class="waiter-actions-subtitle">Fast actions based on your permissions and floor status.</p>
                        <div class="waiter-actions-grid">
                            <?php foreach ($quickActions as $action): ?>
                                <button
                                    class="waiter-action-btn <?= !((bool)($action['enabled'] ?? false)) ? 'is-disabled' : '' ?>"
                                    type="button"
                                    data-action="<?= waiter_e((string)($action['key'] ?? '')) ?>"
                                    <?= !empty($action['enabled']) ? '' : 'disabled' ?>>
                                    <span class="waiter-action-icon">
                                        <i class="fa-solid <?= waiter_e((string)($action['icon'] ?? 'fa-bolt')) ?>"></i>
                                    </span>
                                    <span><?= waiter_e((string)($action['label'] ?? 'Action')) ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                <!-- ═══════════════ TABLE MANAGEMENT ═══════════════ -->
                <section class="waiter-main-section" data-waiter-section="tables">
                    <div class="waiter-section-head">
                        <div class="waiter-section-head-left">
                            <span class="waiter-section-kicker">Tables</span>
                            <h2>Table Management</h2>
                        </div>
                        <div class="waiter-section-head-right">
                            <div class="waiter-filter-pills">
                                <button class="waiter-filter-pill active waiter-filter-btn" data-filter="all" type="button">All</button>
                                <button class="waiter-filter-pill waiter-filter-btn" data-filter="available" type="button">Available</button>
                                <button class="waiter-filter-pill waiter-filter-btn" data-filter="occupied" type="button">Occupied</button>
                                <button class="waiter-filter-pill waiter-filter-btn" data-filter="waiting" type="button">Waiting</button>
                                <button class="waiter-filter-pill waiter-filter-btn" data-filter="payment" type="button">Payment</button>
                            </div>
                        </div>
                    </div>

                    <div class="waiter-tables-grid" id="waiterTablesGrid">
                        <?php if (!empty($tables)): ?>
                            <?php foreach ($tables as $table): ?>
                                <?php $stateClass = waiter_get_table_state_class((string)($table['status'] ?? 'available')); ?>
                                <article
                                    class="waiter-table-card <?= waiter_e($stateClass) ?>"
                                    data-status="<?= waiter_e($stateClass) ?>"
                                    data-table-id="<?= waiter_e((string)($table['id'] ?? '')) ?>"
                                    data-table-code="<?= waiter_e((string)($table['code'] ?? '')) ?>">

                                    <div class="waiter-table-top">
                                        <div>
                                            <div class="waiter-table-code"><?= waiter_e((string)($table['code'] ?? 'Table')) ?></div>
                                            <div class="waiter-table-meta"><?= (int)($table['guests'] ?? 0) ?> Guests</div>
                                        </div>
                                        <span class="waiter-mini-badge"><?= waiter_e((string)($table['elapsed'] ?? 'Now')) ?></span>
                                    </div>

                                    <div class="waiter-table-middle">
                                        <div class="waiter-table-status"><?= waiter_e((string)($table['status_label'] ?? 'Available')) ?></div>
                                        <div class="waiter-table-amount">
                                            <?= ((float)($table['amount'] ?? 0) > 0) ? waiter_e(waiter_money((float)($table['amount'] ?? 0))) : 'No active bill' ?>
                                        </div>
                                    </div>

                                    <div class="waiter-table-actions">
                                        <button class="waiter-mini-btn js-open-table-modal" type="button" data-table-id="<?= waiter_e((string)($table['id'] ?? '')) ?>">
                                            <i class="fa-solid fa-eye"></i> Open
                                        </button>
                                        <button class="waiter-mini-btn js-table-primary-action" type="button"
                                            data-table-id="<?= waiter_e((string)($table['id'] ?? '')) ?>"
                                            data-order-id="<?= waiter_e((string)($table['current_order_id'] ?? '')) ?>"
                                            data-request-id="<?= waiter_e((string)($table['request_id'] ?? '')) ?>"
                                            data-status="<?= waiter_e($stateClass) ?>">
                                            <i class="fa-solid fa-bolt"></i> Action
                                        </button>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="waiter-content-card waiter-empty-card" style="grid-column:1/-1;">
                                <i class="fa-solid fa-table-cells-large waiter-empty-icon"></i>
                                <strong>No tables assigned</strong>
                                <p>Manager can assign tables from the manager dashboard.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- ═══════════════ PENDING BILLS ═══════════════ -->
                <section class="waiter-main-section" data-waiter-section="bills">
                    <div class="waiter-section-head">
                        <div class="waiter-section-head-left">
                            <span class="waiter-section-kicker">Bills</span>
                            <h2>Pending Bills</h2>
                        </div>
                        <div class="waiter-section-head-right">
                            <div class="waiter-filter-pills">
                                <button type="button" class="waiter-filter-pill active" data-bill-filter="all">All</button>
                                <button type="button" class="waiter-filter-pill" data-bill-filter="unpaid">Unpaid</button>
                                <button type="button" class="waiter-filter-pill" data-bill-filter="in_processing">In Processing</button>
                            </div>
                        </div>
                    </div>

                    <div class="waiter-bills-list" id="waiterBillsList">
                        <?php if (!empty($pendingBills)): ?>
                            <?php foreach ($pendingBills as $bill): ?>
                                <?php
                                $billId  = (string)($bill['bill_id'] ?? $bill['id'] ?? '');
                                $billNo  = (string)($bill['bill_number'] ?? $billId);
                                $tblCode = (string)($bill['table_code'] ?? $bill['table_name'] ?? '-');
                                $bStatus = waiter_normalize_status($bill['payment_status'] ?? $bill['status'] ?? 'unpaid', 'payment');
                                $billStatus = waiter_normalize_status($bill['bill_status'] ?? 'new', 'bill');
                                $amount  = (float)($bill['total_amount'] ?? $bill['grand_total'] ?? 0);
                                $updAt   = (string)($bill['updated_at'] ?? $bill['generated_at'] ?? '');
                                ?>
                                <div class="waiter-content-card waiter-bill-item" data-bill-id="<?= waiter_e($billId) ?>" data-bill-status="<?= waiter_e($bStatus) ?>" data-bill-approval-status="<?= waiter_e($billStatus) ?>">
                                    <div class="waiter-bill-top">
                                        <strong>#<?= waiter_e($billNo) ?></strong>
                                        <span class="waiter-status-badge waiter-status-badge--<?= waiter_e(waiter_badge_class($bStatus)) ?>"><?= waiter_e(waiter_status_label($bStatus, 'payment')) ?></span>
                                    </div>
                                    <div class="waiter-bill-meta">
                                        <span><i class="fa-solid fa-chair"></i> Table <?= waiter_e($tblCode) ?></span>
                                        <strong><?= waiter_e(waiter_money($amount)) ?></strong>
                                    </div>
                                    <div class="waiter-bill-footer">
                                        <small><?= waiter_e($updAt) ?></small>
                                        <?php if (waiter_can($permissions, 'collect_payment', false)): ?>
                                            <button type="button" class="waiter-tbl-btn waiter-tbl-btn--primary js-collect-payment" data-bill-id="<?= waiter_e($billId) ?>">
                                                Collect Payment
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="waiter-content-card waiter-empty-card">
                                <i class="fa-solid fa-file-invoice-dollar waiter-empty-icon"></i>
                                <strong>No pending bills</strong>
                                <p>Open bills from your tables will appear here.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- ═══════════════ PICKUPS ═══════════════ -->
                <section class="waiter-main-section" data-waiter-section="pickups">
                    <div class="waiter-section-head">
                        <div class="waiter-section-head-left">
                            <span class="waiter-section-kicker">Pickups</span>
                            <h2>Kitchen Pickups</h2>
                        </div>
                    </div>

                    <div class="waiter-pickups-list" id="waiterPickupsList">
                        <?php if (!empty($pickupOrders)): ?>
                            <?php foreach ($pickupOrders as $row): ?>
                                <?php
                                $kot     = (string)($row['kot'] ?? ('#' . ($row['order_number'] ?? 'ORDER')));
                                $tbl     = (string)($row['table'] ?? '');
                                $items   = (int)($row['items'] ?? 0);
                                $orderId = (string)($row['id'] ?? $row['order_id'] ?? '');
                                $pTime   = (string)($row['time'] ?? waiter_time_ago($row['updated_at'] ?? null));
                                $note    = (string)($row['chef_note'] ?? '');
                                ?>
                                <div class="waiter-content-card waiter-pickup-item">
                                    <div class="waiter-pickup-top">
                                        <strong><?= waiter_e($kot) ?></strong>
                                        <span class="waiter-ready-pill"><i class="fa-solid fa-check"></i> Ready</span>
                                    </div>
                                    <div class="waiter-pickup-meta">
                                        <span>Table <?= waiter_e($tbl) ?> · <?= $items ?> items</span>
                                        <small><?= waiter_e($pTime) ?></small>
                                    </div>
                                    <?php if ($note !== ''): ?>
                                        <p class="waiter-pickup-note"><?= waiter_e($note) ?></p>
                                    <?php endif; ?>
                                    <div class="waiter-req-actions" style="margin-top:12px;">
                                        <button
                                            class="waiter-tbl-btn waiter-tbl-btn--primary js-mark-served"
                                            type="button"
                                            data-order-id="<?= waiter_e($orderId) ?>"
                                            <?= waiter_can($permissions, 'mark_served', true) ? '' : 'disabled' ?>>
                                            <i class="fa-solid fa-check"></i> Mark Served
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="waiter-content-card waiter-empty-card">
                                <i class="fa-solid fa-kitchen-set waiter-empty-icon"></i>
                                <strong>No ready pickups</strong>
                                <p>Orders ready at the kitchen pass will appear here.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- ═══════════════ SETTINGS ═══════════════ -->
                <section class="waiter-main-section" data-waiter-section="settings">
                    <div class="waiter-section-head">
                        <div class="waiter-section-head-left">
                            <span class="waiter-section-kicker">Settings</span>
                            <h2>Waiter Preferences</h2>
                        </div>
                    </div>

                    <div class="waiter-settings-panel">

                        <div class="waiter-settings-block">
                            <div class="waiter-settings-block-head">
                                <h4><i class="fa-solid fa-circle-dot"></i> Availability Status</h4>
                            </div>
                            <div class="waiter-settings-list">
                                <div class="waiter-setting-row">
                                    <div class="waiter-setting-info">
                                        <strong>Active / Inactive</strong>
                                        <p>Toggle your availability. When inactive, choose your break duration — your status auto-resets when time expires.</p>
                                        <span
                                            class="waiter-avail-badge <?= $waiterStatus === 'active' ? 'is-active' : 'is-inactive' ?>"
                                            id="waiterCurrentStatusBadge">
                                            <?= $waiterStatus === 'active' ? '● Active' : '● Inactive' ?>
                                        </span>
                                        <div class="waiter-inactive-countdown" id="inactiveCountdown" style="display:none;"></div>
                                    </div>
                                    <div class="waiter-setting-control">
                                        <label class="waiter-switch" aria-label="Toggle waiter active status">
                                            <input type="checkbox" id="waiterStatusToggle" <?= $waiterStatus === 'active' ? 'checked' : '' ?>>
                                            <span class="waiter-switch-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="waiter-settings-block">
                            <div class="waiter-settings-block-head">
                                <h4><i class="fa-solid fa-sliders"></i> General</h4>
                            </div>
                            <div class="waiter-settings-list">
                                <div class="waiter-setting-row">
                                    <div class="waiter-setting-info">
                                        <strong>Theme Mode</strong>
                                        <p>Switch between light and dark mode.</p>
                                    </div>
                                    <div class="waiter-setting-control">
                                        <div class="waiter-segmented-ctrl" id="waiterThemeSwitch" role="tablist" aria-label="Theme mode selector">
                                            <button type="button" class="waiter-seg-btn <?= $activeTheme === 'light' ? 'active' : '' ?>" data-theme-mode="light" aria-pressed="<?= $activeTheme === 'light' ? 'true' : 'false' ?>">
                                                <i class="fa-solid fa-sun"></i> Light
                                            </button>
                                            <button type="button" class="waiter-seg-btn <?= $activeTheme === 'dark' ? 'active' : '' ?>" data-theme-mode="dark" aria-pressed="<?= $activeTheme === 'dark' ? 'true' : 'false' ?>">
                                                <i class="fa-solid fa-moon"></i> Dark
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="waiter-setting-row">
                                    <div class="waiter-setting-info">
                                        <strong>Sound Alerts</strong>
                                        <p>Play alert sound on new requests and pickups.</p>
                                    </div>
                                    <div class="waiter-setting-control">
                                        <label class="waiter-switch" aria-label="Toggle sound alerts">
                                            <input type="checkbox" id="settingsSoundToggle" <?= $soundAlertsEnabled ? 'checked' : '' ?>>
                                            <span class="waiter-switch-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="waiter-setting-row">
                                    <div class="waiter-setting-info">
                                        <strong>Compact View</strong>
                                        <p>Reduce spacing for a denser layout.</p>
                                    </div>
                                    <div class="waiter-setting-control">
                                        <label class="waiter-switch" aria-label="Toggle compact mode">
                                            <input type="checkbox" id="compactModeToggle">
                                            <span class="waiter-switch-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="waiter-settings-block">
                            <div class="waiter-settings-block-head">
                                <h4><i class="fa-solid fa-bell"></i> Alert Sound</h4>
                            </div>
                            <div class="waiter-settings-list">
                                <div class="waiter-setting-row">
                                    <div class="waiter-setting-info">
                                        <strong>Notification Sound</strong>
                                        <p>Choose the sound for new alerts.</p>
                                    </div>
                                    <div class="waiter-setting-control">
                                        <select id="notificationSoundSelect" class="waiter-select" aria-label="Notification sound selector">
                                            <?php $sound1 = waiter_resolve_asset_url('RestaurantAssets/AlertSounds/Sound1.mpeg', 'RestaurantAssets/AlertSounds/Sound1.mpeg'); ?>
                                            <?php $sound2 = waiter_resolve_asset_url('RestaurantAssets/AlertSounds/Sound2.mpeg', 'RestaurantAssets/AlertSounds/Sound1.mpeg'); ?>
                                            <?php $sound3 = waiter_resolve_asset_url('RestaurantAssets/AlertSounds/Sound3.mpeg', 'RestaurantAssets/AlertSounds/Sound1.mpeg'); ?>
                                            <option value="<?= waiter_e($sound1) ?>" <?= $selectedNotificationSound === $sound1 ? 'selected' : '' ?>>Sound 1</option>
                                            <option value="<?= waiter_e($sound2) ?>" <?= $selectedNotificationSound === $sound2 ? 'selected' : '' ?>>Sound 2</option>
                                            <option value="<?= waiter_e($sound3) ?>" <?= $selectedNotificationSound === $sound3 ? 'selected' : '' ?>>Sound 3</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>

            </div><!-- /waiter-main-body -->
        </div><!-- /waiter-right-main -->
    </div><!-- /waiter-main-shell -->

    <!-- Mobile sidebar overlay -->
    <div class="waiter-sidebar-overlay" id="waiterSidebarOverlay"></div>

    <!-- ═══════════════════════════════════════════════════════════════════════
         MODALS
         ═══════════════════════════════════════════════════════════════════════ -->

    <!-- User Profile Modal -->
    <div class="app-modal" id="userProfileModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="userProfileModalTitle">
        <div class="modal-overlay" data-close="modal"></div>
        <div class="modal-dialog modal-dialog--profile">
            <div class="modal-header">
                <div>
                    <div class="app-eyebrow">Profile</div>
                    <h3 id="userProfileModalTitle">My Profile</h3>
                </div>
                <button type="button" data-close="modal" aria-label="Close profile modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="waiter-profile-hero">
                    <div class="waiter-profile-avatar-ring">
                        <img
                            src="<?= $waiterProfilePic ?>"
                            alt="<?= waiter_e($waiterName) ?>"
                            onerror="this.style.display='none';this.nextElementSibling.style.display='grid';">
                        <span class="waiter-avatar-fallback waiter-avatar-fallback--lg" style="display:none">
                            <?= waiter_e(strtoupper(substr($waiterName, 0, 1))) ?>
                        </span>
                    </div>

                    <div class="waiter-profile-hero-info">
                        <h2><?= waiter_e($waiterName) ?></h2>
                        <p>
                            <?= waiter_e(ucfirst($waiterRole)) ?>
                            <?= $waiterEmployeeCode !== '' ? ' · ' . waiter_e($waiterEmployeeCode) : '' ?>
                        </p>

                        <div class="waiter-profile-hero-pills">
                            <span class="waiter-meta-pill"><?= waiter_e(ucfirst($waiterRole)) ?></span>
                            <span class="waiter-meta-pill <?= $waiterStatus === 'active' ? 'is-success' : 'is-danger' ?>">
                                <?= waiter_e(ucfirst($waiterStatus)) ?>
                            </span>
                            <?php if ($shiftName !== '' && $shiftName !== 'Shift Not Set'): ?>
                                <span class="waiter-meta-pill"><?= waiter_e($shiftName) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="waiter-profile-grid">
                    <div class="waiter-profile-card">
                        <div class="app-eyebrow">Contact</div>
                        <div class="waiter-info-list">
                            <div class="waiter-info-row">
                                <span>Email</span>
                                <strong><?= waiter_e($waiterEmail !== '' ? $waiterEmail : '-') ?></strong>
                            </div>
                            <div class="waiter-info-row">
                                <span>Phone</span>
                                <strong><?= waiter_e($waiterPhone !== '' ? $waiterPhone : '-') ?></strong>
                            </div>
                            <div class="waiter-info-row">
                                <span>User ID</span>
                                <strong><?= waiter_e($userId !== '' ? $userId : '-') ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="waiter-profile-card">
                        <div class="app-eyebrow">Branch</div>
                        <div class="waiter-info-list">
                            <div class="waiter-info-row">
                                <span>Brand</span>
                                <strong><?= waiter_e($brandName) ?></strong>
                            </div>
                            <div class="waiter-info-row">
                                <span>Branch</span>
                                <strong><?= waiter_e($branchName) ?></strong>
                            </div>
                            <div class="waiter-info-row">
                                <span>City</span>
                                <strong><?= waiter_e($branchCity !== '' ? $branchCity : '-') ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="waiter-btn-secondary" data-close="modal">Close</button>
            </div>
        </div>
    </div>

    <!-- Table Detail Modal -->
    <div class="app-modal" id="tableModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="tableModalTitle">
        <div class="modal-overlay" data-close="modal"></div>
        <div class="modal-dialog modal-dialog--lg">
            <div class="modal-header">
                <div>
                    <div class="app-eyebrow">Table Detail</div>
                    <h3 id="tableModalTitle">Table</h3>
                </div>
                <button type="button" data-close="modal" aria-label="Close table modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="modal-body" id="tableModalBody">
                <div class="waiter-modal-loading">
                    <div class="waiter-spinner small"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Branch Information Modal -->
    <div class="app-modal" id="branchInfoModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="branchInfoModalTitle">
        <div class="modal-overlay" data-close="modal"></div>
        <div class="modal-dialog">
            <div class="modal-header">
                <div>
                    <div class="app-eyebrow">Branch</div>
                    <h3 id="branchInfoModalTitle">Branch Information</h3>
                </div>
                <button type="button" data-close="modal" aria-label="Close branch modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="waiter-profile-grid">
                    <div class="waiter-profile-card">
                        <div class="waiter-info-list">
                            <div class="waiter-info-row">
                                <span>Brand</span>
                                <strong><?= waiter_e($brandName) ?></strong>
                            </div>
                            <div class="waiter-info-row">
                                <span>Branch</span>
                                <strong><?= waiter_e($branchName) ?></strong>
                            </div>
                            <div class="waiter-info-row">
                                <span>Status</span>
                                <strong><?= waiter_e(ucfirst($branchStatus)) ?></strong>
                            </div>
                            <div class="waiter-info-row">
                                <span>City</span>
                                <strong><?= waiter_e($branchCity !== '' ? $branchCity : '-') ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="waiter-profile-card">
                        <div class="waiter-info-list">
                            <div class="waiter-info-row">
                                <span>Assigned Waiter</span>
                                <strong><?= waiter_e($waiterName) ?></strong>
                            </div>
                            <div class="waiter-info-row">
                                <span>Shift</span>
                                <strong><?= waiter_e($shiftName !== '' ? $shiftName : '-') ?></strong>
                            </div>
                            <div class="waiter-info-row waiter-info-row--block">
                                <span>Address / Description</span>
                                <strong><?= waiter_e($branchDesc !== '' ? $branchDesc : '-') ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="waiter-btn-secondary" data-close="modal">Close</button>
            </div>
        </div>
    </div>

    <?php if ($waiterRole === 'waiter'): ?>
        <!-- Notifications Modal -->
        <div class="app-modal" id="notificationsModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="notificationsModalTitle">
            <div class="modal-overlay" data-close="modal"></div>
            <div class="modal-dialog modal-dialog--lg waiter-notification-dialog">
                <div class="modal-header">
                    <div>
                        <div class="app-eyebrow">Alerts</div>
                        <h3 id="notificationsModalTitle">Waiter Notifications</h3>
                    </div>
                    <button type="button" data-close="modal" aria-label="Close notifications modal">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="waiter-modal-toolbar">
                    <div class="waiter-modal-toolbar-meta">
                        <span class="waiter-modal-counter" id="waiterNotificationsCounter"><?= waiter_e((string)$unreadNotifs) ?> unread</span>
                    </div>
                    <div class="waiter-modal-toolbar-actions">
                        <button type="button" class="waiter-btn-secondary waiter-btn-secondary--compact" id="waiterNotificationsRefreshBtn">
                            <i class="fa-solid fa-rotate"></i> Refresh
                        </button>
                        <button type="button" class="waiter-btn-secondary waiter-btn-secondary--compact" id="waiterNotificationsMarkAllBtn">
                            <i class="fa-solid fa-circle-check"></i> Mark All Read
                        </button>
                        <button type="button" class="waiter-btn-secondary waiter-btn-secondary--compact" id="waiterNotificationsClearBtn">
                            <i class="fa-solid fa-trash-can"></i> Clear All
                        </button>
                    </div>
                </div>

                <div class="modal-body" id="waiterNotificationsBody">
                    <?php if (!empty($notifications)): ?>
                        <div class="waiter-modal-list">
                            <?php foreach ($notifications as $notification): ?>
                                <?php
                                $nType = (string)($notification['title'] ?? $notification['type'] ?? 'Notification');
                                $nMessage = (string)($notification['message'] ?? $notification['note'] ?? '-');
                                $nTime = (string)($notification['time'] ?? waiter_time_ago($notification['created_at'] ?? null));
                                $nOrderRef = (string)($notification['order_ref'] ?? '');
                                $nTable = (string)($notification['table_code'] ?? $notification['table_number'] ?? '-');
                                $nSection = (string)($notification['section'] ?? 'requests');
                                $nStatus = (string)($notification['status'] ?? ((int)($notification['is_read'] ?? 0) === 1 ? 'read' : 'unread'));
                                ?>
                                <?php $notificationId = (string)($notification['id'] ?? ''); ?>
                                <article
                                    class="waiter-notification-item <?= $nStatus === 'read' ? 'is-read' : 'is-unread' ?>"
                                    data-notification-id="<?= waiter_e($notificationId) ?>">
                                    <div class="waiter-notification-top">
                                        <div class="waiter-notification-title-group">
                                            <span class="waiter-notification-dot" aria-hidden="true"></span>
                                            <strong><?= waiter_e($nType) ?></strong>
                                        </div>
                                        <div class="waiter-notification-actions">
                                            <button
                                                type="button"
                                                class="waiter-mini-btn js-notification-open"
                                                data-notification-id="<?= waiter_e($notificationId) ?>"
                                                data-section="<?= waiter_e($nSection) ?>">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Open
                                            </button>
                                            <?php if ($nStatus !== 'read'): ?>
                                                <button
                                                    type="button"
                                                    class="waiter-mini-btn js-notification-read"
                                                    data-notification-id="<?= waiter_e($notificationId) ?>">
                                                    <i class="fa-solid fa-circle-check"></i> Read
                                                </button>
                                            <?php endif; ?>
                                            <button
                                                type="button"
                                                class="waiter-mini-btn js-notification-delete"
                                                data-notification-id="<?= waiter_e($notificationId) ?>">
                                                <i class="fa-solid fa-trash-can"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                    <div class="waiter-notification-grid">
                                        <div class="waiter-notification-row"><span>Table</span><strong><?= waiter_e($nTable) ?></strong></div>
                                        <div class="waiter-notification-row"><span>Section</span><strong><?= waiter_e(ucfirst($nSection)) ?></strong></div>
                                        <div class="waiter-notification-row"><span>Order Ref</span><strong><?= waiter_e($nOrderRef !== '' ? $nOrderRef : '-') ?></strong></div>
                                        <div class="waiter-notification-row"><span>Time</span><strong><?= waiter_e($nTime) ?></strong></div>
                                        <div class="waiter-notification-row"><span>Status</span><strong><?= waiter_e(ucfirst($nStatus)) ?></strong></div>
                                    </div>
                                    <p><?= waiter_e($nMessage) ?></p>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="waiter-empty-state">
                            <i class="fa-solid fa-bell-slash"></i>
                            <strong>No waiter notifications</strong>
                            <p>Service requests, pickups, bill alerts, and table alerts will appear here.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="waiter-btn-secondary" data-close="modal">Close</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="app-modal" id="waiterSectionModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="waiterSectionModalTitle">
        <div class="modal-overlay" data-close="modal"></div>
        <div class="modal-dialog modal-dialog--lg waiter-section-dialog">
            <div class="modal-header">
                <div>
                    <div class="app-eyebrow">Overview</div>
                    <h3 id="waiterSectionModalTitle">Section Details</h3>
                </div>
                <button type="button" data-close="modal" aria-label="Close section modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="waiter-modal-toolbar">
                <div class="waiter-modal-toolbar-meta">
                    <span class="waiter-modal-counter" id="waiterSectionCounter">0 items</span>
                </div>
                <div class="waiter-modal-toolbar-actions">
                    <button type="button" class="waiter-btn-secondary waiter-btn-secondary--compact" id="waiterSectionRefreshBtn">
                        <i class="fa-solid fa-rotate"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="modal-body" id="waiterSectionModalBody">
                <div class="waiter-empty-state">
                    <i class="fa-solid fa-layer-group"></i>
                    <strong>Select a card</strong>
                    <p>Overview cards load their own filtered data here.</p>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="waiter-btn-secondary" data-close="modal">Close</button>
            </div>
        </div>
    </div>

    <?php if ($waiterRole === 'waiter'): ?>
        <div class="waiter-live-request-host" id="waiterLiveRequestHost" aria-live="polite" aria-atomic="true"></div>
    <?php endif; ?>


    <!-- Inactive Time Picker Modal -->
    <div class="waiter-modal-wrap" id="inactivePickerModal" aria-hidden="true" style="z-index:9999;">
        <div class="waiter-modal-backdrop" data-close-modal="inactivePickerModal" id="inactiveModalBackdrop"></div>
        <div class="waiter-modal-dialog waiter-modal-dialog--sm">
            <div class="waiter-modal-head">
                <h3><i class="fa-solid fa-clock" style="color:#d97706;margin-right:8px;"></i>Set Inactive Duration</h3>
                <button type="button" class="waiter-modal-close" id="inactivePickerClose" data-close-modal="inactivePickerModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="waiter-modal-body">
                <p class="waiter-inactive-desc">How long will you be away? Your status auto-resets when time expires.</p>
                <div class="waiter-inactive-options">
                    <button type="button" class="waiter-inactive-opt" data-minutes="10">10 min</button>
                    <button type="button" class="waiter-inactive-opt" data-minutes="20">20 min</button>
                    <button type="button" class="waiter-inactive-opt" data-minutes="30">30 min</button>
                    <button type="button" class="waiter-inactive-opt" data-minutes="60">1 hour</button>
                </div>
                <div class="waiter-inactive-custom">
                    <label>Custom duration (minutes)</label>
                    <div class="waiter-inactive-custom-row">
                        <input type="number" id="customInactiveMinutes" min="1" max="480" placeholder="e.g. 45">
                        <button type="button" class="waiter-btn-primary" id="applyCustomInactiveBtn">Apply</button>
                    </div>
                </div>
                <div class="waiter-modal-actions" style="justify-content:flex-end;">
                    <button type="button" class="waiter-btn-secondary" id="cancelInactiveBtn" data-close-modal="inactivePickerModal">
                        Cancel (Stay Active)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Data -->
    <script>
        window.WaiterPageData = {
            apiBase: <?= json_encode($apiBase, JSON_UNESCAPED_SLASHES) ?>,
            brandId: <?= json_encode($brandId, JSON_UNESCAPED_SLASHES) ?>,
            branchId: <?= json_encode($branchId, JSON_UNESCAPED_SLASHES) ?>,
            userId: <?= json_encode($userId, JSON_UNESCAPED_SLASHES) ?>,
            waiterName: <?= json_encode($waiterName, JSON_UNESCAPED_SLASHES) ?>,
            waiterRole: <?= json_encode($waiterRole, JSON_UNESCAPED_SLASHES) ?>,
            branchName: <?= json_encode($branchName, JSON_UNESCAPED_SLASHES) ?>,
            shiftName: <?= json_encode($shiftName, JSON_UNESCAPED_SLASHES) ?>,
            permissions: <?= json_encode($permissions, JSON_UNESCAPED_SLASHES) ?>,
            unreadNotifs: <?= json_encode($unreadNotifs, JSON_UNESCAPED_SLASHES) ?>,
            pendingRequests: <?= json_encode($pendingRequestsCount, JSON_UNESCAPED_SLASHES) ?>,
            readyPickups: <?= json_encode($readyPickupsCount, JSON_UNESCAPED_SLASHES) ?>,
            pendingBills: <?= json_encode($pendingBillsCount, JSON_UNESCAPED_SLASHES) ?>,
            waiterStatus: <?= json_encode($waiterStatus, JSON_UNESCAPED_SLASHES) ?>,
            responseMetrics: <?= json_encode($responseMetrics, JSON_UNESCAPED_SLASHES) ?>,
            userSettings: <?= json_encode(array_merge($userSettings, ['lite_notification_sound' => $selectedNotificationSound]), JSON_UNESCAPED_SLASHES) ?>,
            defaultNotificationSound: <?= json_encode($defaultNotificationSoundUrl, JSON_UNESCAPED_SLASHES) ?>,
        };
    </script>
    <script src="<?= waiter_e(rs_base_url('RestaurantAssets/js/waiter.js')) ?>"></script>

</body>

</html>
