<?php

namespace App\Controllers;

use App\Models\View_page_model;
use CodeIgniter\Controller;
use Throwable;

class DeviceUiSchemaApi extends Controller
{
    public function show($modelno = '', $nodeId = '')
    {
        try {
            $this->loadApiResponseHelper();

            $modelno = $this->normalizeModelNo($modelno);
            $nodeId = $this->normalizeNodeId($nodeId);
            $validationError = $this->validateModelNo($modelno);

            if ($validationError !== null) {
                return $this->apiErrorResponse('INVALID_MODEL_NO', $validationError, 400);
            }

            if ($nodeId !== '') {
                $nodeValidationError = $this->validateNodeId($nodeId);
                if ($nodeValidationError !== null) {
                    return $this->apiErrorResponse('INVALID_NODE_ID', $nodeValidationError, 400);
                }
            }

            $itemsModel = new View_page_model();
            // Always create/refresh incomplete auto schemas here — never require Items.php or Design Studio.
            if (method_exists($itemsModel, 'autoPersistDeviceUiSchema')) {
                $itemsModel->autoPersistDeviceUiSchema($modelno, $nodeId);
            }
            $schema = $itemsModel->getDeviceUiSchema($modelno, $nodeId);

            if (!is_array($schema)) {
                return $this->apiErrorResponse(
                    'DEVICE_UI_SCHEMA_NOT_FOUND',
                    'No saved UI schema was found for this model.',
                    404,
                    ['model_no' => $modelno]
                );
            }

            $nodeAccess = $this->resolveNodeIdAccess($schema, $modelno, $nodeId);
            if (!$nodeAccess['ok']) {
                return $this->apiErrorResponse(
                    $nodeAccess['code'],
                    $nodeAccess['message'],
                    $nodeAccess['status'],
                    $nodeAccess['details']
                );
            }

            $resolvedNodeId = $nodeAccess['node_id'];
            $schema = $this->applyNodeIdToSchema($schema, $modelno, $resolvedNodeId, $nodeId !== '');
            $schema = $this->applyStoredScanResultToSchema($itemsModel, $schema, $modelno, $resolvedNodeId);

            $json = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json === false) {
                return $this->apiErrorResponse(
                    'SCHEMA_ENCODE_FAILED',
                    'Unable to encode the device UI schema as JSON: ' . json_last_error_msg(),
                    500,
                    ['model_no' => $modelno, 'node_id' => $resolvedNodeId]
                );
            }

            $this->response->removeHeader('Cache-Control');

            return $this->response
                ->setHeader('Cache-Control', 'no-store, max-age=0')
                ->setContentType('application/json', 'utf-8')
                ->setBody($json);
        } catch (Throwable $e) {
            log_message('error', 'Device UI schema API failed: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());

            return $this->apiErrorResponse(
                'INTERNAL_SERVER_ERROR',
                'Unable to load the device UI schema: ' . $e->getMessage(),
                500,
                [
                    'model_no' => (string) $modelno,
                    'node_id' => (string) $nodeId,
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );
        }
    }

    public function store($modelno = '', $nodeId = '')
    {
        try {
            $this->loadApiResponseHelper();

            $modelno = $this->normalizeModelNo($modelno);
            $nodeId = $this->normalizeNodeId($nodeId);
            $validationError = $this->validateModelNo($modelno);

            if ($validationError !== null) {
                return $this->apiErrorResponse('INVALID_MODEL_NO', $validationError, 400);
            }

            if ($nodeId !== '') {
                $nodeValidationError = $this->validateNodeId($nodeId);
                if ($nodeValidationError !== null) {
                    return $this->apiErrorResponse('INVALID_NODE_ID', $nodeValidationError, 400);
                }
            }

            $payload = $this->request->getJSON(true);
            if (!is_array($payload)) {
                $raw = (string) $this->request->getBody();
                $decoded = json_decode($raw, true);
                $payload = is_array($decoded) ? $decoded : [];
                if ($payload === [] && trim($raw) !== '') {
                    $payload = ['value' => trim($raw)];
                }
            }

            if (!is_array($payload) || $payload === []) {
                return $this->apiErrorResponse(
                    'INVALID_SCAN_PAYLOAD',
                    'Request body must include a QR/scan value (value, qr, code, data, or scanResult).',
                    400,
                    ['model_no' => $modelno, 'node_id' => $nodeId]
                );
            }

            $itemsModel = new View_page_model();
            $existingSchema = $itemsModel->getDeviceUiSchema($modelno);

            if (!is_array($existingSchema)) {
                return $this->apiErrorResponse(
                    'DEVICE_UI_SCHEMA_NOT_FOUND',
                    'No saved UI schema was found for this model.',
                    404,
                    ['model_no' => $modelno]
                );
            }

            $nodeAccess = $this->resolveNodeIdAccess($existingSchema, $modelno, $nodeId);
            if (!$nodeAccess['ok']) {
                return $this->apiErrorResponse(
                    $nodeAccess['code'],
                    $nodeAccess['message'],
                    $nodeAccess['status'],
                    $nodeAccess['details']
                );
            }

            $resolvedNodeId = $nodeAccess['node_id'];

            $isFullSchema = isset($payload['schemaType'])
                || isset($payload['documentType'])
                || isset($payload['schemaVersion'])
                || (isset($payload['ui']) && is_array($payload['ui']));

            if ($isFullSchema) {
                $schema = $payload;
                $schema = $this->applyNodeIdToSchema($schema, $modelno, $resolvedNodeId, $nodeId !== '');
                $scan = $this->extractScanResultFromPayload($schema, $resolvedNodeId);
                if ($scan === null) {
                    $scan = $this->extractScanResultFromPayload($payload, $resolvedNodeId);
                }
                if ($scan !== null) {
                    $schema = $this->mergeScanResultIntoSchema($schema, $scan);
                    $itemsModel->saveDeviceUiScanResult($modelno, $resolvedNodeId, $scan);
                }
                $saved = $itemsModel->saveDeviceUiSchema($modelno, $schema);
                if (!$saved) {
                    return $this->apiErrorResponse(
                        'SCHEMA_SAVE_FAILED',
                        'Unable to save the device UI schema.',
                        500,
                        ['model_no' => $modelno, 'node_id' => $resolvedNodeId]
                    );
                }

                return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json', 'utf-8')
                    ->setJSON([
                        'success' => true,
                        'message' => 'Schema saved.',
                        'model_no' => $modelno,
                        'node_id' => $resolvedNodeId,
                        'scanResult' => $scan,
                    ]);
            }

            $scan = $this->extractScanResultFromPayload($payload, $resolvedNodeId);
            if ($scan === null) {
                return $this->apiErrorResponse(
                    'INVALID_SCAN_PAYLOAD',
                    'No QR/scan value found in request body.',
                    400,
                    ['model_no' => $modelno, 'node_id' => $resolvedNodeId]
                );
            }

            $itemsModel->saveDeviceUiScanResult($modelno, $resolvedNodeId, $scan);

            if (is_array($existingSchema)) {
                $schema = $this->applyNodeIdToSchema($existingSchema, $modelno, $resolvedNodeId, $nodeId !== '');
                $schema = $this->mergeScanResultIntoSchema($schema, $scan);
                $itemsModel->saveDeviceUiSchema($modelno, $schema);
            }

            return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json', 'utf-8')
                ->setJSON([
                    'success' => true,
                    'message' => 'Scan result saved.',
                    'model_no' => $modelno,
                    'node_id' => $resolvedNodeId,
                    'scanResult' => $scan,
                ]);
        } catch (Throwable $e) {
            log_message('error', 'Device UI schema store failed: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());

            return $this->apiErrorResponse(
                'INTERNAL_SERVER_ERROR',
                'Unable to save the scan/schema payload: ' . $e->getMessage(),
                500,
                [
                    'model_no' => (string) $modelno,
                    'node_id' => (string) $nodeId,
                ]
            );
        }
    }

    public function parameters($modelno = '')
    {
        $this->loadApiResponseHelper();

        $modelno = $this->normalizeModelNo($modelno);
        $validationError = $this->validateModelNo($modelno);

        if ($validationError !== null) {
            return $this->apiErrorResponse('INVALID_MODEL_NO', $validationError, 400);
        }

        try {
            $itemsModel = new View_page_model();
            $schema = $itemsModel->getDeviceUiSchema($modelno);
        } catch (Throwable $e) {
            log_message('error', 'Device UI parameters API failed: ' . $e->getMessage());

            return $this->apiErrorResponse(
                'INTERNAL_SERVER_ERROR',
                'Unable to load the device UI parameters.',
                500
            );
        }

        if (!is_array($schema)) {
            return $this->apiErrorResponse(
                'DEVICE_UI_SCHEMA_NOT_FOUND',
                'No saved UI schema was found for this model.',
                404,
                ['model_no' => $modelno]
            );
        }

        $isServerDrivenUi = ($schema['documentType'] ?? '') === 'serverDrivenUi';

        $parameters = [];
        if ($isServerDrivenUi && isset($schema['state']['parameters']) && is_array($schema['state']['parameters'])) {
            // Single source of truth: derive control summaries from typed state (no catalog duplicate).
            foreach ($schema['state']['parameters'] as $stateKey => $param) {
                if (!is_array($param)) {
                    continue;
                }
                $meta = isset($param['meta']) && is_array($param['meta']) ? $param['meta'] : [];
                $parameters[] = [
                    'id' => $meta['controlId'] ?? $stateKey,
                    'stateKey' => $stateKey,
                    'label' => $meta['label'] ?? $stateKey,
                    'parameterName' => $meta['parameterName'] ?? ($meta['label'] ?? $stateKey),
                    'parameterKey' => $meta['parameterKey'] ?? $stateKey,
                    'componentType' => $meta['componentType'] ?? null,
                    'pageNo' => $meta['pageNo'] ?? null,
                    'icon' => $meta['icon'] ?? null,
                    'action' => $meta['actionId'] ?? null,
                    'binding' => 'state.parameters.' . $stateKey . '.value',
                    'type' => $param['type'] ?? null,
                    'value' => $param['value'] ?? null,
                    'readOnly' => !empty($param['readOnly']),
                    'enum' => $param['enum'] ?? null,
                    'options' => $param['options'] ?? null,
                    'constraints' => $param['constraints'] ?? null,
                    'commandId' => $meta['commandId'] ?? null,
                    'functionCode' => $meta['functionCode'] ?? null,
                ];
            }
        } elseif (isset($schema['controls']) && is_array($schema['controls'])) {
            $parameters = $schema['controls'];
        } elseif (isset($schema['parameters']) && is_array($schema['parameters'])) {
            $parameters = $schema['parameters'];
        } elseif (isset($schema['catalog']['controls']) && is_array($schema['catalog']['controls'])) {
            $parameters = $schema['catalog']['controls'];
        } elseif (isset($schema['data']['parameterIcons']['parameters']) && is_array($schema['data']['parameterIcons']['parameters'])) {
            $parameters = $schema['data']['parameterIcons']['parameters'];
        } elseif (isset($schema['data']['parameters']) && is_array($schema['data']['parameters'])) {
            $parameters = $schema['data']['parameters'];
        }

        $stateParameters = null;
        if ($isServerDrivenUi && isset($schema['state']['parameters']) && is_array($schema['state']['parameters'])) {
            $stateParameters = $schema['state']['parameters'];
        } elseif (isset($schema['state']['device']['parameters']) && is_array($schema['state']['device']['parameters'])) {
            $stateParameters = $schema['state']['device']['parameters'];
        }

        $pages = null;
        if ($isServerDrivenUi) {
            // Derive pages from routes.device pageView children (single UI tree).
            $pageViewChildren = $schema['routes']['device']['screen']['body']['children'] ?? null;
            if (is_array($pageViewChildren)) {
                foreach ($pageViewChildren as $child) {
                    if (is_array($child) && ($child['type'] ?? '') === 'pageView' && isset($child['children']) && is_array($child['children'])) {
                        $pages = array_map(static function ($page) {
                            return [
                                'id' => $page['id'] ?? null,
                                'pageNo' => $page['pageNo'] ?? null,
                                'title' => $page['title'] ?? null,
                            ];
                        }, $child['children']);
                        break;
                    }
                }
            }
            if ($pages === null) {
                $pages = $schema['catalog']['pages'] ?? null;
            }
        } else {
            $pages = $schema['pages'] ?? null;
        }

        $icons = null;
        if ($isServerDrivenUi) {
            $icons = $schema['assets']['icons'] ?? ($schema['theme']['icons'] ?? null);
        } else {
            $icons = $schema['icons'] ?? ($schema['theme']['icons'] ?? ($schema['data']['parameterIcons'] ?? null));
        }

        $links = isset($schema['links']) && is_array($schema['links']) ? $schema['links'] : [];
        $schemaUrl = get_uri('api/device-ui-schema/' . rawurlencode($modelno));

        $this->response->removeHeader('Cache-Control');

        return $this->response
            ->setHeader('Cache-Control', 'no-store, max-age=0')
            ->setContentType('application/json', 'utf-8')
            ->setJSON([
                'modelNo' => $modelno,
                'schemaVersion' => $schema['schemaVersion'] ?? null,
                'schemaType' => $schema['schemaType'] ?? null,
                'documentType' => $schema['documentType'] ?? null,
                'generatedAt' => $schema['generatedAt'] ?? null,
                'count' => count($parameters),
                'controls' => $parameters,
                'parameters' => $parameters,
                'stateParameters' => $stateParameters,
                'pages' => $pages,
                'state' => $schema['state'] ?? null,
                'settings' => $isServerDrivenUi ? ($schema['theme']['layout'] ?? null) : ($schema['settings'] ?? null),
                'actions' => $isServerDrivenUi ? ($schema['actions'] ?? null) : null,
                'dataSources' => $isServerDrivenUi ? ($schema['dataSources'] ?? null) : null,
                'icons' => $icons,
                'iconLibrary' => $schema['icons']['library'] ?? ($schema['iconLibrary'] ?? ($schema['theme']['iconLibrary'] ?? ($schema['data']['iconLibrary'] ?? null))),
                'componentCatalog' => $schema['components']['catalog'] ?? ($schema['theme']['components']['catalog'] ?? ($schema['data']['componentCatalog'] ?? null)),
                'registerComponents' => $schema['registerComponents'] ?? null,
                'links' => array_merge([
                    'schema' => $schemaUrl,
                    'parameters' => get_uri('api/device-ui-schema/' . rawurlencode($modelno) . '/parameters'),
                    'deviceInfo' => get_uri('api/device-ui-info/' . rawurlencode($modelno)),
                ], $links),
            ]);
    }

    public function info($modelno = '')
    {
        $this->loadApiResponseHelper();

        $modelno = $this->normalizeModelNo($modelno);
        $validationError = $this->validateModelNo($modelno);

        if ($validationError !== null) {
            return $this->apiErrorResponse('INVALID_MODEL_NO', $validationError, 400);
        }

        try {
            $itemsModel = new View_page_model();
            $infoPayload = $itemsModel->getDeviceUiInfo($modelno);
        } catch (Throwable $e) {
            log_message('error', 'Device UI info API failed: ' . $e->getMessage());

            return $this->apiErrorResponse(
                'INTERNAL_SERVER_ERROR',
                'Unable to load the device information.',
                500
            );
        }

        if (!is_array($infoPayload) || empty($infoPayload)) {
            $infoPayload = [
                'info' => [
                    'name' => $modelno,
                    'model' => $modelno,
                    'product' => $modelno,
                    'type' => 'Gateway',
                    'description' => 'Device information is not configured for this model yet.',
                ],
                'node_details' => [[
                    'id' => $modelno,
                    'config' => [
                        'info' => [
                            'name' => $modelno,
                            'model' => $modelno,
                            'type' => 'Gateway',
                        ],
                        'devices' => [],
                        'node_id' => $modelno,
                    ],
                    'role' => 'primary',
                ]],
                'nodes' => $modelno,
            ];
        }

        $info = isset($infoPayload['info']) && is_array($infoPayload['info'])
            ? $infoPayload['info']
            : $infoPayload;
        $nodeDetails = isset($infoPayload['node_details']) && is_array($infoPayload['node_details'])
            ? $infoPayload['node_details']
            : [[
                'id' => $modelno,
                'config' => [
                    'info' => $info,
                    'devices' => $infoPayload['deviceConfig']['devices'] ?? [],
                    'node_id' => $modelno,
                ],
                'role' => 'primary',
            ]];

        $this->response->removeHeader('Cache-Control');

        return $this->response
            ->setHeader('Cache-Control', 'no-store, max-age=0')
            ->setContentType('application/json', 'utf-8')
            ->setJSON([
                'node_details' => $nodeDetails,
                'nodes' => $infoPayload['nodes'] ?? $modelno,
                'info' => $info,
                'deviceConfig' => $infoPayload['deviceConfig'] ?? ($nodeDetails[0]['config'] ?? null),
            ]);
    }

    private function loadApiResponseHelper(): void
    {
        helper('api_response');

        if (!function_exists('api_error_response') && is_file(APPPATH . 'Helpers/api_response_helper.php')) {
            require_once APPPATH . 'Helpers/api_response_helper.php';
        }
    }

    private function apiErrorResponse(string $code, string $message, int $status = 400, array $details = [])
    {
        if (function_exists('api_error_response')) {
            return \api_error_response($code, $message, $status, $details);
        }

        $payload = [
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];

        if (!empty($details)) {
            $payload['error']['details'] = $details;
        }

        return $this->response
            ->setStatusCode($status)
            ->setContentType('application/json', 'utf-8')
            ->setJSON($payload);
    }

    private function normalizeModelNo($modelno): string
    {
        return trim((string) $modelno);
    }

    private function normalizeNodeId($nodeId): string
    {
        return trim((string) $nodeId);
    }

    private function validateModelNo(string $modelno): ?string
    {
        if ($modelno === '') {
            return 'Model number is required.';
        }

        if (strlen($modelno) > 191) {
            return 'Model number is too long.';
        }

        if (preg_match('/[\x00-\x1F\x7F]/', $modelno)) {
            return 'Model number contains invalid control characters.';
        }

        return null;
    }

    private function validateNodeId(string $nodeId): ?string
    {
        if ($nodeId === '') {
            return null;
        }

        if (strlen($nodeId) > 191) {
            return 'Node ID is too long.';
        }

        if (preg_match('/[\x00-\x1F\x7F]/', $nodeId)) {
            return 'Node ID contains invalid control characters.';
        }

        if (strcasecmp($nodeId, 'parameters') === 0) {
            return 'Node ID cannot be "parameters".';
        }

        return null;
    }

    private function resolveNodeIdAccess(array $schema, string $modelno, string $requestedNodeId): array
    {
        $authoritative = $this->extractSchemaNodeId($schema, $modelno);
        $requiresExplicitNode = $authoritative !== '' && strcasecmp($authoritative, $modelno) !== 0;
        $schemaApi = site_url('api/device-ui-schema/' . rawurlencode($modelno)
            . ($requiresExplicitNode ? '/' . rawurlencode($authoritative) : ''));

        if ($requiresExplicitNode) {
            if ($requestedNodeId === '') {
                return [
                    'ok' => false,
                    'code' => 'NODE_ID_REQUIRED',
                    'message' => 'This model requires the current node_id in the API URL.',
                    'status' => 404,
                    'details' => [
                        'model_no' => $modelno,
                        'active_node_id' => $authoritative,
                        'schema_api' => $schemaApi,
                    ],
                ];
            }

            // Issue #1: do NOT reject a node_id just because a different device's node
            // happens to be stored in the schema. The schema template is per-model — any
            // valid node_id for this model is acceptable. Always use the requested one.
            return [
                'ok' => true,
                'node_id' => $requestedNodeId,
            ];
        }

        if ($requestedNodeId !== ''
            && strcasecmp($requestedNodeId, $authoritative) !== 0
            && strcasecmp($requestedNodeId, $modelno) !== 0) {
            return [
                'ok' => false,
                'code' => 'NODE_ID_MISMATCH',
                'message' => 'The requested node_id is not valid for this model.',
                'status' => 404,
                'details' => [
                    'model_no' => $modelno,
                    'requested_node_id' => $requestedNodeId,
                    'active_node_id' => $authoritative,
                    'schema_api' => $schemaApi,
                ],
            ];
        }

        // Issue #1: when the caller explicitly supplies a node_id in the URL,
        // always honour it — never override with the DB-stored node from another device.
        // This prevents schema for device A from embedding device B's node_id.
        $resolvedNodeId = $requestedNodeId !== '' ? $requestedNodeId : $authoritative;

        return [
            'ok' => true,
            'node_id' => $resolvedNodeId,
        ];
    }

    private function extractSchemaNodeId(array $schema, string $modelno): string
    {
        $candidate = '';
        if (!empty($schema['node_id']['id'])) {
            $candidate = trim((string) $schema['node_id']['id']);
        } elseif (!empty($schema['node_id']) && is_string($schema['node_id'])) {
            $candidate = trim((string) $schema['node_id']);
        } elseif (!empty($schema['deviceConfig']['node_id'])) {
            $candidate = trim((string) $schema['deviceConfig']['node_id']);
        } elseif (!empty($schema['nodes']) && is_string($schema['nodes'])) {
            $candidate = trim((string) $schema['nodes']);
        } elseif (!empty($schema['node_details'][0]['id'])) {
            $candidate = trim((string) $schema['node_details'][0]['id']);
        } elseif (!empty($schema['meta']['app']['nodeId'])) {
            $candidate = trim((string) $schema['meta']['app']['nodeId']);
        }

        if ($candidate !== '' && strcasecmp($candidate, $modelno) !== 0) {
            return $candidate;
        }

        $resolved = $this->resolveApiNodeIdForModel($modelno);
        return $resolved !== '' ? $resolved : ($candidate !== '' ? $candidate : $modelno);
    }

    private function resolveApiNodeIdForModel(string $modelno): string
    {
        $modelno = trim($modelno);
        if ($modelno === '') {
            return '';
        }

        $db = db_connect();
        $apiNode = $db->table('product_api')
            ->select('node_id')
            ->where('model_name', $modelno)
            ->where('node_id IS NOT NULL', null, false)
            ->where('node_id !=', '')
            ->orderBy('selected', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        $resolved = trim((string) ($apiNode['node_id'] ?? ''));
        if ($resolved !== '' && strcasecmp($resolved, $modelno) !== 0) {
            return $resolved;
        }

        if ($db->tableExists('efactorproduct')) {
            $productNode = $db->table('efactorproduct')
                ->select('node_id')
                ->groupStart()
                    ->where('model_name', $modelno)
                    ->orWhere('model_number', $modelno)
                ->groupEnd()
                ->where('node_id IS NOT NULL', null, false)
                ->where('node_id !=', '')
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            $resolved = trim((string) ($productNode['node_id'] ?? ''));
            if ($resolved !== '' && strcasecmp($resolved, $modelno) !== 0) {
                return $resolved;
            }
        }

        return '';
    }

    private function applyNodeIdToSchema(array $schema, string $modelno, string $nodeId, bool $fromUrl): array
    {
        $resolved = $nodeId !== '' ? $nodeId : $modelno;
        $schemaUrl = site_url('api/device-ui-schema/' . rawurlencode($modelno) . (($fromUrl || ($resolved !== '' && $resolved !== $modelno)) ? '/' . rawurlencode($resolved) : ''));
        if (!is_string($schemaUrl) || $schemaUrl === '') {
            $schemaUrl = '/index.php/api/device-ui-schema/' . rawurlencode($modelno)
                . (($fromUrl || ($resolved !== '' && $resolved !== $modelno)) ? '/' . rawurlencode($resolved) : '');
        }

        $schema['node_id'] = [
            'id' => $resolved,
            'modelNo' => $modelno,
            'model_id' => $modelno,
            'value' => $resolved,
            'source' => $fromUrl ? 'url' : 'schema',
            'binding' => 'node_id.id',
        ];
        $schema['nodes'] = $resolved;

        if (!isset($schema['meta']) || !is_array($schema['meta'])) {
            $schema['meta'] = [];
        }
        if (!isset($schema['meta']['app']) || !is_array($schema['meta']['app'])) {
            $schema['meta']['app'] = [];
        }
        $schema['meta']['app']['modelNo'] = $modelno;
        $schema['meta']['app']['nodeId'] = $resolved;
        $schema['meta']['app']['model_id'] = $modelno;
        if (!isset($schema['meta']['links']) || !is_array($schema['meta']['links'])) {
            $schema['meta']['links'] = [];
        }
        $schema['meta']['links']['schema'] = $schemaUrl;

        if (isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])) {
            $schema['deviceConfig']['node_id'] = $resolved;
            if (isset($schema['deviceConfig']['devices'][0]) && is_array($schema['deviceConfig']['devices'][0])) {
                $attrs = isset($schema['deviceConfig']['devices'][0]['attributes']) && is_array($schema['deviceConfig']['devices'][0]['attributes'])
                    ? $schema['deviceConfig']['devices'][0]['attributes']
                    : [];
                $found = false;
                foreach ($attrs as $index => $attribute) {
                    if (!is_array($attribute)) {
                        continue;
                    }
                    $name = strtolower((string) ($attribute['name'] ?? ''));
                    if ($name === 'device id' || $name === 'device_id' || $name === 'node_id') {
                        $attrs[$index]['value'] = $resolved;
                        $found = true;
                    }
                }
                if (!$found) {
                    $attrs[] = ['name' => 'Device Id', 'value' => $resolved];
                }
                $schema['deviceConfig']['devices'][0]['attributes'] = $attrs;
                $schema['deviceConfig']['devices'][0]['Device Id'] = $resolved;
            }
        }

        if (isset($schema['node_details']) && is_array($schema['node_details'])) {
            foreach ($schema['node_details'] as $index => $detail) {
                if (!is_array($detail)) {
                    continue;
                }
                $schema['node_details'][$index]['id'] = $resolved;
                if (!isset($schema['node_details'][$index]['config']) || !is_array($schema['node_details'][$index]['config'])) {
                    $schema['node_details'][$index]['config'] = [];
                }
                $schema['node_details'][$index]['config']['node_id'] = $resolved;
                if (isset($schema['node_details'][$index]['config']['devices'][0]) && is_array($schema['node_details'][$index]['config']['devices'][0])) {
                    $schema['node_details'][$index]['config']['devices'][0]['Device Id'] = $resolved;
                    $attrs = isset($schema['node_details'][$index]['config']['devices'][0]['attributes']) && is_array($schema['node_details'][$index]['config']['devices'][0]['attributes'])
                        ? $schema['node_details'][$index]['config']['devices'][0]['attributes']
                        : [];
                    $found = false;
                    foreach ($attrs as $attrIndex => $attribute) {
                        if (!is_array($attribute)) {
                            continue;
                        }
                        $name = strtolower(trim((string) ($attribute['name'] ?? '')));
                        if ($name === 'device id' || $name === 'device_id' || $name === 'node_id') {
                            $attrs[$attrIndex]['value'] = $resolved;
                            $found = true;
                        }
                    }
                    if (!$found) {
                        $attrs[] = ['name' => 'Device Id', 'value' => $resolved];
                    }
                    $schema['node_details'][$index]['config']['devices'][0]['attributes'] = $attrs;
                }
            }
        }

        if (isset($schema['deviceState']) && is_array($schema['deviceState'])) {
            if (isset($schema['deviceState']['config']) && is_array($schema['deviceState']['config'])) {
                $schema['deviceState']['config']['node_id'] = $resolved;
            }
            $schema['deviceState']['node_id'] = $resolved;
        }

        if (isset($schema['mqtt']) && is_array($schema['mqtt'])) {
            if (!isset($schema['mqtt']['topics']) || !is_array($schema['mqtt']['topics'])) {
                $schema['mqtt']['topics'] = [];
            }
            $schema['mqtt']['topics']['command'] = 'device/' . $resolved . '/command';
            $schema['mqtt']['topics']['state'] = 'device/' . $resolved . '/state';
            $schema['mqtt']['topics']['telemetry'] = 'device/' . $resolved . '/telemetry';
            $schema['mqtt']['topics']['presence'] = 'device/' . $resolved . '/presence';
            $schema['mqtt']['clientIdPattern'] = 'efactor_' . $resolved . '_{clientId}';
        }

        if (isset($schema['websocket']['subscriptions']) && is_array($schema['websocket']['subscriptions'])) {
            foreach ($schema['websocket']['subscriptions'] as $index => $subscription) {
                if (!is_array($subscription) || empty($subscription['topic'])) {
                    continue;
                }
                $schema['websocket']['subscriptions'][$index]['topic'] = preg_replace(
                    '#^device/[^/]+/#',
                    'device/' . $resolved . '/',
                    (string) $subscription['topic']
                );
            }
        }

        return $schema;
    }

    private function applyStoredScanResultToSchema(View_page_model $itemsModel, array $schema, string $modelno, string $nodeId): array
    {
        $stored = $itemsModel->getDeviceUiScanResult($modelno, $nodeId);
        if (!is_array($stored) || empty($stored['value'])) {
            return $schema;
        }

        return $this->mergeScanResultIntoSchema($schema, $stored);
    }

    private function mergeScanResultIntoSchema(array $schema, array $scan): array
    {
        if (!isset($schema['state']) || !is_array($schema['state'])) {
            $schema['state'] = [];
        }

        $schema['state']['scanResult'] = [
            'value' => $scan['value'] ?? null,
            'format' => $scan['format'] ?? ($scan['type'] ?? 'qr'),
            'type' => $scan['type'] ?? ($scan['format'] ?? 'qr'),
            'scannedAt' => $scan['scannedAt'] ?? ($scan['scanned_at'] ?? null),
            'source' => $scan['source'] ?? 'app',
            'nodeId' => $scan['nodeId'] ?? ($scan['node_id'] ?? null),
        ];
        $schema['scanResult'] = $schema['state']['scanResult'];

        if (!isset($schema['extensions']) || !is_array($schema['extensions'])) {
            $schema['extensions'] = [];
        }
        $schema['extensions']['scanResult'] = $schema['state']['scanResult'];

        return $schema;
    }

    private function extractScanResultFromPayload(array $payload, string $nodeId): ?array
    {
        $candidates = [];

        if (isset($payload['state']['scanResult']) && is_array($payload['state']['scanResult'])) {
            $candidates[] = $payload['state']['scanResult'];
        }
        if (isset($payload['scanResult']) && is_array($payload['scanResult'])) {
            $candidates[] = $payload['scanResult'];
        }
        if (isset($payload['extensions']['scanResult']) && is_array($payload['extensions']['scanResult'])) {
            $candidates[] = $payload['extensions']['scanResult'];
        }
        if (isset($payload['data']) && is_array($payload['data'])) {
            $candidates[] = $payload['data'];
        }
        $candidates[] = $payload;

        foreach ($candidates as $candidate) {
            if (!is_array($candidate)) {
                continue;
            }
            $value = $this->firstPresentValue([
                $candidate['value'] ?? null,
                $candidate['qr'] ?? null,
                $candidate['qrValue'] ?? null,
                $candidate['qr_value'] ?? null,
                $candidate['code'] ?? null,
                $candidate['data'] ?? null,
                $candidate['result'] ?? null,
                $candidate['scan'] ?? null,
                $candidate['text'] ?? null,
            ]);
            if ($value === null || $value === '') {
                continue;
            }

            return [
                'value' => (string) $value,
                'format' => (string) $this->firstPresentValue([
                    $candidate['format'] ?? null,
                    $candidate['type'] ?? null,
                    'qr',
                ]),
                'type' => (string) $this->firstPresentValue([
                    $candidate['type'] ?? null,
                    $candidate['format'] ?? null,
                    'qr',
                ]),
                'scannedAt' => (string) $this->firstPresentValue([
                    $candidate['scannedAt'] ?? null,
                    $candidate['scanned_at'] ?? null,
                    $candidate['updatedAt'] ?? null,
                    date('c'),
                ]),
                'source' => (string) $this->firstPresentValue([
                    $candidate['source'] ?? null,
                    'app',
                ]),
                'nodeId' => $nodeId !== '' ? $nodeId : (string) $this->firstPresentValue([
                    $candidate['nodeId'] ?? null,
                    $candidate['node_id'] ?? null,
                    '',
                ]),
            ];
        }

        return null;
    }

    private function firstPresentValue(array $values)
    {
        foreach ($values as $value) {
            if ($value === null) {
                continue;
            }
            if (is_array($value) || is_object($value)) {
                continue;
            }
            $text = trim((string) $value);
            if ($text !== '') {
                return $text;
            }
        }

        return null;
    }
}
