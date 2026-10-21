<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Vendor-only data layer extracted from Items_model.
 * Requested filename: vendor_modal.php
 */
class vendor_modal extends Model
{
    protected $table = 'vendorproducts';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = false;

public function get_cats($user_id) {
    return $this->db->table('productcats')
        ->select('id, name, Product_image')
        ->where('user_id', $user_id)
        ->get();
}





function get_brands($user_id) {
    // Use query binding
    return $this->db->query("SELECT * FROM brands WHERE user_id=?", [$user_id]);
}



function get_vendorproducts($user_id)
{
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


    $idField = $pickField(array(
        'id',
        'vendorproduct_id',
        'vendor_product_id'
    ));

    $userField = $pickField(array(
        'user_id',
        'userid',
        'userId',
        'created_by'
    ));

    $modelNameField = $pickField(array(
        'modelname',
        'model_name',
        'modal_name',
        'name'
    ));

    $modelNoField = $pickField(array(
        'modelno',
        'model_no',
        'model_number',
        'modelname',
        'model_name'
    ));


    $select = array(

        $idField
            ? "`" . $idField . "` AS `id`"
            : "0 AS `id`",


        $selectField(
            array(
                'modelname',
                'model_name',
                'modal_name',
                'name'
            ),
            'model_name'
        ),


        /*
         * BRAND
         *
         * Added more possible DB column names.
         */
        $selectField(
            array(
                'brand',
                'brand_name',
                'brandname',
                'Brand',
                'Brand_name',
                'BrandName'
            ),
            'brand_name',
            ''
        ),


        /*
         * PRODUCT
         */
        $selectField(
            array(
                'category',
                'category_name',
                'product_name',
                'product_type',
                'product',
                'Product_name'
            ),
            'category_name'
        ),

        $selectField(
            array(
                'category',
                'category_name',
                'product_name',
                'product_type',
                'product',
                'Product_name'
            ),
            'product_name'
        ),


        /*
         * MODEL NUMBER
         */
        $selectField(
            array(
                'modelno',
                'model_no',
                'model_number'
            ),
            'model_no'
        ),


        /*
         * VERSION
         */
        $selectField(
            array(
                'model_version',
                'version'
            ),
            'version',
            'N/A'
        ),


        /*
         * BATCH
         */
        $selectField(
            array(
                'Batch',
                'batch'
            ),
            'batch',
            'N/A'
        ),

        // Dedicated model logo, independent from Brand and Product images.
        $selectField(array('model_logo'), 'model_logo')
    );


    $whereParts = array();

    if ($userField) {
        $whereParts[] =
            "`" . $userField . "` = " .
            $this->db->escape($user_id);
    }


    /*
     * Keep EFDEFLT model available.
     */
    $defaultColumns = array_filter(
        array_unique(
            array(
                $modelNameField,
                $modelNoField
            )
        )
    );

    foreach ($defaultColumns as $defaultColumn) {
        $whereParts[] =
            "`" . $defaultColumn . "` = 'EFDEFLT V1.0'";
    }


    $sql =
        "SELECT " .
        implode(", ", $select) .
        " FROM " . $table;


    if (!empty($whereParts)) {
        $sql .=
            " WHERE (" .
            implode(" OR ", $whereParts) .
            ")";
    }


    return $this->db->query($sql);
}
function savevendorproduct($data, $user_id, $id = 0)
{
    $id = (int) $id;
    $user_id = (int) $user_id;

    $modelName = trim((string) ($data['modelname'] ?? ''));
    $modelNo   = trim((string) ($data['modelno'] ?? ''));

    if ($modelName === '' || $modelNo === '') {
        return 'Error: Model name and model number are required.';
    }

    /*
     * Check duplicate model.
     */
    $duplicateBuilder = $this->db
        ->table('vendorproducts')
        ->where('modelname', $modelName)
        ->where('modelno', $modelNo);

    if ($id > 0) {
        $duplicateBuilder->where('id !=', $id);
    }

    $existingProduct = $duplicateBuilder->get()->getRow();

    if ($existingProduct) {
        return "Error: Product with modelname '{$modelName}' and modelno '{$modelNo}' already exists.";
    }

    /*
     * IMPORTANT:
     *
     * HTML / Controller:
     *      version
     *      batch
     *
     * Database:
     *      model_version
     *      Batch
     */
    $vendorData = array(
        'brand'         => trim((string) ($data['brand'] ?? '')),
        'category'      => trim((string) ($data['category'] ?? '')),
        'modelname'     => $modelName,
        'modelno'       => $modelNo,

        // FIXED DATABASE COLUMN NAMES
        'model_version' => trim((string) ($data['version'] ?? '')),
        'Batch'         => trim((string) ($data['batch'] ?? '')),

        'product_type'  => trim((string) ($data['category'] ?? '')),
        'description'   => (string) ($data['description'] ?? ''),
        'user_id'       => $user_id
    );

    // Do not erase the existing logo when another screen updates model fields.
    // Only write it when the controller explicitly supplied a new file name.
    if (array_key_exists('model_logo', $data)) {
        $vendorData['model_logo'] = (string) $data['model_logo'];
    }

    /*
     * Keep only actual database columns.
     */
    $vendorData = $this->defaultApiFilterTableData(
        'vendorproducts',
        $vendorData
    );

    /*
     * EDIT EXISTING PRODUCT
     */
    if ($id > 0) {

        $update = $this->db
            ->table('vendorproducts')
            ->where('id', $id);

        $vendorFields = $this->db->getFieldNames('vendorproducts');

        if (in_array('user_id', $vendorFields, true)) {
            $update->where('user_id', $user_id);
        }

        if (!$update->update($vendorData)) {

            $dbError = $this->db->error();

            log_message(
                'error',
                'Vendor product update failed: ' .
                ($dbError['message'] ?? 'Unknown database error')
            );

            return false;
        }

        return $id;
    }

    /*
     * CREATE NEW PRODUCT
     *
     * Commit the model, default APIs, and Status page before page linking.
     * Linking loads the device-ui schema, which runs CREATE/ALTER and
     * SET SESSION. MySQL treats that DDL as an implicit commit, and a later
     * failed query marks the CodeIgniter transaction failed. Rolling back
     * then leaves the model in the database while the screen shows Save failed.
     */
    $newItemId = 0;
    $this->db->transBegin();

    try {

        if (!$this->db
            ->table('vendorproducts')
            ->insert($vendorData)) {

            $dbError = $this->db->error();

            throw new \RuntimeException(
                'Unable to insert model: ' .
                ($dbError['message'] ?? 'Unknown database error')
            );
        }

        $newItemId = (int) $this->db->insertID();

        if ($newItemId <= 0) {
            $newItemId = $this->findVendorProductId($modelName, $modelNo, $user_id);
        }

        if ($newItemId <= 0) {
            throw new \RuntimeException(
                'Unable to resolve the newly created model ID.'
            );
        }

        /*
         * Create default APIs
         */
        if (!$this->createDefaultApisForModel(
            $newItemId,
            $data,
            $user_id
        )) {
            throw new \RuntimeException(
                'Unable to create default V2 APIs for the new model.'
            );
        }

        $modelNo = $modelNo !== '' ? $modelNo : $modelName;
        $fullPageName = $this->defaultStatusPageFullName();

        if (!$this->ensureDefaultStatusOperationPage(
            $newItemId,
            $user_id,
            $modelNo,
            $fullPageName
        )) {
            throw new \RuntimeException(
                'Unable to create the default Status page for the new model.'
            );
        }

        if ($this->db->transStatus() === false) {
            throw new \RuntimeException(
                'Database transaction failed while creating the model defaults.'
            );
        }

        $this->db->transCommit();

    } catch (\Throwable $e) {

        $this->db->transRollback();

        if ($newItemId > 0 && $this->vendorProductRowExists($newItemId)) {
            log_message(
                'error',
                'savevendorproduct model row remained after rollback: ' .
                $e->getMessage()
            );
        } else {
            log_message(
                'error',
                'savevendorproduct default setup failed: ' .
                $e->getMessage()
            );

            return $e->getMessage();
        }
    }

    try {
        $modelNo = $modelNo !== '' ? $modelNo : $modelName;
        $fullPageName = $this->defaultStatusPageFullName();
        $this->assignDefaultApisToStatusPage(
            $newItemId,
            $user_id,
            $modelNo,
            $fullPageName
        );
        $this->ensureDefaultStatusPageLinking(
            $newItemId,
            $user_id,
            $modelNo,
            $fullPageName
        );
    } catch (\Throwable $e) {
        log_message(
            'error',
            'savevendorproduct page linking failed after the model was saved: ' .
            $e->getMessage()
        );
    }

    return $newItemId;
}
// function savevendorproduct($data, $user_id, $id = 0) {
//     $id = (int) $id;
//     $user_id = (int) $user_id;
//     $table = $this->db->table('vendorproducts');

//     $modelName = trim((string) ($data['modelname'] ?? ''));
//     $modelNo = trim((string) ($data['modelno'] ?? ''));

//     if ($modelName === '' || $modelNo === '') {
//         return 'Error: Model name and model number are required.';
//     }

//     // Keep the existing duplicate rule, but exclude the row being edited.
//     $duplicateBuilder = $this->db->table('vendorproducts')
//         ->where('modelname', $modelName)
//         ->where('modelno', $modelNo);

//     if ($id > 0) {
//         $duplicateBuilder->where('id !=', $id);
//     }

//     $existingProduct = $duplicateBuilder->get()->getRow();
//     if ($existingProduct) {
//         return "Error: Product with modelname '{$modelName}' and modelno '{$modelNo}' already exists.";
//     }

//     $vendorData = array(
//         'brand'        => trim((string) ($data['brand'] ?? '')),
//         'category'     => trim((string) ($data['category'] ?? '')),
//         'modelname'    => $modelName,
//         'modelno'      => $modelNo,
//         'version'      => trim((string) ($data['version'] ?? '')),
//         'batch'        => trim((string) ($data['batch'] ?? '')),
//         'product_type' => trim((string) ($data['category'] ?? '')),
//         'description'  => (string) ($data['description'] ?? ''),
//         'user_id'      => $user_id
//     );

//     // Use only columns that exist on this installation. This keeps the method
//     // compatible with older vendorproducts schemas that may not yet contain
//     // brand/version/batch.
//     $vendorData = $this->defaultApiFilterTableData('vendorproducts', $vendorData);

//     if ($id > 0) {
//         $update = $this->db->table('vendorproducts')->where('id', $id);
//         $vendorFields = $this->db->getFieldNames('vendorproducts');
//         if (in_array('user_id', $vendorFields, true)) {
//             $update->where('user_id', $user_id);
//         }

//         return $update->update($vendorData) ? $id : false;
//     }

//     $this->db->transBegin();

//     try {
//         if (!$this->db->table('vendorproducts')->insert($vendorData)) {
//             $dbError = $this->db->error();
//             throw new \RuntimeException(
//                 'Unable to insert model: ' . ($dbError['message'] ?? 'Unknown database error')
//             );
//         }

//         $newItemId = (int) $this->db->insertID();
//         if ($newItemId <= 0) {
//             throw new \RuntimeException('Unable to resolve the newly created model ID.');
//         }

//         if (!$this->createDefaultApisForModel($newItemId, $data, $user_id)) {
//             throw new \RuntimeException('Unable to create default V2 APIs for the new model.');
//         }

//         if (!$this->createDefaultStatusPageAndLinking($newItemId, $data, $user_id)) {
//             throw new \RuntimeException('Unable to create the default Status page and API linking for the new model.');
//         }

//         if ($this->db->transStatus() === false) {
//             throw new \RuntimeException('Database transaction failed while creating the model defaults.');
//         }

//         $this->db->transCommit();
//         return $newItemId;
//     } catch (\Throwable $e) {
//         $this->db->transRollback();
//             log_message('error', 'savevendorproduct default setup failed: ' . $e->getMessage());
//         return false;
//     }
// }

/**
 * Create the four V2 APIs that every newly-created model must start with.
 *
 * Defaults requested by the UI workflow:
 *   1. Send Data          -> Command A2EA0A, Data/Text
 *   2. Display Log        -> Command A3EA0A, Data/Button
 *   3. Command Get status -> fixed Command A1E70A + editable Response DEE70A
 *   4. Display            -> Command A4EA0A, Command ID/Console
 *
 * Editing policy is enforced in Items::_make_api_tree_row():
 * Command Get status is editable; Send Data, Display Log and Display
 * are shown as read-only defaults.
 */
public function createDefaultApisForModel($itemId, $modelData, $userId) {
    $itemId = (int) $itemId;
    $userId = (int) $userId;

    if ($itemId <= 0 || $userId <= 0) {
        return false;
    }

    foreach (array('api_product_configs', 'api_frames', 'api_frame_fields') as $requiredTable) {
        if (!$this->db->tableExists($requiredTable)) {
            log_message('error', 'Default API table is missing: ' . $requiredTable);
            return false;
        }
    }

    $brand = trim((string) ($modelData['brand'] ?? ''));
    $product = trim((string) ($modelData['category'] ?? ''));
    $modelNo = trim((string) ($modelData['modelno'] ?? ''));

    if ($modelNo === '') {
        $modelNo = trim((string) ($modelData['modelname'] ?? ''));
    }

    $commandFieldData = array(
        'partition_name'       => 'data',
        'field_name'           => 'Data',
        'data_type'            => 'unsigned integer',
        'value_definition'     => 'fixed value',
        'ui_type'              => 'button',
        'notification_enable'  => 'Yes',
        'notification_message' => '',
        'ui_in_out'            => 'read/write',
        'message_count'        => '',
        'ui_values'            => ''
    );

    $apiDefinitions = array(
        array(
            'name' => 'Send Data',
            'command' => array(
                'frame_code'      => 'A2EA0A',
                'direction_label' => 'App/Server to Device',
                'direction_value' => 'server_to_device',
                'multiple_ui'     => 'false',
                'voice_command'   => 'No',
                'locked_value'    => true,
                'partitions'      => array(
                    array(
                        'start'                => 0,
                        'end'                  => 3,
                        'partition_name'       => 'data',
                        'field_name'           => 'Data',
                        'data_type'            => 'string',
                        'value_definition'     => 'range value',
                        'ui_type'              => 'text',
                        'notification_enable'  => 'Yes',
                        'notification_message' => '',
                        'ui_in_out'            => 'read/write',
                        'message_count'        => '1',
                        'ui_values'            => ''
                    )
                )
            )
        ),
        array(
            'name' => 'Display Log',
            'command' => array(
                'frame_code'      => 'A3EA0A',
                'direction_label' => 'App/Server to Device',
                'direction_value' => 'server_to_device',
                'multiple_ui'     => 'false',
                'voice_command'   => 'No',
                'locked_value'    => true,
                'partitions'      => array(
                    array(
                        'start'                => 0,
                        'end'                  => 3,
                        'partition_name'       => 'data',
                        'field_name'           => 'Data',
                        'data_type'            => 'unsigned integer',
                        'value_definition'     => 'fixed value',
                        'ui_type'              => 'button',
                        'notification_enable'  => 'Yes',
                        'notification_message' => '',
                        'ui_in_out'            => 'read/write',
                        'message_count'        => '',
                        'ui_values'            => ''
                    )
                )
            )
        ),
        array(
            'name' => 'Command Get status',
            'command' => array(
                'frame_code'      => 'A1E70A',
                'direction_label' => 'App/Server to Device',
                'direction_value' => 'server_to_device',
                'multiple_ui'     => 'true',
                'voice_command'   => 'No',
                // Only the command VALUE is locked. The API itself remains editable.
                'locked_value'    => true,
                'partitions'      => array(
                    array_merge(array('start' => 0, 'end' => 3), $commandFieldData)
                )
            ),
            'response' => array(
                'frame_code'      => 'DEE70A',
                'direction_label' => 'Device to Server',
                'direction_value' => 'device_to_server',
                'multiple_ui'     => 'true',
                'voice_command'   => 'No',
                'locked_value'    => false,
                'fixed_wrapper'   => true,
                'partitions'      => array(
                    array(
                        'start'                => 0,
                        'end'                  => 1,
                        'partition_name'       => 'commandId',
                        'field_name'           => 'Command ID',
                        'data_type'            => 'unsigned integer',
                        'value_definition'     => 'fixed value',
                        'ui_type'              => 'none',
                        'notification_enable'  => 'Yes',
                        'notification_message' => '',
                        'ui_in_out'            => 'read',
                        'message_count'        => '',
                        'ui_values'            => ''
                    ),
                    array(
                        'start'                => 1,
                        'end'                  => 2,
                        'partition_name'       => 'functionCode',
                        'field_name'           => 'Function Code',
                        'data_type'            => 'unsigned integer',
                        'value_definition'     => 'fixed value',
                        'ui_type'              => 'none',
                        'notification_enable'  => 'Yes',
                        'notification_message' => '',
                        'ui_in_out'            => 'read',
                        'message_count'        => '',
                        'ui_values'            => ''
                    ),
                    array(
                        'start'                => 2,
                        'end'                  => 3,
                        'partition_name'       => 'terminatorCrc',
                        'field_name'           => 'Terminator / CRC',
                        'data_type'            => 'unsigned integer',
                        'value_definition'     => 'fixed value',
                        'ui_type'              => 'none',
                        'notification_enable'  => 'Yes',
                        'notification_message' => '',
                        'ui_in_out'            => 'read',
                        'message_count'        => '',
                        'ui_values'            => ''
                    )
                )
            )
        ),
        array(
            'name' => 'Display',
            'command' => array(
                'frame_code'      => 'A4EA0A',
                'direction_label' => 'App/Server to Device',
                'direction_value' => 'server_to_device',
                'multiple_ui'     => 'false',
                'voice_command'   => 'No',
                'locked_value'    => true,
                'partitions'      => array(
                    array(
                        'start'                => 0,
                        'end'                  => 3,
                        'partition_name'       => 'commandId',
                        'field_name'           => 'Command ID',
                        'data_type'            => 'string',
                        'value_definition'     => 'range value',
                        'ui_type'              => 'console',
                        'notification_enable'  => 'Yes',
                        'notification_message' => '',
                        'ui_in_out'            => 'read',
                        'message_count'        => '',
                        'ui_values'            => ''
                    )
                )
            )
        )
    );

    foreach ($apiDefinitions as $sequence => $definition) {
        $apiName = $definition['name'];

        $alreadyExists = $this->db->table('api_product_configs')
            ->where('user_id', $userId)
            ->where('item_id', $itemId)
            ->where('parameter_name', $apiName)
            ->countAllResults() > 0;

        if ($alreadyExists) {
            continue;
        }

        $payload = $this->buildDefaultApiPayload(
            $itemId,
            $brand,
            $product,
            $modelNo,
            $definition,
            $sequence + 1
        );

        $responseCount = !empty($definition['response']) ? '1' : '0';

        $configData = array(
            'user_id'              => $userId,
            'item_id'              => $itemId,
            'brand_name'           => $brand,
            'product_name'         => $product,
            'model_name'           => $modelNo,
            'config_date'          => date('Y-m-d'),
            'parameter_name'       => $apiName,
            'page_number'          => '1',
            'sequence_no'          => (string) ($sequence + 1),
            'command_frame_count'  => '1',
            'response_frame_count' => $responseCount,
            'error_frame_count'    => '0',
            'description'          => '',
            'raw_payload_json'     => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        if (!$this->defaultApiInsertRow('api_product_configs', $configData)) {
            return false;
        }

        $configId = (int) $this->db->insertID();
        if ($configId <= 0) {
            return false;
        }

        if (!$this->insertDefaultApiFrameTree($configId, 'command', $apiName, $definition['command'])) {
            return false;
        }

        if (!empty($definition['response'])) {
            if (!$this->insertDefaultApiFrameTree($configId, 'response', $apiName, $definition['response'])) {
                return false;
            }
        }
    }

    return true;
}

private function defaultStatusPageFullName()
{
    return 'Status.operation';
}

private function defaultApiParameterNames()
{
    return array(
        'Send Data',
        'Display Log',
        'Command Get status',
        'Display',
    );
}

/**
 * New models only: create Status.operation and link the four default APIs to it.
 */
private function createDefaultStatusPageAndLinking($itemId, $modelData, $userId)
{
    $itemId = (int) $itemId;
    $userId = (int) $userId;
    $modelNo = trim((string) ($modelData['modelno'] ?? ''));
    if ($modelNo === '') {
        $modelNo = trim((string) ($modelData['modelname'] ?? ''));
    }
    $fullPageName = $this->defaultStatusPageFullName();

    if ($itemId <= 0 || $userId <= 0 || $modelNo === '') {
        return false;
    }

    if (!$this->ensureDefaultStatusOperationPage($itemId, $userId, $modelNo, $fullPageName)) {
        return false;
    }

    $this->assignDefaultApisToStatusPage($itemId, $userId, $modelNo, $fullPageName);

    return $this->ensureDefaultStatusPageLinking($itemId, $userId, $modelNo, $fullPageName);
}

private function ensureDefaultStatusOperationPage($itemId, $userId, $modelNo, $fullPageName)
{
    $table = 'vendorproduct_pages';
    if (!$this->db->tableExists($table)) {
        log_message('error', 'Default Status page table is missing: ' . $table);
        return false;
    }

    $existing = $this->db->table($table)
        ->where('vendorproduct_id', $itemId)
        ->groupStart()
            ->where('full_page_name', $fullPageName)
            ->orWhere('page_name', 'Status')
        ->groupEnd()
        ->get()
        ->getRowArray();

    if (!empty($existing)) {
        return true;
    }

    $dotPosition = strrpos($fullPageName, '.');
    $pageName = $dotPosition !== false ? substr($fullPageName, 0, $dotPosition) : 'Status';
    $pageExtension = $dotPosition !== false ? substr($fullPageName, $dotPosition + 1) : 'operation';

    $row = $this->defaultApiFilterTableData($table, array(
        'vendorproduct_id' => $itemId,
        'user_id'          => (string) $userId,
        'modelno'          => $modelNo,
        'page_name'        => $pageName,
        'page_extension'   => $pageExtension,
        'full_page_name'   => $fullPageName,
        'sequence_no'      => 1,
    ));

    if (empty($row)) {
        log_message('error', 'No compatible columns found for vendorproduct_pages.');
        return false;
    }

    if (!$this->db->table($table)->insert($row)) {
        $dbError = $this->db->error();
        log_message('error', 'Default Status page insert failed: ' . ($dbError['message'] ?? 'Unknown database error'));
        return false;
    }

    return true;
}

private function assignDefaultApisToStatusPage($itemId, $userId, $modelNo, $fullPageName)
{
    $parameterNames = $this->defaultApiParameterNames();
    if ($this->db->tableExists('api_product_configs')) {
        $configUpdate = $this->defaultApiFilterTableData('api_product_configs', array(
            'page_number' => '1',
        ));
        if (!empty($configUpdate)) {
            $this->db->table('api_product_configs')
                ->where('item_id', $itemId)
                ->where('user_id', $userId)
                ->whereIn('parameter_name', $parameterNames)
                ->update($configUpdate);
        }

        $configRows = $this->db->table('api_product_configs')
            ->select('id, raw_payload_json, sequence_no')
            ->where('item_id', $itemId)
            ->where('user_id', $userId)
            ->whereIn('parameter_name', $parameterNames)
            ->get()
            ->getResultArray();

        foreach ($configRows as $configRow) {
            $payload = json_decode((string) ($configRow['raw_payload_json'] ?? ''), true);
            if (!is_array($payload)) {
                continue;
            }
            if (!isset($payload['product']) || !is_array($payload['product'])) {
                $payload['product'] = array();
            }
            $payload['product']['page_number'] = '1';
            $payload['product']['page_names'] = $fullPageName;
            if (!isset($payload['product']['sequence_number']) || $payload['product']['sequence_number'] === '') {
                $payload['product']['sequence_number'] = (string) ($configRow['sequence_no'] ?? '1');
            }
            $this->db->table('api_product_configs')
                ->where('id', (int) $configRow['id'])
                ->update(array(
                    'raw_payload_json' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ));
        }
    }

    if ($this->db->tableExists('product_api')) {
        $productUpdate = $this->defaultApiFilterTableData('product_api', array(
            'page_names' => $fullPageName,
            'page_no'    => '1',
        ));
        if (!empty($productUpdate)) {
            $productBuilder = $this->db->table('product_api')
                ->where('model_name', $modelNo)
                ->whereIn('parameter_name', $parameterNames);
            $productFields = $this->db->getFieldNames('product_api');
            if (in_array('itemid', $productFields, true)) {
                $productBuilder->where('itemid', $itemId);
            }
            $productBuilder->update($productUpdate);
        }
    }
}

private function ensureDefaultStatusPageLinking($itemId, $userId, $modelNo, $fullPageName)
{
    if ($this->db->tableExists('api_page_linking_data')) {
        $existing = $this->db->table('api_page_linking_data')
            ->where('model_name', $modelNo)
            ->get()
            ->getRowArray();
        if (!empty($existing)) {
            return true;
        }
    }

    $parameters = $this->defaultApiParameterNames();
    $pageNo = '1';
    $pageNameMap = array($pageNo => $fullPageName);
    $linkingData = array();
    $sequence = 1;
    foreach ($parameters as $parameter) {
        $linkingData[$pageNo][] = array(
            'parameter' => $parameter,
            'sequence'  => $sequence,
            'page_no'   => $pageNo,
            'page_name' => $fullPageName,
        );
        $sequence++;
    }
    $uiRows = array(
        array(
            'page_no'     => $pageNo,
            'page_name'   => $fullPageName,
            'parameters'  => $parameters,
            'sequence'    => 1,
        ),
    );

    $viewPageModel = $this->loadViewPageModelForDefaults();
    if (!$viewPageModel || !method_exists($viewPageModel, 'savePageParameterJsonMapping')) {
        log_message('error', 'View_page_model is unavailable for default Status page linking.');
        return false;
    }

    return (bool) $viewPageModel->savePageParameterJsonMapping(
        $modelNo,
        $pageNameMap,
        $linkingData,
        $uiRows,
        true
    );
}

private function loadViewPageModelForDefaults()
{
    try {
        if (!class_exists('\\App\\Models\\View_page_model', false)) {
            foreach (array('View_page_model.php', 'view_page_model.php') as $fileName) {
                $path = APPPATH . 'Models/' . $fileName;
                if (is_file($path)) {
                    require_once $path;
                    break;
                }
            }
        }

        if (!class_exists('\\App\\Models\\View_page_model', false)) {
            return null;
        }

        return new \App\Models\View_page_model();
    } catch (\Throwable $exception) {
        log_message('error', 'Unable to load View_page_model for default Status linking: ' . $exception->getMessage());
        return null;
    }
}

/** Build one default API payload using the same shape as modal_api.php. */
private function buildDefaultApiPayload($itemId, $brand, $product, $modelNo, array $definition, $sequence) {
    $apiName = (string) $definition['name'];

    $commandFrame = $this->buildDefaultApiFramePayload('command', $apiName, $definition['command']);
    $responseFrames = array();
    if (!empty($definition['response'])) {
        $responseFrames[] = $this->buildDefaultApiFramePayload('response', $apiName, $definition['response']);
    }

    return array(
        'storageVersion' => 3,
        'savedAt'        => date('c'),
        'product'        => array(
            'itemid'               => (string) $itemId,
            'brandname'            => $brand,
            'productname'          => $product,
            'modelname'            => $modelNo,
            'date'                 => date('Y-m-d'),
            'parameter_name'       => $apiName,
            'page_number'          => '1',
            'sequence_number'      => (string) $sequence,
            'command_frame_count'  => '1',
            'response_frame_count' => !empty($responseFrames) ? '1' : '0',
            'error_frame_count'    => '0',
            'page_names'           => $this->defaultStatusPageFullName(),
            'product_description'  => ''
        ),
        'commands'  => array($commandFrame),
        'responses' => $responseFrames,
        'errors'    => array()
    );
}

private function buildDefaultApiFramePayload($frameGroup, $apiName, array $frameDefinition) {
    $frameCode = strtoupper((string) ($frameDefinition['frame_code'] ?? ''));
    $byteCount = max(1, (int) (strlen($frameCode) / 2));
    $partitions = isset($frameDefinition['partitions']) && is_array($frameDefinition['partitions'])
        ? $frameDefinition['partitions']
        : array();

    $cuts = array();
    $labels = array();
    $viewModes = array();
    $configs = array();
    $rows = array();

    foreach ($partitions as $partitionIndex => $partitionDefinition) {
        $start = max(0, (int) ($partitionDefinition['start'] ?? 0));
        $end = max($start + 1, (int) ($partitionDefinition['end'] ?? ($start + 1)));
        $end = min($end, $byteCount);
        $key = $start . '-' . $end;

        if ($start > 0 && !in_array($start, $cuts, true)) {
            $cuts[] = $start;
        }
        if ($end > 0 && $end < $byteCount && !in_array($end, $cuts, true)) {
            $cuts[] = $end;
        }

        $labels[$key] = (string) ($partitionDefinition['partition_name'] ?? '');
        $viewModes[$key] = 'bytewise';
        $fieldConfiguration = $this->buildDefaultFieldConfiguration($partitionDefinition, $start);
        $configs[$key] = array('byte' => $fieldConfiguration);
        $rows[] = $this->buildDefaultApiRow($partitionDefinition, $start, $end);
    }

    sort($cuts, SORT_NUMERIC);

    $partition = array(
        'fullApiValue'                 => $frameCode,
        'partitionCuts'                => $cuts,
        'partitionLabels'              => $labels,
        'partitionViewModes'           => $viewModes,
        'partitionFieldConfigurations' => $configs
    );

    $frameTypeLabel = $frameGroup === 'response' ? 'Response' : 'Command';
    $directionLabel = (string) ($frameDefinition['direction_label'] ?? ($frameGroup === 'response' ? 'Device to Server' : 'App/Server to Device'));
    $directionValue = (string) ($frameDefinition['direction_value'] ?? ($frameGroup === 'response' ? 'device_to_server' : 'server_to_device'));
    $multipleUi = (string) ($frameDefinition['multiple_ui'] ?? 'false');
    $voiceCommand = (string) ($frameDefinition['voice_command'] ?? 'No');

    $meta = array(
        'parametername'     => $apiName,
        'noofrows'          => (string) count($rows),
        'remarks'           => '',
        'dataflowdirection' => $directionLabel,
        'frametype'         => $frameTypeLabel,
        'framesize_bytes'   => (string) $byteCount,
        'frametrigger'      => 'None',
        'multipleui'        => $multipleUi,
        'triggerui'         => 'false',
        'voicecommand'      => $voiceCommand,
        'voicecontrol'      => ''
    );

    return array(
        'frameNumber'       => 1,
        'frameType'         => $frameTypeLabel,
        'parameterName'     => $apiName,
        'direction'         => $directionLabel,
        'dataFlowDirection' => $directionValue,
        'size'              => (string) $byteCount,
        'triggerOptions'    => 'None',
        'multipleui'        => $multipleUi,
        'triggerui'         => 'false',
        'voicecommand'      => $voiceCommand,
        'voice_command'     => $voiceCommand,
        'voicecontrol'      => '',
        'voice_control'     => '',
        'voiceCommands'     => array(),
        'voice_commands'    => array(),
        'frameCode'         => $frameCode,
        ($frameGroup === 'response' ? 'responseCode' : 'commandCode') => $frameCode,
        'valueGroups'       => array(),
        'partition'         => $partition,
        'fieldDescriptions' => array(),
        'byteDescriptions'  => array(),
        'fieldViewModes'    => array(),
        'rows'              => $rows,
        'meta'              => $meta,
        'defaultValueLocked'=> !empty($frameDefinition['locked_value'])
    );
}

private function buildDefaultFieldConfiguration(array $definition, $byteIndex) {
    $fieldName = (string) ($definition['field_name'] ?? '');
    $messageCount = (string) ($definition['message_count'] ?? '');
    $uiValues = (string) ($definition['ui_values'] ?? '');

    return array(
        'name'                => $fieldName,
        'fieldName'           => $fieldName,
        'field_name'          => $fieldName,
        'dataType'            => (string) ($definition['data_type'] ?? ''),
        'valueDef'            => (string) ($definition['value_definition'] ?? ''),
        'defaultvalue'        => $fieldName,
        'defaultValue'        => $fieldName,
        'default_value'       => $fieldName,
        'uiType'              => (string) ($definition['ui_type'] ?? 'none'),
        'asperui'             => $messageCount,
        'sno_asperui'         => $messageCount !== '' ? '1' : '',
        'asperuivalue'        => $uiValues,
        'asperUi'             => $messageCount,
        'uiSerialNo'          => $messageCount !== '' ? '1' : '',
        'uiValues'            => $uiValues,
        'message'             => '',
        'notificationMessage' => (string) ($definition['notification_message'] ?? ''),
        'notificationEnable'  => (string) ($definition['notification_enable'] ?? 'Yes'),
        'uiInOut'             => (string) ($definition['ui_in_out'] ?? 'read'),
        'sliderStep'          => '',
        'sliderStepValue'     => '',
        'scalling'            => '',
        'displayValueMin'     => '',
        'displayValueMax'     => '',
        'developerNotes'      => '',
        'bitPosition'         => null,
        'bitPositions'        => null,
        'byteIndex'           => (int) $byteIndex
    );
}

private function buildDefaultApiRow(array $definition, $start, $end) {
    $numberOfBytes = max(1, ((int) $end - (int) $start));
    $fieldName = (string) ($definition['field_name'] ?? '');
    $messageCount = (string) ($definition['message_count'] ?? '');
    $uiValues = (string) ($definition['ui_values'] ?? '');

    return array(
        'bitwise'             => null,
        'startBytePosition'   => (string) $start,
        'endBytePosition'     => (string) ($end - 1),
        'numberOfBytes'       => (string) $numberOfBytes,
        'bitSize'             => (string) ($numberOfBytes * 8),
        'name'                => $fieldName,
        'fieldName'           => $fieldName,
        'fieldDescription'    => (string) ($definition['partition_name'] ?? ''),
        'dataType'            => (string) ($definition['data_type'] ?? ''),
        'valueDefinition'     => (string) ($definition['value_definition'] ?? ''),
        'displayValueMin'     => '',
        'displayValueMax'     => '',
        'valueInHexMin'       => '0x' . str_repeat('00', $numberOfBytes),
        'valueInHexMax'       => '0x' . str_repeat('FF', $numberOfBytes),
        'uiType'              => (string) ($definition['ui_type'] ?? 'none'),
        'uiId'                => '1',
        'asperui'             => $messageCount,
        'sno_asperui'         => $messageCount !== '' ? '1' : '',
        'asperuivalue'        => $uiValues,
        'uiPageNo'            => '',
        'uiDescriptionInOut'  => (string) ($definition['ui_in_out'] ?? 'read'),
        'sliderStepValue'     => '',
        'defaultvalue'        => $fieldName,
        'developerNotes'      => '',
        'notificationEnable'  => (string) ($definition['notification_enable'] ?? 'Yes'),
        'notificationMessage' => (string) ($definition['notification_message'] ?? ''),
        'scaling'             => '',
        'bitwiseMode'         => 'Bytewise'
    );
}

/** Insert one normalized frame and all of its bytewise partition fields. */
private function insertDefaultApiFrameTree($configId, $frameGroup, $apiName, array $frameDefinition) {
    $frameCode = strtoupper((string) ($frameDefinition['frame_code'] ?? ''));
    $byteCount = max(1, (int) (strlen($frameCode) / 2));
    $partitions = isset($frameDefinition['partitions']) && is_array($frameDefinition['partitions'])
        ? $frameDefinition['partitions']
        : array();
    $frameTypeLabel = $frameGroup === 'response' ? 'Response' : 'Command';
    $directionLabel = (string) ($frameDefinition['direction_label'] ?? ($frameGroup === 'response' ? 'Device to Server' : 'App/Server to Device'));
    $multipleUi = (string) ($frameDefinition['multiple_ui'] ?? 'false');
    $voiceCommand = (string) ($frameDefinition['voice_command'] ?? 'No');

    $frameMeta = array(
        'parametername'     => $apiName,
        'noofrows'          => (string) count($partitions),
        'remarks'           => '',
        'dataflowdirection' => $directionLabel,
        'frametype'         => $frameTypeLabel,
        'framesize_bytes'   => (string) $byteCount,
        'frametrigger'      => 'None',
        'multipleui'        => $multipleUi,
        'triggerui'         => 'false',
        'voicecommand'      => $voiceCommand,
        'voicecontrol'      => ''
    );

    $frameData = array(
        'config_id'           => (int) $configId,
        'frame_group'         => $frameGroup,
        'frame_index'         => 0,
        'parameter_name'      => $apiName,
        'no_of_rows'          => (string) count($partitions),
        'remarks'             => '',
        'data_flow_direction' => $directionLabel,
        'frame_type'          => $frameTypeLabel,
        'frame_size_bytes'    => (string) $byteCount,
        'frame_trigger'       => 'None',
        'multiple_ui'         => $multipleUi,
        'trigger_ui'          => 'false',
        'voice_command'       => $voiceCommand,
        'voice_control'       => '',
        'raw_meta_json'       => json_encode($frameMeta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    );

    if (!$this->defaultApiInsertRow('api_frames', $frameData)) {
        return false;
    }

    $frameId = (int) $this->db->insertID();
    if ($frameId <= 0) {
        return false;
    }

    foreach ($partitions as $rowSequence => $definition) {
        $start = max(0, (int) ($definition['start'] ?? 0));
        $end = max($start + 1, (int) ($definition['end'] ?? ($start + 1)));
        $end = min($end, $byteCount);
        $numberOfBytes = max(1, $end - $start);
        $fieldName = (string) ($definition['field_name'] ?? '');
        $partitionName = (string) ($definition['partition_name'] ?? '');
        $messageCount = (string) ($definition['message_count'] ?? '');
        $uiValues = (string) ($definition['ui_values'] ?? '');

        $rawField = array(
            'name'                => $fieldName,
            'fieldName'           => $fieldName,
            'fieldDescription'    => $partitionName,
            'dataType'            => (string) ($definition['data_type'] ?? ''),
            'valueDefinition'     => (string) ($definition['value_definition'] ?? ''),
            'uiType'              => (string) ($definition['ui_type'] ?? 'none'),
            'notificationEnable'  => (string) ($definition['notification_enable'] ?? 'Yes'),
            'notificationMessage' => (string) ($definition['notification_message'] ?? ''),
            'uiDescriptionInOut'  => (string) ($definition['ui_in_out'] ?? 'read'),
            'asperui'             => $messageCount,
            'sno_asperui'         => $messageCount !== '' ? '1' : '',
            'asperuivalue'        => $uiValues
        );

        $fieldData = array(
            'frame_id'              => $frameId,
            'parent_field_id'       => null,
            'field_level'           => 'bytewise',
            'row_sequence'          => $rowSequence + 1,
            'sub_field_sequence'    => 0,
            'start_byte_position'   => $start,
            'byte_start_position'   => $start,
            'end_byte_position'     => $end - 1,
            'number_of_bytes'       => $numberOfBytes,
            'no_of_bytes'           => $numberOfBytes,
            'bit_size'              => $numberOfBytes * 8,
            'field_name'            => $fieldName,
            'default_name'          => $fieldName,
            'field_description'     => $partitionName,
            'data_type'             => (string) ($definition['data_type'] ?? ''),
            'value_definition'      => (string) ($definition['value_definition'] ?? ''),
            'type'                  => (string) ($definition['value_definition'] ?? ''),
            'default_value'         => $fieldName,
            'value'                 => $fieldName,
            'display_value_min'     => null,
            'display_value_max'     => null,
            'value_in_hex_min'      => '0x' . str_repeat('00', $numberOfBytes),
            'value_in_hex_max'      => '0x' . str_repeat('FF', $numberOfBytes),
            'ui_type'               => (string) ($definition['ui_type'] ?? 'none'),
            'uiType'                => (string) ($definition['ui_type'] ?? 'none'),
            'ui_id'                 => '1',
            'ui_ids'                => '1',
            'ui_description_in_out' => (string) ($definition['ui_in_out'] ?? 'read'),
            'ui_description'        => (string) ($definition['ui_in_out'] ?? 'read'),
            'as_per_ui'             => $messageCount,
            'asperui'               => $messageCount,
            'sno_as_per_ui'         => $messageCount !== '' ? '1' : '',
            'sno_asperui'           => $messageCount !== '' ? '1' : '',
            'ui_values'             => $uiValues,
            'asperui_value'         => $uiValues,
            'asperui_values'        => $uiValues,
            'slider_step_value'     => null,
            'developer_notes'       => '',
            'notification_enable'   => (string) ($definition['notification_enable'] ?? 'Yes'),
            'notification_message'  => (string) ($definition['notification_message'] ?? ''),
            'scaling'               => null,
            'raw_field_json'        => json_encode($rawField, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        if (!$this->defaultApiInsertRow('api_frame_fields', $fieldData)) {
            return false;
        }
    }

    return true;
}

private function defaultApiInsertRow($tableName, array $data) {
    $filtered = $this->defaultApiFilterTableData($tableName, $data);

    if (empty($filtered)) {
        log_message('error', 'No compatible columns found for default API table: ' . $tableName);
        return false;
    }

    $inserted = $this->db->table($tableName)->insert($filtered);
    if (!$inserted) {
        $dbError = $this->db->error();
        log_message(
            'error',
            'Default API insert failed for ' . $tableName . ': ' . ($dbError['message'] ?? 'Unknown database error')
        );
        return false;
    }

    return true;
}

private function defaultApiFilterTableData($tableName, array $data) {
    $fields = $this->db->getFieldNames($tableName);
    $allowed = array_fill_keys($fields, true);
    return array_intersect_key($data, $allowed);
}




    private function findVendorProductId($modelName, $modelNo, $userId)
    {
        $builder = $this->db->table('vendorproducts')
            ->select('id')
            ->where('modelname', $modelName)
            ->where('modelno', $modelNo)
            ->orderBy('id', 'DESC');

        $fields = $this->db->getFieldNames('vendorproducts');
        if (in_array('user_id', $fields, true)) {
            $builder->where('user_id', (int) $userId);
        }

        $row = $builder->get()->getRowArray();

        return (int) ($row['id'] ?? 0);
    }

    private function vendorProductRowExists($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return false;
        }

        return $this->db->table('vendorproducts')
            ->where('id', $id)
            ->countAllResults() > 0;
    }

    function deletevendorproduct($id)

     {
         $table = $this->db->prefixTable('vendorproducts');

        $sql = "delete from $table where id='".$id."'";

         return $this->db->query($sql);
     }


/**
 * Legacy product_api rows for a modal.
 * Match itemid as well as model name. Older APIs stay on itemid after the
 * vendor model is renamed, so a model_name-only lookup copies V2 rows only.
 * Skip rows whose model_name belongs to a different vendor product (leftover
 * copies that still point at the source itemid).
 */
protected function legacyProductApiRows($sourceId, array $aliases)
{
    if (!$this->db->tableExists('product_api')) {
        return array();
    }

    $sourceId = (int) $sourceId;
    $fields = $this->db->getFieldNames('product_api');
    $hasItem = in_array('itemid', $fields, true);
    $hasModel = in_array('model_name', $fields, true);
    $foreignNames = $this->otherVendorProductModelNames($sourceId);

    $aliasLookup = array();
    $queryAliases = array();
    foreach ($aliases as $alias) {
        $alias = trim((string) $alias);
        if ($alias === '') {
            continue;
        }
        $key = strtolower($alias);
        $aliasLookup[$key] = true;
        $queryAliases[$key] = $alias;
    }

    if ($sourceId > 0 && $hasItem && $hasModel) {
        foreach ($this->db->table('product_api')
            ->select('model_name')
            ->where('itemid', $sourceId)
            ->where('model_name !=', '')
            ->groupBy('model_name')
            ->get()
            ->getResultArray() as $row) {
            $name = trim((string) (isset($row['model_name']) ? $row['model_name'] : ''));
            $key = strtolower($name);
            if ($name === '' || isset($foreignNames[$key])) {
                continue;
            }
            $queryAliases[$key] = $name;
        }
    }

    $queryAliases = array_values($queryAliases);
    if ($sourceId <= 0 && empty($queryAliases)) {
        return array();
    }

    $builder = $this->db->table('product_api');
    $builder->groupStart();
    $hasCondition = false;
    if ($sourceId > 0 && $hasItem) {
        $builder->where('itemid', $sourceId);
        $hasCondition = true;
    }
    if ($hasModel && !empty($queryAliases)) {
        if ($hasCondition) {
            $builder->orWhereIn('model_name', $queryAliases);
        } else {
            $builder->whereIn('model_name', $queryAliases);
            $hasCondition = true;
        }
    }
    $builder->groupEnd();

    if (!$hasCondition) {
        return array();
    }

    $rows = $builder->orderBy('id', 'ASC')->get()->getResultArray();
    $filtered = array();
    foreach ($rows as $row) {
        $modelKey = strtolower(trim((string) (isset($row['model_name']) ? $row['model_name'] : '')));
        if ($modelKey !== '' && isset($foreignNames[$modelKey]) && !isset($aliasLookup[$modelKey])) {
            continue;
        }
        $filtered[] = $row;
    }

    return $filtered;
}

/**
 * True when this name is already used as a vendor model name or model number.
 */
protected function modalNameExists($name)
{
    $name = trim((string) $name);
    if ($name === '' || !$this->db->tableExists('vendorproducts')) {
        return false;
    }

    $wanted = array();
    foreach ($this->db->getFieldNames('vendorproducts') as $field) {
        if (in_array(strtolower($field), array('modelname', 'model_name', 'modal_name', 'modelno', 'model_no', 'model_number'), true)) {
            $wanted[] = $field;
        }
    }
    if (empty($wanted)) {
        return false;
    }

    $builder = $this->db->table('vendorproducts')->groupStart();
    $first = true;
    foreach ($wanted as $field) {
        if ($first) {
            $builder->where($field, $name);
            $first = false;
        } else {
            $builder->orWhere($field, $name);
        }
    }
    $builder->groupEnd();

    return $builder->countAllResults() > 0;
}

/**
 * Model names owned by vendor products other than the source modal.
 */
protected function otherVendorProductModelNames($sourceId)
{
    $names = array();
    $sourceId = (int) $sourceId;
    if (!$this->db->tableExists('vendorproducts')) {
        return $names;
    }

    $wanted = array();
    foreach ($this->db->getFieldNames('vendorproducts') as $field) {
        if (in_array(strtolower($field), array('modelname', 'model_name', 'modal_name', 'modelno', 'model_no', 'model_number'), true)) {
            $wanted[] = $field;
        }
    }
    if (empty($wanted)) {
        return $names;
    }

    foreach ($this->db->table('vendorproducts')->select('id, ' . implode(', ', $wanted))->get()->getResultArray() as $row) {
        if ((int) (isset($row['id']) ? $row['id'] : 0) === $sourceId) {
            continue;
        }
        foreach ($wanted as $field) {
            $value = strtolower(trim((string) (isset($row[$field]) ? $row[$field] : '')));
            if ($value !== '') {
                $names[$value] = true;
            }
        }
    }

    return $names;
}

public function copyModal($sourceId, $newName, $userId)
{
    $sourceId = (int) $sourceId;
    $newName = trim((string) $newName);
    $userId = (int) $userId;

    $source = $this->db->table('vendorproducts')->where('id', $sourceId)->get()->getRowArray();
    if (!$source) {
        return ['success' => false, 'message' => 'Source model not found'];
    }

    if ($newName === '') {
        return ['success' => false, 'message' => 'Enter a new model name'];
    }

    if ($this->modalNameExists($newName)) {
        return ['success' => false, 'message' => 'Model name already exists. Enter a different name.'];
    }

    $oldModelName = trim((string) (
        $source['modelname']
        ?? ($source['model_name'] ?? ($source['modal_name'] ?? ($source['name'] ?? '')))
    ));
    $oldModelNo = trim((string) (
        $source['modelno']
        ?? ($source['model_no'] ?? ($source['model_number'] ?? $oldModelName))
    ));

    $aliases = [];
    foreach ([$oldModelName, $oldModelNo] as $alias) {
        $alias = trim((string) $alias);
        if ($alias !== '') {
            $aliases[strtolower($alias)] = $alias;
        }
    }
    $aliases = array_values($aliases);

    $newData = $source;
    unset($newData['id']);
    if (array_key_exists('modelname', $newData)) {
        $newData['modelname'] = $newName;
    }
    if (array_key_exists('model_name', $newData)) {
        $newData['model_name'] = $newName;
    }
    if (array_key_exists('modal_name', $newData)) {
        $newData['modal_name'] = $newName;
    }
    if (array_key_exists('modelno', $newData)) {
        $newData['modelno'] = $newName;
    }
    if (array_key_exists('model_no', $newData)) {
        $newData['model_no'] = $newName;
    }
    if (array_key_exists('model_number', $newData)) {
        $newData['model_number'] = $newName;
    }
    if (array_key_exists('user_id', $newData)) {
        $newData['user_id'] = $userId;
    }

    $this->db->transStart();
    $this->db->table('vendorproducts')->insert($newData);
    $newId = (int) $this->db->insertID();

    if ($newId <= 0) {
        $this->db->transComplete();
        return ['success' => false, 'message' => 'Failed to create new model'];
    }

    $copiedApis = 0;
    $copiedLegacy = 0;
    $copiedLinks = 0;

    // Copy V2 API configs + full frame/field trees (by item_id and model aliases)
    if ($this->db->tableExists('api_product_configs')) {
        $builder = $this->db->table('api_product_configs')->groupStart()
            ->where('item_id', $sourceId);

        if (!empty($aliases)) {
            $builder->orWhereIn('model_name', $aliases);
        }

        $builder->groupEnd();

        if ($this->db->fieldExists('deleted_at', 'api_product_configs')) {
            $builder->where('deleted_at', null);
        }

        $apis = $builder->get()->getResultArray();
        $seenConfigKeys = [];

        foreach ($apis as $api) {
            $oldConfigId = (int) ($api['id'] ?? 0);
            $paramKey = strtolower(trim((string) ($api['parameter_name'] ?? '')));
            $dedupeKey = $paramKey !== '' ? $paramKey : ('id:' . $oldConfigId);

            if (isset($seenConfigKeys[$dedupeKey])) {
                continue;
            }
            $seenConfigKeys[$dedupeKey] = true;

            unset($api['id'], $api['created_at'], $api['updated_at']);
            $api['item_id'] = $newId;
            if (array_key_exists('user_id', $api)) {
                $api['user_id'] = $userId;
            }
            if (array_key_exists('model_name', $api)) {
                $api['model_name'] = $newName;
            }
            if (array_key_exists('modelname', $api)) {
                $api['modelname'] = $newName;
            }

            if (!empty($api['raw_payload_json'])) {
                $decoded = json_decode($api['raw_payload_json'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    if (!isset($decoded['product']) || !is_array($decoded['product'])) {
                        $decoded['product'] = [];
                    }
                    $decoded['product']['modelname'] = $newName;
                    $decoded['product']['model_name'] = $newName;
                    $decoded['product']['itemid'] = $newId;
                    $api['raw_payload_json'] = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }

            $allowed = $this->db->getFieldNames('api_product_configs');
            $api = array_intersect_key($api, array_flip($allowed));

            if (!$this->db->table('api_product_configs')->insert($api)) {
                continue;
            }

            $newConfigId = (int) $this->db->insertID();
            if ($newConfigId <= 0) {
                continue;
            }

            $copiedApis++;
            $this->copyApiFrameTree($oldConfigId, $newConfigId);
        }
    }

    // Copy legacy product_api rows. Old modals keep APIs on itemid, and
    // model_name often differs from vendorproducts.modelname / modelno.
    $legacyApis = $this->legacyProductApiRows($sourceId, $aliases);
    if (!empty($legacyApis)) {
        $seenLegacy = [];
        $legacyAllowed = $this->db->getFieldNames('product_api');

        foreach ($legacyApis as $api) {
            $paramKey = strtolower(trim((string) ($api['parameter_name'] ?? '')));
            if ($paramKey !== '' && isset($seenLegacy[$paramKey])) {
                continue;
            }
            if ($paramKey !== '') {
                $seenLegacy[$paramKey] = true;
            }

            unset($api['id'], $api['created_at'], $api['updated_at']);
            if (in_array('model_name', $legacyAllowed, true)) {
                $api['model_name'] = $newName;
            }
            if (in_array('itemid', $legacyAllowed, true)) {
                $api['itemid'] = $newId;
            }
            if (array_key_exists('user_id', $api)) {
                $api['user_id'] = (string) $userId;
            }
            $api = array_intersect_key($api, array_flip($legacyAllowed));

            if ($this->db->table('product_api')->insert($api)) {
                $copiedLegacy++;
            } else {
                $dbError = $this->db->error();
                log_message('error', 'copyModal legacy insert failed: ' . ($dbError['message'] ?? 'unknown database error'));
            }
        }
    }

    // Copy page linking rows tied to source model aliases
    if (!empty($aliases) && $this->db->tableExists('api_page_linking_data')) {
        $links = $this->db->table('api_page_linking_data')
            ->whereIn('model_name', $aliases)
            ->get()
            ->getResultArray();
        $linkAllowed = $this->db->getFieldNames('api_page_linking_data');

        foreach ($links as $link) {
            unset($link['id'], $link['created_at'], $link['updated_at']);
            $link['model_name'] = $newName;
            if (array_key_exists('user_id', $link)) {
                $link['user_id'] = $userId;
            }
            if (array_key_exists('item_id', $link)) {
                $link['item_id'] = $newId;
            }
            $link = array_intersect_key($link, array_flip($linkAllowed));
            if ($this->db->table('api_page_linking_data')->insert($link)) {
                $copiedLinks++;
            }
        }
    }

    $this->db->transComplete();

    $ok = (bool) $this->db->transStatus();
    $totalApis = $copiedApis + $copiedLegacy;

    return [
        'success' => $ok,
        'message' => $ok
            ? ('Model copied successfully. APIs copied: ' . $totalApis . ($copiedLinks ? (', page links: ' . $copiedLinks) : ''))
            : 'Model copy failed',
        'id' => $newId,
        'copied_apis' => $totalApis,
        'copied_v2' => $copiedApis,
        'copied_legacy' => $copiedLegacy,
        'copied_links' => $copiedLinks,
    ];
}

/**
 * Deep-copy api_frames + api_frame_fields from one config to another.
 */
protected function copyApiFrameTree($oldConfigId, $newConfigId)
{
    $oldConfigId = (int) $oldConfigId;
    $newConfigId = (int) $newConfigId;

    if ($oldConfigId <= 0 || $newConfigId <= 0 || !$this->db->tableExists('api_frames')) {
        return;
    }

    $frames = $this->db->table('api_frames')
        ->where('config_id', $oldConfigId)
        ->orderBy('id', 'ASC')
        ->get()
        ->getResultArray();

    $frameAllowed = $this->db->getFieldNames('api_frames');
    $fieldAllowed = $this->db->tableExists('api_frame_fields')
        ? $this->db->getFieldNames('api_frame_fields')
        : [];

    foreach ($frames as $frame) {
        $oldFrameId = (int) ($frame['id'] ?? 0);
        unset($frame['id'], $frame['created_at'], $frame['updated_at']);
        $frame['config_id'] = $newConfigId;
        $frame = array_intersect_key($frame, array_flip($frameAllowed));

        if (!$this->db->table('api_frames')->insert($frame)) {
            continue;
        }

        $newFrameId = (int) $this->db->insertID();
        if ($newFrameId <= 0 || empty($fieldAllowed) || $oldFrameId <= 0) {
            continue;
        }

        $fields = $this->db->table('api_frame_fields')
            ->where('frame_id', $oldFrameId)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $idMap = [];
        foreach ($fields as $field) {
            $oldFieldId = (int) ($field['id'] ?? 0);
            $oldParent = isset($field['parent_field_id']) ? (int) $field['parent_field_id'] : 0;
            unset($field['id'], $field['created_at'], $field['updated_at']);
            $field['frame_id'] = $newFrameId;

            if ($oldParent > 0 && isset($idMap[$oldParent])) {
                $field['parent_field_id'] = $idMap[$oldParent];
            } elseif (array_key_exists('parent_field_id', $field)) {
                $field['parent_field_id'] = null;
            }

            $field = array_intersect_key($field, array_flip($fieldAllowed));
            if (!$this->db->table('api_frame_fields')->insert($field)) {
                continue;
            }

            $newFieldId = (int) $this->db->insertID();
            if ($oldFieldId > 0 && $newFieldId > 0) {
                $idMap[$oldFieldId] = $newFieldId;
            }
        }
    }
}

/**
 * Preview payload for Copy Modal page.
 */
public function getCopyModalPreview($id, $model, $userId)
{
    $id = (int) $id;
    $model = trim((string) $model);
    $userId = (int) $userId;

    $result = array(
        'name' => $model,
        'brand' => '',
        'category' => '',
        'brand_image' => '',
        'product_image' => '',
        'api_names' => array(),
    );

    $modal = null;
    if ($id > 0) {
        $modal = $this->db->table('vendorproducts')->where('id', $id)->get()->getRowArray();
    }

    if (!$modal && $model !== '' && $this->db->tableExists('vendorproducts')) {
        $fields = $this->db->getFieldNames('vendorproducts');
        $lookup = array();
        foreach ($fields as $field) {
            $lookup[strtolower($field)] = $field;
        }
        $modelNameField = isset($lookup['modelname']) ? $lookup['modelname'] : (isset($lookup['model_name']) ? $lookup['model_name'] : null);
        $modelNoField = isset($lookup['modelno']) ? $lookup['modelno'] : (isset($lookup['model_no']) ? $lookup['model_no'] : null);

        $builder = $this->db->table('vendorproducts')->groupStart();
        if ($modelNameField) {
            $builder->orWhere($modelNameField, $model);
        }
        if ($modelNoField) {
            $builder->orWhere($modelNoField, $model);
        }
        $builder->groupEnd()->limit(1);
        $modal = $builder->get()->getRowArray();
    }

    if ($modal) {
        $result['name'] = trim((string) (
            isset($modal['modelname']) ? $modal['modelname'] : (isset($modal['model_name']) ? $modal['model_name'] : (isset($modal['name']) ? $modal['name'] : $model))
        ));
        $result['brand'] = trim((string) (
            isset($modal['brand']) ? $modal['brand'] : (isset($modal['brand_name']) ? $modal['brand_name'] : '')
        ));
        $result['category'] = trim((string) (
            isset($modal['category']) ? $modal['category'] : (isset($modal['category_name']) ? $modal['category_name'] : (isset($modal['product_name']) ? $modal['product_name'] : (isset($modal['product_type']) ? $modal['product_type'] : '')))
        ));
        $id = (int) (isset($modal['id']) ? $modal['id'] : $id);
    }

    $normalize = function ($value) {
        return strtolower(trim(preg_replace('/\s+/', ' ', (string) $value)));
    };

    $toUrl = function ($filename) {
        $filename = trim((string) $filename);
        if ($filename === '') {
            return '';
        }
        if (preg_match('/^https?:\/\//i', $filename) || strpos($filename, '/') === 0) {
            return $filename;
        }
        return base_url('files/brand_logo/' . rawurlencode(basename($filename)));
    };

    if ($result['brand'] !== '' && $this->db->tableExists('brands')) {
        $fields = $this->db->getFieldNames('brands');
        $lookup = array();
        foreach ($fields as $field) {
            $lookup[strtolower($field)] = $field;
        }
        $nameField = isset($lookup['name']) ? $lookup['name'] : null;
        $logoField = isset($lookup['brandlogo']) ? $lookup['brandlogo'] : (isset($lookup['brand_logo']) ? $lookup['brand_logo'] : null);
        if ($nameField && $logoField) {
            $brandRow = $this->db->table('brands')
                ->select($logoField)
                ->groupStart()
                ->where($nameField, $result['brand'])
                ->orWhere('LOWER(' . $nameField . ') =', $normalize($result['brand']))
                ->groupEnd()
                ->limit(1)
                ->get()
                ->getRowArray();
            if (!empty($brandRow[$logoField])) {
                $result['brand_image'] = $toUrl($brandRow[$logoField]);
            }
        }
    }

    if ($result['category'] !== '' && $this->db->tableExists('productcats')) {
        $fields = $this->db->getFieldNames('productcats');
        $lookup = array();
        foreach ($fields as $field) {
            $lookup[strtolower($field)] = $field;
        }
        $nameField = isset($lookup['name']) ? $lookup['name'] : null;
        $imageField = isset($lookup['product_image']) ? $lookup['product_image'] : (isset($lookup['prodimg']) ? $lookup['prodimg'] : null);
        if ($nameField && $imageField) {
            $productRow = $this->db->table('productcats')
                ->select($imageField)
                ->groupStart()
                ->where($nameField, $result['category'])
                ->orWhere('LOWER(' . $nameField . ') =', $normalize($result['category']))
                ->groupEnd()
                ->limit(1)
                ->get()
                ->getRowArray();
            if (!empty($productRow[$imageField])) {
                $result['product_image'] = $toUrl($productRow[$imageField]);
            }
        }
    }

    $aliases = array();
    foreach (array($result['name'], $model) as $alias) {
        $alias = trim((string) $alias);
        if ($alias !== '') {
            $aliases[$normalize($alias)] = $alias;
        }
    }
    if ($modal) {
        foreach (array('modelno', 'model_no', 'model_number', 'modelname', 'model_name') as $key) {
            if (!empty($modal[$key])) {
                $aliases[$normalize($modal[$key])] = trim((string) $modal[$key]);
            }
        }
    }
    $aliases = array_values($aliases);

    $apiNames = array();
    $seen = array();

    $addApi = function ($name) use (&$apiNames, &$seen) {
        $name = trim((string) $name);
        if ($name === '') {
            return;
        }
        $key = strtolower($name);
        if (isset($seen[$key])) {
            return;
        }
        $seen[$key] = true;
        $apiNames[] = $name;
    };

    if ($this->db->tableExists('api_product_configs')) {
        $builder = $this->db->table('api_product_configs')->select('parameter_name')->groupStart();
        $hasCondition = false;
        if ($id > 0) {
            $builder->where('item_id', $id);
            $hasCondition = true;
        }
        if (!empty($aliases)) {
            if ($hasCondition) {
                $builder->orWhereIn('model_name', $aliases);
            } else {
                $builder->whereIn('model_name', $aliases);
                $hasCondition = true;
            }
        }
        $builder->groupEnd();

        if ($hasCondition) {
            if ($this->db->fieldExists('deleted_at', 'api_product_configs')) {
                $builder->where('deleted_at', null);
            }
            foreach ($builder->orderBy('parameter_name', 'ASC')->get()->getResultArray() as $row) {
                $addApi(isset($row['parameter_name']) ? $row['parameter_name'] : '');
            }
        }
    }

    foreach ($this->legacyProductApiRows($id, $aliases) as $row) {
        $addApi(isset($row['parameter_name']) ? $row['parameter_name'] : '');
    }

    $result['api_names'] = $apiNames;
    return $result;
}

}
