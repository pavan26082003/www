<?php
namespace App\Helpers\NAS;

class ResponseHelper
{
    public static function success($data = null, string $message = 'Success', int $statusCode = 200): void
    {
        $response = ['success' => true, 'message' => $message];
        if ($data !== null) {
            if (is_array($data)) {
                $response = array_merge($response, $data);
            } else {
                $response['data'] = $data;
            }
        }
        self::json($response, $statusCode);
    }

    public static function error(string $message, int $statusCode = 400, $data = null): void
    {
        $response = ['success' => false, 'message' => $message];
        if ($data !== null) {
            $response['data'] = $data;
        }
        self::json($response, $statusCode);
    }

    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}