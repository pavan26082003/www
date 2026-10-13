<?php

declare(strict_types=1);

/**
 * Restaurant System — Manager Dashboard View
 * Full-scale SaaS: profile update, change password, image uploads, notifications, all modals.
 */

if (!function_exists('manager_html')) {
    function manager_html($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('manager_json')) {
    function manager_json($value): string
    {
        $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $json === false ? '{}' : $json;
    }
}

if (!function_exists('manager_base_url_safe')) {
    function manager_base_url_safe(string $path = ''): string
    {
        $path = ltrim($path, '/');
        if (function_exists('base_url')) {
            return rtrim((string) base_url($path), '/');
        }
        $root = rtrim((string) ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'), '/');
        return $root . '/' . $path;
    }
}

if (!function_exists('manager_field')) {
    function manager_field($row, array $keys, $fallback = '')
    {
        if (!is_array($row)) return $fallback;
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
                return $row[$key];
            }
        }
        return $fallback;
    }
}

if (!function_exists('manager_badge_class')) {
    function manager_badge_class(string $status): string
    {
        $status = strtolower(trim(str_replace([' ', '-'], '_', $status)));

        if (in_array($status, ['active', 'available', 'paid', 'ready', 'resolved', 'accepted', 'approved', 'served', 'completed', 'in_stock', 'open'], true)) {
            return 'success';
        }

        if (in_array($status, ['pending', 'new', 'preparing', 'processing', 'in_processing', 'billing', 'unpaid', 'waiting', 'low_stock', 'in_progress', 'occupied', 'reserved'], true)) {
            return 'warning';
        }

        if (in_array($status, ['cancelled', 'rejected', 'failed', 'inactive', 'out_of_stock', 'maintenance', 'closed'], true)) {
            return 'danger';
        }

        return 'neutral';
    }
}

if (!function_exists('manager_asset_url')) {
    function manager_asset_url(string $path = '', string $fallback = ''): string
    {
        $path = trim($path);
        if ($path === '') $path = $fallback;
        if ($path === '') return '';
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0 || strpos($path, '//') === 0 || strpos($path, 'data:') === 0) return $path;
        return manager_base_url_safe('RestaurantAssets/' . ltrim($path, '/'));
    }
}

$pageSnapshot = is_array($snapshot ?? null) ? $snapshot : [];
$pageContext  = is_array($pageSnapshot['context'] ?? null) ? $pageSnapshot['context'] : [];
$pageSummary  = is_array($pageSnapshot['summary'] ?? null) ? $pageSnapshot['summary'] : [];

$brandId  = (string) ($brandId ?? '');
$branchId = (string) ($branchId ?? '');
$userId   = (string) ($userId ?? '');

$brandName    = (string) ($pageContext['brand_name'] ?? ($brand['name'] ?? 'Restaurant'));
$branchName   = (string) ($pageContext['branch_name'] ?? ($branch['name'] ?? 'Main Branch'));
$branchCode   = (string) ($pageContext['branch_code'] ?? ($branch['code'] ?? ''));
$branchCity   = (string) ($pageContext['branch_city'] ?? ($branch['city'] ?? ''));
$branchState  = (string) ($pageContext['branch_state'] ?? ($branch['state'] ?? ''));
$branchPhone  = (string) ($pageContext['branch_phone'] ?? ($branch['phone'] ?? ''));
$branchEmail  = (string) ($pageContext['branch_email'] ?? ($branch['email'] ?? ''));
$branchAddress = (string) ($pageContext['branch_address'] ?? ($branch['address'] ?? ''));
$branchPincode = (string) ($pageContext['branch_pincode'] ?? ($branch['pincode'] ?? ''));
$branchOpens  = (string) ($pageContext['branch_opens_at'] ?? ($branch['opens_at'] ?? ''));
$branchCloses = (string) ($pageContext['branch_closes_at'] ?? ($branch['closes_at'] ?? ''));
$branchTaxNo  = (string) ($pageContext['tax_number'] ?? ($branch['tax_number'] ?? ''));
$branchStatus = (string) ($pageContext['branch_status'] ?? ($branch['status'] ?? 'active'));

$brandLogo    = manager_asset_url((string) ($pageContext['brand_logo'] ?? ''), 'default/default-logo.svg');
$brandFavicon = manager_asset_url((string) ($pageContext['brand_favicon'] ?? ''), 'default/default-logo.svg');

$managerName         = (string) ($pageContext['manager_name'] ?? 'Manager');
$managerRole         = (string) ($pageContext['manager_role'] ?? 'manager');
$managerEmail        = (string) ($pageContext['manager_email'] ?? '');
$managerPhone        = (string) ($pageContext['manager_phone'] ?? '');
$managerEmployeeCode = (string) ($pageContext['manager_employee_code'] ?? '');
$managerAadharNo     = (string) ($pageContext['manager_aadhar_no'] ?? '');
$managerUserId       = (string) ($pageContext['manager_user_id'] ?? $userId);
$managerProfilePic   = manager_asset_url((string) ($pageContext['manager_profile_pic'] ?? ''), 'default/default-avatar.svg');

$currency = (string) ($pageContext['currency'] ?? 'INR');

$allOrders         = is_array($pageSnapshot['all_orders'] ?? null) ? $pageSnapshot['all_orders'] : [];
$liveOrders        = is_array($pageSnapshot['live_orders'] ?? null) ? $pageSnapshot['live_orders'] : [];
$allBills          = is_array($pageSnapshot['all_bills'] ?? null) ? $pageSnapshot['all_bills'] : [];
$openBills         = is_array($pageSnapshot['open_bills'] ?? null) ? $pageSnapshot['open_bills'] : [];
$staff             = is_array($pageSnapshot['staff'] ?? null) ? $pageSnapshot['staff'] : [];
$menuItems         = is_array($pageSnapshot['menu_items'] ?? null) ? $pageSnapshot['menu_items'] : [];
$offerBanners      = is_array($pageSnapshot['offer_banners'] ?? null) ? $pageSnapshot['offer_banners'] : [];
$tables            = is_array($pageSnapshot['tables'] ?? null) ? $pageSnapshot['tables'] : [];
$requestItems      = is_array($pageSnapshot['service_requests'] ?? null) ? $pageSnapshot['service_requests'] : [];
$notificationItems = is_array($pageSnapshot['notifications'] ?? null) ? $pageSnapshot['notifications'] : [];
$recentActivity    = is_array($pageSnapshot['recent_activity'] ?? null) ? $pageSnapshot['recent_activity'] : [];
$topItems          = is_array($pageSnapshot['top_items'] ?? null) ? $pageSnapshot['top_items'] : [];
$revenueChart      = is_array($pageSnapshot['revenue_chart'] ?? null) ? $pageSnapshot['revenue_chart'] : [];

$newOrders = array_filter($liveOrders, static function ($order) {
    // Count only orders that are new/pending for the live orders badge
    $status = strtolower((string) manager_field($order, ['status'], ''));
    return in_array($status, ['new', 'pending', 'open'], true);
});
$liveOrdersCount  = count($newOrders);

// Only count requests that are new/pending for the requests badge
$newRequests = array_filter($requestItems, static function ($req) {
    $status = strtolower((string) manager_field($req, ['status'], ''));
    return in_array($status, ['new', 'pending'], true);
});
$pendingRequests  = (int) ($pageSummary['pending_requests'] ?? count($newRequests));

// Only count bills that are unpaid/partial/new for the bills badge
$unpaidBills = array_filter($openBills, static function ($bill) {
    $paymentStatus = strtolower(trim(str_replace([' ', '-'], '_', (string)manager_field($bill, ['payment_status'], 'unpaid'))));
    return in_array($paymentStatus, ['unpaid', 'in_processing'], true);
});

$openBillsCount   = count($unpaidBills);
$kitchenQueue     = (int) ($pageSummary['kitchen_queue'] ?? 0);
$todayRevenue     = (float) ($pageSummary['today_revenue'] ?? 0);
$todayOrders      = (int) ($pageSummary['today_orders'] ?? 0);
$activeMenuItems  = (int) ($pageSummary['active_menu_items'] ?? count($menuItems));
$activeTables     = (int) ($pageSummary['active_tables'] ?? count($tables));
$staffCount       = (int) ($pageSummary['staff_count'] ?? count($staff));
$unreadNotifs     = (int) ($pageSummary['unread_notifications'] ?? count(array_filter($notificationItems, static fn($item) => (int)(is_array($item) ? ($item['is_read'] ?? 0) : 0) !== 1)));

$apiBase = function_exists('site_url')
    ? (string) site_url('restaurant-system/manager/api')
    : manager_base_url_safe('restaurant-system/manager/api');

$menuCategories = is_array($pageSnapshot['menu_categories'] ?? null) ? $pageSnapshot['menu_categories'] : [];

$managerPageData = [
    'brandId'  => $brandId,
    'branchId' => $branchId,
    'userId'   => $userId,
    'currency' => $currency,
    'apiBase'  => $apiBase,
    'snapshot' => $pageSnapshot,
    'graph'    => $revenueChart,
    'context'  => $pageContext,
    'ui'       => [
        'searchConfig' => [
            'overview'         => ['placeholder' => 'Search overview by order id, bill id, table, item...'],
            'orders'           => ['placeholder' => 'Search orders by order id, table, customer, item...'],
            'requests'         => ['placeholder' => 'Search requests by type, table, note, status...'],
            'bills'            => ['placeholder' => 'Search bills by bill id, order id, table...'],
            'staff-management' => ['placeholder' => 'Search staff by name, code, role, aadhaar...'],
            'tables'           => ['placeholder' => 'Search tables by code, status, zone...'],
            'menus'            => ['placeholder' => 'Search menu items by item, category, type...'],
            'offers'           => ['placeholder' => 'Search offers by status, date, sort order...'],
            'activity'         => ['placeholder' => 'Search activity by role, event, action...'],
        ],
    ],
];

$currSymbol = $currency === 'INR' ? '₹' : $currency . ' ';
function fmt_money(float $v, string $sym): string
{
    return $sym . number_format($v, 2);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= manager_html($brandName) ?> • <?= manager_html($branchName) ?> • Manager Dashboard</title>
    <link rel="icon" href="<?= manager_html($brandFavicon) ?>">
    <link rel="stylesheet" href="<?= manager_html(manager_base_url_safe('RestaurantAssets/css/global.css')) ?>">
    <link rel="stylesheet" href="<?= manager_html(manager_base_url_safe('RestaurantAssets/css/manager.css')) ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


</head>

<body
    class="manager-page theme-owner"
    data-brand-id="<?= manager_html($brandId) ?>"
    data-branch-id="<?= manager_html($branchId) ?>"
    data-user-id="<?= manager_html($userId) ?>">

    <audio id="managerAlertAudio" preload="auto">
        <source src="<?= manager_html(manager_base_url_safe('RestaurantAssets/AlertSounds/Sound1.mpeg')) ?>" type="audio/mpeg">
    </audio>

    <!-- Toast Container -->
    <div class="manager-toast-container" id="managerToastContainer" aria-live="polite"></div>

    <section class="manager-layout-section">
        <div class="container">
            <div class="main manager-main-shell">

                <!-- ═══ SIDEBAR ═══ -->
                <aside class="sidebar manager-sidebar">
                    <button type="button" class="sidebar-card hotel-profile-card" data-open-modal="branchDetailsModal">
                        <div class="hotel-profile-top">
                            <div class="hotel-logo-wrap">
                                <img src="<?= manager_html($brandLogo) ?>" alt="<?= manager_html($brandName) ?>">
                            </div>
                            <div class="hotel-profile-info">
                                <h2><?= manager_html($brandName) ?></h2>
                                <p><?= manager_html($branchName) ?><?= $branchCity !== '' ? ' • ' . manager_html($branchCity) : '' ?></p>
                            </div>
                        </div>
                        <div class="hotel-profile-meta">
                            <span class="meta-pill status-pill-<?= manager_html(manager_badge_class($branchStatus)) ?>"><?= manager_html(ucfirst($branchStatus)) ?></span>
                            <?php if ($branchCode !== ''): ?>
                                <span class="meta-pill"><?= manager_html($branchCode) ?></span>
                            <?php endif; ?>
                        </div>
                    </button>

                    <nav class="sidebar-nav">
                        <button type="button" class="sidebar-nav-item active" data-section-target="overview">
                            <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span>
                            <span class="nav-label">Overview</span>
                        </button>
                        <button type="button" class="sidebar-nav-item" data-section-target="orders">
                            <span class="nav-icon"><i class="fa-solid fa-bowl-food"></i></span>
                            <span class="nav-label">Orders</span>
                            <span class="order-badge" id="order_notification" <?= $liveOrdersCount > 0 ? '' : 'style="display:none;"' ?>><?= manager_html((string)$liveOrdersCount) ?></span>
                        </button>
                        <button type="button" class="sidebar-nav-item" data-section-target="requests">
                            <span class="nav-icon"><i class="fa-solid fa-bell-concierge"></i></span>
                            <span class="nav-label">Requests</span>
                            <span class="request-badge" id="request_notification" <?= $pendingRequests > 0 ? '' : 'style="display:none;"' ?>><?= manager_html((string)$pendingRequests) ?></span>
                        </button>
                        <button type="button" class="sidebar-nav-item" data-section-target="bills">
                            <span class="nav-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                            <span class="nav-label">Bills</span>
                            <span class="bill-badge" id="bill_notification" <?= $openBillsCount > 0 ? '' : 'style="display:none;"' ?>><?= manager_html((string)$openBillsCount) ?></span>
                        </button>
                        <button type="button" class="sidebar-nav-item" data-section-target="staff-management">
                            <span class="nav-icon"><i class="fa-solid fa-user-group"></i></span>
                            <span class="nav-label">Staff</span>
                        </button>
                        <button type="button" class="sidebar-nav-item" data-section-target="tables">
                            <span class="nav-icon"><i class="fa-solid fa-table-cells-large"></i></span>
                            <span class="nav-label">Tables</span>
                        </button>
                        <button type="button" class="sidebar-nav-item" data-section-target="menus">
                            <span class="nav-icon"><i class="fa-solid fa-utensils"></i></span>
                            <span class="nav-label">Menu</span>
                        </button>
                        <button type="button" class="sidebar-nav-item" data-section-target="offers">
                            <span class="nav-icon"><i class="fa-solid fa-tags"></i></span>
                            <span class="nav-label">Offers & Discounts</span>
                        </button>
                        <button type="button" class="sidebar-nav-item" data-section-target="activity">
                            <span class="nav-icon"><i class="fa-solid fa-wave-square"></i></span>
                            <span class="nav-label">Activity</span>
                        </button>

                        <button type="button" class="sidebar-nav-item" data-section-target="settings">
                            <span class="nav-icon"><i class="fa-solid fa-sliders"></i></span>
                            <span class="nav-label">Settings</span>
                        </button>
                    </nav>

                    <button type="button" class="sidebar-card manager-profile-card" data-open-modal="managerProfileModal">
                        <div class="manager-avatar">
                            <img src="<?= manager_html($managerProfilePic) ?>" alt="<?= manager_html($managerName) ?>" id="sidebarManagerAvatar">
                        </div>
                        <div class="manager-profile-info">
                            <h3 id="sidebarManagerName"><?= manager_html($managerName) ?></h3>
                            <p><?= manager_html(ucfirst($managerRole)) ?><?= $managerEmployeeCode !== '' ? ' • ' . manager_html($managerEmployeeCode) : '' ?></p>
                        </div>
                        <span class="manager-profile-arrow"><i class="fa-solid fa-angle-right"></i></span>
                    </button>
                </aside>

                <!-- ═══ RIGHT CONTENT ═══ -->
                <div class="right-main-cont manager-right-main">
                    <div class="topbar-minimal manager-topbar">
                        <div class="topbar-left-search">
                            <div class="topbar-search-box">
                                <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="search" id="managerGlobalSearch" placeholder="Search overview..." autocomplete="off">
                            </div>
                        </div>
                        <div class="topbar-actions-wrap">
                            <button type="button" class="topbar-action-card manager-notification-btn" data-open-modal="notificationsModal" title="Notifications">
                                <i class="fa-solid fa-bell"></i>
                                <span class="action-badge manager-notification-btn__count" <?= $unreadNotifs > 0 ? '' : 'style="display:none;"' ?>><?= manager_html((string)$unreadNotifs) ?></span>
                            </button>
                            <button type="button" class="topbar-action-card" id="soundToggleBtn" aria-pressed="true" title="Toggle Sound">
                                <i class="fa-solid fa-volume-high"></i>
                            </button>
                            <button type="button" class="topbar-action-card manager-refresh-btn" id="refreshDashboardBtn" title="Refresh">
                                <i class="fa-solid fa-rotate"></i>
                            </button>
                        </div>
                        <div class="topbar-time-card manager-live-clock" id="liveTimeCard"><?= manager_html(date('h:i A')) ?></div>
                    </div>

                    <div class="main-body manager-main-body">

                        <!-- ═══ OVERVIEW ═══ -->
                        <section class="main-section is-visible" data-manager-section="overview">
                            <div class="section-head">
                                <div class="section-head-left">
                                    <span class="section-kicker">Overview</span>
                                    <h2>Branch Overview Dashboard</h2>
                                </div>
                            </div>
                            <div class="overview-kpi-grid">
                                <div class="kpi-card"><span class="kpi-label">Live Orders</span><strong class="kpi-value" id="kpiLiveOrders"><?= manager_html((string)$liveOrdersCount) ?></strong><small>Active orders now</small></div>
                                <div class="kpi-card"><span class="kpi-label">Requests</span><strong class="kpi-value" id="kpiRequests"><?= manager_html((string)$pendingRequests) ?></strong><small>Manager action queue</small></div>
                                <div class="kpi-card"><span class="kpi-label">Open Bills</span><strong class="kpi-value" id="kpiBills"><?= manager_html((string)$openBillsCount) ?></strong><small>Pending bill actions</small></div>
                                <div class="kpi-card"><span class="kpi-label">Kitchen Queue</span><strong class="kpi-value" id="kpiKitchen"><?= manager_html((string)$kitchenQueue) ?></strong><small>Current pipeline</small></div>
                            </div>
                            <div class="overview-main-grid">
                                <div class="manager-mini-graph-card">
                                    <div class="manager-mini-graph-head">
                                        <div>
                                            <span class="card-kicker">Analytics</span>
                                            <h3 class="manager-mini-graph-title" id="graphTitle">Revenue Trend</h3>
                                            <p class="manager-mini-graph-subtitle" id="graphSubtitle">Branch analytics for today.</p>
                                        </div>
                                        <div class="manager-mini-graph-controls">
                                            <!-- Reordered graph controls: metrics on the left and date ranges on the right -->
                                            <div class="manager-mini-metric-switch">
                                                <button type="button" class="manager-mini-chip active" data-metric="revenue">Revenue</button>
                                                <button type="button" class="manager-mini-chip" data-metric="orders">Orders</button>
                                                <button type="button" class="manager-mini-chip" data-metric="items">Items</button>
                                            </div>
                                            <div class="manager-mini-segmented">
                                                <button type="button" class="manager-mini-seg-btn active" data-range="today">Today</button>
                                                <button type="button" class="manager-mini-seg-btn" data-range="week">Week</button>
                                                <button type="button" class="manager-mini-seg-btn" data-range="month">Month</button>
                                                <button type="button" class="manager-mini-seg-btn" data-range="custom">Custom</button>
                                            </div>
                                            <div class="manager-mini-custom-range" id="customDateWrap">
                                                <div class="manager-mini-date-shell"><label for="graphRangeFromMini">From</label><input type="date" id="graphRangeFromMini"></div>
                                                <div class="manager-mini-date-shell"><label for="graphRangeToMini">To</label><input type="date" id="graphRangeToMini"></div>
                                                <button type="button" class="manager-mini-apply-btn" id="applyCustomBtn">Apply</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="manager-mini-meta-strip" id="metaStrip"></div>
                                    <div class="manager-chart-shell" id="chartShell">
                                        <canvas id="managerRevenueChart" height="220"></canvas>
                                        <div id="chartHover" style="position:absolute;top:14px;bottom:36px;width:1px;background:rgba(37,99,235,.18);left:0;opacity:0;pointer-events:none;"></div>
                                        <div id="chartTooltip" style="position:absolute;opacity:0;pointer-events:none;min-width:160px;padding:10px 12px;border-radius:14px;background:#fff;border:1px solid rgba(15,23,42,.08);box-shadow:0 18px 40px rgba(15,23,42,.12);">
                                            <div id="ttLabel" style="font-size:11px;font-weight:800;color:#0f172a;">-</div>
                                            <div id="ttValue" style="font-size:16px;font-weight:800;color:#2563eb;margin-top:4px;">-</div>
                                            <div id="ttSub" style="font-size:11px;color:#64748b;margin-top:4px;">-</div>
                                        </div>
                                    </div>
                                    <div style="padding-top:10px;"><small id="footerNote" class="text-muted">Hover the chart to explore data points.</small></div>
                                </div>

                            </div>
                            <div class="content-card side-summary-card" style="margin: 5px 0px;">
                                <div class="card-head">
                                    <h4>Today Summary</h4>
                                </div>
                                <div class="top-products-list">
                                    <div class="top-product-row"><span>Revenue</span><strong id="ovRevenue"><?= manager_html(fmt_money($todayRevenue, $currSymbol)) ?></strong></div>
                                    <div class="top-product-row"><span>Orders</span><strong id="ovOrders"><?= manager_html((string)$todayOrders) ?></strong></div>
                                    <div class="top-product-row"><span>Menu Items</span><strong><?= manager_html((string)$activeMenuItems) ?></strong></div>
                                    <div class="top-product-row"><span>Tables</span><strong><?= manager_html((string)$activeTables) ?></strong></div>
                                    <div class="top-product-row"><span>Staff</span><strong><?= manager_html((string)$staffCount) ?></strong></div>
                                </div>
                                <div class="card-head" style="margin-top:12px;">
                                    <h4>Top Items</h4>
                                </div>
                                <div class="top-products-list">
                                    <?php if (!empty($topItems)): foreach ($topItems as $i => $item): ?>
                                            <div class="top-product-row"><span>#<?= $i + 1 ?> <?= manager_html((string)manager_field($item, ['item_name', 'name'], 'Item')) ?></span><strong><?= manager_html((string)manager_field($item, ['quantity', 'qty'], '0')) ?> orders</strong></div>
                                        <?php endforeach;
                                    else: ?>
                                        <div class="top-product-row"><span>No data yet</span><strong>—</strong></div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-head" style="margin-top:12px;">
                                    <h4>Recent Activity</h4>
                                </div>
                                <div class="activity-list">
                                    <?php if (!empty($recentActivity)): foreach ($recentActivity as $act): ?>
                                            <div class="activity-item"><span class="activity-dot"></span>
                                                <div class="activity-content"><strong><?= manager_html((string)manager_field($act, ['title', 'action'], 'Activity')) ?></strong>
                                                    <p><?= manager_html((string)manager_field($act, ['description', 'message', 'note'], '-')) ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach;
                                    else: ?>
                                        <div class="activity-item"><span class="activity-dot"></span>
                                            <div class="activity-content"><strong>No activity yet</strong>
                                                <p>Activity will appear here.</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </section>

                        <!-- ═══ ORDERS ═══ -->
                        <section class="main-section" data-manager-section="orders">
                            <div class="section-head">
                                <div class="section-head-left"><span class="section-kicker">Orders</span>
                                    <h2>Orders</h2>
                                </div>
                                <div class="section-head-right">
                                    <!-- Date range filters for orders -->
                                    <div class="orders-range-filter manager-mini-segmented">
                                        <button type="button" class="manager-mini-seg-btn active" data-order-range="today">Today</button>
                                        <button type="button" class="manager-mini-seg-btn" data-order-range="week">Week</button>
                                        <button type="button" class="manager-mini-seg-btn" data-order-range="month">Month</button>
                                        <button type="button" class="manager-mini-seg-btn" data-order-range="custom">Custom</button>
                                        <div class="manager-mini-custom-range" id="ordersCustomRange" style="display:none;">
                                            <input type="date" id="ordersRangeFrom">
                                            <input type="date" id="ordersRangeTo">
                                            <button type="button" class="manager-mini-apply-btn" id="ordersApplyBtn">Apply</button>
                                        </div>
                                    </div>
                                    <div class="filter-pills" data-filter-scope="orders">
                                        <button type="button" class="filter-pill active" data-filter-group="category" data-filter-value="all">All</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="new">New</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="accepted">Accepted</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="preparing">Preparing</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="ready">Ready</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="served">Served</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="completed">Completed</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="cancelled">Cancelled</button>
                                    </div>
                                </div>
                            </div>
                            <div class="manager-table-wrap">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Table</th>
                                            <th>Customer</th>
                                            <th>Waiter</th>
                                            <th>Items</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                            <th>Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody data-filter-list="orders">
                                        <?php if (!empty($allOrders)): foreach ($allOrders as $order):
                                                $orderId   = (string)manager_field($order, ['order_id', 'id'], '');
                                                $orderNo   = (string)manager_field($order, ['order_number', 'order_code', 'id'], 'Order');
                                                $tableCode = (string)manager_field($order, ['table_code', 'table_name'], '-');
                                                $customer  = (string)manager_field($order, ['customer_name', 'customer'], 'Walk-in');
                                                $itemsCnt  = (string)manager_field($order, ['items_count'], '0');
                                                // Retrieve the waiter/assigned server name if available
                                                $waiterName = (string)manager_field($order, ['waiter_name', 'waiter', 'assigned_waiter', 'server_name'], '-');
                                                $status    = strtolower((string)manager_field($order, ['status'], 'new'));
                                                $amount    = (float)manager_field($order, ['grand_total', 'total_amount'], 0);
                                                $createdAt = (string)manager_field($order, ['created_at', 'updated_at'], '');
                                        ?>
                                                <tr data-order-id="<?= manager_html($orderId) ?>" data-filter-item data-category="<?= manager_html($status) ?>" data-date="<?= manager_html($createdAt) ?>">
                                                    <td>#<?= manager_html($orderNo) ?></td>
                                                    <td><?= manager_html($tableCode) ?></td>
                                                    <td><?= manager_html($customer) ?></td>
                                                    <td><?= manager_html($waiterName) ?></td>
                                                    <td><?= manager_html($itemsCnt) ?></td>
                                                    <td><span class="status-badge <?= manager_html(manager_badge_class($status)) ?>"><?= manager_html(ucfirst(str_replace('_', ' ', $status))) ?></span></td>
                                                    <td><?= manager_html(fmt_money($amount, $currSymbol)) ?></td>
                                                    <td class="text-muted" style="font-size:11px;"><?= manager_html($createdAt) ?></td>
                                                    <td><button type="button" class="table-action-btn" data-open-modal="orderActionModal" data-order-id="<?= manager_html($orderId) ?>"><span>Manage</span></button></td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?><tr>
                                                <td colspan="9" style="text-align:center;padding:24px;color:#94a3b8;">No orders found.</td>
                                            </tr><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <!-- ═══ REQUESTS ═══ -->
                        <section class="main-section" data-manager-section="requests">
                            <div class="section-head">
                                <div class="section-head-left"><span class="section-kicker">Requests</span>
                                    <h2>Manager Action Requests</h2>
                                </div>
                                <div class="section-head-right">
                                    <!-- Date range filters for requests -->
                                    <div class="requests-range-filter manager-mini-segmented">
                                        <button type="button" class="manager-mini-seg-btn active" data-request-range="today">Today</button>
                                        <button type="button" class="manager-mini-seg-btn" data-request-range="week">Week</button>
                                        <button type="button" class="manager-mini-seg-btn" data-request-range="month">Month</button>
                                        <button type="button" class="manager-mini-seg-btn" data-request-range="custom">Custom</button>
                                        <div class="manager-mini-custom-range" id="requestsCustomRange" style="display:none;">
                                            <input type="date" id="requestsRangeFrom">
                                            <input type="date" id="requestsRangeTo">
                                            <button type="button" class="manager-mini-apply-btn" id="requestsApplyBtn">Apply</button>
                                        </div>
                                    </div>
                                    <div class="filter-pills" data-filter-scope="requests">
                                        <button type="button" class="filter-pill active" data-filter-group="category" data-filter-value="all">All</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="new">New</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="approved">Approved</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="cancelled">Cancelled</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="closed">Closed</button>

                                    </div>
                                </div>
                            </div>
                            <div class="request-cards-grid" data-filter-list="requests">
                                <?php if (!empty($requestItems)): foreach ($requestItems as $req):
                                        $reqId   = (string)manager_field($req, ['request_id', 'id'], '');
                                        $reqType = (string)manager_field($req, ['request_type', 'title', 'type'], 'Request');
                                        $tblCode = (string)manager_field($req, ['table_code', 'table_name'], '-');
                                        $status  = strtolower((string)manager_field($req, ['status'], 'pending'));
                                        $note    = (string)manager_field($req, ['message', 'description', 'note'], '-');
                                        $reqAt   = (string)manager_field($req, ['created_at', 'requested_at', 'updated_at'], '');
                                ?>
                                        <div class="content-card request-card manager-request-card" data-filter-item data-category="<?= manager_html($status) ?>" data-date="<?= manager_html($reqAt) ?>">
                                            <div class="request-card-top">
                                                <strong><?= manager_html($reqType) ?></strong>
                                                <span class="status-badge <?= manager_html(manager_badge_class($status)) ?>"><?= manager_html(ucfirst(str_replace('_', ' ', $status))) ?></span>
                                            </div>
                                            <p><?= manager_html($note) ?></p>
                                            <div class="request-meta"><i class="fa-solid fa-chair" style="margin-right:4px;"></i>Table <?= manager_html($tblCode) ?></div>
                                            <div class="request-actions"><button type="button" class="table-action-btn" data-open-modal="requestActionModal" data-request-id="<?= manager_html($reqId) ?>"><span>Open</span></button></div>
                                        </div>
                                    <?php endforeach;
                                else: ?>
                                    <div class="content-card request-card">
                                        <div class="request-card-top"><strong>No requests</strong><span class="status-badge neutral">Idle</span></div>
                                        <p>Manager-action requests will appear here.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </section>

                        <!-- ═══ BILLS ═══ -->
                        <section class="main-section" data-manager-section="bills">
                            <div class="section-head">
                                <div class="section-head-left"><span class="section-kicker">Bills</span>
                                    <h2>Bills</h2>
                                </div>
                                <div class="section-head-right">
                                    <!-- Date range filters for bills -->
                                    <div class="bills-range-filter manager-mini-segmented">
                                        <button type="button" class="manager-mini-seg-btn active" data-bill-range="today">Today</button>
                                        <button type="button" class="manager-mini-seg-btn" data-bill-range="week">Week</button>
                                        <button type="button" class="manager-mini-seg-btn" data-bill-range="month">Month</button>
                                        <button type="button" class="manager-mini-seg-btn" data-bill-range="custom">Custom</button>
                                        <div class="manager-mini-custom-range" id="billsCustomRange" style="display:none;">
                                            <input type="date" id="billsRangeFrom">
                                            <input type="date" id="billsRangeTo">
                                            <button type="button" class="manager-mini-apply-btn" id="billsApplyBtn">Apply</button>
                                        </div>
                                    </div>
                                    <div class="filter-pills" data-filter-scope="bills">
                                        <button type="button" class="filter-pill active" data-filter-group="category" data-filter-value="all">All</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="unpaid">Unpaid</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="in_processing">In Processing</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="paid">Paid</button>
                                    </div>
                                </div>
                            </div>
                            <div class="manager-table-wrap">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Bill ID</th>
                                            <th>Table</th>
                                            <th>Order</th>
                                            <th>Payment Status</th>
                                            <th>Amount</th>
                                            <th>Updated</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody data-filter-list="bills">
                                        <?php if (!empty($allBills)): foreach ($allBills as $bill):
                                                $billId    = (string)manager_field($bill, ['bill_id', 'id'], '');
                                                $billNo    = (string)manager_field($bill, ['bill_number', 'id'], 'Bill');
                                                $tblCode   = (string)manager_field($bill, ['table_code', 'table_name'], '-');
                                                $orderCode = (string)manager_field($bill, ['order_code', 'order_number', 'order_id'], '-');
                                                $status = strtolower(trim(str_replace([' ', '-'], '_', (string)manager_field($bill, ['payment_status'], 'unpaid'))));
                                                $amount    = (float)manager_field($bill, ['total_amount', 'grand_total', 'payable_amount'], 0);
                                                $updAt     = (string)manager_field($bill, ['updated_at', 'generated_at', 'event_time'], '-');
                                        ?>
                                                <tr data-bill-id="<?= manager_html($billId) ?>" data-filter-item data-category="<?= manager_html($status) ?>" data-date="<?= manager_html($updAt) ?>">
                                                    <td>#<?= manager_html($billNo) ?></td>
                                                    <td><?= manager_html($tblCode) ?></td>
                                                    <td><?= manager_html($orderCode) ?></td>
                                                    <td><span class="status-badge <?= manager_html(manager_badge_class($status)) ?>"><?= manager_html(ucfirst(str_replace('_', ' ', $status))) ?></span></td>
                                                    <td><?= manager_html(fmt_money($amount, $currSymbol)) ?></td>
                                                    <td class="text-muted" style="font-size:11px;"><?= manager_html($updAt) ?></td>
                                                    <td><button type="button" class="table-action-btn" data-open-modal="billActionModal" data-bill-id="<?= manager_html($billId) ?>"><span>Open</span></button></td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?><tr>
                                                <td colspan="7" style="text-align:center;padding:24px;color:#94a3b8;">No bills found.</td>
                                            </tr><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <!-- ═══ STAFF ═══ -->
                        <section class="main-section" data-manager-section="staff-management">
                            <div class="section-head">
                                <div class="section-head-left"><span class="section-kicker">Staff</span>
                                    <h2>Staff Management</h2>
                                </div>
                                <div class="section-head-right">
                                    <div class="filter-pills" data-filter-scope="staff-management">
                                        <button type="button" class="filter-pill active" data-filter-group="category" data-filter-value="all">All</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="waiter">Waiter</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="chef">Chef</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="pad">Pad</button>
                                    </div>
                                    <button type="button" class="primary-btn" data-open-modal="staffActionModal" data-staff-id="">
                                        <i class="fa-solid fa-plus" style="margin-right:6px;"></i>Add Staff
                                    </button>
                                </div>
                            </div>
                            <div class="manager-table-wrap">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Profile</th>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>Role</th>
                                            <th>Shift</th>
                                            <th>Phone</th>
                                            <th>Aadhaar</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody data-filter-list="staff-management">
                                        <?php if (!empty($staff)): foreach ($staff as $member):
                                                $staffId    = (string)manager_field($member, ['user_id', 'id'], '');
                                                $staffName  = (string)manager_field($member, ['full_name', 'name'], 'Staff');
                                                $staffCode  = (string)manager_field($member, ['employee_code'], '-');
                                                $staffRole  = strtolower((string)manager_field($member, ['role_at_branch', 'role_type', 'designation'], 'waiter'));
                                                if (in_array($staffRole, ['customerpad', 'customer_pad'], true)) $staffRole = 'pad';
                                                if (in_array($staffRole, ['kitchen', 'cook'], true)) $staffRole = 'chef';
                                                $staffStat  = strtolower((string)manager_field($member, ['access_status', 'user_status', 'status'], 'active'));
                                                $staffPhone = (string)manager_field($member, ['phone', 'mobile'], '-');
                                                $staffAdh   = (string)manager_field($member, ['aadhar_no'], '-');
                                                $staffPic   = manager_asset_url((string)manager_field($member, ['profile_pic', 'profile_pic_url', 'photo'], ''), 'default/default-avatar.svg');
                                                $shiftStart = (string)manager_field($member, ['shift_start_time'], '');
                                                $shiftEnd   = (string)manager_field($member, ['shift_end_time'], '');
                                                $shiftText  = trim($shiftStart . ($shiftStart !== '' || $shiftEnd !== '' ? ' – ' : '') . $shiftEnd) ?: (string)manager_field($member, ['shift_label', 'shift_name'], '-');
                                        ?>
                                                <tr data-staff-id="<?= manager_html($staffId) ?>" data-filter-item data-category="<?= manager_html($staffRole) ?>">
                                                    <td>
                                                        <div class="staff-avatar-cell">
                                                            <img src="<?= manager_html($staffPic) ?>" alt="<?= manager_html($staffName) ?>">
                                                        </div>
                                                    </td>
                                                    <td><?= manager_html($staffName) ?></td>
                                                    <td><?= manager_html($staffCode !== '' ? $staffCode : '-') ?></td>
                                                    <td><?= manager_html(ucfirst(str_replace('_', ' ', $staffRole))) ?></td>
                                                    <td><?= manager_html($shiftText) ?></td>
                                                    <td><?= manager_html($staffPhone) ?></td>
                                                    <td><?= manager_html($staffAdh !== '' ? $staffAdh : '-') ?></td>
                                                    <td><span class="status-badge <?= manager_html(manager_badge_class($staffStat)) ?>"><?= manager_html(ucfirst(str_replace('_', ' ', $staffStat))) ?></span></td>
                                                    <td>
                                                        <div class="action-btn-group">
                                                            <button type="button" class="table-action-btn" data-open-modal="staffActionModal" data-staff-id="<?= manager_html($staffId) ?>"><span>Edit</span></button>
                                                            <button type="button" class="table-action-btn danger-btn" data-action="delete_staff" data-staff-id="<?= manager_html($staffId) ?>" data-staff-name="<?= manager_html($staffName) ?>"><span>Del</span></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?><tr>
                                                <td colspan="9" style="text-align:center;padding:24px;color:#94a3b8;">No staff records.</td>
                                            </tr><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <!-- ═══ TABLES ═══ -->
                        <section class="main-section" data-manager-section="tables">
                            <div class="section-head">
                                <div class="section-head-left"><span class="section-kicker">Tables</span>
                                    <h2>Table Management</h2>
                                </div>
                                <div class="section-head-right">
                                    <div class="filter-pills" data-filter-scope="tables">
                                        <button type="button" class="filter-pill active" data-filter-group="category" data-filter-value="all">All</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="available">Available</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="occupied">Occupied</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="reserved">Reserved</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="waiting">Waiting</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="maintenance">Maintenance</button>
                                    </div>
                                    <button type="button" class="primary-btn" data-open-modal="tableActionModal" data-table-id="">
                                        <i class="fa-solid fa-plus" style="margin-right:6px;"></i>Add Table
                                    </button>
                                </div>
                            </div>
                            <div class="table-cards-grid" data-filter-list="tables">
                                <?php if (!empty($tables)): foreach ($tables as $table):
                                        $tableId   = (string)manager_field($table, ['table_id', 'id'], '');
                                        $tableCode = (string)manager_field($table, ['table_code', 'table_name'], 'Table');
                                        $capacity  = (string)manager_field($table, ['capacity'], '0');
                                        $zone      = (string)manager_field($table, ['zone', 'area_name', 'section_name', 'label'], '-');
                                        $shape     = (string)manager_field($table, ['shape'], 'square');
                                        $status    = strtolower((string)manager_field($table, ['status', 'occupancy_status'], 'available'));
                                        // Retrieve assigned waiter name and status if provided
                                        $waiterName  = (string)manager_field($table, ['waiter_name', 'assigned_waiter_name', 'waiter', 'server_name'], '');
                                        $waiterStatus = (string)manager_field($table, ['waiter_status', 'assigned_waiter_status', 'waiter_status'], '');
                                ?>
                                        <div class="content-card table-card" data-filter-item data-category="<?= manager_html($status) ?>">
                                            <div class="table-card-top">
                                                <div class="table-icon-wrap table-shape-<?= manager_html($shape) ?>">
                                                    <i class="fa-solid fa-chair"></i>
                                                </div>
                                                <span class="status-badge <?= manager_html(manager_badge_class($status)) ?>"><?= manager_html(ucfirst(str_replace('_', ' ', $status))) ?></span>
                                            </div>
                                            <strong class="table-code-label"><?= manager_html($tableCode) ?></strong>
                                            <div class="table-meta-row"><i class="fa-solid fa-users"></i> <?= manager_html($capacity) ?> seats &nbsp;•&nbsp; <i class="fa-solid fa-location-dot"></i> <?= manager_html($zone) ?></div>
                                            <?php if ($waiterName !== ''): ?>
                                                <div class="table-meta-row"><i class="fa-solid fa-user-tie"></i> <?= manager_html($waiterName) ?><?= $waiterStatus !== '' ? ' (' . manager_html(ucfirst($waiterStatus)) . ')' : '' ?></div>
                                            <?php endif; ?>
                                            <button type="button" class="table-action-btn" data-open-modal="tableActionModal" data-table-id="<?= manager_html($tableId) ?>"><span>Manage</span></button>
                                        </div>
                                    <?php endforeach;
                                else: ?>
                                    <div class="content-card table-card">
                                        <div class="table-card-top"><i class="fa-solid fa-chair"></i><span class="status-badge neutral">Empty</span></div><strong>No Tables</strong>
                                        <p>No dining tables found.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </section>

                        <!-- ═══ MENUS ═══ -->
                        <section class="main-section" data-manager-section="menus">
                            <div class="section-head">
                                <div class="section-head-left"><span class="section-kicker">Menu</span>
                                    <h2>Menu Management</h2>
                                </div>
                                <div class="section-head-right">
                                    <div class="filter-pills" data-filter-scope="menus">
                                        <button type="button" class="filter-pill active" data-filter-group="category" data-filter-value="all">All</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="veg">Veg</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="non_veg">Non-Veg</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="drinks">Drinks</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="desserts">Desserts</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="others">Others</button>
                                    </div>
                                    <button type="button" class="primary-btn" data-open-modal="menuActionModal" data-menu-id="">
                                        <i class="fa-solid fa-plus" style="margin-right:6px;"></i>Add Item
                                    </button>
                                </div>
                            </div>
                            <div class="manager-table-wrap">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Category</th>
                                            <th>Type</th>
                                            <th>Price</th>
                                            <th>Available</th>
                                            <th>Stock</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody data-filter-list="menus">
                                        <?php if (!empty($menuItems)): foreach ($menuItems as $item):
                                                $menuId    = (string)manager_field($item, ['menu_id', 'id'], '');
                                                $itemName  = (string)manager_field($item, ['name', 'item_name'], 'Menu Item');
                                                $category  = (string)manager_field($item, ['category_name', 'category'], '-');
                                                $type      = (string)manager_field($item, ['item_type', 'food_type', 'type'], '-');
                                                $price     = (float)manager_field($item, ['selling_price', 'price', 'base_price'], 0);
                                                $stock     = strtolower((string)manager_field($item, ['stock_status', 'status'], 'in_stock'));
                                                $avail     = (string)manager_field($item, ['is_available'], '1') === '1';
                                                $imageUrl  = manager_asset_url((string)manager_field($item, ['image_url', 'item_image', 'image', 'photo'], ''), 'default/default-food.jpg');
                                        ?>
                                                <tr data-menu-id="<?= manager_html($menuId) ?>" data-filter-item data-category="<?= manager_html(strtolower($type)) ?>">
                                                    <td>
                                                        <div class="manager-menu-item-cell">
                                                            <span class="manager-menu-item-thumb"><img src="<?= manager_html($imageUrl) ?>" alt="<?= manager_html($itemName) ?>"></span>
                                                            <span><?= manager_html($itemName) ?></span>
                                                        </div>
                                                    </td>
                                                    <td><?= manager_html($category) ?></td>
                                                    <td><?= manager_html(ucfirst(str_replace('_', ' ', $type))) ?></td>
                                                    <td><?= manager_html(fmt_money($price, $currSymbol)) ?></td>
                                                    <td><span class="status-badge <?= $avail ? 'success' : 'danger' ?>"><?= $avail ? 'Yes' : 'No' ?></span></td>
                                                    <td><span class="status-badge <?= manager_html(manager_badge_class($stock)) ?>"><?= manager_html(ucfirst(str_replace('_', ' ', $stock))) ?></span></td>
                                                    <td>
                                                        <div class="action-btn-group">
                                                            <button type="button" class="table-action-btn" data-open-modal="menuActionModal" data-menu-id="<?= manager_html($menuId) ?>"><span>Edit</span></button>
                                                            <button type="button" class="table-action-btn danger-btn" data-action="delete_menu_item" data-menu-id="<?= manager_html($menuId) ?>" data-item-name="<?= manager_html($itemName) ?>"><span>Del</span></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?><tr>
                                                <td colspan="7" style="text-align:center;padding:24px;color:#94a3b8;">No menu items.</td>
                                            </tr><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <!-- ═══ OFFERS & DISCOUNTS ═══ -->
                        <section class="main-section" data-manager-section="offers">
                            <div class="section-head">
                                <div class="section-head-left">
                                    <span class="section-kicker">Promotions</span>
                                    <h2>Offers & Discounts</h2>
                                </div>

                                <div class="section-head-right">
                                    <div class="filter-pills" data-filter-scope="offers">
                                        <button type="button" class="filter-pill active" data-filter-group="category" data-filter-value="all">All</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="active">Active</button>
                                        <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="inactive">Inactive</button>
                                    </div>

                                    <button type="button" class="primary-btn" data-open-modal="offerActionModal" data-offer-id="">
                                        <i class="fa-solid fa-plus" style="margin-right:6px;"></i>Add Offer
                                    </button>
                                </div>
                            </div>

                            <div class="offers-premium-grid" data-filter-list="offers">
                                <?php if (!empty($offerBanners)): foreach ($offerBanners as $offer):
                                        $offerId   = (string) manager_field($offer, ['banner_id', 'id'], '');
                                        $imgPath   = (string) manager_field($offer, ['image_path'], '');
                                        $imgUrl    = manager_asset_url($imgPath, 'default/default-food.jpg');
                                        $sortOrder = (int) manager_field($offer, ['sort_order'], 1);
                                        $isActive  = (string) manager_field($offer, ['is_active'], '1') === '1';
                                        $startsAt  = (string) manager_field($offer, ['starts_at'], '');
                                        $endsAt    = (string) manager_field($offer, ['ends_at'], '');
                                        $createdAt = (string) manager_field($offer, ['created_at'], '');
                                        $statusKey = $isActive ? 'active' : 'inactive';
                                ?>
                                        <article class="offer-banner-card"
                                            data-filter-item
                                            data-offer-id="<?= manager_html($offerId) ?>"
                                            data-category="<?= manager_html($statusKey) ?>"
                                            data-date="<?= manager_html($createdAt) ?>">
                                            <div class="offer-banner-media">
                                                <img src="<?= manager_html($imgUrl) ?>" alt="Offer banner">
                                                <span class="offer-banner-ratio-pill">Customer Hero Fit</span>
                                            </div>

                                            <div class="offer-banner-body">
                                                <div>
                                                    <span class="card-kicker">Offer Banner</span>
                                                    <h3>Sort #<?= manager_html((string) $sortOrder) ?></h3>
                                                </div>

                                                <span class="status-badge <?= $isActive ? 'success' : 'danger' ?>">
                                                    <?= $isActive ? 'Active' : 'Inactive' ?>
                                                </span>
                                            </div>

                                            <div class="offer-banner-meta">
                                                <span><i class="fa-regular fa-calendar"></i> Start: <?= manager_html($startsAt !== '' ? $startsAt : 'Always') ?></span>
                                                <span><i class="fa-regular fa-clock"></i> End: <?= manager_html($endsAt !== '' ? $endsAt : 'No expiry') ?></span>
                                            </div>

                                            <div class="offer-banner-actions">
                                                <button type="button" class="table-action-btn" data-open-modal="offerActionModal" data-offer-id="<?= manager_html($offerId) ?>">
                                                    <span>Edit</span>
                                                </button>
                                                <button type="button"
                                                    class="table-action-btn danger-btn"
                                                    data-action="delete_offer_banner"
                                                    data-offer-id="<?= manager_html($offerId) ?>">
                                                    <span>Delete</span>
                                                </button>
                                            </div>
                                        </article>
                                    <?php endforeach;
                                else: ?>
                                    <div class="manager-empty-state">
                                        <img src="<?= manager_html(manager_asset_url('default/no-data.svg', 'default/default-logo.svg')) ?>" alt="No offers">
                                        <strong>No offers available</strong>
                                        <p>Create premium offer banners for the customer pad hero carousel.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </section>

                        <!-- ═══ ACTIVITY ═══ -->
                        <section class="main-section" data-manager-section="activity">
                            <div class="section-head">
                                <div class="section-head-left"><span class="section-kicker">Activity</span>
                                    <h2>Branch Activity Feed</h2>
                                </div>
                            </div>
                            <div class="content-card">
                                <div class="filter-pills" data-filter-scope="activity">
                                    <button type="button" class="filter-pill active" data-filter-group="category" data-filter-value="all">All</button>
                                    <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="waiter">Waiter</button>
                                    <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="kitchen">Kitchen</button>
                                    <button type="button" class="filter-pill" data-filter-group="category" data-filter-value="customerpad">Customer Pad</button>
                                </div>
                                <div class="activity-list" data-filter-list="activity" style="margin-top:14px;">
                                    <?php if (!empty($recentActivity)): foreach ($recentActivity as $act):
                                            $actSrc   = strtolower((string)manager_field($act, ['source', 'role', 'actor_role', 'raised_by_role'], 'waiter'));
                                            if (in_array($actSrc, ['customer', 'customer_pad'], true)) $actSrc = 'customerpad';
                                            if (in_array($actSrc, ['chef', 'cook'], true)) $actSrc = 'kitchen';
                                            $actDate  = (string)manager_field($act, ['created_at', 'updated_at', 'event_time', 'logged_at'], '');
                                            $actTitle = (string)manager_field($act, ['title', 'action'], 'Activity');
                                            $actDesc  = (string)manager_field($act, ['description', 'message', 'note'], '-');
                                    ?>
                                            <div class="activity-item" data-filter-item data-category="<?= manager_html($actSrc) ?>" data-date="<?= manager_html($actDate) ?>">
                                                <span class="activity-dot activity-dot--<?= manager_html($actSrc) ?>"></span>
                                                <div class="activity-content">
                                                    <strong><?= manager_html($actTitle) ?></strong>
                                                    <p><?= manager_html($actDesc) ?></p>
                                                    <?php if ($actDate !== ''): ?><small class="text-muted"><?= manager_html($actDate) ?></small><?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach;
                                    else: ?>
                                        <div class="activity-item"><span class="activity-dot"></span>
                                            <div class="activity-content"><strong>No activity</strong>
                                                <p>Activity feed will appear here.</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </section>

                        <!-- ═══ Settings ═══ -->
                        <section class="main-section" data-manager-section="settings">
                            <div class="section-head settings-section-head">
                                <div class="section-head-left">
                                    <span class="section-kicker">Settings</span>
                                    <h2>Dashboard Preferences</h2>
                                </div>

                                <div class="settings-top-actions">
                                    <button type="button" class="primary-btn" id="saveDashboardSettingsBtn">
                                        <i class="fa-solid fa-floppy-disk"></i> Save Preferences
                                    </button>
                                    <button type="button" class="secondary-btn" id="resetDashboardSettingsBtn">
                                        <i class="fa-solid fa-rotate-left"></i> Reset Default
                                    </button>
                                    <button type="button" class="secondary-btn" id="testNotificationSoundBtn">
                                        <i class="fa-solid fa-play"></i> Test Sound
                                    </button>
                                </div>
                            </div>

                            <div class="settings-panel">
                                <div class="settings-block">
                                    <div class="settings-block-head">
                                        <h4><i class="fa-solid fa-sliders"></i> General</h4>
                                    </div>

                                    <div class="settings-list">
                                        <div class="setting-row">
                                            <div class="setting-info">
                                                <strong>Theme Mode</strong>
                                                <p>Switch between light and dark dashboard mode.</p>
                                            </div>
                                            <div class="setting-control">
                                                <div class="manager-segmented-control" id="themeModeSwitch">
                                                    <button type="button" class="seg-btn active" data-theme-mode="light">
                                                        <i class="fa-solid fa-sun"></i> Light
                                                    </button>
                                                    <button type="button" class="seg-btn" data-theme-mode="dark">
                                                        <i class="fa-solid fa-moon"></i> Dark
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="setting-row">
                                            <div class="setting-info">
                                                <strong>Compact Dashboard</strong>
                                                <p>Reduce spacing for denser manager view.</p>
                                            </div>
                                            <div class="setting-control">
                                                <label class="manager-switch">
                                                    <input type="checkbox" id="compactModeToggle">
                                                    <span class="manager-switch-slider"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="setting-row">
                                            <div class="setting-info">
                                                <strong>Smooth Animations</strong>
                                                <p>Enable premium transitions and motion effects.</p>
                                            </div>
                                            <div class="setting-control">
                                                <label class="manager-switch">
                                                    <input type="checkbox" id="animationToggle" checked>
                                                    <span class="manager-switch-slider"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="settings-block">
                                    <div class="settings-block-head">
                                        <h4><i class="fa-solid fa-bell"></i> Notifications</h4>
                                    </div>

                                    <div class="settings-list">
                                        <div class="setting-row">
                                            <div class="setting-info">
                                                <strong>Notification Sound</strong>
                                                <p>Choose the sound for new orders, requests, and alerts.</p>
                                            </div>
                                            <div class="setting-control">
                                                <select id="notificationSoundSelect">
                                                    <option value="/RestaurantAssets/AlertSounds/Sound1.mpeg">Sound 1</option>
                                                    <option value="/RestaurantAssets/AlertSounds/Sound2.mpeg">Sound 2</option>
                                                    <option value="/RestaurantAssets/AlertSounds/Sound3.mpeg">Sound 3</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="setting-row">
                                            <div class="setting-info">
                                                <strong>Sound Alerts</strong>
                                                <p>Play alert sound for important live updates.</p>
                                            </div>
                                            <div class="setting-control">
                                                <label class="manager-switch">
                                                    <input type="checkbox" id="settingsSoundToggle" checked>
                                                    <span class="manager-switch-slider"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="setting-row">
                                            <div class="setting-info">
                                                <strong>Desktop Alerts</strong>
                                                <p>Show browser notifications for new manager events.</p>
                                            </div>
                                            <div class="setting-control">
                                                <label class="manager-switch">
                                                    <input type="checkbox" id="desktopAlertToggle">
                                                    <span class="manager-switch-slider"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </section>

                    </div><!-- /main-body -->

                    <div class="footer manager-footer">
                        <div class="footer-left">
                            <p><?= manager_html($brandName) ?> • Manager Control Center</p>
                        </div>
                        <div class="footer-right"><span>Branch: <?= manager_html($branchName) ?></span><span>•</span><span>Manager: <?= manager_html($managerName) ?></span></div>
                    </div>
                </div><!-- /right-main-cont -->
            </div><!-- /main -->
        </div><!-- /container -->
    </section>

    <!-- ══════════════════════════════════════════════
         MODALS
    ══════════════════════════════════════════════ -->
    <div class="manager-modal-backdrop" id="managerModalBackdrop" aria-hidden="true"></div>

    <!-- ── Branch Details Modal ── -->
    <div class="action-modal manager-modal" id="branchDetailsModal" aria-hidden="true">
        <div class="container">
            <div class="modal-card manager-modal__dialog">
                <div class="modal-head manager-modal__head">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="modal-head-icon" style="background:var(--g-blue-soft);"><i class="fa-solid fa-store" style="color:var(--g-blue);"></i></div>
                        <h3>Branch Details</h3>
                    </div>
                    <button type="button" class="modal-close-btn manager-modal__close" data-close-modal="branchDetailsModal"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body manager-modal__body">
                    <div class="branch-details-card">
                        <div class="branch-hero">
                            <img src="<?= manager_html($brandLogo) ?>" alt="<?= manager_html($brandName) ?>" class="branch-logo">
                            <div>
                                <h2><?= manager_html($brandName) ?></h2>
                                <p class="branch-sub"><?= manager_html($branchName) ?></p>
                                <span class="status-badge <?= manager_html(manager_badge_class($branchStatus)) ?>"><?= manager_html(ucfirst($branchStatus)) ?></span>
                            </div>
                        </div>
                        <div class="manager-form-grid" style="margin-top:18px;">
                            <div class="manager-field"><span><i class="fa-solid fa-tag"></i> Branch Code</span>
                                <div><?= manager_html($branchCode ?: '—') ?></div>
                            </div>
                            <div class="manager-field"><span><i class="fa-solid fa-city"></i> City / State</span>
                                <div><?= manager_html(trim($branchCity . ', ' . $branchState, ', ') ?: '—') ?></div>
                            </div>
                            <div class="manager-field"><span><i class="fa-solid fa-location-pin"></i> Pincode</span>
                                <div><?= manager_html($branchPincode ?: '—') ?></div>
                            </div>
                            <div class="manager-field"><span><i class="fa-solid fa-phone"></i> Phone</span>
                                <div><?= manager_html($branchPhone ?: '—') ?></div>
                            </div>
                            <div class="manager-field"><span><i class="fa-solid fa-envelope"></i> Email</span>
                                <div><?= manager_html($branchEmail ?: '—') ?></div>
                            </div>
                            <div class="manager-field"><span><i class="fa-solid fa-file-invoice"></i> GST / Tax No.</span>
                                <div><?= manager_html($branchTaxNo ?: '—') ?></div>
                            </div>
                            <div class="manager-field manager-field--full"><span><i class="fa-solid fa-map-marker-alt"></i> Address</span>
                                <div><?= manager_html($branchAddress ?: '—') ?></div>
                            </div>
                            <?php if ($branchOpens || $branchCloses): ?>
                                <div class="manager-field"><span><i class="fa-solid fa-clock"></i> Opens At</span>
                                    <div><?= manager_html($branchOpens ?: '—') ?></div>
                                </div>
                                <div class="manager-field"><span><i class="fa-solid fa-clock"></i> Closes At</span>
                                    <div><?= manager_html($branchCloses ?: '—') ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Manager Profile Modal ── -->
    <div class="action-modal manager-modal" id="managerProfileModal" aria-hidden="true">
        <div class="container">
            <div class="modal-card manager-modal__dialog modal-card-profile">
                <div class="modal-head manager-modal__head">
                    <h3>My Profile</h3>
                    <button type="button" class="modal-close-btn manager-modal__close" data-close-modal="managerProfileModal"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body manager-modal__body" style="padding:0;">
                    <!-- Profile Hero -->
                    <div class="profile-hero-banner">
                        <div class="profile-avatar-wrap">
                            <div class="profile-avatar-ring" id="profileAvatarRing">
                                <img src="<?= manager_html($managerProfilePic) ?>" alt="<?= manager_html($managerName) ?>" id="profileAvatarImg">
                            </div>
                            <label class="profile-avatar-edit-btn" for="profilePicFileInput" title="Change photo">
                                <i class="fa-solid fa-camera"></i>
                                <input type="file" id="profilePicFileInput" name="profile_pic_file" accept="image/jpeg,image/png,image/webp,image/gif" data-max-size="20971520" style="display:none;">
                            </label>
                        </div>
                        <div class="profile-hero-info">
                            <h2 id="profileHeroName"><?= manager_html($managerName) ?></h2>
                            <p id="profileHeroRole"><?= manager_html(ucfirst($managerRole)) ?><?= $managerEmployeeCode !== '' ? ' · ' . manager_html($managerEmployeeCode) : '' ?></p>
                            <div class="profile-hero-pills">
                                <span class="meta-pill"><?= manager_html(ucfirst($managerRole)) ?></span>
                                <span class="meta-pill status-pill-success">Active</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="profile-tabs-bar">
                        <button type="button" class="profile-tab-btn active" data-profile-tab="view"><i class="fa-solid fa-eye"></i> View</button>
                        <button type="button" class="profile-tab-btn" data-profile-tab="edit"><i class="fa-solid fa-pen"></i> Edit Profile</button>
                        <button type="button" class="profile-tab-btn" data-profile-tab="password"><i class="fa-solid fa-lock"></i> Change Password</button>
                    </div>

                    <!-- Tab: View -->
                    <div class="profile-tab-panel active" id="profileTabView" style="padding:20px 24px 24px;">
                        <div class="manager-form-grid">
                            <div class="manager-field"><span><i class="fa-solid fa-user"></i> Full Name</span>
                                <div id="pvName"><?= manager_html($managerName) ?></div>
                            </div>
                            <div class="manager-field"><span><i class="fa-solid fa-id-badge"></i> Employee Code</span>
                                <div id="pvEmpCode"><?= manager_html($managerEmployeeCode ?: '—') ?></div>
                            </div>
                            <div class="manager-field"><span><i class="fa-solid fa-envelope"></i> Email</span>
                                <div id="pvEmail"><?= manager_html($managerEmail ?: '—') ?></div>
                            </div>
                            <div class="manager-field"><span><i class="fa-solid fa-phone"></i> Phone</span>
                                <div id="pvPhone"><?= manager_html($managerPhone ?: '—') ?></div>
                            </div>
                            <div class="manager-field"><span><i class="fa-solid fa-briefcase"></i> Role</span>
                                <div id="pvRole"><?= manager_html(ucfirst($managerRole)) ?></div>
                            </div>
                            <div class="manager-field"><span><i class="fa-solid fa-fingerprint"></i> Aadhaar No.</span>
                                <div id="pvAadhar"><?= manager_html($managerAadharNo ?: '—') ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Edit Profile -->
                    <div class="profile-tab-panel" id="profileTabEdit" style="padding:20px 24px 24px;">
                        <form id="profileEditForm" autocomplete="off">
                            <input type="hidden" name="action" value="update_manager_profile">
                            <input type="hidden" name="target_user_id" id="profileEditUserId" value="<?= manager_html($managerUserId) ?>">
                            <input type="hidden" name="profile_pic_data" id="profilePicDataInput" value="">
                            <input type="hidden" name="existing_profile_pic" id="profileExistingPicInput" value="<?= manager_html((string)($pageContext['manager_profile_pic'] ?? '')) ?>">

                            <div class="manager-form-grid">
                                <div class="manager-field manager-field--full">
                                    <span>Profile Picture</span>
                                    <div class="img-upload-preview-wrap">
                                        <img src="<?= manager_html($managerProfilePic) ?>" alt="Preview" class="img-upload-preview" id="profileEditPreview">
                                        <div class="img-upload-actions">
                                            <label class="secondary-btn" for="profilePicFileInput" style="cursor:pointer;">
                                                <i class="fa-solid fa-upload"></i> Choose Photo
                                            </label>
                                            <small id="profilePicFileName">No file chosen</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="manager-field">
                                    <span>Full Name <span class="required-star">*</span></span>
                                    <input type="text" name="full_name" id="profileEditName" value="<?= manager_html($managerName) ?>" placeholder="Full name" required>
                                </div>
                                <div class="manager-field">
                                    <span>Employee Code</span>
                                    <input type="text" name="employee_code" id="profileEditEmpCode" value="<?= manager_html($managerEmployeeCode) ?>" placeholder="e.g. MGR001">
                                </div>
                                <div class="manager-field">
                                    <span>Email <span class="required-star">*</span></span>
                                    <input type="email" name="email" id="profileEditEmail" value="<?= manager_html($managerEmail) ?>" placeholder="manager@example.com" required>
                                </div>
                                <div class="manager-field">
                                    <span>Phone</span>
                                    <input type="tel" name="phone" id="profileEditPhone" value="<?= manager_html($managerPhone) ?>" placeholder="9XXXXXXXXX">
                                </div>
                                <div class="manager-field">
                                    <span>Aadhaar No.</span>
                                    <input type="text" name="aadhar_no" id="profileEditAadhar" value="<?= manager_html($managerAadharNo) ?>" placeholder="12-digit aadhaar" maxlength="12">
                                </div>
                            </div>
                            <div class="manager-modal-actions">
                                <button type="submit" class="primary-btn" id="profileEditSaveBtn">
                                    <i class="fa-solid fa-floppy-disk"></i> Save Profile
                                </button>
                                <button type="button" class="secondary-btn" data-profile-tab-switch="view">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <!-- Tab: Change Password -->
                    <div class="profile-tab-panel" id="profileTabPassword" style="padding:20px 24px 24px;">
                        <form id="profilePasswordForm" autocomplete="off">
                            <input type="hidden" name="action" value="change_password">
                            <input type="hidden" name="target_user_id" value="<?= manager_html($managerUserId) ?>">
                            <div class="manager-form-grid">
                                <div class="manager-field manager-field--full">
                                    <span>Current Password <span class="required-star">*</span></span>
                                    <div class="password-input-wrap">
                                        <input type="password" name="current_password" id="pwCurrentPass" placeholder="Your current password" required autocomplete="current-password">
                                        <button type="button" class="pw-toggle-btn" data-pw-target="pwCurrentPass"><i class="fa-solid fa-eye"></i></button>
                                    </div>
                                </div>
                                <div class="manager-field">
                                    <span>New Password <span class="required-star">*</span></span>
                                    <div class="password-input-wrap">
                                        <input type="password" name="new_password" id="pwNewPass" placeholder="Min 6 characters" required autocomplete="new-password">
                                        <button type="button" class="pw-toggle-btn" data-pw-target="pwNewPass"><i class="fa-solid fa-eye"></i></button>
                                    </div>
                                </div>
                                <div class="manager-field">
                                    <span>Confirm Password <span class="required-star">*</span></span>
                                    <div class="password-input-wrap">
                                        <input type="password" name="confirm_password" id="pwConfirmPass" placeholder="Repeat new password" required autocomplete="new-password">
                                        <button type="button" class="pw-toggle-btn" data-pw-target="pwConfirmPass"><i class="fa-solid fa-eye"></i></button>
                                    </div>
                                </div>
                                <div class="manager-field manager-field--full">
                                    <div class="password-strength-bar" id="pwStrengthBar">
                                        <div class="pw-strength-fill" id="pwStrengthFill"></div>
                                    </div>
                                    <small id="pwStrengthLabel" style="font-size:11px;color:#64748b;margin-top:4px;display:block;"></small>
                                </div>
                            </div>
                            <div class="manager-modal-actions">
                                <button type="submit" class="primary-btn" id="pwChangeSaveBtn">
                                    <i class="fa-solid fa-shield-halved"></i> Update Password
                                </button>
                                <button type="button" class="secondary-btn" data-profile-tab-switch="view">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Notifications Modal ── -->
    <div class="action-modal manager-modal" id="notificationsModal" aria-hidden="true">
        <div class="container">
            <div class="modal-card modal-card-lg manager-modal__dialog manager-modal__dialog--lg">
                <div class="modal-head manager-modal__head">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="modal-head-icon"><i class="fa-solid fa-bell" style="color:var(--g-blue);"></i></div>
                        <h3>Notifications</h3>
                        <span class="manager-notification-btn__count" id="notifModalCount" style="position:static;<?= $unreadNotifs > 0 ? '' : 'display:none;' ?>"><?= manager_html((string)$unreadNotifs) ?></span>
                    </div>
                    <div class="manager-action-group">
                        <button type="button" class="secondary-btn" id="markAllReadBtn"><i class="fa-solid fa-check-double"></i> Mark All Read</button>
                        <button type="button" class="secondary-btn danger-outline-btn" id="clearAllNotifications"><i class="fa-solid fa-trash"></i> Clear All</button>
                        <button type="button" class="modal-close-btn manager-modal__close" data-close-modal="notificationsModal"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                </div>
                <div class="modal-body manager-modal__body">
                    <div id="managerNotificationsList" class="notification-list">
                        <?php if (!empty($notificationItems)): foreach ($notificationItems as $notif):
                                $notifId   = (string)manager_field($notif, ['notification_id', 'id'], '');
                                $reqId     = (string)manager_field($notif, ['request_id'], '');
                                $type      = (string)manager_field($notif, ['title', 'request_type', 'type'], 'Notification');
                                $message   = (string)manager_field($notif, ['message', 'description', 'note'], '-');
                                $tblCode   = (string)manager_field($notif, ['table_code'], '-');
                                $isRead    = (int)manager_field($notif, ['is_read'], 0);
                                $createdAt = (string)manager_field($notif, ['created_at'], '');
                        ?>
                                <div class="notification-item <?= $isRead ? '' : 'unread' ?>"
                                    data-notification-id="<?= manager_html($notifId) ?>"
                                    data-request-id="<?= manager_html($reqId) ?>">
                                    <div class="notif-dot"></div>
                                    <div class="notif-content">
                                        <strong><?= manager_html($type) ?></strong>
                                        <p><?= manager_html($message) ?></p>
                                        <div class="notif-meta">
                                            <?php if ($tblCode !== '-'): ?><span><i class="fa-solid fa-chair"></i> Table <?= manager_html($tblCode) ?></span><?php endif; ?>
                                            <?php if ($createdAt !== ''): ?><span><i class="fa-regular fa-clock"></i> <?= manager_html($createdAt) ?></span><?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="notif-actions">
                                        <?php if (!$isRead): ?><button type="button" class="notif-read-btn" data-action="mark_notification_read" data-notification-id="<?= manager_html($notifId) ?>" title="Mark read"><i class="fa-solid fa-check"></i></button><?php endif; ?>
                                        <button type="button" class="notif-del-btn" data-action="delete_notification" data-notification-id="<?= manager_html($notifId) ?>" title="Delete"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                </div>
                            <?php endforeach;
                        else: ?>
                            <div class="empty-state"><i class="fa-solid fa-bell-slash"></i>
                                <p>No notifications</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Order Action Modal ── -->
    <div class="action-modal manager-modal" id="orderActionModal" aria-hidden="true">
        <div class="container">
            <div class="modal-card modal-card-lg manager-modal__dialog manager-modal__dialog--lg">
                <div class="modal-head manager-modal__head">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="modal-head-icon"><i class="fa-solid fa-bowl-food" style="color:var(--g-blue);"></i></div>
                        <h3>Manage Order</h3>
                    </div>
                    <button type="button" class="modal-close-btn manager-modal__close" data-close-modal="orderActionModal"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body manager-modal__body">
                    <div class="manager-modal-summary" id="orderActionSummary">Order details will appear here.</div>
                    <form id="orderActionForm" autocomplete="off">
                        <input type="hidden" name="action" value="update_order_status">
                        <input type="hidden" name="order_id" value="">
                        <div class="manager-form-grid">
                            <div class="manager-field">
                                <span>Order Status</span>
                                <select name="status">
                                    <option value="new">New</option>
                                    <option value="accepted">Accepted</option>
                                    <option value="preparing">Preparing</option>
                                    <option value="ready">Ready</option>
                                    <option value="served">Served</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="manager-field">
                                <span>Priority</span>
                                <select name="priority">
                                    <option value="normal">Normal</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="manager-field manager-field--full">
                                <span>Manager Note</span>
                                <textarea name="manager_note" rows="2" placeholder="Optional note for kitchen / waiter..."></textarea>
                            </div>
                            <div class="manager-field manager-field--full" id="orderItemsArea" style="display:none;">
                                <span>Order Items</span>
                                <div id="orderItemsList" class="order-items-list"></div>
                            </div>
                        </div>
                        <div class="manager-modal-actions">
                            <button type="submit" class="primary-btn"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
                            <!-- New delete button for deleting an order permanently -->
                            <button type="button" class="secondary-btn danger-outline-btn" id="orderDeleteBtn"><i class="fa-solid fa-trash"></i> Delete</button>
                            <button type="button" class="secondary-btn" data-close-modal="orderActionModal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Request Action Modal ── -->
    <div class="action-modal manager-modal" id="requestActionModal" aria-hidden="true">
        <div class="container">
            <div class="modal-card manager-modal__dialog">
                <div class="modal-head manager-modal__head">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="modal-head-icon"><i class="fa-solid fa-bell-concierge" style="color:var(--g-blue);"></i></div>
                        <h3>Resolve Request</h3>
                    </div>
                    <button type="button" class="modal-close-btn manager-modal__close" data-close-modal="requestActionModal"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body manager-modal__body">
                    <div class="manager-modal-summary" id="requestActionSummary">Request details will appear here.</div>
                    <form id="requestActionForm" autocomplete="off">
                        <input type="hidden" name="action" value="resolve_request">
                        <input type="hidden" name="request_id" value="">
                        <div class="manager-form-grid">
                            <div class="manager-field">
                                <span>Status</span>
                                <select name="status" required>
                                    <option value="new">New</option>
                                    <option value="approved">Approved</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <div class="manager-field manager-field--full">
                                <span>Resolution Note</span>
                                <textarea name="resolution_note" rows="2" placeholder="Note for the waiter / customer..."></textarea>
                            </div>
                        </div>
                        <div class="manager-modal-actions">
                            <button type="submit" class="primary-btn"><i class="fa-solid fa-check"></i> Save</button>
                            <!-- New delete button for deleting a request permanently -->
                            <button type="button" class="secondary-btn danger-outline-btn" id="requestDeleteBtn"><i class="fa-solid fa-trash"></i> Delete</button>
                            <button type="button" class="secondary-btn" data-close-modal="requestActionModal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Bill Action Modal ── -->
    <div class="action-modal manager-modal" id="billActionModal" aria-hidden="true">
        <div class="container">
            <div class="modal-card modal-card-lg manager-modal__dialog manager-modal__dialog--lg">
                <div class="modal-head manager-modal__head">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="modal-head-icon"><i class="fa-solid fa-file-invoice-dollar" style="color:var(--g-blue);"></i></div>
                        <h3>Manage Bill</h3>
                    </div>
                    <button type="button" class="modal-close-btn manager-modal__close" data-close-modal="billActionModal"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body manager-modal__body">
                    <div class="manager-modal-summary" id="billActionSummary">Bill details will appear here.</div>
                    <div id="billBreakdownArea" style="display:none;" class="bill-breakdown-panel"></div>
                    <form id="billActionForm" autocomplete="off">
                        <input type="hidden" name="action" value="update_bill_status">
                        <input type="hidden" name="bill_id" value="">
                        <div class="manager-form-grid">
                            <div class="manager-field">
                                <span>Bill Status</span>
                                <select name="bill_status" required>
                                    <option value="new">New</option>
                                    <option value="approved">Approved</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <div class="manager-field">
                                <span>Payment Status</span>
                                <select name="payment_status" required>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="in_processing">In Processing</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                            <div class="manager-field">
                                <span>Payment Method</span>
                                <select name="payment_method">
                                    <option value="cash">Cash</option>
                                    <option value="upi">UPI</option>
                                    <option value="card">Card</option>
                                    <option value="online">Online</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="manager-field">
                                <span>Discount Amount</span>
                                <input type="number" name="discount_amount" value="0" min="0" step="0.01" placeholder="0.00">
                            </div>
                            <div class="manager-field">
                                <span>Tip Amount</span>
                                <input type="number" name="tip_amount" value="0" min="0" step="0.01" placeholder="0.00">
                            </div>
                            <div class="manager-field manager-field--full">
                                <span>Billing Note</span>
                                <textarea name="billing_note" rows="2" placeholder="Optional billing remark..."></textarea>
                            </div>
                        </div>
                        <div class="manager-modal-actions">
                            <button type="submit" class="primary-btn"><i class="fa-solid fa-circle-check"></i> Update Bill</button>
                            <!-- New delete button for deleting a bill permanently -->
                            <button type="button" class="secondary-btn danger-outline-btn" id="billDeleteBtn"><i class="fa-solid fa-trash"></i> Delete</button>
                            <button type="button" class="secondary-btn" data-close-modal="billActionModal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Staff Action Modal ── -->
    <div class="action-modal manager-modal" id="staffActionModal" aria-hidden="true">
        <div class="container">
            <div class="modal-card modal-card-lg manager-modal__dialog manager-modal__dialog--lg">
                <div class="modal-head manager-modal__head">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="modal-head-icon"><i class="fa-solid fa-user-group" style="color:var(--g-blue);"></i></div>
                        <h3 id="staffModalTitle">Staff</h3>
                    </div>
                    <button type="button" class="modal-close-btn manager-modal__close" data-close-modal="staffActionModal"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body manager-modal__body">
                    <div class="manager-modal-summary" id="staffActionSummary"></div>
                    <form id="staffActionForm" autocomplete="off">
                        <input type="hidden" name="action" id="staffActionType" value="create_staff">
                        <input type="hidden" name="target_user_id" id="staffTargetUserId" value="">
                        <input type="hidden" name="profile_pic_data" id="staffPicDataInput" value="">

                        <div class="manager-form-grid">
                            <!-- Profile Pic Upload -->
                            <div class="manager-field manager-field--full">
                                <span>Profile Photo</span>
                                <div class="img-upload-row">
                                    <div class="staff-avatar-upload" id="staffAvatarPreviewWrap">
                                        <img src="<?= manager_html(manager_asset_url('', 'default/default-avatar.svg')) ?>" alt="Staff" id="staffAvatarPreview">
                                    </div>
                                    <div class="img-upload-actions">
                                        <label class="secondary-btn" for="staffPicFileInput" style="cursor:pointer;">
                                            <i class="fa-solid fa-upload"></i> Choose Photo
                                        </label>
                                        <input type="file" id="staffPicFileInput" accept="image/*" style="display:none;">
                                        <small id="staffPicFileName">No file chosen</small>
                                        <button type="button" class="secondary-btn danger-outline-btn" id="staffPicClearBtn" style="display:none;"><i class="fa-solid fa-xmark"></i> Remove</button>
                                    </div>
                                </div>
                            </div>

                            <div class="manager-field">
                                <span>Full Name <span class="required-star">*</span></span>
                                <input type="text" name="full_name" id="staffFullName" placeholder="e.g. Rahul Varma" required>
                            </div>
                            <div class="manager-field">
                                <span>Employee Code</span>
                                <!-- <input type="text" name="employee_code" id="staffEmpCode" placeholder="e.g. WTR001"> -->
                                <input type="text" name="employee_code" id="staffEmpCode" placeholder="Auto generated after role selection, editable">
                            </div>
                            <div class="manager-field">
                                <span>Email <span class="required-star">*</span></span>
                                <input type="email" name="email" id="staffEmail" placeholder="staff@example.com" required>
                            </div>
                            <div class="manager-field">
                                <span>Phone</span>
                                <input type="tel" name="phone" id="staffPhone" placeholder="9XXXXXXXXX">
                            </div>
                            <div class="manager-field">
                                <span>Role <span class="required-star">*</span></span>
                                <select name="role_at_branch" id="staffRole">
                                    <option value="">Select role</option>
                                    <option value="waiter">Waiter</option>
                                    <option value="chef">Chef</option>
                                    <option value="pad">Pad</option>
                                    <option value="cashier">Cashier</option>
                                    <option value="manager">Manager</option>
                                    <option value="kitchen">Kitchen</option>
                                    <option value="staff">Staff</option>
                                </select>
                            </div>
                            <div class="manager-field">
                                <span>Access Status</span>
                                <select name="access_status" id="staffAccessStatus">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="manager-field">
                                <span>Shift Start</span>
                                <input type="time" name="shift_start_time" id="staffShiftStart">
                            </div>
                            <div class="manager-field">
                                <span>Shift End</span>
                                <input type="time" name="shift_end_time" id="staffShiftEnd">
                            </div>
                            <div class="manager-field">
                                <span>Aadhaar No.</span>
                                <input type="text" name="aadhar_no" id="staffAadhar" placeholder="12-digit aadhaar" maxlength="12">
                            </div>
                            <!-- <div class="manager-field manager-field--full">
                                <span>Assigned Tables (comma separated)</span>
                                <input type="text" name="assigned_table_codes" id="staffTables" placeholder="e.g. T01,T02,T03">
                            </div> -->
                            <div class="manager-field manager-field--full">
                                <span>Assigned Tables</span>

                                <input type="hidden" name="assigned_table_codes" id="staffTables" value="">

                                <div class="permissions-checkbox-grid" id="staffTablesCheckboxGrid">
                                    <?php
                                    $staffAssignableTables = $tables;
                                    usort($staffAssignableTables, static function ($a, $b) {
                                        $aCode = (string) manager_field($a, ['table_code', 'table_name'], '');
                                        $bCode = (string) manager_field($b, ['table_code', 'table_name'], '');

                                        preg_match('/(\d+)/', $aCode, $aMatch);
                                        preg_match('/(\d+)/', $bCode, $bMatch);

                                        $aNum = isset($aMatch[1]) ? (int) $aMatch[1] : PHP_INT_MAX;
                                        $bNum = isset($bMatch[1]) ? (int) $bMatch[1] : PHP_INT_MAX;

                                        return $aNum === $bNum
                                            ? strnatcasecmp($aCode, $bCode)
                                            : $aNum <=> $bNum;
                                    });
                                    ?>

                                    <?php if (!empty($staffAssignableTables)): foreach ($staffAssignableTables as $table): ?>
                                            <?php
                                            $tableCode = (string) manager_field($table, ['table_code', 'table_name'], '');
                                            if ($tableCode === '') continue;
                                            ?>
                                            <label class="perm-check">
                                                <input type="checkbox" class="staff-table-checkbox" value="<?= manager_html($tableCode) ?>">
                                                <?= manager_html($tableCode) ?>
                                            </label>
                                        <?php endforeach;
                                    else: ?>
                                        <small class="text-muted">No tables found. Add tables first.</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="manager-field manager-field--full">
                                <span>Permissions</span>
                                <div class="permissions-checkbox-grid">
                                    <label class="perm-check"><input type="checkbox" name="permissions[take_order]" value="1"> Take Order</label>
                                    <label class="perm-check"><input type="checkbox" name="permissions[edit_order]" value="1"> Edit Order</label>
                                    <label class="perm-check"><input type="checkbox" name="permissions[send_to_kitchen]" value="1"> Send to Kitchen</label>
                                    <label class="perm-check"><input type="checkbox" name="permissions[mark_served]" value="1"> Mark Served</label>
                                    <label class="perm-check"><input type="checkbox" name="permissions[generate_bill]" value="1"> Generate Bill</label>
                                    <label class="perm-check"><input type="checkbox" name="permissions[collect_payment]" value="1"> Collect Payment</label>
                                    <label class="perm-check"><input type="checkbox" name="permissions[discount]" value="1"> Apply Discount</label>
                                    <label class="perm-check"><input type="checkbox" name="permissions[table_transfer]" value="1"> Table Transfer</label>
                                </div>
                            </div>
                        </div>

                        <div class="manager-modal-actions">
                            <button type="submit" class="primary-btn" id="staffSaveBtn"><i class="fa-solid fa-floppy-disk"></i> Save Staff</button>
                            <!-- Delete button is now visible by default when editing a staff member -->
                            <button type="button" class="secondary-btn danger-outline-btn" id="staffDeleteBtn" data-action-type="delete_staff"><i class="fa-solid fa-trash"></i> Delete</button>
                            <button type="button" class="secondary-btn" data-close-modal="staffActionModal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Table Action Modal ── -->
    <div class="action-modal manager-modal" id="tableActionModal" aria-hidden="true">
        <div class="container">
            <div class="modal-card manager-modal__dialog">
                <div class="modal-head manager-modal__head">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="modal-head-icon"><i class="fa-solid fa-chair" style="color:var(--g-blue);"></i></div>
                        <h3 id="tableModalTitle">Table</h3>
                    </div>
                    <button type="button" class="modal-close-btn manager-modal__close" data-close-modal="tableActionModal"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body manager-modal__body">
                    <div class="manager-modal-summary" id="tableActionSummary"></div>
                    <form id="tableActionForm" autocomplete="off">
                        <input type="hidden" name="action" id="tableActionType" value="save_table">
                        <input type="hidden" name="table_id" id="tableTargetId" value="">
                        <div class="manager-form-grid">
                            <div class="manager-field">
                                <span>Table Code <span class="required-star">*</span></span>
                                <input type="text" name="table_code" id="tableCode" placeholder="e.g. T01" required>
                            </div>
                            <div class="manager-field">
                                <span>Table Number</span>
                                <input type="text" name="table_number" id="tableNumber" placeholder="e.g. 1">
                            </div>
                            <div class="manager-field">
                                <span>Capacity</span>
                                <input type="number" name="capacity" id="tableCapacity" min="1" max="50" placeholder="e.g. 4">
                            </div>
                            <div class="manager-field">
                                <span>Zone / Area</span>
                                <input type="text" name="zone" id="tableZone" placeholder="e.g. Indoor, Rooftop">
                            </div>
                            <div class="manager-field">
                                <span>Shape</span>
                                <select name="shape" id="tableShape">
                                    <option value="square">Square</option>
                                    <option value="round">Round</option>
                                    <option value="rectangle">Rectangle</option>
                                </select>
                            </div>
                            <div class="manager-field">
                                <span>Status</span>
                                <select name="status" id="tableStatus">
                                    <option value="available">Available</option>
                                    <option value="occupied">Occupied</option>
                                    <option value="reserved">Reserved</option>
                                    <option value="waiting">Waiting</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                            </div>
                        </div>
                        <div class="manager-modal-actions">
                            <button type="submit" class="primary-btn"><i class="fa-solid fa-floppy-disk"></i> Save Table</button>
                            <!-- Delete button for permanently removing a table -->
                            <button type="button" class="secondary-btn danger-outline-btn" id="tableDeleteBtn" data-action-type="delete_table"><i class="fa-solid fa-trash"></i> Delete</button>
                            <button type="button" class="secondary-btn" data-close-modal="tableActionModal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Menu Action Modal ── -->
    <div class="action-modal manager-modal" id="menuActionModal" aria-hidden="true">
        <div class="container">
            <div class="modal-card modal-card-lg manager-modal__dialog manager-modal__dialog--lg">
                <div class="modal-head manager-modal__head">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="modal-head-icon"><i class="fa-solid fa-utensils" style="color:var(--g-blue);"></i></div>
                        <h3 id="menuModalTitle">Menu Item</h3>
                    </div>
                    <button type="button" class="modal-close-btn manager-modal__close" data-close-modal="menuActionModal"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body manager-modal__body">
                    <div class="manager-modal-summary" id="menuActionSummary"></div>
                    <form id="menuActionForm" autocomplete="off">
                        <input type="hidden" name="action" id="menuActionType" value="create_menu_item">
                        <input type="hidden" name="menu_id" id="menuTargetId" value="">
                        <input type="hidden" name="item_image_data" id="menuImageDataInput" value="">
                        <input type="hidden" name="existing_image_url" id="menuExistingImageUrl" value="">

                        <div class="manager-form-grid">
                            <!-- Image Upload -->
                            <div class="manager-field manager-field--full">
                                <span>Item Image</span>
                                <div class="img-upload-row">
                                    <div class="menu-img-preview-wrap" id="menuImgPreviewWrap">
                                        <img src="<?= manager_html(manager_asset_url('', 'default/default-food.jpg')) ?>" alt="Preview" id="menuImgPreview">
                                    </div>
                                    <div class="img-upload-actions">
                                        <label class="secondary-btn" for="menuImageFileInput" style="cursor:pointer;">
                                            <i class="fa-solid fa-image"></i> Choose Image
                                        </label>
                                        <input type="file" id="menuImageFileInput" name="menu_image_file" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;">
                                        <small id="menuImageFileName">No file chosen</small>
                                        <button type="button" class="secondary-btn danger-outline-btn" id="menuImgClearBtn" style="display:none;"><i class="fa-solid fa-xmark"></i> Remove</button>
                                    </div>
                                </div>
                            </div>

                            <div class="manager-field">
                                <span>Item Name <span class="required-star">*</span></span>
                                <input type="text" name="item_name" id="menuItemName" placeholder="e.g. Butter Chicken" required>
                            </div>
                            <div class="manager-field">
                                <span>Category</span>
                                <select name="category_id" id="menuCategorySelect" class="manager-input" required>
                                    <option value="">Select category</option>
                                    <?php foreach ($menuCategories as $cat): ?>
                                        <?php
                                        $catId = (string)manager_field($cat, ['category_id', 'id'], '');
                                        $catName = (string)manager_field($cat, ['name', 'category_name'], '');
                                        ?>
                                        <?php if ($catId !== '' && $catName !== ''): ?>
                                            <option value="<?= manager_html($catId) ?>" data-category-name="<?= manager_html($catName) ?>">
                                                <?= manager_html($catName) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="category_name" id="menuCategoryNameInput">
                            </div>
                            <div class="manager-field">
                                <span>Food Type</span>
                                <select name="item_type" id="menuItemType">
                                    <option value="veg">Veg</option>
                                    <option value="non_veg">Non-Veg</option>
                                    <option value="drinks">Drinks</option>
                                    <option value="desserts">Desserts</option>
                                    <option value="snacks">Snacks</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="manager-field">
                                <span>Base Price (<?= manager_html($currSymbol) ?>)</span>
                                <input type="number" name="base_price" id="menuBasePrice" step="0.01" min="0" placeholder="0.00" value="0">
                            </div>
                            <div class="manager-field">
                                <span>Selling Price (<?= manager_html($currSymbol) ?>) <span class="required-star">*</span></span>
                                <input type="number" name="selling_price" id="menuSellingPrice" step="0.01" min="0" placeholder="0.00" value="0" required>
                            </div>
                            <div class="manager-field">
                                <span>Tax Rate (%)</span>
                                <input type="number" name="tax_rate" id="menuTaxRate" step="0.01" min="0" max="100" placeholder="0" value="0">
                            </div>
                            <div class="manager-field">
                                <span>Prep Time (mins)</span>
                                <input type="number" name="preparation_time_mins" id="menuPrepTime" min="0" placeholder="0" value="0">
                            </div>
                            <div class="manager-field">
                                <span>Available</span>
                                <select name="is_available" id="menuIsAvailable">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="manager-field">
                                <span>Stock Status</span>
                                <select name="stock_status" id="menuStockStatus">
                                    <option value="in_stock">In Stock</option>
                                    <option value="low_stock">Low Stock</option>
                                    <option value="out_of_stock">Out of Stock</option>
                                </select>
                            </div>
                            <div class="menu-flags-row manager-field manager-field--full">
                                <label class="perm-check"><input type="checkbox" name="is_veg" id="menuIsVeg" value="1"> Veg</label>
                                <label class="perm-check"><input type="checkbox" name="is_vegan" id="menuIsVegan" value="1"> Vegan</label>
                                <label class="perm-check"><input type="checkbox" name="is_spicy" id="menuIsSpicy" value="1"> Spicy</label>
                            </div>
                            <div class="manager-field manager-field--full">
                                <span>Description</span>
                                <textarea name="description" id="menuDescription" rows="2" placeholder="Short description of the menu item..."></textarea>
                            </div>
                        </div>
                        <div class="manager-modal-actions">
                            <button type="submit" class="primary-btn" id="menuSaveBtn"><i class="fa-solid fa-floppy-disk"></i> Save Item</button>
                            <!-- Delete button now visible by default for menu items -->
                            <button type="button" class="secondary-btn danger-outline-btn" id="menuDeleteBtn" data-action-type="delete_menu_item"><i class="fa-solid fa-trash"></i> Delete</button>
                            <button type="button" class="secondary-btn" data-close-modal="menuActionModal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Offer Banner Modal ── -->
    <div class="action-modal manager-modal" id="offerActionModal" aria-hidden="true">
        <div class="container">
            <div class="modal-card modal-card-lg manager-modal__dialog manager-modal__dialog--xl offer-crop-modal-card">
                <div class="modal-head manager-modal__head offer-crop-modal-head">
                    <div class="offer-crop-title-block">
                        <div class="modal-head-icon"><i class="fa-solid fa-tags" style="color:var(--g-blue);"></i></div>
                        <div>
                            <h3 id="offerModalTitle">Add Offer Banner</h3>
                            <p>Crop fixed website-ready offer images with premium 16:9 as the default fit.</p>
                        </div>
                    </div>
                    <button type="button" class="modal-close-btn manager-modal__close" data-close-modal="offerActionModal">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="modal-body manager-modal__body offer-crop-modal-body">
                    <div class="manager-modal-summary offer-crop-summary" id="offerActionSummary">
                        Upload a high-quality offer image and crop it for perfect customer-page fit.
                    </div>

                    <form id="offerActionForm" autocomplete="off">
                        <input type="hidden" name="action" id="offerActionType" value="create_offer_banner">
                        <input type="hidden" name="banner_id" id="offerTargetId" value="">
                        <input type="hidden" name="existing_image_path" id="offerExistingImagePath" value="">
                        <input type="hidden" name="original_image_size" id="offerOriginalImageSize" value="">

                        <div class="offer-editor-layout offer-editor-layout--pro">
                            <aside class="offer-editor-left offer-editor-panel">
                                <div class="offer-upload-panel offer-upload-panel--pro">
                                    <div class="offer-upload-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                                    <div class="offer-upload-copy">
                                        <strong>Offer source image</strong>
                                        <span>JPG, PNG, WEBP or GIF · min 1MB · max 25MB</span>
                                    </div>
                                    <label class="secondary-btn offer-upload-btn" for="offerImageFileInput">
                                        <i class="fa-solid fa-image"></i> Choose Image
                                    </label>
                                    <input type="file" id="offerImageFileInput" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;">
                                    <small id="offerImageFileName">No file chosen</small>
                                </div>

                                <div class="offer-crop-meta-card">
                                    <span>Selected Export</span>
                                    <strong id="offerCropRatioLabel">16:9 Website</strong>
                                    <p id="offerCropSizeLabel">1920 × 1080 px export</p>
                                    <small id="offerCropMeta">Choose an image to unlock crop, zoom, rotate and preview.</small>
                                </div>

                                <div class="offer-live-preview-wrap offer-live-preview-wrap--pro">
                                    <div class="offer-preview-topline">
                                        <span class="offer-preview-label">Live Website Preview</span>
                                        <small>Final cropped output</small>
                                    </div>
                                    <div class="offer-live-preview-box offer-live-preview-box--wide">
                                        <img src="<?= manager_html(manager_asset_url('', 'default/default-food.jpg')) ?>" alt="Offer live preview" id="offerExistingPreview">
                                    </div>
                                </div>
                            </aside>

                            <section class="offer-editor-right offer-crop-workbench">
                                <div class="offer-crop-topbar">
                                    <div>
                                        <span class="card-kicker">Advanced Crop Studio</span>
                                        <h4>Fixed ratio crop for website banners</h4>
                                        <p>Drag to reposition. Use the slider or mouse wheel to zoom. Rotate or flip without losing the crop frame.</p>
                                    </div>

                                    <div class="offer-crop-tool-actions" aria-label="Crop actions">
                                        <button type="button" class="offer-tool-btn" id="offerCropRotateLeftBtn" title="Rotate left">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                        <button type="button" class="offer-tool-btn" id="offerCropRotateRightBtn" title="Rotate right">
                                            <i class="fa-solid fa-rotate-right"></i>
                                        </button>
                                        <button type="button" class="offer-tool-btn" id="offerCropFlipBtn" title="Flip horizontal">
                                            <i class="fa-solid fa-arrows-left-right"></i>
                                        </button>
                                        <button type="button" class="offer-tool-btn offer-tool-btn--reset" id="offerCropResetBtn" title="Reset crop">
                                            <i class="fa-solid fa-arrow-rotate-left"></i> Reset
                                        </button>
                                    </div>
                                </div>

                                <div class="offer-crop-toolbar" aria-label="Fixed crop ratios">
                                    <button type="button" class="offer-ratio-btn active" data-offer-ratio="1.777778">
                                        <strong>16:9</strong><span>Website Hero</span>
                                    </button>
                                    <button type="button" class="offer-ratio-btn" data-offer-ratio="2.333333">
                                        <strong>21:9</strong><span>Ultra Wide</span>
                                    </button>
                                    <button type="button" class="offer-ratio-btn" data-offer-ratio="4">
                                        <strong>4:1</strong><span>Hero Strip</span>
                                    </button>
                                    <button type="button" class="offer-ratio-btn" data-offer-ratio="3">
                                        <strong>3:1</strong><span>Promo Wide</span>
                                    </button>
                                    <button type="button" class="offer-ratio-btn" data-offer-ratio="1">
                                        <strong>1:1</strong><span>Square</span>
                                    </button>
                                </div>

                                <div class="offer-crop-stage offer-crop-stage--pro" id="offerCropStage">
                                    <canvas id="offerCropCanvas" width="1280" height="720"></canvas>
                                    <div class="offer-crop-placeholder" id="offerCropPlaceholder">
                                        <i class="fa-solid fa-crop-simple"></i>
                                        <span>Select an image to start cropping</span>
                                        <small>Default output is 16:9 for modern website banners.</small>
                                    </div>
                                    <div class="offer-crop-drag-hint" id="offerCropDragHint" style="display:none;">
                                        <i class="fa-solid fa-hand"></i> Drag to pan &nbsp;·&nbsp;
                                        <i class="fa-solid fa-magnifying-glass-plus"></i> Scroll / pinch to zoom
                                    </div>
                                </div>

                                <div class="offer-crop-controlbar">
                                    <div class="offer-zoom-control">
                                        <span><i class="fa-solid fa-magnifying-glass-minus"></i></span>
                                        <input type="range" id="offerCropZoomInput" min="1" max="5" step="0.01" value="1" aria-label="Offer crop zoom">
                                        <span><i class="fa-solid fa-magnifying-glass-plus"></i></span>
                                    </div>
                                    <strong id="offerCropZoomValue">100%</strong>
                                </div>
                            </section>
                        </div>

                        <div class="manager-form-grid offer-schedule-grid">
                            <div class="manager-field">
                                <span>Sort Order</span>
                                <input type="number" name="sort_order" id="offerSortOrder" min="1" value="1">
                            </div>

                            <div class="manager-field">
                                <span>Status</span>
                                <select name="is_active" id="offerIsActive">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <div class="manager-field">
                                <span>Starts At</span>
                                <input type="datetime-local" name="starts_at" id="offerStartsAt">
                            </div>

                            <div class="manager-field">
                                <span>Ends At</span>
                                <input type="datetime-local" name="ends_at" id="offerEndsAt">
                            </div>
                        </div>

                        <div class="manager-modal-actions offer-modal-actions">
                            <button type="submit" class="primary-btn" id="offerSaveBtn">
                                <i class="fa-solid fa-floppy-disk"></i> Save Cropped Offer
                            </button>
                            <button type="button" class="secondary-btn danger-outline-btn" id="offerDeleteBtn" style="display:none;">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                            <button type="button" class="secondary-btn" data-close-modal="offerActionModal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    

    <!-- ── Confirm / Alert Modal ── -->
    <div class="action-modal manager-modal" id="confirmModal" aria-hidden="true" style="z-index:9999;">
        <div class="container">
            <div class="modal-card manager-modal__dialog" style="max-width:400px;">
                <div class="modal-head manager-modal__head">
                    <h3 id="confirmModalTitle">Confirm Action</h3>
                    <button type="button" class="modal-close-btn manager-modal__close" data-close-modal="confirmModal"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body manager-modal__body">
                    <p id="confirmModalMessage" style="font-size:14px;color:#374151;line-height:1.6;"></p>
                    <div class="manager-modal-actions" style="margin-top:20px;">
                        <button type="button" class="primary-btn danger-btn" id="confirmModalOkBtn">Confirm</button>
                        <button type="button" class="secondary-btn" data-close-modal="confirmModal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.ManagerPageData = <?= manager_json($managerPageData) ?>;
    </script>
    <script src="<?= manager_html(manager_base_url_safe('RestaurantAssets/js/manager.js')) ?>"></script>
</body>

</html>