<?php
if (!function_exists('count_unread_message')) {
    function count_unread_message() { return 0; }
}
$user_id                 = $login_user->id ?? 1;
$vendorCount             = $vendorCount             ?? 54;
$appusers                = $appusers                ?? 2;
$onlineproducts          = $onlineproducts          ?? 52;
$offlineproducts         = $offlineproducts         ?? 48;
$totalmodules            = $totalmodules            ?? 86;
$totalusermodules        = $totalusermodules        ?? 54;
$totaluseronlinemodules  = $totaluseronlinemodules  ?? 2;
$totaluserofflinemodules = $totaluserofflinemodules ?? 52;
$appusername             = $appusername             ?? 'prafulsshah@yahoo.com';
$login_user              = $login_user              ?? (object)['is_admin' => true, 'id' => 1];
?>
<!-- 
<style>
/* ── Reset sidebar offset — sidebar is hidden via topbar.php CSS ── */
.page-container        { margin-left: 0 !important; padding-left: 0 !important; }
#left-menu-toggle-mask { margin-left: 0 !important; }
html, body             { overflow-x: hidden !important; }

/* ── Dashboard root ── */
.ef-dash {
    --v900:   #2e1065;
    --v800:   #3b0764;
    --v700:   #4c1d95;
    --v600:   #6d28d9;
    --v500:   #7c3aed;
    --v400:   #8b5cf6;
    --v300:   #a78bfa;
    --v200:   #c4b5fd;
    --v100:   #ede9fe;
    --v50:    #f5f3ff;
    --orange: #f97316;
    --teal:   #0f766e;
    --bg:     #f8f7ff;
    --surface:#ffffff;
    --border: #ede9fe;
    --text:   #1e1b4b;
    --muted:  #6b7280;
    --r-xl:   1.5rem;
    --r-lg:   1.1rem;
    --r-md:   0.75rem;
    --sh-sm:  0 2px 8px rgba(109,40,217,0.06);
    --sh-md:  0 8px 24px rgba(109,40,217,0.12);
    --sh-lg:  0 20px 40px rgba(109,40,217,0.14);

    font-family: 'Inter', 'Roboto', sans-serif;
    background: var(--bg);
    padding: 28px 24px;
    min-height: 100vh;
    box-sizing: border-box;
    color: var(--text);
}

/* ── Two-column grid ── */
.ef-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 1.5rem;
    align-items: start;
    max-width: 1400px;
    margin: 0 auto;
}
/* Left column — needs bottom padding so downloads aren't clipped */
.ef-grid > div:first-child {
    padding-bottom: 2rem;
}

/* ── Card base ── */
.ef-card {
    background: var(--surface);
    border-radius: var(--r-xl);
    border: 1px solid var(--border);
    box-shadow: var(--sh-sm);
    overflow: hidden;
}

/* ── Welcome banner ── */
.ef-banner {
    background: #ffffff;
    border-radius: var(--r-xl);
    padding: 1.6rem 1.75rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.25rem;
    position: relative;
    overflow: hidden;
    border: 1.5px solid #ddd6fe;
    box-shadow: 0 4px 20px rgba(109,40,217,0.08);
}
.ef-banner::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(rgba(124,58,237,0.05) 1px, transparent 1px);
    background-size: 24px 24px;
    pointer-events: none;
}
.ef-banner-deco {
    position: absolute; right: -40px; top: -40px;
    width: 180px; height: 180px; border-radius: 50%;
    background: radial-gradient(circle, rgba(167,139,250,0.12) 0%, transparent 70%);
    pointer-events: none;
}
.ef-banner-text { position: relative; }
.ef-banner-text h1 {
    font-size: 1.4rem; font-weight: 800; letter-spacing: -0.3px; margin: 0;
    color: #3b0764;
}
.ef-banner-text p {
    font-size: 0.82rem; color: #7c3aed; margin: 5px 0 0; font-weight: 500;
}
.ef-banner-btns { display: flex; gap: 8px; flex-wrap: wrap; position: relative; }

/* Banner pills */
.ef-pill {
    padding: 8px 16px; border-radius: 40px; font-weight: 600; font-size: 0.78rem;
    display: inline-flex; align-items: center; gap: 6px;
    text-decoration: none; transition: all 0.2s; cursor: pointer; white-space: nowrap;
}
.ef-pill-ghost {
    background: #f5f3ff; color: #6d28d9;
    border: 1.5px solid #c4b5fd;
    box-shadow: 0 2px 8px rgba(109,40,217,0.1);
}
.ef-pill-ghost:hover {
    background: #ede9fe; border-color: #7c3aed;
    transform: translateY(-2px); box-shadow: 0 4px 14px rgba(109,40,217,0.18);
}
.ef-pill-orange {
    background: linear-gradient(105deg,#f97316,#fb923c); color:#fff;
    border: none; box-shadow: 0 4px 12px rgba(249,115,22,0.25);
}
.ef-pill-orange:hover { transform: translateY(-2px); filter: brightness(1.06); }
.ef-pill-teal {
    background: linear-gradient(105deg,#0f766e,#14b8a6); color:#fff;
    border: none; box-shadow: 0 4px 12px rgba(15,118,110,0.25);
}
.ef-pill-teal:hover { transform: translateY(-2px); filter: brightness(1.06); }

/* ── Section heading ── */
.ef-section-title {
    font-size: 0.75rem; font-weight: 800; color: var(--v700);
    display: flex; align-items: center; gap: 7px; margin: 0 0 1rem;
    text-transform: uppercase; letter-spacing: 0.8px;
}
.ef-section-title i { color: var(--v500); font-size: 0.8rem; }

/* ── Stats grid ── */
.ef-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem; margin-bottom: 1.5rem; }
.ef-stat {
    background: var(--surface); border-radius: var(--r-lg); padding: 1.2rem 0.75rem;
    text-align: center; cursor: pointer; border: 1px solid var(--border);
    box-shadow: var(--sh-sm); transition: all 0.22s cubic-bezier(0.2,0.9,0.4,1.1);
}
.ef-stat:hover { transform: translateY(-5px); border-color: var(--v300); box-shadow: 0 16px 32px rgba(109,40,217,0.15); }
.ef-stat-icon {
    width: 42px; height: 42px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 0.65rem; font-size: 1rem; color: #fff;
}
.ef-stat-num {
    font-size: 1.9rem; font-weight: 800;
    background: linear-gradient(135deg, var(--v600), var(--v400));
    -webkit-background-clip: text; background-clip: text; color: transparent; line-height: 1.1;
}
.ef-stat-lbl { font-size: 0.72rem; font-weight: 600; color: var(--muted); margin-top: 4px; }

/* ── Module circles ── */
.ef-modules { display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem; margin-bottom: 1.5rem; }
.ef-mod {
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-lg);
    padding: 1.2rem 0.5rem; text-align: center; box-shadow: var(--sh-sm); transition: all 0.2s;
}
.ef-mod:hover { transform: translateY(-4px); box-shadow: var(--sh-md); border-color: var(--v300); }
.ef-mod-circle {
    width: 72px; height: 72px; margin: 0 auto 0.75rem; border-radius: 50%;
    background: linear-gradient(145deg, var(--v50), var(--v100));
    border: 2px solid var(--v200);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 6px 16px rgba(109,40,217,0.12);
}
.ef-mod-num {
    font-size: 1.5rem; font-weight: 800;
    background: linear-gradient(125deg, var(--v600), var(--v400));
    -webkit-background-clip: text; background-clip: text; color: transparent;
}
.ef-mod-lbl { font-size: 0.68rem; font-weight: 700; color: var(--v700); text-transform: uppercase; letter-spacing: 0.4px; line-height: 1.4; }

/* ── Quick actions ── */
.ef-actions { display: grid; grid-template-columns: repeat(2,1fr); gap: 1rem; margin-bottom: 1.5rem; }
.ef-action {
    background: var(--surface); border-radius: var(--r-lg); padding: 1.2rem 0.75rem;
    text-align: center; cursor: pointer; border: 1px solid var(--border);
    box-shadow: var(--sh-sm); transition: all 0.2s; text-decoration: none; display: block;
}
.ef-action:hover { transform: translateY(-4px); border-color: var(--v300); box-shadow: 0 14px 28px rgba(109,40,217,0.14); }
.ef-action-icon {
    width: 48px; height: 48px; margin: 0 auto 0.65rem; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; font-size: 1.1rem; color: #fff;
}
.ef-action-lbl { font-weight: 600; font-size: 0.78rem; color: var(--v700); line-height: 1.4; }

/* ── Divider ── */
.ef-divider { height: 1px; background: var(--border); margin: 1.5rem 0; }

/* ── Videos ── */
.ef-videos { display: grid; grid-template-columns: repeat(2,1fr); gap: 1rem; margin-bottom: 1.5rem; }
.ef-video-card {
    background: var(--surface); border-radius: var(--r-lg); overflow: hidden;
    border: 1px solid var(--border); box-shadow: var(--sh-sm); transition: all 0.2s;
}
.ef-video-card:hover { transform: scale(0.985); box-shadow: var(--sh-md); }
.ef-video-card video { width: 100%; height: 140px; object-fit: cover; background: #0f172a; display: block; }
.ef-video-title { padding: 9px 12px; font-weight: 600; font-size: 0.75rem; color: var(--v700); text-align: center; border-top: 1px solid var(--border); background: var(--v50); }

/* ── Downloads ── */
.ef-downloads { display: grid; grid-template-columns: repeat(2,1fr); gap: 1rem; }
.ef-dl-card { background: var(--v50); border-radius: var(--r-lg); padding: 1rem; text-align: center; border: 1px solid var(--v100); transition: all 0.2s; }
.ef-dl-card:hover { border-color: var(--v300); box-shadow: var(--sh-sm); }
.ef-dl-card h4 { font-size: 0.8rem; font-weight: 700; color: var(--v700); margin-bottom: 4px; }
.ef-dl-card p  { font-size: 0.7rem; color: var(--muted); margin-bottom: 8px; }
.ef-btn-dl {
    display: inline-block; background: linear-gradient(105deg, var(--v600), var(--v400));
    color: #fff; border: none; padding: 6px 16px; border-radius: 40px;
    font-weight: 600; font-size: 0.72rem; text-decoration: none; margin: 2px; transition: 0.2s; cursor: pointer;
    box-shadow: 0 2px 8px rgba(109,40,217,0.2);
}
.ef-btn-dl:hover { transform: translateY(-1px); filter: brightness(1.08); color: #fff; }
.ef-btn-dl-dark { background: linear-gradient(115deg,#1e293b,#0f172a); box-shadow: 0 2px 8px rgba(0,0,0,0.2); }

/* ── Notification panel ── */
.ef-notif-panel {
    position: sticky;
    top: 76px;
    max-height: calc(100vh - 96px);
    display: flex;
    flex-direction: column;
}
.ef-notif-panel.ef-card {
    overflow: visible; /* don't clip the panel itself */
}
.ef-notif-head {
    display: flex; justify-content: space-between; align-items: center;
    padding: 1.1rem 1.25rem 0.9rem; border-bottom: 1px solid var(--border);
}
.ef-notif-head h3 { font-size: 0.9rem; font-weight: 700; color: var(--v700); display: flex; align-items: center; gap: 7px; margin: 0; }
.ef-notif-head h3 i { color: var(--v500); }
.ef-notif-badge { background: linear-gradient(135deg, var(--v500), var(--v300)); color:#fff; font-size:0.62rem; font-weight:700; padding:2px 8px; border-radius:40px; }
.ef-notif-sub { font-size: 0.68rem; color: var(--muted); }
#ef-notif-window {
    flex: 1;
    max-height: calc(100vh - 180px);
    overflow-y: auto; overflow-x: hidden;
    padding: 0.9rem 1rem; scrollbar-width: thin; scrollbar-color: var(--v300) var(--v50);
}
#ef-notif-window::-webkit-scrollbar { width: 4px; }
#ef-notif-window::-webkit-scrollbar-track { background: var(--v50); border-radius: 10px; }
#ef-notif-window::-webkit-scrollbar-thumb { background: var(--v300); border-radius: 10px; }
.ef-notif-item {
    background: var(--v50); border-radius: var(--r-md); padding: 0.8rem 0.9rem;
    margin-bottom: 0.65rem; border: 1px solid var(--v100); border-left: 3px solid var(--v500);
    transition: all 0.2s; cursor: pointer;
}
.ef-notif-item:hover { transform: translateX(3px); box-shadow: 0 4px 14px rgba(109,40,217,0.1); border-color: var(--v200); }
.ef-notif-item.new-notification { background: linear-gradient(115deg,#fff7ed,#fffbeb); border-left-color: var(--orange); animation: efFlash 0.4s ease; }
@keyframes efFlash { 0%{transform:scale(0.97);} 100%{transform:scale(1);} }
.ef-notif-date { display:inline-flex; align-items:center; gap:4px; background:var(--v100); padding:2px 9px; border-radius:40px; font-size:0.65rem; font-weight:700; color:var(--v600); margin-bottom:6px; }
.ef-user-list { display:flex; flex-wrap:wrap; gap:5px; margin-top:5px; }
.ef-user-item { background:var(--surface); border:1px solid var(--v100); border-radius:30px; padding:3px 9px; font-size:0.65rem; font-weight:500; display:inline-flex; align-items:center; gap:4px; color:var(--v600); }

/* ── Sign-out modal ── */
.ef-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(46,16,101,0.45); backdrop-filter: blur(6px);
    z-index: 99999; align-items: center; justify-content: center;
}
.ef-overlay.show { display: flex; }
.ef-modal {
    background: #fff; border-radius: var(--r-xl); padding: 2rem 1.75rem;
    max-width: 340px; width: 90%; text-align: center;
    box-shadow: 0 32px 64px rgba(46,16,101,0.25);
    animation: efModalIn 0.25s cubic-bezier(.4,0,.2,1);
    border: 1px solid var(--v100);
}
@keyframes efModalIn { from{opacity:0;transform:scale(0.9) translateY(20px);} to{opacity:1;transform:scale(1) translateY(0);} }
.ef-modal-icon { width:56px; height:56px; border-radius:50%; background:#fff5f5; border:2px solid #fecaca; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; font-size:1.4rem; color:#dc2626; }
.ef-modal h3 { font-size:1.15rem; font-weight:700; color:var(--v800); margin:0 0 0.4rem; }
.ef-modal p  { font-size:0.85rem; color:var(--muted); margin:0 0 1.3rem; line-height:1.6; }
.ef-modal-btns { display:flex; gap:0.6rem; justify-content:center; }
.ef-btn-cancel { flex:1; padding:9px 16px; border-radius:40px; border:1.5px solid var(--border); background:#fff; color:var(--muted); font-size:0.84rem; font-weight:600; cursor:pointer; transition:all 0.2s; }
.ef-btn-cancel:hover { background:var(--v50); color:var(--v700); border-color:var(--v200); }
.ef-btn-signout-c { flex:1; padding:9px 16px; border-radius:40px; border:none; background:linear-gradient(135deg,#dc2626,#ef4444); color:#fff; font-size:0.84rem; font-weight:700; cursor:pointer; transition:all 0.2s; box-shadow:0 4px 12px rgba(220,38,38,0.28); }
.ef-btn-signout-c:hover { transform:translateY(-1px); box-shadow:0 8px 20px rgba(220,38,38,0.35); }

/* ── Responsive ── */
@media (max-width: 1100px) {
    .ef-grid { grid-template-columns: 1fr; }
    .ef-notif-panel { position: static; }
}
@media (max-width: 768px) {
    .ef-dash { padding: 16px 14px; }
    .ef-stats { grid-template-columns: repeat(2,1fr); }
    .ef-videos { grid-template-columns: 1fr; }
    .ef-downloads { grid-template-columns: 1fr; }
    .ef-banner-text h1 { font-size: 1.1rem; }
}
@media (max-width: 480px) {
    .ef-stats { grid-template-columns: repeat(2,1fr); }
    .ef-modules { gap: 0.5rem; }
    .ef-actions { grid-template-columns: repeat(2,1fr); }
    .ef-banner { padding: 1rem; }
    .ef-banner-text h1 { font-size: 1rem; }
}
</style> -->

<!-- Sign-out confirmation modal -->
<div class="ef-overlay" id="efSignoutOverlay" onclick="efOverlayClick(event)">
    <div class="ef-modal">
        <div class="ef-modal-icon"><i class="fas fa-sign-out-alt"></i></div>
        <h3>Sign Out?</h3>
        <p>Are you sure you want to sign out of your Efactor account?</p>
        <div class="ef-modal-btns">
            <button class="ef-btn-cancel" onclick="efHideSignout()">Cancel</button>
            <button class="ef-btn-signout-c" id="efSignoutConfirmBtn">
                <i class="fas fa-sign-out-alt"></i> Yes, Sign Out
            </button>
        </div>
    </div>
</div>

<!-- Dashboard wrapper -->
<div class="ef-dash">
<div class="ef-grid">

<!-- ══ LEFT COLUMN ══ -->
<div>

    <!-- Welcome Banner -->
    <div class="ef-banner">
        <div class="ef-banner-deco"></div>
        <div class="ef-banner-text">
            <h1>👋 Welcome, <?= htmlspecialchars($appusername) ?></h1>
            <p>Manage your ecosystem — smart insights &amp; tools</p>
        </div>
        <div class="ef-banner-btns">
            <a href="https://efactor.tech/efactor_visiting_profile.php?user_id=<?= urlencode($user_id) ?>" class="ef-pill ef-pill-ghost">
                <i class="fas fa-th-large"></i> GRID
            </a>
            <a href="https://efactor.tech/visiting_card_studio.php?user_id=<?= urlencode($user_id) ?>" class="ef-pill ef-pill-orange">
                <i class="fas fa-id-card"></i> Visiting Card
            </a>
            <a href="<?= base_url('restaurant-system/main') ?>?user_id=<?= urlencode($user_id) ?>&appuser=<?= urlencode($appusername) ?>" class="ef-pill ef-pill-teal">
                <i class="fas fa-utensils"></i> Restaurant
            </a>
        </div>
    </div>

    <!-- Admin Stats -->
    <?php if ($login_user->is_admin): ?>
    <div class="ef-section-title"><i class="fas fa-chart-bar"></i> Overview</div>
    <div class="ef-stats">
        <div class="ef-stat" onclick="location.href='<?= site_url('clients/index') ?>'">
            <div class="ef-stat-icon" style="background:linear-gradient(135deg,#2563eb,#7c3aed)"><i class="fas fa-store"></i></div>
            <div class="ef-stat-num"><?= $vendorCount ?></div>
            <div class="ef-stat-lbl">Vendors</div>
        </div>
        <div class="ef-stat" onclick="location.href='<?= site_url('user_details/index') ?>'">
            <div class="ef-stat-icon" style="background:linear-gradient(135deg,#0891b2,#06b6d4)"><i class="fas fa-users"></i></div>
            <div class="ef-stat-num"><?= $appusers ?></div>
            <div class="ef-stat-lbl">App Users</div>
        </div>
        <div class="ef-stat" onclick="location.href='<?= site_url('items/index') ?>?search=Online'">
            <div class="ef-stat-icon" style="background:linear-gradient(135deg,#16a34a,#22c55e)"><i class="fas fa-wifi"></i></div>
            <div class="ef-stat-num"><?= $onlineproducts ?></div>
            <div class="ef-stat-lbl">Online Products</div>
        </div>
        <div class="ef-stat" onclick="location.href='<?= site_url('items/index') ?>'">
            <div class="ef-stat-icon" style="background:linear-gradient(135deg,#d97706,#f59e0b)"><i class="fas fa-box"></i></div>
            <div class="ef-stat-num"><?= $offlineproducts ?></div>
            <div class="ef-stat-lbl">Offline Products</div>
        </div>
        <div class="ef-stat" onclick="location.href='<?= site_url('items/index') ?>?search=Offline'">
            <div class="ef-stat-icon" style="background:linear-gradient(135deg,#7c3aed,#a855f7)"><i class="fas fa-puzzle-piece"></i></div>
            <div class="ef-stat-num"><?= $totalmodules ?></div>
            <div class="ef-stat-lbl">Total Modules</div>
        </div>
        <div class="ef-stat" onclick="location.href='<?= base_url('dashboard/user_verification') ?>'">
            <div class="ef-stat-icon" style="background:linear-gradient(135deg,#0f766e,#14b8a6)"><i class="fas fa-clipboard-list"></i></div>
            <div class="ef-stat-num" style="font-size:1.2rem">📋</div>
            <div class="ef-stat-lbl">User Info</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Module Stats -->
    <div class="ef-section-title"><i class="fas fa-layer-group"></i> Module Stats</div>
    <div class="ef-modules">
        <div class="ef-mod">
            <div class="ef-mod-circle"><div class="ef-mod-num"><?= $totalusermodules ?></div></div>
            <div class="ef-mod-lbl">Total User Modules</div>
        </div>
        <div class="ef-mod">
            <div class="ef-mod-circle"><div class="ef-mod-num"><?= $totaluseronlinemodules ?></div></div>
            <div class="ef-mod-lbl">Online Modules</div>
        </div>
        <div class="ef-mod">
            <div class="ef-mod-circle"><div class="ef-mod-num"><?= $totaluserofflinemodules ?></div></div>
            <div class="ef-mod-lbl">Offline Modules</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="ef-section-title"><i class="fas fa-bolt"></i> Quick Actions</div>
    <div class="ef-actions">
        <a class="ef-action" href="<?= site_url('items/vendor') ?>">
            <div class="ef-action-icon" style="background:linear-gradient(135deg,#2563eb,#7c3aed)"><i class="fas fa-eye"></i></div>
            <div class="ef-action-lbl">View Efactor Models</div>
        </a>
        <a class="ef-action" href="<?= site_url('items/vendor') ?>">
            <div class="ef-action-icon" style="background:linear-gradient(135deg,#f97316,#facc15)"><i class="fas fa-key"></i></div>
            <div class="ef-action-lbl">APP ID Generation</div>
        </a>
        <div class="ef-action">
            <div class="ef-action-icon" style="background:linear-gradient(135deg,#06b6d4,#3b82f6)"><i class="fas fa-upload"></i></div>
            <div class="ef-action-lbl">App Registration</div>
        </div>
        <a class="ef-action" href="https://efactor.tech/resources.html" target="_blank" rel="noopener">
            <div class="ef-action-icon" style="background:linear-gradient(135deg,#10b981,#34d399)"><i class="fas fa-question-circle"></i></div>
            <div class="ef-action-lbl">How to Add API Form</div>
        </a>
    </div>

    <div class="ef-divider"></div>

    <!-- Video Tutorials -->
    <div class="ef-section-title"><i class="fas fa-play-circle"></i> Video Tutorials</div>
    <div class="ef-videos">
        <div class="ef-video-card">
            <video controls preload="none"><source src="https://efactor.tech/appstore/app_install.mp4" type="video/mp4"></video>
            <div class="ef-video-title">APP INSTALL VIDEO</div>
        </div>
        <div class="ef-video-card">
            <video controls preload="none"><source src="https://efactor.tech/appstore/peer-to-peer.mp4" type="video/mp4"></video>
            <div class="ef-video-title">PEER TO PEER</div>
        </div>
        <div class="ef-video-card">
            <video controls preload="none"><source src="https://efactor.tech/appstore/ping.mp4" type="video/mp4"></video>
            <div class="ef-video-title">PING</div>
        </div>
        <div class="ef-video-card">
            <video controls preload="none"><source src="https://efactor.tech/appstore/addnet.mp4" type="video/mp4"></video>
            <div class="ef-video-title">ADDING DEVICE TO NETWORK</div>
        </div>
    </div>

    <div class="ef-divider"></div>

    <!-- Downloads -->
    <div class="ef-section-title"><i class="fas fa-download"></i> Downloads &amp; Resources</div>
    <div class="ef-downloads">
        <div class="ef-dl-card">
            <h4><i class="fas fa-file-excel"></i> Download API</h4>
            <p>API_Specification.xlsx</p>
            <a href="https://efactor.tech/appstore/API_Specification.xlsx" class="ef-btn-dl" download>Download</a>
        </div>
        <div class="ef-dl-card">
            <h4><i class="fas fa-book-open"></i> Efactor Library</h4>
            <p>Full documentation</p>
            <a href="https://efactor.tech/v8/EfactorLibrary.html" class="ef-btn-dl" target="_blank" rel="noopener">Open Library</a>
        </div>
        <div class="ef-dl-card">
            <h4><i class="fas fa-file-code"></i> Provision Files</h4>
            <p>Java &amp; XML files</p>
            <a href="https://efactor.tech/v8/ProvisionActivity.java" class="ef-btn-dl" download>Java</a>
            <a href="https://efactor.tech/v8/activity_provision.xml" class="ef-btn-dl" download>XML</a>
        </div>
        <div class="ef-dl-card">
            <h4><i class="fas fa-print"></i> Printer Software</h4>
            <p>Local driver V1</p>
            <button class="ef-btn-dl ef-btn-dl-dark"
                    onclick="window.location.href='https://efactor.tech/printer-driver/printer-local-driver_V1.exe'">
                Download
            </button>
        </div>
    </div>

</div><!-- /left col -->

<!-- ══ RIGHT: NOTIFICATION PANEL ══ -->
<div class="ef-notif-panel ef-card">
    <div class="ef-notif-head">
        <h3>
            <i class="fas fa-bell" style="color:#2563eb"></i>
            Live Alerts
            <span id="efNotifBadge" class="ef-notif-badge">0</span>
        </h3>
        <span class="ef-notif-sub">real-time</span>
    </div>
    <div id="ef-notif-window">
        <div style="text-align:center;padding:2rem;color:#64748b">
            <i class="fas fa-spinner fa-pulse fa-lg"></i>
            <p style="margin-top:10px;font-size:0.75rem">Loading notifications…</p>
        </div>
    </div>
</div>

</div><!-- /ef-grid -->
</div><!-- /ef-dash -->

<script>
/* ── Sign-out modal ── */
(function(){
    var url = '<?= site_url("signin/sign_out") ?>';
    function efShowSignout(){ document.getElementById('efSignoutOverlay').classList.add('show'); }
    function efHideSignout(){ document.getElementById('efSignoutOverlay').classList.remove('show'); }
    function efOverlayClick(e){ if(e.target===document.getElementById('efSignoutOverlay')) efHideSignout(); }
    document.getElementById('efSignoutConfirmBtn').addEventListener('click',function(){ window.location.href=url; });
    document.addEventListener('keydown',function(e){ if(e.key==='Escape') efHideSignout(); });
    document.addEventListener('DOMContentLoaded',function(){
        document.querySelectorAll('a[href*="sign_out"],a[href*="signout"]').forEach(function(el){
            el.addEventListener('click',function(e){ e.preventDefault(); efShowSignout(); });
        });
    });
    window.efHideSignout  = efHideSignout;
    window.efOverlayClick = efOverlayClick;
    window.efShowSignout  = efShowSignout;
})();

/* ── Notifications ── */
var efLastNotifs = [];
function efEsc(s){ var d=document.createElement('div'); d.appendChild(document.createTextNode(s||'')); return d.innerHTML; }

function efFetchNotifications(){
    fetch('<?= base_url('dashboard/notifications') ?>')
        .then(function(r){ return r.ok?r.json():Promise.reject(); })
        .then(function(data){
            var c=document.getElementById('ef-notif-window');
            if(!c) return;
            var prev=efLastNotifs.map(function(i){ return (i.date||'')+(i.count||0); });
            c.innerHTML='';
            if(!data||!data.length){
                c.innerHTML='<div style="text-align:center;padding:2rem;color:#64748b"><i class="far fa-bell-slash fa-2x"></i><p style="margin-top:10px;font-size:0.75rem">No new notifications</p></div>';
                document.getElementById('efNotifBadge').innerText='0';
                efLastNotifs=data||[];
                return;
            }
            var total=0;
            data.forEach(function(item){
                total+=item.count||0;
                var usersHtml='';
                if(!item.users||!item.users.length){
                    usersHtml='<div class="ef-user-item"><i class="fas fa-user"></i> No users registered</div>';
                } else {
                    usersHtml=item.users.map(function(u){
                        return '<div class="ef-user-item"><i class="fas fa-envelope"></i> '+efEsc(u.email||'N/A')+'</div>';
                    }).join('');
                }
                var isNew=efLastNotifs.length>0&&prev.indexOf((item.date||'')+(item.count||0))===-1;
                var card=document.createElement('div');
                card.className='ef-notif-item'+(isNew?' new-notification':'');
                card.innerHTML=
                    '<div class="ef-notif-date"><i class="far fa-calendar-alt"></i> '+efEsc(item.date||'Just now')+
                    ' &bull; '+(item.count||0)+' new user'+((item.count!==1)?'s':'')+'</div>'+
                    '<div class="ef-user-list">'+usersHtml+'</div>'+
                    '<div style="font-size:0.58rem;margin-top:5px;color:#94a3b8">'+(isNew?'✨ fresh arrival':'📌 earlier')+'</div>';
                c.appendChild(card);
            });
            document.getElementById('efNotifBadge').innerText=total;
            efLastNotifs=data;
        })
        .catch(function(){
            var c=document.getElementById('ef-notif-window');
            if(c&&efLastNotifs.length===0)
                c.innerHTML='<div style="text-align:center;padding:2rem;color:#64748b"><i class="fas fa-exclamation-circle"></i><p style="margin-top:8px;font-size:0.72rem">Could not load</p></div>';
        });
}

/* ── Title unread count ── */
function efUpdateTitle(){
    fetch('<?= site_url('messages/get_unread_count') ?>?t='+Date.now())
        .then(function(r){ return r.ok?r.json():Promise.reject(); })
        .then(function(d){
            var n=d.unread_count||d.count||0;
            document.title=parseInt(n)>0?'('+n+') Efactor Dashboard':'Efactor Dashboard';
        })
        .catch(function(){})
        .finally(function(){ setTimeout(efUpdateTitle,5000); });
}

efFetchNotifications();
efUpdateTitle();
setInterval(efFetchNotifications, 28000);
</script>
