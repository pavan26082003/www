<?php

namespace App\Controllers;

use App\Models\IotDashboardModel;
use App\Models\View_page_ui_model;

class View_page_controller extends Security_Controller {

    /** @var View_page_ui_model */
    protected $View_page_ui_model;

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("order");

        if (empty($this->View_page_ui_model)) {
            $class = '\\App\\Models\\View_page_ui_model';
            if (!class_exists($class, false)) {
                foreach (array('View_page_ui_model.php', 'view_page_ui_model.php') as $fileName) {
                    $modelFile = APPPATH . 'Models/' . $fileName;
                    if (is_file($modelFile)) {
                        require_once $modelFile;
                        break;
                    }
                }
            }

            if (class_exists($class, false)) {
                $this->View_page_ui_model = new $class();
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
        if ($this->View_page_ui_model && method_exists($this->View_page_ui_model, 'resolveLinkingModelNo')) {
            return $this->View_page_ui_model->resolveLinkingModelNo((int) $itemId, $requested);
        }

        if ($requested !== '') {
            return $requested;
        }

        return $this->View_page_ui_model ? $this->View_page_ui_model->getModelNameForItem((int) $itemId) : '';
    }

public function view_page_ui($id = 0)
{
    if (\App\Models\View_page_ui_model::loadDeviceUiSchemaGenerator()) {
        \App\Services\DeviceUiSchemaGenerator::raiseMemoryLimit();
    }

    $modelno = $this->resolveBuilderModelNo($id, $this->request->getVar('modelno'));
    $model_data = $this->View_page_ui_model->getPageParameterMapping($modelno);
    if (!is_array($model_data)) {
        $model_data = ['rows' => [], 'page_names_array' => []];
    }
    // The canonical mapping is the DB row saved by submit_page_params/save_page_linking_row.
    // Avoid overriding it with short-lived session rows because those rows do not include
    // hydrated API metadata, which can make Studio revisit with missing/wrong components.
    $uiTheme = $this->View_page_ui_model->getDeviceUiTheme($modelno) ?: ['theme' => [], 'schema' => []];
    $requestNodeId = trim((string) $this->request->getGet('node_id'));
    $savedSchema = null;
    if (empty($model_data['rows'])) {
        try {
            $savedSchema = $this->View_page_ui_model->getDeviceUiSchema($modelno, $requestNodeId);
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
            $preview = $this->View_page_ui_model->buildSinglePagePreviewMapping(
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

    $resolvedNodeId = method_exists($this->View_page_ui_model, 'getDeviceUiNodeId')
        ? $this->View_page_ui_model->getDeviceUiNodeId($modelno, $requestNodeId)
        : '';
    $deviceUiInfo = $this->View_page_ui_model->getDeviceUiInfo($modelno, $requestNodeId !== '' ? $requestNodeId : $resolvedNodeId);

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
    @ini_set('display_errors', '0');

    try {
        return $this->saveViewPageUiThemeBody();
    } catch (\Throwable $exception) {
        log_message('error', 'Device UI schema save failed: ' . $exception->getMessage());

        return $this->response->setStatusCode(500)->setJSON([
            'success' => false,
            'message' => 'Unable to save the schema. ' . $exception->getMessage(),
        ]);
    }
}

private function saveViewPageUiThemeBody()
{
    $modelno = trim((string) $this->request->getPost('modelno'));
    $themeJson = (string) $this->request->getPost('theme_json');
    $schemaJson = (string) $this->request->getPost('schema_json');
    if (\App\Models\View_page_ui_model::loadDeviceUiSchemaGenerator()) {
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

    $themeSaved = $this->View_page_ui_model->saveDeviceUiTheme($modelno, $theme, $schema);
    $schemaSaved = $this->View_page_ui_model->saveDeviceUiSchema($modelno, $schema);
    $saveError = method_exists($this->View_page_ui_model, 'getDeviceUiSaveError')
        ? trim((string) $this->View_page_ui_model->getDeviceUiSaveError())
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

