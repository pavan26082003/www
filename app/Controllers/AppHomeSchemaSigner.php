<?php

namespace App\Libraries;

/**
 * Attach Ed25519 signature to published App Home schemas.
 */
class AppHomeSchemaSigner
{
    public static function attachSignature(array $schema): array
    {
        if (empty($schema) || !is_array($schema)) {
            return $schema;
        }

        $secretKey = self::resolveSigningSecretKey();
        if ($secretKey === null) {
            return $schema;
        }

        $unsigned = $schema;
        if (!isset($unsigned['signature']) || !is_array($unsigned['signature'])) {
            $unsigned['signature'] = array();
        }
        unset($unsigned['signature']['value'], $unsigned['signature']['pendingServerSignature']);

        $payload = json_encode($unsigned, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($payload === false) {
            log_message('error', 'App home schema signing failed: json_encode ' . json_last_error_msg());
            return $schema;
        }

        if (!function_exists('sodium_crypto_sign_detached')) {
            log_message('warning', 'App home schema signing skipped: sodium extension unavailable');
            return $schema;
        }

        try {
            $signature = sodium_crypto_sign_detached($payload, $secretKey);
        } catch (\Throwable $e) {
            log_message('error', 'App home schema signing failed: ' . $e->getMessage());
            return $schema;
        }

        if (!isset($schema['signature']) || !is_array($schema['signature'])) {
            $schema['signature'] = array();
        }

        $schema['signature']['algorithm'] = 'Ed25519';
        $schema['signature']['keyId'] = self::keyId();
        $schema['signature']['value'] = base64_encode($signature);
        $schema['signature']['signedAt'] = gmdate('c');
        unset($schema['signature']['pendingServerSignature']);

        return $schema;
    }

    private static function keyId(): string
    {
        $fromEnv = trim((string) (getenv('APP_HOME_SCHEMA_SIGNING_KEY_ID') ?: ''));
        return $fromEnv !== '' ? $fromEnv : 'efactor-ui-key-2026-01';
    }

    private static function resolveSigningSecretKey(): ?string
    {
        $encoded = trim((string) (getenv('APP_HOME_SCHEMA_SIGNING_KEY') ?: ''));
        if ($encoded !== '') {
            $decoded = base64_decode($encoded, true);
            if ($decoded !== false && strlen($decoded) === SODIUM_CRYPTO_SIGN_SECRETKEYBYTES) {
                return $decoded;
            }
            log_message('error', 'APP_HOME_SCHEMA_SIGNING_KEY must be base64-encoded 64-byte Ed25519 secret key');
            return null;
        }

        if (!function_exists('sodium_crypto_sign_seed_keypair')) {
            return null;
        }

        $encryptionKey = '';
        try {
            $encryptionKey = (string) (config('Encryption')->key ?? '');
        } catch (\Throwable $e) {
            $encryptionKey = '';
        }

        if ($encryptionKey === '') {
            return null;
        }

        $seed = hash('sha256', 'efactor-app-home-schema-v3:' . $encryptionKey, true);
        $keyPair = sodium_crypto_sign_seed_keypair(substr($seed, 0, SODIUM_CRYPTO_SIGN_SEEDBYTES));

        return sodium_crypto_sign_secretkey($keyPair);
    }
}
