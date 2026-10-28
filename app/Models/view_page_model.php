<?php

namespace App\Models;

use App\Services\DeviceUiSchemaGenerator;
use CodeIgniter\Model;

class View_page_model extends Model
{
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;
    protected $allowedFields    = [];

    private $deviceUiLiveProductCache = [];
    private $deviceUiTestNodesCache = [];
    private $deviceUiSchemaSyncing = false;
    private $pageParameterMappingCache = [];
    private $pageParameterMappingBuilding = [];
    private $linkingPageOptionsCache = [];
    private $productApiPageParameterRowsCache = [];

    /**
     * Bump when Device UI schema generator / normalizer output shape changes.
     * Stale models auto-rebuild on next getDeviceUiSchema / API fetch.
     */
    private const DEVICE_UI_SCHEMA_BUILD_VERSION = '2026-10-08.1';

    public function __construct()
    {
        parent::__construct();
        self::loadDeviceUiSchemaGenerator();
    }

    public static function loadDeviceUiSchemaGenerator()
    {
        if (class_exists(DeviceUiSchemaGenerator::class, false)) {
            return true;
        }

        $candidates = [
            __DIR__ . '/../services/DeviceUiSchemaGenerator.php',
            __DIR__ . '/../Services/DeviceUiSchemaGenerator.php',
        ];
        if (defined('APPPATH')) {
            $candidates[] = APPPATH . 'services/DeviceUiSchemaGenerator.php';
            $candidates[] = APPPATH . 'Services/DeviceUiSchemaGenerator.php';
        }

        foreach ($candidates as $file) {
            if (is_file($file)) {
                require_once $file;
                return class_exists(DeviceUiSchemaGenerator::class, false);
            }
        }

        return false;
    }

// here studio funciots start

public function extract_pages($user_id, $modelno, $id = null)
{
    $pages = [];

    // Match Modal Pages: pages belong to the vendor product, not a user-scoped subset.
    if ($id !== null && $id !== '') {
        $sql = "
            SELECT full_page_name AS page_names, sequence_no
            FROM vendorproduct_pages
            WHERE vendorproduct_id = ?
            ORDER BY COALESCE(sequence_no, id), id
        ";

        $pages = $this->db->query($sql, [$id])->getResultArray();
    }

    if (empty($pages) && $user_id !== null && $user_id !== '' && trim((string) $modelno) !== '') {
        $sql = "
            SELECT full_page_name AS page_names, sequence_no
            FROM vendorproduct_pages
            WHERE user_id = ? AND modelno = ?
            ORDER BY COALESCE(sequence_no, id), id
        ";

        $pages = $this->db->query($sql, [$user_id, $modelno])->getResultArray();
    }

    // Always merge product_api pages. Old models store real pages there, and
    // vendorproduct_pages may be empty or only contain later test pages.
    return $this->mergeLegacyProductApiPagesIntoExtract($pages, $modelno, $id);
}

public function getLinkingPageOptions($user_id, $modelno, $id = null)
{
    $cacheKey = trim((string) $user_id) . '|' . trim((string) $modelno) . '|' . trim((string) $id);
    if (isset($this->linkingPageOptionsCache[$cacheKey])) {
        return $this->linkingPageOptionsCache[$cacheKey];
    }

    $pages = $this->extract_pages($user_id, $modelno, $id);
    $pageOptionsByName = [];
    $usedNos = [];

    foreach ($pages as $index => $page) {
        $name = trim((string) ($page['page_names'] ?? ''));
        if ($name === '') {
            continue;
        }

        $key = strtolower($name);
        if (isset($pageOptionsByName[$key])) {
            continue;
        }

        $sequenceNo = trim((string) ($page['sequence_no'] ?? ''));
        $pageNo = ($sequenceNo !== '' && ctype_digit($sequenceNo) && (int) $sequenceNo > 0)
            ? (string) ((int) $sequenceNo)
            : (string) ($index + 1);

        if (isset($usedNos[$pageNo])) {
            $next = !empty($usedNos) ? (max(array_map('intval', array_keys($usedNos))) + 1) : 1;
            $pageNo = (string) $next;
        }

        $usedNos[$pageNo] = true;
        $pageOptionsByName[$key] = [
            'page_no' => $pageNo,
            'page_name' => $name,
        ];
    }

    $pageOptions = array_values($pageOptionsByName);
    usort($pageOptions, function ($a, $b) {
        return ((int) ($a['page_no'] ?? 0)) <=> ((int) ($b['page_no'] ?? 0));
    });

    $this->linkingPageOptionsCache[$cacheKey] = $pageOptions;

    return $pageOptions;
}

public function getProductApiPageParameterRows($modelno, $userId = null, $itemId = 0)
{
    $modelno = trim((string) $modelno);
    $cacheKey = $modelno . '|' . trim((string) $userId) . '|' . (int) $itemId;
    if (isset($this->productApiPageParameterRowsCache[$cacheKey])) {
        return $this->productApiPageParameterRowsCache[$cacheKey];
    }

    if ($modelno === '' || !$this->db->tableExists('product_api')) {
        $this->productApiPageParameterRowsCache[$cacheKey] = [];
        return [];
    }

    $itemId = (int) $itemId;
    if ($itemId <= 0) {
        $legacyRow = $this->db->table('product_api')
            ->select('itemid')
            ->where('model_name', $modelno)
            ->where('itemid >', 0)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();
        if (!empty($legacyRow['itemid'])) {
            $itemId = (int) $legacyRow['itemid'];
        }
    }

    $pageOptions = $this->getLinkingPageOptions($userId, $modelno, $itemId > 0 ? $itemId : null);
    $pageNoByName = [];
    foreach ($pageOptions as $option) {
        $pageName = trim((string) ($option['page_name'] ?? ''));
        $pageNo = trim((string) ($option['page_no'] ?? ''));
        if ($pageName === '' || $pageNo === '') {
            continue;
        }
        $pageNoByName[strtolower($pageName)] = $pageNo;
    }

    $apiRows = $this->db->table('product_api')
        ->select('page_names, parameter_name, page_no, cmd_uipageno, sequence_no, id')
        ->where('model_name', $modelno)
        ->where('parameter_name !=', '')
        ->where('page_names !=', '')
        ->orderBy('id', 'ASC')
        ->get()
        ->getResultArray();

    $grouped = [];
    foreach ($apiRows as $apiRow) {
        $parameterName = trim((string) ($apiRow['parameter_name'] ?? ''));
        if ($parameterName === '') {
            continue;
        }

        foreach ($this->splitLegacyPageNames($apiRow['page_names'] ?? '') as $pageName) {
            $key = strtolower($pageName);
            $pageNo = $pageNoByName[$key] ?? '';
            if ($pageNo === '') {
                continue;
            }

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'page_no' => $pageNo,
                    'page_name' => $pageName,
                    'parameters' => [],
                    'sequence' => max(1, (int) $pageNo),
                    'locked' => true,
                ];
            }

            if (!in_array($parameterName, $grouped[$key]['parameters'], true)) {
                $grouped[$key]['parameters'][] = $parameterName;
            }
        }
    }

    $rows = array_values(array_filter($grouped, function ($row) {
        return is_array($row) && !empty($row['parameters']);
    }));
    usort($rows, function ($a, $b) {
        return ((int) ($a['page_no'] ?? 0)) <=> ((int) ($b['page_no'] ?? 0));
    });

    $this->productApiPageParameterRowsCache[$cacheKey] = $rows;

    return $rows;
}

private function splitLegacyPageNames($value)
{
    $names = [];
    $value = trim((string) $value);
    if ($value === '') {
        return $names;
    }

    foreach (preg_split('/\s*,\s*/', $value) as $name) {
        $name = trim($name);
        if ($name === '' || strcasecmp($name, 'None') === 0) {
            continue;
        }
        $names[] = $name;
    }

    return $names;
}

private function seedLockedUiRowsFromLoadedProductApi(array $apiRows, array &$rows, array &$pageNames, array &$uiRows)
{
    $legacyPageNames = [];
    foreach ($apiRows as $apiRow) {
        if (!is_array($apiRow)) {
            continue;
        }
        foreach ($this->splitLegacyPageNames($apiRow['page_names'] ?? '') as $name) {
            $legacyPageNames[strtolower($name)] = $name;
        }
    }
    if (empty($legacyPageNames)) {
        return;
    }

    $grouped = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $pageName = trim((string) ($row['page_name'] ?? ''));
        $pageNo = trim((string) ($row['page_no'] ?? ''));
        $parameter = trim((string) ($row['parameter'] ?? ''));
        $key = strtolower($pageName);
        if ($pageName === '' || $parameter === '' || !isset($legacyPageNames[$key])) {
            continue;
        }
        if (!isset($grouped[$key])) {
            $grouped[$key] = [
                'page_no' => $pageNo !== '' ? $pageNo : '1',
                'page_name' => $legacyPageNames[$key],
                'parameters' => [],
                'sequence' => max(1, (int) $pageNo),
                'locked' => true,
            ];
        }
        if (!in_array($parameter, $grouped[$key]['parameters'], true)) {
            $grouped[$key]['parameters'][] = $parameter;
        }
        if ($pageNo !== '') {
            $grouped[$key]['page_no'] = $pageNo;
            $pageNames[$pageNo] = $grouped[$key]['page_name'];
        }
    }

    if (empty($grouped)) {
        return;
    }

    $uiByName = [];
    foreach ($uiRows as $idx => $uiRow) {
        if (!is_array($uiRow)) {
            continue;
        }
        $key = strtolower(trim((string) ($uiRow['page_name'] ?? '')));
        if ($key !== '') {
            $uiByName[$key] = $idx;
        }
    }

    foreach ($grouped as $key => $locked) {
        if (isset($uiByName[$key])) {
            $uiRows[$uiByName[$key]]['page_no'] = $locked['page_no'];
            $uiRows[$uiByName[$key]]['page_name'] = $locked['page_name'];
            $uiRows[$uiByName[$key]]['parameters'] = $locked['parameters'];
            $uiRows[$uiByName[$key]]['locked'] = true;
        } else {
            $uiRows[] = $locked;
        }
    }
}

private function mergeLegacyProductApiPagesIntoExtract(array $pages, $modelno, $id = null)
{
    $modelno = trim((string) $modelno);
    if ($modelno === '' || !$this->db->tableExists('product_api')) {
        return $pages;
    }

    $seen = [];
    $usedSequences = [];
    $maxSequence = 0;

    foreach ($pages as $page) {
        $name = trim((string) ($page['page_names'] ?? ''));
        if ($name !== '') {
            $seen[strtolower($name)] = true;
        }

        $sequence = (int) ($page['sequence_no'] ?? 0);
        if ($sequence > 0) {
            $usedSequences[$sequence] = true;
        }
        if ($sequence > $maxSequence) {
            $maxSequence = $sequence;
        }
    }

    $legacyRows = $this->db->table('product_api')
        ->select('page_names, page_no, cmd_uipageno')
        ->where('model_name', $modelno)
        ->where('page_names !=', '')
        ->orderBy('id', 'ASC')
        ->get()
        ->getResultArray();

    $legacyByName = [];
    foreach ($legacyRows as $legacyRow) {
        foreach ($this->splitLegacyPageNames($legacyRow['page_names'] ?? '') as $name) {
            $key = strtolower($name);
            if (isset($legacyByName[$key])) {
                continue;
            }
            $copy = $legacyRow;
            $copy['page_names'] = $name;
            $legacyByName[$key] = $copy;
        }
    }

    foreach ($legacyByName as $legacyRow) {
        $name = trim((string) ($legacyRow['page_names'] ?? ''));
        if ($name === '' || isset($seen[strtolower($name)])) {
            continue;
        }

        $sequenceNo = $this->resolveLegacyApiPageNumber($legacyRow);
        if ($sequenceNo <= 0 || isset($usedSequences[$sequenceNo])) {
            $maxSequence++;
            $sequenceNo = $maxSequence;
        } elseif ($sequenceNo > $maxSequence) {
            $maxSequence = $sequenceNo;
        }

        $usedSequences[$sequenceNo] = true;
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

private function pageParameterLinkKey($pageNo, $parameterName)
{
    $pageNo = trim((string) $pageNo);
    $parameterName = trim((string) $parameterName);

    if ($pageNo === '') {
        $pageNo = '1';
    }

    return strtolower($pageNo) . ':' . strtolower($parameterName);
}

public function isDefaultStatusOperationPage($pageName)
{
    $name = strtolower(trim((string) $pageName));
    if ($name === '') {
        return false;
    }
    $compact = preg_replace('/\s+/', '', $name);

    return $compact === 'status.operation' || $compact === 'status';
}

public function preserveDefaultStatusOperationLinking($modelno, array $pageNameMap, array $linkingData, array $uiRows)
{
    $existing = $this->getPageParameterMapping($modelno);
    if (!is_array($existing)) {
        return [$pageNameMap, $linkingData, $uiRows];
    }

    $existingUiRows = is_array($existing['ui_rows'] ?? null) ? $existing['ui_rows'] : [];
    $statusUi = null;
    foreach ($existingUiRows as $row) {
        if (!is_array($row) || !$this->isDefaultStatusOperationPage($row['page_name'] ?? '')) {
            continue;
        }
        $statusUi = $row;
        break;
    }

    if ($statusUi === null && is_array($existing['rows'] ?? null)) {
        $grouped = [];
        foreach ($existing['rows'] as $row) {
            if (!is_array($row) || !$this->isDefaultStatusOperationPage($row['page_name'] ?? '')) {
                continue;
            }
            $pageNo = trim((string) ($row['page_no'] ?? ''));
            $parameter = trim((string) ($row['parameter'] ?? ''));
            if ($pageNo === '' || $parameter === '') {
                continue;
            }
            if (!isset($grouped[$pageNo])) {
                $grouped[$pageNo] = [
                    'page_no' => $pageNo,
                    'page_name' => trim((string) ($row['page_name'] ?? '')),
                    'parameters' => [],
                    'sequence' => (int) ($row['sequence'] ?? 1),
                ];
            }
            if (!in_array($parameter, $grouped[$pageNo]['parameters'], true)) {
                $grouped[$pageNo]['parameters'][] = $parameter;
            }
        }
        $statusUi = $grouped ? reset($grouped) : null;
    }

    $lockedParams = [];
    if (is_array($statusUi) && !empty($statusUi['parameters']) && is_array($statusUi['parameters'])) {
        foreach ($statusUi['parameters'] as $parameter) {
            $parameter = trim((string) $parameter);
            if ($parameter !== '' && !in_array($parameter, $lockedParams, true)) {
                $lockedParams[] = $parameter;
            }
        }
    }

    if (empty($lockedParams)) {
        return [$pageNameMap, $linkingData, $uiRows];
    }

    $statusPageNo = trim((string) ($statusUi['page_no'] ?? '1'));
    $statusPageName = trim((string) ($statusUi['page_name'] ?? 'Status.operation'));
    $found = false;

    foreach ($uiRows as &$row) {
        if (!is_array($row) || !$this->isDefaultStatusOperationPage($row['page_name'] ?? '')) {
            continue;
        }
        $found = true;
        $incoming = [];
        if (!empty($row['parameters']) && is_array($row['parameters'])) {
            foreach ($row['parameters'] as $parameter) {
                $parameter = trim((string) $parameter);
                if ($parameter !== '' && !in_array($parameter, $incoming, true)) {
                    $incoming[] = $parameter;
                }
            }
        }
        $sameSet = count($incoming) === count($lockedParams)
            && empty(array_diff($lockedParams, $incoming))
            && empty(array_diff($incoming, $lockedParams));
        $row['parameters'] = $sameSet ? $incoming : $lockedParams;
        $row['page_name'] = $statusPageName;
        $pageNo = trim((string) ($row['page_no'] ?? $statusPageNo));
        $pageNameMap[$pageNo] = $statusPageName;
        $linkingData[$pageNo] = [];
        $sequence = 1;
        foreach ($row['parameters'] as $parameter) {
            $linkingData[$pageNo][] = [
                'parameter' => $parameter,
                'sequence' => $sequence++,
                'page_no' => $pageNo,
                'page_name' => $statusPageName,
            ];
        }
        break;
    }
    unset($row);

    if (!$found) {
        array_unshift($uiRows, [
            'page_no' => $statusPageNo,
            'page_name' => $statusPageName,
            'parameters' => $lockedParams,
            'sequence' => (int) ($statusUi['sequence'] ?? 1),
        ]);
        $pageNameMap[$statusPageNo] = $statusPageName;
        $linkingData[$statusPageNo] = [];
        $sequence = 1;
        foreach ($lockedParams as $parameter) {
            $linkingData[$statusPageNo][] = [
                'parameter' => $parameter,
                'sequence' => $sequence++,
                'page_no' => $statusPageNo,
                'page_name' => $statusPageName,
            ];
        }
    }

    return [$pageNameMap, $linkingData, $uiRows];
}

public function preserveLockedProductApiLinking($modelno, array $pageNameMap, array $linkingData, array $uiRows, $userId = null, $itemId = 0)
{
    // Status.operation stays reserved. Every other page the user removes must
    // stay removed. Rebuilding rows from product_api here put deleted pages back
    // into api_page_linking_data on the next save.
    return $this->preserveDefaultStatusOperationLinking(
        $modelno,
        $pageNameMap,
        $linkingData,
        $uiRows
    );
}

public function getPageParameterMapping($modelno)
{
    $modelno = trim((string) $modelno);
    if ($modelno === '') {
        return null;
    }

    if (array_key_exists($modelno, $this->pageParameterMappingCache)) {
        return $this->pageParameterMappingCache[$modelno];
    }

    if (!empty($this->pageParameterMappingBuilding[$modelno])) {
        return [
            'model_name' => $modelno,
            'page_names_array' => [],
            'linking_data_array' => [],
            'ui_rows' => [],
            'rows' => [],
        ];
    }

    $this->pageParameterMappingBuilding[$modelno] = true;

    $record = $this->db->table('api_page_linking_data')
        ->where('model_name', $modelno)
        ->orderBy('created_at', 'DESC')
        ->get()
        ->getRowArray();
    $hasSavedMappingRecord = !empty($record);

    if (!$record) {
        $record = [
            'model_name' => $modelno,
            'page_names' => '[]',
            'linking_data' => '[]',
        ];
    }

    $pageNames = json_decode($record['page_names'] ?? '[]', true);
    $linkingData = json_decode($record['linking_data'] ?? '[]', true);

    if (!is_array($pageNames)) {
        $pageNames = [];
    }

    if (!is_array($linkingData)) {
        $linkingData = [];
    }

    $uiRows = [];
    if (isset($linkingData['__ui_rows__']) && is_array($linkingData['__ui_rows__'])) {
        $uiRows = $linkingData['__ui_rows__'];
    }
    $removedPages = [];
    if (isset($linkingData['__removed_pages__']) && is_array($linkingData['__removed_pages__'])) {
        foreach ($linkingData['__removed_pages__'] as $removedName) {
            $removedName = strtolower(trim((string) $removedName));
            if ($removedName !== '') {
                $removedPages[$removedName] = true;
            }
        }
    }
    unset($linkingData['__ui_rows__'], $linkingData['__removed_pages__']);

    if (!empty($removedPages)) {
        foreach ($pageNames as $pageNo => $pageName) {
            if (isset($removedPages[strtolower(trim((string) $pageName))])) {
                unset($pageNames[$pageNo]);
            }
        }
        foreach ($linkingData as $linkKey => $entries) {
            if (!is_array($entries)) {
                unset($linkingData[$linkKey]);
                continue;
            }
            $keptEntries = [];
            foreach ($entries as $entry) {
                $entryName = is_array($entry) ? trim((string) ($entry['page_name'] ?? '')) : '';
                if ($entryName !== '' && isset($removedPages[strtolower($entryName)])) {
                    continue;
                }
                $keptEntries[] = $entry;
            }
            if (empty($keptEntries)) {
                unset($linkingData[$linkKey]);
            } else {
                $linkingData[$linkKey] = $keptEntries;
            }
        }
        $uiRows = array_values(array_filter($uiRows, static function ($uiRow) use ($removedPages) {
            if (!is_array($uiRow)) {
                return false;
            }
            $pageName = strtolower(trim((string) ($uiRow['page_name'] ?? '')));
            return $pageName === '' || !isset($removedPages[$pageName]);
        }));
    }

    $apiRows = $this->db->table('product_api')
        ->where('model_name', $modelno)
        ->get()
        ->getResultArray();
    $apiByParameter = $this->loadDeviceUiApiMetadataByParameter($modelno, $apiRows);
    $vendorPageMap = $this->getVendorPageNumberMap($modelno);

    $rows = [];
    $pageNoByName = [];
    $linkedParameters = [];
    foreach ($pageNames as $no => $name) {
        $normalizedNo = trim((string) $no);
        $normalizedName = trim((string) $name);
        if ($normalizedNo !== '') {
            $pageNames[$normalizedNo] = $normalizedName !== '' ? $normalizedName : ('Page ' . $normalizedNo);
            if ($normalizedName !== '') {
                $pageNoByName[strtolower($normalizedName)] = $normalizedNo;
            }
        }
    }

    foreach ($linkingData as $linkKey => $entries) {
        if ($linkKey === '__ui_rows__' || !is_array($entries)) {
            continue;
        }
        foreach ($entries as $index => $entry) {
            $parameterName = is_array($entry) ? trim((string) ($entry['parameter'] ?? '')) : trim((string) $entry);
            if ($parameterName === '') {
                continue;
            }

            $resolvedPageNo = is_array($entry) ? trim((string) ($entry['page_no'] ?? '')) : '';
            $resolvedPageName = is_array($entry) ? trim((string) ($entry['page_name'] ?? '')) : '';
            $linkKeyText = trim((string) $linkKey);

            if ($resolvedPageNo === '') {
                if ($linkKeyText !== '' && ctype_digit($linkKeyText)) {
                    $resolvedPageNo = $linkKeyText;
                } elseif ($linkKeyText !== '' && isset($pageNoByName[strtolower($linkKeyText)])) {
                    $resolvedPageNo = $pageNoByName[strtolower($linkKeyText)];
                }
            }
            if ($resolvedPageNo === '') {
                $resolvedPageNo = '1';
            }

            $paramKey = $this->pageParameterLinkKey($resolvedPageNo, $parameterName);
            if (isset($linkedParameters[$paramKey])) {
                continue;
            }

            if ($resolvedPageName === '') {
                if (isset($pageNames[$resolvedPageNo]) && trim((string) $pageNames[$resolvedPageNo]) !== '') {
                    $resolvedPageName = trim((string) $pageNames[$resolvedPageNo]);
                } elseif ($linkKeyText !== '' && !ctype_digit($linkKeyText)) {
                    $resolvedPageName = $linkKeyText;
                } else {
                    $resolvedPageName = 'Page ' . $resolvedPageNo;
                }
            }

            $pageNames[$resolvedPageNo] = $resolvedPageName;
            $pageNoByName[strtolower($resolvedPageName)] = $resolvedPageNo;
            $linkedParameters[$paramKey] = true;

            $rows[] = [
                'page_no' => $resolvedPageNo,
                'page_name' => $resolvedPageName,
                'parameter' => $parameterName,
                'sequence' => is_array($entry) ? (int) ($entry['sequence'] ?? ($index + 1)) : ($index + 1),
                'api' => $this->resolveDeviceUiApiMetadata($apiByParameter, $parameterName),
            ];
        }
    }

    if (!$hasSavedMappingRecord) {
        // Before the first save, old models still store their pages on product_api.
        // After a save, api_page_linking_data is the list the user kept. Do not
        // merge those legacy rows back in, or a removed page returns on refresh.
        $this->mergeProductApiPageRows($rows, $pageNames, $pageNoByName, $apiRows, $apiByParameter, $vendorPageMap);
        $this->mergeV2ApiPageRows($rows, $pageNames, $pageNoByName, $modelno, $apiByParameter, $vendorPageMap);
    }

    $this->fillMissingDeviceUiApiMetadataForRows($modelno, $apiByParameter, $rows);

    if (!empty($uiRows)) {
        $rowsByKey = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $key = $this->pageParameterLinkKey(
                trim((string) ($row['page_no'] ?? '')),
                trim((string) ($row['parameter'] ?? ''))
            );
            if ($key !== '' && !isset($rowsByKey[$key])) {
                $rowsByKey[$key] = $row;
            }
        }

        $orderedRows = [];
        $usedKeys = [];
        $sequence = 1;
        foreach ($uiRows as $uiRow) {
            if (!is_array($uiRow)) {
                continue;
            }
            $pageNo = trim((string) ($uiRow['page_no'] ?? ''));
            $parameters = [];
            if (!empty($uiRow['parameters']) && is_array($uiRow['parameters'])) {
                $parameters = $uiRow['parameters'];
            } elseif (!empty($uiRow['parameter'])) {
                $parameters = [(string) $uiRow['parameter']];
            }
            foreach ($parameters as $parameterName) {
                $parameterName = trim((string) $parameterName);
                if ($parameterName === '') {
                    continue;
                }
                $key = $this->pageParameterLinkKey($pageNo, $parameterName);
                if ($key === '' || !isset($rowsByKey[$key]) || isset($usedKeys[$key])) {
                    continue;
                }
                $orderedRow = $rowsByKey[$key];
                $orderedRow['sequence'] = $sequence++;
                $orderedRows[] = $orderedRow;
                $usedKeys[$key] = true;
            }
        }
        if (!$hasSavedMappingRecord) {
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $key = $this->pageParameterLinkKey(
                    trim((string) ($row['page_no'] ?? '')),
                    trim((string) ($row['parameter'] ?? ''))
                );
                if ($key === '' || isset($usedKeys[$key])) {
                    continue;
                }
                $row['sequence'] = $sequence++;
                $orderedRows[] = $row;
                $usedKeys[$key] = true;
            }
        }
        if (!empty($orderedRows)) {
            $rows = $orderedRows;
        }
    }

    usort($rows, function ($a, $b) {
        $pageCompare = ((int) $a['page_no']) <=> ((int) $b['page_no']);

        if ($pageCompare !== 0) {
            return $pageCompare;
        }

        return ((int) $a['sequence']) <=> ((int) $b['sequence']);
    });

    if (!empty($pageNames)) {
        uksort($pageNames, function ($a, $b) {
            return ((int) $a) <=> ((int) $b);
        });
    }

    if (!$hasSavedMappingRecord) {
        $this->seedLockedUiRowsFromLoadedProductApi($apiRows, $rows, $pageNames, $uiRows);
    }

    if (!empty($pageNames)) {
        uksort($pageNames, function ($a, $b) {
            return ((int) $a) <=> ((int) $b);
        });
    }

    $record['page_names_array'] = $pageNames;
    $record['linking_data_array'] = $linkingData;
    $record['ui_rows'] = $uiRows;
    $record['rows'] = $rows;

    $this->pageParameterMappingCache[$modelno] = $record;
    $this->pageParameterMappingBuilding[$modelno] = false;

    return $record;
}

/**
 * Single-row View UI must use the same hydrated API metadata as Save & View Linked UI.
 * The modal only sends parameter names in the query string; without this, Studio
 * renders empty "--" cards instead of switches, text fields, and buttons.
 */
public function buildSinglePagePreviewMapping($modelno, $pageNo, $pageName, array $parameters, $sequence = 1)
{
    $full = $this->getPageParameterMapping($modelno);
    if (!is_array($full)) {
        $full = ['rows' => [], 'page_names_array' => []];
    }

    $pageNo = trim((string) $pageNo);
    $pageName = trim((string) $pageName);
    $pageNo = $pageNo !== '' ? $pageNo : '1';
    $pageName = $pageName !== '' ? $pageName : ('Page ' . $pageNo);
    $sequence = (int) $sequence;
    $sequence = $sequence > 0 ? $sequence : 1;

    $existingOnPage = [];
    $existingAny = [];
    foreach ($full['rows'] ?? [] as $row) {
        if (!is_array($row)) {
            continue;
        }
        $parameterName = trim((string) ($row['parameter'] ?? ''));
        if ($parameterName === '') {
            continue;
        }
        $key = strtolower($parameterName);
        if (!isset($existingAny[$key])) {
            $existingAny[$key] = $row;
        }
        if (trim((string) ($row['page_no'] ?? '')) === $pageNo && !isset($existingOnPage[$key])) {
            $existingOnPage[$key] = $row;
        }
    }

    $rows = [];
    foreach ($parameters as $index => $parameter) {
        $parameter = trim((string) $parameter);
        if ($parameter === '') {
            continue;
        }
        $key = strtolower($parameter);
        $source = $existingOnPage[$key] ?? ($existingAny[$key] ?? null);
        if (is_array($source)) {
            $row = $source;
            $row['parameter'] = $parameter;
            $row['page_no'] = $pageNo;
            $row['page_name'] = $pageName;
            $row['sequence'] = $sequence + (int) $index;
            $rows[] = $row;
            continue;
        }
        $rows[] = [
            'page_no' => $pageNo,
            'page_name' => $pageName,
            'parameter' => $parameter,
            'sequence' => $sequence + (int) $index,
            'api' => [],
        ];
    }

    $apiByParameter = $this->loadDeviceUiApiMetadataByParameter($modelno);
    foreach ($rows as &$row) {
        if (!is_array($row)) {
            continue;
        }
        if (!empty($row['api']) && is_array($row['api'])) {
            continue;
        }
        $row['api'] = $this->resolveDeviceUiApiMetadata($apiByParameter, $row['parameter'] ?? '');
    }
    unset($row);
    $this->fillMissingDeviceUiApiMetadataForRows($modelno, $apiByParameter, $rows);

    return [
        'model_name' => $modelno,
        'rows' => $rows,
        'page_names_array' => [$pageNo => $pageName],
        'ui_rows' => [],
        'linking_data_array' => $full['linking_data_array'] ?? [],
    ];
}

private function loadDeviceUiApiMetadataByParameter($modelno, ?array $apiRows = null)
{
    if ($apiRows === null) {
        $apiRows = $this->db->table('product_api')
            ->where('model_name', $modelno)
            ->get()
            ->getResultArray();
    }

    $apiByParameter = [];

    foreach ($apiRows as $apiRow) {
        $parameterName = trim((string) ($apiRow['parameter_name'] ?? ''));
        if ($parameterName !== '') {
            $this->indexDeviceUiApiMetadata($apiByParameter, $parameterName, $this->buildDeviceUiApiMetadata($apiRow));
        }
    }

    $relatedModels = $this->deviceUiRelatedModelNames($modelno);
    if (count($relatedModels) > 1) {
        $relatedRows = $this->db->table('product_api')
            ->whereIn('model_name', $relatedModels)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
        foreach ($relatedRows as $apiRow) {
            $parameterName = trim((string) ($apiRow['parameter_name'] ?? ''));
            if ($parameterName === '' || !empty($this->resolveDeviceUiApiMetadata($apiByParameter, $parameterName))) {
                continue;
            }
            $this->indexDeviceUiApiMetadata($apiByParameter, $parameterName, $this->buildDeviceUiApiMetadata($apiRow));
        }
    }

    $configQuery = $this->db->table('api_product_configs')
        ->select('id, parameter_name, model_name')
        ->where('parameter_name !=', '');
    if (!empty($relatedModels)) {
        $configQuery->whereIn('model_name', $relatedModels);
    } else {
        $configQuery->where('model_name', $modelno);
    }
    $configs = $configQuery
        ->orderBy('model_name = ' . $this->db->escape($modelno), 'DESC', false)
        ->orderBy('id', 'DESC')
        ->get()
        ->getResultArray();

    foreach ($configs as $config) {
        $parameterName = trim((string) ($config['parameter_name'] ?? ''));
        if ($parameterName === '') {
            continue;
        }

        $metadata = [];
        try {
            $metadata = $this->buildDeviceUiApiMetadataFromTree((int) ($config['id'] ?? 0));
        } catch (\Throwable $e) {
            log_message('error', 'Failed to build V2 UI metadata for config ' . ($config['id'] ?? '') . ': ' . $e->getMessage());
        }

        if (empty($metadata)) {
            continue;
        }
        $sameModel = strcasecmp((string) ($config['model_name'] ?? ''), (string) $modelno) === 0;
        if ($sameModel || empty($this->resolveDeviceUiApiMetadata($apiByParameter, $parameterName))) {
            $this->indexDeviceUiApiMetadata($apiByParameter, $parameterName, $metadata);
        }
    }

    return $apiByParameter;
}

private function fillMissingDeviceUiApiMetadataForRows($modelno, array &$apiByParameter, array &$rows)
{
    $missing = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $parameterName = trim((string) ($row['parameter'] ?? ''));
        if ($parameterName === '' || !empty($row['api'])) {
            continue;
        }
        if (empty($this->resolveDeviceUiApiMetadata($apiByParameter, $parameterName))) {
            $missing[$parameterName] = true;
        }
    }

    if (empty($missing)) {
        return;
    }

    $parameters = array_keys($missing);
    $configs = $this->db->table('api_product_configs')
        ->select('id, parameter_name, model_name')
        ->whereIn('parameter_name', $parameters)
        ->orderBy('model_name = ' . $this->db->escape($modelno), 'DESC', false)
        ->orderBy('id', 'DESC')
        ->get()
        ->getResultArray();

    $loaded = [];
    foreach ($configs as $config) {
        $parameterName = trim((string) ($config['parameter_name'] ?? ''));
        if ($parameterName === '' || isset($loaded[strtolower($parameterName)])) {
            continue;
        }
        try {
            $metadata = $this->buildDeviceUiApiMetadataFromTree((int) ($config['id'] ?? 0));
        } catch (\Throwable $e) {
            log_message('error', 'Failed to build fallback V2 UI metadata for config ' . ($config['id'] ?? '') . ': ' . $e->getMessage());
            $metadata = [];
        }
        if (!empty($metadata)) {
            $this->indexDeviceUiApiMetadata($apiByParameter, $parameterName, $metadata);
            $loaded[strtolower($parameterName)] = true;
        }
    }

    $stillMissing = [];
    foreach ($parameters as $parameterName) {
        if (!isset($loaded[strtolower($parameterName)])) {
            $stillMissing[] = $parameterName;
        }
    }
    if (!empty($stillMissing)) {
        $relatedModels = $this->deviceUiRelatedModelNames($modelno);
        $legacyQuery = $this->db->table('product_api')->whereIn('parameter_name', $stillMissing);
        if (!empty($relatedModels)) {
            $legacyQuery->whereIn('model_name', $relatedModels);
        }
        $legacyRows = $legacyQuery
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
        usort($legacyRows, static function ($a, $b) use ($modelno) {
            $aExact = strcasecmp((string) ($a['model_name'] ?? ''), (string) $modelno) === 0 ? 0 : 1;
            $bExact = strcasecmp((string) ($b['model_name'] ?? ''), (string) $modelno) === 0 ? 0 : 1;
            if ($aExact !== $bExact) {
                return $aExact <=> $bExact;
            }
            return ((int) ($b['id'] ?? 0)) <=> ((int) ($a['id'] ?? 0));
        });
        foreach ($legacyRows as $apiRow) {
            $parameterName = trim((string) ($apiRow['parameter_name'] ?? ''));
            if ($parameterName === '' || isset($loaded[strtolower($parameterName)])) {
                continue;
            }
            $metadata = $this->buildDeviceUiApiMetadata($apiRow);
            if (empty($metadata)) {
                continue;
            }
            $this->indexDeviceUiApiMetadata($apiByParameter, $parameterName, $metadata);
            $loaded[strtolower($parameterName)] = true;
        }
    }

    foreach ($rows as $index => $row) {
        if (!is_array($row) || !empty($row['api'])) {
            continue;
        }
        $parameterName = trim((string) ($row['parameter'] ?? ''));
        if ($parameterName === '') {
            continue;
        }
        $metadata = $this->resolveDeviceUiApiMetadata($apiByParameter, $parameterName);
        if (!empty($metadata)) {
            $rows[$index]['api'] = $metadata;
        }
    }
}

private function deviceUiRelatedModelNames($modelno)
{
    $modelno = trim((string) $modelno);
    $names = [];
    if ($modelno !== '') {
        $names[] = $modelno;
    }
    if ($modelno !== '' && preg_match('/^(.*)[._-](\d+)$/', $modelno, $match) && trim((string) $match[1]) !== '') {
        $names[] = trim((string) $match[1]);
    }
    return array_values(array_unique($names));
}

private function deviceUiLookupKeys($name)
{
    $raw = strtolower(trim((string) $name));
    if ($raw === '') {
        return [];
    }

    $spaced = trim((string) preg_replace('/\s+/', ' ', (string) preg_replace('/[^a-z0-9]+/', ' ', $raw)));
    $compact = preg_replace('/[^a-z0-9]+/', '', $raw);
    return array_values(array_unique(array_filter([$raw, $spaced, $compact], static function ($key) {
        return $key !== '';
    })));
}

private function indexDeviceUiApiMetadata(array &$apiByParameter, $parameterName, array $metadata)
{
    $names = [
        $parameterName,
        $metadata['parameter_name'] ?? '',
        $metadata['frame']['name'] ?? '',
    ];

    $frame = is_array($metadata['frame'] ?? null) ? $metadata['frame'] : [];
    foreach (['commands', 'response', 'error'] as $group) {
        $fields = is_array($frame[$group] ?? null) ? $frame[$group] : [];
        foreach ($fields as $field) {
            if (!is_array($field) || !$this->isDeviceUiTypeUsable($field['ui_type'] ?? null)) {
                continue;
            }

            $names[] = $field['default_name'] ?? '';
            $description = trim((string) ($field['Field_description'] ?? $field['field_description'] ?? ''));
            $descriptionKey = strtolower($description);
            if ($description !== '' && !in_array($descriptionKey, ['data', 'trigger', 'commandid', 'command id', 'function code', 'functioncode', 'actionid', 'action id', 'terminator', 'header'], true)) {
                $names[] = $description;
            }
        }
    }

    foreach ($names as $name) {
        foreach ($this->deviceUiLookupKeys($name) as $key) {
            $apiByParameter[$key] = $metadata;
        }
    }
}

private function resolveDeviceUiApiMetadata(array $apiByParameter, $parameterName)
{
    foreach ($this->deviceUiLookupKeys($parameterName) as $key) {
        if (isset($apiByParameter[$key]) && is_array($apiByParameter[$key])) {
            return $apiByParameter[$key];
        }
    }

    return [];
}

private function buildDeviceUiApiMetadataFromTree($configId)
{
    $configId = (int) $configId;
    if ($configId <= 0) {
        return [];
    }

    $tree = $this->getApiTreeConfigFull($configId);
    if (!$tree) {
        return [];
    }

    $parameterName = trim((string) ($tree['config']['parameter_name'] ?? ''));
    $pseudoRow = [
        'parameter_name' => $parameterName,
        'cmd_fieldDescription' => [],
        'cmd_uiType' => [],
        'cmd_defaultvalue' => [],
        'cmd_displayValueMin' => [],
        'cmd_displayValueMax' => [],
        'cmd_sliderStepValue' => [],
        'cmd_uiDescriptionInOut' => [],
        'cmd_asperui' => [],
        'cmd_asperui_values' => [],
        'cmd_sno_asperui' => [],
        'cmd_startBytePosition' => [],
        'cmd_numberOfBytes' => [],
        'cmd_dataType' => [],
        'cmd_valueDefinition' => [],
        'cmd_uiId' => [],
        'cmd_fieldLevel' => [],
        'cmd_parentFieldId' => [],
        'cmd_fieldId' => [],
        'cmd_bitwiseMode' => [],
        'cmd_startBit' => [],
        'cmd_endBit' => [],
        'cmd_multipleui' => [],
        'cmd_triggerui' => [],
        'resp_fieldDescription' => [],
        'resp_uiType' => [],
        'resp_defaultvalue' => [],
        'resp_displayValueMin' => [],
        'resp_displayValueMax' => [],
        'resp_sliderStepValue' => [],
        'resp_uiDescriptionInOut' => [],
        'resp_asperui' => [],
        'resp_asperui_values' => [],
        'resp_sno_asperui' => [],
        'resp_startBytePosition' => [],
        'resp_numberOfBytes' => [],
        'resp_dataType' => [],
        'resp_valueDefinition' => [],
        'resp_uiId' => [],
        'resp_fieldLevel' => [],
        'resp_parentFieldId' => [],
        'resp_fieldId' => [],
        'resp_bitwiseMode' => [],
        'resp_startBit' => [],
        'resp_endBit' => [],
        'resp_multipleui' => [],
        'resp_triggerui' => [],
        'error_fieldDescription' => [],
        'error_uiType' => [],
        'error_defaultvalue' => [],
        'error_displayValueMin' => [],
        'error_displayValueMax' => [],
        'error_sliderStepValue' => [],
        'error_uiDescriptionInOut' => [],
        'error_asperui' => [],
        'error_asperui_values' => [],
        'error_sno_asperui' => [],
        'error_startBytePosition' => [],
        'error_numberOfBytes' => [],
        'error_dataType' => [],
        'error_valueDefinition' => [],
        'error_uiId' => [],
        'error_fieldLevel' => [],
        'error_parentFieldId' => [],
        'error_fieldId' => [],
        'error_bitwiseMode' => [],
        'error_startBit' => [],
        'error_endBit' => [],
        'error_multipleui' => [],
        'error_triggerui' => [],
    ];

    $prefixMap = [
        'command' => 'cmd',
        'response' => 'resp',
        'error' => 'error',
    ];

    foreach ($tree['frames'] as $frame) {
        $group = $frame['frame_group'] ?? '';
        if (!isset($prefixMap[$group])) {
            continue;
        }

        $prefix = $prefixMap[$group];
        $frameId = (int) ($frame['id'] ?? 0);
        $frameFields = $tree['fields_by_frame'][$frameId] ?? [];

        if ($group === 'command') {
            $pseudoRow['cmd_multipleui'] = $this->normalizeDeviceUiFlagList($frame['multiple_ui'] ?? null, 3);
            $pseudoRow['cmd_triggerui'] = $this->normalizeDeviceUiFlagList($frame['trigger_ui'] ?? null, 3);
            if ($parameterName === '' && !empty($frame['parameter_name'])) {
                $parameterName = trim((string) $frame['parameter_name']);
                $pseudoRow['parameter_name'] = $parameterName;
            }
        } elseif ($group === 'response') {
            $pseudoRow['resp_multipleui'] = $this->normalizeDeviceUiFlagList($frame['multiple_ui'] ?? null, 3);
            $pseudoRow['resp_triggerui'] = $this->normalizeDeviceUiFlagList($frame['trigger_ui'] ?? null, 3);
        } elseif ($group === 'error') {
            $pseudoRow['error_multipleui'] = $this->normalizeDeviceUiFlagList($frame['multiple_ui'] ?? null, 3);
            $pseudoRow['error_triggerui'] = $this->normalizeDeviceUiFlagList($frame['trigger_ui'] ?? null, 3);
        }

        foreach ($frameFields as $field) {
            $this->appendDeviceUiTreeFieldToPseudoRow($pseudoRow, $prefix, $field);
        }
    }

    if (empty($pseudoRow['cmd_fieldDescription']) && empty($pseudoRow['resp_fieldDescription'])) {
        return [];
    }

    return $this->buildDeviceUiApiMetadata($pseudoRow);
}

private function appendDeviceUiTreeFieldToPseudoRow(array &$pseudoRow, $prefix, array $field)
{
    $uiType = trim((string) ($field['ui_type'] ?? ''));
    $fieldDescription = trim((string) ($field['field_description'] ?? ''));
    $level = strtolower(trim((string) ($field['field_level'] ?? 'bytewise')));
    if ($level !== 'bitwise') {
        $level = 'bytewise';
    }
    if ($fieldDescription === '' && $level === 'bitwise') {
        $startBit = trim((string) ($field['start_bit'] ?? ''));
        $fieldDescription = $startBit !== '' ? ('Bit ' . $startBit) : 'Bitwise field';
    }
    if ($uiType === '' && $fieldDescription === '') {
        return;
    }

    $pseudoRow[$prefix . '_fieldDescription'][] = $fieldDescription;
    $pseudoRow[$prefix . '_uiType'][] = $uiType;
    $pseudoRow[$prefix . '_defaultvalue'][] = $field['default_value'] ?? null;
    $pseudoRow[$prefix . '_displayValueMin'][] = $field['display_value_min'] ?? null;
    $pseudoRow[$prefix . '_displayValueMax'][] = $field['display_value_max'] ?? null;
    $pseudoRow[$prefix . '_sliderStepValue'][] = $field['slider_step_value'] ?? null;
    $pseudoRow[$prefix . '_uiDescriptionInOut'][] = $field['ui_description_in_out'] ?? null;
    $pseudoRow[$prefix . '_asperui'][] = $field['as_per_ui'] ?? '';
    $pseudoRow[$prefix . '_asperui_values'][] = $field['ui_values'] ?? '';
    $pseudoRow[$prefix . '_sno_asperui'][] = $field['sno_as_per_ui'] ?? ($field['sno_asperui'] ?? '');
    $pseudoRow[$prefix . '_startBytePosition'][] = $field['start_byte_position'] ?? ($field['byte_start_position'] ?? ($field['start_byte'] ?? null));
    $pseudoRow[$prefix . '_numberOfBytes'][] = $field['number_of_bytes'] ?? ($field['no_of_bytes'] ?? null);
    $pseudoRow[$prefix . '_dataType'][] = $field['data_type'] ?? null;
    $pseudoRow[$prefix . '_valueDefinition'][] = $field['value_definition'] ?? ($field['type'] ?? null);
    $pseudoRow[$prefix . '_uiId'][] = $field['ui_id'] ?? ($field['ui_ids'] ?? null);
    $pseudoRow[$prefix . '_fieldLevel'][] = $level;
    $pseudoRow[$prefix . '_parentFieldId'][] = (int) ($field['parent_field_id'] ?? 0);
    $pseudoRow[$prefix . '_fieldId'][] = (int) ($field['id'] ?? 0);
    $pseudoRow[$prefix . '_bitwiseMode'][] = trim((string) ($field['bitwise_mode'] ?? ($level === 'bitwise' ? 'Bitwise' : 'Bytewise')));
    $pseudoRow[$prefix . '_startBit'][] = $field['start_bit'] ?? null;
    $pseudoRow[$prefix . '_endBit'][] = $field['end_bit'] ?? null;
}

private function getVendorPageNumberMap($modelno, $itemId = 0)
{
    $map = [];
    $vendorProductId = (int) $itemId;

    if ($vendorProductId <= 0) {
        $legacyRow = $this->db->table('product_api')
            ->select('itemid')
            ->where('model_name', $modelno)
            ->where('itemid >', 0)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        if (!empty($legacyRow['itemid'])) {
            $vendorProductId = (int) $legacyRow['itemid'];
        }
    }

    if ($vendorProductId <= 0) {
        $configRow = $this->db->table('api_product_configs')
            ->select('item_id')
            ->where('model_name', $modelno)
            ->where('item_id >', 0)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        if (!empty($configRow['item_id'])) {
            $vendorProductId = (int) $configRow['item_id'];
        }
    }

    if ($vendorProductId <= 0) {
        return $map;
    }

    $pages = $this->db->table('vendorproduct_pages')
        ->select('full_page_name, sequence_no')
        ->where('vendorproduct_id', $vendorProductId)
        ->orderBy('sequence_no', 'ASC')
        ->orderBy('id', 'ASC')
        ->get()
        ->getResultArray();

    foreach ($pages as $page) {
        $pageName = trim((string) ($page['full_page_name'] ?? ''));
        $pageNo = trim((string) ($page['sequence_no'] ?? ''));
        if ($pageName === '' || $pageNo === '') {
            continue;
        }

        $map[$pageName] = $pageNo;
        $map[strtolower($pageName)] = $pageNo;
    }

    return $map;
}

private function getVendorPageNameByNumber($pageNo, array $vendorPageMap)
{
    $pageNo = trim((string) $pageNo);
    if ($pageNo === '') {
        return '';
    }

    $fallback = '';
    foreach ($vendorPageMap as $pageName => $mappedNo) {
        if ((string) $mappedNo !== $pageNo || !is_string($pageName)) {
            continue;
        }

        if ($pageName !== strtolower($pageName)) {
            return $pageName;
        }

        if ($fallback === '') {
            $fallback = $pageName;
        }
    }

    return $fallback;
}

private function resolveVendorPageNumber($pageName, array &$pageNames, array &$pageNoByName, array $vendorPageMap, array $pageNoByLegacyName)
{
    $pageName = trim((string) $pageName);
    if ($pageName === '') {
        return '1';
    }

    if (isset($pageNoByLegacyName[$pageName])) {
        return (string) $pageNoByLegacyName[$pageName];
    }

    if (isset($vendorPageMap[$pageName])) {
        return (string) $vendorPageMap[$pageName];
    }

    if (isset($vendorPageMap[strtolower($pageName)])) {
        return (string) $vendorPageMap[strtolower($pageName)];
    }

    $nextNo = 1;
    if (!empty($pageNames)) {
        $numericNos = array_map('intval', array_keys($pageNames));
        $nextNo = max($numericNos) + 1;
    }

    $pageNoByLegacyName[$pageName] = (string) $nextNo;
    return (string) $nextNo;
}

private function mergeProductApiPageRows(array &$rows, array &$pageNames, array &$pageNoByName, array $apiRows, array $apiByParameter, array $vendorPageMap = [])
{
    $linkedParameters = [];
    foreach ($rows as $row) {
        $paramKey = $this->pageParameterLinkKey($row['page_no'] ?? '1', $row['parameter'] ?? '');
        if ($paramKey !== ':') {
            $linkedParameters[$paramKey] = true;
        }
    }

    $pageNoByLegacyName = [];
    foreach ($pageNames as $no => $name) {
        $legacyName = trim((string) $name);
        if ($legacyName !== '') {
            $pageNoByLegacyName[$legacyName] = (string) $no;
        }
    }

    foreach ($apiRows as $apiRow) {
        $parameterName = trim((string) ($apiRow['parameter_name'] ?? ''));
        if ($parameterName === '') {
            continue;
        }

        $pageNamesForRow = $this->splitLegacyPageNames($apiRow['page_names'] ?? '');
        if (empty($pageNamesForRow)) {
            continue;
        }

        foreach ($pageNamesForRow as $pageName) {
            $legacyKey = strtolower($pageName);
            if (!isset($pageNoByLegacyName[$pageName]) && !isset($pageNoByLegacyName[$legacyKey])) {
                $pageNo = $this->resolveVendorPageNumber($pageName, $pageNames, $pageNoByName, $vendorPageMap, $pageNoByLegacyName);
                $pageNoByLegacyName[$pageName] = $pageNo;
                $pageNoByLegacyName[$legacyKey] = $pageNo;
            }

            $pageNo = $pageNoByLegacyName[$pageName] ?? $pageNoByLegacyName[$legacyKey] ?? '1';
            $paramKey = $this->pageParameterLinkKey($pageNo, $parameterName);
            if (isset($linkedParameters[$paramKey])) {
                continue;
            }

            $pageNames[$pageNo] = $pageName;
            $pageNoByName[strtolower($pageName)] = $pageNo;
            $linkedParameters[$paramKey] = true;

            $sequence = 1;
            foreach ($rows as $existingRow) {
                if ((string) ($existingRow['page_no'] ?? '') === (string) $pageNo) {
                    $sequence++;
                }
            }

            $rows[] = [
                'page_no' => $pageNo,
                'page_name' => $pageName,
                'parameter' => $parameterName,
                'sequence' => $sequence,
                'api' => $this->resolveDeviceUiApiMetadata($apiByParameter, $parameterName),
            ];
        }
    }
}

private function mergeV2ApiPageRows(array &$rows, array &$pageNames, array &$pageNoByName, $modelno, array $apiByParameter, array $vendorPageMap = [])
{
    $linkedParameters = [];
    foreach ($rows as $row) {
        $paramKey = $this->pageParameterLinkKey($row['page_no'] ?? '1', $row['parameter'] ?? '');
        if ($paramKey !== ':') {
            $linkedParameters[$paramKey] = true;
        }
    }

    $configs = $this->db->table('api_product_configs')
        ->select('parameter_name, page_number, sequence_no')
        ->where('model_name', $modelno)
        ->where('parameter_name !=', '')
        ->orderBy('id', 'DESC')
        ->get()
        ->getResultArray();

    foreach ($configs as $config) {
        $parameterName = trim((string) ($config['parameter_name'] ?? ''));
        if ($parameterName === '') {
            continue;
        }

        $pageNo = trim((string) ($config['page_number'] ?? ''));
        if ($pageNo === '') {
            $pageNo = '1';
        }

        $paramKey = $this->pageParameterLinkKey($pageNo, $parameterName);
        if (isset($linkedParameters[$paramKey])) {
            continue;
        }

        $pageName = isset($pageNames[$pageNo]) && trim((string) $pageNames[$pageNo]) !== ''
            ? trim((string) $pageNames[$pageNo])
            : $this->getVendorPageNameByNumber($pageNo, $vendorPageMap);

        if ($pageName === '') {
            $pageName = 'Page ' . $pageNo;
        }

        $pageNames[$pageNo] = $pageName;
        $pageNoByName[strtolower($pageName)] = $pageNo;
        $linkedParameters[$paramKey] = true;

        $sequence = (int) ($config['sequence_no'] ?? 0);
        if ($sequence <= 0) {
            $sequence = 1;
            foreach ($rows as $existingRow) {
                if ((string) ($existingRow['page_no'] ?? '') === $pageNo) {
                    $sequence++;
                }
            }
        }

        $rows[] = [
            'page_no' => $pageNo,
            'page_name' => $pageName,
            'parameter' => $parameterName,
            'sequence' => $sequence,
            'api' => $this->resolveDeviceUiApiMetadata($apiByParameter, $parameterName),
        ];
    }
}

private function buildDeviceUiApiMetadata(array $row)
{
    $fieldDescriptions = $this->deviceUiApiList($this->deviceUiApiValue($row, ['cmd_fieldDescription', 'cmd_fielddescription', 'cmd_field_description']));
    $uiTypes = $this->deviceUiApiList($this->deviceUiApiValue($row, ['cmd_uiType', 'cmd_uitype', 'cmd_ui_type']));
    $defaults = $this->deviceUiApiList($this->deviceUiApiValue($row, ['cmd_defaultvalue']));
    $displayMins = $this->deviceUiApiList($this->deviceUiApiValue($row, ['cmd_displayValueMin', 'cmd_displayvaluemin']));
    $displayMaxs = $this->deviceUiApiList($this->deviceUiApiValue($row, ['cmd_displayValueMax', 'cmd_displayvaluemax']));
    $steps = $this->deviceUiApiList($this->deviceUiApiValue($row, ['cmd_sliderStepValue', 'cmd_sliderstepvalue']));
    $accessModes = $this->deviceUiApiList($this->deviceUiApiValue($row, ['cmd_uiDescriptionInOut', 'cmd_uidescriptioninout']));
    $items = $this->deviceUiApiList($this->deviceUiApiValue($row, ['cmd_asperui']));
    $itemValues = $this->deviceUiApiList($this->deviceUiApiValue($row, ['cmd_asperui_values']));
    $snoItems = $this->deviceUiApiList($this->deviceUiApiValue($row, ['cmd_sno_asperui', 'cmd_snoasperui']));
    $dataIndex = null;
    $commandId = null;
    $functionCode = null;
    $actionId = null;
    $uiTypeSource = 'command';

    $hexMins = $this->deviceUiApiList($this->deviceUiApiValue($row, [
        'cmd_valueInHexMin',
        'cmd_valueinhexmin',
        'cmd_value_in_hex_min',
    ]));
    foreach ($fieldDescriptions as $index => $description) {
        $normalized = strtolower(trim((string) $description));
        // Prefer protocol hex (cmd_valueinhexmin) over cmd_defaultvalue labels / stubs.
        if ($normalized === 'commandid' || $normalized === 'command id') {
            $commandId = $this->deviceUiFirstNonEmpty($hexMins[$index] ?? null, $defaults[$index] ?? null);
        } elseif ($normalized === 'function code' || $normalized === 'functioncode') {
            $functionCode = $this->deviceUiFirstNonEmpty($hexMins[$index] ?? null, $defaults[$index] ?? null);
        } elseif ($normalized === 'actionid' || $normalized === 'action id') {
            $actionId = $this->deviceUiFirstNonEmpty($hexMins[$index] ?? null, $defaults[$index] ?? null);
        } elseif ($dataIndex === null && ($normalized === 'data' || $normalized === 'trigger')) {
            $dataIndex = $index;
        }
    }

    if ($dataIndex === null) {
        $dataIndex = $this->pickDeviceUiControlIndex($fieldDescriptions, $uiTypes);
    } elseif (!$this->isDeviceUiTypeUsable($uiTypes[$dataIndex] ?? null)) {
        $pickedCommandIndex = $this->pickDeviceUiControlIndex($fieldDescriptions, $uiTypes);
        if ($pickedCommandIndex !== null) {
            $dataIndex = $pickedCommandIndex;
        }
    }

    $uiType = $dataIndex !== null ? trim((string) ($uiTypes[$dataIndex] ?? '')) : '';

    if (!$this->isDeviceUiTypeUsable($uiType)) {
        foreach (['resp' => 'response', 'error' => 'error'] as $prefix => $sourceLabel) {
            $fallbackDescriptions = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_fieldDescription', $prefix . '_fielddescription', $prefix . '_field_description']));
            $fallbackUiTypes = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_uiType', $prefix . '_uitype', $prefix . '_ui_type']));
            $fallbackIndex = null;
            foreach ($fallbackDescriptions as $index => $description) {
                $normalized = strtolower(trim((string) $description));
                if ($fallbackIndex === null && in_array($normalized, ['data', 'trigger'], true) && $this->isDeviceUiTypeUsable($fallbackUiTypes[$index] ?? null)) {
                    $fallbackIndex = $index;
                    break;
                }
            }
            if ($fallbackIndex === null) {
                $fallbackIndex = $this->pickDeviceUiControlIndex($fallbackDescriptions, $fallbackUiTypes);
            }
            $fallbackUiType = $fallbackIndex !== null ? trim((string) ($fallbackUiTypes[$fallbackIndex] ?? '')) : '';
            if (!$this->isDeviceUiTypeUsable($fallbackUiType)) {
                continue;
            }

            $fieldDescriptions = $fallbackDescriptions;
            $uiTypes = $fallbackUiTypes;
            $defaults = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_defaultvalue']));
            $displayMins = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_displayValueMin', $prefix . '_displayvaluemin']));
            $displayMaxs = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_displayValueMax', $prefix . '_displayvaluemax']));
            $steps = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_sliderStepValue', $prefix . '_sliderstepvalue']));
            $accessModes = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_uiDescriptionInOut', $prefix . '_uidescriptioninout']));
            $items = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_asperui']));
            $itemValues = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_asperui_values', $prefix . '_asperuivalues']));
            $snoItems = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_sno_asperui', $prefix . '_snoasperui']));
            $dataIndex = $fallbackIndex;
            $uiType = $fallbackUiType;
            $uiTypeSource = $sourceLabel;
            break;
        }
    }

    $parameterName = trim((string) ($row['parameter_name'] ?? $row['parametername'] ?? ''));
    $frameName = $parameterName !== '' ? $parameterName : 'frame';
    $commands = $this->buildDeviceUiFrameFieldList($row, 'cmd');
    $response = $this->buildDeviceUiFrameFieldList($row, 'resp');
    $error = $this->buildDeviceUiFrameFieldList($row, 'error');

    if ($uiTypeSource === 'command' && $dataIndex !== null && !empty($commands[$dataIndex])) {
        if (empty($commands[$dataIndex]['asperui']) && !empty($items)) {
            $commands[$dataIndex]['asperui'] = array_values(array_filter($items, static function ($value) {
                return $value !== '';
            }));
        }
        if (empty($commands[$dataIndex]['asperui_values']) && !empty($itemValues)) {
            $commands[$dataIndex]['asperui_values'] = array_values(array_filter($itemValues, static function ($value) {
                return $value !== '';
            }));
        }
        if (empty($commands[$dataIndex]['sno_asperui']) && !empty($snoItems)) {
            $commands[$dataIndex]['sno_asperui'] = array_values(array_filter($snoItems, static function ($value) {
                return $value !== '';
            }));
        }
    }

    return [
        'parameter_name' => $parameterName,
        'ui_type' => $uiType,
        'default' => $dataIndex !== null ? ($defaults[$dataIndex] ?? null) : null,
        'items' => array_values(array_filter($items, static function ($value) { return $value !== ''; })),
        'item_values' => array_values(array_filter($itemValues, static function ($value) { return $value !== ''; })),
        'sno_asperui' => array_values(array_filter($snoItems, static function ($value) { return $value !== ''; })),
        'minimum' => $dataIndex !== null ? ($displayMins[$dataIndex] ?? null) : null,
        'maximum' => $dataIndex !== null ? ($displayMaxs[$dataIndex] ?? null) : null,
        'step' => $dataIndex !== null ? ($steps[$dataIndex] ?? null) : null,
        'access' => $dataIndex !== null ? ($accessModes[$dataIndex] ?? null) : null,
        'command_id' => $commandId,
        'function_code' => $functionCode,
        'action_id' => $actionId,
        'data_field_index' => $dataIndex,
        'ui_type_source' => $uiTypeSource,
        'ui_components' => $this->collectDeviceUiApiComponents([
            'command' => $commands,
            'response' => $response,
            'error' => $error,
        ], $parameterName, [
            'command_id' => $commandId,
            'function_code' => $functionCode,
            'action_id' => $actionId,
            'multiple_ui' => $this->deviceUiFlagTriple($row, 'multiple'),
            'multiple_ui_command' => $this->deviceUiApiValue($row, ['cmd_multipleui', 'multiple_ui', 'multipleui']),
            'multiple_ui_response' => $this->deviceUiApiValue($row, ['resp_multipleui', 'resp_multiple_ui']),
            'multiple_ui_error' => $this->deviceUiApiValue($row, ['error_multipleui', 'error_multiple_ui']),
            'trigger_ui' => $this->deviceUiFlagTriple($row, 'trigger'),
        ]),
        'multiple_ui' => $this->deviceUiFlagTriple($row, 'multiple'),
        'multiple_ui_by_source' => $this->deviceUiFlagTripleBySource($row, 'multiple'),
        'trigger_ui' => $this->deviceUiFlagTriple($row, 'trigger'),
        'frame' => [
            'name' => $frameName,
            'multiple_ui' => $this->deviceUiFlagTriple($row, 'multiple'),
            'multiple_ui_by_source' => $this->deviceUiFlagTripleBySource($row, 'multiple'),
            'trigger_ui' => $this->deviceUiFlagTriple($row, 'trigger'),
            'commands' => $this->hydrateDeviceUiFrameFields($commands, [
                'command_id' => $commandId,
                'function_code' => $functionCode,
                'action_id' => $actionId,
            ]),
            'response' => $response,
            'error' => $error,
        ],
    ];
}

private function collectDeviceUiApiComponents(array $frames, $parameterName = '', array $writeMeta = [])
{
    $components = [];
    $commandId = $writeMeta['command_id'] ?? $this->extractDeviceUiFrameFieldValue($frames['command'] ?? [], 'commandid');
    $functionCode = $writeMeta['function_code'] ?? $this->extractDeviceUiFrameFieldValue($frames['command'] ?? [], 'functioncode');
    $actionId = $writeMeta['action_id'] ?? $this->extractDeviceUiFrameFieldValue($frames['command'] ?? [], 'actionid');
    $sharedFrame = [
        'name' => trim((string) $parameterName) !== '' ? trim((string) $parameterName) : 'frame',
        'multiple_ui' => isset($writeMeta['multiple_ui']) && is_array($writeMeta['multiple_ui'])
            ? array_values($writeMeta['multiple_ui'])
            : ['false', 'false', 'false'],
        'multiple_ui_by_source' => $this->deviceUiMultipleFlagsBySource($writeMeta),
        'trigger_ui' => isset($writeMeta['trigger_ui']) && is_array($writeMeta['trigger_ui'])
            ? array_values($writeMeta['trigger_ui'])
            : ['false', 'false', 'false'],
        'commands' => $this->hydrateDeviceUiFrameFields(
            isset($frames['command']) && is_array($frames['command']) ? $frames['command'] : [],
            [
                'command_id' => $commandId,
                'function_code' => $functionCode,
                'action_id' => $actionId,
            ]
        ),
        'response' => isset($frames['response']) && is_array($frames['response']) ? $frames['response'] : [],
        'error' => isset($frames['error']) && is_array($frames['error']) ? $frames['error'] : [],
    ];
    $flagBySource = $this->deviceUiMultipleFlagsBySource($writeMeta);
    $multipleBySource = [
        'command' => $flagBySource['command'] === 'true',
        'response' => $flagBySource['response'] === 'true',
        'error' => $flagBySource['error'] === 'true',
    ];

    foreach (['command', 'response', 'error'] as $source) {
        $fields = isset($frames[$source]) && is_array($frames[$source]) ? $frames[$source] : [];
        if (empty($fields)) {
            continue;
        }

        $selectedIndexes = $this->selectDeviceUiComponentFieldIndexes($fields, !empty($multipleBySource[$source]));

        foreach ($selectedIndexes as $selectedIndex) {
            if (!isset($fields[$selectedIndex]) || !is_array($fields[$selectedIndex])) {
                continue;
            }

            $field = $fields[$selectedIndex];
            $uiType = trim((string) ($field['ui_type'] ?? $field['uiType'] ?? ''));
            if (!$this->isDeviceUiTypeUsable($uiType)) {
                continue;
            }
            $displayValue = isset($field['display_value']) && is_array($field['display_value']) ? $field['display_value'] : [];
            // Every UI component inherits the parent API's write IDs and full frame so
            // response/error cards still publish a complete deviceFrame in the schema.
            $fieldDescription = trim((string) ($field['Field_description'] ?? $field['field_description'] ?? ''));
            $defaultName = trim((string) ($field['default_name'] ?? ''));
            if (($fieldDescription === '' || strcasecmp($fieldDescription, 'Data') === 0)
                && $defaultName !== ''
                && strcasecmp($defaultName, 'Data') !== 0
            ) {
                $fieldDescription = $defaultName;
            } elseif ($fieldDescription === '') {
                $fieldDescription = $defaultName;
            }
            $components[] = [
                'parameter_name' => trim((string) $parameterName),
                'ui_type' => $uiType,
                'ui_type_source' => $source,
                'field_index' => $selectedIndex,
                'field_description' => $fieldDescription,
                // UI default prefers human asperui_value; wire hex stays on deviceFrame.value.
                'default' => $this->deviceUiFirstNonEmpty($field['asperui_value'] ?? null, $field['value'] ?? null),
                'items' => is_array($field['asperui'] ?? null) ? array_values(array_filter($field['asperui'], static function ($value) { return $value !== ''; })) : [],
                'item_values' => is_array($field['asperui_values'] ?? null) ? array_values(array_filter($field['asperui_values'], static function ($value) { return $value !== ''; })) : [],
                'sno_asperui' => is_array($field['sno_asperui'] ?? null) ? array_values(array_filter($field['sno_asperui'], static function ($value) { return $value !== ''; })) : [],
                'minimum' => $displayValue['min'] ?? null,
                'maximum' => $displayValue['max'] ?? null,
                'step' => $field['slider_step_value'] ?? null,
                'access' => $field['ui_description'] ?? null,
                'field_level' => $this->deviceUiFieldLevel($field),
                'parent_field_id' => (int) ($field['parent_field_id'] ?? $field['parentFieldId'] ?? 0),
                'start_bit' => $field['start_bit'] ?? ($field['startBit'] ?? null),
                'end_bit' => $field['end_bit'] ?? ($field['endBit'] ?? null),
                'command_id' => $commandId,
                'function_code' => $functionCode,
                'action_id' => $actionId,
                'frame' => $sharedFrame,
            ];
        }
    }

    return $components;
}

private function deviceUiFieldLevel(array $field)
{
    $level = strtolower(trim((string) ($field['field_level'] ?? $field['fieldLevel'] ?? 'bytewise')));
    return $level === 'bitwise' ? 'bitwise' : 'bytewise';
}

private function selectDeviceUiComponentFieldIndexes(array $fields, $includeAllUsable)
{
    $bitwiseUsable = [];
    $bytewiseUsable = [];
    $parentIdsWithBitwiseUi = [];

    foreach ($fields as $index => $field) {
        if (!is_array($field) || !$this->isDeviceUiTypeUsable($field['ui_type'] ?? ($field['uiType'] ?? ''))) {
            continue;
        }
        if ($this->deviceUiFieldLevel($field) === 'bitwise') {
            $bitwiseUsable[] = $index;
            $parentId = (int) ($field['parent_field_id'] ?? $field['parentFieldId'] ?? 0);
            if ($parentId > 0) {
                $parentIdsWithBitwiseUi[$parentId] = true;
            }
            continue;
        }
        $bytewiseUsable[] = $index;
    }

    $bytewiseKeep = [];
    foreach ($bytewiseUsable as $index) {
        $field = $fields[$index];
        $fieldId = (int) ($field['id'] ?? ($field['field_id'] ?? ($field['fieldId'] ?? 0)));
        if ($fieldId > 0 && isset($parentIdsWithBitwiseUi[$fieldId])) {
            continue;
        }
        $mode = strtolower(trim((string) ($field['bitwise_mode'] ?? ($field['bitwiseMode'] ?? ''))));
        if ($mode === 'bitwise' && !empty($bitwiseUsable)) {
            continue;
        }
        $bytewiseKeep[] = $index;
    }

    if ($includeAllUsable) {
        $merged = array_merge($bytewiseKeep, $bitwiseUsable);
        sort($merged, SORT_NUMERIC);
        return array_values(array_unique($merged));
    }

    $pool = !empty($bytewiseKeep) ? $bytewiseKeep : array_keys($fields);
    $selectedIndex = null;
    foreach ($pool as $index) {
        if (!isset($fields[$index]) || !is_array($fields[$index])) {
            continue;
        }
        $description = strtolower(trim((string) ($fields[$index]['Field_description'] ?? ($fields[$index]['field_description'] ?? ($fields[$index]['default_name'] ?? '')))));
        $uiType = trim((string) ($fields[$index]['ui_type'] ?? ($fields[$index]['uiType'] ?? '')));
        if (in_array($description, ['data', 'trigger'], true) && $this->isDeviceUiTypeUsable($uiType)) {
            $selectedIndex = $index;
            break;
        }
    }
    if ($selectedIndex === null) {
        foreach ($pool as $index) {
            if (!isset($fields[$index]) || !is_array($fields[$index])) {
                continue;
            }
            $uiType = trim((string) ($fields[$index]['ui_type'] ?? ($fields[$index]['uiType'] ?? '')));
            if ($this->isDeviceUiTypeUsable($uiType)) {
                $selectedIndex = $index;
                break;
            }
        }
    }

    if ($selectedIndex === null) {
        foreach ($fields as $index => $field) {
            if (!is_array($field)) {
                continue;
            }
            $description = strtolower(trim((string) ($field['Field_description'] ?? ($field['field_description'] ?? ($field['default_name'] ?? '')))));
            if (!in_array($description, ['data', 'trigger'], true)) {
                continue;
            }
            $selectedIndex = $index;
            break;
        }
    }

    if ($selectedIndex === null) {
        return [];
    }

    $selectedField = is_array($fields[$selectedIndex] ?? null) ? $fields[$selectedIndex] : [];
    $selectedId = (int) ($selectedField['id'] ?? ($selectedField['field_id'] ?? ($selectedField['fieldId'] ?? 0)));
    $childBits = [];
    if ($selectedId > 0) {
        foreach ($bitwiseUsable as $bitIndex) {
            $parentId = (int) ($fields[$bitIndex]['parent_field_id'] ?? ($fields[$bitIndex]['parentFieldId'] ?? 0));
            if ($parentId === $selectedId) {
                $childBits[] = $bitIndex;
            }
        }
    }
    if (!empty($childBits)) {
        return array_values($childBits);
    }

    return [$selectedIndex];
}

private function deviceUiMultipleFlagsBySource(array $writeMeta)
{
    $triple = isset($writeMeta['multiple_ui']) && is_array($writeMeta['multiple_ui'])
        ? array_values($writeMeta['multiple_ui'])
        : ['false', 'false', 'false'];

    return [
        'command' => $this->deviceUiOwnSourceFlagEnabled($writeMeta['multiple_ui_command'] ?? null, $triple[0] ?? false) ? 'true' : 'false',
        'response' => $this->deviceUiOwnSourceFlagEnabled($writeMeta['multiple_ui_response'] ?? null, $triple[1] ?? false) ? 'true' : 'false',
        'error' => $this->deviceUiOwnSourceFlagEnabled($writeMeta['multiple_ui_error'] ?? null, $triple[2] ?? false) ? 'true' : 'false',
    ];
}

private function deviceUiOwnSourceFlagEnabled($ownValue, $fallback)
{
    if ($ownValue === null || $ownValue === '' || $ownValue === []) {
        return $this->deviceUiMultipleFlagEnabled($fallback);
    }

    if (is_array($ownValue)) {
        $list = array_values($ownValue);
        if (count($list) >= 2) {
            return $this->deviceUiMultipleFlagEnabled($list[0] ?? false);
        }

        return $this->deviceUiMultipleFlagEnabled($list[0] ?? false);
    }

    $decoded = json_decode(trim((string) $ownValue), true);
    if (is_array($decoded) && count($decoded) >= 2) {
        $list = array_values($decoded);

        return $this->deviceUiMultipleFlagEnabled($list[0] ?? false);
    }

    return $this->deviceUiMultipleFlagEnabled($ownValue);
}

private function extractDeviceUiFrameFieldValue(array $fields, $fieldName)
{
    $fieldName = strtolower(preg_replace('/[\s_]+/', '', (string) $fieldName));
    foreach ($fields as $field) {
        if (!is_array($field)) {
            continue;
        }
        if ($this->deviceUiFrameFieldName($field) !== $fieldName) {
            continue;
        }
        if (!$this->deviceUiFrameValueIsEmpty($field['value'] ?? null)) {
            return $field['value'];
        }
        if (!$this->deviceUiFrameValueIsEmpty($field['asperui_value'] ?? null)) {
            return $field['asperui_value'];
        }
    }
    return null;
}

private function buildDeviceUiFrameFieldList(array $row, $prefix)
{
    $prefix = strtolower(trim((string) $prefix));
    if ($prefix === '') {
        return [];
    }
    $parameterLabel = trim((string) $this->deviceUiApiValue($row, ['parameter_name', 'parameterName', 'parameter']));

    $descriptions = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_fieldDescription', $prefix . '_fielddescription', $prefix . '_field_description']));
    $uiTypes = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_uiType', $prefix . '_uitype', $prefix . '_ui_type']));
    $defaults = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_defaultvalue']));
    $hexMins = $this->deviceUiApiList($this->deviceUiApiValue($row, [
        $prefix . '_valueInHexMin',
        $prefix . '_valueinhexmin',
        $prefix . '_value_in_hex_min',
    ]));
    $byteStarts = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_startBytePosition', $prefix . '_startbyteposition']));
    $byteCounts = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_numberOfBytes', $prefix . '_numberofbytes']));
    $dataTypes = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_dataType', $prefix . '_datatype']));
    $valueDefs = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_valueDefinition', $prefix . '_valuedefinition']));
    $displayMins = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_displayValueMin', $prefix . '_displayvaluemin']));
    $displayMaxs = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_displayValueMax', $prefix . '_displayvaluemax']));
    $steps = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_sliderStepValue', $prefix . '_sliderstepvalue']));
    $accessModes = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_uiDescriptionInOut', $prefix . '_uidescriptioninout']));
    $uiIds = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_uiId', $prefix . '_uiid']));
    $fieldLevels = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_fieldLevel', $prefix . '_field_level']));
    $parentFieldIds = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_parentFieldId', $prefix . '_parent_field_id']));
    $fieldIds = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_fieldId', $prefix . '_field_id']));
    $bitwiseModes = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_bitwiseMode', $prefix . '_bitwise_mode']));
    $startBits = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_startBit', $prefix . '_start_bit']));
    $endBits = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_endBit', $prefix . '_end_bit']));
    $asperui = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_asperui']));
    $asperuiValues = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_asperui_values', $prefix . '_asperuivalues']));
    $snoAsperui = $this->deviceUiApiList($this->deviceUiApiValue($row, [$prefix . '_sno_asperui', $prefix . '_snoasperui']));

    $count = max(
        count($descriptions),
        count($uiTypes),
        count($defaults),
        count($hexMins),
        count($byteStarts),
        count($byteCounts)
    );

    $fields = [];
    for ($index = 0; $index < $count; $index++) {
        $description = trim((string) ($descriptions[$index] ?? ''));
        $uiType = trim((string) ($uiTypes[$index] ?? 'None'));
        $rawDefault = $defaults[$index] ?? null;
        $normalizedDescription = strtolower($description);
        // Multi-data slots often store the human field name in defaultvalue (Resistance, Imin…).
        $defaultName = $description !== '' ? $description : ('field_' . ($index + 1));
        if ($normalizedDescription === 'data'
            && is_string($rawDefault)
            && trim($rawDefault) !== ''
            && !$this->deviceUiValueLooksLikeProtocolHex($rawDefault)
        ) {
            $defaultName = trim((string) $rawDefault);
        }
        if (in_array($prefix, ['resp', 'error'], true) && $this->deviceUiTelemetryNameIsGeneric($defaultName)) {
            $humanDefault = is_string($rawDefault) ? trim($rawDefault) : '';
            if ($humanDefault !== '' && !$this->deviceUiValueLooksLikeProtocolHex($humanDefault) && !$this->deviceUiTelemetryNameIsGeneric($humanDefault)) {
                $defaultName = $humanDefault;
            } elseif ($parameterLabel !== '' && strcasecmp($parameterLabel, 'frame') !== 0) {
                $defaultName = $parameterLabel;
            }
        }
        $fieldDescription = $description !== '' ? $description : $defaultName;
        if (in_array($prefix, ['resp', 'error'], true) && $this->deviceUiTelemetryNameIsGeneric($fieldDescription)) {
            $fieldDescription = $defaultName;
        }
        $fieldLevel = strtolower(trim((string) ($fieldLevels[$index] ?? 'bytewise')));
        if ($fieldLevel !== 'bitwise') {
            $fieldLevel = 'bytewise';
        }
        $byteCount = $this->deviceUiApiInt($byteCounts[$index] ?? 1, 1);
        $wireValue = $this->deviceUiResolveFrameWireValue(
            $rawDefault,
            $hexMins[$index] ?? null,
            $byteCount,
            $normalizedDescription
        );
        $field = [
            'default_name' => $defaultName,
            'value' => $wireValue,
            'asperui_value' => $rawDefault,
            'Field_description' => $fieldDescription,
            'type' => trim((string) ($valueDefs[$index] ?? 'Fixed Value')),
            'data_type' => trim((string) ($dataTypes[$index] ?? 'UnsignedInt')),
            'no_of_bytes' => $byteCount,
            'byte_start_position' => $this->deviceUiApiInt($byteStarts[$index] ?? ($index + 1), $index + 1),
            'ui_type' => $uiType !== '' ? $uiType : 'None',
            'local_control' => 'enable',
            'ui_ids' => $this->deviceUiApiInt($uiIds[$index] ?? 0, 0),
            'asperui' => [],
            'sno_asperui' => [],
            'asperui_values' => [],
            'ui_description' => $accessModes[$index] ?? null,
            'slider_step_value' => $steps[$index] ?? 0,
            'display_value' => [
                'min' => $displayMins[$index] ?? '',
                'max' => $displayMaxs[$index] ?? '',
            ],
            'field_level' => $fieldLevel,
            'parent_field_id' => (int) ($parentFieldIds[$index] ?? 0),
            'id' => (int) ($fieldIds[$index] ?? 0),
            'bitwise_mode' => trim((string) ($bitwiseModes[$index] ?? ($fieldLevel === 'bitwise' ? 'Bitwise' : 'Bytewise'))),
            'start_bit' => $startBits[$index] ?? null,
            'end_bit' => $endBits[$index] ?? null,
        ];

        if (in_array($normalizedDescription, ['data', 'trigger'], true) || (strtolower($uiType) !== 'none' && $uiType !== '')) {
            $fieldAsperui = $this->deviceUiApiList($asperui[$index] ?? null);
            $fieldAsperuiValues = $this->deviceUiApiList($asperuiValues[$index] ?? null);
            $fieldSnoAsperui = $this->deviceUiApiList($snoAsperui[$index] ?? null);
            // Legacy product_api stores option lists once for the data/trigger field.
            if (in_array($normalizedDescription, ['data', 'trigger'], true)) {
                if (empty($fieldAsperui) && !empty($asperui)) {
                    $fieldAsperui = array_values(array_filter($asperui, static function ($value) {
                        return $value !== '';
                    }));
                }
                if (empty($fieldAsperuiValues) && !empty($asperuiValues)) {
                    $fieldAsperuiValues = array_values(array_filter($asperuiValues, static function ($value) {
                        return $value !== '';
                    }));
                }
                if (empty($fieldSnoAsperui) && !empty($snoAsperui)) {
                    $fieldSnoAsperui = array_values(array_filter($snoAsperui, static function ($value) {
                        return $value !== '';
                    }));
                }
            }
            $field['asperui'] = $this->deviceUiChoiceListOrNull($fieldAsperui);
            $field['asperui_values'] = $this->deviceUiChoiceListOrNull($fieldAsperuiValues);
            $field['sno_asperui'] = $this->deviceUiChoiceListOrNull($fieldSnoAsperui);
            list($choiceLabels, $choiceValues) = $this->normalizeDeviceUiChoiceLists(
                is_array($field['asperui']) ? $field['asperui'] : [],
                is_array($field['asperui_values']) ? $field['asperui_values'] : []
            );
            if (!empty($choiceLabels)) {
                $field['asperui'] = $choiceLabels;
                $field['asperui_values'] = $choiceValues;
            }
            // Prefer numeric asperui default for the UI; keep wire hex on value.
            $publishedValues = is_array($field['asperui_values'] ?? null) ? $field['asperui_values'] : [];
            if (!$this->deviceUiFrameValueIsEmpty($publishedValues[0] ?? null)) {
                $field['asperui_value'] = $publishedValues[0];
            } elseif ($this->deviceUiFrameValueIsEmpty($field['asperui_value'] ?? null)
                || $this->deviceUiValueLooksLikeProtocolHex($field['asperui_value'])
            ) {
                $field['asperui_value'] = $wireValue;
            }
        }

        if (strtolower($uiType) === 'none' || in_array($normalizedDescription, ['commandid', 'command id', 'function code', 'functioncode', 'actionid', 'action id', 'terminator', 'header'], true)) {
            unset($field['asperui_value'], $field['type'], $field['data_type'], $field['ui_ids'], $field['asperui'], $field['sno_asperui'], $field['asperui_values'], $field['ui_description'], $field['slider_step_value'], $field['display_value']);
        }

        $fields[] = $field;
    }

    return $fields;
}

/**
 * True when $value is protocol hex digits (odd length is allowed; callers pad).
 */
private function deviceUiValueLooksLikeProtocolHex($value)
{
    $value = strtoupper(preg_replace('/[\s:_-]+/', '', preg_replace('/^0x/i', '', trim((string) $value))));
    if ($value === '') {
        return false;
    }

    return (bool) preg_match('/^[0-9A-F]+$/', $value);
}

/**
 * Normalize a hex string so strlen(value) === 2 * no_of_bytes (Rule 1).
 * Spaces / separators from product_api (e.g. "00 00 00 01") are stripped.
 */
private function deviceUiNormalizeProtocolHex($value, $byteCount = 0)
{
    $value = strtoupper(preg_replace('/^0x/i', '', trim((string) $value)));
    $value = preg_replace('/[\s:_-]+/', '', $value);
    if ($value === '' || !preg_match('/^[0-9A-F]+$/', $value)) {
        return '';
    }
    if ((strlen($value) % 2) !== 0) {
        $value = '0' . $value;
    }
    $bytes = max(0, (int) $byteCount);
    if ($bytes > 0) {
        $width = $bytes * 2;
        if (strlen($value) < $width) {
            $value = str_pad($value, $width, '0', STR_PAD_LEFT);
        } elseif (strlen($value) > $width) {
            $value = substr($value, -$width);
        }
    }

    return $value;
}

/**
 * Picks the transmittable frame byte string for a product_api field.
 * cmd_valueinhexmin is authoritative; cmd_defaultvalue often holds labels (Resistance)
 * or stubs that must not be sent on the wire.
 */
private function deviceUiResolveFrameWireValue($rawDefault, $hexMin, $byteCount, $fieldName = '')
{
    $normalizedHex = $this->deviceUiNormalizeProtocolHex($hexMin, $byteCount);
    if ($normalizedHex !== '') {
        return $normalizedHex;
    }

    $normalizedDefault = $this->deviceUiNormalizeProtocolHex($rawDefault, $byteCount);
    if ($normalizedDefault !== '') {
        return $normalizedDefault;
    }

    $fieldName = strtolower(preg_replace('/[\s_]+/', '', (string) $fieldName));
    if ($fieldName === 'terminator') {
        return '0A';
    }
    // Do not invent 00 for protocol ids. An empty slot can still be filled
    // from protocolMeta.command_id / function_code. A real stored 00 is kept
    // above, because deviceUiNormalizeProtocolHex already returned it.
    if (in_array($fieldName, ['commandid', 'functioncode', 'actionid'], true)) {
        return '';
    }

    $bytes = max(1, (int) $byteCount);

    return str_repeat('00', $bytes);
}

private function normalizeDeviceUiFlagList($value, $size = 3)
{
    $list = $this->deviceUiApiList($value);
    $normalized = [];
    for ($index = 0; $index < $size; $index++) {
        $raw = isset($list[$index]) ? strtolower(trim((string) $list[$index])) : 'false';
        $normalized[] = in_array($raw, ['1', 'true', 'yes', 'on'], true) ? 'true' : 'false';
    }
    return $normalized;
}

private function deviceUiMultipleFlagEnabled($value)
{
    if (is_array($value)) {
        foreach ($value as $item) {
            if ($this->deviceUiMultipleFlagEnabled($item)) {
                return true;
            }
        }
        return false;
    }

    $raw = strtolower(trim((string) $value));
    if ($raw === '') {
        return false;
    }

    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        return $this->deviceUiMultipleFlagEnabled($decoded);
    }

    return in_array($raw, ['1', 'true', 'yes', 'on'], true);
}

/**
 * Command / response / error flags are stored on separate frames.
 * A single "true" on the command frame must not be padded into
 * [true, false, false], or the response dropdown never opens.
 *
 * @return array{0:string,1:string,2:string}
 */
private function deviceUiFlagTriple(array $row, $kind = 'multiple')
{
    $bySource = $this->deviceUiFlagTripleBySource($row, $kind);

    return [$bySource['command'], $bySource['response'], $bySource['error']];
}

/**
 * @return array{command:string,response:string,error:string}
 */
private function deviceUiFlagTripleBySource(array $row, $kind = 'multiple')
{
    if ($kind === 'trigger') {
        $cmdKeys = ['cmd_triggerui', 'trigger_ui', 'triggerui'];
        $respKeys = ['resp_triggerui', 'resp_trigger_ui'];
        $errKeys = ['error_triggerui', 'error_trigger_ui'];
    } else {
        $cmdKeys = ['cmd_multipleui', 'multiple_ui', 'multipleui'];
        $respKeys = ['resp_multipleui', 'resp_multiple_ui'];
        $errKeys = ['error_multipleui', 'error_multiple_ui'];
    }

    $cmdRaw = $this->deviceUiApiValue($row, $cmdKeys);
    $respRaw = $this->deviceUiApiValue($row, $respKeys);
    $errRaw = $this->deviceUiApiValue($row, $errKeys);
    $cmdParts = array_values(array_filter($this->deviceUiApiList($cmdRaw), static function ($value) {
        return trim((string) $value) !== '';
    }));
    $cmdIsExplicitTriple = count($cmdParts) >= 2;
    $normalizedCmd = $this->normalizeDeviceUiFlagList($cmdRaw, 3);
    $hasOwn = static function ($raw) {
        if ($raw === null || $raw === '' || $raw === []) {
            return false;
        }
        if (is_array($raw) && count(array_filter($raw, static function ($value) {
            return trim((string) $value) !== '';
        })) === 0) {
            return false;
        }

        return true;
    };

    if ($cmdIsExplicitTriple && !$hasOwn($respRaw) && !$hasOwn($errRaw)) {
        return [
            'command' => $normalizedCmd[0],
            'response' => $normalizedCmd[1],
            'error' => $normalizedCmd[2],
        ];
    }

    return [
        'command' => $cmdIsExplicitTriple ? $normalizedCmd[0] : ($this->deviceUiMultipleFlagEnabled($cmdRaw) ? 'true' : 'false'),
        'response' => $hasOwn($respRaw)
            ? ($this->deviceUiMultipleFlagEnabled($respRaw) ? 'true' : 'false')
            : ($cmdIsExplicitTriple ? $normalizedCmd[1] : 'false'),
        'error' => $hasOwn($errRaw)
            ? ($this->deviceUiMultipleFlagEnabled($errRaw) ? 'true' : 'false')
            : ($cmdIsExplicitTriple ? $normalizedCmd[2] : 'false'),
    ];
}

/**
 * AS PER UI is sometimes only the option count ("4") while the choices
 * live in UI Values ("karat,efactor,cos,automation"). A dropdown built from
 * the count alone has nothing to open.
 *
 * @return array{0:array,1:array}
 */
private function normalizeDeviceUiChoiceLists($labels, $values)
{
    $clean = static function ($list) {
        $out = [];
        foreach ($list as $value) {
            $value = trim((string) $value);
            if ($value === '' || $value === '[]') {
                continue;
            }
            if (strpos($value, ',') !== false && strpos($value, '[') === false) {
                foreach (array_map('trim', explode(',', $value)) as $part) {
                    if ($part !== '') {
                        $out[] = $part;
                    }
                }
                continue;
            }
            $out[] = $value;
        }

        return $out;
    };

    $labels = $clean($this->deviceUiApiList($labels));
    $values = $clean($this->deviceUiApiList($values));
    if (count($values) > 1 && count($labels) === 1 && preg_match('/^\d+$/', $labels[0]) && (int) $labels[0] === count($values)) {
        return [$values, $values];
    }

    return [$labels, $values];
}

private function normalizeDeviceUiFrameChoiceFields(array $fields)
{
    foreach ($fields as $index => $field) {
        if (!is_array($field)) {
            continue;
        }
        $uiType = strtolower(trim((string) ($field['ui_type'] ?? '')));
        $isChoice = $uiType !== '' && $uiType !== 'none' && (
            strpos($uiType, 'drop') !== false
            || strpos($uiType, 'select') !== false
            || strpos($uiType, 'notification') !== false
            || strpos($uiType, 'segment') !== false
        );
        if (!$isChoice) {
            continue;
        }
        list($labels, $values) = $this->normalizeDeviceUiChoiceLists($field['asperui'] ?? [], $field['asperui_values'] ?? []);
        if (empty($labels)) {
            continue;
        }
        $fields[$index]['asperui'] = $labels;
        $fields[$index]['asperui_values'] = $values;
    }

    return $fields;
}

private function deviceUiFormatTypeName($uiType)
{
    $uiType = trim((string) $uiType);
    if ($uiType === '') {
        return '';
    }
    if ($uiType !== strtolower($uiType) && $uiType !== strtoupper($uiType)) {
        return $uiType;
    }

    return ucwords(strtolower($uiType));
}

private function deviceUiMultiComponentLabel(array $componentMeta, $parameterName)
{
    $uiType = $this->deviceUiFormatTypeName($componentMeta['ui_type'] ?? '');
    if ($uiType !== '') {
        return $uiType;
    }
    $fieldName = trim((string) ($componentMeta['field_description'] ?? ''));
    if ($fieldName !== '' && strcasecmp($fieldName, (string) $parameterName) !== 0) {
        return $fieldName;
    }

    return trim((string) $parameterName);
}

/**
 * Puts the frame's real choices on a dropdown so the control can open them.
 */
private function applyDeviceUiChoiceContract(array &$control)
{
    $type = strtolower(trim((string) ($control['componentType'] ?? '')));
    $uiType = strtolower(trim((string) ($control['protocolMeta']['ui_type'] ?? '')));
    $isChoice = strpos($type, 'dropdown') !== false
        || strpos($type, 'notification') !== false
        || strpos($type, 'segment') !== false
        || strpos($uiType, 'drop') !== false
        || strpos($uiType, 'notification') !== false
        || strpos($uiType, 'select') !== false;
    if (!$isChoice) {
        return;
    }

    $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
    list($labels, $values) = $this->normalizeDeviceUiChoiceLists($meta['items'] ?? [], $meta['item_values'] ?? []);
    if (empty($labels) && isset($control['deviceFrame']) && is_array($control['deviceFrame'])) {
        $source = strtolower(trim((string) ($meta['ui_type_source'] ?? 'command')));
        $bucket = $source === 'response' ? 'response' : ($source === 'error' ? 'error' : 'commands');
        $fieldIndex = isset($meta['field_index']) ? (int) $meta['field_index'] : -1;
        $field = ($fieldIndex >= 0 && isset($control['deviceFrame'][$bucket][$fieldIndex]) && is_array($control['deviceFrame'][$bucket][$fieldIndex]))
            ? $control['deviceFrame'][$bucket][$fieldIndex]
            : null;
        if ($field !== null) {
            list($labels, $values) = $this->normalizeDeviceUiChoiceLists($field['asperui'] ?? [], $field['asperui_values'] ?? []);
        }
    }
    if (empty($labels)) {
        return;
    }

    $meta['items'] = $labels;
    $meta['item_values'] = $values;
    $control['protocolMeta'] = $meta;
    $options = [];
    foreach ($labels as $index => $label) {
        $wire = isset($values[$index]) && trim((string) $values[$index]) !== '' ? (string) $values[$index] : (string) $label;
        $options[] = [
            'label' => (string) $label,
            'value' => $wire,
        ];
    }
    $control['options'] = $options;
    if (!isset($control['component']) || !is_array($control['component'])) {
        $control['component'] = [];
    }
    $control['component']['options'] = $options;
    if (!isset($control['component']['props']) || !is_array($control['component']['props'])) {
        $control['component']['props'] = [];
    }
    $control['component']['props']['options'] = $options;
}

private function deviceUiApiInt($value, $fallback = 0)
{
    if (is_int($value) || is_float($value)) {
        return (int) $value;
    }
    $raw = trim((string) $value);
    if ($raw === '' || !is_numeric($raw)) {
        return (int) $fallback;
    }
    return (int) $raw;
}

private function isDeviceUiTypeUsable($uiType)
{
    $normalized = strtolower(trim((string) $uiType));
    return $normalized !== '' && !in_array($normalized, ['none', 'no', 'na', 'n/a', 'null', 'false', 'disabled'], true);
}

private function pickDeviceUiControlIndex(array $fieldDescriptions, array $uiTypes)
{
    $bestIndex = null;
    $bestScore = -1;

    foreach ($uiTypes as $index => $uiType) {
        $normalizedType = strtolower(trim((string) $uiType));
        if ($normalizedType === '' || $normalizedType === 'none') {
            continue;
        }

        $score = 10;
        if (strpos($normalizedType, 'toggle') !== false || strpos($normalizedType, 'switch') !== false) {
            $score = 100;
        } elseif (strpos($normalizedType, 'hue') !== false || strpos($normalizedType, 'slider') !== false || strpos($normalizedType, 'range') !== false) {
            $score = 90;
        } elseif (strpos($normalizedType, 'button') !== false || strpos($normalizedType, 'trigger') !== false) {
            $score = 80;
        } elseif (strpos($normalizedType, 'segment') !== false) {
            $score = 75;
        } elseif (strpos($normalizedType, 'drop') !== false || strpos($normalizedType, 'select') !== false) {
            $score = 70;
        } elseif (strpos($normalizedType, 'gauge') !== false || strpos($normalizedType, 'dial') !== false) {
            $score = 65;
        } elseif (strpos($normalizedType, 'text') !== false) {
            $score = 60;
        }

        $description = strtolower(trim((string) ($fieldDescriptions[$index] ?? '')));
        if (in_array($description, ['header', 'terminator', 'reserved'], true)) {
            $score -= 5;
        }
        if ($description === 'command id' || $description === 'commandid') {
            $score -= 3;
        }
        if ($description === 'data' || $description === 'trigger') {
            $score += 8;
        }

        if ($score > $bestScore) {
            $bestScore = $score;
            $bestIndex = $index;
        }
    }

    return $bestIndex;
}

private function deviceUiApiValue(array $row, array $keys)
{
    foreach ($keys as $key) {
        if (array_key_exists($key, $row)) {
            return $row[$key];
        }
    }

    $lowercaseRow = array_change_key_case($row, CASE_LOWER);
    foreach ($keys as $key) {
        $lowercaseKey = strtolower($key);
        if (array_key_exists($lowercaseKey, $lowercaseRow)) {
            return $lowercaseRow[$lowercaseKey];
        }
    }

    return null;
}

private function deviceUiApiList($value)
{
    if (is_array($value)) {
        return array_values($value);
    }

    $value = trim((string) $value);
    if ($value === '') {
        return [];
    }

    $decoded = json_decode($value, true);
    if (is_array($decoded)) {
        return array_values($decoded);
    }

    return array_map('trim', explode(',', $value));
}

public function getDeviceUiTheme($modelno)
{
    if (!$modelno) {
        return null;
    }

    $this->ensureDeviceUiThemesTable();

    // schema_json on this table is not the canonical document. Selecting it
    // pulls a second full copy into memory before device_ui_schemas is read.
    $record = $this->db->table('device_ui_themes')
        ->select('theme_json')
        ->where('model_name', $modelno)
        ->get()
        ->getRowArray();

    if (!$record) {
        return null;
    }

    $theme = json_decode($record['theme_json'] ?? '{}', true);
    unset($record);

    return [
        'theme' => is_array($theme) ? $theme : [],
        'schema' => [],
    ];
}

/**
 * Decode schema_json, reassembling part files when the row is a split manifest.
 * A legacy inline document larger than 1.5MB is rewritten to part files so the
 * next request does not load that blob through MySQL again.
 */
private function decodeDeviceUiSchemaJson($raw, $modelno = '', $nodeId = '')
{
    DeviceUiSchemaGenerator::raiseMemoryLimit();
    $raw = is_string($raw) ? $raw : '';
    $bytes = strlen($raw);
    $split = DeviceUiSchemaGenerator::isSplitPayload($raw);
    $schema = DeviceUiSchemaGenerator::decode($raw);
    unset($raw);

    if (!$split && $bytes >= 1500000 && is_array($schema) && trim((string) $modelno) !== '') {
        $this->migrateDeviceUiSchemaStorage($modelno, $nodeId, $schema);
    }

    return is_array($schema) ? $schema : null;
}

private function migrateDeviceUiSchemaStorage($modelno, $nodeId, array $schema)
{
    try {
        $packaged = DeviceUiSchemaGenerator::package($modelno, $nodeId, $schema);
        if (!DeviceUiSchemaGenerator::isSplitPayload($packaged)) {
            unset($packaged);
            return;
        }
        $this->ensureDeviceUiSchemasTable();
        $this->db->table('device_ui_schemas')
            ->where('model_name', $modelno)
            ->update(['schema_json' => $packaged]);
        unset($packaged);
    } catch (\Throwable $exception) {
        log_message('error', 'Device UI schema file split failed: ' . $exception->getMessage());
    }
}

private $deviceUiSaveError = '';

public function getDeviceUiSaveError()
{
    return trim((string) $this->deviceUiSaveError);
}

private function prepareDeviceUiSave()
{
    $this->deviceUiSaveError = '';
    // max_allowed_packet is read-only at session scope on MySQL 8 / MariaDB.
    // Never SET GLOBAL here: MySQL implicitly commits the open transaction.
}

private function deviceUiDbLost($message)
{
    $message = strtolower((string) $message);
    return strpos($message, 'gone away') !== false
        || strpos($message, 'lost connection') !== false
        || strpos($message, 'server shutdown') !== false
        || strpos($message, 'error 2006') !== false
        || strpos($message, 'error 2013') !== false;
}

private function reconnectDeviceUiDatabase()
{
    try {
        $this->db->reconnect();
        return;
    } catch (\Throwable $exception) {
        log_message('error', 'Device UI DB reconnect failed: ' . $exception->getMessage());
    }

    try {
        $this->db->close();
    } catch (\Throwable $exception) {
        // The socket is already dead.
    }
    try {
        $this->db->initialize();
    } catch (\Throwable $exception) {
        log_message('error', 'Device UI DB initialize failed: ' . $exception->getMessage());
    }
}

/**
 * Packaging a large schema can sit idle longer than MySQL's net_read_timeout.
 * Ping, and open a new connection when the server has already dropped this one.
 */
private function ensureDeviceUiConnection()
{
    for ($attempt = 1; $attempt <= 2; $attempt++) {
        try {
            $result = $this->db->query('SELECT 1');
            if ($result === false) {
                $error = $this->db->error();
                $message = trim((string) ($error['message'] ?? ''));
                if ($attempt === 1 && $this->deviceUiDbLost($message)) {
                    $this->reconnectDeviceUiDatabase();
                    continue;
                }
                if ($message !== '') {
                    $this->deviceUiSaveError = $message;
                }
                return $result !== false;
            }
            return true;
        } catch (\Throwable $exception) {
            if ($attempt === 1 && $this->deviceUiDbLost($exception->getMessage())) {
                $this->reconnectDeviceUiDatabase();
                continue;
            }
            $this->deviceUiSaveError = $exception->getMessage();
            return false;
        }
    }

    return false;
}

private function writeDeviceUiTableRow($table, array $data, $modelno, $isUpdate)
{
    try {
        if ($isUpdate) {
            $ok = $this->db->table($table)->where('model_name', $modelno)->update($data);
        } else {
            $ok = $this->db->table($table)->insert($data);
        }
    } catch (\Throwable $exception) {
        $this->deviceUiSaveError = $exception->getMessage();
        log_message('error', 'Device UI save failed for ' . $table . ': ' . $exception->getMessage());
        return false;
    }

    if ($ok === false) {
        $error = $this->db->error();
        $code = isset($error['code']) ? (int) $error['code'] : 0;
        if ($code === 0) {
            return true;
        }
        $this->deviceUiSaveError = trim((string) ($error['message'] ?? ('SQL error ' . $code)));
        log_message('error', 'Device UI SQL error for ' . $table . ': ' . $this->deviceUiSaveError);
        return false;
    }

    return true;
}

public function saveDeviceUiTheme($modelno, array $theme, array $schema = [])
{
    $this->prepareDeviceUiSave();
    $this->ensureDeviceUiThemesTable();

    $now = date('Y-m-d H:i:s');
    try {
        $now = (new \DateTime('now', new \DateTimeZone('Asia/Kolkata')))->format('Y-m-d H:i:s');
    } catch (\Throwable $exception) {
        // Keep server default if timezone data is unavailable.
    }
    $themeJson = json_encode($theme, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($themeJson === false) {
        $this->deviceUiSaveError = 'Theme JSON could not be encoded.';
        return false;
    }

    $existing = $this->db->table('device_ui_themes')
        ->where('model_name', $modelno)
        ->get()
        ->getRowArray();

    $data = [
        'theme_json' => $themeJson,
        'updated_at' => $now,
    ];

    if ($existing) {
        return $this->writeDeviceUiTableRow('device_ui_themes', $data, $modelno, true);
    }

    $data['model_name'] = $modelno;
    $data['created_at'] = $now;
    $data['schema_json'] = '{}';
    return $this->writeDeviceUiTableRow('device_ui_themes', $data, $modelno, false);
}

public function saveDeviceUiSchema($modelno, array $schema)
{
    DeviceUiSchemaGenerator::raiseMemoryLimit();
    $this->prepareDeviceUiSave();
    $this->ensureDeviceUiSchemasTable();

    $app = [];
    if (isset($schema['app']) && is_array($schema['app'])) {
        $app = $schema['app'];
    } elseif (isset($schema['meta']['app']) && is_array($schema['meta']['app'])) {
        $app = $schema['meta']['app'];
    }

    $nodeId = '';

    if (!empty($schema['node_id']['id'])) {
        $nodeId = trim((string)$schema['node_id']['id']);
    } elseif (!empty($schema['meta']['app']['nodeId'])) {
        $nodeId = trim((string)$schema['meta']['app']['nodeId']);
    } elseif (!empty($schema['state']['node_id'])) {
        $nodeId = trim((string)$schema['state']['node_id']);
    }


    $nodeId = $this->resolveDeviceUiNodeId($modelno, $nodeId);

    try {
        $schema = $this->refreshDeviceUiSchemaApiDetails($modelno, $schema, $nodeId);
    } catch (\Throwable $exception) {
        log_message('error', 'Device UI schema refresh on save failed: ' . $exception->getMessage());
    }

    if (!isset($schema['meta']) || !is_array($schema['meta'])) {
        $schema['meta'] = [];
    }
    $mappingFingerprint = $this->deviceUiMappingFingerprint($modelno);
    if ($mappingFingerprint !== '') {
        $schema['meta']['mappingFingerprint'] = $mappingFingerprint;
    }
    $schema['meta']['schemaBuildVersion'] = $this->deviceUiSchemaBuildVersion();

    try {
        $istNow = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
    } catch (\Throwable $exception) {
        $istNow = new \DateTime('now');
    }
    $schema['generatedAt'] = $istNow->format('Y-m-d H:i:s') . ' IST';
    $schema['generatedAtIso'] = $istNow->format('Y-m-d\TH:i:s') . '+05:30';
    $schema['generatedTimezone'] = 'Asia/Kolkata';
    $schema['updatedAt'] = $schema['generatedAt'];
    $now = $istNow->format('Y-m-d H:i:s');

    $schemaJson = DeviceUiSchemaGenerator::package((string) $modelno, (string) $nodeId, $schema);
    if (!is_string($schemaJson) || $schemaJson === '' || strlen($schemaJson) > DeviceUiSchemaGenerator::INLINE_LIMIT) {
        $detail = trim(DeviceUiSchemaGenerator::lastError());
        $this->deviceUiSaveError = $detail !== ''
            ? $detail
            : 'Schema JSON could not be encoded into a MySQL-sized value.';
        log_message('error', 'Device UI schema package failed for ' . $modelno . ': ' . $this->deviceUiSaveError);
        return false;
    }

    if (!$this->ensureDeviceUiConnection()) {
        if ($this->deviceUiSaveError === '') {
            $this->deviceUiSaveError = 'MySQL connection was lost before the schema could be saved.';
        }
        return false;
    }

    $data = [
    'model_name' => $modelno,
    'node_id'    => $nodeId,
    'app_id'     => substr((string) (isset($app['id']) ? $app['id'] : (isset($schema['id']) ? $schema['id'] : (isset($schema['app_id']) ? $schema['app_id'] : ''))), 0, 191),
    'version'    => substr((string) (isset($app['version']) ? $app['version'] : (isset($schema['schemaVersion']) ? $schema['schemaVersion'] : '1.0.0')), 0, 32),
    'schema_json'=> $schemaJson,
    'updated_at' => $now,
    ];

    $existing = $this->findDeviceUiSchemaRowMeta($modelno, $nodeId);
    if ($existing === false) {
        return false;
    }
    if ($existing) {
        unset($data['model_name']);
        $saved = $this->writeDeviceUiTableRow('device_ui_schemas', $data, $modelno, true);
    } else {
        $data['created_at'] = $now;
        $saved = $this->writeDeviceUiTableRow('device_ui_schemas', $data, $modelno, false);
    }

    if (!$saved && $this->deviceUiDbLost($this->deviceUiSaveError)) {
        $this->reconnectDeviceUiDatabase();
        $this->deviceUiSaveError = '';
        if ($existing) {
            $saved = $this->writeDeviceUiTableRow('device_ui_schemas', $data, $modelno, true);
        } else {
            $saved = $this->writeDeviceUiTableRow('device_ui_schemas', $data, $modelno, false);
        }
    }

    if ($saved) {
        $activeNodeId = '';
        if (!empty($schema['node_id']['id'])) {
            $activeNodeId = trim((string) $schema['node_id']['id']);
        } elseif (!empty($schema['state']['node_id']) && is_string($schema['state']['node_id'])) {
            $activeNodeId = trim((string) $schema['state']['node_id']);
        } elseif (!empty($schema['meta']['app']['nodeId'])) {
            $activeNodeId = trim((string) $schema['meta']['app']['nodeId']);
        }
        $activeNodeId = $this->resolveDeviceUiNodeId($modelno, $activeNodeId);
        $this->deleteDeviceUiScanResultsExcept($modelno, $activeNodeId);
    }

    return $saved;
}

public function deleteDeviceUiScanResultsExcept($modelno, $activeNodeId)
{
    $modelno = trim((string) $modelno);
    $activeNodeId = trim((string) $activeNodeId);
    if ($modelno === '' || $activeNodeId === '') {
        return false;
    }

    $this->ensureDeviceUiScanResultsTable();

    return $this->db->table('device_ui_scan_results')
        ->where('model_name', $modelno)
        ->where('node_id !=', $activeNodeId)
        ->delete();
}

public function getDeviceUiSchema($modelno, $nodeId = '')
{
    if (!$modelno) {
        return null;
    }

    DeviceUiSchemaGenerator::raiseMemoryLimit();

    $record = $this->findDeviceUiSchemaRecord($modelno, $nodeId);
    $recordNodeId = is_array($record) ? trim((string) ($record['node_id'] ?? '')) : '';
    $schema = $this->decodeDeviceUiSchemaJson(
        is_array($record) ? (string) ($record['schema_json'] ?? '') : '',
        $modelno,
        $recordNodeId !== '' ? $recordNodeId : $nodeId
    );
    unset($record);
    if (!is_array($schema) || empty($schema['schemaType'])) {
        try {
            $this->ensureDeviceUiSchema($modelno, $nodeId, true);
        } catch (\Throwable $exception) {
            log_message('error', 'Auto device-ui schema generate failed: ' . $exception->getMessage());
        }
        $record = $this->findDeviceUiSchemaRecord($modelno, $nodeId);
        $recordNodeId = is_array($record) ? trim((string) ($record['node_id'] ?? '')) : $recordNodeId;
        $schema = $this->decodeDeviceUiSchemaJson(
            is_array($record) ? (string) ($record['schema_json'] ?? '') : '',
            $modelno,
            $recordNodeId !== '' ? $recordNodeId : $nodeId
        );
        unset($record);
    }

    if (!is_array($schema)) {
        return null;
    }
    $resolvedNodeId = trim((string) $nodeId);
    if ($resolvedNodeId === '') {
        $resolvedNodeId = $recordNodeId;
    }

    // Persist mapping/build updates when missing/incomplete, fingerprint stale,
    // or schema build version is outdated. Old models should not require Studio.
    if (!$this->deviceUiSchemaSyncing) {
        try {
            $mappingFingerprint = $this->deviceUiMappingFingerprint($modelno);
            $storedFingerprint = trim((string) ($schema['meta']['mappingFingerprint'] ?? ''));
            $needsSync = $this->deviceUiSchemaNeedsAutoRebuild($schema, $modelno)
                || ($mappingFingerprint !== '' && $mappingFingerprint !== $storedFingerprint)
                || $this->deviceUiSchemaNeedsBuildRefresh($schema);

            if ($needsSync) {
                $syncResult = $this->syncDeviceUiSchemaFromPageMapping($modelno, $resolvedNodeId);
                if (!empty($syncResult['saved']) || !empty($syncResult['created'])) {
                    $reloaded = (isset($syncResult['schema']) && is_array($syncResult['schema'])) ? $syncResult['schema'] : null;
                    unset($syncResult);
                    if (is_array($reloaded) && !empty($reloaded['schemaType'])) {
                        $schema = $reloaded;
                    }
                    unset($reloaded);
                }
            }
        } catch (\Throwable $exception) {
            log_message('error', 'Device UI schema mapping sync on read failed: ' . $exception->getMessage());
        }
    }

    return $this->normalizeDeviceUiSchemaForApp($schema, $modelno, $resolvedNodeId);
}

/**
 * Repairs the document for the device the app actually opened: node-specific URLs,
 * incomplete frame slots, missing card styles and unknown icon tokens.
 */
private function normalizeDeviceUiSchemaForApp(array $schema, $modelno, $nodeId = '')
{
    try {
        if (!class_exists(\App\Services\DeviceUiSchemaNormalizer::class, false)) {
            $normalizerCandidates = [
                APPPATH . 'services/DeviceUiSchemaNormalizer.php',
                APPPATH . 'Services/DeviceUiSchemaNormalizer.php',
            ];
            foreach ($normalizerCandidates as $normalizerFile) {
                if (is_file($normalizerFile)) {
                    require_once $normalizerFile;
                    break;
                }
            }
        }

        return \App\Services\DeviceUiSchemaNormalizer::normalize($schema, $modelno, $nodeId);
    } catch (\Throwable $exception) {
        log_message('error', 'Device UI schema normalize failed: ' . $exception->getMessage());

        return $schema;
    }
}

/**
 * Single write-side entry point: create or refresh the stored device UI schema
 * from current pages/APIs/page-linking without opening view_page_ui.
 */
public function autoPersistDeviceUiSchema($modelno, $nodeId = '')
{
    $modelno = trim((string) $modelno);
    $nodeId = trim((string) $nodeId);
    if ($modelno === '') {
        return array('synced' => false, 'changed' => false, 'saved' => false, 'reason' => 'empty_model');
    }

    try {
        DeviceUiSchemaGenerator::raiseMemoryLimit();
        $result = $this->syncDeviceUiSchemaFromPageMapping($modelno, $nodeId);
        if (!is_array($result)) {
            $result = array('synced' => false, 'changed' => false, 'saved' => false, 'reason' => 'invalid_result');
        }

        // If sync claimed success but no row exists, force a fresh ensure+save.
        $record = $this->findDeviceUiSchemaRecord($modelno, $nodeId);
        if (!is_array($record) || trim((string) ($record['schema_json'] ?? '')) === '') {
            $created = $this->ensureDeviceUiSchema($modelno, $nodeId, true);
            $ok = is_array($created) && !empty($created['schemaType']);
            $record = $ok ? $this->findDeviceUiSchemaRecord($modelno, $nodeId) : null;
            $saved = is_array($record) && trim((string) ($record['schema_json'] ?? '')) !== '';

            return array(
                'synced' => $saved,
                'changed' => $saved,
                'saved' => $saved,
                'created' => $saved,
                'reason' => $saved ? 'ensured' : 'ensure_failed',
                'error' => $saved ? '' : $this->getDeviceUiSaveError(),
                'schema' => $saved ? $created : null,
            );
        }

        return $result;
    } catch (\Throwable $exception) {
        log_message('error', 'Auto persist device-ui schema failed for ' . $modelno . ': ' . $exception->getMessage());

        return array(
            'synced' => false,
            'changed' => false,
            'saved' => false,
            'reason' => 'exception',
            'error' => $exception->getMessage(),
        );
    }
}

/**
 * Backfill auto schemas for models that have pages/APIs/linking but no device_ui_schemas row.
 * Safe for existing Studio-locked schemas (skipped by ensureDeviceUiSchema).
 *
 * @param int $limit Max models to process in one call.
 * @return array{processed:int,saved:int,failed:int,models:array}
 */
public function backfillMissingDeviceUiSchemas($limit = 100)
{
    DeviceUiSchemaGenerator::raiseMemoryLimit();
    $limit = max(1, min(500, (int) $limit));
    $processed = 0;
    $saved = 0;
    $failed = 0;
    $models = array();

    $candidates = array();
    try {
        if ($this->db->tableExists('vendorproducts')) {
            $rows = $this->db->query(
                "SELECT v.modelno AS model_name, MAX(v.id) AS max_id
                 FROM vendorproducts v
                 LEFT JOIN device_ui_schemas s ON s.model_name = v.modelno
                 WHERE s.id IS NULL
                   AND v.modelno IS NOT NULL
                   AND v.modelno != ''
                 GROUP BY v.modelno
                 ORDER BY max_id DESC
                 LIMIT " . (int) $limit
            )->getResultArray();
            foreach ($rows as $row) {
                $name = trim((string) ($row['model_name'] ?? ''));
                if ($name !== '') {
                    $candidates[$name] = true;
                }
            }
        }
        if ($this->db->tableExists('api_page_linking_data') && count($candidates) < $limit) {
            $remaining = $limit - count($candidates);
            $rows = $this->db->query(
                "SELECT a.model_name, MAX(a.id) AS max_id
                 FROM api_page_linking_data a
                 LEFT JOIN device_ui_schemas s ON s.model_name = a.model_name
                 WHERE s.id IS NULL
                   AND a.model_name IS NOT NULL
                   AND a.model_name != ''
                 GROUP BY a.model_name
                 ORDER BY max_id DESC
                 LIMIT " . (int) $remaining
            )->getResultArray();
            foreach ($rows as $row) {
                $name = trim((string) ($row['model_name'] ?? ''));
                if ($name !== '') {
                    $candidates[$name] = true;
                }
            }
        }
        if ($this->db->tableExists('product_api') && count($candidates) < $limit) {
            $remaining = $limit - count($candidates);
            $rows = $this->db->query(
                "SELECT p.model_name, MAX(p.id) AS max_id
                 FROM product_api p
                 LEFT JOIN device_ui_schemas s ON s.model_name = p.model_name
                 WHERE s.id IS NULL
                   AND p.model_name IS NOT NULL
                   AND p.model_name != ''
                 GROUP BY p.model_name
                 ORDER BY max_id DESC
                 LIMIT " . (int) $remaining
            )->getResultArray();
            foreach ($rows as $row) {
                $name = trim((string) ($row['model_name'] ?? ''));
                if ($name !== '') {
                    $candidates[$name] = true;
                }
            }
        }
    } catch (\Throwable $exception) {
        log_message('error', 'backfillMissingDeviceUiSchemas candidate query failed: ' . $exception->getMessage());
    }

    foreach (array_keys($candidates) as $modelno) {
        $processed++;
        $result = $this->autoPersistDeviceUiSchema($modelno, '');
        $ok = !empty($result['saved']) || !empty($result['synced']);
        if ($ok) {
            $record = $this->findDeviceUiSchemaRecord($modelno, '');
            $ok = is_array($record) && trim((string) ($record['schema_json'] ?? '')) !== '';
        }
        if ($ok) {
            $saved++;
            $models[] = array('model_no' => $modelno, 'status' => 'saved');
        } else {
            $failed++;
            $models[] = array(
                'model_no' => $modelno,
                'status' => 'failed',
                'reason' => $result['reason'] ?? 'unknown',
                'error' => $result['error'] ?? $this->getDeviceUiSaveError(),
            );
        }
    }

    return array(
        'processed' => $processed,
        'saved' => $saved,
        'failed' => $failed,
        'models' => $models,
    );
}

private function deviceUiMappingRowCount($modelno)
{
    $mapping = $this->getPageParameterMapping($modelno);
    if (!is_array($mapping) || empty($mapping['rows']) || !is_array($mapping['rows'])) {
        return 0;
    }

    $count = 0;
    foreach ($mapping['rows'] as $row) {
        if (!is_array($row)) {
            continue;
        }
        if (trim((string) ($row['parameter'] ?? '')) !== '') {
            $count++;
        }
    }

    return $count;
}

private function deviceUiSchemaControlCount($schema)
{
    if (!is_array($schema) || empty($schema['controls']) || !is_array($schema['controls'])) {
        return 0;
    }

    return count($schema['controls']);
}

/**
 * Current server-side Device UI schema builder revision.
 * Bump DEVICE_UI_SCHEMA_BUILD_VERSION when generator output must refresh for all models.
 */
private function deviceUiSchemaBuildVersion(): string
{
    return self::DEVICE_UI_SCHEMA_BUILD_VERSION;
}

/**
 * True when stored schema was produced by an older generator build.
 */
private function deviceUiSchemaNeedsBuildRefresh($schema): bool
{
    if (!is_array($schema) || empty($schema['schemaType'])) {
        return true;
    }
    $stored = trim((string) ($schema['meta']['schemaBuildVersion'] ?? ''));

    return $stored === '' || $stored !== $this->deviceUiSchemaBuildVersion();
}

/**
 * True when an auto/bootstrap schema should be rebuilt from current mapping/APIs.
 * Studio-locked schemas are never treated as rebuild candidates here.
 */
private function deviceUiSchemaNeedsAutoRebuild($schema, $modelno)
{
    if ($this->deviceUiSchemaIsStudioLocked($schema)) {
        return false;
    }

    if (!is_array($schema) || empty($schema['schemaType'])) {
        return true;
    }

    $source = strtolower(trim((string) ($schema['meta']['source'] ?? 'auto')));
    if ($source !== '' && $source !== 'auto' && $source !== 'bootstrap') {
        return false;
    }

    $mappingRows = $this->deviceUiMappingRowCount($modelno);
    $controlCount = $this->deviceUiSchemaControlCount($schema);

    if ($mappingRows > 0 && $controlCount < $mappingRows) {
        return true;
    }

    if ($mappingRows > 0 && $controlCount === 0) {
        return true;
    }

    $storedFingerprint = trim((string) ($schema['meta']['mappingFingerprint'] ?? ''));
    if ($mappingRows > 0 && $storedFingerprint === '') {
        return true;
    }

    return false;
}

/**
 * Full regenerate from current pages/APIs, keeping Studio visual customizations.
 * Used when schema build version is stale so old models refresh without opening Studio.
 */
private function rebuildDeviceUiSchemaPreservingCustomizations($modelno, $nodeId, array $existing)
{
    $fresh = $this->generateInitialDeviceUiSchema($modelno, $nodeId);
    if (!is_array($fresh) || empty($fresh['schemaType'])) {
        return null;
    }

    foreach (array('theme', 'icons', 'wifiProvisioning', 'infoPage', 'mobileView', 'assets') as $preserveKey) {
        if (isset($existing[$preserveKey]) && is_array($existing[$preserveKey]) && $existing[$preserveKey] !== array()) {
            $fresh[$preserveKey] = $existing[$preserveKey];
        }
    }

    if (isset($existing['settings']) && is_array($existing['settings'])) {
        if (!isset($fresh['settings']) || !is_array($fresh['settings'])) {
            $fresh['settings'] = array();
        }
        foreach (array('parameterIcons', 'componentOverrides', 'componentTypes') as $settingKey) {
            if (isset($existing['settings'][$settingKey]) && is_array($existing['settings'][$settingKey])) {
                $fresh['settings'][$settingKey] = $existing['settings'][$settingKey];
            }
        }
    }

    if (!isset($fresh['meta']) || !is_array($fresh['meta'])) {
        $fresh['meta'] = array();
    }
    $previousSource = strtolower(trim((string) ($existing['meta']['source'] ?? '')));
    if ($previousSource === 'studio' || $this->deviceUiSchemaIsStudioLocked($existing)) {
        $fresh['meta']['source'] = ($previousSource !== '' && $previousSource !== 'auto' && $previousSource !== 'bootstrap')
            ? (string) ($existing['meta']['source'] ?? 'studio')
            : 'studio';
    } else {
        $fresh['meta']['source'] = $previousSource !== '' ? (string) ($existing['meta']['source'] ?? 'auto') : 'auto';
    }
    $fresh['meta']['mappingFingerprint'] = $this->deviceUiMappingFingerprint($modelno);
    $fresh['meta']['mappingSyncedAt'] = date('c');
    $fresh['meta']['schemaBuildVersion'] = $this->deviceUiSchemaBuildVersion();
    $fresh['meta']['autoRebuiltFromBuildVersion'] = true;

    return $this->syncDeviceUiSchemaControlReferences($fresh);
}

/**
 * Existence check that does not pull schema_json. A legacy inline document
 * can be larger than max_allowed_packet and that SELECT is what drops MySQL.
 * false means the lookup failed; null means there is no row.
 */
private function findDeviceUiSchemaRowMeta($modelno, $nodeId)
{
    $select = 'id, model_name, node_id';
    $nodeId = trim((string) $nodeId);

    for ($attempt = 1; $attempt <= 2; $attempt++) {
        try {
            $row = null;
            if ($nodeId !== '') {
                $result = $this->db->table('device_ui_schemas')
                    ->select($select)
                    ->where('model_name', $modelno)
                    ->where('node_id', $nodeId)
                    ->orderBy('id', 'DESC')
                    ->get();
                if ($result === false) {
                    throw new \RuntimeException(trim((string) (($this->db->error()['message'] ?? '') ?: 'Could not read the device UI schema row.')));
                }
                $row = $result->getRowArray();
            }
            if (!$row) {
                $result = $this->db->table('device_ui_schemas')
                    ->select($select)
                    ->where('model_name', $modelno)
                    ->orderBy('id', 'DESC')
                    ->get();
                if ($result === false) {
                    throw new \RuntimeException(trim((string) (($this->db->error()['message'] ?? '') ?: 'Could not read the device UI schema row.')));
                }
                $row = $result->getRowArray();
            }

            return $row ?: null;
        } catch (\Throwable $exception) {
            if ($attempt === 1 && $this->deviceUiDbLost($exception->getMessage())) {
                $this->reconnectDeviceUiDatabase();
                continue;
            }
            $this->deviceUiSaveError = $exception->getMessage();
            log_message('error', 'Device UI schema lookup failed: ' . $exception->getMessage());
            return false;
        }
    }

    return false;
}

private function findDeviceUiSchemaRecord($modelno, $nodeId = '')
{
    DeviceUiSchemaGenerator::raiseMemoryLimit();
    $this->ensureDeviceUiSchemasTable();
    $nodeId = trim((string) $nodeId);

    if ($nodeId !== '') {
        $row = $this->db->table('device_ui_schemas')
            ->where('model_name', $modelno)
            ->where('node_id', $nodeId)
            ->orderBy('updated_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();
        if ($row) {
            return $row;
        }
    }

    return $this->db->table('device_ui_schemas')
        ->where('model_name', $modelno)
        ->orderBy('updated_at', 'DESC')
        ->orderBy('id', 'DESC')
        ->get()
        ->getRowArray();
}

private function deviceUiSchemaIsStudioLocked($schema)
{
    if (!is_array($schema) || empty($schema)) {
        return false;
    }
    $source = strtolower(trim((string) ($schema['meta']['source'] ?? '')));
    if ($source === 'auto' || $source === 'bootstrap') {
        return false;
    }
    if ($source === 'studio') {
        return true;
    }
    if (!empty($schema['extensions']['serverDrivenUi']) && is_array($schema['extensions']['serverDrivenUi'])) {
        return true;
    }

    return !empty($schema['controls']) && is_array($schema['controls']);
}

public function ensureDeviceUiSchema($modelno, $nodeId = '', $replaceAuto = false)
{
    $modelno = trim((string) $modelno);
    if ($modelno === '') {
        return null;
    }

    DeviceUiSchemaGenerator::raiseMemoryLimit();
    $record = $this->findDeviceUiSchemaRecord($modelno, $nodeId);
    $existing = $this->decodeDeviceUiSchemaJson(
        is_array($record) ? (string) ($record['schema_json'] ?? '') : '',
        $modelno,
        is_array($record) ? (string) ($record['node_id'] ?? $nodeId) : $nodeId
    );
    unset($record);
    if ($this->deviceUiSchemaIsStudioLocked($existing)) {
        return is_array($existing) ? $existing : null;
    }

    $needsRebuild = $replaceAuto || $this->deviceUiSchemaNeedsAutoRebuild($existing, $modelno);
    if (is_array($existing) && !empty($existing['schemaType']) && !$needsRebuild) {
        return $existing;
    }

    $schema = $this->generateInitialDeviceUiSchema($modelno, $nodeId);
    if (!is_array($schema) || empty($schema['schemaType'])) {
        return is_array($existing) ? $existing : null;
    }

    if (!isset($schema['meta']) || !is_array($schema['meta'])) {
        $schema['meta'] = array();
    }
    $schema['meta']['source'] = 'auto';
    $schema['meta']['mappingFingerprint'] = $this->deviceUiMappingFingerprint($modelno);
    $schema['meta']['mappingSyncedAt'] = date('c');
    $schema['meta']['schemaBuildVersion'] = $this->deviceUiSchemaBuildVersion();
    $schema['meta']['autoGeneratedWithoutStudio'] = true;

    // Keep any previously saved theme/icons when rebuilding an incomplete auto schema.
    if (is_array($existing)) {
        foreach (array('theme', 'icons', 'wifiProvisioning', 'infoPage', 'mobileView', 'assets') as $preserveKey) {
            if (isset($existing[$preserveKey]) && is_array($existing[$preserveKey]) && $existing[$preserveKey] !== array()) {
                $schema[$preserveKey] = $existing[$preserveKey];
            }
        }
    }

    $saved = $this->saveDeviceUiSchema($modelno, $schema);
    if (!$saved) {
        log_message(
            'error',
            'ensureDeviceUiSchema save failed for ' . $modelno . ': ' . $this->getDeviceUiSaveError()
        );

        // Only return an in-memory schema when a previous row still exists.
        return is_array($existing) && !empty($existing['schemaType']) ? $existing : null;
    }

    // Confirm the row is actually readable — bootstrap/API callers key off this.
    $persisted = $this->findDeviceUiSchemaRecord($modelno, $nodeId);
    if (!is_array($persisted) || trim((string) ($persisted['schema_json'] ?? '')) === '') {
        log_message('error', 'ensureDeviceUiSchema persisted row missing after save for ' . $modelno);

        return null;
    }

    return $schema;
}

/**
 * Merge the latest page-linking / API mapping into the stored device UI schema and
 * persist only when the structural content actually changed.
 *
 * Studio-locked schemas are updated in place (controls/pages/widget lists) without
 * wiping theme or Studio customizations. Failures leave the previous row intact.
 */
public function syncDeviceUiSchemaFromPageMapping($modelno, $nodeId = '')
{
    $modelno = trim((string) $modelno);
    $nodeId = trim((string) $nodeId);
    if ($modelno === '') {
        return ['synced' => false, 'changed' => false, 'saved' => false, 'reason' => 'empty_model'];
    }
    if ($this->deviceUiSchemaSyncing) {
        return ['synced' => false, 'changed' => false, 'saved' => false, 'reason' => 'reentrant'];
    }

    $this->deviceUiSchemaSyncing = true;
    try {
        DeviceUiSchemaGenerator::raiseMemoryLimit();
        $record = $this->findDeviceUiSchemaRecord($modelno, $nodeId);
        $recordNodeId = is_array($record) ? trim((string) ($record['node_id'] ?? '')) : '';
        $existing = $this->decodeDeviceUiSchemaJson(
            is_array($record) ? (string) ($record['schema_json'] ?? '') : '',
            $modelno,
            $recordNodeId !== '' ? $recordNodeId : $nodeId
        );
        unset($record);

        // Missing schema OR incomplete auto schema → full regenerate from current mapping/APIs.
        // syncDeviceUiSchemaPagesFromMapping alone cannot invent controls for empty schemas.
        if (!is_array($existing) || empty($existing['schemaType']) || $this->deviceUiSchemaNeedsAutoRebuild($existing, $modelno)) {
            $created = $this->ensureDeviceUiSchema($modelno, $nodeId, true);
            $ok = is_array($created) && !empty($created['schemaType']);
            $recordAfter = $ok ? $this->findDeviceUiSchemaRecord($modelno, $nodeId) : null;
            $saved = is_array($recordAfter) && trim((string) ($recordAfter['schema_json'] ?? '')) !== '';
            if ($ok && $saved) {
                $createdFingerprint = $this->deviceUiMappingFingerprint($modelno);
                if ($createdFingerprint !== '') {
                    $created['meta'] = isset($created['meta']) && is_array($created['meta']) ? $created['meta'] : array();
                    if (($created['meta']['mappingFingerprint'] ?? '') !== $createdFingerprint) {
                        $created['meta']['mappingFingerprint'] = $createdFingerprint;
                        $this->saveDeviceUiSchema($modelno, $created);
                    }
                }
            }

            return array(
                'synced' => $saved,
                'changed' => $saved,
                'saved' => $saved,
                'created' => $saved,
                'reason' => $saved ? 'created' : 'ensure_failed',
                'error' => $saved ? '' : $this->getDeviceUiSaveError(),
                'schema' => $saved ? $created : null,
            );
        }

        $resolvedNodeId = $nodeId;
        if ($resolvedNodeId === '') {
            $resolvedNodeId = $recordNodeId;
        }

        // Stale generator build (old models never re-opened in Studio): rebuild structure,
        // keep theme/icons/overrides, then persist — same outcome as clicking Skema + Save.
        if ($this->deviceUiSchemaNeedsBuildRefresh($existing)) {
            $rebuilt = $this->rebuildDeviceUiSchemaPreservingCustomizations($modelno, $resolvedNodeId, $existing);
            if (is_array($rebuilt) && !empty($rebuilt['schemaType'])) {
                $saved = $this->saveDeviceUiSchema($modelno, $rebuilt);
                if ($saved) {
                    return array(
                        'synced' => true,
                        'changed' => true,
                        'saved' => true,
                        'rebuilt' => true,
                        'reason' => 'build_version_refresh',
                        'schema' => $rebuilt,
                    );
                }
                log_message(
                    'error',
                    'Device UI schema build-version rebuild save failed for ' . $modelno . ': ' . $this->getDeviceUiSaveError()
                );
            }
            // Fall through to light mapping sync if full rebuild could not be saved.
        }

        $beforeHash = $this->deviceUiSchemaContentHash($existing);
        $working = $existing;
        if (!isset($working['controls']) || !is_array($working['controls'])) {
            $working['controls'] = [];
        }

        $updated = $this->refreshDeviceUiSchemaApiDetails($modelno, $working, $resolvedNodeId);
        if (!is_array($updated) || empty($updated['schemaType'])) {
            return [
                'synced' => false,
                'changed' => false,
                'saved' => false,
                'reason' => 'refresh_failed',
                'schema' => $existing,
            ];
        }

        $updated = $this->syncDeviceUiSchemaPagesFromMapping($modelno, $updated);

        // Preserve Studio visual / provisioning customizations before reference sync
        // so component overrides are re-filtered against the current control set.
        foreach (['theme', 'icons', 'wifiProvisioning', 'infoPage', 'mobileView', 'assets'] as $preserveKey) {
            if (isset($existing[$preserveKey]) && is_array($existing[$preserveKey]) && $existing[$preserveKey] !== []) {
                $updated[$preserveKey] = $existing[$preserveKey];
            }
        }
        if (isset($existing['settings']) && is_array($existing['settings'])) {
            if (!isset($updated['settings']) || !is_array($updated['settings'])) {
                $updated['settings'] = [];
            }
            foreach (['parameterIcons', 'componentOverrides', 'componentTypes'] as $settingKey) {
                if (isset($existing['settings'][$settingKey]) && is_array($existing['settings'][$settingKey])) {
                    $updated['settings'][$settingKey] = $existing['settings'][$settingKey];
                }
            }
        }

        $updated = $this->syncDeviceUiSchemaControlReferences($updated);

        if (!isset($updated['meta']) || !is_array($updated['meta'])) {
            $updated['meta'] = [];
        }
        $previousSource = strtolower(trim((string) ($existing['meta']['source'] ?? '')));
        if ($previousSource === 'studio' || $this->deviceUiSchemaIsStudioLocked($existing)) {
            $updated['meta']['source'] = $previousSource !== '' && $previousSource !== 'auto' && $previousSource !== 'bootstrap'
                ? (string) ($existing['meta']['source'] ?? 'studio')
                : 'studio';
        } elseif ($previousSource !== '') {
            $updated['meta']['source'] = (string) ($existing['meta']['source'] ?? 'auto');
        }
        $updated['meta']['mappingFingerprint'] = $this->deviceUiMappingFingerprint($modelno);
        $updated['meta']['mappingSyncedAt'] = date('c');
        $updated['meta']['mappingSync'] = true;
        $updated['meta']['schemaBuildVersion'] = $this->deviceUiSchemaBuildVersion();

        $afterHash = $this->deviceUiSchemaContentHash($updated);
        if ($beforeHash === $afterHash) {
            return [
                'synced' => true,
                'changed' => false,
                'saved' => false,
                'schema' => $existing,
            ];
        }

        $saved = $this->saveDeviceUiSchema($modelno, $updated);
        if (!$saved) {
            log_message(
                'error',
                'Auto device-ui schema sync save failed for ' . $modelno . ': ' . $this->getDeviceUiSaveError()
            );

            return [
                'synced' => false,
                'changed' => true,
                'saved' => false,
                'reason' => 'save_failed',
                'schema' => $existing,
            ];
        }

        return [
            'synced' => true,
            'changed' => true,
            'saved' => true,
            'schema' => $updated,
        ];
    } catch (\Throwable $exception) {
        log_message('error', 'Auto device-ui schema sync failed: ' . $exception->getMessage());

        return [
            'synced' => false,
            'changed' => false,
            'saved' => false,
            'reason' => 'exception',
            'error' => $exception->getMessage(),
        ];
    } finally {
        $this->deviceUiSchemaSyncing = false;
    }
}

private function syncDeviceUiThemeControlOrderFromMapping($modelno)
{
    $modelno = trim((string) $modelno);
    if ($modelno === '') {
        return false;
    }

    $schema = null;
    try {
        $record = $this->findDeviceUiSchemaRecord($modelno);
        $schema = $this->decodeDeviceUiSchemaJson(
            is_array($record) ? (string) ($record['schema_json'] ?? '') : '',
            $modelno,
            is_array($record) ? (string) ($record['node_id'] ?? '') : ''
        );
        unset($record);
    } catch (\Throwable $exception) {
        $schema = null;
    }

    $order = [];
    if (is_array($schema) && !empty($schema['settings']['controlOrder']) && is_array($schema['settings']['controlOrder'])) {
        $order = array_values($schema['settings']['controlOrder']);
    }
    if (empty($order) && is_array($schema) && !empty($schema['controls']) && is_array($schema['controls'])) {
        foreach ($schema['controls'] as $control) {
            if (!is_array($control)) {
                continue;
            }
            $id = trim((string) ($control['id'] ?? ''));
            if ($id !== '') {
                $order[] = $id;
            }
        }
    }
    if (empty($order)) {
        return false;
    }

    $this->ensureDeviceUiThemesTable();
    $record = $this->db->table('device_ui_themes')
        ->where('model_name', $modelno)
        ->get()
        ->getRowArray();
    $theme = [];
    if ($record) {
        $decoded = json_decode($record['theme_json'] ?? '{}', true);
        if (is_array($decoded)) {
            $theme = $decoded;
        }
    }
    $theme['controlOrder'] = $order;

    return $this->saveDeviceUiTheme($modelno, $theme);
}

private function deviceUiMappingFingerprint($modelno)
{
    $mapping = $this->getPageParameterMapping($modelno);
    if (!is_array($mapping)) {
        return '';
    }

    $rows = [];
    foreach ($mapping['rows'] ?? [] as $row) {
        if (!is_array($row)) {
            continue;
        }
        $api = is_array($row['api'] ?? null) ? $row['api'] : [];
        $rows[] = [
            'page_no' => trim((string) ($row['page_no'] ?? '')),
            'page_name' => trim((string) ($row['page_name'] ?? '')),
            'parameter' => trim((string) ($row['parameter'] ?? '')),
            'sequence' => (int) ($row['sequence'] ?? 0),
            'command_id' => $api['command_id'] ?? null,
            'function_code' => $api['function_code'] ?? null,
            'action_id' => $api['action_id'] ?? null,
            'ui_type' => $api['ui_type'] ?? null,
            'ui_type_source' => $api['ui_type_source'] ?? null,
            'access' => $api['access'] ?? null,
        ];
    }

    $payload = [
        'page_names' => $mapping['page_names_array'] ?? [],
        'rows' => $rows,
    ];
    $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    return $encoded === false ? '' : hash('sha256', $encoded);
}

private function deviceUiSchemaContentHash(array $schema)
{
    $copy = $schema;
    unset(
        $copy['generatedAt'],
        $copy['generatedAtIso'],
        $copy['updatedAt'],
        $copy['generatedTimezone']
    );
    if (isset($copy['meta']) && is_array($copy['meta'])) {
        unset(
            $copy['meta']['mappingSyncedAt'],
            $copy['meta']['canonicalValidation'],
            $copy['meta']['flutterValidation']
        );
    }
    if (isset($copy['node_details']) && is_array($copy['node_details'])) {
        foreach ($copy['node_details'] as &$detail) {
            if (!is_array($detail) || !isset($detail['status']['connectivity']) || !is_array($detail['status']['connectivity'])) {
                continue;
            }
            unset($detail['status']['connectivity']['timestamp']);
        }
        unset($detail);
    }
    if (isset($copy['state']) && is_array($copy['state'])) {
        unset($copy['state']['online'], $copy['state']['onlineLabel'], $copy['state']['deviceConnectivity']);
    }

    $encoded = json_encode($copy, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    return $encoded === false ? '' : hash('sha256', $encoded);
}

private function syncDeviceUiSchemaPagesFromMapping($modelno, array $schema)
{
    $mapping = $this->getPageParameterMapping($modelno) ?: [];
    $pageNames = is_array($mapping['page_names_array'] ?? null) ? $mapping['page_names_array'] : [];
    $rows = is_array($mapping['rows'] ?? null) ? $mapping['rows'] : [];

    $controlsByPage = [];
    foreach ($schema['controls'] ?? [] as $control) {
        if (!is_array($control)) {
            continue;
        }
        $pageNo = trim((string) ($control['pageNo'] ?? '1'));
        if ($pageNo === '') {
            $pageNo = '1';
        }
        $id = trim((string) ($control['id'] ?? ''));
        if ($id === '') {
            continue;
        }
        if (!isset($controlsByPage[$pageNo])) {
            $controlsByPage[$pageNo] = [];
        }
        $controlsByPage[$pageNo][] = $id;
    }

    $expectedPages = [];
    foreach ($pageNames as $pageNo => $pageName) {
        $pageNo = trim((string) $pageNo);
        if ($pageNo === '') {
            continue;
        }
        $title = trim((string) $pageName);
        $expectedPages[$pageNo] = $title !== '' ? $title : ('Page ' . $pageNo);
    }
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $pageNo = trim((string) ($row['page_no'] ?? '1'));
        if ($pageNo === '') {
            $pageNo = '1';
        }
        $title = trim((string) ($row['page_name'] ?? ''));
        if ($title === '' && isset($pageNames[$pageNo])) {
            $title = trim((string) $pageNames[$pageNo]);
        }
        if ($title === '') {
            $title = 'Page ' . $pageNo;
        }
        $expectedPages[$pageNo] = $title;
    }
    foreach ($controlsByPage as $pageNo => $_ids) {
        if (!isset($expectedPages[$pageNo])) {
            $expectedPages[$pageNo] = 'Page ' . $pageNo;
        }
    }

    $existingByNo = [];
    foreach ($schema['pages'] ?? [] as $page) {
        if (!is_array($page)) {
            continue;
        }
        $pageNo = trim((string) ($page['pageNo'] ?? ''));
        if ($pageNo === '') {
            continue;
        }
        $existingByNo[$pageNo] = $page;
    }

    uksort($expectedPages, static function ($a, $b) {
        return ((int) $a) <=> ((int) $b);
    });

    $newPages = [];
    foreach ($expectedPages as $pageNo => $title) {
        $page = isset($existingByNo[$pageNo]) && is_array($existingByNo[$pageNo])
            ? $existingByNo[$pageNo]
            : [
                'id' => 'page_' . $pageNo,
                'pageNo' => $pageNo,
                'title' => $title,
                'index' => count($newPages),
                'controlIds' => [],
                'controlCount' => 0,
            ];
        $page['id'] = trim((string) ($page['id'] ?? '')) !== '' ? $page['id'] : ('page_' . $pageNo);
        $page['pageNo'] = $pageNo;
        $page['title'] = $title;
        $page['controlIds'] = array_values($controlsByPage[$pageNo] ?? []);
        $page['controlCount'] = count($page['controlIds']);
        $page['index'] = count($newPages);
        $newPages[] = $page;
    }

    $schema['pages'] = $newPages;
    if (!isset($schema['meta']) || !is_array($schema['meta'])) {
        $schema['meta'] = [];
    }
    $schema['meta']['pageCount'] = count($newPages);
    $schema['meta']['totalControlCount'] = is_array($schema['controls'] ?? null) ? count($schema['controls']) : 0;

    $pageIds = [];
    foreach ($newPages as $page) {
        $pageIds[] = $page['id'];
    }
    if (!isset($schema['navigation']) || !is_array($schema['navigation'])) {
        $schema['navigation'] = [];
    }
    $schema['navigation']['pages'] = $pageIds;
    $initial = trim((string) ($schema['navigation']['initial'] ?? ''));
    if ($initial === '' || !in_array($initial, $pageIds, true)) {
        $fallbackPageNo = '1';
        foreach ($expectedPages as $expectedPageNo => $_title) {
            $fallbackPageNo = (string) $expectedPageNo;
            break;
        }
        $schema['navigation']['initial'] = $pageIds[0] ?? ('page_' . $fallbackPageNo);
    }

    if (isset($schema['state']) && is_array($schema['state']) && !empty($newPages[0])) {
        $first = $newPages[0];
        if (!isset($schema['state']['activePageId']) || !in_array($schema['state']['activePageId'], $pageIds, true)) {
            $schema['state']['activePageId'] = $first['id'];
            $schema['state']['activePageNo'] = $first['pageNo'];
            $schema['state']['activePageName'] = $first['title'];
            $schema['state']['activePageIndex'] = 0;
        }
    }

    return $this->syncDeviceUiSchemaControlListNodes($schema, $controlsByPage, $expectedPages);
}

private function syncDeviceUiSchemaControlListNodes(array $schema, array $controlsByPage, array $expectedPages = [])
{
    $makeNode = static function ($pageNo, array $ids) {
        return [
            'type' => 'schemaControlList',
            'id' => 'schema_controls_' . $pageNo,
            'properties' => [
                'pageNo' => (string) $pageNo,
                'controlIds' => array_values($ids),
                'controlCount' => count($ids),
                'renderMode' => 'schemaControls',
                'filterBy' => 'pageNo',
                'cardType' => 'ParameterCard',
            ],
        ];
    };

    $pageNos = array_keys($expectedPages !== [] ? $expectedPages : $controlsByPage);
    if ($pageNos === [] && $controlsByPage !== []) {
        $pageNos = array_keys($controlsByPage);
    }
    usort($pageNos, static function ($a, $b) {
        return ((int) $a) <=> ((int) $b);
    });

    if (!isset($schema['ui']) || !is_array($schema['ui'])) {
        $schema['ui'] = [];
    }
    if (!isset($schema['ui']['widgetTree']) || !is_array($schema['ui']['widgetTree'])) {
        $children = [];
        foreach ($pageNos as $pageNo) {
            $children[] = $makeNode($pageNo, $controlsByPage[$pageNo] ?? []);
        }
        $schema['ui']['widgetTree'] = [
            'type' => 'column',
            'id' => 'phone_shell',
            'children' => $children,
        ];

        return $schema;
    }

    $presentPages = [];
    $collectPages = function ($node) use (&$collectPages, &$presentPages) {
        if (!is_array($node)) {
            return;
        }
        $type = strtolower(trim((string) ($node['type'] ?? '')));
        if ($type === 'schemacontrollist') {
            $pageNo = '';
            if (isset($node['properties']['pageNo'])) {
                $pageNo = trim((string) $node['properties']['pageNo']);
            } elseif (isset($node['pageNo'])) {
                $pageNo = trim((string) $node['pageNo']);
            }
            if ($pageNo !== '') {
                $presentPages[$pageNo] = true;
            }
        }
        foreach ($node as $key => $child) {
            if ($key === 'controls' || $key === 'catalog' || $key === 'registerComponents') {
                continue;
            }
            if (is_array($child)) {
                $collectPages($child);
            }
        }
    };
    $collectPages($schema['ui']['widgetTree']);

    $missing = [];
    foreach ($pageNos as $pageNo) {
        if (!isset($presentPages[$pageNo])) {
            $missing[] = $pageNo;
        }
    }

    if ($missing !== []) {
        $tree = &$schema['ui']['widgetTree'];
        if (!isset($tree['children']) || !is_array($tree['children'])) {
            $tree['children'] = [];
        }
        foreach ($missing as $pageNo) {
            $tree['children'][] = $makeNode($pageNo, $controlsByPage[$pageNo] ?? []);
        }
        unset($tree);
    }

    $this->replaceDeviceUiSchemaControlLists($schema['ui'], $controlsByPage);
    if (isset($schema['extensions']['serverDrivenUi']) && is_array($schema['extensions']['serverDrivenUi'])) {
        $this->replaceDeviceUiSchemaControlLists($schema['extensions']['serverDrivenUi'], $controlsByPage);
    }
    if (isset($schema['extensions']['universalUi']['ui']) && is_array($schema['extensions']['universalUi']['ui'])) {
        $this->replaceDeviceUiSchemaControlLists($schema['extensions']['universalUi']['ui'], $controlsByPage);
    }

    return $schema;
}

public function generateInitialDeviceUiSchema($modelno, $nodeId = '')
{
    $modelno = trim((string) $modelno);
    $nodeId = $this->resolveDeviceUiNodeId($modelno, $nodeId);
    $mapping = $this->getPageParameterMapping($modelno) ?: [];
    $pageNames = is_array($mapping['page_names_array'] ?? null) ? $mapping['page_names_array'] : [];
    $rows = is_array($mapping['rows'] ?? null) ? $mapping['rows'] : [];
    $infoPayload = $this->getDeviceUiInfo($modelno, $nodeId) ?: [];
    $deviceConfig = is_array($infoPayload['deviceConfig'] ?? null)
        ? $infoPayload['deviceConfig']
        : $this->buildDeviceUiNodeConfig($modelno, [], $pageNames, $rows, $nodeId);
    $brandLogo = $this->resolveDeviceUiBrandLogoAsset($modelno, $nodeId);
    $schemaApiUrl = $this->deviceUiSchemaApiUrl($modelno, $nodeId);
    $deviceOnline = false;
    $deviceOnlineTimestamp = (int) round(microtime(true) * 1000);
    if (isset($infoPayload['node_details'][0]['status']['connectivity']['connected'])) {
        $deviceOnline = (bool) $infoPayload['node_details'][0]['status']['connectivity']['connected'];
        if (isset($infoPayload['node_details'][0]['status']['connectivity']['timestamp'])) {
            $deviceOnlineTimestamp = (int) $infoPayload['node_details'][0]['status']['connectivity']['timestamp'];
        }
    }
    $deviceOnlineLabel = $deviceOnline ? 'Online' : 'Offline';

    $controls = [];
    $pagesByNo = [];
    $deviceParameters = [];
    foreach ($rows as $index => $row) {
        if (!is_array($row)) {
            continue;
        }
        $parameterName = trim((string) ($row['parameter'] ?? ''));
        if ($parameterName === '') {
            continue;
        }
        $pageNo = trim((string) ($row['page_no'] ?? '1'));
        if ($pageNo === '') {
            $pageNo = '1';
        }
        $pageName = trim((string) ($row['page_name'] ?? ''));
        if ($pageName === '' && isset($pageNames[$pageNo])) {
            $pageName = trim((string) $pageNames[$pageNo]);
        }
        if ($pageName === '') {
            $pageName = 'Page ' . $pageNo;
        }
        $api = is_array($row['api'] ?? null) ? $row['api'] : [];
        $controlId = $this->deviceUiSchemaControlKey($pageNo, $parameterName, '', false);
        $parameterKey = $this->deviceUiSchemaParameterSlug($controlId);
        $binding = $this->deviceUiSchemaBinding($parameterKey);
        $component = $this->deviceUiSchemaComponentFromApi($api['ui_type'] ?? '', $parameterName);
        $schemaType = $component['schema'] !== 'auto' ? $component['schema'] : 'value';
        $catalogType = $component['catalog'] !== 'auto' ? $component['catalog'] : $schemaType;
        $writable = $this->deviceUiMetaIsWritable($api);
        // Text entry is stricter than the other controls: it is editable only when the
        // API row explicitly grants write access. cmd is device output, never editable.
        if ($catalogType === 'cmd') {
            $writable = false;
        } elseif ($schemaType === 'textField') {
            $writable = $writable && $this->deviceUiMetaTextIsEditable($api);
        }
        $valueType = $this->deviceUiSchemaValueType($catalogType);
        $flutterWidget = $this->deviceUiSchemaFlutterWidget($schemaType);
        $initialValue = $api['default'] ?? ($writable ? null : '');
        $protocolMeta = [
            'command_id' => $api['command_id'] ?? null,
            'function_code' => $api['function_code'] ?? null,
            'action_id' => $api['action_id'] ?? null,
            'ui_type' => $api['ui_type'] ?? null,
            'ui_type_source' => $api['ui_type_source'] ?? 'command',
            'access' => $api['access'] ?? null,
            'items' => is_array($api['items'] ?? null) ? $api['items'] : [],
            'item_values' => is_array($api['item_values'] ?? null) ? $api['item_values'] : [],
            'sno_asperui' => is_array($api['sno_asperui'] ?? null) ? $api['sno_asperui'] : [],
            'minimum' => $api['minimum'] ?? null,
            'maximum' => $api['maximum'] ?? null,
            'step' => $api['step'] ?? null,
            'default' => $api['default'] ?? null,
            'frame' => is_array($api['frame'] ?? null) ? $api['frame'] : null,
        ];
        $control = [
            'id' => $controlId,
            'parameterKey' => $parameterKey,
            'parameter' => $parameterName,
            'parameterName' => $parameterName,
            'pageNo' => $pageNo,
            'pageName' => $pageName,
            'pageId' => 'page_' . $pageNo,
            'sequence' => (int) ($row['sequence'] ?? ($index + 1)),
            'componentType' => $schemaType,
            'controlType' => $schemaType,
            'catalogComponentType' => $catalogType !== $schemaType ? $catalogType : null,
            'componentTypeSource' => 'api',
            'valueType' => $valueType,
            'flutterWidget' => $flutterWidget,
            'binding' => $binding,
            'flutterBinding' => 'state.parameters.' . $parameterKey . '.value',
            'initialValue' => $initialValue,
            'label' => $parameterName,
            'value' => $initialValue,
            'visible' => true,
            'readOnly' => !$writable,
            'writable' => $writable,
            'interactive' => $writable,
            'events' => $writable ? $this->deviceUiSchemaEventsForControl($schemaType, $binding, []) : [],
            'protocolMeta' => $protocolMeta,
            'telemetryKey' => $parameterName,
            'deviceFrame' => is_array($api['frame'] ?? null)
                ? $this->normalizeDeviceUiFrame($api['frame'], $parameterName, $api)
                : $this->defaultDeviceUiFrame($parameterName, $api),
        ];
        $controls[] = $control;
        $this->applyDeviceUiDirectionControlContract($controls[count($controls) - 1]);
        $deviceParameters[$parameterKey] = $controls[count($controls) - 1]['initialValue'];
        // Signal Flutter to prefer live device state over the schema's initialValue.
        // This is needed because initialValue is set from api['default'] at schema-build time
        // and may be null/empty for controls that have no default — Flutter must fetch the
        // current device state from the API and apply it to state.parameters at runtime.
        $controls[count($controls) - 1]['preferLiveState'] = true;
        $controls[count($controls) - 1]['valueSource']     = 'deviceState';
        if (!isset($pagesByNo[$pageNo])) {
            $pagesByNo[$pageNo] = [
                'id' => 'page_' . $pageNo,
                'pageNo' => $pageNo,
                'title' => $pageName,
                'index' => count($pagesByNo),
                'controlIds' => [],
            ];
        }
        $pagesByNo[$pageNo]['controlIds'][] = $controlId;
        $pagesByNo[$pageNo]['controlCount'] = count($pagesByNo[$pageNo]['controlIds']);
        $pagesByNo[$pageNo]['title'] = $pageName;
    }

    if (empty($pagesByNo) && !empty($pageNames)) {
        foreach ($pageNames as $pageNo => $pageName) {
            $pageNo = trim((string) $pageNo);
            if ($pageNo === '') {
                continue;
            }
            $pagesByNo[$pageNo] = [
                'id' => 'page_' . $pageNo,
                'pageNo' => $pageNo,
                'title' => trim((string) $pageName) !== '' ? trim((string) $pageName) : ('Page ' . $pageNo),
                'index' => count($pagesByNo),
                'controlIds' => [],
                'controlCount' => 0,
            ];
        }
    }

    // A model with no pages yet still needs one shell page so the app can render.
    if ($pagesByNo === []) {
        $pagesByNo['1'] = [
            'id' => 'page_1',
            'pageNo' => '1',
            'title' => 'Page 1',
            'index' => 0,
            'controlIds' => [],
            'controlCount' => 0,
        ];
    }

    uksort($pagesByNo, static function ($a, $b) {
        return ((int) $a) <=> ((int) $b);
    });
    $pages = array_values($pagesByNo);
    foreach ($pages as $index => &$page) {
        $page['index'] = $index;
    }
    unset($page);

    $pageListNodes = [];
    foreach ($pages as $page) {
        $pageListNodes[] = [
            'type' => 'schemaControlList',
            'id' => 'schema_controls_' . $page['pageNo'],
            'properties' => [
                'pageNo' => $page['pageNo'],
                'controlIds' => $page['controlIds'],
                'controlCount' => count($page['controlIds']),
                'renderMode' => 'schemaControls',
                'filterBy' => 'pageNo',
                'cardType' => 'ParameterCard',
            ],
        ];
    }

    $appId = 'efactor_' . $this->deviceUiSchemaParameterSlug($modelno);
    $firstPage = $pages[0] ?? ['id' => 'page_1', 'pageNo' => '1', 'title' => 'Page 1'];

    $schema = [
        'schemaVersion' => '4.0.0',
        'schemaType' => 'device-ui',
        'rendering' => [
            'layout' => 'ui.widgetTree',
            'controls' => 'controls',
            'mode' => 'inline',
            'engine' => 'stac',
        ],
        'meta' => [
            'source' => 'auto',
            'productionReady' => false,
            'renderHint' => 'Auto-generated on model create. Open Design Studio only if you need visual overrides.',
            'app' => [
                'id' => $appId,
                'name' => $modelno . ' Control',
                'modelNo' => $modelno,
                'model_id' => $modelno,
                'nodeId' => $nodeId,
                'entryWidgetId' => 'phone_shell',
            ],
            'totalControlCount' => count($controls),
            'pageCount' => count($pages),
            'links' => [
                'schema' => $schemaApiUrl,
                'parameters' => $schemaApiUrl,
                'deviceInfo' => $schemaApiUrl,
                'brandLogo' => $brandLogo['url'] ?? '',
            ],
        ],
        'assets' => [
            'brandLogo' => $this->schemaDeviceUiBrandLogoAsset($brandLogo),
        ],
        'node_id' => [
            'id' => $nodeId,
            'modelNo' => $modelno,
            'model_id' => $modelno,
            'value' => $nodeId,
            'source' => $nodeId !== '' ? 'api' : 'none',
            'binding' => 'node_id.id',
            'schemaApi' => $schemaApiUrl,
        ],
        'theme' => [
            'mode' => 'light',
            'colors' => [
                'background' => '#F8FAFC',
                'surface' => '#FFFFFF',
                'text' => '#0F172A',
                'accent' => '#2563EB',
                'border' => '#E2E8F0',
            ],
        ],
        'icons' => [
            'default' => 'gauge',
            'size' => 20,
        ],
        'controls' => $controls,
        'pages' => $pages,
        'navigation' => [
            'initial' => $firstPage['id'],
            'pages' => array_map(static function ($page) {
                return $page['id'];
            }, $pages),
        ],
        'actions' => new \stdClass(),
        'state' => [
            'activePageIndex' => 0,
            'activePageId' => $firstPage['id'],
            'activePageNo' => $firstPage['pageNo'],
            'activePageName' => $firstPage['title'],
            'node_id' => $nodeId,
            'online' => $deviceOnline,
            'onlineLabel' => $deviceOnlineLabel,
            'deviceConnectivity' => [
                'online' => $deviceOnline,
                'label' => $deviceOnlineLabel,
                'timestamp' => $deviceOnlineTimestamp,
                'source' => 'node_details.status.connectivity.connected',
            ],
            'device' => [
                'parameters' => $deviceParameters,
                'config' => $deviceConfig,
                'node_id' => $nodeId,
                'primary' => $deviceConfig['devices'][0]['primary'] ?? 'power',
            ],
        ],
        'deviceState' => [
            'parameters' => $deviceParameters,
            'config' => $deviceConfig,
            'node_id' => $nodeId,
            'bindingRoot' => 'state.parameters',
            // Flutter: always load live device state from the API on screen open.
            // Merge the API response into state.parameters so controls show real values.
            'preferLiveState'  => true,
            'liveStateSource'  => 'dataSources.deviceState',
            'liveStatePath'    => 'node_details[0].config.devices[0]',
            'mergeStrategy'    => 'deepMerge',
        ],
        'deviceConfig' => $deviceConfig,
        'node_details' => [[
            'id' => $nodeId,
            'config' => $deviceConfig,
            'role' => 'primary',
            'status' => [
                'connectivity' => [
                    'connected' => $deviceOnline,
                    'timestamp' => $deviceOnlineTimestamp,
                ],
            ],
        ]],
        'nodes' => $nodeId,
        'settings' => [
            'controlOrder' => array_map(static function ($control) {
                return $control['id'];
            }, $controls),
            'parameterIcons' => new \stdClass(),
            'componentOverrides' => new \stdClass(),
        ],
        'ui' => [
            'widgetTree' => [
                'type' => 'column',
                'id' => 'phone_shell',
                'children' => $pageListNodes,
            ],
        ],
        'dataSources' => [
            'deviceInfo' => [
                'type' => 'rest',
                'method' => 'GET',
                'url' => $schemaApiUrl,
                'responsePath' => 'node_details[0].config',
                'load' => 'onAction',
            ],
            'deviceCommands' => [
                'type' => 'adapter',
                'adapter' => 'deviceApi',
                'protocol' => 'mqtt',
                'frameSource' => 'deviceConfig.devices[0].pages',
                'controlFramePath' => 'controls[].deviceFrame',
            ],
            'deviceState' => [
                'type' => 'state',
                'namespace' => 'device.parameters',
            ],
        ],
        'responsive' => $this->deviceUiResponsiveContract(),
        'rendererContract' => [
            'version' => '1.0.0',
            'productionReady' => false,
            'primaryConsumer' => 'flutter',
            'documentEntry' => 'ui.widgetTree',
        ],
    ];

    // Run the same P0 repair as the refresh pipeline so the first generated schema
    // already has consistent flags, ordering and component projections.
    return $this->attachDeviceUiFlutterContract(
        $this->repairDeviceUiSchemaP0Contracts($this->hydrateDeviceUiProtocolMetaFromFrames($schema))
    );
}

private function deviceUiResponsiveContract()
{
    return [
        'version' => '1.0',
        'unit' => 'logicalPx',
        'dimensionPolicy' => 'logical',
        'scaleLegacyDimensions' => false,
        'safeArea' => true,
        'breakpoints' => [
            // 360dp phones with ~24dp padding fall below 360; keep them in compact.
            'narrow' => ['minimumWidth' => 0, 'maximumWidth' => 319],
            'compact' => ['minimumWidth' => 320, 'maximumWidth' => 599],
            'medium' => ['minimumWidth' => 600, 'maximumWidth' => 839],
            'expanded' => ['minimumWidth' => 840],
        ],
        'layouts' => [
            'narrow' => ['maxContentWidth' => 319, 'columns' => 1, 'navigation' => 'pager', 'pagePadding' => 12, 'cardGap' => 10, 'density' => 'comfortable'],
            'compact' => ['maxContentWidth' => 430, 'columns' => 1, 'navigation' => 'pager', 'pagePadding' => 16, 'cardGap' => 12, 'density' => 'comfortable'],
            'medium' => ['maxContentWidth' => 720, 'columns' => 1, 'navigation' => 'pager', 'pagePadding' => 22, 'cardGap' => 14, 'density' => 'comfortable'],
            'expanded' => ['maxContentWidth' => 840, 'columns' => 1, 'navigation' => 'sidebar-pager', 'pagePadding' => 28, 'cardGap' => 16, 'density' => 'comfortable'],
        ],
    ];
}

private function deviceUiSchemaApiUrl($modelno, $nodeId = '')
{
    $base = '';
    if (function_exists('base_url')) {
        $base = rtrim((string) base_url('v8/api_schema.php'), '/');
    }
    if ($base === '') {
        $base = '/v8/api_schema.php';
    }
    $url = $base . '?model_no=' . rawurlencode((string) $modelno);
    $nodeId = trim((string) $nodeId);
    if ($nodeId !== '') {
        $url .= '&node_id=' . rawurlencode($nodeId);
    }

    return $url;
}

private function deviceUiSchemaFlutterWidget($schemaType)
{
    $type = strtolower(str_replace(['_', '-'], '', trim((string) $schemaType)));
    $map = [
        'switch' => 'Switch',
        'elevatedbutton' => 'ElevatedButton',
        'button' => 'PowerButton',
        'slider' => 'Slider',
        'dropdown' => 'DropdownButton',
        'textfield' => 'TextField',
        'value' => 'Text',
        'powergauge' => 'AnimatedRadialGauge',
        'needlegauge' => 'NeedleGauge',
        'segmented' => 'SegmentedControl',
        'lineargauge' => 'LinearGauge',
    ];

    return $map[$type] ?? 'ListTile';
}

/**
 * Interactive widgets that identify which control a widget tree was built for.
 * Text/ListTile are excluded because they also appear as labels and decoration.
 */
private function deviceUiControlWidgetVocabulary()
{
    return [
        'switch' => true,
        'slider' => true,
        'textfield' => true,
        'powerbutton' => true,
        'elevatedbutton' => true,
        'dropdownbutton' => true,
        'animatedradialgauge' => true,
        'needlegauge' => true,
        'segmentedcontrol' => true,
        'lineargauge' => true,
    ];
}

private function deviceUiNormalizeWidgetName($value)
{
    return strtolower(preg_replace('/[^a-z0-9]+/i', '', (string) $value));
}

private function deviceUiCollectWidgetTypes($node, array &$types)
{
    if (!is_array($node)) {
        return;
    }
    if (isset($node['type']) && is_string($node['type'])) {
        $normalized = $this->deviceUiNormalizeWidgetName($node['type']);
        if ($normalized !== '') {
            $types[$normalized] = true;
        }
    }
    foreach ($node as $value) {
        if (is_array($value)) {
            $this->deviceUiCollectWidgetTypes($value, $types);
        }
    }
}

/**
 * Keep componentType, flutterWidget and the nested component projection in agreement.
 * A widget tree cloned from a different component type is dropped so the renderer
 * falls back to componentType instead of drawing the wrong control.
 */
private function syncDeviceUiControlComponentProjection(array &$control, $schemaType, $catalogType = null)
{
    $schemaType = trim((string) $schemaType);
    if ($schemaType === '' || strtolower($schemaType) === 'auto') {
        return;
    }

    $widget = $this->deviceUiSchemaFlutterWidget($schemaType);
    $control['flutterWidget'] = $widget;

    // Only realign the nested projection when one already exists; never introduce it.
    if (!isset($control['component']) || !is_array($control['component'])) {
        return;
    }
    $control['component']['type'] = $schemaType;
    $control['component']['flutterWidget'] = $widget;
    if ($catalogType !== null && trim((string) $catalogType) !== '') {
        $control['component']['catalogComponentType'] = $catalogType;
    }
    if (isset($control['valueType'])) {
        $control['component']['valueType'] = $control['valueType'];
    }

    if (empty($control['component']['widgetTree']) || !is_array($control['component']['widgetTree'])) {
        return;
    }

    $expected = $this->deviceUiNormalizeWidgetName($widget);
    $vocabulary = $this->deviceUiControlWidgetVocabulary();
    if ($expected === '' || !isset($vocabulary[$expected])) {
        return;
    }

    $types = [];
    $this->deviceUiCollectWidgetTypes($control['component']['widgetTree'], $types);
    if (isset($types[$expected])) {
        return;
    }
    foreach (array_keys($types) as $present) {
        if (isset($vocabulary[$present])) {
            // Tree draws a different interactive control than componentType declares.
            unset($control['component']['widgetTree']);
            return;
        }
    }
}

private function deviceUiMetaIsWritable(array $meta)
{
    $source = strtolower(trim((string) ($meta['ui_type_source'] ?? '')));
    $access = strtolower(trim((string) ($meta['access'] ?? '')));
    if (in_array($source, ['response', 'error'], true)) {
        return false;
    }
    if ($access === '') {
        return true;
    }
    if (in_array($access, ['read', 'readonly', 'read-only', 'read_only', 'none'], true)) {
        return false;
    }

    return strpos($access, 'write') !== false;
}

/**
 * Stricter than deviceUiMetaIsWritable: a blank access column means the API never
 * declared the field writable, so a text box must not offer an edit.
 */
private function deviceUiMetaTextIsEditable(array $meta)
{
    $source = strtolower(trim((string) ($meta['ui_type_source'] ?? '')));
    if (in_array($source, ['response', 'error'], true)) {
        return false;
    }

    $access = strtolower(trim((string) ($meta['access'] ?? '')));
    if ($access === '') {
        return false;
    }

    return strpos($access, 'write') !== false || $access === 'w' || $access === 'rw';
}

private function resolveDeviceUiNodeId($modelno, $nodeId = '')
{
    $modelno = trim((string) $modelno);
    $nodeId = trim((string) $nodeId);

    if ($nodeId !== '' && strtolower($nodeId) !== strtolower($modelno)) {
        return $nodeId;
    }

    if ($modelno === '') {
        return $nodeId;
    }

    $apiNode = $this->db->table('product_api')
        ->select('node_id')
        ->where('model_name', $modelno)
        ->where('node_id IS NOT NULL', null, false)
        ->where('node_id !=', '')
        ->orderBy('selected', 'DESC')
        ->orderBy('id', 'DESC')
        ->get()
        ->getRowArray();

    $resolved = trim((string) ($apiNode['node_id'] ?? ''));
    if ($resolved !== '' && strtolower($resolved) !== strtolower($modelno)) {
        return $resolved;
    }

    if ($this->db->tableExists('efactorproduct')) {
        $productNode = $this->db->table('efactorproduct')
            ->select('node_id')
            ->groupStart()
                ->where('model_name', $modelno)
                ->orWhere('model_number', $modelno)
            ->groupEnd()
            ->where('node_id IS NOT NULL', null, false)
            ->where('node_id !=', '')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        $resolved = trim((string) ($productNode['node_id'] ?? ''));
        if ($resolved !== '' && strtolower($resolved) !== strtolower($modelno)) {
            return $resolved;
        }
    }

    if ($this->db->tableExists('device_ui_schemas')) {
        $schemaNode = $this->db->table('device_ui_schemas')
            ->select('node_id')
            ->where('model_name', $modelno)
            ->where('node_id IS NOT NULL', null, false)
            ->where('node_id !=', '')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        $resolved = trim((string) ($schemaNode['node_id'] ?? ''));
        if ($resolved !== '' && strtolower($resolved) !== strtolower($modelno)) {
            return $resolved;
        }
    }

    return $nodeId !== '' ? $nodeId : $modelno;
}

public function getDeviceUiNodeId($modelno, $nodeId = '')
{
    $modelno = trim((string) $modelno);
    $resolved = trim((string) $this->resolveDeviceUiNodeId($modelno, $nodeId));
    if ($resolved === '' || ($modelno !== '' && strtolower($resolved) === strtolower($modelno))) {
        return '';
    }

    return $resolved;
}

private function refreshDeviceUiSchemaApiDetails($modelno, array $schema, $nodeId = '')
{
    $modelno = trim((string) $modelno);
    $nodeId = trim((string) $nodeId);
    if ($modelno === '') {
        return $schema;
    }
    if ($nodeId === '') {
        if (!empty($schema['node_id']['id'])) {
            $nodeId = trim((string) $schema['node_id']['id']);
        } elseif (!empty($schema['meta']['app']['nodeId'])) {
            $nodeId = trim((string) $schema['meta']['app']['nodeId']);
        } elseif (!empty($schema['state']['node_id']) && is_string($schema['state']['node_id'])) {
            $nodeId = trim((string) $schema['state']['node_id']);
        } elseif (!empty($schema['node_details'][0]['id'])) {
            $nodeId = trim((string) $schema['node_details'][0]['id']);
        }
    }
    if ($nodeId === '') {
        $nodeId = $modelno;
    }

    $existingConfig = [];
    $existingStatus = [];
    if ($nodeId !== '' && isset($schema['node_details']) && is_array($schema['node_details'])) {
        $storedDetail = $this->findDeviceUiNodeDetail($schema['node_details'], $nodeId);
        if ($storedDetail && isset($storedDetail['config']) && is_array($storedDetail['config'])) {
            $existingConfig = $storedDetail['config'];
        }
        if ($storedDetail && isset($storedDetail['status']) && is_array($storedDetail['status'])) {
            $existingStatus = $storedDetail['status'];
        }
    }
    if (!$existingConfig && isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])) {
        $existingConfig = $schema['deviceConfig'];
    }

    $nodeId = $this->resolveDeviceUiNodeId($modelno, $nodeId);
    $infoPayload = $this->getDeviceUiInfo($modelno, $nodeId);
    $liveConfig = is_array($infoPayload) && isset($infoPayload['deviceConfig']) && is_array($infoPayload['deviceConfig'])
        ? $infoPayload['deviceConfig']
        : [];
    $deviceConfig = $this->mergeDeviceUiNodeConfig(
        $liveConfig,
        $existingConfig,
        $modelno,
        $nodeId,
        is_array($infoPayload) && isset($infoPayload['info']) && is_array($infoPayload['info']) ? $infoPayload['info'] : []
    );
    if (empty($deviceConfig)) {
        try {
            $schema = $this->refreshDeviceUiSchemaControlsFromApi($modelno, $schema);
            return $this->syncDeviceUiSchemaControlReferences($schema);
        } catch (\Throwable $exception) {
            return $schema;
        }
    }

    $live = $this->loadDeviceUiLiveProductContext($modelno, $nodeId);
    $connected = !empty($live['connected']);
    $liveNodeId = $nodeId;
    $outputNodeId = !empty($live['output_node_id']) ? $live['output_node_id'] : $liveNodeId;
    $schema['deviceConfig'] = $deviceConfig;
    $schema['nodes'] = $outputNodeId;
    if (isset($schema['state']['device']) && is_array($schema['state']['device'])) {
        $schema['state']['device']['config'] = $deviceConfig;
        $schema['state']['device']['node_id'] = $liveNodeId;
    }
    if (isset($schema['deviceState']) && is_array($schema['deviceState'])) {
        $schema['deviceState']['config'] = $deviceConfig;
        $schema['deviceState']['node_id'] = $liveNodeId;
    }
    if (isset($schema['state']) && is_array($schema['state'])) {
        $schema['state']['node_id'] = $outputNodeId;
    }

    $testNodes = $this->fetchDeviceUiTestNodes($nodeId);
    if ($this->isDeviceUiTestNodesPayload($testNodes, $nodeId)) {
        $schema = $this->applyDeviceUiTestNodesToSchema($schema, $testNodes, $nodeId);
        $outputNodeId = isset($schema['nodes']) && $schema['nodes'] !== ''
            ? $schema['nodes']
            : $outputNodeId;
    } else {
        $schema['node_details'] = [[
            'id' => $liveNodeId,
            'config' => $deviceConfig,
            'role' => 'primary',
            'status' => [
                'connectivity' => [
                    'connected' => $connected,
                    'timestamp' => (int) (($existingStatus['connectivity']['timestamp'] ?? 0) ?: round(microtime(true) * 1000)),
                ],
            ],
        ]];
    }

    if (!empty($deviceConfig) && isset($schema['node_details'][0]) && is_array($schema['node_details'][0])) {
        $schema['node_details'][0]['id'] = $liveNodeId;
        $schema['node_details'][0]['config'] = $deviceConfig;
    }
    $schema['deviceConfig'] = $deviceConfig;
    $schema['nodes'] = $outputNodeId;
    $schema = $this->refreshDeviceUiSchemaControlsFromApi($modelno, $schema);

    if (!isset($schema['meta']) || !is_array($schema['meta'])) {
        $schema['meta'] = [];
    }
    if (!isset($schema['meta']['app']) || !is_array($schema['meta']['app'])) {
        $schema['meta']['app'] = [];
    }
    $schema['meta']['app']['modelNo'] = $modelno;
    $schema['meta']['app']['nodeId'] = $nodeId;
    $schema = $this->applyDeviceUiBrandLogoToSchema(
        $schema,
        $this->resolveDeviceUiBrandLogoAsset($modelno, $nodeId)
    );
    $schema = $this->stripDeviceUiNameCards($schema);
    $schema = $this->stripDeviceUiSlideHeroes($schema);
    $schema = $this->syncDeviceUiPreviewContracts($schema);

    try {
        return $this->syncDeviceUiSchemaControlReferences($schema);
    } catch (\Throwable $exception) {
        return $schema;
    }
}

private function mergeDeviceUiAttributeLists(array $existing, array $fresh)
{
    $merged = [];
    $seen = [];
    foreach (array_merge($existing, $fresh) as $attribute) {
        if (!is_array($attribute)) {
            continue;
        }
        $name = trim((string) ($attribute['name'] ?? ''));
        if ($name === '') {
            continue;
        }
        $key = strtolower($name);
        $clean = [
            'name' => $name,
            'value' => $attribute['value'] ?? '',
        ];
        if (isset($seen[$key])) {
            $merged[$seen[$key]] = $clean;
            continue;
        }
        $seen[$key] = count($merged);
        $merged[] = $clean;
    }

    return $merged;
}

private function refreshDeviceUiSchemaControlsFromApi($modelno, array $schema)
{
    // Allow empty controls so newly linked parameters can be added from mapping.
    if (!isset($schema['controls']) || !is_array($schema['controls'])) {
        $schema['controls'] = [];
    }

    $mapping = $this->getPageParameterMapping($modelno);
    $rows = is_array($mapping['rows'] ?? null) ? $mapping['rows'] : [];
    if (empty($rows)) {
        return $schema;
    }

    $apiByKey = [];
    $apiByParameter = [];
    $allowedControlKeys = [];
    $sequenceByKey = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $parameterName = trim((string) ($row['parameter'] ?? ''));
        if ($parameterName === '') {
            continue;
        }
        $pageNo = trim((string) ($row['page_no'] ?? '1'));
        $key = $this->pageParameterLinkKey($pageNo, $parameterName);
        $allowedControlKeys[$key] = true;
        $sequenceByKey[$key] = max(1, (int) ($row['sequence'] ?? 0));
        if (empty($row['api']) || !is_array($row['api'])) {
            continue;
        }
        $apiByKey[$key] = $row['api'];
        $this->indexDeviceUiApiMetadata($apiByParameter, $parameterName, $row['api']);
    }

    $expandedControls = [];
    foreach ($schema['controls'] as $index => $control) {
        if (!is_array($control)) {
            continue;
        }

        $parameterName = trim((string) ($control['parameterName'] ?? $control['parameter'] ?? $control['label'] ?? ''));
        if ($parameterName === '') {
            $expandedControls[] = $control;
            continue;
        }

        $pageNo = trim((string) ($control['pageNo'] ?? '1'));
        $lookupKey = $this->pageParameterLinkKey($pageNo, $parameterName);
        if (isset($sequenceByKey[$lookupKey])) {
            $control['sequence'] = (int) $sequenceByKey[$lookupKey];
        }
        $apiMeta = $apiByKey[$lookupKey] ?? $this->resolveDeviceUiApiMetadata($apiByParameter, $parameterName);
        if (!is_array($apiMeta) || empty($apiMeta)) {
            $apiMeta = isset($control['protocolMeta']) && is_array($control['protocolMeta'])
                ? $control['protocolMeta']
                : [];
        }
        if (empty($apiMeta)) {
            $expandedControls[] = $control;
            continue;
        }

        $apiComponents = isset($apiMeta['ui_components']) && is_array($apiMeta['ui_components']) ? $apiMeta['ui_components'] : [];
        if (empty($apiComponents)) {
            $apiComponents = [$apiMeta];
        }

        $usableComponents = [];
        foreach ($apiComponents as $componentMeta) {
            if (!is_array($componentMeta)) {
                continue;
            }
            if (!$this->isDeviceUiTypeUsable($componentMeta['ui_type'] ?? '')) {
                continue;
            }
            $usableComponents[] = $componentMeta;
        }
        if (empty($usableComponents)) {
            $usableComponents = [$apiMeta];
        }

        $hasMultipleApiComponents = count($usableComponents) > 1;
        foreach ($usableComponents as $componentIndex => $componentMeta) {
            $updatedControl = $control;
            if (isset($sequenceByKey[$lookupKey])) {
                $updatedControl['sequence'] = (int) $sequenceByKey[$lookupKey];
            }
            $updatedControl['apiComponentIndex'] = $componentIndex;
            $source = trim((string) ($componentMeta['ui_type_source'] ?? ($apiMeta['ui_type_source'] ?? 'api')));
            $fieldIndex = $componentMeta['field_index'] ?? ($apiMeta['data_field_index'] ?? null);
            $componentKey = preg_replace('/[^a-z0-9]+/', '-', strtolower($source . '-' . (string) $fieldIndex . '-' . (string) ($componentMeta['ui_type'] ?? '')));
            $componentKey = trim($componentKey, '-');
            if ($componentKey === '') {
                $componentKey = 'api-' . ($componentIndex + 1);
            }

            $fieldName = trim((string) ($componentMeta['field_description'] ?? ''));
            $sourceLabel = '';
            if ($hasMultipleApiComponents) {
                $sourceLabel = $fieldName !== ''
                    ? $fieldName
                    : ($source !== '' ? ucfirst($source) : ('API ' . ($componentIndex + 1)));
            }
            $existingId = trim((string) ($updatedControl['id'] ?? ''));
            $existingParameterKey = trim((string) ($updatedControl['parameterKey'] ?? ''));
            $oldBinding = trim((string) ($updatedControl['binding'] ?? ''));
            $rewriteIdentity = $hasMultipleApiComponents || $existingId === '';
            $controlKey = $rewriteIdentity
                ? $this->deviceUiSchemaControlKey($pageNo, $parameterName, $componentKey, $hasMultipleApiComponents)
                : $existingId;
            $parameterKey = $rewriteIdentity || $existingParameterKey === ''
                ? $this->deviceUiSchemaParameterSlug($controlKey)
                : $existingParameterKey;
            $newBinding = $rewriteIdentity || $oldBinding === ''
                ? $this->deviceUiSchemaBinding($parameterKey)
                : $oldBinding;
            $updatedControl['id'] = $controlKey;
            $updatedControl['parameterKey'] = $parameterKey;
            $updatedControl['binding'] = $newBinding;
            $updatedControl['flutterBinding'] = 'state.parameters.' . $parameterKey . '.value';
            $updatedControl['label'] = $hasMultipleApiComponents
                ? $this->deviceUiMultiComponentLabel($componentMeta, $parameterName)
                : trim($parameterName . ($sourceLabel !== '' ? ' ' . $sourceLabel : ''));
            $updatedControl['apiComponentKey'] = $componentKey;
            $updatedControl['apiComponentIndex'] = $componentIndex;

            if ($oldBinding !== '' && $oldBinding !== $newBinding) {
                $this->replaceDeviceUiControlBinding($updatedControl, $oldBinding, $newBinding);
            }

            $component = $this->deviceUiSchemaComponentFromApi($componentMeta['ui_type'] ?? ($apiMeta['ui_type'] ?? ''), $parameterName);
            // The linked API ui type is the frame contract. A stale studio pick
            // (Get Status dropdown saved as a button) must not hide that control.
            if ($component['schema'] !== 'auto') {
                $updatedControl['componentType'] = $component['schema'];
                $updatedControl['controlType'] = $component['schema'];
                $updatedControl['variant'] = $component['schema'];
                $updatedControl['componentTypeSource'] = 'api';
                $updatedControl['catalogComponentType'] = $component['catalog'];
                if (isset($updatedControl['override']) && is_array($updatedControl['override'])) {
                    $updatedControl['override']['componentType'] = $component['catalog'] !== 'auto' ? $component['catalog'] : $component['schema'];
                }
            } elseif (empty($updatedControl['componentTypeSource'])) {
                $updatedControl['componentTypeSource'] = 'api';
            }
            $updatedControl['valueType'] = $this->deviceUiSchemaValueType($updatedControl['catalogComponentType'] ?? $updatedControl['componentType'] ?? 'auto');
            $this->syncDeviceUiControlComponentProjection(
                $updatedControl,
                $updatedControl['componentType'] ?? 'auto',
                $updatedControl['catalogComponentType'] ?? null
            );

            // Re-derive the write contract from the refreshed access mode instead of
            // inheriting stale flags from the previous control.
            $refreshedWritable = $this->deviceUiMetaIsWritable([
                'ui_type_source' => $source !== '' ? $source : ($apiMeta['ui_type_source'] ?? ''),
                'access' => $componentMeta['access'] ?? ($apiMeta['access'] ?? null),
            ]);
            $updatedControl['writable'] = $refreshedWritable;
            $updatedControl['readOnly'] = !$refreshedWritable;
            $updatedControl['interactive'] = $refreshedWritable;
            $updatedControl['events'] = $refreshedWritable
                ? $this->deviceUiSchemaEventsForControl($updatedControl['componentType'] ?? 'auto', $newBinding, $updatedControl['events'] ?? [])
                : [];

            if (!isset($updatedControl['protocolMeta']) || !is_array($updatedControl['protocolMeta'])) {
                $updatedControl['protocolMeta'] = [];
            }
            $updatedControl['protocolMeta'] = array_merge($updatedControl['protocolMeta'], [
                'command_id' => $this->deviceUiFirstNonEmpty($componentMeta['command_id'] ?? null, $apiMeta['command_id'] ?? null, $updatedControl['protocolMeta']['command_id'] ?? null),
                'function_code' => $this->deviceUiFirstNonEmpty($componentMeta['function_code'] ?? null, $apiMeta['function_code'] ?? null, $updatedControl['protocolMeta']['function_code'] ?? null),
                'action_id' => $this->deviceUiFirstNonEmpty($componentMeta['action_id'] ?? null, $apiMeta['action_id'] ?? null, $updatedControl['protocolMeta']['action_id'] ?? null),
                'ui_type' => $componentMeta['ui_type'] ?? ($apiMeta['ui_type'] ?? null),
                'ui_type_source' => $source !== '' ? $source : ($apiMeta['ui_type_source'] ?? null),
                'field_index' => $fieldIndex,
                'field_description' => $componentMeta['field_description'] ?? null,
                'access' => $componentMeta['access'] ?? ($apiMeta['access'] ?? null),
                'items' => is_array($componentMeta['items'] ?? null) ? $componentMeta['items'] : (is_array($apiMeta['items'] ?? null) ? $apiMeta['items'] : []),
                'item_values' => is_array($componentMeta['item_values'] ?? null) ? $componentMeta['item_values'] : (is_array($apiMeta['item_values'] ?? null) ? $apiMeta['item_values'] : []),
                'sno_asperui' => is_array($componentMeta['sno_asperui'] ?? null) ? $componentMeta['sno_asperui'] : (is_array($apiMeta['sno_asperui'] ?? null) ? $apiMeta['sno_asperui'] : []),
                'minimum' => $componentMeta['minimum'] ?? ($apiMeta['minimum'] ?? null),
                'maximum' => $componentMeta['maximum'] ?? ($apiMeta['maximum'] ?? null),
                'step' => $componentMeta['step'] ?? ($apiMeta['step'] ?? null),
                'default' => $componentMeta['default'] ?? ($apiMeta['default'] ?? null),
                'frame' => is_array($apiMeta['frame'] ?? null) ? $apiMeta['frame'] : null,
                'ui_components' => $apiComponents,
            ]);
            if (is_array($apiMeta['frame'] ?? null)) {
                $updatedControl['deviceFrame'] = $apiMeta['frame'];
            }
            $this->applyDeviceUiChoiceContract($updatedControl);
            $expandedControls[] = $updatedControl;
        }
    }

    $presentControlKeys = [];
    foreach ($expandedControls as $control) {
        if (!is_array($control)) {
            continue;
        }
        $name = trim((string) ($control['parameterName'] ?? $control['parameter'] ?? ''));
        if ($name === '') {
            continue;
        }
        $pageNo = trim((string) ($control['pageNo'] ?? '1'));
        $presentControlKeys[$this->pageParameterLinkKey($pageNo, $name)] = true;
    }

    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $parameterName = trim((string) ($row['parameter'] ?? ''));
        if ($parameterName === '') {
            continue;
        }
        $pageNo = trim((string) ($row['page_no'] ?? '1'));
        $lookupKey = $this->pageParameterLinkKey($pageNo, $parameterName);
        if (isset($presentControlKeys[$lookupKey])) {
            continue;
        }

        $apiMeta = is_array($row['api'] ?? null) ? $row['api'] : $this->resolveDeviceUiApiMetadata($apiByParameter, $parameterName);
        $apiComponents = isset($apiMeta['ui_components']) && is_array($apiMeta['ui_components']) ? $apiMeta['ui_components'] : [];
        if (empty($apiComponents) && !empty($apiMeta)) {
            $apiComponents = [$apiMeta];
        }

        $usableComponents = [];
        foreach ($apiComponents as $componentMeta) {
            if (is_array($componentMeta) && $this->isDeviceUiTypeUsable($componentMeta['ui_type'] ?? '')) {
                $usableComponents[] = $componentMeta;
            }
        }
        if (empty($usableComponents)) {
            $usableComponents = [[]];
        }

        $hasMultipleApiComponents = count($usableComponents) > 1;
        foreach ($usableComponents as $componentIndex => $componentMeta) {
            $source = trim((string) ($componentMeta['ui_type_source'] ?? ($apiMeta['ui_type_source'] ?? 'api')));
            $fieldIndex = $componentMeta['field_index'] ?? ($apiMeta['data_field_index'] ?? null);
            $componentKey = preg_replace('/[^a-z0-9]+/', '-', strtolower($source . '-' . (string) $fieldIndex . '-' . (string) ($componentMeta['ui_type'] ?? '')));
            $componentKey = trim($componentKey, '-');
            if ($componentKey === '') {
                $componentKey = 'api-' . ($componentIndex + 1);
            }
            $fieldName = trim((string) ($componentMeta['field_description'] ?? ''));
            $sourceLabel = '';
            if ($hasMultipleApiComponents) {
                $sourceLabel = $fieldName !== ''
                    ? $fieldName
                    : ($source !== '' ? ucfirst($source) : ('API ' . ($componentIndex + 1)));
            }
            $component = $this->deviceUiSchemaComponentFromApi($componentMeta['ui_type'] ?? ($apiMeta['ui_type'] ?? ''), $parameterName);
            $schemaType = $component['schema'] !== 'auto' ? $component['schema'] : 'value';
            $controlKey = $this->deviceUiSchemaControlKey($pageNo, $parameterName, $componentKey, $hasMultipleApiComponents);
            $parameterKey = $this->deviceUiSchemaParameterSlug($controlKey);
            $binding = $this->deviceUiSchemaBinding($parameterKey);

            $control = [
                'id' => $controlKey,
                'parameterKey' => $parameterKey,
                'label' => $hasMultipleApiComponents
                    ? $this->deviceUiMultiComponentLabel($componentMeta, $parameterName)
                    : trim($parameterName . ($sourceLabel !== '' ? ' ' . $sourceLabel : '')),
                'parameter' => $parameterName,
                'parameterName' => $parameterName,
                'telemetryKey' => $parameterName,
                'pageNo' => $pageNo,
                'pageName' => $row['page_name'] ?? ('Page ' . $pageNo),
                'sequence' => (int) ($row['sequence'] ?? (count($expandedControls) + 1)),
                'componentType' => $schemaType,
                'controlType' => $schemaType,
                'variant' => $schemaType,
                'componentTypeSource' => 'api',
                'catalogComponentType' => $component['catalog'],
                'valueType' => $this->deviceUiSchemaValueType($component['catalog'] !== 'auto' ? $component['catalog'] : $schemaType),
                'binding' => $binding,
                'flutterBinding' => 'state.parameters.' . $parameterKey . '.value',
                'events' => $this->deviceUiSchemaEventsForControl($schemaType, $binding),
                'apiComponentKey' => $componentKey,
                'apiComponentIndex' => $componentIndex,
                'protocolMeta' => [
                    'command_id' => $apiMeta['command_id'] ?? null,
                    'function_code' => $apiMeta['function_code'] ?? null,
                    'action_id' => $apiMeta['action_id'] ?? null,
                    'ui_type' => $componentMeta['ui_type'] ?? ($apiMeta['ui_type'] ?? null),
                    'ui_type_source' => $source !== '' ? $source : ($apiMeta['ui_type_source'] ?? null),
                    'field_index' => $fieldIndex,
                    'field_description' => $componentMeta['field_description'] ?? null,
                    'access' => $componentMeta['access'] ?? ($apiMeta['access'] ?? null),
                    'items' => is_array($componentMeta['items'] ?? null) ? $componentMeta['items'] : (is_array($apiMeta['items'] ?? null) ? $apiMeta['items'] : []),
                    'item_values' => is_array($componentMeta['item_values'] ?? null) ? $componentMeta['item_values'] : (is_array($apiMeta['item_values'] ?? null) ? $apiMeta['item_values'] : []),
                    'sno_asperui' => is_array($componentMeta['sno_asperui'] ?? null) ? $componentMeta['sno_asperui'] : (is_array($apiMeta['sno_asperui'] ?? null) ? $apiMeta['sno_asperui'] : []),
                    'minimum' => $componentMeta['minimum'] ?? ($apiMeta['minimum'] ?? null),
                    'maximum' => $componentMeta['maximum'] ?? ($apiMeta['maximum'] ?? null),
                    'step' => $componentMeta['step'] ?? ($apiMeta['step'] ?? null),
                    'default' => $componentMeta['default'] ?? ($apiMeta['default'] ?? null),
                    'frame' => is_array($apiMeta['frame'] ?? null) ? $apiMeta['frame'] : null,
                    'ui_components' => $apiComponents,
                ],
            ];
            $newWritable = $this->deviceUiMetaIsWritable([
                'ui_type_source' => $source !== '' ? $source : ($apiMeta['ui_type_source'] ?? ''),
                'access' => $componentMeta['access'] ?? ($apiMeta['access'] ?? null),
            ]);
            $control['visible'] = true;
            $control['writable'] = $newWritable;
            $control['readOnly'] = !$newWritable;
            $control['interactive'] = $newWritable;
            $control['flutterWidget'] = $this->deviceUiSchemaFlutterWidget($schemaType);
            if (!$newWritable) {
                $control['events'] = [];
            }
            if (is_array($apiMeta['frame'] ?? null)) {
                $control['deviceFrame'] = $apiMeta['frame'];
            }
            $this->applyDeviceUiChoiceContract($control);
            if ($control['catalogComponentType'] === null || $control['catalogComponentType'] === 'auto') {
                unset($control['catalogComponentType']);
            }
            $expandedControls[] = $control;
        }
        $presentControlKeys[$lookupKey] = true;
    }
    $schema['controls'] = $expandedControls;

    $schema['controls'] = array_values(array_filter($schema['controls'], function ($control) use ($allowedControlKeys) {
        if (!is_array($control)) {
            return false;
        }
        $parameterName = trim((string) ($control['parameterName'] ?? $control['parameter'] ?? $control['label'] ?? ''));
        if ($parameterName === '') {
            return true;
        }
        $pageNo = trim((string) ($control['pageNo'] ?? '1'));
        return empty($allowedControlKeys) || isset($allowedControlKeys[$this->pageParameterLinkKey($pageNo, $parameterName)]);
    }));
    $schema['controls'] = $this->dedupeDeviceUiSchemaControls($schema['controls']);
    usort($schema['controls'], static function ($a, $b) {
        if (!is_array($a) || !is_array($b)) {
            return 0;
        }
        $pageCompare = ((int) ($a['pageNo'] ?? 0)) <=> ((int) ($b['pageNo'] ?? 0));
        if ($pageCompare !== 0) {
            return $pageCompare;
        }
        $sequenceCompare = ((int) ($a['sequence'] ?? 0)) <=> ((int) ($b['sequence'] ?? 0));
        if ($sequenceCompare !== 0) {
            return $sequenceCompare;
        }

        return ((int) ($a['apiComponentIndex'] ?? 0)) <=> ((int) ($b['apiComponentIndex'] ?? 0));
    });
    $schema['controls'] = array_values($schema['controls']);

    return $schema;
}


// --------
private function deviceUiSchemaControlKey($pageNo, $parameterName, $componentKey = '', $includeComponent = true)
{
    $base = strtolower(trim((string) $pageNo)) . ':' . strtolower(trim((string) $parameterName));
    $componentKey = trim((string) $componentKey);
    if ($includeComponent && $componentKey !== '') {
        $base .= ':' . strtolower($componentKey);
    }

    return preg_replace('/[^a-z0-9:_-]+/', '_', $base);
}

private function deviceUiSchemaParameterSlug($value)
{
    $slug = strtolower(trim((string) $value));
    $slug = preg_replace('/[^a-z0-9_]+/', '_', $slug);
    $slug = trim((string) $slug, '_');

    return $slug !== '' ? $slug : 'parameter';
}

private function deviceUiSchemaBinding($controlKey)
{
    return 'device.parameters.' . $this->deviceUiSchemaParameterSlug($controlKey);
}

private function syncDeviceUiSchemaControlReferences(array $schema)
{
    if (empty($schema['controls']) || !is_array($schema['controls'])) {
        return $schema;
    }

    $controlsByPage = [];
    $controlIds = [];
    $allowedSettingKeys = [];
    $parameterValues = [];
    $idToParameterKey = [];

    foreach ($schema['controls'] as $index => &$control) {
        if (!is_array($control)) {
            continue;
        }

        $id = trim((string) ($control['id'] ?? ''));
        if ($id === '') {
            $pageNo = trim((string) ($control['pageNo'] ?? '1'));
            $name = trim((string) ($control['parameterName'] ?? $control['parameter'] ?? $control['label'] ?? ('control_' . ($index + 1))));
            $componentKey = trim((string) ($control['apiComponentKey'] ?? ''));
            $id = $this->deviceUiSchemaControlKey($pageNo, $name, $componentKey, $componentKey !== '');
            $control['id'] = $id;
        }

        $parameterKey = trim((string) ($control['parameterKey'] ?? ''));
        if ($parameterKey === '') {
            $parameterKey = $this->deviceUiSchemaParameterSlug($id);
            $control['parameterKey'] = $parameterKey;
        }

        $expectedBinding = $this->deviceUiSchemaBinding($parameterKey);
        $oldBinding = trim((string) ($control['binding'] ?? ''));
        if ($oldBinding === '' || $oldBinding !== $expectedBinding) {
            $control['binding'] = $expectedBinding;
            if ($oldBinding !== '' && $oldBinding !== $expectedBinding) {
                $this->replaceDeviceUiControlBinding($control, $oldBinding, $expectedBinding);
            }
        }
        $control['flutterBinding'] = 'state.parameters.' . $parameterKey . '.value';

        $pageNo = trim((string) ($control['pageNo'] ?? '1'));
        if (!isset($controlsByPage[$pageNo])) {
            $controlsByPage[$pageNo] = [];
        }
        $controlsByPage[$pageNo][] = $id;
        $controlIds[] = $id;
        $idToParameterKey[$id] = $parameterKey;

        $allowedSettingKeys[$id] = true;
        $allowedSettingKeys[$parameterKey] = true;
        $name = trim((string) ($control['parameterName'] ?? $control['parameter'] ?? ''));
        if ($pageNo !== '' && $name !== '') {
            $allowedSettingKeys[$this->pageParameterLinkKey($pageNo, $name)] = true;
            $allowedSettingKeys[$pageNo . ':' . $name] = true;
        }
        $componentKey = trim((string) ($control['apiComponentKey'] ?? ''));
        if ($componentKey !== '' && $name !== '') {
            $longId = $this->deviceUiSchemaControlKey($pageNo, $name, $componentKey, true);
            $allowedSettingKeys[$longId] = true;
            $allowedSettingKeys[$this->deviceUiSchemaParameterSlug($longId)] = true;
        }

        $value = $control['initialValue'] ?? ($control['value'] ?? null);
        if (isset($control['component']) && is_array($control['component']) && array_key_exists('initialValue', $control['component'])) {
            $value = $control['component']['initialValue'];
        }
        $parameterValues[$parameterKey] = $value;
    }
    unset($control);

    if (!empty($schema['pages']) && is_array($schema['pages'])) {
        foreach ($schema['pages'] as &$page) {
            if (!is_array($page)) {
                continue;
            }
            $pageNo = trim((string) ($page['pageNo'] ?? ''));
            if ($pageNo !== '' && isset($controlsByPage[$pageNo])) {
                $page['controlIds'] = array_values($controlsByPage[$pageNo]);
                $page['controlCount'] = count($page['controlIds']);
            }
        }
        unset($page);
    }

    foreach (['navigation', 'state', 'dashboard'] as $section) {
        if (empty($schema[$section]['pages']) || !is_array($schema[$section]['pages'])) {
            continue;
        }
        foreach ($schema[$section]['pages'] as &$page) {
            if (!is_array($page)) {
                continue;
            }
            $pageNo = trim((string) ($page['pageNo'] ?? ''));
            if ($pageNo === '' || !isset($controlsByPage[$pageNo])) {
                continue;
            }
            if (array_key_exists('controlIds', $page)) {
                $page['controlIds'] = array_values($controlsByPage[$pageNo]);
            }
            $page['controlCount'] = count($controlsByPage[$pageNo]);
        }
        unset($page);
    }

    if (isset($schema['navigation']['graph']['routes']) && is_array($schema['navigation']['graph']['routes'])) {
        foreach ($schema['navigation']['graph']['routes'] as &$route) {
            if (!is_array($route)) {
                continue;
            }
            $pageNo = trim((string) ($route['pageNo'] ?? ''));
            if ($pageNo !== '' && isset($controlsByPage[$pageNo])) {
                $route['controlIds'] = array_values($controlsByPage[$pageNo]);
            }
        }
        unset($route);
    }

    if (!isset($schema['settings']) || !is_array($schema['settings'])) {
        $schema['settings'] = [];
    }
    $schema['settings']['controlOrder'] = $controlIds;
    $schema['settings']['parameterIcons'] = $this->filterDeviceUiSchemaMap($schema['settings']['parameterIcons'] ?? [], $allowedSettingKeys);
    $schema['settings']['componentOverrides'] = $this->filterDeviceUiSchemaMap($schema['settings']['componentOverrides'] ?? [], $allowedSettingKeys);
    $schema['settings']['componentTypes'] = $this->filterDeviceUiSchemaMap($schema['settings']['componentTypes'] ?? [], $allowedSettingKeys);

    if (isset($schema['ui']) && is_array($schema['ui'])) {
        $this->replaceDeviceUiSchemaControlLists($schema['ui'], $controlsByPage);
    }
    if (isset($schema['extensions']['serverDrivenUi']) && is_array($schema['extensions']['serverDrivenUi'])) {
        $this->replaceDeviceUiSchemaControlLists($schema['extensions']['serverDrivenUi'], $controlsByPage);
    }
    if (isset($schema['extensions']['universalUi']['ui']) && is_array($schema['extensions']['universalUi']['ui'])) {
        $this->replaceDeviceUiSchemaControlLists($schema['extensions']['universalUi']['ui'], $controlsByPage);
    }
    if (isset($schema['rendering']) && is_array($schema['rendering'])) {
        $this->replaceDeviceUiSchemaControlLists($schema['rendering'], $controlsByPage);
    }

    if (isset($schema['state']['device']) && is_array($schema['state']['device'])) {
        $existing = isset($schema['state']['device']['parameters']) && is_array($schema['state']['device']['parameters'])
            ? $schema['state']['device']['parameters']
            : [];
        $schema['state']['device']['parameters'] = $this->remapDeviceUiFlatParameterBag($existing, $parameterValues);
    }
    if (isset($schema['deviceState']) && is_array($schema['deviceState'])) {
        $existing = isset($schema['deviceState']['parameters']) && is_array($schema['deviceState']['parameters'])
            ? $schema['deviceState']['parameters']
            : [];
        $schema['deviceState']['parameters'] = $this->remapDeviceUiFlatParameterBag($existing, $parameterValues);
    }

    $typedRemap = null;
    if (isset($schema['state']['parameters']) && is_array($schema['state']['parameters'])) {
        $typedRemap = $this->remapDeviceUiTypedParameterBag($schema['state']['parameters'], $schema['controls']);
        $schema['state']['parameters'] = $typedRemap['bag'];
    }

    if (isset($schema['permissions']['controls']) && is_array($schema['permissions']['controls'])) {
        $schema['permissions']['controls'] = $this->filterDeviceUiSchemaMap($schema['permissions']['controls'], $allowedSettingKeys);
    }

    $schema = $this->hydrateDeviceUiSchemaConfigs($schema);
    $schema = $this->attachCanonicalDeviceUiFrames($schema);
    $schema = $this->hydrateDeviceUiProtocolMetaFromFrames($schema);
    $schema = $this->repairDeviceUiSchemaP0Contracts($schema);
    $schema = $this->syncDeviceUiPreviewContracts($schema);
    $schema = $this->attachDeviceUiFlutterContract($schema);
    $schema = $this->rewriteDeviceUiSchemaLinkNodeIds($schema);

    $hasWidgetTree = !empty($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree']);
    if (isset($schema['rendererContract']) && is_array($schema['rendererContract'])) {
        $schema['rendererContract']['documentEntry'] = 'ui.widgetTree';
        $schema['rendererContract']['productionReady'] = !empty($schema['rendererContract']['productionReady']) && $hasWidgetTree;
    }
    if (isset($schema['meta']) && is_array($schema['meta'])) {
        $schema['meta']['flutterEntry'] = 'ui.widgetTree';
        $schema['meta']['productionReady'] = !empty($schema['meta']['productionReady']) && $hasWidgetTree;
        if (!$hasWidgetTree) {
            $schema['meta']['renderHint'] = 'Production Flutter document missing at ui.widgetTree.';
        }
    }

    $hasSdui = !empty($schema['extensions']['serverDrivenUi']) && is_array($schema['extensions']['serverDrivenUi']);
    if ($hasSdui) {
        $sdui = &$schema['extensions']['serverDrivenUi'];
        if (isset($sdui['state']['parameters']) && is_array($sdui['state']['parameters'])) {
            $sduiRemap = $this->remapDeviceUiTypedParameterBag($sdui['state']['parameters'], $schema['controls']);
            $sdui['state']['parameters'] = $sduiRemap['bag'];
            if (!empty($sduiRemap['oldToNew'])) {
                $this->rewriteDeviceUiSchemaStateKeys($sdui, $sduiRemap['oldToNew']);
            }
        }
        unset($sdui);
    }

    $contract = $this->validateDeviceUiSchemaContract($schema);
    if (!isset($schema['meta']) || !is_array($schema['meta'])) {
        $schema['meta'] = [];
    }
    $schema['meta']['canonicalValidation'] = $contract;
    if (!empty($contract['errors'])) {
        $schema['meta']['productionReady'] = false;
        if (isset($schema['rendererContract']) && is_array($schema['rendererContract'])) {
            $schema['rendererContract']['productionReady'] = false;
        }
        $schema['meta']['renderHint'] = 'Schema contract errors remain — check meta.canonicalValidation.errors.';
    } elseif ($hasWidgetTree) {
        $schema['meta']['productionReady'] = true;
        $schema['meta']['flutterEntry'] = 'ui.widgetTree';
        if (isset($schema['rendererContract']) && is_array($schema['rendererContract'])) {
            $schema['rendererContract']['productionReady'] = true;
            $schema['rendererContract']['documentEntry'] = 'ui.widgetTree';
        }
        $schema['meta']['renderHint'] = 'Production Flutter document attached at ui.widgetTree.';
        if (isset($schema['meta']['flutterValidation']) && is_array($schema['meta']['flutterValidation'])) {
            $schema['meta']['flutterValidation']['valid'] = true;
            $schema['meta']['flutterValidation']['errors'] = [];
        }
    }

    return $schema;
}

private function deviceUiControlIsWritable(array $control)
{
    $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
    $source = strtolower(trim((string) ($meta['ui_type_source'] ?? '')));
    $access = strtolower(trim((string) ($meta['access'] ?? '')));
    if (in_array($source, ['response', 'error'], true)) {
        return false;
    }
    if ($access === '') {
        return true;
    }
    if (in_array($access, ['read', 'readonly', 'read-only', 'read_only', 'none'], true)) {
        return false;
    }
    return strpos($access, 'write') !== false;
}

private function collectDeviceUiConfigFrames(array $schema, array &$framesByPage = null)
{
    $frames = [];
    if (!is_array($framesByPage)) {
        $framesByPage = [];
    }
    $configs = [];
    if (isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])) {
        $configs[] = $schema['deviceConfig'];
    }
    if (isset($schema['node_details'][0]['config']) && is_array($schema['node_details'][0]['config'])) {
        $configs[] = $schema['node_details'][0]['config'];
    }
    if (isset($schema['state']['device']['config']) && is_array($schema['state']['device']['config'])) {
        $configs[] = $schema['state']['device']['config'];
    }
    if (isset($schema['deviceState']['config']) && is_array($schema['deviceState']['config'])) {
        $configs[] = $schema['deviceState']['config'];
    }

    foreach ($configs as $config) {
        foreach ($config['devices'] ?? [] as $device) {
            if (!is_array($device) || empty($device['pages']) || !is_array($device['pages'])) {
                continue;
            }
            foreach ($device['pages'] as $pageKey => $pageFrames) {
                if (!is_array($pageFrames)) {
                    continue;
                }
                foreach ($pageFrames as $item) {
                    if (empty($item['frame']) || !is_array($item['frame'])) {
                        continue;
                    }
                    $name = strtolower(trim((string) ($item['frame']['name'] ?? '')));
                    if ($name !== '') {
                        $frames[$name] = $item['frame'];
                        // Retain the owning config page so a control can be matched
                        // within its own page before falling back to a global guess.
                        $framesByPage[(string) $pageKey][$name] = $item['frame'];
                    }
                }
            }
        }
    }

    return $frames;
}

private function attachCanonicalDeviceUiFrames(array $schema)
{
    $framesByPage = [];
    $frames = $this->collectDeviceUiConfigFrames($schema, $framesByPage);
    if (empty($schema['controls']) || !is_array($schema['controls'])) {
        return $schema;
    }

    foreach ($schema['controls'] as &$control) {
        if (!is_array($control)) {
            continue;
        }
        // Attach the same frame contract to every control (command / response / error),
        // not only writable power controls — Flutter needs consistent deviceFrame formatting.
        $name = strtolower(trim((string) ($control['parameterName'] ?? $control['parameter'] ?? $control['label'] ?? '')));
        $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
        $pageFrames = $this->deviceUiFramesForPage($framesByPage, $control['pageNo'] ?? '');
        $resolved = $this->resolveDeviceUiFrameForControl($frames, $name, $meta, $pageFrames);
        if (is_array($resolved)) {
            $control['deviceFrame'] = $this->normalizeDeviceUiFrame($resolved, $name !== '' ? $name : 'frame', $meta);
        } elseif (!empty($control['deviceFrame']) && is_array($control['deviceFrame'])) {
            $control['deviceFrame'] = $this->normalizeDeviceUiFrame($control['deviceFrame'], $name !== '' ? $name : 'frame', $meta);
        } else {
            $control['deviceFrame'] = $this->normalizeDeviceUiFrame([], $name !== '' ? $name : 'frame', $meta);
        }
        if (!isset($control['protocolMeta']) || !is_array($control['protocolMeta'])) {
            $control['protocolMeta'] = [];
        }
        if (empty($control['protocolMeta']['frame']) || !is_array($control['protocolMeta']['frame'])) {
            $control['protocolMeta']['frame'] = $control['deviceFrame'];
        }
    }
    unset($control);

    return $schema;
}

/**
 * Resolve a device frame by exact/fuzzy parameter name, then protocolMeta.frame.
 *
 * @param array<string, array> $frames
 * @param string $name
 * @param array $meta
 * @return array|null
 */
private function resolveDeviceUiFrameForControl(array $frames, $name, array $meta, array $pageFrames = [])
{
    $name = strtolower(trim((string) $name));
    $slug = preg_replace('/[^a-z0-9]+/', '', $name);

    // Page-scoped candidates win, so a control on Page 1 cannot bind to a frame
    // that belongs to another page just because the names happen to overlap.
    $scopes = [];
    if (!empty($pageFrames)) {
        $scopes[] = $pageFrames;
    }
    $scopes[] = $frames;

    foreach ($scopes as $scope) {
        if ($name !== '' && isset($scope[$name]) && is_array($scope[$name])) {
            return $scope[$name];
        }
    }
    foreach ($scopes as $scope) {
        $exact = $this->deviceUiFrameSlugMatch($scope, $slug, true);
        if ($exact !== null) {
            return $exact;
        }
    }
    foreach ($scopes as $scope) {
        $partial = $this->deviceUiFrameSlugMatch($scope, $slug, false);
        if ($partial !== null) {
            return $partial;
        }
    }

    if (!empty($meta['frame']) && is_array($meta['frame'])) {
        return $meta['frame'];
    }

    return null;
}

/**
 * Match a frame by normalised name. Partial matching must be unambiguous —
 * returning the first of several candidates binds controls to arbitrary frames.
 *
 * @return array|null
 */
private function deviceUiFrameSlugMatch(array $frames, $slug, $exactOnly)
{
    $slug = (string) $slug;
    if ($slug === '') {
        return null;
    }

    $match = null;
    foreach ($frames as $frameName => $frame) {
        if (!is_array($frame)) {
            continue;
        }
        $frameSlug = preg_replace('/[^a-z0-9]+/', '', strtolower((string) $frameName));
        if ($frameSlug === '') {
            continue;
        }
        if ($frameSlug === $slug) {
            return $frame;
        }
        if ($exactOnly) {
            continue;
        }
        if (strpos($frameSlug, $slug) !== false || strpos($slug, $frameSlug) !== false) {
            if ($match !== null) {
                return null;
            }
            $match = $frame;
        }
    }

    return $match;
}

/**
 * Config pages are named like "Page1.Operation"; controls only carry a page number.
 */
private function deviceUiFramesForPage(array $framesByPage, $pageNo)
{
    $pageNo = trim((string) $pageNo);
    if ($pageNo === '') {
        return [];
    }

    $needle = 'page' . $pageNo;
    $matched = [];
    foreach ($framesByPage as $pageKey => $frames) {
        if (!is_array($frames)) {
            continue;
        }
        $slug = preg_replace('/[^a-z0-9]+/', '', strtolower((string) $pageKey));
        $isMatch = $slug === $needle
            || (strpos($slug, $needle) === 0 && !ctype_digit((string) substr($slug, strlen($needle), 1)));
        if (!$isMatch) {
            continue;
        }
        foreach ($frames as $frameName => $frame) {
            $matched[$frameName] = $frame;
        }
    }

    return $matched;
}

/**
 * Repair Device UI schema against Flutter P0 contract before validation/publish.
 */
private function repairDeviceUiSchemaP0Contracts(array $schema)
{
    if (empty($schema['controls']) || !is_array($schema['controls'])) {
        return $schema;
    }

    $disabledOpacity = 0.45;
    foreach ($schema['controls'] as &$control) {
        if (!is_array($control)) {
            continue;
        }

        $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
        $source = strtolower(trim((string) ($meta['ui_type_source'] ?? '')));
        $label = trim((string) ($control['label'] ?? $control['parameterName'] ?? $control['parameter'] ?? ''));
        $parameterName = trim((string) ($control['parameterName'] ?? $control['parameter'] ?? ''));
        if ($parameterName !== '' && (!isset($control['telemetryKey']) || trim((string) $control['telemetryKey']) === '')) {
            $control['telemetryKey'] = $parameterName;
        }
        $isArrayPayload = strtolower((string) ($control['valueType'] ?? '')) === 'array'
            || strtolower((string) ($control['componentType'] ?? '')) === 'array'
            || strtoupper($label) === 'ARRAY'
            || strtoupper($parameterName) === 'ARRAY';
        // Only hide internal ARRAY/parser payloads. Named product rows such as
        // TUNING Command/Response/Error must stay in controlIds / controlOrder.
        $isParserOnly = $isArrayPayload && (
            !empty($control['parserOnly'])
            || (isset($control['renderable']) && $control['renderable'] === false)
            || in_array($source, ['response', 'error'], true)
        );
        if (!$isArrayPayload && $parameterName !== '') {
            unset($control['parserOnly']);
            $control['visible'] = $control['visible'] ?? true;
            $control['renderable'] = true;
        }

        if ($isParserOnly) {
            $control['visible'] = false;
            $control['renderable'] = false;
            $control['parserOnly'] = true;
            $control['events'] = [];
            if (isset($control['component']) && is_array($control['component'])) {
                $control['component']['events'] = [];
            }
            continue;
        }

        // Never publish API source-row names as the live value (TUNING Command/Response/Error).
        $this->blankDeviceUiReservedControlValues($control);
        $this->applyDeviceUiDirectionControlContract($control);

        $writable = $this->deviceUiControlIsWritable($control);
        $catalogType = strtolower(trim((string) ($control['catalogComponentType'] ?? '')));
        $componentType = strtolower(trim((string) ($control['componentType'] ?? '')));
        $isTimerOrSlider = $catalogType === 'autotriggertimer'
            || $componentType === 'slider'
            || strpos($catalogType, 'slider') !== false;

        // Timer / slider rows must keep physical 0–255s constraints (not 0–1 switch leftovers).
        if ($isTimerOrSlider) {
            $min = isset($control['constraints']['minimum']) ? (float) $control['constraints']['minimum'] : 0.0;
            $max = isset($control['constraints']['maximum']) ? (float) $control['constraints']['maximum'] : 255.0;
            if (!is_finite($min)) {
                $min = 0.0;
            }
            if (!is_finite($max) || $max <= $min || ($catalogType === 'autotriggertimer' && $max <= 1)) {
                $max = 255.0;
            }
            $control['constraints'] = [
                'minimum' => $min,
                'maximum' => $max,
                'step' => isset($control['constraints']['step']) && is_numeric($control['constraints']['step'])
                    ? (float) $control['constraints']['step']
                    : 1.0,
                'unit' => $catalogType === 'autotriggertimer' ? 's' : ($control['constraints']['unit'] ?? null),
            ];
            $control['valueType'] = 'number';
            $control['componentType'] = 'slider';
            $control['controlType'] = 'slider';
            if (!isset($control['component']) || !is_array($control['component'])) {
                $control['component'] = [];
            }
            $control['component']['type'] = 'slider';
            $control['component']['valueType'] = 'number';
            if (!isset($control['component']['props']) || !is_array($control['component']['props'])) {
                $control['component']['props'] = [];
            }
            if ($catalogType === 'autotriggertimer') {
                $control['component']['props']['variant'] = 'timer';
                $control['component']['props']['valueSuffix'] = 's';
            }
            $control['component']['props']['min'] = $min;
            $control['component']['props']['max'] = $max;
            $control['component']['props']['step'] = $control['constraints']['step'];
            unset(
                $control['component']['props']['checked'],
                $control['component']['props']['trueValues'],
                $control['component']['props']['falseValues'],
                $control['component']['props']['activeThumbColor'],
                $control['component']['props']['inactiveThumbColor']
            );
        }

        // Document send-button as related UI on text fields (REMOTE ADDRESS 3 UI chip).
        $schemaType = strtolower(trim((string) ($control['componentType'] ?? '')));
        if ($schemaType === 'textfield' && $writable) {
            if (!isset($control['component']) || !is_array($control['component'])) {
                $control['component'] = [];
            }
            if (!isset($control['component']['props']) || !is_array($control['component']['props'])) {
                $control['component']['props'] = [];
            }
            $accent = '#2563EB';
            if (isset($control['styles']['accentColor']) && $this->deviceUiIsCssColor($control['styles']['accentColor'])) {
                $accent = (string) $control['styles']['accentColor'];
            } elseif (isset($schema['theme']['colors']['accent']) && $this->deviceUiIsCssColor($schema['theme']['colors']['accent'])) {
                $accent = (string) $schema['theme']['colors']['accent'];
            }
            $binding = (string) ($control['binding'] ?? '');
            $control['component']['props']['sendButton'] = $this->deviceUiMergeSendButtonProps(
                $control['component']['props']['sendButton'] ?? null,
                $binding,
                true,
                $accent
            );
            $control['uiParts'] = ['textField', 'sendButton'];
            $control['uiCount'] = 2;
            $control['relatedUiCount'] = 3;
        }

        // One authoritative component selection drives flutterWidget and the nested
        // projection, and drops a widget tree cloned from a different control type.
        $this->syncDeviceUiControlComponentProjection(
            $control,
            $control['componentType'] ?? 'auto',
            $control['catalogComponentType'] ?? null
        );
        $this->applyDeviceUiChoiceContract($control);

        if (!$writable) {
            $control['readOnly'] = true;
            $control['writable'] = false;
            $control['interactive'] = false;
            $control['events'] = [];
            if (isset($control['action']) && stripos((string) $control['action'], 'update_') === 0) {
                unset($control['action']);
            }
            if (!isset($control['component']) || !is_array($control['component'])) {
                $control['component'] = [];
            }
            $control['component']['events'] = [];
            $control['component']['readOnly'] = true;
            $control['component']['interactive'] = false;
            $props = isset($control['component']['props']) && is_array($control['component']['props'])
                ? $control['component']['props']
                : [];
            $props['readOnly'] = true;
            $props['enabled'] = true;
            $props['interactive'] = false;
            $props['disabledOpacity'] = 1;
            $textColor = '#0F172A';
            if (isset($schema['theme']['colors']['text']) && $this->deviceUiIsCssColor($schema['theme']['colors']['text'])) {
                $textColor = (string) $schema['theme']['colors']['text'];
            }
            $props['textColor'] = $textColor;
            $props['valueColor'] = $textColor;
            $props['disabledTextColor'] = $textColor;
            $props['readOnlyTextColor'] = $textColor;
            $control['component']['props'] = $props;
            $style = isset($control['component']['style']) && is_array($control['component']['style'])
                ? $control['component']['style']
                : [];
            $style['color'] = $textColor;
            $style['textColor'] = $textColor;
            $style['disabled'] = [
                'opacity' => 1,
                'color' => $textColor,
                'textColor' => $textColor,
            ];
            $control['component']['style'] = $style;
            // Must be a real variable: the ?? expression cannot bind to the by-ref
            // parameter and threw a fatal that aborted the whole repair pass.
            if (!empty($control['component']['widgetTree']) && is_array($control['component']['widgetTree'])) {
                $this->stripDeviceUiWriteEventsFromTree($control['component']['widgetTree']);
            }
        } else {
            // Clear read-only artefacts left behind from a previous refresh, otherwise a
            // control whose access became writable stays disabled and event-less forever.
            $control['readOnly'] = false;
            $control['writable'] = true;
            $control['interactive'] = true;
            if (empty($control['events']) || !is_array($control['events'])) {
                $control['events'] = $this->deviceUiSchemaEventsForControl(
                    $control['componentType'] ?? 'auto',
                    (string) ($control['binding'] ?? ''),
                    []
                );
            }
            if (isset($control['component']) && is_array($control['component'])) {
                $control['component']['readOnly'] = false;
                $control['component']['interactive'] = true;
                if (isset($control['component']['props']) && is_array($control['component']['props'])) {
                    $control['component']['props']['readOnly'] = false;
                    $control['component']['props']['enabled'] = true;
                    $control['component']['props']['interactive'] = true;
                }
            }

            // Ensure writable controls expose complete write slots when protocol meta has them.
            $frame = isset($control['deviceFrame']) && is_array($control['deviceFrame']) ? $control['deviceFrame'] : [];
            $commands = isset($frame['commands']) && is_array($frame['commands']) ? $frame['commands'] : [];
            $slotMeta = array_merge($meta, ['ensure_terminator' => true]);
            $commands = $this->hydrateDeviceUiFrameFields($commands, $slotMeta);
            $commands = $this->ensureDeviceUiWriteFieldSlots($commands, $slotMeta);
            $commands = $this->repairDeviceUiFrameBytePositions($commands);
            $frame['commands'] = $commands;
            $frame['name'] = $frame['name'] ?? ($control['parameterName'] ?? $control['parameter'] ?? 'frame');
            $knownCommandId = $this->deviceUiFirstNonEmpty(
                $meta['command_id'] ?? null,
                $meta['commandId'] ?? null,
                $this->deviceUiFrameCommandValue($frame, 'commandid')
            );
            if ($knownCommandId !== null && $knownCommandId !== '') {
                $frame['commandId'] = $this->deviceUiNormalizeProtocolHex($knownCommandId, 1) ?: $knownCommandId;
            }
            $control['deviceFrame'] = $frame;
            if (!isset($control['protocolMeta']) || !is_array($control['protocolMeta'])) {
                $control['protocolMeta'] = $meta;
            }
            $control['protocolMeta']['frame'] = $frame;
            // Rule 3: explicit write permissions on Send / writable input controls.
            $control['writable'] = true;
            $control['readOnly'] = false;
            $control['interactive'] = true;
            $control['access'] = 'write';
            $metaAccess = strtolower(trim((string) ($control['protocolMeta']['access'] ?? '')));
            if ($metaAccess === '' || strpos($metaAccess, 'write') === false) {
                $control['protocolMeta']['access'] = 'write';
            }
        }
    }
    unset($control);

    // Keep parser-only fields out of visible page control lists, and derive every
    // ordering projection from one source: page number, then controls[].sequence.
    $renderableControls = [];
    foreach ($schema['controls'] as $index => $control) {
        if (!is_array($control) || empty($control['id'])) {
            continue;
        }
        if (!empty($control['parserOnly']) || (isset($control['visible']) && $control['visible'] === false) || (isset($control['renderable']) && $control['renderable'] === false)) {
            continue;
        }
        $renderableControls[] = [
            'id' => (string) $control['id'],
            'pageNo' => trim((string) ($control['pageNo'] ?? '1')),
            'sequence' => (int) ($control['sequence'] ?? 0),
            'index' => $index,
        ];
    }
    usort($renderableControls, static function ($a, $b) {
        $pageA = (int) $a['pageNo'];
        $pageB = (int) $b['pageNo'];
        if ($pageA !== $pageB) {
            return $pageA <=> $pageB;
        }
        if ($a['sequence'] !== $b['sequence']) {
            return $a['sequence'] <=> $b['sequence'];
        }

        return $a['index'] <=> $b['index'];
    });

    $controlsByPage = [];
    $orderedControlIds = [];
    foreach ($renderableControls as $entry) {
        $orderedControlIds[] = $entry['id'];
        if (!isset($controlsByPage[$entry['pageNo']])) {
            $controlsByPage[$entry['pageNo']] = [];
        }
        $controlsByPage[$entry['pageNo']][] = $entry['id'];
    }

    // Rebuilt here, after parser-only controls are known, so hidden controls can
    // never reach the renderer through settings.controlOrder.
    if (!isset($schema['settings']) || !is_array($schema['settings'])) {
        $schema['settings'] = [];
    }
    $schema['settings']['controlOrder'] = $orderedControlIds;
    if (!empty($schema['pages']) && is_array($schema['pages'])) {
        foreach ($schema['pages'] as &$page) {
            if (!is_array($page)) {
                continue;
            }
            $pageNo = trim((string) ($page['pageNo'] ?? ''));
            if ($pageNo !== '' && isset($controlsByPage[$pageNo])) {
                $page['controlIds'] = array_values($controlsByPage[$pageNo]);
                $page['controlCount'] = count($page['controlIds']);
            }
        }
        unset($page);
    }
    if (isset($schema['ui']) && is_array($schema['ui'])) {
        $this->replaceDeviceUiSchemaControlLists($schema['ui'], $controlsByPage);
    }
    if (isset($schema['extensions']['universalUi']) && is_array($schema['extensions']['universalUi'])) {
        $this->replaceDeviceUiSchemaControlLists($schema['extensions']['universalUi'], $controlsByPage);
    }
    if (isset($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree'])) {
        $this->ensureDeviceUiDisabledOpacityOnTree($schema['ui']['widgetTree'], $disabledOpacity);
    }
    if (!empty($schema['extensions']['serverDrivenUi']) && is_array($schema['extensions']['serverDrivenUi'])) {
        $this->ensureDeviceUiDisabledOpacityOnTree($schema['extensions']['serverDrivenUi'], $disabledOpacity);
    }

    $schema = $this->hideDeviceUiHeroAndNameCards($schema);
    $schema = $this->repairDeviceUiHeaderPreviewStyle($schema);
    $schema = $this->repairDeviceUiThemeIconColor($schema);
    $schema = $this->ensureDeviceUiCatalogTimerAndChip($schema);
    $schema = $this->ensureDeviceUiControlWidgetTrees($schema);
    $schema = $this->finalizeDeviceUiPublishedInteractions($schema);
    $schema = $this->ensureDeviceUiSendIconLibrary($schema);
    $schema = $this->finalizeDeviceUiControlJsonObjects($schema);
    $schema = $this->markDeviceUiSduiExtensionNotProduction($schema);

    return $schema;
}

/**
 * Power and switch controls use the same frame keys as the send button.
 * A stub {useLiveBinding:true} leaves the GET STATUS button unframed.
 */
private function deviceUiPowerButtonFrame($accentColor = '#2563EB')
{
    $accent = trim((string) $accentColor);
    if ($accent === '' || !$this->deviceUiIsCssColor($accent)) {
        $accent = '#2563EB';
    }

    return [
        'useLiveBinding' => true,
        'size' => 44,
        'width' => 44,
        'height' => 44,
        'borderRadius' => 22,
        'backgroundColor' => $accent,
        'color' => $accent,
        'icon' => 'power',
        'iconColor' => '#FFFFFF',
        'iconSize' => 22,
        'borderWidth' => 0,
    ];
}

private function deviceUiSwitchFrame($accentColor = '#2563EB')
{
    $accent = trim((string) $accentColor);
    if ($accent === '' || !$this->deviceUiIsCssColor($accent)) {
        $accent = '#2563EB';
    }

    return [
        'useLiveBinding' => true,
        'width' => 52,
        'height' => 32,
        'borderRadius' => 16,
        'activeColor' => $accent,
        'inactiveColor' => '#E5E7EB',
        'activeTrackColor' => $accent,
        'inactiveTrackColor' => '#E5E7EB',
        'activeThumbColor' => '#FFFFFF',
        'inactiveThumbColor' => '#FFFFFF',
        'thumbColor' => '#FFFFFF',
    ];
}

/**
 * Build a full send-button props object (icon/size/color/action).
 * Apps reject the stub {visible,label} shape.
 */
private function deviceUiBuildSendButtonProps($binding, $visible = true, $accentColor = '#2563EB')
{
    $accent = trim((string) $accentColor);
    if ($accent === '' || !$this->deviceUiIsCssColor($accent)) {
        $accent = '#2563EB';
    }

    return [
        'visible' => (bool) $visible,
        'label' => 'Send',
        'icon' => 'send',
        'size' => 32,
        'borderRadius' => 9,
        'backgroundColor' => $accent,
        'color' => $accent,
        'iconColor' => '#FFFFFF',
        'iconSize' => 15,
        'action' => [
            'type' => 'send',
            'binding' => (string) $binding,
        ],
    ];
}

/**
 * Merge missing send-button fields onto an existing bag without dropping keys.
 */
private function deviceUiMergeSendButtonProps($existing, $binding, $visible = true, $accentColor = '#2563EB')
{
    $full = $this->deviceUiBuildSendButtonProps($binding, $visible, $accentColor);
    if (!is_array($existing)) {
        return $full;
    }
    foreach ($full as $key => $value) {
        if (!array_key_exists($key, $existing) || $existing[$key] === null || $existing[$key] === '') {
            $existing[$key] = $value;
        }
    }
    if (!isset($existing['action']) || !is_array($existing['action'])) {
        $existing['action'] = $full['action'];
    } else {
        if (empty($existing['action']['type'])) {
            $existing['action']['type'] = 'send';
        }
        if (empty($existing['action']['binding'])) {
            $existing['action']['binding'] = (string) $binding;
        }
    }
    $existing['visible'] = array_key_exists('visible', $existing)
        ? (bool) $existing['visible']
        : (bool) $visible;

    return $existing;
}

/**
 * Ensure icons.library.paths includes the send glyph used by catalog send buttons.
 */
private function ensureDeviceUiSendIconLibrary(array $schema)
{
    $sendPath = '<line x1="22" x2="11" y1="2" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>';
    if (!isset($schema['icons']) || !is_array($schema['icons'])) {
        $schema['icons'] = [];
    }
    if (!isset($schema['icons']['library']) || !is_array($schema['icons']['library'])) {
        $schema['icons']['library'] = [
            'icons' => [],
            'paths' => [],
        ];
    }
    if (!isset($schema['icons']['library']['icons']) || !is_array($schema['icons']['library']['icons'])) {
        $schema['icons']['library']['icons'] = [];
    }
    if (!isset($schema['icons']['library']['paths']) || !is_array($schema['icons']['library']['paths'])) {
        $schema['icons']['library']['paths'] = [];
    }
    if (!in_array('send', $schema['icons']['library']['icons'], true)) {
        $schema['icons']['library']['icons'][] = 'send';
    }
    if (empty($schema['icons']['library']['paths']['send'])) {
        $schema['icons']['library']['paths']['send'] = $sendPath;
    }
    $schema['icons']['library']['count'] = count($schema['icons']['library']['icons']);

    return $schema;
}

/**
 * PHP empty arrays encode as JSON []. Controls require component.props to be {}.
 */
private function deviceUiEmptyJsonObject()
{
    return new \stdClass();
}

private function deviceUiForceEmptyArrayToObject($value)
{
    if (is_array($value) && $value === []) {
        return $this->deviceUiEmptyJsonObject();
    }

    return $value;
}

private function finalizeDeviceUiControlJsonObjects(array $schema)
{
    if (empty($schema['controls']) || !is_array($schema['controls'])) {
        return $schema;
    }
    foreach ($schema['controls'] as &$control) {
        if (!is_array($control) || !isset($control['component']) || !is_array($control['component'])) {
            continue;
        }
        if (!isset($control['component']['props']) || !is_array($control['component']['props'])) {
            $control['component']['props'] = $this->deviceUiEmptyJsonObject();
        } elseif ($control['component']['props'] === []) {
            $control['component']['props'] = $this->deviceUiEmptyJsonObject();
        }
        if (isset($control['component']['widgetTree']) && is_array($control['component']['widgetTree'])) {
            $this->deviceUiForceEmptyPropsObjectsInTree($control['component']['widgetTree']);
        }
    }
    unset($control);

    return $schema;
}

private function deviceUiForceEmptyPropsObjectsInTree(&$node)
{
    if (!is_array($node)) {
        return;
    }
    if (array_key_exists('props', $node) && is_array($node['props']) && $node['props'] === []) {
        $node['props'] = $this->deviceUiEmptyJsonObject();
    }
    if (isset($node['properties']) && is_array($node['properties'])) {
        if (array_key_exists('props', $node['properties']) && is_array($node['properties']['props']) && $node['properties']['props'] === []) {
            $node['properties']['props'] = $this->deviceUiEmptyJsonObject();
        }
    }
    foreach (['children', 'widgetTree'] as $key) {
        if (empty($node[$key]) || !is_array($node[$key])) {
            continue;
        }
        if ($this->deviceUiIsAssocArray($node[$key])) {
            $this->deviceUiForceEmptyPropsObjectsInTree($node[$key]);
        } else {
            foreach ($node[$key] as &$child) {
                $this->deviceUiForceEmptyPropsObjectsInTree($child);
            }
            unset($child);
        }
    }
}

private function deviceUiIsCssColor($value)
{
    $raw = trim((string) $value);
    if ($raw === '') {
        return false;
    }

    return (bool) preg_match('/^(#([0-9a-f]{3,8})|rgba?\(|transparent|hsla?\()/i', $raw);
}

private function blankDeviceUiReservedControlValues(array &$control)
{
    $reservedValues = ['command' => true, 'response' => true, 'error' => true];
    $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
    $fieldDescription = strtolower(trim((string) ($meta['field_description'] ?? $meta['Field_description'] ?? '')));
    $source = strtolower(trim((string) ($meta['ui_type_source'] ?? '')));
    $blankIf = function ($raw) use ($reservedValues, $fieldDescription, $source) {
        $text = strtolower(trim((string) $raw));
        if ($text === '') {
            return true;
        }
        if (isset($reservedValues[$text])) {
            return true;
        }
        if ($fieldDescription !== '' && $text === $fieldDescription && isset($reservedValues[$fieldDescription])) {
            return true;
        }
        if (in_array($source, ['command', 'response', 'error'], true) && $text === $source) {
            return true;
        }

        return false;
    };

    foreach (['value', 'displayValue', 'initialValue'] as $valueKey) {
        if (!array_key_exists($valueKey, $control)) {
            continue;
        }
        if ($blankIf($control[$valueKey])) {
            $control[$valueKey] = '';
        }
    }
    if (isset($control['component']) && is_array($control['component'])) {
        foreach (['displayValue', 'initialValue', 'value'] as $valueKey) {
            if (!array_key_exists($valueKey, $control['component'])) {
                continue;
            }
            if ($blankIf($control['component'][$valueKey])) {
                $control['component'][$valueKey] = '';
            }
        }
    }
}

private function hideDeviceUiHeroAndNameCards(array $schema)
{
    $matcher = function ($node) {
        return $this->isDeviceUiSlideHeroNode($node) || $this->isDeviceUiNameCardNode($node);
    };
    // Only walk UI trees. Walking the full schema (controls, frames, icon
    // library, exactHtmlPreview) exhausts PHP memory on production (128MB).
    if (isset($schema['ui']) && is_array($schema['ui'])) {
        $this->setDeviceUiMatchingNodeVisibility($schema['ui'], $matcher, false);
    }
    if (isset($schema['extensions']['serverDrivenUi']) && is_array($schema['extensions']['serverDrivenUi'])) {
        $this->setDeviceUiMatchingNodeVisibility($schema['extensions']['serverDrivenUi'], $matcher, false);
    }
    if (!isset($schema['settings']) || !is_array($schema['settings'])) {
        $schema['settings'] = [];
    }
    if (!isset($schema['settings']['visibility']) || !is_array($schema['settings']['visibility'])) {
        $schema['settings']['visibility'] = [];
    }
    $schema['settings']['visibility']['heroVisible'] = false;
    $schema['settings']['visibility']['nameCardVisible'] = false;
    if (isset($schema['mobileView']['pageContent']) && is_array($schema['mobileView']['pageContent'])) {
        $schema['mobileView']['pageContent']['heroVisible'] = false;
        $schema['mobileView']['pageContent']['nameCardVisible'] = false;
    }
    if (isset($schema['theme']['visibility']) && is_array($schema['theme']['visibility'])) {
        $schema['theme']['visibility']['heroVisible'] = false;
        $schema['theme']['visibility']['nameCardVisible'] = false;
    }

    return $schema;
}

private function deviceUiSchemaWalkSkipKey($key)
{
    static $skip = [
        'exactHtmlPreview' => true,
        'icons' => true,
        'catalog' => true,
        'registerComponents' => true,
        'deviceFrame' => true,
        'protocolMeta' => true,
        'css' => true,
        'paths' => true,
        'assets' => true,
        'deviceConfig' => true,
        'deviceState' => true,
        'node_details' => true,
        'dataSources' => true,
        'permissions' => true,
        'controls' => true,
        'state' => true,
        'actions' => true,
        'componentTemplates' => true,
        'extensions' => true,
        'html' => true,
    ];

    return is_string($key) && isset($skip[$key]);
}

private function setDeviceUiMatchingNodeVisibility(&$node, callable $matcher, $visible, $depth = 0)
{
    if (!is_array($node) || $depth > 60) {
        return;
    }
    if ($matcher($node)) {
        $node['visibility'] = $visible;
        $node['visible'] = $visible;
        if (isset($node['properties']) && is_array($node['properties'])) {
            $node['properties']['visibility'] = $visible;
            $node['properties']['visible'] = $visible;
        }
    }
    foreach ($node as $key => $child) {
        if (!is_array($child) || $this->deviceUiSchemaWalkSkipKey($key)) {
            continue;
        }
        $this->setDeviceUiMatchingNodeVisibility($node[$key], $matcher, $visible, $depth + 1);
    }
}

private function deviceUiColorLuminance($value)
{
    $raw = strtoupper(preg_replace('/[^0-9A-F]/', '', (string) $value));
    if (strlen($raw) === 8) {
        $raw = substr($raw, -6);
    }
    if (strlen($raw) === 3) {
        $raw = $raw[0] . $raw[0] . $raw[1] . $raw[1] . $raw[2] . $raw[2];
    }
    if (strlen($raw) !== 6) {
        return 0;
    }

    $r = hexdec(substr($raw, 0, 2));
    $g = hexdec(substr($raw, 2, 2));
    $b = hexdec(substr($raw, 4, 2));

    return (0.299 * $r) + (0.587 * $g) + (0.114 * $b);
}

private function deviceUiColorIsDark($value)
{
    return $this->deviceUiColorLuminance($value) < 160;
}

private function deviceUiIsForcedWhiteHeader($value)
{
    $raw = strtoupper(preg_replace('/[^0-9A-F]/', '', (string) $value));
    if (strlen($raw) === 8) {
        $raw = substr($raw, -6);
    }
    if (strlen($raw) === 3) {
        $raw = $raw[0] . $raw[0] . $raw[1] . $raw[1] . $raw[2] . $raw[2];
    }

    return $raw === 'FFFFFF';
}

private function deviceUiSchemaColorAt(array $schema, array $path)
{
    $cursor = $schema;
    foreach ($path as $key) {
        if (!is_array($cursor) || !array_key_exists($key, $cursor)) {
            return null;
        }
        $cursor = $cursor[$key];
    }

    return $this->deviceUiIsCssColor($cursor) ? (string) $cursor : null;
}

private function deviceUiResolveTopbarColor(array $schema)
{
    $candidates = [];
    $paths = [
        ['designSystem', 'colors', 'topbar'],
        ['theme', 'tokens', 'topbarColor'],
        ['theme', 'tokens', 'colors', 'topbar'],
        ['theme', 'colors', 'topbar'],
        ['mobileView', 'header', 'backgroundColor'],
    ];
    foreach ($paths as $path) {
        $color = $this->deviceUiSchemaColorAt($schema, $path);
        if ($color !== null) {
            $candidates[] = $color;
        }
    }
    foreach ($candidates as $color) {
        if (!$this->deviceUiIsForcedWhiteHeader($color)) {
            return $color;
        }
    }

    return $candidates[0] ?? '#0F172A';
}

private function deviceUiHeaderPreviewTokens(array $schema)
{
    $accent = '#2563EB';
    $topbar = $this->deviceUiResolveTopbarColor($schema);
    $secondary = $topbar;
    if (isset($schema['theme']['colors']['accent']) && $this->deviceUiIsCssColor($schema['theme']['colors']['accent'])) {
        $accent = (string) $schema['theme']['colors']['accent'];
    }
    if (isset($schema['theme']['tokens']['accentColor']) && $this->deviceUiIsCssColor($schema['theme']['tokens']['accentColor'])) {
        $accent = (string) $schema['theme']['tokens']['accentColor'];
    }
    if (isset($schema['theme']['colors']['secondary']) && $this->deviceUiIsCssColor($schema['theme']['colors']['secondary'])) {
        $secondary = (string) $schema['theme']['colors']['secondary'];
    }
    $icon = $accent;
    if (isset($schema['theme']['colors']['icon']) && $this->deviceUiIsCssColor($schema['theme']['colors']['icon'])) {
        $icon = (string) $schema['theme']['colors']['icon'];
    }
    $dark = $this->deviceUiColorIsDark($topbar);

    return [
        'backgroundColor' => $topbar,
        'secondaryColor' => $secondary,
        'titleColor' => $dark ? '#FFFFFF' : '#0F172A',
        'subtitleColor' => $dark ? 'rgba(255,255,255,0.72)' : '#64748B',
        'backBackground' => $dark ? 'rgba(255,255,255,0.10)' : '#FFFFFF',
        'backForeground' => $dark ? '#FFFFFF' : '#0F172A',
        'iconColor' => $dark ? '#FFFFFF' : $icon,
        'accent' => $accent,
    ];
}

private function deviceUiIsLegacyNavyHeader($value)
{
    $raw = strtoupper(preg_replace('/[^0-9A-F]/', '', (string) $value));
    $navy = [
        '1E3A8A' => true,
        'FF1E3A8A' => true,
        '0F172A' => true,
        'FF0F172A' => true,
        '123246' => true,
        'FF123246' => true,
    ];

    return isset($navy[$raw]);
}

private function repairDeviceUiHeaderPreviewStyle(array $schema)
{
    $tokens = $this->deviceUiHeaderPreviewTokens($schema);
    if (isset($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree'])) {
        $this->applyDeviceUiHeaderPreviewStyle($schema['ui']['widgetTree'], $tokens);
    }
    if (isset($schema['extensions']['serverDrivenUi']) && is_array($schema['extensions']['serverDrivenUi'])) {
        $this->applyDeviceUiHeaderPreviewStyle($schema['extensions']['serverDrivenUi'], $tokens);
    }
    if (isset($schema['theme']['colors']) && is_array($schema['theme']['colors'])) {
        if (empty($schema['theme']['colors']['topbar']) || $this->deviceUiIsForcedWhiteHeader($schema['theme']['colors']['topbar'])) {
            $schema['theme']['colors']['topbar'] = $tokens['backgroundColor'];
        }
    }
    if (isset($schema['theme']['tokens']) && is_array($schema['theme']['tokens'])) {
        if (empty($schema['theme']['tokens']['topbarColor']) || $this->deviceUiIsForcedWhiteHeader($schema['theme']['tokens']['topbarColor'])) {
            $schema['theme']['tokens']['topbarColor'] = $tokens['backgroundColor'];
        }
        if (isset($schema['theme']['tokens']['colors']) && is_array($schema['theme']['tokens']['colors'])) {
            if (empty($schema['theme']['tokens']['colors']['topbar']) || $this->deviceUiIsForcedWhiteHeader($schema['theme']['tokens']['colors']['topbar'])) {
                $schema['theme']['tokens']['colors']['topbar'] = $tokens['backgroundColor'];
            }
        }
    }
    if (isset($schema['designSystem']['colors']) && is_array($schema['designSystem']['colors'])) {
        if (empty($schema['designSystem']['colors']['topbar']) || $this->deviceUiIsForcedWhiteHeader($schema['designSystem']['colors']['topbar'])) {
            $schema['designSystem']['colors']['topbar'] = $tokens['backgroundColor'];
        }
    }
    if (isset($schema['mobileView']['header']) && is_array($schema['mobileView']['header'])) {
        $schema['mobileView']['header']['backgroundColor'] = $tokens['backgroundColor'];
        $schema['mobileView']['header']['foregroundColor'] = $tokens['titleColor'];
        $schema['mobileView']['header']['titleColor'] = $tokens['titleColor'];
        $schema['mobileView']['header']['iconColor'] = $tokens['iconColor'];
    }

    return $schema;
}

private function applyDeviceUiHeaderPreviewStyle(&$node, array $tokens, $depth = 0)
{
    if (!is_array($node) || $depth > 60) {
        return;
    }
    $id = strtolower(trim((string) ($node['id'] ?? '')));
    $type = strtolower(trim((string) ($node['type'] ?? '')));

    $setBg = function (&$target) use ($tokens) {
        if (!is_array($target)) {
            return;
        }
        $secondary = $tokens['secondaryColor'] ?? $tokens['backgroundColor'];
        $target['backgroundColor'] = $tokens['backgroundColor'];
        $target['gradient'] = [
            'type' => isset($target['gradient']['type']) ? $target['gradient']['type'] : 'linear',
            'begin' => isset($target['gradient']['begin']) ? $target['gradient']['begin'] : 'topLeft',
            'end' => isset($target['gradient']['end']) ? $target['gradient']['end'] : 'bottomRight',
            'colors' => [$tokens['backgroundColor'], $secondary],
        ];
    };

    if (in_array($id, ['app_topbar', 'app-bar', 'appbar'], true) || $type === 'appbar') {
        if (!isset($node['style']) || !is_array($node['style'])) {
            $node['style'] = [];
        }
        $setBg($node['style']);
        $node['style']['backgroundColor'] = $tokens['backgroundColor'];
        if (isset($node['properties']) && is_array($node['properties'])) {
            $setBg($node['properties']);
        }
        $node['backgroundColor'] = $tokens['backgroundColor'];
        $node['foregroundColor'] = $tokens['titleColor'];
    }

    if ($id === 'phone_back_button' || $id === 'back-button' || $id === 'backbutton') {
        if (!isset($node['style']) || !is_array($node['style'])) {
            $node['style'] = [];
        }
        $node['style']['backgroundColor'] = $tokens['backBackground'];
        $node['style']['color'] = $tokens['backForeground'];
        if (isset($node['style']['border'])) {
            $node['style']['border'] = ['width' => 0, 'color' => '#00000000'];
        }
        if (isset($node['properties']) && is_array($node['properties'])) {
            $node['properties']['backgroundColor'] = $tokens['backBackground'];
        }
    }
    if ($id === 'phone_back_text' || $id === 'phone_back_icon') {
        if (!isset($node['style']) || !is_array($node['style'])) {
            $node['style'] = [];
        }
        $node['style']['color'] = $tokens['backForeground'];
        $node['style']['textColor'] = $tokens['backForeground'];
        if (isset($node['style']['textStyle']) && is_array($node['style']['textStyle'])) {
            $node['style']['textStyle']['color'] = $tokens['backForeground'];
        }
    }
    if (in_array($id, ['active_page_title', 'top_title'], true)) {
        if (!isset($node['style']) || !is_array($node['style'])) {
            $node['style'] = [];
        }
        $node['style']['color'] = $tokens['titleColor'];
        $node['style']['textColor'] = $tokens['titleColor'];
        if (isset($node['style']['textStyle']) && is_array($node['style']['textStyle'])) {
            $node['style']['textStyle']['color'] = $tokens['titleColor'];
        }
        if ($id === 'active_page_title') {
            $node['maxLines'] = 1;
            $node['overflow'] = 'ellipsis';
            if (isset($node['properties']) && is_array($node['properties'])) {
                $node['properties']['maxLines'] = 1;
                $node['properties']['overflow'] = 'ellipsis';
            }
        }
    }
    if (in_array($id, ['model_subtitle', 'active_page_subtitle'], true)) {
        if (!isset($node['style']) || !is_array($node['style'])) {
            $node['style'] = [];
        }
        $node['style']['color'] = $tokens['subtitleColor'];
        $node['style']['textColor'] = $tokens['subtitleColor'];
        if (isset($node['style']['textStyle']) && is_array($node['style']['textStyle'])) {
            $node['style']['textStyle']['color'] = $tokens['subtitleColor'];
        }
    }
    if (in_array($id, ['device_info_icon', 'edit_ui_icon', 'device_scan_icon', 'device_wifi_icon', 'device_info_button', 'edit_ui_button', 'device_scan_button', 'device_wifi_button'], true)) {
        if (!isset($node['style']) || !is_array($node['style'])) {
            $node['style'] = [];
        }
        $node['style']['color'] = $tokens['iconColor'];
        if (strpos($id, 'button') !== false) {
            $node['style']['backgroundColor'] = $tokens['backBackground'];
        }
    }

    if (isset($node['header']) && is_array($node['header']) && (($node['header']['type'] ?? '') === 'appBar' || isset($node['header']['backgroundColor']))) {
        $header = &$node['header'];
        $header['backgroundColor'] = $tokens['backgroundColor'];
        if (isset($header['gradient']) && is_array($header['gradient'])) {
            $header['gradient']['colors'] = [$tokens['backgroundColor'], $tokens['secondaryColor'] ?? $tokens['backgroundColor']];
        }
        if (isset($header['title']) && is_array($header['title'])) {
            $header['title']['color'] = $tokens['titleColor'];
        }
        if (isset($header['leading']) && is_array($header['leading'])) {
            $header['leading']['backgroundColor'] = $tokens['backBackground'];
            $header['leading']['color'] = $tokens['backForeground'];
        }
        if (isset($header['actions']) && is_array($header['actions'])) {
            foreach ($header['actions'] as &$action) {
                if (!is_array($action)) {
                    continue;
                }
                $action['backgroundColor'] = '#00000000';
                $action['color'] = $tokens['iconColor'];
            }
            unset($action);
        }
        unset($header);
    }

    foreach ($node as $key => $child) {
        if (!is_array($child) || $this->deviceUiSchemaWalkSkipKey($key)) {
            continue;
        }
        $this->applyDeviceUiHeaderPreviewStyle($node[$key], $tokens, $depth + 1);
    }
}

private function repairDeviceUiThemeIconColor(array $schema)
{
    $fallback = '#2563EB';
    if (isset($schema['theme']['colors']['accent']) && $this->deviceUiIsCssColor($schema['theme']['colors']['accent'])) {
        $fallback = (string) $schema['theme']['colors']['accent'];
    }
    if (isset($schema['theme']['tokens']['iconColor']) && $this->deviceUiIsCssColor($schema['theme']['tokens']['iconColor'])) {
        $fallback = (string) $schema['theme']['tokens']['iconColor'];
    }
    if (isset($schema['theme']['colors']) && is_array($schema['theme']['colors'])) {
        $icon = $schema['theme']['colors']['icon'] ?? null;
        if (!$this->deviceUiIsCssColor($icon)) {
            $schema['theme']['colors']['icon'] = $fallback;
        }
    }
    if (isset($schema['theme']['tokens']['iconColor']) && !$this->deviceUiIsCssColor($schema['theme']['tokens']['iconColor'])) {
        $schema['theme']['tokens']['iconColor'] = $fallback;
    }

    return $schema;
}

private function ensureDeviceUiCatalogTimerAndChip(array $schema)
{
    if (!isset($schema['components']) || !is_array($schema['components'])) {
        $schema['components'] = [];
    }
    if (!isset($schema['components']['catalog']) || !is_array($schema['components']['catalog'])) {
        $schema['components']['catalog'] = [];
    }
    $catalog = &$schema['components']['catalog'];
    $isList = $catalog === [] || array_keys($catalog) === range(0, count($catalog) - 1);
    $entries = $isList ? $catalog : array_values($catalog);

    $findIndex = static function (array $entries, $id) {
        foreach ($entries as $index => $entry) {
            if (!is_array($entry)) {
                continue;
            }
            if (strtolower((string) ($entry['id'] ?? '')) === strtolower((string) $id)) {
                return $index;
            }
        }

        return null;
    };

    $sliderIndex = $findIndex($entries, 'slider');
    if ($sliderIndex !== null && isset($entries[$sliderIndex]['widgetTree']['properties']['props']) && is_array($entries[$sliderIndex]['widgetTree']['properties']['props'])) {
        $props = &$entries[$sliderIndex]['widgetTree']['properties']['props'];
        if (!isset($props['min'])) {
            $props['min'] = 0;
        }
        if (!isset($props['max']) || (float) $props['max'] <= 1) {
            $props['max'] = 255;
        }
        unset($props);
    }

    $timerIndex = $findIndex($entries, 'autoTriggerTimer');
    $timerTree = [
        'type' => 'schemaSlider',
        'properties' => [
            'binding' => '{{binding}}',
            'props' => array_merge([
                'min' => 0,
                'max' => 255,
                'step' => 1,
                'variant' => 'timer',
                'valueSuffix' => 's',
                'kicker' => 'TIMER',
                'useLiveBinding' => true,
                'showMinMaxLabels' => true,
            ], $this->deviceUiTimerSliderStyleDefaults()),
            'events' => [
                ['event' => 'onChanged', 'action' => ['type' => 'update', 'binding' => '{{binding}}']],
            ],
        ],
    ];
    if ($timerIndex === null) {
        $entries[] = [
            'id' => 'autoTriggerTimer',
            'label' => 'Auto Trigger Timer',
            'flutterWidget' => 'schemaSlider',
            'valueType' => 'number',
            'kicker' => 'TIMER',
            'widgetTree' => $timerTree,
        ];
    } else {
        $entries[$timerIndex]['kicker'] = 'TIMER';
        $entries[$timerIndex]['widgetTree'] = $timerTree;
        if (!isset($entries[$timerIndex]['flutterWidget'])) {
            $entries[$timerIndex]['flutterWidget'] = 'schemaSlider';
        }
    }

    $textIndex = $findIndex($entries, 'textField');
    if ($textIndex !== null) {
        if (empty($entries[$textIndex]['widgetTree']) || !is_array($entries[$textIndex]['widgetTree'])) {
            $entries[$textIndex]['widgetTree'] = [
                'type' => 'TextField',
                'properties' => [
                    'binding' => '{{binding}}',
                    'props' => [
                        'keyboardType' => 'text',
                        'maxLines' => 1,
                        'useLiveBinding' => true,
                        'valueBinding' => '{{binding}}',
                        'sendButton' => $this->deviceUiBuildSendButtonProps('{{binding}}', true),
                    ],
                    'events' => [
                        ['event' => 'onChanged', 'action' => ['type' => 'update', 'binding' => '{{binding}}']],
                    ],
                ],
            ];
        } else {
            $this->deviceUiEnsureSendButtonOnWidgetTree($entries[$textIndex]['widgetTree']);
        }
    }

    $chipIndex = $findIndex($entries, 'value');
    if ($chipIndex !== null) {
        $tree = $entries[$chipIndex]['widgetTree'] ?? null;
        if (!is_array($tree)) {
            $entries[$chipIndex]['widgetTree'] = [
                'type' => 'metaChip',
                'properties' => [
                    'binding' => '{{binding}}',
                    'props' => [
                        'textTemplate' => '{value}',
                        'valueTemplate' => '{value}',
                        'readOnly' => true,
                    ],
                ],
            ];
        } elseif (isset($entries[$chipIndex]['widgetTree']['properties']['props']) && is_array($entries[$chipIndex]['widgetTree']['properties']['props'])) {
            $entries[$chipIndex]['widgetTree']['properties']['props']['textTemplate'] = '{value}';
            $entries[$chipIndex]['widgetTree']['properties']['props']['valueTemplate'] = '{value}';
        }
    }

    if ($isList) {
        $catalog = array_values($entries);
    } else {
        $mapped = [];
        foreach ($entries as $entry) {
            $id = (string) ($entry['id'] ?? '');
            if ($id !== '') {
                $mapped[$id] = $entry;
            }
        }
        $catalog = $mapped;
    }
    unset($catalog);

    return $schema;
}

private function ensureDeviceUiControlWidgetTrees(array $schema)
{
    if (empty($schema['controls']) || !is_array($schema['controls'])) {
        return $schema;
    }
    $catalog = [];
    $rawCatalog = $schema['components']['catalog'] ?? [];
    if (is_array($rawCatalog)) {
        foreach ($rawCatalog as $entry) {
            if (!is_array($entry) || empty($entry['id'])) {
                continue;
            }
            $catalog[strtolower((string) $entry['id'])] = $entry;
        }
    }

    foreach ($schema['controls'] as &$control) {
        if (!is_array($control)) {
            continue;
        }
        $schemaType = strtolower(trim((string) ($control['componentType'] ?? '')));
        $catalogType = strtolower(trim((string) ($control['catalogComponentType'] ?? '')));
        if (empty($control['component']) || !is_array($control['component'])) {
            $control['component'] = ['type' => $schemaType !== '' ? $schemaType : 'value'];
        }
        $hasTree = !empty($control['component']['widgetTree']) && is_array($control['component']['widgetTree']);
        $treeNeedsAccessoryChild = false;
        if ($hasTree) {
            $existingTree = $control['component']['widgetTree'];
            $treeType = strtolower(trim((string) ($existingTree['type'] ?? '')));
            $children = isset($existingTree['children']) && is_array($existingTree['children'])
                ? $existingTree['children']
                : [];
            $treeNeedsAccessoryChild = ($treeType === 'parametercard' && $children === []);
        }
        if (!$hasTree || $treeNeedsAccessoryChild) {
            $catalogId = $catalogType !== '' ? $catalogType : $schemaType;
            if ($catalogId === 'autotriggertimer') {
                $catalogId = 'autoTriggerTimer';
            }
            $entry = $catalog[strtolower($catalogId)] ?? ($catalog[$schemaType] ?? ($catalog['slider'] ?? ($catalog['value'] ?? null)));
            if ($catalogType === 'autotriggertimer') {
                $entry = $catalog['autotriggertimer'] ?? $entry;
            }
            $binding = (string) ($control['binding'] ?? '');
            $tree = null;
            if (is_array($entry) && !empty($entry['widgetTree']) && is_array($entry['widgetTree'])) {
                $tree = $this->deviceUiSubstituteBindingDeep($entry['widgetTree'], $binding);
            } else {
                $tree = $this->deviceUiSynthesizeControlWidgetTree($control);
            }
            if (is_array($tree)) {
                if ($treeNeedsAccessoryChild
                    && strtolower(trim((string) ($tree['type'] ?? ''))) === 'parametercard'
                    && !empty($tree['children'])
                    && is_array($tree['children'])
                ) {
                    $control['component']['widgetTree']['children'] = $tree['children'];
                } elseif (!$hasTree) {
                    $control['component']['widgetTree'] = $tree;
                } elseif ($treeNeedsAccessoryChild) {
                    // Catalog entry was a bare TextField/PowerButton — wrap as ParameterCard child.
                    $accessoryType = strtolower(trim((string) ($tree['type'] ?? '')));
                    if ($accessoryType !== '' && $accessoryType !== 'parametercard') {
                        $control['component']['widgetTree']['children'] = [$tree];
                    } else {
                        $control['component']['widgetTree'] = $tree;
                    }
                }
            }
        }
        if (!isset($control['component']['props']) || !is_array($control['component']['props'])) {
            $control['component']['props'] = [];
        }
        if ($catalogType === 'autotriggertimer' || $schemaType === 'slider') {
            if ($catalogType === 'autotriggertimer') {
                $control['component']['props']['variant'] = 'timer';
                $control['component']['props']['valueSuffix'] = 's';
                $control['component']['props']['kicker'] = 'TIMER';
                $this->deviceUiApplyTimerSliderStyleProps($control['component']['props']);
                if (isset($control['props']) && is_array($control['props'])) {
                    $this->deviceUiApplyTimerSliderStyleProps($control['props']);
                }
                if (!empty($control['component']['widgetTree']) && is_array($control['component']['widgetTree'])) {
                    $this->deviceUiEnsureTimerPropsOnWidgetTree($control['component']['widgetTree']);
                }
            }
        }
        if ($schemaType === 'textfield') {
            $accent = '#2563EB';
            if (isset($control['styles']['accentColor']) && $this->deviceUiIsCssColor($control['styles']['accentColor'])) {
                $accent = (string) $control['styles']['accentColor'];
            }
            $binding = (string) ($control['binding'] ?? '');
            $control['component']['props']['sendButton'] = $this->deviceUiMergeSendButtonProps(
                $control['component']['props']['sendButton'] ?? null,
                $binding,
                true,
                $accent
            );
            $control['uiParts'] = ['textField', 'sendButton'];
            $control['uiCount'] = 2;
            $control['relatedUiCount'] = 3;
            if (!empty($control['component']['widgetTree']) && is_array($control['component']['widgetTree'])) {
                $this->deviceUiEnsureSendButtonOnWidgetTree($control['component']['widgetTree'], $binding, $accent);
            }
        }
    }
    unset($control);

    return $schema;
}

private function deviceUiSubstituteBindingDeep($node, $binding)
{
    if (is_string($node)) {
        return str_replace('{{binding}}', (string) $binding, $node);
    }
    if (!is_array($node)) {
        return $node;
    }
    $out = [];
    foreach ($node as $key => $value) {
        $out[$key] = $this->deviceUiSubstituteBindingDeep($value, $binding);
    }

    return $out;
}

private function deviceUiEnsureSendButtonOnWidgetTree(&$node, $binding = '', $accentColor = '#2563EB')
{
    $this->deviceUiPatchNestedWidgetTree($node, function (&$target) use ($binding, $accentColor) {
        $type = strtolower(trim((string) ($target['type'] ?? '')));
        if ($type !== 'textfield' && $type !== 'text_field') {
            return;
        }
        $bags = [];
        if (isset($target['properties']['props']) && is_array($target['properties']['props'])) {
            $bags[] =& $target['properties']['props'];
        }
        if (isset($target['props']) && is_array($target['props'])) {
            $bags[] =& $target['props'];
        }
        if ($bags === []) {
            if (!isset($target['properties']) || !is_array($target['properties'])) {
                $target['properties'] = [];
            }
            if (!isset($target['properties']['props']) || !is_array($target['properties']['props'])) {
                $target['properties']['props'] = [];
            }
            $bags[] =& $target['properties']['props'];
        }
        $nodeBinding = $binding;
        if ($nodeBinding === '' && isset($target['properties']['binding'])) {
            $nodeBinding = (string) $target['properties']['binding'];
        } elseif ($nodeBinding === '' && isset($target['binding'])) {
            $nodeBinding = (string) $target['binding'];
        }
        foreach ($bags as &$bag) {
            $bag['sendButton'] = $this->deviceUiMergeSendButtonProps(
                $bag['sendButton'] ?? null,
                $nodeBinding,
                true,
                $accentColor
            );
        }
        unset($bag);
    });
}

/**
 * Typography and color fields the Flutter slider validator requires on a timer.
 * Existing keys are left unchanged; only a missing or invalid value is filled.
 */
private function deviceUiTimerSliderStyleDefaults()
{
    return [
        'trackHeight' => 8,
        'thumbRadius' => 9,
        'thumbBorderColor' => '#FFFFFF',
        'thumbBorderWidth' => 2,
        'labelColor' => '#64748B',
        'valueColor' => '#1E293B',
        'rangeFontSize' => 12,
        'rangeFontWeight' => 600,
        'currentValueFontSize' => 14,
        'currentValueFontWeight' => 600,
        'kickerColor' => '#64748B',
        'kickerFontSize' => 12,
        'kickerFontWeight' => 800,
        'showTickMarks' => false,
    ];
}

private function deviceUiApplyTimerSliderStyleProps(array &$props)
{
    $contract = [
        'labelColor' => true,
        'valueColor' => true,
        'rangeFontSize' => true,
        'currentValueFontSize' => true,
        'kickerColor' => true,
        'kickerFontSize' => true,
    ];
    foreach ($this->deviceUiTimerSliderStyleDefaults() as $key => $value) {
        $force = isset($contract[$key]);
        if (!$force && !$this->deviceUiTimerStylePropMissing($key, array_key_exists($key, $props) ? $props[$key] : null)) {
            continue;
        }
        $props[$key] = $value;
    }
}

private function deviceUiTimerStylePropMissing($key, $value)
{
    $colorKeys = ['labelColor', 'valueColor', 'kickerColor', 'thumbBorderColor'];
    $numberKeys = [
        'trackHeight',
        'thumbRadius',
        'thumbBorderWidth',
        'rangeFontSize',
        'rangeFontWeight',
        'currentValueFontSize',
        'currentValueFontWeight',
        'kickerFontSize',
        'kickerFontWeight',
    ];
    if (in_array($key, $colorKeys, true)) {
        return !$this->deviceUiIsCssColor($value);
    }
    if (in_array($key, $numberKeys, true)) {
        return !is_numeric($value);
    }
    if ($key === 'showTickMarks') {
        return !is_bool($value);
    }

    return $value === null || $value === '';
}

private function deviceUiEnsureTimerPropsOnWidgetTree(&$node)
{
    $this->deviceUiPatchNestedWidgetTree($node, function (&$target) {
        $type = strtolower(trim((string) ($target['type'] ?? '')));
        if ($type !== 'schemaslider' && $type !== 'slider') {
            return;
        }
        $bags = [];
        if (isset($target['properties']['props']) && is_array($target['properties']['props'])) {
            $bags[] =& $target['properties']['props'];
        }
        if (isset($target['props']) && is_array($target['props'])) {
            $bags[] =& $target['props'];
        }
        if ($bags === []) {
            if (!isset($target['properties']) || !is_array($target['properties'])) {
                $target['properties'] = [];
            }
            if (!isset($target['properties']['props']) || !is_array($target['properties']['props'])) {
                $target['properties']['props'] = [];
            }
            $bags[] =& $target['properties']['props'];
        }
        foreach ($bags as &$bag) {
            $bag['variant'] = 'timer';
            $bag['valueSuffix'] = 's';
            $bag['kicker'] = 'TIMER';
            if (!isset($bag['min'])) {
                $bag['min'] = 0;
            }
            if (!isset($bag['max']) || (float) $bag['max'] <= 1) {
                $bag['max'] = 255;
            }
            $this->deviceUiApplyTimerSliderStyleProps($bag);
        }
        unset($bag);
    });
}

private function deviceUiPatchNestedWidgetTree(&$node, callable $patcher, $depth = 0)
{
    if (!is_array($node) || $depth > 40) {
        return;
    }
    $patcher($node);
    foreach ($node as $key => $child) {
        if (!is_array($child) || $this->deviceUiSchemaWalkSkipKey($key)) {
            continue;
        }
        $this->deviceUiPatchNestedWidgetTree($node[$key], $patcher, $depth + 1);
    }
}

private function deviceUiSynthesizeControlWidgetTree(array $control)
{
    $type = strtolower(trim((string) ($control['componentType'] ?? 'value')));
    $binding = (string) ($control['binding'] ?? '');
    $label = (string) ($control['label'] ?? $control['parameterName'] ?? 'Parameter');
    $accessoryType = 'metaChip';
    $props = ['readOnly' => true, 'textTemplate' => '{value}', 'valueTemplate' => '{value}'];
    if ($type === 'switch') {
        $accessoryType = 'Switch';
        $props = $this->deviceUiSwitchFrame('#2563EB');
    } elseif ($type === 'slider') {
        $accessoryType = 'schemaSlider';
        $props = [
            'min' => isset($control['constraints']['minimum']) ? (float) $control['constraints']['minimum'] : 0,
            'max' => isset($control['constraints']['maximum']) ? (float) $control['constraints']['maximum'] : 255,
            'step' => isset($control['constraints']['step']) ? (float) $control['constraints']['step'] : 1,
            'useLiveBinding' => true,
        ];
        if (strtolower((string) ($control['catalogComponentType'] ?? '')) === 'autotriggertimer') {
            $props['variant'] = 'timer';
            $props['valueSuffix'] = 's';
            $props['kicker'] = 'TIMER';
            $props['useLiveBinding'] = true;
            $props['showMinMaxLabels'] = true;
            $props = array_merge($props, $this->deviceUiTimerSliderStyleDefaults());
        }
    } elseif ($type === 'textfield') {
        $accessoryType = 'TextField';
        $props = [
            'useLiveBinding' => true,
            'valueBinding' => $binding,
            'sendButton' => $this->deviceUiBuildSendButtonProps($binding, true),
        ];
    } elseif ($type === 'button' || $type === 'elevatedbutton') {
        $accessoryType = $type === 'button' ? 'PowerButton' : 'ElevatedButton';
        $props = $type === 'button'
            ? $this->deviceUiPowerButtonFrame('#2563EB')
            : [
                'useLiveBinding' => true,
                'borderRadius' => 16,
                'backgroundColor' => '#2563EB',
                'color' => '#FFFFFF',
            ];
    }

    return [
        'type' => 'ParameterCard',
        'properties' => [
            'binding' => $binding,
            'valueBinding' => $binding,
            'textBinding' => $binding,
            'useLiveBinding' => true,
            'props' => [
                'label' => $label,
                'variant' => $type,
                'useLiveBinding' => true,
            ],
        ],
        'children' => [[
            'type' => $accessoryType,
            'properties' => [
                'binding' => $binding,
                'props' => $props,
            ],
        ]],
    ];
}

private function markDeviceUiSduiExtensionNotProduction(array $schema)
{
    if (!isset($schema['extensions']) || !is_array($schema['extensions'])) {
        $schema['extensions'] = [];
    }
    if (!isset($schema['extensions']['serverDrivenUi']) || !is_array($schema['extensions']['serverDrivenUi'])) {
        return $schema;
    }
    $sdui = &$schema['extensions']['serverDrivenUi'];
    if (!isset($sdui['rendererContract']) || !is_array($sdui['rendererContract'])) {
        $sdui['rendererContract'] = [];
    }
    $sdui['rendererContract']['productionReady'] = false;
    $sdui['rendererContract']['documentEntry'] = 'ui.widgetTree';
    if (!isset($schema['rendererContract']) || !is_array($schema['rendererContract'])) {
        $schema['rendererContract'] = [];
    }
    $schema['rendererContract']['documentEntry'] = 'ui.widgetTree';
    if (!isset($schema['meta']) || !is_array($schema['meta'])) {
        $schema['meta'] = [];
    }
    $schema['meta']['flutterEntry'] = 'ui.widgetTree';
    unset($sdui);

    return $schema;
}

private function stripDeviceUiWriteEventsFromTree(&$node)
{
    if (!is_array($node)) {
        return;
    }
    if (isset($node['events']) && is_array($node['events'])) {
        $keys = array_keys($node['events']);
        $isList = $keys === [] || $keys === range(0, count($keys) - 1);
        if ($isList) {
            $node['events'] = array_values(array_filter($node['events'], function ($event) {
                return !$this->deviceUiEventIsWrite($event);
            }));
        } else {
            foreach ($node['events'] as $eventName => $event) {
                $probe = is_array($event) ? $event : [];
                if (!isset($probe['event']) && !isset($probe['name'])) {
                    $probe['event'] = (string) $eventName;
                }
                if ($this->deviceUiEventIsWrite($probe)) {
                    unset($node['events'][$eventName]);
                }
            }
        }
    }
    foreach ($node as $key => &$child) {
        if ($key === 'events' || !is_array($child)) {
            continue;
        }
        $this->stripDeviceUiWriteEventsFromTree($child);
    }
    unset($child);
}

private function deviceUiEventIsWrite($event)
{
    if (!is_array($event)) {
        return false;
    }
    $name = strtolower(trim((string) ($event['event'] ?? $event['name'] ?? '')));
    if (in_array($name, ['onchange', 'onchanged', 'onchangeend', 'ontap', 'onvaluecommitted', 'onsubmitted', 'onselected', 'onsendtap', 'oncommand'], true)) {
        return true;
    }
    $action = $event['action'] ?? null;
    if (is_string($action) && stripos($action, 'update_') === 0) {
        return true;
    }
    if (!is_array($action)) {
        return false;
    }
    $type = strtolower(trim((string) ($action['type'] ?? '')));
    if (in_array($type, ['send', 'update', 'write'], true)) {
        return true;
    }
    $id = trim((string) ($action['id'] ?? $action['actionId'] ?? ''));

    return stripos($id, 'update_') === 0;
}

private function ensureDeviceUiDisabledOpacityOnTree(&$node, $opacity = 0.45)
{
    if (!is_array($node)) {
        return;
    }
    $props = isset($node['properties']) && is_array($node['properties']) ? $node['properties'] : [];
    $altProps = isset($node['props']) && is_array($node['props']) ? $node['props'] : [];
    $isDisabled = ($props['enabled'] ?? null) === false
        || ($props['disabled'] ?? null) === true
        || ($altProps['enabled'] ?? null) === false
        || ($altProps['disabled'] ?? null) === true
        || ($node['enabled'] ?? null) === false
        || ($node['disabled'] ?? null) === true;

    if ($isDisabled) {
        if (!isset($node['props']) || !is_array($node['props'])) {
            $node['props'] = [];
        }
        if (!isset($node['props']['disabledOpacity']) || !is_numeric($node['props']['disabledOpacity'])) {
            $node['props']['disabledOpacity'] = (float) $opacity;
        }
        if (isset($node['properties']) && is_array($node['properties']) && (!isset($node['properties']['disabledOpacity']) || !is_numeric($node['properties']['disabledOpacity']))) {
            $node['properties']['disabledOpacity'] = (float) $opacity;
        }
        if (!isset($node['style']) || !is_array($node['style'])) {
            $node['style'] = [];
        }
        if (!isset($node['style']['disabled']) || !is_array($node['style']['disabled'])) {
            $node['style']['disabled'] = [];
        }
        if (!isset($node['style']['disabled']['opacity']) || !is_numeric($node['style']['disabled']['opacity'])) {
            $node['style']['disabled']['opacity'] = (float) $opacity;
        }
    }

    foreach (['children'] as $key) {
        if (!empty($node[$key]) && is_array($node[$key])) {
            foreach ($node[$key] as &$child) {
                $this->ensureDeviceUiDisabledOpacityOnTree($child, $opacity);
            }
            unset($child);
        }
    }
    if (!empty($node['widgetTree']) && is_array($node['widgetTree'])) {
        $this->ensureDeviceUiDisabledOpacityOnTree($node['widgetTree'], $opacity);
    }
    if (!empty($node['component']['widgetTree']) && is_array($node['component']['widgetTree'])) {
        $this->ensureDeviceUiDisabledOpacityOnTree($node['component']['widgetTree'], $opacity);
    }
}

private function deviceUiFrameCommandValue(array $frame, $fieldName)
{
    $fieldName = strtolower(preg_replace('/[\s_]+/', '', trim((string) $fieldName)));
    foreach (['commands', 'response', 'error'] as $group) {
        foreach ($frame[$group] ?? [] as $field) {
            if (!is_array($field)) {
                continue;
            }
            if ($this->deviceUiFrameFieldName($field) === $fieldName && !$this->deviceUiFrameValueIsEmpty($field['value'] ?? null)) {
                return $field['value'];
            }
        }
    }

    return null;
}

private function hydrateDeviceUiProtocolMetaFromFrames(array $schema)
{
    if (empty($schema['controls']) || !is_array($schema['controls'])) {
        return $schema;
    }

    foreach ($schema['controls'] as &$control) {
        if (!is_array($control)) {
            continue;
        }
        $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
        $deviceFrame = isset($control['deviceFrame']) && is_array($control['deviceFrame']) ? $control['deviceFrame'] : [];
        $metaFrame = isset($meta['frame']) && is_array($meta['frame']) ? $meta['frame'] : [];
        $sourceFrame = !empty($deviceFrame) ? $deviceFrame : $metaFrame;
        $commandId = $this->deviceUiFirstNonEmpty(
            $meta['command_id'] ?? null,
            $meta['commandId'] ?? null,
            $this->deviceUiFrameCommandValue($sourceFrame, 'commandid')
        );
        $functionCode = $this->deviceUiFirstNonEmpty(
            $meta['function_code'] ?? null,
            $meta['functionCode'] ?? null,
            $this->deviceUiFrameCommandValue($sourceFrame, 'functioncode')
        );
        $actionId = $this->deviceUiFirstNonEmpty(
            $meta['action_id'] ?? null,
            $meta['actionId'] ?? null,
            $this->deviceUiFrameCommandValue($sourceFrame, 'actionid')
        );
        $meta['command_id'] = $commandId !== null ? $commandId : '';
        $meta['function_code'] = $functionCode !== null ? $functionCode : '';
        if ($actionId !== null) {
            $meta['action_id'] = $actionId;
        }
        if (!empty($deviceFrame) && $commandId !== null && $commandId !== '') {
            $deviceFrame['commandId'] = $commandId;
            $control['deviceFrame'] = $deviceFrame;
        }
        if (!empty($deviceFrame)) {
            if (empty($metaFrame)) {
                $meta['frame'] = $deviceFrame;
            } else {
                $meta['frame'] = $this->normalizeDeviceUiFrame(
                    $this->mergeDeviceUiFrameValues($metaFrame, $deviceFrame),
                    $metaFrame['name'] ?? ($deviceFrame['name'] ?? ''),
                    $meta
                );
            }
        } elseif (!empty($metaFrame)) {
            $meta['frame'] = $this->normalizeDeviceUiFrame($metaFrame, $metaFrame['name'] ?? '', $meta);
        }
        $control['protocolMeta'] = $meta;
    }
    unset($control);

    return $schema;
}

private function deviceUiFillMissingArray(array &$target, array $source)
{
    foreach ($source as $key => $value) {
        if (!array_key_exists($key, $target) || $target[$key] === null || $target[$key] === '') {
            $target[$key] = $value;
            continue;
        }
        if (is_array($target[$key]) && is_array($value) && $this->deviceUiIsAssocArray($value) && $this->deviceUiIsAssocArray($target[$key])) {
            $this->deviceUiFillMissingArray($target[$key], $value);
        }
    }
}

private function deviceUiIsAssocArray(array $value)
{
    if ($value === []) {
        return true;
    }

    return array_keys($value) !== range(0, count($value) - 1);
}

private function attachDeviceUiFlutterContract(array $schema)
{
    $controls = isset($schema['controls']) && is_array($schema['controls']) ? $schema['controls'] : [];
    $typedParameters = [];
    $flatParameters = [];
    foreach ($controls as $control) {
        if (!is_array($control)) {
            continue;
        }
        $key = trim((string) ($control['parameterKey'] ?? ''));
        if ($key === '') {
            continue;
        }
        $value = $control['initialValue'] ?? ($control['value'] ?? null);
        $valueType = strtolower((string) ($control['valueType'] ?? ''));
        if ($valueType === 'boolean') {
            $value = ($value === true || $value === 1 || $value === '1' || strtolower((string) $value) === 'true' || strtolower((string) $value) === 'on');
        }
        $typedParameters[$key] = [
            'value' => $value,
            'meta' => [
                'parameterKey' => $key,
                'controlId' => $control['id'] ?? $key,
                'parameterName' => $control['parameterName'] ?? ($control['parameter'] ?? ''),
            ],
        ];
        $flatParameters[$key] = $value;
    }

    $nodeId = trim((string) ($schema['node_id']['id'] ?? ($schema['meta']['app']['nodeId'] ?? '')));
    $modelNo = trim((string) ($schema['meta']['app']['modelNo'] ?? ($schema['node_id']['modelNo'] ?? '')));
    $schemaApi = (string) ($schema['meta']['links']['schema'] ?? ($schema['node_id']['schemaApi'] ?? ''));
    $scanApi = (string) ($schema['meta']['links']['scan'] ?? $schemaApi);
    $online = false;
    if (isset($schema['state']['online'])) {
        $online = (bool) $schema['state']['online'];
    } elseif (isset($schema['node_details'][0]['status']['connectivity']['connected'])) {
        $online = (bool) $schema['node_details'][0]['status']['connectivity']['connected'];
    }

    if (!isset($schema['extensions']) || !is_array($schema['extensions'])) {
        $schema['extensions'] = [];
    }

    $fallbackSdui = [
        'schemaVersion' => '4.0.0',
        'documentType' => 'serverDrivenUi',
        'state' => [
            'parameters' => $typedParameters,
            'online' => $online,
        ],
        'accessibility' => ['enabled' => true],
        'responsive' => $this->deviceUiResponsiveContract(),
    ];
    $existingSdui = $schema['extensions']['serverDrivenUi'] ?? null;
    if (!is_array($existingSdui) || empty($existingSdui)) {
        $schema['extensions']['serverDrivenUi'] = $fallbackSdui;
    } else {
        $this->deviceUiFillMissingArray($existingSdui, $fallbackSdui);
        if (empty($existingSdui['state']['parameters']) && $typedParameters !== []) {
            $existingSdui['state']['parameters'] = $typedParameters;
        }
        $schema['extensions']['serverDrivenUi'] = $existingSdui;
    }

    $fallbackUniversal = [
        'data' => [
            'state' => ['online' => $online],
            'parameters' => $flatParameters,
        ],
        'interactions' => [
            'controls' => [],
        ],
        'actions' => [],
        'designSystem' => [
            'componentStyles' => [
                'parameterCard' => [
                    'layout' => [
                        'type' => 'row',
                        'columns' => [
                            ['flex' => 1],
                            ['flex' => 0],
                        ],
                        'gap' => 8,
                        'alignItems' => 'center',
                    ],
                    'parts' => [
                        'iconBubble' => [
                            'size' => 28,
                            'width' => 28,
                            'height' => 28,
                            'shape' => 'circle',
                        ],
                    ],
                ],
                'pagerControls' => [
                    'layout' => [
                        'type' => 'grid',
                        'columns' => [48, '1fr', 48],
                        'alignItems' => 'center',
                        'gap' => 10,
                    ],
                ],
            ],
        ],
    ];
    $existingUniversal = $schema['extensions']['universalUi'] ?? null;
    if (!is_array($existingUniversal) || empty($existingUniversal)) {
        $schema['extensions']['universalUi'] = $fallbackUniversal;
    } else {
        $this->deviceUiFillMissingArray($existingUniversal, $fallbackUniversal);
        $schema['extensions']['universalUi'] = $existingUniversal;
        if (isset($schema['extensions']['universalUi']['designSystem']['componentStyles']['parameterCard']['parts']['iconBubble']['size'])
            && (int) $schema['extensions']['universalUi']['designSystem']['componentStyles']['parameterCard']['parts']['iconBubble']['size'] > 28) {
            $schema['extensions']['universalUi']['designSystem']['componentStyles']['parameterCard']['parts']['iconBubble']['size'] = 28;
            $schema['extensions']['universalUi']['designSystem']['componentStyles']['parameterCard']['parts']['iconBubble']['width'] = 28;
            $schema['extensions']['universalUi']['designSystem']['componentStyles']['parameterCard']['parts']['iconBubble']['height'] = 28;
        }
    }

    if (!isset($schema['dataSources']) || !is_array($schema['dataSources'])) {
        $schema['dataSources'] = [];
    }
    $deviceCommands = [
        'type' => 'adapter',
        'adapter' => 'deviceApi',
        'protocol' => 'mqtt',
        'frameSource' => 'deviceConfig.devices[0].pages',
        'controlFramePath' => 'controls[].deviceFrame',
        'endpoint' => $scanApi !== '' ? $scanApi : $schemaApi,
        'model_no' => $modelNo,
        'node_id' => $nodeId,
    ];
    if (!isset($schema['dataSources']['deviceCommands']) || !is_array($schema['dataSources']['deviceCommands'])) {
        $schema['dataSources']['deviceCommands'] = $deviceCommands;
    } else {
        $this->deviceUiFillMissingArray($schema['dataSources']['deviceCommands'], $deviceCommands);
    }

    if (!isset($schema['responsive']) || !is_array($schema['responsive']) || $schema['responsive'] === []) {
        $schema['responsive'] = $this->deviceUiResponsiveContract();
    } else {
        $this->deviceUiFillMissingArray($schema['responsive'], $this->deviceUiResponsiveContract());
    }

    return $schema;
}

private function collectDeviceUiSchemaControlListIds($node, array &$out)
{
    if (!is_array($node)) {
        return;
    }

    $type = strtolower(trim((string) ($node['type'] ?? '')));
    if ($type === 'schemacontrollist') {
        $ids = [];
        if (isset($node['properties']['controlIds']) && is_array($node['properties']['controlIds'])) {
            $ids = $node['properties']['controlIds'];
        } elseif (isset($node['controlIds']) && is_array($node['controlIds'])) {
            $ids = $node['controlIds'];
        }
        foreach ($ids as $id) {
            $out[] = (string) $id;
        }
    }

    foreach ($node as $key => $child) {
        if ($key === 'controls' || $key === 'catalog' || $key === 'registerComponents' || $key === 'exactHtmlPreview' || $key === 'icons' || $key === 'assets') {
            continue;
        }
        if (is_array($child)) {
            $this->collectDeviceUiSchemaControlListIds($child, $out);
        }
    }
}

private function rewriteDeviceUiSchemaLinkNodeIds(array $schema)
{
    $nodeId = '';
    if (!empty($schema['node_id']['id'])) {
        $nodeId = trim((string) $schema['node_id']['id']);
    } elseif (!empty($schema['meta']['app']['nodeId'])) {
        $nodeId = trim((string) $schema['meta']['app']['nodeId']);
    }
    if ($nodeId === '' || empty($schema['meta']['links']) || !is_array($schema['meta']['links'])) {
        return $schema;
    }

    foreach (['parameters', 'deviceInfo', 'schema', 'scan', 'wifiStatus'] as $key) {
        if (empty($schema['meta']['links'][$key]) || !is_string($schema['meta']['links'][$key])) {
            continue;
        }
        $url = $schema['meta']['links'][$key];
        if (strpos($url, 'node_id=') !== false) {
            $schema['meta']['links'][$key] = preg_replace('/([?&]node_id=)[^&]*/', '${1}' . rawurlencode($nodeId), $url);
        }
    }

    return $schema;
}

private function validateDeviceUiSchemaContract(array $schema)
{
    $result = ['valid' => true, 'errors' => [], 'warnings' => []];
    $controls = isset($schema['controls']) && is_array($schema['controls']) ? $schema['controls'] : [];
    $byId = [];
    $allowedKeys = [];

    foreach ($controls as $control) {
        if (!is_array($control) || empty($control['id'])) {
            $result['errors'][] = 'Control is missing canonical id.';
            continue;
        }
        $id = (string) $control['id'];
        if (isset($byId[$id])) {
            $result['errors'][] = 'Duplicate control id: ' . $id;
        }
        $byId[$id] = $control;
        $parameterKey = trim((string) ($control['parameterKey'] ?? ''));
        if ($parameterKey !== '') {
            $allowedKeys[$parameterKey] = true;
        }
        $expectedBinding = $parameterKey !== '' ? ('device.parameters.' . $parameterKey) : '';
        if ($expectedBinding !== '' && !empty($control['binding']) && $control['binding'] !== $expectedBinding) {
            $result['errors'][] = $id . ': binding must be ' . $expectedBinding;
        }
        $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
        $source = strtolower(trim((string) ($meta['ui_type_source'] ?? '')));
        $access = strtolower(trim((string) ($meta['access'] ?? '')));
        if (in_array($source, ['response', 'error'], true)) {
            $writable = false;
        } elseif ($access === '') {
            $writable = true;
        } elseif (in_array($access, ['read', 'readonly', 'read-only', 'read_only', 'none'], true)) {
            $writable = false;
        } else {
            $writable = strpos($access, 'write') !== false;
        }
        if (!$writable) {
            foreach (array_merge($control['events'] ?? [], $control['component']['events'] ?? []) as $event) {
                $type = strtolower((string) ($event['action']['type'] ?? ''));
                if ($type === 'send' || $type === 'update') {
                    $result['errors'][] = $id . ': Read access still has a ' . $type . ' event.';
                }
            }
        }
        $name = strtolower(str_replace(['_', '-'], ' ', (string) ($control['parameterName'] ?? $control['parameter'] ?? $control['label'] ?? '')));
        if (strpos($name, 'get status') !== false && !$writable) {
            $type = strtolower(str_replace(['_', '-'], '', (string) ($control['componentType'] ?? '')));
            $controlType = strtolower(str_replace(['_', '-'], '', (string) ($control['controlType'] ?? '')));
            if ($controlType !== '' && $type !== '' && $controlType !== $type) {
                $result['errors'][] = $id . ': Get Status response componentType and controlType do not agree.';
            }
        }
        if (strpos($name, 'get status') !== false && $writable) {
            $type = strtolower(str_replace(['_', '-'], '', (string) ($control['componentType'] ?? '')));
            $controlType = strtolower(str_replace(['_', '-'], '', (string) ($control['controlType'] ?? '')));
            $widget = strtolower(str_replace(['_', '-'], '', (string) ($control['flutterWidget'] ?? ($control['component']['flutterWidget'] ?? ''))));
            $expected = [
                'switch' => 'switch',
                'toggleswitch' => 'switch',
                'elevatedbutton' => 'elevatedbutton',
                'button' => 'powerbutton',
                'trigger' => 'elevatedbutton',
                'slider' => 'slider',
                'textfield' => 'textfield',
                'value' => 'text',
                'display' => 'text',
            ];
            if ($controlType !== '' && $controlType !== $type) {
                $result['errors'][] = $id . ': Get Status command componentType and controlType do not agree.';
            }
            if ($type !== '' && $widget !== '' && isset($expected[$type]) && $widget !== $expected[$type]) {
                $result['errors'][] = $id . ': Get Status command componentType and flutterWidget do not agree.';
            }
        }
        if ($writable) {
            $commands = isset($control['deviceFrame']['commands']) && is_array($control['deviceFrame']['commands'])
                ? $control['deviceFrame']['commands']
                : [];
            $presentFields = [];
            $occupiedBytes = [];
            foreach ($commands as $field) {
                if (!is_array($field)) {
                    continue;
                }
                $fieldName = $this->deviceUiFrameFieldName($field);
                if ($fieldName !== '' && !$this->deviceUiFrameValueIsEmpty($field['value'] ?? null)) {
                    $presentFields[$fieldName] = true;
                }

                $start = $field['byte_start_position'] ?? null;
                $length = $field['no_of_bytes'] ?? null;
                if (!is_numeric($start) || !is_numeric($length)) {
                    continue;
                }
                $start = (int) $start;
                $length = (int) $length;
                // Trigger may legitimately occupy byte position 0; treat it as a
                // UI marker rather than a physical overlapping frame field.
                if ($start === 0 && $fieldName === 'trigger') {
                    continue;
                }
                if ($start <= 0 || $length <= 0) {
                    $result['errors'][] = $id . ': frame field "' . $fieldName . '" has a non-physical byte position or length.';
                    continue;
                }
                for ($offset = $start; $offset < $start + $length; $offset++) {
                    if (isset($occupiedBytes[$offset])) {
                        $result['errors'][] = $id . ': frame fields overlap at byte position ' . $offset . '.';
                        break;
                    }
                    $occupiedBytes[$offset] = true;
                }
            }

            if (empty($presentFields['commandid'])) {
                $result['errors'][] = $id . ': writable control deviceFrame is missing CommandId value.';
            }
            if (empty($presentFields['functioncode'])) {
                $result['warnings'][] = $id . ': writable control deviceFrame is missing Function Code value.';
            }
            if (empty($presentFields['terminator'])) {
                $result['warnings'][] = $id . ': writable control deviceFrame is missing Terminator value.';
            }
        }

        // Declared interaction flags must agree with protocolMeta.access.
        if (empty($control['parserOnly'])) {
            if (!empty($control['writable']) !== $writable) {
                $result['errors'][] = $id . ': writable flag does not match protocolMeta.access.';
            }
            if (!empty($control['readOnly']) === $writable) {
                $result['errors'][] = $id . ': readOnly flag does not match protocolMeta.access.';
            }
            if (!empty($control['interactive']) !== $writable) {
                $result['errors'][] = $id . ': interactive flag does not match protocolMeta.access.';
            }
        }

        // Every projection must come from one authoritative component selection.
        $componentType = trim((string) ($control['componentType'] ?? ''));
        if ($componentType !== '' && strtolower($componentType) !== 'auto') {
            $expectedWidget = $this->deviceUiSchemaFlutterWidget($componentType);
            $expectedKey = $this->deviceUiNormalizeWidgetName($expectedWidget);
            $declaredWidget = trim((string) ($control['flutterWidget'] ?? ''));
            if ($declaredWidget !== '' && $this->deviceUiNormalizeWidgetName($declaredWidget) !== $expectedKey) {
                $result['errors'][] = $id . ': flutterWidget does not match componentType.';
            }
            $nestedType = trim((string) ($control['component']['type'] ?? ''));
            if ($nestedType !== '' && $this->deviceUiNormalizeWidgetName($nestedType) !== $this->deviceUiNormalizeWidgetName($componentType)) {
                $result['errors'][] = $id . ': component.type does not match componentType.';
            }
            $tree = $control['component']['widgetTree'] ?? null;
            $vocabulary = $this->deviceUiControlWidgetVocabulary();
            if (is_array($tree) && isset($vocabulary[$expectedKey])) {
                $treeTypes = [];
                $this->deviceUiCollectWidgetTypes($tree, $treeTypes);
                if (!isset($treeTypes[$expectedKey])) {
                    foreach (array_keys($treeTypes) as $presentWidget) {
                        if (isset($vocabulary[$presentWidget])) {
                            $result['errors'][] = $id . ': component.widgetTree renders ' . $presentWidget . ' but componentType is ' . $componentType . '.';
                            break;
                        }
                    }
                }
            }
        }
    }

    $order = isset($schema['settings']['controlOrder']) && is_array($schema['settings']['controlOrder'])
        ? array_values($schema['settings']['controlOrder'])
        : [];
    if (!empty($order)) {
        $expectedOrder = [];
        foreach ($controls as $index => $control) {
            if (!is_array($control) || empty($control['id'])) {
                continue;
            }
            if (!empty($control['parserOnly']) || (isset($control['visible']) && $control['visible'] === false) || (isset($control['renderable']) && $control['renderable'] === false)) {
                continue;
            }
            $expectedOrder[] = [
                'id' => (string) $control['id'],
                'pageNo' => (int) trim((string) ($control['pageNo'] ?? '0')),
                'sequence' => (int) ($control['sequence'] ?? 0),
                'index' => $index,
            ];
        }
        usort($expectedOrder, static function ($a, $b) {
            if ($a['pageNo'] !== $b['pageNo']) {
                return $a['pageNo'] <=> $b['pageNo'];
            }
            if ($a['sequence'] !== $b['sequence']) {
                return $a['sequence'] <=> $b['sequence'];
            }

            return $a['index'] <=> $b['index'];
        });

        foreach ($order as $controlId) {
            $controlId = (string) $controlId;
            if (!isset($byId[$controlId])) {
                $result['errors'][] = 'settings.controlOrder item does not match controls[].id: ' . $controlId;
                continue;
            }
            $ordered = $byId[$controlId];
            if (!empty($ordered['parserOnly']) || (isset($ordered['visible']) && $ordered['visible'] === false)) {
                $result['errors'][] = 'settings.controlOrder includes a hidden control: ' . $controlId;
            }
        }
        if ($order !== array_column($expectedOrder, 'id')) {
            $result['warnings'][] = 'settings.controlOrder does not match controls[].sequence order.';
        }
    }

    foreach ($schema['pages'] ?? [] as $page) {
        if (!is_array($page) || empty($page['controlIds']) || !is_array($page['controlIds'])) {
            continue;
        }
        foreach ($page['controlIds'] as $controlId) {
            if (!isset($byId[(string) $controlId])) {
                $result['errors'][] = 'pages[].controlIds item does not match controls[].id: ' . $controlId;
            }
        }
    }

    $listIds = [];
    $this->collectDeviceUiSchemaControlListIds($schema['ui'] ?? [], $listIds);
    foreach ($listIds as $controlId) {
        if ($controlId !== '' && !isset($byId[$controlId])) {
            $result['errors'][] = 'schemaControlList.controlIds item does not match controls[].id: ' . $controlId;
        }
    }

    foreach (['state.device.parameters' => $schema['state']['device']['parameters'] ?? null, 'deviceState.parameters' => $schema['deviceState']['parameters'] ?? null] as $label => $bag) {
        if (!is_array($bag)) {
            continue;
        }
        foreach (array_keys($bag) as $key) {
            if ($key !== '' && !isset($allowedKeys[$key])) {
                $result['errors'][] = 'Duplicate parameter-binding alias in ' . $label . ': ' . $key;
            }
        }
    }

    $nodeId = trim((string) ($schema['node_id']['id'] ?? ($schema['meta']['app']['nodeId'] ?? '')));
    if ($nodeId !== '' && isset($schema['meta']['links']) && is_array($schema['meta']['links'])) {
        foreach (['parameters', 'deviceInfo'] as $key) {
            $url = (string) ($schema['meta']['links'][$key] ?? '');
            if ($url !== '' && strpos($url, 'node_id=') !== false && strpos($url, $nodeId) === false && strpos($url, rawurlencode($nodeId)) === false) {
                $result['errors'][] = 'meta.links.' . $key . ' node_id does not match schema node_id.';
            }
        }
    }

    $result['valid'] = empty($result['errors']);
    return $result;
}

private function hydrateDeviceUiSchemaConfigs(array $schema)
{
    $metaByName = [];
    foreach ($schema['controls'] ?? [] as &$control) {
        if (!is_array($control)) {
            continue;
        }
        $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
        $name = strtolower(trim((string) ($control['parameterName'] ?? $control['parameter'] ?? $control['label'] ?? '')));
        $frame = isset($control['deviceFrame']) && is_array($control['deviceFrame']) ? $control['deviceFrame'] : [];
        if ($name !== '') {
            $metaByName[$name] = $meta;
        }
        if (!empty($frame)) {
            $control['deviceFrame'] = $this->normalizeDeviceUiFrame($frame, $frame['name'] ?? $name, $meta);
            if ($name !== '') {
                $metaByName[$name]['_frame'] = $control['deviceFrame'];
            }
        }
    }
    unset($control);

    $hydrateConfig = function (&$config) use ($metaByName) {
        if (!is_array($config) || empty($config['devices']) || !is_array($config['devices'])) {
            return;
        }
        foreach ($config['devices'] as &$device) {
            if (empty($device['pages']) || !is_array($device['pages'])) {
                continue;
            }
            foreach ($device['pages'] as &$frames) {
                if (!is_array($frames)) {
                    continue;
                }
                foreach ($frames as &$item) {
                    if (empty($item['frame']) || !is_array($item['frame'])) {
                        continue;
                    }
                    $frameName = strtolower(trim((string) ($item['frame']['name'] ?? '')));
                    $meta = ($frameName !== '' && isset($metaByName[$frameName])) ? $metaByName[$frameName] : [];
                    $sourceFrame = is_array($meta['_frame'] ?? null) ? $meta['_frame'] : [];
                    unset($meta['_frame']);
                    $item['frame'] = $this->normalizeDeviceUiFrame(
                        $this->mergeDeviceUiFrameValues($item['frame'], $sourceFrame),
                        $item['frame']['name'] ?? '',
                        $meta
                    );
                }
                unset($item);
            }
            unset($frames);
        }
        unset($device);
    };

    if (isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])) {
        $hydrateConfig($schema['deviceConfig']);
    }
    if (isset($schema['node_details'][0]['config']) && is_array($schema['node_details'][0]['config'])) {
        $hydrateConfig($schema['node_details'][0]['config']);
    }
    if (isset($schema['state']['device']['config']) && is_array($schema['state']['device']['config'])) {
        $hydrateConfig($schema['state']['device']['config']);
    }
    if (isset($schema['deviceState']['config']) && is_array($schema['deviceState']['config'])) {
        $hydrateConfig($schema['deviceState']['config']);
    }

    return $schema;
}

private function filterDeviceUiSchemaMap($map, array $allowedKeys)
{
    if (!is_array($map)) {
        return [];
    }

    $out = [];
    foreach ($map as $key => $value) {
        $key = (string) $key;
        if ($key === '' || !isset($allowedKeys[$key])) {
            continue;
        }
        if (is_array($value) && $value === []) {
            continue;
        }
        $out[$key] = $value;
    }

    return $out;
}

private function replaceDeviceUiSchemaControlLists(&$node, array $controlsByPage)
{
    if (!is_array($node)) {
        return;
    }

    $type = strtolower(trim((string) ($node['type'] ?? '')));
    if ($type === 'schemacontrollist') {
        $pageNo = '';
        if (isset($node['properties']['pageNo'])) {
            $pageNo = trim((string) $node['properties']['pageNo']);
        } elseif (isset($node['pageNo'])) {
            $pageNo = trim((string) $node['pageNo']);
        }
        if ($pageNo !== '' && isset($controlsByPage[$pageNo])) {
            $ids = array_values($controlsByPage[$pageNo]);
            if (isset($node['properties']) && is_array($node['properties'])) {
                $node['properties']['controlIds'] = $ids;
                $node['properties']['controlCount'] = count($ids);
            } else {
                $node['controlIds'] = $ids;
            }
        }
    }

    foreach ($node as $key => $child) {
        if (!is_array($child) || $this->deviceUiSchemaWalkSkipKey($key)) {
            continue;
        }
        $this->replaceDeviceUiSchemaControlLists($node[$key], $controlsByPage);
    }
}

private function remapDeviceUiFlatParameterBag(array $existing, array $parameterValues)
{
    $out = [];
    foreach ($parameterValues as $key => $value) {
        if (array_key_exists($key, $existing)) {
            $out[$key] = $existing[$key];
            continue;
        }
        $matched = $value;
        foreach ($existing as $oldKey => $oldValue) {
            if ($this->deviceUiSchemaParameterSlug($oldKey) === $key || strpos((string) $oldKey, $key) === 0 || strpos($key, (string) $oldKey) === 0) {
                $matched = $oldValue;
                break;
            }
        }
        $out[$key] = $matched;
    }

    return $out;
}

private function remapDeviceUiTypedParameterBag(array $bag, array $controls)
{
    $oldToNew = [];
    $out = [];
    $usedOldKeys = [];

    $matchEntry = function ($control, $requireExact) use ($bag, &$usedOldKeys) {
        $newKey = trim((string) ($control['parameterKey'] ?? ''));
        $controlId = trim((string) ($control['id'] ?? ''));
        $parameterName = strtolower(trim((string) ($control['parameterName'] ?? $control['parameter'] ?? $control['label'] ?? '')));
        $controlSlug = $controlId !== '' ? $this->deviceUiSchemaParameterSlug($controlId) : '';

        foreach ($bag as $oldKey => $entry) {
            $oldKey = (string) $oldKey;
            if (isset($usedOldKeys[$oldKey])) {
                continue;
            }
            $meta = is_array($entry) && isset($entry['meta']) && is_array($entry['meta']) ? $entry['meta'] : [];
            $metaParameterKey = trim((string) ($meta['parameterKey'] ?? ''));
            $metaControlId = trim((string) ($meta['controlId'] ?? ''));
            $metaName = strtolower(trim((string) ($meta['parameterName'] ?? $meta['label'] ?? '')));
            $exact = (
                $oldKey === $newKey
                || $metaParameterKey === $newKey
                || ($controlId !== '' && ($metaControlId === $controlId || $metaParameterKey === $controlId || $oldKey === $controlSlug))
                || ($parameterName !== '' && $metaName === $parameterName)
            );
            $fuzzy = !$requireExact && $newKey !== '' && (
                strpos($oldKey, $newKey) === 0
                || ($controlSlug !== '' && strpos($oldKey, $controlSlug) === 0)
            );
            if ($exact || $fuzzy) {
                return [$oldKey, $entry];
            }
        }

        return [null, null];
    };

    foreach ($controls as $control) {
        if (!is_array($control)) {
            continue;
        }
        $newKey = trim((string) ($control['parameterKey'] ?? ''));
        $controlId = trim((string) ($control['id'] ?? ''));
        if ($newKey === '') {
            continue;
        }

        [$matchedKey, $matched] = $matchEntry($control, true);
        if ($matched === null) {
            [$matchedKey, $matched] = $matchEntry($control, false);
        }

        if ($matched === null) {
            $out[$newKey] = [
                'type' => $control['valueType'] ?? 'string',
                'value' => $control['initialValue'] ?? ($control['value'] ?? null),
                'meta' => [
                    'controlId' => $controlId,
                    'parameterKey' => $newKey,
                    'parameterName' => $control['parameterName'] ?? $control['parameter'] ?? $control['label'] ?? $newKey,
                    'label' => $control['label'] ?? '',
                    'componentType' => $control['componentType'] ?? '',
                    'pageNo' => $control['pageNo'] ?? '1',
                ],
            ];
            continue;
        }

        $usedOldKeys[$matchedKey] = true;
        if (is_array($matched)) {
            if (!isset($matched['meta']) || !is_array($matched['meta'])) {
                $matched['meta'] = [];
            }
            $matched['meta']['controlId'] = $controlId;
            $matched['meta']['parameterKey'] = $newKey;
            $out[$newKey] = $matched;
        } else {
            $out[$newKey] = $matched;
        }

        if ($matchedKey !== $newKey) {
            $oldToNew[$matchedKey] = $newKey;
        }
    }

    return ['bag' => $out, 'oldToNew' => $oldToNew];
}

private function rewriteDeviceUiSchemaStateKeys(&$node, array $oldToNew)
{
    if ($oldToNew === [] || (!is_array($node) && !is_string($node))) {
        return;
    }

    if (is_string($node)) {
        foreach ($oldToNew as $oldKey => $newKey) {
            if ($oldKey === '' || $oldKey === $newKey) {
                continue;
            }
            $node = str_replace(
                ['state.parameters.' . $oldKey, 'device.parameters.' . $oldKey],
                ['state.parameters.' . $newKey, 'device.parameters.' . $newKey],
                $node
            );
            if ($node === (string) $oldKey) {
                $node = (string) $newKey;
            }
        }
        return;
    }

    foreach ($node as $key => $child) {
        if ($this->deviceUiSchemaWalkSkipKey($key)) {
            continue;
        }
        if (($key === 'stateKey' || $key === 'parameterKey') && is_string($child) && isset($oldToNew[$child])) {
            $node[$key] = $oldToNew[$child];
            continue;
        }
        $this->rewriteDeviceUiSchemaStateKeys($node[$key], $oldToNew);
    }
}

private function replaceDeviceUiControlBinding(array &$value, $oldBinding, $newBinding)
{
    $oldBinding = trim((string) $oldBinding);
    if ($oldBinding === '') {
        return;
    }
    foreach ($value as $key => &$child) {
        if (is_array($child)) {
            $this->replaceDeviceUiControlBinding($child, $oldBinding, $newBinding);
            continue;
        }
        if (is_string($child)) {
            if ($child === $oldBinding) {
                $child = $newBinding;
            } elseif ($child === '$' . $oldBinding) {
                $child = '$' . $newBinding;
            } elseif (strpos($child, $oldBinding) !== false) {
                $child = str_replace($oldBinding, $newBinding, $child);
            }
        }
    }
    unset($child);
}

private function deviceUiSchemaEventsForControl($componentType, $binding, $existingEvents = [])
{
    $type = strtolower(trim((string) $componentType));
    if (strpos($type, 'slider') !== false || in_array($type, ['powergauge', 'needlegauge'], true)) {
        return [
            ['event' => 'onChanged', 'action' => ['type' => 'update', 'binding' => $binding]],
            ['event' => 'onValueCommitted', 'action' => ['type' => 'send', 'binding' => $binding]],
        ];
    }
    if (strpos($type, 'switch') !== false || strpos($type, 'toggle') !== false) {
        return [
            ['event' => 'onChanged', 'action' => ['type' => 'update', 'binding' => $binding]],
            ['event' => 'onChanged', 'action' => ['type' => 'send', 'binding' => $binding]],
        ];
    }
    if (strpos($type, 'button') !== false || strpos($type, 'trigger') !== false) {
        return [['event' => 'onTap', 'action' => ['type' => 'send', 'binding' => $binding]]];
    }
    if (strpos($type, 'dropdown') !== false || strpos($type, 'segmented') !== false) {
        return [
            ['event' => 'onChanged', 'action' => ['type' => 'update', 'binding' => $binding]],
            ['event' => 'onSelected', 'action' => ['type' => 'send', 'binding' => $binding]],
        ];
    }
    if (strpos($type, 'textfield') !== false || strpos($type, 'text') !== false) {
        return [
            ['event' => 'onChanged', 'action' => ['type' => 'update', 'binding' => $binding]],
            ['event' => 'onSubmitted', 'action' => ['type' => 'send', 'binding' => $binding]],
        ];
    }
    return is_array($existingEvents) ? $existingEvents : [];
}

private function deviceUiSchemaValueType($componentType)
{
    $type = strtolower(trim((string) $componentType));
    if (strpos($type, 'switch') !== false || strpos($type, 'toggle') !== false) {
        return 'boolean';
    }
    if (strpos($type, 'slider') !== false || strpos($type, 'gauge') !== false || strpos($type, 'circle') !== false) {
        return 'number';
    }
    if (strpos($type, 'dropdown') !== false || strpos($type, 'segment') !== false || strpos($type, 'notification') !== false) {
        return 'enum';
    }
    if (strpos($type, 'button') !== false || strpos($type, 'trigger') !== false) {
        return 'trigger';
    }
    return 'string';
}

private function isDeviceUiDirectionParameter($paramName)
{
    $paramName = strtolower(trim((string) $paramName));
    if ($paramName === '') {
        return false;
    }
    if (strpos($paramName, 'data flow') !== false || strpos($paramName, 'dataflow') !== false) {
        return false;
    }

    return $paramName === 'direction' || (bool) preg_match('/(^|[^a-z0-9])direction([^a-z0-9]|$)/', $paramName);
}

private function deviceUiExtractOptionTokens($raw)
{
    if (is_array($raw)) {
        $tokens = [];
        foreach ($raw as $item) {
            $tokens = array_merge($tokens, $this->deviceUiExtractOptionTokens($item));
        }
        return $tokens;
    }
    $text = trim((string) $raw);
    if ($text === '' || $text === '[]') {
        return [];
    }
    if (preg_match_all('/\[([^\]]*)\]/', $text, $matches)) {
        $found = [];
        foreach ($matches[1] as $match) {
            $match = trim((string) $match);
            if ($match !== '') {
                $found[] = $match;
            }
        }
        if (!empty($found)) {
            return $found;
        }
    }

    return [$text];
}

private function deviceUiDirectionOptions(array $meta = [])
{
    $labels = $this->deviceUiExtractOptionTokens($meta['items'] ?? []);
    $values = $this->deviceUiExtractOptionTokens($meta['item_values'] ?? []);
    $pairs = [];
    if (empty($labels)) {
        // Protocol Data for Direction is a byte string ("00"/"01"), not bare 0/1.
        $pairs = [
            ['label' => '0', 'value' => '00'],
            ['label' => '1', 'value' => '01'],
        ];
    } else {
        foreach ($labels as $index => $label) {
            $pairs[] = [
                'label' => (string) $label,
                'value' => array_key_exists($index, $values) ? (string) $values[$index] : (string) $label,
            ];
        }
    }

    $uniqueValues = [];
    foreach ($pairs as $pair) {
        $uniqueValues[$pair['value']] = true;
    }
    if (count($uniqueValues) < count($pairs)) {
        foreach ($pairs as $index => $pair) {
            $pairs[$index]['value'] = $pair['label'];
        }
    }

    $antiTokens = ['1', '01', 'ccw', 'anticlockwise', 'anticlock', 'counterclockwise', 'counterclock', 'reverse', 'rev'];
    $cwTokens = ['0', '00', 'cw', 'clockwise', 'clock', 'forward', 'fwd'];
    $mapped = [];
    foreach ($pairs as $index => $pair) {
        $token = strtolower((string) preg_replace('/[^a-z0-9]+/', '', (string) $pair['label']));
        $valueToken = strtolower((string) preg_replace('/[^a-z0-9]+/', '', (string) $pair['value']));
        if (in_array($token, $antiTokens, true) || in_array($valueToken, $antiTokens, true)) {
            $isAnti = true;
        } elseif (in_array($token, $cwTokens, true) || in_array($valueToken, $cwTokens, true)) {
            $isAnti = false;
        } else {
            $isAnti = ($index === 1);
        }
        $mapped[] = [
            'label' => $isAnti ? 'Anti-clockwise' : 'Clockwise',
            'value' => $this->deviceUiNormalizeDirectionWireValue((string) $pair['value'], $isAnti),
        ];
    }
    if (count($mapped) < 2) {
        return [
            ['label' => 'Clockwise', 'value' => isset($mapped[0]['value']) ? $mapped[0]['value'] : '00'],
            ['label' => 'Anti-clockwise', 'value' => '01'],
        ];
    }

    return array_slice($mapped, 0, 2);
}

/**
 * Direction protocol Data is a 2-digit hex byte ("00"/"01"). Keep richer values as-is.
 */
private function deviceUiNormalizeDirectionWireValue($value, $isAnti = false)
{
    $raw = trim((string) $value);
    if ($raw === '') {
        return $isAnti ? '01' : '00';
    }
    if (preg_match('/^(0|00)$/i', $raw)) {
        return '00';
    }
    if (preg_match('/^(1|01)$/i', $raw)) {
        return '01';
    }
    // Numeric single-byte values (0-255) → 2-digit hex uppercase for the wire.
    if (preg_match('/^\d+$/', $raw)) {
        $num = (int) $raw;
        if ($num >= 0 && $num <= 255) {
            return strtoupper(str_pad(dechex($num), 2, '0', STR_PAD_LEFT));
        }
    }

    return $raw;
}

private function applyDeviceUiDirectionControlContract(array &$control)
{
    $parameterName = trim((string) ($control['parameterName'] ?? $control['parameter'] ?? $control['label'] ?? ''));
    if (!$this->isDeviceUiDirectionParameter($parameterName)) {
        return;
    }

    $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
    $source = strtolower(trim((string) ($meta['ui_type_source'] ?? '')));
    if (in_array($source, ['response', 'error'], true)) {
        return;
    }
    $options = $this->deviceUiDirectionOptions($meta);
    $rawValue = trim((string) ($control['initialValue'] ?? ($control['value'] ?? '')));
    $selected = $options[0]['value'];
    foreach ($options as $option) {
        $optionValue = (string) $option['value'];
        if ($rawValue !== '' && $optionValue === $rawValue) {
            $selected = $optionValue;
            break;
        }
        if ($rawValue !== '' && strcasecmp((string) $option['label'], $rawValue) === 0) {
            $selected = $optionValue;
            break;
        }
        // Match bare 0/1 against wire "00"/"01".
        if ($rawValue !== '' && preg_match('/^(0|1|00|01)$/i', $rawValue)
            && preg_match('/^(0|1|00|01)$/i', $optionValue)
            && (int) $rawValue === (int) $optionValue
        ) {
            $selected = $optionValue;
            break;
        }
    }
    if ($rawValue === '' || strcasecmp($rawValue, $parameterName) === 0) {
        $selected = (string) $options[0]['value'];
    }

    $binding = (string) ($control['binding'] ?? '');
    $control['componentType'] = 'segmented';
    $control['controlType'] = 'segmented';
    $control['catalogComponentType'] = null;
    $control['valueType'] = 'enum';
    $control['flutterWidget'] = 'SegmentedControl';
    $control['options'] = $options;
    $control['initialValue'] = $selected;
    $control['value'] = $selected;
    $control['events'] = $this->deviceUiSchemaEventsForControl('segmented', $binding, []);

    if (!isset($control['component']) || !is_array($control['component'])) {
        $control['component'] = [];
    }
    $control['component']['type'] = 'segmented';
    $control['component']['flutterWidget'] = 'SegmentedControl';
    $control['component']['valueType'] = 'enum';
    $control['component']['initialValue'] = $selected;
    $control['component']['options'] = $options;
    if (!isset($control['component']['props']) || !is_array($control['component']['props'])) {
        $control['component']['props'] = [];
    }
    $control['component']['props']['options'] = $options;
    $control['component']['props']['value'] = $selected;
    $control['component']['props']['useLiveBinding'] = true;
    $control['component']['widgetTree'] = [
        'type' => 'SegmentedControl',
        'properties' => [
            'binding' => $binding,
            'props' => $control['component']['props'],
            'events' => $control['events'],
        ],
    ];

    // Keep deviceFrame Data wire value in sync with the padded direction hex (Rule 1).
    if (isset($control['deviceFrame']['commands']) && is_array($control['deviceFrame']['commands'])) {
        foreach ($control['deviceFrame']['commands'] as &$frameField) {
            if (!is_array($frameField)) {
                continue;
            }
            $fieldName = $this->deviceUiFrameFieldName($frameField);
            if ($fieldName !== 'data' && $fieldName !== 'trigger') {
                continue;
            }
            $bytes = isset($frameField['no_of_bytes']) ? max(1, (int) $frameField['no_of_bytes']) : 1;
            $frameField['no_of_bytes'] = $bytes;
            $frameField['value'] = $this->deviceUiNormalizeProtocolHex($selected, $bytes) ?: $selected;
        }
        unset($frameField);
        if (isset($control['protocolMeta']['frame']) && is_array($control['protocolMeta']['frame'])) {
            $control['protocolMeta']['frame'] = $control['deviceFrame'];
        }
    }
}

private function dedupeDeviceUiSchemaControls(array $controls)
{
    $deduped = [];
    $positions = [];
    foreach ($controls as $control) {
        if (!is_array($control)) {
            continue;
        }
        $name = trim((string) ($control['parameterName'] ?? $control['parameter'] ?? $control['label'] ?? ''));
        $pageNo = trim((string) ($control['pageNo'] ?? '1'));
        $protocolMeta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
        $apiSource = trim((string) ($protocolMeta['ui_type_source'] ?? ''));
        $apiFieldIndex = trim((string) ($protocolMeta['field_index'] ?? ''));
        $apiUiType = trim((string) ($protocolMeta['ui_type'] ?? ''));
        $apiPart = $apiSource !== '' || $apiFieldIndex !== '' || $apiUiType !== ''
            ? ':' . strtolower($apiSource . ':' . $apiFieldIndex . ':' . $apiUiType)
            : '';
        $key = strtolower($pageNo . ':' . $name) . $apiPart;
        if ($name === '' || !isset($positions[$key])) {
            $positions[$key] = count($deduped);
            $deduped[] = $control;
            continue;
        }
        $existingIndex = $positions[$key];
        $existing = $deduped[$existingIndex];
        $existingSource = strtolower(trim((string) ($existing['componentTypeSource'] ?? '')));
        $newSource = strtolower(trim((string) ($control['componentTypeSource'] ?? '')));
        if ($existingSource !== 'api' && $newSource === 'api') {
            $deduped[$existingIndex] = $control;
        }
    }

    return array_values($deduped);
}

private function deviceUiSchemaComponentFromApi($apiType, $parameterName = '')
{
    $apiType = strtolower(trim((string) $apiType));
    $normalizedApiType = preg_replace('/[^a-z0-9]+/', ' ', $apiType);
    $normalizedApiType = trim((string) preg_replace('/\s+/', ' ', (string) $normalizedApiType));
    $parameterName = strtolower(trim((string) $parameterName));
    $catalog = 'auto';

    if ($this->isDeviceUiDirectionParameter($parameterName)) {
        $blockedTypes = ['none', 'no', 'na', 'n/a', 'null', 'false', 'disabled'];
        if ($apiType === '' || !in_array($apiType, $blockedTypes, true)) {
            return [
                'catalog' => 'segmented',
                'schema' => 'segmented',
            ];
        }
    }

    $patterns = [
        'auto trigger with timer' => 'autoTriggerTimer',
        'slider' => 'slider',
        'push button big' => 'pushButton',
        'push button' => 'pushButton',
        'power button' => 'pushButton',
        'brightness slider' => 'brightnessSlider',
        'airflow slider' => 'airflowSlider',
        'cct slider' => 'cctSlider',
        'saturation slider' => 'saturationSlider',
        'hue slider custom' => 'hueSliderCustom',
        'hue slider' => 'hueSlider',
        'hue circle' => 'hueCircle',
        'toast display' => 'toastDisplay',
        'toggle switch' => 'toggleSwitch',
        'checkbox' => 'toggleSwitch',
        'lock icon' => 'toggleSwitch',
        'toggle button' => 'toggleSwitch',
        'toggle' => 'toggleSwitch',
        'switch' => 'toggleSwitch',
        'notification' => 'notification',
        'text field' => 'enterText',
        'text input' => 'enterText',
        'enter text' => 'enterText',
        'entertext' => 'enterText',
        'cmd' => 'cmd',
        'console' => 'cmd',
        'drop down' => 'dropdown',
        'drop-down' => 'dropdown',
        'dropdown' => 'dropdown',
        'dropdown button' => 'dropdown',
        'dropdownbutton' => 'dropdown',
        'select' => 'dropdown',
        'text trigger' => 'trigger',
        'trigger' => 'trigger',
        'status led' => 'display',
        'network icon' => 'display',
        'alaram icon' => 'display',
        'alarm icon' => 'display',
        'eye icon' => 'display',
        'display' => 'display',
        'button big' => 'button',
        'button' => 'button',
        'text' => 'enterText',
    ];
    foreach ($patterns as $pattern => $id) {
        $normalizedPattern = trim((string) preg_replace('/\s+/', ' ', (string) preg_replace('/[^a-z0-9]+/', ' ', $pattern)));
        if ($apiType === $pattern || strpos($apiType, $pattern) !== false || $normalizedApiType === $normalizedPattern || preg_match('/(^| )' . preg_quote($normalizedPattern, '/') . '( |$)/', $normalizedApiType)) {
            if ($id === 'hueSlider' && strpos($apiType, 'custom') !== false) {
                continue;
            }
            $catalog = $id;
            break;
        }
    }

    if ($catalog === 'auto' && $parameterName !== '') {
        if (strpos($parameterName, 'power') !== false) {
            $catalog = 'toggleSwitch';
        } elseif (strpos($parameterName, 'brightness') !== false || strpos($parameterName, 'dimmer') !== false || strpos($parameterName, 'light level') !== false) {
            $catalog = 'brightnessSlider';
        } elseif (strpos($parameterName, 'airflow') !== false || strpos($parameterName, 'fan speed') !== false) {
            $catalog = 'airflowSlider';
        } elseif (strpos($parameterName, 'hue') !== false && strpos($parameterName, 'circle') !== false) {
            $catalog = 'hueCircle';
        } elseif (strpos($parameterName, 'hue') !== false) {
            $catalog = 'hueSlider';
        }
    }

    $schemaMap = [
        'toggleSwitch' => 'switch',
        'notification' => 'dropdown',
        'cmd' => 'textField',
        'console' => 'textField',
        'enterText' => 'textField',
        'slider' => 'slider',
        'brightnessSlider' => 'slider',
        'airflowSlider' => 'slider',
        'cctSlider' => 'slider',
        'saturationSlider' => 'slider',
        'hueSliderCustom' => 'slider',
        'hueSlider' => 'slider',
        'autoTriggerTimer' => 'slider',
        'hueCircle' => 'powerGauge',
        'dropdown' => 'dropdown',
        'trigger' => 'elevatedButton',
        'button' => 'button',
        'pushButton' => 'button',
        'segmented' => 'segmented',
        'toastDisplay' => 'value',
        'display' => 'value',
    ];

    return [
        'catalog' => $catalog,
        'schema' => $schemaMap[$catalog] ?? 'auto',
    ];
}

private function mergeDeviceUiNodeConfig(array $liveConfig, array $existingConfig, $modelno, $nodeId, array $info = [])
{
    $hasLivePages = isset($liveConfig['devices'][0]['pages'])
        && is_array($liveConfig['devices'][0]['pages'])
        && !empty($liveConfig['devices'][0]['pages']);
    $hasExistingPages = isset($existingConfig['devices'][0]['pages'])
        && is_array($existingConfig['devices'][0]['pages'])
        && !empty($existingConfig['devices'][0]['pages']);

    if (!$hasLivePages && $hasExistingPages) {
        $liveConfig = $existingConfig;
    } elseif ($hasLivePages && $hasExistingPages) {
        // Keep every page from both sides. Live frames win on shared page keys when
        // they carry protocol data; pages only present in studio/existing are retained
        // so multi-page models are not truncated to Page 1 from test_nodes.
        $livePages = $liveConfig['devices'][0]['pages'];
        $existingPages = $existingConfig['devices'][0]['pages'];
        $mergedPages = $this->mergeDeviceUiNodePages($livePages, $existingPages);
        foreach ($existingPages as $pageName => $frames) {
            if (!is_array($frames)) {
                continue;
            }
            if (!isset($mergedPages[$pageName]) || !is_array($mergedPages[$pageName]) || empty($mergedPages[$pageName])) {
                $mergedPages[$pageName] = $frames;
            }
        }
        $liveConfig['devices'][0]['pages'] = $mergedPages;
        $liveConfig['devices'][0]['total_pages'] = (string) max(1, count($mergedPages));
    } elseif (!$hasLivePages && !$hasExistingPages) {
        $liveConfig = !empty($liveConfig) ? $liveConfig : $existingConfig;
    }

    if (empty($liveConfig) || !is_array($liveConfig)) {
        $liveConfig = [];
    }

    if (!empty($existingConfig['info']) && is_array($existingConfig['info'])) {
        $liveConfig['info'] = array_merge($existingConfig['info'], is_array($liveConfig['info'] ?? null) ? $liveConfig['info'] : []);
    }
    if (!empty($info)) {
        foreach (['fw_version', 'model', 'name', 'type', 'product_type', 'product', 'brand', 'SSID', 'Access_Point', 'Guarantee_Card', 'Video', 'Need_Help', 'chat_status', 'Auto_Test', 'SIM_Activation_Request'] as $infoKey) {
            if (!isset($liveConfig['info'][$infoKey]) || $liveConfig['info'][$infoKey] === '' || $liveConfig['info'][$infoKey] === null) {
                if (isset($info[$infoKey]) && $info[$infoKey] !== '' && $info[$infoKey] !== null) {
                    if (!isset($liveConfig['info']) || !is_array($liveConfig['info'])) {
                        $liveConfig['info'] = [];
                    }
                    $liveConfig['info'][$infoKey] = $info[$infoKey];
                }
            }
        }
        if (empty($liveConfig['info']['name']) && !empty($info['product'])) {
            $liveConfig['info']['name'] = $info['product'];
        }
        if (empty($liveConfig['info']['product']) && !empty($info['product'])) {
            $liveConfig['info']['product'] = $info['product'];
        }
        if (empty($liveConfig['info']['brand']) && !empty($info['brand'])) {
            $liveConfig['info']['brand'] = $info['brand'];
        }
    }

    return $this->applyDeviceUiNodeIdentity($liveConfig, $modelno, $nodeId);
}

private function mergeDeviceUiNodePages(array $livePages, array $existingPages)
{
    $existingByName = [];
    foreach ($existingPages as $pageFrames) {
        if (!is_array($pageFrames)) {
            continue;
        }
        foreach ($pageFrames as $item) {
            if (!is_array($item) || empty($item['frame']) || !is_array($item['frame'])) {
                continue;
            }
            $name = strtolower(trim((string) ($item['frame']['name'] ?? '')));
            if ($name === '') {
                continue;
            }
            $existingByName[$name] = $item['frame'];
        }
    }

    foreach ($livePages as $pageName => $frames) {
        if (!is_array($frames)) {
            continue;
        }
        foreach ($frames as $index => $item) {
            $frame = is_array($item['frame'] ?? null) ? $item['frame'] : [];
            $name = strtolower(trim((string) ($frame['name'] ?? '')));
            $liveCommands = isset($frame['commands']) && is_array($frame['commands']) ? $frame['commands'] : [];
            if ($name !== '' && isset($existingByName[$name])) {
                $existingFrame = $existingByName[$name];
                if (empty($liveCommands) || $this->deviceUiFrameScore($existingFrame) > $this->deviceUiFrameScore($frame)) {
                    $livePages[$pageName][$index]['frame'] = $this->normalizeDeviceUiFrame(
                        $this->mergeDeviceUiFrameValues($existingFrame, $frame),
                        $frame['name'] ?? $name
                    );
                    continue;
                }
                $livePages[$pageName][$index]['frame'] = $this->normalizeDeviceUiFrame(
                    $this->mergeDeviceUiFrameValues($frame, $existingFrame),
                    $frame['name'] ?? $name
                );
                continue;
            }
            $livePages[$pageName][$index]['frame'] = $this->normalizeDeviceUiFrame($frame, $frame['name'] ?? $name);
        }
    }

    return $livePages;
}

private function deviceUiFrameScore(array $frame)
{
    $commands = isset($frame['commands']) && is_array($frame['commands']) ? $frame['commands'] : [];
    $score = count($commands) * 10;
    foreach ($commands as $command) {
        if (!is_array($command)) {
            continue;
        }
        if (!empty($command['asperui']) && is_array($command['asperui'])) {
            $score += 20;
        }
        if (!empty($command['asperui_values']) && is_array($command['asperui_values'])) {
            $score += 20;
        }
        $value = $command['value'] ?? null;
        if ($value !== '' && $value !== null) {
            $score += 5;
        }
        $uiType = strtolower(trim((string) ($command['ui_type'] ?? '')));
        if ($uiType !== '' && !in_array($uiType, ['none', 'text'], true)) {
            $score += 15;
        }
    }
    $score += isset($frame['response']) && is_array($frame['response']) ? count($frame['response']) : 0;
    $score += isset($frame['error']) && is_array($frame['error']) ? count($frame['error']) : 0;

    return $score;
}

private function deviceUiNodeDetailsOrigin()
{
    return 'https://efactor.tech';
}

private function fetchDeviceUiUrl($url)
{
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        if ($curl !== false) {
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_TIMEOUT => 4,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_HTTPHEADER => ['Accept: application/json'],
            ]);
            $raw = curl_exec($curl);
            $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if (is_string($raw) && $raw !== '' && $httpCode >= 200 && $httpCode < 400) {
                return $raw;
            }
        }
    }
    $context = stream_context_create([
        'http' => [
            'timeout' => 4,
            'ignore_errors' => true,
            'header' => "Accept: application/json\r\n",
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ]);
    $raw = @file_get_contents($url, false, $context);
    return is_string($raw) ? $raw : '';
}

private function decodeDeviceUiTestNodesPayload($raw)
{
    $raw = trim((string) $raw);
    if ($raw === '') {
        return [];
    }
    $decoded = json_decode($raw, true);
    if (is_array($decoded) && isset($decoded['node_details'])) {
        return $decoded;
    }
    $start = strpos($raw, '{"node_details"');
    if ($start === false) {
        return [];
    }
    $decoded = json_decode(substr($raw, $start), true);
    return is_array($decoded) ? $decoded : [];
}

private function deviceUiNodeDetailId(array $detail)
{
    return trim((string) ($detail['id'] ?? $detail['node_id'] ?? ''));
}

private function findDeviceUiNodeDetail(array $nodeDetails, $nodeId)
{
    $nodeId = trim((string) $nodeId);
    if ($nodeId === '') {
        return null;
    }
    foreach ($nodeDetails as $detail) {
        if (!is_array($detail)) {
            continue;
        }
        $id = $this->deviceUiNodeDetailId($detail);
        if ($id !== '' && strcasecmp($id, $nodeId) === 0) {
            return $detail;
        }
    }
    return null;
}

private function orderDeviceUiNodeDetails(array $nodeDetails, $nodeId)
{
    $matched = $this->findDeviceUiNodeDetail($nodeDetails, $nodeId);
    if (!$matched) {
        return [];
    }
    $matchedId = strtolower($this->deviceUiNodeDetailId($matched));
    $ordered = [$matched];
    foreach ($nodeDetails as $detail) {
        if (!is_array($detail)) {
            continue;
        }
        if (strtolower($this->deviceUiNodeDetailId($detail)) === $matchedId) {
            continue;
        }
        $ordered[] = $detail;
    }
    return $ordered;
}

private function isDeviceUiTestNodesPayload($payload, $nodeId)
{
    if (!is_array($payload) || empty($payload['node_details']) || !is_array($payload['node_details'])) {
        return false;
    }
    $nodeId = trim((string) $nodeId);
    $detail = $this->findDeviceUiNodeDetail($payload['node_details'], $nodeId);
    if (!$detail && $nodeId === '' && isset($payload['node_details'][0]) && is_array($payload['node_details'][0])) {
        $detail = $payload['node_details'][0];
    }
    if (!$detail) {
        return false;
    }
    return isset($detail['config']) && is_array($detail['config']);
}

private function fetchDeviceUiTestNodes($nodeId)
{
    $nodeId = trim((string) $nodeId);
    if ($nodeId === '') {
        return [];
    }
    $cacheKey = strtolower($nodeId);
    if (isset($this->deviceUiTestNodesCache[$cacheKey])) {
        return $this->deviceUiTestNodesCache[$cacheKey];
    }

    $encoded = rawurlencode($nodeId);
    $urls = [
        $this->deviceUiNodeDetailsOrigin() . '/v8/newapi.php?action=test_nodes&node_id=' . $encoded,
    ];

    $seen = [];
    foreach ($urls as $url) {
        if (isset($seen[$url])) {
            continue;
        }
        $seen[$url] = true;
        $payload = $this->decodeDeviceUiTestNodesPayload($this->fetchDeviceUiUrl($url));
        if ($this->isDeviceUiTestNodesPayload($payload, $nodeId)) {
            $this->deviceUiTestNodesCache[$cacheKey] = $payload;
            return $payload;
        }
    }

    $this->deviceUiTestNodesCache[$cacheKey] = [];
    return [];
}

private function applyDeviceUiTestNodesToSchema(array $schema, array $testNodes, $requestedNodeId)
{
    $requestedNodeId = trim((string) $requestedNodeId);
    $rawDetails = isset($testNodes['node_details']) && is_array($testNodes['node_details'])
        ? $testNodes['node_details']
        : [];
    $nodeDetails = $this->orderDeviceUiNodeDetails($rawDetails, $requestedNodeId);
    if (!$nodeDetails) {
        return $schema;
    }
    $outputNodes = $requestedNodeId !== ''
        ? $requestedNodeId
        : (isset($testNodes['nodes']) && $testNodes['nodes'] !== '' ? $testNodes['nodes'] : $requestedNodeId);
    $config = isset($nodeDetails[0]['config']) && is_array($nodeDetails[0]['config'])
        ? $nodeDetails[0]['config']
        : [];
    $schema['node_details'] = $nodeDetails;
    $schema['nodes'] = $outputNodes;
    if (!empty($config)) {
        $schema['deviceConfig'] = $config;
    }
    if (isset($schema['state']['device']) && is_array($schema['state']['device'])) {
        $schema['state']['device']['config'] = $config;
        $schema['state']['device']['node_id'] = $requestedNodeId;
    }
    if (isset($schema['deviceState']) && is_array($schema['deviceState'])) {
        $schema['deviceState']['config'] = $config;
        $schema['deviceState']['node_id'] = $requestedNodeId;
    }
    if (isset($schema['state']) && is_array($schema['state'])) {
        $schema['state']['node_id'] = $outputNodes;
    }
    return $schema;
}

private function isSafeDeviceUiIdentifier($value)
{
    $value = trim((string) $value);
    return $value !== '' && (bool) preg_match('/^[A-Za-z0-9][A-Za-z0-9 ._()\\-]*$/', $value);
}

private function resolveDeviceUiVendorBrandSlug($vendorName)
{
    switch (trim((string) $vendorName)) {
        case 'EFACTOR':
            return 'efactor';
        case 'BAJAJ':
            return 'Bajaj';
        case 'DHRIO':
            return 'dhrio';
        case 'Invendis':
            return 'Invendis';
        case 'KIOT':
            return 'KIOT';
        case 'AEI':
            return 'AEI';
        case 'THEJU':
            return 'Theju';
        case 'Skytec':
            return 'Skytec';
        case 'USHA':
            return 'USHA';
        case 'exhaust_hood':
            return 'exhaust';
        default:
            return 'efactor';
    }
}

private function resolveDeviceUiDeviceImageFile($modelno)
{
    $modelno = trim((string) $modelno);
    $groups = [
        'fan-EF-003FMN03.jpg' => ['EF-003FMN03', 'EF-002FMN02', 'EF-001FMN01', 'BAJAJ-001FMN01'],
        'DHRIO_01ATOMFAN-001_WHITE.jpg' => ['DHRIO_01ATOMFAN-001_WHITE'],
        'DHRIO_01SPTFAN-001_HIMGREY.jpg' => ['DHRIO_01SPTFAN-001_HIMGREY'],
        'DHRIO_01GLOFAN-001_MATBLUE.jpg' => ['DHRIO_01GLOFAN-001_MATBLUE'],
        'DHRIO_01RKAFAN5B-001_BROWN.jpg' => ['DHRIO_01RKAFAN5B-001_BROWN'],
        'DHRIO_01ARVFAN-001_SMKBROWN.jpg' => ['DHRIO_01ARVFAN-001_SMKBROWN'],
        'SKY_01ATOMFAN-001_WHITE.jpg' => ['SKY_01ATOMFAN-001_WHITE'],
        'SKY_01RKAFAN5B-001_BROWN.jpg' => ['SKY_01RKAFAN5B-001_BROWN'],
        'SKY_01GLOFAN-001_MATBLUE.jpg' => ['SKY_01GLOFAN-001_MATBLUE'],
        'kiot.jpg' => [
            'KIOT Touch Switch- 8S_1F',
            'KIOT Touch Switch- 8SW',
            'KIOT Touch Switch- 10SW',
            'KIOT Touch Switch_C6- 10SW',
            'KIOT Touch Switch_C6- 8SW',
            'KIOT Touch Switch_C6- 8SW_1F',
        ],
        'efact_cooker.jpg' => ['EFCKER V1.0'],
        'light_org.jpg' => ['EF-001LIGHT01', 'EF-002CWLIGHT02', 'EF-001RGBLIGHT01', 'EF-003WLIGHT03'],
        'outlet.jpg' => ['EF-001OULT01', 'EF-001SOCKET_6A01', 'EF-001SOCKET_10A01'],
        'terminal.jpg' => [
            'EF001-TERMINAL_BLOCK_6A_01',
            'EF001-TERMINAL_BLOCK_32A_01',
            'EF001-TERMINAL_BLOCK_16A_01',
            'EF001-TERMINAL_BLOCK_10A_01',
        ],
        'ceiling_rose.jpg' => ['EF001-CEILING_ROSE_6A_01', 'EF001-CEILING_ROSE_10A_01'],
        'swpcp.jpg' => ['EFSWPCP V1.0', 'LTE_1PH_STARTER'],
        'invendis_device.jpg' => ['INVENDIS_MFM_V1'],
        'mcb.jpg' => ['EF-001-MCB-01'],
        'EF-SOFT-001.png' => ['EF-SOFT-001'],
        'LTE_3PH_STARTER.jpeg' => ['LTE_3PH_STARTER'],
        'smart plug.jpeg' => ['EF_smart_plug'],
        'QR_SCANNER.jpeg' => ['QR_SCANNER'],
        'EF_lable_1.jpg' => ['EF_Disp_LB_COLOUR_2_1'],
        'fire_tiny.jpg' => ['EFWF2GAV1'],
        'exhaust_hood.jpg' => ['DMEXHD V1.0'],
        'TOUCH_HD_V2.jpeg' => ['TOUCH HD V2'],
        'KIOT_TOUCH_SW_4T_V2.jpeg' => ['KIOT TOUCH SW 4T V2'],
        'KIOT_TOUCH_FAN.jpeg' => ['KIOT TOUCH FAN'],
        'Smart Touch Panel.jpeg' => ['Smart Touch Panel'],
        'gateway_org.jpg' => ['EF_RF_GATEW V1.0'],
    ];
    foreach ($groups as $file => $models) {
        if (in_array($modelno, $models, true)) {
            return $file;
        }
    }
    return 'input_output.jpg';
}

private function deviceUiIsPlaceholderValue($value)
{
    $value = trim((string) $value);
    if ($value === '' || strcasecmp($value, 'null') === 0) {
        return true;
    }
    return (bool) preg_match('/Logo_new\\.(png|jpe?g)$/i', $value);
}

private function deviceUiIsGenericBrandName($value)
{
    $value = strtolower(preg_replace('/[^a-z0-9]+/i', '', (string) $value));
    return in_array($value, array('efactor', 'efact', 'e', 'brand', 'defaultbrand'), true);
}

private function deviceUiNonEmptyString($value)
{
    $value = trim((string) $value);
    if ($value === '' || strcasecmp($value, 'null') === 0) {
        return '';
    }
    return $value;
}

private function deviceUiFirstNonEmpty(...$values)
{
    foreach ($values as $value) {
        $clean = $this->deviceUiNonEmptyString($value);
        if ($clean !== '') {
            return $clean;
        }
    }
    return '';
}

private function deviceUiRowHasLiveIdentity(array $row)
{
    return $this->deviceUiFirstNonEmpty(
        $row['current_devicetype'] ?? '',
        $row['device_type'] ?? '',
        $row['ssid'] ?? '',
        $row['mac'] ?? '',
        $row['module_type'] ?? '',
        $row['fw_version'] ?? ''
    ) !== '';
}

private function loadDeviceUiEfactorProductRow($nodeId, $modelno = '')
{
    if (!$this->db->tableExists('efactorproduct')) {
        return [];
    }
    try {
        $builder = $this->db->table('efactorproduct')->select('*');
        $nodeId = trim((string) $nodeId);
        $modelno = trim((string) $modelno);
        if ($nodeId !== '' && strtolower($nodeId) !== strtolower($modelno)) {
            $builder->where('node_id', $nodeId);
        } elseif ($modelno !== '') {
            $builder->groupStart()
                ->where('model_name', $modelno)
                ->orWhere('model_number', $modelno)
                ->groupEnd();
        } else {
            return [];
        }
        $row = $builder
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();
        return is_array($row) ? $row : [];
    } catch (\Throwable $exception) {
        return [];
    }
}

private function loadDeviceUiModelDeviceRow($modelName, $nodeId)
{
    $modelName = trim((string) $modelName);
    $nodeId = trim((string) $nodeId);
    if ($modelName === '' || $nodeId === '') {
        return [];
    }
    if (strcasecmp($modelName, 'EFDEFLT V1.0') === 0) {
        return $this->loadDeviceUiEfactorProductRow($nodeId, $modelName);
    }
    if (!$this->isSafeDeviceUiIdentifier($modelName)) {
        return [];
    }
    try {
        if (!$this->db->tableExists($modelName)) {
            return [];
        }
        $table = str_replace('`', '', $modelName);
        $row = $this->db->query(
            'SELECT * FROM `' . $table . '` WHERE node_id = ? LIMIT 1',
            [$nodeId]
        )->getRowArray();
        return is_array($row) ? $row : [];
    } catch (\Throwable $exception) {
        return [];
    }
}

private function loadDeviceUiLatestModelDeviceRow($modelName)
{
    $modelName = trim((string) $modelName);
    if ($modelName === '' || strcasecmp($modelName, 'EFDEFLT V1.0') === 0) {
        return [];
    }
    if (!$this->isSafeDeviceUiIdentifier($modelName)) {
        return [];
    }
    try {
        if (!$this->db->tableExists($modelName)) {
            return [];
        }
        $table = str_replace('`', '', $modelName);
        try {
            $row = $this->db->query('SELECT * FROM `' . $table . '` ORDER BY id DESC LIMIT 1')->getRowArray();
        } catch (\Throwable $exception) {
            $row = $this->db->query('SELECT * FROM `' . $table . '` ORDER BY node_id DESC LIMIT 1')->getRowArray();
        }
        return is_array($row) ? $row : [];
    } catch (\Throwable $exception) {
        return [];
    }
}

private function loadDeviceUiLiveProductContext($modelno, $nodeId)
{
    $modelno = trim((string) $modelno);
    $nodeId = trim((string) $nodeId);
    $cacheKey = strtolower($modelno) . '|' . strtolower($nodeId);
    if (isset($this->deviceUiLiveProductCache[$cacheKey])) {
        return $this->deviceUiLiveProductCache[$cacheKey];
    }

    $origin = $this->deviceUiNodeDetailsOrigin();
    $product = $this->loadDeviceUiEfactorProductRow($nodeId, '');
    $resolvedNodeId = $nodeId;
    $modelNumber = $this->deviceUiFirstNonEmpty($product['model_number'] ?? '', $product['model_name'] ?? '', $modelno);
    if ($modelNumber === '') {
        $modelNumber = $modelno;
    }

    $deviceRow = $resolvedNodeId !== '' ? $this->loadDeviceUiModelDeviceRow($modelNumber, $resolvedNodeId) : [];

    $apiRow = [];
    $userId = '';
    $apiNodeId = '';
    try {
        $apiSelect = 'user_id, brand_name, product_name, number_of_pages, node_id';
        if ($this->db->fieldExists('brand_logo', 'product_api')) {
            $apiSelect .= ', brand_logo';
        }
        $apiRow = $this->db->table('product_api')
            ->select($apiSelect)
            ->where('model_name', $modelNumber)
            ->orderBy('selected', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();
        if (!is_array($apiRow)) {
            $apiRow = [];
        }
    } catch (\Throwable $exception) {
        try {
            $apiRow = $this->db->table('product_api')
                ->select('user_id, brand_name, product_name, number_of_pages')
                ->where('model_name', $modelNumber)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();
            $apiRow = is_array($apiRow) ? $apiRow : [];
        } catch (\Throwable $inner) {
            $apiRow = [];
        }
    }
    if (is_array($apiRow)) {
        $userId = $this->deviceUiNonEmptyString($apiRow['user_id'] ?? '');
    }

    if (!$product && $modelNumber !== '' && $nodeId === '') {
        $product = $this->loadDeviceUiEfactorProductRow('', $modelNumber);
    }

    $vendorName = $this->deviceUiNonEmptyString($product['vendor_name'] ?? '');
    $brandSlug = $this->resolveDeviceUiVendorBrandSlug($vendorName !== '' ? $vendorName : 'EFACTOR');
    $deviceType = $this->deviceUiFirstNonEmpty($deviceRow['device_type'] ?? '', $product['device_type'] ?? '');
    $currentDeviceType = $this->deviceUiFirstNonEmpty($deviceRow['current_devicetype'] ?? '', $product['current_devicetype'] ?? '');
    $ssid = $this->deviceUiFirstNonEmpty($deviceRow['ssid'] ?? '', $product['ssid'] ?? '');
    $mac = $this->deviceUiFirstNonEmpty($deviceRow['mac'] ?? '', $product['mac'] ?? '');
    $moduleType = $this->deviceUiFirstNonEmpty($deviceRow['module_type'] ?? '', $product['module_type'] ?? '');
    $productType = $this->deviceUiFirstNonEmpty($product['product_type'] ?? '', 'IOT');
    $internet = strtolower($this->deviceUiFirstNonEmpty($deviceRow['internet'] ?? '', $product['internet'] ?? ''));
    $isAdded = !empty($product['is_added']) && strcasecmp((string) $product['is_added'], 'NULL') !== 0 && $product['is_added'] !== '0';
    $addGateway = $this->deviceUiNonEmptyString($product['add_gateway'] ?? '');
    $accessPoint = '';
    if ($currentDeviceType !== '') {
        $parts = explode('_', $currentDeviceType, 2);
        $accessPoint = 'EWIFI_' . (isset($parts[1]) && $parts[1] !== '' ? $parts[1] : $currentDeviceType);
    }

    $simBased = 'false';
    try {
        if ($this->db->tableExists('vendorproducts') && $modelNumber !== '') {
            $vendorRow = $this->db->table('vendorproducts')
                ->select('sim_based')
                ->where('modelname', $modelNumber)
                ->get()
                ->getRowArray();
            if (is_array($vendorRow) && isset($vendorRow['sim_based'])) {
                $simBased = strtolower(trim((string) $vendorRow['sim_based'])) === 'true' ? 'true' : 'false';
            }
        }
    } catch (\Throwable $exception) {
        $simBased = 'false';
    }

    $brandLogoAsset = $this->resolveDeviceUiBrandLogoAsset($modelNumber, $resolvedNodeId, [
        'userId' => $userId,
        'vendorId' => $this->deviceUiNonEmptyString($product['vendor_id'] ?? ''),
        'vendorName' => $vendorName,
        'vendorType' => $this->deviceUiNonEmptyString($product['vendor_type'] ?? ''),
        'brandName' => $this->deviceUiFirstNonEmpty($apiRow['brand_name'] ?? '', $vendorName, 'Efactor'),
        'apiBrandLogo' => $this->deviceUiNonEmptyString($apiRow['brand_logo'] ?? ''),
        'productBrandLogo' => $this->deviceUiFirstNonEmpty($product['brand_logo'] ?? '', $product['brandlogo'] ?? ''),
        'deviceBrandLogo' => $this->deviceUiFirstNonEmpty(
            $deviceRow['brandlogo'] ?? '',
            $deviceRow['brand_logo'] ?? '',
            $deviceRow['brandLogo'] ?? ''
        ),
    ]);
    $brandLogoDarkUrl = $this->deviceUiBrandLogoDarkUrl($brandLogoAsset, $origin, $brandSlug);

    $helpUrl = $origin . '/v8/call_services.php?node_id=' . rawurlencode($resolvedNodeId);
    $connected = ($productType !== 'IOT') ? true : ($internet === 'true');
    $outputNodeId = ($addGateway !== '' && $isAdded) ? $addGateway : $resolvedNodeId;

    $childNodes = [];
    if ($isAdded && $addGateway !== '' && $this->db->tableExists('efactorproduct')) {
        try {
            $rows = $this->db->table('efactorproduct')
                ->select('node_id, current_devicetype, mac, model_number, device_type')
                ->where('add_gateway', $addGateway)
                ->get()
                ->getResultArray();
            foreach ($rows as $child) {
                $childId = trim((string) ($child['node_id'] ?? ''));
                if ($childId === '' || strcasecmp($childId, $resolvedNodeId) === 0) {
                    continue;
                }
                $childNodes[] = [
                    'node_id' => $childId,
                    'current_devicetype' => trim((string) ($child['current_devicetype'] ?? '')),
                    'mac' => trim((string) ($child['mac'] ?? '')),
                    'model_number' => trim((string) ($child['model_number'] ?? '')),
                    'device_type' => trim((string) ($child['device_type'] ?? '')),
                ];
            }
        } catch (\Throwable $exception) {
            $childNodes = [];
        }
    }

    $context = [
        'node_id' => $resolvedNodeId,
        'output_node_id' => $outputNodeId,
        'model_number' => $modelNumber,
        'device_name' => trim((string) ($product['device_name'] ?? '')),
        'current_devicetype' => $currentDeviceType,
        'device_type' => $deviceType,
        'vendor_name' => $vendorName,
        'brand_slug' => $brandSlug,
        'module_type' => $moduleType,
        'product_type' => $productType,
        'ssid' => $ssid,
        'mac' => $mac,
        'internet' => $internet,
        'connected' => $connected,
        'is_added' => $isAdded,
        'add_gateway' => $addGateway,
        'device_p2p_control' => $this->deviceUiFirstNonEmpty($deviceRow['device_p2p_control'] ?? '', $product['device_p2p_control'] ?? ''),
        'device_comm_page' => $this->deviceUiFirstNonEmpty($deviceRow['device_comm_page'] ?? '', $product['device_comm_page'] ?? ''),
        'status_page' => $this->deviceUiFirstNonEmpty($deviceRow['status_page'] ?? '', $product['status_page'] ?? ''),
        'url_path' => $this->deviceUiNonEmptyString($deviceRow['url_path'] ?? ''),
        'fw_version' => '1.5.1',
        'user_id' => $userId,
        'sim_based' => $simBased,
        'brand_logo' => $brandLogoAsset['url'],
        'brand_logo_dark' => $brandLogoDarkUrl,
        'device_image' => $origin . '/files/brand_logo/' . $this->resolveDeviceUiDeviceImageFile($modelNumber),
        'Need_Help' => $helpUrl,
        'chat_status' => null,
        'Guarantee_Card' => $origin . '/v8/api.php?action=guarantee_card_display&',
        'Video' => $origin . '/v8/user_guide_support.php?product=' . rawurlencode($deviceType !== '' ? $deviceType : $modelNumber),
        'Auto_Test' => $origin . '/v8/auto_test.php?',
        'SIM_Activation_Request' => $origin . '/v8/sim_activation_request.php?node_id=' . rawurlencode($resolvedNodeId),
        'Access_Point' => $accessPoint,
        'product_name' => trim((string) ($apiRow['product_name'] ?? '')),
        'brand_name' => trim((string) (($apiRow['brand_name'] ?? '') !== '' ? $apiRow['brand_name'] : $vendorName)),
        'number_of_pages' => $apiRow['number_of_pages'] ?? null,
        'child_nodes' => $childNodes,
    ];

    $this->deviceUiLiveProductCache[$cacheKey] = $context;
    return $context;
}

private function applyDeviceUiLiveProductToNodeConfig(array $config, array $live, $modelno, $nodeId)
{
    $modelno = trim((string) $modelno);
    $requestedNodeId = trim((string) $nodeId);
    $nodeId = $requestedNodeId !== '' ? $requestedNodeId : trim((string) ($live['node_id'] ?? ''));
    $deviceName = trim((string) ($live['current_devicetype'] ?? ''));
    if ($deviceName === '') {
        $deviceName = trim((string) ($live['device_name'] ?? ''));
    }
    $deviceType = trim((string) ($live['device_type'] ?? ''));
    $type = $deviceType !== '' ? ('efactor.device.' . $deviceType) : 'efactor.device.';
    $mac = trim((string) ($live['mac'] ?? ''));
    $optionalOrNull = static function ($value) {
        $value = is_string($value) ? trim($value) : $value;
        if ($value === '' || $value === null || (is_string($value) && strcasecmp($value, 'null') === 0)) {
            return null;
        }
        return $value;
    };

    if (!isset($config['devices'][0]) || !is_array($config['devices'][0])) {
        $config['devices'] = [[
            'name' => $deviceName !== '' ? $deviceName : $modelno,
            'total_pages' => null,
            'attributes' => [],
            'Device Id' => $nodeId,
            'pages' => [],
            'primary' => 'power',
            'type' => $type,
        ]];
    }

    $device = $config['devices'][0];
    if ($deviceName !== '') {
        $device['name'] = $deviceName;
    }
    $device['Device Id'] = $nodeId;
    $device['primary'] = !empty($device['primary']) ? $device['primary'] : 'power';
    if ($deviceType !== '') {
        $device['type'] = $type;
    } elseif ($this->deviceUiIsPlaceholderValue($device['type'] ?? '') || $device['type'] === 'efactor.device.') {
        $device['type'] = $type;
    }
    $device['brand_logo'] = $live['brand_logo'] ?? ($device['brand_logo'] ?? null);
    $device['brand_logo_dark'] = $live['brand_logo_dark'] ?? ($device['brand_logo_dark'] ?? null);
    $device['Need_Help'] = $live['Need_Help'] ?? ($device['Need_Help'] ?? null);
    $device['chat_status'] = array_key_exists('chat_status', $live) ? $live['chat_status'] : ($device['chat_status'] ?? null);
    $device['device_image'] = $live['device_image'] ?? ($device['device_image'] ?? null);
    $device['device_p2p_control'] = $optionalOrNull($live['device_p2p_control'] ?? ($device['device_p2p_control'] ?? null));
    $device['Device Communication'] = $optionalOrNull($live['device_comm_page'] ?? ($device['Device Communication'] ?? null));
    $device['Status'] = $optionalOrNull($live['status_page'] ?? ($device['Status'] ?? null));

    $requiredAttrs = [
        ['name' => 'Device Id', 'value' => $nodeId],
    ];
    if ($mac !== '') {
        $requiredAttrs[] = ['name' => 'mac', 'value' => $mac];
    }
    $deviceAttrs = isset($device['attributes']) && is_array($device['attributes']) ? $device['attributes'] : [];
    $deviceAttrs = array_values(array_filter($deviceAttrs, static function ($attribute) {
        if (!is_array($attribute)) {
            return false;
        }
        return strtolower(trim((string) ($attribute['name'] ?? ''))) !== 'model';
    }));
    $device['attributes'] = $this->upsertDeviceUiAttributes($deviceAttrs, $requiredAttrs);
    $config['devices'][0] = $device;

    if (!empty($live['child_nodes']) && is_array($live['child_nodes'])) {
        $devices = [$config['devices'][0]];
        foreach ($live['child_nodes'] as $child) {
            if (!is_array($child)) {
                continue;
            }
            $childId = trim((string) ($child['node_id'] ?? ''));
            if ($childId === '') {
                continue;
            }
            $childDevice = $config['devices'][0];
            $childName = trim((string) ($child['current_devicetype'] ?? ''));
            $childDevice['name'] = $childName !== '' ? $childName : $childDevice['name'];
            $childDevice['Device Id'] = $childId;
            $childDevice['Need_Help'] = $this->deviceUiNodeDetailsOrigin() . '/v8/call_services.php?node_id=' . rawurlencode($childId);
            $childModel = $this->deviceUiNonEmptyString($child['model_number'] ?? '');
            if ($childModel !== '') {
                $childBrand = $this->resolveDeviceUiBrandLogoAsset($childModel, $childId);
                if (!empty($childBrand['url']) && !$this->deviceUiIsPlaceholderValue($childBrand['url'])) {
                    $childDevice['brand_logo'] = $childBrand['url'];
                }
            }
            $childAttrs = [
                ['name' => 'Device Id', 'value' => $childId],
            ];
            if (!empty($child['mac'])) {
                $childAttrs[] = ['name' => 'mac', 'value' => $child['mac']];
            }
            $childDevice['attributes'] = $this->upsertDeviceUiAttributes([], $childAttrs);
            $devices[] = $childDevice;
        }
        $config['devices'] = $devices;
    }

    $info = isset($config['info']) && is_array($config['info']) ? $config['info'] : [];
    unset($info['product'], $info['brand']);
    $info = array_merge($info, array_filter([
        'fw_version' => '1.5.1',
        'model' => $live['model_number'] ?? $modelno,
        'name' => $deviceName !== '' ? $deviceName : ($info['name'] ?? $modelno),
        'type' => ($live['module_type'] ?? '') !== '' ? $live['module_type'] : ($info['type'] ?? 'IOT'),
        'product_type' => ($live['product_type'] ?? '') !== '' ? $live['product_type'] : ($info['product_type'] ?? 'IOT'),
        'SSID' => ($live['ssid'] ?? '') !== '' ? $live['ssid'] : ($info['SSID'] ?? null),
        'Access_Point' => ($live['Access_Point'] ?? '') !== '' ? $live['Access_Point'] : ($info['Access_Point'] ?? null),
        'Guarantee_Card' => $live['Guarantee_Card'] ?? ($info['Guarantee_Card'] ?? null),
        'Video' => $live['Video'] ?? ($info['Video'] ?? null),
        'Need_Help' => $live['Need_Help'] ?? ($info['Need_Help'] ?? null),
        'chat_status' => array_key_exists('chat_status', $live) ? $live['chat_status'] : ($info['chat_status'] ?? null),
        'Auto_Test' => $live['Auto_Test'] ?? ($info['Auto_Test'] ?? null),
    ], static function ($value) {
        return $value !== '';
    }));
    if (($live['sim_based'] ?? 'false') === 'true') {
        $info['SIM_Activation_Request'] = $live['SIM_Activation_Request'];
    }
    $config['info'] = $info;

    $config['attributes'] = [
        ['name' => 'serial_num', 'value' => '1345'],
        ['name' => 'model', 'value' => 'wifi'],
    ];
    $config['node_id'] = $nodeId;
    $config['config_version'] = !empty($config['config_version']) ? $config['config_version'] : '2020-03-20';

    return $config;
}

private function applyDeviceUiNodeIdentity(array $config, $modelno, $nodeId)
{
    $modelno = trim((string) $modelno);
    $nodeId = trim((string) $nodeId);
    if ($nodeId === '') {
        $nodeId = $modelno;
    }

    $live = $this->loadDeviceUiLiveProductContext($modelno, $nodeId);

    $config['config_version'] = !empty($config['config_version']) ? $config['config_version'] : '2020-03-20';
    $config['node_id'] = $nodeId;
    $config['attributes'] = [
        ['name' => 'serial_num', 'value' => '1345'],
        ['name' => 'model', 'value' => 'wifi'],
    ];

    if (!isset($config['devices'][0]) || !is_array($config['devices'][0])) {
        $config['devices'] = [[
            'name' => $modelno,
            'total_pages' => '1',
            'attributes' => [],
            'Device Id' => $nodeId,
            'pages' => [],
            'primary' => 'power',
            'type' => 'efactor.device.',
            'brand_logo' => null,
            'brand_logo_dark' => null,
            'Need_Help' => null,
            'chat_status' => null,
            'device_image' => null,
            'device_p2p_control' => null,
            'Device Communication' => null,
            'Status' => null,
        ]];
    }

    $device = $config['devices'][0];
    if (empty($device['name'])) {
        $device['name'] = $modelno;
    }
    $device['Device Id'] = $nodeId;
    $device['type'] = !empty($device['type']) ? $device['type'] : 'efactor.device.';
    foreach (['brand_logo', 'brand_logo_dark', 'Need_Help', 'chat_status', 'device_image', 'device_p2p_control', 'Device Communication', 'Status'] as $optionalKey) {
        if (!array_key_exists($optionalKey, $device)) {
            $device[$optionalKey] = null;
        }
        if (($optionalKey === 'brand_logo' || $optionalKey === 'brand_logo_dark') && $this->deviceUiIsPlaceholderValue($device[$optionalKey] ?? '')) {
            $device[$optionalKey] = null;
        }
    }
    $pages = isset($device['pages']) && is_array($device['pages']) ? $device['pages'] : [];
    if (!array_key_exists('total_pages', $device)) {
        $device['total_pages'] = count($pages) > 0 ? count($pages) : null;
    }
    $deviceAttrs = isset($device['attributes']) && is_array($device['attributes']) ? $device['attributes'] : [];
    $deviceAttrs = array_values(array_filter($deviceAttrs, static function ($attribute) {
        if (!is_array($attribute)) {
            return false;
        }
        return strtolower(trim((string) ($attribute['name'] ?? ''))) !== 'model';
    }));
    $device['attributes'] = $this->upsertDeviceUiAttributes(
        $deviceAttrs,
        [
            ['name' => 'Device Id', 'value' => $nodeId],
        ]
    );
    $config['devices'][0] = $device;

    $info = isset($config['info']) && is_array($config['info']) ? $config['info'] : [];
    unset($info['product'], $info['brand']);
    $config['info'] = array_merge([
        'fw_version' => null,
        'model' => $modelno,
        'name' => $info['name'] ?? $modelno,
        'type' => 'IOT',
        'product_type' => 'IOT',
        'SSID' => $info['SSID'] ?? null,
        'Access_Point' => $info['Access_Point'] ?? null,
        'Guarantee_Card' => $info['Guarantee_Card'] ?? null,
        'Video' => $info['Video'] ?? null,
        'Need_Help' => $info['Need_Help'] ?? null,
        'chat_status' => $info['chat_status'] ?? null,
        'Auto_Test' => $info['Auto_Test'] ?? null,
    ], $info);
    $config['info']['model'] = $modelno;
    $config = $this->applyDeviceUiLiveProductToNodeConfig($config, $live, $modelno, $nodeId);

    return $config;
}

private function upsertDeviceUiAttributes(array $attributes, array $required)
{
    $merged = $this->mergeDeviceUiAttributeLists($attributes, []);
    foreach ($required as $attribute) {
        if (!is_array($attribute)) {
            continue;
        }
        $name = trim((string) ($attribute['name'] ?? ''));
        if ($name === '') {
            continue;
        }
        $found = false;
        foreach ($merged as $index => $existing) {
            if (!is_array($existing)) {
                continue;
            }
            if (strtolower(trim((string) ($existing['name'] ?? ''))) === strtolower($name)) {
                $merged[$index]['name'] = $name;
                $merged[$index]['value'] = $attribute['value'] ?? ($existing['value'] ?? '');
                $found = true;
                break;
            }
        }
        if (!$found) {
            $merged[] = [
                'name' => $name,
                'value' => $attribute['value'] ?? '',
            ];
        }
    }

    return array_values($merged);
}

private function normalizeDeviceUiFrame(array $frame, $parameterName = '', array $meta = [])
{
    $parameterName = trim((string) ($frame['name'] ?? $parameterName));
    if ($parameterName === '') {
        $parameterName = 'frame';
    }
    $commands = isset($frame['commands']) && is_array($frame['commands']) ? $frame['commands'] : [];
    if (empty($commands)) {
        return $this->defaultDeviceUiFrame($parameterName, $meta);
    }

    $access = strtolower(trim((string) ($meta['access'] ?? '')));
    $writableMeta = $access === '' || strpos($access, 'write') !== false;
    $slotMeta = $writableMeta ? array_merge($meta, ['ensure_terminator' => true]) : $meta;
    $commands = $this->hydrateDeviceUiFrameFields($commands, $slotMeta);
    $commands = $this->ensureDeviceUiWriteFieldSlots($commands, $slotMeta);
    $commands = $this->repairDeviceUiFrameBytePositions($commands);

    $multipleUi = isset($frame['multiple_ui_by_source']) && is_array($frame['multiple_ui_by_source'])
        ? [
            $this->deviceUiMultipleFlagEnabled($frame['multiple_ui_by_source']['command'] ?? false) ? 'true' : 'false',
            $this->deviceUiMultipleFlagEnabled($frame['multiple_ui_by_source']['response'] ?? false) ? 'true' : 'false',
            $this->deviceUiMultipleFlagEnabled($frame['multiple_ui_by_source']['error'] ?? false) ? 'true' : 'false',
        ]
        : $this->normalizeDeviceUiFlagList($frame['multiple_ui'] ?? ['false', 'false', 'false'], 3);

    return [
        'name' => $parameterName,
        'multiple_ui' => $multipleUi,
        'multiple_ui_by_source' => [
            'command' => $multipleUi[0],
            'response' => $multipleUi[1],
            'error' => $multipleUi[2],
        ],
        'trigger_ui' => $this->normalizeDeviceUiFlagList($frame['trigger_ui'] ?? ['false', 'false', 'false'], 3),
        'commands' => $this->normalizeDeviceUiFrameChoiceFields($this->stripNonPhysicalDeviceUiFrameFields($commands)),
        'response' => $this->normalizeDeviceUiFrameChoiceFields($this->hydrateDeviceUiFrameFields(isset($frame['response']) && is_array($frame['response']) ? $frame['response'] : [], $meta)),
        'error' => $this->normalizeDeviceUiFrameChoiceFields($this->hydrateDeviceUiFrameFields(isset($frame['error']) && is_array($frame['error']) ? $frame['error'] : [], $meta)),
    ];
}

/**
 * Inserts CommandId / Function Code / ActionId slots when product_api already
 * stored those values on the row but the frame list omitted the named field.
 */
private function ensureDeviceUiWriteFieldSlots(array $commands, array $meta = [])
{
    $commandId = $this->deviceUiFirstNonEmpty($meta['command_id'] ?? null, $meta['commandId'] ?? null);
    $functionCode = $this->deviceUiFirstNonEmpty($meta['function_code'] ?? null, $meta['functionCode'] ?? null);
    $actionId = $this->deviceUiFirstNonEmpty($meta['action_id'] ?? null, $meta['actionId'] ?? null);
    if ($commandId !== null && $commandId !== '') {
        $commandId = $this->deviceUiNormalizeProtocolHex($commandId, 1) ?: $commandId;
    }
    if ($functionCode !== null && $functionCode !== '') {
        $functionCode = $this->deviceUiNormalizeProtocolHex($functionCode, 1) ?: $functionCode;
    }
    if ($actionId !== null && $actionId !== '') {
        $actionId = $this->deviceUiNormalizeProtocolHex($actionId, 1) ?: $actionId;
    }

    $present = [
        'commandid' => false,
        'functioncode' => false,
        'actionid' => false,
        'terminator' => false,
    ];
    $nextPosition = 1;
    foreach ($commands as $field) {
        if (!is_array($field)) {
            continue;
        }
        $name = $this->deviceUiFrameFieldName($field);
        if (isset($present[$name])) {
            $present[$name] = true;
        }
        $start = isset($field['byte_start_position']) ? (int) $field['byte_start_position'] : 0;
        $bytes = isset($field['no_of_bytes']) ? max(1, (int) $field['no_of_bytes']) : 1;
        if ($start > 0) {
            $nextPosition = max($nextPosition, $start + $bytes);
        }
    }

    $inserts = [];
    if (!$present['commandid'] && $commandId !== null && $commandId !== '') {
        $inserts[] = [
            'default_name' => 'CommandId',
            'Field_description' => 'CommandId',
            'value' => $commandId,
            'asperui_value' => $commandId,
            'type' => 'Fixed Value',
            'data_type' => 'UnsignedInt',
            'no_of_bytes' => 1,
            'byte_start_position' => 1,
            'ui_type' => 'None',
            'local_control' => 'enable',
        ];
    }
    if (!$present['functioncode'] && $functionCode !== null && $functionCode !== '') {
        $inserts[] = [
            'default_name' => 'Function Code',
            'Field_description' => 'Function Code',
            'value' => $functionCode,
            'asperui_value' => $functionCode,
            'type' => 'Fixed Value',
            'data_type' => 'UnsignedInt',
            'no_of_bytes' => 1,
            'byte_start_position' => 2,
            'ui_type' => 'None',
            'local_control' => 'enable',
        ];
    }
    if (!$present['actionid'] && $actionId !== null && $actionId !== '') {
        $inserts[] = [
            'default_name' => 'ActionId',
            'Field_description' => 'ActionId',
            'value' => $actionId,
            'asperui_value' => $actionId,
            'type' => 'Fixed Value',
            'data_type' => 'UnsignedInt',
            'no_of_bytes' => 1,
            'byte_start_position' => 3,
            'ui_type' => 'None',
            'local_control' => 'enable',
        ];
    }

    $commands = empty($inserts) ? $commands : array_merge($inserts, $commands);

    // Rule 2: Send/write frames must close with Terminator 0A (never isolated Data).
    if (!$present['terminator'] && !empty($meta['ensure_terminator'])) {
        $commands[] = [
            'default_name' => 'Terminator',
            'Field_description' => 'Terminator',
            'value' => '0A',
            'asperui_value' => '0A',
            'type' => 'Fixed Value',
            'data_type' => 'UnsignedInt',
            'no_of_bytes' => 1,
            'byte_start_position' => $nextPosition,
            'ui_type' => 'None',
            'local_control' => 'enable',
        ];
    }

    return $commands;
}

/**
 * Gives every surviving command slot a positive byte_start_position and a
 * no_of_bytes of at least 1. Trigger may legitimately sit at position 0.
 */
private function repairDeviceUiFrameBytePositions(array $fields)
{
    $nextPosition = 1;
    $repaired = [];
    foreach ($fields as $field) {
        if (!is_array($field)) {
            continue;
        }
        $name = $this->deviceUiFrameFieldName($field);
        $bytes = isset($field['no_of_bytes']) ? (int) $field['no_of_bytes'] : 0;
        if ($bytes < 1) {
            $bytes = 1;
        }
        $start = isset($field['byte_start_position']) ? (int) $field['byte_start_position'] : 0;
        if ($start === 0 && $name === 'trigger') {
            $field['no_of_bytes'] = $bytes;
            $repaired[] = $field;
            continue;
        }
        if ($start < 1) {
            $start = $nextPosition;
        }
        $field['no_of_bytes'] = $bytes;
        $field['byte_start_position'] = $start;
        $nextPosition = max($nextPosition, $start + $bytes);
        $repaired[] = $field;
    }

    return $repaired;
}

/**
 * A field occupying byte position 0 with a length of 0 bytes is UI-only metadata,
 * not a transmittable part of the frame. Keeping it makes the frame unbuildable.
 */
private function stripNonPhysicalDeviceUiFrameFields(array $fields)
{
    $filtered = [];
    foreach ($fields as $field) {
        if (!is_array($field)) {
            continue;
        }
        $start = $field['byte_start_position'] ?? null;
        $length = $field['no_of_bytes'] ?? null;
        $isBitwise = strtolower(trim((string) ($field['field_level'] ?? $field['fieldLevel'] ?? ''))) === 'bitwise';
        if (!$isBitwise && is_numeric($start) && is_numeric($length) && (int) $start <= 0 && (int) $length <= 0) {
            continue;
        }
        $filtered[] = $field;
    }

    return array_values($filtered);
}

private function deviceUiFrameFieldName(array $field)
{
    return strtolower(preg_replace(
        '/[\s_]+/',
        '',
        trim((string) ($field['Field_description'] ?? $field['field_description'] ?? $field['default_name'] ?? ''))
    ));
}

private function deviceUiFrameValueIsEmpty($value)
{
    return $value === '' || $value === null;
}

private function deviceUiHexIsZeroPadding($value)
{
    $text = strtoupper(preg_replace('/[^0-9A-F]/', '', (string) $value));

    return $text !== '' && preg_match('/^0+$/', $text) === 1;
}

/**
 * Empty slots and zero-padding written when product_api had no hex
 * take the real protocolMeta id. A stored non-zero id is left alone.
 */
private function deviceUiProtocolSlotNeedsFill($current, $incoming)
{
    if ($this->deviceUiFrameValueIsEmpty($incoming)) {
        return false;
    }
    if ($this->deviceUiFrameValueIsEmpty($current)) {
        return true;
    }

    return $this->deviceUiHexIsZeroPadding($current) && !$this->deviceUiHexIsZeroPadding($incoming);
}

private function deviceUiTelemetryNameIsGeneric($name)
{
    $name = strtolower(trim((string) $name));

    return $name === '' || in_array($name, ['data', 'none', 'field'], true) || preg_match('/^field_\d+$/', $name) === 1;
}

private function deviceUiTelemetryLabel(array $field, $parameterName)
{
    $current = trim((string) ($field['default_name'] ?? ''));
    $description = trim((string) ($field['Field_description'] ?? $field['field_description'] ?? ''));
    if ($current !== '' && !$this->deviceUiTelemetryNameIsGeneric($current)) {
        return $current;
    }
    if ($description !== '' && !$this->deviceUiTelemetryNameIsGeneric($description)) {
        return $description;
    }
    $parameterName = trim((string) $parameterName);
    if ($parameterName !== '' && strcasecmp($parameterName, 'frame') !== 0) {
        return $parameterName;
    }

    return 'Value';
}

private function deviceUiChoiceListOrNull($value)
{
    if ($value === null) {
        return null;
    }
    if (!is_array($value)) {
        $text = trim((string) $value);
        if ($text === '' || $text === '[]' || $text === '{}') {
            return null;
        }
        $decoded = json_decode($text, true);
        $value = is_array($decoded) ? $decoded : [$text];
    }
    $clean = [];
    foreach ($value as $item) {
        if (is_array($item) || is_object($item)) {
            continue;
        }
        $text = trim((string) $item);
        if ($text === '' || $text === '[]' || $text === '{}' || strcasecmp($text, 'null') === 0) {
            continue;
        }
        $clean[] = $text;
    }

    return $clean === [] ? null : array_values($clean);
}

private function repairDeviceUiFrameDocument(array $frame, $commandId, $functionCode, $actionId, $parameterName)
{
    $meta = [
        'command_id' => $commandId,
        'function_code' => $functionCode,
        'action_id' => $actionId,
    ];
    foreach (['commands', 'response', 'error'] as $group) {
        if (!isset($frame[$group]) || !is_array($frame[$group])) {
            continue;
        }
        $fields = $this->hydrateDeviceUiFrameFields($frame[$group], $meta);
        foreach ($fields as &$field) {
            if (!is_array($field)) {
                continue;
            }
            if (in_array($group, ['response', 'error'], true)) {
                $label = $this->deviceUiTelemetryLabel($field, $parameterName);
                if ($label !== '') {
                    $field['default_name'] = $label;
                    $description = trim((string) ($field['Field_description'] ?? $field['field_description'] ?? ''));
                    if ($this->deviceUiTelemetryNameIsGeneric($description)) {
                        $field['Field_description'] = $label;
                        $field['field_description'] = $label;
                    }
                }
            }
            foreach (['asperui', 'asperui_values', 'sno_asperui'] as $listKey) {
                if (array_key_exists($listKey, $field)) {
                    $field[$listKey] = $this->deviceUiChoiceListOrNull($field[$listKey]);
                }
            }
        }
        unset($field);
        $frame[$group] = $fields;
    }

    return $frame;
}

private function applyDeviceUiPublishedFrameContract(array &$control)
{
    $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
    $parameterName = trim((string) ($control['parameterName'] ?? $control['parameter'] ?? $control['label'] ?? ''));
    $commandId = $meta['command_id'] ?? ($meta['commandId'] ?? null);
    $functionCode = $meta['function_code'] ?? ($meta['functionCode'] ?? null);
    $actionId = $meta['action_id'] ?? ($meta['actionId'] ?? null);
    if (isset($control['deviceFrame']) && is_array($control['deviceFrame'])) {
        $control['deviceFrame'] = $this->repairDeviceUiFrameDocument($control['deviceFrame'], $commandId, $functionCode, $actionId, $parameterName);
    }
    if (isset($meta['frame']) && is_array($meta['frame'])) {
        $meta['frame'] = $this->repairDeviceUiFrameDocument($meta['frame'], $commandId, $functionCode, $actionId, $parameterName);
    }
    if (isset($meta['ui_components']) && is_array($meta['ui_components'])) {
        foreach ($meta['ui_components'] as $componentIndex => $component) {
            if (!is_array($component)) {
                continue;
            }
            $componentCommand = $component['command_id'] ?? $commandId;
            $componentFunction = $component['function_code'] ?? $functionCode;
            $componentAction = $component['action_id'] ?? $actionId;
            $componentName = trim((string) ($component['parameter_name'] ?? $parameterName));
            if (isset($component['frame']) && is_array($component['frame'])) {
                $component['frame'] = $this->repairDeviceUiFrameDocument($component['frame'], $componentCommand, $componentFunction, $componentAction, $componentName);
            }
            foreach (['items', 'item_values', 'sno_asperui'] as $listKey) {
                if (array_key_exists($listKey, $component)) {
                    $component[$listKey] = $this->deviceUiChoiceListOrNull($component[$listKey]);
                }
            }
            $meta['ui_components'][$componentIndex] = $component;
        }
    }
    $control['protocolMeta'] = $meta;
}

/**
 * Widget trees are attached after the first read-only pass, so write events
 * have to be removed again. Writable controls get a schema.actions entry
 * whose id matches control.action.
 */
private function finalizeDeviceUiPublishedInteractions(array $schema)
{
    if (empty($schema['controls']) || !is_array($schema['controls'])) {
        return $schema;
    }
    foreach ($schema['controls'] as &$control) {
        if (!is_array($control)) {
            continue;
        }
        $this->applyDeviceUiPublishedFrameContract($control);
        $writable = $this->deviceUiControlIsWritable($control);
        if (!$writable) {
            $control['events'] = [];
            $control['readOnly'] = true;
            $control['writable'] = false;
            $control['interactive'] = false;
            if (isset($control['action']) && stripos((string) $control['action'], 'update_') === 0) {
                unset($control['action']);
            }
            if (isset($control['component']) && is_array($control['component'])) {
                $control['component']['events'] = [];
                if (!empty($control['component']['widgetTree']) && is_array($control['component']['widgetTree'])) {
                    $this->stripDeviceUiWriteEventsFromTree($control['component']['widgetTree']);
                }
            }
            if (!empty($control['widgetTree']) && is_array($control['widgetTree'])) {
                $this->stripDeviceUiWriteEventsFromTree($control['widgetTree']);
            }
            continue;
        }
        $id = trim((string) ($control['id'] ?? ''));
        if ($id === '') {
            continue;
        }
        $actionId = 'update_' . trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $id), '_');
        $control['action'] = $actionId;
        if (!isset($schema['actions']) || is_object($schema['actions'])) {
            $schema['actions'] = is_object($schema['actions']) ? (array) $schema['actions'] : [];
        }
        if (!is_array($schema['actions'])) {
            $schema['actions'] = [];
        }
        if (!isset($schema['actions'][$actionId]) || !is_array($schema['actions'][$actionId])) {
            $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
            $binding = trim((string) ($control['binding'] ?? ''));
            $schema['actions'][$actionId] = [
                'type' => 'dataOperation',
                'dataSource' => 'deviceCommands',
                'operation' => 'write',
                'input' => [
                    'modelNo' => '$app.modelNo',
                    'parameter' => trim((string) ($control['parameterName'] ?? $control['parameter'] ?? $control['label'] ?? '')),
                    'value' => $binding !== '' ? ('$' . $binding) : '',
                    'commandId' => $meta['command_id'] ?? ($meta['commandId'] ?? null),
                    'functionCode' => $meta['function_code'] ?? ($meta['functionCode'] ?? null),
                ],
            ];
        }
    }
    unset($control);
    if (isset($schema['actions']) && is_array($schema['actions']) && $schema['actions'] === []) {
        $schema['actions'] = new \stdClass();
    }

    return $schema;
}

private function hydrateDeviceUiFrameFields(array $fields, array $meta = [])
{
    $commandId = $meta['command_id'] ?? ($meta['commandId'] ?? null);
    $functionCode = $meta['function_code'] ?? ($meta['functionCode'] ?? null);
    $actionId = $meta['action_id'] ?? ($meta['actionId'] ?? null);

    foreach ($fields as &$field) {
        if (!is_array($field)) {
            continue;
        }
        $bytes = isset($field['no_of_bytes']) ? max(1, (int) $field['no_of_bytes']) : 1;
        $field['no_of_bytes'] = $bytes;
        $name = $this->deviceUiFrameFieldName($field);
        $slotFill = null;
        if ($name === 'commandid') {
            $slotFill = $commandId;
        } elseif ($name === 'functioncode') {
            $slotFill = $functionCode;
        } elseif ($name === 'actionid') {
            $slotFill = $actionId;
        }
        if ($slotFill !== null && !$this->deviceUiFrameValueIsEmpty($slotFill) && $this->deviceUiProtocolSlotNeedsFill($field['value'] ?? null, $slotFill)) {
            $normalizedSlot = $this->deviceUiNormalizeProtocolHex($slotFill, $bytes);
            $field['value'] = $normalizedSlot !== '' ? $normalizedSlot : $slotFill;
            if ($this->deviceUiProtocolSlotNeedsFill($field['asperui_value'] ?? null, $field['value'])) {
                $field['asperui_value'] = $field['value'];
            }
        }

        // Only promote asperui → wire when asperui is real protocol hex (never labels / live text).
        if ($this->deviceUiFrameValueIsEmpty($field['value'] ?? null)
            && !$this->deviceUiFrameValueIsEmpty($field['asperui_value'] ?? null)
            && $this->deviceUiValueLooksLikeProtocolHex($field['asperui_value'])
        ) {
            $field['value'] = $field['asperui_value'];
        }

        if (!$this->deviceUiFrameValueIsEmpty($field['value'] ?? null)) {
            if ($name === 'terminator' && !$this->deviceUiValueLooksLikeProtocolHex($field['value'])) {
                $field['value'] = '0A';
                if ($this->deviceUiFrameValueIsEmpty($field['asperui_value'] ?? null)
                    || !$this->deviceUiValueLooksLikeProtocolHex($field['asperui_value'])
                ) {
                    $field['asperui_value'] = '0A';
                }
            } elseif ($this->deviceUiValueLooksLikeProtocolHex($field['value'])) {
                $field['value'] = $this->deviceUiNormalizeProtocolHex($field['value'], $bytes);
            } elseif (!in_array($name, ['commandid', 'functioncode', 'actionid'], true)) {
                // Live/UI text must not ride on the wire; keep it in asperui_value.
                if ($this->deviceUiFrameValueIsEmpty($field['asperui_value'] ?? null)
                    || $this->deviceUiValueLooksLikeProtocolHex($field['asperui_value'])
                ) {
                    $field['asperui_value'] = $field['value'];
                }
                $field['value'] = str_repeat('00', $bytes);
            }
            continue;
        }
        $fill = null;
        if ($name === 'commandid' && !$this->deviceUiFrameValueIsEmpty($commandId)) {
            $fill = $this->deviceUiNormalizeProtocolHex($commandId, $bytes) ?: $commandId;
        } elseif ($name === 'functioncode' && !$this->deviceUiFrameValueIsEmpty($functionCode)) {
            $fill = $this->deviceUiNormalizeProtocolHex($functionCode, $bytes) ?: $functionCode;
        } elseif ($name === 'actionid' && !$this->deviceUiFrameValueIsEmpty($actionId)) {
            $fill = $this->deviceUiNormalizeProtocolHex($actionId, $bytes) ?: $actionId;
        } elseif ($name === 'terminator') {
            $fill = '0A';
        } elseif (in_array($name, ['data', 'trigger'], true)) {
            $fill = str_repeat('00', $bytes);
        }
        if ($fill === null) {
            continue;
        }
        $field['value'] = $fill;
        if ($this->deviceUiFrameValueIsEmpty($field['asperui_value'] ?? null)) {
            $field['asperui_value'] = $fill;
        }
    }
    unset($field);

    return $fields;
}

private function mergeDeviceUiFrameFieldLists(array $primary, array $secondary)
{
    $byName = [];
    foreach ($secondary as $index => $field) {
        if (!is_array($field)) {
            continue;
        }
        $name = $this->deviceUiFrameFieldName($field);
        if ($name !== '') {
            $byName[$name] = $field;
        }
        $byName['#' . $index] = $field;
    }

    foreach ($primary as $index => &$field) {
        if (!is_array($field)) {
            continue;
        }
        $name = $this->deviceUiFrameFieldName($field);
        $other = ($name !== '' && isset($byName[$name])) ? $byName[$name] : ($byName['#' . $index] ?? null);
        if (!is_array($other)) {
            continue;
        }
        if ($this->deviceUiFrameValueIsEmpty($field['value'] ?? null) && !$this->deviceUiFrameValueIsEmpty($other['value'] ?? null)) {
            $field['value'] = $other['value'];
        }
        if ($this->deviceUiFrameValueIsEmpty($field['asperui_value'] ?? null) && !$this->deviceUiFrameValueIsEmpty($other['asperui_value'] ?? null)) {
            $field['asperui_value'] = $other['asperui_value'];
        }
        foreach (['asperui', 'asperui_values', 'sno_asperui'] as $listKey) {
            if (empty($field[$listKey]) && !empty($other[$listKey]) && is_array($other[$listKey])) {
                $field[$listKey] = $other[$listKey];
            }
        }
    }
    unset($field);

    return $primary;
}

private function mergeDeviceUiFrameValues(array $primary, array $secondary)
{
    if (empty($primary)) {
        return $secondary;
    }
    if (empty($secondary)) {
        return $primary;
    }

    foreach (['commands', 'response', 'error'] as $group) {
        $primaryList = isset($primary[$group]) && is_array($primary[$group]) ? $primary[$group] : [];
        $secondaryList = isset($secondary[$group]) && is_array($secondary[$group]) ? $secondary[$group] : [];
        if (!empty($primaryList) && !empty($secondaryList)) {
            $primary[$group] = $this->mergeDeviceUiFrameFieldLists($primaryList, $secondaryList);
        } elseif (empty($primaryList) && !empty($secondaryList)) {
            $primary[$group] = $secondaryList;
        }
    }

    return $primary;
}

private function defaultDeviceUiFrame($parameterName, array $meta = [])
{
    $parameterName = trim((string) $parameterName);
    if ($parameterName === '') {
        $parameterName = 'frame';
    }
    $commandId = $meta['command_id'] ?? ($meta['commandId'] ?? '');
    $functionCode = $meta['function_code'] ?? ($meta['functionCode'] ?? '');
    $actionId = $meta['action_id'] ?? ($meta['actionId'] ?? '');
    $access = trim((string) ($meta['access'] ?? 'Read/Write'));
    if ($access === '') {
        $access = 'Read/Write';
    }
    $uiType = trim((string) ($meta['ui_type'] ?? 'Text'));
    if ($uiType === '') {
        $uiType = 'Text';
    }
    $isTrigger = (stripos($uiType, 'trigger') !== false) || strcasecmp($uiType, 'button') === 0;

    $commands = [
        [
            'default_name' => 'CommandId',
            'Field_description' => 'CommandId',
            'value' => $commandId,
            'asperui_value' => $commandId,
            'type' => 'Fixed Value',
            'data_type' => 'UnsignedInt',
            'no_of_bytes' => 1,
            'byte_start_position' => 1,
            'ui_type' => 'None',
            'local_control' => 'enable',
        ],
        [
            'default_name' => 'Function Code',
            'Field_description' => 'Function Code',
            'value' => $functionCode,
            'asperui_value' => $functionCode,
            'type' => 'Fixed Value',
            'data_type' => 'UnsignedInt',
            'no_of_bytes' => 1,
            'byte_start_position' => 2,
            'ui_type' => 'None',
            'local_control' => 'enable',
        ],
    ];
    $bytePos = 3;
    if (!$this->deviceUiFrameValueIsEmpty($actionId)) {
        $commands[] = [
            'default_name' => 'ActionId',
            'Field_description' => 'ActionId',
            'value' => $actionId,
            'asperui_value' => $actionId,
            'type' => 'Fixed Value',
            'data_type' => 'UnsignedInt',
            'no_of_bytes' => 1,
            'byte_start_position' => $bytePos,
            'ui_type' => 'None',
            'local_control' => 'enable',
        ];
        $bytePos += 1;
    }
    $dataValue = $meta['default'] ?? $parameterName;
    $commands[] = [
        'default_name' => 'Data',
        'value' => $dataValue,
        'asperui_value' => $dataValue,
        'Field_description' => $isTrigger ? 'Trigger' : 'Data',
        'type' => 'Fixed Value',
        'data_type' => 'UnsignedInt',
        'no_of_bytes' => 1,
        'byte_start_position' => $isTrigger ? 0 : $bytePos,
        'ui_type' => $uiType,
        'local_control' => 'enable',
        'ui_ids' => 0,
        'asperui' => is_array($meta['items'] ?? null) ? $meta['items'] : [],
        'sno_asperui' => is_array($meta['sno_asperui'] ?? null) ? $meta['sno_asperui'] : [],
        'asperui_values' => is_array($meta['item_values'] ?? null) ? $meta['item_values'] : [],
        'ui_description' => $access,
        'slider_step_value' => $meta['step'] ?? '',
        'display_value' => [
            'min' => isset($meta['minimum']) ? (string) $meta['minimum'] : '',
            'max' => isset($meta['maximum']) ? (string) $meta['maximum'] : '',
        ],
    ];
    if (!$isTrigger) {
        $commands[] = [
            'default_name' => 'Terminator',
            'Field_description' => 'Terminator',
            'value' => '0A',
            'asperui_value' => '0A',
            'type' => 'Fixed Value',
            'data_type' => 'UnsignedInt',
            'no_of_bytes' => 1,
            'byte_start_position' => $bytePos + 1,
            'ui_type' => 'None',
            'local_control' => 'enable',
        ];
    }

    return [
        'name' => $parameterName,
        'multiple_ui' => ['false', 'false', 'false'],
        'trigger_ui' => [$isTrigger ? 'true' : 'false', 'false', 'false'],
        'commands' => $commands,
        'response' => [[
            'default_name' => ($parameterName !== '' && strcasecmp($parameterName, 'frame') !== 0) ? $parameterName : 'Value',
            'Field_description' => ($parameterName !== '' && strcasecmp($parameterName, 'frame') !== 0) ? $parameterName : 'Value',
            'value' => '',
            'asperui_value' => '',
            'ui_type' => 'None',
            'local_control' => 'enable',
            'no_of_bytes' => 0,
            'byte_start_position' => 0,
        ]],
        'error' => [[
            'default_name' => ($parameterName !== '' && strcasecmp($parameterName, 'frame') !== 0) ? $parameterName : 'Value',
            'Field_description' => ($parameterName !== '' && strcasecmp($parameterName, 'frame') !== 0) ? $parameterName : 'Value',
            'value' => '',
            'asperui_value' => '',
            'ui_type' => 'None',
            'local_control' => 'enable',
            'no_of_bytes' => 0,
            'byte_start_position' => 0,
        ]],
    ];
}

public function getDeviceUiInfo($modelno, $nodeId = '', array $options = [])
{
    if (!$modelno) {
        return null;
    }

    $nodeId = $this->resolveDeviceUiNodeId($modelno, $nodeId);
    $skipRemote = !empty($options['skipRemote']);
    $product = null;
    try {
        $product = $this->db->table('product_api')
            ->select('brand_name, product_name, model_name, product_description, number_of_pages, node_id')
            ->where('model_name', $modelno)
            ->orderBy('selected', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();
    } catch (\Throwable $exception) {
        $product = $this->db->table('product_api')
            ->select('brand_name, product_name, model_name, product_description, number_of_pages')
            ->where('model_name', $modelno)
            ->get()
            ->getRowArray();
    }

    // Reuse caller mapping when provided (avoids a second heavy hydrate on Studio open).
    $mapping = isset($options['mapping']) && is_array($options['mapping'])
        ? $options['mapping']
        : $this->getPageParameterMapping($modelno);
    $pageNames = is_array($mapping['page_names_array'] ?? null)
        ? $mapping['page_names_array']
        : [];
    $rows = is_array($mapping['rows'] ?? null) ? $mapping['rows'] : [];

    // Studio open: skip remote test_nodes (up to ~4s). Live values still come from the app/schema API.
    if (!$skipRemote) {
        $testNodes = $this->fetchDeviceUiTestNodes($nodeId);
        if ($this->isDeviceUiTestNodesPayload($testNodes, $nodeId)) {
            $nodeDetails = $this->orderDeviceUiNodeDetails($testNodes['node_details'], $nodeId);
            if (!$nodeDetails && isset($testNodes['node_details'][0]) && is_array($testNodes['node_details'][0])) {
                $nodeDetails = $testNodes['node_details'];
            }
            $deviceConfig = isset($nodeDetails[0]['config']) && is_array($nodeDetails[0]['config'])
                ? $nodeDetails[0]['config']
                : [];
            $info = is_array($deviceConfig['info'] ?? null) ? $deviceConfig['info'] : [];
            return [
                'info' => $info,
                'deviceConfig' => $deviceConfig,
                'node_details' => $nodeDetails,
                'nodes' => $nodeId !== '' ? $nodeId : ($testNodes['nodes'] ?? $nodeId),
            ];
        }
    }
    $deviceConfig = $this->buildDeviceUiNodeConfig($modelno, $product ?: [], $pageNames, $rows, $nodeId);

    $live = $skipRemote ? [] : $this->loadDeviceUiLiveProductContext($modelno, $nodeId);
    $info = [
        'brand' => $deviceConfig['info']['brand'] ?? ($product['brand_name'] ?? null),
        'product' => $deviceConfig['info']['product'] ?? ($product['product_name'] ?? null),
        'model' => $deviceConfig['info']['model'] ?? ($product['model_name'] ?? $modelno),
        'name' => $deviceConfig['info']['name'] ?? ($product['product_name'] ?? $modelno),
        'type' => $deviceConfig['info']['type'] ?? 'IOT',
        'product_type' => $deviceConfig['info']['product_type'] ?? 'IOT',
        'description' => $product['product_description'] ?? null,
        'total_pages' => $product['number_of_pages'] ?? count($pageNames),
        'pages' => array_values($pageNames),
        'fw_version' => $deviceConfig['info']['fw_version'] ?? ($live['fw_version'] ?? null),
        'SSID' => $deviceConfig['info']['SSID'] ?? ($live['ssid'] ?? null),
        'Access_Point' => $deviceConfig['info']['Access_Point'] ?? ($live['Access_Point'] ?? null),
        'Guarantee_Card' => $deviceConfig['info']['Guarantee_Card'] ?? ($live['Guarantee_Card'] ?? null),
        'Video' => $deviceConfig['info']['Video'] ?? ($live['Video'] ?? null),
        'Need_Help' => $deviceConfig['info']['Need_Help'] ?? ($live['Need_Help'] ?? null),
        'chat_status' => $deviceConfig['info']['chat_status'] ?? ($live['chat_status'] ?? 0),
        'Auto_Test' => $deviceConfig['info']['Auto_Test'] ?? ($live['Auto_Test'] ?? null),
    ];
    if (($live['sim_based'] ?? 'false') === 'true') {
        $info['SIM_Activation_Request'] = $live['SIM_Activation_Request'];
    }

    $info = array_filter($info, static function ($value) {
        return $value !== null && $value !== '' && $value !== [];
    });

    return [
        'info' => $info,
        'deviceConfig' => $deviceConfig,
        'node_details' => [[
            'id' => $nodeId,
            'config' => $deviceConfig,
            'role' => 'primary',
            'status' => [
                'connectivity' => [
                    'connected' => !empty($live['connected']),
                    'timestamp' => (int) round(microtime(true) * 1000),
                ],
            ],
        ]],
        'nodes' => !empty($live['output_node_id']) ? $live['output_node_id'] : $nodeId,
    ];
}

public function getDeviceUiBrandLogoAsset($modelno = '', $nodeId = '', $vendorProductId = 0)
{
    return $this->resolveDeviceUiBrandLogoAsset($modelno, $nodeId, array(
        'vendorProductId' => (int) $vendorProductId,
    ));
}

private function resolveDeviceUiBrandLogoAsset($modelno = '', $nodeId = '', array $hints = [])
{
    $vendorProductId = (int) ($hints['vendorProductId'] ?? 0);
    $fromVendor = $this->resolveBrandLogoFromVendorProduct($modelno, $vendorProductId);
    if ($fromVendor) {
        return $fromVendor;
    }

    $hints = $this->collectDeviceUiBrandLogoHints($modelno, $nodeId, $hints);
    $file = $this->lookupDeviceUiBrandLogoFile($hints);
    $alt = $this->deviceUiFirstNonEmpty($hints['brandName'] ?? '', 'Brand');
    $asset = $this->buildDeviceUiBrandLogoAsset($file, $alt);
    if ($asset) {
        return $asset;
    }

    return $this->fallbackDeviceUiBrandLogoAsset($alt);
}

private function resolveBrandLogoFromVendorProduct($modelno, $vendorProductId = 0)
{
    $vendorRow = $this->loadVendorProductRowForBrand($modelno, $vendorProductId);
    if (!$vendorRow) {
        return null;
    }

    $brandName = $this->deviceUiFirstNonEmpty(
        $vendorRow['brand'] ?? '',
        $vendorRow['brand_name'] ?? ''
    );
    $userId = $this->deviceUiNonEmptyString($vendorRow['user_id'] ?? '');
    if ($brandName === '') {
        return null;
    }

    $brandRow = $this->loadBrandRowByName($brandName, $userId);
    $file = $this->deviceUiFirstNonEmpty(
        $brandRow['brandlogo'] ?? '',
        $brandRow['img_path'] ?? '',
        $brandRow['brand_logo'] ?? ''
    );
    if ($file === '') {
        return null;
    }

    return $this->buildDeviceUiBrandLogoAsset($file, $brandName);
}

private function loadVendorProductRowForBrand($modelno, $vendorProductId = 0)
{
    if (!$this->db->tableExists('vendorproducts')) {
        return [];
    }

    try {
        $vendorProductId = (int) $vendorProductId;
        if ($vendorProductId > 0) {
            $row = $this->db->table('vendorproducts')
                ->where('id', $vendorProductId)
                ->get()
                ->getRowArray();
            if (is_array($row) && $row) {
                return $row;
            }
        }

        $modelno = trim((string) $modelno);
        if ($modelno === '') {
            return [];
        }

        $builder = $this->db->table('vendorproducts');
        $builder->groupStart();
        $hasColumn = false;
        foreach (array('modelname', 'modelno') as $column) {
            if ($this->db->fieldExists($column, 'vendorproducts')) {
                $builder->orWhere($column, $modelno);
                $hasColumn = true;
            }
        }
        $builder->groupEnd();
        if (!$hasColumn) {
            return [];
        }

        $row = $builder->orderBy('id', 'DESC')->get()->getRowArray();
        return is_array($row) ? $row : [];
    } catch (\Throwable $exception) {
        return [];
    }
}

private function loadBrandRowByName($brandName, $userId = '')
{
    $brandName = trim((string) $brandName);
    if ($brandName === '' || !$this->db->tableExists('brands')) {
        return [];
    }

    $pickLogoRow = function (array $rows) {
        $fallback = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            if (!$fallback) {
                $fallback = $row;
            }
            $logo = $this->deviceUiFirstNonEmpty(
                $row['brandlogo'] ?? '',
                $row['img_path'] ?? '',
                $row['brand_logo'] ?? ''
            );
            if ($logo !== '') {
                return $row;
            }
        }
        return $fallback;
    };

    $queryByName = function ($restrictUser) use ($brandName, $userId, $pickLogoRow) {
        try {
            $builder = $this->db->table('brands')->select('*');
            $builder->groupStart();
            $builder->where('LOWER(TRIM(name))', strtolower($brandName));
            if (ctype_digit($brandName)) {
                $builder->orWhere('id', (int) $brandName);
            }
            $builder->groupEnd();
            if ($restrictUser && $userId !== '' && $this->db->fieldExists('user_id', 'brands')) {
                $builder->where('user_id', $userId);
            }
            $rows = $builder->orderBy('id', 'DESC')->get()->getResultArray();
            $match = $pickLogoRow(is_array($rows) ? $rows : []);
            if ($match) {
                return $match;
            }

            $normalized = strtolower(preg_replace('/\s+/', '', $brandName));
            $all = $this->db->table('brands')->select('*');
            if ($restrictUser && $userId !== '' && $this->db->fieldExists('user_id', 'brands')) {
                $all->where('user_id', $userId);
            }
            $loose = [];
            foreach ($all->get()->getResultArray() as $row) {
                $name = strtolower(preg_replace('/\s+/', '', (string) ($row['name'] ?? '')));
                if ($name !== '' && $name === $normalized) {
                    $loose[] = $row;
                }
            }
            return $pickLogoRow($loose);
        } catch (\Throwable $exception) {
            return [];
        }
    };

    $row = $queryByName(true);
    if ($row) {
        return $row;
    }

    return $queryByName(false);
}

private function collectDeviceUiBrandLogoHints($modelno, $nodeId, array $hints = [])
{
    $modelno = trim((string) $modelno);
    $nodeId = trim((string) $nodeId);
    $base = [
        'modelno' => $modelno,
        'nodeId' => $nodeId,
        'userId' => '',
        'vendorId' => '',
        'vendorName' => '',
        'vendorType' => '',
        'brandName' => '',
        'apiBrandLogo' => '',
        'productBrandLogo' => '',
        'deviceBrandLogo' => '',
        'vendorProductLogo' => '',
    ];
    foreach ($base as $key => $default) {
        $hints[$key] = $this->deviceUiNonEmptyString($hints[$key] ?? $default);
    }

    $hasDirectLogo = $hints['deviceBrandLogo'] !== ''
        || $hints['productBrandLogo'] !== ''
        || $hints['apiBrandLogo'] !== '';
    if ($hasDirectLogo && $hints['brandName'] !== '') {
        return $hints;
    }

    $product = [];
    if ($nodeId !== '') {
        $product = $this->loadDeviceUiEfactorProductRow($nodeId, '');
    }
    if (!$product && $modelno !== '') {
        $product = $this->loadDeviceUiEfactorProductRow('', $modelno);
    }
    $product = is_array($product) ? $product : [];
    $modelNumber = $this->deviceUiFirstNonEmpty(
        $product['model_number'] ?? '',
        $product['model_name'] ?? '',
        $modelno
    );

    $deviceRow = [];
    if ($modelNumber !== '') {
        if ($nodeId !== '' && strcasecmp($nodeId, $modelno) !== 0) {
            $deviceRow = $this->loadDeviceUiModelDeviceRow($modelNumber, $nodeId);
        }
        if (!$deviceRow) {
            $deviceRow = $this->loadDeviceUiLatestModelDeviceRow($modelNumber);
        }
    }

    $apiRow = $this->loadDeviceUiProductApiBrandRow($modelNumber !== '' ? $modelNumber : $modelno);
    $vendorRow = $this->loadVendorProductRowForBrand(
        $modelNumber !== '' ? $modelNumber : $modelno,
        (int) ($hints['vendorProductId'] ?? 0)
    );

    if ($hints['userId'] === '') {
        $hints['userId'] = $this->deviceUiFirstNonEmpty(
            $apiRow['user_id'] ?? '',
            $product['user_id'] ?? '',
            $vendorRow['user_id'] ?? ''
        );
    }
    if ($hints['vendorId'] === '') {
        $hints['vendorId'] = $this->deviceUiFirstNonEmpty(
            $product['vendor_id'] ?? '',
            $vendorRow['vendor_id'] ?? '',
            $vendorRow['brandid'] ?? '',
            $vendorRow['brand_id'] ?? ''
        );
    }
    if ($hints['vendorName'] === '') {
        $hints['vendorName'] = $this->deviceUiFirstNonEmpty(
            $product['vendor_name'] ?? '',
            $vendorRow['vendor_name'] ?? ''
        );
    }
    if ($hints['vendorType'] === '') {
        $hints['vendorType'] = $this->deviceUiNonEmptyString($product['vendor_type'] ?? '');
    }
    if ($hints['brandName'] === '') {
        $apiBrandName = $this->deviceUiNonEmptyString($apiRow['brand_name'] ?? '');
        if ($this->deviceUiIsGenericBrandName($apiBrandName)) {
            $apiBrandName = '';
        }
        $hints['brandName'] = $this->deviceUiFirstNonEmpty(
            $vendorRow['brand'] ?? '',
            $vendorRow['brand_name'] ?? '',
            $apiBrandName,
            $hints['vendorName'],
            $product['vendor_name'] ?? ''
        );
    }
    if ($hints['apiBrandLogo'] === '') {
        $hints['apiBrandLogo'] = $this->deviceUiFirstNonEmpty(
            $apiRow['brand_logo'] ?? '',
            $apiRow['brandlogo'] ?? ''
        );
    }
    if ($hints['productBrandLogo'] === '') {
        $hints['productBrandLogo'] = $this->deviceUiFirstNonEmpty(
            $product['brand_logo'] ?? '',
            $product['brandlogo'] ?? ''
        );
    }
    if ($hints['deviceBrandLogo'] === '') {
        $hints['deviceBrandLogo'] = $this->deviceUiFirstNonEmpty(
            $deviceRow['brandlogo'] ?? '',
            $deviceRow['brand_logo'] ?? '',
            $deviceRow['brandLogo'] ?? ''
        );
    }
    if ($hints['vendorProductLogo'] === '') {
        $hints['vendorProductLogo'] = $this->deviceUiFirstNonEmpty(
            $vendorRow['brandlogo'] ?? '',
            $vendorRow['brand_logo'] ?? '',
            $vendorRow['img_path'] ?? ''
        );
    }

    return $hints;
}

private function loadDeviceUiProductApiBrandRow($modelno)
{
    $modelno = trim((string) $modelno);
    if ($modelno === '' || !$this->db->tableExists('product_api')) {
        return [];
    }
    try {
        $select = ['user_id', 'brand_name'];
        foreach (['brand_logo', 'brandlogo', 'node_id'] as $column) {
            if ($this->db->fieldExists($column, 'product_api')) {
                $select[] = $column;
            }
        }
        $rows = $this->db->table('product_api')
            ->select(implode(', ', $select))
            ->where('model_name', $modelno)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
        $fallback = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            if (!$fallback) {
                $fallback = $row;
            }
            $logo = $this->deviceUiFirstNonEmpty($row['brand_logo'] ?? '', $row['brandlogo'] ?? '');
            if ($logo !== '') {
                return $row;
            }
        }
        return $fallback;
    } catch (\Throwable $exception) {
        return [];
    }
}

private function loadDeviceUiVendorProductBrandRow($modelno)
{
    $modelno = trim((string) $modelno);
    if ($modelno === '' || !$this->db->tableExists('vendorproducts')) {
        return [];
    }
    try {
        $builder = $this->db->table('vendorproducts')->select('*');
        $builder->groupStart();
        $matchedColumn = false;
        foreach (['modelname', 'model_name', 'modelno', 'model_no', 'model_number'] as $column) {
            if ($this->db->fieldExists($column, 'vendorproducts')) {
                $builder->orWhere($column, $modelno);
                $matchedColumn = true;
            }
        }
        $builder->groupEnd();
        if (!$matchedColumn) {
            return [];
        }
        $row = $builder->orderBy('id', 'DESC')->get()->getRowArray();
        return is_array($row) ? $row : [];
    } catch (\Throwable $exception) {
        return [];
    }
}

private function lookupDeviceUiBrandLogoFile(array $hints)
{
    $candidates = [
        $hints['deviceBrandLogo'] ?? '',
        $hints['productBrandLogo'] ?? '',
        $hints['apiBrandLogo'] ?? '',
        $hints['vendorProductLogo'] ?? '',
    ];

    $brandRow = $this->loadBrandRowByName(
        $this->deviceUiFirstNonEmpty($hints['brandName'] ?? '', $hints['vendorName'] ?? ''),
        $hints['userId'] ?? ''
    );
    if ($brandRow) {
        $candidates[] = $brandRow['img_path'] ?? '';
        $candidates[] = $brandRow['brandlogo'] ?? '';
        $candidates[] = $brandRow['brand_logo'] ?? '';
    }

    foreach ($candidates as $candidate) {
        $candidate = trim((string) $candidate);
        if ($candidate !== '' && !$this->deviceUiIsPlaceholderValue($candidate)) {
            return $candidate;
        }
    }

    return '';
}

private function lookupDeviceUiBrandRow(array $hints)
{
    if (!$this->db->tableExists('brands')) {
        return [];
    }

    $userId = $this->deviceUiNonEmptyString($hints['userId'] ?? '');
    $vendorId = $this->deviceUiNonEmptyString($hints['vendorId'] ?? '');
    $vendorName = $this->deviceUiNonEmptyString($hints['vendorName'] ?? '');
    $names = array_values(array_unique(array_filter([
        $this->deviceUiNonEmptyString($hints['brandName'] ?? ''),
        $vendorName,
        $this->deviceUiNonEmptyString($hints['vendorType'] ?? ''),
        $vendorName !== '' ? $this->resolveDeviceUiVendorBrandSlug($vendorName) : '',
    ], static function ($value) {
        return $value !== '';
    })));

    if ($vendorId === '' && $names === []) {
        return [];
    }

    $tryLookup = function ($restrictUser) use ($userId, $vendorId, $names) {
        try {
            $builder = $this->db->table('brands')->select('*');
            $builder->groupStart();
            $hasCondition = false;
            if ($vendorId !== '') {
                $builder->orWhere('id', $vendorId);
                $hasCondition = true;
            }
            foreach ($names as $name) {
                $builder->orWhere('name', $name);
                $hasCondition = true;
            }
            $builder->groupEnd();
            if (!$hasCondition) {
                return [];
            }
            if ($restrictUser && $userId !== '' && $this->db->fieldExists('user_id', 'brands')) {
                $builder->where('user_id', $userId);
            }
            $row = $builder->orderBy('id', 'DESC')->get()->getRowArray();
            return is_array($row) ? $row : [];
        } catch (\Throwable $exception) {
            return [];
        }
    };

    $row = $tryLookup(true);
    if ($row) {
        return $row;
    }

    return $tryLookup(false);
}

private function buildDeviceUiBrandLogoAsset($fileOrUrl, $alt = 'Brand')
{
    $fileOrUrl = trim((string) $fileOrUrl);
    if ($fileOrUrl === '' || $this->deviceUiIsPlaceholderValue($fileOrUrl)) {
        return null;
    }

    $origin = $this->deviceUiPublicOrigin();
    $source = 'files/brand_logo';
    if (preg_match('#^https?://#i', $fileOrUrl)) {
        $url = $fileOrUrl;
        $path = (string) (parse_url($url, PHP_URL_PATH) ?: '');
        $fileName = $path !== '' ? basename($path) : 'brand_logo';
        if (stripos($path, '/files/home/') !== false) {
            $source = 'files/home';
        }
    } elseif (strpos(str_replace('\\', '/', $fileOrUrl), '/') !== false) {
        $path = '/' . ltrim(str_replace('\\', '/', $fileOrUrl), '/');
        $url = $origin . $path;
        $fileName = basename($path);
        if (stripos($path, '/files/home/') !== false) {
            $source = 'files/home';
        }
    } else {
        $fileName = $fileOrUrl;
        $path = '/files/brand_logo/' . rawurlencode($fileName);
        $url = $origin . $path;
    }

    return [
        'fileName' => $fileName,
        'path' => $path !== '' ? $path : '/files/brand_logo/' . rawurlencode($fileName),
        'url' => $url,
        'previewUrl' => $url,
        'alt' => $alt !== '' ? $alt : 'Brand',
        'source' => $source,
        'usage' => 'brandMark',
    ];
}

private function fallbackDeviceUiBrandLogoAsset($alt = 'Efactor')
{
    $fileName = 'Logo_new.png';
    $dir = rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''), '/\\') . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'home';
    foreach (['Logo_new.png', 'Logo_new.jpeg', 'logo_new.png', 'logo_new.jpeg'] as $candidate) {
        if (is_file($dir . DIRECTORY_SEPARATOR . $candidate)) {
            $fileName = $candidate;
            break;
        }
    }
    $path = '/files/home/' . $fileName;
    $url = $this->deviceUiPublicOrigin() . $path;
    return [
        'fileName' => $fileName,
        'path' => $path,
        'url' => $url,
        'previewUrl' => $url,
        'alt' => $alt !== '' ? $alt : 'Efactor',
        'source' => 'files/home',
        'usage' => 'brandMark',
    ];
}

private function deviceUiPublicOrigin()
{
    if (function_exists('base_url')) {
        $origin = rtrim((string) base_url(), '/');
        if ($origin !== '') {
            return $origin;
        }
    }
    if (!empty($_SERVER['HTTP_HOST'])) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        return $scheme . '://' . $_SERVER['HTTP_HOST'];
    }

    return $this->deviceUiNodeDetailsOrigin();
}

private function deviceUiBrandLogoDarkUrl(array $brandLogo, $origin, $brandSlug)
{
    $origin = rtrim((string) $origin, '/');
    $fileName = trim((string) ($brandLogo['fileName'] ?? ''));
    if ($fileName !== '' && !$this->deviceUiIsPlaceholderValue($fileName)) {
        return $origin . '/files/brand_logo_dark/' . rawurlencode($fileName);
    }
    $brandSlug = trim((string) $brandSlug);
    if ($brandSlug !== '') {
        return $origin . '/files/brand_logo_dark/' . $brandSlug . '.jpg';
    }

    return $brandLogo['url'] ?? '';
}

private function syncDeviceUiPreviewContracts(array $schema)
{
    try {
        if (isset($schema['ui']) && is_array($schema['ui'])) {
            $this->walkDeviceUiPreviewContractNodes($schema['ui'], $this->resolveDeviceUiThemeColors($schema));
        }
        if (isset($schema['extensions']['serverDrivenUi']) && is_array($schema['extensions']['serverDrivenUi'])) {
            $this->walkDeviceUiPreviewContractNodes($schema['extensions']['serverDrivenUi'], $this->resolveDeviceUiThemeColors($schema));
        }
    } catch (\Throwable $exception) {
        log_message('error', 'Device UI preview contract sync failed: ' . $exception->getMessage());
    }
    return $schema;
}

private function resolveDeviceUiThemeColors(array $schema)
{
    $theme = [];
    if (isset($schema['theme']) && is_array($schema['theme'])) {
        $theme = $schema['theme'];
    } elseif (isset($schema['settings']['theme']) && is_array($schema['settings']['theme'])) {
        $theme = $schema['settings']['theme'];
    } elseif (isset($schema['mobileView']['theme']) && is_array($schema['mobileView']['theme'])) {
        $theme = $schema['mobileView']['theme'];
    }

    return [
        'cardColor' => trim((string) ($theme['cardColor'] ?? '')),
        'textColor' => trim((string) ($theme['textColor'] ?? '')),
        'mutedTextColor' => trim((string) ($theme['mutedTextColor'] ?? '')),
        'shellColor' => trim((string) ($theme['shellColor'] ?? '')),
    ];
}

private function deviceUiScalarText($value)
{
    return is_scalar($value) ? strtolower(trim((string) $value)) : '';
}

private function walkDeviceUiPreviewContractNodes(&$node, array $theme = [], $depth = 0)
{
    if (!is_array($node) || $depth > 60) {
        return;
    }

    $id = $this->deviceUiScalarText($node['id'] ?? '');
    $type = $this->deviceUiScalarText($node['type'] ?? null);
    if ($type === '') {
        $type = $this->deviceUiScalarText($node['flutterWidget'] ?? null);
    }
    if ($type === '') {
        $type = $this->deviceUiScalarText($node['component'] ?? null);
    }
    $componentName = $this->deviceUiScalarText($node['component'] ?? null);
    $isBrandStrip = in_array($id, ['brand_strip', 'brand-strip', 'brandstrip'], true)
        || in_array($type, ['brandbar', 'brandstrip'], true)
        || $componentName === 'brandstrip';
    $isParamList = strpos($id, 'param_list') === 0
        || $type === 'schemacontrollist'
        || $componentName === 'parameterlist';
    $isDropdown = in_array($type, ['dropdownbutton', 'dropdown'], true)
        || (isset($node['flutterWidget']) && strtolower(trim((string) $node['flutterWidget'])) === 'dropdownbutton')
        || (
            (isset($node['props']['options']) || isset($node['options']) || isset($node['properties']['props']['options']))
            && (
                (isset($node['props']['hint']) && is_string($node['props']['hint']))
                || (isset($node['properties']['props']['hint']) && is_string($node['properties']['props']['hint']))
                || (isset($node['hint']) && is_string($node['hint']))
            )
        );

    if ($isParamList) {
        if (isset($node['properties']) && is_array($node['properties']) && array_key_exists('spacing', $node['properties'])) {
            $node['properties']['spacing'] = 0;
        }
        if (isset($node['gap']) && (is_string($node['gap']) || is_numeric($node['gap']))) {
            $node['gap'] = is_string($node['gap']) ? '0px' : 0;
        }
        if (isset($node['style']['margin']) && is_array($node['style']['margin']) && array_key_exists('top', $node['style']['margin'])) {
            $node['style']['margin']['top'] = 0;
        }
    }

    if ($isBrandStrip && isset($node['style']) && is_array($node['style'])) {
        if (array_key_exists('borderRadius', $node['style'])) {
            $node['style']['borderRadius'] = is_string($node['style']['borderRadius']) ? '999px' : 999;
        }
        if (array_key_exists('backgroundColor', $node['style']) && ($theme['cardColor'] ?? '') !== '') {
            $node['style']['backgroundColor'] = $theme['cardColor'];
        }
        if (array_key_exists('borderWidth', $node['style'])) {
            $node['style']['borderWidth'] = is_string($node['style']['borderWidth']) ? '0px' : 0;
        }
        if (isset($node['style']['border']) && is_array($node['style']['border']) && array_key_exists('width', $node['style']['border'])) {
            $node['style']['border']['width'] = 0;
        }
        if (isset($node['style']['padding']) && is_array($node['style']['padding'])) {
            $paddingSync = ['top' => 10, 'right' => 16, 'bottom' => 10, 'left' => 16];
            foreach ($paddingSync as $edge => $value) {
                if (array_key_exists($edge, $node['style']['padding'])) {
                    $node['style']['padding'][$edge] = $value;
                }
            }
        } elseif (array_key_exists('padding', $node['style']) && (is_string($node['style']['padding']) || is_numeric($node['style']['padding']))) {
            $node['style']['padding'] = is_string($node['style']['padding']) ? '10px 16px' : 16;
        }
        if (isset($node['style']['margin']) && is_array($node['style']['margin'])) {
            $marginSync = ['top' => 10, 'right' => 16, 'bottom' => 12, 'left' => 16];
            foreach ($marginSync as $edge => $value) {
                if (array_key_exists($edge, $node['style']['margin'])) {
                    $node['style']['margin'][$edge] = $value;
                }
            }
        }
        if (isset($node['style']['shadow']) && is_array($node['style']['shadow'])) {
            if (array_key_exists('offsetY', $node['style']['shadow'])) {
                $node['style']['shadow']['offsetY'] = 10;
            }
            if (array_key_exists('y', $node['style']['shadow'])) {
                $node['style']['shadow']['y'] = 10;
            }
            if (array_key_exists('blurRadius', $node['style']['shadow'])) {
                $node['style']['shadow']['blurRadius'] = 28;
            }
            if (array_key_exists('blur', $node['style']['shadow'])) {
                $node['style']['shadow']['blur'] = 28;
            }
            if (array_key_exists('enabled', $node['style']['shadow'])) {
                $node['style']['shadow']['enabled'] = true;
            }
            if (array_key_exists('strength', $node['style']['shadow'])) {
                $node['style']['shadow']['strength'] = 12;
            }
        }
        if (isset($node['style']['gradient']['colors']) && is_array($node['style']['gradient']['colors']) && ($theme['cardColor'] ?? '') !== '') {
            $fill = $theme['cardColor'];
            $node['style']['gradient']['colors'] = [$fill, $fill];
        }
    }

    if ($id === 'brand_model' && ($theme['textColor'] ?? '') !== '') {
        if (isset($node['style']['color'])) {
            $node['style']['color'] = $theme['textColor'];
        }
        if (isset($node['style']['textColor'])) {
            $node['style']['textColor'] = $theme['textColor'];
        }
        if (isset($node['style']['textStyle']['color'])) {
            $node['style']['textStyle']['color'] = $theme['textColor'];
        }
    }
    if ($id === 'brand_pages' && ($theme['mutedTextColor'] ?? '') !== '') {
        if (isset($node['style']['color'])) {
            $node['style']['color'] = $theme['mutedTextColor'];
        }
        if (isset($node['style']['textColor'])) {
            $node['style']['textColor'] = $theme['mutedTextColor'];
        }
        if (isset($node['style']['textStyle']['color'])) {
            $node['style']['textStyle']['color'] = $theme['mutedTextColor'];
        }
    }

    if ($isDropdown) {
        $this->syncDeviceUiDropdownPreviewContract($node);
    }

    foreach ($node as $key => $child) {
        if (!is_array($child) || $this->deviceUiSchemaWalkSkipKey($key)) {
            continue;
        }
        $this->walkDeviceUiPreviewContractNodes($node[$key], $theme, $depth + 1);
    }
}

private function syncDeviceUiDropdownPreviewContract(array &$node)
{
    $hintTargets = [];
    if (isset($node['hint']) && is_string($node['hint'])) {
        $hintTargets[] =& $node['hint'];
    }
    if (isset($node['props']['hint']) && is_string($node['props']['hint'])) {
        $hintTargets[] =& $node['props']['hint'];
    }
    if (isset($node['properties']['props']['hint']) && is_string($node['properties']['props']['hint'])) {
        $hintTargets[] =& $node['properties']['props']['hint'];
    }
    if (isset($node['component']['props']['hint']) && is_string($node['component']['props']['hint'])) {
        $hintTargets[] =& $node['component']['props']['hint'];
    }
    foreach ($hintTargets as &$hint) {
        if (strcasecmp(trim((string) $hint), 'Select') === 0) {
            $hint = 'Choose';
        }
    }
    unset($hint);

    $options = [];
    if (isset($node['options']) && is_array($node['options'])) {
        $options = $node['options'];
    } elseif (isset($node['props']['options']) && is_array($node['props']['options'])) {
        $options = $node['props']['options'];
    } elseif (isset($node['properties']['props']['options']) && is_array($node['properties']['props']['options'])) {
        $options = $node['properties']['props']['options'];
    } elseif (isset($node['component']['options']) && is_array($node['component']['options'])) {
        $options = $node['component']['options'];
    }
    $firstOption = '';
    if (isset($options[0]) && is_array($options[0]) && array_key_exists('value', $options[0])) {
        $firstOption = trim((string) $options[0]['value']);
    } elseif (isset($options[0]) && (is_string($options[0]) || is_numeric($options[0]))) {
        $firstOption = trim((string) $options[0]);
    }
    if ($firstOption === '') {
        return;
    }

    $valueKeys = ['initialValue', 'displayValue'];
    $bags = [&$node];
    if (isset($node['props']) && is_array($node['props'])) {
        $bags[] =& $node['props'];
    }
    if (isset($node['properties']['props']) && is_array($node['properties']['props'])) {
        $bags[] =& $node['properties']['props'];
    }
    if (isset($node['component']) && is_array($node['component'])) {
        $bags[] =& $node['component'];
        if (isset($node['component']['props']) && is_array($node['component']['props'])) {
            $bags[] =& $node['component']['props'];
        }
    }
    foreach ($bags as &$bag) {
        foreach ($valueKeys as $key) {
            if (!array_key_exists($key, $bag)) {
                continue;
            }
            if (trim((string) $bag[$key]) === $firstOption) {
                $bag[$key] = '';
            }
        }
    }
    unset($bag);
}

private function stripDeviceUiSlideHeroes(array $schema)
{
    return $this->hideDeviceUiHeroAndNameCards($schema);
}

private function isDeviceUiSlideHeroNode($node)
{
    if (!is_array($node)) {
        return false;
    }
    $id = strtolower(trim((string) ($node['id'] ?? '')));
    $component = strtolower(trim((string) ($node['component'] ?? $node['type'] ?? '')));
    $className = strtolower(trim((string) ($node['className'] ?? '')));
    if (strpos($id, 'wifi') !== false) {
        return false;
    }
    if ($id === 'slidehero' || $id === 'pagehero' || $id === 'hero') {
        return true;
    }
    if (strpos($id, 'slide_hero') === 0 || strpos($id, 'hero_ring') === 0 || strpos($id, 'slide_count') === 0) {
        return true;
    }
    if (substr($id, -5) === '-hero') {
        return true;
    }
    if ($component === 'slidehero' || $component === 'herocard') {
        return true;
    }
    if (strpos($className, 'slide-hero') !== false) {
        return true;
    }
    return false;
}

private function isDeviceUiSlideHeroRef($value)
{
    $value = strtolower(trim((string) $value));
    if (strpos($value, 'wifi') !== false) {
        return false;
    }
    return $value === 'slidehero'
        || $value === 'pagehero'
        || $value === 'hero'
        || $value === 'slide_hero'
        || strpos($value, 'slide_hero_') === 0
        || strpos($value, 'hero_ring_') === 0;
}

private function stripDeviceUiSlideHeroNodes(&$node, $depth = 0)
{
    if (!is_array($node) || $depth > 60) {
        return;
    }

    if (isset($node['children']) && is_array($node['children'])) {
        $node['children'] = array_values(array_filter($node['children'], function ($child) {
            if ($this->isDeviceUiSlideHeroNode($child)) {
                return false;
            }
            if (!is_array($child) && $this->isDeviceUiSlideHeroRef($child)) {
                return false;
            }
            return true;
        }));
    }

    if (isset($node['components']) && is_array($node['components'])) {
        if (array_key_exists('pageHero', $node['components'])) {
            unset($node['components']['pageHero']);
        }
        if (array_key_exists('slideHero', $node['components'])) {
            unset($node['components']['slideHero']);
        }
        $isList = array_keys($node['components']) === range(0, count($node['components']) - 1);
        if ($isList) {
            $node['components'] = array_values(array_filter($node['components'], function ($item) {
                if ($this->isDeviceUiSlideHeroNode($item)) {
                    return false;
                }
                if (!is_array($item) && $this->isDeviceUiSlideHeroRef($item)) {
                    return false;
                }
                return true;
            }));
        }
    }

    if (isset($node['requiredBlocksInOrder']) && is_array($node['requiredBlocksInOrder'])) {
        $node['requiredBlocksInOrder'] = array_values(array_filter($node['requiredBlocksInOrder'], function ($item) {
            return !$this->isDeviceUiSlideHeroRef($item);
        }));
    }

    if (isset($node['sections']) && is_array($node['sections'])) {
        $isList = array_keys($node['sections']) === range(0, count($node['sections']) - 1);
        if ($isList && !empty($node['sections']) && !is_array($node['sections'][0] ?? null)) {
            $node['sections'] = array_values(array_filter($node['sections'], function ($item) {
                return !$this->isDeviceUiSlideHeroRef($item);
            }));
        }
    }

    if (isset($node['componentTemplates']) && is_array($node['componentTemplates']) && array_key_exists('slideHero', $node['componentTemplates'])) {
        unset($node['componentTemplates']['slideHero']);
    }

    if (isset($node['componentContract']) && is_array($node['componentContract']) && array_key_exists('SlideHero', $node['componentContract'])) {
        unset($node['componentContract']['SlideHero']);
    }

    foreach ($node as $key => $child) {
        if (!is_array($child) || $this->deviceUiSchemaWalkSkipKey($key)) {
            continue;
        }
        $this->stripDeviceUiSlideHeroNodes($node[$key], $depth + 1);
    }
}

private function stripDeviceUiNameCards(array $schema)
{
    return $this->hideDeviceUiHeroAndNameCards($schema);
}

private function isDeviceUiNameCardNode($node)
{
    if (!is_array($node)) {
        return false;
    }
    $id = strtolower(trim((string) ($node['id'] ?? '')));
    $component = strtolower(trim((string) ($node['component'] ?? $node['type'] ?? '')));
    $className = strtolower(trim((string) ($node['className'] ?? '')));
    if ($id === 'namecard' || $id === 'pagenamecard' || strpos($id, 'name_card') === 0) {
        return true;
    }
    if (substr($id, -5) === '-name' && in_array($component, ['infocard', 'namecard'], true)) {
        return true;
    }
    if ($component === 'namecard') {
        return true;
    }
    if (strpos($className, 'name-card') !== false) {
        return true;
    }
    return false;
}

private function isDeviceUiNameCardRef($value)
{
    $value = strtolower(trim((string) $value));
    return $value === 'namecard'
        || $value === 'pagenamecard'
        || $value === 'name_card'
        || strpos($value, 'name_card_') === 0;
}

private function stripDeviceUiNameCardNodes(&$node, $depth = 0)
{
    if (!is_array($node) || $depth > 60) {
        return;
    }

    if (isset($node['children']) && is_array($node['children'])) {
        $node['children'] = array_values(array_filter($node['children'], function ($child) {
            if ($this->isDeviceUiNameCardNode($child)) {
                return false;
            }
            if (!is_array($child) && $this->isDeviceUiNameCardRef($child)) {
                return false;
            }
            return true;
        }));
    }

    if (isset($node['components']) && is_array($node['components'])) {
        if (array_key_exists('pageNameCard', $node['components'])) {
            unset($node['components']['pageNameCard']);
        }
        if (array_key_exists('nameCard', $node['components'])) {
            unset($node['components']['nameCard']);
        }
        $isList = array_keys($node['components']) === range(0, count($node['components']) - 1);
        if ($isList) {
            $node['components'] = array_values(array_filter($node['components'], function ($item) {
                if ($this->isDeviceUiNameCardNode($item)) {
                    return false;
                }
                if (!is_array($item) && $this->isDeviceUiNameCardRef($item)) {
                    return false;
                }
                return true;
            }));
        }
    }

    if (isset($node['requiredBlocksInOrder']) && is_array($node['requiredBlocksInOrder'])) {
        $node['requiredBlocksInOrder'] = array_values(array_filter($node['requiredBlocksInOrder'], function ($item) {
            return !$this->isDeviceUiNameCardRef($item);
        }));
    }

    if (isset($node['sections']) && is_array($node['sections'])) {
        $isList = array_keys($node['sections']) === range(0, count($node['sections']) - 1);
        if ($isList && !empty($node['sections']) && !is_array($node['sections'][0] ?? null)) {
            $node['sections'] = array_values(array_filter($node['sections'], function ($item) {
                return !$this->isDeviceUiNameCardRef($item);
            }));
        }
    }

    foreach ($node as $key => $child) {
        if (!is_array($child) || $this->deviceUiSchemaWalkSkipKey($key)) {
            continue;
        }
        $this->stripDeviceUiNameCardNodes($node[$key], $depth + 1);
    }
}

private function schemaDeviceUiBrandLogoAsset(array $brandLogo)
{
    return [
        'fileName' => $brandLogo['fileName'] ?? 'Logo_new.png',
        'path' => $brandLogo['path'] ?? '/files/home/Logo_new.png',
        'url' => $brandLogo['url'] ?? '',
        'alt' => $brandLogo['alt'] ?? 'Brand',
        'source' => $brandLogo['source'] ?? 'files/brand_logo',
        'usage' => $brandLogo['usage'] ?? 'brandMark',
    ];
}

private function applyDeviceUiBrandLogoToSchema(array $schema, array $brandLogo)
{
    if (empty($brandLogo['url'])) {
        return $schema;
    }

    $schemaSafe = $this->schemaDeviceUiBrandLogoAsset($brandLogo);
    $schema['assets'] = isset($schema['assets']) && is_array($schema['assets']) ? $schema['assets'] : [];
    $schema['assets']['brandLogo'] = $schemaSafe;

    if (isset($schema['mobileView']['brandStrip']) && is_array($schema['mobileView']['brandStrip'])) {
        $schema['mobileView']['brandStrip']['logoUrl'] = $schemaSafe['url'];
        $schema['mobileView']['brandStrip']['logoPath'] = $schemaSafe['path'];
        $schema['mobileView']['brandStrip']['logoFileName'] = $schemaSafe['fileName'];
    }

    if (isset($schema['meta']['links']) && is_array($schema['meta']['links'])) {
        $schema['meta']['links']['brandLogo'] = $schemaSafe['url'];
    }

    foreach (['ui', 'app', 'mobileView', 'pages'] as $rootKey) {
        if (isset($schema[$rootKey]) && is_array($schema[$rootKey])) {
            $this->applyDeviceUiBrandLogoToNode($schema[$rootKey], $brandLogo);
        }
    }
    if (isset($schema['extensions']) && is_array($schema['extensions'])) {
        $this->applyDeviceUiBrandLogoToNode($schema['extensions'], $brandLogo);
    }

    return $schema;
}

private function applyDeviceUiBrandLogoToNode(&$node, array $brandLogo, $depth = 0)
{
    if (!is_array($node) || $depth > 60) {
        return;
    }

    $id = strtolower(trim((string) ($node['id'] ?? '')));
    $type = strtolower(trim((string) ($node['type'] ?? $node['component'] ?? '')));
    $isBrandMark = in_array($id, ['brand_mark', 'brand-mark', 'brandmark'], true);
    $isBrandStrip = in_array($id, ['brand_strip', 'brand-strip', 'brandstrip'], true)
        || in_array($type, ['brandbar', 'brandstrip'], true);

    if ($isBrandMark) {
        if (array_key_exists('src', $node)) {
            $node['src'] = $brandLogo['url'];
        }
        if (array_key_exists('url', $node)) {
            $node['url'] = $brandLogo['url'];
        }
        if (array_key_exists('path', $node) && isset($brandLogo['path'])) {
            $node['path'] = $brandLogo['path'];
        }
        if (array_key_exists('fileName', $node) && isset($brandLogo['fileName'])) {
            $node['fileName'] = $brandLogo['fileName'];
        }
        if (array_key_exists('semanticLabel', $node) && !empty($brandLogo['alt'])) {
            $node['semanticLabel'] = $brandLogo['alt'];
        }
    }

    if ($isBrandStrip) {
        if (isset($node['props']['logo']) && is_array($node['props']['logo'])) {
            if (array_key_exists('src', $node['props']['logo'])) {
                $node['props']['logo']['src'] = $brandLogo['url'];
            }
            if (array_key_exists('url', $node['props']['logo'])) {
                $node['props']['logo']['url'] = $brandLogo['url'];
            }
            if (array_key_exists('path', $node['props']['logo']) && isset($brandLogo['path'])) {
                $node['props']['logo']['path'] = $brandLogo['path'];
            }
            if (array_key_exists('fileName', $node['props']['logo']) && isset($brandLogo['fileName'])) {
                $node['props']['logo']['fileName'] = $brandLogo['fileName'];
            }
            if (array_key_exists('alt', $node['props']['logo']) && !empty($brandLogo['alt'])) {
                $node['props']['logo']['alt'] = $brandLogo['alt'];
            }
        }
        if (isset($node['brand']) && is_array($node['brand'])) {
            if (array_key_exists('logoUrl', $node['brand'])) {
                $node['brand']['logoUrl'] = $brandLogo['url'];
            }
            if (array_key_exists('logoPath', $node['brand']) && isset($brandLogo['path'])) {
                $node['brand']['logoPath'] = $brandLogo['path'];
            }
            if (array_key_exists('logoFileName', $node['brand']) && isset($brandLogo['fileName'])) {
                $node['brand']['logoFileName'] = $brandLogo['fileName'];
            }
            if (array_key_exists('alt', $node['brand']) && !empty($brandLogo['alt'])) {
                $node['brand']['alt'] = $brandLogo['alt'];
            }
        }
    }

    foreach ($node as $key => $child) {
        if (!is_array($child) || $this->deviceUiSchemaWalkSkipKey($key)) {
            continue;
        }
        $this->applyDeviceUiBrandLogoToNode($node[$key], $brandLogo, $depth + 1);
    }
}

public function buildDeviceUiNodeConfig($modelno, array $product = [], array $pageNames = [], array $rows = [], $nodeId = '')
{
    $modelno = trim((string) $modelno);
    $nodeId = trim((string) $nodeId);
    if ($nodeId === '' || strtolower($nodeId) === strtolower($modelno)) {
        $nodeId = $this->resolveDeviceUiNodeId($modelno, $nodeId);
    }
    $pages = [];
    $primary = null;

    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $parameterName = trim((string) ($row['parameter'] ?? ''));
        if ($parameterName === '') {
            continue;
        }
        $pageNo = trim((string) ($row['page_no'] ?? '1'));
        $pageName = trim((string) ($row['page_name'] ?? ''));
        if ($pageName === '' && isset($pageNames[$pageNo])) {
            $pageName = trim((string) $pageNames[$pageNo]);
        }
        if ($pageName === '') {
            $pageName = 'Page' . ($pageNo !== '' ? $pageNo : '1') . '.Operation';
        }

        $api = is_array($row['api'] ?? null) ? $row['api'] : [];
        $frame = is_array($api['frame'] ?? null) ? $api['frame'] : [];
        $frame = $this->normalizeDeviceUiFrame(
            $frame,
            $parameterName !== '' ? $parameterName : ($frame['name'] ?? ''),
            $api
        );
        if ($primary === null && strtolower($parameterName) === 'power') {
            $primary = $frame['name'] ?: $parameterName;
        }
        if (!isset($pages[$pageName])) {
            $pages[$pageName] = [];
        }
        $pages[$pageName][] = ['frame' => $frame];
    }

    if ($primary === null) {
        foreach ($pages as $pageFrames) {
            if (!empty($pageFrames[0]['frame']['name'])) {
                $primary = $pageFrames[0]['frame']['name'];
                break;
            }
        }
    }

    $productName = trim((string) ($product['product_name'] ?? ''));
    $brandName = trim((string) ($product['brand_name'] ?? ''));
    $totalPages = count($pages);
    if ($totalPages <= 0 && !empty($pageNames)) {
        $totalPages = count($pageNames);
    }

    return $this->applyDeviceUiNodeIdentity([
        'attributes' => [
            ['name' => 'model', 'value' => $modelno],
        ],
        'config_version' => '2020-03-20',
        'devices' => [[
            'name' => $modelno,
            'total_pages' => (string) max(1, $totalPages),
            'attributes' => [
                ['name' => 'Device Id', 'value' => $nodeId],
                ['name' => 'model', 'value' => $modelno],
            ],
            'Device Id' => $nodeId,
            'pages' => $pages,
            'primary' => $primary ?: 'power',
            'type' => 'efactor.device.',
            'brand_logo' => null,
            'brand_logo_dark' => null,
            'Need_Help' => null,
            'chat_status' => null,
            'device_image' => null,
            'device_p2p_control' => null,
            'Device Communication' => null,
            'Status' => null,
        ]],
        'info' => [
            'fw_version' => null,
            'model' => $product['model_name'] ?? $modelno,
            'name' => $productName !== '' ? $productName : $modelno,
            'type' => 'IOT',
            'product_type' => 'IOT',
            'product' => $productName !== '' ? $productName : $modelno,
            'brand' => $brandName !== '' ? $brandName : null,
            'SSID' => null,
            'Access_Point' => null,
        ],
        'node_id' => $nodeId,
    ], $modelno, $nodeId);
}

private function ensureDeviceUiJsonColumn($table, $column, $notNull = false)
{
    try {
        $nullSql = $notNull ? 'NOT NULL' : 'NULL';
        if (!$this->db->fieldExists($column, $table)) {
            $this->db->query("ALTER TABLE `{$table}` ADD `{$column}` LONGTEXT {$nullSql}");
            return;
        }

        $fields = $this->db->getFieldData($table);
        foreach ($fields as $field) {
            if ($field->name !== $column) {
                continue;
            }
            $type = strtolower((string) ($field->type ?? ''));
            if ($type !== 'longtext' && $type !== 'json') {
                $this->db->query("ALTER TABLE `{$table}` MODIFY `{$column}` LONGTEXT {$nullSql}");
            }
            break;
        }
    } catch (\Throwable $exception) {
        log_message('error', 'Device UI column ensure failed for ' . $table . '.' . $column . ': ' . $exception->getMessage());
    }
}

private function ensureDeviceUiThemesTable()
{
    $this->db->query("
        CREATE TABLE IF NOT EXISTS `device_ui_themes` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `model_name` VARCHAR(191) NOT NULL,
            `theme_json` LONGTEXT NOT NULL,
            `schema_json` LONGTEXT NULL,
            `created_at` DATETIME NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `model_name` (`model_name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    $this->ensureDeviceUiJsonColumn('device_ui_themes', 'theme_json', true);
    $this->ensureDeviceUiJsonColumn('device_ui_themes', 'schema_json', false);
}

private function ensureDeviceUiSchemasTable()
{
    $this->db->query("
        CREATE TABLE IF NOT EXISTS `device_ui_schemas` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `model_name` VARCHAR(191) NOT NULL,
            `node_id` VARCHAR(191) NULL,
            `app_id` VARCHAR(191) NULL,
            `version` VARCHAR(32) NOT NULL DEFAULT '1.0.0',
            `schema_json` LONGTEXT NOT NULL,
            `created_at` DATETIME NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `model_name` (`model_name`),
            KEY `node_id` (`node_id`),
            KEY `app_id` (`app_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    if (!$this->db->fieldExists('node_id', 'device_ui_schemas')) {
        $this->db->query("ALTER TABLE `device_ui_schemas` ADD `node_id` VARCHAR(191) NULL AFTER `model_name`");
        $this->db->query("ALTER TABLE `device_ui_schemas` ADD KEY `node_id` (`node_id`)");
    }
    $this->ensureDeviceUiJsonColumn('device_ui_schemas', 'schema_json', true);
}

private function ensureDeviceUiScanResultsTable()
{
    $this->db->query("
        CREATE TABLE IF NOT EXISTS `device_ui_scan_results` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `model_name` VARCHAR(191) NOT NULL,
            `node_id` VARCHAR(191) NOT NULL,
            `scan_value` TEXT NOT NULL,
            `scan_format` VARCHAR(64) NULL,
            `scanned_at` VARCHAR(64) NULL,
            `source` VARCHAR(64) NULL,
            `payload_json` LONGTEXT NULL,
            `created_at` DATETIME NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `model_node` (`model_name`, `node_id`),
            KEY `model_name` (`model_name`),
            KEY `node_id` (`node_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
}

public function extract_apis($user_id, $modelno)
{
    $sql = "SELECT parameter_name FROM product_api WHERE model_name = ?";
    $query = $this->db->query($sql, [$modelno]);

    $results = $query->getResultArray();
    $paramNames = [];

    foreach ($results as $row) {
        $paramNames[] = $row['parameter_name'];
    }

    return $paramNames;
}

public function savePageParameterJsonMapping($modelno, $pageNameMap, $linkingData, $uiRows = [], $replace = false)
{
    $db = \Config\Database::connect();
    $builder = $db->table('api_page_linking_data');

    $existing = $builder->where('model_name', $modelno)
        ->orderBy('created_at', 'DESC')
        ->get()
        ->getRowArray();

    if ($existing && !$replace) {
        $existingPageNames = json_decode($existing['page_names'] ?? '[]', true);
        $existingLinking = json_decode($existing['linking_data'] ?? '[]', true);

        if (!is_array($existingPageNames)) {
            $existingPageNames = [];
        }
        if (!is_array($existingLinking)) {
            $existingLinking = [];
        }

        unset($existingLinking['__ui_rows__']);

        $pageNameMap = array_merge($existingPageNames, $pageNameMap);
        $linkingData = $this->mergePageLinkingData($existingLinking, $linkingData);
    }

    $previousPageNames = [];
    $removedPages = [];
    if ($existing) {
        $previousPageMap = json_decode($existing['page_names'] ?? '[]', true);
        $previousLinking = json_decode($existing['linking_data'] ?? '[]', true);
        if (!is_array($previousPageMap)) {
            $previousPageMap = [];
        }
        if (!is_array($previousLinking)) {
            $previousLinking = [];
        }

        foreach ($previousPageMap as $previousName) {
            $previousName = trim((string) $previousName);
            if ($previousName !== '') {
                $previousPageNames[strtolower($previousName)] = $previousName;
            }
        }
        if (!empty($previousLinking['__ui_rows__']) && is_array($previousLinking['__ui_rows__'])) {
            foreach ($previousLinking['__ui_rows__'] as $previousRow) {
                if (!is_array($previousRow)) {
                    continue;
                }
                $previousName = trim((string) ($previousRow['page_name'] ?? ''));
                if ($previousName !== '') {
                    $previousPageNames[strtolower($previousName)] = $previousName;
                }
            }
        }
        if (!empty($previousLinking['__removed_pages__']) && is_array($previousLinking['__removed_pages__'])) {
            foreach ($previousLinking['__removed_pages__'] as $removedName) {
                $removedName = trim((string) $removedName);
                if ($removedName !== '') {
                    $removedPages[strtolower($removedName)] = $removedName;
                }
            }
        }
    }

    $savedPageNames = [];
    foreach ((array) $pageNameMap as $savedName) {
        $savedName = trim((string) $savedName);
        if ($savedName !== '') {
            $savedPageNames[strtolower($savedName)] = $savedName;
        }
    }
    foreach ((array) $uiRows as $savedRow) {
        if (!is_array($savedRow)) {
            continue;
        }
        $savedName = trim((string) ($savedRow['page_name'] ?? ''));
        if ($savedName !== '') {
            $savedPageNames[strtolower($savedName)] = $savedName;
        }
    }

    foreach ($previousPageNames as $removedKey => $removedName) {
        if (!isset($savedPageNames[$removedKey]) && !$this->isDefaultStatusOperationPage($removedName)) {
            $removedPages[$removedKey] = $removedName;
        }
    }
    foreach ($savedPageNames as $savedKey => $savedName) {
        unset($removedPages[$savedKey]);
    }

    unset($linkingData['__ui_rows__'], $linkingData['__removed_pages__']);
    if (!empty($uiRows)) {
        $linkingData['__ui_rows__'] = array_values($uiRows);
    }
    if (!empty($removedPages)) {
        $linkingData['__removed_pages__'] = array_values($removedPages);
    }

    if (!empty($removedPages) && $this->db->tableExists('product_api')) {
        $legacyApiRows = $this->db->table('product_api')
            ->select('id, page_names')
            ->where('model_name', $modelno)
            ->where('page_names !=', '')
            ->get()
            ->getResultArray();

        foreach ($legacyApiRows as $legacyApiRow) {
            $rawNames = trim((string) ($legacyApiRow['page_names'] ?? ''));
            $parts = array_values(array_filter(array_map('trim', explode(',', $rawNames)), static function ($part) {
                return $part !== '';
            }));
            $kept = [];
            $changed = false;
            foreach ($parts as $part) {
                if (isset($removedPages[strtolower($part)])) {
                    $changed = true;
                    continue;
                }
                $kept[] = $part;
            }
            if (!$changed) {
                continue;
            }
            $this->db->table('product_api')
                ->where('id', (int) ($legacyApiRow['id'] ?? 0))
                ->update(['page_names' => implode(',', $kept)]);
        }
    }

    $data = [
        'model_name'   => $modelno,
        'page_names'   => json_encode($pageNameMap, JSON_UNESCAPED_UNICODE),
        'linking_data' => json_encode($linkingData, JSON_UNESCAPED_UNICODE),
        'created_at'   => date('Y-m-d H:i:s')
    ];

    // Keep exactly one canonical mapping row per model to avoid stale snapshots.
    $db->table('api_page_linking_data')->where('model_name', $modelno)->delete();
    $db->table('api_page_linking_data')->insert($data);

    unset($this->pageParameterMappingCache[$modelno], $this->pageParameterMappingBuilding[$modelno]);
    $this->linkingPageOptionsCache = [];
    $this->productApiPageParameterRowsCache = [];

$linking_data_json = $data['linking_data'];
$linking_data = json_decode($linking_data_json, true);




foreach ($linking_data as $pageKey => $params) {
    if ($pageKey === '__ui_rows__' || !is_array($params)) {
        continue;
    }
    foreach ($params as $param) {
        $paramName = is_array($param) ? ($param['parameter'] ?? '') : $param;

        if ($paramName === '') {
            continue;
        }

        $pageForApi = '';
        if (is_array($param) && !empty($param['page_name'])) {
            $pageForApi = trim((string) $param['page_name']);
        }
        if ($pageForApi === '') {
            $pageForApi = isset($pageNameMap[$pageKey]) ? trim((string) $pageNameMap[$pageKey]) : trim((string) $pageKey);
        }
        if ($pageForApi === 'Page 1') {
            $pageForApi = 'Page1.Operation';
        }

        $this->db->table('product_api')
            ->where('parameter_name', $paramName)
            ->where('model_name', $modelno)
            ->update(['page_names' => $pageForApi]);
    }

}




    try {
        // Create/refresh stored schema from current pages+APIs without opening Design Studio.
        $this->autoPersistDeviceUiSchema($modelno, '');
        $this->syncDeviceUiThemeControlOrderFromMapping($modelno);
    } catch (\Throwable $exception) {
        log_message('error', 'Auto device-ui schema sync after page linking failed: ' . $exception->getMessage());
    }

return true;
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

    $this->applyApiViewScope($apiBuilder, 'item_id', 'model_name', 'user_id', $itemId, $userId, $modelno);

    foreach ($apiBuilder->orderBy('id', 'DESC')->get()->getResultArray() as $row) {
        $addParam($row['parameter_name'] ?? '');
    }

    $legacyBuilder = $this->db->table('product_api')
        ->select('parameter_name')
        ->where('parameter_name !=', '');

    $this->applyApiViewScope($legacyBuilder, 'itemid', 'model_name', 'user_id', $itemId, $userId, $modelno);

    foreach ($legacyBuilder->orderBy('id', 'DESC')->get()->getResultArray() as $row) {
        $addParam($row['parameter_name'] ?? '');
    }

    $modelNames = method_exists($this, 'getModelNamesForItem')
        ? $this->getModelNamesForItem($itemId, $modelno)
        : array();
    $modelno = trim((string) $modelno);
    if ($modelno !== '' && !in_array($modelno, $modelNames, true)) {
        $modelNames[] = $modelno;
    }

    if (!empty($modelNames)) {
        foreach ($this->db->table('product_api')
            ->select('parameter_name')
            ->whereIn('model_name', $modelNames)
            ->where('parameter_name !=', '')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray() as $row) {
            $addParam($row['parameter_name'] ?? '');
        }

        foreach ($this->db->table('api_product_configs')
            ->select('parameter_name')
            ->whereIn('model_name', $modelNames)
            ->where('parameter_name !=', '')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray() as $row) {
            $addParam($row['parameter_name'] ?? '');
        }
    }

    return $params;
}

private function mergePageLinkingData(array $existingLinking, array $newLinking)
{
    $merged = $existingLinking;

    foreach ($newLinking as $pageKey => $entries) {
        if (!is_array($entries)) {
            continue;
        }

        if (!isset($merged[$pageKey]) || !is_array($merged[$pageKey])) {
            $merged[$pageKey] = $entries;
            continue;
        }

        $existingParams = [];
        foreach ($merged[$pageKey] as $entry) {
            $param = is_array($entry) ? trim((string) ($entry['parameter'] ?? '')) : trim((string) $entry);
            if ($param !== '') {
                $existingParams[strtolower($param)] = true;
            }
        }

        foreach ($entries as $entry) {
            $param = is_array($entry) ? trim((string) ($entry['parameter'] ?? '')) : trim((string) $entry);
            if ($param === '' || isset($existingParams[strtolower($param)])) {
                continue;
            }
            $merged[$pageKey][] = $entry;
            $existingParams[strtolower($param)] = true;
        }
    }

    return $merged;
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
        ->select('modelno, modelname')
        ->where('id', $itemId)
        ->get()
        ->getRowArray();

    if (!empty($vendor)) {
        $vendorModel = trim((string) ($vendor['modelno'] ?? '')) ?: trim((string) ($vendor['modelname'] ?? ''));

        if ($vendorModel !== '') {
            return $vendorModel;
        }
    }

    $item = $this->db->table('efactorproduct')
        ->select('model_number, model_name')
        ->where('id', $itemId)
        ->get()
        ->getRowArray();

    return trim((string) ($item['model_number'] ?? '')) ?: trim((string) ($item['model_name'] ?? ''));
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

/**
 * One model number for Actions, Pages, Linking, and View UI.
 * Prefer an existing api_page_linking_data row so saved APIs load from every entry point.
 */
public function resolveLinkingModelNo($itemId, $requested = '')
{
    $requested = trim((string) $requested);
    $itemId = (int) $itemId;
    $aliases = $this->getModelNamesForItem($itemId, $requested);

    if ($requested !== '' && !in_array($requested, $aliases, true)) {
        $aliases[] = $requested;
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

    if ($requested !== '') {
        return $requested;
    }

    return $this->getModelNameForItem($itemId);
}

/**
 * Restrict API rows to this vendor product and the logged-in profile.
 * Do not use item_id OR model_name across all users â€” that leaked other models/profiles.
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

// here studio funcitsn ends

public function saveDeviceUiScanResult($modelno, $nodeId, array $scan)
{
    $modelno = trim((string) $modelno);
    $nodeId = trim((string) $nodeId);
    if ($modelno === '') {
        return false;
    }
    $nodeId = $this->resolveDeviceUiNodeId($modelno, $nodeId);

    $this->ensureDeviceUiScanResultsTable();

    $value = isset($scan['value']) ? trim((string) $scan['value']) : '';
    if ($value === '') {
        return false;
    }

    $now = date('Y-m-d H:i:s');
    $data = [
        'model_name' => $modelno,
        'node_id' => $nodeId,
        'scan_value' => $value,
        'scan_format' => isset($scan['format']) ? (string) $scan['format'] : (isset($scan['type']) ? (string) $scan['type'] : 'qr'),
        'scanned_at' => isset($scan['scannedAt']) ? (string) $scan['scannedAt'] : (isset($scan['scanned_at']) ? (string) $scan['scanned_at'] : $now),
        'source' => isset($scan['source']) ? (string) $scan['source'] : 'app',
        'payload_json' => json_encode($scan, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'updated_at' => $now,
    ];

    $existing = $this->db->table('device_ui_scan_results')
        ->where('model_name', $modelno)
        ->where('node_id', $nodeId)
        ->get()
        ->getRowArray();

    if ($existing) {
        unset($data['model_name'], $data['node_id']);
        return $this->db->table('device_ui_scan_results')
            ->where('model_name', $modelno)
            ->where('node_id', $nodeId)
            ->update($data);
    }

    $data['created_at'] = $now;
    return $this->db->table('device_ui_scan_results')->insert($data);
}

public function getDeviceUiScanResult($modelno, $nodeId = '')
{
    $modelno = trim((string) $modelno);
    $nodeId = trim((string) $nodeId);
    if ($modelno === '') {
        return null;
    }
    if ($nodeId === '') {
        $nodeId = $modelno;
    }

    $this->ensureDeviceUiScanResultsTable();

    $record = $this->db->table('device_ui_scan_results')
        ->where('model_name', $modelno)
        ->where('node_id', $nodeId)
        ->get()
        ->getRowArray();

    if (!$record) {
        return null;
    }

    $payload = [];
    if (!empty($record['payload_json'])) {
        $decoded = json_decode($record['payload_json'], true);
        if (is_array($decoded)) {
            $payload = $decoded;
        }
    }

    return [
        'value' => (string) ($record['scan_value'] ?? ($payload['value'] ?? '')),
        'format' => (string) ($record['scan_format'] ?? ($payload['format'] ?? 'qr')),
        'type' => (string) ($payload['type'] ?? ($record['scan_format'] ?? 'qr')),
        'scannedAt' => (string) ($record['scanned_at'] ?? ($payload['scannedAt'] ?? '')),
        'source' => (string) ($record['source'] ?? ($payload['source'] ?? 'app')),
        'nodeId' => (string) ($record['node_id'] ?? $nodeId),
    ];
}
}
