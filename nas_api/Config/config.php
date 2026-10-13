<?php

declare(strict_types=1);

/**
 * Configuration for the isolated mobile/Postman NAS API.
 *
 * IMPORTANT ARCHITECTURE:
 * - nas_api owns mobile API routing/auth/response transport.
 * - Existing app/Services/NAS and app/Repositories/NAS own NAS business/data logic.
 * - Existing Efactor/NAS web files are not modified.
 */

if (!function_exists('nas_api_env')) {
    function nas_api_env(string $name, ?string $default = null): ?string
    {
        $value = getenv($name);
        if ($value === false || $value === '') {
            return $default;
        }

        return $value;
    }
}

$nasApiEnvironment = strtolower((string) nas_api_env('NAS_API_ENV', 'local'));
define('NAS_API_ENV', $nasApiEnvironment);
define('NAS_API_DEBUG', nas_api_env('NAS_API_DEBUG', '1') === '1');

$GLOBALS['NAS_API_CONFIG'] = [
    'shared_app' => [
        // Default when project layout is <root>/app + <root>/nas_api.
        // Override only when nas_api is physically outside the Efactor project.
        'project_root' => (string) nas_api_env(
            'EFACTOR_PROJECT_ROOT',
            dirname(__DIR__, 2)
        ),
    ],

    'jwt' => [
        'access_secret' => (string) nas_api_env(
            'EFACTOR_ACCESS_JWT_SECRET',
            'Efactor_access'
        ),
        'clock_skew_seconds' => 300,
        'allow_legacy_fixed_expiry' => (
            $nasApiEnvironment === 'local'
            && nas_api_env('NAS_API_ALLOW_LEGACY_EFACTOR_EXPIRY', '1') === '1'
        ),
        'legacy_auth_time' => 1667022242,
        'legacy_iat' => 1667022242,
        'legacy_exp' => 1667025842,
    ],

    // Authentication-specific Efactor DB check used by EfactorBearerAuth.
    // NAS database configuration is intentionally NOT duplicated here anymore;
    // NAS data access comes from app/Repositories/NAS/*.
    'efactor_db' => [
        'host' => (string) nas_api_env('EFACTOR_LOCAL_DB_HOST', '127.0.0.1'),
        'port' => (int) nas_api_env('EFACTOR_LOCAL_DB_PORT', '3306'),
        'name' => (string) nas_api_env('EFACTOR_LOCAL_DB_NAME', 'factor_db2'),
        'user' => (string) nas_api_env('EFACTOR_LOCAL_DB_USER', 'root'),
        'pass' => (string) nas_api_env('EFACTOR_LOCAL_DB_PASS', 'mysql'),
        'charset' => 'utf8mb4',
    ],
];
