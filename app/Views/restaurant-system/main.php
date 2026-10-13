<?php
declare(strict_types=1);

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('rs_valid_entity_id')) {
    function rs_valid_entity_id(?string $value): bool
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '') {
            return false;
        }

        return preg_match('/^[a-f0-9]{32}$/i', $value) === 1
            || preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $value) === 1;
    }
}

if (!function_exists('rs_brand_initials')) {
    function rs_brand_initials(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            return 'RS';
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        $initials = '';

        foreach ($parts as $part) {
            if ($part !== '') {
                $initials .= function_exists('mb_substr') ? mb_substr($part, 0, 1) : substr($part, 0, 1);
            }

            if ((function_exists('mb_strlen') ? mb_strlen($initials) : strlen($initials)) >= 2) {
                break;
            }
        }

        $initials = strtoupper($initials);

        return $initials !== '' ? $initials : 'RS';
    }
}

if (!function_exists('rs_base_url')) {
    function rs_base_url(string $path = ''): string
    {
        if (function_exists('base_url')) {
            return rtrim((string) base_url($path), '/');
        }

        $path = ltrim($path, '/');
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $scheme = $https ? 'https' : 'http';
        $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');

        return $scheme . '://' . $host . ($path !== '' ? '/' . $path : '');
    }
}

if (!function_exists('rs_resolve_asset_url')) {
    function rs_resolve_asset_url(?string $path, string $fallback = ''): string
    {
        $path = trim((string) $path);
        $fallback = trim((string) $fallback);

        if ($path === '') {
            $path = $fallback;
        }

        if ($path === '') {
            return '';
        }

        if (
            str_starts_with($path, 'http://') ||
            str_starts_with($path, 'https://') ||
            str_starts_with($path, '//') ||
            str_starts_with($path, 'data:')
        ) {
            return $path;
        }

        $normalized = str_replace('\\', '/', $path);
        $normalized = ltrim($normalized, '/');

        if (stripos($normalized, 'RestaurantAssets/') === 0) {
            return rs_base_url($normalized);
        }

        return rs_base_url('RestaurantAssets/' . $normalized);
    }
}

if (!function_exists('rs_favicon_type')) {
    function rs_favicon_type(string $url): string
    {
        $ext = strtolower((string) pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        switch ($ext) {
            case 'svg':
                return 'image/svg+xml';
            case 'png':
                return 'image/png';
            case 'jpg':
            case 'jpeg':
                return 'image/jpeg';
            case 'webp':
                return 'image/webp';
            case 'ico':
            default:
                return 'image/x-icon';
        }
    }
}

if (!function_exists('rs_pretty_role')) {
    function rs_pretty_role(string $role): string
    {
        $role = strtolower(trim($role));

        switch ($role) {
            case 'brand':
                return 'Brand';
            case 'manager':
                return 'Manager';
            case 'waiter':
                return 'Waiter';
            case 'kitchen':
                return 'Kitchen';
            case 'chef':
                return 'Chef';
            case 'pad':
                return 'Pad';
            case 'customer':
                return 'Customer';
            default:
                return $role !== '' ? ucfirst($role) : 'User';
        }
    }
}

$pageTitle = trim((string) ($pageTitle ?? 'Restaurant Management System'));
$pageError = trim((string) ($pageError ?? ''));

$brandProfile = is_array($brandProfile ?? null) ? $brandProfile : [];
$moduleUrls = is_array($moduleUrls ?? null) ? $moduleUrls : [];
$visibleModules = is_array($visibleModules ?? null) ? $visibleModules : [];

$brandId = rs_valid_entity_id((string) ($brandId ?? '')) ? strtolower(trim((string) $brandId)) : '';
$branchId = rs_valid_entity_id((string) ($branchId ?? '')) ? strtolower(trim((string) $branchId)) : '';

$brandName = trim((string) ($brandProfile['brand_name'] ?? 'Restaurant System'));
$platformName = trim((string) ($brandProfile['platform_name'] ?? 'Restaurant Management System'));
$brandStatus = trim((string) ($brandProfile['status'] ?? 'Active'));
$scope = trim((string) ($brandProfile['scope'] ?? 'Role-based restaurant access'));
$currency = trim((string) ($brandProfile['currency'] ?? 'INR'));
$timezone = trim((string) ($brandProfile['timezone'] ?? 'Asia/Kolkata'));
$subscriptionPlan = trim((string) ($brandProfile['subscription_plan'] ?? 'starter'));
$mailId = trim((string) ($brandProfile['mail_id'] ?? ''));
$mobile = trim((string) ($brandProfile['mobile'] ?? ''));
$setupCompleted = (int) ($brandProfile['is_setup_completed'] ?? 0);

$branchName = trim((string) ($brandProfile['branch_name'] ?? 'Main Branch'));
$branchCode = trim((string) ($brandProfile['branch_code'] ?? ''));
$branchCity = trim((string) ($brandProfile['branch_city'] ?? ''));
$branchAddress = trim((string) ($brandProfile['branch_address'] ?? ''));
$branchPhone = trim((string) ($brandProfile['branch_phone'] ?? ''));
$branchEmail = trim((string) ($brandProfile['branch_email'] ?? ''));
$branchStatus = trim((string) ($brandProfile['branch_status'] ?? 'Active'));

$userId = trim((string) ($brandProfile['user_id'] ?? ''));
$userName = trim((string) ($brandProfile['user_name'] ?? ''));
$userEmail = trim((string) ($brandProfile['user_email'] ?? ''));
$userRole = strtolower(trim((string) ($brandProfile['user_role'] ?? '')));
$employeeCode = trim((string) ($brandProfile['employee_code'] ?? ''));
$profilePic = trim((string) ($brandProfile['profile_pic'] ?? ''));

$branchesCount = (int) ($brandProfile['branches_count'] ?? 0);
$todayOrders = (int) ($brandProfile['today_orders'] ?? 0);
$totalTables = (int) ($brandProfile['total_tables'] ?? 0);
$activeTables = (int) ($brandProfile['active_tables'] ?? 0);
$pendingRequests = (int) ($brandProfile['pending_requests'] ?? 0);
$liveKitchenTickets = (int) ($brandProfile['live_kitchen_tickets'] ?? 0);

$brandInitials = rs_brand_initials($brandName);
$brandLogo = rs_resolve_asset_url($brandProfile['logo'] ?? null, 'default/default_logo.svg');
$brandFavicon = rs_resolve_asset_url($brandProfile['favicon'] ?? null, 'default/default_favicon.svg');
$profilePicUrl = rs_resolve_asset_url($profilePic, '');
$faviconType = rs_favicon_type($brandFavicon);

$allCards = [
    'brand' => [
        'id' => 'brand',
        'number' => '01',
        'title' => 'Brand',
        'subtitle' => 'Central ownership control, brand settings, branch structure, business identity, and platform-level administration.',
        'href' => (string) ($moduleUrls['brand'] ?? '#'),
        'tag' => 'Ownership',
        'metric' => 'Brand & Structure',
        'icon' => '
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M3 21h18"></path>
                <path d="M5 21V7l7-4 7 4v14"></path>
                <path d="M9 10h.01"></path>
                <path d="M15 10h.01"></path>
                <path d="M9 14h.01"></path>
                <path d="M15 14h.01"></path>
            </svg>
        ',
    ],
    'customer' => [
        'id' => 'customer',
        'number' => '02',
        'title' => 'Customer',
        'subtitle' => 'Guest ordering, menu browsing, service requests, live order tracking, and premium dining interaction flow.',
        'href' => (string) ($moduleUrls['customer'] ?? '#'),
        'tag' => 'Guest',
        'metric' => 'Ordering & Requests',
        'icon' => '
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M8 3v8"></path>
                <path d="M12 3v8"></path>
                <path d="M10 11v10"></path>
                <path d="M17 3c1.657 0 3 1.79 3 4s-1.343 4-3 4v10"></path>
                <path d="M4 3v5a4 4 0 0 0 4 4"></path>
            </svg>
        ',
    ],
    'waiter' => [
        'id' => 'waiter',
        'number' => '03',
        'title' => 'Waiter',
        'subtitle' => 'Live table service, floor support, request handling, order assistance, and dine-in coordination.',
        'href' => (string) ($moduleUrls['waiter'] ?? '#'),
        'tag' => 'Service',
        'metric' => 'Floor Operations',
        'icon' => '
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M7 22v-6"></path>
                <path d="M17 22v-6"></path>
                <path d="M5 10h14"></path>
                <path d="M6 10l2-5h8l2 5"></path>
                <path d="M9 14h6"></path>
            </svg>
        ',
    ],
    'kitchen' => [
        'id' => 'kitchen',
        'number' => '04',
        'title' => 'Kitchen',
        'subtitle' => 'KOT execution, preparation queue, cooking progress, readiness control, and production-side workflow.',
        'href' => (string) ($moduleUrls['kitchen'] ?? '#'),
        'tag' => 'Production',
        'metric' => 'Kitchen Queue',
        'icon' => '
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M8 4h8"></path>
                <path d="M7 8h10"></path>
                <path d="M6 12h12"></path>
                <path d="M8 16h8"></path>
                <path d="M10 20h4"></path>
            </svg>
        ',
    ],
    'manager' => [
        'id' => 'manager',
        'number' => '05',
        'title' => 'Manager',
        'subtitle' => 'Billing, branch monitoring, staff visibility, requests, alerts, live operations, and decision control.',
        'href' => (string) ($moduleUrls['manager'] ?? '#'),
        'tag' => 'Control',
        'metric' => 'Branch Analytics',
        'icon' => '
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M4 19V10"></path>
                <path d="M10 19V5"></path>
                <path d="M16 19v-8"></path>
                <path d="M22 19v-4"></path>
                <path d="M3 19h19"></path>
            </svg>
        ',
    ],
];

$navCards = [];
foreach ($visibleModules as $moduleKey) {
    $moduleKey = strtolower(trim((string) $moduleKey));
    if (isset($allCards[$moduleKey])) {
        $navCards[] = $allCards[$moduleKey];
    }
}

$emptyStateTitle = 'No access modules available';
$emptyStateText = 'No module card could be displayed for the current restaurant role. Please verify the user role mapping in restaurant users and user_access.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <?php if ($brandFavicon !== ''): ?>
        <link rel="icon" type="<?= e($faviconType) ?>" href="<?= e($brandFavicon) ?>">
        <link rel="shortcut icon" type="<?= e($faviconType) ?>" href="<?= e($brandFavicon) ?>">
    <?php endif; ?>
    <style>
        :root {
            --app-max-width: 1380px;
            --app-side-padding: 24px;

            --bg: #f4f7fb;
            --bg-soft: #eef4fb;
            --surface: rgba(255,255,255,0.88);
            --surface-strong: rgba(255,255,255,0.96);
            --surface-alt: rgba(248,251,255,0.92);

            --line: rgba(24,55,92,0.09);
            --line-strong: rgba(24,55,92,0.14);

            --text: #10233f;
            --text-soft: #5e7392;
            --text-dim: #7f92ad;

            --primary: #2563eb;
            --primary-2: #4f8cff;
            --accent: #7c3aed;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;

            --shadow-xs: 0 6px 18px rgba(23,43,77,0.04);
            --shadow-sm: 0 12px 30px rgba(23,43,77,0.06);
            --shadow-md: 0 18px 45px rgba(23,43,77,0.08);
            --shadow-lg: 0 28px 70px rgba(23,43,77,0.10);
            --shadow-xl: 0 38px 90px rgba(23,43,77,0.12);

            --radius-2xl: 34px;
            --radius-xl: 28px;
            --radius-lg: 22px;
            --radius-md: 18px;
            --radius-sm: 14px;

            --transition: 220ms ease;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, "Segoe UI", Roboto, Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(79,140,255,0.10), transparent 26%),
                radial-gradient(circle at top right, rgba(124,58,237,0.09), transparent 22%),
                linear-gradient(180deg, #f8fbff 0%, #f2f6fb 40%, #eef3f9 100%);
            overflow-x: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            z-index: 0;
            pointer-events: none;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
        }

        body::before {
            width: 320px;
            height: 320px;
            top: -90px;
            left: -70px;
            background: rgba(79,140,255,0.12);
        }

        body::after {
            width: 340px;
            height: 340px;
            right: -90px;
            top: 80px;
            background: rgba(124,58,237,0.10);
        }

        a { color: inherit; text-decoration: none; }
        button { font: inherit; }

        .app-container {
            width: 100%;
            max-width: var(--app-max-width);
            margin: 0 auto;
            padding-left: var(--app-side-padding);
            padding-right: var(--app-side-padding);
        }

        .landing-page {
            position: relative;
            min-height: 100vh;
            padding: 32px 0 60px;
            overflow: hidden;
            z-index: 1;
        }

        .hero-shell {
            position: relative;
            z-index: 2;
        }

        .reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.55s ease, transform 0.55s ease;
        }

        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 28px;
            padding: 16px 18px;
            border-radius: 24px;
            background: linear-gradient(180deg, rgba(255,255,255,0.90), rgba(248,251,255,0.82));
            border: 1px solid var(--line);
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(16px);
        }

        .brand-chip {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .brand-logo,
        .brand-logo-fallback,
        .profile-avatar {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            overflow: hidden;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            color: #ffffff;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border: 1px solid rgba(255,255,255,0.55);
            box-shadow: 0 14px 28px rgba(79,140,255,0.22);
        }

        .profile-avatar {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            font-size: 14px;
        }

        .brand-logo img,
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .brand-copy h1,
        .user-copy h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.02em;
        }

        .brand-copy p,
        .user-copy p {
            margin: 6px 0 0;
            font-size: 13px;
            color: var(--text-soft);
            line-height: 1.55;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .topbar-status {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            border-radius: 999px;
            white-space: nowrap;
            background: rgba(255,255,255,0.80);
            border: 1px solid var(--line);
            color: var(--text-soft);
            font-size: 13px;
            font-weight: 700;
            box-shadow: var(--shadow-xs);
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 0 6px rgba(34,197,94,0.12);
        }

        .user-pill {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 8px;
            border-radius: 22px;
            background: linear-gradient(180deg, rgba(255,255,255,0.92), rgba(248,251,255,0.86));
            border: 1px solid var(--line);
            box-shadow: var(--shadow-xs);
            min-width: 0;
        }

        .hero-card {
            position: relative;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 26px;
            margin-bottom: 28px;
            padding: 38px;
            border-radius: var(--radius-2xl);
            background:
                linear-gradient(135deg, rgba(255,255,255,0.94), rgba(247,250,255,0.88)),
                linear-gradient(180deg, rgba(255,255,255,0.65), rgba(255,255,255,0.72));
            border: 1px solid rgba(24,55,92,0.08);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
        }

        .hero-card::before {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            right: -80px;
            top: -100px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79,140,255,0.16), transparent 70%);
            pointer-events: none;
        }

        .hero-card::after {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            left: -90px;
            bottom: -120px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(124,58,237,0.12), transparent 70%);
            pointer-events: none;
        }

        .hero-left, .hero-right {
            position: relative;
            z-index: 1;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            margin-bottom: 18px;
            border-radius: 999px;
            background: rgba(255,255,255,0.78);
            border: 1px solid rgba(37,99,235,0.10);
            color: var(--primary);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            box-shadow: var(--shadow-xs);
        }

        .eyebrow::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
        }

        .hero-title {
            margin: 0 0 14px;
            font-size: clamp(34px, 4vw, 56px);
            line-height: 1.02;
            letter-spacing: -0.05em;
            color: var(--text);
            max-width: 780px;
        }

        .hero-title .accent {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero-desc {
            margin: 0;
            max-width: 760px;
            color: var(--text-soft);
            font-size: 15px;
            line-height: 1.88;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 30px;
        }

        .btn {
            appearance: none;
            border: 0;
            outline: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 54px;
            padding: 0 22px;
            border-radius: 18px;
            cursor: pointer;
            transition: transform var(--transition), box-shadow var(--transition), background var(--transition), border-color var(--transition);
            font-weight: 800;
        }

        .btn:hover { transform: translateY(-2px); }

        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--primary), var(--primary-2), var(--accent));
            box-shadow: 0 18px 36px rgba(79,140,255,0.22);
        }

        .btn-secondary {
            color: var(--text);
            background: rgba(255,255,255,0.82);
            border: 1px solid var(--line);
            box-shadow: var(--shadow-xs);
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .stat-card,
        .quick-card,
        .error-card,
        .empty-card,
        .footer-note {
            border: 1px solid var(--line);
            box-shadow: var(--shadow-sm);
            background: linear-gradient(180deg, rgba(255,255,255,0.90), rgba(248,251,255,0.84));
        }

        .stat-card {
            padding: 22px;
            border-radius: 24px;
            transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: rgba(79,140,255,0.16);
        }

        .stat-card strong {
            display: block;
            margin-bottom: 10px;
            font-size: 29px;
            line-height: 1;
            color: var(--text);
            font-weight: 900;
            letter-spacing: -0.03em;
        }

        .stat-card span {
            display: block;
            color: var(--text-soft);
            font-size: 13px;
            line-height: 1.7;
        }

        .stat-card small {
            display: inline-flex;
            margin-top: 14px;
            padding: 7px 11px;
            border-radius: 999px;
            background: #eef4ff;
            color: var(--primary);
            font-size: 11px;
            font-weight: 800;
            border: 1px solid rgba(37,99,235,0.10);
        }

        .quick-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .quick-card {
            position: relative;
            padding: 22px 22px 20px;
            border-radius: 24px;
            overflow: hidden;
            transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition);
        }

        .quick-card::before {
            content: "";
            position: absolute;
            inset: 0 auto auto 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            opacity: 0.9;
        }

        .quick-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: rgba(79,140,255,0.16);
        }

        .quick-label {
            margin: 0 0 10px;
            color: var(--text-dim);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .quick-value {
            margin: 0;
            color: var(--text);
            font-size: 26px;
            line-height: 1.15;
            font-weight: 900;
            letter-spacing: -0.03em;
        }

        .quick-note {
            margin: 8px 0 0;
            color: var(--text-soft);
            font-size: 13px;
            line-height: 1.7;
        }

        .section-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 16px;
            margin: 28px 0 18px;
        }

        .section-head h2 {
            margin: 0;
            color: var(--text);
            font-size: 29px;
            font-weight: 900;
            letter-spacing: -0.03em;
        }

        .section-head p {
            margin: 7px 0 0;
            color: var(--text-soft);
            font-size: 14px;
            line-height: 1.75;
        }

        .section-meta {
            color: var(--primary);
            font-size: 13px;
            font-weight: 800;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.75);
            border: 1px solid rgba(37,99,235,0.10);
            box-shadow: var(--shadow-xs);
        }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .nav-card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 290px;
            padding: 24px;
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(255,255,255,0.94), rgba(248,251,255,0.88));
            border: 1px solid var(--line);
            box-shadow: var(--shadow-md);
            transition: transform var(--transition), border-color var(--transition), box-shadow var(--transition), background var(--transition);
            overflow: hidden;
        }

        .nav-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(79,140,255,0.10), transparent 28%),
                radial-gradient(circle at bottom left, rgba(124,58,237,0.08), transparent 26%);
            pointer-events: none;
        }

        .nav-card > * {
            position: relative;
            z-index: 1;
        }

        .nav-card:hover {
            transform: translateY(-7px);
            border-color: rgba(79,140,255,0.18);
            box-shadow: var(--shadow-lg);
        }

        .nav-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 18px;
        }

        .nav-icon {
            width: 66px;
            height: 66px;
            border-radius: 20px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, rgba(37,99,235,0.12), rgba(124,58,237,0.10));
            border: 1px solid rgba(37,99,235,0.10);
            color: var(--primary);
            flex-shrink: 0;
            box-shadow: var(--shadow-xs);
        }

        .nav-icon svg {
            width: 30px;
            height: 30px;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .nav-tag {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 76px;
            min-height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            color: var(--accent);
            background: rgba(255,255,255,0.76);
            border: 1px solid rgba(124,58,237,0.10);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            box-shadow: var(--shadow-xs);
        }

        .nav-number {
            color: var(--text-dim);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .nav-title {
            margin: 8px 0 10px;
            color: var(--text);
            font-size: 26px;
            font-weight: 900;
            letter-spacing: -0.03em;
        }

        .nav-subtitle {
            margin: 0;
            color: var(--text-soft);
            font-size: 14px;
            line-height: 1.78;
        }

        .nav-metric {
            display: inline-flex;
            width: fit-content;
            margin-top: 15px;
            padding: 9px 13px;
            border-radius: 999px;
            background: #f3f7ff;
            border: 1px solid rgba(37,99,235,0.10);
            color: var(--primary);
            font-size: 12px;
            font-weight: 800;
        }

        .nav-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-top: auto;
            padding-top: 24px;
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-height: 48px;
            padding: 0 18px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            border: 1px solid rgba(37,99,235,0.14);
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 14px 30px rgba(79,140,255,0.18);
            transition: transform var(--transition), box-shadow var(--transition);
        }

        .nav-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 34px rgba(79,140,255,0.24);
        }

        .nav-pulse {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-dim);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .nav-pulse::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 0 6px rgba(34,197,94,0.10);
        }

        .error-card,
        .empty-card,
        .footer-note {
            margin-bottom: 24px;
            padding: 22px 24px;
            border-radius: 24px;
        }

        .error-card {
            border-color: rgba(239,68,68,0.16);
            background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(255,245,246,0.92));
            box-shadow: 0 20px 55px rgba(239,68,68,0.08);
        }

        .error-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.14);
            color: var(--danger);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .error-title,
        .empty-title {
            margin: 14px 0 8px;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -0.03em;
            color: var(--text);
        }

        .error-text,
        .empty-text,
        .footer-note {
            color: var(--text-soft);
            font-size: 14px;
            line-height: 1.85;
        }

        .empty-card {
            text-align: center;
            padding: 34px 24px;
        }

        .footer-note {
            margin-top: 28px;
        }

        @media (max-width: 1180px) {
            .hero-card,
            .nav-grid {
                grid-template-columns: 1fr;
            }

            .quick-strip {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 860px) {
            :root {
                --app-side-padding: 16px;
            }

            .topbar {
                flex-direction: column;
                align-items: stretch;
            }

            .topbar-right {
                justify-content: space-between;
            }

            .hero-card {
                padding: 24px;
            }

            .hero-stats {
                grid-template-columns: 1fr 1fr;
            }

            .section-head {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 580px) {
            .hero-title {
                font-size: 36px;
            }

            .hero-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .hero-stats {
                grid-template-columns: 1fr;
            }

            .nav-card {
                min-height: 240px;
            }

            .nav-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .nav-link {
                justify-content: center;
            }

            .brand-copy p,
            .user-copy p {
                word-break: break-word;
            }

            .user-pill {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <main class="landing-page">
        <div class="app-container hero-shell">
            <?php if ($pageError !== ''): ?>
                <section class="error-card reveal">
                    <span class="error-badge">Access Error</span>
                    <h2 class="error-title">Unable to load restaurant main access</h2>
                    <p class="error-text"><?= e($pageError) ?></p>
                    <p class="error-text">
                        Please verify the authenticated restaurant user, active branch access, and role mapping in the restaurant database.
                    </p>
                </section>
            <?php endif; ?>

            <header class="topbar reveal">
                <div class="brand-chip">
                    <?php if ($brandLogo !== ''): ?>
                        <div class="brand-logo">
                            <img src="<?= e($brandLogo) ?>" alt="<?= e($brandName) ?>" loading="eager" decoding="async">
                        </div>
                    <?php else: ?>
                        <div class="brand-logo-fallback"><?= e($brandInitials) ?></div>
                    <?php endif; ?>

                    <div class="brand-copy">
                        <h1><?= e($platformName) ?></h1>
                        <p>
                            <?= e($brandName) ?>
                            <?php if ($branchName !== ''): ?> · <?= e($branchName) ?><?php endif; ?>
                            <?php if ($brandId !== ''): ?> · Brand ID: <?= e($brandId) ?><?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="topbar-status">
                        <span class="status-dot"></span>
                        <span><?= e($brandStatus) ?> · <?= e($scope) ?></span>
                    </div>

                    <div class="user-pill">
                        <div class="profile-avatar">
                            <?php if ($profilePicUrl !== ''): ?>
                                <img src="<?= e($profilePicUrl) ?>" alt="<?= e($userName !== '' ? $userName : 'User') ?>">
                            <?php else: ?>
                                <?= e(rs_brand_initials($userName !== '' ? $userName : ($userEmail !== '' ? $userEmail : 'U'))) ?>
                            <?php endif; ?>
                        </div>
                        <div class="user-copy">
                            <h3><?= e($userName !== '' ? $userName : 'Restaurant User') ?></h3>
                            <p>
                                <?= e($userEmail !== '' ? $userEmail : 'Email unavailable') ?>
                                · <?= e(rs_pretty_role($userRole)) ?>
                                <?php if ($employeeCode !== ''): ?> · <?= e($employeeCode) ?><?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </header>

            <section class="hero-card reveal">
                <div class="hero-left">
                    <div class="eyebrow">Premium Role-Based Access</div>

                    <h2 class="hero-title">
                        Welcome to your
                        <span class="accent">restaurant command hub</span>
                    </h2>

                    <p class="hero-desc">
                        This main page now reads the authenticated restaurant context directly from the base controller,
                        removes all hardcoded IDs, loads brand, branch, user and operational summary dynamically,
                        and shows only the modules allowed for the current role.
                    </p>

                    <div class="hero-actions">
                        <a class="btn btn-primary" href="#modules">Open Your Modules</a>
                        <button class="btn btn-secondary" type="button" id="scrollTopBtn">Back to Top</button>
                    </div>
                </div>

                <div class="hero-right">
                    <div class="hero-stats">
                        <div class="stat-card">
                            <strong><?= e((string) $branchesCount) ?></strong>
                            <span>Total branches available under the current restaurant brand.</span>
                            <small>Branches</small>
                        </div>

                        <div class="stat-card">
                            <strong><?= e((string) $todayOrders) ?></strong>
                            <span>Orders created today in the current branch context.</span>
                            <small>Today Orders</small>
                        </div>

                        <div class="stat-card">
                            <strong><?= e((string) $pendingRequests) ?></strong>
                            <span>Live service requests waiting for operational attention.</span>
                            <small>Requests</small>
                        </div>

                        <div class="stat-card">
                            <strong><?= e((string) $liveKitchenTickets) ?></strong>
                            <span>Kitchen workload currently active for this branch.</span>
                            <small>Kitchen Queue</small>
                        </div>
                    </div>
                </div>
            </section>

            <section class="quick-strip">
                <article class="quick-card reveal">
                    <p class="quick-label">Authenticated Role</p>
                    <h3 class="quick-value"><?= e(rs_pretty_role($userRole)) ?></h3>
                    <p class="quick-note">
                        User ID: <?= e($userId !== '' ? $userId : 'Not available') ?>
                    </p>
                </article>

                <article class="quick-card reveal">
                    <p class="quick-label">Brand Identity</p>
                    <h3 class="quick-value"><?= e($brandName) ?></h3>
                    <p class="quick-note">
                        <?= e($mailId !== '' ? $mailId : 'Brand email not available') ?>
                        <?= $mobile !== '' ? ' · ' . e($mobile) : '' ?>
                    </p>
                </article>

                <article class="quick-card reveal">
                    <p class="quick-label">Branch Context</p>
                    <h3 class="quick-value"><?= e($branchName) ?></h3>
                    <p class="quick-note">
                        <?= e($branchCode !== '' ? $branchCode : 'Code unavailable') ?>
                        <?= $branchCity !== '' ? ' · ' . e($branchCity) : '' ?>
                        <?= $branchStatus !== '' ? ' · ' . e($branchStatus) : '' ?>
                    </p>
                </article>
            </section>

            <section class="quick-strip">
                <article class="quick-card reveal">
                    <p class="quick-label">Tables</p>
                    <h3 class="quick-value"><?= e((string) $totalTables) ?></h3>
                    <p class="quick-note">Total tables in this branch. Active tables: <?= e((string) $activeTables) ?></p>
                </article>

                <article class="quick-card reveal">
                    <p class="quick-label">Setup & Region</p>
                    <h3 class="quick-value"><?= $setupCompleted === 1 ? 'Completed' : 'Pending' ?></h3>
                    <p class="quick-note"><?= e(ucfirst($subscriptionPlan)) ?> · <?= e(strtoupper($currency)) ?> · <?= e($timezone) ?></p>
                </article>

                <article class="quick-card reveal">
                    <p class="quick-label">Branch Contact</p>
                    <h3 class="quick-value"><?= e($branchPhone !== '' ? $branchPhone : 'Not Set') ?></h3>
                    <p class="quick-note"><?= e($branchEmail !== '' ? $branchEmail : 'Branch email not available') ?></p>
                </article>
            </section>

            <section id="modules">
                <div class="section-head reveal">
                    <div>
                        <h2>Available Modules</h2>
                        <p>Only the cards allowed for the authenticated restaurant role are shown below.</p>
                    </div>
                    <div class="section-meta"><?= e(rs_pretty_role($userRole)) ?> Access</div>
                </div>

                <?php if ($navCards === []): ?>
                    <div class="empty-card reveal">
                        <h3 class="empty-title"><?= e($emptyStateTitle) ?></h3>
                        <p class="empty-text"><?= e($emptyStateText) ?></p>
                    </div>
                <?php else: ?>
                    <div class="nav-grid">
                        <?php foreach ($navCards as $card): ?>
                            <article class="nav-card reveal" id="<?= e((string) $card['id']) ?>">
                                <div class="nav-top">
                                    <div class="nav-icon" aria-hidden="true"><?= $card['icon'] ?></div>
                                    <div class="nav-tag"><?= e((string) $card['tag']) ?></div>
                                </div>

                                <div class="nav-number">Module <?= e((string) $card['number']) ?></div>
                                <h3 class="nav-title"><?= e((string) $card['title']) ?></h3>
                                <p class="nav-subtitle"><?= e((string) $card['subtitle']) ?></p>
                                <div class="nav-metric"><?= e((string) $card['metric']) ?></div>

                                <div class="nav-footer">
                                    <a class="nav-link" href="<?= e((string) $card['href']) ?>">
                                        <span>Open <?= e((string) $card['title']) ?></span>
                                        <span>→</span>
                                    </a>
                                    <span class="nav-pulse">Premium Access</span>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <div class="footer-note reveal">
                This premium main page is now a clean role-based navigation layer only. It reads the active user, brand, branch,
                and role from the base controller context, then safely shows only the allowed module cards with graceful fallbacks.
            </div>
        </div>
    </main>

    <script>
        (function () {
            const revealItems = Array.from(document.querySelectorAll('.reveal'));
            const scrollTopBtn = document.getElementById('scrollTopBtn');

            function revealOnView() {
                const trigger = window.innerHeight * 0.9;

                revealItems.forEach((item, index) => {
                    const rect = item.getBoundingClientRect();
                    if (rect.top < trigger) {
                        setTimeout(() => {
                            item.classList.add('is-visible');
                        }, Math.min(index * 55, 320));
                    }
                });
            }

            revealOnView();
            window.addEventListener('scroll', revealOnView, { passive: true });
            window.addEventListener('resize', revealOnView);

            if (scrollTopBtn) {
                scrollTopBtn.addEventListener('click', function () {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }

            const navCards = Array.from(document.querySelectorAll('.nav-card'));

            navCards.forEach((card) => {
                card.addEventListener('mousemove', function (event) {
                    const rect = card.getBoundingClientRect();
                    const x = event.clientX - rect.left;
                    const y = event.clientY - rect.top;

                    card.style.background =
                        'radial-gradient(circle at ' + x + 'px ' + y + 'px, rgba(79,140,255,0.12), transparent 28%), linear-gradient(180deg, rgba(255,255,255,0.94), rgba(248,251,255,0.88))';
                });

                card.addEventListener('mouseleave', function () {
                    card.style.background = 'linear-gradient(180deg, rgba(255,255,255,0.94), rgba(248,251,255,0.88))';
                });
            });
        })();
    </script>
</body>
</html>














