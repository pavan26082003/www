<?php

namespace App\Models;

use CodeIgniter\Model;

class Viewapis_model extends Model
{
    protected $returnType     = 'object';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = false;
    protected $allowedFields  = array();

    public function __construct()
    {
        parent::__construct();
    }

    private function queryResult($builder)
    {
        if (!$builder) {
            return array();
        }

        $query = $builder->get();
        if (!$query) {
            return array();
        }

        $rows = $query->getResult();
        return is_array($rows) ? $rows : array();
    }

    private function queryRowArray($builder)
    {
        if (!$builder) {
            return null;
        }

        $query = $builder->get();
        if (!$query) {
            return null;
        }

        $row = $query->getRowArray();
        return is_array($row) ? $row : null;
    }

    private function queryRowsArray($builder)
    {
        if (!$builder) {
            return array();
        }

        $query = $builder->get();
        if (!$query) {
            return array();
        }

        $rows = $query->getResultArray();
        return is_array($rows) ? $rows : array();
    }

    public function get_itemapis_for_view($itemid, $user_id = null, $modelName = '')
    {
        $builder = $this->db->table('product_api');
        $this->applyApiViewScope($builder, 'itemid', 'model_name', 'user_id', $itemid, $user_id, $modelName);
        $builder->orderBy('id', 'DESC');

        return $this->queryResult($builder);
    }

    public function getApiTreeConfigList($itemId = 0, $userId = null, $modelName = '')
    {
        $itemId = (int) $itemId;
        $modelName = trim((string) $modelName);

        $builder = $this->db->table('api_product_configs');
        $builder->select('id, user_id, item_id, brand_name, product_name, model_name, config_date, parameter_name, page_number, sequence_no, command_frame_count, response_frame_count, error_frame_count, created_at');
        $this->applyApiViewScope($builder, 'item_id', 'model_name', 'user_id', $itemId, $userId, $modelName);
        $builder->orderBy('id', 'DESC');

        return $this->queryResult($builder);
    }

    public function getApiTreeConfigFull($configId)
    {
        $configId = (int) $configId;
        if ($configId <= 0) {
            return null;
        }

        $config = $this->queryRowArray(
            $this->db->table('api_product_configs')->where('id', $configId)
        );

        if (!$config) {
            return null;
        }

        $frames = $this->queryRowsArray(
            $this->db->table('api_frames')
                ->where('config_id', $configId)
                ->orderBy('frame_group', 'ASC')
                ->orderBy('frame_index', 'ASC')
                ->orderBy('id', 'ASC')
        );

        $frameIds = array();
        foreach ($frames as $frame) {
            $frameIds[] = (int) $frame['id'];
        }

        $fieldsByFrame = array();

        if (!empty($frameIds)) {
            $fields = $this->queryRowsArray(
                $this->db->table('api_frame_fields')
                    ->whereIn('frame_id', $frameIds)
                    ->orderBy('frame_id', 'ASC')
                    ->orderBy('row_sequence', 'ASC')
                    ->orderBy('field_level', 'ASC')
                    ->orderBy('sub_field_sequence', 'ASC')
                    ->orderBy('id', 'ASC')
            );

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

        $config = $this->queryRowArray($configBuilder);

        if (!$config) {
            return false;
        }

        $this->db->transStart();

        $frames = $this->queryRowsArray(
            $this->db->table('api_frames')
                ->select('id')
                ->where('config_id', $configId)
        );

        $frameIds = array();
        foreach ($frames as $frame) {
            $frameIds[] = (int) $frame['id'];
        }

        if (!empty($frameIds)) {
            $this->db->table('api_frame_fields')->whereIn('frame_id', $frameIds)->delete();
        }

        $this->db->table('api_frames')->where('config_id', $configId)->delete();

        $legacyDelete = $this->db->table('product_api')
            ->where('itemid', (int) (isset($config['item_id']) ? $config['item_id'] : 0))
            ->where('parameter_name', isset($config['parameter_name']) ? $config['parameter_name'] : '')
            ->where('model_name', isset($config['model_name']) ? $config['model_name'] : '')
            ->where('product_name', isset($config['product_name']) ? $config['product_name'] : '');

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

        return (bool) $this->db->transStatus();
    }

    public function getModelNameForItem($itemId)
    {
        $itemId = (int) $itemId;
        if ($itemId <= 0) {
            return '';
        }

        $config = $this->queryRowArray(
            $this->db->table('api_product_configs')
                ->select('model_name')
                ->where('item_id', $itemId)
                ->where('model_name !=', '')
                ->orderBy('id', 'DESC')
        );

        if (!empty($config['model_name'])) {
            return trim((string) $config['model_name']);
        }

        $legacy = $this->queryRowArray(
            $this->db->table('product_api')
                ->select('model_name')
                ->where('itemid', $itemId)
                ->where('model_name !=', '')
                ->orderBy('id', 'DESC')
        );

        if (!empty($legacy['model_name'])) {
            return trim((string) $legacy['model_name']);
        }

        $vendor = $this->queryRowArray(
            $this->db->table('vendorproducts')
                ->select('modelno, modelname')
                ->where('id', $itemId)
        );

        if (!empty($vendor)) {
            $vendorModel = trim((string) (isset($vendor['modelno']) ? $vendor['modelno'] : ''));
            if ($vendorModel === '') {
                $vendorModel = trim((string) (isset($vendor['modelname']) ? $vendor['modelname'] : ''));
            }

            if ($vendorModel !== '') {
                return $vendorModel;
            }
        }

        $item = $this->queryRowArray(
            $this->db->table('efactorproduct')
                ->select('model_number, model_name')
                ->where('id', $itemId)
        );

        if (empty($item) || !is_array($item)) {
            return '';
        }

        $modelNumber = trim((string) (isset($item['model_number']) ? $item['model_number'] : ''));
        if ($modelNumber !== '') {
            return $modelNumber;
        }

        return trim((string) (isset($item['model_name']) ? $item['model_name'] : ''));
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

        $vendor = $this->queryRowArray(
            $this->db->table('vendorproducts')->where('id', $itemId)
        );

        if (!empty($vendor) && is_array($vendor)) {
            foreach (array('modelno', 'model_no', 'model_number', 'modelname', 'model_name', 'modal_name') as $key) {
                if (isset($vendor[$key])) {
                    $add($vendor[$key]);
                }
            }
        }

        $add($this->getModelNameForItem($itemId));

        return $names;
    }

    public function itemBelongsToUser($itemId, $userId)
    {
        $itemId = (int) $itemId;
        if ($itemId <= 0 || $userId === null || $userId === '') {
            return false;
        }

        try {
            $fields = $this->db->getFieldNames('vendorproducts');
        } catch (\Throwable $e) {
            return false;
        }

        if (!is_array($fields) || empty($fields)) {
            return false;
        }

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

        $idField = $pickField(array('id', 'vendorproduct_id', 'vendor_product_id'));
        $userField = $pickField(array('user_id', 'userid', 'userId', 'created_by'));

        if (!$idField) {
            return false;
        }

        $builder = $this->db->table('vendorproducts')->where($idField, $itemId);
        if ($userField) {
            $builder->where($userField, $userId);
        }

        $row = $this->queryRowArray($builder);
        return !empty($row);
    }

    /**
     * Restrict API rows to this vendor product and the logged-in profile.
     */
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
                ->orWhere($userColumn . ' IS NULL')
                ->groupEnd();
        }

        $builder->groupEnd();

        if (!empty($modelNames) && $hasUser) {
            $builder->orGroupStart()
                ->where($userColumn, (int) $userId)
                ->whereIn($modelColumn, $modelNames)
                ->groupStart()
                    ->where($itemColumn, 0)
                    ->orWhere($itemColumn . ' IS NULL')
                ->groupEnd()
                ->groupEnd();
        }

        $builder->groupEnd();

        return $builder;
    }
}
