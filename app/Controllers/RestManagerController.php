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

                case 'generate_staff_employee_code':
                    $this->generateStaffEmployeeCode($input);
                    return;

                case 'create_staff':
                case 'save_user':
                    $this->createStaff($input);
                    return;

                case 'update_staff':
                    $this->updateStaff($input);
                    return;

                case 'update_manager_profile':
                    $this->updateManagerProfile($input);
                    return;

                case 'change_password':
                    $this->changePassword($input);
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

                case 'offer_banner_detail':
                    $this->offerBannerDetail($input);
                    return;

                case 'create_offer_banner':
                    $this->createOfferBanner($input);
                    return;

                case 'update_offer_banner':
                    $this->updateOfferBanner($input);
                    return;

                case 'delete_offer_banner':
                    $this->deleteOfferBanner($input);
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

                case 'get_manager_settings':
                    $this->getManagerSettings($input);
                    return;

                case 'save_manager_settings':
                    $this->saveManagerSettings($input);
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


    protected function getManagerSettings(array $input): void
    {
        $context = $this->resolveContext($input);

        $settings = $this->managerModel->getUserSettings(
            $context['user_id'],
            'manager'
        );

        $this->json([
            'ok' => true,
            'settings' => $settings,
        ]);
    }

    protected function saveManagerSettings(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $payload = [
            'theme' => (string)($input['theme'] ?? 'light'),
            'lite_notification_sound' => (string)($input['notification_sound'] ?? $input['lite_notification_sound'] ?? ''),
            'sound_alerts_enabled' => (string)($input['sound_alerts_enabled'] ?? $input['sound'] ?? '1'),
        ];

        $settings = $this->managerModel->saveUserSettings(
            $context['user_id'],
            'manager',
            $payload
        );

        $this->json([
            'ok' => true,
            'message' => 'Settings saved successfully.',
            'settings' => $settings,
        ]);
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
        // JS opens staff detail with `user_id` as selected staff id; do not let it overwrite manager context.
        $targetUserId = trim((string) (
            $_GET['target_user_id']
            ?? $input['target_user_id']
            ?? $_GET['staff_id']
            ?? $input['staff_id']
            ?? $_GET['user_id']
            ?? $input['user_id']
            ?? ''
        ));

        $contextInput = $input;
        unset($contextInput['user_id'], $_GET['user_id']);
        $context = $this->resolveContext($contextInput);

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


    protected function offerBannerDetail(array $input): void
    {
        $context = $this->resolveContext($input);
        $bannerId = trim((string) ($_GET['banner_id'] ?? $input['banner_id'] ?? ''));

        if ($bannerId === '') {
            $this->error('Offer banner id is required.', 422);
        }

        $detail = $this->managerModel->getOfferBannerDetail(
            $context['brand_id'],
            $context['branch_id'],
            $bannerId
        );

        if ($detail === null) {
            $this->error('Offer banner not found.', 404);
        }

        $this->json([
            'ok'     => true,
            'detail' => $detail,
        ]);
    }

    protected function uploadedOfferImageExtension(array $file, int $originalSize): string
    {
        if ((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->error('Offer image upload failed.', 422);
        }

        $uploadedSize = (int) ($file['size'] ?? 0);
        $sourceSize = $originalSize > 0 ? $originalSize : $uploadedSize;

        if ($sourceSize < 1 * 1024 * 1024) {
            $this->error('Offer source image must be at least 1MB.', 422);
        }

        if ($sourceSize > 25 * 1024 * 1024 || $uploadedSize > 25 * 1024 * 1024) {
            $this->error('Offer image must be less than 25MB.', 422);
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');
        $mime = is_file($tmpPath) ? (string) mime_content_type($tmpPath) : '';

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];

        if (!isset($allowed[$mime])) {
            $this->error('Only JPG, PNG, WEBP, and GIF offer images are allowed.', 422);
        }

        return $allowed[$mime];
    }

    protected function saveOfferBannerImage(array $context, array $input): string
    {
        if (empty($_FILES['offer_image_file']['name'])) {
            return '';
        }

        $file = $_FILES['offer_image_file'];
        $originalSize = (int) ($input['original_image_size'] ?? 0);
        $extension = $this->uploadedOfferImageExtension($file, $originalSize);

        $brandId = (string) $context['brand_id'];
        $root = $this->publicAssetRoot();

        $offerFolder = $root . 'RestaurantAssets' . DIRECTORY_SEPARATOR . 'brands' . DIRECTORY_SEPARATOR . $brandId . DIRECTORY_SEPARATOR . 'offers-banner';

        if (!is_dir($offerFolder) && !mkdir($offerFolder, 0755, true)) {
            $this->error('Unable to create offer banner folder.', 500);
        }

        $fileName = 'offer_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $targetPath = $offerFolder . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file((string) $file['tmp_name'], $targetPath)) {
            $this->error('Unable to save offer banner image.', 500);
        }

        return 'brands/' . $brandId . '/offers-banner/' . $fileName;
    }

    protected function normalizeOfferDateTime(string $value): ?string
    {
        $value = trim(str_replace('T', ' ', $value));

        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}$/', $value) === 1) {
            $value .= ':00';
        }

        $time = strtotime($value);

        if ($time === false) {
            $this->error('Please provide a valid offer date/time.', 422);
        }

        return date('Y-m-d H:i:s', $time);
    }

    protected function createOfferBanner(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $imagePath = $this->saveOfferBannerImage($context, $input);

        if ($imagePath === '') {
            $this->error('Offer image is required.', 422);
        }

        $payload = [
            'image_path' => $imagePath,
            'sort_order' => (int) ($input['sort_order'] ?? 1),
            'is_active' => (string) ($input['is_active'] ?? '1') === '1' ? 1 : 0,
            'starts_at' => $this->normalizeOfferDateTime((string) ($input['starts_at'] ?? '')),
            'ends_at' => $this->normalizeOfferDateTime((string) ($input['ends_at'] ?? '')),
        ];

        if (!$this->managerModel->createOfferBanner(
            $context['brand_id'],
            $context['branch_id'],
            $payload,
            $context['user_id']
        )) {
            $this->error('Unable to create offer banner.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Offer banner created successfully.'
        );
    }

    protected function updateOfferBanner(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $bannerId = trim((string) ($input['banner_id'] ?? ''));

        if ($bannerId === '') {
            $this->error('Offer banner id is required.', 422);
        }

        $existing = $this->managerModel->getOfferBannerDetail(
            $context['brand_id'],
            $context['branch_id'],
            $bannerId
        );

        if ($existing === null) {
            $this->error('Offer banner not found.', 404);
        }

        $newImagePath = $this->saveOfferBannerImage($context, $input);

        $payload = [
            'image_path' => $newImagePath !== '' ? $newImagePath : (string) ($existing['image_path'] ?? ''),
            'sort_order' => (int) ($input['sort_order'] ?? 1),
            'is_active' => (string) ($input['is_active'] ?? '1') === '1' ? 1 : 0,
            'starts_at' => $this->normalizeOfferDateTime((string) ($input['starts_at'] ?? '')),
            'ends_at' => $this->normalizeOfferDateTime((string) ($input['ends_at'] ?? '')),
        ];

        if (!$this->managerModel->updateOfferBanner(
            $context['brand_id'],
            $context['branch_id'],
            $bannerId,
            $payload,
            $context['user_id']
        )) {
            $this->error('Unable to update offer banner.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Offer banner updated successfully.'
        );
    }

    protected function deleteOfferBanner(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $bannerId = trim((string) ($input['banner_id'] ?? ''));

        if ($bannerId === '') {
            $this->error('Offer banner id is required.', 422);
        }

        if (!$this->managerModel->deleteOfferBanner(
            $context['brand_id'],
            $context['branch_id'],
            $bannerId,
            $context['user_id']
        )) {
            $this->error('Unable to delete offer banner.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Offer banner removed successfully.'
        );
    }

    protected function normalizeStaffImageRole(string $role): string
    {
        $role = strtolower(trim(str_replace([' ', '-'], '_', $role)));

        if (in_array($role, ['waiter', 'chef', 'manager', 'pad', 'cashier', 'staff'], true)) {
            return $role;
        }

        if (in_array($role, ['kitchen', 'cook'], true)) {
            return 'chef';
        }

        if (in_array($role, ['customerpad', 'customer_pad', 'customer'], true)) {
            return 'pad';
        }

        return 'staff';
    }

    // protected function nextRoleImageFilename(string $folder, string $rolePrefix, string $extension): string
    // {
    //     $next = 1;

    //     if (is_dir($folder)) {
    //         foreach ((scandir($folder) ?: []) as $file) {
    //             if (preg_match('/^' . preg_quote($rolePrefix, '/') . '(\d+)\.[a-z0-9]+$/i', $file, $m)) {
    //                 $next = max($next, ((int) $m[1]) + 1);
    //             }
    //         }
    //     }

    //     return $rolePrefix . $next . '.' . $extension;
    // }

    // protected function saveStaffProfileImage(array $context, array $input): string
    // {
    //     if (empty($_FILES['profile_pic_file']['name'])) {
    //         return '';
    //     }

    //     $file = $_FILES['profile_pic_file'];

    //     if ((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    //         $this->error('Profile image upload failed.', 422);
    //     }

    //     if ((int) ($file['size'] ?? 0) > 20 * 1024 * 1024) {
    //         $this->error('Profile image must be less than 20MB.', 422);
    //     }

    //     $originalName = (string) ($file['name'] ?? '');
    //     $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    //     $extension = preg_replace('/[^a-z0-9]/', '', $extension);

    //     if ($extension === '') {
    //         $extension = 'jpg';
    //     }

    //     $root = defined('FCPATH')
    //         ? rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR
    //         : rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''), '/\\') . DIRECTORY_SEPARATOR;

    //     if ($root === DIRECTORY_SEPARATOR) {
    //         $this->error('Public asset root is not available.', 500);
    //     }

    //     $brandId = (string) $context['brand_id'];

    //     $brandFolder = $root . 'RestaurantAssets' . DIRECTORY_SEPARATOR . 'brands' . DIRECTORY_SEPARATOR . $brandId;
    //     $profileFolder = $brandFolder . DIRECTORY_SEPARATOR . 'User-profile';

    //     if (!is_dir($brandFolder) && !mkdir($brandFolder, 0755, true)) {
    //         $this->error('Unable to create brand asset folder.', 500);
    //     }

    //     if (!is_dir($profileFolder) && !mkdir($profileFolder, 0755, true)) {
    //         $this->error('Unable to create user profile folder.', 500);
    //     }

    //     $rolePrefix = $this->normalizeStaffImageRole(
    //         (string) ($input['role_at_branch'] ?? $input['role'] ?? 'staff')
    //     );

    //     $fileName = $this->nextRoleImageFilename($profileFolder, $rolePrefix, $extension);
    //     $targetPath = $profileFolder . DIRECTORY_SEPARATOR . $fileName;

    //     if (!move_uploaded_file((string) $file['tmp_name'], $targetPath)) {
    //         $this->error('Unable to save profile image.', 500);
    //     }

    //     return 'brands/' . $brandId . '/User-profile/' . $fileName;
    // }



    // protected function saveMenuItemImage(array $context): string
    // {
    //     if (empty($_FILES['menu_image_file']['name'])) {
    //         return '';
    //     }

    //     $file = $_FILES['menu_image_file'];

    //     if ((int)($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    //         $this->error('Menu item photo upload failed.', 422);
    //     }

    //     if ((int)($file['size'] ?? 0) > 20 * 1024 * 1024) {
    //         $this->error('Menu item photo must be less than 20MB.', 422);
    //     }

    //     $tmpPath = (string)($file['tmp_name'] ?? '');
    //     $mime = is_file($tmpPath) ? (string)mime_content_type($tmpPath) : '';

    //     $allowed = [
    //         'image/jpeg' => 'jpg',
    //         'image/png'  => 'png',
    //         'image/webp' => 'webp',
    //         'image/gif'  => 'gif',
    //     ];

    //     if (!isset($allowed[$mime])) {
    //         $this->error('Only JPG, PNG, WEBP, and GIF images are allowed.', 422);
    //     }

    //     $root = defined('FCPATH')
    //         ? rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR
    //         : rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''), '/\\') . DIRECTORY_SEPARATOR;

    //     if ($root === DIRECTORY_SEPARATOR) {
    //         $this->error('Public asset root is not available.', 500);
    //     }

    //     $brandId = (string)$context['brand_id'];
    //     $folder = $root . 'RestaurantAssets' . DIRECTORY_SEPARATOR . 'brands' . DIRECTORY_SEPARATOR . $brandId . DIRECTORY_SEPARATOR . 'Menu-items';

    //     if (!is_dir($folder) && !mkdir($folder, 0755, true)) {
    //         $this->error('Unable to create menu item image folder.', 500);
    //     }

    //     $fileName = 'menu_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    //     $targetPath = $folder . DIRECTORY_SEPARATOR . $fileName;

    //     if (!move_uploaded_file($tmpPath, $targetPath)) {
    //         $this->error('Unable to save menu item photo.', 500);
    //     }

    //     return 'brands/' . $brandId . '/Menu-items/' . $fileName;
    // }




    protected function publicAssetRoot(): string
    {
        $root = defined('FCPATH')
            ? rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR
            : rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''), '/\\') . DIRECTORY_SEPARATOR;

        if ($root === DIRECTORY_SEPARATOR || $root === '') {
            $this->error('Public asset root is not available.', 500);
        }

        return $root;
    }

    protected function normalizeDbAssetPath(string $path): string
    {
        $path = trim($path);
        if ($path === '' || str_starts_with($path, 'data:') || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return '';
        }

        $path = parse_url($path, PHP_URL_PATH) ?: $path;
        $path = ltrim($path, '/');
        $path = preg_replace('#^RestaurantAssets/#i', '', $path);

        return str_replace(['..', '\\'], ['', '/'], $path);
    }

    protected function uploadedImageExtension(array $file, string $label): string
    {
        if ((int)($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->error($label . ' upload failed.', 422);
        }

        if ((int)($file['size'] ?? 0) > 20 * 1024 * 1024) {
            $this->error($label . ' must be less than 20MB.', 422);
        }

        $tmpPath = (string)($file['tmp_name'] ?? '');
        $mime = is_file($tmpPath) ? (string)mime_content_type($tmpPath) : '';

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];

        if (!isset($allowed[$mime])) {
            $this->error('Only JPG, PNG, WEBP, and GIF images are allowed.', 422);
        }

        return $allowed[$mime];
    }

    protected function nextRoleImageFilename(string $folder, string $rolePrefix, string $extension): string
    {
        $next = 1;

        if (is_dir($folder)) {
            foreach ((scandir($folder) ?: []) as $file) {
                if (preg_match('/^' . preg_quote($rolePrefix, '/') . '(\d+)\.[a-z0-9]+$/i', $file, $m)) {
                    $next = max($next, ((int)$m[1]) + 1);
                }
            }
        }

        return $rolePrefix . $next . '.' . $extension;
    }

    protected function safeExistingBrandFilePath(array $context, string $existingDbPath, array $allowedFolders): ?array
    {
        $brandId = (string)$context['brand_id'];
        $clean = $this->normalizeDbAssetPath($existingDbPath);

        if ($clean === '') {
            return null;
        }

        $allowedFolders = array_map(
            static fn($v) => trim(str_replace('\\', '/', (string)$v), '/'),
            $allowedFolders
        );

        $matched = false;
        foreach ($allowedFolders as $folder) {
            if (preg_match('#^brands/' . preg_quote($brandId, '#') . '/' . preg_quote($folder, '#') . '/[^/]+\.(jpg|jpeg|png|webp|gif)$#i', $clean)) {
                $matched = true;
                break;
            }
        }

        if (!$matched) {
            return null;
        }

        $absolute = $this->publicAssetRoot() . 'RestaurantAssets' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $clean);
        $extension = strtolower(pathinfo($absolute, PATHINFO_EXTENSION));

        return [
            'db_path'   => $clean,
            'absolute'  => $absolute,
            'extension' => $extension === 'jpeg' ? 'jpg' : $extension,
        ];
    }

    protected function saveStaffProfileImage(array $context, array $input, string $existingDbPath = ''): string
    {
        if (empty($_FILES['profile_pic_file']['name'])) {
            return '';
        }

        $file = $_FILES['profile_pic_file'];
        $extension = $this->uploadedImageExtension($file, 'Profile image');

        $brandId = (string)$context['brand_id'];
        $root = $this->publicAssetRoot();

        $brandFolder = $root . 'RestaurantAssets' . DIRECTORY_SEPARATOR . 'brands' . DIRECTORY_SEPARATOR . $brandId;
        $profileFolder = $brandFolder . DIRECTORY_SEPARATOR . 'User-profile';

        if (!is_dir($profileFolder) && !mkdir($profileFolder, 0755, true)) {
            $this->error('Unable to create user profile folder.', 500);
        }

        $existing = $this->safeExistingBrandFilePath($context, $existingDbPath, ['User-profile']);

        if ($existing !== null) {
            if ($existing['extension'] !== $extension) {
                $this->error('Please upload the same image type to replace the existing profile photo.', 422);
            }

            if (!move_uploaded_file((string)$file['tmp_name'], $existing['absolute'])) {
                $this->error('Unable to replace profile image.', 500);
            }

            return $existing['db_path'];
        }

        $rolePrefix = $this->normalizeStaffImageRole(
            (string)($input['role_at_branch'] ?? $input['role'] ?? 'staff')
        );

        $fileName = $this->nextRoleImageFilename($profileFolder, $rolePrefix, $extension);
        $targetPath = $profileFolder . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file((string)$file['tmp_name'], $targetPath)) {
            $this->error('Unable to save profile image.', 500);
        }

        return 'brands/' . $brandId . '/User-profile/' . $fileName;
    }

    protected function saveMenuItemImage(array $context, string $existingDbPath = ''): string
    {
        if (empty($_FILES['menu_image_file']['name'])) {
            return '';
        }

        $file = $_FILES['menu_image_file'];
        $extension = $this->uploadedImageExtension($file, 'Menu item photo');

        $brandId = (string)$context['brand_id'];
        $root = $this->publicAssetRoot();

        $menuFolder = $root . 'RestaurantAssets' . DIRECTORY_SEPARATOR . 'brands' . DIRECTORY_SEPARATOR . $brandId . DIRECTORY_SEPARATOR . 'menu-items';

        if (!is_dir($menuFolder) && !mkdir($menuFolder, 0755, true)) {
            $this->error('Unable to create menu item image folder.', 500);
        }

        $existing = $this->safeExistingBrandFilePath($context, $existingDbPath, ['menu-items', 'Menu-items']);

        if ($existing !== null) {
            if ($existing['extension'] !== $extension) {
                $this->error('Please upload the same image type to replace the existing menu photo.', 422);
            }

            if (!move_uploaded_file((string)$file['tmp_name'], $existing['absolute'])) {
                $this->error('Unable to replace menu item photo.', 500);
            }

            return $existing['db_path'];
        }

        $fileName = 'menu_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $targetPath = $menuFolder . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file((string)$file['tmp_name'], $targetPath)) {
            $this->error('Unable to save menu item photo.', 500);
        }

        return 'brands/' . $brandId . '/menu-items/' . $fileName;
    }



    protected function generateStaffEmployeeCode(array $input): void
    {
        $context = $this->resolveContext($input);

        $role = trim((string) (
            $input['role_at_branch']
            ?? $_GET['role_at_branch']
            ?? $input['role']
            ?? $_GET['role']
            ?? ''
        ));

        if ($role === '') {
            $this->error('Role is required to generate employee code.', 422);
        }

        $code = $this->managerModel->generateNextEmployeeCode(
            $context['brand_id'],
            $context['branch_id'],
            $role
        );

        $this->json([
            'ok' => true,
            'employee_code' => $code,
        ]);
    }

    protected function createStaff(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $profilePicPath = $this->saveStaffProfileImage($context, $input);



        $payload = [
            'full_name'            => trim((string) ($input['full_name'] ?? '')),
            'email'                => strtolower(trim((string) ($input['email'] ?? ''))),
            'phone'                => trim((string) ($input['phone'] ?? '')),
            'employee_code'        => trim((string) ($input['employee_code'] ?? '')) !== ''
                ? trim((string) ($input['employee_code'] ?? ''))
                : $this->managerModel->generateNextEmployeeCode(
                    $context['brand_id'],
                    $context['branch_id'],
                    trim((string) ($input['role_at_branch'] ?? $input['role'] ?? 'waiter'))
                ),
            'profile_pic'          => $profilePicPath,
            'aadhar_no'            => trim((string) ($input['aadhar_no'] ?? '')),
            'role_at_branch'       => trim((string) ($input['role_at_branch'] ?? $input['role'] ?? 'waiter')),
            'is_active'            => trim((string) ($input['is_active'] ?? '1')),
            'access_status'        => trim((string) ($input['access_status'] ?? 'active')),
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

    // protected function updateStaff(array $input): void
    // {
    //     if ($this->requestMethod() !== 'POST') {
    //         $this->error('Invalid request method.', 405);
    //     }

    //     $context = $this->resolveContext($input);
    //     $targetUserId = trim((string) ($input['target_user_id'] ?? ''));
    //     $existingStaff = $this->managerModel->getStaffDetail($context['brand_id'], $context['branch_id'], $targetUserId);
    //     $profilePicPath = $this->saveStaffProfileImage($context, $input, (string)($existingStaff['profile_pic'] ?? ''));

    //     if ($targetUserId === '') {
    //         $this->error('Target user id is required.', 422);
    //     }

    //     $payload = [
    //         'full_name'            => trim((string) ($input['full_name'] ?? '')),
    //         'email'                => strtolower(trim((string) ($input['email'] ?? ''))),
    //         'phone'                => trim((string) ($input['phone'] ?? '')),
    //         'employee_code'        => trim((string) ($input['employee_code'] ?? '')),
    //         'profile_pic'          => $profilePicPath,
    //         'aadhar_no'            => trim((string) ($input['aadhar_no'] ?? '')),
    //         'role_at_branch'       => trim((string) ($input['role_at_branch'] ?? $input['role'] ?? 'waiter')),
    //         'is_active'            => trim((string) ($input['is_active'] ?? '1')),
    //         'access_status'        => trim((string) ($input['access_status'] ?? 'active')),
    //         'assigned_table_codes' => $this->managerModel->normalizeCsvList($input['assigned_table_codes'] ?? []),
    //         'shift_start_time'     => trim((string) ($input['shift_start_time'] ?? '')),
    //         'shift_end_time'       => trim((string) ($input['shift_end_time'] ?? '')),
    //         'permissions'          => is_array($input['permissions'] ?? null) ? $input['permissions'] : [],
    //     ];

    //     if ($payload['full_name'] === '' || $payload['email'] === '') {
    //         $this->error('Full name and email are required.', 422);
    //     }

    //     if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
    //         $this->error('Please enter a valid email address.', 422);
    //     }

    //     if (!$this->managerModel->updateStaff(
    //         $context['brand_id'],
    //         $context['branch_id'],
    //         $targetUserId,
    //         $payload,
    //         $context['user_id']
    //     )) {
    //         $this->error('Unable to update staff.', 500);
    //     }

    //     $this->refreshPayload(
    //         $context['brand_id'],
    //         $context['branch_id'],
    //         $context['user_id'],
    //         'Staff updated successfully.'
    //     );
    // }

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

        $existingStaff = $this->managerModel->getStaffDetail(
            $context['brand_id'],
            $context['branch_id'],
            $targetUserId
        );

        if ($existingStaff === null) {
            $this->error('Staff member not found.', 404);
        }

        $profilePicPath = $this->saveStaffProfileImage(
            $context,
            $input,
            (string) ($existingStaff['profile_pic'] ?? '')
        );

        $payload = [
            'full_name'            => trim((string) ($input['full_name'] ?? '')),
            'email'                => strtolower(trim((string) ($input['email'] ?? ''))),
            'phone'                => trim((string) ($input['phone'] ?? '')),
            'employee_code'        => trim((string) ($input['employee_code'] ?? '')),
            'profile_pic'          => $profilePicPath !== '' ? $profilePicPath : ((string)($existingStaff['profile_pic'] ?? '') !== '' ? (string)$existingStaff['profile_pic'] : 'default/default-avatar.svg'),
            'aadhar_no'            => trim((string) ($input['aadhar_no'] ?? '')),
            'role_at_branch'       => trim((string) ($input['role_at_branch'] ?? $input['role'] ?? 'waiter')),
            'is_active'            => trim((string) ($input['is_active'] ?? '1')),
            'access_status'        => trim((string) ($input['access_status'] ?? 'active')),
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

    protected function updateManagerProfile(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $targetUserId = trim((string)($input['target_user_id'] ?? $context['user_id']));
        if ($targetUserId === '') {
            $this->error('Manager user id is required.', 422);
        }

        // $profilePicPath = $this->saveStaffProfileImage($context, [
        //     'role_at_branch' => 'manager',
        // ]);

        $existingManager = $this->managerModel->one(
            "SELECT profile_pic FROM users WHERE id = ? AND brand_id = ? LIMIT 1",
            [$targetUserId, $context['brand_id']]
        );

        $profilePicPath = $this->saveStaffProfileImage(
            $context,
            ['role_at_branch' => 'manager'],
            (string)($existingManager['profile_pic'] ?? '')
        );


        $payload = [
            'full_name'     => trim((string)($input['full_name'] ?? '')),
            'email'         => strtolower(trim((string)($input['email'] ?? ''))),
            'phone'         => trim((string)($input['phone'] ?? '')),
            'employee_code' => trim((string)($input['employee_code'] ?? '')),
            'profile_pic'   => $profilePicPath !== '' ? $profilePicPath : ((string)($existingManager['profile_pic'] ?? '') !== '' ? (string)$existingManager['profile_pic'] : 'default/default-avatar.svg'),
            'aadhar_no'     => trim((string)($input['aadhar_no'] ?? '')),
        ];

        if ($payload['full_name'] === '' || $payload['email'] === '') {
            $this->error('Full name and email are required.', 422);
        }

        if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error('Please enter a valid email address.', 422);
        }

        if (!$this->managerModel->updateManagerProfile(
            $context['brand_id'],
            $context['branch_id'],
            $targetUserId,
            $payload,
            $context['user_id']
        )) {
            $this->error('Unable to update manager profile.', 500);
        }

        $this->refreshPayload(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            'Profile updated successfully.'
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
        $targetUserId = trim((string) (
            $input['target_user_id']
            ?? $input['staff_id']
            ?? $input['user_id_to_delete']
            ?? ''
        ));

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
        $menuImagePath = $this->saveMenuItemImage($context);

        $payload = [
            'category_id'                => trim((string) ($input['category_id'] ?? '')),
            'category_name'              => trim((string) ($input['category_name'] ?? $input['category'] ?? '')),
            'default_kitchen_station_id' => trim((string) ($input['default_kitchen_station_id'] ?? '')),
            'name'                       => trim((string) ($input['name'] ?? $input['item_name'] ?? '')),
            'sku'                        => trim((string) ($input['sku'] ?? '')),
            'description'                => trim((string) ($input['description'] ?? '')),
            'image_url'                  => $menuImagePath !== '' ? $menuImagePath : trim((string)($input['image_url'] ?? '')),
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

    // protected function updateMenuItem(array $input): void
    // {
    //     if ($this->requestMethod() !== 'POST') {
    //         $this->error('Invalid request method.', 405);
    //     }

    //     $context = $this->resolveContext($input);
    //     $menuId = trim((string) ($input['menu_id'] ?? ''));
    //     $existingMenu = $this->managerModel->getMenuItemDetail($context['brand_id'], $context['branch_id'], $menuId);
    //     $menuImagePath = $this->saveMenuItemImage($context, (string)($existingMenu['image_url'] ?? ''));

    //     if ($menuId === '') {
    //         $this->error('Menu id is required.', 422);
    //     }

    //     $payload = [
    //         'category_id'                => trim((string) ($input['category_id'] ?? '')),
    //         'category_name'              => trim((string) ($input['category_name'] ?? $input['category'] ?? '')),
    //         'default_kitchen_station_id' => trim((string) ($input['default_kitchen_station_id'] ?? '')),
    //         'name'                       => trim((string) ($input['name'] ?? $input['item_name'] ?? '')),
    //         'sku'                        => trim((string) ($input['sku'] ?? '')),
    //         'description'                => trim((string) ($input['description'] ?? '')),
    //         'image_url'                  => $menuImagePath !== '' ? $menuImagePath : trim((string)($input['existing_image_url'] ?? $input['image_url'] ?? '')),
    //         'video_url'                  => trim((string) ($input['video_url'] ?? '')),
    //         'video_type'                 => trim((string) ($input['video_type'] ?? '')),
    //         'item_type'                  => trim((string) ($input['item_type'] ?? 'food')),
    //         'base_price'                 => trim((string) ($input['base_price'] ?? $input['price'] ?? '0')),
    //         'selling_price'              => trim((string) ($input['selling_price'] ?? $input['price'] ?? '0')),
    //         'tax_rate'                   => trim((string) ($input['tax_rate'] ?? '0')),
    //         'preparation_time_mins'      => trim((string) ($input['preparation_time_mins'] ?? '0')),
    //         'is_veg'                     => trim((string) ($input['is_veg'] ?? '0')),
    //         'is_vegan'                   => trim((string) ($input['is_vegan'] ?? '0')),
    //         'is_spicy'                   => trim((string) ($input['is_spicy'] ?? '0')),
    //         'is_available'               => trim((string) ($input['is_available'] ?? '1')),
    //         'stock_status'               => trim((string) ($input['stock_status'] ?? 'in_stock')),
    //         'available_from'             => trim((string) ($input['available_from'] ?? '')),
    //         'available_to'               => trim((string) ($input['available_to'] ?? '')),
    //     ];

    //     if ($payload['name'] === '') {
    //         $this->error('Menu item name is required.', 422);
    //     }

    //     if (
    //         !is_numeric($payload['base_price']) ||
    //         !is_numeric($payload['selling_price']) ||
    //         !is_numeric($payload['tax_rate'])
    //     ) {
    //         $this->error('Base price, selling price, and tax rate must be numeric.', 422);
    //     }

    //     if (!$this->managerModel->updateMenuItem(
    //         $context['brand_id'],
    //         $context['branch_id'],
    //         $menuId,
    //         $payload,
    //         $context['user_id']
    //     )) {
    //         $this->error('Unable to update menu item.', 500);
    //     }

    //     $this->refreshPayload(
    //         $context['brand_id'],
    //         $context['branch_id'],
    //         $context['user_id'],
    //         'Menu item updated successfully.'
    //     );
    // }
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

        $existingMenu = $this->managerModel->getMenuItemDetail(
            $context['brand_id'],
            $context['branch_id'],
            $menuId
        );

        if ($existingMenu === null) {
            $this->error('Menu item not found.', 404);
        }

        $menuImagePath = $this->saveMenuItemImage(
            $context,
            (string) ($existingMenu['image_url'] ?? '')
        );

        $payload = [
            'category_id'                => trim((string) ($input['category_id'] ?? '')),
            'category_name'              => trim((string) ($input['category_name'] ?? $input['category'] ?? '')),
            'default_kitchen_station_id' => trim((string) ($input['default_kitchen_station_id'] ?? '')),
            'name'                       => trim((string) ($input['name'] ?? $input['item_name'] ?? '')),
            'sku'                        => trim((string) ($input['sku'] ?? '')),
            'description'                => trim((string) ($input['description'] ?? '')),
            'image_url'                  => $menuImagePath !== '' ? $menuImagePath : ((string)($existingMenu['image_url'] ?? '') !== '' ? (string)$existingMenu['image_url'] : 'default/default-food.jpg'),
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

        $status = $this->normalizeOrderStatus((string) ($input['status'] ?? 'new'));
        $note = trim((string) ($input['manager_note'] ?? $input['note'] ?? ''));

        if ($status === '') {
            $this->error('Invalid order status.', 422);
        }

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

        $status = $this->normalizeRequestStatus((string) ($input['status'] ?? 'new'));
        $note = trim((string) ($input['resolution_note'] ?? $input['note'] ?? ''));

        if ($status === '') {
            $this->error('Invalid request status.', 422);
        }

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

    protected function changePassword(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $targetUserId = trim((string) ($input['target_user_id'] ?? $context['user_id']));
        $currentPassword = (string) ($input['current_password'] ?? '');
        $newPassword = (string) ($input['new_password'] ?? '');
        $confirmPassword = (string) ($input['confirm_password'] ?? '');

        if ($targetUserId === '') {
            $this->error('Target user id is required.', 422);
        }
        if ($currentPassword === '') {
            $this->error('Current password is required.', 422);
        }
        if (strlen($newPassword) < 6) {
            $this->error('New password must be at least 6 characters.', 422);
        }
        if ($newPassword !== $confirmPassword) {
            $this->error('Passwords do not match.', 422);
        }

        if (!$this->changeUserPasswordRecord(
            $context['brand_id'],
            $context['branch_id'],
            $targetUserId,
            $currentPassword,
            $newPassword
        )) {
            $this->error('Unable to change password.', 500);
        }

        $this->success('Password changed successfully.');
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
        $tableNumber = trim((string) ($input['table_number'] ?? ''));
        $zone = trim((string) ($input['zone'] ?? ''));
        $shape = trim((string) ($input['shape'] ?? 'square'));
        $status = $this->normalizeTableStatus((string) ($input['status'] ?? 'available'));

        if ($tableCode === '') {
            $this->error('Table code is required.', 422);
        }

        $ok = $this->saveTableRecord(
            $context['brand_id'],
            $context['branch_id'],
            $tableId,
            [
                'table_code'   => $tableCode,
                'table_number' => $tableNumber,
                'capacity'     => $capacity > 0 ? $capacity : 1,
                'zone'         => $zone,
                'shape'        => $shape !== '' ? $shape : 'square',
                'status'       => $status,
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
        $tableNumber = trim((string) ($payload['table_number'] ?? ''));
        $shape = trim((string) ($payload['shape'] ?? 'square'));
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
                    $updates['table_number'] = $tableNumber !== '' ? $tableNumber : $this->extractTableNumber($tableCode);
                }
                if ($this->managerModel->columnExists($tableName, 'capacity')) {
                    $updates['capacity'] = $capacity;
                }
                if ($this->managerModel->columnExists($tableName, 'shape')) {
                    $updates['shape'] = $shape !== '' ? $shape : 'square';
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
            $insert['table_number'] = $tableNumber !== '' ? $tableNumber : $this->extractTableNumber($tableCode);
        }
        if ($this->managerModel->columnExists($tableName, 'capacity')) {
            $insert['capacity'] = $capacity;
        }
        if ($this->managerModel->columnExists($tableName, 'shape')) {
            $insert['shape'] = $shape !== '' ? $shape : 'square';
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

    private function changeUserPasswordRecord(
        string $brandId,
        string $branchId,
        string $targetUserId,
        string $currentPassword,
        string $newPassword
    ): bool {
        $userTable = $this->managerModel->tableExists('users') ? 'users' : '';

        if ($userTable === '') {
            return false;
        }

        $where = 'id = ?';
        $params = [$targetUserId];

        if ($this->managerModel->columnExists($userTable, 'brand_id')) {
            $where .= ' AND brand_id = ?';
            $params[] = $brandId;
        }

        $user = $this->managerModel->one(
            "SELECT * FROM {$userTable} WHERE {$where} LIMIT 1",
            $params
        );

        if (!is_array($user)) {
            throw new RuntimeException('User not found.', 404);
        }

        $passwordColumn = $this->managerModel->columnExists($userTable, 'password_hash')
            ? 'password_hash'
            : ($this->managerModel->columnExists($userTable, 'password') ? 'password' : '');

        if ($passwordColumn === '') {
            throw new RuntimeException('Password column is missing.', 500);
        }

        $stored = (string) ($user[$passwordColumn] ?? '');
        $valid = password_verify($currentPassword, $stored);

        if (!$valid && $stored !== '' && hash_equals($stored, $currentPassword)) {
            $valid = true;
        }

        if (!$valid) {
            throw new RuntimeException('Current password is incorrect.', 422);
        }

        $updates = [
            $passwordColumn => password_hash($newPassword, PASSWORD_DEFAULT),
        ];

        if ($this->managerModel->columnExists($userTable, 'updated_at')) {
            $updates['updated_at'] = $this->now();
        }

        return $this->dynamicUpdateById($userTable, $targetUserId, $updates);
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

    private function normalizeOrderStatus(string $status): string
    {
        $status = strtolower(trim(str_replace([' ', '-'], '_', $status)));

        if ($status === 'canceled') {
            $status = 'cancelled';
        }

        $allowed = ['new', 'accepted', 'preparing', 'ready', 'served', 'completed', 'cancelled'];

        return in_array($status, $allowed, true) ? $status : '';
    }

    private function normalizeRequestStatus(string $status): string
    {
        $status = strtolower(trim(str_replace([' ', '-'], '_', $status)));

        if ($status === 'accepted' || $status === 'resolved') {
            $status = 'approved';
        }
        if ($status === 'canceled') {
            $status = 'cancelled';
        }

        $allowed = ['new', 'approved', 'cancelled', 'closed'];

        return in_array($status, $allowed, true) ? $status : '';
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
