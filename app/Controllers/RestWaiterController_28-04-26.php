<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\RestWaiterModel;
use Throwable;

require_once APPPATH . 'Models/RestWaiterModel.php';

final class RestWaiterController extends RestBaseController
{
    private const VIEW_PATH = APPPATH . 'Views/restaurant-system/waiter.php';

    /** @var RestWaiterModel */
    protected $waiterModel;

    public function __construct()
    {
        parent::__construct();
        $this->waiterModel = new RestWaiterModel();
    }

    public function index(): void
    {
        $input = $this->requestData();
        $context = $this->resolveContext($input);

        $pageData = $this->waiterModel->getWaiterPageData(
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

                case 'table_detail':
                    $this->tableDetail($input);
                    return;

                case 'ack_service_request':
                    $this->ackServiceRequest($input);
                    return;

                case 'resolve_service_request':
                    $this->resolveServiceRequest($input);
                    return;

                case 'mark_served':
                    $this->markServed($input);
                    return;

                case 'generate_bill':
                    $this->generateBill($input);
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
        return 'rest_waiter_context';
    }

    protected function resolveContext(array $input = array()): array
    {
        $sessionContext = $_SESSION[$this->contextSessionKey()] ?? array();

        $brandId = (string)($input['brand_id'] ?? $_GET['brand_id'] ?? $sessionContext['brand_id'] ?? $this->brandId);
        $branchId = (string)($input['branch_id'] ?? $_GET['branch_id'] ?? $sessionContext['branch_id'] ?? $this->branchId);
        $userId = (string)($input['user_id'] ?? $_GET['user_id'] ?? $sessionContext['user_id'] ?? $this->userId);

        $brandId = $this->waiterModel->normalizeEntityIdOrDefault($brandId, $this->brandId);
        $branchId = $this->waiterModel->normalizeEntityIdOrDefault($branchId, $this->branchId);
        $userId = $this->waiterModel->normalizeEntityIdOrDefault($userId, $this->userId);

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

    protected function dashboardSnapshot(array $input): void
    {
        if ($this->requestMethod() !== 'GET') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $snapshot = $this->waiterModel->buildDashboardSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        if ($snapshot === null) {
            $this->error('Unable to load dashboard snapshot.', 500);
        }

        $this->json(array(
            'ok'       => true,
            'snapshot' => $snapshot,
        ));
    }

    protected function tableDetail(array $input): void
    {
        if ($this->requestMethod() !== 'GET') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $tableId = trim((string)($_GET['table_id'] ?? $input['table_id'] ?? ''));

        if ($tableId === '') {
            $this->error('table_id is required.', 400);
        }

        $detail = $this->waiterModel->getTableModalData(
            $context['brand_id'],
            $context['branch_id'],
            $tableId
        );

        if ($detail === null) {
            $this->error('Table not found.', 404);
        }

        $this->json(array(
            'ok'     => true,
            'detail' => $detail,
        ));
    }

    protected function ackServiceRequest(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $requestId = trim((string)($input['request_id'] ?? ''));

        if ($requestId === '') {
            $this->error('request_id is required.', 400);
        }

        if (!$this->waiterModel->acknowledgeServiceRequest($requestId, $context['user_id'])) {
            $this->error('Unable to accept service request.', 500);
        }

        $snapshot = $this->waiterModel->buildDashboardSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        $this->json(array(
            'ok'       => true,
            'message'  => 'Service request accepted.',
            'snapshot' => $snapshot,
        ));
    }

    protected function resolveServiceRequest(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $requestId = trim((string)($input['request_id'] ?? ''));

        if ($requestId === '') {
            $this->error('request_id is required.', 400);
        }

        if (!$this->waiterModel->resolveServiceRequest($requestId, $context['user_id'])) {
            $this->error('Unable to resolve service request.', 500);
        }

        $snapshot = $this->waiterModel->buildDashboardSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        $this->json(array(
            'ok'       => true,
            'message'  => 'Service request resolved.',
            'snapshot' => $snapshot,
        ));
    }

    protected function markServed(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $orderId = trim((string)($input['order_id'] ?? ''));

        if ($orderId === '') {
            $this->error('order_id is required.', 400);
        }

        if (!$this->waiterModel->markOrderServed($orderId, $context['user_id'])) {
            $this->error('Unable to mark order served.', 500);
        }

        $snapshot = $this->waiterModel->buildDashboardSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        $this->json(array(
            'ok'       => true,
            'message'  => 'Order marked as served.',
            'snapshot' => $snapshot,
        ));
    }

    protected function generateBill(array $input): void
    {
        if ($this->requestMethod() !== 'POST') {
            $this->error('Invalid request method.', 405);
        }

        $context = $this->resolveContext($input);
        $tableId = trim((string)($input['table_id'] ?? ''));

        if ($tableId === '') {
            $this->error('table_id is required.', 400);
        }

        $bill = $this->waiterModel->generateBillForTable(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $tableId
        );

        if ($bill === null) {
            $this->error('Unable to generate bill.', 500);
        }

        $snapshot = $this->waiterModel->buildDashboardSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        );

        $this->json(array(
            'ok'       => true,
            'message'  => 'Bill is ready.',
            'bill'     => $bill,
            'snapshot' => $snapshot,
        ));
    }
}