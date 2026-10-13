<?php
/**
 * GRID — Standalone Team Workspace View
 * Hosts the independent React Application for Slack-like GRID Team
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

$teamJsBundle = __DIR__ . '/team_react/dist/team_bundle.js';
?>
<!doctype html>
<html lang="en" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRID Team — Collaborative Workspace</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS Engine -->
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
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #F8FAFC;
            color: #0F172A;
            overscroll-behavior-x: none;
            overscroll-behavior: none;
        }
        .g-loader-stage {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #F8FAFC;
            position: relative;
            overflow: hidden;
        }
        .g-ambient-radial {
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(17, 100, 163, 0.12) 0%, rgba(241, 245, 249, 0.5) 45%, transparent 70%);
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
            background-color: rgba(15, 23, 42, 0.15);
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
                background-color: rgba(15, 23, 42, 0.15);
                transform: scale(1);
                box-shadow: 0 0 0 transparent;
            }
            25% {
                background-color: #0284C7;
                transform: scale(1.22);
                box-shadow: 0 0 14px rgba(2, 132, 199, 0.5), 0 0 24px rgba(56, 189, 248, 0.3);
            }
            50% {
                background-color: rgba(15, 23, 42, 0.15);
                transform: scale(1);
                box-shadow: 0 0 0 transparent;
            }
        }
    </style>
</head>
<body class="h-full bg-[#F8FAFC] text-slate-800 antialiased overflow-hidden">

    <!-- Standalone React Root -->
    <div id="grid-team-root" class="h-full w-full">
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

    <!-- Inject Session Data -->
    <script>
        window.__GRID_TEAM_USER__ = {
            id: <?= json_encode($login_user->id ?? 1) ?>,
            name: <?= json_encode($fullName) ?>,
            firstName: <?= json_encode($firstName) ?>,
            email: <?= json_encode($userEmail) ?>,
            initials: <?= json_encode($initials) ?>
        };
    </script>

    <!-- Standalone React Bundle -->
    <?php if (file_exists($teamJsBundle)): ?>
        <script><?= file_get_contents($teamJsBundle) ?></script>
    <?php else: ?>
        <script src="<?= function_exists('base_url') ? base_url('Views/GRID/team_react/dist/team_bundle.js') : '/Views/GRID/team_react/dist/team_bundle.js' ?>"></script>
    <?php endif; ?>

</body>
</html>
