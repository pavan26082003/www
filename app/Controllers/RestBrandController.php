<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\RestBrandModel;
use Throwable;

require_once APPPATH . 'Models/RestBrandModel.php';

final class RestBrandController extends RestBaseController
{
    private const VIEW_PATH = APPPATH . 'Views/restaurant-system/brand.php';

    protected string $userId = '00000000-0000-0000-0000-000000009001';

    /** @var RestBrandModel */
    protected $brandModel;

    public function __construct()
    {
        parent::__construct();
        $this->brandModel = new RestBrandModel();
    }

    public function index(): void
    {
        $input = $this->requestData();
        $context = $this->resolveContext($input);

        $pageData = $this->brandModel->getBrandPageData(
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

                case 'branch_detail':
                    $this->branchDetail($input);
                    return;

                case 'menu_detail':
                    $this->menuDetail($input);
                    return;

                case 'request_detail':
                    $this->requestDetail($input);
                    return;

                case 'save_brand_profile':
                case 'update_brand_profile':
                    $this->saveBrandProfile($input);
                    return;

                case 'save_brand_settings':
                    $this->saveBrandSettings($input);
                    return;

                case 'save_branch':
                case 'create_branch':
                    $this->saveBranch($input);
                    return;

                case 'toggle_branch_status':
                    $this->toggleBranchStatus($input);
                    return;

                case 'delete_branch':
                    $this->deleteBranch($input);
                    return;

                case 'save_staff':
                case 'create_staff':
                case 'save_user':
                    $this->saveStaff($input);
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

                case 'save_manager_assignment':
                    $this->saveManagerAssignment($input);
                    return;

                case 'save_menu_item':
                case 'create_menu_item':
                    $this->saveMenuItem($input);
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

                case 'save_table':
                    $this->saveTable($input);
                    return;

                case 'update_order_status':
                    $this->updateOrderStatus($input);
                    return;

                case 'resolve_request':
                    $this->resolveRequest($input);
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
        return 'rest_brand_context';
    }

    protected function resolveContext(array $input = array()): array
    {
        $sessionContext = $_SESSION[$this->contextSessionKey()] ?? array();

        $brandId = (string)($input['brand_id'] ?? $_GET['brand_id'] ?? $sessionContext['brand_id'] ?? $this->brandId);
        $branchId = (string)($input['branch_id'] ?? $_GET['branch_id'] ?? $sessionContext['branch_id'] ?? $this->branchId);
        $userId = (string)($input['user_id'] ?? $_GET['user_id'] ?? $sessionContext['user_id'] ?? $this->userId);

        $brandId = $this->brandModel->normalizeEntityIdOrDefault($brandId, $this->brandId);
        $branchId = $this->brandModel->normalizeEntityIdOrDefault($branchId, $this->branchId);
        $userId = $this->brandModel->normalizeEntityIdOrDefault($userId, $this->userId);

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
        $snapshot = $this->brandModel->buildDashboardSnapshot($brandId, $branchId, $userId);
        if ($snapshot === null) {
            $this->error('Unable to refresh brand dashboard.', 500);
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
        $snapshot = $this->brandModel->buildDashboardSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        if ($snapshot === null) {
            $this->error('Unable to load brand dashboard.', 404);
        }

        $this->json(array(
            'ok'       => true,
            'snapshot' => $snapshot,
            'graph'    => $snapshot['revenue_chart'] ?? array(),
            'message'  => 'Brand dashboard loaded.',
        ));
    }

    protected function orderDetail(array $input): void
    {
        $context = $this->resolveContext($input);
        $orderId = trim((string)($_GET['order_id'] ?? $input['order_id'] ?? ''));

        if ($orderId === '') {
            $this->error('Order id is required.', 422);
        }

        $detail = $this->brandModel->getOrderDetail($context['brand_id'], $orderId);
        if ($detail === null) {
            $this->error('Order not found.', 404);
        }

        $this->json(array(
            'ok'     => true,
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

        $detail = $this->brandModel->getBillDetail($context['brand_id'], $billId);
        if ($detail === null) {
            $this->error('Bill not found.', 404);
        }

        $this->json(array(
            'ok'     => true,
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

        $detail = $this->brandModel->getStaffDetail($context['brand_id'], $targetUserId);
        if ($detail === null) {
            $this->error('Staff member not found.', 404);
        }

        $this->json(array(
            'ok'     => true,
            'detail' => $detail,
        ));
    }

    protected function branchDetail(array $input): void
    {
        $context = $this->resolveContext($input);
        $targetBranchId = trim((string)($_GET['target_branch_id'] ?? $input['target_branch_id'] ?? $input['branch_id'] ?? ''));

        if ($targetBranchId === '') {
            $this->error('Branch id is required.', 422);
        }

        $detail = $this->brandModel->getBranchDetail($context['brand_id'], $targetBranchId);
        if ($detail === null) {
            $this->error('Branch not found.', 404);
        }

        $this->json(array(
            'ok'     => true,
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

        $detail = $this->brandModel->getMenuItemDetail($context['brand_id'], $menuId, (string)($input['branch_id'] ?? $_GET['branch_id'] ?? $context['branch_id']));
        if ($detail === null) {
            $this->error('Menu item not found.', 404);
        }

        $this->json(array(
            'ok'     => true,
            'detail' => $detail,
        ));
    }

    protected function requestDetail(array $input): void
    {
        $context = $this->resolveContext($input);
        $requestId = trim((string)($_GET['request_id'] ?? $input['request_id'] ?? ''));

        if ($requestId === '') {
            $this->error('Request id is required.', 422);
        }

        $detail = $this->brandModel->getRequestDetail($context['brand_id'], $requestId);
        if ($detail === null) {
            $this->error('Request not found.', 404);
        }

        $this->json(array(
            'ok'     => true,
            'detail' => $detail,
        ));
    }

    protected function saveBrandProfile(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $payload = array(
            'brand_name' => trim((string)($input['brand_name'] ?? $input['name'] ?? '')),
            'legal_name' => trim((string)($input['legal_name'] ?? '')),
            'mail_id'    => strtolower(trim((string)($input['mail_id'] ?? $input['email'] ?? ''))),
            'mobile'     => trim((string)($input['mobile'] ?? $input['phone'] ?? '')),
            'currency'   => strtoupper(trim((string)($input['currency'] ?? 'INR'))),
            'timezone'   => trim((string)($input['timezone'] ?? 'Asia/Kolkata')),
            'logo'       => trim((string)($input['logo'] ?? '')),
            'favicon'    => trim((string)($input['favicon'] ?? '')),
            'status'     => trim((string)($input['status'] ?? 'active')),
        );

        if ($payload['brand_name'] === '') {
            $this->error('Brand name is required.', 422);
        }

        if ($payload['mail_id'] !== '' && !filter_var($payload['mail_id'], FILTER_VALIDATE_EMAIL)) {
            $this->error('Please enter a valid brand email address.', 422);
        }

        if (!$this->brandModel->saveBrandProfile($context['brand_id'], $payload, $context['user_id'])) {
            $this->error('Unable to save brand profile.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Brand profile updated successfully.');
    }

    protected function saveBrandSettings(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $payload = array(
            'subscription_plan' => trim((string)($input['subscription_plan'] ?? '')),
            'status'            => trim((string)($input['status'] ?? 'active')),
            'timezone'          => trim((string)($input['timezone'] ?? 'Asia/Kolkata')),
            'currency'          => strtoupper(trim((string)($input['currency'] ?? 'INR'))),
        );

        if (!$this->brandModel->saveBrandSettings($context['brand_id'], $payload, $context['user_id'])) {
            $this->error('Unable to save brand settings.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Brand settings updated successfully.');
    }

    protected function saveBranch(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $payload = array(
            'branch_id'   => trim((string)($input['branch_id'] ?? $input['id'] ?? '')),
            'name'        => trim((string)($input['name'] ?? '')),
            'code'        => trim((string)($input['code'] ?? '')),
            'address'     => trim((string)($input['address'] ?? '')),
            'city'        => trim((string)($input['city'] ?? '')),
            'state'       => trim((string)($input['state'] ?? '')),
            'pincode'     => trim((string)($input['pincode'] ?? '')),
            'phone'       => trim((string)($input['phone'] ?? '')),
            'email'       => strtolower(trim((string)($input['email'] ?? ''))),
            'status'      => trim((string)($input['status'] ?? 'active')),
            'opens_at'    => trim((string)($input['opens_at'] ?? '')),
            'closes_at'   => trim((string)($input['closes_at'] ?? '')),
            'tax_number'  => trim((string)($input['tax_number'] ?? '')),
        );

        if ($payload['name'] === '') {
            $this->error('Branch name is required.', 422);
        }

        if ($payload['email'] !== '' && !filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error('Please enter a valid branch email address.', 422);
        }

        if (!$this->brandModel->saveBranch($context['brand_id'], $payload, $context['user_id'])) {
            $this->error('Unable to save branch.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Branch saved successfully.');
    }

    protected function toggleBranchStatus(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $targetBranchId = trim((string)($input['target_branch_id'] ?? $input['branch_id'] ?? ''));

        if ($targetBranchId === '') {
            $this->error('Branch id is required.', 422);
        }

        if (!$this->brandModel->toggleBranchStatus($context['brand_id'], $targetBranchId, $context['user_id'])) {
            $this->error('Unable to change branch status.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Branch status updated successfully.');
    }

    protected function deleteBranch(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $targetBranchId = trim((string)($input['target_branch_id'] ?? $input['branch_id'] ?? ''));

        if ($targetBranchId === '') {
            $this->error('Branch id is required.', 422);
        }

        if (!$this->brandModel->deleteBranch($context['brand_id'], $targetBranchId, $context['user_id'])) {
            $this->error('Unable to delete branch.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Branch removed successfully.');
    }

    protected function saveStaff(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $payload = array(
            'target_user_id'       => trim((string)($input['target_user_id'] ?? $input['user_id'] ?? '')),
            'branch_id'            => trim((string)($input['branch_id'] ?? $context['branch_id'])),
            'full_name'            => trim((string)($input['full_name'] ?? '')),
            'email'                => strtolower(trim((string)($input['email'] ?? ''))),
            'phone'                => trim((string)($input['phone'] ?? '')),
            'employee_code'        => trim((string)($input['employee_code'] ?? '')),
            'role_at_branch'       => trim((string)($input['role_at_branch'] ?? $input['role'] ?? 'waiter')),
            'is_active'            => trim((string)($input['is_active'] ?? '1')),
            'assigned_table_codes' => $this->brandModel->normalizeCsvList($input['assigned_table_codes'] ?? array()),
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

        if (!$this->brandModel->saveStaff($context['brand_id'], $payload, $context['user_id'])) {
            $this->error('Unable to save staff.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Staff saved successfully.');
    }

    protected function updateStaff(array $input): void
    {
        $this->saveStaff($input);
    }

    protected function toggleStaffStatus(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $targetUserId = trim((string)($input['target_user_id'] ?? $input['user_id'] ?? ''));

        if ($targetUserId === '') {
            $this->error('Target user id is required.', 422);
        }

        if (!$this->brandModel->toggleStaffStatus($context['brand_id'], $targetUserId, $context['user_id'])) {
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
        $targetUserId = trim((string)($input['target_user_id'] ?? $input['user_id'] ?? ''));

        if ($targetUserId === '') {
            $this->error('Target user id is required.', 422);
        }

        if (!$this->brandModel->deleteStaff($context['brand_id'], $targetUserId, $context['user_id'])) {
            $this->error('Unable to delete staff.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Staff removed successfully.');
    }

    protected function saveManagerAssignment(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $targetUserId = trim((string)($input['user_id'] ?? ''));
        $targetBranchId = trim((string)($input['branch_id'] ?? ''));

        if ($targetUserId === '' || $targetBranchId === '') {
            $this->error('User id and branch id are required.', 422);
        }

        if (!$this->brandModel->saveManagerAssignment($context['brand_id'], $targetBranchId, $targetUserId, $context['user_id'])) {
            $this->error('Unable to save manager assignment.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Manager assignment saved successfully.');
    }

    protected function saveMenuItem(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $payload = array(
            'menu_id'                    => trim((string)($input['menu_id'] ?? $input['id'] ?? '')),
            'branch_id'                  => trim((string)($input['branch_id'] ?? $context['branch_id'])),
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

        if (!$this->brandModel->saveMenuItem($context['brand_id'], $payload, $context['user_id'])) {
            $this->error('Unable to save menu item.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Menu item saved successfully.');
    }

    protected function updateMenuItem(array $input): void
    {
        $this->saveMenuItem($input);
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

        $branchId = trim((string)($input['branch_id'] ?? $context['branch_id']));

        if (!$this->brandModel->toggleMenuItemStatus($context['brand_id'], $branchId, $menuId, $context['user_id'])) {
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

        $branchId = trim((string)($input['branch_id'] ?? $context['branch_id']));

        if (!$this->brandModel->deleteMenuItem($context['brand_id'], $branchId, $menuId, $context['user_id'])) {
            $this->error('Unable to delete menu item.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Menu item removed successfully.');
    }

    protected function saveTable(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $payload = array(
            'branch_id'   => trim((string)($input['branch_id'] ?? $context['branch_id'])),
            'table_id'    => trim((string)($input['table_id'] ?? $input['id'] ?? '')),
            'table_code'  => trim((string)($input['table_code'] ?? '')),
            'capacity'    => trim((string)($input['capacity'] ?? '0')),
            'zone'        => trim((string)($input['zone'] ?? '')),
            'label'       => trim((string)($input['label'] ?? '')),
            'status'      => trim((string)($input['status'] ?? 'available')),
        );

        if ($payload['table_code'] === '') {
            $this->error('Table code is required.', 422);
        }

        if (!$this->brandModel->saveTable($context['brand_id'], $payload, $context['user_id'])) {
            $this->error('Unable to save table.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Table saved successfully.');
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

        if (!$this->brandModel->updateOrderStatus($context['brand_id'], $orderId, $status, trim((string)($input['note'] ?? '')), $context['user_id'])) {
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
        $note = trim((string)($input['resolution_note'] ?? $input['note'] ?? ''));

        if (!$this->brandModel->resolveRequest($context['brand_id'], $requestId, $status, $note, $context['user_id'])) {
            $this->error('Unable to resolve request.', 500);
        }

        $this->refreshPayload($context['brand_id'], $context['branch_id'], $context['user_id'], 'Request updated successfully.');
    }
}