<?php
namespace App\Routes;

use App\Controllers\Api\V1\AuthController;
use App\Controllers\Api\V1\FileController;
use App\Controllers\Api\V1\FolderController;
use App\Controllers\Api\V1\DashboardController;
use App\Controllers\Api\V1\ShareController;
use App\Controllers\Api\V1\ClusterController;
use App\Controllers\Api\V1\UserController;
use App\Controllers\Api\V1\SyncController;
use App\Controllers\Api\V1\PreviewController;
use App\Controllers\Api\V1\PptPreviewController;
use App\Controllers\Api\V1\DocxPreviewController;
use App\Middleware\AuthMiddleware;

class Api
{
    private static $routes = [];

    public static function add(string $method, string $path, string $controller, string $action, bool $auth = true): void
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action,
            'auth' => $auth
        ];
    }

    public static function register(): void
    {
        // PUBLIC ROUTES (No auth required)
        self::add('POST', '/auth/login', AuthController::class, 'login', false);
        self::add('POST', '/auth/register', AuthController::class, 'register', false);

        // PROTECTED ROUTES
        self::add('POST', '/auth/logout', AuthController::class, 'logout', true);
        self::add('GET', '/auth/me', AuthController::class, 'me', true);

        self::add('GET', '/dashboard', DashboardController::class, 'index', true);
        self::add('GET', '/dashboard/recent', DashboardController::class, 'recent', true);
        self::add('GET', '/dashboard/stats', DashboardController::class, 'stats', true);

        // File routes
        self::add('GET', '/files', FileController::class, 'index', true);
        self::add('POST', '/files', FileController::class, 'store', true);
        self::add('PUT', '/files/{id}', FileController::class, 'update', true);
        self::add('DELETE', '/files/{id}', FileController::class, 'destroy', true);
        self::add('GET', '/files/{id}/download', FileController::class, 'download', true);
        self::add('GET', '/files/{id}/preview', FileController::class, 'download', true);
        self::add('GET', '/files/{id}/info', FileController::class, 'info', true);
        self::add('GET', '/files/{id}/content', FileController::class, 'contentPreview', false);
        self::add('PUT', '/files/{id}/restore', FileController::class, 'restore', true);
        self::add('GET', '/files/download-folder', FileController::class, 'downloadFolder', true);

        // Folder routes
        self::add('GET', '/folders', FolderController::class, 'index', true);
        self::add('POST', '/folders', FolderController::class, 'store', true);
        self::add('PUT', '/folders/{id}', FolderController::class, 'update', true);
        self::add('DELETE', '/folders/{id}', FolderController::class, 'destroy', true);
        self::add('PUT', '/folders/{id}/restore', FolderController::class, 'restore', true);
        self::add('GET', '/folders/{id}/info', FolderController::class, 'info', true);

        // Share routes
        self::add('POST', '/share', ShareController::class, 'store', true);
        self::add('GET', '/share', ShareController::class, 'index', true);
        self::add('PUT', '/share/{id}', ShareController::class, 'update', true);
        self::add('DELETE', '/share/{id}', ShareController::class, 'destroy', true);

        // Cluster routes
        self::add('GET', '/clusters', ClusterController::class, 'index', true);
        self::add('GET', '/users/search', UserController::class, 'search', true);

        // Sync routes
        self::add('GET', '/sync/status', SyncController::class, 'status', true);
        self::add('POST', '/sync/init', SyncController::class, 'init', true);
        self::add('POST', '/sync/start', SyncController::class, 'start', true);
        self::add('POST', '/sync/download', SyncController::class, 'download', true);
        self::add('POST', '/sync/uploaded', SyncController::class, 'uploaded', true);
        self::add('POST', '/sync/repair', SyncController::class, 'repair', true);
        self::add('GET', '/sync/history', SyncController::class, 'history', true);
        self::add('POST', '/sync/cancel', SyncController::class, 'cancel', true);
        self::add('GET', '/sync/folders', SyncController::class, 'folders', true);
        self::add('GET', '/sync/unsynced', SyncController::class, 'unsynced', true);
        self::add('GET', '/sync/logs', SyncController::class, 'logs', true);
        self::add('POST', '/sync/pause', SyncController::class, 'pause', true);
        self::add('POST', '/sync/resume', SyncController::class, 'resume', true);

        // Preview routes
        self::add('GET', '/preview/convert', PreviewController::class, 'convert', true);
        self::add('GET', '/preview/info', PreviewController::class, 'info', true);
        self::add('GET', '/preview/ppt/{id}', PptPreviewController::class, 'preview', true);
        self::add('GET', '/preview/docx/{id}', DocxPreviewController::class, 'preview', true);
    }

    public static function dispatch(string $method, string $uri): void
    {
        // Remove query string
        $uri = strtok($uri, '?');
        
        
        // Find the position of '/api/v1' in the URI
        $pos = strpos($uri, '/api/v1');
        if ($pos !== false) {
            // Extract everything after '/api/v1'
            $path = substr($uri, $pos + 7); // 7 is length of '/api/v1'
            // If path is empty, set to '/'
            if (empty($path)) {
                $path = '/';
            }
        } else {
            // Fallback: remove base path
            $basePath = '/app/file-manager-v3';
            if (strpos($uri, $basePath) === 0) {
                $path = substr($uri, strlen($basePath));
                if (empty($path)) {
                    $path = '/';
                }
            } else {
                $path = $uri;
            }
        }
        
        
        foreach (self::$routes as $route) {
            $pattern = self::convertRouteToRegex($route['path']);
            
            if ($route['method'] === $method && preg_match($pattern, $path, $matches)) {
                
                // Check auth
                if ($route['auth']) {
                    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
                        http_response_code(401);
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => false,
                            'message' => 'Unauthorized - Please login first'
                        ]);
                        return;
                    }
                }

                // ✅ FIX: Extract parameters correctly
                // Remove the full match at index 0, keep only the captured groups
                $params = [];
                foreach ($matches as $key => $value) {
                    // Skip the full match (index 0) and keep only named or numeric indices > 0
                    if (is_int($key) && $key > 0) {
                        $params[] = $value;
                    }
                }
                

                // Instantiate controller and call action
                $controller = new $route['controller']();
                $controller->{$route['action']}(...$params);
                return;
            }
        }

        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Route not found: ' . $method . ' ' . $path]);
    }

    private static function convertRouteToRegex(string $route): string
    {
        // Convert {id} to ([0-9]+) for numeric IDs
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([0-9]+)', $route);
        return '#^' . $pattern . '$#';
    }
}