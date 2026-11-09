<?php

/**
 * Shared CORS and access-token checks for the standalone device schema APIs.
 *
 * EFACTOR_SCHEMA_API_AUTH controls token handling:
 *   (unset) log     Serve the request and log callers without a valid token.
 *   enforce         Reject those callers with HTTP 401.
 * Switch to enforce only after the log shows the shipped app sends a valid token.
 */

require_once dirname(__DIR__) . '/app/Libraries/GridWebSso.php';

use App\Libraries\GridWebSso;

function efactor_schema_api_send_cors($methods)
{
    header('Access-Control-Allow-Methods: ' . $methods);
    header('Access-Control-Allow-Headers: Content-Type, Authorization, If-None-Match, If-Modified-Since, Cache-Control');

    $origin = trim((string) ($_SERVER['HTTP_ORIGIN'] ?? ''));
    if ($origin === '') {
        return;
    }

    $allowed = array('https://efactor.tech', 'https://www.efactor.tech');
    foreach (explode(',', GridWebSso::envValue('EFACTOR_ALLOWED_ORIGINS')) as $extra) {
        if (trim($extra) !== '') {
            $allowed[] = strtolower(trim($extra));
        }
    }

    $serverIsLocal = (bool) preg_match('/^(localhost|127\.0\.0\.1)(:\d+)?$/i', (string) ($_SERVER['HTTP_HOST'] ?? ''));
    $originIsLocal = (bool) preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#i', $origin);
    if (in_array(strtolower($origin), $allowed, true) || ($serverIsLocal && $originIsLocal)) {
        header('Access-Control-Allow-Origin: ' . $origin);
    }
}

function efactor_schema_api_request_token()
{
    $header = (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? ''));
    if ($header === '' && function_exists('getallheaders')) {
        foreach ((array) getallheaders() as $name => $value) {
            if (strcasecmp((string) $name, 'Authorization') === 0) {
                $header = (string) $value;
                break;
            }
        }
    }

    $token = trim((string) preg_replace('/^Bearer\s+/i', '', trim($header)));
    return (strlen($token) >= 16 && !in_array(strtoupper($token), array('NULL', 'UNDEFINED'), true)) ? $token : '';
}

/**
 * Stops the request in enforce mode when the caller has no valid access token.
 */
function efactor_schema_api_authorize($db, $endpoint)
{
    $token = efactor_schema_api_request_token();
    $valid = false;
    if ($token !== '') {
        try {
            $safeToken = $db->escape($token);
            $valid = (bool) $db->get_row("SELECT id FROM users WHERE access_token = '$safeToken' AND deleted = 0 LIMIT 1");
        } catch (Throwable $exception) {
            error_log('Schema API token check failed: ' . $exception->getMessage());
        }
    }
    if ($valid) {
        return;
    }

    error_log('Schema API ' . $endpoint . ': ' . ($token === '' ? 'no access token' : 'invalid access token'));

    if (strtolower(GridWebSso::envValue('EFACTOR_SCHEMA_API_AUTH')) !== 'enforce') {
        return;
    }

    http_response_code(401);
    echo json_encode(array('status' => false, 'message' => 'A valid access token is required'));
    exit();
}
