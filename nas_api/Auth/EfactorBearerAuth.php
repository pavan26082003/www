<?php

declare(strict_types=1);

namespace NasApi\Auth;

use NasApi\Core\ApiException;
use NasApi\Core\Database;
use PDO;

final class EfactorBearerAuth
{
    private PDO $efactorDb;
    private array $jwtConfig;

    public function __construct()
    {
        $config = $GLOBALS['NAS_API_CONFIG'] ?? [];
        $this->jwtConfig = $config['jwt'] ?? [];
        $this->efactorDb = Database::connect($config['efactor_db'] ?? []);
    }

    /**
     * @return array{efactor_user_id:int,email:?string,phone:?string}
     */
    public function authenticate(): array
    {
        $token = $this->readBearerToken();
        if ($token === '') {
            throw new ApiException(
                'Bearer access token is required.',
                401,
                'bearer_token_required'
            );
        }

        $payload = $this->validateAccessJwt($token);

        $userId = isset($payload['custom:user_id'])
            ? (int) $payload['custom:user_id']
            : 0;

        if ($userId <= 0) {
            throw new ApiException(
                'The Efactor token does not contain a valid custom:user_id.',
                401,
                'invalid_user_claim'
            );
        }

        $stmt = $this->efactorDb->prepare(
            'SELECT id, email, phone, status, deleted, disable_login
               FROM users
              WHERE id = :id
              LIMIT 1'
        );
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new ApiException(
                'The Efactor user does not exist in the local development database.',
                401,
                'efactor_user_not_found'
            );
        }

        if ((string) ($user['status'] ?? '') !== 'active') {
            throw new ApiException(
                'The Efactor account is inactive.',
                403,
                'efactor_user_inactive'
            );
        }

        if ((int) ($user['deleted'] ?? 0) !== 0) {
            throw new ApiException(
                'The Efactor account has been deleted.',
                403,
                'efactor_user_deleted'
            );
        }

        if ((int) ($user['disable_login'] ?? 0) !== 0) {
            throw new ApiException(
                'Login is disabled for this Efactor account.',
                403,
                'efactor_login_disabled'
            );
        }

        return [
            'efactor_user_id' => (int) $user['id'],
            'email' => isset($user['email']) ? (string) $user['email'] : null,
            'phone' => isset($user['phone']) ? (string) $user['phone'] : null,
        ];
    }

    private function readBearerToken(): string
    {
        $authorization = '';

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authorization = trim((string) $_SERVER['HTTP_AUTHORIZATION']);
        }

        if ($authorization === '' && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authorization = trim((string) $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        }

        if ($authorization === '' && function_exists('getallheaders')) {
            $headers = getallheaders();
            if (is_array($headers)) {
                foreach ($headers as $name => $value) {
                    if (strcasecmp((string) $name, 'Authorization') === 0) {
                        $authorization = trim((string) $value);
                        break;
                    }
                }
            }
        }

        if (!preg_match('/^Bearer\s+([^\s]+)$/i', $authorization, $matches)) {
            return '';
        }

        return trim((string) $matches[1]);
    }

    private function validateAccessJwt(string $token): array
    {
        $secret = (string) ($this->jwtConfig['access_secret'] ?? '');
        if ($secret === '') {
            throw new ApiException(
                'EFACTOR_ACCESS_JWT_SECRET is not configured on this local API server.',
                500,
                'jwt_secret_missing'
            );
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new ApiException(
                'The Efactor access token is not a valid JWT.',
                401,
                'invalid_token_format'
            );
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;

        $header = $this->decodeJsonPart($encodedHeader, 'header');
        $payload = $this->decodeJsonPart($encodedPayload, 'payload');

        if (($header['alg'] ?? null) !== 'HS256') {
            throw new ApiException(
                'Unsupported Efactor token algorithm.',
                401,
                'unsupported_token_algorithm'
            );
        }

        $expectedSignature = $this->base64UrlEncode(
            hash_hmac(
                'sha256',
                $encodedHeader . '.' . $encodedPayload,
                $secret,
                true
            )
        );

        if (!hash_equals($expectedSignature, $encodedSignature)) {
            throw new ApiException(
                'The Efactor access token signature is invalid.',
                401,
                'invalid_token_signature'
            );
        }

        $now = time();
        $clockSkew = max(0, (int) ($this->jwtConfig['clock_skew_seconds'] ?? 0));
        $exp = isset($payload['exp']) ? (int) $payload['exp'] : 0;

        if ($exp <= 0) {
            throw new ApiException(
                'The Efactor access token does not contain a valid expiry.',
                401,
                'token_expiry_missing'
            );
        }

        if (($exp + $clockSkew) <= $now) {
            if (!$this->isAllowedLegacyFixedExpiry($payload)) {
                throw new ApiException(
                    'The Efactor access token has expired.',
                    401,
                    'token_expired'
                );
            }

            // The supplied v8/api.php currently hardcodes historical JWT
            // timestamps. Local development may accept only that exact legacy
            // timestamp profile, and only after signature verification passed.
            error_log(
                '[nas_api] Accepted Efactor legacy fixed-expiry token in LOCAL mode. '
                . 'Do not enable this compatibility mode in production.'
            );
        }

        if (isset($payload['iat'])) {
            $iat = (int) $payload['iat'];
            if ($iat > ($now + $clockSkew)) {
                throw new ApiException(
                    'The Efactor access token issue time is in the future.',
                    401,
                    'invalid_token_iat'
                );
            }
        }

        return $payload;
    }


    private function isAllowedLegacyFixedExpiry(array $payload): bool
    {
        $allowed = (bool) ($this->jwtConfig['allow_legacy_fixed_expiry'] ?? false);
        if (!$allowed || !defined('NAS_API_ENV') || NAS_API_ENV !== 'local') {
            return false;
        }

        $expectedAuthTime = (int) ($this->jwtConfig['legacy_auth_time'] ?? 0);
        $expectedIat = (int) ($this->jwtConfig['legacy_iat'] ?? 0);
        $expectedExp = (int) ($this->jwtConfig['legacy_exp'] ?? 0);

        if ($expectedAuthTime <= 0 || $expectedIat <= 0 || $expectedExp <= 0) {
            return false;
        }

        return isset($payload['auth_time'], $payload['iat'], $payload['exp'])
            && (int) $payload['auth_time'] === $expectedAuthTime
            && (int) $payload['iat'] === $expectedIat
            && (int) $payload['exp'] === $expectedExp;
    }

    private function decodeJsonPart(string $encoded, string $partName): array
    {
        $decoded = $this->base64UrlDecode($encoded);
        $json = json_decode($decoded, true);

        if (!is_array($json)) {
            throw new ApiException(
                'The Efactor token ' . $partName . ' is invalid.',
                401,
                'invalid_token_' . $partName
            );
        }

        return $json;
    }

    private function base64UrlDecode(string $value): string
    {
        $value = strtr($value, '-_', '+/');
        $padding = strlen($value) % 4;

        if ($padding !== 0) {
            $value .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($value, true);
        if ($decoded === false) {
            throw new ApiException(
                'Unable to decode the Efactor token.',
                401,
                'invalid_token_encoding'
            );
        }

        return $decoded;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
