<?php
/**
 * GRID User Identity API
 * Location: C:\Program Files\Ampps\www\v8\grid_api.php
 * Endpoint: /v8/grid_api.php (or /v8/api/me)
 */

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
}

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// 1. Read existing CodeIgniter session cookie
$sessionId = $_COOKIE['ci_session'] ?? '';

if (!$sessionId || !preg_match('/^[0-9a-v]{32}$/', $sessionId)) {
    http_response_code(401);
    echo json_encode([
        'status'  => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

// 2. Database connection configuration (parsed from v8/config.php with fallback)
$configFile = __DIR__ . '/config.php';
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'factor_db2';

if (file_exists($configFile)) {
    $configContent = file_get_contents($configFile);
    if (preg_match('/define\(\s*["\']DB_HOST["\']\s*,\s*["\'](.*?)["\']\s*\)/', $configContent, $m)) {
        $dbHost = $m[1];
    }
    if (preg_match('/define\(\s*["\']DB_USER["\']\s*,\s*["\'](.*?)["\']\s*\)/', $configContent, $m)) {
        $dbUser = $m[1];
    }
    if (preg_match('/define\(\s*["\']DB_PASS["\']\s*,\s*["\'](.*?)["\']\s*\)/', $configContent, $m)) {
        $dbPass = $m[1];
    }
    if (preg_match('/define\(\s*["\']DB_NAME["\']\s*,\s*["\'](.*?)["\']\s*\)/', $configContent, $m)) {
        $dbName = $m[1];
    }
}

try {
    $mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($mysqli->connect_error) {
        throw new Exception($mysqli->connect_error);
    }
} catch (\Throwable $e) {
    // Fallback for local AMPPS environment where root password is 'mysql'
    try {
        $mysqli = new mysqli($dbHost, $dbUser, 'mysql', $dbName);
    } catch (\Throwable $ex) {
        // Fallback for factor_db1 if factor_db2 fails
        $mysqli = new mysqli($dbHost, $dbUser, 'mysql', 'factor_db1');
    }
}

if (!$mysqli || $mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode([
        'status'  => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

$mysqli->set_charset('utf8mb4');

// 3. Query ci_sessions table to validate authenticated session
$stmt = $mysqli->prepare("SELECT data, timestamp FROM ci_sessions WHERE id = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => 'Session query failed']);
    exit;
}

$stmt->bind_param("s", $sessionId);
$stmt->execute();
$sessionRes = $stmt->get_result();
$sessionRow = $sessionRes ? $sessionRes->fetch_assoc() : null;
$stmt->close();

if (!$sessionRow) {
    http_response_code(401);
    echo json_encode([
        'status'  => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

// Check session expiration (default CI4 expiration: 7200 seconds)
if ((time() - (int)$sessionRow['timestamp']) > 7200) {
    http_response_code(401);
    echo json_encode([
        'status'  => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

// 4. Extract user_id from session data string
$userId = null;
if (preg_match('/user_id\|[si]:\d*:"?([^";]+)"?;/', $sessionRow['data'], $matches)) {
    $userId = $matches[1];
}

if (!$userId) {
    http_response_code(401);
    echo json_encode([
        'status'  => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

// 5. Query users table for active, non-deleted user
$userStmt = $mysqli->prepare("SELECT grid_id, first_name, last_name, phone FROM users WHERE id = ? AND status = 'active' AND deleted = 0");
if (!$userStmt) {
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => 'User query failed']);
    exit;
}

$userStmt->bind_param("s", $userId);
$userStmt->execute();
$userRes = $userStmt->get_result();
$user = $userRes ? $userRes->fetch_assoc() : null;
$userStmt->close();

if (!$user) {
    http_response_code(404);
    echo json_encode([
        'status'  => false,
        'message' => 'User not found.'
    ]);
    exit;
}

// 6. Return identity fields (grid_id, first_name, last_name, phone)
http_response_code(200);
echo json_encode([
    'status' => true,
    'data'   => [
        'grid_id'    => $user['grid_id'],
        'first_name' => $user['first_name'],
        'last_name'  => $user['last_name'],
        'phone'      => $user['phone'] ?? ''
    ]
]);
