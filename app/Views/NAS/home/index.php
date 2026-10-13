<?php

/*
|--------------------------------------------------------------------------
| NAS Home View - Phase 1
|--------------------------------------------------------------------------
|
| Standalone NAS home page mounted inside the existing Efactor CI4 app.
| All routes and assets are generated from CodeIgniter helpers.
|
*/

$basePath = isset($basePath) && $basePath !== ''
    ? rtrim($basePath, '/')
    : rtrim(site_url('NAS'), '/');

$apiBase = isset($apiBase) && $apiBase !== ''
    ? rtrim($apiBase, '/')
    : rtrim(site_url('NAS/api/v1'), '/');

$assetsBase = isset($assetsBase) && $assetsBase !== ''
    ? rtrim($assetsBase, '/')
    : rtrim(base_url('assets/NAS'), '/');

$userData = $userData ?? [
    'user_id' => null,
    'username' => 'User',
    'email' => '',
    'initial' => 'U',
];

$username = (string) ($userData['username'] ?? 'User');
$userEmail = (string) ($userData['email'] ?? '');
$userInitial = (string) ($userData['initial'] ?? strtoupper(substr($username ?: 'U', 0, 1)));
$userId = $userData['user_id'] ?? null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>EFACTOR</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="<?= esc($assetsBase) ?>/css/style.css"
    >

    <script>
    /* Apply the saved visual theme before first paint to avoid theme flash. */
    (function () {
        try {
            var savedTheme = localStorage.getItem('efactor_nas_home_theme');
            document.documentElement.setAttribute(
                'data-nas-theme',
                savedTheme === 'dark' ? 'dark' : 'light'
            );
        } catch (error) {
            document.documentElement.setAttribute('data-nas-theme', 'light');
        }
    })();
    </script>

    <style>
:root {
    --nas-bg: #f5f7fb;
    --nas-surface: #ffffff;
    --nas-surface-soft: #fafbfe;
    --nas-surface-hover: #f7f8fc;

    --nas-text: #202431;
    --nas-text-strong: #161a24;
    --nas-muted: #6f7787;
    --nas-subtle: #9ba3b2;

    --nas-border: #e4e8ef;
    --nas-border-strong: #d5dbe5;

    --nas-primary: #6257d9;
    --nas-primary-dark: #4f46b8;
    --nas-primary-soft: #f0efff;

    --nas-blue: #3979d6;
    --nas-blue-soft: #edf5ff;

    --nas-amber: #ad7616;
    --nas-amber-soft: #fff6e4;

    --nas-green: #168269;
    --nas-green-soft: #eaf8f3;

    --nas-red: #c5525c;
    --nas-red-soft: #fff0f2;

    --nas-radius-lg: 20px;
    --nas-radius-md: 15px;
    --nas-radius-sm: 10px;

    --nas-shadow-xs: 0 1px 2px rgba(20, 28, 45, .025);
    --nas-shadow-sm: 0 5px 16px rgba(20, 28, 45, .045);
    --nas-shadow-hover: 0 13px 30px rgba(32, 40, 60, .09);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    min-height: 100%;
}

body.nas-home-page {
    min-height: 100vh;
    font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    background: var(--nas-bg);
    color: var(--nas-text);
    font-size: 14px;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

body.nas-home-page::before,
body.nas-home-page::after {
    display: none;
    content: none;
}

/* ============================================================
   HEADER
   ============================================================ */
.home-header {
    position: sticky;
    top: 0;
    z-index: 1000;
    height: 68px;
    padding: 0 clamp(18px, 3vw, 44px);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    background: rgba(255, 255, 255, .96);
    border-bottom: 1px solid var(--nas-border);
    box-shadow: 0 1px 5px rgba(19, 27, 43, .025);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
}

.home-header .logo {
    min-width: 0;
    display: inline-flex;
    align-items: center;
    gap: 11px;
    color: var(--nas-text);
    text-decoration: none;
}

.home-header .logo-mark {
    width: 42px;
    height: 42px;
    flex: 0 0 auto;
    display: grid;
    place-items: center;
    border: 1px solid #e8ebf1;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 4px 12px rgba(28, 36, 52, .045);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
}

.home-header .logo:hover .logo-mark {
    transform: translateY(-1px);
    border-color: #dfe3ec;
    box-shadow: 0 7px 16px rgba(28, 36, 52, .075);
}

.home-header .logo img {
    max-width: 31px;
    max-height: 31px;
    width: auto;
    height: auto;
    object-fit: contain;
}

.home-header .brand-copy {
    min-width: 0;
    display: flex;
    flex-direction: column;
    line-height: 1.08;
}

.home-header .brand-name {
    color: var(--nas-text-strong);
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -.2px;
}

.home-header .brand-subtitle {
    margin-top: 3px;
    color: var(--nas-muted);
    font-size: 10.5px;
    font-weight: 550;
    letter-spacing: .03em;
}

.home-header .user-info {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
}

.home-header .user-chip {
    height: 40px;
    padding: 4px 11px 4px 4px;
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid var(--nas-border);
    border-radius: 999px;
    background: #fff;
    transition: background .18s ease, border-color .18s ease, box-shadow .18s ease;
}

.home-header .user-chip:hover {
    background: #fbfbfe;
    border-color: var(--nas-border-strong);
    box-shadow: 0 4px 12px rgba(26, 34, 50, .04);
}

.home-header .user-avatar {
    width: 31px;
    height: 31px;
    flex: 0 0 auto;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: linear-gradient(135deg, #6257d9, #7a70e3);
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    box-shadow: 0 4px 10px rgba(98, 87, 217, .20);
}

.home-header .user-name {
    max-width: 165px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: var(--nas-text);
    font-size: 12.5px;
    font-weight: 600;
}

.home-header .logout-btn {
    min-height: 38px;
    padding: 0 13px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid var(--nas-border);
    border-radius: 9px;
    background: #fff;
    color: #525b6a;
    cursor: pointer;
    text-decoration: none;
    font-size: 12.5px;
    font-weight: 600;
    transition: transform .18s ease, background .18s ease, color .18s ease, border-color .18s ease, box-shadow .18s ease;
}

.home-header .logout-btn:hover {
    transform: translateY(-1px);
    background: var(--nas-surface-soft);
    border-color: var(--nas-border-strong);
    color: var(--nas-primary-dark);
    box-shadow: 0 5px 12px rgba(25, 34, 50, .05);
}

.home-header .logout-btn.logout-action:hover {
    color: var(--nas-red);
    background: var(--nas-red-soft);
    border-color: #f0cfd2;
}

/* ============================================================
   MAIN / HERO
   ============================================================ */
.home-main {
    width: min(1280px, calc(100% - 40px));
    margin: 0 auto;
    padding: 28px 0 50px;
}

.welcome-section {
    position: relative;
    min-height: 186px;
    overflow: hidden;
    padding: clamp(26px, 4vw, 40px) clamp(26px, 4vw, 42px);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 28px;
    border: 1px solid var(--nas-border);
    border-radius: var(--nas-radius-lg);
    background: #fff;
    box-shadow: var(--nas-shadow-sm);
}

/* Decorative shapes stay inside the white hero only; page background stays one solid color. */
.welcome-section::before {
    content: "";
    position: absolute;
    width: 265px;
    height: 265px;
    right: -105px;
    top: -130px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(98,87,217,.11), rgba(98,87,217,.025) 52%, transparent 72%);
    pointer-events: none;
}

.welcome-section::after {
    content: "";
    position: absolute;
    width: 180px;
    height: 180px;
    right: 130px;
    bottom: -145px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(22,130,105,.07), transparent 72%);
    pointer-events: none;
}

.welcome-copy {
    position: relative;
    z-index: 2;
    max-width: 720px;
}

.welcome-eyebrow {
    width: fit-content;
    margin-bottom: 11px;
    padding: 6px 10px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border: 1px solid #e2e0ff;
    border-radius: 999px;
    background: #f5f3ff;
    color: var(--nas-primary-dark);
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: .035em;
    text-transform: none;
}

.welcome-eyebrow i {
    font-size: 10px;
}

.welcome-section h1 {
    max-width: 700px;
    color: var(--nas-text-strong);
    font-size: clamp(27px, 3.2vw, 39px);
    font-weight: 690;
    line-height: 1.16;
    letter-spacing: -.9px;
}

.welcome-section h1 span {
    color: var(--nas-primary);
}

.welcome-section p {
    max-width: 650px;
    margin-top: 11px;
    color: var(--nas-muted);
    font-size: 14px;
    line-height: 1.65;
}

.hero-visual {
    position: relative;
    z-index: 2;
    width: 124px;
    height: 124px;
    flex: 0 0 auto;
    display: grid;
    place-items: center;
    border: 1px solid #e6e4ff;
    border-radius: 32px;
    background: linear-gradient(145deg, #faf9ff, #f0efff);
    box-shadow: 0 12px 28px rgba(89, 78, 186, .10);
    transform: none;
}

.hero-visual::before {
    content: "";
    position: absolute;
    width: 76px;
    height: 76px;
    border-radius: 23px;
    background: linear-gradient(145deg, #665bd9, #7b70e6);
    box-shadow: 0 12px 23px rgba(98, 87, 217, .22);
    animation: nasHeroPulse 4.4s ease-in-out infinite;
}

.hero-visual i {
    position: relative;
    z-index: 2;
    color: #fff;
    font-size: 29px;
    animation: nasCloudFloat 4.4s ease-in-out infinite;
}

@keyframes nasCloudFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-4px); }
}

@keyframes nasHeroPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.035); }
}

/* ============================================================
   SECTION HEADERS
   ============================================================ */
.home-section {
    margin-top: 26px;
    animation: nasSectionIn .42s ease both;
}

.home-section:nth-of-type(2) { animation-delay: .04s; }
.home-section:nth-of-type(3) { animation-delay: .08s; }
.home-section:nth-of-type(4) { animation-delay: .12s; }

@keyframes nasSectionIn {
    from { opacity: 0; transform: translateY(7px); }
    to { opacity: 1; transform: translateY(0); }
}

.section-heading {
    margin-bottom: 12px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 14px;
}

.section-heading h2 {
    color: var(--nas-text-strong);
    font-size: 17px;
    font-weight: 690;
    letter-spacing: -.2px;
}

.section-heading p {
    margin-top: 3px;
    color: var(--nas-muted);
    font-size: 11.5px;
}

/* ============================================================
   STAT CARDS
   ============================================================ */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 13px;
}

.stat-card {
    position: relative;
    min-height: 128px;
    overflow: hidden;
    padding: 17px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border: 1px solid var(--nas-border);
    border-radius: var(--nas-radius-md);
    background: var(--nas-surface);
    color: var(--nas-text);
    box-shadow: var(--nas-shadow-xs);
    text-decoration: none;
    transform: translateZ(0);
    transition: transform .22s cubic-bezier(.2,.8,.2,1), box-shadow .22s ease, border-color .22s ease, background .22s ease;
}

.stat-card::before {
    content: "";
    position: absolute;
    left: 16px;
    right: 16px;
    bottom: 0;
    height: 2px;
    border-radius: 999px 999px 0 0;
    opacity: 0;
    transform: scaleX(.35);
    transition: opacity .22s ease, transform .22s ease;
}

.stat-card.files::before { background: var(--nas-blue); }
.stat-card.folders::before { background: var(--nas-amber); }
.stat-card.shared::before { background: var(--nas-green); }
.stat-card.trash::before { background: var(--nas-red); }

.stat-card::after {
    content: "";
    position: absolute;
    width: 95px;
    height: 95px;
    right: -46px;
    top: -48px;
    border-radius: 50%;
    opacity: .36;
    pointer-events: none;
}

.stat-card.files::after { background: var(--nas-blue-soft); }
.stat-card.folders::after { background: var(--nas-amber-soft); }
.stat-card.shared::after { background: var(--nas-green-soft); }
.stat-card.trash::after { background: var(--nas-red-soft); }

.stat-card:hover {
    transform: translateY(-3px);
    border-color: var(--nas-border-strong);
    background: #fff;
    box-shadow: var(--nas-shadow-hover);
}

.stat-card:hover::before {
    opacity: 1;
    transform: scaleX(1);
}

.stat-card .stat-top {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.stat-card .stat-icon {
    width: 40px;
    height: 40px;
    display: grid;
    place-items: center;
    border-radius: 11px;
    font-size: 16px;
    transition: transform .22s ease, box-shadow .22s ease;
}

.stat-card.files .stat-icon { background: var(--nas-blue-soft); color: var(--nas-blue); }
.stat-card.folders .stat-icon { background: var(--nas-amber-soft); color: var(--nas-amber); }
.stat-card.shared .stat-icon { background: var(--nas-green-soft); color: var(--nas-green); }
.stat-card.trash .stat-icon { background: var(--nas-red-soft); color: var(--nas-red); }

.stat-card:hover .stat-icon {
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(29, 37, 54, .055);
}

.stat-card .stat-arrow {
    position: relative;
    z-index: 1;
    color: #b1b8c4;
    font-size: 11px;
    transition: transform .22s ease, color .22s ease;
}

.stat-card:hover .stat-arrow {
    transform: translate(3px, -2px);
    color: var(--nas-primary);
}

.stat-card .stat-number {
    position: relative;
    z-index: 1;
    margin-top: 15px;
    color: var(--nas-text-strong);
    font-size: 29px;
    font-weight: 700;
    line-height: 1;
    letter-spacing: -.6px;
}

.stat-card .stat-label {
    position: relative;
    z-index: 1;
    margin-top: 5px;
    color: var(--nas-muted);
    font-size: 12px;
    font-weight: 600;
}

/* ============================================================
   QUICK ACCESS
   ============================================================ */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 13px;
}

.quick-action-btn {
    position: relative;
    min-height: 106px;
    padding: 16px 38px 16px 16px;
    display: flex;
    align-items: center;
    gap: 13px;
    border: 1px solid var(--nas-border);
    border-radius: var(--nas-radius-md);
    background: var(--nas-surface);
    color: var(--nas-text);
    box-shadow: var(--nas-shadow-xs);
    text-align: left;
    text-decoration: none;
    overflow: hidden;
    transition: transform .22s cubic-bezier(.2,.8,.2,1), box-shadow .22s ease, border-color .22s ease, background .22s ease;
}

.quick-action-btn::before {
    content: "";
    position: absolute;
    inset: 0;
    opacity: 0;
    background: linear-gradient(105deg, transparent, rgba(98,87,217,.035));
    transition: opacity .2s ease;
    pointer-events: none;
}

.quick-action-btn::after {
    content: "›";
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #adb4c0;
    font-size: 21px;
    font-weight: 300;
    transition: right .2s ease, color .2s ease;
}

.quick-action-btn:hover {
    transform: translateY(-2px);
    border-color: #d8dbea;
    background: #fff;
    box-shadow: var(--nas-shadow-hover);
}

.quick-action-btn:hover::before { opacity: 1; }
.quick-action-btn:hover::after { right: 13px; color: var(--nas-primary); }

.quick-action-btn .action-icon {
    position: relative;
    z-index: 1;
    width: 44px;
    height: 44px;
    flex: 0 0 auto;
    display: grid;
    place-items: center;
    border-radius: 11px;
    background: var(--nas-primary-soft);
    color: var(--nas-primary);
    font-size: 17px;
    transition: transform .22s ease, box-shadow .22s ease;
}

.quick-action-btn:nth-child(2) .action-icon { background: var(--nas-green-soft); color: var(--nas-green); }
.quick-action-btn:nth-child(3) .action-icon { background: var(--nas-blue-soft); color: var(--nas-blue); }
.quick-action-btn:nth-child(4) .action-icon { background: var(--nas-red-soft); color: var(--nas-red); }

.quick-action-btn:hover .action-icon {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 6px 14px rgba(29, 37, 54, .055);
}

.quick-action-btn .action-copy {
    position: relative;
    z-index: 1;
    min-width: 0;
}

.quick-action-btn .label {
    color: var(--nas-text-strong);
    font-size: 13.5px;
    font-weight: 670;
}

.quick-action-btn .desc {
    margin-top: 4px;
    color: var(--nas-muted);
    font-size: 10.8px;
    line-height: 1.45;
}

/* ============================================================
   STORAGE + RECENT
   ============================================================ */
.overview-grid {
    display: grid;
    grid-template-columns: minmax(285px, .82fr) minmax(0, 1.65fr);
    gap: 14px;
    align-items: start;
}

.storage-section,
.recent-section {
    margin: 0;
    border: 1px solid var(--nas-border);
    border-radius: var(--nas-radius-md);
    background: var(--nas-surface);
    box-shadow: var(--nas-shadow-xs);
    transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
}

.storage-section:hover,
.recent-section:hover {
    border-color: var(--nas-border-strong);
    box-shadow: var(--nas-shadow-sm);
}

.storage-section {
    min-height: 0;
    height: auto;
    padding: 18px;
    display: flex;
    flex-direction: column;
    align-self: start;
}

.storage-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.storage-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.storage-title-icon {
    width: 40px;
    height: 40px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 11px;
    background: var(--nas-primary-soft);
    color: var(--nas-primary);
    font-size: 15px;
    transition: transform .22s ease, box-shadow .22s ease;
}

.storage-section:hover .storage-title-icon {
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(29, 37, 54, .055);
}

.storage-title h3 {
    color: var(--nas-text-strong);
    font-size: 13.5px;
    font-weight: 680;
}

.storage-title p {
    margin-top: 2px;
    color: var(--nas-muted);
    font-size: 10.5px;
}

.storage-live {
    padding: 5px 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    background: var(--nas-green-soft);
    color: var(--nas-green);
    font-size: 9.5px;
    font-weight: 700;
}

.storage-live::before {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--nas-green);
    animation: nasLivePulse 2.2s ease-out infinite;
}

@keyframes nasLivePulse {
    0% { box-shadow: 0 0 0 0 rgba(22,130,105,.24); }
    70%, 100% { box-shadow: 0 0 0 7px rgba(22,130,105,0); }
}

.storage-section .storage-header {
    margin-top: 23px;
    margin-bottom: 8px;
    padding-top: 0;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 12px;
}

.storage-section .storage-header > span:first-child {
    color: var(--nas-muted);
    font-size: 10px;
    font-weight: 650;
    text-transform: none;
    letter-spacing: .02em;
}

.storage-section .storage-header > span:last-child {
    color: var(--nas-muted);
    font-size: 10.5px;
    text-align: right;
}

.storage-section .storage-header strong {
    color: var(--nas-text-strong);
    font-weight: 670;
}

.storage-section .storage-header strong#storageUsed {
    font-size: 16px;
}

.storage-bar {
    width: 100%;
    height: 8px;
    overflow: hidden;
    border-radius: 999px;
    background: #eceff4;
}

.storage-bar .fill {
    height: 100%;
    border-radius: inherit;
    background: linear-gradient(90deg, #6257d9, #766ce1);
    box-shadow: none;
    transition: width .65s cubic-bezier(.2,.8,.2,1);
}

.storage-bar .fill.warning {
    background: linear-gradient(90deg, #d79a31, #ce5862);
}

.storage-note {
    margin-top: 11px;
    color: var(--nas-subtle);
    font-size: 10px;
    line-height: 1.5;
}

.recent-section {
    overflow: hidden;
}

.recent-section .section-header {
    min-height: 61px;
    padding: 13px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    border-bottom: 1px solid var(--nas-border);
    background: #fff;
}

.recent-section .section-header h3 {
    display: flex;
    align-items: center;
    gap: 9px;
    color: var(--nas-text-strong);
    font-size: 13.5px;
    font-weight: 680;
}

.recent-section .section-header h3 i {
    width: 30px;
    height: 30px;
    display: grid;
    place-items: center;
    border-radius: 8px;
    background: var(--nas-primary-soft);
    color: var(--nas-primary);
    font-size: 12px;
}

.recent-section .section-header a {
    padding: 6px 9px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border-radius: 7px;
    color: var(--nas-primary-dark);
    background: transparent;
    text-decoration: none;
    font-size: 10.5px;
    font-weight: 650;
    transition: background .18s ease, transform .18s ease;
}

.recent-section .section-header a:hover {
    transform: translateY(-1px);
    background: var(--nas-primary-soft);
}

.recent-section .section-header a i {
    font-size: 9px;
    transition: transform .18s ease;
}

.recent-section .section-header a:hover i {
    transform: translateX(2px);
}

#recentItems {
    min-height: 218px;
}

.recent-item {
    position: relative;
    min-height: 58px;
    padding: 9px 42px 9px 15px;
    display: flex;
    align-items: center;
    border-bottom: 1px solid #eef1f5;
    cursor: pointer;
    transition: background .18s ease, padding-left .18s ease;
}

.recent-item::after {
    content: "›";
    position: absolute;
    right: 17px;
    top: 50%;
    transform: translateY(-50%);
    color: #b4bac4;
    font-size: 19px;
    transition: right .18s ease, color .18s ease;
}

.recent-item:last-child {
    border-bottom: none;
}

.recent-item:hover {
    padding-left: 18px;
    background: var(--nas-surface-hover);
}

.recent-item:hover::after {
    right: 14px;
    color: var(--nas-primary);
}

.recent-item .icon {
    width: 34px;
    height: 34px;
    margin-right: 11px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 8px;
    font-size: 12.5px;
    background: var(--nas-blue-soft);
    color: var(--nas-blue);
    transition: transform .18s ease;
}

.recent-item .icon.folder {
    background: var(--nas-amber-soft);
    color: var(--nas-amber);
}

.recent-item .icon.file {
    background: var(--nas-blue-soft);
    color: var(--nas-blue);
}

.recent-item:hover .icon {
    transform: translateY(-1px);
}

.recent-item .info {
    min-width: 0;
    flex: 1;
}

.recent-item .info .name {
    overflow: hidden;
    color: var(--nas-text-strong);
    font-size: 12px;
    font-weight: 590;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.recent-item .info .meta {
    margin-top: 3px;
    color: var(--nas-subtle);
    font-size: 9.8px;
}

.empty-state,
.loading {
    min-height: 218px;
    padding: 30px 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--nas-muted);
    text-align: center;
    font-size: 11px;
    font-weight: 550;
}

.empty-state i,
.loading i {
    width: 43px;
    height: 43px;
    margin-bottom: 10px;
    display: grid;
    place-items: center;
    border-radius: 12px;
    background: #f1f3f7;
    color: #9ba2ae;
    font-size: 16px;
    opacity: 1;
}

.empty-state p {
    color: var(--nas-muted);
    font-size: 11px;
}

/* ============================================================
   MICRO INTERACTIONS / ACCESSIBILITY
   ============================================================ */
.stat-card:active,
.quick-action-btn:active,
.home-header .logout-btn:active {
    transform: scale(.985);
}

.stat-card:focus-visible,
.quick-action-btn:focus-visible,
.logout-btn:focus-visible,
.recent-section .section-header a:focus-visible,
.home-header .logo:focus-visible {
    outline: 2px solid rgba(98, 87, 217, .55);
    outline-offset: 3px;
}

/* Neutralize the old visual-feedback layers if a stale cached script adds them. */
.nas-pointer-light,
.nas-ux-edge,
.nas-ux-sheen,
.nas-ux-ripple {
    display: none !important;
}

/* ============================================================
   LIGHT / DARK THEME SWITCH
   Visual-only feature. No NAS routes, APIs or data logic change.
   ============================================================ */
.theme-toggle-btn {
    width: 38px;
    height: 38px;
    flex: 0 0 auto;
    display: inline-grid;
    place-items: center;
    border: 1px solid var(--nas-border);
    border-radius: 10px;
    background: var(--nas-surface);
    color: var(--nas-primary-dark);
    cursor: pointer;
    font-size: 14px;
    box-shadow: var(--nas-shadow-xs);
    transition:
        transform .18s ease,
        background .22s ease,
        color .22s ease,
        border-color .22s ease,
        box-shadow .22s ease;
}

.theme-toggle-btn:hover {
    transform: translateY(-1px);
    background: var(--nas-primary-soft);
    border-color: rgba(98, 87, 217, .25);
    color: var(--nas-primary);
    box-shadow: 0 6px 15px rgba(35, 43, 62, .07);
}

.theme-toggle-btn:active {
    transform: scale(.96);
}

.theme-toggle-btn i {
    transition: transform .28s cubic-bezier(.2,.8,.2,1), opacity .18s ease;
}

.theme-toggle-btn:hover i {
    transform: rotate(-10deg) scale(1.06);
}

.theme-toggle-btn:focus-visible {
    outline: 2px solid rgba(98, 87, 217, .55);
    outline-offset: 3px;
}

/* Smooth only the surfaces affected by a user-triggered theme switch. */
body.nas-home-page,
.home-header,
.home-header .logo-mark,
.home-header .user-chip,
.home-header .logout-btn,
.theme-toggle-btn,
.welcome-section,
.welcome-eyebrow,
.hero-visual,
.stat-card,
.quick-action-btn,
.storage-section,
.recent-section,
.recent-section .section-header,
.recent-item,
.empty-state i,
.loading i,
.storage-bar {
    transition-property: background-color, background, color, border-color, box-shadow, transform;
    transition-duration: .24s;
    transition-timing-function: ease;
}

/* Premium dark palette: one background, elevated surfaces, restrained accents. */
html[data-nas-theme="dark"] {
    color-scheme: dark;

    --nas-bg: #10141b;
    --nas-surface: #171c25;
    --nas-surface-soft: #1c222d;
    --nas-surface-hover: #1e2531;

    --nas-text: #e8ebf2;
    --nas-text-strong: #f5f7fb;
    --nas-muted: #a8b0bf;
    --nas-subtle: #7f899a;

    --nas-border: #282f3b;
    --nas-border-strong: #384252;

    --nas-primary: #9289ff;
    --nas-primary-dark: #aaa3ff;
    --nas-primary-soft: #272442;

    --nas-blue: #74a9f3;
    --nas-blue-soft: #192a40;

    --nas-amber: #e4b55b;
    --nas-amber-soft: #332a1a;

    --nas-green: #61bea6;
    --nas-green-soft: #18322c;

    --nas-red: #e5828a;
    --nas-red-soft: #392126;

    --nas-shadow-xs: 0 1px 2px rgba(0, 0, 0, .18);
    --nas-shadow-sm: 0 8px 24px rgba(0, 0, 0, .20);
    --nas-shadow-hover: 0 16px 36px rgba(0, 0, 0, .28);
}

html[data-nas-theme="dark"] body.nas-home-page {
    background: var(--nas-bg);
    color: var(--nas-text);
}

html[data-nas-theme="dark"] .home-header {
    background: rgba(18, 23, 31, .96);
    border-bottom-color: var(--nas-border);
    box-shadow: 0 1px 8px rgba(0, 0, 0, .20);
}

html[data-nas-theme="dark"] .home-header .logo-mark,
html[data-nas-theme="dark"] .home-header .user-chip,
html[data-nas-theme="dark"] .home-header .logout-btn,
html[data-nas-theme="dark"] .theme-toggle-btn {
    background: var(--nas-surface);
    border-color: var(--nas-border);
}

html[data-nas-theme="dark"] .home-header .logo:hover .logo-mark,
html[data-nas-theme="dark"] .home-header .user-chip:hover,
html[data-nas-theme="dark"] .home-header .logout-btn:hover {
    background: var(--nas-surface-hover);
    border-color: var(--nas-border-strong);
}

html[data-nas-theme="dark"] .home-header .logout-btn {
    color: var(--nas-muted);
}

html[data-nas-theme="dark"] .home-header .logout-btn:hover {
    color: var(--nas-primary-dark);
}

html[data-nas-theme="dark"] .home-header .logout-btn.logout-action:hover {
    color: var(--nas-red);
    background: var(--nas-red-soft);
    border-color: rgba(229, 130, 138, .30);
}

html[data-nas-theme="dark"] .theme-toggle-btn {
    color: #f2c866;
}

html[data-nas-theme="dark"] .theme-toggle-btn:hover {
    background: #292740;
    border-color: rgba(170, 163, 255, .34);
    color: #ffd878;
    box-shadow: 0 7px 18px rgba(0, 0, 0, .24);
}

html[data-nas-theme="dark"] .welcome-section {
    background: #171c25;
    border-color: var(--nas-border);
    box-shadow: var(--nas-shadow-sm);
}

html[data-nas-theme="dark"] .welcome-section::before {
    background: radial-gradient(circle, rgba(146,137,255,.14), rgba(146,137,255,.035) 52%, transparent 72%);
}

html[data-nas-theme="dark"] .welcome-section::after {
    background: radial-gradient(circle, rgba(97,190,166,.09), transparent 72%);
}

html[data-nas-theme="dark"] .welcome-eyebrow {
    background: #24213d;
    color: #aaa3ff;
    border-color: #39345d;
}

html[data-nas-theme="dark"] .hero-visual {
    background: linear-gradient(145deg, #211f37, #1c1b31);
    border-color: #343054;
    box-shadow: 0 14px 30px rgba(0, 0, 0, .22);
}

html[data-nas-theme="dark"] .hero-visual::before {
    background: linear-gradient(145deg, #7369e8, #8c82f4);
    box-shadow: 0 12px 26px rgba(89, 78, 196, .30);
}

html[data-nas-theme="dark"] .stat-card,
html[data-nas-theme="dark"] .quick-action-btn,
html[data-nas-theme="dark"] .storage-section,
html[data-nas-theme="dark"] .recent-section {
    background: var(--nas-surface);
    border-color: var(--nas-border);
}

html[data-nas-theme="dark"] .stat-card:hover,
html[data-nas-theme="dark"] .quick-action-btn:hover,
html[data-nas-theme="dark"] .storage-section:hover,
html[data-nas-theme="dark"] .recent-section:hover {
    background: var(--nas-surface-hover);
    border-color: var(--nas-border-strong);
}

html[data-nas-theme="dark"] .quick-action-btn::before {
    background: linear-gradient(105deg, transparent, rgba(146,137,255,.055));
}

html[data-nas-theme="dark"] .stat-card .stat-arrow,
html[data-nas-theme="dark"] .quick-action-btn::after,
html[data-nas-theme="dark"] .recent-item::after {
    color: #687385;
}

html[data-nas-theme="dark"] .recent-section .section-header {
    background: var(--nas-surface);
    border-bottom-color: var(--nas-border);
}

html[data-nas-theme="dark"] .recent-item {
    border-bottom-color: #232a35;
}

html[data-nas-theme="dark"] .recent-item:hover {
    background: var(--nas-surface-hover);
}

html[data-nas-theme="dark"] .storage-bar {
    background: #2a313d;
}

html[data-nas-theme="dark"] .storage-bar .fill {
    background: linear-gradient(90deg, #766ceb, #948aff);
}

html[data-nas-theme="dark"] .storage-bar .fill.warning {
    background: linear-gradient(90deg, #d79a31, #d7646d);
}

html[data-nas-theme="dark"] .empty-state i,
html[data-nas-theme="dark"] .loading i {
    background: #202733;
    color: #7e899a;
}

html[data-nas-theme="dark"] .stat-card:hover .stat-icon,
html[data-nas-theme="dark"] .quick-action-btn:hover .action-icon,
html[data-nas-theme="dark"] .storage-section:hover .storage-title-icon {
    box-shadow: 0 7px 16px rgba(0, 0, 0, .20);
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 1040px) {
    .stats-grid,
    .quick-actions {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .overview-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 780px) {
    .home-header {
        height: 62px;
        min-height: 62px;
        padding: 0 14px;
    }

    .home-header .brand-subtitle,
    .home-header .user-name {
        display: none;
    }

    .home-header .user-chip {
        padding-right: 4px;
        border: none;
        background: transparent;
    }

    .home-header .logout-btn {
        width: 37px;
        padding: 0;
    }

    .theme-toggle-btn {
        width: 37px;
        height: 37px;
    }

    .home-header .logout-btn span {
        display: none;
    }

    .home-main {
        width: min(100% - 24px, 1280px);
        padding-top: 18px;
    }

    .welcome-section {
        min-height: 165px;
        padding: 26px 24px;
        border-radius: 17px;
    }

    .welcome-section h1 {
        font-size: clamp(27px, 8vw, 36px);
    }

    .hero-visual {
        width: 104px;
        height: 104px;
        border-radius: 27px;
    }

    .hero-visual::before {
        width: 65px;
        height: 65px;
        border-radius: 19px;
    }

    .hero-visual i {
        font-size: 25px;
    }
}

@media (max-width: 600px) {
    .home-header .brand-copy {
        display: none;
    }

    .home-header .logo-mark {
        width: 38px;
        height: 38px;
        border-radius: 10px;
    }

    .home-header .user-info {
        gap: 6px;
    }

    .welcome-section {
        display: block;
    }

    .hero-visual {
        display: none;
    }

    .section-heading {
        align-items: flex-start;
    }

    .stats-grid,
    .quick-actions {
        grid-template-columns: 1fr;
    }

    .stat-card {
        min-height: 112px;
    }

    .quick-action-btn {
        min-height: 88px;
    }

    .storage-section .storage-header {
        align-items: flex-start;
        flex-direction: column;
        gap: 4px;
    }

    .storage-section .storage-header > span:last-child {
        text-align: left;
    }
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: .001ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .001ms !important;
        scroll-behavior: auto !important;
    }
}


/* ============================================================
   LOW STORAGE WARNING
   Trigger: occupied storage percentage >= 70%.
   Visual-only warning; storage values still come from /dashboard.
   ============================================================ */

body.nas-low-storage-open {
    overflow: hidden;
}

.nas-low-storage-overlay {
    position: fixed;
    inset: 0;
    z-index: 5000;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 22px;

    background: rgba(20, 25, 36, .34);

    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);

    opacity: 0;
    visibility: hidden;
    pointer-events: none;

    transition:
        opacity .22s ease,
        visibility .22s ease;
}

.nas-low-storage-overlay.active {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.nas-low-storage-dialog {
    position: relative;

    width: min(440px, 100%);

    padding: 24px;

    overflow: hidden;

    border: 1px solid var(--nas-border);
    border-radius: 20px;

    background: var(--nas-surface);

    box-shadow:
        0 28px 80px rgba(23, 30, 44, .20),
        0 8px 24px rgba(23, 30, 44, .08);

    color: var(--nas-text);

    transform: translateY(12px) scale(.985);

    transition:
        transform .24s cubic-bezier(.2,.8,.2,1);
}

.nas-low-storage-overlay.active .nas-low-storage-dialog {
    transform: translateY(0) scale(1);
}

.nas-low-storage-dialog::before {
    content: "";

    position: absolute;

    width: 180px;
    height: 180px;

    right: -72px;
    top: -92px;

    border-radius: 50%;

    background:
        radial-gradient(
            circle,
            rgba(173, 118, 22, .14),
            rgba(173, 118, 22, .035) 53%,
            transparent 72%
        );

    pointer-events: none;
}

.nas-low-storage-top {
    position: relative;
    z-index: 2;

    display: flex;
    align-items: flex-start;
    gap: 14px;
}

.nas-low-storage-icon {
    width: 48px;
    height: 48px;

    flex: 0 0 auto;

    display: grid;
    place-items: center;

    border: 1px solid rgba(173, 118, 22, .16);
    border-radius: 14px;

    background: var(--nas-amber-soft);
    color: var(--nas-amber);

    font-size: 19px;
}

.nas-low-storage-copy {
    min-width: 0;
    flex: 1;
}

.nas-low-storage-kicker {
    color: var(--nas-amber);

    font-size: 10px;
    font-weight: 750;

    letter-spacing: .055em;
    text-transform: uppercase;
}

.nas-low-storage-title {
    margin-top: 4px;

    color: var(--nas-text-strong);

    font-size: 20px;
    font-weight: 730;

    line-height: 1.25;
    letter-spacing: -.3px;
}

.nas-low-storage-message {
    margin-top: 7px;

    color: var(--nas-muted);

    font-size: 12.5px;
    line-height: 1.6;
}

.nas-low-storage-message strong {
    color: var(--nas-text-strong);
    font-weight: 720;
}

.nas-low-storage-close {
    width: 34px;
    height: 34px;

    flex: 0 0 auto;

    display: grid;
    place-items: center;

    border: 1px solid var(--nas-border);
    border-radius: 9px;

    background: var(--nas-surface-soft);
    color: var(--nas-muted);

    cursor: pointer;

    transition:
        transform .16s ease,
        background .16s ease,
        color .16s ease,
        border-color .16s ease;
}

.nas-low-storage-close:hover {
    transform: scale(1.03);

    color: var(--nas-red);
    background: var(--nas-red-soft);
    border-color: rgba(197, 82, 92, .22);
}

.nas-low-storage-meter {
    position: relative;
    z-index: 2;

    margin-top: 20px;
    padding: 14px;

    border: 1px solid var(--nas-border);
    border-radius: 13px;

    background: var(--nas-surface-soft);
}

.nas-low-storage-numbers {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;

    gap: 14px;
}

.nas-low-storage-remaining-label,
.nas-low-storage-limit-label {
    color: var(--nas-subtle);

    font-size: 9.5px;
    font-weight: 650;
}

.nas-low-storage-remaining {
    margin-top: 3px;

    color: var(--nas-amber);

    font-size: 19px;
    font-weight: 760;

    letter-spacing: -.35px;
}

.nas-low-storage-limit {
    margin-top: 3px;

    color: var(--nas-text);

    font-size: 12px;
    font-weight: 650;

    text-align: right;
}


.nas-low-storage-stat {
    min-width: 0;
}

.nas-low-storage-stat-right {
    text-align: right;
}

.nas-low-storage-occupied {
    color: var(--nas-red);
}

.nas-low-storage-percentage {
    margin-top: 3px;

    color: var(--nas-muted);

    font-size: 10px;
    font-weight: 650;
}

@media (max-width: 520px) {
    .nas-low-storage-numbers {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));

        gap: 12px;

        align-items: start;
    }

    .nas-low-storage-stat-right {
        grid-column: 1 / -1;

        text-align: left;
    }

    .nas-low-storage-limit {
        text-align: left;
    }
}

.nas-low-storage-progress {
    width: 100%;
    height: 7px;

    margin-top: 12px;

    overflow: hidden;

    border-radius: 999px;

    background: var(--nas-border);
}

.nas-low-storage-progress-fill {
    width: 0;
    height: 100%;

    border-radius: inherit;

    background:
        linear-gradient(
            90deg,
            #d49a33,
            #cf5c62
        );

    transition:
        width .55s cubic-bezier(.2,.8,.2,1);
}

.nas-low-storage-actions {
    position: relative;
    z-index: 2;

    margin-top: 18px;

    display: flex;
    justify-content: flex-end;
}

.nas-low-storage-dismiss {
    min-height: 39px;

    padding: 0 15px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 7px;

    border: 1px solid var(--nas-primary);
    border-radius: 10px;

    background: var(--nas-primary);
    color: #ffffff;

    cursor: pointer;

    font-size: 12px;
    font-weight: 700;

    transition:
        transform .18s ease,
        background .18s ease,
        box-shadow .18s ease;
}

.nas-low-storage-dismiss:hover {
    transform: translateY(-1px);

    background: var(--nas-primary-dark);

    box-shadow:
        0 7px 17px
        rgba(98, 87, 217, .18);
}

/* Dark theme */
html[data-nas-theme="dark"] .nas-low-storage-overlay {
    background: rgba(4, 7, 12, .58);
}

html[data-nas-theme="dark"] .nas-low-storage-dialog {
    background: var(--nas-surface);
    border-color: var(--nas-border);

    box-shadow:
        0 30px 90px rgba(0, 0, 0, .48),
        0 10px 26px rgba(0, 0, 0, .24);
}

html[data-nas-theme="dark"] .nas-low-storage-dialog::before {
    background:
        radial-gradient(
            circle,
            rgba(228, 181, 91, .15),
            rgba(228, 181, 91, .035) 54%,
            transparent 72%
        );
}

html[data-nas-theme="dark"] .nas-low-storage-icon {
    border-color: rgba(228, 181, 91, .21);
}

html[data-nas-theme="dark"] .nas-low-storage-close {
    background: var(--nas-surface-soft);
}

html[data-nas-theme="dark"] .nas-low-storage-meter {
    background: var(--nas-surface-soft);
}

@media (max-width: 520px) {
    .nas-low-storage-overlay {
        align-items: flex-end;
        padding: 10px;
    }

    .nas-low-storage-dialog {
        width: 100%;

        padding: 20px;

        border-radius: 18px;
    }

    .nas-low-storage-title {
        font-size: 18px;
    }

    .nas-low-storage-message {
        font-size: 12px;
    }

    .nas-low-storage-actions {
        display: block;
    }

    .nas-low-storage-dismiss {
        width: 100%;
        min-height: 42px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .nas-low-storage-overlay,
    .nas-low-storage-dialog,
    .nas-low-storage-progress-fill,
    .nas-low-storage-dismiss,
    .nas-low-storage-close {
        transition: none !important;
    }
}



/* ============================================================
   GET MORE STORAGE CTA
   Additive UI. Existing storage API logic remains unchanged.
   ============================================================ */

.storage-footer {
    margin-top: 12px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 12px;
}

.storage-footer .storage-note {
    margin-top: 0;

    flex: 1;
}

.storage-upgrade-btn {
    min-height: 35px;

    padding: 0 11px;

    flex: 0 0 auto;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 7px;

    border:
        1px solid
        color-mix(
            in srgb,
            var(--nas-primary) 25%,
            var(--nas-border)
        );

    border-radius: 9px;

    background:
        var(--nas-primary-soft);

    color:
        var(--nas-primary-dark);

    text-decoration: none;

    font-size: 10.5px;
    font-weight: 720;

    white-space: nowrap;

    transition:
        transform .18s ease,
        background .18s ease,
        border-color .18s ease,
        box-shadow .18s ease;
}

.storage-upgrade-btn:hover {
    transform: translateY(-1px);

    border-color:
        color-mix(
            in srgb,
            var(--nas-primary) 40%,
            var(--nas-border)
        );

    background:
        color-mix(
            in srgb,
            var(--nas-primary) 13%,
            var(--nas-surface)
        );

    box-shadow:
        0 6px 16px
        rgba(98,87,217,.11);
}

.storage-upgrade-btn i {
    font-size: 10px;
}

@media (max-width: 600px) {
    .storage-footer {
        align-items: stretch;
        flex-direction: column;
    }

    .storage-upgrade-btn {
        width: 100%;
        min-height: 39px;
    }
}

</style>

</head>

<body class="nas-home-page">

<header class="home-header">

    <a href="<?= esc($basePath) ?>/home" class="logo">
        <span class="logo-mark">
            <img
                src="<?= esc($assetsBase) ?>/images/efactor.png"
                alt="eFactor"
            >
        </span>

        <span class="brand-copy">
            <span class="brand-name">eFactor NAS</span>
            <span class="brand-subtitle">Secure Drive</span>
        </span>
    </a>

    <div class="user-info">
        <button
            type="button"
            class="theme-toggle-btn"
            id="themeToggleBtn"
            aria-label="Switch to dark theme"
            aria-pressed="false"
            title="Switch to dark theme"
        >
            <i class="fas fa-moon" id="themeToggleIcon" aria-hidden="true"></i>
        </button>

        <div class="user-chip">
            <div class="user-avatar">
                <?= esc($userInitial) ?>
            </div>

            <span class="user-name">
                <?= esc($username) ?>
            </span>
        </div>

        <a
            href="<?= site_url('dashboard') ?>"
            class="logout-btn"
            title="Back to Efactor Dashboard"
        >
            <i class="fas fa-arrow-left"></i>
            <span>Back</span>
        </a>

        <a
            href="<?= esc($basePath) ?>/logout"
            onclick="return confirm('Are you sure you want to logout?');"
            class="logout-btn logout-action"
            title="Logout"
        >
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>

</header>

<main class="home-main">

    <section class="welcome-section">
        <div class="welcome-copy">
            <div class="welcome-eyebrow">
                <i class="fas fa-shield-alt"></i>
                Secure workspace
            </div>

            <h1>
                Welcome back,
                <span><?= esc($username) ?></span>
            </h1>

            <p>
                Everything you need is in one place. Access your files, folders,
                shared content and storage overview from your eFactor NAS workspace.
            </p>
        </div>

        <div class="hero-visual" aria-hidden="true">
            <i class="fas fa-cloud"></i>
        </div>
    </section>

    <section class="home-section">
        <div class="section-heading">
            <div>
                <h2>Workspace overview</h2>
                <p>A quick summary of your NAS content.</p>
            </div>
        </div>

        <div class="stats-grid" id="statsGrid">

            <a
                href="<?= esc($basePath) ?>/dashboard?view=documents"
                class="stat-card files"
            >
                <div class="stat-top">
                    <div class="stat-icon">
                        <i class="fas fa-file"></i>
                    </div>
                    <i class="fas fa-arrow-up-right-from-square stat-arrow"></i>
                </div>
                <div>
                    <div class="stat-number" id="statFiles">0</div>
                    <div class="stat-label">Files</div>
                </div>
            </a>

            <a
                href="<?= esc($basePath) ?>/dashboard?view=projects"
                class="stat-card folders"
            >
                <div class="stat-top">
                    <div class="stat-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    <i class="fas fa-arrow-up-right-from-square stat-arrow"></i>
                </div>
                <div>
                    <div class="stat-number" id="statFolders">0</div>
                    <div class="stat-label">Folders</div>
                </div>
            </a>

            <a
                href="<?= esc($basePath) ?>/dashboard?view=shared"
                class="stat-card shared"
            >
                <div class="stat-top">
                    <div class="stat-icon">
                        <i class="fas fa-share-nodes"></i>
                    </div>
                    <i class="fas fa-arrow-up-right-from-square stat-arrow"></i>
                </div>
                <div>
                    <div class="stat-number" id="statShared">0</div>
                    <div class="stat-label">Shared Items</div>
                </div>
            </a>

            <a
                href="<?= esc($basePath) ?>/dashboard?view=trash"
                class="stat-card trash"
            >
                <div class="stat-top">
                    <div class="stat-icon">
                        <i class="fas fa-trash-can"></i>
                    </div>
                    <i class="fas fa-arrow-up-right-from-square stat-arrow"></i>
                </div>
                <div>
                    <div class="stat-number" id="statTrash">0</div>
                    <div class="stat-label">In Trash</div>
                </div>
            </a>

        </div>
    </section>

    <section class="home-section">
        <div class="section-heading">
            <div>
                <h2>Quick access</h2>
                <p>Open the areas you use most.</p>
            </div>
        </div>

        <div class="quick-actions">

            <a
                href="<?= esc($basePath) ?>/dashboard"
                class="quick-action-btn"
            >
                <div class="action-icon">
                    <i class="fas fa-th-large"></i>
                </div>
                <div class="action-copy">
                    <div class="label">Dashboard</div>
                    <div class="desc">Open your complete file manager workspace.</div>
                </div>
            </a>

            <a
                href="<?= esc($basePath) ?>/dashboard"
                class="quick-action-btn"
            >
                <div class="action-icon">
                    <i class="fas fa-cloud-arrow-up"></i>
                </div>
                <div class="action-copy">
                    <div class="label">Upload Files</div>
                    <div class="desc">Go to the dashboard to add new files.</div>
                </div>
            </a>

            <a
                href="<?= esc($basePath) ?>/dashboard?view=shared"
                class="quick-action-btn"
            >
                <div class="action-icon">
                    <i class="fas fa-user-group"></i>
                </div>
                <div class="action-copy">
                    <div class="label">Shared with Me</div>
                    <div class="desc">Review files and folders shared with you.</div>
                </div>
            </a>

            <a
                href="<?= esc($basePath) ?>/dashboard?view=trash"
                class="quick-action-btn"
            >
                <div class="action-icon">
                    <i class="fas fa-trash"></i>
                </div>
                <div class="action-copy">
                    <div class="label">Trash</div>
                    <div class="desc">Restore or manage deleted NAS content.</div>
                </div>
            </a>

        </div>
    </section>

    <section class="home-section overview-grid">

        <div class="storage-section">
            <div class="storage-title-row">
                <div class="storage-title">
                    <div class="storage-title-icon">
                        <i class="fas fa-hard-drive"></i>
                    </div>
                    <div>
                        <h3>Storage</h3>
                        <p>Your current NAS capacity</p>
                    </div>
                </div>

                <span class="storage-live">Live</span>
            </div>

            <div class="storage-header">
                <span>Storage used</span>

                <span>
                    <strong id="storageUsed">0 B</strong>
                    of
                    <strong id="storageLimit">10 GB</strong>
                </span>
            </div>

            <div class="storage-bar">
                <div
                    class="fill"
                    id="storageFill"
                    style="width:0%"
                ></div>
            </div>

            <div class="storage-footer">

                <p class="storage-note">
                    Usage is loaded from your existing NAS dashboard API.
                </p>

                <a
                    href="<?= esc($basePath) ?>/storage-plans"
                    class="storage-upgrade-btn"
                    title="Get more NAS storage"
                >
                    <i class="fas fa-circle-plus"></i>
                    Get more storage
                </a>

            </div>
        </div>

        <div class="recent-section">

            <div class="section-header">
                <h3>
                    <i class="fas fa-clock-rotate-left"></i>
                    Recent Items
                </h3>

                <a href="<?= esc($basePath) ?>/dashboard">
                    View All
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div id="recentItems">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    Loading...
                </div>
            </div>

        </div>

    </section>

</main>

<!-- =========================================================
     LOW STORAGE WARNING
     Uses live storage values returned by /NAS/api/v1/dashboard.
     ========================================================= -->
<div
    class="nas-low-storage-overlay"
    id="nasLowStorageOverlay"
    aria-hidden="true"
>
    <div
        class="nas-low-storage-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="nasLowStorageTitle"
        aria-describedby="nasLowStorageMessage"
    >
        <div class="nas-low-storage-top">

            <div class="nas-low-storage-icon" aria-hidden="true">
                <i class="fas fa-hard-drive"></i>
            </div>

            <div class="nas-low-storage-copy">
                <div class="nas-low-storage-kicker">
                    Storage alert
                </div>

                <h2
                    class="nas-low-storage-title"
                    id="nasLowStorageTitle"
                >
                    Your NAS storage is running low
                </h2>

                <p
                    class="nas-low-storage-message"
                    id="nasLowStorageMessage"
                >
                    You have limited storage remaining.
                </p>
            </div>

            <button
                type="button"
                class="nas-low-storage-close"
                id="nasLowStorageClose"
                aria-label="Close storage warning"
                title="Close"
            >
                <i class="fas fa-xmark"></i>
            </button>

        </div>

        <div class="nas-low-storage-meter">

            <div class="nas-low-storage-numbers">

                <div class="nas-low-storage-stat">
                    <div class="nas-low-storage-remaining-label">
                        Occupied
                    </div>

                    <div
                        class="nas-low-storage-remaining nas-low-storage-occupied"
                        id="nasLowStorageOccupied"
                    >
                        0 GB
                    </div>
                </div>

                <div class="nas-low-storage-stat">
                    <div class="nas-low-storage-remaining-label">
                        Remaining
                    </div>

                    <div
                        class="nas-low-storage-remaining"
                        id="nasLowStorageRemaining"
                    >
                        0 GB
                    </div>
                </div>

                <div class="nas-low-storage-stat nas-low-storage-stat-right">
                    <div class="nas-low-storage-limit-label">
                        Total capacity
                    </div>

                    <div
                        class="nas-low-storage-limit"
                        id="nasLowStorageUsage"
                    >
                        0 GB total
                    </div>

                    <div
                        class="nas-low-storage-percentage"
                        id="nasLowStoragePercentage"
                    >
                        0% used
                    </div>
                </div>

            </div>

            <div class="nas-low-storage-progress">
                <div
                    class="nas-low-storage-progress-fill"
                    id="nasLowStorageProgressFill"
                ></div>
            </div>

        </div>

        <div class="nas-low-storage-actions">
            <button
                type="button"
                class="nas-low-storage-dismiss"
                id="nasLowStorageDismiss"
            >
                <i class="fas fa-check"></i>
                Got it
            </button>
        </div>
    </div>
</div>


<script>
/* ============================================================
   HOME THEME TOGGLE
   UI-only: remembers light/dark preference in this browser.
   ============================================================ */
(function () {
    'use strict';

    const STORAGE_KEY = 'efactor_nas_home_theme';
    const root = document.documentElement;

    function normalizeTheme(value) {
        return value === 'dark' ? 'dark' : 'light';
    }

    function updateThemeButton(theme) {
        const button = document.getElementById('themeToggleBtn');
        const icon = document.getElementById('themeToggleIcon');

        if (!button || !icon) return;

        const isDark = theme === 'dark';

        icon.className = isDark
            ? 'fas fa-sun'
            : 'fas fa-moon';

        button.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        button.setAttribute(
            'aria-label',
            isDark ? 'Switch to light theme' : 'Switch to dark theme'
        );
        button.setAttribute(
            'title',
            isDark ? 'Switch to light theme' : 'Switch to dark theme'
        );
    }

    function applyTheme(theme, persist) {
        const normalized = normalizeTheme(theme);

        root.setAttribute('data-nas-theme', normalized);
        updateThemeButton(normalized);

        if (persist !== false) {
            try {
                localStorage.setItem(STORAGE_KEY, normalized);
            } catch (error) {
                console.warn('Unable to save NAS theme preference:', error);
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById('themeToggleBtn');
        const initialTheme = normalizeTheme(
            root.getAttribute('data-nas-theme')
        );

        applyTheme(initialTheme, false);

        if (!button) return;

        button.addEventListener('click', function () {
            const currentTheme = normalizeTheme(
                root.getAttribute('data-nas-theme')
            );

            applyTheme(
                currentTheme === 'dark' ? 'light' : 'dark',
                true
            );
        });
    });
})();
</script>

<script>
window._basePath = <?= json_encode($basePath) ?>;
window._apiBase = <?= json_encode($apiBase) ?>;
window.API_BASE = window._apiBase;
window.NAS_BASE = window._basePath;
window.NAS_ASSETS = <?= json_encode($assetsBase) ?>;
window._userId = <?= json_encode($userId) ?>;
window._username = <?= json_encode($username) ?>;
window._email = <?= json_encode($userEmail) ?>;

const API_BASE = window.API_BASE;
const BASE_PATH = window._basePath;
const USER_ID = window._userId;

console.log('✅ NAS Home - API_BASE:', API_BASE);
console.log('✅ NAS Home - BASE_PATH:', BASE_PATH);

document.addEventListener('DOMContentLoaded', function () {
    loadDashboardData();
});

async function loadDashboardData() {
    try {
        const response = await fetch(API_BASE + '/dashboard', {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Accept': 'application/json'
            }
        });

        const rawResponse = await response.text();

        let data;

        try {
            data = JSON.parse(rawResponse);
        } catch (parseError) {
            console.error(
                '❌ NAS dashboard returned non-JSON:',
                rawResponse
            );

            throw new Error(
                'Dashboard API returned an invalid server response.'
            );
        }

        if (!response.ok || !data.success) {
            throw new Error(
                data.message ||
                ('Dashboard request failed with status ' + response.status)
            );
        }

        document.getElementById('statFiles').textContent =
            data.stats?.files || 0;

        document.getElementById('statFolders').textContent =
            data.stats?.folders || 0;

        document.getElementById('statShared').textContent =
            data.stats?.shared || 0;

        document.getElementById('statTrash').textContent =
            data.stats?.trash_total || 0;

        document.getElementById('storageUsed').textContent =
            data.storage?.used_formatted || '0 B';

        document.getElementById('storageLimit').textContent =
            data.storage?.limit_formatted || '10 GB';

        const fill = document.getElementById('storageFill');
        const percentage = Number(data.storage?.percentage || 0);

        fill.style.width =
            Math.min(Math.max(percentage, 0), 100) + '%';

        fill.classList.toggle(
            'warning',
            percentage > 85
        );

        evaluateLowStorageWarning(
            data.storage || {}
        );

        renderRecentItems(
            data.recent_items || []
        );

    } catch (error) {
        console.error(
            '❌ Error loading NAS dashboard:',
            error
        );

        const recentItems =
            document.getElementById('recentItems');

        if (recentItems) {
            recentItems.innerHTML =
                '<div class="empty-state">' +
                '<i class="fas fa-exclamation-circle"></i>' +
                '<p>Unable to load dashboard data.</p>' +
                '</div>';
        }
    }
}


/* ============================================================
   LOW STORAGE WARNING
   ------------------------------------------------------------
   Trigger:
   occupied storage percentage >= 70%

   Suppression:
   Do not show when this Home page was reached directly from
   /NAS/dashboard (including dashboard query-string views).

   No storage values are written here. This only reads the
   existing live dashboard response.
   ============================================================ */

const NAS_LOW_STORAGE_TRIGGER_PERCENT = 70;


function isReturningFromNasDashboard() {
    const referrer =
        String(document.referrer || '').trim();

    if (!referrer) {
        return false;
    }

    try {
        const referrerUrl =
            new URL(referrer, window.location.href);

        if (
            referrerUrl.origin
            !==
            window.location.origin
        ) {
            return false;
        }

        const pathname =
            String(referrerUrl.pathname || '')
                .replace(/\/+$/, '');

        return /\/NAS\/dashboard$/i.test(
            pathname
        );

    } catch (error) {

        return false;
    }
}


function formatStorageRemaining(bytes) {
    const safeBytes =
        Math.max(
            0,
            Number(bytes) || 0
        );

    const gb =
        safeBytes
        /
        (1024 * 1024 * 1024);

    if (gb >= 1) {
        const rounded =
            gb >= 10
                ? gb.toFixed(1)
                : gb.toFixed(2);

        return (
            rounded
                .replace(/\.00$/, '')
                .replace(/(\.\d)0$/, '$1')
            +
            ' GB'
        );
    }

    const mb =
        safeBytes
        /
        (1024 * 1024);

    if (mb >= 1) {
        return (
            Math.max(0, Math.round(mb))
            +
            ' MB'
        );
    }

    const kb =
        safeBytes
        /
        1024;

    if (kb >= 1) {
        return (
            Math.max(0, Math.round(kb))
            +
            ' KB'
        );
    }

    return (
        Math.max(0, Math.round(safeBytes))
        +
        ' B'
    );
}


function evaluateLowStorageWarning(storage) {
    const used =
        Number(storage?.used);

    const limit =
        Number(storage?.limit);

    /*
     * Do not infer a warning when the API does not provide
     * valid positive quota data.
     */
    if (
        !Number.isFinite(used)
        ||
        !Number.isFinite(limit)
        ||
        limit <= 0
    ) {
        return;
    }

    const remaining =
        Math.max(
            0,
            limit - used
        );

    const usedPercentage =
        limit > 0
            ?
            (
                used
                /
                limit
            )
            *
            100
            :
            0;

    /*
     * The warning begins when occupied storage reaches
     * 70% or more of the total quota.
     */
    if (
        usedPercentage
        <
        NAS_LOW_STORAGE_TRIGGER_PERCENT
    ) {
        return;
    }

    /*
     * User requirement:
     * returning from Dashboard to Home must NOT trigger it.
     */
    if (
        isReturningFromNasDashboard()
    ) {
        console.log(
            'ℹ️ Low-storage popup suppressed: returned from NAS dashboard.'
        );

        return;
    }

    showLowStorageWarning({
        used: used,
        limit: limit,
        remaining: remaining,
        usedFormatted:
            storage?.used_formatted
            ||
            formatStorageRemaining(used),
        limitFormatted:
            storage?.limit_formatted
            ||
            formatStorageRemaining(limit),
        percentage:
            Number.isFinite(
                Number(storage?.percentage)
            )
                ?
                Number(storage.percentage)
                :
                usedPercentage
    });
}


function showLowStorageWarning(storage) {
    const overlay =
        document.getElementById(
            'nasLowStorageOverlay'
        );

    if (!overlay) {
        return;
    }

    const remainingBytes =
        Math.max(
            0,
            Number(storage.remaining) || 0
        );

    const remainingFormatted =
        formatStorageRemaining(
            remainingBytes
        );

    const usedFormatted =
        String(
            storage.usedFormatted
            ||
            formatStorageRemaining(storage.used)
        );

    const limitFormatted =
        String(
            storage.limitFormatted
            ||
            formatStorageRemaining(storage.limit)
        );

    const percentage =
        Math.min(
            Math.max(
                Number(storage.percentage)
                ||
                (
                    Number(storage.limit) > 0
                        ?
                        (
                            Number(storage.used)
                            /
                            Number(storage.limit)
                        )
                        *
                        100
                        :
                        0
                ),
                0
            ),
            100
        );

    const occupiedElement =
        document.getElementById(
            'nasLowStorageOccupied'
        );

    const remainingElement =
        document.getElementById(
            'nasLowStorageRemaining'
        );

    const usageElement =
        document.getElementById(
            'nasLowStorageUsage'
        );

    const percentageElement =
        document.getElementById(
            'nasLowStoragePercentage'
        );

    const messageElement =
        document.getElementById(
            'nasLowStorageMessage'
        );

    const progressFill =
        document.getElementById(
            'nasLowStorageProgressFill'
        );

    if (occupiedElement) {
        occupiedElement.textContent =
            usedFormatted;
    }

    if (remainingElement) {
        remainingElement.textContent =
            remainingFormatted;
    }

    if (usageElement) {
        usageElement.textContent =
            limitFormatted
            +
            ' total';
    }

    if (percentageElement) {
        percentageElement.textContent =
            percentage.toFixed(1)
            +
            '% used';
    }

    if (messageElement) {
        messageElement.innerHTML =
            'Your NAS storage is now <strong>'
            +
            escapeHtml(
                percentage.toFixed(1)
                +
                '% occupied'
            )
            +
            '</strong>. '
            +
            'The low-storage warning starts when storage usage reaches 70%.';
    }

    if (progressFill) {
        progressFill.style.width =
            '0%';

        window.requestAnimationFrame(
            function () {
                window.requestAnimationFrame(
                    function () {
                        progressFill.style.width =
                            percentage.toFixed(2)
                            +
                            '%';
                    }
                );
            }
        );
    }

    overlay.classList.add(
        'active'
    );

    overlay.setAttribute(
        'aria-hidden',
        'false'
    );

    document.body.classList.add(
        'nas-low-storage-open'
    );

    const dismissButton =
        document.getElementById(
            'nasLowStorageDismiss'
        );

    if (dismissButton) {
        window.setTimeout(
            function () {
                dismissButton.focus();
            },
            80
        );
    }
}


function closeLowStorageWarning() {
    const overlay =
        document.getElementById(
            'nasLowStorageOverlay'
        );

    if (!overlay) {
        return;
    }

    overlay.classList.remove(
        'active'
    );

    overlay.setAttribute(
        'aria-hidden',
        'true'
    );

    document.body.classList.remove(
        'nas-low-storage-open'
    );
}


document.addEventListener(
    'DOMContentLoaded',
    function () {

        const overlay =
            document.getElementById(
                'nasLowStorageOverlay'
            );

        const closeButton =
            document.getElementById(
                'nasLowStorageClose'
            );

        const dismissButton =
            document.getElementById(
                'nasLowStorageDismiss'
            );

        if (closeButton) {
            closeButton.addEventListener(
                'click',
                closeLowStorageWarning
            );
        }

        if (dismissButton) {
            dismissButton.addEventListener(
                'click',
                closeLowStorageWarning
            );
        }

        if (overlay) {
            overlay.addEventListener(
                'click',
                function (event) {
                    if (
                        event.target
                        ===
                        overlay
                    ) {
                        closeLowStorageWarning();
                    }
                }
            );
        }

        document.addEventListener(
            'keydown',
            function (event) {
                if (
                    event.key
                    ===
                    'Escape'
                    &&
                    overlay
                    &&
                    overlay.classList.contains(
                        'active'
                    )
                ) {
                    closeLowStorageWarning();
                }
            }
        );
    }
);



function renderRecentItems(items) {
    const container =
        document.getElementById('recentItems');

    if (!items || items.length === 0) {
        container.innerHTML =
            '<div class="empty-state">' +
            '<i class="fas fa-inbox"></i>' +
            '<p>No recent items yet.</p>' +
            '</div>';

        return;
    }

    let html = '';

    items.forEach(function (item) {
        const icon =
            item.type === 'folder'
                ? 'fa-folder'
                : 'fa-file';

        const iconClass =
            item.type === 'folder'
                ? 'folder'
                : 'file';

        const timeAgo =
            timeElapsed(item.created_at);

        html +=
            '<div class="recent-item" ' +
            'onclick="window.location.href=' +
            JSON.stringify(BASE_PATH + '/dashboard') +
            '">' +
                '<div class="icon ' +
                    iconClass +
                '">' +
                    '<i class="fas ' +
                        icon +
                    '"></i>' +
                '</div>' +
                '<div class="info">' +
                    '<div class="name">' +
                        escapeHtml(item.name) +
                    '</div>' +
                    '<div class="meta">' +
                        '<span>' +
                            escapeHtml(timeAgo) +
                        '</span>' +
                    '</div>' +
                '</div>' +
            '</div>';
    });

    container.innerHTML = html;
}

function timeElapsed(dateTime) {
    if (!dateTime) {
        return 'Unknown';
    }

    const now = new Date();
    const past = new Date(dateTime);

    if (Number.isNaN(past.getTime())) {
        return 'Unknown';
    }

    const diff =
        Math.floor((now - past) / 1000);

    if (diff < 60) {
        return 'Just now';
    }

    if (diff < 3600) {
        return Math.floor(diff / 60) + 'm ago';
    }

    if (diff < 86400) {
        return Math.floor(diff / 3600) + 'h ago';
    }

    if (diff < 604800) {
        return Math.floor(diff / 86400) + 'd ago';
    }

    if (diff < 2592000) {
        return Math.floor(diff / 604800) + 'w ago';
    }

    return past.toLocaleDateString();
}

function escapeHtml(text) {
    if (text === null || text === undefined) {
        return '';
    }

    const div =
        document.createElement('div');

    div.textContent =
        String(text);

    return div.innerHTML;
}

async function logout() {
    if (!confirm('Are you sure you want to logout?')) {
        return;
    }

    try {
        await fetch(API_BASE + '/auth/logout', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Accept': 'application/json'
            }
        });
    } catch (error) {
        console.warn(
            'NAS logout request failed:',
            error
        );
    }

    window.location.href =
        BASE_PATH + '/login';
}
</script>



<script src="<?= esc($assetsBase) ?>/js/config.js"></script>
<script src="<?= esc($assetsBase) ?>/js/websocket-client.js"></script>

</body>
</html>
