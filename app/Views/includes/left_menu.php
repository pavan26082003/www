<div class="sidebar sidebar-off" style="background-color: #4A90E2 !important; color: #fff !important;">
    <?php
    $user = $login_user->id;
    $dashboard_link = get_uri("dashboard");
    $user_dashboard = get_setting("user_" . $user . "_dashboard");
    if ($user_dashboard) {
        $dashboard_link = get_uri("dashboard/view/" . $user_dashboard);
    }
    ?>
    <a class="sidebar-toggle-btn hide" href="#">
        <i data-feather="menu" class="icon mt-1 text-off"></i>
    </a>

    <a class="sidebar-brand brand-logo" href="<?php echo $dashboard_link; ?>"><img class="dashboard-image" src="<?php echo get_logo_url(); ?>" /></a>
    <a class="sidebar-brand brand-logo-mini" href="<?php echo $dashboard_link; ?>"><img class="dashboard-image" src="<?php echo get_favicon_url(); ?>" /></a>

    <div class="sidebar-scroll">
        <ul id="sidebar-menu" class="sidebar-menu">
            <?php
            if (!$is_preview) {
                $sidebar_menu = get_active_menu($sidebar_menu);
            }

            foreach ($sidebar_menu as $main_menu) {

                if($main_menu['name']=='projects' || $main_menu['name']=='users')
                    continue;

                if (isset($main_menu["name"])) {
                    $submenu = get_array_value($main_menu, "submenu");
                    $expend_class = $submenu ? " expand " : "";
                    $active_class = isset($main_menu["is_active_menu"]) ? "active" : "";

                    $submenu_open_class = "";
                    if ($expend_class && $active_class) {
                        $submenu_open_class = " open ";
                    }

                    $badge = get_array_value($main_menu, "badge");
                    $badge_class = get_array_value($main_menu, "badge_class");
                    $target = (isset($main_menu['is_custom_menu_item']) && isset($main_menu['open_in_new_tab']) && $main_menu['open_in_new_tab']) ? "target='_blank'" : "";
                    ?>

                    <li class="<?php echo $active_class . " " . $expend_class . " " . $submenu_open_class . " "; ?> main">
                        <a <?php echo $target; ?> href="<?php echo isset($main_menu['is_custom_menu_item']) ? $main_menu['url'] : get_uri($main_menu['url']); ?>">
                            <i data-feather="<?php echo ($main_menu['class']); ?>" class="icon"></i>
                            <span class="menu-text <?php echo isset($main_menu['custom_class']) ? $main_menu['custom_class'] : ""; ?>">

                                <?php
                                if($main_menu['name']=='items')
                                 echo 'Products';
                             else
                                echo isset($main_menu['is_custom_menu_item']) ? $main_menu['name'] : app_lang($main_menu['name']);
                                ?>
                            </span>
                            <?php
                            if ($badge) {
                                echo "<span class='badge rounded-pill $badge_class'>$badge</span>";
                            }
                            ?>
                        </a>
                        <?php
                        if ($submenu) {
                            echo "<ul>";
                            foreach ($submenu as $s_menu) {
                                if (isset($s_menu['name'])) {
                                    $sub_menu_target = (isset($s_menu['is_custom_menu_item']) && isset($s_menu['open_in_new_tab']) && $s_menu['open_in_new_tab']) ? "target='_blank'" : "";
                                    ?>
                                <li>
                                    <a <?php echo $sub_menu_target; ?> href="<?php echo isset($s_menu['is_custom_menu_item']) ? $s_menu['url'] : get_uri($s_menu['url']); ?>">
                                        <i data-feather='minus' width='12'></i>
                                        <span><?php echo isset($s_menu['is_custom_menu_item']) ? $s_menu['name'] : app_lang($s_menu['name']); ?></span>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                        echo "</ul>";
                    }
                    ?>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
    </div>
</div><!-- sidebar menu end -->

<!-- Sidebar refined styles -->
<style type="text/css">
    /* ── Sidebar base ── */
    .sidebar,
    .sidebar.sidebar-off {
        background: linear-gradient(180deg, #1e40af 0%, #1d4ed8 40%, #2563eb 100%) !important;
        border-right: none !important;
        box-shadow: 4px 0 20px rgba(37,99,235,0.18) !important;
    }

    /* Brand logo area */
    .sidebar .sidebar-brand {
        padding: 1rem 1.2rem !important;
        border-bottom: 1px solid rgba(255,255,255,0.12) !important;
        margin-bottom: 0.5rem !important;
    }

    /* Scrollable area */
    .sidebar .sidebar-scroll {
        padding: 0.5rem 0.6rem !important;
    }

    /* Menu items */
    .sidebar-menu li a {
        border-radius: 10px !important;
        margin: 2px 0 !important;
        padding: 9px 14px !important;
        color: rgba(255,255,255,0.78) !important;
        font-size: 0.82rem !important;
        font-weight: 500 !important;
        transition: all 0.18s ease !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }
    .sidebar-menu li a .icon,
    .sidebar-menu li a svg {
        color: rgba(255,255,255,0.65) !important;
        stroke: rgba(255,255,255,0.65) !important;
        flex-shrink: 0 !important;
    }

    /* Hover */
    .sidebar-menu li a:hover {
        background: rgba(255,255,255,0.14) !important;
        color: #fff !important;
    }
    .sidebar-menu li a:hover .icon,
    .sidebar-menu li a:hover svg {
        color: #fff !important;
        stroke: #fff !important;
    }

    /* Active */
    .sidebar-menu li.active > a {
        background: rgba(255,255,255,0.22) !important;
        color: #fff !important;
        font-weight: 700 !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.12) !important;
    }
    .sidebar-menu li.active > a .icon,
    .sidebar-menu li.active > a svg {
        color: #fff !important;
        stroke: #fff !important;
    }

    /* Submenu */
    .sidebar-menu li > ul {
        background: rgba(0,0,0,0.12) !important;
        border-radius: 8px !important;
        margin: 2px 0 !important;
        padding: 4px 0 !important;
    }
    .sidebar-menu li > ul li a {
        padding: 7px 14px 7px 32px !important;
        font-size: 0.78rem !important;
        color: rgba(255,255,255,0.68) !important;
    }
    .sidebar-menu li > ul li a:hover {
        background: rgba(255,255,255,0.12) !important;
        color: #fff !important;
    }

    /* Badge */
    .sidebar-menu li a .badge {
        background: linear-gradient(135deg, #22c55e, #16a34a) !important;
        font-size: 0.62rem !important;
        padding: 2px 7px !important;
        border-radius: 40px !important;
    }

    /* Menu text */
    .sidebar-menu li a .menu-text {
        color: inherit !important;
    }

    /* Toggle button */
    .sidebar .sidebar-toggle-btn {
        color: rgba(255,255,255,0.7) !important;
    }
    .sidebar .sidebar-toggle-btn:hover {
        color: #fff !important;
    }
</style>

<script type='text/javascript'>
    feather.replace();
</script>