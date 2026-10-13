<?php
namespace App\Controllers;

abstract class BaseController
{
    protected function jsonResponse($data, int $statusCode = 200): void
    {        
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json');
        }
        
        echo json_encode($data);
        exit;
    }

    protected function success($data = null, string $message = 'Success', int $statusCode = 200): void
    {
        $response = ['success' => true, 'message' => $message];
        if ($data !== null) {
            if (is_array($data)) {
                $response = array_merge($response, $data);
            } else {
                $response['data'] = $data;
            }
        }
        $this->jsonResponse($response, $statusCode);
    }

    protected function error(string $message, int $statusCode = 400, $data = null): void
    {
        $statusCode = is_int($statusCode) ? $statusCode : 500;
    
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json');
        }
        
        $response = ['success' => false, 'message' => $message];
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response);
        exit;
    }

    protected function getUserId(): int
    {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            throw new \Exception('Unauthorized', 401);
        }
        return (int)$_SESSION['user_id'];
    }

    protected function getPdo()
    {
        global $pdo;
        return $pdo;
    }

    protected function formatFileSize($size): string
    {
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
        $stmt = $pdo->prepare("
            SELECT storage_used, storage_limit 
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        if (!$result) {
            return [
                'used' => 0,
                'limit' => 10737418240,
                'used_formatted' => '0 B',
                'limit_formatted' => '10 GB',
                'percentage' => 0
            ];
        }
        
        $used = (int)$result['storage_used'];
        $limit = (int)$result['storage_limit'];
        $percentage = $limit > 0 ? ($used / $limit) * 100 : 0;
        
        return [
            'used' => $used,
            'limit' => $limit,
            'used_formatted' => $this->formatFileSize($used),
            'limit_formatted' => $this->formatFileSize($limit),
            'percentage' => $percentage
        ];
    }
}