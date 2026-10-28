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
        $pages = $this->loadVendorPages($user_id, $modelno, $id);
        $pages = $this->mergeLegacyProductApiPagesIntoExtract($pages, $modelno, $id);

        // Old models keep their real pages on product_api. Once any new page is
        // stored in vendorproduct_pages, those older pages must stay in the list
        // and become real rows so a later save cannot drop them.
        if ($id !== null && $id !== '' && (int) $id > 0) {
            $inserted = $this->persistMissingVendorPages((int) $id, $user_id, $modelno, $pages);
            if ($inserted > 0) {
                $pages = $this->loadVendorPages($user_id, $modelno, $id);
                $pages = $this->mergeLegacyProductApiPagesIntoExtract($pages, $modelno, $id);
            }
        }

        return $pages;
    }

    private function loadVendorPages($user_id, $modelno, $id = null)
    {
        $table = $this->vendorPagesTable();

        if ($id !== null && $id !== '') {
            $sql = "SELECT full_page_name AS page_names, sequence_no
                FROM " . $table . "
                WHERE vendorproduct_id = ?
                ORDER BY IFNULL(sequence_no, id), id";

            $query = $this->db->query($sql, array((int) $id));
            return $query ? $query->getResultArray() : array();
        }

        $sql = "SELECT full_page_name AS page_names, sequence_no
            FROM " . $table . "
            WHERE user_id = ? AND modelno = ?
            ORDER BY IFNULL(sequence_no, id), id";

        $query = $this->db->query($sql, array($user_id, $modelno));
        return $query ? $query->getResultArray() : array();
    }

    private function persistMissingVendorPages($vendorProductId, $userId, $modelNo, array $pages)
    {
        $vendorProductId = (int) $vendorProductId;
        if ($vendorProductId <= 0 || empty($pages)) {
            return 0;
        }

        $table = $this->vendorPagesTable();
        $existingQuery = $this->db->query(
            "SELECT full_page_name, user_id, modelno, sequence_no FROM " . $table . " WHERE vendorproduct_id = ?",
            array($vendorProductId)
        );
        $existingRows = $existingQuery ? $existingQuery->getResultArray() : array();

        $seen = array();
        $resolvedUserId = trim((string) $userId);
        $resolvedModelNo = trim((string) $modelNo);
        $maxSequence = 0;

        foreach ($existingRows as $row) {
            $name = strtolower(trim((string) (isset($row['full_page_name']) ? $row['full_page_name'] : '')));
            if ($name !== '') {
                $seen[$name] = true;
            }
            if ($resolvedUserId === '' && !empty($row['user_id'])) {
                $resolvedUserId = trim((string) $row['user_id']);
            }
            if ($resolvedModelNo === '' && !empty($row['modelno'])) {
                $resolvedModelNo = trim((string) $row['modelno']);
            }
            $sequence = (int) (isset($row['sequence_no']) ? $row['sequence_no'] : 0);
            if ($sequence > $maxSequence) {
                $maxSequence = $sequence;
            }
        }

        if ($resolvedModelNo === '') {
            $resolvedModelNo = $this->resolveModelNoForVendorProduct($vendorProductId, $modelNo);
        }

        if ($resolvedUserId === '' && $resolvedModelNo !== '') {
            $apiTable = $this->productApiTable();
            $userQuery = $this->db->query(
                "SELECT user_id FROM " . $apiTable . " WHERE model_name = ? AND user_id IS NOT NULL AND user_id != '' ORDER BY id DESC LIMIT 1",
                array($resolvedModelNo)
            );
            $userRow = $userQuery ? $userQuery->getRowArray() : null;
            if (!empty($userRow['user_id'])) {
                $resolvedUserId = trim((string) $userRow['user_id']);
            }
        }

        if ($resolvedUserId === '') {
            return 0;
        }

        $inserted = 0;
        foreach ($pages as $page) {
            $fullPageName = trim((string) (isset($page['page_names']) ? $page['page_names'] : ''));
            $nameKey = strtolower($fullPageName);
            if ($fullPageName === '' || isset($seen[$nameKey]) || strcasecmp($fullPageName, 'None') === 0) {
                continue;
            }

            $pageName = $fullPageName;
            $pageExtension = 'PAGE';
            $dotPosition = strrpos($fullPageName, '.');
            if ($dotPosition !== false && $dotPosition > 0) {
                $pageName = substr($fullPageName, 0, $dotPosition);
                $extension = substr($fullPageName, $dotPosition + 1);
                if ($extension !== '') {
                    $pageExtension = $extension;
                }
            }

            $sequenceNo = (int) (isset($page['sequence_no']) ? $page['sequence_no'] : 0);
            if ($sequenceNo <= 0) {
                $maxSequence++;
                $sequenceNo = $maxSequence;
            } elseif ($sequenceNo > $maxSequence) {
                $maxSequence = $sequenceNo;
            }

            $insertSql = "INSERT INTO " . $table . "
                (vendorproduct_id, user_id, modelno, page_name, page_extension, full_page_name, sequence_no)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insertOk = $this->db->query($insertSql, array(
                $vendorProductId,
                substr($resolvedUserId, 0, 10),
                $resolvedModelNo,
                substr($pageName, 0, 150),
                substr($pageExtension, 0, 30),
                substr($fullPageName, 0, 200),
                $sequenceNo
            ));

            if ($insertOk) {
                $seen[$nameKey] = true;
                $inserted++;
            }
        }

        if ($inserted > 0) {
            $this->autoPersistDeviceUiSchemaForModel($resolvedModelNo);
        }

        return $inserted;
    }

    private function collectModelAliases($vendorProductId, $fallbackModelNo = '')
    {
        $aliases = array();
        $addAlias = function ($value) use (&$aliases) {
            $value = trim((string) $value);
            if ($value !== '' && strcasecmp($value, 'N/A') !== 0 && !in_array($value, $aliases, true)) {
                $aliases[] = $value;
            }
        };

        $addAlias($fallbackModelNo);
        $vendorProductId = (int) $vendorProductId;

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
                // Keep the fallback model number.
            }

            $table = $this->vendorPagesTable();
            $query = $this->db->query(
                "SELECT DISTINCT modelno FROM " . $table . " WHERE vendorproduct_id = ? AND modelno IS NOT NULL AND modelno != ''",
                array($vendorProductId)
            );
            foreach (($query ? $query->getResultArray() : array()) as $pageRow) {
                $addAlias(isset($pageRow['modelno']) ? $pageRow['modelno'] : '');
            }
        }

        if (!empty($aliases) && $this->db->tableExists('product_api')) {
            $placeholders = implode(', ', array_fill(0, count($aliases), '?'));
            $apiQuery = $this->db->query(
                "SELECT DISTINCT model_name FROM " . $this->productApiTable() . " WHERE model_name IN (" . $placeholders . ")",
                $aliases
            );
            foreach (($apiQuery ? $apiQuery->getResultArray() : array()) as $apiRow) {
                $addAlias(isset($apiRow['model_name']) ? $apiRow['model_name'] : '');
            }
        }

        return $aliases;
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
        $aliases = $this->collectModelAliases($id, $modelno);
        if (empty($aliases)) {
            return $pages;
        }

        $placeholders = implode(', ', array_fill(0, count($aliases), '?'));
        $sql = "SELECT page_names, page_no, cmd_uipageno
            FROM " . $apiTable . "
            WHERE model_name IN (" . $placeholders . ")
            AND page_names != ''
            ORDER BY id ASC";
        $query = $this->db->query($sql, $aliases);

        $legacyRows = $query ? $query->getResultArray() : array();

        $legacyByName = [];
        foreach ($legacyRows as $legacyRow) {
            $rawName = trim((string) ($legacyRow['page_names'] ?? ''));
            $nameParts = (strpos($rawName, ',') !== false) ? explode(',', $rawName) : array($rawName);

            foreach ($nameParts as $name) {
                $name = trim((string) $name);
                if ($name === '' || strcasecmp($name, 'None') === 0 || isset($legacyByName[strtolower($name)])) {
                    continue;
                }

                $copy = $legacyRow;
                $copy['page_names'] = $name;
                $legacyByName[strtolower($name)] = $copy;
            }
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
        $query = $this->db->query(
            "SELECT id, page_names FROM " . $apiTable . "
                WHERE model_name = ?
                AND page_names IS NOT NULL
                AND TRIM(page_names) <> ''",
            array($modelno)
        );
        $rows = $query ? $query->getResultArray() : array();

        foreach ($rows as $row) {
            $rawName = trim((string) (isset($row['page_names']) ? $row['page_names'] : ''));
            $parts = array_values(array_filter(array_map('trim', explode(',', $rawName)), function ($part) {
                return $part !== '';
            }));
            $kept = array();
            $removed = false;

            foreach ($parts as $part) {
                if (strcasecmp($part, $pageName) === 0) {
                    $removed = true;
                    continue;
                }
                $kept[] = $part;
            }

            if (!$removed) {
                continue;
            }

            $this->db->query(
                "UPDATE " . $apiTable . " SET page_names = ? WHERE id = ?",
                array(implode(',', $kept), (int) $row['id'])
            );
        }

        $this->removePageFromLinkingRecord($modelno, $pageName);

        return true;
    }

    private function removePageFromLinkingRecord($modelno, $pageName)
    {
        $modelno = trim((string) $modelno);
        $pageName = strtolower(trim((string) $pageName));
        if ($modelno === '' || $pageName === '' || !$this->db->tableExists('api_page_linking_data')) {
            return false;
        }

        $record = $this->db->table('api_page_linking_data')
            ->where('model_name', $modelno)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRowArray();

        if (empty($record['id'])) {
            return false;
        }

        $pageNames = json_decode(isset($record['page_names']) ? $record['page_names'] : '[]', true);
        $linkingData = json_decode(isset($record['linking_data']) ? $record['linking_data'] : '[]', true);
        if (!is_array($pageNames)) {
            $pageNames = array();
        }
        if (!is_array($linkingData)) {
            $linkingData = array();
        }

        $removedPageNos = array();
        foreach ($pageNames as $pageNo => $name) {
            if (strtolower(trim((string) $name)) === $pageName) {
                $removedPageNos[] = (string) $pageNo;
                unset($pageNames[$pageNo]);
            }
        }

        foreach ($linkingData as $pageKey => $entries) {
            if ($pageKey === '__ui_rows__' || !is_array($entries)) {
                continue;
            }

            if (in_array((string) $pageKey, $removedPageNos, true)) {
                unset($linkingData[$pageKey]);
                continue;
            }

            $keptEntries = array();
            foreach ($entries as $entry) {
                $entryName = is_array($entry) ? trim((string) (isset($entry['page_name']) ? $entry['page_name'] : '')) : '';
                if ($entryName !== '' && strtolower($entryName) === $pageName) {
                    continue;
                }
                $keptEntries[] = $entry;
            }

            if (empty($keptEntries)) {
                unset($linkingData[$pageKey]);
            } else {
                $linkingData[$pageKey] = array_values($keptEntries);
            }
        }

        if (isset($linkingData['__ui_rows__']) && is_array($linkingData['__ui_rows__'])) {
            $keptRows = array();
            foreach ($linkingData['__ui_rows__'] as $uiRow) {
                if (!is_array($uiRow)) {
                    continue;
                }
                $rowName = strtolower(trim((string) (isset($uiRow['page_name']) ? $uiRow['page_name'] : '')));
                $rowNo = trim((string) (isset($uiRow['page_no']) ? $uiRow['page_no'] : ''));
                if ($rowName === $pageName || ($rowNo !== '' && in_array($rowNo, $removedPageNos, true))) {
                    continue;
                }
                $keptRows[] = $uiRow;
            }
            $linkingData['__ui_rows__'] = array_values($keptRows);
        }

        $this->db->table('api_page_linking_data')
            ->where('id', (int) $record['id'])
            ->update(array(
                'page_names' => json_encode($pageNames, JSON_UNESCAPED_UNICODE),
                'linking_data' => json_encode($linkingData, JSON_UNESCAPED_UNICODE),
            ));

        return true;
    }

    public function get_last_db_error()
    {
        return $this->db->error();
    }
}
