<?php
/**
 * GRID — Home Workspace & Professional Feed
 * Unified React 18 Application Host Shell
 */

$fullName = 'K Pavan Kumar';
$userEmail = '';
$firstName = 'Pavan';
$initials = 'PK';

if (isset($login_user) && !empty($login_user->first_name)) {
    $firstName = $login_user->first_name;
    $fullName = trim($login_user->first_name . ' ' . ($login_user->last_name ?? ''));
    $userEmail = $login_user->email ?? '';
    $initials = strtoupper(substr($login_user->first_name, 0, 1) . substr($login_user->last_name ?? '', 0, 1));
}

// Fetch profile and wizard status (never blank the React shell if GRID DB is down)
$loginUserId = (isset($login_user) && !empty($login_user->id)) ? (int)$login_user->id : 1;
$userProfile = [];
try {
    $profileModel = model('App\Models\GRID_MODEL\Grid_profile_model');
    $userProfile = $profileModel->get_profile_by_user_id($loginUserId) ?: [];
} catch (Throwable $e) {
    log_message('error', 'GRID UI profile load failed: ' . $e->getMessage());
}
$isWizardCompleted = !empty($userProfile['is_wizard_completed']);
$userHeadline = !empty($userProfile['headline']) ? $userProfile['headline'] : '';
$userLocation = !empty($userProfile['location']) ? $userProfile['location'] : '';
$userBio = !empty($userProfile['bio']) ? $userProfile['bio'] : '';
$userGridId = !empty($userProfile['grid_id']) ? $userProfile['grid_id'] : '';
$isOpenToWork = isset($userProfile['is_open_to_work']) ? (bool)$userProfile['is_open_to_work'] : true;

// 1. Resolve Avatar data strictly from grid_profiles (isolated from main hub login_user->image)
$avatarSrc = '';
if (!empty($userProfile['avatar'])) {
    if (function_exists('get_avatar')) {
        $avatarSrc = (string) get_avatar($userProfile['avatar']);
    } else {
        $unserialized = @unserialize($userProfile['avatar']);
        if (is_array($unserialized) && !empty($unserialized['file_name'])) {
            $avatarSrc = function_exists('base_url') ? base_url('files/profile_images/' . $unserialized['file_name']) : '/files/profile_images/' . $unserialized['file_name'];
        } else {
            $avatarSrc = function_exists('base_url') ? base_url('files/profile_images/' . $userProfile['avatar']) : '/files/profile_images/' . $userProfile['avatar'];
        }
    }
}

// If no custom avatar uploaded in user profile, keep avatarSrc empty
// so the UI displays the user's authentic initials until they upload their own photo


if (!function_exists('getBase64Image')) {
    function getBase64Image($relPath) {
        $fullPath = __DIR__ . DIRECTORY_SEPARATOR . $relPath;
        if (file_exists($fullPath)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($fullPath));
        }
        return '';
    }
}

$gridLogoSrc = getBase64Image('images/grid_logo.png');
$gridLogoLightSrc = getBase64Image('images/grid_logo_light.png') ?: $gridLogoSrc;
$gridLogoDarkSrc = getBase64Image('images/grid_logo_dark.png') ?: $gridLogoSrc;
$cssBundlePath = __DIR__ . '/react/dist/grid_profile.css';
$jsBundlePath = __DIR__ . '/react/dist/bundle.js';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRID — Unified Professional Workspace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <!-- Tailwind CSS Engine for dynamic utilities -->
    <script>
        (function() {
            var _w = console.warn;
            console.warn = function() {
                if (arguments[0] && typeof arguments[0] === 'string' && arguments[0].indexOf('cdn.tailwindcss.com') !== -1) return;
                _w.apply(console, arguments);
            };
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    }
                }
            }
        }
    </script>
    
    <!-- Consolidated Profile & Component Stylesheet -->
    <?php if (file_exists($cssBundlePath)): ?>
        <style><?= file_get_contents($cssBundlePath) ?></style>
    <?php else: ?>
        <link rel="stylesheet" href="<?= base_url('Views/GRID/react/dist/grid_profile.css') ?>">
    <?php endif; ?>
</head>
<body class="antialiased">

    <!-- React 18 Root Mounting Target -->
    <div id="grid-profile-root">
        <style>
            .g-loader-stage {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #f8fafc;
                position: relative;
                overflow: hidden;
            }
            .g-ambient-radial {
                position: absolute;
                width: 450px;
                height: 450px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, rgba(147, 51, 234, 0.06) 45%, transparent 70%);
                filter: blur(50px);
                pointer-events: none;
                animation: gAmbientPulse 4s ease-in-out infinite alternate;
            }
            @keyframes gAmbientPulse {
                0% { transform: scale(0.9); opacity: 0.6; }
                100% { transform: scale(1.15); opacity: 1; }
            }
            .g-matrix-icon {
                display: grid;
                grid-template-columns: repeat(3, 16px);
                grid-template-rows: repeat(3, 16px);
                gap: 6px;
                width: 60px;
                height: 60px;
                user-select: none;
                z-index: 10;
            }
            .g-matrix-dot {
                width: 16px;
                height: 16px;
                border-radius: 4.5px;
                background-color: #cbd5e1;
                animation: gQuantumWave 2.6s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            }
            .g-matrix-dot:nth-child(1) { animation-delay: calc(2.6s * 0.00); }
            .g-matrix-dot:nth-child(2) { animation-delay: calc(2.6s * 0.12); }
            .g-matrix-dot:nth-child(3) { animation-delay: calc(2.6s * 0.24); }
            .g-matrix-dot:nth-child(4) { animation-delay: calc(2.6s * 0.12); }
            .g-matrix-dot:nth-child(5) { animation-delay: calc(2.6s * 0.24); }
            .g-matrix-dot:nth-child(6) { animation-delay: calc(2.6s * 0.36); }
            .g-matrix-dot:nth-child(7) { animation-delay: calc(2.6s * 0.24); }
            .g-matrix-dot:nth-child(8) { animation-delay: calc(2.6s * 0.36); }
            .g-matrix-dot:nth-child(9) { animation-delay: calc(2.6s * 0.48); }
            @keyframes gQuantumWave {
                0%, 100% {
                    background-color: #cbd5e1;
                    transform: scale(1);
                    box-shadow: 0 0 0 transparent;
                }
                25% {
                    background-color: #2563eb;
                    transform: scale(1.22);
                    box-shadow: 0 0 14px rgba(37, 99, 235, 0.45), 0 0 24px rgba(6, 182, 212, 0.3);
                }
                50% {
                    background-color: #cbd5e1;
                    transform: scale(1);
                    box-shadow: 0 0 0 transparent;
                }
            }
        </style>
        <div class="g-loader-stage">
            <div class="g-ambient-radial"></div>
            <div class="g-matrix-icon">
                <div class="g-matrix-dot"></div>
                <div class="g-matrix-dot"></div>
                <div class="g-matrix-dot"></div>
                <div class="g-matrix-dot"></div>
                <div class="g-matrix-dot"></div>
                <div class="g-matrix-dot"></div>
                <div class="g-matrix-dot"></div>
                <div class="g-matrix-dot"></div>
                <div class="g-matrix-dot"></div>
            </div>
        </div>
    </div>

    <!-- Inject PHP Data Contract to React Store -->
    <script>
        window.__GRID_DATA__ = {
            user: {
                id: <?= json_encode($login_user->id ?? 1) ?>,
                name: <?= json_encode($fullName) ?>,
                firstName: <?= json_encode($firstName) ?>,
                email: <?= json_encode($userEmail) ?>,
                initials: <?= json_encode($initials) ?>,
                avatar: <?= json_encode($avatarSrc) ?>,
                headline: <?= json_encode($userHeadline) ?>,
                location: <?= json_encode($userLocation) ?>,
                bio: <?= json_encode($userBio) ?>,
                gridId: <?= json_encode($userGridId) ?>,
                isWizardCompleted: <?= $isWizardCompleted ? 'true' : 'false' ?>,
                spacesCount: 1,
                connectionsCount: 0,
                followersCount: 0,
                profileViews: 0,
                postImpressions: 0,
                searchAppearances: 0,
                bannerVideoSrc: '',
                bannerTag: '',
                bannerTitle: '',
                bannerUrl: <?= json_encode($userGridId ? 'grid.community/in/' . $userGridId : '') ?>,
                openToWork: {
                    active: <?= $isOpenToWork ? 'true' : 'false' ?>,
                    roles: 'Open to opportunities',
                    note: 'Available'
                },
                profileStrength: {
                    level: <?= $isWizardCompleted ? "'Intermediate'" : "'Beginner'" ?>,
                    percent: <?= $isWizardCompleted ? 60 : 20 ?>,
                    tip: 'Complete your profile setup to increase visibility across the community.'
                }
            },
            images: {
                logo: <?= json_encode($gridLogoLightSrc) ?>,
                logoLight: <?= json_encode($gridLogoLightSrc) ?>,
                logoDark: <?= json_encode($gridLogoDarkSrc) ?>
            },
            apiUrl: <?= json_encode(function_exists('site_url') ? site_url('grid/api') : '/index.php/grid/api') ?>
        };
    </script>

    <script src="/grid-offline.js?v=1"></script>
    <script>
        (function () {
            var path = window.location.pathname.toLowerCase();
            if (path.indexOf('/grid/connect') === -1) return;
            var params = new URLSearchParams(window.location.search);
            var userId = params.get('user_id') || params.get('userId') || params.get('u') || '';
            var gridId = params.get('grid_id') || params.get('gridId') || '';
            var next = new URLSearchParams();
            if (userId) next.set('userId', userId);
            if (gridId) next.set('grid_id', gridId);
            next.set('mode', 'professional');
            var base = path.indexOf('/index.php/') !== -1 ? '/index.php/grid/profile' : '/grid/profile';
            window.history.replaceState({ route: 'profile', targetUser: userId ? { id: userId } : null }, '', base + '?' + next.toString());
        })();
    </script>

    <!-- React 18 Application Bundle -->
    <?php if (file_exists($jsBundlePath)): ?>
        <script><?= file_get_contents($jsBundlePath) ?></script>
    <?php else: ?>
        <script src="<?= base_url('Views/GRID/react/dist/bundle.js') ?>"></script>
    <?php endif; ?>

</body>
</html>
