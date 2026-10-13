<?php

namespace App\Controllers;

use App\Models\IotDashboardModel;
use CodeIgniter\HTTP\ResponseInterface;

class IotDashboard extends Security_Controller
{
    protected IotDashboardModel $iotDashboardModel;

    public function __construct()
    {
        parent::__construct();
        $this->iotDashboardModel = new IotDashboardModel();
    }

    public function index()
    {
        $userId = (int) $this->login_user->id;
        $isAdmin = !empty($this->login_user->is_admin);
        $devices = $this->iotDashboardModel->getDevicesWithLiveStatus($userId, $isAdmin);

        // Always ensure a default IoT schema exists (even with 0 devices) so the app
        // can load get_iot_schema without requiring a manual "Skema Generate" visit.
        $ensured = $this->iotDashboardModel->ensureSchemaForUser($userId, $isAdmin);
        $schema = is_array($ensured['schema'] ?? null) ? $ensured['schema'] : array();
        if (!empty($ensured['created'])) {
            log_message('info', 'IoT schema auto-generated for user ' . $userId);
        }

        if ($schema !== array()) {
            try {
                $builder = new \App\Libraries\IotSduiSchemaBuilder();
                $accessToken = $this->iotDashboardModel->getSavedAccessToken($userId, $isAdmin, $devices);
                $allNodes = array();
                $nodesPayload = $this->iotDashboardModel->getAllNodesStatus($userId, $accessToken);
                if (!empty($nodesPayload['all_nodes']) && is_array($nodesPayload['all_nodes'])) {
                    $allNodes = $nodesPayload['all_nodes'];
                }
                $schema = $builder->applyLiveMetrics($schema, $devices, $this->iotDashboardModel->getPublicApiBaseUrl(), $allNodes, false);
                $schema = $builder->stripHeavyPayload($schema, false, true);
            } catch (\Throwable $e) {
                log_message('error', 'IoT dashboard schema metrics failed: ' . $e->getMessage());
                $schema = $this->iotDashboardModel->getGeneratedSchema($userId);
            }
        }

        $accessToken = $this->iotDashboardModel->getSavedAccessToken($userId, $isAdmin, $devices);

        return $this->template->rander('iot_dashboard/index', array(
            'devices' => $devices,
            'schema' => $schema,
            'accessToken' => $accessToken,
            'userId' => $userId,
            'isAdmin' => $isAdmin,
            'apiBase' => site_url('iot-dashboard/api'),
            'dashboardUrl' => site_url(''),
            'v8ApiPath' => $this->iotDashboardModel->getPublicApiBaseUrl(),
            'v8DeviceApiPath' => $this->iotDashboardModel->getV8DeviceApiBaseUrl(),
            'csrfTokenName' => function_exists('csrf_token') ? csrf_token() : '',
            'csrfHash' => function_exists('csrf_hash') ? csrf_hash() : '',
        ));
    }

    public function api(string $action = ''): ResponseInterface
    {
        $userId = (int) $this->login_user->id;
        $isAdmin = !empty($this->login_user->is_admin);

        if ($action === 'devices') {
            $devices = $this->iotDashboardModel->getDevicesWithLiveStatus($userId, $isAdmin);
            $stats = $this->iotDashboardModel->countDeviceStats($devices);
            $accessToken = $this->iotDashboardModel->getSavedAccessToken($userId, $isAdmin, $devices);
            return $this->jsonResponse(array(
                'success' => true,
                'user_id' => $userId,
                'access_token' => $accessToken,
                'access_token_source' => $accessToken !== '' ? 'efactorproduct' : '',
                'total' => $stats['total'],
                'online' => $stats['online'],
                'offline' => $stats['offline'],
                'node_ids' => $this->iotDashboardModel->getNodeIdsForUser($userId, $isAdmin),
                'devices' => $devices,
            ));
        }

        if ($action === 'products') {
            $name = trim((string) $this->request->getGet('name'));
            if ($name === '') {
                return $this->jsonResponse(array('success' => false, 'message' => 'name is required'), 400);
            }

            $products = $this->iotDashboardModel->getProductsForUserName($userId, $name, $isAdmin);
            return $this->jsonResponse(array(
                'success' => true,
                'user_id' => $userId,
                'name' => $name,
                'node_ids' => array_values(array_filter(array_map(function ($product) {
                    return $product['node_id'] ?? '';
                }, $products))),
                'products' => $products,
                'devices' => $products,
            ));
        }

        if ($action === 'generate-schema') {
            if (strtolower($this->request->getMethod()) !== 'post') {
                return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
            }

            try {
                $payload = $this->request->getJSON(true);
                if (!is_array($payload)) {
                    $payload = $this->request->getPost();
                }
                if (!is_array($payload)) {
                    $payload = array();
                }

                $accessToken = trim((string) ($payload['access_token'] ?? ''));
                $generated = $this->iotDashboardModel->generateAndPersistSchema($userId, $isAdmin, $accessToken);
                $schema = $generated['schema'];
                $accessToken = (string) ($generated['access_token'] ?? $accessToken);
                $saveResult = is_array($generated['save'] ?? null) ? $generated['save'] : array();
                $saved = !empty($saveResult['success']);
                $savedAt = (string) ($schema['savedAt'] ?? date('c'));

                return $this->jsonResponse(array(
                    'success' => $saved,
                    'message' => $saved
                        ? 'IoT schema generated and saved to DB.'
                        : ('Could not save schema.' . (!empty($saveResult['error']) ? ' ' . $saveResult['error'] : '')),
                    'savedAt' => $savedAt,
                    'savedTime' => $schema['savedTime'] ?? date('H:i:s'),
                    'schema_version' => $schema['schemaVersion'] ?? '2.3.2',
                    'user_id' => $userId,
                    'device_count' => count($schema['devices'] ?? array()),
                    'access_token' => $accessToken,
                    'schema' => $schema,
                ), $saved ? 200 : 500);
            } catch (\Throwable $e) {
                log_message('error', 'IoT generate-schema failed: ' . $e->getMessage());
                return $this->jsonResponse(array(
                    'success' => false,
                    'message' => 'Schema generation failed: ' . $e->getMessage(),
                ), 500);
            }
        }

        if ($action === 'access-token') {
            $devices = $this->iotDashboardModel->getDevicesForUser($userId, $isAdmin);
            $token = $this->iotDashboardModel->resolveOnboardingAccessToken($userId, $isAdmin, $devices);
            return $this->jsonResponse(array(
                'success' => true,
                'access_token' => $token,
                'source' => $this->iotDashboardModel->describeAccessTokenSource($userId, $isAdmin, $devices, $token),
                'user_id' => $userId,
            ));
        }

        if ($action === 'check-device') {
            if (strtolower($this->request->getMethod()) !== 'post') {
                return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
            }

            $payload = $this->request->getJSON(true);
            if (!is_array($payload)) {
                $payload = $this->request->getPost();
            }
            if (!is_array($payload)) {
                $payload = array();
            }

            $nodeId = $this->iotDashboardModel->extractNodeIdFromPayload($payload);
            if ($nodeId === '') {
                return $this->jsonResponse(array('success' => false, 'message' => 'node_id is required.'), 400);
            }

            $devices = $this->iotDashboardModel->getDevicesForUser($userId, $isAdmin);
            $accessToken = $this->iotDashboardModel->resolveOnboardingAccessToken($userId, $isAdmin, $devices);
            $payload['node_id'] = $nodeId;
            // Do NOT set $payload['access_token'] — proxyV8DeviceApi strips it anyway
            // and the token is sent via Authorization header in httpPostJson.
            $result = $this->iotDashboardModel->proxyV8DeviceApi('check_device', $payload, $accessToken, $nodeId);
            if (!isset($result['success'])) {
                $result['success'] = !isset($result['status']) || strtolower((string) $result['status']) !== 'failure';
            }
            $result['node_id'] = $nodeId;

            return $this->jsonResponse($result, !empty($result['success']) ? 200 : 422);
        }

        if ($action === 'add-wifi-node') {
            if (strtolower($this->request->getMethod()) !== 'post') {
                return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
            }

            $payload = $this->request->getJSON(true);
            if (!is_array($payload)) {
                $payload = $this->request->getPost();
            }
            if (!is_array($payload)) {
                $payload = array();
            }

            $nodeId = $this->iotDashboardModel->extractNodeIdFromPayload($payload);
            if ($nodeId === '') {
                return $this->jsonResponse(array('success' => false, 'message' => 'node_id is required.'), 400);
            }

            $devices = $this->iotDashboardModel->getDevicesForUser($userId, $isAdmin);
            $accessToken = $this->iotDashboardModel->resolveOnboardingAccessToken($userId, $isAdmin, $devices);
            $payload['node_id'] = $nodeId;
            // Do NOT set $payload['access_token'] — token goes in Authorization header via httpPostJson.
            $result = $this->iotDashboardModel->proxyV8DeviceApi('add_wifi_node', $payload, $accessToken, $nodeId);
            if (!isset($result['success'])) {
                $result['success'] = !isset($result['status']) || strtolower((string) $result['status']) !== 'failure';
            }
            $result['node_id'] = $nodeId;
            // Return refreshed device list so callers can update My Devices in one round-trip.
            if (!empty($result['success'])) {
                try {
                    $refreshed = $this->iotDashboardModel->fetchRemoteIotDevices($userId);
                    $result['devices'] = $refreshed['devices'] ?? array();
                    $result['total']   = $refreshed['total']   ?? 0;
                    $result['online']  = $refreshed['online']  ?? 0;
                    $result['offline'] = $refreshed['offline'] ?? 0;
                } catch (\Throwable $e) {
                    log_message('error', 'add-wifi-node post-refresh failed: ' . $e->getMessage());
                }
            }

            return $this->jsonResponse($result, !empty($result['success']) ? 200 : 422);
        }

        if ($action === 'remove-node') {
            if (strtolower($this->request->getMethod()) !== 'post') {
                return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
            }

            $payload = $this->request->getJSON(true);
            if (!is_array($payload)) {
                $payload = $this->request->getPost();
            }
            if (!is_array($payload)) {
                $payload = array();
            }

            $nodeId = $this->iotDashboardModel->extractNodeIdFromPayload($payload);
            if ($nodeId === '') {
                return $this->jsonResponse(array('success' => false, 'message' => 'node_id is required.'), 400);
            }

            $devices     = $this->iotDashboardModel->getDevicesForUser($userId, $isAdmin);
            $accessToken = $this->iotDashboardModel->resolveOnboardingAccessToken($userId, $isAdmin, $devices);
            $result      = $this->iotDashboardModel->removeDeviceNode($nodeId, $accessToken);

            if (!isset($result['success'])) {
                $result['success'] = !isset($result['status']) || strtolower((string) $result['status']) !== 'failure';
            }
            $result['node_id'] = $nodeId;

            if (!empty($result['success'])) {
                try {
                    $refreshed = $this->iotDashboardModel->fetchRemoteIotDevices($userId);
                    $result['devices'] = $refreshed['devices'] ?? array();
                    $result['total']   = $refreshed['total']   ?? 0;
                    $result['online']  = $refreshed['online']  ?? 0;
                    $result['offline'] = $refreshed['offline'] ?? 0;
                } catch (\Throwable $e) {
                    log_message('error', 'remove-node post-refresh failed: ' . $e->getMessage());
                }
            }

            return $this->jsonResponse($result, !empty($result['success']) ? 200 : 422);
        }

        if ($action === 'device-power-toggle') {
            if (strtolower($this->request->getMethod()) !== 'post') {
                return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
            }

            $payload = $this->request->getJSON(true);
            if (!is_array($payload)) {
                $payload = $this->request->getPost();
            }
            if (!is_array($payload)) {
                $payload = array();
            }

            $result = $this->iotDashboardModel->toggleDevicePower($userId, $payload, $isAdmin);
            return $this->jsonResponse($result, !empty($result['success']) ? 200 : 422);
        }

        if ($action === 'nodes-status') {
            $devices     = $this->iotDashboardModel->getDevicesForUser($userId, $isAdmin);
            // Token must NOT come from a GET query param — resolve it from the session-bound user.
            $accessToken = $this->iotDashboardModel->getSavedAccessToken($userId, $isAdmin, $devices);
            $payload     = $this->iotDashboardModel->getAllNodesStatus($userId, $accessToken);
            return $this->jsonResponse($payload, !empty($payload['success']) ? 200 : 500);
        }

        if ($action === 'schema') {
            // Always ensure schema exists for this session user (new accounts included).
            $ensured = $this->iotDashboardModel->ensureSchemaForUser($userId, $isAdmin);
            $schema = $this->iotDashboardModel->getSchemaWithLiveMetrics($userId, $isAdmin);
            if ($schema === array() && !empty($ensured['schema'])) {
                $schema = $ensured['schema'];
            }
            if (!empty($ensured['created'])) {
                log_message('info', 'IoT schema auto-generated via schema API for user ' . $userId);
            }
            return $this->jsonResponse(array(
                'success' => true,
                'schema' => $schema,
                'created' => !empty($ensured['created']),
            ));
        }

        return $this->jsonResponse(array('success' => false, 'message' => 'Unknown action'), 404);
    }

    private function jsonResponse(array $data, int $status = 200): ResponseInterface
    {
        return $this->response->setStatusCode($status)->setJSON($data);
    }
}
