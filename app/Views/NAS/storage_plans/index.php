<?php

$basePath =
    isset($basePath) && $basePath !== ''
        ?
        rtrim($basePath, '/')
        :
        rtrim(site_url('NAS'), '/');

$assetsBase =
    isset($assetsBase) && $assetsBase !== ''
        ?
        rtrim($assetsBase, '/')
        :
        rtrim(base_url('assets/NAS'), '/');

$username =
    (string) ($username ?? 'User');

$userInitial =
    (string) (
        $userInitial
        ??
        strtoupper(
            substr(
                $username ?: 'U',
                0,
                1
            )
        )
    );

$plans =
    is_array($plans ?? null)
        ?
        $plans
        :
        [];

$storage =
    is_array($storage ?? null)
        ?
        $storage
        :
        [];

$purchaseHistory =
    is_array($purchaseHistory ?? null)
        ?
        $purchaseHistory
        :
        [];

$currentLimit =
    (int) ($storage['limit'] ?? 0);

$percentage =
    min(
        100,
        max(
            0,
            (float) (
                $storage['percentage']
                ??
                0
            )
        )
    );

$successMessage =
    (string) (
        session()->getFlashdata(
            'nas_storage_success'
        )
        ??
        ''
    );

$errorMessage =
    (string) (
        session()->getFlashdata(
            'nas_storage_error'
        )
        ??
        ''
    );

function nasPlanPriceLabel(array $plan): string
{
    if (
        !array_key_exists('price', $plan)
        ||
        $plan['price'] === null
    ) {
        return 'Price to be configured';
    }

    $currency =
        trim(
            (string) (
                $plan['currency']
                ??
                'INR'
            )
        );

    return
        $currency
        .
        ' '
        .
        number_format(
            (float) $plan['price'],
            2
        );
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <script>
    (function () {
        try {
            var savedTheme =
                localStorage.getItem(
                    'efactor_nas_home_theme'
                );

            document.documentElement
                .setAttribute(
                    'data-nas-theme',
                    savedTheme === 'dark'
                        ?
                        'dark'
                        :
                        'light'
                );
        } catch (error) {
            document.documentElement
                .setAttribute(
                    'data-nas-theme',
                    'light'
                );
        }
    })();
    </script>

    <title>Storage Plans - eFactor NAS</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

<style>
:root {
    --sp-bg: #f5f7fb;
    --sp-surface: #ffffff;
    --sp-soft: #fafbfe;

    --sp-text: #202431;
    --sp-strong: #171b25;
    --sp-muted: #6e7686;
    --sp-subtle: #9ba3b1;

    --sp-border: #e4e8ef;
    --sp-border-strong: #d6dce6;

    --sp-primary: #6257d9;
    --sp-primary-dark: #4f46b8;
    --sp-primary-soft: #f0efff;

    --sp-green: #168269;
    --sp-green-soft: #eaf8f3;

    --sp-amber: #ad7616;
    --sp-amber-soft: #fff6e4;

    --sp-red: #c5525c;
    --sp-red-soft: #fff0f2;

    --sp-shadow:
        0 7px 24px
        rgba(29, 38, 59, .055);

    --sp-shadow-hover:
        0 18px 42px
        rgba(29, 38, 59, .105);
}

html[data-nas-theme="dark"] {
    --sp-bg: #10141c;
    --sp-surface: #181d27;
    --sp-soft: #1d2330;

    --sp-text: #dfe5ef;
    --sp-strong: #f5f7fb;
    --sp-muted: #98a3b5;
    --sp-subtle: #717d90;

    --sp-border: #293241;
    --sp-border-strong: #354153;

    --sp-primary: #9789ff;
    --sp-primary-dark: #b2a8ff;
    --sp-primary-soft:
        rgba(134,119,255,.14);

    --sp-green: #4cd0a8;
    --sp-green-soft:
        rgba(76,208,168,.12);

    --sp-amber: #e9b94f;
    --sp-amber-soft:
        rgba(233,185,79,.12);

    --sp-red: #ff7d88;
    --sp-red-soft:
        rgba(255,125,136,.12);

    --sp-shadow:
        0 8px 28px
        rgba(0,0,0,.18);

    --sp-shadow-hover:
        0 20px 48px
        rgba(0,0,0,.28);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html,
body {
    min-height: 100%;
}

body {
    min-height: 100vh;

    font-family:
        Inter,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        Roboto,
        Arial,
        sans-serif;

    background: var(--sp-bg);

    color: var(--sp-text);

    -webkit-font-smoothing: antialiased;
}

.sp-header {
    position: sticky;
    top: 0;
    z-index: 100;

    min-height: 66px;

    padding:
        0
        clamp(18px, 3vw, 42px);

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    border-bottom:
        1px solid
        var(--sp-border);

    background:
        color-mix(
            in srgb,
            var(--sp-surface) 95%,
            transparent
        );

    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
}

.sp-brand {
    display: inline-flex;
    align-items: center;

    gap: 11px;

    color: var(--sp-text);
    text-decoration: none;
}

.sp-logo {
    width: 40px;
    height: 40px;

    display: grid;
    place-items: center;

    border:
        1px solid
        var(--sp-border);

    border-radius: 11px;

    background: #fff;
}

.sp-logo img {
    max-width: 30px;
    max-height: 30px;

    object-fit: contain;
}

.sp-brand-copy {
    display: flex;
    flex-direction: column;
}

.sp-brand-name {
    color: var(--sp-strong);

    font-size: 16px;
    font-weight: 720;
}

.sp-brand-subtitle {
    margin-top: 2px;

    color: var(--sp-muted);

    font-size: 10.5px;
}

.sp-header-actions {
    display: flex;
    align-items: center;

    gap: 8px;
}

.sp-theme,
.sp-back {
    min-height: 39px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 7px;

    border:
        1px solid
        var(--sp-border);

    border-radius: 10px;

    background: var(--sp-surface);
    color: var(--sp-text);

    cursor: pointer;

    text-decoration: none;

    transition:
        transform .18s ease,
        background .18s ease,
        border-color .18s ease;
}

.sp-theme {
    width: 40px;
}

.sp-back {
    padding: 0 13px;

    font-size: 12px;
    font-weight: 650;
}

.sp-theme:hover,
.sp-back:hover {
    transform: translateY(-1px);

    background: var(--sp-soft);

    border-color:
        var(--sp-border-strong);
}

html[data-nas-theme="dark"] .sp-theme {
    color: var(--sp-amber);

    background:
        var(--sp-amber-soft);
}

.sp-user {
    height: 40px;

    padding:
        4px 10px 4px 4px;

    display: flex;
    align-items: center;

    gap: 8px;

    border:
        1px solid
        var(--sp-border);

    border-radius: 999px;

    background: var(--sp-surface);
}

.sp-avatar {
    width: 31px;
    height: 31px;

    display: grid;
    place-items: center;

    border-radius: 50%;

    background:
        linear-gradient(
            135deg,
            #6257d9,
            #7d70e6
        );

    color: #fff;

    font-size: 12px;
    font-weight: 760;
}

.sp-user-name {
    max-width: 150px;

    overflow: hidden;

    color: var(--sp-text);

    font-size: 12px;
    font-weight: 600;

    text-overflow: ellipsis;
    white-space: nowrap;
}

.sp-main {
    width:
        min(
            1240px,
            calc(100% - 40px)
        );

    margin: 0 auto;

    padding:
        32px 0 54px;
}

.sp-hero {
    padding:
        clamp(27px,4vw,42px);

    display: grid;

    grid-template-columns:
        minmax(0,1fr)
        auto;

    gap: 30px;

    align-items: center;

    border:
        1px solid
        var(--sp-border);

    border-radius: 20px;

    background:
        linear-gradient(
            125deg,
            var(--sp-surface) 0%,
            var(--sp-surface) 59%,
            var(--sp-primary-soft) 100%
        );

    box-shadow: var(--sp-shadow);
}

.sp-eyebrow {
    width: fit-content;

    padding: 6px 10px;

    display: inline-flex;
    align-items: center;

    gap: 7px;

    border-radius: 999px;

    background:
        var(--sp-primary-soft);

    color:
        var(--sp-primary-dark);

    font-size: 10px;
    font-weight: 760;

    letter-spacing: .05em;
    text-transform: uppercase;
}

.sp-hero h1 {
    max-width: 760px;

    margin-top: 12px;

    color: var(--sp-strong);

    font-size:
        clamp(
            29px,
            3.5vw,
            43px
        );

    font-weight: 760;

    line-height: 1.13;

    letter-spacing: -1px;
}

.sp-hero p {
    max-width: 680px;

    margin-top: 11px;

    color: var(--sp-muted);

    font-size: 13.5px;
    line-height: 1.65;
}

.sp-hero-icon {
    width: 112px;
    height: 112px;

    display: grid;
    place-items: center;

    border:
        1px solid
        var(--sp-border);

    border-radius: 31px;

    background:
        var(--sp-primary-soft);

    color: var(--sp-primary);

    font-size: 35px;
}

.sp-message {
    margin-top: 17px;

    padding: 13px 15px;

    display: flex;
    align-items: center;

    gap: 9px;

    border:
        1px solid
        var(--sp-border);

    border-radius: 12px;

    font-size: 12px;
    font-weight: 650;
}

.sp-message.success {
    color: var(--sp-green);
    background: var(--sp-green-soft);
}

.sp-message.error {
    color: var(--sp-red);
    background: var(--sp-red-soft);
}

.sp-current {
    margin-top: 20px;

    padding: 18px 20px;

    display: grid;

    grid-template-columns:
        auto
        minmax(0,1fr)
        auto;

    gap: 15px;

    align-items: center;

    border:
        1px solid
        var(--sp-border);

    border-radius: 15px;

    background: var(--sp-surface);

    box-shadow: var(--sp-shadow);
}

.sp-current-icon {
    width: 44px;
    height: 44px;

    display: grid;
    place-items: center;

    border-radius: 12px;

    background:
        var(--sp-primary-soft);

    color:
        var(--sp-primary);
}

.sp-current-copy h2 {
    color: var(--sp-strong);

    font-size: 14px;
    font-weight: 720;
}

.sp-current-copy p {
    margin-top: 3px;

    color: var(--sp-muted);

    font-size: 11px;
}

.sp-bar {
    width: 100%;
    height: 7px;

    margin-top: 10px;

    overflow: hidden;

    border-radius: 999px;

    background: var(--sp-border);
}

.sp-bar-fill {
    height: 100%;

    border-radius: inherit;

    background:
        linear-gradient(
            90deg,
            #6257d9,
            #7c70e7
        );
}

.sp-current-limit {
    text-align: right;
}

.sp-current-limit strong {
    display: block;

    color: var(--sp-strong);

    font-size: 18px;
}

.sp-current-limit span {
    color: var(--sp-muted);

    font-size: 10px;
}

.sp-section-heading {
    margin:
        29px 0 13px;
}

.sp-section-heading h2 {
    color: var(--sp-strong);

    font-size: 18px;
    font-weight: 750;
}

.sp-section-heading p {
    margin-top: 4px;

    color: var(--sp-muted);

    font-size: 11.5px;
}

.sp-plans {
    display: grid;

    grid-template-columns:
        repeat(
            4,
            minmax(0,1fr)
        );

    gap: 14px;
}

.sp-card {
    position: relative;

    min-height: 390px;

    padding: 21px;

    display: flex;
    flex-direction: column;

    overflow: hidden;

    border:
        1px solid
        var(--sp-border);

    border-radius: 17px;

    background: var(--sp-surface);

    box-shadow: var(--sp-shadow);

    transition:
        transform .22s cubic-bezier(.2,.8,.2,1),
        border-color .22s ease,
        box-shadow .22s ease;
}

.sp-card:hover {
    transform: translateY(-4px);

    border-color:
        color-mix(
            in srgb,
            var(--sp-primary) 28%,
            var(--sp-border)
        );

    box-shadow:
        var(--sp-shadow-hover);
}

.sp-card.popular {
    border-color:
        color-mix(
            in srgb,
            var(--sp-primary) 43%,
            var(--sp-border)
        );
}

.sp-popular {
    position: absolute;
    top: 13px;
    right: 13px;

    padding: 5px 8px;

    border-radius: 999px;

    background:
        var(--sp-primary-soft);

    color:
        var(--sp-primary-dark);

    font-size: 9px;
    font-weight: 770;
}

.sp-plan-icon {
    width: 42px;
    height: 42px;

    display: grid;
    place-items: center;

    border-radius: 11px;

    background:
        var(--sp-primary-soft);

    color:
        var(--sp-primary);

    font-size: 16px;
}

.sp-card h3 {
    margin-top: 16px;

    color: var(--sp-strong);

    font-size: 17px;
}

.sp-capacity {
    margin-top: 6px;

    color: var(--sp-strong);

    font-size: 30px;
    font-weight: 790;

    letter-spacing: -.8px;
}

.sp-price {
    margin-top: 5px;

    color: var(--sp-primary);

    font-size: 11px;
    font-weight: 700;
}

.sp-description {
    min-height: 55px;

    margin-top: 9px;

    color: var(--sp-muted);

    font-size: 11px;
    line-height: 1.55;
}

.sp-features {
    margin:
        15px 0 20px;

    display: grid;

    gap: 9px;

    list-style: none;
}

.sp-features li {
    display: flex;

    gap: 8px;

    color: var(--sp-text);

    font-size: 10.7px;
    line-height: 1.42;
}

.sp-features i {
    margin-top: 2px;

    color: var(--sp-green);
}

.sp-card-action {
    margin-top: auto;
}

.sp-button {
    width: 100%;
    min-height: 42px;

    padding: 0 13px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    border:
        1px solid transparent;

    border-radius: 10px;

    background: var(--sp-primary);
    color: #fff;

    cursor: pointer;

    font-size: 11.5px;
    font-weight: 720;

    transition:
        transform .18s ease,
        background .18s ease,
        box-shadow .18s ease;
}

.sp-button:hover:not(:disabled) {
    transform: translateY(-1px);

    background:
        var(--sp-primary-dark);

    box-shadow:
        0 8px 18px
        rgba(98,87,217,.20);
}

.sp-button:disabled {
    cursor: default;

    color: var(--sp-muted);

    background: var(--sp-soft);

    border-color: var(--sp-border);

    box-shadow: none;
}

.sp-framework-note {
    margin-top: 17px;

    padding: 13px 15px;

    display: flex;

    gap: 9px;

    border:
        1px solid
        var(--sp-border);

    border-radius: 12px;

    background: var(--sp-soft);

    color: var(--sp-muted);

    font-size: 10.5px;
    line-height: 1.55;
}

.sp-framework-note i {
    margin-top: 2px;

    color: var(--sp-amber);
}

.sp-history {
    overflow: hidden;

    border:
        1px solid
        var(--sp-border);

    border-radius: 15px;

    background: var(--sp-surface);

    box-shadow: var(--sp-shadow);
}

.sp-history-head,
.sp-history-row {
    display: grid;

    grid-template-columns:
        minmax(145px,1.3fr)
        minmax(110px,.8fr)
        minmax(110px,.8fr)
        minmax(120px,.9fr)
        minmax(140px,1fr);

    gap: 12px;

    align-items: center;
}

.sp-history-head {
    padding: 11px 15px;

    border-bottom:
        1px solid
        var(--sp-border);

    background: var(--sp-soft);

    color: var(--sp-muted);

    font-size: 9px;
    font-weight: 760;

    text-transform: uppercase;
    letter-spacing: .04em;
}

.sp-history-row {
    min-height: 58px;

    padding: 10px 15px;

    border-bottom:
        1px solid
        var(--sp-border);

    color: var(--sp-text);

    font-size: 10.5px;
}

.sp-history-row:last-child {
    border-bottom: none;
}

.sp-history-order strong {
    display: block;

    color: var(--sp-strong);

    font-size: 10.7px;
}

.sp-history-order span {
    display: block;

    margin-top: 2px;

    color: var(--sp-muted);

    font-size: 9px;
}

.sp-status {
    width: fit-content;

    padding: 5px 8px;

    border-radius: 999px;

    font-size: 9px;
    font-weight: 750;
}

.sp-status.activated {
    color: var(--sp-green);
    background: var(--sp-green-soft);
}

.sp-status.pending {
    color: var(--sp-amber);
    background: var(--sp-amber-soft);
}

.sp-history-empty {
    padding: 28px 18px;

    color: var(--sp-muted);

    text-align: center;

    font-size: 11px;
}

@media (max-width: 1050px) {
    .sp-plans {
        grid-template-columns:
            repeat(
                2,
                minmax(0,1fr)
            );
    }
}

@media (max-width: 800px) {
    .sp-history {
        overflow-x: auto;
    }

    .sp-history-head,
    .sp-history-row {
        min-width: 720px;
    }
}

@media (max-width: 720px) {
    .sp-header {
        padding:
            0 12px;
    }

    .sp-brand-copy,
    .sp-user-name,
    .sp-back span {
        display: none;
    }

    .sp-back {
        width: 40px;

        padding: 0;
    }

    .sp-user {
        padding-right: 4px;

        border-color: transparent;

        background: transparent;
    }

    .sp-main {
        width:
            calc(100% - 24px);

        padding-top: 20px;
    }

    .sp-hero {
        grid-template-columns: 1fr;

        padding: 24px 21px;
    }

    .sp-hero-icon {
        display: none;
    }

    .sp-current {
        grid-template-columns:
            auto
            minmax(0,1fr);
    }

    .sp-current-limit {
        grid-column: 2;

        text-align: left;
    }
}

@media (max-width: 580px) {
    .sp-plans {
        grid-template-columns: 1fr;
    }

    .sp-card {
        min-height: auto;
    }
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation: none !important;
        transition: none !important;
    }
}
</style>

</head>

<body>

<header class="sp-header">

    <a
        href="<?= esc($basePath) ?>/home"
        class="sp-brand"
    >
        <span class="sp-logo">
            <img
                src="<?= esc($assetsBase) ?>/images/efactor.png"
                alt="eFactor"
            >
        </span>

        <span class="sp-brand-copy">
            <span class="sp-brand-name">
                eFactor NAS
            </span>

            <span class="sp-brand-subtitle">
                Storage plans
            </span>
        </span>
    </a>

    <div class="sp-header-actions">

        <button
            type="button"
            class="sp-theme"
            id="spThemeToggle"
            title="Switch theme"
            aria-label="Switch theme"
        >
            <i class="fas fa-moon"></i>
        </button>

        <div class="sp-user">
            <span class="sp-avatar">
                <?= esc($userInitial) ?>
            </span>

            <span class="sp-user-name">
                <?= esc($username) ?>
            </span>
        </div>

        <a
            href="<?= esc($basePath) ?>/home"
            class="sp-back"
            title="Back to NAS Home"
        >
            <i class="fas fa-arrow-left"></i>
            <span>Back</span>
        </a>

    </div>

</header>

<main class="sp-main">

    <section class="sp-hero">

        <div>
            <div class="sp-eyebrow">
                <i class="fas fa-database"></i>
                Storage upgrade
            </div>

            <h1>
                Choose the storage capacity your workspace needs.
            </h1>

            <p>
                The purchase framework is ready now.
                Payment-gateway verification will be connected later.
                Until then, selecting a larger plan creates a real purchase
                record and temporarily activates the quota immediately.
            </p>
        </div>

        <div
            class="sp-hero-icon"
            aria-hidden="true"
        >
            <i class="fas fa-cloud-arrow-up"></i>
        </div>

    </section>

    <?php if ($successMessage !== ''): ?>
        <div class="sp-message success">
            <i class="fas fa-circle-check"></i>
            <?= esc($successMessage) ?>
        </div>
    <?php endif; ?>

    <?php if ($errorMessage !== ''): ?>
        <div class="sp-message error">
            <i class="fas fa-circle-exclamation"></i>
            <?= esc($errorMessage) ?>
        </div>
    <?php endif; ?>

    <section class="sp-current">

        <div class="sp-current-icon">
            <i class="fas fa-hard-drive"></i>
        </div>

        <div class="sp-current-copy">
            <h2>Current storage</h2>

            <p>
                <?= esc((string) ($storage['used_formatted'] ?? '0 B')) ?>
                occupied,
                <?= esc((string) ($storage['remaining_formatted'] ?? '0 B')) ?>
                remaining
            </p>

            <div class="sp-bar">
                <div
                    class="sp-bar-fill"
                    style="width: <?= esc((string) $percentage) ?>%"
                ></div>
            </div>
        </div>

        <div class="sp-current-limit">
            <strong>
                <?= esc((string) ($storage['limit_formatted'] ?? '0 B')) ?>
            </strong>

            <span>
                Current total quota
            </span>
        </div>

    </section>

    <div class="sp-section-heading">
        <h2>Storage plans</h2>

        <p>
            Each plan is the new total account quota, not an amount added
            repeatedly to your current quota.
        </p>
    </div>

    <section class="sp-plans">

        <?php foreach ($plans as $plan): ?>

            <?php
                $planLimit =
                    (int) (
                        $plan['storage_bytes']
                        ??
                        0
                    );

                $canPurchase =
                    $planLimit
                    >
                    $currentLimit;

                $isPopular =
                    !empty(
                        $plan['is_popular']
                    );
            ?>

            <article
                class="sp-card <?= $isPopular ? 'popular' : '' ?>"
            >

                <?php if ($isPopular): ?>
                    <span class="sp-popular">
                        Popular
                    </span>
                <?php endif; ?>

                <div class="sp-plan-icon">
                    <i class="fas fa-cloud"></i>
                </div>

                <h3>
                    <?= esc((string) ($plan['name'] ?? 'Storage')) ?>
                </h3>

                <div class="sp-capacity">
                    <?= esc((string) ($plan['storage_formatted'] ?? '0 GB')) ?>
                </div>

                <div class="sp-price">
                    <?= esc(nasPlanPriceLabel($plan)) ?>
                </div>

                <p class="sp-description">
                    <?= esc((string) ($plan['description'] ?? '')) ?>
                </p>

                <ul class="sp-features">
                    <?php foreach (($plan['features'] ?? []) as $feature): ?>
                        <li>
                            <i class="fas fa-check"></i>

                            <span>
                                <?= esc((string) $feature) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="sp-card-action">

                    <form
                        method="post"
                        action="<?= esc($basePath) ?>/storage-plans/purchase"
                        onsubmit="return confirm('Select <?= esc((string) ($plan['name'] ?? 'this')) ?> plan with total storage <?= esc((string) ($plan['storage_formatted'] ?? '')) ?>?');"
                    >
                        <?= csrf_field() ?>

                        <input
                            type="hidden"
                            name="plan_code"
                            value="<?= esc((string) ($plan['code'] ?? '')) ?>"
                        >

                        <button
                            type="submit"
                            class="sp-button"
                            <?= $canPurchase ? '' : 'disabled' ?>
                        >

                            <?php if (!$canPurchase): ?>

                                <i class="fas fa-check"></i>
                                Current / smaller plan

                            <?php elseif (!empty($temporaryActivationEnabled)): ?>

                                <i class="fas fa-arrow-up"></i>
                                Choose plan

                            <?php else: ?>

                                <i class="fas fa-cart-shopping"></i>
                                Continue to purchase

                            <?php endif; ?>

                        </button>

                    </form>

                </div>

            </article>

        <?php endforeach; ?>

    </section>

    <div class="sp-framework-note">

        <i class="fas fa-shield-halved"></i>

        <div>
            The browser submits only the
            <strong>plan code</strong>.
            Storage bytes, plan name and future price are resolved again
            from the server-side database. In the current development mode,
            payment status is stored as
            <strong>not_integrated</strong>.
            Later the gateway will replace only the activation step.
        </div>

    </div>

    <div class="sp-section-heading">
        <h2>Recent storage purchases</h2>

        <p>
            These records remain useful after the payment gateway is connected.
        </p>
    </div>

    <section class="sp-history">

        <?php if (empty($purchaseHistory)): ?>

            <div class="sp-history-empty">
                <i class="fas fa-receipt"></i>
                No storage purchases yet.
            </div>

        <?php else: ?>

            <div class="sp-history-head">
                <div>Order</div>
                <div>Plan</div>
                <div>Quota</div>
                <div>Status</div>
                <div>Created</div>
            </div>

            <?php foreach ($purchaseHistory as $purchase): ?>

                <?php
                    $purchaseStatus =
                        strtolower(
                            (string) (
                                $purchase['purchase_status']
                                ??
                                'pending'
                            )
                        );
                ?>

                <div class="sp-history-row">

                    <div class="sp-history-order">
                        <strong>
                            <?= esc((string) ($purchase['order_reference'] ?? '')) ?>
                        </strong>

                        <span>
                            Payment:
                            <?= esc((string) ($purchase['payment_status'] ?? '')) ?>
                        </span>
                    </div>

                    <div>
                        <?= esc((string) ($purchase['plan_name'] ?? '')) ?>
                    </div>

                    <div>
                        <?= esc((string) ($purchase['storage_after_formatted'] ?? '')) ?>
                    </div>

                    <div>
                        <span
                            class="sp-status <?= $purchaseStatus === 'activated' ? 'activated' : 'pending' ?>"
                        >
                            <?= esc(ucfirst($purchaseStatus)) ?>
                        </span>
                    </div>

                    <div>
                        <?= esc((string) ($purchase['created_at'] ?? '')) ?>
                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </section>

</main>

<script>
(function () {
    'use strict';

    const STORAGE_KEY =
        'efactor_nas_home_theme';

    const root =
        document.documentElement;

    const button =
        document.getElementById(
            'spThemeToggle'
        );

    function getTheme() {
        return (
            root.getAttribute(
                'data-nas-theme'
            )
            ===
            'dark'
        )
            ?
            'dark'
            :
            'light';
    }

    function renderThemeButton() {
        if (!button) {
            return;
        }

        const dark =
            getTheme()
            ===
            'dark';

        const icon =
            button.querySelector('i');

        if (icon) {
            icon.className =
                dark
                    ?
                    'fas fa-sun'
                    :
                    'fas fa-moon';
        }

        button.title =
            dark
                ?
                'Switch to light theme'
                :
                'Switch to dark theme';

        button.setAttribute(
            'aria-label',
            button.title
        );
    }

    if (button) {
        button.addEventListener(
            'click',
            function () {

                const next =
                    getTheme()
                    ===
                    'dark'
                        ?
                        'light'
                        :
                        'dark';

                root.setAttribute(
                    'data-nas-theme',
                    next
                );

                try {
                    localStorage.setItem(
                        STORAGE_KEY,
                        next
                    );
                } catch (error) {}

                renderThemeButton();
            }
        );
    }

    renderThemeButton();
})();
</script>

</body>
</html>
