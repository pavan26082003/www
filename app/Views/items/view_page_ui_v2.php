<?php
$modelno = $modelno ?? '';
$modal_id = $modal_id ?? 0;
$rows = !empty($model_data['rows']) && is_array($model_data['rows']) ? $model_data['rows'] : [];
$is_row_view = !empty($is_row_view);
$schema_sync = !empty($schema_sync);

$pages = [];
$savedPageMap = [];
if (!empty($model_data['page_names_array']) && is_array($model_data['page_names_array'])) {
    $savedPageMap = $model_data['page_names_array'];
} elseif (!empty($model_data['page_names'])) {
    $decodedPageMap = json_decode((string) $model_data['page_names'], true);
    if (is_array($decodedPageMap)) {
        $savedPageMap = $decodedPageMap;
    }
}

foreach ($savedPageMap as $savedPageNo => $savedPageName) {
    $savedPageNo = trim((string) $savedPageNo);
    if ($savedPageNo === '') {
        continue;
    }
    $pages[$savedPageNo] = [
        'page_no' => $savedPageNo,
        'page_name' => trim((string) $savedPageName) !== '' ? (string) $savedPageName : ('Page ' . $savedPageNo),
        'params' => [],
    ];
}

$knownPageNumbers = [];
foreach ($rows as $row) {
    $rawPageNo = trim((string) ($row['page_no'] ?? ''));
    if ($rawPageNo !== '') {
        $knownPageNumbers[$rawPageNo] = true;
    }
}

$nextGeneratedPageNo = 1;
if (!empty($knownPageNumbers)) {
    $numericPageNos = array_map('intval', array_keys($knownPageNumbers));
    $nextGeneratedPageNo = max($numericPageNos) + 1;
}
$generatedPageMap = [];

foreach ($rows as $index => $row) {
    $pageNo = trim((string) ($row['page_no'] ?? ''));
    if ($pageNo === '') {
        $pageName = trim((string) ($row['page_name'] ?? ''));
        $fallbackKey = $pageName !== '' ? strtolower($pageName) : ('row_' . $index);
        if (!isset($generatedPageMap[$fallbackKey])) {
            while (isset($knownPageNumbers[(string) $nextGeneratedPageNo])) {
                $nextGeneratedPageNo++;
            }
            $generatedPageMap[$fallbackKey] = (string) $nextGeneratedPageNo;
            $knownPageNumbers[(string) $nextGeneratedPageNo] = true;
            $nextGeneratedPageNo++;
        }
        $pageNo = $generatedPageMap[$fallbackKey];
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
        'api' => !empty($row['api']) && is_array($row['api']) ? $row['api'] : [],
        'buttons' => [],
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

$default_ui_theme = [
    'themeMode' => 'pro',
    'backgroundType' => 'gradient',
    'backgroundColor' => '#F8FAFC',
    'gradientColor' => '#EEF2F7',
    'shellColor' => '#F8FAFC',
    'cardColor' => '#FFFFFF',
    'textColor' => '#0F172A',
    'mutedTextColor' => '#64748B',
    'topbarColor' => '#0F172A',
    'accentColor' => '#2563EB',
    'secondaryColor' => '#94A3B8',
    'toggleOnColor' => '#2563EB',
    'toggleOffColor' => '#CBD5E1',
    'iconStyle' => 'signal',
    'parameterIcon' => 'gauge',
    'parameterIcons' => (object) [],
    'fontFamily' => 'Inter',
    'fontSize' => 15,
    'fontWeight' => 500,
    'letterSpacing' => -0.2,
    'lineHeight' => 1.5,
    'textAlign' => 'left',
    'buttonBgColor' => '#2563EB',
    'buttonTextColor' => '#FFFFFF',
    'successColor' => '#10B981',
    'warningColor' => '#F59E0B',
    'errorColor' => '#EF4444',
    'iconSize' => 18,
    'iconColor' => '#2563EB',
    'headerHeight' => 72,
    'headerText' => '',
    'headerAlignment' => 'left',
    'showHeaderIcons' => '1',
    'showStatus' => '1',
    'borderSize' => 1,
    'borderColor' => '#E2E8F0',
    'borderRadius' => 16,
    'shadowStrength' => 42,
    'cardBlur' => 0,
    'pagePadding' => 16,
    'cardPadding' => 14,
    'cardMargin' => 0,
    'cardSpacing' => 12,
    'buttonText' => 'Save Changes',
    'buttonSize' => 48,
    'buttonRadius' => 14,
    'buttonHover' => 'lift',
    'buttonIconPosition' => 'left',
    'buttonTarget' => 'save',
    'brandVisible' => '1',
    'heroVisible' => '1',
    'footerVisible' => '1',
    'pageNavigatorVisible' => '1',
    'nameCardVisible' => '1',
    'deviceMode' => 'mobile',
    'pageType' => 'Control Page',
    'headerStyle' => 'Solid',
    'componentOverrides' => [],
    'controlOrder' => [],
];

$saved_ui_theme = !empty($ui_theme['theme']) && is_array($ui_theme['theme']) ? $ui_theme['theme'] : [];
$active_ui_theme = array_merge($default_ui_theme, $saved_ui_theme);
$saved_ui_schema = !empty($ui_theme['schema']) && is_array($ui_theme['schema']) ? $ui_theme['schema'] : [];

if (!function_exists('view_ui_icon')) {
    function view_ui_icon($name)
    {
        $icons = [
            'arrow-left' => '<path d="M19 12H5"></path><path d="m12 19-7-7 7-7"></path>',
            'arrow-right' => '<path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path>',
            'bolt' => '<path d="M13 2 3 14h8l-1 8 11-14h-8l1-6z"></path>',
            'code' => '<path d="m16 18 6-6-6-6"></path><path d="m8 6-6 6 6 6"></path>',
            'chevron-left' => '<path d="m15 18-6-6 6-6"></path>',
            'chevron-right' => '<path d="m9 18 6-6-6-6"></path>',
            'clock' => '<circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path>',
            'droplet' => '<path d="M12 2.69 5.66 9.03a9 9 0 1 0 12.68 0L12 2.69z"></path>',
            'edit' => '<path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>',
            'fan' => '<path d="M10.8 14.8a3 3 0 1 0 2.4-5.6"></path><path d="M12 12c-2-4-2-7 0-9 3 2 3 5 0 9Z"></path><path d="M12 12c4-2 7-2 9 0-2 3-5 3-9 0Z"></path><path d="M12 12c2 4 2 7 0 9-3-2-3-5 0-9Z"></path><path d="M12 12c-4 2-7 2-9 0 2-3 5-3 9 0Z"></path>',
            'file' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path>',
            'gauge' => '<path d="m12 14 4-4"></path><path d="M3.34 19a10 10 0 1 1 17.32 0"></path>',
            'heart' => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>',
            'info' => '<circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path>',
            'layers' => '<path d="m12 2 10 5-10 5L2 7l10-5z"></path><path d="m2 17 10 5 10-5"></path><path d="m2 12 10 5 10-5"></path>',
            'lightbulb' => '<path d="M15 14c.2-1 .7-1.7 1.4-2.5A6 6 0 1 0 7.6 11.5C8.3 12.3 8.8 13 9 14"></path><path d="M9 18h6"></path><path d="M10 22h4"></path><path d="M10 14h4v4h-4z"></path>',
            'lock' => '<rect width="18" height="11" x="3" y="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path>',
            'move' => '<path d="M12 2v20"></path><path d="m15 5-3-3-3 3"></path><path d="m15 19-3 3-3-3"></path><path d="M2 12h20"></path><path d="m5 9-3 3 3 3"></path><path d="m19 9 3 3-3 3"></path>',
            'minus' => '<line x1="5" x2="19" y1="12" y2="12"></line>',
            'maximize' => '<path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>',
            'plus' => '<line x1="12" x2="12" y1="5" y2="19"></line><line x1="5" x2="19" y1="12" y2="12"></line>',
            'palette' => '<circle cx="13.5" cy="6.5" r=".5"></circle><circle cx="17.5" cy="10.5" r=".5"></circle><circle cx="8.5" cy="7.5" r=".5"></circle><circle cx="6.5" cy="12.5" r=".5"></circle><path d="M12 2a10 10 0 0 0 0 20 2 2 0 0 0 2-2 1.5 1.5 0 0 1 1.5-1.5H17a5 5 0 0 0 5-5.3A10 10 0 0 0 12 2z"></path>',
            'power' => '<path d="M12 2v10"></path><path d="M18.4 6.6a9 9 0 1 1-12.8 0"></path>',
            'save' => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><path d="M17 21v-8H7v8"></path><path d="M7 3v5h8"></path>',
            'settings' => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.52a2 2 0 0 1-1 1.72l-.15.1a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1-1-1.72v-.52a2 2 0 0 1 1-1.72l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle>',
            'signal' => '<path d="M2 20h.01"></path><path d="M7 20a5 5 0 0 1 10 0"></path><path d="M4 14a11 11 0 0 1 16 0"></path><path d="M2 8a16 16 0 0 1 20 0"></path>',
            'smartphone' => '<rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect><path d="M12 18h.01"></path>',
            'thermometer' => '<path d="M14 14.76V5a2 2 0 1 0-4 0v9.76a4 4 0 1 0 4 0Z"></path>',
            'wifi' => '<path d="M5 13a10 10 0 0 1 14 0"></path><path d="M8.5 16.5a5 5 0 0 1 7 0"></path><path d="M2 8.5a15 15 0 0 1 20 0"></path><path d="M12 20h.01"></path>',
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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --ui-bg: #F8FAFC;
      --ui-shell: #F8FAFC;
      --ui-surface: #FFFFFF;
      --ui-surface-2: #F1F5F9;
      --ui-text: #0F172A;
      --ui-muted: #64748B;
      --ui-border: #E2E8F0;
      --ui-cyan: #E0F2FE;
      --ui-blue: #94A3B8;
      --ui-steel: #64748B;
      --ui-green: #10B981;
      --ui-green-dark: #2563EB;
      --ui-green-soft: rgba(37, 99, 235, .12);
      --ui-accent: #2563EB;
      --ui-graphite: #0F172A;
      --ui-indigo: #334155;
      --ui-shadow: 0 4px 20px rgba(15, 23, 42, .08);
      --ui-card-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 6px 18px rgba(15, 23, 42, .06);
      --ui-radius: 16px;
      --ui-shadow-alpha: .042;
      --ui-toggle-on: #2563EB;
      --ui-toggle-off: #CBD5E1;
      --ui-font: "Inter", "Segoe UI", system-ui, -apple-system, sans-serif;
      --ui-font-size: 15px;
      --ui-font-weight: 500;
      --ui-letter-spacing: -0.2px;
      --ui-line-height: 1.5;
      --ui-text-align: left;
      --ui-button-bg: #2563EB;
      --ui-button-text: #FFFFFF;
      --ui-success: #10B981;
      --ui-warning: #F59E0B;
      --ui-error: #EF4444;
      --ui-icon-size: 18px;
      --ui-icon-color: #2563EB;
      --ui-header-height: 72px;
      --ui-border-size: 1px;
      --ui-border-color: #E2E8F0;
      --ui-card-blur: 0px;
      --ui-page-padding: 16px;
      --ui-card-padding: 14px;
      --ui-card-spacing: 12px;
      --ui-button-height: 48px;
      --ui-button-radius: 14px;
      --studio-phone-width: 375px;
      --studio-phone-height: 812px;
      --studio-canvas: #44403C;
      --studio-canvas-grid: rgba(255, 255, 255, .07);
      --studio-canvas-border: rgba(255, 255, 255, .1);
    }

    .phone-shell {
      --ui-bg: <?= esc($active_ui_theme['backgroundColor']) ?>;
      --ui-shell: <?= esc($active_ui_theme['shellColor']) ?>;
      --ui-surface: <?= esc($active_ui_theme['cardColor']) ?>;
      --ui-text: <?= esc($active_ui_theme['textColor']) ?>;
      --ui-muted: <?= esc($active_ui_theme['mutedTextColor']) ?>;
      --ui-blue: <?= esc($active_ui_theme['secondaryColor']) ?>;
      --ui-green-dark: <?= esc($active_ui_theme['accentColor']) ?>;
      --ui-accent: <?= esc($active_ui_theme['accentColor']) ?>;
      --ui-graphite: <?= esc($active_ui_theme['topbarColor']) ?>;
      --ui-radius: <?= (int) $active_ui_theme['borderRadius'] ?>px;
      --ui-shadow-alpha: <?= max(0, min(100, (int) $active_ui_theme['shadowStrength'])) / 1000 ?>;
      --ui-toggle-on: <?= esc($active_ui_theme['toggleOnColor']) ?>;
      --ui-toggle-off: <?= esc($active_ui_theme['toggleOffColor']) ?>;
      --ui-font: "<?= esc($active_ui_theme['fontFamily']) ?>", "Segoe UI", Arial, sans-serif;
      --ui-font-size: <?= (int) $active_ui_theme['fontSize'] ?>px;
      --ui-font-weight: <?= (int) $active_ui_theme['fontWeight'] ?>;
      --ui-letter-spacing: <?= (float) $active_ui_theme['letterSpacing'] ?>px;
      --ui-line-height: <?= (float) $active_ui_theme['lineHeight'] ?>;
      --ui-text-align: <?= esc($active_ui_theme['textAlign']) ?>;
      --ui-button-bg: <?= esc($active_ui_theme['buttonBgColor']) ?>;
      --ui-button-text: <?= esc($active_ui_theme['buttonTextColor']) ?>;
      --ui-success: <?= esc($active_ui_theme['successColor']) ?>;
      --ui-warning: <?= esc($active_ui_theme['warningColor']) ?>;
      --ui-error: <?= esc($active_ui_theme['errorColor']) ?>;
      --ui-icon-size: <?= (int) $active_ui_theme['iconSize'] ?>px;
      --ui-icon-color: <?= esc($active_ui_theme['iconColor']) ?>;
      --ui-header-height: <?= (int) $active_ui_theme['headerHeight'] ?>px;
      --ui-border-size: <?= (int) $active_ui_theme['borderSize'] ?>px;
      --ui-border-color: <?= esc($active_ui_theme['borderColor']) ?>;
      --ui-card-blur: <?= (int) $active_ui_theme['cardBlur'] ?>px;
      --ui-page-padding: <?= (int) $active_ui_theme['pagePadding'] ?>px;
      --ui-card-padding: <?= (int) $active_ui_theme['cardPadding'] ?>px;
      --ui-card-spacing: <?= (int) $active_ui_theme['cardSpacing'] ?>px;
      --ui-button-height: <?= (int) $active_ui_theme['buttonSize'] ?>px;
      --ui-button-radius: <?= (int) $active_ui_theme['buttonRadius'] ?>px;
    }

    .desktop-flow-actions { display: none; }

    @media (min-width: 720px) {
      .desktop-flow-actions {
        position: fixed; top: 24px; left: 24px; z-index: 70;
        display: flex; gap: 10px;
      }
      .desktop-flow-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 7px;
        min-height: 42px; padding: 0 16px; border: 1px solid rgba(18, 50, 70, .18);
        border-radius: 12px; background: #fff; color: #123246; font: inherit;
        font-weight: 900; text-decoration: none; cursor: pointer;
        box-shadow: 0 10px 28px rgba(34, 40, 60, .14);
      }
      .desktop-flow-btn svg { width: 17px; height: 17px; }
      .desktop-flow-studio { border-color: #C2410C; background: #C2410C; color: #fff; }
    }

    .page-preview-shell {
      display: flex;
      flex-direction: column;
      height: 100vh;
      height: 100dvh;
      min-height: 100vh;
      min-height: 100dvh;
      overflow: hidden;
    }

    body.studio-open .page-preview-shell {
      display: none;
    }

    .page-preview-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex: 0 0 auto;
      padding: 10px 14px;
      color: #E2E8F0;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: .06em;
      text-transform: uppercase;
      background: rgba(15, 23, 42, .88);
      backdrop-filter: blur(10px);
    }

    .page-preview-head-tools {
      display: flex;
      align-items: center;
      gap: 10px;
      min-width: 0;
    }

    .page-preview-title {
      white-space: nowrap;
    }

    .page-preview-size {
      padding: 4px 10px;
      border: 1px solid rgba(255, 255, 255, .16);
      border-radius: 999px;
      background: rgba(15, 23, 42, .28);
      color: #F1F5F9;
      font-size: 10px;
      font-weight: 600;
      letter-spacing: .04em;
      text-transform: none;
      white-space: nowrap;
    }

    .page-preview-mount {
      display: flex;
      align-items: flex-start;
      justify-content: center;
      flex: 1 1 auto;
      width: 100%;
      min-height: 0;
      overflow-x: hidden;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
      overscroll-behavior: contain;
      touch-action: pan-y;
      scrollbar-width: thin;
      scrollbar-color: color-mix(in srgb, var(--ui-accent) 70%, #64748B) rgba(15, 23, 42, .12);
    }

    .page-preview-mount::-webkit-scrollbar,
    .studio-preview-mount::-webkit-scrollbar {
      width: 10px;
    }

    .page-preview-mount::-webkit-scrollbar-track,
    .studio-preview-mount::-webkit-scrollbar-track {
      background: rgba(15, 23, 42, .08);
      border-radius: 999px;
      margin: 4px 0;
    }

    .page-preview-mount::-webkit-scrollbar-thumb,
    .studio-preview-mount::-webkit-scrollbar-thumb {
      border-radius: 999px;
      border: 2px solid transparent;
      background: linear-gradient(180deg, color-mix(in srgb, var(--ui-accent) 82%, #FFFFFF), color-mix(in srgb, var(--ui-accent) 55%, #0F2550));
      background-clip: padding-box;
    }

    .page-preview-mount::-webkit-scrollbar-thumb:hover,
    .studio-preview-mount::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(180deg, var(--ui-accent), color-mix(in srgb, var(--ui-accent) 70%, #0F2550));
      background-clip: padding-box;
    }

    .page-preview-frame {
      position: relative;
      flex: 0 0 auto;
      width: calc(var(--studio-phone-width) + 20px);
      max-width: calc(var(--studio-phone-width) + 20px);
      min-height: calc(var(--studio-phone-height) + 20px);
      transform-origin: top center;
    }

    .page-preview-frame > .phone-shell {
      width: var(--studio-phone-width);
      max-width: var(--studio-phone-width);
      height: var(--studio-phone-height);
      max-height: var(--studio-phone-height);
      min-height: var(--studio-phone-height);
    }

    @media (min-width: 720px) {
      body {
        padding: 0;
        overflow: hidden;
      }

      .page-preview-shell {
        min-height: 100vh;
        min-height: 100dvh;
        background-color: var(--studio-canvas);
        background-image:
          radial-gradient(circle at 50% 0%, rgba(255, 255, 255, .08), transparent 40%),
          radial-gradient(circle at 50% 100%, rgba(15, 23, 42, .22), transparent 42%),
          radial-gradient(var(--studio-canvas-grid) 1px, transparent 1px);
        background-size: auto, auto, 20px 20px;
      }

      .page-preview-head {
        justify-content: space-between;
        width: calc(100% - 48px);
        max-width: calc(var(--studio-phone-width) + 80px);
        margin: 72px auto 14px;
        padding: 0;
        background: transparent;
        backdrop-filter: none;
      }

      .page-preview-head-tools {
        gap: 12px;
      }

      .page-preview-mount {
        padding: 8px 24px 24px;
      }

      .page-preview-frame {
        width: calc(var(--studio-phone-width) + 20px);
        max-width: calc(var(--studio-phone-width) + 20px);
        padding: 10px;
        border-radius: 42px;
        background: linear-gradient(160deg, #4B5563 0%, #1F2937 52%, #111827 100%);
        box-shadow:
          0 0 0 1px rgba(255, 255, 255, .14),
          0 2px 0 rgba(255, 255, 255, .08) inset,
          0 40px 80px -20px rgba(0, 0, 0, .55);
      }

      .page-preview-frame::before {
        content: "";
        position: absolute;
        top: 16px;
        left: 50%;
        z-index: 2;
        transform: translateX(-50%);
        width: 92px;
        height: 24px;
        border-radius: 999px;
        background: #000000;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .08);
        pointer-events: none;
      }

      .page-preview-frame .phone-shell {
        width: var(--studio-phone-width);
        max-width: var(--studio-phone-width);
        height: var(--studio-phone-height);
        max-height: var(--studio-phone-height);
        min-height: var(--studio-phone-height);
        border-radius: 32px;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .04);
      }
    }

    @media (max-width: 719px) {
      .page-preview-head {
        position: sticky;
        top: 0;
        z-index: 60;
        flex: 0 0 auto;
      }

      .page-preview-size {
        display: none;
      }

      .page-preview-mount {
        flex: 1 1 auto;
        min-height: 0;
      }

      .page-preview-frame {
        width: 100%;
        max-width: min(100%, calc(var(--studio-phone-width) + 20px));
        min-height: 0;
      }

      .page-preview-frame > .phone-shell {
        width: min(100%, var(--studio-phone-width));
        max-width: var(--studio-phone-width);
        height: calc(100dvh - 52px);
        max-height: calc(100dvh - 52px);
        min-height: calc(100dvh - 52px);
      }
    }

    * { box-sizing: border-box; }

    .ui-studio,
    .icon-picker,
    .component-picker,
    .save-toast {
      --studio-accent: #C2410C;
      --studio-accent-hover: #EA580C;
      --studio-accent-soft: rgba(194, 65, 12, .14);
      --studio-accent-ring: rgba(194, 65, 12, .24);
      --studio-accent-text: #9A3412;
      --ui-bg: #F5F5F4;
      --ui-shell: #FAFAF9;
      --ui-surface: #FFFFFF;
      --ui-surface-2: #FAFAF9;
      --ui-text: #1C1917;
      --ui-muted: #78716C;
      --ui-border: #E7E5E4;
      --ui-blue: #A8A29E;
      --ui-steel: #78716C;
      --ui-green: #C2410C;
      --ui-green-dark: #9A3412;
      --ui-accent: var(--studio-accent);
      --ui-graphite: #292524;
      --ui-success: #15803D;
      --ui-warning: #D97706;
      --ui-error: #DC2626;
      --ui-font: "DM Sans", "Inter", "Segoe UI", system-ui, sans-serif;
      --ui-font-size: 13px;
      --ui-font-weight: 500;
      --ui-border-color: #E7E5E4;
      --ui-toggle-off: #D6D3D1;
      --studio-sidebar: #1C1917;
      --studio-sidebar-border: rgba(255, 255, 255, .08);
      --studio-sidebar-hover: rgba(255, 255, 255, .06);
      --studio-sidebar-active: var(--studio-accent-soft);
      --studio-sidebar-text: #A8A29E;
      --studio-sidebar-text-active: #FAFAF9;
      --studio-sidebar-kicker: #78716C;
      --studio-canvas: #44403C;
      --studio-canvas-grid: rgba(255, 255, 255, .07);
      --studio-canvas-border: rgba(255, 255, 255, .1);
      --studio-phone-width: 375px;
      --studio-phone-height: 812px;
      --studio-toolbar: #FFFFFF;
      --studio-toolbar-border: #E7E5E4;
      --studio-range-fill: var(--studio-accent);
      --studio-range-track: #D6D3D1;
      --studio-range-thumb: #EA580C;
    }

    .ui-studio {
      font-family: var(--ui-font);
      font-size: var(--ui-font-size);
      font-weight: var(--ui-font-weight);
      letter-spacing: -.01em;
      line-height: 1.45;
      background: var(--ui-bg);
    }

    body {
      margin: 0;
      min-height: 100vh;
      background:
        radial-gradient(circle at 12% 0%, rgba(251, 191, 36, .18), transparent 34%),
        radial-gradient(circle at 88% 12%, rgba(120, 113, 108, .14), transparent 30%),
        linear-gradient(135deg, #FAFAF9 0%, #F5F5F4 48%, #E7E5E4 100%);
      color: #292524;
      font-family: "Poppins", "Segoe UI", Arial, sans-serif;
      font-size: 14px;
      font-weight: 600;
      letter-spacing: 0;
      line-height: 1.4;
      text-align: left;
    }

    .phone-shell {
      width: min(100%, var(--studio-phone-width));
      height: var(--studio-phone-height);
      max-height: var(--studio-phone-height);
      min-height: var(--studio-phone-height);
      margin: 0 auto;
      background: var(--ui-bg);
      box-shadow: 0 30px 80px rgba(34, 40, 60, .18);
      display: grid;
      grid-template-rows: auto auto minmax(0, 1fr);
      overflow: hidden;
      color: var(--ui-text);
      font-family: var(--ui-font);
      font-size: var(--ui-font-size);
      font-weight: var(--ui-font-weight);
      letter-spacing: var(--ui-letter-spacing);
      line-height: var(--ui-line-height);
      text-align: var(--ui-text-align);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      text-rendering: optimizeLegibility;
    }

    /* Professional mobile app polish */
    .phone-shell .app-topbar {
      min-height: var(--ui-header-height);
      padding: 10px 16px 10px;
      border-bottom: 1px solid rgba(255, 255, 255, .08);
      background:
        linear-gradient(180deg, color-mix(in srgb, var(--ui-graphite) 92%, #000000) 0%, var(--ui-graphite) 100%);
      box-shadow: 0 1px 0 rgba(255, 255, 255, .05) inset, 0 8px 24px rgba(15, 23, 42, .14);
    }

    .phone-shell .top-title h1 {
      font-size: 17px;
      font-weight: 600;
      letter-spacing: -0.025em;
      line-height: 1.25;
    }

    .phone-shell .top-title p {
      margin: 1px 0 0;
      font-size: 11px;
      font-weight: 500;
      letter-spacing: 0.01em;
      color: rgba(255, 255, 255, .62);
    }

    .phone-shell .app-back-btn {
      grid-area: back;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 2px;
      width: auto;
      min-width: 36px;
      height: 36px;
      padding: 0 10px 0 6px;
      border: 0;
      border-radius: 999px;
      background: rgba(255, 255, 255, .1);
      color: #fff;
      font-family: inherit;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: -0.01em;
      cursor: pointer;
      box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .12);
      transition: background .18s ease, transform .14s ease;
    }

    .phone-shell .app-back-btn:hover,
    .phone-shell .app-back-btn:focus {
      background: rgba(255, 255, 255, .16);
      outline: none;
    }

    .phone-shell .app-back-btn:active {
      transform: scale(.96);
    }

    .phone-shell .app-back-btn .icon {
      width: 18px;
      height: 18px;
      stroke-width: 2.5;
    }

    .phone-shell .app-back-label {
      line-height: 1;
    }

    .phone-shell .icon-btn {
      width: 40px;
      height: 40px;
      background: rgba(255, 255, 255, .08);
      box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .1);
      transition: background .18s ease, transform .14s ease;
    }

    .phone-shell .icon-btn:hover {
      background: rgba(255, 255, 255, .14);
    }

    .phone-shell .icon-btn:active {
      transform: scale(.96);
    }

    .phone-shell .brand-strip {
      margin: 14px 16px 0;
      padding: 12px 14px;
      border-radius: calc(var(--ui-radius) + 2px);
      background: var(--ui-surface);
      border: 1px solid var(--ui-border-color);
      box-shadow: var(--ui-card-shadow);
    }

    .phone-shell .brand-mark {
      min-width: 72px;
      height: 30px;
      padding: 0 9px;
      border: 1.5px solid color-mix(in srgb, var(--ui-accent) 40%, var(--ui-border-color));
      border-radius: 8px;
      background: color-mix(in srgb, var(--ui-accent) 6%, var(--ui-surface));
      color: var(--ui-text);
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.04em;
      box-shadow: none;
    }

    .phone-shell .brand-mark span {
      color: var(--ui-accent);
      font-weight: 800;
    }

    .phone-shell .brand-copy strong {
      font-size: 13px;
      font-weight: 600;
      letter-spacing: -0.02em;
    }

    .phone-shell .brand-copy span {
      font-size: 11px;
      font-weight: 500;
      color: var(--ui-muted);
    }

    .phone-shell .online-state {
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.02em;
    }

    .phone-shell .slide-hero {
      min-height: 104px;
      margin-bottom: 12px;
      padding: 16px;
      border-radius: calc(var(--ui-radius) + 4px);
      background:
        linear-gradient(135deg, var(--ui-graphite) 0%, color-mix(in srgb, var(--ui-graphite) 72%, var(--ui-accent)) 100%);
      box-shadow: 0 10px 28px rgba(15, 23, 42, .16);
    }

    .phone-shell .slide-hero::after {
      border-color: rgba(255, 255, 255, .08);
    }

    .phone-shell .slide-hero-kicker {
      margin-bottom: 6px;
      font-size: 10px;
      font-weight: 600;
      letter-spacing: 0.1em;
      color: rgba(255, 255, 255, .58);
    }

    .phone-shell .slide-hero h2 {
      font-size: 20px;
      font-weight: 600;
      letter-spacing: -0.03em;
      line-height: 1.15;
    }

    .phone-shell .slide-hero p {
      font-size: 12px;
      font-weight: 500;
      color: rgba(255, 255, 255, .62);
    }

    .phone-shell .slide-count {
      width: 54px;
      height: 54px;
      border-radius: 16px;
      background: rgba(255, 255, 255, .1);
    }

    .phone-shell .slide-count strong {
      font-size: 18px;
      font-weight: 700;
    }

    .phone-shell .slide-count span {
      font-size: 9px;
      font-weight: 600;
      letter-spacing: 0.08em;
    }

    .phone-shell .page-slide {
      padding-bottom: 24px;
    }

    .phone-shell .device-card,
    .phone-shell .param-card {
      background: var(--ui-surface);
      border: 1px solid var(--ui-border-color);
      box-shadow: var(--ui-card-shadow);
      backdrop-filter: blur(var(--ui-card-blur));
    }

    .phone-shell .param-card {
      min-height: 60px;
      gap: 10px;
    }

    .phone-shell .param-card.layout-stacked {
      display: flex;
      flex-direction: column;
      align-items: stretch;
      min-height: 0;
      overflow: visible;
      padding-bottom: calc(var(--ui-card-padding) + 4px);
    }

    .phone-shell .param-card.layout-stacked > .param-main-row {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      gap: 12px;
      align-items: center;
      width: 100%;
      min-width: 0;
    }

    .phone-shell .param-card::before {
      top: 12px;
      bottom: 12px;
      width: 2px;
      background: var(--ui-accent);
      opacity: .88;
    }

    .phone-shell .field-label {
      margin-bottom: 4px;
      font-size: 10px;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--ui-muted);
    }

    .phone-shell .field-value {
      font-size: 15px;
      font-weight: 600;
      letter-spacing: -0.015em;
      line-height: 1.35;
      color: var(--ui-text);
    }

    .phone-shell .edit-link {
      font-size: 13px;
      font-weight: 600;
      color: var(--ui-accent);
    }

    .phone-shell .param-icon {
      width: 36px;
      height: 36px;
      background: color-mix(in srgb, var(--ui-accent) 10%, var(--ui-surface));
      box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--ui-accent) 14%, transparent);
    }

    .phone-shell .param-meta {
      min-width: 40px;
      height: 32px;
      font-size: 11px;
      font-weight: 600;
      background: color-mix(in srgb, var(--ui-accent) 10%, var(--ui-surface));
      color: var(--ui-accent);
      box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--ui-accent) 16%, transparent);
    }

    .phone-shell .param-meta:hover,
    .phone-shell .param-meta:focus {
      transform: none;
      background: color-mix(in srgb, var(--ui-accent) 16%, var(--ui-surface));
    }

    .phone-shell .progress-shell {
      margin: 0 16px 6px;
      height: 3px;
      background: color-mix(in srgb, var(--ui-border-color) 80%, transparent);
    }

    .phone-shell .progress-bar {
      background: linear-gradient(90deg, var(--ui-accent), color-mix(in srgb, var(--ui-accent) 72%, #FFFFFF));
    }

    .phone-shell .slider-controls {
      margin: 4px 16px 12px;
      padding: 10px;
      border-radius: calc(var(--ui-radius) + 2px);
      border: 1px solid var(--ui-border-color);
      background: var(--ui-surface);
      box-shadow: var(--ui-card-shadow);
    }

    .phone-shell .slide-arrow {
      width: 40px;
      height: 40px;
      border: 1px solid var(--ui-border-color);
      background: var(--ui-graphite);
      box-shadow: 0 4px 12px rgba(15, 23, 42, .12);
    }

    .phone-shell .page-button {
      min-width: 32px;
      height: 32px;
      font-size: 12px;
      font-weight: 600;
      border: 1px solid var(--ui-border-color);
      background: var(--ui-surface);
      box-shadow: none;
    }

    .phone-shell .page-button.is-active {
      border-color: var(--ui-accent);
      background: var(--ui-accent);
      color: #fff;
      box-shadow: 0 4px 12px color-mix(in srgb, var(--ui-accent) 28%, transparent);
    }

    .phone-shell .save-btn {
      min-height: var(--ui-button-height);
      border-radius: var(--ui-button-radius);
      font-size: 15px;
      font-weight: 600;
      letter-spacing: -0.01em;
      background: var(--ui-accent);
      box-shadow: 0 8px 20px color-mix(in srgb, var(--ui-accent) 28%, transparent);
    }

    .phone-shell .footer-actions {
      padding: 12px 16px 20px;
      border-top: 1px solid color-mix(in srgb, var(--ui-border-color) 70%, transparent);
      background: color-mix(in srgb, var(--ui-surface) 88%, var(--ui-shell));
    }

    .phone-shell .empty-ui {
      padding: 24px 20px;
      color: var(--ui-muted);
      font-size: 14px;
      font-weight: 500;
      text-align: center;
    }

    .phone-shell > .app-topbar,
    .phone-shell > .brand-strip,
    .phone-shell > .footer-actions {
      flex: 0 0 auto;
    }

    .phone-shell > .slider-shell,
    .phone-shell > .empty-ui {
      flex: 1 1 auto;
      min-height: 0;
      overflow: hidden;
    }

    .phone-shell > .slider-shell {
      display: grid;
      grid-template-rows: minmax(0, 1fr) auto auto;
      height: 100%;
      min-height: 0;
      padding: 0;
    }

    .phone-shell > .slider-shell > .slide-window {
      position: relative;
      height: 100%;
      min-height: 220px;
      overflow: hidden;
    }

    .phone-shell > .slider-shell > .slide-window > .slide-track {
      position: relative;
      display: flex;
      width: max-content;
      max-width: none;
      height: 100%;
      min-height: 0;
      align-items: stretch;
      overflow: visible;
      transition: transform .28s ease;
      will-change: transform;
    }

    .phone-shell > .slider-shell > .slide-window > .slide-track > .page-slide {
      flex: 0 0 auto;
      box-sizing: border-box;
      width: var(--slide-viewport-width, 100%);
      min-width: var(--slide-viewport-width, 100%);
      max-width: var(--slide-viewport-width, 100%);
      height: 100%;
      max-height: 100%;
      min-height: 0;
      overflow-x: hidden;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
      overscroll-behavior: contain;
      touch-action: pan-y;
      scrollbar-width: thin;
      scrollbar-color: color-mix(in srgb, var(--ui-accent) 75%, #64748B) rgba(106, 142, 174, .12);
    }

    .phone-shell > .slider-shell > .slide-window > .slide-track > .page-slide::-webkit-scrollbar {
      width: 5px;
    }

    .phone-shell > .slider-shell > .slide-window > .slide-track > .page-slide::-webkit-scrollbar-track {
      background: transparent;
      margin: 8px 0;
    }

    .phone-shell > .slider-shell > .slide-window > .slide-track > .page-slide::-webkit-scrollbar-thumb {
      border-radius: 999px;
      border: 1px solid transparent;
      background: linear-gradient(180deg, color-mix(in srgb, var(--ui-accent) 82%, #FFFFFF), var(--ui-accent));
      background-clip: padding-box;
      box-shadow: 0 0 8px color-mix(in srgb, var(--ui-accent) 28%, transparent);
    }

    .phone-shell > .slider-shell > .slide-window > .slide-track > .page-slide::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(180deg, var(--ui-accent), color-mix(in srgb, var(--ui-accent) 72%, #0F2550));
      background-clip: padding-box;
    }

    .phone-shell > .slider-shell > .progress-shell,
    .phone-shell > .slider-shell > .slider-controls {
      flex: 0 0 auto;
    }

    .phone-shell > .empty-ui {
      flex: 1 1 0;
      min-height: 0;
      overflow-x: hidden;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
      overscroll-behavior: contain;
      scrollbar-width: thin;
      scrollbar-color: var(--ui-accent) color-mix(in srgb, var(--ui-border) 45%, transparent);
    }

    .phone-shell > .empty-ui::-webkit-scrollbar {
      width: 6px;
    }

    .phone-shell > .empty-ui::-webkit-scrollbar-thumb {
      border-radius: 999px;
      background: color-mix(in srgb, var(--ui-accent) 55%, transparent);
    }

    .app-topbar {
      z-index: 10;
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      grid-template-areas: "back title actions";
      gap: 10px;
      align-items: center;
      min-height: 84px;
      min-height: var(--ui-header-height);
      padding: 14px 16px 12px;
      background:
        linear-gradient(145deg, var(--ui-graphite), var(--ui-blue)),
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

    .back-btn,
    .app-back-btn {
      grid-area: back;
      width: auto;
      min-width: 36px;
      height: 36px;
      padding: 0 10px 0 6px;
      border-radius: 999px;
      gap: 2px;
      font-size: 13px;
      font-weight: 600;
    }

    .back-btn,
    .back-btn:hover,
    .back-btn:focus,
    .app-back-btn,
    .app-back-btn:hover,
    .app-back-btn:focus {
      width: auto;
      min-width: 36px;
      border-radius: 999px;
    }

    .top-title {
      grid-area: title;
      min-width: 0;
    }

    .app-topbar.is-align-center .top-title {
      text-align: center;
    }

    .app-topbar.is-align-right .top-title {
      text-align: right;
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
      grid-area: actions;
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
      padding: var(--ui-card-padding);
      border: var(--ui-border-size) solid var(--ui-border-color);
      border-radius: calc(var(--ui-radius) + 2px);
      background: var(--ui-surface);
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
      background: var(--ui-surface);
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
      color: var(--ui-success);
      font-size: 12px;
      font-weight: 900;
    }

    .online-state::before {
      content: "";
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--ui-success);
      box-shadow: 0 0 0 5px color-mix(in srgb, var(--ui-success) 18%, transparent);
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
      border-radius: calc(var(--ui-radius) + 6px);
      background:
        linear-gradient(145deg, var(--ui-graphite), var(--ui-blue)),
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
      padding-right: 4px;
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
      background: transparent;
    }

    .slide-window {
      width: 100%;
      height: 100%;
      min-height: 0;
    }

    .slide-track {
      display: flex;
      width: max-content;
      max-width: none;
      height: 100%;
      min-height: 0;
      transition: transform .28s ease;
      will-change: transform;
    }

    .page-slide {
      flex: 0 0 auto;
      box-sizing: border-box;
      width: var(--slide-viewport-width, 100%);
      min-width: var(--slide-viewport-width, 100%);
      max-width: var(--slide-viewport-width, 100%);
      height: 100%;
      min-height: 0;
      padding: var(--ui-page-padding) var(--ui-page-padding) 28px;
    }

    .device-card {
      min-height: 50px;
      padding: var(--ui-card-padding);
      border: var(--ui-border-size) solid var(--ui-border-color);
      border-radius: var(--ui-radius);
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
      gap: var(--ui-card-spacing);
      margin-top: 16px;
    }

    .param-card {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      gap: 12px;
      align-items: center;
      min-height: 66px;
      padding: var(--ui-card-padding);
      border: var(--ui-border-size) solid var(--ui-border-color);
      border-radius: var(--ui-radius);
      background: var(--ui-surface);
      box-shadow: var(--ui-card-shadow);
      backdrop-filter: blur(var(--ui-card-blur));
      position: relative;
      overflow: hidden;
    }

    .param-card.layout-stacked {
      display: flex;
      flex-direction: column;
      align-items: stretch;
      gap: 10px;
      min-height: 0;
      overflow: visible;
      padding-bottom: calc(var(--ui-card-padding) + 4px);
    }

    .param-card:has(.needle-gauge-visual, .pointer-gauge-visual, .power-gauge-visual, .range-visual, .segmented-visual, .linear-gauge-visual, .registered-visual) {
      overflow: visible;
    }

    .param-card.layout-stacked::before {
      top: 10px;
      bottom: 10px;
    }

    .param-card > .param-main-row {
      display: contents;
    }

    .param-card.layout-stacked > .param-main-row {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      gap: 12px;
      align-items: center;
      width: 100%;
      min-width: 0;
    }

    .param-card.layout-stacked > .range-visual,
    .param-card.layout-stacked > .needle-gauge-visual,
    .param-card.layout-stacked > .pointer-gauge-visual,
    .param-card.layout-stacked > .power-gauge-visual,
    .param-card.layout-stacked > .segmented-visual,
    .param-card.layout-stacked > .linear-gauge-visual,
    .param-card.layout-stacked > .registered-visual {
      width: 100%;
      max-width: 100%;
      margin-top: 0;
      flex: 0 0 auto;
      overflow: visible;
      box-sizing: border-box;
    }

    .param-card.layout-stacked .field-value {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .param-card.is-registered-component.layout-stacked > .param-main-row {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      margin-bottom: 6px;
    }

    .param-card.is-registered-component.layout-stacked {
      padding-top: 12px;
    }

    .param-card.is-hidden {
      display: none;
    }

    .param-icon {
      border: 0;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: color-mix(in srgb, var(--ui-accent) 14%, var(--ui-surface));
      color: var(--ui-accent);
      color: var(--ui-icon-color);
      cursor: pointer;
      box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--ui-accent) 18%, transparent);
    }

    .param-icon:hover,
    .param-icon:focus {
      background: color-mix(in srgb, var(--ui-accent) 22%, var(--ui-surface));
      outline: 2px solid color-mix(in srgb, var(--ui-accent) 34%, transparent);
      outline-offset: 2px;
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
      border: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      min-width: 42px;
      height: 34px;
      padding: 0 8px;
      border-radius: 999px;
      background: rgba(87, 167, 115, .16);
      color: var(--ui-green-dark);
      font-size: 12px;
      font-weight: 900;
      cursor: pointer;
      transition: transform .18s ease, background .18s ease, box-shadow .18s ease;
    }

    .param-meta .icon {
      width: 14px;
      height: 14px;
      stroke-width: 2.6;
    }

    .param-meta:hover,
    .param-meta:focus {
      background: color-mix(in srgb, var(--ui-accent) 20%, var(--ui-surface));
      box-shadow: 0 8px 18px rgba(18, 50, 70, .12);
      outline: 2px solid color-mix(in srgb, var(--ui-accent) 34%, transparent);
      outline-offset: 2px;
      transform: translateY(-1px);
    }

    .toggle-visual {
      position: relative;
      width: 52px;
      height: 28px;
      border-radius: 999px;
      background: #CBD5E1;
      flex-shrink: 0;
      box-shadow: inset 0 1px 3px rgba(15, 37, 80, .14);
      transition: background .24s ease, box-shadow .24s ease;
    }

    .toggle-visual::after {
      content: "";
      position: absolute;
      top: 3px;
      left: 3px;
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: #FFFFFF;
      box-shadow: 0 2px 8px rgba(15, 37, 80, .22);
      transition: transform .24s cubic-bezier(.4, 0, .2, 1);
    }

    .toggle-visual.is-on {
      background: linear-gradient(135deg, var(--ui-accent), color-mix(in srgb, var(--ui-accent) 72%, #0F2550));
      box-shadow: 0 4px 16px color-mix(in srgb, var(--ui-accent) 38%, transparent);
    }

    .toggle-visual.is-on::after {
      transform: translateX(24px);
    }

    button.toggle-visual.param-switch,
    button.power-button-ui.param-button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 0;
      padding: 0;
      cursor: pointer;
    }

    .power-button-ui {
      position: relative;
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: transparent;
      flex-shrink: 0;
      transition: transform .16s ease;
    }

    .power-button-ui .power-button-ring {
      position: absolute;
      inset: 0;
      border-radius: 50%;
      border: 3px solid #94A3B8;
      background: #F1F5F9;
      box-shadow: inset 0 0 0 3px #FFFFFF, 0 6px 16px rgba(15, 37, 80, .12);
      transition: border-color .22s ease, background .22s ease, box-shadow .22s ease;
    }

    .power-button-ui .power-button-core {
      position: relative;
      z-index: 1;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #64748B;
      transition: color .22s ease, transform .16s ease;
    }

    .power-button-ui .power-button-core .icon {
      width: 20px;
      height: 20px;
      stroke-width: 2.4;
    }

    .power-button-ui.is-on .power-button-ring {
      border-color: var(--ui-accent);
      background: linear-gradient(145deg, color-mix(in srgb, var(--ui-accent) 88%, #FFFFFF), var(--ui-accent));
      box-shadow: inset 0 0 0 3px rgba(255, 255, 255, .35), 0 8px 22px color-mix(in srgb, var(--ui-accent) 42%, transparent);
    }

    .power-button-ui.is-on .power-button-core {
      color: #FFFFFF;
    }

    .power-button-ui.is-off .power-button-ring {
      border-color: #CBD5E1;
      background: #F8FAFC;
    }

    .power-button-ui:active {
      transform: scale(.94);
    }

    .power-button-ui.is-pressed .power-button-core {
      transform: scale(.9);
    }

    button.action-dot.param-button {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      border: 3px solid var(--ui-green-dark);
      background: var(--ui-green-dark);
      box-shadow: inset 0 0 0 4px #fff, 0 7px 16px rgba(21, 113, 69, .22);
    }

    button.action-dot.param-button.is-pressed {
      transform: scale(0.96);
    }

    .param-dropdown {
      min-width: 88px;
      max-width: 140px;
      height: 34px;
      border-radius: 999px;
      border: 1px solid rgba(106, 142, 174, .28);
      background: rgba(87, 167, 115, .16);
      color: var(--ui-accent);
      font-size: 12px;
      font-weight: 800;
      padding: 0 10px;
      cursor: pointer;
    }

    .param-range-input {
      position: relative;
      z-index: 2;
      width: 100%;
      margin: 0;
      height: 28px;
      background: transparent;
      -webkit-appearance: none;
      appearance: none;
      cursor: pointer;
    }

    .param-range-input::-webkit-slider-runnable-track {
      height: 8px;
      border-radius: 999px;
      background: transparent;
    }

    .param-range-input::-webkit-slider-thumb {
      -webkit-appearance: none;
      width: 22px;
      height: 22px;
      margin-top: -7px;
      border-radius: 50%;
      border: 3px solid #FFFFFF;
      background: var(--ui-accent);
      box-shadow: 0 3px 10px rgba(15, 37, 80, .22);
      cursor: pointer;
      transition: transform .14s ease;
    }

    .param-range-input:active::-webkit-slider-thumb {
      transform: scale(1.08);
    }

    .param-range-input::-moz-range-track {
      height: 8px;
      border-radius: 999px;
      background: transparent;
      border: 0;
    }

    .param-range-input::-moz-range-thumb {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      border: 3px solid #FFFFFF;
      background: var(--ui-accent);
      box-shadow: 0 3px 10px rgba(15, 37, 80, .22);
      cursor: pointer;
    }

    .range-visual {
      grid-column: 1 / -1;
      flex: 0 0 auto;
      width: 100%;
      margin-top: 10px;
      padding: 12px 14px;
      border-radius: 14px;
      background: linear-gradient(180deg, rgba(248, 250, 252, .95), rgba(241, 245, 249, .88));
      border: 1px solid rgba(106, 142, 174, .18);
      box-sizing: border-box;
      overflow: visible;
    }

    .range-visual .range-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      margin-bottom: 10px;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .04em;
      color: var(--ui-muted);
      text-transform: uppercase;
    }

    .range-visual .range-current {
      font-size: 18px;
      font-weight: 800;
      color: var(--ui-accent);
      letter-spacing: 0;
      text-transform: none;
    }

    .range-track-shell {
      position: relative;
      height: 8px;
      border-radius: 999px;
      background: #E2E8F0;
      overflow: visible;
    }

    .range-track-fill {
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 0;
      border-radius: inherit;
      background: linear-gradient(90deg, color-mix(in srgb, var(--ui-accent) 70%, #FFFFFF), var(--ui-accent));
      pointer-events: none;
      transition: width .12s ease;
    }

    .range-visual .range-values {
      display: none;
    }

    .range-visual .range-line {
      display: none;
    }

    .needle-gauge-visual,
    .pointer-gauge-visual,
    .power-gauge-visual {
      grid-column: 1 / -1;
      flex: 0 0 auto;
      width: 100%;
      margin-top: 10px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      padding: 10px 12px 12px;
      border-radius: 16px;
      background: linear-gradient(180deg, rgba(248, 250, 252, .98), rgba(241, 245, 249, .9));
      border: 1px solid rgba(106, 142, 174, .16);
      user-select: none;
      touch-action: none;
      overflow: visible;
      box-sizing: border-box;
    }

    .power-gauge-svg-host {
      width: 100%;
      max-width: 240px;
      margin: 0 auto;
      display: flex;
      justify-content: center;
      overflow: visible;
      line-height: 0;
    }

    .param-card.layout-stacked > .needle-gauge-visual,
    .param-card.layout-stacked > .pointer-gauge-visual,
    .param-card.layout-stacked > .power-gauge-visual,
    .param-card.layout-stacked > .range-visual {
      margin-top: 0;
    }

    .needle-gauge-svg,
    .power-gauge-svg {
      width: 100%;
      max-width: 240px;
      height: auto;
      min-height: 148px;
      display: block;
      cursor: pointer;
      overflow: visible;
    }

    .needle-gauge-value,
    .power-gauge-value {
      color: var(--ui-text);
      font-size: 13px;
      font-weight: 800;
      letter-spacing: .02em;
    }

    .needle-gauge-value strong,
    .power-gauge-value strong {
      color: var(--ui-accent);
      font-size: 22px;
      font-weight: 800;
      margin-right: 4px;
    }

    .param-text-input {
      width: 100%;
      border: 1px solid rgba(106, 142, 174, .28);
      border-radius: 10px;
      padding: 6px 10px;
      font: inherit;
      font-weight: 700;
      color: var(--ui-text);
      background: #fff;
    }

    .component-preview-powerGauge svg,
    .component-preview-pointerGauge svg,
    .component-preview-needleGauge svg {
      width: 72px;
      height: 44px;
      display: block;
    }

    .segmented-visual {
      grid-column: 1 / -1;
      flex: 0 0 auto;
      width: 100%;
      margin-top: 10px;
      display: flex;
      gap: 6px;
      padding: 5px;
      border-radius: 14px;
      background: #F1F5F9;
      border: 1px solid rgba(106, 142, 174, .16);
      user-select: none;
      box-sizing: border-box;
      overflow: visible;
    }

    .segmented-option {
      flex: 1 1 0;
      border: 0;
      cursor: pointer;
      padding: 10px 12px;
      border-radius: 10px;
      background: transparent;
      color: var(--ui-muted);
      font-size: 12px;
      font-weight: 800;
      transition: background .18s ease, color .18s ease, box-shadow .18s ease, transform .12s ease;
    }

    .segmented-option.is-active {
      background: var(--ui-accent);
      color: #FFFFFF;
      box-shadow: 0 6px 16px color-mix(in srgb, var(--ui-accent) 34%, transparent);
    }

    .segmented-option:active {
      transform: scale(.97);
    }

    .param-card.is-registered-component {
      grid-template-columns: 1fr;
      align-items: stretch;
    }

    .param-card.is-registered-component > .param-icon {
      display: none;
    }

    .param-card.is-registered-component > .param-main-row {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 10px;
      align-items: center;
      width: 100%;
      margin-bottom: 4px;
    }

    .param-card.is-registered-component > .param-main-row > .param-icon {
      display: none;
    }

    .param-card.is-registered-component > div:has(.field-label) {
      display: block;
      margin-bottom: 6px;
    }

    .param-card.is-registered-component .registered-visual {
      grid-column: 1 / -1;
      margin-top: 0;
    }

    .registered-visual {
      grid-column: 1 / -1;
      margin-top: 10px;
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 14px;
      border-radius: 14px;
      background: #E8F7EE;
      border: 1px solid #B7E3C7;
      user-select: none;
    }

    .registered-visual-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #157145;
    }

    .registered-visual-icon .icon {
      width: 22px;
      height: 22px;
    }

    .registered-visual-text {
      display: flex;
      flex-direction: column;
      gap: 2px;
      min-width: 0;
    }

    .registered-visual-kicker {
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .04em;
      color: #5F7482;
    }

    .registered-visual-value {
      font-size: 16px;
      font-weight: 700;
      color: #102A3A;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
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
      flex: 0 0 auto;
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
      margin: 6px 18px 14px;
      padding: 12px;
      border: 1px solid var(--ui-border);
      border-radius: 20px;
      background: color-mix(in srgb, var(--ui-surface) 84%, transparent);
      box-shadow: var(--ui-card-shadow);
      flex: 0 0 auto;
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
      background: var(--ui-surface);
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
      background: var(--ui-surface);
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
      transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
    }

    .save-btn.is-hover-lift:hover,
    .save-btn.is-hover-lift:focus-visible {
      transform: translateY(-2px);
      box-shadow: 0 18px 34px rgba(4, 147, 66, .28);
    }

    .save-btn.is-hover-glow:hover,
    .save-btn.is-hover-glow:focus-visible {
      filter: brightness(1.06);
      box-shadow: 0 0 0 4px color-mix(in srgb, var(--ui-accent) 18%, transparent), 0 18px 34px rgba(4, 147, 66, .28);
    }

    .studio-launch {
      background: rgba(255, 255, 255, .16);
    }

    .studio-backdrop {
      position: fixed;
      inset: 0;
      z-index: 80;
      display: none;
      pointer-events: none;
      background: transparent;
    }

    .studio-backdrop.is-open {
      display: block;
    }

    .ui-studio {
      position: fixed;
      inset: 0;
      z-index: 90;
      display: none;
      grid-template-rows: 56px minmax(0, 1fr);
      width: 100%;
      background: var(--ui-bg);
      color: var(--ui-text);
      font-family: var(--ui-font);
      font-size: var(--ui-font-size);
      font-weight: var(--ui-font-weight);
      overflow: hidden;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    .ui-studio.is-open {
      display: grid;
    }

    .ui-studio ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    .ui-studio ::-webkit-scrollbar-track {
      background: transparent;
    }

    .ui-studio ::-webkit-scrollbar-thumb {
      border-radius: 999px;
      background: color-mix(in srgb, var(--ui-muted) 28%, transparent);
    }

    .studio-topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      min-width: 0;
      padding: 0 20px;
      border-bottom: 1px solid var(--studio-toolbar-border);
      background: var(--studio-toolbar);
      box-shadow: 0 1px 0 rgba(28, 25, 23, .04);
    }

    .studio-topbar-left {
      display: flex;
      align-items: center;
      gap: 14px;
      min-width: 0;
    }

    .studio-title {
      display: flex;
      align-items: center;
      gap: 12px;
      min-width: 0;
      padding-left: 0;
      border-left: 0;
    }

    .studio-brand-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      border-radius: 10px;
      background: linear-gradient(135deg, #EA580C, #C2410C);
      color: #fff;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .04em;
      box-shadow: 0 8px 20px rgba(194, 65, 12, .28);
      flex: 0 0 auto;
    }

    .studio-title h2 {
      margin: 0;
      font-size: 15px;
      line-height: 1.2;
      font-weight: 700;
      color: var(--ui-text);
      letter-spacing: -.02em;
    }

    .studio-title p {
      margin: 1px 0 0;
      color: var(--ui-muted);
      font-size: 12px;
      font-weight: 500;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .studio-device-tabs,
    .studio-top-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 8px;
      min-width: 0;
      flex-wrap: wrap;
    }

    .studio-top-actions {
      padding: 0;
      border: 0;
      border-radius: 0;
      background: transparent;
      box-shadow: none;
    }

    .studio-action-divider {
      width: 1px;
      height: 24px;
      background: var(--ui-border);
      margin: 0 2px;
    }

    .studio-workspace {
      display: grid;
      grid-template-columns: 232px minmax(360px, 1fr) minmax(360px, 420px);
      grid-template-areas: "nav preview panel";
      min-height: 0;
      overflow: hidden;
    }

    .studio-settings-column {
      grid-area: panel;
      display: flex;
      flex-direction: column;
      min-height: 0;
      min-width: 0;
      border-left: 1px solid var(--ui-border);
      background: var(--ui-surface);
    }

    .studio-panel-scroll {
      flex: 1 1 auto;
      min-height: 0;
      overflow: auto;
      scroll-behavior: smooth;
    }

    .studio-panel-footer {
      flex: 0 0 auto;
      padding: 12px 20px 16px;
      border-top: 1px solid var(--ui-border);
      background: linear-gradient(180deg, #FAFAF9, #FFFFFF);
    }

    .studio-sections,
    .studio-controls,
    .studio-inspector {
      min-height: 0;
      overflow: auto;
    }

    .studio-sections {
      grid-area: nav;
      padding: 16px 12px;
      border-right: 1px solid var(--studio-sidebar-border);
      background: var(--studio-sidebar);
      box-shadow: 1px 0 0 rgba(0, 0, 0, .35);
    }

    .studio-section-kicker {
      display: block;
      margin: 0 8px 12px;
      padding: 0;
      border: 0;
      border-radius: 0;
      background: transparent;
      color: var(--studio-sidebar-kicker);
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .12em;
    }

    .studio-section-kicker::before {
      display: none;
    }

    .studio-nav-btn {
      display: grid;
      grid-template-columns: 34px minmax(0, 1fr);
      gap: 10px;
      align-items: center;
      width: 100%;
      min-height: 44px;
      margin-bottom: 4px;
      padding: 8px 10px;
      border: 0;
      border-radius: 10px;
      background: transparent;
      color: var(--studio-sidebar-text);
      text-align: left;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      box-shadow: none;
      transition: background .15s ease, color .15s ease;
    }

    .studio-nav-btn:hover {
      background: var(--studio-sidebar-hover);
      color: #E7E5E4;
      transform: none;
    }

    .studio-nav-btn.is-active {
      background: var(--studio-sidebar-active);
      color: var(--studio-sidebar-text-active);
      box-shadow: inset 0 0 0 1px var(--studio-accent-ring);
      transform: none;
    }

    .studio-nav-btn.is-active::before {
      content: "";
      position: absolute;
      inset: 8px auto 8px 0;
      width: 3px;
      border-radius: 0 4px 4px 0;
      background: var(--studio-accent);
      box-shadow: none;
    }

    .studio-nav-btn > .icon {
      width: 34px;
      height: 34px;
      padding: 8px;
      border-radius: 8px;
      background: rgba(255, 255, 255, .04);
      color: #A8A29E;
      box-shadow: none;
    }

    .studio-nav-btn.is-active > .icon,
    .studio-nav-btn:hover > .icon {
      background: var(--studio-accent-soft);
      color: #FDBA74;
      box-shadow: none;
    }

    .studio-nav-btn span {
      display: block;
      min-width: 0;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: -.01em;
    }

    .studio-nav-btn small {
      display: block;
      margin-top: 1px;
      color: #78716C;
      font-size: 10px;
      font-weight: 500;
    }

    .studio-nav-btn.is-active small {
      color: #D6D3D1;
    }

    .studio-nav-divider {
      margin: 14px 8px 8px;
      padding-top: 12px;
      border-top: 1px solid rgba(255, 255, 255, .08);
      color: #6B7280;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
    }

    .studio-nav-divider:first-of-type {
      margin-top: 4px;
      padding-top: 0;
      border-top: 0;
    }

    .studio-help {
      display: none;
    }

    .icon-search-box {
      width: 100% !important;
      min-height: 38px !important;
      padding: 9px 12px !important;
      font-size: 13px !important;
      margin-bottom: 12px !important;
      border: 1px solid var(--ui-border) !important;
      border-radius: 8px !important;
      background: #FFFFFF !important;
      color: var(--ui-text) !important;
      transition: border-color .15s ease, box-shadow .15s ease;
    }

    .icon-search-box:focus {
      border-color: var(--ui-accent) !important;
      outline: 0 !important;
      box-shadow: 0 0 0 3px color-mix(in srgb, var(--ui-accent) 12%, transparent) !important;
    }

    .icon-library-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
      gap: 8px;
      margin-bottom: 14px;
    }

    .icon-library-grid .icon-choice {
      min-height: 50px;
      padding: 8px;
    }

    #params-buttons-container {
      display: grid;
      gap: 14px;
    }

    .param-editor-card {
      background: var(--ui-surface);
      border: 1px solid var(--ui-border);
      padding: 16px;
      margin: 0;
      border-radius: 12px;
      box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    }

    .param-editor-card h4 {
      margin: 0;
      font-size: 14px;
      font-weight: 700;
      color: var(--ui-text);
      background: none;
      -webkit-text-fill-color: currentColor;
    }

    .param-editor-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
      justify-content: flex-end;
    }

    #params-buttons-container .studio-btn {
      min-height: 36px;
      padding: 0 12px;
      border-color: var(--ui-border) !important;
      background: var(--ui-surface) !important;
      color: var(--ui-text) !important;
      font-weight: 600;
    }

    #params-buttons-container .studio-btn:hover {
      border-color: color-mix(in srgb, var(--ui-accent) 40%, var(--ui-border)) !important;
      background: #FAFAF9 !important;
      color: var(--studio-accent-text) !important;
    }

    .studio-section h3 {
      color: var(--ui-text);
      background: none;
      -webkit-text-fill-color: currentColor;
    }

    .studio-controls {
      background: var(--ui-surface);
    }

    .studio-field input:focus,
    .studio-field select:focus,
    .studio-field textarea:focus {
      border-color: var(--ui-accent) !important;
      box-shadow: 0 0 0 3px color-mix(in srgb, var(--ui-accent) 12%, transparent) !important;
      outline: 0;
    }

    .studio-color-row input[type="color"] {
      cursor: pointer;
      border: 2px solid var(--ui-border) !important;
      transition: all .2s ease;
    }

    .studio-color-row input[type="color"]:hover {
      border-color: var(--ui-accent) !important;
    }

    .studio-controls,
    .studio-inspector {
      display: none;
      padding: 0;
      border: 0;
      background: transparent;
      overflow: visible;
    }

    .studio-controls.has-active,
    .studio-inspector.has-active {
      display: block;
    }

    .studio-panel-body {
      padding: 20px 20px 8px;
    }

    .studio-panel-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 18px;
      padding-bottom: 14px;
      border-bottom: 1px solid var(--ui-border);
    }

    .studio-panel-header h3 {
      margin: 0;
      padding: 0;
      border: 0;
      font-size: 16px;
      font-weight: 700;
      letter-spacing: -.02em;
      color: var(--ui-text);
    }

    .studio-panel-subtitle {
      margin: 4px 0 0;
      color: var(--ui-muted);
      font-size: 12px;
      font-weight: 500;
      line-height: 1.45;
    }

    .studio-panel-group {
      margin-bottom: 20px;
      padding: 14px;
      border: 1px solid var(--ui-border);
      border-radius: 12px;
      background: #FAFAF9;
    }

    .studio-panel-group:last-child {
      margin-bottom: 0;
    }

    .studio-panel-label {
      margin: 0 0 12px;
      color: var(--ui-muted);
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
    }

    .studio-preset-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 8px;
    }

    .studio-preset-card {
      display: grid;
      gap: 8px;
      align-content: start;
      min-height: 72px;
      padding: 10px;
      border: 1px solid var(--ui-border);
      border-radius: 10px;
      background: #FFFFFF;
      color: var(--ui-text);
      text-align: left;
      cursor: pointer;
      transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }

    .studio-preset-card:hover {
      border-color: color-mix(in srgb, var(--ui-accent) 35%, var(--ui-border));
      box-shadow: 0 4px 12px rgba(15, 23, 42, .06);
    }

    .studio-preset-card.is-active {
      border-color: var(--ui-accent);
      background: color-mix(in srgb, var(--ui-accent) 4%, #FFFFFF);
      box-shadow: 0 0 0 1px color-mix(in srgb, var(--ui-accent) 24%, transparent), 0 6px 16px rgba(194, 65, 12, .1);
    }

    .studio-preset-swatch {
      display: block;
      height: 28px;
      border-radius: 7px;
      border: 1px solid rgba(15, 23, 42, .08);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .35);
    }

    .studio-preset-swatch-light { background: linear-gradient(135deg, #F8FAFC, #E2E8F0); }
    .studio-preset-swatch-dark { background: linear-gradient(135deg, #0F172A, #1E293B); }
    .studio-preset-swatch-ocean { background: linear-gradient(135deg, #EFF6FF, #BFDBFE); }
    .studio-preset-swatch-nature { background: linear-gradient(135deg, #ECFDF5, #D1FAE5); }
    .studio-preset-swatch-glass { background: linear-gradient(135deg, #F8FAFC, #CBD5E1); }

    .studio-preset-name {
      font-size: 12px;
      font-weight: 600;
      letter-spacing: -.01em;
    }

    .studio-preset-grid .studio-preset-card:last-child:nth-child(odd) {
      grid-column: 1 / -1;
      max-width: calc(50% - 4px);
    }

    .studio-segmented-full {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0;
      padding: 3px;
      border: 1px solid var(--ui-border);
      border-radius: 10px;
      background: #F5F5F4;
    }

    .studio-segmented-full .studio-segment {
      min-height: 36px;
      border: 0;
      border-radius: 7px;
      background: transparent;
      box-shadow: none;
      color: var(--ui-muted);
      font-weight: 600;
    }

    .studio-segmented-full .studio-segment.is-active {
      background: #FFFFFF;
      color: var(--ui-text);
      box-shadow: 0 1px 3px rgba(15, 23, 42, .1);
    }

    .studio-preview-column {
      grid-area: preview;
      min-width: 0;
      min-height: 0;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      padding: 16px 20px 20px;
      background-color: var(--studio-canvas);
      background-image:
        radial-gradient(circle at 50% 0%, rgba(255, 255, 255, .08), transparent 40%),
        radial-gradient(circle at 50% 100%, rgba(28, 25, 23, .28), transparent 42%),
        radial-gradient(var(--studio-canvas-grid) 1px, transparent 1px);
      background-size: auto, auto, 20px 20px;
      border-right: 1px solid var(--studio-canvas-border);
      box-shadow: inset 1px 0 0 rgba(255, 255, 255, .04);
    }

    .studio-preview-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      width: 100%;
      max-width: calc(var(--studio-phone-width) + 40px);
      margin: 0 auto 14px;
      color: #E7E5E4;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: .06em;
      text-transform: uppercase;
      flex: 0 0 auto;
    }

    .studio-preview-head-tools {
      display: flex;
      align-items: center;
      gap: 10px;
      min-width: 0;
    }

    .studio-preview-zoom {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px;
      border: 1px solid rgba(255, 255, 255, .14);
      border-radius: 999px;
      background: rgba(28, 25, 23, .34);
    }

    .studio-preview-zoom-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 28px;
      height: 28px;
      padding: 0;
      border: 0;
      border-radius: 50%;
      background: transparent;
      color: #F5F5F4;
      font: inherit;
      font-size: 14px;
      font-weight: 700;
      line-height: 1;
      cursor: pointer;
      transition: background .15s ease, color .15s ease;
    }

    .studio-preview-zoom-btn svg {
      width: 14px;
      height: 14px;
    }

    .studio-preview-zoom-btn:hover,
    .studio-preview-zoom-btn:focus {
      background: rgba(255, 255, 255, .12);
      color: #FFFFFF;
      outline: none;
    }

    .studio-preview-zoom-btn:disabled {
      opacity: .35;
      cursor: not-allowed;
    }

    .studio-preview-zoom-label {
      min-width: 44px;
      padding: 0 4px;
      color: #F5F5F4;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .04em;
      text-align: center;
      text-transform: none;
    }

    .studio-preview-zoom-fit {
      width: auto;
      min-width: 36px;
      padding: 0 10px;
      margin-left: 2px;
      border-radius: 999px;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .04em;
      text-transform: uppercase;
    }

    .studio-preview-zoom-fit.is-active {
      background: rgba(16, 185, 129, .22);
      color: #A7F3D0;
    }

    .studio-section h3 .studio-btn {
      background-clip: border-box;
      -webkit-background-clip: border-box;
      -webkit-text-fill-color: currentColor;
      color: var(--ui-muted);
      min-height: 32px;
      padding: 0 10px;
      font-size: 11px;
      font-weight: 600;
    }

    .studio-preview-size {
      padding: 4px 10px;
      border: 1px solid rgba(255, 255, 255, .16);
      border-radius: 999px;
      background: rgba(15, 23, 42, .28);
      color: #F1F5F9;
      font-size: 10px;
      font-weight: 600;
      letter-spacing: .04em;
      text-transform: none;
    }

    .studio-inspector {
      background: var(--ui-surface);
      box-shadow: none;
    }

    .studio-settings-column .studio-grid,
    .studio-settings-column .studio-range-grid.is-two-col {
      grid-template-columns: 1fr;
    }

    .studio-settings-column .studio-panel-group {
      width: 100%;
      min-width: 0;
    }

    .studio-settings-column .studio-panel-body {
      padding: 18px 20px 24px;
      min-width: 0;
    }

    .studio-settings-column .studio-range-field,
    .studio-settings-column .studio-field,
    .studio-settings-column .studio-color-row {
      width: 100%;
      min-width: 0;
      box-sizing: border-box;
      max-width: 100%;
    }

    .studio-settings-column .studio-range-top label {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    #studio-typography-section .studio-panel-group {
      display: grid;
      gap: 12px;
    }

    .studio-preview-mount {
      display: flex;
      align-items: flex-start;
      justify-content: center;
      flex: 1 1 auto;
      width: 100%;
      min-height: 0;
      overflow-x: hidden;
      overflow-y: auto;
      padding: 8px 0 12px;
      -webkit-overflow-scrolling: touch;
      overscroll-behavior: contain;
      scrollbar-width: thin;
      scrollbar-color: color-mix(in srgb, var(--ui-accent) 70%, #64748B) rgba(15, 23, 42, .12);
    }

    body.studio-open .studio-preview-mount {
      touch-action: pan-y;
    }

    .studio-preview-frame {
      position: relative;
      flex: 0 0 auto;
      width: calc(var(--studio-phone-width) + 20px);
      padding: 10px;
      border-radius: 42px;
      background: linear-gradient(160deg, #4B5563 0%, #1F2937 52%, #111827 100%);
      box-shadow:
        0 0 0 1px rgba(255, 255, 255, .14),
        0 2px 0 rgba(255, 255, 255, .08) inset,
        0 40px 80px -20px rgba(0, 0, 0, .55);
      transform-origin: top center;
    }

    .studio-preview-frame::before {
      content: "";
      position: absolute;
      top: 16px;
      left: 50%;
      z-index: 2;
      transform: translateX(-50%);
      width: 92px;
      height: 24px;
      border-radius: 999px;
      background: #000000;
      box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .08);
      pointer-events: none;
    }

    .studio-preview-frame::after {
      content: "";
      position: absolute;
      right: -3px;
      top: 120px;
      width: 3px;
      height: 56px;
      border-radius: 0 3px 3px 0;
      background: #1F2937;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .08);
      pointer-events: none;
    }

    body.studio-open {
      padding: 0;
    }

    body.studio-open .ui-studio .phone-shell {
      width: var(--studio-phone-width);
      max-width: var(--studio-phone-width);
      height: var(--studio-phone-height);
      max-height: var(--studio-phone-height);
      min-height: var(--studio-phone-height);
      flex-shrink: 0;
      margin: 0;
      border-radius: 32px;
      box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .04);
      overflow: hidden;
      display: grid;
      grid-template-rows: auto auto minmax(0, 1fr);
    }

    body.studio-open .ui-studio .phone-shell > .app-topbar,
    body.studio-open .ui-studio .phone-shell > .brand-strip {
      flex: 0 0 auto;
    }

    body.studio-open .ui-studio .phone-shell > .slider-shell,
    body.studio-open .ui-studio .phone-shell > .empty-ui {
      min-height: 0;
      overflow: hidden;
    }

    body.studio-open .ui-studio .phone-shell > .slider-shell {
      display: grid;
      grid-template-rows: minmax(0, 1fr) auto auto;
      height: 100%;
      min-height: 0;
    }

    body.studio-open .ui-studio .phone-shell > .slider-shell > .slide-window {
      position: relative;
      height: 100%;
      min-height: 220px;
      overflow: hidden;
    }

    body.studio-open .ui-studio .phone-shell > .slider-shell > .slide-window > .slide-track {
      position: relative;
      width: max-content;
      max-width: none;
      height: 100%;
      min-height: 0;
      overflow: visible;
    }

    body.studio-open .ui-studio .phone-shell > .slider-shell > .slide-window > .slide-track > .page-slide {
      flex: 0 0 auto;
      width: var(--slide-viewport-width, 100%);
      min-width: var(--slide-viewport-width, 100%);
      max-width: var(--slide-viewport-width, 100%);
      height: 100%;
      max-height: 100%;
      overflow-x: hidden;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
      touch-action: pan-y;
    }

    body.studio-open .ui-studio .phone-shell.is-showing-info {
      display: flex;
      flex-direction: column;
      overflow: hidden;
      min-height: 0;
      grid-template-rows: none;
    }

    body.studio-open .ui-studio .phone-shell.is-showing-info > .device-details-view {
      display: flex;
      flex-direction: column;
      flex: 1 1 auto;
      min-height: 0;
      width: 100%;
      overflow: hidden;
    }

    body.studio-open .ui-studio .phone-shell > .device-details-view {
      min-height: 0;
      overflow: hidden;
    }

    body.studio-open .ui-studio .phone-shell.is-showing-info > .device-details-view .device-details-body {
      flex: 1 1 auto;
      min-height: 0;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
    }

    .studio-section-desc {
      margin: -8px 0 14px;
      color: var(--ui-muted);
      font-size: 12px;
      font-weight: 500;
      line-height: 1.5;
    }

    .studio-section {
      scroll-margin-top: 16px;
      padding: 0;
      margin-bottom: 0;
      border-bottom: 0;
    }

    .studio-controls > .studio-section,
    .studio-inspector > .studio-section {
      display: none;
    }

    .studio-controls > .studio-section.is-active,
    .studio-inspector > .studio-section.is-active {
      display: block;
    }

    .studio-section h3 {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin: 0 0 16px;
      padding-bottom: 0;
      border-bottom: 0;
      color: var(--ui-text);
      font-size: 16px;
      font-weight: 700;
      letter-spacing: -.02em;
    }

    .studio-panel-header .studio-btn,
    .studio-section h3 .studio-btn {
      flex: 0 0 auto;
    }

    .studio-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .studio-field {
      position: relative;
      display: grid;
      gap: 6px;
      min-width: 0;
      padding: 0;
      border: 0;
      border-radius: 0;
      background: transparent;
      box-shadow: none;
    }

    .studio-field.has-range {
      padding-right: 48px;
    }

    .studio-field.has-range::after {
      content: attr(data-range-value);
      position: absolute;
      right: 0;
      top: 0;
      min-width: 36px;
      padding: 3px 8px;
      border: 1px solid var(--ui-border);
      border-radius: 6px;
      background: #FAFAF9;
      color: var(--ui-muted);
      font-size: 10px;
      font-weight: 600;
      text-align: center;
    }

    .studio-field label,
    .studio-field span {
      color: var(--ui-muted);
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .05em;
    }

    .studio-color-row {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 48px;
      gap: 10px;
      align-items: center;
      min-height: 44px;
      margin-bottom: 6px;
      padding: 8px 10px;
      border: 1px solid transparent;
      border-radius: 8px;
      background: #FFFFFF;
      box-shadow: none;
      transition: border-color .15s ease, background .15s ease;
    }

    .studio-panel-group .studio-color-row:last-child {
      margin-bottom: 0;
    }

    .studio-color-row:hover {
      border-color: var(--ui-border);
      background: #FFFFFF;
    }

    .studio-color-row label {
      color: var(--ui-text);
      font-size: 12px;
      font-weight: 600;
      letter-spacing: -.01em;
    }

    .studio-field input,
    .studio-field select,
    .studio-field textarea {
      appearance: none;
      width: 100%;
      min-height: 38px;
      border: 1px solid var(--ui-border);
      border-radius: 8px;
      background: #FFFFFF;
      color: var(--ui-text);
      font: inherit;
      font-size: 13px;
      font-weight: 500;
      padding: 0 11px;
      box-shadow: none;
      transition: border-color .15s ease, box-shadow .15s ease;
    }

    .studio-field select {
      padding-right: 34px;
      background:
        linear-gradient(45deg, transparent 50%, var(--studio-select-arrow, #78716C) 50%) right 16px center / 6px 6px no-repeat,
        linear-gradient(135deg, var(--studio-select-arrow, #78716C) 50%, transparent 50%) right 10px center / 6px 6px no-repeat,
        #FFFFFF;
    }

    .studio-field select option,
    #studio-param-select option {
      background: #FFFFFF;
      color: #102A3A;
      font-weight: 700;
    }

    .studio-field select:disabled {
      opacity: .68;
      cursor: not-allowed;
    }

    .studio-field input::placeholder,
    .studio-field textarea::placeholder {
      color: color-mix(in srgb, var(--ui-muted) 70%, transparent);
      font-weight: 700;
    }

    .studio-field input[type="color"] {
      padding: 4px;
      min-height: 42px;
      cursor: pointer;
    }

    .studio-color-row input[type="color"] {
      width: 48px;
      min-height: 32px;
      padding: 2px;
      border-radius: 8px;
      border: 1px solid var(--ui-border) !important;
      background: var(--ui-surface);
      box-shadow: none;
    }

    .ui-studio .studio-field input[type="range"] {
      min-height: 28px;
      padding: 0;
      border: 0;
      background: transparent;
      box-shadow: none;
      accent-color: var(--studio-range-fill);
      cursor: pointer;
    }

    .ui-studio .studio-field input[type="range"]::-webkit-slider-runnable-track {
      height: 6px;
      border-radius: 999px;
      background: var(--studio-range-track);
      box-shadow: none;
    }

    .ui-studio .studio-field input[type="range"]::-webkit-slider-thumb {
      appearance: none;
      width: 16px;
      height: 16px;
      margin-top: -5px;
      border: 2px solid #fff;
      border-radius: 50%;
      background: var(--studio-range-thumb);
      box-shadow: 0 1px 4px rgba(28, 25, 23, .18);
    }

    .ui-studio .studio-field input[type="range"]::-moz-range-track {
      height: 6px;
      border-radius: 999px;
      background: var(--studio-range-track);
    }

    .ui-studio .studio-field input[type="range"]::-moz-range-thumb {
      width: 12px;
      height: 12px;
      border: 2px solid #fff;
      border-radius: 50%;
      background: var(--studio-range-thumb);
      box-shadow: 0 1px 4px rgba(28, 25, 23, .18);
    }

    .studio-field input[type="range"] {
      min-height: 28px;
      padding: 0;
      border: 0;
      background: transparent;
      box-shadow: none;
      accent-color: var(--ui-accent);
      cursor: pointer;
    }

    .studio-field input[type="range"]::-webkit-slider-runnable-track {
      height: 6px;
      border-radius: 999px;
      background: #E2E8F0;
      box-shadow: none;
    }

    .studio-field input[type="range"]::-webkit-slider-thumb {
      appearance: none;
      width: 16px;
      height: 16px;
      margin-top: -5px;
      border: 2px solid #fff;
      border-radius: 50%;
      background: var(--ui-accent);
      box-shadow: 0 1px 4px rgba(15, 23, 42, .18);
    }

    .studio-field input[type="range"]::-moz-range-track {
      height: 6px;
      border-radius: 999px;
      background: #E2E8F0;
    }

    .studio-field input[type="range"]::-moz-range-thumb {
      width: 12px;
      height: 12px;
      border: 2px solid #fff;
      border-radius: 50%;
      background: var(--ui-accent);
      box-shadow: 0 1px 4px rgba(15, 23, 42, .18);
    }

    .studio-segmented,
    .studio-preset-row {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-bottom: 14px;
    }

    .studio-chip,
    .studio-segment {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 34px;
      padding: 0 12px;
      border: 1px solid var(--ui-border);
      border-radius: 8px;
      background: #FAFAF9;
      color: var(--ui-text);
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: none;
      transition: border-color .15s ease, background .15s ease, color .15s ease;
    }

    .studio-chip:hover,
    .studio-segment:hover {
      border-color: color-mix(in srgb, var(--ui-accent) 35%, var(--ui-border));
      background: #FFFFFF;
    }



    .studio-chip.is-active,
    .studio-segment.is-active {
      border-color: var(--ui-accent);
      background: var(--ui-accent);
      color: #fff;
      box-shadow: 0 4px 12px rgba(194, 65, 12, .2);
    }

    .studio-toggle-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      min-height: 44px;
      margin: 0 0 8px;
      padding: 10px 12px;
      border: 1px solid var(--ui-border);
      border-radius: 10px;
      background: #FAFAF9;
      color: var(--ui-text);
      font-size: 12px;
      font-weight: 600;
      box-shadow: none;
    }

    .ui-studio .studio-toggle-row input {
      appearance: none;
      position: relative;
      width: 42px;
      height: 24px;
      margin: 0;
      border-radius: 999px;
      background: var(--ui-toggle-off);
      accent-color: var(--studio-accent);
      cursor: pointer;
      transition: background .18s ease;
    }

    .studio-toggle-row input::after {
      content: "";
      position: absolute;
      top: 3px;
      left: 3px;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: var(--ui-surface);
      box-shadow: 0 4px 10px rgba(18, 50, 70, .22);
      transition: transform .18s ease;
    }

    .ui-studio .studio-toggle-row input:checked {
      background: var(--studio-accent);
    }

    .studio-toggle-row input:checked {
      background: var(--ui-accent);
    }

    .studio-toggle-row input:checked::after {
      transform: translateX(18px);
    }

    .component-panel {
      display: none;
    }

    .component-panel.is-active {
      display: block;
    }

    .component-selected {
      outline: 2px solid var(--ui-accent);
      outline-offset: 3px;
    }

    .studio-ai {
      position: fixed;
      left: 10px;
      right: 10px;
      bottom: 10px;
      z-index: 95;
      display: none;
      grid-template-columns: minmax(280px, 1fr) minmax(280px, 1fr);
      gap: 18px;
      padding: 14px 18px;
      border: 1px solid var(--ui-border);
      border-radius: 8px;
      background: color-mix(in srgb, var(--ui-surface) 94%, transparent);
      box-shadow: 0 18px 48px rgba(18, 50, 70, .14);
    }

    body.studio-open .studio-ai {
      display: grid;
    }

    .studio-ai h3 {
      margin: 0 0 8px;
      color: var(--ui-accent);
      font-size: 14px;
      font-weight: 900;
    }

    .studio-ai-form {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 10px;
    }

    .studio-ai input,
    .studio-import-export textarea {
      width: 100%;
      min-height: 40px;
      border: 1px solid var(--ui-border);
      border-radius: 8px;
      background: var(--ui-surface);
      color: var(--ui-text);
      font: inherit;
      font-size: 13px;
      font-weight: 700;
      padding: 8px 10px;
    }

    .studio-import-export textarea {
      min-height: 84px;
      font-family: Consolas, "Courier New", monospace;
      resize: vertical;
    }

    @media (max-width: 860px) {
      .studio-ai {
        position: static;
        grid-template-columns: 1fr;
        margin: 10px;
      }
    }

    .studio-field textarea {
      min-height: 150px;
      padding: 10px;
      resize: vertical;
      font-family: Consolas, "Courier New", monospace;
      font-size: 12px;
      font-weight: 600;
    }

    #studio-schema-section {
      min-height: calc(100vh - 126px);
    }

    #studio-schema-section h3 {
      padding: 0 0 12px;
      border: 0;
      border-bottom: 1px solid var(--ui-border);
      border-radius: 0;
      background: transparent;
      box-shadow: none;
    }

    #studio-schema-section .studio-field {
      padding: 0;
      border-radius: 0;
      background: transparent;
      box-shadow: none;
    }

    #schema-output {
      min-height: calc(100vh - 315px);
      border-radius: 8px;
      border-color: var(--ui-border);
      background: #F8FAFC;
      color: #1E293B;
      font-size: 11px;
      line-height: 1.6;
      box-shadow: none;
    }

    .studio-range-field {
      display: grid;
      gap: 8px;
      min-width: 0;
      padding: 12px 14px;
      border: 1px solid var(--ui-border);
      border-radius: 12px;
      background: linear-gradient(180deg, #FFFFFF, #FAFAF9);
      box-shadow: 0 1px 2px rgba(28, 25, 23, .04);
      touch-action: none;
    }

    .studio-range-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }

    .studio-range-top label {
      color: var(--ui-text);
      font-size: 12px;
      font-weight: 700;
      letter-spacing: -.01em;
      text-transform: none;
    }

    .studio-range-track-wrap {
      padding: 2px 0 4px;
    }

    .ui-studio .studio-range-output {
      min-width: 44px;
      padding: 4px 8px;
      border: 1px solid color-mix(in srgb, var(--studio-accent) 24%, var(--ui-border));
      border-radius: 8px;
      background: color-mix(in srgb, var(--studio-accent) 8%, #FFFFFF);
      color: var(--studio-accent-text);
      font-size: 11px;
      font-weight: 800;
      text-align: center;
      line-height: 1.2;
    }

    .ui-studio .studio-range-field input[type="range"] {
      width: 100%;
      min-height: 24px;
      margin: 0;
      padding: 0;
      border: 0;
      background: transparent;
      box-shadow: none;
      cursor: pointer;
      accent-color: var(--studio-range-fill);
      --range-progress: 50%;
      touch-action: none;
    }

    .ui-studio .studio-range-field input[type="range"]::-webkit-slider-runnable-track {
      height: 8px;
      border-radius: 999px;
      background: linear-gradient(90deg, var(--studio-range-fill) 0%, var(--studio-range-fill) var(--range-progress), var(--studio-range-track) var(--range-progress), var(--studio-range-track) 100%);
      box-shadow: inset 0 1px 2px rgba(28, 25, 23, .06);
    }

    .ui-studio .studio-range-field input[type="range"]::-webkit-slider-thumb {
      appearance: none;
      width: 18px;
      height: 18px;
      margin-top: -5px;
      border: 2px solid #FFFFFF;
      border-radius: 50%;
      background: linear-gradient(180deg, #FDBA74, var(--studio-range-thumb));
      box-shadow: 0 2px 8px rgba(28, 25, 23, .18);
      transition: transform .12s ease, box-shadow .12s ease;
    }

    .ui-studio .studio-range-field input[type="range"]:active::-webkit-slider-thumb {
      transform: scale(1.08);
      box-shadow: 0 3px 12px color-mix(in srgb, var(--studio-accent) 34%, transparent);
    }

    .ui-studio .studio-range-field input[type="range"]::-moz-range-track {
      height: 8px;
      border-radius: 999px;
      background: var(--studio-range-track);
    }

    .ui-studio .studio-range-field input[type="range"]::-moz-range-progress {
      height: 8px;
      border-radius: 999px;
      background: var(--studio-range-fill);
    }

    .ui-studio .studio-range-field input[type="range"]::-moz-range-thumb {
      width: 16px;
      height: 16px;
      border: 2px solid #FFFFFF;
      border-radius: 50%;
      background: var(--studio-range-thumb);
      box-shadow: 0 2px 8px rgba(28, 25, 23, .18);
    }

    .studio-range-output {
      min-width: 44px;
      padding: 4px 8px;
      border: 1px solid color-mix(in srgb, var(--ui-accent) 24%, var(--ui-border));
      border-radius: 8px;
      background: color-mix(in srgb, var(--ui-accent) 8%, #FFFFFF);
      color: var(--ui-accent);
      font-size: 11px;
      font-weight: 800;
      text-align: center;
      line-height: 1.2;
    }

    .studio-range-field input[type="range"] {
      width: 100%;
      min-height: 24px;
      margin: 0;
      padding: 0;
      border: 0;
      background: transparent;
      box-shadow: none;
      cursor: pointer;
      --range-progress: 50%;
    }

    .studio-range-field input[type="range"]::-webkit-slider-runnable-track {
      height: 8px;
      border-radius: 999px;
      background: linear-gradient(90deg, var(--ui-accent) 0%, var(--ui-accent) var(--range-progress), #E2E8F0 var(--range-progress), #E2E8F0 100%);
      box-shadow: inset 0 1px 2px rgba(15, 23, 42, .06);
    }

    .studio-range-field input[type="range"]::-webkit-slider-thumb {
      appearance: none;
      width: 18px;
      height: 18px;
      margin-top: -5px;
      border: 2px solid #FFFFFF;
      border-radius: 50%;
      background: linear-gradient(180deg, color-mix(in srgb, var(--ui-accent) 82%, #FFFFFF), var(--ui-accent));
      box-shadow: 0 2px 8px rgba(15, 23, 42, .18);
      transition: transform .12s ease, box-shadow .12s ease;
    }

    .studio-range-field input[type="range"]:active::-webkit-slider-thumb {
      transform: scale(1.08);
      box-shadow: 0 3px 12px color-mix(in srgb, var(--ui-accent) 34%, transparent);
    }

    .studio-range-field input[type="range"]::-moz-range-track {
      height: 8px;
      border-radius: 999px;
      background: #E2E8F0;
    }

    .studio-range-field input[type="range"]::-moz-range-progress {
      height: 8px;
      border-radius: 999px;
      background: var(--ui-accent);
    }

    .studio-range-field input[type="range"]::-moz-range-thumb {
      width: 16px;
      height: 16px;
      border: 2px solid #FFFFFF;
      border-radius: 50%;
      background: var(--ui-accent);
      box-shadow: 0 2px 8px rgba(15, 23, 42, .18);
    }

    .studio-range-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 10px;
    }

    @media (min-width: 720px) {
      .studio-range-grid.is-two-col {
        grid-template-columns: 1fr 1fr;
      }
    }

    .order-list {
      display: grid;
      gap: 14px;
    }

    .order-page-group {
      border: 1px solid var(--ui-border);
      border-radius: 14px;
      background: #FFFFFF;
      overflow: hidden;
      box-shadow: 0 1px 3px rgba(15, 23, 42, .05);
    }

    .order-page-head {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      gap: 10px;
      align-items: center;
      padding: 12px 14px;
      border-bottom: 1px solid var(--ui-border);
      background: linear-gradient(180deg, #F8FAFC, #FFFFFF);
    }

    .order-page-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 58px;
      min-height: 28px;
      padding: 0 10px;
      border-radius: 999px;
      background: color-mix(in srgb, var(--ui-accent) 12%, #FFFFFF);
      color: var(--ui-accent);
      font-size: 10px;
      font-weight: 800;
      letter-spacing: .06em;
      text-transform: uppercase;
    }

    .order-page-title {
      min-width: 0;
      color: var(--ui-text);
      font-size: 13px;
      font-weight: 800;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .order-page-count {
      color: var(--ui-muted);
      font-size: 11px;
      font-weight: 700;
      white-space: nowrap;
    }

    .order-page-list {
      display: grid;
      gap: 8px;
      padding: 10px;
      background: #FAFBFC;
    }

    .order-page-hint {
      margin: 0 0 10px;
      color: var(--ui-muted);
      font-size: 11px;
      font-weight: 600;
      line-height: 1.5;
    }

    .order-row {
      display: grid;
      grid-template-columns: 28px minmax(0, 1fr) 34px;
      gap: 10px;
      align-items: center;
      padding: 10px 12px;
      border: 1px solid var(--ui-border);
      border-radius: 10px;
      background: #FFFFFF;
      box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
      cursor: grab;
    }

    .order-row.is-dragging {
      opacity: .58;
      cursor: grabbing;
    }

    .order-row-index {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 24px;
      height: 24px;
      border-radius: 8px;
      background: #EEF2F7;
      color: var(--ui-muted);
      font-size: 11px;
      font-weight: 800;
    }

    .order-grip {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: var(--ui-muted);
    }

    .order-row-name {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      font-size: 12px;
      font-weight: 700;
      color: var(--ui-text);
    }

    .phone-shell.is-app-exit-pulse {
      animation: app-exit-pulse .55s ease;
    }

    @keyframes app-exit-pulse {
      0% { box-shadow: inset 0 0 0 0 rgba(239, 68, 68, 0); }
      35% { box-shadow: inset 0 0 0 2px rgba(239, 68, 68, .45); }
      100% { box-shadow: inset 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    .studio-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      min-height: 36px;
      padding: 0 12px;
      border: 1px solid var(--ui-border);
      border-radius: 8px;
      background: #FFFFFF;
      color: var(--ui-text);
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: none;
      transition: border-color .15s ease, background .15s ease, color .15s ease;
    }

    .studio-btn svg {
      width: 15px;
      height: 15px;
    }

    .studio-btn:hover,
    .studio-btn:focus {
      border-color: color-mix(in srgb, var(--ui-accent) 35%, var(--ui-border));
      background: #FAFAF9;
      color: var(--ui-text);
      box-shadow: none;
      outline: 0;
      transform: none;
    }

    .studio-btn-ghost {
      border-color: transparent;
      background: transparent;
      color: var(--ui-muted);
    }

    .studio-btn-ghost:hover,
    .studio-btn-ghost:focus {
      border-color: transparent;
      background: #F5F5F4;
      color: var(--ui-text);
    }

    .studio-btn:disabled {
      cursor: not-allowed;
      opacity: .55;
    }

    .studio-btn.primary {
      border-color: #9A3412;
      background: linear-gradient(180deg, #EA580C, #C2410C);
      color: #FFFFFF;
      box-shadow: 0 1px 2px rgba(28, 25, 23, .08), 0 4px 12px rgba(194, 65, 12, .24);
    }

    .studio-btn.primary:hover,
    .studio-btn.primary:focus {
      border-color: #9A3412;
      background: linear-gradient(180deg, #F97316, #EA580C);
      color: #FFFFFF;
    }

    .studio-status {
      min-width: 0;
      min-height: 0;
      margin: 0;
      padding: 0;
      border: 0;
      border-radius: 0;
      background: transparent;
      color: var(--ui-muted);
      font-size: 12px;
      font-weight: 500;
      line-height: 1.45;
    }

    .studio-status:not(:empty) {
      display: flex;
      align-items: flex-start;
      gap: 8px;
      padding: 10px 12px;
      border: 1px solid color-mix(in srgb, var(--ui-accent) 22%, var(--ui-border));
      border-radius: 8px;
      background: color-mix(in srgb, var(--ui-accent) 5%, #FFFFFF);
      color: #44403C;
    }

    .studio-status:not(:empty)::before {
      content: "";
      width: 8px;
      height: 8px;
      margin-top: 5px;
      border-radius: 50%;
      background: var(--ui-accent);
      flex: 0 0 auto;
      box-shadow: 0 0 0 3px color-mix(in srgb, var(--ui-accent) 16%, transparent);
    }

    .icon-picker {
      position: fixed;
      z-index: 120;
      display: none;
      width: min(320px, calc(100vw - 24px));
      max-height: min(520px, calc(100vh - 28px));
      padding: 14px;
      border: 1px solid var(--ui-border);
      border-radius: 12px;
      background: var(--ui-surface);
      box-shadow: 0 24px 48px rgba(15, 23, 42, .16), 0 0 0 1px rgba(15, 23, 42, .04);
      overflow: hidden;
    }

    .icon-picker.is-open {
      display: block;
    }

    .icon-picker-title {
      margin: 0 0 10px;
      color: var(--ui-muted);
      font-size: 11px;
      font-weight: 900;
      text-transform: uppercase;
    }

    .icon-picker-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 8px;
      max-height: min(430px, calc(100vh - 104px));
      overflow-y: auto;
      padding: 2px 4px 2px 2px;
      scrollbar-width: thin;
      scrollbar-color: var(--ui-accent) color-mix(in srgb, var(--ui-border) 45%, transparent);
    }

    .icon-picker-grid::-webkit-scrollbar {
      width: 8px;
    }

    .icon-picker-grid::-webkit-scrollbar-track {
      border-radius: 999px;
      background: color-mix(in srgb, var(--ui-border) 45%, transparent);
    }

    .icon-picker-grid::-webkit-scrollbar-thumb {
      border-radius: 999px;
      background: var(--ui-accent);
    }

    .icon-choice {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 44px;
      height: 40px;
      border: 1px solid var(--ui-border);
      border-radius: 12px;
      background: linear-gradient(he 180deg, var(--ui-surface), color-mix(in srgb, var(--ui-shell) 30%, var(--ui-surface)));
      color: var(--ui-text);
      cursor: pointer;
      box-shadow: 0 8px 16px rgba(18, 50, 70, .055);
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
    }

    .icon-choice:hover,
    .icon-choice.is-active {
      border-color: var(--ui-accent);
      color: var(--ui-accent);
      background: color-mix(in srgb, var(--ui-accent) 10%, var(--ui-surface));
      box-shadow: 0 12px 22px color-mix(in srgb, var(--ui-accent) 18%, transparent);
      transform: translateY(-1px);
    }

    .component-picker {
      position: fixed;
      z-index: 121;
      display: none;
      flex-direction: column;
      width: min(360px, calc(100vw - 24px));
      max-height: min(560px, calc(100dvh - 28px));
      padding: 14px;
      border: 1px solid var(--ui-border);
      border-radius: 12px;
      background: var(--ui-surface);
      box-shadow: 0 24px 48px rgba(15, 23, 42, .16), 0 0 0 1px rgba(15, 23, 42, .04);
      overflow: hidden;
    }

    .component-picker.is-open {
      display: flex;
    }

    .component-picker-title {
      margin: 0 0 10px;
      color: var(--ui-muted);
      font-size: 11px;
      font-weight: 900;
      text-transform: uppercase;
    }

    .component-picker-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
      flex: 1 1 auto;
      min-height: 0;
      overflow-x: hidden;
      overflow-y: auto;
      padding: 2px 4px 2px 2px;
      overscroll-behavior: contain;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: thin;
      scrollbar-color: var(--ui-accent) color-mix(in srgb, var(--ui-border) 45%, transparent);
    }

    .component-choice {
      display: flex;
      flex-direction: column;
      align-items: stretch;
      gap: 8px;
      min-height: 92px;
      padding: 10px;
      border: 1px solid var(--ui-border);
      border-radius: 14px;
      background: linear-gradient(180deg, var(--ui-surface), color-mix(in srgb, var(--ui-shell) 30%, var(--ui-surface)));
      color: var(--ui-text);
      cursor: pointer;
      text-align: left;
      box-shadow: 0 8px 16px rgba(18, 50, 70, .055);
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
    }

    .component-choice:hover,
    .component-choice.is-active {
      border-color: var(--ui-accent);
      background: color-mix(in srgb, var(--ui-accent) 8%, var(--ui-surface));
      box-shadow: 0 12px 22px color-mix(in srgb, var(--ui-accent) 18%, transparent);
      transform: translateY(-1px);
    }

    .component-choice-preview {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 38px;
      padding: 6px;
      border-radius: 10px;
      background: color-mix(in srgb, var(--ui-accent) 6%, var(--ui-shell));
    }

    .component-choice-label {
      font-size: 12px;
      font-weight: 900;
      color: var(--ui-text);
    }

    .component-choice-desc {
      font-size: 10px;
      font-weight: 700;
      color: var(--ui-muted);
      line-height: 1.3;
    }

    .component-preview-switch {
      position: relative;
      width: 40px;
      height: 22px;
      border-radius: 999px;
      background: #CBD5E1;
    }

    .component-preview-switch::after {
      content: "";
      position: absolute;
      top: 2px;
      left: 2px;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: #FFFFFF;
      box-shadow: 0 2px 6px rgba(15, 37, 80, .18);
    }

    .component-preview-slider {
      width: 100%;
      max-width: 120px;
      height: 8px;
      border-radius: 999px;
      background: #E2E8F0;
      position: relative;
      overflow: hidden;
    }

    .component-preview-slider::before {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 55%;
      border-radius: inherit;
      background: linear-gradient(90deg, color-mix(in srgb, var(--ui-accent) 70%, #fff), var(--ui-accent));
    }

    .component-preview-slider::after {
      content: "";
      position: absolute;
      left: 55%;
      top: 50%;
      width: 16px;
      height: 16px;
      border-radius: 50%;
      background: var(--ui-accent);
      border: 2px solid #fff;
      transform: translate(-50%, -50%);
      box-shadow: 0 2px 8px rgba(15, 37, 80, .2);
    }

    .component-preview-button {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: linear-gradient(145deg, var(--ui-accent), color-mix(in srgb, var(--ui-accent) 75%, #0F2550));
      box-shadow: inset 0 0 0 2px rgba(255, 255, 255, .35), 0 4px 10px color-mix(in srgb, var(--ui-accent) 35%, transparent);
    }

    .component-preview-value {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      min-width: 42px;
      height: 28px;
      padding: 0 8px;
      border-radius: 999px;
      background: rgba(87, 167, 115, .16);
      color: var(--ui-accent);
      font-size: 11px;
      font-weight: 900;
    }

    .component-preview-dropdown {
      min-width: 88px;
      height: 28px;
      padding: 0 10px;
      border-radius: 10px;
      border: 1px solid var(--ui-border);
      background: var(--ui-surface);
      color: var(--ui-text);
      font-size: 11px;
      font-weight: 800;
      display: inline-flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
    }

    .component-preview-segmented {
      display: inline-flex;
      gap: 3px;
      padding: 3px;
      border-radius: 9px;
      background: rgba(106, 142, 174, .18);
    }

    .component-preview-segmented span {
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 10px;
      font-weight: 800;
      color: var(--ui-muted);
    }

    .component-preview-segmented span.is-active {
      background: var(--ui-accent);
      color: #fff;
    }

    .component-preview-textField {
      width: 100%;
      max-width: 120px;
      height: 28px;
      border-radius: 10px;
      border: 1px solid var(--ui-border);
      background: var(--ui-surface);
    }

    .component-preview-auto {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      border: 2px dashed var(--ui-accent);
      color: var(--ui-accent);
      font-size: 14px;
      font-weight: 900;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .component-preview-registered {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      min-width: 88px;
      height: 28px;
      padding: 0 8px;
      border-radius: 10px;
      background: #E8F7EE;
      border: 1px solid #B7E3C7;
    }

    .component-preview-registered-icon {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: #157145;
      flex-shrink: 0;
    }

    .component-preview-registered-text {
      flex: 1;
      height: 8px;
      border-radius: 999px;
      background: rgba(16, 42, 58, .18);
    }

    .component-preview-heartRate {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      min-width: 72px;
      height: 28px;
      padding: 0 8px;
      border-radius: 10px;
      background: #FFF0F3;
      border: 1px solid #FECDD3;
    }

    .component-preview-heartRate-icon {
      width: 12px;
      height: 12px;
      flex-shrink: 0;
      background: #E11D48;
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='black' d='M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z'/%3E%3C/svg%3E") center / contain no-repeat;
      -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='black' d='M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z'/%3E%3C/svg%3E") center / contain no-repeat;
    }

    .component-preview-heartRate-value {
      font-size: 12px;
      font-weight: 800;
      color: #881337;
      line-height: 1;
    }

    .component-preview-elevatedButton {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 46px;
      height: 28px;
      padding: 0 10px;
      border-radius: 10px;
      background: var(--ui-accent);
      color: #fff;
      font-size: 11px;
      font-weight: 800;
    }

    .component-preview-linearGauge {
      display: inline-flex;
      align-items: center;
      width: 88px;
      height: 10px;
      border-radius: 999px;
      background: #E2E8F0;
      overflow: hidden;
    }

    .component-preview-linearGauge-fill {
      display: block;
      width: 72%;
      height: 100%;
      background: var(--ui-accent);
    }

    .component-preview-firePanel {
      display: inline-flex;
      gap: 4px;
      height: 28px;
      align-items: center;
    }

    .component-preview-firePanel span {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 40px;
      height: 22px;
      border-radius: 8px;
      font-size: 10px;
      font-weight: 800;
      color: #fff;
    }

    .component-preview-firePanel span:first-child { background: #16A34A; }
    .component-preview-firePanel span:last-child { background: #DC2626; }

    .component-preview-toggleButton,
    .component-preview-iconButton {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 54px;
      height: 28px;
      padding: 0 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 800;
    }

    .component-preview-toggleButton { background: #EEF2FF; color: #4338CA; border: 1px solid #C7D2FE; }
    .component-preview-iconButton { background: #F0F9FF; color: #0369A1; border: 1px solid #BAE6FD; }

    .component-preview-sliderWithLabel {
      display: inline-flex;
      flex-direction: column;
      gap: 4px;
      min-width: 72px;
    }

    .component-preview-sliderWithLabel > span:first-child {
      font-size: 11px;
      font-weight: 800;
      color: var(--ui-text);
    }

    .component-preview-sliderWithLabel-bar {
      display: block;
      width: 100%;
      height: 6px;
      border-radius: 999px;
      background: linear-gradient(90deg, var(--ui-accent) 50%, #E2E8F0 50%);
    }

    .component-preview-buttonGroup {
      display: inline-flex;
      gap: 3px;
      height: 28px;
      align-items: center;
    }

    .component-preview-buttonGroup span {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 22px;
      height: 22px;
      border-radius: 8px;
      background: #EDE9FE;
      color: #5B21B6;
      font-size: 11px;
      font-weight: 800;
    }

    .component-preview-buttonGroup span.is-active {
      background: #7C3AED;
      color: #fff;
    }

    .elevated-action-btn {
      appearance: none;
      border: 0;
      min-height: 40px;
      padding: 0 18px;
      border-radius: 12px;
      background: linear-gradient(135deg, var(--ui-accent), color-mix(in srgb, var(--ui-accent) 78%, #0F2550));
      color: #fff;
      font: inherit;
      font-size: 12px;
      font-weight: 800;
      letter-spacing: .04em;
      cursor: pointer;
      box-shadow: 0 8px 20px color-mix(in srgb, var(--ui-accent) 34%, transparent);
      transition: transform .14s ease, box-shadow .18s ease, filter .18s ease;
    }

    .elevated-action-btn.is-pressed {
      transform: scale(.96);
      filter: brightness(.94);
      box-shadow: 0 4px 12px color-mix(in srgb, var(--ui-accent) 24%, transparent);
    }

    .linear-gauge-visual {
      display: grid;
      gap: 8px;
      width: 100%;
      flex: 0 0 auto;
      padding: 10px 12px;
      border-radius: 12px;
      background: #F8FAFC;
      border: 1px solid #E2E8F0;
      box-sizing: border-box;
      overflow: visible;
    }

    .linear-gauge-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
    }

    .linear-gauge-label {
      font-size: 11px;
      font-weight: 800;
      color: var(--ui-muted);
      text-transform: uppercase;
    }

    .linear-gauge-value {
      font-size: 14px;
      font-weight: 800;
      color: var(--ui-text);
    }

    .linear-gauge-track {
      width: 100%;
      height: 10px;
      border-radius: 999px;
      background: #E2E8F0;
      overflow: hidden;
    }

    .linear-gauge-fill {
      height: 100%;
      border-radius: inherit;
      background: linear-gradient(90deg, var(--ui-secondary, #9BD1E5), var(--ui-accent));
    }

    .registered-heart-icon {
      animation: registered-heart-pulse 1.2s ease-in-out infinite;
      transform-origin: center;
    }

    @keyframes registered-heart-pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.08); }
    }

    .registered-visual[data-registered-type="heartRateDisplay"] .registered-node--text {
      line-height: 1.1;
    }

    .registered-visual[data-registered-type="toggleButton"],
    .registered-visual[data-registered-type="buttonGroup"],
    .registered-visual[data-registered-type="sliderWithLabel"],
    .registered-visual[data-registered-type="fireControlPanel"] {
      display: block;
      padding: 0;
      background: transparent;
      border: 0;
    }

    .registered-interactive {
      width: 100%;
      padding: 12px 14px;
      border-radius: 14px;
      border: 1px solid #CBD5E1;
      background: #F8FAFC;
    }

    .registered-interactive--toggle {
      display: flex;
      align-items: center;
      gap: 12px;
      cursor: pointer;
      border-color: #C7D2FE;
      background: #EEF2FF;
      user-select: none;
    }

    .registered-interactive--toggle.is-locked {
      border-color: #FECACA;
      background: #FEF2F2;
    }

    .registered-interactive--toggle .registered-toggle-icon {
      color: #4338CA;
    }

    .registered-interactive--toggle.is-locked .registered-toggle-icon {
      color: #DC2626;
    }

    .registered-interactive-kicker {
      display: block;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .04em;
      color: #64748B;
    }

    .registered-interactive-value {
      display: block;
      font-size: 16px;
      font-weight: 700;
      color: #0F172A;
    }

    .registered-button-group {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-top: 8px;
    }

    .registered-group-btn {
      border: 0;
      border-radius: 10px;
      padding: 8px 14px;
      font-size: 13px;
      font-weight: 800;
      cursor: pointer;
      background: #EDE9FE;
      color: #5B21B6;
      transition: background .15s ease, color .15s ease, transform .12s ease;
    }

    .registered-group-btn.is-active {
      background: #7C3AED;
      color: #FFFFFF;
    }

    .registered-group-btn:active {
      transform: scale(.97);
    }

    .registered-group-btn--start.is-active {
      background: #16A34A;
      color: #FFFFFF;
    }

    .registered-group-btn--stop.is-active {
      background: #DC2626;
      color: #FFFFFF;
    }

    .registered-slider-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 10px;
    }

    .registered-slider-track {
      position: relative;
      height: 8px;
      border-radius: 999px;
      background: #E2E8F0;
      overflow: hidden;
    }

    .registered-slider-fill {
      position: absolute;
      inset: 0 auto 0 0;
      width: 0;
      border-radius: inherit;
      background: linear-gradient(90deg, var(--ui-accent), var(--ui-blue));
      pointer-events: none;
    }

    .registered-slider-input {
      position: relative;
      z-index: 1;
      width: 100%;
      margin: 0;
      height: 8px;
      background: transparent;
      -webkit-appearance: none;
      appearance: none;
    }

    .registered-slider-input::-webkit-slider-thumb {
      -webkit-appearance: none;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      border: 2px solid #FFFFFF;
      background: var(--ui-accent);
      box-shadow: 0 2px 8px rgba(15, 37, 80, .22);
      cursor: pointer;
    }

    .registered-slider-input::-moz-range-thumb {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      border: 2px solid #FFFFFF;
      background: var(--ui-accent);
      box-shadow: 0 2px 8px rgba(15, 37, 80, .22);
      cursor: pointer;
    }

    .param-component-edit {
      width: 100%;
      justify-content: flex-start;
      gap: 10px;
    }

    .param-component-edit .component-choice-preview {
      flex: 0 0 auto;
      min-width: 54px;
      min-height: 34px;
      padding: 4px 8px;
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
      width: var(--ui-icon-size);
      height: var(--ui-icon-size);
      flex: 0 0 var(--ui-icon-size);
      stroke: currentColor;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
      fill: none;
    }

    .save-toast {
      position: fixed;
      left: 50%;
      bottom: 24px;
      z-index: 160;
      display: none;
      min-width: 210px;
      padding: 12px 16px;
      border: 1px solid color-mix(in srgb, var(--ui-success) 40%, var(--ui-border));
      border-radius: 14px;
      background: color-mix(in srgb, var(--ui-success) 14%, var(--ui-surface));
      color: var(--ui-text);
      font-size: 13px;
      font-weight: 900;
      text-align: center;
      box-shadow: 0 18px 40px rgba(18, 50, 70, .18);
      transform: translateX(-50%);
    }

    .save-toast.is-visible {
      display: block;
    }

    .phone-shell.is-showing-info {
      display: flex;
      flex-direction: column;
      overflow: hidden;
      min-height: 0;
    }

    .phone-shell.is-showing-info > .device-details-view {
      display: flex;
      flex-direction: column;
      flex: 1 1 auto;
      min-height: 0;
      width: 100%;
      overflow: hidden;
    }

    .phone-shell.is-showing-info > .device-details-view .device-details-body {
      flex: 1 1 auto;
      min-height: 0;
      max-height: 100%;
      overflow-x: hidden;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
      overscroll-behavior: contain;
      touch-action: pan-y;
    }

    .device-details-view {
      display: none;
      grid-row: 1 / -1;
      grid-column: 1;
      z-index: 30;
      flex-direction: column;
      min-height: 0;
      overflow: hidden;
      background: var(--ui-bg);
      color: var(--ui-text);
    }

    .phone-shell.is-showing-info > .app-topbar,
    .phone-shell.is-showing-info > .brand-strip,
    .phone-shell.is-showing-info > .slider-shell,
    .phone-shell.is-showing-info > .empty-ui,
    .phone-shell.is-showing-info > .footer-actions {
      display: none !important;
    }

    .phone-shell.is-showing-info > .device-details-view {
      display: flex;
    }

    .device-details-head {
      flex: 0 0 auto;
      z-index: 4;
      display: grid;
      grid-template-columns: 46px minmax(0, 1fr) 46px;
      align-items: center;
      min-height: var(--ui-header-height);
      padding: 10px 14px;
      border-bottom: var(--ui-border-size) solid var(--ui-border-color);
      background: color-mix(in srgb, var(--ui-surface) 94%, var(--ui-bg));
      box-shadow: 0 8px 24px rgba(18, 50, 70, .08);
    }

    .device-details-back {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      padding: 0;
      border: 0;
      border-radius: 999px;
      background: color-mix(in srgb, var(--ui-accent) 10%, transparent);
      color: var(--ui-accent);
      cursor: pointer;
      box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--ui-accent) 18%, transparent);
      transition: background .18s ease, transform .14s ease;
    }

    .device-details-back:hover,
    .device-details-back:focus {
      background: color-mix(in srgb, var(--ui-accent) 16%, transparent);
      outline: none;
    }

    .device-details-back:active {
      transform: scale(.96);
    }

    .device-details-head h2 {
      margin: 0;
      color: var(--ui-text);
      font-size: 18px;
      line-height: 1.2;
      font-weight: 800;
      letter-spacing: 0;
    }

    .device-details-body {
      display: flex;
      flex-direction: column;
      gap: var(--ui-card-spacing);
      flex: 1 1 auto;
      min-height: 0;
      overflow-x: hidden;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
      overscroll-behavior: contain;
      touch-action: pan-y;
      padding: var(--ui-page-padding);
    }

    .device-details-group {
      overflow: hidden;
      border: var(--ui-border-size) solid var(--ui-border-color);
      border-radius: var(--ui-radius);
      background: var(--ui-surface);
      box-shadow: var(--ui-card-shadow);
      backdrop-filter: blur(var(--ui-card-blur));
    }

    .device-detail-row {
      position: relative;
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      align-items: center;
      gap: 14px;
      min-height: 72px;
      padding: 14px var(--ui-card-padding);
      border-bottom: var(--ui-border-size) solid var(--ui-border-color);
      background: color-mix(in srgb, var(--ui-shell) 22%, var(--ui-surface));
    }

    .device-detail-row:last-child {
      border-bottom: 0;
    }

    .device-detail-copy {
      min-width: 0;
    }

    .device-detail-label {
      display: block;
      color: var(--ui-text);
      font-size: 14px;
      line-height: 1.25;
      font-weight: 700;
      letter-spacing: 0;
    }

    .device-detail-value {
      display: block;
      margin-top: 7px;
      color: var(--ui-muted);
      font-size: 11px;
      line-height: 1.4;
      font-weight: 600;
      letter-spacing: 0;
      overflow-wrap: anywhere;
    }

    .device-detail-side-value {
      max-width: 160px;
      color: var(--ui-muted);
      font-size: 11px;
      font-weight: 600;
      text-align: right;
      overflow-wrap: anywhere;
    }

    .device-detail-action {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 38px;
      height: 38px;
      padding: 0;
      border: 0;
      border-radius: 50%;
      background: color-mix(in srgb, var(--ui-accent) 10%, transparent);
      color: var(--ui-accent);
      cursor: pointer;
      text-decoration: none;
    }

    .device-detail-action .icon {
      width: 19px;
      height: 19px;
    }

    .info-modal-state {
      padding: 24px 12px;
      color: var(--ui-muted);
      font-size: 13px;
      font-weight: 800;
      text-align: center;
    }

    @media (min-width: 720px) {
      body {
        padding: 0;
        overflow: hidden;
      }

      .phone-shell {
        height: var(--studio-phone-height);
        max-height: var(--studio-phone-height);
        min-height: var(--studio-phone-height);
        border-radius: 28px;
      }
    }

    @media (max-width: 1180px) {
      .studio-workspace {
        grid-template-columns: 220px minmax(320px, 1fr) minmax(320px, 380px);
      }
    }

    @media (max-width: 860px) {
      .studio-topbar {
        flex-wrap: wrap;
        padding: 12px 16px;
        gap: 12px;
      }

      .studio-topbar-left {
        width: 100%;
      }

      .studio-top-actions {
        width: 100%;
        justify-content: flex-start;
      }

      .ui-studio {
        grid-template-rows: auto minmax(0, 1fr);
      }

      .studio-workspace {
        grid-template-columns: 1fr;
        grid-template-areas:
          "preview"
          "panel";
        overflow: auto;
      }

      .studio-settings-column {
        border-left: 0;
        border-top: 1px solid var(--ui-border);
      }

      .studio-sections {
        display: none;
      }

      .studio-controls,
      .studio-preview-column,
      .studio-settings-column {
        border-right: 0;
      }

      .studio-grid {
        grid-template-columns: 1fr;
      }

      .studio-title p {
        white-space: normal;
      }

      .studio-preview-frame {
        width: min(calc(var(--studio-phone-width) + 20px), calc(100vw - 40px));
      }

      body.studio-open .ui-studio .phone-shell {
        width: min(var(--studio-phone-width), calc(100vw - 60px));
        max-width: min(var(--studio-phone-width), calc(100vw - 60px));
      }
    }
  </style>
  

</head>
<body>
  <nav class="desktop-flow-actions" aria-label="Linked UI actions">
    <a href="<?= get_uri('items/vendor') ?>" class="desktop-flow-btn"><?= view_ui_icon('chevron-left') ?> Back</a>
    <button type="button" class="desktop-flow-btn desktop-flow-studio" id="open-studio-btn"><?= view_ui_icon('palette') ?> Studio</button>
  </nav>
  <div class="page-preview-shell" id="page-preview-shell">
    <div class="page-preview-head">
      <span class="page-preview-title">Live Preview</span>
      <div class="page-preview-head-tools">
        <div class="studio-preview-zoom" aria-label="Preview zoom controls">
          <button type="button" class="studio-preview-zoom-btn" id="page-preview-zoom-out" aria-label="Zoom out"><?= view_ui_icon('minus') ?></button>
          <span class="studio-preview-zoom-label" id="page-preview-zoom-label">100%</span>
          <button type="button" class="studio-preview-zoom-btn" id="page-preview-zoom-in" aria-label="Zoom in"><?= view_ui_icon('plus') ?></button>
          <button type="button" class="studio-preview-zoom-btn studio-preview-zoom-fit" id="page-preview-zoom-fit" aria-label="Fit preview to canvas"><?= view_ui_icon('maximize') ?> Fit</button>
        </div>
        <span class="page-preview-size">375 × 812 · Standard mobile</span>
      </div>
    </div>
    <div class="page-preview-mount" id="page-preview-mount">
      <div class="page-preview-frame" id="page-preview-frame">
  <main class="phone-shell">
    
    <header class="app-topbar">
      <button type="button" class="app-back-btn" id="phone-back-btn" aria-label="Back">
        <?= view_ui_icon('chevron-left') ?>
        <span class="app-back-label">Back</span>
      </button>

      <div class="top-title">
        <h1 id="active-page-title"><?= esc(!empty($pages[0]['page_name']) ? $pages[0]['page_name'] : ($modelno ?: 'Model UI')) ?></h1>
        <p><?= esc($modelno ?: 'Model UI') ?></p>
        <p id="active-page-subtitle"><?= $is_row_view ? 'Selected Row UI' : (!empty($pages[0]['page_no']) ? 'Page' . esc($pages[0]['page_no']) . '.Operation' : 'No pages') ?></p>
      </div>

      <div class="top-actions">
        <button type="button" class="icon-btn" id="device-info-btn" aria-label="Information">
          <?= view_ui_icon('info') ?>
        </button>
        <!-- <a class="icon-btn" href="<?= get_uri('items/api_page_linking/' . (int) $modal_id . '?modelno=' . rawurlencode($modelno)) ?>" aria-label="Edit UI">
          <?= view_ui_icon('settings') ?>
        </a> -->
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
                    $paramKey = $page['page_no'] . ':' . $paramName;
                    $paramIcon = !empty($active_ui_theme['parameterIcons'][$paramKey]) ? $active_ui_theme['parameterIcons'][$paramKey] : $active_ui_theme['parameterIcon'];
                    ?>
                    <div class="param-card<?= $isTimer ? ' layout-stacked' : '' ?>" data-param-key="<?= esc($paramKey) ?>" data-param-name="<?= esc($paramName ?: 'Parameter') ?>" data-sequence="<?= esc($param['sequence']) ?>" data-api-meta="<?= esc(json_encode($param['api'] ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) ?>">
                      <div class="param-main-row">
                        <button type="button" class="param-icon" data-icon-picker-trigger="1" data-icon="<?= esc($paramIcon) ?>" aria-label="Change <?= esc($paramName ?: 'parameter') ?> icon"><?= view_ui_icon($paramIcon) ?></button>
                        <div>
                          <span class="field-label"><?= esc($paramName ?: 'Parameter') ?></span>
                          <span class="field-value"><?= $isPower ? 'ON' : esc($paramName ?: 'N/A') ?></span>
                        </div>

                        <?php if ($isPower): ?>
                          <span class="toggle-visual" aria-hidden="true"></span>
                        <?php elseif ($isTrigger): ?>
                          <span class="action-dot" aria-hidden="true"></span>
                        <?php else: ?>
                          <button type="button" class="param-meta" data-meta-icon-picker-trigger="1" data-meta-icon="gauge" aria-label="Change <?= esc($paramName ?: 'parameter') ?> meta icon">
                            <?= view_ui_icon('gauge') ?>
                            <span class="meta-value"><?= esc($param['sequence']) ?></span>
                          </button>
                        <?php endif; ?>
                      </div>

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

    <section class="device-details-view" id="device-info-modal" aria-hidden="true" aria-labelledby="device-info-title">
      <header class="device-details-head">
        <button type="button" class="device-details-back" id="device-info-close-btn" aria-label="Back to controls">
          <?= view_ui_icon('chevron-left') ?>
        </button>
        <h2 id="device-info-title">Gateway Details</h2>
        <span aria-hidden="true"></span>
      </header>
      <div class="device-details-body" id="device-info-content">
        <div class="info-modal-state">Select Information to load device details.</div>
      </div>
    </section>
  </main>
      </div>
    </div>
  </div>

  <div class="studio-backdrop" id="studio-backdrop"></div>
  <aside class="ui-studio" id="ui-studio" aria-label="UI Customization Studio" aria-hidden="true">
    <div class="studio-topbar">
      <div class="studio-topbar-left">
        <button type="button" class="studio-btn studio-btn-ghost" id="close-studio-btn"><?= view_ui_icon('chevron-left') ?> Back</button>
        <div class="studio-title">
          <span class="studio-brand-badge">UI</span>
          <div>
            <h2>Design Studio</h2>
            <p>Customize mobile UI — colors, typography, parameters &amp; layout</p>
          </div>
        </div>
      </div>

      <div class="studio-top-actions">
        <button type="button" class="studio-btn" id="reset-theme-btn"><?= view_ui_icon('settings') ?> Reset</button>
        <button type="button" class="studio-btn" id="generate-schema-btn"><?= view_ui_icon('code') ?> Schema</button>
        <span class="studio-action-divider" aria-hidden="true"></span>
        <button type="button" class="studio-btn primary" id="save-theme-btn"><?= view_ui_icon('save') ?> Save Changes</button>
      </div>
    </div>

    <div class="studio-workspace">
      <nav class="studio-sections" aria-label="Studio sections">
        <p class="studio-section-kicker">Workspace</p>
        <p class="studio-nav-divider">Foundation</p>
        <button type="button" class="studio-nav-btn is-active" data-studio-target="studio-theme-section"><?= view_ui_icon('palette') ?><span>Themes<small>Presets and mobile colors</small></span></button>
        <button type="button" class="studio-nav-btn" data-studio-target="studio-typography-section"><?= view_ui_icon('file') ?><span>Typography<small>Text styles and fonts</small></span></button>
        <p class="studio-nav-divider">Components</p>
        <button type="button" class="studio-nav-btn" data-studio-target="studio-params-section"><?= view_ui_icon('gauge') ?><span>Parameters<small>Buttons and icons</small></span></button>
        <button type="button" class="studio-nav-btn" data-studio-target="studio-page-section"><?= view_ui_icon('layers') ?><span>Header<small>Page and header</small></span></button>
        <button type="button" class="studio-nav-btn" data-studio-target="studio-general-section"><?= view_ui_icon('settings') ?><span>Cards<small>Shape, shadow and borders</small></span></button>
        <button type="button" class="studio-nav-btn" data-studio-target="studio-order-section"><?= view_ui_icon('move') ?><span>Layout<small>Spacing, visibility and order</small></span></button>
        <p class="studio-nav-divider">Output</p>
        <button type="button" class="studio-nav-btn" data-studio-target="studio-schema-section"><?= view_ui_icon('code') ?><span>Schema<small>Device UI JSON</small></span></button>
      </nav>

      <div class="studio-preview-column">
        <div class="studio-preview-head">
          <span>Live Preview</span>
          <div class="studio-preview-head-tools">
            <div class="studio-preview-zoom" aria-label="Preview zoom controls">
              <button type="button" class="studio-preview-zoom-btn" id="studio-preview-zoom-out" aria-label="Zoom out"><?= view_ui_icon('minus') ?></button>
              <span class="studio-preview-zoom-label" id="studio-preview-zoom-label">100%</span>
              <button type="button" class="studio-preview-zoom-btn" id="studio-preview-zoom-in" aria-label="Zoom in"><?= view_ui_icon('plus') ?></button>
              <button type="button" class="studio-preview-zoom-btn studio-preview-zoom-fit" id="studio-preview-zoom-fit" aria-label="Fit preview to canvas"><?= view_ui_icon('maximize') ?> Fit</button>
            </div>
            <span class="studio-preview-size">375 × 812 · Standard mobile</span>
          </div>
        </div>
        <div class="studio-preview-mount" id="studio-preview-mount">
          <div class="studio-preview-frame" id="studio-preview-frame"></div>
        </div>
      </div>

      <div class="studio-settings-column">
        <div class="studio-panel-scroll">
      <div class="studio-controls has-active">
        <section class="studio-section is-active" id="studio-theme-section">
          <div class="studio-panel-body">
            <div class="studio-panel-header">
              <div>
                <h3>Theme &amp; Colors</h3>
                <p class="studio-panel-subtitle">Presets, background style, and full mobile color palette.</p>
              </div>
              <button type="button" class="studio-btn" id="reset-theme-inline-btn">Reset</button>
            </div>
            <div class="studio-panel-group">
              <p class="studio-panel-label">Preset themes</p>
              <div class="studio-preset-grid">
                <button type="button" class="studio-preset-card" data-preset="light"><span class="studio-preset-swatch studio-preset-swatch-light"></span><span class="studio-preset-name">Pro Light</span></button>
                <button type="button" class="studio-preset-card" data-preset="dark"><span class="studio-preset-swatch studio-preset-swatch-dark"></span><span class="studio-preset-name">Dark</span></button>
                <button type="button" class="studio-preset-card" data-preset="ocean"><span class="studio-preset-swatch studio-preset-swatch-ocean"></span><span class="studio-preset-name">Ocean</span></button>
                <button type="button" class="studio-preset-card" data-preset="nature"><span class="studio-preset-swatch studio-preset-swatch-nature"></span><span class="studio-preset-name">Nature</span></button>
                <button type="button" class="studio-preset-card" data-preset="glass"><span class="studio-preset-swatch studio-preset-swatch-glass"></span><span class="studio-preset-name">Glass</span></button>
              </div>
            </div>
            <div class="studio-panel-group">
              <p class="studio-panel-label">Background style</p>
              <div class="studio-segmented studio-segmented-full" data-segment-key="backgroundType">
                <button type="button" class="studio-segment" data-segment-value="solid">Solid</button>
                <button type="button" class="studio-segment" data-segment-value="gradient">Gradient</button>
              </div>
            </div>
            <div class="studio-panel-group">
              <p class="studio-panel-label">Mobile screen surfaces</p>
              <div class="studio-color-row"><label>Page background</label><input type="color" data-theme-key="backgroundColor"></div>
              <div class="studio-color-row"><label>Gradient end color</label><input type="color" data-theme-key="gradientColor"></div>
              <div class="studio-color-row"><label>Screen surface</label><input type="color" data-theme-key="shellColor"></div>
              <div class="studio-color-row"><label>Card background</label><input type="color" data-theme-key="cardColor"></div>
              <div class="studio-color-row"><label>Card border</label><input type="color" data-theme-key="borderColor"></div>
            </div>
            <div class="studio-panel-group">
              <p class="studio-panel-label">Text on mobile</p>
              <div class="studio-color-row"><label>Primary text</label><input type="color" data-theme-key="textColor"></div>
              <div class="studio-color-row"><label>Secondary text</label><input type="color" data-theme-key="mutedTextColor"></div>
            </div>
            <div class="studio-panel-group">
              <p class="studio-panel-label">Header &amp; accent</p>
              <div class="studio-color-row"><label>Top header bar</label><input type="color" data-theme-key="topbarColor"></div>
              <div class="studio-color-row"><label>Accent color</label><input type="color" data-theme-key="accentColor"></div>
              <div class="studio-color-row"><label>Secondary fill</label><input type="color" data-theme-key="secondaryColor"></div>
              <div class="studio-color-row"><label>Icon color</label><input type="color" data-theme-key="iconColor"></div>
            </div>
            <div class="studio-panel-group">
              <p class="studio-panel-label">Status &amp; controls</p>
              <div class="studio-color-row"><label>Success / online</label><input type="color" data-theme-key="successColor"></div>
              <div class="studio-color-row"><label>Warning</label><input type="color" data-theme-key="warningColor"></div>
              <div class="studio-color-row"><label>Error</label><input type="color" data-theme-key="errorColor"></div>
              <div class="studio-color-row"><label>Switch on</label><input type="color" data-theme-key="toggleOnColor"></div>
              <div class="studio-color-row"><label>Switch off</label><input type="color" data-theme-key="toggleOffColor"></div>
            </div>
            <div class="studio-panel-group">
              <p class="studio-panel-label">Footer button</p>
              <div class="studio-color-row"><label>Button background</label><input type="color" data-theme-key="buttonBgColor"></div>
              <div class="studio-color-row"><label>Button text</label><input type="color" data-theme-key="buttonTextColor"></div>
            </div>
          </div>
        </section>

        <section class="studio-section" id="studio-typography-section">
          <div class="studio-panel-body">
            <div class="studio-panel-header">
              <div>
                <h3>Typography</h3>
                <p class="studio-panel-subtitle">Font family, size, weight and alignment for the mobile app.</p>
              </div>
            </div>
            <div class="studio-panel-group">
              <div class="studio-field">
                <label for="studio-fontFamily">Font family</label>
                <select id="studio-fontFamily" data-theme-key="fontFamily">
                  <option value="Inter">Inter</option>
                  <option value="Poppins">Poppins</option>
                  <option value="Roboto">Roboto</option>
                  <option value="Segoe UI">Segoe UI</option>
                  <option value="Arial">Arial</option>
                  <option value="Verdana">Verdana</option>
                  <option value="Tahoma">Tahoma</option>
                  <option value="Trebuchet MS">Trebuchet MS</option>
                  <option value="Georgia">Georgia</option>
                  <option value="Courier New">Courier New</option>
                </select>
              </div>
              <div class="studio-range-field">
                <div class="studio-range-top">
                  <label for="studio-fontSize">Font size</label>
                  <output class="studio-range-output" for="studio-fontSize">15px</output>
                </div>
                <div class="studio-range-track-wrap">
                  <input id="studio-fontSize" type="range" min="8" max="36" step="1" data-theme-key="fontSize">
                </div>
              </div>
              <div class="studio-range-field">
                <div class="studio-range-top">
                  <label for="studio-fontWeight">Font weight</label>
                  <output class="studio-range-output" for="studio-fontWeight">500</output>
                </div>
                <div class="studio-range-track-wrap">
                  <input id="studio-fontWeight" type="range" min="100" max="900" step="100" data-theme-key="fontWeight">
                </div>
              </div>
              <div class="studio-range-field">
                <div class="studio-range-top">
                  <label for="studio-letterSpacing">Letter spacing</label>
                  <output class="studio-range-output" for="studio-letterSpacing">0px</output>
                </div>
                <div class="studio-range-track-wrap">
                  <input id="studio-letterSpacing" type="range" min="-2" max="12" step="0.1" data-theme-key="letterSpacing">
                </div>
              </div>
              <div class="studio-range-field">
                <div class="studio-range-top">
                  <label for="studio-lineHeight">Line height</label>
                  <output class="studio-range-output" for="studio-lineHeight">1.5</output>
                </div>
                <div class="studio-range-track-wrap">
                  <input id="studio-lineHeight" type="range" min="0.8" max="3" step="0.05" data-theme-key="lineHeight">
                </div>
              </div>
              <div class="studio-field">
                <label>Text align</label>
                <select data-theme-key="textAlign">
                  <option value="left">Left</option>
                  <option value="center">Center</option>
                  <option value="right">Right</option>
                </select>
              </div>
            </div>
          </div>
        </section>

        <section class="studio-section" id="studio-params-section">
          <div class="studio-panel-body">
            <div class="studio-panel-header">
              <div>
                <h3>Parameters</h3>
                <p class="studio-panel-subtitle">Choose one parameter, then edit label, value, icons, colors and visibility.</p>
              </div>
            </div>
            <div id="params-buttons-container"></div>
          </div>
        </section>

        <section class="studio-section" id="studio-general-section">
          <div class="studio-panel-body">
            <div class="studio-panel-header">
              <div>
                <h3>Card Style</h3>
                <p class="studio-panel-subtitle">Shape, borders, shadows and card surface effects.</p>
              </div>
            </div>
            <div class="studio-panel-group">
              <div class="studio-range-grid is-two-col">
                <div class="studio-range-field">
                  <div class="studio-range-top">
                    <label for="studio-borderRadius">Corner radius</label>
                    <output class="studio-range-output" for="studio-borderRadius">18px</output>
                  </div>
                  <div class="studio-range-track-wrap">
                    <input id="studio-borderRadius" type="range" min="0" max="64" step="1" data-theme-key="borderRadius">
                  </div>
                </div>
                <div class="studio-range-field">
                  <div class="studio-range-top">
                    <label for="studio-shadowStrength">Shadow depth</label>
                    <output class="studio-range-output" for="studio-shadowStrength">70%</output>
                  </div>
                  <div class="studio-range-track-wrap">
                    <input id="studio-shadowStrength" type="range" min="0" max="100" step="1" data-theme-key="shadowStrength">
                  </div>
                </div>
                <div class="studio-range-field">
                  <div class="studio-range-top">
                    <label for="studio-borderSize">Border width</label>
                    <output class="studio-range-output" for="studio-borderSize">1px</output>
                  </div>
                  <div class="studio-range-track-wrap">
                    <input id="studio-borderSize" type="range" min="0" max="16" step="1" data-theme-key="borderSize">
                  </div>
                </div>
                <div class="studio-range-field">
                  <div class="studio-range-top">
                    <label for="studio-cardBlur">Glass blur</label>
                    <output class="studio-range-output" for="studio-cardBlur">0px</output>
                  </div>
                  <div class="studio-range-track-wrap">
                    <input id="studio-cardBlur" type="range" min="0" max="48" step="1" data-theme-key="cardBlur">
                  </div>
                </div>
                <div class="studio-range-field">
                  <div class="studio-range-top">
                    <label for="studio-cardPadding">Inner padding</label>
                    <output class="studio-range-output" for="studio-cardPadding">16px</output>
                  </div>
                  <div class="studio-range-track-wrap">
                    <input id="studio-cardPadding" type="range" min="0" max="56" step="1" data-theme-key="cardPadding">
                  </div>
                </div>
              </div>
              <div class="studio-field" style="margin-top:12px">
                <label>Border color</label>
                <input type="color" data-theme-key="borderColor">
              </div>
            </div>
          </div>
        </section>
      </div>

      <aside class="studio-inspector">
        <section class="studio-section" id="studio-page-section">
          <div class="studio-panel-body">
            <div class="studio-panel-header">
              <div>
                <h3>Page Settings</h3>
                <p class="studio-panel-subtitle">Header chrome, page sections, navigator and default icons.</p>
              </div>
            </div>
            <div class="studio-panel-group">
              <div class="studio-field">
                <label>Header icon</label>
                <button type="button" class="studio-btn" id="header-icon-picker-btn"><?= view_ui_icon($active_ui_theme['iconStyle']) ?> Choose header icon</button>
              </div>
              <div class="studio-range-field">
                <div class="studio-range-top">
                  <label for="studio-headerHeight">Header height</label>
                  <output class="studio-range-output" for="studio-headerHeight">84px</output>
                </div>
                <div class="studio-range-track-wrap">
                  <input id="studio-headerHeight" type="range" min="48" max="200" step="1" data-theme-key="headerHeight">
                </div>
              </div>
              <div class="studio-field">
                <label>Header alignment</label>
                <select data-theme-key="headerAlignment">
                  <option value="left">Left</option>
                  <option value="center">Center</option>
                  <option value="right">Right</option>
                </select>
              </div>
              <label class="studio-toggle-row">Show header icons <input type="checkbox" data-theme-key="showHeaderIcons"></label>
              <label class="studio-toggle-row">Show brand strip <input type="checkbox" data-theme-key="brandVisible"></label>
              <label class="studio-toggle-row">Show hero card <input type="checkbox" data-theme-key="heroVisible"></label>
              <label class="studio-toggle-row">Show name card <input type="checkbox" data-theme-key="nameCardVisible"></label>
              <label class="studio-toggle-row">Show online status <input type="checkbox" data-theme-key="showStatus"></label>
              <label class="studio-toggle-row">Show page navigator <input type="checkbox" data-theme-key="pageNavigatorVisible"></label>
              <label class="studio-toggle-row">Show footer save button <input type="checkbox" data-theme-key="footerVisible"></label>
              <div class="studio-field"><label>Hero title override</label><input type="text" data-theme-key="headerText" placeholder="Leave empty to use page name"></div>
              <div class="studio-range-field">
                <div class="studio-range-top">
                  <label for="studio-iconSize">Icon size</label>
                  <output class="studio-range-output" for="studio-iconSize">20px</output>
                </div>
                <div class="studio-range-track-wrap">
                  <input id="studio-iconSize" type="range" min="8" max="64" step="1" data-theme-key="iconSize">
                </div>
              </div>
              <div class="studio-field">
                <label>Default parameter icon</label>
                <select id="studio-parameterIcon" data-theme-key="parameterIcon">
                  <option value="gauge">Gauge</option>
                  <option value="bolt">Power</option>
                  <option value="power">Switch</option>
                  <option value="clock">Timer</option>
                  <option value="thermometer">Temperature</option>
                  <option value="fan">Fan</option>
                  <option value="droplet">Water</option>
                  <option value="lightbulb">Light</option>
                  <option value="lock">Lock</option>
                  <option value="wifi">Signal</option>
                  <option value="settings">Settings</option>
                </select>
              </div>
            </div>
          </div>
        </section>

        <section class="studio-section" id="studio-order-section">
          <div class="studio-panel-body">
            <div class="studio-panel-header">
              <div>
                <h3>Page Layout</h3>
                <p class="studio-panel-subtitle">Spacing, visible sections, footer button and per-page control order.</p>
              </div>
            </div>

            <div class="studio-panel-group">
              <p class="studio-panel-label">Page spacing</p>
              <div class="studio-range-grid is-two-col">
                <div class="studio-range-field">
                  <div class="studio-range-top">
                    <label for="studio-pagePadding">Screen padding</label>
                    <output class="studio-range-output" for="studio-pagePadding">18px</output>
                  </div>
                  <div class="studio-range-track-wrap">
                    <input id="studio-pagePadding" type="range" min="0" max="64" step="1" data-theme-key="pagePadding">
                  </div>
                </div>
                <div class="studio-range-field">
                  <div class="studio-range-top">
                    <label for="studio-cardSpacing">Gap between cards</label>
                    <output class="studio-range-output" for="studio-cardSpacing">14px</output>
                  </div>
                  <div class="studio-range-track-wrap">
                    <input id="studio-cardSpacing" type="range" min="0" max="56" step="1" data-theme-key="cardSpacing">
                  </div>
                </div>
                <div class="studio-range-field">
                  <div class="studio-range-top">
                    <label for="studio-cardMargin">Card outer margin</label>
                    <output class="studio-range-output" for="studio-cardMargin">0px</output>
                  </div>
                  <div class="studio-range-track-wrap">
                    <input id="studio-cardMargin" type="range" min="0" max="48" step="1" data-theme-key="cardMargin">
                  </div>
                </div>
              </div>
            </div>

            <div class="studio-panel-group">
              <p class="studio-panel-label">Footer save button</p>
              <div class="studio-field"><label>Button label</label><input type="text" data-theme-key="buttonText"></div>
              <div class="studio-range-grid is-two-col">
                <div class="studio-range-field">
                  <div class="studio-range-top">
                    <label for="studio-buttonSize">Button height</label>
                    <output class="studio-range-output" for="studio-buttonSize">44px</output>
                  </div>
                  <div class="studio-range-track-wrap">
                    <input id="studio-buttonSize" type="range" min="24" max="96" step="1" data-theme-key="buttonSize">
                  </div>
                </div>
                <div class="studio-range-field">
                  <div class="studio-range-top">
                    <label for="studio-buttonRadius">Button radius</label>
                    <output class="studio-range-output" for="studio-buttonRadius">16px</output>
                  </div>
                  <div class="studio-range-track-wrap">
                    <input id="studio-buttonRadius" type="range" min="0" max="48" step="1" data-theme-key="buttonRadius">
                  </div>
                </div>
              </div>
              <div class="studio-grid" style="margin-top:10px">
                <div class="studio-field">
                  <label>Style target</label>
                  <select data-theme-key="buttonTarget">
                    <option value="save">Save button</option>
                    <option value="customize">Customize UI button</option>
                    <option value="all">Both footer buttons</option>
                  </select>
                </div>
                <div class="studio-field">
                  <label>Hover effect</label>
                  <select data-theme-key="buttonHover">
                    <option value="lift">Lift</option>
                    <option value="glow">Glow</option>
                    <option value="none">None</option>
                  </select>
                </div>
                <div class="studio-field">
                  <label>Icon position</label>
                  <select data-theme-key="buttonIconPosition">
                    <option value="left">Left</option>
                    <option value="right">Right</option>
                    <option value="none">Hidden</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="studio-panel-group">
              <p class="studio-panel-label">Control order</p>
              <p class="order-page-hint">Drag controls within each page group. Order applies only to that page in the mobile preview.</p>
              <div class="order-list" id="control-order-list"></div>
            </div>
          </div>
        </section>

        <section class="studio-section" id="studio-schema-section">
          <div class="studio-panel-body">
            <div class="studio-panel-header">
              <div>
                <h3>Device UI Schema</h3>
                <p class="studio-panel-subtitle">Generated JSON for the mobile UI. Opening this tab syncs it to <?= htmlspecialchars($schema_api_url ?? 'the public API', ENT_QUOTES, 'UTF-8') ?>.</p>
              </div>
              <button type="button" class="studio-btn" id="copy-schema-btn"><?= view_ui_icon('file') ?> Copy</button>
            </div>
            <div class="studio-panel-group">
              <div class="studio-field">
                <textarea id="schema-output" spellcheck="false"></textarea>
              </div>
            </div>
          </div>
        </section>
      </aside>
        </div>
        <div class="studio-panel-footer">
          <div class="studio-status" id="studio-status"></div>
        </div>
      </div>
    </div>
  </aside>

  <div class="icon-picker" id="param-icon-picker" aria-label="Parameter icon picker">
    <p class="icon-picker-title" id="icon-picker-title">Choose icon</p>
    <input type="text" class="icon-search-box" id="param-icon-search-input" placeholder="Search icons, e.g. fan, power, light">
    <div class="icon-picker-grid" id="icon-picker-grid"></div>
  </div>
  <div class="component-picker" id="param-component-picker" aria-label="Parameter component picker">
    <p class="component-picker-title" id="component-picker-title">Choose component</p>
    <div class="component-picker-grid" id="component-picker-grid"></div>
  </div>
  <div class="save-toast" id="save-toast" role="status" aria-live="polite">UI created successfully.</div>

  <script src="<?= base_url('assets/js/sdui/sdui-core.js') ?>"></script>
  <script>
    // ============================================================
    // COMPLETE UI STUDIO - Enhanced Version
    // Features: Full icon library, typography, parameter buttons, XML schema
    // ============================================================

    // All Free FontAwesome Icons
    const FONTAWESOME_ICONS = {
      'signal': '<path d="M2 20h.01"></path><path d="M7 20a5 5 0 0 1 10 0"></path><path d="M4 14a11 11 0 0 1 16 0"></path><path d="M2 8a16 16 0 0 1 20 0"></path>',
      'smartphone': '<rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect><path d="M12 18h.01"></path>',
      'layers': '<path d="m12 2 10 5-10 5L2 7l10-5z"></path><path d="m2 17 10 5 10-5"></path><path d="m2 12 10 5 10-5"></path>',
      'bolt': '<path d="M13 2 3 14h8l-1 8 11-14h-8l1-6z"></path>',
      'gauge': '<path d="m12 14 4-4"></path><path d="M3.34 19a10 10 0 1 1 17.32 0"></path>',
      'heart': '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>',
      'clock': '<circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path>',
      'droplet': '<path d="M12 2.69 5.66 9.03a9 9 0 1 0 12.68 0L12 2.69z"></path>',
      'fan': '<path d="M10.8 14.8a3 3 0 1 0 2.4-5.6"></path><path d="M12 12c-2-4-2-7 0-9 3 2 3 5 0 9Z"></path><path d="M12 12c4-2 7-2 9 0-2 3-5 3-9 0Z"></path><path d="M12 12c2 4 2 7 0 9-3-2-3-5 0-9Z"></path><path d="M12 12c-4 2-7 2-9 0 2-3 5-3 9 0Z"></path>',
      'lightbulb': '<path d="M15 14c.2-1 .7-1.7 1.4-2.5A6 6 0 1 0 7.6 11.5C8.3 12.3 8.8 13 9 14"></path><path d="M9 18h6"></path><path d="M10 22h4"></path><path d="M10 14h4v4h-4z"></path>',
      'lock': '<rect width="18" height="11" x="3" y="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path>',
      'power': '<path d="M12 2v10"></path><path d="M18.4 6.6a9 9 0 1 1-12.8 0"></path>',
      'settings': '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.52a2 2 0 0 1-1 1.72l-.15.1a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1-1-1.72v-.52a2 2 0 0 1 1-1.72l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle>',
      'thermometer': '<path d="M14 14.76V5a2 2 0 1 0-4 0v9.76a4 4 0 1 0 4 0Z"></path>',
      'wifi': '<path d="M5 13a10 10 0 0 1 14 0"></path><path d="M8.5 16.5a5 5 0 0 1 7 0"></path><path d="M2 8.5a15 15 0 0 1 20 0"></path><path d="M12 20h.01"></path>',
      'arrow-left': '<path d="M19 12H5"></path><path d="m12 19-7-7 7-7"></path>',
      'arrow-right': '<path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path>',
      'chevron-left': '<path d="m15 18-6-6 6-6"></path>',
      'chevron-right': '<path d="m9 18 6-6-6-6"></path>',
      'code': '<path d="m16 18 6-6-6-6"></path><path d="m8 6-6 6 6 6"></path>',
      'edit': '<path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>',
      'file': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path>',
      'info': '<circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path>',
      'move': '<path d="M12 2v20"></path><path d="m15 5-3-3-3 3"></path><path d="m15 19-3 3-3-3"></path><path d="M2 12h20"></path><path d="m5 9-3 3 3 3"></path><path d="m19 9 3 3-3 3"></path>',
      'palette': '<circle cx="13.5" cy="6.5" r=".5"></circle><circle cx="17.5" cy="10.5" r=".5"></circle><circle cx="8.5" cy="7.5" r=".5"></circle><circle cx="6.5" cy="12.5" r=".5"></circle><path d="M12 2a10 10 0 0 0 0 20 2 2 0 0 0 2-2 1.5 1.5 0 0 1 1.5-1.5H17a5 5 0 0 0 5-5.3A10 10 0 0 0 12 2z"></path>',
      'save': '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><path d="M17 21v-8H7v8"></path><path d="M7 3v5h8"></path>',
      'volume-2': '<path d="M9 9v6"></path><path d="M15 7.5a9 9 0 0 1 0 9M9 20H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2l9-5v19l-9-5Z"></path>',
      'temperature': '<path d="M14 14.76V5a2 2 0 1 0-4 0v9.76a4 4 0 1 0 4 0Z"></path>',
      'sliders': '<path d="M4 9h16"></path><path d="M4 9v2a2 2 0 0 0 2 2h1v5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-5h1a2 2 0 0 0 2-2V9"></path><path d="M9 20v-5"></path><path d="M20 9v2a2 2 0 0 1-2 2h-1v5a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2v-5H7a2 2 0 0 1-2-2v-2"></path><path d="M15 20v-5"></path>',
      'aperture': '<circle cx="12" cy="12" r="10"></circle><path d="M14.31 8l5.74 9.94"></path><path d="M9.69 8h11.48"></path><path d="M7.38 12.5l5.74-9.94"></path><path d="M13.69 16l-5.74 9.94"></path><path d="M14.31 16h-11.48"></path><path d="M16.62 11.5l-5.74 9.94"></path>',
      'battery': '<rect width="16" height="10" x="2" y="7" rx="2" ry="2"></rect><line x1="22" x2="20" y1="11" y2="11"></line><line x1="4" x2="4" y1="4" y2="5"></line><line x1="12" x2="12" y1="4" y2="5"></line><line x1="20" x2="20" y1="4" y2="5"></line>',
      'bell': '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10 19c1 1 2 2 4 2s3-1 4-2"></path><path d="M9 12c-2-2-1-7-1-7"></path>',
      'bluetooth': '<path d="m7 16 5-5-5-5"></path><path d="m17 16-5-5 5-5"></path>',
      'check': '<path d="M20 6 9 17l-5-5"></path>',
      'check-circle': '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path>',
      'circle': '<circle cx="12" cy="12" r="10"></circle>',
      'copy': '<rect width="14" height="14" x="8" y="8" rx="2" ry="2"></rect><path d="M4 16H2a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v2"></path>',
      'cpu': '<rect width="16" height="16" x="4" y="4" rx="2"></rect><path d="M9 9h6v6H9z"></path><path d="M9 4V2"></path><path d="M15 4V2"></path><path d="M9 20v2"></path><path d="M15 20v2"></path><path d="M4 9H2"></path><path d="M4 15H2"></path><path d="M20 9h2"></path><path d="M20 15h2"></path>',
      'download': '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="M7 10 12 15l5-5"></path><path d="M12 15V3"></path>',
      'eye': '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>',
      'eye-off': '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" x2="23" y1="1" y2="23"></line>',
      'filter': '<polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>',
      'flag': '<path d="M4 15s1-1 5-1 5 1 5 1"></path><path d="M4 22v-7"></path><path d="M9 8 5 4m4 16 4-3"></path>',
      'grid': '<rect width="7" height="7" x="3" y="3" rx="1"></rect><rect width="7" height="7" x="14" y="3" rx="1"></rect><rect width="7" height="7" x="14" y="14" rx="1"></rect><rect width="7" height="7" x="3" y="14" rx="1"></rect>',
      'help-circle': '<circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path>',
      'home': '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>',
      'inbox': '<rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="M2 6h3a6 6 0 0 1 6-6 6 6 0 0 1 6 6h3"></path>',
      'key': '<path d="M21 2 9 14m12 4-8.5-8.5a1.5 1.5 0 0 1 0-2.12L6.5 2.5a1.5 1.5 0 0 1 2.12 0l11.38 11.38a1.5 1.5 0 0 1 0 2.12Z"></path><circle cx="6" cy="16" r="2"></circle>',
      'mail': '<rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>',
      'map-pin': '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle>',
      'maximize': '<path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>',
      'menu': '<line x1="4" x2="20" y1="6" y2="6"></line><line x1="4" x2="20" y1="12" y2="12"></line><line x1="4" x2="20" y1="18" y2="18"></line>',
      'message-circle': '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>',
      'message-square': '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>',
      'mic': '<path d="M12 1a3 3 0 0 0-3 3v12a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" x2="12" y1="19" y2="23"></line><line x1="8" x2="16" y1="23" y2="23"></line>',
      'minimize': '<path d="M8 3v6h6M16 3v6h-6m12 11h-6v-6M4 14h6v6H4z"></path>',
      'minus': '<line x1="5" x2="19" y1="12" y2="12"></line>',
      'monitor': '<path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle>',
      'moon': '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>',
      'more-horizontal': '<circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle>',
      'more-vertical': '<circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle>',
      'mouse': '<rect width="18" height="20" x="3" y="2" rx="2" ry="2"></rect><path d="M12 2v8"></path>',
      'move-horizontal': '<path d="M18 8L22 12M22 12L18 16M22 12H2M2 12L6 8M2 12L6 16"></path>',
      'move-vertical': '<path d="M8 18L12 22L16 18M12 22V2M12 2L8 6M12 2L16 6"></path>',
      'music': '<path d="M9 18V5l12-2v13a4 4 0 1 1-4-4 4 4 0 0 1 4 4z"></path><circle cx="6" cy="18" r="3"></circle>',
      'navigation': '<polygon points="12 2 19 21 12 18 5 21 12 2"></polygon>',
      'navigation-2': '<circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4m3-3h-6"></path>',
      'octagon': '<polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>',
      'package': '<line x1="16.5" x2="9.5" y1="9.4" y2="4"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" x2="12" y1="22.08" y2="12"></line>',
      'pause': '<rect width="4" height="16" x="6" y="4" rx="1"></rect><rect width="4" height="16" x="14" y="4" rx="1"></rect>',
      'pause-circle': '<circle cx="12" cy="12" r="10"></circle><line x1="10" x2="10" y1="8" y2="16"></line><line x1="14" x2="14" y1="8" y2="16"></line>',
      'percent': '<circle cx="8" cy="6" r="1"></circle><circle cx="16" cy="18" r="1"></circle><path d="M17 1 7 23"></path>',
      'phone': '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>',
      'phone-call': '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path><polygon points="23 16 23 23 16 23"></polygon>',
      'phone-off': '<path d="M23 18.36l-5.64-5.64a2 2 0 0 0-2.82 0l-.88.88a2 2 0 0 1-2.83 0L9.36 11c-1-1-1-2.65 0-3.83l.88-.88a2 2 0 0 0 0-2.82L3.64 1M23 18.36a10 10 0 0 1-14.1-14.1M1 1l22 22"></path>',
      'play': '<polygon points="5 3 19 12 5 21 5 3"></polygon>',
      'play-circle': '<circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon>',
      'plus': '<line x1="12" x2="12" y1="5" y2="19"></line><line x1="5" x2="19" y1="12" y2="12"></line>',
      'plus-circle': '<circle cx="12" cy="12" r="10"></circle><line x1="12" x2="12" y1="8" y2="16"></line><line x1="8" x2="16" y1="12" y2="12"></line>',
      'plus-square': '<rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><line x1="12" x2="12" y1="8" y2="16"></line><line x1="8" x2="16" y1="12" y2="12"></line>',
      'pocket': '<path d="M4 3h16a2 2 0 0 1 2 2v6a10 10 0 0 1-10 10A10 10 0 0 1 2 11V5a2 2 0 0 1 2-2z"></path><polyline points="8 10 12 14 16 10"></polyline>',
      'power-off': '<path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" x2="12" y1="2" y2="12"></line>',
      'printer': '<polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect width="12" height="8" x="6" y="14" rx="1" ry="1"></rect>',
      'radio': '<circle cx="12" cy="12" r="1"></circle><path d="M4.93 4.93a10 10 0 0 1 14.14 0m-2.12 2.12a6 6 0 0 1 0 8.49M2.81 2.81a14 14 0 0 1 19.78 0"></path>',
      'refresh-cw': '<path d="M23 4v6h-6"></path><path d="M1 20v-6h6"></path><path d="M3.51 9a9 9 0 0 1 14.85-3.36M3.86 19a9 9 0 0 0 14.74 3.5"></path>',
      'refresh-ccw': '<path d="M1 4v6h6"></path><path d="M23 20v-6h-6"></path><path d="M20.49 9a9 9 0 0 0-14.85-3.36M20.13 19a9 9 0 0 1-14.73 3.5"></path>',
      'repeat': '<path d="M17 2l4 4-4 4"></path><path d="M3 11v-1a4 4 0 0 1 4-4h14M7 22l-4-4 4-4"></path><path d="M21 13v1a4 4 0 0 1-4 4H3"></path>',
      'repeat-1': '<path d="M10 9V5l-7 7 7 7v-4a4 4 0 0 0 4-4v-1a4 4 0 0 0-4-4Z"></path><path d="M14 20l7-7-7-7v4a4 4 0 0 1-4 4v1a4 4 0 0 1 4 4Z"></path>',
      'reply': '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4v-4H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>',
      'reply-all': '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4v-4H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path><path d="m3 7 5 5-5 5"></path>',
      'rotate-ccw': '<path d="M1 4v6h6"></path><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>',
      'rotate-cw': '<path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>',
      'rss': '<circle cx="6" cy="18" r="1"></circle><path d="M4 4a16 16 0 0 1 16 16"></path><path d="M4 11a9 9 0 0 1 9 9"></path>',
      'save-draft': '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline>',
      'scale': '<path d="M12 3v18m8.5-9H3.5"></path><path d="M20 9v1a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V9a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"></path><path d="M4 9v1a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V9a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1z"></path>',
      'scissors': '<circle cx="6" cy="6" r="3"></circle><circle cx="18" cy="18" r="3"></circle><line x1="20.88" x2="9.12" y1="20.88" y2="9.12"></line><line x1="8.88" x2="20.88" y1="9.12" y2="20.88"></line>',
      'search': '<circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path>',
      'send': '<line x1="22" x2="11" y1="2" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>',
      'server': '<rect width="20" height="8" x="2" y="2" rx="2" ry="2"></rect><rect width="20" height="8" x="2" y="14" rx="2" ry="2"></rect><line x1="6" x2="6.01" y1="6" y2="6"></line><line x1="6" x2="6.01" y1="18" y2="18"></line>',
      'settings-2': '<path d="M12 2c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9-4.03-9-9-9z"></path><circle cx="12" cy="12" r="3"></circle>',
      'share': '<circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"></line><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"></line>',
      'share-2': '<circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"></line><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"></line>',
      'share-alt': '<path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><path d="M16 6l-4-4-4 4"></path><line x1="12" x2="12" y1="2" y2="15"></line>',
      'shield': '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>',
      'shield-off': '<path d="M19.69 14c.3-1.99.46-4.05.16-6L12 2 4.27 8c-.3 1.83-.46 3.78-.16 5.07C3.51 15.07 6 17 12 20c2.91-1.66 4.9-2.99 5.64-4M1 1l22 22"></path>',
      'shift': '<path d="M6 9l6-7 6 7M12 16V9.5"></path><path d="M4 15h16a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-2a2 2 0 0 1 2-2z"></path>',
      'shopping-bag': '<circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>',
      'shopping-cart': '<circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>',
      'shuffle': '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><path d="M3.27 6.96 12 12.01l8.73-5.05"></path><line x1="12" x2="12" y1="22.08" y2="12"></line>',
      'skip-back': '<polygon points="19 20 9 12 19 4 19 20"></polygon><line x1="5" x2="5" y1="4" y2="20"></line>',
      'skip-forward': '<polygon points="5 4 15 12 5 20 5 4"></polygon><line x1="19" x2="19" y1="4" y2="20"></line>',
      'slack': '<path d="M14 16a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-7h1z"></path><path d="M15 0a2 2 0 0 0-2 2v5h7V2a2 2 0 0 0-5 0z"></path><path d="M8 7H1a2 2 0 0 0-2 2v1h7V7z"></path><path d="M0 8a2 2 0 0 0 2 2h5V5H0v3z"></path><path d="M10 7H3v7h7V7z"></path><path d="M7 0a2 2 0 0 1 2 2v5H2V2a2 2 0 0 1 5 0z"></path><path d="M0 16a2 2 0 0 0 2 2h1v3a2 2 0 0 0 2 2 2 2 0 0 0 2-2v-3h-7z"></path>',
      'slash': '<circle cx="12" cy="12" r="10"></circle><line x1="4.93" x2="19.07" y1="4.93" y2="19.07"></line>',
      'sliders': '<path d="M4 9h16"></path><path d="M4 9v2a2 2 0 0 0 2 2h1v5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-5h1a2 2 0 0 0 2-2V9"></path><path d="M9 20v-5"></path><path d="M20 9v2a2 2 0 0 1-2 2h-1v5a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2v-5H7a2 2 0 0 1-2-2v-2"></path><path d="M15 20v-5"></path>',
      'smile': '<circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" x2="9.01" y1="9" y2="9"></line><line x1="15" x2="15.01" y1="9" y2="9"></line>',
      'smartphone-2': '<rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect><path d="M12 18h.01"></path>',
      'snap': '<rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect><path d="M12 18h.01"></path>',
      'snow-flake': '<path d="M20.041 17.045l-5.77-5.773M24.005 12.494l-5.774 5.773M12 1v11.26M12 22.974V12.26M4.5 12.494l5.77 5.773m11.26-5.773-5.77-5.773"></path>',
      'software': '<rect width="20" height="14" x="2" y="3" rx="2"></rect><path d="M2 17h20"></path><line x1="6" x2="6.01" y1="21" y2="21"></line><line x1="12" x2="12.01" y1="21" y2="21"></line><line x1="18" x2="18.01" y1="21" y2="21"></line>',
      'sort-asc': '<polyline points="12 5 19 12 12 19"></polyline><line x1="5" x2="12" y1="19" y2="19"></line><line x1="5" x2="12" y1="12" y2="12"></line><line x1="5" x2="12" y1="5" y2="5"></line>',
      'sort-desc': '<polyline points="12 19 19 12 12 5"></polyline><line x1="5" x2="12" y1="5" y2="5"></line><line x1="5" x2="12" y1="12" y2="12"></line><line x1="5" x2="12" y1="19" y2="19"></line>',
      'speaker': '<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M15.54 5.47a7 7 0 0 1 0 13.07m2.12-2.12a3.5 3.5 0 0 0 0-4.95"></path>',
      'square': '<rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>',
      'star': '<polygon points="12 2 15.09 10.26 23.77 10.5 17.88 16.46 20.09 25.5 12 20.92 3.91 25.5 6.12 16.46 0.22 10.5 8.91 10.26 12 2"></polygon>',
      'stop-circle': '<circle cx="12" cy="12" r="10"></circle><rect width="6" height="6" x="9" y="9" rx="1" ry="1"></rect>',
      'strikethrough': '<path d="M17.3 13.61a3.5 3.5 0 0 0-4.6-4.6M6.7 10.39a3.5 3.5 0 0 1 4.6 4.6"></path><line x1="4" x2="20" y1="12" y2="12"></line>',
      'sun': '<circle cx="12" cy="12" r="5"></circle><line x1="12" x2="12" y1="1" y2="3"></line><line x1="12" x2="12" y1="21" y2="23"></line><line x1="4.22" x2="5.64" y1="4.22" y2="5.64"></line><line x1="18.36" x2="19.78" y1="18.36" y2="19.78"></line><line x1="1" x2="3" y1="12" y2="12"></line><line x1="21" x2="23" y1="12" y2="12"></line><line x1="4.22" x2="5.64" y1="19.78" y2="18.36"></line><line x1="18.36" x2="19.78" y1="5.64" y2="4.22"></line>',
      'sunrise': '<path d="M17 18a5 5 0 1 0-10 0"></path><line x1="12" x2="12" y1="2" y2="9"></line><line x1="4.22" x2="5.64" y1="10.22" y2="11.64"></line><line x1="1" x2="3" y1="18" y2="18"></line><line x1="21" x2="23" y1="18" y2="18"></line><line x1="18.36" x2="19.78" y1="11.64" y2="10.22"></line><line x1="23" x2="1" y1="22" y2="22"></line><polyline points="8 6 12 2 16 6"></polyline>',
      'sunset': '<path d="M17 18a5 5 0 1 0-10 0"></path><line x1="12" x2="12" y1="2" y2="9"></line><line x1="4.22" x2="5.64" y1="10.22" y2="11.64"></line><line x1="1" x2="3" y1="18" y2="18"></line><line x1="21" x2="23" y1="18" y2="18"></line><line x1="18.36" x2="19.78" y1="11.64" y2="10.22"></line><line x1="23" x2="1" y1="22" y2="22"></line><polyline points="8 18 12 22 16 18"></polyline>',
      'sun-off': '<path d="M4.02 4.02c-1.19 1.19-2 2.84-2 4.64 0 3.63 2.95 6.57 6.57 6.57.98 0 1.92-.21 2.76-.62M16.56 8.44a6.57 6.57 0 1 0-8.12 8.12M1 1l22 22"></path>',
      'sunrise-2': '<path d="M17 18a5 5 0 1 0-10 0"></path><line x1="12" x2="12" y1="2" y2="9"></line><line x1="4.22" x2="5.64" y1="10.22" y2="11.64"></line><line x1="1" x2="3" y1="18" y2="18"></line><line x1="21" x2="23" y1="18" y2="18"></line><line x1="18.36" x2="19.78" y1="11.64" y2="10.22"></line><line x1="23" x2="1" y1="22" y2="22"></line><polyline points="8 6 12 2 16 6"></polyline>',
      'sunset-2': '<path d="M17 18a5 5 0 1 0-10 0"></path><line x1="12" x2="12" y1="2" y2="9"></line><line x1="4.22" x2="5.64" y1="10.22" y2="11.64"></line><line x1="1" x2="3" y1="18" y2="18"></line><line x1="21" x2="23" y1="18" y2="18"></line><line x1="18.36" x2="19.78" y1="11.64" y2="10.22"></line><line x1="23" x2="1" y1="22" y2="22"></line><polyline points="8 18 12 22 16 18"></polyline>',
      'tablet': '<rect width="18" height="24" x="3" y="2" rx="2" ry="2"></rect><line x1="9" x2="15" y1="18" y2="18"></line>',
      'tag': '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" x2="7.01" y1="7" y2="7"></line>',
      'target': '<circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="12" r="5"></circle><circle cx="12" cy="12" r="9"></circle>',
      'thermometer-2': '<path d="M14 14.76V5a2 2 0 1 0-4 0v9.76a4 4 0 1 0 4 0Z"></path>',
      'triangle': '<polygon points="12 2 20 20 4 20 12 2"></polygon>',
      'trash': '<path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line>',
      'trash-2': '<path d="M3 6h18"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m4 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>',
      'trello': '<rect width="7" height="7" x="3" y="3" rx="1"></rect><rect width="7" height="7" x="14" y="3" rx="1"></rect><rect width="7" height="7" x="14" y="14" rx="1"></rect><rect width="7" height="7" x="3" y="14" rx="1"></rect>',
      'trending-down': '<polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline><polyline points="17 18 23 18 23 12"></polyline>',
      'trending-up': '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline>',
      'triangle-alert': '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3.05h16.94a2 2 0 0 0 1.71-3.05l-8.47-14.14a2 2 0 0 0-3.42 0z"></path><line x1="12" x2="12" y1="9" y2="13"></line><line x1="12" x2="12.01" y1="17" y2="17"></line>',
      'trophy': '<path d="M6 9H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2h-2m-4-3v2m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m8 3v2"></path>',
      'truck': '<rect width="16" height="10" x="1" y="3" rx="2"></rect><path d="M17 8h4a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-1"></path><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle>',
      'tv': '<rect width="20" height="14" x="2" y="2" rx="2" ry="2"></rect><polyline points="2 17 12 9 22 17"></polyline>',
      'twitch': '<path d="M21 2H3v16h5v4l4-4h5l4-4V2zm-10 9V7m5 4v-5"></path>',
      'twitter': '<path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2s9 5 20 5a9.5 9.5 0 00-9-5.5c4.75 2.25 7-7 7-7z"></path>',
      'type': '<polyline points="4 7h16M4 7l2.293-2.293a1 1 0 011.414 0l2.586 2.586a1 1 0 001.414 0l2.586-2.586a1 1 0 011.414 0L20 7M4 7v10a1 1 0 001 1h14a1 1 0 001-1V7"></polyline>',
      'umbrella': '<path d="M23 12a11.05 11.05 0 00-22 0zm-5 7a3 3 0 01-6 0v-7"></path>',
      'underline': '<path d="M6 3v7a6 6 0 006 6 6 6 0 006-6V3M4 21h16"></path>',
      'unlock': '<rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 9.2-1"></path>',
      'upload': '<path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" x2="12" y1="3" y2="15"></line>',
      'upload-cloud': '<polyline points="16 16 12 12 8 16"></polyline><line x1="12" x2="12" y1="12" y2="21"></line><path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.3"></path>',
      'user': '<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
      'user-check': '<path d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path><circle cx="12" cy="7" r="4"></circle><polyline points="18 14 21 17 24 14"></polyline>',
      'user-minus': '<path d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path><circle cx="12" cy="7" r="4"></circle><line x1="17" x2="23" y1="17" y2="17"></line>',
      'user-plus': '<path d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path><circle cx="12" cy="7" r="4"></circle><line x1="12" x2="12" y1="14" y2="20"></line><line x1="9" x2="15" y1="17" y2="17"></line>',
      'user-x': '<path d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path><circle cx="12" cy="7" r="4"></circle><line x1="18" x2="20" y1="14" y2="20"></line><line x1="20" x2="18" y1="14" y2="20"></line>',
      'users': '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 00-3-3.87"></path><path d="M16 3.13a4 4 0 010 7.75"></path>',
      'video': '<polygon points="23 7 16 12 23 17 23 7"></polygon><rect width="15" height="14" x="1" y="5" rx="2" ry="2"></rect>',
      'video-off': '<path d="M16 16v1a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2h2m5.66 0a2 2 0 012.828 0l5.656 5.656a2 2 0 010 2.828l-5.656 5.656a2 2 0 01-2.828 0l-.172-.172M23 7v10"></path><line x1="1" x2="23" y1="1" y2="23"></line>',
      'voicemail': '<circle cx="5" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><path d="M15 16.29c0 1.25.936 2.29 2.09 2.29 1.08 0 1.99-.93 2-2.12"></path><path d="M4.99 12H4v2.12C4 15.36 4.94 16 6.08 16c1.04 0 1.91-.68 2-1.71"></path>',
      'volume': '<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>',
      'volume-1': '<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M15.54 5.47a7 7 0 010 13.07"></path>',
      'watch': '<circle cx="12" cy="12" r="7"></circle><polyline points="12 9 12 12 13.5 13.5"></polyline><path d="M12 7V5m0 14v-2m4.22-10.22l-1.41 1.41m-5.62 5.62l-1.41 1.41m9.9 0l-1.41-1.41m-5.62-5.62l-1.41-1.41"></path>',
      'zap': '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>',
      'zap-off': '<polyline points="10 19 9 20 21 20"></polyline><line x1="14" x2="14" y1="4" y2="14"></line><path d="M4.73 4.73 3 6.46M12 9v6m8-11-7 11h6l-7 5"></path><line x1="1" x2="23" y1="1" y2="23"></line>',
      'zoom-in': '<circle cx="11" cy="11" r="8"></circle><line x1="21" x2="16.65" y1="21" y2="16.65"></line><line x1="11" x2="11" y1="8" y2="14"></line><line x1="8" x2="14" y1="11" y2="11"></line>',
      'zoom-out': '<circle cx="11" cy="11" r="8"></circle><line x1="21" x2="16.65" y1="21" y2="16.65"></line><line x1="8" x2="14" y1="11" y2="11"></line>'
    };

    Object.assign(FONTAWESOME_ICONS, {
      'fan-speed': '<path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0-4 0"></path><path d="M12 10c-1.6-3.8-.8-6.7 2.4-8 2.1 3.1 1.3 5.8-2.4 8Z"></path><path d="M14 12c3.8-1.6 6.7-.8 8 2.4-3.1 2.1-5.8 1.3-8-2.4Z"></path><path d="M12 14c1.6 3.8.8 6.7-2.4 8-2.1-3.1-1.3-5.8 2.4-8Z"></path><path d="M10 12c-3.8 1.6-6.7.8-8-2.4 3.1-2.1 5.8-1.3 8 2.4Z"></path>',
      'fan-round': '<circle cx="12" cy="12" r="9"></circle><circle cx="12" cy="12" r="2"></circle><path d="M12 10c-1.8-3-.9-5.1 1.3-6"></path><path d="M14 12c3-1.8 5.1-.9 6 1.3"></path><path d="M12 14c1.8 3 .9 5.1-1.3 6"></path><path d="M10 12c-3 1.8-5.1.9-6-1.3"></path>',
      'fan-stand': '<path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0-8 0"></path><path d="M12 8c-1.5-3-.7-5 1.6-6"></path><path d="M16 12c3-1.5 5-.7 6 1.6"></path><path d="M12 16c1.5 3 .7 5-1.6 6"></path><path d="M8 12c-3 1.5-5 .7-6-1.6"></path><path d="M12 16v6"></path><path d="M8 22h8"></path>',
      'air-flow': '<path d="M3 8h10a3 3 0 1 0-3-3"></path><path d="M3 12h16"></path><path d="M3 16h10a3 3 0 1 1-3 3"></path>',
      'light-ceiling': '<path d="M8 2h8"></path><path d="M12 2v5"></path><path d="M6 13a6 6 0 0 1 12 0H6Z"></path><path d="M9 17h6"></path><path d="M10 21h4"></path>',
      'plug': '<path d="M12 22v-5"></path><path d="M9 8V2"></path><path d="M15 8V2"></path><path d="M7 8h10v4a5 5 0 0 1-10 0V8Z"></path>',
      'motor': '<rect width="16" height="12" x="4" y="7" rx="2"></rect><path d="M8 7V5h8v2"></path><path d="M10 13h4"></path><path d="M2 11h2"></path><path d="M20 11h2"></path>',
      'humidity': '<path d="M12 2s6 7 6 12a6 6 0 0 1-12 0c0-5 6-12 6-12Z"></path><path d="M8 14a4 4 0 0 0 8 0"></path>',
      'speed-high': '<path d="M3.34 19a10 10 0 1 1 17.32 0"></path><path d="m12 14 6-6"></path><path d="M12 14h.01"></path>',
      'speed-low': '<path d="M3.34 19a10 10 0 1 1 17.32 0"></path><path d="m12 14-3-3"></path><path d="M12 14h.01"></path>'
    });

    const ICON_SEARCH_ALIASES = {
      fan: 'fan blower air airflow speed wind rotate cooling',
      'fan-speed': 'fan blower fast high air speed',
      'fan-round': 'fan round ceiling exhaust ventilation',
      'fan-stand': 'fan pedestal stand air cooler',
      'air-flow': 'fan air wind airflow ventilation',
      lightbulb: 'light lamp bulb',
      'light-ceiling': 'light lamp ceiling',
      bolt: 'power electricity energy',
      power: 'switch power on off',
      plug: 'power plug socket',
      gauge: 'meter speed parameter control',
      'speed-high': 'speed fan high gauge',
      'speed-low': 'speed fan low gauge',
      droplet: 'water humidity drop',
      humidity: 'water humidity moisture',
      thermometer: 'temperature heat cool',
      wifi: 'signal network wireless',
      signal: 'network live signal'
    };

    const PARAMETER_COMPONENT_LIBRARY = [
      { id: 'auto', label: 'Auto', description: 'Use API parameter type', flutterWidget: null, flutterPackage: null, valueType: 'auto', position: 'trailing', layout: 'row', interactive: false },
      { id: 'switch', label: 'Switch', description: 'On/off toggle control', flutterWidget: 'Switch', flutterPackage: 'material', valueType: 'boolean', position: 'trailing', layout: 'row', interactive: true },
      { id: 'slider', label: 'Slider', description: 'Range slider 0-255', flutterWidget: 'Slider', flutterPackage: 'material', valueType: 'number', position: 'trailing', layout: 'stacked', interactive: true },
      { id: 'powerGauge', label: 'Pointer Gauge', description: 'Triangle pointer radial dial', flutterWidget: 'AnimatedRadialGauge', flutterPackage: 'gauges', valueType: 'number', position: 'stacked', layout: 'stacked', interactive: true },
      { id: 'needleGauge', label: 'Needle Gauge', description: 'Thin needle with rounded arc track', flutterWidget: 'NeedleGauge', flutterPackage: 'custom', valueType: 'number', position: 'stacked', layout: 'stacked', interactive: true },
      { id: 'button', label: 'Power Button', description: 'Large momentary power / emergency control', flutterWidget: 'PowerButton', flutterPackage: 'custom', valueType: 'trigger', position: 'trailing', layout: 'row', interactive: true },
      { id: 'elevatedButton', label: 'Elevated Button', description: 'Execute action trigger', flutterWidget: 'ElevatedButton', flutterPackage: 'material', valueType: 'trigger', position: 'trailing', layout: 'row', interactive: true },
      { id: 'value', label: 'Value', description: 'Read-only status chip', flutterWidget: 'Text', flutterPackage: 'material', valueType: 'string', position: 'trailing', layout: 'row', interactive: false },
      { id: 'dropdown', label: 'Dropdown', description: 'Select from options list', flutterWidget: 'DropdownButton', flutterPackage: 'material', valueType: 'enum', position: 'trailing', layout: 'row', interactive: true },
      { id: 'segmented', label: 'Segmented', description: 'Choose one from a pill group', flutterWidget: 'SegmentedControl', flutterPackage: 'material', valueType: 'enum', position: 'stacked', layout: 'stacked', interactive: true },
      { id: 'textField', label: 'Text Field', description: 'Editable text input', flutterWidget: 'TextField', flutterPackage: 'material', valueType: 'string', position: 'inline', layout: 'row', interactive: true },
      { id: 'linearGauge', label: 'Linear Gauge', description: 'Horizontal signal / progress gauge', flutterWidget: 'LinearGauge', flutterPackage: 'custom', valueType: 'number', position: 'stacked', layout: 'stacked', interactive: false }
    ];

    const FLUTTER_COMPONENT_CONTRACT = {
      version: '1.3.0',
      supportedWidgets: [
        'Switch',
        'Slider',
        'schemaSlider',
        'ParameterCard',
        'schemaControlList',
        'metaChip',
        'NeedleGauge',
        'AnimatedRadialGauge',
        'SegmentedControl',
        'RotaryKnob',
        'LinearGauge',
        'ElevatedButton',
        'DropdownButton',
        'TextField',
        'Text',
        'column',
        'row',
        'text',
        'PowerButton',
        'CustomInfoTile',
        'HeartRateDisplay',
        'FireControlPanel',
        'ToggleButton',
        'IconButton',
        'SliderWithLabel',
        'ButtonGroup'
      ],
      bindingRules: {
        parameterValues: 'state.device.parameters via ParameterCard.valueBinding',
        navigation: 'state.activePageIndex',
        onlineStatus: 'state.online / state.onlineLabel',
        progress: 'state.pageProgressPercent'
      }
    };

    const STAC_RENDERING_DEFAULTS = {
      mode: 'inline',
      engine: 'stac',
      stac: {
        entry: 'schemaRoot',
        localProjectId: 'efdeflt_dynamic_ui_local',
        envelope: 'assets/stac/dashboard.json',
        description: 'UI renders via Stac.fromJson — shell from ui.widgetTree, interactive ParameterCards from schemaControlList.children bound to state.device.parameters'
      }
    };

    const INLINE_COMPONENT_CONTRACT = {
      description: 'Put the full ParameterCard UI in controls[].component.widgetTree and also expand ParameterCard nodes under ui.widgetTree schemaControlList.children. Each control must render as icon + label/value + accessory, with slider/gauge/segmented controls also rendering interactive rows below the main row. Dynamic values must use valueBinding/textBinding to device.parameters.*, not hardcoded text.',
      requiredControlFields: ['id', 'label', 'binding', 'componentType', 'component.widgetTree', 'events', 'styles'],
      eventsFormat: {
        event: 'onChanged|onValueCommitted|onTap|onSelected|onSubmitted',
        action: { type: 'update|send', binding: 'device.parameters.{key}' }
      },
      bindingContract: {
        valueBinding: 'device.parameters.{parameterKey}',
        textBinding: 'device.parameters.{parameterKey}',
        stateSeed: 'state.device.parameters',
        navigationModel: 'state.activePageIndex'
      }
    };

    // Registry of brand-new components defined entirely by the schema and built
    // from primitives the shell already renders (container/row/column/text/icon).
    // A control references one of these by name via flutterWidget; the engine
    // resolves the widgetTree from registerComponents and injects {{binding}}.
    // Kept separate from components.catalog so these never override the real
    // interactive widgets (switch/slider/button/etc).
    const REGISTERED_COMPONENTS = [
      {
        id: 'customInfoTile',
        name: 'CustomInfoTile',
        label: 'Custom Info Tile',
        description: 'Brand-new component defined only by the API, rendered in a Parameter card.',
        icon: 'gauge',
        valueType: 'string',
        registered: true,
        buildWidgetTree: function() {
          return {
            type: 'container',
            properties: { visibility: true },
            style: {
              padding: { top: 12, right: 14, bottom: 12, left: 14 },
              backgroundColor: '#E8F7EE',
              borderRadius: 14,
              border: { width: 1, color: '#B7E3C7' }
            },
            children: [
              {
                type: 'row',
                properties: { crossAxisAlignment: 'center', spacing: 12 },
                children: [
                  {
                    type: 'icon',
                    properties: { name: 'gauge', size: 22 },
                    style: { color: '#157145' }
                  },
                  {
                    type: 'column',
                    properties: { crossAxisAlignment: 'start', spacing: 2 },
                    children: [
                      {
                        type: 'text',
                        properties: { text: 'CUSTOM COMPONENT' },
                        style: { textStyle: { fontFamily: 'Poppins', fontSize: 11, fontWeight: 800, color: '#5F7482' } }
                      },
                      {
                        type: 'text',
                        properties: { text: 'Live value', textBinding: '{{binding}}' },
                        style: { textStyle: { fontFamily: 'Poppins', fontSize: 16, fontWeight: 700, color: '#102A3A' } }
                      }
                    ]
                  }
                ]
              }
            ]
          };
        }
      },
      {
        id: 'heartRateDisplay',
        name: 'HeartRateDisplay',
        label: 'Heart Rate Display',
        description: 'Live BPM vitals readout with heart icon for monitoring pulse rate.',
        icon: 'heart',
        valueType: 'number',
        unit: 'BPM',
        defaultValue: 72,
        min: 30,
        max: 220,
        step: 1,
        registered: true,
        formatDisplay: function(value) {
          return String(value) + ' BPM';
        },
        buildWidgetTree: function() {
          return {
            type: 'container',
            properties: {
              visibility: true,
              valueType: 'number',
              unit: 'BPM',
              min: 30,
              max: 220,
              useLiveBinding: true
            },
            style: {
              padding: { top: 14, right: 16, bottom: 14, left: 16 },
              backgroundColor: '#FFF0F3',
              borderRadius: 16,
              border: { width: 1, color: '#FECDD3' }
            },
            children: [
              {
                type: 'row',
                properties: { crossAxisAlignment: 'center', spacing: 14 },
                children: [
                  {
                    type: 'icon',
                    properties: { name: 'heart', size: 30 },
                    style: { color: '#E11D48' }
                  },
                  {
                    type: 'column',
                    properties: { crossAxisAlignment: 'start', spacing: 4 },
                    children: [
                      {
                        type: 'text',
                        properties: { text: 'HEART RATE' },
                        style: { textStyle: { fontFamily: 'Poppins', fontSize: 11, fontWeight: 800, color: '#9F1239' } }
                      },
                      {
                        type: 'row',
                        properties: { crossAxisAlignment: 'end', spacing: 6 },
                        children: [
                          {
                            type: 'text',
                            properties: { textBinding: '{{binding}}' },
                            style: { textStyle: { fontFamily: 'Poppins', fontSize: 32, fontWeight: 800, color: '#881337' } }
                          },
                          {
                            type: 'text',
                            properties: { text: 'BPM' },
                            style: { textStyle: { fontFamily: 'Poppins', fontSize: 14, fontWeight: 700, color: '#BE123C' } }
                          }
                        ]
                      }
                    ]
                  }
                ]
              }
            ]
          };
        }
      },
      {
        id: 'fireControlPanel',
        name: 'FireControlPanel',
        label: 'Fire Control Panel',
        description: 'Dual START/STOP fire-system control with API and snackbar actions.',
        icon: 'zap',
        valueType: 'enum',
        defaultValue: 'STOP',
        options: [{ label: 'START', value: 'START' }, { label: 'STOP', value: 'STOP' }],
        registered: true,
        buildWidgetTree: function() {
          return {
            type: 'container',
            properties: { visibility: true, valueType: 'enum', useLiveBinding: true },
            style: {
              padding: { top: 12, right: 12, bottom: 12, left: 12 },
              backgroundColor: '#FFF7ED',
              borderRadius: 14,
              border: { width: 1, color: '#FED7AA' }
            },
            children: [
              {
                type: 'column',
                properties: { crossAxisAlignment: 'stretch', spacing: 10 },
                children: [
                  {
                    type: 'text',
                    properties: { text: 'FIRE SYSTEM' },
                    style: { textStyle: { fontFamily: 'Poppins', fontSize: 11, fontWeight: 800, color: '#9A3412' } }
                  },
                  {
                    type: 'row',
                    properties: { crossAxisAlignment: 'center', spacing: 10 },
                    children: [
                      {
                        type: 'button',
                        properties: { label: 'START', actionValue: 'START' },
                        style: { backgroundColor: '#16A34A', borderRadius: 10, padding: { top: 10, right: 14, bottom: 10, left: 14 } },
                        children: [
                          { type: 'text', properties: { text: 'START' }, style: { textStyle: { fontFamily: 'Poppins', fontSize: 13, fontWeight: 800, color: '#FFFFFF' } } }
                        ]
                      },
                      {
                        type: 'button',
                        properties: { label: 'STOP', actionValue: 'STOP' },
                        style: { backgroundColor: '#DC2626', borderRadius: 10, padding: { top: 10, right: 14, bottom: 10, left: 14 } },
                        children: [
                          { type: 'text', properties: { text: 'STOP' }, style: { textStyle: { fontFamily: 'Poppins', fontSize: 13, fontWeight: 800, color: '#FFFFFF' } } }
                        ]
                      },
                      {
                        type: 'text',
                        properties: { textBinding: '{{binding}}' },
                        style: { textStyle: { fontFamily: 'Poppins', fontSize: 14, fontWeight: 800, color: '#9A3412' } }
                      }
                    ]
                  }
                ]
              }
            ]
          };
        }
      },
      {
        id: 'toggleButton',
        name: 'ToggleButton',
        label: 'Toggle Button',
        description: 'Custom lock/unlock toggle with icon state change.',
        icon: 'lock',
        valueType: 'boolean',
        defaultValue: 'UNLOCKED',
        registered: true,
        buildWidgetTree: function() {
          return {
            type: 'container',
            properties: { visibility: true, valueType: 'boolean', useLiveBinding: true },
            style: {
              padding: { top: 12, right: 14, bottom: 12, left: 14 },
              backgroundColor: '#EEF2FF',
              borderRadius: 14,
              border: { width: 1, color: '#C7D2FE' }
            },
            children: [
              {
                type: 'row',
                properties: { crossAxisAlignment: 'center', spacing: 12 },
                children: [
                  { type: 'icon', properties: { name: 'lock', size: 22 }, style: { color: '#4338CA' } },
                  {
                    type: 'column',
                    properties: { crossAxisAlignment: 'start', spacing: 2 },
                    children: [
                      { type: 'text', properties: { text: 'LOCK STATUS' }, style: { textStyle: { fontFamily: 'Poppins', fontSize: 11, fontWeight: 800, color: '#4338CA' } } },
                      { type: 'text', properties: { textBinding: '{{binding}}' }, style: { textStyle: { fontFamily: 'Poppins', fontSize: 16, fontWeight: 700, color: '#1E1B4B' } } }
                    ]
                  }
                ]
              }
            ]
          };
        }
      },
      {
        id: 'iconButton',
        name: 'IconButton',
        label: 'Icon Button',
        description: 'One-click configure / settings action.',
        icon: 'settings',
        valueType: 'trigger',
        defaultValue: 'TAP',
        registered: true,
        buildWidgetTree: function() {
          return {
            type: 'container',
            properties: { visibility: true, valueType: 'trigger' },
            style: {
              padding: { top: 12, right: 14, bottom: 12, left: 14 },
              backgroundColor: '#F0F9FF',
              borderRadius: 14,
              border: { width: 1, color: '#BAE6FD' }
            },
            children: [
              {
                type: 'row',
                properties: { crossAxisAlignment: 'center', spacing: 12 },
                children: [
                  { type: 'icon', properties: { name: 'settings', size: 22 }, style: { color: '#0369A1' } },
                  {
                    type: 'column',
                    properties: { crossAxisAlignment: 'start', spacing: 2 },
                    children: [
                      { type: 'text', properties: { text: 'CONFIGURE' }, style: { textStyle: { fontFamily: 'Poppins', fontSize: 11, fontWeight: 800, color: '#0369A1' } } },
                      { type: 'text', properties: { text: 'Tap to open settings', textBinding: '{{binding}}' }, style: { textStyle: { fontFamily: 'Poppins', fontSize: 15, fontWeight: 700, color: '#0C4A6E' } } }
                    ]
                  }
                ]
              }
            ]
          };
        }
      },
      {
        id: 'sliderWithLabel',
        name: 'SliderWithLabel',
        label: 'Slider With Label',
        description: 'Range slider that shows the live numeric value.',
        icon: 'gauge',
        valueType: 'number',
        defaultValue: 50,
        min: 0,
        max: 100,
        step: 1,
        registered: true,
        formatDisplay: function(value) {
          return String(value);
        },
        buildWidgetTree: function() {
          return {
            type: 'container',
            properties: { visibility: true, valueType: 'number', min: 0, max: 100, step: 1, useLiveBinding: true },
            style: {
              padding: { top: 12, right: 14, bottom: 12, left: 14 },
              backgroundColor: '#F8FAFC',
              borderRadius: 14,
              border: { width: 1, color: '#CBD5E1' }
            },
            children: [
              {
                type: 'column',
                properties: { crossAxisAlignment: 'stretch', spacing: 8 },
                children: [
                  {
                    type: 'row',
                    properties: { crossAxisAlignment: 'center', spacing: 8 },
                    children: [
                      { type: 'text', properties: { text: 'BRIGHTNESS' }, style: { textStyle: { fontFamily: 'Poppins', fontSize: 11, fontWeight: 800, color: '#64748B' } } },
                      { type: 'text', properties: { textBinding: '{{binding}}' }, style: { textStyle: { fontFamily: 'Poppins', fontSize: 16, fontWeight: 800, color: '#0F172A' } } }
                    ]
                  },
                  {
                    type: 'container',
                    properties: { visibility: true },
                    style: { height: 8, backgroundColor: '#E2E8F0', borderRadius: 999 },
                    children: []
                  }
                ]
              }
            ]
          };
        }
      },
      {
        id: 'buttonGroup',
        name: 'ButtonGroup',
        label: 'Button Group',
        description: 'A/B/C mode selector button group.',
        icon: 'layers',
        valueType: 'enum',
        defaultValue: 'A',
        options: [{ label: 'A', value: 'A' }, { label: 'B', value: 'B' }, { label: 'C', value: 'C' }],
        registered: true,
        buildWidgetTree: function() {
          return {
            type: 'container',
            properties: { visibility: true, valueType: 'enum', useLiveBinding: true },
            style: {
              padding: { top: 12, right: 12, bottom: 12, left: 12 },
              backgroundColor: '#F5F3FF',
              borderRadius: 14,
              border: { width: 1, color: '#DDD6FE' }
            },
            children: [
              {
                type: 'column',
                properties: { crossAxisAlignment: 'stretch', spacing: 8 },
                children: [
                  { type: 'text', properties: { text: 'MODE' }, style: { textStyle: { fontFamily: 'Poppins', fontSize: 11, fontWeight: 800, color: '#6D28D9' } } },
                  {
                    type: 'row',
                    properties: { crossAxisAlignment: 'center', spacing: 8 },
                    children: [
                      { type: 'button', properties: { label: 'A', actionValue: 'A' }, style: { backgroundColor: '#7C3AED', borderRadius: 10, padding: { top: 8, right: 12, bottom: 8, left: 12 } }, children: [{ type: 'text', properties: { text: 'A' }, style: { textStyle: { fontFamily: 'Poppins', fontSize: 13, fontWeight: 800, color: '#FFFFFF' } } }] },
                      { type: 'button', properties: { label: 'B', actionValue: 'B' }, style: { backgroundColor: '#EDE9FE', borderRadius: 10, padding: { top: 8, right: 12, bottom: 8, left: 12 } }, children: [{ type: 'text', properties: { text: 'B' }, style: { textStyle: { fontFamily: 'Poppins', fontSize: 13, fontWeight: 800, color: '#5B21B6' } } }] },
                      { type: 'button', properties: { label: 'C', actionValue: 'C' }, style: { backgroundColor: '#EDE9FE', borderRadius: 10, padding: { top: 8, right: 12, bottom: 8, left: 12 } }, children: [{ type: 'text', properties: { text: 'C' }, style: { textStyle: { fontFamily: 'Poppins', fontSize: 13, fontWeight: 800, color: '#5B21B6' } } }] },
                      { type: 'text', properties: { textBinding: '{{binding}}' }, style: { textStyle: { fontFamily: 'Poppins', fontSize: 14, fontWeight: 800, color: '#5B21B6' } } }
                    ]
                  }
                ]
              }
            ]
          };
        }
      }
    ];

    function resolveRegisteredComponentValue(registered, valueText, meta) {
      const raw = valueText || meta.default || '';
      if (registered.valueType === 'number') {
        const parsed = parseFloat(String(raw).replace(/[^\d.-]/g, ''));
        const fallback = registered.defaultValue != null ? registered.defaultValue : 0;
        return Number.isFinite(parsed) ? parsed : fallback;
      }
      if (registered.valueType === 'boolean') {
        if (raw !== '') return raw;
        return registered.defaultValue != null ? registered.defaultValue : 'OFF';
      }
      if (registered.valueType === 'enum') {
        if (raw !== '') return raw;
        if (Array.isArray(registered.options) && registered.options[0]) {
          return registered.options[0].value;
        }
        return registered.defaultValue || '';
      }
      return raw || registered.defaultValue || '';
    }

    function formatRegisteredDisplayValue(registered, value) {
      if (registered && typeof registered.formatDisplay === 'function') {
        return registered.formatDisplay(value);
      }
      if (registered && registered.valueType === 'number' && registered.unit) {
        return String(value) + ' ' + registered.unit;
      }
      return String(value || (registered && registered.label) || '');
    }

    function getRegisteredComponent(typeOrName) {
      if (!typeOrName) return null;
      const key = String(typeOrName);
      return REGISTERED_COMPONENTS.find(function(item) {
        return item.id === key || item.name === key;
      }) || null;
    }

    function isRegisteredComponentType(type) {
      return Boolean(getRegisteredComponent(type));
    }

    function getAnyComponentEntry(type) {
      return getComponentLibraryEntry(type) || getRegisteredComponent(type);
    }

    function buildRegisterComponentsSchema() {
      const registry = {};
      REGISTERED_COMPONENTS.forEach(function(item) {
        registry[item.name] = {
          description: item.description,
          valueType: item.valueType || 'string',
          unit: item.unit || null,
          min: item.min != null ? item.min : null,
          max: item.max != null ? item.max : null,
          step: item.step != null ? item.step : null,
          options: Array.isArray(item.options) ? item.options : null,
          widgetTree: item.buildWidgetTree()
        };
      });
      return registry;
    }

    function substituteRegisteredBindings(value, binding, liveValue) {
      if (typeof value === 'string') {
        return value.replace(/\{\{binding\}\}/g, binding).replace(/\{\{value\}\}/g, liveValue);
      }
      if (Array.isArray(value)) {
        return value.map(function(item) { return substituteRegisteredBindings(item, binding, liveValue); });
      }
      if (value && typeof value === 'object') {
        const output = {};
        Object.keys(value).forEach(function(key) {
          output[key] = substituteRegisteredBindings(value[key], binding, liveValue);
        });
        return output;
      }
      return value;
    }

    function applyRegisteredPreviewStyle(el, style) {
      if (!style || !el) return;
      if (style.padding) {
        const pad = style.padding;
        el.style.padding = [pad.top, pad.right, pad.bottom, pad.left].map(function(v) { return v + 'px'; }).join(' ');
      }
      if (style.margin) {
        const margin = style.margin;
        el.style.margin = [margin.top, margin.right, margin.bottom, margin.left].map(function(v) { return v + 'px'; }).join(' ');
      }
      if (style.backgroundColor) el.style.backgroundColor = style.backgroundColor;
      if (style.borderRadius != null) el.style.borderRadius = style.borderRadius + 'px';
      if (style.border) {
        el.style.border = (style.border.width || 1) + 'px solid ' + (style.border.color || 'transparent');
      }
      if (style.color) el.style.color = style.color;
      if (style.textStyle) {
        const textStyle = style.textStyle;
        if (textStyle.fontFamily) el.style.fontFamily = textStyle.fontFamily;
        if (textStyle.fontSize != null) el.style.fontSize = textStyle.fontSize + 'px';
        if (textStyle.fontWeight != null) el.style.fontWeight = String(textStyle.fontWeight);
        if (textStyle.color) el.style.color = textStyle.color;
      }
    }

    function renderRegisteredWidgetNode(node, card, binding, liveValue) {
      if (!node || !node.type) return document.createElement('span');
      const type = node.type;
      const props = node.properties || {};
      const style = node.style || {};
      const el = document.createElement('div');
      el.className = 'registered-node registered-node--' + type;
      applyRegisteredPreviewStyle(el, style);

      if (type === 'container') {
        (node.children || []).forEach(function(child) {
          el.appendChild(renderRegisteredWidgetNode(child, card, binding, liveValue));
        });
        return el;
      }

      if (type === 'row') {
        el.style.display = 'flex';
        el.style.flexDirection = 'row';
        el.style.alignItems = props.crossAxisAlignment === 'start' ? 'flex-start' : (props.crossAxisAlignment === 'end' ? 'flex-end' : 'center');
        if (props.spacing != null) el.style.gap = props.spacing + 'px';
        (node.children || []).forEach(function(child) {
          el.appendChild(renderRegisteredWidgetNode(child, card, binding, liveValue));
        });
        return el;
      }

      if (type === 'column') {
        el.style.display = 'flex';
        el.style.flexDirection = 'column';
        el.style.alignItems = props.crossAxisAlignment === 'start' ? 'flex-start' : (props.crossAxisAlignment === 'end' ? 'flex-end' : 'center');
        if (props.spacing != null) el.style.gap = props.spacing + 'px';
        (node.children || []).forEach(function(child) {
          el.appendChild(renderRegisteredWidgetNode(child, card, binding, liveValue));
        });
        return el;
      }

      if (type === 'icon') {
        const iconName = props.name === 'gauge' ? getParamIcon(card) : (props.name || 'gauge');
        el.className = 'registered-node registered-node--icon' + (props.name === 'heart' ? ' registered-heart-icon' : '');
        el.innerHTML = iconSvg(iconName);
        if (style.color) {
          const svg = el.querySelector('.icon');
          if (svg) svg.style.color = style.color;
        }
        if (props.size) {
          const svg = el.querySelector('.icon');
          if (svg) {
            svg.style.width = props.size + 'px';
            svg.style.height = props.size + 'px';
          }
        }
        return el;
      }

      if (type === 'text') {
        el.className = 'registered-node registered-node--text';
        const text = props.textBinding ? liveValue : (props.text || '');
        el.textContent = text;
        return el;
      }

      if (type === 'button') {
        el.className = 'registered-node registered-node--button';
        el.style.display = 'inline-flex';
        el.style.alignItems = 'center';
        el.style.justifyContent = 'center';
        el.style.cursor = 'pointer';
        el.setAttribute('role', 'button');
        el.dataset.actionValue = props.actionValue || props.label || '';
        (node.children || []).forEach(function(child) {
          el.appendChild(renderRegisteredWidgetNode(child, card, binding, liveValue));
        });
        if (!(node.children || []).length) {
          el.textContent = props.label || 'Action';
        }
        el.addEventListener('click', function(event) {
          event.stopPropagation();
          const nextValue = props.actionValue || props.label || liveValue;
          const row = el.closest('.registered-node--row');
          if (row) {
            row.querySelectorAll('.registered-node--button').forEach(function(btn) {
              btn.classList.toggle('is-active', btn === el);
            });
          }
          commitRegisteredValue(card, nextValue);
        });
        return el;
      }

      (node.children || []).forEach(function(child) {
        el.appendChild(renderRegisteredWidgetNode(child, card, binding, liveValue));
      });
      return el;
    }

    function refreshRegisteredComponentPreview(card) {
      if (!card) return;
      const type = getResolvedParamComponentType(card);
      const registered = getRegisteredComponent(type);
      if (!registered) return;
      const binding = controlBindingKey(card.dataset.paramKey);
      const liveValue = String(getParamLiveValue(card, type, getComponentOverride(card.dataset.paramKey)) || '');
      const existing = card.querySelector('.registered-visual');
      const preview = buildRegisteredComponentPreview(card, registered, binding, liveValue);
      if (existing) {
        existing.replaceWith(preview);
      } else {
        card.classList.add('is-registered-component', 'layout-stacked');
        card.appendChild(preview);
      }
      card.dataset.componentType = type;
    }

    const ICON_LIBRARY_SOURCE = {
      source: 'inline-svg',
      style: 'lucide-feather',
      phpHelper: 'view_ui_icon()',
      jsMap: 'FONTAWESOME_ICONS',
      file: 'Views/items/view_page_ui.php',
      note: 'Icons are embedded SVG path strings (not Font Awesome npm). Same keys used in PHP and JavaScript.'
    };

    function getParameterComponentCatalog() {
      return PARAMETER_COMPONENT_LIBRARY.filter(function(item) { return item.id !== 'auto'; }).map(function(item) {
        const entry = Object.assign({}, item, {
          preview: componentPreviewMarkup(item.id)
        });
        delete entry.flutterPackage;
        if (item.id === 'switch') {
          entry.widgetTree = buildStacWidgetTree('Switch', '{{binding}}', {
            activeTrackColor: currentTheme.toggleOnColor,
            inactiveTrackColor: currentTheme.toggleOffColor,
            useLiveBinding: true
          }, [{ event: 'onChanged', action: { type: 'update', binding: '{{binding}}' } }]);
        } else if (item.id === 'slider') {
          entry.widgetTree = buildStacWidgetTree('schemaSlider', '{{binding}}', {
            min: 0,
            max: 255,
            step: 1,
            useLiveBinding: true,
            showMinMaxLabels: true
          }, [{ event: 'onChanged', action: { type: 'update', binding: '{{binding}}' } }]);
        } else if (item.id === 'powerGauge') {
          entry.widgetTree = buildStacWidgetTree('AnimatedRadialGauge', '{{binding}}', buildAnimatedRadialGaugeProps(resolveGaugeNumericBounds({}, '65')), [
            { event: 'onChanged', action: { type: 'update', binding: '{{binding}}' } },
            { event: 'onValueCommitted', action: { type: 'send', binding: '{{binding}}' } }
          ]);
        } else if (item.id === 'button') {
          entry.widgetTree = buildStacWidgetTree('PowerButton', '{{binding}}', {
            color: currentTheme.accentColor,
            shape: 'circle',
            size: 36,
            useLiveBinding: true
          }, [{ event: 'onTap', action: { type: 'update', binding: '{{binding}}' } }]);
        } else if (item.id === 'elevatedButton') {
          entry.widgetTree = buildStacWidgetTree('ElevatedButton', '{{binding}}', {
            label: 'EXECUTE',
            color: currentTheme.accentColor,
            useLiveBinding: true
          }, [{ event: 'onTap', action: { type: 'send', binding: '{{binding}}' } }]);
        } else if (item.id === 'linearGauge') {
          entry.widgetTree = buildStacWidgetTree('LinearGauge', '{{binding}}', {
            min: 0,
            max: 100,
            value: 75,
            unit: '%',
            showLabel: true,
            activeColor: currentTheme.accentColor,
            inactiveColor: '#D9DEEB',
            useLiveBinding: true
          }, []);
        } else if (item.id === 'segmented') {
          entry.widgetTree = buildStacWidgetTree('SegmentedControl', '{{binding}}', {
            options: [{ label: 'Low', value: 'Low' }, { label: 'Mid', value: 'Mid' }, { label: 'High', value: 'High' }],
            selectedColor: currentTheme.accentColor,
            selectedTextColor: '#FFFFFF',
            unselectedColor: 'rgba(106,142,174,0.14)',
            unselectedTextColor: currentTheme.textColor,
            useLiveBinding: true
          }, [
            { event: 'onChanged', action: { type: 'update', binding: '{{binding}}' } },
            { event: 'onSelected', action: { type: 'send', binding: '{{binding}}' } }
          ]);
        }
        return entry;
      });
    }

    function getComponentLibraryEntry(type) {
      return PARAMETER_COMPONENT_LIBRARY.find(function(item) { return item.id === type; }) || null;
    }

    function componentPreviewMarkup(type) {
      switch (type) {
        case 'switch':
          return '<span class="component-preview-switch" aria-hidden="true"></span>';
        case 'slider':
          return '<span class="component-preview-slider" aria-hidden="true"></span>';
        case 'needleGauge':
          return '<span class="component-preview-needleGauge" aria-hidden="true"><svg viewBox="0 0 120 72" aria-hidden="true"><path d="M18 58 A46 46 0 1 1 102 58" fill="none" stroke="#E2E8F0" stroke-width="10" stroke-linecap="round"/><path d="M18 58 A46 46 0 0 1 72 24" fill="none" stroke="var(--ui-accent)" stroke-width="10" stroke-linecap="round"/><circle cx="60" cy="58" r="4" fill="#0F2550"/><line x1="60" y1="58" x2="78" y2="34" stroke="#0F2550" stroke-width="3" stroke-linecap="round"/></svg></span>';
        case 'powerGauge':
          return '<span class="component-preview-pointerGauge" aria-hidden="true"><svg viewBox="0 0 120 72" aria-hidden="true"><path d="M18 58 A46 46 0 1 1 102 58" fill="none" stroke="#E2E8F0" stroke-width="10" stroke-linecap="round"/><path d="M18 58 A46 46 0 0 1 72 24" fill="none" stroke="var(--ui-accent)" stroke-width="10" stroke-linecap="round"/><circle cx="60" cy="58" r="5" fill="#fff" stroke="#CBD5E1" stroke-width="2"/><polygon points="60,58 54,44 66,44" fill="var(--ui-accent)" transform="rotate(35 60 58)"/></svg></span>';
        case 'button':
          return '<span class="component-preview-button" aria-hidden="true"></span>';
        case 'elevatedButton':
          return '<span class="component-preview-elevatedButton" aria-hidden="true">Go</span>';
        case 'linearGauge':
          return '<span class="component-preview-linearGauge" aria-hidden="true"><span class="component-preview-linearGauge-fill"></span></span>';
        case 'value':
          return '<span class="component-preview-value" aria-hidden="true">12</span>';
        case 'dropdown':
          return '<span class="component-preview-dropdown" aria-hidden="true"><span>Option</span><span>▾</span></span>';
        case 'segmented':
          return '<span class="component-preview-segmented" aria-hidden="true"><span class="is-active">Low</span><span>Mid</span><span>High</span></span>';
        case 'textField':
          return '<span class="component-preview-textField" aria-hidden="true"></span>';
        case 'customInfoTile':
          return '<span class="component-preview-registered" aria-hidden="true"><span class="component-preview-registered-icon"></span><span class="component-preview-registered-text"></span></span>';
        case 'heartRateDisplay':
          return '<span class="component-preview-heartRate" aria-hidden="true"><span class="component-preview-heartRate-icon"></span><span class="component-preview-heartRate-value">72</span></span>';
        case 'fireControlPanel':
          return '<span class="component-preview-firePanel" aria-hidden="true"><span>START</span><span>STOP</span></span>';
        case 'toggleButton':
          return '<span class="component-preview-toggleButton" aria-hidden="true">LOCK</span>';
        case 'iconButton':
          return '<span class="component-preview-iconButton" aria-hidden="true">⚙</span>';
        case 'sliderWithLabel':
          return '<span class="component-preview-sliderWithLabel" aria-hidden="true"><span>50</span><span class="component-preview-sliderWithLabel-bar"></span></span>';
        case 'buttonGroup':
          return '<span class="component-preview-buttonGroup" aria-hidden="true"><span class="is-active">A</span><span>B</span><span>C</span></span>';
        case 'auto':
        default:
          return '<span class="component-preview-auto" aria-hidden="true">A</span>';
      }
    }

    function getComponentChoiceLabel(type) {
      const entry = getAnyComponentEntry(type);
      return entry ? entry.label : 'Auto';
    }

    function buildCompactIconLibrary(usedIconNames) {
      const icons = Array.isArray(usedIconNames) && usedIconNames.length
        ? usedIconNames.filter(function(name) { return FONTAWESOME_ICONS[name]; }).sort()
        : [currentTheme.parameterIcon || 'gauge'];
      const paths = {};
      icons.forEach(function(name) {
        paths[name] = FONTAWESOME_ICONS[name];
      });
      return {
        source: ICON_LIBRARY_SOURCE.source,
        style: ICON_LIBRARY_SOURCE.style,
        count: icons.length,
        icons: icons,
        paths: paths
      };
    }

    function buildIconLibrarySchema(usedIconNames) {
      const allIcons = Object.keys(FONTAWESOME_ICONS).sort();
      const names = Array.isArray(usedIconNames) && usedIconNames.length
        ? usedIconNames.filter(function(name) { return FONTAWESOME_ICONS[name]; }).sort()
        : allIcons;
      const paths = {};
      names.forEach(function(name) {
        paths[name] = FONTAWESOME_ICONS[name];
      });
      return Object.assign({}, ICON_LIBRARY_SOURCE, {
        count: allIcons.length,
        icons: allIcons,
        paths: paths,
        usedCount: names.length
      });
    }

    function parameterKeySlug(parameterKey) {
      return String(parameterKey || '').replace(/[^a-zA-Z0-9]+/g, '_').toLowerCase();
    }

    function controlBindingKey(parameterKey) {
      return 'device.parameters.' + parameterKeySlug(parameterKey);
    }

    function controlSchemaVariant(componentType) {
      if (componentType === 'switch' || componentType === 'button' || componentType === 'elevatedButton') return 'action';
      if (componentType === 'slider' || componentType === 'powerGauge' || componentType === 'needleGauge' || componentType === 'linearGauge') return 'slider';
      if (componentType === 'segmented' || componentType === 'dropdown') return 'segmented';
      if (isRegisteredComponentType(componentType)) return 'custom';
      return 'status';
    }

    function controlDataOperation(componentType) {
      if (componentType === 'button') return 'write';
      if (componentType === 'elevatedButton') return 'execute';
      if (componentType === 'value' || componentType === 'linearGauge') return 'read';
      return 'write';
    }

    function buildControlInteractionEvents(componentType, binding) {
      if (componentType === 'switch') {
        return [
          { event: 'onChanged', action: { type: 'update', binding: binding } },
          { event: 'onChanged', action: { type: 'send', binding: binding } }
        ];
      }
      if (componentType === 'slider' || componentType === 'powerGauge' || componentType === 'needleGauge') {
        return [
          { event: 'onChanged', action: { type: 'update', binding: binding } },
          { event: 'onValueCommitted', action: { type: 'send', binding: binding } }
        ];
      }
      if (componentType === 'button' || componentType === 'elevatedButton') {
        return [{ event: 'onTap', action: { type: 'send', binding: binding } }];
      }
      if (componentType === 'dropdown' || componentType === 'segmented') {
        return [
          { event: 'onChanged', action: { type: 'update', binding: binding } },
          { event: 'onSelected', action: { type: 'send', binding: binding } }
        ];
      }
      if (componentType === 'textField') {
        return [
          { event: 'onChanged', action: { type: 'update', binding: binding } },
          { event: 'onSubmitted', action: { type: 'send', binding: binding } }
        ];
      }
      return [];
    }

    function buildControlDataActionId(controlId) {
      return 'update_' + String(controlId || 'control').replace(/[^a-zA-Z0-9]+/g, '_');
    }

    function buildControlDataAction(controlId, componentType, binding, meta, parameterName) {
      const actionId = buildControlDataActionId(controlId);
      return {
        id: actionId,
        definition: {
          type: 'dataOperation',
          dataSource: 'deviceCommands',
          operation: controlDataOperation(componentType),
          input: {
            modelNo: '$app.modelNo',
            parameter: parameterName,
            value: '$' + binding,
            commandId: meta.command_id || null,
            functionCode: meta.function_code || null
          }
        }
      };
    }

    function buildStacEvent(eventName, binding) {
      return {
        event: eventName,
        action: { type: 'update', binding: binding }
      };
    }

    function buildStacWidgetTree(widgetType, binding, props, events) {
      return {
        type: widgetType,
        properties: {
          binding: binding,
          props: props || {},
          events: events || [buildStacEvent('onChanged', binding)]
        }
      };
    }

    const POWER_GAUGE_ARC_START = 150;
    const POWER_GAUGE_ARC_SWEEP = 240;
    const NEEDLE_GAUGE_VIEW_WIDTH = 240;
    const NEEDLE_GAUGE_VIEW_HEIGHT = 208;
    const NEEDLE_GAUGE_PIVOT_Y = 132;
    const NEEDLE_GAUGE_RADIUS = 74;

    function computeGaugeViewBox(cx, cy, r, strokeW) {
      const pad = 8;
      const start = POWER_GAUGE_ARC_START;
      const end = POWER_GAUGE_ARC_START + POWER_GAUGE_ARC_SWEEP;
      let minX = cx - r - strokeW - pad;
      let maxX = cx + r + strokeW + pad;
      let minY = 18;
      let maxY = cy + r + strokeW + pad;
      for (let deg = start; deg <= end; deg += 2) {
        const point = polarGaugePoint(cx, cy, r, deg);
        minX = Math.min(minX, point.x - strokeW - pad);
        maxX = Math.max(maxX, point.x + strokeW + pad);
        minY = Math.min(minY, point.y - strokeW - pad);
        maxY = Math.max(maxY, point.y + strokeW + pad);
      }
      const pivotPad = 14;
      minX = Math.min(minX, cx - pivotPad);
      maxX = Math.max(maxX, cx + pivotPad);
      maxY = Math.max(maxY, cy + pivotPad);
      const width = maxX - minX;
      const height = maxY - minY;
      return {
        minX: minX,
        minY: minY,
        width: width,
        height: height,
        attr: minX.toFixed(1) + ' ' + minY.toFixed(1) + ' ' + width.toFixed(1) + ' ' + height.toFixed(1)
      };
    }

    function isGaugeComponentType(type) {
      return type === 'powerGauge' || type === 'needleGauge';
    }

    function resolveGaugeNumericBounds(meta, valueText) {
      const minRaw = meta.minimum === null || typeof meta.minimum === 'undefined' || meta.minimum === '' ? 0 : parseFloat(meta.minimum);
      const maxRaw = meta.maximum === null || typeof meta.maximum === 'undefined' || meta.maximum === '' ? 100 : parseFloat(meta.maximum);
      const stepRaw = meta.step === null || typeof meta.step === 'undefined' || meta.step === '' ? 1 : parseFloat(meta.step);
      const safeMin = Number.isFinite(minRaw) ? minRaw : 0;
      const safeMax = Number.isFinite(maxRaw) && maxRaw > safeMin ? maxRaw : 100;
      const safeStep = Number.isFinite(stepRaw) && stepRaw > 0 ? stepRaw : 1;
      const defaultRaw = meta.default !== '' && meta.default != null ? parseFloat(String(meta.default).replace(/[^\d.-]/g, '')) : safeMin;
      const parsed = valueText !== '' && valueText != null
        ? parseFloat(String(valueText).replace(/[^\d.-]/g, ''))
        : defaultRaw;
      const safeValue = Number.isFinite(parsed) ? Math.max(safeMin, Math.min(safeMax, parsed)) : safeMin;
      return { min: safeMin, max: safeMax, step: safeStep, value: safeValue };
    }

    function snapGaugeValue(value, min, max, step) {
      const clamped = Math.max(min, Math.min(max, value));
      if (!step || step <= 0) return clamped;
      const steps = Math.round((clamped - min) / step);
      const snapped = min + steps * step;
      const stepDecimals = (String(step).split('.')[1] || '').length;
      return stepDecimals > 0 ? parseFloat(snapped.toFixed(stepDecimals)) : snapped;
    }

    function gaugePivotScreenPoint(svg) {
      const rect = svg.getBoundingClientRect();
      const cx = NEEDLE_GAUGE_VIEW_WIDTH / 2;
      const cy = NEEDLE_GAUGE_PIVOT_Y;
      const viewBox = computeGaugeViewBox(cx, cy, NEEDLE_GAUGE_RADIUS, 14);
      const pivotXRatio = (cx - viewBox.minX) / viewBox.width;
      const pivotYRatio = (cy - viewBox.minY) / viewBox.height;
      return {
        x: rect.left + rect.width * pivotXRatio,
        y: rect.top + rect.height * pivotYRatio
      };
    }

    function gaugeAngleFromPointer(clientX, clientY, svg) {
      const pivot = gaugePivotScreenPoint(svg);
      const angleRad = Math.atan2(clientY - pivot.y, clientX - pivot.x);
      let angleDeg = (angleRad * 180 / Math.PI) + 90;
      while (angleDeg < 0) angleDeg += 360;
      while (angleDeg >= 360) angleDeg -= 360;

      const arcStart = POWER_GAUGE_ARC_START;
      const arcEnd = (arcStart + POWER_GAUGE_ARC_SWEEP) % 360;
      let delta;

      if (angleDeg >= arcStart || angleDeg <= arcEnd) {
        delta = angleDeg >= arcStart ? (angleDeg - arcStart) : ((360 - arcStart) + angleDeg);
      } else {
        const distToEnd = angleDeg - arcEnd;
        const distToStart = arcStart - angleDeg;
        delta = distToEnd <= distToStart ? POWER_GAUGE_ARC_SWEEP : 0;
      }

      return Math.max(0, Math.min(1, delta / POWER_GAUGE_ARC_SWEEP));
    }

    function gaugeValueToAngle(value, min, max) {
      const ratio = max > min ? (value - min) / (max - min) : 0;
      return POWER_GAUGE_ARC_START + ratio * POWER_GAUGE_ARC_SWEEP;
    }

    function gaugePointerToValue(clientX, clientY, svg, min, max, step, shouldSnap) {
      const ratio = gaugeAngleFromPointer(clientX, clientY, svg);
      const raw = min + ratio * (max - min);
      const clamped = Math.max(min, Math.min(max, raw));
      if (shouldSnap === false) return clamped;
      return snapGaugeValue(clamped, min, max, step);
    }

    function ensureGaugeVisualUid(wrap) {
      if (!wrap.dataset.gaugeUid) {
        wrap.dataset.gaugeUid = 'g' + Math.random().toString(36).slice(2, 10);
      }
      return wrap.dataset.gaugeUid;
    }

    function polarGaugePoint(cx, cy, r, angleDeg) {
      const rad = (angleDeg - 90) * Math.PI / 180;
      return { x: cx + r * Math.cos(rad), y: cy + r * Math.sin(rad) };
    }

    function gaugeArcPath(cx, cy, r, startDeg, endDeg) {
      const start = polarGaugePoint(cx, cy, r, endDeg);
      const end = polarGaugePoint(cx, cy, r, startDeg);
      const large = (endDeg - startDeg) > 180 ? 1 : 0;
      return 'M ' + start.x.toFixed(2) + ' ' + start.y.toFixed(2) + ' A ' + r + ' ' + r + ' 0 ' + large + ' 0 ' + end.x.toFixed(2) + ' ' + end.y.toFixed(2);
    }

    function buildAnimatedRadialGaugeProps(bounds, themeOptions) {
      const opts = themeOptions || {};
      const activeColor = opts.activeColor || currentTheme.accentColor || '#7C3AED';
      const inactiveColor = opts.inactiveColor || '#D9DEEB';
      const needleColor = opts.needleColor || '#0F2550';
      const range = bounds.max - bounds.min;
      const zoneAt = function(ratio) { return bounds.min + range * ratio; };
      return {
        radius: 92,
        width: 220,
        height: 172,
        duration: 450,
        curve: 'Curves.easeOutCubic',
        value: bounds.value,
        min: bounds.min,
        max: bounds.max,
        step: bounds.step,
        sweepDegrees: POWER_GAUGE_ARC_SWEEP,
        useLiveBinding: true,
        sendOnChange: true,
        package: 'gauges',
        axis: {
          min: bounds.min,
          max: bounds.max,
          sweepDegrees: POWER_GAUGE_ARC_SWEEP,
          pointer: {
            type: 'GaugePointer.triangle',
            width: 16,
            height: 28,
            borderRadius: 3,
            color: needleColor,
            position: { type: 'GaugePointerPosition.surface', offset: { dx: 0, dy: 2 } },
            border: { color: '#FFFFFF', width: 2.5 }
          },
          style: {
            thickness: 14,
            zoneSpacing: 8,
            blendColors: false,
            cornerRadius: { radius: 8 }
          },
          zones: [
            { from: bounds.min, to: zoneAt(0.55), color: activeColor, cornerRadius: { radius: 8 } },
            { from: zoneAt(0.55), to: zoneAt(0.65), color: activeColor, cornerRadius: { radius: 8 } },
            { from: zoneAt(0.65), to: zoneAt(0.85), color: inactiveColor, cornerRadius: { radius: 8 } },
            { from: zoneAt(0.85), to: bounds.max, color: inactiveColor, cornerRadius: { radius: 8 } }
          ]
        },
        builder: {
          type: 'RadialGaugeLabel',
          style: { color: needleColor, fontSize: 20, fontWeight: 'bold' },
          value: 'value'
        }
      };
    }

    function renderPointerGaugeSvg(bounds, uid) {
      const cx = NEEDLE_GAUGE_VIEW_WIDTH / 2;
      const cy = NEEDLE_GAUGE_PIVOT_Y;
      const r = NEEDLE_GAUGE_RADIUS;
      const strokeW = 14;
      const viewBox = computeGaugeViewBox(cx, cy, r, strokeW);
      const activeColor = currentTheme.accentColor || '#2563EB';
      const inactiveColor = '#E2E8F0';
      const gradId = 'gaugePointerGrad-' + (uid || '0');
      const start = POWER_GAUGE_ARC_START;
      const end = start + POWER_GAUGE_ARC_SWEEP;
      const ratio = bounds.max > bounds.min ? (bounds.value - bounds.min) / (bounds.max - bounds.min) : 0;
      const progressAngle = start + ratio * POWER_GAUGE_ARC_SWEEP;
      const tip = polarGaugePoint(cx, cy, r - 18, progressAngle);
      const left = polarGaugePoint(cx, cy, 12, progressAngle - 90);
      const right = polarGaugePoint(cx, cy, 12, progressAngle + 90);
      const progressArc = ratio > 0.001
        ? '<path d="' + gaugeArcPath(cx, cy, r, start, progressAngle) + '" fill="none" stroke="url(#' + gradId + ')" stroke-width="' + strokeW + '" stroke-linecap="round"/>'
        : '';
      return '<svg class="pointer-gauge-svg power-gauge-svg" viewBox="' + viewBox.attr + '" preserveAspectRatio="xMidYMid meet" role="img" aria-label="Pointer gauge ' + bounds.value + '">' +
        '<defs><linearGradient id="' + gradId + '" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="' + activeColor + '" stop-opacity=".78"/><stop offset="100%" stop-color="' + activeColor + '"/></linearGradient></defs>' +
        '<path d="' + gaugeArcPath(cx, cy, r, start, end) + '" fill="none" stroke="' + inactiveColor + '" stroke-width="' + strokeW + '" stroke-linecap="round"/>' +
        progressArc +
        '<circle cx="' + cx + '" cy="' + cy + '" r="11" fill="#FFFFFF" stroke="' + inactiveColor + '" stroke-width="3"/>' +
        '<polygon points="' + tip.x.toFixed(1) + ',' + tip.y.toFixed(1) + ' ' + left.x.toFixed(1) + ',' + left.y.toFixed(1) + ' ' + right.x.toFixed(1) + ',' + right.y.toFixed(1) + '" fill="' + activeColor + '" stroke="#FFFFFF" stroke-width="2" stroke-linejoin="round"/>' +
        '<text x="' + cx + '" y="' + Math.max(24, viewBox.minY + 16).toFixed(1) + '" text-anchor="middle" font-family="Inter,Segoe UI,sans-serif" font-size="10" font-weight="700" fill="#64748B" letter-spacing=".08em">LEVEL</text>' +
      '</svg>';
    }

    function renderNeedleGaugeSvg(bounds, uid) {
      const cx = NEEDLE_GAUGE_VIEW_WIDTH / 2;
      const cy = NEEDLE_GAUGE_PIVOT_Y;
      const r = NEEDLE_GAUGE_RADIUS;
      const strokeW = 14;
      const viewBox = computeGaugeViewBox(cx, cy, r, strokeW);
      const activeColor = currentTheme.accentColor || '#10B981';
      const inactiveColor = '#E2E8F0';
      const needleColor = '#0F2550';
      const gradId = 'gaugeProgressGrad-' + (uid || '0');
      const start = POWER_GAUGE_ARC_START;
      const end = start + POWER_GAUGE_ARC_SWEEP;
      const ratio = bounds.max > bounds.min ? (bounds.value - bounds.min) / (bounds.max - bounds.min) : 0;
      const progressAngle = start + ratio * POWER_GAUGE_ARC_SWEEP;
      const needleTip = polarGaugePoint(cx, cy, r - 12, progressAngle);
      const progressArc = ratio > 0.001
        ? '<path d="' + gaugeArcPath(cx, cy, r, start, progressAngle) + '" fill="none" stroke="url(#' + gradId + ')" stroke-width="' + strokeW + '" stroke-linecap="round"/>'
        : '';
      return '<svg class="needle-gauge-svg power-gauge-svg" viewBox="' + viewBox.attr + '" preserveAspectRatio="xMidYMid meet" role="img" aria-label="Gauge ' + bounds.value + '">' +
        '<defs><linearGradient id="' + gradId + '" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="' + activeColor + '" stop-opacity=".72"/><stop offset="100%" stop-color="' + activeColor + '"/></linearGradient></defs>' +
        '<path d="' + gaugeArcPath(cx, cy, r, start, end) + '" fill="none" stroke="' + inactiveColor + '" stroke-width="' + strokeW + '" stroke-linecap="round"/>' +
        progressArc +
        '<circle cx="' + cx + '" cy="' + cy + '" r="10" fill="#FFFFFF" stroke="' + inactiveColor + '" stroke-width="3"/>' +
        '<circle cx="' + cx + '" cy="' + cy + '" r="5.5" fill="' + needleColor + '"/>' +
        '<line x1="' + cx + '" y1="' + cy + '" x2="' + needleTip.x.toFixed(1) + '" y2="' + needleTip.y.toFixed(1) + '" stroke="' + needleColor + '" stroke-width="3.5" stroke-linecap="round"/>' +
        '<circle cx="' + needleTip.x.toFixed(1) + '" cy="' + needleTip.y.toFixed(1) + '" r="4" fill="' + activeColor + '" stroke="#FFFFFF" stroke-width="1.5"/>' +
        '<text x="' + cx + '" y="' + Math.max(24, viewBox.minY + 16).toFixed(1) + '" text-anchor="middle" font-family="Poppins,Segoe UI,sans-serif" font-size="10" font-weight="800" fill="#64748B" letter-spacing=".08em">LEVEL</text>' +
      '</svg>';
    }

    function formatGaugeDisplayValue(value, bounds, isDragging) {
      if (isDragging) {
        const decimals = bounds.max - bounds.min <= 10 ? 1 : 0;
        return decimals ? (Math.round(value * 10) / 10) : Math.round(value);
      }
      return snapGaugeValue(value, bounds.min, bounds.max, bounds.step);
    }

    function updatePowerGaugeVisual(wrap, bounds, card, options) {
      if (!wrap) return;
      const opts = options || {};
      const uid = ensureGaugeVisualUid(wrap);
      const displayValue = formatGaugeDisplayValue(bounds.value, bounds, Boolean(opts.isDragging));
      wrap.dataset.value = String(bounds.value);
      wrap.dataset.min = String(bounds.min);
      wrap.dataset.max = String(bounds.max);
      wrap.dataset.step = String(bounds.step);
      const svgHost = wrap.querySelector('.power-gauge-svg-host');
      if (svgHost) svgHost.innerHTML = wrap.classList.contains('pointer-gauge-visual')
        ? renderPointerGaugeSvg(bounds, uid)
        : renderNeedleGaugeSvg(bounds, uid);
      const label = wrap.querySelector('.power-gauge-value');
      if (label) label.innerHTML = '<strong>' + displayValue + '</strong> / ' + bounds.max;
      const gaugeInput = wrap.querySelector('.power-gauge-input');
      if (gaugeInput) gaugeInput.value = String(displayValue);
      const valueEl = card ? card.querySelector('.field-value') : null;
      if (valueEl && !valueEl.querySelector('input, select, textarea')) {
        valueEl.textContent = String(displayValue);
      }
      const metaValue = card ? card.querySelector('.meta-value') : null;
      if (metaValue) metaValue.textContent = String(displayValue);
    }

    function bindPowerGaugeInteractions(wrap, card) {
      if (!wrap || wrap.dataset.bound === '1') return;
      wrap.dataset.bound = '1';
      let dragging = false;
      let lastPointer = { x: 0, y: 0 };
      ensureGaugeVisualUid(wrap);

      const applyPointer = function(clientX, clientY, commit) {
        lastPointer.x = clientX;
        lastPointer.y = clientY;
        const svg = wrap.querySelector('.power-gauge-svg');
        if (!svg || !card) return;
        const meta = getEffectiveParamMeta(card);
        const bounds = resolveGaugeNumericBounds(meta, wrap.dataset.value || '');
        bounds.value = gaugePointerToValue(clientX, clientY, svg, bounds.min, bounds.max, bounds.step, commit);
        updatePowerGaugeVisual(wrap, bounds, card, { isDragging: !commit });
        if (commit) {
          const override = getComponentOverride(card.dataset.paramKey);
          override.value = String(bounds.value);
          generateSchema();
        }
      };

      wrap.addEventListener('pointerdown', function(event) {
        if (!event.target.closest('.power-gauge-svg')) return;
        dragging = true;
        wrap.setPointerCapture(event.pointerId);
        applyPointer(event.clientX, event.clientY, false);
        event.preventDefault();
      });
      wrap.addEventListener('pointermove', function(event) {
        if (!dragging) return;
        applyPointer(event.clientX, event.clientY, false);
        event.preventDefault();
      });
      wrap.addEventListener('pointerup', function(event) {
        if (!dragging) return;
        dragging = false;
        applyPointer(event.clientX, event.clientY, true);
        try { wrap.releasePointerCapture(event.pointerId); } catch (error) {}
      });
      wrap.addEventListener('pointercancel', function() {
        if (!dragging) return;
        dragging = false;
        applyPointer(lastPointer.x, lastPointer.y, true);
      });
    }

    function buildNeedleGaugeVisualElement(card, meta, override) {
      const valueEl = card.querySelector('.field-value');
      const resolvedOverride = override || getComponentOverride(card.dataset.paramKey);
      const rawValue = resolvedOverride.value || (valueEl ? valueEl.textContent.trim() : '');
      const bounds = resolveGaugeNumericBounds(meta, rawValue);
      const wrap = document.createElement('div');
      wrap.className = 'needle-gauge-visual power-gauge-visual param-component';
      wrap.innerHTML =
        '<div class="power-gauge-svg-host">' + renderNeedleGaugeSvg(bounds, ensureGaugeVisualUid(wrap)) + '</div>' +
        '<span class="needle-gauge-value power-gauge-value"><strong>' + bounds.value + '</strong> / ' + bounds.max + '</span>' +
        '<input type="hidden" class="power-gauge-input" value="' + bounds.value + '">';
      wrap.dataset.value = String(bounds.value);
      bindPowerGaugeInteractions(wrap, card);
      return wrap;
    }

    function buildPointerGaugeVisualElement(card, meta, override) {
      const valueEl = card.querySelector('.field-value');
      const resolvedOverride = override || getComponentOverride(card.dataset.paramKey);
      const rawValue = resolvedOverride.value || (valueEl ? valueEl.textContent.trim() : '');
      const bounds = resolveGaugeNumericBounds(meta, rawValue);
      const wrap = document.createElement('div');
      wrap.className = 'pointer-gauge-visual power-gauge-visual param-component';
      wrap.innerHTML =
        '<div class="power-gauge-svg-host">' + renderPointerGaugeSvg(bounds, ensureGaugeVisualUid(wrap)) + '</div>' +
        '<span class="needle-gauge-value power-gauge-value"><strong>' + bounds.value + '</strong> / ' + bounds.max + '</span>' +
        '<input type="hidden" class="power-gauge-input" value="' + bounds.value + '">';
      wrap.dataset.value = String(bounds.value);
      bindPowerGaugeInteractions(wrap, card);
      return wrap;
    }

    function buildPowerGaugeVisualElement(card, meta, override) {
      return buildPointerGaugeVisualElement(card, meta, override);
    }

    function getParamDisplayValue(card, componentType, override, valueText) {
      const valueEl = card.querySelector('.field-value');
      const fieldText = valueEl ? valueEl.textContent.trim() : '';
      if (override.value) return String(override.value);
      if (componentType === 'switch') {
        return parseToggleInitialValue(valueText) ? 'ON' : 'OFF';
      }
      if (componentType === 'slider') {
        return fieldText || String(valueText || '');
      }
      if (componentType === 'powerGauge') {
        return fieldText || String(valueText || '');
      }
      return fieldText || String(valueText || '');
    }

    function buildParameterCardStyle(card, override) {
      const colors = getEffectiveComponentColors(override);
      return {
        backgroundColor: colors.cardColor,
        borderColor: colors.borderColor,
        borderWidth: parseInt(currentTheme.borderSize || 1, 10),
        borderRadius: parseInt(currentTheme.borderRadius || 18, 10),
        padding: parseInt(currentTheme.cardPadding || 16, 10),
        minHeight: 66,
        leftAccent: {
          visible: true,
          width: 3,
          colorStart: currentTheme.secondaryColor,
          colorEnd: colors.accentColor
        },
        iconBubble: {
          size: 38,
          backgroundColor: 'rgba(37,99,235,0.14)',
          iconColor: colors.iconColor
        },
        fieldLabel: {
          fontSize: 11,
          fontWeight: 800,
          color: currentTheme.mutedTextColor,
          uppercase: true
        },
        fieldValue: {
          fontSize: 16,
          fontWeight: 700,
          color: colors.textColor
        }
      };
    }

    function buildAccessoryWidgetTree(componentType, card, override, meta, valueText, binding) {
      const colors = getEffectiveComponentColors(override);
      const trackColor = override.statusColor || currentTheme.toggleOffColor;

      if (componentType === 'switch') {
        const isOn = parseToggleInitialValue(valueText);
        return {
          events: [buildStacEvent('onChanged', binding)],
          accessory: buildStacWidgetTree('Switch', binding, {
            activeTrackColor: currentTheme.toggleOnColor,
            activeThumbColor: currentTheme.toggleOnColor,
            inactiveTrackColor: trackColor,
            inactiveThumbColor: currentTheme.toggleOnColor,
            useLiveBinding: true,
            valueBinding: binding,
            trueValues: ['ON', '1', 'true', 'yes'],
            falseValues: ['OFF', '0', 'false', 'no'],
            initialValue: isOn,
            checked: isOn
          }, [buildStacEvent('onChanged', binding)])
        };
      }

      if (componentType === 'slider') {
        const min = meta.minimum === null || typeof meta.minimum === 'undefined' || meta.minimum === '' ? 0 : parseFloat(meta.minimum);
        const max = meta.maximum === null || typeof meta.maximum === 'undefined' || meta.maximum === '' ? 255 : parseFloat(meta.maximum);
        const step = meta.step === null || typeof meta.step === 'undefined' || meta.step === '' ? 1 : parseFloat(meta.step);
        const safeMin = Number.isFinite(min) ? min : 0;
        const safeMax = Number.isFinite(max) ? max : 255;
        const safeStep = Number.isFinite(step) ? step : 1;
        const numericValue = valueText !== '' ? parseFloat(valueText) : (meta.default !== '' && meta.default != null ? parseFloat(meta.default) : safeMin);
        const initialValue = Number.isFinite(numericValue) ? numericValue : safeMin;
        const metaIcon = getParamMetaIcon(card);
        const metaValue = String(card.dataset.sequence || '1');
        return {
          events: [buildStacEvent('onChanged', binding)],
          accessory: buildStacWidgetTree('metaChip', binding, {
            icon: metaIcon,
            value: metaValue,
            backgroundColor: override.statusColor || 'rgba(37,99,235,0.12)',
            textColor: colors.accentColor,
            iconColor: colors.accentColor,
            readOnly: true
          }, []),
          rangeRow: buildStacWidgetTree('schemaSlider', binding, {
            min: safeMin,
            max: safeMax,
            step: safeStep,
            valueBinding: binding,
            divisions: safeStep > 0 ? Math.max(1, Math.round((safeMax - safeMin) / safeStep)) : null,
            activeColor: colors.accentColor,
            inactiveColor: 'rgba(106,142,174,0.28)',
            thumbColor: colors.accentColor,
            showMinMaxLabels: true,
            showRangeLine: true,
            rangeLineColor: colors.accentColor,
            layout: 'stacked',
            useLiveBinding: true,
            initialValue: initialValue
          }, [buildStacEvent('onChanged', binding)])
        };
      }

      if (componentType === 'powerGauge' || componentType === 'needleGauge') {
        const bounds = resolveGaugeNumericBounds(meta, valueText);
        const gaugeProps = buildAnimatedRadialGaugeProps(bounds, {
          activeColor: colors.accentColor,
          inactiveColor: '#D9DEEB',
          needleColor: '#0F2550'
        });
        const gaugeEvents = [
          buildStacEvent('onChanged', binding),
          { event: 'onValueCommitted', action: { type: 'send', binding: binding } }
        ];
        const gaugeWidget = componentType === 'powerGauge' ? 'AnimatedRadialGauge' : 'NeedleGauge';
        return {
          events: gaugeEvents,
          accessory: buildStacWidgetTree('metaChip', binding, {
            icon: getParamMetaIcon(card),
            valueBinding: binding,
            useLiveBinding: true,
            backgroundColor: override.statusColor || 'rgba(37,99,235,0.12)',
            textColor: colors.accentColor,
            iconColor: colors.accentColor,
            readOnly: true
          }, []),
          gaugeRow: buildStacWidgetTree(gaugeWidget, binding, Object.assign({}, gaugeProps, { useLiveBinding: true }), gaugeEvents)
        };
      }

      if (componentType === 'linearGauge') {
        const bounds = resolveGaugeNumericBounds(meta, valueText);
        return {
          events: [],
          accessory: buildStacWidgetTree('metaChip', binding, {
            icon: getParamMetaIcon(card),
            valueBinding: binding,
            useLiveBinding: true,
            backgroundColor: override.statusColor || 'rgba(37,99,235,0.12)',
            textColor: colors.accentColor,
            iconColor: colors.accentColor,
            readOnly: true
          }, []),
          gaugeRow: buildStacWidgetTree('LinearGauge', binding, {
            minimum: bounds.min,
            maximum: bounds.max,
            valueBinding: binding,
            unit: '%',
            activeColor: colors.accentColor,
            inactiveColor: '#D9DEEB',
            showLabel: true,
            useLiveBinding: true
          }, [])
        };
      }

      if (componentType === 'elevatedButton') {
        return {
          events: [{ event: 'onTap', action: { type: 'send', binding: binding } }],
          accessory: buildStacWidgetTree('ElevatedButton', binding, {
            labelBinding: binding,
            backgroundColor: colors.accentColor,
            textColor: '#FFFFFF',
            useLiveBinding: true
          }, [{ event: 'onTap', action: { type: 'send', binding: binding } }])
        };
      }

      if (componentType === 'button') {
        const dotColor = override.statusColor || colors.accentColor;
        return {
          events: [buildStacEvent('onTap', binding)],
          accessory: buildStacWidgetTree('PowerButton', binding, {
            color: dotColor,
            borderColor: dotColor,
            size: 36,
            borderWidth: 3,
            shape: 'circle',
            useLiveBinding: true
          }, [buildStacEvent('onTap', binding)])
        };
      }

      if (componentType === 'dropdown') {
        const options = parseOptionPairs(meta);
        const selected = valueText || (options[0] ? options[0].value : '');
        return {
          events: [buildStacEvent('onChanged', binding)],
          accessory: buildStacWidgetTree('DropdownButton', binding, {
            options: options,
            hint: 'Select',
            backgroundColor: override.statusColor || 'rgba(37,99,235,0.12)',
            textColor: currentTheme.accentColor,
            useLiveBinding: true,
            valueBinding: binding,
            initialValue: selected
          }, [buildStacEvent('onChanged', binding)])
        };
      }

      if (componentType === 'segmented') {
        const options = parseOptionPairs(meta);
        const selectedOptions = options.length ? options : [{ label: 'Low', value: 'Low' }, { label: 'Mid', value: 'Mid' }, { label: 'High', value: 'High' }];
        const selected = valueText || (selectedOptions[0] ? selectedOptions[0].value : '');
        const segmentEvents = [
          buildStacEvent('onChanged', binding),
          { event: 'onSelected', action: { type: 'send', binding: binding } }
        ];
        return {
          events: segmentEvents,
          accessory: buildStacWidgetTree('metaChip', binding, {
            icon: getParamMetaIcon(card),
            value: String(selected || ''),
            backgroundColor: override.statusColor || 'rgba(37,99,235,0.12)',
            textColor: colors.accentColor,
            iconColor: colors.accentColor,
            readOnly: true
          }, []),
          segmentedRow: buildStacWidgetTree('SegmentedControl', binding, {
            options: selectedOptions,
            valueBinding: binding,
            initialValue: selected,
            selectedColor: colors.accentColor,
            selectedTextColor: '#FFFFFF',
            unselectedColor: 'rgba(106,142,174,0.14)',
            unselectedTextColor: colors.textColor,
            borderColor: currentTheme.borderColor,
            useLiveBinding: true
          }, segmentEvents)
        };
      }

      if (componentType === 'textField') {
        const initialValue = valueText || meta.default || '';
        return {
          events: [buildStacEvent('onChanged', binding)],
          accessory: buildStacWidgetTree('TextField', binding, {
            keyboardType: 'text',
            hintText: meta.default || card.dataset.paramName || 'Enter value',
            maxLines: 1,
            readOnly: false,
            useLiveBinding: true,
            valueBinding: binding,
            initialValue: initialValue
          }, [buildStacEvent('onChanged', binding)])
        };
      }

      const readOnlyValue = valueText || String(card.dataset.sequence || '1');
      return {
        events: [],
        accessory: buildStacWidgetTree('metaChip', binding, {
          icon: getParamMetaIcon(card),
          value: readOnlyValue,
          backgroundColor: override.statusColor || 'rgba(37,99,235,0.12)',
          textColor: colors.accentColor,
          iconColor: colors.accentColor,
          readOnly: true
        }, [])
      };
    }

    function isStackedParamLayout(componentType) {
      return ['slider', 'powerGauge', 'needleGauge', 'segmented', 'linearGauge'].indexOf(componentType) !== -1;
    }

    function buildParameterCardWidgetTree(componentType, card, override, meta, valueText, binding, label, icon) {
      const resolvedType = componentType || 'value';
      const accessoryBundle = buildAccessoryWidgetTree(resolvedType, card, override, meta, valueText, binding);
      const children = [accessoryBundle.accessory];
      if (resolvedType === 'slider' && accessoryBundle.rangeRow) {
        children.push(accessoryBundle.rangeRow);
      }
      if ((resolvedType === 'powerGauge' || resolvedType === 'needleGauge' || resolvedType === 'linearGauge') && accessoryBundle.gaugeRow) {
        children.push(accessoryBundle.gaugeRow);
      }
      if (resolvedType === 'segmented' && accessoryBundle.segmentedRow) {
        children.push(accessoryBundle.segmentedRow);
      }
      return {
        type: 'ParameterCard',
        properties: {
          binding: binding,
          valueBinding: binding,
          textBinding: binding,
          useLiveBinding: true,
          props: {
            label: label,
            valueBinding: binding,
            textBinding: binding,
            icon: icon,
            variant: resolvedType,
            layout: isStackedParamLayout(resolvedType) ? 'stacked' : 'row',
            cardStyle: buildParameterCardStyle(card, override),
            metaIcon: getParamMetaIcon(card),
            metaValue: String(card.dataset.sequence || '1'),
            useLiveBinding: true
          },
          events: accessoryBundle.events || []
        },
        children: children
      };
    }

    function buildInlineComponentSchema(componentType, card, override, meta, valueText, binding) {
      const resolvedType = componentType || 'value';
      meta = mergeParamMetaWithOverride(meta, override);

      // Registered (schema-defined) components reference the registry by name and
      // deliberately omit component.widgetTree so the engine pulls the tree from
      // registerComponents[name] and injects {{binding}}.
      const registered = getRegisteredComponent(resolvedType);
      if (registered) {
        const initialValue = resolveRegisteredComponentValue(registered, valueText, meta);
        const displayValue = formatRegisteredDisplayValue(registered, initialValue);
        const colors = getEffectiveComponentColors(override);
        const output = {
          type: resolvedType,
          renderMode: 'dynamic',
          flutterWidget: registered.name,
          binding: binding,
          initialValue: initialValue,
          displayValue: displayValue,
          styleOverrides: {
            backgroundColor: colors.cardColor,
            borderColor: colors.borderColor,
            accentColor: colors.accentColor,
            iconColor: colors.iconColor,
            textColor: colors.textColor,
            statusColor: colors.statusColor
          }
        };
        if (registered.valueType === 'number') {
          output.props = {
            unit: registered.unit || null,
            minimum: registered.min != null ? registered.min : null,
            maximum: registered.max != null ? registered.max : null,
            step: registered.step != null ? registered.step : 1
          };
        }
        if (Array.isArray(registered.options) && registered.options.length) {
          output.options = registered.options;
          output.props = Object.assign({}, output.props || {}, { options: registered.options });
        }
        if (registered.valueType === 'number') {
          output.events = buildControlInteractionEvents('slider', binding);
        } else if (registered.valueType === 'boolean') {
          output.events = buildControlInteractionEvents('switch', binding);
        } else if (registered.valueType === 'enum') {
          output.events = buildControlInteractionEvents('segmented', binding);
        } else if (registered.valueType === 'trigger') {
          output.events = buildControlInteractionEvents('button', binding);
        }
        return output;
      }

      const entry = getComponentLibraryEntry(resolvedType) || getComponentLibraryEntry('value');
      const label = String(override.title || card.dataset.paramName || 'Parameter');
      const icon = getParamIcon(card);
      const cardWidgetTree = buildParameterCardWidgetTree(resolvedType, card, override, meta, valueText, binding, label, icon);
      const base = {
        type: resolvedType,
        renderMode: 'dynamic',
        flutterWidget: entry.flutterWidget || 'Text',
        binding: binding,
        widgetTree: cardWidgetTree
      };

      if (resolvedType === 'switch') {
        const isOn = parseToggleInitialValue(valueText);
        return Object.assign(base, {
          flutterWidget: 'Switch',
          initialValue: isOn,
          displayValue: isOn ? 'ON' : 'OFF',
          events: buildControlInteractionEvents('switch', binding)
        });
      }

      if (resolvedType === 'slider') {
        const min = meta.minimum === null || typeof meta.minimum === 'undefined' || meta.minimum === '' ? 0 : parseFloat(meta.minimum);
        const max = meta.maximum === null || typeof meta.maximum === 'undefined' || meta.maximum === '' ? 255 : parseFloat(meta.maximum);
        const step = meta.step === null || typeof meta.step === 'undefined' || meta.step === '' ? 1 : parseFloat(meta.step);
        const safeMin = Number.isFinite(min) ? min : 0;
        const safeMax = Number.isFinite(max) ? max : 255;
        const safeStep = Number.isFinite(step) ? step : 1;
        const numericValue = valueText !== '' ? parseFloat(valueText) : (meta.default !== '' && meta.default != null ? parseFloat(meta.default) : safeMin);
        const initialValue = Number.isFinite(numericValue) ? numericValue : safeMin;
        return Object.assign(base, {
          flutterWidget: 'schemaSlider',
          initialValue: initialValue,
          displayValue: getParamDisplayValue(card, resolvedType, override, valueText),
          events: buildControlInteractionEvents('slider', binding)
        });
      }

      if (resolvedType === 'powerGauge' || resolvedType === 'needleGauge') {
        const bounds = resolveGaugeNumericBounds(meta, valueText);
        const colors = getEffectiveComponentColors(override);
        return Object.assign(base, {
          flutterWidget: resolvedType === 'powerGauge' ? 'AnimatedRadialGauge' : 'NeedleGauge',
          initialValue: bounds.value,
          displayValue: String(bounds.value),
          props: {
            minimum: bounds.min,
            maximum: bounds.max,
            step: bounds.step,
            sweepDegrees: POWER_GAUGE_ARC_SWEEP,
            arcStart: POWER_GAUGE_ARC_START
          },
          gauge: buildAnimatedRadialGaugeProps(bounds, {
            activeColor: colors.accentColor,
            inactiveColor: '#E2E8F0',
            needleColor: '#0F2550'
          }),
          styleOverrides: {
            backgroundColor: colors.cardColor,
            borderColor: colors.borderColor,
            accentColor: colors.accentColor,
            iconColor: colors.iconColor,
            textColor: colors.textColor,
            statusColor: colors.statusColor
          },
          events: buildControlInteractionEvents('slider', binding)
        });
      }

      if (resolvedType === 'button') {
        const isOn = parseToggleInitialValue(valueText);
        return Object.assign(base, {
          flutterWidget: 'PowerButton',
          initialValue: isOn,
          displayValue: isOn ? 'ON' : 'OFF',
          events: buildControlInteractionEvents('switch', binding)
        });
      }

      if (resolvedType === 'elevatedButton') {
        return Object.assign(base, {
          flutterWidget: 'ElevatedButton',
          initialValue: valueText || null,
          displayValue: valueText || 'EXECUTE',
          events: buildControlInteractionEvents('elevatedButton', binding)
        });
      }

      if (resolvedType === 'linearGauge') {
        const min = meta.minimum === null || typeof meta.minimum === 'undefined' || meta.minimum === '' ? 0 : parseFloat(meta.minimum);
        const max = meta.maximum === null || typeof meta.maximum === 'undefined' || meta.maximum === '' ? 100 : parseFloat(meta.maximum);
        const safeMin = Number.isFinite(min) ? min : 0;
        const safeMax = Number.isFinite(max) ? max : 100;
        const numericValue = valueText !== '' ? parseFloat(String(valueText).replace(/[^\d.-]/g, '')) : (meta.default !== '' && meta.default != null ? parseFloat(meta.default) : 75);
        const initialValue = Number.isFinite(numericValue) ? numericValue : 75;
        return Object.assign(base, {
          flutterWidget: 'LinearGauge',
          initialValue: initialValue,
          displayValue: String(initialValue),
          props: {
            minimum: safeMin,
            maximum: safeMax,
            value: initialValue,
            unit: '%',
            activeColor: currentTheme.accentColor,
            inactiveColor: '#D9DEEB',
            showLabel: true
          }
        });
      }

      if (resolvedType === 'dropdown') {
        const options = parseOptionPairs(meta);
        const selected = valueText || (options[0] ? options[0].value : '');
        return Object.assign(base, {
          flutterWidget: 'DropdownButton',
          initialValue: selected,
          displayValue: selected,
          options: options,
          events: buildControlInteractionEvents('dropdown', binding)
        });
      }

      if (resolvedType === 'segmented') {
        const parsedOptions = parseOptionPairs(meta);
        const options = parsedOptions.length ? parsedOptions : [{ label: 'Low', value: 'Low' }, { label: 'Mid', value: 'Mid' }, { label: 'High', value: 'High' }];
        const selected = valueText || (options[0] ? options[0].value : '');
        return Object.assign(base, {
          flutterWidget: 'SegmentedControl',
          initialValue: selected,
          displayValue: selected,
          options: options,
          events: buildControlInteractionEvents('segmented', binding)
        });
      }

      if (resolvedType === 'textField') {
        const initialValue = valueText || meta.default || '';
        return Object.assign(base, {
          flutterWidget: 'TextField',
          initialValue: initialValue,
          displayValue: initialValue,
          events: buildControlInteractionEvents('textField', binding)
        });
      }

      const readOnlyValue = valueText || String(card.dataset.sequence || '1');
      return Object.assign(base, {
        flutterWidget: 'Text',
        initialValue: readOnlyValue,
        displayValue: getParamDisplayValue(card, resolvedType, override, valueText) || 'N/A'
      });
    }

    function buildParameterComponentSchema(componentType, card, override, meta, valueText, binding) {
      return buildInlineComponentSchema(componentType, card, override, meta, valueText, binding);
    }

    function parseToggleInitialValue(valueText) {
      const normalized = String(valueText || '').trim().toLowerCase();
      return normalized === 'on' || normalized === '1' || normalized === 'true' || normalized === 'yes';
    }

    function parseOptionList(items, values) {
      const extract = function(raw) {
        if (Array.isArray(raw)) {
          return raw.reduce(function(all, item) {
            return all.concat(extract(item));
          }, []);
        }
        const text = String(raw || '');
        const matches = Array.from(text.matchAll(/\[([^\]]*)\]/g))
          .map(function(match) { return match[1]; })
          .filter(function(item) { return item !== ''; });
        return matches.length ? matches : (text && text !== '[]' ? [text] : []);
      };
      const labels = extract(items);
      const optionValues = extract(values);
      return labels.map(function(label, index) {
        return {
          label: label,
          value: typeof optionValues[index] !== 'undefined' ? optionValues[index] : label
        };
      });
    }

    function parseOptionPairs(meta) {
      return parseOptionList(meta.items || [], meta.item_values || []);
    }

    function sortParamCardsByControlOrder(cards) {
      const order = Array.isArray(currentTheme.controlOrder) ? currentTheme.controlOrder : [];
      return cards.slice().sort(function(a, b) {
        const aKey = a.dataset.paramKey || '';
        const bKey = b.dataset.paramKey || '';
        const ai = order.indexOf(aKey);
        const bi = order.indexOf(bKey);
        if (ai === -1 && bi === -1) {
          const aSeq = parseInt(a.dataset.sequence || '0', 10) || 0;
          const bSeq = parseInt(b.dataset.sequence || '0', 10) || 0;
          return aSeq - bSeq;
        }
        if (ai === -1) return 1;
        if (bi === -1) return -1;
        return ai - bi;
      });
    }

    function buildControlAccessorySchema(componentType, card, override, meta, valueText, binding) {
      return buildParameterComponentSchema(componentType, card, override, meta, valueText, binding);
    }

    function normalizeParameterIcons(map) {
      if (!map || typeof map !== 'object' || Array.isArray(map)) {
        return {};
      }
      return Object.assign({}, map);
    }

    function buildIconOverridesSchema(controls) {
      const overrides = normalizeParameterIcons(currentTheme.parameterIcons);
      (controls || []).forEach(function(control) {
        if (control.id && control.icon) {
          overrides[control.id] = control.icon;
        }
      });
      return overrides;
    }

    function buildDeviceParametersState(controls) {
      const parameters = {};
      (controls || []).forEach(function(control) {
        if (!control.binding) return;
        const key = String(control.binding).replace(/^device\.parameters\./, '');
        if (!key) return;
        if (typeof control.initialValue !== 'undefined' && control.initialValue !== null) {
          parameters[key] = control.initialValue;
        } else if (control.component && typeof control.component.initialValue !== 'undefined' && control.component.initialValue !== null) {
          parameters[key] = control.component.initialValue;
        } else {
          parameters[key] = control.value;
        }
      });
      return parameters;
    }

    function fontAssetForFamily(family) {
      const map = {
        Poppins: 'assets/fonts/Poppins-Regular.ttf',
        Inter: 'assets/fonts/Inter-Regular.ttf',
        Roboto: 'assets/fonts/Roboto-Regular.ttf',
        'Open Sans': 'assets/fonts/OpenSans-Regular.ttf'
      };
      return map[family] || null;
    }

    function collectUsedIconNames(controls) {
      const names = new Set([
        currentTheme.parameterIcon || 'gauge',
        currentTheme.iconStyle || 'signal',
        'arrow-left',
        'arrow-right',
        'info',
        'settings',
        'chevron-left',
        'save'
      ]);
      (controls || []).forEach(function(control) {
        if (control.icon) names.add(control.icon);
        if (control.metaIcon) names.add(control.metaIcon);
        if (control.component && control.component.props && control.component.props.metaIcon) {
          names.add(control.component.props.metaIcon);
        }
      });
      return Array.from(names).filter(function(name) { return FONTAWESOME_ICONS[name]; });
    }

    function getParamLiveValue(card, componentType, override) {
      const valueEl = card.querySelector('.field-value');
      if (componentType === 'switch') {
        const toggle = card.querySelector('.param-switch');
        if (toggle) return toggle.classList.contains('is-on') ? 'ON' : 'OFF';
      }
      if (componentType === 'button') {
        const button = card.querySelector('.power-button-ui, .param-button');
        if (button) return button.classList.contains('is-on') ? 'ON' : 'OFF';
      }
      if (componentType === 'slider') {
        const input = card.querySelector('.param-range-input');
        if (input) return input.value;
      }
      if (componentType === 'powerGauge' || componentType === 'needleGauge') {
        const gauge = card.querySelector('.needle-gauge-visual, .power-gauge-visual');
        if (gauge && gauge.dataset.value) return gauge.dataset.value;
        const gaugeInput = card.querySelector('.power-gauge-input');
        if (gaugeInput) return gaugeInput.value;
      }
      if (componentType === 'dropdown') {
        const select = card.querySelector('.param-dropdown');
        if (select) return select.value;
      }
      if (componentType === 'segmented') {
        const seg = card.querySelector('.segmented-visual');
        if (seg && seg.dataset.value) return seg.dataset.value;
      }
      if (componentType === 'textField') {
        const input = card.querySelector('.param-text-input');
        if (input) return input.value;
        if (valueEl && valueEl.dataset.liveValue) return valueEl.dataset.liveValue;
      }
      if (componentType === 'linearGauge') {
        const gauge = card.querySelector('.linear-gauge-visual');
        if (gauge && gauge.dataset.value) return gauge.dataset.value;
        const raw = override.value || (valueEl ? valueEl.textContent.trim() : '');
        const parsed = parseFloat(String(raw).replace(/[^\d.-]/g, ''));
        if (Number.isFinite(parsed)) return String(Math.round(parsed));
        return '75';
      }
      if (componentType === 'elevatedButton') {
        const button = card.querySelector('.elevated-action-btn');
        if (button) return button.textContent.trim();
      }
      if (componentType === 'heartRateDisplay') {
        const raw = override.value || (valueEl ? valueEl.textContent.trim() : '');
        const parsed = parseFloat(String(raw).replace(/[^\d.-]/g, ''));
        if (Number.isFinite(parsed)) return String(Math.round(parsed));
        return '72';
      }
      if (componentType === 'sliderWithLabel') {
        const input = card.querySelector('.registered-slider-input');
        if (input) return input.value;
        const raw = override.value || (valueEl ? valueEl.textContent.trim() : '');
        const parsed = parseFloat(String(raw).replace(/[^\d.-]/g, ''));
        if (Number.isFinite(parsed)) return String(Math.round(parsed));
        return '50';
      }
      if (componentType === 'toggleButton' || componentType === 'buttonGroup' || componentType === 'fireControlPanel') {
        const visual = card.querySelector('.registered-visual');
        if (visual && visual.dataset.value) return visual.dataset.value;
      }
      return override.value || (valueEl ? valueEl.textContent.trim() : '');
    }

    function buildRightAccessoryForControl(componentType, card, meta, value, override, paramIndex) {
      const componentColors = getEffectiveComponentColors(override);
      const metaValueElement = card.querySelector('.meta-value');
      const options = parseOptionPairs(meta);
      const minRaw = meta.minimum === null || typeof meta.minimum === 'undefined' || meta.minimum === '' ? null : parseFloat(meta.minimum);
      const maxRaw = meta.maximum === null || typeof meta.maximum === 'undefined' || meta.maximum === '' ? null : parseFloat(meta.maximum);
      const stepRaw = meta.step === null || typeof meta.step === 'undefined' || meta.step === '' ? null : parseFloat(meta.step);
      const registeredComponent = getRegisteredComponent(componentType);

      if (registeredComponent) {
        return { type: 'registered', component: registeredComponent.name };
      }
      if (componentType === 'switch') {
        return {
          type: 'toggle',
          checked: parseToggleInitialValue(value),
          onColor: currentTheme.toggleOnColor,
          offColor: currentTheme.toggleOffColor
        };
      }
      if (componentType === 'slider') {
        return {
          type: 'sliderRange',
          icon: getParamMetaIcon(card),
          value: metaValueElement ? metaValueElement.textContent.trim() : String(paramIndex + 1),
          min: Number.isFinite(minRaw) ? minRaw : 0,
          max: Number.isFinite(maxRaw) ? maxRaw : 255,
          step: Number.isFinite(stepRaw) ? stepRaw : 1,
          backgroundColor: componentColors.statusColor,
          textColor: componentColors.accentColor,
          trackColor: componentColors.accentColor
        };
      }
      if (componentType === 'powerGauge' || componentType === 'needleGauge') {
        return {
          type: componentType === 'powerGauge' ? 'powerGauge' : 'needleGauge',
          icon: getParamMetaIcon(card),
          value: value,
          min: Number.isFinite(minRaw) ? minRaw : 0,
          max: Number.isFinite(maxRaw) ? maxRaw : 100,
          step: Number.isFinite(stepRaw) ? stepRaw : 1,
          gauge: buildAnimatedRadialGaugeProps(resolveGaugeNumericBounds(meta, value), {
            activeColor: componentColors.accentColor,
            inactiveColor: '#D9DEEB',
            needleColor: '#0F2550'
          })
        };
      }
      if (componentType === 'linearGauge') {
        const bounds = resolveGaugeNumericBounds(meta, value);
        return {
          type: 'linearGauge',
          value: bounds.value,
          min: bounds.min,
          max: bounds.max,
          unit: '%',
          activeColor: componentColors.accentColor,
          inactiveColor: '#D9DEEB',
          showLabel: true
        };
      }
      if (componentType === 'segmented') {
        return {
          type: 'segmented',
          icon: getParamMetaIcon(card),
          value: value,
          options: (options.length ? options : [{ label: 'Low', value: 'Low' }, { label: 'Mid', value: 'Mid' }, { label: 'High', value: 'High' }]),
          selectedColor: componentColors.accentColor,
          textColor: componentColors.textColor
        };
      }
      if (componentType === 'dropdown') {
        return {
          type: 'dropdown',
          icon: getParamMetaIcon(card),
          value: value,
          options: options,
          textColor: componentColors.textColor
        };
      }
      if (componentType === 'textField') {
        return {
          type: 'textField',
          value: value,
          placeholder: meta.default || ''
        };
      }
      if (componentType === 'button') {
        return {
          type: 'powerButton',
          checked: parseToggleInitialValue(value),
          activeColor: componentColors.accentColor
        };
      }
      if (componentType === 'elevatedButton') {
        return {
          type: 'elevatedButton',
          label: value || 'EXECUTE',
          backgroundColor: componentColors.accentColor,
          textColor: componentColors.textColor || '#FFFFFF'
        };
      }
      return {
        type: 'metaChip',
        icon: getParamMetaIcon(card),
        value: metaValueElement ? metaValueElement.textContent.trim() : String(paramIndex + 1),
        backgroundColor: componentColors.statusColor,
        textColor: componentColors.accentColor
      };
    }

    function buildControlsSchema() {
      const controls = [];
      const controlActions = {};
      const componentTypeOverrides = {};
      const pagesMap = {};

      document.querySelectorAll('.page-slide').forEach(function(slide, pageIndex) {
        const pageNo = String(slide.dataset.pageNo || String(pageIndex + 1));
        const pageName = String(slide.dataset.pageTitle || ('Page ' + pageNo));
        const pageId = 'page_' + pageNo;
        if (!pagesMap[pageNo]) {
          pagesMap[pageNo] = { id: pageId, pageNo: pageNo, title: pageName, index: pageIndex, controlIds: [] };
        }

        sortParamCardsByControlOrder(Array.from(slide.querySelectorAll('.param-card'))).forEach(function(card, paramIndex) {
          const parameterKey = card.dataset.paramKey || '';
          const controlId = parameterKey || ('control_' + pageNo + '_' + paramIndex);
          const override = getComponentOverride(parameterKey);
          const meta = getEffectiveParamMeta(card);
          const labelElement = card.querySelector('.field-label');
          const componentType = getResolvedParamComponentType(card);
          const registeredComponent = getRegisteredComponent(componentType);
          const componentEntry = getAnyComponentEntry(componentType);
          const icon = getParamIcon(card);
          const label = String(override.title || (labelElement ? labelElement.textContent.trim() : card.dataset.paramName) || 'Parameter');
          const value = String(getParamLiveValue(card, componentType, override));
          const binding = controlBindingKey(parameterKey);
          const component = buildInlineComponentSchema(componentType, card, override, meta, value, binding);
          const interactionEvents = component.events && component.events.length
            ? component.events
            : buildControlInteractionEvents(componentType, binding);
          const dataAction = buildControlDataAction(controlId, componentType, binding, meta, card.dataset.paramName || label);
          controlActions[dataAction.id] = dataAction.definition;
          const options = parseOptionPairs(meta);
          const minConstraint = meta.minimum === null || typeof meta.minimum === 'undefined' || meta.minimum === '' ? null : parseFloat(meta.minimum);
          const maxConstraint = meta.maximum === null || typeof meta.maximum === 'undefined' || meta.maximum === '' ? null : parseFloat(meta.maximum);
          const stepConstraint = meta.step === null || typeof meta.step === 'undefined' || meta.step === '' ? null : parseFloat(meta.step);
          const constraints = registeredComponent && registeredComponent.valueType === 'number' ? {
            minimum: registeredComponent.min != null ? registeredComponent.min : minConstraint,
            maximum: registeredComponent.max != null ? registeredComponent.max : maxConstraint,
            step: registeredComponent.step != null ? registeredComponent.step : (Number.isFinite(stepConstraint) ? stepConstraint : 1),
            unit: registeredComponent.unit || null
          } : {
            minimum: minConstraint,
            maximum: maxConstraint,
            step: stepConstraint
          };
          const metaValueElement = card.querySelector('.meta-value');
          const componentColors = getEffectiveComponentColors(override);
          const rightAccessory = buildRightAccessoryForControl(componentType, card, meta, value, override, paramIndex);

          controls.push({
            id: controlId,
            parameterKey: parameterKeySlug(parameterKey),
            parameter: card.dataset.paramName || label,
            parameterName: card.dataset.paramName || label,
            pageNo: pageNo,
            pageName: pageName,
            pageId: pageId,
            sequence: paramIndex + 1,
            componentType: componentType,
            controlType: componentType,
            renderAs: 'ParameterCard',
            variant: controlSchemaVariant(componentType),
            flutterWidget: component.flutterWidget,
            binding: binding,
            valueBinding: binding,
            textBinding: binding,
            useLiveBinding: true,
            initialValue: component.initialValue,
            component: component,
            componentLabel: componentEntry ? componentEntry.label : componentType,
            icon: icon,
            metaIcon: getParamMetaIcon(card),
            label: label,
            value: component.displayValue,
            visible: !override.hidden,
            events: interactionEvents,
            action: dataAction.id,
            rightAccessory: rightAccessory,
            options: options,
            constraints: constraints,
            override: buildComponentOverridePayload(override),
            styles: {
              cardColor: componentColors.cardColor,
              borderColor: componentColors.borderColor,
              statusColor: componentColors.statusColor,
              accentColor: componentColors.accentColor,
              iconColor: componentColors.iconColor,
              textColor: componentColors.textColor,
              mutedTextColor: currentTheme.mutedTextColor,
              leftAccent: {
                width: 3,
                colorStart: currentTheme.secondaryColor,
                colorEnd: componentColors.accentColor
              },
              iconBubble: {
                size: 38,
                backgroundColor: 'rgba(37,99,235,0.14)',
                iconSize: parseInt(currentTheme.iconSize || 20, 10),
                iconColor: componentColors.iconColor
              }
            }
          });

          pagesMap[pageNo].controlIds.push(controlId);
          if (parameterKey && componentType) {
            componentTypeOverrides[parameterKey] = componentType;
          }
        });
      });

      controls.sort(function(a, b) {
        const pageA = parseInt(a.pageNo, 10) || 0;
        const pageB = parseInt(b.pageNo, 10) || 0;
        if (pageA !== pageB) return pageA - pageB;
        return (a.sequence || 0) - (b.sequence || 0);
      });

      const pages = Object.keys(pagesMap).map(function(pageNo) {
        return pagesMap[pageNo];
      }).sort(function(a, b) {
        return a.index - b.index;
      });

      return {
        controls: controls,
        pages: pages,
        componentTypeOverrides: componentTypeOverrides,
        controlActions: controlActions
      };
    }

    // DOM Elements
    const track = document.getElementById('slide-track');
    const slides = Array.from(document.querySelectorAll('.page-slide'));
    const pageButtons = Array.from(document.querySelectorAll('.page-button'));
    const prevButton = document.getElementById('prev-slide');
    const nextButton = document.getElementById('next-slide');
    const progressBar = document.getElementById('slide-progress');
    const title = document.getElementById('active-page-title');
    const subtitle = document.getElementById('active-page-subtitle');
    const studio = document.getElementById('ui-studio');
    const studioBackdrop = document.getElementById('studio-backdrop');
    const studioStatus = document.getElementById('studio-status');
    const schemaOutput = document.getElementById('schema-output');
    const orderList = document.getElementById('control-order-list');
    const phoneShell = document.querySelector('.phone-shell');
    const studioPreviewMount = document.getElementById('studio-preview-mount');
    const studioPreviewFrame = document.getElementById('studio-preview-frame');
    const studioPreviewZoomLabel = document.getElementById('studio-preview-zoom-label');
    const studioPreviewZoomInBtn = document.getElementById('studio-preview-zoom-in');
    const studioPreviewZoomOutBtn = document.getElementById('studio-preview-zoom-out');
    const studioPreviewZoomFitBtn = document.getElementById('studio-preview-zoom-fit');
    const pagePreviewMount = document.getElementById('page-preview-mount');
    const pagePreviewFrame = document.getElementById('page-preview-frame');
    const pagePreviewZoomLabel = document.getElementById('page-preview-zoom-label');
    const pagePreviewZoomInBtn = document.getElementById('page-preview-zoom-in');
    const pagePreviewZoomOutBtn = document.getElementById('page-preview-zoom-out');
    const pagePreviewZoomFitBtn = document.getElementById('page-preview-zoom-fit');
    const PREVIEW_ZOOM_MIN = 0.35;
    const PREVIEW_ZOOM_MAX = 2.5;
    const PREVIEW_ZOOM_STEP = 0.12;
    const previewZoomStates = {
      page: { autoFit: false, manualScale: 1 },
      studio: { autoFit: true, manualScale: 1 }
    };

    function queryPreviewAll(selector) {
      return phoneShell ? Array.from(phoneShell.querySelectorAll(selector)) : [];
    }
    const phoneHome = document.createComment('phone-shell-home');
    const iconPicker = document.getElementById('param-icon-picker');
    const iconPickerGrid = document.getElementById('icon-picker-grid');
    const iconPickerTitle = document.getElementById('icon-picker-title');
    const paramIconSearchInput = document.getElementById('param-icon-search-input');
    const componentPicker = document.getElementById('param-component-picker');
    const componentPickerGrid = document.getElementById('component-picker-grid');
    const componentPickerTitle = document.getElementById('component-picker-title');
    const themeInputs = Array.from(document.querySelectorAll('[data-theme-key]'));
    const iconSearchInput = document.getElementById('icon-search-input');
    const iconLibraryGrid = document.getElementById('icon-library-grid');
    const paramsButtonsContainer = document.getElementById('params-buttons-container');
    const copySchemaButton = document.getElementById('copy-schema-btn');
    const saveToast = document.getElementById('save-toast');
    const deviceInfoModal = document.getElementById('device-info-modal');
    const deviceInfoTitle = document.getElementById('device-info-title');
    const deviceInfoContent = document.getElementById('device-info-content');

    const defaultTheme = <?= json_encode($default_ui_theme, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    const savedTheme = <?= json_encode($active_ui_theme, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    const pageData = <?= json_encode($pages, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    const modelNo = <?= json_encode($modelno, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    const modalId = <?= json_encode((int) $modal_id, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    const saveThemeUrl = <?= json_encode(get_uri('items/save_view_page_ui_theme'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    const shouldSyncLinkedSchema = <?= $schema_sync ? 'true' : 'false' ?>;
    const deviceInfoUrl = <?= json_encode($device_info_api_url ?? '', JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    const schemaApiUrl = <?= json_encode($schema_api_url ?? '', JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    const parametersApiUrl = <?= json_encode($parameters_api_url ?? '', JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    const editPageLinkingUrl = <?= json_encode(get_uri('items/api_page_linking/' . (int) $modal_id . '?modelno=' . rawurlencode($modelno)), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;

    let currentTheme = Object.assign({}, defaultTheme, savedTheme);
    currentTheme.parameterIcons = normalizeParameterIcons(currentTheme.parameterIcons);
    currentTheme.componentOverrides = currentTheme.componentOverrides && typeof currentTheme.componentOverrides === 'object' && !Array.isArray(currentTheme.componentOverrides) ? currentTheme.componentOverrides : {};
    let activeIndex = 0;
    let activeIconCard = null;
    let activeIconTargetType = 'param';
    let activeComponentCard = null;
    let selectedParamKey = '';
    let draggedOrderKey = '';
    let draggedOrderPageNo = '';
    let lastHorizontalNavAt = 0;

    // Insert phone home comment
    if (phoneShell && phoneShell.parentNode) {
      phoneShell.parentNode.insertBefore(phoneHome, phoneShell);
    }

    // Helper Functions
    function iconSvg(name) {
      return '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' + (FONTAWESOME_ICONS[name] || FONTAWESOME_ICONS.gauge) + '</svg>';
    }

    function bindClick(id, handler) {
      const element = document.getElementById(id);
      if (element) {
        element.addEventListener('click', handler);
      }
      return element;
    }

    function escapeHtml(value) {
      return String(value || '').replace(/[&<>"']/g, function(character) {
        return {
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#039;'
        }[character];
      });
    }

    function setStudioStatus(message) {
      if (studioStatus) studioStatus.textContent = message || '';
    }

    function showSaveToast(message) {
      if (!saveToast) return;
      saveToast.textContent = message || 'Changes saved';
      saveToast.classList.add('is-visible');
      window.clearTimeout(showSaveToast.timer);
      showSaveToast.timer = window.setTimeout(function() {
        saveToast.classList.remove('is-visible');
      }, 2400);
    }

    function closeDeviceInfoModal() {
      if (!deviceInfoModal || !phoneShell) return;
      deviceInfoModal.classList.remove('is-open');
      deviceInfoModal.setAttribute('aria-hidden', 'true');
      phoneShell.classList.remove('is-showing-info');
      const infoButton = document.getElementById('device-info-btn');
      if (infoButton) infoButton.focus();
    }

    function firstPresent() {
      for (let index = 0; index < arguments.length; index += 1) {
        const value = arguments[index];
        if (value !== null && typeof value !== 'undefined' && String(value).trim() !== '') return value;
      }
      return '';
    }

    function normalizeDeviceInfo(responseData) {
      const details = Array.isArray(responseData && responseData.node_details) ? responseData.node_details : [];
      const node = details.find(function(item) { return item && item.config; }) || details[0] || {};
      const config = node && node.config && typeof node.config === 'object' ? node.config : {};
      const info = config.info && typeof config.info === 'object' ? config.info : (responseData && responseData.info ? responseData.info : {});
      const devices = Array.isArray(config.devices) ? config.devices : [];
      const device = devices[0] && typeof devices[0] === 'object' ? devices[0] : {};
      const attributeList = []
        .concat(Array.isArray(config.attributes) ? config.attributes : [])
        .concat(Array.isArray(device.attributes) ? device.attributes : []);
      const attributes = {};
      attributeList.forEach(function(attribute) {
        if (attribute && attribute.name) attributes[String(attribute.name).toLowerCase()] = attribute.value;
      });

      return {
        deviceId: firstPresent(node.node_id, node.id, device['Device Id'], attributes['device id'], modelNo),
        name: firstPresent(info.name, device.name, info.product, info.product_name, modelNo),
        type: firstPresent(info.type, info.product_type, device.type, info.model, attributes.model, 'Gateway'),
        network: firstPresent(info.SSID, info.ssid, info.Access_Point, info.access_point, attributes.model, 'Efactor'),
        firmware: firstPresent(info.fw_version, info.firmware_version, info.version, responseData && responseData.version, '—'),
        guaranteeUrl: firstPresent(info.Guarantee_Card, info.guarantee_card, device.Guarantee_Card),
        userGuideUrl: firstPresent(info.Video, info.user_guide, info.User_Guide, device.Video),
        helpUrl: firstPresent(info.Need_Help, info.need_help, device.Need_Help),
        ticketsUrl: firstPresent(info.Track_Tickets, info.track_tickets, info.Auto_Test, info.auto_test)
      };
    }

    function createDetailRow(options) {
      const row = document.createElement(options.href ? 'a' : 'div');
      const copy = document.createElement('div');
      const label = document.createElement('span');
      row.className = 'device-detail-row';
      copy.className = 'device-detail-copy';
      label.className = 'device-detail-label';
      label.textContent = options.label;
      copy.appendChild(label);

      if (options.valueBelow) {
        const value = document.createElement('span');
        value.className = 'device-detail-value';
        value.textContent = String(options.valueBelow || '—');
        copy.appendChild(value);
      }
      row.appendChild(copy);

      if (options.sideValue) {
        const sideValue = document.createElement('span');
        sideValue.className = 'device-detail-side-value';
        sideValue.textContent = String(options.sideValue);
        row.appendChild(sideValue);
      } else if (options.copyValue) {
        const copyButton = document.createElement('button');
        copyButton.type = 'button';
        copyButton.className = 'device-detail-action';
        copyButton.setAttribute('aria-label', 'Copy ' + options.label);
        copyButton.innerHTML = iconSvg('copy');
        copyButton.addEventListener('click', function() {
          const value = String(options.copyValue || '');
          if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(value).then(function() { showSaveToast('Device ID copied'); });
          } else {
            const input = document.createElement('textarea');
            input.value = value;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            input.remove();
            showSaveToast('Device ID copied');
          }
        });
        row.appendChild(copyButton);
      } else if (options.icon) {
        const icon = document.createElement('span');
        icon.className = 'device-detail-action';
        icon.innerHTML = iconSvg(options.icon);
        row.appendChild(icon);
      }

      if (options.href) {
        row.href = options.href;
        row.target = '_blank';
        row.rel = 'noopener noreferrer';
        row.style.textDecoration = 'none';
      }
      return row;
    }

    function renderGatewayDetails(details) {
      if (!deviceInfoContent) return;
      deviceInfoContent.innerHTML = '';

      const identityGroup = document.createElement('section');
      identityGroup.className = 'device-details-group';
      identityGroup.appendChild(createDetailRow({ label: 'Device Id', valueBelow: details.deviceId, copyValue: details.deviceId }));
      identityGroup.appendChild(createDetailRow({ label: 'Name', sideValue: details.name }));
      identityGroup.appendChild(createDetailRow({ label: 'Type', sideValue: details.type }));
      identityGroup.appendChild(createDetailRow({ label: 'Network Connection', valueBelow: details.network, icon: 'wifi' }));
      deviceInfoContent.appendChild(identityGroup);

      const firmwareGroup = document.createElement('section');
      firmwareGroup.className = 'device-details-group';
      firmwareGroup.appendChild(createDetailRow({ label: 'Firmware Version', sideValue: details.firmware }));
      deviceInfoContent.appendChild(firmwareGroup);

      const supportGroup = document.createElement('section');
      supportGroup.className = 'device-details-group';
      supportGroup.appendChild(createDetailRow({ label: 'Guarantee Card', valueBelow: 'Terms & Conditions', href: details.guaranteeUrl, icon: 'file' }));
      supportGroup.appendChild(createDetailRow({ label: 'User Guide', valueBelow: 'Troubleshooting', href: details.userGuideUrl, icon: 'chevron-right' }));
      supportGroup.appendChild(createDetailRow({ label: 'Need Help?', valueBelow: 'Chat with us', href: details.helpUrl, icon: 'help-circle' }));
      supportGroup.appendChild(createDetailRow({ label: 'Track tickets', valueBelow: 'Your service requests', href: details.ticketsUrl, icon: 'inbox' }));
      deviceInfoContent.appendChild(supportGroup);
      deviceInfoContent.scrollTop = 0;
    }

    function openDeviceInfoFromSchema() {
      if (!deviceInfoModal || !deviceInfoContent || !phoneShell) return;
      if (deviceInfoTitle) deviceInfoTitle.textContent = 'Gateway Details';
      deviceInfoContent.innerHTML = '<div class="info-modal-state">Loading gateway details…</div>';
      deviceInfoModal.classList.add('is-open');
      deviceInfoModal.setAttribute('aria-hidden', 'false');
      phoneShell.classList.add('is-showing-info');
      if (deviceInfoContent) deviceInfoContent.scrollTop = 0;

      fetch(deviceInfoUrl, { method: 'GET', credentials: 'same-origin' })
        .then(function(response) {
          if (!response.ok) throw new Error('Information request failed');
          return response.json();
        })
        .then(function(responseData) {
          renderGatewayDetails(normalizeDeviceInfo(responseData));
        })
        .catch(function() {
          renderGatewayDetails(normalizeDeviceInfo({ info: { name: modelNo, type: 'Gateway' } }));
        });
    }

    function getFilteredIconNames(filterText) {
      const searchTerm = String(filterText || '').trim().toLowerCase();
      const icons = Object.keys(FONTAWESOME_ICONS);
      if (!searchTerm) return icons;
      return icons.filter(function(name) {
        return name.toLowerCase().indexOf(searchTerm) !== -1 ||
          String(ICON_SEARCH_ALIASES[name] || '').toLowerCase().indexOf(searchTerm) !== -1;
      });
    }

    function getRangeInputStep(input) {
      if (!input) return 1;
      const raw = input.getAttribute('step');
      if (raw === null || raw === '') return 1;
      if (raw === 'any') return 0;
      const parsed = parseFloat(raw);
      return Number.isFinite(parsed) ? parsed : 1;
    }

    function snapRangeInputValue(input, value) {
      const min = parseFloat(input.min || '0');
      const max = parseFloat(input.max || '100');
      const step = getRangeInputStep(input);
      let numeric = parseFloat(value);
      if (!Number.isFinite(numeric)) numeric = min;
      let clamped = Math.max(min, Math.min(max, numeric));
      if (step > 0) {
        const steps = Math.round((clamped - min) / step);
        clamped = min + steps * step;
        const stepText = String(step);
        const decimals = stepText.indexOf('.') !== -1 ? (stepText.split('.')[1] || '').length : 0;
        if (decimals > 0) clamped = parseFloat(clamped.toFixed(decimals));
      }
      return clamped;
    }

    function parseRangeInputValue(input) {
      return snapRangeInputValue(input, parseFloat(input.value || input.min || '0'));
    }

    function setRangeInputValue(input, value) {
      if (!input || input.type !== 'range') return parseFloat(value || '0');
      const snapped = snapRangeInputValue(input, value);
      input.value = String(snapped);
      return snapped;
    }

    function formatRangeValue(input) {
      const key = input.dataset.themeKey || '';
      const suffixMap = {
        fontSize: 'px',
        letterSpacing: 'px',
        borderRadius: 'px',
        borderSize: 'px',
        shadowStrength: '%',
        cardBlur: 'px',
        pagePadding: 'px',
        cardPadding: 'px',
        cardSpacing: 'px',
        cardMargin: 'px',
        buttonSize: 'px',
        buttonRadius: 'px',
        headerHeight: 'px',
        iconSize: 'px'
      };
      const numeric = parseRangeInputValue(input);
      if (key === 'lineHeight') {
        const step = getRangeInputStep(input);
        const decimals = step <= 0.05 ? 2 : 1;
        return numeric.toFixed(decimals);
      }
      if (key === 'fontWeight') {
        return String(Math.round(numeric));
      }
      const step = getRangeInputStep(input);
      if (step > 0 && String(step).indexOf('.') !== -1) {
        const decimals = (String(step).split('.')[1] || '').length;
        return numeric.toFixed(decimals) + (suffixMap[key] || '');
      }
      return String(Math.round(numeric)) + (suffixMap[key] || '');
    }

    function updateRangeProgress(input) {
      if (!input || input.type !== 'range') return;
      const min = parseFloat(input.min || '0');
      const max = parseFloat(input.max || '100');
      const value = parseRangeInputValue(input);
      const span = max - min;
      const pct = span > 0 ? ((value - min) / span) * 100 : 0;
      input.style.setProperty('--range-progress', Math.max(0, Math.min(100, pct)) + '%');
    }

    function updateRangeBadge(input) {
      if (!input || input.type !== 'range') return;
      updateRangeProgress(input);
      const rangeField = input.closest('.studio-range-field');
      const legacyField = input.closest('.studio-field');
      const value = formatRangeValue(input);
      if (rangeField) {
        const output = rangeField.querySelector('.studio-range-output');
        if (output) output.textContent = value;
        return;
      }
      if (!legacyField) return;
      legacyField.classList.add('has-range');
      legacyField.dataset.rangeValue = value;
    }

    function parseParamApiMeta(card) {
      try {
        const parsed = JSON.parse(card.dataset.apiMeta || '{}');
        return parsed && typeof parsed === 'object' && !Array.isArray(parsed) ? parsed : {};
      } catch (error) {
        return {};
      }
    }

    function detectParamComponentTypeFromApi(card) {
      if (!card) return 'value';
      const meta = parseParamApiMeta(card);
      const paramName = String(card.dataset.paramName || '').toLowerCase();
      const apiType = String(meta.ui_type || '').toLowerCase();
      const apiAccess = String(meta.access || '').toLowerCase();
      const isAutoTriggerTimer = apiType.indexOf('auto trigger') !== -1 && apiType.indexOf('timer') !== -1;
      if (paramName.indexOf('brightness') !== -1 || paramName.indexOf('dimmer') !== -1 || paramName.indexOf('light level') !== -1) return 'sliderWithLabel';
      if (paramName.indexOf('lock') !== -1 || paramName.indexOf('unlock') !== -1) return 'toggleButton';
      if (paramName.indexOf('level') !== -1) return 'needleGauge';
      if (apiType.indexOf('radial') !== -1 || apiType.indexOf('dial') !== -1) return 'powerGauge';
      if (apiType.indexOf('gauge') !== -1) return 'needleGauge';
      if (apiType.indexOf('segment') !== -1) return 'segmented';
      if (apiType.indexOf('drop') !== -1 || apiType.indexOf('select') !== -1) return 'dropdown';
      if (apiType.indexOf('toggle') !== -1 || apiType.indexOf('switch') !== -1) return 'switch';
      if (apiType.indexOf('slider') !== -1 || apiType.indexOf('range') !== -1) return 'slider';
      if (!isAutoTriggerTimer && (apiType.indexOf('trigger') !== -1 || apiType.indexOf('button') !== -1)) return 'button';
      if (apiType.indexOf('text') !== -1 && apiAccess.indexOf('write') !== -1) return 'textField';
      return 'value';
    }

    function detectParamComponentType(card) {
      if (!card) return 'value';
      const registeredVisual = card.querySelector('.registered-visual');
      if (registeredVisual && card.dataset.componentType && isRegisteredComponentType(card.dataset.componentType)) {
        return card.dataset.componentType;
      }
      const meta = parseParamApiMeta(card);
      const paramName = String(card.dataset.paramName || '').toLowerCase();
      const apiType = String(meta.ui_type || '').toLowerCase();
      const apiAccess = String(meta.access || '').toLowerCase();
      const isAutoTriggerTimer = apiType.indexOf('auto trigger') !== -1 && apiType.indexOf('timer') !== -1;
      if (paramName.indexOf('brightness') !== -1 || paramName.indexOf('dimmer') !== -1 || paramName.indexOf('light level') !== -1) return 'sliderWithLabel';
      if (paramName.indexOf('lock') !== -1 || paramName.indexOf('unlock') !== -1) return 'toggleButton';
      if (paramName.indexOf('level') !== -1) return 'needleGauge';
      if (Boolean(card.querySelector('.segmented-visual')) || apiType.indexOf('segment') !== -1) return 'segmented';
      if (apiType.indexOf('drop') !== -1 || apiType.indexOf('select') !== -1) return 'dropdown';
      if (Boolean(card.querySelector('.toggle-visual')) || apiType.indexOf('toggle') !== -1 || apiType.indexOf('switch') !== -1) return 'switch';
      if (Boolean(card.querySelector('.range-visual')) || apiType.indexOf('slider') !== -1 || apiType.indexOf('range') !== -1) return 'slider';
      if (Boolean(card.querySelector('.pointer-gauge-visual')) || apiType.indexOf('radial') !== -1 || apiType.indexOf('dial') !== -1) return 'powerGauge';
      if (Boolean(card.querySelector('.needle-gauge-visual')) || apiType.indexOf('gauge') !== -1) return 'needleGauge';
      if (!isAutoTriggerTimer && (Boolean(card.querySelector('.power-button-ui, .action-dot')) || apiType.indexOf('trigger') !== -1 || apiType.indexOf('button') !== -1)) return 'button';
      if (apiType.indexOf('text') !== -1 && apiAccess.indexOf('write') !== -1) return 'textField';
      return 'value';
    }

    function getResolvedParamComponentType(card) {
      if (!card) return 'value';
      const override = getComponentOverride(card.dataset.paramKey);
      if (override.componentType && override.componentType !== 'auto') {
        if (getRegisteredComponent(override.componentType) || getComponentLibraryEntry(override.componentType)) {
          return override.componentType;
        }
      }
      return detectParamComponentType(card);
    }

    function flutterWidgetForComponentType(type) {
      const registered = getRegisteredComponent(type);
      if (registered) return registered.name;
      const entry = getComponentLibraryEntry(type);
      if (entry && entry.flutterWidget) return entry.flutterWidget;
      return 'ListTile';
    }

    function removeParamAccessories(card) {
      card.querySelectorAll('.toggle-visual, .action-dot, .power-button-ui, .param-meta, .range-visual, .needle-gauge-visual, .power-gauge-visual, .segmented-visual, .param-dropdown, .param-text-input-wrap, .registered-visual, .linear-gauge-visual, .elevated-action-btn').forEach(function(node) {
        node.remove();
      });
      const valueEl = card.querySelector('.field-value');
      if (valueEl && valueEl.querySelector('input.param-text-input')) {
        const input = valueEl.querySelector('input.param-text-input');
        valueEl.textContent = input.value || valueEl.dataset.defaultValue || '';
      }
    }

    function updateRangeTrackFill(wrap, value, min, max) {
      const fill = wrap ? wrap.querySelector('.range-track-fill') : null;
      if (!fill) return;
      const safeMin = Number.isFinite(min) ? min : 0;
      const safeMax = Number.isFinite(max) ? max : 100;
      const numeric = parseFloat(value);
      const clamped = Number.isFinite(numeric) ? Math.max(safeMin, Math.min(safeMax, numeric)) : safeMin;
      const percent = safeMax === safeMin ? 0 : ((clamped - safeMin) / (safeMax - safeMin)) * 100;
      fill.style.width = percent + '%';
    }

    function buildRangeVisualElement(card, meta, override) {
      const min = meta.minimum === null || typeof meta.minimum === 'undefined' || meta.minimum === '' ? 0 : parseFloat(meta.minimum);
      const max = meta.maximum === null || typeof meta.maximum === 'undefined' || meta.maximum === '' ? 255 : parseFloat(meta.maximum);
      const step = meta.step === null || typeof meta.step === 'undefined' || meta.step === '' ? 1 : parseFloat(meta.step);
      const safeMin = Number.isFinite(min) ? min : 0;
      const safeMax = Number.isFinite(max) ? max : 255;
      const safeStep = Number.isFinite(step) ? step : 1;
      const valueEl = card.querySelector('.field-value');
      const overrideValue = override && override.value !== '' && typeof override.value !== 'undefined' ? parseFloat(override.value) : NaN;
      const parsed = Number.isFinite(overrideValue)
        ? overrideValue
        : (valueEl ? parseFloat(valueEl.textContent) : safeMin);
      const current = Number.isFinite(parsed) ? parsed : safeMin;
      const wrap = document.createElement('div');
      wrap.className = 'range-visual';
      wrap.innerHTML =
        '<div class="range-header">' +
          '<span class="range-min">Min ' + safeMin + '</span>' +
          '<strong class="range-current">' + current + '</strong>' +
          '<span class="range-max">Max ' + safeMax + '</span>' +
        '</div>' +
        '<div class="range-track-shell">' +
          '<div class="range-track-fill"></div>' +
        '</div>';
      const input = document.createElement('input');
      input.type = 'range';
      input.className = 'param-range-input param-component';
      input.min = String(safeMin);
      input.max = String(safeMax);
      input.step = String(safeStep);
      input.value = String(current);
      const trackShell = wrap.querySelector('.range-track-shell');
      if (trackShell) trackShell.appendChild(input);
      updateRangeTrackFill(wrap, current, safeMin, safeMax);
      input.addEventListener('input', function() {
        if (valueEl) valueEl.textContent = input.value;
        const currentLabel = wrap.querySelector('.range-current');
        if (currentLabel) currentLabel.textContent = input.value;
        updateRangeTrackFill(wrap, input.value, safeMin, safeMax);
        const paramOverride = getComponentOverride(card.dataset.paramKey);
        paramOverride.value = input.value;
        generateSchema();
      });
      return wrap;
    }

    function appendParamDropdown(card, meta, override) {
      const valueEl = card.querySelector('.field-value');
      const options = parseOptionPairs(meta);
      const select = document.createElement('select');
      select.className = 'param-dropdown param-component';
      select.setAttribute('aria-label', 'Select ' + (card.dataset.paramName || 'parameter'));
      if (!options.length) {
        const empty = document.createElement('option');
        empty.value = '';
        empty.textContent = 'No options';
        select.appendChild(empty);
      } else {
        options.forEach(function(option) {
          const opt = document.createElement('option');
          opt.value = option.value;
          opt.textContent = option.label;
          select.appendChild(opt);
        });
      }
      const selected = override.value || (valueEl ? valueEl.textContent.trim() : '') || (options[0] ? options[0].value : '');
      select.value = selected;
      if (valueEl) valueEl.textContent = selected;
      select.addEventListener('change', function() {
        if (valueEl) valueEl.textContent = select.value;
        getComponentOverride(card.dataset.paramKey).value = select.value;
        generateSchema();
      });
      return select;
    }

    function buildSegmentedControlElement(card, meta, override) {
      const valueEl = card.querySelector('.field-value');
      const parsedOptions = parseOptionPairs(meta);
      const options = parsedOptions.length ? parsedOptions : [{ label: 'Low', value: 'Low' }, { label: 'Mid', value: 'Mid' }, { label: 'High', value: 'High' }];
      const current = override.value || (valueEl ? valueEl.textContent.trim() : '') || (options[0] ? options[0].value : '');
      const wrap = document.createElement('div');
      wrap.className = 'segmented-visual param-component';
      wrap.setAttribute('role', 'group');
      wrap.setAttribute('aria-label', 'Select ' + (card.dataset.paramName || 'parameter'));
      wrap.dataset.value = current;
      options.forEach(function(option) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'segmented-option' + (String(option.value) === String(current) ? ' is-active' : '');
        btn.dataset.value = option.value;
        btn.textContent = option.label;
        btn.addEventListener('click', function(event) {
          event.stopPropagation();
          wrap.dataset.value = option.value;
          wrap.querySelectorAll('.segmented-option').forEach(function(node) {
            node.classList.toggle('is-active', node === btn);
          });
          if (valueEl && !valueEl.querySelector('input, select, textarea')) valueEl.textContent = option.value;
          const metaValue = card.querySelector('.meta-value');
          if (metaValue) metaValue.textContent = option.value;
          generateSchema();
        });
        wrap.appendChild(btn);
      });
      if (valueEl && !valueEl.querySelector('input, select, textarea')) valueEl.textContent = current;
      return wrap;
    }

    function createInteractiveSwitch(card, override) {
      const valueEl = card.querySelector('.field-value');
      const paramOverride = getComponentOverride(card.dataset.paramKey);
      const isOn = parseToggleInitialValue(paramOverride.value || override.value || (valueEl ? valueEl.textContent : 'OFF'));
      const toggle = document.createElement('button');
      toggle.type = 'button';
      toggle.className = 'toggle-visual param-component param-switch' + (isOn ? ' is-on' : '');
      toggle.setAttribute('aria-pressed', isOn ? 'true' : 'false');
      toggle.setAttribute('aria-label', 'Toggle ' + (card.dataset.paramName || 'parameter'));
      toggle.addEventListener('click', function(event) {
        event.stopPropagation();
        const nextOn = !toggle.classList.contains('is-on');
        toggle.classList.toggle('is-on', nextOn);
        toggle.setAttribute('aria-pressed', nextOn ? 'true' : 'false');
        const nextVal = nextOn ? 'ON' : 'OFF';
        if (valueEl) valueEl.textContent = nextVal;
        paramOverride.value = nextVal;
        generateSchema();
      });
      if (valueEl && !paramOverride.value && !override.value) valueEl.textContent = isOn ? 'ON' : 'OFF';
      return toggle;
    }

    function createInteractiveButton(card, override) {
      const valueEl = card.querySelector('.field-value');
      const paramOverride = getComponentOverride(card.dataset.paramKey);
      const isOn = parseToggleInitialValue(paramOverride.value || override.value || (valueEl ? valueEl.textContent : 'OFF'));
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'power-button-ui action-dot param-component param-button' + (isOn ? ' is-on' : ' is-off');
      button.setAttribute('aria-pressed', isOn ? 'true' : 'false');
      button.setAttribute('aria-label', 'Power ' + (card.dataset.paramName || 'parameter'));
      button.innerHTML = '<span class="power-button-ring" aria-hidden="true"></span><span class="power-button-core" aria-hidden="true">' + iconSvg('power-off') + '</span>';
      button.addEventListener('click', function(event) {
        event.stopPropagation();
        const nextOn = !button.classList.contains('is-on');
        button.classList.toggle('is-on', nextOn);
        button.classList.toggle('is-off', !nextOn);
        button.setAttribute('aria-pressed', nextOn ? 'true' : 'false');
        button.classList.add('is-pressed');
        const nextVal = nextOn ? 'ON' : 'OFF';
        if (valueEl) valueEl.textContent = nextVal;
        paramOverride.value = nextVal;
        generateSchema();
        setTimeout(function() { button.classList.remove('is-pressed'); }, 160);
      });
      if (valueEl && !paramOverride.value && !override.value) valueEl.textContent = isOn ? 'ON' : 'OFF';
      return button;
    }

    function createInteractiveElevatedButton(card, override) {
      const valueEl = card.querySelector('.field-value');
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'elevated-action-btn param-component';
      button.textContent = override.value || (valueEl ? valueEl.textContent.trim() : '') || 'EXECUTE';
      button.setAttribute('aria-label', 'Execute ' + (card.dataset.paramName || 'parameter'));
      button.addEventListener('click', function(event) {
        event.stopPropagation();
        button.classList.add('is-pressed');
        if (valueEl) valueEl.textContent = button.textContent;
        getComponentOverride(card.dataset.paramKey).value = button.textContent;
        setTimeout(function() { button.classList.remove('is-pressed'); }, 180);
        generateSchema();
      });
      return button;
    }

    function buildLinearGaugeVisualElement(card, meta, override) {
      const valueEl = card.querySelector('.field-value');
      const min = meta.minimum === null || typeof meta.minimum === 'undefined' || meta.minimum === '' ? 0 : parseFloat(meta.minimum);
      const max = meta.maximum === null || typeof meta.maximum === 'undefined' || meta.maximum === '' ? 100 : parseFloat(meta.maximum);
      const safeMin = Number.isFinite(min) ? min : 0;
      const safeMax = Number.isFinite(max) ? max : 100;
      const raw = override && override.value != null && override.value !== ''
        ? override.value
        : (valueEl ? valueEl.textContent.trim() : '75');
      const parsed = parseFloat(String(raw).replace(/[^\d.-]/g, ''));
      const current = Number.isFinite(parsed) ? parsed : 75;
      const clamped = Math.max(safeMin, Math.min(safeMax, current));
      const percent = safeMax === safeMin ? 0 : ((clamped - safeMin) / (safeMax - safeMin)) * 100;
      const wrap = document.createElement('div');
      wrap.className = 'linear-gauge-visual param-component';
      wrap.dataset.value = String(clamped);
      wrap.innerHTML =
        '<div class="linear-gauge-header">' +
          '<span class="linear-gauge-label">Signal</span>' +
          '<strong class="linear-gauge-value">' + escapeHtml(String(Math.round(clamped))) + '%</strong>' +
        '</div>' +
        '<div class="linear-gauge-track"><div class="linear-gauge-fill" style="width:' + percent + '%"></div></div>';
      if (valueEl && !valueEl.querySelector('input, select, textarea')) {
        valueEl.textContent = String(Math.round(clamped));
      }
      return wrap;
    }

    function applyTextFieldComponent(card, override) {
      const valueEl = card.querySelector('.field-value');
      if (!valueEl) return;
      if (!valueEl.dataset.defaultValue) valueEl.dataset.defaultValue = valueEl.textContent;
      const current = override.value || valueEl.dataset.defaultValue || valueEl.textContent || '';
      valueEl.innerHTML = '<input type="text" class="param-text-input param-component" value="' + escapeHtml(current) + '" aria-label="' + escapeHtml(card.dataset.paramName || 'parameter') + '">';
      const input = valueEl.querySelector('input');
      if (!input) return;
      input.addEventListener('input', function() {
        valueEl.dataset.liveValue = input.value;
        generateSchema();
      });
    }

    function initializeParamComponents() {
      queryPreviewAll('.param-card').forEach(function(card) {
        applyParameterComponentType(card, getResolvedParamComponentType(card));
      });
    }

    function isStackedComponentType(type) {
      return ['slider', 'powerGauge', 'needleGauge', 'segmented', 'linearGauge'].indexOf(type) !== -1 || isRegisteredComponentType(type);
    }

    function syncParamCardDisplay(card) {
      if (!card) return;
      const override = getComponentOverride(card.dataset.paramKey);
      if (override.hidden || card.classList.contains('is-hidden')) {
        card.style.display = 'none';
        return;
      }
      card.style.removeProperty('display');
    }

    function ensureParamMainRow(card) {
      if (!card) return null;
      let mainRow = card.querySelector('.param-main-row');
      if (!mainRow) {
        mainRow = document.createElement('div');
        mainRow.className = 'param-main-row';
        const stackChildren = '.range-visual, .needle-gauge-visual, .pointer-gauge-visual, .power-gauge-visual, .segmented-visual, .linear-gauge-visual, .registered-visual';
        Array.from(card.children).forEach(function(child) {
          if (!child.matches(stackChildren)) {
            mainRow.appendChild(child);
          }
        });
        card.insertBefore(mainRow, card.firstChild);
      }
      return mainRow;
    }

    function appendParamMetaButton(card, parent) {
      const label = card.dataset.paramName || 'parameter';
      const sequence = card.dataset.sequence || '1';
      const metaIcon = getParamMetaIcon(card);
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'param-meta';
      button.dataset.metaIconPickerTrigger = '1';
      button.dataset.metaIcon = metaIcon;
      button.setAttribute('aria-label', 'Change ' + label + ' meta icon');
      button.innerHTML = iconSvg(metaIcon) + '<span class="meta-value">' + escapeHtml(sequence) + '</span>';
      (parent || card).appendChild(button);
      return button;
    }

    function applyParameterComponentType(card, type) {
      if (!card || !type) return;
      const override = getComponentOverride(card.dataset.paramKey);
      const meta = getEffectiveParamMeta(card);
      const stacked = isStackedComponentType(type);
      removeParamAccessories(card);
      card.classList.remove('layout-stacked', 'is-registered-component');
      const mainRow = ensureParamMainRow(card);

      if (type === 'switch') {
        mainRow.appendChild(createInteractiveSwitch(card, override));
      } else if (type === 'slider') {
        card.classList.add('layout-stacked');
        appendParamMetaButton(card, mainRow);
        card.appendChild(buildRangeVisualElement(card, meta, override));
      } else if (type === 'needleGauge') {
        appendParamMetaButton(card, mainRow);
        card.classList.add('layout-stacked');
        card.appendChild(buildNeedleGaugeVisualElement(card, meta, override));
      } else if (type === 'powerGauge') {
        appendParamMetaButton(card, mainRow);
        card.classList.add('layout-stacked');
        card.appendChild(buildPointerGaugeVisualElement(card, meta, override));
      } else if (type === 'button') {
        mainRow.appendChild(createInteractiveButton(card, override));
      } else if (type === 'elevatedButton') {
        mainRow.appendChild(createInteractiveElevatedButton(card, override));
      } else if (type === 'linearGauge') {
        appendParamMetaButton(card, mainRow);
        card.classList.add('layout-stacked');
        card.appendChild(buildLinearGaugeVisualElement(card, meta, override));
      } else if (type === 'dropdown') {
        mainRow.appendChild(appendParamDropdown(card, meta, override));
      } else if (type === 'segmented') {
        appendParamMetaButton(card, mainRow);
        card.classList.add('layout-stacked');
        card.appendChild(buildSegmentedControlElement(card, meta, override));
      } else if (type === 'textField') {
        applyTextFieldComponent(card, override);
      } else if (isRegisteredComponentType(type)) {
        const registered = getRegisteredComponent(type);
        const binding = controlBindingKey(card.dataset.paramKey);
        const valueEl = card.querySelector('.field-value');
        const current = valueEl ? valueEl.textContent.trim() : '';
        if (!override.value && registered) {
          if (registered.valueType === 'number' && registered.defaultValue != null) {
            const defaultValue = String(registered.defaultValue);
            if (valueEl && !current) valueEl.textContent = defaultValue;
            override.value = defaultValue;
          } else if (registered.valueType === 'boolean') {
            const defaultValue = String(registered.defaultValue || 'UNLOCKED');
            if (valueEl && (!current || current === (card.dataset.paramName || 'Parameter'))) valueEl.textContent = defaultValue;
            override.value = defaultValue;
          } else if (registered.valueType === 'enum') {
            const defaultValue = String(registered.defaultValue || (registered.options && registered.options[0] ? registered.options[0].value : ''));
            if (valueEl && (!current || current === (card.dataset.paramName || 'Parameter'))) valueEl.textContent = defaultValue;
            override.value = defaultValue;
          }
        }
        const liveValue = String(getParamLiveValue(card, type, override) || '');
        card.classList.add('is-registered-component', 'layout-stacked');
        card.appendChild(buildRegisteredComponentPreview(card, registered, binding, liveValue));
      } else {
        appendParamMetaButton(card, mainRow);
      }

      card.dataset.componentType = type;
      syncParamCardDisplay(card);
    }

    function parseLockState(value) {
      const normalized = String(value || '').trim().toUpperCase();
      return normalized === 'LOCKED' || normalized === 'LOCK' || normalized === 'ON' || normalized === '1' || normalized === 'TRUE';
    }

    function commitRegisteredValue(card, nextValue, options) {
      const opts = options || {};
      const valueEl = card.querySelector('.field-value');
      const override = getComponentOverride(card.dataset.paramKey);
      const textValue = String(nextValue);
      override.value = textValue;
      const visual = card.querySelector('.registered-visual');
      if (visual) visual.dataset.value = textValue;
      if (valueEl && !valueEl.querySelector('input, select, textarea')) {
        valueEl.textContent = textValue;
      }
      if (opts.refresh !== false && visual && visual.dataset.registeredType) {
        refreshRegisteredComponentPreview(card);
      } else {
        generateSchema();
      }
    }

    function buildToggleButtonPreview(card, registered, binding, liveValue) {
      const wrap = document.createElement('div');
      const isLocked = parseLockState(liveValue);
      const stateLabel = isLocked ? 'LOCKED' : 'UNLOCKED';
      wrap.className = 'registered-interactive registered-interactive--toggle' + (isLocked ? ' is-locked' : '');
      wrap.setAttribute('role', 'button');
      wrap.setAttribute('tabindex', '0');
      wrap.setAttribute('aria-pressed', isLocked ? 'true' : 'false');
      wrap.innerHTML =
        '<span class="registered-toggle-icon">' + iconSvg(isLocked ? 'lock' : 'unlock') + '</span>' +
        '<span>' +
          '<span class="registered-interactive-kicker">LOCK STATUS</span>' +
          '<strong class="registered-interactive-value">' + escapeHtml(stateLabel) + '</strong>' +
        '</span>';
      const toggle = function() {
        const nextLocked = !parseLockState(getParamLiveValue(card, 'toggleButton', getComponentOverride(card.dataset.paramKey)));
        commitRegisteredValue(card, nextLocked ? 'LOCKED' : 'UNLOCKED');
      };
      wrap.addEventListener('click', function(event) {
        event.stopPropagation();
        toggle();
      });
      wrap.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          toggle();
        }
      });
      return wrap;
    }

    function buildButtonGroupPreview(card, registered, binding, liveValue) {
      const wrap = document.createElement('div');
      wrap.className = 'registered-interactive registered-interactive--group';
      const options = Array.isArray(registered.options) && registered.options.length
        ? registered.options
        : [{ label: 'A', value: 'A' }, { label: 'B', value: 'B' }, { label: 'C', value: 'C' }];
      const current = String(liveValue || registered.defaultValue || options[0].value);
      wrap.innerHTML =
        '<span class="registered-interactive-kicker">MODE</span>' +
        '<div class="registered-button-group" role="group" aria-label="Mode selector"></div>';
      const group = wrap.querySelector('.registered-button-group');
      options.forEach(function(option) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'registered-group-btn' + (String(option.value) === String(current) ? ' is-active' : '');
        btn.textContent = option.label;
        btn.dataset.actionValue = option.value;
        btn.addEventListener('click', function(event) {
          event.stopPropagation();
          commitRegisteredValue(card, option.value);
        });
        group.appendChild(btn);
      });
      return wrap;
    }

    function buildFireControlPanelPreview(card, registered, binding, liveValue) {
      const wrap = document.createElement('div');
      wrap.className = 'registered-interactive registered-interactive--fire';
      const options = Array.isArray(registered.options) && registered.options.length
        ? registered.options
        : [{ label: 'START', value: 'START' }, { label: 'STOP', value: 'STOP' }];
      const current = String(liveValue || registered.defaultValue || 'STOP');
      wrap.innerHTML =
        '<span class="registered-interactive-kicker">FIRE SYSTEM</span>' +
        '<div class="registered-button-group" role="group" aria-label="Fire control"></div>' +
        '<strong class="registered-interactive-value" style="margin-top:8px;display:block;">' + escapeHtml(current) + '</strong>';
      const group = wrap.querySelector('.registered-button-group');
      options.forEach(function(option) {
        const btn = document.createElement('button');
        btn.type = 'button';
        const modifier = option.value === 'START' ? ' registered-group-btn--start' : (option.value === 'STOP' ? ' registered-group-btn--stop' : '');
        btn.className = 'registered-group-btn' + modifier + (String(option.value) === String(current) ? ' is-active' : '');
        btn.textContent = option.label;
        btn.addEventListener('click', function(event) {
          event.stopPropagation();
          commitRegisteredValue(card, option.value);
        });
        group.appendChild(btn);
      });
      return wrap;
    }

    function buildSliderWithLabelPreview(card, registered, binding, liveValue) {
      const wrap = document.createElement('div');
      wrap.className = 'registered-interactive registered-interactive--slider';
      const meta = parseParamApiMeta(card);
      const min = registered.min != null ? registered.min : (meta.minimum != null && meta.minimum !== '' ? parseFloat(meta.minimum) : 0);
      const max = registered.max != null ? registered.max : (meta.maximum != null && meta.maximum !== '' ? parseFloat(meta.maximum) : 100);
      const step = registered.step != null ? registered.step : (meta.step != null && meta.step !== '' ? parseFloat(meta.step) : 1);
      const safeMin = Number.isFinite(min) ? min : 0;
      const safeMax = Number.isFinite(max) ? max : 100;
      const safeStep = Number.isFinite(step) ? step : 1;
      const parsed = parseFloat(String(liveValue).replace(/[^\d.-]/g, ''));
      const current = Number.isFinite(parsed) ? Math.max(safeMin, Math.min(safeMax, parsed)) : (registered.defaultValue != null ? registered.defaultValue : 50);
      const percent = safeMax === safeMin ? 0 : ((current - safeMin) / (safeMax - safeMin)) * 100;
      wrap.innerHTML =
        '<div class="registered-slider-header">' +
          '<span class="registered-interactive-kicker">BRIGHTNESS</span>' +
          '<strong class="registered-interactive-value registered-slider-value">' + escapeHtml(String(Math.round(current))) + '</strong>' +
        '</div>' +
        '<div class="registered-slider-track">' +
          '<div class="registered-slider-fill" style="width:' + percent + '%"></div>' +
          '<input type="range" class="registered-slider-input param-component" min="' + safeMin + '" max="' + safeMax + '" step="' + safeStep + '" value="' + current + '" aria-label="Brightness">' +
        '</div>';
      const input = wrap.querySelector('.registered-slider-input');
      const fill = wrap.querySelector('.registered-slider-fill');
      const valueLabel = wrap.querySelector('.registered-slider-value');
      const updateVisual = function(nextValue) {
        const numeric = Math.max(safeMin, Math.min(safeMax, parseFloat(nextValue)));
        if (fill) fill.style.width = (safeMax === safeMin ? 0 : ((numeric - safeMin) / (safeMax - safeMin)) * 100) + '%';
        if (valueLabel) valueLabel.textContent = String(Math.round(numeric));
        const valueEl = card.querySelector('.field-value');
        const override = getComponentOverride(card.dataset.paramKey);
        override.value = String(Math.round(numeric));
        const visual = card.querySelector('.registered-visual');
        if (visual) visual.dataset.value = String(Math.round(numeric));
        if (valueEl && !valueEl.querySelector('input, select, textarea')) {
          valueEl.textContent = String(Math.round(numeric));
        }
        generateSchema();
      };
      if (input) {
        input.addEventListener('input', function() {
          updateVisual(input.value);
        });
      }
      return wrap;
    }

    function buildRegisteredComponentPreview(card, registered, binding, liveValue) {
      const wrap = document.createElement('div');
      wrap.className = 'registered-visual param-component';
      if (registered && registered.id) {
        wrap.dataset.registeredType = registered.id;
      }
      const resolvedBinding = binding || controlBindingKey(card.dataset.paramKey);
      const resolvedValue = typeof liveValue === 'string'
        ? liveValue
        : String(getParamLiveValue(card, card.dataset.componentType, getComponentOverride(card.dataset.paramKey)) || '');
      wrap.dataset.value = resolvedValue;

      const specializedBuilders = {
        toggleButton: buildToggleButtonPreview,
        buttonGroup: buildButtonGroupPreview,
        sliderWithLabel: buildSliderWithLabelPreview,
        fireControlPanel: buildFireControlPanelPreview
      };

      if (registered && specializedBuilders[registered.id]) {
        wrap.appendChild(specializedBuilders[registered.id](card, registered, resolvedBinding, resolvedValue));
        return wrap;
      }

      const widgetTree = substituteRegisteredBindings(
        registered && registered.buildWidgetTree ? registered.buildWidgetTree() : null,
        resolvedBinding,
        resolvedValue
      );
      if (widgetTree) {
        wrap.appendChild(renderRegisteredWidgetNode(widgetTree, card, resolvedBinding, resolvedValue));
      } else {
        wrap.innerHTML =
          '<span class="registered-visual-icon">' + iconSvg((registered && registered.icon) || 'gauge') + '</span>' +
          '<span class="registered-visual-text">' +
            '<span class="registered-visual-kicker">' + escapeHtml((registered && registered.label) || 'Custom Component').toUpperCase() + '</span>' +
            '<span class="registered-visual-value">' + escapeHtml(resolvedValue || 'Live value') + '</span>' +
          '</span>';
      }
      return wrap;
    }

    function getParamIcon(card) {
      if (!card) return currentTheme.parameterIcon || 'gauge';
      const iconButton = card.querySelector('.param-icon');
      const domIcon = iconButton && iconButton.dataset.icon ? String(iconButton.dataset.icon).trim() : '';
      return currentTheme.parameterIcons[card.dataset.paramKey] || domIcon || currentTheme.parameterIcon || 'gauge';
    }

    function getParamMetaIcon(card) {
      if (!card) return 'gauge';
      const metaButton = card.querySelector('.param-meta');
      const domMetaIcon = metaButton && metaButton.dataset.metaIcon ? String(metaButton.dataset.metaIcon).trim() : '';
      const override = getComponentOverride(card.dataset.paramKey);
      return override.metaIcon || domMetaIcon || 'gauge';
    }

    function applyComponentTypeSelection(card, type) {
      if (!card) return;
      const override = getComponentOverride(card.dataset.paramKey);
      if (!type || type === 'auto') {
        delete override.componentType;
        applyParameterComponentType(card, detectParamComponentTypeFromApi(card));
      } else {
        override.componentType = type;
        applyParameterComponentType(card, type);
      }
      applyComponentOverrides();
      generateSchema();
      setStudioStatus('Component updated to ' + getComponentChoiceLabel(type || 'auto') + '. Save changes to keep it.');
    }

    function getComponentOverride(key) {
      currentTheme.componentOverrides = currentTheme.componentOverrides && typeof currentTheme.componentOverrides === 'object' ? currentTheme.componentOverrides : {};
      currentTheme.componentOverrides[key] = currentTheme.componentOverrides[key] && typeof currentTheme.componentOverrides[key] === 'object' && !Array.isArray(currentTheme.componentOverrides[key]) ? currentTheme.componentOverrides[key] : {};
      return currentTheme.componentOverrides[key];
    }

    function getEffectiveComponentColors(override) {
      const source = override || {};
      return {
        cardColor: source.cardColor || currentTheme.cardColor,
        borderColor: source.borderColor || currentTheme.borderColor,
        statusColor: source.statusColor || currentTheme.successColor,
        accentColor: source.accentColor || currentTheme.accentColor,
        iconColor: source.iconColor || currentTheme.iconColor || currentTheme.accentColor,
        textColor: source.textColor || currentTheme.textColor
      };
    }

    function buildComponentOverridePayload(override) {
      const source = override || {};
      const colors = getEffectiveComponentColors(source);
      return {
        title: source.title || null,
        value: source.value != null && source.value !== '' ? String(source.value) : null,
        hidden: Boolean(source.hidden),
        componentType: source.componentType || 'auto',
        metaIcon: source.metaIcon || null,
        onLabel: source.onLabel || null,
        offLabel: source.offLabel || null,
        minimum: source.minimum != null && source.minimum !== '' ? source.minimum : null,
        maximum: source.maximum != null && source.maximum !== '' ? source.maximum : null,
        step: source.step != null && source.step !== '' ? source.step : null,
        unit: source.unit || null,
        optionsText: source.optionsText || null,
        colors: colors
      };
    }

    function mergeParamMetaWithOverride(meta, override) {
      const merged = Object.assign({}, meta || {});
      const source = override || {};
      if (source.minimum !== '' && source.minimum != null) merged.minimum = source.minimum;
      if (source.maximum !== '' && source.maximum != null) merged.maximum = source.maximum;
      if (source.step !== '' && source.step != null) merged.step = source.step;
      if (source.unit) merged.unit = source.unit;
      if (source.optionsText) {
        merged.items = String(source.optionsText).split(',').map(function(item) {
          return item.trim();
        }).filter(Boolean);
      }
      return merged;
    }

    function getEffectiveParamMeta(card) {
      if (!card) return {};
      return mergeParamMetaWithOverride(parseParamApiMeta(card), getComponentOverride(card.dataset.paramKey));
    }

    function buildParameterActionFieldsMarkup(resolvedType, override, meta) {
      const merged = mergeParamMetaWithOverride(meta, override);
      const rangeTypes = ['slider', 'powerGauge', 'needleGauge', 'linearGauge', 'sliderWithLabel'];
      if (rangeTypes.indexOf(resolvedType) !== -1) {
        const defaultMax = (resolvedType === 'powerGauge' || resolvedType === 'needleGauge' || resolvedType === 'linearGauge') ? 100 : 255;
        const minValue = override.minimum != null && override.minimum !== '' ? override.minimum : (merged.minimum != null && merged.minimum !== '' ? merged.minimum : 0);
        const maxValue = override.maximum != null && override.maximum !== '' ? override.maximum : (merged.maximum != null && merged.maximum !== '' ? merged.maximum : defaultMax);
        const stepValue = override.step != null && override.step !== '' ? override.step : (merged.step != null && merged.step !== '' ? merged.step : 1);
        const unitValue = override.unit || merged.unit || '';
        const rangeLabel = resolvedType === 'powerGauge' || resolvedType === 'needleGauge' ? 'Gauge range' : 'Slider range';
        return '<div class="studio-panel-group" style="margin-top:12px">' +
          '<p class="studio-panel-label">Component actions</p>' +
          '<div class="studio-grid">' +
            '<label class="studio-field"><span>' + rangeLabel + ' · Min</span><input type="number" data-param-prop="minimum" value="' + escapeHtml(String(minValue)) + '" step="any"></label>' +
            '<label class="studio-field"><span>Max</span><input type="number" data-param-prop="maximum" value="' + escapeHtml(String(maxValue)) + '" step="any"></label>' +
            '<label class="studio-field"><span>Step</span><input type="number" data-param-prop="step" value="' + escapeHtml(String(stepValue)) + '" step="any" min="0"></label>' +
            ((resolvedType === 'powerGauge' || resolvedType === 'needleGauge') ? '<label class="studio-field"><span>Unit label</span><input type="text" data-param-prop="unit" value="' + escapeHtml(unitValue) + '" placeholder="%, °C, kW"></label>' : '') +
          '</div>' +
        '</div>';
      }

      if (resolvedType === 'segmented' || resolvedType === 'dropdown') {
        const optionsValue = override.optionsText || (Array.isArray(merged.items) ? merged.items.join(', ') : '');
        return '<div class="studio-panel-group" style="margin-top:12px">' +
          '<p class="studio-panel-label">Component actions</p>' +
          '<label class="studio-field"><span>Options (comma separated)</span><input type="text" data-param-prop="optionsText" value="' + escapeHtml(optionsValue) + '" placeholder="Low, Medium, High"></label>' +
        '</div>';
      }

      if (resolvedType === 'switch' || resolvedType === 'toggleButton' || resolvedType === 'button') {
        const onLabel = override.onLabel || 'ON';
        const offLabel = override.offLabel || 'OFF';
        return '<div class="studio-panel-group" style="margin-top:12px">' +
          '<p class="studio-panel-label">Component actions</p>' +
          '<div class="studio-grid">' +
            '<label class="studio-field"><span>On label</span><input type="text" data-param-prop="onLabel" value="' + escapeHtml(onLabel) + '"></label>' +
            '<label class="studio-field"><span>Off label</span><input type="text" data-param-prop="offLabel" value="' + escapeHtml(offLabel) + '"></label>' +
          '</div>' +
        '</div>';
      }

      return '';
    }

    function refreshParameterComponentAfterActionChange(card, resolvedType, override) {
      if (!card) return;
      applyParameterComponentType(card, resolvedType);
      applyComponentOverrides();
      const valueEl = card.querySelector('.field-value');
      const valueText = override && override.value !== '' && override.value != null
        ? String(override.value)
        : (valueEl ? valueEl.textContent.trim() : '');
      if (valueText) {
        syncInteractiveValueFromOverride(card, resolvedType, Object.assign({}, override, { value: valueText }));
      }
    }

    function syncInteractiveValueFromOverride(card, resolvedType, override) {
      if (!card || !override || override.value === '' || typeof override.value === 'undefined' || override.value === null) return;
      const valueEl = card.querySelector('.field-value');
      const valueText = String(override.value);

      if (resolvedType === 'switch') {
        const isOn = parseToggleInitialValue(valueText);
        const toggle = card.querySelector('.param-switch');
        if (toggle) {
          toggle.classList.toggle('is-on', isOn);
          toggle.setAttribute('aria-pressed', isOn ? 'true' : 'false');
        }
        if (valueEl && !valueEl.querySelector('input, select, textarea')) {
          valueEl.textContent = isOn ? (override.onLabel || 'ON') : (override.offLabel || 'OFF');
        }
        return;
      }

      if (resolvedType === 'toggleButton') {
        const isOn = parseToggleInitialValue(valueText);
        const button = card.querySelector('.toggle-button-ui, .param-toggle-button');
        if (button) {
          button.classList.toggle('is-on', isOn);
          button.textContent = isOn ? (override.onLabel || 'ON') : (override.offLabel || 'OFF');
        }
        if (valueEl && !valueEl.querySelector('input, select, textarea')) {
          valueEl.textContent = isOn ? (override.onLabel || 'ON') : (override.offLabel || 'OFF');
        }
        return;
      }

      if (resolvedType === 'slider') {
        const meta = getEffectiveParamMeta(card);
        const input = card.querySelector('.param-range-input');
        const parsed = parseFloat(valueText);
        const rangeWrap = card.querySelector('.range-visual');
        const min = meta.minimum === null || typeof meta.minimum === 'undefined' || meta.minimum === '' ? 0 : parseFloat(meta.minimum);
        const max = meta.maximum === null || typeof meta.maximum === 'undefined' || meta.maximum === '' ? 255 : parseFloat(meta.maximum);
        const safeMin = Number.isFinite(min) ? min : 0;
        const safeMax = Number.isFinite(max) ? max : 255;
        if (input && Number.isFinite(parsed)) {
          input.value = String(parsed);
          const currentLabel = rangeWrap ? rangeWrap.querySelector('.range-current') : null;
          if (currentLabel) currentLabel.textContent = String(parsed);
          updateRangeTrackFill(rangeWrap, parsed, safeMin, safeMax);
        }
        if (valueEl && !valueEl.querySelector('input, select, textarea')) {
          valueEl.textContent = Number.isFinite(parsed) ? String(parsed) : valueText;
        }
        return;
      }

      if (resolvedType === 'powerGauge' || resolvedType === 'needleGauge') {
        const meta = getEffectiveParamMeta(card);
        const gauge = card.querySelector('.needle-gauge-visual, .pointer-gauge-visual, .power-gauge-visual');
        if (gauge) {
          const bounds = resolveGaugeNumericBounds(meta, valueText);
          updatePowerGaugeVisual(gauge, bounds, card);
        }
        return;
      }

      if (resolvedType === 'button') {
        const button = card.querySelector('.power-button-ui, .param-button');
        const isOn = parseToggleInitialValue(valueText);
        if (button) {
          button.classList.toggle('is-on', isOn);
          button.classList.toggle('is-off', !isOn);
          button.setAttribute('aria-pressed', isOn ? 'true' : 'false');
        }
        if (valueEl && !valueEl.querySelector('input, select, textarea')) {
          valueEl.textContent = isOn ? 'ON' : 'OFF';
        }
        return;
      }

      if (resolvedType === 'dropdown') {
        const select = card.querySelector('.param-dropdown');
        if (select) select.value = valueText;
        if (valueEl && !valueEl.querySelector('input, select, textarea')) valueEl.textContent = valueText;
        return;
      }

      if (resolvedType === 'segmented') {
        const wrap = card.querySelector('.segmented-visual');
        if (wrap) {
          wrap.dataset.value = valueText;
          wrap.querySelectorAll('.segmented-option').forEach(function(node) {
            node.classList.toggle('is-active', String(node.dataset.value) === String(valueText));
          });
        }
        if (valueEl && !valueEl.querySelector('input, select, textarea')) valueEl.textContent = valueText;
        const metaValue = card.querySelector('.meta-value');
        if (metaValue) metaValue.textContent = valueText;
        return;
      }

      if (resolvedType === 'textField') {
        const input = card.querySelector('.param-text-input');
        if (input) {
          input.value = valueText;
          if (valueEl) valueEl.dataset.liveValue = valueText;
        }
        return;
      }

      if (resolvedType === 'elevatedButton') {
        const button = card.querySelector('.elevated-action-btn');
        if (button) button.textContent = valueText;
        return;
      }

      if (isRegisteredComponentType(resolvedType)) {
        refreshRegisteredComponentPreview(card);
        return;
      }

      if (resolvedType === 'linearGauge') {
        const meta = parseParamApiMeta(card);
        const gauge = card.querySelector('.linear-gauge-visual');
        if (gauge) {
          const rebuilt = buildLinearGaugeVisualElement(card, meta, override);
          gauge.replaceWith(rebuilt);
        }
      }
    }

    function applyComponentOverrides() {
      queryPreviewAll('.param-card').forEach(function(card) {
        const override = getComponentOverride(card.dataset.paramKey);
        const colors = getEffectiveComponentColors(override);
        const label = card.querySelector('.field-label');
        const value = card.querySelector('.field-value');

        if (value && !value.dataset.defaultValue) value.dataset.defaultValue = value.textContent;
        if (label) label.textContent = override.title || card.dataset.paramName || 'Parameter';
        if (value && !value.querySelector('input, select, textarea')) {
          value.textContent = override.value || value.dataset.defaultValue || '';
          value.style.color = colors.textColor;
        }

        if (override.cardColor) {
          card.style.background = colors.cardColor;
        } else {
          card.style.removeProperty('background');
        }
        if (override.borderColor) {
          card.style.borderColor = colors.borderColor;
        } else {
          card.style.removeProperty('border-color');
        }

        const iconButton = card.querySelector('.param-icon');
        if (iconButton) {
          if (override.iconColor) {
            iconButton.style.color = colors.iconColor;
          } else {
            iconButton.style.removeProperty('color');
          }
        }

        const metaButton = card.querySelector('.param-meta');
        if (metaButton) {
          const sequenceValue = metaButton.querySelector('.meta-value') ? metaButton.querySelector('.meta-value').textContent : card.dataset.sequence || '';
          const metaIcon = getParamMetaIcon(card);
          metaButton.innerHTML = iconSvg(metaIcon) + '<span class="meta-value">' + escapeHtml(sequenceValue || '') + '</span>';
          metaButton.dataset.metaIcon = metaIcon;
        }

        if (override.statusColor) {
          const meta = card.querySelector('.param-meta');
          if (meta) meta.style.background = colors.statusColor;
          const dot = card.querySelector('.action-dot');
          if (dot) dot.style.background = colors.statusColor;
        } else {
          const meta = card.querySelector('.param-meta');
          if (meta) meta.style.removeProperty('background');
          const dot = card.querySelector('.action-dot');
          if (dot) dot.style.removeProperty('background');
        }

        card.classList.toggle('is-hidden', Boolean(override.hidden));
        syncParamCardDisplay(card);

        const resolvedType = getResolvedParamComponentType(card);
        if (!card.dataset.componentType || card.dataset.componentType !== resolvedType) {
          applyParameterComponentType(card, resolvedType);
        } else if (isRegisteredComponentType(resolvedType)) {
          refreshRegisteredComponentPreview(card);
        } else if (override.value !== '' && typeof override.value !== 'undefined' && override.value !== null) {
          syncInteractiveValueFromOverride(card, resolvedType, override);
        }

        if (override.accentColor) {
          card.querySelectorAll('.range-track-fill').forEach(function(fill) {
            fill.style.background = colors.accentColor;
          });
          const gaugeWrap = card.querySelector('.needle-gauge-visual, .pointer-gauge-visual, .power-gauge-visual');
          if (gaugeWrap) {
            const prevAccent = currentTheme.accentColor;
            currentTheme.accentColor = colors.accentColor;
            const meta = getEffectiveParamMeta(card);
            const bounds = resolveGaugeNumericBounds(meta, gaugeWrap.dataset.value || '');
            updatePowerGaugeVisual(gaugeWrap, bounds, card);
            currentTheme.accentColor = prevAccent;
          }
        }
      });
    }

    // Slider Navigation
    function showSlide(index) {
      if (!slides.length || !track) return;

      activeIndex = Math.max(0, Math.min(index, slides.length - 1));
      const targetSlide = slides[activeIndex];
      const targetOffset = targetSlide ? targetSlide.offsetLeft : 0;
      track.style.transform = 'translateX(-' + targetOffset + 'px)';

      pageButtons.forEach((button, buttonIndex) => {
        button.classList.toggle('is-active', buttonIndex === activeIndex);
      });

      if (prevButton) prevButton.disabled = activeIndex === 0;
      if (nextButton) nextButton.disabled = activeIndex === slides.length - 1;
      if (progressBar) progressBar.style.width = (((activeIndex + 1) / slides.length) * 100) + '%';

      if (targetSlide) {
        if (title) title.textContent = targetSlide.dataset.pageTitle || 'Model UI';
        if (subtitle) subtitle.textContent = 'Page ' + (targetSlide.dataset.pageNo || (activeIndex + 1)) + '.Operation';
        targetSlide.scrollTop = 0;
      }
    }

    function syncSlideViewportWidth() {
      if (!track) return;
      const slideWindow = track.parentElement;
      const viewportWidth = slideWindow && slideWindow.clientWidth
        ? slideWindow.clientWidth
        : (phoneShell ? phoneShell.clientWidth : 0);
      if (viewportWidth > 0) {
        track.style.setProperty('--slide-viewport-width', viewportWidth + 'px');
      }
      showSlide(activeIndex);
    }

    function attemptHorizontalPageNav(delta) {
      if (!slides.length) return false;
      const now = Date.now();
      if (now - lastHorizontalNavAt < 220) return false;
      const threshold = 26;
      if (Math.abs(delta) < threshold) return false;
      const direction = delta > 0 ? 1 : -1;
      const nextIndex = activeIndex + direction;
      if (nextIndex < 0 || nextIndex >= slides.length) return false;
      lastHorizontalNavAt = now;
      showSlide(nextIndex);
      return true;
    }

    function getPreviewScrollTarget() {
      if (!phoneShell) return null;
      if (phoneShell.classList.contains('is-showing-info')) {
        return phoneShell.querySelector('.device-details-body');
      }
      if (!slides.length) {
        return phoneShell.querySelector('.empty-ui');
      }
      return slides[activeIndex] || slides[0] || null;
    }

    function forwardPreviewWheel(event) {
      if (Math.abs(event.deltaX) > Math.abs(event.deltaY)) {
        if (attemptHorizontalPageNav(event.deltaX)) {
          event.preventDefault();
          return true;
        }
        return false;
      }
      const scrollTarget = getPreviewScrollTarget();
      if (!scrollTarget) return false;
      const maxScroll = scrollTarget.scrollHeight - scrollTarget.clientHeight;
      if (maxScroll <= 0) return false;
      const nextScrollTop = scrollTarget.scrollTop + event.deltaY;
      if ((event.deltaY < 0 && scrollTarget.scrollTop <= 0 && nextScrollTop <= 0) ||
          (event.deltaY > 0 && scrollTarget.scrollTop >= maxScroll && nextScrollTop >= maxScroll)) {
        return false;
      }
      scrollTarget.scrollTop = Math.max(0, Math.min(maxScroll, nextScrollTop));
      event.preventDefault();
      return true;
    }

    function bindSlideSwipe() {
      if (!track || !track.parentElement) return;
      const swipeHost = track.parentElement;
      let startX = 0;
      let startY = 0;
      let tracking = false;

      swipeHost.addEventListener('touchstart', function(event) {
        if (!event.touches || !event.touches.length) return;
        const touch = event.touches[0];
        startX = touch.clientX;
        startY = touch.clientY;
        tracking = true;
      }, { passive: true });

      swipeHost.addEventListener('touchend', function(event) {
        if (!tracking || !event.changedTouches || !event.changedTouches.length) return;
        const touch = event.changedTouches[0];
        const dx = touch.clientX - startX;
        const dy = touch.clientY - startY;
        tracking = false;
        if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 40) {
          attemptHorizontalPageNav(-dx);
        }
      }, { passive: true });
    }

    function getMobileViewportElement() {
      if (!phoneShell) return null;
      if (phoneShell.classList.contains('is-showing-info')) {
        return phoneShell.querySelector('.device-details-view') || phoneShell.querySelector('.device-details-body');
      }
      if (!slides.length) {
        return phoneShell.querySelector('.empty-ui');
      }
      return phoneShell.querySelector('.slide-window');
    }

    function isPointerOverMobileViewport(clientX, clientY) {
      const viewport = getMobileViewportElement();
      if (!viewport) return false;
      const rect = viewport.getBoundingClientRect();
      return clientX >= rect.left && clientX <= rect.right && clientY >= rect.top && clientY <= rect.bottom;
    }

    function getActivePreviewMount() {
      return document.body.classList.contains('studio-open') ? studioPreviewMount : pagePreviewMount;
    }

    function handlePreviewWheel(event) {
      if (event.ctrlKey) return;
      if (!isPointerOverMobileViewport(event.clientX, event.clientY)) return;

      if (phoneShell && phoneShell.classList.contains('is-showing-info')) {
        const infoBody = phoneShell.querySelector('.device-details-body');
        if (infoBody) {
          const maxScroll = infoBody.scrollHeight - infoBody.clientHeight;
          if (maxScroll > 0) {
            infoBody.scrollTop = Math.max(0, Math.min(maxScroll, infoBody.scrollTop + event.deltaY));
            event.preventDefault();
            event.stopPropagation();
          }
          return;
        }
      }

      if (forwardPreviewWheel(event)) {
        event.preventDefault();
        event.stopPropagation();
        return;
      }
      const scrollTarget = getPreviewScrollTarget();
      const mount = getActivePreviewMount();
      if (!scrollTarget || !mount) return;
      const maxScroll = scrollTarget.scrollHeight - scrollTarget.clientHeight;
      if (maxScroll <= 0) return;
      const atTop = scrollTarget.scrollTop <= 0 && event.deltaY < 0;
      const atBottom = scrollTarget.scrollTop >= maxScroll && event.deltaY > 0;
      if (!atTop && !atBottom) {
        event.preventDefault();
        event.stopPropagation();
      }
    }

    function bindPreviewScrollBridge() {
      document.addEventListener('wheel', handlePreviewWheel, { passive: false, capture: true });
    }

    bindPreviewScrollBridge();
    bindSlideSwipe();

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

    window.addEventListener('resize', function() {
      syncSlideViewportWidth();
    });

    // Theme Application
    function applyTheme(theme, options) {
      const settings = options || {};
      const root = phoneShell;
      if (root) {
        root.style.setProperty('--ui-bg', theme.backgroundColor);
        root.style.setProperty('--ui-shell', theme.shellColor);
        root.style.setProperty('--ui-surface', theme.cardColor);
        root.style.setProperty('--ui-surface-2', theme.shellColor);
        root.style.setProperty('--ui-text', theme.textColor);
        root.style.setProperty('--ui-muted', theme.mutedTextColor);
        root.style.setProperty('--ui-graphite', theme.topbarColor);
        root.style.setProperty('--ui-accent', theme.accentColor);
        root.style.setProperty('--ui-green', theme.accentColor);
        root.style.setProperty('--ui-green-dark', theme.accentColor);
        root.style.setProperty('--ui-blue', theme.secondaryColor);
        root.style.setProperty('--ui-steel', theme.secondaryColor);
        root.style.setProperty('--ui-toggle-on', theme.toggleOnColor);
        root.style.setProperty('--ui-toggle-off', theme.toggleOffColor);
        root.style.setProperty('--ui-radius', parseInt(theme.borderRadius || 18, 10) + 'px');
        root.style.setProperty('--ui-font', '"' + theme.fontFamily + '", "Segoe UI", Arial, sans-serif');
        root.style.setProperty('--ui-font-size', theme.fontSize + 'px');
        root.style.setProperty('--ui-font-weight', theme.fontWeight);
        root.style.setProperty('--ui-letter-spacing', theme.letterSpacing + 'px');
        root.style.setProperty('--ui-line-height', theme.lineHeight);
        root.style.setProperty('--ui-text-align', theme.textAlign || 'left');
        root.style.setProperty('--ui-button-bg', theme.buttonBgColor || theme.accentColor);
        root.style.setProperty('--ui-button-text', theme.buttonTextColor || '#FFFFFF');
        root.style.setProperty('--ui-success', theme.successColor || '#10B981');
        root.style.setProperty('--ui-warning', theme.warningColor || '#F59E0B');
        root.style.setProperty('--ui-error', theme.errorColor || '#EF4444');
        root.style.setProperty('--ui-icon-size', parseInt(theme.iconSize || 20, 10) + 'px');
        root.style.setProperty('--ui-icon-color', theme.iconColor || theme.accentColor);
        root.style.setProperty('--ui-header-height', parseInt(theme.headerHeight || 84, 10) + 'px');
        root.style.setProperty('--ui-border-size', parseInt(theme.borderSize || 1, 10) + 'px');
        root.style.setProperty('--ui-border', theme.borderColor || 'rgba(106, 142, 174, .28)');
        root.style.setProperty('--ui-border-color', theme.borderColor || '#DCE8EF');
        root.style.setProperty('--ui-card-blur', parseInt(theme.cardBlur || 0, 10) + 'px');
        root.style.setProperty('--ui-page-padding', parseInt(theme.pagePadding || 18, 10) + 'px');
        root.style.setProperty('--ui-card-padding', parseInt(theme.cardPadding || 16, 10) + 'px');
        root.style.setProperty('--ui-card-spacing', parseInt(theme.cardSpacing || 14, 10) + 'px');
        root.style.setProperty('--ui-card-margin', parseInt(theme.cardMargin || 0, 10) + 'px');
        root.style.setProperty('--ui-button-height', parseInt(theme.buttonSize || 44, 10) + 'px');
        root.style.setProperty('--ui-button-radius', parseInt(theme.buttonRadius || 16, 10) + 'px');
        const shadow = Math.max(0, Math.min(100, parseInt(theme.shadowStrength || 42, 10))) / 1000;
        const cardShadowY = Math.round(4 + shadow * 24);
        const cardShadowBlur = Math.round(10 + shadow * 28);
        const cardShadowAlpha = Math.min(.14, shadow * 2.2).toFixed(3);
        root.style.setProperty('--ui-card-shadow', '0 1px 2px rgba(15, 23, 42, ' + (shadow * 1.4).toFixed(3) + '), 0 ' + cardShadowY + 'px ' + cardShadowBlur + 'px rgba(15, 23, 42, ' + cardShadowAlpha + ')');
        root.style.setProperty('--ui-shadow', '0 8px 24px rgba(15, 23, 42, ' + Math.min(.16, shadow * 2.8).toFixed(3) + ')');

        if (theme.backgroundType === 'gradient') {
          root.style.background = 'linear-gradient(165deg, ' + theme.backgroundColor + ' 0%, ' + (theme.gradientColor || theme.shellColor) + ' 55%, ' + theme.backgroundColor + ' 100%)';
        } else {
          root.style.background = theme.backgroundColor || theme.shellColor;
        }
        root.style.setProperty('--ui-surface-2', theme.shellColor || theme.backgroundColor);
      }

      queryPreviewAll('.save-btn').forEach(function(button) {
        const target = theme.buttonTarget || 'save';
        const isSave = button.id === 'save-ui-btn';
        const isCustomize = button.id === 'open-studio-footer-btn';
        const shouldStyle = target === 'all' || (target === 'save' && isSave) || (target === 'customize' && isCustomize);
        button.style.background = shouldStyle ? (theme.buttonBgColor || theme.accentColor) : '';
        button.style.color = shouldStyle ? (theme.buttonTextColor || '#fff') : '';
        button.style.minHeight = (parseInt(theme.buttonSize || 44, 10)) + 'px';
        button.style.borderRadius = (parseInt(theme.buttonRadius || 16, 10)) + 'px';
        button.classList.remove('is-hover-lift', 'is-hover-glow');
        const hover = theme.buttonHover || 'lift';
        if (hover && hover !== 'none') {
          button.classList.add('is-hover-' + hover);
        }
      });

      const saveUiButton = phoneShell ? phoneShell.querySelector('#save-ui-btn') : null;
      if (saveUiButton) {
        const label = escapeHtml(theme.buttonText || 'Save');
        const saveIcon = iconSvg('save');
        if (theme.buttonIconPosition === 'none') {
          saveUiButton.innerHTML = label;
        } else if (theme.buttonIconPosition === 'right') {
          saveUiButton.innerHTML = label + ' ' + saveIcon;
        } else {
          saveUiButton.innerHTML = saveIcon + ' ' + label;
        }
      }

      queryPreviewAll('.app-topbar').forEach(function(topbar) {
        topbar.classList.remove('is-align-center', 'is-align-right');
        const alignment = theme.headerAlignment || 'left';
        if (alignment === 'center') {
          topbar.classList.add('is-align-center');
        } else if (alignment === 'right') {
          topbar.classList.add('is-align-right');
        }
      });

      queryPreviewAll('.top-actions').forEach(function(actions) {
        actions.style.display = String(theme.showHeaderIcons) === '0' ? 'none' : 'flex';
      });

      queryPreviewAll('.brand-strip').forEach(function(brand) {
        brand.style.display = String(theme.brandVisible) === '0' ? 'none' : 'grid';
      });

      queryPreviewAll('.slide-hero').forEach(function(hero) {
        hero.style.display = String(theme.heroVisible) === '0' ? 'none' : 'grid';
      });

      queryPreviewAll('.name-card').forEach(function(card) {
        card.style.display = String(theme.nameCardVisible) === '0' ? 'none' : 'grid';
      });

      queryPreviewAll('.progress-shell, .slider-controls').forEach(function(nav) {
        nav.style.display = String(theme.pageNavigatorVisible) === '0' ? 'none' : '';
      });

      queryPreviewAll('.footer-actions').forEach(function(footer) {
        footer.style.display = String(theme.footerVisible) === '0' ? 'none' : '';
      });

      queryPreviewAll('.param-card').forEach(function(card) {
        const margin = parseInt(theme.cardMargin || 0, 10);
        card.style.marginTop = margin > 0 ? margin + 'px' : '';
        card.style.marginBottom = margin > 0 ? margin + 'px' : '';
      });

      queryPreviewAll('.online-state').forEach(function(state) {
        state.style.display = String(theme.showStatus) === '0' ? 'none' : 'inline-flex';
      });

      if (theme.headerText) {
        queryPreviewAll('.slide-hero h2').forEach(function(heroTitle) {
          heroTitle.textContent = theme.headerText;
        });
      } else {
        queryPreviewAll('.page-slide').forEach(function(slide) {
          const heroTitle = slide.querySelector('.slide-hero h2');
          if (heroTitle) heroTitle.textContent = slide.dataset.pageTitle || heroTitle.textContent;
        });
      }

      queryPreviewAll('.param-card').forEach(function(card) {
        const iconButton = card.querySelector('.param-icon');
        if (!iconButton) return;
        const iconName = getParamIcon(card);
        iconButton.innerHTML = iconSvg(iconName);
        iconButton.dataset.icon = iconName;
      });

      queryPreviewAll('.slide-hero-kicker').forEach(function(kicker) {
        kicker.innerHTML = iconSvg(theme.iconStyle) + ' Live Control Page';
      });

      applyControlOrder();
      if (settings.reinitializeComponents) {
        initializeParamComponents();
      }
      applyComponentOverrides();
      if (document.body.classList.contains('studio-open') || document.getElementById('page-preview-shell')) {
        requestAnimationFrame(applyActivePreviewZoom);
      }
    }

    function syncThemeStateFromPreview() {
      normalizedControlOrder();
      currentTheme.componentOverrides = currentTheme.componentOverrides && typeof currentTheme.componentOverrides === 'object' && !Array.isArray(currentTheme.componentOverrides)
        ? currentTheme.componentOverrides
        : {};
      currentTheme.parameterIcons = normalizeParameterIcons(currentTheme.parameterIcons);

      queryPreviewAll('.param-card').forEach(function(card) {
        const key = card.dataset.paramKey;
        if (!key) return;
        const override = getComponentOverride(key);
        const componentType = getResolvedParamComponentType(card);
        const liveValue = getParamLiveValue(card, componentType, override);
        if (liveValue !== '') {
          override.value = liveValue;
        }
        const icon = getParamIcon(card);
        if (icon) {
          currentTheme.parameterIcons[key] = icon;
        }
        const metaIcon = getParamMetaIcon(card);
        if (metaIcon) {
          override.metaIcon = metaIcon;
        }
        if (!override.componentType || override.componentType === 'auto') {
          const detectedType = detectParamComponentType(card);
          if (detectedType) {
            override.componentType = detectedType;
          }
        }
      });
    }

    function buildThemePayloadForSave() {
      syncThemeStateFromPreview();
      const themeForSave = JSON.parse(JSON.stringify(currentTheme));
      themeForSave.parameterIcons = normalizeParameterIcons(themeForSave.parameterIcons);
      themeForSave.componentOverrides = themeForSave.componentOverrides && typeof themeForSave.componentOverrides === 'object' && !Array.isArray(themeForSave.componentOverrides)
        ? themeForSave.componentOverrides
        : {};
      themeForSave.controlOrder = Array.isArray(themeForSave.controlOrder) ? themeForSave.controlOrder : [];
      return themeForSave;
    }

    function parseSaveResponse(response) {
      return response.text().then(function(raw) {
        let result = {};
        try {
          result = raw ? JSON.parse(raw) : {};
        } catch (error) {
          throw new Error('Server returned an invalid response while saving.');
        }
        if (!response.ok) {
          throw new Error(result.message || ('Save failed with status ' + response.status + '.'));
        }
        return result;
      });
    }

    function persistGeneratedSchema(options) {
      options = options || {};
      const schema = generateSchema();
      if (!schema || schema.error) {
        const message = 'Cannot save — schema generation failed.';
        if (!options.silent) {
          setStudioStatus(message);
        }
        return Promise.reject(new Error(message));
      }

      if (!modelNo) {
        const message = 'Model number is missing — schema was not saved to the API.';
        if (!options.silent) {
          setStudioStatus(message);
        }
        return Promise.reject(new Error(message));
      }

      const formData = new FormData();
      formData.append('modelno', modelNo);
      formData.append('theme_json', JSON.stringify(buildThemePayloadForSave()));
      formData.append('schema_json', JSON.stringify(schema));

      if (!options.silent) {
        setStudioStatus('Saving schema to API...');
      }

      return fetch(saveThemeUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      })
      .then(parseSaveResponse)
      .then(function(result) {
        const saved = result.success === true || result.success === 1 || result.success === '1';
        if (saved) {
          const message = options.statusMessage || result.message || 'Device UI schema synced to API.';
          if (!options.silent) {
            setStudioStatus(message);
          }
          if (options.showToast !== false) {
            showSaveToast(message);
          }
        } else if (!options.silent) {
          setStudioStatus(result.message || 'Schema sync failed.');
        }
        if (!saved) {
          return Promise.reject(new Error(result.message || 'Schema sync failed.'));
        }
        return result;
      });
    }

    function syncInputsFromTheme() {
      themeInputs.forEach(function(input) {
        const key = input.dataset.themeKey;
        if (currentTheme[key] !== undefined) {
          if (input.type === 'checkbox') {
            input.checked = String(currentTheme[key]) !== '0';
          } else if (input.type === 'range') {
            const synced = setRangeInputValue(input, currentTheme[key]);
            currentTheme[key] = synced;
          } else {
            input.value = currentTheme[key];
          }
          updateRangeBadge(input);
        }
      });

      document.querySelectorAll('[data-segment-key]').forEach(function(group) {
        const key = group.dataset.segmentKey;
        group.querySelectorAll('[data-segment-value]').forEach(function(button) {
          button.classList.toggle('is-active', String(currentTheme[key]) === String(button.dataset.segmentValue));
        });
      });

      document.querySelectorAll('[data-preset]').forEach(function(button) {
        button.classList.toggle('is-active', String(currentTheme.themeMode) === String(button.dataset.preset));
      });
    }

    // Icon Library
    function renderIconLibrary(filterText) {
      if (!iconLibraryGrid) return;
      const filtered = getFilteredIconNames(filterText);

      iconLibraryGrid.innerHTML = filtered.map(iconName => {
        return '<button type="button" class="icon-choice" data-icon-name="' + escapeHtml(iconName) + '" title="' + escapeHtml(iconName) + '" aria-label="' + escapeHtml(iconName) + '">' + iconSvg(iconName) + '</button>';
      }).join('');

      document.querySelectorAll('#icon-library-grid .icon-choice').forEach(btn => {
        btn.addEventListener('click', function() {
          const iconName = this.dataset.iconName;
          currentTheme.parameterIcon = iconName;
          applyTheme(currentTheme);
          generateSchema();
          setStudioStatus('Default parameter icon updated to ' + iconName + '.');
        });
      });
    }

    if (iconSearchInput) {
      iconSearchInput.addEventListener('input', function() {
        renderIconLibrary(this.value);
      });
    }

    function getEditableParamCards() {
      return Array.from(document.querySelectorAll('.page-slide .param-card')).map(function(card) {
        const slide = card.closest('.page-slide');
        return {
          card: card,
          key: card.dataset.paramKey,
          name: card.dataset.paramName || 'Parameter',
          pageNo: slide ? slide.dataset.pageNo : '',
          pageName: slide ? slide.dataset.pageTitle : ''
        };
      });
    }

    // Parameters Buttons Management
    function renderParametersButtons() {
      if (!paramsButtonsContainer) return;
      const params = getEditableParamCards();

      if (!params.length) {
        paramsButtonsContainer.innerHTML = '<div class="param-editor-card"><h4>No parameters found</h4></div>';
        return;
      }

      const selectedExists = params.some(function(item) { return item.key === selectedParamKey; });
      if (!selectedExists) selectedParamKey = params[0].key;

      const selected = params.find(function(item) { return item.key === selectedParamKey; }) || params[0];
      const card = selected.card;
      const override = getComponentOverride(selected.key);
      const paramMeta = parseParamApiMeta(card);
      const currentIcon = getParamIcon(card);
      const resolvedComponentType = getResolvedParamComponentType(card);
      const selectedComponentType = override.componentType || 'auto';
      const componentColors = getEffectiveComponentColors(override);
      const metaIconButton = (card.querySelector('.param-meta') || ['slider', 'value', 'dropdown'].indexOf(resolvedComponentType) !== -1)
        ? '<button type="button" class="studio-btn param-meta-icon-edit" title="Meta icon">' + iconSvg(getParamMetaIcon(card)) + ' Meta Icon</button>'
        : '';

      paramsButtonsContainer.innerHTML =
        '<label class="studio-field">' +
          '<span>Select parameter</span>' +
          '<select id="studio-param-select">' +
            params.map(function(item) {
              const label = 'Page ' + item.pageNo + ' - ' + item.name;
              return '<option value="' + escapeHtml(item.key) + '"' + (item.key === selected.key ? ' selected' : '') + '>' + escapeHtml(label) + '</option>';
            }).join('') +
          '</select>' +
        '</label>' +
        '<div class="param-editor-card">' +
          '<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px;">' +
            '<div><h4>' + escapeHtml(selected.name) + '</h4><small style="color:var(--ui-muted);font-weight:800;">Page ' + escapeHtml(selected.pageNo) + ' - ' + escapeHtml(selected.pageName) + '</small></div>' +
            '<div class="param-editor-actions">' +
              '<button type="button" class="studio-btn param-icon-edit" title="Main icon">' + iconSvg(currentIcon) + ' Main Icon</button>' +
              metaIconButton +
            '</div>' +
          '</div>' +
          '<label class="studio-field"><span>Component type</span>' +
            '<button type="button" class="studio-btn param-component-edit" id="param-component-edit-btn" title="Choose component">' +
              '<span class="component-choice-preview">' + componentPreviewMarkup(selectedComponentType) + '</span>' +
              '<span><strong>' + escapeHtml(getComponentChoiceLabel(selectedComponentType)) + '</strong><br><small style="color:var(--ui-muted);font-weight:700;">' + escapeHtml(flutterWidgetForComponentType(resolvedComponentType)) + '</small></span>' +
            '</button>' +
          '</label>' +
          buildParameterActionFieldsMarkup(resolvedComponentType, override, paramMeta) +
          '<div class="studio-panel-group" style="margin-top:12px">' +
            '<p class="studio-panel-label">Component colors</p>' +
            '<div class="studio-grid">' +
              '<label class="studio-field"><span>Card background</span><input type="color" data-param-prop="cardColor" value="' + escapeHtml(componentColors.cardColor || '#FFFFFF') + '"></label>' +
              '<label class="studio-field"><span>Border color</span><input type="color" data-param-prop="borderColor" value="' + escapeHtml(componentColors.borderColor || '#DCE8EF') + '"></label>' +
              '<label class="studio-field"><span>Accent / control</span><input type="color" data-param-prop="accentColor" value="' + escapeHtml(componentColors.accentColor || '#2563EB') + '"></label>' +
              '<label class="studio-field"><span>Icon color</span><input type="color" data-param-prop="iconColor" value="' + escapeHtml(componentColors.iconColor || '#2563EB') + '"></label>' +
              '<label class="studio-field"><span>Text color</span><input type="color" data-param-prop="textColor" value="' + escapeHtml(componentColors.textColor || '#0F172A') + '"></label>' +
              '<label class="studio-field"><span>Status / chip</span><input type="color" data-param-prop="statusColor" value="' + escapeHtml(componentColors.statusColor || '#10B981') + '"></label>' +
            '</div>' +
          '</div>' +
          '<div class="studio-grid">' +
            '<label class="studio-field"><span>Title</span><input type="text" data-param-prop="title" value="' + escapeHtml(override.title || selected.name || '') + '"></label>' +
            '<label class="studio-field"><span>Value</span><input type="text" data-param-prop="value" value="' + escapeHtml(override.value || '') + '" placeholder="Use live value"></label>' +
          '</div>' +
          '<label class="studio-toggle-row">Hide this parameter <input type="checkbox" data-param-prop="hidden" ' + (override.hidden ? 'checked' : '') + '></label>' +
          '<p class="studio-section-note">Resolved component: <strong>' + escapeHtml(resolvedComponentType) + '</strong> · Flutter: <strong>' + escapeHtml(flutterWidgetForComponentType(resolvedComponentType)) + '</strong> · Icon: <strong>' + escapeHtml(currentIcon) + '</strong></p>' +
        '</div>';

      const select = document.getElementById('studio-param-select');
      if (select) {
        select.addEventListener('change', function() {
          selectedParamKey = select.value;
          const selectedCard = getEditableParamCards().find(function(item) {
            return item.key === selectedParamKey;
          });
          if (selectedCard && selectedCard.card) {
            selectedCard.card.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
          renderParametersButtons();
        });
      }

      const mainIconEdit = paramsButtonsContainer.querySelector('.param-icon-edit');
      if (mainIconEdit) {
        mainIconEdit.addEventListener('click', function(event) {
          activeIconCard = card;
          activeIconTargetType = 'param';
          renderParamIconPicker(card, 'param');
          openIconPicker(event.currentTarget);
        });
      }

      const metaIconEdit = paramsButtonsContainer.querySelector('.param-meta-icon-edit');
      if (metaIconEdit) {
        metaIconEdit.addEventListener('click', function(event) {
          activeIconCard = card;
          activeIconTargetType = 'meta';
          renderParamIconPicker(card, 'meta');
          openIconPicker(event.currentTarget);
        });
      }

      const componentEdit = paramsButtonsContainer.querySelector('.param-component-edit');
      if (componentEdit) {
        componentEdit.addEventListener('click', function(event) {
          activeComponentCard = card;
          renderComponentPicker(card);
          openComponentPicker(event.currentTarget);
        });
      }

      paramsButtonsContainer.querySelectorAll('[data-param-prop]').forEach(function(input) {
        const updateParameterOverride = function() {
          const nextOverride = getComponentOverride(selected.key);
          nextOverride[input.dataset.paramProp] = input.type === 'checkbox' ? input.checked : input.value;
          const actionProps = ['minimum', 'maximum', 'step', 'unit', 'optionsText', 'onLabel', 'offLabel'];
          if (actionProps.indexOf(input.dataset.paramProp) !== -1) {
            refreshParameterComponentAfterActionChange(card, resolvedComponentType, nextOverride);
          } else {
            applyComponentOverrides();
          }
          generateSchema();
          setStudioStatus('Parameter updated. Save changes to keep it.');
        };
        input.addEventListener('input', updateParameterOverride);
        input.addEventListener('change', updateParameterOverride);
      });
    }

    function renderComponentPicker(card) {
      if (!card || !componentPickerGrid) return;
      const override = getComponentOverride(card.dataset.paramKey);
      const currentType = override.componentType || 'auto';
      const paramName = card.dataset.paramName || 'parameter';
      if (componentPickerTitle) {
        componentPickerTitle.textContent = 'Component for ' + paramName;
      }
      const catalogChoices = getParameterComponentCatalog();
      const registeredChoices = REGISTERED_COMPONENTS.map(function(item) {
        return { id: item.id, label: item.label, description: item.description, flutterWidget: item.name };
      });
      const componentChoices = catalogChoices.concat(registeredChoices);
      componentPickerGrid.innerHTML = componentChoices.map(function(item) {
        return '<button type="button" class="component-choice' + (item.id === currentType ? ' is-active' : '') + '" data-component-type="' + escapeHtml(item.id) + '" aria-label="' + escapeHtml(item.label) + '">' +
          '<span class="component-choice-preview">' + componentPreviewMarkup(item.id) + '</span>' +
          '<span class="component-choice-label">' + escapeHtml(item.label) + '</span>' +
        '</button>';
      }).join('');

      componentPickerGrid.querySelectorAll('.component-choice').forEach(function(button) {
        button.addEventListener('click', function() {
          if (!activeComponentCard) return;
          applyComponentTypeSelection(activeComponentCard, button.dataset.componentType);
          closeComponentPicker();
          renderParametersButtons();
        });
      });
    }

    function openComponentPicker(anchor) {
      if (!componentPicker) return;
      closeIconPicker();
      const rect = anchor ? anchor.getBoundingClientRect() : null;
      const pickerHeight = Math.min(560, window.innerHeight - 28);
      if (rect) {
        const left = Math.max(12, Math.min(window.innerWidth - 372, rect.left));
        let top = rect.bottom + 8;
        if (top + pickerHeight > window.innerHeight - 12) {
          top = Math.max(12, rect.top - pickerHeight - 8);
        }
        componentPicker.style.left = left + 'px';
        componentPicker.style.top = top + 'px';
        componentPicker.style.maxHeight = pickerHeight + 'px';
      } else {
        componentPicker.style.left = 'auto';
        componentPicker.style.right = '24px';
        componentPicker.style.top = '86px';
        componentPicker.style.maxHeight = pickerHeight + 'px';
      }
      componentPicker.classList.add('is-open');
    }

    function closeComponentPicker() {
      if (componentPicker) componentPicker.classList.remove('is-open');
      activeComponentCard = null;
    }

    // Icon Picker for Parameters
    function renderParamIconPicker(card, targetType, filterText) {
      if (!card || !iconPickerGrid) return;
      const type = targetType || activeIconTargetType || 'param';
      const currentIcon = type === 'meta' ? getParamMetaIcon(card) : getParamIcon(card);
      const paramName = card.dataset.paramName || 'parameter';
      const icons = getFilteredIconNames(filterText);

      iconPickerTitle.textContent = (type === 'meta' ? 'Meta icon for ' : 'Icon for ') + paramName;
      iconPickerGrid.innerHTML = icons.length ? icons.map(iconName => {
        return '<button type="button" class="icon-choice' + (iconName === currentIcon ? ' is-active' : '') + '" data-icon-name="' + escapeHtml(iconName) + '" aria-label="' + escapeHtml(iconName) + '">' + iconSvg(iconName) + '</button>';
      }).join('') : '<div style="grid-column:1/-1;color:var(--ui-muted);font-weight:800;font-size:12px;padding:12px;">No icons found</div>';

      document.querySelectorAll('#icon-picker-grid .icon-choice').forEach(btn => {
        btn.addEventListener('click', function() {
          const iconName = this.dataset.iconName;
          if (activeIconCard) {
            if (activeIconTargetType === 'meta') {
              const override = getComponentOverride(activeIconCard.dataset.paramKey);
              override.metaIcon = iconName;
              const metaButton = activeIconCard.querySelector('.param-meta');
              if (metaButton) {
                const sequenceValue = metaButton.querySelector('.meta-value') ? metaButton.querySelector('.meta-value').textContent : '';
                metaButton.innerHTML = iconSvg(iconName) + '<span class="meta-value">' + escapeHtml(sequenceValue || '') + '</span>';
                metaButton.dataset.metaIcon = iconName;
              }
            } else {
              currentTheme.parameterIcons[activeIconCard.dataset.paramKey] = iconName;
              const iconButton = activeIconCard.querySelector('.param-icon');
              if (iconButton) {
                iconButton.innerHTML = iconSvg(iconName);
                iconButton.dataset.icon = iconName;
              }
            }
            generateSchema();
            setStudioStatus((activeIconTargetType === 'meta' ? 'Parameter meta icon' : 'Parameter icon') + ' updated. Save Studio to keep it.');
            closeIconPicker();
            renderParametersButtons();
          }
        });
      });
    }

    function renderHeaderIconPicker(filterText) {
      if (!iconPickerGrid) return;
      const currentIcon = currentTheme.iconStyle || 'signal';
      const icons = getFilteredIconNames(filterText);
      iconPickerTitle.textContent = 'Header icon';
      iconPickerGrid.innerHTML = icons.length ? icons.map(iconName => {
        return '<button type="button" class="icon-choice' + (iconName === currentIcon ? ' is-active' : '') + '" data-icon-name="' + escapeHtml(iconName) + '" aria-label="' + escapeHtml(iconName) + '">' + iconSvg(iconName) + '</button>';
      }).join('') : '<div style="grid-column:1/-1;color:var(--ui-muted);font-weight:800;font-size:12px;padding:12px;">No icons found</div>';

      document.querySelectorAll('#icon-picker-grid .icon-choice').forEach(btn => {
        btn.addEventListener('click', function() {
          currentTheme.iconStyle = this.dataset.iconName;
          const headerButton = document.getElementById('header-icon-picker-btn');
          if (headerButton) headerButton.innerHTML = iconSvg(currentTheme.iconStyle) + ' Choose header icon';
          applyTheme(currentTheme);
          generateSchema();
          closeIconPicker();
          setStudioStatus('Header icon updated. Save changes to keep it.');
        });
      });
    }

    function openIconPicker(anchor) {
      if (!iconPicker) return;
      closeComponentPicker();
      if (paramIconSearchInput) {
        paramIconSearchInput.value = '';
      }
      const rect = anchor ? anchor.getBoundingClientRect() : null;
      if (rect) {
        iconPicker.style.left = Math.max(12, Math.min(window.innerWidth - 340, rect.left)) + 'px';
        iconPicker.style.top = Math.min(window.innerHeight - 280, rect.bottom + 8) + 'px';
      } else {
        iconPicker.style.left = 'auto';
        iconPicker.style.right = '24px';
        iconPicker.style.top = '86px';
      }
      iconPicker.classList.add('is-open');
      if (paramIconSearchInput) {
        window.setTimeout(function() { paramIconSearchInput.focus(); }, 30);
      }
    }

    function closeIconPicker() {
      if (iconPicker) iconPicker.classList.remove('is-open');
      activeIconCard = null;
      activeIconTargetType = 'param';
    }

    if (paramIconSearchInput) {
      paramIconSearchInput.addEventListener('input', function() {
        if (activeIconTargetType === 'header') {
          renderHeaderIconPicker(paramIconSearchInput.value);
        } else if (activeIconCard) {
          renderParamIconPicker(activeIconCard, activeIconTargetType, paramIconSearchInput.value);
        }
      });
    }

    window.addEventListener('scroll', function(event) {
      if (event.target && iconPicker && iconPicker.contains(event.target)) return;
      if (event.target && componentPicker && componentPicker.contains(event.target)) return;
      closeIconPicker();
      closeComponentPicker();
    }, true);

    // Control Order
    function getControlsGroupedByPage() {
      const groups = [];
      document.querySelectorAll('.page-slide').forEach(function(slide) {
        const pageNo = String(slide.dataset.pageNo || '');
        const pageName = String(slide.dataset.pageTitle || ('Page ' + pageNo));
        const controls = Array.from(slide.querySelectorAll('.param-card')).map(function(card) {
          return {
            key: card.dataset.paramKey,
            name: card.dataset.paramName || card.dataset.paramKey
          };
        });
        groups.push({ pageNo: pageNo, pageName: pageName, controls: controls });
      });
      return groups;
    }

    function getAllControlKeys() {
      const keys = [];
      getControlsGroupedByPage().forEach(function(group) {
        group.controls.forEach(function(item) {
          keys.push(item);
        });
      });
      return keys;
    }

    function normalizedControlOrder() {
      const all = getAllControlKeys();
      const known = new Set(all.map(function(item) { return item.key; }));
      const saved = Array.isArray(currentTheme.controlOrder) ? currentTheme.controlOrder.filter(function(key) {
        return known.has(key);
      }) : [];
      all.forEach(function(item) {
        if (saved.indexOf(item.key) === -1) saved.push(item.key);
      });
      currentTheme.controlOrder = saved;
      return saved;
    }

    function renderOrderList() {
      if (!orderList) return;
      const groups = getControlsGroupedByPage();
      const controlsByKey = {};
      getAllControlKeys().forEach(function(item) {
        controlsByKey[item.key] = item.name;
      });

      orderList.innerHTML = '';
      if (!groups.length) {
        orderList.innerHTML = '<p class="order-page-hint">No linked parameters yet. Add parameters to a page to reorder them here.</p>';
        return;
      }

      const savedOrder = normalizedControlOrder();
      groups.forEach(function(group) {
        const groupKeys = group.controls.map(function(item) { return item.key; });
        const orderedKeys = savedOrder.filter(function(key) {
          return groupKeys.indexOf(key) !== -1;
        });
        groupKeys.forEach(function(key) {
          if (orderedKeys.indexOf(key) === -1) orderedKeys.push(key);
        });

        const section = document.createElement('section');
        section.className = 'order-page-group';
        section.dataset.pageNo = group.pageNo;
        section.innerHTML =
          '<div class="order-page-head">' +
            '<span class="order-page-badge">Page ' + escapeHtml(group.pageNo || '?') + '</span>' +
            '<strong class="order-page-title" title="' + escapeHtml(group.pageName) + '">' + escapeHtml(group.pageName) + '</strong>' +
            '<span class="order-page-count">' + orderedKeys.length + ' control' + (orderedKeys.length === 1 ? '' : 's') + '</span>' +
          '</div>' +
          '<div class="order-page-list"></div>';

        const list = section.querySelector('.order-page-list');
        orderedKeys.forEach(function(key, index) {
          const row = document.createElement('div');
          row.className = 'order-row';
          row.draggable = true;
          row.dataset.orderKey = key;
          row.dataset.pageNo = group.pageNo;
          row.innerHTML =
            '<span class="order-row-index">' + (index + 1) + '</span>' +
            '<span class="order-row-name" title="' + escapeHtml(key) + '">' + escapeHtml(controlsByKey[key] || key) + '</span>' +
            '<span class="order-grip">' + iconSvg('move') + '</span>';
          list.appendChild(row);
        });
        orderList.appendChild(section);
      });
    }

    function rebuildControlOrderFromDom() {
      const order = [];
      if (!orderList) return order;
      orderList.querySelectorAll('.order-page-group').forEach(function(group) {
        group.querySelectorAll('.order-row').forEach(function(row) {
          order.push(row.dataset.orderKey);
        });
      });
      currentTheme.controlOrder = order;
      return order;
    }

    function applyControlOrder() {
      normalizedControlOrder();
      document.querySelectorAll('.param-list').forEach(function(list) {
        const cards = Array.from(list.querySelectorAll('.param-card'));
        cards.sort(function(a, b) {
          return currentTheme.controlOrder.indexOf(a.dataset.paramKey) - currentTheme.controlOrder.indexOf(b.dataset.paramKey);
        });
        cards.forEach(function(card) {
          list.appendChild(card);
        });
      });
      renderOrderList();
    }

    // Convert the exact browser preview into a renderer-neutral document.  The
    // neutral core is the source of truth; preview-specific HTML/CSS is retained
    // under extensions so older consumers can still reproduce this screen.
    function buildUniversalSchema(previewSchema, pages, controls) {
      const numberValue = function(value, fallback) {
        const parsed = parseFloat(value);
        return Number.isFinite(parsed) ? parsed : fallback;
      };
      const boolValue = function(value) {
        return String(value) !== '0';
      };
      const safeId = function(value) {
        return String(value || 'item').trim().replace(/[^a-zA-Z0-9_-]+/g, '-').replace(/^-+|-+$/g, '') || 'item';
      };
      const token = function(path) {
        return '{' + path + '}';
      };
      const shadowAlpha = Math.max(0, Math.min(100, numberValue(currentTheme.shadowStrength, 70))) / 500;
      const linearGradient = function(angle, stops, fallbackColor) {
        return { type: 'linearGradient', angle: angle, stops: stops, fallbackColor: fallbackColor };
      };
      const componentStyles = {
        viewport: {
          minWidth: 0,
          minHeight: '100vh',
          backgroundColor: currentTheme.backgroundColor,
          background: {
            type: 'layered',
            fallbackColor: currentTheme.backgroundColor,
            layers: [
              { type: 'radialGradient', center: { x: 0.12, y: 0 }, radius: 0.32, stops: [{ offset: 0, color: 'rgba(155,209,229,0.72)' }, { offset: 1, color: 'rgba(155,209,229,0)' }] },
              { type: 'radialGradient', center: { x: 0.88, y: 0.12 }, radius: 0.30, stops: [{ offset: 0, color: 'rgba(37,99,235,0.22)' }, { offset: 1, color: 'rgba(37,99,235,0)' }] },
              linearGradient(135, [{ offset: 0, color: currentTheme.backgroundColor }, { offset: 0.48, color: currentTheme.gradientColor || currentTheme.shellColor }, { offset: 1, color: '#EAF7F0' }], currentTheme.backgroundColor)
            ]
          }
        },
        screen: {
          width: '100%',
          maxWidth: 430,
          height: '100dvh',
          maxHeight: '100dvh',
          marginHorizontal: 'auto',
          overflow: 'clip',
          display: 'flex',
          flexDirection: 'column',
          backgroundColor: currentTheme.backgroundColor,
          background: currentTheme.backgroundType === 'gradient'
            ? linearGradient(135, [{ offset: 0, color: currentTheme.backgroundColor }, { offset: 1, color: currentTheme.gradientColor || currentTheme.shellColor }], currentTheme.backgroundColor)
            : { type: 'solid', color: currentTheme.backgroundColor },
          foregroundColor: currentTheme.textColor,
          shadow: { x: 0, y: 30, blur: 80, color: 'rgba(34,40,60,0.18)' }
        },
        appBar: {
          layout: { type: 'grid', columns: ['auto', '1fr', 'auto'], alignItems: 'center', gap: 10 },
          minHeight: numberValue(currentTheme.headerHeight, 84),
          padding: { top: 14, right: 16, bottom: 12, left: 16 },
          backgroundColor: currentTheme.topbarColor,
          background: linearGradient(145, [{ offset: 0, color: 'rgba(18,50,70,0.98)' }, { offset: 1, color: 'rgba(106,142,174,0.94)' }], currentTheme.topbarColor),
          foregroundColor: '#FFFFFF',
          shadow: { x: 0, y: 18, blur: 38, color: 'rgba(18,50,70,' + Math.min(0.35, shadowAlpha + 0.06).toFixed(2) + ')' },
          parts: {
            title: { color: '#FFFFFF', fontSize: 18, fontWeight: 800, lineHeight: 1.2, maxLines: 1, overflow: 'ellipsis' },
            subtitle: { color: 'rgba(255,255,255,0.68)', fontSize: 12, fontWeight: 600, lineHeight: 1.25, maxLines: 1, overflow: 'ellipsis' },
            actionGroup: { layout: 'horizontal', alignItems: 'center', gap: 4 }
          }
        },
        headerIconButton: {
          width: 42,
          height: 42,
          shape: 'circle',
          borderWidth: 0,
          backgroundColor: 'rgba(255,255,255,0.10)',
          foregroundColor: '#FFFFFF',
          innerBorder: { width: 1, color: 'rgba(255,255,255,0.12)' }
        },
        backButton: {
          minWidth: 42,
          height: 42,
          paddingHorizontal: 12,
          gap: 6,
          shape: 'pill',
          backgroundColor: 'rgba(255,255,255,0.10)',
          foregroundColor: '#FFFFFF',
          fontSize: 12,
          fontWeight: 900
        },
        brandBar: {
          layout: { type: 'grid', columns: ['auto', '1fr', 'auto'], alignItems: 'center', gap: 12 },
          margin: { top: 16, right: 18, bottom: 2, left: 18 },
          padding: numberValue(currentTheme.cardPadding, 16),
          border: { width: numberValue(currentTheme.borderSize, 1), color: currentTheme.borderColor },
          borderRadius: numberValue(currentTheme.borderRadius, 18) + 2,
          backgroundColor: currentTheme.cardColor,
          background: linearGradient(135, [{ offset: 0, color: currentTheme.cardColor }, { offset: 1, color: currentTheme.secondaryColor }], currentTheme.cardColor),
          shadow: { x: 0, y: 12, blur: 28, color: 'rgba(18,50,70,' + shadowAlpha.toFixed(2) + ')' },
          parts: {
            mark: { minWidth: 78, height: 32, paddingHorizontal: 10, borderWidth: 2, borderColor: currentTheme.topbarColor, borderRadius: 8, backgroundColor: currentTheme.cardColor, color: currentTheme.topbarColor, fontSize: 11, fontWeight: 900 },
            title: { color: currentTheme.textColor, fontSize: 14, fontWeight: 900, lineHeight: 1.2, maxLines: 1, overflow: 'ellipsis' },
            subtitle: { color: currentTheme.mutedTextColor, fontSize: 11, fontWeight: 700, maxLines: 1, overflow: 'ellipsis' },
            status: { color: currentTheme.accentColor, fontSize: 12, fontWeight: 900, dotSize: 8, dotColor: currentTheme.accentColor, dotHaloColor: 'rgba(4,147,66,0.12)', dotHaloWidth: 5 }
          }
        },
        page: {
          width: '100%',
          padding: { top: numberValue(currentTheme.pagePadding, 18), right: numberValue(currentTheme.pagePadding, 18), bottom: 4, left: numberValue(currentTheme.pagePadding, 18) },
          gap: numberValue(currentTheme.cardSpacing, 14),
          backgroundColor: 'transparent'
        },
        heroCard: {
          layout: { type: 'grid', columns: ['1fr', 'auto'], alignItems: 'end', gap: 12 },
          position: 'relative',
          minHeight: 112,
          marginBottom: 14,
          padding: 18,
          borderRadius: numberValue(currentTheme.borderRadius, 18) + 6,
          overflow: 'clip',
          backgroundColor: currentTheme.topbarColor,
          background: linearGradient(145, [{ offset: 0, color: 'rgba(18,50,70,0.98)' }, { offset: 1, color: 'rgba(106,142,174,0.90)' }], currentTheme.topbarColor),
          foregroundColor: '#FFFFFF',
          shadow: { x: 0, y: 18, blur: 38, color: 'rgba(18,50,70,' + Math.min(0.35, shadowAlpha + 0.06).toFixed(2) + ')' },
          decorations: [{ type: 'ring', position: { top: -36, right: -36 }, size: 112, borderWidth: 24, borderColor: 'rgba(209,250,255,0.16)' }],
          parts: {
            kicker: { color: 'rgba(255,255,255,0.72)', fontSize: 11, fontWeight: 800, uppercase: true, gap: 6, marginBottom: 8 },
            title: { color: '#FFFFFF', fontSize: 22, fontWeight: 900, lineHeight: 1.1 },
            subtitle: { color: 'rgba(255,255,255,0.68)', fontSize: 12, fontWeight: 700, marginTop: 6 },
            badge: { width: 58, height: 58, borderRadius: 20, backgroundColor: 'rgba(255,255,255,0.12)', innerBorder: { width: 1, color: 'rgba(255,255,255,0.14)' }, valueStyle: { color: '#FFFFFF', fontSize: 20, fontWeight: 900 }, labelStyle: { color: 'rgba(255,255,255,0.68)', fontSize: 10, fontWeight: 800, uppercase: true } }
          }
        },
        infoCard: {
          layout: { type: 'grid', columns: ['1fr', 'auto'], alignItems: 'center', gap: 10 },
          minHeight: 50,
          padding: numberValue(currentTheme.cardPadding, 16),
          border: { width: numberValue(currentTheme.borderSize, 1), color: currentTheme.borderColor },
          borderRadius: numberValue(currentTheme.borderRadius, 18),
          backgroundColor: currentTheme.cardColor,
          shadow: { x: 0, y: 12, blur: 28, color: 'rgba(18,50,70,' + shadowAlpha.toFixed(2) + ')' },
          parts: {
            label: { color: currentTheme.mutedTextColor, fontSize: 11, fontWeight: 800, lineHeight: 1, uppercase: true, marginBottom: 5 },
            value: { color: currentTheme.textColor, fontSize: 16, fontWeight: 700, lineHeight: 1.25 },
            action: { color: currentTheme.accentColor, fontSize: 12, fontWeight: 800 }
          }
        },
        parameterCard: {
          layout: { type: 'grid', columns: ['auto', '1fr', 'auto'], alignItems: 'center', gap: 12 },
          stackedLayout: { type: 'column', alignItems: 'stretch', gap: 10, overflow: 'visible' },
          position: 'relative',
          overflow: 'visible',
          minHeight: 66,
          padding: numberValue(currentTheme.cardPadding, 16),
          borderWidth: numberValue(currentTheme.borderSize, 1),
          borderRadius: numberValue(currentTheme.borderRadius, 18),
          blur: numberValue(currentTheme.cardBlur, 0),
          shadow: { x: 0, y: 12, blur: 28, color: 'rgba(18,50,70,' + shadowAlpha.toFixed(2) + ')' },
          parts: {
            label: { color: currentTheme.mutedTextColor, fontSize: 11, fontWeight: 800, lineHeight: 1, uppercase: true, marginBottom: 5 },
            value: { color: currentTheme.textColor, fontSize: 16, fontWeight: 700, lineHeight: 1.25 },
            iconBubble: { width: 38, height: 38, shape: 'circle', backgroundColor: 'rgba(37,99,235,0.14)', innerBorder: { width: 1, color: 'rgba(37,99,235,0.18)' } },
            metaChip: { minWidth: 42, height: 34, paddingHorizontal: 8, gap: 5, shape: 'pill', backgroundColor: 'rgba(37,99,235,0.12)', color: currentTheme.accentColor, fontSize: 12, fontWeight: 900, iconSize: 14 },
            switch: { trackWidth: 34, trackHeight: 18, thumbSize: 20, shape: 'pill', onTrackColor: currentTheme.toggleOffColor, onThumbColor: currentTheme.toggleOnColor, offTrackColor: currentTheme.toggleOffColor, offThumbColor: currentTheme.cardColor },
            actionDot: { width: 36, height: 36, shape: 'circle', borderWidth: 3, borderColor: currentTheme.accentColor, backgroundColor: currentTheme.accentColor, innerBorder: { width: 4, color: '#FFFFFF' } },
            leftAccent: { visible: true, width: 3, insetVertical: 14, background: linearGradient(180, [{ offset: 0, color: currentTheme.secondaryColor }, { offset: 1, color: currentTheme.accentColor }], currentTheme.accentColor) }
          }
        },
        progress: {
          height: 5,
          margin: { top: 0, right: 18, bottom: 8, left: 18 },
          borderRadius: 999,
          trackColor: 'rgba(106,142,174,0.18)',
          fill: linearGradient(90, [{ offset: 0, color: '#6A8EAE' }, { offset: 1, color: currentTheme.accentColor }], currentTheme.accentColor)
        },
        pagerControls: {
          layout: { type: 'grid', columns: [48, '1fr', 48], alignItems: 'center', gap: 10 },
          margin: { top: 6, right: 18, bottom: 0, left: 18 },
          padding: 12,
          border: { width: 1, color: 'rgba(106,142,174,0.28)' },
          borderRadius: 20,
          backgroundColor: 'rgba(255,255,255,0.84)',
          shadow: { x: 0, y: 12, blur: 28, color: 'rgba(18,50,70,' + shadowAlpha.toFixed(2) + ')' },
          parts: {
            arrow: { width: 44, height: 44, shape: 'circle', borderWidth: 1, borderColor: 'rgba(106,142,174,0.28)', backgroundColor: currentTheme.topbarColor, foregroundColor: '#FFFFFF' },
            arrowDisabled: { backgroundColor: currentTheme.cardColor, foregroundColor: '#B8BDC7', opacity: 0.65 },
            pageButton: { minWidth: 34, height: 34, paddingHorizontal: 10, shape: 'pill', borderWidth: 1, borderColor: 'rgba(106,142,174,0.28)', backgroundColor: currentTheme.cardColor, foregroundColor: currentTheme.textColor, fontSize: 13, fontWeight: 900 },
            pageButtonActive: { borderColor: currentTheme.accentColor, background: linearGradient(135, [{ offset: 0, color: currentTheme.secondaryColor }, { offset: 1, color: currentTheme.accentColor }], currentTheme.accentColor), foregroundColor: '#FFFFFF' }
          }
        }
      };
      const parameterDefinitions = {};

      const normalizedPages = pages.map(function(page, pageIndex) {
        const pageId = 'page-' + safeId(page.pageNo || pageIndex + 1);
        const normalizedControls = page.controls.map(function(control, controlIndex) {
          const isSwitch = control.controlKind === 'Switch';
          const isSlider = control.controlKind === 'Slider';
          const isAction = !isSwitch && !isSlider && control.rightAccessory && control.rightAccessory.type === 'actionDot';
          const isReadOnly = !isSwitch && !isSlider && !isAction;
          const parameterId = 'parameter-' + safeId(control.id);
          const actionId = isAction
            ? 'parameter.execute.' + safeId(control.id)
            : (isReadOnly ? null : 'parameter.change.' + safeId(control.id));
          const currentValue = isSwitch
            ? String(control.value).toLowerCase() === 'on'
            : (isSlider ? numberValue(control.value, 0) : control.value);
          const constraints = isSlider ? { minimum: 0, maximum: 255, step: 1 } : {};

          parameterDefinitions[parameterId] = {
            id: parameterId,
            sourceKey: control.id,
            pageId: pageId,
            name: control.name,
            label: control.label,
            sequence: control.order || controlIndex + 1,
            dataType: isSwitch ? 'boolean' : (isSlider ? 'number' : 'string'),
            access: isAction ? 'execute' : (isReadOnly ? 'read' : 'readWrite'),
            value: currentValue,
            defaultValue: currentValue,
            constraints: constraints,
            binding: {
              path: 'parameters.' + parameterId + '.value',
              mode: isAction ? 'event' : (isReadOnly ? 'oneWay' : 'twoWay'),
              updateEvent: isAction ? 'press' : (isReadOnly ? null : 'change')
            },
            action: actionId,
            metadata: {
              originalParameter: control.name,
              originalPageNo: page.pageNo,
              originalSequence: control.order || controlIndex + 1
            }
          };

          return {
            id: 'control-' + safeId(control.id),
            type: 'ParameterCard',
            visible: control.visible !== false,
            order: control.order || controlIndex + 1,
            parameterId: parameterId,
            variant: isSwitch ? 'switch' : (isSlider ? 'slider' : (isAction ? 'action' : 'status')),
            semantics: {
              role: isSwitch ? 'switch' : (isSlider ? 'adjustable' : (isAction ? 'button' : 'status')),
              label: control.label,
              value: String(control.value || '')
            },
            props: {
              label: control.label,
              value: currentValue,
              displayValue: control.value,
              icon: control.icon || 'gauge',
              metaIcon: control.metaIcon || 'gauge',
              accessory: control.rightAccessory,
              enabled: true
            },
            bindings: {
              value: 'parameters.' + parameterId + '.value'
            },
            events: isAction
              ? { press: actionId }
              : (isReadOnly ? {} : { change: actionId }),
            styleRef: 'parameterCard',
            style: {
              layout: componentStyles.parameterCard.layout,
              backgroundColor: control.styles.cardColor,
              background: { type: 'solid', color: control.styles.cardColor },
              borderColor: control.styles.borderColor,
              borderWidth: numberValue(currentTheme.borderSize, 1),
              borderRadius: numberValue(currentTheme.borderRadius, 18),
              minHeight: 66,
              padding: numberValue(currentTheme.cardPadding, 16),
              blur: numberValue(currentTheme.cardBlur, 0),
              iconColor: control.styles.iconColor,
              textColor: control.styles.textColor,
              mutedTextColor: control.styles.mutedTextColor,
              statusColor: control.styles.statusColor,
              shadow: componentStyles.parameterCard.shadow,
              parts: {
                label: componentStyles.parameterCard.parts.label,
                value: componentStyles.parameterCard.parts.value,
                iconBubble: Object.assign({}, componentStyles.parameterCard.parts.iconBubble, { foregroundColor: control.styles.iconColor }),
                metaChip: Object.assign({}, componentStyles.parameterCard.parts.metaChip, { backgroundColor: control.rightAccessory && control.rightAccessory.backgroundColor ? control.rightAccessory.backgroundColor : componentStyles.parameterCard.parts.metaChip.backgroundColor }),
                switch: Object.assign({}, componentStyles.parameterCard.parts.switch, { onTrackColor: control.rightAccessory && control.rightAccessory.onColor ? control.rightAccessory.onColor : currentTheme.toggleOnColor, offTrackColor: control.rightAccessory && control.rightAccessory.offColor ? control.rightAccessory.offColor : currentTheme.toggleOffColor }),
                actionDot: componentStyles.parameterCard.parts.actionDot,
                leftAccent: componentStyles.parameterCard.parts.leftAccent
              }
            }
          };
        });

        const pageButtons = pages.map(function(targetPage, targetIndex) {
          return {
            id: 'page-button-' + safeId(targetPage.pageNo || targetIndex + 1),
            type: 'Button',
            variant: targetIndex === pageIndex ? 'pageActive' : 'page',
            props: {
              text: String(targetIndex + 1),
              selected: targetIndex === pageIndex,
              accessibilityLabel: 'Open page ' + String(targetPage.pageNo || targetIndex + 1)
            },
            events: { press: 'navigation.page.select' },
            eventParameters: { pageIndex: targetIndex, pageId: 'page-' + safeId(targetPage.pageNo || targetIndex + 1) },
            style: targetIndex === pageIndex ? componentStyles.pagerControls.parts.pageButtonActive : componentStyles.pagerControls.parts.pageButton
          };
        });

        return {
          id: pageId,
          route: '/pages/' + encodeURIComponent(String(page.pageNo || pageIndex + 1)),
          pageNo: String(page.pageNo || pageIndex + 1),
          name: page.pageName,
          title: page.pageName,
          initial: pageIndex === 0,
          parameterIds: normalizedControls.map(function(control) { return control.parameterId; }),
          componentTree: {
            id: pageId + '-content',
            type: 'ScrollView',
            props: { axis: 'vertical', keyboardSafe: true, safeArea: true },
            styleRef: 'page',
            style: componentStyles.page,
            children: [
              {
                id: pageId + '-hero',
                type: 'HeroCard',
                visible: boolValue(currentTheme.heroVisible),
                props: {
                  kicker: 'LIVE CONTROL PAGE',
                  icon: currentTheme.iconStyle || 'signal',
                  title: page.pageName,
                  subtitle: normalizedControls.length + ' controls linked to Page ' + page.pageNo,
                  badgeValue: pageIndex + 1,
                  badgeLabel: 'PAGE'
                },
                styleRef: 'heroCard',
                style: componentStyles.heroCard
              },
              {
                id: pageId + '-name',
                type: 'InfoCard',
                props: { label: 'NAME', value: page.pageName, actionText: 'Edit' },
                events: { actionPress: 'ui.edit' },
                styleRef: 'infoCard',
                style: componentStyles.infoCard
              },
              {
                id: pageId + '-parameters',
                type: 'List',
                props: { axis: 'vertical', gap: token('spacing.card'), reorderable: false },
                children: normalizedControls
              },
              {
                id: pageId + '-progress',
                type: 'Progress',
                props: { value: (pageIndex + 1) / Math.max(1, pages.length), minimum: 0, maximum: 1 },
                styleRef: 'progress',
                style: componentStyles.progress
              },
              {
                id: pageId + '-pager',
                type: 'PagerControls',
                styleRef: 'pagerControls',
                style: componentStyles.pagerControls,
                children: [
                  { id: pageId + '-previous', type: 'IconButton', props: { icon: 'arrow-left', accessibilityLabel: 'Previous page', disabled: pageIndex === 0 }, events: { press: 'navigation.page.previous' }, style: pageIndex === 0 ? Object.assign({}, componentStyles.pagerControls.parts.arrow, componentStyles.pagerControls.parts.arrowDisabled) : componentStyles.pagerControls.parts.arrow },
                  { id: pageId + '-page-buttons', type: 'HStack', props: { alignment: 'center', wrap: true }, children: pageButtons },
                  { id: pageId + '-next', type: 'IconButton', props: { icon: 'arrow-right', accessibilityLabel: 'Next page', disabled: pageIndex === pages.length - 1 }, events: { press: 'navigation.page.next' }, style: pageIndex === pages.length - 1 ? Object.assign({}, componentStyles.pagerControls.parts.arrow, componentStyles.pagerControls.parts.arrowDisabled) : componentStyles.pagerControls.parts.arrow }
                ]
              }
            ]
          }
        };
      });

      const parameterActions = {};
      Object.keys(parameterDefinitions).forEach(function(parameterId) {
        const parameter = parameterDefinitions[parameterId];
        if (!parameter.action) return;
        parameterActions[parameter.action] = {
          id: parameter.action,
          type: parameter.access === 'execute' ? 'command' : 'setParameter',
          target: parameterId,
          input: parameter.access === 'execute' ? {} : { value: parameter.dataType },
          transport: { adapter: 'deviceApi', operation: parameter.access === 'execute' ? 'execute' : 'write' }
        };
      });

      const actions = Object.assign({
        'navigation.back': { id: 'navigation.back', type: 'navigateBack' },
        'navigation.page.previous': { id: 'navigation.page.previous', type: 'pagerPrevious', target: 'mainPager', wrap: false },
        'navigation.page.next': { id: 'navigation.page.next', type: 'pagerNext', target: 'mainPager', wrap: false },
        'navigation.page.select': { id: 'navigation.page.select', type: 'pagerSelect', target: 'mainPager', input: { pageIndex: 'integer', pageId: 'string' } },
        'ui.info': { id: 'ui.info', type: 'openInfo', target: 'model' },
        'ui.edit': { id: 'ui.edit', type: 'navigate', route: '/ui/edit', parameters: { modelNo: modelNo } }
      }, parameterActions);

      return {
        schemaVersion: '2.1.0',
        schemaType: 'universal-ui',
        schemaId: 'efactor://ui/' + encodeURIComponent(modelNo || 'model') + '/view',
        generatedAt: new Date().toISOString(),
        generator: { name: 'Efactor UI Studio', version: '2.1', source: 'Views/items/view_page_ui.php' },
        compatibility: { minimumParserVersion: '2.0.0', legacyPreviewIncluded: true },
        document: {
          id: 'model-' + safeId(modelNo || 'model'),
          modelNo: modelNo,
          title: modelNo || 'Linked UI',
          locale: document.documentElement.lang || 'en',
          direction: document.documentElement.dir || 'ltr',
          initialPageId: normalizedPages.length ? normalizedPages[0].id : null
        },
        rendererTargets: {
          required: ['html', 'flutter', 'androidXml', 'swiftUI', 'reactNative'],
          contract: 'Render the neutral componentTree, apply visualContract style precedence, resolve design token references, connect bindings, and dispatch declared actions. Extensions are optional.',
          capabilities: ['multiPage', 'navigation', 'buttons', 'parameterBindings', 'commands', 'validation', 'accessibility', 'responsiveLayout', 'theming', 'icons', 'visibility', 'ordering']
        },
        visualContract: {
          fidelity: 'exact',
          strict: true,
          allowRendererDefaults: false,
          renderCanvas: true,
          canvasStyleRef: 'viewport',
          stylePrecedence: ['node.style', 'designSystem.componentStyles[node.styleRef]', 'designSystem tokens'],
          mergeRule: 'deepMerge',
          dimensionsAreDensityIndependent: true,
          colorFormat: 'sRGB',
          gradientFallback: 'Use fallbackColor when a target cannot render the declared gradient.',
          requiredVisualFeatures: ['canvasBackground', 'screenBackground', 'componentBackgrounds', 'gradients', 'perComponentColors', 'perParameterColors', 'borders', 'radii', 'shadows', 'blur', 'spacing', 'typography', 'icons', 'decorations', 'progress', 'selectedStates', 'disabledStates'],
          instructions: [
            'Never replace a declared background, foreground, border, gradient, radius, shadow, spacing, or typography value with a platform default.',
            'Apply designSystem.componentStyles for every node with styleRef, then deep-merge node.style over it.',
            'Render viewport separately from the phone screen so the surrounding canvas does not use the host application default color.',
            'Preserve per-parameter node.style colors and style.parts overrides.',
            'Render declared decorations such as the HeroCard ring and ParameterCard left accent.',
            'Use component part styles for titles, subtitles, badges, icons, accessories, switches, pager buttons, and status indicators.'
          ]
        },
        designSystem: {
          units: { dimension: 'dp', font: 'sp', densityIndependent: true },
          colors: {
            background: currentTheme.backgroundColor,
            gradientEnd: currentTheme.gradientColor,
            surface: currentTheme.shellColor,
            card: currentTheme.cardColor,
            text: currentTheme.textColor,
            textMuted: currentTheme.mutedTextColor,
            topbar: currentTheme.topbarColor,
            accent: currentTheme.accentColor,
            secondary: currentTheme.secondaryColor,
            success: currentTheme.successColor,
            warning: currentTheme.warningColor,
            error: currentTheme.errorColor,
            border: currentTheme.borderColor,
            switchOn: currentTheme.toggleOnColor,
            switchOff: currentTheme.toggleOffColor,
            icon: currentTheme.iconColor
          },
          typography: {
            body: { family: currentTheme.fontFamily, size: numberValue(currentTheme.fontSize, 14), weight: numberValue(currentTheme.fontWeight, 700), letterSpacing: numberValue(currentTheme.letterSpacing, 0), lineHeight: numberValue(currentTheme.lineHeight, 1.35), align: currentTheme.textAlign || 'left' },
            label: { family: currentTheme.fontFamily, size: 11, weight: 800 },
            title: { family: currentTheme.fontFamily, size: 22, weight: 900 }
          },
          spacing: { page: numberValue(currentTheme.pagePadding, 18), card: numberValue(currentTheme.cardSpacing, 14), cardPadding: numberValue(currentTheme.cardPadding, 16) },
          radii: { card: numberValue(currentTheme.borderRadius, 18), button: numberValue(currentTheme.buttonRadius, 16), pill: 999 },
          borders: { card: { width: numberValue(currentTheme.borderSize, 1), color: token('colors.border') } },
          shadows: { card: { enabled: numberValue(currentTheme.shadowStrength, 70) > 0, strength: numberValue(currentTheme.shadowStrength, 70) } },
          icons: { library: 'semantic', default: currentTheme.parameterIcon || 'gauge', size: numberValue(currentTheme.iconSize, 20), overrides: currentTheme.parameterIcons || {} },
          componentStyles: componentStyles
        },
        responsive: {
          strategy: 'adaptive',
          breakpoints: { compact: 0, medium: 600, expanded: 1024 },
          layouts: {
            compact: { maxContentWidth: 430, columns: 1, navigation: 'pager' },
            medium: { maxContentWidth: 720, columns: 1, navigation: 'pager' },
            expanded: { maxContentWidth: 1024, columns: 1, navigation: 'pager' }
          },
          safeArea: true
        },
        data: { bindingRoot: 'data', parameters: parameterDefinitions, state: { activePageIndex: 0, online: true }, validation: { mode: 'onChange', showInlineErrors: true } },
        actions: actions,
        navigation: { type: 'pager', id: 'mainPager', initialIndex: 0, pageIds: normalizedPages.map(function(page) { return page.id; }), preservePageState: true },
        app: {
          id: 'app-root',
          type: 'App',
          styleRef: 'viewport',
          style: componentStyles.viewport,
          componentTree: {
            id: 'screen-root',
            type: 'Screen',
            styleRef: 'screen',
            style: componentStyles.screen,
            children: [
              {
                id: 'app-bar',
                type: 'AppBar',
                props: { titleBinding: 'navigation.activePage.title', subtitle: modelNo, captionPattern: 'Page {pageNo}.Operation', height: numberValue(currentTheme.headerHeight, 84), alignment: currentTheme.headerAlignment || 'left' },
                styleRef: 'appBar',
                style: componentStyles.appBar,
                children: [
                  { id: 'back-button', type: 'Button', variant: 'textIcon', props: { text: 'Back', icon: 'chevron-left', accessibilityLabel: 'Back' }, events: { press: 'navigation.back' }, styleRef: 'backButton', style: componentStyles.backButton },
                  { id: 'info-button', type: 'IconButton', visible: boolValue(currentTheme.showHeaderIcons), props: { icon: 'info', accessibilityLabel: 'Information' }, events: { press: 'ui.info' }, styleRef: 'headerIconButton', style: componentStyles.headerIconButton },
                  { id: 'edit-button', type: 'IconButton', visible: boolValue(currentTheme.showHeaderIcons), props: { icon: 'settings', accessibilityLabel: 'Edit UI' }, events: { press: 'ui.edit' }, styleRef: 'headerIconButton', style: componentStyles.headerIconButton }
                ]
              },
              {
                id: 'brand-strip',
                type: 'BrandBar',
                visible: boolValue(currentTheme.brandVisible),
                props: { mark: 'E factor', highlight: 'E', title: modelNo || 'Linked UI', subtitle: normalizedPages.length + ' linked page' + (normalizedPages.length === 1 ? '' : 's'), status: { visible: boolValue(currentTheme.showStatus), text: 'Online', stateBinding: 'state.online', color: currentTheme.accentColor } },
                styleRef: 'brandBar',
                style: componentStyles.brandBar
              },
              { id: 'main-pager', type: 'Pager', props: { pageIds: normalizedPages.map(function(page) { return page.id; }), activeIndexBinding: 'state.activePageIndex', keepAlive: true } }
            ]
          }
        },
        pages: normalizedPages,
        componentDefinitions: {
          ParameterCard: { slots: ['icon', 'label', 'value', 'accessory'], variants: ['switch', 'slider', 'action', 'status'], requiredProps: ['label', 'value', 'icon'] },
          HeroCard: { slots: ['kicker', 'title', 'subtitle', 'badge'], requiredProps: ['title'] },
          PagerControls: { slots: ['previous', 'pages', 'next'] }
        },
        platformMappings: {
          html: { language: 'HTML/CSS/JavaScript', root: 'main', components: { AppBar: 'header', ScrollView: 'section', List: 'div', Button: 'button', IconButton: 'button', ParameterCard: 'article', Switch: 'input[type=checkbox]', Slider: 'input[type=range]', Progress: 'progress', Text: 'span' }, events: { press: 'click', change: 'change' }, dimensions: 'px-from-dp', styleResolution: 'CSS custom properties plus inline/class styles; implement structured gradients with CSS gradients and decorations with pseudo-elements.' },
          flutter: { language: 'Dart', root: 'MaterialApp/Scaffold', components: { AppBar: 'AppBar', ScrollView: 'SingleChildScrollView', List: 'Column/ListView', Button: 'FilledButton/OutlinedButton', IconButton: 'IconButton', ParameterCard: 'Card', Switch: 'Switch', Slider: 'Slider', Progress: 'LinearProgressIndicator', Text: 'Text' }, events: { press: 'onPressed', change: 'onChanged' }, dimensions: 'logicalPixels', styleResolution: 'BoxDecoration, LinearGradient/RadialGradient, BoxShadow, BorderRadius, Padding and Stack/Positioned decorations.' },
          androidXml: { language: 'Android XML + Kotlin/Java bindings', root: 'ConstraintLayout', components: { AppBar: 'MaterialToolbar', ScrollView: 'NestedScrollView', List: 'LinearLayout/RecyclerView', Button: 'MaterialButton', IconButton: 'ImageButton', ParameterCard: 'MaterialCardView', Switch: 'SwitchMaterial', Slider: 'Slider', Progress: 'LinearProgressIndicator', Text: 'TextView' }, events: { press: 'android:onClick/listener', change: 'listener/dataBinding' }, dimensions: 'dp/sp', styleResolution: 'Material shape appearances and drawable XML; use GradientDrawable/layer-list for structured fills and View overlays for decorations.' },
          swiftUI: { language: 'Swift', root: 'NavigationStack', components: { AppBar: 'toolbar', ScrollView: 'ScrollView', List: 'LazyVStack', Button: 'Button', IconButton: 'Button/Image', ParameterCard: 'GroupBox/custom View', Switch: 'Toggle', Slider: 'Slider', Progress: 'ProgressView', Text: 'Text' }, events: { press: 'Button action', change: 'Binding/onChange' }, dimensions: 'points', styleResolution: 'Shape fills, LinearGradient/RadialGradient, overlay, shadow, clipShape and ZStack decorations.' },
          reactNative: { language: 'TypeScript/JavaScript', root: 'SafeAreaView', components: { AppBar: 'View', ScrollView: 'ScrollView', List: 'FlatList/View', Button: 'Pressable', IconButton: 'Pressable', ParameterCard: 'View', Switch: 'Switch', Slider: '@react-native-community/slider', Progress: 'View/custom progress', Text: 'Text' }, events: { press: 'onPress', change: 'onValueChange' }, dimensions: 'density-independent-pixels', styleResolution: 'StyleSheet plus a gradient component for structured fills; use absolute View overlays for decorations.' }
        },
        validationContract: {
          requiredTopLevel: ['schemaVersion', 'schemaType', 'document', 'rendererTargets', 'visualContract', 'designSystem', 'data', 'actions', 'navigation', 'app', 'pages', 'platformMappings'],
          componentRequired: ['id', 'type'],
          references: { token: '{section.name}', binding: 'dot.path', action: 'actions key', page: 'pages id', parameter: 'data.parameters key' },
          unknownFields: 'ignore',
          unknownComponents: 'renderFallbackAndReport'
        },
        extensions: {
          exactHtmlPreview: previewSchema,
          notes: 'The neutral fields above are authoritative. This extension preserves the original browser preview and supports schema 1.0 consumers.'
        }
      };
    }

    // Schema Generation
    function buildSchema() {
      const toBool = function(value) {
        return String(value) !== '0';
      };
      const px = function(value, fallback) {
        const number = parseInt(value || fallback || 0, 10);
        return number + 'px';
      };
      const dp = function(value, fallback) {
        const number = parseInt(value || fallback || 0, 10);
        return number + 'dp';
      };
      const baseCardStyle = function() {
        return {
          backgroundColor: currentTheme.cardColor,
          borderColor: currentTheme.borderColor,
          borderWidth: px(currentTheme.borderSize, 1),
          borderRadius: px(currentTheme.borderRadius, 18),
          padding: px(currentTheme.cardPadding, 16),
          shadow: {
            enabled: parseInt(currentTheme.shadowStrength || 0, 10) > 0,
            strength: parseInt(currentTheme.shadowStrength || 70, 10)
          }
        };
      };
      const baseTextStyle = function(sizeOffset) {
        return {
          fontFamily: currentTheme.fontFamily,
          fontSize: (parseInt(currentTheme.fontSize || 14, 10) + (sizeOffset || 0)) + 'px',
          fontWeight: parseInt(currentTheme.fontWeight || 700, 10),
          letterSpacing: (parseFloat(currentTheme.letterSpacing || 0)) + 'px',
          lineHeight: parseFloat(currentTheme.lineHeight || 1.35),
          color: currentTheme.textColor,
          align: currentTheme.textAlign || 'left'
        };
      };
      const schemaTheme = Object.assign({}, currentTheme);
      [
        'buttonBgColor', 'buttonTextColor', 'buttonText', 'buttonSize',
        'buttonRadius', 'buttonHover', 'buttonIconPosition', 'buttonTarget',
        'footerVisible'
      ].forEach(function(key) {
        delete schemaTheme[key];
      });
      const controls = [];
      document.querySelectorAll('.page-slide').forEach(function(slide) {
        Array.from(slide.querySelectorAll('.param-card')).forEach(function(card, index) {
          const override = getComponentOverride(card.dataset.paramKey);
          const componentColors = getEffectiveComponentColors(override);
          const resolvedType = getResolvedParamComponentType(card);
          const isSwitch = Boolean(card.querySelector('.toggle-visual')) || resolvedType === 'switch';
          const isRange = Boolean(card.querySelector('.range-visual')) || resolvedType === 'slider';
          const isPowerGauge = resolvedType === 'powerGauge' || Boolean(card.querySelector('.pointer-gauge-visual, .power-gauge-visual'));
          const isNeedleGauge = resolvedType === 'needleGauge' || Boolean(card.querySelector('.needle-gauge-visual'));
          const isGauge = isPowerGauge || isNeedleGauge;
          const isSegmented = Boolean(card.querySelector('.segmented-visual')) || resolvedType === 'segmented';
          const segmentedEl = card.querySelector('.segmented-visual');
          const metaButton = card.querySelector('.param-meta');
          const actionDot = card.querySelector('.action-dot');
          const labelElement = card.querySelector('.field-label');
          const valueElement = card.querySelector('.field-value');
          const metaValueElement = card.querySelector('.meta-value');
          const apiMetaRaw = (function() {
            try {
              const parsed = JSON.parse(card.dataset.apiMeta || '{}');
              return parsed && typeof parsed === 'object' && !Array.isArray(parsed) ? parsed : {};
            } catch (error) {
              return {};
            }
          })();
          const apiMeta = mergeParamMetaWithOverride(apiMetaRaw, override);
          const labelText = override.title || (labelElement ? labelElement.textContent.trim() : card.dataset.paramName);
          const valueText = override.value || (valueElement ? valueElement.textContent.trim() : '');
          const controlKind = isSwitch ? 'Switch' : (isRange ? 'Slider' : (isGauge ? (isPowerGauge ? 'AnimatedRadialGauge' : 'NeedleGauge') : (isSegmented ? 'SegmentedControl' : 'Action')));
          const rangeMin = apiMeta.minimum === null || typeof apiMeta.minimum === 'undefined' || apiMeta.minimum === '' ? 0 : parseFloat(apiMeta.minimum);
          const rangeMax = apiMeta.maximum === null || typeof apiMeta.maximum === 'undefined' || apiMeta.maximum === '' ? 255 : parseFloat(apiMeta.maximum);
          const rangeStep = apiMeta.step === null || typeof apiMeta.step === 'undefined' || apiMeta.step === '' ? 1 : parseFloat(apiMeta.step);
          const rightAccessory = isSwitch ? {
            type: 'toggle',
            checked: String(valueText).toLowerCase() === 'on',
            onColor: currentTheme.toggleOnColor,
            offColor: currentTheme.toggleOffColor
          } : (isRange ? {
            type: 'sliderRange',
            icon: getParamMetaIcon(card),
            value: metaValueElement ? metaValueElement.textContent.trim() : String(index + 1),
            min: Number.isFinite(rangeMin) ? rangeMin : 0,
            max: Number.isFinite(rangeMax) ? rangeMax : 255,
            step: Number.isFinite(rangeStep) ? rangeStep : 1,
            current: parseFloat(valueText),
            backgroundColor: componentColors.statusColor,
            textColor: componentColors.accentColor,
            trackColor: componentColors.accentColor
          } : (isGauge ? {
            type: isPowerGauge ? 'powerGauge' : 'needleGauge',
            icon: getParamMetaIcon(card),
            value: valueText,
            min: Number.isFinite(rangeMin) ? rangeMin : 0,
            max: Number.isFinite(rangeMax) ? rangeMax : 100,
            step: Number.isFinite(rangeStep) ? rangeStep : 1,
            current: parseFloat(valueText),
            gauge: buildAnimatedRadialGaugeProps(resolveGaugeNumericBounds(apiMeta, valueText), {
              activeColor: componentColors.accentColor,
              inactiveColor: '#D9DEEB',
              needleColor: '#0F2550'
            })
          } : (isSegmented ? {
            type: 'segmented',
            icon: getParamMetaIcon(card),
            value: segmentedEl && segmentedEl.dataset.value ? segmentedEl.dataset.value : valueText,
            options: (function() {
              const parsed = parseOptionPairs(apiMeta);
              if (parsed.length) return parsed;
              return Array.from(segmentedEl ? segmentedEl.querySelectorAll('.segmented-option') : []).map(function(node) {
                return { label: node.textContent.trim(), value: node.dataset.value };
              });
            })(),
            selectedColor: componentColors.accentColor,
            textColor: componentColors.textColor
          } : (metaButton ? {
            type: 'metaChip',
            icon: getParamMetaIcon(card),
            value: metaValueElement ? metaValueElement.textContent.trim() : String(index + 1),
            backgroundColor: componentColors.statusColor,
            textColor: componentColors.accentColor
          } : {
            type: actionDot ? 'actionDot' : 'none',
            color: componentColors.accentColor
          }))));
          controls.push({
            id: card.dataset.paramKey,
            name: labelText,
            pageNo: slide.dataset.pageNo,
            pageName: slide.dataset.pageTitle || '',
            component: isSwitch ? 'Switch' : (isRange ? 'Slider' : (isGauge ? (isPowerGauge ? 'AnimatedRadialGauge' : 'NeedleGauge') : (isSegmented ? 'SegmentedControl' : 'ActionCard'))),
            controlKind: controlKind,
            renderAs: 'ParameterCard',
            mustUseTemplate: 'controlCard',
            icon: getParamIcon(card),
            metaIcon: getParamMetaIcon(card),
            order: index + 1,
            visible: !override.hidden,
            value: valueText,
            label: labelText,
            fieldLabel: labelText,
            fieldValue: valueText,
            rightAccessory: rightAccessory,
            override: buildComponentOverridePayload(override),
            styles: {
              width: 'match_parent',
              minHeight: '66px',
              cardColor: componentColors.cardColor,
              borderColor: componentColors.borderColor,
              borderWidth: px(currentTheme.borderSize, 1),
              statusColor: componentColors.statusColor,
              accentColor: componentColors.accentColor,
              iconColor: componentColors.iconColor,
              textColor: componentColors.textColor,
              mutedTextColor: currentTheme.mutedTextColor,
              borderRadius: px(currentTheme.borderRadius, 18),
              cardPadding: px(currentTheme.cardPadding, 16),
              leftAccent: {
                visible: true,
                width: '3px',
                colorStart: currentTheme.secondaryColor,
                colorEnd: componentColors.accentColor
              },
              iconBubble: {
                shape: 'circle',
                size: '38px',
                backgroundColor: 'color-mix(' + componentColors.accentColor + ' 14%, #FFFFFF)',
                iconColor: componentColors.iconColor
              }
            },
            xml: {
              tag: 'MaterialCardView',
              styleRef: '@style/PhoneParameterCard',
              controlTag: isSwitch ? 'SwitchCompat' : (isRange ? 'SeekBar' : 'LinearLayout'),
              textBinding: labelText,
              valueBinding: valueText,
              iconRef: '@drawable/' + (getParamIcon(card) || 'ic_gauge'),
              metaIconRef: '@drawable/' + (getParamMetaIcon(card) || 'ic_gauge')
            }
          });
        });
      });

      const pagesFromDom = Array.from(document.querySelectorAll('.page-slide')).map(function(slide, pageIndex) {
        const pageNo = slide.dataset.pageNo || String(pageIndex + 1);
        const pageName = slide.dataset.pageTitle || ('Page ' + pageNo);
        const heroTitle = slide.querySelector('.slide-hero h2');
        const displayedHeroTitle = heroTitle ? heroTitle.textContent.trim() : pageName;
        const pageControls = controls.filter(function(control) {
          return String(control.pageNo) === String(pageNo);
        });
        return {
          pageNo: pageNo,
          pageName: pageName,
          layout: 'PhoneShellExact',
          activeIndex: pageIndex,
          controls: pageControls,
          uiTree: [
            {
              id: 'appTopbar',
              component: 'AppTopbar',
              required: true,
              className: 'app-topbar',
              style: {
                height: px(currentTheme.headerHeight, 84),
                backgroundColor: currentTheme.topbarColor,
                color: '#FFFFFF',
                padding: '14px 16px 12px',
                layout: 'back-title-actions'
              },
              children: [
                { id: 'backButton', component: 'IconTextButton', icon: 'chevron-left', text: 'Back', className: 'app-back-btn' },
                {
                  id: 'topTitle',
                  component: 'TitleBlock',
                  title: pageName,
                  subtitle: modelNo,
                  caption: 'Page ' + pageNo + '.Operation'
                },
                {
                  id: 'headerActions',
                  component: 'IconButtonGroup',
                  visible: toBool(currentTheme.showHeaderIcons),
                  actions: [
                    { icon: 'info', label: 'Information' },
                    { icon: 'settings', label: 'Edit UI' }
                  ]
                }
              ]
            },
            {
              id: 'brandStrip',
              component: 'BrandStrip',
              required: true,
              visible: toBool(currentTheme.brandVisible),
              className: 'brand-strip',
              brand: { mark: 'E factor', highlight: 'E' },
              title: modelNo || 'Linked UI',
              subtitle: pageData.length + ' linked page' + (pageData.length === 1 ? '' : 's'),
              status: {
                visible: toBool(currentTheme.showStatus),
                text: 'Online',
                color: currentTheme.successColor
              },
              style: baseCardStyle()
            },
            {
              id: 'slideHero',
              component: 'SlideHero',
              required: true,
              visible: toBool(currentTheme.heroVisible),
              className: 'slide-hero',
              kicker: { icon: currentTheme.iconStyle || 'signal', text: 'LIVE CONTROL PAGE' },
              title: pageName,
              subtitle: pageControls.length + ' controls linked to Page ' + pageNo,
              countBadge: { value: pageIndex + 1, label: 'PAGE' },
              style: {
                minHeight: '112px',
                borderRadius: 'calc(' + px(currentTheme.borderRadius, 18) + ' + 6px)',
                backgroundColor: currentTheme.topbarColor,
                decorativeRing: true,
                textColor: '#FFFFFF'
              }
            },
            {
              id: 'nameCard',
              component: 'NameCard',
              required: true,
              visible: toBool(currentTheme.nameCardVisible),
              className: 'device-card name-card',
              label: 'NAME',
              value: pageName,
              actionText: 'Edit',
              style: baseCardStyle()
            },
            {
              id: 'parameterList',
              component: 'ParameterList',
              required: true,
              className: 'param-list',
              gap: px(currentTheme.cardSpacing, 14),
              controls: pageControls.map(function(control) { return control.id; })
            },
            {
              id: 'progress',
              component: 'SlideProgress',
              required: true,
              visible: toBool(currentTheme.pageNavigatorVisible),
              className: 'progress-shell',
              value: (pageIndex + 1) / Math.max(1, pagesFromDomLength()),
              colorStart: currentTheme.secondaryColor,
              colorEnd: currentTheme.accentColor
            },
            {
              id: 'sliderControls',
              component: 'PageSliderControls',
              required: true,
              visible: toBool(currentTheme.pageNavigatorVisible),
              className: 'slider-controls',
              previousIcon: 'arrow-left',
              nextIcon: 'arrow-right',
              activePage: pageIndex + 1,
              pages: pageData.map(function(item, index) {
                return { index: index + 1, pageNo: item.page_no, label: String(index + 1), active: index === pageIndex };
              })
            }
          ],
          xmlLayout: {
            rootElement: 'ConstraintLayout',
            xmlns: 'http://schemas.android.com/apk/res/android',
            tools: 'http://schemas.android.com/tools',
            attributes: {
              layout_width: 'match_parent',
              layout_height: 'match_parent',
              background: currentTheme.backgroundColor,
              padding: dp(currentTheme.pagePadding, 18)
            },
            children: [
              { id: 'appTopbar', tag: 'MaterialToolbar', required: true },
              { id: 'brandStrip', tag: 'MaterialCardView', required: true },
              { id: 'slideHero', tag: 'MaterialCardView', required: true },
              { id: 'nameCard', tag: 'MaterialCardView', required: true },
              { id: 'parameterList', tag: 'LinearLayout', required: true, childTag: 'MaterialCardView' },
              { id: 'slideProgress', tag: 'ProgressBar', required: true },
              { id: 'sliderControls', tag: 'LinearLayout', required: true }
            ],
            styles: {
              phoneShell: '@style/PhoneShell',
              appTopbar: '@style/PhoneAppTopbar',
              brandStrip: '@style/PhoneBrandStrip',
              hero: '@style/PhoneSlideHero',
              nameCard: '@style/PhoneNameCard',
              parameterCard: '@style/PhoneParameterCard',
              sliderControls: '@style/PhoneSliderControls'
            }
          }
        };
      });

      function pagesFromDomLength() {
        return Math.max(1, document.querySelectorAll('.page-slide').length || pageData.length || 1);
      }

      const rendererContract = {
        name: 'PhoneShellExactRenderer',
        version: '1.0',
        rendererType: 'schema-driven-html-css',
        strict: true,
        sourceOfTruth: ['theme', 'mobileView', 'pages', 'pages.controls', 'xmlHints'],
        instructions: [
          'Render the schema as a phone-shell preview, not as a generic card list.',
          'Use pages[].uiTree in order. If a block is marked required, render it.',
          'Use pages[].controls only inside the ParameterList block.',
          'Use control.renderAs = ParameterCard for every control card.',
          'Use control.component only to choose the right accessory: Switch renders a toggle; Slider renders meta chip plus range slider; ActionCard renders the meta chip/right badge.',
          'Use theme and mobileView values for colors, fonts, spacing, radius, and shadows.',
          'Do not drop BrandStrip, SlideHero, NameCard, SlideProgress, or PageSliderControls.'
        ],
        fieldUsage: {
          theme: 'CSS variables, colors, fonts, radius, shadows, spacing, and icons',
          mobileView: 'Phone frame, required block order, and header/brand/cards configuration',
          pages: 'Page title, page number, uiTree, control ordering, XML layout children',
          controls: 'Parameter card title/value/icon/right accessory and per-control overrides',
          xmlHints: 'Renderer mode and required native-layout/style names'
        },
        htmlSkeleton:
          '<main class="phone-shell" data-renderer="PhoneShellExactRenderer">' +
            '<header class="app-topbar">{{appTopbar}}</header>' +
            '<section class="brand-strip">{{brandStrip}}</section>' +
            '<section class="slider-shell">' +
              '<article class="page-slide is-active">{{slideHero}}{{nameCard}}<div class="param-list">{{controlCards}}</div></article>' +
              '<div class="progress-shell"><div class="progress-bar"></div></div>' +
              '<div class="slider-controls">{{sliderControls}}</div>' +
            '</section>' +
          '</main>',
        css: [
          '.phone-shell{width:min(100%,430px);height:100vh;height:100dvh;max-height:100dvh;margin:0 auto;background:' + currentTheme.backgroundColor + ';box-shadow:0 30px 80px rgba(34,40,60,.18);display:flex;flex-direction:column;overflow:hidden;color:' + currentTheme.textColor + ';font-family:"' + currentTheme.fontFamily + '","Segoe UI",Arial,sans-serif;font-size:' + px(currentTheme.fontSize, 14) + ';font-weight:' + parseInt(currentTheme.fontWeight || 700, 10) + ';letter-spacing:' + (parseFloat(currentTheme.letterSpacing || 0)) + 'px;line-height:' + parseFloat(currentTheme.lineHeight || 1.35) + ';text-align:' + (currentTheme.textAlign || 'left') + ';}.phone-shell>.app-topbar,.phone-shell>.brand-strip{flex:0 0 auto}.phone-shell>.slider-shell{flex:1 1 auto;min-height:0;display:grid;grid-template-rows:minmax(0,1fr) auto auto;overflow:hidden}.phone-shell>.slider-shell>.slide-window{position:relative;min-height:0;overflow:hidden}.phone-shell>.slider-shell>.slide-window>.slide-track{position:absolute;inset:0;display:flex;min-height:0;align-items:stretch}.phone-shell>.slider-shell>.slide-window>.slide-track>.page-slide{flex:0 0 100%;width:100%;min-width:100%;height:100%;max-height:100%;min-height:0;overflow-x:hidden;overflow-y:auto;-webkit-overflow-scrolling:touch;overscroll-behavior:contain;touch-action:pan-y}.phone-shell>.slider-shell>.progress-shell,.phone-shell>.slider-shell>.slider-controls{flex:0 0 auto}.phone-shell>.empty-ui{flex:1 1 auto;min-height:0;overflow-x:hidden;overflow-y:auto;-webkit-overflow-scrolling:touch;overscroll-behavior:contain}',
          '.app-topbar{display:grid;grid-template-columns:auto minmax(0,1fr) auto;gap:10px;align-items:center;min-height:' + px(currentTheme.headerHeight, 84) + ';padding:14px 16px 12px;background:linear-gradient(145deg,rgba(18,50,70,.98),rgba(106,142,174,.94)),' + currentTheme.topbarColor + ';color:#fff;box-shadow:0 18px 38px rgba(18,50,70,.16);}',
          '.app-back-btn,.back-btn,.icon-btn{display:inline-flex;align-items:center;justify-content:center;height:36px;border:0;border-radius:999px;background:rgba(255,255,255,.1);color:#fff;box-shadow:inset 0 0 0 1px rgba(255,255,255,.12);}.app-back-btn,.back-btn{width:auto;min-width:36px;padding:0 10px 0 6px;gap:2px;font-size:13px;font-weight:600;}.icon-btn{width:42px;border-radius:50%;}',
          '.top-title h1{margin:0;color:#fff;font-size:18px;line-height:1.2;font-weight:800;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.top-title p{margin:2px 0 0;color:rgba(255,255,255,.68);font-size:12px;line-height:1.25;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.top-actions{display:flex;align-items:center;gap:4px;}',
          '.brand-strip{display:grid;grid-template-columns:auto minmax(0,1fr) auto;align-items:center;gap:12px;margin:16px 18px 2px;padding:' + px(currentTheme.cardPadding, 16) + ';border:' + px(currentTheme.borderSize, 1) + ' solid ' + currentTheme.borderColor + ';border-radius:calc(' + px(currentTheme.borderRadius, 18) + ' + 2px);background:linear-gradient(135deg,rgba(255,255,255,.94),rgba(209,250,255,.48));box-shadow:0 12px 28px rgba(18,50,70,.1);}',
          '.brand-mark{display:inline-flex;align-items:center;justify-content:center;min-width:78px;height:32px;padding:0 10px;border:2px solid ' + currentTheme.topbarColor + ';border-radius:8px;background:#fff;color:' + currentTheme.topbarColor + ';font-size:11px;font-weight:900;}.brand-copy strong{display:block;color:' + currentTheme.textColor + ';font-size:14px;line-height:1.2;font-weight:900;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.brand-copy span{display:block;margin-top:2px;color:' + currentTheme.mutedTextColor + ';font-size:11px;font-weight:700;}.online-state{display:inline-flex;align-items:center;gap:7px;color:' + currentTheme.successColor + ';font-size:12px;font-weight:900;}',
          '.page-slide{padding:' + px(currentTheme.pagePadding, 18) + ' ' + px(currentTheme.pagePadding, 18) + ' 4px}.slide-hero{position:relative;display:grid;grid-template-columns:minmax(0,1fr) auto;gap:12px;align-items:end;min-height:112px;margin-bottom:14px;padding:18px;border-radius:calc(' + px(currentTheme.borderRadius, 18) + ' + 6px);background:linear-gradient(145deg,rgba(18,50,70,.98),rgba(106,142,174,.9)),' + currentTheme.topbarColor + ';color:#fff;box-shadow:0 18px 38px rgba(18,50,70,.16);overflow:hidden;}.slide-hero:after{content:"";position:absolute;right:-36px;top:-36px;width:112px;height:112px;border-radius:50%;border:24px solid rgba(209,250,255,.16);}',
          '.slide-hero-kicker{display:inline-flex;align-items:center;gap:6px;margin-bottom:8px;color:rgba(255,255,255,.72);font-size:11px;font-weight:800;text-transform:uppercase}.slide-hero h2{margin:0;color:#fff;font-size:22px;line-height:1.1;font-weight:900;overflow-wrap:anywhere}.slide-hero p{margin:6px 0 0;color:rgba(255,255,255,.68);font-size:12px;font-weight:700}.slide-count{position:relative;z-index:1;display:inline-flex;flex-direction:column;align-items:center;justify-content:center;width:58px;height:58px;border-radius:20px;background:rgba(255,255,255,.12);box-shadow:inset 0 0 0 1px rgba(255,255,255,.14);}',
          '.device-card,.param-card{border:' + px(currentTheme.borderSize, 1) + ' solid ' + currentTheme.borderColor + ';border-radius:' + px(currentTheme.borderRadius, 18) + ';background:' + currentTheme.cardColor + ';box-shadow:0 12px 28px rgba(18,50,70,.1);}.name-card{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;align-items:center;padding:' + px(currentTheme.cardPadding, 16) + ';}.field-label{display:block;margin-bottom:5px;color:' + currentTheme.mutedTextColor + ';font-size:11px;line-height:1;font-weight:800;text-transform:uppercase;letter-spacing:.02em}.field-value{display:block;min-width:0;color:' + currentTheme.textColor + ';font-size:16px;line-height:1.25;font-weight:700;overflow-wrap:anywhere}.edit-link{color:' + currentTheme.accentColor + ';font-size:12px;font-weight:800;text-decoration:none;}',
          '.param-list{display:flex;flex-direction:column;gap:' + px(currentTheme.cardSpacing, 14) + ';margin-top:16px}.param-card{display:grid;grid-template-columns:auto minmax(0,1fr) auto;gap:12px;align-items:center;min-height:66px;padding:' + px(currentTheme.cardPadding, 16) + ';position:relative;overflow:hidden;}.param-card.layout-stacked{display:flex;flex-direction:column;align-items:stretch;min-height:0;overflow:visible;padding-bottom:calc(' + px(currentTheme.cardPadding, 16) + ' + 4px)}.param-card.layout-stacked>.param-main-row{display:grid;grid-template-columns:auto minmax(0,1fr) auto;gap:12px;align-items:center;width:100%}.param-card.layout-stacked>.range-visual,.param-card.layout-stacked>.needle-gauge-visual,.param-card.layout-stacked>.pointer-gauge-visual,.param-card.layout-stacked>.power-gauge-visual,.param-card.layout-stacked>.segmented-visual,.param-card.layout-stacked>.linear-gauge-visual,.param-card.layout-stacked>.registered-visual{width:100%;max-width:100%;flex:0 0 auto;overflow:visible;box-sizing:border-box}.needle-gauge-visual,.pointer-gauge-visual,.power-gauge-visual{width:100%;display:flex;flex-direction:column;align-items:center;overflow:visible}.power-gauge-svg-host{width:100%;max-width:240px;margin:0 auto;display:flex;justify-content:center;overflow:visible}.needle-gauge-svg,.power-gauge-svg{width:100%;max-width:240px;height:auto;min-height:148px;display:block;overflow:visible}.param-card:before{content:"";position:absolute;left:0;top:14px;bottom:14px;width:3px;border-radius:0 999px 999px 0;background:linear-gradient(180deg,' + currentTheme.secondaryColor + ',' + currentTheme.accentColor + ')}.param-icon{border:0;display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:rgba(37,99,235,.14);color:' + (currentTheme.iconColor || currentTheme.accentColor) + ';}.param-meta{display:flex;align-items:center;justify-content:center;gap:5px;min-width:42px;height:34px;padding:0 8px;border-radius:999px;background:rgba(37,99,235,.12);color:' + currentTheme.accentColor + ';font-size:12px;font-weight:900;}.toggle-visual{position:relative;width:34px;height:18px;border-radius:999px;background:' + currentTheme.toggleOffColor + ';}.toggle-visual:after{content:"";position:absolute;top:-1px;right:0;width:20px;height:20px;border-radius:50%;background:' + currentTheme.toggleOnColor + ';box-shadow:0 4px 9px rgba(37,99,235,.24);}.range-visual{grid-column:1/-1;margin-top:10px;width:100%}.range-values{display:flex;justify-content:space-between;color:' + currentTheme.textColor + ';font-size:10px;font-weight:600}.range-line{position:relative;height:3px;margin:12px 8px 8px;border-radius:999px;background:' + currentTheme.accentColor + '}.range-line:before{content:"";position:absolute;left:-6px;top:-9px;width:22px;height:22px;border-radius:50%;background:' + currentTheme.accentColor + '}',
          '.progress-shell{margin:0 18px 8px;height:5px;border-radius:999px;background:rgba(106,142,174,.18);overflow:hidden}.progress-bar{height:100%;border-radius:inherit;background:linear-gradient(90deg,' + currentTheme.secondaryColor + ',' + currentTheme.accentColor + ')}.slider-controls{display:grid;grid-template-columns:48px 1fr 48px;gap:10px;align-items:center;margin:6px 18px 0;padding:12px;border:1px solid rgba(106,142,174,.28);border-radius:20px;background:rgba(255,255,255,.72);box-shadow:0 12px 28px rgba(18,50,70,.1);}.slide-arrow{display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border:1px solid rgba(106,142,174,.28);border-radius:50%;background:' + currentTheme.topbarColor + ';color:#fff}.page-buttons{display:flex;align-items:center;justify-content:center;gap:8px;flex-wrap:wrap}.page-button{min-width:34px;height:34px;border:1px solid rgba(106,142,174,.28);border-radius:999px;background:#fff;color:' + currentTheme.textColor + ';font-size:13px;font-weight:900}.page-button.is-active{border-color:' + currentTheme.accentColor + ';background:linear-gradient(135deg,' + currentTheme.secondaryColor + ',' + currentTheme.accentColor + ');color:#fff;}'
        ].join('\n'),
        componentTemplates: {
          appTopbar: '<button class="app-back-btn">{{icon:chevron-left}}<span class="app-back-label">Back</span></button><div class="top-title"><h1>{{page.pageName}}</h1><p>{{modelNo}}</p><p>Page {{page.pageNo}}.Operation</p></div><div class="top-actions">{{actionIcons}}</div>',
          brandStrip: '<div class="brand-mark"><span>E</span>factor</div><div class="brand-copy"><strong>{{modelNo}}</strong><span>{{linkedPagesText}}</span></div><div class="online-state">Online</div>',
          slideHero: '<div class="slide-hero-content"><div class="slide-hero-kicker">{{icon:theme.iconStyle}} Live Control Page</div><h2>{{page.pageName}}</h2><p>{{controlCount}} controls linked to Page {{page.pageNo}}</p></div><div class="slide-count"><strong>{{pageIndex}}</strong><span>Page</span></div>',
          nameCard: '<div><span class="field-label">name</span><span class="field-value">{{page.pageName}}</span></div><a class="edit-link">Edit</a>',
          controlCard: '<article class="param-card{{stackedClass}}"><div class="param-main-row"><button class="param-icon">{{icon:control.icon}}</button><div><span class="field-label">{{control.name}}</span><span class="field-value">{{control.value}}</span></div>{{rightAccessory}}</div>{{rangeAccessory}}</article>',
          switchAccessory: '<span class="toggle-visual" aria-hidden="true"></span>',
          actionAccessory: '<button class="param-meta">{{icon:control.metaIcon}}<span class="meta-value">{{control.rightAccessory.value}}</span></button>',
          sliderAccessory: '<button class="param-meta">{{icon:control.metaIcon}}<span class="meta-value">{{control.rightAccessory.value}}</span></button><div class="range-visual"><div class="range-values"><span>{{control.rightAccessory.min}}</span><span>{{control.rightAccessory.current}}</span><span>{{control.rightAccessory.max}}</span></div><div class="range-line"></div></div>',
          sliderControls: '<button class="slide-arrow">{{icon:arrow-left}}</button><div class="page-buttons">{{pageButtons}}</div><button class="slide-arrow">{{icon:arrow-right}}</button>'
        }
      };

      const previewSchema = {
        schemaVersion: '1.0',
        schemaPurpose: 'exact-phone-shell-ui',
        renderMode: 'match-preview',
        doNotSimplify: true,
        modelNo: modelNo,
        generatedAt: new Date().toISOString(),
        theme: schemaTheme,
        renderer: rendererContract,
        mobileView: {
          device: {
            mode: currentTheme.deviceMode || 'mobile',
            previewWidth: 375,
            previewHeight: 812,
            rootClass: 'phone-shell'
          },
          shell: {
            backgroundType: currentTheme.backgroundType,
            backgroundColor: currentTheme.backgroundColor,
            gradientColor: currentTheme.gradientColor,
            screenColor: currentTheme.shellColor,
            pagePadding: currentTheme.pagePadding,
            width: 'min(100%, 430px)',
            height: '100dvh',
            maxHeight: '100dvh',
            overflow: 'hidden',
            scrollRegion: 'page-slide'
          },
          header: {
            height: currentTheme.headerHeight,
            backgroundColor: currentTheme.topbarColor,
            titleSource: 'page.pageName',
            subtitlePattern: 'Page {pageNo}.Operation',
            showHeaderIcons: String(currentTheme.showHeaderIcons) !== '0',
            alignment: currentTheme.headerAlignment,
            actions: [
              { id: 'info', icon: 'info', label: 'Information' },
              { id: 'settings', icon: 'settings', label: 'Edit UI' }
            ]
          },
          brandStrip: {
            visible: String(currentTheme.brandVisible) !== '0',
            modelNo: modelNo,
            showStatus: String(currentTheme.showStatus) !== '0',
            statusText: 'Online',
            statusColor: currentTheme.successColor
          },
          cards: {
            backgroundColor: currentTheme.cardColor,
            borderColor: currentTheme.borderColor,
            borderSize: currentTheme.borderSize,
            borderRadius: currentTheme.borderRadius,
            shadowStrength: currentTheme.shadowStrength,
            padding: currentTheme.cardPadding,
            spacing: currentTheme.cardSpacing
          },
          typography: {
            fontFamily: currentTheme.fontFamily,
            fontSize: currentTheme.fontSize,
            fontWeight: currentTheme.fontWeight,
            letterSpacing: currentTheme.letterSpacing,
            lineHeight: currentTheme.lineHeight,
            textAlign: currentTheme.textAlign,
            textColor: currentTheme.textColor,
            mutedTextColor: currentTheme.mutedTextColor
          },
          requiredBlocksInOrder: [
            'appTopbar',
            'brandStrip',
            'slideHero',
            'nameCard',
            'parameterList',
            'slideProgress',
            'sliderControls'
          ],
          componentContract: {
            Switch: 'Render with the controlCard template and switchAccessory. Do not render as a plain switch row.',
            Slider: 'Render with the controlCard template and sliderAccessory. Use stacked layout with meta chip and range line.',
            ActionCard: 'Render with the controlCard template and actionAccessory/meta chip. Do not render as a plain row with chevron.',
            ParameterCard: 'Visual template used by both Switch and ActionCard: left round icon, label/value stack, and right accessory chip/toggle/dot.',
            SlideHero: 'Must render the dark hero card with live control kicker and page count badge.',
            PageSliderControls: 'Must render previous arrow, numbered page buttons, next arrow, and progress bar.'
          }
        },
        pages: pagesFromDom,
        xmlHints: {
          root: 'ConstraintLayout',
          layoutName: 'PhoneShellExact',
          renderAllRequiredBlocks: true,
          rendererName: 'PhoneShellExactRenderer',
          mustUseRendererTemplates: true,
          forbiddenSimplifications: [
            'Do not render controls as a standalone generic list.',
            'Do not omit brandStrip, slideHero, nameCard, progress, or sliderControls.',
            'Do not replace ActionCard meta chips with a plain chevron unless control.rightAccessory.type is explicitly none.',
            'Do not ignore renderer.css or renderer.componentTemplates.'
          ],
          useCssVariablesAsAndroidResources: true,
          cardStyle: 'PhoneParameterCard',
          convertColorsToResources: true,
          buttonIconLibrary: 'LocalSvgOrFontAwesome',
          generatedFrom: 'Views/items/view_page_ui.php',
          requiredResources: {
            colors: ['backgroundColor', 'shellColor', 'cardColor', 'textColor', 'mutedTextColor', 'topbarColor', 'accentColor', 'secondaryColor', 'iconColor'],
            dimens: ['headerHeight', 'borderRadius', 'borderSize', 'pagePadding', 'cardPadding', 'cardSpacing'],
            fonts: [currentTheme.fontFamily]
          },
          parameterIconReferences: Object.keys(currentTheme.parameterIcons || {}).map(key => ({
            parameterKey: key,
            iconName: currentTheme.parameterIcons[key],
            fontAwesomeClass: 'fa-' + (currentTheme.parameterIcons[key] || 'gauge')
          })),
          parameterMetaIconReferences: Object.keys(currentTheme.componentOverrides || {}).filter(key => currentTheme.componentOverrides[key] && currentTheme.componentOverrides[key].metaIcon).map(key => ({
            parameterKey: key,
            iconName: currentTheme.componentOverrides[key].metaIcon,
            fontAwesomeClass: 'fa-' + currentTheme.componentOverrides[key].metaIcon
          }))
        }
      };

      return buildUniversalSchema(previewSchema, pagesFromDom, controls);
    }

    function buildCleanSchema() {
      const makeId = function(value, fallback) {
        const id = String(value || '')
          .normalize('NFKD')
          .replace(/[\u0300-\u036f]/g, '')
          .replace(/([a-z0-9])([A-Z])/g, '$1-$2')
          .toLowerCase()
          .replace(/[^a-z0-9]+/g, '_')
          .replace(/^_+|_+$/g, '');
        return id || fallback;
      };
      const uniqueId = function(base, usedIds) {
        let candidate = base;
        let suffix = 2;
        while (usedIds.has(candidate)) {
          candidate = base + '_' + suffix;
          suffix += 1;
        }
        usedIds.add(candidate);
        return candidate;
      };
      const numberValue = function(value, fallback) {
        const parsed = parseFloat(value);
        return Number.isFinite(parsed) ? parsed : fallback;
      };
      const apiMeta = function(card) {
        try {
          const parsed = JSON.parse(card.dataset.apiMeta || '{}');
          return parsed && typeof parsed === 'object' && !Array.isArray(parsed) ? parsed : {};
        } catch (error) {
          return {};
        }
      };
      const componentType = function(card, meta) {
        const apiType = String(meta.ui_type || '').toLowerCase();
        if (apiType.indexOf('segment') !== -1) return 'segmented';
        if (apiType.indexOf('drop') !== -1 || apiType.indexOf('select') !== -1) return 'dropdown';
        if (apiType.indexOf('switch') !== -1 || apiType.indexOf('toggle') !== -1) return 'switch';
        if (apiType.indexOf('slider') !== -1 || apiType.indexOf('range') !== -1) return 'slider';
        if (apiType.indexOf('radial') !== -1 || apiType.indexOf('dial') !== -1) return 'powerGauge';
        if (apiType.indexOf('gauge') !== -1) return 'needleGauge';
        if (apiType.indexOf('button') !== -1 || apiType.indexOf('trigger') !== -1) return 'button';
        if (Boolean(card.querySelector('.toggle-visual'))) return 'switch';
        if (Boolean(card.querySelector('.range-visual'))) return 'slider';
        if (Boolean(card.querySelector('.pointer-gauge-visual, .power-gauge-visual'))) return 'powerGauge';
        if (Boolean(card.querySelector('.needle-gauge-visual'))) return 'needleGauge';
        if (Boolean(card.querySelector('.segmented-visual'))) return 'segmented';
        if (Boolean(card.querySelector('.action-dot'))) return 'button';
        return 'value';
      };
      const componentDefault = function(type, meta, valueElement) {
        const rawValue = meta.default !== null && typeof meta.default !== 'undefined'
          ? meta.default
          : (valueElement ? valueElement.textContent.trim() : null);
        if (type === 'button' || type === 'value') return null;
        if (type === 'switch') return /^(1|true|on|enable|enabled)$/i.test(String(rawValue));
        if (type === 'slider' || type === 'powerGauge' || type === 'needleGauge') return numberValue(rawValue, 0);
        return rawValue;
      };
      const pageIds = new Set();
      const components = {};
      const actions = {
        goBack: { type: 'navigateBack' },
        previousPage: { type: 'navigatePage', target: 'previous' },
        nextPage: { type: 'navigatePage', target: 'next' },
        selectPage: { type: 'navigatePage', target: '$event.pageId' },
        openInfo: { type: 'openModal', target: 'deviceInfoModal', load: 'deviceInfo' },
        closeInfo: { type: 'closeModal', target: 'deviceInfoModal' }
      };

      const baseComponent = function(id, type, label, binding, style, action, visible) {
        return {
          id: id,
          type: type,
          label: label,
          binding: binding,
          style: style,
          action: action,
          visibility: { type: 'constant', value: visible !== false }
        };
      };

      components.appBar = Object.assign(baseComponent('appBar', 'appBar', null, null, 'appBar', null, true), {
        children: ['backButton', 'pageTitle', 'infoButton']
      });
      components.backButton = Object.assign(baseComponent('backButton', 'iconButton', 'Back', null, 'headerButton', 'goBack', true), { icon: 'chevron-left' });
      components.pageTitle = baseComponent('pageTitle', 'title', null, 'navigation.activePage.title', 'pageTitle', null, true);
      components.infoButton = Object.assign(baseComponent('infoButton', 'iconButton', 'Information', null, 'headerButton', 'openInfo', true), { icon: 'info' });
      components.brandBar = baseComponent('brandBar', 'statusCard', null, 'device', 'brandBar', null, String(currentTheme.brandVisible) !== '0');
      components.pageHero = baseComponent('pageHero', 'hero', null, 'navigation.activePage', 'pageHero', null, String(currentTheme.heroVisible) !== '0');
      components.pageNameCard = baseComponent('pageNameCard', 'infoCard', 'Name', 'navigation.activePage.title', 'infoCard', null, true);
      components.pagePager = Object.assign(baseComponent('pagePager', 'pager', null, 'navigation.activePage', 'pager', 'selectPage', true), {
        previousAction: 'previousPage',
        nextAction: 'nextPage'
      });

      const pages = Array.from(document.querySelectorAll('.page-slide')).map(function(slide, pageIndex) {
        const rawPageNumber = slide.dataset.pageNo || String(pageIndex + 1);
        const pageTitle = String(slide.dataset.pageTitle || '').trim() || ('Page ' + rawPageNumber);
        const pageId = uniqueId(makeId(pageTitle, 'page_' + (pageIndex + 1)), pageIds);
        const controlIds = new Set();

        const componentRefs = Array.from(slide.querySelectorAll('.param-card')).map(function(card, controlIndex) {
          const override = getComponentOverride(card.dataset.paramKey);
          const meta = mergeParamMetaWithOverride(apiMeta(card), override);
          const type = componentType(card, meta);
          const label = String(override.title || card.dataset.paramName || ('Control ' + (controlIndex + 1))).trim();
          const controlId = uniqueId(makeId(label, 'control_' + (controlIndex + 1)), controlIds);
          const componentId = pageId + '_' + controlId;
          const actionId = 'update_' + componentId;
          const valueElement = card.querySelector('.field-value');
          const parameterName = card.dataset.paramName || label;
          const binding = 'device.parameters.' + makeId(parameterName, controlId);
          const operation = type === 'button' ? 'execute' : (type === 'value' ? 'read' : 'write');
          const component = baseComponent(componentId, type, label, binding, 'controlCard', actionId, !override.hidden);
          component.icon = getParamIcon(card);
          component.default = componentDefault(type, meta, valueElement);
          if (Array.isArray(meta.items) && meta.items.length) {
            component.options = meta.items.map(function(item, optionIndex) {
              return {
                label: item,
                value: Array.isArray(meta.item_values) && typeof meta.item_values[optionIndex] !== 'undefined'
                  ? meta.item_values[optionIndex]
                  : item
              };
            });
          }
          if (override.cardColor || override.borderColor || override.statusColor || override.accentColor || override.iconColor || override.textColor) {
            component.styleOverrides = {};
            if (override.cardColor) component.styleOverrides.background = override.cardColor;
            if (override.borderColor) component.styleOverrides.border = override.borderColor;
            if (override.statusColor) component.styleOverrides.status = override.statusColor;
            if (override.accentColor) component.styleOverrides.accent = override.accentColor;
            if (override.iconColor) component.styleOverrides.icon = override.iconColor;
            if (override.textColor) component.styleOverrides.text = override.textColor;
          }
          if (type === 'slider' || type === 'powerGauge' || type === 'needleGauge') {
            component.constraints = {
              minimum: numberValue(override.minimum != null && override.minimum !== '' ? override.minimum : meta.minimum, type === 'powerGauge' || type === 'needleGauge' ? 0 : 0),
              maximum: numberValue(override.maximum != null && override.maximum !== '' ? override.maximum : meta.maximum, type === 'powerGauge' || type === 'needleGauge' ? 100 : 255),
              step: numberValue(override.step != null && override.step !== '' ? override.step : meta.step, 1)
            };
            if (type === 'powerGauge' || type === 'needleGauge') {
              component.flutterWidget = type === 'powerGauge' ? 'AnimatedRadialGauge' : 'NeedleGauge';
              component.gauge = buildAnimatedRadialGaugeProps(resolveGaugeNumericBounds(meta, valueElement ? valueElement.textContent.trim() : ''), {
                activeColor: currentTheme.accentColor,
                inactiveColor: '#D9DEEB',
                needleColor: '#0F2550'
              });
              component.events = [
                { event: 'onChanged', action: { type: 'update', binding: binding } },
                { event: 'onValueCommitted', action: { type: 'send', binding: binding } }
              ];
            }
          }
          if (type === 'segmented') {
            component.flutterWidget = 'SegmentedControl';
            const segEl = card.querySelector('.segmented-visual');
            if (!component.options || !component.options.length) {
              const parsed = parseOptionPairs(meta);
              component.options = parsed.length ? parsed : Array.from(segEl ? segEl.querySelectorAll('.segmented-option') : []).map(function(node) {
                return { label: node.textContent.trim(), value: node.dataset.value };
              });
            }
            if (segEl && segEl.dataset.value) component.default = segEl.dataset.value;
            component.events = [
              { event: 'onChanged', action: { type: 'update', binding: binding } },
              { event: 'onSelected', action: { type: 'send', binding: binding } }
            ];
          }
          components[componentId] = component;
          actions[actionId] = {
            type: 'dataOperation',
            dataSource: 'deviceCommands',
            operation: operation,
            input: {
              modelNo: '$app.modelNo',
              parameter: parameterName,
              value: '$' + binding,
              commandId: meta.command_id || null,
              functionCode: meta.function_code || null
            }
          };
          return componentId;
        });

        return {
          id: pageId,
          title: pageTitle,
          layout: {
            type: 'scroll',
            direction: 'vertical',
            padding: 'pagePadding',
            gap: 'cardGap'
          },
          sections: [{
            id: pageId + '_controls',
            layout: { type: 'column', gap: 'cardGap' },
            components: ['pageHero', 'pageNameCard'].concat(componentRefs)
          }]
        };
      });

      const appId = makeId(modelNo, 'efactor_device');

      return {
        schemaVersion: '1.0',
        app: {
          id: appId,
          name: (modelNo || 'Efactor Device') + ' Control',
          version: '1.0.0',
          modelNo: modelNo,
          platforms: ['flutter', 'react', 'android', 'ios'],
          shell: { header: 'appBar', brand: 'brandBar', content: '$navigation.activePage', footer: 'pagePager' }
        },
        theme: {
          mode: currentTheme.themeMode || 'light',
          tokens: {
            colors: {
              primary: currentTheme.accentColor,
              secondary: currentTheme.secondaryColor,
              background: currentTheme.backgroundColor,
              surface: currentTheme.cardColor,
              text: currentTheme.textColor,
              muted: currentTheme.mutedTextColor,
              border: currentTheme.borderColor
            },
            typography: { family: currentTheme.fontFamily, body: numberValue(currentTheme.fontSize, 14) },
            spacing: { pagePadding: numberValue(currentTheme.pagePadding, 18), cardGap: numberValue(currentTheme.cardSpacing, 14) },
            radii: { card: numberValue(currentTheme.borderRadius, 18), modal: 24 }
          },
          styles: {
            appBar: { background: '$colors.primary', foreground: '#FFFFFF' },
            headerButton: { shape: 'circle', size: 42 },
            pageTitle: { color: '#FFFFFF', weight: 800 },
            brandBar: { background: '$colors.surface', border: '$colors.border', radius: '$radii.card' },
            pageHero: { background: '$colors.primary', foreground: '#FFFFFF', radius: '$radii.card' },
            infoCard: { background: '$colors.surface', border: '$colors.border', radius: '$radii.card' },
            controlCard: { background: '$colors.surface', border: '$colors.border', radius: '$radii.card' },
            pager: { background: '$colors.surface', border: '$colors.border', radius: '$radii.card' },
            infoModal: { background: '$colors.surface', radius: '$radii.modal' }
          }
        },
        navigation: {
          type: 'pager',
          initial: pages.length ? pages[0].id : null,
          pages: pages.map(function(page) { return page.id; }),
          controls: 'pagePager'
        },
        pages: pages,
        components: components,
        modals: {
          deviceInfoModal: {
            type: 'modal',
            title: 'Information',
            style: 'infoModal',
            layout: { type: 'scroll', direction: 'vertical' },
            content: { type: 'keyValueList', source: 'deviceInfo' },
            closeAction: 'closeInfo'
          }
        },
        actions: actions,
        dataSources: {
          deviceInfo: {
            type: 'rest',
            method: 'GET',
            url: deviceInfoUrl,
            responsePath: 'node_details[0].config.info',
            load: 'onAction'
          },
          deviceCommands: {
            type: 'adapter',
            adapter: 'deviceApi',
            protocol: 'mqtt'
          },
          deviceState: {
            type: 'state',
            namespace: 'device.parameters'
          }
        },
        events: {
          infoPress: { source: 'infoButton', event: 'press', action: 'openInfo' }
        }
      };
    }

    function buildFlutterWidgetSchema() {
      const n = function(value, fallback) {
        const parsed = parseFloat(value);
        return Number.isFinite(parsed) ? parsed : fallback;
      };
      const bool = function(value) {
        return String(value) !== '0';
      };
      const hexByte = function(value) {
        const clamped = Math.max(0, Math.min(255, Math.round(value)));
        return clamped.toString(16).padStart(2, '0').toUpperCase();
      };
      const alphaHex = function(alpha) {
        return hexByte(Math.max(0, Math.min(1, alpha)) * 255);
      };
      const hexWithAlpha = function(hex, alpha) {
        const clean = String(hex || '#000000').replace('#', '').trim();
        const rgb = clean.length === 3
          ? clean.split('').map(function(part) { return part + part; }).join('')
          : clean.slice(0, 6);
        return '#' + alphaHex(alpha) + rgb.toUpperCase();
      };
      const rgbaHex = function(red, green, blue, alpha) {
        return '#' + alphaHex(alpha) + hexByte(red) + hexByte(green) + hexByte(blue);
      };
      const normalizeColor = function(value) {
        if (value === null || typeof value === 'undefined') return value;
        const raw = String(value).trim();
        if (raw === 'transparent') return '#00000000';
        const rgbaMatch = raw.match(/^rgba?\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)(?:\s*,\s*([\d.]+))?\s*\)$/i);
        if (rgbaMatch) {
          return rgbaHex(
            parseFloat(rgbaMatch[1]),
            parseFloat(rgbaMatch[2]),
            parseFloat(rgbaMatch[3]),
            typeof rgbaMatch[4] === 'undefined' ? 1 : parseFloat(rgbaMatch[4])
          );
        }
        if (/^#[0-9a-f]{3}$/i.test(raw)) {
          return '#' + raw.slice(1).split('').map(function(part) { return part + part; }).join('').toUpperCase();
        }
        if (/^#[0-9a-f]{6}$/i.test(raw) || /^#[0-9a-f]{8}$/i.test(raw)) return raw.toUpperCase();
        return value;
      };
      const resolveSchemaValues = function(value, key) {
        if (Array.isArray(value)) {
          return value.map(function(item) { return resolveSchemaValues(item, key); });
        }
        if (value && typeof value === 'object') {
          Object.keys(value).forEach(function(childKey) {
            value[childKey] = resolveSchemaValues(value[childKey], childKey);
          });
          return value;
        }
        if (typeof value === 'string') {
          if (/(color|background|foreground|border|shadow|textcolor)$/i.test(String(key || '')) || value === 'transparent' || /^rgba?\(/i.test(value) || /^#[0-9a-f]{3,8}$/i.test(value)) {
            return normalizeColor(value);
          }
        }
        return value;
      };
      const slug = function(value, fallback) {
        const id = String(value || '')
          .normalize('NFKD')
          .replace(/[\u0300-\u036f]/g, '')
          .toLowerCase()
          .replace(/[^a-z0-9]+/g, '_')
          .replace(/^_+|_+$/g, '');
        return id || fallback;
      };
      const edge = function(top, right, bottom, left) {
        return { top: top, right: right, bottom: bottom, left: left };
      };
      const border = function(width, color) {
        return { width: width, color: normalizeColor(color || '#00000000') };
      };
      const shadow = function(y, blur, color) {
        return { offsetX: 0, offsetY: y, blurRadius: blur, spreadRadius: 0, color: normalizeColor(color) };
      };
      const textStyle = function(size, weight, color, lineHeight) {
        return {
          fontFamily: currentTheme.fontFamily,
          fontSize: size,
          fontWeight: weight,
          lineHeight: lineHeight || n(currentTheme.lineHeight, 1.35),
          letterSpacing: n(currentTheme.letterSpacing, 0),
          color: normalizeColor(color),
          textAlign: currentTheme.textAlign || 'left'
        };
      };
      const emptyStyle = function(extra) {
        const style = Object.assign({
          width: null,
          height: null,
          minWidth: null,
          minHeight: null,
          maxWidth: null,
          maxHeight: null,
          padding: edge(0, 0, 0, 0),
          margin: edge(0, 0, 0, 0),
          alignment: null,
          spacing: 0,
          position: { top: null, right: null, bottom: null, left: null },
          backgroundColor: '#00000000',
          borderRadius: 0,
          border: border(0, '#00000000'),
          borderWidth: 0,
          borderColor: '#00000000',
          shadow: null,
          opacity: 1
        }, extra || {});
        if (style.border && typeof style.border === 'object') {
          style.borderWidth = style.border.width;
          style.borderColor = style.border.color;
        }
        return style;
      };
      const node = function(type, id, properties, style, children) {
        const props = Object.assign({ visibility: true, opacity: 1 }, properties || {});
        const resolvedStyle = emptyStyle(style);
        ['width', 'height', 'minWidth', 'minHeight', 'maxWidth', 'maxHeight', 'alignment', 'spacing', 'position'].forEach(function(key) {
          if (typeof props[key] !== 'undefined' && props[key] !== null && (resolvedStyle[key] === null || resolvedStyle[key] === 0 || key === 'position')) {
            resolvedStyle[key] = props[key];
          }
        });
        if (typeof props.opacity !== 'undefined') resolvedStyle.opacity = props.opacity;
        return {
          type: type,
          id: id,
          properties: props,
          style: resolvedStyle,
          children: children || []
        };
      };
      const iconNode = function(id, name, color, size) {
        return node('icon', id, { name: name || 'gauge', size: size || n(currentTheme.iconSize, 20) }, { color: color || currentTheme.iconColor }, []);
      };
      const textNode = function(id, text, style, maxLines, extraProps) {
        return node('text', id, Object.assign({ text: text, maxLines: maxLines || 1, overflow: 'ellipsis' }, extraProps || {}), Object.assign({ textStyle: style }, {
          fontFamily: style.fontFamily,
          fontSize: style.fontSize,
          fontWeight: style.fontWeight,
          letterSpacing: style.letterSpacing,
          lineHeight: style.lineHeight,
          textAlign: style.textAlign,
          textColor: style.color,
          color: style.color
        }), []);
      };
      const buttonNode = function(id, semanticLabel, events, style, children) {
        return node('button', id, { label: semanticLabel, semanticLabel: semanticLabel, events: events || [] }, style, children || []);
      };
      const action = function(event, type, value, payload) {
        const output = { event: event, action: { type: type, value: value } };
        if (payload) output.action.payload = payload;
        return output;
      };

      const pagePadding = n(currentTheme.pagePadding, 18);
      const cardPadding = n(currentTheme.cardPadding, 16);
      const cardGap = n(currentTheme.cardSpacing, 14);
      const radius = n(currentTheme.borderRadius, 18);
      const borderWidth = n(currentTheme.borderSize, 1);
      const shadowAlpha = Math.max(0, Math.min(100, n(currentTheme.shadowStrength, 70))) / 1000;
      const modelTitle = modelNo || 'Linked UI';
      const linkedPagesText = pageData.length + ' linked page' + (pageData.length === 1 ? '' : 's');

      const controlsBundle = buildControlsSchema();
      const iconOverrides = buildIconOverridesSchema(controlsBundle.controls);
      const usedIconNames = collectUsedIconNames(controlsBundle.controls);
      const deviceParameters = buildDeviceParametersState(controlsBundle.controls);
      const navigationPages = controlsBundle.pages.map(function(page) {
        const pageControls = controlsBundle.controls.filter(function(control) {
          return String(control.pageNo) === String(page.pageNo);
        });
        const pageSubtitle = 'Page ' + page.pageNo + '.Operation';
        return {
          id: page.id,
          pageNo: page.pageNo,
          title: page.title,
          index: page.index,
          widgetId: 'page_slide_' + page.index,
          controlIds: page.controlIds,
          controlCount: pageControls.length,
          subtitle: pageSubtitle,
          heroSummary: pageControls.length + ' controls linked to Page ' + page.pageNo
        };
      });
      const statePages = navigationPages.map(function(page) {
        return {
          id: page.id,
          pageNo: page.pageNo,
          title: page.title,
          index: page.index,
          subtitle: page.subtitle,
          controlCount: page.controlCount,
          heroSummary: page.heroSummary
        };
      });

      const buildUiTreeParameterCardNode = function(control, pageIndex, controlIndex) {
        const binding = control.binding;
        const componentType = control.componentType || control.controlType || 'value';
        const styles = control.styles || {};
        const variant = control.variant || controlSchemaVariant(componentType);
        const stacked = isStackedParamLayout(componentType);
        const events = (control.events && control.events.length)
          ? control.events
          : ((control.component && control.component.events) || []);
        const flutterWidget = control.flutterWidget
          || (control.component && control.component.flutterWidget)
          || 'Text';
        const componentTree = control.component && control.component.widgetTree
          ? control.component.widgetTree
          : null;
        const children = componentTree && Array.isArray(componentTree.children)
          ? componentTree.children
          : [];
        const gaugeProps = (control.component && control.component.gauge)
          || (control.rightAccessory && control.rightAccessory.gauge)
          || null;
        const styleOverrides = (control.component && control.component.styleOverrides) || {};
        const cardId = 'param_card_' + pageIndex + '_' + controlIndex + '_' + slug(control.id || control.parameterKey || 'control', 'c');

        return node('ParameterCard', cardId, {
          controlId: control.id,
          parameterKey: control.parameterKey,
          parameterName: control.parameterName || control.parameter || control.label,
          pageNo: control.pageNo,
          pageId: control.pageId,
          sequence: control.sequence,
          componentType: componentType,
          controlType: componentType,
          variant: variant,
          flutterWidget: flutterWidget,
          renderAs: 'ParameterCard',
          layout: stacked ? 'stacked' : 'row',
          binding: binding,
          valueBinding: binding,
          textBinding: binding,
          useLiveBinding: true,
          label: control.label,
          icon: control.icon,
          metaIcon: control.metaIcon,
          action: control.action,
          events: events,
          visibility: control.visible !== false,
          visible: control.visible !== false,
          rightAccessory: control.rightAccessory || null,
          options: control.options || null,
          constraints: control.constraints || null,
          gauge: gaugeProps,
          props: Object.assign({}, (control.component && control.component.props) || {}, {
            label: control.label,
            icon: control.icon,
            variant: componentType,
            layout: stacked ? 'stacked' : 'row',
            valueBinding: binding,
            textBinding: binding,
            useLiveBinding: true,
            metaIcon: control.metaIcon
          }),
          styleOverrides: styleOverrides,
          override: control.override || {},
          initialValue: control.component ? control.component.initialValue : control.initialValue,
          component: control.component || null
        }, {
          backgroundColor: styles.cardColor || currentTheme.cardColor,
          borderColor: styles.borderColor || currentTheme.borderColor,
          borderWidth: borderWidth,
          borderRadius: radius,
          padding: edge(cardPadding, cardPadding, cardPadding, cardPadding),
          margin: edge(0, 0, cardGap, 0),
          minHeight: 66,
          textColor: styles.textColor || currentTheme.textColor,
          mutedTextColor: styles.mutedTextColor || currentTheme.mutedTextColor,
          accentColor: styles.accentColor || currentTheme.accentColor,
          iconColor: styles.iconColor || currentTheme.iconColor || currentTheme.accentColor,
          statusColor: styles.statusColor || currentTheme.accentColor,
          shadow: shadow(12, 28, 'rgba(18,50,70,' + shadowAlpha.toFixed(2) + ')'),
          leftAccent: styles.leftAccent || {
            width: 3,
            colorStart: currentTheme.secondaryColor,
            colorEnd: styles.accentColor || currentTheme.accentColor
          },
          iconBubble: styles.iconBubble || null,
          overflow: stacked ? 'visible' : 'hidden'
        }, children);
      };

      const pageNodes = Array.from(document.querySelectorAll('.page-slide')).map(function(slide, pageIndex) {
        const pageNo = slide.dataset.pageNo || String(pageIndex + 1);
        const pageName = slide.dataset.pageTitle || ('Page ' + pageNo);
        const heroTitle = slide.querySelector('.slide-hero h2');
        const displayedHeroTitle = (currentTheme.headerText && String(currentTheme.headerText).trim())
          ? String(currentTheme.headerText).trim()
          : (heroTitle ? heroTitle.textContent.trim() : pageName);
        const pageControls = controlsBundle.controls.filter(function(control) {
          return String(control.pageNo) === String(pageNo);
        });
        const visiblePageControls = pageControls.filter(function(control) {
          return control.visible !== false;
        });
        const parameterCardNodes = visiblePageControls.map(function(control, controlIndex) {
          return buildUiTreeParameterCardNode(control, pageIndex, controlIndex);
        });

        return node('column', 'page_slide_' + pageIndex, {
          width: 'matchParent',
          crossAxisAlignment: 'stretch',
          pageId: 'page_' + pageNo,
          pageNo: pageNo,
          pageName: pageName,
          isActive: pageIndex === activeIndex,
          visibility: true
        }, {
          padding: edge(pagePadding, pagePadding, 4, pagePadding)
        }, [
          node('stack', 'slide_hero_' + pageIndex, { minHeight: 112, clipBehavior: 'hardEdge', visibility: bool(currentTheme.heroVisible) }, {
            padding: edge(18, 18, 18, 18),
            margin: edge(0, 0, 14, 0),
            backgroundColor: currentTheme.topbarColor,
            gradient: { type: 'linear', begin: 'topLeft', end: 'bottomRight', colors: [currentTheme.topbarColor, currentTheme.secondaryColor] },
            borderRadius: radius + 6,
            shadow: shadow(18, 38, 'rgba(18,50,70,0.16)')
          }, [
            node('container', 'hero_ring_' + pageIndex, { width: 112, height: 112, position: { top: -36, right: -36 } }, {
              borderRadius: 999,
              border: border(24, 'rgba(209,250,255,0.16)')
            }, []),
            node('row', 'slide_hero_row_' + pageIndex, { mainAxisAlignment: 'spaceBetween', crossAxisAlignment: 'end', spacing: 12 }, {}, [
              node('column', 'slide_hero_text_' + pageIndex, { width: 'expanded', crossAxisAlignment: 'start', spacing: 6 }, {}, [
                node('row', 'slide_hero_kicker_' + pageIndex, { spacing: 6, crossAxisAlignment: 'center' }, { margin: edge(0, 0, 8, 0) }, [
                  iconNode('slide_signal_icon_' + pageIndex, currentTheme.iconStyle || 'signal', 'rgba(255,255,255,0.72)', 14),
                  textNode('slide_kicker_text_' + pageIndex, 'Live Control Page', textStyle(11, 800, 'rgba(255,255,255,0.72)', 1.2), 1)
                ]),
                textNode('slide_title_' + pageIndex, displayedHeroTitle, textStyle(22, 900, '#FFFFFF', 1.1), 2, pageIndex === activeIndex ? { textBinding: 'state.activePageName' } : null),
                textNode('slide_subtitle_' + pageIndex, pageControls.length + ' controls linked to Page ' + pageNo, textStyle(12, 700, 'rgba(255,255,255,0.68)', 1.25), 1)
              ]),
              node('container', 'slide_count_' + pageIndex, { width: 58, height: 58, alignment: 'center' }, {
                backgroundColor: 'rgba(255,255,255,0.12)',
                borderRadius: 20,
                border: border(1, 'rgba(255,255,255,0.14)')
              }, [
                node('column', 'slide_count_column_' + pageIndex, { mainAxisAlignment: 'center', crossAxisAlignment: 'center', spacing: 4 }, {}, [
                  textNode('slide_count_value_' + pageIndex, String(pageIndex + 1), textStyle(20, 900, '#FFFFFF', 1), 1),
                  textNode('slide_count_label_' + pageIndex, 'Page', textStyle(10, 800, 'rgba(255,255,255,0.68)', 1), 1)
                ])
              ])
            ])
          ]),
          node('card', 'name_card_' + pageIndex, { minHeight: 50, visibility: bool(currentTheme.nameCardVisible) }, {
            padding: edge(cardPadding, cardPadding, cardPadding, cardPadding),
            backgroundColor: currentTheme.cardColor,
            borderRadius: radius,
            border: border(borderWidth, currentTheme.borderColor),
            shadow: shadow(12, 28, 'rgba(18,50,70,' + shadowAlpha.toFixed(2) + ')')
          }, [
            node('row', 'name_card_row_' + pageIndex, { mainAxisAlignment: 'spaceBetween', crossAxisAlignment: 'center', spacing: 10 }, {}, [
              node('column', 'name_card_text_' + pageIndex, { width: 'expanded', crossAxisAlignment: 'start', spacing: 5 }, {}, [
                textNode('name_card_label_' + pageIndex, 'name', textStyle(11, 800, currentTheme.mutedTextColor, 1), 1),
                textNode('name_card_value_' + pageIndex, pageName, textStyle(16, 700, currentTheme.textColor, 1.25), 2, pageIndex === activeIndex ? { textBinding: 'state.activePageName' } : null)
              ]),
              buttonNode('edit_page_button_' + pageIndex, 'Edit', [
                action('onTap', 'navigation', editPageLinkingUrl, { modalId: modalId, modelNo: modelNo })
              ], { backgroundColor: '#00000000', padding: edge(4, 6, 4, 6) }, [
                textNode('edit_page_text_' + pageIndex, 'Edit', textStyle(12, 800, currentTheme.accentColor, 1.2), 1)
              ])
            ])
          ]),
          node('schemaControlList', 'param_list_' + pageIndex, {
            renderMode: 'inlineParameterCards',
            source: 'controls',
            pageNo: pageNo,
            pageId: 'page_' + pageNo,
            controlIds: pageControls.map(function(control) { return control.id; }),
            controlCount: pageControls.length,
            visibleControlCount: visiblePageControls.length,
            cardType: 'ParameterCard',
            width: 'matchParent',
            shrinkWrap: true,
            physics: 'neverScrollable',
            scrollDirection: 'vertical',
            spacing: cardGap,
            useLiveBinding: true,
            filterBy: 'pageNo'
          }, { margin: edge(16, 0, 0, 0) }, parameterCardNodes)
        ]);
      });

      const activePageMeta = navigationPages[activeIndex] || navigationPages[0] || null;
      const activePageName = activePageMeta ? activePageMeta.title : (modelNo || 'Model UI');
      const activePageNo = activePageMeta ? activePageMeta.pageNo : '';
      const activePageId = activePageMeta ? activePageMeta.id : null;
      const activeSubtitle = activePageNo ? ('Page ' + activePageNo + '.Operation') : 'No pages';
      const progressWidthPercent = navigationPages.length ? Math.round(((activeIndex + 1) / navigationPages.length) * 10000) / 100 : 0;

      const headerAlignment = currentTheme.headerAlignment || 'left';
      const topbarRowAlignment = headerAlignment === 'center' ? 'center' : (headerAlignment === 'right' ? 'end' : 'spaceBetween');
      const buttonBg = currentTheme.buttonBgColor || currentTheme.accentColor;
      const buttonTextColor = currentTheme.buttonTextColor || '#FFFFFF';
      const buttonLabel = currentTheme.buttonText || 'Save Changes';
      const buttonHeight = n(currentTheme.buttonSize, 48);
      const buttonRadius = n(currentTheme.buttonRadius, 16);

      const root = node('container', 'phone_shell', { width: 430, maxWidth: 430, height: 812, maxHeight: 812, clipBehavior: 'hardEdge' }, {
        backgroundColor: currentTheme.backgroundColor,
        gradient: currentTheme.backgroundType === 'gradient'
          ? { type: 'linear', begin: 'topLeft', end: 'bottomRight', colors: [currentTheme.backgroundColor, currentTheme.gradientColor || currentTheme.shellColor] }
          : null,
        margin: edge(0, 0, 0, 0),
        shadow: shadow(30, 80, 'rgba(34,40,60,0.18)')
      }, [
        node('column', 'phone_shell_column', { width: 'matchParent', height: 'matchParent', crossAxisAlignment: 'stretch' }, {}, [
          node('container', 'app_topbar', { minHeight: n(currentTheme.headerHeight, 84) }, {
            padding: edge(14, 16, 12, 16),
            backgroundColor: currentTheme.topbarColor,
            gradient: { type: 'linear', begin: 'topLeft', end: 'bottomRight', colors: [currentTheme.topbarColor, currentTheme.secondaryColor] },
            shadow: shadow(18, 38, 'rgba(18,50,70,0.16)')
          }, [
            node('row', 'app_topbar_row', { mainAxisAlignment: topbarRowAlignment, crossAxisAlignment: 'center', spacing: 10 }, {}, [
              buttonNode('phone_back_button', 'Back', [action('onTap', 'navigateBack', 'back')], {
                minWidth: 42,
                height: 42,
                padding: edge(0, 12, 0, 12),
                backgroundColor: 'rgba(255,255,255,0.10)',
                borderRadius: 999,
                border: border(1, 'rgba(255,255,255,0.12)')
              }, [
                iconNode('phone_back_icon', 'chevron-left', '#FFFFFF', 17),
                textNode('phone_back_text', 'Back', textStyle(12, 900, '#FFFFFF', 1), 1)
              ]),
              node('column', 'top_title', { width: 'expanded', crossAxisAlignment: 'start', spacing: 2 }, {}, [
                textNode('active_page_title', activePageName, textStyle(18, 800, '#FFFFFF', 1.2), 1, { textBinding: 'state.activePageName' }),
                textNode('model_subtitle', modelNo || 'Model UI', textStyle(12, 600, 'rgba(255,255,255,0.68)', 1.25), 1),
                textNode('active_page_subtitle', activeSubtitle, textStyle(12, 600, 'rgba(255,255,255,0.68)', 1.25), 1, { textBinding: 'state.activePageSubtitle' })
              ]),
              node('row', 'top_actions', { spacing: 4, visibility: bool(currentTheme.showHeaderIcons) }, {}, [
                buttonNode('device_info_button', 'Information', [action('onTap', 'openDeviceInfo', 'openDeviceInfo')], {
                  width: 42,
                  height: 42,
                  backgroundColor: 'rgba(255,255,255,0.10)',
                  borderRadius: 999,
                  border: border(1, 'rgba(255,255,255,0.12)')
                }, [iconNode('device_info_icon', 'info', '#FFFFFF', 20)]),
                buttonNode('edit_ui_button', 'Edit UI', [action('onTap', 'navigation', editPageLinkingUrl, { modalId: modalId, modelNo: modelNo })], {
                  width: 42,
                  height: 42,
                  backgroundColor: 'rgba(255,255,255,0.10)',
                  borderRadius: 999,
                  border: border(1, 'rgba(255,255,255,0.12)')
                }, [iconNode('edit_ui_icon', 'settings', '#FFFFFF', 20)])
              ])
            ])
          ]),
          node('card', 'brand_strip', { visibility: bool(currentTheme.brandVisible) }, {
            margin: edge(16, 18, 2, 18),
            padding: edge(cardPadding, cardPadding, cardPadding, cardPadding),
            backgroundColor: currentTheme.cardColor,
            gradient: { type: 'linear', begin: 'topLeft', end: 'bottomRight', colors: [currentTheme.cardColor, currentTheme.shellColor] },
            borderRadius: radius + 2,
            border: border(borderWidth, currentTheme.borderColor),
            shadow: shadow(12, 28, 'rgba(18,50,70,' + shadowAlpha.toFixed(2) + ')')
          }, [
            node('row', 'brand_strip_row', { mainAxisAlignment: 'spaceBetween', crossAxisAlignment: 'center', spacing: 12 }, {}, [
              node('container', 'brand_mark', { minWidth: 78, height: 32, alignment: 'center' }, {
                padding: edge(0, 10, 0, 10),
                backgroundColor: currentTheme.cardColor,
                borderRadius: 8,
                border: border(2, currentTheme.topbarColor),
                shadow: shadow(7, 14, 'rgba(34,40,60,0.16)')
              }, [textNode('brand_mark_text', 'Efactor', textStyle(11, 900, currentTheme.topbarColor, 1), 1)]),
              node('column', 'brand_copy', { width: 'expanded', crossAxisAlignment: 'start', spacing: 2 }, {}, [
                textNode('brand_model', modelTitle, textStyle(14, 900, currentTheme.textColor, 1.2), 1),
                textNode('brand_pages', linkedPagesText, textStyle(11, 700, currentTheme.mutedTextColor, 1.2), 1)
              ]),
              node('row', 'online_state', { spacing: 7, visibility: bool(currentTheme.showStatus) }, {}, [
                node('container', 'online_dot', {
                  width: 8,
                  height: 8,
                  stateBinding: 'state.online'
                }, {
                  backgroundColor: currentTheme.successColor || currentTheme.accentColor,
                  borderRadius: 999,
                  shadow: { offsetX: 0, offsetY: 0, blurRadius: 0, spreadRadius: 5, color: 'rgba(4,147,66,0.12)' }
                }, []),
                textNode('online_text', 'Online', textStyle(12, 900, currentTheme.successColor || currentTheme.accentColor, 1), 1, {
                  textBinding: 'state.onlineLabel'
                })
              ])
            ])
          ]),
          node('container', 'empty_ui', { visibility: pageNodes.length === 0, alignment: 'center' }, {
            margin: edge(pagePadding, pagePadding, pagePadding, pagePadding),
            padding: edge(cardPadding, cardPadding, cardPadding, cardPadding),
            backgroundColor: currentTheme.cardColor,
            borderRadius: radius,
            border: border(borderWidth, currentTheme.borderColor),
            shadow: shadow(12, 28, 'rgba(18,50,70,' + shadowAlpha.toFixed(2) + ')')
          }, [textNode('empty_ui_text', 'No UI parameters selected yet.', textStyle(n(currentTheme.fontSize, 14), n(currentTheme.fontWeight, 700), currentTheme.textColor, n(currentTheme.lineHeight, 1.35)), 2)]),
          node('column', 'slider_shell', { visibility: pageNodes.length > 0, crossAxisAlignment: 'stretch', flex: 1, scrollable: true }, {
            padding: edge(4, 0, 18, 0)
          }, [
            node('listView', 'page_slider', {
              scrollDirection: 'horizontal',
              physics: 'page',
              activeIndexBinding: 'state.activePageIndex',
              itemExtent: '100%'
            }, {}, pageNodes),
            node('container', 'progress_shell', { height: 5, alignment: 'centerLeft', visibility: bool(currentTheme.pageNavigatorVisible) }, {
              margin: edge(0, 18, 8, 18),
              backgroundColor: 'rgba(106,142,174,0.18)',
              borderRadius: 999
            }, [
              node('container', 'progress_bar', {
                width: progressWidthPercent + '%',
                widthPercent: progressWidthPercent,
                widthBinding: 'state.pageProgressPercent',
                height: 'matchParent'
              }, {
                backgroundColor: currentTheme.accentColor,
                gradient: { type: 'linear', begin: 'centerLeft', end: 'centerRight', colors: [currentTheme.secondaryColor, currentTheme.accentColor] },
                borderRadius: 999
              }, [])
            ]),
            node('card', 'slider_controls', { visibility: bool(currentTheme.pageNavigatorVisible) }, {
              margin: edge(6, 18, 0, 18),
              padding: edge(12, 12, 12, 12),
              backgroundColor: 'rgba(255,255,255,0.72)',
              borderRadius: 20,
              border: border(1, 'rgba(106,142,174,0.28)'),
              shadow: shadow(12, 28, 'rgba(18,50,70,' + shadowAlpha.toFixed(2) + ')')
            }, [
              node('row', 'slider_controls_row', { mainAxisAlignment: 'spaceBetween', crossAxisAlignment: 'center', spacing: 10 }, {}, [
                buttonNode('prev_slide_button', 'Previous page', [action('onTap', 'update', 'activePageIndex=max(activePageIndex-1,0)')], {
                  width: 44,
                  height: 44,
                  backgroundColor: currentTheme.topbarColor,
                  borderRadius: 999,
                  border: border(1, 'rgba(106,142,174,0.28)')
                }, [iconNode('prev_slide_icon', 'arrow-left', '#FFFFFF', 20)]),
                node('listView', 'page_buttons', { width: 'expanded', scrollDirection: 'horizontal', spacing: 8 }, {}, pageNodes.map(function(_, index) {
                  return buttonNode('page_button_' + index, 'Page ' + (index + 1), [action('onTap', 'update', 'activePageIndex=' + index)], {
                    minWidth: 34,
                    height: 34,
                    padding: edge(0, 10, 0, 10),
                    margin: edge(0, 8, 0, 0),
                    backgroundColor: index === activeIndex ? currentTheme.accentColor : currentTheme.cardColor,
                    gradient: index === activeIndex ? { type: 'linear', begin: 'topLeft', end: 'bottomRight', colors: [currentTheme.secondaryColor, currentTheme.accentColor] } : null,
                    borderRadius: 999,
                    border: border(1, index === activeIndex ? currentTheme.accentColor : 'rgba(106,142,174,0.28)')
                  }, [textNode('page_button_text_' + index, String(navigationPages[index] ? navigationPages[index].pageNo : (index + 1)), textStyle(13, 900, index === activeIndex ? '#FFFFFF' : currentTheme.textColor, 1), 1)]);
                })),
                buttonNode('next_slide_button', 'Next page', [action('onTap', 'update', 'activePageIndex=min(activePageIndex+1,pages.length-1)')], {
                  width: 44,
                  height: 44,
                  backgroundColor: currentTheme.topbarColor,
                  borderRadius: 999,
                  border: border(1, 'rgba(106,142,174,0.28)')
                }, [iconNode('next_slide_icon', 'arrow-right', '#FFFFFF', 20)])
              ])
            ])
          ]),
          node('container', 'footer_actions', { visibility: bool(currentTheme.footerVisible) }, {
            padding: edge(12, 16, 20, 16),
            backgroundColor: 'rgba(255,255,255,0.88)'
          }, [
            buttonNode('save_ui_button', buttonLabel, [action('onTap', 'api', saveThemeUrl, { modalId: modalId, modelNo: modelNo, operation: 'saveTheme' })], {
              minHeight: buttonHeight,
              padding: edge(0, 16, 0, 16),
              backgroundColor: buttonBg,
              borderRadius: buttonRadius,
              shadow: shadow(14, 28, 'rgba(4,147,66,0.22)')
            }, [
              textNode('save_ui_button_text', buttonLabel, textStyle(14, 900, buttonTextColor, 1.2), 1)
            ])
          ])
        ])
      ]);

      const activePageControls = controlsBundle.controls.filter(function(control) {
        return activePageNo && String(control.pageNo) === String(activePageNo);
      });

      const schema = {
        schemaVersion: '4.0.0',
        schemaType: 'device-ui',
        generatedAt: new Date().toISOString(),
        rendering: {
          layout: 'ui.widgetTree',
          controls: 'controls',
          mode: STAC_RENDERING_DEFAULTS.mode,
          engine: STAC_RENDERING_DEFAULTS.engine,
          stac: Object.assign({}, STAC_RENDERING_DEFAULTS.stac, {
            localProjectId: 'efdeflt_' + slug(modelNo, 'dynamic_ui_local')
          }),
          paramListNode: {
            type: 'schemaControlList',
            renderMode: 'inlineParameterCards',
            filterBy: 'pageNo',
            cardType: 'ParameterCard',
            childrenAreParameterCards: true,
            resolveBindingsFrom: 'state.device.parameters',
            layout: {
              switch: 'row',
              slider: 'stacked',
              powerGauge: 'stacked',
              needleGauge: 'stacked',
              segmented: 'stacked',
              linearGauge: 'stacked',
              button: 'row',
              elevatedButton: 'row',
              value: 'row',
              dropdown: 'row',
              textField: 'row',
              auto: 'row'
            },
            instructions: [
              'Render each schemaControlList child as an interactive ParameterCard.',
              'Bind ParameterCard.valueBinding / textBinding to state paths under device.parameters.',
              'Dispatch ParameterCard.events and control.action for writes/commands.',
              'Apply ParameterCard.style (card/border/text/accent/icon colors) from theme and per-control overrides.'
            ]
          },
          inlineComponentContract: INLINE_COMPONENT_CONTRACT
        },
        meta: {
          app: {
            id: 'efactor_' + slug(modelNo, 'device_ui'),
            name: (modelNo || 'Efactor Device') + ' Control',
            modelNo: modelNo,
            modalId: modalId,
            entryWidgetId: 'phone_shell'
          },
          generatedFromActivePageIndex: activeIndex,
          generatedFromActivePageNo: activePageNo,
          generatedFromActivePageControlCount: activePageControls.length,
          totalControlCount: controlsBundle.controls.length,
          links: {
            schema: schemaApiUrl,
            parameters: parametersApiUrl,
            deviceInfo: deviceInfoUrl,
            editPageLinking: editPageLinkingUrl
          }
        },
        theme: {
          mode: currentTheme.themeMode || 'light',
          backgroundType: currentTheme.backgroundType || 'gradient',
          deviceMode: currentTheme.deviceMode || 'mobile',
          colors: {
            background: currentTheme.backgroundColor,
            gradientEnd: currentTheme.gradientColor || currentTheme.shellColor,
            shell: currentTheme.shellColor,
            surface: currentTheme.cardColor,
            card: currentTheme.cardColor,
            text: currentTheme.textColor,
            mutedText: currentTheme.mutedTextColor,
            accent: currentTheme.accentColor,
            primary: currentTheme.accentColor,
            secondary: currentTheme.secondaryColor,
            success: currentTheme.successColor,
            warning: currentTheme.warningColor,
            error: currentTheme.errorColor,
            border: currentTheme.borderColor,
            topbar: currentTheme.topbarColor,
            toggleOn: currentTheme.toggleOnColor,
            toggleOff: currentTheme.toggleOffColor,
            buttonBg: currentTheme.buttonBgColor || currentTheme.accentColor,
            buttonText: currentTheme.buttonTextColor || '#FFFFFF',
            icon: currentTheme.iconColor || currentTheme.accentColor,
            online: currentTheme.successColor || currentTheme.accentColor
          },
          spacing: {
            pagePadding: pagePadding,
            cardPadding: cardPadding,
            cardGap: cardGap,
            cardMargin: n(currentTheme.cardMargin, 0)
          },
          typography: {
            fontFamily: currentTheme.fontFamily,
            bodySize: n(currentTheme.fontSize, 14),
            bodyWeight: n(currentTheme.fontWeight, 700),
            letterSpacing: n(currentTheme.letterSpacing, 0),
            lineHeight: n(currentTheme.lineHeight, 1.35),
            textAlign: currentTheme.textAlign || 'left',
            fontAsset: fontAssetForFamily(currentTheme.fontFamily)
          },
          radius: {
            card: radius,
            button: n(currentTheme.buttonRadius, 16),
            pill: 999
          },
          borders: {
            size: borderWidth,
            color: currentTheme.borderColor
          },
          shadows: {
            strength: n(currentTheme.shadowStrength, 70),
            card: shadow(12, 28, 'rgba(18,50,70,' + shadowAlpha.toFixed(2) + ')')
          },
          effects: {
            cardBlur: n(currentTheme.cardBlur, 0)
          },
          tokens: {
            backgroundColor: currentTheme.backgroundColor,
            gradientColor: currentTheme.gradientColor,
            shellColor: currentTheme.shellColor,
            cardColor: currentTheme.cardColor,
            textColor: currentTheme.textColor,
            mutedTextColor: currentTheme.mutedTextColor,
            accentColor: currentTheme.accentColor,
            secondaryColor: currentTheme.secondaryColor,
            topbarColor: currentTheme.topbarColor,
            borderColor: currentTheme.borderColor,
            iconColor: currentTheme.iconColor,
            successColor: currentTheme.successColor,
            warningColor: currentTheme.warningColor,
            errorColor: currentTheme.errorColor,
            toggleOnColor: currentTheme.toggleOnColor,
            toggleOffColor: currentTheme.toggleOffColor
          }
        },
        icons: {
          library: buildCompactIconLibrary(usedIconNames),
          default: currentTheme.parameterIcon || 'gauge',
          size: parseInt(currentTheme.iconSize || 20, 10),
          color: currentTheme.iconColor || currentTheme.accentColor || '#157145'
        },
        components: {
          contract: FLUTTER_COMPONENT_CONTRACT,
          catalog: getParameterComponentCatalog()
        },
        registerComponents: buildRegisterComponentsSchema(),
        controls: controlsBundle.controls,
        pages: navigationPages,
        state: {
          activePageIndex: activeIndex,
          activePageId: activePageId,
          activePageNo: activePageNo,
          activePageName: activePageName,
          activePageSubtitle: activeSubtitle,
          pageProgressPercent: progressWidthPercent,
          online: true,
          onlineLabel: 'Online',
          pages: statePages,
          device: {
            parameters: deviceParameters
          }
        },
        settings: {
          parameterIcon: currentTheme.parameterIcon || 'gauge',
          parameterIcons: iconOverrides,
          componentTypes: controlsBundle.componentTypeOverrides || {},
          componentOverrides: currentTheme.componentOverrides || {},
          controlOrder: currentTheme.controlOrder || [],
          visibility: {
            brandVisible: bool(currentTheme.brandVisible),
            heroVisible: bool(currentTheme.heroVisible),
            nameCardVisible: bool(currentTheme.nameCardVisible),
            footerVisible: bool(currentTheme.footerVisible),
            pageNavigatorVisible: bool(currentTheme.pageNavigatorVisible),
            showHeaderIcons: bool(currentTheme.showHeaderIcons),
            showStatus: bool(currentTheme.showStatus)
          },
          layout: {
            headerHeight: n(currentTheme.headerHeight, 84),
            headerAlignment: currentTheme.headerAlignment || 'left',
            headerText: currentTheme.headerText || '',
            buttonText: currentTheme.buttonText || 'Save Changes',
            buttonSize: n(currentTheme.buttonSize, 48),
            buttonRadius: n(currentTheme.buttonRadius, 16),
            buttonTarget: currentTheme.buttonTarget || 'save',
            buttonHover: currentTheme.buttonHover || 'lift',
            buttonIconPosition: currentTheme.buttonIconPosition || 'left'
          }
        },
        navigation: {
          type: 'pager',
          id: 'mainPager',
          initialIndex: activeIndex,
          activeIndex: activeIndex,
          initialPageId: activePageId,
          activePageId: activePageId,
          pageIds: navigationPages.map(function(page) { return page.id; }),
          pages: navigationPages,
          controls: {
            previousAction: 'previousPage',
            nextAction: 'nextPage',
            selectAction: 'selectPage'
          },
          stateBinding: 'state.activePageIndex'
        },
        actions: Object.assign({
          openDeviceInfo: action('onTap', 'api', deviceInfoUrl || ''),
          saveTheme: action('onTap', 'api', saveThemeUrl || '', { modalId: modalId, modelNo: modelNo, operation: 'saveTheme' }),
          openEditUi: action('onTap', 'navigation', editPageLinkingUrl || '', { modalId: modalId, modelNo: modelNo }),
          previousPage: action('onTap', 'update', 'activePageIndex=max(activePageIndex-1,0)'),
          nextPage: action('onTap', 'update', 'activePageIndex=min(activePageIndex+1,pages.length-1)'),
          selectPage: action('onTap', 'update', 'activePageIndex={pageIndex}')
        }, controlsBundle.controlActions || {}),
        mobileView: {
          device: {
            mode: currentTheme.deviceMode || 'mobile',
            previewWidth: 375,
            previewHeight: 812,
            rootClass: 'phone-shell'
          },
          shell: {
            backgroundType: currentTheme.backgroundType,
            backgroundColor: currentTheme.backgroundColor,
            gradientColor: currentTheme.gradientColor,
            screenColor: currentTheme.shellColor,
            pagePadding: pagePadding,
            width: 'min(100%, 430px)',
            height: '100dvh',
            maxHeight: '100dvh',
            overflow: 'hidden',
            scrollRegion: 'page-slide'
          },
          header: {
            height: n(currentTheme.headerHeight, 84),
            backgroundColor: currentTheme.topbarColor,
            alignment: headerAlignment,
            titleSource: 'state.activePageName',
            subtitlePattern: 'Page {pageNo}.Operation',
            showHeaderIcons: bool(currentTheme.showHeaderIcons),
            actions: [
              { id: 'info', icon: 'info', label: 'Information', action: 'openDeviceInfo' },
              { id: 'settings', icon: 'settings', label: 'Edit UI', action: 'openEditUi' }
            ]
          },
          brandStrip: {
            visible: bool(currentTheme.brandVisible),
            modelNo: modelNo,
            showStatus: bool(currentTheme.showStatus),
            statusText: 'Online'
          },
          pageContent: {
            heroVisible: bool(currentTheme.heroVisible),
            nameCardVisible: bool(currentTheme.nameCardVisible),
            pageNavigatorVisible: bool(currentTheme.pageNavigatorVisible),
            footerVisible: bool(currentTheme.footerVisible),
            headerText: currentTheme.headerText || ''
          },
          footer: {
            visible: bool(currentTheme.footerVisible),
            buttonText: buttonLabel,
            buttonHeight: buttonHeight,
            buttonRadius: buttonRadius,
            buttonBg: buttonBg,
            buttonTextColor: buttonTextColor,
            buttonTarget: currentTheme.buttonTarget || 'save',
            buttonHover: currentTheme.buttonHover || 'lift',
            buttonIconPosition: currentTheme.buttonIconPosition || 'left',
            action: 'saveTheme'
          }
        },
        extensions: {
          universalUi: null,
          notes: 'Primary Flutter contract is schema v4. extensions.universalUi contains the cross-platform neutral schema (v2.1) for HTML/Android/Swift/React Native renderers.'
        },
        ui: {
          widgetTree: root
        }
      };
      return resolveSchemaValues(schema);
    }

    function generateSchema() {
      try {
        syncThemeStateFromPreview();
        const schema = buildSduiSchema();
        if (window.EfactorSdui && typeof window.EfactorSdui.validateSemantic === 'function') {
          const report = window.EfactorSdui.validateSemantic(schema, { requireNativeRegistration: false });
          if (!report.valid) {
            console.warn('SDUI semantic validation errors:', report.errors);
            setStudioStatus('Schema generated with ' + report.errors.length + ' validation issue(s). See console.');
          } else if (report.warnings && report.warnings.length) {
            console.warn('SDUI semantic validation warnings:', report.warnings);
          }
        }
        if (schemaOutput) schemaOutput.value = JSON.stringify(schema, null, 2);
        return schema;
      } catch (error) {
        console.error('Schema generation failed:', error);
        if (schemaOutput) {
          schemaOutput.value = JSON.stringify({
            error: 'Schema generation failed',
            message: error && error.message ? error.message : String(error)
          }, null, 2);
        }
        setStudioStatus('Schema generation failed. Check the browser console for details.');
        return null;
      }
    }

    function buildSduiSchema() {
      if (!window.EfactorSdui || typeof window.EfactorSdui.buildDocument !== 'function') {
        throw new Error('EfactorSdui core library failed to load (assets/js/sdui/sdui-core.js).');
      }

      const controlsBundle = buildControlsSchema();
      const pagesMeta = controlsBundle.pages || [];
      const visibleControls = (controlsBundle.controls || []).filter(function(control) {
        return control.visible !== false;
      }).map(function(control) {
        const paramKey = String(control.parameterKey || control.id || '');
        const card = paramKey
          ? document.querySelector('.param-card[data-param-key="' + paramKey.replace(/"/g, '') + '"]')
          : null;
        const meta = card ? getEffectiveParamMeta(card) : {};
        return Object.assign({}, control, {
          meta: {
            command_id: meta.command_id || meta.commandId || null,
            function_code: meta.function_code || meta.functionCode || null
          },
          readOnly: !!(control.component && control.component.readOnly) || control.componentType === 'value' || control.componentType === 'linearGauge',
          interactive: control.componentType === 'powerGauge' || control.componentType === 'needleGauge'
        });
      });
      const usedIconNames = collectUsedIconNames(visibleControls);
      const fontFamily = currentTheme.fontFamily || 'Inter';
      const fontAsset = fontAssetForFamily(fontFamily) || ('assets/fonts/' + String(fontFamily).replace(/\s+/g, '') + '-Regular.ttf');
      const schemaDocsUrl = (window.location.origin || '') + '/assets/js/sdui/schema/server-driven-ui.schema.json';

      return window.EfactorSdui.buildDocument({
        theme: currentTheme,
        controls: visibleControls,
        pages: pagesMeta,
        modelNo: modelNo || 'Linked UI',
        modalId: modalId,
        activeIndex: typeof activeIndex === 'number' ? activeIndex : 0,
        locale: document.documentElement.lang || 'en-IN',
        origin: window.location.origin,
        fontAsset: fontAsset,
        parametersApiUrl: parametersApiUrl || '',
        saveThemeUrl: saveThemeUrl || '',
        deviceInfoUrl: deviceInfoUrl || '',
        schemaApiUrl: schemaApiUrl || '',
        schemaDocsUrl: schemaDocsUrl,
        usedIconNames: usedIconNames,
        arcStart: typeof POWER_GAUGE_ARC_START !== 'undefined' ? POWER_GAUGE_ARC_START : -210,
        arcSweep: typeof POWER_GAUGE_ARC_SWEEP !== 'undefined' ? POWER_GAUGE_ARC_SWEEP : 240
      });
    }

    function getPreviewZoomConfig(type) {
      const isStudio = type === 'studio';
      return {
        mount: isStudio ? studioPreviewMount : pagePreviewMount,
        frame: isStudio ? studioPreviewFrame : pagePreviewFrame,
        labelEl: isStudio ? studioPreviewZoomLabel : pagePreviewZoomLabel,
        inBtn: isStudio ? studioPreviewZoomInBtn : pagePreviewZoomInBtn,
        outBtn: isStudio ? studioPreviewZoomOutBtn : pagePreviewZoomOutBtn,
        fitBtn: isStudio ? studioPreviewZoomFitBtn : pagePreviewZoomFitBtn,
        state: previewZoomStates[type],
        isActive: function() {
          return isStudio
            ? document.body.classList.contains('studio-open')
            : !document.body.classList.contains('studio-open');
        }
      };
    }

    function getActivePreviewZoomConfig() {
      return document.body.classList.contains('studio-open')
        ? getPreviewZoomConfig('studio')
        : getPreviewZoomConfig('page');
    }

    function getPreviewFitScale(config) {
      if (!config.mount || !config.frame) return 1;
      config.frame.style.transform = '';
      config.frame.style.marginBottom = '';
      const mountHeight = config.mount.clientHeight;
      const frameHeight = config.frame.offsetHeight;
      if (!mountHeight || !frameHeight) return 1;
      return Math.min(1, (mountHeight - 12) / frameHeight);
    }

    function updatePreviewZoomUi(config, scale) {
      if (config.labelEl) {
        config.labelEl.textContent = Math.round(scale * 100) + '%';
      }
      if (config.outBtn) {
        config.outBtn.disabled = !config.state.autoFit && scale <= PREVIEW_ZOOM_MIN + 0.001;
      }
      if (config.inBtn) {
        config.inBtn.disabled = !config.state.autoFit && scale >= PREVIEW_ZOOM_MAX - 0.001;
      }
      if (config.fitBtn) {
        config.fitBtn.classList.toggle('is-active', config.state.autoFit);
      }
    }

    function applyPreviewZoom(config) {
      if (!config || !config.mount || !config.frame || !config.isActive()) return;
      let scale = config.state.autoFit ? getPreviewFitScale(config) : config.state.manualScale;
      if (!config.state.autoFit) {
        scale = Math.max(PREVIEW_ZOOM_MIN, Math.min(PREVIEW_ZOOM_MAX, scale));
        config.state.manualScale = scale;
      }
      const frameHeight = config.frame.offsetHeight;
      if (Math.abs(scale - 1) > 0.001) {
        config.frame.style.transform = 'scale(' + scale + ')';
        config.frame.style.marginBottom = ((frameHeight * scale) - frameHeight) + 'px';
      } else {
        config.frame.style.transform = '';
        config.frame.style.marginBottom = '';
      }
      updatePreviewZoomUi(config, scale);
    }

    function applyActivePreviewZoom() {
      applyPreviewZoom(getActivePreviewZoomConfig());
      syncSlideViewportWidth();
    }

    function zoomPreviewBy(config, delta) {
      if (!config || !config.isActive()) return;
      if (config.state.autoFit) {
        config.state.manualScale = getPreviewFitScale(config);
        config.state.autoFit = false;
      }
      config.state.manualScale = Math.max(
        PREVIEW_ZOOM_MIN,
        Math.min(PREVIEW_ZOOM_MAX, config.state.manualScale + delta)
      );
      applyPreviewZoom(config);
    }

    function zoomActivePreviewBy(delta) {
      zoomPreviewBy(getActivePreviewZoomConfig(), delta);
    }

    function resetPreviewZoomFit(config) {
      if (!config) return;
      config.state.autoFit = true;
      applyPreviewZoom(config);
    }

    function resetActivePreviewZoomFit() {
      resetPreviewZoomFit(getActivePreviewZoomConfig());
    }

    function bindPreviewZoomMount(config) {
      if (!config.mount) return;
      config.mount.addEventListener('wheel', function(event) {
        if (!config.isActive() || !event.ctrlKey) return;
        event.preventDefault();
        zoomPreviewBy(config, event.deltaY > 0 ? -PREVIEW_ZOOM_STEP : PREVIEW_ZOOM_STEP);
      }, { passive: false });
    }

    // Studio Controls
    function openStudio() {
      studio.classList.add('is-open');
      studioBackdrop.classList.add('is-open');
      document.body.classList.add('studio-open');
      const previewTarget = studioPreviewFrame || studioPreviewMount;
      if (phoneShell && previewTarget && phoneShell.parentNode !== previewTarget) {
        previewTarget.appendChild(phoneShell);
      }
      studio.setAttribute('aria-hidden', 'false');
      syncInputsFromTheme();
      applyTheme(currentTheme, { reinitializeComponents: true });
      activateStudioSection('studio-theme-section');
      renderOrderList();
      renderParametersButtons();
      renderIconLibrary('');
      generateSchema();
      previewZoomStates.studio.autoFit = true;
      previewZoomStates.studio.manualScale = 1;
      requestAnimationFrame(function() {
        applyActivePreviewZoom();
        syncSlideViewportWidth();
        requestAnimationFrame(function() {
          applyActivePreviewZoom();
          syncSlideViewportWidth();
        });
      });
    }

    function closeStudio() {
      studio.classList.remove('is-open');
      studioBackdrop.classList.remove('is-open');
      document.body.classList.remove('studio-open');
      previewZoomStates.studio.autoFit = true;
      previewZoomStates.studio.manualScale = 1;
      if (studioPreviewFrame) {
        studioPreviewFrame.style.transform = '';
        studioPreviewFrame.style.marginBottom = '';
      }
      if (phoneShell && pagePreviewFrame && phoneShell.parentNode !== pagePreviewFrame) {
        pagePreviewFrame.appendChild(phoneShell);
      } else if (phoneShell && phoneHome.parentNode) {
        phoneHome.parentNode.insertBefore(phoneShell, phoneHome.nextSibling);
      }
      previewZoomStates.page.autoFit = false;
      previewZoomStates.page.manualScale = 1;
      requestAnimationFrame(function() {
        applyActivePreviewZoom();
        syncSlideViewportWidth();
      });
      studio.setAttribute('aria-hidden', 'true');
      closeIconPicker();
      closeComponentPicker();
    }

    let themeSchemaSyncTimer = null;

    function scheduleThemeSchemaSync() {
      window.clearTimeout(themeSchemaSyncTimer);
      themeSchemaSyncTimer = window.setTimeout(function() {
        generateSchema();
      }, 180);
    }

    // Theme Input Listeners
    themeInputs.forEach(function(input) {
      const updateThemeFromInput = function(commitSchema) {
        if (input.type === 'checkbox') {
          currentTheme[input.dataset.themeKey] = input.checked ? '1' : '0';
        } else if (input.type === 'range') {
          const numeric = parseRangeInputValue(input);
          input.value = String(numeric);
          currentTheme[input.dataset.themeKey] = numeric;
        } else {
          currentTheme[input.dataset.themeKey] = input.value;
        }
        updateRangeBadge(input);
        applyTheme(currentTheme);
        if (commitSchema) {
          window.clearTimeout(themeSchemaSyncTimer);
          generateSchema();
        } else {
          scheduleThemeSchemaSync();
        }
      };
      input.addEventListener('input', function() {
        updateThemeFromInput(false);
      });
      input.addEventListener('change', function() {
        updateThemeFromInput(true);
      });
    });

    const themePresets = {
      light: {
        themeMode: 'pro', backgroundType: 'gradient', backgroundColor: '#F8FAFC', gradientColor: '#EEF2F7',
        shellColor: '#F8FAFC', cardColor: '#FFFFFF', textColor: '#0F172A', mutedTextColor: '#64748B',
        topbarColor: '#0F172A', accentColor: '#2563EB', secondaryColor: '#94A3B8', buttonBgColor: '#2563EB',
        buttonTextColor: '#FFFFFF', iconColor: '#2563EB', borderColor: '#E2E8F0', toggleOnColor: '#2563EB',
        toggleOffColor: '#CBD5E1', fontFamily: 'Inter', fontSize: 15, fontWeight: 500, letterSpacing: -0.2,
        lineHeight: 1.5, borderRadius: 16, shadowStrength: 42, headerHeight: 72, cardSpacing: 12, pagePadding: 16
      },
      dark: {
        themeMode: 'dark', backgroundType: 'gradient', backgroundColor: '#0B1220', gradientColor: '#111827',
        shellColor: '#0F172A', cardColor: '#1E293B', textColor: '#F8FAFC', mutedTextColor: '#94A3B8',
        topbarColor: '#020617', accentColor: '#60A5FA', secondaryColor: '#64748B', buttonBgColor: '#2563EB',
        buttonTextColor: '#FFFFFF', iconColor: '#60A5FA', borderColor: '#334155', toggleOnColor: '#60A5FA', toggleOffColor: '#475569',
        fontFamily: 'Inter', fontWeight: 500, shadowStrength: 50
      },
      ocean: {
        themeMode: 'ocean', backgroundType: 'gradient', backgroundColor: '#EFF6FF', gradientColor: '#DBEAFE',
        shellColor: '#F8FAFC', cardColor: '#FFFFFF', textColor: '#0F172A', mutedTextColor: '#64748B',
        topbarColor: '#1E3A8A', accentColor: '#2563EB', secondaryColor: '#93C5FD', buttonBgColor: '#2563EB',
        buttonTextColor: '#FFFFFF', iconColor: '#2563EB', borderColor: '#BFDBFE', fontFamily: 'Inter', fontWeight: 500
      },
      nature: {
        themeMode: 'nature', backgroundType: 'gradient', backgroundColor: '#ECFDF5', gradientColor: '#D1FAE5',
        shellColor: '#F0FDF4', cardColor: '#FFFFFF', textColor: '#0F172A', mutedTextColor: '#64748B',
        topbarColor: '#064E3B', accentColor: '#2563EB', secondaryColor: '#6EE7B7', buttonBgColor: '#2563EB',
        buttonTextColor: '#FFFFFF', iconColor: '#2563EB', borderColor: '#A7F3D0', fontFamily: 'Inter', fontWeight: 500
      },
      glass: {
        themeMode: 'glass', backgroundType: 'gradient', backgroundColor: '#F8FAFC', gradientColor: '#E2E8F0',
        shellColor: '#F8FAFC', cardColor: 'rgba(255,255,255,0.82)', textColor: '#0F172A', mutedTextColor: '#64748B',
        topbarColor: '#1E293B', accentColor: '#475569', secondaryColor: '#94A3B8', buttonBgColor: '#334155',
        buttonTextColor: '#FFFFFF', iconColor: '#334155', borderColor: '#CBD5E1', cardBlur: 12, shadowStrength: 36,
        fontFamily: 'Inter', fontWeight: 500
      }
    };

    document.querySelectorAll('[data-preset]').forEach(function(button) {
      button.addEventListener('click', function() {
        currentTheme = Object.assign({}, currentTheme, themePresets[button.dataset.preset] || {});
        syncInputsFromTheme();
        applyTheme(currentTheme);
        generateSchema();
        setStudioStatus('Preset applied. Save changes to keep it.');
      });
    });

    document.querySelectorAll('[data-segment-key]').forEach(function(group) {
      group.addEventListener('click', function(event) {
        const button = event.target.closest('[data-segment-value]');
        if (!button) return;
        currentTheme[group.dataset.segmentKey] = button.dataset.segmentValue;
        syncInputsFromTheme();
        applyTheme(currentTheme);
        generateSchema();
      });
    });

    // Order List Drag and Drop
    if (orderList) {
      orderList.addEventListener('dragstart', function(event) {
        const row = event.target.closest('.order-row');
        if (!row) return;
        draggedOrderKey = row.dataset.orderKey;
        draggedOrderPageNo = row.dataset.pageNo || '';
        row.classList.add('is-dragging');
        event.dataTransfer.effectAllowed = 'move';
      });

      orderList.addEventListener('dragend', function(event) {
        const row = event.target.closest('.order-row');
        if (row) row.classList.remove('is-dragging');
        draggedOrderKey = '';
        draggedOrderPageNo = '';
      });

      orderList.addEventListener('dragover', function(event) {
        const row = event.target.closest('.order-row');
        if (!row || !draggedOrderKey || row.dataset.pageNo !== draggedOrderPageNo) return;
        event.preventDefault();
      });

      orderList.addEventListener('drop', function(event) {
        event.preventDefault();
        const targetRow = event.target.closest('.order-row');
        if (!targetRow || !draggedOrderKey || targetRow.dataset.orderKey === draggedOrderKey) return;
        if (targetRow.dataset.pageNo !== draggedOrderPageNo) return;

        const pageList = targetRow.parentElement;
        if (!pageList) return;
        const rows = Array.from(pageList.querySelectorAll('.order-row'));
        const fromIndex = rows.findIndex(function(row) { return row.dataset.orderKey === draggedOrderKey; });
        const toIndex = rows.findIndex(function(row) { return row.dataset.orderKey === targetRow.dataset.orderKey; });
        if (fromIndex < 0 || toIndex < 0 || fromIndex === toIndex) return;

        const moved = rows.splice(fromIndex, 1)[0];
        rows.splice(toIndex, 0, moved);
        rows.forEach(function(row) {
          pageList.appendChild(row);
        });
        rows.forEach(function(row, index) {
          const indexEl = row.querySelector('.order-row-index');
          if (indexEl) indexEl.textContent = String(index + 1);
        });

        rebuildControlOrderFromDom();
        applyControlOrder();
        generateSchema();
        setStudioStatus('Control order updated for Page ' + (targetRow.dataset.pageNo || draggedOrderPageNo) + '.');
      });
    }

    function activateStudioSection(sectionId, options) {
      options = options || {};
      document.querySelectorAll('.studio-controls > .studio-section, .studio-inspector > .studio-section').forEach(function(section) {
        section.classList.toggle('is-active', section.id === sectionId);
      });

      document.querySelectorAll('.studio-controls, .studio-inspector').forEach(function(panel) {
        panel.classList.toggle('has-active', Boolean(panel.querySelector('.studio-section.is-active')));
      });

      document.querySelectorAll('.studio-nav-btn[data-studio-target]').forEach(function(navButton) {
        navButton.classList.toggle('is-active', navButton.dataset.studioTarget === sectionId);
      });

      renderParametersButtons();
      renderIconLibrary(iconSearchInput ? iconSearchInput.value : '');
      if (sectionId === 'studio-schema-section' && modelNo) {
        persistGeneratedSchema({
          silent: !options.showSchemaToast,
          showToast: options.showSchemaToast === true,
          statusMessage: options.schemaStatusMessage || 'Device UI schema synced to API.'
        }).catch(function(error) {
          console.warn('Schema API sync failed:', error);
          setStudioStatus(error && error.message ? error.message : 'Schema displayed, but API sync failed.');
        });
      } else {
        generateSchema();
      }
    }

    document.querySelectorAll('.studio-nav-btn[data-studio-target]').forEach(function(button) {
      button.addEventListener('click', function() {
        activateStudioSection(button.dataset.studioTarget);
      });
    });

    // Icon Picker Outside Click
    document.addEventListener('click', function(event) {
      const iconTrigger = event.target.closest('[data-icon-picker-trigger]');
      if (iconTrigger) {
        event.preventDefault();
        activeIconCard = iconTrigger.closest('.param-card');
        activeIconTargetType = 'param';
        if (activeIconCard) {
          renderParamIconPicker(activeIconCard, 'param');
          openIconPicker(iconTrigger);
        }
        return;
      }

      const metaIconTrigger = event.target.closest('[data-meta-icon-picker-trigger]');
      if (metaIconTrigger) {
        event.preventDefault();
        activeIconCard = metaIconTrigger.closest('.param-card');
        activeIconTargetType = 'meta';
        if (activeIconCard) {
          renderParamIconPicker(activeIconCard, 'meta');
          openIconPicker(metaIconTrigger);
        }
        return;
      }

      if (iconPicker && iconPicker.classList.contains('is-open') && !event.target.closest('#param-icon-picker')) {
        closeIconPicker();
      }
      if (componentPicker && componentPicker.classList.contains('is-open') && !event.target.closest('#param-component-picker') && !event.target.closest('.param-component-edit')) {
        closeComponentPicker();
      }
    });

    function handleAppBackNavigation() {
      if (phoneShell && phoneShell.classList.contains('is-showing-info')) {
        closeDeviceInfoModal();
        setStudioStatus('Closed device info.');
        return;
      }
      if (slides.length && activeIndex > 0) {
        const previousIndex = activeIndex - 1;
        showSlide(previousIndex);
        const pageNo = slides[activeIndex] ? (slides[activeIndex].dataset.pageNo || String(activeIndex + 1)) : String(activeIndex + 1);
        setStudioStatus('Back to Page ' + pageNo + '.');
        return;
      }
      if (phoneShell) {
        phoneShell.classList.add('is-app-exit-pulse');
        window.setTimeout(function() {
          phoneShell.classList.remove('is-app-exit-pulse');
        }, 550);
      }
      setStudioStatus('App exit: back pressed on the first page.');
    }

    // Button Click Handlers
    bindClick('open-studio-btn', openStudio);
    bindClick('phone-back-btn', handleAppBackNavigation);
    bindClick('open-studio-footer-btn', openStudio);
    bindClick('close-studio-btn', closeStudio);
    bindClick('studio-preview-zoom-in', function() {
      zoomPreviewBy(getPreviewZoomConfig('studio'), PREVIEW_ZOOM_STEP);
    });
    bindClick('studio-preview-zoom-out', function() {
      zoomPreviewBy(getPreviewZoomConfig('studio'), -PREVIEW_ZOOM_STEP);
    });
    bindClick('studio-preview-zoom-fit', function() {
      resetPreviewZoomFit(getPreviewZoomConfig('studio'));
    });
    bindClick('page-preview-zoom-in', function() {
      zoomPreviewBy(getPreviewZoomConfig('page'), PREVIEW_ZOOM_STEP);
    });
    bindClick('page-preview-zoom-out', function() {
      zoomPreviewBy(getPreviewZoomConfig('page'), -PREVIEW_ZOOM_STEP);
    });
    bindClick('page-preview-zoom-fit', function() {
      resetPreviewZoomFit(getPreviewZoomConfig('page'));
    });
    window.addEventListener('resize', applyActivePreviewZoom);
    bindPreviewZoomMount(getPreviewZoomConfig('page'));
    bindPreviewZoomMount(getPreviewZoomConfig('studio'));
    requestAnimationFrame(function() {
      applyActivePreviewZoom();
      requestAnimationFrame(applyActivePreviewZoom);
    });
    bindClick('device-info-btn', openDeviceInfoFromSchema);
    bindClick('device-info-close-btn', closeDeviceInfoModal);
    if (deviceInfoModal) {
      deviceInfoModal.addEventListener('click', function(event) {
        if (event.target === deviceInfoModal) closeDeviceInfoModal();
      });
    }
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape' && deviceInfoModal && deviceInfoModal.classList.contains('is-open')) {
        closeDeviceInfoModal();
      }
    });
    bindClick('header-icon-picker-btn', function(event) {
      activeIconCard = null;
      activeIconTargetType = 'header';
      renderHeaderIconPicker('');
      openIconPicker(event.currentTarget);
    });

    function resetThemePreview() {
      currentTheme = Object.assign({}, defaultTheme);
      currentTheme.parameterIcons = {};
      currentTheme.componentOverrides = {};
      syncInputsFromTheme();
      applyTheme(currentTheme, { reinitializeComponents: true });
      generateSchema();
      setStudioStatus('Theme reset in preview. Save to keep it.');
    }

    bindClick('reset-theme-btn', resetThemePreview);
    bindClick('reset-theme-inline-btn', resetThemePreview);

    function generateSchemaFromButton() {
      activateStudioSection('studio-schema-section', {
        showSchemaToast: true,
        schemaStatusMessage: 'Readable device UI schema generated and synced to API.'
      });
    }

    bindClick('generate-schema-btn', generateSchemaFromButton);

    bindClick('copy-schema-btn', function() {
      generateSchema();
      if (!schemaOutput) return;
      const schemaText = schemaOutput.value || '';
      const onCopied = function() {
        setStudioStatus('Schema copied to clipboard.');
      };
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(schemaText).then(onCopied).catch(function() {
          schemaOutput.focus();
          schemaOutput.select();
          document.execCommand('copy');
          onCopied();
        });
      } else {
        schemaOutput.focus();
        schemaOutput.select();
        document.execCommand('copy');
        onCopied();
      }
    });

    bindClick('save-theme-btn', function() {
      const saveButton = document.getElementById('save-theme-btn');
      if (saveButton) saveButton.disabled = true;

      persistGeneratedSchema({
        showToast: true,
        statusMessage: 'UI created successfully.'
      })
      .then(function() {
        closeStudio();
      })
      .catch(function(error) {
        setStudioStatus(error && error.message ? error.message : 'Save failed. Please try again.');
      })
      .finally(function() {
        if (saveButton) saveButton.disabled = false;
      });
    });

    bindClick('save-ui-btn', function() {
      openStudio();
      setStudioStatus('Use Save Changes to store the device UI schema.');
    });

    // Initialization
    syncSlideViewportWidth();
    syncInputsFromTheme();
    applyTheme(currentTheme, { reinitializeComponents: true });
    requestAnimationFrame(function() {
      syncSlideViewportWidth();
    });
    const initialSchema = generateSchema();
    if (shouldSyncLinkedSchema && modelNo) {
      const syncData = new FormData();
      const syncTheme = buildThemePayloadForSave();
      syncData.append('modelno', modelNo);
      syncData.append('theme_json', JSON.stringify(syncTheme));
      syncData.append('schema_json', JSON.stringify(initialSchema));
      fetch(saveThemeUrl, { method: 'POST', body: syncData, credentials: 'same-origin' })
        .then(function(response) { return response.json(); })
        .then(function(result) {
          if (result && (result.success === true || result.success === 1 || result.success === '1')) {
            showSaveToast('Linked pages saved to schema.');
            if (window.history && window.history.replaceState) {
              const cleanUrl = new URL(window.location.href);
              cleanUrl.searchParams.delete('schema_sync');
              window.history.replaceState({}, document.title, cleanUrl.toString());
            }
          }
        })
        .catch(function() { showSaveToast('Linked pages saved, but schema sync failed.'); });
    }
  </script>
</body>
</html>
