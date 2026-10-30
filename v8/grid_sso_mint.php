<?php
/**
 * Mint a fresh grid_sso for the current scanner handoff.
 * Accepts the scanner page's existing ?grid_sso= (verify → re-issue).
 * Tokens are short-lived; every scanned URL open must mint a new one
 * on this same host so /grid/connect can verify it.
 */
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function grid_sso_mint_json(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$dir = str_replace('\\', '/', __DIR__);
$docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
$candidates = array(
    $dir . '/../app/Libraries/GridWebSso.php',
    $dir . '/../../app/Libraries/GridWebSso.php',
    $docRoot !== '' ? ($docRoot . '/app/Libraries/GridWebSso.php') : '',
);
$loaded = false;
foreach ($candidates as $path) {
    if ($path === '' || !is_file($path)) {
        continue;
    }
    require_once $path;
    if (class_exists('\\App\\Libraries\\GridWebSso', false)) {
        $loaded = true;
        break;
    }
}

if (!$loaded) {
    grid_sso_mint_json(array('success' => false, 'message' => 'GridWebSso unavailable'), 500);
}

$tokenIn = trim((string) ($_GET['grid_sso'] ?? $_GET['token'] ?? ''));
if ($tokenIn === '') {
    grid_sso_mint_json(array('success' => false, 'message' => 'Missing grid_sso'), 400);
}

$userId = \App\Libraries\GridWebSso::verify($tokenIn);
if (!$userId) {
    grid_sso_mint_json(array('success' => false, 'message' => 'grid_sso rejected'), 401);
}

$fresh = \App\Libraries\GridWebSso::issue((int) $userId);
if ($fresh === '') {
    grid_sso_mint_json(array('success' => false, 'message' => 'Could not mint grid_sso'), 500);
}

$host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
$host = preg_replace('/:\d+$/', '', $host) ?: $host;

grid_sso_mint_json(array(
    'success' => true,
    'grid_sso' => $fresh,
    'param' => \App\Libraries\GridWebSso::QUERY_PARAM,
    'user_id' => (int) $userId,
    'host' => $host,
));
