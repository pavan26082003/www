<?php
/**
 * V2 test_nodes-compatible API.
 *
 * GET /v8/v2_test_nodes.php?node_id=...&model_no=...
 *
 * Returns the same envelope as legacy test_nodes:
 *   { node_details: [...], nodes: "<node_id>", v2_node_details: [...] }
 *
 * Built from api_product_configs / frames / fields + V2 display handlers,
 * with optional live overlay from auto_get_nodes_params.
 * Legacy test_nodes is unchanged.
 *
 * Standalone file only — not routed through api.php.
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
@ini_set('memory_limit', '1024M');
@ini_set('max_execution_time', '120');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: private, no-cache, max-age=0, must-revalidate');

/**
 * Always emit JSON — never leave Content-Length: 0 after headers.
 * json_encode(false) was a common cause of blank 200 responses.
 */
function v2_test_nodes_emit($payload, $httpCode = 200)
{
    if (!is_array($payload)) {
        $payload = [
            'status' => false,
            'message' => 'Invalid V2 payload',
            'node_details' => [],
            'nodes' => '',
            'source' => 'v2_test_nodes',
        ];
        $httpCode = 500;
    }
    if (!isset($payload['source'])) {
        $payload['source'] = 'v2_test_nodes';
    }

    $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
    if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
        $flags |= JSON_INVALID_UTF8_SUBSTITUTE;
    }
    if (defined('JSON_PARTIAL_OUTPUT_ON_ERROR')) {
        $flags |= JSON_PARTIAL_OUTPUT_ON_ERROR;
    }

    $json = json_encode($payload, $flags);
    if ($json === false) {
        $httpCode = 500;
        $json = json_encode([
            'status' => false,
            'message' => 'V2 test_nodes JSON encode failed',
            'error' => function_exists('json_last_error_msg') ? json_last_error_msg() : 'json_encode failed',
            'node_details' => [],
            'nodes' => isset($payload['nodes']) ? $payload['nodes'] : '',
            'model_no' => isset($payload['model_no']) ? $payload['model_no'] : null,
            'source' => 'v2_test_nodes',
        ], JSON_UNESCAPED_SLASHES);
    }
    if ($json === false || $json === '') {
        $httpCode = 500;
        $json = '{"status":false,"message":"V2 test_nodes encode failed","node_details":[],"source":"v2_test_nodes"}';
    }

    if (!headers_sent()) {
        http_response_code((int) $httpCode);
        header('Content-Type: application/json; charset=UTF-8');
    }
    echo $json;
}

// Convert fatals (OOM/parse after require) into JSON instead of an empty 200 body.
register_shutdown_function(static function () {
    $err = error_get_last();
    if (!is_array($err)) {
        return;
    }
    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if (!in_array((int) ($err['type'] ?? 0), $fatalTypes, true)) {
        return;
    }

    // If we already printed a JSON body, do not append another.
    $sent = headers_sent();
    $buffer = function_exists('ob_get_length') ? (int) ob_get_length() : -1;
    if ($sent && $buffer === 0) {
        // Headers sent, no body yet — typical empty-200 failure mode.
    } elseif ($sent && $buffer > 0) {
        return;
    }

    $message = trim((string) ($err['message'] ?? 'fatal error'));
    $file = (string) ($err['file'] ?? '');
    $line = (int) ($err['line'] ?? 0);
    error_log('v2_test_nodes fatal: ' . $message . ' @ ' . $file . ':' . $line);

    if (!headers_sent()) {
        header('Content-Type: application/json; charset=UTF-8');
    }
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'V2 test_nodes fatal error',
        'error' => $message,
        'error_file' => $file !== '' ? basename($file) : null,
        'error_line' => $line > 0 ? $line : null,
        'node_details' => [],
        'source' => 'v2_test_nodes',
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
});

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$nodeId = '';
if (isset($_GET['node_id']) && is_scalar($_GET['node_id'])) {
    $nodeId = trim((string) $_GET['node_id']);
} elseif (isset($_GET['nodeId']) && is_scalar($_GET['nodeId'])) {
    $nodeId = trim((string) $_GET['nodeId']);
}

$modelNo = '';
if (isset($_GET['model_no']) && is_scalar($_GET['model_no'])) {
    $modelNo = trim((string) $_GET['model_no']);
} elseif (isset($_GET['model_id']) && is_scalar($_GET['model_id'])) {
    $modelNo = trim((string) $_GET['model_id']);
} elseif (isset($_GET['modelNo']) && is_scalar($_GET['modelNo'])) {
    $modelNo = trim((string) $_GET['modelNo']);
}

if ($nodeId === '' && $modelNo === '') {
    v2_test_nodes_emit([
        'status' => false,
        'message' => 'node_id or model_no is required',
        'node_details' => [],
        'nodes' => '',
        'source' => 'v2_test_nodes',
    ], 400);
    exit();
}

// CodeIgniter App config reads HTTP_HOST during boot.
if (empty($_SERVER['HTTP_HOST'])) {
    $fallbackHost = !empty($_SERVER['SERVER_NAME']) ? (string) $_SERVER['SERVER_NAME'] : 'localhost';
    $_SERVER['HTTP_HOST'] = $fallbackHost;
}
if (empty($_SERVER['SCRIPT_NAME'])) {
    $_SERVER['SCRIPT_NAME'] = '/v8/v2_test_nodes.php';
}
if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = '/v8/v2_test_nodes.php';
}

$bootError = '';
$bootstrapped = false;

/**
 * Same CI boot strategy as bootstrap_device_ui_schema.php / api_schema.php.
 */
$bootstrapCandidates = [
    __DIR__ . '/../app/Libraries/AppHomeBootstrap.php',
    dirname(__DIR__) . '/app/Libraries/AppHomeBootstrap.php',
];
$docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
if ($docRoot !== '') {
    $bootstrapCandidates[] = $docRoot . '/app/Libraries/AppHomeBootstrap.php';
    // Some hosts use DOCUMENT_ROOT = .../public or .../v8
    $bootstrapCandidates[] = dirname($docRoot) . '/app/Libraries/AppHomeBootstrap.php';
}

foreach (array_unique($bootstrapCandidates) as $path) {
    if (!is_file($path)) {
        continue;
    }
    try {
        require_once $path;
    } catch (Throwable $exception) {
        $bootError = 'require AppHomeBootstrap: ' . $exception->getMessage();
        continue;
    }
    if (!class_exists('App\\Libraries\\AppHomeBootstrap')) {
        $bootError = 'AppHomeBootstrap class missing after require: ' . $path;
        continue;
    }
    try {
        \App\Libraries\AppHomeBootstrap::bootIfNeeded();
        $bootstrapped = true;
        break;
    } catch (Throwable $exception) {
        $bootError = 'bootIfNeeded: ' . $exception->getMessage();
        error_log('v2_test_nodes bootstrap failed: ' . $exception->getMessage());
    }
}

// Fallback: boot CI Paths directly (if AppHomeBootstrap path differs on host).
if (!$bootstrapped) {
    $roots = [];
    if ($docRoot !== '') {
        $roots[] = $docRoot;
        $roots[] = dirname($docRoot);
    }
    $roots[] = dirname(__DIR__); // www root when script is in /v8
    $roots[] = dirname(__DIR__, 2);

    foreach (array_unique($roots) as $root) {
        $root = rtrim(str_replace('\\', '/', (string) $root), '/');
        $pathsFile = $root . '/app/Config/Paths.php';
        if (!is_file($pathsFile)) {
            continue;
        }
        try {
            if (!defined('FCPATH')) {
                define('FCPATH', $root . DIRECTORY_SEPARATOR);
            }
            require_once $pathsFile;
            if (!class_exists('Config\\Paths', false)) {
                continue;
            }
            $paths = new \Config\Paths();
            $bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
            if (!is_file($bootstrap)) {
                $bootError = 'CI bootstrap.php missing at ' . $bootstrap;
                continue;
            }
            require_once $bootstrap;
            if (!defined('APP_HOME_CI_BOOTED')) {
                define('APP_HOME_CI_BOOTED', true);
            }
            $bootstrapped = true;
            break;
        } catch (Throwable $exception) {
            $bootError = 'direct Paths boot: ' . $exception->getMessage();
            error_log('v2_test_nodes direct boot failed: ' . $exception->getMessage());
        }
    }
}

// Linux is case-sensitive: class App\Models\View_page_model does NOT autoload
// from file view_page_model.php (PSR-4 expects View_page_model.php). Load by path.
$modelLoadError = '';
$modelFileCandidates = [];
if (defined('APPPATH')) {
    $modelFileCandidates[] = rtrim(str_replace('\\', '/', APPPATH), '/') . '/Models/view_page_model.php';
    $modelFileCandidates[] = rtrim(str_replace('\\', '/', APPPATH), '/') . '/Models/View_page_model.php';
}
$modelFileCandidates[] = __DIR__ . '/../app/Models/view_page_model.php';
$modelFileCandidates[] = dirname(__DIR__) . '/app/Models/view_page_model.php';
if ($docRoot !== '') {
    $modelFileCandidates[] = $docRoot . '/app/Models/view_page_model.php';
    $modelFileCandidates[] = dirname($docRoot) . '/app/Models/view_page_model.php';
}

$modelFileLoaded = null;
foreach (array_unique($modelFileCandidates) as $modelFile) {
    if (!is_file($modelFile)) {
        continue;
    }
    try {
        require_once $modelFile;
        $modelFileLoaded = $modelFile;
        break;
    } catch (Throwable $exception) {
        $modelLoadError = 'require model: ' . $exception->getMessage();
    }
}

$modelClassOk = class_exists('App\\Models\\View_page_model');
if (!$bootstrapped || !$modelClassOk) {
    v2_test_nodes_emit([
        'status' => false,
        'message' => 'V2 test_nodes unavailable (CI boot or View_page_model missing)',
        'detail' => [
            'bootstrapped' => $bootstrapped,
            'view_page_model' => $modelClassOk,
            'model_file_loaded' => $modelFileLoaded,
            'boot_error' => $bootError !== '' ? $bootError : null,
            'model_load_error' => $modelLoadError !== '' ? $modelLoadError : null,
            'doc_root' => $docRoot,
            'script_dir' => __DIR__,
            'apppath' => defined('APPPATH') ? APPPATH : null,
        ],
        'node_details' => [],
        'nodes' => $nodeId,
        'source' => 'v2_test_nodes',
    ], 500);
    exit();
}

try {
    $model = new \App\Models\View_page_model();
    if (!method_exists($model, 'buildV2TestNodesPayload')) {
        v2_test_nodes_emit([
            'status' => false,
            'message' => 'View_page_model::buildV2TestNodesPayload missing — deploy updated view_page_model.php',
            'node_details' => [],
            'nodes' => $nodeId,
            'source' => 'v2_test_nodes',
        ], 500);
        exit();
    }

    $payload = $model->buildV2TestNodesPayload($nodeId, $modelNo);
    if (!is_array($payload)) {
        $payload = [
            'status' => false,
            'message' => 'Invalid V2 payload',
            'node_details' => [],
            'nodes' => $nodeId,
            'source' => 'v2_test_nodes',
        ];
    }
    if (!isset($payload['source'])) {
        $payload['source'] = 'v2_test_nodes';
    }

    $ok = !empty($payload['status']) && !empty($payload['node_details']) && is_array($payload['node_details']);
    v2_test_nodes_emit($payload, $ok ? 200 : 404);
} catch (Throwable $exception) {
    error_log('v2_test_nodes failed: ' . $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    v2_test_nodes_emit([
        'status' => false,
        'message' => 'V2 test_nodes failed',
        'error' => $exception->getMessage(),
        'error_file' => basename((string) $exception->getFile()),
        'error_line' => (int) $exception->getLine(),
        'node_details' => [],
        'nodes' => $nodeId,
        'model_no' => $modelNo,
        'source' => 'v2_test_nodes',
    ], 500);
}
exit();
