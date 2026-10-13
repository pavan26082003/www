<?php
/**
 * One-shot backfill: create device_ui_schemas for existing models that are missing them.
 * Usage: php v8/backfill_device_ui_schemas.php [limit]
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('memory_limit', '2048M');

$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
$_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] ?? str_replace('\\', '/', dirname(__DIR__));

require_once __DIR__ . '/bootstrap_device_ui_schema.php';

$limit = isset($argv[1]) ? (int) $argv[1] : 200;
if ($limit <= 0) {
    $limit = 200;
}

$result = efactor_backfill_missing_device_ui_schemas($limit);
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
