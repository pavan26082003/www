<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Dedicated backend for app/Views/items/modal_api.php.
 *
 * Extracted from Items_model.php so the API configuration editor no longer
 * depends on the very large general Items_model class.
 */
class model_apiform extends Model
{
    protected $table = 'api_product_configs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [];

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
            $selectField(array('Batch', 'batch'), 'batch', 'N/A'),
            $selectField(array('brandlogo', 'brand_logo', 'brand_image'), 'brand_image'),
            $selectField(array('product_image', 'Product_image', 'prodimg'), 'product_image')
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

       public function saveApiTreeConfig($mainData, $allData, $user_id = null)
        {
            if (!is_array($mainData)) {
                $mainData = array();
            }
    
            if (!is_array($allData)) {
                throw new \Exception('Invalid API configuration JSON.');
            }

            $this->apiTreePreflightFieldSchedules($allData);
    
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
                'command_frame_count'  => count(isset($allData['commands']) && is_array($allData['commands']) ? $allData['commands'] : array()),
                'response_frame_count' => count(isset($allData['responses']) && is_array($allData['responses']) ? $allData['responses'] : array()),
                'error_frame_count'    => count(isset($allData['errors']) && is_array($allData['errors']) ? $allData['errors'] : array()),
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
    
                    $frameWireHex = $this->apiTreeExtractFrameWireHex($frame);
                    $rows = $this->apiTreeApplyFrameWireHexToRows(
                        is_array($rows) ? $rows : array(),
                        $frameWireHex,
                        $frameGroup
                    );

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
                            $frameGroup,
                            $configId
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
                                    $frameGroup,
                                    $configId
                                );
                            }
                        }
                    }
                }
            }
    
      
        $this->autoSyncDeviceUiSchemaAfterApiChange(
            $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'modelname'))
        );

        return $configId;
        }

        /**
         * After API create/update/delete, refresh the stored device UI schema so
         * api_schema / operate frames pick up new values without a Studio re-save.
         */
        private function autoSyncDeviceUiSchemaAfterApiChange($modelName)
        {
            $modelName = trim((string) $modelName);
            if ($modelName === '') {
                return;
            }

            try {
                if (!class_exists(\App\Models\View_page_model::class, false)) {
                    $candidates = [
                        APPPATH . 'Models/view_page_model.php',
                        APPPATH . 'Models/View_page_model.php',
                    ];
                    foreach ($candidates as $file) {
                        if (is_file($file)) {
                            require_once $file;
                            break;
                        }
                    }
                }
                if (!class_exists(\App\Models\View_page_model::class, false)) {
                    return;
                }
                $pageModel = new \App\Models\View_page_model();
                if (method_exists($pageModel, 'autoPersistDeviceUiSchema')) {
                    $pageModel->autoPersistDeviceUiSchema($modelName, '');
                }
            } catch (\Throwable $exception) {
                log_message(
                    'error',
                    'Auto device-ui schema sync after API change failed for '
                    . $modelName . ': ' . $exception->getMessage()
                );
            }
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

    /**
     * Existing Parameter chooser source.
     *
     * IMPORTANT:
     * Existing Parameter names come ONLY from:
     *   Command Get Status -> Response -> Field Configuration -> Name
     *
     * The fixed protocol wrapper fields are never returned:
     *   DE = Command ID
     *   E7 = Function Code
     *   final 0A = Terminator / CRC
     *
     * This implementation intentionally reads BOTH:
     *   1. raw_payload_json / partitionFieldConfigurations
     *   2. normalized api_frames + api_frame_fields
     *
     * That makes the dropdown work for old records, new records and records
     * created before the normalized field rows were fully populated.
     */
    public function getCommandGetStatusResponseParameterNames($itemId, $userId = null)
    {
        $itemId = (int) $itemId;

        if ($itemId <= 0) {
            return array();
        }

        /*
         * SOURCE OF EXISTING PARAMETERS
         * -----------------------------
         * Every non-protocol Name saved in:
         *
         *   Command Get Status
         *      -> Response frame
         *      -> Field Configuration
         *      -> Name
         *
         * must be returned here, regardless of UI type.
         *
         * Example:
         *   C2 -> speed  (Toggle)
         *   D3 -> fan    (Slider)
         *   CA -> cooker (Text)
         *
         * Result:
         *   speed, fan, cooker
         *
         * Only the fixed Response wrapper bytes are excluded:
         *   byte 0 = DE
         *   byte 1 = E7
         *   final byte = 0A
         *
         * IMPORTANT:
         * Do NOT exclude a payload field merely because an old copied
         * field_description still says Function Code / Terminator / CRC.
         * Position is authoritative for payload rows. This fixes the case
         * where only one of several Response Name values was being returned.
         */

        $normalize = static function ($value) {
            return strtolower(trim(preg_replace('/\s+/', ' ', (string) $value)));
        };

        $compact = static function ($value) {
            return strtolower(preg_replace('/[^a-z0-9]/i', '', (string) $value));
        };

        $cleanHex = static function ($value) {
            return strtoupper(preg_replace('/[^0-9a-f]/i', '', (string) $value));
        };

        $names = array();
        $seen = array();

        $appendName = static function ($value) use (&$names, &$seen, $normalize) {
            $value = trim((string) $value);

            if ($value === '') {
                return;
            }

            $key = $normalize($value);

            if ($key === '' || isset($seen[$key])) {
                return;
            }

            /*
             * Never expose the protocol-owned wrapper labels as Existing
             * Parameters even if a legacy row accidentally stored them in Name.
             */
            $systemToken = strtolower(preg_replace('/[^a-z0-9]/i', '', $value));
            if (in_array(
                $systemToken,
                array(
                    'commandid',
                    'commandidentifier',
                    'functioncode',
                    'terminatorcrc',
                    'terminator',
                    'crc'
                ),
                true
            )) {
                return;
            }

            $seen[$key] = true;
            $names[] = $value;
        };

        /*
         * 1) Find every configuration for this product.
         */
        $configRows = $this->db->table('api_product_configs')
            ->select('id, item_id, parameter_name, raw_payload_json')
            ->where('item_id', $itemId)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        if (empty($configRows)) {
            return array();
        }

        $configIds = array();
        $configById = array();
        $candidateConfigIds = array();

        foreach ($configRows as $configRow) {
            $configId = (int) ($configRow['id'] ?? 0);

            if ($configId <= 0) {
                continue;
            }

            $configIds[] = $configId;
            $configById[$configId] = $configRow;

            $isCommandGetStatus =
                $normalize($configRow['parameter_name'] ?? '') ===
                'command get status';

            $payload = array();

            if (!empty($configRow['raw_payload_json'])) {
                $decoded = json_decode(
                    (string) $configRow['raw_payload_json'],
                    true
                );

                if (
                    json_last_error() === JSON_ERROR_NONE &&
                    is_array($decoded)
                ) {
                    $payload = $decoded;
                }
            }

            /*
             * Product-level parameter name fallback.
             */
            if (!$isCommandGetStatus && !empty($payload)) {
                $product = (
                    isset($payload['product']) &&
                    is_array($payload['product'])
                ) ? $payload['product'] : array();

                foreach (
                    array(
                        'parameter_name',
                        'parameterName',
                        'parametername',
                        'cmd_parametername'
                    )
                    as $key
                ) {
                    if (
                        $normalize($product[$key] ?? '') ===
                        'command get status'
                    ) {
                        $isCommandGetStatus = true;
                        break;
                    }
                }
            }

            /*
             * Fixed Command frame fallback: A1 E7 0A.
             */
            if (!$isCommandGetStatus && !empty($payload)) {
                $commands = array();

                foreach (
                    array('commands', 'commandFrames', 'command_frames')
                    as $commandKey
                ) {
                    if (
                        isset($payload[$commandKey]) &&
                        is_array($payload[$commandKey])
                    ) {
                        $commands = $payload[$commandKey];
                        break;
                    }
                }

                foreach ($commands as $commandFrame) {
                    if (!is_array($commandFrame)) {
                        continue;
                    }

                    $partition = (
                        isset($commandFrame['partition']) &&
                        is_array($commandFrame['partition'])
                    ) ? $commandFrame['partition'] : array();

                    foreach (
                        array(
                            $commandFrame['frameCode'] ?? null,
                            $commandFrame['commandCode'] ?? null,
                            $commandFrame['commandValue'] ?? null,
                            $partition['fullApiValue'] ?? null
                        )
                        as $candidateCode
                    ) {
                        if ($cleanHex($candidateCode) === 'A1E70A') {
                            $isCommandGetStatus = true;
                            break 2;
                        }
                    }
                }
            }

            if ($isCommandGetStatus) {
                $candidateConfigIds[$configId] = true;
            }
        }

        if (empty($configIds)) {
            return array();
        }

        /*
         * 2) Load every Response frame for this product.
         *
         * A frame-level parameter_name can identify Command Get Status even
         * when an older parent api_product_configs row is incomplete.
         */
        $responseFrames = $this->db->table('api_frames')
            ->select(
                'id, config_id, frame_index, parameter_name, ' .
                'frame_size_bytes, raw_meta_json'
            )
            ->whereIn('config_id', $configIds)
            ->where('frame_group', 'response')
            ->orderBy('config_id', 'DESC')
            ->orderBy('frame_index', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $commandGetStatusFrames = array();

        foreach ($responseFrames as $responseFrame) {
            $frameId = (int) ($responseFrame['id'] ?? 0);
            $configId = (int) ($responseFrame['config_id'] ?? 0);

            if ($frameId <= 0 || $configId <= 0) {
                continue;
            }

            $isCommandGetStatus = isset($candidateConfigIds[$configId]);

            if (
                !$isCommandGetStatus &&
                $normalize($responseFrame['parameter_name'] ?? '') ===
                    'command get status'
            ) {
                $isCommandGetStatus = true;
            }

            if (
                !$isCommandGetStatus &&
                !empty($responseFrame['raw_meta_json'])
            ) {
                $meta = json_decode(
                    (string) $responseFrame['raw_meta_json'],
                    true
                );

                if (
                    json_last_error() === JSON_ERROR_NONE &&
                    is_array($meta)
                ) {
                    foreach (
                        array(
                            'parametername',
                            'parameter_name',
                            'parameterName'
                        )
                        as $key
                    ) {
                        if (
                            $normalize($meta[$key] ?? '') ===
                            'command get status'
                        ) {
                            $isCommandGetStatus = true;
                            break;
                        }
                    }
                }
            }

            if ($isCommandGetStatus) {
                $candidateConfigIds[$configId] = true;
                $commandGetStatusFrames[] = $responseFrame;
            }
        }

        /*
         * Legacy fallback:
         * If the parent/frame parameter labels are missing, locate a Response
         * frame for this product that contains the fixed protocol wrapper
         * fields. This fallback is intentionally used only when no identified
         * Command Get Status Response frame was found.
         */
        if (empty($commandGetStatusFrames)) {
            foreach ($responseFrames as $responseFrame) {
                $frameId = (int) ($responseFrame['id'] ?? 0);

                if ($frameId <= 0) {
                    continue;
                }

                $probeRows = $this->db->table('api_frame_fields')
                    ->select(
                        'field_level, row_sequence, name, field_description, ' .
                        'start_byte'
                    )
                    ->where('frame_id', $frameId)
                    ->orderBy('row_sequence', 'ASC')
                    ->get()
                    ->getResultArray();

                $hasByte0 = false;
                $hasByte1 = false;
                $hasProtocolText = false;

                foreach ($probeRows as $probeRow) {
                    if (
                        strtolower(
                            trim(
                                (string) (
                                    $probeRow['field_level'] ?? ''
                                )
                            )
                        ) === 'bitwise'
                    ) {
                        continue;
                    }

                    if (is_numeric($probeRow['start_byte'] ?? null)) {
                        $position = (int) $probeRow['start_byte'];

                        if ($position === 0) {
                            $hasByte0 = true;
                        }

                        if ($position === 1) {
                            $hasByte1 = true;
                        }
                    }

                    foreach (
                        array(
                            $probeRow['name'] ?? '',
                            $probeRow['field_description'] ?? ''
                        )
                        as $probeText
                    ) {
                        $token = $compact($probeText);

                        if (
                            in_array(
                                $token,
                                array(
                                    'commandid',
                                    'commandidentifier',
                                    'functioncode',
                                    'terminatorcrc',
                                    'terminator',
                                    'crc'
                                ),
                                true
                            )
                        ) {
                            $hasProtocolText = true;
                        }
                    }
                }

                if ($hasByte0 && $hasByte1 && $hasProtocolText) {
                    $commandGetStatusFrames[] = $responseFrame;
                    $candidateConfigIds[
                        (int) ($responseFrame['config_id'] ?? 0)
                    ] = true;
                }
            }
        }

        /*
         * 3) Read ALL Name values from each identified Response frame.
         *
         * ui_type is deliberately NOT part of this query/filter.
         * Toggle, Slider, Text, Notification, etc. are treated equally.
         */
        foreach ($commandGetStatusFrames as $responseFrame) {
            $frameId = (int) ($responseFrame['id'] ?? 0);

            if ($frameId <= 0) {
                continue;
            }

            $fields = $this->db->table('api_frame_fields')
                ->select(
                    'id, parent_field_id, field_level, row_sequence, ' .
                    'sub_field_sequence, name, field_description, ' .
                    'start_byte, end_byte, ui_type'
                )
                ->where('frame_id', $frameId)
                ->orderBy('row_sequence', 'ASC')
                ->orderBy('sub_field_sequence', 'ASC')
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();

            if (empty($fields)) {
                continue;
            }

            $parentRows = array();
            $parentById = array();
            $maxStartByte = null;

            foreach ($fields as $field) {
                $fieldId = (int) ($field['id'] ?? 0);
                $level = strtolower(
                    trim((string) ($field['field_level'] ?? ''))
                );

                if ($level === 'bitwise') {
                    continue;
                }

                $parentRows[] = $field;

                if ($fieldId > 0) {
                    $parentById[$fieldId] = $field;
                }

                if (is_numeric($field['start_byte'] ?? null)) {
                    $startByte = (int) $field['start_byte'];

                    if (
                        $maxStartByte === null ||
                        $startByte > $maxStartByte
                    ) {
                        $maxStartByte = $startByte;
                    }
                }
            }

            /*
             * Resolve the final 0A row.
             *
             * Prefer a protocol-labelled row at the highest byte position.
             * Otherwise the highest byte position is the protocol terminator.
             */
            $terminatorFieldId = 0;
            $terminatorStartByte = $maxStartByte;

            foreach ($parentRows as $field) {
                $fieldId = (int) ($field['id'] ?? 0);
                $startByte = is_numeric($field['start_byte'] ?? null)
                    ? (int) $field['start_byte']
                    : null;

                if (
                    $startByte === null ||
                    $maxStartByte === null ||
                    $startByte !== $maxStartByte
                ) {
                    continue;
                }

                $nameToken = $compact($field['name'] ?? '');
                $descriptionToken = $compact(
                    $field['field_description'] ?? ''
                );

                if (
                    in_array(
                        $nameToken,
                        array('terminatorcrc', 'terminator', 'crc'),
                        true
                    ) ||
                    in_array(
                        $descriptionToken,
                        array('terminatorcrc', 'terminator', 'crc'),
                        true
                    )
                ) {
                    $terminatorFieldId = $fieldId;
                    break;
                }
            }

            if ($terminatorFieldId <= 0 && $maxStartByte !== null) {
                foreach ($parentRows as $field) {
                    if (
                        is_numeric($field['start_byte'] ?? null) &&
                        (int) $field['start_byte'] === $maxStartByte
                    ) {
                        $terminatorFieldId =
                            (int) ($field['id'] ?? 0);
                        break;
                    }
                }
            }

            $lockedParentIds = array();

            foreach ($parentRows as $field) {
                $fieldId = (int) ($field['id'] ?? 0);
                $startByte = is_numeric($field['start_byte'] ?? null)
                    ? (int) $field['start_byte']
                    : null;
                $rowSequence = (int) ($field['row_sequence'] ?? 0);

                $locked = false;

                /*
                 * Position is authoritative.
                 */
                if ($startByte !== null) {
                    if ($startByte === 0 || $startByte === 1) {
                        $locked = true;
                    }

                    if (
                        $terminatorStartByte !== null &&
                        $startByte === $terminatorStartByte
                    ) {
                        $locked = true;
                    }
                } else {
                    /*
                     * Very old rows may not have start_byte. In that case only
                     * the first two bytewise rows and the explicitly resolved
                     * terminator row are treated as protocol wrapper rows.
                     */
                    if ($rowSequence === 1 || $rowSequence === 2) {
                        $locked = true;
                    }

                    if (
                        $terminatorFieldId > 0 &&
                        $fieldId === $terminatorFieldId
                    ) {
                        $locked = true;
                    }
                }

                if ($locked && $fieldId > 0) {
                    $lockedParentIds[$fieldId] = true;
                }
            }

            foreach ($fields as $field) {
                $fieldId = (int) ($field['id'] ?? 0);
                $parentId = (int) ($field['parent_field_id'] ?? 0);
                $level = strtolower(
                    trim((string) ($field['field_level'] ?? ''))
                );

                if ($level === 'bitwise') {
                    /*
                     * A bitwise Name is valid when its parent payload byte/
                     * partition is not one of DE / E7 / final 0A.
                     */
                    if (
                        $parentId > 0 &&
                        isset($lockedParentIds[$parentId])
                    ) {
                        continue;
                    }

                    $appendName($field['name'] ?? '');
                    continue;
                }

                if (
                    $fieldId > 0 &&
                    isset($lockedParentIds[$fieldId])
                ) {
                    continue;
                }

                /*
                 * ALL unlocked bytewise names are valid Existing Parameters.
                 * ui_type is intentionally ignored.
                 */
                $appendName($field['name'] ?? '');
            }
        }

        /*
         * 4) Raw JSON fallback.
         *
         * This covers configurations created before api_frame_fields was fully
         * populated. Again, position - not UI type - controls wrapper exclusion.
         */
        foreach ($configRows as $configRow) {
            $configId = (int) ($configRow['id'] ?? 0);

            if (
                !isset($candidateConfigIds[$configId]) ||
                empty($configRow['raw_payload_json'])
            ) {
                continue;
            }

            $payload = json_decode(
                (string) $configRow['raw_payload_json'],
                true
            );

            if (
                json_last_error() !== JSON_ERROR_NONE ||
                !is_array($payload)
            ) {
                continue;
            }

            $responses = array();

            foreach (
                array('responses', 'responseFrames', 'response_frames')
                as $key
            ) {
                if (
                    isset($payload[$key]) &&
                    is_array($payload[$key])
                ) {
                    $responses = $payload[$key];
                    break;
                }
            }

            foreach ($responses as $responseFrame) {
                if (!is_array($responseFrame)) {
                    continue;
                }

                $partition = (
                    isset($responseFrame['partition']) &&
                    is_array($responseFrame['partition'])
                ) ? $responseFrame['partition'] : array();

                $configs = (
                    isset($partition['partitionFieldConfigurations']) &&
                    is_array($partition['partitionFieldConfigurations'])
                ) ? $partition['partitionFieldConfigurations'] : array();

                if (empty($configs)) {
                    continue;
                }

                $maxEnd = 0;

                foreach (array_keys($configs) as $rangeKey) {
                    if (
                        preg_match(
                            '/^\s*(\d+)\s*-\s*(\d+)\s*$/',
                            (string) $rangeKey,
                            $matches
                        )
                    ) {
                        $maxEnd = max($maxEnd, (int) $matches[2]);
                    }
                }

                foreach ($configs as $rangeKey => $configuration) {
                    if (!is_array($configuration)) {
                        continue;
                    }

                    $start = null;

                    if (
                        preg_match(
                            '/^\s*(\d+)\s*-\s*(\d+)\s*$/',
                            (string) $rangeKey,
                            $matches
                        )
                    ) {
                        $start = (int) $matches[1];
                    }

                    /*
                     * DE = 0, E7 = 1, final 0A = maxEnd - 1.
                     */
                    if (
                        $start !== null &&
                        (
                            $start === 0 ||
                            $start === 1 ||
                            ($maxEnd > 0 && $start === ($maxEnd - 1))
                        )
                    ) {
                        continue;
                    }

                    $byteConfig = (
                        isset($configuration['byte']) &&
                        is_array($configuration['byte'])
                    ) ? $configuration['byte'] : array();

                    $appendName(
                        $byteConfig['name'] ??
                        $byteConfig['fieldName'] ??
                        $byteConfig['field_name'] ??
                        ''
                    );

                    foreach (
                        $configuration as $configurationKey => $bitConfig
                    ) {
                        if (
                            $configurationKey === 'byte' ||
                            strpos((string) $configurationKey, '_') === 0 ||
                            !is_array($bitConfig)
                        ) {
                            continue;
                        }

                        $appendName(
                            $bitConfig['name'] ??
                            $bitConfig['fieldName'] ??
                            $bitConfig['field_name'] ??
                            $bitConfig['name_1'] ??
                            $bitConfig['fieldName_1'] ??
                            $bitConfig['field_name_1'] ??
                            ''
                        );
                    }
                }
            }
        }

        return array_values($names);
    }

    /**
     * Return Existing Parameter names that are already assigned to another API
     * for this product. Only names that still exist in Command Get Status ->
     * Response -> Field Configuration are returned.
     *
     * $excludeConfigId keeps the current parameter available while editing its
     * own API, but prevents every other API from reusing it.
     */
    public function getUsedCommandGetStatusResponseParameterNames(
        $itemId,
        $userId = null,
        $excludeConfigId = 0
    ) {
        $itemId = (int) $itemId;
        $excludeConfigId = (int) $excludeConfigId;

        if ($itemId <= 0) {
            return array();
        }

        $sourceNames = $this->getCommandGetStatusResponseParameterNames(
            $itemId,
            $userId
        );

        if (empty($sourceNames)) {
            return array();
        }

        $sourceMap = array();

        foreach ($sourceNames as $sourceName) {
            $sourceName = trim((string) $sourceName);

            if ($sourceName === '') {
                continue;
            }

            $key = strtolower(
                trim(
                    preg_replace(
                        '/\s+/',
                        ' ',
                        $sourceName
                    )
                )
            );

            $sourceMap[$key] = $sourceName;
        }

        $builder = $this->db->table('api_product_configs')
            ->select('id, parameter_name, raw_payload_json')
            ->where('item_id', $itemId);

        if ($excludeConfigId > 0) {
            $builder->where('id !=', $excludeConfigId);
        }

        $rows = $builder
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        $used = array();
        $seen = array();

        foreach ($rows as $row) {
            $parameterName = trim(
                (string) ($row['parameter_name'] ?? '')
            );

            if ($parameterName === '') {
                continue;
            }

            $key = strtolower(
                trim(
                    preg_replace(
                        '/\s+/',
                        ' ',
                        $parameterName
                    )
                )
            );

            /*
             * Command Get Status is the source definition, not a consumer.
             */
            if (
                $key === 'command get status' ||
                !isset($sourceMap[$key])
            ) {
                continue;
            }

            /*
             * IMPORTANT:
             * A Command Get Status Response name is consumed ONLY when the
             * saved API explicitly says that it was created from the
             * "Existing Parameter" source.
             *
             * Matching parameter_name text by itself is NOT enough. A Custom
             * Parameter is allowed to have the same text without consuming
             * the Response field name. Legacy rows that do not contain the
             * source marker are therefore NOT treated as used.
             */
            $parameterSource = '';

            if (!empty($row['raw_payload_json'])) {
                $payload = json_decode(
                    (string) $row['raw_payload_json'],
                    true
                );

                if (
                    json_last_error() === JSON_ERROR_NONE &&
                    is_array($payload)
                ) {
                    $product = (
                        isset($payload['product']) &&
                        is_array($payload['product'])
                    ) ? $payload['product'] : array();

                    foreach (
                        array(
                            'parameter_name_source',
                            'parameterNameSource',
                            'parameter_source',
                            'parameterSource'
                        )
                        as $sourceKey
                    ) {
                        if (
                            isset($product[$sourceKey]) &&
                            trim((string) $product[$sourceKey]) !== ''
                        ) {
                            $parameterSource = strtolower(
                                trim(
                                    (string) $product[$sourceKey]
                                )
                            );
                            break;
                        }
                    }
                }
            }

            // Only an explicit Existing Parameter selection consumes the name.
            if ($parameterSource !== 'existing') {
                continue;
            }

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $used[] = $sourceMap[$key];
        }

        return $used;
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
            count($payload['commands'] ?? array())
        );

        $responseFrameCount = max(
            (int) ($tree['config']['response_frame_count'] ?? 0),
            count($payload['responses'] ?? array())
        );

        $errorFrameCount = max(
            (int) ($tree['config']['error_frame_count'] ?? 0),
            count($payload['errors'] ?? array())
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
            'name' => $field['name'] ?? '',
            'fieldName' => $field['name'] ?? '',
            'field_name' => $field['name'] ?? '',
            'aliasName' => $field['alias_name'] ?? '',
            'scheduleEnabled' => $field['schedule_enabled'] ?? 'No',
            'scheduleDay' => $field['schedule_day'] ?? '',
            'scheduleTime' => substr((string) ($field['schedule_time'] ?? ''), 0, 5),
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
            'name_1' => $field['name'] ?? '',
            'fieldName_1' => $field['name'] ?? '',
            'field_name_1' => $field['name'] ?? '',
            'aliasName_1' => $field['alias_name'] ?? '',
            'scheduleEnabled_1' => $field['schedule_enabled'] ?? 'No',
            'scheduleDay_1' => $field['schedule_day'] ?? '',
            'scheduleTime_1' => substr((string) ($field['schedule_time'] ?? ''), 0, 5),
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

            $this->apiTreePreflightFieldSchedules($allData);
    
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
                'command_frame_count'  => count(isset($allData['commands']) && is_array($allData['commands']) ? $allData['commands'] : array()),
                'response_frame_count' => count(isset($allData['responses']) && is_array($allData['responses']) ? $allData['responses'] : array()),
                'error_frame_count'    => count(isset($allData['errors']) && is_array($allData['errors']) ? $allData['errors'] : array()),
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
    
                    $frameWireHex = $this->apiTreeExtractFrameWireHex($frame);
                    $rows = $this->apiTreeApplyFrameWireHexToRows(
                        is_array($rows) ? $rows : array(),
                        $frameWireHex,
                        $frameGroup
                    );

                    foreach ($rows as $rowIndex => $row) {
                        if (!is_array($row)) {
                            continue;
                        }
    
                        $parentFieldId = $this->insertApiTreeField($frameId, null, 'bytewise', $row, $rowIndex + 1, null, $frameGroup, $configId);
    
                        if (!empty($row['bitwise']) && is_array($row['bitwise'])) {
                            foreach ($row['bitwise'] as $bitIndex => $bitRow) {
                                if (is_array($bitRow)) {
                                    $this->insertApiTreeField($frameId, $parentFieldId, 'bitwise', $bitRow, $rowIndex + 1, $bitIndex + 1, $frameGroup, $configId);
                                }
                            }
                        }
                    }
                }
            }

            $syncModel = $this->apiTreeCleanValue($this->apiTreeArrayValue($mainData, 'modelname'));
            if ($syncModel === '') {
                $syncModel = (string) ($existing['model_name'] ?? '');
            }
            $this->autoSyncDeviceUiSchemaAfterApiChange($syncModel);
    
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

            $ok = $this->db->transStatus();
            if ($ok) {
                $this->autoSyncDeviceUiSchemaAfterApiChange((string) ($config['model_name'] ?? ''));
            }
    
            return $ok;
        }

        /**
         * Resolve config_id from api_frames when callers did not pass it.
         */
        private function resolveApiFrameConfigId($frameId)
        {
            $frameId = (int) $frameId;
            if ($frameId <= 0 || !$this->db->tableExists('api_frames')) {
                return null;
            }

            $row = $this->db->table('api_frames')
                ->select('config_id')
                ->where('id', $frameId)
                ->get()
                ->getRowArray();

            if (empty($row) || !isset($row['config_id'])) {
                return null;
            }

            $configId = (int) $row['config_id'];
            return $configId > 0 ? $configId : null;
        }

        /**
         * Read per-field display alias and weekly time schedule from frontend
         * rows (both bytewise and bitwise). Do not modify the protocol Name.
         */
        private function apiTreeFieldScheduleValues($row, $frameGroup)
        {
            $prefixes = $this->apiTreePrefixes($frameGroup);
            $alias = $this->apiTreeCleanValue(
                $this->apiTreePrefixedValue($row, $prefixes, 'aliasName')
                ?? $this->apiTreePrefixedValue($row, $prefixes, 'alias_name')
            );
            // Legacy nonempty display aliases are treated as enabled on resave.
            $alias = ($alias !== null && trim($alias) !== '' && strtolower(trim($alias)) !== 'no') ? 'Yes' : 'No';

            $enabled = $this->apiTreeCleanValue(
                $this->apiTreePrefixedValue($row, $prefixes, 'scheduleEnabled')
                ?? $this->apiTreePrefixedValue($row, $prefixes, 'schedule_enabled')
            );
            $enabled = $enabled === null ? 'No' : ucfirst(strtolower($enabled));
            if (!in_array($enabled, array('Yes', 'No'), true)) {
                throw new \InvalidArgumentException('Time Scheduling must be Yes or No.');
            }

            $day = null;
            $time = null;
            if ($enabled === 'Yes') {
                $day = $this->apiTreeCleanValue(
                    $this->apiTreePrefixedValue($row, $prefixes, 'scheduleDay')
                    ?? $this->apiTreePrefixedValue($row, $prefixes, 'schedule_day')
                );
                $time = $this->apiTreeCleanValue(
                    $this->apiTreePrefixedValue($row, $prefixes, 'scheduleTime')
                    ?? $this->apiTreePrefixedValue($row, $prefixes, 'schedule_time')
                );
                if (!in_array($day, array(
                    'Every day', 'Monday', 'Tuesday', 'Wednesday', 'Thursday',
                    'Friday', 'Saturday', 'Sunday'
                ), true)) {
                    throw new \InvalidArgumentException('Choose a valid scheduling day.');
                }
                if ($time === null || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $time)) {
                    throw new \InvalidArgumentException('Scheduling time must use HH:MM (24-hour format).');
                }
                // Accept older saved seconds, but persist minute precision only.
                $time = substr($time, 0, 5) . ':00';
            }
            return array(
                'alias_name' => $alias,
                'schedule_enabled' => $enabled,
                'schedule_day' => $day,
                'schedule_time' => $time
            );
        }

        /**
         * Fail safely before editing existing rows if the SQL migration is
         * missing, or the submitted JSON contains an invalid schedule.
         * This deliberately runs before replaceApiTreeConfig deletes frames.
         */
        private function apiTreePreflightFieldSchedules($allData)
        {
            $columns = array_flip($this->db->getFieldNames('api_frame_fields'));
            foreach (array('alias_name', 'schedule_enabled', 'schedule_day', 'schedule_time') as $column) {
                if (!isset($columns[$column])) {
                    throw new \RuntimeException(
                        'Missing api_frame_fields.' . $column . '. Apply the EFACTOR Field Scheduling SQL migration before saving.'
                    );
                }
            }

            foreach (array('commands' => 'command', 'responses' => 'response', 'errors' => 'error') as $key => $group) {
                $frames = isset($allData[$key]) && is_array($allData[$key]) ? $allData[$key] : array();
                foreach ($frames as $frame) {
                    if (!is_array($frame)) continue;
                    $rows = isset($frame['rows']) && is_array($frame['rows']) ? $frame['rows'] : array();
                    foreach ($rows as $row) {
                        if (!is_array($row)) continue;
                        $this->apiTreeFieldScheduleValues($row, $group);
                        $bits = isset($row['bitwise']) && is_array($row['bitwise']) ? $row['bitwise'] : array();
                        foreach ($bits as $bitRow) {
                            if (is_array($bitRow)) $this->apiTreeFieldScheduleValues($bitRow, $group);
                        }
                    }
                }
            }
        }

        // Persist one-based dropdown option keys, accepting old numeric counts.
        private function apiTreeDropdownAsPerUi($row, $prefixes)
        {
            $value = $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'asperui'));
            $type = strtolower(trim((string) $this->apiTreePrefixedValue($row, $prefixes, 'uiType')));
            if ($type !== 'dropdown') return $value;
            $serials = $this->apiTreeGenerateUiSerialNo($row, $prefixes);
            if ($serials === null) return null;
            return implode(',', range(1, count(explode(',', $serials))));
        }

        // Generate zero-based serials from the configured UI options, not the legacy default.
        private function apiTreeGenerateUiSerialNo($row, $prefixes)
        {
            $type = strtolower(trim((string) $this->apiTreePrefixedValue($row, $prefixes, 'uiType')));
            $asPerUi = trim((string) $this->apiTreePrefixedValue($row, $prefixes, 'asperui'));
            if (in_array($type, array('text', 'dropdown'), true)) {
                $count = ctype_digit($asPerUi) ? (int) $asPerUi : count(array_filter(array_map('trim', explode(',', $asPerUi)), 'strlen'));
            } elseif (in_array($type, array('toggle switch', 'button', 'push button'), true)) {
                $count = count(array_filter(array_map('trim', explode(',', $asPerUi)), 'strlen'));
            } else {
                return null;
            }
            return $count > 0 && $count <= 1000 ? implode(',', range(0, $count - 1)) : null;
        }

        private function insertApiTreeField($frameId, $parentFieldId, $fieldLevel, $row, $rowSequence, $subFieldSequence, $frameGroup, $configId = null)
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
    
            $scheduleData = $this->apiTreeFieldScheduleValues($row, $frameGroup);

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
    
                // Field Configuration Name (supports current UI aliases and bitwise _1 aliases)
                'name'                  => $this->apiTreeCleanValue($this->apiTreeFirst($row, array(
                    'name',
                    'fieldName',
                    'field_name',
                    'name_1',
                    'fieldName_1',
                    'field_name_1',
                    'cmd_name',
                    'cmd_name_1',
                    'resp_name',
                    'resp_name_1',
                    'res_name',
                    'res_name_1',
                    'err_name',
                    'err_name_1',
                    'error_name',
                    'error_name_1'
                ))),

                'alias_name'            => $scheduleData['alias_name'],
                'schedule_enabled'      => $scheduleData['schedule_enabled'],
                'schedule_day'          => $scheduleData['schedule_day'],
                'schedule_time'         => $scheduleData['schedule_time'],
                'field_description'     => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'fieldDescription')),
                'data_type'             => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'dataType')),
                'value_definition'      => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'valueDefinition')),
    
                'display_value_min'     => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'displayValueMin')),
                'display_value_max'     => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'displayValueMax')),
                'value_in_hex_min'      => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'valueInHexMin')),
                'value_in_hex_max'      => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'valueInHexMax')),
    
                'ui_type'               => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'uiType')),
                'ui_id'                 => $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'uiId')),
                'as_per_ui'             => $this->apiTreeDropdownAsPerUi($row, $prefixes),
                'ui_serial_no'          => $this->apiTreeGenerateUiSerialNo($row, $prefixes),
                'ui_values'             => strtolower(trim((string) $this->apiTreePrefixedValue($row, $prefixes, 'uiType'))) === 'push button'
                    ? '00,01'
                    : $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'asperuivalue')),
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

            // Denormalized join key (safe: only when column exists).
            if ($this->db->fieldExists('config_id', 'api_frame_fields')) {
                $resolvedConfigId = (int) $configId;
                if ($resolvedConfigId <= 0) {
                    $resolvedConfigId = (int) $this->resolveApiFrameConfigId($frameId);
                }
                if ($resolvedConfigId > 0) {
                    $data['config_id'] = $resolvedConfigId;
                }
            }
    
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

        /**
         * Pull the authoritative frame wire hex from the modal payload.
         * Prefer frameCode / commandCode / valueGroups over per-row valueInHexMin
         * (UI often sends 0x00..0xFF as a range, not the live byte).
         */
        private function apiTreeExtractFrameWireHex($frame)
        {
            if (!is_array($frame)) {
                return '';
            }

            $partition = (isset($frame['partition']) && is_array($frame['partition']))
                ? $frame['partition']
                : array();

            $candidates = array(
                $frame['frameCode'] ?? null,
                $frame['commandCode'] ?? null,
                $frame['responseCode'] ?? null,
                $frame['commandValue'] ?? null,
                $frame['fullApiValue'] ?? null,
                $partition['fullApiValue'] ?? null,
            );

            if (!empty($frame['valueGroups']) && is_array($frame['valueGroups'])) {
                $joined = '';
                foreach ($frame['valueGroups'] as $group) {
                    $joined .= preg_replace('/[^0-9A-Fa-f]/', '', (string) $group);
                }
                if ($joined !== '') {
                    $candidates[] = $joined;
                }
            }

            if (!empty($frame['editor_bytes']) && is_array($frame['editor_bytes'])) {
                $joined = '';
                foreach ($frame['editor_bytes'] as $byte) {
                    if (is_array($byte)) {
                        $joined .= preg_replace('/[^0-9A-Fa-f]/', '', (string) ($byte['value'] ?? ''));
                    } else {
                        $joined .= preg_replace('/[^0-9A-Fa-f]/', '', (string) $byte);
                    }
                }
                if ($joined !== '') {
                    $candidates[] = $joined;
                }
            }

            // product.editor_bytes is sometimes nested on the frame payload root via save JSON.
            if (!empty($frame['product']['editor_bytes']) && is_array($frame['product']['editor_bytes'])) {
                $joined = '';
                foreach ($frame['product']['editor_bytes'] as $byte) {
                    if (is_array($byte)) {
                        $joined .= preg_replace('/[^0-9A-Fa-f]/', '', (string) ($byte['value'] ?? ''));
                    }
                }
                if ($joined !== '') {
                    $candidates[] = $joined;
                }
            }

            foreach ($candidates as $candidate) {
                $hex = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', (string) $candidate));
                if ($hex !== '' && (strlen($hex) % 2) === 0) {
                    return $hex;
                }
            }

            return '';
        }

        private function apiTreeCanonicalProtocolSlot($raw)
        {
            $name = preg_replace('/[^a-z0-9]+/', '', strtolower((string) $raw));
            if ($name === '' ) {
                return '';
            }
            if ($name === 'terminator' || $name === 'terminatorcrc' || $name === 'crc'
                || strpos($name, 'terminator') === 0
            ) {
                return 'terminator';
            }
            if (in_array($name, array('commandid', 'command', 'cmdid', 'cmd'), true)) {
                return 'commandid';
            }
            if (in_array($name, array('functioncode', 'function', 'funcode', 'funccode'), true)) {
                return 'functioncode';
            }
            if (in_array($name, array('actionid', 'action'), true)) {
                return 'actionid';
            }

            return $name;
        }

        /**
         * Overwrite row valueInHexMin (and protocol defaultvalue) from frame wire bytes.
         */
        /**
         * Apply frameCode slices to every row with sibling occupancy awareness so a
         * Data row cannot persist the entire framed command while wrappers exist.
         */
        private function apiTreeApplyFrameWireHexToRows(array $rows, $frameWireHex, $frameGroup = 'command')
        {
            $frameWireHex = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', (string) $frameWireHex));
            if ($frameWireHex === '' || empty($rows)) {
                return $rows;
            }

            $prefixes = $this->apiTreePrefixes($frameGroup);
            $occupied = array();
            $meta = array();
            $rawStarts = array();

            foreach ($rows as $index => $row) {
                if (!is_array($row)) {
                    continue;
                }
                $startRaw = $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'startBytePosition'));
                $bytesRaw = $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'numberOfBytes'));
                $start = is_numeric($startRaw) ? max(0, (int) $startRaw) : 0;
                $declaredBytes = is_numeric($bytesRaw) ? max(0, (int) $bytesRaw) : 1;
                $numberOfBytes = $declaredBytes;
                if ($numberOfBytes < 1) {
                    $numberOfBytes = 1;
                }
                $fieldDescription = (string) $this->apiTreePrefixedValue($row, $prefixes, 'fieldDescription');
                $slot = $this->apiTreeCanonicalProtocolSlot($fieldDescription);
                if ($slot === '') {
                    $slot = $this->apiTreeCanonicalProtocolSlot(
                        (string) $this->apiTreeFirst($row, array('name', 'fieldName', 'field_name', 'byteDescriptions'))
                    );
                }
                $meta[$index] = array(
                    'start' => $start,
                    'bytes' => $numberOfBytes,
                    'slot' => $slot,
                );
                // Position 0 on a zero-width trigger is not a wire byte.
                $uiOnly = ($start === 0 && ($declaredBytes < 1 || $slot === 'trigger'));
                if (!$uiOnly) {
                    $rawStarts[] = $start;
                }
            }

            // Studio positions are often 1-based; wire slices are 0-based.
            $oneBased = !empty($rawStarts) && min($rawStarts) >= 1;
            foreach ($meta as $index => $info) {
                $start = $oneBased ? max(0, (int) $info['start'] - 1) : (int) $info['start'];
                $meta[$index]['start'] = $start;
                $slot = $info['slot'];
                $numberOfBytes = (int) $info['bytes'];
                if (in_array($slot, array('commandid', 'functioncode', 'actionid', 'terminator'), true)) {
                    for ($b = $start; $b < ($start + $numberOfBytes); $b++) {
                        $occupied[$b] = $slot;
                    }
                }
            }

            foreach ($rows as $index => $row) {
                if (!is_array($row) || !isset($meta[$index])) {
                    continue;
                }
                $rows[$index] = $this->apiTreeApplyFrameWireHexToRow(
                    $row,
                    $frameWireHex,
                    $frameGroup,
                    $meta[$index]['slot'],
                    $meta[$index]['start'],
                    $meta[$index]['bytes'],
                    $occupied
                );
            }

            return $rows;
        }

        private function apiTreeApplyFrameWireHexToRow(
            array $row,
            $frameWireHex,
            $frameGroup = 'command',
            $slot = null,
            $start = null,
            $numberOfBytes = null,
            array $occupied = array()
        ) {
            $frameWireHex = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', (string) $frameWireHex));
            if ($frameWireHex === '') {
                return $row;
            }

            $prefixes = $this->apiTreePrefixes($frameGroup);
            if ($start === null || $numberOfBytes === null || $slot === null) {
                $startRaw = $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'startBytePosition'));
                $bytesRaw = $this->apiTreeCleanValue($this->apiTreePrefixedValue($row, $prefixes, 'numberOfBytes'));
                $start = is_numeric($startRaw) ? max(0, (int) $startRaw) : 0;
                $numberOfBytes = is_numeric($bytesRaw) ? max(1, (int) $bytesRaw) : 1;
                $fieldDescription = (string) $this->apiTreePrefixedValue($row, $prefixes, 'fieldDescription');
                $slot = $this->apiTreeCanonicalProtocolSlot($fieldDescription);
                if ($slot === '') {
                    $slot = $this->apiTreeCanonicalProtocolSlot(
                        (string) $this->apiTreeFirst($row, array('name', 'fieldName', 'field_name', 'byteDescriptions'))
                    );
                }
            }

            $isProtocol = in_array($slot, array('commandid', 'functioncode', 'actionid', 'terminator'), true);

            // Non-protocol rows only take bytes not already owned by protocol wrappers.
            if (!$isProtocol && !empty($occupied)) {
                $free = '';
                $freeCount = 0;
                for ($b = 0; $b < (int) (strlen($frameWireHex) / 2); $b++) {
                    if (isset($occupied[$b])) {
                        continue;
                    }
                    $free .= substr($frameWireHex, $b * 2, 2);
                    $freeCount++;
                }
                if ($free === '') {
                    // No exclusive payload bytes — do not invent multi-byte 00 padding
                    // (that inflates commands like A1E70000000A).
                    $wireHex = '';
                    $numberOfBytes = 0;
                } else {
                    // Prefer the free slice that intersects this row's declared window.
                    $window = '';
                    for ($b = $start; $b < ($start + $numberOfBytes); $b++) {
                        if (isset($occupied[$b])) {
                            continue;
                        }
                        $part = substr($frameWireHex, $b * 2, 2);
                        if ($part !== '') {
                            $window .= $part;
                        }
                    }
                    $wireHex = $window !== '' ? strtoupper($window) : strtoupper($free);
                    // Whole-frame Data row (start 0 spanning all bytes): keep only free payload.
                    if ($start === 0 && ($numberOfBytes * 2) >= strlen($frameWireHex)) {
                        $wireHex = strtoupper($free);
                    }
                    $numberOfBytes = max(1, (int) (strlen($wireHex) / 2));
                }
                $row['numberOfBytes'] = (string) $numberOfBytes;
                foreach ($prefixes as $prefix) {
                    $row[$prefix . '_numberOfBytes'] = (string) $numberOfBytes;
                }
            } else {
                $slice = substr($frameWireHex, $start * 2, $numberOfBytes * 2);
                if ($slice === '' || (strlen($slice) % 2) !== 0) {
                    return $row;
                }
                $wireHex = strtoupper($slice);
            }

            if ($slot === 'terminator') {
                $isZero = (bool) preg_match('/^0+$/', $wireHex);
                $wireHex = (!$isZero && $wireHex !== '') ? $wireHex : '0A';
                $row['fieldDescription'] = 'Terminator';
                $row['name'] = 'Terminator';
                $row['fieldName'] = 'Terminator';
                foreach ($prefixes as $prefix) {
                    $row[$prefix . '_fieldDescription'] = 'Terminator';
                    $row[$prefix . '_name'] = 'Terminator';
                }
            }

            $row['valueInHexMin'] = $wireHex;
            foreach ($prefixes as $prefix) {
                $row[$prefix . '_valueInHexMin'] = $wireHex;
            }

            // Protocol ids must store the real wire byte, not the UI label ("Child Lock").
            if ($isProtocol) {
                $row['defaultvalue'] = $wireHex;
                foreach ($prefixes as $prefix) {
                    $row[$prefix . '_defaultvalue'] = $wireHex;
                }
            }

            return $row;
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

    /**
     * Return the one vendor/product row used by modal_api.php.
     */


    /*niree*/


    public function getProductMetaRow($itemId, $userId = null)
{
    $query = $this->default_form_values($itemId, $userId);
    $row = $query ? $query->getRow() : null;

    if (!$row) {
        return null;
    }

    $brandName = trim((string) ($row->brand ?? ''));
    $productName = trim((string) ($row->category ?? ''));

    $brandFallback = trim((string) ($row->brand_image ?? ''));
    $productFallback = trim((string) ($row->product_image ?? ''));

    $brandLogo = $this->findNamedImage(
        'brands',
        $brandName,
        $userId,
        array('name', 'brand_name', 'brand'),
        array(
            'img_path',
            'brandlogo',
            'brand_logo',
            'brand_image',
            'logo'
        )
    );

    $productImage = $this->findNamedImage(
        'productcats',
        $productName,
        $userId,
        array(
            'name',
            'product_name',
            'category'
        ),
        array(
            'img_path',
            'Product_image',
            'product_image',
            'prodimg',
            'image'
        )
    );

    $row->brand_image = $brandLogo !== ''
        ? $brandLogo
        : $brandFallback;

    $row->product_image = $productImage !== ''
        ? $productImage
        : $productFallback;

    // Compatibility aliases used by modal_api.php
    // and older Vendor/Product UI code.
    $row->brand_logo = $row->brand_image;
    $row->brandlogo = $row->brand_image;

    $row->Product_image = $row->product_image;
    $row->prodimg = $row->product_image;

    return $row;
}










private function findNamedImage(
    $tableName,
    $selectedName,
    $userId,
    array $nameCandidates,
    array $imageCandidates
) {
    $selectedName = trim((string) $selectedName);

    if (
        $selectedName === '' ||
        !$this->db->tableExists($tableName)
    ) {
        return '';
    }

    $fields = $this->db->getFieldNames($tableName);

    $fieldMap = array();

    foreach ($fields as $field) {
        $fieldMap[strtolower($field)] = $field;
    }

    $nameField = $this->pickExistingMetaColumn(
        $fieldMap,
        $nameCandidates
    );

    $userField = $this->pickExistingMetaColumn(
        $fieldMap,
        array(
            'user_id',
            'userid',
            'created_by'
        )
    );

    if (!$nameField) {
        return '';
    }

    /*
     * Search the returned rows for the exact selected
     * Brand/Product name and return the first non-empty image.
     */
    $findInRows = function (array $rows) use (
        $selectedName,
        $nameField,
        $fieldMap,
        $imageCandidates
    ) {
        $normalizedSelected = strtolower(
            trim($selectedName)
        );

        foreach ($rows as $row) {

            $candidateName = strtolower(
                trim(
                    (string) (
                        $row[$nameField] ?? ''
                    )
                )
            );

            if ($candidateName !== $normalizedSelected) {
                continue;
            }

            foreach ($imageCandidates as $candidate) {

                $columnKey = strtolower(
                    (string) $candidate
                );

                if (!isset($fieldMap[$columnKey])) {
                    continue;
                }

                $realColumn = $fieldMap[$columnKey];

                $value = trim(
                    (string) (
                        $row[$realColumn] ?? ''
                    )
                );

                if ($value !== '') {
                    return $value;
                }
            }
        }

        return '';
    };

    /*
     * Prefer Brand/Product belonging to
     * the currently logged-in user.
     */
    if (
        $userId !== null &&
        $userId !== '' &&
        $userField
    ) {
        $userRows = $this->db
            ->table($tableName)
            ->where(
                $userField,
                (int) $userId
            )
            ->get()
            ->getResultArray();

        $image = $findInRows($userRows);

        if ($image !== '') {
            return $image;
        }
    }

    /*
     * Legacy/global fallback.
     */
    $rows = $this->db
        ->table($tableName)
        ->get()
        ->getResultArray();

    return $findInRows($rows);
}









private function pickExistingMetaColumn(
    array $fieldMap,
    array $candidates
) {
    foreach ($candidates as $candidate) {

        $key = strtolower(
            (string) $candidate
        );

        if (isset($fieldMap[$key])) {
            return $fieldMap[$key];
        }
    }

    return null;
}








    // public function getProductMetaRow($itemId, $userId = null)
    // {
    //     $query = $this->default_form_values($itemId, $userId);
    //     return $query ? $query->getRow() : null;
    // }

    /**
     * Small JSON-safe database identity helper used by the save response.
     */
    public function getConnectedDatabaseName(): string
    {
        $row = $this->db->query('SELECT DATABASE() AS db_name')->getRowArray();
        return (string) ($row['db_name'] ?? 'UNKNOWN');
    }
}
