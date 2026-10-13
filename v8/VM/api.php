<?php
/**
 * Centralized API Gateway for KVM Virtual Machine Manager
 * Updated to match exact manual command structure
 *
 * @package KVM_VM_Manager
 * @version 3.1.0
 * @author System Administrator
 * @license Proprietary
 */

// ============================================
// PORTABLE INITIALIZATION / CONFIGURATION
// ============================================

/**
 * This API is intentionally standalone: it has no database dependency.
 * Configuration is read from environment variables and optional .env files.
 * Local files (logs/test state) are always stored relative to this api.php.
 */
define('BASE_DIR', __DIR__);
define('LOG_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'logs');
define('DATA_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'data');
define('LOG_FILE', LOG_DIR . DIRECTORY_SEPARATOR . 'api.log');
define('COMMAND_LOG_FILE', LOG_DIR . DIRECTORY_SEPARATOR . 'commands.log');
define('ERROR_LOG_FILE', LOG_DIR . DIRECTORY_SEPARATOR . 'error.log');
define('TEST_STATE_FILE', DATA_DIR . DIRECTORY_SEPARATOR . 'kvm_test_state.json');

function loadSimpleEnvFile(string $file): void
{
    if (!is_file($file) || !is_readable($file)) {
        return;
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if ($key === '' || getenv($key) !== false) {
            continue; // real environment variables take precedence
        }

        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
    }
}

// Look beside api.php first, then one directory above it.
loadSimpleEnvFile(BASE_DIR . DIRECTORY_SEPARATOR . '.env');
loadSimpleEnvFile(dirname(BASE_DIR) . DIRECTORY_SEPARATOR . '.env');

function cfg(string $key, $default = null)
{
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return $value;
    }
    return $default;
}

function cfgBool(string $key, bool $default = false): bool
{
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }
    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
}

function cfgCsv(string $key, array $default = []): array
{
    $raw = cfg($key, '');
    if ($raw === '') {
        return $default;
    }
    return array_values(array_filter(array_map('trim', explode(',', $raw)), static fn($v) => $v !== ''));
}

foreach ([LOG_DIR, DATA_DIR] as $dir) {
    if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
        // Do not fail the API solely because logging/test-state directories cannot be created.
        // The exception handler will still return JSON if a later operation fails.
    }
}

// On Windows AMPPS, test mode defaults to ON because KVM/libvirt is normally remote.
// On Linux, production/real mode defaults to OFF unless KVM_TEST_MODE is explicitly enabled.
define('KVM_TEST_MODE', cfgBool('KVM_TEST_MODE', PHP_OS_FAMILY === 'Windows'));
define('KVM_EXEC_MODE', strtolower((string) cfg('KVM_EXEC_MODE', 'ssh'))); // ssh | local

define('KVM_HOST', (string) cfg('KVM_HOST', cfg('KVM_SERVER_IP', '192.168.10.80')));
define('KVM_USER', (string) cfg('KVM_USER', cfg('KVM_SSH_USERNAME', 'root')));
define('KVM_SSH_PORT', (int) cfg('KVM_SSH_PORT', 22));
define('KVM_SSH_KEY', (string) cfg('KVM_SSH_KEY', cfg('KVM_SSH_PRIVATE_KEY', '')));
define('KVM_SSH_BINARY', (string) cfg('KVM_SSH_BINARY', 'ssh'));
define('KVM_SSH_CONNECT_TIMEOUT', (int) cfg('KVM_SSH_CONNECT_TIMEOUT', 10));
define('KVM_SSH_STRICT_HOST_KEY_CHECKING', (string) cfg('KVM_SSH_STRICT_HOST_KEY_CHECKING', 'accept-new'));
define('KVM_SSH_KNOWN_HOSTS', (string) cfg('KVM_SSH_KNOWN_HOSTS', ''));
define('KVM_API_TOKEN', (string) cfg('KVM_API_TOKEN', ''));

// These are paths on the KVM host, not paths on the Windows/PHP machine.
// Every value can be overridden in .env for another server layout.
define('DEFAULT_IMAGE_PATH', rtrim((string) cfg('KVM_DEFAULT_IMAGE_PATH', '/vm_storage/images'), '/\\') . '/');
define('DEFAULT_ISO_PATH', rtrim((string) cfg('KVM_DEFAULT_ISO_PATH', '/vm_storage/iso'), '/\\') . '/');
define('OS_TEMPLATE_PATH', rtrim((string) cfg('KVM_OS_TEMPLATE_PATH', '/vm_storage/templates/os'), '/\\') . '/');
define('DB_PACKAGES_PATH', rtrim((string) cfg('KVM_DB_PACKAGES_PATH', '/vm_storage/database-packages'), '/\\') . '/');
define('REMOTE_TMP_PATH', rtrim((string) cfg('KVM_REMOTE_TMP_PATH', '/tmp'), '/\\'));

// Test-mode capacity. No real resources are consumed.
define('TEST_CPU_CORES', (int) cfg('KVM_TEST_CPU_CORES', 16));
define('TEST_MEMORY_MB', (int) cfg('KVM_TEST_MEMORY_MB', 65536));
define('TEST_STORAGE_TOTAL_GB', (float) cfg('KVM_TEST_STORAGE_TOTAL_GB', 1000));
define('TEST_STORAGE_USED_GB', (float) cfg('KVM_TEST_STORAGE_USED_GB', 300));

// ============================================
// ERROR HANDLING
// ============================================

// Enable strict error reporting for production
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', ERROR_LOG_FILE);

// Set JSON content type header
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') { http_response_code(204); exit; }

/**
 * Custom exception handler - DO NOT expose internal errors to client
 */
function handleException(Throwable $exception): void
{
    $errorMessage = sprintf(
        '[%s] Exception: %s in %s:%d',
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    );
    error_log($errorMessage . "\n", 3, LOG_FILE);
    error_log($errorMessage . "\n", 3, ERROR_LOG_FILE);

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal Server Error',
        'error_id' => uniqid('err_', true),
    ]);
    exit;
}

/**
 * Custom error handler - DO NOT expose internal errors to client
 */
function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
{
    if (!(error_reporting() & $errno)) return false;
    $errorMessage = sprintf(
        '[%s] Error: %s in %s:%d',
        date('Y-m-d H:i:s'),
        $errstr,
        $errfile,
        $errline
    );
    error_log($errorMessage . "\n", 3, LOG_FILE);
    error_log($errorMessage . "\n", 3, ERROR_LOG_FILE);

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

// Set error and exception handlers
set_exception_handler('handleException');
set_error_handler('handleError');

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Log API request for auditing purposes
 */
function logRequest(string $action, array $params = []): void
{
    foreach (['password','ssh_password','token','authorization'] as $sensitiveKey) {
        if (array_key_exists($sensitiveKey, $params)) $params[$sensitiveKey] = '[REDACTED]';
    }
    $logData = sprintf(
        '[%s] Action: %s | Method: %s | IP: %s | Params: %s',
        date('Y-m-d H:i:s'),
        $action,
        $_SERVER['REQUEST_METHOD'],
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        json_encode($params)
    );
    error_log($logData . "\n", 3, LOG_FILE);
}

/**
 * Log command execution for troubleshooting
 */
function logCommand(string $command, int $status, string $output): void
{
    $logData = sprintf(
        '[%s] Command: %s | Status: %d | Output: %s',
        date('Y-m-d H:i:s'),
        $command,
        $status,
        trim($output)
    );
    error_log($logData . "\n", 3, COMMAND_LOG_FILE);
}

/**
 * Sanitize input data
 */
function sanitizeInput(array $data): array
{
    $sanitized = [];
    foreach ($data as $key => $value) {
        if (is_string($value)) {
            $sanitized[$key] = trim(htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8'));
        } elseif (is_array($value)) {
            $sanitized[$key] = sanitizeInput($value);
        } elseif (is_numeric($value)) {
            $sanitized[$key] = filter_var($value, FILTER_VALIDATE_FLOAT) !== false ? (float) $value : (int) $value;
        } elseif (is_bool($value)) {
            $sanitized[$key] = (bool) $value;
        } else {
            $sanitized[$key] = $value;
        }
    }
    return $sanitized;
}

/**
 * Validate request method
 */
function validateRequestMethod(string $action, array $allowedMethods): void
{
    $currentMethod = $_SERVER['REQUEST_METHOD'];
    if (!in_array($currentMethod, $allowedMethods, true)) {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => sprintf('Method %s not allowed for action %s', $currentMethod, $action),
            'allowed_methods' => $allowedMethods,
        ]);
        exit;
    }
}

/**
 * Get request parameters based on method
 */
function getRequestParams(): array
{
    $params = [];
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        $params = $_GET;
    } elseif ($method === 'POST') {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $input = file_get_contents('php://input');
            if ($input) {
                $jsonData = json_decode($input, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                    $params = $jsonData;
                }
            }
        } else {
            $params = $_POST;
        }
    }

    return sanitizeInput($params);
}

/**
 * Run a local process with a timeout in a platform-neutral way.
 */
function runLocalProcess(string $command, int $timeout = 300): array
{
    if (!function_exists('proc_open')) {
        $output = [];
        $status = 0;
        exec($command . ' 2>&1', $output, $status);
        return [
            'status' => $status,
            'output' => implode("\n", $output),
            'output_array' => $output,
            'success' => ($status === 0),
        ];
    }

    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = @proc_open($command, $descriptors, $pipes);
    if (!is_resource($process)) {
        return ['status' => -1, 'output' => 'Unable to start process', 'output_array' => [], 'success' => false];
    }

    fclose($pipes[0]);
    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);

    $stdout = '';
    $stderr = '';
    $start = microtime(true);
    $timedOut = false;

    while (true) {
        $stdout .= stream_get_contents($pipes[1]);
        $stderr .= stream_get_contents($pipes[2]);
        $statusInfo = proc_get_status($process);

        if (!$statusInfo['running']) {
            break;
        }
        if ((microtime(true) - $start) >= $timeout) {
            $timedOut = true;
            proc_terminate($process);
            break;
        }
        usleep(100000);
    }

    $stdout .= stream_get_contents($pipes[1]);
    $stderr .= stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    if ($timedOut) {
        $exitCode = 124;
        $stderr .= ($stderr !== '' ? "\n" : '') . "Command timed out after {$timeout} seconds";
    }

    $combined = trim($stdout . ($stderr !== '' ? "\n" . $stderr : ''));
    return [
        'status' => $exitCode,
        'output' => $combined,
        'output_array' => $combined === '' ? [] : preg_split('/\R/', $combined),
        'success' => ($exitCode === 0),
    ];
}

/**
 * Execute a command on the configured KVM host.
 * - KVM_EXEC_MODE=ssh: use the operating system's OpenSSH client.
 * - KVM_EXEC_MODE=local: execute directly (for a PHP API running on the KVM host itself).
 * No PHP ssh2 extension is required.
 */
function executeCommand(string $command, bool $logOutput = true, int $timeout = 300): array
{
    if (KVM_TEST_MODE) {
        return [
            'status' => 0,
            'output' => 'SIMULATED',
            'output_array' => ['SIMULATED'],
            'success' => true,
            'test_mode' => true,
        ];
    }

    if (KVM_EXEC_MODE === 'local') {
        $result = runLocalProcess($command, $timeout);
        if ($logOutput) {
            logCommand('[LOCAL] ' . $command, $result['status'], $result['output']);
        }
        return $result;
    }

    if (KVM_EXEC_MODE !== 'ssh') {
        return ['status' => -1, 'output' => 'Invalid KVM_EXEC_MODE. Use ssh or local.', 'output_array' => [], 'success' => false];
    }

    if (KVM_SSH_KEY !== '' && !is_file(KVM_SSH_KEY)) {
        $error = 'Configured SSH private key was not found: ' . KVM_SSH_KEY;
        error_log($error . PHP_EOL, 3, ERROR_LOG_FILE);
        return ['status' => -1, 'output' => $error, 'output_array' => [$error], 'success' => false];
    }

    $parts = [];
    $parts[] = escapeshellarg(KVM_SSH_BINARY);
    $parts[] = '-p ' . (int) KVM_SSH_PORT;
    $parts[] = '-o BatchMode=yes';
    $parts[] = '-o ConnectTimeout=' . max(1, KVM_SSH_CONNECT_TIMEOUT);
    $parts[] = '-o StrictHostKeyChecking=' . escapeshellarg(KVM_SSH_STRICT_HOST_KEY_CHECKING);

    if (KVM_SSH_KNOWN_HOSTS !== '') {
        $parts[] = '-o UserKnownHostsFile=' . escapeshellarg(KVM_SSH_KNOWN_HOSTS);
    }
    if (KVM_SSH_KEY !== '') {
        $parts[] = '-i ' . escapeshellarg(KVM_SSH_KEY);
    }

    $parts[] = escapeshellarg(KVM_USER . '@' . KVM_HOST);
    $parts[] = escapeshellarg($command);
    $sshCommand = implode(' ', $parts);

    $result = runLocalProcess($sshCommand, $timeout);
    if ($logOutput) {
        // Do not log credentials; only key path/target/command are present.
        logCommand($sshCommand, $result['status'], $result['output']);
    }
    return $result;
}

/**
 * Format bytes to human readable format
 */
function formatBytes(int $bytes, int $precision = 2): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Parse human readable size to bytes
 */
function parseSize(string $size): int
{
    $size = trim($size);
    
    // Handle GiB, MiB, KiB format
    if (preg_match('/^([\d.]+)\s*([KMG])i?B?$/i', $size, $matches)) {
        $value = (float) $matches[1];
        $unit = strtoupper($matches[2]);
        $multiplier = 1024;
        
        switch ($unit) {
            case 'K': return (int) ($value * $multiplier);
            case 'M': return (int) ($value * $multiplier * $multiplier);
            case 'G': return (int) ($value * $multiplier * $multiplier * $multiplier);
            case 'T': return (int) ($value * $multiplier * $multiplier * $multiplier * $multiplier);
            default: return (int) $value;
        }
    }
    
    // Handle GB, MB, KB format (decimal)
    if (preg_match('/^([\d.]+)\s*([KMG])B?$/i', $size, $matches)) {
        $value = (float) $matches[1];
        $unit = strtoupper($matches[2]);
        $multiplier = 1000;
        
        switch ($unit) {
            case 'K': return (int) ($value * $multiplier);
            case 'M': return (int) ($value * $multiplier * $multiplier);
            case 'G': return (int) ($value * $multiplier * $multiplier * $multiplier);
            case 'T': return (int) ($value * $multiplier * $multiplier * $multiplier * $multiplier);
            default: return (int) $value;
        }
    }
    
    // Handle plain numbers (assume bytes)
    return (int) $size;
}

/**
 * Generate UUID
 */
function generateUuid(): string
{
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

/**
 * Generate MAC address
 */
function generateMacAddress(): string
{
    $prefix = '52:54:00';
    $suffix = sprintf('%02x:%02x:%02x',
        mt_rand(0, 255),
        mt_rand(0, 255),
        mt_rand(0, 255)
    );
    return "{$prefix}:{$suffix}";
}

/**
 * Format uptime
 */
function formatUptime(int $seconds): string
{
    $days = floor($seconds / 86400);
    $hours = floor(($seconds % 86400) / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    
    $parts = [];
    if ($days > 0) $parts[] = $days . 'd';
    if ($hours > 0) $parts[] = $hours . 'h';
    if ($minutes > 0) $parts[] = $minutes . 'm';
    
    return implode(' ', $parts) ?: '0m';
}

/**
 * Check if VM exists on remote host
 */
function vmExists(string $vmName): bool
{
    $result = executeCommand("virsh dominfo " . escapeshellarg($vmName), false);
    return $result['success'];
}

/**
 * Validate common VM parameters
 */
function validateVmParams(array $params): array
{
    $errors = [];
    
    // VM name validation
    if (!isset($params['vm_name']) || empty($params['vm_name'])) {
        $errors[] = 'VM name is required';
    } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $params['vm_name'])) {
        $errors[] = 'VM name contains invalid characters. Use only alphanumeric, dash, and underscore.';
    }
    
    // CPU validation
    if (isset($params['vcpus'])) {
        $vcpus = (int) $params['vcpus'];
        if ($vcpus < 1 || $vcpus > 64) {
            $errors[] = 'CPU cores must be between 1 and 64';
        }
    }
    
    // Memory validation
    if (isset($params['memory'])) {
        $memory = (int) $params['memory'];
        if ($memory < 512 || $memory > 1048576) {
            $errors[] = 'Memory must be between 512 MB and 1048576 MB';
        }
    }
    
    // Disk size validation
    if (isset($params['disk_size'])) {
        $diskSize = (int) $params['disk_size'];
        if ($diskSize < 1 || $diskSize > 10240) {
            $errors[] = 'Disk size must be between 1 GB and 10240 GB';
        }
    }
    
    return $errors;
}

// ============================================
// ROUTER CONFIGURATION
// ============================================

/**
 * Action routing definition
 * Maps action names to handler functions
 */
const ACTION_ROUTES = [
    'health' => ['handler' => 'handleHealth', 'methods' => ['GET']],
    'system_overview' => ['handler' => 'handleSystemOverview', 'methods' => ['GET']],
    'list_vms' => ['handler' => 'handleListVmsPortable', 'methods' => ['GET']],
    'vm_action' => ['handler' => 'handleVmActionPortable', 'methods' => ['POST']],
    'edit_vm' => ['handler' => 'handleEditVmPortable', 'methods' => ['POST']],
    'edit_disk' => ['handler' => 'handleEditDiskPortable', 'methods' => ['POST']],
    'delete_disk' => ['handler' => 'handleDeleteDiskPortable', 'methods' => ['POST']],
    'create' => ['handler' => 'handleCreateVm', 'methods' => ['POST']],
    'validate_vm_name' => ['handler' => 'handleValidateVmName', 'methods' => ['POST']],
    'get_iso_list' => ['handler' => 'handleGetIsoList', 'methods' => ['GET']],
    'get_storage_pools' => ['handler' => 'handleGetStoragePools', 'methods' => ['GET']],
    'get_networks' => ['handler' => 'handleGetNetworks', 'methods' => ['GET']],
    'get_host_resources' => ['handler' => 'handleGetHostResources', 'methods' => ['GET']],
    'calculate_disk_space' => ['handler' => 'handleCalculateDiskSpace', 'methods' => ['POST']],
    'preview_vm_configuration' => ['handler' => 'handlePreviewVmConfiguration', 'methods' => ['POST']],
    'create_disk' => ['handler' => 'handleCreateDisk', 'methods' => ['POST']],
    'attach_iso' => ['handler' => 'handleAttachIso', 'methods' => ['POST']],
    'generate_vm_command' => ['handler' => 'handleGenerateVmCommand', 'methods' => ['POST']],
    'create_vm' => ['handler' => 'handleCreateVm', 'methods' => ['POST']],
    'create_vm_without_disk' => ['handler' => 'handleCreateVmWithoutDisk', 'methods' => ['POST']],
    'get_creation_status' => ['handler' => 'handleGetCreationStatus', 'methods' => ['GET']],
    'cancel_creation' => ['handler' => 'handleCancelCreation', 'methods' => ['POST']],
    'get_os_variants' => ['handler' => 'handleGetOsVariants', 'methods' => ['GET']],
    'list_bridges' => ['handler' => 'handleListBridges', 'methods' => ['GET']],
    'get_storage_usage' => ['handler' => 'handleGetStorageUsage', 'methods' => ['GET']],
    'check_iso_exists' => ['handler' => 'handleCheckIsoExists', 'methods' => ['POST']],
    'check_cpu_availability' => ['handler' => 'handleCheckCpuAvailability', 'methods' => ['POST']],
    'check_memory_availability' => ['handler' => 'handleCheckMemoryAvailability', 'methods' => ['POST']],
    'create_windows_vm' => ['handler' => 'handleCreateWindowsVm', 'methods' => ['POST']],
    // TEMPLATE APIs
    'get_os_templates' => ['handler' => 'handleGetOsTemplates', 'methods' => ['GET']],
    'get_database_templates' => ['handler' => 'handleGetDatabaseTemplates', 'methods' => ['GET']],
    'get_templates' => ['handler' => 'handleGetTemplates', 'methods' => ['GET']],
];


// ============================================
// LOCAL TEST MODE (NO SSH / NO DATABASE)
// ============================================

function defaultTestState(): array
{
    return ['vms' => [], 'disks' => []];
}

function readTestState(): array
{
    if (!is_file(TEST_STATE_FILE)) {
        return defaultTestState();
    }
    $raw = @file_get_contents(TEST_STATE_FILE);
    $data = $raw ? json_decode($raw, true) : null;
    return is_array($data) ? array_merge(defaultTestState(), $data) : defaultTestState();
}

function writeTestState(array $state): bool
{
    if (!is_dir(DATA_DIR) && !@mkdir(DATA_DIR, 0755, true) && !is_dir(DATA_DIR)) {
        return false;
    }
    return @file_put_contents(TEST_STATE_FILE, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX) !== false;
}

function testIpForName(string $name): string
{
    $n = (abs(crc32($name)) % 200) + 20;
    return '192.168.100.' . $n;
}

function testVmExists(string $name, array $state): bool
{
    foreach ($state['vms'] as $vm) {
        if (($vm['name'] ?? '') === $name) return true;
    }
    return false;
}

function generateVirtInstallCommandPortable(array $params): string
{
    return generateVirtInstallCommand($params);
}

function testDispatch(string $action, array $params): array
{
    $state = readTestState();
    $totalBytes = (int) round(TEST_STORAGE_TOTAL_GB * 1073741824);
    $usedBytes = (int) round(TEST_STORAGE_USED_GB * 1073741824);
    foreach ($state['disks'] as $disk) {
        $usedBytes += (int) round(((float)($disk['allocated_gb'] ?? 0)) * 1073741824);
    }
    $freeBytes = max(0, $totalBytes - $usedBytes);

    switch ($action) {
        case 'health':
            return ['success' => true, 'test_mode' => true, 'message' => 'Portable KVM API is working in LOCAL TEST MODE. No SSH or database is used.', 'platform' => PHP_OS_FAMILY, 'api_file' => __FILE__];

        case 'get_iso_list':
            return ['success' => true, 'test_mode' => true, 'message' => 'ISO list retrieved successfully (simulated)', 'data' => ['iso_files' => [
                ['name' => 'ubuntu-22.04.iso', 'path' => DEFAULT_ISO_PATH . 'ubuntu-22.04.iso', 'size' => 4900000000, 'size_human' => '4.56 GB', 'modified' => date('Y-m-d')],
                ['name' => 'AlmaLinux-9.8-x86_64-dvd.iso', 'path' => DEFAULT_ISO_PATH . 'AlmaLinux-9.8-x86_64-dvd.iso', 'size' => 11000000000, 'size_human' => '10.24 GB', 'modified' => date('Y-m-d')],
                ['name' => 'Windows.iso', 'path' => DEFAULT_ISO_PATH . 'Windows.iso', 'size' => 6500000000, 'size_human' => '6.05 GB', 'modified' => date('Y-m-d')],
            ], 'count' => 3]];

        case 'get_storage_pools':
            return ['success' => true, 'test_mode' => true, 'message' => 'Storage pools retrieved successfully (simulated)', 'data' => ['pools' => [[
                'name' => 'vm_storage', 'path' => DEFAULT_IMAGE_PATH, 'type' => 'dir', 'state' => 'active', 'available' => true,
                'free_space' => $freeBytes, 'free_space_human' => formatBytes($freeBytes),
                'total_space' => $totalBytes, 'total_space_human' => formatBytes($totalBytes),
                'used_space' => $usedBytes, 'used_space_human' => formatBytes($usedBytes),
            ]], 'count' => 1]];

        case 'get_networks':
            return ['success' => true, 'test_mode' => true, 'message' => 'Networks retrieved successfully (simulated)', 'data' => ['networks' => [
                ['name' => 'default', 'type' => 'virtual', 'state' => 'active', 'bridge' => 'virbr0', 'interfaces' => []],
                ['name' => 'virbr0', 'type' => 'bridge', 'state' => 'active', 'bridge' => 'virbr0', 'interfaces' => []],
            ], 'count' => 2]];

        case 'get_host_resources':
            return ['success' => true, 'test_mode' => true, 'message' => 'Host resources retrieved successfully (simulated)', 'data' => [
                'cpu' => ['cores' => TEST_CPU_CORES, 'model' => 'Simulated CPU', 'usage' => 12.5, 'load_avg' => [0.5, 0.4, 0.3]],
                'memory' => ['total' => TEST_MEMORY_MB * 1048576, 'available' => (int)(TEST_MEMORY_MB * 0.75 * 1048576), 'used' => (int)(TEST_MEMORY_MB * 0.25 * 1048576), 'available_percent' => 75],
                'storage' => ['partitions' => [], 'total' => $totalBytes, 'used' => $usedBytes, 'free' => $freeBytes, 'total_human' => formatBytes($totalBytes), 'used_human' => formatBytes($usedBytes), 'free_human' => formatBytes($freeBytes)],
                'system' => ['hostname' => 'kvm-test-host', 'os' => PHP_OS_FAMILY . ' local simulation', 'uptime' => '1d 2h', 'kernel' => 'SIMULATED', 'architecture' => php_uname('m')],
            ]];

        case 'validate_vm_name':
            $errors = validateVmParams($params);
            if ($errors) return ['success' => false, 'test_mode' => true, 'message' => 'Validation failed', 'errors' => $errors];
            $exists = testVmExists((string)$params['vm_name'], $state);
            return ['success' => !$exists, 'test_mode' => true, 'message' => $exists ? "VM '{$params['vm_name']}' already exists" : "VM name '{$params['vm_name']}' is available", 'data' => ['exists' => $exists, 'available' => !$exists]];

        case 'check_cpu_availability':
            $required = (int)($params['cpu_cores'] ?? $params['vcpus'] ?? 0);
            return ['success' => $required > 0, 'test_mode' => true, 'message' => $required <= TEST_CPU_CORES ? 'Sufficient CPU resources available' : 'Insufficient CPU resources', 'data' => ['required_cores' => $required, 'total_cores' => TEST_CPU_CORES, 'usage_percent' => 12.5, 'available_cores' => TEST_CPU_CORES - 2, 'sufficient' => $required <= TEST_CPU_CORES]];

        case 'check_memory_availability':
            $requiredMb = (int)($params['memory_mb'] ?? $params['memory'] ?? 0);
            $availableMb = (int)(TEST_MEMORY_MB * 0.75);
            return ['success' => $requiredMb > 0, 'test_mode' => true, 'message' => $requiredMb <= $availableMb ? 'Sufficient memory available' : 'Insufficient memory available', 'data' => ['required_memory' => $requiredMb * 1048576, 'required_memory_human' => formatBytes($requiredMb * 1048576), 'available_memory' => $availableMb * 1048576, 'available_memory_human' => formatBytes($availableMb * 1048576), 'total_memory' => TEST_MEMORY_MB * 1048576, 'total_memory_human' => formatBytes(TEST_MEMORY_MB * 1048576), 'sufficient' => $requiredMb <= $availableMb]];

        case 'calculate_disk_space':
            $gb = (float)($params['disk_size'] ?? 0);
            $required = (int)round($gb * 1073741824);
            $withBuffer = (int)round($required * 1.10);
            return ['success' => $gb > 0, 'test_mode' => true, 'message' => $freeBytes >= $withBuffer ? 'Sufficient disk space available' : 'Insufficient disk space', 'data' => ['available_space' => $freeBytes, 'available_space_human' => formatBytes($freeBytes), 'required_space' => $required, 'required_space_human' => formatBytes($required), 'total_required' => $withBuffer, 'total_required_human' => formatBytes($withBuffer), 'sufficient' => $freeBytes >= $withBuffer]];

        case 'preview_vm_configuration':
        case 'generate_vm_command':
            $required = ['vm_name','vcpus','memory','disk_size','storage_pool'];
            foreach ($required as $f) if (empty($params[$f])) return ['success' => false, 'test_mode' => true, 'message' => "Required parameter missing: {$f}"];
            $command = generateVirtInstallCommandPortable($params);
            $data = ['vm_name' => $params['vm_name'], 'vcpus' => (int)$params['vcpus'], 'memory_mb' => (int)$params['memory'], 'memory_gb' => round(((int)$params['memory'])/1024, 2), 'disk_size_gb' => (int)$params['disk_size'], 'storage_pool' => $params['storage_pool'], 'os_variant' => $params['os_variant'] ?? 'generic', 'network' => $params['network'] ?? 'default', 'iso_path' => $params['iso_path'] ?? '', 'disk_path' => rtrim((string)$params['storage_pool'], '/\\') . '/' . $params['vm_name'] . '.qcow2', 'virt_install_command' => $command, 'command' => $command];
            return ['success' => true, 'test_mode' => true, 'message' => $action === 'generate_vm_command' ? 'VM command generated successfully (simulated)' : 'VM configuration preview generated successfully (simulated)', 'data' => $data];

        case 'create_disk':
            $name = (string)($params['vm_name'] ?? ''); $gb = (float)($params['disk_size'] ?? 0); $pool = (string)($params['storage_pool'] ?? DEFAULT_IMAGE_PATH); $format = (string)($params['format'] ?? 'qcow2');
            if ($name === '' || $gb <= 0) return ['success' => false, 'test_mode' => true, 'message' => 'vm_name and disk_size are required'];
            $path = rtrim($pool, '/\\') . '/' . $name . '.' . $format;
            foreach ($state['disks'] as $d) if (($d['disk_path'] ?? '') === $path) return ['success' => false, 'test_mode' => true, 'message' => 'Disk already exists at: ' . $path];
            $state['disks'][] = ['name' => $name, 'disk_path' => $path, 'storage_path' => $pool, 'format' => $format, 'allocated_gb' => $gb, 'used_gb' => 0, 'is_attached' => false, 'vm_name' => ''];
            writeTestState($state);
            return ['success' => true, 'test_mode' => true, 'message' => 'Disk created successfully (simulated)', 'data' => ['disk_path' => $path, 'disk_size' => $gb . 'G', 'format' => $format]];

        case 'create':
        case 'create_vm':
        case 'create_windows_vm':
            $name = (string)($params['vm_name'] ?? '');
            $vcpus = (int)($params['vcpus'] ?? $params['cpu_cores'] ?? 0);
            $memory = (int)($params['memory'] ?? $params['memory_mb'] ?? 0);
            $diskSize = (float)($params['disk_size'] ?? 0);
            $pool = (string)($params['storage_pool'] ?? $params['storage_path'] ?? DEFAULT_IMAGE_PATH);
            if ($name === '' || $vcpus < 1 || $memory < 512 || $diskSize <= 0) return ['success' => false, 'test_mode' => true, 'message' => 'vm_name, vcpus, memory, disk_size and storage_pool are required'];
            if (testVmExists($name, $state)) return ['success' => false, 'test_mode' => true, 'message' => "VM '{$name}' already exists"];
            if ($vcpus > TEST_CPU_CORES) return ['success' => false, 'test_mode' => true, 'message' => 'Insufficient CPU resources'];
            if ($memory > (int)(TEST_MEMORY_MB * 0.75)) return ['success' => false, 'test_mode' => true, 'message' => 'Insufficient memory available'];
            if (($diskSize * 1073741824 * 1.10) > $freeBytes) return ['success' => false, 'test_mode' => true, 'message' => 'Insufficient disk space'];
            $diskPath = rtrim($pool, '/\\') . '/' . $name . '.qcow2';
            $osVariant = (string)($params['os_variant'] ?? ($action === 'create_windows_vm' ? 'win10' : 'generic'));
            $autoStart = !empty($params['auto_start']);
            $vm = ['name' => $name, 'state' => $autoStart ? 'running' : 'stopped', 'vcpus' => $vcpus, 'cpu' => $vcpus, 'memory' => $memory, 'mem' => $memory, 'disk_size' => $diskSize, 'disk_path' => $diskPath, 'storage_pool' => $pool, 'os_variant' => $osVariant, 'os_type' => $osVariant, 'network' => $params['network'] ?? 'default', 'iso_path' => $params['iso_path'] ?? '', 'ip_address' => $autoStart ? testIpForName($name) : '', 'created' => date('c')];
            $state['vms'][] = $vm;
            $foundDisk = false;
            foreach ($state['disks'] as &$d) { if (($d['disk_path'] ?? '') === $diskPath) { $d['is_attached'] = true; $d['vm_name'] = $name; $foundDisk = true; } } unset($d);
            if (!$foundDisk) $state['disks'][] = ['name' => $name, 'disk_path' => $diskPath, 'storage_path' => $pool, 'format' => 'qcow2', 'allocated_gb' => $diskSize, 'used_gb' => 0, 'is_attached' => true, 'vm_name' => $name];
            writeTestState($state);
            return ['success' => true, 'test_mode' => true, 'message' => 'VM creation simulated successfully', 'data' => ['vm_name' => $name, 'disk_path' => $diskPath, 'status' => $vm['state'], 'cpu_cores' => $vcpus, 'memory_mb' => $memory, 'disk_size' => $diskSize, 'os_variant' => $osVariant]];

        case 'create_vm_without_disk':
            $name = (string)($params['vm_name'] ?? ''); $pool = (string)($params['storage_pool'] ?? DEFAULT_IMAGE_PATH); $path = rtrim($pool, '/\\') . '/' . $name . '.qcow2';
            $disk = null; foreach ($state['disks'] as $d) if (($d['disk_path'] ?? '') === $path) { $disk = $d; break; }
            if (!$disk) return ['success' => false, 'test_mode' => true, 'message' => 'Disk does not exist at: ' . $path . '. Please create disk first using create_disk action.'];
            $params['disk_size'] = $disk['allocated_gb'];
            return testDispatch('create_vm', $params);

        case 'attach_iso':
            $name = (string)($params['vm_name'] ?? ''); $iso = (string)($params['iso_path'] ?? '');
            if (!testVmExists($name, $state)) return ['success' => false, 'test_mode' => true, 'message' => "VM '{$name}' does not exist"];
            foreach ($state['vms'] as &$vm) if (($vm['name'] ?? '') === $name) $vm['iso_path'] = $iso; unset($vm); writeTestState($state);
            return ['success' => true, 'test_mode' => true, 'message' => 'ISO attached successfully (simulated)', 'data' => ['vm_name' => $name, 'iso_path' => $iso, 'status' => 'attached']];

        case 'get_creation_status':
            $name = (string)($params['vm_name'] ?? ''); foreach ($state['vms'] as $vm) if (($vm['name'] ?? '') === $name) return ['success' => true, 'test_mode' => true, 'message' => 'VM status retrieved successfully (simulated)', 'data' => ['vm_name' => $name, 'state' => $vm['state'], 'is_running' => $vm['state'] === 'running', 'cpu_usage' => 8.5, 'memory_usage_percent' => 24.0, 'uptime' => $vm['state'] === 'running' ? '12m' : 'N/A', 'autostart' => 'no', 'persistent' => 'yes']];
            return ['success' => false, 'test_mode' => true, 'message' => "VM '{$name}' does not exist"];

        case 'cancel_creation':
            $name = (string)($params['vm_name'] ?? ''); $state['vms'] = array_values(array_filter($state['vms'], static fn($vm) => ($vm['name'] ?? '') !== $name)); if (!empty($params['remove_disk'])) $state['disks'] = array_values(array_filter($state['disks'], static fn($d) => ($d['vm_name'] ?? '') !== $name)); writeTestState($state);
            return ['success' => true, 'test_mode' => true, 'message' => "VM '{$name}' cancelled successfully (simulated)", 'data' => ['vm_name' => $name, 'status' => 'cancelled']];

        case 'get_os_variants':
            $variants = [['id'=>'generic','name'=>'Generic'],['id'=>'almalinux9','name'=>'AlmaLinux 9'],['id'=>'ubuntu22.04','name'=>'Ubuntu 22.04 LTS'],['id'=>'debian11','name'=>'Debian 11'],['id'=>'win10','name'=>'Windows 10'],['id'=>'win11','name'=>'Windows 11']];
            return ['success' => true, 'test_mode' => true, 'message' => 'OS variants retrieved successfully (simulated)', 'data' => ['variants' => $variants, 'count' => count($variants)]];

        case 'list_bridges':
            return ['success' => true, 'test_mode' => true, 'message' => 'Bridges listed successfully (simulated)', 'data' => ['bridges' => [['name'=>'virbr0','status'=>'active'],['name'=>'default','status'=>'active']], 'count' => 2]];

        case 'get_storage_usage':
            return ['success' => true, 'test_mode' => true, 'message' => 'Storage usage retrieved successfully (simulated)', 'data' => ['storage' => [['device'=>'SIMULATED','mount'=>DEFAULT_IMAGE_PATH,'total'=>formatBytes($totalBytes),'used'=>formatBytes($usedBytes),'free'=>formatBytes($freeBytes),'usage_percent'=>$totalBytes > 0 ? (int)round($usedBytes/$totalBytes*100) : 0]], 'count'=>1]];

        case 'check_iso_exists':
            $iso = (string)($params['iso_path'] ?? ''); return ['success' => $iso !== '', 'test_mode' => true, 'message' => $iso !== '' ? 'ISO file exists (simulated)' : 'ISO path is required', 'data' => ['path'=>$iso,'exists'=>$iso !== '','readable'=>$iso !== '','size'=>4900000000,'size_human'=>'4.56 GB']];

        case 'get_os_templates':
            $templates = [['name'=>'ubuntu22-template','type'=>'linux','location'=>OS_TEMPLATE_PATH.'ubuntu22-template.qcow2','size'=>8589934592,'size_human'=>'8 GB'],['name'=>'almalinux9-template','type'=>'linux','location'=>OS_TEMPLATE_PATH.'almalinux9-template.qcow2','size'=>8589934592,'size_human'=>'8 GB']];
            return ['success'=>true,'test_mode'=>true,'message'=>'OS templates retrieved successfully (simulated)','data'=>['templates'=>$templates,'count'=>count($templates)]];

        case 'get_database_templates':
            $templates = [['name'=>'MariaDB','type'=>'database','subtype'=>'mysql','location'=>DB_PACKAGES_PATH.'mariadb','has_content'=>true,'size'=>1073741824,'size_human'=>'1 GB'],['name'=>'PostgreSQL','type'=>'database','subtype'=>'postgresql','location'=>DB_PACKAGES_PATH.'postgresql','has_content'=>true,'size'=>1073741824,'size_human'=>'1 GB']];
            return ['success'=>true,'test_mode'=>true,'message'=>'Database templates retrieved successfully (simulated)','data'=>['templates'=>$templates,'count'=>count($templates)]];

        case 'get_templates':
            $os = testDispatch('get_os_templates', $params); $db = testDispatch('get_database_templates', $params); return ['success'=>true,'test_mode'=>true,'message'=>'All templates retrieved successfully (simulated)','data'=>['os_templates'=>$os['data']['templates'],'database_templates'=>$db['data']['templates'],'os_count'=>$os['data']['count'],'database_count'=>$db['data']['count'],'total_count'=>$os['data']['count']+$db['data']['count']]];

        case 'system_overview':
        case 'list_vms':
            $vms = [];
            foreach ($state['vms'] as $vm) {
                $vms[] = ['name'=>$vm['name'],'state'=>$vm['state'],'cpu'=>$vm['vcpus'],'mem'=>$vm['memory'],'os_type'=>$vm['os_type'],'disk_path'=>$vm['disk_path'],'used_size_gb'=>0,'allocated_size_gb'=>$vm['disk_size'],'created'=>$vm['created'],'ip_address'=>$vm['ip_address'] ?? '','ip_addresses'=>!empty($vm['ip_address'])?[$vm['ip_address']]:[],'disks'=>[]];
            }
            if ($action === 'list_vms') return ['success'=>true,'test_mode'=>true,'vms'=>$vms,'count'=>count($vms)];
            $diskList = []; foreach ($state['disks'] as $d) $diskList[] = array_merge($d, ['has_vm'=>!empty($d['vm_name'])]);
            return ['success'=>true,'test_mode'=>true,'vms'=>$vms,'disks'=>$diskList,'host_storage'=>['total_gb'=>TEST_STORAGE_TOTAL_GB,'used_gb'=>round($usedBytes/1073741824,2),'free_gb'=>round($freeBytes/1073741824,2),'used_percent'=>$totalBytes>0?round($usedBytes/$totalBytes*100,1):0], 'summary'=>['vm_count'=>count($vms),'running_vms'=>count(array_filter($vms,static fn($v)=>$v['state']==='running')),'stopped_vms'=>count(array_filter($vms,static fn($v)=>$v['state']!=='running')),'disk_count'=>count($diskList)]];

        case 'vm_action':
            $name=(string)($params['vm_name']??''); $act=(string)($params['vm_action']??''); $found=false;
            foreach ($state['vms'] as &$vm) if (($vm['name']??'')===$name) { $found=true; if ($act==='start'||$act==='restart') { $vm['state']='running'; $vm['ip_address']=testIpForName($name); } elseif ($act==='stop'||$act==='power-off') { $vm['state']='stopped'; $vm['ip_address']=''; } } unset($vm);
            if (!$found) return ['success'=>false,'test_mode'=>true,'message'=>'VM not found'];
            if ($act==='delete') { $state['vms']=array_values(array_filter($state['vms'],static fn($v)=>($v['name']??'')!==$name)); foreach($state['disks'] as &$d) if(($d['vm_name']??'')===$name){$d['vm_name']='';$d['is_attached']=false;} unset($d); }
            writeTestState($state); return ['success'=>true,'test_mode'=>true,'message'=>"VM {$act} successful (simulated)"];

        case 'edit_vm':
            $name=(string)($params['vm_name']??''); foreach($state['vms'] as &$vm) if(($vm['name']??'')===$name){ if(isset($params['cpu_cores'])){$vm['vcpus']=(int)$params['cpu_cores'];$vm['cpu']=$vm['vcpus'];} if(isset($params['memory_mb'])){$vm['memory']=(int)$params['memory_mb'];$vm['mem']=$vm['memory'];} if(isset($params['disk_size']))$vm['disk_size']=(float)$params['disk_size']; writeTestState($state); return ['success'=>true,'test_mode'=>true,'message'=>'VM specifications updated (simulated)','data'=>$vm]; } unset($vm); return ['success'=>false,'test_mode'=>true,'message'=>'VM not found'];

        case 'edit_disk':
            $path=(string)($params['disk_path']??''); $size=(float)($params['disk_size']??0); foreach($state['disks'] as &$d) if(($d['disk_path']??'')===$path){ if($size < (float)$d['allocated_gb']) return ['success'=>false,'test_mode'=>true,'message'=>'Cannot shrink disk']; $d['allocated_gb']=$size; writeTestState($state); return ['success'=>true,'test_mode'=>true,'message'=>'Disk resized successfully (simulated)','data'=>$d]; } unset($d); return ['success'=>false,'test_mode'=>true,'message'=>'Disk not found'];

        case 'delete_disk':
            $path=(string)($params['disk_path']??''); foreach($state['disks'] as $d) if(($d['disk_path']??'')===$path && !empty($d['is_attached'])) return ['success'=>false,'test_mode'=>true,'message'=>'Disk is attached to VM: '.($d['vm_name']??'')]; $before=count($state['disks']); $state['disks']=array_values(array_filter($state['disks'],static fn($d)=>($d['disk_path']??'')!==$path)); writeTestState($state); return ['success'=>$before!==count($state['disks']),'test_mode'=>true,'message'=>$before!==count($state['disks'])?'Disk deleted successfully (simulated)':'Disk not found'];
    }

    return ['success' => false, 'test_mode' => true, 'message' => 'Action is not implemented in local test mode: ' . $action];
}

// Additional real-mode handlers used by the integrated UI.
function handleHealth(array $params): array
{
    if (KVM_TEST_MODE) return testDispatch('health', $params);
    $probe = executeCommand('virsh --version', false, 20);
    return ['success'=>$probe['success'],'test_mode'=>false,'message'=>$probe['success']?'KVM backend connection is working.':'KVM backend connection failed.','server_ip'=>KVM_HOST,'virsh_version'=>$probe['success']?trim($probe['output']):null,'error'=>$probe['success']?null:$probe['output']];
}

function handleSystemOverview(array $params): array
{
    // The full dashboard endpoint belongs to the integrated KvmService. For standalone real mode,
    // expose the available host resources plus VM list in a compatible shape.
    $list = handleListVmsPortable($params);
    $host = handleGetHostResources($params);
    if (!$list['success']) return $list;
    $storage = $host['data']['storage'] ?? ['total'=>0,'used'=>0,'free'=>0];
    return ['success'=>true,'test_mode'=>false,'vms'=>$list['vms'],'disks'=>[],'host_storage'=>['total_gb'=>round(($storage['total']??0)/1073741824,2),'used_gb'=>round(($storage['used']??0)/1073741824,2),'free_gb'=>round(($storage['free']??0)/1073741824,2)]];
}

function handleListVmsPortable(array $params): array
{
    $r = executeCommand("virsh list --all --name 2>/dev/null | grep -v '^$'", false);
    if (!$r['success']) return ['success'=>false,'message'=>'Failed to list VMs: '.$r['output']];
    $vms=[]; foreach(array_filter(preg_split('/\R/',trim($r['output']))) as $name){$name=trim($name);$s=executeCommand('virsh domstate '.escapeshellarg($name).' 2>/dev/null',false);$i=executeCommand('virsh dominfo '.escapeshellarg($name).' 2>/dev/null',false);$cpu=1;$mem=1024;if($i['success']){if(preg_match('/CPU\(s\):\s*(\d+)/i',$i['output'],$m))$cpu=(int)$m[1];if(preg_match('/Max memory:\s*(\d+)\s*KiB/i',$i['output'],$m))$mem=(int)round(((int)$m[1])/1024);} $vms[]=['name'=>$name,'state'=>strtolower(trim($s['output']))==='running'?'running':'stopped','cpu'=>$cpu,'mem'=>$mem,'os_type'=>'Unknown','disk_path'=>'','used_size_gb'=>0,'allocated_size_gb'=>0,'created'=>'','ip_address'=>'','ip_addresses'=>[]];}
    return ['success'=>true,'vms'=>$vms,'count'=>count($vms)];
}

function handleVmActionPortable(array $params): array
{
    $name=(string)($params['vm_name']??'');$action=(string)($params['vm_action']??'');if($name===''||$action==='')return['success'=>false,'message'=>'vm_name and vm_action are required'];
    $vm=escapeshellarg($name);$map=['start'=>"virsh start $vm",'stop'=>"virsh shutdown $vm",'power-off'=>"virsh destroy $vm",'restart'=>"virsh reboot $vm"];
    if($action==='delete'){$state=executeCommand("virsh domstate $vm 2>/dev/null",false);if(strtolower(trim($state['output']))==='running')executeCommand("virsh destroy $vm",false);$r=executeCommand("virsh undefine $vm --nvram 2>/dev/null || virsh undefine $vm",true);return['success'=>$r['success'],'message'=>$r['success']?'VM deleted successfully':'Failed to delete VM: '.$r['output']];}
    if(!isset($map[$action]))return['success'=>false,'message'=>'Unsupported VM action'];$r=executeCommand($map[$action],true);return['success'=>$r['success'],'message'=>$r['success']?"VM $action successful":$r['output']];
}

function handleEditVmPortable(array $params): array
{
    $name=(string)($params['vm_name']??'');if($name==='')return['success'=>false,'message'=>'vm_name is required'];$vm=escapeshellarg($name);$cpu=(int)($params['cpu_cores']??2);$mem=(int)($params['memory_mb']??2048);$r1=executeCommand("virsh setvcpus $vm $cpu --config --maximum 2>/dev/null || virsh setvcpus $vm $cpu --config",false);$r2=executeCommand("virsh setmaxmem $vm ".($mem*1024)." --config && virsh setmem $vm ".($mem*1024)." --config",false);return['success'=>$r1['success']&&$r2['success'],'message'=>($r1['success']&&$r2['success'])?'VM specifications updated':'Failed to update VM'];
}

function handleEditDiskPortable(array $params): array
{
    $path=(string)($params['disk_path']??'');$size=(int)($params['disk_size']??0);if($path===''||$size<1)return['success'=>false,'message'=>'disk_path and disk_size are required'];$r=executeCommand('qemu-img resize '.escapeshellarg($path).' '.$size.'G',true);return['success'=>$r['success'],'message'=>$r['success']?'Disk resized successfully':'Failed to resize disk: '.$r['output']];
}

function handleDeleteDiskPortable(array $params): array
{
    $path=(string)($params['disk_path']??'');if($path==='')return['success'=>false,'message'=>'disk_path is required'];$r=executeCommand('rm -f -- '.escapeshellarg($path),true);return['success'=>$r['success'],'message'=>$r['success']?'Disk deleted successfully':'Failed to delete disk: '.$r['output']];
}

// ============================================
// ACTION HANDLERS
// ============================================

/**
 * Action: validate_vm_name
 * Check if the VM name already exists
 */
function handleValidateVmName(array $params): array
{
    $errors = validateVmParams($params);
    if (!empty($errors)) {
        return [
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors,
        ];
    }

    $vmName = $params['vm_name'];

    // Check if VM exists on remote host
    $result = executeCommand("virsh list --all --name | grep -x " . escapeshellarg($vmName), false);
    if ($result['success'] && !empty(trim($result['output']))) {
        return [
            'success' => false,
            'message' => "VM '{$vmName}' already exists",
            'data' => ['exists' => true],
        ];
    }

    return [
        'success' => true,
        'message' => "VM name '{$vmName}' is available",
        'data' => ['exists' => false, 'available' => true],
    ];
}

/**
 * Action: get_iso_list
 * Return all available ISO images
 */
function handleGetIsoList(array $params): array
{
    $isoPaths = cfgCsv('KVM_ISO_PATHS', [DEFAULT_ISO_PATH]);

    $isoFiles = [];
    
    foreach ($isoPaths as $path) {
        // Check if directory exists on remote host
        $checkDir = executeCommand("test -d " . escapeshellarg($path) . " && echo 'exists'", false);
        if ($checkDir['success'] && trim($checkDir['output']) === 'exists') {
            // List files in directory
            $result = executeCommand("ls -la " . escapeshellarg($path) . " 2>/dev/null | grep -E '\\.(iso|img)$'", false);
            if ($result['success'] && !empty(trim($result['output']))) {
                $lines = explode("\n", trim($result['output']));
                foreach ($lines as $line) {
                    if (empty($line)) continue;
                    // Parse ls -la output
                    $parts = preg_split('/\s+/', $line);
                    if (count($parts) >= 9) {
                        $fileName = $parts[8];
                        $fileSize = (int) $parts[4];
                        $filePath = rtrim($path, '/') . '/' . $fileName;
                        $isoFiles[] = [
                            'name' => $fileName,
                            'path' => $filePath,
                            'size' => $fileSize,
                            'size_human' => formatBytes($fileSize),
                            'modified' => $parts[5] . ' ' . $parts[6] . ' ' . $parts[7],
                        ];
                    }
                }
            }
        }
    }

    usort($isoFiles, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });

    return [
        'success' => true,
        'message' => 'ISO list retrieved successfully',
        'data' => [
            'iso_files' => $isoFiles,
            'count' => count($isoFiles),
        ],
    ];
}

/**
 * Action: get_storage_pools
 * Return available storage pools
 */
function handleGetStoragePools(array $params): array
{
    $pools = [];
    
    $result = executeCommand("virsh pool-list --all --name", false);
    if ($result['success']) {
        $poolNames = array_filter(explode("\n", trim($result['output'])));
        
        foreach ($poolNames as $poolName) {
            $poolInfo = getPoolInfo($poolName);
            if ($poolInfo !== null) {
                $pools[] = $poolInfo;
            }
        }
    }

    // Check common storage paths on remote host
    $commonPaths = cfgCsv('KVM_STORAGE_PATHS', [DEFAULT_IMAGE_PATH]);

    foreach ($commonPaths as $path) {
        $checkDir = executeCommand("test -d " . escapeshellarg($path) . " && echo 'exists'", false);
        if ($checkDir['success'] && trim($checkDir['output']) === 'exists') {
            $poolExists = false;
            foreach ($pools as $pool) {
                if ($pool['path'] === $path) {
                    $poolExists = true;
                    break;
                }
            }
            if (!$poolExists) {
                // Get disk space info
                $dfResult = executeCommand("df -h " . escapeshellarg($path) . " 2>/dev/null | tail -1", false);
                if ($dfResult['success'] && !empty(trim($dfResult['output']))) {
                    $parts = preg_split('/\s+/', trim($dfResult['output']));
                    if (count($parts) >= 6) {
                        $total = parseSize($parts[1]);
                        $used = parseSize($parts[2]);
                        $free = parseSize($parts[3]);
                        $pools[] = [
                            'name' => basename($path),
                            'path' => $path,
                            'type' => 'dir',
                            'state' => 'active',
                            'available' => true,
                            'free_space' => $free,
                            'free_space_human' => $parts[3],
                            'total_space' => $total,
                            'total_space_human' => $parts[1],
                            'used_space' => $used,
                            'used_space_human' => $parts[2],
                        ];
                    }
                } else {
                    // Fallback: just add the path without space info
                    $pools[] = [
                        'name' => basename($path),
                        'path' => $path,
                        'type' => 'dir',
                        'state' => 'active',
                        'available' => true,
                        'free_space' => 0,
                        'free_space_human' => 'Unknown',
                        'total_space' => 0,
                        'total_space_human' => 'Unknown',
                    ];
                }
            }
        }
    }

    return [
        'success' => true,
        'message' => 'Storage pools retrieved successfully',
        'data' => [
            'pools' => $pools,
            'count' => count($pools),
        ],
    ];
}

function getPoolInfo(string $poolName): ?array
{
    $result = executeCommand("virsh pool-info " . escapeshellarg($poolName), false);
    if (!$result['success']) {
        return null;
    }

    $info = [
        'name' => $poolName,
        'state' => 'unknown',
        'path' => '',
        'type' => 'unknown',
        'available' => false,
        'free_space' => 0,
        'total_space' => 0,
    ];

    $lines = explode("\n", $result['output']);
    foreach ($lines as $line) {
        if (strpos($line, 'State:') !== false) {
            $parts = explode(':', $line, 2);
            $info['state'] = trim($parts[1] ?? 'unknown');
            $info['available'] = strtolower($info['state']) === 'active';
        } elseif (strpos($line, 'Path:') !== false) {
            $parts = explode(':', $line, 2);
            $info['path'] = trim($parts[1] ?? '');
        } elseif (strpos($line, 'Type:') !== false) {
            $parts = explode(':', $line, 2);
            $info['type'] = trim($parts[1] ?? 'unknown');
        } elseif (strpos($line, 'Free Space:') !== false) {
            $parts = explode(':', $line, 2);
            $freeSpace = trim($parts[1] ?? '0');
            $info['free_space'] = parseSize($freeSpace);
            $info['free_space_human'] = $freeSpace;
        } elseif (strpos($line, 'Total Space:') !== false) {
            $parts = explode(':', $line, 2);
            $totalSpace = trim($parts[1] ?? '0');
            $info['total_space'] = parseSize($totalSpace);
            $info['total_space_human'] = $totalSpace;
        }
    }

    return $info;
}

/**
 * Action: get_networks
 * Return available virtual networks/bridges
 */
function handleGetNetworks(array $params): array
{
    $networks = [];
    $networkNames = [];
    
    $result = executeCommand("virsh net-list --all --name", false);
    if ($result['success']) {
        $networkNamesList = array_filter(explode("\n", trim($result['output'])));
        
        foreach ($networkNamesList as $networkName) {
            $netInfo = getNetworkInfo($networkName);
            if ($netInfo !== null && !in_array($networkName, $networkNames)) {
                $networks[] = $netInfo;
                $networkNames[] = $networkName;
            }
        }
    }

    // Get bridges from remote host
    $result = executeCommand("brctl show 2>/dev/null", false);
    if ($result['success']) {
        $lines = explode("\n", $result['output']);
        foreach ($lines as $line) {
            if (preg_match('/^([^\s]+)\s+([^\s]+)\s+([^\s]+)/', $line, $matches)) {
                $bridge = $matches[1];
                if ($bridge !== 'bridge' && $bridge !== 'name' && !in_array($bridge, $networkNames)) {
                    $networks[] = [
                        'name' => $bridge,
                        'type' => 'bridge',
                        'state' => 'active',
                        'interfaces' => [],
                    ];
                    $networkNames[] = $bridge;
                }
            }
        }
    }

    $result = executeCommand("ip link show 2>/dev/null", false);
    if ($result['success']) {
        preg_match_all('/^\d+:\s+([^:]+):/', $result['output'], $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $iface) {
                $iface = trim($iface);
                if ($iface !== 'lo' && !str_starts_with($iface, 'vir') && !in_array($iface, $networkNames)) {
                    $networks[] = [
                        'name' => $iface,
                        'type' => 'interface',
                        'state' => 'active',
                        'interfaces' => [],
                    ];
                    $networkNames[] = $iface;
                }
            }
        }
    }

    return [
        'success' => true,
        'message' => 'Networks retrieved successfully',
        'data' => [
            'networks' => $networks,
            'count' => count($networks),
        ],
    ];
}

function getNetworkInfo(string $networkName): ?array
{
    $result = executeCommand("virsh net-info " . escapeshellarg($networkName), false);
    if (!$result['success']) {
        return null;
    }

    $info = [
        'name' => $networkName,
        'state' => 'unknown',
        'type' => 'virtual',
        'bridge' => '',
        'interfaces' => [],
    ];

    $lines = explode("\n", $result['output']);
    foreach ($lines as $line) {
        if (strpos($line, 'State:') !== false) {
            $parts = explode(':', $line, 2);
            $info['state'] = trim($parts[1] ?? 'unknown');
        } elseif (strpos($line, 'Bridge:') !== false) {
            $parts = explode(':', $line, 2);
            $info['bridge'] = trim($parts[1] ?? '');
        }
    }

    return $info;
}

/**
 * Action: get_host_resources
 * Return host CPU, RAM, storage information
 */
function handleGetHostResources(array $params): array
{
    $resources = [
        'cpu' => getCpuInfo(),
        'memory' => getMemoryInfo(),
        'storage' => getStorageInfo(),
        'system' => getSystemInfo(),
    ];

    return [
        'success' => true,
        'message' => 'Host resources retrieved successfully',
        'data' => $resources,
    ];
}

function getCpuInfo(): array
{
    $cpuInfo = [
        'cores' => 0,
        'model' => 'Unknown',
        'usage' => 0,
        'load_avg' => [0, 0, 0],
    ];

    // Get number of CPU cores
    $result = executeCommand("nproc 2>/dev/null", false);
    if ($result['success']) {
        $cpuInfo['cores'] = (int) trim($result['output']);
    }

    // Get CPU model
    $result = executeCommand("grep 'model name' /proc/cpuinfo | head -1 | cut -d':' -f2 2>/dev/null", false);
    if ($result['success']) {
        $cpuInfo['model'] = trim($result['output']);
    }

    // Get CPU usage from /proc/stat
    $result = executeCommand("cat /proc/stat | grep '^cpu ' | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}' 2>/dev/null", false);
    if ($result['success'] && !empty(trim($result['output']))) {
        $cpuInfo['usage'] = (float) trim($result['output']);
    }

    // Get load average
    $result = executeCommand("cat /proc/loadavg | awk '{print $1,$2,$3}' 2>/dev/null", false);
    if ($result['success'] && !empty(trim($result['output']))) {
        $loadParts = explode(' ', trim($result['output']));
        if (count($loadParts) >= 3) {
            $cpuInfo['load_avg'] = [(float) $loadParts[0], (float) $loadParts[1], (float) $loadParts[2]];
        }
    }

    return $cpuInfo;
}

function getMemoryInfo(): array
{
    $memInfo = [
        'total' => 0,
        'available' => 0,
        'used' => 0,
        'free' => 0,
        'cached' => 0,
        'buffers' => 0,
        'swap_total' => 0,
        'swap_used' => 0,
        'swap_free' => 0,
        'available_percent' => 0,
    ];

    // Get memory info from remote host
    $result = executeCommand("cat /proc/meminfo 2>/dev/null", false);
    if ($result['success']) {
        $lines = explode("\n", $result['output']);
        foreach ($lines as $line) {
            if (preg_match('/^MemTotal:\s+(\d+)/', $line, $matches)) {
                $memInfo['total'] = (int) $matches[1] * 1024;
            } elseif (preg_match('/^MemFree:\s+(\d+)/', $line, $matches)) {
                $memInfo['free'] = (int) $matches[1] * 1024;
            } elseif (preg_match('/^MemAvailable:\s+(\d+)/', $line, $matches)) {
                $memInfo['available'] = (int) $matches[1] * 1024;
            } elseif (preg_match('/^Cached:\s+(\d+)/', $line, $matches)) {
                $memInfo['cached'] = (int) $matches[1] * 1024;
            } elseif (preg_match('/^Buffers:\s+(\d+)/', $line, $matches)) {
                $memInfo['buffers'] = (int) $matches[1] * 1024;
            } elseif (preg_match('/^SwapTotal:\s+(\d+)/', $line, $matches)) {
                $memInfo['swap_total'] = (int) $matches[1] * 1024;
            } elseif (preg_match('/^SwapFree:\s+(\d+)/', $line, $matches)) {
                $memInfo['swap_free'] = (int) $matches[1] * 1024;
            }
        }
    }

    $memInfo['used'] = $memInfo['total'] - $memInfo['available'];
    $memInfo['swap_used'] = $memInfo['swap_total'] - $memInfo['swap_free'];
    $memInfo['available_percent'] = $memInfo['total'] > 0 ? round(($memInfo['available'] / $memInfo['total']) * 100, 2) : 0;

    foreach ($memInfo as $key => $value) {
        if (is_int($value) && $key !== 'available_percent') {
            $memInfo[$key . '_human'] = formatBytes($value);
        }
    }

    return $memInfo;
}

function getStorageInfo(): array
{
    $storageInfo = [
        'partitions' => [],
        'total' => 0,
        'used' => 0,
        'free' => 0,
    ];

    $result = executeCommand("df -h 2>/dev/null", false);

    if ($result['success']) {
        $lines = explode("\n", $result['output']);

        foreach ($lines as $line) {
            $parts = preg_split('/\s+/', trim($line));

            if (count($parts) >= 6 && str_starts_with($parts[0], '/dev/')) {
                $total = parseSize($parts[1]);
                $used = parseSize($parts[2]);
                $free = parseSize($parts[3]);

                $storageInfo['partitions'][] = [
                    'device' => $parts[0],
                    'mount_point' => $parts[5],
                    'total' => $total,
                    'total_human' => $parts[1],
                    'used' => $used,
                    'used_human' => $parts[2],
                    'free' => $free,
                    'free_human' => $parts[3],
                    'usage_percent' => (int) rtrim($parts[4], '%'),
                ];

                $storageInfo['total'] += $total;
                $storageInfo['used'] += $used;
                $storageInfo['free'] += $free;
            }
        }
    }

    $storageInfo['total_human'] = formatBytes($storageInfo['total']);
    $storageInfo['used_human'] = formatBytes($storageInfo['used']);
    $storageInfo['free_human'] = formatBytes($storageInfo['free']);

    return $storageInfo;
}

function getSystemInfo(): array
{
    $hostname = '';
    $os = '';
    $kernel = '';
    $arch = '';
    
    $result = executeCommand("hostname 2>/dev/null", false);
    if ($result['success']) {
        $hostname = trim($result['output']);
    }
    
    $result = executeCommand("uname -s -r 2>/dev/null", false);
    if ($result['success']) {
        $os = trim($result['output']);
    }
    
    $result = executeCommand("uname -r 2>/dev/null", false);
    if ($result['success']) {
        $kernel = trim($result['output']);
    }
    
    $result = executeCommand("uname -m 2>/dev/null", false);
    if ($result['success']) {
        $arch = trim($result['output']);
    }
    
    return [
        'hostname' => $hostname,
        'os' => $os,
        'uptime' => getUptime(),
        'kernel' => $kernel,
        'architecture' => $arch,
    ];
}

function getUptime(): string
{
    $result = executeCommand("cat /proc/uptime | cut -d' ' -f1 2>/dev/null", false);
    if (!$result['success']) {
        return 'Unknown';
    }
    $uptime = (int) trim($result['output']);
    return formatUptime($uptime);
}

/**
 * Action: calculate_disk_space
 * Verify enough storage exists
 */
function handleCalculateDiskSpace(array $params): array
{
    if (!isset($params['disk_size']) || empty($params['disk_size'])) {
        return [
            'success' => false,
            'message' => 'Disk size is required',
        ];
    }

    if (!isset($params['storage_pool']) || empty($params['storage_pool'])) {
        return [
            'success' => false,
            'message' => 'Storage pool is required',
        ];
    }

    $diskSize = (int) $params['disk_size'];
    $storagePool = $params['storage_pool'];

    if ($diskSize <= 0) {
        return [
            'success' => false,
            'message' => 'Disk size must be greater than 0',
        ];
    }

    $availableSpace = getAvailableSpace($storagePool);
    
    if ($availableSpace === false) {
        return [
            'success' => false,
            'message' => 'Unable to determine available space for storage pool: ' . $storagePool,
        ];
    }

    $requiredSpace = $diskSize * 1024 * 1024 * 1024;
    $bufferSpace = $requiredSpace * 0.10;
    $totalRequired = $requiredSpace + $bufferSpace;

    $hasEnoughSpace = $availableSpace >= $totalRequired;

    return [
        'success' => true,
        'message' => $hasEnoughSpace ? 'Sufficient disk space available' : 'Insufficient disk space',
        'data' => [
            'available_space' => $availableSpace,
            'available_space_human' => formatBytes($availableSpace),
            'required_space' => $requiredSpace,
            'required_space_human' => formatBytes($requiredSpace),
            'buffer_space' => $bufferSpace,
            'buffer_space_human' => formatBytes($bufferSpace),
            'total_required' => $totalRequired,
            'total_required_human' => formatBytes($totalRequired),
            'sufficient' => $hasEnoughSpace,
        ],
    ];
}

function getAvailableSpace(string $storagePool): int|false
{
    // Check if it's a directory on remote host
    $result = executeCommand("test -d " . escapeshellarg($storagePool) . " && echo 'exists'", false);
    if ($result['success'] && trim($result['output']) === 'exists') {
        $dfResult = executeCommand("df " . escapeshellarg($storagePool) . " 2>/dev/null | tail -1 | awk '{print $4}'", false);
        if ($dfResult['success'] && !empty(trim($dfResult['output']))) {
            return (int) trim($dfResult['output']) * 1024; // Convert to bytes
        }
    }

    // Check virsh pool
    $result = executeCommand("virsh pool-info " . escapeshellarg($storagePool) . " 2>/dev/null", false);
    if ($result['success']) {
        preg_match('/Free Space:\s+([^\n]+)/', $result['output'], $matches);
        if (isset($matches[1])) {
            return parseSize($matches[1]);
        }
    }

    return false;
}

/**
 * Action: preview_vm_configuration
 * Validate VM configuration before creation
 */
function handlePreviewVmConfiguration(array $params): array
{
    $errors = validateVmParams($params);
    if (!empty($errors)) {
        return [
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors,
        ];
    }

    $required = ['vm_name', 'vcpus', 'memory', 'disk_size', 'storage_pool'];
    foreach ($required as $field) {
        if (!isset($params[$field]) || empty($params[$field])) {
            return [
                'success' => false,
                'message' => "Required parameter missing: {$field}",
            ];
        }
    }

    $vmName = $params['vm_name'];
    $vcpus = (int) $params['vcpus'];
    $memory = (int) $params['memory'];
    $diskSize = (int) $params['disk_size'];
    $storagePool = $params['storage_pool'];
    $osVariant = $params['os_variant'] ?? 'generic';
    $network = $params['network'] ?? 'default';
    $isoPath = $params['iso_path'] ?? '';
    $cpuMode = $params['cpu_mode'] ?? 'host-passthrough';
    $bootType = $params['boot_type'] ?? 'uefi';
    $videoModel = $params['video_model'] ?? 'virtio';
    $diskBus = $params['disk_bus'] ?? 'virtio';
    $networkModel = $params['network_model'] ?? 'virtio';

    $config = [
        'vm_name' => $vmName,
        'vcpus' => $vcpus,
        'memory_mb' => $memory,
        'memory_gb' => round($memory / 1024, 2),
        'disk_size_gb' => $diskSize,
        'storage_pool' => $storagePool,
        'os_variant' => $osVariant,
        'network' => $network,
        'iso_path' => $isoPath,
        'cpu_mode' => $cpuMode,
        'boot_type' => $bootType,
        'video_model' => $videoModel,
        'disk_bus' => $diskBus,
        'network_model' => $networkModel,
        'disk_path' => rtrim($storagePool, '/') . '/' . $vmName . '.qcow2',
        'virt_install_command' => generateVirtInstallCommand($params),
    ];

    return [
        'success' => true,
        'message' => 'VM configuration preview generated successfully',
        'data' => $config,
    ];
}

/**
 * Generate the exact virt-install command based on parameters
 */
function generateVirtInstallCommand(array $params): string
{
    $vmName = escapeshellarg($params['vm_name']);
    $vcpus = escapeshellarg((string)$params['vcpus']);
    $memory = escapeshellarg((string)$params['memory']);
    $diskPath = escapeshellarg(rtrim($params['storage_pool'], '/') . '/' . $params['vm_name'] . '.qcow2');
    $osVariant = escapeshellarg($params['os_variant'] ?? 'generic');
    $network = escapeshellarg($params['network'] ?? 'default');
    $cpuMode = $params['cpu_mode'] ?? 'host-passthrough';
    $videoModel = $params['video_model'] ?? 'virtio';
    $diskBus = $params['disk_bus'] ?? 'virtio';
    $networkModel = $params['network_model'] ?? 'virtio';
    $bootType = $params['boot_type'] ?? 'uefi';

    $allowedCpuModes = ['host-passthrough', 'host-model', 'default'];
    $allowedVideoModels = ['virtio', 'qxl', 'vga', 'cirrus'];
    $allowedDiskBuses = ['virtio', 'sata', 'scsi', 'ide'];
    $allowedNetworkModels = ['virtio', 'e1000', 'rtl8139'];
    $allowedBootTypes = ['uefi', 'bios', 'hd'];

    if (!in_array($cpuMode, $allowedCpuModes, true)) $cpuMode = 'host-passthrough';
    if (!in_array($videoModel, $allowedVideoModels, true)) $videoModel = 'virtio';
    if (!in_array($diskBus, $allowedDiskBuses, true)) $diskBus = 'virtio';
    if (!in_array($networkModel, $allowedNetworkModels, true)) $networkModel = 'virtio';
    if (!in_array($bootType, $allowedBootTypes, true)) $bootType = 'uefi';

    $command = "virt-install \\\n";
    $command .= "  --name {$vmName} \\\n";
    $command .= "  --memory {$memory} \\\n";
    $command .= "  --vcpus {$vcpus} \\\n";
    $command .= "  --cpu {$cpuMode} \\\n";
    $command .= "  --disk path={$diskPath},format=qcow2,bus={$diskBus} \\\n";

    // Add ISO if provided
    if (!empty($params['iso_path'])) {
        $isoPath = escapeshellarg($params['iso_path']);
        $command .= "  --cdrom {$isoPath} \\\n";
    }

    // Add second CDROM for Windows virtio drivers
    if (!empty($params['virtio_iso_path'])) {
        $virtioPath = escapeshellarg($params['virtio_iso_path']);
        $command .= "  --disk path={$virtioPath},device=cdrom \\\n";
    }

    $command .= "  --os-variant {$osVariant} \\\n";
    $command .= "  --network network={$network},model={$networkModel} \\\n";
    $command .= "  --graphics vnc,listen=0.0.0.0 \\\n";
    $command .= "  --video {$videoModel} \\\n";

    // Boot type
    if ($bootType === 'uefi') {
        $command .= "  --boot uefi";
    } else {
        $command .= "  --boot hd";
    }

    return $command;
}

/**
 * Action: create_disk
 * Create a QCOW2 disk image on remote host (exact match to qemu-img command)
 */
function handleCreateDisk(array $params): array
{
    $errors = validateVmParams($params);
    if (!empty($errors)) {
        return [
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors,
        ];
    }

    if (!isset($params['vm_name']) || empty($params['vm_name'])) {
        return [
            'success' => false,
            'message' => 'VM name is required',
        ];
    }

    if (!isset($params['disk_size']) || empty($params['disk_size'])) {
        return [
            'success' => false,
            'message' => 'Disk size is required',
        ];
    }

    if (!isset($params['storage_pool']) || empty($params['storage_pool'])) {
        return [
            'success' => false,
            'message' => 'Storage pool is required',
        ];
    }

    $vmName = $params['vm_name'];
    $diskSize = (int) $params['disk_size'];
    $storagePool = $params['storage_pool'];
    $format = $params['format'] ?? 'qcow2';

    $diskPath = rtrim($storagePool, '/') . '/' . $vmName . '.' . $format;

    // Check if disk exists on remote host
    $checkResult = executeCommand("test -f " . escapeshellarg($diskPath) . " && echo 'exists'", false);
    if ($checkResult['success'] && trim($checkResult['output']) === 'exists') {
        return [
            'success' => false,
            'message' => "Disk already exists at: {$diskPath}",
        ];
    }

    // Create directory if it doesn't exist
    executeCommand("mkdir -p " . escapeshellarg($storagePool) . " 2>/dev/null", false);

    // EXACT command from your manual example: sudo qemu-img create -f qcow2 /vm_storage/images/vm-windows01.qcow2 100G
    $command = "qemu-img create -f {$format} " . escapeshellarg($diskPath) . " {$diskSize}G";
    $result = executeCommand($command);

    if (!$result['success']) {
        return [
            'success' => false,
            'message' => "Failed to create disk: " . $result['output'],
        ];
    }

    // Set permissions
    executeCommand("chmod 640 " . escapeshellarg($diskPath), false);

    $infoResult = executeCommand("qemu-img info " . escapeshellarg($diskPath), false);

    return [
        'success' => true,
        'message' => "Disk created successfully",
        'data' => [
            'disk_path' => $diskPath,
            'disk_size' => $diskSize . 'G',
            'format' => $format,
            'command' => $command,
            'info' => $infoResult['output'],
        ],
    ];
}

/**
 * Action: attach_iso
 * Attach the selected ISO to the VM
 */
function handleAttachIso(array $params): array
{
    if (!isset($params['vm_name']) || empty($params['vm_name'])) {
        return [
            'success' => false,
            'message' => 'VM name is required',
        ];
    }

    if (!isset($params['iso_path']) || empty($params['iso_path'])) {
        return [
            'success' => false,
            'message' => 'ISO path is required',
        ];
    }

    $vmName = $params['vm_name'];
    $isoPath = $params['iso_path'];

    if (!vmExists($vmName)) {
        return [
            'success' => false,
            'message' => "VM '{$vmName}' does not exist or is not accessible",
        ];
    }

    // Check if ISO exists on remote host
    $checkResult = executeCommand("test -f " . escapeshellarg($isoPath) . " && echo 'exists'", false);
    if (!($checkResult['success'] && trim($checkResult['output']) === 'exists')) {
        return [
            'success' => false,
            'message' => "ISO file not found: {$isoPath}",
        ];
    }

    $xml = "<disk type='file' device='cdrom'>\n";
    $xml .= "  <driver name='qemu' type='raw'/>\n";
    $xml .= "  <source file='" . xmlEscape($isoPath) . "'/>\n";
    $xml .= "  <target dev='hda' bus='ide'/>\n";
    $xml .= "  <readonly/>\n";
    $xml .= "</disk>";

    $uniqueId = uniqid();
    $tempFile = tempnam(sys_get_temp_dir(), 'iso_attach_');
    file_put_contents($tempFile, $xml);

    $remoteFile = REMOTE_TMP_PATH . "/iso_attach_{$uniqueId}.xml";


    // Write the XML through the already-configured command transport.
    // This avoids hardcoded scp paths/known_hosts locations on the PHP machine.
    $encodedXml = base64_encode($xml);
    $writeResult = executeCommand(
        "printf %s " . escapeshellarg($encodedXml) . " | base64 -d > " . escapeshellarg($remoteFile),
        false
    );
    if (!$writeResult['success']) {
        @unlink($tempFile);
        return [
            'success' => false,
            'message' => 'Failed to create temporary ISO XML on KVM host: ' . $writeResult['output'],
        ];
    }

    $command = "virsh attach-device " . escapeshellarg($vmName) . " --live --config " . escapeshellarg($remoteFile);
    $result = executeCommand($command);

    executeCommand("rm -f " . escapeshellarg($remoteFile), false);
    unlink($tempFile);

    if ($result['success']) {
        return [
            'success' => true,
            'message' => "ISO attached successfully",
            'data' => [
                'vm_name' => $vmName,
                'iso_path' => $isoPath,
                'status' => 'attached',
            ],
        ];
    }

    return [
        'success' => false,
        'message' => "Failed to attach ISO: " . $result['output'],
    ];
}

/**
 * Action: generate_vm_command
 * Generate the exact virt-install command
 */
function handleGenerateVmCommand(array $params): array
{
    $errors = validateVmParams($params);
    if (!empty($errors)) {
        return [
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors,
        ];
    }

    $required = ['vm_name', 'vcpus', 'memory', 'disk_size', 'storage_pool'];
    foreach ($required as $field) {
        if (!isset($params[$field]) || empty($params[$field])) {
            return [
                'success' => false,
                'message' => "Required parameter missing: {$field}",
            ];
        }
    }

    $command = generateVirtInstallCommand($params);

    return [
        'success' => true,
        'message' => 'VM command generated successfully',
        'data' => [
            'command' => $command,
            'disk_path' => rtrim($params['storage_pool'], '/') . '/' . $params['vm_name'] . '.qcow2',
        ],
    ];
}

/**
 * Action: create_vm
 * Create VM with disk creation first (exact match to your manual process)
 */
function handleCreateVm(array $params): array
{
    $errors = validateVmParams($params);
    if (!empty($errors)) {
        return [
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors,
        ];
    }

    $required = ['vm_name', 'vcpus', 'memory', 'disk_size', 'storage_pool'];
    foreach ($required as $field) {
        if (!isset($params[$field]) || empty($params[$field])) {
            return [
                'success' => false,
                'message' => "Required parameter missing: {$field}",
            ];
        }
    }

    $vmName = $params['vm_name'];
    $storagePool = $params['storage_pool'];
    $diskPath = rtrim($storagePool, '/') . '/' . $vmName . '.qcow2';

    // Step 1: Check if VM exists
    if (vmExists($vmName)) {
        return [
            'success' => false,
            'message' => "VM '{$vmName}' already exists",
        ];
    }

    // Step 2: Create disk (exact match to manual command)
    // sudo qemu-img create -f qcow2 /vm_storage/images/vm-windows01.qcow2 100G
    $diskSize = (int) $params['disk_size'];
    executeCommand("mkdir -p " . escapeshellarg($storagePool), false);
    
    $diskCommand = "qemu-img create -f qcow2 " . escapeshellarg($diskPath) . " {$diskSize}G";
    $diskResult = executeCommand($diskCommand);
    
    if (!$diskResult['success']) {
        return [
            'success' => false,
            'message' => "Failed to create disk: " . $diskResult['output'],
        ];
    }
    
    executeCommand("chmod 640 " . escapeshellarg($diskPath), false);

    // Step 3: Generate and execute virt-install command
    $command = generateVirtInstallCommand($params);
    
    // Remove backslashes and newlines for execution
    $execCommand = str_replace("\\\n", "", $command);
    $execCommand = str_replace("  ", " ", $execCommand);
    
    // Execute in background
    $logFile = REMOTE_TMP_PATH . "/vm_{$vmName}.log";
    $bgCommand = "nohup " . $execCommand . " >{$logFile} 2>&1 < /dev/null &";
    
    $result = executeCommand($bgCommand);
    
    if (!$result['success']) {
        // Rollback - delete the disk
        executeCommand("rm -f " . escapeshellarg($diskPath), false);
        return [
            'success' => false,
            'message' => 'Failed to start VM creation',
            'error' => $result['output'],
        ];
    }

    // Wait and check
    sleep(2);
    $vmCheck = vmExists($vmName);
    
    if (!$vmCheck) {
        executeCommand("rm -f " . escapeshellarg($diskPath), false);
        return [
            'success' => false,
            'message' => "VM creation failed to start. Check logs at {$logFile}",
        ];
    }

    return [
        'success' => true,
        'message' => "VM creation started in background",
        'data' => [
            'vm_name' => $vmName,
            'disk_path' => $diskPath,
            'status' => 'started',
            'command' => $execCommand,
            'log_file' => $logFile,
        ],
    ];
}

/**
 * Action: create_vm_without_disk
 * Create VM using existing disk (does NOT create disk first)
 * This matches: virt-install with --disk path=... without creating disk separately
 */
function handleCreateVmWithoutDisk(array $params): array
{
    $errors = validateVmParams($params);
    if (!empty($errors)) {
        return [
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors,
        ];
    }

    $required = ['vm_name', 'vcpus', 'memory', 'storage_pool'];
    foreach ($required as $field) {
        if (!isset($params[$field]) || empty($params[$field])) {
            return [
                'success' => false,
                'message' => "Required parameter missing: {$field}",
            ];
        }
    }

    $vmName = $params['vm_name'];
    $storagePool = $params['storage_pool'];
    $diskPath = rtrim($storagePool, '/') . '/' . $vmName . '.qcow2';

    // Check if disk exists
    $checkResult = executeCommand("test -f " . escapeshellarg($diskPath) . " && echo 'exists'", false);
    if (!($checkResult['success'] && trim($checkResult['output']) === 'exists')) {
        return [
            'success' => false,
            'message' => "Disk does not exist at: {$diskPath}. Please create disk first using create_disk action.",
        ];
    }

    if (vmExists($vmName)) {
        return [
            'success' => false,
            'message' => "VM '{$vmName}' already exists",
        ];
    }

    // Generate virt-install command
    $command = generateVirtInstallCommand($params);
    
    // Remove backslashes and newlines for execution
    $execCommand = str_replace("\\\n", "", $command);
    $execCommand = str_replace("  ", " ", $execCommand);
    
    // Execute in background
    $logFile = REMOTE_TMP_PATH . "/vm_{$vmName}.log";
    $bgCommand = "nohup " . $execCommand . " >{$logFile} 2>&1 < /dev/null &";
    
    $result = executeCommand($bgCommand);
    
    if (!$result['success']) {
        return [
            'success' => false,
            'message' => 'Failed to start VM creation',
            'error' => $result['output'],
        ];
    }

    sleep(2);
    $vmCheck = vmExists($vmName);
    
    if (!$vmCheck) {
        return [
            'success' => false,
            'message' => "VM creation failed to start. Check logs at {$logFile}",
        ];
    }

    return [
        'success' => true,
        'message' => "VM creation started in background",
        'data' => [
            'vm_name' => $vmName,
            'disk_path' => $diskPath,
            'status' => 'started',
            'command' => $execCommand,
            'log_file' => $logFile,
        ],
    ];
}

/**
 * Action: create_windows_vm
 * Create Windows VM with virtio drivers (exact match to your Windows command)
 */
function handleCreateWindowsVm(array $params): array
{
    $errors = validateVmParams($params);
    if (!empty($errors)) {
        return [
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors,
        ];
    }

    $required = ['vm_name', 'vcpus', 'memory', 'disk_size', 'storage_pool'];
    foreach ($required as $field) {
        if (!isset($params[$field]) || empty($params[$field])) {
            return [
                'success' => false,
                'message' => "Required parameter missing: {$field}",
            ];
        }
    }

    $vmName = $params['vm_name'];
    $vcpus = (int) $params['vcpus'];
    $memory = (int) $params['memory'];
    $diskSize = (int) $params['disk_size'];
    $storagePool = $params['storage_pool'];
    $isoPath = $params['iso_path'] ?? (DEFAULT_ISO_PATH . 'Windows.iso');
    $virtioIsoPath = $params['virtio_iso_path'] ?? (DEFAULT_ISO_PATH . 'virtio-win.iso');
    $osVariant = $params['os_variant'] ?? 'win10';
    $network = $params['network'] ?? 'default';
    $diskPath = rtrim($storagePool, '/') . '/' . $vmName . '.qcow2';

    if (vmExists($vmName)) {
        return [
            'success' => false,
            'message' => "VM '{$vmName}' already exists",
        ];
    }

    // Step 1: Create disk
    executeCommand("mkdir -p " . escapeshellarg($storagePool), false);
    $diskCommand = "qemu-img create -f qcow2 " . escapeshellarg($diskPath) . " {$diskSize}G";
    $diskResult = executeCommand($diskCommand);
    
    if (!$diskResult['success']) {
        return [
            'success' => false,
            'message' => "Failed to create disk: " . $diskResult['output'],
        ];
    }
    executeCommand("chmod 640 " . escapeshellarg($diskPath), false);

    // Step 2: Build Windows virt-install command (exact match to your example)
    $command = "virt-install \\\n";
    $command .= "  --name " . escapeshellarg($vmName) . " \\\n";
    $command .= "  --memory " . escapeshellarg((string)$memory) . " \\\n";
    $command .= "  --vcpus " . escapeshellarg((string)$vcpus) . " \\\n";
    $command .= "  --cpu host-passthrough \\\n";
    $command .= "  --disk path=" . escapeshellarg($diskPath) . ",size={$diskSize},format=qcow2,bus=virtio \\\n";
    $command .= "  --cdrom " . escapeshellarg($isoPath) . " \\\n";
    $command .= "  --disk path=" . escapeshellarg($virtioIsoPath) . ",device=cdrom \\\n";
    $command .= "  --os-variant " . escapeshellarg($osVariant) . " \\\n";
    $command .= "  --network network={$network},model=virtio \\\n";
    $command .= "  --graphics vnc,listen=0.0.0.0 \\\n";
    $command .= "  --video virtio \\\n";
    $command .= "  --boot uefi";

    // Execute
    $execCommand = str_replace("\\\n", "", $command);
    $execCommand = str_replace("  ", " ", $execCommand);
    
    $logFile = REMOTE_TMP_PATH . "/vm_{$vmName}.log";
    $bgCommand = "nohup " . $execCommand . " >{$logFile} 2>&1 < /dev/null &";
    
    $result = executeCommand($bgCommand);
    
    if (!$result['success']) {
        executeCommand("rm -f " . escapeshellarg($diskPath), false);
        return [
            'success' => false,
            'message' => 'Failed to start VM creation',
            'error' => $result['output'],
        ];
    }

    sleep(2);
    $vmCheck = vmExists($vmName);
    
    if (!$vmCheck) {
        executeCommand("rm -f " . escapeshellarg($diskPath), false);
        return [
            'success' => false,
            'message' => "VM creation failed to start. Check logs at {$logFile}",
        ];
    }

    return [
        'success' => true,
        'message' => "Windows VM creation started in background",
        'data' => [
            'vm_name' => $vmName,
            'disk_path' => $diskPath,
            'status' => 'started',
            'command' => $execCommand,
            'log_file' => $logFile,
        ],
    ];
}

/**
 * Action: get_creation_status
 * Return VM creation progress/status
 */
function handleGetCreationStatus(array $params): array
{
    if (!isset($params['vm_name']) || empty($params['vm_name'])) {
        return [
            'success' => false,
            'message' => 'VM name is required',
        ];
    }

    $vmName = $params['vm_name'];

    if (!vmExists($vmName)) {
        return [
            'success' => false,
            'message' => "VM '{$vmName}' does not exist",
        ];
    }

    $result = executeCommand("virsh dominfo " . escapeshellarg($vmName), false);
    if (!$result['success']) {
        return [
            'success' => false,
            'message' => "Failed to get VM status: " . $result['output'],
        ];
    }

    $statusData = parseVmStatus($result['output']);
    $statusData['vm_name'] = $vmName;

    return [
        'success' => true,
        'message' => "VM status retrieved successfully",
        'data' => $statusData,
    ];
}

function parseVmStatus(string $status): array
{
    $data = [
        'state' => 'unknown',
        'cpu_usage' => 0,
        'memory_usage' => 0,
        'memory_total' => 0,
        'memory_usage_percent' => 0,
        'uptime' => 'N/A',
        'autostart' => 'no',
        'persistent' => 'no',
        'is_running' => false,
    ];

    $lines = explode("\n", $status);
    foreach ($lines as $line) {
        if (strpos($line, 'State:') !== false) {
            $parts = explode(':', $line, 2);
            $data['state'] = trim($parts[1] ?? 'unknown');
        } elseif (strpos($line, 'CPU:') !== false) {
            $parts = explode(':', $line, 2);
            $data['cpu_usage'] = (float) trim($parts[1] ?? '0');
        } elseif (strpos($line, 'Max memory:') !== false) {
            $parts = explode(':', $line, 2);
            $data['memory_total'] = (int) trim($parts[1] ?? '0');
        } elseif (strpos($line, 'Used memory:') !== false) {
            $parts = explode(':', $line, 2);
            $data['memory_usage'] = (int) trim($parts[1] ?? '0');
        } elseif (strpos($line, 'Autostart:') !== false) {
            $parts = explode(':', $line, 2);
            $data['autostart'] = trim($parts[1] ?? 'no');
        } elseif (strpos($line, 'Persistent:') !== false) {
            $parts = explode(':', $line, 2);
            $data['persistent'] = trim($parts[1] ?? 'no');
        }
    }

    if ($data['memory_total'] > 0) {
        $data['memory_usage_percent'] = round(($data['memory_usage'] / $data['memory_total']) * 100, 2);
    }

    $data['is_running'] = strtolower($data['state']) === 'running';

    return $data;
}

/**
 * Action: cancel_creation
 * Cancel VM creation
 */
function handleCancelCreation(array $params): array
{
    if (!isset($params['vm_name']) || empty($params['vm_name'])) {
        return [
            'success' => false,
            'message' => 'VM name is required',
        ];
    }

    $vmName = $params['vm_name'];

    if (!vmExists($vmName)) {
        return [
            'success' => false,
            'message' => "VM '{$vmName}' does not exist",
        ];
    }

    $cancelMode = $params['cancel_mode'] ?? 'destroy';

    if ($cancelMode === 'destroy') {
        $destroy = executeCommand("virsh destroy " . escapeshellarg($vmName));
        if (!$destroy['success']) {
            return [
                'success' => false,
                'message' => "Failed to stop VM: " . $destroy['output'],
            ];
        }
    }

    $undefine = executeCommand("virsh undefine " . escapeshellarg($vmName) . " --remove-all-storage");
    if (!$undefine['success']) {
        return [
            'success' => false,
            'message' => "Failed to undefine VM: " . $undefine['output'],
        ];
    }

    if (isset($params['remove_disk']) && $params['remove_disk']) {
        $diskPath = $params['disk_path'] ?? (DEFAULT_IMAGE_PATH . $vmName . '.qcow2');
        executeCommand("rm -f " . escapeshellarg($diskPath), false);
    }

    return [
        'success' => true,
        'message' => "VM '{$vmName}' cancelled successfully",
        'data' => [
            'vm_name' => $vmName,
            'status' => 'cancelled',
        ],
    ];
}

/**
 * Action: get_os_variants
 * Return available OS variants
 */
function handleGetOsVariants(array $params): array
{
    $result = executeCommand("osinfo-query os 2>&1", false);
    
    $variants = [];
    if ($result['success']) {
        $lines = explode("\n", $result['output']);
        foreach ($lines as $line) {
            if (preg_match('/^([^\s]+)\s+([^\s]+)/', $line, $matches)) {
                $variants[] = [
                    'id' => $matches[1],
                    'name' => $matches[2],
                ];
            }
        }
    }

    if (empty($variants)) {
        $commonVariants = [
            ['id' => 'generic', 'name' => 'Generic'],
            ['id' => 'linux', 'name' => 'Linux'],
            ['id' => 'windows', 'name' => 'Windows'],
            ['id' => 'almalinux9', 'name' => 'AlmaLinux 9'],
            ['id' => 'almalinux8', 'name' => 'AlmaLinux 8'],
            ['id' => 'ubuntu22.04', 'name' => 'Ubuntu 22.04 LTS'],
            ['id' => 'ubuntu20.04', 'name' => 'Ubuntu 20.04 LTS'],
            ['id' => 'debian11', 'name' => 'Debian 11'],
            ['id' => 'centos8', 'name' => 'CentOS 8'],
            ['id' => 'centos7', 'name' => 'CentOS 7'],
            ['id' => 'rhel8', 'name' => 'RHEL 8'],
            ['id' => 'rhel7', 'name' => 'RHEL 7'],
            ['id' => 'fedora37', 'name' => 'Fedora 37'],
            ['id' => 'fedora36', 'name' => 'Fedora 36'],
            ['id' => 'win10', 'name' => 'Windows 10'],
            ['id' => 'win11', 'name' => 'Windows 11'],
            ['id' => 'win2019', 'name' => 'Windows Server 2019'],
            ['id' => 'win2022', 'name' => 'Windows Server 2022'],
        ];
        $variants = $commonVariants;
    }

    return [
        'success' => true,
        'message' => 'OS variants retrieved successfully',
        'data' => [
            'variants' => $variants,
            'count' => count($variants),
        ],
    ];
}

/**
 * Action: list_bridges
 * List available network bridges
 */
function handleListBridges(array $params): array
{
    $bridges = [];
    
    $result = executeCommand("brctl show 2>/dev/null", false);
    if ($result['success']) {
        $lines = explode("\n", $result['output']);
        foreach ($lines as $line) {
            if (preg_match('/^([^\s]+)\s+([^\s]+)\s+([^\s]+)/', $line, $matches)) {
                $bridge = $matches[1];
                if ($bridge !== 'bridge' && $bridge !== 'name') {
                    $bridges[] = [
                        'name' => $bridge,
                        'status' => 'active',
                    ];
                }
            }
        }
    }

    $netResult = executeCommand("virsh net-list --all 2>/dev/null", false);
    if ($netResult['success']) {
        $lines = explode("\n", $netResult['output']);
        foreach ($lines as $line) {
            if (preg_match('/^([^\s]+)\s+([^\s]+)/', $line, $matches)) {
                if ($matches[1] !== 'Name' && $matches[1] !== '---') {
                    $bridges[] = [
                        'name' => $matches[1],
                        'status' => strtolower($matches[2] ?? 'unknown'),
                    ];
                }
            }
        }
    }

    return [
        'success' => true,
        'message' => 'Bridges listed successfully',
        'data' => [
            'bridges' => $bridges,
            'count' => count($bridges),
        ],
    ];
}

/**
 * Action: get_storage_usage
 * Get detailed storage usage
 */
function handleGetStorageUsage(array $params): array
{
    $storage = [];

    $result = executeCommand("df -h 2>/dev/null", false);

    if ($result['success']) {
        $lines = explode("\n", $result['output']);

        foreach ($lines as $line) {
            $parts = preg_split('/\s+/', trim($line));

            if (count($parts) >= 6 && str_starts_with($parts[0], '/dev/')) {
                $storage[] = [
                    'device' => $parts[0],
                    'mount' => $parts[5],
                    'total' => $parts[1],
                    'used' => $parts[2],
                    'free' => $parts[3],
                    'usage_percent' => (int) rtrim($parts[4], '%'),
                ];
            }
        }
    }

    return [
        'success' => true,
        'message' => 'Storage usage retrieved successfully',
        'data' => [
            'storage' => $storage,
            'count' => count($storage),
        ],
    ];
}

/**
 * Action: check_iso_exists
 * Check if an ISO file exists
 */
function handleCheckIsoExists(array $params): array
{
    if (!isset($params['iso_path']) || empty($params['iso_path'])) {
        return [
            'success' => false,
            'message' => 'ISO path is required',
        ];
    }

    $isoPath = $params['iso_path'];
    
    $result = executeCommand("test -f " . escapeshellarg($isoPath) . " && stat -c %s " . escapeshellarg($isoPath) . " 2>/dev/null || echo 'notfound'", false);
    
    if ($result['success']) {
        $output = trim($result['output']);
        if ($output !== 'notfound' && is_numeric($output)) {
            $size = (int) $output;
            return [
                'success' => true,
                'message' => 'ISO file exists',
                'data' => [
                    'path' => $isoPath,
                    'exists' => true,
                    'readable' => true,
                    'size' => $size,
                    'size_human' => formatBytes($size),
                ],
            ];
        }
    }

    return [
        'success' => true,
        'message' => 'ISO file not found',
        'data' => [
            'path' => $isoPath,
            'exists' => false,
            'readable' => false,
            'size' => 0,
            'size_human' => '0 B',
        ],
    ];
}

/**
 * Action: check_cpu_availability
 * Check CPU availability
 */
function handleCheckCpuAvailability(array $params): array
{
    if (!isset($params['cpu_cores']) || empty($params['cpu_cores'])) {
        return [
            'success' => false,
            'message' => 'CPU cores is required',
        ];
    }

    $requiredCores = (int) $params['cpu_cores'];
    
    $result = executeCommand("nproc 2>/dev/null", false);
    $totalCores = $result['success'] ? (int) trim($result['output']) : 0;
    
    $cpuInfo = getCpuInfo();
    $availableCores = $totalCores - ($totalCores * ($cpuInfo['usage'] / 100));
    $sufficient = $availableCores >= $requiredCores;

    return [
        'success' => true,
        'message' => $sufficient ? 'Sufficient CPU resources available' : 'Insufficient CPU resources',
        'data' => [
            'required_cores' => $requiredCores,
            'total_cores' => $totalCores,
            'usage_percent' => $cpuInfo['usage'],
            'available_cores' => round($availableCores, 2),
            'sufficient' => $sufficient,
        ],
    ];
}

/**
 * Action: check_memory_availability
 * Check memory availability
 */
function handleCheckMemoryAvailability(array $params): array
{
    if (!isset($params['memory_mb']) || empty($params['memory_mb'])) {
        return [
            'success' => false,
            'message' => 'Memory (MB) is required',
        ];
    }

    $requiredMemory = (int) $params['memory_mb'] * 1024 * 1024;
    
    $memInfo = getMemoryInfo();
    $availableMemory = $memInfo['available'];
    $sufficient = $availableMemory >= $requiredMemory;

    return [
        'success' => true,
        'message' => $sufficient ? 'Sufficient memory available' : 'Insufficient memory available',
        'data' => [
            'required_memory' => $requiredMemory,
            'required_memory_human' => formatBytes($requiredMemory),
            'available_memory' => $availableMemory,
            'available_memory_human' => formatBytes($availableMemory),
            'total_memory' => $memInfo['total'],
            'total_memory_human' => formatBytes($memInfo['total']),
            'usage_percent' => $memInfo['available_percent'],
            'sufficient' => $sufficient,
        ],
    ];
}

// ============================================
// TEMPLATE HANDLERS
// ============================================

/**
 * Action: get_os_templates
 * Get OS name, type, and location from template storage
 * Looks for OS disk images in the OS template directory
 */
function handleGetOsTemplates(array $params): array
{
    $osTemplates = [];
    
    // Define OS template search paths (prioritize /vm_storage/templates/os/)
    $searchPaths = cfgCsv('KVM_OS_TEMPLATE_PATHS', [OS_TEMPLATE_PATH]);
    
    foreach ($searchPaths as $path) {
        // Check if directory exists on remote host
        $checkDir = executeCommand("test -d " . escapeshellarg($path) . " && echo 'exists'", false);
        if ($checkDir['success'] && trim($checkDir['output']) === 'exists') {
            // Look for qcow2 and raw disk images
            $result = executeCommand("find " . escapeshellarg($path) . " -maxdepth 1 -type f \\( -name '*.qcow2' -o -name '*.raw' -o -name '*.img' \\) 2>/dev/null", false);
            
            if ($result['success'] && !empty(trim($result['output']))) {
                $files = array_filter(explode("\n", trim($result['output'])));
                
                foreach ($files as $filePath) {
                    if (empty($filePath)) continue;
                    
                    // Extract OS name from filename
                    $name = pathinfo($filePath, PATHINFO_FILENAME);
                    
                    // Determine OS type based on common patterns
                    $type = 'linux'; // default
                    $osName = strtolower($name);
                    
                    if (preg_match('/win|windows/i', $osName)) {
                        $type = 'windows';
                    } elseif (preg_match('/ubuntu|debian|linux/i', $osName)) {
                        $type = 'linux';
                    } elseif (preg_match('/centos|rhel|fedora|almalinux|rocky/i', $osName)) {
                        $type = 'linux';
                    } elseif (preg_match('/freebsd|openbsd/i', $osName)) {
                        $type = 'bsd';
                    } elseif (preg_match('/macos|darwin/i', $osName)) {
                        $type = 'macos';
                    }
                    
                    // Get file size
                    $sizeResult = executeCommand("stat -c %s " . escapeshellarg($filePath) . " 2>/dev/null", false);
                    $size = $sizeResult['success'] ? (int) trim($sizeResult['output']) : 0;
                    
                    $osTemplates[] = [
                        'name' => $name,
                        'type' => $type,
                        'location' => $filePath,
                        'size' => $size,
                        'size_human' => $size > 0 ? formatBytes($size) : 'Unknown',
                    ];
                }
                break; // Found templates in this path, stop searching
            }
        }
    }
    
    // Sort templates by name
    usort($osTemplates, function($a, $b) {
        return strcasecmp($a['name'], $b['name']);
    });
    
    return [
        'success' => true,
        'message' => 'OS templates retrieved successfully',
        'data' => [
            'templates' => $osTemplates,
            'count' => count($osTemplates),
        ],
    ];
}

/**
 * Action: get_database_templates
 * Get database name, type, and location from database packages directory
 * Looks specifically in /vm_storage/database-packages/
 */
function handleGetDatabaseTemplates(array $params): array
{
    $dbTemplates = [];
    
    // Database packages directory
    $dbPackagesPath = DB_PACKAGES_PATH;
    
    // Check if directory exists on remote host
    $checkDir = executeCommand("test -d " . escapeshellarg($dbPackagesPath) . " && echo 'exists'", false);
    if (!($checkDir['success'] && trim($checkDir['output']) === 'exists')) {
        return [
            'success' => true,
            'message' => 'Database packages directory not found',
            'data' => [
                'templates' => [],
                'count' => 0,
            ],
        ];
    }
    
    // List all subdirectories in database-packages
    $result = executeCommand("find " . escapeshellarg($dbPackagesPath) . " -maxdepth 1 -type d 2>/dev/null | tail -n +2", false);
    
    if ($result['success'] && !empty(trim($result['output']))) {
        $directories = array_filter(explode("\n", trim($result['output'])));
        
        foreach ($directories as $dirPath) {
            if (empty($dirPath)) continue;
            
            $dbName = basename($dirPath);
            
            // Determine database type based on directory name
            $type = 'other';
            $dbLower = strtolower($dbName);
            
            if (preg_match('/mariadb|mysql/i', $dbLower)) {
                $type = 'mysql';
            } elseif (preg_match('/postgres|pgsql|postgresql/i', $dbLower)) {
                $type = 'postgresql';
            } elseif (preg_match('/oracle/i', $dbLower)) {
                $type = 'oracle';
            } elseif (preg_match('/mongo|mongodb/i', $dbLower)) {
                $type = 'mongodb';
            } elseif (preg_match('/redis/i', $dbLower)) {
                $type = 'redis';
            } elseif (preg_match('/elastic|elasticsearch/i', $dbLower)) {
                $type = 'elasticsearch';
            } elseif (preg_match('/cassandra/i', $dbLower)) {
                $type = 'cassandra';
            } elseif (preg_match('/sqlite/i', $dbLower)) {
                $type = 'sqlite';
            } elseif (preg_match('/sqlserver|mssql/i', $dbLower)) {
                $type = 'sqlserver';
            }
            
            // Check if directory has content (to determine if it's a valid package)
            $countResult = executeCommand("ls -1 " . escapeshellarg($dirPath) . " 2>/dev/null | wc -l", false);
            $hasContent = $countResult['success'] && (int) trim($countResult['output']) > 0;
            
            // Get directory size
            $sizeResult = executeCommand("du -sb " . escapeshellarg($dirPath) . " 2>/dev/null | cut -f1", false);
            $size = $sizeResult['success'] ? (int) trim($sizeResult['output']) : 0;
            
            // Format the name properly
            $displayName = $dbName;
            if ($type !== 'other') {
                $displayName = ucfirst($type);
                if ($dbName !== $type && !str_contains($dbName, $type)) {
                    $displayName = ucfirst($dbName);
                }
            }
            
            $dbTemplates[] = [
                'name' => $displayName,
                'type' => 'database',
                'subtype' => $type,
                'location' => $dirPath,
                'has_content' => $hasContent,
                'size' => $size,
                'size_human' => $size > 0 ? formatBytes($size) : 'Unknown',
            ];
        }
    }
    
    // Sort templates by name
    usort($dbTemplates, function($a, $b) {
        return strcasecmp($a['name'], $b['name']);
    });
    
    return [
        'success' => true,
        'message' => 'Database templates retrieved successfully',
        'data' => [
            'templates' => $dbTemplates,
            'count' => count($dbTemplates),
        ],
    ];
}

/**
 * Action: get_templates
 * Get both OS and database templates in one response
 */
function handleGetTemplates(array $params): array
{
    // Get OS templates
    $osResult = handleGetOsTemplates($params);
    $osTemplates = $osResult['success'] ? $osResult['data']['templates'] : [];
    
    // Get Database templates
    $dbResult = handleGetDatabaseTemplates($params);
    $dbTemplates = $dbResult['success'] ? $dbResult['data']['templates'] : [];
    
    return [
        'success' => true,
        'message' => 'All templates retrieved successfully',
        'data' => [
            'os_templates' => $osTemplates,
            'database_templates' => $dbTemplates,
            'os_count' => count($osTemplates),
            'database_count' => count($dbTemplates),
            'total_count' => count($osTemplates) + count($dbTemplates),
        ],
    ];
}

/**
 * XML escape helper
 */
function xmlEscape(string $value): string
{
    return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

// ============================================
// MAIN ROUTER
// ============================================

/**
 * Optional bearer/X-API-Key protection. Leave KVM_API_TOKEN empty for local development.
 */
function enforceApiTokenIfConfigured(): void
{
    if (KVM_API_TOKEN === '') return;

    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $auth = $headers['Authorization'] ?? $headers['authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
    $apiKey = $headers['X-API-Key'] ?? $headers['x-api-key'] ?? ($_SERVER['HTTP_X_API_KEY'] ?? '');
    $provided = $apiKey;
    if ($provided === '' && preg_match('/^Bearer\s+(.+)$/i', (string)$auth, $m)) {
        $provided = trim($m[1]);
    }

    if (!hash_equals(KVM_API_TOKEN, (string)$provided)) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}

/**
 * Main API router with explicit switch-case routing
 */
function routeRequest(): void
{
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    if (empty($action)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Missing action parameter',
        ]);
        return;
    }

    $action = preg_replace('/[^a-zA-Z0-9_]/', '', $action);

    if (empty($action)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action parameter',
        ]);
        return;
    }

    enforceApiTokenIfConfigured();

    $params = getRequestParams();
    logRequest($action, $params);

    if (isset(ACTION_ROUTES[$action])) {
        validateRequestMethod($action, ACTION_ROUTES[$action]['methods']);
    }

    if (KVM_TEST_MODE) {
        // In test mode, never reach SSH, virsh, qemu-img or virt-install.
        $result = testDispatch($action, $params);
        http_response_code(($result['success'] ?? false) ? 200 : 400);
        echo json_encode($result, JSON_UNESCAPED_SLASHES);
        return;
    }

    switch ($action) {
        case 'health':
            validateRequestMethod($action, ['GET']);
            $result = handleHealth($params);
            break;

        case 'system_overview':
            validateRequestMethod($action, ['GET']);
            $result = handleSystemOverview($params);
            break;

        case 'list_vms':
            validateRequestMethod($action, ['GET']);
            $result = handleListVmsPortable($params);
            break;

        case 'vm_action':
            validateRequestMethod($action, ['POST']);
            $result = handleVmActionPortable($params);
            break;

        case 'edit_vm':
            validateRequestMethod($action, ['POST']);
            $result = handleEditVmPortable($params);
            break;

        case 'edit_disk':
            validateRequestMethod($action, ['POST']);
            $result = handleEditDiskPortable($params);
            break;

        case 'delete_disk':
            validateRequestMethod($action, ['POST']);
            $result = handleDeleteDiskPortable($params);
            break;

        case 'create':
            validateRequestMethod($action, ['POST']);
            $result = handleCreateVm($params);
            break;

        case 'validate_vm_name':
            validateRequestMethod($action, ['POST']);
            $result = handleValidateVmName($params);
            break;

        case 'get_iso_list':
            validateRequestMethod($action, ['GET']);
            $result = handleGetIsoList($params);
            break;

        case 'get_storage_pools':
            validateRequestMethod($action, ['GET']);
            $result = handleGetStoragePools($params);
            break;

        case 'get_networks':
            validateRequestMethod($action, ['GET']);
            $result = handleGetNetworks($params);
            break;

        case 'get_host_resources':
            validateRequestMethod($action, ['GET']);
            $result = handleGetHostResources($params);
            break;

        case 'calculate_disk_space':
            validateRequestMethod($action, ['POST']);
            $result = handleCalculateDiskSpace($params);
            break;

        case 'preview_vm_configuration':
            validateRequestMethod($action, ['POST']);
            $result = handlePreviewVmConfiguration($params);
            break;

        case 'create_disk':
            validateRequestMethod($action, ['POST']);
            $result = handleCreateDisk($params);
            break;

        case 'attach_iso':
            validateRequestMethod($action, ['POST']);
            $result = handleAttachIso($params);
            break;

        case 'generate_vm_command':
            validateRequestMethod($action, ['POST']);
            $result = handleGenerateVmCommand($params);
            break;

        case 'create_vm':
            validateRequestMethod($action, ['POST']);
            $result = handleCreateVm($params);
            break;

        case 'create_vm_without_disk':
            validateRequestMethod($action, ['POST']);
            $result = handleCreateVmWithoutDisk($params);
            break;

        case 'create_windows_vm':
            validateRequestMethod($action, ['POST']);
            $result = handleCreateWindowsVm($params);
            break;

        case 'get_creation_status':
            validateRequestMethod($action, ['GET']);
            $result = handleGetCreationStatus($params);
            break;

        case 'cancel_creation':
            validateRequestMethod($action, ['POST']);
            $result = handleCancelCreation($params);
            break;

        case 'get_os_variants':
            validateRequestMethod($action, ['GET']);
            $result = handleGetOsVariants($params);
            break;

        case 'list_bridges':
            validateRequestMethod($action, ['GET']);
            $result = handleListBridges($params);
            break;

        case 'get_storage_usage':
            validateRequestMethod($action, ['GET']);
            $result = handleGetStorageUsage($params);
            break;

        case 'check_iso_exists':
            validateRequestMethod($action, ['POST']);
            $result = handleCheckIsoExists($params);
            break;

        case 'check_cpu_availability':
            validateRequestMethod($action, ['POST']);
            $result = handleCheckCpuAvailability($params);
            break;

        case 'check_memory_availability':
            validateRequestMethod($action, ['POST']);
            $result = handleCheckMemoryAvailability($params);
            break;

        // TEMPLATE ACTIONS
        case 'get_os_templates':
            validateRequestMethod($action, ['GET']);
            $result = handleGetOsTemplates($params);
            break;

        case 'get_database_templates':
            validateRequestMethod($action, ['GET']);
            $result = handleGetDatabaseTemplates($params);
            break;

        case 'get_templates':
            validateRequestMethod($action, ['GET']);
            $result = handleGetTemplates($params);
            break;

        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action',
                'allowed_actions' => array_keys(ACTION_ROUTES),
            ]);
            return;
    }

    if (is_array($result) && isset($result['success'])) {
        http_response_code($result['success'] ? 200 : 400);
        echo json_encode($result);
    } else {
        http_response_code(500);
        error_log("Invalid response from action handler for '{$action}'", 3, LOG_FILE);
        echo json_encode([
            'success' => false,
            'message' => 'Internal Server Error',
            'error_id' => uniqid('err_', true),
        ]);
    }
}

// ============================================
// HANDLE REQUEST
// ============================================

try {
    routeRequest();
} catch (Throwable $e) {
    $id = uniqid('err_', true);
    @error_log('[' . date('Y-m-d H:i:s') . '] ' . $id . ' ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL, 3, ERROR_LOG_FILE);
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Internal Server Error",
        "error_id" => $id,
    ]);
    exit;
}