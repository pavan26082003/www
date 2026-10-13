<?php
/**
 * First-party QR image for Grid shareQr.
 * Flutter caches this URL; the schema must not point at third-party QR CDNs.
 *
 * GET /v8/qr_image.php?data={payload}&mode={mode}&size=160
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$data = trim((string) ($_GET['data'] ?? $_GET['payload'] ?? ''));
$mode = preg_replace('/[^a-z0-9_-]/i', '', (string) ($_GET['mode'] ?? ''));
$size = (int) ($_GET['size'] ?? 160);
if ($size < 80) {
    $size = 80;
}
if ($size > 512) {
    $size = 512;
}

if ($data === '' || strlen($data) > 2048) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'success' => false,
        'message' => 'data is required and must be 2048 characters or fewer.',
    ));
    exit;
}

$cacheDirCandidates = array(
    dirname(__DIR__) . '/files/cache/qr',
    __DIR__ . '/cache/qr',
    rtrim(sys_get_temp_dir(), '/\\') . '/efactor_qr',
);
$cacheDir = null;
foreach ($cacheDirCandidates as $dir) {
    if (is_dir($dir) || @mkdir($dir, 0775, true)) {
        $cacheDir = $dir;
        break;
    }
}

$hash = sha1($data . '|' . $mode . '|' . $size);
$cacheFile = $cacheDir ? ($cacheDir . '/qr_' . $hash . '.png') : '';
if ($cacheFile !== '' && is_file($cacheFile) && filesize($cacheFile) > 32) {
    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=2592000, immutable');
    header('Content-Length: ' . filesize($cacheFile));
    readfile($cacheFile);
    exit;
}

$encoded = rawurlencode($data);
$sources = array(
    'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&margin=8&ecc=M&data=' . $encoded,
);

$png = '';
foreach ($sources as $url) {
    $png = qr_image_fetch($url);
    if (is_string($png) && strlen($png) > 32 && strncmp($png, "\x89PNG", 4) === 0) {
        break;
    }
    $png = '';
}

if ($png === '') {
    http_response_code(502);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'success' => false,
        'message' => 'QR image could not be generated.',
    ));
    exit;
}

if ($cacheFile !== '') {
    @file_put_contents($cacheFile, $png);
}

header('Content-Type: image/png');
header('Cache-Control: public, max-age=2592000, immutable');
header('Content-Length: ' . strlen($png));
echo $png;

function qr_image_fetch($url)
{
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        if ($curl !== false) {
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
            ));
            $raw = curl_exec($curl);
            $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if (is_string($raw) && $raw !== '' && $httpCode >= 200 && $httpCode < 400) {
                return $raw;
            }
        }
    }
    $context = stream_context_create(array(
        'http' => array(
            'timeout' => 15,
            'ignore_errors' => true,
        ),
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
        ),
    ));
    $raw = @file_get_contents($url, false, $context);
    return is_string($raw) ? $raw : '';
}
