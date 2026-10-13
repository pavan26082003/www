<?php

namespace App\Controllers;

use App\Models\IotDashboardModel;
use App\Models\View_page_model;

class View_page_controller extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("order");

        if (empty($this->View_page_model)) {
            $class = '\\App\\Models\\View_page_model';
            if (!class_exists($class, false)) {
                foreach (array('View_page_model.php', 'view_page_model.php') as $fileName) {
                    $modelFile = APPPATH . 'Models/' . $fileName;
                    if (is_file($modelFile)) {
                        require_once $modelFile;
                        break;
                    }
                }
            }

            if (class_exists($class, false)) {
                $this->View_page_model = new $class();
            }
        }
    }

    private function currentBuilderUserId()
    {
        if (!empty($this->login_user) && !empty($this->login_user->id)) {
            return $this->login_user->id;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['user_id'] ?? 0;
    }

    private function resolveBuilderModelNo($itemId, $requested = '')
    {
        $requested = trim((string) $requested);
        if ($this->View_page_model && method_exists($this->View_page_model, 'resolveLinkingModelNo')) {
            return $this->View_page_model->resolveLinkingModelNo((int) $itemId, $requested);
        }

        if ($requested !== '') {
            return $requested;
        }

        return $this->View_page_model ? $this->View_page_model->getModelNameForItem((int) $itemId) : '';
    }

    private function isStatusOperationPageName($pageName)
    {
        $name = strtolower(preg_replace('/\s+/', '', (string) $pageName));

        return $name === 'status.operation' || $name === 'status';
    }

    private function keepFirstPageLinkingParameters(array $uiRows)
    {
        $claimed = [];
        $assign = function (&$row) use (&$claimed) {
            if (!is_array($row)) {
                return;
            }

            $kept = [];
            $parameters = !empty($row['parameters']) && is_array($row['parameters']) ? $row['parameters'] : [];
            foreach ($parameters as $parameter) {
                $parameter = trim((string) $parameter);
                if ($parameter === '') {
                    continue;
                }

                $key = strtolower($parameter);
                if (isset($claimed[$key])) {
                    continue;
                }

                $claimed[$key] = true;
                $kept[] = $parameter;
            }

            $row['parameters'] = $kept;
        };

        foreach ($uiRows as &$row) {
            if (is_array($row) && $this->isStatusOperationPageName($row['page_name'] ?? '')) {
                $assign($row);
            }
        }
        unset($row);

        foreach ($uiRows as &$row) {
            if (!is_array($row) || $this->isStatusOperationPageName($row['page_name'] ?? '')) {
                continue;
            }
            $assign($row);
        }
        unset($row);

        return $uiRows;
    }

    private function linkingAllowedPageMaps($user_id, $modelno, $modal_id)
    {
        $allowedPageNamesByNo = [];
        $allowedPageNosByName = [];
        $pageOptions = [];

        if ($this->View_page_model && method_exists($this->View_page_model, 'getLinkingPageOptions')) {
            $pageOptions = $this->View_page_model->getLinkingPageOptions($user_id, $modelno, $modal_id);
        }

        foreach ($pageOptions as $page) {
            $pageNo = trim((string) ($page['page_no'] ?? ''));
            $pageName = trim((string) ($page['page_name'] ?? ''));
            if ($pageNo === '' || $pageName === '') {
                continue;
            }

            $allowedPageNamesByNo[$pageNo] = $pageName;
            $allowedPageNosByName[strtolower($pageName)] = $pageNo;
        }

        return [$allowedPageNamesByNo, $allowedPageNosByName, $pageOptions];
    }

    private function rebuildLinkingDataFromUiRows(array $uiRows)
    {
        $pageNameMap = [];
        $linkingData = [];
        $sequence = 1;

        foreach ($uiRows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $pageNo = trim((string) ($row['page_no'] ?? ''));
            $pageName = trim((string) ($row['page_name'] ?? ''));
            $parameters = !empty($row['parameters']) && is_array($row['parameters']) ? $row['parameters'] : [];
            if ($pageNo === '' || $pageName === '' || empty($parameters)) {
                continue;
            }

            $pageNameMap[$pageNo] = $pageName;
            foreach ($parameters as $parameter) {
                $parameter = trim((string) $parameter);
                if ($parameter === '') {
                    continue;
                }

                $linkingData[$pageNo][] = [
                    'parameter' => $parameter,
                    'sequence' => $sequence++,
                    'page_no' => $pageNo,
                    'page_name' => $pageName,
                ];
            }
        }

        return [$pageNameMap, $linkingData];
    }

// here studio funciots start

public function api_page_linking($id)
{
    $user_id = $this->currentBuilderUserId();
    $modelno = $this->resolveBuilderModelNo($id, $this->request->getVar('modelno'));

    $parameter_names = $this->View_page_model->getApiParameterNamesForLinking($id, $user_id, $modelno);
    $page_names = $this->View_page_model->extract_pages($user_id, $modelno, $id);
    $saved_mapping = $this->View_page_model->getPageParameterMapping($modelno);
    $linking_page_options = $this->View_page_model->getLinkingPageOptions($user_id, $modelno, $id);
    $locked_rows = $this->View_page_model->getProductApiPageParameterRows($modelno, $user_id, $id);

    return view('items/modal_api_page_linking', [
        'user_id' => $user_id,
        'modal_id' => $id,
        'modelno' => $modelno,
        'params' => $parameter_names,
        'pages' => $page_names,
        'linking_page_options' => $linking_page_options,
        'locked_rows' => $locked_rows,
        'saved_mapping' => $saved_mapping,
    ]);
}

public function submit_page_params()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $cmd_parameters = $this->request->getPost('cmd_parameter');
    $uiPageNos      = $this->request->getPost('uiPageNo_cmd');
    $pageNames      = $this->request->getPost('page_name');
    $sequences      = $this->request->getPost('parameter_sequence');
    $modal_id       = $this->request->getPost('modal_id');
    $modelno        = $this->resolveBuilderModelNo($modal_id, $this->request->getPost('modelno'));
    $uiRowsJson     = $this->request->getPost('ui_rows_json');
    $uiRows         = [];
    $user_id        = $this->currentBuilderUserId();

    $cmd_parameters = is_array($cmd_parameters) ? $cmd_parameters : [];
    $uiPageNos = is_array($uiPageNos) ? $uiPageNos : [];
    $pageNames = is_array($pageNames) ? $pageNames : [];
    $sequences = is_array($sequences) ? $sequences : [];

    [$allowedPageNamesByNo, $allowedPageNosByName] = $this->linkingAllowedPageMaps($user_id, $modelno, $modal_id);

    if (is_string($uiRowsJson) && trim($uiRowsJson) !== '') {
        $decodedUiRows = json_decode($uiRowsJson, true);
        if (is_array($decodedUiRows)) {
            foreach ($decodedUiRows as $uiRow) {
                if (!is_array($uiRow)) {
                    continue;
                }

                $pageNo = trim((string) ($uiRow['page_no'] ?? ''));
                $pageName = trim((string) ($uiRow['page_name'] ?? ''));
                $sequence = max(1, (int) ($uiRow['sequence'] ?? 1));
                $parameters = [];

                if (!empty($uiRow['parameters']) && is_array($uiRow['parameters'])) {
                    foreach ($uiRow['parameters'] as $parameter) {
                        $parameter = trim((string) $parameter);
                        if ($parameter !== '' && !in_array($parameter, $parameters, true)) {
                            $parameters[] = $parameter;
                        }
                    }
                } else {
                    $parameter = trim((string) ($uiRow['parameter'] ?? ''));
                    if ($parameter !== '') {
                        $parameters[] = $parameter;
                    }
                }

                if (
                    $pageNo === '' ||
                    $pageName === '' ||
                    empty($parameters) ||
                    !isset($allowedPageNamesByNo[$pageNo]) ||
                    !isset($allowedPageNosByName[strtolower($pageName)]) ||
                    (string) $allowedPageNosByName[strtolower($pageName)] !== (string) $pageNo
                ) {
                    continue;
                }

                $uiRows[] = [
                    'page_no' => $pageNo,
                    'page_name' => $allowedPageNamesByNo[$pageNo],
                    'parameters' => $parameters,
                    'sequence' => $sequence,
                ];
            }
        }
    }

    // Ã°Å¸â€Â Debug log (optional - shows post data in writable/logs/)
    log_message('debug', print_r($this->request->getPost(), true));

    // Ã¢Å“â€¦ Input validation
    if (!$modelno) {
        return $this->response->setJSON(['error' => 'Invalid or missing input data']);
    }

    $linkingData = [];
    $pageNameMap = [];

    foreach ($cmd_parameters as $index => $param) {
        $param = trim((string) $param);
        $pageNo = isset($uiPageNos[$index]) ? trim((string) $uiPageNos[$index]) : '';
        $sequence = isset($sequences[$index]) ? (int) $sequences[$index] : ($index + 1);

        if ($param === '' || $pageNo === '') {
            continue;
        }

        // Ã¢Å“ÂÃ¯Â¸Â Page name fallback if blank
        if (!isset($allowedPageNamesByNo[$pageNo])) {
            continue;
        }

        $pageName = isset($pageNames[$pageNo]) && trim($pageNames[$pageNo]) !== ''
                    ? trim($pageNames[$pageNo])
                    : "Page " . $pageNo;

        if (
            !isset($allowedPageNosByName[strtolower($pageName)]) ||
            (string) $allowedPageNosByName[strtolower($pageName)] !== (string) $pageNo
        ) {
            $pageName = $allowedPageNamesByNo[$pageNo];
        }

        $pageNameMap[$pageNo] = $pageName;

        // Use page number as canonical key so dynamic rows never collapse.
        if (!isset($linkingData[$pageNo])) {
            $linkingData[$pageNo] = [];
        }

        $linkingData[$pageNo][] = [
            'parameter' => $param,
            'sequence' => max(1, $sequence),
            'page_no' => $pageNo,
            'page_name' => $pageName,
        ];
    }

    // Ã¢Å“â€¦ Save to DB via model
    foreach ($linkingData as $pageName => $rows) {
        usort($rows, function ($a, $b) {
            return ((int) ($a['sequence'] ?? 0)) <=> ((int) ($b['sequence'] ?? 0));
        });

        $linkingData[$pageName] = array_values($rows);
    }

    [$pageNameMap, $linkingData, $uiRows] = $this->View_page_model->preserveLockedProductApiLinking(
        $modelno,
        $pageNameMap,
        $linkingData,
        $uiRows,
        $user_id,
        $modal_id
    );

    $uiRows = $this->keepFirstPageLinkingParameters($uiRows);
    $uiRows = array_values(array_filter($uiRows, function ($row) {
        return is_array($row) && !empty($row['parameters']);
    }));
    if (!empty($uiRows)) {
        [$pageNameMap, $linkingData] = $this->rebuildLinkingDataFromUiRows($uiRows);
    }

    if (empty($linkingData) && empty($uiRows)) {
        $this->View_page_model->savePageParameterJsonMapping($modelno, [], [], [], true);
        session()->setTempdata('view_ui_mapping_' . $modelno, [
            'model_name' => $modelno,
            'page_names_array' => [],
            'linking_data_array' => [],
            'rows' => [],
            'ui_rows' => [],
        ], 300);
        return redirect()->to(get_uri("items/view_page_ui/" . (int) $modal_id . "?modelno=" . rawurlencode($modelno) . "&schema_sync=1"));
    }

    $flattenedRows = [];
    foreach ($linkingData as $pageNo => $rows) {
        foreach ($rows as $rowIndex => $row) {
            $parameter = trim((string) ($row['parameter'] ?? ''));
            if ($parameter === '') {
                continue;
            }
            $resolvedPageNo = trim((string) ($row['page_no'] ?? $pageNo));
            $resolvedPageName = trim((string) ($row['page_name'] ?? ($pageNameMap[$resolvedPageNo] ?? ('Page ' . $resolvedPageNo))));
            $flattenedRows[] = [
                'page_no' => $resolvedPageNo,
                'page_name' => $resolvedPageName,
                'parameter' => $parameter,
                'sequence' => (int) ($row['sequence'] ?? ($rowIndex + 1)),
                'api' => [],
            ];
        }
    }
    usort($flattenedRows, function ($a, $b) {
        $pageCompare = ((int) ($a['page_no'] ?? 0)) <=> ((int) ($b['page_no'] ?? 0));
        if ($pageCompare !== 0) {
            return $pageCompare;
        }
        return ((int) ($a['sequence'] ?? 0)) <=> ((int) ($b['sequence'] ?? 0));
    });

    $session = session();
    $session->setTempdata('view_ui_mapping_' . $modelno, [
        'model_name' => $modelno,
        'page_names_array' => $pageNameMap,
        'linking_data_array' => $linkingData,
        'rows' => $flattenedRows,
        'ui_rows' => $uiRows,
    ], 300);

    $this->View_page_model->savePageParameterJsonMapping($modelno, $pageNameMap, $linkingData, $uiRows, true);

    return redirect()->to(get_uri("items/view_page_ui/" . (int) $modal_id . "?modelno=" . rawurlencode($modelno) . "&schema_sync=1"));
}

public function save_page_linking_row()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $modal_id = (int) $this->request->getPost('modal_id');
    $modelno = $this->resolveBuilderModelNo($modal_id, $this->request->getPost('modelno'));
    $uiRowsJson = (string) $this->request->getPost('ui_rows_json');
    $allowEmpty = (string) $this->request->getPost('allow_empty') === '1';
    $user_id = $this->currentBuilderUserId();

    if ($modelno === '' || $modal_id <= 0 || trim($uiRowsJson) === '') {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Missing page linking data.',
        ]);
    }

    [$allowedPageNamesByNo, $allowedPageNosByName] = $this->linkingAllowedPageMaps($user_id, $modelno, $modal_id);

    $decodedUiRows = json_decode($uiRowsJson, true);
    if (!is_array($decodedUiRows)) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid page linking data.',
        ]);
    }

    $uiRows = [];
    $linkingData = [];
    $pageNameMap = [];
    $seenPages = [];
    $sequence = 1;

    foreach ($decodedUiRows as $uiRow) {
        if (!is_array($uiRow)) {
            continue;
        }

        $pageNo = trim((string) ($uiRow['page_no'] ?? ''));
        $pageName = trim((string) ($uiRow['page_name'] ?? ''));

        if (
            $pageNo === '' ||
            $pageName === '' ||
            !isset($allowedPageNamesByNo[$pageNo]) ||
            !isset($allowedPageNosByName[strtolower($pageName)]) ||
            (string) $allowedPageNosByName[strtolower($pageName)] !== (string) $pageNo ||
            isset($seenPages[$pageNo])
        ) {
            continue;
        }

        $parameters = [];
        if (!empty($uiRow['parameters']) && is_array($uiRow['parameters'])) {
            foreach ($uiRow['parameters'] as $parameter) {
                $parameter = trim((string) $parameter);
                if ($parameter !== '' && !in_array($parameter, $parameters, true)) {
                    $parameters[] = $parameter;
                }
            }
        }

        if (empty($parameters)) {
            continue;
        }

        $pageName = $allowedPageNamesByNo[$pageNo];
        $pageNameMap[$pageNo] = $pageName;
        $seenPages[$pageNo] = true;
        $rowSequence = max(1, (int) ($uiRow['sequence'] ?? $sequence));

        $uiRows[] = [
            'page_no' => $pageNo,
            'page_name' => $pageName,
            'parameters' => $parameters,
            'sequence' => $rowSequence,
        ];
    }

    $uiRows = $this->keepFirstPageLinkingParameters($uiRows);
    $uiRows = array_values(array_filter($uiRows, function ($row) {
        return is_array($row) && !empty($row['parameters']);
    }));
    [$pageNameMap, $linkingData] = $this->rebuildLinkingDataFromUiRows($uiRows);

    [$pageNameMap, $linkingData, $uiRows] = $this->View_page_model->preserveLockedProductApiLinking(
        $modelno,
        $pageNameMap,
        $linkingData,
        $uiRows,
        $user_id,
        $modal_id
    );

    $uiRows = $this->keepFirstPageLinkingParameters($uiRows);
    $uiRows = array_values(array_filter($uiRows, function ($row) {
        return is_array($row) && !empty($row['parameters']);
    }));
    [$pageNameMap, $linkingData] = $this->rebuildLinkingDataFromUiRows($uiRows);

    if (empty($uiRows) || empty($linkingData)) {
        if ($allowEmpty) {
            $this->View_page_model->savePageParameterJsonMapping($modelno, [], [], [], true);

            $response = [
                'success' => true,
                'message' => 'Rows cleared.',
            ];

            if (function_exists('csrf_token') && function_exists('csrf_hash')) {
                $response['csrfName'] = csrf_token();
                $response['csrfHash'] = csrf_hash();
            }

            return $this->response->setJSON($response);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Please select at least one parameter before saving.',
        ]);
    }

    $this->View_page_model->savePageParameterJsonMapping($modelno, $pageNameMap, $linkingData, $uiRows, true);

    $response = [
        'success' => true,
        'message' => 'Row saved.',
    ];

    if (function_exists('csrf_token') && function_exists('csrf_hash')) {
        $response['csrfName'] = csrf_token();
        $response['csrfHash'] = csrf_hash();
    }

    return $this->response->setJSON($response);
}

public function view_page_ui($id = 0)
{
    if (\App\Models\View_page_model::loadDeviceUiSchemaGenerator()) {
        \App\Services\DeviceUiSchemaGenerator::raiseMemoryLimit();
    }

    $modelno = $this->resolveBuilderModelNo($id, $this->request->getVar('modelno'));
    $model_data = $this->View_page_model->getPageParameterMapping($modelno);
    if (!is_array($model_data)) {
        $model_data = ['rows' => [], 'page_names_array' => []];
    }
    // The canonical mapping is the DB row saved by submit_page_params/save_page_linking_row.
    // Avoid overriding it with short-lived session rows because those rows do not include
    // hydrated API metadata, which can make Studio revisit with missing/wrong components.
    $uiTheme = $this->View_page_model->getDeviceUiTheme($modelno) ?: ['theme' => [], 'schema' => []];
    $requestNodeId = trim((string) $this->request->getGet('node_id'));
    $savedSchema = null;
    if (empty($model_data['rows'])) {
        try {
            $savedSchema = $this->View_page_model->getDeviceUiSchema($modelno, $requestNodeId);
        } catch (\Throwable $exception) {
            $savedSchema = null;
        }
    }
    if (is_array($savedSchema) && !empty($savedSchema)) {
        $uiTheme['schema'] = $savedSchema;
    }
    $rowParametersJson = $this->request->getGet('row_parameters');
    $isRowView = false;

    if ($rowParametersJson) {
        $rowParameters = json_decode($rowParametersJson, true);

        if (is_array($rowParameters) && count($rowParameters)) {
            $pageNo = trim((string) $this->request->getGet('row_page_no'));
            $pageName = trim((string) $this->request->getGet('row_page_name'));
            $sequence = (int) $this->request->getGet('row_sequence');
            $preview = $this->View_page_model->buildSinglePagePreviewMapping(
                $modelno,
                $pageNo,
                $pageName,
                $rowParameters,
                $sequence
            );
            if (!empty($preview['rows']) && is_array($preview['rows'])) {
                $model_data = $preview;
                $isRowView = true;
            }
        }
    }

    $resolvedNodeId = method_exists($this->View_page_model, 'getDeviceUiNodeId')
        ? $this->View_page_model->getDeviceUiNodeId($modelno, $requestNodeId)
        : '';
    $deviceUiInfo = $this->View_page_model->getDeviceUiInfo($modelno, $requestNodeId !== '' ? $requestNodeId : $resolvedNodeId);

    $schemaApiUrl = base_url('v8/api_schema.php') . '?model_no=' . rawurlencode((string) $modelno);
    $schemaNodeId = $resolvedNodeId !== '' ? $resolvedNodeId : $requestNodeId;
    if ($schemaNodeId !== '' && strcasecmp($schemaNodeId, (string) $modelno) !== 0) {
        $schemaApiUrl .= '&node_id=' . rawurlencode($schemaNodeId);
    }

    $iotDashboardModel = new IotDashboardModel();
    $userId = (int) ($this->login_user->id ?? 0);
    $isAdmin = !empty($this->login_user->is_admin);
    $devices = $userId > 0 ? $iotDashboardModel->getDevicesForUser($userId, $isAdmin) : [];
    $studioAccessToken = $userId > 0
        ? $iotDashboardModel->getSavedAccessToken($userId, $isAdmin, $devices)
        : '';

    return view('items/view_page_ui', [
        'modal_id' => $id,
        'modelno' => $modelno,
        'model_data' => $model_data,
        'is_row_view' => $isRowView,
        // Browser SDUI rebuild only when page-linking redirects with schema_sync=1.
        // Mapping→schema persistence now happens server-side on save.
        'schema_sync' => !$isRowView && trim((string) $this->request->getGet('schema_sync')) === '1',
        'ui_theme' => $uiTheme,
        'device_ui_info' => is_array($deviceUiInfo) ? $deviceUiInfo : [],
        'resolved_node_id' => $schemaNodeId !== '' ? $schemaNodeId : $resolvedNodeId,
        // The Flutter app and Studio now use only the V8 schema endpoint.
        // node_id is resolved from product_api / saved schema when Studio opens.
        'schema_api_url' => $schemaApiUrl,
        'v8_device_api_base' => $iotDashboardModel->getV8DeviceApiBaseUrl(),
        'studio_access_token' => $studioAccessToken,
        'remove_device_url' => get_uri('items/remove_device_node'),
    ]);
}

public function remove_device_node()
{
    $userId = (int) ($this->login_user->id ?? 0);
    if ($userId <= 0) {
        return $this->response->setJSON(array('success' => false, 'message' => 'Authentication required.'));
    }

    $nodeId = trim((string) ($this->request->getPost('node_id') ?? $this->request->getGet('node_id') ?? ''));
    if ($nodeId === '') {
        return $this->response->setJSON(array('success' => false, 'message' => 'node_id is required.'));
    }

    $isAdmin = !empty($this->login_user->is_admin);
    $iotDashboardModel = new IotDashboardModel();
    $devices = $iotDashboardModel->getDevicesForUser($userId, $isAdmin);
    $accessToken = $iotDashboardModel->getSavedAccessToken($userId, $isAdmin, $devices);
    if ($accessToken === '') {
        return $this->response->setJSON(array('success' => false, 'message' => 'Access token is not available for this account.'));
    }

    $result = $iotDashboardModel->removeDeviceNode($nodeId, $accessToken);
    if (!isset($result['success'])) {
        $result['success'] = !isset($result['status']) || strtolower((string) $result['status']) !== 'failure';
    }

    return $this->response->setJSON($result);
}

public function save_view_page_ui_theme()
{
    $modelno = trim((string) $this->request->getPost('modelno'));
    $themeJson = (string) $this->request->getPost('theme_json');
    $schemaJson = (string) $this->request->getPost('schema_json');
    if (\App\Models\View_page_model::loadDeviceUiSchemaGenerator()) {
        \App\Services\DeviceUiSchemaGenerator::raiseMemoryLimit();
    }

    if ($modelno === '') {
        return $this->response->setJSON(['success' => false, 'message' => 'Model number is required.']);
    }

    $theme = json_decode($themeJson, true);
    if (!is_array($theme)) {
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid theme JSON.']);
    }

    $schema = json_decode($schemaJson, true);
    unset($schemaJson);
    if (!is_array($schema) || empty($schema)) {
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid schema JSON.']);
    }

    $documentType = isset($schema['documentType']) ? (string) $schema['documentType'] : '';
    $schemaType = isset($schema['schemaType']) ? (string) $schema['schemaType'] : '';

    if ($documentType === 'serverDrivenUi') {
        $requiredSchemaKeys = ['schemaVersion', 'documentType', 'app', 'theme', 'state', 'dataSources', 'actions', 'routes'];
    } elseif ($schemaType === 'device-ui') {
        $requiredSchemaKeys = ['schemaVersion', 'schemaType', 'meta', 'theme', 'icons', 'controls', 'pages', 'navigation', 'actions', 'state', 'settings', 'ui'];
    } elseif ($schemaType === 'flutter-widget-tree') {
        $requiredSchemaKeys = ['schemaVersion', 'schemaType', 'app', 'theme', 'navigation', 'data', 'actions', 'widgetTree'];
    } else {
        $requiredSchemaKeys = ['schemaVersion', 'app', 'theme', 'navigation', 'pages', 'components', 'modals', 'actions', 'dataSources'];
    }

    foreach ($requiredSchemaKeys as $requiredSchemaKey) {
        if (!array_key_exists($requiredSchemaKey, $schema)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Schema is missing the required "' . $requiredSchemaKey . '" definition.',
            ]);
        }
    }

    if ($documentType === 'serverDrivenUi') {
        $initialRoute = isset($schema['app']['initialRoute']) ? (string) $schema['app']['initialRoute'] : '';
        if ($initialRoute === '' || !isset($schema['routes'][$initialRoute]['screen']) || !is_array($schema['routes'][$initialRoute]['screen'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Server-driven UI schema must include a valid routes.<initialRoute>.screen definition.',
            ]);
        }

        if (!isset($schema['state']['parameters']) || !is_array($schema['state']['parameters'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Server-driven UI schema must include a valid state.parameters map.',
            ]);
        }
    } elseif ($schemaType === 'device-ui') {
        if (!is_array($schema['ui']['widgetTree'] ?? null) || empty($schema['ui']['widgetTree']['type']) || empty($schema['ui']['widgetTree']['id'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Device UI schema must include a valid ui.widgetTree root.',
            ]);
        }

        if (!is_array($schema['navigation']) || empty($schema['navigation']['type'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Device UI schema must include a valid navigation definition.',
            ]);
        }
    } elseif ($schemaType === 'flutter-widget-tree') {
        if (!is_array($schema['widgetTree']) || empty($schema['widgetTree']['type']) || empty($schema['widgetTree']['id'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Flutter widget schema must include a valid widgetTree root.',
            ]);
        }

        if (!is_array($schema['navigation']) || empty($schema['navigation']['type'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Flutter widget schema must include a valid navigation definition.',
            ]);
        }
    }

    $themeSaved = $this->View_page_model->saveDeviceUiTheme($modelno, $theme, $schema);
    $schemaSaved = $this->View_page_model->saveDeviceUiSchema($modelno, $schema);
    $saveError = method_exists($this->View_page_model, 'getDeviceUiSaveError')
        ? trim((string) $this->View_page_model->getDeviceUiSaveError())
        : '';
    $saveErrorSuffix = $saveError !== '' ? ' ' . $saveError : '';

    if (!$themeSaved && !$schemaSaved) {
        return $this->response->setStatusCode(500)->setJSON([
            'success' => false,
            'message' => 'Unable to save the UI theme and schema.' . $saveErrorSuffix,
        ]);
    }

    if (!$themeSaved) {
        return $this->response->setStatusCode(500)->setJSON([
            'success' => false,
            'message' => 'Theme saved partially, but the theme record could not be updated.' . $saveErrorSuffix,
        ]);
    }

    if (!$schemaSaved) {
        return $this->response->setStatusCode(500)->setJSON([
            'success' => false,
            'message' => 'Theme saved, but the device UI schema could not be updated.' . $saveErrorSuffix,
        ]);
    }

    return $this->response->setJSON([
        'success' => true,
        'message' => 'UI created successfully.',
        'api_url' => base_url('v8/api_schema.php') . '?model_no=' . rawurlencode($modelno),
    ]);
}

// here studio funcitsn ends
}

