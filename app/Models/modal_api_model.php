<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model for the Add/Edit API modal only.
 * Save, replace, delete, and edit loading for modal_api live here.
 */
class Modal_api_model extends Model
{
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $useTimestamps = false;

    public function __construct()
    {
        parent::__construct();
    }

    function default_form_values($itemid, $user_id = null) {
    $itemid = (int) $itemid;
    $table = $this->db->prefixTable('vendorproducts');
    $fields = $this->db->getFieldNames('vendorproducts');
    $fieldMap = array();

    foreach ($fields as $field) {
        $fieldMap[strtolower($field)] = $field;
    }

    $pickField = function ($candidates) use ($fieldMap) {
        foreach ($candidates as $candidate) {
            $key = strtolower($candidate);
            if (isset($fieldMap[$key])) {
                return $fieldMap[$key];
            }
        }

        return null;
    };

    $selectField = function ($candidates, $alias, $default = '') use ($pickField) {
        $field = $pickField($candidates);

        if ($field) {
            return "`" . $field . "` AS `" . $alias . "`";
        }

        return "'" . addslashes($default) . "' AS `" . $alias . "`";
    };

    $idField = $pickField(array('id', 'vendorproduct_id', 'vendor_product_id'));
    $userField = $pickField(array('user_id', 'userid', 'userId', 'created_by'));

    $select = array(
        $idField ? "`" . $idField . "` AS `id`" : "0 AS `id`",
        $selectField(array('brand', 'brand_name'), 'brand'),
        $selectField(array('category', 'category_name', 'product_name', 'product_type'), 'category'),
        $selectField(array('modelname', 'model_name', 'modal_name', 'name'), 'modelname'),
        $selectField(array('modelno', 'model_no', 'model_number'), 'modelno'),
        $selectField(array('model_version', 'version'), 'version', 'N/A'),
        $selectField(array('Batch', 'batch'), 'batch', 'N/A')
    );

    if (!$idField) {
        return $this->db->query("SELECT " . implode(", ", $select) . " FROM $table WHERE 1 = 0");
    }

    $where = array("`" . $idField . "`='$itemid'");
    if ($user_id !== null && $user_id !== '' && $userField) {
        $where[] = "`" . $userField . "` = " . $this->db->escape($user_id);
    }

    $sql = "SELECT " . implode(", ", $select) . " FROM $table where " . implode(" AND ", $where);
    return $this->db->query($sql);    
    }


public function extract_api_default_names($user_id, $modelno)
{
    $sql = "SELECT cmd_defaultvalue, resp_defaultvalue FROM product_api WHERE model_name = ?";
    $query = $this->db->query($sql, [$modelno]);

    $results = $query->getResultArray();
    $paramNames = [];

    foreach ($results as $row) {
        // Explode comma-separated values if needed
        $values = explode(',', $row['cmd_defaultvalue']);
        
        // Clean and filter out empty entries
        foreach ($values as $val) {
            $cleaned = trim($val);
            if ($cleaned !== '' && !in_array($cleaned, $paramNames)) {
                $paramNames[] = $cleaned;
            }
        }
    }

    return $paramNames;
}


   public function saveApiTreeConfig($mainData, $allData, $user_id = null)
    {
        if (!is_array($mainData)) {
            $mainData = array();
        }
    
        if (!is_array($allData)) {
            throw new \Exception('Invalid API configuration JSON.');
        }
    
       // TEMP FIX: Do not use CI transaction here because it is rolling back silently.
    // We are using explicit insert checks below for each table.
    $dbNameRow = $this->db->query('SELECT DATABASE() AS db_name')->getRowArray();
    log_message('error', 'API TREE SAVE CONNECTED DB: ' . (isset($dbNameRow['db_name']) ? $dbNameRow['db_name'] : 'UNKNOWN'));
    
        $configData = array(
            'user_id'          => $this->apiTreeCleanInt($user_id),
            'item_id'          => $this->apiTreeCleanInt($this->apiTreeArrayValue($mainData, 'itemid')),
            'brand_name'       => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'brandname')),
            'product_name'     => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'productname')),
            'model_name'       => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'modelname')),
            'config_date'      => $this->apiTreeCleanDate($this->apiTreeArrayValue($mainData, 'date')),
            'parameter_name'   => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'parameter_name')),
            'page_number'      => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'page_number')),
            'sequence_no'      => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'sequence_number')),
            'command_frame_count'  => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'command_frame_count', count(isset($allData['commands']) && is_array($allData['commands']) ? $allData['commands'] : array()))),
            'response_frame_count' => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'response_frame_count', count(isset($allData['responses']) && is_array($allData['responses']) ? $allData['responses'] : array()))),
            'error_frame_count'    => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'error_frame_count', count(isset($allData['errors']) && is_array($allData['errors']) ? $allData['errors'] : array()))),
            'description'      => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'product_description')),
            'raw_payload_json' => json_encode($allData, JSON_UNESCAPED_UNICODE)
        );
    
        $insertMain = $this->db->table('api_product_configs')->insert($configData);
    
        if (!$insertMain) {
            $dbError = $this->db->error();
    
            throw new \Exception(
                'Failed to insert api_product_configs. DB Error: ' .
                (isset($dbError['message']) ? $dbError['message'] : 'Unknown database error')
            );
        }
    
        $configId = (int) $this->db->insertID();
    
        if (!$configId) {
            throw new \Exception('Failed to resolve api_product_configs insert id.');
        }
    
        $frameGroups = array(
            'commands'  => 'command',
            'responses' => 'response',
            'errors'    => 'error'
        );
    
        foreach ($frameGroups as $jsonKey => $frameGroup) {
            $frames = isset($allData[$jsonKey]) && is_array($allData[$jsonKey]) ? $allData[$jsonKey] : array();
    
            foreach ($frames as $frameIndex => $frame) {
                if (!is_array($frame)) {
                    continue;
                }
    
                $meta = isset($frame['meta']) && is_array($frame['meta']) ? $frame['meta'] : array();
                $rows = isset($frame['rows']) && is_array($frame['rows']) ? $frame['rows'] : array();
                $frameMeta = array(
                    'parametername'      => $this->apiTreeFrameFirst($meta, $frame, array('parametername', 'parameter_name', 'parameterName'), $configData['parameter_name']),
                    'noofrows'           => $this->apiTreeFrameFirst($meta, $frame, array('noofrows', 'no_of_rows', 'noOfRows'), count($rows)),
                    'remarks'            => $this->apiTreeFrameFirst($meta, $frame, array('remarks', 'remark')),
                    'dataflowdirection'  => $this->apiTreeFrameFirst($meta, $frame, array('dataflowdirection', 'data_flow_direction', 'direction')),
                    'frametype'          => $this->apiTreeFrameFirst($meta, $frame, array('frametype', 'frame_type', 'type')),
                    'framesize_bytes'    => $this->apiTreeFrameFirst($meta, $frame, array('framesize_bytes', 'frame_size_bytes', 'framesizeinbytes', 'size')),
                    'frametrigger'       => $this->apiTreeFrameFirst($meta, $frame, array('frametrigger', 'frame_trigger', 'triggerOptions')),
                    'multipleui'         => $this->apiTreeFrameFirst($meta, $frame, array('multipleui', 'multiple_ui')),
                    'triggerui'          => $this->apiTreeFrameFirst($meta, $frame, array('triggerui', 'trigger_ui')),
                    'voicecommand'       => $this->apiTreeFrameFirst($meta, $frame, array('voicecommand', 'voice_command')),
                    'voicecontrol'       => $this->apiTreeFrameFirst($meta, $frame, array('voicecontrol', 'voice_control'))
                );
    
                $frameData = array(
                    'config_id'           => $configId,
                    'frame_group'         => $frameGroup,
                    'frame_index'         => (int) $frameIndex,
                    'parameter_name'      => $this->apiTreeCleanValue($frameMeta['parametername']),
                    'no_of_rows'          => $this->apiTreeCleanValue($frameMeta['noofrows']),
                    'remarks'             => $this->apiTreeCleanValue($frameMeta['remarks']),
                    'data_flow_direction' => $this->apiTreeCleanValue($frameMeta['dataflowdirection']),
                    'frame_type'          => $this->apiTreeCleanValue($frameMeta['frametype']),
                    'frame_size_bytes'    => $this->apiTreeCleanValue($frameMeta['framesize_bytes']),
                    'frame_trigger'       => $this->apiTreeCleanValue($frameMeta['frametrigger']),
                    'multiple_ui'         => $this->apiTreeCleanValue($frameMeta['multipleui']),
                    'trigger_ui'          => $this->apiTreeCleanValue($frameMeta['triggerui']),
                    'voice_command'       => $this->apiTreeCleanValue($frameMeta['voicecommand']),
                    'voice_control'       => $this->apiTreeCleanValue($frameMeta['voicecontrol']),
                    'raw_meta_json'       => json_encode(!empty($meta) ? $meta : $frameMeta, JSON_UNESCAPED_UNICODE)
                );
    
                $insertFrame = $this->db->table('api_frames')->insert($frameData);
    
                if (!$insertFrame) {
                    $dbError = $this->db->error();
    
                    throw new \Exception(
                        'Failed to insert api_frames. DB Error: ' .
                        (isset($dbError['message']) ? $dbError['message'] : 'Unknown database error')
                    );
                }
    
                $frameId = (int) $this->db->insertID();
    
                if (!$frameId) {
                    throw new \Exception('Failed to resolve api_frames insert id.');
                }
    
                foreach ($rows as $rowIndex => $row) {
                    if (!is_array($row)) {
                        continue;
                    }
    
                    $parentFieldId = $this->insertApiTreeField(
                        $frameId,
                        null,
                        'bytewise',
                        $row,
                        $rowIndex + 1,
                        null,
                        $frameGroup
                    );
    
                    if (!empty($row['bitwise']) && is_array($row['bitwise'])) {
                        foreach ($row['bitwise'] as $bitIndex => $bitRow) {
                            if (!is_array($bitRow)) {
                                continue;
                            }
    
                            $this->insertApiTreeField(
                                $frameId,
                                $parentFieldId,
                                'bitwise',
                                $bitRow,
                                $rowIndex + 1,
                                $bitIndex + 1,
                                $frameGroup
                            );
                        }
                    }
                }
            }
        }
    
      
    
    return $configId;
    }


    public function getApiTreeSaveCheck($configId)
    {
        $configId = (int) $configId;
    
        $dbNameRow = $this->db->query('SELECT DATABASE() AS db_name')->getRowArray();
    
        $connectedDb = isset($dbNameRow['db_name']) ? $dbNameRow['db_name'] : 'UNKNOWN';
    
        $mainRow = $this->db->table('api_product_configs')
            ->where('id', $configId)
            ->get()
            ->getRowArray();
    
        $frameRows = $this->db->table('api_frames')
            ->select('id')
            ->where('config_id', $configId)
            ->get()
            ->getResultArray();
    
        $frameIds = array();
    
        foreach ($frameRows as $frameRow) {
            if (isset($frameRow['id'])) {
                $frameIds[] = (int) $frameRow['id'];
            }
        }
    
        $fieldCount = 0;
    
        if (!empty($frameIds)) {
            $fieldCount = $this->db->table('api_frame_fields')
                ->whereIn('frame_id', $frameIds)
                ->countAllResults();
        }
    
        return array(
            'connected_database' => $connectedDb,
            'config_id'          => $configId,
            'main_row_found'     => !empty($mainRow) ? 1 : 0,
            'frame_rows_saved'   => count($frameRows),
            'field_rows_saved'   => $fieldCount
        );
    }


public function getApiTreeConfigList($itemId = 0, $userId = null, $modelName = '')
{
    $itemId = (int) $itemId;
    $modelName = trim((string) $modelName);

    $builder = $this->db->table('api_product_configs p');
    $builder->select("
        p.id,
        p.user_id,
        p.item_id,
        p.brand_name,
        p.product_name,
        p.model_name,
        p.config_date,
        p.parameter_name,
        p.page_number,
        p.sequence_no,
        p.command_frame_count,
        p.response_frame_count,
        p.error_frame_count,
        p.created_at,
        COUNT(DISTINCT CASE WHEN f.frame_group = 'command' THEN f.id END) AS actual_command_frames,
        COUNT(DISTINCT CASE WHEN f.frame_group = 'response' THEN f.id END) AS actual_response_frames,
        COUNT(DISTINCT CASE WHEN f.frame_group = 'error' THEN f.id END) AS actual_error_frames,
        COUNT(DISTINCT f.id) AS total_frames,
        COUNT(ff.id) AS total_fields
    ", false);
    $builder->join('api_frames f', 'f.config_id = p.id', 'left');
    $builder->join('api_frame_fields ff', 'ff.frame_id = f.id', 'left');

    $this->applyApiViewScope($builder, 'p.item_id', 'p.model_name', 'p.user_id', $itemId, $userId, $modelName);

    $builder->groupBy('p.id');
    $builder->orderBy('p.id', 'DESC');

    return $builder->get();
}


public function getApiTreeConfigForEdit($configId, $userId = null)
{
    $tree = $this->getApiTreeConfigFull($configId);

    if (!$tree) {
        return null;
    }

    // Ensure the configuration belongs to the current user.
    if (
        $userId !== null &&
        $userId !== '' &&
        (int) $tree['config']['user_id'] !== (int) $userId
    ) {
        return null;
    }

    $payload = array(
        'commands'  => array(),
        'responses' => array(),
        'errors'    => array()
    );

    /*
     * Load the complete original frontend data.
     * This contains:
     * - Command/Response/Error values
     * - Frame cards
     * - Partitions
     * - Bytewise configuration
     * - Bitwise configuration
     */
    if (!empty($tree['config']['raw_payload_json'])) {
        $decoded = json_decode(
            $tree['config']['raw_payload_json'],
            true
        );

        if (
            json_last_error() === JSON_ERROR_NONE &&
            is_array($decoded)
        ) {
            $payload = array_merge($payload, $decoded);
        }
    }

    $payloadProduct = (
        isset($payload['product']) &&
        is_array($payload['product'])
    ) ? $payload['product'] : array();

    // Reconstruct data from normalized database rows.
    $dbPayload = $this->buildApiTreeEditPayloadFromDb($tree);

    /*
     * Keep raw_payload_json as the primary source.
     *
     * Database-reconstructed rows are used only when a complete frame group
     * is missing from raw_payload_json.
     *
     * Do not use:
     *
     * $payload = array_merge($payload, $dbPayload);
     *
     * That would overwrite the original Command Value, partitions and
     * bytewise/bitwise configurations.
     */
    foreach (array('commands', 'responses', 'errors') as $groupKey) {
        $hasSavedFrames = (
            isset($payload[$groupKey]) &&
            is_array($payload[$groupKey]) &&
            !empty($payload[$groupKey])
        );

        if (
            !$hasSavedFrames &&
            isset($dbPayload[$groupKey]) &&
            is_array($dbPayload[$groupKey])
        ) {
            $payload[$groupKey] = $dbPayload[$groupKey];
        }
    }

    $pageNames = '';

    foreach (
        array('page_names', 'pageNames', 'pagenames')
        as $pageNameKey
    ) {
        if (
            isset($payloadProduct[$pageNameKey]) &&
            $payloadProduct[$pageNameKey] !== ''
        ) {
            $pageNames = $payloadProduct[$pageNameKey];
            break;
        }
    }

    $commandFrameCount = max(
        (int) ($tree['config']['command_frame_count'] ?? 0),
        count($payload['commands'] ?? array()),
        1
    );

    $responseFrameCount = max(
        (int) ($tree['config']['response_frame_count'] ?? 0),
        count($payload['responses'] ?? array()),
        1
    );

    $errorFrameCount = max(
        (int) ($tree['config']['error_frame_count'] ?? 0),
        count($payload['errors'] ?? array()),
        1
    );

    return array(
        'config_id' => (int) $tree['config']['id'],

        'product' => array(
            'itemid'               => $tree['config']['item_id'],
            'brandname'            => $tree['config']['brand_name'],
            'productname'          => $tree['config']['product_name'],
            'modelname'            => $tree['config']['model_name'],
            'date'                 => $tree['config']['config_date'],
            'parameter_name'       => $tree['config']['parameter_name'],
            'page_number'          => $tree['config']['page_number'],
            'sequence_number'      => $tree['config']['sequence_no'],
            'command_frame_count'  => $commandFrameCount,
            'response_frame_count' => $responseFrameCount,
            'error_frame_count'    => $errorFrameCount,
            'page_names'           => $pageNames,
            'product_description'  => $tree['config']['description']
        ),

        'payload' => $payload
    );
}


private function buildApiTreeEditPayloadFromDb($tree)
{
    $payload = array('commands' => array(), 'responses' => array(), 'errors' => array());
    $groupMap = array('command' => 'commands', 'response' => 'responses', 'error' => 'errors');
    $fieldsByFrame = isset($tree['fields_by_frame']) && is_array($tree['fields_by_frame']) ? $tree['fields_by_frame'] : array();

    foreach ($tree['frames'] as $frame) {
        $group = isset($frame['frame_group']) ? $frame['frame_group'] : '';
        if (!isset($groupMap[$group])) {
            continue;
        }

        $frameId = (int) $frame['id'];
        $frameIndex = isset($frame['frame_index']) ? (int) $frame['frame_index'] : count($payload[$groupMap[$group]]);
        $fieldRows = isset($fieldsByFrame[$frameId]) ? $fieldsByFrame[$frameId] : array();

        $byteRows = array();
        $bitRowsByParent = array();
        $bitRowsBySequence = array();

        foreach ($fieldRows as $field) {
            if (($field['field_level'] ?? '') === 'bitwise') {
                $parentId = isset($field['parent_field_id']) ? (int) $field['parent_field_id'] : 0;
                if ($parentId) {
                    if (!isset($bitRowsByParent[$parentId])) $bitRowsByParent[$parentId] = array();
                    $bitRowsByParent[$parentId][] = $this->apiTreeDbFieldToEditBitRow($field);
                }

                $sequence = isset($field['row_sequence']) ? (int) $field['row_sequence'] : 0;
                if (!isset($bitRowsBySequence[$sequence])) $bitRowsBySequence[$sequence] = array();
                $bitRowsBySequence[$sequence][] = $this->apiTreeDbFieldToEditBitRow($field);
            } else {
                $byteRows[] = $field;
            }
        }

        usort($byteRows, function ($a, $b) {
            return ((int) ($a['row_sequence'] ?? 0)) <=> ((int) ($b['row_sequence'] ?? 0));
        });

        $rows = array();
        if (empty($byteRows) && !empty($bitRowsBySequence)) {
            foreach ($bitRowsBySequence as $sequence => $bitRows) {
                $rows[] = array(
                    'bitwise' => $bitRows,
                    'bitwiseMode' => 'Bitwise',
                    'cmd_bitwiseMode' => 'Bitwise',
                    'resp_bitwiseMode' => 'Bitwise',
                    'err_bitwiseMode' => 'Bitwise'
                );
            }
        }

        foreach ($byteRows as $byteField) {
            $row = $this->apiTreeDbFieldToEditRow($byteField);
            $fieldId = isset($byteField['id']) ? (int) $byteField['id'] : 0;
            $sequence = isset($byteField['row_sequence']) ? (int) $byteField['row_sequence'] : 0;
            $bitRows = isset($bitRowsByParent[$fieldId]) ? $bitRowsByParent[$fieldId] : (isset($bitRowsBySequence[$sequence]) ? $bitRowsBySequence[$sequence] : array());

            if (!empty($bitRows)) {
                $row['bitwise'] = $bitRows;
                $row['bitwiseMode'] = 'Bitwise';
                $row['cmd_bitwiseMode'] = 'Bitwise';
                $row['resp_bitwiseMode'] = 'Bitwise';
                $row['err_bitwiseMode'] = 'Bitwise';
            }

            $rows[] = $row;
        }

        $payload[$groupMap[$group]][$frameIndex] = array(
            'parameterName' => $frame['parameter_name'] ?? '',
            'direction' => $frame['data_flow_direction'] ?? '',
            'size' => $frame['frame_size_bytes'] ?? '',
            'triggerOptions' => $frame['frame_trigger'] ?? 'None',
            'meta' => array(
                'parametername' => $frame['parameter_name'] ?? '',
                'noofrows' => $frame['no_of_rows'] ?? count($rows),
                'remarks' => $frame['remarks'] ?? '',
                'dataflowdirection' => $frame['data_flow_direction'] ?? '',
                'frametype' => $frame['frame_type'] ?? '',
                'framesize_bytes' => $frame['frame_size_bytes'] ?? '',
                'frametrigger' => $frame['frame_trigger'] ?? '',
                'multipleui' => $frame['multiple_ui'] ?? '',
                'triggerui' => $frame['trigger_ui'] ?? '',
                'voicecommand' => $frame['voice_command'] ?? '',
                'voicecontrol' => $frame['voice_control'] ?? ''
            ),
            'rows' => $rows
        );
    }

    foreach ($payload as $key => $frames) {
        ksort($frames);
        $payload[$key] = array_values($frames);
    }

    return $payload;
}

private function apiTreeDbFieldToEditRow($field)
{
    return array(
        'bitwiseMode' => $field['bitwise_mode'] ?? 'Bytewise',
        'startBytePosition' => $field['start_byte'] ?? '',
        'endBytePosition' => $field['end_byte'] ?? '',
        'numberOfBytes' => $field['no_of_bytes'] ?? '',
        'bitSize' => $field['bit_size'] ?? '',
        'fieldDescription' => $field['field_description'] ?? '',
        'dataType' => $field['data_type'] ?? '',
        'valueDefinition' => $field['value_definition'] ?? '',
        'displayValueMin' => $field['display_value_min'] ?? '',
        'displayValueMax' => $field['display_value_max'] ?? '',
        'valueInHexMin' => $field['value_in_hex_min'] ?? '',
        'valueInHexMax' => $field['value_in_hex_max'] ?? '',
        'uiType' => $field['ui_type'] ?? '',
        'uiId' => $field['ui_id'] ?? '',
        'asperui' => $field['as_per_ui'] ?? '',
        'sno_asperui' => $field['ui_serial_no'] ?? '',
        'asperuivalue' => $field['ui_values'] ?? '',
        'uiPageNo' => $field['ui_page_no'] ?? '',
        'uiDescriptionInOut' => $field['ui_description_in_out'] ?? '',
        'sliderStepValue' => $field['slider_step_value'] ?? '',
        'defaultvalue' => $field['default_value'] ?? '',
        'developerNotes' => $field['developer_notes'] ?? '',
        'notificationEnable' => $field['notification_enable'] ?? 'No',
        'notificationMessage' => $field['notification_message'] ?? '',
        'scaling' => $field['scaling'] ?? ''
    );
}

private function apiTreeDbFieldToEditBitRow($field)
{
    return array(
        'startBytePosition_1' => $field['start_bit'] ?? '',
        'endBytePosition_1' => $field['end_bit'] ?? '',
        'numberOfBytes_1' => $field['bit_size'] ?? '',
        'bitSize_1' => $field['bit_size'] ?? '',
        'fieldDescription_1' => $field['field_description'] ?? '',
        'dataType_1' => $field['data_type'] ?? '',
        'valueDefinition_1' => $field['value_definition'] ?? '',
        'displayValueMin_1' => $field['display_value_min'] ?? '',
        'displayValueMax_1' => $field['display_value_max'] ?? '',
        'valueInHexMin_1' => $field['value_in_hex_min'] ?? '',
        'valueInHexMax_1' => $field['value_in_hex_max'] ?? '',
        'uiType_1' => $field['ui_type'] ?? '',
        'uiId_1' => $field['ui_id'] ?? '',
        'asperui_1' => $field['as_per_ui'] ?? '',
        'sno_asperui_1' => $field['ui_serial_no'] ?? '',
        'asperuivalue_1' => $field['ui_values'] ?? '',
        'uiPageNo_1' => $field['ui_page_no'] ?? '',
        'uiDescriptionInOut_1' => $field['ui_description_in_out'] ?? '',
        'sliderStepValue_1' => $field['slider_step_value'] ?? '',
        'defaultvalue_1' => $field['default_value'] ?? '',
        'developerNotes_1' => $field['developer_notes'] ?? '',
        'notifEnable_1' => $field['notification_enable'] ?? 'No',
        'notifMsg_1' => $field['notification_message'] ?? '',
        'scaling_1' => $field['scaling'] ?? ''
    );
}


 public function replaceApiTreeConfig($configId, $mainData, $allData, $user_id = null)
    {
        $configId = (int) $configId;
    
        if (!$configId) {
            return $this->saveApiTreeConfig($mainData, $allData, $user_id);
        }
    
        $existing = $this->db->table('api_product_configs')
            ->where('id', $configId)
            ->get()
            ->getRowArray();
    
        if (!$existing) {
            throw new \Exception('API config not found for update.');
        }
    
        if ($user_id !== null && $user_id !== '' && (int) $existing['user_id'] !== (int) $user_id) {
            throw new \Exception('You do not have permission to update this API config.');
        }
    
        $frames = $this->db->table('api_frames')
            ->select('id')
            ->where('config_id', $configId)
            ->get()
            ->getResultArray();
    
        $frameIds = array();
        foreach ($frames as $frame) {
            $frameIds[] = (int) $frame['id'];
        }
    
        if (!empty($frameIds)) {
            $this->db->table('api_frame_fields')->whereIn('frame_id', $frameIds)->delete();
            $this->db->table('api_frames')->whereIn('id', $frameIds)->delete();
        }
    
        $configData = array(
            'user_id'          => $this->apiTreeCleanInt($user_id),
            'item_id'          => $this->apiTreeCleanInt($this->apiTreeArrayValue($mainData, 'itemid')),
            'brand_name'       => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'brandname')),
            'product_name'     => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'productname')),
            'model_name'       => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'modelname')),
            'config_date'      => $this->apiTreeCleanDate($this->apiTreeArrayValue($mainData, 'date')),
            'parameter_name'   => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'parameter_name')),
            'page_number'      => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'page_number')),
            'sequence_no'      => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'sequence_number')),
            'command_frame_count'  => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'command_frame_count', count(isset($allData['commands']) && is_array($allData['commands']) ? $allData['commands'] : array()))),
            'response_frame_count' => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'response_frame_count', count(isset($allData['responses']) && is_array($allData['responses']) ? $allData['responses'] : array()))),
            'error_frame_count'    => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'error_frame_count', count(isset($allData['errors']) && is_array($allData['errors']) ? $allData['errors'] : array()))),
            'description'      => $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'product_description')),
            'raw_payload_json' => json_encode($allData, JSON_UNESCAPED_UNICODE)
        );
    
        $updated = $this->db->table('api_product_configs')
            ->where('id', $configId)
            ->update($configData);
    
        if (!$updated) {
            $dbError = $this->db->error();
            throw new \Exception('Failed to update api_product_configs. DB Error: ' . (isset($dbError['message']) ? $dbError['message'] : 'Unknown database error'));
        }
    
        $frameGroups = array(
            'commands'  => 'command',
            'responses' => 'response',
            'errors'    => 'error'
        );
    
        foreach ($frameGroups as $jsonKey => $frameGroup) {
            $frames = isset($allData[$jsonKey]) && is_array($allData[$jsonKey]) ? $allData[$jsonKey] : array();
    
            foreach ($frames as $frameIndex => $frame) {
                if (!is_array($frame)) {
                    continue;
                }
    
                $meta = isset($frame['meta']) && is_array($frame['meta']) ? $frame['meta'] : array();
                $rows = isset($frame['rows']) && is_array($frame['rows']) ? $frame['rows'] : array();
                $frameMeta = array(
                    'parametername'      => $this->apiTreeFrameFirst($meta, $frame, array('parametername', 'parameter_name', 'parameterName'), $configData['parameter_name']),
                    'noofrows'           => $this->apiTreeFrameFirst($meta, $frame, array('noofrows', 'no_of_rows', 'noOfRows'), count($rows)),
                    'remarks'            => $this->apiTreeFrameFirst($meta, $frame, array('remarks', 'remark')),
                    'dataflowdirection'  => $this->apiTreeFrameFirst($meta, $frame, array('dataflowdirection', 'data_flow_direction', 'direction')),
                    'frametype'          => $this->apiTreeFrameFirst($meta, $frame, array('frametype', 'frame_type', 'type')),
                    'framesize_bytes'    => $this->apiTreeFrameFirst($meta, $frame, array('framesize_bytes', 'frame_size_bytes', 'framesizeinbytes', 'size')),
                    'frametrigger'       => $this->apiTreeFrameFirst($meta, $frame, array('frametrigger', 'frame_trigger', 'triggerOptions')),
                    'multipleui'         => $this->apiTreeFrameFirst($meta, $frame, array('multipleui', 'multiple_ui')),
                    'triggerui'          => $this->apiTreeFrameFirst($meta, $frame, array('triggerui', 'trigger_ui')),
                    'voicecommand'       => $this->apiTreeFrameFirst($meta, $frame, array('voicecommand', 'voice_command')),
                    'voicecontrol'       => $this->apiTreeFrameFirst($meta, $frame, array('voicecontrol', 'voice_control'))
                );
    
                $frameData = array(
                    'config_id'           => $configId,
                    'frame_group'         => $frameGroup,
                    'frame_index'         => (int) $frameIndex,
                    'parameter_name'      => $this->apiTreeCleanValue($frameMeta['parametername']),
                    'no_of_rows'          => $this->apiTreeCleanValue($frameMeta['noofrows']),
                    'remarks'             => $this->apiTreeCleanValue($frameMeta['remarks']),
                    'data_flow_direction' => $this->apiTreeCleanValue($frameMeta['dataflowdirection']),
                    'frame_type'          => $this->apiTreeCleanValue($frameMeta['frametype']),
                    'frame_size_bytes'    => $this->apiTreeCleanValue($frameMeta['framesize_bytes']),
                    'frame_trigger'       => $this->apiTreeCleanValue($frameMeta['frametrigger']),
                    'multiple_ui'         => $this->apiTreeCleanValue($frameMeta['multipleui']),
                    'trigger_ui'          => $this->apiTreeCleanValue($frameMeta['triggerui']),
                    'voice_command'       => $this->apiTreeCleanValue($frameMeta['voicecommand']),
                    'voice_control'       => $this->apiTreeCleanValue($frameMeta['voicecontrol']),
                    'raw_meta_json'       => json_encode(!empty($meta) ? $meta : $frameMeta, JSON_UNESCAPED_UNICODE)
                );
    
                $insertFrame = $this->db->table('api_frames')->insert($frameData);
    
                if (!$insertFrame) {
                    $dbError = $this->db->error();
                    throw new \Exception('Failed to insert api_frames during update. DB Error: ' . (isset($dbError['message']) ? $dbError['message'] : 'Unknown database error'));
                }
    
                $frameId = (int) $this->db->insertID();
    
                foreach ($rows as $rowIndex => $row) {
                    if (!is_array($row)) {
                        continue;
                    }
    
                    $parentFieldId = $this->insertApiTreeField($frameId, null, 'bytewise', $row, $rowIndex + 1, null, $frameGroup);
    
                    if (!empty($row['bitwise']) && is_array($row['bitwise'])) {
                        foreach ($row['bitwise'] as $bitIndex => $bitRow) {
                            if (is_array($bitRow)) {
                                $this->insertApiTreeField($frameId, $parentFieldId, 'bitwise', $bitRow, $rowIndex + 1, $bitIndex + 1, $frameGroup);
                            }
                        }
                    }
                }
            }
        }
    
        return $configId;
    }


public function deleteApiTreeConfig($configId, $userId = null)
    {
        $configId = (int) $configId;
    
        if (!$configId) {
            return false;
        }
    
        $configBuilder = $this->db->table('api_product_configs')->where('id', $configId);
    
        if ($userId !== null && $userId !== '') {
            $configBuilder->where('user_id', (int) $userId);
        }
    
        $config = $configBuilder->get()->getRowArray();
    
        if (!$config) {
            return false;
        }
    
        $this->db->transStart();
    
        $frames = $this->db->table('api_frames')
            ->select('id')
            ->where('config_id', $configId)
            ->get()
            ->getResultArray();
    
        $frameIds = array();
        foreach ($frames as $frame) {
            $frameIds[] = (int) $frame['id'];
        }
    
        if (!empty($frameIds)) {
            $this->db->table('api_frame_fields')->whereIn('frame_id', $frameIds)->delete();
        }
    
        $this->db->table('api_frames')->where('config_id', $configId)->delete();
    
        $legacyDelete = $this->db->table('product_api')
            ->where('itemid', (int) ($config['item_id'] ?? 0))
            ->where('parameter_name', $config['parameter_name'] ?? '')
            ->where('model_name', $config['model_name'] ?? '')
            ->where('product_name', $config['product_name'] ?? '');
    
        if ($userId !== null && $userId !== '') {
            $legacyDelete->where('user_id', (int) $userId);
        }
    
        $legacyDelete->delete();
    
        $deleteConfig = $this->db->table('api_product_configs')->where('id', $configId);
        if ($userId !== null && $userId !== '') {
            $deleteConfig->where('user_id', (int) $userId);
        }
        $deleteConfig->delete();
    
        $this->db->transComplete();
    
        return $this->db->transStatus();
    }


    private function insertApiTreeField($frameId, $parentFieldId, $fieldLevel, $row, $rowSequence, $subFieldSequence, $frameGroup)
    {
        $prefixes = $this->apiTreePrefixes($frameGroup);
    
        if ($fieldLevel === 'bitwise') {
            $startByte = null;
            $endByte = null;
    
            $startBit = $this->apiTreeCleanValue($this->apiTreeFirst($row, array(
                'start_bit',
                'startBit',
                'startBytePosition_1',
                'cmd_startBytePosition_1',
                'resp_startBytePosition_1',
                'res_startBytePosition_1',
                'err_startBytePosition_1',
                'error_startBytePosition_1'
            )));
    
            $endBit = $this->apiTreeCleanValue($this->apiTreeFirst($row, array(
                'end_bit',
                'endBit',
                'endBytePosition_1',
                'cmd_endBytePosition_1',
                'resp_endBytePosition_1',
                'res_endBytePosition_1',
                'err_endBytePosition_1',
                'error_endBytePosition_1'
            )));
    
            $noOfBytes = null;
    
            $bitSize = $this->apiTreeCleanValue($this->apiTreeFirst($row, array(
                'bitSize_1',
                'numberOfBits_1',
                'numberOfBytes_1',
                'cmd_bitSize_1',
                'resp_bitSize_1',
                'res_bitSize_1',
                'err_bitSize_1',
                'error_bitSize_1'
            )));
        } else {
            $startByte = $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'startBytePosition'));
            $endByte = $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'endBytePosition'));
            $startBit = null;
            $endBit = null;
            $noOfBytes = $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'numberOfBytes'));
            $bitSize = $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'bitSize'));
        }
    
        $hasBitwiseRows = !empty($row['bitwise']) && is_array($row['bitwise']);
        $bitwiseMode = $fieldLevel === 'bitwise'
            ? 'Bitwise'
            : $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'bitwiseMode'));
    
        if ($fieldLevel === 'bytewise' && !$bitwiseMode) {
            $bitwiseMode = $hasBitwiseRows ? 'Bitwise' : 'Bytewise';
        }
    
        if ($fieldLevel === 'bytewise' && $bitwiseMode === 'Bitwise') {
            if ($this->apiTreeLooksLikeListValue($startByte)) {
                $startByte = null;
            }
            if ($this->apiTreeLooksLikeListValue($endByte)) {
                $endByte = null;
            }
            if ($this->apiTreeLooksLikeListValue($noOfBytes)) {
                $noOfBytes = null;
            }
            if ($this->apiTreeLooksLikeListValue($bitSize)) {
                $bitSize = null;
            }
        }
    
        $data = array(
            'frame_id'              => $frameId,
            'parent_field_id'       => $parentFieldId,
            'field_level'           => $fieldLevel,
            'row_sequence'          => $rowSequence,
            'sub_field_sequence'    => $subFieldSequence,
            'bitwise_mode'          => $bitwiseMode,
    
            'start_byte'            => $startByte,
            'end_byte'              => $endByte,
            'start_bit'             => $startBit,
            'end_bit'               => $endBit,
            'no_of_bytes'           => $noOfBytes,
            'bit_size'              => $bitSize,
    
            'field_description'     => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'fieldDescription')),
            'data_type'             => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'dataType')),
            'value_definition'      => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'valueDefinition')),
    
            'display_value_min'     => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'displayValueMin')),
            'display_value_max'     => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'displayValueMax')),
            'value_in_hex_min'      => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'valueInHexMin')),
            'value_in_hex_max'      => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'valueInHexMax')),
    
            'ui_type'               => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'uiType')),
            'ui_id'                 => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'uiId')),
            'as_per_ui'             => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'asperui')),
            'ui_serial_no'          => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'sno_asperui')),
            'ui_values'             => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'asperuivalue')),
            'ui_page_no'            => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'uiPageNo')),
            'ui_description_in_out' => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'uiDescriptionInOut')),
    
            'slider_step_value'     => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'sliderStepValue')),
            'default_value'         => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'defaultvalue')),
            'developer_notes'       => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'developerNotes')),
    
            'notification_enable'   => $this->apiTreeCleanValue($this->apiTreeFirst($row, array(
                'notificationEnable',
                'notifEnable_1',
                'cmd_notificationEnable',
                'resp_notificationEnable',
                'res_notificationEnable',
                'err_notificationEnable',
                'error_notificationEnable'
            ))),
    
            'notification_message'  => $this->apiTreeCleanValue($this->apiTreeFirst($row, array(
                'notificationMessage',
                'notifMsg_1',
                'cmd_notificationMessage',
                'resp_notificationMessage',
                'res_notificationMessage',
                'err_notificationMessage',
                'error_notificationMessage'
            ))),
    
            'scaling'               => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'scaling')),
            'raw_row_json'          => json_encode($row, JSON_UNESCAPED_UNICODE)
        );
    
        $insertField = $this->db->table('api_frame_fields')->insert($data);
    
        if (!$insertField) {
            $dbError = $this->db->error();
    
            throw new \Exception(
                'Failed to insert api_frame_fields. DB Error: ' .
                (isset($dbError['message']) ? $dbError['message'] : 'Unknown database error')
            );
        }
    
        $fieldId = (int) $this->db->insertID();
    
        if (!$fieldId) {
            throw new \Exception('Failed to resolve api_frame_fields insert id.');
        }
    
        return $fieldId;
    }







private function apiTreeNextId($tableName)
{
    $row = $this->db->table($tableName)
        ->select('MAX(id) AS max_id')
        ->get()
        ->getRowArray();

    $maxId = 0;

    if (isset($row['max_id']) && is_numeric($row['max_id'])) {
        $maxId = (int) $row['max_id'];
    }

    return $maxId + 1;
}


private function apiTreeGetLastId($tableName, $where)
{
    $insertId = (int) $this->db->insertID();

    if ($insertId > 0) {
        return $insertId;
    }

    $lastIdRow = $this->db->query('SELECT LAST_INSERT_ID() AS id')->getRowArray();

    if (isset($lastIdRow['id']) && (int) $lastIdRow['id'] > 0) {
        return (int) $lastIdRow['id'];
    }

    $builder = $this->db->table($tableName)->select('id');

    if (is_array($where) && !empty($where)) {
        foreach ($where as $key => $value) {
            if ($value === null) {
                $builder->where($key . ' IS NULL', null, false);
            } else {
                $builder->where($key, $value);
            }
        }
    }

    $row = $builder
        ->orderBy('id', 'DESC')
        ->limit(1)
        ->get()
        ->getRowArray();

    return isset($row['id']) ? (int) $row['id'] : 0;
}
   private function apiTreePrefixes($frameGroup)
    {
        if ($frameGroup === 'command') {
            return array('cmd');
        }
    
        if ($frameGroup === 'response') {
            return array('resp', 'res');
        }
    
        if ($frameGroup === 'error') {
            return array('err', 'error');
        }
    
        return array();
    }
    
    
    
    

private function apiTreePrefixedValue($row, $prefixes, $baseKey)
{
    $keys = array();

    foreach ($prefixes as $prefix) {
        $keys[] = $prefix . '_' . $baseKey;
        $keys[] = $prefix . '_' . $baseKey . '_1';
    }

    $keys[] = $baseKey;
    $keys[] = $baseKey . '_1';

    return $this->apiTreeFirst($row, $keys);
}

private function apiTreeFirst($row, $keys, $default = null)
{
    if (!is_array($row)) {
        return $default;
    }

    foreach ($keys as $key) {
        if (array_key_exists($key, $row) && $row[$key] !== '' && $row[$key] !== null) {
            return $row[$key];
        }
    }

    return $default;
}

private function apiTreeFrameFirst($meta, $frame, $keys, $default = null)
{
    $value = $this->apiTreeFirst($meta, $keys, null);

    if ($value !== null && $value !== '') {
        return $value;
    }

    return $this->apiTreeFirst($frame, $keys, $default);
}

private function apiTreeLooksLikeListValue($value)
{
    if (!is_string($value)) {
        return false;
    }

    $value = trim($value);

    return $value !== '' && $value[0] === '[';
}


    private function apiTreeArrayValue($array, $key, $default = null)
    {
        return is_array($array) && array_key_exists($key, $array) ? $array[$key] : $default;
    }


    private function apiTreeCleanValue($value)
    {
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
    
        if ($value === null) {
            return null;
        }
    
        $value = trim((string) $value);
    
        return $value === '' ? null : $value;
    }

    private function apiTreeCleanInt($value)
    {
        if (is_array($value)) {
            return null;
        }
    
        if ($value === null || $value === '') {
            return null;
        }
    
        $value = trim((string) $value);
    
        return is_numeric($value) ? (int) $value : null;
    }






private function apiTreeCleanDate($value)
    {
        $value = $this->apiTreeCleanValue($value);
    
        if (!$value || $value === 'N/A') {
            return null;
        }
    
        return $value;
    }


private function applyApiViewScope($builder, $itemColumn, $modelColumn, $userColumn, $itemId, $userId, $modelName)
{
    $itemId = (int) $itemId;
    $modelNames = $this->getModelNamesForItem($itemId, $modelName);
    $hasUser = ($userId !== null && $userId !== '');

    if ($itemId <= 0 && (empty($modelNames) || !$hasUser)) {
        $builder->where($itemColumn, -1);
        return $builder;
    }

    if ($itemId <= 0) {
        $builder->where($userColumn, (int) $userId);
        $builder->whereIn($modelColumn, $modelNames);
        return $builder;
    }

    $builder->groupStart();

    $builder->groupStart()
        ->where($itemColumn, $itemId);

    if ($hasUser) {
        $builder->groupStart()
            ->where($userColumn, (int) $userId)
            ->orWhere($userColumn, 0)
            ->orWhere($userColumn, null)
            ->groupEnd();
    }

    $builder->groupEnd();

    if (!empty($modelNames) && $hasUser) {
        $builder->orGroupStart()
            ->where($userColumn, (int) $userId)
            ->whereIn($modelColumn, $modelNames)
            ->groupStart()
                ->where($itemColumn, 0)
                ->orWhere($itemColumn, null)
            ->groupEnd()
            ->groupEnd();
    }

    $builder->groupEnd();

    return $builder;
}


public function getApiTreeConfigFull($configId)
{
    $configId = (int) $configId;

    $config = $this->db->table('api_product_configs')
        ->where('id', $configId)
        ->get()
        ->getRowArray();

    if (!$config) {
        return null;
    }

    $frames = $this->db->table('api_frames')
        ->where('config_id', $configId)
        ->orderBy('frame_group', 'ASC')
        ->orderBy('frame_index', 'ASC')
        ->orderBy('id', 'ASC')
        ->get()
        ->getResultArray();

    $frameIds = array();
    foreach ($frames as $frame) {
        $frameIds[] = (int) $frame['id'];
    }

    $fieldsByFrame = array();

    if (!empty($frameIds)) {
        $fields = $this->db->table('api_frame_fields')
            ->whereIn('frame_id', $frameIds)
            ->orderBy('frame_id', 'ASC')
            ->orderBy('row_sequence', 'ASC')
            ->orderBy('field_level', 'ASC')
            ->orderBy('sub_field_sequence', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($fields as $field) {
            $frameId = (int) $field['frame_id'];
            if (!isset($fieldsByFrame[$frameId])) {
                $fieldsByFrame[$frameId] = array();
            }
            $fieldsByFrame[$frameId][] = $field;
        }
    }

    return array(
        'config' => $config,
        'frames' => $frames,
        'fields_by_frame' => $fieldsByFrame
    );
}


    public function getProductMetaRow($itemId, $userId = null)
    {
        $result = $this->default_form_values($itemId, $userId);
        $row = $result ? $result->getRow() : null;

        if (!$row) {
            return null;
        }

        $row->brand_image = $this->lookupNamedImage(
            'brands',
            $row->brand ?? '',
            array('brandlogo', 'img_path', 'image'),
            $userId
        );
        $row->product_image = $this->lookupNamedImage(
            'productcats',
            $row->category ?? '',
            array('Product_image', 'img_path', 'image', 'prodimg'),
            $userId
        );

        return $row;
    }

    public function getConnectedDatabaseName()
    {
        $row = $this->db->query('SELECT DATABASE() AS db_name')->getRowArray();

        return isset($row['db_name']) && $row['db_name'] !== ''
            ? (string) $row['db_name']
            : 'UNKNOWN';
    }

    public function getCommandGetStatusResponseParameterNames($itemId, $userId = null)
    {
        $itemId = (int) $itemId;
        $names = array();

        if ($itemId <= 0) {
            return $names;
        }

        $builder = $this->db->table('api_product_configs')
            ->select('id, raw_payload_json, parameter_name')
            ->where('item_id', $itemId)
            ->where('parameter_name', 'Command Get Status')
            ->orderBy('id', 'DESC');

        if ($userId !== null && $userId !== '') {
            $builder->where('user_id', (int) $userId);
        }

        $configs = $builder->get()->getResultArray();

        if (!$configs && $userId !== null && $userId !== '') {
            $configs = $this->db->table('api_product_configs')
                ->select('id, raw_payload_json, parameter_name')
                ->where('item_id', $itemId)
                ->where('parameter_name', 'Command Get Status')
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();
        }

        foreach ($configs as $config) {
            $this->collectResponseFieldNames($config['raw_payload_json'] ?? '', $names);

            if (!$names) {
                $this->collectResponseFieldNamesFromDb((int) $config['id'], $names);
            }

            if ($names) {
                break;
            }
        }

        return array_values($names);
    }

    public function getModelNameForItem($itemId)
    {
        $itemId = (int) $itemId;
        if ($itemId <= 0) {
            return '';
        }

        $config = $this->db->table('api_product_configs')
            ->select('model_name')
            ->where('item_id', $itemId)
            ->where('model_name !=', '')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        if (!empty($config['model_name'])) {
            return trim((string) $config['model_name']);
        }

        $legacy = $this->db->table('product_api')
            ->select('model_name')
            ->where('itemid', $itemId)
            ->where('model_name !=', '')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        if (!empty($legacy['model_name'])) {
            return trim((string) $legacy['model_name']);
        }

        $vendor = $this->db->table('vendorproducts')
            ->where('id', $itemId)
            ->get()
            ->getRowArray();

        if (!empty($vendor)) {
            foreach (array('modelno', 'model_no', 'model_number', 'modelname', 'model_name') as $key) {
                $value = trim((string) ($vendor[$key] ?? ''));
                if ($value !== '') {
                    return $value;
                }
            }
        }

        return '';
    }

    public function getModelNamesForItem($itemId, $extraModelName = '')
    {
        $names = array();
        $add = function ($value) use (&$names) {
            $value = trim((string) $value);
            if ($value !== '' && strcasecmp($value, 'N/A') !== 0 && !in_array($value, $names, true)) {
                $names[] = $value;
            }
        };

        $add($extraModelName);
        $itemId = (int) $itemId;
        if ($itemId <= 0) {
            return $names;
        }

        $vendor = $this->db->table('vendorproducts')
            ->where('id', $itemId)
            ->get()
            ->getRowArray();

        if (!empty($vendor)) {
            foreach (array('modelno', 'model_no', 'model_number', 'modelname', 'model_name', 'modal_name') as $key) {
                if (isset($vendor[$key])) {
                    $add($vendor[$key]);
                }
            }
        }

        $add($this->getModelNameForItem($itemId));

        return $names;
    }

    private function lookupNamedImage($table, $name, array $imageColumns, $userId = null)
    {
        $name = trim((string) $name);
        if ($name === '') {
            return '';
        }

        try {
            $builder = $this->db->table($table)->where('name', $name);
            if ($userId !== null && $userId !== '') {
                $builder->where('user_id', $userId);
            }
            $row = $builder->orderBy('id', 'DESC')->get()->getRowArray();

            if (!$row && $userId !== null && $userId !== '') {
                $row = $this->db->table($table)
                    ->where('name', $name)
                    ->orderBy('id', 'DESC')
                    ->get()
                    ->getRowArray();
            }
        } catch (\Throwable $e) {
            return '';
        }

        if (!$row) {
            return '';
        }

        foreach ($imageColumns as $column) {
            if (!empty($row[$column])) {
                return (string) $row[$column];
            }
        }

        return '';
    }

    private function collectResponseFieldNames($json, array &$names)
    {
        $payload = json_decode((string) $json, true);
        if (!is_array($payload) || empty($payload['responses']) || !is_array($payload['responses'])) {
            return;
        }

        foreach ($payload['responses'] as $frame) {
            if (!is_array($frame) || empty($frame['rows']) || !is_array($frame['rows'])) {
                continue;
            }

            foreach ($frame['rows'] as $row) {
                if (!is_array($row)) {
                    continue;
                }

                $this->addResponseParameterName($names, $row['fieldName'] ?? '');
                $this->addResponseParameterName($names, $row['name'] ?? '');
                $this->addResponseParameterName($names, $row['resp_fieldName'] ?? '');
                $this->addResponseParameterName($names, $row['resp_fieldDescription'] ?? '');
                $this->addResponseParameterName($names, $row['fieldDescription'] ?? '');

                if (empty($row['bitwise']) || !is_array($row['bitwise'])) {
                    continue;
                }

                foreach ($row['bitwise'] as $bitRow) {
                    if (!is_array($bitRow)) {
                        continue;
                    }

                    $this->addResponseParameterName($names, $bitRow['fieldName_1'] ?? '');
                    $this->addResponseParameterName($names, $bitRow['name_1'] ?? '');
                    $this->addResponseParameterName($names, $bitRow['fieldDescription_1'] ?? '');
                }
            }
        }
    }

    private function collectResponseFieldNamesFromDb($configId, array &$names)
    {
        $configId = (int) $configId;
        if ($configId <= 0) {
            return;
        }

        $frames = $this->db->table('api_frames')
            ->select('id')
            ->where('config_id', $configId)
            ->where('frame_group', 'response')
            ->get()
            ->getResultArray();

        $frameIds = array();
        foreach ($frames as $frame) {
            $frameIds[] = (int) $frame['id'];
        }

        if (!$frameIds) {
            return;
        }

        $fields = $this->db->table('api_frame_fields')
            ->select('field_description, raw_row_json')
            ->whereIn('frame_id', $frameIds)
            ->orderBy('row_sequence', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($fields as $field) {
            $decoded = json_decode((string) ($field['raw_row_json'] ?? ''), true);
            if (is_array($decoded)) {
                $this->addResponseParameterName($names, $decoded['fieldName'] ?? '');
                $this->addResponseParameterName($names, $decoded['name'] ?? '');
                $this->addResponseParameterName($names, $decoded['fieldName_1'] ?? '');
                $this->addResponseParameterName($names, $decoded['name_1'] ?? '');
            }

            $this->addResponseParameterName($names, $field['field_description'] ?? '');
        }
    }

    private function addResponseParameterName(array &$names, $value)
    {
        $value = trim((string) $value);
        if ($value === '' || strcasecmp($value, 'N/A') === 0 || strcasecmp($value, 'Command Get Status') === 0) {
            return;
        }

        foreach ($names as $existing) {
            if (strcasecmp($existing, $value) === 0) {
                return;
            }
        }

        $names[] = $value;
    }
}
