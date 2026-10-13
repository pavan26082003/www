<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\RestManagerModel;
use RuntimeException;
use Throwable;

require_once APPPATH . 'Models/RestManagerModel.php';

final class RestManagerController extends RestBaseController
{
    private const VIEW_PATH = APPPATH . 'Views/restaurant-system/manager.php';

    /** @var RestManagerModel */
    protected $managerModel;

    public function __construct()
    {
        parent::__construct();
        $this->managerModel = new RestManagerModel();
    }

    public function index(): void
    {
        $context = $this->resolveContext($this->requestData());

        $pageData = $this->managerModel->getManagerPageData(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        $this->renderView(self::VIEW_PATH, $pageData);
    }

    public function api(): void
    {
        try {
            $input = $this->requestData();
            $action = trim((string) ($_GET['action'] ?? $input['action'] ?? ''));

            if ($action === '') {
                $this->error('Action is required.', 400);
            }

            switch ($action) {
                case 'dashboard_snapshot':
                    $this->dashboardSnapshot($input);
                    return;

                case 'order_detail':
                    $this->orderDetail($input);
                    return;

                case 'bill_detail':
                case 'open_bill':
                    $this->billDetail($input);
                    return;

                case 'staff_detail':
                    $this->staffDetail($input);
                    return;

                case 'menu_detail':
                    $this->menuDetail($input);
                    return;

                case 'create_staff':
                case 'save_user':
                    $this->createStaff($input);
                    return;

                case 'update_staff':
                    $this->updateStaff($input);
                    return;

                case 'toggle_staff_status':
                    $this->toggleStaffStatus($input);
                    return;

                case 'delete_staff':
                    $this->deleteStaff($input);
                    return;

                case 'create_menu_item':
                case 'save_menu_item':
                    $this->createMenuItem($input);
                    return;

                case 'update_menu_item':
                    $this->updateMenuItem($input);
                    return;

                case 'toggle_menu_item_status':
                    $this->toggleMenuItemStatus($input);
                    return;

                case 'delete_menu_item':
                    $this->deleteMenuItem($input);
                    return;

                case 'delete_order':
                    $this->deleteOrder($input);
                    return;

                case 'delete_request':
                    $this->deleteRequest($input);
                    return;

                case 'delete_bill':
                    $this->deleteBill($input);
                    return;

                case 'delete_table':
                    $this->deleteTable($input);
                    return;

                case 'save_waiter_assignment':
                    $this->saveWaiterAssignment($input);
                    return;

                case 'update_order_status':
                    $this->updateOrderStatus($input);
                    return;

                case 'resolve_request':
                    $this->resolveRequest($input);
                    return;

                case 'mark_notification_read':
                    $this->markNotificationRead($input);
                    return;

                case 'delete_notification':
                    $this->deleteNotification($input);
                    return;

                case 'clear_notifications':
                case 'clear_manager_notifications':
                    $this->clearNotifications($input);
                    return;

                case 'save_table':
                    $this->saveTable($input);
                    return;

                case 'update_bill_status':
                case 'save_bill':
                case 'close_bill':
                case 'mark_bill_paid':
                    $this->updateBillStatus($input);
                    return;

                default:
                    $this->error('Unknown action.', 404);
                    return;
            }
        } catch (Throwable $e) {
            $code = (int) $e->getCode();
            if ($code < 400 || $code > 599) {
                $code = 500;
            }

            $this->error(
                $e->getMessage() !== '' ? $e->getMessage() : 'Manager action failed.',
                $code
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
        return strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    }

    protected function contextSessionKey(): string
    {
        return 'rest_manager_context';
    }

    protected function resolveContext(array $input = []): array
    {
        $sessionContext = $_SESSION[$this->contextSessionKey()] ?? [];

        $brandId = (string) (
            $input['brand_id']
            ?? $_GET['brand_id']
            ?? $sessionContext['brand_id']
            ?? $this->currentBrandId()
        );

        $branchId = (string) (
            $input['branch_id']
            ?? $_GET['branch_id']
            ?? $sessionContext['branch_id']
            ?? $this->currentBranchId()
        );

        $userId = (string) (
            $input['user_id']
            ?? $_GET['user_id']
            ?? $sessionContext['user_id']
            ?? $this->currentUserId()
        );

        $brandId = $this->managerModel->normalizeEntityIdOrDefault($brandId, $this->currentBrandId());
        $branchId = $this->managerModel->normalizeEntityIdOrDefault($branchId, $this->currentBranchId());
        $userId = $this->managerModel->normalizeEntityIdOrDefault($userId, $this->currentUserId());

        $context = [
            'brand_id'  => $brandId,
            'branch_id' => $branchId,
            'user_id'   => $userId,
        ];

        $_SESSION[$this->contextSessionKey()] = $context;
        $_SESSION['rest_brand_id'] = $brandId;
        $_SESSION['rest_branch_id'] = $branchId;
        $_SESSION['rest_user_id'] = $userId;

        return $context;
    }

    protected function refreshPayload(string $brandId, string $branchId, string $userId, string $message): void
    {
        $snapshot = $this->managerModel->buildDashboardSnapshot($brandId, $branchId, $userId);

        if ($snapshot === null) {
            $this->error('Unable to refresh manager dashboard.', 500);
        }

        $this->json([
            'ok'       => true,
            'message'  => $message,
            'snapshot' => $snapshot,
            'graph'    => $snapshot['revenue_chart'] ?? [],
        ]);
    }

    protected function dashboardSnapshot(array $input): void
    {
        if (!in_array($this->requestMethod(), ['GET', 'POST'], true)) {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $snapshot = $this->managerModel->buildDashboardSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        if ($snapshot === null) {
            $this->error('Unable to load manager dashboard.', 404);
        }

        $this->json([
            'ok'       => true,
            'snapshot' => $snapshot,
            'graph'    => $snapshot['revenue_chart'] ?? [],
            'message'  => 'Dashboard loaded.',
        ]);
    }

    protected function orderDetail(array $input): void
    {
        $context = $this->resolveContext($input);
        $orderId = trim((string) ($_GET['order_id'] ?? $input['order_id'] ?? ''));

        if ($orderId === '') {
            $this->error('Order id is required.', 422);
        }

        $detail = $this->managerModel->getOrderDetail(
            $context['brand_id'],
            $context['branch_id'],
            $orderId
        );

        if ($detail === null) {
            $this->error('Order not found.', 404);
        }

        $this->json([
            'ok'     => true,
            'detail' => $detail,
        ]);
    }

    protected function billDetail(array $input): void
    {
        $context = $this->resolveContext($input);
        $billId = trim((string) ($_GET['bill_id'] ?? $input['bill_id'] ?? ''));

        if ($billId === '') {
            $this->error('Bill id is required.', 422);
        }

        $detail = $this->managerModel->getBillDetail(
            $context['brand_id'],
            $context['branch_id'],
            $billId
        );

        if ($detail === null) {
            $this->error('Bill not found.', 404);
        }

        $this->json([
            'ok'     => true,
            'detail' => $detail,
        ]);
    }

    protected function staffDetail(array $input): void
    {
        $context = $this->resolveContext($input);
        $targetUserId = trim((string) ($_GET['user_id'] ?? $input['user_id'] ?? ''));

        if ($targetUserId === '') {
            $this->error('User id is required.', 422);
        }

        $detail = $this->managerModel->getStaffDetail(
            $context['brand_id'],
            $context['branch_id'],
            $targetUserId
        );

        if ($detail === null) {
            $this->error('Staff member not found.', 404);
        }

        $this->json([
            'ok'     => true,
            'detail' => $detail,
        ]);
    }

    protected function menuDetail(array $input): void
    {
        $context = $this->resolveContext($input);
        $menuId = trim((string) ($_GET['menu_id'] ?? $input['menu_id'] ?? ''));

        if ($menuId === '') {
            $this->error('Menu id is required.', 422);
        }

        $detail = $this->managerModel->getMenuItemDetail(
            $context['brand_id'],
            $context['branch_id'],
            $menuId
        );

        if ($detail === null) {
            $this->error('Menu item not found.', 404);
        }

        $this->json([
            'ok'     => true,
            'detail' => $detail,
        ]);
    }

    protected function createStaff(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $payload = [
            'full_name'            => trim((string) ($input['full_name'] ?? '')),
            'email'                => strtolower(trim((string) ($input['email'] ?? ''))),
            'phone'                => trim((string) ($input['phone'] ?? '')),
            'employee_code'        => trim((string) ($input['employee_code'] ?? '')),
            'profile_pic'          => trim((string) ($input['profile_pic'] ?? '')),
            'aadhar_no'            => trim((string) ($input['aadhar_no'] ?? '')),
            'role_at_branch'       => trim((string) ($input['role_at_branch'] ?? $input['role'] ?? 'waiter')),
            'is_active'            => trim((string) ($input['is_active'] ?? '1')),
            'assigned_table_codes' => $this->managerModel->normalizeCsvList($input['assigned_table_codes'] ?? []),
            'shift_start_time'     => trim((string) ($input['shift_start_time'] ?? '')),
            'shift_end_time'       => trim((string) ($input['shift_end_time'] ?? '')),
            'permissions'          => is_array($input['permissions'] ?? null) ? $input['permissions'] : [],
        ];

        if ($payload['full_name'] === '' || $payload['email'] === '') {
            $this->error('Full name and email are required.', 422);
        }

        if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error('Please enter a valid email address.', 422);
        }

        if (!$this->managerModel->createStaff(
            $context['brand_id'],
            $context['branch_id'],
            $payload,
            $context['user_id']
        )) {
            $this->error('Unable to create staff.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Staff created successfully.'
        );
    }

    protected function updateStaff(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $targetUserId = trim((string) ($input['target_user_id'] ?? ''));

        if ($targetUserId === '') {
            $this->error('Target user id is required.', 422);
        }

        $payload = [
            'full_name'            => trim((string) ($input['full_name'] ?? '')),
            'email'                => strtolower(trim((string) ($input['email'] ?? ''))),
            'phone'                => trim((string) ($input['phone'] ?? '')),
            'employee_code'        => trim((string) ($input['employee_code'] ?? '')),
            'profile_pic'          => trim((string) ($input['profile_pic'] ?? '')),
            'aadhar_no'            => trim((string) ($input['aadhar_no'] ?? '')),
            'role_at_branch'       => trim((string) ($input['role_at_branch'] ?? $input['role'] ?? 'waiter')),
            'is_active'            => trim((string) ($input['is_active'] ?? '1')),
            'assigned_table_codes' => $this->managerModel->normalizeCsvList($input['assigned_table_codes'] ?? []),
            'shift_start_time'     => trim((string) ($input['shift_start_time'] ?? '')),
            'shift_end_time'       => trim((string) ($input['shift_end_time'] ?? '')),
            'permissions'          => is_array($input['permissions'] ?? null) ? $input['permissions'] : [],
        ];

        if ($payload['full_name'] === '' || $payload['email'] === '') {
            $this->error('Full name and email are required.', 422);
        }

        if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error('Please enter a valid email address.', 422);
        }

        if (!$this->managerModel->updateStaff(
            $context['brand_id'],
            $context['branch_id'],
            $targetUserId,
            $payload,
            $context['user_id']
        )) {
            $this->error('Unable to update staff.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Staff updated successfully.'
        );
    }

    protected function toggleStaffStatus(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $targetUserId = trim((string) ($input['target_user_id'] ?? ''));

        if ($targetUserId === '') {
            $this->error('Target user id is required.', 422);
        }

        if (!$this->managerModel->toggleStaffStatus(
            $context['brand_id'],
            $context['branch_id'],
            $targetUserId,
            $context['user_id']
        )) {
            $this->error('Unable to change staff status.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Staff status updated successfully.'
        );
    }

    protected function deleteStaff(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $targetUserId = trim((string) ($input['target_user_id'] ?? ''));

        if ($targetUserId === '') {
            $this->error('Target user id is required.', 422);
        }

        if (!$this->managerModel->deleteStaff(
            $context['brand_id'],
            $context['branch_id'],
            $targetUserId,
            $context['user_id']
        )) {
            $this->error('Unable to delete staff.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Staff removed successfully.'
        );
    }

    protected function createMenuItem(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $payload = [
            'category_id'                => trim((string) ($input['category_id'] ?? '')),
            'category_name'              => trim((string) ($input['category_name'] ?? $input['category'] ?? '')),
            'default_kitchen_station_id' => trim((string) ($input['default_kitchen_station_id'] ?? '')),
            'name'                       => trim((string) ($input['name'] ?? $input['item_name'] ?? '')),
            'sku'                        => trim((string) ($input['sku'] ?? '')),
            'description'                => trim((string) ($input['description'] ?? '')),
            'image_url'                  => trim((string) ($input['image_url'] ?? '')),
            'video_url'                  => trim((string) ($input['video_url'] ?? '')),
            'video_type'                 => trim((string) ($input['video_type'] ?? '')),
            'item_type'                  => trim((string) ($input['item_type'] ?? 'food')),
            'base_price'                 => trim((string) ($input['base_price'] ?? $input['price'] ?? '0')),
            'selling_price'              => trim((string) ($input['selling_price'] ?? $input['price'] ?? '0')),
            'tax_rate'                   => trim((string) ($input['tax_rate'] ?? '0')),
            'preparation_time_mins'      => trim((string) ($input['preparation_time_mins'] ?? '0')),
            'is_veg'                     => trim((string) ($input['is_veg'] ?? '0')),
            'is_vegan'                   => trim((string) ($input['is_vegan'] ?? '0')),
            'is_spicy'                   => trim((string) ($input['is_spicy'] ?? '0')),
            'is_available'               => trim((string) ($input['is_available'] ?? '1')),
            'stock_status'               => trim((string) ($input['stock_status'] ?? 'in_stock')),
            'available_from'             => trim((string) ($input['available_from'] ?? '')),
            'available_to'               => trim((string) ($input['available_to'] ?? '')),
        ];

        if ($payload['name'] === '') {
            $this->error('Menu item name is required.', 422);
        }

        if (
            !is_numeric($payload['base_price']) ||
            !is_numeric($payload['selling_price']) ||
            !is_numeric($payload['tax_rate'])
        ) {
            $this->error('Base price, selling price, and tax rate must be numeric.', 422);
        }

        if (!$this->managerModel->createMenuItem(
            $context['brand_id'],
            $context['branch_id'],
            $payload,
            $context['user_id']
        )) {
            $this->error('Unable to create menu item.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Menu item created successfully.'
        );
    }

    protected function updateMenuItem(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $menuId = trim((string) ($input['menu_id'] ?? ''));

        if ($menuId === '') {
            $this->error('Menu id is required.', 422);
        }

        $payload = [
            'category_id'                => trim((string) ($input['category_id'] ?? '')),
            'category_name'              => trim((string) ($input['category_name'] ?? $input['category'] ?? '')),
            'default_kitchen_station_id' => trim((string) ($input['default_kitchen_station_id'] ?? '')),
            'name'                       => trim((string) ($input['name'] ?? $input['item_name'] ?? '')),
            'sku'                        => trim((string) ($input['sku'] ?? '')),
            'description'                => trim((string) ($input['description'] ?? '')),
            'image_url'                  => trim((string) ($input['image_url'] ?? '')),
            'video_url'                  => trim((string) ($input['video_url'] ?? '')),
            'video_type'                 => trim((string) ($input['video_type'] ?? '')),
            'item_type'                  => trim((string) ($input['item_type'] ?? 'food')),
            'base_price'                 => trim((string) ($input['base_price'] ?? $input['price'] ?? '0')),
            'selling_price'              => trim((string) ($input['selling_price'] ?? $input['price'] ?? '0')),
            'tax_rate'                   => trim((string) ($input['tax_rate'] ?? '0')),
            'preparation_time_mins'      => trim((string) ($input['preparation_time_mins'] ?? '0')),
            'is_veg'                     => trim((string) ($input['is_veg'] ?? '0')),
            'is_vegan'                   => trim((string) ($input['is_vegan'] ?? '0')),
            'is_spicy'                   => trim((string) ($input['is_spicy'] ?? '0')),
            'is_available'               => trim((string) ($input['is_available'] ?? '1')),
            'stock_status'               => trim((string) ($input['stock_status'] ?? 'in_stock')),
            'available_from'             => trim((string) ($input['available_from'] ?? '')),
            'available_to'               => trim((string) ($input['available_to'] ?? '')),
        ];

        if ($payload['name'] === '') {
            $this->error('Menu item name is required.', 422);
        }

        if (
            !is_numeric($payload['base_price']) ||
            !is_numeric($payload['selling_price']) ||
            !is_numeric($payload['tax_rate'])
        ) {
            $this->error('Base price, selling price, and tax rate must be numeric.', 422);
        }

        if (!$this->managerModel->updateMenuItem(
            $context['brand_id'],
            $context['branch_id'],
            $menuId,
            $payload,
            $context['user_id']
        )) {
            $this->error('Unable to update menu item.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Menu item updated successfully.'
        );
    }

    protected function toggleMenuItemStatus(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $menuId = trim((string) ($input['menu_id'] ?? ''));

        if ($menuId === '') {
            $this->error('Menu id is required.', 422);
        }

        if (!$this->managerModel->toggleMenuItemStatus(
            $context['brand_id'],
            $context['branch_id'],
            $menuId,
            $context['user_id']
        )) {
            $this->error('Unable to change menu item status.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Menu item status updated successfully.'
        );
    }

    protected function deleteMenuItem(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $menuId = trim((string) ($input['menu_id'] ?? ''));

        if ($menuId === '') {
            $this->error('Menu id is required.', 422);
        }

        if (!$this->managerModel->deleteMenuItem(
            $context['brand_id'],
            $context['branch_id'],
            $menuId,
            $context['user_id']
        )) {
            $this->error('Unable to delete menu item.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Menu item removed successfully.'
        );
    }

    /**
     * Permanently delete an order and refresh the dashboard.
     *
     * Expects POST with `order_id` and current context.  Calls the model
     * deleteOrder() method and then refreshes the manager payload on
     * success.
     *
     * @param array $input
     */
    protected function deleteOrder(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $orderId = trim((string) ($input['order_id'] ?? ''));

        if ($orderId === '') {
            $this->error('Order id is required.', 422);
        }

        if (!$this->managerModel->deleteOrder(
            $context['brand_id'],
            $context['branch_id'],
            $orderId,
            $context['user_id']
        )) {
            $this->error('Unable to delete order.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Order deleted successfully.'
        );
    }

    /**
     * Permanently delete a service request and refresh the dashboard.
     *
     * Expects POST with `request_id`.  Calls model deleteRequest().
     *
     * @param array $input
     */
    protected function deleteRequest(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $requestId = trim((string) ($input['request_id'] ?? ''));

        if ($requestId === '') {
            $this->error('Request id is required.', 422);
        }

        if (!$this->managerModel->deleteRequest(
            $context['brand_id'],
            $context['branch_id'],
            $requestId,
            $context['user_id']
        )) {
            $this->error('Unable to delete request.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Request deleted successfully.'
        );
    }

    /**
     * Permanently delete a bill and refresh the dashboard.
     *
     * Expects POST with `bill_id`.  Calls model deleteBill().
     *
     * @param array $input
     */
    protected function deleteBill(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $billId = trim((string) ($input['bill_id'] ?? ''));

        if ($billId === '') {
            $this->error('Bill id is required.', 422);
        }

        if (!$this->managerModel->deleteBill(
            $context['brand_id'],
            $context['branch_id'],
            $billId,
            $context['user_id']
        )) {
            $this->error('Unable to delete bill.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Bill deleted successfully.'
        );
    }

    /**
     * Permanently delete a table and refresh the dashboard.
     *
     * Expects POST with `table_id`.  Calls model deleteTable().
     *
     * @param array $input
     */
    protected function deleteTable(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $tableId = trim((string) ($input['table_id'] ?? ''));

        if ($tableId === '') {
            $this->error('Table id is required.', 422);
        }

        if (!$this->managerModel->deleteTable(
            $context['brand_id'],
            $context['branch_id'],
            $tableId,
            $context['user_id']
        )) {
            $this->error('Unable to delete table.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Table deleted successfully.'
        );
    }

    protected function saveWaiterAssignment(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $targetUserId = trim((string) ($input['user_id'] ?? $input['target_user_id'] ?? ''));

        if ($targetUserId === '') {
            $this->error('Waiter/User id is required.', 422);
        }

        $payload = [
            'full_name'            => trim((string) ($input['full_name'] ?? '')),
            'email'                => strtolower(trim((string) ($input['email'] ?? ''))),
            'phone'                => trim((string) ($input['phone'] ?? '')),
            'employee_code'        => trim((string) ($input['employee_code'] ?? '')),
            'profile_pic'          => trim((string) ($input['profile_pic'] ?? '')),
            'aadhar_no'            => trim((string) ($input['aadhar_no'] ?? '')),
            'role_at_branch'       => 'waiter',
            'is_active'            => $this->managerModel->statusToBoolString(trim((string) ($input['status'] ?? 'active'))),
            'assigned_table_codes' => $this->managerModel->normalizeCsvList($input['assigned_tables'] ?? $input['assigned_table_codes'] ?? []),
            'shift_start_time'     => trim((string) ($input['shift_start_time'] ?? '')),
            'shift_end_time'       => trim((string) ($input['shift_end_time'] ?? '')),
            'permissions'          => is_array($input['permissions'] ?? null) ? $input['permissions'] : [],
        ];

        if (!$this->managerModel->upsertWaiterAssignment(
            $context['brand_id'],
            $context['branch_id'],
            $targetUserId,
            $payload,
            $context['user_id']
        )) {
            $this->error('Unable to save waiter assignment.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Waiter assignment saved successfully.'
        );
    }

    protected function updateOrderStatus(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $orderId = trim((string) ($input['order_id'] ?? ''));

        if ($orderId === '') {
            $this->error('Order id is required.', 422);
        }

        $status = trim((string) ($input['status'] ?? 'new'));
        $note = trim((string) ($input['manager_note'] ?? $input['note'] ?? ''));

        if (!$this->managerModel->updateOrderStatus(
            $context['brand_id'],
            $context['branch_id'],
            $orderId,
            $status,
            $note,
            $context['user_id']
        )) {
            $this->error('Unable to update order status.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Order updated successfully.'
        );
    }

    protected function resolveRequest(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $requestId = trim((string) ($input['request_id'] ?? ''));

        if ($requestId === '') {
            $this->error('Request id is required.', 422);
        }

        $status = trim((string) ($input['status'] ?? 'resolved'));
        $note = trim((string) ($input['resolution_note'] ?? $input['note'] ?? ''));

        if (!$this->managerModel->resolveRequest(
            $context['brand_id'],
            $context['branch_id'],
            $requestId,
            $status,
            $note,
            $context['user_id']
        )) {
            $this->error('Unable to resolve request.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Request updated successfully.'
        );
    }

    protected function markNotificationRead(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $notificationId = trim((string) ($input['notification_id'] ?? ''));

        if ($notificationId === '') {
            $this->error('Notification id is required.', 422);
        }

        if (!$this->managerModel->markNotificationRead(
            $context['brand_id'],
            $context['branch_id'],
            $notificationId,
            $context['user_id']
        )) {
            $this->error('Unable to mark notification as read.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Notification marked as read.'
        );
    }

    protected function deleteNotification(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $notificationId = trim((string) ($input['notification_id'] ?? ''));

        if ($notificationId === '') {
            $this->error('Notification id is required.', 422);
        }

        if (!$this->managerModel->deleteNotification(
            $context['brand_id'],
            $context['branch_id'],
            $notificationId,
            $context['user_id']
        )) {
            $this->error('Unable to delete notification.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Notification deleted successfully.'
        );
    }

    protected function clearNotifications(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        if (!$this->managerModel->clearManagerNotifications(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        )) {
            $this->error('Unable to clear notifications.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Notifications cleared successfully.'
        );
    }

    protected function saveTable(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $tableId = trim((string) ($input['table_id'] ?? ''));
        $tableCode = trim((string) ($input['table_code'] ?? ''));
        $capacity = (int) ($input['capacity'] ?? 0);
        $zone = trim((string) ($input['zone'] ?? ''));
        $status = $this->normalizeTableStatus((string) ($input['status'] ?? 'available'));

        if ($tableCode === '') {
            $this->error('Table code is required.', 422);
        }

        $ok = $this->saveTableRecord(
            $context['brand_id'],
            $context['branch_id'],
            $tableId,
            [
                'table_code' => $tableCode,
                'capacity'   => $capacity > 0 ? $capacity : 1,
                'zone'       => $zone,
                'status'     => $status,
            ],
            $context['user_id']
        );

        if (!$ok) {
            $this->error('Unable to save table.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Table saved successfully.'
        );
    }

    protected function updateBillStatus(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $billId = trim((string) ($input['bill_id'] ?? ''));

        if ($billId === '') {
            $this->error('Bill id is required.', 422);
        }

        $paymentStatus = $this->normalizeBillPaymentStatus((string) ($input['payment_status'] ?? 'unpaid'));
        $billStatus = $this->normalizeBillLifecycleStatus((string) ($input['bill_status'] ?? 'new'));

        if ($billStatus === '') {
            $this->error('Invalid bill status.', 422);
        }

        if ($paymentStatus === 'paid') {
            $billStatus = 'closed';
        }

        if ($paymentStatus === 'in_processing' && $billStatus === 'closed') {
            $billStatus = 'approved';
        }

        if ($paymentStatus === 'unpaid' && $billStatus === 'closed') {
            $billStatus = 'approved';
        }

        $paymentMethod = trim((string) ($input['payment_method'] ?? 'cash'));
        $note = trim((string) ($input['billing_note'] ?? $input['note'] ?? ''));

        $ok = $this->saveBillStatusRecord(
            $context['brand_id'],
            $context['branch_id'],
            $billId,
            $billStatus,
            $paymentStatus,
            $paymentMethod,
            $note,
            $context['user_id']
        );

        if (!$ok) {
            $this->error('Unable to update bill status.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $paymentStatus === 'paid' ? 'Bill paid and closed successfully.' : 'Bill updated successfully.'
        );
    }

    private function saveBillStatusRecord(
        string $brandId,
        string $branchId,
        string $billId,
        string $explicitBillStatus,
        string $paymentStatus,
        string $paymentMethod,
        string $note,
        string $actorUserId
    ): bool {
        if (!$this->managerModel->tableExists('bills')) {
            return false;
        }

        $bill = $this->managerModel->one(
            "SELECT *
             FROM bills
             WHERE id = ?
               AND brand_id = ?
               AND branch_id = ?
             LIMIT 1",
            [$billId, $brandId, $branchId]
        );

        if (!is_array($bill)) {
            throw new RuntimeException('Bill not found.', 404);
        }

        $now = $this->now();
        $releaseAt = date('Y-m-d H:i:s', strtotime($now . ' +5 minutes'));
        $billStatus = $paymentStatus === 'paid'
            ? 'closed'
            : ($explicitBillStatus !== '' ? $explicitBillStatus : 'new');

        $updates = [
            'payment_status' => $paymentStatus,
            'bill_status'    => $billStatus,
        ];

        if ($this->managerModel->columnExists('bills', 'updated_at')) {
            $updates['updated_at'] = $now;
        }
        if ($this->managerModel->columnExists('bills', 'paid_at') && $paymentStatus === 'paid') {
            $updates['paid_at'] = $now;
        }
        if ($this->managerModel->columnExists('bills', 'auto_release_at')) {
            $updates['auto_release_at'] = $paymentStatus === 'paid' ? $releaseAt : null;
        }
        if ($this->managerModel->columnExists('bills', 'payment_method')) {
            $updates['payment_method'] = $paymentMethod;
        }
        if ($this->managerModel->columnExists('bills', 'note')) {
            $updates['note'] = $note;
        }
        if ($this->managerModel->columnExists('bills', 'manager_note')) {
            $updates['manager_note'] = $note;
        }

        if (!$this->dynamicUpdateById('bills', $billId, $updates)) {
            return false;
        }

        $orderId = (string) ($bill['order_id'] ?? '');
        $tableId = (string) ($bill['table_id'] ?? '');

        if ($orderId !== '' && $this->managerModel->tableExists('orders')) {
            $orderUpdates = [];

            if ($paymentStatus === 'paid' && $this->managerModel->columnExists('orders', 'status')) {
                $orderUpdates['status'] = 'paid';
            } elseif ($paymentStatus !== 'paid' && $this->managerModel->columnExists('orders', 'status')) {
                $orderUpdates['status'] = 'billing';
            }

            if ($this->managerModel->columnExists('orders', 'updated_at')) {
                $orderUpdates['updated_at'] = $now;
            }
            if ($paymentStatus === 'paid' && $this->managerModel->columnExists('orders', 'is_active')) {
                $orderUpdates['is_active'] = 0;
            }
            if ($paymentStatus === 'paid' && $this->managerModel->columnExists('orders', 'paid_at')) {
                $orderUpdates['paid_at'] = $now;
            }
            if ($paymentStatus === 'paid' && $this->managerModel->columnExists('orders', 'closed_at')) {
                $orderUpdates['closed_at'] = $now;
            }

            if ($orderUpdates !== []) {
                $this->dynamicUpdateById('orders', $orderId, $orderUpdates);
            }
        }

        $tableTable = $this->managerModel->tableExists('tables')
            ? 'tables'
            : ($this->managerModel->tableExists('restaurant_tables') ? 'restaurant_tables' : '');

        if ($tableTable !== '' && $tableId !== '') {
            $tableUpdates = [];

            if ($this->managerModel->columnExists($tableTable, 'status')) {
                $tableUpdates['status'] = $paymentStatus === 'paid' ? 'waiting' : 'occupied';
            }
            if ($this->managerModel->columnExists($tableTable, 'current_bill_id')) {
                $tableUpdates['current_bill_id'] = $paymentStatus === 'paid' ? null : $billId;
            }
            if ($this->managerModel->columnExists($tableTable, 'current_order_id') && $paymentStatus === 'paid') {
                $tableUpdates['current_order_id'] = null;
            }
            if ($this->managerModel->columnExists($tableTable, 'auto_available_at')) {
                $tableUpdates['auto_available_at'] = $paymentStatus === 'paid' ? $releaseAt : null;
            }
            if ($this->managerModel->columnExists($tableTable, 'manual_status_override') && $paymentStatus === 'paid') {
                $tableUpdates['manual_status_override'] = 0;
            }
            if ($this->managerModel->columnExists($tableTable, 'updated_at')) {
                $tableUpdates['updated_at'] = $now;
            }
            if ($this->managerModel->columnExists($tableTable, 'status_updated_at')) {
                $tableUpdates['status_updated_at'] = $now;
            }

            if ($tableUpdates !== []) {
                $this->dynamicUpdateById($tableTable, $tableId, $tableUpdates);
            }
        }

        if ($paymentStatus === 'paid') {
            $this->insertPaymentRowIfPossible(
                $brandId,
                $branchId,
                $billId,
                $orderId,
                $tableId,
                (float) ($bill['total_amount'] ?? 0),
                $paymentMethod,
                $actorUserId
            );
        }

        $this->insertNotificationIfPossible(
            $brandId,
            $branchId,
            $actorUserId,
            'manager',
            'bill_status_updated',
            'Bill Updated',
            $note !== '' ? $note : ('Bill #' . (string) ($bill['bill_number'] ?? $billId) . ' updated: bill ' . $billStatus . ', payment ' . $paymentStatus)
        );

        return true;
    }

    private function saveTableRecord(
        string $brandId,
        string $branchId,
        string $tableId,
        array $payload,
        string $actorUserId
    ): bool {
        $tableName = $this->managerModel->tableExists('tables')
            ? 'tables'
            : ($this->managerModel->tableExists('restaurant_tables') ? 'restaurant_tables' : '');

        if ($tableName === '') {
            return false;
        }

        $now = $this->now();
        $tableCode = trim((string) ($payload['table_code'] ?? ''));
        $capacity = (int) ($payload['capacity'] ?? 1);
        $status = $this->normalizeTableStatus((string) ($payload['status'] ?? 'available'));
        $zone = trim((string) ($payload['zone'] ?? ''));

        if ($tableCode === '') {
            return false;
        }

        if ($tableId !== '') {
            $existing = $this->managerModel->one(
                "SELECT id
                 FROM {$tableName}
                 WHERE id = ?
                   AND branch_id = ?
                 LIMIT 1",
                [$tableId, $branchId]
            );

            if (is_array($existing)) {
                $updates = [];

                if ($this->managerModel->columnExists($tableName, 'table_code')) {
                    $updates['table_code'] = $tableCode;
                }
                if ($this->managerModel->columnExists($tableName, 'table_number')) {
                    $updates['table_number'] = $this->extractTableNumber($tableCode);
                }
                if ($this->managerModel->columnExists($tableName, 'capacity')) {
                    $updates['capacity'] = $capacity;
                }
                if ($this->managerModel->columnExists($tableName, 'status')) {
                    $updates['status'] = $status;
                }
                if ($this->managerModel->columnExists($tableName, 'manual_status_override')) {
                    $updates['manual_status_override'] = 1;
                }
                if ($this->managerModel->columnExists($tableName, 'auto_available_at')) {
                    $updates['auto_available_at'] = null;
                }
                if ($zone !== '' && $this->managerModel->columnExists($tableName, 'zone')) {
                    $updates['zone'] = $zone;
                }
                if ($zone !== '' && $this->managerModel->columnExists($tableName, 'area_name')) {
                    $updates['area_name'] = $zone;
                }
                if ($zone !== '' && $this->managerModel->columnExists($tableName, 'label')) {
                    $updates['label'] = $zone;
                }
                if ($this->managerModel->columnExists($tableName, 'updated_at')) {
                    $updates['updated_at'] = $now;
                }
                if ($this->managerModel->columnExists($tableName, 'status_updated_at')) {
                    $updates['status_updated_at'] = $now;
                }

                if (!$this->dynamicUpdateById($tableName, $tableId, $updates)) {
                    return false;
                }

                $this->insertNotificationIfPossible(
                    $brandId,
                    $branchId,
                    $actorUserId,
                    'manager',
                    'table_updated',
                    'Table Updated',
                    'Table ' . $tableCode . ' updated.'
                );

                return true;
            }
        }

        $newId = $tableId !== '' ? $tableId : $this->uuid();

        $insert = [
            'id' => $newId,
        ];

        if ($this->managerModel->columnExists($tableName, 'brand_id')) {
            $insert['brand_id'] = $brandId;
        }
        if ($this->managerModel->columnExists($tableName, 'branch_id')) {
            $insert['branch_id'] = $branchId;
        }
        if ($this->managerModel->columnExists($tableName, 'table_code')) {
            $insert['table_code'] = $tableCode;
        }
        if ($this->managerModel->columnExists($tableName, 'table_number')) {
            $insert['table_number'] = $this->extractTableNumber($tableCode);
        }
        if ($this->managerModel->columnExists($tableName, 'capacity')) {
            $insert['capacity'] = $capacity;
        }
        if ($this->managerModel->columnExists($tableName, 'status')) {
            $insert['status'] = $status;
        }
        if ($this->managerModel->columnExists($tableName, 'manual_status_override')) {
            $insert['manual_status_override'] = 1;
        }
        if ($this->managerModel->columnExists($tableName, 'auto_available_at')) {
            $insert['auto_available_at'] = null;
        }
        if ($zone !== '' && $this->managerModel->columnExists($tableName, 'zone')) {
            $insert['zone'] = $zone;
        }
        if ($zone !== '' && $this->managerModel->columnExists($tableName, 'area_name')) {
            $insert['area_name'] = $zone;
        }
        if ($zone !== '' && $this->managerModel->columnExists($tableName, 'label')) {
            $insert['label'] = $zone;
        }
        if ($this->managerModel->columnExists($tableName, 'created_at')) {
            $insert['created_at'] = $now;
        }
        if ($this->managerModel->columnExists($tableName, 'updated_at')) {
            $insert['updated_at'] = $now;
        }
        if ($this->managerModel->columnExists($tableName, 'status_updated_at')) {
            $insert['status_updated_at'] = $now;
        }

        $ok = $this->dynamicInsert($tableName, $insert);

        if ($ok) {
            $this->insertNotificationIfPossible(
                $brandId,
                $branchId,
                $actorUserId,
                'manager',
                'table_created',
                'Table Created',
                'Table ' . $tableCode . ' created.'
            );
        }

        return $ok;
    }

    private function insertPaymentRowIfPossible(
        string $brandId,
        string $branchId,
        string $billId,
        string $orderId,
        string $tableId,
        float $amount,
        string $paymentMethod,
        string $actorUserId
    ): void {
        if (!$this->managerModel->tableExists('payments')) {
            return;
        }

        $now = $this->now();

        $insert = [
            'id' => $this->uuid(),
        ];

        if ($this->managerModel->columnExists('payments', 'brand_id')) {
            $insert['brand_id'] = $brandId;
        }
        if ($this->managerModel->columnExists('payments', 'branch_id')) {
            $insert['branch_id'] = $branchId;
        }
        if ($this->managerModel->columnExists('payments', 'bill_id')) {
            $insert['bill_id'] = $billId;
        }
        if ($orderId !== '' && $this->managerModel->columnExists('payments', 'order_id')) {
            $insert['order_id'] = $orderId;
        }
        if ($tableId !== '' && $this->managerModel->columnExists('payments', 'table_id')) {
            $insert['table_id'] = $tableId;
        }
        if ($this->managerModel->columnExists('payments', 'amount')) {
            $insert['amount'] = $amount;
        }
        if ($this->managerModel->columnExists('payments', 'payment_method')) {
            $insert['payment_method'] = $paymentMethod;
        }
        if ($this->managerModel->columnExists('payments', 'status')) {
            $insert['status'] = 'paid';
        }
        if ($this->managerModel->columnExists('payments', 'received_by_user_id')) {
            $insert['received_by_user_id'] = $actorUserId;
        }
        if ($this->managerModel->columnExists('payments', 'created_at')) {
            $insert['created_at'] = $now;
        }
        if ($this->managerModel->columnExists('payments', 'updated_at')) {
            $insert['updated_at'] = $now;
        }
        if ($this->managerModel->columnExists('payments', 'paid_at')) {
            $insert['paid_at'] = $now;
        }

        $this->dynamicInsert('payments', $insert);
    }

    private function insertNotificationIfPossible(
        string $brandId,
        string $branchId,
        string $userId,
        string $roleTarget,
        string $type,
        string $title,
        string $message,
        array $payload = []
    ): void {
        if (!$this->managerModel->tableExists('notifications')) {
            return;
        }

        $now = $this->now();
        $requestId = trim((string) ($payload['request_id'] ?? ''));

        /*
     * Exact semantic dedupe before insert.
     * This matches your DB unique key and prevents repeated inserts
     * even if the same action is triggered multiple times.
     */
        if ($requestId !== '') {
            $existing = $this->managerModel->one(
                "SELECT id
             FROM notifications
             WHERE brand_id = ?
               AND branch_id = ?
               AND COALESCE(user_id, '') = ?
               AND COALESCE(role_target, '') = ?
               AND COALESCE(type, '') = ?
               AND request_id = ?
             LIMIT 1",
                [
                    $brandId,
                    $branchId,
                    $userId !== '' ? $userId : '',
                    $roleTarget,
                    $type,
                    $requestId,
                ]
            );

            if ($existing) {
                return;
            }
        }

        $insert = [
            'id' => $this->uuid(),
        ];

        if ($this->managerModel->columnExists('notifications', 'brand_id')) {
            $insert['brand_id'] = $brandId;
        }
        if ($this->managerModel->columnExists('notifications', 'branch_id')) {
            $insert['branch_id'] = $branchId;
        }
        if ($this->managerModel->columnExists('notifications', 'user_id')) {
            $insert['user_id'] = $userId !== '' ? $userId : null;
        }
        if ($this->managerModel->columnExists('notifications', 'role_target')) {
            $insert['role_target'] = $roleTarget;
        }
        if ($this->managerModel->columnExists('notifications', 'type')) {
            $insert['type'] = $type;
        }
        if ($this->managerModel->columnExists('notifications', 'title')) {
            $insert['title'] = $title;
        }
        if ($this->managerModel->columnExists('notifications', 'message')) {
            $insert['message'] = $message;
        }

        /*
     * THIS WAS THE MISSING PART:
     * populate the real request_id column, not only payload_json.
     */
        if (
            $requestId !== '' &&
            $this->managerModel->columnExists('notifications', 'request_id')
        ) {
            $insert['request_id'] = $requestId;
        }

        if ($this->managerModel->columnExists('notifications', 'payload_json')) {
            $insert['payload_json'] = !empty($payload)
                ? json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : null;
        }
        if ($this->managerModel->columnExists('notifications', 'is_read')) {
            $insert['is_read'] = 0;
        }
        if ($this->managerModel->columnExists('notifications', 'created_at')) {
            $insert['created_at'] = $now;
        }

        try {
            $this->dynamicInsert('notifications', $insert);
        } catch (Throwable $e) {
            $error = strtolower($e->getMessage());
            if (
                strpos($error, 'duplicate') !== false ||
                strpos($error, 'unique') !== false ||
                strpos($error, '1062') !== false
            ) {
                return;
            }

            throw $e;
        }
    }

    private function dynamicInsert(string $table, array $data): bool
    {
        $filtered = [];

        foreach ($data as $column => $value) {
            if ($this->managerModel->columnExists($table, $column)) {
                $filtered[$column] = $value;
            }
        }

        if ($filtered === []) {
            return false;
        }

        $columns = array_keys($filtered);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (' . $placeholders . ')';

        return $this->managerModel->execute($sql, array_values($filtered));
    }

    private function dynamicUpdateById(string $table, string $id, array $data): bool
    {
        $filtered = [];

        foreach ($data as $column => $value) {
            if ($column !== 'id' && $this->managerModel->columnExists($table, $column)) {
                $filtered[$column] = $value;
            }
        }

        if ($filtered === []) {
            return false;
        }

        $parts = [];
        $params = [];

        foreach ($filtered as $column => $value) {
            $parts[] = $column . ' = ?';
            $params[] = $value;
        }

        $params[] = $id;
        $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $parts) . ' WHERE id = ?';

        return $this->managerModel->execute($sql, $params);
    }

    private function normalizeBillPaymentStatus(string $status): string
    {
        $status = strtolower(trim(str_replace([' ', '-'], '_', $status)));

        if (in_array($status, ['processing', 'pending', 'inprogress'], true)) {
            $status = 'in_processing';
        }

        $allowed = ['unpaid', 'paid', 'in_processing'];

        return in_array($status, $allowed, true) ? $status : 'unpaid';
    }

    private function normalizeBillLifecycleStatus(string $status): string
    {
        $status = strtolower(trim(str_replace([' ', '-'], '_', $status)));

        if ($status === 'generated' || $status === 'requested' || $status === 'pending') {
            $status = 'new';
        }

        $allowed = ['new', 'approved', 'cancelled', 'closed'];

        return in_array($status, $allowed, true) ? $status : '';
    }

    private function normalizeTableStatus(string $status): string
    {
        $status = strtolower(trim(str_replace([' ', '-'], '_', $status)));

        $allowed = ['available', 'occupied', 'reserved', 'waiting', 'maintenance'];

        return in_array($status, $allowed, true) ? $status : 'available';
    }

    private function deriveBillStatusFromPayment(string $paymentStatus): string
    {
        if ($paymentStatus === 'paid') {
            return 'closed';
        }

        if ($paymentStatus === 'failed') {
            return 'approved';
        }

        if ($paymentStatus === 'partial') {
            return 'approved';
        }

        if ($paymentStatus === 'pending') {
            return 'approved';
        }

        return 'approved';
    }


    private function extractTableNumber(string $tableCode): string
    {
        if (preg_match('/(\d+)/', $tableCode, $matches) === 1) {
            return (string) $matches[1];
        }

        return $tableCode;
    }

    private function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}
