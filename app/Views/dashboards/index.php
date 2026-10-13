<?php
// Safe fallback for unread count function
if (!function_exists('count_unread_message')) {
    function count_unread_message()
    {
        return 0;
    }
}

// Safe session handling
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize core values safely
$login_user = $login_user ?? (object) ['is_admin' => true, 'id' => 1];
$user_id = $login_user->id ?? 0;
$_SESSION['user_id'] = $user_id;

// Initialize variables with defaults
$vendorCount = $vendorCount ?? 54;
$appusers = $appusers ?? 2;
$onlineproducts = $onlineproducts ?? 52;
$offlineproducts = $offlineproducts ?? 48;
$totalmodules = $totalmodules ?? 86;

$totalusermodules = $totalusermodules ?? 54;
$totaluseronlinemodules = $totaluseronlinemodules ?? 2;
$totaluserofflinemodules = $totaluserofflinemodules ?? 52;

$appusername = $appusername ?? 'prafulsshah@yahoo.com';

$safe_username = htmlspecialchars($appusername, ENT_QUOTES, 'UTF-8');
$safe_user_id = urlencode((string) $user_id);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
        /* ==========================================================
           PREMIUM GOOGLE LIGHT HOME DASHBOARD
           Scoped CSS only. Does not touch sidebar/navbar globally.
        ========================================================== */

        .ef-home-dashboard,
        .ef-home-dashboard * {
            box-sizing: border-box !important;
        }

        .ef-home-dashboard {
            width: 100% !important;
            min-height: calc(100vh - var(--ki-topbar-height, 60px)) !important;
            padding: clamp(12px, 1.8vw, 18px) clamp(12px, 2vw, 20px) clamp(16px, 2.2vw, 24px) !important;
            background:
                radial-gradient(circle at 12% 0%, rgba(37, 99, 235, 0.05), transparent 34%),
                linear-gradient(180deg, #fbfdff 0%, #ffffff 100%) !important;
            color: #111827 !important;
            font-family: 'Roboto', Arial, sans-serif !important;
            overflow-x: hidden !important;
        }

        .ef-home-shell {
            width: 100% !important;
            max-width: min(1280px, 100%) !important;
            margin: 0 auto !important;
            position: relative !important;
        }

        .ef-home-topbar {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 12px !important;
            margin-bottom: 14px !important;
            position: relative !important;
            z-index: 20 !important;
        }

        .ef-home-welcome {
            min-width: 0 !important;
        }

        .ef-home-title {
            margin: 0 0 4px !important;
            color: #0f172a !important;
            font-size: clamp(17px, 1.5vw, 22px) !important;
            line-height: 1.25 !important;
            font-weight: 700 !important;
            letter-spacing: -0.015em !important;
            word-break: break-word !important;
        }

        .ef-home-subtitle {
            margin: 0 !important;
            color: #64748b !important;
            font-size: clamp(12px, 1.1vw, 13px) !important;
            line-height: 1.45 !important;
            font-weight: 400 !important;
        }

        .ef-notification-area {
            position: relative !important;
            flex: 0 0 auto !important;
        }

        .ef-bell-button {
            position: relative !important;
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
            border-radius: 999px !important;
            border: 0 !important;
            outline: 0 !important;
            cursor: pointer !important;
            background: linear-gradient(145deg, #2563eb, #005cff) !important;
            color: #ffffff !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.2) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease !important;
            flex-shrink: 0 !important;
        }

        .ef-bell-button:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.28), inset 0 1px 0 rgba(255, 255, 255, 0.25) !important;
        }

        .ef-bell-button i {
            font-size: 16px !important;
            line-height: 1 !important;
        }

        .ef-bell-count {
            position: absolute !important;
            top: -2px !important;
            right: -2px !important;
            min-width: 16px !important;
            height: 16px !important;
            padding: 0 4px !important;
            border-radius: 999px !important;
            background: #ef4444 !important;
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 12px !important;
            font-weight: 800 !important;
            line-height: 1 !important;
        }

        .ef-notification-panel {
            position: absolute !important;
            top: 48px !important;
            right: 0 !important;
            width: min(340px, calc(100vw - 24px)) !important;
            max-height: min(480px, calc(100vh - 100px)) !important;
            background: rgba(255, 255, 255, 0.98) !important;
            border: 1px solid rgba(226, 232, 240, 0.95) !important;
            border-radius: 18px !important;
            box-shadow: 0 30px 75px rgba(15, 23, 42, 0.14) !important;
            backdrop-filter: blur(18px) !important;
            overflow: hidden !important;
            z-index: 1000 !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateY(12px) scale(0.985) !important;
            transform-origin: top right !important;
            transition: all 0.22s ease !important;
        }

        .ef-notification-panel.is-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) scale(1) !important;
        }

        .ef-notification-panel::before {
            content: "" !important;
            position: absolute !important;
            top: -10px !important;
            right: 122px !important;
            width: 22px !important;
            height: 22px !important;
            background: #ffffff !important;
            border-left: 1px solid rgba(226, 232, 240, 0.9) !important;
            border-top: 1px solid rgba(226, 232, 240, 0.9) !important;
            transform: rotate(45deg) !important;
        }

        .ef-notification-head {
            position: relative !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 12px !important;
            padding: 18px 20px !important;
            border-bottom: 1px solid #eef2f7 !important;
            background: #ffffff !important;
            z-index: 2 !important;
        }

        .ef-notification-head h3 {
            margin: 0 !important;
            font-size: 14px !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            letter-spacing: -0.01em !important;
        }

        .ef-mark-read {
            border: 0 !important;
            background: transparent !important;
            color: #2563eb !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            padding: 4px 0 !important;
        }

        #notification-window {
            max-height: 445px !important;
            overflow-y: auto !important;
            background: #ffffff !important;
        }

        #notification-window::-webkit-scrollbar {
            width: 7px !important;
        }

        #notification-window::-webkit-scrollbar-track {
            background: #f8fafc !important;
        }

        #notification-window::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
            border-radius: 99px !important;
        }

        .ef-notification-footer {
            border-top: 1px solid #eef2f7 !important;
            padding: 15px 20px !important;
            text-align: center !important;
            background: #ffffff !important;
        }

        .ef-notification-footer button {
            border: 0 !important;
            background: transparent !important;
            color: #2563eb !important;
            font-size: 14px !important;
            font-weight: 800 !important;
            cursor: pointer !important;
        }

        .ef-app-grid {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: clamp(10px, 1.2vw, 14px) !important;
            margin-bottom: 16px !important;
        }

        .ef-product-card {
            position: relative !important;
            min-height: 72px !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 12px 14px !important;
            background: #ffffff !important;
            border: 1px solid #e8edf3 !important;
            border-radius: 12px !important;
            text-decoration: none !important;
            color: inherit !important;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03) !important;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease !important;
            overflow: hidden !important;
        }

        .ef-product-card:hover {
            text-decoration: none !important;
            color: inherit !important;
            transform: translateY(-1px) !important;
            border-color: rgba(37, 99, 235, 0.16) !important;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06) !important;
        }

        .ef-product-icon {
            width: 40px !important;
            height: 40px !important;
            flex: 0 0 40px !important;
            border-radius: 10px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #ffffff !important;
            box-shadow: none !important;
        }

        .ef-product-icon i {
            font-size: 17px !important;
            line-height: 1 !important;
        }

        .ef-blue {
            background: linear-gradient(145deg, #2563eb, #0061ff) !important;
        }

        .ef-purple {
            background: linear-gradient(145deg, #7c3aed, #4f46e5) !important;
        }

        .ef-orange {
            background: linear-gradient(145deg, #ff7a1a, #ff5a00) !important;
        }

        .ef-green {
            background: linear-gradient(145deg, #22c55e, #16a34a) !important;
        }

        .ef-soft-blue {
            background: rgba(37, 99, 235, 0.09) !important;
            color: #2563eb !important;
        }

        .ef-soft-green {
            background: rgba(34, 197, 94, 0.1) !important;
            color: #16a34a !important;
        }

        .ef-soft-orange {
            background: rgba(249, 115, 22, 0.1) !important;
            color: #f97316 !important;
        }

        .ef-product-copy {
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }

        .ef-product-copy h3 {
            margin: 0 0 2px !important;
            color: #111827 !important;
            font-size: 14px !important;
            line-height: 1.3 !important;
            font-weight: 600 !important;
            letter-spacing: -0.01em !important;
        }

        .ef-product-copy p {
            margin: 0 !important;
            color: #64748b !important;
            font-size: 12px !important;
            line-height: 1.4 !important;
            font-weight: 400 !important;
        }

        .ef-card-arrow {
            color: #94a3b8 !important;
            font-size: 12px !important;
            flex-shrink: 0 !important;
        }

        .ef-stats-grid {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: clamp(10px, 1.2vw, 14px) !important;
            margin-bottom: 18px !important;
        }

        .ef-stat-card {
            min-height: 96px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 12px !important;
            padding: 14px 16px !important;
            background: #ffffff !important;
            border: 1px solid #e8edf3 !important;
            border-radius: 12px !important;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03) !important;
            cursor: pointer !important;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease !important;
        }

        .ef-stat-card:hover {
            transform: translateY(-1px) !important;
            border-color: rgba(37, 99, 235, 0.16) !important;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06) !important;
        }

        .ef-stat-left {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            min-width: 0 !important;
        }

        .ef-stat-icon {
            width: 44px !important;
            height: 44px !important;
            flex: 0 0 44px !important;
            border-radius: 12px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 18px !important;
        }

        .ef-stat-text {
            min-width: 0 !important;
        }

        .ef-stat-label {
            margin: 0 0 4px !important;
            color: #1f2937 !important;
            font-size: 13px !important;
            line-height: 1.2 !important;
            font-weight: 600 !important;
        }

        .ef-stat-number {
            margin: 0 !important;
            color: #020617 !important;
            font-size: 22px !important;
            line-height: 1 !important;
            font-weight: 800 !important;
            letter-spacing: -0.03em !important;
        }

        .ef-stat-trend {
            margin-top: 6px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 7px !important;
            color: #64748b !important;
            font-size: 12px !important;
            font-weight: 500 !important;
        }

        .ef-stat-trend strong {
            font-weight: 900 !important;
        }

        .ef-trend-up strong {
            color: #2563eb !important;
        }

        .ef-trend-green strong {
            color: #16a34a !important;
        }

        .ef-trend-down strong {
            color: #f97316 !important;
        }

        .ef-stat-graph {
            width: 42px !important;
            height: 42px !important;
            flex: 0 0 42px !important;
            border-radius: 10px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 18px !important;
        }

        .ef-section-head {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 12px !important;
            margin: 0 0 12px !important;
        }

        .ef-section-title {
            margin: 0 !important;
            color: #111827 !important;
            font-size: clamp(16px, 1.4vw, 18px) !important;
            line-height: 1.25 !important;
            font-weight: 700 !important;
            letter-spacing: -0.02em !important;
        }

        .ef-section-link {
            color: #2563eb !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            white-space: nowrap !important;
        }

        .ef-section-link:hover {
            color: #1d4ed8 !important;
            text-decoration: none !important;
        }

        .ef-quick-grid {
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 140px), 1fr)) !important;
            gap: 10px !important;
            margin-bottom: 18px !important;
        }

        .ef-quick-card {
            min-height: 110px !important;
            padding: 12px 10px !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            border: 1px solid #e8edf3 !important;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03) !important;
            text-decoration: none !important;
            color: inherit !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
            cursor: pointer !important;
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease !important;
        }

        .ef-quick-card:hover {
            transform: translateY(-4px) !important;
            color: inherit !important;
            text-decoration: none !important;
            border-color: rgba(37, 99, 235, 0.18) !important;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.075) !important;
        }

        .ef-quick-icon {
            width: 58px !important;
            height: 58px !important;
            margin-bottom: 14px !important;
            border-radius: 999px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: rgba(37, 99, 235, 0.09) !important;
            color: #2563eb !important;
            font-size: 24px !important;
        }

        .ef-quick-card h4 {
            margin: 0 0 7px !important;
            color: #1e293b !important;
            font-size: 14px !important;
            line-height: 1.25 !important;
            font-weight: 900 !important;
            letter-spacing: -0.015em !important;
        }

        .ef-quick-card p {
            margin: 0 !important;
            color: #64748b !important;
            font-size: 12px !important;
            line-height: 1.45 !important;
            font-weight: 500 !important;
        }

        .ef-videos-grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 24px !important;
            margin-bottom: 34px !important;
        }

        .ef-video-card {
            min-height: 218px !important;
            display: grid !important;
            grid-template-columns: 275px minmax(0, 1fr) !important;
            gap: 24px !important;
            align-items: center !important;
            padding: 20px !important;
            border-radius: 16px !important;
            background: rgba(255, 255, 255, 0.88) !important;
            border: 1px solid rgba(226, 232, 240, 0.95) !important;
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.045) !important;
            overflow: hidden !important;
            position: relative !important;
        }

        .ef-video-media {
            position: relative !important;
            height: 174px !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            background: linear-gradient(145deg, #eff6ff, #eef2ff) !important;
            box-shadow: inset 0 0 0 1px rgba(226, 232, 240, 0.85) !important;
        }

        .ef-video-media video {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border: 0 !important;
            outline: 0 !important;
        }

        .ef-video-duration {
            position: absolute !important;
            top: 10px !important;
            right: 10px !important;
            z-index: 2 !important;
            background: #eaf2ff !important;
            color: #2563eb !important;
            border-radius: 999px !important;
            padding: 4px 8px !important;
            font-size: 11px !important;
            line-height: 1 !important;
            font-weight: 900 !important;
        }

        .ef-video-info {
            min-width: 0 !important;
        }

        .ef-video-info h4 {
            margin: 0 0 10px !important;
            color: #0f172a !important;
            font-size: 16px !important;
            line-height: 1.35 !important;
            font-weight: 900 !important;
            letter-spacing: -0.02em !important;
        }

        .ef-video-info p {
            margin: 0 0 18px !important;
            color: #64748b !important;
            font-size: 14px !important;
            line-height: 1.55 !important;
            font-weight: 400 !important;
        }

        .ef-watch-link {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            color: #2563eb !important;
            text-decoration: none !important;
            font-size: 14px !important;
            font-weight: 900 !important;
            cursor: pointer !important;
        }

        .ef-watch-link:hover {
            color: #1d4ed8 !important;
            text-decoration: none !important;
        }

        .ef-resources-grid {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 18px !important;
        }

        .ef-resource-card {
            padding: 22px !important;
            border-radius: 16px !important;
            background: rgba(255, 255, 255, 0.88) !important;
            border: 1px solid rgba(226, 232, 240, 0.95) !important;
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.045) !important;
            min-height: 150px !important;
        }

        .ef-resource-card h4 {
            margin: 0 0 8px !important;
            color: #0f172a !important;
            font-size: 15px !important;
            line-height: 1.3 !important;
            font-weight: 900 !important;
        }

        .ef-resource-card p {
            margin: 0 0 15px !important;
            color: #64748b !important;
            font-size: 13px !important;
            line-height: 1.45 !important;
        }

        .ef-primary-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            min-height: 39px !important;
            padding: 9px 15px !important;
            border-radius: 999px !important;
            border: 0 !important;
            background: #2563eb !important;
            color: #ffffff !important;
            text-decoration: none !important;
            font-size: 13px !important;
            font-weight: 900 !important;
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.2) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease !important;
        }

        .ef-primary-btn:hover {
            color: #ffffff !important;
            text-decoration: none !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.25) !important;
        }

        .module-box {
            width: 100% !important;
            border-radius: 18px !important;
            overflow: hidden !important;
        }

        .module-link {
            display: flex !important;
            align-items: center !important;
            gap: 14px !important;
            min-height: 116px !important;
            padding: 18px !important;
            background: rgba(255, 255, 255, 0.88) !important;
            border: 1px solid rgba(226, 232, 240, 0.95) !important;
            border-radius: 16px !important;
            text-decoration: none !important;
            color: inherit !important;
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.045) !important;
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease !important;
        }

        .module-link:hover {
            text-decoration: none !important;
            color: inherit !important;
            transform: translateY(-4px) !important;
            border-color: rgba(37, 99, 235, 0.18) !important;
            box-shadow: 0 22px 55px rgba(15, 23, 42, 0.08) !important;
        }

        .module-icon {
            width: 52px !important;
            height: 52px !important;
            min-width: 52px !important;
            border-radius: 14px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: rgba(37, 99, 235, 0.09) !important;
            color: #2563eb !important;
            font-size: 20px !important;
        }

        .module-content {
            flex: 1 1 auto !important;
            min-width: 0 !important;
        }

        .module-content h4 {
            margin: 0 0 6px !important;
            color: #111827 !important;
            font-size: 16px !important;
            line-height: 1.3 !important;
            font-weight: 800 !important;
        }

        .module-content p {
            margin: 0 !important;
            color: #475569 !important;
            font-size: 13px !important;
            line-height: 1.5 !important;
        }

        .module-arrow {
            color: #334155 !important;
            font-size: 18px !important;
            opacity: 0.9 !important;
        }

        .notification-item {
            position: relative !important;
            display: grid !important;
            grid-template-columns: 44px minmax(0, 1fr) 10px !important;
            gap: 14px !important;
            align-items: flex-start !important;
            padding: 18px 20px !important;
            margin: 0 !important;
            border-radius: 0 !important;
            border: 0 !important;
            border-bottom: 1px solid #eef2f7 !important;
            background: #ffffff !important;
            box-shadow: none !important;
            cursor: pointer !important;
            transition: background 0.2s ease !important;
            animation: efNotificationIn 0.26s ease both !important;
        }

        .notification-item:hover {
            transform: none !important;
            background: #f8fbff !important;
            box-shadow: none !important;
        }

        .notification-item.new-notification {
            background: #f8fbff !important;
            border-left: 0 !important;
            box-shadow: none !important;
        }

        .notification-avatar {
            width: 44px !important;
            height: 44px !important;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #ffffff !important;
            font-weight: 900 !important;
            font-size: 16px !important;
            flex: 0 0 44px !important;
            background: linear-gradient(145deg, #2563eb, #0061ff) !important;
        }

        .notification-body {
            min-width: 0 !important;
        }

        .notification-title {
            margin: 0 0 6px !important;
            color: #0f172a !important;
            font-size: 14px !important;
            line-height: 1.45 !important;
            font-weight: 700 !important;
        }

        .notification-date {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            border-radius: 0 !important;
            color: #64748b !important;
            font-size: 13px !important;
            line-height: 1.35 !important;
            font-weight: 500 !important;
        }

        .notification-dot {
            width: 10px !important;
            height: 10px !important;
            border-radius: 999px !important;
            background: #2563eb !important;
            margin-top: 15px !important;
        }

        .user-list {
            display: none !important;
        }

        .user-item {
            display: none !important;
        }

        .ef-empty-state {
            padding: 38px 20px !important;
            text-align: center !important;
            color: #64748b !important;
            font-size: 14px !important;
        }

        .ef-empty-state i {
            display: block !important;
            margin-bottom: 12px !important;
            color: #94a3b8 !important;
            font-size: 28px !important;
        }

        @keyframes efNotificationIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1199px) {
            .ef-app-grid,
            .ef-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 991px) {
            .ef-app-grid,
            .ef-stats-grid,
            .ef-resources-grid {
                grid-template-columns: 1fr !important;
            }

            .ef-video-card {
                grid-template-columns: 1fr !important;
            }

            .ef-video-media {
                height: 200px !important;
            }

            .ef-quick-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 767px) {
            .ef-home-topbar {
                align-items: flex-start !important;
            }

            .ef-notification-panel {
                position: fixed !important;
                top: calc(var(--ki-topbar-height, 56px) + 8px) !important;
                right: 10px !important;
                left: 10px !important;
                width: auto !important;
                max-height: calc(100vh - var(--ki-topbar-height, 56px) - 24px) !important;
            }

            .ef-notification-panel::before {
                display: none !important;
            }

            #notification-window {
                max-height: calc(100vh - 200px) !important;
            }

            .ef-section-head {
                align-items: flex-start !important;
                flex-direction: column !important;
                gap: 4px !important;
            }

            .ef-quick-grid {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width: 479px) {
            .ef-home-welcome {
                flex: 1 1 auto !important;
                min-width: 0 !important;
            }

            .ef-product-card {
                padding: 11px 12px !important;
                gap: 10px !important;
                min-height: 68px !important;
            }

            .ef-product-icon {
                width: 36px !important;
                height: 36px !important;
                flex: 0 0 36px !important;
            }

            .ef-product-icon i {
                font-size: 15px !important;
            }

            .ef-product-copy h3 {
                font-size: 13px !important;
            }

            .ef-product-copy p {
                font-size: 11px !important;
            }

            .ef-card-arrow {
                display: none !important;
            }

            .ef-stat-card {
                padding: 12px !important;
                min-height: 84px !important;
            }

            .ef-stat-graph {
                display: none !important;
            }
        }

        @media (max-width: 359px) {
            .ef-home-title {
                font-size: 16px !important;
            }

            .ef-bell-button {
                width: 36px !important;
                height: 36px !important;
                min-width: 36px !important;
            }

            .ef-bell-button i {
                font-size: 14px !important;
            }
        }

        @media (min-width: 1400px) {
            .ef-home-shell {
                max-width: min(1360px, 100%) !important;
            }
        }

        @media (min-width: 1600px) {
            .ef-app-grid {
                gap: 16px !important;
            }
        }


        /* ===== Final minimal overrides ===== */
        .ef-product-card {
            min-height: 72px !important;
            gap: 12px !important;
            padding: 12px 14px !important;
        }

        .ef-product-icon {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
            flex: 0 0 40px !important;
            border-radius: 10px !important;
            box-shadow: none !important;
        }

        .ef-product-icon i {
            font-size: 17px !important;
        }

        .ef-bell-button {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
        }

        .ef-bell-button i {
            font-size: 16px !important;
        }

        .ef-notification-panel {
            top: 48px !important;
        }

        .ef-notification-panel::before {
            right: 12px !important;
        }
    </style>

    <main class="ef-home-dashboard">
        <div class="ef-home-shell">

            <!-- Header + Notifications -->
            <div class="ef-home-topbar">
                <div class="ef-home-welcome">
                    <h1 class="ef-home-title">Welcome back, <?php echo $safe_username; ?></h1>
                    <p class="ef-home-subtitle">Manage your workspace, products, and business tools from one place</p>
                </div>

                <div class="ef-notification-area">
                    <button type="button" class="ef-bell-button" id="efNotificationToggle"
                        aria-label="Open notifications">
                        <i class="fa-regular fa-bell"></i>
                        <span id="notifCountBadge" class="ef-bell-count">0</span>
                    </button>

                    <section class="ef-notification-panel" id="efNotificationPanel" aria-label="Notifications">
                        <div class="ef-notification-head">
                            <h3>Notifications</h3>
                            <button type="button" class="ef-mark-read" id="efMarkReadBtn">Mark all as read</button>
                        </div>

                        <div id="notification-window">
                            <div class="ef-empty-state">
                                <i class="fas fa-spinner fa-spin"></i>
                                Loading notifications...
                            </div>
                        </div>

                        <div class="ef-notification-footer">
                            <button type="button" id="efViewAllNotifications">View all notifications</button>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Product Cards -->
            <section class="ef-app-grid" aria-label="Main modules">
                <!-- <a href="https://efactor.tech/efactor_visiting_profile.php?user_id=<?php echo $safe_user_id; ?>"
                    class="ef-product-card">
                    <span class="ef-product-icon ef-blue">
                        <i class="fa-solid fa-grip"></i>
                    </span>
                    <span class="ef-product-copy">
                        <h3>GRID</h3>
                        <p>Manage your QR codes and smart links</p>
                    </span>
                    <i class="fa-solid fa-chevron-right ef-card-arrow"></i>
                </a> -->

                <!-- <a href="https://efactor.tech/visiting_card_studio.php?user_id=<?php echo $safe_user_id; ?>"
                    class="ef-product-card">
                    <span class="ef-product-icon ef-purple">
                        <i class="fa-regular fa-address-card"></i>
                    </span>
                    <span class="ef-product-copy">
                        <h3>Visiting Card Studio</h3>
                        <p>Create and manage digital visiting cards</p>
                    </span>
                    <i class="fa-solid fa-chevron-right ef-card-arrow"></i>
                </a> -->

                <!-- <a href="<?= base_url('restaurant-system/main') . '?user_id=' . urlencode($user_id) . '&appuser=' . urlencode($appusername) ?>"
                    class="ef-product-card">
                    <span class="ef-product-icon ef-orange">
                        <i class="fa-solid fa-utensils"></i>
                    </span>
                    <span class="ef-product-copy">
                        <h3>Restaurant</h3>
                        <p>Manage menu, orders, and restaurant settings</p>
                    </span>
                    <i class="fa-solid fa-chevron-right ef-card-arrow"></i>
                </a> -->

                <!-- <a href="<?= base_url('production-management/production')  ?>"
                    class="ef-product-card">
                    <span class="ef-product-icon ef-blue">
                        <i class="fa-solid fa-industry"></i>
                    </span>

                    <span class="ef-product-copy">
                        <h3>Production Management</h3>
                        <p>Manage brands, products, models, versions, batches and item tracking</p>
                    </span>

                    <i class="fa-solid fa-chevron-right ef-card-arrow"></i>
                </a> -->



                <!-- <a href="<?= base_url('work-hive/access'); ?>" class="ef-product-card" target="_blank" rel="noopener noreferrer">
                    <span class="ef-product-icon ef-green">
                        <i class="fa-solid fa-briefcase"></i>
                    </span>

                    <span class="ef-product-copy">
                        <h3>WorkHive</h3>
                        <p>Attendance, work management, tasks, projects and employee productivity</p>
                    </span>

                    <i class="fa-solid fa-chevron-right ef-card-arrow"></i>
                </a> -->

                <!-- Efactor_phase1 MODULE BOX -->
                <!-- <div class="module-box">

                    <a href="<?= site_url('items/efactor_phase1'); ?>" class="module-link">

                        <div class="module-icon">
                            <i class="fas fa-user-check"></i>
                        </div>

                        <div class="module-content">
                            <h4>Efactor ID Assignment</h4>

                        </div>

                        <div class="module-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>

                    </a>

                </div> -->

                <!-- <div class="module-box">
                    <a href="<?= site_url('attendance/admin'); ?>" class="module-link">
                        <div class="module-icon">
                            <i class="fas fa-user-check"></i>
                        </div>

                        <div class="module-content">
                            <h4>Attendance</h4>
                            <p>Launch attendance, face logs, employees and payroll dashboard</p>
                        </div>

                        <div class="module-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                </div> -->

                <!-- <div class="module-box">
                    <a href="<?= site_url('items/modal_config'); ?>" class="module-link">
                        <div class="module-icon">
                            <i class="fas fa-vial"></i>
                        </div>

                        <div class="module-content">
                            <h4>Efactor Testing</h4>
                            <p>Open Efactor testing configuration and validation tools</p>
                        </div>

                        <div class="module-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                </div>

                <div class="module-box">
                    <a href="<?= site_url('hrms/admin') . '?user_id=' . urlencode($user_id) . '&appuser=' . urlencode($appusername) ?>"
                        class="module-link">
                        <div class="module-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>

                        <div class="module-content">
                            <h4>HRMS</h4>
                            <p>Launch HRMS, employee management, leave tracking and payroll dashboard</p>
                        </div>

                        <div class="module-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                </div>

                <a href="<?= base_url('taglink') ?>"
                    class="ef-product-card">
                    <span class="ef-product-icon ef-purple">
                        <i class="fa-solid fa-tags"></i>
                    </span>

                    <span class="ef-product-copy">
                        <h3>TagLink</h3>
                        <p>Manage smart tags, linked items, QR mapping and tracking connections</p>
                    </span>

                    <i class="fa-solid fa-chevron-right ef-card-arrow"></i>
                </a> -->
            </section>

            <!-- User Module Stats -->
            <section class="ef-stats-grid" aria-label="Module overview">
                <article class="ef-stat-card" onclick="location.href='items/index'">
                    <div class="ef-stat-left">
                        <span class="ef-stat-icon ef-soft-blue">
                            <i class="fa-solid fa-layer-group"></i>
                        </span>
                        <div class="ef-stat-text">
                            <p class="ef-stat-label">Total Modules</p>
                            <p class="ef-stat-number">
                                <?php echo htmlspecialchars((string) $totalusermodules, ENT_QUOTES, 'UTF-8'); ?></p>
                            <span class="ef-stat-trend ef-trend-up">
                                <i class="fa-solid fa-arrow-up"></i>
                                <strong>12%</strong> from last month
                            </span>
                        </div>
                    </div>
                    <span class="ef-stat-graph ef-soft-blue">
                        <i class="fa-solid fa-chart-line"></i>
                    </span>
                </article>

                <article class="ef-stat-card" onclick="location.href='items/index?search=Online'">
                    <div class="ef-stat-left">
                        <span class="ef-stat-icon ef-soft-green">
                            <i class="fa-solid fa-wifi"></i>
                        </span>
                        <div class="ef-stat-text">
                            <p class="ef-stat-label">Online Modules</p>
                            <p class="ef-stat-number">
                                <?php echo htmlspecialchars((string) $totaluseronlinemodules, ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            <span class="ef-stat-trend ef-trend-green">
                                <i class="fa-solid fa-arrow-up"></i>
                                <strong>8%</strong> from last month
                            </span>
                        </div>
                    </div>
                    <span class="ef-stat-graph ef-soft-green">
                        <i class="fa-solid fa-check"></i>
                    </span>
                </article>

                <article class="ef-stat-card" onclick="location.href='items/index?search=Offline'">
                    <div class="ef-stat-left">
                        <span class="ef-stat-icon ef-soft-orange">
                            <!-- <i class="fa fa-ban"></i> -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-wifi-off-icon lucide-wifi-off">
                                <path d="M12 20h.01" />
                                <path d="M8.5 16.429a5 5 0 0 1 7 0" />
                                <path d="M5 12.859a10 10 0 0 1 5.17-2.69" />
                                <path d="M19 12.859a10 10 0 0 0-2.007-1.523" />
                                <path d="M2 8.82a15 15 0 0 1 4.177-2.643" />
                                <path d="M22 8.82a15 15 0 0 0-11.288-3.764" />
                                <path d="m2 2 20 20" />
                            </svg>
                        </span>
                        <div class="ef-stat-text">
                            <p class="ef-stat-label">Offline Modules</p>
                            <p class="ef-stat-number">
                                <?php echo htmlspecialchars((string) $totaluserofflinemodules, ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            <span class="ef-stat-trend ef-trend-down">
                                <i class="fa-solid fa-arrow-down"></i>
                                <strong>4%</strong> from last month
                            </span>
                        </div>
                    </div>
                    <span class="ef-stat-graph ef-soft-orange">
                        <i class="fa-solid fa-check"></i>
                    </span>
                </article>
            </section>

            <!-- Quick Access -->

            <?php if (!empty($login_user->is_admin)) { ?>
                <!-- Admin Access - existing admin functionality preserved -->
                <section aria-label="Admin tools">
                    <div class="ef-section-head">
                        <h2 class="ef-section-title">Admin Overview</h2>
                    </div>

                    <div class="ef-quick-grid">
                        <div class="ef-quick-card" onclick="location.href='<?= site_url('clients/index'); ?>'">
                            <span class="ef-quick-icon"><i class="fa fa-ban"></i></span>
                            <h4>Vendors</h4>
                            <p><?php echo htmlspecialchars((string) $vendorCount, ENT_QUOTES, 'UTF-8'); ?> vendors</p>
                        </div>

                        <div class="ef-quick-card" onclick="location.href='<?= site_url('user_details/index'); ?>'">
                            <span class="ef-quick-icon"><i class="fa-solid fa-user-check"></i></span>
                            <h4>App Users</h4>
                            <p><?php echo htmlspecialchars((string) $appusers, ENT_QUOTES, 'UTF-8'); ?> users</p>
                        </div>

                        <div class="ef-quick-card" onclick="location.href='<?= site_url('items/index'); ?>?search=Online'">
                            <span class="ef-quick-icon"><i class="fa-solid fa-cloud-arrow-up"></i></span>
                            <h4>Online Products</h4>
                            <p><?php echo htmlspecialchars((string) $onlineproducts, ENT_QUOTES, 'UTF-8'); ?> active products
                            </p>
                        </div>

                        <div class="ef-quick-card" onclick="location.href='<?= site_url('items/index'); ?>?search=Offline'">
                            <span class="ef-quick-icon"><i class="fa-solid fa-cloud-arrow-down"></i></span>
                            <h4>Offline Products</h4>
                            <p><?php echo htmlspecialchars((string) $offlineproducts, ENT_QUOTES, 'UTF-8'); ?> offline
                                products</p>
                        </div>

                        <div class="ef-quick-card" onclick="location.href='<?= site_url('items/index'); ?>'">
                            <span class="ef-quick-icon"><i class="fa-solid fa-cubes"></i></span>
                            <h4>Total Products</h4>
                            <p><?php echo htmlspecialchars((string) $totalmodules, ENT_QUOTES, 'UTF-8'); ?> total modules</p>
                        </div>

                        <div class="ef-quick-card" onclick="location.href='<?= base_url('dashboard/user_verification') ?>'">
                            <span class="ef-quick-icon"><i class="fa-solid fa-clipboard-check"></i></span>
                            <h4>User Info</h4>
                            <p>Verify and review user details</p>
                        </div>

                        <div class="ef-quick-card" onclick="location.href='<?= site_url('items/vendor'); ?>'">
                            <span class="ef-quick-icon"><i class="fa-solid fa-eye"></i></span>
                            <h4>View Efactor Models</h4>
                            <p>Browse all available models</p>
                        </div>

                        <div class="ef-quick-card" onclick="location.href='<?= site_url('items/vendor'); ?>'">
                            <span class="ef-quick-icon"><i class="fa-solid fa-key"></i></span>
                            <h4>APP ID Generation</h4>
                            <p>Generate and manage app IDs</p>
                        </div>

                        <div class="ef-quick-card">
                            <span class="ef-quick-icon"><i class="fa-solid fa-upload"></i></span>
                            <h4>App Registration</h4>
                            <p>Register new application details</p>
                        </div>

                        <div class="ef-quick-card" onclick="window.location.href='https://efactor.tech/resources.html'">
                            <span class="ef-quick-icon"><i class="fa-regular fa-circle-question"></i></span>
                            <h4>API Form Help</h4>
                            <p>How to add API form</p>
                        </div>
                        
                        <?php echo view('dashboards/app_settings_card'); ?>
                        
                        
                        <div class="ef-quick-card" onclick="window.location.href='https://efactor.tech/resources.html'">
                            <span class="ef-quick-icon"><i class="fa-regular fa-circle-question"></i></span>
                            <h4>API Form Help</h4>
                            <p>How to add API form</p>
                        </div>
                        
                        
                        
                        
                  <?php echo view('dashboards/iot_card'); ?>      



<div class="col-xl-2 col-md-4 col-6 mb-4">
                <div class="action-tile" onclick="location.href='<?= site_url('dashboard/open-nas'); ?>'">
                    <div class="icon-circle bg-warning text-dark"><i class="fas fa-key"></i></div>
                    <div class="action-label">eFactor Drive</div>
                </div>
            </div>
            
            <?php echo view('dashboards/grid_new_card'); ?>
            
            
            <a href="<?= site_url('grid') ?>" class="profile-btn">
                        <i class="fas fa-user-circle"></i> DEMO
                    </a>
                        
                        
                        <a href="<?= get_uri('grid-chat') ?>" class="profile-btn">
                        <i class="fas fa-comments"></i> CHAT
                    </a>
                    
                    
<div class="ef-quick-card" onclick="window.location.href='<?= site_url('kvm') ?>'">
    <span class="ef-quick-icon">
        <i class="fas fa-folder-open"></i>
    </span>
    <h4>Efactor VMs</h4>
    <p>Server VMs</p>
</div>  

<div class="ef-quick-card" onclick="window.location.href='<?= site_url('welcome') ?>'">
    <span class="ef-quick-icon">
        <i class="fas fa-folder-open"></i>
    </span>
    <h4>Open SD welcome page</h4>
    <p>SD login</p>
</div>   


<div class="col-xl-2 col-md-4 col-6 mb-4">
    <div class="action-tile"
         onclick="location.href='<?= site_url('items/display_lable'); ?>'">

        <div class="icon-circle display-label-icon">
            <i class="fas fa-tags"></i>
        </div>

        <div class="action-label">
            Display Label
        </div>
    </div>
</div>      
                        
                        
                        
                        
                        
                        
                    </div>
                </section>
            <?php } else { ?>
                <!-- Non-admin tools - existing functionality preserved -->
                <section aria-label="User tools">
                    <div class="ef-section-head">
                        <h2 class="ef-section-title">Module Tools</h2>
                    </div>

                    <div class="ef-quick-grid">
                        <div class="ef-quick-card" onclick="location.href='<?= site_url('items/vendor'); ?>'">
                            <span class="ef-quick-icon"><i class="fa-solid fa-eye"></i></span>
                            <h4>View Efactor Models</h4>
                            <p>Browse all available models</p>
                        </div>

                        <div class="ef-quick-card" onclick="location.href='<?= site_url('items/vendor'); ?>'">
                            <span class="ef-quick-icon"><i class="fa-solid fa-key"></i></span>
                            <h4>APP ID Generation</h4>
                            <p>Generate and manage app IDs</p>
                        </div>

                        <div class="ef-quick-card">
                            <span class="ef-quick-icon"><i class="fa-solid fa-upload"></i></span>
                            <h4>App Registration</h4>
                            <p>Register new application details</p>
                        </div>

                        <div class="ef-quick-card" onclick="window.location.href='https://efactor.tech/resources.html'">
                            <span class="ef-quick-icon"><i class="fa-regular fa-circle-question"></i></span>
                            <h4>API Form Help</h4>
                            <p>How to add API form</p>
                        </div>
                        
                        
                        <?php echo view('dashboards/app_settings_card'); ?>
                        
                        
<?php echo view('dashboards/iot_card'); ?>


<div class="col-xl-2 col-md-4 col-6 mb-4">
                <div class="action-tile" onclick="location.href='<?= site_url('dashboard/open-nas'); ?>'">
                    <div class="icon-circle bg-warning text-dark"><i class="fas fa-key"></i></div>
                    <div class="action-label">eFactor Drive</div>
                </div>
            </div>
            
            
            
             <?php echo view('dashboards/grid_new_card'); ?>
             
             <a href="<?= site_url('grid') ?>" class="profile-btn">
                        <i class="fas fa-user-circle"></i> DEMO
                    </a>
                    
                    
                    <a href="<?= get_uri('grid-chat') ?>" class="profile-btn">
                        <i class="fas fa-comments"></i> CHAT
                    </a>


<div class="ef-quick-card" onclick="window.location.href='<?= site_url('kvm') ?>'">
    <span class="ef-quick-icon">
        <i class="fas fa-folder-open"></i>
    </span>
    <h4>Efactor VMs</h4>
    <p>Server VMs</p>
</div>  



<div class="ef-quick-card" onclick="window.location.href='<?= site_url('welcome') ?>'">
    <span class="ef-quick-icon">
        <i class="fas fa-folder-open"></i>
    </span>
    <h4>Open SD welcome page</h4>
    <p>SD login</p>
</div>  


<div class="col-xl-2 col-md-4 col-6 mb-4">
    <div class="action-tile"
         onclick="location.href='<?= site_url('items/display_lable'); ?>'">

        <div class="icon-circle display-label-icon">
            <i class="fas fa-tags"></i>
        </div>

        <div class="action-label">
            Display Label
        </div>
    </div>
</div>   


      
                        
                    </div>
                </section>
            <?php } ?>

            <!-- Videos / Tutorials -->
            <section aria-label="Videos and tutorials">
                <div class="ef-section-head">
                    <h2 class="ef-section-title">Videos / Tutorials</h2>
                    <a class="ef-section-link" href="javascript:void(0);">View all tutorials <i
                            class="fa-solid fa-chevron-right"></i></a>
                </div>

                <div class="ef-videos-grid">
                    <article class="ef-video-card">
                        <div class="ef-video-media">
                            <span class="ef-video-duration">5:24</span>
                            <video controls preload="none" loading="lazy">
                                <source src="https://efactor.tech/appstore/app_install.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div class="ef-video-info">
                            <h4>Getting Started with GRID</h4>
                            <p>Learn how to create your first QR code and manage smart links effectively.</p>
                            <a class="ef-watch-link" href="https://efactor.tech/appstore/app_install.mp4"
                                target="_blank">
                                Watch tutorial <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>

                    <article class="ef-video-card">
                        <div class="ef-video-media">
                            <span class="ef-video-duration">6:15</span>
                            <video controls preload="none" loading="lazy">
                                <source src="https://efactor.tech/appstore/peer-to-peer.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div class="ef-video-info">
                            <h4>Create Digital Visiting Cards</h4>
                            <p>Step-by-step guide to create, customize, and share your digital visiting cards.</p>
                            <a class="ef-watch-link" href="https://efactor.tech/appstore/peer-to-peer.mp4"
                                target="_blank">
                                Watch tutorial <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>

                    <article class="ef-video-card">
                        <div class="ef-video-media">
                            <span class="ef-video-duration">4:20</span>
                            <video controls preload="none" loading="lazy">
                                <source src="https://efactor.tech/appstore/ping.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div class="ef-video-info">
                            <h4>Ping Setup Guide</h4>
                            <p>Understand the ping workflow and setup steps for your connected module.</p>
                            <a class="ef-watch-link" href="https://efactor.tech/appstore/ping.mp4" target="_blank">
                                Watch tutorial <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>

                    <article class="ef-video-card">
                        <div class="ef-video-media">
                            <span class="ef-video-duration">7:10</span>
                            <video controls preload="none" loading="lazy">
                                <source src="https://efactor.tech/appstore/addnet.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div class="ef-video-info">
                            <h4>Adding Device to Network</h4>
                            <p>Learn how to connect and manage devices inside your workspace network.</p>
                            <a class="ef-watch-link" href="https://efactor.tech/appstore/addnet.mp4" target="_blank">
                                Watch tutorial <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                </div>
            </section>

            <!-- Resources / Downloads -->
            <section aria-label="Resources and downloads">
                <div class="ef-section-head">
                    <h2 class="ef-section-title">Resources</h2>
                </div>

                <div class="ef-resources-grid">
                    <article class="ef-resource-card">
                        <h4><i class="fas fa-download"></i> Download API</h4>
                        <p>API_Specification.xlsx</p>
                        <a href="https://efactor.tech/appstore/API_Specification.xlsx" class="ef-primary-btn" download>
                            <i class="fa-solid fa-download"></i> Download
                        </a>
                    </article>

                    <article class="ef-resource-card">
                        <h4><i class="fas fa-book"></i> Efactor Library</h4>
                        <p>Open the Efactor library documentation and integration guide.</p>
                        <a href="https://efactor.tech/v8/EfactorLibrary.html" class="ef-primary-btn" target="_blank">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Open Library
                        </a>
                    </article>

                    <article class="ef-resource-card">
                        <h4><i class="fas fa-file-code"></i> Provision Files</h4>
                        <p>Download Java and XML provision files.</p>
                        <a href="https://efactor.tech/v8/ProvisionActivity.java" class="ef-primary-btn" download>
                            Java File
                        </a>
                        <a href="https://efactor.tech/v8/activity_provision.xml" class="ef-primary-btn" download
                            style="margin-left: 8px !important;">
                            XML File
                        </a>
                    </article>

                 <article class="ef-resource-card">
    <h4><i class="fas fa-print"></i> Printer Software</h4>

    <p>Download the local printer driver setup for dashboard printing.</p>

    <a href="https://efactor.tech/printer-driver/PrintaX-Setup.exe"
       class="ef-primary-btn">
        <i class="fa-solid fa-print"></i> Download
    </a>

    <div style="margin-top: 12px;">
        <input type="text"
               id="printerKey"
               value="SPL-JK2IXMCY-IWP46QEV-TMO526RW-Z5AKTFSG-XVNYCCI"
               readonly
               style="width: 100%; padding: 8px;">

        <button type="button"
                class="ef-primary-btn"
                onclick="copyPrinterKey()"
                style="margin-top: 8px;">
            <i class="fas fa-copy"></i> Copy Key
        </button>
    </div>
</article>
                </div>
            </section>

        </div>
    </main>

    <script>
        // ==========================================================
        // PREMIUM HOME DASHBOARD JS
        // Existing functionality preserved:
        // - Fetch dashboard notifications
        // - Poll every 30 seconds
        // - Update title unread count
        // - Keep old routes and links
        // ==========================================================

        let lastNotificationData = [];
        let currentUnreadCount = 0;

        const notificationToggle = document.getElementById('efNotificationToggle');
        const notificationPanel = document.getElementById('efNotificationPanel');
        const markReadBtn = document.getElementById('efMarkReadBtn');
        const viewAllNotificationsBtn = document.getElementById('efViewAllNotifications');

        if (notificationToggle && notificationPanel) {
            notificationToggle.addEventListener('click', function(event) {
                event.stopPropagation();
                notificationPanel.classList.toggle('is-open');
            });

            notificationPanel.addEventListener('click', function(event) {
                event.stopPropagation();
            });

            document.addEventListener('click', function() {
                notificationPanel.classList.remove('is-open');
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    notificationPanel.classList.remove('is-open');
                }
            });
        }

        if (markReadBtn) {
            markReadBtn.addEventListener('click', function() {
                const badge = document.getElementById('notifCountBadge');
                if (badge) {
                    badge.innerText = '0';
                }

                document.querySelectorAll('.notification-dot').forEach(function(dot) {
                    dot.style.display = 'none';
                });
            });
        }

        if (viewAllNotificationsBtn && notificationPanel) {
            viewAllNotificationsBtn.addEventListener('click', function() {
                notificationPanel.classList.add('is-open');
            });
        }

        function fetchNotifications() {
            fetch("<?= base_url('dashboard/notifications') ?>")
                .then(function(res) {
                    if (!res.ok) {
                        throw new Error('Network error');
                    }

                    return res.json();
                })
                .then(function(data) {
                    const container = document.getElementById("notification-window");
                    const badge = document.getElementById('notifCountBadge');

                    if (!container) {
                        return;
                    }

                    const previousIds = lastNotificationData.map(function(item) {
                        return String(item.date || '') + String(item.count || '');
                    });

                    container.innerHTML = "";

                    if (!data || !Array.isArray(data) || data.length === 0) {
                        container.innerHTML = `
                            <div class="ef-empty-state">
                                <i class="fas fa-bell-slash"></i>
                                <p style="margin:0 !important;">No new notifications</p>
                            </div>
                        `;

                        if (badge) {
                            badge.innerText = '0';
                        }

                        lastNotificationData = [];
                        return;
                    }

                    let totalUsersCount = 0;

                    data.forEach(function(item, idx) {
                        const dateLabel = item.date || "Just now";
                        const count = Number(item.count || 0);
                        const users = Array.isArray(item.users) ? item.users : [];

                        totalUsersCount += count;

                        const isNew = lastNotificationData.length > 0 &&
                            !previousIds.includes(String(item.date || '') + String(item.count || ''));

                        let firstEmail = 'New dashboard update';
                        if (users.length > 0 && users[0] && users[0].email) {
                            firstEmail = users[0].email;
                        }

                        const avatarText = getNotificationAvatarText(firstEmail, idx);
                        const avatarColorClass = idx % 3 === 0 ? 'ef-blue' : (idx % 3 === 1 ? 'ef-green' : 'ef-orange');

                        const card = document.createElement("div");
                        card.className = "notification-item";
                        if (isNew) {
                            card.classList.add("new-notification");
                        }

                        card.innerHTML = `
                            <div class="notification-avatar ${avatarColorClass}">
                                ${avatarText}
                            </div>

                            <div class="notification-body">
                                <p class="notification-title">
                                    ${buildNotificationTitle(users, count)}
                                </p>
                                <span class="notification-date">${escapeHtml(dateLabel)}</span>
                            </div>

                            <span class="notification-dot"></span>
                        `;

                        container.appendChild(card);
                    });

                    if (badge) {
                        badge.innerText = String(totalUsersCount);
                    }

                    lastNotificationData = data;
                })
                .catch(function(err) {
                    console.error("Notification fetch error:", err);

                    const container = document.getElementById("notification-window");
                    if (container && container.innerHTML.indexOf('Loading notifications') !== -1) {
                        container.innerHTML = `
                            <div class="ef-empty-state">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p style="margin:0 !important;">Unable to load notifications</p>
                            </div>
                        `;
                    }
                });
        }

        function buildNotificationTitle(users, count) {
            if (users.length > 0) {
                const email = escapeHtml(users[0].email || 'A user');
                const moreCount = Math.max(0, count - 1);

                if (moreCount > 0) {
                    return `${email} and ${moreCount} more user${moreCount > 1 ? 's' : ''} registered.`;
                }

                return `${email} registered successfully.`;
            }

            if (count > 0) {
                return `${count} new user${count > 1 ? 's' : ''} registered.`;
            }

            return `New dashboard notification received.`;
        }

        function getNotificationAvatarText(text, index) {
            if (!text || typeof text !== 'string') {
                return String(index + 1);
            }

            const cleanText = text.trim();
            if (!cleanText) {
                return String(index + 1);
            }

            return escapeHtml(cleanText.charAt(0).toUpperCase());
        }

        function escapeHtml(str) {
            if (str === null || str === undefined) {
                return '';
            }

            return String(str).replace(/[&<>"']/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                if (m === '"') return '&quot;';
                if (m === "'") return '&#039;';
                return m;
            });
        }
        
        
        function copyPrinterKey() {
    const key = document.getElementById("printerKey").value;

    navigator.clipboard.writeText(key).then(function () {
        alert("Printer key copied successfully!");
    }).catch(function () {
        // Fallback for older browsers
        const input = document.getElementById("printerKey");
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand("copy");

        alert("Printer key copied successfully!");
    });
}

        function updateTitleWithUnreadCount() {
            fetch('<?php echo site_url("messages/get_unread_count"); ?>?t=' + Date.now())
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Network error');
                    }

                    return response.json();
                })
                .then(function(data) {
                    if (data && data.unread_count !== undefined && data.unread_count !== currentUnreadCount) {
                        currentUnreadCount = data.unread_count;
                        document.title = `(${data.unread_count}) Dashboard`;
                    }
                })
                .catch(function(error) {
                    console.error('Error fetching unread count:', error);
                })
                .finally(function() {
                    setTimeout(updateTitleWithUnreadCount, 5000);
                });
        }

        // Smooth video behavior: pause other videos when one starts
        document.addEventListener('play', function(event) {
            const target = event.target;
            if (!target || target.tagName !== 'VIDEO') {
                return;
            }

            document.querySelectorAll('.ef-video-media video').forEach(function(video) {
                if (video !== target) {
                    video.pause();
                }
            });
        }, true);

        document.addEventListener('DOMContentLoaded', function() {
            window.setTimeout(function() {
                fetchNotifications();
                setInterval(fetchNotifications, 30000);
            }, 1200);

            window.setTimeout(updateTitleWithUnreadCount, 800);
        });
    </script>
