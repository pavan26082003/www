<?php
declare(strict_types=1);

namespace App\Models;

require_once __DIR__ . '/RestBaseModel.php';

use RuntimeException;
use Throwable;

final class RestMainModel extends RestBaseModel
{
    /**
     * @return array<string, mixed>
     */
    public function getMainContext(array $runtimeContext): array
    {
        $this->setRuntimeContext($runtimeContext);

        $brandId = $this->runtimeBrandId();
        $branchId = $this->runtimeBranchId();
        $userId = $this->runtimeUserId();
        $role = $this->normalizeRole($this->runtimeUserRole());

        return [
            'brand_id' => $brandId,
            'branch_id' => $branchId,
            'user_id' => $userId,
            'role' => $role,
            'brand' => $this->getBrandContext($brandId),
            'branch' => $this->getBranchContext($brandId, $branchId),
            'user' => $this->getUserContext($userId, $brandId, $branchId),
            'stats' => $this->getStats($brandId, $branchId),
            'visible_modules' => $this->resolveVisibleModules($role),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getBrandContext(string $brandId): array
    {
        $fallback = [
            'id' => $brandId,
            'name' => 'Restaurant System',
            'legal_name' => '',
            'logo' => '',
            'favicon' => '',
            'mail_id' => '',
            'mobile' => '',
            'status' => 'active',
            'timezone' => 'Asia/Kolkata',
            'currency' => 'INR',
            'subscription_plan' => 'starter',
            'is_setup_completed' => 0,
        ];

        if (!$this->tableExists('brands')) {
            return $fallback;
        }

        try {
            $row = $this->one(
                "
                SELECT
                    id,
                    name,
                    legal_name,
                    logo,
                    favicon,
                    mail_id,
                    mobile,
                    status,
                    timezone,
                    currency,
                    subscription_plan,
                    is_setup_completed
                FROM brands
                WHERE id = ?
                LIMIT 1
                ",
                [$brandId]
            );

            if (!is_array($row)) {
                return $fallback;
            }

            return [
                'id' => (string) ($row['id'] ?? $brandId),
                'name' => trim((string) ($row['name'] ?? 'Restaurant System')),
                'legal_name' => trim((string) ($row['legal_name'] ?? '')),
                'logo' => $this->normalizeAssetPath((string) ($row['logo'] ?? '')),
                'favicon' => $this->normalizeAssetPath((string) ($row['favicon'] ?? '')),
                'mail_id' => trim((string) ($row['mail_id'] ?? '')),
                'mobile' => trim((string) ($row['mobile'] ?? '')),
                'status' => strtolower(trim((string) ($row['status'] ?? 'active'))),
                'timezone' => trim((string) ($row['timezone'] ?? 'Asia/Kolkata')),
                'currency' => strtoupper(trim((string) ($row['currency'] ?? 'INR'))),
                'subscription_plan' => strtolower(trim((string) ($row['subscription_plan'] ?? 'starter'))),
                'is_setup_completed' => (int) ($row['is_setup_completed'] ?? 0),
            ];
        } catch (Throwable $e) {
            return $fallback;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function getBranchContext(string $brandId, string $branchId): array
    {
        $fallback = [
            'id' => $branchId,
            'brand_id' => $brandId,
            'name' => 'Main Branch',
            'code' => '',
            'city' => '',
            'address' => '',
            'status' => 'active',
            'phone' => '',
            'email' => '',
        ];

        if (!$this->tableExists('branches')) {
            return $fallback;
        }

        try {
            $row = $this->one(
                "
                SELECT
                    id,
                    brand_id,
                    name,
                    code,
                    city,
                    address,
                    status,
                    phone,
                    email
                FROM branches
                WHERE id = ?
                  AND brand_id = ?
                LIMIT 1
                ",
                [$branchId, $brandId]
            );

            if (!is_array($row)) {
                return $fallback;
            }

            return [
                'id' => (string) ($row['id'] ?? $branchId),
                'brand_id' => (string) ($row['brand_id'] ?? $brandId),
                'name' => trim((string) ($row['name'] ?? 'Main Branch')),
                'code' => trim((string) ($row['code'] ?? '')),
                'city' => trim((string) ($row['city'] ?? '')),
                'address' => trim((string) ($row['address'] ?? '')),
                'status' => strtolower(trim((string) ($row['status'] ?? 'active'))),
                'phone' => trim((string) ($row['phone'] ?? '')),
                'email' => trim((string) ($row['email'] ?? '')),
            ];
        } catch (Throwable $e) {
            return $fallback;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function getUserContext(string $userId, string $brandId, string $branchId): array
    {
        $fallback = [
            'id' => $userId,
            'name' => '',
            'email' => (string) ($this->runtimeContext['email'] ?? ''),
            'role' => $this->normalizeRole((string) ($this->runtimeContext['role'] ?? '')),
            'employee_code' => '',
            'profile_pic' => '',
            'brand_id' => $brandId,
            'branch_id' => $branchId,
        ];

        if (!$this->tableExists('users')) {
            return $fallback;
        }

        $selectColumns = [
            'u.id',
            'u.email',
        ];

        if ($this->columnExists('users', 'name')) {
            $selectColumns[] = 'u.name';
        }

        if ($this->columnExists('users', 'full_name')) {
            $selectColumns[] = 'u.full_name';
        }

        if ($this->columnExists('users', 'username')) {
            $selectColumns[] = 'u.username';
        }

        if ($this->columnExists('users', 'role_type')) {
            $selectColumns[] = 'u.role_type';
        }

        if ($this->columnExists('users', 'employee_code')) {
            $selectColumns[] = 'u.employee_code';
        }

        if ($this->columnExists('users', 'profile_pic')) {
            $selectColumns[] = 'u.profile_pic';
        }

        if ($this->columnExists('users', 'brand_id')) {
            $selectColumns[] = 'u.brand_id';
        }

        if ($this->columnExists('users', 'default_branch_id')) {
            $selectColumns[] = 'u.default_branch_id';
        }

        try {
            $row = $this->one(
                "
                SELECT " . implode(",\n", $selectColumns) . "
                FROM users u
                WHERE u.id = ?
                LIMIT 1
                ",
                [$userId]
            );

            if (!is_array($row)) {
                return $fallback;
            }

            $displayName = trim((string) (
                $row['name']
                ?? $row['full_name']
                ?? $row['username']
                ?? ''
            ));

            return [
                'id' => (string) ($row['id'] ?? $userId),
                'name' => $displayName,
                'email' => trim((string) ($row['email'] ?? $fallback['email'])),
                'role' => $this->normalizeRole((string) ($row['role_type'] ?? $fallback['role'])),
                'employee_code' => trim((string) ($row['employee_code'] ?? '')),
                'profile_pic' => $this->normalizeAssetPath((string) ($row['profile_pic'] ?? '')),
                'brand_id' => trim((string) ($row['brand_id'] ?? $brandId)),
                'branch_id' => trim((string) ($row['default_branch_id'] ?? $branchId)),
            ];
        } catch (Throwable $e) {
            return $fallback;
        }
    }

    /**
     * @return array<string, int>
     */
    private function getStats(string $brandId, string $branchId): array
    {
        return [
            'branches_count' => $this->safeCount(
                'branches',
                "
                SELECT COUNT(*) AS total
                FROM branches
                WHERE brand_id = ?
                ",
                [$brandId]
            ),
            'total_tables' => $this->safeCount(
                'tables',
                "
                SELECT COUNT(*) AS total
                FROM tables
                WHERE brand_id = ?
                  AND branch_id = ?
                ",
                [$brandId, $branchId]
            ),
            'active_tables' => $this->safeCount(
                'tables',
                "
                SELECT COUNT(*) AS total
                FROM tables
                WHERE brand_id = ?
                  AND branch_id = ?
                  AND LOWER(COALESCE(status, '')) IN ('occupied', 'reserved', 'running', 'filled')
                ",
                [$brandId, $branchId]
            ),
            'today_orders' => $this->safeCount(
                'orders',
                "
                SELECT COUNT(*) AS total
                FROM orders
                WHERE brand_id = ?
                  AND branch_id = ?
                  AND DATE(created_at) = CURDATE()
                ",
                [$brandId, $branchId]
            ),
            'pending_requests' => $this->safeCount(
                'service_requests',
                "
                SELECT COUNT(*) AS total
                FROM service_requests
                WHERE brand_id = ?
                  AND branch_id = ?
                  AND LOWER(COALESCE(status, 'pending')) IN ('pending', 'new', 'open', 'accepted')
                ",
                [$brandId, $branchId]
            ),
            'live_kitchen_tickets' => $this->safeCount(
                'orders',
                "
                SELECT COUNT(*) AS total
                FROM orders
                WHERE brand_id = ?
                  AND branch_id = ?
                  AND LOWER(COALESCE(status, '')) IN ('placed', 'accepted', 'preparing', 'ready')
                ",
                [$brandId, $branchId]
            ),
        ];
    }

    private function safeCount(string $table, string $sql, array $params): int
    {
        if (!$this->tableExists($table)) {
            return 0;
        }

        try {
            return (int) $this->scalar($sql, $params, 0);
        } catch (Throwable $e) {
            return 0;
        }
    }

    /**
     * @return array<int, string>
     */
    private function resolveVisibleModules(string $role): array
    {
        $role = $this->normalizeRole($role);

        switch ($role) {
            case 'brand':
            case 'owner':
                return ['brand', 'customer'];

            case 'manager':
                return ['manager', 'customer'];

            case 'waiter':
                return ['waiter'];

            case 'chef':
            case 'kitchen':
                return ['kitchen'];

            case 'pad':
            case 'customer':
                return ['customer'];

            default:
                return ['customer'];
        }
    }

    private function normalizeRole(string $role): string
    {
        $role = strtolower(trim($role));

        $map = [
            'owner' => 'brand',
            'brand_owner' => 'brand',
            'admin' => 'brand',
            'branch_manager' => 'manager',
            'floor_manager' => 'manager',
            'captain' => 'waiter',
            'server' => 'waiter',
            'steward' => 'waiter',
            'chef_station' => 'kitchen',
            'cook' => 'kitchen',
            'kitchen_staff' => 'kitchen',
            'tablet' => 'pad',
            'guest' => 'customer',
        ];

        return $map[$role] ?? $role;
    }

    private function normalizeAssetPath(string $path): string
    {
        $path = trim($path);

        if ($path === '') {
            return '';
        }

        if (
            str_starts_with($path, 'http://') ||
            str_starts_with($path, 'https://') ||
            str_starts_with($path, '//') ||
            str_starts_with($path, 'data:')
        ) {
            return $path;
        }

        $path = str_replace('\\', '/', $path);
        $restaurantAssetsPos = stripos($path, 'RestaurantAssets/');
        if ($restaurantAssetsPos !== false) {
            $path = substr($path, $restaurantAssetsPos);
        }

        $path = ltrim($path, '/');

        if (stripos($path, 'RestaurantAssets/') === 0) {
            $path = substr($path, strlen('RestaurantAssets/'));
        }

        return ltrim($path, '/');
    }
}