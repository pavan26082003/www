<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Api extends BaseConfig
{
    /**
     * Comma-separated list of exact browser origins allowed to call /api/*.
     *
     * Example:
     * API_ALLOWED_ORIGINS=http://localhost:5173,https://app.example.com
     *
     * @var string[]
     */
    public $allowedOrigins = [];

    /**
     * Allow http://localhost:<port> and http://127.0.0.1:<port> only in
     * development. This supports Flutter Web's changing local dev port.
     *
     * @var bool
     */
    public $allowLocalhostDevOrigins = false;

    /**
     * Send Access-Control-Allow-Credentials for cross-origin API requests.
     * Keep false unless the API intentionally uses cookies/sessions across
     * origins.
     *
     * @var bool
     */
    public $allowCredentials = false;

    /**
     * @var string
     */
    public $allowedMethods = 'GET, POST, PUT, PATCH, DELETE, OPTIONS';

    /**
     * @var string
     */
    public $allowedHeaders = 'Content-Type, Authorization, Accept, X-Requested-With, X-API-Key';

    /**
     * @var int
     */
    public $maxAge = 86400;

    public function __construct()
    {
        $origins = env('API_ALLOWED_ORIGINS', '');

        $this->allowedOrigins = array_values(array_filter(array_map(static function ($origin) {
            return rtrim(trim($origin), '/');
        }, explode(',', (string) $origins))));

        $this->allowLocalhostDevOrigins = filter_var(
            env('API_ALLOW_LOCALHOST_DEV_ORIGINS', $this->isLocalServer() ? 'true' : 'false'),
            FILTER_VALIDATE_BOOLEAN
        );

        $this->allowCredentials = filter_var(
            env('API_ALLOW_CREDENTIALS', 'false'),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    private function isLocalServer(): bool
    {
        $host = strtolower($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '');
        $host = preg_replace('/:\d+$/', '', $host);

        return ENVIRONMENT === 'development'
            || in_array($host, ['localhost', '127.0.0.1', '::1', '[::1]'], true);
    }
}
