<?php
declare(strict_types=1);

namespace App\Models;

use RuntimeException;
use Throwable;

require_once APPPATH . 'Models/RestBaseModel.php';

final class RestKitchenModel extends RestBaseModel
{
    private const DEFAULT_SOUND = 'RestaurantAssets/AlertSounds/Sound1.mpeg';

    private const ACTIVE_KITCHEN_ITEM_STATUSES = [
        'placed',
        'pending',
        'new',
        'accepted',
        'preparing',
        'cooking',
        'ready',
        'delayed',
    ];

    public function getKitchenPageData(string $brandId, string $branchId, string $userId): array
    {
        $brandId = $this->requireBrandId($brandId);
        $branchId = $this->requireBranchId($branchId);
        $userId = $this->requireUserId($userId);
        $snapshot = $this->buildKitchenSnapshot($brandId, $branchId, $userId);
        $orders = is_array($snapshot['orders'] ?? null) ? $snapshot['orders'] : [];

        return [
            'brandId'                  => $brandId,
            'branchId'                 => $branchId,
            'userId'                   => $userId,
            'brand'                    => $this->getBrand($brandId),
            'branch'                   => $this->getBranch($brandId, $branchId),
            'context'                  => is_array($snapshot['context'] ?? null) ? $snapshot['context'] : [],
            'summary'                  => is_array($snapshot['summary'] ?? null) ? $snapshot['summary'] : [],
            'orders'                   => $orders,
            'selectedOrder'            => $orders[0] ?? null,
            'stations'                 => is_array($snapshot['stations'] ?? null) ? $snapshot['stations'] : [],
            'snapshot'                 => $snapshot,
            'userSettings'             => $this->getUserSettings($brandId, $branchId, $userId),
            'defaultNotificationSound' => self::DEFAULT_SOUND,
            'apiBase'                  => site_url('restaurant-system/kitchen/api'),
        ];
    }

    public function buildKitchenSnapshot(string $brandId, string $branchId, string $userId): array
    {
        $brandId = $this->requireBrandId($brandId);
        $branchId = $this->requireBranchId($branchId);
        $userId = $this->requireUserId($userId);

        $orders = $this->getKitchenOrders($brandId, $branchId);

        return [
            'context'  => $this->getKitchenContext($brandId, $branchId, $userId),
            'summary'  => $this->buildSummary($orders),
            'orders'   => $orders,
            'stations' => $this->buildStations($orders),
            'settings' => $this->getUserSettings($brandId, $branchId, $userId),
        ];
    }

    public function getKitchenOrderDetail(string $brandId, string $branchId, string $orderId): array
    {
        $brandId = $this->requireBrandId($brandId);
        $branchId = $this->requireBranchId($branchId);
        $orderId = $this->normalizeUuid($orderId, 'Order ID');

        $order = $this->one(
            "
            SELECT
                o.id,
                o.brand_id,
                o.branch_id,
                o.table_id,
                o.assigned_waiter_id,
                o.order_number,
                o.status,
                o.customer_count,
                o.notes,
                o.subtotal,
                o.tax_amount,
                o.discount_amount,
                o.grand_total,
                o.opened_at,
                o.created_at,
                o.updated_at,
                t.table_code,
                t.table_number
            FROM orders o
            LEFT JOIN tables t ON t.id = o.table_id
            WHERE o.id = ?
              AND o.brand_id = ?
              AND o.branch_id = ?
            LIMIT 1
            ",
            [$orderId, $brandId, $branchId]
        );

        if (!$order) {
            return [];
        }

        $items = $this->getOrderItems($brandId, $branchId, $orderId);
        $rawStatus = (string)($order['status'] ?? '');
        $status = $this->isActiveKitchenDbStatus($rawStatus)
            ? $this->normalizeUiStatus($rawStatus)
            : $this->deriveOrderStatusFromItems($items);

        return [
            'id'             => (string)$order['id'],
            'order_id'       => (string)$order['id'],
            'kot'            => (string)($order['order_number'] ?? ''),
            'order_number'   => (string)($order['order_number'] ?? ''),
            'table'          => (string)($order['table_code'] ?? $order['table_number'] ?? '-'),
            'table_code'     => (string)($order['table_code'] ?? '-'),
            'status'         => $status,
            'status_label'   => $this->statusLabel($status),
            'priority'       => $this->priorityForOrder($order),
            'customer_count' => (int)($order['customer_count'] ?? 0),
            'grand_total'    => (float)($order['grand_total'] ?? 0),
            'elapsed'        => $this->timeAgo((string)($order['created_at'] ?? $order['opened_at'] ?? '')),
            'items_count'    => count($items),
            'items'          => $items,
            'waiter_called'  => $this->hasWaiterCallNotification($brandId, $branchId, $orderId),
        ];
    }

    public function getUserSettings(string $brandId, string $branchId, string $userId): array
    {
        $this->requireBrandId($brandId);
        $this->requireBranchId($branchId);
        $userId = $this->requireUserId($userId);

        $role = $this->resolveSettingsRole($userId, $branchId);

        $row = $this->one(
            "
            SELECT
                id,
                user_id,
                role,
                theme,
                lite_notification_sound,
                sound_alerts_enabled,
                created_at,
                updated_at
            FROM user_settings
            WHERE user_id = ?
              AND role = ?
            LIMIT 1
            ",
            [$userId, $role]
        );

        if (!$row) {
            return [
                'role'                    => $role,
                'theme'                   => 'light',
                'lite_notification_sound' => self::DEFAULT_SOUND,
                'sound_alerts_enabled'    => 1,
            ];
        }

        return [
            'role'                    => (string)$row['role'],
            'theme'                   => in_array((string)$row['theme'], ['light', 'dark'], true) ? (string)$row['theme'] : 'light',
            'lite_notification_sound' => (string)($row['lite_notification_sound'] ?: self::DEFAULT_SOUND),
            'sound_alerts_enabled'    => (int)$row['sound_alerts_enabled'] === 1 ? 1 : 0,
        ];
    }

    public function updateUserSettings(string $brandId, string $branchId, string $userId, array $payload): array
    {
        $this->requireBrandId($brandId);
        $branchId = $this->requireBranchId($branchId);
        $userId = $this->requireUserId($userId);

        $role = $this->resolveSettingsRole($userId, $branchId);
        $existing = $this->one(
            "SELECT id FROM user_settings WHERE user_id = ? AND role = ? LIMIT 1",
            [$userId, $role]
        );

        $current = $this->getUserSettings($brandId, $branchId, $userId);

        $theme = array_key_exists('theme', $payload)
            ? strtolower(trim((string)$payload['theme']))
            : $current['theme'];

        if (!in_array($theme, ['light', 'dark'], true)) {
            throw new RuntimeException('Invalid theme value.');
        }

        $soundEnabled = array_key_exists('sound_alerts_enabled', $payload)
            ? ((int)$payload['sound_alerts_enabled'] === 1 ? 1 : 0)
            : (int)$current['sound_alerts_enabled'];

        $sound = array_key_exists('lite_notification_sound', $payload)
            ? trim((string)($payload['lite_notification_sound'] ?? ''))
            : (string)$current['lite_notification_sound'];

        $sound = $this->sanitizeNotificationSound($sound !== '' ? $sound : self::DEFAULT_SOUND);

        if ($existing) {
            $this->execute(
                "
                UPDATE user_settings
                SET theme = ?,
                    lite_notification_sound = ?,
                    sound_alerts_enabled = ?,
                    updated_at = NOW()
                WHERE id = ?
                LIMIT 1
                ",
                [$theme, $sound, $soundEnabled, (string)$existing['id']]
            );
        } else {
            $this->execute(
                "
                INSERT INTO user_settings
                    (id, user_id, role, theme, lite_notification_sound, sound_alerts_enabled, created_at, updated_at)
                VALUES
                    (?, ?, ?, ?, ?, ?, NOW(), NOW())
                ",
                [$this->uuid(), $userId, $role, $theme, $sound, $soundEnabled]
            );
        }

        return $this->getUserSettings($brandId, $branchId, $userId);
    }

    public function acceptOrder(string $brandId, string $branchId, string $orderId, string $userId): bool
    {
        return $this->updateWholeOrderStatus($brandId, $branchId, $orderId, 'accepted', $userId);
    }

    public function startPreparing(string $brandId, string $branchId, string $orderId, string $userId): bool
    {
        return $this->updateWholeOrderStatus($brandId, $branchId, $orderId, 'preparing', $userId);
    }

    public function markReady(string $brandId, string $branchId, string $orderId, string $userId): bool
    {
        return $this->updateWholeOrderStatus($brandId, $branchId, $orderId, 'ready', $userId);
    }

    public function markDelayed(string $brandId, string $branchId, string $orderId, string $userId): bool
    {
        throw new RuntimeException('Kitchen cannot mark orders as delayed. Priority is calculated automatically.');
    }

    public function markServed(string $brandId, string $branchId, string $orderId, string $userId): bool
    {
        throw new RuntimeException('Kitchen cannot mark orders as served. Waiter must mark ready orders as served.');
    }

    public function cancelOrder(string $brandId, string $branchId, string $orderId, string $userId): bool
    {
        return $this->updateWholeOrderStatus($brandId, $branchId, $orderId, 'cancelled', $userId);
    }

    public function updateOrderItemStatus(
        string $brandId,
        string $branchId,
        string $orderId,
        string $orderItemId,
        string $status,
        string $userId
    ): bool {
        $brandId = $this->requireBrandId($brandId);
        $branchId = $this->requireBranchId($branchId);
        $orderId = $this->normalizeUuid($orderId, 'Order ID');
        $orderItemId = $this->normalizeUuid($orderItemId, 'Order item ID');
        $userId = $this->requireUserId($userId);
        $status = $this->normalizeDbStatus($status);
        if (!$this->isKitchenWritableStatus($status)) {
            throw new RuntimeException('Kitchen item status can only be accepted, preparing, ready, or cancelled.');
        }


        $order = $this->getOrderForWrite($brandId, $branchId, $orderId);

        if (!$order) {
            throw new RuntimeException('Order not found.');
        }

        $this->db()->begin_transaction();

        try {
            $stampColumns = $this->itemStampColumns($status);

            $this->execute(
                "
                UPDATE order_items
                SET status = ?,
                    status_changed_at = NOW(),
                    {$stampColumns}
                    updated_at = NOW()
                WHERE id = ?
                  AND order_id = ?
                  AND brand_id = ?
                  AND branch_id = ?
                LIMIT 1
                ",
                [$status, $orderItemId, $orderId, $brandId, $branchId]
            );

            $this->syncOrderStatusFromItems($brandId, $branchId, $orderId);
            $this->syncTableAfterOrderMutation($brandId, $branchId, $orderId);
            $this->writeAudit($brandId, $branchId, $userId, 'kitchen_item_status_updated', 'order_items', $orderItemId, [
                'order_id' => $orderId,
                'status'   => $status,
            ]);

            if ($status === 'ready') {
                $this->notifyAssignedWaiter($brandId, $branchId, $orderId, 'order_ready');
            }

            $this->db()->commit();
            return true;
        } catch (Throwable $e) {
            $this->db()->rollback();
            throw $e;
        }
    }

    public function callWaiter(string $brandId, string $branchId, string $orderId, string $userId): bool
    {
        $brandId = $this->requireBrandId($brandId);
        $branchId = $this->requireBranchId($branchId);
        $orderId = $this->normalizeUuid($orderId, 'Order ID');
        $userId = $this->requireUserId($userId);

        $this->notifyAssignedWaiter($brandId, $branchId, $orderId, 'kitchen_call_waiter', true);
        $this->writeAudit($brandId, $branchId, $userId, 'kitchen_called_waiter', 'orders', $orderId, [
            'order_id' => $orderId,
        ]);

        return true;
    }

    private function updateWholeOrderStatus(
        string $brandId,
        string $branchId,
        string $orderId,
        string $status,
        string $userId
    ): bool {
        $brandId = $this->requireBrandId($brandId);
        $branchId = $this->requireBranchId($branchId);
        $orderId = $this->normalizeUuid($orderId, 'Order ID');
        $userId = $this->requireUserId($userId);
        $status = $this->normalizeDbStatus($status);
        if (!$this->isKitchenWritableStatus($status)) {
            throw new RuntimeException('Kitchen can only move orders through accepted, preparing, ready, or cancelled.');
        }


        $order = $this->getOrderForWrite($brandId, $branchId, $orderId);

        if (!$order) {
            throw new RuntimeException('Order not found.');
        }

        $this->db()->begin_transaction();

        try {
            $stampColumns = $this->itemStampColumns($status);

            $this->execute(
                "
                UPDATE order_items
                SET status = ?,
                    status_changed_at = NOW(),
                    {$stampColumns}
                    updated_at = NOW()
                WHERE order_id = ?
                  AND brand_id = ?
                  AND branch_id = ?
                  AND COALESCE(status, '') NOT IN ('cancelled')
                ",
                [$status, $orderId, $brandId, $branchId]
            );

            $this->execute(
                "
                UPDATE orders
                SET status = ?,
                    updated_at = NOW()
                WHERE id = ?
                  AND brand_id = ?
                  AND branch_id = ?
                LIMIT 1
                ",
                [$status, $orderId, $brandId, $branchId]
            );

            $this->syncTableAfterOrderMutation($brandId, $branchId, $orderId);

            if ($status === 'ready') {
                $this->notifyAssignedWaiter($brandId, $branchId, $orderId, 'order_ready');
            }

            $this->writeAudit($brandId, $branchId, $userId, 'kitchen_order_status_updated', 'orders', $orderId, [
                'order_id' => $orderId,
                'status'   => $status,
            ]);

            $this->db()->commit();
            return true;
        } catch (Throwable $e) {
            $this->db()->rollback();
            throw $e;
        }
    }

    private function getKitchenOrders(string $brandId, string $branchId): array
    {
        $rows = $this->all(
            "
            SELECT
                o.id,
                o.order_number,
                o.table_id,
                o.assigned_waiter_id,
                o.status AS order_status,
                o.customer_count,
                o.grand_total,
                o.created_at AS order_created_at,
                o.opened_at,
                o.updated_at AS order_updated_at,
                t.table_code,
                t.table_number
            FROM orders o
            LEFT JOIN tables t ON t.id = o.table_id
            WHERE o.brand_id = ?
              AND o.branch_id = ?
              AND COALESCE(o.is_active, 1) = 1
              AND (
                    COALESCE(o.status, '') IN ('placed', 'pending', 'new', 'accepted', 'preparing', 'cooking', 'ready', 'delayed')
                    OR EXISTS (
                        SELECT 1
                        FROM order_items oi
                        WHERE oi.order_id = o.id
                          AND oi.brand_id = o.brand_id
                          AND oi.branch_id = o.branch_id
                          AND COALESCE(oi.status, '') IN ('placed', 'pending', 'new', 'accepted', 'preparing', 'cooking', 'ready', 'delayed')
                          AND COALESCE(oi.is_billed, 0) = 0
                    )
              )
            ORDER BY
                CASE
                    WHEN COALESCE(o.status, '') IN ('placed', 'pending', 'new') THEN 1
                    WHEN COALESCE(o.status, '') = 'accepted' THEN 2
                    WHEN COALESCE(o.status, '') IN ('preparing', 'cooking', 'delayed') THEN 3
                    WHEN COALESCE(o.status, '') = 'ready' THEN 4
                    ELSE 5
                END,
                COALESCE(o.created_at, o.opened_at, o.updated_at) ASC
            LIMIT 100
            ",
            [$brandId, $branchId]
        );

        if (!$rows) {
            return [];
        }

        $orders = [];

        foreach ($rows as $row) {
            $orderId = (string)$row['id'];
            $items = $this->getOrderItems($brandId, $branchId, $orderId);
            $rawStatus = (string)($row['order_status'] ?? '');
            $status = $this->isActiveKitchenDbStatus($rawStatus)
                ? $this->normalizeUiStatus($rawStatus)
                : $this->deriveOrderStatusFromItems($items);

            $orders[] = [
                'id'             => $orderId,
                'order_id'       => $orderId,
                'kot'            => (string)($row['order_number'] ?? ''),
                'order_number'   => (string)($row['order_number'] ?? ''),
                'table'          => (string)($row['table_code'] ?? $row['table_number'] ?? '-'),
                'table_code'     => (string)($row['table_code'] ?? '-'),
                'status'         => $status,
                'status_label'   => $this->statusLabel($status),
                'priority'       => $this->priorityForOrder($row),
                'elapsed'        => $this->timeAgo((string)($row['order_created_at'] ?? $row['opened_at'] ?? '')),
                'items_count'    => count($items),
                'items'          => $items,
                'grand_total'    => (float)($row['grand_total'] ?? 0),
                'customer_count' => (int)($row['customer_count'] ?? 0),
                'waiter_called'  => $this->hasWaiterCallNotification($brandId, $branchId, $orderId),
            ];
        }

        return $orders;
    }

    private function getOrderItems(string $brandId, string $branchId, string $orderId): array
    {
        $rows = $this->all(
            "
            SELECT
                oi.id,
                oi.order_id,
                oi.menu_item_id,
                oi.item_name_snapshot,
                oi.quantity,
                oi.price_snapshot,
                oi.line_total,
                oi.special_instructions,
                oi.status,
                oi.created_at,
                oi.status_changed_at,
                mi.preparation_time_mins,
                mc.name AS category_name
            FROM order_items oi
            LEFT JOIN menu_items mi ON mi.id = oi.menu_item_id
            LEFT JOIN menu_categories mc ON mc.id = mi.category_id
            WHERE oi.brand_id = ?
              AND oi.branch_id = ?
              AND oi.order_id = ?
            ORDER BY COALESCE(oi.created_at, oi.updated_at) ASC
            ",
            [$brandId, $branchId, $orderId]
        );

        return array_map(function (array $row): array {
            $status = $this->normalizeUiStatus((string)($row['status'] ?? ''));

            return [
                'id'            => (string)$row['id'],
                'order_item_id' => (string)$row['id'],
                'item_id'       => (string)$row['id'],
                'name'          => (string)($row['item_name_snapshot'] ?? 'Item'),
                'item_name'     => (string)($row['item_name_snapshot'] ?? 'Item'),
                'qty'           => (int)($row['quantity'] ?? 0),
                'quantity'      => (int)($row['quantity'] ?? 0),
                'price'         => (float)($row['price_snapshot'] ?? 0),
                'line_total'    => (float)($row['line_total'] ?? 0),
                'note'          => (string)($row['special_instructions'] ?? ''),
                'station'       => (string)($row['category_name'] ?? 'Kitchen'),
                'station_name'  => (string)($row['category_name'] ?? 'Kitchen'),
                'status'        => $status,
                'item_status'   => $status,
                'status_label'  => $this->statusLabel($status),
                'prep_time'     => (int)($row['preparation_time_mins'] ?? 0),
            ];
        }, $rows);
    }

    private function getBrand(string $brandId): array
    {
        return $this->one(
            "
            SELECT id, name, legal_name, logo, favicon, mobile, mail_id, status, timezone, currency
            FROM brands
            WHERE id = ?
            LIMIT 1
            ",
            [$brandId]
        ) ?? [];
    }

    private function getBranch(string $brandId, string $branchId): array
    {
        return $this->one(
            "
            SELECT id, brand_id, name, code, address, city, state, pincode, phone, email, status, opens_at, closes_at
            FROM branches
            WHERE id = ?
              AND brand_id = ?
            LIMIT 1
            ",
            [$branchId, $brandId]
        ) ?? [];
    }

    private function getKitchenContext(string $brandId, string $branchId, string $userId): array
    {
        $brand = $this->getBrand($brandId);
        $branch = $this->getBranch($brandId, $branchId);
        $user = $this->one(
            "
            SELECT
                u.id,
                u.full_name,
                u.employee_code,
                u.email,
                u.profile_pic,
                u.phone,
                u.role_type,
                u.status,
                ua.role_at_branch,
                ua.shift_start_time,
                ua.shift_end_time,
                ua.access_status
            FROM users u
            LEFT JOIN user_access ua
                   ON ua.user_id = u.id
                  AND ua.branch_id = ?
            WHERE u.id = ?
              AND u.brand_id = ?
            LIMIT 1
            ",
            [$branchId, $userId, $brandId]
        ) ?? [];

        $role = strtolower((string)($user['role_at_branch'] ?? $user['role_type'] ?? 'chef'));

        return [
            'brand_id'       => $brandId,
            'branch_id'      => $branchId,
            'user_id'        => $userId,
            'brand_name'     => (string)($brand['name'] ?? 'Restaurant'),
            'brand_logo'     => (string)($brand['logo'] ?? ''),
            'brand_favicon'  => (string)($brand['favicon'] ?? ''),
            'currency'       => (string)($brand['currency'] ?? 'INR'),
            'timezone'       => (string)($brand['timezone'] ?? 'Asia/Kolkata'),
            'branch_name'    => (string)($branch['name'] ?? 'Branch'),
            'branch_code'    => (string)($branch['code'] ?? ''),
            'branch_city'    => (string)($branch['city'] ?? ''),
            'branch_address' => (string)($branch['address'] ?? ''),
            'branch_status'  => (string)($branch['status'] ?? ''),
            'chef_name'      => (string)($user['full_name'] ?? 'Kitchen User'),
            'chef_email'     => (string)($user['email'] ?? ''),
            'chef_phone'     => (string)($user['phone'] ?? ''),
            'chef_avatar'    => (string)($user['profile_pic'] ?? ''),
            'employee_code'  => (string)($user['employee_code'] ?? ''),
            'role'           => $role,
            'shift_name'     => $this->shiftLabel((string)($user['shift_start_time'] ?? ''), (string)($user['shift_end_time'] ?? '')),
            'status'         => (string)($user['access_status'] ?? $user['status'] ?? 'active'),
        ];
    }

    private function buildSummary(array $orders): array
    {
        $counts = [
            'new_orders'         => 0,
            'accepted'           => 0,
            'preparing'          => 0,
            'ready_to_serve'     => 0,
            'priority_attention' => 0,
            'urgent_attention'   => 0,
            'total_active'       => count($orders),
        ];

        foreach ($orders as $order) {
            $status = (string)($order['status'] ?? 'new');

            if ($status === 'new') {
                $counts['new_orders']++;
            } elseif ($status === 'accepted') {
                $counts['accepted']++;
            } elseif ($status === 'preparing') {
                $counts['preparing']++;
            } elseif ($status === 'ready') {
                $counts['ready_to_serve']++;
            }

            $priority = (string)($order['priority'] ?? 'normal');
            if ($priority === 'high' || $priority === 'urgent') {
                $counts['priority_attention']++;
            }
            if ($priority === 'urgent') {
                $counts['urgent_attention']++;
            }
        }

        return $counts;
    }

    private function buildStations(array $orders): array
    {
        $stationMap = [];

        foreach ($orders as $order) {
            foreach (($order['items'] ?? []) as $item) {
                $status = (string)($item['status'] ?? 'new');

                if (in_array($status, ['ready', 'cancelled'], true)) {
                    continue;
                }

                $station = trim((string)($item['station_name'] ?? $item['station'] ?? 'Kitchen'));
                $station = $station !== '' ? $station : 'Kitchen';

                if (!isset($stationMap[$station])) {
                    $stationMap[$station] = 0;
                }

                $stationMap[$station]++;
            }
        }

        $stations = [];

        foreach ($stationMap as $name => $count) {
            $stations[] = [
                'name'  => $name,
                'count' => $count,
                'load'  => $this->stationLoadLabel($count),
                'class' => $this->stationLoadClass($count),
            ];
        }

        usort($stations, array($this, 'sortStationsByCountDesc'));

        return $stations;
    }

    private function syncOrderStatusFromItems(string $brandId, string $branchId, string $orderId): void
    {
        $items = $this->getOrderItems($brandId, $branchId, $orderId);
        $status = $this->deriveOrderStatusFromItems($items);

        $this->execute(
            "
            UPDATE orders
            SET status = ?,
                updated_at = NOW()
            WHERE id = ?
              AND brand_id = ?
              AND branch_id = ?
            LIMIT 1
            ",
            [$status, $orderId, $brandId, $branchId]
        );

        if ($status === 'ready') {
            $this->notifyAssignedWaiter($brandId, $branchId, $orderId, 'order_ready');
        }
    }

    private function deriveOrderStatusFromItems(array $items): string
    {
        if (!$items) {
            return 'new';
        }

        $statuses = [];
        foreach ($items as $item) {
            $statuses[] = $this->normalizeUiStatus((string)($item['status'] ?? 'new'));
        }

        $unique = array_unique($statuses);
        if (count($unique) === 1) {
            return $this->normalizeDbStatus($statuses[0]);
        }

        if (in_array('preparing', $statuses, true)) {
            return 'preparing';
        }

        if (in_array('accepted', $statuses, true)) {
            return 'accepted';
        }

        if (in_array('new', $statuses, true)) {
            return 'accepted';
        }

        if (in_array('ready', $statuses, true)) {
            return 'ready';
        }

        return 'new';
    }

    private function syncTableAfterOrderMutation(string $brandId, string $branchId, string $orderId): void
    {
        $order = $this->one(
            "
            SELECT id, table_id, bill_id, status
            FROM orders
            WHERE id = ?
              AND brand_id = ?
              AND branch_id = ?
            LIMIT 1
            ",
            [$orderId, $brandId, $branchId]
        );

        if (!$order || empty($order['table_id'])) {
            return;
        }

        $status = $this->normalizeUiStatus((string)($order['status'] ?? ''));

        if ($status === 'cancelled') {
            $tableStatus = 'available';
        } else {
            $tableStatus = 'occupied';
        }

        $this->execute(
            "
            UPDATE tables
            SET status = ?,
                current_order_id = ?,
                status_updated_at = NOW(),
                updated_at = NOW()
            WHERE id = ?
              AND brand_id = ?
              AND branch_id = ?
              AND COALESCE(manual_status_override, 0) = 0
            LIMIT 1
            ",
            [$tableStatus, $orderId, (string)$order['table_id'], $brandId, $branchId]
        );
    }

    private function notifyAssignedWaiter(string $brandId, string $branchId, string $orderId, string $type, bool $force = false): void
    {
        if (!$this->tableExists('notifications')) {
            return;
        }

        $order = $this->one(
            "
            SELECT
                o.id,
                o.order_number,
                o.assigned_waiter_id,
                o.table_id,
                t.table_code
            FROM orders o
            LEFT JOIN tables t ON t.id = o.table_id
            WHERE o.id = ?
              AND o.brand_id = ?
              AND o.branch_id = ?
            LIMIT 1
            ",
            [$orderId, $brandId, $branchId]
        );

        if (!$order) {
            return;
        }

        $waiterId = (string)($order['assigned_waiter_id'] ?? '');
        if ($waiterId === '') {
            $waiterId = $this->resolveAnyActiveWaiterForBranch($branchId);
        }

        if ($waiterId === '') {
            return;
        }

        if (!$force) {
            $exists = (int)$this->scalar(
                "
                SELECT COUNT(*)
                FROM notifications
                WHERE brand_id = ?
                  AND branch_id = ?
                  AND user_id = ?
                  AND type = ?
                  AND request_id = ?
                  AND created_at >= DATE_SUB(NOW(), INTERVAL 2 MINUTE)
                ",
                [$brandId, $branchId, $waiterId, $type, $orderId],
                0
            );

            if ($exists > 0) {
                return;
            }
        }

        $title = $type === 'order_ready' ? 'Order Ready' : 'Waiter Requested';
        $tableCode = (string)($order['table_code'] ?? '-');
        $orderNumber = (string)($order['order_number'] ?? 'Order');

        $message = $type === 'order_ready'
            ? "{$orderNumber} is ready for pickup at Table {$tableCode}."
            : "{$orderNumber} needs waiter attention at Table {$tableCode}.";

        $payload = json_encode([
            'section'     => 'pickups',
            'order_id'    => $orderId,
            'order_ref'   => $orderNumber,
            'table_id'    => (string)($order['table_id'] ?? ''),
            'table_code'  => $tableCode,
            'source_role' => 'kitchen',
        ], JSON_UNESCAPED_SLASHES);

        $this->execute(
            "
            INSERT INTO notifications
                (id, brand_id, branch_id, user_id, role_target, type, title, message, request_id, payload_json, is_read, created_at)
            VALUES
                (?, ?, ?, ?, 'waiter', ?, ?, ?, ?, ?, 0, NOW())
            ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                message = VALUES(message),
                payload_json = VALUES(payload_json),
                is_read = 0,
                created_at = NOW()
            ",
            [$this->uuid(), $brandId, $branchId, $waiterId, $type, $title, $message, $orderId, $payload]
        );
    }

    private function resolveAnyActiveWaiterForBranch(string $branchId): string
    {
        $row = $this->one(
            "
            SELECT u.id
            FROM users u
            INNER JOIN user_access ua ON ua.user_id = u.id
            WHERE ua.branch_id = ?
              AND LOWER(COALESCE(ua.role_at_branch, u.role_type, '')) = 'waiter'
              AND COALESCE(ua.access_status, 'active') = 'active'
              AND COALESCE(u.is_active, 1) = 1
              AND COALESCE(u.status, 'active') = 'active'
            ORDER BY u.full_name ASC
            LIMIT 1
            ",
            [$branchId]
        );

        return (string)($row['id'] ?? '');
    }

    private function hasWaiterCallNotification(string $brandId, string $branchId, string $orderId): bool
    {
        if (!$this->tableExists('notifications')) {
            return false;
        }

        return (int)$this->scalar(
            "
            SELECT COUNT(*)
            FROM notifications
            WHERE brand_id = ?
              AND branch_id = ?
              AND request_id = ?
              AND type IN ('order_ready', 'kitchen_call_waiter')
              AND created_at >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)
            ",
            [$brandId, $branchId, $orderId],
            0
        ) > 0;
    }

    private function getOrderForWrite(string $brandId, string $branchId, string $orderId): ?array
    {
        return $this->one(
            "
            SELECT id, table_id, assigned_waiter_id, status
            FROM orders
            WHERE id = ?
              AND brand_id = ?
              AND branch_id = ?
            LIMIT 1
            ",
            [$orderId, $brandId, $branchId]
        );
    }

    private function resolveSettingsRole(string $userId, string $branchId): string
    {
        $row = $this->one(
            "
            SELECT COALESCE(ua.role_at_branch, u.role_type) AS role_name
            FROM users u
            LEFT JOIN user_access ua ON ua.user_id = u.id AND ua.branch_id = ?
            WHERE u.id = ?
            LIMIT 1
            ",
            [$branchId, $userId]
        );

        $role = strtolower(trim((string)($row['role_name'] ?? 'chef')));

        if ($role === 'kitchen' || $role === 'chef') {
            return 'chef';
        }
        if ($role === 'manager') {
            return 'manager';
        }
        if ($role === 'brand' || $role === 'owner') {
            return 'brand';
        }
        if ($role === 'waiter') {
            return 'waiter';
        }
        if ($role === 'pad') {
            return 'pad';
        }

        return 'chef';
    }

    private function normalizeUiStatus(string $status): string
    {
        $status = strtolower(trim($status));

        if ($status === 'placed' || $status === 'pending' || $status === 'new' || $status === 'requested' || $status === 'created' || $status === '') {
            return 'new';
        }
        if ($status === 'accepted' || $status === 'approved') {
            return 'accepted';
        }
        if ($status === 'preparing' || $status === 'cooking' || $status === 'processing' || $status === 'in_progress' || $status === 'delayed') {
            return 'preparing';
        }
        if ($status === 'ready' || $status === 'served' || $status === 'completed' || $status === 'closed' || $status === 'paid') {
            return 'ready';
        }
        if ($status === 'cancelled' || $status === 'canceled' || $status === 'rejected') {
            return 'cancelled';
        }

        return 'new';
    }

    private function isActiveKitchenDbStatus(string $status): bool
    {
        return in_array(strtolower(trim($status)), self::ACTIVE_KITCHEN_ITEM_STATUSES, true);
    }

    private function normalizeDbStatus(string $status): string
    {
        $status = $this->normalizeUiStatus($status);

        if ($status === 'cancelled') {
            return 'cancelled';
        }

        return $status;
    }

    private function sanitizeNotificationSound(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return self::DEFAULT_SOUND;
        }

        if (strlen($value) > 255 || strpos($value, "\0") !== false) {
            throw new RuntimeException('Notification sound path is invalid.');
        }

        $lower = strtolower($value);
        if (strpos($lower, 'javascript:') !== false || strpos($value, '..') !== false) {
            throw new RuntimeException('Notification sound path is invalid.');
        }

        return $value;
    }

    private function statusLabel(string $status): string
    {
        $status = $this->normalizeUiStatus($status);

        if ($status === 'accepted') {
            return 'Accepted';
        }
        if ($status === 'preparing') {
            return 'Preparing';
        }
        if ($status === 'ready') {
            return 'Ready';
        }
        if ($status === 'cancelled') {
            return 'Cancelled';
        }

        return 'New';
    }

    private function itemStampColumns(string $status): string
    {
        $status = $this->normalizeUiStatus($status);

        if ($status === 'ready') {
            return "prepared_at = COALESCE(prepared_at, NOW()),";
        }
        if ($status === 'cancelled') {
            return "cancelled_at = COALESCE(cancelled_at, NOW()),";
        }

        return '';
    }

    private function priorityForOrder(array $order): string
    {
        $createdAt = (string)($order['order_created_at'] ?? $order['created_at'] ?? $order['opened_at'] ?? '');
        $minutes = $this->minutesSince($createdAt);
        $status = $this->normalizeUiStatus((string)($order['status'] ?? $order['order_status'] ?? ''));

        if ($minutes >= 25) {
            return 'urgent';
        }

        if ($minutes >= 12) {
            return 'high';
        }

        return 'normal';
    }

    private function isKitchenWritableStatus(string $status): bool
    {
        $status = $this->normalizeDbStatus($status);
        return in_array($status, ['accepted', 'preparing', 'ready', 'cancelled'], true);
    }

    public function sortStationsByCountDesc(array $a, array $b): int
    {
        $left = isset($a['count']) ? (int)$a['count'] : 0;
        $right = isset($b['count']) ? (int)$b['count'] : 0;
        if ($left === $right) {
            return 0;
        }
        return $left < $right ? 1 : -1;
    }

    private function stationLoadLabel(int $count): string
    {
        if ($count >= 12) {
            return 'High';
        }

        if ($count >= 7) {
            return 'Busy';
        }

        if ($count >= 3) {
            return 'Normal';
        }

        return 'Low';
    }

    private function stationLoadClass(int $count): string
    {
        if ($count >= 12) {
            return 'high';
        }

        if ($count >= 7) {
            return 'busy';
        }

        if ($count >= 3) {
            return 'normal';
        }

        return 'low';
    }

    private function shiftLabel(string $start, string $end): string
    {
        if ($start === '' && $end === '') {
            return 'Kitchen Shift';
        }

        $startLabel = $start !== '' ? substr($start, 0, 5) : '—';
        $endLabel = $end !== '' ? substr($end, 0, 5) : '—';

        return "{$startLabel} - {$endLabel}";
    }

    private function timeAgo(string $dateTime): string
    {
        $dateTime = trim($dateTime);
        if ($dateTime === '') {
            return 'Now';
        }

        $timestamp = strtotime($dateTime);
        if (!$timestamp) {
            return 'Now';
        }

        $diff = max(0, time() - $timestamp);

        if ($diff < 60) {
            return 'Just now';
        }

        if ($diff < 3600) {
            return (string)floor($diff / 60) . ' min ago';
        }

        if ($diff < 86400) {
            return (string)floor($diff / 3600) . ' hr ago';
        }

        return (string)floor($diff / 86400) . ' day ago';
    }

    private function minutesSince(string $dateTime): int
    {
        $dateTime = trim($dateTime);
        if ($dateTime === '') {
            return 0;
        }

        $timestamp = strtotime($dateTime);
        if (!$timestamp) {
            return 0;
        }

        return max(0, (int)floor((time() - $timestamp) / 60));
    }

    private function writeAudit(
        string $brandId,
        string $branchId,
        string $userId,
        string $action,
        string $entityType,
        string $entityId,
        array $payload = []
    ): void {
        try {
            if (!$this->tableExists('audit_logs')) {
                return;
            }

            $columns = [
                'id',
                'brand_id',
                'branch_id',
                'user_id',
                'entity_type',
                'entity_id',
                'action',
                'created_at',
            ];

            $values = [
                $this->uuid(),
                $brandId,
                $branchId,
                $userId,
                $entityType,
                $entityId,
                $action,
                date('Y-m-d H:i:s'),
            ];

            if ($this->columnExists('audit_logs', 'module')) {
                array_splice($columns, 4, 0, ['module']);
                array_splice($values, 4, 0, ['kitchen']);
            }

            $payloadJson = $this->jsonEncode($payload);

            if ($this->columnExists('audit_logs', 'metadata_json')) {
                $columns[] = 'metadata_json';
                $values[] = $payloadJson;
            } elseif ($this->columnExists('audit_logs', 'new_data_json')) {
                $columns[] = 'new_data_json';
                $values[] = $payloadJson;
            }

            if ($this->columnExists('audit_logs', 'old_data_json')) {
                $columns[] = 'old_data_json';
                $values[] = null;
            }

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
                'INSERT INTO audit_logs (' . implode(', ', $columns) . ") VALUES ({$placeholders})",
                $values
            );
        } catch (Throwable $e) {
            error_log('RestKitchenModel::writeAudit failed: ' . $e->getMessage());
        }
    }

    private function jsonEncode($value): ?string
    {
        $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $json === false ? null : $json;
    }

    private function uuid(): string
    {
        $data = random_bytes(16);

        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
