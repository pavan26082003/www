<?php

namespace App\Models;

use CodeIgniter\Model;

class Modal_pages_model extends Model
{
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = false;
    protected $allowedFields  = [];
    protected $protectFields  = false;

    public function __construct()
    {
        parent::__construct();
    }

    private function vendorPagesTable()
    {
        $table = $this->db->prefixTable('vendorproduct_pages');
        if ($table === '' || $table === null) {
            $table = 'vendorproduct_pages';
        }

        return $table;
    }

    private function productApiTable()
    {
        $table = $this->db->prefixTable('product_api');
        if ($table === '' || $table === null) {
            $table = 'product_api';
        }

        return $table;
    }

    public function resolveModelNoForVendorProduct($vendorProductId, $fallbackModelNo = '')
    {
        $fallbackModelNo = trim((string) $fallbackModelNo);
        $vendorProductId = (int) $vendorProductId;
        $aliases = array();
        $addAlias = function ($value) use (&$aliases) {
            $value = trim((string) $value);
            if ($value !== '' && strcasecmp($value, 'N/A') !== 0 && !in_array($value, $aliases, true)) {
                $aliases[] = $value;
            }
        };

        $addAlias($fallbackModelNo);

        if ($vendorProductId > 0) {
            try {
                $row = $this->db->table('vendorproducts')
                    ->select('modelno, modelname')
                    ->where('id', $vendorProductId)
                    ->get()
                    ->getRowArray();
                if (!empty($row)) {
                    $addAlias(isset($row['modelno']) ? $row['modelno'] : '');
                    $addAlias(isset($row['modelname']) ? $row['modelname'] : '');
                }
            } catch (\Throwable $e) {
                // Fall through to page-table lookup.
            }

            $table = $this->vendorPagesTable();
            $query = $this->db->query(
                "SELECT modelno FROM " . $table . " WHERE vendorproduct_id = ? AND modelno IS NOT NULL AND modelno != '' ORDER BY id DESC LIMIT 1",
                array($vendorProductId)
            );
            $pageRow = $query ? $query->getRowArray() : null;
            $addAlias(isset($pageRow['modelno']) ? $pageRow['modelno'] : '');
        }

        if (!empty($aliases) && $this->db->tableExists('api_page_linking_data')) {
            $record = $this->db->table('api_page_linking_data')
                ->select('model_name')
                ->whereIn('model_name', $aliases)
                ->orderBy('created_at', 'DESC')
                ->get()
                ->getRowArray();
            if (!empty($record['model_name'])) {
                return trim((string) $record['model_name']);
            }
        }

        if ($fallbackModelNo !== '') {
            return $fallbackModelNo;
        }

        return isset($aliases[0]) ? $aliases[0] : '';
    }

    private function autoPersistDeviceUiSchemaForModel($modelno)
    {
        $modelno = trim((string) $modelno);
        if ($modelno === '') {
            return;
        }

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
                return;
            }

            $viewPageModel = new \App\Models\View_page_model();
            if (method_exists($viewPageModel, 'autoPersistDeviceUiSchema')) {
                $viewPageModel->autoPersistDeviceUiSchema($modelno, '');
            } elseif (method_exists($viewPageModel, 'syncDeviceUiSchemaFromPageMapping')) {
                $viewPageModel->syncDeviceUiSchemaFromPageMapping($modelno, '');
            }
        } catch (\Throwable $exception) {
            log_message('error', 'Auto device-ui schema sync after pages change failed for ' . $modelno . ': ' . $exception->getMessage());
        }
    }

    public function extract_pages($user_id, $modelno, $id = null)
    {
        $pages = [];
        $table = $this->vendorPagesTable();

        if ($id !== null && $id !== '') {
            $sql = "SELECT full_page_name AS page_names, sequence_no
                FROM " . $table . "
                WHERE vendorproduct_id = ?
                ORDER BY IFNULL(sequence_no, id), id";

            $query = $this->db->query($sql, array((int) $id));
            $pages = $query ? $query->getResultArray() : array();
        } else {
            $sql = "SELECT full_page_name AS page_names, sequence_no
                FROM " . $table . "
                WHERE user_id = ? AND modelno = ?
                ORDER BY IFNULL(sequence_no, id), id";
 
            $query = $this->db->query($sql, array($user_id, $modelno));
            $pages = $query ? $query->getResultArray() : array();
        }

        if (!empty($pages)) {
            return $pages;
        }

        return $this->mergeLegacyProductApiPagesIntoExtract($pages, $modelno, $id);
    }

    private function mergeLegacyProductApiPagesIntoExtract(array $pages, $modelno, $id = null)
    {
        $modelno = trim((string) $modelno);
        if ($modelno === '') {
            return $pages;
        }

        $seen = [];
        $maxSequence = 0;

        foreach ($pages as $page) {
            $name = trim((string) ($page['page_names'] ?? ''));
            if ($name !== '') {
                $seen[strtolower($name)] = true;
            }

            $sequence = (int) ($page['sequence_no'] ?? 0);
            if ($sequence > $maxSequence) {
                $maxSequence = $sequence;
            }
        }

        $apiTable = $this->productApiTable();
        $itemId = (int) $id;

        if ($itemId > 0) {
            $sql = "SELECT page_names, page_no, cmd_uipageno
                FROM " . $apiTable . "
                WHERE model_name = ?
                AND page_names != ''
                AND (itemid = ? OR model_name = ?)
                ORDER BY id ASC";
            $query = $this->db->query($sql, array($modelno, $itemId, $modelno));
        } else {
            $sql = "SELECT page_names, page_no, cmd_uipageno
                FROM " . $apiTable . "
                WHERE model_name = ?
                AND page_names != ''
                ORDER BY id ASC";
            $query = $this->db->query($sql, array($modelno));
        }

        $legacyRows = $query ? $query->getResultArray() : array();

        $legacyByName = [];
        foreach ($legacyRows as $legacyRow) {
            $name = trim((string) ($legacyRow['page_names'] ?? ''));
            if ($name === '' || isset($legacyByName[strtolower($name)])) {
                continue;
            }

            $legacyByName[strtolower($name)] = $legacyRow;
        }

        foreach ($legacyByName as $legacyRow) {
            $name = trim((string) ($legacyRow['page_names'] ?? ''));
            if ($name === '' || isset($seen[strtolower($name)])) {
                continue;
            }

            $sequenceNo = $this->resolveLegacyApiPageNumber($legacyRow);
            if ($sequenceNo <= 0) {
                $maxSequence++;
                $sequenceNo = $maxSequence;
            } elseif ($sequenceNo > $maxSequence) {
                $maxSequence = $sequenceNo;
            }

            $pages[] = [
                'page_names' => $name,
                'sequence_no' => $sequenceNo,
            ];
            $seen[strtolower($name)] = true;
        }

        usort($pages, function ($a, $b) {
            $sequenceCompare = ((int) ($a['sequence_no'] ?? 0)) <=> ((int) ($b['sequence_no'] ?? 0));
            if ($sequenceCompare !== 0) {
                return $sequenceCompare;
            }

            return strcmp((string) ($a['page_names'] ?? ''), (string) ($b['page_names'] ?? ''));
        });

        return $pages;
    }

    private function resolveLegacyApiPageNumber(array $row)
    {
        $pageNo = trim((string) ($row['page_no'] ?? ''));
        if ($pageNo !== '' && is_numeric($pageNo)) {
            return (int) $pageNo;
        }

        $uiPageNo = trim((string) ($row['cmd_uipageno'] ?? ''));
        if ($uiPageNo !== '') {
            foreach (explode(',', $uiPageNo) as $part) {
                $part = trim($part);
                if ($part !== '' && is_numeric($part)) {
                    return (int) $part;
                }
            }
        }

        return 0;
    }

    public function save_pages($data, $page_data)
    {
        if (empty($data) || empty($page_data['modal_id']) || empty($page_data['user_id'])) {
            return false;
        }

        $vendorProductId = (int) $page_data['modal_id'];
        $userId = (string) $page_data['user_id'];
        $modelNo = (string) ($page_data['modelno'] ?? '');
        $pages = array_values(array_unique(array_filter(array_map('trim', $data))));

        if (empty($pages)) {
            return false;
        }

        $table = $this->vendorPagesTable();

        $maxSql = "SELECT MAX(sequence_no) AS sequence_no
            FROM " . $table . "
            WHERE vendorproduct_id = ?";
        $maxQuery = $this->db->query($maxSql, array($vendorProductId));
        $maxSequenceRow = $maxQuery ? $maxQuery->getRowArray() : array();
        $nextSequence = (int) ($maxSequenceRow['sequence_no'] ?? 0) + 1;
        $inserted = 0;

        foreach ($pages as $fullPageName) {
            $pageName = $fullPageName;
            $pageExtension = '';
            $dotPosition = strrpos($fullPageName, '.');

            if ($dotPosition !== false) {
                $pageName = substr($fullPageName, 0, $dotPosition);
                $pageExtension = substr($fullPageName, $dotPosition + 1);
            }

            $existingSql = "SELECT id FROM " . $table . "
                WHERE vendorproduct_id = ? AND full_page_name = ?
                LIMIT 1";
            $existingQuery = $this->db->query($existingSql, array($vendorProductId, $fullPageName));
            $existingPage = $existingQuery ? $existingQuery->getRowArray() : null;

            if ($existingPage) {
                continue;
            }

            $insertSql = "INSERT INTO " . $table . "
                (vendorproduct_id, user_id, modelno, page_name, page_extension, full_page_name, sequence_no)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insertOk = $this->db->query($insertSql, array(
                $vendorProductId,
                $userId,
                $modelNo,
                $pageName,
                $pageExtension,
                $fullPageName,
                $nextSequence
            ));

            if ($insertOk) {
                $inserted++;
                $nextSequence++;
            }
        }

        if ($inserted > 0) {
            $this->autoPersistDeviceUiSchemaForModel($this->resolveModelNoForVendorProduct($vendorProductId, $modelNo));
        }

        return $inserted > 0;
    }

    public function save_page_seq($data, $user_id)
    {
        if (empty($data['pageSelect']) || empty($data['page_seq']) || empty($user_id)) {
            return false;
        }

        $vendorProductId = (int) $data['pageSelect'];
        $sequenceEntries = array_filter(array_map('trim', explode(';', $data['page_seq'])));

        if (empty($sequenceEntries)) {
            return false;
        }

        $table = $this->vendorPagesTable();
        $updated = 0;

        foreach ($sequenceEntries as $entry) {
            if (!preg_match('/^(.*?)\s+-\s+Sequence:\s+(\d+)$/', $entry, $matches)) {
                continue;
            }

            $pageName = trim($matches[1]);
            $sequenceNo = (int) $matches[2];

            $updateSql = "UPDATE " . $table . "
                SET sequence_no = ?
                WHERE vendorproduct_id = ? AND full_page_name = ?";
            $ok = $this->db->query($updateSql, array($sequenceNo, $vendorProductId, $pageName));
            if ($ok) {
                $updated++;
            }
        }

        if ($updated > 0) {
            $this->autoPersistDeviceUiSchemaForModel($this->resolveModelNoForVendorProduct($vendorProductId));
        }

        return $updated > 0;
    }

    public function reorder_saved_pages($modal_id, $user_id, array $pages)
    {
        if (empty($modal_id) || empty($pages)) {
            return false;
        }

        $table = $this->vendorPagesTable();
        $vendorProductId = (int) $modal_id;
        $selectSql = "SELECT id, full_page_name FROM " . $table . " WHERE vendorproduct_id = ?";
        $query = $this->db->query($selectSql, array($vendorProductId));
        $rows = $query ? $query->getResultArray() : array();

        if (empty($rows)) {
            return false;
        }

        $idByName = array();
        foreach ($rows as $row) {
            $nameKey = strtolower(trim((string) (isset($row['full_page_name']) ? $row['full_page_name'] : '')));
            if ($nameKey !== '') {
                $idByName[$nameKey] = (int) $row['id'];
            }
        }

        $updated = 0;
        foreach (array_values($pages) as $index => $pageName) {
            $nameKey = strtolower(trim((string) $pageName));
            if ($nameKey === '' || !isset($idByName[$nameKey])) {
                continue;
            }

            $updateSql = "UPDATE " . $table . " SET sequence_no = ? WHERE id = ?";
            $ok = $this->db->query($updateSql, array(($index + 1), $idByName[$nameKey]));
            if ($ok) {
                $updated++;
            }
        }

        if ($updated > 0) {
            $this->autoPersistDeviceUiSchemaForModel($this->resolveModelNoForVendorProduct($vendorProductId));
        }

        return $updated > 0;
    }

    public function delete_saved_page($modal_id, $user_id, $page_name)
    {
        $page_name = trim((string) $page_name);
        $vendorProductId = (int) $modal_id;

        if ($vendorProductId <= 0 || $page_name === '') {
            return false;
        }

        $table = $this->vendorPagesTable();
        $deleteSql = "DELETE FROM " . $table . "
            WHERE vendorproduct_id = ?
            AND (
                TRIM(full_page_name) = ?
                OR TRIM(page_name) = ?
                OR TRIM(CONCAT(IFNULL(page_name, ''), '.', IFNULL(page_extension, ''))) = ?
            )";
        $this->db->query($deleteSql, array($vendorProductId, $page_name, $page_name, $page_name));

        $checkSql = "SELECT id FROM " . $table . "
            WHERE vendorproduct_id = ?
            AND TRIM(full_page_name) = ?
            LIMIT 1";
        $check = $this->db->query($checkSql, array($vendorProductId, $page_name));
        $stillExists = $check ? $check->getRowArray() : null;

        if (empty($stillExists)) {
            $this->autoPersistDeviceUiSchemaForModel($this->resolveModelNoForVendorProduct($vendorProductId));
        }

        return empty($stillExists);
    }

    public function delete_legacy_page_name($modal_id, $modelno, $page_name)
    {
        $modelno = trim((string) $modelno);
        $pageName = trim((string) $page_name);

        if ($modelno === '' || $pageName === '') {
            return false;
        }

        $apiTable = $this->productApiTable();
        $itemId = (int) $modal_id;

        if ($itemId > 0) {
            $updateSql = "UPDATE " . $apiTable . "
                SET page_names = ''
                WHERE model_name = ? AND page_names = ? AND itemid = ?";
            $ok = $this->db->query($updateSql, array($modelno, $pageName, $itemId));
        } else {
            $updateSql = "UPDATE " . $apiTable . "
                SET page_names = ''
                WHERE model_name = ? AND page_names = ?";
            $ok = $this->db->query($updateSql, array($modelno, $pageName));
        }

        return $ok ? true : false;
    }

    public function get_last_db_error()
    {
        return $this->db->error();
    }
}
