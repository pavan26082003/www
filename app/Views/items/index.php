

<!-- Bootstrap CSS-->
    <!--link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/bootstrap/css/bootstrap.min.css"-->
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/fontastic.css">
    <!-- Google fonts - Roboto -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
    <!-- jQuery Circle-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/grasp_mobile_progress_circle-1.0.0.min.css">
    <!-- Custom Scrollbar-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/style.default.css" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/custom.css">
    <!-- Favicon-->
    <link rel="shortcut icon" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/img/favicon.ico">






<?php
session_start();
$user_id = $login_user->id ?? null;
$_SESSION['user_id'] = $user_id;

// Get the 'search' parameter from the URL. JS encoding is handled where it is printed.
$searchQuery = isset($_GET['search']) ? (string) $_GET['search'] : '';
?>

<style>
    #item-table th {
        position: sticky;
        top: 0;
        z-index: 2;
        background-color: #f0f1ff;
    }

    #page-content {
        max-width: 100%;
        overflow-x: hidden;
        padding: 22px 30px !important;
    }

    #page-content > .card {
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
        padding: 24px !important;
        border-radius: 0;
    }

    #products-card-app {
        --items-bg: #1B1B1E;
        --items-panel: #252b37;
        --items-panel-soft: #373F51;
        --items-text: #F7FAFC;
        --items-muted: #A9BCD0;
        --items-border: rgba(169, 188, 208, .28);
        --items-accent: #58A4B0;
        --items-accent-strong: #373F51;
        --items-plum: #7e6a91;
        --items-amber: #D8DBE2;
        --items-graphite: #1B1B1E;
        --items-good: #58A4B0;
        --items-bad: #A9BCD0;
        --items-shadow: 0 22px 52px rgba(0, 0, 0, .32);
        background:
            radial-gradient(at 0% 0%, rgba(88, 164, 176, .28), transparent 34%),
            radial-gradient(at 100% 0%, rgba(169, 188, 208, .14), transparent 36%),
            linear-gradient(135deg, #1B1B1E 0%, #273043 48%, #373F51 100%);
        color: var(--items-text);
        border: 1px solid rgba(169, 188, 208, .22);
        border-radius: 8px;
        padding: 16px;
        margin-top: 14px;
        font-family: Roboto, Arial, sans-serif;
        max-width: 100%;
        overflow: hidden;
    }

    #products-card-app *,
    #products-card-app *::before,
    #products-card-app *::after {
        box-sizing: border-box;
    }

    #products-card-app.theme-light {
        --items-bg: #D8DBE2;
        --items-panel: #ffffff;
        --items-panel-soft: #eef2f6;
        --items-text: #1B1B1E;
        --items-muted: #526076;
        --items-border: rgba(55, 63, 81, .18);
        --items-accent: #58A4B0;
        --items-accent-strong: #373F51;
        --items-plum: #6f5c86;
        --items-amber: #A9BCD0;
        --items-graphite: #1B1B1E;
        --items-shadow: 0 12px 30px rgba(55, 63, 81, .12);
        background:
            radial-gradient(at 0% 0%, rgba(88, 164, 176, .22), transparent 34%),
            radial-gradient(at 100% 0%, rgba(169, 188, 208, .45), transparent 36%),
            linear-gradient(135deg, #D8DBE2 0%, #f7f9fb 48%, #A9BCD0 100%);
        border-color: rgba(55, 63, 81, .18);
    }

    .items-toolbar {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) minmax(330px, auto) auto;
        align-items: center;
        gap: 14px;
        padding: 14px;
        background:
            linear-gradient(135deg, rgba(88, 164, 176, .18), rgba(169, 188, 208, .32)),
            linear-gradient(180deg, color-mix(in srgb, var(--items-panel) 92%, #ffffff 8%), var(--items-panel));
        border: 1px solid color-mix(in srgb, var(--items-accent) 26%, var(--items-border));
        border-radius: 8px;
        margin-bottom: 16px;
        min-width: 0;
        box-shadow: 0 18px 38px rgba(15, 23, 42, .14), inset 0 1px 0 rgba(255,255,255,.12);
    }

    .items-toolbar-actions {
        display: grid;
        grid-template-columns: auto auto;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .items-search-wrap {
        display: block;
        width: 100%;
        min-width: 0;
    }

    .items-search-field {
        position: relative;
        min-width: 0;
    }

    .items-search-field .fa {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #ffffff;
        pointer-events: none;
        z-index: 1;
    }

    #item-card-search {
        width: 100%;
        min-height: 48px;
        padding: 11px 14px 11px 46px;
        border: 1px solid rgba(88, 164, 176, .38);
        border-radius: 8px;
        background:
            linear-gradient(135deg, rgba(55, 63, 81, .96), rgba(88, 164, 176, .88)) padding-box,
            linear-gradient(135deg, #D8DBE2, #A9BCD0) border-box;
        color: var(--items-text);
        font-size: 14px;
        font-weight: 700;
        line-height: 1.35;
        outline: none;
        box-shadow: 0 12px 24px rgba(55, 63, 81, .16), inset 0 1px 0 rgba(255,255,255,.14);
        transition: border-color .16s ease, box-shadow .16s ease, background .16s ease;
    }

    #item-card-search::placeholder {
        color: rgba(255, 255, 255, .76);
        font-weight: 600;
    }

    #item-card-search:focus {
        border-color: transparent;
        box-shadow: 0 0 0 3px rgba(88, 164, 176, .24), 0 16px 30px rgba(55, 63, 81, .2), inset 0 1px 0 rgba(255,255,255,.18);
    }

    .items-filter-panel {
        display: grid;
        grid-template-columns: auto minmax(145px, auto) minmax(170px, auto);
        align-items: center;
        gap: 10px;
        min-width: 0;
        padding: 8px;
        border: 1px solid color-mix(in srgb, var(--items-plum) 24%, var(--items-border));
        border-radius: 8px;
        background:
            linear-gradient(135deg, rgba(88, 164, 176, .16), rgba(169, 188, 208, .26)),
            color-mix(in srgb, var(--items-panel-soft) 72%, transparent);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.08);
    }

    .items-filter-title {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 36px;
        padding: 8px 11px;
        border-radius: 8px;
        background: linear-gradient(135deg, #373F51, #58A4B0);
        color: #ffffff;
        font-size: 13px;
        font-weight: 900;
        white-space: nowrap;
        box-shadow: 0 8px 16px rgba(55, 63, 81, .2);
    }

    .items-filter-title .fa {
        color: #ffffff;
    }

    .items-status-filter,
    .items-category-filter {
        display: grid;
        grid-template-columns: auto minmax(95px, 1fr);
        align-items: center;
        gap: 7px;
        min-width: 0;
    }

    .items-filter-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--items-text);
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: .02em;
    }

    .items-filter-label .fa {
        color: var(--items-accent);
        font-size: 13px;
    }

    .items-filter-select {
        width: 100%;
        min-height: 38px;
        padding: 8px 34px 8px 11px;
        border: 1px solid rgba(88, 164, 176, .34);
        border-radius: 8px;
        appearance: none;
        -webkit-appearance: none;
        background:
            linear-gradient(45deg, transparent 50%, currentColor 50%) right 15px top 16px / 6px 6px no-repeat,
            linear-gradient(135deg, color-mix(in srgb, var(--items-panel) 82%, #D8DBE2 18%), var(--items-panel));
        color: var(--items-text);
        font-size: 13px;
        font-weight: 800;
        line-height: 1.2;
        outline: none;
        cursor: pointer;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.08), 0 8px 16px rgba(15, 23, 42, .08);
        transition: border-color .16s ease, box-shadow .16s ease, transform .16s ease;
    }

    .items-filter-select:hover {
        transform: translateY(-1px);
        border-color: var(--items-accent);
    }

    .items-filter-select:focus {
        border-color: var(--items-accent);
        box-shadow: 0 0 0 3px rgba(88, 164, 176, .2), 0 10px 20px rgba(55, 63, 81, .14);
    }

    .items-filter-select option,
    #item-card-length option {
        background: var(--items-panel);
        color: var(--items-text);
        font-weight: 800;
    }

    .items-length-control {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 48px;
        padding: 7px 10px;
        border: 1px solid rgba(169, 188, 208, .34);
        border-radius: 8px;
        background:
            linear-gradient(135deg, rgba(216, 219, 226, .42), rgba(88, 164, 176, .12)),
            color-mix(in srgb, var(--items-panel-soft) 84%, #A9BCD0 16%);
        color: var(--items-text);
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        min-width: 0;
        max-width: 100%;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.08);
    }

    .items-length-control .fa {
        color: var(--items-accent);
        font-size: 13px;
    }

    #item-card-length {
        width: 58px;
        min-height: 28px;
        border: 1px solid rgba(169, 188, 208, .34);
        border-radius: 7px;
        background: var(--items-panel);
        color: var(--items-text);
        outline: none;
        font-weight: 800;
    }

    .items-theme-toggle {
        min-height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 13px;
        border: 1px solid rgba(169, 188, 208, .3);
        border-radius: 8px;
        background: linear-gradient(135deg, #1B1B1E, #373F51);
        color: #ffffff;
        font-weight: 800;
        cursor: pointer;
        white-space: nowrap;
        min-width: 0;
        max-width: 100%;
        box-shadow: 0 10px 20px rgba(55, 63, 81, .22), inset 0 1px 0 rgba(255,255,255,.18);
        transition: transform .16s ease, border-color .16s ease, box-shadow .16s ease, background .16s ease;
    }

    .items-theme-toggle.theme-target-dark {
        background: linear-gradient(135deg, #1B1B1E, #373F51);
        color: #ffffff;
        border-color: rgba(169, 188, 208, .32);
    }

    .items-theme-toggle.theme-target-light {
        background: linear-gradient(135deg, #ffffff, #D8DBE2);
        color: #1B1B1E;
        border-color: rgba(55, 63, 81, .18);
        box-shadow: 0 10px 20px rgba(55, 63, 81, .14), inset 0 1px 0 rgba(255,255,255,.88);
    }

    .items-theme-toggle:hover {
        transform: translateY(-1px);
        border-color: rgba(255, 255, 255, .42);
        box-shadow: 0 14px 26px rgba(55, 63, 81, .28);
    }

    .items-theme-toggle .fa {
        color: currentColor;
    }

    .items-meta-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        color: var(--items-muted);
        font-size: 13px;
        line-height: 1.4;
        margin-bottom: 12px;
    }

    #items-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 190px), 1fr));
        gap: 10px;
    }

    .product-card {
        --card-white: var(--items-text);
        --card-black: var(--items-bg);
        --card-line: var(--items-border);
        --card-primary: var(--items-accent);
        --card-surface: var(--items-panel);
        --card-surface-soft: var(--items-panel-soft);
        --card-muted: var(--items-muted);
        --card-glow-1: rgba(88, 164, 176, .26);
        --card-glow-2: rgba(169, 188, 208, .18);
        --card-glow-3: rgba(126, 106, 145, .2);
        --card-border-glow: rgba(88, 164, 176, .44);
        min-width: 0;
        position: relative;
        isolation: isolate;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        gap: 8px;
        background-color: var(--card-surface);
        background-image:
            radial-gradient(at 88% 40%, var(--card-surface) 0px, transparent 70%),
            radial-gradient(at 49% 30%, var(--card-surface) 0px, transparent 72%),
            radial-gradient(at 12% 24%, var(--card-surface) 0px, transparent 72%),
            radial-gradient(at 0% 64%, var(--card-glow-1) 0px, transparent 70%),
            radial-gradient(at 42% 96%, var(--card-glow-2) 0px, transparent 68%),
            radial-gradient(at 100% 99%, var(--card-glow-3) 0px, transparent 70%);
        border: 1px solid var(--card-line);
        border-radius: 8px;
        padding: 10px;
        box-shadow: inset 0 -8px 18px rgba(255, 255, 255, .05), 0 10px 20px rgba(0, 0, 0, .18);
        contain: layout paint;
        transition: border-color .2s ease, box-shadow .2s ease, filter .2s ease, transform .2s ease;
    }

    #products-card-app.theme-light .product-card {
        --card-white: var(--items-text);
        --card-surface: #ffffff;
        --card-surface-soft: #eef2f6;
        --card-line: rgba(55, 63, 81, .16);
        --card-muted: #526076;
        --card-glow-1: rgba(88, 164, 176, .2);
        --card-glow-2: rgba(169, 188, 208, .18);
        --card-glow-3: rgba(55, 63, 81, .12);
        --card-border-glow: rgba(88, 164, 176, .42);
        background-color: #ffffff;
        background-image:
            radial-gradient(at 85% 16%, rgba(255,255,255,.98) 0px, transparent 55%),
            radial-gradient(at 0% 58%, var(--card-glow-1) 0px, transparent 64%),
            radial-gradient(at 50% 100%, var(--card-glow-2) 0px, transparent 62%),
            radial-gradient(at 100% 96%, var(--card-glow-3) 0px, transparent 62%),
            linear-gradient(180deg, #ffffff 0%, #f8fbfb 100%);
        box-shadow: 0 14px 30px rgba(27, 27, 30, .1), inset 0 -12px 22px rgba(88, 164, 176, .05);
    }

    #products-card-app.theme-light .product-card::before {
        background: linear-gradient(145deg, rgba(255,255,255,.95), rgba(169,188,208,.22) 38%, rgba(88,164,176,.5));
    }

    #products-card-app.theme-light .product-card::after {
        background: linear-gradient(90deg, transparent, rgba(88, 164, 176, .46), transparent);
    }

    #products-card-app.theme-light .product-card.is-selected {
        box-shadow: 0 0 0 2px rgba(88, 164, 176, .18), 0 18px 34px rgba(27, 27, 30, .12);
    }

    #products-card-app.theme-light .brand-logo-box,
    #products-card-app.theme-light .device-image-box,
    #products-card-app.theme-light .info-chip,
    #products-card-app.theme-light .signal-row,
    #products-card-app.theme-light .status-pill {
        background: rgba(255, 255, 255, .68);
        border-color: rgba(38, 50, 56, .12);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.85);
    }

    #products-card-app.theme-light .details-action {
        background: rgba(255, 255, 255, .7) !important;
        color: #373F51 !important;
        border-color: rgba(88, 164, 176, .28) !important;
    }

    .product-card::before {
        content: "";
        position: absolute;
        inset: 0;
        z-index: -1;
        padding: 1px;
        border-radius: inherit;
        background: linear-gradient(145deg, rgba(255,255,255,.72), rgba(255,255,255,.06) 36%, var(--card-border-glow));
        -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }

    .product-card::after {
        content: "";
        position: absolute;
        inset: auto 18px 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--card-border-glow), transparent);
        pointer-events: none;
    }

    .product-card.is-selected {
        border-color: var(--card-primary);
        box-shadow: inset 0 -12px 22px rgba(255, 255, 255, .08), 0 0 0 2px rgba(88, 164, 176, .2), 0 18px 34px rgba(0, 0, 0, .26);
    }

    .product-card.is-online {
        --card-glow-1: rgba(88, 164, 176, .38);
        --card-glow-2: rgba(169, 188, 208, .22);
        --card-border-glow: rgba(88, 164, 176, .72);
        border-color: rgba(88, 164, 176, .62);
        background-color: #373F51;
        background-image:
            linear-gradient(135deg, rgba(55, 63, 81, .96) 0%, rgba(88, 164, 176, .88) 52%, rgba(27, 27, 30, .94) 100%),
            radial-gradient(at 15% 18%, rgba(216, 219, 226, .24) 0px, transparent 58%),
            radial-gradient(at 92% 78%, rgba(169, 188, 208, .24) 0px, transparent 62%);
        box-shadow:
            inset 0 -12px 22px rgba(255, 255, 255, .08),
            0 0 0 1px rgba(88, 164, 176, .22),
            0 18px 34px rgba(0, 0, 0, .28),
            0 0 24px rgba(88, 164, 176, .16);
    }

    .product-card.is-online::after {
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(88, 164, 176, .92), transparent);
    }

    .product-card.is-online .device-image-box {
        border-color: rgba(169, 188, 208, .34);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.14), 0 10px 22px rgba(88, 164, 176, .08);
    }

    .product-card.is-online .brand-logo-box,
    .product-card.is-online .device-image-box,
    .product-card.is-online .info-chip,
    .product-card.is-online .signal-row {
        background: rgba(27, 27, 30, .26);
        border-color: rgba(216, 219, 226, .24);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.13);
    }

    .product-card:hover {
        transform: translateY(-2px);
        filter: saturate(1.04) brightness(1.02);
        border-color: var(--card-primary);
        box-shadow: inset 0 -12px 22px rgba(255, 255, 255, .08), 0 0 0 2px rgba(88, 164, 176, .18), 0 20px 36px rgba(0, 0, 0, .3);
    }

    .product-card:hover::after {
        height: 2px;
        opacity: 1;
    }

    #products-card-app.theme-light .product-card:hover {
        filter: saturate(1.04) brightness(1.01);
        border-color: #58A4B0;
        box-shadow: 0 0 0 2px rgba(88, 164, 176, .2), 0 20px 38px rgba(27, 27, 30, .16);
    }

    .product-card.is-online:hover {
        filter: saturate(1.08) brightness(1.04);
        border-color: rgba(88, 164, 176, .72);
        box-shadow:
            inset 0 -12px 22px rgba(255, 255, 255, .1),
            0 0 0 2px rgba(88, 164, 176, .22),
            0 22px 42px rgba(0, 0, 0, .32),
            0 0 28px rgba(88, 164, 176, .16);
    }

    #products-card-app.theme-light .product-card.is-online {
        --card-glow-1: rgba(88, 164, 176, .26);
        --card-glow-2: rgba(169, 188, 208, .2);
        --card-border-glow: rgba(88, 164, 176, .62);
        border-color: rgba(88, 164, 176, .5);
        background-color: #D8DBE2;
        background-image:
            linear-gradient(135deg, #D8DBE2 0%, #A9BCD0 48%, #ffffff 100%),
            radial-gradient(at 0% 58%, rgba(88, 164, 176, .26) 0px, transparent 64%),
            radial-gradient(at 86% 18%, rgba(55, 63, 81, .12) 0px, transparent 58%);
        box-shadow:
            0 0 0 1px rgba(88, 164, 176, .16),
            0 18px 34px rgba(27, 27, 30, .12),
            0 0 24px rgba(88, 164, 176, .08),
            inset 0 -12px 22px rgba(88, 164, 176, .06);
    }

    #products-card-app.theme-light .product-card.is-online::after {
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(88, 164, 176, .82), transparent);
    }

    #products-card-app.theme-light .product-card.is-online .device-image-box {
        border-color: rgba(88, 164, 176, .28);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 10px 22px rgba(88, 164, 176, .08);
    }

    #products-card-app.theme-light .product-card.is-online .brand-logo-box,
    #products-card-app.theme-light .product-card.is-online .device-image-box,
    #products-card-app.theme-light .product-card.is-online .info-chip,
    #products-card-app.theme-light .product-card.is-online .signal-row {
        background: rgba(255, 255, 255, .72);
        border-color: rgba(88, 164, 176, .24);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 8px 16px rgba(88, 164, 176, .06);
    }

    #products-card-app.theme-light .product-card.is-online:hover {
        filter: saturate(1.07) brightness(1.02);
        border-color: rgba(88, 164, 176, .62);
        box-shadow:
            0 0 0 2px rgba(88, 164, 176, .18),
            0 22px 42px rgba(27, 27, 30, .14),
            0 0 28px rgba(88, 164, 176, .1),
            inset 0 -12px 22px rgba(88, 164, 176, .07);
    }

    .product-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        min-height: 32px;
    }

    .brand-logo-box,
    .device-image-box {
        display: flex;
        align-items: center;
        justify-content: center;
        background: color-mix(in srgb, var(--card-surface-soft) 72%, transparent);
        border: 1px solid var(--card-line);
        border-radius: 12px;
        overflow: hidden;
        backdrop-filter: blur(4px);
    }

    .brand-logo-box {
        width: 56px;
        height: 32px;
        padding: 4px;
    }

    .brand-logo-box img {
        width: 100% !important;
        height: 100% !important;
        object-fit: contain;
        display: block;
    }

    .device-image-box {
        width: 100%;
        aspect-ratio: 5 / 3;
        padding: 8px;
    }

    .device-image-box img {
        width: 100% !important;
        height: 100% !important;
        max-width: 96px;
        object-fit: contain;
        display: block;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 26px;
        padding: 5px 8px;
        border-radius: 999px;
        background: color-mix(in srgb, var(--card-surface-soft) 72%, transparent);
        border: 1px solid var(--card-line);
        color: var(--card-white);
        font-size: 11px;
        font-weight: 800;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .status-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: var(--items-muted);
    }

    .status-pill.is-online .status-dot {
        background: #22c55e;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, .18), 0 0 14px rgba(34, 197, 94, .5);
    }

    .status-pill.is-online {
        border-color: rgba(34, 197, 94, .42);
        background: rgba(34, 197, 94, .14);
        color: #dfffe9;
    }

    #products-card-app.theme-light .status-pill.is-online {
        border-color: rgba(34, 197, 94, .34);
        background: rgba(34, 197, 94, .12);
        color: #14532d;
    }

    .status-pill.is-offline .status-dot {
        background: #f97316;
        box-shadow: 0 0 0 4px rgba(249, 115, 22, .16), 0 0 12px rgba(249, 115, 22, .38);
    }

    .status-pill.is-offline {
        border-color: rgba(249, 115, 22, .38);
        background: rgba(249, 115, 22, .12);
        color: #ffe7d2;
    }

    #products-card-app.theme-light .status-pill.is-offline {
        border-color: rgba(249, 115, 22, .32);
        background: rgba(249, 115, 22, .1);
        color: #9a3412;
    }

    .product-name {
        margin: 0 0 6px;
        color: var(--card-white);
        font-size: 15px;
        line-height: 1.25;
        font-weight: 800;
        letter-spacing: 0;
        overflow-wrap: anywhere;
    }

    .product-subtitle {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        color: var(--card-muted);
        font-size: 13px;
    }

    .info-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
        padding: 5px 8px;
        background: rgba(88, 164, 176, .28);
        border: 1px solid rgba(169, 188, 208, .62);
        border-radius: 999px;
        color: #ffffff;
        font-size: 12px;
        font-weight: 900;
        line-height: 1.15;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .2), 0 8px 16px rgba(0, 0, 0, .18);
        overflow-wrap: anywhere;
    }

    .info-chip .fa {
        color: #A9BCD0;
        font-size: 14px;
    }

    #products-card-app.theme-light .info-chip {
        background: #D8DBE2;
        border-color: rgba(88, 164, 176, .42);
        color: #1B1B1E;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .95), 0 8px 16px rgba(15, 23, 42, .08);
    }

    #products-card-app.theme-light .info-chip .fa {
        color: #58A4B0;
    }

    .signal-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 7px 9px;
        background: color-mix(in srgb, var(--card-surface-soft) 72%, transparent);
        border: 1px solid var(--card-line);
        border-radius: 12px;
        color: var(--card-muted);
        font-size: 12px;
    }

    .signal-value {
        color: var(--card-white);
        font-weight: 800;
        text-align: right;
        overflow-wrap: anywhere;
    }

    .signal-value span {
        color: inherit !important;
    }

    .signal-value .rssi-muted {
        color: var(--card-muted) !important;
        font-weight: 700;
    }

    .details-action {
        min-height: 40px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 9px 10px !important;
        border: 1px solid transparent !important;
        border-radius: 999px !important;
        font-size: 13px;
        font-weight: 800;
        line-height: 1.1;
        text-align: center;
        text-decoration: none !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .12);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
    }

    .details-action i {
        width: 22px;
        height: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, .2);
        color: inherit;
        flex: 0 0 22px;
    }

    .details-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(15, 23, 42, .16);
    }

    .details-action {
        background: rgba(255, 255, 255, .08) !important;
        color: #A9BCD0 !important;
        border-color: rgba(169, 188, 208, .26) !important;
        box-shadow: none;
        margin-top: auto;
        min-height: 34px;
        padding: 7px 10px !important;
        font-size: 12px;
    }

    .details-action:hover {
        color: #ffffff !important;
        border-color: var(--items-accent-strong) !important;
        background: linear-gradient(135deg, var(--items-accent-strong), #58A4B0) !important;
    }

    .details-action:hover i {
        background: rgba(255, 255, 255, .18);
    }

    #products-card-app.theme-light .details-action:hover {
        background: linear-gradient(135deg, #373F51, #58A4B0) !important;
        color: #ffffff !important;
        border-color: transparent !important;
        box-shadow: 0 10px 22px rgba(55, 63, 81, .24) !important;
    }

    #products-card-app.theme-light .details-action:hover i {
        background: rgba(255, 255, 255, .2);
        color: #ffffff;
    }

    .items-hidden-table {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        opacity: 0;
        pointer-events: none;
    }

    .items-empty-state {
        grid-column: 1 / -1;
        padding: 28px;
        text-align: center;
        color: var(--items-muted);
        background: var(--items-panel);
        border: 1px dashed var(--items-border);
        border-radius: 8px;
    }

    .items-card-pager {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 16px;
        color: var(--items-muted);
        font-size: 13px;
    }

    .items-pager-buttons {
        display: inline-flex;
        gap: 8px;
    }

    .items-page-btn {
        min-height: 36px;
        padding: 8px 12px;
        border: 1px solid var(--items-border);
        border-radius: 8px;
        background: var(--items-panel-soft);
        color: var(--items-text);
        font-weight: 800;
        cursor: pointer;
    }

    .items-page-btn:disabled {
        cursor: not-allowed;
        opacity: .48;
    }

    @media (max-width: 1200px) {
        #page-content {
            padding: 18px !important;
        }

        #page-content > .card {
            padding: 18px !important;
        }

        .items-toolbar {
            grid-template-columns: minmax(0, 1fr);
        }

        .items-filter-panel {
            grid-template-columns: auto repeat(2, minmax(0, 1fr));
        }

        .items-toolbar-actions {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            width: 100%;
        }
    }

    @media (min-width: 641px) and (max-width: 1100px) {
        .items-toolbar {
            grid-template-columns: minmax(150px, 1fr) minmax(220px, .9fr) auto;
            gap: 8px;
            padding: 10px;
        }

        #item-card-search {
            min-height: 42px;
            padding: 9px 10px 9px 38px;
            font-size: 13px;
        }

        .items-search-field .fa {
            left: 13px;
        }

        .items-filter-panel {
            grid-template-columns: 38px minmax(78px, 1fr) minmax(88px, 1fr);
            gap: 6px;
            padding: 6px;
        }

        .items-filter-title {
            width: 36px;
            min-height: 36px;
            justify-content: center;
            padding: 0;
            font-size: 0;
        }

        .items-filter-title .fa {
            font-size: 14px;
        }

        .items-status-filter,
        .items-category-filter {
            grid-template-columns: 24px minmax(0, 1fr);
            gap: 5px;
        }

        .items-filter-label {
            justify-content: center;
            min-width: 24px;
            font-size: 0;
        }

        .items-filter-label .fa {
            font-size: 14px;
        }

        .items-filter-select {
            min-height: 36px;
            padding: 7px 25px 7px 8px;
            background-position: right 10px top 15px, 0 0;
            font-size: 12px;
        }

        .items-toolbar-actions {
            grid-template-columns: minmax(72px, auto) 44px;
            gap: 7px;
            width: auto;
        }

        .items-length-control {
            min-height: 42px;
            gap: 6px;
            padding: 6px 8px;
            justify-content: center;
        }

        .items-length-control span {
            display: none;
        }

        .items-length-control .fa {
            display: inline-flex;
        }

        #item-card-length {
            width: 52px;
            min-height: 28px;
        }

        .items-theme-toggle {
            width: 44px;
            min-height: 42px;
            padding: 0;
            gap: 0;
        }

        .items-theme-toggle span {
            display: none;
        }
    }

    @media (min-width: 641px) and (max-width: 1100px) {
        #items-card-grid {
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 175px), 1fr));
            gap: 9px;
        }

        .product-card {
            padding: 9px;
            gap: 7px;
        }

        .product-card-top {
            min-height: 28px;
            gap: 6px;
        }

        .brand-logo-box {
            width: 50px;
            height: 28px;
            padding: 4px;
            border-radius: 8px;
        }

        .status-pill {
            min-height: 24px;
            padding: 4px 7px;
            gap: 5px;
            font-size: 10px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
        }

        .device-image-box {
            aspect-ratio: 4 / 3;
            padding: 7px;
            border-radius: 8px;
        }

        .device-image-box img {
            max-width: 82px;
        }

        .product-name {
            margin-bottom: 5px;
            font-size: 14px;
            line-height: 1.2;
        }

        .product-subtitle {
            gap: 4px;
            font-size: 11px;
        }

        .info-chip {
            padding: 4px 7px;
            gap: 5px;
            font-size: 11px;
        }

        .info-chip .fa {
            font-size: 12px;
        }

        .signal-row {
            gap: 6px;
            padding: 6px 8px;
            border-radius: 8px;
            font-size: 11px;
        }

        .details-action {
            min-height: 32px;
            padding: 6px 8px !important;
            font-size: 11px;
            gap: 5px;
        }

        .details-action i {
            width: 18px;
            height: 18px;
            flex-basis: 18px;
            font-size: 10px;
        }
    }

    @media (max-width: 640px) {
        .items-toolbar {
            grid-template-columns: 1fr;
        }

        .items-toolbar-actions {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            width: 100%;
        }
    }

    @media (max-width: 640px) {
        #products-card-app {
            padding: 12px;
        }

        .items-meta-row {
            flex-direction: column;
            align-items: stretch;
        }

        .items-toolbar {
            padding: 10px;
        }

        .items-filter-panel {
            grid-template-columns: 1fr;
        }

        .items-filter-title {
            justify-content: flex-start;
        }

        .items-status-filter,
        .items-category-filter {
            grid-template-columns: 72px minmax(0, 1fr);
        }

        .items-length-control,
        .items-theme-toggle {
            width: 100%;
        }

        .items-length-control {
            justify-content: space-between;
        }

        #items-card-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .product-card {
            display: flex;
            flex-direction: column;
            gap: 7px;
            padding: 9px;
            border-radius: 8px;
        }

        .product-card-top {
            min-height: 28px;
            gap: 6px;
        }

        .brand-logo-box {
            width: 48px;
            height: 27px;
            padding: 4px;
            border-radius: 8px;
        }

        .status-pill {
            min-height: 24px;
            padding: 4px 7px;
            gap: 5px;
            font-size: 10px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
        }

        .device-image-box {
            width: 100%;
            height: auto;
            aspect-ratio: 4 / 3;
            padding: 7px;
            border-radius: 8px;
        }

        .device-image-box img {
            max-width: 72px;
            max-height: 64px;
        }

        .product-card > div:nth-of-type(3) {
            min-width: 0;
        }

        .product-name {
            margin-bottom: 5px;
            font-size: 13px;
            line-height: 1.2;
        }

        .product-subtitle {
            gap: 5px;
            font-size: 11px;
        }

        .info-chip {
            padding: 4px 7px;
            max-width: 100%;
            font-size: 10px;
        }

        .signal-row {
            padding: 6px 7px;
            border-radius: 8px;
            font-size: 10px;
            gap: 5px;
        }

        .details-action {
            min-height: 32px;
            padding: 6px 7px !important;
            font-size: 11px;
            gap: 5px;
        }

        .details-action i {
            width: 19px;
            height: 19px;
            flex-basis: 19px;
            font-size: 11px;
        }

        .details-action {
            min-height: 32px;
        }

        .items-card-pager {
            align-items: stretch;
            flex-direction: column;
        }

        .items-pager-buttons {
            width: 100%;
        }

        .items-page-btn {
            flex: 1;
        }

    }

    @media (min-width: 421px) and (max-width: 640px) {
        .items-toolbar {
            grid-template-columns: minmax(110px, 1fr) minmax(180px, 1.2fr) auto;
            gap: 7px;
        }

        #item-card-search {
            min-height: 40px;
            padding: 8px 8px 8px 34px;
            font-size: 12px;
        }

        .items-search-field .fa {
            left: 12px;
        }

        .items-filter-panel {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 6px;
            padding: 5px;
        }

        .items-filter-title {
            display: none;
        }

        .items-status-filter,
        .items-category-filter {
            grid-template-columns: 22px minmax(0, 1fr);
            gap: 4px;
        }

        .items-filter-label {
            min-width: 22px;
            justify-content: center;
            font-size: 0;
        }

        .items-filter-label .fa {
            font-size: 13px;
        }

        .items-filter-select {
            min-height: 34px;
            padding: 6px 23px 6px 7px;
            background-position: right 9px top 14px, 0 0;
            font-size: 11px;
        }

        .items-toolbar-actions {
            grid-template-columns: 58px 38px;
            gap: 6px;
            width: auto;
        }

        .items-length-control {
            min-height: 40px;
            padding: 5px 6px;
            justify-content: center;
        }

        .items-length-control .fa,
        .items-length-control span {
            display: none;
        }

        #item-card-length {
            width: 46px;
            min-height: 28px;
            font-size: 12px;
        }

        .items-theme-toggle {
            width: 38px;
            min-height: 40px;
            padding: 0;
        }

        .items-theme-toggle span {
            display: none;
        }
    }

    @media (max-width: 420px) {
        #page-content {
            padding: 6px !important;
        }

        #page-content > .card {
            padding: 8px !important;
        }

        #products-card-app {
            padding: 8px;
        }

        #item-card-search {
            min-height: 38px;
            padding: 8px 9px 8px 34px;
            font-size: 12px;
        }

        .items-toolbar {
            grid-template-columns: 1fr;
            gap: 8px;
            padding: 8px;
        }

        .items-filter-panel {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 6px;
            padding: 6px;
        }

        .items-filter-title {
            grid-column: 1 / -1;
            min-height: 32px;
            justify-content: center;
            padding: 6px 8px;
            font-size: 12px;
        }

        .items-status-filter,
        .items-category-filter {
            grid-template-columns: 22px minmax(0, 1fr);
            gap: 4px;
        }

        .items-filter-label {
            min-width: 22px;
            justify-content: center;
            font-size: 0;
        }

        .items-filter-label .fa {
            font-size: 13px;
        }

        .items-filter-select {
            min-height: 34px;
            padding: 6px 22px 6px 7px;
            background-position: right 8px top 14px, 0 0;
            font-size: 11px;
        }

        .items-toolbar-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 6px;
            width: 100%;
        }

        .items-length-control {
            min-height: 38px;
            justify-content: center;
            gap: 6px;
            padding: 5px 7px;
        }

        .items-length-control span {
            display: none;
        }

        #item-card-length {
            width: 48px;
            min-height: 28px;
            font-size: 12px;
        }

        .items-theme-toggle {
            width: 100%;
            min-height: 38px;
            padding: 0 8px;
            gap: 6px;
            font-size: 12px;
        }

        .items-search-field .fa {
            left: 12px;
        }

        .items-meta-row {
            display: none;
        }

        .product-card {
            gap: 7px;
            padding: 8px;
        }

        .device-image-box {
            width: 100%;
            height: auto;
            padding: 6px;
        }

        .device-image-box img {
            max-width: 58px;
            max-height: 54px;
        }

        .product-name {
            font-size: 12px;
        }
    }

    @media (max-width: 340px) {
        .items-theme-toggle span {
            display: none;
        }

        .items-theme-toggle {
            justify-content: center;
        }

        #items-card-grid {
            grid-template-columns: 1fr;
        }

        .product-card {
            display: grid;
            grid-template-columns: 64px minmax(0, 1fr);
            grid-template-areas:
                "top top"
                "image title"
                "image signal"
                "details details";
            gap: 8px;
            padding: 10px;
        }

        .product-card-top {
            grid-area: top;
        }

        .device-image-box {
            grid-area: image;
            width: 64px;
            height: 64px;
            aspect-ratio: auto;
        }

        .product-card > div:nth-of-type(3) {
            grid-area: title;
        }

        .signal-row {
            grid-area: signal;
        }

        .details-action {
            grid-area: details;
        }
    }
</style>

<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <h1>Products</h1>
        <!-- Uncomment this block if you need the Add Product modal -->
        <!--
        <div class="title-button-group">
            <?= modal_anchor(get_uri("items/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> Add Product", ["class" => "btn btn-default", "title" => "Add Product"]); ?>
        </div>
        -->
        <div id="products-card-app" class="theme-dark">
            <div class="items-toolbar">
                <div class="items-search-wrap">
                    <div class="items-search-field">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <input id="item-card-search" type="search" placeholder="Search product, device type, model, brand..." autocomplete="off">
                    </div>
                </div>
                <div class="items-filter-panel">
                    <div class="items-filter-title">
                        <i class="fa fa-filter" aria-hidden="true"></i>
                        Filters
                    </div>
                    <label class="items-status-filter">
                        <span class="items-filter-label" title="Status">
                            <i class="fa fa-signal" aria-hidden="true"></i>
                            <span class="items-filter-label-text">Status</span>
                        </span>
                        <select id="items-status-filter" class="items-filter-select">
                            <option value="all">All</option>
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                        </select>
                    </label>
                    <label class="items-category-filter">
                        <span class="items-filter-label" title="Category">
                            <i class="fa fa-tags" aria-hidden="true"></i>
                            <span class="items-filter-label-text">Category</span>
                        </span>
                        <select id="items-category-filter" class="items-filter-select">
                            <option value="all">All</option>
                        </select>
                    </label>
                </div>
                <div class="items-toolbar-actions">
                    <label class="items-length-control">
                        <i class="fa fa-list-ol" aria-hidden="true" title="Entries"></i>
                        <span>Show</span>
                        <select id="item-card-length">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="-1">All</option>
                        </select>
                        <span>entries</span>
                    </label>
                    <button type="button" id="items-theme-toggle" class="items-theme-toggle theme-target-dark" aria-pressed="true" title="Dark mode">
                        <i class="fa fa-moon-o" aria-hidden="true"></i>
                        <span>Dark mode</span>
                    </button>
                </div>
            </div>
            <div class="items-meta-row">
                <span id="items-result-count">Loading products...</span>
                <span>Showing product name, device type, model, status and RSSI.</span>
            </div>
            <div id="items-card-grid"></div>
            <div class="items-card-pager">
                <span id="items-page-info">Page 1</span>
                <div class="items-pager-buttons">
                    <button type="button" id="items-prev-page" class="items-page-btn">Previous</button>
                    <button type="button" id="items-next-page" class="items-page-btn">Next</button>
                </div>
            </div>
        </div>
        <div class="items-hidden-table">
            <table id="item-table" class="display" cellspacing="0" width="100%"></table>
        </div>
    </div>
</div>

<script type="text/javascript">
   $(document).ready(function () {
        const searchQuery = <?= json_encode($searchQuery, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const $cardApp = $("#products-card-app");
        const $cardGrid = $("#items-card-grid");
        const $searchInput = $("#item-card-search");
        const $themeToggle = $("#items-theme-toggle");
        const $resultCount = $("#items-result-count");
        const $lengthSelect = $("#item-card-length");
        const $pageInfo = $("#items-page-info");
        const $prevPage = $("#items-prev-page");
        const $nextPage = $("#items-next-page");
        const $statusFilter = $("#items-status-filter");
        const $categoryFilter = $("#items-category-filter");
        let activeStatusFilter = "all";
        let activeCategoryFilter = "all";

        function stripHtml(value) {
            return $("<div>").html(value || "").text().trim();
        }

        function escapeHtml(value) {
            return $("<div>").text(value || "").html();
        }

        function normalizeImage(html, type) {
            const $image = $("<div>").html(html || "").find("img").first();
            if ($image.length) {
                $image.removeAttr("style width height");
                $image.attr("alt", type === "brand" ? "Brand logo" : "Device image");
                return $("<div>").append($image).html();
            }

            const icon = type === "brand" ? "fa-tag" : "fa-microchip";
            return '<i class="fa ' + icon + '" aria-hidden="true"></i>';
        }

        function normalizeAction(html, iconClass, label, iconOnly) {
            const $wrap = $("<div>").html(html || "");
            const $action = $wrap.find("a, button").first();

            if (!$action.length) {
                return "";
            }

            $action.removeAttr("style");
            const actionClass = "items-action-" + label.toLowerCase().replace(/\s+/g, "-");
            $action.addClass("items-card-action " + actionClass);
            $action.html('<i class="fa ' + iconClass + '" aria-hidden="true"></i>' + (iconOnly ? '<span class="sr-only">' + label + '</span>' : '<span>' + label + '</span>'));

            return $("<div>").append($action).html();
        }

        function normalizeDetailsAction(html) {
            const $wrap = $("<div>").html(html || "");
            const $action = $wrap.find("a, button").first();

            if (!$action.length) {
                return "";
            }

            $action.removeAttr("style");
            $action.addClass("details-action");
            $action.attr("data-modal-lg", "1");
            $action.attr("data-title", "User & Product Details");
            $action.attr("title", "User & Product Details");
            $action.html('<i class="fa fa-info-circle" aria-hidden="true"></i><span>More Details</span>');

            return $("<div>").append($action).html();
        }

        function getOnlineClass(status) {
            const cleanStatus = String(status || "").trim().toLowerCase();
            if (cleanStatus.indexOf("offline") !== -1 || cleanStatus === "false" || cleanStatus === "0" || cleanStatus === "no") {
                return "is-offline";
            }

            if (cleanStatus.indexOf("online") !== -1 || cleanStatus === "true" || cleanStatus === "1" || cleanStatus === "yes") {
                return "is-online";
            }

            return "";
        }

        function getStatusLabel(status) {
            const onlineClass = getOnlineClass(status);
            if (onlineClass === "is-online") {
                return "Online";
            }

            if (onlineClass === "is-offline") {
                return "Offline";
            }

            return stripHtml(status) || "Unknown";
        }

        function formatRssi(value) {
            const raw = value || "";
            const cleanValue = stripHtml(raw);

            if (!cleanValue || cleanValue.toLowerCase() === "null" || cleanValue.toLowerCase() === "undefined") {
                return '<span class="rssi-muted">N/A</span>';
            }

            return raw;
        }

        function normalizeFilterValue(value) {
            return stripHtml(value).toLowerCase();
        }

        function matchesStatusFilter(rowStatus) {
            if (activeStatusFilter === "all") {
                return true;
            }

            return getOnlineClass(rowStatus) === "is-" + activeStatusFilter;
        }

        function buildCategoryFilters(table) {
            const categories = [];
            const seen = {};

            table.rows().data().toArray().forEach(function (row) {
                const category = stripHtml(row[6]) || "Uncategorized";
                const key = category.toLowerCase();

                if (!seen[key]) {
                    seen[key] = true;
                    categories.push(category);
                }
            });

            categories.sort(function (a, b) {
                return a.localeCompare(b);
            });

            const options = [
                '<option value="all">All</option>'
            ].concat(categories.map(function (category) {
                return '<option value="' + escapeHtml(category) + '">' + escapeHtml(category) + '</option>';
            }));

            $categoryFilter.html(options.join(""));
        }

        function renderCards(table) {
            const rows = table.rows({ search: "applied", page: "current" }).data().toArray();
            const pageInfo = table.page.info();

            const cards = rows.map(function (row, index) {
                const brandLogo = normalizeImage(row[5], "brand");
                const deviceImage = normalizeImage(row[9], "device");
                const productName = escapeHtml(stripHtml(row[8]) || stripHtml(row[6]) || "Unnamed Product");
                const deviceType = escapeHtml(stripHtml(row[6]) || "N/A");
                const modelNumber = escapeHtml(stripHtml(row[7]) || "N/A");
                const rawInternetStatus = stripHtml(row[12]) || "Unknown";
                const internetStatus = escapeHtml(getStatusLabel(rawInternetStatus));
                const onlineClass = getOnlineClass(rawInternetStatus);
                const rssi = formatRssi(row[14]);
                const detailsAction = normalizeDetailsAction(row[21]);

                return [
                    '<article class="product-card ' + onlineClass + '" data-card-index="' + index + '">',
                        '<div class="product-card-top">',
                            '<div class="brand-logo-box">' + brandLogo + '</div>',
                            '<div class="status-pill ' + onlineClass + '"><span class="status-dot"></span><span>' + internetStatus + '</span></div>',
                        '</div>',
                        '<div class="device-image-box">' + deviceImage + '</div>',
                        '<div>',
                            '<h3 class="product-name">' + productName + '</h3>',
                            '<div class="product-subtitle">',
                                '<span class="info-chip"><i class="fa fa-plug" aria-hidden="true"></i>' + deviceType + '</span>',
                                '<span class="info-chip"><i class="fa fa-barcode" aria-hidden="true"></i>' + modelNumber + '</span>',
                            '</div>',
                        '</div>',
                        '<div class="signal-row">',
                            '<span><i class="fa fa-wifi" aria-hidden="true"></i> RSSI</span>',
                            '<span class="signal-value">' + rssi + '</span>',
                        '</div>',
                        detailsAction,
                    '</article>'
                ].join("");
            });

            $cardGrid.html(cards.length ? cards.join("") : '<div class="items-empty-state">No products found.</div>');
            $resultCount.text(pageInfo.recordsDisplay + " product" + (pageInfo.recordsDisplay === 1 ? "" : "s") + " found");
            $pageInfo.text(pageInfo.recordsDisplay
                ? "Showing " + (pageInfo.start + 1) + " to " + pageInfo.end + " of " + pageInfo.recordsDisplay
                : "No products to show");
            $prevPage.prop("disabled", pageInfo.page <= 0);
            $nextPage.prop("disabled", pageInfo.page >= pageInfo.pages - 1 || pageInfo.pages === 0);

        }

        function setCardTheme(theme) {
            const isLight = theme === "light";
            $cardApp.toggleClass("theme-light", isLight);
            $themeToggle.attr("aria-pressed", isLight ? "false" : "true");
            $themeToggle
                .toggleClass("theme-target-dark", isLight)
                .toggleClass("theme-target-light", !isLight)
                .attr("title", isLight ? "Switch to dark mode" : "Switch to light mode")
                .html(isLight
                    ? '<i class="fa fa-moon-o" aria-hidden="true"></i><span>Dark mode</span>'
                    : '<i class="fa fa-sun-o" aria-hidden="true"></i><span>Light mode</span>');
            localStorage.setItem("itemsCardTheme", isLight ? "light" : "dark");
        }

        setCardTheme(localStorage.getItem("itemsCardTheme") || "dark");

        $.fn.dataTable.ext.search.push(function (settings, rowData) {
            if (settings.nTable.id !== "item-table") {
                return true;
            }

            const deviceType = normalizeFilterValue(rowData[6]);
            const status = stripHtml(rowData[12]);
            const categoryMatches = activeCategoryFilter === "all" || deviceType === activeCategoryFilter;

            return categoryMatches && matchesStatusFilter(status);
        });

        const table = $("#item-table").DataTable({
            ajax: "<?= echo_uri('items/list_data') ?>",
            dom: "t",
            paging: true,
            pageLength: 25,
            lengthChange: false,
            info: false,
            ordering: true,
            order: [[0, 'desc']],
            columns: [
                { title: "User" },
                { title: "Auto Test" },
                { title: "mode" },
                { title: "Node ID" },
                { title: "Brand" },
                { title: "Brand logo" },
                { title: "Device Type" },
                { title: "model_number" },
                { title: "Product Name" },
                { title: "Device" },
                { title: "User ID" },
                { title: "Phone" },
                { title: "Internet" },
                { title: "BaudRate" },
                { title: "RSSI" },
                { title: "FW_Version" },
                { title: "power" },
                { title: "latitude" },
                { title: "longitude" },
                { title: "signin_latitude" },
                { title: "signin_longitude" },
                { title: "Product Info" },
                { title: "APIs Info" },
                { title: "Control" },
                { title: "Chat" },
                { title: "Fw_Update" }
            ],
            drawCallback: function () {
                renderCards(this.api());
            },
            initComplete: function () {
                buildCategoryFilters(this.api());
                renderCards(this.api());
            }
        });

        if (searchQuery) {
            $searchInput.val(searchQuery);
            table.search(searchQuery).draw();
        }

        $searchInput.on("input", function () {
            table.search(this.value).draw();
        });

        $lengthSelect.on("change", function () {
            const length = parseInt(this.value, 10);
            table.page.len(length).draw();
        });

        $statusFilter.on("change", function () {
            activeStatusFilter = this.value || "all";
            table.draw();
        });

        $categoryFilter.on("change", function () {
            activeCategoryFilter = normalizeFilterValue(this.value || "all") || "all";
            table.draw();
        });

        $prevPage.on("click", function () {
            table.page("previous").draw("page");
        });

        $nextPage.on("click", function () {
            table.page("next").draw("page");
        });

        $themeToggle.on("click", function () {
            const nextTheme = $cardApp.hasClass("theme-light") ? "dark" : "light";
            setCardTheme(nextTheme);
        });
    });
</script>
