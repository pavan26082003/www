<?php

declare(strict_types=1);

namespace NasApi\Core;

final class JsonResponse
{
    public static function send(array $body, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        header('Pragma: no-cache');

        echo json_encode(
            $body,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
        exit;
    }
}
