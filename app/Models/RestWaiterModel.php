<?php

declare(strict_types=1);

namespace App\Models;

use RuntimeException;
use Throwable;

final class RestWaiterModel extends RestBaseModel
{
    protected string $defaultBrandId = '00000000-0000-0000-0000-000000000001';
    protected string $defaultBranchId = '00000000-0000-0000-0000-000000000101';
    protected string $defaultUserId = '00000000-0000-0000-0000-000000001001';

    public function entityIdOrNull(?string $value): ?string
    {
        $value = strtolower(trim((string)$value));

        if ($value === '') {
            return null;
        }

        if (preg_match('/^[a-f0-9]{32}$/', $value) === 1) {
            return $value;
        }

        if (preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/', $value) === 1) {
            return $value;
        }

        return null;
    }

    public function normalizeEntityIdOrDefault(?string $value, string $default): string
    {
        $normalized = $this->entityIdOrNull($value);
        if ($normalized !== null) {
            return $normalized;
        }

        return strtolower(trim($default));
    }

    public function begin(): void
    {
        $this->db()->begin_transaction();
    }

    public function commit(): void
    {
        $this->db()->commit();
    }

    public function rollback(): void
    {
        $this->db()->rollback();
    }

    protected function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    protected function now(): string
    {
        return date('Y-m-d H:i:s');
    }

    protected function parseJson(?string $json): array
    {
        $decoded = json_decode((string)$json, true);
        return is_array($decoded) ? $decoded : array();
    }

    protected function jsonEncode($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $json === false ? null : $json;
    }

    protected function auditLog(
        ?string $brandId,
        ?string $branchId,
        ?string $userId,
        string $entityType,
        ?string $entityId,
        string $action,
        $oldData = null,
        $newData = null
    ): void {
        if (!$this->tableExists('audit_logs')) {
            return;
        }

        $columns = array(
            'id',
            'brand_id',
            'branch_id',
            'user_id',
            'module',
            'entity_type',
            'entity_id',
            'action',
            'old_data_json',
            'new_data_json',
            'created_at',
        );

        $values = array(
            $this->uuid(),
            $brandId,
            $branchId,
            $userId,
            'waiter',
            $entityType,
            $entityId,
            $action,
            $this->jsonEncode($oldData),
            $this->jsonEncode($newData),
            $this->now(),
        );

        if ($this->columnExists('audit_logs', 'ip_address')) {
            $columns[] = 'ip_address';
            $values[] = (string)($_SERVER['REMOTE_ADDR'] ?? '');
        }

        if ($this->columnExists('audit_logs', 'user_agent')) {
            $columns[] = 'user_agent';
            $values[] = (string)($_SERVER['HTTP_USER_AGENT'] ?? '');
        }

        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        $this->execute(
            "INSERT INTO audit_logs (" . implode(', ', $columns) . ")
             VALUES ({$placeholders})",
            $values
        );
    }

    protected function parseTableCodes(?string $csv): array
    {
        $csv = trim((string)$csv);
        if ($csv === '') {
            return array();
        }

        $parts = array_map('trim', explode(',', $csv));
        $parts = array_filter($parts, static function ($value) {
            return $value !== '';
        });

        return array_values(array_unique($parts));
    }

    protected function timeAgo(?string $datetime, string $fallback = 'Now'): string
    {
        $datetime = trim((string)$datetime);
        if ($datetime === '') {
            return $fallback;
        }

        $timestamp = strtotime($datetime);
        if ($timestamp === false) {
            return $fallback;
        }

        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'Just now';
        }

        if ($diff < 3600) {
            return floor($diff / 60) . ' min ago';
        }

        if ($diff < 86400) {
            return floor($diff / 3600) . ' hr ago';
        }

        return floor($diff / 86400) . ' day ago';
    }

    protected function formatShift(?string $start, ?string $end): string
    {
        $start = trim((string)$start);
        $end = trim((string)$end);

        if ($start === '' || $end === '') {
            return 'Shift Not Set';
        }

        $startTs = strtotime($start);
        $endTs = strtotime($end);

        if ($startTs === false || $endTs === false) {
            return 'Shift Not Set';
        }

        return date('g:i A', $startTs) . ' – ' . date('g:i A', $endTs);
    }

    protected function mapPriority(string $requestType): string
    {
        $requestType = strtolower(trim($requestType));

        if (in_array($requestType, array('bill', 'bill_request', 'assistance'), true)) {
            return 'high';
        }

        return 'normal';
    }

    protected function normalizeOrderStatusValue(?string $status): string
    {
        $status = strtolower(trim((string)$status));
        if ($status === '' || $status === 'pending' || $status === 'placed') return 'new';
        if ($status === 'cooking') return 'preparing';
        if ($status === 'paid' || $status === 'closed') return 'completed';
        $allowed = array('new', 'accepted', 'preparing', 'ready', 'served', 'completed', 'cancelled');
        return in_array($status, $allowed, true) ? $status : 'new';
    }

    protected function normalizeRequestStatusValue(?string $status): string
    {
        $status = strtolower(trim((string)$status));
        if ($status === '' || $status === 'pending' || $status === 'requested' || $status === 'created') return 'new';
        if ($status === 'accepted' || $status === 'acknowledged' || $status === 'in_progress' || $status === 'processing') return 'approved';
        if ($status === 'resolved' || $status === 'completed' || $status === 'done') return 'closed';
        if ($status === 'cancelled' || $status === 'canceled' || $status === 'rejected') return 'cancelled';
        $allowed = array('new', 'approved', 'cancelled', 'closed');
        return in_array($status, $allowed, true) ? $status : 'new';
    }

    protected function normalizeBillStatusValue(?string $status): string
    {
        $status = strtolower(trim((string)$status));
        if ($status === '' || $status === 'pending' || $status === 'requested') return 'new';
        if ($status === 'generated' || $status === 'accepted') return 'approved';
        if ($status === 'paid' || $status === 'completed') return 'closed';
        if ($status === 'cancelled' || $status === 'canceled' || $status === 'rejected') return 'cancelled';
        $allowed = array('new', 'approved', 'cancelled', 'closed');
        return in_array($status, $allowed, true) ? $status : 'new';
    }

    protected function normalizePaymentStatusValue(?string $status): string
    {
        $status = strtolower(trim((string)$status));
        if ($status === '' || $status === 'pending' || $status === 'requested' || $status === 'unpaid') return 'unpaid';
        if ($status === 'awaiting_collection' || $status === 'processing' || $status === 'pending_payment' || $status === 'initiated' || $status === 'in_progress' || $status === 'in_process') return 'in_processing';
        if ($status === 'success' || $status === 'successful' || $status === 'completed' || $status === 'closed') return 'paid';
        $allowed = array('unpaid', 'in_processing', 'paid');
        return in_array($status, $allowed, true) ? $status : 'unpaid';
    }

    protected function mapTableState(string $tableStatus, ?array $order, bool $hasRequest, bool $isBillRequest, bool $isReady): array
    {
        $tableStatus = strtolower(trim($tableStatus));
        $orderStatus = strtolower(trim((string)($order['status'] ?? '')));

        if ($isReady) {
            return array('occupied', 'Food Ready');
        }

        if ($isBillRequest) {
            return array('occupied', 'Bill Requested');
        }

        if ($hasRequest) {
            return array('waiting', 'Needs Service');
        }

        if ($order !== null) {
            if (in_array($orderStatus, array('billing', 'payment_pending'), true)) {
                return array('occupied', 'Payment Pending');
            }

            return array('occupied', 'Dining');
        }

        if ($tableStatus === 'occupied') {
            return array('occupied', 'Occupied');
        }

        return array('available', 'Available');
    }

    protected function normalizePermissions(array $permissions): array
    {
        $defaults = array(
            'take_order'             => true,
            'generate_bill'          => false,
            'collect_payment'        => false,
            'manage_service_request' => true,
            'mark_served'            => true,
            'manage_tables'          => true,
        );
        $normalized = array_merge($defaults, $permissions);
        $normalized['generate_bill'] = false;
        $normalized['collect_payment'] = false;

        return $normalized;
    }

    protected function normalizeTableStatus(string $status): string
    {
        $status = strtolower(trim($status));
        if ($status === 'payment' || $status === 'payments' || $status === 'payment_pending') return 'occupied';
        if ($status === 'blocked') return 'maintenance';
        $allowed = array('available', 'occupied', 'reserved', 'waiting', 'maintenance');
        return in_array($status, $allowed, true) ? $status : 'available';
    }

    protected function waiterTableStatusOptions(): array
    {
        return array('available', 'occupied', 'reserved', 'waiting', 'maintenance');
    }

    public function getBrand(string $brandId): ?array
    {
        if (!$this->tableExists('brands')) {
            return null;
        }

        return $this->one(
            "SELECT * FROM brands WHERE id = ? LIMIT 1",
            array($brandId)
        );
    }

    public function getBranch(string $branchId): ?array
    {
        if (!$this->tableExists('branches')) {
            return null;
        }

        return $this->one(
            "SELECT * FROM branches WHERE id = ? LIMIT 1",
            array($branchId)
        );
    }

    public function pushRealtimeEvent(
        string $brandId,
        string $branchId,
        string $eventType,
        ?string $entityType = null,
        ?string $entityId = null,
        ?array $payload = null
    ): bool {
        if (!$this->tableExists('realtime_events')) {
            return true;
        }

        $jsonPayload = $payload !== null
            ? json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            : null;

        return $this->execute(
            "INSERT INTO realtime_events (
                brand_id, branch_id, event_type, entity_type, entity_id, payload_json, created_at, is_published
             ) VALUES (?, ?, ?, ?, ?, ?, ?, 0)",
            array($brandId, $branchId, $eventType, $entityType, $entityId, $jsonPayload, $this->now())
        );
    }

    public function getRealtimeEvents(string $brandId, string $branchId, int $afterId = 0, int $limit = 50): array
    {
        if (!$this->tableExists('realtime_events')) {
            return [];
        }

        $limit = max(1, min(200, $limit));

        return $this->all(
            "SELECT id, brand_id, branch_id, event_type, entity_type, entity_id, payload_json, created_at
             FROM realtime_events
             WHERE brand_id = ?
               AND branch_id = ?
               AND id > ?
             ORDER BY id ASC
             LIMIT {$limit}",
            [$brandId, $branchId, $afterId]
        );
    }

    protected function normalizeScope(?string $scope): string
    {
        $scope = strtolower(trim((string)$scope));
        return $scope === 'all' ? 'all' : 'assigned';
    }

    protected function getRoleName(array $context): string
    {
        return strtolower(trim((string)($context['role_at_branch'] ?? $context['role_type'] ?? 'waiter')));
    }

    protected function canUseGlobalScope(array $context): bool
    {
        return in_array($this->getRoleName($context), ['manager', 'admin'], true);
    }

    public function getWaiterContext(string $brandId, string $branchId, string $userId): ?array
    {
        if (!$this->tableExists('users')) {
            return null;
        }

        $select = [
            "u.id AS user_id",
            "u.brand_id",
            "u.full_name",
            "u.employee_code",
            "u.email",
            "u.phone",
            "u.role_type",
            "u.status AS user_status",
            "u.is_active"
        ];

        if ($this->columnExists('users', 'photo')) {
            $select[] = "u.photo";
        }

        if ($this->columnExists('users', 'profile_pic')) {
            $select[] = "u.profile_pic";
        }

        $joinUserAccess = $this->tableExists('user_access');
        $joinBranch = $this->tableExists('branches');

        if ($joinUserAccess) {
            $select[] = "ua.branch_id";
            $select[] = "ua.role_at_branch";
            $select[] = "ua.shift_start_time";
            $select[] = "ua.shift_end_time";
            $select[] = "ua.access_status";

            if ($this->columnExists('user_access', 'permissions_json')) {
                $select[] = "ua.permissions_json";
            }

            if ($this->columnExists('user_access', 'assigned_table_codes')) {
                $select[] = "ua.assigned_table_codes";
            }
        }

        if ($joinBranch) {
            $select[] = "b.name AS branch_name";

            if ($this->columnExists('branches', 'city')) {
                $select[] = "b.city AS branch_city";
            }

            if ($this->columnExists('branches', 'address')) {
                $select[] = "b.address AS branch_address";
            }

            if ($this->columnExists('branches', 'status')) {
                $select[] = "b.status AS branch_status";
            }
        }

        $sql = "SELECT " . implode(", ", $select) . " FROM users u";

        if ($joinUserAccess) {
            $sql .= " INNER JOIN user_access ua
                ON ua.user_id = u.id
               AND ua.branch_id = ?";
        }

        if ($joinBranch && $joinUserAccess) {
            $sql .= " LEFT JOIN branches b ON b.id = ua.branch_id";
        }

        $sql .= " WHERE u.id = ?
               AND u.brand_id = ?";

        if ($joinUserAccess) {
            $sql .= " AND (ua.access_status IS NULL OR ua.access_status = 'active')";
        }

        $sql .= " LIMIT 1";

        $params = $joinUserAccess
            ? [$branchId, $userId, $brandId]
            : [$userId, $brandId];

        $row = $this->one($sql, $params);

        if (!$row) {
            return null;
        }

        $row['permissions'] = isset($row['permissions_json'])
            ? $this->normalizePermissions($this->parseJson($row['permissions_json']))
            : $this->normalizePermissions([]);

        $row['assigned_tables'] = isset($row['assigned_table_codes'])
            ? $this->parseTableCodes($row['assigned_table_codes'])
            : [];

        return $row;
    }

    protected function getAllActiveTables(string $brandId, string $branchId): array
    {
        if (!$this->tableExists('tables')) {
            return [];
        }

        return $this->all(
            "SELECT
                t.id,
                t.brand_id,
                t.branch_id,
                t.table_number,
                t.table_code,
                t.capacity,
                t.shape,
                t.status,
                t.current_order_id,
                t.current_bill_id,
                t.status_updated_at,
                t.updated_at,
                t.is_active,
                t.is_blocked,
                t.block_reason
             FROM tables t
             WHERE t.brand_id = ?
               AND t.branch_id = ?
               AND t.is_active = 1
               AND (t.is_blocked = 0 OR t.is_blocked IS NULL)
             ORDER BY t.table_code ASC",
            [$brandId, $branchId]
        );
    }

    protected function resolveAccessScope(
        string $brandId,
        string $branchId,
        string $userId,
        string $scope = 'assigned'
    ): ?array {
        $context = $this->getWaiterContext($brandId, $branchId, $userId);
        if (!$context) {
            return null;
        }

        $scope = $this->normalizeScope($scope);
        $role = $this->getRoleName($context);
        $permissions = $this->normalizePermissions((array)($context['permissions'] ?? []));
        $assignedTableCodes = (array)($context['assigned_tables'] ?? []);

        $tables = ($scope === 'all' && $this->canUseGlobalScope($context))
            ? $this->getAllActiveTables($brandId, $branchId)
            : $this->getAssignedTables($brandId, $branchId, $assignedTableCodes);

        $tableIds = array_values(array_filter(array_map(static function ($row) {
            return (string)($row['id'] ?? '');
        }, $tables)));

        return [
            'context'              => $context,
            'role'                 => $role,
            'permissions'          => $permissions,
            'scope'                => $scope,
            'assigned_table_codes' => $assignedTableCodes,
            'tables'               => $tables,
            'table_ids'            => $tableIds,
        ];
    }

    protected function canAccessTable(array $scopeData, string $tableId): bool
    {
        return $tableId !== '' && in_array($tableId, (array)($scopeData['table_ids'] ?? []), true);
    }

    protected function getScopedTableRecord(string $brandId, string $branchId, string $tableId, array $scopeData): ?array
    {
        if (!$this->canAccessTable($scopeData, $tableId)) {
            return null;
        }

        return $this->one(
            "SELECT *
             FROM tables
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?
             LIMIT 1",
            [$tableId, $brandId, $branchId]
        );
    }

    protected function getScopedOrderRecord(string $brandId, string $branchId, string $orderId, array $scopeData): ?array
    {
        $order = $this->one(
            "SELECT *
             FROM orders
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?
             LIMIT 1",
            [$orderId, $brandId, $branchId]
        );

        if (!$order) {
            return null;
        }

        return $this->canAccessTable($scopeData, (string)($order['table_id'] ?? '')) ? $order : null;
    }

    protected function getScopedBillRecord(string $brandId, string $branchId, string $billId, array $scopeData): ?array
    {
        $bill = $this->one(
            "SELECT *
             FROM bills
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?
             LIMIT 1",
            [$billId, $brandId, $branchId]
        );

        if (!$bill) {
            return null;
        }

        return $this->canAccessTable($scopeData, (string)($bill['table_id'] ?? '')) ? $bill : null;
    }

    protected function getScopedServiceRequestRecord(
        string $brandId,
        string $branchId,
        string $requestId,
        array $scopeData
    ): ?array {
        $request = $this->one(
            "SELECT *
             FROM service_requests
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?
             LIMIT 1",
            [$requestId, $brandId, $branchId]
        );

        if (!$request) {
            return null;
        }

        return $this->canAccessTable($scopeData, (string)($request['table_id'] ?? '')) ? $request : null;
    }

    public function getAssignedTables(string $brandId, string $branchId, array $assignedTableCodes): array
    {
        if (!$this->tableExists('tables')) {
            return array();
        }

        if (empty($assignedTableCodes)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($assignedTableCodes), '?'));
        $params = array_merge([$brandId, $branchId], array_values($assignedTableCodes));

        return $this->all(
            "SELECT
                t.id,
                t.brand_id,
                t.branch_id,
                t.table_number,
                t.table_code,
                t.capacity,
                t.shape,
                t.status,
                t.current_order_id,
                t.current_bill_id,
                t.status_updated_at,
                t.updated_at,
                t.is_active,
                t.is_blocked,
                t.block_reason
             FROM tables t
             WHERE t.brand_id = ?
               AND t.branch_id = ?
               AND t.is_active = 1
               AND (t.is_blocked = 0 OR t.is_blocked IS NULL)
               AND t.table_code IN ($placeholders)
             ORDER BY t.table_code ASC",
            $params
        );
    }

    public function getActiveOrdersForTables(array $tableIds): array
    {
        if (empty($tableIds) || !$this->tableExists('orders')) {
            return array();
        }

        $placeholders = implode(',', array_fill(0, count($tableIds), '?'));
        $rows = $this->all(
            "SELECT
                o.id,
                o.brand_id,
                o.branch_id,
                o.table_id,
                o.assigned_waiter_id,
                o.bill_id,
                o.order_number,
                o.order_source,
                o.order_type,
                o.status,
                o.is_active,
                o.customer_count,
                o.notes,
                o.subtotal,
                o.tax_amount,
                o.service_charge,
                o.discount_amount,
                o.grand_total,
                o.bill_requested_at,
                o.opened_at,
                o.created_at,
                o.updated_at
             FROM orders o
             WHERE o.table_id IN ($placeholders)
               AND o.is_active = 1
               AND LOWER(COALESCE(o.status, '')) NOT IN ('closed', 'paid', 'completed', 'cancelled')
             ORDER BY o.created_at DESC",
            array_values($tableIds)
        );

        $map = array();
        foreach ($rows as $row) {
            $tableId = (string)($row['table_id'] ?? '');
            if ($tableId !== '' && !isset($map[$tableId])) {
                $map[$tableId] = $row;
            }
        }

        return $map;
    }

    public function getOpenServiceRequestsForTables(array $tableIds): array
    {
        if (empty($tableIds) || !$this->tableExists('service_requests')) {
            return array();
        }

        $placeholders = implode(',', array_fill(0, count($tableIds), '?'));

        return $this->all(
            "SELECT
                sr.id,
                sr.brand_id,
                sr.branch_id,
                sr.table_id,
                sr.order_id,
                sr.assigned_to_user_id,
                sr.request_type,
                sr.message,
                sr.status,
                sr.requested_at,
                sr.acknowledged_at,
                sr.resolved_at,
                sr.created_at,
                t.table_code
             FROM service_requests sr
             INNER JOIN tables t
                ON t.id = sr.table_id
             WHERE sr.table_id IN ($placeholders)
               AND LOWER(COALESCE(sr.request_type, '')) NOT IN ('pickup', 'bill', 'bill_request')
               AND LOWER(COALESCE(sr.status, '')) IN ('new', 'approved', 'pending', 'accepted', 'in_progress')
             ORDER BY sr.requested_at DESC, sr.created_at DESC",
            array_values($tableIds)
        );
    }

    public function getLatestOpenRequestMap(array $requests): array
    {
        $map = array();

        foreach ($requests as $row) {
            $tableId = (string)($row['table_id'] ?? '');
            if ($tableId !== '' && !isset($map[$tableId])) {
                $map[$tableId] = $row;
            }
        }

        return $map;
    }

    public function getReadyPickupsForTables(array $tableIds): array
    {
        if (empty($tableIds) || !$this->tableExists('orders') || !$this->tableExists('order_items')) {
            return array();
        }

        $placeholders = implode(',', array_fill(0, count($tableIds), '?'));

        return $this->all(
            "SELECT
                o.id AS order_id,
                o.table_id,
                o.order_number,
                o.updated_at,
                t.table_code,
                COUNT(oi.id) AS item_count
             FROM orders o
             INNER JOIN tables t
                ON t.id = o.table_id
             INNER JOIN order_items oi
                ON oi.order_id = o.id
             WHERE o.table_id IN ($placeholders)
               AND oi.status = 'ready'
             GROUP BY o.id, o.table_id, o.order_number, o.updated_at, t.table_code
             ORDER BY o.updated_at DESC",
            array_values($tableIds)
        );
    }

    public function getPendingBillsForTables(array $tableIds): array
    {
        if (empty($tableIds) || !$this->tableExists('bills')) {
            return array();
        }

        $placeholders = implode(',', array_fill(0, count($tableIds), '?'));

        return $this->all(
            "SELECT
                b.id,
                b.order_id,
                b.table_id,
                b.bill_number,
                b.bill_status,
                b.payment_status,
                b.total_amount,
                b.updated_at,
                t.table_code
             FROM bills b
             INNER JOIN tables t
                ON t.id = b.table_id
             WHERE b.table_id IN ($placeholders)
               AND LOWER(COALESCE(b.bill_status, '')) IN ('approved', 'generated')
               AND LOWER(COALESCE(b.payment_status, '')) IN ('unpaid', 'in_processing', 'awaiting_collection', 'processing', 'pending')
             ORDER BY b.updated_at DESC, b.created_at DESC",
            array_values($tableIds)
        );
    }

    protected function humanizeLabel(string $value, string $fallback = 'Notification'): string
    {
        $value = strtolower(trim(str_replace(array('_', '-'), ' ', $value)));
        if ($value === '') {
            return $fallback;
        }

        return ucwords($value);
    }

    protected function normalizeNotificationType(string $value): string
    {
        $value = strtolower(trim($value));

        if ($value === 'pickup' || $value === 'order_ready' || $value === 'pickup_ready') {
            return 'order_ready';
        }

        if ($value === 'bill' || $value === 'bill_request') {
            return 'bill_request';
        }

        if (in_array($value, array('water', 'waiter', 'assistance', 'cleaning', 'menu', 'cutlery', 'refill'), true)) {
            return 'service_request';
        }

        return 'table_alert';
    }

    protected function notificationTitleForType(string $type, string $requestType = ''): string
    {
        return match ($type) {
            'order_ready'     => 'Order Ready',
            'bill_request'    => 'Bill Request',
            'service_request' => $requestType !== '' ? $this->humanizeLabel($requestType, 'Service Request') : 'Service Request',
            default           => 'Table Alert',
        };
    }

    protected function notificationSectionForType(string $type): string
    {
        return match ($type) {
            'order_ready'  => 'pickups',
            'bill_request' => 'bills',
            'table_alert'  => 'tables',
            default        => 'requests',
        };
    }

    protected function buildNotificationMessageFromRequest(array $request, string $type): string
    {
        $message = trim((string)($request['message'] ?? ''));
        if ($message !== '') {
            return $message;
        }

        $tableCode = trim((string)($request['table_code'] ?? $request['table_number'] ?? ''));
        $orderRef = trim((string)($request['order_number'] ?? $request['order_ref'] ?? ''));
        $tableLabel = $tableCode !== '' ? 'Table ' . $tableCode : 'A table';

        return match ($type) {
            'order_ready'  => $orderRef !== '' ? $orderRef . ' is ready for pickup.' : $tableLabel . ' has an order ready for pickup.',
            'bill_request' => $tableLabel . ' requested the bill.',
            'service_request' => $tableLabel . ' requested service.',
            default        => $tableLabel . ' raised an alert.',
        };
    }

    protected function buildNotificationPayloadFromRequest(array $request, string $type): array
    {
        $requestId = (string)($request['id'] ?? '');
        $tableCode = (string)($request['table_code'] ?? $request['table_number'] ?? '');

        return array(
            'source'             => 'service_requests',
            'request_id'         => $requestId,
            'service_request_id' => $requestId,
            'request_type'       => (string)($request['request_type'] ?? ''),
            'table_id'           => (string)($request['table_id'] ?? ''),
            'table_code'         => $tableCode,
            'table_number'       => (string)($request['table_number'] ?? $tableCode),
            'order_id'           => (string)($request['order_id'] ?? ''),
            'order_ref'          => (string)($request['order_number'] ?? ''),
            'order_number'       => (string)($request['order_number'] ?? ''),
            'bill_id'            => (string)($request['order_bill_id'] ?? $request['current_bill_id'] ?? ''),
            'section'            => $this->notificationSectionForType($type),
            'status'             => (string)($request['status'] ?? 'pending'),
            'created_at'         => (string)($request['requested_at'] ?? $request['created_at'] ?? ''),
        );
    }

    protected function extractDeletedNotificationRequestId(array $row): string
    {
        $oldData = $this->parseJson((string)($row['old_data_json'] ?? ''));
        $requestId = trim((string)($oldData['request_id'] ?? $oldData['service_request_id'] ?? ''));
        if ($requestId !== '') {
            return $requestId;
        }

        $payload = $oldData['payload_json'] ?? array();
        if (!is_array($payload)) {
            $payload = $this->parseJson((string)$payload);
        }

        return trim((string)($payload['request_id'] ?? $payload['service_request_id'] ?? ''));
    }

    protected function getDeletedNotificationRequestIds(string $brandId, string $branchId, string $userId): array
    {
        if (!$this->tableExists('audit_logs')) {
            return array();
        }

        $rows = $this->all(
            "SELECT old_data_json
             FROM audit_logs
             WHERE brand_id = ?
               AND branch_id = ?
               AND user_id = ?
               AND module = 'waiter'
               AND entity_type = 'notification'
               AND action = 'delete'",
            array($brandId, $branchId, $userId)
        );

        $requestIds = array();
        foreach ($rows as $row) {
            $requestId = $this->extractDeletedNotificationRequestId($row);
            if ($requestId !== '') {
                $requestIds[$requestId] = true;
            }
        }

        return $requestIds;
    }

    protected function getOpenServiceRequestNotificationCandidates(array $tableIds): array
    {
        if (empty($tableIds) || !$this->tableExists('service_requests')) {
            return array();
        }

        $placeholders = implode(',', array_fill(0, count($tableIds), '?'));

        return $this->all(
            "SELECT
                sr.id,
                sr.brand_id,
                sr.branch_id,
                sr.table_id,
                sr.order_id,
                sr.assigned_to_user_id,
                sr.request_type,
                sr.message,
                sr.status,
                sr.requested_at,
                sr.acknowledged_at,
                sr.resolved_at,
                sr.created_at,
                t.table_code,
                t.table_number,
                t.current_bill_id,
                o.order_number,
                o.assigned_waiter_id,
                o.bill_id AS order_bill_id
             FROM service_requests sr
             INNER JOIN tables t
                ON t.id = sr.table_id
             LEFT JOIN orders o
                ON o.id = sr.order_id
             WHERE sr.table_id IN ({$placeholders})
               AND LOWER(COALESCE(sr.status, '')) IN ('new', 'approved', 'pending', 'accepted', 'in_progress')
             ORDER BY sr.requested_at DESC, sr.created_at DESC",
            array_values($tableIds)
        );
    }

    protected function shouldMaterializeNotificationForWaiter(array $request, string $userId, string $role): bool
    {
        if (in_array(strtolower($role), array('manager', 'admin'), true)) {
            return true;
        }

        $assignedRequestUser = (string)($request['assigned_to_user_id'] ?? '');
        if ($assignedRequestUser !== '' && $assignedRequestUser !== $userId) {
            return false;
        }

        $requestType = $this->normalizeNotificationType((string)($request['request_type'] ?? ''));
        $assignedOrderWaiter = (string)($request['assigned_waiter_id'] ?? '');

        if (in_array($requestType, array('bill_request', 'order_ready'), true) && $assignedOrderWaiter !== '' && $assignedOrderWaiter !== $userId) {
            return false;
        }

        return true;
    }

    protected function upsertWaiterNotification(
        string $brandId,
        string $branchId,
        string $userId,
        string $type,
        string $title,
        string $message,
        string $requestId,
        array $payload,
        string $createdAt
    ): void {
        if (!$this->tableExists('notifications') || $requestId === '') {
            return;
        }

        $createdAt = trim($createdAt) !== '' ? trim($createdAt) : $this->now();

        $this->execute(
            "INSERT INTO notifications (
                id, brand_id, branch_id, user_id, role_target, type, title,
                message, request_id, payload_json, is_read, created_at
             ) VALUES (?, ?, ?, ?, 'waiter', ?, ?, ?, ?, ?, 0, ?)
             ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                message = VALUES(message),
                payload_json = VALUES(payload_json)",
            array(
                $this->uuid(),
                $brandId,
                $branchId,
                $userId,
                $type,
                $title,
                $message,
                $requestId,
                $this->jsonEncode($payload),
                $createdAt,
            )
        );
    }

    protected function syncWaiterNotifications(string $brandId, string $branchId, string $userId, array $scopeData): void
    {
        if (!$this->tableExists('notifications') || !$this->tableExists('service_requests')) {
            return;
        }

        $tableIds = (array)($scopeData['table_ids'] ?? array());
        if ($tableIds === array()) {
            return;
        }

        $deletedRequestIds = $this->getDeletedNotificationRequestIds($brandId, $branchId, $userId);
        $role = (string)($scopeData['role'] ?? 'waiter');

        foreach ($this->getOpenServiceRequestNotificationCandidates($tableIds) as $request) {
            $requestId = trim((string)($request['id'] ?? ''));
            if ($requestId === '' || isset($deletedRequestIds[$requestId])) {
                continue;
            }

            if (!$this->shouldMaterializeNotificationForWaiter($request, $userId, $role)) {
                continue;
            }

            $type = $this->normalizeNotificationType((string)($request['request_type'] ?? ''));
            $title = $this->notificationTitleForType($type, (string)($request['request_type'] ?? ''));
            $message = $this->buildNotificationMessageFromRequest($request, $type);
            $payload = $this->buildNotificationPayloadFromRequest($request, $type);

            $this->upsertWaiterNotification(
                $brandId,
                $branchId,
                $userId,
                $type,
                $title,
                $message,
                $requestId,
                $payload,
                (string)($request['requested_at'] ?? $request['created_at'] ?? '')
            );
        }
    }

    protected function normalizeNotificationRow(array $row, array $tableIds): ?array
    {
        $payload = $this->parseJson((string)($row['payload_json'] ?? '{}'));
        $tableId = trim((string)($payload['table_id'] ?? ''));

        if (!empty($tableIds) && $tableId !== '' && !in_array($tableId, $tableIds, true)) {
            return null;
        }

        $requestId = trim((string)($row['request_id'] ?? $payload['request_id'] ?? $payload['service_request_id'] ?? ''));
        $type = $this->normalizeNotificationType((string)($row['type'] ?? $payload['request_type'] ?? ''));
        $title = trim((string)($row['title'] ?? ''));
        if ($title === '') {
            $title = $this->notificationTitleForType($type, (string)($payload['request_type'] ?? ''));
        }

        $tableCode = trim((string)($payload['table_code'] ?? $payload['table_number'] ?? ''));
        $orderRef = trim((string)($payload['order_ref'] ?? $payload['order_number'] ?? ''));
        $billId = trim((string)($payload['bill_id'] ?? ''));
        $createdAt = trim((string)($row['created_at'] ?? $payload['created_at'] ?? ''));
        $isRead = (int)($row['is_read'] ?? 0) === 1;

        $message = trim((string)($row['message'] ?? $payload['message'] ?? ''));
        if ($message === '') {
            $message = $this->buildNotificationMessageFromRequest(array(
                'message'      => '',
                'table_code'   => $tableCode,
                'table_number' => $tableCode,
                'request_type' => (string)($payload['request_type'] ?? $type),
                'order_number' => $orderRef,
            ), $type);
        }

        return array(
            'id'                 => (string)($row['id'] ?? ''),
            'type'               => $type,
            'title'              => $title,
            'message'            => $message,
            'table_code'         => $tableCode,
            'table_number'       => (string)($payload['table_number'] ?? $tableCode),
            'table_id'           => $tableId,
            'order_id'           => (string)($payload['order_id'] ?? ''),
            'order_ref'          => $orderRef,
            'bill_id'            => $billId,
            'bill_number'        => (string)($payload['bill_number'] ?? ''),
            'request_id'         => $requestId,
            'service_request_id' => $requestId,
            'created_at'         => $createdAt,
            'time'               => $this->timeAgo($createdAt, 'Now'),
            'status'             => $isRead ? 'read' : 'unread',
            'is_read'            => $isRead ? 1 : 0,
            'section'            => (string)($payload['section'] ?? $this->notificationSectionForType($type)),
        );
    }

    public function getNotificationsForWaiter(
        string $brandId,
        string $branchId,
        string $userId,
        array $tableIds,
        int $limit = 40
    ): array {
        if (!$this->tableExists('notifications')) {
            return array();
        }

        $limit = max(1, min(100, $limit));
        $rows = $this->all(
            "SELECT *
             FROM notifications
             WHERE brand_id = ?
               AND branch_id = ?
               AND role_target = 'waiter'
               AND user_id = ?
             ORDER BY created_at DESC
             LIMIT {$limit}",
            array($brandId, $branchId, $userId)
        );

        $notifications = array();
        foreach ($rows as $row) {
            $normalized = $this->normalizeNotificationRow($row, $tableIds);
            if ($normalized !== null) {
                $notifications[] = $normalized;
            }
        }

        return $notifications;
    }

    public function getNotificationSnapshot(
        string $brandId,
        string $branchId,
        string $userId,
        string $scope = 'assigned'
    ): ?array {
        $scopeData = $this->resolveAccessScope($brandId, $branchId, $userId, $scope);
        if (!$scopeData) {
            return null;
        }

        $this->syncWaiterNotifications($brandId, $branchId, $userId, $scopeData);

        $notifications = $this->getNotificationsForWaiter(
            $brandId,
            $branchId,
            $userId,
            (array)($scopeData['table_ids'] ?? array())
        );

        return array(
            'notifications' => $notifications,
            'summary'       => array(
                'total'  => count($notifications),
                'unread' => count(array_filter($notifications, static function ($row) {
                    return (int)($row['is_read'] ?? 0) !== 1;
                })),
            ),
        );
    }

    protected function getExistingNotificationRow(string $brandId, string $branchId, string $userId, string $notificationId): ?array
    {
        if (!$this->tableExists('notifications')) {
            return null;
        }

        return $this->one(
            "SELECT *
             FROM notifications
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?
               AND role_target = 'waiter'
               AND user_id = ?
             LIMIT 1",
            array($notificationId, $brandId, $branchId, $userId)
        );
    }

    public function markNotificationRead(
        string $brandId,
        string $branchId,
        string $userId,
        string $notificationId,
        string $scope = 'assigned'
    ): bool {
        $existing = $this->getExistingNotificationRow($brandId, $branchId, $userId, $notificationId);
        if (!$existing) {
            return false;
        }

        $this->begin();

        try {
            $this->execute(
                "UPDATE notifications
                 SET is_read = 1
                 WHERE id = ?
                   AND brand_id = ?
                   AND branch_id = ?
                   AND user_id = ?",
                array($notificationId, $brandId, $branchId, $userId)
            );

            $this->auditLog($brandId, $branchId, $userId, 'notification', $notificationId, 'mark_read', $existing, array(
                'is_read' => 1,
            ));

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestWaiterModel::markNotificationRead failed: ' . $e->getMessage());
            return false;
        }
    }

    public function markAllNotificationsRead(
        string $brandId,
        string $branchId,
        string $userId,
        string $scope = 'assigned'
    ): bool {
        if (!$this->tableExists('notifications')) {
            return false;
        }

        $existingRows = $this->all(
            "SELECT id
             FROM notifications
             WHERE brand_id = ?
               AND branch_id = ?
               AND role_target = 'waiter'
               AND user_id = ?
               AND COALESCE(is_read, 0) <> 1",
            array($brandId, $branchId, $userId)
        );

        if ($existingRows === array()) {
            return true;
        }

        $this->begin();

        try {
            $this->execute(
                "UPDATE notifications
                 SET is_read = 1
                 WHERE brand_id = ?
                   AND branch_id = ?
                   AND role_target = 'waiter'
                   AND user_id = ?",
                array($brandId, $branchId, $userId)
            );

            $this->auditLog($brandId, $branchId, $userId, 'notification', null, 'mark_all_read', null, array(
                'notification_ids' => array_values(array_map(static function ($row) {
                    return (string)($row['id'] ?? '');
                }, $existingRows)),
            ));

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestWaiterModel::markAllNotificationsRead failed: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteNotification(
        string $brandId,
        string $branchId,
        string $userId,
        string $notificationId,
        string $scope = 'assigned'
    ): bool {
        $existing = $this->getExistingNotificationRow($brandId, $branchId, $userId, $notificationId);
        if (!$existing) {
            return false;
        }

        $this->begin();

        try {
            $this->execute(
                "DELETE FROM notifications
                 WHERE id = ?
                   AND brand_id = ?
                   AND branch_id = ?
                   AND user_id = ?",
                array($notificationId, $brandId, $branchId, $userId)
            );

            $this->auditLog($brandId, $branchId, $userId, 'notification', $notificationId, 'delete', $existing, null);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestWaiterModel::deleteNotification failed: ' . $e->getMessage());
            return false;
        }
    }

    public function clearNotifications(
        string $brandId,
        string $branchId,
        string $userId,
        string $scope = 'assigned'
    ): bool {
        if (!$this->tableExists('notifications')) {
            return false;
        }

        $existingRows = $this->all(
            "SELECT *
             FROM notifications
             WHERE brand_id = ?
               AND branch_id = ?
               AND role_target = 'waiter'
               AND user_id = ?",
            array($brandId, $branchId, $userId)
        );

        if ($existingRows === array()) {
            return true;
        }

        $this->begin();

        try {
            $this->execute(
                "DELETE FROM notifications
                 WHERE brand_id = ?
                   AND branch_id = ?
                   AND role_target = 'waiter'
                   AND user_id = ?",
                array($brandId, $branchId, $userId)
            );

            foreach ($existingRows as $row) {
                $this->auditLog(
                    $brandId,
                    $branchId,
                    $userId,
                    'notification',
                    (string)($row['id'] ?? ''),
                    'delete',
                    $row,
                    null
                );
            }

            $this->auditLog($brandId, $branchId, $userId, 'notification', null, 'clear_all', null, array(
                'cleared' => true,
                'count'   => count($existingRows),
            ));

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestWaiterModel::clearNotifications failed: ' . $e->getMessage());
            return false;
        }
    }


    

    protected function defaultUserSettings(): array
    {
        return array(
            'id'                      => '',
            'user_id'                 => '',
            'role'                    => 'waiter',
            'theme'                   => 'light',
            'lite_notification_sound' => null,
            'sound_alerts_enabled'    => 1,
            'created_at'              => '',
            'updated_at'              => '',
        );
    }

    protected function normalizeUserSettingsRow(?array $row, string $userId): array
    {
        $settings = array_merge($this->defaultUserSettings(), is_array($row) ? $row : array());
        $settings['user_id'] = $userId;
        $settings['theme'] = in_array((string)($settings['theme'] ?? ''), array('light', 'dark'), true)
            ? (string)$settings['theme']
            : 'light';
        $settings['sound_alerts_enabled'] = (int)((int)($settings['sound_alerts_enabled'] ?? 1) === 1 ? 1 : 0);

        $sound = trim((string)($settings['lite_notification_sound'] ?? ''));
        $settings['lite_notification_sound'] = $sound !== '' ? $sound : null;

        return $settings;
    }

    protected function sanitizeTheme(string $theme): string
    {
        $theme = strtolower(trim($theme));
        if (!in_array($theme, array('light', 'dark'), true)) {
            throw new RuntimeException('theme must be light or dark.');
        }

        return $theme;
    }

    protected function sanitizeSoundAlertsEnabled($value): int
    {
        if ($value === 1 || $value === '1' || $value === true || $value === 'true') {
            return 1;
        }

        if ($value === 0 || $value === '0' || $value === false || $value === 'false') {
            return 0;
        }

        throw new RuntimeException('sound_alerts_enabled must be 0 or 1.');
    }

    protected function sanitizeNotificationSound(?string $value): ?string
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        if (strlen($value) > 255) {
            throw new RuntimeException('Notification sound path is too long.');
        }

        $lower = strtolower($value);
        if (str_contains($lower, 'javascript:') || str_contains($value, '..') || str_contains($value, "\0")) {
            throw new RuntimeException('Notification sound path is invalid.');
        }

        return $value;
    }

    protected function ensureUserSettingsRow(string $userId): array
    {
        if (!$this->tableExists('user_settings')) {
            return $this->normalizeUserSettingsRow(null, $userId);
        }

        $existing = $this->one(
            "SELECT *
             FROM user_settings
             WHERE user_id = ?
             LIMIT 1",
            array($userId)
        );

        if ($existing) {
            return $this->normalizeUserSettingsRow($existing, $userId);
        }

        $rowId = $this->uuid();
        $now = $this->now();

        try {
            $this->execute(
                "INSERT INTO user_settings (
                    id, user_id, role, theme, lite_notification_sound,
                    sound_alerts_enabled, created_at, updated_at
                 ) VALUES (?, ?, 'waiter', 'light', NULL, 1, ?, ?)",
                array($rowId, $userId, $now, $now)
            );
        } catch (Throwable $e) {
            $existing = $this->one(
                "SELECT *
                 FROM user_settings
                 WHERE user_id = ?
                 LIMIT 1",
                array($userId)
            );

            if ($existing) {
                return $this->normalizeUserSettingsRow($existing, $userId);
            }

            throw $e;
        }

        return $this->normalizeUserSettingsRow(array(
            'id'                      => $rowId,
            'user_id'                 => $userId,
            'role'                    => 'waiter',
            'theme'                   => 'light',
            'lite_notification_sound' => null,
            'sound_alerts_enabled'    => 1,
            'created_at'              => $now,
            'updated_at'              => $now,
        ), $userId);
    }

    public function getUserSettings(string $brandId, string $branchId, string $userId): array
    {
        try {
            return $this->ensureUserSettingsRow($userId);
        } catch (Throwable $e) {
            error_log('RestWaiterModel::getUserSettings failed: ' . $e->getMessage());
            return $this->normalizeUserSettingsRow(null, $userId);
        }
    }

    public function updateUserSettings(string $brandId, string $branchId, string $userId, array $payload): array
    {
        $existing = $this->ensureUserSettingsRow($userId);

        $updates = array();
        $params = array();

        if (array_key_exists('theme', $payload)) {
            $updates[] = 'theme = ?';
            $params[] = $this->sanitizeTheme((string)$payload['theme']);
        }

        if (array_key_exists('sound_alerts_enabled', $payload)) {
            $updates[] = 'sound_alerts_enabled = ?';
            $params[] = $this->sanitizeSoundAlertsEnabled($payload['sound_alerts_enabled']);
        }

        if (array_key_exists('lite_notification_sound', $payload)) {
            $updates[] = 'lite_notification_sound = ?';
            $params[] = $this->sanitizeNotificationSound($payload['lite_notification_sound']);
        }

        if ($updates === array()) {
            return $existing;
        }

        $updates[] = 'role = ?';
        $params[] = 'waiter';
        $updates[] = 'updated_at = ?';
        $params[] = $this->now();
        $params[] = $userId;

        $this->begin();

        try {
            $this->execute(
                "UPDATE user_settings
                 SET " . implode(', ', $updates) . "
                 WHERE user_id = ?",
                $params
            );

            $updated = $this->ensureUserSettingsRow($userId);

            $this->auditLog(
                $brandId,
                $branchId,
                $userId,
                'user_settings',
                (string)($updated['id'] ?? ''),
                'update',
                $existing,
                $updated
            );

            $this->commit();
            return $updated;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestWaiterModel::updateUserSettings failed: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function responseRangeBounds(string $range): array
    {
        $range = strtolower(trim($range));
        $now = time();

        if ($range === 'week') {
            $start = strtotime('monday this week', $now);
            if ($start === false) {
                $start = strtotime(date('Y-m-d 00:00:00', $now));
            }
            $end = strtotime('+7 days', $start);

            return array(date('Y-m-d H:i:s', $start), date('Y-m-d H:i:s', $end));
        }

        if ($range === 'month') {
            $start = strtotime(date('Y-m-01 00:00:00', $now));
            $end = strtotime('+1 month', $start);

            return array(date('Y-m-d H:i:s', $start), date('Y-m-d H:i:s', $end));
        }

        $start = strtotime(date('Y-m-d 00:00:00', $now));
        $end = strtotime('+1 day', $start);

        return array(date('Y-m-d H:i:s', $start), date('Y-m-d H:i:s', $end));
    }

    protected function formatAverageResponseLabel(?int $seconds): string
    {
        if ($seconds === null || $seconds < 0) {
            return '—';
        }

        if ($seconds < 60) {
            return $seconds . ' sec';
        }

        $minutes = intdiv($seconds, 60);
        $remainingSeconds = $seconds % 60;

        if ($minutes < 60) {
            return $remainingSeconds > 0
                ? $minutes . ' min ' . $remainingSeconds . ' sec'
                : $minutes . ' min';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return $remainingMinutes > 0
            ? $hours . ' hr ' . $remainingMinutes . ' min'
            : $hours . ' hr';
    }

    protected function emptyMetricRange(string $range): array
    {
        return array(
            'range'           => $range,
            'average_seconds' => null,
            'average_label'   => '—',
            'sample_count'    => 0,
        );
    }

    protected function calculateAverageResponseForRange(
        string $brandId,
        string $branchId,
        string $userId,
        array $tableIds,
        string $range
    ): array {
        if (!$this->tableExists('service_requests') || $tableIds === array()) {
            return $this->emptyMetricRange($range);
        }

        $placeholders = implode(',', array_fill(0, count($tableIds), '?'));
        list($startAt, $endAt) = $this->responseRangeBounds($range);
        $maxDateSentinel = "'9999-12-31 23:59:59'";
        $standardActionExpr = "NULLIF(LEAST(
                COALESCE(sr.acknowledged_at, {$maxDateSentinel}),
                COALESCE(sr.resolved_at, {$maxDateSentinel})
            ), {$maxDateSentinel})";

        $standardParams = array_merge(
            array($brandId, $branchId),
            array_values($tableIds),
            array($userId, $startAt, $endAt)
        );

        $standardQuery = "SELECT
                TIMESTAMPDIFF(SECOND, sr.requested_at, {$standardActionExpr}) AS response_seconds
             FROM service_requests sr
             WHERE sr.brand_id = ?
               AND sr.branch_id = ?
               AND sr.table_id IN ({$placeholders})
               AND LOWER(COALESCE(sr.request_type, '')) <> 'pickup'
               AND sr.assigned_to_user_id = ?
               AND sr.requested_at >= ?
               AND sr.requested_at < ?
               AND {$standardActionExpr} IS NOT NULL";

        $pickupActionPieces = array(
            'sr.acknowledged_at',
            'sr.resolved_at',
        );
        $pickupItemServedExpr = null;

        if ($this->tableExists('order_items')) {
            $pickupItemServedExpr = "(SELECT MIN(oi.served_at)
                FROM order_items oi
                WHERE oi.order_id = sr.order_id
                  AND oi.served_at IS NOT NULL
                  AND oi.served_at >= sr.requested_at)";
            $pickupActionPieces[] = $pickupItemServedExpr;
        }

        if ($this->tableExists('orders')) {
            $pickupOrderFallbackExpr = "(SELECT MAX(o2.updated_at)
                FROM orders o2
                WHERE o2.id = sr.order_id
                  AND LOWER(COALESCE(o2.status, '')) = 'served'
                  AND o2.updated_at IS NOT NULL
                  AND o2.updated_at >= sr.requested_at";

            if ($pickupItemServedExpr !== null) {
                $pickupOrderFallbackExpr .= "
                  AND NOT EXISTS (
                      SELECT 1
                      FROM order_items oi2
                      WHERE oi2.order_id = sr.order_id
                        AND oi2.served_at IS NOT NULL
                        AND oi2.served_at >= sr.requested_at
                  )";
            }

            $pickupOrderFallbackExpr .= ")";
            $pickupActionPieces[] = $pickupOrderFallbackExpr;
        }

        $pickupActionExpr = 'NULLIF(LEAST(' . implode(', ', array_map(static function ($expr) use ($maxDateSentinel) {
            return 'COALESCE(' . $expr . ', ' . $maxDateSentinel . ')';
        }, $pickupActionPieces)) . '), ' . $maxDateSentinel . ')';
        $pickupJoin = $this->tableExists('orders')
            ? "LEFT JOIN orders o ON o.id = sr.order_id"
            : '';
        $pickupAssignmentFilter = $this->tableExists('orders')
            ? "(sr.assigned_to_user_id = ? OR o.assigned_waiter_id = ?)"
            : "sr.assigned_to_user_id = ?";
        $pickupParams = $this->tableExists('orders')
            ? array($startAt, $endAt, $userId, $userId)
            : array($startAt, $endAt, $userId);

        $pickupQuery = "SELECT
                TIMESTAMPDIFF(SECOND, sr.requested_at, {$pickupActionExpr}) AS response_seconds
             FROM service_requests sr
             {$pickupJoin}
             WHERE sr.brand_id = ?
               AND sr.branch_id = ?
               AND sr.table_id IN ({$placeholders})
               AND LOWER(COALESCE(sr.request_type, '')) = 'pickup'
               AND sr.requested_at >= ?
               AND sr.requested_at < ?
               AND {$pickupAssignmentFilter}
               AND {$pickupActionExpr} IS NOT NULL";

        $pickupParams = array_merge(
            array($brandId, $branchId),
            array_values($tableIds),
            $pickupParams
        );

        $metricRow = $this->one(
            "SELECT
                AVG(metric.response_seconds) AS average_seconds,
                COUNT(*) AS sample_count
             FROM (
                {$standardQuery}
                UNION ALL
                {$pickupQuery}
             ) metric
             WHERE metric.response_seconds >= 0",
            array_merge($standardParams, $pickupParams)
        );

        $sampleCount = (int)($metricRow['sample_count'] ?? 0);
        if ($sampleCount < 1) {
            return $this->emptyMetricRange($range);
        }

        $averageSeconds = (int)round((float)($metricRow['average_seconds'] ?? 0));

        return array(
            'range'           => $range,
            'average_seconds' => $averageSeconds,
            'average_label'   => $this->formatAverageResponseLabel($averageSeconds),
            'sample_count'    => $sampleCount,
        );
    }

    public function getWaiterMetrics(
        string $brandId,
        string $branchId,
        string $userId,
        string $scope = 'assigned'
    ): ?array {
        $scopeData = $this->resolveAccessScope($brandId, $branchId, $userId, $scope);
        if (!$scopeData) {
            return null;
        }

        $tableIds = (array)($scopeData['table_ids'] ?? array());

        return array(
            'today' => $this->calculateAverageResponseForRange($brandId, $branchId, $userId, $tableIds, 'today'),
            'week'  => $this->calculateAverageResponseForRange($brandId, $branchId, $userId, $tableIds, 'week'),
            'month' => $this->calculateAverageResponseForRange($brandId, $branchId, $userId, $tableIds, 'month'),
        );
    }

    public function getWaiterEvents(
        string $brandId,
        string $branchId,
        string $userId,
        int $afterId = 0,
        int $limit = 20,
        string $scope = 'assigned',
        bool $bootstrap = false
    ): ?array {
        return array(
            'events'        => array(),
            'last_event_id' => 0,
        );
    }

    public function getTableModalData(
        string $brandId,
        string $branchId,
        string $tableId,
        string $userId,
        string $scope = 'assigned'
    ): ?array
    {
        if (!$this->tableExists('tables')) {
            return null;
        }

        $this->syncPaidTableAvailability($brandId, $branchId, [$tableId]);

        $scopeData = $this->resolveAccessScope($brandId, $branchId, $userId, $scope);
        if (!$scopeData) {
            return null;
        }

        $table = $this->one(
            "SELECT
                t.*,
                b.name AS branch_name
             FROM tables t
             LEFT JOIN branches b
                ON b.id = t.branch_id
             WHERE t.id = ?
               AND t.brand_id = ?
               AND t.branch_id = ?
             LIMIT 1",
            array($tableId, $brandId, $branchId)
        );

        if (!$table || !$this->canAccessTable($scopeData, (string)($table['id'] ?? ''))) {
            return null;
        }

        $order = null;
        if (!empty($table['current_order_id'])) {
            $order = $this->getOrderDetail((string)$table['current_order_id']);
        }

        if ($order === null) {
            $order = $this->getLatestActiveOrderForTable((string)$table['id']);
        }

        $bill = null;
        if (!empty($table['current_bill_id'])) {
            $bill = $this->getBillDetail((string)$table['current_bill_id']);
        } elseif (!empty($order['id'])) {
            $bill = $this->getBillByOrderId((string)$order['id']);
        }

        $requests = $this->getRequestsForTable((string)$table['id']);
        $orderItems = !empty($order['id']) ? $this->getOrderItemsDetail((string)$order['id']) : array();

        return array(
            'table'          => $table,
            'order'          => $order,
            'bill'           => $bill,
            'requests'       => $requests,
            'order_items'    => $orderItems,
            'status_options' => $this->waiterTableStatusOptions(),
            'permissions'    => $this->normalizePermissions((array)($scopeData['permissions'] ?? [])),
        );
    }

    public function updateTableStatus(
        string $brandId,
        string $branchId,
        string $userId,
        string $tableId,
        string $status,
        string $scope = 'assigned'
    ): bool {
        $scopeData = $this->resolveAccessScope($brandId, $branchId, $userId, $scope);
        if (!$scopeData || !$this->canAccessTable($scopeData, $tableId)) {
            return false;
        }

        $tableName = $this->tableExists('tables')
            ? 'tables'
            : ($this->tableExists('restaurant_tables') ? 'restaurant_tables' : '');
        if ($tableName === '') {
            return false;
        }

        $existing = $this->one(
            "SELECT *
             FROM {$tableName}
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?
             LIMIT 1",
            [$tableId, $brandId, $branchId]
        );

        if (!is_array($existing)) {
            return false;
        }

        $normalizedStatus = $this->normalizeTableStatus($status);
        $now = $this->now();
        $updates = [];
        $params = [];

        if ($this->columnExists($tableName, 'status')) {
            $updates[] = 'status = ?';
            $params[] = $normalizedStatus;
        }
        if ($this->columnExists($tableName, 'manual_status_override')) {
            $updates[] = 'manual_status_override = ?';
            $params[] = 1;
        }
        if ($this->columnExists($tableName, 'auto_available_at')) {
            $updates[] = 'auto_available_at = NULL';
        }
        if ($this->columnExists($tableName, 'status_updated_at')) {
            $updates[] = 'status_updated_at = ?';
            $params[] = $now;
        }
        if ($this->columnExists($tableName, 'updated_at')) {
            $updates[] = 'updated_at = ?';
            $params[] = $now;
        }

        if ($updates === []) {
            return false;
        }

        $params[] = $tableId;

        $this->begin();

        try {
            $ok = $this->execute(
                "UPDATE {$tableName}
                 SET " . implode(', ', $updates) . "
                 WHERE id = ?",
                $params
            );

            if (!$ok) {
                throw new RuntimeException('Unable to update table status.');
            }

            if ($this->tableExists('bills')) {
                $billUpdates = [];
                $billParams = [];

                if ($this->columnExists('bills', 'auto_release_at')) {
                    $billUpdates[] = 'auto_release_at = NULL';
                }
                if ($this->columnExists('bills', 'updated_at')) {
                    $billUpdates[] = 'updated_at = ?';
                    $billParams[] = $now;
                }

                if ($billUpdates !== []) {
                    $billParams[] = $brandId;
                    $billParams[] = $branchId;
                    $billParams[] = $tableId;

                    $this->execute(
                        "UPDATE bills
                         SET " . implode(', ', $billUpdates) . "
                         WHERE brand_id = ?
                           AND branch_id = ?
                           AND table_id = ?
                           AND LOWER(COALESCE(payment_status, '')) = 'paid'",
                        $billParams
                    );
                }
            }

            $this->auditLog(
                $brandId,
                $branchId,
                $userId,
                'table',
                $tableId,
                'status_update',
                $existing,
                [
                    'status' => $normalizedStatus,
                    'manual_status_override' => 1,
                ]
            );

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestWaiterModel::updateTableStatus failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getLatestActiveOrderForTable(string $tableId): ?array
    {
        if (!$this->tableExists('orders')) {
            return null;
        }

        return $this->one(
            "SELECT *
             FROM orders
             WHERE table_id = ?
               AND is_active = 1
               AND LOWER(COALESCE(status, '')) NOT IN ('closed', 'paid', 'completed', 'cancelled')
             ORDER BY created_at DESC
             LIMIT 1",
            array($tableId)
        );
    }

    public function getOrderDetail(string $orderId): ?array
    {
        if (!$this->tableExists('orders')) {
            return null;
        }

        return $this->one(
            "SELECT * FROM orders WHERE id = ? LIMIT 1",
            array($orderId)
        );
    }

    public function getOrderItemsDetail(string $orderId): array
    {
        if (!$this->tableExists('order_items')) {
            return array();
        }

        $joinKitchen = $this->tableExists('kitchen_stations')
            ? "LEFT JOIN kitchen_stations ks ON ks.id = oi.kitchen_station_id"
            : "";

        $selectKitchen = $this->tableExists('kitchen_stations')
            ? ", ks.name AS kitchen_station_name"
            : ", NULL AS kitchen_station_name";

        return $this->all(
            "SELECT
                oi.*
                {$selectKitchen},
                mi.preparation_time_mins,
                mi.tax_rate
             FROM order_items oi
             LEFT JOIN menu_items mi
                ON mi.id = oi.menu_item_id
             {$joinKitchen}
             WHERE oi.order_id = ?
             ORDER BY oi.created_at ASC",
            array($orderId)
        );
    }

    public function getBillDetail(string $billId): ?array
    {
        if (!$this->tableExists('bills')) {
            return null;
        }

        return $this->one(
            "SELECT * FROM bills WHERE id = ? LIMIT 1",
            array($billId)
        );
    }

    public function getBillByOrderId(string $orderId): ?array
    {
        if (!$this->tableExists('bills')) {
            return null;
        }

        return $this->one(
            "SELECT *
             FROM bills
             WHERE order_id = ?
             ORDER BY created_at DESC
             LIMIT 1",
            array($orderId)
        );
    }

    public function getRequestsForTable(string $tableId): array
    {
        if (!$this->tableExists('service_requests')) {
            return array();
        }

        $rows = $this->all(
            "SELECT *
             FROM service_requests
             WHERE table_id = ?
             ORDER BY requested_at DESC, created_at DESC
             LIMIT 20",
            array($tableId)
        );

        foreach ($rows as &$row) {
            $row['time'] = $this->timeAgo((string)($row['requested_at'] ?? $row['created_at'] ?? ''), 'Now');
        }
        unset($row);

        return $rows;
    }

    public function acknowledgeServiceRequest(
        string $brandId,
        string $branchId,
        string $requestId,
        string $waiterUserId,
        string $scope = 'assigned'
    ): bool
    {
        $scopeData = $this->resolveAccessScope($brandId, $branchId, $waiterUserId, $scope);
        if (!$scopeData) {
            return false;
        }

        $now = $this->now();
        $request = $this->getScopedServiceRequestRecord($brandId, $branchId, $requestId, $scopeData);
        if (!$request) {
            return false;
        }

        $ok = $this->execute(
            "UPDATE service_requests
             SET assigned_to_user_id = ?,
                 status = 'approved',
                 acknowledged_at = ?,
                 resolved_at = NULL
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?
               AND LOWER(COALESCE(status, '')) IN ('new', 'approved', 'pending', 'accepted', 'in_progress')",
            array($waiterUserId, $now, $requestId, $brandId, $branchId)
        );

        if ($ok && $request) {
            $this->pushRealtimeEvent(
                (string)$request['brand_id'],
                (string)$request['branch_id'],
                'service_request.approved',
                'service_request',
                (string)$request['id'],
                array(
                    'table_id'       => (string)$request['table_id'],
                    'order_id'       => (string)($request['order_id'] ?? ''),
                    'request_type'   => (string)($request['request_type'] ?? ''),
                    'waiter_user_id' => $waiterUserId,
                )
            );
        }

        return $ok;
    }

    public function resolveServiceRequest(
        string $brandId,
        string $branchId,
        string $requestId,
        string $waiterUserId,
        string $scope = 'assigned'
    ): bool
    {
        $scopeData = $this->resolveAccessScope($brandId, $branchId, $waiterUserId, $scope);
        if (!$scopeData) {
            return false;
        }

        $now = $this->now();
        $request = $this->getScopedServiceRequestRecord($brandId, $branchId, $requestId, $scopeData);
        if (!$request) {
            return false;
        }

        $ok = $this->execute(
            "UPDATE service_requests
             SET assigned_to_user_id = ?,
                 status = 'closed',
                 acknowledged_at = COALESCE(acknowledged_at, ?),
                 resolved_at = ?
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?
               AND LOWER(COALESCE(status, '')) IN ('new', 'approved', 'pending', 'accepted', 'in_progress')",
            array($waiterUserId, $now, $now, $requestId, $brandId, $branchId)
        );

        if ($ok && $request) {
            $this->pushRealtimeEvent(
                (string)$request['brand_id'],
                (string)$request['branch_id'],
                'service_request.closed',
                'service_request',
                (string)$request['id'],
                array(
                    'table_id'       => (string)$request['table_id'],
                    'order_id'       => (string)($request['order_id'] ?? ''),
                    'request_type'   => (string)($request['request_type'] ?? ''),
                    'waiter_user_id' => $waiterUserId,
                )
            );
        }

        return $ok;
    }

    public function markOrderServed(
        string $brandId,
        string $branchId,
        string $orderId,
        string $waiterUserId,
        string $scope = 'assigned'
    ): bool
    {
        $scopeData = $this->resolveAccessScope($brandId, $branchId, $waiterUserId, $scope);
        if (!$scopeData) {
            return false;
        }

        $this->begin();

        try {
            $order = $this->getScopedOrderRecord($brandId, $branchId, $orderId, $scopeData);
            if (!$order) {
                throw new RuntimeException('Order not found.');
            }

            $now = $this->now();

            $this->execute(
                "UPDATE order_items
                 SET status = 'served',
                     served_at = ?,
                     status_changed_at = ?,
                     updated_at = ?
                 WHERE order_id = ?
                   AND LOWER(COALESCE(status, '')) IN ('new', 'ready', 'preparing', 'placed', 'accepted', 'cooking')",
                array($now, $now, $now, $orderId)
            );

            $this->execute(
                "UPDATE orders
                 SET status = 'served',
                     assigned_waiter_id = COALESCE(assigned_waiter_id, ?),
                     updated_at = ?
                 WHERE id = ?",
                array($waiterUserId, $now, $orderId)
            );

            $this->execute(
                "UPDATE tables
                 SET status = 'occupied',
                     status_updated_at = ?,
                     updated_at = ?
                 WHERE id = ?",
                array($now, $now, (string)$order['table_id'])
            );

            $this->execute(
                "UPDATE service_requests
                 SET assigned_to_user_id = ?,
                     status = 'closed',
                     acknowledged_at = COALESCE(acknowledged_at, ?),
                     resolved_at = ?
                 WHERE brand_id = ?
                   AND branch_id = ?
                   AND order_id = ?
                   AND request_type = 'pickup'
                   AND LOWER(COALESCE(status, '')) IN ('new', 'approved', 'pending', 'accepted', 'in_progress')",
                array($waiterUserId, $now, $now, $brandId, $branchId, $orderId)
            );

            $this->pushRealtimeEvent(
                (string)$order['brand_id'],
                (string)$order['branch_id'],
                'order.served',
                'order',
                (string)$order['id'],
                array(
                    'table_id'       => (string)$order['table_id'],
                    'waiter_user_id' => $waiterUserId,
                )
            );

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestWaiterModel::markOrderServed failed: ' . $e->getMessage());
            return false;
        }
    }

    protected function nextBillNumber(string $branchId): string
    {
        $prefix = 'BILL-' . date('Ymd') . '-';
        $total = (int)$this->scalar(
            "SELECT COUNT(*)
             FROM bills
             WHERE branch_id = ?
               AND DATE(created_at) = CURDATE()",
            array($branchId),
            0
        );

        return $prefix . str_pad((string)($total + 1), 4, '0', STR_PAD_LEFT);
    }

    public function createBillFromOrder(string $brandId, string $branchId, string $tableId, string $orderId): ?string
    {
        $order = $this->getOrderDetail($orderId);
        if (!$order || !$this->tableExists('bills')) {
            return null;
        }

        $billId = $this->uuid();
        $now = $this->now();

        $subtotal = (float)($order['subtotal'] ?? 0);
        $taxAmount = (float)($order['tax_amount'] ?? 0);
        $serviceCharge = (float)($order['service_charge'] ?? 0);
        $discountAmount = (float)($order['discount_amount'] ?? 0);
        $roundoffAmount = 0.0;
        $totalAmount = (float)($order['grand_total'] ?? 0);

        $ok = $this->execute(
            "INSERT INTO bills (
                id, brand_id, branch_id, order_id, table_id, bill_number,
                bill_status, payment_status, subtotal, tax_amount,
                service_charge, discount_amount, roundoff_amount, total_amount,
                generated_at, created_at, updated_at
             ) VALUES (?, ?, ?, ?, ?, ?, 'new', 'unpaid', ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            array(
                $billId,
                $brandId,
                $branchId,
                $orderId,
                $tableId,
                $this->nextBillNumber($branchId),
                $subtotal,
                $taxAmount,
                $serviceCharge,
                $discountAmount,
                $roundoffAmount,
                $totalAmount,
                $now,
                $now,
                $now
            )
        );

        if (!$ok) {
            return null;
        }

        if ($this->tableExists('bill_items')) {
            $items = $this->getOrderItemsDetail($orderId);

            foreach ($items as $item) {
                $billItemId = $this->uuid();
                $itemName = (string)($item['item_name_snapshot'] ?? 'Menu Item');
                $qty = (int)($item['quantity'] ?? 1);
                $unitPrice = (float)($item['price_snapshot'] ?? 0);
                $lineTotal = (float)($item['line_total'] ?? ($qty * $unitPrice));
                $taxRate = (float)($item['tax_rate'] ?? 0);
                $taxItemAmount = round(($lineTotal * $taxRate) / 100, 2);

                $this->execute(
                    "INSERT INTO bill_items (
                        id, bill_id, order_item_id, item_name, quantity,
                        unit_price, tax_rate, tax_amount, total_amount, created_at
                     ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    array(
                        $billItemId,
                        $billId,
                        (string)$item['id'],
                        $itemName,
                        $qty,
                        $unitPrice,
                        $taxRate,
                        $taxItemAmount,
                        $lineTotal,
                        $now
                    )
                );

                $this->execute(
                    "UPDATE order_items
                     SET bill_id = ?, is_billed = 1, updated_at = ?
                     WHERE id = ?",
                    array($billId, $now, (string)$item['id'])
                );
            }
        }

        return $billId;
    }

    public function markOrderBillRequested(string $orderId, string $billId): bool
    {
        $now = $this->now();

        return $this->execute(
            "UPDATE orders
             SET bill_id = ?,
                 bill_requested_at = ?,
                 updated_at = ?
             WHERE id = ?",
            array($billId, $now, $now, $orderId)
        );
    }

    public function assignBillToTable(string $tableId, string $billId): bool
    {
        $now = $this->now();

        return $this->execute(
            "UPDATE tables
             SET current_bill_id = ?,
                 status = 'occupied',
                 status_updated_at = ?,
                 updated_at = ?
             WHERE id = ?",
            array($billId, $now, $now, $tableId)
        );
    }

    public function generateBillForOrder(
        string $brandId,
        string $branchId,
        string $userId,
        string $orderId,
        string $scope = 'assigned'
    ): ?array
    {
        $scopeData = $this->resolveAccessScope($brandId, $branchId, $userId, $scope);
        if (!$scopeData) {
            return null;
        }

        $order = $this->getScopedOrderRecord($brandId, $branchId, $orderId, $scopeData);
        if (!$order || empty($order['table_id'])) {
            return null;
        }

        $existingBill = $this->getBillByOrderId($orderId);
        if ($existingBill) {
            return $existingBill;
        }

        return $this->generateBillForTable(
            $brandId,
            $branchId,
            $userId,
            (string)$order['table_id'],
            $scope
        );
    }

    public function generateBillForTable(
        string $brandId,
        string $branchId,
        string $userId,
        string $tableId,
        string $scope = 'assigned'
    ): ?array
    {
        $scopeData = $this->resolveAccessScope($brandId, $branchId, $userId, $scope);
        if (!$scopeData || !$this->canAccessTable($scopeData, $tableId)) {
            return null;
        }

        $order = $this->getLatestActiveOrderForTable($tableId);
        if (!$order || empty($order['id'])) {
            return null;
        }

        $orderId = (string)$order['id'];
        $existingBill = $this->getBillByOrderId($orderId);
        if ($existingBill) {
            return $existingBill;
        }

        $this->begin();

        try {
            $now = $this->now();

            $billId = $this->createBillFromOrder($brandId, $branchId, $tableId, $orderId);
            if ($billId === null) {
                throw new RuntimeException('Unable to create bill.');
            }

            if (!$this->markOrderBillRequested($orderId, $billId)) {
                throw new RuntimeException('Unable to update order bill reference.');
            }

            if (!$this->assignBillToTable($tableId, $billId)) {
                throw new RuntimeException('Unable to assign bill to table.');
            }

            $this->execute(
                "UPDATE service_requests
                 SET assigned_to_user_id = ?,
                     status = 'closed',
                     acknowledged_at = COALESCE(acknowledged_at, ?),
                     resolved_at = ?
                 WHERE brand_id = ?
                   AND branch_id = ?
                   AND table_id = ?
                   AND request_type IN ('bill', 'bill_request')
                   AND LOWER(COALESCE(status, '')) IN ('new', 'approved', 'pending', 'accepted', 'in_progress')",
                [$userId, $now, $now, $brandId, $branchId, $tableId]
            );

            $this->pushRealtimeEvent(
                $brandId,
                $branchId,
                'bill.generated',
                'bill',
                $billId,
                array(
                    'table_id'       => $tableId,
                    'order_id'       => $orderId,
                    'generated_by'   => $userId,
                    'total_amount'   => (float)($order['grand_total'] ?? 0),
                )
            );

            $this->commit();

            return $this->getBillDetail($billId);
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestWaiterModel::generateBillForTable failed: ' . $e->getMessage());
            return null;
        }
    }

    protected function paymentStatusColumn(): string
    {
        if ($this->tableExists('payments') && $this->columnExists('payments', 'status')) return 'status';
        return 'payment_status';
    }

    protected function paymentAmountColumn(): string
    {
        if ($this->tableExists('payments') && $this->columnExists('payments', 'amount')) return 'amount';
        return 'amount_paid';
    }

    protected function paymentMethodColumn(): string
    {
        if ($this->tableExists('payments') && $this->columnExists('payments', 'method')) return 'method';
        return 'payment_method';
    }

    protected function createOrMarkPaymentPaid(
        string $brandId,
        string $branchId,
        string $billId,
        string $orderId,
        string $tableId,
        string $userId,
        float $amount,
        string $now
    ): string {
        if (!$this->tableExists('payments')) return '';

        $statusColumn = $this->paymentStatusColumn();
        $amountColumn = $this->paymentAmountColumn();
        $methodColumn = $this->paymentMethodColumn();

        $existing = null;
        if ($this->columnExists('payments', $statusColumn)) {
            $existing = $this->one(
                "SELECT * FROM payments WHERE bill_id = ? AND LOWER(COALESCE({$statusColumn}, '')) IN ('unpaid', 'in_processing', 'awaiting_collection', 'processing', 'pending') ORDER BY created_at DESC LIMIT 1",
                array($billId)
            );
        }

        if ($existing && !empty($existing['id'])) {
            $updates = array();
            $params = array();
            if ($this->columnExists('payments', $statusColumn)) { $updates[] = $statusColumn . ' = ?'; $params[] = 'paid'; }
            if ($this->columnExists('payments', $amountColumn)) { $updates[] = $amountColumn . ' = ?'; $params[] = $amount; }
            if ($this->columnExists('payments', $methodColumn)) { $updates[] = $methodColumn . ' = ?'; $params[] = 'cash'; }
            if ($this->columnExists('payments', 'received_by_user_id')) { $updates[] = 'received_by_user_id = ?'; $params[] = $userId; }
            if ($this->columnExists('payments', 'paid_at')) { $updates[] = 'paid_at = ?'; $params[] = $now; }
            if ($this->columnExists('payments', 'transaction_time')) { $updates[] = 'transaction_time = ?'; $params[] = $now; }
            if ($this->columnExists('payments', 'updated_at')) { $updates[] = 'updated_at = ?'; $params[] = $now; }
            if ($updates !== array()) {
                $params[] = (string)$existing['id'];
                $this->execute("UPDATE payments SET " . implode(', ', $updates) . " WHERE id = ?", $params);
            }
            return (string)$existing['id'];
        }

        $paymentId = $this->uuid();
        $columns = array('id');
        $values = array($paymentId);
        $maybe = array(
            'brand_id' => $brandId,
            'branch_id' => $branchId,
            'bill_id' => $billId,
            'order_id' => $orderId,
            'table_id' => $tableId,
            $amountColumn => $amount,
            $methodColumn => 'cash',
            $statusColumn => 'paid',
            'received_by_user_id' => $userId,
            'initiated_by' => 'waiter',
            'provider' => 'manual',
            'transaction_time' => $now,
            'paid_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        );
        foreach ($maybe as $column => $value) {
            if ($this->columnExists('payments', (string)$column)) {
                $columns[] = (string)$column;
                $values[] = $value;
            }
        }
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $this->execute("INSERT INTO payments (" . implode(', ', $columns) . ") VALUES ({$placeholders})", $values);
        return $paymentId;
    }

    public function collectApprovedBillPayment(
        string $brandId,
        string $branchId,
        string $userId,
        string $billId = '',
        string $orderId = '',
        string $scope = 'assigned'
    ): ?array {
        $scopeData = $this->resolveAccessScope($brandId, $branchId, $userId, $scope);
        if (!$scopeData) return null;

        if ($billId === '' && $orderId !== '') {
            $order = $this->getScopedOrderRecord($brandId, $branchId, $orderId, $scopeData);
            if (!$order) return null;
            $bill = $this->getBillByOrderId($orderId);
            $billId = (string)($bill['id'] ?? '');
        }
        if ($billId === '') return null;

        $bill = $this->getScopedBillRecord($brandId, $branchId, $billId, $scopeData);
        if (!$bill) return null;

        $billStatus = $this->normalizeBillStatusValue((string)($bill['bill_status'] ?? ''));
        $paymentStatus = $this->normalizePaymentStatusValue((string)($bill['payment_status'] ?? ''));
        if ($billStatus !== 'approved' || $paymentStatus === 'paid') return null;

        $order = !empty($bill['order_id']) ? $this->getOrderDetail((string)$bill['order_id']) : null;
        $tableId = (string)($bill['table_id'] ?? ($order['table_id'] ?? ''));
        if ($tableId === '') return null;

        $now = $this->now();
        $amount = (float)($bill['total_amount'] ?? 0);
        $orderIdForPayment = (string)($bill['order_id'] ?? ($order['id'] ?? ''));
        $paymentId = '';

        $this->begin();
        try {
            $paymentId = $this->createOrMarkPaymentPaid($brandId, $branchId, $billId, $orderIdForPayment, $tableId, $userId, $amount, $now);

            $billUpdates = array("payment_status = 'paid'", "bill_status = 'closed'");
            $billParams = array();
            if ($this->columnExists('bills', 'paid_at')) { $billUpdates[] = 'paid_at = ?'; $billParams[] = $now; }
            if ($this->columnExists('bills', 'closed_at')) { $billUpdates[] = 'closed_at = ?'; $billParams[] = $now; }
            if ($this->columnExists('bills', 'auto_release_at')) { $billUpdates[] = 'auto_release_at = NULL'; }
            if ($this->columnExists('bills', 'updated_at')) { $billUpdates[] = 'updated_at = ?'; $billParams[] = $now; }
            $billParams[] = $billId;
            $this->execute("UPDATE bills SET " . implode(', ', $billUpdates) . " WHERE id = ?", $billParams);

            if ($orderIdForPayment !== '') {
                $orderUpdates = array("status = 'completed'", 'is_active = 0');
                $orderParams = array();
                if ($this->columnExists('orders', 'closed_at')) { $orderUpdates[] = 'closed_at = ?'; $orderParams[] = $now; }
                if ($this->columnExists('orders', 'paid_at')) { $orderUpdates[] = 'paid_at = ?'; $orderParams[] = $now; }
                if ($this->columnExists('orders', 'updated_at')) { $orderUpdates[] = 'updated_at = ?'; $orderParams[] = $now; }
                $orderParams[] = $orderIdForPayment;
                $this->execute("UPDATE orders SET " . implode(', ', $orderUpdates) . " WHERE id = ?", $orderParams);
            }

            $this->execute(
                "UPDATE service_requests SET assigned_to_user_id = COALESCE(assigned_to_user_id, ?), status = 'closed', acknowledged_at = COALESCE(acknowledged_at, ?), resolved_at = ? WHERE brand_id = ? AND branch_id = ? AND table_id = ? AND LOWER(COALESCE(status, '')) IN ('new', 'approved', 'pending', 'accepted', 'in_progress', 'processing') AND LOWER(COALESCE(request_type, '')) IN ('payment_collection', 'payment_collection_cash', 'payment_collection_upi_verify', 'payment_verify', 'upi_verify')",
                array($userId, $now, $now, $brandId, $branchId, $tableId)
            );

            $this->execute(
                "UPDATE tables SET current_order_id = NULL, current_bill_id = NULL, status = 'available', manual_status_override = 0, status_updated_at = ?, updated_at = ? WHERE id = ?",
                array($now, $now, $tableId)
            );

            $this->pushRealtimeEvent($brandId, $branchId, 'payment.paid', 'bill', $billId, array(
                'bill_id' => $billId,
                'order_id' => $orderIdForPayment,
                'table_id' => $tableId,
                'payment_id' => $paymentId,
                'amount_paid' => $amount,
                'collected_by' => $userId,
                'bill_status' => 'closed',
                'payment_status' => 'paid',
            ));

            $this->auditLog($brandId, $branchId, $userId, 'bill', $billId, 'payment_collected', $bill, array(
                'bill_status' => 'closed',
                'payment_status' => 'paid',
                'payment_id' => $paymentId,
                'table_id' => $tableId,
            ));

            $this->commit();
            $this->syncPaidTableAvailability($brandId, $branchId, array($tableId));

            return array(
                'bill_id' => $billId,
                'payment_id' => $paymentId,
                'table_id' => $tableId,
                'order_id' => $orderIdForPayment,
                'amount_paid' => $amount,
                'status' => 'paid',
                'payment_status' => 'paid',
                'bill_status' => 'closed',
                'table_release_triggered' => true,
            );
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestWaiterModel::collectApprovedBillPayment failed: ' . $e->getMessage());
            return null;
        }
    }

    public function collectPayment(
        string $brandId,
        string $branchId,
        string $userId,
        string $billId = '',
        string $orderId = '',
        string $scope = 'assigned'
    ): ?array {
        return $this->collectApprovedBillPayment($brandId, $branchId, $userId, $billId, $orderId, $scope);
    }

    protected function buildQuickActions(string $role, array $permissions): array
    {
        $isWaiter = strtolower(trim($role)) === 'waiter';

        return array(
            array(
                'key'     => 'call_manager',
                'label'   => 'Call Manager',
                'icon'    => 'fa-user-tie',
                'enabled' => $isWaiter,
            ),
        );
    }

    protected function buildTableRows(
        array $rawTables,
        array $ordersByTable,
        array $latestRequestMap,
        array $readyPickups
    ): array {
        $readyPickupTableMap = array();
        foreach ($readyPickups as $pickup) {
            $readyPickupTableMap[(string)($pickup['table_id'] ?? '')] = true;
        }

        $tables = array();
        foreach ($rawTables as $tableRow) {
            $tableId = (string)($tableRow['id'] ?? '');
            $order = $ordersByTable[$tableId] ?? null;
            $request = $latestRequestMap[$tableId] ?? null;
            $requestType = strtolower((string)($request['request_type'] ?? ''));
            $hasRequest = $request !== null;
            $isBillRequest = in_array($requestType, array('bill', 'bill_request'), true);
            $isReady = isset($readyPickupTableMap[$tableId]);

            $state = $this->mapTableState(
                (string)($tableRow['status'] ?? 'available'),
                $order,
                $hasRequest,
                $isBillRequest,
                $isReady
            );

            $tables[] = array(
                'id'               => $tableId,
                'code'             => (string)($tableRow['table_code'] ?? $tableRow['table_number'] ?? 'Table'),
                'table_code'       => (string)($tableRow['table_code'] ?? $tableRow['table_number'] ?? 'Table'),
                'table_number'     => (string)($tableRow['table_number'] ?? $tableRow['table_code'] ?? 'Table'),
                'guests'           => (int)($order['customer_count'] ?? $tableRow['capacity'] ?? 0),
                'capacity'         => (int)($tableRow['capacity'] ?? 0),
                'status'           => (string)$state[0],
                'status_label'     => (string)$state[1],
                'amount'           => (float)($order['grand_total'] ?? 0),
                'current_order_id' => (string)($order['id'] ?? ''),
                'current_bill_id'  => (string)($tableRow['current_bill_id'] ?? ''),
                'order_id'         => (string)($order['id'] ?? ''),
                'request_id'       => (string)($request['id'] ?? ''),
                'updated_at'       => (string)($tableRow['updated_at'] ?? $tableRow['status_updated_at'] ?? ''),
                'elapsed'          => $this->timeAgo((string)($tableRow['updated_at'] ?? $tableRow['status_updated_at'] ?? ''), 'Now'),
            );
        }

        return $tables;
    }

    protected function buildServiceRequestRows(array $openRequests): array
    {
        $serviceRequests = array();
        foreach ($openRequests as $row) {
            $serviceRequests[] = array(
                'id'           => (string)($row['id'] ?? ''),
                'table_id'     => (string)($row['table_id'] ?? ''),
                'order_id'     => (string)($row['order_id'] ?? ''),
                'type'         => (string)($row['request_type'] ?? 'Service Request'),
                'request_type' => (string)($row['request_type'] ?? 'Service Request'),
                'table'        => (string)($row['table_code'] ?? ''),
                'table_code'   => (string)($row['table_code'] ?? ''),
                'message'      => (string)($row['message'] ?? ''),
                'status'       => $this->normalizeRequestStatusValue((string)($row['status'] ?? 'new')),
                'priority'     => $this->mapPriority((string)($row['request_type'] ?? '')),
                'requested_at' => (string)($row['requested_at'] ?? $row['created_at'] ?? ''),
                'created_at'   => (string)($row['created_at'] ?? ''),
                'time'         => $this->timeAgo((string)($row['requested_at'] ?? $row['created_at'] ?? ''), 'Now'),
            );
        }

        return $serviceRequests;
    }

    protected function buildPickupRows(array $readyPickups): array
    {
        $pickupOrders = array();
        foreach ($readyPickups as $row) {
            $pickupOrders[] = array(
                'id'           => (string)($row['order_id'] ?? ''),
                'order_id'     => (string)($row['order_id'] ?? ''),
                'order_number' => (string)($row['order_number'] ?? ''),
                'kot'          => '#' . (string)($row['order_number'] ?? 'ORDER'),
                'table_id'     => (string)($row['table_id'] ?? ''),
                'table'        => (string)($row['table_code'] ?? ''),
                'table_code'   => (string)($row['table_code'] ?? ''),
                'items'        => (int)($row['item_count'] ?? 0),
                'item_count'   => (int)($row['item_count'] ?? 0),
                'updated_at'   => (string)($row['updated_at'] ?? ''),
                'time'         => $this->timeAgo((string)($row['updated_at'] ?? ''), 'Now'),
            );
        }

        return $pickupOrders;
    }

    protected function buildPendingBillRows(array $pendingBills): array
    {
        $pendingBillRows = array();
        foreach ($pendingBills as $row) {
            $pendingBillRows[] = array(
                'id'             => (string)($row['id'] ?? ''),
                'bill_id'        => (string)($row['id'] ?? ''),
                'order_id'       => (string)($row['order_id'] ?? ''),
                'table_id'       => (string)($row['table_id'] ?? ''),
                'bill_number'    => (string)($row['bill_number'] ?? ''),
                'bill_status'    => $this->normalizeBillStatusValue((string)($row['bill_status'] ?? 'new')),
                'payment_status' => $this->normalizePaymentStatusValue((string)($row['payment_status'] ?? 'unpaid')),
                'table'          => (string)($row['table_code'] ?? ''),
                'table_code'     => (string)($row['table_code'] ?? ''),
                'total_amount'   => (float)($row['total_amount'] ?? 0),
                'updated_at'     => (string)($row['updated_at'] ?? ''),
                'time'           => $this->timeAgo((string)($row['updated_at'] ?? ''), 'Now'),
            );
        }

        return $pendingBillRows;
    }

    protected function getClosedOrdersCountForToday(array $tableIds, string $userId): int
    {
        if ($tableIds === array() || !$this->tableExists('orders')) {
            return 0;
        }

        $placeholders = implode(',', array_fill(0, count($tableIds), '?'));
        $params = array_merge(array_values($tableIds), array($userId));

        return (int)$this->scalar(
            "SELECT COUNT(*)
             FROM orders
             WHERE table_id IN ({$placeholders})
               AND assigned_waiter_id = ?
               AND LOWER(COALESCE(status, '')) IN ('served', 'paid', 'closed', 'completed')
               AND DATE(COALESCE(paid_at, closed_at, updated_at, created_at)) = CURDATE()",
            $params,
            0
        );
    }

    public function getSectionData(
        string $brandId,
        string $branchId,
        string $userId,
        string $section,
        string $scope = 'assigned'
    ): ?array {
        $scopeData = $this->resolveAccessScope($brandId, $branchId, $userId, $scope);
        if (!$scopeData) {
            return null;
        }

        $section = strtolower(trim($section));
        $rawTables = (array)($scopeData['tables'] ?? []);
        $tableIds = (array)($scopeData['table_ids'] ?? []);

        switch ($section) {
            case 'requests':
                $openRequests = $this->getOpenServiceRequestsForTables($tableIds);
                $items = $this->buildServiceRequestRows($openRequests);
                return ['section' => 'requests', 'title' => 'Requests', 'count' => count($items), 'items' => $items];

            case 'actions':
                $items = array_values(array_filter($this->buildQuickActions(
                    (string)($scopeData['role'] ?? 'waiter'),
                    (array)($scopeData['permissions'] ?? [])
                ), static function ($row) {
                    return !empty($row['enabled']);
                }));
                return ['section' => 'actions', 'title' => 'Actions', 'count' => count($items), 'items' => $items];

            case 'tables':
            case 'table_management':
                $ordersByTable = $this->getActiveOrdersForTables($tableIds);
                $openRequests = $this->getOpenServiceRequestsForTables($tableIds);
                $latestRequestMap = $this->getLatestOpenRequestMap($openRequests);
                $readyPickups = $this->getReadyPickupsForTables($tableIds);
                $items = $this->buildTableRows($rawTables, $ordersByTable, $latestRequestMap, $readyPickups);
                return ['section' => 'tables', 'title' => 'Table Management', 'count' => count($items), 'items' => $items];

            case 'bills':
                $pendingBills = $this->getPendingBillsForTables($tableIds);
                $items = $this->buildPendingBillRows($pendingBills);
                return ['section' => 'bills', 'title' => 'Bills', 'count' => count($items), 'items' => $items];

            case 'pickups':
                $readyPickups = $this->getReadyPickupsForTables($tableIds);
                $items = $this->buildPickupRows($readyPickups);
                return ['section' => 'pickups', 'title' => 'Pickups', 'count' => count($items), 'items' => $items];

            default:
                return null;
        }
    }

    public function buildDashboardSnapshot(
        string $brandId,
        string $branchId,
        string $userId,
        string $scope = 'assigned'
    ): ?array
    {
        $this->syncPaidTableAvailability($brandId, $branchId);

        $scopeData = $this->resolveAccessScope($brandId, $branchId, $userId, $scope);
        if (!$scopeData) {
            return null;
        }

        $context = (array)($scopeData['context'] ?? []);
        $permissions = (array)($scopeData['permissions'] ?? []);
        $role = (string)($scopeData['role'] ?? 'waiter');
        $rawTables = (array)($scopeData['tables'] ?? []);
        $tableIds = (array)($scopeData['table_ids'] ?? []);

        $ordersByTable = $this->getActiveOrdersForTables($tableIds);
        $openRequests = $this->getOpenServiceRequestsForTables($tableIds);
        $latestRequestMap = $this->getLatestOpenRequestMap($openRequests);
        $readyPickups = $this->getReadyPickupsForTables($tableIds);
        $pendingBills = $this->getPendingBillsForTables($tableIds);
        $notificationSnapshot = $this->getNotificationSnapshot($brandId, $branchId, $userId, $scope) ?? array(
            'notifications' => array(),
            'summary'       => array('total' => 0, 'unread' => 0),
        );
        $notifications = (array)($notificationSnapshot['notifications'] ?? array());
        $metrics = $this->getWaiterMetrics($brandId, $branchId, $userId, $scope) ?? array(
            'today' => $this->emptyMetricRange('today'),
            'week'  => $this->emptyMetricRange('week'),
            'month' => $this->emptyMetricRange('month'),
        );
        $tables = $this->buildTableRows($rawTables, $ordersByTable, $latestRequestMap, $readyPickups);
        $serviceRequests = $this->buildServiceRequestRows($openRequests);
        $pickupOrders = $this->buildPickupRows($readyPickups);
        $pendingBillRows = $this->buildPendingBillRows($pendingBills);
        $quickActions = array_values(array_filter($this->buildQuickActions($role, $permissions), static function ($row) {
            return !empty($row['enabled']);
        }));

        $summary = array(
            'active_tables'        => count(array_filter($tables, static function ($row) {
                return in_array((string)($row['status'] ?? ''), array('occupied', 'waiting'), true);
            })),
            'assigned_tables'      => count($tables),
            'waiting_calls'        => count($serviceRequests),
            'ready_pickups'        => count($pickupOrders),
            'pending_bills'        => count($pendingBillRows),
            'action_items'         => count($quickActions),
            'unread_notifications' => (int)($notificationSnapshot['summary']['unread'] ?? 0),
        );

        return array(
            'context' => array(
                'brand_name'       => (string)($this->getBrand($brandId)['name'] ?? 'Restaurant'),
                'branch_name'      => (string)($context['branch_name'] ?? ''),
                'branch_city'      => (string)($context['branch_city'] ?? ''),
                'branch_address'   => (string)($context['branch_address'] ?? ''),
                'branch_status'    => (string)($context['branch_status'] ?? 'active'),
                'full_name'        => (string)($context['full_name'] ?? ''),
                'email'            => (string)($context['email'] ?? ''),
                'phone'            => (string)($context['phone'] ?? ''),
                'employee_code'    => (string)($context['employee_code'] ?? ''),
                'role'             => $role,
                'waiter_status'    => (string)($context['user_status'] ?? 'active'),
                'profile_pic'      => (string)($context['profile_pic'] ?? $context['photo'] ?? ''),
                'shift_start_time' => (string)($context['shift_start_time'] ?? ''),
                'shift_end_time'   => (string)($context['shift_end_time'] ?? ''),
                'permissions'      => $permissions,
            ),
            'summary'          => $summary,
            'tables'           => $tables,
            'service_requests' => $serviceRequests,
            'pickup_orders'    => $pickupOrders,
            'pending_bills'    => $pendingBillRows,
            'quick_actions'    => $quickActions,
            'notifications'    => $notifications,
            'metrics'          => $metrics,
        );
    }

    public function getWaiterPageData(string $brandId, string $branchId, string $userId): array
    {
        $brand = $this->getBrand($brandId) ?? array();
        $branch = $this->getBranch($branchId) ?? array();
        $context = $this->getWaiterContext($brandId, $branchId, $userId) ?? array();
        $snapshot = $this->buildDashboardSnapshot($brandId, $branchId, $userId) ?? array();
        $userSettings = $this->getUserSettings($brandId, $branchId, $userId);
        $responseMetrics = $this->getWaiterMetrics($brandId, $branchId, $userId) ?? array(
            'today' => $this->emptyMetricRange('today'),
            'week'  => $this->emptyMetricRange('week'),
            'month' => $this->emptyMetricRange('month'),
        );

        $permissions = $this->normalizePermissions((array)($context['permissions'] ?? array()));
        $role = $this->getRoleName($context);
        $quickActions = $this->buildQuickActions($role, $permissions);
        $todayMetric = (array)($responseMetrics['today'] ?? $this->emptyMetricRange('today'));
        $tableIds = array_values(array_filter(array_map(static function ($row) {
            return (string)($row['id'] ?? '');
        }, (array)($snapshot['tables'] ?? array()))));
        $defaultSound = 'RestaurantAssets/AlertSounds/Sound1.mpeg';

        return array(
            'brandId'         => $brandId,
            'branchId'        => $branchId,
            'userId'          => $userId,
            'brand'           => $brand,
            'context'         => array_merge($snapshot['context'] ?? array(), array(
                'branch_name'        => (string)($snapshot['context']['branch_name'] ?? $branch['name'] ?? $context['branch_name'] ?? ''),
                'branch_city'        => (string)($snapshot['context']['branch_city'] ?? $branch['city'] ?? $context['branch_city'] ?? ''),
                'branch_address'     => (string)($snapshot['context']['branch_address'] ?? $branch['address'] ?? $context['branch_address'] ?? ''),
                'branch_status'      => (string)($snapshot['context']['branch_status'] ?? $branch['status'] ?? 'active'),
                'full_name'          => (string)($snapshot['context']['full_name'] ?? $context['full_name'] ?? ''),
                'email'              => (string)($snapshot['context']['email'] ?? $context['email'] ?? ''),
                'phone'              => (string)($snapshot['context']['phone'] ?? $context['phone'] ?? ''),
                'employee_code'      => (string)($snapshot['context']['employee_code'] ?? $context['employee_code'] ?? ''),
                'role'               => (string)($snapshot['context']['role'] ?? $role),
                'waiter_status'      => (string)($snapshot['context']['waiter_status'] ?? $context['user_status'] ?? 'active'),
                'profile_pic'        => (string)($snapshot['context']['profile_pic'] ?? $context['profile_pic'] ?? $context['photo'] ?? ''),
                'shift_start_time'   => (string)($snapshot['context']['shift_start_time'] ?? $context['shift_start_time'] ?? ''),
                'shift_end_time'     => (string)($snapshot['context']['shift_end_time'] ?? $context['shift_end_time'] ?? ''),
            )),
            'waiterName'      => (string)($context['full_name'] ?? ''),
            'shiftName'       => $this->formatShift(
                (string)($context['shift_start_time'] ?? ''),
                (string)($context['shift_end_time'] ?? '')
            ),
            'permissions'     => $permissions,
            'tables'          => (array)($snapshot['tables'] ?? array()),
            'serviceRequests' => (array)($snapshot['service_requests'] ?? array()),
            'pickupOrders'    => (array)($snapshot['pickup_orders'] ?? array()),
            'summary'         => (array)($snapshot['summary'] ?? array()),
            'quickActions'    => $quickActions,
            'shiftMetrics'    => array(
                'tables_handled'   => count((array)($snapshot['tables'] ?? array())),
                'orders_closed'    => $this->getClosedOrdersCountForToday($tableIds, $userId),
                'average_response' => (string)($todayMetric['average_label'] ?? '—'),
                'average_response_seconds' => $todayMetric['average_seconds'] ?? null,
                'average_response_range' => 'today',
                'average_response_sample_count' => (int)($todayMetric['sample_count'] ?? 0),
                'pending_actions'  => count((array)($snapshot['service_requests'] ?? array())) + count((array)($snapshot['pending_bills'] ?? array())),
            ),
            'notifications'   => (array)($snapshot['notifications'] ?? array()),
            'pendingBills'    => (array)($snapshot['pending_bills'] ?? array()),
            'responseMetrics' => $responseMetrics,
            'userSettings'    => $userSettings,
            'defaultNotificationSound' => $defaultSound,
        );
    }
}
