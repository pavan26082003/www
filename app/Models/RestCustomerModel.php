<?php

declare(strict_types=1);

namespace App\Models;

final class RestCustomerModel extends RestBaseModel
{
    protected string $defaultBrandId = '00000000-0000-0000-0000-000000000001';
    protected string $defaultBranchId = '00000000-0000-0000-0000-000000000101';
    protected string $defaultTableCode = 'T01';

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
        return $this->entityIdOrNull($value) ?? strtolower(trim($default));
    }

    public function normalizeTableCode(?string $value, string $default = 'T01'): string
    {
        $value = strtoupper(trim((string)$value));
        if ($value === '') {
            return strtoupper($default);
        }

        return preg_replace('/[^A-Z0-9_-]/', '', $value) ?: strtoupper($default);
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

    protected function cartSessionKey(string $brandId, string $branchId, string $tableCode): string
    {
        return 'customer_cart_' . md5($brandId . '|' . $branchId . '|' . $tableCode);
    }

    public function getCart(string $brandId, string $branchId, string $tableCode): array
    {
        $key = $this->cartSessionKey($brandId, $branchId, $tableCode);
        $cart = $_SESSION[$key] ?? [];

        return is_array($cart) ? $cart : [];
    }

    public function setCart(string $brandId, string $branchId, string $tableCode, array $cart): void
    {
        $_SESSION[$this->cartSessionKey($brandId, $branchId, $tableCode)] = $cart;
    }

    public function clearCart(string $brandId, string $branchId, string $tableCode): void
    {
        unset($_SESSION[$this->cartSessionKey($brandId, $branchId, $tableCode)]);
    }

    public function rebuildCartSummary(array $cart): array
    {
        $subtotal = 0.0;
        $itemCount = 0;
        $items = [];

        foreach ($cart as $item) {
            $qty = max(1, (int)($item['quantity'] ?? 1));
            $price = (float)($item['price'] ?? $item['unit_price'] ?? 0);
            $lineTotal = round($qty * $price, 2);

            $subtotal += $lineTotal;
            $itemCount += $qty;

            $items[] = [
                'menu_item_id'  => (string)($item['menu_item_id'] ?? ''),
                'name'          => (string)($item['name'] ?? 'Menu Item'),
                'description'   => (string)($item['description'] ?? ''),
                'price'         => $price,
                'unit_price'    => $price,
                'quantity'      => $qty,
                'line_total'    => $lineTotal,
                'category_name' => (string)($item['category_name'] ?? ''),
                'image_url'     => (string)($item['image_url'] ?? ''),
                'tax_rate'      => (float)($item['tax_rate'] ?? 0),
            ];
        }

        return [
            'item_count'  => $itemCount,
            'subtotal'    => round($subtotal, 2),
            'grand_total' => round($subtotal, 2),
            'items'       => array_values($items),
        ];
    }

    public function defaultServiceMessage(string $type): string
    {
        $type = strtolower(trim($type));

        switch ($type) {
            case 'water':
                return 'Customer requested water.';
            case 'bill':
                return 'Customer requested bill.';
            case 'cleaning':
                return 'Customer requested table cleaning.';
            case 'assistance':
                return 'Customer requested assistance.';
            case 'payment_collection_cash':
                return 'Customer is ready to pay by cash.';
            case 'payment_collection_upi_verify':
                return 'Customer initiated UPI payment and needs verification.';
            case 'waiter':
            default:
                return 'Customer requested waiter service.';
        }
    }

    protected function normalizeOrderStatusValue(?string $status): string
    {
        $status = strtolower(trim((string)$status));

        if ($status === '' || $status === 'placed' || $status === 'pending') {
            return 'new';
        }

        if ($status === 'cooking') {
            return 'preparing';
        }

        if ($status === 'closed' || $status === 'paid') {
            return 'completed';
        }

        return $status;
    }

    protected function normalizeBillStatusValue(?string $status): string
    {
        $status = strtolower(trim((string)$status));

        if ($status === '' || $status === 'requested' || $status === 'generated' || $status === 'pending') {
            return 'new';
        }

        return $status;
    }

    protected function normalizePaymentStatusValue(?string $status): string
    {
        $status = strtolower(trim((string)$status));

        if ($status === '') {
            return 'unpaid';
        }

        if ($status === 'awaiting_collection' || $status === 'processing' || $status === 'pending' || $status === 'initiated') {
            return 'in_processing';
        }

        return $status;
    }

    protected function normalizeRequestStatusValue(?string $status): string
    {
        $status = strtolower(trim((string)$status));

        if ($status === '' || $status === 'pending') {
            return 'new';
        }

        if ($status === 'accepted' || $status === 'acknowledged') {
            return 'approved';
        }

        if ($status === 'resolved' || $status === 'completed') {
            return 'closed';
        }

        return $status;
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

    public function getTableByCode(string $brandId, string $branchId, string $tableCode): ?array
    {
        if (!$this->tableExists('tables')) {
            return null;
        }

        return $this->one(
            "SELECT *
             FROM tables
             WHERE brand_id = ?
               AND branch_id = ?
               AND (table_code = ? OR table_number = ?)
               AND (is_active = 1 OR is_active IS NULL)
               AND (is_blocked = 0 OR is_blocked IS NULL)
             LIMIT 1",
            [$brandId, $branchId, $tableCode, $tableCode]
        );
    }

    public function getTableById(string $brandId, string $branchId, string $tableId): ?array
    {
        if (!$this->tableExists('tables')) {
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

    public function getBranchTables(string $brandId, string $branchId): array
    {
        if (!$this->tableExists('tables')) {
            return [];
        }

        return $this->all(
            "SELECT id, table_number, table_code, status, capacity, shape
             FROM tables
             WHERE brand_id = ?
               AND branch_id = ?
               AND (is_active = 1 OR is_active IS NULL)
               AND (is_blocked = 0 OR is_blocked IS NULL)
             ORDER BY table_code ASC, table_number ASC",
            [$brandId, $branchId]
        );
    }

    public function getMenuCategories(string $brandId, string $branchId): array
    {
        if (!$this->tableExists('menu_categories')) {
            return [];
        }

        return $this->all(
            "SELECT id, brand_id, branch_id, name, description, image_url, sort_order, is_active, created_at
             FROM menu_categories
             WHERE brand_id = ?
               AND branch_id = ?
               AND is_active = 1
             ORDER BY sort_order ASC, name ASC",
            [$brandId, $branchId]
        );
    }

    public function getMenuItems(string $brandId, string $branchId): array
    {
        if (
            !$this->tableExists('branch_menu_items') ||
            !$this->tableExists('menu_items') ||
            !$this->tableExists('menu_categories')
        ) {
            return [];
        }

        return $this->all(
            "SELECT
                mi.id,
                mi.brand_id,
                mi.category_id,
                mi.default_kitchen_station_id,
                mi.name,
                mi.description,
                mi.image_url,
                mi.video_url,
                mi.video_type,
                mi.item_type,
                mi.sku,
                mi.base_price,
                mi.tax_rate,
                mi.preparation_time_mins,
                mi.is_veg,
                mi.is_vegan,
                mi.is_spicy,
                mi.is_available AS item_available,
                mc.name AS category_name,
                mc.sort_order,
                bmi.selling_price,
                bmi.is_available AS branch_available,
                bmi.stock_status,
                bmi.available_from,
                bmi.available_to
             FROM branch_menu_items bmi
             INNER JOIN menu_items mi
                ON mi.id = bmi.menu_item_id
             INNER JOIN menu_categories mc
                ON mc.id = mi.category_id
             WHERE mi.brand_id = ?
               AND bmi.branch_id = ?
               AND mc.brand_id = ?
               AND mc.branch_id = ?
               AND mc.is_active = 1
               AND mi.is_available = 1
               AND bmi.is_available = 1
               AND (bmi.stock_status IS NULL OR bmi.stock_status = 'in_stock')
             ORDER BY mc.sort_order ASC, mc.name ASC, mi.name ASC",
            [$brandId, $branchId, $brandId, $branchId]
        );
    }

    public function getMenuItemDetail(string $brandId, string $branchId, string $menuItemId): ?array
    {
        if (
            !$this->tableExists('branch_menu_items') ||
            !$this->tableExists('menu_items') ||
            !$this->tableExists('menu_categories')
        ) {
            return null;
        }

        return $this->one(
            "SELECT
                mi.*,
                mc.name AS category_name,
                bmi.selling_price,
                bmi.is_available AS branch_available,
                bmi.stock_status
             FROM menu_items mi
             INNER JOIN menu_categories mc
                ON mc.id = mi.category_id
             INNER JOIN branch_menu_items bmi
                ON bmi.menu_item_id = mi.id
             WHERE mi.id = ?
               AND mi.brand_id = ?
               AND mc.brand_id = ?
               AND mc.branch_id = ?
               AND bmi.branch_id = ?
               AND mi.is_available = 1
               AND bmi.is_available = 1
             LIMIT 1",
            [$menuItemId, $brandId, $brandId, $branchId, $branchId]
        );
    }

    public function getActiveOrderForTable(string $brandId, string $branchId, string $tableId): ?array
    {
        if (!$this->tableExists('orders')) {
            return null;
        }

        return $this->one(
            "SELECT *
             FROM orders
             WHERE brand_id = ?
               AND branch_id = ?
               AND table_id = ?
               AND is_active = 1
               AND status NOT IN ('completed', 'closed', 'paid', 'cancelled')
             ORDER BY created_at DESC
             LIMIT 1",
            [$brandId, $branchId, $tableId]
        );
    }

    public function getOrderById(string $orderId): ?array
    {
        if (!$this->tableExists('orders')) {
            return null;
        }

        return $this->one(
            "SELECT * FROM orders WHERE id = ? LIMIT 1",
            [$orderId]
        );
    }

    public function getOrderItemsForTracking(string $orderId): array
    {
        if (!$this->tableExists('order_items')) {
            return [];
        }

        $joinKitchen = $this->tableExists('kitchen_stations')
            ? "LEFT JOIN kitchen_stations ks ON ks.id = oi.kitchen_station_id"
            : '';

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
             {$joinKitchen}
             LEFT JOIN menu_items mi
                ON mi.id = oi.menu_item_id
             WHERE oi.order_id = ?
             ORDER BY oi.created_at ASC",
            [$orderId]
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
            [$orderId]
        );
    }

    public function getBillById(string $billId): ?array
    {
        if (!$this->tableExists('bills')) {
            return null;
        }

        return $this->one(
            "SELECT * FROM bills WHERE id = ? LIMIT 1",
            [$billId]
        );
    }

    public function getLatestServiceRequest(string $brandId, string $branchId, string $tableId): ?array
    {
        if (!$this->tableExists('service_requests')) {
            return null;
        }

        return $this->one(
            "SELECT *
             FROM service_requests
             WHERE brand_id = ?
               AND branch_id = ?
               AND table_id = ?
             ORDER BY COALESCE(resolved_at, acknowledged_at, requested_at, created_at) DESC, created_at DESC
             LIMIT 1",
            [$brandId, $branchId, $tableId]
        );
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

    public function createServiceRequest(
        string $brandId,
        string $branchId,
        string $tableId,
        ?string $orderId,
        string $requestType,
        string $message
    ): ?string {
        if (!$this->tableExists('service_requests')) {
            return null;
        }

        $id = $this->uuid();
        $now = $this->now();

        $ok = $this->execute(
            "INSERT INTO service_requests (
                id, brand_id, branch_id, table_id, order_id,
                request_type, message, status, requested_at, created_at
             ) VALUES (?, ?, ?, ?, ?, ?, ?, 'new', ?, ?)",
            [$id, $brandId, $branchId, $tableId, $orderId, $requestType, $message, $now, $now]
        );

        return $ok ? $id : null;
    }

    protected function nextOrderNumber(string $branchId): string
    {
        $prefix = 'ORD-' . date('Ymd') . '-';
        $total = (int)$this->scalar(
            "SELECT COUNT(*)
             FROM orders
             WHERE branch_id = ?
               AND DATE(created_at) = CURDATE()",
            [$branchId],
            0
        );

        return $prefix . str_pad((string)($total + 1), 4, '0', STR_PAD_LEFT);
    }

    public function createOrder(
        string $brandId,
        string $branchId,
        string $tableId,
        string $orderSource = 'customer_pad',
        string $orderType = 'dine_in',
        int $customerCount = 1,
        ?string $notes = null
    ): ?string {
        if (!$this->tableExists('orders')) {
            return null;
        }

        $id = $this->uuid();
        $now = $this->now();

        $ok = $this->execute(
            "INSERT INTO orders (
                id, brand_id, branch_id, table_id, order_number,
                order_source, order_type, status, is_active, customer_count,
                notes, subtotal, tax_amount, service_charge, discount_amount,
                grand_total, opened_at, created_at, updated_at
             ) VALUES (?, ?, ?, ?, ?, ?, ?, 'new', 1, ?, ?, 0, 0, 0, 0, 0, ?, ?, ?)",
            [
                $id,
                $brandId,
                $branchId,
                $tableId,
                $this->nextOrderNumber($branchId),
                $orderSource,
                $orderType,
                $customerCount,
                $notes,
                $now,
                $now,
                $now,
            ]
        );

        return $ok ? $id : null;
    }

    public function createOrderItem(
        string $brandId,
        string $branchId,
        string $orderId,
        string $menuItemId,
        string $itemNameSnapshot,
        float $priceSnapshot,
        int $quantity,
        ?string $kitchenStationId = null,
        ?string $specialInstructions = null
    ): ?string {
        if (!$this->tableExists('order_items')) {
            return null;
        }

        $id = $this->uuid();
        $now = $this->now();
        $lineTotal = round($priceSnapshot * $quantity, 2);

        $ok = $this->execute(
            "INSERT INTO order_items (
                id, brand_id, branch_id, order_id, menu_item_id, kitchen_station_id,
                item_name_snapshot, price_snapshot, quantity, line_total,
                special_instructions, status, status_changed_at, sent_to_kitchen_at,
                is_billed, created_at, updated_at
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'new', ?, ?, 0, ?, ?)",
            [
                $id,
                $brandId,
                $branchId,
                $orderId,
                $menuItemId,
                $kitchenStationId,
                $itemNameSnapshot,
                $priceSnapshot,
                $quantity,
                $lineTotal,
                $specialInstructions,
                $now,
                $now,
                $now,
                $now,
            ]
        );

        return $ok ? $id : null;
    }

    public function recalculateOrderTotals(string $orderId): bool
    {
        $rows = $this->all(
            "SELECT oi.price_snapshot, oi.quantity, mi.tax_rate
             FROM order_items oi
             LEFT JOIN menu_items mi
                ON mi.id = oi.menu_item_id
             WHERE oi.order_id = ?
               AND (oi.status IS NULL OR oi.status NOT IN ('cancelled', 'rejected'))",
            [$orderId]
        );

        $subtotal = 0.0;
        $taxAmount = 0.0;

        foreach ($rows as $row) {
            $price = (float)($row['price_snapshot'] ?? 0);
            $qty = (int)($row['quantity'] ?? 0);
            $taxRate = (float)($row['tax_rate'] ?? 0);

            $lineTotal = $price * $qty;
            $subtotal += $lineTotal;
            $taxAmount += ($lineTotal * $taxRate) / 100;
        }

        $subtotal = round($subtotal, 2);
        $taxAmount = round($taxAmount, 2);
        $grandTotal = round($subtotal + $taxAmount, 2);
        $now = $this->now();

        return $this->execute(
            "UPDATE orders
             SET subtotal = ?,
                 tax_amount = ?,
                 service_charge = 0,
                 discount_amount = 0,
                 grand_total = ?,
                 updated_at = ?
             WHERE id = ?",
            [$subtotal, $taxAmount, $grandTotal, $now, $orderId]
        );
    }

    public function assignOrderToTable(string $tableId, string $orderId): bool
    {
        $now = $this->now();

        return $this->execute(
            "UPDATE tables
             SET current_order_id = ?,
                 status = 'occupied',
                 status_updated_at = ?,
                 updated_at = ?
             WHERE id = ?",
            [$orderId, $now, $now, $tableId]
        );
    }

    public function markTableOccupied(string $tableId, string $orderId): bool
    {
        return $this->assignOrderToTable($tableId, $orderId);
    }

    protected function nextBillNumber(string $branchId): string
    {
        $prefix = 'BILL-' . date('Ymd') . '-';
        $total = (int)$this->scalar(
            "SELECT COUNT(*)
             FROM bills
             WHERE branch_id = ?
               AND DATE(created_at) = CURDATE()",
            [$branchId],
            0
        );

        return $prefix . str_pad((string)($total + 1), 4, '0', STR_PAD_LEFT);
    }

    public function createBillFromOrder(string $brandId, string $branchId, string $tableId, string $orderId): ?string
    {
        $order = $this->getOrderById($orderId);
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
            [
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
                $now,
            ]
        );

        if (!$ok) {
            return null;
        }

        if ($this->tableExists('bill_items')) {
            $items = $this->getOrderItemsForTracking($orderId);

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
                    [$billItemId, $billId, $item['id'], $itemName, $qty, $unitPrice, $taxRate, $taxItemAmount, $lineTotal, $now]
                );

                $this->execute(
                    "UPDATE order_items
                     SET bill_id = ?, is_billed = 1, updated_at = ?
                     WHERE id = ?",
                    [$billId, $now, $item['id']]
                );
            }
        }

        return $billId;
    }

    // public function refreshExistingBillFromOrder(string $billId, string $orderId): bool
    // {
    //     if (!$this->tableExists('bills')) {
    //         return false;
    //     }

    //     $order = $this->getOrderById($orderId);
    //     if (!$order) {
    //         return false;
    //     }

    //     $now = $this->now();

    //     $subtotal = (float)($order['subtotal'] ?? 0);
    //     $taxAmount = (float)($order['tax_amount'] ?? 0);
    //     $serviceCharge = (float)($order['service_charge'] ?? 0);
    //     $discountAmount = (float)($order['discount_amount'] ?? 0);
    //     $roundoffAmount = 0.0;
    //     $totalAmount = (float)($order['grand_total'] ?? 0);

    //     $ok = $this->execute(
    //         "UPDATE bills
    //      SET bill_status = 'new',
    //          payment_status = 'unpaid',
    //          subtotal = ?,
    //          tax_amount = ?,
    //          service_charge = ?,
    //          discount_amount = ?,
    //          roundoff_amount = ?,
    //          total_amount = ?,
    //          updated_at = ?
    //      WHERE id = ?
    //        AND order_id = ?",
    //         [
    //             $subtotal,
    //             $taxAmount,
    //             $serviceCharge,
    //             $discountAmount,
    //             $roundoffAmount,
    //             $totalAmount,
    //             $now,
    //             $billId,
    //             $orderId,
    //         ]
    //     );

    //     if (!$ok) {
    //         return false;
    //     }

    //     if ($this->tableExists('bill_items')) {
    //         $items = $this->getOrderItemsForTracking($orderId);

    //         foreach ($items as $item) {
    //             $orderItemId = (string)($item['id'] ?? '');
    //             if ($orderItemId === '') {
    //                 continue;
    //             }

    //             $alreadyExists = (int)$this->scalar(
    //                 "SELECT COUNT(*)
    //              FROM bill_items
    //              WHERE bill_id = ?
    //                AND order_item_id = ?",
    //                 [$billId, $orderItemId],
    //                 0
    //             );

    //             if ($alreadyExists > 0) {
    //                 continue;
    //             }

    //             $billItemId = $this->uuid();
    //             $itemName = (string)($item['item_name_snapshot'] ?? 'Menu Item');
    //             $qty = max(1, (int)($item['quantity'] ?? 1));
    //             $unitPrice = (float)($item['price_snapshot'] ?? 0);
    //             $lineTotal = (float)($item['line_total'] ?? ($qty * $unitPrice));
    //             $taxRate = (float)($item['tax_rate'] ?? 0);
    //             $taxItemAmount = round(($lineTotal * $taxRate) / 100, 2);

    //             $this->execute(
    //                 "INSERT INTO bill_items (
    //                 id, bill_id, order_item_id, item_name, quantity,
    //                 unit_price, tax_rate, tax_amount, total_amount, created_at
    //              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
    //                 [
    //                     $billItemId,
    //                     $billId,
    //                     $orderItemId,
    //                     $itemName,
    //                     $qty,
    //                     $unitPrice,
    //                     $taxRate,
    //                     $taxItemAmount,
    //                     $lineTotal,
    //                     $now,
    //                 ]
    //             );

    //             $this->execute(
    //                 "UPDATE order_items
    //              SET bill_id = ?,
    //                  is_billed = 1,
    //                  updated_at = ?
    //              WHERE id = ?",
    //                 [$billId, $now, $orderItemId]
    //             );
    //         }
    //     }

    //     return true;
    // }


    public function refreshExistingBillFromOrder(string $billId, string $orderId): bool
    {
        if (!$this->tableExists('bills')) {
            return false;
        }

        $billId = trim($billId);
        $orderId = trim($orderId);

        if ($billId === '' || $orderId === '') {
            return false;
        }

        $order = $this->getOrderById($orderId);

        if (!$order || empty($order['id'])) {
            return false;
        }

        $now = $this->now();

        $subtotal       = (float)($order['subtotal'] ?? 0);
        $taxAmount      = (float)($order['tax_amount'] ?? 0);
        $serviceCharge  = (float)($order['service_charge'] ?? 0);
        $discountAmount = (float)($order['discount_amount'] ?? 0);
        $roundoffAmount = (float)($order['roundoff_amount'] ?? 0);
        $totalAmount    = (float)($order['grand_total'] ?? $order['total_amount'] ?? 0);

        /*
     * Refresh existing bill:
     * - Send bill back to manager approval queue.
     * - Reset payment only if it is not already paid/in processing.
     * - Keep same bill id.
     */
        $updated = $this->execute(
            "UPDATE bills
         SET bill_status = 'new',
             payment_status = 'unpaid',
             subtotal = ?,
             tax_amount = ?,
             service_charge = ?,
             discount_amount = ?,
             roundoff_amount = ?,
             total_amount = ?,
             updated_at = ?
         WHERE id = ?
           AND order_id = ?
           AND COALESCE(payment_status, 'unpaid') <> 'paid'",
            [
                $subtotal,
                $taxAmount,
                $serviceCharge,
                $discountAmount,
                $roundoffAmount,
                $totalAmount,
                $now,
                $billId,
                $orderId,
            ]
        );

        if (!$updated) {
            return false;
        }

        /*
     * If bill_items table exists, attach only newly ordered items.
     * Existing bill items are not duplicated.
     */
        if ($this->tableExists('bill_items')) {
            $items = $this->getOrderItemsForTracking($orderId);

            foreach ($items as $item) {
                $orderItemId = trim((string)($item['id'] ?? ''));

                if ($orderItemId === '') {
                    continue;
                }

                $alreadyExists = (int)$this->scalar(
                    "SELECT COUNT(*)
                 FROM bill_items
                 WHERE bill_id = ?
                   AND order_item_id = ?",
                    [$billId, $orderItemId],
                    0
                );

                if ($alreadyExists > 0) {
                    continue;
                }

                $billItemId = $this->uuid();

                $itemName = trim((string)(
                    $item['item_name_snapshot']
                    ?? $item['name']
                    ?? 'Menu Item'
                ));

                if ($itemName === '') {
                    $itemName = 'Menu Item';
                }

                $quantity = max(1, (int)($item['quantity'] ?? 1));
                $unitPrice = (float)(
                    $item['price_snapshot']
                    ?? $item['unit_price']
                    ?? $item['price']
                    ?? 0
                );

                $lineTotal = (float)(
                    $item['line_total']
                    ?? ($quantity * $unitPrice)
                );

                $taxRate = (float)($item['tax_rate'] ?? 0);
                $taxItemAmount = round(($lineTotal * $taxRate) / 100, 2);

                $inserted = $this->execute(
                    "INSERT INTO bill_items (
                    id,
                    bill_id,
                    order_item_id,
                    item_name,
                    quantity,
                    unit_price,
                    tax_rate,
                    tax_amount,
                    total_amount,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $billItemId,
                        $billId,
                        $orderItemId,
                        $itemName,
                        $quantity,
                        $unitPrice,
                        $taxRate,
                        $taxItemAmount,
                        $lineTotal,
                        $now,
                    ]
                );

                if (!$inserted) {
                    return false;
                }

                /*
             * Mark order item as billed only after bill item insert succeeds.
             */
                $this->execute(
                    "UPDATE order_items
                 SET bill_id = ?,
                     is_billed = 1,
                     updated_at = ?
                 WHERE id = ?",
                    [
                        $billId,
                        $now,
                        $orderItemId,
                    ]
                );
            }
        }

        return true;
    }

    public function createPaymentRequest(
        string $brandId,
        string $branchId,
        string $tableId,
        string $billId,
        string $orderId,
        string $method,
        float $amount,
        array $metadata = []
    ): ?string {
        if (!$this->tableExists('payments')) {
            return null;
        }

        $id = $this->uuid();
        $now = $this->now();
        $paymentReference = 'PAY-' . date('YmdHis') . '-' . substr(str_replace('-', '', $id), 0, 6);

        $metadataJson = json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($metadataJson === false) {
            $metadataJson = '{}';
        }

        $ok = $this->execute(
            "INSERT INTO payments (
                id, brand_id, branch_id, table_id, bill_id, order_id,
                initiated_by, payment_reference, method, amount, status,
                metadata_json, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, 'customer', ?, ?, ?, 'in_processing', ?, ?, ?)",
            [
                $id,
                $brandId,
                $branchId,
                $tableId,
                $billId,
                $orderId !== '' ? $orderId : null,
                $paymentReference,
                $method,
                $amount,
                $metadataJson,
                $now,
                $now,
            ]
        );

        return $ok ? $id : null;
    }

    public function getLatestPaymentByBillId(string $billId): ?array
    {
        if (!$this->tableExists('payments')) {
            return null;
        }

        return $this->one(
            "SELECT *
             FROM payments
             WHERE bill_id = ?
             ORDER BY created_at DESC
             LIMIT 1",
            [$billId]
        );
    }

    public function getPaymentById(string $paymentId): ?array
    {
        if (!$this->tableExists('payments')) {
            return null;
        }

        return $this->one(
            "SELECT * FROM payments WHERE id = ? LIMIT 1",
            [$paymentId]
        );
    }

    public function markBillPaymentPending(string $billId, string $paymentStatus = 'in_processing'): bool
    {
        $now = $this->now();

        return $this->execute(
            "UPDATE bills
             SET payment_status = ?, updated_at = ?
             WHERE id = ?",
            [$paymentStatus, $now, $billId]
        );
    }

    public function markOrderPaymentPending(string $orderId): bool
    {
        if ($orderId === '') {
            return true;
        }

        $now = $this->now();

        return $this->execute(
            "UPDATE orders
             SET updated_at = ?
             WHERE id = ?",
            [$now, $orderId]
        );
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
            [$billId, $now, $now, $orderId]
        );
    }

    public function assignBillToTable(string $tableId, string $billId): bool
    {
        $now = $this->now();

        return $this->execute(
            "UPDATE tables
             SET current_bill_id = ?,
                 updated_at = ?
             WHERE id = ?",
            [$billId, $now, $tableId]
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
            [$brandId, $branchId, $eventType, $entityType, $entityId, $jsonPayload, $this->now()]
        );
    }

    public function getRealtimeEvents(string $brandId, string $branchId, int $afterId = 0, int $limit = 100): array
    {
        if (!$this->tableExists('realtime_events')) {
            return [];
        }

        $limit = max(1, min(500, $limit));

        return $this->all(
            "SELECT id, brand_id, branch_id, event_type, entity_type, entity_id, payload_json, created_at, is_published
             FROM realtime_events
             WHERE brand_id = ?
               AND branch_id = ?
               AND id > ?
             ORDER BY id ASC
             LIMIT {$limit}",
            [$brandId, $branchId, $afterId]
        );
    }

    public function syncPaidTableAvailability(string $brandId, string $branchId, array $tableIds = []): int
    {
        if (!$this->tableExists('tables') || !$this->tableExists('bills')) {
            return 0;
        }

        $ids = [];
        foreach ($tableIds as $tableId) {
            $tableId = $this->entityIdOrNull((string)$tableId);
            if ($tableId !== null) {
                $ids[] = $tableId;
            }
        }

        $params = [$brandId, $branchId];
        $tableFilter = '';

        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $tableFilter = " AND t.id IN ({$placeholders})";
            foreach ($ids as $id) {
                $params[] = $id;
            }
        }

        $rows = $this->all(
            "SELECT
                t.id AS table_id,
                t.current_order_id,
                t.current_bill_id,
                b.id AS bill_id,
                b.order_id,
                b.bill_status,
                b.payment_status
             FROM tables t
             INNER JOIN bills b
                ON b.id = t.current_bill_id
             WHERE t.brand_id = ?
               AND t.branch_id = ?
               {$tableFilter}
               AND (
                    LOWER(COALESCE(b.payment_status, '')) = 'paid'
                    OR LOWER(COALESCE(b.bill_status, '')) = 'closed'
               )",
            $params
        );

        if (empty($rows)) {
            return 0;
        }

        $now = $this->now();

        $releasedCount = 0;

        foreach ($rows as $row) {
            $tableId = (string)($row['table_id'] ?? '');
            $billId = (string)($row['bill_id'] ?? '');
            $orderId = (string)($row['order_id'] ?? $row['current_order_id'] ?? '');

            if ($tableId === '') {
                continue;
            }

            if ($billId !== '') {
                $this->execute(
                    "UPDATE bills
                     SET bill_status = 'closed',
                         payment_status = 'paid',
                         paid_at = COALESCE(paid_at, ?),
                         closed_at = COALESCE(closed_at, ?),
                         updated_at = ?
                     WHERE id = ?",
                    [$now, $now, $now, $billId]
                );
            }

            if ($orderId !== '') {
                $this->execute(
                    "UPDATE orders
                     SET status = 'completed',
                         paid_at = COALESCE(paid_at, ?),
                         closed_at = COALESCE(closed_at, ?),
                         order_closed_reason = COALESCE(order_closed_reason, 'paid'),
                         updated_at = ?
                     WHERE id = ?
                       AND LOWER(COALESCE(status, '')) NOT IN ('cancelled', 'completed')",
                    [$now, $now, $now, $orderId]
                );
            }

            $this->execute(
                "UPDATE service_requests
                 SET status = 'closed',
                     resolved_at = COALESCE(resolved_at, ?)
                 WHERE brand_id = ?
                   AND branch_id = ?
                   AND table_id = ?
                   AND LOWER(COALESCE(status, '')) IN ('new', 'pending', 'approved', 'accepted', 'acknowledged')",
                [$now, $brandId, $branchId, $tableId]
            );

            $this->execute(
                "UPDATE tables
                 SET status = 'available',
                     current_order_id = NULL,
                     current_bill_id = NULL,
                     auto_available_at = NULL,
                     status_updated_at = ?,
                     updated_at = ?
                 WHERE id = ?
                   AND brand_id = ?
                   AND branch_id = ?
                   AND LOWER(COALESCE(status, '')) <> 'maintenance'",
                [$now, $now, $tableId, $brandId, $branchId]
            );

            $this->pushRealtimeEvent(
                $brandId,
                $branchId,
                'table.available',
                'table',
                $tableId,
                [
                    'table_id' => $tableId,
                    'order_id' => $orderId,
                    'bill_id' => $billId,
                    'payment_status' => 'paid',
                    'bill_status' => 'closed',
                ]
            );

            $releasedCount++;
        }

        return $releasedCount;
    }

    public function markBillPaidAndReleaseTable(string $brandId, string $branchId, string $billId): bool
    {
        if (!$this->tableExists('bills')) {
            return false;
        }

        $bill = $this->getBillById($billId);
        if (!$bill) {
            return false;
        }

        $tableId = (string)($bill['table_id'] ?? '');
        $orderId = (string)($bill['order_id'] ?? '');
        $now = $this->now();

        $ok = $this->execute(
            "UPDATE bills
             SET bill_status = 'closed',
                 payment_status = 'paid',
                 paid_at = COALESCE(paid_at, ?),
                 closed_at = COALESCE(closed_at, ?),
                 updated_at = ?
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?",
            [$now, $now, $now, $billId, $brandId, $branchId]
        );

        if (!$ok) {
            return false;
        }

        if ($orderId !== '') {
            $this->execute(
                "UPDATE orders
                 SET status = 'completed',
                     paid_at = COALESCE(paid_at, ?),
                     closed_at = COALESCE(closed_at, ?),
                     order_closed_reason = COALESCE(order_closed_reason, 'paid'),
                     updated_at = ?
                 WHERE id = ?",
                [$now, $now, $now, $orderId]
            );
        }

        if ($this->tableExists('payments')) {
            $this->execute(
                "UPDATE payments
                 SET status = 'paid',
                     paid_at = COALESCE(paid_at, ?),
                     transaction_time = COALESCE(transaction_time, ?),
                     updated_at = ?
                 WHERE bill_id = ?
                   AND LOWER(COALESCE(status, '')) IN ('in_processing', 'awaiting_collection', 'processing', 'pending')",
                [$now, $now, $now, $billId]
            );
        }

        if ($tableId !== '') {
            $this->syncPaidTableAvailability($brandId, $branchId, [$tableId]);
        }

        $this->pushRealtimeEvent(
            $brandId,
            $branchId,
            'payment.paid',
            'bill',
            $billId,
            [
                'bill_id' => $billId,
                'order_id' => $orderId,
                'table_id' => $tableId,
                'bill_status' => 'closed',
                'payment_status' => 'paid',
            ]
        );

        return true;
    }

    public function getCustomerPageData(string $brandId, string $branchId, string $tableCode): array
    {
        $brand = $this->getBrand($brandId) ?? [];
        $branch = $this->getBranch($brandId, $branchId) ?? [];
        $customerContent = $this->getCustomerContent($brandId);
        $offerBanners = $this->getActiveOfferBanners($brandId, $branchId);

        $table = [];
        if ($tableCode !== '') {
            $table = $this->getTableByCode($brandId, $branchId, $tableCode) ?? [];
            if (!empty($table['table_code'])) {
                $tableCode = (string)$table['table_code'];
            } elseif (!empty($table['table_number'])) {
                $tableCode = (string)$table['table_number'];
            }
        }

        if (!empty($table['id'])) {
            $this->syncPaidTableAvailability($brandId, $branchId, [(string)$table['id']]);
            $table = $this->getTableByCode($brandId, $branchId, $tableCode) ?? $table;
        }

        $menuCategories = $this->getMenuCategories($brandId, $branchId);
        $menuItems = $this->getMenuItems($brandId, $branchId);
        $availableTables = $this->getBranchTables($brandId, $branchId);

        $activeOrder = [];
        $activeOrderItems = [];
        $activeBill = [];
        $recentServiceRequest = [];

        if (!empty($table['id'])) {
            $tableId = (string)$table['id'];

            $activeOrder = $this->getActiveOrderForTable($brandId, $branchId, $tableId) ?? [];

            if (!empty($activeOrder['id'])) {
                $activeOrderItems = $this->getOrderItemsForTracking((string)$activeOrder['id']);
                $activeBill = $this->getBillByOrderId((string)$activeOrder['id']) ?? [];
            }

            $recentServiceRequest = $this->getLatestServiceRequest($brandId, $branchId, $tableId) ?? [];
        }

        return [
            'brandId'              => $brandId,
            'branchId'             => $branchId,
            'customerContent'      => $customerContent,
            'offerBanners'         => $offerBanners,
            'tableCode'            => $tableCode !== '' ? $tableCode : $this->defaultTableCode,
            'brand'                => $brand,
            'branch'               => $branch,
            'table'                => $table,
            'menuCategories'       => $menuCategories,
            'menuItems'            => $menuItems,
            'availableTables'      => $availableTables,
            'activeOrder'          => $activeOrder,
            'activeOrderItems'     => $activeOrderItems,
            'activeBill'           => $activeBill,
            'recentServiceRequest' => $recentServiceRequest,
        ];
    }

    protected function projectRootPath(): string
    {
        return rtrim(dirname(APPPATH), DIRECTORY_SEPARATOR);
    }

    protected function defaultCustomerJsonPath(): string
    {
        return $this->projectRootPath()
            . DIRECTORY_SEPARATOR . 'RestaurantAssets'
            . DIRECTORY_SEPARATOR . 'default'
            . DIRECTORY_SEPARATOR . 'default.json';
    }

    public function loadDefaultCustomerContent(): array
    {
        $path = $this->defaultCustomerJsonPath();

        if (!is_file($path) || !is_readable($path)) {
            return $this->normalizeCustomerContent([]);
        }

        $raw = file_get_contents($path);
        if ($raw === false || trim($raw) === '') {
            return $this->normalizeCustomerContent([]);
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return $this->normalizeCustomerContent([]);
        }

        return $this->normalizeCustomerContent($decoded);
    }

    public function normalizeCustomerContent(array $content): array
    {
        return [
            'brand_block' => [
                'kicker'   => trim((string)($content['brand_block']['kicker'] ?? 'Fine Dining Restaurant')),
                'subtitle' => trim((string)($content['brand_block']['subtitle'] ?? 'Fresh food, fast service, great dining')),
            ],
            'hero_section' => [
                'badge'           => trim((string)($content['hero_section']['badge'] ?? 'Premium Dining Experience')),
                'title_line_1'    => trim((string)($content['hero_section']['title_line_1'] ?? 'Discover Exceptional')),
                'title_highlight' => trim((string)($content['hero_section']['title_highlight'] ?? 'Flavours')),
                'description'     => trim((string)($content['hero_section']['description'] ?? 'Explore our chef-crafted menu filled with fresh ingredients, signature recipes, and mouth-watering flavors prepared specially for your table with care, taste, and elegance.')),
                'cta_primary'     => trim((string)($content['hero_section']['cta_primary'] ?? 'Explore Menu')),
                'cta_secondary'   => trim((string)($content['hero_section']['cta_secondary'] ?? 'About Us')),
            ],
            'service_panel' => [
                'badge'       => trim((string)($content['service_panel']['badge'] ?? 'Table Service')),
                'title'       => trim((string)($content['service_panel']['title'] ?? 'Order your favorites with one tap')),
                'description' => trim((string)($content['service_panel']['description'] ?? 'Browse the menu, customize dishes, track your live order, request service, and pay your bill directly from your table pad with a seamless dining experience.')),
            ],
            'menu_discovery' => [
                'kicker'      => trim((string)($content['menu_discovery']['kicker'] ?? 'Curated Menu')),
                'title'       => trim((string)($content['menu_discovery']['title'] ?? 'Find your perfect dish')),
                'description' => trim((string)($content['menu_discovery']['description'] ?? 'Search, explore categories, and discover chef specials crafted to match every craving at your table.')),
                'badge'       => trim((string)($content['menu_discovery']['badge'] ?? 'Trending Today')),
            ],
            'footer' => [
                'description' => trim((string)($content['footer']['description'] ?? 'Fresh food, premium dining experience, and seamless table ordering.')),
            ],
        ];
    }

    public function getCustomerContent(string $brandId): array
    {
        $default = $this->loadDefaultCustomerContent();
        $brand = $this->getBrand($brandId);

        if (!$brand) {
            return $default;
        }

        $raw = $brand['customer_data'] ?? null;

        if (is_string($raw) && trim($raw) !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $this->normalizeCustomerContent(array_replace_recursive($default, $decoded));
            }
        }

        if (is_array($raw)) {
            return $this->normalizeCustomerContent(array_replace_recursive($default, $raw));
        }

        return $default;
    }

    public function getActiveOfferBanners(string $brandId, string $branchId): array
    {
        if (!$this->tableExists('branch_offer_banners')) {
            return [];
        }

        return $this->all(
            "SELECT id, image_path, sort_order
             FROM branch_offer_banners
             WHERE brand_id = ?
               AND branch_id = ?
               AND is_active = 1
               AND (starts_at IS NULL OR starts_at <= NOW())
               AND (ends_at IS NULL OR ends_at >= NOW())
             ORDER BY sort_order ASC, created_at ASC",
            [$brandId, $branchId]
        );
    }

    public function getTableDeviceByToken(string $token): ?array
    {
        if (!$this->tableExists('table_devices')) {
            return null;
        }

        return $this->one(
            "SELECT *
             FROM table_devices
             WHERE auth_token = ?
               AND status = 'active'
               AND (token_expires_at IS NULL OR token_expires_at > NOW())
             LIMIT 1",
            [$token]
        );
    }

    public function getCustomerContextByToken(string $token): ?array
    {
        $device = $this->getTableDeviceByToken($token);
        if (!$device) {
            return null;
        }

        $table = $this->getTableById(
            (string)$device['brand_id'],
            (string)$device['branch_id'],
            (string)$device['table_id']
        );

        if (!$table) {
            return null;
        }

        return [
            'brand_id'   => (string)$device['brand_id'],
            'branch_id'  => (string)$device['branch_id'],
            'table_id'   => (string)$device['table_id'],
            'table_code' => (string)($table['table_code'] ?? $table['table_number'] ?? $this->defaultTableCode),
        ];
    }
}
