<?php
/**
 * Production Grid public API.
 * Deploy this file to: /v8/grid_api_creation.php
 *
 * GET ?action=get_grid&user_id=N
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: private, no-cache, max-age=0, must-revalidate');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (!function_exists('grid_api_json')) {
    function grid_api_json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    function grid_api_bootstrap_config(): void
    {
        static $loaded = false;
        if ($loaded) {
            return;
        }
        $loaded = true;

        $dir = str_replace('\\', '/', __DIR__);
        $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
        $candidates = array(
            $dir . '/config.php',
            $dir . '/../config.php',
            $dir . '/../v8/config.php',
            $dir . '/../../v8/config.php',
            $dir . '/../../../v8/config.php',
        );
        if ($docRoot !== '') {
            $candidates[] = $docRoot . '/v8/config.php';
            $candidates[] = $docRoot . '/config.php';
        }

        foreach ($candidates as $path) {
            if (!is_file($path)) {
                continue;
            }
            $prevCwd = getcwd();
            $configDir = dirname($path);
            if (is_dir($configDir)) {
                @chdir($configDir);
            }
            require_once $path;
            if ($prevCwd) {
                @chdir($prevCwd);
            }
            return;
        }
    }

    function grid_api_load_class(string $className, string $relativeFile): bool
    {
        if (class_exists($className, false)) {
            return true;
        }

        $dir = str_replace('\\', '/', __DIR__);
        $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
        $candidates = array(
            $dir . '/Libraries/' . $relativeFile,
            $dir . '/../Libraries/' . $relativeFile,
            $dir . '/../../Libraries/' . $relativeFile,
            $dir . '/../app/Libraries/' . $relativeFile,
            $dir . '/../../app/Libraries/' . $relativeFile,
            $dir . '/../../../app/Libraries/' . $relativeFile,
        );
        if ($docRoot !== '') {
            $candidates[] = $docRoot . '/app/Libraries/' . $relativeFile;
            $candidates[] = $docRoot . '/../app/Libraries/' . $relativeFile;
            $candidates[] = dirname($docRoot) . '/app/Libraries/' . $relativeFile;
        }
        if (defined('BASE_PATH')) {
            $candidates[] = rtrim(str_replace('\\', '/', (string) BASE_PATH), '/') . '/app/Libraries/' . $relativeFile;
        }

        foreach ($candidates as $path) {
            $real = realpath($path);
            if (!$real || !is_file($real)) {
                continue;
            }
            require_once $real;
            if (class_exists($className, false)) {
                return true;
            }
        }

        return false;
    }

    function grid_api_load_builder(): bool
    {
        grid_api_load_class(\App\Libraries\GridWebSso::class, 'GridWebSso.php');
        return grid_api_load_class(\App\Libraries\GridSduiSchemaBuilder::class, 'GridSduiSchemaBuilder.php');
    }

    function grid_api_db_connect()
    {
        $host = defined('DB_HOST') ? (string) DB_HOST : (string) (getenv('DB_HOST') ?: getenv('database.default.hostname') ?: '');
        $user = defined('DB_USER') ? (string) DB_USER : (string) (getenv('DB_USER') ?: getenv('database.default.username') ?: '');
        $pass = defined('DB_PASS') ? (string) DB_PASS : (string) (getenv('DB_PASS') ?: getenv('database.default.password') ?: '');
        $name = defined('DB_NAME') ? (string) DB_NAME : (string) (getenv('DB_NAME') ?: getenv('database.default.database') ?: '');
        if ($host === '' || $user === '' || $name === '') {
            return null;
        }
        $db = @new mysqli($host, $user, $pass, $name);
        if ($db->connect_errno) {
            return null;
        }
        $db->set_charset('utf8mb4');
        return $db;
    }

    function grid_api_resolve_content(int $userId): array
    {
        if ($userId <= 0) {
            return array();
        }

        $content = null;
        try {
            grid_api_bootstrap_config();
            $db = grid_api_db_connect();
            if ($db) {
                $stmt = $db->prepare('SELECT layout_json FROM app_home_page_settings WHERE user_id = ? LIMIT 1');
                if ($stmt) {
                    $stmt->bind_param('i', $userId);
                    $stmt->execute();
                    $row = $stmt->get_result()->fetch_assoc();
                    if ($row && !empty($row['layout_json'])) {
                        $layout = json_decode((string) $row['layout_json'], true);
                        if (is_array($layout) && !empty($layout['gridPage']) && is_array($layout['gridPage'])) {
                            $content = $layout['gridPage'];
                        }
                    }
                    $stmt->close();
                }
                $db->close();
            }
        } catch (Throwable $e) {
            $content = null;
        }

        return is_array($content) ? $content : array();
    }

    function grid_api_public_endpoint(): string
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443)
            || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
        $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/v8/grid_api_creation.php'));
        $path = $script;
        if (strpos($script, '/v8/') !== false) {
            $path = '/v8/grid_api_creation.php';
        }
        return ($https ? 'https://' : 'http://') . $host . $path;
    }
}

grid_api_bootstrap_config();

if (!grid_api_load_builder()) {
    grid_api_json(array(
        'success' => false,
        'message' => 'GridSduiSchemaBuilder not found on server. Deploy app/Libraries/GridSduiSchemaBuilder.php',
    ), 500);
}

$action = strtolower(trim((string) ($_GET['action'] ?? 'get_grid')));
$userId = (int) ($_GET['user_id'] ?? $_GET['userId'] ?? 0);

if ($action !== 'get_grid') {
    grid_api_json(array(
        'success' => false,
        'message' => 'Unknown action. Use action=get_grid.',
        'supported' => array('get_grid'),
    ), 400);
}

$contentOverride = grid_api_resolve_content($userId);
$endpoint = grid_api_public_endpoint();
$ssoToken = '';
if ($userId > 0 && class_exists(\App\Libraries\GridWebSso::class, false)) {
    $ssoToken = \App\Libraries\GridWebSso::issue($userId);
}
$schema = \App\Libraries\GridSduiSchemaBuilder::build(array(
    'userId' => $userId,
    'content' => $contentOverride,
    'endpoint' => $endpoint,
    'ssoToken' => $ssoToken,
));

$schemaJson = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
grid_api_json(array(
    'success' => true,
    'user_id' => $userId > 0 ? $userId : null,
    'pageId' => 'grid',
    'routeId' => 'grid',
    'schema_version' => $schema['schemaVersion'] ?? '3.0.0',
    'updated_at' => gmdate('c'),
    'contentHash' => is_string($schemaJson) ? hash('sha256', $schemaJson) : '',
    'gridApiUrl' => $endpoint,
    'schema' => $schema,
));
