<?php

/**
 * LOCAL-ONLY helper.
 * Reads an Efactor access token and prints only configuration claims needed by
 * the NAS bridge. This DOES NOT validate the token and must never be used as
 * authentication logic.
 */

fwrite(STDOUT, "Paste Efactor accesstoken, then press Enter:\n");
$token = trim((string) fgets(STDIN));

if ($token === '') {
    fwrite(STDERR, "No token supplied.\n");
    exit(1);
}

if (stripos($token, 'Bearer ') === 0) {
    $token = trim(substr($token, 7));
}

$parts = explode('.', $token);
if (count($parts) !== 3) {
    fwrite(STDERR, "Token is not a three-part JWT.\n");
    exit(1);
}

$payloadPart = $parts[1];
$remainder = strlen($payloadPart) % 4;
if ($remainder !== 0) {
    $payloadPart .= str_repeat('=', 4 - $remainder);
}

$json = base64_decode(strtr($payloadPart, '-_', '+/'), true);
$payload = $json !== false ? json_decode($json, true) : null;

if (!is_array($payload)) {
    fwrite(STDERR, "Unable to decode JWT payload.\n");
    exit(1);
}

$result = [
    'token_use' => $payload['token_use'] ?? null,
    'iss' => $payload['iss'] ?? null,
    'client_id' => $payload['client_id'] ?? null,
    'exp' => $payload['exp'] ?? null,
];

fwrite(
    STDOUT,
    json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
);
