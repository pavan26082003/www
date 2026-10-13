<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Customer page bootstrap
|--------------------------------------------------------------------------
| Brand ID and Branch ID must come only from controller/base controller.
| Table code can stay hardcoded for now as fallback, until later dynamic flow.
|--------------------------------------------------------------------------
*/

$brandId = trim((string)($brandId ?? ''));
$branchId = trim((string)($branchId ?? ''));
$tableCode = strtoupper(trim((string)($tableCode ?? 'T01')));

if ($brandId === '' || $branchId === '') {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Customer page failed to load. Brand or branch context is missing.';
    exit;
}

$brand = is_array($brand ?? null) ? $brand : [];
$branch = is_array($branch ?? null) ? $branch : [];
$table = is_array($table ?? null) ? $table : [];
$menuCategories = is_array($menuCategories ?? null) ? $menuCategories : [];
$menuItems = is_array($menuItems ?? null) ? $menuItems : [];
$availableTables = is_array($availableTables ?? null) ? $availableTables : [];
$activeOrder = is_array($activeOrder ?? null) ? $activeOrder : [];
$activeOrderItems = is_array($activeOrderItems ?? null) ? $activeOrderItems : [];
$activeBill = is_array($activeBill ?? null) ? $activeBill : [];
$recentServiceRequest = is_array($recentServiceRequest ?? null) ? $recentServiceRequest : [];
$customerContent = is_array($customerContent ?? null) ? $customerContent : [];
$offerBanners = is_array($offerBanners ?? null) ? $offerBanners : [];

/*
|--------------------------------------------------------------------------
| Default content merge
|--------------------------------------------------------------------------
*/
$customerContent = array_replace_recursive([
    'brand_block' => [
        'kicker' => 'Fine Dining Restaurant',
        'subtitle' => 'Fresh food, fast service, great dining',
    ],
    'hero_section' => [
        'badge' => 'Premium Dining Experience',
        'title_line_1' => 'Discover Exceptional',
        'title_highlight' => 'Flavours',
        'description' => 'Explore our chef-crafted menu filled with fresh ingredients, signature recipes, and mouth-watering flavors prepared specially for your table with care, taste, and elegance.',
        'cta_primary' => 'Explore Menu',
        'cta_secondary' => 'About Us',
    ],
    'service_panel' => [
        'badge' => 'Table Service',
        'title' => 'Order your favorites with one tap',
        'description' => 'Browse the menu, customize dishes, track your live order, request service, and pay your bill directly from your table pad with a seamless dining experience.',
    ],
    'menu_discovery' => [
        'kicker' => 'Curated Menu',
        'title' => 'Find your perfect dish',
        'description' => 'Search, explore categories, and discover chef specials crafted to match every craving at your table.',
        'badge' => 'Trending Today',
    ],
    'footer' => [
        'description' => 'Fresh food, premium dining experience, and seamless table ordering.',
    ],
], $customerContent);

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
        if (function_exists('base_url')) {
            return base_url(ltrim($path, '/'));
        }

        $path = ltrim($path, '/');
        $scriptName = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
        $baseDir = rtrim(str_replace('/index.php', '', $scriptName), '/');

        return $baseDir . ($path !== '' ? '/' . $path : '');
    }
}

if (!function_exists('customer_slugify_local')) {
    function customer_slugify_local(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        return trim($text, '-');
    }
}

if (!function_exists('customer_food_badge_label')) {
    function customer_food_badge_label(array $item): string
    {
        $category = strtolower((string)($item['category_name'] ?? ''));
        $itemType = strtolower((string)($item['item_type'] ?? ''));
        $isVeg = isset($item['is_veg']) ? (int)$item['is_veg'] : 0;

        if (strpos($category, 'drink') !== false || $itemType === 'drink') {
            return 'Drink';
        }

        if (strpos($category, 'water') !== false) {
            return 'Water';
        }

        if (strpos($category, 'dessert') !== false) {
            return 'Dessert';
        }

        return $isVeg === 1 ? 'Veg' : 'Non-Veg';
    }
}

if (!function_exists('customer_food_badge_class')) {
    function customer_food_badge_class(array $item): string
    {
        $label = strtolower(customer_food_badge_label($item));

        switch ($label) {
            case 'drink':
                return 'badge-drink';
            case 'water':
                return 'badge-water';
            case 'dessert':
                return 'badge-dessert';
            case 'veg':
                return 'badge-veg';
            default:
                return 'badge-nonveg';
        }
    }
}

if (!function_exists('customer_string_starts_with')) {
    function customer_string_starts_with(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return true;
        }

        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}
if (!function_exists('customer_resolve_asset_url')) {
    function customer_resolve_asset_url(?string $path, string $fallback = ''): string
    {
        $path = trim((string)$path);
        $fallback = trim((string)$fallback);

        if ($path === '') {
            return $fallback !== ''
                ? rs_html_escape(rs_base_url('RestaurantAssets/' . ltrim($fallback, '/')))
                : '';
        }

        if (
            customer_string_starts_with($path, 'http://') ||
            customer_string_starts_with($path, 'https://') ||
            customer_string_starts_with($path, '//') ||
            customer_string_starts_with($path, 'data:')
        ) {
            return rs_html_escape($path);
        }

        return rs_html_escape(rs_base_url('RestaurantAssets/' . ltrim($path, '/')));
    }
}

if (!function_exists('customer_format_time_label')) {
    function customer_format_time_label(?string $timeValue, string $fallback = 'N/A'): string
    {
        $timeValue = trim((string)$timeValue);
        if ($timeValue === '') {
            return $fallback;
        }

        $timestamp = strtotime($timeValue);
        if ($timestamp === false) {
            return $fallback;
        }

        return date('g:i A', $timestamp);
    }
}

if (!function_exists('customer_money')) {
    function customer_money($amount): string
    {
        return '₹' . number_format((float)$amount, 0);
    }
}

/*
|--------------------------------------------------------------------------
| Safe view values
|--------------------------------------------------------------------------
*/
$brandName = trim((string)($brand['name'] ?? ''));
if ($brandName === '') {
    $brandName = 'Restaurant';
}

$brandLogo = customer_resolve_asset_url($brand['logo'] ?? null, 'default/default_logo.svg');
$brandFavicon = customer_resolve_asset_url($brand['favicon'] ?? null, 'default/default_favicon.svg');

$branchName = trim((string)($branch['name'] ?? ''));
if ($branchName === '') {
    $branchName = 'Main Branch';
}

$tableDisplay = trim((string)($table['table_code'] ?? $table['table_number'] ?? $tableCode));
if ($tableDisplay === '') {
    $tableDisplay = $tableCode !== '' ? $tableCode : 'T01';
}

$tableStatus = strtolower(trim((string)($table['status'] ?? 'available')));
$orderStatus = strtolower(trim((string)($activeOrder['status'] ?? '')));

$displayStatusText = 'Ready to Order';
$displayStatusClass = 'status-waiting';



$branchHours = 'Open daily';
$openAt = customer_format_time_label($branch['opens_at'] ?? null, '');
$closeAt = customer_format_time_label($branch['closes_at'] ?? null, '');
if ($openAt !== '' && $closeAt !== '') {
    $branchHours = 'Open daily: ' . $openAt . ' - ' . $closeAt;
}

/*
|--------------------------------------------------------------------------
| Recommended items
|--------------------------------------------------------------------------
*/
$recommendedItems = [];

foreach ($menuItems as $item) {
    if (count($recommendedItems) >= 3) {
        break;
    }

    $price = $item['selling_price'] ?? $item['base_price'] ?? 0;
    $typeLabel = strtolower(customer_food_badge_label($item));

    $recommendedItems[] = [
        'id' => (string)($item['id'] ?? ''),
        'name' => (string)($item['name'] ?? 'Special Dish'),
        'description' => (string)($item['description'] ?? 'Freshly prepared premium signature dish.'),
        'image_url' => customer_resolve_asset_url($item['image_url'] ?? null, 'default/default_food.jpg'),
        'price' => (float)$price,
        'rating' => '4.8',
        'time' => !empty($item['preparation_time_mins']) ? ((int)$item['preparation_time_mins'] . ' mins') : 'Fresh',
        'badge' => 'Chef Pick',
        'type' => $typeLabel === 'non-veg' ? 'nonveg' : $typeLabel,
    ];
}

$kitchenVideos = array_values(array_filter($menuItems, static function ($item) {
    return !empty($item['video_url']);
}));

/*
|--------------------------------------------------------------------------
| Cart and bill data
|--------------------------------------------------------------------------
*/
$cartItems = [];

foreach ($activeOrderItems as $row) {
    $qty = max(1, (int)($row['quantity'] ?? 1));
    $unitPrice = (float)($row['price_snapshot'] ?? 0);
    $lineTotal = (float)($row['line_total'] ?? ($qty * $unitPrice));

    $cartItems[] = [
        'id' => (string)($row['id'] ?? ''),
        'menu_item_id' => (string)($row['menu_item_id'] ?? ''),
        'name' => (string)($row['item_name_snapshot'] ?? 'Menu Item'),
        'description' => (string)($row['special_instructions'] ?? 'Freshly prepared item'),
        'unit_price' => $unitPrice,
        'quantity' => $qty,
        'line_total' => $lineTotal,
    ];
}

$cartSubtotal = 0.0;
$cartItemCount = 0;

foreach ($cartItems as $cartItem) {
    $cartSubtotal += (float)$cartItem['line_total'];
    $cartItemCount += (int)$cartItem['quantity'];
}

$cartTaxAmount = (float)($activeOrder['tax_amount'] ?? $activeBill['tax_amount'] ?? 0);
$cartServiceCharge = (float)($activeOrder['service_charge'] ?? $activeBill['service_charge'] ?? 0);
$cartDiscountAmount = (float)($activeOrder['discount_amount'] ?? $activeBill['discount_amount'] ?? 0);

$cartGrandTotal = $cartSubtotal + $cartTaxAmount + $cartServiceCharge - $cartDiscountAmount;

if ($cartGrandTotal <= 0 && !empty($activeOrder['grand_total'])) {
    $cartGrandTotal = (float)$activeOrder['grand_total'];
}

if ($cartGrandTotal <= 0 && !empty($activeBill['total_amount'])) {
    $cartGrandTotal = (float)$activeBill['total_amount'];
}

$pageTitle = 'Customer Pad - ' . $brandName;
$customerApiBase = rs_base_url('restaurant-system/customer/api');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= rs_html_escape($pageTitle) ?></title>

    <link rel="icon" type="image/png" href="<?= $brandFavicon ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="<?= rs_html_escape(rs_base_url('RestaurantAssets/css/global.css')) ?>">
    <link rel="stylesheet" href="<?= rs_html_escape(rs_base_url('RestaurantAssets/css/customer.css')) ?>">
</head>

<body
    class="theme-customer"
    data-brand-id="<?= rs_html_escape($brandId) ?>"
    data-branch-id="<?= rs_html_escape($branchId) ?>"
    data-table-id="<?= rs_html_escape((string)($table['id'] ?? '')) ?>"
    data-table-code="<?= rs_html_escape($tableDisplay) ?>"
    data-order-id="<?= rs_html_escape((string)($activeOrder['id'] ?? '')) ?>">

    <header class="customer-topbar">
        <div class="topbar-strip">
            <div class="app-container">
                <div class="customer-topbar-inner">

                    <div class="brand-block">
                        <div class="brand-badge">
                            <img src="<?= $brandLogo ?>" alt="Restaurant Logo" class="brand-logo">
                        </div>

                        <div class="brand-text">
                            <span class="brand-kicker"><?= rs_html_escape((string)($customerContent['brand_block']['kicker'] ?? '')) ?></span>
                            <h1 class="brand-title"><?= rs_html_escape($brandName) ?></h1>
                            <p class="brand-subtitle"><?= rs_html_escape((string)($customerContent['brand_block']['subtitle'] ?? '')) ?></p>
                        </div>
                    </div>

                    <div class="table-meta-wrap">
                        <div class="table-meta-card">
                            <span class="meta-label">Branch</span>
                            <strong class="meta-value"><?= rs_html_escape($branchName) ?></strong>
                        </div>

                        <div class="table-meta-card">
                            <span class="meta-label">Table</span>
                            <strong class="meta-value"><?= rs_html_escape($tableDisplay) ?></strong>
                        </div>


                        <div class="topbar-actions">
                            <button class="topbar-settings-btn" id="topbarSettingsBtn" type="button" aria-label="Settings">
                                <span class="settings-icon">
                                    <i class="fa-solid fa-gear"></i>
                                </span>
                            </button>

                            <div class="restaurant-settings-popover" id="restaurantSettingsPopover" aria-hidden="true">
                                <div class="restaurant-settings-card">

                                    <div class="restaurant-settings-head">
                                        <div>
                                            <span class="settings-kicker">Restaurant Settings</span>
                                            <h3 class="settings-title">Quick Controls</h3>
                                        </div>

                                        <button class="settings-close-btn" id="settingsCloseBtn" type="button" aria-label="Close">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>

                                    <div class="restaurant-settings-body">
                                        <div class="settings-block">
                                            <div class="settings-block-top">
                                                <div>
                                                    <span class="settings-label">Current Table</span>
                                                    <div class="settings-main-value" id="currentTableDisplay"><?= rs_html_escape($tableDisplay) ?></div>
                                                </div>

                                                <button class="settings-chip-btn" id="changeTableBtn" type="button">
                                                    Change
                                                </button>
                                            </div>

                                            <div class="settings-sub-value">
                                                Branch: <strong id="currentBranchDisplay"><?= rs_html_escape($branchName) ?></strong>
                                            </div>

                                            <div class="table-edit-panel" id="tableEditPanel">
                                                <div class="table-edit-inner">
                                                    <label class="field-label" for="tableSelect">Select Table</label>
                                                    <div class="select-wrap">
                                                        <select id="tableSelect" class="premium-select">
                                                            <option value="">Choose table</option>
                                                            <?php foreach ($availableTables as $branchTable): ?>
                                                                <?php
                                                                $optionValue = (string)($branchTable['table_code'] ?? $branchTable['table_number'] ?? '');
                                                                if ($optionValue === '') continue;
                                                                $selected = $optionValue === $tableDisplay ? 'selected' : '';
                                                                ?>
                                                                <option value="<?= rs_html_escape($optionValue) ?>" <?= $selected ?>>
                                                                    <?= rs_html_escape($optionValue) ?>
                                                                </option>
                                                            <?php endforeach; ?>

                                                            <?php if (empty($availableTables)): ?>
                                                                <option value="<?= rs_html_escape($tableDisplay) ?>" selected><?= rs_html_escape($tableDisplay) ?></option>
                                                            <?php endif; ?>
                                                        </select>
                                                        <span class="select-icon">
                                                            <i class="fa-solid fa-chevron-down"></i>
                                                        </span>
                                                    </div>

                                                    <button class="settings-save-btn" id="saveTableBtn" type="button">
                                                        Save Table
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- <div class="settings-block">
                                            <span class="settings-label">Service</span>
                                            <button class="service-call-btn js-service-btn" id="callWaiterBtn" data-service-type="waiter" type="button">
                                                <span class="service-call-icon">
                                                    <i class="fa-solid fa-bell-concierge"></i>
                                                </span>
                                                <span>Call Waiter</span>
                                            </button>
                                        </div> -->

                                        <div class="settings-block">
                                            <span class="settings-label">Language</span>
                                            <div class="select-wrap">
                                                <select id="languageSelect" class="premium-select">
                                                    <option value="en">English</option>
                                                    <option value="te">Telugu</option>
                                                    <option value="hi">Hindi</option>
                                                </select>
                                                <span class="select-icon">
                                                    <i class="fa-solid fa-chevron-down"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="settings-block">
                                            <span class="settings-label">Theme</span>
                                            <div class="theme-toggle-row">
                                                <button class="theme-pill is-active" type="button" data-theme="light">Light</button>
                                                <button class="theme-pill" type="button" data-theme="dark">Dark</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <?php
        $heroImage = customer_resolve_asset_url(
            $brand['hero_bg_image'] ?? null,
            'default/default_bg.jpeg'
        );
        ?>
        <div class="header-hero" style="background-image: url('<?= $heroImage ?>');">
            <div class="header-overlay"></div>

            <div class="app-container">
                <div class="header-hero-content">

                    <span class="hero-badge">
                        <?= rs_html_escape((string)($customerContent['hero_section']['badge'] ?? '')) ?>
                    </span>

                    <h2 class="hero-main-title">
                        <?= rs_html_escape((string)($customerContent['hero_section']['title_line_1'] ?? '')) ?>
                        <span class="hero-highlight"><?= rs_html_escape((string)($customerContent['hero_section']['title_highlight'] ?? '')) ?></span>
                    </h2>

                    <p class="hero-description">
                        <?= rs_html_escape((string)($customerContent['hero_section']['description'] ?? '')) ?>
                    </p>

                    <div class="hero-cta-row">
                        <a href="#menuSection" class="btn-app btn-primary-app">
                            <?= rs_html_escape((string)($customerContent['hero_section']['cta_primary'] ?? 'Explore Menu')) ?>
                        </a>
                        <button class="btn-app btn-outline-app" type="button">
                            <?= rs_html_escape((string)($customerContent['hero_section']['cta_secondary'] ?? 'About Us')) ?>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </header>

    <section class="service-panel-wrap">
        <div class="app-container">
            <div class="service-panel">

                <div class="service-panel-copy">
                    <span class="service-panel-badge"><?= rs_html_escape((string)($customerContent['service_panel']['badge'] ?? '')) ?></span>
                    <h2 class="service-panel-title"><?= rs_html_escape((string)($customerContent['service_panel']['title'] ?? '')) ?></h2>
                    <p class="service-panel-text"><?= rs_html_escape((string)($customerContent['service_panel']['description'] ?? '')) ?></p>
                </div>

                <div class="service-panel-actions">
                    <button class="quick-action-btn quick-action-water js-service-btn" data-service-type="water" type="button">
                        <span class="quick-action-icon"><i class="fa-solid fa-glass-water"></i></span>
                        <span class="quick-action-content">
                            <strong>Water</strong>
                            <small>Request now</small>
                        </span>
                    </button>

                    <button class="quick-action-btn quick-action-waiter js-service-btn" data-service-type="waiter" type="button" >
                        <span class="quick-action-icon"><i class="fa-solid fa-user-tie"></i></span>
                        <span class="quick-action-content">
                            <strong>Call Waiter</strong>
                            <small>Instant assistance</small>
                        </span>
                    </button>

                    <a href="#trackorderlive" class="quick-action-btn quick-action-track">
                        <span class="quick-action-icon">
                            <i class="fa-solid fa-receipt"></i>
                        </span>
                        <span class="quick-action-content">
                            <strong>Track Order</strong>
                            <small>Live order status</small>
                        </span>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <section class="menu-discovery-section section-space-sm">
        <div class="app-container">
            <div class="menu-discovery-shell">

                <?php
                $resolvedOfferBanners = [];

                foreach ($offerBanners as $offerBanner) {
                    $imagePath = trim((string)($offerBanner['image_path'] ?? ''));
                    if ($imagePath === '') {
                        continue;
                    }

                    $resolvedOfferBanners[] = [
                        'id' => (string)($offerBanner['id'] ?? ''),
                        'image_url' => customer_resolve_asset_url($imagePath, ''),
                    ];
                }
                ?>

                <?php if (!empty($resolvedOfferBanners)): ?>
                    <div class="promo-banner-slider <?= count($resolvedOfferBanners) > 1 ? 'is-multi' : 'is-single' ?>" id="promoBannerSlider">
                        <div class="promo-banner-slider-track">
                            <?php foreach ($resolvedOfferBanners as $index => $banner): ?>
                                <div class="promo-banner-slide <?= $index === 0 ? 'is-active' : '' ?>">
                                    <img
                                        src="<?= rs_html_escape($banner['image_url']) ?>"
                                        alt="Offer Banner"
                                        class="promo-banner-image"
                                        loading="<?= $index === 0 ? 'eager' : 'lazy' ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (count($resolvedOfferBanners) > 1): ?>
                            <div class="promo-banner-dots">
                                <?php foreach ($resolvedOfferBanners as $index => $banner): ?>
                                    <button
                                        type="button"
                                        class="promo-banner-dot <?= $index === 0 ? 'is-active' : '' ?>"
                                        data-banner-index="<?= $index ?>"
                                        aria-label="Offer banner <?= $index + 1 ?>">
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="menu-discovery-head">
                    <div class="menu-discovery-copy">
                        <span class="menu-discovery-kicker"><?= rs_html_escape((string)($customerContent['menu_discovery']['kicker'] ?? '')) ?></span>
                        <h3 class="menu-discovery-title"><?= rs_html_escape((string)($customerContent['menu_discovery']['title'] ?? '')) ?></h3>
                        <p class="menu-discovery-text"><?= rs_html_escape((string)($customerContent['menu_discovery']['description'] ?? '')) ?></p>
                    </div>

                    <div class="menu-discovery-badge">
                        <i class="fa-solid fa-fire-flame-curved"></i>
                        <?= rs_html_escape((string)($customerContent['menu_discovery']['badge'] ?? '')) ?>
                    </div>
                </div>

                <div class="menu-toolbar-premium">
                    <div class="toolbar-search-premium">
                        <span class="toolbar-search-icon">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" id="menuSearchInput" placeholder="Search dishes, drinks, desserts...">
                    </div>

                    <div class="toolbar-filter-group">
                        <button class="toolbar-filter-btn active" type="button" data-food-filter="all">All</button>
                        <button class="toolbar-filter-btn" type="button" data-food-filter="veg">Veg</button>
                        <button class="toolbar-filter-btn" type="button" data-food-filter="non-veg">Non-Veg</button>
                        <button class="toolbar-filter-btn" type="button" data-food-filter="drink">Drinks</button>
                        <button class="toolbar-filter-btn" type="button" data-food-filter="dessert">Desserts</button>
                    </div>
                </div>

                <div class="category-rail-wrap">
                    <div class="category-rail" id="menuCategoryRail">
                        <button class="category-pill active" type="button" data-category="all">
                            <span class="category-pill-icon"><i class="fa-solid fa-border-all"></i></span>
                            <span>All Items</span>
                        </button>

                        <?php foreach ($menuCategories as $cat): ?>
                            <?php $catSlug = customer_slugify_local((string)$cat['name']); ?>
                            <button class="category-pill" type="button" data-category="<?= rs_html_escape($catSlug) ?>">
                                <span class="category-pill-icon"><i class="fa-solid fa-utensils"></i></span>
                                <span><?= rs_html_escape((string)$cat['name']) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="section-space menu-collection-section">
        <div class="app-container">

            <div class="section-head-premium">
                <div>
                    <span class="section-kicker">Curated Picks</span>
                    <h3 class="section-title-premium">Recommended for You</h3>
                    <p class="section-subtitle-premium">
                        Handpicked dishes curated for a more indulgent and premium dining experience.
                    </p>
                </div>
            </div>

            <div class="recommended-grid">
                <?php foreach ($recommendedItems as $item): ?>
                    <?php
                    $recommendedItemId = (string)($item['id'] ?? '');
                    $recommendedQty = 0;

                    foreach ($cartItems as $cartRow) {
                        $cartMenuItemId = (string)($cartRow['menu_item_id'] ?? $cartRow['id'] ?? '');
                        if ($cartMenuItemId === $recommendedItemId) {
                            $recommendedQty = max(0, (int)($cartRow['quantity'] ?? 0));
                            break;
                        }
                    }

                    $recommendedBadgeClass = 'badge-nonveg';
                    if (($item['type'] ?? '') === 'veg') {
                        $recommendedBadgeClass = 'badge-veg';
                    } elseif (($item['type'] ?? '') === 'dessert') {
                        $recommendedBadgeClass = 'badge-dessert';
                    } elseif (($item['type'] ?? '') === 'drink') {
                        $recommendedBadgeClass = 'badge-drink';
                    } elseif (($item['type'] ?? '') === 'water') {
                        $recommendedBadgeClass = 'badge-water';
                    }
                    ?>
                    <article
                        class="premium-food-card recommended-card js-menu-card"
                        data-item-id="<?= rs_html_escape($recommendedItemId) ?>"
                        data-item-name="<?= rs_html_escape((string)$item['name']) ?>"
                        data-item-price="<?= rs_html_escape((string)$item['price']) ?>"
                        data-item-category="Recommended">

                        <div class="premium-food-media">
                            <img src="<?= rs_html_escape((string)$item['image_url']) ?>" alt="<?= rs_html_escape((string)$item['name']) ?>">
                            <div class="premium-food-overlay"></div>

                            <span class="premium-food-badge <?= rs_html_escape($recommendedBadgeClass) ?>">
                                <?= rs_html_escape(ucfirst((string)$item['type'])) ?>
                            </span>

                            <span class="premium-food-tag">
                                <?= rs_html_escape((string)$item['badge']) ?>
                            </span>

                            <button class="premium-wishlist-btn" type="button" aria-label="Wishlist">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                        </div>

                        <div class="premium-food-body">
                            <div class="premium-food-top">
                                <div class="premium-food-title-block">
                                    <h4 class="premium-food-title"><?= rs_html_escape((string)$item['name']) ?></h4>
                                    <p class="premium-food-desc"><?= rs_html_escape((string)$item['description']) ?></p>
                                </div>

                                <div class="premium-food-rating">
                                    <i class="fa-solid fa-star"></i>
                                    <span><?= rs_html_escape((string)$item['rating']) ?></span>
                                </div>
                            </div>

                            <div class="premium-food-meta">
                                <span class="premium-meta-pill">
                                    <i class="fa-regular fa-clock"></i>
                                    <?= rs_html_escape((string)$item['time']) ?>
                                </span>
                                <span class="premium-meta-pill">
                                    <i class="fa-solid fa-sparkles"></i>
                                    Recommended
                                </span>
                            </div>

                            <div class="premium-food-bottom">
                                <div class="premium-price-block">
                                    <span class="premium-price-label">Starting at</span>
                                    <div class="premium-price"><?= rs_html_escape(customer_money((float)$item['price'])) ?></div>
                                </div>

                                <div
                                    class="card-cart-actions js-card-cart-actions"
                                    data-menu-item-id="<?= rs_html_escape($recommendedItemId) ?>"
                                    data-item-name="<?= rs_html_escape((string)$item['name']) ?>"
                                    data-item-price="<?= rs_html_escape((string)$item['price']) ?>"
                                    data-item-category="Recommended">

                                    <button
                                        class="premium-add-btn add-to-cart-btn js-add-to-cart-btn"
                                        type="button"
                                        data-item-id="<?= rs_html_escape($recommendedItemId) ?>"
                                        data-item-name="<?= rs_html_escape((string)$item['name']) ?>"
                                        data-item-price="<?= rs_html_escape((string)$item['price']) ?>"
                                        data-item-category="Recommended"
                                        <?= $recommendedQty > 0 ? 'hidden' : '' ?>>
                                        Add to Cart
                                    </button>

                                    <div
                                        class="card-qty-box js-card-qty-box"
                                        <?= $recommendedQty > 0 ? '' : 'hidden' ?>>
                                        <button
                                            type="button"
                                            class="card-qty-btn js-card-qty-btn"
                                            data-card-action="decrease"
                                            data-item-id="<?= rs_html_escape($recommendedItemId) ?>"
                                            aria-label="Decrease quantity">
                                            −
                                        </button>

                                        <span
                                            class="card-qty-value js-card-qty-value"
                                            data-item-id="<?= rs_html_escape($recommendedItemId) ?>">
                                            <?= rs_html_escape((string)max(1, $recommendedQty ?: 1)) ?>
                                        </span>

                                        <button
                                            type="button"
                                            class="card-qty-btn card-qty-btn-plus js-card-qty-btn"
                                            data-card-action="increase"
                                            data-item-id="<?= rs_html_escape($recommendedItemId) ?>"
                                            aria-label="Increase quantity">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <section class="section-space menu-collection-section" id="menuSection">
        <div class="app-container">

            <div class="section-head-premium">
                <div>
                    <span class="section-kicker">Full Menu</span>
                    <h3 class="section-title-premium">Explore Our Menu</h3>
                    <p class="section-subtitle-premium">
                        Freshly prepared dishes from every category, served with branch-specific pricing and availability.
                    </p>
                </div>
            </div>

            <?php if (!empty($menuItems)): ?>
                <div class="db-menu-grid" id="dbMenuGrid">
                    <?php foreach ($menuItems as $item): ?>
                        <?php
                        $categorySlug = customer_slugify_local((string)($item['category_name'] ?? ''));
                        $price = $item['selling_price'] !== null ? $item['selling_price'] : ($item['base_price'] ?? 0);
                        $badgeLabel = customer_food_badge_label($item);
                        $badgeClass = customer_food_badge_class($item);
                        $img = customer_resolve_asset_url($item['image_url'] ?? null, 'default/default_food.jpg');
                        $time = !empty($item['preparation_time_mins']) ? ((int)$item['preparation_time_mins'] . ' mins') : 'Freshly prepared';

                        $menuItemId = (string)($item['id'] ?? '');
                        $menuCardQty = 0;

                        foreach ($cartItems as $cartRow) {
                            $cartMenuItemId = (string)($cartRow['menu_item_id'] ?? $cartRow['id'] ?? '');
                            if ($cartMenuItemId === $menuItemId) {
                                $menuCardQty = max(0, (int)($cartRow['quantity'] ?? 0));
                                break;
                            }
                        }
                        ?>
                        <article
                            class="premium-food-card db-food-card js-menu-card"
                            data-item-id="<?= rs_html_escape($menuItemId) ?>"
                            data-category="<?= rs_html_escape($categorySlug) ?>"
                            data-food-type="<?= rs_html_escape(strtolower($badgeLabel)) ?>"
                            data-search="<?= rs_html_escape(strtolower((string)(($item['name'] ?? '') . ' ' . ($item['description'] ?? '') . ' ' . ($item['category_name'] ?? '')))) ?>"
                            data-item-name="<?= rs_html_escape((string)$item['name']) ?>"
                            data-item-price="<?= rs_html_escape((string)$price) ?>"
                            data-item-category="<?= rs_html_escape((string)($item['category_name'] ?? '')) ?>">

                            <div class="premium-food-media">
                                <img src="<?= $img ?>" alt="<?= rs_html_escape((string)$item['name']) ?>">
                                <div class="premium-food-overlay"></div>

                                <span class="premium-food-badge <?= rs_html_escape($badgeClass) ?>">
                                    <?= rs_html_escape($badgeLabel) ?>
                                </span>

                                <span class="premium-food-tag">
                                    <?= rs_html_escape((string)($item['category_name'] ?? 'Menu')) ?>
                                </span>

                                <button class="premium-wishlist-btn" type="button" aria-label="Wishlist">
                                    <i class="fa-regular fa-heart"></i>
                                </button>
                            </div>

                            <div class="premium-food-body">
                                <div class="premium-food-top">
                                    <div class="premium-food-title-block">
                                        <h4 class="premium-food-title"><?= rs_html_escape((string)$item['name']) ?></h4>
                                        <p class="premium-food-desc">
                                            <?= rs_html_escape((string)($item['description'] ?: 'Freshly prepared for your table with premium ingredients.')) ?>
                                        </p>
                                    </div>

                                    <div class="premium-food-rating premium-food-rating-soft">
                                        <i class="fa-solid fa-utensils"></i>
                                        <span>Fresh</span>
                                    </div>
                                </div>

                                <div class="premium-food-meta">
                                    <span class="premium-meta-pill">
                                        <i class="fa-regular fa-clock"></i>
                                        <?= rs_html_escape($time) ?>
                                    </span>

                                    <?php if (!empty($item['stock_status'])): ?>
                                        <span class="premium-meta-pill">
                                            <i class="fa-solid fa-box-open"></i>
                                            <?= rs_html_escape(ucwords(str_replace('_', ' ', (string)$item['stock_status']))) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="premium-food-bottom">
                                    <div class="premium-price-block">
                                        <span class="premium-price-label">Price</span>
                                        <div class="premium-price"><?= rs_html_escape(customer_money((float)$price)) ?></div>
                                    </div>

                                    <div
                                        class="card-cart-actions js-card-cart-actions"
                                        data-menu-item-id="<?= rs_html_escape($menuItemId) ?>"
                                        data-item-name="<?= rs_html_escape((string)$item['name']) ?>"
                                        data-item-price="<?= rs_html_escape((string)$price) ?>"
                                        data-item-category="<?= rs_html_escape((string)($item['category_name'] ?? '')) ?>">

                                        <button
                                            class="premium-add-btn add-to-cart-btn js-add-to-cart-btn"
                                            type="button"
                                            data-item-id="<?= rs_html_escape($menuItemId) ?>"
                                            data-item-name="<?= rs_html_escape((string)$item['name']) ?>"
                                            data-item-price="<?= rs_html_escape((string)$price) ?>"
                                            data-item-category="<?= rs_html_escape((string)($item['category_name'] ?? '')) ?>"
                                            <?= $menuCardQty > 0 ? 'hidden' : '' ?>>
                                            Add to Cart
                                        </button>

                                        <div
                                            class="card-qty-box js-card-qty-box"
                                            <?= $menuCardQty > 0 ? '' : 'hidden' ?>>
                                            <button
                                                type="button"
                                                class="card-qty-btn js-card-qty-btn"
                                                data-card-action="decrease"
                                                data-item-id="<?= rs_html_escape($menuItemId) ?>"
                                                aria-label="Decrease quantity">
                                                −
                                            </button>

                                            <span
                                                class="card-qty-value js-card-qty-value"
                                                data-item-id="<?= rs_html_escape($menuItemId) ?>">
                                                <?= rs_html_escape((string)max(1, $menuCardQty ?: 1)) ?>
                                            </span>

                                            <button
                                                type="button"
                                                class="card-qty-btn card-qty-btn-plus js-card-qty-btn"
                                                data-card-action="increase"
                                                data-item-id="<?= rs_html_escape($menuItemId) ?>"
                                                aria-label="Increase quantity">
                                                +
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="premium-empty-state">
                    <div class="premium-empty-icon">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <h4>No menu items available</h4>
                    <p>This branch currently has no active menu items to display.</p>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <section class="section-space kitchen-video-section">
        <div class="app-container">

            <div class="kitchen-video-header">
                <div class="kitchen-video-title">
                    <span class="kitchen-video-kicker">Kitchen Live</span>
                    <h3>Chef Movements</h3>
                    <p class="kitchen-video-subtitle">
                        Watch signature dishes come alive with premium chef action, plating, and finishing.
                    </p>
                </div>

                <div class="kitchen-slider-meta">
                    <div class="kitchen-slider-live-pill">
                        <span class="slider-live-dot"></span>
                        <span>Auto Showcase</span>
                    </div>

                    <div class="kitchen-slider-dots" id="kitchenDots"></div>
                </div>
            </div>

            <?php if (!empty($kitchenVideos)): ?>
                <div class="kitchen-slider-viewport">
                    <div class="kitchen-slider-track" id="kitchenSlider">
                        <?php foreach ($kitchenVideos as $item): ?>
                            <?php
                            $videoTitle = (string)($item['name'] ?? 'Kitchen Special');
                            $videoTime  = !empty($item['preparation_time_mins']) ? ((int)$item['preparation_time_mins'] . ' mins') : 'Freshly prepared';
                            $isVeg = isset($item['is_veg']) ? (int)$item['is_veg'] : 0;
                            $foodType = $isVeg === 1 ? 'Veg' : 'Non-Veg';
                            $foodBadgeClass = $isVeg === 1 ? 'veg' : 'nonveg';
                            $videoUrl  = customer_resolve_asset_url($item['video_url'] ?? null, '');
                            $posterUrl = customer_resolve_asset_url($item['image_url'] ?? null, 'default/default_food.jpg');
                            ?>
                            <article class="kitchen-video-card">
                                <video
                                    class="kitchen-video"
                                    muted
                                    loop
                                    playsinline
                                    preload="metadata"
                                    poster="<?= $posterUrl ?>">
                                    <source src="<?= $videoUrl ?>" type="video/mp4">
                                </video>

                                <div class="kitchen-video-overlay">
                                    <div class="kitchen-video-topline">
                                        <span class="kitchen-food-badge <?= rs_html_escape($foodBadgeClass) ?>">
                                            <?= rs_html_escape($foodType) ?>
                                        </span>

                                        <span class="kitchen-live-mini">
                                            <span class="slider-live-dot"></span>
                                            Live
                                        </span>
                                    </div>

                                    <div class="kitchen-video-bottomline">
                                        <div class="kitchen-video-label">
                                            <span class="kitchen-video-name"><?= rs_html_escape($videoTitle) ?></span>
                                            <span class="kitchen-video-time">
                                                <i class="fa-regular fa-clock"></i>
                                                <?= rs_html_escape($videoTime) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="premium-empty-state">
                    <div class="premium-empty-icon">
                        <i class="fa-solid fa-video"></i>
                    </div>
                    <h4>No kitchen videos available</h4>
                    <p>Kitchen movement videos will appear here once video URLs are added to menu items.</p>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <section class="section-space live-order-section">
        <div class="app-container">

            <div id="trackorderlive" class="section-head-premium">
                <div>
                    <span class="section-kicker">Live Tracking</span>
                    <h3 class="section-title-premium">Track Your Order</h3>
                    <p class="section-subtitle-premium">
                        Follow each ordered item in real time, from kitchen acceptance to final table delivery.
                    </p>
                </div>
            </div>

            <div class="live-order-shell" id="liveOrderShell">
                <?php if (!empty($activeOrderItems)): ?>
                    <?php foreach ($activeOrderItems as $trackItem): ?>
                        <?php
                        $trackName = (string)($trackItem['item_name_snapshot'] ?? 'Menu Item');
                        $trackQty = (int)($trackItem['quantity'] ?? 1);
                        $trackStatus = strtolower((string)($trackItem['status'] ?? 'pending'));
                        $trackPrepMins = (int)($trackItem['preparation_time_mins'] ?? 0);
                        $trackEstimated = $trackPrepMins > 0 ? $trackPrepMins . ' mins' : 'Freshly prepared';

                        $cardClass = 'is-preparing';
                        $statusPillClass = 'order-status-preparing';
                        $statusPillIcon = 'fa-fire-burner';
                        $statusLabel = 'Preparing';
                        $progressPercent = 25;

                        if (in_array($trackStatus, ['new', 'pending', 'placed'], true)) {
                            $statusPillIcon = 'fa-cart-shopping';
                            $statusLabel = 'New';
                            $progressPercent = 15;
                        } elseif ($trackStatus === 'accepted') {
                            $statusPillIcon = 'fa-clipboard-check';
                            $statusLabel = 'Accepted';
                            $progressPercent = 35;
                        } elseif (in_array($trackStatus, ['preparing', 'cooking'], true)) {
                            $statusLabel = 'Preparing';
                            $progressPercent = 68;
                        } elseif ($trackStatus === 'ready') {
                            $cardClass = 'is-ready';
                            $statusPillClass = 'order-status-ready';
                            $statusPillIcon = 'fa-circle-check';
                            $statusLabel = 'Ready';
                            $progressPercent = 100;
                        } elseif ($trackStatus === 'served') {
                            $cardClass = 'is-ready';
                            $statusPillClass = 'order-status-ready';
                            $statusPillIcon = 'fa-circle-check';
                            $statusLabel = 'Served';
                            $progressPercent = 100;
                        }

                        $orderedAt = !empty($trackItem['created_at']) ? date('g:i A', strtotime((string)$trackItem['created_at'])) : 'Just now';
                        $kitchenLabel = (string)($trackItem['kitchen_station_name'] ?? 'Kitchen Queue');
                        ?>
                        <details class="order-track-card <?= rs_html_escape($cardClass) ?>" open>
                            <summary class="order-track-summary">
                                <div class="order-track-main">
                                    <div class="order-track-left">
                                        <div class="order-track-icon">
                                            <i class="fa-solid fa-bowl-food"></i>
                                        </div>

                                        <div class="order-track-copy">
                                            <h4 class="order-track-name"><?= rs_html_escape($trackName) ?> × <?= rs_html_escape((string)$trackQty) ?></h4>
                                            <p class="order-track-note">Estimated delivery in <?= rs_html_escape($trackEstimated) ?></p>
                                        </div>
                                    </div>

                                    <div class="order-track-right">
                                        <span class="order-status-pill <?= rs_html_escape($statusPillClass) ?>">
                                            <i class="fa-solid <?= rs_html_escape($statusPillIcon) ?>"></i>
                                            <?= rs_html_escape($statusLabel) ?>
                                        </span>

                                        <span class="order-progress-label"><?= rs_html_escape((string)$progressPercent) ?>%</span>

                                        <span class="order-expand-icon">
                                            <i class="fa-solid fa-chevron-down"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="order-progress-wrap">
                                    <div class="order-progress-line">
                                        <div class="order-progress-fill" style="width: <?= rs_html_escape((string)$progressPercent) ?>%;"></div>
                                    </div>
                                </div>
                            </summary>

                            <div class="order-track-detail">
                                <div class="order-detail-top">
                                    <div class="order-detail-meta">
                                        <span><i class="fa-regular fa-clock"></i> Ordered at <?= rs_html_escape($orderedAt) ?></span>
                                        <span><i class="fa-solid fa-kitchen-set"></i> <?= rs_html_escape($kitchenLabel) ?></span>
                                        <span><i class="fa-solid fa-hourglass-half"></i> Approx. <?= rs_html_escape($trackEstimated) ?></span>
                                    </div>
                                </div>

                                <div class="order-timeline">
                                    <div class="timeline-step done">
                                        <div class="timeline-node"><i class="fa-solid fa-cart-shopping"></i></div>
                                        <div class="timeline-content">
                                            <h5>Order Created</h5>
                                            <p>Your item was successfully added to the kitchen queue.</p>
                                            <span><?= rs_html_escape($orderedAt) ?></span>
                                        </div>
                                    </div>

                                    <div class="timeline-step <?= in_array($trackStatus, ['accepted', 'preparing', 'cooking', 'ready', 'served'], true) ? 'done' : 'pending' ?>">
                                        <div class="timeline-node"><i class="fa-solid fa-clipboard-check"></i></div>
                                        <div class="timeline-content">
                                            <h5>Accepted by Kitchen</h5>
                                            <p>The chef station accepted your order and started preparation planning.</p>
                                            <span><?= !empty($trackItem['sent_to_kitchen_at']) ? rs_html_escape(date('g:i A', strtotime((string)$trackItem['sent_to_kitchen_at']))) : 'Pending' ?></span>
                                        </div>
                                    </div>

                                    <div class="timeline-step <?= in_array($trackStatus, ['preparing', 'cooking'], true) ? 'active' : (in_array($trackStatus, ['ready', 'served'], true) ? 'done' : 'pending') ?>">
                                        <div class="timeline-node"><i class="fa-solid fa-fire-burner"></i></div>
                                        <div class="timeline-content">
                                            <h5>Preparing</h5>
                                            <p>Your dish is currently being cooked with live kitchen progress.</p>
                                            <span><?= in_array($trackStatus, ['preparing', 'cooking'], true) ? 'In Progress' : 'Pending' ?></span>
                                        </div>
                                    </div>

                                    <div class="timeline-step <?= $trackStatus === 'ready' ? 'active' : ($trackStatus === 'served' ? 'done' : 'pending') ?>">
                                        <div class="timeline-node"><i class="fa-solid fa-bell-concierge"></i></div>
                                        <div class="timeline-content">
                                            <h5>Ready for Service</h5>
                                            <p>The dish will be plated and marked ready for table delivery.</p>
                                            <span><?= $trackStatus === 'ready' ? 'Ready Now' : ($trackStatus === 'served' ? 'Completed' : 'Pending') ?></span>
                                        </div>
                                    </div>

                                    <div class="timeline-step <?= $trackStatus === 'served' ? 'done' : 'pending' ?>">
                                        <div class="timeline-node"><i class="fa-solid fa-circle-check"></i></div>
                                        <div class="timeline-content">
                                            <h5>Served to Table</h5>
                                            <p>Your item will be delivered to your table by the service team.</p>
                                            <span><?= !empty($trackItem['served_at']) ? rs_html_escape(date('g:i A', strtotime((string)$trackItem['served_at']))) : 'Pending' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </details>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="premium-empty-state">
                        <div class="premium-empty-icon">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <h4>No active order to track</h4>
                        <p>Once you place an order, the live tracking timeline will appear here.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer class="customer-footer section-space">
        <div class="app-container">

            <div class="customer-footer-card">
                <div class="footer-col footer-brand-col">
                    <div class="footer-brand-row">
                        <div class="brand-badge">
                            <img src="<?= $brandLogo ?>" alt="Restaurant Logo" class="brand-logo">
                        </div>

                        <div>
                            <h4><?= rs_html_escape($brandName) ?></h4>
                            <p><?= rs_html_escape((string)($customerContent['footer']['description'] ?? '')) ?></p>
                        </div>
                    </div>
                </div>

                <div class="footer-col">
                    <h5>Branch</h5>
                    <p><?= rs_html_escape($branchName) ?>, Table <?= rs_html_escape($tableDisplay) ?></p>
                </div>

                <div class="footer-col">
                    <h5>Hours</h5>
                    <p><?= rs_html_escape($branchHours) ?></p>
                </div>

                <div class="footer-col">
                    <h5>Support</h5>
                    <p>Use “Call Waiter” for instant assistance.</p>
                </div>
            </div>

            <div class="customer-footer-bottom">
                <span>© 2026 <?= rs_html_escape($brandName) ?>. All rights reserved.</span>
                <span>Powered by Efactor Restaurant Eco-System</span>
            </div>
        </div>
    </footer>

    <div class="customer-bottom-dock">

        <input type="checkbox" id="cartToggle" class="dock-toggle-input">
        <input type="checkbox" id="waiterToggle" class="dock-toggle-input">
        <input type="checkbox" id="billToggle" class="dock-toggle-input">
        <input type="checkbox" id="paymentToggle" class="dock-toggle-input">

        <div class="app-container">
            <div class="customer-bottom-dock-inner">

                <div class="dock-cart-summary">
                    <div class="dock-cart-count"><?= rs_html_escape((string)$cartItemCount) ?> item<?= $cartItemCount === 1 ? '' : 's' ?> in cart</div>
                    <div class="dock-cart-total"><?= rs_html_escape(customer_money($cartGrandTotal)) ?></div>
                </div>

                <div class="dock-actions">
                    <button
                        type="button"
                        id="callWaiterBtn"
                        class="dock-btn dock-btn-secondary dock-waiter js-service-btn"
                        data-service-type="waiter">
                        <span class="dock-btn-icon">
                            <i class="fa-solid fa-user-tie"></i>
                        </span>
                        <span>Call Waiter</span>
                    </button>

                    <button type="button" id="waterBtn" class="dock-btn dock-btn-secondary dock-water js-service-btn" data-service-type="water">
                        <span class="dock-btn-icon">
                            <i class="fa-solid fa-glass-water"></i>
                        </span>
                        <span>Water</span>
                    </button>

                    <a href="#trackorderlive" id="trackOrderBtn" class="dock-btn dock-btn-secondary dock-track">
                        <span class="dock-btn-icon">
                            <i class="fa-solid fa-route"></i>
                        </span>
                        <span>Track Order</span>
                    </a>

                    <button
                        type="button"
                        id="openBillBtn"
                        class="dock-btn dock-btn-secondary dock-bill"
                        onclick="document.getElementById('billToggle').checked = true;">
                        <span class="dock-btn-icon">
                            <i class="fa-solid fa-file-invoice"></i>
                        </span>
                        <span>Bill</span>
                    </button>

                    <button
                        type="button"
                        id="viewCartBtn"
                        class="dock-btn dock-btn-primary dock-cart"
                        onclick="document.getElementById('cartToggle').checked = true;">
                        <span class="dock-btn-icon">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </span>
                        <span>View Cart</span>
                    </button>
                </div>

            </div>
        </div>

        <div class="waiter-float-panel">
            <button
                type="button"
                id="closeWaiterPanelBtn"
                class="mini-close-btn"
                aria-label="Close"
                onclick="document.getElementById('waiterToggle').checked = false;">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="waiter-float-head">
                <div class="waiter-float-badge">
                    <i class="fa-solid fa-bell-concierge"></i>
                </div>
                <div>
                    <h4>Waiter is on the way</h4>
                    <p><?= !empty($recentServiceRequest) ? 'Your latest service request is being handled.' : 'Your service request has been sent successfully.' ?></p>
                </div>
            </div>

            <div class="waiter-animation-wrap">
                <div class="waiter-track-line"></div>
                <div class="waiter-walk">
                    <div class="waiter-avatar">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                </div>
            </div>

            <div class="waiter-float-note">
                <span><i class="fa-regular fa-clock"></i> Estimated arrival: 1-2 mins</span>
            </div>
        </div>

        <div class="cart-sheet-overlay"></div>

        <div class="cart-sheet">
            <div class="cart-sheet-header">
                <div>
                    <span class="cart-sheet-kicker">Your Order</span>
                    <h3 class="cart-sheet-title">Cart Summary</h3>
                </div>

                <button
                    type="button"
                    id="closeCartBtn"
                    class="cart-sheet-close"
                    aria-label="Close Cart"
                    onclick="document.getElementById('cartToggle').checked = false;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="cart-sheet-body" id="cartSheetBody">
                <?php if (!empty($cartItems)): ?>
                    <?php foreach ($cartItems as $index => $cartItem): ?>
                        <article class="cart-item-card">
                            <div class="cart-item-main">
                                <div class="cart-item-copy">
                                    <h4><?= rs_html_escape((string)$cartItem['name']) ?></h4>
                                    <p><?= rs_html_escape((string)$cartItem['description']) ?></p>
                                </div>
                                <div class="cart-item-price"><?= rs_html_escape(customer_money((float)$cartItem['unit_price'])) ?></div>
                            </div>

                            <div class="cart-item-footer">
                                <div class="cart-qty-box">
                                    <button
                                        type="button"
                                        id="cartItem<?= rs_html_escape((string)($index + 1)) ?>MinusBtn"
                                        class="cart-qty-btn"
                                        data-cart-action="decrease"
                                        data-item-id="<?= rs_html_escape((string)($cartItem['menu_item_id'] ?: $cartItem['id'])) ?>">−</button>

                                    <span class="cart-qty-value"><?= rs_html_escape((string)$cartItem['quantity']) ?></span>

                                    <button
                                        type="button"
                                        id="cartItem<?= rs_html_escape((string)($index + 1)) ?>PlusBtn"
                                        class="cart-qty-btn cart-qty-btn-plus"
                                        data-cart-action="increase"
                                        data-item-id="<?= rs_html_escape((string)($cartItem['menu_item_id'] ?: $cartItem['id'])) ?>">+</button>
                                </div>
                                <div class="cart-line-total"><?= rs_html_escape(customer_money((float)$cartItem['line_total'])) ?></div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <article class="cart-item-card">
                        <div class="cart-item-main">
                            <div class="cart-item-copy">
                                <h4>Your cart is empty</h4>
                                <p>Add menu items to start your order.</p>
                            </div>
                            <div class="cart-item-price">₹0</div>
                        </div>
                    </article>
                <?php endif; ?>
            </div>

            <div class="cart-sheet-footer">
                <div class="cart-total-row">
                    <span>Total Amount</span>
                    <strong><?= rs_html_escape(customer_money($cartGrandTotal)) ?></strong>
                </div>

                <div class="cart-footer-actions">
                    <button id="reviewBillBtn" class="dock-btn dock-btn-secondary" type="button">
                        <span class="dock-btn-icon"><i class="fa-solid fa-receipt"></i></span>
                        <span>Review Bill</span>
                    </button>

                    <button id="proceedCheckoutBtn" class="dock-btn dock-btn-primary" type="button">
                        <span class="dock-btn-icon"><i class="fa-solid fa-credit-card"></i></span>
                        <span>Place Order</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- BILL MODAL -->
        <div class="customer-modal-overlay bill-modal-overlay"></div>

        <div class="customer-modal-shell bill-modal-shell" id="billModalShell" aria-hidden="true">
            <div class="customer-modal-card bill-modal-card">
                <div class="customer-modal-header">
                    <div>
                        <span class="customer-modal-kicker">Table Billing</span>
                        <h3 class="customer-modal-title">Order & Bill Summary</h3>
                    </div>

                    <button
                        type="button"
                        id="closeBillModalBtn"
                        class="customer-modal-close"
                        aria-label="Close Bill Modal"
                        onclick="document.getElementById('billToggle').checked = false;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="customer-modal-body">
                    <div class="bill-modal-status-row">
                        <div class="bill-status-card">
                            <span class="bill-status-label">Table</span>
                            <strong><?= rs_html_escape($tableDisplay) ?></strong>
                        </div>

                        <div class="bill-status-card">
                            <span class="bill-status-label">Order Status</span>
                            <strong id="billModalOrderStatus"><?= rs_html_escape($displayStatusText) ?></strong>
                        </div>

                        <div class="bill-status-card">
                            <span class="bill-status-label">Bill Status</span>
                            <strong id="billModalBillStatus"><?= !empty($activeBill['id']) ? ucwords(str_replace('_', ' ', (string)($activeBill['bill_status'] ?? 'new'))) : 'Pending' ?></strong>
                        </div>
                    </div>

                    <div class="bill-modal-section">
                        <div class="bill-modal-section-head">
                            <h4>Ordered Items</h4>
                            <span class="bill-modal-badge"><?= rs_html_escape((string)$cartItemCount) ?> Items</span>
                        </div>

                        <div class="bill-order-list" id="billOrderList">
                            <?php if (!empty($activeOrderItems)): ?>
                                <?php foreach ($activeOrderItems as $billItem): ?>
                                    <?php
                                    $billQty = (int)($billItem['quantity'] ?? 1);
                                    $billUnit = (float)($billItem['price_snapshot'] ?? 0);
                                    $billLine = (float)($billItem['line_total'] ?? ($billQty * $billUnit));
                                    ?>
                                    <article class="bill-order-item">
                                        <div class="bill-order-item-copy">
                                            <h5><?= rs_html_escape((string)($billItem['item_name_snapshot'] ?? 'Menu Item')) ?></h5>
                                            <p><?= rs_html_escape((string)$billQty) ?> × <?= rs_html_escape(customer_money($billUnit)) ?></p>
                                        </div>
                                        <div class="bill-order-item-total"><?= rs_html_escape(customer_money($billLine)) ?></div>
                                    </article>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="bill-empty-state" id="billOrderEmptyState">
                                    <i class="fa-solid fa-receipt"></i>
                                    <span>No ordered items yet.</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="bill-modal-section">
                        <div class="bill-modal-section-head">
                            <h4>Bill Breakdown</h4>
                            <span class="bill-modal-badge">Live</span>
                        </div>

                        <div class="bill-summary-card" id="billSummaryCard">
                            <div class="bill-summary-row">
                                <span>Subtotal</span>
                                <strong id="billSubtotalValue"><?= rs_html_escape(customer_money($cartSubtotal)) ?></strong>
                            </div>

                            <div class="bill-summary-row">
                                <span>Tax</span>
                                <strong id="billTaxValue"><?= rs_html_escape(customer_money($cartTaxAmount)) ?></strong>
                            </div>

                            <div class="bill-summary-row">
                                <span>Service Charge</span>
                                <strong id="billServiceChargeValue"><?= rs_html_escape(customer_money($cartServiceCharge)) ?></strong>
                            </div>

                            <div class="bill-summary-row">
                                <span>Discount</span>
                                <strong id="billDiscountValue"><?= rs_html_escape(customer_money($cartDiscountAmount)) ?></strong>
                            </div>

                            <div class="bill-summary-row bill-summary-row-total">
                                <span>Total Payable</span>
                                <strong id="billGrandTotalValue"><?= rs_html_escape(customer_money($cartGrandTotal)) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="customer-modal-footer">
                    <button
                        type="button"
                        id="billRequestBtn"
                        class="dock-btn dock-btn-secondary">
                        <span class="dock-btn-icon"><i class="fa-solid fa-file-invoice"></i></span>
                        <span>Request Bill</span>
                    </button>

                    <button
                        type="button"
                        id="payNowBtn"
                        class="dock-btn dock-btn-primary"
                        disabled>
                        <span class="dock-btn-icon"><i class="fa-solid fa-wallet"></i></span>
                        <span>Pay Now</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- PAYMENT MODAL -->
        <div class="customer-modal-overlay payment-modal-overlay" id="paymentModalOverlay"></div>

        <div class="customer-modal-shell payment-modal-shell" id="paymentModalShell" aria-hidden="true">
            <div class="customer-modal-card payment-modal-card" role="dialog" aria-modal="true" aria-labelledby="paymentModalTitle">
                <div class="customer-modal-header">
                    <div>
                        <span class="customer-modal-kicker">Secure Checkout</span>
                        <h3 class="customer-modal-title" id="paymentModalTitle">Choose Payment Method</h3>
                    </div>

                    <button
                        type="button"
                        id="closePaymentModalBtn"
                        class="customer-modal-close"
                        aria-label="Close Payment Modal">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="customer-modal-body">
                    <div class="payment-total-highlight">
                        <span class="payment-total-label">Amount to Pay</span>
                        <strong id="paymentGrandTotalValue"><?= rs_html_escape(customer_money($cartGrandTotal)) ?></strong>
                    </div>

                    <div class="payment-method-grid" id="paymentMethodGrid" aria-label="Payment method options">
                        <button type="button" class="payment-method-card is-active" data-payment-method="upi" aria-pressed="true">
                            <span class="payment-method-icon"><i class="fa-solid fa-qrcode"></i></span>
                            <span class="payment-method-copy">
                                <strong>UPI QR</strong>
                                <small>Scan and pay. Manager verifies payment.</small>
                            </span>
                        </button>

                        <button type="button" class="payment-method-card" data-payment-method="cash" aria-pressed="false">
                            <span class="payment-method-icon"><i class="fa-solid fa-money-bill-wave"></i></span>
                            <span class="payment-method-copy">
                                <strong>Cash at Table</strong>
                                <small>Waiter collects cash and confirms.</small>
                            </span>
                        </button>

                        <button type="button" class="payment-method-card payment-method-card--soon" data-payment-method="card" aria-disabled="true" disabled>
                            <span class="payment-method-icon"><i class="fa-regular fa-credit-card"></i></span>
                            <span class="payment-method-copy">
                                <strong>Card</strong>
                                <small>Coming soon</small>
                            </span>
                        </button>

                        <button type="button" class="payment-method-card payment-method-card--soon" data-payment-method="wallet" aria-disabled="true" disabled>
                            <span class="payment-method-icon"><i class="fa-solid fa-wallet"></i></span>
                            <span class="payment-method-copy">
                                <strong>Wallet</strong>
                                <small>Coming soon</small>
                            </span>
                        </button>
                    </div>

                    <div id="upiPaymentPane" class="payment-dynamic-pane is-active">
                        <div class="upi-qr-shell">
                            <div class="upi-qr-box" id="upiQrBox"></div>
                            <div class="upi-qr-copy">
                                <span>Scan this QR and pay exactly</span>
                                <strong id="upiQrAmountLabel">₹0</strong>
                                <small id="upiQrMetaLabel">Waiting to generate UPI QR</small>
                            </div>
                        </div>
                    </div>

                    <div id="cashPaymentPane" class="payment-dynamic-pane" hidden>
                        <div class="cash-wait-shell">
                            <div class="cash-wait-icon">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </div>
                            <div class="cash-wait-copy">
                                <strong>Cash collection request</strong>
                                <span>After you continue, the waiter will collect the bill amount at your table.</span>
                            </div>
                        </div>
                    </div>

                    <div id="paymentWaitingPane" class="payment-waiting-pane" hidden>
                        <div class="payment-waiting-loader"></div>
                        <div class="payment-waiting-copy">
                            <strong id="paymentWaitingTitle">Awaiting confirmation</strong>
                            <span id="paymentWaitingText">Please wait while the payment is being confirmed.</span>
                        </div>
                        <div class="payment-countdown-card" aria-live="polite">
                            <span>Expected confirmation within</span>
                            <strong id="paymentCountdownText">05:00</strong>
                            <div class="payment-countdown-track">
                                <div id="paymentCountdownProgress" class="payment-countdown-progress"></div>
                            </div>
                        </div>
                        <button type="button" id="cancelWaitingBtn" class="dock-btn dock-btn-secondary payment-wait-cancel-btn">
                            <span class="dock-btn-icon"><i class="fa-solid fa-xmark"></i></span>
                            <span>Close Waiting Screen</span>
                        </button>
                    </div>

                    <div class="payment-method-note">
                        <i class="fa-solid fa-shield-check"></i>
                        <span>Payment is confirmed only after waiter or manager verification.</span>
                    </div>
                </div>

                <div class="customer-modal-footer" id="paymentModalFooter">
                    <button
                        type="button"
                        id="backToBillBtn"
                        class="dock-btn dock-btn-secondary">
                        <span class="dock-btn-icon"><i class="fa-solid fa-arrow-left"></i></span>
                        <span>Cancel</span>
                    </button>

                    <button
                        type="button"
                        id="confirmPaymentBtn"
                        class="dock-btn dock-btn-primary">
                        <span class="dock-btn-icon" id="confirmPaymentBtnIcon"><i class="fa-solid fa-circle-check"></i></span>
                        <span id="confirmPaymentBtnLabel">Continue</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        window.CustomerPageData = {
            apiBase: <?= json_encode($customerApiBase, JSON_UNESCAPED_SLASHES) ?>,
            brandId: <?= json_encode($brandId, JSON_UNESCAPED_SLASHES) ?>,
            branchId: <?= json_encode($branchId, JSON_UNESCAPED_SLASHES) ?>,
            tableId: <?= json_encode((string)($table['id'] ?? ''), JSON_UNESCAPED_SLASHES) ?>,
            tableCode: <?= json_encode($tableDisplay, JSON_UNESCAPED_SLASHES) ?>,
            orderId: <?= json_encode((string)($activeOrder['id'] ?? ''), JSON_UNESCAPED_SLASHES) ?>,
            billId: <?= json_encode((string)($activeBill['id'] ?? ''), JSON_UNESCAPED_SLASHES) ?>,
            cartItemCount: <?= json_encode($cartItemCount, JSON_UNESCAPED_SLASHES) ?>,
            cartGrandTotal: <?= json_encode($cartGrandTotal, JSON_UNESCAPED_SLASHES) ?>,
            currency: <?= json_encode((string)($brand['currency'] ?? 'INR'), JSON_UNESCAPED_SLASHES) ?>,
            upiId: <?= json_encode((string)($brand['upi_id'] ?? 'hotel@upi'), JSON_UNESCAPED_SLASHES) ?>,
            brandName: <?= json_encode((string)$brandName, JSON_UNESCAPED_SLASHES) ?>,
            paymentStatus: <?= json_encode((string)($activeBill['payment_status'] ?? 'unpaid'), JSON_UNESCAPED_SLASHES) ?>,
            billStatus: <?= json_encode((string)($activeBill['bill_status'] ?? ''), JSON_UNESCAPED_SLASHES) ?>,
            tableStatus: <?= json_encode((string)($table['status'] ?? 'available'), JSON_UNESCAPED_SLASHES) ?>,
            orderStatus: <?= json_encode((string)($activeOrder['status'] ?? ''), JSON_UNESCAPED_SLASHES) ?>,
            enterpriseStatuses: {
                order: ['new', 'accepted', 'preparing', 'ready', 'served', 'completed', 'cancelled'],
                request: ['new', 'approved', 'cancelled', 'closed'],
                bill: ['new', 'approved', 'cancelled', 'closed'],
                payment: ['unpaid', 'in_processing', 'paid'],
                table: ['available', 'occupied', 'reserved', 'waiting', 'maintenance']
            },
        };
    </script>

    <div id="appStatusFx" class="app-status-fx" aria-live="polite" aria-atomic="true" hidden>
        <div class="app-status-fx-backdrop"></div>
        <div class="app-status-fx-card">
            <div class="app-status-fx-icon" id="appStatusFxIcon"></div>
            <h3 class="app-status-fx-title" id="appStatusFxTitle">Success</h3>
            <p class="app-status-fx-text" id="appStatusFxText">Completed successfully.</p>
        </div>
    </div>

    <div id="customerNotificationCenter" class="customer-notification-center" aria-live="polite" aria-atomic="false"></div>

    <div id="paymentSuccessFx" class="payment-success-fx" aria-live="polite" aria-atomic="true" hidden>
        <div class="payment-success-backdrop"></div>
        <div class="payment-success-card">
            <div class="payment-success-orbit">
                <div class="payment-success-ring"></div>
                <div class="payment-success-check">
                    <svg viewBox="0 0 120 120" role="img" aria-label="Payment successful">
                        <circle cx="60" cy="60" r="46"></circle>
                        <path d="M38 61.5 L53.5 77 L83 45"></path>
                    </svg>
                </div>
            </div>
            <h3 id="paymentSuccessTitle">Payment Successful</h3>
            <p id="paymentSuccessText">Bill closed. Thank you.</p>
        </div>
    </div>
    <script src="https://unpkg.com/qr-code-styling/lib/qr-code-styling.js"></script>
    <script src="<?= rs_html_escape(rs_base_url('RestaurantAssets/js/customer.js')) ?>"></script>

</body>

</html>