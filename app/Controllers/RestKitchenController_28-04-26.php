<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\RestKitchenModel;
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
        $input = $this->requestData();
        $context = $this->resolveContext($input);

        $pageData = $this->kitchenModel->getKitchenPageData(
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

                case 'accept_order':
                    $this->acceptOrder($input);
                    return;

                case 'start_preparing':
                    $this->startPreparing($input);
                    return;

                case 'mark_ready':
                    $this->markReady($input);
                    return;

                case 'call_waiter':
                    $this->callWaiter($input);
                    return;

                default:
                    $this->error('Unknown action.', 404);
                    return;
            }
        } catch (Throwable $e) {
            $this->error($e->getMessage(), 500);
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

    protected function contextSessionKey(): string
    {
        return 'rest_kitchen_context';
    }

    protected function resolveContext(array $input = []): array
    {
        $sessionContext = $_SESSION[$this->contextSessionKey()] ?? [];

        $brandId = (string)($input['brand_id'] ?? $_GET['brand_id'] ?? $sessionContext['brand_id'] ?? $this->brandId);
        $branchId = (string)($input['branch_id'] ?? $_GET['branch_id'] ?? $sessionContext['branch_id'] ?? $this->branchId);
        $userId = (string)($input['user_id'] ?? $_GET['user_id'] ?? $sessionContext['user_id'] ?? $this->userId);

        $brandId = $this->kitchenModel->normalizeEntityIdOrDefault($brandId, $this->brandId);
        $branchId = $this->kitchenModel->normalizeEntityIdOrDefault($branchId, $this->branchId);
        $userId = $this->kitchenModel->normalizeEntityIdOrDefault($userId, $this->userId);

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

    protected function dashboardSnapshot(array $input): void
    {
        if ($this->requestMethod() !== 'GET') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);

        $snapshot = $this->kitchenModel->buildKitchenSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        if ($snapshot === null) {
            $this->error('Unable to load kitchen snapshot.', 500);
        }

        $this->json([
            'ok'       => true,
            'snapshot' => $snapshot,
        ]);
    }

    protected function orderDetail(array $input): void
    {
        if ($this->requestMethod() !== 'GET') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $orderId = trim((string)($_GET['order_id'] ?? $input['order_id'] ?? ''));

        if ($orderId === '') {
            $this->error('order_id is required.', 400);
        }

        $detail = $this->kitchenModel->getKitchenOrderDetail(
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

    protected function acceptOrder(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $orderId = trim((string)($input['order_id'] ?? ''));

        if ($orderId === '') {
            $this->error('order_id is required.', 400);
        }

        if (!$this->kitchenModel->acceptOrder(
            $context['brand_id'],
            $context['branch_id'],
            $orderId,
            $context['user_id']
        )) {
            $this->error('Unable to accept order.', 500);
        }

        $snapshot = $this->kitchenModel->buildKitchenSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        $this->json([
            'ok'       => true,
            'message'  => 'Order accepted.',
            'snapshot' => $snapshot,
        ]);
    }

    protected function startPreparing(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $orderId = trim((string)($input['order_id'] ?? ''));

        if ($orderId === '') {
            $this->error('order_id is required.', 400);
        }

        if (!$this->kitchenModel->startPreparing(
            $context['brand_id'],
            $context['branch_id'],
            $orderId,
            $context['user_id']
        )) {
            $this->error('Unable to start preparing.', 500);
        }

        $snapshot = $this->kitchenModel->buildKitchenSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        $this->json([
            'ok'       => true,
            'message'  => 'Preparation started.',
            'snapshot' => $snapshot,
        ]);
    }

    protected function markReady(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $orderId = trim((string)($input['order_id'] ?? ''));

        if ($orderId === '') {
            $this->error('order_id is required.', 400);
        }

        if (!$this->kitchenModel->markReady(
            $context['brand_id'],
            $context['branch_id'],
            $orderId,
            $context['user_id']
        )) {
            $this->error('Unable to mark order ready.', 500);
        }

        $snapshot = $this->kitchenModel->buildKitchenSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        $this->json([
            'ok'       => true,
            'message'  => 'Order marked ready.',
            'snapshot' => $snapshot,
        ]);
    }

    protected function callWaiter(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $orderId = trim((string)($input['order_id'] ?? ''));

        if ($orderId === '') {
            $this->error('order_id is required.', 400);
        }

        if (!$this->kitchenModel->callWaiter(
            $context['brand_id'],
            $context['branch_id'],
            $orderId,
            $context['user_id']
        )) {
            $this->error('Unable to notify waiter.', 500);
        }

        $snapshot = $this->kitchenModel->buildKitchenSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        $this->json([
            'ok'       => true,
            'message'  => 'Waiter notified successfully.',
            'snapshot' => $snapshot,
        ]);
    }
}