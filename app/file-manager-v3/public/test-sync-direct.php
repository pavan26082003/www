<?php
// public/test-sync-direct.php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

try {
    echo "=== Starting direct sync test ===\n\n";
    
    // Load bootstrap
    require_once __DIR__ . '/../bootstrap/autoload.php';
    echo "✅ Bootstrap loaded\n";
    
    // Check session
    echo "Session status: " . session_status() . "\n";
    echo "User ID in session: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
    
    // Get user
    $userId = $_SESSION['user_id'] ?? 1;
    echo "Using user ID: $userId\n";
    
    // Create repositories
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
    
    // Create sync service
    $syncService = new \App\Services\SyncService(
        $syncRepo,
        $fileRepo,
        $folderRepo,
        $userRepo,
        $storageService,
        $permissionService
    );
    echo "✅ Sync service created\n";
    
    // Check user
    $user = $userRepo->find($userId);
    echo "User found: " . ($user ? 'YES' : 'NO') . "\n";
    if ($user) {
        echo "Username: " . $user['username'] . "\n";
    }
    
    // Initialize sync
    echo "\nCalling initializeSync...\n";
    $result = $syncService->initializeSync($userId);
    
    echo "\n✅ Sync initialized successfully!\n";
    echo json_encode($result, JSON_PRETTY_PRINT);
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}