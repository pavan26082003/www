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

    $app_data_script = '<script>window.__APP_DATA__ = ' . json_encode($app_data, JSON_UNESCAPED_SLASHES) . ';</script>';
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
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>GRID CHAT - Build Required</title>
        <style>
            body { font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #0f172a; color: #f8fafc; }
            .card { background: #1e293b; padding: 2rem; border-radius: 12px; max-width: 500px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
            code { background: #334155; padding: 0.2rem 0.5rem; border-radius: 4px; color: #38bdf8; }
        </style>
    </head>
    <body>
        <div class="card">
            <h2>GRID_CHAT Build Not Found</h2>
            <p>Please build the React frontend application first:</p>
            <p><code>cd "app/Views/GRID_CHAT" && npm run build</code></p>
        </div>
    </body>
    </html>
    <?php
}
