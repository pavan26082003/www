<?php
declare(strict_types=1);

namespace App\Models;

use Config\RestDatabase;
use mysqli;
use mysqli_result;
use mysqli_stmt;
use RuntimeException;

abstract class RestBaseModel
{
    protected mysqli $db;

    /** @var array<string, bool> */
    private array $tableExistsCache = [];

    /** @var array<string, bool> */
    private array $columnExistsCache = [];

    /** @var array<string, string> */
    protected array $runtimeContext = [];

    public function __construct()
    {
        $this->db = RestDatabase::connect();
    }

    protected function db(): mysqli
    {
        if (!($this->db instanceof mysqli) || !$this->db->ping()) {
            $this->db = RestDatabase::reconnect();
        }

        return $this->db;
    }

    protected function prepare(string $sql): mysqli_stmt
    {
        $stmt = $this->db()->prepare($sql);

        if (!$stmt instanceof mysqli_stmt) {
            throw new RuntimeException(
                'Failed to prepare SQL statement: ' . $this->db()->error
            );
        }

        return $stmt;
    }

    /**
     * @param array<int, mixed> $params
     */
    protected function bindAndExecute(mysqli_stmt $stmt, array $params = []): void
    {
        if ($params !== []) {
            $types = $this->detectParamTypes($params);
            $refs = $this->makeReferences(array_values($params));

            array_unshift($refs, $types);

            if (!call_user_func_array([$stmt, 'bind_param'], $refs)) {
                $error = $stmt->error !== '' ? $stmt->error : 'Unknown bind_param error.';
                $stmt->close();
                throw new RuntimeException('Failed to bind SQL parameters: ' . $error);
            }
        }

        if (!$stmt->execute()) {
            $error = $stmt->error !== '' ? $stmt->error : 'Unknown execute error.';
            $stmt->close();
            throw new RuntimeException('Failed to execute SQL statement: ' . $error);
        }
    }

    /**
     * @param array<int, mixed> $params
     */
    protected function query(string $sql, array $params = []): mysqli_stmt
    {
        $stmt = $this->prepare($sql);
        $this->bindAndExecute($stmt, $params);
        return $stmt;
    }

    /**
     * @param array<int, mixed> $params
     * @return array<int, array<string, mixed>>
     */
    public function all(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->get_result();

        if (!$result instanceof mysqli_result) {
            $stmt->close();
            return [];
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $stmt->close();

        return is_array($rows) ? $rows : [];
    }

    /**
     * @param array<int, mixed> $params
     * @return array<string, mixed>|null
     */
    public function one(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->get_result();

        if (!$result instanceof mysqli_result) {
            $stmt->close();
            return null;
        }

        $row = $result->fetch_assoc();
        $result->free();
        $stmt->close();

        return is_array($row) ? $row : null;
    }

    /**
     * @param array<int, mixed> $params
     * @param mixed $default
     * @return mixed
     */
    public function scalar(string $sql, array $params = [], $default = null)
    {
        $row = $this->one($sql, $params);

        if ($row === null) {
            return $default;
        }

        $values = array_values($row);

        return array_key_exists(0, $values) ? $values[0] : $default;
    }

    /**
     * @param array<int, mixed> $params
     */
    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->query($sql, $params);
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();

        return $ok;
    }

    /**
     * @param array<int, mixed> $params
     */
    public function insert(string $sql, array $params = []): string
    {
        $stmt = $this->query($sql, $params);
        $insertId = (string) $this->db()->insert_id;
        $stmt->close();

        return $insertId;
    }

    public function tableExists(string $table): bool
    {
        $table = trim($table);
        if ($table === '') {
            return false;
        }

        $cacheKey = strtolower($table);
        if (array_key_exists($cacheKey, $this->tableExistsCache)) {
            return $this->tableExistsCache[$cacheKey];
        }

        $sql = "
            SELECT COUNT(*)
            FROM information_schema.tables
            WHERE table_schema = DATABASE()
              AND table_name = ?
        ";

        $exists = (int) $this->scalar($sql, [$table], 0) > 0;
        $this->tableExistsCache[$cacheKey] = $exists;

        return $exists;
    }

    public function columnExists(string $table, string $column): bool
    {
        $table = trim($table);
        $column = trim($column);

        if ($table === '' || $column === '') {
            return false;
        }

        $cacheKey = strtolower($table . '.' . $column);
        if (array_key_exists($cacheKey, $this->columnExistsCache)) {
            return $this->columnExistsCache[$cacheKey];
        }

        $sql = "
            SELECT COUNT(*)
            FROM information_schema.columns
            WHERE table_schema = DATABASE()
              AND table_name = ?
              AND column_name = ?
        ";

        $exists = (int) $this->scalar($sql, [$table, $column], 0) > 0;
        $this->columnExistsCache[$cacheKey] = $exists;

        return $exists;
    }

    public function uuidOrNull(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (preg_match(
            '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i',
            $value
        ) === 1) {
            return strtolower($value);
        }

        return null;
    }

    public function normalizeUuid(string $value, string $label = 'ID'): string
    {
        $uuid = $this->uuidOrNull($value);

        if ($uuid === null) {
            throw new RuntimeException($label . ' is invalid.');
        }

        return $uuid;
    }

    public function brandIdOrNull(?string $value): ?string
    {
        return $this->uuidOrNull($value);
    }

    public function branchIdOrNull(?string $value): ?string
    {
        return $this->uuidOrNull($value);
    }

    protected function findExistingTableName(array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            $name = trim((string) $candidate);
            if ($name !== '' && $this->tableExists($name)) {
                return $name;
            }
        }

        return null;
    }

    protected function currentSqlTimestamp(): string
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Release tables back to `available` when a paid bill's scheduled hold has
     * expired and there is no active order/bill state keeping the table busy.
     *
     * @param array<int, string> $tableIds
     */
    public function syncPaidTableAvailability(string $brandId, string $branchId, array $tableIds = []): int
    {
        $tablesTable = $this->findExistingTableName(['tables', 'restaurant_tables']);
        if ($tablesTable === null || !$this->tableExists('bills')) {
            return 0;
        }

        $tableIds = array_values(array_filter(array_map('strval', $tableIds), static function (string $value): bool {
            return trim($value) !== '';
        }));

        $params = [$brandId, $branchId];
        $tableScopeSql = '';
        if ($tableIds !== []) {
            $placeholders = implode(',', array_fill(0, count($tableIds), '?'));
            $tableScopeSql = " AND t.id IN ({$placeholders})";
            $params = array_merge($params, $tableIds);
        }

        $manualOverrideCheck = $this->columnExists($tablesTable, 'manual_status_override')
            ? "AND COALESCE(t.manual_status_override, 0) = 0"
            : '';
        $autoAvailableCheck = $this->columnExists($tablesTable, 'auto_available_at')
            ? "AND t.auto_available_at IS NOT NULL AND t.auto_available_at <= NOW()"
            : '';
        $currentOrderCheck = $this->columnExists($tablesTable, 'current_order_id')
            ? "AND COALESCE(t.current_order_id, '') = ''"
            : '';
        $currentBillCheck = $this->columnExists($tablesTable, 'current_bill_id')
            ? "AND COALESCE(t.current_bill_id, '') = ''"
            : '';

        $activeOrderCheck = '';
        if ($this->tableExists('orders')) {
            $activeOrderCheck = "
                AND NOT EXISTS (
                    SELECT 1
                    FROM orders o
                    WHERE o.table_id = t.id
                      AND o.brand_id = ?
                      AND o.branch_id = ?
                      AND COALESCE(o.is_active, 1) = 1
                      AND LOWER(COALESCE(o.status, '')) NOT IN ('closed', 'paid', 'cancelled')
                )";
            $params[] = $brandId;
            $params[] = $branchId;
        }

        $openBillCheck = "
            AND NOT EXISTS (
                SELECT 1
                FROM bills ob
                WHERE ob.table_id = t.id
                  AND ob.brand_id = ?
                  AND ob.branch_id = ?
                  AND LOWER(COALESCE(ob.payment_status, 'unpaid')) IN ('unpaid', 'partial', 'pending', 'awaiting_collection', 'processing')
                  AND LOWER(COALESCE(ob.bill_status, 'requested')) NOT IN ('closed', 'cancelled')
            )";
        $params[] = $brandId;
        $params[] = $branchId;

        $paidHoldCheck = "
            AND EXISTS (
                SELECT 1
                FROM bills pb
                WHERE pb.table_id = t.id
                  AND pb.brand_id = ?
                  AND pb.branch_id = ?
                  AND LOWER(COALESCE(pb.payment_status, '')) = 'paid'
                  AND pb.auto_release_at IS NOT NULL
                  AND pb.auto_release_at <= NOW()
            )";
        $params[] = $brandId;
        $params[] = $branchId;

        $eligibleRows = $this->all(
            "SELECT t.id
             FROM {$tablesTable} t
             WHERE t.brand_id = ?
               AND t.branch_id = ?
               AND LOWER(COALESCE(t.status, 'available')) <> 'available'
               {$tableScopeSql}
               {$manualOverrideCheck}
               {$autoAvailableCheck}
               {$currentOrderCheck}
               {$currentBillCheck}
               {$activeOrderCheck}
               {$openBillCheck}
               {$paidHoldCheck}",
            $params
        );

        if ($eligibleRows === []) {
            return 0;
        }

        $now = $this->currentSqlTimestamp();
        $released = 0;

        foreach ($eligibleRows as $row) {
            $tableId = trim((string) ($row['id'] ?? ''));
            if ($tableId === '') {
                continue;
            }

            $updates = [];
            if ($this->columnExists($tablesTable, 'status')) {
                $updates[] = 'status = ?';
            }
            if ($this->columnExists($tablesTable, 'manual_status_override')) {
                $updates[] = 'manual_status_override = ?';
            }
            if ($this->columnExists($tablesTable, 'auto_available_at')) {
                $updates[] = 'auto_available_at = NULL';
            }
            if ($this->columnExists($tablesTable, 'current_order_id')) {
                $updates[] = 'current_order_id = NULL';
            }
            if ($this->columnExists($tablesTable, 'current_bill_id')) {
                $updates[] = 'current_bill_id = NULL';
            }
            if ($this->columnExists($tablesTable, 'status_updated_at')) {
                $updates[] = 'status_updated_at = ?';
            }
            if ($this->columnExists($tablesTable, 'updated_at')) {
                $updates[] = 'updated_at = ?';
            }

            if ($updates === []) {
                continue;
            }

            $updateParams = [];
            if ($this->columnExists($tablesTable, 'status')) {
                $updateParams[] = 'available';
            }
            if ($this->columnExists($tablesTable, 'manual_status_override')) {
                $updateParams[] = 0;
            }
            if ($this->columnExists($tablesTable, 'status_updated_at')) {
                $updateParams[] = $now;
            }
            if ($this->columnExists($tablesTable, 'updated_at')) {
                $updateParams[] = $now;
            }
            $updateParams[] = $tableId;

            $ok = $this->execute(
                "UPDATE {$tablesTable}
                 SET " . implode(', ', $updates) . "
                 WHERE id = ?",
                $updateParams
            );

            if ($ok) {
                $released++;
            }
        }

        return $released;
    }

    public function tableIdOrNull(?string $value): ?string
    {
        return $this->uuidOrNull($value);
    }

    public function userIdOrNull(?string $value): ?string
    {
        return $this->uuidOrNull($value);
    }

    public function requireBrandId(string $value): string
    {
        return $this->normalizeUuid($value, 'Brand ID');
    }

    public function requireBranchId(string $value): string
    {
        return $this->normalizeUuid($value, 'Branch ID');
    }

    public function requireTableId(string $value): string
    {
        return $this->normalizeUuid($value, 'Table ID');
    }

    public function requireUserId(string $value): string
    {
        return $this->normalizeUuid($value, 'User ID');
    }

    /**
     * Minimal login resolver for restaurant SaaS.
     * Resolves only:
     * user_id, brand_id, branch_id, role, email.
     *
     * @return array<string, string>
     */
    public function resolveActiveUserContextByEmail(string $email, ?string $preferredBranchId = null): array
    {
        $email = $this->normalizeEmail($email);
        if ($email === '') {
            throw new RuntimeException('Valid user email is required for restaurant access.');
        }

        $user = $this->findActiveUserByEmail($email);
        if ($user === null) {
            throw new RuntimeException('No active restaurant user exists for email: ' . $email);
        }

        $userId = $this->requireUserId((string) ($user['user_id'] ?? ''));
        $brandId = $this->requireBrandId((string) ($user['brand_id'] ?? ''));
        $defaultBranchId = $this->requireBranchId((string) ($user['default_branch_id'] ?? ''));

        $branchAccess = $this->resolveUserBranchAccess($userId, $preferredBranchId, $defaultBranchId);

        $branchId = $this->branchIdOrNull((string) ($branchAccess['branch_id'] ?? '')) ?? $defaultBranchId;

        $role = strtolower(trim((string) ($branchAccess['role'] ?? '')));
        if ($role === '') {
            $role = strtolower(trim((string) ($user['role_type'] ?? '')));
        }

        if ($role === '') {
            throw new RuntimeException('Restaurant role was not resolved for this user.');
        }

        $branch = $this->findActiveBranchById($branchId, $brandId);
        if ($branch === null) {
            throw new RuntimeException('Assigned restaurant branch is invalid or inactive.');
        }

        $context = [
            'user_id' => $userId,
            'brand_id' => $brandId,
            'branch_id' => $branchId,
            'role' => $role,
            'email' => $email,
        ];

        $this->runtimeContext = $context;

        return $context;
    }

    /**
     * @param array<string, string> $context
     */
    public function setRuntimeContext(array $context): self
    {
        $this->runtimeContext = $context;
        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getRuntimeContext(): array
    {
        return $this->runtimeContext;
    }

    public function runtimeBrandId(): string
    {
        return $this->requireBrandId((string) ($this->runtimeContext['brand_id'] ?? ''));
    }

    public function runtimeBranchId(): string
    {
        return $this->requireBranchId((string) ($this->runtimeContext['branch_id'] ?? ''));
    }

    public function runtimeUserId(): string
    {
        return $this->requireUserId((string) ($this->runtimeContext['user_id'] ?? ''));
    }

    public function runtimeUserRole(): string
    {
        $role = strtolower(trim((string) ($this->runtimeContext['role'] ?? '')));
        if ($role === '') {
            throw new RuntimeException('Runtime user role is missing.');
        }

        return $role;
    }

    public function userHasBranchAccess(string $userId, string $branchId): bool
    {
        $userId = $this->requireUserId($userId);
        $branchId = $this->requireBranchId($branchId);

        $sql = "
            SELECT COUNT(*) AS total
            FROM user_access
            WHERE user_id = ?
              AND branch_id = ?
              AND access_status = 'active'
        ";

        return (int) $this->scalar($sql, [$userId, $branchId], 0) > 0;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function findActiveUserByEmail(string $email): ?array
    {
        $sql = "
            SELECT
                u.id AS user_id,
                u.brand_id,
                u.default_branch_id,
                u.email,
                u.role_type
            FROM users u
            WHERE LOWER(u.email) = ?
              AND u.is_active = 1
              AND LOWER(COALESCE(u.status, '')) = 'active'
            LIMIT 1
        ";

        return $this->one($sql, [$this->normalizeEmail($email)]);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function findActiveBranchById(string $branchId, string $brandId): ?array
    {
        $branchId = $this->requireBranchId($branchId);
        $brandId = $this->requireBrandId($brandId);

        $sql = "
            SELECT
                br.id
            FROM branches br
            WHERE br.id = ?
              AND br.brand_id = ?
              AND LOWER(COALESCE(br.status, '')) = 'active'
            LIMIT 1
        ";

        return $this->one($sql, [$branchId, $brandId]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function resolveUserBranchAccess(string $userId, ?string $preferredBranchId, string $defaultBranchId): array
    {
        $userId = $this->requireUserId($userId);
        $defaultBranchId = $this->requireBranchId($defaultBranchId);

        $preferred = $preferredBranchId !== null ? $this->branchIdOrNull($preferredBranchId) : null;

        if ($preferred !== null) {
            $preferredAccess = $this->one(
                "
                SELECT
                    ua.branch_id,
                    ua.role_at_branch AS role
                FROM user_access ua
                WHERE ua.user_id = ?
                  AND ua.branch_id = ?
                  AND ua.access_status = 'active'
                LIMIT 1
                ",
                [$userId, $preferred]
            );

            if (is_array($preferredAccess)) {
                return $preferredAccess;
            }
        }

        $defaultAccess = $this->one(
            "
            SELECT
                ua.branch_id,
                ua.role_at_branch AS role
            FROM user_access ua
            WHERE ua.user_id = ?
              AND ua.branch_id = ?
              AND ua.access_status = 'active'
            LIMIT 1
            ",
            [$userId, $defaultBranchId]
        );

        if (is_array($defaultAccess)) {
            return $defaultAccess;
        }

        $firstAccess = $this->one(
            "
            SELECT
                ua.branch_id,
                ua.role_at_branch AS role
            FROM user_access ua
            WHERE ua.user_id = ?
              AND ua.access_status = 'active'
            ORDER BY ua.updated_at DESC, ua.created_at DESC
            LIMIT 1
            ",
            [$userId]
        );

        if (is_array($firstAccess)) {
            return $firstAccess;
        }

        return [
            'branch_id' => $defaultBranchId,
            'role' => '',
        ];
    }

    private function normalizeEmail(string $email): string
    {
        $email = strtolower(trim($email));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return '';
        }

        return $email;
    }

    /**
     * @param array<int, mixed> $params
     */
    private function detectParamTypes(array $params): string
    {
        $types = '';

        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
                continue;
            }

            if (is_float($param)) {
                $types .= 'd';
                continue;
            }

            if (is_null($param)) {
                $types .= 's';
                continue;
            }

            if (is_bool($param)) {
                $types .= 'i';
                continue;
            }

            $types .= 's';
        }

        return $types;
    }

    /**
     * @param array<int, mixed> $values
     * @return array<int, mixed>
     */
    private function makeReferences(array $values): array
    {
        foreach ($values as $index => $value) {
            if (is_bool($value)) {
                $values[$index] = $value ? 1 : 0;
            } elseif ($value === null) {
                $values[$index] = null;
            }
        }

        $refs = [];
        foreach ($values as $index => &$value) {
            $refs[$index] = &$value;
        }

        return $refs;
    }
}
