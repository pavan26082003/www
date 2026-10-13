<?php
$modelno = $modelno ?? '';
$modal_id = $modal_id ?? 0;
$rows = !empty($model_data['rows']) && is_array($model_data['rows']) ? $model_data['rows'] : [];
$is_row_view = !empty($is_row_view);

$pages = [];
foreach ($rows as $row) {
    $pageNo = (string) ($row['page_no'] ?? '');
    if ($pageNo === '') {
        continue;
    }

    if (!isset($pages[$pageNo])) {
        $pages[$pageNo] = [
            'page_no' => $pageNo,
            'page_name' => $row['page_name'] ?? ('Page ' . $pageNo),
            'params' => [],
        ];
    }

    $pages[$pageNo]['params'][] = [
        'parameter' => $row['parameter'] ?? '',
        'sequence' => (int) ($row['sequence'] ?? 0),
    ];
}

uksort($pages, function ($a, $b) {
    return ((int) $a) <=> ((int) $b);
});

foreach ($pages as &$page) {
    usort($page['params'], function ($a, $b) {
        return ((int) $a['sequence']) <=> ((int) $b['sequence']);
    });
}
unset($page);

$pages = array_values($pages);

if (!function_exists('view_ui_icon')) {
    function view_ui_icon($name)
    {
        $icons = [
            'arrow-left' => '<path d="M19 12H5"></path><path d="m12 19-7-7 7-7"></path>',
            'arrow-right' => '<path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path>',
            'chevron-left' => '<path d="m15 18-6-6 6-6"></path>',
            'chevron-right' => '<path d="m9 18 6-6-6-6"></path>',
            'edit' => '<path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>',
            'file' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path>',
            'gauge' => '<path d="m12 14 4-4"></path><path d="M3.34 19a10 10 0 1 1 17.32 0"></path>',
            'info' => '<circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path>',
            'layers' => '<path d="m12 2 10 5-10 5L2 7l10-5z"></path><path d="m2 17 10 5 10-5"></path><path d="m2 12 10 5 10-5"></path>',
            'save' => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><path d="M17 21v-8H7v8"></path><path d="M7 3v5h8"></path>',
            'settings' => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.52a2 2 0 0 1-1 1.72l-.15.1a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1-1-1.72v-.52a2 2 0 0 1 1-1.72l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle>',
            'signal' => '<path d="M2 20h.01"></path><path d="M7 20a5 5 0 0 1 10 0"></path><path d="M4 14a11 11 0 0 1 16 0"></path><path d="M2 8a16 16 0 0 1 20 0"></path>',
        ];

        $svg = $icons[$name] ?? $icons['file'];
        return '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $svg . '</svg>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>View UI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root {
      --ui-bg: #D1FAFF;
      --ui-shell: #F7FDFF;
      --ui-surface: #FFFFFF;
      --ui-surface-2: #EDF8FB;
      --ui-text: #102A3A;
      --ui-muted: #5F7482;
      --ui-border: rgba(106, 142, 174, .28);
      --ui-cyan: #D1FAFF;
      --ui-blue: #9BD1E5;
      --ui-steel: #6A8EAE;
      --ui-green: #57A773;
      --ui-green-dark: #157145;
      --ui-green-soft: rgba(87, 167, 115, .22);
      --ui-accent: #157145;
      --ui-graphite: #123246;
      --ui-indigo: #2E5A73;
      --ui-shadow: 0 18px 38px rgba(18, 50, 70, .16);
      --ui-card-shadow: 0 12px 28px rgba(18, 50, 70, .1);
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
      background:
        radial-gradient(circle at 12% 0%, rgba(155, 209, 229, .72), transparent 32%),
        radial-gradient(circle at 88% 12%, rgba(87, 167, 115, .28), transparent 30%),
        linear-gradient(135deg, #D1FAFF 0%, #F7FDFF 48%, #EAF7F0 100%),
        var(--ui-bg);
      color: var(--ui-text);
      font-family: "Poppins", "Segoe UI", Arial, sans-serif;
    }

    .phone-shell {
      width: min(100%, 430px);
      min-height: 100vh;
      margin: 0 auto;
      background: var(--ui-shell);
      box-shadow: 0 30px 80px rgba(34, 40, 60, .18);
      overflow: hidden;
    }

    .app-topbar {
      z-index: 10;
      display: grid;
      grid-template-columns: 44px minmax(0, 1fr) auto;
      gap: 10px;
      align-items: center;
      min-height: 84px;
      padding: 14px 16px 12px;
      background:
        linear-gradient(145deg, rgba(18, 50, 70, .98), rgba(106, 142, 174, .94)),
        var(--ui-graphite);
      color: #fff;
      box-shadow: var(--ui-shadow);
    }

    .icon-btn,
    .icon-btn:hover,
    .icon-btn:focus {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 42px;
      height: 42px;
      border: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, .1);
      color: #fff;
      cursor: pointer;
      text-decoration: none;
      box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .12);
    }

    .icon-btn:hover {
      background: rgba(255, 255, 255, .18);
    }

    .top-title {
      min-width: 0;
    }

    .top-title h1 {
      margin: 0;
      color: #fff;
      font-size: 18px;
      line-height: 1.2;
      font-weight: 800;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .top-title p {
      margin: 2px 0 0;
      color: rgba(255, 255, 255, .68);
      font-size: 12px;
      line-height: 1.25;
      font-weight: 600;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .top-actions {
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .brand-strip {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      align-items: center;
      gap: 12px;
      margin: 16px 18px 2px;
      padding: 14px;
      border: 1px solid rgba(106, 142, 174, .24);
      border-radius: 20px;
      background:
        linear-gradient(135deg, rgba(255, 255, 255, .94), rgba(209, 250, 255, .48));
      box-shadow: var(--ui-card-shadow);
    }

    .brand-mark {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 78px;
      height: 32px;
      padding: 0 10px;
      border: 2px solid var(--ui-graphite);
      border-radius: 8px;
      background: #fff;
      color: var(--ui-graphite);
      font-size: 11px;
      font-weight: 900;
      letter-spacing: 0;
      box-shadow: 0 7px 14px rgba(34, 40, 60, .16);
    }

    .brand-mark span {
      color: var(--ui-green-dark);
      margin-right: 2px;
    }

    .online-state {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      color: var(--ui-green-dark);
      font-size: 12px;
      font-weight: 900;
    }

    .online-state::before {
      content: "";
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--ui-green-dark);
      box-shadow: 0 0 0 5px rgba(4, 147, 66, .12);
    }

    .brand-copy {
      min-width: 0;
    }

    .brand-copy strong {
      display: block;
      color: var(--ui-text);
      font-size: 14px;
      line-height: 1.2;
      font-weight: 900;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .slide-hero {
      position: relative;
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 12px;
      align-items: end;
      min-height: 112px;
      margin-bottom: 14px;
      padding: 18px;
      border-radius: 24px;
      background:
        linear-gradient(145deg, rgba(18, 50, 70, .98), rgba(106, 142, 174, .9)),
        var(--ui-graphite);
      color: #fff;
      box-shadow: var(--ui-shadow);
      overflow: hidden;
    }

    .slide-hero::after {
      content: "";
      position: absolute;
      right: -36px;
      top: -36px;
      width: 112px;
      height: 112px;
      border-radius: 50%;
      border: 24px solid rgba(209, 250, 255, .16);
    }

    .slide-hero-content {
      position: relative;
      z-index: 1;
      min-width: 0;
    }

    .slide-hero-kicker {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 8px;
      color: rgba(255, 255, 255, .72);
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .slide-hero h2 {
      margin: 0;
      color: #fff;
      font-size: 22px;
      line-height: 1.1;
      font-weight: 900;
      overflow-wrap: anywhere;
    }

    .slide-hero p {
      margin: 6px 0 0;
      color: rgba(255, 255, 255, .68);
      font-size: 12px;
      font-weight: 700;
    }

    .slide-count {
      position: relative;
      z-index: 1;
      display: inline-flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      width: 58px;
      height: 58px;
      border-radius: 20px;
      background: rgba(255, 255, 255, .12);
      box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .14);
    }

    .slide-count strong {
      color: #fff;
      font-size: 20px;
      line-height: 1;
      font-weight: 900;
    }

    .slide-count span {
      margin-top: 4px;
      color: rgba(255, 255, 255, .68);
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .brand-copy span {
      display: block;
      margin-top: 2px;
      color: var(--ui-muted);
      font-size: 11px;
      font-weight: 700;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .slider-shell {
      padding: 4px 0 18px;
      background: transparent;
    }

    .slide-window {
      overflow: hidden;
      width: 100%;
    }

    .slide-track {
      display: flex;
      transition: transform .28s ease;
      will-change: transform;
    }

    .page-slide {
      flex: 0 0 100%;
      min-width: 100%;
      padding: 18px 18px 4px;
    }

    .device-card {
      min-height: 50px;
      padding: 15px 16px;
      border: 1px solid var(--ui-border);
      border-radius: 18px;
      background: var(--ui-surface);
      box-shadow: var(--ui-card-shadow);
    }

    .name-card {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 10px;
      align-items: center;
    }

    .field-label {
      display: block;
      margin-bottom: 5px;
      color: var(--ui-muted);
      font-size: 11px;
      line-height: 1;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .02em;
    }

    .field-value {
      display: block;
      min-width: 0;
      color: var(--ui-text);
      font-size: 16px;
      line-height: 1.25;
      font-weight: 700;
      overflow-wrap: anywhere;
    }

    .edit-link,
    .edit-link:hover,
    .edit-link:focus {
      color: var(--ui-green);
      font-size: 12px;
      font-weight: 800;
      text-decoration: none;
    }

    .param-list {
      display: flex;
      flex-direction: column;
      gap: 14px;
      margin-top: 16px;
    }

    .param-card {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 12px;
      align-items: center;
      min-height: 66px;
      padding: 15px 16px;
      border: 1px solid var(--ui-border);
      border-radius: 18px;
      background:
        linear-gradient(180deg, #FFFFFF, #FAF8F4);
      box-shadow: var(--ui-card-shadow);
      position: relative;
      overflow: hidden;
    }

    .param-card::before {
      content: "";
      position: absolute;
      left: 0;
      top: 14px;
      bottom: 14px;
      width: 3px;
      border-radius: 0 999px 999px 0;
      background: linear-gradient(180deg, var(--ui-blue), var(--ui-green-dark));
    }

    .param-meta {
      display: flex;
      align-items: center;
      justify-content: center;
      min-width: 34px;
      height: 34px;
      padding: 0 8px;
      border-radius: 999px;
      background: rgba(87, 167, 115, .16);
      color: var(--ui-green-dark);
      font-size: 12px;
      font-weight: 900;
    }

    .toggle-visual {
      position: relative;
      width: 34px;
      height: 18px;
      border-radius: 999px;
      background: var(--ui-green-soft);
    }

    .toggle-visual::after {
      content: "";
      position: absolute;
      top: -1px;
      right: 0;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: var(--ui-green-dark);
      box-shadow: 0 4px 9px rgba(21, 113, 69, .24);
    }

    .action-dot {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      border: 3px solid var(--ui-green-dark);
      background: var(--ui-green-dark);
      box-shadow: inset 0 0 0 4px #fff, 0 7px 16px rgba(21, 113, 69, .22);
    }

    .range-visual {
      grid-column: 1 / -1;
      margin-top: 10px;
    }

    .range-line {
      position: relative;
      height: 3px;
      margin: 12px 8px 8px;
      border-radius: 999px;
      background: var(--ui-green-dark);
    }

    .range-line::before {
      content: "";
      position: absolute;
      left: -6px;
      top: -9px;
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: var(--ui-green-dark);
    }

    .range-values {
      display: flex;
      justify-content: space-between;
      color: var(--ui-text);
      font-size: 10px;
      font-weight: 600;
    }

    .progress-shell {
      margin: 0 18px 8px;
      height: 5px;
      border-radius: 999px;
      background: rgba(106, 142, 174, .18);
      overflow: hidden;
    }

    .progress-bar {
      width: 0;
      height: 100%;
      border-radius: inherit;
      background: linear-gradient(90deg, var(--ui-steel), var(--ui-green-dark));
      transition: width .28s ease;
    }

    .slider-controls {
      display: grid;
      grid-template-columns: 48px 1fr 48px;
      gap: 10px;
      align-items: center;
      margin: 6px 18px 0;
      padding: 12px;
      border: 1px solid var(--ui-border);
      border-radius: 20px;
      background: rgba(255, 255, 255, .72);
      box-shadow: var(--ui-card-shadow);
    }

    .slide-arrow {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 44px;
      height: 44px;
      border: 1px solid var(--ui-border);
      border-radius: 50%;
      background: var(--ui-graphite);
      color: #fff;
      cursor: pointer;
      box-shadow: 0 10px 20px rgba(34, 40, 60, .14);
    }

    .slide-arrow:disabled {
      color: #B8BDC7;
      background: #fff;
      cursor: not-allowed;
      opacity: .65;
    }

    .page-buttons {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      flex-wrap: wrap;
      min-width: 0;
    }

    .page-button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 34px;
      height: 34px;
      padding: 0 10px;
      border: 1px solid var(--ui-border);
      border-radius: 999px;
      background: #fff;
      color: var(--ui-text);
      font-size: 13px;
      font-weight: 900;
      cursor: pointer;
      box-shadow: var(--ui-card-shadow);
    }

    .page-button.is-active {
      border-color: var(--ui-green-dark);
      background: linear-gradient(135deg, var(--ui-green), var(--ui-green-dark));
      color: #fff;
    }

    .footer-actions {
      display: grid;
      grid-template-columns: 1fr;
      gap: 10px;
      padding: 14px 18px 24px;
    }

    .save-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 44px;
      border: 0;
      border-radius: 16px;
      background: linear-gradient(135deg, var(--ui-green), var(--ui-green-dark));
      color: #fff;
      font-size: 14px;
      font-weight: 900;
      cursor: pointer;
      box-shadow: 0 14px 28px rgba(4, 147, 66, .22);
    }

    .empty-ui {
      margin: 28px 18px;
      padding: 20px;
      border: 1px dashed var(--ui-border);
      border-radius: 12px;
      background: var(--ui-surface);
      color: var(--ui-muted);
      font-weight: 800;
      text-align: center;
    }

    .icon {
      width: 20px;
      height: 20px;
      flex: 0 0 20px;
      stroke: currentColor;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
      fill: none;
    }

    @media (min-width: 720px) {
      body {
        padding: 24px 0;
      }

      .phone-shell {
        min-height: calc(100vh - 48px);
        border-radius: 28px;
      }
    }
  </style>
</head>
<body>
  <main class="phone-shell">
    <header class="app-topbar">
      <button type="button" class="icon-btn" onclick="history.back()" aria-label="Back">
        <?= view_ui_icon('chevron-left') ?>
      </button>

      <div class="top-title">
        <h1 id="active-page-title"><?= esc(!empty($pages[0]['page_name']) ? $pages[0]['page_name'] : ($modelno ?: 'Model UI')) ?></h1>
        <p><?= esc($modelno ?: 'Model UI') ?></p>
        <p id="active-page-subtitle"><?= $is_row_view ? 'Selected Row UI' : (!empty($pages[0]['page_no']) ? 'Page' . esc($pages[0]['page_no']) . '.Operation' : 'No pages') ?></p>
      </div>

      <div class="top-actions">
        <button type="button" class="icon-btn" aria-label="Information">
          <?= view_ui_icon('info') ?>
        </button>
        <a class="icon-btn" href="<?= get_uri('items/api_page_linking/' . (int) $modal_id . '?modelno=' . rawurlencode($modelno)) ?>" aria-label="Edit UI">
          <?= view_ui_icon('settings') ?>
        </a>
      </div>
    </header>

    <section class="brand-strip">
      <div class="brand-mark"><span>E</span>factor</div>
      <div class="brand-copy">
        <strong><?= esc($modelno ?: 'Linked UI') ?></strong>
        <span><?= $is_row_view ? 'Row preview' : count($pages) . ' linked page' . (count($pages) === 1 ? '' : 's') ?></span>
      </div>
      <div class="online-state">Online</div>
    </section>

    <?php if (empty($pages)): ?>
      <div class="empty-ui">No UI parameters selected yet.</div>
    <?php else: ?>
      <section class="slider-shell" aria-label="Linked page slider">
        <div class="slide-window">
          <div class="slide-track" id="slide-track">
            <?php foreach ($pages as $index => $page): ?>
              <article class="page-slide" data-page-title="<?= esc($page['page_name']) ?>" data-page-no="<?= esc($page['page_no']) ?>">
                <div class="slide-hero">
                  <div class="slide-hero-content">
                    <div class="slide-hero-kicker"><?= view_ui_icon('signal') ?> Live Control Page</div>
                    <h2><?= esc($page['page_name']) ?></h2>
                    <p><?= esc(count($page['params'])) ?> controls linked to Page <?= esc($page['page_no']) ?></p>
                  </div>
                  <div class="slide-count">
                    <strong><?= esc($index + 1) ?></strong>
                    <span>Page</span>
                  </div>
                </div>

                <div class="device-card name-card">
                  <div>
                    <span class="field-label">name</span>
                    <span class="field-value"><?= esc($page['page_name']) ?></span>
                  </div>
                  <a class="edit-link" href="<?= get_uri('items/api_page_linking/' . (int) $modal_id . '?modelno=' . rawurlencode($modelno)) ?>">Edit</a>
                </div>

                <div class="param-list">
                  <?php foreach ($page['params'] as $paramIndex => $param): ?>
                    <?php
                    $paramName = trim((string) ($param['parameter'] ?? ''));
                    $paramLower = strtolower($paramName);
                    $isPower = strpos($paramLower, 'power') !== false;
                    $isTimer = strpos($paramLower, 'timer') !== false;
                    $isTrigger = strpos($paramLower, 'trigger') !== false || strpos($paramLower, 'button') !== false;
                    ?>
                    <div class="param-card">
                      <div>
                        <span class="field-label"><?= esc($paramName ?: 'Parameter') ?></span>
                        <span class="field-value"><?= $isPower ? 'ON' : esc($paramName ?: 'N/A') ?></span>
                      </div>

                      <?php if ($isPower): ?>
                        <span class="toggle-visual" aria-hidden="true"></span>
                      <?php elseif ($isTrigger): ?>
                        <span class="action-dot" aria-hidden="true"></span>
                      <?php else: ?>
                        <span class="param-meta"><?= esc($param['sequence']) ?></span>
                      <?php endif; ?>

                      <?php if ($isTimer): ?>
                        <div class="range-visual">
                          <div class="range-values">
                            <span>0</span>
                            <span>255</span>
                          </div>
                          <div class="range-line"></div>
                          <div class="range-values">
                            <span>0</span>
                            <span>255</span>
                          </div>
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="progress-shell" aria-hidden="true">
          <div class="progress-bar" id="slide-progress"></div>
        </div>

        <div class="slider-controls">
          <button type="button" class="slide-arrow" id="prev-slide" aria-label="Previous page">
            <?= view_ui_icon('arrow-left') ?>
          </button>

          <div class="page-buttons" aria-label="Page buttons">
            <?php foreach ($pages as $index => $page): ?>
              <button type="button" class="page-button<?= $index === 0 ? ' is-active' : '' ?>" data-slide-index="<?= esc($index) ?>">
                <?= esc($index + 1) ?>
              </button>
            <?php endforeach; ?>
          </div>

          <button type="button" class="slide-arrow" id="next-slide" aria-label="Next page">
            <?= view_ui_icon('arrow-right') ?>
          </button>
        </div>
      </section>
    <?php endif; ?>

    <div class="footer-actions">
      <button type="button" class="save-btn" id="save-ui-btn"><?= view_ui_icon('save') ?> Save</button>
    </div>
  </main>

  <script>
    const track = document.getElementById('slide-track');
    const slides = Array.from(document.querySelectorAll('.page-slide'));
    const pageButtons = Array.from(document.querySelectorAll('.page-button'));
    const prevButton = document.getElementById('prev-slide');
    const nextButton = document.getElementById('next-slide');
    const progressBar = document.getElementById('slide-progress');
    const title = document.getElementById('active-page-title');
    const subtitle = document.getElementById('active-page-subtitle');
    let activeIndex = 0;

    function showSlide(index) {
      if (!slides.length) return;

      activeIndex = Math.max(0, Math.min(index, slides.length - 1));
      track.style.transform = 'translateX(-' + (activeIndex * 100) + '%)';

      pageButtons.forEach((button, buttonIndex) => {
        button.classList.toggle('is-active', buttonIndex === activeIndex);
      });

      if (prevButton) prevButton.disabled = activeIndex === 0;
      if (nextButton) nextButton.disabled = activeIndex === slides.length - 1;
      if (progressBar) progressBar.style.width = (((activeIndex + 1) / slides.length) * 100) + '%';

      const activeSlide = slides[activeIndex];
      if (activeSlide) {
        title.textContent = activeSlide.dataset.pageTitle || 'Model UI';
        subtitle.textContent = 'Page' + (activeSlide.dataset.pageNo || (activeIndex + 1)) + '.Operation';
      }
    }

    if (prevButton) {
      prevButton.addEventListener('click', function() {
        showSlide(activeIndex - 1);
      });
    }

    if (nextButton) {
      nextButton.addEventListener('click', function() {
        showSlide(activeIndex + 1);
      });
    }

    pageButtons.forEach(function(button) {
      button.addEventListener('click', function() {
        showSlide(parseInt(button.dataset.slideIndex || '0', 10));
      });
    });

    document.getElementById('save-ui-btn').addEventListener('click', function() {
      alert('UI saved successfully.');
      window.location.href = '<?= get_uri('items/vendor') ?>';
    });

    showSlide(0);
  </script>
</body>
</html>
