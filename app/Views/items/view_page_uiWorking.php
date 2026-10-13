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
    'themeMode' => 'light',
    'backgroundType' => 'solid',
    'backgroundColor' => '#D1FAFF',
    'gradientColor' => '#F7FDFF',
    'shellColor' => '#F7FDFF',
    'cardColor' => '#FFFFFF',
    'textColor' => '#102A3A',
    'mutedTextColor' => '#5F7482',
    'topbarColor' => '#123246',
    'accentColor' => '#157145',
    'secondaryColor' => '#9BD1E5',
    'toggleOnColor' => '#157145',
    'toggleOffColor' => '#B8C8D0',
    'iconStyle' => 'signal',
    'parameterIcon' => 'gauge',
    'parameterIcons' => [],
    'fontFamily' => 'Poppins',
    'fontSize' => 14,
    'fontWeight' => 700,
    'letterSpacing' => 0,
    'lineHeight' => 1.35,
    'textAlign' => 'left',
    'buttonBgColor' => '#157145',
    'buttonTextColor' => '#FFFFFF',
    'successColor' => '#10B981',
    'warningColor' => '#F59E0B',
    'errorColor' => '#EF4444',
    'iconSize' => 20,
    'iconColor' => '#157145',
    'headerHeight' => 84,
    'headerText' => '',
    'headerAlignment' => 'left',
    'showHeaderIcons' => '1',
    'showStatus' => '1',
    'borderSize' => 1,
    'borderColor' => '#DCE8EF',
    'borderRadius' => 18,
    'shadowStrength' => 70,
    'cardBlur' => 0,
    'pagePadding' => 18,
    'cardPadding' => 16,
    'cardMargin' => 0,
    'cardSpacing' => 14,
    'buttonText' => 'Save Changes',
    'buttonSize' => 44,
    'buttonRadius' => 16,
    'buttonHover' => 'lift',
    'buttonIconPosition' => 'left',
    'buttonTarget' => 'save',
    'brandVisible' => '1',
    'heroVisible' => '1',
    'footerVisible' => '1',
    'deviceMode' => 'mobile',
    'pageType' => 'Control Page',
    'headerStyle' => 'Gradient',
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
            'info' => '<circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path>',
            'layers' => '<path d="m12 2 10 5-10 5L2 7l10-5z"></path><path d="m2 17 10 5 10-5"></path><path d="m2 12 10 5 10-5"></path>',
            'lightbulb' => '<path d="M15 14c.2-1 .7-1.7 1.4-2.5A6 6 0 1 0 7.6 11.5C8.3 12.3 8.8 13 9 14"></path><path d="M9 18h6"></path><path d="M10 22h4"></path><path d="M10 14h4v4h-4z"></path>',
            'lock' => '<rect width="18" height="11" x="3" y="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path>',
            'move' => '<path d="M12 2v20"></path><path d="m15 5-3-3-3 3"></path><path d="m15 19-3 3-3-3"></path><path d="M2 12h20"></path><path d="m5 9-3 3 3 3"></path><path d="m19 9 3 3-3 3"></path>',
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
      --ui-radius: 18px;
      --ui-shadow-alpha: .07;
      --ui-toggle-on: #157145;
      --ui-toggle-off: #B8C8D0;
      --ui-font: "Poppins", "Segoe UI", Arial, sans-serif;
      --ui-font-size: 14px;
      --ui-font-weight: 700;
      --ui-letter-spacing: 0px;
      --ui-line-height: 1.35;
      --ui-text-align: left;
      --ui-button-bg: #157145;
      --ui-button-text: #FFFFFF;
      --ui-success: #10B981;
      --ui-warning: #F59E0B;
      --ui-error: #EF4444;
      --ui-icon-size: 20px;
      --ui-icon-color: #157145;
      --ui-header-height: 84px;
      --ui-border-size: 1px;
      --ui-border-color: #DCE8EF;
      --ui-card-blur: 0px;
      --ui-page-padding: 18px;
      --ui-card-padding: 16px;
      --ui-card-spacing: 14px;
      --ui-button-height: 44px;
      --ui-button-radius: 16px;
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
      font-family: var(--ui-font);
      font-size: var(--ui-font-size);
      font-weight: var(--ui-font-weight);
      letter-spacing: var(--ui-letter-spacing);
      line-height: var(--ui-line-height);
      text-align: var(--ui-text-align);
    }

    .phone-shell {
      width: min(100%, 430px);
      min-height: 100vh;
      margin: 0 auto;
      background: var(--ui-bg);
      box-shadow: 0 30px 80px rgba(34, 40, 60, .18);
      overflow: hidden;
    }

    .app-topbar {
      z-index: 10;
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      gap: 10px;
      align-items: center;
      min-height: 84px;
      min-height: var(--ui-header-height);
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

    .back-btn {
      width: auto;
      min-width: 42px;
      height: 42px;
      padding: 0 12px;
      border-radius: 999px;
      gap: 6px;
      font-size: 12px;
      font-weight: 900;
    }

    .back-btn,
    .back-btn:hover,
    .back-btn:focus {
      width: auto;
      min-width: 42px;
      border-radius: 999px;
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
      padding: var(--ui-card-padding);
      border: var(--ui-border-size) solid var(--ui-border-color);
      border-radius: calc(var(--ui-radius) + 2px);
      background:
        linear-gradient(135deg, color-mix(in srgb, var(--ui-surface) 94%, transparent), color-mix(in srgb, var(--ui-blue) 20%, var(--ui-surface)));
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
      border-radius: calc(var(--ui-radius) + 6px);
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
      padding: var(--ui-page-padding) var(--ui-page-padding) 4px;
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
      background:
        linear-gradient(180deg, var(--ui-surface), color-mix(in srgb, var(--ui-shell) 28%, var(--ui-surface)));
      box-shadow: var(--ui-card-shadow);
      backdrop-filter: blur(var(--ui-card-blur));
      position: relative;
      overflow: hidden;
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
      width: 34px;
      height: 18px;
      border-radius: 999px;
      background: var(--ui-toggle-off);
    }

    .toggle-visual::after {
      content: "";
      position: absolute;
      top: -1px;
      right: 0;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: var(--ui-toggle-on);
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
      background: color-mix(in srgb, var(--ui-surface) 84%, transparent);
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
      grid-template-rows: 64px minmax(0, 1fr);
      width: 100%;
      background:
        linear-gradient(135deg, #F8FCFD 0%, #EEF8FB 48%, #F6FBF8 100%);
      color: var(--ui-text);
      overflow: hidden;
    }

    .ui-studio.is-open {
      display: grid;
    }

    .studio-topbar {
      display: grid;
      grid-template-columns: minmax(168px, auto) minmax(180px, 1fr) auto;
      gap: 16px;
      align-items: center;
      min-width: 0;
      padding: 10px 18px;
      border-bottom: 1px solid rgba(112, 143, 160, .22);
      background:
        linear-gradient(135deg, rgba(255, 255, 255, .96), rgba(239, 249, 251, .96));
      box-shadow: 0 12px 34px rgba(18, 50, 70, .1);
      position: relative;
    }

    .studio-topbar::after {
      content: "";
      position: absolute;
      left: 0;
      right: 0;
      bottom: -1px;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(21, 113, 69, .34), transparent);
    }

    .studio-title {
      min-width: 0;
      padding-left: 14px;
      border-left: 4px solid var(--ui-accent);
    }

    .studio-title h2 {
      margin: 0;
      font-size: 19px;
      line-height: 1.2;
      font-weight: 900;
      color: var(--ui-text);
    }

    .studio-title p {
      margin: 2px 0 0;
      color: var(--ui-muted);
      font-size: 12.5px;
      font-weight: 700;
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
      padding: 6px;
      border: 1px solid rgba(106, 142, 174, .2);
      border-radius: 16px;
      background: rgba(255, 255, 255, .72);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .82), 0 12px 26px rgba(18, 50, 70, .07);
    }

    .studio-workspace {
      display: grid;
      grid-template-columns: 280px minmax(420px, 1fr) 430px;
      grid-template-areas: "nav preview panel";
      min-height: 0;
      overflow: hidden;
    }

    .studio-sections,
    .studio-controls,
    .studio-inspector {
      min-height: 0;
      overflow: auto;
      border-right: 1px solid var(--ui-border);
      background: var(--ui-surface);
    }

    .studio-sections {
      grid-area: nav;
      padding: 22px 16px;
      background:
        linear-gradient(180deg, rgba(234, 247, 240, .88), rgba(255, 255, 255, .94) 42%),
        var(--ui-surface);
      box-shadow: inset -1px 0 0 rgba(106, 142, 174, .18);
    }

    .studio-section-kicker {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      margin: 0 8px 14px;
      padding: 6px 10px;
      border: 1px solid rgba(21, 113, 69, .14);
      border-radius: 999px;
      background: rgba(21, 113, 69, .07);
      color: var(--ui-muted);
      font-size: 11px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .06em;
    }

    .studio-section-kicker::before {
      content: "";
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: var(--ui-accent);
      box-shadow: 0 0 0 4px rgba(21, 113, 69, .12);
    }

    .studio-nav-btn {
      display: grid;
      grid-template-columns: 40px minmax(0, 1fr);
      gap: 12px;
      align-items: center;
      width: 100%;
      min-height: 66px;
      margin-bottom: 8px;
      padding: 10px 12px;
      border: 1px solid rgba(106, 142, 174, .18);
      border-radius: 18px;
      background: rgba(255, 255, 255, .58);
      color: var(--ui-text);
      text-align: left;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(18, 50, 70, .035);
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease, color .18s ease;
    }

    .studio-nav-btn.is-active,
    .studio-nav-btn:hover {
      border-color: rgba(21, 113, 69, .26);
      background:
        linear-gradient(135deg, rgba(21, 113, 69, .1), rgba(155, 209, 229, .18)),
        rgba(255, 255, 255, .9);
      color: var(--ui-accent);
      box-shadow: 0 16px 34px rgba(18, 50, 70, .09);
      transform: translateX(4px);
    }

    .studio-nav-btn.is-active::before {
      content: "";
      position: absolute;
      inset: 10px auto 10px 0;
      width: 4px;
      border-radius: 0 999px 999px 0;
      background: var(--ui-accent);
      box-shadow: 0 0 18px rgba(21, 113, 69, .36);
    }

    .studio-nav-btn > .icon {
      width: 42px;
      height: 42px;
      padding: 10px;
      border-radius: 14px;
      background: linear-gradient(135deg, #F8FCFD, #EAF7F0);
      color: #123246;
      box-shadow: inset 0 0 0 1px rgba(21, 113, 69, .14), 0 8px 16px rgba(18, 50, 70, .06);
    }

    .studio-nav-btn.is-active > .icon,
    .studio-nav-btn:hover > .icon {
      background: var(--ui-accent);
      color: #fff;
      box-shadow: 0 10px 22px color-mix(in srgb, var(--ui-accent) 22%, transparent);
    }

    .studio-nav-btn span {
      display: block;
      min-width: 0;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      font-size: 12px;
      font-weight: 900;
    }

    .studio-nav-btn small {
      display: block;
      margin-top: 2px;
      color: var(--ui-muted);
      font-size: 11px;
      font-weight: 700;
    }

    .studio-help {
      display: none;
    }

    .icon-search-box {
      width: 100% !important;
      min-height: 40px !important;
      padding: 10px 12px !important;
      font-size: 14px !important;
      margin-bottom: 12px !important;
      border: 2px solid var(--ui-border) !important;
      border-radius: 10px !important;
      background: linear-gradient(135deg, var(--ui-shell), var(--ui-surface)) !important;
      color: var(--ui-text) !important;
      transition: all .2s ease;
    }

    .icon-search-box:focus {
      border-color: var(--ui-accent) !important;
      outline: 0 !important;
      box-shadow: 0 0 0 3px color-mix(in srgb, var(--ui-accent) 16%, transparent) !important;
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
      background: linear-gradient(135deg, color-mix(in srgb, var(--ui-accent) 4%, var(--ui-surface)), var(--ui-surface));
      border: 1px solid color-mix(in srgb, var(--ui-accent) 12%, var(--ui-border));
      padding: 14px;
      margin: 0;
      border-radius: 16px;
      box-shadow: 0 16px 34px rgba(18, 50, 70, .07);
    }

    .param-editor-card h4 {
      margin: 0;
      font-size: 14px;
      font-weight: 900;
      background: linear-gradient(135deg, var(--ui-accent), color-mix(in srgb, var(--ui-accent) 80%, transparent));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .param-editor-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
      justify-content: flex-end;
    }

    #params-buttons-container .studio-btn {
      min-height: 40px;
      padding: 0 14px;
      border-color: var(--ui-accent) !important;
      background: color-mix(in srgb, var(--ui-accent) 8%, var(--ui-surface)) !important;
      color: var(--ui-accent) !important;
    }

    #params-buttons-container .studio-btn:hover {
      background: color-mix(in srgb, var(--ui-accent) 14%, var(--ui-surface)) !important;
    }

    .studio-section h3 {
      background: linear-gradient(135deg, var(--ui-text), var(--ui-muted));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .studio-controls {
      background:
        linear-gradient(180deg, color-mix(in srgb, var(--ui-accent) 5%, var(--ui-surface)), var(--ui-surface) 46%),
        var(--ui-surface);
    }

    .studio-field input:focus,
    .studio-field select:focus,
    .studio-field textarea:focus {
      border-color: var(--ui-accent) !important;
      box-shadow: inset 0 0 0 1px var(--ui-accent), 0 0 0 3px color-mix(in srgb, var(--ui-accent) 16%, transparent) !important;
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
      grid-area: panel;
      display: none;
      border-left: 1px solid rgba(106, 142, 174, .2);
      border-right: 0;
      padding: 22px;
      scroll-behavior: smooth;
    }

    .studio-controls.has-active,
    .studio-inspector.has-active {
      display: block;
    }

    .studio-preview-column {
      grid-area: preview;
      min-width: 0;
      min-height: 0;
      overflow: auto;
      display: grid;
      grid-template-rows: auto minmax(0, 1fr);
      justify-items: center;
      padding: 18px 18px 24px;
      background:
        radial-gradient(circle at 50% 0%, rgba(155, 209, 229, .26), transparent 32%),
        linear-gradient(180deg, #F7FDFF, #EAF7F0);
      border-right: 1px solid rgba(106, 142, 174, .2);
    }

    .studio-preview-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: min(100%, 430px);
      margin-bottom: 12px;
      color: var(--ui-text);
      font-size: 12px;
      font-weight: 900;
    }

    .studio-section h3 .studio-btn {
      background-clip: border-box;
      -webkit-background-clip: border-box;
      -webkit-text-fill-color: var(--ui-text);
      color: var(--ui-text);
    }

    .studio-preview-size {
      padding: 4px 9px;
      border: 1px solid var(--ui-border);
      border-radius: 8px;
      background: var(--ui-surface);
      color: var(--ui-muted);
      font-size: 11px;
    }

    .studio-inspector {
      background:
        linear-gradient(180deg, rgba(255, 255, 255, .96), rgba(248, 252, 253, .98));
      box-shadow: inset 1px 0 0 rgba(255, 255, 255, .78);
    }

    .studio-preview-mount {
      display: flex;
      align-items: flex-start;
      justify-content: center;
      width: 100%;
      min-height: 0;
    }

    body.studio-open {
      padding: 0;
    }

    body.studio-open .ui-studio .phone-shell {
      width: min(100%, 375px);
      min-height: 812px;
      margin: 0;
      border-radius: 26px;
      box-shadow: 0 24px 60px rgba(18, 50, 70, .18);
    }

    .studio-section {
      scroll-margin-top: 16px;
      padding: 0;
      margin-bottom: 18px;
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
      margin: 0 0 18px;
      color: var(--ui-text);
      font-size: 19px;
      font-weight: 900;
      letter-spacing: 0;
    }

    .studio-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
    }

    .studio-field {
      position: relative;
      display: grid;
      gap: 7px;
      min-width: 0;
      padding: 12px;
      border: 1px solid color-mix(in srgb, var(--ui-border) 78%, transparent);
      border-radius: 14px;
      background:
        linear-gradient(180deg, color-mix(in srgb, var(--ui-surface) 92%, #fff), color-mix(in srgb, var(--ui-shell) 34%, var(--ui-surface)));
      box-shadow: 0 10px 24px rgba(18, 50, 70, .045);
    }

    .studio-field.has-range {
      padding-right: 58px;
    }

    .studio-field.has-range::after {
      content: attr(data-range-value);
      position: absolute;
      right: 12px;
      top: 12px;
      min-width: 38px;
      padding: 4px 7px;
      border: 1px solid color-mix(in srgb, var(--ui-accent) 20%, var(--ui-border));
      border-radius: 999px;
      background: color-mix(in srgb, var(--ui-accent) 9%, var(--ui-surface));
      color: var(--ui-accent);
      font-size: 11px;
      font-weight: 900;
      text-align: center;
    }

    .studio-field label,
    .studio-field span {
      color: var(--ui-muted);
      font-size: 11px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .035em;
    }

    .studio-color-row {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 58px;
      gap: 12px;
      align-items: center;
      min-height: 54px;
      margin-bottom: 9px;
      padding: 10px 11px 10px 13px;
      border: 1px solid color-mix(in srgb, var(--ui-border) 80%, transparent);
      border-radius: 14px;
      background:
        linear-gradient(135deg, color-mix(in srgb, var(--ui-surface) 94%, #fff), color-mix(in srgb, var(--ui-shell) 42%, var(--ui-surface)));
      box-shadow: 0 10px 24px rgba(18, 50, 70, .045);
    }

    .studio-color-row label {
      color: var(--ui-text);
      font-size: 12px;
      font-weight: 900;
    }

    .studio-field input,
    .studio-field select,
    .studio-field textarea {
      appearance: none;
      width: 100%;
      min-height: 42px;
      border: 1px solid var(--ui-border);
      border-radius: 12px;
      background:
        linear-gradient(180deg, var(--ui-surface), color-mix(in srgb, var(--ui-shell) 30%, var(--ui-surface)));
      color: var(--ui-text);
      font: inherit;
      font-size: 13px;
      font-weight: 800;
      padding: 0 12px;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .65), 0 7px 16px rgba(18, 50, 70, .04);
      transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .studio-field select {
      padding-right: 34px;
      background:
        linear-gradient(45deg, transparent 50%, var(--studio-select-arrow, #5F7482) 50%) right 16px center / 6px 6px no-repeat,
        linear-gradient(135deg, var(--studio-select-arrow, #5F7482) 50%, transparent 50%) right 10px center / 6px 6px no-repeat,
        linear-gradient(180deg, var(--ui-surface), color-mix(in srgb, var(--ui-shell) 30%, var(--ui-surface)));
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
      width: 54px;
      min-height: 36px;
      padding: 3px;
      border-radius: 12px;
      background: var(--ui-surface);
      box-shadow: 0 8px 18px rgba(18, 50, 70, .08);
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
      height: 7px;
      border-radius: 999px;
      background: linear-gradient(90deg, var(--ui-accent), color-mix(in srgb, var(--ui-blue) 70%, #fff));
      box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--ui-accent) 14%, transparent);
    }

    .studio-field input[type="range"]::-webkit-slider-thumb {
      appearance: none;
      width: 18px;
      height: 18px;
      margin-top: -5.5px;
      border: 3px solid #fff;
      border-radius: 50%;
      background: var(--ui-accent);
      box-shadow: 0 7px 14px rgba(18, 50, 70, .22);
    }

    .studio-field input[type="range"]::-moz-range-track {
      height: 7px;
      border-radius: 999px;
      background: linear-gradient(90deg, var(--ui-accent), color-mix(in srgb, var(--ui-blue) 70%, #fff));
    }

    .studio-field input[type="range"]::-moz-range-thumb {
      width: 14px;
      height: 14px;
      border: 3px solid #fff;
      border-radius: 50%;
      background: var(--ui-accent);
      box-shadow: 0 7px 14px rgba(18, 50, 70, .22);
    }

    .studio-segmented,
    .studio-preset-row {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 12px;
    }

    .studio-chip,
    .studio-segment {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 38px;
      padding: 0 14px;
      border: 1px solid var(--ui-border);
      border-radius: 12px;
      background: linear-gradient(180deg, var(--ui-surface), color-mix(in srgb, var(--ui-shell) 25%, var(--ui-surface)));
      color: var(--ui-text);
      font-size: 12px;
      font-weight: 900;
      cursor: pointer;
      box-shadow: 0 8px 18px rgba(18, 50, 70, .045);
    }

    .studio-chip.is-active,
    .studio-segment.is-active {
      border-color: var(--ui-accent);
      background: linear-gradient(135deg, var(--ui-accent), color-mix(in srgb, var(--ui-accent) 72%, var(--ui-blue)));
      color: var(--ui-accent);
      color: #fff;
      box-shadow: 0 12px 24px color-mix(in srgb, var(--ui-accent) 22%, transparent);
    }

    .studio-toggle-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      min-height: 50px;
      margin: 9px 0;
      padding: 10px 12px;
      border: 1px solid color-mix(in srgb, var(--ui-border) 80%, transparent);
      border-radius: 14px;
      background: linear-gradient(180deg, var(--ui-surface), color-mix(in srgb, var(--ui-shell) 30%, var(--ui-surface)));
      color: var(--ui-text);
      font-size: 12px;
      font-weight: 900;
      box-shadow: 0 10px 24px rgba(18, 50, 70, .045);
    }

    .studio-toggle-row input {
      appearance: none;
      position: relative;
      width: 42px;
      height: 24px;
      margin: 0;
      border-radius: 999px;
      background: var(--ui-toggle-off);
      accent-color: var(--ui-accent);
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
      padding: 16px;
      border: 1px solid rgba(106, 142, 174, .2);
      border-radius: 18px;
      background:
        linear-gradient(135deg, rgba(21, 113, 69, .08), rgba(155, 209, 229, .16)),
        #FFFFFF;
      box-shadow: 0 14px 30px rgba(18, 50, 70, .07);
    }

    #studio-schema-section .studio-field {
      padding: 14px;
      border-radius: 20px;
      background:
        linear-gradient(180deg, #FFFFFF, #F7FDFF);
      box-shadow: 0 18px 42px rgba(18, 50, 70, .08);
    }

    #schema-output {
      min-height: calc(100vh - 315px);
      border-radius: 16px;
      border-color: rgba(106, 142, 174, .24);
      background:
        linear-gradient(180deg, #FFFFFF, #FBFEFF);
      color: #173244;
      font-size: 12px;
      line-height: 1.55;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .9);
    }

    .order-list {
      display: grid;
      gap: 10px;
    }

    .order-row {
      display: grid;
      grid-template-columns: 34px minmax(0, 1fr) 40px;
      gap: 10px;
      align-items: center;
      padding: 10px;
      border: 1px solid color-mix(in srgb, var(--ui-border) 82%, transparent);
      border-radius: 14px;
      background: linear-gradient(180deg, var(--ui-surface), color-mix(in srgb, var(--ui-shell) 34%, var(--ui-surface)));
      box-shadow: 0 10px 24px rgba(18, 50, 70, .045);
      cursor: grab;
    }

    .order-row.is-dragging {
      opacity: .58;
      cursor: grabbing;
    }

    .order-grip {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: var(--ui-muted);
    }

    .order-row span {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      font-size: 12px;
      font-weight: 800;
    }

    .studio-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      min-height: 40px;
      padding: 0 14px;
      border: 1px solid rgba(106, 142, 174, .24);
      border-radius: 12px;
      background: linear-gradient(180deg, #FFFFFF, #F4FBFC);
      color: var(--ui-text);
      font-size: 12px;
      font-weight: 900;
      cursor: pointer;
      box-shadow: 0 9px 18px rgba(18, 50, 70, .07);
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
    }

    .studio-btn:hover,
    .studio-btn:focus {
      border-color: rgba(21, 113, 69, .28);
      background: linear-gradient(180deg, #FFFFFF, #EAF7F0);
      box-shadow: 0 13px 26px rgba(18, 50, 70, .11);
      outline: 0;
      transform: translateY(-1px);
    }

    .studio-btn:disabled {
      cursor: not-allowed;
      opacity: .62;
    }

    .studio-btn.primary {
      border-color: var(--ui-accent);
      background: linear-gradient(135deg, #21885C, #157145);
      color: #FFFFFF;
      box-shadow: 0 15px 30px rgba(21, 113, 69, .26);
    }

    .studio-status {
      min-width: 140px;
      min-height: 42px;
      margin-top: 18px;
      padding: 12px 14px;
      border: 1px solid rgba(21, 113, 69, .14);
      border-radius: 14px;
      background: rgba(21, 113, 69, .06);
      color: #315265;
      font-size: 12px;
      font-weight: 800;
    }

    .icon-picker {
      position: fixed;
      z-index: 120;
      display: none;
      width: min(320px, calc(100vw - 24px));
      max-height: min(520px, calc(100vh - 28px));
      padding: 12px;
      border: 1px solid var(--ui-border);
      border-radius: 16px;
      background:
        linear-gradient(180deg, color-mix(in srgb, var(--ui-accent) 5%, var(--ui-surface)), var(--ui-surface) 36%),
        var(--ui-surface);
      box-shadow: 0 20px 50px rgba(12, 25, 34, .22);
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
      background: linear-gradient(180deg, var(--ui-surface), color-mix(in srgb, var(--ui-shell) 30%, var(--ui-surface)));
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

    .device-details-view {
      display: none;
      min-height: 100vh;
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
      display: block;
    }

    .device-details-head {
      position: sticky;
      top: 0;
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
      width: 42px;
      height: 42px;
      padding: 0;
      border: 0;
      border-radius: 50%;
      background: transparent;
      color: var(--ui-accent);
      cursor: pointer;
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
      display: grid;
      gap: var(--ui-card-spacing);
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

    @media (max-width: 719px) {
      .device-details-view {
        min-height: 100vh;
      }
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

    @media (max-width: 1180px) {
      .studio-workspace {
        grid-template-columns: 230px minmax(340px, 1fr) 380px;
      }
    }

    @media (max-width: 860px) {
      .studio-topbar {
        grid-template-columns: 1fr;
        height: auto;
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

      .studio-sections {
        display: none;
      }

      .studio-controls,
      .studio-preview-column {
        border-right: 0;
      }

      .studio-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>


</head>
<body>
  <main class="phone-shell">
    
    <header class="app-topbar">
      <button type="button" class="icon-btn back-btn" onclick="history.back()" aria-label="Back">
        <?= view_ui_icon('chevron-left') ?>
        <span>Back</span>
      </button>

      <div class="top-title">
        <h1 id="active-page-title"><?= esc(!empty($pages[0]['page_name']) ? $pages[0]['page_name'] : ($modelno ?: 'Model UI')) ?></h1>
        <p><?= esc($modelno ?: 'Model UI') ?></p>
        <p id="active-page-subtitle"><?= $is_row_view ? 'Selected Row UI' : (!empty($pages[0]['page_no']) ? 'Page' . esc($pages[0]['page_no']) . '.Operation' : 'No pages') ?></p>
      </div>

      <div class="top-actions">
        <button type="button" class="icon-btn studio-launch" id="open-studio-btn" aria-label="Customize UI">
          <?= view_ui_icon('palette') ?>
        </button>
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
                    <div class="param-card" data-param-key="<?= esc($paramKey) ?>" data-param-name="<?= esc($paramName ?: 'Parameter') ?>" data-api-meta="<?= esc(json_encode($param['api'] ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) ?>">
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

  <div class="studio-backdrop" id="studio-backdrop"></div>
  <aside class="ui-studio" id="ui-studio" aria-label="UI Customization Studio" aria-hidden="true">
    <div class="studio-topbar">
      <button type="button" class="studio-btn" id="close-studio-btn"><?= view_ui_icon('chevron-left') ?> Back to Pages</button>

      <div class="studio-title">
        <h2>UI Studio</h2>
        <p>Customize page design, colors, icons and parameters</p>
      </div>

      <div class="studio-top-actions">
        <!-- <div class="studio-device-tabs" aria-label="Preview size">
          <button type="button" class="studio-btn"><?= view_ui_icon('smartphone') ?></button>
          <button type="button" class="studio-btn"><?= view_ui_icon('layers') ?></button>
          <button type="button" class="studio-btn"><?= view_ui_icon('file') ?></button>
        </div> -->
        <button type="button" class="studio-btn" id="reset-theme-btn"><?= view_ui_icon('settings') ?> Reset</button>
        <button type="button" class="studio-btn" id="generate-schema-btn"><?= view_ui_icon('code') ?> Generate Schema</button>
        <button type="button" class="studio-btn primary" id="save-theme-btn"><?= view_ui_icon('save') ?> Save Changes</button>
      </div>
    </div>

    <div class="studio-workspace">
      <nav class="studio-sections" aria-label="Studio sections">
        <p class="studio-section-kicker">Studio Sections</p>
        <button type="button" class="studio-nav-btn is-active" data-studio-target="studio-theme-section"><?= view_ui_icon('palette') ?><span>Themes<small>Presets and background</small></span></button>
        <button type="button" class="studio-nav-btn" data-studio-target="studio-colors-section"><?= view_ui_icon('droplet') ?><span>Colors<small>Palette and states</small></span></button>
        <button type="button" class="studio-nav-btn" data-studio-target="studio-typography-section"><?= view_ui_icon('file') ?><span>Typography<small>Text styles and fonts</small></span></button>
        <button type="button" class="studio-nav-btn" data-studio-target="studio-params-section"><?= view_ui_icon('gauge') ?><span>Parameters<small>Buttons and icons</small></span></button>
        <button type="button" class="studio-nav-btn" data-studio-target="studio-page-section"><?= view_ui_icon('layers') ?><span>Header<small>Page and header</small></span></button>
        <button type="button" class="studio-nav-btn" data-studio-target="studio-general-section"><?= view_ui_icon('layers') ?><span>Cards<small>Cards and controls</small></span></button>
        <button type="button" class="studio-nav-btn" data-studio-target="studio-button-section"><?= view_ui_icon('power') ?><span>Buttons<small>Button styles</small></span></button>
        <button type="button" class="studio-nav-btn" data-studio-target="studio-order-section"><?= view_ui_icon('move') ?><span>Layout<small>Order and spacing</small></span></button>
        <button type="button" class="studio-nav-btn" data-studio-target="studio-schema-section"><?= view_ui_icon('code') ?><span>Schema<small>Readable device UI JSON</small></span></button>
      </nav>

      <div class="studio-controls has-active">
        <section class="studio-section is-active" id="studio-theme-section">
          <h3>Theme <button type="button" class="studio-btn" id="reset-theme-inline-btn">Reset</button></h3>
          <div class="studio-preset-row">
            <button type="button" class="studio-chip" data-preset="light">Light</button>
            <button type="button" class="studio-chip" data-preset="dark">Dark</button>
            <button type="button" class="studio-chip" data-preset="ocean">Ocean</button>
            <button type="button" class="studio-chip" data-preset="nature">Nature</button>
            <button type="button" class="studio-chip" data-preset="glass">Glass</button>
          </div>
          <div class="studio-segmented" data-segment-key="backgroundType">
            <button type="button" class="studio-segment" data-segment-value="solid">Solid</button>
            <button type="button" class="studio-segment" data-segment-value="gradient">Gradient</button>
          </div>
          <div class="studio-grid">
            <div class="studio-field">
              <label for="studio-themeMode">Theme</label>
              <select id="studio-themeMode" data-theme-key="themeMode">
                <option value="light">Light</option>
                <option value="dark">Dark</option>
                <option value="brand">Brand</option>
              </select>
            </div>
          </div>
        </section>

        <section class="studio-section" id="studio-colors-section">
          <h3>Colors</h3>
          <div class="studio-color-row"><label>Mobile page background</label><input type="color" data-theme-key="backgroundColor"></div>
          <div class="studio-color-row"><label>Mobile gradient end</label><input type="color" data-theme-key="gradientColor"></div>
          <div class="studio-color-row"><label>Phone screen surface</label><input type="color" data-theme-key="shellColor"></div>
          <div class="studio-color-row"><label>Control card background</label><input type="color" data-theme-key="cardColor"></div>
          <div class="studio-color-row"><label>Main label text</label><input type="color" data-theme-key="textColor"></div>
          <div class="studio-color-row"><label>Small helper text</label><input type="color" data-theme-key="mutedTextColor"></div>
          <div class="studio-color-row"><label>Top header bar</label><input type="color" data-theme-key="topbarColor"></div>
          <div class="studio-color-row"><label>Selected icons and accents</label><input type="color" data-theme-key="accentColor"></div>
          <div class="studio-color-row"><label>Progress and secondary fills</label><input type="color" data-theme-key="secondaryColor"></div>
          <div class="studio-color-row"><label>Online/success status</label><input type="color" data-theme-key="successColor"></div>
          <div class="studio-color-row"><label>Warning status</label><input type="color" data-theme-key="warningColor"></div>
          <div class="studio-color-row"><label>Error status</label><input type="color" data-theme-key="errorColor"></div>
          <div class="studio-color-row"><label>Footer action button</label><input type="color" data-theme-key="buttonBgColor"></div>
          <div class="studio-color-row"><label>Footer button text/icon</label><input type="color" data-theme-key="buttonTextColor"></div>
          <div class="studio-color-row"><label>Switch on state</label><input type="color" data-theme-key="toggleOnColor"></div>
          <div class="studio-color-row"><label>Switch off track</label><input type="color" data-theme-key="toggleOffColor"></div>
          <div class="studio-color-row"><label>Parameter icon color</label><input type="color" data-theme-key="iconColor"></div>
        </section>

        <section class="studio-section" id="studio-typography-section">
          <h3>Typography</h3>
          <div class="studio-grid">
            <div class="studio-field">
              <label for="studio-fontFamily">Font family</label>
              <select id="studio-fontFamily" data-theme-key="fontFamily">
                <option value="Poppins">Poppins</option>
                <option value="Inter">Inter</option>
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
            <div class="studio-field"><label>Font size</label><input type="range" min="10" max="22" data-theme-key="fontSize"></div>
            <div class="studio-field"><label>Font weight</label><input type="range" min="300" max="900" step="100" data-theme-key="fontWeight"></div>
            <div class="studio-field"><label>Letter spacing</label><input type="range" min="0" max="3" step="0.1" data-theme-key="letterSpacing"></div>
            <div class="studio-field"><label>Line height</label><input type="range" min="1" max="2" step="0.05" data-theme-key="lineHeight"></div>
            <div class="studio-field">
              <label>Text align</label>
              <select data-theme-key="textAlign">
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
              </select>
            </div>
          </div>
        </section>

        <section class="studio-section" id="studio-params-section">
          <h3>Parameter Buttons</h3>
          <p style="font-size: 12px; color: var(--ui-muted); margin: 0 0 14px;">Choose one parameter, then edit its label, value, icons, colors and visibility.</p>
          <div id="params-buttons-container"></div>
        </section>

        <section class="studio-section" id="studio-general-section">
          <h3>Cards and Layout</h3>
          <div class="studio-grid">
            <div class="studio-field">
              <label for="studio-borderRadius">Border radius</label>
              <input id="studio-borderRadius" type="range" min="4" max="32" data-theme-key="borderRadius">
            </div>
            <div class="studio-field">
              <label for="studio-shadowStrength">Shadows</label>
              <input id="studio-shadowStrength" type="range" min="0" max="100" data-theme-key="shadowStrength">
            </div>
            <div class="studio-field"><label>Border size</label><input type="range" min="0" max="6" data-theme-key="borderSize"></div>
            <div class="studio-field"><label>Border color</label><input type="color" data-theme-key="borderColor"></div>
            <div class="studio-field"><label>Page padding</label><input type="range" min="8" max="36" data-theme-key="pagePadding"></div>
            <div class="studio-field"><label>Card padding</label><input type="range" min="8" max="32" data-theme-key="cardPadding"></div>
            <div class="studio-field"><label>Card spacing</label><input type="range" min="6" max="28" data-theme-key="cardSpacing"></div>
          </div>
        </section>

        <section class="studio-section" id="studio-button-section">
          <h3>Buttons</h3>
          <div class="studio-grid">
            <div class="studio-field"><label>Button text</label><input type="text" data-theme-key="buttonText"></div>
            <div class="studio-field"><label>Button height</label><input type="range" min="34" max="64" data-theme-key="buttonSize"></div>
            <div class="studio-field"><label>Button radius</label><input type="range" min="4" max="30" data-theme-key="buttonRadius"></div>
            <div class="studio-field">
              <label>Choose button</label>
              <select data-theme-key="buttonTarget">
                <option value="save">Save button</option>
                <option value="customize">Customize UI button</option>
                <option value="all">Both footer buttons</option>
              </select>
            </div>
            <div class="studio-field">
              <label>Hover animation</label>
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
        </section>
      </div>

      <div class="studio-preview-column">
        <div class="studio-preview-head">
          <span>Live Preview</span>
          <span class="studio-preview-size">375 x 812</span>
        </div>
        <div class="studio-preview-mount" id="studio-preview-mount"></div>
      </div>

      <aside class="studio-inspector">
        <section class="studio-section" id="studio-page-section">
          <h3>Page Settings</h3>
          <div class="studio-field">
            <label>Header icon</label>
            <button type="button" class="studio-btn" id="header-icon-picker-btn"><?= view_ui_icon($active_ui_theme['iconStyle']) ?> Choose header icon</button>
          </div>
          <div class="studio-field"><label>Header height</label><input type="range" min="60" max="130" data-theme-key="headerHeight"></div>
          <div class="studio-field">
            <label>Header alignment</label>
            <select data-theme-key="headerAlignment">
              <option value="left">Left</option>
              <option value="center">Center</option>
              <option value="right">Right</option>
            </select>
          </div>
          <label class="studio-toggle-row">Show header icons <input type="checkbox" data-theme-key="showHeaderIcons"></label>
          <label class="studio-toggle-row">Show status <input type="checkbox" data-theme-key="showStatus"></label>
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
        </section>

        <section class="studio-section" id="studio-order-section">
          <h3>Control Order</h3>
          <div class="order-list" id="control-order-list"></div>
        </section>

        <section class="studio-section" id="studio-schema-section">
          <h3>Device UI Schema <button type="button" class="studio-btn" id="copy-schema-btn"><?= view_ui_icon('file') ?> Copy</button></h3>
          <div class="studio-field">
            <textarea id="schema-output" spellcheck="false"></textarea>
          </div>
        </section>

        <div class="studio-status" id="studio-status"></div>
      </aside>
    </div>
  </aside>

  <div class="icon-picker" id="param-icon-picker" aria-label="Parameter icon picker">
    <p class="icon-picker-title" id="icon-picker-title">Choose icon</p>
    <input type="text" class="icon-search-box" id="param-icon-search-input" placeholder="Search icons, e.g. fan, power, light">
    <div class="icon-picker-grid" id="icon-picker-grid"></div>
  </div>
  <div class="save-toast" id="save-toast" role="status" aria-live="polite">UI created successfully.</div>

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
    const phoneHome = document.createComment('phone-shell-home');
    const iconPicker = document.getElementById('param-icon-picker');
    const iconPickerGrid = document.getElementById('icon-picker-grid');
    const iconPickerTitle = document.getElementById('icon-picker-title');
    const paramIconSearchInput = document.getElementById('param-icon-search-input');
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
    const saveThemeUrl = <?= json_encode(get_uri('items/save_view_page_ui_theme'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    const deviceInfoUrl = <?= json_encode($device_info_api_url ?? '', JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;

    let currentTheme = Object.assign({}, defaultTheme, savedTheme);
    currentTheme.parameterIcons = currentTheme.parameterIcons && typeof currentTheme.parameterIcons === 'object' && !Array.isArray(currentTheme.parameterIcons) ? currentTheme.parameterIcons : {};
    currentTheme.componentOverrides = currentTheme.componentOverrides && typeof currentTheme.componentOverrides === 'object' && !Array.isArray(currentTheme.componentOverrides) ? currentTheme.componentOverrides : {};
    let activeIndex = 0;
    let activeIconCard = null;
    let activeIconTargetType = 'param';
    let selectedParamKey = '';
    let draggedOrderKey = '';

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
    }

    function openDeviceInfoFromSchema() {
      if (!deviceInfoModal || !deviceInfoContent || !phoneShell) return;
      if (deviceInfoTitle) deviceInfoTitle.textContent = 'Gateway Details';
      deviceInfoContent.innerHTML = '<div class="info-modal-state">Loading gateway details…</div>';
      deviceInfoModal.classList.add('is-open');
      deviceInfoModal.setAttribute('aria-hidden', 'false');
      phoneShell.classList.add('is-showing-info');

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
        buttonSize: 'px',
        buttonRadius: 'px',
        headerHeight: 'px'
      };
      return String(input.value || '0') + (suffixMap[key] || '');
    }

    function updateRangeBadge(input) {
      if (!input || input.type !== 'range') return;
      const field = input.closest('.studio-field');
      if (!field) return;
      field.classList.add('has-range');
      field.dataset.rangeValue = formatRangeValue(input);
    }

    function getParamIcon(card) {
      if (!card) return currentTheme.parameterIcon || 'gauge';
      return currentTheme.parameterIcons[card.dataset.paramKey] || currentTheme.parameterIcon || 'gauge';
    }

    function getParamMetaIcon(card) {
      if (!card) return 'gauge';
      const override = getComponentOverride(card.dataset.paramKey);
      return override.metaIcon || 'gauge';
    }

    function getComponentOverride(key) {
      currentTheme.componentOverrides = currentTheme.componentOverrides && typeof currentTheme.componentOverrides === 'object' ? currentTheme.componentOverrides : {};
      currentTheme.componentOverrides[key] = currentTheme.componentOverrides[key] && typeof currentTheme.componentOverrides[key] === 'object' && !Array.isArray(currentTheme.componentOverrides[key]) ? currentTheme.componentOverrides[key] : {};
      return currentTheme.componentOverrides[key];
    }

    function applyComponentOverrides() {
      document.querySelectorAll('.param-card').forEach(function(card) {
        const override = getComponentOverride(card.dataset.paramKey);
        const label = card.querySelector('.field-label');
        const value = card.querySelector('.field-value');

        if (value && !value.dataset.defaultValue) value.dataset.defaultValue = value.textContent;
        if (label) label.textContent = override.title || card.dataset.paramName || 'Parameter';
        if (value) value.textContent = override.value || value.dataset.defaultValue || '';
        card.style.background = override.cardColor || '';
        card.style.borderColor = override.borderColor || '';
        if (override.statusColor) {
          const meta = card.querySelector('.param-meta');
          if (meta) meta.style.background = override.statusColor;
          const toggle = card.querySelector('.toggle-visual');
          if (toggle) toggle.style.background = override.statusColor;
        } else {
          const meta = card.querySelector('.param-meta');
          if (meta) meta.style.background = '';
          const toggle = card.querySelector('.toggle-visual');
          if (toggle) toggle.style.background = '';
        }
        const metaButton = card.querySelector('.param-meta');
        if (metaButton) {
          const sequenceValue = metaButton.querySelector('.meta-value') ? metaButton.querySelector('.meta-value').textContent : '';
          const metaIcon = getParamMetaIcon(card);
          metaButton.innerHTML = iconSvg(metaIcon) + '<span class="meta-value">' + escapeHtml(sequenceValue || '') + '</span>';
          metaButton.dataset.metaIcon = metaIcon;
        }
        card.style.display = override.hidden ? 'none' : 'grid';
      });
    }

    // Slider Navigation
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
        subtitle.textContent = 'Page ' + (activeSlide.dataset.pageNo || (activeIndex + 1)) + '.Operation';
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

    // Theme Application
    function applyTheme(theme) {
      const root = phoneShell || document.documentElement;
      root.style.setProperty('--ui-bg', theme.backgroundColor);
      root.style.setProperty('--ui-shell', theme.shellColor);
      root.style.setProperty('--ui-surface', theme.cardColor);
      root.style.setProperty('--ui-text', theme.textColor);
      root.style.setProperty('--ui-muted', theme.mutedTextColor);
      root.style.setProperty('--ui-graphite', theme.topbarColor);
      root.style.setProperty('--ui-accent', theme.accentColor);
      root.style.setProperty('--ui-green-dark', theme.accentColor);
      root.style.setProperty('--ui-blue', theme.secondaryColor);
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
      root.style.setProperty('--ui-border-color', theme.borderColor || 'var(--ui-border)');
      root.style.setProperty('--ui-card-blur', parseInt(theme.cardBlur || 0, 10) + 'px');
      root.style.setProperty('--ui-page-padding', parseInt(theme.pagePadding || 18, 10) + 'px');
      root.style.setProperty('--ui-card-padding', parseInt(theme.cardPadding || 16, 10) + 'px');
      root.style.setProperty('--ui-card-spacing', parseInt(theme.cardSpacing || 14, 10) + 'px');
      root.style.setProperty('--ui-button-height', parseInt(theme.buttonSize || 44, 10) + 'px');
      root.style.setProperty('--ui-button-radius', parseInt(theme.buttonRadius || 16, 10) + 'px');

      if (phoneShell) {
        if (theme.backgroundType === 'gradient') {
          phoneShell.style.background = 'linear-gradient(135deg, ' + theme.backgroundColor + ' 0%, ' + (theme.gradientColor || theme.shellColor) + ' 100%)';
        } else {
          phoneShell.style.background = theme.backgroundColor || theme.shellColor;
        }
      }

      document.querySelectorAll('.save-btn').forEach(function(button) {
        const target = theme.buttonTarget || 'save';
        const isSave = button.id === 'save-ui-btn';
        const isCustomize = button.id === 'open-studio-footer-btn';
        const shouldStyle = target === 'all' || (target === 'save' && isSave) || (target === 'customize' && isCustomize);
        button.style.background = shouldStyle ? (theme.buttonBgColor || theme.accentColor) : '';
        button.style.color = shouldStyle ? (theme.buttonTextColor || '#fff') : '';
        button.style.minHeight = (parseInt(theme.buttonSize || 44, 10)) + 'px';
        button.style.borderRadius = (parseInt(theme.buttonRadius || 16, 10)) + 'px';
      });

      const saveUiButton = document.getElementById('save-ui-btn');
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

      document.querySelectorAll('.top-actions').forEach(function(actions) {
        actions.style.display = String(theme.showHeaderIcons) === '0' ? 'none' : 'flex';
      });

      document.querySelectorAll('.online-state').forEach(function(state) {
        state.style.display = String(theme.showStatus) === '0' ? 'none' : 'inline-flex';
      });

      if (theme.headerText) {
        document.querySelectorAll('.slide-hero h2').forEach(function(heroTitle) {
          heroTitle.textContent = theme.headerText;
        });
      }

      const shadow = Math.max(0, Math.min(100, parseInt(theme.shadowStrength || 0, 10))) / 500;
      root.style.setProperty('--ui-card-shadow', '0 12px 28px rgba(18, 50, 70, ' + shadow.toFixed(2) + ')');
      root.style.setProperty('--ui-shadow', '0 18px 38px rgba(18, 50, 70, ' + Math.min(.35, shadow + .06).toFixed(2) + ')');

      document.querySelectorAll('.param-card').forEach(function(card) {
        const iconButton = card.querySelector('.param-icon');
        if (!iconButton) return;
        const iconName = getParamIcon(card);
        iconButton.innerHTML = iconSvg(iconName);
        iconButton.dataset.icon = iconName;
      });

      document.querySelectorAll('.slide-hero-kicker').forEach(function(kicker) {
        kicker.innerHTML = iconSvg(theme.iconStyle) + ' Live Control Page';
      });

      applyComponentOverrides();
      applyControlOrder();
    }

    function syncInputsFromTheme() {
      themeInputs.forEach(function(input) {
        const key = input.dataset.themeKey;
        if (currentTheme[key] !== undefined) {
          if (input.type === 'checkbox') {
            input.checked = String(currentTheme[key]) !== '0';
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
      const currentIcon = getParamIcon(card);
      const metaIconButton = card.querySelector('.param-meta')
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
          '<div class="studio-grid">' +
            '<label class="studio-field"><span>Title</span><input type="text" data-param-prop="title" value="' + escapeHtml(override.title || selected.name || '') + '"></label>' +
            '<label class="studio-field"><span>Value</span><input type="text" data-param-prop="value" value="' + escapeHtml(override.value || '') + '" placeholder="Use live value"></label>' +
            '<label class="studio-field"><span>Status color</span><input type="color" data-param-prop="statusColor" value="' + escapeHtml(override.statusColor || currentTheme.successColor || '#10B981') + '"></label>' +
            '<label class="studio-field"><span>Card color</span><input type="color" data-param-prop="cardColor" value="' + escapeHtml(override.cardColor || currentTheme.cardColor || '#FFFFFF') + '"></label>' +
            '<label class="studio-field"><span>Border color</span><input type="color" data-param-prop="borderColor" value="' + escapeHtml(override.borderColor || currentTheme.borderColor || '#DCE8EF') + '"></label>' +
          '</div>' +
          '<label class="studio-toggle-row">Hide this parameter <input type="checkbox" data-param-prop="hidden" ' + (override.hidden ? 'checked' : '') + '></label>' +
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

      paramsButtonsContainer.querySelectorAll('[data-param-prop]').forEach(function(input) {
        const updateParameterOverride = function() {
          const nextOverride = getComponentOverride(selected.key);
          nextOverride[input.dataset.paramProp] = input.type === 'checkbox' ? input.checked : input.value;
          applyComponentOverrides();
          generateSchema();
          setStudioStatus('Parameter updated. Save changes to keep it.');
        };
        input.addEventListener('input', updateParameterOverride);
        input.addEventListener('change', updateParameterOverride);
      });
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
      closeIconPicker();
    }, true);

    // Control Order
    function getAllControlKeys() {
      return Array.from(document.querySelectorAll('.param-card')).map(function(card) {
        return {
          key: card.dataset.paramKey,
          name: card.dataset.paramName || card.dataset.paramKey
        };
      });
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
      const controlsByKey = {};
      getAllControlKeys().forEach(function(item) {
        controlsByKey[item.key] = item.name;
      });

      orderList.innerHTML = '';
      normalizedControlOrder().forEach(function(key, index) {
        const row = document.createElement('div');
        row.className = 'order-row';
        row.draggable = true;
        row.dataset.orderKey = key;
        const escapedKey = escapeHtml(key);
        const escapedName = escapeHtml(controlsByKey[key] || key);
        row.innerHTML =
          '<span>' + (index + 1) + '</span>' +
          '<span title="' + escapedKey + '">' + escapedName + '</span>' +
          '<span class="order-grip">' + iconSvg('move') + '</span>';
        orderList.appendChild(row);
      });
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
              { type: 'radialGradient', center: { x: 0.88, y: 0.12 }, radius: 0.30, stops: [{ offset: 0, color: 'rgba(87,167,115,0.28)' }, { offset: 1, color: 'rgba(87,167,115,0)' }] },
              linearGradient(135, [{ offset: 0, color: currentTheme.backgroundColor }, { offset: 0.48, color: currentTheme.gradientColor || currentTheme.shellColor }, { offset: 1, color: '#EAF7F0' }], currentTheme.backgroundColor)
            ]
          }
        },
        screen: {
          width: '100%',
          maxWidth: 430,
          minHeight: '100vh',
          marginHorizontal: 'auto',
          overflow: 'clip',
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
          position: 'relative',
          overflow: 'clip',
          minHeight: 66,
          padding: numberValue(currentTheme.cardPadding, 16),
          borderWidth: numberValue(currentTheme.borderSize, 1),
          borderRadius: numberValue(currentTheme.borderRadius, 18),
          blur: numberValue(currentTheme.cardBlur, 0),
          shadow: { x: 0, y: 12, blur: 28, color: 'rgba(18,50,70,' + shadowAlpha.toFixed(2) + ')' },
          parts: {
            label: { color: currentTheme.mutedTextColor, fontSize: 11, fontWeight: 800, lineHeight: 1, uppercase: true, marginBottom: 5 },
            value: { color: currentTheme.textColor, fontSize: 16, fontWeight: 700, lineHeight: 1.25 },
            iconBubble: { width: 38, height: 38, shape: 'circle', backgroundColor: 'rgba(21,113,69,0.14)', innerBorder: { width: 1, color: 'rgba(21,113,69,0.18)' } },
            metaChip: { minWidth: 42, height: 34, paddingHorizontal: 8, gap: 5, shape: 'pill', backgroundColor: 'rgba(87,167,115,0.16)', color: currentTheme.accentColor, fontSize: 12, fontWeight: 900, iconSize: 14 },
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
            pageButtonActive: { borderColor: currentTheme.accentColor, background: linearGradient(135, [{ offset: 0, color: '#57A773' }, { offset: 1, color: currentTheme.accentColor }], currentTheme.accentColor), foregroundColor: '#FFFFFF' }
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
          const isSwitch = Boolean(card.querySelector('.toggle-visual'));
          const isRange = Boolean(card.querySelector('.range-visual'));
          const metaButton = card.querySelector('.param-meta');
          const actionDot = card.querySelector('.action-dot');
          const labelElement = card.querySelector('.field-label');
          const valueElement = card.querySelector('.field-value');
          const metaValueElement = card.querySelector('.meta-value');
          const labelText = override.title || (labelElement ? labelElement.textContent.trim() : card.dataset.paramName);
          const valueText = override.value || (valueElement ? valueElement.textContent.trim() : '');
          const controlKind = isSwitch ? 'Switch' : (isRange ? 'Slider' : 'Action');
          const rightAccessory = isSwitch ? {
            type: 'toggle',
            checked: String(valueText).toLowerCase() === 'on',
            onColor: currentTheme.toggleOnColor,
            offColor: currentTheme.toggleOffColor
          } : (metaButton ? {
            type: 'metaChip',
            icon: getParamMetaIcon(card),
            value: metaValueElement ? metaValueElement.textContent.trim() : String(index + 1),
            backgroundColor: override.statusColor || currentTheme.successColor,
            textColor: currentTheme.accentColor
          } : {
            type: actionDot ? 'actionDot' : 'none',
            color: currentTheme.accentColor
          });
          controls.push({
            id: card.dataset.paramKey,
            name: labelText,
            pageNo: slide.dataset.pageNo,
            pageName: slide.dataset.pageTitle || '',
            component: isSwitch ? 'Switch' : 'ActionCard',
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
            styles: {
              width: 'match_parent',
              minHeight: '66px',
              cardColor: override.cardColor || currentTheme.cardColor,
              borderColor: override.borderColor || currentTheme.borderColor,
              borderWidth: px(currentTheme.borderSize, 1),
              statusColor: override.statusColor || currentTheme.successColor,
              iconColor: currentTheme.iconColor || currentTheme.accentColor,
              textColor: currentTheme.textColor,
              mutedTextColor: currentTheme.mutedTextColor,
              borderRadius: px(currentTheme.borderRadius, 18),
              cardPadding: px(currentTheme.cardPadding, 16),
              leftAccent: {
                visible: true,
                width: '3px',
                colorStart: currentTheme.secondaryColor,
                colorEnd: currentTheme.accentColor
              },
              iconBubble: {
                shape: 'circle',
                size: '38px',
                backgroundColor: 'color-mix(' + currentTheme.accentColor + ' 14%, #FFFFFF)',
                iconColor: currentTheme.iconColor || currentTheme.accentColor
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
                { id: 'backButton', component: 'IconTextButton', icon: 'chevron-left', text: 'Back', className: 'back-btn' },
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
              className: 'progress-shell',
              value: (pageIndex + 1) / Math.max(1, pagesFromDomLength()),
              colorStart: currentTheme.secondaryColor,
              colorEnd: currentTheme.accentColor
            },
            {
              id: 'sliderControls',
              component: 'PageSliderControls',
              required: true,
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
          'Use control.component only to choose the right accessory: Switch renders a toggle; ActionCard renders the meta chip/right badge.',
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
          '.phone-shell{width:min(100%,430px);min-height:100vh;margin:0 auto;background:' + currentTheme.backgroundColor + ';box-shadow:0 30px 80px rgba(34,40,60,.18);overflow:hidden;color:' + currentTheme.textColor + ';font-family:"' + currentTheme.fontFamily + '","Segoe UI",Arial,sans-serif;font-size:' + px(currentTheme.fontSize, 14) + ';font-weight:' + parseInt(currentTheme.fontWeight || 700, 10) + ';letter-spacing:' + (parseFloat(currentTheme.letterSpacing || 0)) + 'px;line-height:' + parseFloat(currentTheme.lineHeight || 1.35) + ';text-align:' + (currentTheme.textAlign || 'left') + ';}',
          '.app-topbar{display:grid;grid-template-columns:auto minmax(0,1fr) auto;gap:10px;align-items:center;min-height:' + px(currentTheme.headerHeight, 84) + ';padding:14px 16px 12px;background:linear-gradient(145deg,rgba(18,50,70,.98),rgba(106,142,174,.94)),' + currentTheme.topbarColor + ';color:#fff;box-shadow:0 18px 38px rgba(18,50,70,.16);}',
          '.back-btn,.icon-btn{display:inline-flex;align-items:center;justify-content:center;height:42px;border:0;border-radius:999px;background:rgba(255,255,255,.1);color:#fff;box-shadow:inset 0 0 0 1px rgba(255,255,255,.12);}.back-btn{width:auto;min-width:42px;padding:0 12px;gap:6px;font-size:12px;font-weight:900;}.icon-btn{width:42px;border-radius:50%;}',
          '.top-title h1{margin:0;color:#fff;font-size:18px;line-height:1.2;font-weight:800;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.top-title p{margin:2px 0 0;color:rgba(255,255,255,.68);font-size:12px;line-height:1.25;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.top-actions{display:flex;align-items:center;gap:4px;}',
          '.brand-strip{display:grid;grid-template-columns:auto minmax(0,1fr) auto;align-items:center;gap:12px;margin:16px 18px 2px;padding:' + px(currentTheme.cardPadding, 16) + ';border:' + px(currentTheme.borderSize, 1) + ' solid ' + currentTheme.borderColor + ';border-radius:calc(' + px(currentTheme.borderRadius, 18) + ' + 2px);background:linear-gradient(135deg,rgba(255,255,255,.94),rgba(209,250,255,.48));box-shadow:0 12px 28px rgba(18,50,70,.1);}',
          '.brand-mark{display:inline-flex;align-items:center;justify-content:center;min-width:78px;height:32px;padding:0 10px;border:2px solid ' + currentTheme.topbarColor + ';border-radius:8px;background:#fff;color:' + currentTheme.topbarColor + ';font-size:11px;font-weight:900;}.brand-copy strong{display:block;color:' + currentTheme.textColor + ';font-size:14px;line-height:1.2;font-weight:900;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.brand-copy span{display:block;margin-top:2px;color:' + currentTheme.mutedTextColor + ';font-size:11px;font-weight:700;}.online-state{display:inline-flex;align-items:center;gap:7px;color:' + currentTheme.successColor + ';font-size:12px;font-weight:900;}',
          '.page-slide{padding:' + px(currentTheme.pagePadding, 18) + ' ' + px(currentTheme.pagePadding, 18) + ' 4px}.slide-hero{position:relative;display:grid;grid-template-columns:minmax(0,1fr) auto;gap:12px;align-items:end;min-height:112px;margin-bottom:14px;padding:18px;border-radius:calc(' + px(currentTheme.borderRadius, 18) + ' + 6px);background:linear-gradient(145deg,rgba(18,50,70,.98),rgba(106,142,174,.9)),' + currentTheme.topbarColor + ';color:#fff;box-shadow:0 18px 38px rgba(18,50,70,.16);overflow:hidden;}.slide-hero:after{content:"";position:absolute;right:-36px;top:-36px;width:112px;height:112px;border-radius:50%;border:24px solid rgba(209,250,255,.16);}',
          '.slide-hero-kicker{display:inline-flex;align-items:center;gap:6px;margin-bottom:8px;color:rgba(255,255,255,.72);font-size:11px;font-weight:800;text-transform:uppercase}.slide-hero h2{margin:0;color:#fff;font-size:22px;line-height:1.1;font-weight:900;overflow-wrap:anywhere}.slide-hero p{margin:6px 0 0;color:rgba(255,255,255,.68);font-size:12px;font-weight:700}.slide-count{position:relative;z-index:1;display:inline-flex;flex-direction:column;align-items:center;justify-content:center;width:58px;height:58px;border-radius:20px;background:rgba(255,255,255,.12);box-shadow:inset 0 0 0 1px rgba(255,255,255,.14);}',
          '.device-card,.param-card{border:' + px(currentTheme.borderSize, 1) + ' solid ' + currentTheme.borderColor + ';border-radius:' + px(currentTheme.borderRadius, 18) + ';background:' + currentTheme.cardColor + ';box-shadow:0 12px 28px rgba(18,50,70,.1);}.name-card{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;align-items:center;padding:' + px(currentTheme.cardPadding, 16) + ';}.field-label{display:block;margin-bottom:5px;color:' + currentTheme.mutedTextColor + ';font-size:11px;line-height:1;font-weight:800;text-transform:uppercase;letter-spacing:.02em}.field-value{display:block;min-width:0;color:' + currentTheme.textColor + ';font-size:16px;line-height:1.25;font-weight:700;overflow-wrap:anywhere}.edit-link{color:' + currentTheme.accentColor + ';font-size:12px;font-weight:800;text-decoration:none;}',
          '.param-list{display:flex;flex-direction:column;gap:' + px(currentTheme.cardSpacing, 14) + ';margin-top:16px}.param-card{display:grid;grid-template-columns:auto minmax(0,1fr) auto;gap:12px;align-items:center;min-height:66px;padding:' + px(currentTheme.cardPadding, 16) + ';position:relative;overflow:hidden;}.param-card:before{content:"";position:absolute;left:0;top:14px;bottom:14px;width:3px;border-radius:0 999px 999px 0;background:linear-gradient(180deg,' + currentTheme.secondaryColor + ',' + currentTheme.accentColor + ')}.param-icon{border:0;display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:rgba(21,113,69,.14);color:' + (currentTheme.iconColor || currentTheme.accentColor) + ';}.param-meta{display:flex;align-items:center;justify-content:center;gap:5px;min-width:42px;height:34px;padding:0 8px;border-radius:999px;background:rgba(87,167,115,.16);color:' + currentTheme.accentColor + ';font-size:12px;font-weight:900;}.toggle-visual{position:relative;width:34px;height:18px;border-radius:999px;background:' + currentTheme.toggleOffColor + ';}.toggle-visual:after{content:"";position:absolute;top:-1px;right:0;width:20px;height:20px;border-radius:50%;background:' + currentTheme.toggleOnColor + ';box-shadow:0 4px 9px rgba(21,113,69,.24);}',
          '.progress-shell{margin:0 18px 8px;height:5px;border-radius:999px;background:rgba(106,142,174,.18);overflow:hidden}.progress-bar{height:100%;border-radius:inherit;background:linear-gradient(90deg,' + currentTheme.secondaryColor + ',' + currentTheme.accentColor + ')}.slider-controls{display:grid;grid-template-columns:48px 1fr 48px;gap:10px;align-items:center;margin:6px 18px 0;padding:12px;border:1px solid rgba(106,142,174,.28);border-radius:20px;background:rgba(255,255,255,.72);box-shadow:0 12px 28px rgba(18,50,70,.1);}.slide-arrow{display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border:1px solid rgba(106,142,174,.28);border-radius:50%;background:' + currentTheme.topbarColor + ';color:#fff}.page-buttons{display:flex;align-items:center;justify-content:center;gap:8px;flex-wrap:wrap}.page-button{min-width:34px;height:34px;border:1px solid rgba(106,142,174,.28);border-radius:999px;background:#fff;color:' + currentTheme.textColor + ';font-size:13px;font-weight:900}.page-button.is-active{border-color:' + currentTheme.accentColor + ';background:linear-gradient(135deg,#57A773,' + currentTheme.accentColor + ');color:#fff;}'
        ].join('\n'),
        componentTemplates: {
          appTopbar: '<button class="icon-btn back-btn">{{icon:chevron-left}}<span>Back</span></button><div class="top-title"><h1>{{page.pageName}}</h1><p>{{modelNo}}</p><p>Page {{page.pageNo}}.Operation</p></div><div class="top-actions">{{actionIcons}}</div>',
          brandStrip: '<div class="brand-mark"><span>E</span>factor</div><div class="brand-copy"><strong>{{modelNo}}</strong><span>{{linkedPagesText}}</span></div><div class="online-state">Online</div>',
          slideHero: '<div class="slide-hero-content"><div class="slide-hero-kicker">{{icon:theme.iconStyle}} Live Control Page</div><h2>{{page.pageName}}</h2><p>{{controlCount}} controls linked to Page {{page.pageNo}}</p></div><div class="slide-count"><strong>{{pageIndex}}</strong><span>Page</span></div>',
          nameCard: '<div><span class="field-label">name</span><span class="field-value">{{page.pageName}}</span></div><a class="edit-link">Edit</a>',
          controlCard: '<button class="param-icon">{{icon:control.icon}}</button><div><span class="field-label">{{control.name}}</span><span class="field-value">{{control.value}}</span></div>{{rightAccessory}}',
          switchAccessory: '<span class="toggle-visual" aria-hidden="true"></span>',
          actionAccessory: '<button class="param-meta">{{icon:control.metaIcon}}<span class="meta-value">{{control.rightAccessory.value}}</span></button>',
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
            minHeight: '100vh',
            overflow: 'hidden'
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
        if (apiType.indexOf('drop') !== -1 || apiType.indexOf('select') !== -1) return 'dropdown';
        if (apiType.indexOf('switch') !== -1 || apiType.indexOf('toggle') !== -1) return 'switch';
        if (apiType.indexOf('slider') !== -1 || apiType.indexOf('range') !== -1) return 'slider';
        if (apiType.indexOf('button') !== -1 || apiType.indexOf('trigger') !== -1) return 'button';
        if (Boolean(card.querySelector('.toggle-visual'))) return 'switch';
        if (Boolean(card.querySelector('.range-visual'))) return 'slider';
        if (Boolean(card.querySelector('.action-dot'))) return 'button';
        return 'value';
      };
      const componentDefault = function(type, meta, valueElement) {
        const rawValue = meta.default !== null && typeof meta.default !== 'undefined'
          ? meta.default
          : (valueElement ? valueElement.textContent.trim() : null);
        if (type === 'button' || type === 'value') return null;
        if (type === 'switch') return /^(1|true|on|enable|enabled)$/i.test(String(rawValue));
        if (type === 'slider') return numberValue(rawValue, 0);
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
          const meta = apiMeta(card);
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
          if (override.cardColor || override.borderColor || override.statusColor) {
            component.styleOverrides = {};
            if (override.cardColor) component.styleOverrides.background = override.cardColor;
            if (override.borderColor) component.styleOverrides.border = override.borderColor;
            if (override.statusColor) component.styleOverrides.status = override.statusColor;
          }
          if (type === 'slider') {
            component.constraints = {
              minimum: numberValue(meta.minimum, 0),
              maximum: numberValue(meta.maximum, 255),
              step: numberValue(meta.step, 1)
            };
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

    function generateSchema() {
      const schema = buildCleanSchema();
      if (schemaOutput) schemaOutput.value = JSON.stringify(schema, null, 2);
      return schema;
    }

    // Studio Controls
    function openStudio() {
      studio.classList.add('is-open');
      studioBackdrop.classList.add('is-open');
      document.body.classList.add('studio-open');
      if (phoneShell && studioPreviewMount && phoneShell.parentNode !== studioPreviewMount) {
        studioPreviewMount.appendChild(phoneShell);
      }
      studio.setAttribute('aria-hidden', 'false');
      syncInputsFromTheme();
      activateStudioSection('studio-theme-section');
      renderOrderList();
      renderParametersButtons();
      renderIconLibrary('');
      generateSchema();
    }

    function closeStudio() {
      studio.classList.remove('is-open');
      studioBackdrop.classList.remove('is-open');
      document.body.classList.remove('studio-open');
      if (phoneShell && phoneHome.parentNode) {
        phoneHome.parentNode.insertBefore(phoneShell, phoneHome.nextSibling);
      }
      studio.setAttribute('aria-hidden', 'true');
      closeIconPicker();
    }

    // Theme Input Listeners
    themeInputs.forEach(function(input) {
      const updateThemeFromInput = function() {
        if (input.type === 'checkbox') {
          currentTheme[input.dataset.themeKey] = input.checked ? '1' : '0';
        } else if (input.type === 'range') {
          currentTheme[input.dataset.themeKey] = input.step && input.step.indexOf('.') !== -1 ? parseFloat(input.value) : parseInt(input.value, 10);
        } else {
          currentTheme[input.dataset.themeKey] = input.value;
        }
        updateRangeBadge(input);
        applyTheme(currentTheme);
        generateSchema();
      };
      input.addEventListener('input', updateThemeFromInput);
      input.addEventListener('change', updateThemeFromInput);
    });

    const themePresets = {
      light: {
        themeMode: 'light', backgroundType: 'solid', backgroundColor: '#D1FAFF', gradientColor: '#F7FDFF',
        shellColor: '#F7FDFF', cardColor: '#FFFFFF', textColor: '#102A3A', mutedTextColor: '#5F7482',
        topbarColor: '#123246', accentColor: '#157145', secondaryColor: '#9BD1E5', buttonBgColor: '#157145',
        buttonTextColor: '#FFFFFF', iconColor: '#157145', borderColor: '#DCE8EF'
      },
      dark: {
        themeMode: 'dark', backgroundType: 'gradient', backgroundColor: '#07151F', gradientColor: '#102A3A',
        shellColor: '#0D2230', cardColor: '#173244', textColor: '#F7FDFF', mutedTextColor: '#C5D7E2',
        topbarColor: '#07131C', accentColor: '#7EE2A8', secondaryColor: '#6FB6D6', buttonBgColor: '#1FAD72',
        buttonTextColor: '#06120D', iconColor: '#7EE2A8', borderColor: '#36566A', toggleOnColor: '#7EE2A8', toggleOffColor: '#4A6473'
      },
      ocean: {
        themeMode: 'ocean', backgroundType: 'gradient', backgroundColor: '#D1FAFF', gradientColor: '#9BD1E5',
        shellColor: '#F7FDFF', cardColor: '#FFFFFF', textColor: '#102A3A', mutedTextColor: '#5F7482',
        topbarColor: '#2E5A73', accentColor: '#2E5A73', secondaryColor: '#9BD1E5', buttonBgColor: '#2E5A73',
        buttonTextColor: '#FFFFFF', iconColor: '#2E5A73', borderColor: '#B9DDE9'
      },
      nature: {
        themeMode: 'nature', backgroundType: 'gradient', backgroundColor: '#EAF7F0', gradientColor: '#D1FAFF',
        shellColor: '#F7FDFF', cardColor: '#FFFFFF', textColor: '#102A3A', mutedTextColor: '#5F7482',
        topbarColor: '#157145', accentColor: '#157145', secondaryColor: '#57A773', buttonBgColor: '#157145',
        buttonTextColor: '#FFFFFF', iconColor: '#157145', borderColor: '#CFE9DA'
      },
      glass: {
        themeMode: 'glass', backgroundType: 'gradient', backgroundColor: '#F7FDFF', gradientColor: '#D1FAFF',
        shellColor: '#F7FDFF', cardColor: '#FFFFFF', textColor: '#102A3A', mutedTextColor: '#5F7482',
        topbarColor: '#123246', accentColor: '#6A8EAE', secondaryColor: '#9BD1E5', buttonBgColor: '#6A8EAE',
        buttonTextColor: '#FFFFFF', iconColor: '#2E5A73', borderColor: '#CDE0EA', cardBlur: 10, shadowStrength: 90
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
        row.classList.add('is-dragging');
        event.dataTransfer.effectAllowed = 'move';
      });

      orderList.addEventListener('dragend', function(event) {
        const row = event.target.closest('.order-row');
        if (row) row.classList.remove('is-dragging');
      });

      orderList.addEventListener('dragover', function(event) {
        event.preventDefault();
      });

      orderList.addEventListener('drop', function(event) {
        event.preventDefault();
        const targetRow = event.target.closest('.order-row');
        if (!targetRow || !draggedOrderKey || targetRow.dataset.orderKey === draggedOrderKey) return;

        const copy = currentTheme.controlOrder.slice();
        const fromIndex = copy.indexOf(draggedOrderKey);
        const toIndex = copy.indexOf(targetRow.dataset.orderKey);
        if (fromIndex < 0 || toIndex < 0) return;

        copy.splice(fromIndex, 1);
        copy.splice(toIndex, 0, draggedOrderKey);
        currentTheme.controlOrder = copy;
        draggedOrderKey = '';
        applyControlOrder();
        generateSchema();
      });
    }

    function activateStudioSection(sectionId) {
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
      generateSchema();
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
    });

    // Button Click Handlers
    bindClick('open-studio-btn', openStudio);
    bindClick('open-studio-footer-btn', openStudio);
    bindClick('close-studio-btn', closeStudio);
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
      applyTheme(currentTheme);
      generateSchema();
      setStudioStatus('Theme reset in preview. Save to keep it.');
    }

    bindClick('reset-theme-btn', resetThemePreview);
    bindClick('reset-theme-inline-btn', resetThemePreview);

    function generateSchemaFromButton() {
      generateSchema();
      activateStudioSection('studio-schema-section');
      setStudioStatus('Readable device UI schema generated successfully.');
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
      const schema = generateSchema();
      const formData = new FormData();
      const saveButton = document.getElementById('save-theme-btn');
      formData.append('modelno', modelNo);
      formData.append('theme_json', JSON.stringify(currentTheme));
      formData.append('schema_json', JSON.stringify(schema));
      setStudioStatus('Saving...');
      if (saveButton) saveButton.disabled = true;

      fetch(saveThemeUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      })
      .then(function(response) { return response.json(); })
      .then(function(result) {
        const saved = result.success === true || result.success === 1 || result.success === '1';
        setStudioStatus(result.message || (saved ? 'Saved successfully.' : 'Save failed.'));
        if (saved) {
          closeStudio();
          showSaveToast('UI created successfully.');
        }
      })
      .catch(function() {
        setStudioStatus('Save failed. Please try again.');
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
    showSlide(0);
    syncInputsFromTheme();
    applyTheme(currentTheme);
    generateSchema();
  </script>
</body>
</html>
