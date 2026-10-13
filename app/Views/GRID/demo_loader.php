<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRID Loader Demo — Concept 1: Quantum Matrix Wave</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800;900&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f8fafc;
            --card-bg: rgba(255, 255, 255, 0.85);
            --text-color: #0f172a;
            --text-muted: #64748b;
            --border-color: rgba(0, 0, 0, 0.08);
            --dot-base: #cbd5e1;
            --dot-active: #2563eb;
            --dot-glow: rgba(37, 99, 235, 0.45);
            --wave-speed: 2.6s;
        }

        [data-theme="dark"] {
            --bg-color: #0d1117;
            --card-bg: rgba(255, 255, 255, 0.04);
            --text-color: #ffffff;
            --text-muted: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.1);
            --dot-base: rgba(255, 255, 255, 0.25);
            --dot-active: #3b82f6;
            --dot-glow: rgba(59, 130, 246, 0.85);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: background-color 0.4s ease, color 0.4s ease;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Glow Backdrop */
        .ambient-radial {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, rgba(147, 51, 234, 0.08) 45%, transparent 70%);
            filter: blur(60px);
            pointer-events-none;
            animation: ambientPulse 4s ease-in-out infinite alternate;
        }

        @keyframes ambientPulse {
            0% { transform: scale(0.9); opacity: 0.6; }
            100% { transform: scale(1.15); opacity: 1; }
        }

        /* Center Stage Container */
        .loader-stage {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
            transition: opacity 0.5s ease, transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .loader-stage.dissolve {
            opacity: 0;
            transform: scale(0.94);
            pointer-events: none;
        }

        /* 3x3 Quantum Matrix Grid Container */
        .matrix-icon {
            display: grid;
            grid-template-columns: repeat(3, 16px);
            grid-template-rows: repeat(3, 16px);
            gap: 6px;
            width: 60px;
            height: 60px;
            user-select: none;
        }

        /* Individual Matrix Dot/Cube */
        .matrix-dot {
            width: 16px;
            height: 16px;
            border-radius: 4.5px;
            background-color: var(--dot-base);
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            animation: quantumWave var(--wave-speed) cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        /* Diagonal Cascade Timing */
        .matrix-dot:nth-child(1) { animation-delay: calc(var(--wave-speed) * 0.00); }
        .matrix-dot:nth-child(2) { animation-delay: calc(var(--wave-speed) * 0.12); }
        .matrix-dot:nth-child(3) { animation-delay: calc(var(--wave-speed) * 0.24); }
        .matrix-dot:nth-child(4) { animation-delay: calc(var(--wave-speed) * 0.12); }
        .matrix-dot:nth-child(5) { animation-delay: calc(var(--wave-speed) * 0.24); }
        .matrix-dot:nth-child(6) { animation-delay: calc(var(--wave-speed) * 0.36); }
        .matrix-dot:nth-child(7) { animation-delay: calc(var(--wave-speed) * 0.24); }
        .matrix-dot:nth-child(8) { animation-delay: calc(var(--wave-speed) * 0.36); }
        .matrix-dot:nth-child(9) { animation-delay: calc(var(--wave-speed) * 0.48); }

        @keyframes quantumWave {
            0%, 100% {
                background-color: var(--dot-base);
                transform: scale(1);
                box-shadow: 0 0 0 transparent;
            }
            25% {
                background-color: var(--dot-active);
                transform: scale(1.22);
                box-shadow: 0 0 14px var(--dot-glow), 0 0 24px rgba(6, 182, 212, 0.4);
            }
            50% {
                background-color: var(--dot-base);
                transform: scale(1);
                box-shadow: 0 0 0 transparent;
            }
        }



        /* Interactive Demo Dashboard (Bottom Controls) */
        .demo-controls {
            position: fixed;
            bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 18px;
            border-radius: 20px;
            background: rgba(15, 23, 42, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45);
            z-index: 50;
            color: #f8fafc;
            font-size: 12px;
            flex-wrap: wrap;
            justify-content: center;
            max-width: 90vw;
        }

        .ctrl-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #ffffff;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .ctrl-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.35);
            transform: translateY(-1px);
        }

        .ctrl-btn.active {
            background: #2563eb;
            border-color: #3b82f6;
        }

        /* Success Simulation Screen */
        .app-loaded-banner {
            display: none;
            text-align: center;
            padding: 28px 36px;
            border-radius: 24px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            backdrop-filter: blur(20px);
            max-width: 380px;
            animation: bannerPop 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes bannerPop {
            0% { opacity: 0; transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body data-theme="light">

    <div class="ambient-radial"></div>

    <!-- Active Loading Screen: Quantum Matrix Only -->
    <div class="loader-stage" id="loaderStage">
        <!-- 3x3 Quantum Wave Matrix -->
        <div class="matrix-icon">
            <div class="matrix-dot"></div>
            <div class="matrix-dot"></div>
            <div class="matrix-dot"></div>
            <div class="matrix-dot"></div>
            <div class="matrix-dot"></div>
            <div class="matrix-dot"></div>
            <div class="matrix-dot"></div>
            <div class="matrix-dot"></div>
            <div class="matrix-dot"></div>
        </div>
    </div>

    <!-- Loaded State Simulation -->
    <div class="app-loaded-banner" id="loadedBanner">
        <div style="font-size: 32px; margin-bottom: 8px;">🚀</div>
        <h2 style="font-size: 18px; font-weight: 800; margin-bottom: 6px;">Workspace Ready!</h2>
        <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 16px;">
            The loader smoothly dissolves as soon as the app finishes initializing.
        </p>
        <button class="ctrl-btn active" onclick="restartLoader()">↺ Replay Loader</button>
    </div>

    <!-- Interactive Controls Bar -->
    <div class="demo-controls">
        <button class="ctrl-btn" id="themeBtn" onclick="toggleTheme()">🌙 Switch to Dark Mode</button>
        <span style="width: 1px; height: 16px; background: rgba(255,255,255,0.2);"></span>
        <button class="ctrl-btn" style="background: #2563eb; border-color: #3b82f6;" onclick="simulateLoadComplete()">
            Simulate App Loaded →
        </button>
    </div>

    <script>
        let currentTheme = 'light';
        const body = document.body;
        const themeBtn = document.getElementById('themeBtn');
        const loaderStage = document.getElementById('loaderStage');
        const loadedBanner = document.getElementById('loadedBanner');

        function toggleTheme() {
            currentTheme = currentTheme === 'light' ? 'dark' : 'light';
            body.setAttribute('data-theme', currentTheme);
            themeBtn.innerHTML = currentTheme === 'light' ? '🌙 Switch to Dark Mode' : '☀️ Switch to Light Mode';
        }


        function simulateLoadComplete() {
            loaderStage.classList.add('dissolve');
            setTimeout(() => {
                loaderStage.style.display = 'none';
                loadedBanner.style.display = 'block';
            }, 450);
        }

        function restartLoader() {
            loadedBanner.style.display = 'none';
            loaderStage.style.display = 'flex';
            void loaderStage.offsetWidth; // trigger reflow
            loaderStage.classList.remove('dissolve');
        }
    </script>
</body>
</html>
