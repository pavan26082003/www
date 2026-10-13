<?php
$modelno = $modelno ?? '';
$modal_id = $modal_id ?? 0;
$params = is_array($params ?? null) ? array_values(array_unique(array_filter($params))) : [];
$pages = is_array($pages ?? null) ? $pages : [];
$savedRows = !empty($saved_mapping['rows']) && is_array($saved_mapping['rows']) ? $saved_mapping['rows'] : [];
$savedUiRows = !empty($saved_mapping['ui_rows']) && is_array($saved_mapping['ui_rows']) ? $saved_mapping['ui_rows'] : [];
$displayRows = [];

$pageOptions = [];
$pageOptionsByName = [];

$addPageOption = function ($pageNo, $pageName) use (&$pageOptionsByName) {
    $pageNo = trim((string) $pageNo);
    $pageName = trim((string) $pageName);

    if ($pageNo === '' || $pageName === '') {
        return;
    }

    $key = strtolower($pageName);
    if (!isset($pageOptionsByName[$key])) {
        $pageOptionsByName[$key] = [
            'page_no' => $pageNo,
            'page_name' => $pageName,
        ];
    }
};

foreach ($pages as $index => $page) {
    $name = trim((string) ($page['page_names'] ?? ''));
    if ($name === '') {
        continue;
    }

    $sequenceNo = trim((string) ($page['sequence_no'] ?? ''));
    $pageNo = ($sequenceNo !== '' && ctype_digit($sequenceNo) && (int) $sequenceNo > 0)
        ? (string) ((int) $sequenceNo)
        : (string) ($index + 1);
    $addPageOption($pageNo, $name);
}

$pageOptions = array_values($pageOptionsByName);
usort($pageOptions, function ($a, $b) {
    return ((int) ($a['page_no'] ?? 0)) <=> ((int) ($b['page_no'] ?? 0));
});

$allowedPageNos = [];
$allowedPageNames = [];
foreach ($pageOptions as $pageOption) {
    $allowedPageNos[(string) ($pageOption['page_no'] ?? '')] = true;
    $allowedPageNames[strtolower((string) ($pageOption['page_name'] ?? ''))] = true;
}

if (empty($savedUiRows) && empty($savedRows) && !empty($pageOptions)) {
    $savedRows[] = [
        'page_no' => $pageOptions[0]['page_no'] ?? '1',
        'page_name' => $pageOptions[0]['page_name'] ?? 'Page 1',
        'parameter' => '',
        'sequence' => 1,
    ];
}

if (!empty($savedUiRows)) {
    foreach ($savedUiRows as $savedUiRow) {
        $pageNo = trim((string) ($savedUiRow['page_no'] ?? ''));
        $pageName = trim((string) ($savedUiRow['page_name'] ?? ''));
        $sequence = (int) ($savedUiRow['sequence'] ?? 1);
        $parameters = [];

        if (!empty($savedUiRow['parameters']) && is_array($savedUiRow['parameters'])) {
            foreach ($savedUiRow['parameters'] as $parameter) {
                $parameter = trim((string) $parameter);
                if ($parameter !== '' && !in_array($parameter, $parameters, true)) {
                    $parameters[] = $parameter;
                }
            }
        } else {
            $parameter = trim((string) ($savedUiRow['parameter'] ?? ''));
            if ($parameter !== '') {
                $parameters[] = $parameter;
            }
        }

        if ($pageName === '' || empty($parameters)) {
            continue;
        }

        $pageKey = strtolower($pageName);
        if (isset($pageOptionsByName[$pageKey])) {
            $pageNo = $pageOptionsByName[$pageKey]['page_no'];
            $pageName = $pageOptionsByName[$pageKey]['page_name'];
        } elseif ($pageNo === '' || !isset($allowedPageNos[$pageNo])) {
            continue;
        }

        $displayRows[] = [
            'page_no' => $pageNo,
            'page_name' => $pageName,
            'parameters' => $parameters,
            'sequence' => max(1, $sequence),
        ];
    }
} else {
    foreach ($savedRows as $savedRow) {
        $pageNo = trim((string) ($savedRow['page_no'] ?? ''));
        $pageName = trim((string) ($savedRow['page_name'] ?? ''));
        $parameter = trim((string) ($savedRow['parameter'] ?? ''));
        $sequence = (int) ($savedRow['sequence'] ?? 1);

        if ($pageName === '' || $parameter === '') {
            continue;
        }

        $pageKey = strtolower($pageName);
        if (isset($pageOptionsByName[$pageKey])) {
            $pageNo = $pageOptionsByName[$pageKey]['page_no'];
            $pageName = $pageOptionsByName[$pageKey]['page_name'];
        } elseif ($pageNo === '' || !isset($allowedPageNos[$pageNo])) {
            continue;
        }

        $displayRows[] = [
            'page_no' => $pageNo,
            'page_name' => $pageName,
            'parameters' => $parameter !== '' ? [$parameter] : [],
            'sequence' => max(1, $sequence),
        ];
    }
}

usort($displayRows, function ($a, $b) {
    $pageCompare = ((int) ($a['page_no'] ?? 0)) <=> ((int) ($b['page_no'] ?? 0));
    if ($pageCompare !== 0) {
        return $pageCompare;
    }

    return ((int) ($a['sequence'] ?? 0)) <=> ((int) ($b['sequence'] ?? 0));
});

$groupedDisplayRows = [];
foreach ($displayRows as $displayRow) {
    $pageNo = trim((string) ($displayRow['page_no'] ?? ''));
    if ($pageNo === '') {
        continue;
    }

    if (!isset($groupedDisplayRows[$pageNo])) {
        $groupedDisplayRows[$pageNo] = [
            'page_no' => $pageNo,
            'page_name' => trim((string) ($displayRow['page_name'] ?? '')),
            'parameters' => [],
            'sequence' => max(1, (int) ($displayRow['sequence'] ?? 1)),
        ];
    } else {
        $groupedDisplayRows[$pageNo]['sequence'] = min(
            (int) $groupedDisplayRows[$pageNo]['sequence'],
            max(1, (int) ($displayRow['sequence'] ?? 1))
        );
    }

    foreach (($displayRow['parameters'] ?? []) as $parameter) {
        $parameter = trim((string) $parameter);
        if ($parameter !== '' && !in_array($parameter, $groupedDisplayRows[$pageNo]['parameters'], true)) {
            $groupedDisplayRows[$pageNo]['parameters'][] = $parameter;
        }
    }
}

    $displayRows = array_values($groupedDisplayRows);

    usort($displayRows, function ($a, $b) {
        return ((int) ($a['sequence'] ?? 0)) <=> ((int) ($b['sequence'] ?? 0));
    });

    $claimedParameters = [];
    $claimRowParameters = function (&$row) use (&$claimedParameters) {
        $kept = [];
        foreach (($row['parameters'] ?? []) as $parameter) {
            $parameter = trim((string) $parameter);
            if ($parameter === '') {
                continue;
            }
            $key = strtolower($parameter);
            if (isset($claimedParameters[$key])) {
                continue;
            }
            $claimedParameters[$key] = true;
            $kept[] = $parameter;
        }
        $row['parameters'] = $kept;
    };

    foreach ($displayRows as &$displayRow) {
        $pageName = strtolower(preg_replace('/\s+/', '', (string) ($displayRow['page_name'] ?? '')));
        if ($pageName === 'status.operation' || $pageName === 'status') {
            $claimRowParameters($displayRow);
        }
    }
    unset($displayRow);

    foreach ($displayRows as &$displayRow) {
        $pageName = strtolower(preg_replace('/\s+/', '', (string) ($displayRow['page_name'] ?? '')));
        if ($pageName === 'status.operation' || $pageName === 'status') {
            continue;
        }
        $claimRowParameters($displayRow);
    }
    unset($displayRow);

$viewApisUrl = get_uri('items/viewapis/' . (int) $modal_id . '?modelno=' . rawurlencode((string) $modelno));
$pagesUrl = get_uri('items/modal_pages/' . (int) $modal_id . '?modelno=' . rawurlencode((string) $modelno));

if (!function_exists('linking_icon')) {
    function linking_icon($name)
    {
        $icons = [
            'arrow-left' => '<path d="M19 12H5"></path><path d="M12 19l-7-7 7-7"></path>',
            'copy' => '<rect x="9" y="9" width="13" height="13" rx="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>',
            'check' => '<path d="M20 6 9 17l-5-5"></path>',
            'grip' => '<path d="M9 5h.01"></path><path d="M15 5h.01"></path><path d="M9 12h.01"></path><path d="M15 12h.01"></path><path d="M9 19h.01"></path><path d="M15 19h.01"></path>',
            'eye' => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path><circle cx="12" cy="12" r="3"></circle>',
            'file' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path>',
            'hash' => '<path d="M4 9h16"></path><path d="M4 15h16"></path><path d="M10 3 8 21"></path><path d="M16 3l-2 18"></path>',
            'layers' => '<path d="m12 2 10 5-10 5L2 7l10-5z"></path><path d="m2 17 10 5 10-5"></path><path d="m2 12 10 5 10-5"></path>',
            'list' => '<path d="M8 6h13"></path><path d="M8 12h13"></path><path d="M8 18h13"></path><path d="M3 6h.01"></path><path d="M3 12h.01"></path><path d="M3 18h.01"></path>',
            'move-vertical' => '<path d="M12 3v18"></path><path d="m8 7 4-4 4 4"></path><path d="m8 17 4 4 4-4"></path>',
            'plus' => '<path d="M12 5v14"></path><path d="M5 12h14"></path>',
            'plus-circle' => '<circle cx="12" cy="12" r="10"></circle><path d="M12 8v8"></path><path d="M8 12h8"></path>',
            'trash' => '<path d="M3 6h18"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path>',
            'x' => '<path d="M18 6 6 18"></path><path d="m6 6 12 12"></path>',
        ];

        $svg = $icons[$name] ?? $icons['list'];
        return '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $svg . '</svg>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Modal API Page Linking</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <style>
    :root {
      --api-bg: #FFFFFF;
      --api-card: #FFFFFF;
      --api-graphite: #1F2937;
      --api-graphite-2: #374151;
      --api-sage: #0F766E;
      --api-sage-dark: #115E59;
      --api-copper: #F59E0B;
      --api-border: #DDE5F0;
      --api-muted: #6B7280;
      --api-danger: #DC2626;
      --api-cream: #FFFBF4;
      --api-ink-soft: #F8FAFC;
    }
    * { box-sizing: border-box; }

    body {
      margin: 0;
      background: var(--api-bg);
      color: var(--api-graphite);
      font-family: "Poppins", "Segoe UI", Arial, sans-serif;
      padding: clamp(12px, 2vw, 28px);
    }

    .linking-shell {
      width: min(100%, 1220px);
      margin: 0 auto;
    }

    .linking-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
      padding: clamp(18px, 2.4vw, 28px);
      background: linear-gradient(135deg, #111827, var(--api-graphite-2));
      color: #fff;
      border-radius: 18px 18px 0 0;
      box-shadow: 0 18px 40px rgba(35, 41, 70, .18);
    }

    .linking-kicker {
      margin: 0 0 6px;
      color: #FCD34D;
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0;
    }

    .linking-header h1 {
      margin: 0;
      color: #fff;
      font-size: clamp(24px, 3vw, 34px);
      line-height: 1.15;
      font-weight: 800;
    }

    .header-meta {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-top: 10px;
    }

    .meta-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      min-height: 30px;
      padding: 0 10px;
      border-radius: 999px;
      background: rgba(255, 255, 255, .12);
      border: 1px solid rgba(255, 255, 255, .2);
      color: #fff;
      font-size: 12px;
      font-weight: 900;
    }

    .header-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 10px;
      flex-wrap: wrap;
    }

    .header-btn,
    .header-btn:hover,
    .header-btn:focus {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 42px;
      padding: 0 18px;
      border-radius: 12px;
      background: rgba(255, 255, 255, .12);
      border: 1px solid rgba(255, 255, 255, .24);
      color: #fff;
      font-weight: 800;
      text-decoration: none;
    }

    .header-btn-view {
      background: var(--api-sage);
      border-color: rgba(255, 255, 255, .2);
    }

    .header-btn-pages {
      background: var(--api-copper);
      border-color: rgba(255, 255, 255, .2);
      color: #111827;
    }

    .linking-card {
      background: var(--api-card);
      border: 1px solid var(--api-border);
      border-top: 0;
      border-radius: 0 0 18px 18px;
      padding: clamp(16px, 2vw, 24px);
      box-shadow: 0 18px 44px rgba(17, 24, 39, .08);
      overflow: visible;
    }

    .toolbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
      margin-bottom: 16px;
    }

    .toolbar h2 {
      margin: 0;
      font-size: clamp(18px, 2vw, 24px);
      font-weight: 800;
    }

    .toolbar p {
      margin: 4px 0 0;
      color: var(--api-muted);
      font-size: 14px;
      font-weight: 600;
    }

    .row-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .link-row {
      display: grid;
      grid-template-columns: 112px minmax(190px, 1fr) minmax(320px, 1.55fr) auto auto auto;
      gap: 12px;
      align-items: end;
      padding: 14px;
      border: 1px solid var(--api-border);
      border-radius: 12px;
      background: linear-gradient(180deg, #FFFFFF 0%, #FFFBF4 100%);
      box-shadow: 0 12px 28px rgba(17, 24, 39, .05);
    }

    .link-row.is-dragging {
      opacity: .55;
      border-color: var(--api-copper);
      box-shadow: 0 12px 28px rgba(35, 41, 70, .16);
    }

    .drag-cell {
      align-self: stretch;
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: center;
      gap: 6px;
      min-height: 74px;
      border: 1px dashed rgba(15, 118, 110, .34);
      border-radius: 10px;
      background: var(--api-ink-soft);
      color: var(--api-sage-dark);
      cursor: grab;
      user-select: none;
    }

    .drag-cell:active {
      cursor: grabbing;
    }

    .drag-cell .icon {
      width: 20px;
      height: 20px;
    }

    .drag-motion {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 24px;
      height: 30px;
      color: var(--api-copper);
      animation: dragHint 1.25s ease-in-out infinite;
    }

    .drag-motion .icon {
      width: 18px;
      height: 18px;
    }

    @keyframes dragHint {
      0%, 100% {
        transform: translateY(-3px);
        opacity: .62;
      }

      50% {
        transform: translateY(3px);
        opacity: 1;
      }
    }

    .row-order {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 42px;
      height: 26px;
      border-radius: 999px;
      padding: 0 8px;
      background: rgba(245, 158, 11, .16);
      color: var(--api-graphite);
      font-size: 12px;
      font-weight: 900;
    }

    .drag-help {
      display: flex;
      align-items: center;
      gap: 8px;
      margin: 0 0 14px;
      padding: 10px 12px;
      border: 1px dashed rgba(15, 118, 110, .38);
      border-radius: 12px;
      background: rgba(15, 118, 110, .08);
      color: var(--api-graphite);
      font-size: 13px;
      font-weight: 800;
    }

    .pages-added {
      display: none;
      margin: 0 0 14px;
      padding: 12px;
      border: 1px solid var(--api-border);
      border-radius: 12px;
      background: #fff;
    }

    .pages-added.is-visible {
      display: block;
    }

    .pages-added-title {
      margin: 0 0 8px;
      color: var(--api-muted);
      font-size: 12px;
      font-weight: 900;
      text-transform: uppercase;
    }

    .page-chip-list {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .page-chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      min-height: 30px;
      padding: 0 10px;
      border-radius: 999px;
      background: rgba(15, 118, 110, .1);
      color: var(--api-graphite);
      font-size: 12px;
      font-weight: 900;
    }

    .field {
      min-width: 0;
    }

    label {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 6px;
      color: var(--api-muted);
      font-size: 12px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: 0;
    }

    input,
    select {
      width: 100%;
      min-height: 42px;
      border: 1px solid var(--api-border);
      border-radius: 10px;
      padding: 8px 10px;
      background: #fff;
      color: var(--api-graphite);
      font-size: 14px;
      font-weight: 700;
      outline: none;
      transition: border-color .16s ease, box-shadow .16s ease, background-color .16s ease;
    }

    select {
      appearance: none;
      background-image:
        linear-gradient(45deg, transparent 50%, var(--api-muted) 50%),
        linear-gradient(135deg, var(--api-muted) 50%, transparent 50%);
      background-position:
        calc(100% - 18px) 18px,
        calc(100% - 12px) 18px;
      background-size: 6px 6px, 6px 6px;
      background-repeat: no-repeat;
      padding-right: 34px;
    }

    select[multiple] {
      min-height: 132px;
      padding: 8px;
      background-image: none;
      overflow: auto;
    }

    option {
      padding: 10px 12px;
      color: var(--api-graphite);
      background: #fff;
      font-weight: 700;
    }

    option:checked {
      background: linear-gradient(135deg, rgba(94, 139, 106, .18), rgba(211, 146, 95, .18));
      color: var(--api-graphite);
    }

    input:focus,
    select:focus {
      border-color: var(--api-sage);
      box-shadow: 0 0 0 4px rgba(15, 118, 110, .14);
    }

    input::placeholder {
      color: #98A2B3;
      font-weight: 600;
    }

    button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 42px;
      border: 0;
      border-radius: 10px;
      padding: 0 14px;
      cursor: pointer;
      font-weight: 900;
      white-space: nowrap;
    }

    .btn-row {
      background: var(--api-graphite);
      color: #fff;
    }

    .btn-row.is-limited {
      background: #E5E7EB;
      color: #6B7280;
      border: 1px solid #D1D5DB;
    }

    .btn-save-row {
      background: var(--api-copper);
      color: #fff;
    }

    .btn-save-row.is-saved {
      background: var(--api-sage);
      color: #fff;
    }

    .btn-remove {
      background: rgba(180, 83, 83, .12);
      color: var(--api-danger);
      border: 1px solid rgba(180, 83, 83, .2);
    }

    .btn-remove:hover {
      background: var(--api-danger);
      color: #fff;
    }

    .btn-row-view {
      background: linear-gradient(135deg, #111827, var(--api-graphite-2));
      color: #fff;
      border: 1px solid rgba(245, 158, 11, .42);
      box-shadow: 0 10px 24px rgba(17, 24, 39, .18);
    }

    .btn-row-view:hover {
      background: var(--api-graphite);
      color: #fff;
    }

    .parameter {
      display: none;
    }

    .multi-combo {
      position: relative;
    }

    .multi-combo.is-open,
    .link-row.is-combo-open {
      z-index: 80;
    }

    .link-row.is-combo-open {
      position: relative;
    }

    .multi-combo-control {
      position: relative;
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 6px;
      width: 100%;
      min-height: 46px;
      padding: 6px 38px 6px 8px;
      border: 1px solid var(--api-border);
      border-radius: 10px;
      background: #fff;
      color: var(--api-graphite);
      cursor: pointer;
      transition: border-color .16s ease, box-shadow .16s ease;
    }

    .multi-combo-control:focus-within {
      border-color: var(--api-sage);
      box-shadow: 0 0 0 4px rgba(15, 118, 110, .14);
    }

    .parameter-picker {
      flex: 1 1 140px;
      min-width: 120px;
      min-height: 32px;
      width: 100%;
      border: 0;
      padding: 0 4px;
      margin: 0;
      background: transparent;
      box-shadow: none;
      font-size: 14px;
      font-weight: 700;
      color: var(--api-graphite);
      cursor: pointer;
    }

    .parameter-picker:focus {
      box-shadow: none;
      border: 0;
      outline: none;
    }

    .multi-combo-clear {
      position: absolute;
      right: 8px;
      top: 50%;
      transform: translateY(-50%);
      width: 28px;
      min-width: 28px;
      min-height: 28px;
      padding: 0;
      border-radius: 50%;
      background: transparent;
      color: var(--api-muted);
    }

    .multi-combo-clear:hover {
      background: rgba(180, 83, 83, .1);
      color: var(--api-danger);
    }

    .parameter-field .param-order-hint {
      margin-left: 6px;
      color: var(--api-copper);
      font-size: 11px;
      font-weight: 800;
      text-transform: none;
      letter-spacing: 0;
    }

    .param-order-list {
      display: flex;
      flex-direction: column;
      gap: 6px;
      margin: 0 0 8px;
    }

    .param-order-list:empty,
    .param-order-list.is-empty {
      display: none;
      margin: 0;
    }

    .param-order-item {
      display: flex;
      align-items: center;
      gap: 8px;
      min-height: 34px;
      padding: 0 6px 0 8px;
      border: 1px solid rgba(245, 158, 11, .35);
      border-radius: 8px;
      background: #FEF3C7;
      color: var(--api-graphite);
      cursor: default;
      user-select: none;
    }

    .param-order-item:active {
      cursor: grabbing;
    }

    .param-order-item.is-dragging {
      opacity: .5;
      border-color: var(--api-copper);
    }

    .param-order-grip {
      display: inline-flex;
      color: var(--api-muted);
      cursor: grab;
    }

    .param-order-grip:active {
      cursor: grabbing;
    }

    .param-order-grip .icon {
      width: 14px;
      height: 14px;
    }

    .param-order-seq {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 20px;
      height: 20px;
      border-radius: 999px;
      background: rgba(17, 24, 39, .08);
      color: var(--api-graphite);
      font-size: 11px;
      font-weight: 900;
    }

    .param-order-text {
      min-width: 0;
      flex: 1 1 auto;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      font-size: 13px;
      font-weight: 800;
    }

    .param-order-item .multi-chip-remove {
      position: relative;
      z-index: 2;
      flex: 0 0 auto;
      width: 26px;
      min-width: 26px;
      min-height: 26px;
    }

    .link-row.is-status-locked .multi-combo-control,
    .link-row.is-status-locked .multi-combo-clear,
    .link-row.is-status-locked .multi-chip-remove,
    .link-row.is-status-locked .btn-remove {
      display: none !important;
    }

    .link-row.is-status-locked .page-name {
      pointer-events: none;
      background: #F3F4F6;
      color: #6B7280;
    }

    .link-row.is-status-locked .param-order-hint {
      color: var(--api-muted);
    }

    .multi-chip {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      max-width: 100%;
      min-height: 28px;
      padding: 0 7px 0 10px;
      border-radius: 7px;
      background: #FEF3C7;
      color: var(--api-graphite);
      font-size: 13px;
      font-weight: 800;
    }

    .multi-chip-text {
      min-width: 0;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .multi-chip-remove {
      width: 18px;
      min-width: 18px;
      min-height: 18px;
      padding: 0;
      border-radius: 50%;
      background: transparent;
      color: var(--api-muted);
    }

    .multi-chip-remove:hover {
      background: rgba(35, 41, 70, .1);
      color: var(--api-graphite);
    }

    .multi-chip-remove .icon,
    .multi-combo-clear .icon {
      width: 13px;
      height: 13px;
      flex-basis: 13px;
    }

    .multi-combo-menu {
      position: absolute;
      z-index: 90;
      left: 0;
      right: 0;
      top: calc(100% + 4px);
      display: none;
      max-height: 220px;
      overflow: auto;
      border: 1px solid var(--api-border);
      border-radius: 10px;
      background: #fff;
      box-shadow: 0 16px 34px rgba(35, 41, 70, .16);
    }

    .multi-combo.is-open .multi-combo-menu {
      display: block;
    }

    .multi-option {
      display: flex;
      align-items: center;
      min-height: 38px;
      padding: 9px 11px;
      color: var(--api-graphite);
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
    }

    .multi-option:hover,
    .multi-option.is-active {
      background: rgba(15, 118, 110, .14);
    }

    .multi-option.is-selected {
      background: rgba(245, 158, 11, .18);
      color: var(--api-graphite);
    }

    .multi-option.is-hidden {
      display: none;
    }

    .multi-empty {
      padding: 11px;
      color: var(--api-muted);
      font-size: 13px;
      font-weight: 800;
      display: none;
    }

    .multi-combo.has-no-results .multi-empty {
      display: block;
    }

    .row-status {
      grid-column: 1 / -1;
      display: none;
      align-items: center;
      gap: 6px;
      color: var(--api-sage);
      font-size: 12px;
      font-weight: 900;
    }

    .link-row.is-saved {
      border-color: rgba(15, 118, 110, .5);
      background: linear-gradient(180deg, #FFFFFF 0%, rgba(15, 118, 110, .08) 100%);
    }

    .notice-stack {
      position: fixed;
      z-index: 9999;
      top: 18px;
      right: 18px;
      display: grid;
      gap: 10px;
      width: min(360px, calc(100vw - 28px));
      pointer-events: none;
    }

    .notice {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 13px 14px;
      border-radius: 14px;
      background: #FFFFFF;
      border: 1px solid var(--api-border);
      color: var(--api-graphite);
      box-shadow: 0 18px 44px rgba(17, 24, 39, .18);
      font-weight: 800;
      line-height: 1.35;
      transform: translateY(-8px);
      opacity: 0;
      animation: noticeIn .18s ease forwards;
    }

    .notice .icon {
      margin-top: 1px;
      color: var(--api-copper);
    }

    .notice-warning {
      border-color: rgba(245, 158, 11, .45);
      background: linear-gradient(135deg, #FFFFFF, #FFFBEB);
    }

    .notice-error {
      border-color: rgba(220, 38, 38, .35);
      background: linear-gradient(135deg, #FFFFFF, #FEF2F2);
    }

    .notice-success {
      border-color: rgba(15, 118, 110, .35);
      background: linear-gradient(135deg, #FFFFFF, #ECFDF5);
    }

    .confirm-overlay {
      position: fixed;
      inset: 0;
      z-index: 10000;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 18px;
      background: rgba(17, 24, 39, .42);
      backdrop-filter: blur(6px);
    }

    .confirm-overlay.is-visible {
      display: flex;
    }

    .confirm-dialog {
      width: min(420px, 100%);
      padding: 18px;
      border-radius: 16px;
      background: #FFFFFF;
      border: 1px solid rgba(220, 38, 38, .22);
      box-shadow: 0 24px 70px rgba(17, 24, 39, .28);
    }

    .confirm-dialog h3 {
      margin: 0 0 8px;
      color: var(--api-graphite);
      font-size: 18px;
      font-weight: 900;
    }

    .confirm-dialog p {
      margin: 0;
      color: var(--api-muted);
      font-size: 14px;
      font-weight: 700;
      line-height: 1.45;
    }

    .confirm-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 18px;
      flex-wrap: wrap;
    }

    .btn-confirm-cancel {
      background: #F3F4F6;
      color: var(--api-graphite);
      border: 1px solid var(--api-border);
    }

    .btn-confirm-delete {
      background: var(--api-danger);
      color: #fff;
    }

    @keyframes noticeIn {
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .link-row.is-saved .row-status {
      display: flex;
    }

    .footer-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 18px;
      padding-top: 18px;
      border-top: 1px solid var(--api-border);
    }

    .btn-view {
      min-width: 160px;
      background: var(--api-copper);
      color: #fff;
    }

    .btn-view:hover {
      background: #B97848;
    }

    .empty-note {
      display: none;
      padding: 16px;
      border: 1px dashed var(--api-border);
      border-radius: 12px;
      background: #fff;
      color: var(--api-muted);
      font-weight: 800;
      text-align: center;
    }

    .icon {
      width: 16px;
      height: 16px;
      flex: 0 0 16px;
      stroke: currentColor;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
      fill: none;
    }

    .label-icon {
      color: var(--api-copper);
    }

    @media (max-width: 980px) {
      .link-row {
        grid-template-columns: 104px minmax(220px, 1fr);
      }

      .link-row > .field:nth-of-type(2) {
        grid-column: 1 / -1;
      }

      .btn-row-save,
      .btn-remove,
      .btn-row-view {
        width: 100%;
      }

    }

    @media (max-width: 640px) {
      .linking-header,
      .linking-card {
        border-radius: 14px;
      }

      .linking-card {
        border-top: 1px solid var(--api-border);
        margin-top: 12px;
      }

      .header-actions,
      .header-btn,
      .btn-save-row,
      .btn-row,
      .btn-row-view,
      .btn-view {
        width: 100%;
      }

      .btn-remove {
        width: 100%;
      }

      .link-row {
        grid-template-columns: 1fr;
      }

      .drag-cell {
        min-height: 54px;
        justify-content: flex-start;
      }

      .notice-stack {
        top: 10px;
        right: 10px;
        left: 10px;
        width: auto;
      }

      .footer-actions {
        justify-content: stretch;
      }
    }
    
  </style>




</head>
<body>
  <div class="notice-stack" id="notice-stack" aria-live="polite" aria-atomic="true"></div>
  <div class="confirm-overlay" id="remove-confirm" role="dialog" aria-modal="true" aria-labelledby="remove-confirm-title">
    <div class="confirm-dialog">
      <h3 id="remove-confirm-title">Remove this row?</h3>
      <p id="remove-confirm-text">This page link row will be removed from the current linking setup after you save again.</p>
      <div class="confirm-actions">
        <button type="button" class="btn-confirm-cancel" id="remove-cancel">Cancel</button>
        <button type="button" class="btn-confirm-delete" id="remove-confirm-btn"><?= linking_icon('trash') ?> Remove</button>
      </div>
    </div>
  </div>
  <div class="linking-shell">
    <div class="linking-header">
      <div>
        <p class="linking-kicker">Page UI Builder</p>
        <h1>Modal API Page Linking</h1>
        <div class="header-meta">
          <span class="meta-pill"><?= linking_icon('hash') ?> Modal ID: <?= esc($modal_id) ?></span>
          <span class="meta-pill"><?= linking_icon('layers') ?> Model No: <?= esc($modelno) ?></span>
        </div>
      </div>

      <div class="header-actions">
        <a href="<?= esc($viewApisUrl) ?>" class="header-btn header-btn-view"><?= linking_icon('eye') ?> View APIs</a>
        <a href="<?= esc($pagesUrl) ?>" class="header-btn header-btn-pages"><?= linking_icon('plus-circle') ?> Add Page</a>
        <a href="<?= esc($pagesUrl) ?>" class="header-btn"><?= linking_icon('arrow-left') ?> Back</a>
      </div>
    </div>

    <?php echo form_open(get_uri("items/submit_page_params"), ["id" => "page-linking-form", "class" => "linking-card"]); ?>
      <input type="hidden" name="modelno" value="<?= esc($modelno) ?>">
      <input type="hidden" name="modal_id" value="<?= esc($modal_id) ?>">

      <div class="toolbar">
        <div>
          <h2>Arrange Parameters</h2>
          <p>Choose page details and parameters. Each API can be linked to only one page. Drag rows to set page sequence, and drag parameters in a row to set API sequence.</p>
        </div>
        <button type="button" class="btn-row" id="add-new-row"><?= linking_icon('plus-circle') ?> Add New Row</button>
      </div>

      <div class="drag-help"><?= linking_icon('grip') ?> Drag pages to reorder rows. Drag parameters in a page to change their sequence. An API already used on one page is hidden from the others.</div>
      <div class="pages-added" id="pages-added">
        <p class="pages-added-title">Pages Added</p>
        <div class="page-chip-list" id="pages-added-list"></div>
      </div>
      <div class="empty-note" id="empty-note">No rows added yet.</div>
      <div class="row-list" id="row-list"></div>

      <div class="footer-actions">
        <button type="submit" class="btn-save-row"><?= linking_icon('check') ?> Save &amp; View Linked UI</button>
      </div>
    </form>
  </div>

  <template id="row-template">
    <div class="link-row">
      <div class="drag-cell" draggable="true" title="Drag to reorder pages" aria-label="Drag to reorder pages">
        <?= linking_icon('grip') ?>
        <span class="row-order">Page</span>
        <span class="drag-motion" title="Drag up or down"><?= linking_icon('move-vertical') ?></span>
        <input type="hidden" class="page-no" value="">
      </div>
      <div class="field page-name-field">
        <label><?= linking_icon('file') ?> Page Name</label>
        <select class="page-name">
          <option value="">Select page</option>
          <?php foreach ($pageOptions as $page): ?>
            <option value="<?= esc($page['page_name']) ?>" data-page-no="<?= esc($page['page_no']) ?>">
              <?= esc($page['page_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field parameter-field">
        <label><?= linking_icon('list') ?> Parameter <span class="param-order-hint">Drag to reorder</span></label>
        <div class="multi-combo">
          <div class="param-order-list" aria-label="Selected parameters"></div>
          <div class="multi-combo-control">
            <select class="parameter-picker" aria-label="Add parameter">
              <option value="">Select parameter</option>
              <?php foreach ($params as $param): ?>
                <option value="<?= esc($param) ?>"><?= esc($param) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="multi-combo-clear" title="Clear selected parameters" aria-label="Clear selected parameters"><?= linking_icon('x') ?></button>
          </div>
          <select class="parameter" multiple>
            <?php foreach ($params as $param): ?>
              <option value="<?= esc($param) ?>"><?= esc($param) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <input type="hidden" class="sequence" value="1">

      <button type="button" class="btn-save-row btn-row-save"><?= linking_icon('check') ?> Save</button>
      <button type="button" class="btn-remove"><?= linking_icon('trash') ?> Remove</button>
      <button type="button" class="btn-row-view"><?= linking_icon('eye') ?> View UI</button>
    </div>
  </template>

  <script>
    const initialRows = <?= json_encode($displayRows, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const pageOptions = <?= json_encode($pageOptions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const rowList = document.getElementById('row-list');
    const template = document.getElementById('row-template');
    const emptyNote = document.getElementById('empty-note');
    const pagesAdded = document.getElementById('pages-added');
    const pagesAddedList = document.getElementById('pages-added-list');
    const rowViewBaseUrl = <?= json_encode(get_uri('items/view_page_ui/' . (int) $modal_id), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const rowSaveUrl = <?= json_encode(get_uri('items/save_page_linking_row'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const modelNo = <?= json_encode($modelno, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const allParameterOptions = <?= json_encode(array_values($params), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const addNewRowBtn = document.getElementById('add-new-row');
    const noticeStack = document.getElementById('notice-stack');
    const removeConfirm = document.getElementById('remove-confirm');
    const removeCancel = document.getElementById('remove-cancel');
    const removeConfirmBtn = document.getElementById('remove-confirm-btn');
    const removeConfirmTitle = document.getElementById('remove-confirm-title');
    const removeConfirmText = document.getElementById('remove-confirm-text');
    let pendingRemove = null;

    function isStatusOperationPage(pageName) {
      const name = String(pageName || '').trim().toLowerCase().replace(/\s+/g, '');
      return name === 'status.operation' || name === 'status';
    }

    function parameterKey(value) {
      return String(value || '').trim().toLowerCase();
    }

    function pageLabel(row) {
      const pageNo = row && row.querySelector('.page-no') ? row.querySelector('.page-no').value.trim() : '';
      const pageName = row && row.querySelector('.page-name') ? row.querySelector('.page-name').value.trim() : '';
      if (pageNo && pageName) return 'Page ' + pageNo + ': ' + pageName;
      if (pageNo) return 'Page ' + pageNo;
      return pageName || 'another page';
    }

    function usedParametersOnOtherPages(exceptRow) {
      const used = new Map();
      rowList.querySelectorAll('.link-row').forEach(row => {
        if (exceptRow && row === exceptRow) return;
        getSelectedParameters(row).forEach(parameter => {
          const key = parameterKey(parameter);
          if (key && !used.has(key)) {
            used.set(key, pageLabel(row));
          }
        });
      });
      return used;
    }

    function refreshAllParameterPickers() {
      rowList.querySelectorAll('.link-row').forEach(row => refreshPickerOptions(row));
    }

    function isStatusLockedRow(row) {
      if (!row) return false;
      const pageName = row.querySelector('.page-name') ? row.querySelector('.page-name').value : '';
      return isStatusOperationPage(pageName);
    }

    function applyStatusLock(row) {
      if (!row) return;
      const locked = isStatusLockedRow(row);
      row.classList.toggle('is-status-locked', locked);
      const pageSelect = row.querySelector('.page-name');
      if (pageSelect) pageSelect.disabled = locked;
      const picker = row.querySelector('.parameter-picker');
      if (picker) {
        picker.disabled = locked;
        const firstOption = picker.querySelector('option[value=""]');
        if (firstOption) {
          firstOption.textContent = locked ? 'Default APIs locked' : 'Select parameter';
        }
      }
      const clearBtn = row.querySelector('.multi-combo-clear');
      if (clearBtn) {
        clearBtn.hidden = locked;
        clearBtn.disabled = locked;
      }
      const removeBtn = row.querySelector('.btn-remove');
      if (removeBtn) {
        removeBtn.hidden = locked;
        removeBtn.disabled = locked;
        removeBtn.title = locked ? 'Default Status.operation page cannot be removed' : 'Remove this row';
      }
      row.querySelectorAll('.multi-chip-remove').forEach(button => {
        button.hidden = locked;
        button.disabled = locked;
      });
      const hint = row.querySelector('.param-order-hint');
      if (hint) {
        hint.textContent = locked
          ? 'Default Status.operation APIs cannot be added or removed'
          : 'Drag to reorder';
      }
      if (locked) {
        closeMultiCombo(row);
      }
    }

    function showNotice(message, type = 'warning') {
      const notice = document.createElement('div');
      notice.className = 'notice notice-' + type;
      notice.innerHTML = `<?= linking_icon('plus-circle') ?><span></span>`;
      notice.querySelector('span').textContent = String(message || '');
      noticeStack.appendChild(notice);
      window.setTimeout(() => {
        notice.style.opacity = '0';
        notice.style.transform = 'translateY(-8px)';
        window.setTimeout(() => notice.remove(), 180);
      }, 3200);
    }

    window.alert = message => showNotice(message, 'warning');

    function updateEmptyState() {
      emptyNote.style.display = rowList.children.length ? 'none' : 'block';
    }

    function updatePagesAddedSummary() {
      if (!pagesAdded || !pagesAddedList) return;
      const seen = new Map();
      rowList.querySelectorAll('.link-row').forEach(row => {
        const pageNo = row.querySelector('.page-no').value.trim();
        const pageName = row.querySelector('.page-name').value.trim();
        if (pageNo && pageName && !seen.has(pageNo)) {
          seen.set(pageNo, pageName);
        }
      });
      pagesAddedList.innerHTML = '';
      seen.forEach((pageName, pageNo) => {
        const chip = document.createElement('span');
        chip.className = 'page-chip';
        chip.textContent = 'Page ' + pageNo + ': ' + pageName;
        pagesAddedList.appendChild(chip);
      });
      pagesAdded.classList.toggle('is-visible', seen.size > 0);
    }

    function rebuildSequences() {
      rowList.querySelectorAll('.link-row').forEach((row, index) => {
        const sequence = row.querySelector('.sequence');
        const order = row.querySelector('.row-order');
        const pageNo = row.querySelector('.page-no');
        if (sequence) sequence.value = String(index + 1);
        if (order) order.textContent = pageNo && pageNo.value ? 'Page ' + pageNo.value : 'Page';
      });
      updatePagesAddedSummary();
      refreshPageSelectOptions();
      refreshAllParameterPickers();
      updateAddButtonState();
    }

    function getPageName(pageNo) {
      const page = pageOptions.find(item => String(item.page_no) === String(pageNo));
      return page ? page.page_name : 'Page ' + pageNo;
    }

    function getPageNo(pageName) {
      const page = pageOptions.find(item => String(item.page_name) === String(pageName));
      return page ? page.page_no : '';
    }

    function syncPageNo(row) {
      const pageSelect = row.querySelector('.page-name');
      const selectedOption = pageSelect.options[pageSelect.selectedIndex];
      const pageNo = selectedOption ? selectedOption.getAttribute('data-page-no') : '';
      row.querySelector('.page-no').value = pageNo || getPageNo(pageSelect.value) || '';
      const order = row.querySelector('.row-order');
      if (order) order.textContent = row.querySelector('.page-no').value ? 'Page ' + row.querySelector('.page-no').value : 'Page';
      updatePagesAddedSummary();
      refreshPageSelectOptions();
      refreshAllParameterPickers();
      updateAddButtonState();
      applyStatusLock(row);
    }

    const footerSaveBtn = document.querySelector('.footer-actions .btn-save-row');

    function updateFooterButton() {
      const rows = Array.from(rowList.querySelectorAll('.link-row'));
      const allRowsSaved = rows.length > 0 && rows.every(row => row.classList.contains('is-saved'));
      if (footerSaveBtn) {
        footerSaveBtn.innerHTML = rows.length ? `<?= linking_icon('eye') ?> Save & View Linked UI` : `<?= linking_icon('check') ?> Save`;
        footerSaveBtn.style.display = allRowsSaved ? 'inline-flex' : 'none';
        footerSaveBtn.disabled = !allRowsSaved;
      }
    }

    function requiresRowSave(row) {
      return !row.classList.contains('is-saved');
    }

    function selectedPageNos(exceptRow = null) {
      const selected = new Set();
      rowList.querySelectorAll('.link-row').forEach(row => {
        if (exceptRow && row === exceptRow) return;
        const pageNo = row.querySelector('.page-no').value.trim();
        if (pageNo) selected.add(pageNo);
      });
      return selected;
    }

    function getNextAvailablePageOption() {
      const used = selectedPageNos();
      return pageOptions.find(option => !used.has(String(option.page_no))) || null;
    }

    function refreshPageSelectOptions() {
      rowList.querySelectorAll('.link-row').forEach(row => {
        const currentPageNo = row.querySelector('.page-no').value.trim();
        const usedByOthers = selectedPageNos(row);
        row.querySelectorAll('.page-name option').forEach(option => {
          const optionPageNo = option.getAttribute('data-page-no') || '';
          option.disabled = optionPageNo !== '' && optionPageNo !== currentPageNo && usedByOthers.has(optionPageNo);
        });
      });
    }

    function updateAddButtonState() {
      if (!addNewRowBtn) return;
      const hasAvailablePage = Boolean(getNextAvailablePageOption());
      addNewRowBtn.classList.toggle('is-limited', !hasAvailablePage);
      addNewRowBtn.title = hasAvailablePage ? '' : 'No pages left. Add more pages in Modal Pages first.';
    }

    function markRowUnsaved(row) {
      const saveBtn = row.querySelector('.btn-save-row');
      row.classList.remove('is-saved');
      if (saveBtn) {
        saveBtn.classList.remove('is-saved');
        saveBtn.innerHTML = `<?= linking_icon('check') ?> Save`;
      }
      updateFooterButton();
    }

    function setRowSaved(row) {
      row.classList.add('is-saved');
      const saveBtn = row.querySelector('.btn-save-row');
      if (saveBtn) {
        saveBtn.classList.add('is-saved');
        saveBtn.innerHTML = `<?= linking_icon('check') ?> Saved`;
        saveBtn.disabled = false;
      }
      updateFooterButton();
    }

    async function markRowSaved(row) {
      const pageNo = row.querySelector('.page-no').value.trim();
      const pageName = row.querySelector('.page-name').value.trim();
      const parameters = getSelectedParameters(row);
      const saveBtn = row.querySelector('.btn-save-row');

      if (!pageNo || !pageName || !parameters.length) {
        alert('Please complete page name, page no, and parameter for this row.');
        return;
      }

      let duplicateParam = '';
      let duplicateOn = '';
      const usedElsewhere = usedParametersOnOtherPages(row);
      parameters.forEach(parameter => {
        const key = parameterKey(parameter);
        if (usedElsewhere.has(key) && !duplicateParam) {
          duplicateParam = parameter;
          duplicateOn = usedElsewhere.get(key);
        }
      });

      if (duplicateParam) {
        alert(duplicateParam + ' is already used on ' + duplicateOn + '. Each API can be linked to one page only.');
        return;
      }

      let duplicatePage = false;
      rowList.querySelectorAll('.link-row').forEach(otherRow => {
        if (otherRow !== row && otherRow.querySelector('.page-no').value.trim() === pageNo) {
          duplicatePage = true;
        }
      });

      if (duplicatePage) {
        alert('This page is already added. Please select another page.');
        return;
      }

      if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = `<?= linking_icon('check') ?> Saving`;
      }

      try {
        await saveCurrentRows();
        setRowSaved(row);
      } catch (error) {
        if (saveBtn) {
          saveBtn.disabled = false;
          saveBtn.classList.remove('is-saved');
          saveBtn.innerHTML = `<?= linking_icon('check') ?> Save`;
        }
        alert(error.message || 'Unable to save this row.');
      }
    }

    function createRow(data = {}, options = {}) {
      const row = template.content.firstElementChild.cloneNode(true);
      const firstPageNo = pageOptions.length ? pageOptions[0].page_no : '';
      const pageNo = data.page_no || firstPageNo;
      const pageName = data.page_name || getPageName(pageNo);
      row.dataset.requiresRowSave = '1';

      initMultiCombo(row);
      row.querySelector('.page-name').value = pageName;
      row.querySelector('.page-no').value = pageNo;
      setSelectedParameters(row, data.parameters || (data.parameter ? [data.parameter] : []));
      row.querySelector('.sequence').value = data.sequence || rowList.querySelectorAll('.link-row').length + 1;
      syncPageNo(row);
      applyStatusLock(row);

      const saveBtn = row.querySelector('.btn-save-row');
      if (saveBtn) {
        saveBtn.addEventListener('click', () => markRowSaved(row));
      }

      row.querySelector('.btn-remove').addEventListener('click', () => askRemoveRow(row));
      row.querySelector('.btn-row-view').addEventListener('click', () => viewRowUi(row));

      if (options.insertAfter && options.insertAfter.parentElement === rowList) {
        rowList.insertBefore(row, options.insertAfter.nextSibling);
      } else {
        rowList.appendChild(row);
      }
      rebuildNames();
      rebuildSequences();
      updateEmptyState();

      if (getSelectedParameters(row).length) {
        setRowSaved(row);
      } else {
        updateFooterButton();
      }

      return row;
    }

    function removeRow(row) {
      row.remove();
      rebuildNames();
      rebuildSequences();
      updateEmptyState();
      updateFooterButton();
      refreshPageSelectOptions();
      updateAddButtonState();
    }

    function askRemoveRow(row) {
      if (isStatusLockedRow(row)) {
        showNotice('Default Status.operation APIs cannot be added or removed.', 'warning');
        return;
      }
      pendingRemove = { type: 'row', row: row };
      removeConfirmTitle.textContent = 'Remove this row?';
      removeConfirmText.textContent = 'This page link row will be removed from the current linking setup after you save again.';
      removeConfirm.classList.add('is-visible');
      removeConfirmBtn.focus();
    }

    function askRemoveParameter(row, parameter) {
      if (isStatusLockedRow(row)) {
        showNotice('Default Status.operation APIs cannot be added or removed.', 'warning');
        return;
      }
      if (!parameter) return;
      pendingRemove = { type: 'param', row: row, parameter: parameter };
      removeConfirmTitle.textContent = 'Remove this API?';
      removeConfirmText.textContent = 'Remove "' + parameter + '" from this page?';
      removeConfirm.classList.add('is-visible');
      removeConfirmBtn.focus();
    }

    function askClearParameters(row) {
      if (isStatusLockedRow(row)) {
        showNotice('Default Status.operation APIs cannot be added or removed.', 'warning');
        return;
      }
      const selected = getSelectedParameters(row);
      if (!selected.length) return;
      pendingRemove = { type: 'clear', row: row };
      removeConfirmTitle.textContent = 'Remove these APIs?';
      removeConfirmText.textContent = 'Remove all selected APIs from this page?';
      removeConfirm.classList.add('is-visible');
      removeConfirmBtn.focus();
    }

    function closeRemoveConfirm() {
      pendingRemove = null;
      removeConfirm.classList.remove('is-visible');
    }

    removeCancel.addEventListener('click', closeRemoveConfirm);
    removeConfirm.addEventListener('click', event => {
      if (event.target === removeConfirm) {
        closeRemoveConfirm();
      }
    });
    removeConfirmBtn.addEventListener('click', async () => {
      const pending = pendingRemove;
      if (!pending || !pending.row) {
        closeRemoveConfirm();
        return;
      }

      if (pending.type === 'param') {
        const selected = getSelectedParameters(pending.row).filter(parameter => parameter !== pending.parameter);
        setSelectedParameters(pending.row, selected);
        notifyParameterChanged(pending.row);
        closeRemoveConfirm();
        return;
      }

      if (pending.type === 'clear') {
        setSelectedParameters(pending.row, []);
        notifyParameterChanged(pending.row);
        closeRemoveConfirm();
        return;
      }

      const rowToRemove = pending.row;
      removeConfirmBtn.disabled = true;
      removeConfirmBtn.innerHTML = `<?= linking_icon('trash') ?> Removing`;
      removeRow(rowToRemove);

      try {
        await saveCurrentRows({allowEmpty: true});
        showNotice('Row removed from linked UI.', 'success');
      } catch (error) {
        rowList.appendChild(rowToRemove);
        rebuildNames();
        rebuildSequences();
        updateEmptyState();
        updateFooterButton();
        refreshPageSelectOptions();
        updateAddButtonState();
        showNotice(error.message || 'Unable to remove row from database.', 'error');
      } finally {
        removeConfirmBtn.disabled = false;
        removeConfirmBtn.innerHTML = `<?= linking_icon('trash') ?> Remove`;
      }
      closeRemoveConfirm();
    });
    document.addEventListener('keydown', event => {
      if (event.key === 'Escape' && removeConfirm.classList.contains('is-visible')) {
        closeRemoveConfirm();
      }
    });

    function nextSequenceForPage(pageNo) {
      let max = 0;
      rowList.querySelectorAll('.link-row').forEach(row => {
        if (row.querySelector('.page-no').value === String(pageNo)) {
          const sequence = parseInt(row.querySelector('.sequence').value || '0', 10);
          const parameterCount = Math.max(1, getSelectedParameters(row).length);
          max = Math.max(max, sequence + parameterCount - 1);
        }
      });

      return max + 1;
    }

    function uniqueParameters(parameters) {
      const ordered = [];
      (parameters || []).forEach(parameter => {
        parameter = String(parameter || '').trim();
        if (parameter !== '' && !ordered.includes(parameter)) {
          ordered.push(parameter);
        }
      });
      return ordered;
    }

    function getParameterOrder(row) {
      try {
        return uniqueParameters(JSON.parse(row.dataset.parameterOrder || '[]'));
      } catch (error) {
        return [];
      }
    }

    function availableParameterValues(row) {
      const fromSelect = Array.from(row.querySelectorAll('.parameter option, .parameter-picker option'))
        .map(option => String(option.value || '').trim())
        .filter(Boolean);
      return Array.from(new Set(fromSelect));
    }

    function ensureParameterOption(row, value) {
      value = String(value || '').trim();
      if (!value) return;
      const select = row.querySelector('.parameter');
      if (!select) return;
      const exists = Array.from(select.options).some(option => option.value === value);
      if (!exists) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = value;
        select.appendChild(option);
      }
    }

    function syncSelectToOrder(row, parameters) {
      const select = row.querySelector('.parameter');
      if (!select) return;
      const selected = new Set(parameters);
      const options = Array.from(select.options);

      parameters.forEach((parameter, index) => {
        const option = options.find(item => item.value === parameter);
        if (!option) return;
        option.selected = true;
        if (select.options[index] !== option) {
          select.insertBefore(option, select.options[index] || null);
        }
      });

      options.forEach(option => {
        if (!selected.has(option.value)) {
          option.selected = false;
        }
      });
    }

    function getSelectedParameters(row) {
      const available = new Set(availableParameterValues(row));
      const keep = parameter => !available.size || available.has(parameter);
      const fromList = uniqueParameters(Array.from(row.querySelectorAll('.param-order-item'))
        .map(item => item.getAttribute('data-parameter') || item.dataset.parameter || ''))
        .filter(keep);
      const stored = getParameterOrder(row).filter(keep);

      // Stored order wins when it has more APIs than the current chips.
      // Otherwise adding a second parameter was discarded on re-render.
      if (stored.length > fromList.length) {
        return stored;
      }
      if (fromList.length) {
        row.dataset.parameterOrder = JSON.stringify(fromList);
        return fromList;
      }
      if (stored.length) {
        return stored;
      }

      const select = row.querySelector('.parameter');
      if (!select) return [];
      return uniqueParameters(Array.from(select.selectedOptions).map(option => option.value.trim()));
    }

    function setSelectedParameters(row, parameters) {
      uniqueParameters(parameters).forEach(parameter => ensureParameterOption(row, parameter));
      const available = new Set(availableParameterValues(row));
      const ordered = uniqueParameters(parameters).filter(parameter => !available.size || available.has(parameter));
      row.dataset.parameterOrder = JSON.stringify(ordered);
      syncSelectToOrder(row, ordered);
      renderMultiCombo(row, ordered);
    }

    function initMultiCombo(row) {
      const clearBtn = row.querySelector('.multi-combo-clear');
      if (clearBtn) {
        clearBtn.addEventListener('click', event => {
          event.preventDefault();
          event.stopPropagation();
          askClearParameters(row);
        });
      }
      renderMultiCombo(row);
    }

    function addParameterToRow(row, value) {
      value = String(value || '').trim();
      if (!row || !value) return false;
      if (isStatusLockedRow(row)) {
        showNotice('Default Status.operation APIs cannot be added or removed.', 'warning');
        return false;
      }

      ensureParameterOption(row, value);
      const selected = getSelectedParameters(row);
      if (selected.some(parameter => parameterKey(parameter) === parameterKey(value))) {
        showNotice(value + ' is already added on this page.', 'warning');
        return false;
      }

      const usedElsewhere = usedParametersOnOtherPages(row);
      if (usedElsewhere.has(parameterKey(value))) {
        showNotice(value + ' is already used on ' + usedElsewhere.get(parameterKey(value)) + '. Each API can be linked to one page only.', 'warning');
        return false;
      }

      selected.push(value);
      setSelectedParameters(row, selected);
      notifyParameterChanged(row);
      return true;
    }

    function closeMultiCombo(row) {
      if (!row) return;
      row.classList.remove('is-combo-open');
      const combo = row.querySelector('.multi-combo');
      if (combo) combo.classList.remove('is-open');
    }

    function notifyParameterChanged(row) {
      const select = row.querySelector('.parameter');
      if (select) {
        select.dispatchEvent(new Event('change', {bubbles: true}));
      }
    }

    function refreshPickerOptions(row) {
      if (!row) return;
      const picker = row.querySelector('.parameter-picker');
      if (!picker) return;

      const usedElsewhere = usedParametersOnOtherPages(row);
      const selectedHere = new Set(getSelectedParameters(row).map(parameterKey));
      const names = Array.isArray(allParameterOptions) ? allParameterOptions : [];

      picker.innerHTML = '';
      const placeholder = document.createElement('option');
      placeholder.value = '';
      placeholder.textContent = isStatusLockedRow(row) ? 'Default APIs locked' : 'Select parameter';
      picker.appendChild(placeholder);

      names.forEach(name => {
        const value = String(name || '').trim();
        if (!value) return;
        const key = parameterKey(value);
        if (selectedHere.has(key) || usedElsewhere.has(key)) return;
        const option = document.createElement('option');
        option.value = value;
        option.textContent = value;
        picker.appendChild(option);
      });

      picker.selectedIndex = 0;
    }

    function renderMultiCombo(row, selected) {
      const list = row.querySelector('.param-order-list');
      if (!Array.isArray(selected)) {
        selected = getSelectedParameters(row);
      }
      selected = uniqueParameters(selected);

      if (list) {
        list.innerHTML = '';
        selected.forEach((parameter, index) => {
          const item = document.createElement('div');
          item.className = 'param-order-item';
          item.draggable = false;
          item.setAttribute('data-parameter', parameter);
          item.dataset.parameter = parameter;
          item.title = 'Use the grip to change parameter sequence';

          const grip = document.createElement('span');
          grip.className = 'param-order-grip';
          grip.draggable = true;
          grip.title = 'Drag to reorder';
          grip.innerHTML = `<?= linking_icon('grip') ?>`;

          const seq = document.createElement('span');
          seq.className = 'param-order-seq';
          seq.textContent = String(index + 1);

          const text = document.createElement('span');
          text.className = 'param-order-text';
          text.textContent = parameter;

          const remove = document.createElement('button');
          remove.type = 'button';
          remove.className = 'multi-chip-remove';
          remove.title = 'Remove ' + parameter;
          remove.setAttribute('aria-label', 'Remove ' + parameter);
          remove.draggable = false;
          remove.innerHTML = `<?= linking_icon('x') ?>`;
          remove.addEventListener('pointerdown', event => {
            event.preventDefault();
            event.stopPropagation();
          });
          remove.addEventListener('click', event => {
            event.preventDefault();
            event.stopPropagation();
            askRemoveParameter(row, parameter);
          });

          item.appendChild(grip);
          item.appendChild(seq);
          item.appendChild(text);
          item.appendChild(remove);
          list.appendChild(item);
        });
        list.classList.toggle('is-empty', selected.length === 0);
      }

      refreshPickerOptions(row);
      refreshAllParameterPickers();
      applyStatusLock(row);
    }
    function viewRowUi(row) {
      const pageNo = row.querySelector('.page-no').value.trim();
      const pageName = row.querySelector('.page-name').value.trim();
      const parameters = getSelectedParameters(row);
      rebuildSequences();
      const sequence = row.querySelector('.sequence').value.trim();

      if (!pageNo || !pageName || !parameters.length) {
        alert('Please complete page name, page no, and parameter before viewing UI.');
        return;
      }

      const url = new URL(rowViewBaseUrl, window.location.href);
      url.searchParams.set('modelno', modelNo);
      url.searchParams.set('row_page_no', pageNo);
      url.searchParams.set('row_page_name', pageName);
      url.searchParams.set('row_sequence', sequence);
      url.searchParams.set('row_parameters', JSON.stringify(parameters));
      window.location.href = url.toString();
    }

    function rebuildNames() {
      rowList.querySelectorAll('.link-row').forEach(row => {
        row.querySelector('.page-name').removeAttribute('name');
        row.querySelector('.page-no').removeAttribute('name');
        row.querySelector('.parameter').removeAttribute('name');
        row.querySelector('.sequence').removeAttribute('name');
      });
    }

    addNewRowBtn.addEventListener('click', () => {
      const option = getNextAvailablePageOption();
      if (!option) {
        alert(pageOptions.length ? 'No pages left. Add more pages in Modal Pages first.' : 'Please add pages in Modal Pages first.');
        updateAddButtonState();
        return;
      }
      createRow({
        page_no: option.page_no,
        page_name: option.page_name,
        parameter: '',
        sequence: 1
      });
    });

    rowList.addEventListener('change', event => {
      if (event.target.classList.contains('parameter-picker')) {
        const row = event.target.closest('.link-row');
        const picker = event.target;
        const value = String(picker.value || '').trim();
        if (row && value) {
          addParameterToRow(row, value);
        }
        window.setTimeout(() => {
          picker.selectedIndex = 0;
        }, 0);
        return;
      }

      if (event.target.classList.contains('page-name')) {
        const row = event.target.closest('.link-row');
        syncPageNo(row);
        rebuildSequences();
      }

      if (event.target.closest('.link-row')) {
        markRowUnsaved(event.target.closest('.link-row'));
      }

      rebuildNames();
    });

    rowList.addEventListener('input', event => {
      if (event.target.closest('.link-row')) {
        markRowUnsaved(event.target.closest('.link-row'));
      }

      rebuildNames();
    });

    let draggedRow = null;
    let draggedParam = null;

    function applyParameterListOrder(row) {
      if (!row) return;
      const ordered = uniqueParameters(Array.from(row.querySelectorAll('.param-order-item'))
        .map(item => item.getAttribute('data-parameter') || item.dataset.parameter || ''));
      setSelectedParameters(row, ordered);
      notifyParameterChanged(row);
    }

    rowList.addEventListener('dragstart', event => {
      const grip = event.target.closest('.param-order-grip');
      const paramItem = grip ? grip.closest('.param-order-item') : null;
      if (paramItem) {
        if (event.target.closest('.multi-chip-remove')) {
          event.preventDefault();
          return;
        }
        draggedParam = paramItem;
        draggedRow = null;
        paramItem.classList.add('is-dragging');
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', paramItem.getAttribute('data-parameter') || '');
        event.stopPropagation();
        return;
      }

      const handle = event.target.closest('.drag-cell');
      if (!handle) {
        event.preventDefault();
        return;
      }

      const row = handle.closest('.link-row');
      if (!row) return;
      draggedRow = row;
      draggedParam = null;
      row.classList.add('is-dragging');
      event.dataTransfer.effectAllowed = 'move';
      event.dataTransfer.setData('text/plain', '');
      if (event.dataTransfer.setDragImage) {
        event.dataTransfer.setDragImage(row, 40, 24);
      }
    });

    rowList.addEventListener('dragover', event => {
      if (draggedParam) {
        event.preventDefault();
        const list = draggedParam.parentElement;
        const overList = event.target.closest('.param-order-list');
        if (!list || overList !== list) {
          return;
        }
        const afterElement = getDragAfterChip(list, event.clientY);
        if (afterElement == null) {
          list.appendChild(draggedParam);
        } else {
          list.insertBefore(draggedParam, afterElement);
        }
        refreshParamSequenceNumbers(list);
        const liveRow = list.closest('.link-row');
        if (liveRow) {
          const ordered = uniqueParameters(Array.from(list.querySelectorAll('.param-order-item'))
            .map(item => item.dataset.parameter || ''));
          liveRow.dataset.parameterOrder = JSON.stringify(ordered);
          syncSelectToOrder(liveRow, ordered);
        }
        return;
      }

      event.preventDefault();
      const afterElement = getDragAfterElement(rowList, event.clientY);
      if (!draggedRow) return;
      if (afterElement == null) {
        rowList.appendChild(draggedRow);
      } else {
        rowList.insertBefore(draggedRow, afterElement);
      }
      rebuildSequences();
    });

    rowList.addEventListener('drop', event => {
      if (draggedParam) {
        event.preventDefault();
      }
    });

    rowList.addEventListener('dragend', () => {
      if (draggedParam) {
        const row = draggedParam.closest('.link-row');
        draggedParam.classList.remove('is-dragging');
        draggedParam = null;
        applyParameterListOrder(row);
        return;
      }

      if (draggedRow) {
        draggedRow.classList.remove('is-dragging');
      }
      draggedRow = null;
      rebuildSequences();
      updateFooterButton();
    });

    function refreshParamSequenceNumbers(list) {
      if (!list) return;
      list.querySelectorAll('.param-order-item').forEach((item, index) => {
        const seq = item.querySelector('.param-order-seq');
        if (seq) seq.textContent = String(index + 1);
      });
    }

    function getDragAfterChip(container, y) {
      const draggableElements = [...container.querySelectorAll('.param-order-item:not(.is-dragging)')];
      return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
          return { offset: offset, element: child };
        }
        return closest;
      }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    function getDragAfterElement(container, y) {
      const draggableElements = [...container.querySelectorAll('.link-row:not(.is-dragging)')];
      return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
          return { offset: offset, element: child };
        }
        return closest;
      }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    rowList.addEventListener('click', event => {
      const removeBtn = event.target.closest('.multi-chip-remove');
      if (removeBtn && rowList.contains(removeBtn)) {
        event.preventDefault();
        event.stopPropagation();
        const row = removeBtn.closest('.link-row');
        const item = removeBtn.closest('.param-order-item');
        const parameter = item ? (item.getAttribute('data-parameter') || '') : '';
        askRemoveParameter(row, parameter);
      }
    });

    function addHiddenField(container, name, value) {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = name;
      input.value = value;
      container.appendChild(input);
    }

    function collectUiRows() {
      const uiRows = [];

      rebuildSequences();
      rowList.querySelectorAll('.link-row').forEach((row, rowIndex) => {
        const pageNo = row.querySelector('.page-no').value.trim();
        const pageName = row.querySelector('.page-name').value.trim();
        const parameters = getSelectedParameters(row);
        const sequence = rowIndex + 1;

        if (!pageNo || !pageName || !parameters.length) {
          return;
        }

        uiRows.push({
          page_no: pageNo,
          page_name: pageName,
          parameters: parameters,
          sequence: sequence
        });
      });

      return uiRows;
    }

    function getCsrfField() {
      return document.querySelector('#page-linking-form input[type="hidden"][name*="csrf"]');
    }

    async function saveCurrentRows(options = {}) {
      const uiRows = collectUiRows();

      if (!uiRows.length && !options.allowEmpty) {
        throw new Error('Please complete page name and parameter before saving.');
      }

      const body = new URLSearchParams();
      body.set('modelno', modelNo);
      body.set('modal_id', <?= json_encode((string) $modal_id, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>);
      body.set('ui_rows_json', JSON.stringify(uiRows));
      body.set('allow_empty', options.allowEmpty ? '1' : '0');

      const csrfField = getCsrfField();
      if (csrfField) {
        body.set(csrfField.name, csrfField.value);
      }

      const response = await fetch(rowSaveUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: body.toString()
      });
      const result = await response.json();

      if (!response.ok || !result.success) {
        throw new Error(result.message || 'Unable to save row.');
      }

      if (result.csrfName && result.csrfHash) {
        let field = document.querySelector('#page-linking-form input[name="' + result.csrfName + '"]');
        if (!field) {
          field = document.createElement('input');
          field.type = 'hidden';
          field.name = result.csrfName;
          document.getElementById('page-linking-form').appendChild(field);
        }
        field.value = result.csrfHash;
      }

      return result;
    }

    function prepareFlatSubmitFields() {
      const existing = document.getElementById('flat-submit-fields');
      if (existing) {
        existing.remove();
      }

      const container = document.createElement('div');
      container.id = 'flat-submit-fields';
      container.hidden = true;

      const pageNames = new Map();
      let submitSequence = 1;

      rebuildSequences();
      rowList.querySelectorAll('.link-row').forEach((row, rowIndex) => {
        const pageNo = row.querySelector('.page-no').value.trim();
        const pageName = row.querySelector('.page-name').value.trim();

        if (pageNo && pageName) {
          pageNames.set(pageNo, pageName);
        }

        getSelectedParameters(row).forEach(parameter => {
          addHiddenField(container, 'uiPageNo_cmd[]', pageNo);
          addHiddenField(container, 'cmd_parameter[]', parameter);
          addHiddenField(container, 'parameter_sequence[]', submitSequence++);
        });
      });

      pageNames.forEach((pageName, pageNo) => {
        addHiddenField(container, 'page_name[' + pageNo + ']', pageName);
      });

      addHiddenField(container, 'ui_rows_json', JSON.stringify(collectUiRows()));

      document.getElementById('page-linking-form').appendChild(container);
    }

    document.getElementById('page-linking-form').addEventListener('submit', event => {
      const seen = new Map();
      let valid = true;
      let parameterCount = 0;
      let duplicateParam = '';
      let duplicateOn = '';

      rowList.querySelectorAll('.link-row').forEach(row => {
        const pageNo = row.querySelector('.page-no').value.trim();
        const pageName = row.querySelector('.page-name').value.trim();
        const parameters = getSelectedParameters(row);

        if (!pageNo || !pageName || !parameters.length) {
          valid = false;
          return;
        }

        parameters.forEach(parameter => {
          const key = parameterKey(parameter);
          if (seen.has(key)) {
            valid = false;
            if (!duplicateParam) {
              duplicateParam = parameter;
              duplicateOn = seen.get(key);
            }
            return;
          }

          seen.set(key, 'Page ' + pageNo);
          parameterCount++;
        });
      });

      if (!valid) {
        event.preventDefault();
        if (duplicateParam) {
          alert(duplicateParam + ' is already used on ' + duplicateOn + '. Each API can be linked to one page only.');
        } else {
          alert(parameterCount ? 'Please fix duplicate parameters before viewing UI.' : 'Please complete page name, page no, and parameter before viewing UI.');
        }
        return;
      }

      const pageNos = new Set();
      let hasDuplicatePage = false;
      rowList.querySelectorAll('.link-row').forEach(row => {
        const pageNo = row.querySelector('.page-no').value.trim();
        if (pageNo && pageNos.has(pageNo)) {
          hasDuplicatePage = true;
        }
        if (pageNo) {
          pageNos.add(pageNo);
        }
      });

      if (hasDuplicatePage) {
        event.preventDefault();
        alert('Each page can be added only one time.');
        return;
      }

      const unsavedRows = Array.from(rowList.querySelectorAll('.link-row')).filter(row => requiresRowSave(row));
      if (unsavedRows.length) {
        event.preventDefault();
        alert('Please save each row before viewing linked UI.');
        return;
      }

      prepareFlatSubmitFields();
    });

    initialRows.forEach(row => createRow(row));
    rebuildSequences();
    updateEmptyState();
    updatePagesAddedSummary();
    updateFooterButton();
    refreshPageSelectOptions();
    refreshAllParameterPickers();
    updateAddButtonState();
  </script>
</body>
</html>
