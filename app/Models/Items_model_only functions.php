
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

    public function getApiTreeConfigList($itemId = 0, $userId = null)
    {
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
    
        if ((int) $itemId > 0) {
            $builder->where('p.item_id', (int) $itemId);
        }
    
        if ($userId !== null && $userId !== '') {
            $builder->where('p.user_id', (int) $userId);
        }
    
        $builder->groupBy('p.id');
        $builder->orderBy('p.id', 'DESC');
    
        return $builder->get();
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
    
        if ($userId !== null && $userId !== '' && (int) $tree['config']['user_id'] !== (int) $userId) {
            return null;
        }
    
        $payload = array('commands' => array(), 'responses' => array(), 'errors' => array());
        if (!empty($tree['config']['raw_payload_json'])) {
            $decoded = json_decode($tree['config']['raw_payload_json'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $payload = array_merge($payload, $decoded);
            }
        }
    
        $payloadProduct = isset($payload['product']) && is_array($payload['product']) ? $payload['product'] : array();
        $dbPayload = $this->buildApiTreeEditPayloadFromDb($tree);
        $payload = array_merge($payload, $dbPayload);
        $pageNames = '';
        foreach (array('page_names', 'pageNames', 'pagenames') as $pageNameKey) {
            if (isset($payloadProduct[$pageNameKey]) && $payloadProduct[$pageNameKey] !== '') {
                $pageNames = $payloadProduct[$pageNameKey];
                break;
            }
        }
        $commandFrameCount = max((int) ($tree['config']['command_frame_count'] ?? 0), count($payload['commands'] ?? array()), 1);
        $responseFrameCount = max((int) ($tree['config']['response_frame_count'] ?? 0), count($payload['responses'] ?? array()), 1);
        $errorFrameCount = max((int) ($tree['config']['error_frame_count'] ?? 0), count($payload['errors'] ?? array()), 1);
    
        return array(
            'config_id' => (int) $tree['config']['id'],
            'product' => array(
                'itemid' => $tree['config']['item_id'],
                'brandname' => $tree['config']['brand_name'],
                'productname' => $tree['config']['product_name'],
                'modelname' => $tree['config']['model_name'],
                'date' => $tree['config']['config_date'],
                'parameter_name' => $tree['config']['parameter_name'],
                'page_number' => $tree['config']['page_number'],
                'sequence_number' => $tree['config']['sequence_no'],
                'command_frame_count' => $commandFrameCount,
                'response_frame_count' => $responseFrameCount,
                'error_frame_count' => $errorFrameCount,
                'page_names' => $pageNames,
                'product_description' => $tree['config']['description']
            ),
            'payload' => $payload
        );
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
    
        $item = $this->db->table('efactorproduct')
            ->select('model_number, model_name')
            ->where('id', $itemId)
            ->get()
            ->getRowArray();
    
        return trim((string) ($item['model_number'] ?? '')) ?: trim((string) ($item['model_name'] ?? ''));
    }

    public function getApiParameterNamesForLinking($itemId = 0, $userId = null, $modelno = '')
    {
        $params = array();
        $addParam = function ($value) use (&$params) {
            $value = trim((string) $value);
    
            if ($value !== '' && !in_array($value, $params, true)) {
                $params[] = $value;
            }
        };
    
        $apiBuilder = $this->db->table('api_product_configs')
            ->select('parameter_name')
            ->where('parameter_name !=', '');
    
        if ((int) $itemId > 0) {
            $apiBuilder->where('item_id', (int) $itemId);
        }
    
        if ($userId !== null && $userId !== '') {
            $apiBuilder->where('user_id', (int) $userId);
        }
    
        foreach ($apiBuilder->orderBy('id', 'DESC')->get()->getResultArray() as $row) {
            $addParam($row['parameter_name'] ?? '');
        }
    
        $legacyBuilder = $this->db->table('product_api')
            ->select('parameter_name')
            ->where('parameter_name !=', '');
    
        if ((int) $itemId > 0) {
            $legacyBuilder->where('itemid', (int) $itemId);
        } elseif ($modelno !== '') {
            $legacyBuilder->where('model_name', $modelno);
        }
    
        if ($userId !== null && $userId !== '') {
            $legacyBuilder->where('user_id', (int) $userId);
        }
    
        foreach ($legacyBuilder->orderBy('id', 'DESC')->get()->getResultArray() as $row) {
            $addParam($row['parameter_name'] ?? '');
        }
    
        if (empty($params) && $modelno !== '') {
            foreach ($this->extract_apis($userId, $modelno) as $param) {
                $addParam($param);
            }
        }
    
        return $params;
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

    private function apiTreeArrayValue($array, $key, $default = null)
    {
        return is_array($array) && array_key_exists($key, $array) ? $array[$key] : $default;
    }

    private function apiTreeFrameFirst($meta, $frame, $keys, $default = null)
    {
        $value = $this->apiTreeFirst($meta, $keys, null);
    
        if ($value !== null && $value !== '') {
            return $value;
        }
    
        return $this->apiTreeFirst($frame, $keys, $default);
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

         function get_itemapis_for_view($itemid, $user_id = null) {
             $itemid = (int) $itemid;
             $order_table = $this->db->prefixTable('product_api');
             $where = array("$order_table.itemid = $itemid");
    
             if ($user_id !== null && $user_id !== '') {
                 $where[] = "$order_table.user_id = " . $this->db->escape($user_id);
             }
    
             $sql = "SELECT $order_table.* FROM $order_table WHERE " . implode(" AND ", $where) . " ORDER BY $order_table.id DESC";
    
             return $this->db->query($sql);
         }

    function get_details_api($modelname, $parameter_name, $product_name) {
    
    
    
    //$sql = "SELECT * FROM `product_api` WHERE parameter_name = '$parameter_name' AND model_name = '$modelname'";
    
    $sql = "SELECT * FROM `product_api` WHERE parameter_name = '$parameter_name' AND model_name = '$modelname' AND product_name = '$product_name'";
    return $this->db->query($sql);
    
    }

    function saveapi($data, $additional_data, $respframe_data, $errorframe_data, $user_id) {
        $table = $this->db->prefixTable('product_api');
        $results = []; // Array to store query results
     
        $existing_product = $this->db->table('product_api')
                          ->where('parameter_name', $data['cmd_parametername'],)
                          ->where('model_name', $data['modelname'],)
                          ->where('user_id', $user_id)
                          ->get()
                          ->getRow();
      
     if ($existing_product) { 
        return "parameter_name already exists in this model.";
    } else { 
    $sql = "INSERT INTO $table (brand_name, product_name, model_name, product_description, number_of_pages,sequence_no, page_names, date, itemid, parameter_name, cmd_dataflow, cmd_framesize, cmd_noofrows, cmd_frametrigger, cmd_multipleui, cmd_triggerui,resp_dataflow, resp_framesize, resp_noofrows, resp_frametrigger, resp_multipleui,resp_triggerui, error_dataflow, error_framesize, error_noofrows, error_frametrigger, error_multipleui,error_triggerui, 
    cmd_startBytePosition, cmd_endBytePosition, cmd_fieldDescription, cmd_numberOfBytes, cmd_bitSize, cmd_dataType, cmd_valueDefinition, cmd_displayValueMin, cmd_displayValueMax, cmd_valueInHexMin, cmd_valueInHexMax, cmd_uiType, cmd_uiId, cmd_asperui,cmd_sno_asperui,cmd_asperui_values, cmd_uiPageNo, cmd_uiDescriptionInOut, cmd_sliderStepValue, cmd_defaultvalue, cmd_developerNotes, 
    resp_startBytePosition,resp_endBytePosition, resp_fieldDescription, resp_numberOfBytes, resp_bitSize, resp_dataType, resp_valueDefinition, resp_displayValueMin, resp_displayValueMax, resp_valueInHexMin, resp_valueInHexMax, resp_uiType, resp_uiId, resp_asperui, resp_sno_asperui, resp_asperui_values, resp_uiPageNo, resp_uiDescriptionInOut, resp_sliderStepValue, resp_defaultvalue, resp_developerNotes, 
    error_startBytePosition, error_endBytePosition, error_fieldDescription, error_numberOfBytes, error_bitSize, error_dataType, error_valueDefinition, error_displayValueMin, error_displayValueMax, error_valueInHexMin, error_valueInHexMax, error_uiType, error_uiId, error_asperui, error_sno_asperui, error_asperui_values,error_uiPageNo, error_uiDescriptionInOut, error_sliderStepValue, error_defaultvalue, error_developerNotes, user_id) 
    VALUES(?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    }
    
    
    
    
    
        $cmd_startBytePosition = [];
        $cmd_endBytePosition = [];
        $cmd_fieldDescription = [];
        $cmd_numberOfBytes = [];
        $cmd_bitSize = [];
        $cmd_dataType = [];
        $cmd_valueDefinition = [];
        $cmd_displayValueMin = [];
        $cmd_displayValueMax = [];
        $cmd_valueInHexMin = [];
        $cmd_valueInHexMax = [];
        $cmd_uiType = [];
        $cmd_uiId = [];
        $cmd_asperui = [];
        $cmd_sno_asperui = [];
        $cmd_asperuivalue = [];
        $cmd_uiPageNo = [];
        $cmd_uiDescriptionInOut = [];
        $cmd_sliderStepValue = [];
        $cmd_defaultvalue = [];
        $cmd_developerNotes = [];
    
        $resp_startBytePosition = [];
        $resp_endBytePosition = [];
        $resp_fieldDescription = [];
        $resp_numberOfBytes = [];
        $resp_bitSize = [];
        $resp_dataType = [];
        $resp_valueDefinition = [];
        $resp_displayValueMin = [];
        $resp_displayValueMax = [];
        $resp_valueInHexMin = [];
        $resp_valueInHexMax = [];
        $resp_uiType = [];
        $resp_uiId = [];
        $resp_asperui = [];
        $resp_sno_asperui = [];
        $resp_asperuivalue = [];
        $resp_uiPageNo = [];
        $resp_uiDescriptionInOut = [];
        $resp_sliderStepValue = [];
        $resp_defaultvalue = [];
        $resp_developerNotes = [];
    
    
        $error_startBytePosition = [];
        $error_endBytePosition = [];
        $error_fieldDescription = [];
        $error_numberOfBytes = [];
        $error_bitSize = [];
        $error_dataType = [];
        $error_valueDefinition = [];
        $error_displayValueMin = [];
        $error_displayValueMax = [];
        $error_valueInHexMin = [];
        $error_valueInHexMax = [];
        $error_uiType = [];
        $error_uiId = [];
        $error_asperui = [];
        $error_sno_asperui = [];
        $error_asperuivalue = [];
        $error_uiPageNo = [];
        $error_uiDescriptionInOut = [];
        $error_sliderStepValue = [];
        $error_defaultvalue = [];
        $error_developerNotes = [];
    
    
    
        // Loop through additional data and extract values
        foreach ($additional_data as $cmd_data) {
            $cmd_startBytePosition[] = $cmd_data['cmd_startBytePosition'];
            $cmd_endBytePosition[] = $cmd_data['cmd_endBytePosition'];
            $cmd_fieldDescription[] = $cmd_data['cmd_fieldDescription'];
            $cmd_numberOfBytes[] = $cmd_data['cmd_numberOfBytes'];
            $cmd_bitSize[] = $cmd_data['cmd_bitSize'];
            $cmd_dataType[] = $cmd_data['cmd_dataType'];
            $cmd_valueDefinition[] = $cmd_data['cmd_valueDefinition'];
            $cmd_displayValueMin[] = $cmd_data['cmd_displayValueMin'];
            $cmd_displayValueMax[] = $cmd_data['cmd_displayValueMax'];
            $cmd_valueInHexMin[] = $cmd_data['cmd_valueInHexMin'];
            $cmd_valueInHexMax[] = $cmd_data['cmd_valueInHexMax'];
            $cmd_uiType[] = $cmd_data['cmd_uiType'];
            $cmd_uiId[] = $cmd_data['cmd_uiId'];
            $cmd_asperui[] = $cmd_data['cmd_asperui'];
            $cmd_sno_asperui[] = $cmd_data['cmd_sno_asperui'];
            $cmd_asperuivalue[] = $cmd_data['cmd_asperuivalue'];
            $cmd_uiPageNo[] = $cmd_data['cmd_uiPageNo'];
            $cmd_uiDescriptionInOut[] = $cmd_data['cmd_uiDescriptionInOut'];
            $cmd_sliderStepValue[] = $cmd_data['cmd_sliderStepValue'];
            $cmd_defaultvalue[] = $cmd_data['cmd_defaultvalue'];
            $cmd_developerNotes[] = $cmd_data['cmd_developerNotes'];
        }
    
        // Convert arrays to comma-separated strings
        $cmd_startBytePosition = implode(',', $cmd_startBytePosition);
        $cmd_endBytePosition = implode(',', $cmd_endBytePosition);
        $cmd_fieldDescription = implode(',', $cmd_fieldDescription);
        $cmd_numberOfBytes = implode(',', $cmd_numberOfBytes);
        $cmd_bitSize = implode(',', $cmd_bitSize);
        $cmd_dataType = implode(',', $cmd_dataType);
        $cmd_valueDefinition = implode(',', $cmd_valueDefinition);
        $cmd_displayValueMin = implode(',', $cmd_displayValueMin);
        $cmd_displayValueMax = implode(',', $cmd_displayValueMax);
        $cmd_valueInHexMin = implode(',', $cmd_valueInHexMin);
        $cmd_valueInHexMax = implode(',', $cmd_valueInHexMax);
        $cmd_uiType = implode(',', $cmd_uiType);
        $cmd_uiId = implode(',', $cmd_uiId);
        $cmd_asperui = implode(',', $cmd_asperui);
        $cmd_sno_asperui = implode(',', $cmd_sno_asperui);
        $cmd_asperuivalue = implode(',', $cmd_asperuivalue);
        $cmd_uiPageNo = implode(',', $cmd_uiPageNo);
        $cmd_uiDescriptionInOut = implode(',', $cmd_uiDescriptionInOut);
        $cmd_sliderStepValue = implode(',', $cmd_sliderStepValue);
        $cmd_defaultvalue = implode(',', $cmd_defaultvalue);
        $cmd_developerNotes = implode(',', $cmd_developerNotes);
    
    
    
    if (is_array($respframe_data) && !empty($respframe_data)) {
        foreach ($respframe_data as $resp_data) {
            $resp_startBytePosition[] = $resp_data['resp_startBytePosition'];
            $resp_endBytePosition[] = $resp_data['resp_endBytePosition'];
            $resp_fieldDescription[] = $resp_data['resp_fieldDescription'];
            $resp_numberOfBytes[] = $resp_data['resp_numberOfBytes'];
            $resp_bitSize[] = $resp_data['resp_bitSize'];
            $resp_dataType[] = $resp_data['resp_dataType'];
            $resp_valueDefinition[] = $resp_data['resp_valueDefinition'];
            $resp_displayValueMin[] = $resp_data['resp_displayValueMin'];
            $resp_displayValueMax[] = $resp_data['resp_displayValueMax'];
            $resp_valueInHexMin[] = $resp_data['resp_valueInHexMin'];
            $resp_valueInHexMax[] = $resp_data['resp_valueInHexMax'];
            $resp_uiType[] = $resp_data['resp_uiType'];
            $resp_uiId[] = $resp_data['resp_uiId'];
            $resp_asperui[] = $resp_data['resp_asperui'];
            $resp_sno_asperui[] = $resp_data['resp_sno_asperui'];
            $resp_asperuivalue[] = $resp_data['resp_asperuivalue'];
            $resp_uiPageNo[] = $resp_data['resp_uiPageNo'];
            $resp_uiDescriptionInOut[] = $resp_data['resp_uiDescriptionInOut'];
            $resp_sliderStepValue[] = $resp_data['resp_sliderStepValue'];
            $resp_defaultvalue[] = $resp_data['resp_defaultvalue'];
            $resp_developerNotes[] = $resp_data['resp_developerNotes'];
        }
    
        
        $resp_startBytePosition = implode(',', $resp_startBytePosition);
        $resp_endBytePosition = implode(',', $resp_endBytePosition);
        $resp_fieldDescription = implode(',', $resp_fieldDescription);
        $resp_numberOfBytes = implode(',', $resp_numberOfBytes);
        $resp_bitSize = implode(',', $resp_bitSize);
        $resp_dataType = implode(',', $resp_dataType);
        $resp_valueDefinition = implode(',', $resp_valueDefinition);
        $resp_displayValueMin = implode(',', $resp_displayValueMin);
        $resp_displayValueMax = implode(',', $resp_displayValueMax);
        $resp_valueInHexMin = implode(',', $resp_valueInHexMin);
        $resp_valueInHexMax = implode(',', $resp_valueInHexMax);
        $resp_uiType = implode(',', $resp_uiType);
        $resp_uiId = implode(',', $resp_uiId);
        $resp_asperui = implode(',', $resp_asperui);
        $resp_sno_asperui = implode(',', $resp_sno_asperui);
        $resp_asperuivalue = implode(',', $resp_asperuivalue);
        $resp_uiPageNo = implode(',', $resp_uiPageNo);
        $resp_uiDescriptionInOut = implode(',', $resp_uiDescriptionInOut);
        $resp_sliderStepValue = implode(',', $resp_sliderStepValue);
        $resp_defaultvalue = implode(',', $resp_defaultvalue);
        $resp_developerNotes = implode(',', $resp_developerNotes);
    
    } else {
        // Handle the case when $respframe_data is not iterable
       // echo "Response frame data is empty or not an array.";
    
    
    $resp_startBytePosition = '';
        $resp_endBytePosition = '';
        $resp_fieldDescription = '';
        $resp_numberOfBytes = '';
        $resp_bitSize = '';
        $resp_dataType = '';
        $resp_valueDefinition = '';
        $resp_displayValueMin = '';
        $resp_displayValueMax = '';
        $resp_valueInHexMin = '';
        $resp_valueInHexMax = '';
        $resp_uiType = '';
        $resp_uiId = '';
        $resp_asperui = '';
        $resp_sno_asperui = '';
        $resp_asperuivalue = '';
        $resp_uiPageNo = '';
        $resp_uiDescriptionInOut = '';
        $resp_sliderStepValue = '';
        $resp_defaultvalue = '';
        $resp_developerNotes = '';
    
    }
    
    
    
    
    if (is_array($errorframe_data) && !empty($errorframe_data)) {
        foreach ($errorframe_data as $error_data) {
            $error_startBytePosition[] = $error_data['error_startBytePosition'];
            $error_endBytePosition[] = $error_data['error_endBytePosition'];
            $error_fieldDescription[] = $error_data['error_fieldDescription'];
            $error_numberOfBytes[] = $error_data['error_numberOfBytes'];
            $error_bitSize[] = $error_data['error_bitSize'];
            $error_dataType[] = $error_data['error_dataType'];
            $error_valueDefinition[] = $error_data['error_valueDefinition'];
            $error_displayValueMin[] = $error_data['error_displayValueMin'];
            $error_displayValueMax[] = $error_data['error_displayValueMax'];
            $error_valueInHexMin[] = $error_data['error_valueInHexMin'];
            $error_valueInHexMax[] = $error_data['error_valueInHexMax'];
            $error_uiType[] = $error_data['error_uiType'];
            $error_uiId[] = $error_data['error_uiId'];
            $error_asperui[] = $error_data['error_asperui'];
            $error_sno_asperui[] = $error_data['error_sno_asperui'];
            $error_asperuivalue[] = $error_data['error_asperuivalue'];
            $error_uiPageNo[] = $error_data['error_uiPageNo'];
            $error_uiDescriptionInOut[] = $error_data['error_uiDescriptionInOut'];
            $error_sliderStepValue[] = $error_data['error_sliderStepValue'];
            $error_defaultvalue[] = $error_data['error_defaultvalue'];
            $error_developerNotes[] = $error_data['error_developerNotes'];
        }
    
        
        $error_startBytePosition = implode(',', $error_startBytePosition);
        $error_endBytePosition = implode(',', $error_endBytePosition);
        $error_fieldDescription = implode(',', $error_fieldDescription);
        $error_numberOfBytes = implode(',', $error_numberOfBytes);
        $error_bitSize = implode(',', $error_bitSize);
        $error_dataType = implode(',', $error_dataType);
        $error_valueDefinition = implode(',', $error_valueDefinition);
        $error_displayValueMin = implode(',', $error_displayValueMin);
        $error_displayValueMax = implode(',', $error_displayValueMax);
        $error_valueInHexMin = implode(',', $error_valueInHexMin);
        $error_valueInHexMax = implode(',', $error_valueInHexMax);
        $error_uiType = implode(',', $error_uiType);
        $error_uiId = implode(',', $error_uiId);
        $error_asperui = implode(',', $error_asperui);
        $error_sno_asperui = implode(',', $error_sno_asperui);
        $error_asperuivalue = implode(',', $error_asperuivalue);
        $error_uiPageNo = implode(',', $error_uiPageNo);
        $error_uiDescriptionInOut = implode(',', $error_uiDescriptionInOut);
        $error_sliderStepValue = implode(',', $error_sliderStepValue);
        $error_defaultvalue = implode(',', $error_defaultvalue);
        $error_developerNotes = implode(',', $error_developerNotes);
    
    
    } else {
        // Handle the case when $respframe_data is not iterable
       // echo "error frame data is empty or not an array.";
    
         $error_startBytePosition = '';
        $error_endBytePosition = '';
        $error_fieldDescription = '';
        $error_numberOfBytes = '';
        $error_bitSize = '';
        $error_dataType = '';
        $error_valueDefinition = '';
        $error_displayValueMin = '';
        $error_displayValueMax = '';
        $error_valueInHexMin = '';
        $error_valueInHexMax = '';
        $error_uiType = '';
        $error_uiId = '';
        $error_asperui = '';
        $error_sno_asperui = '';
        $error_asperuivalue = '';
        $error_uiPageNo = '';
        $error_uiDescriptionInOut = '';
        $error_sliderStepValue = '';
        $error_defaultvalue = '';
        $error_developerNotes = '';
    
    
    }
    
    
        // Construct the values array
       $values = [
            
            $data['brandname'],
          //  $data['brandlogo'],
            $data['productname'],
          //  $data['imageDropdown'],
            $data['modelname'],
            $data['productdescription'],
            $data['numberofpages'],
            $data['Sequence'],
            $data['pagenames'],
            $data['date'],
            $data['itemid'], // Add itemid here
    
    
    
            $data['cmd_parametername'],
            $data['cmd_data_flow_direction'],
            $data['cmd_framesizeinbytes'],  
            $data['cmd_noofrows'],
             $data['cmd_frametrigger'],
             $data['cmd_multipleui'],
              $data['cmd_triggerui'],
            $data['resp_data_flow_direction'],
            $data['resp_framesizeinbytes'],
             $data['resp_noofrows'],
            $data['resp_frametrigger'],
            $data['resp_multipleui'],
            $data['resp_triggerui'],
            $data['error_data_flow_direction'],
            $data['error_framesizeinbytes'],
             $data['error_noofrows'],
            $data['error_frametrigger'],
            $data['error_multipleui'],
            $data['error_triggerui'],
    
    
         
            $cmd_startBytePosition,
            $cmd_endBytePosition,
            $cmd_fieldDescription,
            $cmd_numberOfBytes,
            $cmd_bitSize,
            $cmd_dataType,
            $cmd_valueDefinition,
            $cmd_displayValueMin,
            $cmd_displayValueMax,
            $cmd_valueInHexMin,
            $cmd_valueInHexMax,
            $cmd_uiType,
            $cmd_uiId,
            $cmd_asperui,
            $cmd_sno_asperui,
            $cmd_asperuivalue,
            $cmd_uiPageNo,     
            $cmd_uiDescriptionInOut,
            $cmd_sliderStepValue,
            $cmd_defaultvalue,
            $cmd_developerNotes,
            $resp_startBytePosition,
            $resp_endBytePosition,
            $resp_fieldDescription,
            $resp_numberOfBytes,
            $resp_bitSize,
            $resp_dataType,
            $resp_valueDefinition,
            $resp_displayValueMin,
            $resp_displayValueMax,
            $resp_valueInHexMin,
            $resp_valueInHexMax,
            $resp_uiType,
            $resp_uiId,
            $resp_asperui,
            $resp_sno_asperui,
            $resp_asperuivalue,
            $resp_uiPageNo,     
            $resp_uiDescriptionInOut,
            $resp_sliderStepValue,
            $resp_defaultvalue,
            $resp_developerNotes,
            $error_startBytePosition,
            $error_endBytePosition,
            $error_fieldDescription,
            $error_numberOfBytes,
            $error_bitSize,
            $error_dataType,
            $error_valueDefinition,
            $error_displayValueMin,
            $error_displayValueMax,
            $error_valueInHexMin,
            $error_valueInHexMax,
            $error_uiType,
            $error_uiId,
            $error_asperui,
            $error_sno_asperui,
            $error_asperuivalue,
            $error_uiPageNo,       
            $error_uiDescriptionInOut,
            $error_sliderStepValue,
            $error_defaultvalue,
            $error_developerNotes,
            $user_id
            
    
    
        ];
    
    
    
    
    
        // Execute the query
        $result = $this->db->query($sql, $values);
    
        // Check for database errors
        if (!$result) {
            // Log the database error
            log_message('error', 'Database error: ' . $this->db->error()['message']);
            $results[] = false; // Store false in the results array indicating failure
        } else {
            $results[] = true; // Store true in the results array indicating success
        }
    
        
    
    
    
        return $results; 
    }

    function saveapi_edit($data, $additional_data, $respframe_data, $errorframe_data) {
        
        $table = $this->db->prefixTable('product_api');
        $results = []; 
    
    
        $cmd_startBytePosition = [];
        $cmd_endBytePosition = [];
        $cmd_fieldDescription = [];
        $cmd_numberOfBytes = [];
        $cmd_bitSize = [];
        $cmd_dataType = [];
        $cmd_valueDefinition = [];
        $cmd_displayValueMin = [];
        $cmd_displayValueMax = [];
        $cmd_valueInHexMin = [];
        $cmd_valueInHexMax = [];
        $cmd_uiType = [];
        $cmd_uiId = [];
        $cmd_uiPageNo = [];
        $cmd_asperui = [];
        $cmd_sno_asperui = [];
        $cmd_asperuivalue = [];
        $cmd_uiDescriptionInOut = [];
        $cmd_sliderStepValue = [];
        $cmd_defaultvalue = [];
        $cmd_developerNotes = [];
        $resp_startBytePosition = [];
        $resp_endBytePosition = [];
        $resp_fieldDescription = [];
        $resp_numberOfBytes = [];
        $resp_bitSize = [];
        $resp_dataType = [];
        $resp_valueDefinition = [];
        $resp_displayValueMin = [];
        $resp_displayValueMax = [];
        $resp_valueInHexMin = [];
        $resp_valueInHexMax = [];
        $resp_uiType = [];
        $resp_uiId = [];
        $resp_uiPageNo = [];
        $resp_asperui = [];
        $resp_sno_asperui = [];
        $resp_asperuivalue = [];
        $resp_uiDescriptionInOut = [];
        $resp_sliderStepValue = [];
        $resp_defaultvalue = [];
        $resp_developerNotes = [];
        $error_startBytePosition = [];
        $error_endBytePosition = [];
        $error_fieldDescription = [];
        $error_numberOfBytes = [];
        $error_bitSize = [];
        $error_dataType = [];
        $error_valueDefinition = [];
        $error_displayValueMin = [];
        $error_displayValueMax = [];
        $error_valueInHexMin = [];
        $error_valueInHexMax = [];
        $error_uiType = [];
        $error_uiId = [];
        $error_uiPageNo = [];
        $error_asperui = [];
        $error_sno_asperui = [];
        $error_asperuivalue = [];
        $error_uiDescriptionInOut = [];
        $error_sliderStepValue = [];
        $error_defaultvalue = [];
        $error_developerNotes = [];
    
        foreach ($additional_data as $cmd_data) {
            $cmd_startBytePosition[] = $cmd_data['cmd_startBytePosition'];
            $cmd_endBytePosition[] = $cmd_data['cmd_endBytePosition'];
            $cmd_fieldDescription[] = $cmd_data['cmd_fieldDescription'];
            $cmd_numberOfBytes[] = $cmd_data['cmd_numberOfBytes'];
            $cmd_bitSize[] = $cmd_data['cmd_bitSize'];
            $cmd_dataType[] = $cmd_data['cmd_dataType'];
            $cmd_valueDefinition[] = $cmd_data['cmd_valueDefinition'];
            $cmd_displayValueMin[] = $cmd_data['cmd_displayValueMin'];
            $cmd_displayValueMax[] = $cmd_data['cmd_displayValueMax'];
            $cmd_valueInHexMin[] = $cmd_data['cmd_valueInHexMin'];
            $cmd_valueInHexMax[] = $cmd_data['cmd_valueInHexMax'];
            $cmd_uiType[] = $cmd_data['cmd_uiType'];
            $cmd_uiId[] = $cmd_data['cmd_uiId'];
            $cmd_uiPageNo[] = $cmd_data['cmd_uiPageNo'];
            $cmd_asperui[] = $cmd_data['cmd_asperui'];
            $cmd_sno_asperui[] = $cmd_data['cmd_sno_asperui'];
            $cmd_asperuivalue[] = $cmd_data['cmd_asperuivalue'];
            $cmd_uiDescriptionInOut[] = $cmd_data['cmd_uiDescriptionInOut'];
            $cmd_sliderStepValue[] = $cmd_data['cmd_sliderStepValue'];
            $cmd_defaultvalue[] = $cmd_data['cmd_defaultvalue'];
            $cmd_developerNotes[] = $cmd_data['cmd_developerNotes'];
        }
    
        $cmd_startBytePosition = implode(',', $cmd_startBytePosition);
        $cmd_endBytePosition = implode(',', $cmd_endBytePosition);
        $cmd_fieldDescription = implode(',', $cmd_fieldDescription);
        $cmd_numberOfBytes = implode(',', $cmd_numberOfBytes);
        $cmd_bitSize = implode(',', $cmd_bitSize);
        $cmd_dataType = implode(',', $cmd_dataType);
        $cmd_valueDefinition = implode(',', $cmd_valueDefinition);
        $cmd_displayValueMin = implode(',', $cmd_displayValueMin);
        $cmd_displayValueMax = implode(',', $cmd_displayValueMax);
        $cmd_valueInHexMin = implode(',', $cmd_valueInHexMin);
        $cmd_valueInHexMax = implode(',', $cmd_valueInHexMax);
        $cmd_uiType = implode(',', $cmd_uiType);
        $cmd_uiId = implode(',', $cmd_uiId);
        $cmd_uiPageNo = implode(',', $cmd_uiPageNo);
        $cmd_asperui = implode(',', $cmd_asperui);
        $cmd_sno_asperui = implode(',', $cmd_sno_asperui);
        $cmd_asperuivalue = implode(',', $cmd_asperuivalue);
        $cmd_uiDescriptionInOut = implode(',', $cmd_uiDescriptionInOut);
        $cmd_sliderStepValue = implode(',', $cmd_sliderStepValue);
        $cmd_defaultvalue = implode(',', $cmd_defaultvalue);
        $cmd_developerNotes = implode(',', $cmd_developerNotes);
    
     
        foreach ($respframe_data as $resp_data) {
            $resp_startBytePosition[] = $resp_data['resp_startBytePosition'];
            $resp_endBytePosition[] = $resp_data['resp_endBytePosition'];
            $resp_fieldDescription[] = $resp_data['resp_fieldDescription'];
            $resp_numberOfBytes[] = $resp_data['resp_numberOfBytes'];
            $resp_bitSize[] = $resp_data['resp_bitSize'];
            $resp_dataType[] = $resp_data['resp_dataType'];
            $resp_valueDefinition[] = $resp_data['resp_valueDefinition'];
            $resp_displayValueMin[] = $resp_data['resp_displayValueMin'];
            $resp_displayValueMax[] = $resp_data['resp_displayValueMax'];
            $resp_valueInHexMin[] = $resp_data['resp_valueInHexMin'];
            $resp_valueInHexMax[] = $resp_data['resp_valueInHexMax'];
            $resp_uiType[] = $resp_data['resp_uiType'];
            $resp_uiId[] = $resp_data['resp_uiId'];
            $resp_uiPageNo[] = $resp_data['resp_uiPageNo'];
            $resp_asperui[] = $resp_data['resp_asperui'];
            $resp_sno_asperui[] = $resp_data['resp_sno_asperui'];
            $resp_asperuivalue[] = $resp_data['resp_asperuivalue'];
            $resp_uiDescriptionInOut[] = $resp_data['resp_uiDescriptionInOut'];
            $resp_sliderStepValue[] = $resp_data['resp_sliderStepValue'];
            $resp_defaultvalue[] = $resp_data['resp_defaultvalue'];
            $resp_developerNotes[] = $resp_data['resp_developerNotes'];
        }
    
        
        $resp_startBytePosition = implode(',', $resp_startBytePosition);
        $resp_endBytePosition = implode(',', $resp_endBytePosition);
        $resp_fieldDescription = implode(',', $resp_fieldDescription);
        $resp_numberOfBytes = implode(',', $resp_numberOfBytes);
        $resp_bitSize = implode(',', $resp_bitSize);
        $resp_dataType = implode(',', $resp_dataType);
        $resp_valueDefinition = implode(',', $resp_valueDefinition);
        $resp_displayValueMin = implode(',', $resp_displayValueMin);
        $resp_displayValueMax = implode(',', $resp_displayValueMax);
        $resp_valueInHexMin = implode(',', $resp_valueInHexMin);
        $resp_valueInHexMax = implode(',', $resp_valueInHexMax);
        $resp_uiType = implode(',', $resp_uiType);
        $resp_uiId = implode(',', $resp_uiId);
        $resp_uiPageNo = implode(',', $resp_uiPageNo);
        $resp_asperui = implode(',', $resp_asperui);
        $resp_sno_asperui = implode(',', $resp_sno_asperui);
        $resp_asperuivalue = implode(',', $resp_asperuivalue);
        $resp_uiDescriptionInOut = implode(',', $resp_uiDescriptionInOut);
        $resp_sliderStepValue = implode(',', $resp_sliderStepValue);
        $resp_defaultvalue = implode(',', $resp_defaultvalue);
        $resp_developerNotes = implode(',', $resp_developerNotes);
    
     
    
    
        foreach ($errorframe_data as $error_data) {
            $error_startBytePosition[] = $error_data['error_startBytePosition'];
            $error_endBytePosition[] = $error_data['error_endBytePosition'];
            $error_fieldDescription[] = $error_data['error_fieldDescription'];
            $error_numberOfBytes[] = $error_data['error_numberOfBytes'];
            $error_bitSize[] = $error_data['error_bitSize'];
            $error_dataType[] = $error_data['error_dataType'];
            $error_valueDefinition[] = $error_data['error_valueDefinition'];
            $error_displayValueMin[] = $error_data['error_displayValueMin'];
            $error_displayValueMax[] = $error_data['error_displayValueMax'];
            $error_valueInHexMin[] = $error_data['error_valueInHexMin'];
            $error_valueInHexMax[] = $error_data['error_valueInHexMax'];
            $error_uiType[] = $error_data['error_uiType'];
            $error_uiId[] = $error_data['error_uiId'];
            $error_uiPageNo[] = $error_data['error_uiPageNo'];
            $error_asperui[] = $error_data['error_asperui'];
            $error_sno_asperui[] = $error_data['error_sno_asperui'];
            $error_asperuivalue[] = $error_data['error_asperuivalue'];
            $error_uiDescriptionInOut[] = $error_data['error_uiDescriptionInOut'];
            $error_sliderStepValue[] = $error_data['error_sliderStepValue'];
            $error_defaultvalue[] = $error_data['error_defaultvalue'];
            $error_developerNotes[] = $error_data['error_developerNotes'];
        }
    
        
        $error_startBytePosition = implode(',', $error_startBytePosition);
        $error_endBytePosition = implode(',', $error_endBytePosition);
        $error_fieldDescription = implode(',', $error_fieldDescription);
        $error_numberOfBytes = implode(',', $error_numberOfBytes);
        $error_bitSize = implode(',', $error_bitSize);
        $error_dataType = implode(',', $error_dataType);
        $error_valueDefinition = implode(',', $error_valueDefinition);
        $error_displayValueMin = implode(',', $error_displayValueMin);
        $error_displayValueMax = implode(',', $error_displayValueMax);
        $error_valueInHexMin = implode(',', $error_valueInHexMin);
        $error_valueInHexMax = implode(',', $error_valueInHexMax);
        $error_uiType = implode(',', $error_uiType);
        $error_uiId = implode(',', $error_uiId);
        $error_uiPageNo = implode(',', $error_uiPageNo);
        $error_asperui = implode(',', $error_asperui);
        $error_sno_asperui = implode(',', $error_sno_asperui);
        $error_asperuivalue = implode(',', $error_asperuivalue);
        $error_uiDescriptionInOut = implode(',', $error_uiDescriptionInOut);
        $error_sliderStepValue = implode(',', $error_sliderStepValue);
        $error_defaultvalue = implode(',', $error_defaultvalue);
        $error_developerNotes = implode(',', $error_developerNotes);
    
    
    
    
            $values = [
            
            $data['brandname'],
            $data['productname'],
            $data['modelname'],
            $data['productdescription'],
            $data['numberofpages'],
            $data['Sequence'],
            $data['pagenames'],
            $data['date'],
    
    
            $data['cmd_parametername'],
            $data['cmd_data_flow_direction'],
            $data['cmd_noofrows'],
            $data['cmd_framesizeinbytes'],
            $data['cmd_frametrigger'],
            $data['cmd_multipleui'],
            $data['cmd_triggerui'],
            
            $data['resp_data_flow_direction'],
            $data['resp_noofrows'],
            $data['resp_framesizeinbytes'],
            $data['resp_frametrigger'],
            $data['resp_multipleui'],
            $data['resp_triggerui'],
           
            $data['error_data_flow_direction'],
            $data['error_noofrows'],
            $data['error_framesizeinbytes'],
            $data['error_frametrigger'],
            $data['error_multipleui'],
            $data['error_triggerui'],
            
     
            $cmd_startBytePosition,
            $cmd_endBytePosition,
            $cmd_fieldDescription,
            $cmd_numberOfBytes,
            $cmd_bitSize,
            $cmd_dataType,
            $cmd_valueDefinition,
            $cmd_displayValueMin,
            $cmd_displayValueMax,
            $cmd_valueInHexMin,
            $cmd_valueInHexMax,
            $cmd_uiType,
            $cmd_uiId,
            $cmd_uiPageNo,
            $cmd_asperui,
            $cmd_sno_asperui,
            $cmd_asperuivalue,
            $cmd_uiDescriptionInOut,
            $cmd_sliderStepValue,
            $cmd_defaultvalue,
            $cmd_developerNotes,
            $resp_startBytePosition,
            $resp_endBytePosition,
            $resp_fieldDescription,
            $resp_numberOfBytes,
            $resp_bitSize,
            $resp_dataType,
            $resp_valueDefinition,
            $resp_displayValueMin,
            $resp_displayValueMax,
            $resp_valueInHexMin,
            $resp_valueInHexMax,
            $resp_uiType,
            $resp_uiId,
            $resp_uiPageNo,
            $resp_asperui,
            $resp_sno_asperui,
            $resp_asperuivalue,
            $resp_uiDescriptionInOut,
            $resp_sliderStepValue,
            $resp_defaultvalue,
            $resp_developerNotes,
            $error_startBytePosition,
            $error_endBytePosition,
            $error_fieldDescription,
            $error_numberOfBytes,
            $error_bitSize,
            $error_dataType,
            $error_valueDefinition,
            $error_displayValueMin,
            $error_displayValueMax,
            $error_valueInHexMin,
            $error_valueInHexMax,
            $error_uiType,
            $error_uiId,
            $error_uiPageNo,
            $error_asperui,
            $error_sno_asperui,
            $error_asperuivalue,
            $error_uiDescriptionInOut,
            $error_sliderStepValue,
            $error_defaultvalue,
            $error_developerNotes
    
    
        ];
    
    
    
     
    $brandname = $data['brandname'];
    //$brandlogo = $data['brandlogo'];
    $productname = $data['productname'];
    //$imageDropdown = $data['imageDropdown'];
    $modelname = $data['modelname'];
    $productdescription = $data['productdescription'];
    $numberofpages = $data['numberofpages'];
    $Sequence = $data['Sequence'];
    $pagenames = $data['pagenames'];
    $date = $data['date'];
     
    
    
    $parameter_name = $data['cmd_parametername'];
    $cmd_dataflow = $data['cmd_data_flow_direction'];
    $cmd_noofrows = $data['cmd_noofrows'];
    $cmd_framesize = $data['cmd_framesizeinbytes'];
    $cmd_frametrigger = $data['cmd_frametrigger'];
    $cmd_multipleui = $data['cmd_multipleui'];
    $cmd_triggerui = $data['cmd_triggerui'];
    
    $resp_dataflow = $data['resp_data_flow_direction'];
    $resp_noofrows = $data['resp_noofrows'];
    $resp_framesize = $data['resp_framesizeinbytes'];
    $resp_frametrigger = $data['resp_frametrigger'];
    $resp_multipleui = $data['resp_multipleui'];
    $resp_triggerui = $data['resp_triggerui'];
    
    $error_dataflow = $data['error_data_flow_direction'];
    $error_noofrows = $data['error_noofrows'];
    $error_framesize = $data['error_framesizeinbytes']; // Assuming itemid is part of $data array
    $error_frametrigger = $data['error_frametrigger'];
    $error_multipleui = $data['error_multipleui'];
    $error_triggerui = $data['error_triggerui'];
    
    
    $sql = "UPDATE product_api SET 
        
        brand_name = '$brandname', 
        product_name = '$productname', 
        model_name = '$modelname', 
        product_description = '$productdescription', 
        number_of_pages = '$numberofpages', 
        sequence_no = '$Sequence', 
        page_names = '$pagenames', 
        date = '$date', 
    
        parameter_name = '$parameter_name', 
        cmd_dataflow = '$cmd_dataflow', 
        cmd_noofrows = '$cmd_noofrows', 
        cmd_framesize = '$cmd_framesize', 
        cmd_frametrigger = '$cmd_frametrigger', 
        cmd_multipleui = '$cmd_multipleui', 
        cmd_triggerui = '$cmd_triggerui',     
        
        resp_dataflow = '$resp_dataflow', 
        resp_noofrows = '$resp_noofrows', 
        resp_framesize = '$resp_framesize', 
        resp_frametrigger = '$resp_frametrigger', 
        resp_multipleui = '$resp_multipleui', 
        resp_triggerui = '$resp_triggerui', 
        
        error_dataflow = '$error_dataflow',
        error_noofrows = '$error_noofrows', 
        error_framesize = '$error_framesize', 
        error_frametrigger = '$error_frametrigger', 
        error_multipleui = '$error_multipleui', 
        error_triggerui = '$error_triggerui', 
        
     
    
        cmd_startBytePosition = '$cmd_startBytePosition', 
        cmd_endBytePosition = '$cmd_endBytePosition', 
        cmd_fieldDescription = '$cmd_fieldDescription', 
        cmd_numberOfBytes = '$cmd_numberOfBytes', 
        cmd_bitSize = '$cmd_bitSize', 
        cmd_dataType = '$cmd_dataType', 
        cmd_valueDefinition = '$cmd_valueDefinition', 
        cmd_displayValueMin = '$cmd_displayValueMin', 
        cmd_displayValueMax = '$cmd_displayValueMax', 
        cmd_valueInHexMin = '$cmd_valueInHexMin', 
        cmd_valueInHexMax = '$cmd_valueInHexMax', 
        cmd_uiType = '$cmd_uiType', 
        cmd_uiId = '$cmd_uiId', 
        cmd_uiPageNo = '$cmd_uiPageNo', 
        cmd_asperui = '$cmd_asperui', 
        cmd_sno_asperui = '$cmd_sno_asperui', 
        cmd_asperui_values = '$cmd_asperuivalue', 
        cmd_uiDescriptionInOut = '$cmd_uiDescriptionInOut', 
        cmd_sliderStepValue = '$cmd_sliderStepValue', 
        cmd_defaultvalue = '$cmd_defaultvalue', 
        cmd_developerNotes = '$cmd_developerNotes', 
        resp_startBytePosition = '$resp_startBytePosition', 
        resp_endBytePosition = '$resp_endBytePosition', 
        resp_fieldDescription = '$resp_fieldDescription', 
        resp_numberOfBytes = '$resp_numberOfBytes', 
        resp_bitSize = '$resp_bitSize', 
        resp_dataType = '$resp_dataType', 
        resp_valueDefinition = '$resp_valueDefinition', 
        resp_displayValueMin = '$resp_displayValueMin', 
        resp_displayValueMax = '$resp_displayValueMax', 
        resp_valueInHexMin = '$resp_valueInHexMin', 
        resp_valueInHexMax = '$resp_valueInHexMax', 
        resp_uiType = '$resp_uiType', 
        resp_uiId = '$resp_uiId', 
        resp_uiPageNo = '$resp_uiPageNo', 
        resp_asperui = '$resp_asperui', 
        resp_sno_asperui = '$resp_sno_asperui',
        resp_asperui_values = '$resp_asperuivalue', 
        resp_uiDescriptionInOut = '$resp_uiDescriptionInOut', 
        resp_sliderStepValue = '$resp_sliderStepValue',
        resp_defaultvalue = '$resp_defaultvalue',  
        resp_developerNotes = '$resp_developerNotes', 
        error_startBytePosition = '$error_startBytePosition', 
        error_endBytePosition = '$error_endBytePosition', 
        error_fieldDescription = '$error_fieldDescription', 
        error_numberOfBytes = '$error_numberOfBytes', 
        error_bitSize = '$error_bitSize', 
        error_dataType = '$error_dataType', 
        error_valueDefinition = '$error_valueDefinition', 
        error_displayValueMin = '$error_displayValueMin', 
        error_displayValueMax = '$error_displayValueMax', 
        error_valueInHexMin = '$error_valueInHexMin', 
        error_valueInHexMax = '$error_valueInHexMax', 
        error_uiType = '$error_uiType', 
        error_uiId = '$error_uiId', 
        error_uiPageNo = '$error_uiPageNo', 
        error_asperui = '$error_asperui', 
        error_sno_asperui = '$error_sno_asperui', 
        error_asperui_values = '$error_asperuivalue', 
        error_uiDescriptionInOut = '$error_uiDescriptionInOut', 
        error_sliderStepValue = '$error_sliderStepValue', 
        error_defaultvalue = '$error_defaultvalue', 
        error_developerNotes = '$error_developerNotes' 
    WHERE parameter_name = '$parameter_name' and model_name = '$modelname'";
    
    
    
        // Execute the query
        $result = $this->db->query($sql, $values);
    
        // Check for database errors
        if (!$result) {
            // Log the database error
            log_message('error', 'Database error: ' . $this->db->error()['message']);
            $results[] = false; // Store false in the results array indicating failure
        } else {
            $results[] = true; // Store true in the results array indicating success
        }
    
        
    
        return $results; 
    }

    function deleteapi($modelname, $parameter_name, $product_name)
    {
    return $this->db->table('product_api')
        ->where('parameter_name', $parameter_name)
        ->where('model_name', $modelname)
        ->where('product_name', $product_name)
        ->delete();
    }

    function savecommandapi($data) {
    
    
    $table = $this->db->prefixTable('product_api');
    
            $sql = "insert into $table set parameter_name='".$data['cmd_parametername']."',
                                        cmd_dataflow='".$data['cmd_data_flow_direction']."',
                                    cmd_framesize='".$data['cmd_framesizeinbytes']."',
                                         cmd_frametrigger='".$data['cmd_frametrigger']."'";
                       
    
    return $this->db->query($sql);
    
    }

 
    function saveresponseapi($data) {
    
    $table = $this->db->prefixTable('product_api');
    
            $sql = "insert into $table set resp_dataflow='".$data['resp_data_flow_direction']."',
                                    resp_framesize='".$data['resp_framesizeinbytes']."',
                                         resp_frametrigger='".$data['resp_frametrigger']."'";
                       
    return $this->db->query($sql);
    }

    function saveerrorapi($data) {
    
    $table = $this->db->prefixTable('product_api');
    
    $sql = "insert into $table set error_dataflow='".$data['error_data_flow_direction']."',
                                    error_framesize='".$data['error_framesizeinbytes']."',
                                         error_frametrigger='".$data['error_frametrigger']."'";
    
    return $this->db->query($sql);
    
    }

