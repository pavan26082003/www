<?php

namespace App\Controllers;

use App\Models\Viewapis_model;

class Viewapis_controller extends Security_Controller {

    /** @var Viewapis_model */
    protected $Viewapis_model;

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("order");

        $class = '\\App\\Models\\Viewapis_model';
        if (!class_exists($class, false)) {
            foreach (array('Viewapis_model.php', 'viewapis_model.php') as $fileName) {
                $modelFile = APPPATH . 'Models/' . $fileName;
                if (is_file($modelFile)) {
                    require_once $modelFile;
                    break;
                }
            }
        }

        $this->Viewapis_model = class_exists($class, false) ? new $class() : null;
    }

    private function currentUserId()
    {
        if (!empty($this->login_user) && !empty($this->login_user->id)) {
            return $this->login_user->id;
        }

        if (!empty($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (!empty($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }

        return '';
    }

    function viewapis($itemid = 0) {
        $itemid = (int) $itemid;
        $modelno = trim((string) $this->request->getVar('modelno'));
        if ($modelno === '' && $this->Viewapis_model) {
            $modelno = $this->Viewapis_model->getModelNameForItem($itemid);
        }

        $view_data['itemid'] = $itemid;
        $view_data['modelno'] = $modelno;
        $view_data['user_id'] = $this->currentUserId();

        return $this->template->rander("items/viewapis", $view_data);
    }

    function list_data2($itemid = 0) {
        try {
            $itemid = (int) $itemid;
            if ($itemid <= 0) {
                $itemid = (int) $this->request->getVar('itemid');
            }

            $user_id = $this->currentUserId();
            $modelno = trim((string) $this->request->getVar('modelno'));
            if ($modelno === '' && $itemid > 0 && $this->Viewapis_model) {
                $modelno = $this->Viewapis_model->getModelNameForItem($itemid);
            }

            if (!$this->Viewapis_model || ($itemid <= 0 && $modelno === '')) {
                return $this->response->setJSON(array("data" => array()));
            }

            $list_data = $this->Viewapis_model->getApiTreeConfigList($itemid, $user_id, $modelno);
            $legacy_data = $this->Viewapis_model->get_itemapis_for_view($itemid, $user_id, $modelno);
            $list_data = is_array($list_data) ? $list_data : array();
            $legacy_data = is_array($legacy_data) ? $legacy_data : array();

            $result = array();
            $seenLegacyKeys = array();

            foreach ($list_data as $data) {
                $result[] = $this->_make_api_tree_row($data);
                $seenLegacyKeys[$this->_api_legacy_key(
                    isset($data->model_name) ? $data->model_name : '',
                    isset($data->parameter_name) ? $data->parameter_name : '',
                    isset($data->product_name) ? $data->product_name : ''
                )] = true;
            }

            foreach ($legacy_data as $data) {
                $legacyKey = $this->_api_legacy_key(
                    isset($data->model_name) ? $data->model_name : '',
                    isset($data->parameter_name) ? $data->parameter_name : '',
                    isset($data->product_name) ? $data->product_name : ''
                );
                if (isset($seenLegacyKeys[$legacyKey])) {
                    continue;
                }
                $result[] = $this->_make_legacy_api_row($data);
            }

            return $this->response->setJSON(array("data" => $result));

        } catch (\Throwable $e) {
            log_message('error', 'list_data2 error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            return $this->response->setJSON(array(
                "data" => array(),
                "error" => $e->getMessage()
            ));
        }
    }

    function api_config_preview($config_id = 0) {
        $user_id = $this->currentUserId();
        if (!$this->Viewapis_model) {
            return $this->response->setJSON(array('success' => false, 'message' => 'API config not found.'));
        }

        $tree = $this->Viewapis_model->getApiTreeConfigFull($config_id);

        if (!$tree || empty($tree['config'])) {
            return $this->response->setJSON(array('success' => false, 'message' => 'API config not found.'));
        }

        $configUserId = (int) (isset($tree['config']['user_id']) ? $tree['config']['user_id'] : 0);
        $configItemId = (int) (isset($tree['config']['item_id']) ? $tree['config']['item_id'] : 0);
        $isOwner = $user_id && $configUserId === (int) $user_id;
        $isAdmin = (!empty($this->login_user) && !empty($this->login_user->is_admin));
        $belongsToUserItem = false;

        if (!$isOwner && !$isAdmin && $configItemId > 0 && $user_id) {
            $belongsToUserItem = $this->Viewapis_model->itemBelongsToUser($configItemId, $user_id);
        }

        $unsignedOwnItem = ($configUserId <= 0 && $belongsToUserItem);

        if (!$isOwner && !$isAdmin && !$unsignedOwnItem && !$belongsToUserItem) {
            return $this->response->setJSON(array('success' => false, 'message' => 'API config not found.'));
        }

        return $this->response->setJSON(array(
            'success' => true,
            'config_id' => (int) $config_id,
            'preview' => $this->_build_api_tree_preview_text($tree)
        ));
    }

    function delete_api_config($config_id = 0) {
        $user_id = $this->currentUserId();
        $configId = (int) $config_id;
        if ($configId <= 0) {
            $configId = (int) $this->request->getVar('config_id');
        }

        $tree = ($this->Viewapis_model && $configId > 0)
            ? $this->Viewapis_model->getApiTreeConfigFull($configId)
            : null;
        $parameterName = (is_array($tree) && !empty($tree['config']['parameter_name']))
            ? $tree['config']['parameter_name']
            : '';

        if ($parameterName !== '' && $this->isProtectedDefaultApi($parameterName)) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => 'Default APIs cannot be deleted.'
            ));
        }

        $deleted = ($this->Viewapis_model)
            ? $this->Viewapis_model->deleteApiTreeConfig($configId, $user_id)
            : false;

        return $this->response->setJSON(array(
            'success' => $deleted ? true : false,
            'message' => $deleted ? 'API deleted successfully.' : 'Unable to delete API config.'
        ));
    }

    private function _api_legacy_key($modelName, $parameterName, $productName) {
        return strtolower(trim((string) $modelName)) . '|' . strtolower(trim((string) $parameterName)) . '|' . strtolower(trim((string) $productName));
    }

    private function normalizeApiParameterName($parameterName)
    {
        $normalized = html_entity_decode((string) $parameterName, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $normalized = preg_replace('/[^a-zA-Z]+/u', ' ', $normalized);
        $normalized = strtolower(trim(preg_replace('/\s+/u', ' ', $normalized)));

        return $normalized;
    }

    private function isReadOnlyDefaultApi($parameterName)
    {
        $normalized = $this->normalizeApiParameterName($parameterName);
        $compact = preg_replace('/\s+/', '', $normalized);

        return in_array($normalized, array('send data', 'display log', 'display'), true)
            || in_array($compact, array('senddata', 'displaylog', 'display'), true);
    }

    private function isProtectedDefaultApi($parameterName)
    {
        $normalized = $this->normalizeApiParameterName($parameterName);
        $compact = preg_replace('/\s+/', '', $normalized);

        return $this->isReadOnlyDefaultApi($parameterName)
            || $normalized === 'command get status'
            || $compact === 'commandgetstatus';
    }

    private function _make_api_tree_row($data) {
        $configId = (int) (isset($data->id) ? $data->id : 0);
        $itemId = (int) (isset($data->item_id) ? $data->item_id : 0);
        $parameterLabel = (!empty($data->parameter_name)) ? $data->parameter_name : 'N/A';
        $isReadOnly = $this->isReadOnlyDefaultApi($parameterLabel);
        $isProtected = $this->isProtectedDefaultApi($parameterLabel);

        $action = '<button type="button" class="api-action-btn view-api-btn" data-config-id="' . $configId . '" data-item-id="' . $itemId . '">View API</button>';

        if ($isReadOnly) {
            $editControl = '<button type="button" class="api-update-btn api-edit-btn api-locked-btn" disabled="disabled" data-locked="1" title="This default API is read-only">Locked</button>';
        } else {
            $editControl = '<button type="button" class="api-update-btn api-edit-btn" data-config-id="' . $configId . '" data-item-id="' . $itemId . '">Edit</button>';
        }

        if ($isProtected) {
            $deleteControl = '<button type="button" class="api-update-btn api-delete-btn api-locked-btn" disabled="disabled" data-locked="1" title="Default APIs cannot be deleted">Locked</button>';
        } else {
            $deleteControl = '<button type="button" class="api-update-btn api-delete-btn" data-config-id="' . $configId . '">Delete</button>';
        }

        return array(
            '<b>' . htmlspecialchars($parameterLabel, ENT_QUOTES, 'UTF-8') . '</b> <span class="api-version-badge api-version-v2">V2</span>',
            $action,
            $editControl . $deleteControl
        );
    }

    private function _make_legacy_api_row($data) {
        $itemId = (int) (isset($data->itemid) ? $data->itemid : 0);
        $parameterName = (!empty($data->parameter_name)) ? $data->parameter_name : 'N/A';
        $modelName = (!empty($data->model_name)) ? $data->model_name : '';
        $productName = (!empty($data->product_name)) ? $data->product_name : '';

        $preview = $this->_build_legacy_api_preview_text($data);
        $action = '<button type="button" class="api-action-btn view-legacy-api-btn" data-preview="' . htmlspecialchars($preview, ENT_QUOTES, 'UTF-8') . '" data-item-id="' . $itemId . '">View API</button>';

        $editUrl = get_uri("items/modal_command/" . rawurlencode($modelName) . "/" . rawurlencode($parameterName) . "/" . rawurlencode($productName));
        $deleteUrl = get_uri("items/deleteapi/" . rawurlencode($modelName) . "/" . rawurlencode($parameterName) . "/" . rawurlencode($productName));
        $update = '<a class="api-update-btn api-edit-btn" href="' . $editUrl . '">Edit</a>'
            . '<button type="button" class="api-update-btn api-delete-legacy-btn" data-delete-url="' . $deleteUrl . '">Delete</button>';

        return array(
            '<b>' . htmlspecialchars($parameterName, ENT_QUOTES, 'UTF-8') . '</b> <span class="api-version-badge api-version-legacy">Legacy</span>',
            $action,
            $update
        );
    }

    private function _build_legacy_api_preview_text($data) {
        $legacyValue = function ($value) {
            if (is_array($value) || is_object($value)) {
                $encodedValue = json_encode($value, JSON_UNESCAPED_UNICODE);
                return $encodedValue !== false && $encodedValue !== '[]' && $encodedValue !== '{}' ? $encodedValue : 'N/A';
            }

            return $value !== null && $value !== '' ? (string) $value : 'N/A';
        };

        $legacyLabel = function ($key) {
            $label = preg_replace('/^(cmd|resp|res|err|error)_/i', '', (string) $key);
            $label = preg_replace('/(?<!^)([A-Z])/', ' $1', $label);
            $label = trim(str_replace('_', ' ', $label));
            $lowerLabel = strtolower(preg_replace('/\s+/', ' ', $label));
            $labelMap = array(
                'itemid' => 'Item ID',
                'ui id' => 'UI ID',
                'ui type' => 'UI Type',
                'ui page no' => 'UI Page No',
                'cct' => 'CCT'
            );

            return isset($labelMap[$lowerLabel]) ? $labelMap[$lowerLabel] : ucwords($label);
        };

        $legacyRows = is_object($data) ? get_object_vars($data) : (array) $data;
        $groups = array(
            'product' => array(),
            'command' => array(),
            'response' => array(),
            'error' => array()
        );

        foreach ($legacyRows as $key => $value) {
            if (preg_match('/^cmd_/i', $key)) {
                $groups['command'][$key] = $value;
            } elseif (preg_match('/^(resp|res)_/i', $key)) {
                $groups['response'][$key] = $value;
            } elseif (preg_match('/^(err|error)_/i', $key)) {
                $groups['error'][$key] = $value;
            } else {
                $groups['product'][$key] = $value;
            }
        }

        $lines = array();
        $lines[] = '====================================================';
        $lines[] = '         LEGACY PRODUCT API SUMMARY';
        $lines[] = '====================================================';

        foreach ($groups['product'] as $key => $value) {
            $lines[] = $legacyLabel($key) . ': ' . $legacyValue($value);
        }

        $sectionMap = array(
            'command' => 'COMMAND FRAMES',
            'response' => 'RESPONSE FRAMES',
            'error' => 'ERROR FRAMES'
        );

        foreach ($sectionMap as $groupKey => $sectionTitle) {
            $lines[] = '';
            $lines[] = '[SECTION] ' . $sectionTitle . ' (1)';
            $lines[] = '--- Frame #1 ---';

            if (!empty($groups[$groupKey])) {
                foreach ($groups[$groupKey] as $key => $value) {
                    $lines[] = $legacyLabel($key) . ': ' . $legacyValue($value);
                }
            } else {
                $lines[] = 'Frame Data: N/A';
            }
        }

        return implode("\n", $lines);
    }

    private function _build_api_tree_preview_text($tree) {
        $config = $tree['config'];
        $frames = $tree['frames'];
        $fieldsByFrame = $tree['fields_by_frame'];
        $fieldValue = function ($row, $key, $fallback = 'N/A') {
            if (!isset($row[$key]) || $row[$key] === null || $row[$key] === '') {
                return $fallback;
            }

            return $row[$key];
        };

        $lines = array();
        $lines[] = '====================================================';
        $lines[] = '         FULL PRODUCT CONFIGURATION SUMMARY';
        $lines[] = '====================================================';
        $lines[] = 'DATABASE CONFIG ID : ' . $config['id'];
        $lines[] = 'USER ID            : ' . ($config['user_id'] !== null ? $config['user_id'] : 'N/A');
        $lines[] = 'ITEM ID            : ' . ($config['item_id'] !== null ? $config['item_id'] : 'N/A');
        $lines[] = 'BRAND NAME         : ' . ($config['brand_name'] ?: 'N/A');
        $lines[] = 'PRODUCT NAME       : ' . ($config['product_name'] ?: 'N/A');
        $lines[] = 'MODEL NAME         : ' . ($config['model_name'] ?: 'N/A');
        $lines[] = 'DATE               : ' . ($config['config_date'] ?: 'N/A');
        $lines[] = 'PARAMETER NAME     : ' . ($config['parameter_name'] ?: 'N/A');
        $lines[] = 'PAGE NUMBER        : ' . ($config['page_number'] ?: 'N/A');
        $lines[] = 'SEQUENCE NO        : ' . ($config['sequence_no'] ?: 'N/A');
        $lines[] = 'DESCRIPTION        : ' . ($config['description'] ?: 'N/A');
        $lines[] = '----------------------------------------------------';

        $actualCounts = array('command' => 0, 'response' => 0, 'error' => 0);
        foreach ($frames as $frame) {
            if (isset($actualCounts[$frame['frame_group']])) {
                $actualCounts[$frame['frame_group']]++;
            }
        }

        $lines[] = 'NO. OF COMMAND FRAMES  : ' . ($config['command_frame_count'] !== null ? $config['command_frame_count'] : $actualCounts['command']);
        $lines[] = 'NO. OF RESPONSE FRAMES : ' . ($config['response_frame_count'] !== null ? $config['response_frame_count'] : $actualCounts['response']);
        $lines[] = 'NO. OF ERROR FRAMES    : ' . ($config['error_frame_count'] !== null ? $config['error_frame_count'] : $actualCounts['error']);
        $lines[] = '----------------------------------------------------';

        $groupTitles = array(
            'command' => 'COMMAND FRAMES',
            'response' => 'RESPONSE FRAMES',
            'error' => 'ERROR FRAMES'
        );

        foreach ($groupTitles as $group => $title) {
            $groupFrames = array();
            foreach ($frames as $frame) {
                if ($frame['frame_group'] === $group) {
                    $groupFrames[] = $frame;
                }
            }

            $lines[] = '';
            $lines[] = '[SECTION] ' . $title . ' (' . count($groupFrames) . ')';

            foreach ($groupFrames as $frame) {
                $lines[] = '--- Frame #' . $frame['frame_index'] . ' | ID ' . $frame['id'] . ' ---';
                $lines[] = 'Parameter Name      : ' . ($frame['parameter_name'] ?: 'N/A');
                $lines[] = 'Data Flow Direction : ' . ($frame['data_flow_direction'] ?: 'N/A');
                $lines[] = 'Frame Type          : ' . ($frame['frame_type'] ?: 'N/A');
                $lines[] = 'Frame Size Bytes    : ' . ($frame['frame_size_bytes'] ?: 'N/A');
                $lines[] = 'No. of Rows         : ' . ($frame['no_of_rows'] ?: '0');

                $frameFields = isset($fieldsByFrame[(int) $frame['id']]) ? $fieldsByFrame[(int) $frame['id']] : array();

                foreach ($frameFields as $field) {
                    $prefix = $field['field_level'] === 'bitwise' ? '  BITWISE' : '  BYTEWISE';
                    $parent = $field['parent_field_id'] ? ' | parent_field_id=' . $field['parent_field_id'] : '';
                    $lines[] = $prefix . ' ROW ID ' . $field['id'] . $parent;
                    $lines[] = '    Row Sequence       : ' . ($field['row_sequence'] ?: 'N/A');
                    $lines[] = '    Sub Field Sequence : ' . ($field['sub_field_sequence'] ?: 'N/A');
                    $lines[] = '    Description        : ' . ($field['field_description'] ?: 'N/A');
                    $lines[] = '    Byte Range         : ' . (($field['start_byte'] !== null ? $field['start_byte'] : 'NULL') . ' - ' . ($field['end_byte'] !== null ? $field['end_byte'] : 'NULL'));
                    $lines[] = '    Bit Range          : ' . (($field['start_bit'] !== null ? $field['start_bit'] : 'NULL') . ' - ' . ($field['end_bit'] !== null ? $field['end_bit'] : 'NULL'));
                    $lines[] = '    No. Bytes / Bits   : ' . (($field['no_of_bytes'] !== null ? $field['no_of_bytes'] : 'NULL') . ' / ' . ($field['bit_size'] !== null ? $field['bit_size'] : 'NULL'));
                    $lines[] = '    Bitwise Mode       : ' . $fieldValue($field, 'bitwise_mode');
                    $lines[] = '    Data Type          : ' . ($field['data_type'] ?: 'N/A');
                    $lines[] = '    Value Definition   : ' . ($field['value_definition'] ?: 'N/A');
                    $lines[] = '    Display Value Min  : ' . $fieldValue($field, 'display_value_min');
                    $lines[] = '    Display Value Max  : ' . $fieldValue($field, 'display_value_max');
                    $lines[] = '    Value In Hex Min   : ' . $fieldValue($field, 'value_in_hex_min');
                    $lines[] = '    Value In Hex Max   : ' . $fieldValue($field, 'value_in_hex_max');
                    $lines[] = '    UI Type            : ' . ($field['ui_type'] ?: 'N/A');
                    $lines[] = '    UI ID              : ' . $fieldValue($field, 'ui_id');
                    $lines[] = '    AS PER UI          : ' . $fieldValue($field, 'as_per_ui');
                    $lines[] = '    UI Serial No       : ' . $fieldValue($field, 'ui_serial_no');
                    $lines[] = '    UI Values          : ' . $fieldValue($field, 'ui_values');
                    $lines[] = '    UI Page No         : ' . ($field['ui_page_no'] ?: 'N/A');
                    $lines[] = '    UI In/Out          : ' . $fieldValue($field, 'ui_description_in_out');
                    $lines[] = '    Slider Step Value  : ' . $fieldValue($field, 'slider_step_value');
                    $lines[] = '    Default Value      : ' . ($field['default_value'] ?: 'N/A');
                    $lines[] = '    Developer Notes    : ' . $fieldValue($field, 'developer_notes');
                    $lines[] = '    Notification Enable: ' . $fieldValue($field, 'notification_enable');
                    $lines[] = '    Notification Msg   : ' . $fieldValue($field, 'notification_message');
                    $lines[] = '    Scaling            : ' . $fieldValue($field, 'scaling');
                }

                $lines[] = '----------------------------------------------------';
            }
        }

        $lines[] = '';
        $lines[] = '================ END OF REPORT ================';

        return implode("\n", $lines);
    }
}
