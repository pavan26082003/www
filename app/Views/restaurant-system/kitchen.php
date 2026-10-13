<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Kitchen Dashboard View — Enterprise SaaS Layout
| Same structure pattern as waiter.php:
| Collapsible sidebar + fixed topbar + production-only KOT workbench
|--------------------------------------------------------------------------
*/

$brandId  = (string)($brandId  ?? '');
$branchId = (string)($branchId ?? '');
$userId   = (string)($userId   ?? '');

$brand         = is_array($brand ?? null)         ? $brand         : [];
$branch        = is_array($branch ?? null)        ? $branch        : [];
$context       = is_array($context ?? null)       ? $context       : [];
$permissions   = is_array($permissions ?? null)   ? $permissions   : [];
$snapshot      = is_array($snapshot ?? null)      ? $snapshot      : [];
$summary       = is_array($summary ?? null)       ? $summary       : [];
$stations      = is_array($stations ?? null)      ? $stations      : [];
$orders        = is_array($orders ?? null)        ? $orders        : [];
$selectedOrder = is_array($selectedOrder ?? null) ? $selectedOrder : null;
$userSettings  = is_array($userSettings ?? null)  ? $userSettings  : [];
$defaultNotificationSound = (string)($defaultNotificationSound ?? 'RestaurantAssets/AlertSounds/Sound1.mpeg');

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/
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

if (!function_exists('kitchen_resolve_asset_url')) {
    function kitchen_resolve_asset_url(?string $path, string $fallback): string
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

        if (stripos($normalized, 'RestaurantAssets/') === 0) {
            return rs_html_escape(rs_base_url($normalized));
        }

        if (stripos($normalized, 'brands/') === 0) {
            return rs_html_escape(rs_base_url('RestaurantAssets/' . $normalized));
        }

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
            'user-profile/',
            'AlertSounds/',
        ];

        foreach ($restaurantAssetFolders as $prefix) {
            if (stripos($normalized, $prefix) === 0) {
                return rs_html_escape(rs_base_url('RestaurantAssets/' . $normalized));
            }
        }

        return rs_html_escape(rs_base_url($normalized));
    }
}

if (!function_exists('kitchen_e')) {
    function kitchen_e($value): string
    {
        return rs_html_escape($value);
    }
}

if (!function_exists('kitchen_money')) {
    function kitchen_money($amount): string
    {
        return 'Rs ' . number_format((float)$amount, 0);
    }
}

if (!function_exists('kitchen_time_ago')) {
    function kitchen_time_ago(?string $datetime, string $fallback = 'Now'): string
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

if (!function_exists('kitchen_safe_status')) {
    function kitchen_safe_status(?string $status): string
    {
        $status = strtolower(trim(str_replace([' ', '-'], '_', (string)$status)));

        // Kitchen owns only: new -> accepted -> preparing -> ready.
        // Legacy/customer/waiter statuses are normalized for safe display.
        if ($status === '' || $status === 'pending' || $status === 'placed' || $status === 'requested') {
            return 'new';
        }

        if ($status === 'cooking' || $status === 'in_progress' || $status === 'processing') {
            return 'preparing';
        }

        if ($status === 'served' || $status === 'completed' || $status === 'closed' || $status === 'paid') {
            return 'ready';
        }

        if ($status === 'cancelled' || $status === 'canceled' || $status === 'rejected') {
            return 'cancelled';
        }

        $allowed = ['new', 'accepted', 'preparing', 'ready', 'cancelled'];
        return in_array($status, $allowed, true) ? $status : 'new';
    }
}

if (!function_exists('kitchen_status_label')) {
    function kitchen_status_label(?string $status): string
    {
        switch (kitchen_safe_status($status)) {
            case 'accepted':
                return 'Accepted';
            case 'preparing':
                return 'Preparing';
            case 'ready':
                return 'Ready';
            case 'cancelled':
                return 'Cancelled';
            case 'new':
            default:
                return 'New';
        }
    }
}

if (!function_exists('kitchen_status_class')) {
    function kitchen_status_class(?string $status): string
    {
        switch (kitchen_safe_status($status)) {
            case 'accepted':
                return 'kitchen-status-accepted';
            case 'preparing':
                return 'kitchen-status-preparing';
            case 'ready':
                return 'kitchen-status-ready';
            case 'cancelled':
                return 'kitchen-status-cancelled';
            case 'new':
            default:
                return 'kitchen-status-new';
        }
    }
}

if (!function_exists('kitchen_priority_class')) {
    function kitchen_priority_class(?string $priority): string
    {
        $priority = strtolower(trim((string)$priority));
        return in_array($priority, ['normal', 'high', 'urgent'], true) ? $priority : 'normal';
    }
}

if (!function_exists('kitchen_badge_class')) {
    function kitchen_badge_class(string $status): string
    {
        $status = strtolower(trim(str_replace([' ', '-'], '_', $status)));

        if (in_array($status, ['active', 'available', 'ready', 'served', 'completed', 'open', 'paid', 'approved', 'closed'], true)) return 'success';
        if (in_array($status, ['pending', 'new', 'accepted', 'preparing', 'processing', 'in_processing', 'busy', 'occupied', 'unpaid'], true)) return 'warning';
        if (in_array($status, ['cancelled', 'canceled', 'rejected', 'failed', 'inactive', 'blocked', 'maintenance'], true)) return 'danger';

        return 'neutral';
    }
}

if (!function_exists('kitchen_can')) {
    function kitchen_can(array $permissions, string $key, bool $default = true): bool
    {
        return array_key_exists($key, $permissions) ? (bool)$permissions[$key] : $default;
    }
}

if (!function_exists('kitchen_count_status')) {
    function kitchen_count_status(array $orders, array $statuses): int
    {
        $count = 0;
        foreach ($orders as $order) {
            if (!is_array($order)) continue;
            if (in_array(kitchen_safe_status((string)($order['status'] ?? 'new')), $statuses, true)) {
                $count++;
            }
        }
        return $count;
    }
}


if (!function_exists('kitchen_count_priority')) {
    function kitchen_count_priority(array $orders, array $priorities): int
    {
        $count = 0;
        foreach ($orders as $order) {
            if (!is_array($order)) continue;
            $priority = kitchen_priority_class((string)($order['priority'] ?? 'normal'));
            $status = kitchen_safe_status((string)($order['status'] ?? 'new'));
            if ($status !== 'cancelled' && in_array($priority, $priorities, true)) {
                $count++;
            }
        }
        return $count;
    }
}

/*
|--------------------------------------------------------------------------
| View-safe values
|--------------------------------------------------------------------------
*/
$brandName    = (string)($brand['name'] ?? $context['brand_name'] ?? 'Restaurant');
$brandLogo    = kitchen_resolve_asset_url($brand['logo'] ?? $brand['photo'] ?? null, 'RestaurantAssets/default/default_logo.svg');
$brandFavicon = kitchen_resolve_asset_url($brand['favicon'] ?? null, 'RestaurantAssets/default/default_favicon.svg');

$branchName   = (string)($context['branch_name'] ?? $branch['name'] ?? 'Branch');
$branchCity   = (string)($context['branch_city'] ?? $branch['city'] ?? '');
$branchStatus = (string)($context['branch_status'] ?? $branch['status'] ?? 'active');
$branchDesc   = (string)($context['branch_address'] ?? $branch['address'] ?? $context['branch_description'] ?? '');

$kitchenName  = (string)($context['kitchen_name'] ?? 'Central Kitchen');
$shiftName    = (string)($context['shift_name'] ?? 'Live Shift');
$chefName     = (string)($context['full_name'] ?? $context['chef_name'] ?? 'Kitchen Staff');
$chefRole     = (string)($context['role'] ?? 'chef');
$chefEmail    = (string)($context['email'] ?? $context['chef_email'] ?? '');
$chefPhone    = (string)($context['phone'] ?? $context['chef_phone'] ?? '');
$chefCode     = (string)($context['employee_code'] ?? '');
$chefStatus   = (string)($context['chef_status'] ?? $context['status'] ?? 'active');

$chefProfilePic = kitchen_resolve_asset_url(
    $context['profile_pic'] ?? $context['photo'] ?? $context['chef_avatar'] ?? null,
    'RestaurantAssets/default/default-avatar.svg'
);

$summary = array_merge([
    'new_orders'      => kitchen_count_status($orders, ['new']),
    'accepted'        => kitchen_count_status($orders, ['accepted']),
    'preparing'       => kitchen_count_status($orders, ['preparing']),
    'ready_to_serve'  => kitchen_count_status($orders, ['ready']),
    'urgent_attention'=> kitchen_count_priority($orders, ['urgent', 'high']),
    'total_active'    => count($orders),
], $summary);

$newOrdersCount     = (int)($summary['new_orders'] ?? 0);
$acceptedCount      = (int)($summary['accepted'] ?? 0);
$preparingCount     = (int)($summary['preparing'] ?? 0);
$readyCount         = (int)($summary['ready_to_serve'] ?? 0);
$urgentAttentionCount = (int)($summary['urgent_attention'] ?? 0);
$totalActiveCount   = (int)($summary['total_active'] ?? count($orders));

$selectedOrder = is_array($selectedOrder)
    ? $selectedOrder
    : (!empty($orders) && is_array($orders[0] ?? null) ? $orders[0] : null);

$selectedOrderId = (string)($selectedOrder['order_id'] ?? $selectedOrder['id'] ?? '');
$selectedStatus  = kitchen_safe_status((string)($selectedOrder['status'] ?? 'new'));

$userSettings = array_merge([
    'theme' => 'light',
    'sound_alerts_enabled' => 1,
    'lite_notification_sound' => $defaultNotificationSound,
], $userSettings);

$activeTheme = in_array((string)$userSettings['theme'], ['light', 'dark'], true)
    ? (string)$userSettings['theme']
    : 'light';

$soundAlertsEnabled = (int)($userSettings['sound_alerts_enabled'] ?? 1) === 1;

$kitchenSound1 = kitchen_resolve_asset_url('RestaurantAssets/AlertSounds/Sound1.mpeg', 'RestaurantAssets/AlertSounds/Sound1.mpeg');
$kitchenSound2 = kitchen_resolve_asset_url('RestaurantAssets/AlertSounds/Sound2.mpeg', 'RestaurantAssets/AlertSounds/Sound1.mpeg');
$kitchenSound3 = kitchen_resolve_asset_url('RestaurantAssets/AlertSounds/Sound3.mpeg', 'RestaurantAssets/AlertSounds/Sound1.mpeg');
$defaultNotificationSoundUrl = kitchen_resolve_asset_url($defaultNotificationSound, 'RestaurantAssets/AlertSounds/Sound1.mpeg');
$selectedNotificationSound = kitchen_resolve_asset_url(
    (string)($userSettings['lite_notification_sound'] ?? $defaultNotificationSound),
    'RestaurantAssets/AlertSounds/Sound1.mpeg'
);

$initialSnapshot = array_merge([
    'context'  => $context,
    'summary'  => $summary,
    'orders'   => $orders,
    'stations' => $stations,
    'settings' => $userSettings,
], $snapshot);

$providedApiBase = trim((string)($apiBase ?? ''));
$apiBase = $providedApiBase !== ''
    ? rtrim($providedApiBase, '/')
    : (function_exists('site_url')
        ? rtrim((string)site_url('/restaurant-system/kitchen/api'), '/')
        : rtrim(rs_base_url('/restaurant-system/kitchen/api'), '/'));

$pageTitle = 'Kitchen – ' . $branchName;
$currentTime = date('h:i A');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= kitchen_e($pageTitle) ?></title>

    <link rel="icon" href="<?= $brandFavicon ?>">
    <link rel="stylesheet" href="<?= kitchen_e(rs_base_url('RestaurantAssets/css/global.css')) ?>">
    <link rel="stylesheet" href="<?= kitchen_e(rs_base_url('RestaurantAssets/css/kitchen.css')) ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body.kitchen-page {
            --kitchen-glass: rgba(255, 255, 255, .78);
            --kitchen-glass-strong: rgba(255, 255, 255, .92);
            --kitchen-glass-border: rgba(15, 23, 42, .10);
            --kitchen-focus-ring: rgba(66, 133, 244, .30);
            background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 42%, #f7fafc 100%) !important;
            letter-spacing: 0;
        }

        body.kitchen-page.kitchen-dark-mode {
            --kitchen-glass: rgba(15, 23, 42, .74);
            --kitchen-glass-strong: rgba(17, 24, 39, .92);
            --kitchen-glass-border: rgba(148, 163, 184, .18);
            --kitchen-focus-ring: rgba(138, 180, 248, .32);
            background: linear-gradient(135deg, #07111f 0%, #101827 48%, #0b1220 100%) !important;
        }

        .kitchen-sidebar,
        .kitchen-topbar,
        .kitchen-content-card,
        .premium-kitchen-card,
        .kitchen-settings-panel,
        .app-modal-content {
            background: var(--kitchen-glass) !important;
            border-color: var(--kitchen-glass-border) !important;
            box-shadow: 0 20px 60px rgba(15, 23, 42, .10) !important;
            backdrop-filter: blur(24px) saturate(160%);
            -webkit-backdrop-filter: blur(24px) saturate(160%);
        }

        .kitchen-topbar-search,
        .kitchen-filter-btn,
        .kitchen-seg-btn,
        .kitchen-select,
        .kitchen-detail-meta-card,
        .kitchen-station-card,
        .kitchen-detail-item-card {
            background: var(--kitchen-glass-strong) !important;
            border-color: var(--kitchen-glass-border) !important;
        }

        .kitchen-workbench-btn.primary,
        .kitchen-mini-btn:not(.kitchen-mini-btn--success) {
            background: #1a73e8 !important;
            color: #fff !important;
        }

        .kitchen-workbench-btn.success,
        .kitchen-mini-btn--success {
            background: #188038 !important;
            color: #fff !important;
        }

        .kitchen-workbench-btn.info {
            background: #0b57d0 !important;
            color: #fff !important;
        }

        .kitchen-page button:focus-visible,
        .kitchen-page input:focus-visible,
        .kitchen-page select:focus-visible {
            outline: 3px solid var(--kitchen-focus-ring) !important;
            outline-offset: 2px;
        }
    </style>
</head>

<body
    class="kitchen-page theme-kitchen <?= $activeTheme === 'dark' ? 'kitchen-dark-mode' : '' ?>"
    data-brand-id="<?= kitchen_e($brandId) ?>"
    data-branch-id="<?= kitchen_e($branchId) ?>"
    data-user-id="<?= kitchen_e($userId) ?>"
    data-theme="<?= kitchen_e($activeTheme) ?>">

    <div class="kitchen-toast-container" id="kitchenToastContainer" aria-live="polite"></div>

    <div class="kitchen-loading-overlay" id="kitchenLoadingOverlay" aria-hidden="true">
        <div class="kitchen-loading-spinner" aria-hidden="true"></div>
    </div>

    <div class="kitchen-main-shell" id="kitchenMainShell">

        <aside class="kitchen-sidebar" id="kitchenSidebar">

            <button type="button" class="kitchen-hotel-card" data-open-modal="kitchenBranchInfoModal">
                <div class="kitchen-hotel-logo-wrap">
                    <img src="<?= $brandLogo ?>" alt="<?= kitchen_e($brandName) ?>" onerror="this.style.opacity='0'">
                </div>
                <div class="kitchen-hotel-profile-info">
                    <h2><?= kitchen_e($brandName) ?></h2>
                    <p><?= kitchen_e($branchName) ?><?= $branchCity !== '' ? ' · ' . kitchen_e($branchCity) : '' ?></p>
                    <span class="kitchen-meta-pill status-pill-<?= kitchen_e(kitchen_badge_class($branchStatus)) ?>">
                        <?= kitchen_e(ucfirst($branchStatus)) ?>
                    </span>
                </div>
            </button>

            <nav class="kitchen-sidebar-nav" role="navigation" aria-label="Kitchen navigation">
                <button type="button" class="kitchen-nav-item active" data-section-target="overview">
                    <span class="kitchen-nav-icon"><i class="fa-solid fa-chart-line"></i></span>
                    <span class="kitchen-nav-label">Overview</span>
                </button>

                <button type="button" class="kitchen-nav-item" data-section-target="queue">
                    <span class="kitchen-nav-icon"><i class="fa-solid fa-fire-burner"></i></span>
                    <span class="kitchen-nav-label">Live Queue</span>
                    <span class="kitchen-nav-badge" id="kitchenQueueNavBadge" <?= $totalActiveCount > 0 ? '' : 'style="display:none;"' ?>>
                        <?= kitchen_e((string)$totalActiveCount) ?>
                    </span>
                </button>

                <button type="button" class="kitchen-nav-item" data-section-target="new">
                    <span class="kitchen-nav-icon"><i class="fa-solid fa-bell"></i></span>
                    <span class="kitchen-nav-label">New Orders</span>
                    <span class="kitchen-nav-badge" id="kitchenNewNavBadge" <?= $newOrdersCount > 0 ? '' : 'style="display:none;"' ?>>
                        <?= kitchen_e((string)$newOrdersCount) ?>
                    </span>
                </button>

                <button type="button" class="kitchen-nav-item" data-section-target="preparing">
                    <span class="kitchen-nav-icon"><i class="fa-solid fa-utensils"></i></span>
                    <span class="kitchen-nav-label">Preparing</span>
                    <span class="kitchen-nav-badge kitchen-nav-badge--prep" id="kitchenPreparingNavBadge" <?= $preparingCount > 0 ? '' : 'style="display:none;"' ?>>
                        <?= kitchen_e((string)$preparingCount) ?>
                    </span>
                </button>

                <button type="button" class="kitchen-nav-item" data-section-target="ready">
                    <span class="kitchen-nav-icon"><i class="fa-solid fa-circle-check"></i></span>
                    <span class="kitchen-nav-label">Ready</span>
                    <span class="kitchen-nav-badge kitchen-nav-badge--ready" id="kitchenReadyNavBadge" <?= $readyCount > 0 ? '' : 'style="display:none;"' ?>>
                        <?= kitchen_e((string)$readyCount) ?>
                    </span>
                </button>

                <button type="button" class="kitchen-nav-item" data-section-target="stations">
                    <span class="kitchen-nav-icon"><i class="fa-solid fa-layer-group"></i></span>
                    <span class="kitchen-nav-label">Stations</span>
                </button>

                <button type="button" class="kitchen-nav-item" data-section-target="settings">
                    <span class="kitchen-nav-icon"><i class="fa-solid fa-sliders"></i></span>
                    <span class="kitchen-nav-label">Settings</span>
                </button>
            </nav>

            <button
                type="button"
                class="kitchen-profile-card-btn"
                id="kitchenProfileBtn"
                data-open-modal="kitchenUserProfileModal"
                aria-haspopup="dialog"
                aria-controls="kitchenUserProfileModal">
                <div class="kitchen-sidebar-avatar">
                    <img
                        src="<?= $chefProfilePic ?>"
                        alt="<?= kitchen_e($chefName) ?>"
                        id="sidebarKitchenAvatar"
                        onerror="this.style.display='none';document.getElementById('sidebarKitchenAvatarFallback').style.display='grid';">
                    <span class="kitchen-avatar-fallback" id="sidebarKitchenAvatarFallback" style="display:none;">
                        <?= kitchen_e(strtoupper(substr($chefName, 0, 1))) ?>
                    </span>
                </div>
                <div class="kitchen-profile-info">
                    <h3><?= kitchen_e($chefName) ?></h3>
                    <p><?= kitchen_e(ucfirst($chefRole)) ?></p>
                    <span class="kitchen-shift-info"><?= kitchen_e($shiftName) ?></span>
                </div>
                <span class="kitchen-status-dot <?= strtolower($chefStatus) === 'active' ? 'is-active' : 'is-inactive' ?>" id="kitchenSidebarStatusDot"></span>
                <span class="kitchen-profile-arrow"><i class="fa-solid fa-chevron-right"></i></span>
            </button>

            <button class="kitchen-sidebar-toggle" id="kitchenSidebarToggle" type="button" title="Toggle sidebar" aria-label="Toggle sidebar">
                <span class="kitchen-sidebar-toggle-lines" aria-hidden="true">
                    <span></span><span></span><span></span>
                </span>
            </button>
        </aside>

        <div class="kitchen-sidebar-overlay" id="kitchenSidebarOverlay" aria-hidden="true"></div>

        <div class="kitchen-right-main">

            <header class="kitchen-topbar">
                <button class="kitchen-topbar-hamburger" id="kitchenMobileMenuBtn" type="button" aria-label="Open menu">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="kitchen-topbar-search">
                    <i class="fa-solid fa-magnifying-glass kitchen-search-icon"></i>
                    <input
                        type="search"
                        id="kitchenGlobalSearch"
                        placeholder="Search KOT, table, item, station..."
                        autocomplete="off">
                </div>

                <div class="kitchen-topbar-actions">
                    <button
                        type="button"
                        class="kitchen-topbar-btn <?= $soundAlertsEnabled ? 'is-active' : '' ?>"
                        id="kitchenSoundToggleBtn"
                        aria-label="Toggle sound alerts"
                        aria-pressed="<?= $soundAlertsEnabled ? 'true' : 'false' ?>">
                        <i class="fa-solid <?= $soundAlertsEnabled ? 'fa-volume-high' : 'fa-volume-xmark' ?>"></i>
                    </button>

                    <button type="button" class="kitchen-topbar-btn" id="kitchenRefreshBtn" aria-label="Refresh kitchen">
                        <i class="fa-solid fa-rotate"></i>
                    </button>

                    <button type="button" class="kitchen-topbar-btn" data-open-modal="kitchenCommandModal" aria-label="Open command center">
                        <i class="fa-solid fa-bolt"></i>
                    </button>

                    <div class="kitchen-topbar-time" id="kitchenLiveClock"><?= kitchen_e($currentTime) ?></div>
                </div>
            </header>

            <main class="kitchen-main-body">

                <section class="kitchen-main-section is-visible" data-kitchen-section="overview">
                    <div class="kitchen-section-head">
                        <div class="kitchen-section-head-left">
                            <span class="kitchen-section-kicker">Kitchen Command</span>
                            <h2>Live KOT Control</h2>
                        </div>
                        <div class="kitchen-section-actions">
                        </div>
                    </div>

                    <div class="kitchen-kpi-grid">
                        <button type="button" class="kitchen-kpi-card kitchen-kpi-card--interactive" data-section-target="new">
                            <span class="kitchen-kpi-label">New Orders</span>
                            <span class="kitchen-kpi-value" id="kitchenStatNew"><?= kitchen_e((string)$newOrdersCount) ?></span>
                            <small>Waiting for acceptance</small>
                        </button>

                        <button type="button" class="kitchen-kpi-card kitchen-kpi-card--interactive" data-section-target="queue">
                            <span class="kitchen-kpi-label">Accepted</span>
                            <span class="kitchen-kpi-value" id="kitchenStatAccepted"><?= kitchen_e((string)$acceptedCount) ?></span>
                            <small>Ready to start preparation</small>
                        </button>

                        <button type="button" class="kitchen-kpi-card kitchen-kpi-card--interactive" data-section-target="preparing">
                            <span class="kitchen-kpi-label">Preparing</span>
                            <span class="kitchen-kpi-value" id="kitchenStatPreparing"><?= kitchen_e((string)$preparingCount) ?></span>
                            <small>Currently in kitchen</small>
                        </button>

                        <button type="button" class="kitchen-kpi-card kitchen-kpi-card--interactive" data-section-target="ready">
                            <span class="kitchen-kpi-label">Ready</span>
                            <span class="kitchen-kpi-value" id="kitchenStatReady"><?= kitchen_e((string)$readyCount) ?></span>
                            <small>Waiting for pickup</small>
                        </button>

                        <button type="button" class="kitchen-kpi-card kitchen-kpi-card--interactive" data-section-target="queue">
                            <span class="kitchen-kpi-label">Priority</span>
                            <span class="kitchen-kpi-value" id="kitchenStatPriority"><?= kitchen_e((string)$urgentAttentionCount) ?></span>
                            <small>High / urgent KOTs</small>
                        </button>
                    </div>

                    <div class="kitchen-overview-grid">
                        <section class="kitchen-content-card">
                            <div class="kitchen-card-kicker-row">
                                <div>
                                    <span class="app-eyebrow">Selected KOT</span>
                                    <h3 class="app-title">Production Workbench</h3>
                                </div>
                            </div>
                            <div id="kitchenDetailPanel">
                                <?php if ($selectedOrder): ?>
                                    <div class="kitchen-detail-shell">
                                        <div class="kitchen-detail-top">
                                            <div>
                                                <span class="app-eyebrow">Selected KOT</span>
                                                <h3 class="kitchen-detail-title">
                                                    <?= kitchen_e((string)($selectedOrder['kot'] ?? $selectedOrder['order_number'] ?? '#ORDER')) ?>
                                                    <span class="kitchen-detail-table">
                                                        - Table <?= kitchen_e((string)($selectedOrder['table'] ?? $selectedOrder['table_code'] ?? '-')) ?>
                                                    </span>
                                                </h3>
                                            </div>

                                            <div class="kitchen-detail-status-wrap">
                                                <span class="kitchen-detail-status-pill <?= kitchen_e($selectedStatus) ?>">
                                                    <?= kitchen_e((string)($selectedOrder['status_label'] ?? kitchen_status_label($selectedStatus))) ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="kitchen-detail-meta-grid">
                                            <div class="kitchen-detail-meta-card">
                                                <span>Items</span>
                                                <strong><?= (int)($selectedOrder['items_count'] ?? count((array)($selectedOrder['items'] ?? []))) ?></strong>
                                            </div>
                                            <div class="kitchen-detail-meta-card">
                                                <span>Priority</span>
                                                <strong><?= kitchen_e(ucfirst(kitchen_priority_class((string)($selectedOrder['priority'] ?? 'normal')))) ?></strong>
                                            </div>
                                            <div class="kitchen-detail-meta-card">
                                                <span>Elapsed</span>
                                                <strong><?= kitchen_e((string)($selectedOrder['elapsed'] ?? kitchen_time_ago($selectedOrder['created_at'] ?? null))) ?></strong>
                                            </div>
                                            <div class="kitchen-detail-meta-card">
                                                <span>Total</span>
                                                <strong><?= kitchen_e(kitchen_money($selectedOrder['grand_total'] ?? 0)) ?></strong>
                                            </div>
                                        </div>

                                        <div class="kitchen-flow-line">
                                            <span class="kitchen-flow-label"><?= kitchen_e((string)($selectedOrder['status_label'] ?? kitchen_status_label($selectedStatus))) ?></span>
                                            <span><?= !empty($selectedOrder['waiter_called']) ? 'Waiter notified' : 'Next action is shown below' ?></span>
                                        </div>

                                        <div class="kitchen-detail-items-panel">
                                            <div class="kitchen-detail-panel-head">
                                                <h4>Order Items</h4>
                                                <span><?= (int)($selectedOrder['items_count'] ?? count((array)($selectedOrder['items'] ?? []))) ?> items</span>
                                            </div>

                                            <div class="kitchen-detail-items-list">
                                                <?php foreach ((is_array($selectedOrder['items'] ?? null) ? $selectedOrder['items'] : []) as $item): ?>
                                                    <?php
                                                    $itemId = (string)($item['order_item_id'] ?? $item['id'] ?? '');
                                                    $itemStatus = kitchen_safe_status((string)($item['status'] ?? 'new'));
                                                    ?>
                                                    <div class="kitchen-detail-item-card" data-order-item-id="<?= kitchen_e($itemId) ?>">
                                                        <div class="kitchen-detail-item-main">
                                                            <div class="kitchen-detail-item-title-row">
                                                                <span class="kitchen-detail-item-qty"><?= (int)($item['qty'] ?? $item['quantity'] ?? 0) ?>x</span>
                                                                <strong><?= kitchen_e((string)($item['name'] ?? $item['item_name'] ?? 'Item')) ?></strong>
                                                            </div>
                                                            <div class="kitchen-detail-item-sub">
                                                                <?= kitchen_e((string)($item['station'] ?? $item['station_name'] ?? 'Kitchen')) ?>
                                                                -
                                                                <?= kitchen_e((string)($item['status_label'] ?? kitchen_status_label($itemStatus))) ?>
                                                            </div>
                                                        </div>

                                                        <?php if (!empty($item['note'])): ?>
                                                            <div class="kitchen-detail-item-note">
                                                                <i class="fa-solid fa-note-sticky" aria-hidden="true"></i>
                                                                <?= kitchen_e((string)$item['note']) ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php
                                                        $itemNextStatus = '';
                                                        $itemNextLabel = '';
                                                        switch ($itemStatus) {
                                                            case 'new':
                                                                $itemNextStatus = 'accepted';
                                                                $itemNextLabel = 'Accept';
                                                                break;
                                                            case 'accepted':
                                                                $itemNextStatus = 'preparing';
                                                                $itemNextLabel = 'Start Preparing';
                                                                break;
                                                            case 'preparing':
                                                                $itemNextStatus = 'ready';
                                                                $itemNextLabel = 'Mark Ready';
                                                                break;
                                                        }
                                                        ?>
                                                        <?php if ($itemId !== '' && $itemNextStatus !== ''): ?>
                                                            <div class="kitchen-item-action-row">
                                                                <button type="button" class="kitchen-mini-btn kitchen-next-action-btn <?= $itemNextStatus === 'ready' ? 'kitchen-mini-btn--success' : '' ?> js-update-item-status" data-order-id="<?= kitchen_e($selectedOrderId) ?>" data-order-item-id="<?= kitchen_e($itemId) ?>" data-status="<?= kitchen_e($itemNextStatus) ?>"><?= kitchen_e($itemNextLabel) ?></button>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="kitchen-action-workbench" id="kitchenActionWorkbench">
                                            <?php if ($selectedStatus === 'new'): ?>
                                                <button class="kitchen-workbench-btn primary js-accept-order" type="button" data-order-id="<?= kitchen_e($selectedOrderId) ?>">
                                                    <i class="fa-solid fa-clipboard-check"></i>
                                                    <span>Accept Order</span>
                                                </button>
                                            <?php elseif ($selectedStatus === 'accepted'): ?>
                                                <button class="kitchen-workbench-btn primary js-start-preparing" type="button" data-order-id="<?= kitchen_e($selectedOrderId) ?>">
                                                    <i class="fa-solid fa-play"></i>
                                                    <span>Start Preparing</span>
                                                </button>
                                            <?php elseif ($selectedStatus === 'preparing'): ?>
                                                <button class="kitchen-workbench-btn success js-mark-ready" type="button" data-order-id="<?= kitchen_e($selectedOrderId) ?>">
                                                    <i class="fa-solid fa-check"></i>
                                                    <span>Mark Ready</span>
                                                </button>
                                            <?php elseif ($selectedStatus === 'ready'): ?>
                                                <button class="kitchen-workbench-btn info js-call-waiter" type="button" data-order-id="<?= kitchen_e($selectedOrderId) ?>">
                                                    <i class="fa-solid fa-bell-concierge"></i>
                                                    <span>Call Waiter</span>
                                                </button>
                                            <?php endif; ?>

                                            <button class="kitchen-workbench-btn ghost js-view-order-modal" type="button" data-order-id="<?= kitchen_e($selectedOrderId) ?>">
                                                <i class="fa-solid fa-eye"></i>
                                                <span>Full Detail</span>
                                            </button>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="kitchen-empty-state">
                                        <i class="fa-solid fa-fire-burner"></i>
                                        <strong>No active KOT selected</strong>
                                        <p>Select an order from the live queue to manage item and group status.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </section>

                        <section class="kitchen-content-card">
                            <div class="kitchen-card-kicker-row">
                                <div>
                                    <span class="app-eyebrow">Stations</span>
                                    <h3 class="app-title">Station Load</h3>
                                </div>
                            </div>

                            <div class="kitchen-station-grid" id="kitchenStationGrid">
                                <?php if (!empty($stations)): ?>
                                    <?php foreach ($stations as $station): ?>
                                        <?php
                                        if (!is_array($station)) continue;
                                        $stationClass = strtolower((string)($station['class'] ?? 'low'));
                                        if (!in_array($stationClass, ['low', 'normal', 'busy', 'high'], true)) {
                                            $stationClass = 'low';
                                        }
                                        ?>
                                        <div class="kitchen-station-card <?= kitchen_e($stationClass) ?>">
                                            <div class="kitchen-station-name"><?= kitchen_e((string)($station['name'] ?? 'Station')) ?></div>
                                            <div class="kitchen-station-count"><?= (int)($station['count'] ?? 0) ?></div>
                                            <div class="kitchen-station-load"><?= kitchen_e((string)($station['load'] ?? 'Low')) ?> Load</div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="kitchen-empty-inline">
                                        <div class="kitchen-empty-inline-title">No kitchen stations configured.</div>
                                        <div class="kitchen-empty-inline-text">Station load appears here once DB data is available.</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </section>
                    </div>
                </section>

                <section class="kitchen-main-section" data-kitchen-section="queue">
                    <div class="kitchen-section-head">
                        <div class="kitchen-section-head-left">
                            <span class="kitchen-section-kicker">Live Board</span>
                            <h2>All Kitchen Orders</h2>
                        </div>
                        <div class="kitchen-filter-pills">
                            <button type="button" class="kitchen-filter-btn active" data-filter="all">All</button>
                            <button type="button" class="kitchen-filter-btn" data-filter="new">New</button>
                            <button type="button" class="kitchen-filter-btn" data-filter="accepted">Accepted</button>
                            <button type="button" class="kitchen-filter-btn" data-filter="preparing">Preparing</button>
                            <button type="button" class="kitchen-filter-btn" data-filter="ready">Ready</button>
                        </div>
                    </div>

                    <div class="kitchen-board-grid" id="kitchenBoardGrid">
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <?php
                                if (!is_array($order)) continue;
                                $orderId = (string)($order['order_id'] ?? $order['id'] ?? '');
                                $status = kitchen_safe_status((string)($order['status'] ?? 'new'));
                                $priority = kitchen_priority_class((string)($order['priority'] ?? 'normal'));
                                $items = is_array($order['items'] ?? null) ? $order['items'] : [];
                                ?>
                                <article
                                    class="kitchen-order-card premium-kitchen-card <?= kitchen_e(kitchen_status_class($status)) ?> <?= $selectedOrderId !== '' && $selectedOrderId === $orderId ? 'is-selected' : '' ?>"
                                    data-status="<?= kitchen_e($status) ?>"
                                    data-order-id="<?= kitchen_e($orderId) ?>">
                                    <div class="kitchen-priority-rail <?= kitchen_e($priority) ?>"></div>

                                    <div class="kitchen-order-head">
                                        <div class="kitchen-order-head-left">
                                            <div class="kitchen-kot"><?= kitchen_e((string)($order['kot'] ?? $order['order_number'] ?? '#ORDER')) ?></div>
                                            <div class="kitchen-table-ref">Table <?= kitchen_e((string)($order['table'] ?? $order['table_code'] ?? '-')) ?></div>
                                        </div>

                                        <div class="kitchen-order-head-right">
                                            <span class="kitchen-priority-pill <?= kitchen_e($priority) ?>"><?= kitchen_e(ucfirst($priority)) ?></span>
                                            <span class="kitchen-time-pill"><?= kitchen_e((string)($order['elapsed'] ?? kitchen_time_ago($order['created_at'] ?? null))) ?></span>
                                        </div>
                                    </div>

                                    <div class="kitchen-order-body">
                                        <div class="kitchen-order-meta">
                                            <span class="kitchen-stage-label"><?= kitchen_e((string)($order['status_label'] ?? kitchen_status_label($status))) ?></span>
                                            <span class="kitchen-items-count"><?= (int)($order['items_count'] ?? count($items)) ?> items</span>
                                        </div>

                                        <div class="kitchen-items-list">
                                            <?php foreach ($items as $item): ?>
                                                <?php if (!is_array($item)) continue; ?>
                                                <div class="kitchen-item-row">
                                                    <div class="kitchen-item-main">
                                                        <span class="kitchen-item-qty"><?= (int)($item['qty'] ?? $item['quantity'] ?? 0) ?>x</span>
                                                        <span class="kitchen-item-name"><?= kitchen_e((string)($item['name'] ?? $item['item_name'] ?? 'Item')) ?></span>
                                                    </div>
                                                    <?php if (!empty($item['note'])): ?>
                                                        <div class="kitchen-item-note"><?= kitchen_e((string)$item['note']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <div class="kitchen-order-mini-actions">
                                            <button class="kitchen-ghost-mini-btn js-select-order" type="button" data-order-id="<?= kitchen_e($orderId) ?>">
                                                <i class="fa-solid fa-arrow-pointer"></i>
                                                <span>Select</span>
                                            </button>
                                            <button class="kitchen-ghost-mini-btn js-view-order-modal" type="button" data-order-id="<?= kitchen_e($orderId) ?>">
                                                <i class="fa-solid fa-eye"></i>
                                                <span>Open</span>
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="kitchen-empty-card">
                                <i class="fa-solid fa-circle-check kitchen-empty-icon"></i>
                                <strong>No kitchen orders</strong>
                                <p>New KOTs from customer/table pads will appear here in real time.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="kitchen-main-section" data-kitchen-section="new">
                    <div class="kitchen-section-head">
                        <div class="kitchen-section-head-left">
                            <span class="kitchen-section-kicker">Action Required</span>
                            <h2>New Orders</h2>
                        </div>
                    </div>
                    <div class="kitchen-section-grid" id="kitchenNewOrdersGrid" data-section-list="new"></div>
                </section>

                <section class="kitchen-main-section" data-kitchen-section="preparing">
                    <div class="kitchen-section-head">
                        <div class="kitchen-section-head-left">
                            <span class="kitchen-section-kicker">Cooking Flow</span>
                            <h2>Preparing Orders</h2>
                        </div>
                    </div>
                    <div class="kitchen-section-grid" id="kitchenPreparingOrdersGrid" data-section-list="preparing"></div>
                </section>

                <section class="kitchen-main-section" data-kitchen-section="ready">
                    <div class="kitchen-section-head">
                        <div class="kitchen-section-head-left">
                            <span class="kitchen-section-kicker">Pickup Counter</span>
                            <h2>Ready Orders</h2>
                        </div>
                    </div>
                    <div class="kitchen-section-grid" id="kitchenReadyOrdersGrid" data-section-list="ready"></div>
                </section>

                <section class="kitchen-main-section" data-kitchen-section="stations">
                    <div class="kitchen-section-head">
                        <div class="kitchen-section-head-left">
                            <span class="kitchen-section-kicker">Station Control</span>
                            <h2>Kitchen Stations</h2>
                        </div>
                    </div>
                    <div class="kitchen-station-grid kitchen-station-grid--page" id="kitchenStationPageGrid"></div>
                </section>

                <section class="kitchen-main-section" data-kitchen-section="settings">
                    <div class="kitchen-section-head">
                        <div class="kitchen-section-head-left">
                            <span class="kitchen-section-kicker">Preferences</span>
                            <h2>Kitchen Settings</h2>
                        </div>
                    </div>

                    <div class="kitchen-settings-panel">
                        <section class="kitchen-settings-block">
                            <div class="kitchen-settings-block-head">
                                <h4><i class="fa-solid fa-palette"></i> Appearance</h4>
                            </div>
                            <div class="kitchen-setting-row">
                                <div class="kitchen-setting-info">
                                    <strong>Theme</strong>
                                    <p>Switch between light and dark production view.</p>
                                </div>
                                <div class="kitchen-segmented-ctrl">
                                    <button type="button" class="kitchen-seg-btn <?= $activeTheme === 'light' ? 'active' : '' ?>" data-theme-mode="light">Light</button>
                                    <button type="button" class="kitchen-seg-btn <?= $activeTheme === 'dark' ? 'active' : '' ?>" data-theme-mode="dark">Dark</button>
                                </div>
                            </div>
                        </section>

                        <section class="kitchen-settings-block">
                            <div class="kitchen-settings-block-head">
                                <h4><i class="fa-solid fa-volume-high"></i> Alerts</h4>
                            </div>
                            <div class="kitchen-setting-row">
                                <div class="kitchen-setting-info">
                                    <strong>Sound Alerts</strong>
                                    <p>Play the selected DB-driven alert sound when new KOTs arrive.</p>
                                </div>
                                <label class="kitchen-switch">
                                    <input type="checkbox" id="kitchenSettingsSoundToggle" <?= $soundAlertsEnabled ? 'checked' : '' ?>>
                                    <span class="kitchen-switch-slider"></span>
                                </label>
                            </div>

                            <div class="kitchen-setting-row">
                                <div class="kitchen-setting-info">
                                    <strong>Notification Sound</strong>
                                    <p>Choose the kitchen alert tone saved in user settings.</p>
                                </div>
                                <div class="kitchen-setting-control">
                                    <select id="notificationSoundSelect" class="kitchen-select" aria-label="Kitchen notification sound selector">
                                        <option value="<?= $kitchenSound1 ?>" <?= $selectedNotificationSound === $kitchenSound1 ? 'selected' : '' ?>>Alert1</option>
                                        <option value="<?= $kitchenSound2 ?>" <?= $selectedNotificationSound === $kitchenSound2 ? 'selected' : '' ?>>Alert2</option>
                                        <option value="<?= $kitchenSound3 ?>" <?= $selectedNotificationSound === $kitchenSound3 ? 'selected' : '' ?>>Alert3</option>
                                    </select>
                                </div>
                            </div>
                        </section>


                    </div>
                </section>

            </main>
        </div>
    </div>

    <div class="app-modal" id="kitchenOrderModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="kitchenOrderModalTitle">
        <div class="modal-overlay app-modal-overlay" data-close="modal"></div>
        <div class="modal-dialog app-modal-content modal-dialog--lg premium-modal-lg">
            <div class="modal-header app-modal-header">
                <div>
                    <span class="app-eyebrow">KOT Detail</span>
                    <h3 id="kitchenOrderModalTitle">KOT Detail</h3>
                </div>
                <button class="modal-close" type="button" data-close="modal" aria-label="Close modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body app-modal-body" id="kitchenOrderModalBody">
                <div class="kitchen-modal-loading">
                    <div class="kitchen-spinner small"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="app-modal" id="kitchenBranchInfoModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="kitchenBranchInfoTitle">
        <div class="modal-overlay app-modal-overlay" data-close="modal"></div>
        <div class="modal-dialog app-modal-content modal-dialog--profile">
            <div class="modal-header app-modal-header">
                <div>
                    <span class="app-eyebrow">Branch</span>
                    <h3 id="kitchenBranchInfoTitle"><?= kitchen_e($branchName) ?></h3>
                </div>
                <button class="modal-close" type="button" data-close="modal" aria-label="Close modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body app-modal-body">
                <div class="kitchen-profile-hero">
                    <div class="kitchen-profile-avatar-ring">
                        <img src="<?= $brandLogo ?>" alt="<?= kitchen_e($brandName) ?>">
                    </div>
                    <div class="kitchen-profile-hero-info">
                        <h2><?= kitchen_e($brandName) ?></h2>
                        <p><?= kitchen_e($branchDesc !== '' ? $branchDesc : $branchName) ?></p>
                        <div class="kitchen-profile-hero-pills">
                            <span class="kitchen-meta-pill"><?= kitchen_e($branchName) ?></span>
                            <?php if ($branchCity !== ''): ?>
                                <span class="kitchen-meta-pill"><?= kitchen_e($branchCity) ?></span>
                            <?php endif; ?>
                            <span class="kitchen-meta-pill is-success"><?= kitchen_e(ucfirst($branchStatus)) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="app-modal" id="kitchenUserProfileModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="kitchenUserProfileTitle">
        <div class="modal-overlay app-modal-overlay" data-close="modal"></div>
        <div class="modal-dialog app-modal-content modal-dialog--profile">
            <div class="modal-header app-modal-header">
                <div>
                    <span class="app-eyebrow">Kitchen Staff</span>
                    <h3 id="kitchenUserProfileTitle">Profile</h3>
                </div>
                <button class="modal-close" type="button" data-close="modal" aria-label="Close modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body app-modal-body">
                <div class="kitchen-profile-hero">
                    <div class="kitchen-profile-avatar-ring">
                        <img src="<?= $chefProfilePic ?>" alt="<?= kitchen_e($chefName) ?>">
                    </div>
                    <div class="kitchen-profile-hero-info">
                        <h2><?= kitchen_e($chefName) ?></h2>
                        <p><?= kitchen_e(ucfirst($chefRole)) ?> · <?= kitchen_e($shiftName) ?></p>
                        <div class="kitchen-profile-hero-pills">
                            <span class="kitchen-meta-pill <?= strtolower($chefStatus) === 'active' ? 'is-success' : 'is-danger' ?>">
                                <?= kitchen_e(ucfirst($chefStatus)) ?>
                            </span>
                            <?php if ($chefCode !== ''): ?>
                                <span class="kitchen-meta-pill"><?= kitchen_e($chefCode) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="kitchen-profile-grid">
                    <div class="kitchen-profile-card">
                        <h3>Contact</h3>
                        <div class="kitchen-info-list">
                            <div class="kitchen-info-row"><span>Email</span><strong><?= kitchen_e($chefEmail !== '' ? $chefEmail : '—') ?></strong></div>
                            <div class="kitchen-info-row"><span>Phone</span><strong><?= kitchen_e($chefPhone !== '' ? $chefPhone : '—') ?></strong></div>
                        </div>
                    </div>
                    <div class="kitchen-profile-card">
                        <h3>Shift</h3>
                        <div class="kitchen-info-list">
                            <div class="kitchen-info-row"><span>Kitchen</span><strong><?= kitchen_e($kitchenName) ?></strong></div>
                            <div class="kitchen-info-row"><span>Shift</span><strong><?= kitchen_e($shiftName) ?></strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="app-modal" id="kitchenCommandModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="kitchenCommandModalTitle">
        <div class="modal-overlay app-modal-overlay" data-close="modal"></div>
        <div class="modal-dialog app-modal-content">
            <div class="modal-header app-modal-header">
                <div>
                    <span class="app-eyebrow">Quick Actions</span>
                    <h3 id="kitchenCommandModalTitle">Kitchen Command Center</h3>
                </div>
                <button class="modal-close" type="button" data-close="modal" aria-label="Close modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body app-modal-body">
                <div class="kitchen-command-grid">
                    <button type="button" class="kitchen-command-btn" data-section-target="new">
                        <i class="fa-solid fa-bell"></i>
                        <span>Review New Orders</span>
                    </button>
                    <button type="button" class="kitchen-command-btn" data-section-target="preparing">
                        <i class="fa-solid fa-utensils"></i>
                        <span>Preparing Queue</span>
                    </button>
                    <button type="button" class="kitchen-command-btn" data-section-target="ready">
                        <i class="fa-solid fa-bell-concierge"></i>
                        <span>Ready Pickup</span>
                    </button>
                    <button type="button" class="kitchen-command-btn" id="kitchenCommandRefreshBtn">
                        <i class="fa-solid fa-rotate"></i>
                        <span>Refresh Board</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <audio id="kitchenAlertAudio" preload="auto" aria-hidden="true">
        <source src="<?= $selectedNotificationSound ?>" type="audio/mpeg">
    </audio>

    <script>
        window.KitchenPageData = {
            apiBase: <?= json_encode($apiBase, JSON_UNESCAPED_SLASHES) ?>,
            brandId: <?= json_encode($brandId, JSON_UNESCAPED_SLASHES) ?>,
            branchId: <?= json_encode($branchId, JSON_UNESCAPED_SLASHES) ?>,
            userId: <?= json_encode($userId, JSON_UNESCAPED_SLASHES) ?>,
            selectedOrderId: <?= json_encode($selectedOrderId, JSON_UNESCAPED_SLASHES) ?>,
            defaultNotificationSound: <?= json_encode($defaultNotificationSoundUrl, JSON_UNESCAPED_SLASHES) ?>,
            userSettings: <?= json_encode($userSettings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
            initialSnapshot: <?= json_encode($initialSnapshot, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
            context: <?= json_encode([
                            'brand_name' => $brandName,
                            'branch_name' => $branchName,
                            'kitchen_name' => $kitchenName,
                            'shift_name' => $shiftName,
                            'chef_name' => $chefName,
                            'role' => $chefRole,
                        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
        };
    </script>

    <script src="<?= kitchen_e(rs_base_url('RestaurantAssets/js/kitchen.js')) ?>"></script>
</body>

</html>
