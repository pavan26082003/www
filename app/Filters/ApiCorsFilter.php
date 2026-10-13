<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Api;

class ApiCorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('api_response');

        $origin = rtrim(trim($request->getHeaderLine('Origin')), '/');
        $method = strtoupper($request->getMethod());

        if ($origin === '') {
            if ($method === 'OPTIONS') {
                return service('response')
                    ->setStatusCode(204)
                    ->setContentType('application/json', 'utf-8')
                    ->setBody('');
            }

            return null;
        }

        if (!$this->isOriginAllowed($origin)) {
            return api_error_response(
                'CORS_ORIGIN_NOT_ALLOWED',
                'The request origin is not allowed.',
                403
            );
        }

        $response = service('response');
        $this->applyCorsHeaders($response, $origin);

        if ($method === 'OPTIONS') {
            return $response
                ->setStatusCode(204)
                ->setContentType('application/json', 'utf-8')
                ->setBody('');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $origin = rtrim(trim($request->getHeaderLine('Origin')), '/');

        if ($origin !== '' && $this->isOriginAllowed($origin)) {
            $this->applyCorsHeaders($response, $origin);
        }

        if (!$response->hasHeader('Content-Type')) {
            $response->setContentType('application/json', 'utf-8');
        }

        return $response;
    }

    private function applyCorsHeaders(ResponseInterface $response, string $origin): void
    {
        $config = config(Api::class);

        $response
            ->setHeader('Access-Control-Allow-Origin', $origin)
            ->setHeader('Access-Control-Allow-Methods', $config->allowedMethods)
            ->setHeader('Access-Control-Allow-Headers', $config->allowedHeaders)
            ->setHeader('Access-Control-Max-Age', (string) $config->maxAge)
            ->setHeader('Vary', 'Origin');

        if ($config->allowCredentials) {
            $response->setHeader('Access-Control-Allow-Credentials', 'true');
        }
    }

    private function isOriginAllowed(string $origin): bool
    {
        $config = config(Api::class);

        if (in_array($origin, $config->allowedOrigins, true)) {
            return true;
        }

        return $config->allowLocalhostDevOrigins && $this->isDevelopmentLocalhostOrigin($origin);
    }

    private function isDevelopmentLocalhostOrigin(string $origin): bool
    {
        $parts = parse_url($origin);

        if (!is_array($parts)) {
            return false;
        }

        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');
        $port = isset($parts['port']) ? (int) $parts['port'] : null;

        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        if (!in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return false;
        }

        return $port === null || ($port >= 1 && $port <= 65535);
    }
}
