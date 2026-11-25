<?php
/**
 * Grid Chat Entry View
 * Seamlessly loads the built React/Vite application and injects CodeIgniter session data
 */

$html_path = APPPATH . 'Views/GRID_CHAT/dist/index.html';

if (file_exists($html_path)) {
    $html = file_get_contents($html_path);

    $app_data = [
        'currentUser' => $current_user ?? null,
        'apiBase' => $api_base ?? site_url('grid-chat/api'),
        'csrfTokenName' => $csrf_token_name ?? '',
        'csrfHash' => $csrf_hash ?? '',
        'initialRoute' => $initial_route ?? '',
    ];

    $theme_script = '<script>(function(){var theme=String(new URLSearchParams(window.location.search).get("theme")||"").toLowerCase();if(theme!=="dark"&&theme!=="light"){try{theme=window.matchMedia("(prefers-color-scheme: dark)").matches?"dark":"light";}catch(e){theme="light";}}document.documentElement.setAttribute("data-theme",theme);try{var raw=localStorage.getItem("mychat_settings");var settings=raw?JSON.parse(raw):{};if(!settings||typeof settings!=="object")settings={};settings.theme=theme;localStorage.setItem("mychat_settings",JSON.stringify(settings));}catch(e){}})();</script>';
    $app_data_script = $theme_script . '<script>window.__APP_DATA__ = ' . json_encode($app_data, JSON_UNESCAPED_SLASHES) . ';</script>';
    $offline_script = '<script src="/grid-offline.js?v=1"></script>';

    // Inject App Data before closing head tag
    if (strpos($html, '</head>') !== false) {
        $html = str_replace('</head>', $app_data_script . "\n" . $offline_script . "\n</head>", $html);
    } else {
        $html = $app_data_script . $html;
    }

    // Rewrite relative asset paths to our CI4 asset controller endpoint
    $asset_url = rtrim($asset_base ?? site_url('grid-chat/assets'), '/');
    $html = str_replace('src="./assets/', 'src="' . $asset_url . '/', $html);
    $html = str_replace('href="./assets/', 'href="' . $asset_url . '/', $html);

    echo $html;
} else {
    $logoLight = 'https://efactor.tech/assets/images/efactor-logo.png';
    $logoDark = 'https://efactor.tech/assets/images/efactor-logo_light.png';
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="light dark">
        <title>GRID CHAT - Build Required</title>
        <script>
          (function () {
            var theme = String(new URLSearchParams(window.location.search).get('theme') || '').toLowerCase();
            if (theme !== 'dark' && theme !== 'light') {
              try {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
              } catch (e) {
                theme = 'light';
              }
            }
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.style.colorScheme = theme;
          })();
        </script>
        <style>
            :root, html[data-theme="light"] {
              --bg: #f1f5f9;
              --card: #ffffff;
              --text: #0f172a;
              --muted: #475569;
              --code-bg: #e2e8f0;
              --code-fg: #0369a1;
              --line: rgba(15, 23, 42, .12);
              --shadow: 0 10px 25px rgba(15, 23, 42, .12);
            }
            html[data-theme="dark"] {
              --bg: #0f172a;
              --card: #1e293b;
              --text: #f8fafc;
              --muted: #94a3b8;
              --code-bg: #334155;
              --code-fg: #38bdf8;
              --line: rgba(148, 163, 184, .28);
              --shadow: 0 10px 25px rgba(0, 0, 0, .5);
            }
            body {
              font-family: system-ui, sans-serif;
              display: flex;
              align-items: center;
              justify-content: center;
              min-height: 100vh;
              margin: 0;
              background: var(--bg);
              color: var(--text);
            }
            .card {
              background: var(--card);
              padding: 2rem;
              border-radius: 12px;
              max-width: 500px;
              text-align: center;
              box-shadow: var(--shadow);
              border: 1px solid var(--line);
            }
            .brand {
              margin: 0 auto 1rem;
              height: 40px;
              width: auto;
              max-width: 180px;
              object-fit: contain;
            }
            p { color: var(--muted); }
            code {
              background: var(--code-bg);
              padding: 0.2rem 0.5rem;
              border-radius: 4px;
              color: var(--code-fg);
            }
        </style>
    </head>
    <body>
        <div class="card">
            <img
              class="brand"
              id="gridChatFallbackLogo"
              src="<?= esc($logoLight) ?>"
              alt="eFactor"
              data-logo-light="<?= esc($logoLight) ?>"
              data-logo-dark="<?= esc($logoDark) ?>"
            >
            <h2>GRID_CHAT Build Not Found</h2>
            <p>Please build the React frontend application first:</p>
            <p><code>cd "app/Views/GRID_CHAT" && npm run build</code></p>
        </div>
        <script>
          (function () {
            var theme = document.documentElement.getAttribute('data-theme') || 'light';
            var logo = document.getElementById('gridChatFallbackLogo');
            if (!logo) return;
            var next = theme === 'dark'
              ? (logo.getAttribute('data-logo-dark') || '')
              : (logo.getAttribute('data-logo-light') || '');
            if (next) logo.setAttribute('src', next);
          })();
        </script>
    </body>
    </html>
    <?php
}
