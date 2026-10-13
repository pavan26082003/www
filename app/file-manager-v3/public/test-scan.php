<?php
// public/test-scan.php

require_once __DIR__ . '/../bootstrap/autoload.php';

header('Content-Type: text/plain');

echo "=== TEST SCAN LOCAL FOLDER ===\n\n";

$userId = 2;
$userRepo = new \App\Repositories\UserRepository();
$user = $userRepo->find($userId);

if (!$user) {
    echo "❌ User not found\n";
    exit;
}

echo "User: " . $user['username'] . " (ID: " . $userId . ")\n";

$syncService = new \App\Services\SyncService(
    new \App\Repositories\SyncRepository(),
    new \App\Repositories\FileRepository(),
    new \App\Repositories\FolderRepository(),
    $userRepo,
    new \App\Services\StorageService(),
    new \App\Services\PermissionService(
        new \App\Repositories\FileRepository(),
        new \App\Repositories\FolderRepository(),
        new \App\Repositories\ShareRepository()
    )
);

// Get user sync path
$userSyncPath = getenv('USERPROFILE') . '/eFactor/my-drive/' . $user['username'];
echo "Sync path: " . $userSyncPath . "\n\n";

// Check if path exists
if (!is_dir($userSyncPath)) {
    echo "❌ Sync path does not exist\n";
    exit;
}

// List directory structure
echo "=== DIRECTORY STRUCTURE ===\n";
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($userSyncPath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $path) {
    if ($path->isDir()) {
        echo "📁 " . $path->getPathname() . "\n";
    } else {
        echo "  📄 " . $path->getPathname() . "\n";
    }
}

echo "\n=== SCANNING WITH scanLocalFolder() ===\n";

$reflection = new ReflectionClass($syncService);
$method = $reflection->getMethod('scanLocalFolder');
$method->setAccessible(true);

$files = $method->invoke($syncService, $userSyncPath);

echo "Found " . count($files) . " files:\n";
foreach ($files as $file) {
    echo "  - " . $file['relative_path'] . " (" . $file['size'] . " bytes)\n";
}

echo "\n=== DONE ===\n";