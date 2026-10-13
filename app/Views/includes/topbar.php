<?php load_js(array("assets/js/push_notification/pusher/pusher.min.js")); ?>
<?php $user = $login_user->id; ?>

<!-- Font Awesome 6 for navbar icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.4/dist/tailwind.min.css" rel="stylesheet">

<style>
/* ══════════════════════════════════════════════════════
   FULL-WIDTH NAVBAR — kill any sidebar offset
   ══════════════════════════════════════════════════════ */
#default-navbar.navbar-custom,
.navbar-custom,
nav.navbar.fixed-top {
    left: 0 !important; right: 0 !important;
    width: 100% !important; max-width: 100% !important;
    margin-left: 0 !important; margin-right: 0 !important;
}
body.sidebar-open .navbar-custom,
body.sidebar-mini .navbar-custom,
body[class*="sidebar"] .navbar-custom { left: 0 !important; width: 100% !important; }

/* ══════════════════════════════════════════════════════
   HIDE SIDEBAR
   ══════════════════════════════════════════════════════ */
.sidebar, .sidebar.sidebar-off, #left-menu-toggle-mask > .sidebar {
    display: none !important; width: 0 !important; min-width: 0 !important;
    max-width: 0 !important; overflow: hidden !important; padding: 0 !important; margin: 0 !important;
}
.page-container, .main-scrollable-page, .scrollable-page {
    margin-left: 0 !important; padding-left: 0 !important;
}
#left-menu-toggle-mask { margin-left: 0 !important; padding-left: 0 !important; }

/* ══════════════════════════════════════════════════════
   NAVBAR BASE — white background, violet accents
   ══════════════════════════════════════════════════════ */
#default-navbar.navbar-custom {
    height: 64px;
    background: #ffffff !important;
    border-bottom: 2px solid #ede9fe !important;
    box-shadow: 0 2px 20px rgba(109,40,217,0.10) !important;
    padding: 0 !important;
    position: relative !important;
}
/* Violet gradient accent line at bottom */
#default-navbar.navbar-custom::before {
    content: '';
    position: absolute; bottom: -2px; left: 0; right: 0; height: 2px;
    /* background: linear-gradient(90deg, #7c3aed 0%, #a78bfa 50%, #7c3aed 100%); */
    opacity: 1;
}
#default-navbar .container-fluid {
    width: 100% !important; max-width: 100% !important;
    padding: 0 1.5rem !important; height: 64px;
    display: flex; align-items: center; gap: 0;
}

/* ══════════════════════════════════════════════════════
   LOGO
   ══════════════════════════════════════════════════════ */
#default-navbar .navbar-brand-area {
    display: flex; align-items: center; flex-shrink: 0; margin-right: 2rem;
}
#default-navbar .sidebar-toggle-btn { display: none !important; }
#default-navbar .nav-logo-link {
    display: flex; align-items: center; text-decoration: none; flex-shrink: 0;
    background: none !important;
    border: none !important;
    padding: 0 !important;
    transition: opacity 0.2s;
}
#default-navbar .nav-logo-link:hover { opacity: 0.85; }
#default-navbar .nav-logo-img {
    height: 40px;
    max-width: 200px;
    width: auto;
    object-fit: contain;
    display: block;
    background: none !important;
    border: none !important;
    box-shadow: none !important;
    padding: 0 !important;
    filter: none;
}
/* Fallback when logo image fails */
#default-navbar .ef-logo-fallback {
    display: flex; align-items: center; gap: 9px;
}
#default-navbar .ef-logo-icon {
    width: 38px; height: 38px; border-radius: 10px;
    background: linear-gradient(135deg, #3b82f6, #7c3aed);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; flex-shrink: 0;
}
#default-navbar .ef-logo-text {
    font-size: 1.2rem; font-weight: 800; color: #fff;
    letter-spacing: -0.4px; line-height: 1;
}

/* ══════════════════════════════════════════════════════
   PRIMARY NAV LINKS — dark violet text, violet hover pill
   ══════════════════════════════════════════════════════ */
#default-navbar .ef-primary-link {
    display: inline-flex !important; align-items: center !important;
    gap: 7px !important; padding: 8px 14px !important;
    border-radius: 10px !important; font-size: 0.85rem !important;
    font-weight: 600 !important; color: #4c1d95 !important;
    transition: all 0.2s ease !important; position: relative !important;
    white-space: nowrap !important; text-decoration: none !important;
}
#default-navbar .ef-primary-link:hover {
    color: #6d28d9 !important;
    background: #f5f3ff !important;
    box-shadow: 0 2px 10px rgba(109,40,217,0.12) !important;
    transform: translateY(-1px) !important;
}
#default-navbar .ef-primary-link.ef-active {
    color: #7c3aed !important;
    background: #f5f3ff !important;
    font-weight: 700 !important;
}
#default-navbar .ef-primary-link.ef-active::after {
    content: ''; position: absolute; bottom: 3px; left: 14px; right: 14px;
    height: 2.5px;
    background: linear-gradient(90deg, #7c3aed, #a78bfa);
    border-radius: 2px; box-shadow: 0 0 6px rgba(124,58,237,0.4);
}
#default-navbar .ef-primary-link .ef-icon {
    font-size: 0.95rem; width: 16px; text-align: center; flex-shrink: 0; color: #7c3aed;
}
#default-navbar .ef-nav-label { display: inline; }

/* ══════════════════════════════════════════════════════
   MORE DROPDOWN BUTTON
   ══════════════════════════════════════════════════════ */
#default-navbar .ef-more-btn {
    display: inline-flex !important; align-items: center !important;
    gap: 7px !important; padding: 8px 14px !important;
    border-radius: 10px !important; font-size: 0.85rem !important;
    font-weight: 600 !important; color: #4c1d95 !important;
    background: none !important; border: none !important;
    cursor: pointer !important; transition: all 0.2s ease !important;
    white-space: nowrap !important;
}
#default-navbar .ef-more-btn:hover,
#default-navbar .ef-more-btn[aria-expanded="true"] {
    color: #6d28d9 !important;
    background: #f5f3ff !important;
    box-shadow: 0 2px 10px rgba(109,40,217,0.12) !important;
    transform: translateY(-1px) !important;
}
#default-navbar .ef-more-btn .ef-icon { font-size: 0.95rem; width: 16px; text-align: center; color: #7c3aed; }
#default-navbar .ef-more-dropdown {
    min-width: 240px !important; border-radius: 18px !important;
    border: 1px solid #ede9fe !important;
    box-shadow: 0 24px 56px rgba(109,40,217,0.18), 0 2px 8px rgba(0,0,0,0.06) !important;
    padding: 10px !important; margin-top: 10px !important; background: #fff !important;
}
#default-navbar .ef-more-section {
    font-size: 0.6rem; font-weight: 800; color: #a78bfa;
    text-transform: uppercase; letter-spacing: 1px; padding: 8px 14px 4px; display: block;
}
#default-navbar .ef-more-dropdown .dropdown-item {
    border-radius: 11px !important; font-size: 0.84rem !important;
    font-weight: 500 !important; color: #1e293b !important;
    padding: 9px 12px !important; display: flex !important;
    align-items: center !important; gap: 10px !important; transition: all 0.15s !important;
}
#default-navbar .ef-more-dropdown .dropdown-item:hover {
    background: #f5f3ff !important;
    color: #6d28d9 !important; transform: translateX(3px) !important;
}
#default-navbar .ef-more-dropdown .dropdown-item.ef-active-item {
    background: #f5f3ff !important; color: #6d28d9 !important; font-weight: 600 !important;
}
#default-navbar .ef-item-icon {
    width: 32px; height: 32px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.88rem; flex-shrink: 0; color: #fff;
}
#default-navbar .ef-badge-pill {
    background: linear-gradient(135deg,#7c3aed,#a78bfa); color:#fff;
    font-size:0.58rem; font-weight:700; padding:2px 7px; border-radius:40px; margin-left:auto;
}
#default-navbar .ef-more-dropdown .dropdown-divider { margin: 6px 0 !important; border-color: #ede9fe !important; }

/* ══════════════════════════════════════════════════════
   QUICK-ACCESS ICONS — violet on white
   ══════════════════════════════════════════════════════ */
#default-navbar .navbar-nav .nav-item .nav-link {
    color: #6d28d9 !important;
    border-radius: 9px !important;
    padding: 7px 9px !important;
    transition: all 0.18s !important;
    display: flex !important; align-items: center !important; gap: 5px !important;
}
#default-navbar .navbar-nav .nav-item .nav-link:hover {
    color: #7c3aed !important;
    background: #f5f3ff !important;
}
#default-navbar .navbar-nav .nav-item .nav-link .icon {
    stroke: #6d28d9 !important; width: 17px; height: 17px;
}
#default-navbar .nav-icon-pill {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: #f5f3ff;
    border: 1.5px solid #ede9fe;
    color: #7c3aed;
    transition: all 0.2s; position: relative;
    cursor: pointer; text-decoration: none;
}
#default-navbar .nav-icon-pill:hover {
    background: #ede9fe;
    color: #6d28d9;
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(124,58,237,0.18);
    border-color: #c4b5fd;
}
#default-navbar .nav-icon-pill .icon {
    width: 15px; height: 15px; stroke: #7c3aed;
}
#default-navbar .nav-icon-pill .notif-dot {
    position: absolute; top: 3px; right: 3px;
    width: 8px; height: 8px; border-radius: 50%;
    background: #7c3aed; border: 2px solid #fff;
    box-shadow: 0 0 6px rgba(124,58,237,0.5);
}

/* ══════════════════════════════════════════════════════
   DIVIDER
   ══════════════════════════════════════════════════════ */
#default-navbar .nav-divider {
    width: 1px; height: 24px;
    background: #ede9fe;
    margin: 0 8px; align-self: center; flex-shrink: 0;
}

/* ══════════════════════════════════════════════════════
   USER DROPDOWN — violet text on white
   ══════════════════════════════════════════════════════ */
#default-navbar #user-dropdown {
    display: flex !important; align-items: center !important; gap: 9px !important;
    padding: 5px 12px 5px 5px !important; border-radius: 40px !important;
    background: #f5f3ff !important;
    border: 1.5px solid #ede9fe !important;
    color: #4c1d95 !important;
    transition: all 0.2s !important; text-decoration: none !important;
}
#default-navbar #user-dropdown:hover {
    background: #ede9fe !important;
    border-color: #c4b5fd !important;
    box-shadow: 0 4px 16px rgba(124,58,237,0.15) !important;
}
#default-navbar #user-dropdown .avatar {
    width: 32px !important; height: 32px !important; border-radius: 50% !important;
    border: 2px solid #c4b5fd !important; object-fit: cover !important;
    flex-shrink: 0 !important; background: #ede9fe !important;
}
#default-navbar .ef-avatar-fallback {
    width: 32px; height: 32px; border-radius: 50%;
    background: linear-gradient(135deg, #7c3aed, #a78bfa);
    border: 2px solid #c4b5fd;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem; font-weight: 700; color: #fff; flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(124,58,237,0.25);
}
#default-navbar #user-dropdown .user-name {
    font-size: 0.82rem !important; font-weight: 700 !important;
    color: #4c1d95 !important;
    max-width: 130px !important; overflow: hidden !important;
    text-overflow: ellipsis !important; white-space: nowrap !important;
}
#default-navbar #user-dropdown::after {
    border-top-color: #7c3aed !important; margin-left: 4px !important;
}

/* Dropdown panel */
#default-navbar .dropdown-menu {
    border-radius: 18px !important; border: 1px solid #ede9fe !important;
    box-shadow: 0 24px 56px rgba(109,40,217,0.14), 0 2px 8px rgba(0,0,0,0.05) !important;
    padding: 10px !important; min-width: 215px !important;
    margin-top: 10px !important; background: #fff !important;
}
#default-navbar .dropdown-menu .dropdown-item {
    border-radius: 11px !important; font-size: 0.83rem !important;
    font-weight: 500 !important; color: #4c1d95 !important;
    padding: 9px 14px !important; display: flex !important;
    align-items: center !important; gap: 9px !important; transition: all 0.15s !important;
}
#default-navbar .dropdown-menu .dropdown-item:hover {
    background: #f5f3ff !important; color: #6d28d9 !important;
}
#default-navbar .dropdown-menu .dropdown-item .icon-16 {
    width: 15px; height: 15px; stroke: #7c3aed !important; flex-shrink: 0;
}
#default-navbar .dropdown-menu .dropdown-divider {
    margin: 6px 0 !important; border-color: #ede9fe !important;
}
#default-navbar .dropdown-menu .dropdown-item[href*="sign_out"],
#default-navbar .dropdown-menu .dropdown-item[href*="signout"] {
    color: #dc2626 !important; font-weight: 600 !important;
}
#default-navbar .dropdown-menu .dropdown-item[href*="sign_out"]:hover,
#default-navbar .dropdown-menu .dropdown-item[href*="signout"]:hover {
    background: #fff5f5 !important;
}
#default-navbar .language-dropdown { min-width: 160px !important; }

/* ══════════════════════════════════════════════════════
   MOBILE TOGGLER
   ══════════════════════════════════════════════════════ */
#default-navbar .navbar-toggler {
    border: 1.5px solid #c4b5fd !important; border-radius: 9px !important;
    padding: 6px 9px !important; background: #f5f3ff !important;
}
#default-navbar .navbar-toggler-icon {
    /* violet hamburger icon */
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%237c3aed' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
    filter: none !important;
    width: 18px; height: 18px;
}
#default-navbar .navbar-nav { gap: 2px; }
#default-navbar .d-flex { gap: 6px; align-items: center; }

/* ══════════════════════════════════════════════════════
   RESPONSIVE
   ══════════════════════════════════════════════════════ */
@media (max-width: 1200px) {
    #default-navbar .ef-nav-label { display: none; }
    #default-navbar .ef-primary-link { padding: 8px 10px !important; }
    #default-navbar .ef-more-btn { padding: 8px 10px !important; }
}
@media (max-width: 991px) {
    #default-navbar .navbar-collapse {
        background: #fff;
        border-radius: 16px; margin-top: 8px; padding: 14px;
        box-shadow: 0 16px 40px rgba(109,40,217,0.15);
        border: 1.5px solid #ede9fe;
    }
    #default-navbar .ef-nav-label { display: inline; }
    #default-navbar .navbar-nav .nav-item .nav-link { padding: 9px 14px !important; }
    #default-navbar #user-dropdown { border-radius: 12px !important; }
    #default-navbar .nav-divider { display: none; }
}
#default-navbar .navbar-toggler {
    border: 1px solid rgba(255,255,255,0.3) !important; border-radius: 9px !important;
    padding: 6px 9px !important; background: rgba(255,255,255,0.1) !important;
}
#default-navbar .navbar-toggler-icon { filter: invert(1) !important; width: 18px; height: 18px; }
#default-navbar .navbar-nav { gap: 2px; }
#default-navbar .d-flex { gap: 6px; align-items: center; }

/* ══════════════════════════════════════════════════════
   RESPONSIVE
   ══════════════════════════════════════════════════════ */
@media (max-width: 1200px) {
    #default-navbar .ef-nav-label { display: none; }
    #default-navbar .ef-primary-link { padding: 8px 10px !important; }
    #default-navbar .ef-more-btn { padding: 8px 10px !important; }
}
@media (max-width: 991px) {
    #default-navbar .navbar-collapse {
        background: linear-gradient(135deg, #0f172a, #1e3a8a);
        border-radius: 14px; margin-top: 8px; padding: 12px;
        box-shadow: 0 12px 32px rgba(0,0,0,0.3);
    }
    #default-navbar .ef-nav-label { display: inline; }
    #default-navbar .navbar-nav .nav-item .nav-link { padding: 9px 14px !important; }
    #default-navbar #user-dropdown { border-radius: 12px !important; }
    #default-navbar .nav-divider { display: none; }
}
</style>

<nav class="navbar navbar-expand-lg navbar-light navbar-custom shadow-2xl" role="navigation" id="default-navbar">
    <div class="container-fluid">

        <!-- LOGO -->
        <div class="navbar-brand-area">
            <a class="nav-logo-link" href="<?php echo get_uri('dashboard'); ?>">
                <!-- Primary: SVG logo from signin-images — crisp at any size -->
                <img class="nav-logo-img"
                     src="<?php echo base_url('files/signin-images/EFactor-logo.svg'); ?>"
                     alt="Efactor"
                     onerror="this.src='<?php echo base_url('files/signin-images/Logo.4.1.png'); ?>'; this.onerror=function(){ this.style.display='none'; this.nextElementSibling.style.display='flex'; };">
                <!-- Text fallback if both images fail -->
                
            </a>
        </div>

        <!-- Mobile toggler -->
        <button class="navbar-toggler ms-auto" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible nav -->
        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- LEFT: primary links + more + quick icons -->
            <ul class="navbar-nav me-auto mb-lg-0">

                <?php $current_uri = uri_string(); ?>

                <!-- Home -->
                <li class="nav-item">
                    <a href="<?php echo get_uri('dashboard'); ?>"
                       class="nav-link ef-primary-link <?php echo (strpos($current_uri,'dashboard')!==false)?'ef-active':''; ?>">
                        <i class="fas fa-house ef-icon"></i>
                        <span class="ef-nav-label">Home</span>
                    </a>
                </li>

                <!-- Mails -->
                <li class="nav-item">
                    <a href="<?php echo get_uri('messages/index'); ?>"
                       class="nav-link ef-primary-link <?php echo (strpos($current_uri,'messages')!==false)?'ef-active':''; ?>">
                        <i class="fas fa-envelope ef-icon"></i>
                        <span class="ef-nav-label">Mails</span>
                    </a>
                </li>

                <!-- Products -->
                <li class="nav-item">
                    <a href="<?php echo get_uri('items/index'); ?>"
                       class="nav-link ef-primary-link <?php echo (strpos($current_uri,'items')!==false)?'ef-active':''; ?>">
                        <i class="fas fa-box ef-icon"></i>
                        <span class="ef-nav-label">Products</span>
                    </a>
                </li>

                <!-- Customer Support -->
                <li class="nav-item">
                    <a href="https://efactor.tech/resources.html"
                       class="nav-link ef-primary-link" target="_blank" rel="noopener">
                        <i class="fas fa-headset ef-icon"></i>
                        <span class="ef-nav-label">Support</span>
                    </a>
                </li>

                <!-- More dropdown (all sidebar items) -->
                <li class="nav-item dropdown">
                    <button class="nav-link ef-more-btn dropdown-toggle"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-grip ef-icon"></i>
                        <span class="ef-nav-label">More</span>
                    </button>
                    <ul class="dropdown-menu ef-more-dropdown">

                        <li><span class="ef-more-section">Management</span></li>

                        <?php if ($login_user->is_admin || !empty($login_user->permissions['client'])): ?>
                        <li>
                            <a class="dropdown-item <?php echo strpos($current_uri,'clients')!==false?'ef-active-item':''; ?>"
                               href="<?php echo get_uri('clients'); ?>">
                                <div class="ef-item-icon" style="background:linear-gradient(135deg,#2563eb,#7c3aed)"><i class="fas fa-briefcase"></i></div>
                                Vendors
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo strpos($current_uri,'user_details')!==false?'ef-active-item':''; ?>"
                               href="<?php echo get_uri('user_details'); ?>">
                                <div class="ef-item-icon" style="background:linear-gradient(135deg,#0891b2,#06b6d4)"><i class="fas fa-users"></i></div>
                                App Users
                            </a>
                        </li>
                        <?php endif; ?>

                        <li>
                            <a class="dropdown-item <?php echo strpos($current_uri,'projects')!==false?'ef-active-item':''; ?>"
                               href="<?php echo get_uri('projects'); ?>">
                                <div class="ef-item-icon" style="background:linear-gradient(135deg,#16a34a,#22c55e)"><i class="fas fa-diagram-project"></i></div>
                                Projects
                            </a>
                        </li>

                        <?php if (get_setting('module_invoice') == '1'): ?>
                        <li>
                            <a class="dropdown-item <?php echo strpos($current_uri,'invoices')!==false?'ef-active-item':''; ?>"
                               href="<?php echo get_uri('invoices'); ?>">
                                <div class="ef-item-icon" style="background:linear-gradient(135deg,#d97706,#f59e0b)"><i class="fas fa-file-invoice"></i></div>
                                Invoices
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (get_setting('module_order') == '1'): ?>
                        <li>
                            <a class="dropdown-item <?php echo strpos($current_uri,'orders')!==false?'ef-active-item':''; ?>"
                               href="<?php echo get_uri('orders'); ?>">
                                <div class="ef-item-icon" style="background:linear-gradient(135deg,#0f766e,#14b8a6)"><i class="fas fa-cart-shopping"></i></div>
                                Orders
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (get_setting('module_ticket') == '1'): ?>
                        <li>
                            <a class="dropdown-item <?php echo strpos($current_uri,'tickets')!==false?'ef-active-item':''; ?>"
                               href="<?php echo get_uri('tickets'); ?>">
                                <div class="ef-item-icon" style="background:linear-gradient(135deg,#7c3aed,#a855f7)"><i class="fas fa-ticket"></i></div>
                                Tickets
                                <?php $tb = count_new_tickets(); if($tb > 0): ?>
                                <span class="ef-badge-pill"><?php echo $tb; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (get_setting('module_invoice') == '1' || get_setting('module_expense') == '1'): ?>
                        <li><div class="dropdown-divider"></div></li>
                        <li><span class="ef-more-section">Finance</span></li>
                        <?php if (get_setting('module_invoice') == '1'): ?>
                        <li>
                            <a class="dropdown-item <?php echo strpos($current_uri,'invoice_payments')!==false?'ef-active-item':''; ?>"
                               href="<?php echo get_uri('invoice_payments'); ?>">
                                <div class="ef-item-icon" style="background:linear-gradient(135deg,#f97316,#fb923c)"><i class="fas fa-dollar-sign"></i></div>
                                Payments
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (get_setting('module_expense') == '1'): ?>
                        <li>
                            <a class="dropdown-item <?php echo strpos($current_uri,'expenses')!==false?'ef-active-item':''; ?>"
                               href="<?php echo get_uri('expenses'); ?>">
                                <div class="ef-item-icon" style="background:linear-gradient(135deg,#dc2626,#ef4444)"><i class="fas fa-chart-line"></i></div>
                                Expenses
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($login_user->is_admin): ?>
                        <li><div class="dropdown-divider"></div></li>
                        <li><span class="ef-more-section">System</span></li>
                        <li>
                            <a class="dropdown-item <?php echo strpos($current_uri,'settings')!==false?'ef-active-item':''; ?>"
                               href="<?php echo get_uri('settings/general'); ?>">
                                <div class="ef-item-icon" style="background:linear-gradient(135deg,#475569,#64748b)"><i class="fas fa-gear"></i></div>
                                Settings
                            </a>
                        </li>
                        <?php endif; ?>

                    </ul>
                </li>

                <!-- Divider -->
                <li class="nav-item d-none d-lg-flex align-items-center">
                    <div class="nav-divider"></div>
                </li>

                <!-- Existing quick-access icons (todo, favorites, etc.) -->
                <?php
                $hidden_topbar_menus = explode(",", get_setting("user_" . $user . "_hidden_topbar_menus"));
                if (!in_array("to_do", $hidden_topbar_menus)) echo view("todo/topbar_icon");
                if (!in_array("favorite_projects", $hidden_topbar_menus) && !(get_setting("disable_access_favorite_project_option_for_clients") && $login_user->user_type == "client")) echo view("projects/star/topbar_icon");
                if (!in_array("favorite_clients", $hidden_topbar_menus)) echo view("clients/star/topbar_icon");
                if (!in_array("dashboard_customization", $hidden_topbar_menus) && (get_setting("disable_new_dashboard_icon") != 1)) echo view("dashboards/list/topbar_icon");
                if (has_my_open_timers()) echo view("projects/open_timers_topbar_icon");
                ?>
            </ul>

            <!-- RIGHT: search, quick-add, language, user -->
            <div class="d-flex align-items-center">
                <ul class="navbar-nav">

                    <!-- Search -->
                    <?php if ($login_user->user_type == "User") {
                        load_js(array("assets/js/awesomplete/awesomplete.min.js")); ?>
                        <li class="nav-item" title="<?php echo app_lang('search') . ' (/)'; ?>">
                            <?php echo modal_anchor(get_uri("search/search_modal_form"), "<i class='fas fa-magnifying-glass' style='font-size:0.9rem'></i>", array("class" => "nav-link nav-icon-pill", "data-modal-title" => app_lang('search') . ' (/)', "data-post-hide-header" => true, "id" => "global-search-btn")); ?>
                        </li>
                    <?php } ?>

                    <!-- Quick add -->
                    <!-- <?php if (!in_array("quick_add", $hidden_topbar_menus)) echo view("settings/topbar_parts/quick_add"); ?> -->

                    <!-- Language -->
                    <?php if (!in_array("language", $hidden_topbar_menus) && (($login_user->user_type == "User" && !get_setting("disable_language_selector_for_team_members")) || ($login_user->user_type == "client" && !get_setting("disable_language_selector_for_clients")))): ?>
                    <li class="nav-item dropdown">
                        <?php echo js_anchor("<i class='fas fa-globe' style='font-size:0.9rem'></i>", array("id" => "personal-language-icon", "class" => "nav-link nav-icon-pill", "data-bs-toggle" => "dropdown")); ?>
                        <ul class="dropdown-menu dropdown-menu-end language-dropdown">
                            <li>
                                <?php
                                $user_language = get_setting("user_" . $login_user->id . "_personal_language");
                                $system_language = get_setting("language");
                                foreach (get_language_list() as $language) {
                                    $ls = ""; $lt = $language;
                                    if ($user_language == strtolower($language) || (!$user_language && $system_language == strtolower($language))) {
                                        $ls = "<span class='float-end checkbox-checked m0'></span>";
                                        $lt = "<strong>$language</strong>";
                                    }
                                    if ($login_user->user_type == "User") echo ajax_anchor(get_uri("team_members/save_personal_language/$language"), $lt.$ls, array("class" => "dropdown-item clearfix", "data-reload-on-success" => "1"));
                                    else echo ajax_anchor(get_uri("clients/save_personal_language/$language"), $lt.$ls, array("class" => "dropdown-item clearfix", "data-reload-on-success" => "1"));
                                }
                                ?>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Divider -->
                    <li class="nav-item d-none d-lg-flex align-items-center">
                        <div class="nav-divider"></div>
                    </li>

                    <!-- User dropdown with avatar fallback -->
                    <li class="nav-item dropdown">
                        <a id="user-dropdown" href="#" class="nav-link dropdown-toggle"
                           data-bs-toggle="dropdown" role="button" aria-expanded="false">
                            <?php
                            $avatar_src = get_avatar($login_user->image);
                            $user_initials = strtoupper(substr($login_user->first_name ?? 'U', 0, 1));
                            ?>
                            <img class="avatar"
                                 alt="<?php echo htmlspecialchars($login_user->first_name ?? ''); ?>"
                                 src="<?php echo $avatar_src; ?>"
                                 onerror="this.style.display='none'; document.getElementById('ef-avatar-fb-<?php echo $login_user->id; ?>').style.display='flex';">
                            <span class="ef-avatar-fallback" id="ef-avatar-fb-<?php echo $login_user->id; ?>"
                                  style="display:none;"><?php echo $user_initials; ?></span>
                            <span class="user-name">
                                <?php echo htmlspecialchars(trim(($login_user->first_name ?? '') . ' ' . ($login_user->last_name ?? ''))); ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($login_user->user_type == "Brand"): ?>
                                <li><?php echo get_client_contact_profile_link($login_user->id.'/general', "<i data-feather='user' class='icon-16 me-2'></i>".app_lang('my_profile'), array("class"=>"dropdown-item")); ?></li>
                                <li><?php echo get_client_contact_profile_link($login_user->id.'/account', "<i data-feather='key' class='icon-16 me-2'></i>".app_lang('change_password'), array("class"=>"dropdown-item")); ?></li>
                                <li><?php echo get_client_contact_profile_link($login_user->id.'/my_preferences', "<i data-feather='settings' class='icon-16 me-2'></i>".app_lang('my_preferences'), array("class"=>"dropdown-item")); ?></li>
                            <?php else: ?>
                                <li><?php echo get_team_member_profile_link($login_user->id.'/general', "<i data-feather='user' class='icon-16 me-2'></i>".app_lang('my_profile'), array("class"=>"dropdown-item")); ?></li>
                                <li><?php echo get_team_member_profile_link($login_user->id.'/account', "<i data-feather='key' class='icon-16 me-2'></i>".app_lang('change_password'), array("class"=>"dropdown-item")); ?></li>
                                <li><?php echo get_team_member_profile_link($login_user->id.'/my_preferences', "<i data-feather='settings' class='icon-16 me-2'></i>".app_lang('my_preferences'), array("class"=>"dropdown-item")); ?></li>
                            <?php endif; ?>
                            <li class="dropdown-divider"></li>
                            <li>
                                <a href="<?php echo_uri('signin/sign_out'); ?>" class="dropdown-item">
                                    <i data-feather="log-out" class="icon-16 me-2"></i>
                                    <?php echo app_lang('sign_out'); ?>
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </div><!-- /collapse -->
    </div><!-- /container-fluid -->
</nav>

<!-- Fixed Dark Sidebar (modern admin) -->
<style>
/* Sidebar */
.ef-sidebar{position:fixed;left:0;top:0;height:100vh;width:250px;background:#111827;color:#e5e7eb;z-index:1030;overflow:auto;border-right:1px solid rgba(255,255,255,0.03);}
.ef-sidebar .ef-sidebar-inner{padding:18px 12px;display:flex;flex-direction:column;gap:8px}
.ef-sidebar .ef-brand{display:flex;align-items:center;gap:10px;padding:6px 8px;border-radius:8px;margin-bottom:8px}
.ef-sidebar .ef-brand .logo{width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#3b82f6,#7c3aed);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700}
.ef-sidebar .ef-menu{list-style:none;margin:6px 0;padding:0}
.ef-sidebar .ef-menu a{display:flex;align-items:center;gap:12px;padding:10px 12px;color:#cbd5e1;text-decoration:none;border-radius:8px;font-weight:600}
.ef-sidebar .ef-menu a:hover{background:rgba(255,255,255,0.03);color:#fff;transform:translateX(4px)}
.ef-sidebar .ef-menu a.active{background:linear-gradient(90deg,#1f2937,#111827);box-shadow:inset 4px 0 0 0 #7c3aed;color:#fff}
.ef-sidebar .ef-menu i{width:18px;text-align:center;color:#9ca3af}

/* Card updates */
.card{border:none;border-radius:12px;box-shadow:0 6px 18px rgba(15,23,42,0.08);transition:transform .12s ease,box-shadow .12s ease;padding:20px}
.card:hover{transform:translateY(-4px);box-shadow:0 18px 40px rgba(15,23,42,0.12)}
.card h2,.card h4{font-weight:700;color:#0f172a}
.card p, .card strong{color:#374151}

/* Device badges */
.badge-status{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:999px;font-weight:700;font-size:0.875rem}
.badge-status.online{background:linear-gradient(90deg,#ecfeff,#dcfce7);color:#065f46}
.badge-status.offline{background:linear-gradient(90deg,#fff1f2,#fee2e2);color:#7f1d1d}

/* Buttons modern look (composes with existing .btn classes) */
.ef-btn{border-radius:10px;padding:9px 14px;font-weight:700;box-shadow:0 6px 18px rgba(15,23,42,0.06);transition:transform .12s ease,box-shadow .12s ease}
.ef-btn:hover{transform:translateY(-2px);box-shadow:0 18px 40px rgba(15,23,42,0.1)}
.ef-btn.ef-primary{background:#0ea5a4;border-color:transparent;color:#fff}
.ef-btn.ef-secondary{background:#64748b;border-color:transparent;color:#fff}
.ef-btn.ef-warning{background:#f59e0b;border-color:transparent;color:#0f172a}
.ef-btn.ef-success{background:#10b981;border-color:transparent;color:#fff}
.ef-btn.ef-danger{background:#ef4444;border-color:transparent;color:#fff}

/* Responsive tweaks */
@media (max-width:991px){
  .ef-sidebar{position:fixed;left:0;top:56px;width:72px;height:calc(100vh - 56px)}
  .ef-sidebar .ef-menu a span.ef-label{display:none}
  #default-navbar.navbar-custom{left:0 !important; width:100% !important; max-width:100% !important;}
  body{padding-top:72px !important;}
}

</style>

<script>
$(document).click(function(e){
    if(!$(e.target).is('#navbar') && isMobile()) $('#navbar').collapse('hide');
});
var notificationOptions = {};
$(document).ready(function(){
    var messageOptions = {}, messageIcon="#message-notification-icon", notificationIcon="#web-notification-icon";
    messageOptions.notificationUrl="<?php echo_uri('messages/get_notifications'); ?>";
    messageOptions.notificationStatusUpdateUrl="<?php echo_uri('messages/update_notification_checking_status'); ?>";
    messageOptions.checkNotificationAfterEvery="<?php echo get_setting('check_notification_after_every'); ?>";
    messageOptions.icon="mail"; messageOptions.notificationSelector=$(messageIcon); messageOptions.isMessageNotification=true;
    checkNotifications(messageOptions);
    window.updateLastMessageCheckingStatus=function(){ checkNotifications(messageOptions,true); };
    $('body').on('show.bs.dropdown',messageIcon,function(){ checkNotifications(messageOptions,true); });
    notificationOptions.notificationUrl="<?php echo_uri('notifications/count_notifications'); ?>";
    notificationOptions.notificationStatusUpdateUrl="<?php echo_uri('notifications/update_notification_checking_status'); ?>";
    notificationOptions.checkNotificationAfterEvery="<?php echo get_setting('check_notification_after_every'); ?>";
    notificationOptions.icon="bell"; notificationOptions.notificationSelector=$(notificationIcon);
    notificationOptions.notificationType="web";
    notificationOptions.pushNotification="<?php echo get_setting('enable_push_notification') && $login_user->enable_web_notification && !get_setting('user_'.$login_user->id.'_disable_push_notification') ? true : false ?>";
    checkNotifications(notificationOptions);
    if(isMobile()){ notificationOptions.notificationUrlForMobile="<?php echo_uri('notifications/get_notifications'); ?>"; checkNotifications(notificationOptions); }
    $('body').on('show.bs.dropdown',notificationIcon,function(){ notificationOptions.notificationUrl="<?php echo_uri('notifications/get_notifications'); ?>"; checkNotifications(notificationOptions,true); });
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
