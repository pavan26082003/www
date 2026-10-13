<?php

declare(strict_types=1);

namespace App\Models;

use RuntimeException;
use Throwable;

final class RestManagerModel extends RestBaseModel
{
    /**
     * Exposes selected inherited DB helpers to the controller while keeping
     * all query implementation centralized in RestBaseModel.
     */
    public function __call(string $name, array $arguments)
    {
        if (in_array($name, ['tableExists', 'columnExists', 'one', 'all', 'execute'], true)) {
            return $this->{$name}(...$arguments);
        }

        throw new RuntimeException('Undefined manager model method: ' . $name);
    }

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
        $normalized = $this->entityIdOrNull($value);
        if ($normalized !== null) {
            return $normalized;
        }

        $fallback = $this->entityIdOrNull($default);
        if ($fallback !== null) {
            return $fallback;
        }

        throw new RuntimeException('Valid restaurant context ID is required.');
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

    public function statusToBoolString(string $status): string
    {
        $status = strtolower(trim($status));

        return in_array($status, ['1', 'true', 'active', 'available', 'open', 'in_stock', 'resolved', 'paid'], true)
            ? '1'
            : '0';
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

    protected function safeRole(string $role): string
    {
        $role = strtolower(trim($role));
        $allowed = ['manager', 'waiter', 'kitchen', 'cashier', 'staff', 'chef', 'customerpad', 'pad'];

        return in_array($role, $allowed, true) ? $role : 'waiter';
    }

    protected function safeItemType(string $itemType): string
    {
        $itemType = strtolower(trim($itemType));
        $allowed = ['food', 'beverage', 'drink', 'addon', 'combo', 'other'];

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
        $status = strtolower(trim(str_replace([' ', '-'], '_', $status)));

        $allowed = ['available', 'occupied', 'reserved', 'waiting', 'maintenance'];

        return in_array($status, $allowed, true) ? $status : 'available';
    }

    protected function safeOrderStatus(string $status): string
    {
        $status = strtolower(trim(str_replace([' ', '-'], '_', $status)));
        if ($status === 'canceled') {
            $status = 'cancelled';
        }

        $allowed = ['new', 'accepted', 'preparing', 'ready', 'served', 'completed', 'cancelled'];

        return in_array($status, $allowed, true) ? $status : 'new';
    }

    protected function safeRequestStatus(string $status): string
    {
        $status = strtolower(trim(str_replace([' ', '-'], '_', $status)));

        if (in_array($status, ['pending', 'open'], true)) {
            $status = 'new';
        } elseif (in_array($status, ['accepted', 'resolved', 'in_progress'], true)) {
            $status = 'approved';
        } elseif (in_array($status, ['canceled', 'rejected'], true)) {
            $status = 'cancelled';
        }

        $allowed = ['new', 'approved', 'cancelled', 'closed'];

        return in_array($status, $allowed, true) ? $status : 'new';
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

    protected function jsonDecode(?string $json): array
    {
        $json = trim((string) $json);

        if ($json === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
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

    protected function branchExists(string $brandId, string $branchId): bool
    {
        if (!$this->tableExists('branches')) {
            return true;
        }

        $row = $this->one(
            "SELECT id
             FROM branches
             WHERE id = ?
               AND brand_id = ?
             LIMIT 1",
            [$branchId, $brandId]
        );

        return $row !== null;
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
            [
                $newId,
                $brandId,
                $branchId,
                $categoryName,
                '',
                999,
                1,
                $this->now(),
            ]
        );

        return $newId;
    }

    protected function extractTableNumber(string $tableCode): string
    {
        if (preg_match('/(\d+)/', $tableCode, $matches) === 1) {
            return (string) $matches[1];
        }

        return $tableCode;
    }

    protected function resolveRequiredContext(string $brandId, string $branchId, string $userId = ''): array
    {
        $brandId = $this->brandIdOrNull($brandId);
        $branchId = $this->branchIdOrNull($branchId);
        $userId = trim($userId) !== '' ? $this->userIdOrNull($userId) : '';

        if ($brandId === null || $branchId === null) {
            throw new RuntimeException('Valid brand and branch context are required.');
        }

        return [
            'brand_id'  => $brandId,
            'branch_id' => $branchId,
            'user_id'   => is_string($userId) ? $userId : '',
        ];
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
            'manager',
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

    protected function branchContext(string $brandId, string $branchId, string $userId = ''): array
    {
        $context = [
            'brand_name'             => 'Restaurant',
            'brand_logo'             => '',
            'brand_favicon'          => '',
            'currency'               => 'INR',
            'branch_name'            => 'Main Branch',
            'branch_code'            => '',
            'branch_address'         => '',
            'branch_city'            => '',
            'branch_state'           => '',
            'branch_pincode'         => '',
            'branch_phone'           => '',
            'branch_email'           => '',
            'branch_status'          => 'active',
            'branch_opens_at'        => '',
            'branch_closes_at'       => '',
            'tax_number'             => '',
            'manager_name'           => 'Branch Manager',
            'manager_role'           => 'manager',
            'manager_email'          => '',
            'manager_phone'          => '',
            'manager_user_id'        => '',
            'manager_employee_code'  => '',
            'manager_profile_pic'    => '',
            'manager_aadhar_no'      => '',
        ];

        if ($this->tableExists('brands')) {
            $brand = $this->one(
                "SELECT name, logo, favicon, currency
                 FROM brands
                 WHERE id = ?
                 LIMIT 1",
                [$brandId]
            );

            if ($brand) {
                $context['brand_name']    = (string) ($brand['name'] ?? $context['brand_name']);
                $context['brand_logo']    = (string) ($brand['logo'] ?? '');
                $context['brand_favicon'] = (string) ($brand['favicon'] ?? '');
                $context['currency']      = (string) ($brand['currency'] ?? 'INR');
            }
        }

        if ($this->tableExists('branches')) {
            $branch = $this->one(
                "SELECT
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
                 WHERE id = ?
                   AND brand_id = ?
                 LIMIT 1",
                [$branchId, $brandId]
            );

            if ($branch) {
                $context['branch_name']      = (string) ($branch['name'] ?? $context['branch_name']);
                $context['branch_code']      = (string) ($branch['code'] ?? '');
                $context['branch_address']   = (string) ($branch['address'] ?? '');
                $context['branch_city']      = (string) ($branch['city'] ?? '');
                $context['branch_state']     = (string) ($branch['state'] ?? '');
                $context['branch_pincode']   = (string) ($branch['pincode'] ?? '');
                $context['branch_phone']     = (string) ($branch['phone'] ?? '');
                $context['branch_email']     = (string) ($branch['email'] ?? '');
                $context['branch_status']    = (string) ($branch['status'] ?? 'active');
                $context['branch_opens_at']  = (string) ($branch['opens_at'] ?? '');
                $context['branch_closes_at'] = (string) ($branch['closes_at'] ?? '');
                $context['tax_number']       = (string) ($branch['tax_number'] ?? '');
            }
        }

        if ($this->tableExists('users') && $this->tableExists('user_access')) {
            if ($userId !== '') {
                $manager = $this->one(
                    "SELECT
                        u.id,
                        u.full_name,
                        u.email,
                        u.phone,
                        COALESCE(u.employee_code, '') AS employee_code,
                        COALESCE(u.profile_pic, '') AS profile_pic,
                        COALESCE(u.aadhar_no, '') AS aadhar_no,
                        ua.role_at_branch
                     FROM users u
                     INNER JOIN user_access ua
                        ON ua.user_id = u.id
                       AND ua.branch_id = ?
                     WHERE u.id = ?
                       AND u.brand_id = ?
                     LIMIT 1",
                    [$branchId, $userId, $brandId]
                );

                if ($manager) {
                    $context['manager_name']          = (string) ($manager['full_name'] ?? $context['manager_name']);
                    $context['manager_email']         = (string) ($manager['email'] ?? '');
                    $context['manager_phone']         = (string) ($manager['phone'] ?? '');
                    $context['manager_role']          = (string) ($manager['role_at_branch'] ?? 'manager');
                    $context['manager_user_id']       = (string) ($manager['id'] ?? '');
                    $context['manager_employee_code'] = (string) ($manager['employee_code'] ?? '');
                    $context['manager_profile_pic']   = (string) ($manager['profile_pic'] ?? '');
                    $context['manager_aadhar_no']     = (string) ($manager['aadhar_no'] ?? '');
                }
            }

            if ($context['manager_name'] === 'Branch Manager') {
                $manager = $this->one(
                    "SELECT
                        u.id,
                        u.full_name,
                        u.email,
                        u.phone,
                        COALESCE(u.employee_code, '') AS employee_code,
                        COALESCE(u.profile_pic, '') AS profile_pic,
                        COALESCE(u.aadhar_no, '') AS aadhar_no,
                        ua.role_at_branch
                     FROM users u
                     INNER JOIN user_access ua
                        ON ua.user_id = u.id
                       AND ua.branch_id = ?
                     WHERE u.brand_id = ?
                       AND ua.role_at_branch = 'manager'
                     ORDER BY u.full_name ASC
                     LIMIT 1",
                    [$branchId, $brandId]
                );

                if ($manager) {
                    $context['manager_name']          = (string) ($manager['full_name'] ?? $context['manager_name']);
                    $context['manager_email']         = (string) ($manager['email'] ?? '');
                    $context['manager_phone']         = (string) ($manager['phone'] ?? '');
                    $context['manager_role']          = (string) ($manager['role_at_branch'] ?? 'manager');
                    $context['manager_user_id']       = (string) ($manager['id'] ?? '');
                    $context['manager_employee_code'] = (string) ($manager['employee_code'] ?? '');
                    $context['manager_profile_pic']   = (string) ($manager['profile_pic'] ?? '');
                    $context['manager_aadhar_no']     = (string) ($manager['aadhar_no'] ?? '');
                }
            }
        }

        return $context;
    }

    public function getManagerPageData(string $brandId, string $branchId, string $userId = ''): array
    {
        $ctx = $this->resolveRequiredContext($brandId, $branchId, $userId);

        return [
            'brandId'  => $ctx['brand_id'],
            'branchId' => $ctx['branch_id'],
            'userId'   => $ctx['user_id'],
            'brand'    => $this->getBrand($ctx['brand_id']) ?? [],
            'branch'   => $this->getBranch($ctx['brand_id'], $ctx['branch_id']) ?? [],
            'snapshot' => $this->buildDashboardSnapshot($ctx['brand_id'], $ctx['branch_id'], $ctx['user_id']) ?? [],
        ];
    }

    public function buildDashboardSnapshot(string $brandId, string $branchId, string $userId = ''): ?array
    {
        $ctx = $this->resolveRequiredContext($brandId, $branchId, $userId);
        $brandId = $ctx['brand_id'];
        $branchId = $ctx['branch_id'];
        $userId = $ctx['user_id'];

        $this->syncPaidTableAvailability($brandId, $branchId);

        if (!$this->branchExists($brandId, $branchId)) {
            return null;
        }

        $context         = $this->branchContext($brandId, $branchId, $userId);
        $allOrders       = $this->allOrders($brandId, $branchId);
        $liveOrders      = $this->liveOrders($brandId, $branchId);
        $allBills        = $this->allBills($brandId, $branchId);
        $openBills       = $this->openBills($brandId, $branchId);
        $staff           = $this->staffList($brandId, $branchId);
        $menuItems       = $this->menuItems($brandId, $branchId);
        $menuCategories  = $this->menuCategories($brandId, $branchId);
        $offerBanners    = $this->offerBanners($brandId, $branchId);
        $tables          = $this->tableList($brandId, $branchId);
        $serviceRequests = $this->serviceRequests($brandId, $branchId, $userId);
        $notifications   = $this->managerNotifications($brandId, $branchId, $userId);
        $alerts          = [];
        $kitchenBoard    = $this->kitchenBoard($brandId, $branchId);
        $waiterBoard     = $this->waiterBoard($brandId, $branchId);
        $recentActivity  = $this->recentActivity($brandId, $branchId);
        $topItems        = $this->topItems($brandId, $branchId);
        $revenueChart    = $this->revenueChart($brandId, $branchId);

        $tableOrderMap = [];
        foreach ($liveOrders as $order) {
            $code = (string) ($order['table_code'] ?? '');
            if ($code !== '' && !isset($tableOrderMap[$code])) {
                $tableOrderMap[$code] = (string) ($order['order_number'] ?? '-');
            }
        }

        $tableBillMap = [];
        foreach ($openBills as $bill) {
            $code = (string) ($bill['table_code'] ?? '');
            if ($code !== '' && !isset($tableBillMap[$code])) {
                $tableBillMap[$code] = (string) ($bill['bill_number'] ?? '-');
            }
        }

        foreach ($tables as &$table) {
            $code = (string) ($table['table_code'] ?? '');
            $table['order_number'] = $tableOrderMap[$code] ?? '-';
            $table['bill_number'] = $tableBillMap[$code] ?? '-';
        }
        unset($table);

        return [
            'context' => $context,
            'summary' => [
                'today_orders'          => $this->todayOrdersCount($brandId, $branchId),
                'live_orders'           => count($liveOrders),
                'open_bills'            => count($openBills),
                'today_revenue'         => $this->todayRevenueTotal($brandId, $branchId),
                'staff_count'           => count($staff),
                'active_menu_items'     => count(array_filter($menuItems, static function (array $item): bool {
                    return (string) ($item['is_available'] ?? '1') === '1';
                })),
                'active_tables'         => count($tables),
                'pending_requests'      => count(array_filter($serviceRequests, static function (array $item): bool {
                    return in_array((string) ($item['status'] ?? 'pending'), ['pending', 'new', 'open', 'accepted', 'approved', 'in_progress'], true);
                })),
                'unread_notifications'  => count(array_filter($notifications, static function (array $item): bool {
                    return (int) ($item['is_read'] ?? 0) !== 1;
                })),
                'kitchen_queue'         => count($kitchenBoard),
                'active_waiters'        => count(array_filter($waiterBoard, static function (array $item): bool {
                    return (string) ($item['status'] ?? 'inactive') === 'active';
                })),
            ],
            'all_orders'       => $allOrders,
            'live_orders'      => $liveOrders,
            'all_bills'        => $allBills,
            'open_bills'       => $openBills,
            'staff'            => $staff,
            'menu_items'       => $menuItems,
            'menu_categories'  => $menuCategories,
            'offer_banners'    => $offerBanners,
            'tables'           => $tables,
            'service_requests' => $serviceRequests,
            'notifications'    => $notifications,
            'alerts'           => $alerts,
            'kitchen_board'    => $kitchenBoard,
            'waiter_board'     => $waiterBoard,
            'recent_activity'  => $recentActivity,
            'top_items'        => $topItems,
            'revenue_chart'    => $revenueChart,
            'server_time'      => $this->now(),
        ];
    }

    // protected function tableList(string $brandId, string $branchId): array
    // {
    //     $tableName = $this->firstExistingTable(['tables', 'restaurant_tables']);
    //     if ($tableName === null) {
    //         return [];
    //     }

    //     $codeColumn = $this->columnExists($tableName, 'table_code') ? 'table_code' : 'id';

    //     if ($this->columnExists($tableName, 'table_number')) {
    //         $nameColumn = 'table_number';
    //     } elseif ($this->columnExists($tableName, 'table_code')) {
    //         $nameColumn = 'table_code';
    //     } else {
    //         $nameColumn = 'id';
    //     }

    //     $rows = $this->all(
    //         "SELECT
    //             id,
    //             {$codeColumn} AS table_code,
    //             {$nameColumn} AS table_name,
    //             " . ($this->columnExists($tableName, 'status') ? 'status' : "'available'") . " AS status,
    //             " . ($this->columnExists($tableName, 'capacity') ? 'capacity' : '0') . " AS capacity,
    //             " . ($this->columnExists($tableName, 'current_order_id') ? 'current_order_id' : "''") . " AS current_order_id,
    //             " . ($this->columnExists($tableName, 'current_bill_id') ? 'current_bill_id' : "''") . " AS current_bill_id,
    //             " . ($this->columnExists($tableName, 'status_updated_at') ? 'status_updated_at' : "''") . " AS status_updated_at,
    //             " . ($this->columnExists($tableName, 'updated_at') ? 'updated_at' : "''") . " AS updated_at,
    //             " . ($this->columnExists($tableName, 'shape') ? 'shape' : "''") . " AS shape,
    //             " . ($this->columnExists($tableName, 'label') ? 'label' : "''") . " AS label,
    //             " . ($this->columnExists($tableName, 'zone') ? 'zone' : "''") . " AS zone,
    //             " . ($this->columnExists($tableName, 'area_name') ? 'area_name' : "''") . " AS area_name
    //          FROM {$tableName}
    //          WHERE branch_id = ?
    //          ORDER BY {$nameColumn} ASC, id ASC",
    //         [$branchId]
    //     );

    //     return array_map(function (array $row): array {
    //         $status = (string) ($row['status'] ?? 'available');

    //         return [
    //             'id'               => (string) ($row['id'] ?? ''),
    //             'table_id'         => (string) ($row['id'] ?? ''),
    //             'table_code'       => (string) ($row['table_code'] ?? ''),
    //             'table_name'       => (string) ($row['table_name'] ?? ''),
    //             'status'           => $status,
    //             'occupancy_status' => $status,
    //             'capacity'         => (string) ($row['capacity'] ?? '0'),
    //             'label'            => (string) ($row['label'] ?? ''),
    //             'zone'             => (string) ($row['zone'] ?? ''),
    //             'area_name'        => (string) ($row['area_name'] ?? ''),
    //             'section_name'     => (string) ($row['area_name'] ?? ''),
    //             'shape'            => (string) ($row['shape'] ?? ''),
    //             'current_order_id' => (string) ($row['current_order_id'] ?? ''),
    //             'current_bill_id'  => (string) ($row['current_bill_id'] ?? ''),
    //             'order_number'     => '-',
    //             'bill_number'      => '-',
    //         ];
    //     }, $rows);
    // }


    protected function tableList(string $brandId, string $branchId): array
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

        $rows = $this->all(
            "SELECT
            id,
            {$codeColumn} AS table_code,
            {$nameColumn} AS table_name,
            " . ($this->columnExists($tableName, 'status') ? 'status' : "'available'") . " AS status,
            " . ($this->columnExists($tableName, 'capacity') ? 'capacity' : '0') . " AS capacity,
            " . ($this->columnExists($tableName, 'current_order_id') ? 'current_order_id' : "''") . " AS current_order_id,
            " . ($this->columnExists($tableName, 'current_bill_id') ? 'current_bill_id' : "''") . " AS current_bill_id,
            " . ($this->columnExists($tableName, 'status_updated_at') ? 'status_updated_at' : "''") . " AS status_updated_at,
            " . ($this->columnExists($tableName, 'updated_at') ? 'updated_at' : "''") . " AS updated_at,
            " . ($this->columnExists($tableName, 'shape') ? 'shape' : "''") . " AS shape,
            " . ($this->columnExists($tableName, 'label') ? 'label' : "''") . " AS label,
            " . ($this->columnExists($tableName, 'zone') ? 'zone' : "''") . " AS zone,
            " . ($this->columnExists($tableName, 'area_name') ? 'area_name' : "''") . " AS area_name
         FROM {$tableName}
         WHERE branch_id = ?
         ORDER BY {$nameColumn} ASC, id ASC",
            [$branchId]
        );

        usort($rows, static function (array $a, array $b): int {
            $aCode = trim((string) ($a['table_code'] ?? $a['table_name'] ?? ''));
            $bCode = trim((string) ($b['table_code'] ?? $b['table_name'] ?? ''));

            preg_match('/\d+/', $aCode, $aMatch);
            preg_match('/\d+/', $bCode, $bMatch);

            $aNum = isset($aMatch[0]) ? (int) $aMatch[0] : PHP_INT_MAX;
            $bNum = isset($bMatch[0]) ? (int) $bMatch[0] : PHP_INT_MAX;

            if ($aNum !== $bNum) {
                return $aNum <=> $bNum;
            }

            return strnatcasecmp($aCode, $bCode);
        });

        return array_map(function (array $row): array {
            $status = (string) ($row['status'] ?? 'available');

            return [
                'id'               => (string) ($row['id'] ?? ''),
                'table_id'         => (string) ($row['id'] ?? ''),
                'table_code'       => (string) ($row['table_code'] ?? ''),
                'table_name'       => (string) ($row['table_name'] ?? ''),
                'status'           => $status,
                'occupancy_status' => $status,
                'capacity'         => (string) ($row['capacity'] ?? '0'),
                'label'            => (string) ($row['label'] ?? ''),
                'zone'             => (string) ($row['zone'] ?? ''),
                'area_name'        => (string) ($row['area_name'] ?? ''),
                'section_name'     => (string) ($row['area_name'] ?? ''),
                'shape'            => (string) ($row['shape'] ?? ''),
                'current_order_id' => (string) ($row['current_order_id'] ?? ''),
                'current_bill_id'  => (string) ($row['current_bill_id'] ?? ''),
                'order_number'     => '-',
                'bill_number'      => '-',
            ];
        }, $rows);
    }

    protected function orderItemsMap(string $brandId, string $branchId): array
    {
        if (!$this->tableExists('orders') || !$this->tableExists('order_items')) {
            return [];
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
             WHERE o.brand_id = ?
               AND o.branch_id = ?
             ORDER BY oi.created_at ASC, oi.id ASC",
            [$brandId, $branchId]
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

    protected function orderList(string $brandId, string $branchId, bool $onlyLive = true): array
    {
        if (!$this->tableExists('orders')) {
            return [];
        }

        $tablesTable = $this->firstExistingTable(['tables', 'restaurant_tables']);
        $tableCodeExpr = "'Table'";
        $tableJoin = '';

        if ($tablesTable !== null) {
            $tableCodeColumn = $this->columnExists($tablesTable, 'table_code') ? 'table_code' : 'id';
            $tableCodeExpr = 't.' . $tableCodeColumn;
            $tableJoin = "LEFT JOIN {$tablesTable} t ON t.id = o.table_id";
        }

        $customerExpr = $this->columnExists('orders', 'customer_name')
            ? 'COALESCE(o.customer_name, \'\')'
            : '\'\'';

        $where = "
             WHERE o.brand_id = ?
               AND o.branch_id = ?
               AND COALESCE(o.is_active, 1) = 1";

        if ($onlyLive) {
            $where .= "
               AND COALESCE(o.status, 'new') NOT IN ('closed', 'paid', 'cancelled')";
        }

        $rows = $this->all(
            "SELECT
                o.id,
                COALESCE(o.order_number, o.id) AS order_number,
                COALESCE(o.assigned_waiter_id, '') AS assigned_waiter_id,
                COALESCE(o.status, 'new') AS status,
                COALESCE(o.customer_count, 0) AS customer_count,
                COALESCE(o.grand_total, 0) AS grand_total,
                COALESCE(o.opened_at, o.created_at, o.updated_at) AS created_at,
                COALESCE(o.updated_at, o.created_at, o.opened_at) AS updated_at,
                {$tableCodeExpr} AS table_code,
                {$customerExpr} AS customer_name,
                COALESCE(w.full_name, '') AS waiter_name
             FROM orders o
             {$tableJoin}
             LEFT JOIN users w
                ON w.id = o.assigned_waiter_id
             {$where}
             ORDER BY COALESCE(o.opened_at, o.created_at, o.updated_at) DESC, o.id DESC",
            [$brandId, $branchId]
        );

        $itemsMap = $this->orderItemsMap($brandId, $branchId);

        return array_map(function (array $row) use ($itemsMap): array {
            $orderId = (string) ($row['id'] ?? '');
            $items = $itemsMap[$orderId] ?? [];
            $status = (string) ($row['status'] ?? 'new');
            $waiterName = trim((string) ($row['waiter_name'] ?? ''));

            return [
                'order_id'       => $orderId,
                'order_number'   => (string) ($row['order_number'] ?? ('#' . substr($orderId, 0, 8))),
                'assigned_waiter_id' => (string) ($row['assigned_waiter_id'] ?? ''),
                'table_code'     => (string) ($row['table_code'] ?? 'Table'),
                'status'         => $status,
                'status_label'   => ucfirst(str_replace('_', ' ', $status)),
                'customer_count' => (int) ($row['customer_count'] ?? 0),
                'customer_name'  => (string) ($row['customer_name'] ?? ''),
                'grand_total'    => (float) ($row['grand_total'] ?? 0),
                'created_at'     => (string) ($row['created_at'] ?? ''),
                'updated_at'     => (string) ($row['updated_at'] ?? ''),
                'ordered_at'     => (string) ($row['created_at'] ?? ''),
                'elapsed'        => $this->elapsedLabel((string) ($row['created_at'] ?? '')),
                'items'          => $items,
                'items_count'    => array_reduce($items, static function (int $carry, array $item): int {
                    return $carry + (int) ($item['qty'] ?? 0);
                }, 0),
                'waiter_name'    => $waiterName !== '' ? $waiterName : '-',
            ];
        }, $rows);
    }

    protected function allOrders(string $brandId, string $branchId): array
    {
        return $this->orderList($brandId, $branchId, false);
    }

    protected function liveOrders(string $brandId, string $branchId): array
    {
        return $this->orderList($brandId, $branchId, true);
    }

    protected function billList(string $brandId, string $branchId, bool $onlyOpen = true): array
    {
        if (!$this->tableExists('bills')) {
            return [];
        }

        $tablesTable = $this->firstExistingTable(['tables', 'restaurant_tables']);
        $tableCodeExpr = "'Table'";
        $tableJoin = '';

        if ($tablesTable !== null) {
            $tableCodeColumn = $this->columnExists($tablesTable, 'table_code') ? 'table_code' : 'id';
            $tableCodeExpr = 't.' . $tableCodeColumn;
            $tableJoin = "LEFT JOIN {$tablesTable} t ON t.id = b.table_id";
        }

        $where = "
             WHERE b.brand_id = ?
               AND b.branch_id = ?";

        if ($onlyOpen) {
            $where .= "
               AND (
                    COALESCE(b.payment_status, 'unpaid') IN ('unpaid', 'in_processing')
                    OR COALESCE(b.bill_status, 'new') IN ('new', 'approved')
               )";
        }

        $rows = $this->all(
            "SELECT
                b.id,
                COALESCE(b.bill_number, b.id) AS bill_number,
                COALESCE(b.order_id, '') AS order_id,
                COALESCE(b.bill_status, 'new') AS bill_status,
                COALESCE(b.payment_status, 'unpaid') AS payment_status,
                COALESCE(b.subtotal, 0) AS subtotal,
                COALESCE(b.tax_amount, 0) AS tax_amount,
                COALESCE(b.total_amount, 0) AS total_amount,
                COALESCE(b.generated_at, b.created_at, b.updated_at, b.paid_at) AS event_time,
                COALESCE(b.generated_at, b.created_at, b.updated_at, b.paid_at) AS generated_at,
                COALESCE(b.updated_at, b.generated_at, b.created_at, b.paid_at) AS updated_at,
                COALESCE(b.paid_at, '') AS paid_at,
                {$tableCodeExpr} AS table_code,
                COALESCE(o.order_number, b.order_id, '') AS order_code
             FROM bills b
             {$tableJoin}
             LEFT JOIN orders o
                ON o.id = b.order_id
             {$where}
             ORDER BY COALESCE(b.generated_at, b.created_at, b.updated_at, b.paid_at) DESC, b.id DESC",
            [$brandId, $branchId]
        );

        return array_map(static function (array $row): array {
            $paymentStatus = (string) ($row['payment_status'] ?? 'unpaid');
            $billStatus = (string) ($row['bill_status'] ?? 'new');
            $displayStatus = $paymentStatus === 'paid' ? 'paid' : $billStatus;

            return [
                'bill_id'          => (string) ($row['id'] ?? ''),
                'bill_number'      => (string) ($row['bill_number'] ?? '#BILL'),
                'order_id'         => (string) ($row['order_id'] ?? ''),
                'order_code'       => (string) ($row['order_code'] ?? ''),
                'table_code'       => (string) ($row['table_code'] ?? 'Table'),
                'status'           => $displayStatus,
                'status_label'     => ucfirst(str_replace('_', ' ', $displayStatus)),
                'bill_status'      => $billStatus,
                'payment_status'   => $paymentStatus,
                'subtotal'         => (float) ($row['subtotal'] ?? 0),
                'tax_amount'       => (float) ($row['tax_amount'] ?? 0),
                'grand_total'      => (float) ($row['total_amount'] ?? 0),
                'payable_amount'   => (float) ($row['total_amount'] ?? 0),
                'created_at_label' => (string) ($row['event_time'] ?? ''),
                'updated_at'       => (string) ($row['updated_at'] ?? ''),
                'generated_at'     => (string) ($row['generated_at'] ?? ''),
                'paid_at'          => (string) ($row['paid_at'] ?? ''),
                'event_time'       => (string) ($row['event_time'] ?? ''),
            ];
        }, $rows);
    }

    protected function allBills(string $brandId, string $branchId): array
    {
        return $this->billList($brandId, $branchId, false);
    }

    protected function openBills(string $brandId, string $branchId): array
    {
        return $this->billList($brandId, $branchId, true);
    }

    protected function staffList(string $brandId, string $branchId): array
    {
        if (!$this->tableExists('users')) {
            return [];
        }

        $hasProfilePic = $this->columnExists('users', 'profile_pic');
        $hasAadhar = $this->columnExists('users', 'aadhar_no');
        $hasUserAccess = $this->tableExists('user_access');

        $joinUserAccess = $hasUserAccess
            ? "LEFT JOIN user_access ua
                ON ua.user_id = u.id
               AND ua.branch_id = ?"
            : "LEFT JOIN (SELECT NULL AS user_id, NULL AS branch_id, NULL AS role_at_branch, NULL AS assigned_table_codes, NULL AS permissions_json, NULL AS shift_start_time, NULL AS shift_end_time, NULL AS access_status) ua
                ON 1 = 0";

        $params = $hasUserAccess
            ? [$branchId, $brandId, $branchId, $branchId]
            : [$brandId, $branchId, $branchId];

        $rows = $this->all(
            "SELECT
                u.id,
                COALESCE(u.default_branch_id, '') AS default_branch_id,
                u.full_name,
                COALESCE(u.email, '') AS email,
                COALESCE(u.phone, '') AS phone,
                COALESCE(u.employee_code, '') AS employee_code,
                " . ($hasProfilePic ? "COALESCE(u.profile_pic, '')" : "''") . " AS profile_pic,
                " . ($hasAadhar ? "COALESCE(u.aadhar_no, '')" : "''") . " AS aadhar_no,
                COALESCE(ua.role_at_branch, u.role_type, 'waiter') AS role_type,
                COALESCE(u.status, 'active') AS user_status,
                COALESCE(u.is_active, 1) AS is_active,
                COALESCE(ua.role_at_branch, u.role_type, 'waiter') AS role_at_branch,
                COALESCE(ua.assigned_table_codes, '') AS assigned_table_codes,
                COALESCE(ua.permissions_json, '') AS permissions_json,
                COALESCE(ua.shift_start_time, '') AS shift_start_time,
                COALESCE(ua.shift_end_time, '') AS shift_end_time,
                COALESCE(ua.access_status, u.status, 'active') AS access_status
             FROM users u
             {$joinUserAccess}
             WHERE u.brand_id = ?
               AND COALESCE(ua.branch_id, u.default_branch_id, ?) = ?
               AND COALESCE(ua.role_at_branch, u.role_type, 'waiter') IN ('waiter', 'pad', 'chef', 'customerpad')
             ORDER BY u.full_name ASC",
            $params
        );

        return array_map(function (array $row): array {
            $codes = $this->normalizeCsvList((string) ($row['assigned_table_codes'] ?? ''));
            $permissions = $this->parsePermissionsJson((string) ($row['permissions_json'] ?? ''));

            return [
                'id'                         => (string) ($row['id'] ?? ''),
                'user_id'                    => (string) ($row['id'] ?? ''),
                'default_branch_id'          => (string) ($row['default_branch_id'] ?? ''),
                'full_name'                  => (string) ($row['full_name'] ?? 'Staff'),
                'name'                       => (string) ($row['full_name'] ?? 'Staff'),
                'email'                      => (string) ($row['email'] ?? ''),
                'phone'                      => (string) ($row['phone'] ?? ''),
                'employee_code'              => (string) ($row['employee_code'] ?? ''),
                'profile_pic'                => (string) ($row['profile_pic'] ?? ''),
                'aadhar_no'                  => (string) ($row['aadhar_no'] ?? ''),
                'role_type'                  => (string) ($row['role_type'] ?? 'waiter'),
                'role_at_branch'             => (string) ($row['role_at_branch'] ?? 'waiter'),
                'designation'                => (string) ($row['role_at_branch'] ?? 'waiter'),
                'is_active'                  => (string) ($row['is_active'] ?? '1'),
                'user_status'                => (string) ($row['user_status'] ?? 'active'),
                'access_status'              => (string) ($row['access_status'] ?? 'active'),
                'assigned_table_codes'       => $codes,
                'assigned_table_codes_label' => !empty($codes) ? implode(', ', $codes) : 'All',
                'permissions'                => $permissions,
                'permissions_json'           => (string) ($row['permissions_json'] ?? ''),
                'shift_start_time'           => (string) ($row['shift_start_time'] ?? ''),
                'shift_end_time'             => (string) ($row['shift_end_time'] ?? ''),
            ];
        }, $rows);
    }

    protected function menuItems(string $brandId, string $branchId): array
    {
        if (!$this->tableExists('menu_items')) {
            return [];
        }

        $joinCategory = $this->tableExists('menu_categories')
            ? "LEFT JOIN menu_categories mc ON mc.id = mi.category_id"
            : '';

        $joinBranchMenu = $this->tableExists('branch_menu_items')
            ? "LEFT JOIN branch_menu_items bmi
                    ON bmi.menu_item_id = mi.id
                   AND bmi.branch_id = ?"
            : '';

        $sql = "
            SELECT
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
                " . ($this->tableExists('branch_menu_items') ? "COALESCE(bmi.selling_price, mi.base_price, 0)" : "COALESCE(mi.base_price, 0)") . " AS selling_price,
                " . ($this->tableExists('branch_menu_items') ? "COALESCE(bmi.is_available, mi.is_available, 1)" : "COALESCE(mi.is_available, 1)") . " AS is_available,
                " . ($this->tableExists('branch_menu_items') ? "COALESCE(bmi.stock_status, 'in_stock')" : "'in_stock'") . " AS stock_status,
                " . ($this->tableExists('branch_menu_items') ? "COALESCE(bmi.available_from, '')" : "''") . " AS available_from,
                " . ($this->tableExists('branch_menu_items') ? "COALESCE(bmi.available_to, '')" : "''") . " AS available_to
            FROM menu_items mi
            {$joinCategory}
            {$joinBranchMenu}
            WHERE mi.brand_id = ?
            ORDER BY category_name ASC, mi.name ASC
        ";

        $rows = $this->tableExists('branch_menu_items')
            ? $this->all($sql, [$branchId, $brandId])
            : $this->all($sql, [$brandId]);

        return array_map(static function (array $row): array {
            return [
                'id'                         => (string) ($row['id'] ?? ''),
                'menu_id'                    => (string) ($row['id'] ?? ''),
                'branch_menu_item_id'        => (string) ($row['branch_menu_item_id'] ?? ''),
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


    protected function menuCategories(string $brandId, string $branchId): array
    {
        if (!$this->tableExists('menu_categories')) {
            return [];
        }

        $rows = $this->all(
            "SELECT
            id,
            name,
            COALESCE(description, '') AS description,
            COALESCE(sort_order, 999) AS sort_order,
            COALESCE(is_active, 1) AS is_active
         FROM menu_categories
         WHERE brand_id = ?
           AND branch_id = ?
           AND COALESCE(is_active, 1) = 1
         ORDER BY sort_order ASC, name ASC",
            [$brandId, $branchId]
        );

        return array_map(static function (array $row): array {
            return [
                'id'          => (string)($row['id'] ?? ''),
                'category_id' => (string)($row['id'] ?? ''),
                'name'        => (string)($row['name'] ?? ''),
                'description' => (string)($row['description'] ?? ''),
                'sort_order'  => (int)($row['sort_order'] ?? 999),
                'is_active'   => (string)($row['is_active'] ?? '1'),
            ];
        }, $rows);
    }

    protected function isManagerActionableRequest(array $row, string $managerUserId = ''): bool
    {
        $type = strtolower(trim((string) ($row['request_type'] ?? '')));
        $message = strtolower(trim((string) ($row['message'] ?? '')));
        $assigned = (string) ($row['assigned_to_user_id'] ?? '');

        if ($managerUserId !== '' && $assigned !== '' && $assigned === $managerUserId) {
            return true;
        }

        $needles = [
            'bill',
            'billing',
            'discount',
            'cancel',
            'payment',
            'stock',
            'approve',
            'approval',
            'manager',
            'supervisor',
            'kitchen_to_manager',
            'refund',
            'void',
        ];

        foreach ($needles as $needle) {
            if (strpos($type, $needle) !== false || strpos($message, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    protected function requestSourceLabel(string $requestType, string $message = ''): string
    {
        $haystack = strtolower(trim($requestType . ' ' . $message));

        if (strpos($haystack, 'kitchen') !== false || strpos($haystack, 'chef') !== false || strpos($haystack, 'stock') !== false) {
            return 'kitchen';
        }

        if (strpos($haystack, 'pad') !== false || strpos($haystack, 'customer') !== false) {
            return 'customerpad';
        }

        return 'waiter';
    }

    protected function serviceRequests(string $brandId, string $branchId, string $managerUserId = ''): array
    {
        if (!$this->tableExists('service_requests')) {
            return [];
        }

        $tablesTable = $this->firstExistingTable(['tables', 'restaurant_tables']);
        $tableCodeExpr = "'-'";
        $join = '';

        if ($tablesTable !== null) {
            $tableCodeColumn = $this->columnExists($tablesTable, 'table_code') ? 'table_code' : 'id';
            $tableCodeExpr = 't.' . $tableCodeColumn;
            $join = "LEFT JOIN {$tablesTable} t ON t.id = sr.table_id";
        }

        $eventTimeExpr = "COALESCE(sr.requested_at, sr.resolved_at, sr.acknowledged_at, sr.created_at)";

        $rows = $this->all(
            "SELECT
                sr.id,
                COALESCE(sr.order_id, '') AS order_id,
                COALESCE(sr.assigned_to_user_id, '') AS assigned_to_user_id,
                COALESCE(sr.request_type, 'service') AS request_type,
                COALESCE(sr.status, 'pending') AS status,
                COALESCE(sr.message, '') AS message,
                {$eventTimeExpr} AS created_at,
                {$tableCodeExpr} AS table_code
             FROM service_requests sr
             {$join}
             WHERE sr.brand_id = ?
               AND sr.branch_id = ?
             ORDER BY {$eventTimeExpr} DESC, sr.id DESC
             LIMIT 50",
            [$brandId, $branchId]
        );

        $result = [];
        foreach ($rows as $row) {
            if (!$this->isManagerActionableRequest($row, $managerUserId)) {
                continue;
            }

            $createdAt = (string) ($row['created_at'] ?? '');
            $requestType = (string) ($row['request_type'] ?? 'service');
            $message = (string) ($row['message'] ?? '');

            $result[] = [
                'id'                  => (string) ($row['id'] ?? ''),
                'request_id'          => (string) ($row['id'] ?? ''),
                'order_id'            => (string) ($row['order_id'] ?? ''),
                'assigned_to_user_id' => (string) ($row['assigned_to_user_id'] ?? ''),
                'title'               => ucfirst(str_replace('_', ' ', $requestType)),
                'request_type'        => $requestType,
                'status'              => (string) ($row['status'] ?? 'pending'),
                'message'             => $message,
                'table_code'          => (string) ($row['table_code'] ?? '-'),
                'source'              => $this->requestSourceLabel($requestType, $message),
                'created_at'          => $createdAt,
                'created_at_label'    => $this->elapsedLabel($createdAt),
            ];
        }

        return $result;
    }

    // protected function syncManagerNotificationsFromRequests(string $brandId, string $branchId, string $userId = ''): void
    // {
    //     if (!$this->tableExists('notifications')) {
    //         return;
    //     }

    //     $hasRequestIdColumn = $this->columnExists('notifications', 'request_id');
    //     $hasPayloadJsonColumn = $this->columnExists('notifications', 'payload_json');
    //     $hasRoleTargetColumn = $this->columnExists('notifications', 'role_target');
    //     $hasUserIdColumn = $this->columnExists('notifications', 'user_id');
    //     $hasTypeColumn = $this->columnExists('notifications', 'type');
    //     $hasTitleColumn = $this->columnExists('notifications', 'title');
    //     $hasMessageColumn = $this->columnExists('notifications', 'message');
    //     $hasIsReadColumn = $this->columnExists('notifications', 'is_read');
    //     $hasCreatedAtColumn = $this->columnExists('notifications', 'created_at');

    //     foreach ($this->serviceRequests($brandId, $branchId, $userId) as $request) {
    //         $requestId = trim((string) ($request['request_id'] ?? ''));
    //         if ($requestId === '') {
    //             continue;
    //         }

    //         $title = trim((string) ($request['title'] ?? 'Request'));
    //         $message = trim((string) ($request['message'] ?? ''));
    //         $tableCode = trim((string) ($request['table_code'] ?? ''));
    //         $source = trim((string) ($request['source'] ?? 'waiter'));

    //         /*
    //      * Exact logical identity:
    //      * same brand + branch + manager target + type + user + request_id
    //      * This matches your unique key and avoids payload_json LIKE hacks.
    //      */
    //         $existingSql = "
    //         SELECT id
    //         FROM notifications
    //         WHERE brand_id = ?
    //           AND branch_id = ?
    //           AND COALESCE(role_target, 'manager') = 'manager'
    //           AND COALESCE(type, 'manager_request') = 'manager_request'
    //     ";

    //         $existingParams = [$brandId, $branchId];

    //         if ($hasUserIdColumn) {
    //             if ($userId !== '') {
    //                 $existingSql .= " AND COALESCE(user_id, '') = ? ";
    //                 $existingParams[] = $userId;
    //             } else {
    //                 $existingSql .= " AND user_id IS NULL ";
    //             }
    //         }

    //         if ($hasRequestIdColumn) {
    //             $existingSql .= " AND request_id = ? ";
    //             $existingParams[] = $requestId;
    //         } else {
    //             /*
    //          * Fallback only if request_id column truly does not exist.
    //          * Your current DB has it, so this should normally never be used.
    //          */
    //             $existingSql .= " AND payload_json LIKE ? ";
    //             $existingParams[] = '%"request_id":"' . $requestId . '"%';
    //         }

    //         $existingSql .= " LIMIT 1";

    //         $existing = $this->one($existingSql, $existingParams);
    //         if ($existing) {
    //             continue;
    //         }

    //         $columns = ['id', 'brand_id', 'branch_id'];
    //         $placeholders = ['?', '?', '?'];
    //         $params = [$this->uuid(), $brandId, $branchId];

    //         if ($hasUserIdColumn) {
    //             $columns[] = 'user_id';
    //             $placeholders[] = '?';
    //             $params[] = $userId !== '' ? $userId : null;
    //         }

    //         if ($hasRoleTargetColumn) {
    //             $columns[] = 'role_target';
    //             $placeholders[] = '?';
    //             $params[] = 'manager';
    //         }

    //         if ($hasTypeColumn) {
    //             $columns[] = 'type';
    //             $placeholders[] = '?';
    //             $params[] = 'manager_request';
    //         }

    //         if ($hasTitleColumn) {
    //             $columns[] = 'title';
    //             $placeholders[] = '?';
    //             $params[] = $title;
    //         }

    //         if ($hasMessageColumn) {
    //             $columns[] = 'message';
    //             $placeholders[] = '?';
    //             $params[] = $message;
    //         }

    //         if ($hasRequestIdColumn) {
    //             $columns[] = 'request_id';
    //             $placeholders[] = '?';
    //             $params[] = $requestId;
    //         }

    //         if ($hasPayloadJsonColumn) {
    //             $columns[] = 'payload_json';
    //             $placeholders[] = '?';
    //             $params[] = $this->jsonEncode([
    //                 'request_id' => $requestId,
    //                 'table_code' => $tableCode,
    //                 'source'     => $source,
    //             ]);
    //         }

    //         if ($hasIsReadColumn) {
    //             $columns[] = 'is_read';
    //             $placeholders[] = '?';
    //             $params[] = 0;
    //         }

    //         if ($hasCreatedAtColumn) {
    //             $columns[] = 'created_at';
    //             $placeholders[] = '?';
    //             $params[] = $this->now();
    //         }

    //         $sql = sprintf(
    //             "INSERT INTO notifications (%s) VALUES (%s)",
    //             implode(', ', $columns),
    //             implode(', ', $placeholders)
    //         );

    //         try {
    //             $this->execute($sql, $params);
    //         } catch (Throwable $e) {
    //             /*
    //          * If another request inserts the same notification at the same time,
    //          * the unique index may reject this one. That is fine; swallow only
    //          * duplicate-key style failures and keep the dashboard healthy.
    //          */
    //             $messageText = strtolower($e->getMessage());
    //             if (
    //                 strpos($messageText, 'duplicate') !== false ||
    //                 strpos($messageText, 'unique') !== false ||
    //                 strpos($messageText, '1062') !== false
    //             ) {
    //                 continue;
    //             }

    //             throw $e;
    //         }
    //     }
    // }


    // public function syncBillStatusFromRequest(
    //     string $brandId,
    //     string $branchId,
    //     string $requestId,
    //     string $requestStatus,
    //     string $actorUserId = ''
    // ): bool {
    //     if (!$this->tableExists('service_requests') || !$this->tableExists('bills')) {
    //         return true;
    //     }

    //     // 1. Get request
    //     $request = $this->one(
    //         "SELECT id, table_id, order_id, request_type
    //      FROM service_requests
    //      WHERE id = ?
    //        AND brand_id = ?
    //        AND branch_id = ?
    //      LIMIT 1",
    //         [$requestId, $brandId, $branchId]
    //     );

    //     if (!$request) {
    //         return false;
    //     }

    //     // 2. Only BILL requests
    //     $type = strtolower(trim((string)$request['request_type']));
    //     if ($type !== 'bill') {
    //         return true;
    //     }

    //     // 3. Map request → bill status
    //     $map = [
    //         'accepted'    => 'approved',
    //         'approved'    => 'approved',
    //         'in_progress' => 'in_progress',
    //         'resolved'    => 'approved',
    //         'rejected'    => 'rejected'
    //     ];

    //     $billStatus = $map[$requestStatus] ?? null;
    //     if (!$billStatus) {
    //         return true;
    //     }

    //     // 4. Find latest unpaid bill
    //     $bill = $this->one(
    //         "SELECT id
    //      FROM bills
    //      WHERE brand_id = ?
    //        AND branch_id = ?
    //        AND (
    //             (order_id = ? AND order_id <> '')
    //             OR (table_id = ? AND table_id <> '')
    //        )
    //        AND payment_status <> 'paid'
    //      ORDER BY created_at DESC
    //      LIMIT 1",
    //         [
    //             $brandId,
    //             $branchId,
    //             (string)$request['order_id'],
    //             (string)$request['table_id']
    //         ]
    //     );

    //     if (!$bill) {
    //         return true;
    //     }

    //     // 5. Update bill
    //     $now = $this->now();

    //     return $this->execute(
    //         "UPDATE bills
    //      SET bill_status = ?,
    //          approved_by_user_id = CASE WHEN ? = 'approved' THEN ? ELSE approved_by_user_id END,
    //          approved_at = CASE WHEN ? = 'approved' THEN ? ELSE approved_at END,
    //          updated_at = ?
    //      WHERE id = ?
    //        AND brand_id = ?
    //        AND branch_id = ?",
    //         [
    //             $billStatus,
    //             $billStatus,
    //             $actorUserId,
    //             $billStatus,
    //             $now,
    //             $now,
    //             $bill['id'],
    //             $brandId,
    //             $branchId
    //         ]
    //     );
    // }

    public function syncBillStatusFromRequest(
        string $brandId,
        string $branchId,
        string $requestId,
        string $requestStatus,
        string $actorUserId = ''
    ): bool {
        if (!$this->tableExists('service_requests') || !$this->tableExists('bills')) {
            return true;
        }

        try {
            $request = $this->one(
                "SELECT id, table_id, order_id, request_type
             FROM service_requests
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?
             LIMIT 1",
                [$requestId, $brandId, $branchId]
            );

            if (!$request) {
                return true;
            }

            $type = strtolower(trim((string)($request['request_type'] ?? '')));
            if ($type !== 'bill') {
                return true;
            }

            $requestStatus = strtolower(trim(str_replace([' ', '-'], '_', $requestStatus)));

            $map = [
                'new'         => 'new',
                'pending'     => 'new',
                'accepted'    => 'approved',
                'approved'    => 'approved',
                'in_progress' => 'approved',
                'resolved'    => 'approved',
                'cancelled'   => 'cancelled',
                'canceled'    => 'cancelled',
                'rejected'    => 'cancelled',
                'closed'      => 'closed',
            ];

            $billStatus = $map[$requestStatus] ?? '';
            if ($billStatus === '') {
                return true;
            }

            $orderId = trim((string)($request['order_id'] ?? ''));
            $tableId = trim((string)($request['table_id'] ?? ''));

            if ($orderId === '' && $tableId === '') {
                return true;
            }

            $whereParts = [];
            $params = [$brandId, $branchId];

            if ($orderId !== '') {
                $whereParts[] = 'order_id = ?';
                $params[] = $orderId;
            }

            if ($tableId !== '') {
                $whereParts[] = 'table_id = ?';
                $params[] = $tableId;
            }

            $bill = $this->one(
                "SELECT id
             FROM bills
             WHERE brand_id = ?
               AND branch_id = ?
               AND (" . implode(' OR ', $whereParts) . ")
               AND COALESCE(payment_status, 'unpaid') <> 'paid'
             ORDER BY COALESCE(created_at, updated_at) DESC, id DESC
             LIMIT 1",
                $params
            );

            if (!$bill) {
                return true;
            }

            $now = $this->now();

            $setParts = ['bill_status = ?'];
            $updateParams = [$billStatus];

            if ($this->columnExists('bills', 'approved_by_user_id')) {
                $setParts[] = "approved_by_user_id = CASE WHEN ? = 'approved' THEN ? ELSE approved_by_user_id END";
                $updateParams[] = $billStatus;
                $updateParams[] = $actorUserId !== '' ? $actorUserId : null;
            }

            if ($this->columnExists('bills', 'approved_at')) {
                $setParts[] = "approved_at = CASE WHEN ? = 'approved' THEN ? ELSE approved_at END";
                $updateParams[] = $billStatus;
                $updateParams[] = $now;
            }

            if ($this->columnExists('bills', 'updated_at')) {
                $setParts[] = 'updated_at = ?';
                $updateParams[] = $now;
            }

            $updateParams[] = (string)$bill['id'];
            $updateParams[] = $brandId;
            $updateParams[] = $branchId;

            $this->execute(
                "UPDATE bills
             SET " . implode(', ', $setParts) . "
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?",
                $updateParams
            );

            return true;
        } catch (Throwable $e) {
            error_log('RestManagerModel::syncBillStatusFromRequest failed: ' . $e->getMessage());
            return true;
        }
    }

    protected function syncManagerNotificationsFromRequests(string $brandId, string $branchId, string $userId = ''): void
    {
        if (!$this->tableExists('notifications')) {
            return;
        }

        $deletedRequestIds = $this->getDeletedManagerNotificationRequestIds($brandId, $branchId, $userId);

        $hasRequestIdColumn = $this->columnExists('notifications', 'request_id');
        $hasPayloadJsonColumn = $this->columnExists('notifications', 'payload_json');
        $hasRoleTargetColumn = $this->columnExists('notifications', 'role_target');
        $hasUserIdColumn = $this->columnExists('notifications', 'user_id');
        $hasTypeColumn = $this->columnExists('notifications', 'type');
        $hasTitleColumn = $this->columnExists('notifications', 'title');
        $hasMessageColumn = $this->columnExists('notifications', 'message');
        $hasIsReadColumn = $this->columnExists('notifications', 'is_read');
        $hasCreatedAtColumn = $this->columnExists('notifications', 'created_at');

        foreach ($this->serviceRequests($brandId, $branchId, $userId) as $request) {
            $requestId = trim((string) ($request['request_id'] ?? ''));
            if ($requestId === '') {
                continue;
            }
            if (isset($deletedRequestIds[$requestId])) {
                continue;
            }

            /*
         * IMPORTANT:
         * If a notification for this request was deleted manually,
         * do not recreate it again on every refresh.
         *
         * We treat existence in notifications table as the source of truth.
         * Once deleted, it stays deleted unless a new request is created.
         */
            $existing = null;

            if ($hasRequestIdColumn) {
                $existingSql = "
                SELECT id
                FROM notifications
                WHERE brand_id = ?
                  AND branch_id = ?
                  AND COALESCE(role_target, 'manager') = 'manager'
                  AND COALESCE(type, 'manager_request') = 'manager_request'
                  AND request_id = ?
                LIMIT 1
            ";
                $existingParams = [$brandId, $branchId, $requestId];

                if ($hasUserIdColumn && $userId !== '') {
                    $existingSql = "
                    SELECT id
                    FROM notifications
                    WHERE brand_id = ?
                      AND branch_id = ?
                      AND COALESCE(role_target, 'manager') = 'manager'
                      AND COALESCE(type, 'manager_request') = 'manager_request'
                      AND (user_id IS NULL OR user_id = ?)
                      AND request_id = ?
                    LIMIT 1
                ";
                    $existingParams = [$brandId, $branchId, $userId, $requestId];
                }

                $existing = $this->one($existingSql, $existingParams);
            } else {
                $existing = $this->one(
                    "SELECT id
                 FROM notifications
                 WHERE brand_id = ?
                   AND branch_id = ?
                   AND COALESCE(type, '') = 'manager_request'
                   AND payload_json LIKE ?
                 LIMIT 1",
                    [$brandId, $branchId, '%"request_id":"' . $requestId . '"%']
                );
            }

            if ($existing) {
                continue;
            }

            /*
         * HARD RULE:
         * Only create one DB row per request_id.
         * If user deletes that row, we should not recreate it from stale request history.
         *
         * Therefore only create notification for still-open request states.
         */
            $requestStatus = strtolower(trim((string) ($request['status'] ?? 'pending')));
            if (!in_array($requestStatus, ['pending', 'new', 'in_progress', 'accepted'], true)) {
                continue;
            }

            $payload = [
                'request_id' => $requestId,
                'table_code' => (string) ($request['table_code'] ?? ''),
                'source'     => (string) ($request['source'] ?? 'waiter'),
            ];

            $columns = ['id', 'brand_id', 'branch_id'];
            $placeholders = ['?', '?', '?'];
            $params = [$this->uuid(), $brandId, $branchId];

            if ($hasUserIdColumn) {
                $columns[] = 'user_id';
                $placeholders[] = '?';
                $params[] = $userId !== '' ? $userId : null;
            }

            if ($hasRoleTargetColumn) {
                $columns[] = 'role_target';
                $placeholders[] = '?';
                $params[] = 'manager';
            }

            if ($hasTypeColumn) {
                $columns[] = 'type';
                $placeholders[] = '?';
                $params[] = 'manager_request';
            }

            if ($hasTitleColumn) {
                $columns[] = 'title';
                $placeholders[] = '?';
                $params[] = (string) ($request['title'] ?? 'Request');
            }

            if ($hasMessageColumn) {
                $columns[] = 'message';
                $placeholders[] = '?';
                $params[] = (string) ($request['message'] ?? '');
            }

            if ($hasRequestIdColumn) {
                $columns[] = 'request_id';
                $placeholders[] = '?';
                $params[] = $requestId;
            }

            if ($hasPayloadJsonColumn) {
                $columns[] = 'payload_json';
                $placeholders[] = '?';
                $params[] = $this->jsonEncode($payload);
            }

            if ($hasIsReadColumn) {
                $columns[] = 'is_read';
                $placeholders[] = '?';
                $params[] = 0;
            }

            if ($hasCreatedAtColumn) {
                $columns[] = 'created_at';
                $placeholders[] = '?';
                $params[] = $this->now();
            }

            $sql = sprintf(
                "INSERT INTO notifications (%s) VALUES (%s)",
                implode(', ', $columns),
                implode(', ', $placeholders)
            );

            try {
                $this->execute($sql, $params);
            } catch (Throwable $e) {
                $message = strtolower($e->getMessage());
                if (
                    strpos($message, 'duplicate') !== false ||
                    strpos($message, 'unique') !== false ||
                    strpos($message, '1062') !== false
                ) {
                    continue;
                }

                throw $e;
            }
        }
    }

    protected function extractDeletedManagerNotificationRequestId(array $row): string
    {
        $oldData = $this->jsonDecode((string) ($row['old_data_json'] ?? ''));
        $requestId = trim((string) ($oldData['request_id'] ?? $oldData['service_request_id'] ?? ''));
        if ($requestId !== '') {
            return $requestId;
        }

        $payload = $oldData['payload_json'] ?? [];
        if (!is_array($payload)) {
            $payload = $this->jsonDecode((string) $payload);
        }

        return trim((string) ($payload['request_id'] ?? $payload['service_request_id'] ?? ''));
    }

    protected function getDeletedManagerNotificationRequestIds(string $brandId, string $branchId, string $userId = ''): array
    {
        if (!$this->tableExists('audit_logs')) {
            return [];
        }

        $params = [$brandId, $branchId];
        $userSql = '';
        if ($userId !== '') {
            $userSql = ' AND (user_id IS NULL OR user_id = ?) ';
            $params[] = $userId;
        }

        $rows = $this->all(
            "SELECT old_data_json
             FROM audit_logs
             WHERE brand_id = ?
               AND branch_id = ?
               AND module = 'manager'
               AND entity_type = 'notification'
               AND action = 'delete'
               {$userSql}",
            $params
        );

        $requestIds = [];
        foreach ($rows as $row) {
            $requestId = $this->extractDeletedManagerNotificationRequestId($row);
            if ($requestId !== '') {
                $requestIds[$requestId] = true;
            }
        }

        return $requestIds;
    }

    protected function managerNotifications(string $brandId, string $branchId, string $userId = ''): array
    {
        if (!$this->tableExists('notifications')) {
            return [];
        }

        $this->syncManagerNotificationsFromRequests($brandId, $branchId, $userId);

        $params = [$brandId, $branchId];
        $userWhere = '';
        if ($userId !== '') {
            $userWhere = " AND (n.user_id IS NULL OR n.user_id = ?) ";
            $params[] = $userId;
        }

        $rows = $this->all(
            "SELECT
                n.id,
                COALESCE(n.type, 'manager_request') AS type,
                COALESCE(n.title, 'Notification') AS title,
                COALESCE(n.message, '') AS message,
                COALESCE(n.payload_json, '') AS payload_json,
                COALESCE(n.is_read, 0) AS is_read,
                COALESCE(n.created_at, NOW()) AS created_at
             FROM notifications n
             WHERE n.brand_id = ?
               AND n.branch_id = ?
               AND COALESCE(n.role_target, 'manager') = 'manager'
               {$userWhere}
             ORDER BY COALESCE(n.created_at, NOW()) DESC, n.id DESC
             LIMIT 50",
            $params
        );

        $result = [];
        foreach ($rows as $row) {
            $payload = $this->jsonDecode((string) ($row['payload_json'] ?? ''));
            $requestId = is_array($payload) ? (string) ($payload['request_id'] ?? '') : '';
            $source = is_array($payload) ? (string) ($payload['source'] ?? 'waiter') : 'waiter';
            $tableCode = is_array($payload) ? (string) ($payload['table_code'] ?? '-') : '-';

            $result[] = [
                'notification_id'  => (string) ($row['id'] ?? ''),
                'id'               => (string) ($row['id'] ?? ''),
                'type'             => (string) ($row['type'] ?? 'manager_request'),
                'title'            => (string) ($row['title'] ?? 'Notification'),
                'message'          => (string) ($row['message'] ?? ''),
                'request_id'       => $requestId,
                'table_code'       => $tableCode,
                'source'           => $source,
                'is_read'          => (int) ($row['is_read'] ?? 0),
                'created_at'       => (string) ($row['created_at'] ?? ''),
                'created_at_label' => $this->elapsedLabel((string) ($row['created_at'] ?? '')),
                'status'           => ((int) ($row['is_read'] ?? 0) === 1) ? 'read' : 'unread',
            ];
        }

        return $result;
    }


    public function markNotificationRead(string $brandId, string $branchId, string $notificationId, string $userId = ''): bool
    {
        if (!$this->tableExists('notifications')) {
            return false;
        }

        $this->begin();

        try {
            $ok = $this->execute(
                "UPDATE notifications
             SET is_read = 1
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?",
                [$notificationId, $brandId, $branchId]
            );

            if (!$ok) {
                throw new RuntimeException('Unable to mark notification as read.');
            }

            $this->auditLog($brandId, $branchId, $userId, 'notification', $notificationId, 'mark_read', null, [
                'is_read' => 1,
            ]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::markNotificationRead failed: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteNotification(string $brandId, string $branchId, string $notificationId, string $userId = ''): bool
    {
        if (!$this->tableExists('notifications')) {
            return false;
        }

        $this->begin();

        try {
            $existing = $this->one(
                "SELECT *
             FROM notifications
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?
             LIMIT 1",
                [$notificationId, $brandId, $branchId]
            );

            if (!$existing) {
                return false;
            }

            $ok = $this->execute(
                "DELETE FROM notifications
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?",
                [$notificationId, $brandId, $branchId]
            );

            if (!$ok) {
                throw new RuntimeException('Unable to delete notification.');
            }

            $this->auditLog($brandId, $branchId, $userId, 'notification', $notificationId, 'delete', $existing, null);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::deleteNotification failed: ' . $e->getMessage());
            return false;
        }
    }

    public function clearNotifications(string $brandId, string $branchId, string $userId = ''): bool
    {
        if (!$this->tableExists('notifications')) {
            return false;
        }

        $this->begin();

        try {
            $ok = $this->execute(
                "DELETE FROM notifications
             WHERE brand_id = ?
               AND branch_id = ?",
                [$brandId, $branchId]
            );

            if (!$ok) {
                throw new RuntimeException('Unable to clear notifications.');
            }

            $this->auditLog($brandId, $branchId, $userId, 'notification', null, 'clear_all', null, [
                'cleared' => true,
            ]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::clearNotifications failed: ' . $e->getMessage());
            return false;
        }
    }
    protected function alerts(string $brandId, string $branchId): array
    {
        $alerts = [];

        foreach ($this->liveOrders($brandId, $branchId) as $order) {
            if ((string) ($order['status'] ?? '') === 'ready') {
                $alerts[] = [
                    'id'               => 'alert-order-' . (string) ($order['order_id'] ?? ''),
                    'title'            => 'Order Ready',
                    'alert_type'       => 'order_ready',
                    'status'           => 'pending',
                    'message'          => 'Ready to serve ' . (string) ($order['order_number'] ?? ''),
                    'table_code'       => (string) ($order['table_code'] ?? '-'),
                    'source'           => 'kitchen',
                    'created_at'       => $this->now(),
                    'created_at_label' => 'Now',
                ];
            }
        }

        return $alerts;
    }

    protected function kitchenBoard(string $brandId, string $branchId): array
    {
        $result = [];

        foreach ($this->liveOrders($brandId, $branchId) as $order) {
            if (in_array((string) ($order['status'] ?? ''), ['accepted', 'preparing', 'ready'], true)) {
                $result[] = [
                    'title'            => (string) ($order['order_number'] ?? 'Order'),
                    'order_number'     => (string) ($order['order_number'] ?? 'Order'),
                    'message'          => 'Kitchen flow for ' . (string) ($order['order_number'] ?? 'Order'),
                    'summary'          => 'Preparing items',
                    'status'           => (string) ($order['status'] ?? 'preparing'),
                    'table_code'       => (string) ($order['table_code'] ?? '-'),
                    'items_count'      => (int) ($order['items_count'] ?? 0),
                    'elapsed'          => (string) ($order['elapsed'] ?? 'Now'),
                    'created_at_label' => (string) ($order['elapsed'] ?? 'Now'),
                ];
            }
        }

        return array_slice($result, 0, 20);
    }

    protected function waiterBoard(string $brandId, string $branchId): array
    {
        $staff = $this->staffList($brandId, $branchId);
        $requests = $this->serviceRequests($brandId, $branchId);
        $orders = $this->liveOrders($brandId, $branchId);

        $waiters = [];

        foreach ($staff as $member) {
            if ((string) ($member['role_at_branch'] ?? '') !== 'waiter') {
                continue;
            }

            $assignedTables = is_array($member['assigned_table_codes'] ?? null) ? $member['assigned_table_codes'] : [];
            $ordersCount = 0;
            $requestsCount = 0;

            foreach ($orders as $order) {
                $tableCode = (string) ($order['table_code'] ?? '');
                if (empty($assignedTables) || in_array($tableCode, $assignedTables, true)) {
                    $ordersCount++;
                }
            }

            foreach ($requests as $request) {
                $tableCode = (string) ($request['table_code'] ?? '');
                if (empty($assignedTables) || in_array($tableCode, $assignedTables, true)) {
                    $requestsCount++;
                }
            }

            $waiters[] = [
                'id'                         => (string) ($member['id'] ?? ''),
                'user_id'                    => (string) ($member['id'] ?? ''),
                'full_name'                  => (string) ($member['full_name'] ?? 'Waiter'),
                'waiter_name'                => (string) ($member['full_name'] ?? 'Waiter'),
                'employee_code'              => (string) ($member['employee_code'] ?? ''),
                'role_at_branch'             => 'waiter',
                'status'                     => ((string) ($member['is_active'] ?? '1') === '1') ? 'active' : 'inactive',
                'assigned_table_codes_label' => (string) ($member['assigned_table_codes_label'] ?? 'All'),
                'assigned_tables'            => (string) ($member['assigned_table_codes_label'] ?? 'All'),
                'live_orders'                => $ordersCount,
                'orders_count'               => $ordersCount,
                'pending_requests'           => $requestsCount,
            ];
        }

        return $waiters;
    }

    protected function recentActivity(string $brandId, string $branchId): array
    {
        $activity = [];

        foreach (array_slice($this->liveOrders($brandId, $branchId), 0, 6) as $order) {
            $activity[] = [
                'title'            => 'Order ' . (string) ($order['order_number'] ?? ''),
                'description'      => 'Order is currently ' . strtolower((string) ($order['status_label'] ?? 'Active')),
                'message'          => 'Order update',
                'status'           => (string) ($order['status'] ?? 'active'),
                'source'           => 'waiter',
                'actor'            => 'system',
                'table_code'       => (string) ($order['table_code'] ?? '-'),
                'created_at_label' => (string) ($order['elapsed'] ?? 'Now'),
                'elapsed'          => (string) ($order['elapsed'] ?? 'Now'),
            ];
        }

        foreach (array_slice($this->serviceRequests($brandId, $branchId), 0, 6) as $request) {
            $activity[] = [
                'title'            => (string) ($request['title'] ?? 'Request'),
                'description'      => (string) ($request['message'] ?? 'Service request'),
                'message'          => (string) ($request['message'] ?? 'Service request'),
                'status'           => (string) ($request['status'] ?? 'pending'),
                'source'           => (string) ($request['source'] ?? 'waiter'),
                'actor'            => 'branch',
                'table_code'       => (string) ($request['table_code'] ?? '-'),
                'created_at_label' => (string) ($request['created_at_label'] ?? 'Now'),
                'elapsed'          => (string) ($request['created_at_label'] ?? 'Now'),
            ];
        }

        return array_slice($activity, 0, 10);
    }

    protected function topItems(string $brandId, string $branchId): array
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
               AND o.branch_id = ?
               AND DATE(COALESCE(o.created_at, o.updated_at)) = CURDATE()
             GROUP BY COALESCE(oi.item_name_snapshot, mi.name, 'Item')
             ORDER BY quantity DESC, line_total DESC
             LIMIT 8",
            [$brandId, $branchId]
        );
    }

    protected function revenueChart(string $brandId, string $branchId): array
    {
        $dayValues = array_fill(0, 8, 0.0);
        $weekValues = array_fill(0, 7, 0.0);
        $monthValues = array_fill(0, 12, 0.0);

        if (!$this->tableExists('bills')) {
            return [
                'day' => [
                    'labels' => ['09 AM', '11 AM', '01 PM', '03 PM', '05 PM', '07 PM', '09 PM', '11 PM'],
                    'values' => $dayValues,
                ],
                'week' => [
                    'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    'values' => $weekValues,
                ],
                'month' => [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    'values' => $monthValues,
                ],
            ];
        }

        $dayRows = $this->all(
            "SELECT HOUR(COALESCE(paid_at, updated_at, created_at)) AS hour_no,
                    SUM(COALESCE(total_amount, 0)) AS total_amount
             FROM bills
             WHERE brand_id = ?
               AND branch_id = ?
               AND payment_status = 'paid'
               AND DATE(COALESCE(paid_at, updated_at, created_at)) = CURDATE()
             GROUP BY HOUR(COALESCE(paid_at, updated_at, created_at))",
            [$brandId, $branchId]
        );

        $slots = [9, 11, 13, 15, 17, 19, 21, 23];
        foreach ($dayRows as $row) {
            $hour = (int) ($row['hour_no'] ?? -1);
            $index = array_search($hour, $slots, true);
            if ($index !== false) {
                $dayValues[(int) $index] = (float) ($row['total_amount'] ?? 0);
            }
        }

        $weekRows = $this->all(
            "SELECT WEEKDAY(COALESCE(paid_at, updated_at, created_at)) AS weekday_no,
                    SUM(COALESCE(total_amount, 0)) AS total_amount
             FROM bills
             WHERE brand_id = ?
               AND branch_id = ?
               AND payment_status = 'paid'
               AND DATE(COALESCE(paid_at, updated_at, created_at)) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
             GROUP BY WEEKDAY(COALESCE(paid_at, updated_at, created_at))",
            [$brandId, $branchId]
        );

        foreach ($weekRows as $row) {
            $index = (int) ($row['weekday_no'] ?? -1);
            if ($index >= 0 && $index < 7) {
                $weekValues[$index] = (float) ($row['total_amount'] ?? 0);
            }
        }

        $monthRows = $this->all(
            "SELECT MONTH(COALESCE(paid_at, updated_at, created_at)) AS month_no,
                    SUM(COALESCE(total_amount, 0)) AS total_amount
             FROM bills
             WHERE brand_id = ?
               AND branch_id = ?
               AND payment_status = 'paid'
               AND YEAR(COALESCE(paid_at, updated_at, created_at)) = YEAR(CURDATE())
             GROUP BY MONTH(COALESCE(paid_at, updated_at, created_at))",
            [$brandId, $branchId]
        );

        foreach ($monthRows as $row) {
            $index = (int) ($row['month_no'] ?? 0) - 1;
            if ($index >= 0 && $index < 12) {
                $monthValues[$index] = (float) ($row['total_amount'] ?? 0);
            }
        }

        // return [
        //     'day' => [
        //         'labels' => ['09 AM', '11 AM', '01 PM', '03 PM', '05 PM', '07 PM', '09 PM', '11 PM'],
        //         'values' => $dayValues,
        //     ],
        //     'week' => [
        //         'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        //         'values' => $weekValues,
        //     ],
        //     'month' => [
        //         'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        //         'values' => $monthValues,
        //     ],
        // ];

        return [
            'day' => [
                'labels'  => ['09 AM', '11 AM', '01 PM', '03 PM', '05 PM', '07 PM', '09 PM', '11 PM'],
                'revenue' => $dayValues,
                'orders'  => array_fill(0, 8, 0),
                'items'   => array_fill(0, 8, 0),
                'values'  => $dayValues,
            ],
            'week' => [
                'labels'  => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'revenue' => $weekValues,
                'orders'  => array_fill(0, 7, 0),
                'items'   => array_fill(0, 7, 0),
                'values'  => $weekValues,
            ],
            'month' => [
                'labels'  => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'revenue' => $monthValues,
                'orders'  => array_fill(0, 12, 0),
                'items'   => array_fill(0, 12, 0),
                'values'  => $monthValues,
            ],
        ];
    }

    protected function todayOrdersCount(string $brandId, string $branchId): int
    {
        if (!$this->tableExists('orders')) {
            return 0;
        }

        return (int) $this->scalar(
            "SELECT COUNT(*)
             FROM orders
             WHERE brand_id = ?
               AND branch_id = ?
               AND DATE(COALESCE(created_at, updated_at)) = CURDATE()",
            [$brandId, $branchId],
            0
        );
    }

    protected function todayRevenueTotal(string $brandId, string $branchId): float
    {
        if (!$this->tableExists('bills')) {
            return 0.0;
        }

        return (float) $this->scalar(
            "SELECT COALESCE(SUM(COALESCE(total_amount, 0)), 0)
             FROM bills
             WHERE brand_id = ?
               AND branch_id = ?
               AND payment_status = 'paid'
               AND DATE(COALESCE(paid_at, updated_at, created_at)) = CURDATE()",
            [$brandId, $branchId],
            0
        );
    }

    public function getStaffDetail(string $brandId, string $branchId, string $userId): ?array
    {
        foreach ($this->staffList($brandId, $branchId) as $staff) {
            if ((string) ($staff['id'] ?? '') === $userId) {
                return $staff;
            }
        }

        return null;
    }

    public function getMenuItemDetail(string $brandId, string $branchId, string $menuId): ?array
    {
        foreach ($this->menuItems($brandId, $branchId) as $item) {
            if ((string) ($item['id'] ?? '') === $menuId) {
                return $item;
            }
        }

        return null;
    }

    public function getOrderDetail(string $brandId, string $branchId, string $orderId): ?array
    {
        foreach ($this->liveOrders($brandId, $branchId) as $order) {
            if ((string) ($order['order_id'] ?? '') === $orderId) {
                return $order;
            }
        }

        if (!$this->tableExists('orders')) {
            return null;
        }

        $tablesTable = $this->firstExistingTable(['tables', 'restaurant_tables']);
        $tableCodeExpr = "'Table'";
        $join = '';

        if ($tablesTable !== null) {
            $tableCodeColumn = $this->columnExists($tablesTable, 'table_code') ? 'table_code' : 'id';
            $tableCodeExpr = 't.' . $tableCodeColumn;
            $join = "LEFT JOIN {$tablesTable} t ON t.id = o.table_id";
        }

        $customerExpr = $this->columnExists('orders', 'customer_name')
            ? 'COALESCE(o.customer_name, \'\')'
            : '\'\'';

        $row = $this->one(
            "SELECT
                o.id,
                COALESCE(o.order_number, o.id) AS order_number,
                COALESCE(o.status, 'new') AS status,
                COALESCE(o.customer_count, 0) AS customer_count,
                COALESCE(o.grand_total, 0) AS grand_total,
                COALESCE(o.created_at, o.updated_at) AS created_at,
                {$tableCodeExpr} AS table_code,
                {$customerExpr} AS customer_name
             FROM orders o
             {$join}
             WHERE o.id = ?
               AND o.brand_id = ?
               AND o.branch_id = ?
             LIMIT 1",
            [$orderId, $brandId, $branchId]
        );

        if (!$row) {
            return null;
        }

        $itemsMap = $this->orderItemsMap($brandId, $branchId);
        $items = $itemsMap[$orderId] ?? [];

        return [
            'order_id'       => (string) ($row['id'] ?? ''),
            'order_number'   => (string) ($row['order_number'] ?? ('#' . substr($orderId, 0, 8))),
            'table_code'     => (string) ($row['table_code'] ?? 'Table'),
            'status'         => (string) ($row['status'] ?? 'new'),
            'status_label'   => ucfirst((string) ($row['status'] ?? 'new')),
            'customer_count' => (int) ($row['customer_count'] ?? 0),
            'customer_name'  => (string) ($row['customer_name'] ?? ''),
            'grand_total'    => (float) ($row['grand_total'] ?? 0),
            'created_at'     => (string) ($row['created_at'] ?? ''),
            'elapsed'        => $this->elapsedLabel((string) ($row['created_at'] ?? '')),
            'items'          => $items,
            'items_count'    => array_reduce($items, static function (int $carry, array $item): int {
                return $carry + (int) ($item['qty'] ?? 0);
            }, 0),
        ];
    }

    public function getBillDetail(string $brandId, string $branchId, string $billId): ?array
    {
        if (!$this->tableExists('bills')) {
            return null;
        }

        $tablesTable = $this->firstExistingTable(['tables', 'restaurant_tables']);
        $tableCodeExpr = "'Table'";
        $join = '';

        if ($tablesTable !== null) {
            $tableCodeColumn = $this->columnExists($tablesTable, 'table_code') ? 'table_code' : 'id';
            $tableCodeExpr = 't.' . $tableCodeColumn;
            $join = "LEFT JOIN {$tablesTable} t ON t.id = b.table_id";
        }

        $row = $this->one(
            "SELECT
                b.id,
                COALESCE(b.bill_number, b.id) AS bill_number,
                COALESCE(b.order_id, '') AS order_id,
                COALESCE(b.bill_status, 'new') AS bill_status,
                COALESCE(b.payment_status, 'unpaid') AS payment_status,
                COALESCE(b.subtotal, 0) AS subtotal,
                COALESCE(b.tax_amount, 0) AS tax_amount,
                COALESCE(b.total_amount, 0) AS total_amount,
                COALESCE(b.generated_at, b.created_at, b.updated_at) AS event_time,
                {$tableCodeExpr} AS table_code
             FROM bills b
             {$join}
             WHERE b.id = ?
               AND b.brand_id = ?
               AND b.branch_id = ?
             LIMIT 1",
            [$billId, $brandId, $branchId]
        );

        if (!$row) {
            return null;
        }

        $paymentStatus = (string) ($row['payment_status'] ?? 'unpaid');
        $billStatus = (string) ($row['bill_status'] ?? 'new');
        $displayStatus = $paymentStatus === 'paid' ? 'paid' : $billStatus;

        return [
            'bill_id'          => (string) ($row['id'] ?? ''),
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
            'created_at_label' => $this->elapsedLabel((string) ($row['event_time'] ?? '')),
            'updated_at'       => (string) ($row['event_time'] ?? ''),
            'generated_at'     => (string) ($row['event_time'] ?? ''),
        ];
    }


    protected function employeeCodePrefix(string $role): string
    {
        $role = strtolower(trim(str_replace([' ', '-'], '_', $role)));

        if ($role === 'waiter') {
            return 'WTR';
        }

        if (in_array($role, ['chef', 'kitchen', 'cook'], true)) {
            return 'KCN';
        }

        if (in_array($role, ['pad', 'customerpad', 'customer_pad'], true)) {
            return 'PAD';
        }

        if ($role === 'manager') {
            return 'MGR';
        }

        return 'STF';
    }

    public function generateNextEmployeeCode(string $brandId, string $branchId, string $role): string
    {
        $prefix = $this->employeeCodePrefix($role);

        if (!$this->tableExists('users')) {
            return $prefix . '001';
        }

        $hasUserAccess = $this->tableExists('user_access');

        if ($hasUserAccess) {
            $rows = $this->all(
                "SELECT COALESCE(u.employee_code, '') AS employee_code
             FROM users u
             LEFT JOIN user_access ua
                ON ua.user_id = u.id
               AND ua.branch_id = ?
             WHERE u.brand_id = ?
               AND COALESCE(ua.branch_id, u.default_branch_id, ?) = ?
               AND COALESCE(u.employee_code, '') LIKE ?
             ORDER BY u.employee_code ASC",
                [$branchId, $brandId, $branchId, $branchId, $prefix . '%']
            );
        } else {
            $rows = $this->all(
                "SELECT COALESCE(employee_code, '') AS employee_code
             FROM users
             WHERE brand_id = ?
               AND default_branch_id = ?
               AND COALESCE(employee_code, '') LIKE ?
             ORDER BY employee_code ASC",
                [$brandId, $branchId, $prefix . '%']
            );
        }

        $max = 0;

        foreach ($rows as $row) {
            $code = strtoupper(trim((string) ($row['employee_code'] ?? '')));

            if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $code, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }

        return $prefix . str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }


    public function createStaff(string $brandId, string $branchId, array $payload, string $actorUserId = ''): bool
    {
        if (!$this->tableExists('users') || !$this->tableExists('user_access')) {
            return false;
        }

        $fullName = trim((string) ($payload['full_name'] ?? ''));
        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        $phone = trim((string) ($payload['phone'] ?? ''));
        $employeeCode = trim((string) ($payload['employee_code'] ?? ''));
        $profilePic = trim((string) ($payload['profile_pic'] ?? ''));
        $aadharNo = trim((string) ($payload['aadhar_no'] ?? ''));
        $role = $this->safeRole((string) ($payload['role_at_branch'] ?? 'waiter'));
        $isActive = $this->boolToInt($payload['is_active'] ?? 1, 1);
        $assignedTableCodes = $this->normalizeCsvList($payload['assigned_table_codes'] ?? []);
        $shiftStart = trim((string) ($payload['shift_start_time'] ?? ''));
        $shiftEnd = trim((string) ($payload['shift_end_time'] ?? ''));
        $permissions = is_array($payload['permissions'] ?? null) ? $payload['permissions'] : [];
        $accessStatus = $isActive === 1 ? 'active' : 'inactive';
        $userStatus = $isActive === 1 ? 'active' : 'inactive';

        if ($fullName === '' || $email === '') {
            return false;
        }

        if ($this->emailExists($brandId, $email)) {
            return false;
        }

        $hasProfilePic = $this->columnExists('users', 'profile_pic');
        $hasAadhar = $this->columnExists('users', 'aadhar_no');

        $this->begin();

        try {
            $userId = $this->uuid();
            $accessId = $this->uuid();
            $now = $this->now();

            $userColumns = [
                'id',
                'brand_id',
                'default_branch_id',
                'full_name',
                'employee_code',
                'email',
                'phone',
            ];

            $userValues = [
                $userId,
                $brandId,
                $branchId,
                $fullName,
                $employeeCode,
                $email,
                $phone,
            ];

            if ($hasProfilePic) {
                $userColumns[] = 'profile_pic';
                $userValues[] = $profilePic !== '' ? $profilePic : null;
            }

            if ($hasAadhar) {
                $userColumns[] = 'aadhar_no';
                $userValues[] = $aadharNo !== '' ? $aadharNo : null;
            }

            $userColumns = array_merge($userColumns, [
                'role_type',
                'status',
                'is_active',
                'created_at',
                'updated_at',
            ]);

            $userValues = array_merge($userValues, [
                $role,
                $userStatus,
                $isActive,
                $now,
                $now,
            ]);

            $ok1 = $this->execute(
                "INSERT INTO users (" . implode(', ', $userColumns) . ")
                 VALUES (" . implode(', ', array_fill(0, count($userColumns), '?')) . ")",
                $userValues
            );

            if (!$ok1) {
                throw new RuntimeException('Unable to insert user.');
            }

            $ok2 = $this->execute(
                "INSERT INTO user_access (
                    id, user_id, branch_id, role_at_branch, assigned_table_codes, permissions_json,
                    shift_start_time, shift_end_time, access_status, created_at, updated_at
                 ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $accessId,
                    $userId,
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

            if (!$ok2) {
                throw new RuntimeException('Unable to insert user access.');
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'staff', $userId, 'create', null, $payload);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.staff.created', 'staff', $userId, ['user_id' => $userId]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::createStaff failed: ' . $e->getMessage());
            return false;
        }
    }





    public function updateStaff(string $brandId, string $branchId, string $targetUserId, array $payload, string $actorUserId = ''): bool
    {
        if (!$this->tableExists('users') || !$this->tableExists('user_access')) {
            return false;
        }

        $existing = $this->getStaffDetail($brandId, $branchId, $targetUserId);
        if (!$existing) {
            return false;
        }

        $fullName = trim((string) ($payload['full_name'] ?? ''));
        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        $phone = trim((string) ($payload['phone'] ?? ''));
        $employeeCode = trim((string) ($payload['employee_code'] ?? ''));
        $profilePic = trim((string) ($payload['profile_pic'] ?? ''));
        $aadharNo = trim((string) ($payload['aadhar_no'] ?? ''));
        $role = $this->safeRole((string) ($payload['role_at_branch'] ?? 'waiter'));
        $isActive = $this->boolToInt($payload['is_active'] ?? 1, 1);
        $assignedTableCodes = $this->normalizeCsvList($payload['assigned_table_codes'] ?? []);
        $shiftStart = trim((string) ($payload['shift_start_time'] ?? ''));
        $shiftEnd = trim((string) ($payload['shift_end_time'] ?? ''));
        $permissions = is_array($payload['permissions'] ?? null) ? $payload['permissions'] : [];

        $accessStatus = $isActive === 1 ? 'active' : 'inactive';
        $userStatus = $isActive === 1 ? 'active' : 'inactive';

        if ($fullName === '' || $email === '') {
            return false;
        }

        if ($this->emailExists($brandId, $email, $targetUserId)) {
            return false;
        }

        $hasProfilePic = $this->columnExists('users', 'profile_pic');
        $hasAadhar = $this->columnExists('users', 'aadhar_no');

        $this->begin();

        try {
            $now = $this->now();

            $setParts = [
                'full_name = ?',
                'employee_code = ?',
                'email = ?',
                'phone = ?',
                'role_type = ?',
                'status = ?',
                'is_active = ?',
                'updated_at = ?',
            ];

            $params = [
                $fullName,
                $employeeCode,
                $email,
                $phone,
                $role,
                $userStatus,
                $isActive,
                $now,
            ];

            // Important: update profile_pic only when a new file path exists.
            // This preserves the old photo when editing staff without selecting a new image.
            if ($hasProfilePic && $profilePic !== '') {
                $setParts[] = 'profile_pic = ?';
                $params[] = $profilePic;
            }

            if ($hasAadhar) {
                $setParts[] = 'aadhar_no = ?';
                $params[] = $aadharNo !== '' ? $aadharNo : null;
            }

            $params[] = $targetUserId;
            $params[] = $brandId;

            $ok1 = $this->execute(
                "UPDATE users
             SET " . implode(', ', $setParts) . "
             WHERE id = ?
               AND brand_id = ?",
                $params
            );

            if (!$ok1) {
                throw new RuntimeException('Unable to update user.');
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

            $this->auditLog($brandId, $branchId, $actorUserId, 'staff', $targetUserId, 'update', $existing, $payload);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.staff.updated', 'staff', $targetUserId, [
                'user_id' => $targetUserId,
            ]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::updateStaff failed: ' . $e->getMessage());
            return false;
        }
    }

    public function updateManagerProfile(
        string $brandId,
        string $branchId,
        string $targetUserId,
        array $payload,
        string $actorUserId = ''
    ): bool {
        if (!$this->tableExists('users')) {
            return false;
        }

        $existing = $this->one(
            "SELECT id, full_name, email, phone, employee_code, profile_pic, aadhar_no
         FROM users
         WHERE id = ?
           AND brand_id = ?
         LIMIT 1",
            [$targetUserId, $brandId]
        );

        if (!$existing) {
            return false;
        }

        $fullName = trim((string)($payload['full_name'] ?? ''));
        $email = strtolower(trim((string)($payload['email'] ?? '')));
        $phone = trim((string)($payload['phone'] ?? ''));
        $employeeCode = trim((string)($payload['employee_code'] ?? ''));
        $profilePic = trim((string)($payload['profile_pic'] ?? ''));
        $aadharNo = trim((string)($payload['aadhar_no'] ?? ''));

        if ($fullName === '' || $email === '') {
            return false;
        }

        $hasProfilePic = $this->columnExists('users', 'profile_pic');
        $hasAadhar = $this->columnExists('users', 'aadhar_no');
        $now = $this->now();

        $setParts = [
            'full_name = ?',
            'employee_code = ?',
            'email = ?',
            'phone = ?',
            'updated_at = ?',
        ];

        $params = [
            $fullName,
            $employeeCode,
            $email,
            $phone,
            $now,
        ];

        if ($hasProfilePic && $profilePic !== '') {
            $setParts[] = 'profile_pic = ?';
            $params[] = $profilePic;
        }

        if ($hasAadhar) {
            $setParts[] = 'aadhar_no = ?';
            $params[] = $aadharNo !== '' ? $aadharNo : null;
        }

        $params[] = $targetUserId;
        $params[] = $brandId;

        try {
            $ok = $this->execute(
                "UPDATE users
             SET " . implode(', ', $setParts) . "
             WHERE id = ?
               AND brand_id = ?",
                $params
            );

            if (!$ok) {
                return false;
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'manager_profile', $targetUserId, 'update', $existing, $payload);

            $this->pushRealtimeEvent($brandId, $branchId, 'manager.profile.updated', 'manager_profile', $targetUserId, [
                'user_id' => $targetUserId,
            ]);

            return true;
        } catch (Throwable $e) {
            error_log('RestManagerModel::updateManagerProfile failed: ' . $e->getMessage());
            return false;
        }
    }

    public function toggleStaffStatus(string $brandId, string $branchId, string $targetUserId, string $actorUserId = ''): bool
    {
        $existing = $this->getStaffDetail($brandId, $branchId, $targetUserId);
        if (!$existing) {
            return false;
        }

        $newIsActive = ((string) ($existing['is_active'] ?? '1') === '1') ? 0 : 1;
        $newStatus = $newIsActive === 1 ? 'active' : 'inactive';

        $this->begin();

        try {
            $now = $this->now();

            $ok1 = $this->execute(
                "UPDATE users
                 SET is_active = ?, status = ?, updated_at = ?
                 WHERE id = ? AND brand_id = ?",
                [$newIsActive, $newStatus, $now, $targetUserId, $brandId]
            );

            $ok2 = $this->execute(
                "UPDATE user_access
                 SET access_status = ?, updated_at = ?
                 WHERE user_id = ? AND branch_id = ?",
                [$newStatus, $now, $targetUserId, $branchId]
            );

            if (!$ok1 || !$ok2) {
                throw new RuntimeException('Unable to toggle staff status.');
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'staff', $targetUserId, 'toggle_status', $existing, ['is_active' => $newIsActive]);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.staff.status_changed', 'staff', $targetUserId, ['user_id' => $targetUserId, 'is_active' => $newIsActive]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::toggleStaffStatus failed: ' . $e->getMessage());
            return false;
        }
    }



    public function deleteStaff(string $brandId, string $branchId, string $targetUserId, string $actorUserId = ''): bool
    {
        $existing = $this->getStaffDetail($brandId, $branchId, $targetUserId);
        if (!$existing) {
            return false;
        }

        $this->begin();

        try {
            // Prevent FK issues before deleting user
            if ($this->tableExists('orders') && $this->columnExists('orders', 'assigned_waiter_id')) {
                $this->execute(
                    "UPDATE orders
                 SET assigned_waiter_id = NULL, updated_at = ?
                 WHERE assigned_waiter_id = ?
                   AND brand_id = ?
                   AND branch_id = ?",
                    [$this->now(), $targetUserId, $brandId, $branchId]
                );
            }

            if ($this->tableExists('service_requests') && $this->columnExists('service_requests', 'assigned_to_user_id')) {
                $this->execute(
                    "UPDATE service_requests
                 SET assigned_to_user_id = NULL
                 WHERE assigned_to_user_id = ?
                   AND brand_id = ?
                   AND branch_id = ?",
                    [$targetUserId, $brandId, $branchId]
                );
            }

            if ($this->tableExists('notifications') && $this->columnExists('notifications', 'user_id')) {
                $this->execute(
                    "UPDATE notifications
                 SET user_id = NULL
                 WHERE user_id = ?
                   AND brand_id = ?
                   AND branch_id = ?",
                    [$targetUserId, $brandId, $branchId]
                );
            }

            $okAccess = $this->execute(
                "DELETE FROM user_access
             WHERE user_id = ?
               AND branch_id = ?",
                [$targetUserId, $branchId]
            );

            $okUser = $this->execute(
                "DELETE FROM users
             WHERE id = ?
               AND brand_id = ?",
                [$targetUserId, $brandId]
            );

            if (!$okAccess || !$okUser) {
                throw new RuntimeException('Unable to delete staff.');
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'staff', $targetUserId, 'delete', $existing, null);

            $this->pushRealtimeEvent($brandId, $branchId, 'manager.staff.deleted', 'staff', $targetUserId, [
                'user_id' => $targetUserId,
                'branch_id' => $branchId,
            ]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::deleteStaff failed: ' . $e->getMessage());
            return false;
        }
    }

    public function upsertWaiterAssignment(string $brandId, string $branchId, string $targetUserId, array $payload, string $actorUserId = ''): bool
    {
        $payload['role_at_branch'] = 'waiter';

        $existing = $this->getStaffDetail($brandId, $branchId, $targetUserId);
        if ($existing) {
            return $this->updateStaff($brandId, $branchId, $targetUserId, $payload, $actorUserId);
        }

        $payload['email'] = trim((string) ($payload['email'] ?? ''));
        if ($payload['email'] === '') {
            $payload['email'] = 'waiter_' . substr(str_replace('-', '', $targetUserId), 0, 10) . '@local.restaurant';
        }

        if (trim((string) ($payload['full_name'] ?? '')) === '') {
            $payload['full_name'] = 'Waiter ' . substr($targetUserId, 0, 6);
        }

        $payload['employee_code'] = trim((string) ($payload['employee_code'] ?? ('WT-' . strtoupper(substr(str_replace('-', '', $targetUserId), 0, 4)))));

        return $this->createStaff($brandId, $branchId, $payload, $actorUserId);
    }

    public function createMenuItem(string $brandId, string $branchId, array $payload, string $actorUserId = ''): bool
    {
        if (!$this->tableExists('menu_items')) {
            return false;
        }

        $name = trim((string) ($payload['name'] ?? $payload['item_name'] ?? ''));
        if ($name === '') {
            return false;
        }

        $categoryId = $this->resolveCategoryId($brandId, $branchId, $payload);
        $menuId = $this->uuid();
        $now = $this->now();
        $itemType = $this->safeItemType((string) ($payload['item_type'] ?? 'food'));
        $stockStatus = $this->safeStockStatus((string) ($payload['stock_status'] ?? 'in_stock'));
        $isAvailable = $this->boolToInt($payload['is_available'] ?? 1, 1);

        $this->begin();

        try {
            $ok1 = $this->execute(
                "INSERT INTO menu_items (
                    id, brand_id, category_id, default_kitchen_station_id, name, description, image_url, video_url,
                    video_type, item_type, sku, base_price, tax_rate, preparation_time_mins, is_veg, is_vegan,
                    is_spicy, is_available, created_at, updated_at
                 ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $menuId,
                    $brandId,
                    $categoryId !== '' ? $categoryId : null,
                    trim((string) ($payload['default_kitchen_station_id'] ?? '')) ?: null,
                    $name,
                    trim((string) ($payload['description'] ?? '')),
                    trim((string) ($payload['image_url'] ?? '')),
                    trim((string) ($payload['video_url'] ?? '')),
                    trim((string) ($payload['video_type'] ?? '')),
                    $itemType,
                    trim((string) ($payload['sku'] ?? '')),
                    (float) ($payload['base_price'] ?? $payload['price'] ?? $payload['selling_price'] ?? 0),
                    (float) ($payload['tax_rate'] ?? 0),
                    (int) ($payload['preparation_time_mins'] ?? 0),
                    $this->boolToInt($payload['is_veg'] ?? 0, 0),
                    $this->boolToInt($payload['is_vegan'] ?? 0, 0),
                    $this->boolToInt($payload['is_spicy'] ?? 0, 0),
                    $isAvailable,
                    $now,
                    $now,
                ]
            );

            if (!$ok1) {
                throw new RuntimeException('Unable to insert menu item.');
            }

            $ok2 = true;
            if ($this->tableExists('branch_menu_items')) {
                $ok2 = $this->execute(
                    "INSERT INTO branch_menu_items (
                        id, branch_id, menu_item_id, selling_price, is_available, stock_status,
                        available_from, available_to, created_at, updated_at
                     ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $this->uuid(),
                        $branchId,
                        $menuId,
                        (float) ($payload['selling_price'] ?? $payload['base_price'] ?? $payload['price'] ?? 0),
                        $isAvailable,
                        $stockStatus,
                        trim((string) ($payload['available_from'] ?? '')) ?: null,
                        trim((string) ($payload['available_to'] ?? '')) ?: null,
                        $now,
                        $now,
                    ]
                );
            }

            if (!$ok2) {
                throw new RuntimeException('Unable to insert branch menu item.');
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'menu_item', $menuId, 'create', null, $payload);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.menu.created', 'menu_item', $menuId, ['menu_id' => $menuId]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::createMenuItem failed: ' . $e->getMessage());
            return false;
        }
    }

    public function updateMenuItem(string $brandId, string $branchId, string $menuId, array $payload, string $actorUserId = ''): bool
    {
        if (!$this->tableExists('menu_items')) {
            return false;
        }

        $existing = $this->getMenuItemDetail($brandId, $branchId, $menuId);
        if (!$existing) {
            return false;
        }

        $name = trim((string) ($payload['name'] ?? $payload['item_name'] ?? ''));
        if ($name === '') {
            return false;
        }

        $categoryId = $this->resolveCategoryId($brandId, $branchId, $payload);
        $itemType = $this->safeItemType((string) ($payload['item_type'] ?? 'food'));
        $stockStatus = $this->safeStockStatus((string) ($payload['stock_status'] ?? 'in_stock'));
        $isAvailable = $this->boolToInt($payload['is_available'] ?? 1, 1);

        $this->begin();

        try {
            $now = $this->now();

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
                    $name,
                    trim((string) ($payload['description'] ?? '')),
                    trim((string) ($payload['image_url'] ?? '')),
                    trim((string) ($payload['video_url'] ?? '')),
                    trim((string) ($payload['video_type'] ?? '')),
                    $itemType,
                    trim((string) ($payload['sku'] ?? '')),
                    (float) ($payload['base_price'] ?? $payload['price'] ?? $payload['selling_price'] ?? 0),
                    (float) ($payload['tax_rate'] ?? 0),
                    (int) ($payload['preparation_time_mins'] ?? 0),
                    $this->boolToInt($payload['is_veg'] ?? 0, 0),
                    $this->boolToInt($payload['is_vegan'] ?? 0, 0),
                    $this->boolToInt($payload['is_spicy'] ?? 0, 0),
                    $isAvailable,
                    $now,
                    $menuId,
                    $brandId,
                ]
            );

            if (!$ok1) {
                throw new RuntimeException('Unable to update menu item.');
            }

            $ok2 = true;
            if ($this->tableExists('branch_menu_items')) {
                $branchItem = $this->one(
                    "SELECT id
                     FROM branch_menu_items
                     WHERE branch_id = ?
                       AND menu_item_id = ?
                     LIMIT 1",
                    [$branchId, $menuId]
                );

                if ($branchItem) {
                    $ok2 = $this->execute(
                        "UPDATE branch_menu_items
                         SET selling_price = ?,
                             is_available = ?,
                             stock_status = ?,
                             available_from = ?,
                             available_to = ?,
                             updated_at = ?
                         WHERE branch_id = ?
                           AND menu_item_id = ?",
                        [
                            (float) ($payload['selling_price'] ?? $payload['base_price'] ?? $payload['price'] ?? 0),
                            $isAvailable,
                            $stockStatus,
                            trim((string) ($payload['available_from'] ?? '')) ?: null,
                            trim((string) ($payload['available_to'] ?? '')) ?: null,
                            $now,
                            $branchId,
                            $menuId,
                        ]
                    );
                } else {
                    $ok2 = $this->execute(
                        "INSERT INTO branch_menu_items (
                            id, branch_id, menu_item_id, selling_price, is_available, stock_status,
                            available_from, available_to, created_at, updated_at
                         ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                        [
                            $this->uuid(),
                            $branchId,
                            $menuId,
                            (float) ($payload['selling_price'] ?? $payload['base_price'] ?? $payload['price'] ?? 0),
                            $isAvailable,
                            $stockStatus,
                            trim((string) ($payload['available_from'] ?? '')) ?: null,
                            trim((string) ($payload['available_to'] ?? '')) ?: null,
                            $now,
                            $now,
                        ]
                    );
                }

                if (!$ok2) {
                    throw new RuntimeException('Unable to update branch menu item.');
                }
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'menu_item', $menuId, 'update', $existing, $payload);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.menu.updated', 'menu_item', $menuId, ['menu_id' => $menuId]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::updateMenuItem failed: ' . $e->getMessage());
            return false;
        }
    }

    public function toggleMenuItemStatus(string $brandId, string $branchId, string $menuId, string $actorUserId = ''): bool
    {
        $existing = $this->getMenuItemDetail($brandId, $branchId, $menuId);
        if (!$existing) {
            return false;
        }

        $newIsAvailable = ((string) ($existing['is_available'] ?? '1') === '1') ? 0 : 1;
        $newStockStatus = $newIsAvailable === 1 ? 'in_stock' : 'out_of_stock';

        $this->begin();

        try {
            $now = $this->now();

            $ok1 = $this->execute(
                "UPDATE menu_items
                 SET is_available = ?, updated_at = ?
                 WHERE id = ? AND brand_id = ?",
                [$newIsAvailable, $now, $menuId, $brandId]
            );

            $ok2 = true;
            if ($this->tableExists('branch_menu_items')) {
                $ok2 = $this->execute(
                    "UPDATE branch_menu_items
                     SET is_available = ?, stock_status = ?, updated_at = ?
                     WHERE branch_id = ? AND menu_item_id = ?",
                    [$newIsAvailable, $newStockStatus, $now, $branchId, $menuId]
                );
            }

            if (!$ok1 || !$ok2) {
                throw new RuntimeException('Unable to toggle menu item status.');
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'menu_item', $menuId, 'toggle_status', $existing, ['is_available' => $newIsAvailable]);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.menu.status_changed', 'menu_item', $menuId, ['menu_id' => $menuId, 'is_available' => $newIsAvailable]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::toggleMenuItemStatus failed: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteMenuItem(string $brandId, string $branchId, string $menuId, string $actorUserId = ''): bool
    {
        // Permanently delete a menu item.  This removes associated branch
        // relationships, menu_item_addons links and order_items referencing
        // the item to prevent foreign key constraint violations.  An audit log
        // entry and realtime event are generated upon success.

        // Ensure the menu item exists before proceeding
        $existing = $this->getMenuItemDetail($brandId, $branchId, $menuId);
        if (!$existing) {
            return false;
        }

        $this->begin();

        try {
            // Remove branch-specific records for this menu item
            if ($this->tableExists('branch_menu_items')) {
                $this->execute(
                    'DELETE FROM branch_menu_items WHERE branch_id = ? AND menu_item_id = ?',
                    [$branchId, $menuId]
                );
            }

            // Remove addon references (both as menu item and as addon item)
            if ($this->tableExists('menu_item_addons')) {
                $this->execute(
                    'DELETE FROM menu_item_addons WHERE menu_item_id = ? OR addon_item_id = ?',
                    [$menuId, $menuId]
                );
            }

            // Remove order items associated with this menu item
            if ($this->tableExists('order_items') && $this->columnExists('order_items', 'menu_item_id')) {
                $this->execute(
                    'DELETE FROM order_items WHERE menu_item_id = ?',
                    [$menuId]
                );
            }

            // Delete the menu item itself
            $ok = $this->execute(
                'DELETE FROM menu_items WHERE id = ? AND brand_id = ?',
                [$menuId, $brandId]
            );

            if (!$ok) {
                throw new RuntimeException('Unable to delete menu item.');
            }

            // Audit and realtime event
            $this->auditLog($brandId, $branchId, $actorUserId, 'menu_item', $menuId, 'delete', $existing, null);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.menu.deleted', 'menu_item', $menuId, null);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::deleteMenuItem failed: ' . $e->getMessage());
            return false;
        }
    }



    protected function offerBanners(string $brandId, string $branchId): array
    {
        if (!$this->tableExists('branch_offer_banners')) {
            return [];
        }

        $rows = $this->all(
            "SELECT
            id,
            id AS banner_id,
            brand_id,
            branch_id,
            image_path,
            sort_order,
            is_active,
            starts_at,
            ends_at,
            created_at
         FROM branch_offer_banners
         WHERE brand_id = ?
           AND branch_id = ?
         ORDER BY sort_order ASC, created_at DESC",
            [$brandId, $branchId]
        );

        return array_map(static function (array $row): array {
            return [
                'id'         => (string) ($row['id'] ?? ''),
                'banner_id'  => (string) ($row['banner_id'] ?? $row['id'] ?? ''),
                'brand_id'   => (string) ($row['brand_id'] ?? ''),
                'branch_id'  => (string) ($row['branch_id'] ?? ''),
                'image_path' => (string) ($row['image_path'] ?? ''),
                'sort_order' => (int) ($row['sort_order'] ?? 1),
                'is_active'  => (int) ($row['is_active'] ?? 1),
                'starts_at'  => (string) ($row['starts_at'] ?? ''),
                'ends_at'    => (string) ($row['ends_at'] ?? ''),
                'created_at' => (string) ($row['created_at'] ?? ''),
            ];
        }, $rows);
    }

    public function getOfferBannerDetail(string $brandId, string $branchId, string $bannerId): ?array
    {
        if (!$this->tableExists('branch_offer_banners')) {
            return null;
        }

        $row = $this->one(
            "SELECT
            id,
            id AS banner_id,
            brand_id,
            branch_id,
            image_path,
            sort_order,
            is_active,
            starts_at,
            ends_at,
            created_at
         FROM branch_offer_banners
         WHERE id = ?
           AND brand_id = ?
           AND branch_id = ?
         LIMIT 1",
            [$bannerId, $brandId, $branchId]
        );

        if (!$row) {
            return null;
        }

        return [
            'id'         => (string) ($row['id'] ?? ''),
            'banner_id'  => (string) ($row['banner_id'] ?? $row['id'] ?? ''),
            'brand_id'   => (string) ($row['brand_id'] ?? ''),
            'branch_id'  => (string) ($row['branch_id'] ?? ''),
            'image_path' => (string) ($row['image_path'] ?? ''),
            'sort_order' => (int) ($row['sort_order'] ?? 1),
            'is_active'  => (int) ($row['is_active'] ?? 1),
            'starts_at'  => (string) ($row['starts_at'] ?? ''),
            'ends_at'    => (string) ($row['ends_at'] ?? ''),
            'created_at' => (string) ($row['created_at'] ?? ''),
        ];
    }

    public function createOfferBanner(string $brandId, string $branchId, array $payload, string $actorUserId = ''): bool
    {
        if (!$this->tableExists('branch_offer_banners')) {
            return false;
        }

        $imagePath = trim((string) ($payload['image_path'] ?? ''));

        if ($imagePath === '') {
            return false;
        }

        $bannerId = $this->uuid();
        $now = $this->now();

        $ok = $this->execute(
            "INSERT INTO branch_offer_banners (
            id, brand_id, branch_id, image_path, sort_order, is_active, starts_at, ends_at, created_at
         ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $bannerId,
                $brandId,
                $branchId,
                $imagePath,
                max(1, (int) ($payload['sort_order'] ?? 1)),
                (int) ($payload['is_active'] ?? 1),
                $payload['starts_at'] ?? null,
                $payload['ends_at'] ?? null,
                $now,
            ]
        );

        if ($ok) {
            $this->auditLog($brandId, $branchId, $actorUserId, 'offer_banner', $bannerId, 'create', null, $payload);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.offer.created', 'offer_banner', $bannerId, ['banner_id' => $bannerId]);
        }

        return $ok;
    }

    public function updateOfferBanner(string $brandId, string $branchId, string $bannerId, array $payload, string $actorUserId = ''): bool
    {
        if (!$this->tableExists('branch_offer_banners')) {
            return false;
        }

        $existing = $this->getOfferBannerDetail($brandId, $branchId, $bannerId);

        if (!$existing) {
            return false;
        }

        $imagePath = trim((string) ($payload['image_path'] ?? $existing['image_path'] ?? ''));

        if ($imagePath === '') {
            return false;
        }

        $ok = $this->execute(
            "UPDATE branch_offer_banners
         SET image_path = ?,
             sort_order = ?,
             is_active = ?,
             starts_at = ?,
             ends_at = ?
         WHERE id = ?
           AND brand_id = ?
           AND branch_id = ?",
            [
                $imagePath,
                max(1, (int) ($payload['sort_order'] ?? 1)),
                (int) ($payload['is_active'] ?? 1),
                $payload['starts_at'] ?? null,
                $payload['ends_at'] ?? null,
                $bannerId,
                $brandId,
                $branchId,
            ]
        );

        if ($ok) {
            $this->auditLog($brandId, $branchId, $actorUserId, 'offer_banner', $bannerId, 'update', $existing, $payload);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.offer.updated', 'offer_banner', $bannerId, ['banner_id' => $bannerId]);
        }

        return $ok;
    }

    public function deleteOfferBanner(string $brandId, string $branchId, string $bannerId, string $actorUserId = ''): bool
    {
        if (!$this->tableExists('branch_offer_banners')) {
            return false;
        }

        $existing = $this->getOfferBannerDetail($brandId, $branchId, $bannerId);

        if (!$existing) {
            return false;
        }

        $ok = $this->execute(
            "DELETE FROM branch_offer_banners
         WHERE id = ?
           AND brand_id = ?
           AND branch_id = ?",
            [$bannerId, $brandId, $branchId]
        );

        if ($ok) {
            $this->auditLog($brandId, $branchId, $actorUserId, 'offer_banner', $bannerId, 'delete', $existing, ['deleted' => true]);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.offer.deleted', 'offer_banner', $bannerId, ['banner_id' => $bannerId]);
        }

        return $ok;
    }

    /**
     * Permanently delete a service request.
     *
     * Removes the request row and records an audit log and realtime event.  No
     * other tables are affected since requests are non‑referential.
     *
     * @param string $brandId
     * @param string $branchId
     * @param string $requestId
     * @param string $actorUserId
     * @return bool
     */
    public function deleteRequest(string $brandId, string $branchId, string $requestId, string $actorUserId = ''): bool
    {
        if (!$this->tableExists('service_requests')) {
            return false;
        }

        $existing = $this->one(
            "SELECT * FROM service_requests WHERE id = ? AND brand_id = ? AND branch_id = ? LIMIT 1",
            [$requestId, $brandId, $branchId]
        );
        if (!$existing) {
            return false;
        }

        $this->begin();

        try {
            $ok = $this->execute(
                "DELETE FROM service_requests WHERE id = ? AND brand_id = ? AND branch_id = ?",
                [$requestId, $brandId, $branchId]
            );
            if (!$ok) {
                throw new RuntimeException('Unable to delete request.');
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'service_request', $requestId, 'delete', $existing, null);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.request.deleted', 'service_request', $requestId, null);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::deleteRequest failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Permanently delete a bill and its dependent rows.
     *
     * When a bill is removed the associated bill items and payments are
     * deleted, references on orders and tables are cleared and an audit
     * entry and realtime event are generated.
     *
     * @param string $brandId
     * @param string $branchId
     * @param string $billId
     * @param string $actorUserId
     * @return bool
     */
    public function deleteBill(string $brandId, string $branchId, string $billId, string $actorUserId = ''): bool
    {
        if (!$this->tableExists('bills')) {
            return false;
        }

        $existing = $this->getBillDetail($brandId, $branchId, $billId);
        if (!$existing) {
            return false;
        }

        $this->begin();

        try {
            $now = $this->now();

            // Clear bill_id references on orders
            if ($this->tableExists('orders') && $this->columnExists('orders', 'bill_id')) {
                $this->execute(
                    "UPDATE orders SET bill_id = NULL WHERE bill_id = ?",
                    [$billId]
                );
            }

            // Clear bill references on tables
            $tableTable = $this->firstExistingTable(['tables', 'restaurant_tables']);
            if ($tableTable !== null && $this->columnExists($tableTable, 'current_bill_id')) {
                $this->execute(
                    "UPDATE {$tableTable} SET current_bill_id = NULL WHERE current_bill_id = ?",
                    [$billId]
                );
            }

            // Delete bill items
            if ($this->tableExists('bill_items')) {
                $this->execute(
                    "DELETE FROM bill_items WHERE bill_id = ?",
                    [$billId]
                );
            }

            // Delete payments associated with this bill
            if ($this->tableExists('payments') && $this->columnExists('payments', 'bill_id')) {
                $this->execute(
                    "DELETE FROM payments WHERE bill_id = ?",
                    [$billId]
                );
            }

            // Delete the bill
            $ok = $this->execute(
                "DELETE FROM bills WHERE id = ? AND brand_id = ? AND branch_id = ?",
                [$billId, $brandId, $branchId]
            );
            if (!$ok) {
                throw new RuntimeException('Unable to delete bill.');
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'bill', $billId, 'delete', $existing, null);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.bill.deleted', 'bill', $billId, null);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::deleteBill failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Permanently delete a table along with clearing its references.
     *
     * When a table is removed all references on orders, bills and service
     * requests are cleared.  The table is then deleted and an audit log
     * entry and realtime event are created.
     *
     * @param string $brandId
     * @param string $branchId
     * @param string $tableId
     * @param string $actorUserId
     * @return bool
     */
    public function deleteTable(string $brandId, string $branchId, string $tableId, string $actorUserId = ''): bool
    {
        $tableTable = $this->firstExistingTable(['tables', 'restaurant_tables']);
        if ($tableTable === null) {
            return false;
        }

        $existing = $this->one(
            "SELECT * FROM {$tableTable} WHERE id = ? AND branch_id = ? LIMIT 1",
            [$tableId, $branchId]
        );
        if (!$existing) {
            return false;
        }

        $this->begin();

        try {
            // Clear references on orders
            if ($this->tableExists('orders') && $this->columnExists('orders', 'table_id')) {
                $this->execute(
                    "UPDATE orders SET table_id = NULL WHERE table_id = ?",
                    [$tableId]
                );
            }

            // Clear references on bills
            if ($this->tableExists('bills') && $this->columnExists('bills', 'table_id')) {
                $this->execute(
                    "UPDATE bills SET table_id = NULL WHERE table_id = ?",
                    [$tableId]
                );
            }

            // Clear references on service requests
            if ($this->tableExists('service_requests') && $this->columnExists('service_requests', 'table_id')) {
                $this->execute(
                    "UPDATE service_requests SET table_id = NULL WHERE table_id = ?",
                    [$tableId]
                );
            }

            // Delete the table row
            $params = [$tableId];
            $sql = "DELETE FROM {$tableTable} WHERE id = ?";
            if ($this->columnExists($tableTable, 'branch_id')) {
                $sql .= " AND branch_id = ?";
                $params[] = $branchId;
            }
            $ok = $this->execute($sql, $params);
            if (!$ok) {
                throw new RuntimeException('Unable to delete table.');
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'table', $tableId, 'delete', $existing, null);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.table.deleted', 'table', $tableId, null);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::deleteTable failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear all notifications for a manager.  This is a thin wrapper
     * around clearNotifications() that preserves naming consistency with
     * earlier controllers.
     *
     * @param string $brandId
     * @param string $branchId
     * @param string $userId
     * @return bool
     */
    public function clearManagerNotifications(string $brandId, string $branchId, string $userId = ''): bool
    {
        return $this->clearNotifications($brandId, $branchId, $userId);
    }


    /**
     * Permanently delete an order and its dependent data.
     *
     * When an order is removed we need to release any table occupancy,
     * clear references on bills, delete order items, clear order_id from
     * service requests and payments, remove the order row, then log audit
     * and realtime events.  This method is idempotent; if the order does
     * not exist it returns false.
     *
     * @param string $brandId
     * @param string $branchId
     * @param string $orderId
     * @param string $actorUserId
     * @return bool
     */
    public function deleteOrder(string $brandId, string $branchId, string $orderId, string $actorUserId = ''): bool
    {
        if (!$this->tableExists('orders')) {
            return false;
        }

        $existing = $this->getOrderDetail($brandId, $branchId, $orderId);
        if (!$existing) {
            return false;
        }

        $this->begin();

        try {
            // Clear references on tables (current_order_id)
            $tableTable = $this->firstExistingTable(['tables', 'restaurant_tables']);
            if ($tableTable !== null && $this->columnExists($tableTable, 'current_order_id')) {
                $this->execute(
                    "UPDATE {$tableTable} SET current_order_id = NULL WHERE current_order_id = ?",
                    [$orderId]
                );
            }

            // Clear order_id references on bills
            if ($this->tableExists('bills') && $this->columnExists('bills', 'order_id')) {
                $this->execute(
                    "UPDATE bills SET order_id = NULL WHERE order_id = ?",
                    [$orderId]
                );
            }

            // Delete order items
            if ($this->tableExists('order_items') && $this->columnExists('order_items', 'order_id')) {
                $this->execute(
                    "DELETE FROM order_items WHERE order_id = ?",
                    [$orderId]
                );
            }

            // Clear order_id on service requests
            if ($this->tableExists('service_requests') && $this->columnExists('service_requests', 'order_id')) {
                $this->execute(
                    "UPDATE service_requests SET order_id = NULL WHERE order_id = ?",
                    [$orderId]
                );
            }

            // Delete payments associated with this order
            if ($this->tableExists('payments') && $this->columnExists('payments', 'order_id')) {
                $this->execute(
                    "DELETE FROM payments WHERE order_id = ?",
                    [$orderId]
                );
            }

            // Delete the order
            $ok = $this->execute(
                "DELETE FROM orders WHERE id = ? AND brand_id = ? AND branch_id = ?",
                [$orderId, $brandId, $branchId]
            );
            if (!$ok) {
                throw new RuntimeException('Unable to delete order.');
            }

            // Audit and realtime event
            $this->auditLog($brandId, $branchId, $actorUserId, 'order', $orderId, 'delete', $existing, null);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.order.deleted', 'order', $orderId, null);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::deleteOrder failed: ' . $e->getMessage());
            return false;
        }
    }
    public function updateOrderStatus(string $brandId, string $branchId, string $orderId, string $status, string $note = '', string $actorUserId = ''): bool
    {
        if (!$this->tableExists('orders')) {
            return false;
        }

        $status = $this->safeOrderStatus($status);
        $existing = $this->getOrderDetail($brandId, $branchId, $orderId);

        if (!$existing) {
            return false;
        }

        $this->begin();

        try {
            $now = $this->now();

            $ok1 = $this->execute(
                "UPDATE orders
                 SET status = ?, updated_at = ?
                 WHERE id = ? AND brand_id = ? AND branch_id = ?",
                [$status, $now, $orderId, $brandId, $branchId]
            );

            $ok2 = true;
            if ($this->tableExists('order_items') && in_array($status, ['accepted', 'preparing', 'ready', 'served', 'cancelled'], true)) {
                $setParts = ['status = ?'];
                $params = [$status];

                if ($this->columnExists('order_items', 'status_changed_at')) {
                    $setParts[] = 'status_changed_at = ?';
                    $params[] = $now;
                }

                if ($this->columnExists('order_items', 'updated_at')) {
                    $setParts[] = 'updated_at = ?';
                    $params[] = $now;
                }

                $params[] = $orderId;

                $ok2 = $this->execute(
                    "UPDATE order_items
                     SET " . implode(', ', $setParts) . "
                     WHERE order_id = ?
                       AND status NOT IN ('served', 'cancelled')",
                    $params
                );
            }

            if ($status === 'served' || $status === 'completed') {
                $orderRow = $this->one(
                    "SELECT table_id FROM orders WHERE id = ? LIMIT 1",
                    [$orderId]
                );

                if ($orderRow) {
                    $tableId = (string) ($orderRow['table_id'] ?? '');
                    $tableTable = $this->firstExistingTable(['tables', 'restaurant_tables']);

                    if ($tableId !== '' && $tableTable !== null) {
                        $setParts = [];
                        $params = [];

                        if ($this->columnExists($tableTable, 'status')) {
                            $tableStatus = 'occupied';
                            $setParts[] = 'status = ?';
                            $params[] = $tableStatus;
                        }

                        if ($this->columnExists($tableTable, 'status_updated_at')) {
                            $setParts[] = 'status_updated_at = ?';
                            $params[] = $now;
                        }

                        if ($this->columnExists($tableTable, 'updated_at')) {
                            $setParts[] = 'updated_at = ?';
                            $params[] = $now;
                        }

                        if ($setParts !== []) {
                            $params[] = $tableId;

                            $this->execute(
                                "UPDATE {$tableTable}
                                 SET " . implode(', ', $setParts) . "
                                 WHERE id = ?",
                                $params
                            );
                        }
                    }
                }
            }

            if (!$ok1 || !$ok2) {
                throw new RuntimeException('Unable to update order status.');
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'order', $orderId, 'update_status', $existing, ['status' => $status, 'note' => $note]);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.order.status_changed', 'order', $orderId, ['order_id' => $orderId, 'status' => $status, 'note' => $note]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::updateOrderStatus failed: ' . $e->getMessage());
            return false;
        }
    }

    public function resolveRequest(string $brandId, string $branchId, string $requestId, string $status = 'resolved', string $note = '', string $actorUserId = ''): bool
    {
        if (!$this->tableExists('service_requests')) {
            return false;
        }

        $status = $this->safeRequestStatus($status);

        $existing = $this->one(
            "SELECT *
             FROM service_requests
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?
             LIMIT 1",
            [$requestId, $brandId, $branchId]
        );

        if (!$existing) {
            return false;
        }

        $setParts = ['status = ?'];
        $params = [$status];
        $now = $this->now();

        if ($this->columnExists('service_requests', 'assigned_to_user_id') && $actorUserId !== '') {
            $setParts[] = 'assigned_to_user_id = ?';
            $params[] = $actorUserId;
        }

        if ($this->columnExists('service_requests', 'acknowledged_at') && in_array($status, ['approved', 'closed'], true)) {
            $setParts[] = 'acknowledged_at = COALESCE(acknowledged_at, ?)';
            $params[] = $now;
        }

        if ($this->columnExists('service_requests', 'resolved_at') && in_array($status, ['closed', 'cancelled'], true)) {
            $setParts[] = 'resolved_at = ?';
            $params[] = $now;
        }

        if ($this->columnExists('service_requests', 'updated_at')) {
            $setParts[] = 'updated_at = ?';
            $params[] = $now;
        }

        if ($this->columnExists('service_requests', 'resolution_note')) {
            $setParts[] = 'resolution_note = ?';
            $params[] = $note;
        }

        $params[] = $requestId;
        $params[] = $brandId;
        $params[] = $branchId;

        $this->begin();

        try {
            $ok = $this->execute(
                "UPDATE service_requests
                 SET " . implode(', ', $setParts) . "
                 WHERE id = ?
                   AND brand_id = ?
                   AND branch_id = ?",
                $params
            );

            if (!$ok) {
                throw new RuntimeException('Unable to resolve request.');
            }

            if ($this->tableExists('notifications')) {
                if ($this->columnExists('notifications', 'request_id')) {
                    $this->execute(
                        "UPDATE notifications
             SET is_read = 1
             WHERE brand_id = ?
               AND branch_id = ?
               AND request_id = ?",
                        [$brandId, $branchId, $requestId]
                    );
                } else {
                    $this->execute(
                        "UPDATE notifications
             SET is_read = 1
             WHERE brand_id = ?
               AND branch_id = ?
               AND payload_json LIKE ?",
                        [$brandId, $branchId, '%"request_id":"' . $requestId . '"%']
                    );
                }
            }

            if (strtolower((string)($existing['request_type'] ?? '')) === 'bill') {
                $this->syncBillStatusFromRequest(
                    $brandId,
                    $branchId,
                    $requestId,
                    $status,
                    $actorUserId
                );
            }

            $this->auditLog($brandId, $branchId, $actorUserId, 'service_request', $requestId, 'resolve', $existing, ['status' => $status, 'note' => $note]);
            $this->pushRealtimeEvent($brandId, $branchId, 'manager.request.resolved', 'service_request', $requestId, ['request_id' => $requestId, 'status' => $status, 'note' => $note]);

            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollback();
            error_log('RestManagerModel::resolveRequest failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getUserSettings(string $userId, string $role = 'manager'): array
    {
        if (!$this->tableExists('user_settings')) {
            return $this->defaultUserSettings();
        }

        $row = $this->one(
            "SELECT theme, lite_notification_sound, sound_alerts_enabled
         FROM user_settings
         WHERE user_id = ? AND role = ?
         LIMIT 1",
            [$userId, $role]
        );

        if (!$row) {
            return $this->defaultUserSettings();
        }

        return [
            'theme' => in_array((string)$row['theme'], ['light', 'dark'], true) ? (string)$row['theme'] : 'light',
            'compact' => false,
            'animations' => true,
            'sound' => (int)($row['sound_alerts_enabled'] ?? 1) === 1,
            'desktopAlerts' => false,
            'notificationSound' => (string)($row['lite_notification_sound'] ?? ''),
        ];
    }

    public function saveUserSettings(string $userId, string $role, array $payload): array
    {
        if (!$this->tableExists('user_settings')) {
            throw new RuntimeException('user_settings table not found.');
        }

        $theme = in_array((string)($payload['theme'] ?? 'light'), ['light', 'dark'], true)
            ? (string)$payload['theme']
            : 'light';

        $sound = (string)($payload['sound_alerts_enabled'] ?? '1');
        $soundEnabled = in_array(strtolower($sound), ['1', 'true', 'yes', 'on'], true) ? 1 : 0;

        $notificationSound = trim((string)($payload['lite_notification_sound'] ?? ''));

        $existing = $this->one(
            "SELECT id FROM user_settings WHERE user_id = ? AND role = ? LIMIT 1",
            [$userId, $role]
        );

        if ($existing) {
            $this->execute(
                "UPDATE user_settings
             SET theme = ?, lite_notification_sound = ?, sound_alerts_enabled = ?, updated_at = ?
             WHERE user_id = ? AND role = ?",
                [$theme, $notificationSound, $soundEnabled, $this->now(), $userId, $role]
            );
        } else {
            $this->execute(
                "INSERT INTO user_settings
             (id, user_id, role, theme, lite_notification_sound, sound_alerts_enabled, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [$this->uuid(), $userId, $role, $theme, $notificationSound, $soundEnabled, $this->now(), $this->now()]
            );
        }

        return $this->getUserSettings($userId, $role);
    }



    protected function defaultUserSettings(): array
    {
        return [
            'theme' => 'light',
            'compact' => false,
            'animations' => true,
            'sound' => true,
            'desktopAlerts' => false,
            'notificationSound' => '',
        ];
    }
}
