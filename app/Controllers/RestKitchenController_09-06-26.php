<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\RestKitchenModel;
use RuntimeException;
use Throwable;

require_once APPPATH . 'Models/RestKitchenModel.php';

final class RestKitchenController extends RestBaseController
{
    private const VIEW_PATH = APPPATH . 'Views/restaurant-system/kitchen.php';

    protected RestKitchenModel $kitchenModel;

    public function __construct()
    {
        parent::__construct();
        $this->kitchenModel = new RestKitchenModel();
    }

    public function index(): void
    {
        try {
            $context = $this->resolveContext();

            $pageData = $this->kitchenModel->getKitchenPageData(
                $context['brand_id'],
                $context['branch_id'],
                $context['user_id']
            );

            $this->renderView(self::VIEW_PATH, $pageData);
        } catch (Throwable $e) {
            $this->renderView(self::VIEW_PATH, [
                'brandId'                  => $this->currentBrandId(),
                'branchId'                 => $this->currentBranchId(),
                'userId'                   => $this->currentUserId(),
                'brand'                    => [],
                'context'                  => [],
                'summary'                  => [],
                'orders'                   => [],
                'stations'                 => [],
                'userSettings'             => [],
                'defaultNotificationSound' => 'RestaurantAssets/AlertSounds/Sound1.mpeg',
                'pageError'                => $e->getMessage(),
            ]);
        }
    }

    public function api(): void
    {
        try {
            $this->sendNoStoreHeaders();
            $input = $this->requestData();
            $action = trim((string)($_GET['action'] ?? $input['action'] ?? ''));

            if ($action === '') {
                $this->respondError('Action is required.', 400);
            }

            switch ($action) {
                case 'dashboard_snapshot':
                    $this->dashboardSnapshot();
                    return;

                case 'order_detail':
                    $this->orderDetail($input);
                    return;

                case 'get_user_settings':
                    $this->getUserSettings();
                    return;

                case 'update_user_settings':
                    $this->updateUserSettings($input);
                    return;

                case 'accept_order':
                    $this->updateWholeOrder($input, 'accepted', 'Order accepted.');
                    return;

                case 'start_preparing':
                    $this->updateWholeOrder($input, 'preparing', 'Preparation started.');
                    return;

                case 'mark_ready':
                    $this->updateWholeOrder($input, 'ready', 'Order marked ready.');
                    return;

                case 'mark_delayed':
                    $this->updateWholeOrder($input, 'delayed', 'Order marked delayed.');
                    return;

                case 'mark_served':
                    $this->updateWholeOrder($input, 'served', 'Order marked served.');
                    return;

                case 'cancel_order':
                    $this->updateWholeOrder($input, 'cancelled', 'Order cancelled.');
                    return;

                case 'update_order_status':
                    $status = $this->normalizeKitchenStatus((string)($input['status'] ?? ''));
                    $this->updateWholeOrder($input, $status, 'Order updated.');
                    return;

                case 'update_order_item_status':
                    $this->updateOrderItemStatus($input);
                    return;

                case 'call_waiter':
                    $this->callWaiter($input);
                    return;

                default:
                    $this->respondError('Unknown kitchen action.', 404);
            }
        } catch (Throwable $e) {
            $status = (int)$e->getCode();
            if ($status < 400 || $status > 599) {
                $status = 500;
            }

            $this->respondError($e->getMessage(), $status);
        }
    }

    protected function resolveContext(): array
    {
        $brandId = trim((string)$this->currentBrandId());
        $branchId = trim((string)$this->currentBranchId());
        $userId = trim((string)$this->currentUserId());
        $role = strtolower(trim((string)$this->currentUserRole()));

        if ($brandId === '' || $branchId === '' || $userId === '') {
            throw new RuntimeException('Kitchen context could not be resolved from the authenticated session.', 403);
        }

        if (!in_array($role, ['chef', 'kitchen', 'manager', 'brand'], true)) {
            throw new RuntimeException('You are not authorized to access the kitchen module.', 403);
        }

        $_SESSION['rest_kitchen_context'] = [
            'brand_id'  => $brandId,
            'branch_id' => $branchId,
            'user_id'   => $userId,
            'user_role' => $role,
        ];

        return $_SESSION['rest_kitchen_context'];
    }

    protected function dashboardSnapshot(): void
    {
        $this->requireMethod('GET');
        $context = $this->resolveContext();

        $snapshot = $this->kitchenModel->buildKitchenSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        if (!is_array($snapshot)) {
            throw new RuntimeException('Unable to load kitchen snapshot.', 500);
        }

        $this->respondOk([
            'snapshot' => $snapshot,
        ]);
    }

    protected function orderDetail(array $input): void
    {
        $this->requireMethod('GET');
        $context = $this->resolveContext();

        $orderId = trim((string)($_GET['order_id'] ?? $input['order_id'] ?? ''));

        if ($orderId === '') {
            throw new RuntimeException('order_id is required.', 400);
        }

        $detail = $this->kitchenModel->getKitchenOrderDetail(
            $context['brand_id'],
            $context['branch_id'],
            $orderId
        );

        if (!is_array($detail) || $detail === []) {
            throw new RuntimeException('Order not found.', 404);
        }

        $this->respondOk([
            'detail' => $detail,
        ]);
    }

    protected function getUserSettings(): void
    {
        $this->requireMethod('GET');
        $context = $this->resolveContext();

        $settings = $this->kitchenModel->getUserSettings(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        $this->respondOk([
            'settings' => $settings,
        ]);
    }

    protected function updateUserSettings(array $input): void
    {
        $this->requireMethod('POST');
        $context = $this->resolveContext();

        $payload = [];

        if (array_key_exists('theme', $input)) {
            $theme = strtolower(trim((string)$input['theme']));
            if (!in_array($theme, ['light', 'dark'], true)) {
                throw new RuntimeException('Invalid theme value.', 422);
            }
            $payload['theme'] = $theme;
        }

        if (array_key_exists('sound_alerts_enabled', $input)) {
            $payload['sound_alerts_enabled'] = ((int)$input['sound_alerts_enabled'] === 1) ? 1 : 0;
        }

        if (array_key_exists('lite_notification_sound', $input)) {
            $sound = trim((string)$input['lite_notification_sound']);
            $payload['lite_notification_sound'] = $sound !== '' ? $sound : null;
        }

        if ($payload === []) {
            throw new RuntimeException('No valid settings were provided.', 422);
        }

        $settings = $this->kitchenModel->updateUserSettings(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $payload
        );

        $this->respondOk([
            'message'  => 'Kitchen settings updated.',
            'settings' => $settings,
        ]);
    }

  /*  protected function updateWholeOrder(array $input, string $status, string $message): void
    {
        $this->requireMethod('POST');
        $context = $this->resolveContext();

        $orderId = trim((string)($input['order_id'] ?? ''));
        if ($orderId === '') {
            throw new RuntimeException('order_id is required.', 400);
        }

        $status = $this->normalizeKitchenStatus($status);

        $updated = match ($status) {
            'accepted'  => $this->kitchenModel->acceptOrder($context['brand_id'], $context['branch_id'], $orderId, $context['user_id']),
            'preparing' => $this->kitchenModel->startPreparing($context['brand_id'], $context['branch_id'], $orderId, $context['user_id']),
            'ready'     => $this->kitchenModel->markReady($context['brand_id'], $context['branch_id'], $orderId, $context['user_id']),
            'delayed'   => $this->kitchenModel->markDelayed($context['brand_id'], $context['branch_id'], $orderId, $context['user_id']),
            'served'    => $this->kitchenModel->markServed($context['brand_id'], $context['branch_id'], $orderId, $context['user_id']),
            'cancelled' => $this->kitchenModel->cancelOrder($context['brand_id'], $context['branch_id'], $orderId, $context['user_id']),
            default     => false,
        };
               

        if (!$updated) {
            throw new RuntimeException('Unable to update kitchen order.', 500);
        }

        $this->respondWithSnapshot($context, $message);
    }*/
    
    
    
    
    protected function updateWholeOrder(array $input, string $status, string $message): void
{
    $this->requireMethod('POST');
    $context = $this->resolveContext();

    $orderId = trim((string)($input['order_id'] ?? ''));
    if ($orderId === '') {
        throw new RuntimeException('order_id is required.', 400);
    }

    $status = $this->normalizeKitchenStatus($status);

    switch ($status) {
        case 'accepted':
            $updated = $this->kitchenModel->acceptOrder(
                $context['brand_id'],
                $context['branch_id'],
                $orderId,
                $context['user_id']
            );
            break;

        case 'preparing':
            $updated = $this->kitchenModel->startPreparing(
                $context['brand_id'],
                $context['branch_id'],
                $orderId,
                $context['user_id']
            );
            break;

        case 'ready':
            $updated = $this->kitchenModel->markReady(
                $context['brand_id'],
                $context['branch_id'],
                $orderId,
                $context['user_id']
            );
            break;

        case 'delayed':
            $updated = $this->kitchenModel->markDelayed(
                $context['brand_id'],
                $context['branch_id'],
                $orderId,
                $context['user_id']
            );
            break;

        case 'served':
            $updated = $this->kitchenModel->markServed(
                $context['brand_id'],
                $context['branch_id'],
                $orderId,
                $context['user_id']
            );
            break;

        case 'cancelled':
            $updated = $this->kitchenModel->cancelOrder(
                $context['brand_id'],
                $context['branch_id'],
                $orderId,
                $context['user_id']
            );
            break;

        default:
            $updated = false;
            break;
    }

    if (!$updated) {
        throw new RuntimeException('Unable to update kitchen order.', 500);
    }

    $this->respondWithSnapshot($context, $message);
}
    
    
    

    protected function updateOrderItemStatus(array $input): void
    {
        $this->requireMethod('POST');
        $context = $this->resolveContext();

        $orderId = trim((string)($input['order_id'] ?? ''));
        $orderItemId = trim((string)($input['order_item_id'] ?? $input['item_id'] ?? ''));
        $status = $this->normalizeKitchenStatus((string)($input['status'] ?? ''));

        if ($orderId === '') {
            throw new RuntimeException('order_id is required.', 400);
        }

        if ($orderItemId === '') {
            throw new RuntimeException('order_item_id is required.', 400);
        }

        if (!in_array($status, ['accepted', 'preparing', 'ready', 'served', 'cancelled'], true)) {
            throw new RuntimeException('Invalid item status.', 422);
        }

        $updated = $this->kitchenModel->updateOrderItemStatus(
            $context['brand_id'],
            $context['branch_id'],
            $orderId,
            $orderItemId,
            $status,
            $context['user_id']
        );

        if (!$updated) {
            throw new RuntimeException('Unable to update kitchen item status.', 500);
        }

        $this->respondWithSnapshot($context, 'Item updated.');
    }

    protected function callWaiter(array $input): void
    {
        $this->requireMethod('POST');
        $context = $this->resolveContext();

        $orderId = trim((string)($input['order_id'] ?? ''));
        if ($orderId === '') {
            throw new RuntimeException('order_id is required.', 400);
        }

        $called = $this->kitchenModel->callWaiter(
            $context['brand_id'],
            $context['branch_id'],
            $orderId,
            $context['user_id']
        );

        if (!$called) {
            throw new RuntimeException('Unable to notify waiter.', 500);
        }

        $this->respondWithSnapshot($context, 'Waiter notified.');
    }

    protected function respondWithSnapshot(array $context, string $message): void
    {
        $snapshot = $this->kitchenModel->buildKitchenSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        $this->respondOk([
            'message'  => $message,
            'snapshot' => is_array($snapshot) ? $snapshot : [],
        ]);
    }

    protected function normalizeKitchenStatus(string $status): string
    {
        $status = strtolower(trim($status));

        $aliases = [
            'new'        => 'new',
            'pending'    => 'new',
            'placed'     => 'new',
            'accepted'   => 'accepted',
            'preparing'  => 'preparing',
            'cooking'    => 'preparing',
            'ready'      => 'ready',
            'delayed'    => 'delayed',
            'served'     => 'served',
            'cancelled'  => 'cancelled',
            'canceled'   => 'cancelled',
        ];

        if (!isset($aliases[$status])) {
            throw new RuntimeException('Invalid kitchen status.', 422);
        }

        return $aliases[$status];
    }

    protected function requestMethod(): string
    {
        return strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    }

    protected function requireMethod(string $method): void
    {
        if ($this->requestMethod() !== strtoupper($method)) {
            throw new RuntimeException('Invalid request method.', 405);
        }
    }

    protected function respondOk(array $payload = [], int $status = 200): void
    {
        $this->sendNoStoreHeaders();
        $message = (string)($payload['message'] ?? 'OK');
        unset($payload['message']);

        $this->json(array_merge([
            'ok'      => true,
            'success' => true,
            'message' => $message,
        ], $payload), $status);
    }

    protected function respondError(string $message, int $status = 400): void
    {
        $this->sendNoStoreHeaders();
        $this->json([
            'ok'      => false,
            'success' => false,
            'message' => $message,
            'error'   => $message,
        ], $status);
    }

    protected function sendNoStoreHeaders(): void
    {
        if (!headers_sent()) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
        }
    }
}
