<?php
declare(strict_types=1);

namespace App\Models;

use RuntimeException;
use Throwable;

require_once __DIR__ . '/RestBaseModel.php';

final class RestBrandModel extends RestBaseModel
{
    protected string $defaultBrandId  = '00000000-0000-0000-0000-000000000001';
    protected string $defaultBranchId = '00000000-0000-0000-0000-000000000101';
    protected string $defaultUserId   = '00000000-0000-0000-0000-000000009001';

    public function entityIdOrNull(?string $value): ?string
    {
        $value = strtolower(trim((string) $value));

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
        return $this->entityIdOrNull($value) ?? strtolower(trim($default));
    }

    public function normalizeCsvList($value): array
    {
        if (is_array($value)) {
            $items = array_map(static function ($item): string {
                return trim((string) $item);
            }, $value);

            $items = array_filter($items, static function (string $item): bool {
                return $item !== '';
            });

            return array_values(array_unique($items));
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return [];
        }

        $items = array_map('trim', explode(',', $raw));
        $items = array_filter($items, static function (string $item): bool {
            return $item !== '';
        });

        return array_values(array_unique($items));
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

    protected function elapsedLabel(?string $datetime): string
    {
        $datetime = trim((string) $datetime);
        if ($datetime === '') {
            return 'Now';
        }

        $timestamp = strtotime($datetime);
        if ($timestamp === false) {
            return 'Now';
        }

        $diff = max(0, time() - $timestamp);

        if ($diff < 60) {
            return 'Now';
        }

        $minutes = (int) floor($diff / 60);
        if ($minutes < 60) {
            return $minutes . ' min';
        }

        $hours = (int) floor($minutes / 60);
        $rem = $minutes % 60;

        return $rem === 0 ? ($hours . ' hr') : ($hours . ' hr ' . $rem . ' min');
    }

    protected function boolToInt($value, int $default = 0): int
    {
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        $raw = strtolower(trim((string) $value));

        return in_array($raw, ['1', 'true', 'yes', 'on', 'active'], true) ? 1 : 0;
    }

    protected function jsonEncode($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $json === false ? null : $json;
    }

    protected function safeRole(string $role): string
    {
        $role = strtolower(trim($role));
        $allowed = ['owner', 'manager', 'waiter', 'kitchen', 'cashier', 'staff'];

        return in_array($role, $allowed, true) ? $role : 'staff';
    }

    protected function safeItemType(string $itemType): string
    {
        $itemType = strtolower(trim($itemType));
        $allowed = ['food', 'beverage', 'drink', 'dessert', 'addon', 'combo', 'other'];

        return in_array($itemType, $allowed, true) ? $itemType : 'food';
    }

    protected function safeStockStatus(string $stockStatus): string
    {
        $stockStatus = strtolower(trim($stockStatus));
        $allowed = ['in_stock', 'low_stock', 'out_of_stock'];

        return in_array($stockStatus, $allowed, true) ? $stockStatus : 'in_stock';
    }

    protected function safeTableStatus(string $status): string
    {
        $status = strtolower(trim($status));
        $allowed = ['available', 'occupied', 'reserved', 'payment', 'blocked', 'maintenance'];

        return in_array($status, $allowed, true) ? $status : 'available';
    }

    protected function safeOrderStatus(string $status): string
    {
        $status = strtolower(trim($status));
        $allowed = ['new', 'placed', 'accepted', 'preparing', 'ready', 'served', 'billing', 'paid', 'closed', 'cancelled'];

        return in_array($status, $allowed, true) ? $status : 'new';
    }

    protected function safeRequestStatus(string $status): string
    {
        $status = strtolower(trim($status));
        $allowed = ['pending', 'accepted', 'in_progress', 'resolved', 'rejected'];

        return in_array($status, $allowed, true) ? $status : 'resolved';
    }

    protected function firstExistingTable(array $tables): ?string
    {
        foreach ($tables as $table) {
            if ($this->tableExists($table)) {
                return $table;
            }
        }

        return null;
    }

    protected function emailExists(string $brandId, string $email, string $ignoreUserId = ''): bool
    {
        if (!$this->tableExists('users')) {
            return false;
        }

        $sql = "
            SELECT id
            FROM users
            WHERE brand_id = ?
              AND email = ?
              " . ($ignoreUserId !== '' ? "AND id <> ?" : "") . "
            LIMIT 1
        ";

        $row = $ignoreUserId !== ''
            ? $this->one($sql, [$brandId, $email, $ignoreUserId])
            : $this->one($sql, [$brandId, $email]);

        return $row !== null;
    }

    protected function parsePermissionsJson(string $json): array
    {
        $json = trim($json);
        if ($json === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }

    protected function resolveCategoryId(string $brandId, string $branchId, array $payload): string
    {
        $categoryId = trim((string) ($payload['category_id'] ?? ''));
        if ($categoryId !== '') {
            return $categoryId;
        }

        $categoryName = trim((string) ($payload['category_name'] ?? $payload['category'] ?? ''));
        if ($categoryName === '' || !$this->tableExists('menu_categories')) {
            return '';
        }

        $existing = $this->one(
            "SELECT id
             FROM menu_categories
             WHERE brand_id = ?
               AND branch_id = ?
               AND name = ?
             LIMIT 1",
            [$brandId, $branchId, $categoryName]
        );

        if ($existing) {
            return (string) ($existing['id'] ?? '');
        }

        $newId = $this->uuid();
        $this->execute(
            "INSERT INTO menu_categories (
                id, brand_id, branch_id, name, description, sort_order, is_active, created_at
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$newId, $brandId, $branchId, $categoryName, '', 999, 1, $this->now()]
        );

        return $newId;
    }

    protected function brandExists(string $brandId): bool
    {
        if (!$this->tableExists('brands')) {
            return true;
        }

        return $this->one(
            "SELECT id
             FROM brands
             WHERE id = ?
             LIMIT 1",
            [$brandId]
        ) !== null;
    }

    protected function branchExists(string $brandId, string $branchId): bool
    {
        if (!$this->tableExists('branches')) {
            return true;
        }

        return $this->one(
            "SELECT id
             FROM branches
             WHERE id = ?
               AND brand_id = ?
             LIMIT 1",
            [$branchId, $brandId]
        ) !== null;
    }

    protected function pushRealtimeEvent(
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

        return $this->execute(
            "INSERT INTO realtime_events (
                brand_id, branch_id, event_type, entity_type, entity_id, payload_json, created_at, is_published
             ) VALUES (?, ?, ?, ?, ?, ?, ?, 0)",
            [
                $brandId,
                $branchId,
                $eventType,
                $entityType,
                $entityId,
                $this->jsonEncode($payload),
                $this->now(),
            ]
        );
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

        $columns = [
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
        ];

        $values = [
            $this->uuid(),
            $brandId,
            $branchId,
            $userId,
            'brand',
            $entityType,
            $entityId,
            $action,
            $this->jsonEncode($oldData),
            $this->jsonEncode($newData),
            $this->now(),
        ];

        if ($this->columnExists('audit_logs', 'ip_address')) {
            $columns[] = 'ip_address';
            $values[] = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
        }

        if ($this->columnExists('audit_logs', 'user_agent')) {
            $columns[] = 'user_agent';
            $values[] = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
        }

        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        $this->execute(
            "INSERT INTO audit_logs (" . implode(', ', $columns) . ")
             VALUES ({$placeholders})",
            $values
        );
    }

    public function getBrand(string $brandId): ?array
    {
        if (!$this->tableExists('brands')) {
            return null;
        }

        return $this->one(
            "SELECT * FROM brands WHERE id = ? LIMIT 1",
            [$brandId]
        );
    }

    public function getBranch(string $brandId, string $branchId): ?array
    {
        if (!$this->tableExists('branches')) {
            return null;
        }

        return $this->one(
            "SELECT * FROM branches WHERE id = ? AND brand_id = ? LIMIT 1",
            [$branchId, $brandId]
        );
    }

    protected function brandContext(string $brandId, string $userId = ''): array
    {
        $brand = $this->getBrand($brandId) ?? [];

        $context = [
            'brand_name'           => (string) ($brand['name'] ?? 'Restaurant'),
            'brand_logo'           => (string) ($brand['logo'] ?? ''),
            'brand_favicon'        => (string) ($brand['favicon'] ?? ''),
            'legal_name'           => (string) ($brand['legal_name'] ?? ''),
            'subscription_plan'    => (string) ($brand['subscription_plan'] ?? 'starter'),
            'currency'             => (string) ($brand['currency'] ?? 'INR'),
            'timezone'             => (string) ($brand['timezone'] ?? 'Asia/Kolkata'),
            'status'               => (string) ($brand['status'] ?? 'active'),
            'mail_id'              => (string) ($brand['mail_id'] ?? ''),
            'mobile'               => (string) ($brand['mobile'] ?? ''),
            'owner_name'           => 'Brand Owner',
            'owner_email'          => (string) ($brand['mail_id'] ?? ''),
            'owner_phone'          => (string) ($brand['mobile'] ?? ''),
            'total_branches'       => 0,
            'active_branches'      => 0,
            'inactive_branches'    => 0,
        ];

        if ($this->tableExists('branches')) {
            $counts = $this->one(
                "SELECT
                    COUNT(*) AS total_branches,
                    SUM(CASE WHEN LOWER(COALESCE(status, 'active')) = 'active' THEN 1 ELSE 0 END) AS active_branches,
                    SUM(CASE WHEN LOWER(COALESCE(status, 'active')) <> 'active' THEN 1 ELSE 0 END) AS inactive_branches
                 FROM branches
                 WHERE brand_id = ?",
                [$brandId]
            );

            if ($counts) {
                $context['total_branches'] = (int) ($counts['total_branches'] ?? 0);
                $context['active_branches'] = (int) ($counts['active_branches'] ?? 0);
                $context['inactive_branches'] = (int) ($counts['inactive_branches'] ?? 0);
            }
        }

        if ($userId !== '' && $this->tableExists('users')) {
            $owner = $this->one(
                "SELECT full_name, email, phone
                 FROM users
                 WHERE id = ?
                   AND brand_id = ?
                 LIMIT 1",
                [$userId, $brandId]
            );

            if ($owner) {
                $context['owner_name']  = (string) ($owner['full_name'] ?? $context['owner_name']);
                $context['owner_email'] = (string) ($owner['email'] ?? $context['owner_email']);
                $context['owner_phone'] = (string) ($owner['phone'] ?? $context['owner_phone']);
            }
        }

        return $context;
    }

    public function getBrandPageData(string $brandId, string $branchId, string $userId = ''): array
    {
        $brandId  = $this->normalizeEntityIdOrDefault($brandId, $this->defaultBrandId);
        $branchId = $this->normalizeEntityIdOrDefault($branchId, $this->defaultBranchId);
        $userId   = $this->normalizeEntityIdOrDefault($userId, $this->defaultUserId);

        return [
            'brandId'  => $brandId,
            'branchId' => $branchId,
            'userId'   => $userId,
            'brand'    => $this->getBrand($brandId) ?? [],
            'snapshot' => $this->buildDashboardSnapshot($brandId, $branchId, $userId) ?? [],
        ];
    }

    public function buildDashboardSnapshot(string $brandId, string $branchId, string $userId = ''): ?array
    {
        if (!$this->brandExists($brandId)) {
            return null;
        }

        $context            = $this->brandContext($brandId, $userId);
        $branches           = $this->branchList($brandId);
        $liveOrders         = $this->liveOrders($brandId);
        $openBills          = $this->openBills($brandId);
        $staff              = $this->staffList($brandId);
        $tables             = $this->tableList($brandId);
        $menuItems          = $this->menuItems($brandId, $branchId);
        $menuCategories     = $this->menuCategories($brandId, $branchId);
        $serviceRequests    = $this->serviceRequests($brandId);
        $alerts             = $this->alerts($brandId);
        $recentActivity     = $this->recentActivity($brandId);
        $topItems           = $this->topItems($brandId);
        $revenueChart       = $this->revenueChart($brandId);
        $ordersChart        = $this->ordersChart($brandId);
        $categoryRevenue    = $this->categoryRevenueChart($brandId);
        $branchPerformance  = $this->branchPerformance($brandId, $branches);

        $managers = array_values(array_filter($staff, static function (array $row): bool {
            return (string) ($row['role_at_branch'] ?? '') === 'manager';
        }));

        $waiters = array_values(array_filter($staff, static function (array $row): bool {
            return (string) ($row['role_at_branch'] ?? '') === 'waiter';
        }));

        $kitchenStaff = array_values(array_filter($staff, static function (array $row): bool {
            return (string) ($row['role_at_branch'] ?? '') === 'kitchen';
        }));

        $todayRevenue      = $this->todayRevenueTotal($brandId);
        $todayOrders       = $this->todayOrdersCount($brandId);
        $totalTables       = count($tables);
        $activeTables      = count(array_filter($tables, static function (array $table): bool {
            return !in_array((string) ($table['status'] ?? 'available'), ['blocked', 'maintenance'], true);
        }));
        $pendingRequests   = count(array_filter($serviceRequests, static function (array $row): bool {
            return in_array((string) ($row['status'] ?? 'pending'), ['pending', 'new', 'open', 'accepted', 'in_progress'], true);
        })) + count($alerts);
        $menuItemsCount    = count($menuItems);
        $categoriesCount   = count($menuCategories);
        $branchesCount     = count($branches);
        $avgOrderValue     = $todayOrders > 0 ? round($todayRevenue / $todayOrders, 2) : 0.0;
        $occupiedTables    = count(array_filter($tables, static function (array $table): bool {
            return in_array((string) ($table['status'] ?? ''), ['occupied', 'reserved', 'payment'], true);
        }));
        $occupancyRate     = $totalTables > 0 ? round(($occupiedTables / $totalTables) * 100, 2) : 0.0;
        $collectionPending = array_reduce($openBills, static function (float $carry, array $bill): float {
            return $carry + (float) ($bill['grand_total'] ?? 0);
        }, 0.0);
        $todayPayments = $todayRevenue;

        return [
            'context' => $context,
            'summary' => [
                'today_revenue'      => $todayRevenue,
                'today_orders'       => $todayOrders,
                'live_orders'        => count($liveOrders),
                'open_bills'         => count($openBills),
                'pending_requests'   => $pendingRequests,
                'total_tables'       => $totalTables,
                'active_tables'      => $activeTables,
                'staff_count'        => count($staff),
                'manager_count'      => count($managers),
                'waiter_count'       => count($waiters),
                'kitchen_count'      => count($kitchenStaff),
                'menu_items_count'   => $menuItemsCount,
                'categories_count'   => $categoriesCount,
                'branches_count'     => $branchesCount,
                'avg_order_value'    => $avgOrderValue,
                'occupancy_rate'     => $occupancyRate,
                'collection_pending' => round($collectionPending, 2),
                'today_payments'     => $todayPayments,
            ],
            'branches'               => $branches,
            'branch_performance'     => $branchPerformance,
            'live_orders'            => $liveOrders,
            'open_bills'             => $openBills,
            'staff'                  => $staff,
            'managers'               => $managers,
            'waiters'                => $waiters,
            'kitchen_staff'          => $kitchenStaff,
            'tables'                 => $tables,
            'menu_items'             => $menuItems,
            'menu_categories'        => $menuCategories,
            'service_requests'       => $serviceRequests,
            'alerts'                 => $alerts,
            'recent_activity'        => $recentActivity,
            'top_items'              => $topItems,
            'revenue_chart'          => $revenueChart,
            'orders_chart'           => $ordersChart,
            'category_revenue_chart' => $categoryRevenue,
            'server_time'            => $this->now(),
        ];
    }

    protected function branchList(string $brandId): array
    {
        if (!$this->tableExists('branches')) {
            return [];
        }

        $rows = $this->all(
            "SELECT
                id,
                name,
                code,
                address,
                city,
                state,
                pincode,
                phone,
                email,
                status,
                opens_at,
                closes_at,
                tax_number
             FROM branches
             WHERE brand_id = ?
             ORDER BY name ASC, id ASC",
            [$brandId]
        );

        $branches = [];

        foreach ($rows as $row) {
            $branchId = (string) ($row['id'] ?? '');

            $branches[] = [
                'id'            => $branchId,
                'branch_id'     => $branchId,
                'name'          => (string) ($row['name'] ?? 'Branch'),
                'branch_name'   => (string) ($row['name'] ?? 'Branch'),
                'code'          => (string) ($row['code'] ?? ''),
                'branch_code'   => (string) ($row['code'] ?? ''),
                'address'       => (string) ($row['address'] ?? ''),
                'city'          => (string) ($row['city'] ?? ''),
                'state'         => (string) ($row['state'] ?? ''),
                'pincode'       => (string) ($row['pincode'] ?? ''),
                'phone'         => (string) ($row['phone'] ?? ''),
                'email'         => (string) ($row['email'] ?? ''),
                'status'        => (string) ($row['status'] ?? 'active'),
                'opens_at'      => (string) ($row['opens_at'] ?? ''),
                'closes_at'     => (string) ($row['closes_at'] ?? ''),
                'tax_number'    => (string) ($row['tax_number'] ?? ''),
                'today_revenue' => $this->todayRevenueTotal($brandId, $branchId),
                'today_orders'  => $this->todayOrdersCount($brandId, $branchId),
                'tables_count'  => $this->branchTableCount($branchId),
                'manager_count' => $this->branchRoleCount($brandId, $branchId, 'manager'),
                'staff_count'   => $this->branchStaffCount($brandId, $branchId),
            ];
        }

        return $branches;
    }

    protected function branchPerformance(string $brandId, array $branches): array
    {
        $result = [];

        foreach ($branches as $branch) {
            $branchId = (string) ($branch['branch_id'] ?? '');

            $result[] = [
                'branch_id'     => $branchId,
                'branch_name'   => (string) ($branch['branch_name'] ?? 'Branch'),
                'branch_code'   => (string) ($branch['branch_code'] ?? ''),
                'branch_city'   => (string) ($branch['city'] ?? ''),
                'branch_status' => (string) ($branch['status'] ?? 'active'),
                'today_revenue' => (float) ($branch['today_revenue'] ?? 0),
                'today_orders'  => (int) ($branch['today_orders'] ?? 0),
                'tables_count'  => (int) ($branch['tables_count'] ?? 0),
                'manager_count' => (int) ($branch['manager_count'] ?? 0),
                'staff_count'   => (int) ($branch['staff_count'] ?? 0),
            ];
        }

        usort($result, static function (array $a, array $b): int {
            return ((float) ($b['today_revenue'] ?? 0)) <=> ((float) ($a['today_revenue'] ?? 0));
        });

        return $result;
    }

    protected function menuCategories(string $brandId, string $branchId = ''): array
    {
        if (!$this->tableExists('menu_categories')) {
            return [];
        }

        if ($branchId !== '' && $this->branchExists($brandId, $branchId)) {
            return $this->all(
                "SELECT
                    id,
                    name,
                    description,
                    image_url,
                    sort_order,
                    is_active
                 FROM menu_categories
                 WHERE brand_id = ?
                   AND branch_id = ?
                 ORDER BY sort_order ASC, name ASC",
                [$brandId, $branchId]
            );
        }

        return $this->all(
            "SELECT
                id,
                name,
                description,
                image_url,
                sort_order,
                is_active
             FROM menu_categories
             WHERE brand_id = ?
             ORDER BY sort_order ASC, name ASC",
            [$brandId]
        );
    }

    protected function tableList(string $brandId, string $branchId = ''): array
    {
        $tableName = $this->firstExistingTable(['tables', 'restaurant_tables']);
        if ($tableName === null) {
            return [];
        }

        $codeColumn = $this->columnExists($tableName, 'table_code') ? 'table_code' : 'id';

        if ($this->columnExists($tableName, 'table_number')) {
            $nameColumn = 'table_number';
        } elseif ($this->columnExists($tableName, 'table_code')) {
            $nameColumn = 'table_code';
        } else {
            $nameColumn = 'id';
        }

        $branchJoin = $this->tableExists('branches')
            ? "LEFT JOIN branches b ON b.id = t.branch_id"
            : "";

        $where = "t.brand_id = ?";
        $params = [$brandId];

        if ($branchId !== '') {
            $where .= " AND t.branch_id = ?";
            $params[] = $branchId;
        }

        $rows = $this->all(
            "SELECT
                t.id,
                t.branch_id,
                {$codeColumn} AS table_code,
                {$nameColumn} AS table_name,
                " . ($this->columnExists($tableName, 'status') ? 't.status' : "'available'") . " AS status,
                " . ($this->columnExists($tableName, 'capacity') ? 't.capacity' : '0') . " AS capacity,
                " . ($this->columnExists($tableName, 'current_order_id') ? 't.current_order_id' : "''") . " AS current_order_id,
                " . ($this->columnExists($tableName, 'current_bill_id') ? 't.current_bill_id' : "''") . " AS current_bill_id,
                " . ($this->columnExists($tableName, 'shape') ? 't.shape' : "''") . " AS shape,
                '' AS label,
                '' AS zone,
                '' AS area_name,
                " . ($this->tableExists('branches') ? "COALESCE(b.name, '')" : "''") . " AS branch_name
             FROM {$tableName} t
             {$branchJoin}
             WHERE {$where}
             ORDER BY branch_name ASC, {$nameColumn} ASC, t.id ASC",
            $params
        );

        return array_map(static function (array $row): array {
            return [
                'id'               => (string) ($row['id'] ?? ''),
                'table_id'         => (string) ($row['id'] ?? ''),
                'branch_id'        => (string) ($row['branch_id'] ?? ''),
                'branch_name'      => (string) ($row['branch_name'] ?? ''),
                'table_code'       => (string) ($row['table_code'] ?? ''),
                'table_name'       => (string) ($row['table_name'] ?? ''),
                'status'           => (string) ($row['status'] ?? 'available'),
                'occupancy_status' => (string) ($row['status'] ?? 'available'),
                'capacity'         => (string) ($row['capacity'] ?? '0'),
                'label'            => '',
                'zone'             => '',
                'area_name'        => '',
                'section_name'     => '',
                'shape'            => (string) ($row['shape'] ?? ''),
                'current_order_id' => (string) ($row['current_order_id'] ?? ''),
                'current_bill_id'  => (string) ($row['current_bill_id'] ?? ''),
            ];
        }, $rows);
    }

    protected function staffList(string $brandId, string $branchId = ''): array
    {
        if (!$this->tableExists('users') || !$this->tableExists('user_access')) {
            return [];
        }

        $where = "u.brand_id = ?";
        $params = [$brandId];

        if ($branchId !== '') {
            $where .= " AND ua.branch_id = ?";
            $params[] = $branchId;
        }

        $rows = $this->all(
            "SELECT
                u.id,
                COALESCE(u.default_branch_id, '') AS default_branch_id,
                COALESCE(u.full_name, '') AS full_name,
                COALESCE(u.email, '') AS email,
                COALESCE(u.phone, '') AS phone,
                COALESCE(u.employee_code, '') AS employee_code,
                COALESCE(u.role_type, ua.role_at_branch, 'staff') AS role_type,
                COALESCE(u.status, 'active') AS user_status,
                COALESCE(u.is_active, 1) AS is_active,
                COALESCE(ua.branch_id, '') AS branch_id,
                COALESCE(ua.role_at_branch, 'staff') AS role_at_branch,
                COALESCE(ua.assigned_table_codes, '') AS assigned_table_codes,
                COALESCE(ua.permissions_json, '') AS permissions_json,
                COALESCE(ua.shift_start_time, '') AS shift_start_time,
                COALESCE(ua.shift_end_time, '') AS shift_end_time,
                COALESCE(ua.access_status, 'active') AS access_status,
                " . ($this->tableExists('branches') ? "COALESCE(b.name, '')" : "''") . " AS branch_name
             FROM users u
             INNER JOIN user_access ua
                ON ua.user_id = u.id
             " . ($this->tableExists('branches') ? "LEFT JOIN branches b ON b.id = ua.branch_id" : "") . "
             WHERE {$where}
             ORDER BY u.full_name ASC, u.id ASC",
            $params
        );

        return array_map(function (array $row): array {
            $codes = $this->normalizeCsvList((string) ($row['assigned_table_codes'] ?? ''));
            $permissions = $this->parsePermissionsJson((string) ($row['permissions_json'] ?? ''));

            return [
                'id'                         => (string) ($row['id'] ?? ''),
                'user_id'                    => (string) ($row['id'] ?? ''),
                'default_branch_id'          => (string) ($row['default_branch_id'] ?? ''),
                'branch_id'                  => (string) ($row['branch_id'] ?? ''),
                'branch_name'                => (string) ($row['branch_name'] ?? ''),
                'full_name'                  => (string) ($row['full_name'] ?? 'Staff'),
                'name'                       => (string) ($row['full_name'] ?? 'Staff'),
                'email'                      => (string) ($row['email'] ?? ''),
                'phone'                      => (string) ($row['phone'] ?? ''),
                'employee_code'              => (string) ($row['employee_code'] ?? ''),
                'role_type'                  => (string) ($row['role_type'] ?? 'staff'),
                'role_at_branch'             => (string) ($row['role_at_branch'] ?? 'staff'),
                'designation'                => (string) ($row['role_at_branch'] ?? 'staff'),
                'is_active'                  => (string) ($row['is_active'] ?? '1'),
                'user_status'                => (string) ($row['user_status'] ?? 'active'),
                'access_status'              => (string) ($row['access_status'] ?? 'active'),
                'assigned_table_codes'       => $codes,
                'assigned_table_codes_label' => !empty($codes) ? implode(', ', $codes) : 'All',
                'permissions'                => $permissions,
                'permissions_json'           => (string) ($row['permissions_json'] ?? ''),
                'shift_start_time'           => (string) ($row['shift_start_time'] ?? ''),
                'shift_end_time'             => (string) ($row['shift_end_time'] ?? ''),
                'status'                     => ((string) ($row['is_active'] ?? '1') === '1') ? 'active' : 'inactive',
            ];
        }, $rows);
    }

    protected function menuItems(string $brandId, string $branchId = ''): array
    {
        if (!$this->tableExists('menu_items')) {
            return [];
        }

        $joinCategory = $this->tableExists('menu_categories')
            ? "LEFT JOIN menu_categories mc ON mc.id = mi.category_id"
            : '';

        $joinBranchMenu = '';
        $params = [];

        if ($this->tableExists('branch_menu_items')) {
            if ($branchId !== '' && $this->branchExists($brandId, $branchId)) {
                $joinBranchMenu = "LEFT JOIN branch_menu_items bmi
                                     ON bmi.menu_item_id = mi.id
                                    AND bmi.branch_id = ?";
                $params[] = $branchId;
            } else {
                $joinBranchMenu = "LEFT JOIN branch_menu_items bmi
                                     ON bmi.menu_item_id = mi.id";
            }
        }

        $params[] = $brandId;

        $rows = $this->all(
            "SELECT
                mi.id,
                COALESCE(mi.category_id, '') AS category_id,
                COALESCE(mi.default_kitchen_station_id, '') AS default_kitchen_station_id,
                COALESCE(mi.name, '') AS name,
                COALESCE(mi.description, '') AS description,
                COALESCE(mi.image_url, '') AS image_url,
                COALESCE(mi.video_url, '') AS video_url,
                COALESCE(mi.video_type, '') AS video_type,
                COALESCE(mi.item_type, 'food') AS item_type,
                COALESCE(mi.sku, '') AS sku,
                COALESCE(mi.base_price, 0) AS base_price,
                COALESCE(mi.tax_rate, 0) AS tax_rate,
                COALESCE(mi.preparation_time_mins, 0) AS preparation_time_mins,
                COALESCE(mi.is_veg, 0) AS is_veg,
                COALESCE(mi.is_vegan, 0) AS is_vegan,
                COALESCE(mi.is_spicy, 0) AS is_spicy,
                COALESCE(mi.is_available, 1) AS master_is_available,
                " . ($this->tableExists('menu_categories') ? "COALESCE(mc.name, 'General')" : "'General'") . " AS category_name,
                " . ($this->tableExists('branch_menu_items') ? "COALESCE(bmi.id, '')" : "''") . " AS branch_menu_item_id,
                " . ($this->tableExists('branch_menu_items') ? "COALESCE(bmi.branch_id, '')" : "''") . " AS branch_id,
                " . ($this->tableExists('branch_menu_items') ? "COALESCE(bmi.selling_price, mi.base_price, 0)" : "COALESCE(mi.base_price, 0)") . " AS selling_price,
                " . ($this->tableExists('branch_menu_items') ? "COALESCE(bmi.is_available, mi.is_available, 1)" : "COALESCE(mi.is_available, 1)") . " AS is_available,
                " . ($this->tableExists('branch_menu_items') ? "COALESCE(bmi.stock_status, 'in_stock')" : "'in_stock'") . " AS stock_status,
                " . ($this->tableExists('branch_menu_items') ? "COALESCE(bmi.available_from, '')" : "''") . " AS available_from,
                " . ($this->tableExists('branch_menu_items') ? "COALESCE(bmi.available_to, '')" : "''") . " AS available_to
             FROM menu_items mi
             {$joinCategory}
             {$joinBranchMenu}
             WHERE mi.brand_id = ?
             ORDER BY category_name ASC, mi.name ASC",
            $params
        );

        return array_map(static function (array $row): array {
            return [
                'id'                         => (string) ($row['id'] ?? ''),
                'menu_id'                    => (string) ($row['id'] ?? ''),
                'branch_menu_item_id'        => (string) ($row['branch_menu_item_id'] ?? ''),
                'branch_id'                  => (string) ($row['branch_id'] ?? ''),
                'category_id'                => (string) ($row['category_id'] ?? ''),
                'category_name'              => (string) ($row['category_name'] ?? 'General'),
                'default_kitchen_station_id' => (string) ($row['default_kitchen_station_id'] ?? ''),
                'name'                       => (string) ($row['name'] ?? 'Menu Item'),
                'item_name'                  => (string) ($row['name'] ?? 'Menu Item'),
                'sku'                        => (string) ($row['sku'] ?? ''),
                'description'                => (string) ($row['description'] ?? ''),
                'image_url'                  => (string) ($row['image_url'] ?? ''),
                'video_url'                  => (string) ($row['video_url'] ?? ''),
                'video_type'                 => (string) ($row['video_type'] ?? ''),
                'item_type'                  => (string) ($row['item_type'] ?? 'food'),
                'food_type'                  => (string) ($row['item_type'] ?? 'food'),
                'price'                      => (float) ($row['selling_price'] ?? 0),
                'selling_price'              => (float) ($row['selling_price'] ?? 0),
                'base_price'                 => (float) ($row['base_price'] ?? 0),
                'tax_rate'                   => (float) ($row['tax_rate'] ?? 0),
                'preparation_time_mins'      => (int) ($row['preparation_time_mins'] ?? 0),
                'is_veg'                     => (string) ($row['is_veg'] ?? '0'),
                'is_vegan'                   => (string) ($row['is_vegan'] ?? '0'),
                'is_spicy'                   => (string) ($row['is_spicy'] ?? '0'),
                'is_available'               => (string) ($row['is_available'] ?? '1'),
                'is_active'                  => (string) ($row['is_available'] ?? '1'),
                'stock_status'               => (string) ($row['stock_status'] ?? 'in_stock'),
                'available_from'             => (string) ($row['available_from'] ?? ''),
                'available_to'               => (string) ($row['available_to'] ?? ''),
            ];
        }, $rows);
    }

    protected function orderItemsMap(string $brandId, string $branchId = ''): array
    {
        if (!$this->tableExists('orders') || !$this->tableExists('order_items')) {
            return [];
        }

        $where = "o.brand_id = ?";
        $params = [$brandId];

        if ($branchId !== '') {
            $where .= " AND o.branch_id = ?";
            $params[] = $branchId;
        }

        $rows = $this->all(
            "SELECT
                oi.order_id,
                COALESCE(oi.item_name_snapshot, mi.name, 'Item') AS item_name,
                COALESCE(oi.quantity, 0) AS qty_value,
                COALESCE(oi.status, 'placed') AS status,
                COALESCE(oi.special_instructions, '') AS note
             FROM order_items oi
             INNER JOIN orders o
                ON o.id = oi.order_id
             LEFT JOIN menu_items mi
                ON mi.id = oi.menu_item_id
             WHERE {$where}
             ORDER BY oi.created_at ASC, oi.id ASC",
            $params
        );

        $map = [];
        foreach ($rows as $row) {
            $orderId = (string) ($row['order_id'] ?? '');
            if ($orderId === '') {
                continue;
            }

            $map[$orderId][] = [
                'name'   => (string) ($row['item_name'] ?? 'Item'),
                'qty'    => (int) ($row['qty_value'] ?? 0),
                'status' => (string) ($row['status'] ?? 'placed'),
                'note'   => (string) ($row['note'] ?? ''),
            ];
        }

        return $map;
    }

    protected function liveOrders(string $brandId, string $branchId = ''): array
    {
        if (!$this->tableExists('orders')) {
            return [];
        }

        $tablesTable = $this->firstExistingTable(['tables', 'restaurant_tables']);
        $joinTable = '';
        $tableCodeExpr = "'Table'";

        if ($tablesTable !== null) {
            $tableCodeColumn = $this->columnExists($tablesTable, 'table_code') ? 'table_code' : 'id';
            $joinTable = "LEFT JOIN {$tablesTable} t ON t.id = o.table_id";
            $tableCodeExpr = "t.{$tableCodeColumn}";
        }

        $joinBranch = $this->tableExists('branches')
            ? "LEFT JOIN branches b ON b.id = o.branch_id"
            : "";

        $where = "o.brand_id = ?";
        $params = [$brandId];

        if ($branchId !== '') {
            $where .= " AND o.branch_id = ?";
            $params[] = $branchId;
        }

        $rows = $this->all(
            "SELECT
                o.id,
                o.branch_id,
                COALESCE(o.order_number, o.id) AS order_number,
                COALESCE(o.status, 'new') AS status,
                COALESCE(o.customer_count, 0) AS customer_count,
                COALESCE(o.grand_total, 0) AS grand_total,
                COALESCE(o.notes, '') AS notes,
                COALESCE(o.created_at, o.updated_at) AS created_at,
                {$tableCodeExpr} AS table_code,
                " . ($this->tableExists('branches') ? "COALESCE(b.name, '')" : "''") . " AS branch_name
             FROM orders o
             {$joinTable}
             {$joinBranch}
             WHERE {$where}
               AND COALESCE(o.is_active, 1) = 1
               AND COALESCE(o.status, 'new') NOT IN ('closed', 'paid', 'cancelled')
             ORDER BY COALESCE(o.created_at, o.updated_at) DESC, o.id DESC
             LIMIT 150",
            $params
        );

        $itemsMap = $this->orderItemsMap($brandId, $branchId);

        return array_map(function (array $row) use ($itemsMap): array {
            $orderId = (string) ($row['id'] ?? '');
            $items = $itemsMap[$orderId] ?? [];

            return [
                'order_id'       => $orderId,
                'branch_id'      => (string) ($row['branch_id'] ?? ''),
                'branch_name'    => (string) ($row['branch_name'] ?? ''),
                'order_number'   => (string) ($row['order_number'] ?? ('#' . substr($orderId, 0, 8))),
                'table_code'     => (string) ($row['table_code'] ?? 'Table'),
                'status'         => (string) ($row['status'] ?? 'new'),
                'status_label'   => ucfirst((string) ($row['status'] ?? 'new')),
                'customer_count' => (int) ($row['customer_count'] ?? 0),
                'grand_total'    => (float) ($row['grand_total'] ?? 0),
                'notes'          => (string) ($row['notes'] ?? ''),
                'created_at'     => (string) ($row['created_at'] ?? ''),
                'elapsed'        => $this->elapsedLabel((string) ($row['created_at'] ?? '')),
                'items'          => $items,
                'items_count'    => array_reduce($items, static function (int $carry, array $item): int {
                    return $carry + (int) ($item['qty'] ?? 0);
                }, 0),
                'waiter_name'    => '-',
            ];
        }, $rows);
    }

    protected function openBills(string $brandId, string $branchId = ''): array
    {
        if (!$this->tableExists('bills')) {
            return [];
        }

        $tablesTable = $this->firstExistingTable(['tables', 'restaurant_tables']);
        $joinTable = '';
        $tableCodeExpr = "'Table'";

        if ($tablesTable !== null) {
            $tableCodeColumn = $this->columnExists($tablesTable, 'table_code') ? 'table_code' : 'id';
            $joinTable = "LEFT JOIN {$tablesTable} t ON t.id = b.table_id";
            $tableCodeExpr = "t.{$tableCodeColumn}";
        }

        $joinBranch = $this->tableExists('branches')
            ? "LEFT JOIN branches br ON br.id = b.branch_id"
            : "";

        $where = "b.brand_id = ?";
        $params = [$brandId];

        if ($branchId !== '') {
            $where .= " AND b.branch_id = ?";
            $params[] = $branchId;
        }

        $rows = $this->all(
            "SELECT
                b.id,
                b.branch_id,
                COALESCE(b.bill_number, b.id) AS bill_number,
                COALESCE(b.order_id, '') AS order_id,
                COALESCE(b.bill_status, 'generated') AS bill_status,
                COALESCE(b.payment_status, 'unpaid') AS payment_status,
                COALESCE(b.subtotal, 0) AS subtotal,
                COALESCE(b.tax_amount, 0) AS tax_amount,
                COALESCE(b.total_amount, 0) AS total_amount,
                COALESCE(b.generated_at, b.created_at, b.updated_at) AS event_time,
                {$tableCodeExpr} AS table_code,
                " . ($this->tableExists('branches') ? "COALESCE(br.name, '')" : "''") . " AS branch_name
             FROM bills b
             {$joinTable}
             {$joinBranch}
             WHERE {$where}
               AND (
                    COALESCE(b.payment_status, 'unpaid') IN ('unpaid', 'partial')
                    OR COALESCE(b.bill_status, 'generated') IN ('generated', 'open', 'pending')
               )
             ORDER BY event_time DESC, b.id DESC
             LIMIT 150",
            $params
        );

        return array_map(static function (array $row): array {
            $paymentStatus = (string) ($row['payment_status'] ?? 'unpaid');
            $billStatus = (string) ($row['bill_status'] ?? 'generated');
            $displayStatus = $paymentStatus === 'paid' ? 'paid' : $billStatus;

            return [
                'bill_id'          => (string) ($row['id'] ?? ''),
                'branch_id'        => (string) ($row['branch_id'] ?? ''),
                'branch_name'      => (string) ($row['branch_name'] ?? ''),
                'bill_number'      => (string) ($row['bill_number'] ?? '#BILL'),
                'order_id'         => (string) ($row['order_id'] ?? ''),
                'order_code'       => (string) ($row['order_id'] ?? ''),
                'table_code'       => (string) ($row['table_code'] ?? 'Table'),
                'status'           => $displayStatus,
                'status_label'     => ucfirst($displayStatus),
                'bill_status'      => $billStatus,
                'payment_status'   => $paymentStatus,
                'subtotal'         => (float) ($row['subtotal'] ?? 0),
                'tax_amount'       => (float) ($row['tax_amount'] ?? 0),
                'grand_total'      => (float) ($row['total_amount'] ?? 0),
                'payable_amount'   => (float) ($row['total_amount'] ?? 0),
                'created_at_label' => (string) ($row['event_time'] ?? ''),
                'updated_at'       => (string) ($row['event_time'] ?? ''),
                'generated_at'     => (string) ($row['event_time'] ?? ''),
            ];
        }, $rows);
    }

    protected function serviceRequests(string $brandId, string $branchId = ''): array
    {
        if (!$this->tableExists('service_requests')) {
            return [];
        }

        $tablesTable = $this->firstExistingTable(['tables', 'restaurant_tables']);
        $joinTable = '';
        $tableCodeExpr = "'-'";

        if ($tablesTable !== null) {
            $tableCodeColumn = $this->columnExists($tablesTable, 'table_code') ? 'table_code' : 'id';
            $joinTable = "LEFT JOIN {$tablesTable} t ON t.id = sr.table_id";
            $tableCodeExpr = "t.{$tableCodeColumn}";
        }

        $joinBranch = $this->tableExists('branches')
            ? "LEFT JOIN branches b ON b.id = sr.branch_id"
            : "";

        $eventTimeExpr = "COALESCE(sr.requested_at, sr.resolved_at, sr.acknowledged_at, sr.created_at)";
        $where = "sr.brand_id = ?";
        $params = [$brandId];

        if ($branchId !== '') {
            $where .= " AND sr.branch_id = ?";
            $params[] = $branchId;
        }

        $rows = $this->all(
            "SELECT
                sr.id,
                sr.branch_id,
                COALESCE(sr.order_id, '') AS order_id,
                COALESCE(sr.request_type, 'service') AS request_type,
                COALESCE(sr.status, 'pending') AS status,
                COALESCE(sr.message, '') AS message,
                {$eventTimeExpr} AS created_at,
                {$tableCodeExpr} AS table_code,
                " . ($this->tableExists('branches') ? "COALESCE(b.name, '')" : "''") . " AS branch_name
             FROM service_requests sr
             {$joinTable}
             {$joinBranch}
             WHERE {$where}
             ORDER BY {$eventTimeExpr} DESC, sr.id DESC
             LIMIT 100",
            $params
        );

        return array_map(function (array $row): array {
            $createdAt = (string) ($row['created_at'] ?? '');

            return [
                'id'               => (string) ($row['id'] ?? ''),
                'request_id'       => (string) ($row['id'] ?? ''),
                'branch_id'        => (string) ($row['branch_id'] ?? ''),
                'branch_name'      => (string) ($row['branch_name'] ?? ''),
                'order_id'         => (string) ($row['order_id'] ?? ''),
                'title'            => ucfirst((string) ($row['request_type'] ?? 'Request')),
                'request_type'     => (string) ($row['request_type'] ?? 'service'),
                'status'           => (string) ($row['status'] ?? 'pending'),
                'message'          => (string) ($row['message'] ?? ''),
                'table_code'       => (string) ($row['table_code'] ?? '-'),
                'source'           => 'waiter/customer',
                'created_at'       => $createdAt,
                'created_at_label' => $this->elapsedLabel($createdAt),
            ];
        }, $rows);
    }

    protected function alerts(string $brandId): array
    {
        $alerts = [];

        foreach ($this->liveOrders($brandId) as $order) {
            if ((string) ($order['status'] ?? '') === 'ready') {
                $alerts[] = [
                    'id'               => 'alert-order-' . (string) ($order['order_id'] ?? ''),
                    'title'            => 'Order Ready',
                    'alert_type'       => 'order_ready',
                    'status'           => 'pending',
                    'message'          => 'Ready to serve ' . (string) ($order['order_number'] ?? ''),
                    'table_code'       => (string) ($order['table_code'] ?? '-'),
                    'branch_name'      => (string) ($order['branch_name'] ?? ''),
                    'source'           => 'kitchen',
                    'created_at'       => $this->now(),
                    'created_at_label' => 'Now',
                ];
            }
        }

        return $alerts;
    }

    protected function recentActivity(string $brandId): array
    {
        $activity = [];

        foreach (array_slice($this->liveOrders($brandId), 0, 8) as $order) {
            $activity[] = [
                'title'            => 'Order ' . (string) ($order['order_number'] ?? ''),
                'description'      => 'Order is currently ' . strtolower((string) ($order['status_label'] ?? 'Active')),
                'message'          => 'Order update',
                'status'           => (string) ($order['status'] ?? 'active'),
                'source'           => 'orders',
                'actor'            => 'system',
                'table_code'       => (string) ($order['table_code'] ?? '-'),
                'branch_name'      => (string) ($order['branch_name'] ?? ''),
                'created_at_label' => (string) ($order['elapsed'] ?? 'Now'),
                'elapsed'          => (string) ($order['elapsed'] ?? 'Now'),
            ];
        }

        foreach (array_slice($this->serviceRequests($brandId), 0, 6) as $request) {
            $activity[] = [
                'title'            => (string) ($request['title'] ?? 'Request'),
                'description'      => (string) ($request['message'] ?? 'Service request'),
                'message'          => (string) ($request['message'] ?? 'Service request'),
                'status'           => (string) ($request['status'] ?? 'pending'),
                'source'           => (string) ($request['source'] ?? 'service'),
                'actor'            => 'branch',
                'table_code'       => (string) ($request['table_code'] ?? '-'),
                'branch_name'      => (string) ($request['branch_name'] ?? ''),
                'created_at_label' => (string) ($request['created_at_label'] ?? 'Now'),
                'elapsed'          => (string) ($request['created_at_label'] ?? 'Now'),
            ];
        }

        return array_slice($activity, 0, 12);
    }

    protected function topItems(string $brandId): array
    {
        if (!$this->tableExists('orders') || !$this->tableExists('order_items')) {
            return [];
        }

        return $this->all(
            "SELECT
                COALESCE(oi.item_name_snapshot, mi.name, 'Item') AS item_name,
                SUM(COALESCE(oi.quantity, 0)) AS quantity,
                SUM(COALESCE(oi.line_total, 0)) AS line_total
             FROM order_items oi
             INNER JOIN orders o
                ON o.id = oi.order_id
             LEFT JOIN menu_items mi
                ON mi.id = oi.menu_item_id
             WHERE o.brand_id = ?
               AND DATE(COALESCE(o.created_at, o.updated_at)) = CURDATE()
             GROUP BY COALESCE(oi.item_name_snapshot, mi.name, 'Item')
             ORDER BY quantity DESC, line_total DESC
             LIMIT 8",
            [$brandId]
        );
    }

    protected function revenueChart(string $brandId): array
    {
        $day = array_fill(0, 8, 0.0);
        $week = array_fill(0, 7, 0.0);
        $month = array_fill(0, 12, 0.0);

        if (!$this->tableExists('bills')) {
            return ['day' => $day, 'week' => $week, 'month' => $month];
        }

        $dayRows = $this->all(
            "SELECT
                HOUR(COALESCE(paid_at, updated_at, created_at)) AS hour_no,
                SUM(COALESCE(total_amount, 0)) AS total_amount
             FROM bills
             WHERE brand_id = ?
               AND payment_status = 'paid'
               AND DATE(COALESCE(paid_at, updated_at, created_at)) = CURDATE()
             GROUP BY HOUR(COALESCE(paid_at, updated_at, created_at))",
            [$brandId]
        );

        $slots = [9, 11, 13, 15, 17, 19, 21, 23];
        foreach ($dayRows as $row) {
            $hour = (int) ($row['hour_no'] ?? -1);
            $index = array_search($hour, $slots, true);
            if ($index !== false) {
                $day[(int) $index] = (float) ($row['total_amount'] ?? 0);
            }
        }

        $weekRows = $this->all(
            "SELECT
                WEEKDAY(COALESCE(paid_at, updated_at, created_at)) AS weekday_no,
                SUM(COALESCE(total_amount, 0)) AS total_amount
             FROM bills
             WHERE brand_id = ?
               AND payment_status = 'paid'
               AND DATE(COALESCE(paid_at, updated_at, created_at)) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
             GROUP BY WEEKDAY(COALESCE(paid_at, updated_at, created_at))",
            [$brandId]
        );

        foreach ($weekRows as $row) {
            $index = (int) ($row['weekday_no'] ?? -1);
            if ($index >= 0 && $index < 7) {
                $week[$index] = (float) ($row['total_amount'] ?? 0);
            }
        }

        $monthRows = $this->all(
            "SELECT
                MONTH(COALESCE(paid_at, updated_at, created_at)) AS month_no,
                SUM(COALESCE(total_amount, 0)) AS total_amount
             FROM bills
             WHERE brand_id = ?
               AND payment_status = 'paid'
               AND YEAR(COALESCE(paid_at, updated_at, created_at)) = YEAR(CURDATE())
             GROUP BY MONTH(COALESCE(paid_at, updated_at, created_at))",
            [$brandId]
        );

        foreach ($monthRows as $row) {
            $index = (int) ($row['month_no'] ?? 0) - 1;
            if ($index >= 0 && $index < 12) {
                $month[$index] = (float) ($row['total_amount'] ?? 0);
            }
        }

        return ['day' => $day, 'week' => $week, 'month' => $month];
    }

    protected function ordersChart(string $brandId): array
    {
        $day = array_fill(0, 8, 0.0);
        $week = array_fill(0, 7, 0.0);
        $month = array_fill(0, 12, 0.0);

        if (!$this->tableExists('orders')) {
            return ['day' => $day, 'week' => $week, 'month' => $month];
        }

        $dayRows = $this->all(
            "SELECT
                HOUR(COALESCE(created_at, updated_at)) AS hour_no,
                COUNT(*) AS total_rows
             FROM orders
             WHERE brand_id = ?
               AND DATE(COALESCE(created_at, updated_at)) = CURDATE()
             GROUP BY HOUR(COALESCE(created_at, updated_at))",
            [$brandId]
        );

        $slots = [9, 11, 13, 15, 17, 19, 21, 23];
        foreach ($dayRows as $row) {
            $hour = (int) ($row['hour_no'] ?? -1);
            $index = array_search($hour, $slots, true);
            if ($index !== false) {
                $day[(int) $index] = (float) ($row['total_rows'] ?? 0);
            }
        }

        $weekRows = $this->all(
            "SELECT
                WEEKDAY(COALESCE(created_at, updated_at)) AS weekday_no,
                COUNT(*) AS total_rows
             FROM orders
             WHERE brand_id = ?
               AND DATE(COALESCE(created_at, updated_at)) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
             GROUP BY WEEKDAY(COALESCE(created_at, updated_at))",
            [$brandId]
        );

        foreach ($weekRows as $row) {
            $index = (int) ($row['weekday_no'] ?? -1);
            if ($index >= 0 && $index < 7) {
                $week[$index] = (float) ($row['total_rows'] ?? 0);
            }
        }

        $monthRows = $this->all(
            "SELECT
                MONTH(COALESCE(created_at, updated_at)) AS month_no,
                COUNT(*) AS total_rows
             FROM orders
             WHERE brand_id = ?
               AND YEAR(COALESCE(created_at, updated_at)) = YEAR(CURDATE())
             GROUP BY MONTH(COALESCE(created_at, updated_at))",
            [$brandId]
        );

        foreach ($monthRows as $row) {
            $index = (int) ($row['month_no'] ?? 0) - 1;
            if ($index >= 0 && $index < 12) {
                $month[$index] = (float) ($row['total_rows'] ?? 0);
            }
        }

        return ['day' => $day, 'week' => $week, 'month' => $month];
    }

    protected function categoryRevenueChart(string $brandId): array
    {
        if (!$this->tableExists('orders') || !$this->tableExists('order_items') || !$this->tableExists('menu_items')) {
            return [];
        }

        $summaryRows = $this->all(
            "SELECT
                COALESCE(mc.id, '') AS category_id,
                COALESCE(mc.name, 'General') AS category_name,
                COALESCE(SUM(oi.line_total), 0) AS revenue
             FROM order_items oi
             INNER JOIN orders o
                ON o.id = oi.order_id
             LEFT JOIN menu_items mi
                ON mi.id = oi.menu_item_id
             LEFT JOIN menu_categories mc
                ON mc.id = mi.category_id
             WHERE o.brand_id = ?
               AND DATE(COALESCE(o.created_at, o.updated_at)) = CURDATE()
             GROUP BY COALESCE(mc.id, ''), COALESCE(mc.name, 'General')
             ORDER BY revenue DESC, category_name ASC",
            [$brandId]
        );

        $slotLabels = ['09 AM', '11 AM', '01 PM', '03 PM', '05 PM', '07 PM', '09 PM', '11 PM'];
        $slotHours  = [9, 11, 13, 15, 17, 19, 21, 23];

        $seriesRows = $this->all(
            "SELECT
                COALESCE(mc.id, '') AS category_id,
                HOUR(COALESCE(o.created_at, o.updated_at)) AS hour_no,
                COALESCE(SUM(oi.line_total), 0) AS revenue
             FROM order_items oi
             INNER JOIN orders o
                ON o.id = oi.order_id
             LEFT JOIN menu_items mi
                ON mi.id = oi.menu_item_id
             LEFT JOIN menu_categories mc
                ON mc.id = mi.category_id
             WHERE o.brand_id = ?
               AND DATE(COALESCE(o.created_at, o.updated_at)) = CURDATE()
             GROUP BY COALESCE(mc.id, ''), HOUR(COALESCE(o.created_at, o.updated_at))",
            [$brandId]
        );

        $seriesMap = [];
        foreach ($seriesRows as $row) {
            $categoryId = (string) ($row['category_id'] ?? '');
            if (!isset($seriesMap[$categoryId])) {
                $seriesMap[$categoryId] = array_fill(0, count($slotHours), 0.0);
            }

            $hour = (int) ($row['hour_no'] ?? -1);
            $index = array_search($hour, $slotHours, true);
            if ($index !== false) {
                $seriesMap[$categoryId][(int) $index] = (float) ($row['revenue'] ?? 0);
            }
        }

        return array_map(static function (array $row) use ($seriesMap, $slotLabels): array {
            $categoryId = (string) ($row['category_id'] ?? '');

            return [
                'category_id'   => $categoryId,
                'id'            => $categoryId,
                'category_name' => (string) ($row['category_name'] ?? 'General'),
                'name'          => (string) ($row['category_name'] ?? 'General'),
                'value'         => (float) ($row['revenue'] ?? 0),
                'revenue'       => (float) ($row['revenue'] ?? 0),
                'amount'        => (float) ($row['revenue'] ?? 0),
                'labels'        => $slotLabels,
                'values'        => $seriesMap[$categoryId] ?? array_fill(0, count($slotLabels), 0.0),
            ];
        }, $summaryRows);
    }

    protected function todayOrdersCount(string $brandId, string $branchId = ''): int
    {
        if (!$this->tableExists('orders')) {
            return 0;
        }

        $where = "brand_id = ? AND DATE(COALESCE(created_at, updated_at)) = CURDATE()";
        $params = [$brandId];

        if ($branchId !== '') {
            $where .= " AND branch_id = ?";
            $params[] = $branchId;
        }

        return (int) $this->scalar(
            "SELECT COUNT(*) FROM orders WHERE {$where}",
            $params,
            0
        );
    }

    protected function todayRevenueTotal(string $brandId, string $branchId = ''): float
    {
        if (!$this->tableExists('bills')) {
            return 0.0;
        }

        $where = "brand_id = ?
                  AND payment_status = 'paid'
                  AND DATE(COALESCE(paid_at, updated_at, created_at)) = CURDATE()";
        $params = [$brandId];

        if ($branchId !== '') {
            $where .= " AND branch_id = ?";
            $params[] = $branchId;
        }

        return (float) $this->scalar(
            "SELECT COALESCE(SUM(COALESCE(total_amount, 0)), 0) FROM bills WHERE {$where}",
            $params,
            0
        );
    }

    protected function branchTableCount(string $branchId): int
    {
        $tableName = $this->firstExistingTable(['tables', 'restaurant_tables']);
        if ($tableName === null) {
            return 0;
        }

        return (int) $this->scalar(
            "SELECT COUNT(*) FROM {$tableName} WHERE branch_id = ?",
            [$branchId],
            0
        );
    }

    protected function branchRoleCount(string $brandId, string $branchId, string $role): int
    {
        if (!$this->tableExists('users') || !$this->tableExists('user_access')) {
            return 0;
        }

        return (int) $this->scalar(
            "SELECT COUNT(*)
             FROM users u
             INNER JOIN user_access ua
                ON ua.user_id = u.id
             WHERE u.brand_id = ?
               AND ua.branch_id = ?
               AND ua.role_at_branch = ?",
            [$brandId, $branchId, $role],
            0
        );
    }

    protected function branchStaffCount(string $brandId, string $branchId): int
    {
        if (!$this->tableExists('users') || !$this->tableExists('user_access')) {
            return 0;
        }

        return (int) $this->scalar(
            "SELECT COUNT(*)
             FROM users u
             INNER JOIN user_access ua
                ON ua.user_id = u.id
             WHERE u.brand_id = ?
               AND ua.branch_id = ?",
            [$brandId, $branchId],
            0
        );
    }

    public function getStaffDetail(string $brandId, string $userId): ?array
    {
        foreach ($this->staffList($brandId) as $staff) {
            if ((string) ($staff['id'] ?? '') === $userId) {
                return $staff;
            }
        }

        return null;
    }

    public function getBranchDetail(string $brandId, string $branchId): ?array
    {
        $branch = $this->getBranch($brandId, $branchId);
        if (!$branch) {
            return null;
        }

        return [
            'branch'           => $branch,
            'today_revenue'    => $this->todayRevenueTotal($brandId, $branchId),
            'today_orders'     => $this->todayOrdersCount($brandId, $branchId),
            'tables_count'     => $this->branchTableCount($branchId),
            'manager_count'    => $this->branchRoleCount($brandId, $branchId, 'manager'),
            'staff_count'      => $this->branchStaffCount($brandId, $branchId),
            'service_requests' => $this->serviceRequests($brandId, $branchId),
            'live_orders'      => $this->liveOrders($brandId, $branchId),
            'open_bills'       => $this->openBills($brandId, $branchId),
        ];
    }

    public function getMenuItemDetail(string $brandId, string $menuId, string $branchId = ''): ?array
    {
        foreach ($this->menuItems($brandId, $branchId) as $item) {
            if ((string) ($item['id'] ?? '') === $menuId) {
                return $item;
            }
        }

        return null;
    }

    public function getOrderDetail(string $brandId, string $orderId): ?array
    {
        foreach ($this->liveOrders($brandId) as $order) {
            if ((string) ($order['order_id'] ?? '') === $orderId) {
                return $order;
            }
        }

        return null;
    }

    public function getBillDetail(string $brandId, string $billId): ?array
    {
        foreach ($this->openBills($brandId) as $bill) {
            if ((string) ($bill['bill_id'] ?? '') === $billId) {
                return $bill;
            }
        }

        if (!$this->tableExists('bills')) {
            return null;
        }

        $row = $this->one(
            "SELECT * FROM bills WHERE id = ? AND brand_id = ? LIMIT 1",
            [$billId, $brandId]
        );

        return $row ?: null;
    }

    public function getRequestDetail(string $brandId, string $requestId): ?array
    {
        foreach ($this->serviceRequests($brandId) as $request) {
            if ((string) ($request['request_id'] ?? '') === $requestId) {
                return $request;
            }
        }

        return null;
    }

    public function saveBrandProfile(string $brandId, array $payload, string $actorUserId = ''): bool
    {
        if (!$this->tableExists('brands')) {
            return false;
        }

        $existing = $this->getBrand($brandId);
        if (!$existing) {
            return false;
        }

        $updates = [];
        $params = [];

        $map = [
            'brand_name' => 'name',
            'legal_name' => 'legal_name',
            'mail_id'    => 'mail_id',
            'mobile'     => 'mobile',
            'currency'   => 'currency',
            'timezone'   => 'timezone',
            'logo'       => 'logo',
            'favicon'    => 'favicon',
            'status'     => 'status',
        ];

        foreach ($map as $payloadKey => $column) {
            if (!array_key_exists($payloadKey, $payload) || !$this->columnExists('brands', $column)) {
                continue;
            }

            $value = $payload[$payloadKey];
            if ($payloadKey === 'currency') {
                $value = strtoupper(trim((string) $value));
            } elseif ($payloadKey === 'mail_id') {
                $value = strtolower(trim((string) $value));
            } else {
                $value = trim((string) $value);
            }

            $updates[] = "{$column} = ?";
            $params[] = $value;
        }

        if ($this->columnExists('brands', 'updated_at')) {
            $updates[] = "updated_at = ?";
            $params[] = $this->now();
        }

        if (empty($updates)) {
            return true;
        }

        $params[] = $brandId;

        $ok = $this->execute(
            "UPDATE brands
             SET " . implode(', ', $updates) . "
             WHERE id = ?",
            $params
        );

        if ($ok) {
            $this->auditLog($brandId, null, $actorUserId, 'brand', $brandId, 'update_profile', $existing, $payload);
            $this->pushRealtimeEvent($brandId, $this->defaultBranchId, 'brand.profile.updated', 'brand', $brandId, ['brand_id' => $brandId]);
        }

        return $ok;
    }

    public function saveBrandSettings(string $brandId, array $payload, string $actorUserId = ''): bool
    {
        return $this->saveBrandProfile($brandId, $payload, $actorUserId);
    }

    public function saveBranch(string $brandId, array $payload, string $actorUserId = ''): bool
    {
        if (!$this->tableExists('branches')) {
            return false;
        }

        $branchId = trim((string) ($payload['branch_id'] ?? ''));
        $isUpdate = $branchId !== '' && $this->branchExists($brandId, $branchId);
        $now = $this->now();

        $data = [
            'brand_id'   => $brandId,
            'name'       => trim((string) ($payload['name'] ?? '')),
            'code'       => trim((string) ($payload['code'] ?? '')),
            'address'    => trim((string) ($payload['address'] ?? '')),
            'city'       => trim((string) ($payload['city'] ?? '')),
            'state'      => trim((string) ($payload['state'] ?? '')),
            'pincode'    => trim((string) ($payload['pincode'] ?? '')),
            'phone'      => trim((string) ($payload['phone'] ?? '')),
            'email'      => strtolower(trim((string) ($payload['email'] ?? ''))),
            'status'     => trim((string) ($payload['status'] ?? 'active')),
            'opens_at'   => trim((string) ($payload['opens_at'] ?? '')),
            'closes_at'  => trim((string) ($payload['closes_at'] ?? '')),
            'tax_number' => trim((string) ($payload['tax_number'] ?? '')),
        ];

        $this->begin();

        try {
            if ($isUpdate) {
                $existing = $this->getBranch($brandId, $branchId) ?? [];
                $updates = [];
                $params = [];

                foreach ($data as $column => $value) {
                    if (!$this->columnExists('branches', $column)) {
                        continue;
                    }

                    $updates[] = "{$column} = ?";
                    $params[] = $value !== '' ? $value : ($column === 'status' ? 'active' : null);
                }

                if ($this->columnExists('branches', 'updated_at')) {
                    $updates[] = "updated_at = ?";
                    $params[] = $now;
                }

                $params[] = $branchId;
                $params[] = $brandId;

                $ok = $this->execute(
                    "UPDATE branches
                     SET " . implode(', ', $updates) . "
                     WHERE id = ? AND brand_id = ?",
                    $params
                );

                if (!$ok) {
                    throw new RuntimeException('Unable to update branch.');
                }

                $this->auditLog($brandId, $branchId, $actorUserId, 'branch', $branchId, 'update', $existing, $payload);
                $this->pushRealtimeEvent($brandId, $branchId, 'brand.branch.updated', 'branch', $branchId, ['branch_id' => $branchId]);
            } else {
                $branchId = $this->uuid();

                $columns = ['id'];
                $params = [$branchId];

                foreach ($data as $column => $value) {
                    if (!$this->columnExists('branches', $column)) {
                        continue;
                    }

                    $columns[] = $column;
                    $params[] = $value !== '' ? $value : ($column === 'status' ? 'active' : null);
                }

                if ($this->columnExists('branches', 'created_at')) {
                    $columns[] = 'created_at';
                    $params[] = $now;
                }

                if ($this->columnExists('branches', 'updated_at')) {
                    $columns[] = 'updated_at';
                    $params[] = $now;
                }

                $placeholders = implode(', ', array_fill(0, count($columns), '?'));

                $ok = $this->execute(
                    "INSERT INTO branches (" . implode(', ', $columns) . ")
                     VALUES ({$placeholders})",
                    $params
                );

                if (!$ok) {
                    throw new RuntimeException('Unable to create branch.');
                }

                $this->auditLog($brandId, $branchId, $actorUserId, 'branch', $branchId, 'create', null, $payload);
                $this->pushRealtimeEvent($brandId, $branchId, 'brand.branch.created', 'branch', $branchId, ['branch_id' => $branchId]);
            }

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestBrandModel::saveBranch failed: ' . $e->getMessage());
            return false;
        }
    }

    public function toggleBranchStatus(string $brandId, string $branchId, string $actorUserId = ''): bool
    {
        $existing = $this->getBranch($brandId, $branchId);
        if (!$existing || !$this->tableExists('branches') || !$this->columnExists('branches', 'status')) {
            return false;
        }

        $current = strtolower((string) ($existing['status'] ?? 'active'));
        $newStatus = $current === 'active' ? 'inactive' : 'active';
        $now = $this->now();

        $params = [$newStatus];
        $sql = "UPDATE branches SET status = ?";

        if ($this->columnExists('branches', 'updated_at')) {
            $sql .= ", updated_at = ?";
            $params[] = $now;
        }

        $sql .= " WHERE id = ? AND brand_id = ?";
        $params[] = $branchId;
        $params[] = $brandId;

        $ok = $this->execute($sql, $params);

        if ($ok) {
            $this->auditLog($brandId, $branchId, $actorUserId, 'branch', $branchId, 'toggle_status', $existing, ['status' => $newStatus]);
            $this->pushRealtimeEvent($brandId, $branchId, 'brand.branch.status_changed', 'branch', $branchId, ['status' => $newStatus]);
        }

        return $ok;
    }

    public function deleteBranch(string $brandId, string $branchId, string $actorUserId = ''): bool
    {
        $existing = $this->getBranch($brandId, $branchId);
        if (!$existing) {
            return false;
        }

        $this->begin();

        try {
            $ok = $this->execute(
                "DELETE FROM branches
                 WHERE id = ?
                   AND brand_id = ?",
                [$branchId, $brandId]
            );

            if (!$ok) {
                throw new RuntimeException('Unable to delete branch.');
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'branch', $branchId, 'delete', $existing, null);
            $this->pushRealtimeEvent($brandId, $branchId, 'brand.branch.deleted', 'branch', $branchId, ['branch_id' => $branchId]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestBrandModel::deleteBranch failed: ' . $e->getMessage());
            return false;
        }
    }

    public function saveStaff(string $brandId, array $payload, string $actorUserId = ''): bool
    {
        if (!$this->tableExists('users') || !$this->tableExists('user_access')) {
            return false;
        }

        $targetUserId = trim((string) ($payload['target_user_id'] ?? $payload['user_id'] ?? ''));
        $branchId = trim((string) ($payload['branch_id'] ?? ''));
        $role = $this->safeRole((string) ($payload['role_at_branch'] ?? $payload['role'] ?? 'staff'));
        $isActive = $this->boolToInt($payload['is_active'] ?? '1', 1);
        $status = $isActive === 1 ? 'active' : 'inactive';
        $accessStatus = $status;
        $assignedTableCodes = $this->normalizeCsvList($payload['assigned_table_codes'] ?? []);
        $permissions = is_array($payload['permissions'] ?? null) ? $payload['permissions'] : [];
        $shiftStart = trim((string) ($payload['shift_start_time'] ?? ''));
        $shiftEnd = trim((string) ($payload['shift_end_time'] ?? ''));
        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        $now = $this->now();

        if ($branchId === '' || !$this->branchExists($brandId, $branchId) || $email === '') {
            return false;
        }

        if ($targetUserId === '' && $this->emailExists($brandId, $email)) {
            return false;
        }

        if ($targetUserId !== '' && $this->emailExists($brandId, $email, $targetUserId)) {
            return false;
        }

        $this->begin();

        try {
            $existing = $targetUserId !== '' ? $this->getStaffDetail($brandId, $targetUserId) : null;

            if ($targetUserId !== '') {
                $ok1 = $this->execute(
                    "UPDATE users
                     SET full_name = ?,
                         email = ?,
                         phone = ?,
                         employee_code = ?,
                         role_type = ?,
                         status = ?,
                         is_active = ?,
                         default_branch_id = ?,
                         updated_at = ?
                     WHERE id = ?
                       AND brand_id = ?",
                    [
                        trim((string) ($payload['full_name'] ?? '')),
                        $email,
                        trim((string) ($payload['phone'] ?? '')),
                        trim((string) ($payload['employee_code'] ?? '')),
                        $role,
                        $status,
                        $isActive,
                        $branchId,
                        $now,
                        $targetUserId,
                        $brandId,
                    ]
                );
            } else {
                $targetUserId = $this->uuid();

                $ok1 = $this->execute(
                    "INSERT INTO users (
                        id, brand_id, default_branch_id, full_name, email, phone, employee_code,
                        role_type, status, is_active, created_at, updated_at
                     ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $targetUserId,
                        $brandId,
                        $branchId,
                        trim((string) ($payload['full_name'] ?? '')),
                        $email,
                        trim((string) ($payload['phone'] ?? '')),
                        trim((string) ($payload['employee_code'] ?? '')),
                        $role,
                        $status,
                        $isActive,
                        $now,
                        $now,
                    ]
                );
            }

            if (!$ok1) {
                throw new RuntimeException('Unable to save user.');
            }

            $accessExists = $this->one(
                "SELECT id
                 FROM user_access
                 WHERE user_id = ?
                   AND branch_id = ?
                 LIMIT 1",
                [$targetUserId, $branchId]
            );

            if ($accessExists) {
                $ok2 = $this->execute(
                    "UPDATE user_access
                     SET role_at_branch = ?,
                         assigned_table_codes = ?,
                         permissions_json = ?,
                         shift_start_time = ?,
                         shift_end_time = ?,
                         access_status = ?,
                         updated_at = ?
                     WHERE user_id = ?
                       AND branch_id = ?",
                    [
                        $role,
                        implode(',', $assignedTableCodes),
                        $this->jsonEncode($permissions),
                        $shiftStart !== '' ? $shiftStart : null,
                        $shiftEnd !== '' ? $shiftEnd : null,
                        $accessStatus,
                        $now,
                        $targetUserId,
                        $branchId,
                    ]
                );
            } else {
                $ok2 = $this->execute(
                    "INSERT INTO user_access (
                        id, user_id, branch_id, role_at_branch, assigned_table_codes, permissions_json,
                        shift_start_time, shift_end_time, access_status, created_at, updated_at
                     ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $this->uuid(),
                        $targetUserId,
                        $branchId,
                        $role,
                        implode(',', $assignedTableCodes),
                        $this->jsonEncode($permissions),
                        $shiftStart !== '' ? $shiftStart : null,
                        $shiftEnd !== '' ? $shiftEnd : null,
                        $accessStatus,
                        $now,
                        $now,
                    ]
                );
            }

            if (!$ok2) {
                throw new RuntimeException('Unable to update user access.');
            }

            $action = $existing ? 'update' : 'create';
            $this->auditLog($brandId, $branchId, $actorUserId, 'staff', $targetUserId, $action, $existing, $payload);
            $this->pushRealtimeEvent($brandId, $branchId, 'brand.staff.' . ($existing ? 'updated' : 'created'), 'staff', $targetUserId, ['user_id' => $targetUserId]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestBrandModel::saveStaff failed: ' . $e->getMessage());
            return false;
        }
    }

    public function toggleStaffStatus(string $brandId, string $targetUserId, string $actorUserId = ''): bool
    {
        $existing = $this->getStaffDetail($brandId, $targetUserId);
        if (!$existing) {
            return false;
        }

        $branchId = (string) ($existing['branch_id'] ?? '');
        $newIsActive = ((string) ($existing['is_active'] ?? '1') === '1') ? 0 : 1;
        $newStatus = $newIsActive === 1 ? 'active' : 'inactive';
        $now = $this->now();

        $this->begin();

        try {
            $ok1 = $this->execute(
                "UPDATE users
                 SET is_active = ?, status = ?, updated_at = ?
                 WHERE id = ? AND brand_id = ?",
                [$newIsActive, $newStatus, $now, $targetUserId, $brandId]
            );

            $ok2 = $this->execute(
                "UPDATE user_access
                 SET access_status = ?, updated_at = ?
                 WHERE user_id = ?",
                [$newStatus, $now, $targetUserId]
            );

            if (!$ok1 || !$ok2) {
                throw new RuntimeException('Unable to toggle staff status.');
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'staff', $targetUserId, 'toggle_status', $existing, ['is_active' => $newIsActive]);
            $this->pushRealtimeEvent($brandId, $branchId, 'brand.staff.status_changed', 'staff', $targetUserId, ['user_id' => $targetUserId, 'is_active' => $newIsActive]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestBrandModel::toggleStaffStatus failed: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteStaff(string $brandId, string $targetUserId, string $actorUserId = ''): bool
    {
        $existing = $this->getStaffDetail($brandId, $targetUserId);
        if (!$existing) {
            return false;
        }

        $branchId = (string) ($existing['branch_id'] ?? '');

        $this->begin();

        try {
            $ok1 = $this->execute(
                "DELETE FROM user_access WHERE user_id = ?",
                [$targetUserId]
            );

            $ok2 = $this->execute(
                "DELETE FROM users WHERE id = ? AND brand_id = ?",
                [$targetUserId, $brandId]
            );

            if (!$ok1 || !$ok2) {
                throw new RuntimeException('Unable to delete staff.');
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'staff', $targetUserId, 'delete', $existing, null);
            $this->pushRealtimeEvent($brandId, $branchId, 'brand.staff.deleted', 'staff', $targetUserId, ['user_id' => $targetUserId]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestBrandModel::deleteStaff failed: ' . $e->getMessage());
            return false;
        }
    }

    public function saveManagerAssignment(string $brandId, string $branchId, string $targetUserId, string $actorUserId = ''): bool
    {
        if (!$this->branchExists($brandId, $branchId) || !$this->tableExists('user_access')) {
            return false;
        }

        $existing = $this->getStaffDetail($brandId, $targetUserId);
        if (!$existing) {
            return false;
        }

        $now = $this->now();

        $this->begin();

        try {
            $ok1 = $this->execute(
                "UPDATE users
                 SET role_type = 'manager', default_branch_id = ?, updated_at = ?
                 WHERE id = ? AND brand_id = ?",
                [$branchId, $now, $targetUserId, $brandId]
            );

            $accessExists = $this->one(
                "SELECT id
                 FROM user_access
                 WHERE user_id = ? AND branch_id = ?
                 LIMIT 1",
                [$targetUserId, $branchId]
            );

            if ($accessExists) {
                $ok2 = $this->execute(
                    "UPDATE user_access
                     SET role_at_branch = 'manager', access_status = 'active', updated_at = ?
                     WHERE user_id = ? AND branch_id = ?",
                    [$now, $targetUserId, $branchId]
                );
            } else {
                $ok2 = $this->execute(
                    "INSERT INTO user_access (
                        id, user_id, branch_id, role_at_branch, assigned_table_codes, permissions_json,
                        shift_start_time, shift_end_time, access_status, created_at, updated_at
                     ) VALUES (?, ?, ?, 'manager', '', ?, NULL, NULL, 'active', ?, ?)",
                    [$this->uuid(), $targetUserId, $branchId, $this->jsonEncode([]), $now, $now]
                );
            }

            if (!$ok1 || !$ok2) {
                throw new RuntimeException('Unable to save manager assignment.');
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'staff', $targetUserId, 'assign_manager', $existing, ['branch_id' => $branchId, 'role_at_branch' => 'manager']);
            $this->pushRealtimeEvent($brandId, $branchId, 'brand.manager.assigned', 'staff', $targetUserId, ['user_id' => $targetUserId, 'branch_id' => $branchId]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestBrandModel::saveManagerAssignment failed: ' . $e->getMessage());
            return false;
        }
    }

    public function saveMenuItem(string $brandId, array $payload, string $actorUserId = ''): bool
    {
        if (!$this->tableExists('menu_items')) {
            return false;
        }

        $branchId = trim((string) ($payload['branch_id'] ?? ''));
        if ($branchId === '' || !$this->branchExists($brandId, $branchId)) {
            return false;
        }

        $menuId = trim((string) ($payload['menu_id'] ?? ''));
        $isUpdate = $menuId !== '' && $this->getMenuItemDetail($brandId, $menuId, $branchId) !== null;

        $categoryId = $this->resolveCategoryId($brandId, $branchId, $payload);
        $itemType = $this->safeItemType((string) ($payload['item_type'] ?? 'food'));
        $stockStatus = $this->safeStockStatus((string) ($payload['stock_status'] ?? 'in_stock'));
        $isAvailable = $this->boolToInt($payload['is_available'] ?? '1', 1);
        $now = $this->now();

        $this->begin();

        try {
            $existing = $isUpdate ? $this->getMenuItemDetail($brandId, $menuId, $branchId) : null;

            if ($isUpdate) {
                $ok1 = $this->execute(
                    "UPDATE menu_items
                     SET category_id = ?,
                         default_kitchen_station_id = ?,
                         name = ?,
                         description = ?,
                         image_url = ?,
                         video_url = ?,
                         video_type = ?,
                         item_type = ?,
                         sku = ?,
                         base_price = ?,
                         tax_rate = ?,
                         preparation_time_mins = ?,
                         is_veg = ?,
                         is_vegan = ?,
                         is_spicy = ?,
                         is_available = ?,
                         updated_at = ?
                     WHERE id = ?
                       AND brand_id = ?",
                    [
                        $categoryId !== '' ? $categoryId : null,
                        trim((string) ($payload['default_kitchen_station_id'] ?? '')) ?: null,
                        trim((string) ($payload['name'] ?? '')),
                        trim((string) ($payload['description'] ?? '')),
                        trim((string) ($payload['image_url'] ?? '')),
                        trim((string) ($payload['video_url'] ?? '')),
                        trim((string) ($payload['video_type'] ?? '')),
                        $itemType,
                        trim((string) ($payload['sku'] ?? '')),
                        (float) ($payload['base_price'] ?? 0),
                        (float) ($payload['tax_rate'] ?? 0),
                        (int) ($payload['preparation_time_mins'] ?? 0),
                        $this->boolToInt($payload['is_veg'] ?? '0', 0),
                        $this->boolToInt($payload['is_vegan'] ?? '0', 0),
                        $this->boolToInt($payload['is_spicy'] ?? '0', 0),
                        $isAvailable,
                        $now,
                        $menuId,
                        $brandId,
                    ]
                );
            } else {
                $menuId = $this->uuid();

                $ok1 = $this->execute(
                    "INSERT INTO menu_items (
                        id, brand_id, category_id, default_kitchen_station_id, name, description, image_url, video_url, video_type,
                        item_type, sku, base_price, tax_rate, preparation_time_mins, is_veg, is_vegan, is_spicy, is_available,
                        created_at, updated_at
                     ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $menuId,
                        $brandId,
                        $categoryId !== '' ? $categoryId : null,
                        trim((string) ($payload['default_kitchen_station_id'] ?? '')) ?: null,
                        trim((string) ($payload['name'] ?? '')),
                        trim((string) ($payload['description'] ?? '')),
                        trim((string) ($payload['image_url'] ?? '')),
                        trim((string) ($payload['video_url'] ?? '')),
                        trim((string) ($payload['video_type'] ?? '')),
                        $itemType,
                        trim((string) ($payload['sku'] ?? '')),
                        (float) ($payload['base_price'] ?? 0),
                        (float) ($payload['tax_rate'] ?? 0),
                        (int) ($payload['preparation_time_mins'] ?? 0),
                        $this->boolToInt($payload['is_veg'] ?? '0', 0),
                        $this->boolToInt($payload['is_vegan'] ?? '0', 0),
                        $this->boolToInt($payload['is_spicy'] ?? '0', 0),
                        $isAvailable,
                        $now,
                        $now,
                    ]
                );
            }

            if (!$ok1) {
                throw new RuntimeException('Unable to save menu item.');
            }

            $ok2 = true;

            if ($this->tableExists('branch_menu_items')) {
                $branchLink = $this->one(
                    "SELECT id
                     FROM branch_menu_items
                     WHERE branch_id = ?
                       AND menu_item_id = ?
                     LIMIT 1",
                    [$branchId, $menuId]
                );

                if ($branchLink) {
                    $ok2 = $this->execute(
                        "UPDATE branch_menu_items
                         SET selling_price = ?,
                             is_available = ?,
                             available_from = ?,
                             available_to = ?,
                             stock_status = ?,
                             updated_at = ?
                         WHERE branch_id = ?
                           AND menu_item_id = ?",
                        [
                            (float) ($payload['selling_price'] ?? $payload['base_price'] ?? 0),
                            $isAvailable,
                            trim((string) ($payload['available_from'] ?? '')) ?: null,
                            trim((string) ($payload['available_to'] ?? '')) ?: null,
                            $stockStatus,
                            $now,
                            $branchId,
                            $menuId,
                        ]
                    );
                } else {
                    $ok2 = $this->execute(
                        "INSERT INTO branch_menu_items (
                            id, branch_id, menu_item_id, selling_price, is_available,
                            available_from, available_to, stock_status, created_at, updated_at
                         ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                        [
                            $this->uuid(),
                            $branchId,
                            $menuId,
                            (float) ($payload['selling_price'] ?? $payload['base_price'] ?? 0),
                            $isAvailable,
                            trim((string) ($payload['available_from'] ?? '')) ?: null,
                            trim((string) ($payload['available_to'] ?? '')) ?: null,
                            $stockStatus,
                            $now,
                            $now,
                        ]
                    );
                }
            }

            if (!$ok2) {
                throw new RuntimeException('Unable to save branch menu item.');
            }

            $action = $existing ? 'update' : 'create';
            $this->auditLog($brandId, $branchId, $actorUserId, 'menu_item', $menuId, $action, $existing, $payload);
            $this->pushRealtimeEvent($brandId, $branchId, 'brand.menu_item.' . ($existing ? 'updated' : 'created'), 'menu_item', $menuId, ['menu_id' => $menuId]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestBrandModel::saveMenuItem failed: ' . $e->getMessage());
            return false;
        }
    }

    public function toggleMenuItemStatus(string $brandId, string $branchId, string $menuId, string $actorUserId = ''): bool
    {
        $existing = $this->getMenuItemDetail($brandId, $menuId, $branchId);
        if (!$existing) {
            return false;
        }

        $newValue = ((string) ($existing['is_available'] ?? '1') === '1') ? 0 : 1;
        $now = $this->now();

        $this->begin();

        try {
            $ok1 = $this->execute(
                "UPDATE menu_items
                 SET is_available = ?, updated_at = ?
                 WHERE id = ? AND brand_id = ?",
                [$newValue, $now, $menuId, $brandId]
            );

            $ok2 = true;
            if ($this->tableExists('branch_menu_items')) {
                $ok2 = $this->execute(
                    "UPDATE branch_menu_items
                     SET is_available = ?, updated_at = ?
                     WHERE menu_item_id = ?
                       AND branch_id = ?",
                    [$newValue, $now, $menuId, $branchId]
                );
            }

            if (!$ok1 || !$ok2) {
                throw new RuntimeException('Unable to toggle menu item.');
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'menu_item', $menuId, 'toggle_status', $existing, ['is_available' => $newValue]);
            $this->pushRealtimeEvent($brandId, $branchId, 'brand.menu_item.status_changed', 'menu_item', $menuId, ['menu_id' => $menuId, 'is_available' => $newValue]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestBrandModel::toggleMenuItemStatus failed: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteMenuItem(string $brandId, string $branchId, string $menuId, string $actorUserId = ''): bool
    {
        $existing = $this->getMenuItemDetail($brandId, $menuId, $branchId);
        if (!$existing) {
            return false;
        }

        $this->begin();

        try {
            if ($this->tableExists('branch_menu_items')) {
                $this->execute(
                    "DELETE FROM branch_menu_items
                     WHERE menu_item_id = ?",
                    [$menuId]
                );
            }

            $ok = $this->execute(
                "DELETE FROM menu_items
                 WHERE id = ? AND brand_id = ?",
                [$menuId, $brandId]
            );

            if (!$ok) {
                throw new RuntimeException('Unable to delete menu item.');
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'menu_item', $menuId, 'delete', $existing, null);
            $this->pushRealtimeEvent($brandId, $branchId, 'brand.menu_item.deleted', 'menu_item', $menuId, ['menu_id' => $menuId]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestBrandModel::deleteMenuItem failed: ' . $e->getMessage());
            return false;
        }
    }

    public function saveTable(string $brandId, array $payload, string $actorUserId = ''): bool
    {
        $tableName = $this->firstExistingTable(['tables', 'restaurant_tables']);
        if ($tableName === null) {
            return false;
        }

        $branchId = trim((string) ($payload['branch_id'] ?? ''));
        if ($branchId === '' || !$this->branchExists($brandId, $branchId)) {
            return false;
        }

        $tableId = trim((string) ($payload['table_id'] ?? ''));
        $isUpdate = false;

        if ($tableId !== '') {
            $row = $this->one(
                "SELECT id
                 FROM {$tableName}
                 WHERE id = ?
                   AND brand_id = ?
                   AND branch_id = ?
                 LIMIT 1",
                [$tableId, $brandId, $branchId]
            );

            $isUpdate = $row !== null;
        }

        $tableCode = trim((string) ($payload['table_code'] ?? ''));
        $capacity = (int) ($payload['capacity'] ?? 0);
        $status = $this->safeTableStatus((string) ($payload['status'] ?? 'available'));
        $label = trim((string) ($payload['label'] ?? ''));
        $zone = trim((string) ($payload['zone'] ?? ''));
        $now = $this->now();

        $this->begin();

        try {
            if ($isUpdate) {
                $existing = $this->one(
                    "SELECT * FROM {$tableName}
                     WHERE id = ? LIMIT 1",
                    [$tableId]
                ) ?? [];

                $updates = [];
                $params = [];

                if ($this->columnExists($tableName, 'table_code')) {
                    $updates[] = 'table_code = ?';
                    $params[] = $tableCode;
                }

                if ($this->columnExists($tableName, 'table_number')) {
                    $updates[] = 'table_number = ?';
                    $params[] = $tableCode;
                }

                if ($this->columnExists($tableName, 'capacity')) {
                    $updates[] = 'capacity = ?';
                    $params[] = $capacity;
                }

                if ($this->columnExists($tableName, 'status')) {
                    $updates[] = 'status = ?';
                    $params[] = $status;
                }

                if ($this->columnExists($tableName, 'label')) {
                    $updates[] = 'label = ?';
                    $params[] = $label;
                }

                if ($this->columnExists($tableName, 'zone')) {
                    $updates[] = 'zone = ?';
                    $params[] = $zone;
                }

                if ($this->columnExists($tableName, 'updated_at')) {
                    $updates[] = 'updated_at = ?';
                    $params[] = $now;
                }

                if ($this->columnExists($tableName, 'status_updated_at')) {
                    $updates[] = 'status_updated_at = ?';
                    $params[] = $now;
                }

                $params[] = $tableId;
                $params[] = $brandId;
                $params[] = $branchId;

                $ok = $this->execute(
                    "UPDATE {$tableName}
                     SET " . implode(', ', $updates) . "
                     WHERE id = ?
                       AND brand_id = ?
                       AND branch_id = ?",
                    $params
                );

                if (!$ok) {
                    throw new RuntimeException('Unable to update table.');
                }

                $this->auditLog($brandId, $branchId, $actorUserId, 'table', $tableId, 'update', $existing, $payload);
                $this->pushRealtimeEvent($brandId, $branchId, 'brand.table.updated', 'table', $tableId, ['table_id' => $tableId]);
            } else {
                $tableId = $this->uuid();
                $columns = ['id'];
                $params = [$tableId];

                $values = [
                    'brand_id' => $brandId,
                    'branch_id' => $branchId,
                    'table_code' => $tableCode,
                    'table_number' => $tableCode,
                    'capacity' => $capacity,
                    'status' => $status,
                    'label' => $label,
                    'zone' => $zone,
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'status_updated_at' => $now,
                ];

                foreach ($values as $column => $value) {
                    if ($this->columnExists($tableName, $column)) {
                        $columns[] = $column;
                        $params[] = $value;
                    }
                }

                $placeholders = implode(', ', array_fill(0, count($columns), '?'));

                $ok = $this->execute(
                    "INSERT INTO {$tableName} (" . implode(', ', $columns) . ")
                     VALUES ({$placeholders})",
                    $params
                );

                if (!$ok) {
                    throw new RuntimeException('Unable to create table.');
                }

                $this->auditLog($brandId, $branchId, $actorUserId, 'table', $tableId, 'create', null, $payload);
                $this->pushRealtimeEvent($brandId, $branchId, 'brand.table.created', 'table', $tableId, ['table_id' => $tableId]);
            }

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestBrandModel::saveTable failed: ' . $e->getMessage());
            return false;
        }
    }

    public function updateOrderStatus(string $brandId, string $orderId, string $status, string $note = '', string $actorUserId = ''): bool
    {
        if (!$this->tableExists('orders')) {
            return false;
        }

        $existing = $this->one(
            "SELECT *
             FROM orders
             WHERE id = ?
               AND brand_id = ?
             LIMIT 1",
            [$orderId, $brandId]
        );

        if (!$existing) {
            return false;
        }

        $branchId = (string) ($existing['branch_id'] ?? '');
        $safeStatus = $this->safeOrderStatus($status);
        $now = $this->now();

        $this->begin();

        try {
            $ok = $this->execute(
                "UPDATE orders
                 SET status = ?,
                     notes = CASE
                         WHEN ? <> '' THEN ?
                         ELSE notes
                     END,
                     updated_at = ?
                 WHERE id = ?
                   AND brand_id = ?",
                [$safeStatus, $note, $note, $now, $orderId, $brandId]
            );

            if (!$ok) {
                throw new RuntimeException('Unable to update order.');
            }

            if ($this->tableExists('order_items') && in_array($safeStatus, ['accepted', 'preparing', 'ready', 'served'], true)) {
                $itemStatus = $safeStatus === 'accepted' ? 'accepted' : $safeStatus;
                $this->execute(
                    "UPDATE order_items
                     SET status = ?,
                         status_changed_at = ?,
                         updated_at = ?
                     WHERE order_id = ?
                       AND status NOT IN ('cancelled', 'served')",
                    [$itemStatus, $now, $now, $orderId]
                );
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'order', $orderId, 'update_status', $existing, ['status' => $safeStatus, 'note' => $note]);
            $this->pushRealtimeEvent($brandId, $branchId, 'brand.order.status_changed', 'order', $orderId, ['order_id' => $orderId, 'status' => $safeStatus]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestBrandModel::updateOrderStatus failed: ' . $e->getMessage());
            return false;
        }
    }

    public function resolveRequest(string $brandId, string $requestId, string $status = 'resolved', string $note = '', string $actorUserId = ''): bool
    {
        if (!$this->tableExists('service_requests')) {
            return false;
        }

        $existing = $this->one(
            "SELECT *
             FROM service_requests
             WHERE id = ?
               AND brand_id = ?
             LIMIT 1",
            [$requestId, $brandId]
        );

        if (!$existing) {
            return false;
        }

        $branchId = (string) ($existing['branch_id'] ?? '');
        $safeStatus = $this->safeRequestStatus($status);
        $now = $this->now();

        $updates = [
            'status = ?',
        ];
        $params = [$safeStatus];

        if ($this->columnExists('service_requests', 'resolved_at') && in_array($safeStatus, ['resolved', 'rejected'], true)) {
            $updates[] = 'resolved_at = ?';
            $params[] = $now;
        }

        if ($this->columnExists('service_requests', 'acknowledged_at') && $safeStatus === 'accepted') {
            $updates[] = 'acknowledged_at = ?';
            $params[] = $now;
        }

        if ($note !== '' && $this->columnExists('service_requests', 'message')) {
            $updates[] = 'message = ?';
            $params[] = $note;
        }

        if ($this->columnExists('service_requests', 'updated_at')) {
            $updates[] = 'updated_at = ?';
            $params[] = $now;
        }

        $params[] = $requestId;
        $params[] = $brandId;

        $ok = $this->execute(
            "UPDATE service_requests
             SET " . implode(', ', $updates) . "
             WHERE id = ?
               AND brand_id = ?",
            $params
        );

        if ($ok) {
            $this->auditLog($brandId, $branchId, $actorUserId, 'service_request', $requestId, 'update_status', $existing, ['status' => $safeStatus, 'note' => $note]);
            $this->pushRealtimeEvent($brandId, $branchId, 'brand.request.status_changed', 'service_request', $requestId, ['request_id' => $requestId, 'status' => $safeStatus]);
        }

        return $ok;
    }
}