<?php

namespace App\Controllers\NAS;

use App\Repositories\NAS\UserRepository;
use CodeIgniter\Controller;

abstract class BaseController extends Controller
{
    protected function jsonResponse($data, int $statusCode = 200): void
    {
        if ($statusCode < 100 || $statusCode > 599) {
            $statusCode = 500;
        }

        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=UTF-8');
        }

        echo json_encode(
            $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
        exit;
    }

    protected function success(
        $data = null,
        string $message = 'Success',
        int $statusCode = 200
    ): void {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            if (is_array($data)) {
                $response = array_merge($response, $data);
            } else {
                $response['data'] = $data;
            }
        }

        $this->jsonResponse($response, $statusCode);
    }

    protected function error(
        string $message,
        int $statusCode = 400,
        $data = null
    ): void {
        if ($statusCode < 100 || $statusCode > 599) {
            $statusCode = 500;
        }

        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        $this->jsonResponse($response, $statusCode);
    }

    protected function getUserId(): int
    {
        $session = session();
        $userId = (int) $session->get('nas_user_id');
        $websiteUserId = (int) $session->get('user_id');
        $verifiedEfactorUserId = (int) $session->get('nas_efactor_user_id');

        if (
            $userId <= 0
            || (
                $websiteUserId > 0
                && $verifiedEfactorUserId !== $websiteUserId
            )
        ) {
            throw new \RuntimeException('Unauthorized', 401);
        }

        return $userId;
    }

    protected function getPdo(): \PDO
    {
        return (new UserRepository())->getPdo();
    }

    protected function formatFileSize($size): string
    {
        $size = max(0, (float) $size);
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    protected function getUserStorageInfo(int $userId): array
    {
        $pdo = $this->getPdo();

        $stmt = $pdo->prepare(
            'SELECT storage_used, storage_limit FROM users WHERE id = ?'
        );
        $stmt->execute([$userId]);

        $result = $stmt->fetch();

        if (!$result) {
            return [
                'used' => 0,
                'limit' => 10737418240,
                'used_formatted' => '0 B',
                'limit_formatted' => '10 GB',
                'percentage' => 0,
            ];
        }

        $used = (int) ($result['storage_used'] ?? 0);
        $limit = (int) ($result['storage_limit'] ?? 0);
        $percentage = $limit > 0 ? ($used / $limit) * 100 : 0;

        return [
            'used' => $used,
            'limit' => $limit,
            'used_formatted' => $this->formatFileSize($used),
            'limit_formatted' => $this->formatFileSize($limit),
            'percentage' => $percentage,
        ];
    }
}
