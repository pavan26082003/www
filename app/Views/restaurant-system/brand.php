<?php
if (!function_exists('brand_e')) {
    function brand_e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('brand_base_url')) {
    function brand_base_url($path = '')
    {
        $path = ltrim((string) $path, '/');

        if (function_exists('base_url')) {
            return base_url($path);
        }

        return '/' . $path;
    }
}

if (!function_exists('brand_site_url')) {
    function brand_site_url($path = '')
    {
        $path = ltrim((string) $path, '/');

        if (function_exists('site_url')) {
            return site_url($path);
        }

        return '/' . $path;
    }
}




// Hotel Delight — Brand Owner Dashboard (PHP version)
$kpis = [
    ['label' => 'Today Revenue', 'value' => '₹84,650', 'trend' => 18.6, 'up' => true, 'icon' => 'indian-rupee', 'tone' => 'success'],
    ['label' => 'Today Orders', 'value' => '278', 'trend' => 12.4, 'up' => true, 'icon' => 'shopping-bag', 'tone' => 'primary'],
    ['label' => 'Open Bills', 'value' => '32', 'trend' => -8.6, 'up' => false, 'icon' => 'file-text', 'tone' => 'amber'],
    ['label' => 'Pending Payments', 'value' => '₹12,430', 'trend' => -6.2, 'up' => false, 'icon' => 'credit-card', 'tone' => 'purple'],
    ['label' => 'Active Tables', 'value' => '28 / 64', 'helper' => '43% occupied', 'icon' => 'armchair', 'tone' => 'primary'],
    ['label' => 'Staff Online', 'value' => '18 / 24', 'helper' => '75% on duty', 'icon' => 'users', 'tone' => 'teal', 'helperUp' => true],
    ['label' => 'Kitchen Queue', 'value' => '14', 'helper' => 'Avg. 18 min wait', 'icon' => 'chef-hat', 'tone' => 'danger'],
    ['label' => 'Customer Requests', 'value' => '7', 'helper' => '2 awaiting action', 'icon' => 'bell-ring', 'tone' => 'amber'],
];
$nav = [
    ['id' => 'analytics', 'label' => 'Analytics', 'icon' => 'bar-chart-3'],
    ['id' => 'orders', 'label' => 'Orders & Bills', 'icon' => 'receipt-text'],
    ['id' => 'staff', 'label' => 'Staff', 'icon' => 'users'],
    ['id' => 'brand', 'label' => 'Brand Details', 'icon' => 'building-2'],
    ['id' => 'cx', 'label' => 'Customer Experience Studio', 'icon' => 'sparkles'],
    ['id' => 'Branch', 'label' => 'Branch Management', 'icon' => 'branch'],
    ['id' => 'settings', 'label' => 'Settings', 'icon' => 'settings'],

];
$items = [
    ['name' => 'Butter Chicken', 'qty' => 48, 'rev' => '₹9,120', 'emoji' => '🍛'],
    ['name' => 'Paneer Tikka', 'qty' => 42, 'rev' => '₹7,140', 'emoji' => '🧆'],
    ['name' => 'Veg Biryani', 'qty' => 38, 'rev' => '₹6,080', 'emoji' => '🍚'],
    ['name' => 'Masala Dosa', 'qty' => 35, 'rev' => '₹4,725', 'emoji' => '🥞'],
    ['name' => 'Chocolate Brownie', 'qty' => 32, 'rev' => '₹3,840', 'emoji' => '🍫'],
];
$staff = [
    ['name' => 'Ravi Kumar', 'role' => 'Captain', 'status' => 'Online', 'orders' => 42],
    ['name' => 'Sneha Iyer', 'role' => 'Cashier', 'status' => 'Online', 'orders' => 36],
    ['name' => 'Arjun Patel', 'role' => 'Server', 'status' => 'Online', 'orders' => 31],
    ['name' => 'Neha Singh', 'role' => 'Server', 'status' => 'Break', 'orders' => 18],
    ['name' => 'Mohit Verma', 'role' => 'Kitchen', 'status' => 'Online', 'orders' => null],
];
$branches = [
    ['name' => 'Andheri (West)', 'rev' => '₹34,250', 'orders' => 112, 'rating' => 4.6],
    ['name' => 'Bandra (West)', 'rev' => '₹28,430', 'orders' => 94, 'rating' => 4.4],
    ['name' => 'Ghatkopar (E)', 'rev' => '₹14,850', 'orders' => 48, 'rating' => 4.5],
    ['name' => 'Thane (W)', 'rev' => '₹7,110', 'orders' => 24, 'rating' => 4.3],
];
$activity = [
    ['icon' => '✅', 'label' => 'Order #ORD-2198 completed', 'sub' => '₹2,190', 'time' => '11:22 AM', 'tone' => 'success'],
    ['icon' => '₹', 'label' => 'Payment received', 'sub' => '₹2,460', 'time' => '11:20 AM', 'tone' => 'primary'],
    ['icon' => '🛒', 'label' => 'New order #ORD-2197', 'sub' => '₹320', 'time' => '11:18 AM', 'tone' => 'amber'],
    ['icon' => '🪑', 'label' => 'Table T-12 opened', 'sub' => '', 'time' => '11:15 AM', 'tone' => 'purple'],
    ['icon' => '👤', 'label' => 'Staff Ravi Kumar logged in', 'sub' => '', 'time' => '11:10 AM', 'tone' => 'teal'],
];
$alerts = [
    ['label' => '2 bills pending > 30 min', 'count' => 2, 'tone' => 'amber', 'icon' => '⏱'],
    ['label' => '1 payment failed', 'count' => 1, 'tone' => 'danger', 'icon' => '✕'],
    ['label' => '7 customer requests', 'count' => 7, 'tone' => 'primary', 'icon' => '🔔'],
    ['label' => 'Low stock: 5 items', 'count' => 5, 'tone' => 'purple', 'icon' => '📦'],
    ['label' => '1 table dirty', 'count' => 1, 'tone' => 'teal', 'icon' => '🧹'],
];




















$ob_kpis = [
    ['label' => 'Live Orders', 'value' => '24', 'trend' => '14.3% vs yesterday', 'dir' => 'up', 'tone' => 'blue', 'icon' => 'cart'],
    ['label' => 'Open Bills', 'value' => '32', 'trend' => '6.7% vs yesterday', 'dir' => 'up', 'tone' => 'orange', 'icon' => 'doc'],
    ['label' => 'Unpaid Bills', 'value' => '18', 'trend' => '12.5% vs yesterday', 'dir' => 'up', 'tone' => 'red', 'icon' => 'doc-x'],
    ['label' => 'In Processing Payments', 'value' => '12', 'trend' => '9.1% vs yesterday', 'dir' => 'up', 'tone' => 'purple', 'icon' => 'card'],
    ['label' => 'Paid Today', 'value' => '₹72,220', 'trend' => '18.6% vs yesterday', 'dir' => 'up', 'tone' => 'green', 'icon' => 'check'],
    ['label' => 'Average Bill Value', 'value' => '₹1,245', 'trend' => '2.4% vs yesterday', 'dir' => 'down', 'tone' => 'indigo', 'icon' => 'receipt'],
    ['label' => 'Delayed Orders', 'value' => '6', 'trend' => '2 vs yesterday', 'dir' => 'up', 'tone' => 'orange', 'icon' => 'clock'],
    ['label' => 'Tables Awaiting Bill', 'value' => '9', 'trend' => '1 vs yesterday', 'dir' => 'up', 'tone' => 'teal', 'icon' => 'table'],
];

$ob_live_orders = [
    ['ORD-001245', 'T-12', '5', 'New', 'Received', 'Ravi K.', '₹1,240', '11:21 AM'],
    ['ORD-001244', 'T-07', '3', 'Accepted', 'In Progress', 'Sneha I.', '₹820', '11:18 AM'],
    ['ORD-001243', 'T-09', '4', 'Preparing', 'Cooking', 'Arjun P.', '₹1,560', '11:16 AM'],
    ['ORD-001242', 'T-03', '6', 'Ready', 'Ready to Serve', 'Neha S.', '₹2,180', '11:12 AM'],
    ['ORD-001241', 'T-15', '2', 'Served', 'Served', 'Mohit V.', '₹640', '11:05 AM'],
    ['ORD-001240', 'T-05', '3', 'Accepted', 'In Progress', 'Captain', '₹990', '11:03 AM'],
    ['ORD-001239', 'T-11', '4', 'Preparing', 'Cooking', 'Vega B.', '₹1,480', '10:58 AM'],
    ['ORD-001238', 'T-02', '7', 'Ready', 'Ready to Serve', 'Ravi K.', '₹2,760', '10:52 AM'],
];

$ob_bill_queue = [
    ['BILL-002318', 'T-12', '₹1,240', 'Pending Approval', 'Approve'],
    ['BILL-002317', 'T-09', '₹1,560', 'Pending Approval', 'Approve'],
    ['BILL-002316', 'T-03', '₹2,180', 'Pending Approval', 'Review'],
    ['BILL-002315', 'T-15', '₹640', 'Pending Discount', 'Review'],
    ['BILL-002314', 'T-07', '₹820', 'Pending Approval', 'Approve'],
];

$ob_pay_verify = [
    ['PAY-009112', 'T-12', 'UPI', '₹1,240', 'Pending', 'Verify'],
    ['PAY-009111', 'T-07', 'Cash', '₹820', 'Pending', 'Verify'],
    ['PAY-009110', 'T-03', 'UPI', '₹2,180', 'Pending', 'Verify'],
    ['PAY-009109', 'T-09', 'Card', '₹1,560', 'Pending', 'Verify'],
    ['PAY-009108', 'T-15', 'Cash', '₹640', 'Verified', 'View'],
];

$ob_bills = [
    ['BILL-002318', 'ORD-001245', 'T-12', 'New', 'Unpaid', '—', '₹1,240', '11:22 AM'],
    ['BILL-002317', 'ORD-001243', 'T-09', 'New', 'In Processing', 'UPI', '₹1,560', '11:20 AM'],
    ['BILL-002316', 'ORD-001242', 'T-03', 'Approved', 'Paid', 'Cash', '₹2,180', '11:15 AM'],
    ['BILL-002315', 'ORD-001241', 'T-15', 'Approved', 'Paid', 'Card', '₹640', '11:08 AM'],
    ['BILL-002314', 'ORD-001240', 'T-07', 'New', 'Unpaid', '—', '₹820', '11:04 AM'],
    ['BILL-002313', 'ORD-001239', 'T-11', 'Approved', 'Paid', 'UPI', '₹1,480', '10:59 AM'],
    ['BILL-002312', 'ORD-001238', 'T-02', 'Closed', 'Paid', 'Cash', '₹2,760', '10:54 AM'],
    ['BILL-002311', 'ORD-001237', 'T-08', 'New', 'Unpaid', '—', '₹990', '10:48 AM'],
];

$ob_activity = [
    ['approve', 'Bill BILL-002316 approved', '₹2,180 • T-03', '11:15 AM'],
    ['upi', 'Payment received (UPI)', '₹1,560 • T-09', '11:12 AM'],
    ['approve', 'Bill BILL-002315 approved', '₹640 • T-15', '11:08 AM'],
    ['cash', 'Payment received (Cash)', '₹820 • T-07', '11:04 AM'],
    ['create', 'Bill BILL-002311 created', '₹990 • T-08', '10:48 AM'],
];

$ob_alerts = [
    ['danger', 'Orders delayed > 30 min', 6],
    ['warn', 'Payments pending verification', 12],
    ['warn', 'Bills awaiting approval', 5],
    ['info', 'Customer bill requests', 7],
    ['warn', 'Discount approvals pending', 3],
];

function ob_pill($text)
{
    $key = strtolower(str_replace(' ', '-', $text));
    return '<span class="status-pill status-' . $key . '">' . $text . '</span>';
}
function ob_icon($name)
{
    $svgs = [
        'cart'   => '<path d="M3 5h2l2 12h11l2-9H7"/><circle cx="9" cy="20" r="1.5"/><circle cx="17" cy="20" r="1.5"/>',
        'doc'    => '<path d="M7 3h8l4 4v14H7z"/><path d="M9 12h8M9 16h6"/>',
        'doc-x'  => '<path d="M7 3h8l4 4v14H7z"/><path d="M10 12l4 4M14 12l-4 4"/>',
        'card'   => '<rect x="3" y="6" width="18" height="12" rx="2"/><path d="M3 10h18"/>',
        'check'  => '<circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/>',
        'receipt' => '<path d="M6 3h12v18l-3-2-3 2-3-2-3 2z"/><path d="M9 8h6M9 12h6M9 16h4"/>',
        'clock'  => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'table'  => '<path d="M3 9h18M5 9v10M19 9v10M3 9l2-4h14l2 4"/>',
    ];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . ($svgs[$name] ?? '') . '</svg>';
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Hotel Delight — Brand Owner Dashboard</title>
    <meta name="description" content="Premium restaurant SaaS dashboard for live brand performance, operations, and financial overview." />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= brand_e(brand_base_url('RestaurantAssets/css/brand.css')) ?>">
</head>

<body>
    <section class="brand-section">
        <div class="brand-container">
            <div class="brand-main">

                <!-- ============ SIDEBAR ============ -->
                <div id="sidebar-overlay" class="sidebar-overlay"></div>
                <aside class="brand-sidebar" id="brand-sidebar">
                    <div class="card-surface sidebar-inner">
                        <div class="brand-user-card">
                            <div class="banner">
                                <img src="assets/restaurant-banner.jpg" alt="Hotel Delight ambience" loading="lazy" />
                                <div class="banner-overlay"></div>
                                <button class="sidebar-close" id="sidebar-close" aria-label="Close"><i data-lucide="x"></i></button>
                            </div>
                            <div class="user-body">
                                <div class="logo-wrap"><img src="assets/hd-logo.png" alt="Hotel Delight logo" /></div>
                                <h2 class="brand-name">Hotel Delight</h2>
                                <div class="owner-row">
                                    <div class="avatar">PS</div>
                                    <div class="owner-meta">
                                        <div class="name">Praful Shah</div>
                                        <div class="sub">Owner / Admin</div>
                                    </div>
                                </div>
                                <div class="pills">
                                    <span class="status-pill pill-success"><span class="dot dot-success"></span> Active</span>
                                    <span class="status-pill pill-primary">Starter</span>
                                </div>
                                <div class="meta-grid">
                                    <div class="meta-cell">
                                        <div class="k">Currency</div>
                                        <div class="v">INR (₹)</div>
                                    </div>
                                    <div class="meta-cell">
                                        <div class="k">Timezone</div>
                                        <div class="v">Asia/Kolkata</div>
                                    </div>
                                </div>
                                <div class="email">✉ praful.shah@hoteldelight.in</div>
                            </div>
                        </div>

                        <nav class="brand-sidebar-menu">
                            <?php foreach ($nav as $i => $n): ?>
                                <button class="brand-nav-item <?= $i === 0 ? 'active' : '' ?>" data-id="<?= $n['id'] ?>">
                                    <i data-lucide="<?= $n['icon'] ?>"></i>
                                    <span><?= $n['label'] ?></span>
                                    <?php if ($i === 0): ?><span class="active-dot"></span><?php endif; ?>
                                </button>
                            <?php endforeach; ?>
                        </nav>

                        <div class="help-wrap">
                            <div class="help-card">
                                <div class="help-row">
                                    <div class="help-icon"><i data-lucide="life-buoy"></i></div>
                                    <div>
                                        <div class="help-title">Need help?</div>
                                        <div class="help-sub">24/7 priority support</div>
                                    </div>
                                </div>
                                <button class="help-btn">Contact support</button>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- ============ RIGHT MAIN ============ -->
                <main class="brand-rightmain">

                    <!-- TOPBAR -->
                    <div class="brand-topbar">
                        <div class="card-surface topbar-inner">
                            <button class="menu-btn" id="menu-btn" aria-label="Open menu"><i data-lucide="menu"></i></button>
                            <div class="search">
                                <i data-lucide="search" class="search-icon"></i>
                                <input type="text" placeholder="Search orders, bills, customers, staff…" />
                            </div>
                            <div class="topbar-actions">
                                <button class="icon-btn" aria-label="Notifications">
                                    <i data-lucide="bell"></i><span class="badge">5</span>
                                </button>
                                <button class="icon-btn" id="sound-btn" aria-label="Toggle sound"><i data-lucide="volume-2"></i></button>
                                <button class="icon-btn" id="refresh-btn" aria-label="Refresh"><i data-lucide="refresh-cw"></i></button>
                                <button class="icon-btn" id="theme-btn" aria-label="Toggle theme"><i data-lucide="moon"></i></button>
                                <div class="clock">
                                    <div class="time" id="clock-time">--:--</div>
                                    <div class="date" id="clock-date">--</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--  CONTENT -->
                    <div class="brand-content">
                        <!-- Header -->
                        <div class="dashboard-header">
                            <div>
                                <h1>Dashboard</h1>
                                <p>Live brand performance, operations, and financial overview</p>
                            </div>
                            <div class="header-actions">
                                <div class="seg" id="range-seg">
                                    <button data-r="today" class="active">Today</button>
                                    <button data-r="week">Week</button>
                                    <button data-r="month">Month</button>
                                    <button data-r="custom"><i data-lucide="calendar"></i> Custom</button>
                                </div>
                                <div class="custom-range card-surface" id="custom-range" hidden>
                                    <input type="date" id="from-date" />
                                    <span>→</span>
                                    <input type="date" id="to-date" />
                                </div>
                                <button class="card-surface chip">🏬 All Branches <i data-lucide="chevron-down"></i></button>
                                <div class="updated">
                                    <span class="dot dot-success"></span> Last updated: 11:24 AM
                                    <button class="mini-btn"><i data-lucide="refresh-cw"></i></button>
                                </div>
                            </div>
                        </div>

                        <!-- KPI Grid -->
                        <div class="kpi-grid">
                            <?php foreach ($kpis as $k): ?>
                                <div class="kpi-card card-surface hover-lift">
                                    <div class="kpi-row">
                                        <div class="kpi-icon tone-<?= $k['tone'] ?>"><i data-lucide="<?= $k['icon'] ?>"></i></div>
                                        <div class="kpi-body">
                                            <div class="kpi-label"><?= $k['label'] ?></div>
                                            <div class="kpi-value"><?= $k['value'] ?></div>
                                            <?php if (isset($k['trend'])): ?>
                                                <div class="kpi-trend <?= $k['up'] ? 'up' : 'down' ?>">
                                                    <i data-lucide="<?= $k['up'] ? 'trending-up' : 'trending-down' ?>"></i>
                                                    <?= abs($k['trend']) ?>% vs yesterday
                                                </div>
                                            <?php else: ?>
                                                <div class="kpi-helper <?= !empty($k['helperUp']) ? 'up' : '' ?>"><?= $k['helper'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Insight grid -->
                        <div class="insight-grid">
                            <div class="dashboard-card card-surface chart-card">
                                <div class="chart-head">
                                    <div>
                                        <h3>Revenue Performance</h3>
                                        <p>Today vs yesterday hourly trend</p>
                                    </div>
                                    <div class="chart-controls">
                                        <div class="seg sm" id="metric-seg">
                                            <button class="active" data-m="Revenue">Revenue</button>
                                            <button data-m="Orders">Orders</button>
                                            <button data-m="Items Sold">Items Sold</button>
                                        </div>
                                        <div class="seg sm" id="chart-seg">
                                            <button class="active" data-c="line">Line</button>
                                            <button data-c="bar">Bar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart-wrap"><canvas id="revChart"></canvas></div>
                                <div class="chart-legend">
                                    <span><span class="lg-line solid"></span> Today</span>
                                    <span><span class="lg-line dashed"></span> Yesterday</span>
                                </div>
                            </div>

                            <div class="insight-side">
                                <div class="dashboard-card card-surface">
                                    <h3>Payment Overview</h3>
                                    <div class="pay-row">
                                        <div class="pay-chart"><canvas id="payChart"></canvas></div>
                                        <div class="pay-list">
                                            <div class="pay-item"><span><span class="dot" style="background:var(--success)"></span>Paid</span><span class="vals"><span class="muted">₹72,220</span><b>85%</b></span></div>
                                            <div class="pay-item"><span><span class="dot" style="background:var(--warning)"></span>Pending</span><span class="vals"><span class="muted">₹12,430</span><b>15%</b></span></div>
                                            <div class="pay-item"><span><span class="dot" style="background:var(--danger)"></span>Failed</span><span class="vals"><span class="muted">₹0</span><b>0%</b></span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dashboard-card card-surface">
                                    <h3>Orders & Bills Health</h3>
                                    <div class="mini-grid">
                                        <div class="mini">
                                            <div class="k">Total Orders</div>
                                            <div class="v">278</div>
                                            <div class="t up">+12.4%</div>
                                        </div>
                                        <div class="mini">
                                            <div class="k">Completed</div>
                                            <div class="v">246</div>
                                            <div class="t up">88.5%</div>
                                        </div>
                                        <div class="mini">
                                            <div class="k">Open Bills</div>
                                            <div class="v">32</div>
                                            <div class="t down">-8.6%</div>
                                        </div>
                                        <div class="mini">
                                            <div class="k">Avg. Bill Value</div>
                                            <div class="v">₹305</div>
                                            <div class="t up">+5.3%</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dashboard-card card-surface">
                                    <h3>System Status</h3>
                                    <div class="pulse-list">
                                        <?php foreach ([['POS Terminals', '8/8'], ['Kitchen Display', '3/3'], ['Payment Gateway', '100%'], ['Internet', '35 ms']] as $p): ?>
                                            <div class="pulse">
                                                <div>
                                                    <div class="lbl"><?= $p[0] ?></div>
                                                    <div class="sub"><?= $p[1] ?></div>
                                                </div>
                                                <span class="status-online"><span class="dot dot-success"></span> Online</span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lower grid -->
                        <div class="lower-grid">
                            <!-- Top Items -->
                            <div class="dashboard-card table-card card-surface">
                                <div class="card-head">
                                    <h3>Top Selling Items</h3>
                                </div>
                                <div class="thead grid-12">
                                    <div class="c7">Item</div>
                                    <div class="c2 r">Qty</div>
                                    <div class="c3 r">Revenue</div>
                                </div>
                                <?php foreach ($items as $idx => $i): ?>
                                    <div class="trow grid-12">
                                        <div class="c7 row-flex"><span class="rank"><?= $idx + 1 ?></span><span class="emoji"><?= $i['emoji'] ?></span><span class="name"><?= $i['name'] ?></span></div>
                                        <div class="c2 r"><?= $i['qty'] ?></div>
                                        <div class="c3 r b"><?= $i['rev'] ?></div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="card-foot">Based on items sold today</div>
                            </div>

                            <!-- Staff -->
                            <div class="dashboard-card table-card card-surface">
                                <div class="card-head">
                                    <h3>Staff & Operations</h3>
                                </div>
                                <div class="thead grid-12">
                                    <div class="c5">Staff</div>
                                    <div class="c3">Role</div>
                                    <div class="c2">Status</div>
                                    <div class="c2 r">Orders</div>
                                </div>
                                <?php foreach ($staff as $s): $i = implode('', array_map(fn($n) => $n[0], explode(' ', $s['name']))); ?>
                                    <div class="trow grid-12">
                                        <div class="c5 row-flex"><span class="staff-avatar"><?= $i ?></span><span class="name"><?= $s['name'] ?></span></div>
                                        <div class="c3 sub"><?= $s['role'] ?></div>
                                        <div class="c2"><span class="pill-mini <?= $s['status'] === 'Online' ? 'pill-success' : 'pill-warning' ?>"><?= $s['status'] ?></span></div>
                                        <div class="c2 r"><?= $s['orders'] ?? '—' ?></div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="card-foot">18 of 24 staff online</div>
                            </div>

                            <!-- Branch -->
                            <div class="dashboard-card table-card card-surface">
                                <div class="card-head">
                                    <h3>Branch Performance</h3>
                                </div>
                                <div class="thead grid-12">
                                    <div class="c5">Branch</div>
                                    <div class="c3 r">Revenue</div>
                                    <div class="c2 r">Orders</div>
                                    <div class="c2 r">Rating</div>
                                </div>
                                <?php foreach ($branches as $b): ?>
                                    <div class="trow grid-12">
                                        <div class="c5 b"><?= $b['name'] ?></div>
                                        <div class="c3 r b"><?= $b['rev'] ?></div>
                                        <div class="c2 r"><?= $b['orders'] ?></div>
                                        <div class="c2 r rating">★ <?= $b['rating'] ?></div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="card-foot">All branches • Today</div>
                            </div>

                            <!-- Activity -->
                            <div class="dashboard-card table-card card-surface">
                                <div class="card-head">
                                    <h3>Recent Activity</h3><button class="link">View all</button>
                                </div>
                                <?php foreach ($activity as $a): ?>
                                    <div class="activity-row">
                                        <span class="act-icon tone-<?= $a['tone'] ?>"><?= $a['icon'] ?></span>
                                        <div class="grow">
                                            <div class="lbl"><?= $a['label'] ?></div><?php if ($a['sub']): ?><div class="sub"><?= $a['sub'] ?></div><?php endif; ?>
                                        </div>
                                        <div class="time"><?= $a['time'] ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Alerts -->
                            <div class="dashboard-card table-card card-surface">
                                <div class="card-head">
                                    <h3>Pending Alerts</h3><button class="link">View all</button>
                                </div>
                                <?php foreach ($alerts as $a): ?>
                                    <div class="activity-row">
                                        <span class="act-icon tone-<?= $a['tone'] ?>"><?= $a['icon'] ?></span>
                                        <div class="grow lbl"><?= $a['label'] ?></div>
                                        <span class="count tone-<?= $a['tone'] ?>"><?= $a['count'] ?></span>
                                    </div>
                                <?php endforeach; ?>
                                <div class="card-foot">Stay on top of important alerts</div>
                            </div>
                        </div>
                    </div>



                    <section class="orders-module">

                        <!-- Header -->
                        <header class="orders-header">
                            <div>
                                <h1>Orders &amp; Bills</h1>
                                <p>Live order flow, bill approvals, payment verification, and transaction control.</p>
                            </div>
                            <div class="orders-header-actions">
                                <button class="btn btn-ghost"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16" />
                                    </svg>Export</button>
                                <button class="btn btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 5v14M5 12h14" />
                                    </svg>Create Bill</button>
                            </div>
                        </header>

                        <!-- Filters -->
                        <div class="orders-filter-bar">
                            <div class="orders-tabs" data-tabs>
                                <button class="tab active" data-tab="all">All</button>
                                <button class="tab" data-tab="live">Live Orders</button>
                                <button class="tab" data-tab="bills">Bills</button>
                                <button class="tab" data-tab="payments">Payments</button>
                                <button class="tab" data-tab="history">History</button>
                            </div>
                            <div class="orders-filter-right">
                                <div class="seg" data-seg>
                                    <button class="seg-btn active">Today</button>
                                    <button class="seg-btn">Week</button>
                                    <button class="seg-btn">Month</button>
                                </div>
                                <button class="branch-select">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 21V8l8-5 8 5v13" />
                                        <path d="M9 21v-7h6v7" />
                                    </svg>
                                    All Branches
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M6 9l6 6 6-6" />
                                    </svg>
                                </button>
                                <span class="last-updated">Last updated: <span id="ob-last-updated">11:24 AM</span></span>
                                <button class="icon-btn" id="ob-refresh" title="Refresh">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 12a8 8 0 0 1 14-5l2-2v6h-6l2-2a6 6 0 1 0 1 6" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- KPI Grid -->
                        <div class="orders-kpi-grid">
                            <?php foreach ($ob_kpis as $k): ?>
                                <div class="order-kpi-card tone-<?= $k['tone'] ?>">
                                    <div class="kpi-icon"><?= ob_icon($k['icon']) ?></div>
                                    <div class="kpi-body">
                                        <div class="kpi-label"><?= $k['label'] ?></div>
                                        <div class="kpi-value"><?= $k['value'] ?></div>
                                        <div class="kpi-trend <?= $k['dir'] ?>">
                                            <?= $k['dir'] === 'up' ? '↑' : '↓' ?> <?= $k['trend'] ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Main two-column layout -->
                        <div class="orders-layout">

                            <!-- LEFT: Live Orders Queue -->
                            <div class="orders-table-card">
                                <div class="card-head">
                                    <h3>Live Orders Queue</h3>
                                </div>
                                <div class="table-wrap">
                                    <table class="ob-table">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Table</th>
                                                <th>Items</th>
                                                <th>Order Status</th>
                                                <th>Kitchen Stage</th>
                                                <th>Waiter</th>
                                                <th>Amount</th>
                                                <th>Time</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ob_live_orders as $r): ?>
                                                <tr>
                                                    <td class="link"><?= $r[0] ?></td>
                                                    <td><?= $r[1] ?></td>
                                                    <td><?= $r[2] ?></td>
                                                    <td><?= ob_pill($r[3]) ?></td>
                                                    <td><?= ob_pill($r[4]) ?></td>
                                                    <td><?= $r[5] ?></td>
                                                    <td class="amount"><?= $r[6] ?></td>
                                                    <td class="muted"><?= $r[7] ?></td>
                                                    <td><button class="row-action">⋮</button></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-foot">
                                    <span class="muted">Showing 1 to 8 of 24 orders</span>
                                    <a class="link-arrow" href="#">View all live orders →</a>
                                </div>
                            </div>

                            <!-- RIGHT: stack -->
                            <div class="orders-side-stack">

                                <!-- Bill Approval Queue -->
                                <div class="orders-table-card compact">
                                    <div class="card-head">
                                        <h3>Bill Approval Queue</h3>
                                        <a class="view-all" href="#">View all</a>
                                    </div>
                                    <div class="table-wrap">
                                        <table class="ob-table">
                                            <thead>
                                                <tr>
                                                    <th>Bill ID</th>
                                                    <th>Table</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($ob_bill_queue as $r): ?>
                                                    <tr>
                                                        <td class="link"><?= $r[0] ?></td>
                                                        <td><?= $r[1] ?></td>
                                                        <td class="amount"><?= $r[2] ?></td>
                                                        <td><?= ob_pill($r[3]) ?></td>
                                                        <td><button class="action-btn"><?= $r[4] ?></button></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-foot end"><a class="link-arrow" href="#">View all bills →</a></div>
                                </div>

                                <!-- Payment Verification -->
                                <div class="orders-table-card compact">
                                    <div class="card-head">
                                        <h3>Payment Verification</h3>
                                        <a class="view-all" href="#">View all</a>
                                    </div>
                                    <div class="pay-mini-grid">
                                        <div class="pay-mini"><span class="dot orange"></span>
                                            <div>
                                                <div class="m-l">Cash Pending</div>
                                                <div class="m-v">5</div>
                                                <div class="m-s">₹6,480</div>
                                            </div>
                                        </div>
                                        <div class="pay-mini"><span class="dot blue"></span>
                                            <div>
                                                <div class="m-l">UPI Pending</div>
                                                <div class="m-v">7</div>
                                                <div class="m-s">₹8,930</div>
                                            </div>
                                        </div>
                                        <div class="pay-mini"><span class="dot green"></span>
                                            <div>
                                                <div class="m-l">Paid</div>
                                                <div class="m-v">18</div>
                                                <div class="m-s">₹57,620</div>
                                            </div>
                                        </div>
                                        <div class="pay-mini"><span class="dot red"></span>
                                            <div>
                                                <div class="m-l">Failed</div>
                                                <div class="m-v">2</div>
                                                <div class="m-s">₹1,190</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-wrap">
                                        <table class="ob-table">
                                            <thead>
                                                <tr>
                                                    <th>Payment ID</th>
                                                    <th>Table</th>
                                                    <th>Method</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($ob_pay_verify as $r): ?>
                                                    <tr>
                                                        <td class="link"><?= $r[0] ?></td>
                                                        <td><?= $r[1] ?></td>
                                                        <td><span class="method m-<?= strtolower($r[2]) ?>"><?= $r[2] ?></span></td>
                                                        <td class="amount"><?= $r[3] ?></td>
                                                        <td><?= ob_pill($r[4]) ?></td>
                                                        <td><button class="action-btn <?= $r[5] === 'View' ? 'ghost' : '' ?>"><?= $r[5] ?></button></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-foot end"><a class="link-arrow" href="#">View all payments →</a></div>
                                </div>

                            </div>
                        </div>

                        <!-- Bills Management + side -->
                        <div class="orders-layout">
                            <div class="bills-management-card">
                                <div class="card-head">
                                    <h3>Bills Management</h3>
                                </div>
                                <div class="bm-filters">
                                    <select class="select">
                                        <option>Bill Status: All</option>
                                        <option>New</option>
                                        <option>Approved</option>
                                        <option>Closed</option>
                                    </select>
                                    <select class="select">
                                        <option>Payment Status: All</option>
                                        <option>Paid</option>
                                        <option>Unpaid</option>
                                        <option>In Processing</option>
                                    </select>
                                    <select class="select">
                                        <option>Today</option>
                                        <option>Week</option>
                                        <option>Month</option>
                                    </select>
                                    <div class="search">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <circle cx="11" cy="11" r="7" />
                                            <path d="M21 21l-4.3-4.3" />
                                        </svg>
                                        <input id="ob-bill-search" type="text" placeholder="Search bill ID, order ID, table...">
                                    </div>
                                </div>
                                <div class="table-wrap">
                                    <table class="ob-table" id="ob-bills-table">
                                        <thead>
                                            <tr>
                                                <th>Bill ID</th>
                                                <th>Order ID</th>
                                                <th>Table</th>
                                                <th>Bill Status</th>
                                                <th>Payment Status</th>
                                                <th>Method</th>
                                                <th>Amount</th>
                                                <th>Updated Time</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ob_bills as $r): ?>
                                                <tr>
                                                    <td class="link"><?= $r[0] ?></td>
                                                    <td class="link"><?= $r[1] ?></td>
                                                    <td><?= $r[2] ?></td>
                                                    <td><?= ob_pill($r[3]) ?></td>
                                                    <td><?= ob_pill($r[4]) ?></td>
                                                    <td><?= $r[5] ?></td>
                                                    <td class="amount"><?= $r[6] ?></td>
                                                    <td class="muted"><?= $r[7] ?></td>
                                                    <td><button class="action-btn ghost">View</button></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-foot">
                                    <span class="muted">Showing 1 to 8 of 32 bills</span>
                                    <div class="pager">
                                        <button>‹</button>
                                        <button class="active">1</button>
                                        <button>2</button><button>3</button><button>4</button>
                                        <button>›</button>
                                    </div>
                                </div>
                            </div>

                            <div class="orders-side-stack">

                                <!-- Payment Method Split -->
                                <div class="payment-split-card">
                                    <div class="card-head">
                                        <h3>Payment Method Split</h3><span class="chip">Today</span>
                                    </div>
                                    <div class="split-body">
                                        <div class="donut-wrap">
                                            <canvas id="ob-payment-donut" width="160" height="160"></canvas>
                                            <div class="donut-center">
                                                <div class="dc-l">Total</div>
                                                <div class="dc-v">₹72,220</div>
                                            </div>
                                        </div>
                                        <ul class="split-legend">
                                            <li><span class="dot green"></span>
                                                <div>
                                                    <div class="ll">Cash</div>
                                                    <div class="lv">₹24,180 <span class="muted">(33%)</span></div>
                                                </div>
                                            </li>
                                            <li><span class="dot blue"></span>
                                                <div>
                                                    <div class="ll">UPI</div>
                                                    <div class="lv">₹34,220 <span class="muted">(47%)</span></div>
                                                </div>
                                            </li>
                                            <li><span class="dot purple"></span>
                                                <div>
                                                    <div class="ll">Card / Other</div>
                                                    <div class="lv">₹13,820 <span class="muted">(20%)</span></div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-foot"><span class="muted">Total Paid</span><strong>₹72,220</strong></div>
                                </div>

                                <!-- Recent Activity -->
                                <div class="billing-activity-card">
                                    <div class="card-head">
                                        <h3>Recent Billing Activity</h3><a class="view-all" href="#">View all</a>
                                    </div>
                                    <ul class="activity-list">
                                        <?php foreach ($ob_activity as $a): ?>
                                            <li>
                                                <span class="act-icon i-<?= $a[0] ?>">
                                                    <?php if ($a[0] === 'approve'): ?>✓<?php elseif ($a[0] === 'upi' || $a[0] === 'cash'): ?>₹<?php else: ?>+<?php endif; ?>
                                                </span>
                                                <div class="act-body">
                                                    <div class="act-t"><?= $a[1] ?></div>
                                                    <div class="act-s muted"><?= $a[2] ?></div>
                                                </div>
                                                <span class="act-time muted"><?= $a[3] ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>

                                <!-- Pending Alerts -->
                                <div class="pending-alerts-card">
                                    <div class="card-head">
                                        <h3>Pending Alerts</h3><a class="view-all" href="#">View all</a>
                                    </div>
                                    <ul class="alert-list">
                                        <?php foreach ($ob_alerts as $a): ?>
                                            <li>
                                                <span class="al-icon t-<?= $a[0] ?>">!</span>
                                                <span class="al-text"><?= $a[1] ?></span>
                                                <span class="al-badge t-<?= $a[0] ?>"><?= $a[2] ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <div class="card-foot"><span class="muted">Stay on top of important alerts</span></div>
                                </div>

                            </div>
                        </div>

                    </section>




                    <!-- ================= STAFF MODULE START ================= -->
                    <section class="staff-module" id="staff-module" data-module="staff">

                        <header class="staff-header">
                            <div>
                                <h1>Staff</h1>
                                <p>Team management, attendance, shifts, roles, permissions, and workforce performance.</p>
                            </div>

                            <div class="staff-header-actions">
                                <button class="staff-btn staff-btn-ghost" id="staff-export-btn" type="button">
                                    <i data-lucide="download"></i>
                                    Export
                                </button>
                                <button class="staff-btn staff-btn-primary" id="open-add-staff-modal" type="button">
                                    <i data-lucide="plus"></i>
                                    Add Staff
                                </button>
                            </div>
                        </header>

                        <div class="staff-filter-bar">
                            <div class="staff-tabs" id="staff-tabs">
                                <button class="active" data-staff-tab="all" type="button">All Staff</button>
                                <button data-staff-tab="manager" type="button">Managers</button>
                                <button data-staff-tab="waiter" type="button">Waiters</button>
                                <button data-staff-tab="kitchen" type="button">Kitchen</button>
                                <button data-staff-tab="attendance" type="button">Attendance</button>
                                <button data-staff-tab="roles" type="button">Roles &amp; Access</button>
                            </div>

                            <div class="staff-filter-right">
                                <div class="staff-date-filter" id="staff-date-filter">
                                    <button class="active" type="button">Today</button>
                                    <button type="button">Week</button>
                                    <button type="button">Month</button>
                                </div>

                                <button class="staff-branch-select" type="button">
                                    <i data-lucide="building-2"></i>
                                    All Branches
                                    <i data-lucide="chevron-down"></i>
                                </button>

                                <span class="staff-last-updated">
                                    Last updated: <span id="staff-last-updated">11:24 AM</span>
                                </span>

                                <button class="staff-icon-btn" id="staff-refresh-btn" type="button" aria-label="Refresh staff module">
                                    <i data-lucide="refresh-cw"></i>
                                </button>
                            </div>
                        </div>

                        <div class="staff-kpi-grid">
                            <article class="staff-kpi-card tone-blue">
                                <div class="staff-kpi-icon"><i data-lucide="users"></i></div>
                                <div>
                                    <span>Total Staff</span>
                                    <strong>42</strong>
                                    <small class="up">↑ 8.2% vs yesterday</small>
                                </div>
                            </article>

                            <article class="staff-kpi-card tone-green">
                                <div class="staff-kpi-icon"><i data-lucide="user-check"></i></div>
                                <div>
                                    <span>On Duty</span>
                                    <strong>28</strong>
                                    <small class="up">↑ 12.5% vs yesterday</small>
                                </div>
                            </article>

                            <article class="staff-kpi-card tone-purple">
                                <div class="staff-kpi-icon"><i data-lucide="shield-check"></i></div>
                                <div>
                                    <span>Managers</span>
                                    <strong>6</strong>
                                    <small class="up">↑ 0% vs yesterday</small>
                                </div>
                            </article>

                            <article class="staff-kpi-card tone-orange">
                                <div class="staff-kpi-icon"><i data-lucide="hand-platter"></i></div>
                                <div>
                                    <span>Waiters</span>
                                    <strong>20</strong>
                                    <small class="up">↑ 11.1% vs yesterday</small>
                                </div>
                            </article>

                            <article class="staff-kpi-card tone-blue">
                                <div class="staff-kpi-icon"><i data-lucide="chef-hat"></i></div>
                                <div>
                                    <span>Kitchen Staff</span>
                                    <strong>10</strong>
                                    <small class="up">↑ 5.3% vs yesterday</small>
                                </div>
                            </article>

                            <article class="staff-kpi-card tone-green">
                                <div class="staff-kpi-icon"><i data-lucide="badge-check"></i></div>
                                <div>
                                    <span>Attendance Rate</span>
                                    <strong>92.4%</strong>
                                    <small class="up">↑ 3.6% vs yesterday</small>
                                </div>
                            </article>

                            <article class="staff-kpi-card tone-purple">
                                <div class="staff-kpi-icon"><i data-lucide="clock-3"></i></div>
                                <div>
                                    <span>Avg Response Time</span>
                                    <strong>3m 24s</strong>
                                    <small class="down">↓ 8.1% vs yesterday</small>
                                </div>
                            </article>

                            <article class="staff-kpi-card tone-orange">
                                <div class="staff-kpi-icon"><i data-lucide="triangle-alert"></i></div>
                                <div>
                                    <span>Open Staffing Alerts</span>
                                    <strong>7</strong>
                                    <small class="up">↑ 2 vs yesterday</small>
                                </div>
                            </article>
                        </div>

                        <div class="staff-layout">
                            <article class="staff-card staff-directory-card">
                                <div class="staff-card-head">
                                    <div>
                                        <h3>Staff Directory</h3>
                                        <p>Live staff records, role assignment, branch mapping, and status control.</p>
                                    </div>
                                    <button class="staff-link-btn" type="button" data-staff-toast="Opening full staff directory">View all</button>
                                </div>

                                <div class="staff-directory-controls">
                                    <div class="staff-search-box">
                                        <i data-lucide="search"></i>
                                        <input id="staff-search-input" type="text" placeholder="Search staff by name, role, or ID...">
                                    </div>

                                    <div class="staff-filter-wrap">
                                        <button class="staff-filter-btn" id="staff-filter-toggle" type="button">
                                            <i data-lucide="sliders-horizontal"></i>
                                        </button>

                                        <div class="staff-filter-menu" id="staff-filter-menu">
                                            <button type="button" data-filter-role="all">All Roles</button>
                                            <button type="button" data-filter-role="manager">Managers</button>
                                            <button type="button" data-filter-role="waiter">Waiters</button>
                                            <button type="button" data-filter-role="chef">Kitchen / Chef</button>
                                            <button type="button" data-filter-role="cashier">Cashier</button>
                                            <button type="button" data-filter-role="host">Host</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="staff-table-wrap">
                                    <table class="staff-table" id="staff-directory-table">
                                        <thead>
                                            <tr>
                                                <th>Staff ID</th>
                                                <th>Name</th>
                                                <th>Role</th>
                                                <th>Branch</th>
                                                <th>Assigned Area / Tables</th>
                                                <th>Shift</th>
                                                <th>Contact</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr data-staff-row data-role="manager" data-search="stf-0001 rahul mehta manager main branch floor manager active">
                                                <td><a class="staff-id-link" href="#">STF-0001</a></td>
                                                <td>
                                                    <div class="staff-name-cell">
                                                        <span class="staff-avatar"><img src="https://i.pravatar.cc/80?img=12" alt=""></span>
                                                        Rahul Mehta
                                                    </div>
                                                </td>
                                                <td><span class="role-pill role-manager">Manager</span></td>
                                                <td>Main Branch</td>
                                                <td>Floor Manager</td>
                                                <td>General<br><small>9 AM – 5 PM</small></td>
                                                <td>98765 43210</td>
                                                <td><span class="status-pill status-active">Active</span></td>
                                                <td><button class="staff-row-menu-btn" type="button">⋮</button></td>
                                            </tr>

                                            <tr data-staff-row data-role="waiter" data-search="stf-0002 neha sharma waiter main branch t-01 t-08 active">
                                                <td><a class="staff-id-link" href="#">STF-0002</a></td>
                                                <td>
                                                    <div class="staff-name-cell">
                                                        <span class="staff-avatar"><img src="https://i.pravatar.cc/80?img=32" alt=""></span>
                                                        Neha Sharma
                                                    </div>
                                                </td>
                                                <td><span class="role-pill role-waiter">Waiter</span></td>
                                                <td>Main Branch</td>
                                                <td>T-01 – T-08</td>
                                                <td>Morning<br><small>7 AM – 3 PM</small></td>
                                                <td>98765 43211</td>
                                                <td><span class="status-pill status-active">Active</span></td>
                                                <td><button class="staff-row-menu-btn" type="button">⋮</button></td>
                                            </tr>

                                            <tr data-staff-row data-role="waiter" data-search="stf-0003 arjun verma waiter rooftop branch t-09 t-16 on break">
                                                <td><a class="staff-id-link" href="#">STF-0003</a></td>
                                                <td>
                                                    <div class="staff-name-cell">
                                                        <span class="staff-avatar"><img src="https://i.pravatar.cc/80?img=15" alt=""></span>
                                                        Arjun Verma
                                                    </div>
                                                </td>
                                                <td><span class="role-pill role-waiter">Waiter</span></td>
                                                <td>Rooftop Branch</td>
                                                <td>T-09 – T-16</td>
                                                <td>Evening<br><small>3 PM – 11 PM</small></td>
                                                <td>98765 43212</td>
                                                <td><span class="status-pill status-break">On Break</span></td>
                                                <td><button class="staff-row-menu-btn" type="button">⋮</button></td>
                                            </tr>

                                            <tr data-staff-row data-role="chef" data-search="stf-0004 sandeep chef main branch kitchen active">
                                                <td><a class="staff-id-link" href="#">STF-0004</a></td>
                                                <td>
                                                    <div class="staff-name-cell">
                                                        <span class="staff-avatar"><img src="https://i.pravatar.cc/80?img=18" alt=""></span>
                                                        Sandeep K.
                                                    </div>
                                                </td>
                                                <td><span class="role-pill role-chef">Chef</span></td>
                                                <td>Main Branch</td>
                                                <td>Kitchen</td>
                                                <td>General<br><small>8 AM – 4 PM</small></td>
                                                <td>98765 43213</td>
                                                <td><span class="status-pill status-active">Active</span></td>
                                                <td><button class="staff-row-menu-btn" type="button">⋮</button></td>
                                            </tr>

                                            <tr data-staff-row data-role="cashier" data-search="stf-0005 pooja iyer cashier main branch billing counter active">
                                                <td><a class="staff-id-link" href="#">STF-0005</a></td>
                                                <td>
                                                    <div class="staff-name-cell">
                                                        <span class="staff-avatar"><img src="https://i.pravatar.cc/80?img=44" alt=""></span>
                                                        Pooja Iyer
                                                    </div>
                                                </td>
                                                <td><span class="role-pill role-cashier">Cashier</span></td>
                                                <td>Main Branch</td>
                                                <td>Billing Counter</td>
                                                <td>General<br><small>10 AM – 6 PM</small></td>
                                                <td>98765 43214</td>
                                                <td><span class="status-pill status-active">Active</span></td>
                                                <td><button class="staff-row-menu-btn" type="button">⋮</button></td>
                                            </tr>

                                            <tr data-staff-row data-role="host" data-search="stf-0006 vikram singh host rooftop branch entrance late">
                                                <td><a class="staff-id-link" href="#">STF-0006</a></td>
                                                <td>
                                                    <div class="staff-name-cell">
                                                        <span class="staff-avatar"><img src="https://i.pravatar.cc/80?img=11" alt=""></span>
                                                        Vikram Singh
                                                    </div>
                                                </td>
                                                <td><span class="role-pill role-host">Host</span></td>
                                                <td>Rooftop Branch</td>
                                                <td>Entrance</td>
                                                <td>Morning<br><small>8 AM – 4 PM</small></td>
                                                <td>98765 43215</td>
                                                <td><span class="status-pill status-late">Late</span></td>
                                                <td><button class="staff-row-menu-btn" type="button">⋮</button></td>
                                            </tr>

                                            <tr data-staff-row data-role="waiter" data-search="stf-0007 kavya nair waiter main branch t-17 t-24 active">
                                                <td><a class="staff-id-link" href="#">STF-0007</a></td>
                                                <td>
                                                    <div class="staff-name-cell">
                                                        <span class="staff-avatar"><img src="https://i.pravatar.cc/80?img=47" alt=""></span>
                                                        Kavya Nair
                                                    </div>
                                                </td>
                                                <td><span class="role-pill role-waiter">Waiter</span></td>
                                                <td>Main Branch</td>
                                                <td>T-17 – T-24</td>
                                                <td>Evening<br><small>4 PM – 12 AM</small></td>
                                                <td>98765 43216</td>
                                                <td><span class="status-pill status-active">Active</span></td>
                                                <td><button class="staff-row-menu-btn" type="button">⋮</button></td>
                                            </tr>

                                            <tr data-staff-row data-role="chef" data-search="stf-0008 rohit das chef main branch kitchen off duty">
                                                <td><a class="staff-id-link" href="#">STF-0008</a></td>
                                                <td>
                                                    <div class="staff-name-cell">
                                                        <span class="staff-avatar"><img src="https://i.pravatar.cc/80?img=25" alt=""></span>
                                                        Rohit Das
                                                    </div>
                                                </td>
                                                <td><span class="role-pill role-chef">Chef</span></td>
                                                <td>Main Branch</td>
                                                <td>Kitchen</td>
                                                <td>General<br><small>6 AM – 2 PM</small></td>
                                                <td>98765 43217</td>
                                                <td><span class="status-pill status-off-duty">Off Duty</span></td>
                                                <td><button class="staff-row-menu-btn" type="button">⋮</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="staff-card-foot">
                                    <span>Showing 1 to 8 of 42 staff</span>
                                    <div class="staff-pagination">
                                        <button type="button">‹</button>
                                        <button class="active" type="button">1</button>
                                        <button type="button">2</button>
                                        <button type="button">3</button>
                                        <button type="button">4</button>
                                        <button type="button">5</button>
                                        <button type="button">›</button>
                                    </div>
                                </div>
                            </article>

                            <div class="staff-side-stack">
                                <article class="staff-card attendance-overview-card" id="staff-attendance-card">
                                    <div class="staff-card-head">
                                        <div>
                                            <h3>Attendance Overview</h3>
                                            <p>Current day check-in health and exceptions.</p>
                                        </div>
                                        <button class="staff-link-btn" type="button" data-staff-toast="Opening attendance report">View all</button>
                                    </div>

                                    <div class="attendance-mini-grid">
                                        <div class="attendance-mini present">
                                            <i data-lucide="circle-check"></i>
                                            <span>Present</span>
                                            <strong>28</strong>
                                            <small>66.7%</small>
                                        </div>
                                        <div class="attendance-mini late">
                                            <i data-lucide="clock-alert"></i>
                                            <span>Late</span>
                                            <strong>4</strong>
                                            <small>9.5%</small>
                                        </div>
                                        <div class="attendance-mini leave">
                                            <i data-lucide="calendar-clock"></i>
                                            <span>On Leave</span>
                                            <strong>6</strong>
                                            <small>14.3%</small>
                                        </div>
                                        <div class="attendance-mini absent">
                                            <i data-lucide="circle-x"></i>
                                            <span>Absent</span>
                                            <strong>4</strong>
                                            <small>9.5%</small>
                                        </div>
                                    </div>

                                    <div class="staff-table-wrap small">
                                        <table class="staff-table compact">
                                            <thead>
                                                <tr>
                                                    <th>Staff Name</th>
                                                    <th>Check-in Time</th>
                                                    <th>Shift Start</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Rahul Mehta</td>
                                                    <td>8:58 AM</td>
                                                    <td>9:00 AM</td>
                                                    <td><span class="status-pill status-active">Present</span></td>
                                                    <td><button class="action-btn" type="button" data-staff-toast="Opening Rahul attendance">View</button></td>
                                                </tr>
                                                <tr>
                                                    <td>Neha Sharma</td>
                                                    <td>7:02 AM</td>
                                                    <td>7:00 AM</td>
                                                    <td><span class="status-pill status-active">Present</span></td>
                                                    <td><button class="action-btn" type="button" data-staff-toast="Opening Neha attendance">View</button></td>
                                                </tr>
                                                <tr>
                                                    <td>Arjun Verma</td>
                                                    <td>3:18 PM</td>
                                                    <td>3:00 PM</td>
                                                    <td><span class="status-pill status-break">Late</span></td>
                                                    <td><button class="action-btn" type="button" data-staff-toast="Marked follow-up for Arjun">Mark</button></td>
                                                </tr>
                                                <tr>
                                                    <td>Sandeep K.</td>
                                                    <td>8:05 AM</td>
                                                    <td>8:00 AM</td>
                                                    <td><span class="status-pill status-active">Present</span></td>
                                                    <td><button class="action-btn" type="button" data-staff-toast="Opening Sandeep attendance">View</button></td>
                                                </tr>
                                                <tr>
                                                    <td>Pooja Iyer</td>
                                                    <td>10:02 AM</td>
                                                    <td>10:00 AM</td>
                                                    <td><span class="status-pill status-active">Present</span></td>
                                                    <td><button class="action-btn" type="button" data-staff-toast="Opening Pooja attendance">View</button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="staff-card-foot end">
                                        <button class="staff-link-btn" type="button" data-staff-toast="Opening full attendance">View full attendance →</button>
                                    </div>
                                </article>

                                <article class="staff-card shift-schedule-card">
                                    <div class="staff-card-head">
                                        <div>
                                            <h3>Shift Schedule</h3>
                                            <p>Today, May 18, 2025</p>
                                        </div>
                                        <button class="staff-link-btn" type="button" data-staff-toast="Opening shift manager">Manage shifts</button>
                                    </div>

                                    <div class="staff-table-wrap small">
                                        <table class="staff-table compact">
                                            <thead>
                                                <tr>
                                                    <th>Staff Name</th>
                                                    <th>Role</th>
                                                    <th>Shift</th>
                                                    <th>Time</th>
                                                    <th>Branch / Zone</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Rahul Mehta</td>
                                                    <td><span class="role-pill role-manager">Manager</span></td>
                                                    <td>General</td>
                                                    <td>9:00 AM – 5:00 PM</td>
                                                    <td>Main Branch</td>
                                                    <td><button class="action-btn" type="button" data-staff-toast="Opening shift details">View</button></td>
                                                </tr>
                                                <tr>
                                                    <td>Neha Sharma</td>
                                                    <td><span class="role-pill role-waiter">Waiter</span></td>
                                                    <td>Morning</td>
                                                    <td>7:00 AM – 3:00 PM</td>
                                                    <td>Main Branch</td>
                                                    <td><button class="action-btn" type="button" data-staff-toast="Opening shift details">View</button></td>
                                                </tr>
                                                <tr>
                                                    <td>Arjun Verma</td>
                                                    <td><span class="role-pill role-waiter">Waiter</span></td>
                                                    <td>Evening</td>
                                                    <td>3:00 PM – 11:00 PM</td>
                                                    <td>Rooftop Branch</td>
                                                    <td><button class="action-btn" type="button" data-staff-toast="Opening shift details">View</button></td>
                                                </tr>
                                                <tr>
                                                    <td>Sandeep K.</td>
                                                    <td><span class="role-pill role-chef">Chef</span></td>
                                                    <td>General</td>
                                                    <td>8:00 AM – 4:00 PM</td>
                                                    <td>Main Branch</td>
                                                    <td><button class="action-btn" type="button" data-staff-toast="Opening shift details">View</button></td>
                                                </tr>
                                                <tr>
                                                    <td>Kavya Nair</td>
                                                    <td><span class="role-pill role-waiter">Waiter</span></td>
                                                    <td>Evening</td>
                                                    <td>4:00 PM – 12:00 AM</td>
                                                    <td>Main Branch</td>
                                                    <td><button class="action-btn" type="button" data-staff-toast="Opening shift details">View</button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="staff-card-foot end">
                                        <button class="staff-link-btn" type="button" data-staff-toast="Opening all schedules">View all schedules →</button>
                                    </div>
                                </article>
                            </div>
                        </div>

                        <div class="staff-bottom-grid">
                            <article class="staff-card role-access-card" id="staff-roles-card">
                                <div class="staff-card-head">
                                    <div>
                                        <h3>Role &amp; Access Management</h3>
                                        <p>Permission matrix and active users by role.</p>
                                    </div>
                                    <button class="staff-link-btn" type="button" data-staff-toast="Opening role manager">Manage roles</button>
                                </div>

                                <div class="staff-table-wrap">
                                    <table class="staff-table compact">
                                        <thead>
                                            <tr>
                                                <th>Role</th>
                                                <th>Access Level</th>
                                                <th>Active Users</th>
                                                <th>Key Permissions</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Owner / Admin</td>
                                                <td><span class="access-pill access-full">Full Access</span></td>
                                                <td>1</td>
                                                <td>
                                                    <span class="permission-chip">All Modules</span>
                                                    <span class="permission-chip">Settings</span>
                                                    <span class="permission-chip">Reports</span>
                                                </td>
                                                <td><span class="status-pill status-active">Active</span></td>
                                                <td><button class="action-btn" type="button" data-staff-toast="Editing Owner role">Edit</button></td>
                                            </tr>
                                            <tr>
                                                <td>Manager</td>
                                                <td><span class="access-pill access-high">High Access</span></td>
                                                <td>6</td>
                                                <td>
                                                    <span class="permission-chip">Orders</span>
                                                    <span class="permission-chip">Bills</span>
                                                    <span class="permission-chip">Staff</span>
                                                </td>
                                                <td><span class="status-pill status-active">Active</span></td>
                                                <td><button class="action-btn" type="button" data-staff-toast="Editing Manager role">Edit</button></td>
                                            </tr>
                                            <tr>
                                                <td>Waiter</td>
                                                <td><span class="access-pill access-medium">Medium Access</span></td>
                                                <td>20</td>
                                                <td>
                                                    <span class="permission-chip">Orders</span>
                                                    <span class="permission-chip">Bills</span>
                                                    <span class="permission-chip">Menu</span>
                                                </td>
                                                <td><span class="status-pill status-active">Active</span></td>
                                                <td><button class="action-btn" type="button" data-staff-toast="Editing Waiter role">Edit</button></td>
                                            </tr>
                                            <tr>
                                                <td>Kitchen</td>
                                                <td><span class="access-pill access-medium">Medium Access</span></td>
                                                <td>10</td>
                                                <td>
                                                    <span class="permission-chip">Orders</span>
                                                    <span class="permission-chip">Menu</span>
                                                    <span class="permission-chip">Kitchen</span>
                                                </td>
                                                <td><span class="status-pill status-active">Active</span></td>
                                                <td><button class="action-btn" type="button" data-staff-toast="Editing Kitchen role">Edit</button></td>
                                            </tr>
                                            <tr>
                                                <td>Cashier</td>
                                                <td><span class="access-pill access-limited">Limited Access</span></td>
                                                <td>5</td>
                                                <td>
                                                    <span class="permission-chip">Bills</span>
                                                    <span class="permission-chip">Payments</span>
                                                    <span class="permission-chip">Reports</span>
                                                </td>
                                                <td><span class="status-pill status-active">Active</span></td>
                                                <td><button class="action-btn" type="button" data-staff-toast="Editing Cashier role">Edit</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="staff-card-foot end">
                                    <button class="staff-link-btn" type="button" data-staff-toast="Opening all permissions">View all roles &amp; permissions →</button>
                                </div>
                            </article>

                            <article class="staff-card team-performance-card">
                                <div class="staff-card-head">
                                    <h3>Team Performance</h3>
                                    <button class="staff-link-btn" type="button" data-staff-toast="Opening performance view">View all</button>
                                </div>

                                <div class="performance-list">
                                    <div class="performance-row top">
                                        <span class="rank">1</span>
                                        <span class="staff-avatar"><img src="https://i.pravatar.cc/80?img=12" alt=""></span>
                                        <strong>Rahul Mehta</strong>
                                        <span>24</span>
                                        <span>48</span>
                                        <b>4.8 ★</b>
                                    </div>
                                    <div class="performance-row">
                                        <span class="rank">2</span>
                                        <span class="staff-avatar"><img src="https://i.pravatar.cc/80?img=32" alt=""></span>
                                        <strong>Neha Sharma</strong>
                                        <span>22</span>
                                        <span>44</span>
                                        <b>4.7 ★</b>
                                    </div>
                                    <div class="performance-row">
                                        <span class="rank">3</span>
                                        <span class="staff-avatar"><img src="https://i.pravatar.cc/80?img=15" alt=""></span>
                                        <strong>Arjun Verma</strong>
                                        <span>20</span>
                                        <span>40</span>
                                        <b>4.6 ★</b>
                                    </div>
                                    <div class="performance-row">
                                        <span class="rank">4</span>
                                        <span class="staff-avatar"><img src="https://i.pravatar.cc/80?img=47" alt=""></span>
                                        <strong>Kavya Nair</strong>
                                        <span>19</span>
                                        <span>36</span>
                                        <b>4.5 ★</b>
                                    </div>
                                    <div class="performance-row">
                                        <span class="rank">5</span>
                                        <span class="staff-avatar"><img src="https://i.pravatar.cc/80?img=44" alt=""></span>
                                        <strong>Pooja Iyer</strong>
                                        <span>18</span>
                                        <span>34</span>
                                        <b>4.4 ★</b>
                                    </div>
                                </div>

                                <div class="staff-card-foot end">
                                    <button class="staff-link-btn" type="button" data-staff-toast="Opening full performance">View full performance →</button>
                                </div>
                            </article>

                            <article class="staff-card staff-activity-card">
                                <div class="staff-card-head">
                                    <h3>Recent Staff Activity</h3>
                                    <button class="staff-link-btn" type="button" data-staff-toast="Opening staff activity">View all</button>
                                </div>

                                <div class="staff-activity-list">
                                    <div class="staff-activity-row">
                                        <span class="activity-icon blue"><i data-lucide="clock"></i></span>
                                        <div><strong>Rahul Mehta</strong><small>Shift started (General)</small></div>
                                        <time>9:02 AM</time>
                                    </div>
                                    <div class="staff-activity-row">
                                        <span class="activity-icon green"><i data-lucide="badge-check"></i></span>
                                        <div><strong>Neha Sharma</strong><small>Attendance marked present</small></div>
                                        <time>7:02 AM</time>
                                    </div>
                                    <div class="staff-activity-row">
                                        <span class="activity-icon purple"><i data-lucide="users"></i></span>
                                        <div><strong>Arjun Verma</strong><small>Assigned to tables T-09 – T-16</small></div>
                                        <time>3:05 PM</time>
                                    </div>
                                    <div class="staff-activity-row">
                                        <span class="activity-icon orange"><i data-lucide="user-cog"></i></span>
                                        <div><strong>Sandeep K.</strong><small>Profile updated</small></div>
                                        <time>Yesterday</time>
                                    </div>
                                    <div class="staff-activity-row">
                                        <span class="activity-icon blue"><i data-lucide="circle-check"></i></span>
                                        <div><strong>Pooja Iyer</strong><small>Shift ended (General)</small></div>
                                        <time>Yesterday</time>
                                    </div>
                                </div>
                            </article>

                            <article class="staff-card staff-alerts-card">
                                <div class="staff-card-head">
                                    <h3>Pending Alerts</h3>
                                    <button class="staff-link-btn" type="button" data-staff-toast="Opening all staff alerts">View all</button>
                                </div>

                                <div class="staff-alert-list">
                                    <div class="staff-alert-row">
                                        <span class="alert-icon red"><i data-lucide="timer-off"></i></span>
                                        <strong>Missing check-in</strong>
                                        <b>3</b>
                                    </div>
                                    <div class="staff-alert-row">
                                        <span class="alert-icon orange"><i data-lucide="users-round"></i></span>
                                        <strong>Understaffed shift</strong>
                                        <b>2</b>
                                    </div>
                                    <div class="staff-alert-row">
                                        <span class="alert-icon orange"><i data-lucide="user-plus"></i></span>
                                        <strong>Staff approval pending</strong>
                                        <b>1</b>
                                    </div>
                                    <div class="staff-alert-row">
                                        <span class="alert-icon red"><i data-lucide="file-warning"></i></span>
                                        <strong>Expired document reminder</strong>
                                        <b>1</b>
                                    </div>
                                    <div class="staff-alert-row">
                                        <span class="alert-icon orange"><i data-lucide="graduation-cap"></i></span>
                                        <strong>Training due</strong>
                                        <b>1</b>
                                    </div>
                                </div>

                                <div class="staff-card-foot end">
                                    <button class="staff-link-btn" type="button" data-staff-toast="Opening alert center">View all alerts →</button>
                                </div>
                            </article>
                        </div>
                    </section>

                    <div class="add-staff-modal" id="add-staff-modal" aria-hidden="true">
                        <div class="add-staff-backdrop" data-close-add-staff></div>

                        <div class="add-staff-dialog" role="dialog" aria-modal="true" aria-labelledby="add-staff-title">
                            <div class="add-staff-head">
                                <div>
                                    <h3 id="add-staff-title">Add Staff</h3>
                                    <p>Create a staff profile placeholder. Backend can be connected later.</p>
                                </div>
                                <button class="staff-icon-btn" type="button" data-close-add-staff aria-label="Close add staff modal">
                                    <i data-lucide="x"></i>
                                </button>
                            </div>

                            <form id="add-staff-form" class="add-staff-form">
                                <label>
                                    Full Name
                                    <input type="text" placeholder="Enter full name" required>
                                </label>

                                <label>
                                    Role
                                    <select required>
                                        <option value="">Select role</option>
                                        <option>Manager</option>
                                        <option>Waiter</option>
                                        <option>Chef</option>
                                        <option>Cashier</option>
                                        <option>Host</option>
                                    </select>
                                </label>

                                <label>
                                    Branch
                                    <select required>
                                        <option>Main Branch</option>
                                        <option>Rooftop Branch</option>
                                    </select>
                                </label>

                                <label>
                                    Phone
                                    <input type="tel" placeholder="98765 43210">
                                </label>

                                <label>
                                    Email
                                    <input type="email" placeholder="staff@hoteldelight.in">
                                </label>

                                <label>
                                    Shift
                                    <select>
                                        <option>General</option>
                                        <option>Morning</option>
                                        <option>Evening</option>
                                        <option>Night</option>
                                    </select>
                                </label>

                                <label class="wide">
                                    Assigned Area / Tables
                                    <input type="text" placeholder="Example: T-01 – T-08 / Kitchen / Billing Counter">
                                </label>

                                <label>
                                    Status
                                    <select>
                                        <option>Active</option>
                                        <option>Off Duty</option>
                                        <option>On Leave</option>
                                    </select>
                                </label>

                                <div class="add-staff-actions">
                                    <button class="staff-btn staff-btn-ghost" type="button" data-close-add-staff>Cancel</button>
                                    <button class="staff-btn staff-btn-primary" type="submit">Save Staff</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- ================= STAFF MODULE END ================= -->




                    <!-- ================= BRAND DETAILS MODULE START ================= -->
                    <section class="brand-details-module" id="brand-details-module" data-module="brand">
                        <header class="brand-details-header">
                            <div>
                                <h1>Brand Details</h1>
                                <p>Manage brand identity, business information, contact details, and core customer-facing assets.</p>
                            </div>

                            <div class="brand-details-header-actions">
                                <button class="bd-btn bd-btn-ghost" id="bd-export-btn" type="button">
                                    <i data-lucide="download"></i>
                                    Export
                                </button>
                                <button class="bd-btn bd-btn-primary" id="bd-save-btn" type="button">
                                    <i data-lucide="save"></i>
                                    Save Changes
                                </button>
                            </div>
                        </header>

                        <div class="brand-details-filter-bar">
                            <div class="brand-details-tabs" id="brand-details-tabs">
                                <button class="active" data-bd-tab="overview" type="button">Overview</button>
                                <button data-bd-tab="business" type="button">Business Info</button>
                                <button data-bd-tab="contact" type="button">Contact</button>
                                <button data-bd-tab="assets" type="button">Assets</button>
                                <button data-bd-tab="branches" type="button">Branches</button>
                                <button data-bd-tab="publishing" type="button">Publishing</button>
                            </div>

                            <div class="brand-details-filter-right">
                                <button class="bd-branch-select" type="button">
                                    <i data-lucide="building-2"></i>
                                    All Branches
                                    <i data-lucide="chevron-down"></i>
                                </button>

                                <span class="bd-last-updated">
                                    Last updated: <span id="bd-last-updated">11:24 AM</span>
                                </span>

                                <button class="bd-icon-btn" id="bd-refresh-btn" type="button" aria-label="Refresh brand details">
                                    <i data-lucide="refresh-cw"></i>
                                </button>
                            </div>
                        </div>

                        <div class="brand-details-kpi-grid">
                            <article class="brand-details-kpi-card tone-blue">
                                <div class="bd-kpi-icon"><i data-lucide="store"></i></div>
                                <div>
                                    <span>Active Branches</span>
                                    <strong>3</strong>
                                    <small>No change</small>
                                </div>
                            </article>

                            <article class="brand-details-kpi-card tone-green">
                                <div class="bd-kpi-icon"><i data-lucide="shield-check"></i></div>
                                <div>
                                    <span>Brand Status</span>
                                    <strong>Active</strong>
                                    <small>Healthy</small>
                                </div>
                            </article>

                            <article class="brand-details-kpi-card tone-purple">
                                <div class="bd-kpi-icon"><i data-lucide="files"></i></div>
                                <div>
                                    <span>Published Assets</span>
                                    <strong>12</strong>
                                    <small class="up">↑ 2 this week</small>
                                </div>
                            </article>

                            <article class="brand-details-kpi-card tone-green">
                                <div class="bd-kpi-icon"><i data-lucide="heart-pulse"></i></div>
                                <div>
                                    <span>Customer Page Health</span>
                                    <strong>96%</strong>
                                    <small class="up">↑ 4% this week</small>
                                </div>
                            </article>

                            <article class="brand-details-kpi-card tone-blue">
                                <div class="bd-kpi-icon"><i data-lucide="user-round-check"></i></div>
                                <div>
                                    <span>Contact Completion</span>
                                    <strong>100%</strong>
                                    <small>Complete</small>
                                </div>
                            </article>

                            <article class="brand-details-kpi-card tone-orange">
                                <div class="bd-kpi-icon"><i data-lucide="triangle-alert"></i></div>
                                <div>
                                    <span>Pending Updates</span>
                                    <strong>4</strong>
                                    <small class="down">↓ 1 from yesterday</small>
                                </div>
                            </article>

                            <article class="brand-details-kpi-card tone-blue">
                                <div class="bd-kpi-icon"><i data-lucide="globe-2"></i></div>
                                <div>
                                    <span>Live Pages</span>
                                    <strong>3</strong>
                                    <small>No change</small>
                                </div>
                            </article>

                            <article class="brand-details-kpi-card tone-red">
                                <div class="bd-kpi-icon"><i data-lucide="calendar-days"></i></div>
                                <div>
                                    <span>Last Publish</span>
                                    <strong>Today</strong>
                                    <small>10:42 AM</small>
                                </div>
                            </article>
                        </div>

                        <div class="brand-details-layout">
                            <article class="bd-card brand-profile-card" id="bd-profile-card">
                                <div class="bd-card-head">
                                    <div>
                                        <h3>Brand Profile</h3>
                                        <p>Core identity and operational brand settings.</p>
                                    </div>
                                    <button class="bd-action-link" type="button" data-open-brand-modal>
                                        <i data-lucide="pencil"></i>
                                        Edit
                                    </button>
                                </div>

                                <div class="bd-info-grid">
                                    <div class="info-row">
                                        <span>Brand Name</span>
                                        <strong>Hotel Delight</strong>
                                    </div>
                                    <div class="info-row">
                                        <span>Brand Code</span>
                                        <strong>HD-REST-001</strong>
                                    </div>
                                    <div class="info-row">
                                        <span>Subscription Plan</span>
                                        <strong>Starter</strong>
                                    </div>
                                    <div class="info-row">
                                        <span>Status</span>
                                        <strong><span class="status-pill bd-status-active">Active</span></strong>
                                    </div>
                                    <div class="info-row">
                                        <span>Currency</span>
                                        <strong>INR (₹)</strong>
                                    </div>
                                    <div class="info-row">
                                        <span>Timezone</span>
                                        <strong>Asia/Kolkata</strong>
                                    </div>
                                    <div class="info-row">
                                        <span>Industry</span>
                                        <strong>Restaurant / Hospitality</strong>
                                    </div>
                                    <div class="info-row">
                                        <span>Founded</span>
                                        <strong>2019</strong>
                                    </div>
                                    <div class="info-row wide">
                                        <span>Short Description</span>
                                        <strong>Premium dining experience with modern service and multi-branch management.</strong>
                                    </div>
                                </div>
                            </article>

                            <article class="bd-card brand-assets-card" id="bd-assets-card">
                                <div class="bd-card-head">
                                    <div>
                                        <h3>Brand Assets</h3>
                                        <p>Customer-facing logo, visuals, colors, and typography.</p>
                                    </div>
                                    <button class="bd-action-link" type="button" data-bd-toast="Opening asset editor">
                                        <i data-lucide="pencil"></i>
                                        Edit
                                    </button>
                                </div>

                                <div class="bd-assets-grid">
                                    <div class="asset-preview asset-logo">
                                        <span>Logo</span>
                                        <div class="asset-logo-frame">
                                            <img src="assets/hd-logo.png" alt="Hotel Delight logo">
                                        </div>
                                        <button type="button" data-bd-toast="Logo replace placeholder opened">Replace</button>
                                    </div>

                                    <div class="asset-preview asset-favicon">
                                        <span>Favicon</span>
                                        <div class="asset-favicon-frame">
                                            <img src="assets/hd-logo.png" alt="Hotel Delight favicon">
                                        </div>
                                        <button type="button" data-bd-toast="Favicon replace placeholder opened">Replace</button>
                                    </div>

                                    <div class="asset-preview asset-hero">
                                        <span>Hero Background</span>
                                        <div class="asset-hero-frame">
                                            <img src="assets/restaurant-banner.jpg" alt="Restaurant hero background">
                                            <i data-lucide="image"></i>
                                        </div>
                                        <button type="button" data-bd-toast="Hero image replace placeholder opened">Replace</button>
                                    </div>

                                    <div class="bd-style-token-panel">
                                        <div class="bd-style-token">
                                            <span>Primary Color</span>
                                            <strong><i class="bd-color-dot blue"></i>#2563EB</strong>
                                        </div>
                                        <div class="bd-style-token">
                                            <span>Accent Color</span>
                                            <strong><i class="bd-color-dot orange"></i>#F59E0B</strong>
                                        </div>
                                        <div class="bd-style-token">
                                            <span>Button Style</span>
                                            <strong>Primary</strong>
                                            <button type="button" data-bd-toast="Button style preview opened">Preview</button>
                                        </div>
                                        <div class="bd-style-token">
                                            <span>Typography</span>
                                            <strong>Inter / Poppins</strong>
                                            <button type="button" data-bd-toast="Typography preview opened">Preview</button>
                                        </div>
                                    </div>
                                </div>
                            </article>

                            <article class="bd-card business-info-card" id="bd-business-card">
                                <div class="bd-card-head">
                                    <div>
                                        <h3>Business Information</h3>
                                        <p>Legal, tax, address, and registration details.</p>
                                    </div>
                                    <button class="bd-action-link" type="button" data-bd-toast="Opening business information editor">
                                        <i data-lucide="pencil"></i>
                                        Edit
                                    </button>
                                </div>

                                <div class="bd-info-grid compact">
                                    <div class="info-row">
                                        <span>Legal Business Name</span>
                                        <strong>Hotel Delight Private Limited</strong>
                                    </div>
                                    <div class="info-row">
                                        <span>GST / Tax ID</span>
                                        <strong>27AAACH1234A1Z5</strong>
                                    </div>
                                    <div class="info-row">
                                        <span>Business Type</span>
                                        <strong>Private Limited Company</strong>
                                    </div>
                                    <div class="info-row">
                                        <span>Address line 1</span>
                                        <strong>123 MG Road, Connaught Place</strong>
                                    </div>
                                    <div class="info-row">
                                        <span>City</span>
                                        <strong>New Delhi</strong>
                                    </div>
                                    <div class="info-row">
                                        <span>State</span>
                                        <strong>Delhi</strong>
                                    </div>
                                    <div class="info-row">
                                        <span>Postal Code</span>
                                        <strong>110001</strong>
                                    </div>
                                    <div class="info-row">
                                        <span>Country</span>
                                        <strong>India</strong>
                                    </div>
                                    <div class="info-row">
                                        <span>Opening Since</span>
                                        <strong><i data-lucide="calendar-days"></i> Jan 15, 2019</strong>
                                    </div>
                                </div>
                            </article>

                            <article class="bd-card contact-details-card" id="bd-contact-card">
                                <div class="bd-card-head">
                                    <div>
                                        <h3>Primary Contact Details</h3>
                                        <p>Public and operational contact channels.</p>
                                    </div>
                                    <button class="bd-action-link" type="button" data-bd-toast="Opening contact editor">
                                        <i data-lucide="pencil"></i>
                                        Edit
                                    </button>
                                </div>

                                <div class="bd-contact-grid">
                                    <div class="bd-contact-row">
                                        <i data-lucide="mail"></i>
                                        <span>Contact Email</span>
                                        <strong>hello@hoteldelight.in</strong>
                                    </div>
                                    <div class="bd-contact-row">
                                        <i data-lucide="link"></i>
                                        <span>Website</span>
                                        <strong>www.hoteldelight.in <i data-lucide="external-link"></i></strong>
                                    </div>
                                    <div class="bd-contact-row">
                                        <i data-lucide="mail-check"></i>
                                        <span>Support Email</span>
                                        <strong>support@hoteldelight.in</strong>
                                    </div>
                                    <div class="bd-contact-row">
                                        <i data-lucide="user-round"></i>
                                        <span>Owner Name</span>
                                        <strong>Praful Shah</strong>
                                    </div>
                                    <div class="bd-contact-row">
                                        <i data-lucide="phone"></i>
                                        <span>Phone Number</span>
                                        <strong>+91 11 2345 6789</strong>
                                    </div>
                                    <div class="bd-contact-row">
                                        <i data-lucide="users-round"></i>
                                        <span>Operations Contact</span>
                                        <strong>Neha Sharma</strong>
                                    </div>
                                    <div class="bd-contact-row">
                                        <i data-lucide="message-circle"></i>
                                        <span>WhatsApp</span>
                                        <strong>+91 98765 43210</strong>
                                    </div>
                                    <div class="bd-contact-row">
                                        <i data-lucide="phone-call"></i>
                                        <span>Operations Contact Phone</span>
                                        <strong>+91 98123 45678</strong>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <div class="brand-details-lower-grid">
                            <article class="bd-card branch-summary-card" id="bd-branches-card">
                                <div class="bd-card-head">
                                    <h3>Branch Summary</h3>
                                    <button class="bd-action-link" type="button" data-bd-toast="Opening all branches">View all branches →</button>
                                </div>

                                <div class="bd-table-wrap">
                                    <table class="bd-table">
                                        <thead>
                                            <tr>
                                                <th>Branch Name</th>
                                                <th>City</th>
                                                <th>Status</th>
                                                <th>Revenue Today</th>
                                                <th>Tables</th>
                                                <th>Manager</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Main Branch</td>
                                                <td>New Delhi</td>
                                                <td><span class="status-pill bd-status-active">Active</span></td>
                                                <td>₹1,24,560</td>
                                                <td>34</td>
                                                <td><span class="bd-manager-avatar">RM</span> Rahul Mehta</td>
                                            </tr>
                                            <tr>
                                                <td>Rooftop Branch</td>
                                                <td>New Delhi</td>
                                                <td><span class="status-pill bd-status-active">Active</span></td>
                                                <td>₹78,340</td>
                                                <td>22</td>
                                                <td><span class="bd-manager-avatar">NS</span> Neha Sharma</td>
                                            </tr>
                                            <tr>
                                                <td>City Branch</td>
                                                <td>Gurugram</td>
                                                <td><span class="status-pill bd-status-active">Active</span></td>
                                                <td>₹56,120</td>
                                                <td>18</td>
                                                <td><span class="bd-manager-avatar">AV</span> Arjun Verma</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="bd-summary-foot">
                                    <span>Total Revenue <strong>₹2,59,020</strong></span>
                                    <span>Total Tables <strong>74</strong></span>
                                </div>
                            </article>

                            <article class="bd-card publishing-card" id="bd-publishing-card">
                                <div class="bd-card-head">
                                    <h3>Publishing &amp; Visibility</h3>
                                </div>

                                <div class="bd-check-list">
                                    <div><span class="bd-dot green"></span><b>Customer Page Status</b><strong>Live</strong></div>
                                    <div><span class="bd-dot blue"></span><b>Last Published</b><strong>Today, 10:42 AM</strong></div>
                                    <div><span class="bd-dot orange"></span><b>Draft Changes</b><strong>4</strong></div>
                                    <div><span class="bd-dot green"></span><b>Hero Content Status</b><strong>Ready</strong></div>
                                    <div><span class="bd-dot green"></span><b>Footer Content Status</b><strong>Ready</strong></div>
                                    <div><span class="bd-dot green"></span><b>SEO / Meta Status</b><strong>Good</strong></div>
                                </div>

                                <div class="bd-progress-block">
                                    <div>
                                        <span>Publish Progress</span>
                                        <strong>92%</strong>
                                    </div>
                                    <div class="bd-progress-track">
                                        <span style="width: 92%;"></span>
                                    </div>
                                </div>
                            </article>

                            <article class="bd-card brand-activity-card">
                                <div class="bd-card-head">
                                    <h3>Recent Brand Activity</h3>
                                    <button class="bd-action-link" type="button" data-bd-toast="Opening brand activity">View all</button>
                                </div>

                                <div class="bd-activity-list">
                                    <div class="bd-activity-row">
                                        <span><i data-lucide="image-up"></i></span>
                                        <strong>Hero image updated</strong>
                                        <time>9:12 AM</time>
                                    </div>
                                    <div class="bd-activity-row">
                                        <span><i data-lucide="mail"></i></span>
                                        <strong>Contact email changed</strong>
                                        <time>8:44 AM</time>
                                    </div>
                                    <div class="bd-activity-row">
                                        <span><i data-lucide="store"></i></span>
                                        <strong>Main branch details updated</strong>
                                        <time>Yesterday</time>
                                    </div>
                                    <div class="bd-activity-row">
                                        <span><i data-lucide="rocket"></i></span>
                                        <strong>Customer page published</strong>
                                        <time>Yesterday</time>
                                    </div>
                                    <div class="bd-activity-row">
                                        <span><i data-lucide="badge"></i></span>
                                        <strong>Logo replaced</strong>
                                        <time>2 days ago</time>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <div class="brand-details-bottom-grid">
                            <article class="bd-card customer-data-blocks-card">
                                <div class="bd-card-head">
                                    <h3>Customer Page Data Blocks</h3>
                                    <button class="bd-action-link" type="button" data-bd-toast="Opening customer data block manager">Manage</button>
                                </div>

                                <div class="bd-block-tile-grid">
                                    <div class="bd-block-tile">
                                        <i data-lucide="layout-template"></i>
                                        <strong>Brand Block</strong>
                                        <span>Ready</span>
                                    </div>
                                    <div class="bd-block-tile">
                                        <i data-lucide="image"></i>
                                        <strong>Hero Section</strong>
                                        <span>Ready</span>
                                    </div>
                                    <div class="bd-block-tile">
                                        <i data-lucide="panel-top"></i>
                                        <strong>Service Panel</strong>
                                        <span>Ready</span>
                                    </div>
                                    <div class="bd-block-tile">
                                        <i data-lucide="utensils"></i>
                                        <strong>Menu Discovery</strong>
                                        <span>Ready</span>
                                    </div>
                                    <div class="bd-block-tile">
                                        <i data-lucide="panel-bottom"></i>
                                        <strong>Footer</strong>
                                        <span>Ready</span>
                                    </div>
                                </div>
                            </article>

                            <article class="bd-card compliance-card">
                                <div class="bd-card-head">
                                    <h3>Compliance &amp; Verification</h3>
                                    <button class="bd-action-link" type="button" data-bd-toast="Opening compliance details">View details</button>
                                </div>

                                <div class="bd-compliance-grid">
                                    <div><span>GST Verified</span><strong class="verified"><i data-lucide="badge-check"></i> Verified</strong></div>
                                    <div><span>Phone Verified</span><strong class="verified"><i data-lucide="badge-check"></i> Verified</strong></div>
                                    <div><span>Email Verified</span><strong class="verified"><i data-lucide="badge-check"></i> Verified</strong></div>
                                    <div><span>Payment Profile</span><strong class="pending"><i data-lucide="clock"></i> Pending</strong></div>
                                    <div><span>Domain Connected</span><strong class="verified"><i data-lucide="badge-check"></i> Connected</strong></div>
                                </div>
                            </article>

                            <article class="bd-card brand-alerts-card">
                                <div class="bd-card-head">
                                    <h3>Pending Alerts</h3>
                                    <button class="bd-action-link" type="button" data-bd-toast="Opening brand alerts">View all</button>
                                </div>

                                <div class="bd-alert-list">
                                    <div class="bd-alert-row">
                                        <span><i data-lucide="triangle-alert"></i></span>
                                        <strong>Missing hero alt text</strong>
                                        <b>2</b>
                                    </div>
                                    <div class="bd-alert-row">
                                        <span><i data-lucide="triangle-alert"></i></span>
                                        <strong>Branch phone mismatch</strong>
                                        <b>1</b>
                                    </div>
                                    <div class="bd-alert-row">
                                        <span><i data-lucide="triangle-alert"></i></span>
                                        <strong>Tax profile review due</strong>
                                        <b>1</b>
                                    </div>
                                    <div class="bd-alert-row">
                                        <span><i data-lucide="triangle-alert"></i></span>
                                        <strong>Unpublished draft changes</strong>
                                        <b>4</b>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </section>

                    <div class="edit-brand-modal" id="edit-brand-modal" aria-hidden="true">
                        <div class="edit-brand-backdrop" data-close-brand-modal></div>

                        <div class="edit-brand-dialog" role="dialog" aria-modal="true" aria-labelledby="edit-brand-title">
                            <div class="edit-brand-head">
                                <div>
                                    <h3 id="edit-brand-title">Edit Brand Profile</h3>
                                    <p>Static placeholder now. Backend save can be connected later.</p>
                                </div>
                                <button class="bd-icon-btn" type="button" data-close-brand-modal aria-label="Close edit brand modal">
                                    <i data-lucide="x"></i>
                                </button>
                            </div>

                            <form id="edit-brand-form" class="edit-brand-form">
                                <label>
                                    Brand Name
                                    <input type="text" value="Hotel Delight">
                                </label>

                                <label>
                                    Brand Code
                                    <input type="text" value="HD-REST-001">
                                </label>

                                <label>
                                    Subscription Plan
                                    <select>
                                        <option selected>Starter</option>
                                        <option>Growth</option>
                                        <option>Enterprise</option>
                                    </select>
                                </label>

                                <label>
                                    Status
                                    <select>
                                        <option selected>Active</option>
                                        <option>Draft</option>
                                        <option>Paused</option>
                                    </select>
                                </label>

                                <label>
                                    Currency
                                    <input type="text" value="INR (₹)">
                                </label>

                                <label>
                                    Timezone
                                    <input type="text" value="Asia/Kolkata">
                                </label>

                                <label>
                                    Industry
                                    <input type="text" value="Restaurant / Hospitality">
                                </label>

                                <label>
                                    Founded
                                    <input type="text" value="2019">
                                </label>

                                <label class="wide">
                                    Short Description
                                    <textarea rows="4">Premium dining experience with modern service and multi-branch management.</textarea>
                                </label>

                                <div class="edit-brand-actions">
                                    <button class="bd-btn bd-btn-ghost" type="button" data-close-brand-modal>Cancel</button>
                                    <button class="bd-btn bd-btn-primary" type="submit">Save Profile</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- ================= BRAND DETAILS MODULE END ================= -->




                    <!-- ================= CUSTOMER EXPERIENCE STUDIO MODULE START ================= -->
                    <section class="customer-experience-module" id="customer-experience-module" data-module="cx">

                        <header class="customer-experience-header">
                            <div>
                                <h1>Customer Experience Studio</h1>
                                <p>Design, manage, preview, and publish your customer-facing dining experience.</p>
                            </div>

                            <div class="cx-header-actions">
                                <button class="cx-btn cx-btn-ghost" id="cx-preview-page-btn" type="button">
                                    <i data-lucide="eye"></i>
                                    Preview Customer Page
                                </button>
                                <button class="cx-btn cx-btn-primary" id="cx-save-btn" type="button">
                                    <i data-lucide="save"></i>
                                    Save Changes
                                </button>
                            </div>
                        </header>

                        <div class="cx-filter-bar">
                            <div class="customer-experience-tabs" id="customer-experience-tabs">
                                <button class="active" type="button" data-cx-tab="overview">Overview</button>
                                <button type="button" data-cx-tab="brand-block">Brand Block</button>
                                <button type="button" data-cx-tab="hero">Hero Section</button>
                                <button type="button" data-cx-tab="service">Service Panel</button>
                                <button type="button" data-cx-tab="menu">Menu Discovery</button>
                                <button type="button" data-cx-tab="footer">Footer</button>
                                <button type="button" data-cx-tab="publishing">Publishing</button>
                            </div>

                            <div class="cx-filter-right">
                                <button class="cx-branch-select" type="button">
                                    <i data-lucide="building-2"></i>
                                    All Branches
                                    <i data-lucide="chevron-down"></i>
                                </button>

                                <span class="cx-last-updated">
                                    Last updated: <span id="cx-last-updated">11:24 AM</span>
                                </span>

                                <button class="cx-icon-btn" id="cx-refresh-btn" type="button" aria-label="Refresh customer experience studio">
                                    <i data-lucide="refresh-cw"></i>
                                </button>
                            </div>
                        </div>

                        <div class="customer-experience-kpi-grid">
                            <article class="customer-experience-kpi-card tone-blue">
                                <div class="cx-kpi-icon"><i data-lucide="blocks"></i></div>
                                <div>
                                    <span>Published Blocks</span>
                                    <strong>5</strong>
                                    <small>All sections live</small>
                                </div>
                            </article>

                            <article class="customer-experience-kpi-card tone-orange">
                                <div class="cx-kpi-icon"><i data-lucide="file-pen-line"></i></div>
                                <div>
                                    <span>Draft Changes</span>
                                    <strong>4</strong>
                                    <small>Needs review</small>
                                </div>
                            </article>

                            <article class="customer-experience-kpi-card tone-green">
                                <div class="cx-kpi-icon"><i data-lucide="heart-pulse"></i></div>
                                <div>
                                    <span>Page Health</span>
                                    <strong>96%</strong>
                                    <small>Excellent</small>
                                </div>
                            </article>

                            <article class="customer-experience-kpi-card tone-green">
                                <div class="cx-kpi-icon"><i data-lucide="badge-check"></i></div>
                                <div>
                                    <span>Preview Ready</span>
                                    <strong>Yes</strong>
                                    <small>All good</small>
                                </div>
                            </article>

                            <article class="customer-experience-kpi-card tone-blue">
                                <div class="cx-kpi-icon"><i data-lucide="images"></i></div>
                                <div>
                                    <span>Hero Assets</span>
                                    <strong>3</strong>
                                    <small>Images &amp; videos</small>
                                </div>
                            </article>

                            <article class="customer-experience-kpi-card tone-orange">
                                <div class="cx-kpi-icon"><i data-lucide="triangle-alert"></i></div>
                                <div>
                                    <span>Pending Review</span>
                                    <strong>2</strong>
                                    <small>Requires action</small>
                                </div>
                            </article>

                            <article class="customer-experience-kpi-card tone-blue">
                                <div class="cx-kpi-icon"><i data-lucide="globe-2"></i></div>
                                <div>
                                    <span>Live Pages</span>
                                    <strong>3</strong>
                                    <small>Live on web</small>
                                </div>
                            </article>

                            <article class="customer-experience-kpi-card tone-red">
                                <div class="cx-kpi-icon"><i data-lucide="calendar-check"></i></div>
                                <div>
                                    <span>Last Publish</span>
                                    <strong>Today</strong>
                                    <small>10:42 AM</small>
                                </div>
                            </article>
                        </div>

                        <div class="customer-experience-layout">
                            <article class="cx-card page-content-blocks-card" id="cx-page-content-blocks-card">
                                <div class="cx-card-head">
                                    <div>
                                        <h3>Page Content Blocks</h3>
                                        <p>Customer page sections mapped to brand customer data JSON.</p>
                                    </div>
                                </div>

                                <div class="cx-block-list">
                                    <div class="block-row">
                                        <span class="cx-row-icon blue"><i data-lucide="badge-info"></i></span>
                                        <div class="block-main">
                                            <div>
                                                <strong>Brand Block</strong>
                                                <span class="status-pill status-ready">Ready</span>
                                            </div>
                                            <p>Restaurant identity, tagline, and brand message</p>
                                        </div>
                                        <button class="cx-action-btn" type="button" data-cx-edit="Brand Block">Edit</button>
                                    </div>

                                    <div class="block-row">
                                        <span class="cx-row-icon blue"><i data-lucide="layout-template"></i></span>
                                        <div class="block-main">
                                            <div>
                                                <strong>Hero Section</strong>
                                                <span class="status-pill status-ready">Ready</span>
                                            </div>
                                            <p>Hero banner, headline, description &amp; CTAs</p>
                                        </div>
                                        <button class="cx-action-btn" type="button" data-cx-edit="Hero Section">Edit</button>
                                    </div>

                                    <div class="block-row">
                                        <span class="cx-row-icon purple"><i data-lucide="message-square-heart"></i></span>
                                        <div class="block-main">
                                            <div>
                                                <strong>Service Panel</strong>
                                                <span class="status-pill status-ready">Ready</span>
                                            </div>
                                            <p>Customer service shortcuts and quick actions</p>
                                        </div>
                                        <button class="cx-action-btn" type="button" data-cx-edit="Service Panel">Edit</button>
                                    </div>

                                    <div class="block-row">
                                        <span class="cx-row-icon teal"><i data-lucide="book-open"></i></span>
                                        <div class="block-main">
                                            <div>
                                                <strong>Menu Discovery</strong>
                                                <span class="status-pill status-ready">Ready</span>
                                            </div>
                                            <p>Menu highlights, categories &amp; bestsellers</p>
                                        </div>
                                        <button class="cx-action-btn" type="button" data-cx-edit="Menu Discovery">Edit</button>
                                    </div>

                                    <div class="block-row">
                                        <span class="cx-row-icon blue"><i data-lucide="panel-bottom"></i></span>
                                        <div class="block-main">
                                            <div>
                                                <strong>Footer</strong>
                                                <span class="status-pill status-ready">Ready</span>
                                            </div>
                                            <p>Footer info, contact details &amp; social links</p>
                                        </div>
                                        <button class="cx-action-btn" type="button" data-cx-edit="Footer Content">Edit</button>
                                    </div>
                                </div>

                                <div class="cx-card-foot">
                                    <button class="cx-link-btn" type="button" data-cx-toast="Opening all content blocks">Manage all blocks →</button>
                                </div>
                            </article>

                            <article class="cx-card live-preview-card" id="cx-live-preview-card">
                                <div class="cx-card-head">
                                    <div>
                                        <h3>Live Customer Page Preview</h3>
                                        <p>Current customer-facing page layout and content state.</p>
                                    </div>
                                    <button class="cx-link-btn" type="button" data-cx-toast="Customer page preview opened">Open in new tab ↗</button>
                                </div>

                                <div class="preview-frame">
                                    <div class="cx-browser-bar">
                                        <span></span><span></span><span></span>
                                        <small>customer.hoteldelight.in/table/T01</small>
                                    </div>

                                    <div class="cx-preview-page">
                                        <div class="cx-preview-hero" style="background-image: linear-gradient(90deg, rgba(2,6,23,.82), rgba(2,6,23,.34)), url('assets/restaurant-banner.jpg');">
                                            <div class="cx-preview-nav">
                                                <div class="cx-preview-logo">
                                                    <img src="assets/hd-logo.png" alt="Hotel Delight logo">
                                                    <span>HOTEL DELIGHT<br><small>Restaurant</small></span>
                                                </div>
                                                <nav>
                                                    <a>Home</a>
                                                    <a>Menu</a>
                                                    <a>Our Story</a>
                                                    <a>Contact</a>
                                                </nav>
                                                <button>BOOK YOUR TABLE</button>
                                            </div>

                                            <div class="cx-preview-copy">
                                                <h2>Indulge in<br><span>Exceptional Dining</span></h2>
                                                <p>Exquisite flavors, warm ambiance, and impeccable service for an unforgettable dining experience.</p>
                                                <div class="cx-preview-ctas">
                                                    <button>View Menu</button>
                                                    <button>Our Story</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="cx-preview-service-row">
                                            <div><i data-lucide="phone-call"></i><strong>Call Waiter</strong><small>Get immediate help</small></div>
                                            <div><i data-lucide="glass-water"></i><strong>Water Request</strong><small>Request water</small></div>
                                            <div><i data-lucide="route"></i><strong>Track Order</strong><small>Live order status</small></div>
                                            <div><i data-lucide="receipt-text"></i><strong>Bill Request</strong><small>Request your bill</small></div>
                                        </div>

                                        <div class="cx-preview-menu">
                                            <h3>Explore Our Menu</h3>
                                            <p>Curated dishes made with passion and the finest ingredients.</p>
                                            <div class="cx-preview-dishes">
                                                <span></span><span></span><span></span><span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <div class="cx-config-grid">
                            <article class="cx-card hero-config-card" id="cx-hero-config-card">
                                <div class="cx-card-head">
                                    <h3>Hero Section Configuration</h3>
                                    <button class="cx-action-btn" type="button" data-cx-edit="Hero Section">Edit</button>
                                </div>

                                <div class="cx-info-grid">
                                    <div class="cx-info-row"><span>Badge</span><strong>Welcome to Hotel Delight</strong></div>
                                    <div class="cx-info-row"><span>Title Line 1</span><strong>Indulge in</strong></div>
                                    <div class="cx-info-row"><span>Highlighted Title</span><strong>Exceptional Dining</strong></div>
                                    <div class="cx-info-row wide"><span>Description</span><strong>Exquisite flavors, warm ambiance, and impeccable service for an unforgettable dining experience.</strong></div>
                                    <div class="cx-info-row"><span>Primary CTA</span><strong><em class="cx-mini-cta primary">View Menu</em></strong></div>
                                    <div class="cx-info-row"><span>Secondary CTA</span><strong><em class="cx-mini-cta">Our Story</em></strong></div>
                                    <div class="cx-info-row wide">
                                        <span>Hero Background</span>
                                        <strong><img class="cx-hero-thumb" src="assets/restaurant-banner.jpg" alt="Hero background"></strong>
                                    </div>
                                </div>
                            </article>

                            <article class="cx-card brand-block-card" id="cx-brand-block-card">
                                <div class="cx-card-head">
                                    <h3>Brand Block</h3>
                                    <button class="cx-action-btn" type="button" data-cx-edit="Brand Block">Edit</button>
                                </div>
                                <div class="cx-info-list">
                                    <div><span>Kicker</span><strong>Fine Dining Experience</strong></div>
                                    <div><span>Subtitle</span><strong>Where Every Meal Tells a Story</strong></div>
                                </div>
                            </article>

                            <article class="cx-card service-panel-card" id="cx-service-panel-card">
                                <div class="cx-card-head">
                                    <h3>Service Panel</h3>
                                    <button class="cx-action-btn" type="button" data-cx-edit="Service Panel">Edit</button>
                                </div>
                                <div class="cx-info-list">
                                    <div><span>Badge</span><strong>At Your Service</strong></div>
                                    <div><span>Title</span><strong>We’re here to make your dining perfect</strong></div>
                                    <div><span>Description</span><strong>Quick actions to enhance your experience.</strong></div>
                                    <div class="cx-action-chips">
                                        <span><i data-lucide="phone-call"></i>Call Waiter</span>
                                        <span><i data-lucide="glass-water"></i>Water Request</span>
                                        <span><i data-lucide="route"></i>Track Order</span>
                                        <span><i data-lucide="receipt-text"></i>Bill Request</span>
                                    </div>
                                </div>
                            </article>

                            <article class="cx-card menu-discovery-card" id="cx-menu-discovery-card">
                                <div class="cx-card-head">
                                    <h3>Menu Discovery</h3>
                                    <button class="cx-action-btn" type="button" data-cx-edit="Menu Discovery">Edit</button>
                                </div>
                                <div class="cx-info-list">
                                    <div><span>Kicker</span><strong>Taste the Best</strong></div>
                                    <div><span>Badge</span><strong>Chef’s Special</strong></div>
                                    <div><span>Title</span><strong>Explore Our Menu</strong></div>
                                    <div><span>Description</span><strong>Curated dishes made with passion and the finest ingredients.</strong></div>
                                </div>
                            </article>
                        </div>

                        <div class="cx-status-grid">
                            <article class="cx-card footer-content-card" id="cx-footer-content-card">
                                <div class="cx-card-head">
                                    <h3>Footer Content</h3>
                                    <button class="cx-action-btn" type="button" data-cx-edit="Footer Content">Edit</button>
                                </div>
                                <div class="cx-info-list">
                                    <div><span>Footer Description</span><strong>Thank you for dining with us.<br>We look forward to serving you again.</strong></div>
                                    <div><span>Contact Info Visible</span><strong><span class="cx-dot green"></span>Yes</strong></div>
                                    <div><span>Social Links Visible</span><strong><span class="cx-dot green"></span>Yes</strong></div>
                                    <div><span>Policies Visible</span><strong><span class="cx-dot green"></span>Yes</strong></div>
                                </div>
                            </article>

                            <article class="cx-card publishing-visibility-card" id="cx-publishing-card">
                                <div class="cx-card-head">
                                    <h3>Publishing &amp; Visibility</h3>
                                </div>
                                <div class="cx-check-list">
                                    <div><span><i class="cx-dot green"></i>Page Status</span><strong>Live</strong></div>
                                    <div><span><i class="cx-dot orange"></i>Draft Changes</span><strong>4</strong></div>
                                    <div><span><i class="cx-dot green"></i>Preview Ready</span><strong>Yes</strong></div>
                                    <div><span><i class="cx-dot green"></i>SEO Meta</span><strong>Good</strong></div>
                                    <div><span><i class="cx-dot green"></i>Mobile Preview</span><strong>Ready</strong></div>
                                </div>
                                <div class="cx-progress-row">
                                    <span>Publish Progress</span>
                                    <strong>92%</strong>
                                </div>
                                <div class="cx-progress"><span style="width:92%"></span></div>
                            </article>

                            <article class="cx-card studio-activity-card">
                                <div class="cx-card-head">
                                    <h3>Recent Studio Activity</h3>
                                    <button class="cx-link-btn" type="button" data-cx-toast="Opening studio activity">View all</button>
                                </div>
                                <div class="cx-activity-list">
                                    <div><span class="cx-row-icon blue"><i data-lucide="image-up"></i></span><strong>Hero content updated</strong><time>9:12 AM</time></div>
                                    <div><span class="cx-row-icon purple"><i data-lucide="mouse-pointer-click"></i></span><strong>CTA button changed</strong><time>8:44 AM</time></div>
                                    <div><span class="cx-row-icon teal"><i data-lucide="panel-bottom"></i></span><strong>Footer content saved</strong><time>Yesterday</time></div>
                                    <div><span class="cx-row-icon blue"><i data-lucide="eye"></i></span><strong>Customer page preview opened</strong><time>Yesterday</time></div>
                                    <div><span class="cx-row-icon green"><i data-lucide="badge-check"></i></span><strong>Publish completed</strong><time>2 days ago</time></div>
                                </div>
                            </article>

                            <article class="cx-card quality-alerts-card">
                                <div class="cx-card-head">
                                    <h3>Content Quality Alerts</h3>
                                    <button class="cx-link-btn" type="button" data-cx-toast="Opening content quality alerts">View all</button>
                                </div>
                                <div class="cx-alert-list">
                                    <div><span><i data-lucide="triangle-alert"></i>Missing alt text</span><b>2</b></div>
                                    <div><span><i data-lucide="triangle-alert"></i>Draft not published</span><b>1</b></div>
                                    <div><span><i data-lucide="triangle-alert"></i>CTA text too long</span><b>1</b></div>
                                    <div><span><i data-lucide="triangle-alert"></i>Footer social links hidden</span><b>2</b></div>
                                </div>
                            </article>
                        </div>

                        <div class="cx-bottom-grid">
                            <article class="cx-card preview-modes-card">
                                <div class="cx-card-head">
                                    <h3>Preview Modes</h3>
                                </div>
                                <div class="cx-tile-grid three">
                                    <div><i data-lucide="monitor"></i><strong>Desktop Preview</strong><span>Ready</span></div>
                                    <div><i data-lucide="tablet"></i><strong>Tablet Preview</strong><span>Ready</span></div>
                                    <div><i data-lucide="smartphone"></i><strong>Mobile Preview</strong><span>Ready</span></div>
                                </div>
                            </article>

                            <article class="cx-card customer-journey-card">
                                <div class="cx-card-head">
                                    <h3>Customer Journey Elements</h3>
                                </div>
                                <div class="cx-tile-grid five">
                                    <div><i data-lucide="layout-template"></i><strong>Hero</strong><span>Ready</span></div>
                                    <div><i data-lucide="message-square-heart"></i><strong>Service Requests</strong><span>Ready</span></div>
                                    <div><i data-lucide="book-open"></i><strong>Menu Discovery</strong><span>Ready</span></div>
                                    <div><i data-lucide="mouse-pointer-click"></i><strong>CTAs</strong><span>Ready</span></div>
                                    <div><i data-lucide="panel-bottom"></i><strong>Footer</strong><span>Ready</span></div>
                                </div>
                            </article>
                        </div>
                    </section>

                    <div class="studio-edit-modal" id="studio-edit-modal" aria-hidden="true">
                        <div class="studio-edit-backdrop" data-close-studio-modal></div>

                        <div class="studio-edit-dialog" role="dialog" aria-modal="true" aria-labelledby="studio-edit-title">
                            <div class="studio-edit-head">
                                <div>
                                    <h3 id="studio-edit-title">Edit Hero Section</h3>
                                    <p>Static placeholder editor. Backend can be connected later through customer page JSON.</p>
                                </div>
                                <button class="cx-icon-btn" type="button" data-close-studio-modal aria-label="Close editor">
                                    <i data-lucide="x"></i>
                                </button>
                            </div>

                            <form id="studio-edit-form" class="studio-edit-form">
                                <label>
                                    Badge
                                    <input type="text" value="Welcome to Hotel Delight">
                                </label>

                                <label>
                                    Title Line 1
                                    <input type="text" value="Indulge in">
                                </label>

                                <label>
                                    Highlighted Title
                                    <input type="text" value="Exceptional Dining">
                                </label>

                                <label class="wide">
                                    Description
                                    <textarea rows="4">Exquisite flavors, warm ambiance, and impeccable service for an unforgettable dining experience.</textarea>
                                </label>

                                <label>
                                    Primary CTA
                                    <input type="text" value="View Menu">
                                </label>

                                <label>
                                    Secondary CTA
                                    <input type="text" value="Our Story">
                                </label>

                                <label class="wide">
                                    Hero Background
                                    <input type="text" value="assets/restaurant-banner.jpg">
                                </label>

                                <div class="studio-edit-actions">
                                    <button class="cx-btn cx-btn-ghost" type="button" data-close-studio-modal>Cancel</button>
                                    <button class="cx-btn cx-btn-primary" type="submit">Save Section</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- ================= CUSTOMER EXPERIENCE STUDIO MODULE END ================= -->




                    <!-- ================= SETTINGS MODULE START ================= -->
                    <section class="settings-module" id="settings-module" data-module="settings">

                        <header class="settings-header">
                            <div>
                                <h1>Settings</h1>
                                <p>Configure platform preferences, payments, notifications, security, and operational controls.</p>
                            </div>

                            <div class="settings-header-actions">
                                <button class="settings-btn settings-btn-ghost" id="settings-preview-btn" type="button">
                                    <i data-lucide="eye"></i>
                                    Preview Changes
                                </button>
                                <button class="settings-btn settings-btn-primary" id="settings-save-btn" type="button">
                                    <i data-lucide="save"></i>
                                    Save Settings
                                </button>
                            </div>
                        </header>

                        <div class="settings-filter-bar">
                            <div class="settings-tabs" id="settings-tabs">
                                <button class="active" data-settings-tab="overview" type="button">Overview</button>
                                <button data-settings-tab="general" type="button">General</button>
                                <button data-settings-tab="notifications" type="button">Notifications</button>
                                <button data-settings-tab="payments" type="button">Payments</button>
                                <button data-settings-tab="security" type="button">Security</button>
                                <button data-settings-tab="appearance" type="button">Appearance</button>
                                <button data-settings-tab="advanced" type="button">Advanced</button>
                            </div>
                        </div>

                        <div class="settings-kpi-grid">
                            <article class="settings-kpi-card tone-green">
                                <div class="settings-kpi-icon"><i data-lucide="heart-pulse"></i></div>
                                <div>
                                    <span>Settings Health</span>
                                    <strong>96%</strong>
                                    <small>Excellent</small>
                                </div>
                            </article>

                            <article class="settings-kpi-card tone-green">
                                <div class="settings-kpi-icon"><i data-lucide="shield-check"></i></div>
                                <div>
                                    <span>Security Score</span>
                                    <strong>High</strong>
                                    <small>Strong protection</small>
                                </div>
                            </article>

                            <article class="settings-kpi-card tone-blue">
                                <div class="settings-kpi-icon"><i data-lucide="bell"></i></div>
                                <div>
                                    <span>Notifications</span>
                                    <strong>Enabled</strong>
                                    <small>All systems active</small>
                                </div>
                            </article>

                            <article class="settings-kpi-card tone-green">
                                <div class="settings-kpi-icon"><i data-lucide="credit-card"></i></div>
                                <div>
                                    <span>Payment Setup</span>
                                    <strong>Ready</strong>
                                    <small>Accepting payments</small>
                                </div>
                            </article>

                            <article class="settings-kpi-card tone-purple">
                                <div class="settings-kpi-icon"><i data-lucide="volume-2"></i></div>
                                <div>
                                    <span>Sound Alerts</span>
                                    <strong>On</strong>
                                    <small>Audible alerts active</small>
                                </div>
                            </article>

                            <article class="settings-kpi-card tone-blue">
                                <div class="settings-kpi-icon"><i data-lucide="cloud-upload"></i></div>
                                <div>
                                    <span>Last Backup</span>
                                    <strong>Today</strong>
                                    <small>10:42 AM</small>
                                </div>
                            </article>

                            <article class="settings-kpi-card tone-blue">
                                <div class="settings-kpi-icon"><i data-lucide="users-round"></i></div>
                                <div>
                                    <span>Active Sessions</span>
                                    <strong>5</strong>
                                    <small>Staff online now</small>
                                </div>
                            </article>

                            <article class="settings-kpi-card tone-purple">
                                <div class="settings-kpi-icon"><i data-lucide="puzzle"></i></div>
                                <div>
                                    <span>Integrations</span>
                                    <strong>3</strong>
                                    <small>Connected</small>
                                </div>
                            </article>
                        </div>

                        <div class="settings-grid">
                            <article class="settings-card general-preferences-card" data-settings-section="general">
                                <div class="settings-card-head">
                                    <h3><span>1.</span> General Preferences</h3>
                                    <button class="settings-link-btn" type="button" data-settings-toast="Opening General Preferences editor">Edit</button>
                                </div>

                                <div class="setting-list">
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="clock-3"></i><span>Brand Timezone</span></div>
                                        <select class="settings-select">
                                            <option>Asia/Kolkata (UTC +05:30)</option>
                                        </select>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="indian-rupee"></i><span>Currency</span></div>
                                        <select class="settings-select">
                                            <option>INR (₹) - Indian Rupee</option>
                                        </select>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="store"></i><span>Default Branch</span></div>
                                        <select class="settings-select">
                                            <option>Hotel Delight - Main Outlet</option>
                                        </select>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="languages"></i><span>Language</span></div>
                                        <select class="settings-select">
                                            <option>English (India)</option>
                                        </select>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="timer"></i><span>Business Hours Mode</span></div>
                                        <span class="settings-status-pill status-success">Open 24 Hours</span>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="refresh-cw"></i><span>Auto-refresh Interval</span></div>
                                        <select class="settings-select">
                                            <option>60 seconds</option>
                                        </select>
                                    </div>
                                </div>

                                <button class="settings-full-primary" id="settings-save-preferences" type="button">Save Preferences</button>
                            </article>

                            <article class="settings-card notifications-card" data-settings-section="notifications">
                                <div class="settings-card-head">
                                    <h3><span>2.</span> Notifications &amp; Alerts</h3>
                                    <button class="settings-link-btn" type="button" data-settings-toast="Opening Notifications editor">Edit</button>
                                </div>

                                <div class="setting-list compact">
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="bell-ring"></i><span>Order Alerts</span></div><button class="settings-toggle is-on" type="button" aria-label="Toggle order alerts"></button>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="badge-indian-rupee"></i><span>Payment Alerts</span></div><button class="settings-toggle is-on" type="button" aria-label="Toggle payment alerts"></button>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="concierge-bell"></i><span>Customer Request Alerts</span></div><button class="settings-toggle is-on" type="button" aria-label="Toggle customer request alerts"></button>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="users"></i><span>Staff Alerts</span></div><button class="settings-toggle is-on" type="button" aria-label="Toggle staff alerts"></button>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="monitor"></i><span>Desktop Alerts</span></div><button class="settings-toggle is-on" type="button" aria-label="Toggle desktop alerts"></button>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="mail"></i><span>Email Alerts</span></div><button class="settings-toggle is-on" type="button" aria-label="Toggle email alerts"></button>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="message-square-text"></i><span>SMS Alerts</span></div><button class="settings-toggle" type="button" aria-label="Toggle sms alerts"></button>
                                    </div>
                                </div>

                                <div class="settings-inline-actions">
                                    <select class="settings-select">
                                        <option>Chime High</option>
                                        <option>Soft Bell</option>
                                        <option>Digital Ping</option>
                                    </select>
                                    <button class="settings-mini-btn" id="settings-test-sound" type="button"><i data-lucide="play"></i> Test Sound</button>
                                </div>
                            </article>

                            <article class="settings-card payment-settings-card" data-settings-section="payments">
                                <div class="settings-card-head">
                                    <h3><span>3.</span> Payment &amp; Billing Settings</h3>
                                    <button class="settings-link-btn" type="button" data-settings-toast="Opening Payment Settings editor">Edit</button>
                                </div>

                                <div class="setting-list compact">
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="scan-line"></i><span>UPI Payments Enabled</span></div><button class="settings-toggle is-on" type="button"></button>
                                    </div>
                                    <div class="setting-row input-row">
                                        <div class="setting-label"><span>UPI ID</span></div>
                                        <div class="settings-input-copy"><input value="hoteldelight@upi" readonly><button type="button" data-settings-toast="UPI ID copied"><i data-lucide="copy"></i></button></div>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><span>UPI QR Status</span></div><span class="settings-status-pill status-success">Ready</span>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="wallet"></i><span>Cash Payments Enabled</span></div><button class="settings-toggle is-on" type="button"></button>
                                    </div>
                                    <div class="setting-row split-inputs">
                                        <div class="setting-label"><i data-lucide="percent"></i><span>Tax / Service Charge</span></div><input value="5.00" readonly><input value="10.00" readonly>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="badge-check"></i><span>Auto close paid bills</span></div><button class="settings-toggle is-on" type="button"></button>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="armchair"></i><span>Auto release table after payment</span></div><button class="settings-toggle is-on" type="button"></button>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="user-check"></i><span>Manager verification required</span></div><button class="settings-toggle is-on" type="button"></button>
                                    </div>
                                </div>
                            </article>

                            <article class="settings-card security-card" data-settings-section="security">
                                <div class="settings-card-head">
                                    <h3><span>4.</span> Security &amp; Access</h3>
                                    <button class="settings-link-btn" type="button" data-settings-toast="Opening Security editor">Edit</button>
                                </div>

                                <div class="setting-list compact">
                                    <div class="setting-row">
                                        <div class="setting-label"><i data-lucide="shield"></i><span>Two-Factor Authentication</span></div><button class="settings-toggle is-on" type="button"></button>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><span>Session Timeout</span></div><select class="settings-select">
                                            <option>30 minutes</option>
                                            <option>1 hour</option>
                                            <option>2 hours</option>
                                        </select>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><span>Password Policy</span></div><span class="settings-status-pill status-success">Strong <i data-lucide="check-circle"></i></span>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><span>IP Restriction</span></div><button class="settings-toggle" type="button"></button>
                                    </div>
                                </div>

                                <div class="security-device-box">
                                    <div>
                                        <span>Active Devices / Sessions</span>
                                        <strong>5</strong>
                                        <div class="device-icons"><i data-lucide="monitor"></i><i data-lucide="smartphone"></i><i data-lucide="tablet"></i><small>+2</small></div>
                                    </div>
                                    <button class="settings-link-btn" type="button" data-settings-toast="Opening active sessions">View all</button>
                                </div>

                                <div class="security-check-box">
                                    <span><i data-lucide="shield-check"></i></span>
                                    <div><strong>Recent Security Check</strong><small>Today, 09:12 AM</small></div>
                                    <b>Secure</b>
                                </div>
                            </article>

                            <article class="settings-card appearance-card" data-settings-section="appearance">
                                <div class="settings-card-head">
                                    <h3><span>5.</span> Appearance &amp; Experience</h3>
                                    <button class="settings-link-btn" type="button" data-settings-toast="Opening Appearance editor">Edit</button>
                                </div>

                                <div class="setting-list compact">
                                    <div class="setting-row segmented-row">
                                        <div class="setting-label"><span>Theme Mode</span></div>
                                        <div class="settings-segmented" id="settings-theme-mode">
                                            <button class="active" data-mode="light" type="button">Light</button>
                                            <button data-mode="dark" type="button">Dark</button>
                                            <button data-mode="system" type="button">System</button>
                                        </div>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><span>Compact Mode</span></div><button class="settings-toggle is-on" type="button"></button>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><span>Animations</span></div><button class="settings-toggle is-on" type="button"></button>
                                    </div>
                                    <div class="setting-row color-row">
                                        <div class="setting-label"><span>Accent Color</span></div>
                                        <div class="settings-color-swatches">
                                            <button class="active sw-blue" type="button" aria-label="Blue"></button>
                                            <button class="sw-green" type="button" aria-label="Green"></button>
                                            <button class="sw-purple" type="button" aria-label="Purple"></button>
                                            <button class="sw-orange" type="button" aria-label="Orange"></button>
                                            <button class="sw-red" type="button" aria-label="Red"></button>
                                            <button class="sw-teal" type="button" aria-label="Teal"></button>
                                            <button class="sw-gray" type="button" aria-label="Gray"></button>
                                            <button class="sw-plus" type="button" aria-label="Add color">+</button>
                                        </div>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><span>Dashboard Density</span></div><select class="settings-select">
                                            <option>Comfortable</option>
                                            <option>Compact</option>
                                            <option>Spacious</option>
                                        </select>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><span>Customer Page Default Preview</span></div><select class="settings-select">
                                            <option>Desktop</option>
                                            <option>Tablet</option>
                                            <option>Mobile</option>
                                        </select>
                                    </div>
                                </div>
                            </article>

                            <article class="settings-card customer-defaults-card" data-settings-section="appearance">
                                <div class="settings-card-head">
                                    <h3><span>6.</span> Customer Experience Defaults</h3>
                                    <button class="settings-link-btn" type="button" data-settings-toast="Opening Customer Experience Defaults editor">Edit</button>
                                </div>

                                <div class="setting-list compact">
                                    <div class="setting-row">
                                        <div class="setting-label"><span>Default Hero Visibility</span></div><button class="settings-toggle is-on" type="button"></button>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><span>Service Panel Enabled</span></div><button class="settings-toggle is-on" type="button"></button>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><span>Menu Discovery Enabled</span></div><button class="settings-toggle is-on" type="button"></button>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><span>Footer Visibility</span></div><button class="settings-toggle is-on" type="button"></button>
                                    </div>
                                    <div class="setting-row">
                                        <div class="setting-label"><span>Show Social Links</span></div><button class="settings-toggle is-on" type="button"></button>
                                    </div>
                                </div>

                                <div class="settings-segmented wide" id="settings-preview-mode">
                                    <button class="active" type="button">Desktop</button>
                                    <button type="button">Tablet</button>
                                    <button type="button">Mobile</button>
                                </div>
                            </article>

                            <article class="settings-card integrations-card" data-settings-section="advanced">
                                <div class="settings-card-head">
                                    <h3><span>7.</span> Integrations &amp; System</h3>
                                    <button class="settings-link-btn" type="button" data-settings-toast="Opening Integrations editor">Edit</button>
                                </div>

                                <div class="integration-list">
                                    <div><span><i data-lucide="badge-check"></i> POS Sync</span><b class="ok">Connected <i data-lucide="check-circle"></i></b></div>
                                    <div><span><i data-lucide="lock-keyhole"></i> Payment Gateway</span><b class="ok">Connected <i data-lucide="check-circle"></i></b></div>
                                    <div><span><i data-lucide="mail"></i> Email Service</span><b class="ok">Connected <i data-lucide="check-circle"></i></b></div>
                                    <div><span><i data-lucide="bell"></i> Notification Service</span><b class="ok">Connected <i data-lucide="check-circle"></i></b></div>
                                    <div><span><i data-lucide="cloud"></i> Backup Sync</span><b class="warn">Warning <i data-lucide="triangle-alert"></i></b></div>
                                </div>

                                <div class="settings-card-foot">
                                    <button class="settings-link-btn" type="button" data-settings-toast="Opening integration manager">Manage Integrations →</button>
                                </div>
                            </article>

                            <article class="settings-card audit-activity-card" data-settings-section="advanced">
                                <div class="settings-card-head">
                                    <h3><span>8.</span> Audit / Recent Settings Activity</h3>
                                    <button class="settings-link-btn" type="button" data-settings-toast="Opening settings audit log">View all</button>
                                </div>

                                <div class="settings-activity-list">
                                    <div><span class="act green"><i data-lucide="credit-card"></i></span>
                                        <p><strong>UPI ID updated</strong><small>by Praful Shah</small></p><time>10:42 AM</time>
                                    </div>
                                    <div><span class="act purple"><i data-lucide="volume-2"></i></span>
                                        <p><strong>Notification sound changed</strong><small>by Praful Shah</small></p><time>10:15 AM</time>
                                    </div>
                                    <div><span class="act blue"><i data-lucide="clock"></i></span>
                                        <p><strong>Session timeout updated</strong><small>by Praful Shah</small></p><time>09:58 AM</time>
                                    </div>
                                    <div><span class="act blue"><i data-lucide="cloud-upload"></i></span>
                                        <p><strong>Backup completed</strong><small>by System</small></p><time>09:30 AM</time>
                                    </div>
                                    <div><span class="act orange"><i data-lucide="percent"></i></span>
                                        <p><strong>Tax rate updated</strong><small>by Praful Shah</small></p><time>Yesterday</time>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <div class="settings-lower-grid">
                            <article class="settings-card backup-export-card" data-settings-section="advanced">
                                <div class="settings-card-head">
                                    <h3><span>9.</span> Data Backup &amp; Export</h3>
                                </div>

                                <div class="backup-grid">
                                    <div class="backup-info">
                                        <div class="backup-row"><i data-lucide="clock"></i>
                                            <div><span>Last Backup</span><strong>May 18, 2025 10:42 AM</strong></div><b>Successful</b>
                                        </div>
                                        <div class="backup-row"><i data-lucide="cloud-cog"></i>
                                            <div><span>Restore Point Available</span><strong>May 18, 2025 10:42 AM</strong></div>
                                        </div>
                                    </div>
                                    <div class="backup-actions">
                                        <div class="setting-row backup-toggle">
                                            <div class="setting-label"><span>Schedule Backup</span></div><button class="settings-toggle is-on" type="button"></button>
                                        </div>
                                        <select class="settings-select">
                                            <option>Daily at 02:00 AM</option>
                                            <option>Weekly on Monday</option>
                                            <option>Monthly</option>
                                        </select>
                                        <div class="settings-inline-actions no-pad">
                                            <button class="settings-mini-btn" id="settings-export-settings" type="button">Export Settings</button>
                                            <button class="settings-btn settings-btn-primary" id="settings-download-config" type="button">Download Configuration</button>
                                        </div>
                                    </div>
                                </div>
                            </article>

                            <article class="settings-card danger-zone-card" data-settings-section="advanced">
                                <div class="settings-card-head">
                                    <h3><span>10.</span> Quick Actions / Danger Zone</h3>
                                </div>

                                <div class="danger-action-grid">
                                    <button class="danger-action neutral" id="settings-clear-cache" type="button">
                                        <i data-lucide="trash-2"></i>
                                        <strong>Clear Cache</strong>
                                        <small>Free up system cache</small>
                                    </button>
                                    <button class="danger-action neutral" id="settings-test-notification" type="button">
                                        <i data-lucide="bell"></i>
                                        <strong>Send Test Notification</strong>
                                        <small>Test alerts to your devices</small>
                                    </button>
                                    <button class="danger-action neutral" id="settings-reset-drafts" type="button">
                                        <i data-lucide="rotate-cw"></i>
                                        <strong>Reset Draft Changes</strong>
                                        <small>Discard unsaved changes</small>
                                    </button>
                                    <button class="danger-action danger" id="settings-signout-all" type="button">
                                        <i data-lucide="lock"></i>
                                        <strong>Sign Out All Sessions</strong>
                                        <small>Sign out from all devices</small>
                                    </button>
                                </div>

                                <p class="danger-note">Danger actions are irreversible. Please proceed with caution.</p>
                            </article>
                        </div>

                        <article class="settings-card theme-studio-card" data-settings-section="appearance">
                            <div class="settings-card-head theme-head">
                                <div>
                                    <h3><span>11.</span> Theme Studio</h3>
                                    <p>Choose a theme preset to personalize your dashboard and customer page experience.</p>
                                </div>
                                <button class="settings-link-btn" type="button" data-settings-toast="Opening all themes">View all themes →</button>
                            </div>

                            <div class="theme-preset-grid" id="theme-preset-grid">
                                <article class="theme-preset-card active" data-theme-name="Ocean Blue">
                                    <div class="theme-preview ocean"><span></span><b></b><i></i></div>
                                    <div class="theme-card-body">
                                        <div>
                                            <h4>Ocean Blue</h4>
                                            <p>Clean &amp; Professional</p>
                                        </div>
                                        <div class="theme-palette"><span style="--c:#2563eb"></span><span style="--c:#38bdf8"></span><span style="--c:#06b6d4"></span><span style="--c:#475569"></span><span style="--c:#ffffff"></span></div>
                                        <small>A crisp enterprise palette for operational clarity.</small>
                                        <button type="button">Active</button>
                                    </div>
                                </article>

                                <article class="theme-preset-card" data-theme-name="Forest Green">
                                    <div class="theme-preview forest"><span></span><b></b><i></i></div>
                                    <div class="theme-card-body">
                                        <div>
                                            <h4>Forest Green</h4>
                                            <p>Fresh &amp; Balanced</p>
                                        </div>
                                        <div class="theme-palette"><span style="--c:#064e3b"></span><span style="--c:#10b981"></span><span style="--c:#65a30d"></span><span style="--c:#f7fee7"></span></div>
                                        <small>Natural, calm, and hospitality-friendly.</small>
                                        <button type="button">Apply</button>
                                    </div>
                                </article>

                                <article class="theme-preset-card" data-theme-name="Midnight Dark">
                                    <div class="theme-preview midnight"><span></span><b></b><i></i></div>
                                    <div class="theme-card-body">
                                        <div>
                                            <h4>Midnight Dark</h4>
                                            <p>Sleek &amp; Modern</p>
                                        </div>
                                        <div class="theme-palette"><span style="--c:#020617"></span><span style="--c:#0f172a"></span><span style="--c:#334155"></span><span style="--c:#64748b"></span></div>
                                        <small>Premium dark control-room aesthetic.</small>
                                        <button type="button">Apply</button>
                                    </div>
                                </article>

                                <article class="theme-preset-card" data-theme-name="Royal Purple">
                                    <div class="theme-preview royal"><span></span><b></b><i></i></div>
                                    <div class="theme-card-body">
                                        <div>
                                            <h4>Royal Purple</h4>
                                            <p>Bold &amp; Elegant</p>
                                        </div>
                                        <div class="theme-palette"><span style="--c:#6d28d9"></span><span style="--c:#8b5cf6"></span><span style="--c:#c4b5fd"></span><span style="--c:#f8fafc"></span></div>
                                        <small>Distinctive style for premium branding.</small>
                                        <button type="button">Apply</button>
                                    </div>
                                </article>

                                <article class="theme-preset-card" data-theme-name="Sunset Orange">
                                    <div class="theme-preview sunset"><span></span><b></b><i></i></div>
                                    <div class="theme-card-body">
                                        <div>
                                            <h4>Sunset Orange</h4>
                                            <p>Warm &amp; Energetic</p>
                                        </div>
                                        <div class="theme-palette"><span style="--c:#ea580c"></span><span style="--c:#f59e0b"></span><span style="--c:#92400e"></span><span style="--c:#fff7ed"></span></div>
                                        <small>Warm colors for vibrant restaurant brands.</small>
                                        <button type="button">Apply</button>
                                    </div>
                                </article>

                                <article class="theme-preset-card" data-theme-name="Neutral Gray">
                                    <div class="theme-preview neutral-gray"><span></span><b></b><i></i></div>
                                    <div class="theme-card-body">
                                        <div>
                                            <h4>Neutral Gray</h4>
                                            <p>Minimal &amp; Calm</p>
                                        </div>
                                        <div class="theme-palette"><span style="--c:#111827"></span><span style="--c:#6b7280"></span><span style="--c:#94a3b8"></span><span style="--c:#ffffff"></span></div>
                                        <small>Minimal, clean, and distraction-free.</small>
                                        <button type="button">Apply</button>
                                    </div>
                                </article>
                            </div>

                            <div class="theme-studio-foot">
                                <span>Customize any theme to match your brand identity.</span>
                                <button class="settings-btn settings-btn-ghost" type="button" data-settings-toast="Opening theme customizer">Customize Current Theme</button>
                            </div>
                        </article>
                    </section>

                    <div class="confirm-modal" id="settings-confirm-modal" aria-hidden="true">
                        <div class="confirm-modal-backdrop" data-close-settings-confirm></div>
                        <div class="confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="settings-confirm-title">
                            <div class="confirm-icon"><i data-lucide="log-out"></i></div>
                            <h3 id="settings-confirm-title">Sign out all sessions?</h3>
                            <p>This will sign out all active staff and owner sessions from all devices.</p>
                            <div class="confirm-actions">
                                <button class="settings-btn settings-btn-ghost" type="button" data-close-settings-confirm>Cancel</button>
                                <button class="settings-btn settings-btn-danger" id="settings-confirm-signout" type="button">Sign Out Sessions</button>
                            </div>
                        </div>
                    </div>
                    <!-- ================= SETTINGS MODULE END ================= -->




                    <!-- ================= BRANCH MANAGEMENT MODULE START ================= -->
                    <section class="branch-management-module" id="branch-management-module" data-module="branches">

                        <header class="branch-management-header">
                            <div>
                                <h1>Branch Management</h1>
                                <p>Manage branch network, performance, managers, operational status, and expansion readiness.</p>
                            </div>

                            <div class="branch-header-actions">
                                <button class="branch-btn branch-btn-ghost" id="branch-export-btn" type="button">
                                    <i data-lucide="download"></i>
                                    Export
                                </button>
                                <button class="branch-btn branch-btn-primary" id="open-add-branch-modal" type="button">
                                    <i data-lucide="plus"></i>
                                    Add Branch
                                </button>
                            </div>
                        </header>

                        <div class="branch-filter-bar">
                            <div class="branch-management-tabs" id="branch-management-tabs">
                                <button class="active" data-branch-tab="overview" type="button">Overview</button>
                                <button data-branch-tab="branches" type="button">Branches</button>
                                <button data-branch-tab="managers" type="button">Managers</button>
                                <button data-branch-tab="performance" type="button">Performance</button>
                                <button data-branch-tab="operations" type="button">Operations</button>
                                <button data-branch-tab="expansion" type="button">Expansion</button>
                            </div>

                            <div class="branch-filter-right">
                                <div class="branch-date-filter" id="branch-date-filter">
                                    <button class="active" type="button">Today</button>
                                    <button type="button">Week</button>
                                    <button type="button">Month</button>
                                </div>

                                <button class="branch-select-btn" type="button">
                                    <i data-lucide="building-2"></i>
                                    All Branches
                                    <i data-lucide="chevron-down"></i>
                                </button>

                                <span class="branch-last-updated">Last updated: <span id="branch-last-updated">11:24 AM</span></span>

                                <button class="branch-icon-btn" id="branch-refresh-btn" type="button" aria-label="Refresh branch management">
                                    <i data-lucide="refresh-cw"></i>
                                </button>
                            </div>
                        </div>

                        <div class="branch-kpi-grid">
                            <article class="branch-kpi-card tone-blue">
                                <div class="branch-kpi-icon"><i data-lucide="git-branch"></i></div>
                                <div><span>Total Branches</span><strong>6</strong><small>2 added this year</small></div>
                            </article>
                            <article class="branch-kpi-card tone-green">
                                <div class="branch-kpi-icon"><i data-lucide="circle-check"></i></div>
                                <div><span>Active Branches</span><strong>5</strong><small>1 under setup</small></div>
                            </article>
                            <article class="branch-kpi-card tone-purple">
                                <div class="branch-kpi-icon"><i data-lucide="indian-rupee"></i></div>
                                <div><span>Today Revenue</span><strong>₹2,59,020</strong><small class="up">↑ 12.4% vs yesterday</small></div>
                            </article>
                            <article class="branch-kpi-card tone-blue">
                                <div class="branch-kpi-icon"><i data-lucide="armchair"></i></div>
                                <div><span>Total Tables</span><strong>124</strong><small>89 active now</small></div>
                            </article>
                            <article class="branch-kpi-card tone-green">
                                <div class="branch-kpi-icon"><i data-lucide="user-round-check"></i></div>
                                <div><span>Branch Managers</span><strong>6</strong><small>All assigned</small></div>
                            </article>
                            <article class="branch-kpi-card tone-orange">
                                <div class="branch-kpi-icon"><i data-lucide="star"></i></div>
                                <div><span>Avg Rating</span><strong>4.6</strong><small>Across all branches</small></div>
                            </article>
                            <article class="branch-kpi-card tone-orange">
                                <div class="branch-kpi-icon"><i data-lucide="triangle-alert"></i></div>
                                <div><span>Pending Requests</span><strong>11</strong><small>Needs action</small></div>
                            </article>
                            <article class="branch-kpi-card tone-purple">
                                <div class="branch-kpi-icon"><i data-lucide="target"></i></div>
                                <div><span>Expansion Opportunities</span><strong>3</strong><small>Market shortlisted</small></div>
                            </article>
                        </div>

                        <div class="branch-management-layout">
                            <article class="branch-card branch-directory-card" id="branch-directory-card">
                                <div class="branch-card-head">
                                    <div>
                                        <h3>Branch Directory</h3>
                                        <p>Branch status, managers, tables, revenue, and review actions.</p>
                                    </div>
                                    <button class="branch-icon-btn small" type="button" data-branch-toast="Branch options opened">
                                        <i data-lucide="ellipsis"></i>
                                    </button>
                                </div>

                                <div class="branch-directory-controls">
                                    <div class="branch-search-box">
                                        <i data-lucide="search"></i>
                                        <input id="branch-search-input" type="text" placeholder="Search branches...">
                                    </div>

                                    <select id="branch-status-filter" class="branch-filter-select">
                                        <option value="all">Status: All</option>
                                        <option value="active">Active</option>
                                        <option value="setup">Setup</option>
                                        <option value="review">In Review</option>
                                    </select>

                                    <select id="branch-city-filter" class="branch-filter-select">
                                        <option value="all">City: All</option>
                                        <option value="new-delhi">New Delhi</option>
                                        <option value="gurugram">Gurugram</option>
                                        <option value="noida">Noida</option>
                                        <option value="delhi-ncr">Delhi NCR</option>
                                        <option value="delhi-airport">Delhi Airport</option>
                                    </select>
                                </div>

                                <div class="branch-table-wrap">
                                    <table class="branch-table" id="branch-directory-table">
                                        <thead>
                                            <tr>
                                                <th>Branch</th>
                                                <th>City</th>
                                                <th>Status</th>
                                                <th>Manager</th>
                                                <th>Tables</th>
                                                <th>Revenue Today</th>
                                                <th>Rating</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr data-branch-row data-status="active" data-city="new-delhi" data-search="main branch new delhi active rahul mehta 34 124560 4.8">
                                                <td><strong>Main Branch</strong><small>HD-BR-001</small></td>
                                                <td>New Delhi</td>
                                                <td><span class="status-pill status-active">Active</span></td>
                                                <td><span class="manager-cell"><span class="branch-avatar">RM</span>Rahul Mehta</span></td>
                                                <td>34</td>
                                                <td class="amount">₹1,24,560</td>
                                                <td><span class="rating">★ 4.8</span></td>
                                                <td><button class="action-btn" type="button" data-branch-toast="Opening Main Branch">View</button></td>
                                            </tr>
                                            <tr data-branch-row data-status="active" data-city="new-delhi" data-search="rooftop branch new delhi active neha sharma 22 78340 4.6">
                                                <td><strong>Rooftop Branch</strong><small>HD-BR-002</small></td>
                                                <td>New Delhi</td>
                                                <td><span class="status-pill status-active">Active</span></td>
                                                <td><span class="manager-cell"><span class="branch-avatar">NS</span>Neha Sharma</span></td>
                                                <td>22</td>
                                                <td class="amount">₹78,340</td>
                                                <td><span class="rating">★ 4.6</span></td>
                                                <td><button class="action-btn" type="button" data-branch-toast="Opening Rooftop Branch">View</button></td>
                                            </tr>
                                            <tr data-branch-row data-status="active" data-city="gurugram" data-search="city branch gurugram active arjun verma 18 56120 4.5">
                                                <td><strong>City Branch</strong><small>HD-BR-003</small></td>
                                                <td>Gurugram</td>
                                                <td><span class="status-pill status-active">Active</span></td>
                                                <td><span class="manager-cell"><span class="branch-avatar">AV</span>Arjun Verma</span></td>
                                                <td>18</td>
                                                <td class="amount">₹56,120</td>
                                                <td><span class="rating">★ 4.5</span></td>
                                                <td><button class="action-btn" type="button" data-branch-toast="Opening City Branch">View</button></td>
                                            </tr>
                                            <tr data-branch-row data-status="setup" data-city="noida" data-search="express branch noida setup pooja iyer 14 0 review">
                                                <td><strong>Express Branch</strong><small>HD-BR-004</small></td>
                                                <td>Noida</td>
                                                <td><span class="status-pill status-setup">Setup</span></td>
                                                <td><span class="manager-cell"><span class="branch-avatar">PI</span>Pooja Iyer</span></td>
                                                <td>14</td>
                                                <td class="amount">₹0</td>
                                                <td><span class="muted">—</span></td>
                                                <td><button class="action-btn warning" type="button" data-branch-toast="Reviewing Express Branch">Review</button></td>
                                            </tr>
                                            <tr data-branch-row data-status="active" data-city="delhi-ncr" data-search="mall kiosk delhi ncr active vikram singh 12 24880 4.2">
                                                <td><strong>Mall Kiosk</strong><small>HD-BR-005</small></td>
                                                <td>Delhi NCR</td>
                                                <td><span class="status-pill status-active">Active</span></td>
                                                <td><span class="manager-cell"><span class="branch-avatar">VS</span>Vikram Singh</span></td>
                                                <td>12</td>
                                                <td class="amount">₹24,880</td>
                                                <td><span class="rating">★ 4.2</span></td>
                                                <td><button class="action-btn" type="button" data-branch-toast="Opening Mall Kiosk">View</button></td>
                                            </tr>
                                            <tr data-branch-row data-status="review" data-city="delhi-airport" data-search="airport outlet delhi airport in review kavya nair 24 0 review">
                                                <td><strong>Airport Outlet</strong><small>HD-BR-006</small></td>
                                                <td>Delhi Airport</td>
                                                <td><span class="status-pill status-review">In Review</span></td>
                                                <td><span class="manager-cell"><span class="branch-avatar">KN</span>Kavya Nair</span></td>
                                                <td>24</td>
                                                <td class="amount">₹0</td>
                                                <td><span class="muted">—</span></td>
                                                <td><button class="action-btn warning" type="button" data-branch-toast="Reviewing Airport Outlet">Review</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="branch-card-foot">
                                    <span>Showing 1 to 6 of 6 branches</span>
                                    <div class="branch-pagination">
                                        <button type="button">‹</button>
                                        <button class="active" type="button">1</button>
                                        <button type="button">›</button>
                                    </div>
                                </div>
                            </article>

                            <div class="branch-side-stack">
                                <article class="branch-card branch-performance-card" id="branch-performance-card">
                                    <div class="branch-card-head">
                                        <h3>Branch Performance Snapshot</h3>
                                        <button class="branch-link-btn" type="button" data-branch-toast="Opening full performance">View full performance →</button>
                                    </div>

                                    <div class="branch-performance-list">
                                        <div class="branch-performance-row" style="--w:100%">
                                            <div><strong>Main Branch</strong><small>156 orders today</small></div>
                                            <b>₹1,24,560</b>
                                            <span class="branch-progress"><i></i></span>
                                        </div>
                                        <div class="branch-performance-row" style="--w:63%">
                                            <div><strong>Rooftop Branch</strong><small>98 orders today</small></div>
                                            <b>₹78,340</b>
                                            <span class="branch-progress"><i></i></span>
                                        </div>
                                        <div class="branch-performance-row" style="--w:45%">
                                            <div><strong>City Branch</strong><small>72 orders today</small></div>
                                            <b>₹56,120</b>
                                            <span class="branch-progress"><i></i></span>
                                        </div>
                                        <div class="branch-performance-row" style="--w:20%">
                                            <div><strong>Mall Kiosk</strong><small>38 orders today</small></div>
                                            <b>₹24,880</b>
                                            <span class="branch-progress"><i></i></span>
                                        </div>
                                    </div>
                                </article>

                                <article class="branch-card branch-approval-card">
                                    <div class="branch-card-head">
                                        <h3>Branch Approval / Setup Queue</h3>
                                        <button class="branch-link-btn" type="button" data-branch-toast="Opening setup queue">View all</button>
                                    </div>

                                    <div class="branch-mini-table">
                                        <div class="branch-mini-head"><span>Branch</span><span>Status</span><span>Action</span></div>
                                        <div><strong>Express Branch</strong><span class="status-pill status-setup">Pending Setup</span><button class="action-btn warning" type="button" data-branch-toast="Reviewing Express Branch setup">Review</button></div>
                                        <div><strong>Airport Outlet</strong><span class="status-pill status-info">License Review</span><button class="action-btn warning" type="button" data-branch-toast="Reviewing Airport license">Review</button></div>
                                        <div><strong>South City</strong><span class="status-pill status-setup">Lease Pending</span><button class="action-btn warning" type="button" data-branch-toast="Reviewing South City lease">Review</button></div>
                                    </div>
                                </article>
                            </div>
                        </div>

                        <div class="branch-middle-grid">
                            <article class="branch-card operational-overview-card" id="branch-operations-card">
                                <div class="branch-card-head">
                                    <div>
                                        <h3>Operational Overview</h3>
                                        <p>Live readiness matrix across kitchen, staff, payments, internet, requests, and cleanliness.</p>
                                    </div>
                                </div>

                                <div class="branch-table-wrap">
                                    <table class="branch-table operational-table">
                                        <thead>
                                            <tr>
                                                <th>Branch</th>
                                                <th>Kitchen</th>
                                                <th>Staff</th>
                                                <th>Payments</th>
                                                <th>Internet</th>
                                                <th>Customer Requests</th>
                                                <th>Cleanliness</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Main Branch</td>
                                                <td><span class="status-pill status-active">Online</span></td>
                                                <td><span class="status-pill status-active">Stable</span></td>
                                                <td><span class="status-pill status-active">Online</span></td>
                                                <td><span class="status-pill status-active">Online</span></td>
                                                <td><span class="status-pill status-setup">2 Pending</span></td>
                                                <td><span class="status-pill status-active">Good</span></td>
                                            </tr>
                                            <tr>
                                                <td>Rooftop Branch</td>
                                                <td><span class="status-pill status-active">Online</span></td>
                                                <td><span class="status-pill status-active">Stable</span></td>
                                                <td><span class="status-pill status-active">Online</span></td>
                                                <td><span class="status-pill status-active">Online</span></td>
                                                <td><span class="status-pill status-setup">1 Pending</span></td>
                                                <td><span class="status-pill status-active">Good</span></td>
                                            </tr>
                                            <tr>
                                                <td>City Branch</td>
                                                <td><span class="status-pill status-active">Online</span></td>
                                                <td><span class="status-pill status-active">Stable</span></td>
                                                <td><span class="status-pill status-active">Online</span></td>
                                                <td><span class="status-pill status-active">Online</span></td>
                                                <td><span class="status-pill status-setup">3 Pending</span></td>
                                                <td><span class="status-pill status-active">Good</span></td>
                                            </tr>
                                            <tr>
                                                <td>Express Branch</td>
                                                <td><span class="status-pill status-setup">Checking</span></td>
                                                <td><span class="status-pill status-setup">Pending</span></td>
                                                <td><span class="status-pill status-setup">Pending</span></td>
                                                <td><span class="status-pill status-active">Online</span></td>
                                                <td><span class="status-pill status-setup">5 Pending</span></td>
                                                <td><span class="status-pill status-setup">Needs Attention</span></td>
                                            </tr>
                                            <tr>
                                                <td>Mall Kiosk</td>
                                                <td><span class="status-pill status-active">Online</span></td>
                                                <td><span class="status-pill status-active">Stable</span></td>
                                                <td><span class="status-pill status-active">Online</span></td>
                                                <td><span class="status-pill status-active">Online</span></td>
                                                <td><span class="status-pill status-active">No Pending</span></td>
                                                <td><span class="status-pill status-active">Good</span></td>
                                            </tr>
                                            <tr>
                                                <td>Airport Outlet</td>
                                                <td><span class="status-pill status-setup">Checking</span></td>
                                                <td><span class="status-pill status-setup">Pending</span></td>
                                                <td><span class="status-pill status-setup">Pending</span></td>
                                                <td><span class="status-pill status-active">Online</span></td>
                                                <td><span class="status-pill status-setup">2 Pending</span></td>
                                                <td><span class="status-pill status-setup">Needs Attention</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </article>

                            <div class="branch-side-stack">
                                <article class="branch-card branch-manager-card" id="branch-managers-card">
                                    <div class="branch-card-head">
                                        <h3>Branch Manager Directory</h3>
                                    </div>
                                    <div class="branch-manager-list">
                                        <div><span class="branch-avatar">RM</span><strong>Rahul Mehta</strong><small>Main Branch</small><b>+91 98765 43210</b><span class="status-pill status-active">Active</span></div>
                                        <div><span class="branch-avatar">NS</span><strong>Neha Sharma</strong><small>Rooftop Branch</small><b>+91 98765 43211</b><span class="status-pill status-active">Active</span></div>
                                        <div><span class="branch-avatar">AV</span><strong>Arjun Verma</strong><small>City Branch</small><b>+91 98765 43212</b><span class="status-pill status-active">Active</span></div>
                                        <div><span class="branch-avatar">PI</span><strong>Pooja Iyer</strong><small>Express Branch</small><b>+91 98765 43213</b><span class="status-pill status-setup">Setup</span></div>
                                        <div><span class="branch-avatar">VS</span><strong>Vikram Singh</strong><small>Mall Kiosk</small><b>+91 98765 43214</b><span class="status-pill status-active">Active</span></div>
                                        <div><span class="branch-avatar">KN</span><strong>Kavya Nair</strong><small>Airport Outlet</small><b>+91 98765 43215</b><span class="status-pill status-review">In Review</span></div>
                                    </div>
                                </article>

                                <article class="branch-card branch-alerts-card">
                                    <div class="branch-card-head">
                                        <h3>Pending Alerts</h3><button class="branch-link-btn" type="button" data-branch-toast="Opening branch alerts">View all</button>
                                    </div>
                                    <div class="branch-alert-list">
                                        <div><span><i data-lucide="phone-off"></i></span><strong>Branch phone mismatch</strong><b>2</b></div>
                                        <div><span><i data-lucide="file-warning"></i></span><strong>License renewal due</strong><b>1</b></div>
                                        <div><span><i data-lucide="image-off"></i></span><strong>Setup assets pending</strong><b>3</b></div>
                                        <div><span><i data-lucide="users-round"></i></span><strong>Staff shortage reported</strong><b>2</b></div>
                                        <div><span><i data-lucide="message-circle-warning"></i></span><strong>Customer complaints spike</strong><b>1</b></div>
                                    </div>
                                </article>
                            </div>
                        </div>

                        <div class="branch-bottom-grid">
                            <article class="branch-card expansion-growth-card" id="branch-expansion-card">
                                <div class="branch-card-head">
                                    <h3>Expansion &amp; Growth</h3><button class="branch-link-btn" type="button" data-branch-toast="Opening expansion pipeline">View all</button>
                                </div>
                                <div class="branch-table-wrap">
                                    <table class="branch-table compact">
                                        <thead>
                                            <tr>
                                                <th>Location</th>
                                                <th>Potential Score</th>
                                                <th>Footfall (Est.)</th>
                                                <th>Recommended Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>South Delhi</td>
                                                <td><span class="score-badge green">92</span></td>
                                                <td>12,500/day</td>
                                                <td><button class="action-btn success" type="button" data-branch-toast="South Delhi marked proceed">Proceed</button></td>
                                            </tr>
                                            <tr>
                                                <td>Gurugram East</td>
                                                <td><span class="score-badge blue">87</span></td>
                                                <td>9,800/day</td>
                                                <td><button class="action-btn" type="button" data-branch-toast="Gurugram East shortlisted">Shortlist</button></td>
                                            </tr>
                                            <tr>
                                                <td>Noida Sector 18</td>
                                                <td><span class="score-badge orange">78</span></td>
                                                <td>8,200/day</td>
                                                <td><button class="action-btn warning" type="button" data-branch-toast="Noida Sector 18 moved to evaluation">Evaluate</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </article>

                            <article class="branch-card branch-activity-card">
                                <div class="branch-card-head">
                                    <h3>Recent Branch Activity</h3><button class="branch-link-btn" type="button" data-branch-toast="Opening recent activity">View all</button>
                                </div>
                                <div class="branch-activity-list">
                                    <div><span class="activity-icon blue"><i data-lucide="plus"></i></span><strong>Main Branch created</strong><time>May 18, 2025 10:45 AM</time></div>
                                    <div><span class="activity-icon green"><i data-lucide="user-check"></i></span><strong>Neha Sharma assigned to Rooftop Branch</strong><time>May 18, 2025 09:30 AM</time></div>
                                    <div><span class="activity-icon purple"><i data-lucide="armchair"></i></span><strong>Tables updated at City Branch</strong><time>May 17, 2025 08:15 PM</time></div>
                                    <div><span class="activity-icon orange"><i data-lucide="image"></i></span><strong>Hero assets published for Mall Kiosk</strong><time>May 17, 2025 06:40 PM</time></div>
                                    <div><span class="activity-icon blue"><i data-lucide="shield-alert"></i></span><strong>Airport Outlet status changed to In Review</strong><time>May 17, 2025 05:20 PM</time></div>
                                </div>
                            </article>

                            <article class="branch-card branch-visibility-card">
                                <div class="branch-card-head">
                                    <h3>Branch Asset &amp; Visibility</h3>
                                </div>
                                <div class="visibility-body">
                                    <div class="visibility-ring"><span>88%</span><small>Ready</small></div>
                                    <div class="visibility-checks">
                                        <span><i data-lucide="check-circle"></i>Logo Ready</span>
                                        <span><i data-lucide="check-circle"></i>Contact Info Complete</span>
                                        <span><i data-lucide="check-circle"></i>Customer Page Live</span>
                                        <span><i data-lucide="check-circle"></i>Menu Synced</span>
                                        <span><i data-lucide="check-circle"></i>Compliance Docs Verified</span>
                                    </div>
                                </div>
                                <div class="branch-card-foot end"><button class="branch-link-btn" type="button" data-branch-toast="Opening visibility improvements">Improve visibility →</button></div>
                            </article>

                            <article class="branch-card revenue-split-card">
                                <div class="branch-card-head">
                                    <h3>Revenue Split by Branch</h3>
                                </div>
                                <div class="revenue-split-body">
                                    <div class="revenue-donut"><span>Total</span><strong>₹2.59L</strong></div>
                                    <div class="revenue-legend">
                                        <div><i class="dot main"></i><span>Main Branch</span><b>47%</b><small>₹1,24,560</small></div>
                                        <div><i class="dot roof"></i><span>Rooftop Branch</span><b>30%</b><small>₹78,340</small></div>
                                        <div><i class="dot city"></i><span>City Branch</span><b>21%</b><small>₹56,120</small></div>
                                        <div><i class="dot mall"></i><span>Mall Kiosk</span><b>9%</b><small>₹24,880</small></div>
                                    </div>
                                </div>
                                <div class="branch-card-foot"><span>Total Revenue</span><strong>₹2,59,020</strong></div>
                            </article>
                        </div>
                    </section>

                    <div class="add-branch-modal" id="add-branch-modal" aria-hidden="true">
                        <div class="add-branch-backdrop" data-close-add-branch></div>

                        <div class="add-branch-dialog" role="dialog" aria-modal="true" aria-labelledby="add-branch-title">
                            <div class="add-branch-head">
                                <div>
                                    <h3 id="add-branch-title">Add Branch</h3>
                                    <p>Create a branch profile placeholder. Backend connection can be added later.</p>
                                </div>
                                <button class="branch-icon-btn" type="button" data-close-add-branch aria-label="Close add branch modal">
                                    <i data-lucide="x"></i>
                                </button>
                            </div>

                            <form id="add-branch-form" class="add-branch-form">
                                <label>Branch Name<input type="text" placeholder="Example: South City Branch" required></label>
                                <label>City<input type="text" placeholder="Example: New Delhi" required></label>
                                <label>Branch Code<input type="text" placeholder="HD-BR-007"></label>
                                <label>Manager<select>
                                        <option>Rahul Mehta</option>
                                        <option>Neha Sharma</option>
                                        <option>Arjun Verma</option>
                                        <option>Pooja Iyer</option>
                                    </select></label>
                                <label>Status<select>
                                        <option>Setup</option>
                                        <option>Active</option>
                                        <option>In Review</option>
                                    </select></label>
                                <label>Total Tables<input type="number" placeholder="24"></label>
                                <label class="wide">Address<input type="text" placeholder="Enter branch address"></label>
                                <label>Contact Number<input type="tel" placeholder="+91 98765 43210"></label>

                                <div class="add-branch-actions">
                                    <button class="branch-btn branch-btn-ghost" type="button" data-close-add-branch>Cancel</button>
                                    <button class="branch-btn branch-btn-primary" type="submit">Save Branch</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- ================= BRANCH MANAGEMENT MODULE END ================= -->




                </main>
            </div>
        </div>
    </section>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script src="<?= brand_e(brand_base_url('RestaurantAssets/js/brand.js')) ?>"></script>
</body>

</html>