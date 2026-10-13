<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\RestManagerModel;
use Throwable;

require_once APPPATH . 'Models/RestManagerModel.php';

final class RestManagerController extends RestBaseController
{
    private const VIEW_PATH = APPPATH . 'Views/restaurant-system/manager.php';

    protected string $userId = '00000000-0000-0000-0000-000000001003';

    /** @var RestManagerModel */
    protected $managerModel;

    public function __construct()
    {
        parent::__construct();
        $this->managerModel = new RestManagerModel();
    }

    public function index(): void
    {
        $input = $this->requestData();
        $context = $this->resolveContext($input);

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
            $action = trim((string)($_GET['action'] ?? $input['action'] ?? ''));

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

                case 'save_waiter_assignment':
                    $this->saveWaiterAssignment($input);
                    return;

                case 'update_order_status':
                    $this->updateOrderStatus($input);
                    return;

                case 'resolve_request':
                    $this->resolveRequest($input);
                    return;

                case 'save_table':
                    $this->saveTable($input);
                    return;

                default:
                    $this->error('Unknown action.', 404);
                    return;
            }
        } catch (Throwable $e) {
            $this->error($e->getMessage(), 500);
        }
    }

    protected function success(string $message, array $data = array(), int $status = 200): void
    {
        $this->json(array(
            'ok'      => true,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }

    protected function error(string $message, int $status = 400, array $extra = array()): void
    {
        $this->json(array_merge(array(
            'ok'      => false,
            'message' => $message,
        ), $extra), $status);
    }

    protected function requestMethod(): string
    {
        return strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    }

    protected function contextSessionKey(): string
    {
        return 'rest_manager_context';
    }

    protected function resolveContext(array $input = array()): array
    {
        $sessionContext = $_SESSION[$this->contextSessionKey()] ?? array();

        $brandId = (string)($input['brand_id'] ?? $_GET['brand_id'] ?? $sessionContext['brand_id'] ?? $this->brandId);
        $branchId = (string)($input['branch_id'] ?? $_GET['branch_id'] ?? $sessionContext['branch_id'] ?? $this->branchId);
        $userId = (string)($input['user_id'] ?? $_GET['user_id'] ?? $sessionContext['user_id'] ?? $this->userId);

        $brandId = $this->managerModel->normalizeEntityIdOrDefault($brandId, $this->brandId);
        $branchId = $this->managerModel->normalizeEntityIdOrDefault($branchId, $this->branchId);
        $userId = $this->managerModel->normalizeEntityIdOrDefault($userId, $this->userId);

        $context = array(
            'brand_id'  => $brandId,
            'branch_id' => $branchId,
            'user_id'   => $userId,
        );

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

        $this->json(array(
            'ok'       => true,
            'message'  => $message,
            'snapshot' => $snapshot,
            'graph'    => $snapshot['revenue_chart'] ?? array(),
        ));
    }

    protected function dashboardSnapshot(array $input): void
    {
        if ($this->requestMethod() !== 'GET' && $this->requestMethod() !== 'POST') {
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

        $this->json(array(
            'ok'       => true,
            'snapshot' => $snapshot,
            'graph'    => $snapshot['revenue_chart'] ?? array(),
            'message'  => 'Dashboard loaded.',
        ));
    }

    protected function orderDetail(array $input): void
    {
        $context = $this->resolveContext($input);
        $orderId = trim((string)($_GET['order_id'] ?? $input['order_id'] ?? ''));

        if ($orderId === '') {
            $this->error('Order id is required.', 422);
        }

        $detail = $this->managerModel->getOrderDetail($context['brand_id'], $context['branch_id'], $orderId);
        if ($detail === null) {
            $this->error('Order not found.', 404);
        }

        $this->json(array(
            'ok' => true,
            'detail' => $detail,
        ));
    }

    protected function billDetail(array $input): void
    {
        $context = $this->resolveContext($input);
        $billId = trim((string)($_GET['bill_id'] ?? $input['bill_id'] ?? ''));

        if ($billId === '') {
            $this->error('Bill id is required.', 422);
        }

        $detail = $this->managerModel->getBillDetail($context['brand_id'], $context['branch_id'], $billId);
        if ($detail === null) {
            $this->error('Bill not found.', 404);
        }

        $this->json(array(
            'ok' => true,
            'detail' => $detail,
        ));
    }

    protected function staffDetail(array $input): void
    {
        $context = $this->resolveContext($input);
        $targetUserId = trim((string)($_GET['user_id'] ?? $input['user_id'] ?? ''));

        if ($targetUserId === '') {
            $this->error('User id is required.', 422);
        }

        $detail = $this->managerModel->getStaffDetail($context['brand_id'], $context['branch_id'], $targetUserId);
        if ($detail === null) {
            $this->error('Staff member not found.', 404);
        }

        $this->json(array(
            'ok' => true,
            'detail' => $detail,
        ));
    }

    protected function menuDetail(array $input): void
    {
        $context = $this->resolveContext($input);
        $menuId = trim((string)($_GET['menu_id'] ?? $input['menu_id'] ?? ''));

        if ($menuId === '') {
            $this->error('Menu id is required.', 422);
        }

        $detail = $this->managerModel->getMenuItemDetail($context['brand_id'], $context['branch_id'], $menuId);
        if ($detail === null) {
            $this->error('Menu item not found.', 404);
        }

        $this->json(array(
            'ok' => true,
            'detail' => $detail,
        ));
    }

    protected function createStaff(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $payload = array(
            'full_name'            => trim((string)($input['full_name'] ?? '')),
            'email'                => strtolower(trim((string)($input['email'] ?? ''))),
            'phone'                => trim((string)($input['phone'] ?? '')),
            'employee_code'        => trim((string)($input['employee_code'] ?? '')),
            'role_at_branch'       => trim((string)($input['role_at_branch'] ?? $input['role'] ?? 'waiter')),
            'is_active'            => trim((string)($input['is_active'] ?? '1')),
            'assigned_table_codes' => $this->managerModel->normalizeCsvList($input['assigned_table_codes'] ?? array()),
            'shift_start_time'     => trim((string)($input['shift_start_time'] ?? '')),
            'shift_end_time'       => trim((string)($input['shift_end_time'] ?? '')),
            'permissions'          => is_array($input['permissions'] ?? null) ? $input['permissions'] : array(),
        );

        if ($payload['full_name'] === '' || $payload['email'] === '') {
            $this->error('Full name and email are required.', 422);
        }

        if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error('Please enter a valid email address.', 422);
        }

        if (!$this->managerModel->createStaff($context['brand_id'], $context['branch_id'], $payload, $context['user_id'])) {
            $this->error('Unable to create staff.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Staff created successfully.');
    }

    protected function updateStaff(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $targetUserId = trim((string)($input['target_user_id'] ?? ''));

        if ($targetUserId === '') {
            $this->error('Target user id is required.', 422);
        }

        $payload = array(
            'full_name'            => trim((string)($input['full_name'] ?? '')),
            'email'                => strtolower(trim((string)($input['email'] ?? ''))),
            'phone'                => trim((string)($input['phone'] ?? '')),
            'employee_code'        => trim((string)($input['employee_code'] ?? '')),
            'role_at_branch'       => trim((string)($input['role_at_branch'] ?? $input['role'] ?? 'waiter')),
            'is_active'            => trim((string)($input['is_active'] ?? '1')),
            'assigned_table_codes' => $this->managerModel->normalizeCsvList($input['assigned_table_codes'] ?? array()),
            'shift_start_time'     => trim((string)($input['shift_start_time'] ?? '')),
            'shift_end_time'       => trim((string)($input['shift_end_time'] ?? '')),
            'permissions'          => is_array($input['permissions'] ?? null) ? $input['permissions'] : array(),
        );

        if ($payload['full_name'] === '' || $payload['email'] === '') {
            $this->error('Full name and email are required.', 422);
        }

        if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error('Please enter a valid email address.', 422);
        }

        if (!$this->managerModel->updateStaff($context['brand_id'], $context['branch_id'], $targetUserId, $payload, $context['user_id'])) {
            $this->error('Unable to update staff.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Staff updated successfully.');
    }

    protected function toggleStaffStatus(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $targetUserId = trim((string)($input['target_user_id'] ?? ''));

        if ($targetUserId === '') {
            $this->error('Target user id is required.', 422);
        }

        if (!$this->managerModel->toggleStaffStatus($context['brand_id'], $context['branch_id'], $targetUserId, $context['user_id'])) {
            $this->error('Unable to change staff status.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Staff status updated successfully.');
    }

    protected function deleteStaff(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $targetUserId = trim((string)($input['target_user_id'] ?? ''));

        if ($targetUserId === '') {
            $this->error('Target user id is required.', 422);
        }

        if (!$this->managerModel->deleteStaff($context['brand_id'], $context['branch_id'], $targetUserId, $context['user_id'])) {
            $this->error('Unable to delete staff.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Staff removed successfully.');
    }

    protected function createMenuItem(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $payload = array(
            'category_id'                => trim((string)($input['category_id'] ?? '')),
            'category_name'              => trim((string)($input['category_name'] ?? $input['category'] ?? '')),
            'default_kitchen_station_id' => trim((string)($input['default_kitchen_station_id'] ?? '')),
            'name'                       => trim((string)($input['name'] ?? $input['item_name'] ?? '')),
            'sku'                        => trim((string)($input['sku'] ?? '')),
            'description'                => trim((string)($input['description'] ?? '')),
            'image_url'                  => trim((string)($input['image_url'] ?? '')),
            'video_url'                  => trim((string)($input['video_url'] ?? '')),
            'video_type'                 => trim((string)($input['video_type'] ?? '')),
            'item_type'                  => trim((string)($input['item_type'] ?? 'food')),
            'base_price'                 => trim((string)($input['base_price'] ?? $input['price'] ?? '0')),
            'selling_price'              => trim((string)($input['selling_price'] ?? $input['price'] ?? '0')),
            'tax_rate'                   => trim((string)($input['tax_rate'] ?? '0')),
            'preparation_time_mins'      => trim((string)($input['preparation_time_mins'] ?? '0')),
            'is_veg'                     => trim((string)($input['is_veg'] ?? '0')),
            'is_vegan'                   => trim((string)($input['is_vegan'] ?? '0')),
            'is_spicy'                   => trim((string)($input['is_spicy'] ?? '0')),
            'is_available'               => trim((string)($input['is_available'] ?? '1')),
            'stock_status'               => trim((string)($input['stock_status'] ?? 'in_stock')),
            'available_from'             => trim((string)($input['available_from'] ?? '')),
            'available_to'               => trim((string)($input['available_to'] ?? '')),
        );

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

        if (!$this->managerModel->createMenuItem($context['brand_id'], $context['branch_id'], $payload, $context['user_id'])) {
            $this->error('Unable to create menu item.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Menu item created successfully.');
    }

    protected function updateMenuItem(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $menuId = trim((string)($input['menu_id'] ?? ''));

        if ($menuId === '') {
            $this->error('Menu id is required.', 422);
        }

        $payload = array(
            'category_id'                => trim((string)($input['category_id'] ?? '')),
            'category_name'              => trim((string)($input['category_name'] ?? $input['category'] ?? '')),
            'default_kitchen_station_id' => trim((string)($input['default_kitchen_station_id'] ?? '')),
            'name'                       => trim((string)($input['name'] ?? $input['item_name'] ?? '')),
            'sku'                        => trim((string)($input['sku'] ?? '')),
            'description'                => trim((string)($input['description'] ?? '')),
            'image_url'                  => trim((string)($input['image_url'] ?? '')),
            'video_url'                  => trim((string)($input['video_url'] ?? '')),
            'video_type'                 => trim((string)($input['video_type'] ?? '')),
            'item_type'                  => trim((string)($input['item_type'] ?? 'food')),
            'base_price'                 => trim((string)($input['base_price'] ?? $input['price'] ?? '0')),
            'selling_price'              => trim((string)($input['selling_price'] ?? $input['price'] ?? '0')),
            'tax_rate'                   => trim((string)($input['tax_rate'] ?? '0')),
            'preparation_time_mins'      => trim((string)($input['preparation_time_mins'] ?? '0')),
            'is_veg'                     => trim((string)($input['is_veg'] ?? '0')),
            'is_vegan'                   => trim((string)($input['is_vegan'] ?? '0')),
            'is_spicy'                   => trim((string)($input['is_spicy'] ?? '0')),
            'is_available'               => trim((string)($input['is_available'] ?? '1')),
            'stock_status'               => trim((string)($input['stock_status'] ?? 'in_stock')),
            'available_from'             => trim((string)($input['available_from'] ?? '')),
            'available_to'               => trim((string)($input['available_to'] ?? '')),
        );

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

        if (!$this->managerModel->updateMenuItem($context['brand_id'], $context['branch_id'], $menuId, $payload, $context['user_id'])) {
            $this->error('Unable to update menu item.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Menu item updated successfully.');
    }

    protected function toggleMenuItemStatus(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $menuId = trim((string)($input['menu_id'] ?? ''));

        if ($menuId === '') {
            $this->error('Menu id is required.', 422);
        }

        if (!$this->managerModel->toggleMenuItemStatus($context['brand_id'], $context['branch_id'], $menuId, $context['user_id'])) {
            $this->error('Unable to change menu item status.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Menu item status updated successfully.');
    }

    protected function deleteMenuItem(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $menuId = trim((string)($input['menu_id'] ?? ''));

        if ($menuId === '') {
            $this->error('Menu id is required.', 422);
        }

        if (!$this->managerModel->deleteMenuItem($context['brand_id'], $context['branch_id'], $menuId, $context['user_id'])) {
            $this->error('Unable to delete menu item.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Menu item removed successfully.');
    }

    protected function saveWaiterAssignment(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $targetUserId = trim((string)($input['user_id'] ?? ''));

        if ($targetUserId === '') {
            $this->error('Waiter/User id is required.', 422);
        }

        $payload = array(
            'full_name'            => trim((string)($input['full_name'] ?? '')),
            'email'                => strtolower(trim((string)($input['email'] ?? ''))),
            'phone'                => trim((string)($input['phone'] ?? '')),
            'employee_code'        => trim((string)($input['employee_code'] ?? '')),
            'role_at_branch'       => 'waiter',
            'is_active'            => $this->managerModel->statusToBoolString(trim((string)($input['status'] ?? 'active'))),
            'assigned_table_codes' => $this->managerModel->normalizeCsvList($input['assigned_tables'] ?? array()),
            'shift_start_time'     => trim((string)($input['shift_start'] ?? '')),
            'shift_end_time'       => trim((string)($input['shift_end'] ?? '')),
            'permissions'          => array(),
        );

        if (!$this->managerModel->upsertWaiterAssignment($context['brand_id'], $context['branch_id'], $targetUserId, $payload, $context['user_id'])) {
            $this->error('Unable to save waiter assignment.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Waiter assignment saved successfully.');
    }

    protected function updateOrderStatus(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $orderId = trim((string)($input['order_id'] ?? ''));
        $status = trim((string)($input['status'] ?? ''));

        if ($orderId === '' || $status === '') {
            $this->error('Order id and status are required.', 422);
        }

        if (!$this->managerModel->updateOrderStatus($context['brand_id'], $context['branch_id'], $orderId, $status, trim((string)($input['note'] ?? '')), $context['user_id'])) {
            $this->error('Unable to update order status.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Order updated successfully.');
    }

    protected function resolveRequest(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $requestId = trim((string)($input['request_id'] ?? ''));

        if ($requestId === '') {
            $this->error('Request id is required.', 422);
        }

        $status = trim((string)($input['status'] ?? 'resolved'));
        $note = trim((string)($input['resolution_note'] ?? ''));

        if (!$this->managerModel->resolveRequest($context['brand_id'], $context['branch_id'], $requestId, $status, $note, $context['user_id'])) {
            $this->error('Unable to resolve request.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Request updated successfully.');
    }

    protected function saveTable(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $payload = array(
            'table_code' => trim((string)($input['table_code'] ?? '')),
            'capacity'   => trim((string)($input['capacity'] ?? '0')),
            'zone'       => trim((string)($input['zone'] ?? '')),
            'status'     => trim((string)($input['status'] ?? 'available')),
        );

        if ($payload['table_code'] === '') {
            $this->error('Table code is required.', 422);
        }

        if (!$this->managerModel->saveTable($context['brand_id'], $context['branch_id'], $payload, $context['user_id'])) {
            $this->error('Unable to save table.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Table saved successfully.');
    }
}