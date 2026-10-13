<?php
// public/debug-request.php

echo "=== Debug Request ===\n\n";

echo "1. Request Info:\n";
echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Script: " . $_SERVER['SCRIPT_NAME'] . "\n\n";

echo "2. Loading bootstrap...\n";
require_once __DIR__ . '/../bootstrap/autoload.php';
echo "✅ Bootstrap loaded\n\n";

echo "3. Testing Classes:\n";

try {
    // Test SyncRepository
    $syncRepo = new \App\Repositories\SyncRepository();
    echo "✅ SyncRepository loaded\n";
} catch (Exception $e) {
    echo "❌ SyncRepository error: " . $e->getMessage() . "\n";
}

try {
    // Test SyncService
    $syncService = new \App\Services\SyncService(
        new \App\Repositories\SyncRepository(),
        new \App\Repositories\FileRepository(),
        new \App\Repositories\FolderRepository(),
        new \App\Repositories\UserRepository(),
        new \App\Services\StorageService(),
        new \App\Services\PermissionService(
            new \App\Repositories\FileRepository(),
            new \App\Repositories\FolderRepository(),
            new \App\Repositories\ShareRepository()
        )
    );
    echo "✅ SyncService loaded\n";
} catch (Exception $e) {
    echo "❌ SyncService error: " . $e->getMessage() . "\n";
}

try {
    // Test SyncController
    $controller = new \App\Controllers\Api\V1\SyncController();
    echo "✅ SyncController loaded\n";
} catch (Exception $e) {
    echo "❌ SyncController error: " . $e->getMessage() . "\n";
}

echo "\n4. Testing Database Connection:\n";
global $pdo;
if (isset($pdo)) {
    echo "✅ Database connected\n";
    
    // Test query
    try {
        $stmt = $pdo->query("SELECT 1");
        echo "✅ Database query works\n";
    } catch (Exception $e) {
        echo "❌ Database query error: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Database not connected\n";
}

echo "\n5. Testing Sync Init directly:\n";
try {
    $userId = 2; // Change this to your user ID
    
    $syncRepo = new \App\Repositories\SyncRepository();
    $fileRepo = new \App\Repositories\FileRepository();
    $folderRepo = new \App\Repositories\FolderRepository();
    $userRepo = new \App\Repositories\UserRepository();
    $storageService = new \App\Services\StorageService();
    $permissionService = new \App\Services\PermissionService(
        $fileRepo,
        $folderRepo,
        new \App\Repositories\ShareRepository()
    );
    
    $syncService = new \App\Services\SyncService(
        $syncRepo,
        $fileRepo,
        $folderRepo,
        $userRepo,
        $storageService,
        $permissionService
    );
    
    $result = $syncService->initializeSync($userId);
    echo "✅ Sync init successful!\n";
    echo "Result: " . print_r($result, true) . "\n";
} catch (Exception $e) {
    echo "❌ Sync init error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}