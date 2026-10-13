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
        try {
            $input = $this->requestData();
            $context = $this->resolveContext($input);

            $pageData = $this->waiterModel->getWaiterPageData(
                $context['brand_id'],
                $context['branch_id'],
                $context['user_id']
            );

            $this->renderView(self::VIEW_PATH, $pageData);
        } catch (Throwable $e) {
            $this->renderView(self::VIEW_PATH, array(
                'brandId'                  => $this->brandId,
                'branchId'                 => $this->branchId,
                'userId'                   => $this->userId,
                'brand'                    => array(),
                'context'                  => array(),
                'permissions'              => array(),
                'tables'                   => array(),
                'serviceRequests'          => array(),
                'pickupOrders'             => array(),
                'summary'                  => array(),
                'quickActions'             => array(),
                'shiftMetrics'             => array(),
                'notifications'            => array(),
                'pendingBills'             => array(),
                'responseMetrics'          => array(),
                'userSettings'             => array(),
                'defaultNotificationSound' => 'RestaurantAssets/AlertSounds/Sound1.mpeg',
                'pageError'                => $e->getMessage(),
            ));
        }
    }

    public function api(): void
    {
        try {
            $input = $this->requestData();
            $action = trim((string)($_GET['action'] ?? $input['action'] ?? ''));

            if ($action === '') {
                $this->respondError('Action is required.', 400);
            }

            switch ($action) {
                case 'dashboard_snapshot':
                    $this->dashboardSnapshot($input);
                    return;

                case 'section_data':
                    $this->sectionData($input);
                    return;

                case 'table_detail':
                    $this->tableDetail($input);
                    return;

                case 'update_table_status':
                    $this->updateTableStatus($input);
                    return;

                case 'notifications_snapshot':
                case 'get_notifications':
                    $this->notificationsSnapshot($input);
                    return;

                case 'mark_notification_read':
                    $this->markNotificationRead($input);
                    return;

                case 'mark_all_notifications_read':
                    $this->markAllNotificationsRead($input);
                    return;

                case 'delete_notification':
                    $this->deleteNotification($input);
                    return;

                case 'clear_notifications':
                    $this->clearNotifications($input);
                    return;

                case 'waiter_events':
                    $this->waiterEvents($input);
                    return;

                case 'get_waiter_metrics':
                    $this->waiterMetrics($input);
                    return;

                case 'get_user_settings':
                    $this->userSettings($input);
                    return;

                case 'update_user_settings':
                    $this->updateUserSettings($input);
                    return;

                case 'accept_request':
                case 'ack_service_request':
                    $this->acceptRequest($input);
                    return;

                case 'resolve_request':
                case 'resolve_service_request':
                    $this->resolveRequest($input);
                    return;

                case 'mark_served':
                    $this->markServed($input);
                    return;

                case 'generate_bill':
                    $this->generateBill($input);
                    return;

                case 'collect_payment':
                    $this->collectPayment($input);
                    return;

                default:
                    $this->respondError('Unknown action.', 404);
                    return;
            }
        } catch (Throwable $e) {
            $status = (int)$e->getCode();
            if ($status < 400 || $status > 599) {
                $status = 500;
            }

            $this->respondError($e->getMessage(), $status);
        }
    }

    protected function respondSuccess(array $data = array(), int $status = 200): void
    {
        $this->json(array(
            'success' => true,
            'data'    => $data,
            'error'   => null,
        ), $status);
    }

    protected function respondError(string $message, int $status = 400, ?array $data = null): void
    {
        $this->json(array(
            'success' => false,
            'data'    => $data,
            'error'   => $message,
        ), $status);
    }

    protected function requestMethod(): string
    {
        return strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    }

    protected function requireMethod(string $expected): void
    {
        if ($this->requestMethod() !== strtoupper($expected)) {
            $this->respondError('Invalid request method.', 405);
        }
    }

    protected function contextSessionKey(): string
    {
        return 'rest_waiter_context';
    }

    protected function resolveContext(array $input = array()): array
    {
        $sessionContext = $_SESSION[$this->contextSessionKey()] ?? array();
        $scope = strtolower(trim((string)($input['scope'] ?? $_GET['scope'] ?? $sessionContext['scope'] ?? 'assigned')));
        if ($scope !== 'all') {
            $scope = 'assigned';
        }

        $brandSeed = (string)($this->brandId ?: ($sessionContext['brand_id'] ?? $input['brand_id'] ?? $_GET['brand_id'] ?? ''));
        $branchSeed = (string)($this->branchId ?: ($sessionContext['branch_id'] ?? $input['branch_id'] ?? $_GET['branch_id'] ?? ''));
        $userSeed = (string)($this->userId ?: ($sessionContext['user_id'] ?? $input['user_id'] ?? $_GET['user_id'] ?? ''));

        $brandId = $this->waiterModel->normalizeEntityIdOrDefault($brandSeed, (string)$this->brandId);
        $branchId = $this->waiterModel->normalizeEntityIdOrDefault($branchSeed, (string)$this->branchId);
        $userId = $this->waiterModel->normalizeEntityIdOrDefault($userSeed, (string)$this->userId);

        $context = array(
            'brand_id'  => $brandId,
            'branch_id' => $branchId,
            'user_id'   => $userId,
            'user_role' => strtolower(trim((string)$this->userRole)),
            'scope'     => $scope,
        );

        $_SESSION[$this->contextSessionKey()] = $context;
        $_SESSION['rest_brand_id'] = $brandId;
        $_SESSION['rest_branch_id'] = $branchId;
        $_SESSION['rest_user_id'] = $userId;
        $_SESSION['rest_user_role'] = $context['user_role'];

        return $context;
    }

    protected function buildRefreshPayload(array $context, bool $includeSettings = false): array
    {
        $this->waiterModel->syncPaidTableAvailability(
            $context['brand_id'],
            $context['branch_id']
        );

        $snapshot = $this->waiterModel->buildDashboardSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $context['scope']
        );

        if ($snapshot === null) {
            throw new \RuntimeException('Unable to load dashboard snapshot.', 500);
        }

        $notifications = $this->waiterModel->getNotificationSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $context['scope']
        );

        if ($notifications === null) {
            throw new \RuntimeException('Unable to load notifications.', 500);
        }

        $metrics = $this->waiterModel->getWaiterMetrics(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $context['scope']
        );

        if ($metrics === null) {
            throw new \RuntimeException('Unable to load waiter metrics.', 500);
        }

        $payload = array(
            'snapshot'      => $snapshot,
            'notifications' => $notifications,
            'metrics'       => $metrics,
        );

        if ($includeSettings) {
            $payload['settings'] = $this->waiterModel->getUserSettings(
                $context['brand_id'],
                $context['branch_id'],
                $context['user_id']
            );
        }

        return $payload;
    }

    protected function dashboardSnapshot(array $input): void
    {
        $this->requireMethod('GET');
        $context = $this->resolveContext($input);

        $snapshot = $this->waiterModel->buildDashboardSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $context['scope']
        );

        if ($snapshot === null) {
            $this->respondError('Unable to load dashboard snapshot.', 500);
        }

        $metrics = $this->waiterModel->getWaiterMetrics(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $context['scope']
        );

        $this->respondSuccess(array(
            'snapshot' => $snapshot,
            'metrics'  => $metrics ?? array(),
        ));
    }

    protected function sectionData(array $input): void
    {
        $this->requireMethod('GET');
        $context = $this->resolveContext($input);
        $section = trim((string)($_GET['section'] ?? $input['section'] ?? ''));

        if ($section === '') {
            $this->respondError('section is required.', 400);
        }

        $data = $this->waiterModel->getSectionData(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $section,
            $context['scope']
        );

        if ($data === null) {
            $this->respondError('Unable to load section data.', 404);
        }

        $this->respondSuccess(array('section' => $data));
    }

    protected function tableDetail(array $input): void
    {
        $this->requireMethod('GET');
        $context = $this->resolveContext($input);
        $tableId = trim((string)($_GET['table_id'] ?? $input['table_id'] ?? ''));

        if ($tableId === '') {
            $this->respondError('table_id is required.', 400);
        }

        $this->waiterModel->syncPaidTableAvailability(
            $context['brand_id'],
            $context['branch_id'],
            [$tableId]
        );

        $detail = $this->waiterModel->getTableModalData(
            $context['brand_id'],
            $context['branch_id'],
            $tableId,
            $context['user_id'],
            $context['scope']
        );

        if ($detail === null) {
            $this->respondError('Table not found.', 404);
        }

        $this->respondSuccess(array('detail' => $detail));
    }

    protected function updateTableStatus(array $input): void
    {
        $this->requireMethod('POST');
        $context = $this->resolveContext($input);
        $tableId = trim((string)($input['table_id'] ?? ''));
        $status = trim((string)($input['status'] ?? ''));

        if ($tableId === '' || $status === '') {
            $this->respondError('table_id and status are required.', 400);
        }

        if (!$this->waiterModel->updateTableStatus(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $tableId,
            $status,
            $context['scope']
        )) {
            $this->respondError('Unable to update table status.', 403);
        }

        $payload = $this->buildRefreshPayload($context);
        $payload['detail'] = $this->waiterModel->getTableModalData(
            $context['brand_id'],
            $context['branch_id'],
            $tableId,
            $context['user_id'],
            $context['scope']
        );

        $this->respondSuccess($payload);
    }

    protected function notificationsSnapshot(array $input): void
    {
        $this->requireMethod('GET');
        $context = $this->resolveContext($input);

        $snapshot = $this->waiterModel->getNotificationSnapshot(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $context['scope']
        );

        if ($snapshot === null) {
            $this->respondError('Unable to load notifications.', 500);
        }

        $this->respondSuccess($snapshot);
    }

    protected function markNotificationRead(array $input): void
    {
        $this->requireMethod('POST');
        $context = $this->resolveContext($input);
        $notificationId = trim((string)($input['notification_id'] ?? ''));

        if ($notificationId === '') {
            $this->respondError('notification_id is required.', 400);
        }

        if (!$this->waiterModel->markNotificationRead(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $notificationId,
            $context['scope']
        )) {
            $this->respondError('Unable to mark notification as read.', 403);
        }

        $this->respondSuccess($this->buildRefreshPayload($context));
    }

    protected function markAllNotificationsRead(array $input): void
    {
        $this->requireMethod('POST');
        $context = $this->resolveContext($input);

        if (!$this->waiterModel->markAllNotificationsRead(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $context['scope']
        )) {
            $this->respondError('Unable to mark all notifications as read.', 403);
        }

        $this->respondSuccess($this->buildRefreshPayload($context));
    }

    protected function deleteNotification(array $input): void
    {
        $this->requireMethod('POST');
        $context = $this->resolveContext($input);
        $notificationId = trim((string)($input['notification_id'] ?? ''));

        if ($notificationId === '') {
            $this->respondError('notification_id is required.', 400);
        }

        if (!$this->waiterModel->deleteNotification(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $notificationId,
            $context['scope']
        )) {
            $this->respondError('Unable to delete notification.', 403);
        }

        $this->respondSuccess($this->buildRefreshPayload($context));
    }

    protected function clearNotifications(array $input): void
    {
        $this->requireMethod('POST');
        $context = $this->resolveContext($input);

        if (!$this->waiterModel->clearNotifications(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $context['scope']
        )) {
            $this->respondError('Unable to clear notifications.', 403);
        }

        $this->respondSuccess($this->buildRefreshPayload($context));
    }

    protected function waiterEvents(array $input): void
    {
        $this->requireMethod('GET');
        $context = $this->resolveContext($input);
        $afterId = (int)($_GET['after_id'] ?? $input['after_id'] ?? 0);
        $bootstrap = !empty($_GET['bootstrap']) || !empty($input['bootstrap']);

        $events = $this->waiterModel->getWaiterEvents(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $afterId,
            20,
            $context['scope'],
            $bootstrap
        );

        if ($events === null) {
            $this->respondError('Unable to load waiter events.', 500);
        }

        $this->respondSuccess($events);
    }

    protected function waiterMetrics(array $input): void
    {
        $this->requireMethod('GET');
        $context = $this->resolveContext($input);

        $metrics = $this->waiterModel->getWaiterMetrics(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $context['scope']
        );

        if ($metrics === null) {
            $this->respondError('Unable to load waiter metrics.', 500);
        }

        $this->respondSuccess($metrics);
    }

    protected function userSettings(array $input): void
    {
        $this->requireMethod('GET');
        $context = $this->resolveContext($input);

        $this->respondSuccess($this->waiterModel->getUserSettings(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id']
        ));
    }

    protected function updateUserSettings(array $input): void
    {
        $this->requireMethod('POST');
        $context = $this->resolveContext($input);

        $payload = array();
        foreach (array('theme', 'lite_notification_sound', 'sound_alerts_enabled') as $field) {
            if (array_key_exists($field, $input)) {
                $payload[$field] = $input[$field];
            }
        }

        if ($payload === array()) {
            $this->respondError('At least one settings field is required.', 400);
        }

        $settings = $this->waiterModel->updateUserSettings(
            $context['brand_id'],
            $context['branch_id'],
            $context['user_id'],
            $payload
        );

        $this->respondSuccess(array(
            'settings' => $settings,
        ));
    }

    protected function acceptRequest(array $input): void
    {
        $this->requireMethod('POST');
        $context = $this->resolveContext($input);
        $requestId = trim((string)($input['request_id'] ?? ''));

        if ($requestId === '') {
            $this->respondError('request_id is required.', 400);
        }

        if (!$this->waiterModel->acknowledgeServiceRequest(
            $context['brand_id'],
            $context['branch_id'],
            $requestId,
            $context['user_id'],
            $context['scope']
        )) {
            $this->respondError('Unable to accept service request.', 403);
        }

        $this->respondSuccess($this->buildRefreshPayload($context));
    }

    protected function resolveRequest(array $input): void
    {
        $this->requireMethod('POST');
        $context = $this->resolveContext($input);
        $requestId = trim((string)($input['request_id'] ?? ''));

        if ($requestId === '') {
            $this->respondError('request_id is required.', 400);
        }

        if (!$this->waiterModel->resolveServiceRequest(
            $context['brand_id'],
            $context['branch_id'],
            $requestId,
            $context['user_id'],
            $context['scope']
        )) {
            $this->respondError('Unable to resolve service request.', 403);
        }

        $this->respondSuccess($this->buildRefreshPayload($context));
    }

    protected function markServed(array $input): void
    {
        $this->requireMethod('POST');
        $context = $this->resolveContext($input);
        $orderId = trim((string)($input['order_id'] ?? ''));

        if ($orderId === '') {
            $this->respondError('order_id is required.', 400);
        }

        if (!$this->waiterModel->markOrderServed(
            $context['brand_id'],
            $context['branch_id'],
            $orderId,
            $context['user_id'],
            $context['scope']
        )) {
            $this->respondError('Unable to mark order as served.', 403);
        }

        $this->respondSuccess($this->buildRefreshPayload($context));
    }

    protected function generateBill(array $input): void
    {
        $this->requireMethod('POST');
        $this->respondError('Only manager can approve or generate bills.', 403);
    }

    protected function collectPayment(array $input): void
    {
        $this->requireMethod('POST');
        $this->respondError('Only manager can update bill payment status.', 403);
    }
}
