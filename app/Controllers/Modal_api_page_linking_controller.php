<?php

namespace App\Controllers;

use App\Models\Modal_api_page_linking_model;

class Modal_api_page_linking_controller extends Security_Controller {

    /** @var Modal_api_page_linking_model */
    protected $Modal_api_page_linking_model;

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("order");

        $class = '\\App\\Models\\Modal_api_page_linking_model';
        if (!class_exists($class, false)) {
            foreach (array('Modal_api_page_linking_model.php', 'modal_api_page_linking_model.php') as $fileName) {
                $modelFile = APPPATH . 'Models/' . $fileName;
                if (is_file($modelFile)) {
                    require_once $modelFile;
                    break;
                }
            }
        }

        $this->Modal_api_page_linking_model = class_exists($class, false) ? new $class() : null;
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
        if ($this->Modal_api_page_linking_model && method_exists($this->Modal_api_page_linking_model, 'resolveLinkingModelNo')) {
            return $this->Modal_api_page_linking_model->resolveLinkingModelNo((int) $itemId, $requested);
        }

        if ($requested !== '') {
            return $requested;
        }

        return $this->Modal_api_page_linking_model
            ? $this->Modal_api_page_linking_model->getModelNameForItem((int) $itemId)
            : '';
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

        if ($this->Modal_api_page_linking_model && method_exists($this->Modal_api_page_linking_model, 'getLinkingPageOptions')) {
            $pageOptions = $this->Modal_api_page_linking_model->getLinkingPageOptions($user_id, $modelno, $modal_id);
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

    public function api_page_linking($id)
    {
        $user_id = $this->currentBuilderUserId();
        $modelno = $this->resolveBuilderModelNo($id, $this->request->getVar('modelno'));

        $parameter_names = $this->Modal_api_page_linking_model
            ? $this->Modal_api_page_linking_model->getApiParameterNamesForLinking($id, $user_id, $modelno)
            : array();
        $page_names = $this->Modal_api_page_linking_model
            ? $this->Modal_api_page_linking_model->extract_pages($user_id, $modelno, $id)
            : array();
        $saved_mapping = $this->Modal_api_page_linking_model
            ? $this->Modal_api_page_linking_model->getPageParameterMapping($modelno)
            : null;
        $linking_page_options = $this->Modal_api_page_linking_model
            ? $this->Modal_api_page_linking_model->getLinkingPageOptions($user_id, $modelno, $id)
            : array();
        $locked_rows = $this->Modal_api_page_linking_model
            ? $this->Modal_api_page_linking_model->getProductApiPageParameterRows($modelno, $user_id, $id)
            : array();

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

        log_message('debug', print_r($this->request->getPost(), true));

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

        foreach ($linkingData as $pageName => $rows) {
            usort($rows, function ($a, $b) {
                return ((int) ($a['sequence'] ?? 0)) <=> ((int) ($b['sequence'] ?? 0));
            });

            $linkingData[$pageName] = array_values($rows);
        }

        if ($this->Modal_api_page_linking_model) {
            [$pageNameMap, $linkingData, $uiRows] = $this->Modal_api_page_linking_model->preserveLockedProductApiLinking(
                $modelno,
                $pageNameMap,
                $linkingData,
                $uiRows,
                $user_id,
                $modal_id
            );
        }

        $uiRows = $this->keepFirstPageLinkingParameters($uiRows);
        $uiRows = array_values(array_filter($uiRows, function ($row) {
            return is_array($row) && !empty($row['parameters']);
        }));
        if (!empty($uiRows)) {
            [$pageNameMap, $linkingData] = $this->rebuildLinkingDataFromUiRows($uiRows);
        }

        if (empty($linkingData) && empty($uiRows)) {
            if ($this->Modal_api_page_linking_model) {
                $this->Modal_api_page_linking_model->savePageParameterJsonMapping($modelno, [], [], [], true);
            }
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

        if ($this->Modal_api_page_linking_model) {
            $this->Modal_api_page_linking_model->savePageParameterJsonMapping($modelno, $pageNameMap, $linkingData, $uiRows, true);
        }

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

        if ($this->Modal_api_page_linking_model) {
            [$pageNameMap, $linkingData, $uiRows] = $this->Modal_api_page_linking_model->preserveLockedProductApiLinking(
                $modelno,
                $pageNameMap,
                $linkingData,
                $uiRows,
                $user_id,
                $modal_id
            );
        }

        $uiRows = $this->keepFirstPageLinkingParameters($uiRows);
        $uiRows = array_values(array_filter($uiRows, function ($row) {
            return is_array($row) && !empty($row['parameters']);
        }));
        [$pageNameMap, $linkingData] = $this->rebuildLinkingDataFromUiRows($uiRows);

        if (empty($uiRows) || empty($linkingData)) {
            if ($allowEmpty) {
                if ($this->Modal_api_page_linking_model) {
                    $this->Modal_api_page_linking_model->savePageParameterJsonMapping($modelno, [], [], [], true);
                }

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

        if ($this->Modal_api_page_linking_model) {
            $this->Modal_api_page_linking_model->savePageParameterJsonMapping($modelno, $pageNameMap, $linkingData, $uiRows, true);
        }

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
}
