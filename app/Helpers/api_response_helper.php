<?php

use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

if (!function_exists('api_error_response')) {
    function api_error_response(string $code, string $message, int $status = 400, array $details = []): ResponseInterface
    {
        $payload = [
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];

        if (!empty($details)) {
            $payload['error']['details'] = $details;
        }

        return Services::response()
            ->setStatusCode($status)
            ->setContentType('application/json', 'utf-8')
            ->setJSON($payload);
    }
}
