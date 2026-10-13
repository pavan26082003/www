<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\RestCustomerModel;
use RuntimeException;
use Throwable;

require_once APPPATH . 'Models/RestCustomerModel.php';

final class RestCustomerController extends RestBaseController
{
    private const VIEW_PATH = APPPATH . 'Views/restaurant-system/customer.php';

    protected $customerModel;
    protected $defaultTableCode = 'T01';

    public function __construct()
    {
        parent::__construct();
        $this->customerModel = new RestCustomerModel();
    }

    public function index(): void
    {
        $context = $this->resolveContext($this->requestData());

        $pageData = $this->customerModel->getCustomerPageData(
            $context['brand_id'],
            $context['branch_id'],
            $context['table_code']
        );

        $this->renderView(self::VIEW_PATH, $pageData);
    }

    public function api(): void
    {
        try {
            $input = $this->requestData();
            $action = trim((string)($_GET['action'] ?? $input['action'] ?? ''));

            if ($action === '') {
                $this->error('Action is required.', 400);
            }

            switch ($action) {
                case 'order_status':
                    $this->orderStatus($input);
                    break;
                case 'cart_summary':
                    $this->cartSummary($input);
                    break;
                case 'menu_item':
                    $this->menuItem($input);
                    break;
                case 'change_table':
                    $this->changeTable($input);
                    break;
                case 'service_request':
                    $this->serviceRequest($input);
                    break;
                case 'add_to_cart':
                    $this->addToCart($input);
                    break;
                case 'update_cart_qty':
                    $this->updateCartQty($input);
                    break;
                case 'remove_from_cart':
                    $this->removeFromCart($input);
                    break;
                case 'clear_cart':
                    $this->clearCart($input);
                    break;
                case 'place_order':
                    $this->placeOrder($input);
                    break;
                case 'request_bill':
                    $this->requestBill($input);
                    break;
                case 'initiate_payment':
                    $this->initiatePayment($input);
                    break;
                case 'payment_status':
                    $this->paymentStatus($input);
                    break;
                case 'realtime_events':
                    $this->realtimeEvents($input);
                    break;
                default:
                    $this->error('Unknown action.', 404);
            }
        } catch (Throwable $e) {
            $status = (int)$e->getCode();
            if ($status < 400 || $status > 599) {
                $status = 500;
            }

            log_message('error', 'RestCustomerController::api failed: ' . $e->getMessage());

            $this->error(
                $status >= 500 ? 'Something went wrong while processing the request.' : $e->getMessage(),
                $status
            );
        }
    }

    protected function success(string $message, array $data = [], int $status = 200): void
    {
        $this->json([
            'ok'      => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    protected function error(string $message, int $status = 400, array $extra = []): void
    {
        $this->json(array_merge([
            'ok'      => false,
            'message' => $message,
        ], $extra), $status);
    }

    protected function requestMethod(): string
    {
        return strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    }

    protected function ensureRequestMethod(string $method): void
    {
        if ($this->requestMethod() !== strtoupper($method)) {
            $this->error('Invalid request method.', 405);
        }
    }

    protected function contextSessionKey(): string
    {
        return 'rest_customer_context';
    }

    protected function resolveContext(array $input = []): array
    {
        $sessionContext = $_SESSION[$this->contextSessionKey()] ?? [];

        $brandId = (string)($input['brand_id'] ?? $_GET['brand_id'] ?? $sessionContext['brand_id'] ?? $this->brandId);
        $branchId = (string)($input['branch_id'] ?? $_GET['branch_id'] ?? $sessionContext['branch_id'] ?? $this->branchId);
        $tableId = (string)($input['table_id'] ?? $_GET['table_id'] ?? $sessionContext['table_id'] ?? '');
        $tableCode = (string)($input['table_code'] ?? $input['table'] ?? $_GET['table_code'] ?? $_GET['table'] ?? $sessionContext['table_code'] ?? $this->defaultTableCode);

        $brandId = $this->customerModel->normalizeEntityIdOrDefault($brandId, $this->brandId);
        $branchId = $this->customerModel->normalizeEntityIdOrDefault($branchId, $this->branchId);
        $tableId = $this->customerModel->entityIdOrNull($tableId) ?? '';
        $tableCode = $this->customerModel->normalizeTableCode($tableCode, $this->defaultTableCode);

        if ($brandId === '' || $branchId === '') {
            throw new RuntimeException('Restaurant context is missing.', 500);
        }

        if ($tableId === '' && $tableCode !== '') {
            $table = $this->customerModel->getTableByCode($brandId, $branchId, $tableCode);
            if (!empty($table)) {
                $tableId = (string)($table['id'] ?? '');
                $tableCode = (string)($table['table_code'] ?? $table['table_number'] ?? $tableCode);
            }
        }

        $context = [
            'brand_id'   => $brandId,
            'branch_id'  => $branchId,
            'table_id'   => $tableId,
            'table_code' => $tableCode,
        ];

        $_SESSION[$this->contextSessionKey()] = $context;
        $_SESSION['rest_brand_id'] = $brandId;
        $_SESSION['rest_branch_id'] = $branchId;

        return $context;
    }

    protected function refreshSessionContext(string $brandId, string $branchId, string $tableId, string $tableCode): void
    {
        $_SESSION[$this->contextSessionKey()] = [
            'brand_id'   => $brandId,
            'branch_id'  => $branchId,
            'table_id'   => $tableId,
            'table_code' => $tableCode,
        ];
    }

    protected function resolvedTable(array $context): array
    {
        $brandId = (string)$context['brand_id'];
        $branchId = (string)$context['branch_id'];
        $tableId = (string)$context['table_id'];
        $tableCode = (string)$context['table_code'];

        $table = [];

        if ($tableId !== '') {
            $table = $this->customerModel->getTableById($brandId, $branchId, $tableId) ?? [];
        }

        if (empty($table) && $tableCode !== '') {
            $table = $this->customerModel->getTableByCode($brandId, $branchId, $tableCode) ?? [];
        }

        if (!empty($table)) {
            $this->refreshSessionContext(
                $brandId,
                $branchId,
                (string)($table['id'] ?? ''),
                (string)($table['table_code'] ?? $table['table_number'] ?? $tableCode)
            );
        }

        return $table;
    }

    protected function safeSyncPaidTableAvailability(string $brandId, string $branchId, array $tableIds): void
    {
        if (method_exists($this->customerModel, 'syncPaidTableAvailability')) {
            $this->customerModel->syncPaidTableAvailability($brandId, $branchId, $tableIds);
        }
    }

    protected function resolveBillForContext(array $context, array $input = []): array
    {
        $billId = trim((string)($input['bill_id'] ?? $_GET['bill_id'] ?? ''));
        if ($billId !== '') {
            $bill = $this->customerModel->getBillById($billId);
            if (!empty($bill)) {
                return $this->normalizeBill($bill);
            }
        }

        $table = $this->resolvedTable($context);
        if (empty($table['id'])) {
            return [];
        }

        $order = $this->customerModel->getActiveOrderForTable(
            $context['brand_id'],
            $context['branch_id'],
            (string)$table['id']
        ) ?? [];

        if (!empty($order['id'])) {
            $bill = $this->customerModel->getBillByOrderId((string)$order['id']) ?? [];
            return $this->normalizeBill($bill);
        }

        if (!empty($table['current_bill_id'])) {
            $bill = $this->customerModel->getBillById((string)$table['current_bill_id']) ?? [];
            return $this->normalizeBill($bill);
        }

        return [];
    }

    protected function normalizeOrderStatus(?string $status): string
    {
        $status = strtolower(trim((string)$status));

        if ($status === 'placed' || $status === 'pending') {
            return 'new';
        }

        if ($status === 'cooking') {
            return 'preparing';
        }

        if ($status === 'closed' || $status === 'paid') {
            return 'completed';
        }

        if ($status === '') {
            return 'new';
        }

        return $status;
    }

    protected function normalizeBillStatus(?string $status): string
    {
        $status = strtolower(trim((string)$status));

        if ($status === 'requested' || $status === 'generated' || $status === 'pending') {
            return 'new';
        }

        if ($status === '') {
            return 'new';
        }

        return $status;
    }

    protected function normalizePaymentStatus(?string $status): string
    {
        $status = strtolower(trim((string)$status));

        if ($status === 'awaiting_collection' || $status === 'processing' || $status === 'pending' || $status === 'initiated') {
            return 'in_processing';
        }

        if ($status === '') {
            return 'unpaid';
        }

        return $status;
    }

    protected function normalizeRequestStatus(?string $status): string
    {
        $status = strtolower(trim((string)$status));

        if ($status === 'pending') {
            return 'new';
        }

        if ($status === 'accepted' || $status === 'acknowledged') {
            return 'approved';
        }

        if ($status === 'resolved' || $status === 'completed') {
            return 'closed';
        }

        if ($status === '') {
            return 'new';
        }

        return $status;
    }

    protected function normalizeOrder(array $order): array
    {
        if (empty($order)) {
            return [];
        }

        $order['status'] = $this->normalizeOrderStatus($order['status'] ?? 'new');

        if (empty($order['priority'])) {
            $order['priority'] = $this->resolvePriorityByAge($order['created_at'] ?? $order['opened_at'] ?? null, [10, 20]);
        }

        return $order;
    }

    protected function normalizeOrderItems(array $items): array
    {
        foreach ($items as $index => $item) {
            $items[$index]['status'] = $this->normalizeOrderStatus($item['status'] ?? 'new');
        }

        return $items;
    }

    protected function normalizeBill(array $bill): array
    {
        if (empty($bill)) {
            return [];
        }

        $bill['bill_status'] = $this->normalizeBillStatus($bill['bill_status'] ?? 'new');
        $bill['payment_status'] = $this->normalizePaymentStatus($bill['payment_status'] ?? 'unpaid');

        return $bill;
    }

    protected function normalizePayment(array $payment): array
    {
        if (empty($payment)) {
            return [];
        }

        $payment['status'] = $this->normalizePaymentStatus($payment['status'] ?? 'unpaid');

        return $payment;
    }

    protected function normalizeServiceRequest(array $request): array
    {
        if (empty($request)) {
            return [];
        }

        $request['status'] = $this->normalizeRequestStatus($request['status'] ?? 'new');

        if (empty($request['priority'])) {
            $request['priority'] = $this->resolvePriorityByAge($request['created_at'] ?? $request['requested_at'] ?? null, [3, 7]);
        }

        return $request;
    }

    protected function resolvePriorityByAge($createdAt, array $limits): string
    {
        $timestamp = strtotime((string)$createdAt);
        if ($timestamp === false) {
            return 'normal';
        }

        $ageMinutes = (int)floor((time() - $timestamp) / 60);
        $highAt = (int)($limits[0] ?? 10);
        $urgentAt = (int)($limits[1] ?? 20);

        if ($ageMinutes >= $urgentAt) {
            return 'urgent';
        }

        if ($ageMinutes >= $highAt) {
            return 'high';
        }

        return 'normal';
    }

    protected function customerNotifications(array $context, array $table, array $order, array $bill, array $payment, array $latestServiceRequest): array
    {
        $notifications = [];

        if (!empty($latestServiceRequest['id'])) {
            $requestStatus = $this->normalizeRequestStatus($latestServiceRequest['status'] ?? '');
            $requestType = strtolower((string)($latestServiceRequest['request_type'] ?? $latestServiceRequest['type'] ?? 'waiter'));

            if ($requestStatus === 'approved') {
                $message = 'Waiter accepted your request and is coming.';
                $title = 'Request Accepted';

                if ($requestType === 'water') {
                    $message = 'Waiter accepted your request and is bringing water.';
                } elseif ($requestType === 'bill') {
                    $title = 'Bill Request Accepted';
                    $message = 'Your bill request has been accepted.';
                } elseif (strpos($requestType, 'payment_collection') === 0) {
                    $title = 'Payment Request Accepted';
                    $message = 'Waiter accepted your payment request.';
                }

                $notifications[] = [
                    'id'      => (string)$latestServiceRequest['id'] . ':approved',
                    'type'    => 'service.approved',
                    'title'   => $title,
                    'message' => $message,
                ];
            }
        }

        if (!empty($bill['id']) && $this->normalizeBillStatus($bill['bill_status'] ?? '') === 'approved') {
            $notifications[] = [
                'id'      => (string)$bill['id'] . ':approved',
                'type'    => 'bill.approved',
                'title'   => 'Bill Approved',
                'message' => 'Your bill has been approved. You can now pay.',
            ];
        }

        $paymentStatus = $this->normalizePaymentStatus($bill['payment_status'] ?? ($payment['status'] ?? ''));
        if (!empty($bill['id']) && $paymentStatus === 'paid') {
            $notifications[] = [
                'id'      => (string)$bill['id'] . ':paid',
                'type'    => 'payment.paid',
                'title'   => 'Payment Successful',
                'message' => 'Bill closed. Thank you for dining with us.',
            ];
        }

        return $notifications;
    }

    protected function orderStatus(array $input): void
    {
        $this->ensureRequestMethod('GET');

        $context = $this->resolveContext($input);
        $table = $this->resolvedTable($context);

        $order = [];
        $items = [];
        $bill = [];
        $payment = [];
        $latestServiceRequest = [];

        if (!empty($table['id'])) {
            $tableId = (string)$table['id'];
            $this->safeSyncPaidTableAvailability($context['brand_id'], $context['branch_id'], [$tableId]);
            $table = $this->resolvedTable($context);
            $tableId = (string)($table['id'] ?? $tableId);

            $order = $this->customerModel->getActiveOrderForTable(
                $context['brand_id'],
                $context['branch_id'],
                $tableId
            ) ?? [];

            $latestServiceRequest = $this->customerModel->getLatestServiceRequest(
                $context['brand_id'],
                $context['branch_id'],
                $tableId
            ) ?? [];
        }

        if (!empty($order['id'])) {
            $items = $this->customerModel->getOrderItemsForTracking((string)$order['id']);
            $bill = $this->customerModel->getBillByOrderId((string)$order['id']) ?? [];

            if (!empty($bill['id'])) {
                $payment = $this->customerModel->getLatestPaymentByBillId((string)$bill['id']) ?? [];
            }
        } elseif (!empty($table['id']) && !empty($table['current_bill_id'])) {
            $bill = $this->customerModel->getBillById((string)$table['current_bill_id']) ?? [];
            if (!empty($bill['id'])) {
                $payment = $this->customerModel->getLatestPaymentByBillId((string)$bill['id']) ?? [];
            }
        }

        $order = $this->normalizeOrder($order);
        $items = $this->normalizeOrderItems($items);
        $bill = $this->normalizeBill($bill);
        $payment = $this->normalizePayment($payment);
        $latestServiceRequest = $this->normalizeServiceRequest($latestServiceRequest);

        $cart = $this->customerModel->getCart($context['brand_id'], $context['branch_id'], $context['table_code']);
        $paymentFlags = $this->buildBillPaymentFlags($bill, $payment);

        $this->success('Order status loaded.', [
            'context' => [
                'brand_id'   => $context['brand_id'],
                'branch_id'  => $context['branch_id'],
                'table_id'   => (string)($table['id'] ?? ''),
                'table_code' => (string)($table['table_code'] ?? $context['table_code']),
            ],
            'order'                  => $order,
            'order_items'            => $items,
            'bill'                   => $bill,
            'payment'                => $payment,
            'latest_service_request' => $latestServiceRequest,
            'customer_notifications' => $this->customerNotifications($context, $table, $order, $bill, $payment, $latestServiceRequest),
            'can_pay_now'            => $paymentFlags['can_pay_now'],
            'payment_initiated'      => $paymentFlags['payment_initiated'],
            'is_paid'                => $paymentFlags['is_paid'],
            'cart'                   => $this->customerModel->rebuildCartSummary($cart),
        ]);
    }

    protected function cartSummary(array $input): void
    {
        $this->ensureRequestMethod('GET');

        $context = $this->resolveContext($input);
        $cart = $this->customerModel->getCart($context['brand_id'], $context['branch_id'], $context['table_code']);

        $this->success('Cart summary loaded.', [
            'context' => $context,
            'cart'    => $this->customerModel->rebuildCartSummary($cart),
        ]);
    }

    protected function menuItem(array $input): void
    {
        $this->ensureRequestMethod('GET');

        $context = $this->resolveContext($input);
        $menuItemId = trim((string)($_GET['menu_item_id'] ?? $input['menu_item_id'] ?? ''));

        if ($menuItemId === '') {
            $this->error('menu_item_id is required.', 400);
        }

        $item = $this->customerModel->getMenuItemDetail($context['brand_id'], $context['branch_id'], $menuItemId);
        if (!$item) {
            $this->error('Menu item not found.', 404);
        }

        $this->success('Menu item loaded.', ['item' => $item]);
    }

    protected function changeTable(array $input): void
    {
        $this->ensureRequestMethod('POST');

        $context = $this->resolveContext($input);
        $newTableCode = $this->customerModel->normalizeTableCode((string)($input['table'] ?? $input['table_code'] ?? ''), '');

        if ($newTableCode === '') {
            $this->error('Please select a table.', 400);
        }

        $newTable = $this->customerModel->getTableByCode($context['brand_id'], $context['branch_id'], $newTableCode);
        if (!$newTable) {
            $this->error('Selected table was not found.', 404);
        }

        $newTableId = (string)($newTable['id'] ?? '');
        $newTableCode = (string)($newTable['table_code'] ?? $newTable['table_number'] ?? $newTableCode);

        $this->refreshSessionContext($context['brand_id'], $context['branch_id'], $newTableId, $newTableCode);

        $this->success('Table changed successfully.', [
            'table' => $newTable,
            'cart'  => $this->customerModel->rebuildCartSummary([]),
        ]);
    }

    protected function serviceRequest(array $input): void
    {
        $this->ensureRequestMethod('POST');

        $context = $this->resolveContext($input);
        $table = $this->resolvedTable($context);

        if (empty($table['id'])) {
            $this->error('Table not found for the selected context.', 404);
        }

        $type = strtolower(trim((string)($input['type'] ?? $input['request_type'] ?? 'waiter')));
        $message = trim((string)($input['message'] ?? ''));

        $allowedTypes = [
            'waiter',
            'water',
            'bill',
            'cleaning',
            'assistance',
            'payment_collection_cash',
            'payment_collection_upi_verify',
        ];

        if (!in_array($type, $allowedTypes, true)) {
            $type = 'waiter';
        }

        $tableId = (string)$table['id'];
        $activeOrder = $this->customerModel->getActiveOrderForTable(
            $context['brand_id'],
            $context['branch_id'],
            $tableId
        ) ?? [];

        $orderId = (string)($activeOrder['id'] ?? '');

        $requestId = $this->customerModel->createServiceRequest(
            $context['brand_id'],
            $context['branch_id'],
            $tableId,
            $orderId !== '' ? $orderId : null,
            $type,
            $message !== '' ? $message : $this->customerModel->defaultServiceMessage($type)
        );

        if ($requestId === null) {
            $this->error('Unable to create service request.', 500);
        }

        $latest = $this->customerModel->getLatestServiceRequest(
            $context['brand_id'],
            $context['branch_id'],
            $tableId
        ) ?? [];

        $this->customerModel->pushRealtimeEvent(
            $context['brand_id'],
            $context['branch_id'],
            'service_request.created',
            'service_request',
            $requestId,
            [
                'table_id'     => $tableId,
                'table_code'   => (string)($table['table_code'] ?? ''),
                'order_id'     => $orderId,
                'request_type' => $type,
                'status'       => 'new',
                'priority'     => 'normal',
                'message'      => $message,
            ]
        );

        $this->success(ucfirst(str_replace('_', ' ', $type)) . ' request sent.', [
            'request_id'             => $requestId,
            'service_request'        => $this->normalizeServiceRequest($latest),
            'latest_service_request' => $this->normalizeServiceRequest($latest),
        ]);
    }

    protected function addToCart(array $input): void
    {
        $this->ensureRequestMethod('POST');

        $context = $this->resolveContext($input);
        $menuItemId = trim((string)($input['menu_item_id'] ?? $input['item_id'] ?? ''));
        $quantity = max(1, (int)($input['quantity'] ?? 1));

        if ($menuItemId === '') {
            $this->error('menu_item_id is required.', 400);
        }

        $item = $this->customerModel->getMenuItemDetail($context['brand_id'], $context['branch_id'], $menuItemId);
        if (!$item) {
            $this->error('Menu item not found.', 404);
        }

        $cart = $this->customerModel->getCart($context['brand_id'], $context['branch_id'], $context['table_code']);

        if (!isset($cart[$menuItemId])) {
            $price = (float)($item['selling_price'] ?? $item['base_price'] ?? 0);

            $cart[$menuItemId] = [
                'menu_item_id'  => (string)($item['id'] ?? $menuItemId),
                'name'          => (string)($item['name'] ?? 'Menu Item'),
                'description'   => (string)($item['description'] ?? ''),
                'price'         => $price,
                'unit_price'    => $price,
                'quantity'      => 0,
                'category_name' => (string)($item['category_name'] ?? ''),
                'image_url'     => (string)($item['image_url'] ?? ''),
                'tax_rate'      => (float)($item['tax_rate'] ?? 0),
            ];
        }

        $cart[$menuItemId]['quantity'] = max(0, (int)($cart[$menuItemId]['quantity'] ?? 0)) + $quantity;
        $this->customerModel->setCart($context['brand_id'], $context['branch_id'], $context['table_code'], $cart);

        $this->success('Item added to cart.', [
            'cart' => $this->customerModel->rebuildCartSummary($cart),
        ]);
    }

    protected function updateCartQty(array $input): void
    {
        $this->ensureRequestMethod('POST');

        $context = $this->resolveContext($input);
        $menuItemId = trim((string)($input['menu_item_id'] ?? $input['item_id'] ?? ''));
        $quantity = (int)($input['quantity'] ?? 0);

        if ($menuItemId === '') {
            $this->error('menu_item_id is required.', 400);
        }

        $cart = $this->customerModel->getCart($context['brand_id'], $context['branch_id'], $context['table_code']);

        if (!isset($cart[$menuItemId])) {
            $this->error('Cart item not found.', 404);
        }

        if ($quantity <= 0) {
            unset($cart[$menuItemId]);
        } else {
            $cart[$menuItemId]['quantity'] = $quantity;
        }

        $this->customerModel->setCart($context['brand_id'], $context['branch_id'], $context['table_code'], $cart);

        $this->success('Cart updated.', [
            'cart' => $this->customerModel->rebuildCartSummary($cart),
        ]);
    }

    protected function removeFromCart(array $input): void
    {
        $this->ensureRequestMethod('POST');

        $context = $this->resolveContext($input);
        $menuItemId = trim((string)($input['menu_item_id'] ?? $input['item_id'] ?? ''));

        if ($menuItemId === '') {
            $this->error('menu_item_id is required.', 400);
        }

        $cart = $this->customerModel->getCart($context['brand_id'], $context['branch_id'], $context['table_code']);
        unset($cart[$menuItemId]);

        $this->customerModel->setCart($context['brand_id'], $context['branch_id'], $context['table_code'], $cart);

        $this->success('Item removed from cart.', [
            'cart' => $this->customerModel->rebuildCartSummary($cart),
        ]);
    }

    protected function clearCart(array $input): void
    {
        $this->ensureRequestMethod('POST');

        $context = $this->resolveContext($input);
        $this->customerModel->clearCart($context['brand_id'], $context['branch_id'], $context['table_code']);

        $this->success('Cart cleared.', [
            'cart' => $this->customerModel->rebuildCartSummary([]),
        ]);
    }

    protected function placeOrder(array $input): void
    {
        $this->ensureRequestMethod('POST');

        $context = $this->resolveContext($input);
        $table = $this->resolvedTable($context);

        if (empty($table['id'])) {
            $this->error('Table not found for the selected context.', 404);
        }

        $tableId = (string)$table['id'];
        $tableStatus = strtolower((string)($table['status'] ?? 'available'));

        if (in_array($tableStatus, ['reserved', 'maintenance'], true)) {
            $this->error('This table is not available for ordering.', 409);
        }

        $existingOrder = $this->customerModel->getActiveOrderForTable(
            $context['brand_id'],
            $context['branch_id'],
            $tableId
        ) ?? [];

        $cart = $this->customerModel->getCart($context['brand_id'], $context['branch_id'], $context['table_code']);
        $cartSummary = $this->customerModel->rebuildCartSummary($cart);

        if ((int)($cartSummary['item_count'] ?? 0) <= 0) {
            $this->error('Cart is empty.', 400);
        }

        $this->customerModel->begin();

        try {
            $orderId = (string)($existingOrder['id'] ?? '');

            if ($orderId === '') {
                $orderId = (string)$this->customerModel->createOrder(
                    $context['brand_id'],
                    $context['branch_id'],
                    $tableId,
                    'customer_pad',
                    'dine_in',
                    1,
                    trim((string)($input['notes'] ?? '')) ?: null
                );

                if ($orderId === '') {
                    throw new RuntimeException('Unable to create order.');
                }
            }

            foreach ($cartSummary['items'] as $cartItem) {
                $menuItemId = (string)($cartItem['menu_item_id'] ?? '');
                if ($menuItemId === '') {
                    continue;
                }

                $item = $this->customerModel->getMenuItemDetail($context['brand_id'], $context['branch_id'], $menuItemId);
                if (!$item) {
                    continue;
                }

                $createdItemId = $this->customerModel->createOrderItem(
                    $context['brand_id'],
                    $context['branch_id'],
                    $orderId,
                    $menuItemId,
                    (string)($cartItem['name'] ?? $item['name'] ?? 'Menu Item'),
                    (float)($cartItem['unit_price'] ?? $cartItem['price'] ?? $item['selling_price'] ?? $item['base_price'] ?? 0),
                    max(1, (int)($cartItem['quantity'] ?? 1)),
                    !empty($item['default_kitchen_station_id']) ? (string)$item['default_kitchen_station_id'] : null,
                    trim((string)($input['special_instructions'] ?? '')) ?: null
                );

                if ($createdItemId === null) {
                    throw new RuntimeException('Unable to create order item.');
                }
            }

            if (!$this->customerModel->recalculateOrderTotals($orderId)) {
                throw new RuntimeException('Unable to calculate order totals.');
            }

            if (!$this->customerModel->assignOrderToTable($tableId, $orderId)) {
                throw new RuntimeException('Unable to update table status.');
            }

            $this->customerModel->clearCart($context['brand_id'], $context['branch_id'], $context['table_code']);

            $this->customerModel->pushRealtimeEvent(
                $context['brand_id'],
                $context['branch_id'],
                'order.created',
                'order',
                $orderId,
                [
                    'table_id'   => $tableId,
                    'table_code' => (string)($table['table_code'] ?? ''),
                    'status'     => 'new',
                    'priority'   => 'normal',
                ]
            );

            $this->customerModel->commit();

            $order = $this->normalizeOrder($this->customerModel->getOrderById($orderId) ?? []);
            $items = $this->normalizeOrderItems($this->customerModel->getOrderItemsForTracking($orderId));

            $this->success('Order created successfully.', [
                'order'       => $order,
                'order_items' => $items,
                'cart'        => $this->customerModel->rebuildCartSummary([]),
            ]);
        } catch (Throwable $e) {
            $this->customerModel->rollback();
            log_message('error', 'RestCustomerController::placeOrder failed: ' . $e->getMessage());
            $this->error('Unable to place order right now.', 500);
        }
    }

    // protected function requestBill(array $input): void
    // {
    //     $this->ensureRequestMethod('POST');

    //     $context = $this->resolveContext($input);
    //     $table = $this->resolvedTable($context);

    //     if (empty($table['id'])) {
    //         $this->error('Table not found for the selected context.', 404);
    //     }

    //     $tableId = (string)$table['id'];
    //     $order = $this->customerModel->getActiveOrderForTable(
    //         $context['brand_id'],
    //         $context['branch_id'],
    //         $tableId
    //     ) ?? [];

    //     if (empty($order['id'])) {
    //         $this->error('No active order found for this table.', 400);
    //     }

    //     $orderId = (string)$order['id'];
    //     $existingBill = $this->customerModel->getBillByOrderId($orderId) ?? [];

    //     if (!empty($existingBill['id'])) {
    //         $bill = $this->normalizeBill($existingBill);
    //         $payment = [];
    //         if (!empty($bill['id'])) {
    //             $payment = $this->normalizePayment($this->customerModel->getLatestPaymentByBillId((string)$bill['id']) ?? []);
    //         }

    //         $this->success('Bill already requested.', [
    //             'bill'                   => $bill,
    //             'payment'                => $payment,
    //             'customer_notifications' => $this->customerNotifications($context, $table, $this->normalizeOrder($order), $bill, $payment, []),
    //         ]);
    //     }

    //     $this->customerModel->begin();

    //     try {
    //         $billId = $this->customerModel->createBillFromOrder(
    //             $context['brand_id'],
    //             $context['branch_id'],
    //             $tableId,
    //             $orderId
    //         );

    //         if ($billId === null) {
    //             throw new RuntimeException('Unable to create bill.');
    //         }

    //         if (!$this->customerModel->markOrderBillRequested($orderId, $billId)) {
    //             throw new RuntimeException('Unable to update order bill request.');
    //         }

    //         if (!$this->customerModel->assignBillToTable($tableId, $billId)) {
    //             throw new RuntimeException('Unable to update table bill reference.');
    //         }

    //         $serviceRequestId = $this->customerModel->createServiceRequest(
    //             $context['brand_id'],
    //             $context['branch_id'],
    //             $tableId,
    //             $orderId,
    //             'bill',
    //             'Customer requested bill.'
    //         );

    //         $bill = $this->normalizeBill($this->customerModel->getBillById($billId) ?? []);

    //         $this->customerModel->pushRealtimeEvent(
    //             $context['brand_id'],
    //             $context['branch_id'],
    //             'bill.requested',
    //             'bill',
    //             $billId,
    //             [
    //                 'table_id'           => $tableId,
    //                 'table_code'         => (string)($table['table_code'] ?? ''),
    //                 'order_id'           => $orderId,
    //                 'service_request_id' => $serviceRequestId,
    //                 'bill_status'        => 'new',
    //                 'payment_status'     => 'unpaid',
    //                 'total_amount'       => (float)($bill['total_amount'] ?? 0),
    //             ]
    //         );

    //         $this->customerModel->commit();

    //         $this->success('Bill requested successfully.', [
    //             'bill' => $bill,
    //         ]);
    //     } catch (Throwable $e) {
    //         $this->customerModel->rollback();
    //         log_message('error', 'RestCustomerController::requestBill failed: ' . $e->getMessage());
    //         $this->error('Unable to request bill right now.', 500);
    //     }
    // }


    protected function requestBill(array $input): void
{
    $this->ensureRequestMethod('POST');

    $context = $this->resolveContext($input);
    $table = $this->resolvedTable($context);

    if (empty($table['id'])) {
        $this->error('Table not found for the selected context.', 404);
    }

    $tableId = (string)$table['id'];

    $order = $this->customerModel->getActiveOrderForTable(
        $context['brand_id'],
        $context['branch_id'],
        $tableId
    ) ?? [];

    if (empty($order['id'])) {
        $this->error('No active order found for this table.', 400);
    }

    $orderId = (string)$order['id'];

    /*
     * Important:
     * Recalculate order before bill creation/update.
     * This makes sure newly placed items after first bill request
     * are included in the latest total.
     */
    if (!$this->customerModel->recalculateOrderTotals($orderId)) {
        $this->error('Unable to refresh order totals.', 500);
    }

    $order = $this->customerModel->getOrderById($orderId) ?? $order;
    $existingBill = $this->customerModel->getBillByOrderId($orderId) ?? [];

    /*
     * Existing bill flow:
     * - If bill is already paid/closed, do not reopen.
     * - If payment is processing, do not modify totals.
     * - Otherwise refresh the same bill with latest order totals/items.
     * This allows customer to order again after requesting bill once,
     * then request bill again for updated total order bill.
     */
    if (!empty($existingBill['id'])) {
        $bill = $this->normalizeBill($existingBill);
        $payment = [];

        if (!empty($bill['id'])) {
            $payment = $this->normalizePayment(
                $this->customerModel->getLatestPaymentByBillId((string)$bill['id']) ?? []
            );
        }

        $billStatus = $this->normalizeBillStatus((string)($bill['bill_status'] ?? ''));
        $paymentStatus = $this->normalizePaymentStatus(
            (string)($bill['payment_status'] ?? ($payment['status'] ?? ''))
        );

        if ($paymentStatus === 'paid' || $billStatus === 'closed') {
            $this->success('Bill already closed.', [
                'order'                  => $this->normalizeOrder($order),
                'order_items'            => $this->normalizeOrderItems($this->customerModel->getOrderItemsForTracking($orderId)),
                'bill'                   => $bill,
                'payment'                => $payment,
                'customer_notifications' => $this->customerNotifications(
                    $context,
                    $table,
                    $this->normalizeOrder($order),
                    $bill,
                    $payment,
                    []
                ),
            ]);
        }

        if ($paymentStatus === 'in_processing') {
            $this->success('Payment already in processing.', [
                'order'                  => $this->normalizeOrder($order),
                'order_items'            => $this->normalizeOrderItems($this->customerModel->getOrderItemsForTracking($orderId)),
                'bill'                   => $bill,
                'payment'                => $payment,
                'customer_notifications' => $this->customerNotifications(
                    $context,
                    $table,
                    $this->normalizeOrder($order),
                    $bill,
                    $payment,
                    []
                ),
            ]);
        }

        $this->customerModel->begin();

        try {
            if (!$this->customerModel->refreshExistingBillFromOrder((string)$bill['id'], $orderId)) {
                throw new RuntimeException('Unable to refresh existing bill.');
            }

            if (!$this->customerModel->markOrderBillRequested($orderId, (string)$bill['id'])) {
                throw new RuntimeException('Unable to update order bill request.');
            }

            if (!$this->customerModel->assignBillToTable($tableId, (string)$bill['id'])) {
                throw new RuntimeException('Unable to update table bill reference.');
            }

            $serviceRequestId = $this->customerModel->createServiceRequest(
                $context['brand_id'],
                $context['branch_id'],
                $tableId,
                $orderId,
                'bill',
                'Customer requested updated bill.'
            );

            $updatedBill = $this->normalizeBill(
                $this->customerModel->getBillById((string)$bill['id']) ?? []
            );

            $updatedOrder = $this->normalizeOrder(
                $this->customerModel->getOrderById($orderId) ?? []
            );

            $orderItems = $this->normalizeOrderItems(
                $this->customerModel->getOrderItemsForTracking($orderId)
            );

            $this->customerModel->pushRealtimeEvent(
                $context['brand_id'],
                $context['branch_id'],
                'bill.requested',
                'bill',
                (string)$bill['id'],
                [
                    'table_id'           => $tableId,
                    'table_code'         => (string)($table['table_code'] ?? ''),
                    'order_id'           => $orderId,
                    'service_request_id' => $serviceRequestId,
                    'bill_status'        => 'new',
                    'payment_status'     => 'unpaid',
                    'total_amount'       => (float)($updatedBill['total_amount'] ?? 0),
                    'is_updated_bill'    => true,
                ]
            );

            $this->customerModel->commit();

            $this->success('Updated bill requested successfully.', [
                'order'                  => $updatedOrder,
                'order_items'            => $orderItems,
                'bill'                   => $updatedBill,
                'payment'                => [],
                'customer_notifications' => $this->customerNotifications(
                    $context,
                    $table,
                    $updatedOrder,
                    $updatedBill,
                    [],
                    []
                ),
            ]);
        } catch (Throwable $e) {
            $this->customerModel->rollback();
            log_message('error', 'RestCustomerController::requestBill existing refresh failed: ' . $e->getMessage());
            $this->error('Unable to refresh bill right now.', 500);
        }
    }

    /*
     * New bill flow.
     */
    $this->customerModel->begin();

    try {
        $billId = $this->customerModel->createBillFromOrder(
            $context['brand_id'],
            $context['branch_id'],
            $tableId,
            $orderId
        );

        if ($billId === null) {
            throw new RuntimeException('Unable to create bill.');
        }

        if (!$this->customerModel->markOrderBillRequested($orderId, $billId)) {
            throw new RuntimeException('Unable to update order bill request.');
        }

        if (!$this->customerModel->assignBillToTable($tableId, $billId)) {
            throw new RuntimeException('Unable to update table bill reference.');
        }

        $serviceRequestId = $this->customerModel->createServiceRequest(
            $context['brand_id'],
            $context['branch_id'],
            $tableId,
            $orderId,
            'bill',
            'Customer requested bill.'
        );

        $bill = $this->normalizeBill($this->customerModel->getBillById($billId) ?? []);
        $updatedOrder = $this->normalizeOrder($this->customerModel->getOrderById($orderId) ?? []);
        $orderItems = $this->normalizeOrderItems($this->customerModel->getOrderItemsForTracking($orderId));

        $this->customerModel->pushRealtimeEvent(
            $context['brand_id'],
            $context['branch_id'],
            'bill.requested',
            'bill',
            $billId,
            [
                'table_id'           => $tableId,
                'table_code'         => (string)($table['table_code'] ?? ''),
                'order_id'           => $orderId,
                'service_request_id' => $serviceRequestId,
                'bill_status'        => 'new',
                'payment_status'     => 'unpaid',
                'total_amount'       => (float)($bill['total_amount'] ?? 0),
                'is_updated_bill'    => false,
            ]
        );

        $this->customerModel->commit();

        $this->success('Bill requested successfully.', [
            'order'                  => $updatedOrder,
            'order_items'            => $orderItems,
            'bill'                   => $bill,
            'payment'                => [],
            'customer_notifications' => $this->customerNotifications(
                $context,
                $table,
                $updatedOrder,
                $bill,
                [],
                []
            ),
        ]);
    } catch (Throwable $e) {
        $this->customerModel->rollback();
        log_message('error', 'RestCustomerController::requestBill failed: ' . $e->getMessage());
        $this->error('Unable to request bill right now.', 500);
    }
}

    protected function initiatePayment(array $input): void
    {
        $this->ensureRequestMethod('POST');

        $context = $this->resolveContext($input);
        $table = $this->resolvedTable($context);

        if (empty($table['id'])) {
            $this->error('Table not found for the selected context.', 404);
        }

        $tableId = (string)$table['id'];
        $billId = trim((string)($input['bill_id'] ?? ''));
        $method = strtolower(trim((string)($input['payment_method'] ?? 'upi')));
        $txnRef = trim((string)($input['txn_ref'] ?? ''));
        $upiId = trim((string)($input['upi_id'] ?? ''));
        $amount = (float)($input['amount'] ?? 0);

        if ($billId === '') {
            $bill = $this->resolveBillForContext($context, $input);
            $billId = (string)($bill['id'] ?? '');
        } else {
            $bill = $this->customerModel->getBillById($billId) ?? [];
        }

        if ($billId === '' || empty($bill)) {
            $this->error('Bill is required.', 400);
        }

        if (
            (string)($bill['brand_id'] ?? '') !== $context['brand_id'] ||
            (string)($bill['branch_id'] ?? '') !== $context['branch_id'] ||
            (string)($bill['table_id'] ?? '') !== $tableId
        ) {
            $this->error('Bill does not belong to the selected table.', 403);
        }

        $billStatus = $this->normalizeBillStatus($bill['bill_status'] ?? '');
        if ($billStatus !== 'approved') {
            $this->error('Bill is not approved for payment yet.', 400);
        }

        if (!in_array($method, ['upi', 'cash'], true)) {
            $this->error('Unsupported payment method.', 400);
        }

        if ($this->normalizePaymentStatus($bill['payment_status'] ?? '') === 'paid') {
            $this->error('Bill is already paid.', 400);
        }

        $latestPayment = $this->customerModel->getLatestPaymentByBillId($billId) ?? [];
        $latestPaymentStatus = $this->normalizePaymentStatus($latestPayment['status'] ?? '');

        if (!empty($latestPayment) && $latestPaymentStatus === 'in_processing') {
            $this->success('Payment already initiated.', [
                'bill'    => $this->normalizeBill($bill),
                'payment' => $this->normalizePayment($latestPayment),
            ]);
        }

        $orderId = (string)($bill['order_id'] ?? '');

        $this->customerModel->begin();

        try {
            $paymentId = $this->customerModel->createPaymentRequest(
                $context['brand_id'],
                $context['branch_id'],
                $tableId,
                $billId,
                $orderId,
                $method,
                $amount > 0 ? $amount : (float)($bill['total_amount'] ?? 0),
                [
                    'txn_ref'        => $txnRef,
                    'upi_id'         => $upiId,
                    'initiated_from' => 'customer_pad',
                ]
            );

            if ($paymentId === null) {
                throw new RuntimeException('Unable to create payment request.');
            }

            if (!$this->customerModel->markBillPaymentPending($billId, 'in_processing')) {
                throw new RuntimeException('Unable to update bill payment status.');
            }

            if (!$this->customerModel->markOrderPaymentPending($orderId)) {
                throw new RuntimeException('Unable to update order state.');
            }

            $serviceRequestId = $this->customerModel->createServiceRequest(
                $context['brand_id'],
                $context['branch_id'],
                $tableId,
                $orderId !== '' ? $orderId : null,
                $method === 'cash' ? 'payment_collection_cash' : 'payment_collection_upi_verify',
                $method === 'cash'
                    ? 'Customer is ready to pay by cash.'
                    : 'Customer initiated UPI payment and needs verification.'
            );

            $this->customerModel->pushRealtimeEvent(
                $context['brand_id'],
                $context['branch_id'],
                'payment.initiated',
                'payment',
                $paymentId,
                [
                    'bill_id'            => $billId,
                    'order_id'           => $orderId,
                    'table_id'           => $tableId,
                    'method'             => $method,
                    'payment_status'     => 'in_processing',
                    'service_request_id' => $serviceRequestId,
                ]
            );

            $this->customerModel->commit();

            $updatedBill = $this->normalizeBill($this->customerModel->getBillById($billId) ?? []);
            $payment = $this->normalizePayment($this->customerModel->getPaymentById($paymentId) ?? []);

            $this->success('Payment initiated successfully.', [
                'bill'    => $updatedBill,
                'payment' => $payment,
            ]);
        } catch (Throwable $e) {
            $this->customerModel->rollback();
            log_message('error', 'RestCustomerController::initiatePayment failed: ' . $e->getMessage());
            $this->error('Unable to initiate payment right now.', 500);
        }
    }

    protected function paymentStatus(array $input): void
    {
        $this->ensureRequestMethod('GET');

        $context = $this->resolveContext($input);
        $table = $this->resolvedTable($context);

        if (!empty($table['id'])) {
            $this->safeSyncPaidTableAvailability(
                $context['brand_id'],
                $context['branch_id'],
                [(string)$table['id']]
            );
        }

        $bill = $this->normalizeBill($this->resolveBillForContext($context, $input));

        if (empty($bill['id'])) {
            $this->success('Payment status loaded.', [
                'bill'                   => [],
                'payment'                => [],
                'customer_notifications' => [],
                'can_pay_now'            => false,
                'payment_initiated'      => false,
                'is_paid'                => false,
            ]);
        }

        $billId = (string)$bill['id'];
        $payment = $this->normalizePayment($this->customerModel->getLatestPaymentByBillId($billId) ?? []);
        $paymentFlags = $this->buildBillPaymentFlags($bill, $payment);

        $order = [];
        if (!empty($bill['order_id'])) {
            $order = $this->normalizeOrder($this->customerModel->getOrderById((string)$bill['order_id']) ?? []);
        }

        $latestServiceRequest = [];
        if (!empty($table['id'])) {
            $latestServiceRequest = $this->normalizeServiceRequest($this->customerModel->getLatestServiceRequest(
                $context['brand_id'],
                $context['branch_id'],
                (string)$table['id']
            ) ?? []);
        }

        $this->success('Payment status loaded.', [
            'bill'                   => $bill,
            'payment'                => $payment,
            'customer_notifications' => $this->customerNotifications($context, $table, $order, $bill, $payment, $latestServiceRequest),
            'can_pay_now'            => $paymentFlags['can_pay_now'],
            'payment_initiated'      => $paymentFlags['payment_initiated'],
            'is_paid'                => $paymentFlags['is_paid'],
        ]);
    }

    protected function buildBillPaymentFlags(array $bill, array $payment = []): array
    {
        $billStatus = $this->normalizeBillStatus($bill['bill_status'] ?? '');
        $billPaymentStatus = $this->normalizePaymentStatus($bill['payment_status'] ?? '');
        $paymentStatus = $this->normalizePaymentStatus($payment['status'] ?? '');

        return [
            'can_pay_now' => $billStatus === 'approved' && $billPaymentStatus !== 'paid',
            'payment_initiated' => $paymentStatus === 'in_processing' || $billPaymentStatus === 'in_processing',
            'is_paid' => $billPaymentStatus === 'paid' || $paymentStatus === 'paid' || $billStatus === 'closed',
        ];
    }

    protected function realtimeEvents(array $input): void
    {
        $this->ensureRequestMethod('GET');

        $context = $this->resolveContext($input);
        $afterId = max(0, (int)($_GET['after_id'] ?? $input['after_id'] ?? 0));

        $events = $this->customerModel->getRealtimeEvents(
            $context['brand_id'],
            $context['branch_id'],
            $afterId,
            100
        );

        $this->success('Realtime events loaded.', [
            'events' => $events,
        ]);
    }
}
