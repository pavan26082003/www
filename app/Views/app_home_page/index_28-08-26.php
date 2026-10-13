<?php
if (!defined('APPPATH')) {
    header('Location: /index.php/app-home-page');
    exit;
}

helper('url');

function ahp_h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function ahp_color_field(string $id, string $label, string $value): string
{
    $v = ahp_h($value);
    $lbl = ahp_h($label);
    return '<div class="ahp-field ahp-color-field">'
        . '<label for="' . $id . '">' . $lbl . '</label>'
        . '<div class="ahp-color-control">'
        . '<span class="ahp-color-preview" id="' . $id . 'Preview" style="background:' . $v . '" aria-hidden="true"></span>'
        . '<input type="color" id="' . $id . '" value="' . $v . '" aria-label="' . $lbl . ' picker">'
        . '<input type="text" class="ahp-color-hex" id="' . $id . 'Hex" value="' . $v . '" maxlength="7" spellcheck="false" '
        . 'aria-label="' . $lbl . ' hex code" placeholder="#000000" autocomplete="off">'
        . '</div></div>';
}

$layoutJson = json_encode($layout, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
$defaultLayoutJson = json_encode($defaultLayout, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);

function ahp_www_root(): string
{
    if (defined('FCPATH')) {
        return rtrim(FCPATH, '/\\');
    }

    return dirname(__DIR__, 3);
}

function ahp_asset_file_path(string $relativePath): string
{
    return ahp_www_root() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($relativePath, '/'));
}

function ahp_inline_asset(string $relativePath): string
{
    $path = ahp_asset_file_path($relativePath);
    if (!is_readable($path)) {
        return '';
    }
    $content = file_get_contents($path);
    $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
    return str_replace('</script>', '<\\/script>', $content);
}

function ahp_asset_url(string $relativePath): string
{
    return base_url(ltrim($relativePath, '/'));
}

function ahp_asset_version(string $relativePath): string
{
    $path = ahp_asset_file_path($relativePath);
    if (!is_readable($path)) {
        return '';
    }

    return '?v=' . filemtime($path);
}

/**
 * Inline asset when readable; otherwise load from /assets/… (needed on server deploy).
 */
function ahp_script_tag(string $relativePath, string $id = ''): string
{
    $inline = ahp_inline_asset($relativePath);
    $idAttr = $id !== '' ? ' id="' . ahp_h($id) . '"' : '';
    if ($inline !== '') {
        return '<script' . $idAttr . '>' . $inline . '</script>';
    }

    $src = ahp_asset_url($relativePath) . ahp_asset_version($relativePath);
    return '<script' . $idAttr . ' src="' . ahp_h($src) . '"></script>';
}

$ahpInlineCss = ahp_inline_asset('assets/css/app_home_page.css');
$ahpIotSduiReadable = is_readable(ahp_asset_file_path('assets/js/app_home_iot_sdui.js'));
$ahpAppJsReadable = is_readable(ahp_asset_file_path('assets/js/app_home_page.js'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Home Page — eFactor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&family=Poppins:wght@400;500;600;700;800&family=Montserrat:wght@400;500;600;700;800&family=Open+Sans:wght@400;600;700&family=Lato:wght@400;700&family=Nunito:wght@400;600;700;800&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style id="ahp-inline-css"><?= $ahpInlineCss ?></style>
</head>
<body class="ahp-body">

<div class="ahp-shell" id="ahpShell">
    <nav class="ahp-flow-actions" aria-label="Page actions">
        <button type="button" class="ahp-flow-btn" id="ahpBtnBack">
            <i class="fas fa-chevron-left"></i> Back
        </button>
        <button type="button" class="ahp-flow-btn ahp-flow-studio" id="ahpBtnSetting">
            <i class="fas fa-sliders"></i> Setting
        </button>
        <button type="button" class="ahp-flow-btn ahp-flow-done" id="ahpBtnDone" style="display:none">
            <i class="fas fa-check"></i> Done
        </button>
        <button type="button" class="ahp-flow-btn ahp-flow-save" id="ahpBtnSave" style="display:none">
            <i class="fas fa-save"></i> Save
        </button>
        <button type="button" class="ahp-flow-btn ahp-flow-schema" id="ahpBtnSchema" style="display:none">
            <i class="fas fa-code"></i> Schema
        </button>
        <button type="button" class="ahp-flow-btn ahp-flow-studio-theme" id="ahpBtnStudioTheme" style="display:none" title="Toggle studio panel light/dark mode" aria-pressed="false">
            <i class="fas fa-sun" id="ahpStudioThemeIcon"></i> <span id="ahpStudioThemeLabel">Light</span>
        </button>
    </nav>

    <div class="ahp-viewport view-mode" id="ahpViewport">
        <div class="ahp-workspace">

        <!-- Left: menu sidebar (edit mode) -->
        <aside class="ahp-menu-column" aria-label="Editor sections">
            <div class="ahp-studio-brand">
                <div class="ahp-studio-mark"><i class="fas fa-drafting-compass"></i></div>
                <div>
                    <strong>eFactor Studio</strong>
                    <span>App Home Designer</span>
                </div>
            </div>
            <p class="ahp-nav-kicker">Workspace</p>
            <p class="ahp-nav-divider">Foundation</p>
            <nav class="ahp-menu">
                <button type="button" class="ahp-menu-item active" data-menu="layout">
                    <i class="fas fa-th-large"></i>
                    <span>Layout<small>Grid and header</small></span>
                </button>
                <button type="button" class="ahp-menu-item" data-menu="theme">
                    <i class="fas fa-palette"></i>
                    <span>Theme<small>Colors and style</small></span>
                </button>
            </nav>
            <p class="ahp-nav-divider">Content</p>
            <nav class="ahp-menu ahp-menu-secondary">
                <button type="button" class="ahp-menu-item" data-menu="blocks">
                    <i class="fas fa-cubes"></i>
                    <span>Blocks<small>Add new blocks</small></span>
                </button>
                <button type="button" class="ahp-menu-item" data-menu="cards">
                    <i class="fas fa-clone"></i>
                    <span>Cards<small>Edit card faces</small></span>
                </button>
                <button type="button" class="ahp-menu-item" data-menu="navigation">
                    <i class="fas fa-compass"></i>
                    <span>Navigation<small>Bottom nav bar</small></span>
                </button>
                <button type="button" class="ahp-menu-item" data-menu="profile">
                    <i class="fas fa-user"></i>
                    <span>Profile<small>Account and settings</small></span>
                </button>
                <button type="button" class="ahp-menu-item" data-menu="schema">
                    <i class="fas fa-code"></i>
                    <span>Schema<small>API to fetch JSON</small></span>
                </button>
            </nav>
            <div class="ahp-menu-footer">
                <button type="button" class="ahp-menu-reset" id="ahpMenuResetBtn">
                    <i class="fas fa-undo"></i>
                    <span>Reset layout</span>
                </button>
            </div>
        </aside>

        <!-- Middle: live preview -->
        <section class="ahp-preview-column">
            <div class="ahp-preview-head">
                <div class="ahp-preview-title-wrap">
                    <span class="ahp-preview-live-dot" aria-hidden="true"></span>
                    <span class="ahp-preview-title">Live Preview</span>
                </div>
                <div class="ahp-preview-head-tools">
                    <div class="ahp-preview-zoom" aria-label="Preview zoom controls">
                        <button type="button" class="ahp-preview-zoom-btn" id="ahpZoomOut" aria-label="Zoom out"><i class="fas fa-minus"></i></button>
                        <span class="ahp-preview-zoom-label" id="ahpZoomLabel">100%</span>
                        <button type="button" class="ahp-preview-zoom-btn" id="ahpZoomIn" aria-label="Zoom in"><i class="fas fa-plus"></i></button>
                        <button type="button" class="ahp-preview-zoom-btn ahp-preview-zoom-fit is-active" id="ahpZoomFit" aria-label="Fit preview to screen"><i class="fas fa-expand"></i> Fit</button>
                    </div>
                    <span class="ahp-preview-size">375 × 812 · iPhone</span>
                </div>
            </div>

            <div class="ahp-preview-mount" id="ahpPreviewMount">
                <div class="ahp-preview-frame" id="ahpPreviewFrame">
                    <div class="ahp-phone" id="ahpPhone" style="--ahp-card-speed: <?= (int) ($layout['theme']['cardSpeed'] ?? 16) ?>s;--ahp-grid-gap: <?= max(0, min(24, (int) ($layout['theme']['gridGap'] ?? 12))) ?>px;--ahp-grid-columns: <?= max(1, min(6, (int) ($layout['theme']['gridColumns'] ?? 3))) ?>;--ahp-grid-row-height: <?= max(72, min(140, (int) ($layout['theme']['gridRowHeight'] ?? 96))) ?>px">
                        <div class="ahp-phone-notch"></div>
                        <div class="ahp-phone-screen">
                            <div class="ahp-phone-scroll" id="ahpPhoneScroll">
                                <!-- Home page -->
                                <div class="ahp-mobile ahp-page ahp-home-page" id="ahpHomePage" data-page="home">
                                    <div class="ahp-status-bar" aria-hidden="true">
                                        <span class="ahp-status-time">9:41</span>
                                        <span class="ahp-status-icons"><i class="fas fa-signal"></i><i class="fas fa-wifi"></i><i class="fas fa-battery-full"></i></span>
                                    </div>

                                    <header class="ahp-home-top">
                                        <div class="ahp-home-greeting">
                                            <p class="ahp-home-hey" id="ahpHomeHey">Hey, <?= ahp_h($layout['profilePage']['name'] ?? 'Guest') ?> 👋</p>
                                            <h1 class="ahp-home-title" id="ahpHeaderTitle"><?= ahp_h($layout['header']['title'] ?? 'Welcome to SmartHome!') ?></h1>
                                            <p class="ahp-home-sub" id="ahpHeaderSub"><?= ahp_h($layout['header']['subtitle'] ?? 'Control your connected home') ?></p>
                                        </div>
                                        <button type="button" class="ahp-home-avatar" id="ahpHomeAvatar" data-nav="profile" aria-label="Open profile">
                                            <span id="ahpHomeAvatarInitials"><?php
                                                $profName = (string) ($layout['profilePage']['name'] ?? 'Guest');
                                                echo ahp_h(strtoupper(substr(trim($profName), 0, 1)));
                                            ?></span>
                                        </button>
                                    </header>

                                    <div class="ahp-alerts ahp-alerts-compact" id="ahpAlerts">
                                        <div class="ahp-alerts-icon"><i class="fas fa-bell"></i></div>
                                        <div class="ahp-alerts-text">
                                            <h3 id="ahpAlertCount">Live Alerts <?= (int) ($layout['alerts']['count'] ?? 0) ?></h3>
                                            <p id="ahpAlertMeta"><?= (int) ($layout['alerts']['newUsers'] ?? 0) ?> new users today</p>
                                        </div>
                                        <div class="ahp-alerts-badge" id="ahpAlertsBadge"><span class="dot"></span> <span id="ahpBadgeText">real-time</span></div>
                                    </div>

                                    <div class="ahp-grid" id="ahpGrid"></div>
                                </div>

                                <!-- Devices page (IoT SDUI from get_iot_schema) -->
                                <div class="ahp-mobile ahp-page ahp-is-hidden" id="ahpDevicesPage" data-page="devices">
                                    <div id="ahpIotSduiRoot" class="ahp-iot-sdui-root"></div>
                                </div>

                                <!-- Placeholder for other tabs -->
                                <div class="ahp-mobile ahp-page ahp-is-hidden" id="ahpPlaceholderPage" data-page="placeholder">
                                    <div class="ahp-page-placeholder">
                                        <i class="fas fa-layer-group" id="ahpPlaceholderIcon"></i>
                                        <h2 id="ahpPlaceholderTitle">Page</h2>
                                        <p id="ahpPlaceholderHint">This screen is not customized yet.</p>
                                    </div>
                                </div>
                            </div>
                            <nav class="ahp-bottom-nav" id="ahpBottomNav"></nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Right: tools panel (edit mode) -->
        <aside class="ahp-tools-column" aria-label="Editor tools">
            <div class="ahp-tools-scroll">
                <div class="ahp-tools">
                    <div class="ahp-tool-section active" data-tool="layout">
                        <div class="ahp-tool-title">Home Layout</div>
                        <p class="ahp-tool-hint">Header, grid spacing, alerts strip, and cube rotation.</p>
                        <div class="ahp-field">
                            <label for="toolHeaderTitle">Welcome title</label>
                            <input type="text" id="toolHeaderTitle" value="<?= ahp_h($layout['header']['title'] ?? '') ?>" placeholder="Welcome to SmartHome!">
                        </div>
                        <div class="ahp-field">
                            <label for="toolHeaderSub">Subtitle (optional)</label>
                            <input type="text" id="toolHeaderSub" value="<?= ahp_h($layout['header']['subtitle'] ?? '') ?>">
                        </div>
                        <?php
                        $gridGap = max(0, min(24, (int) ($layout['theme']['gridGap'] ?? 12)));
                        $gridColumns = max(1, min(6, (int) ($layout['theme']['gridColumns'] ?? 3)));
                        $gridRowHeight = max(72, min(140, (int) ($layout['theme']['gridRowHeight'] ?? 96)));
                        $gridGapPct = max(0, min(100, round(($gridGap / 24) * 100)));
                        $gridColumnsPct = max(0, min(100, round((($gridColumns - 1) / 5) * 100)));
                        $gridRowHeightPct = max(0, min(100, round((($gridRowHeight - 72) / 68) * 100)));
                        $cardBorderRadius = (int) ($layout['theme']['cardBorderRadius'] ?? 0);
                        $cardBorderRadiusPct = max(0, min(100, round(($cardBorderRadius / 72) * 100)));
                        ?>
                        <div class="ahp-field ahp-range-field">
                            <div class="ahp-range-head">
                                <label for="toolGridGap">Card gap</label>
                                <span class="ahp-range-val" id="toolGridGapVal"><span class="ahp-range-val-num"><?= $gridGap ?></span><span class="ahp-range-val-unit">px</span></span>
                            </div>
                            <div class="ahp-range-control">
                                <input type="range" id="toolGridGap" min="0" max="24" step="1" value="<?= $gridGap ?>" style="--ahp-range-pct:<?= $gridGapPct ?>%" aria-valuemin="0" aria-valuemax="24" aria-valuenow="<?= $gridGap ?>">
                            </div>
                            <div class="ahp-range-scale"><span>0px</span><span>24px</span></div>
                        </div>
                        <div class="ahp-field ahp-range-field">
                            <div class="ahp-range-head">
                                <label for="toolGridColumns">Grid columns</label>
                                <span class="ahp-range-val" id="toolGridColumnsVal"><span class="ahp-range-val-num"><?= $gridColumns ?></span><span class="ahp-range-val-unit"></span></span>
                            </div>
                            <div class="ahp-range-control">
                                <input type="range" id="toolGridColumns" min="1" max="6" step="1" value="<?= $gridColumns ?>" style="--ahp-range-pct:<?= $gridColumnsPct ?>%" aria-valuemin="1" aria-valuemax="6" aria-valuenow="<?= $gridColumns ?>">
                            </div>
                            <div class="ahp-range-scale"><span>1 col</span><span>6 cols</span></div>
                        </div>
                        <?php
                        $gridRows = max(1, min(6, (int) ($layout['theme']['gridRows'] ?? 3)));
                        $gridRowsPct = max(0, min(100, round((($gridRows - 1) / 5) * 100)));
                        ?>
                        <div class="ahp-field ahp-range-field">
                            <div class="ahp-range-head">
                                <label for="toolGridRows">Grid rows</label>
                                <span class="ahp-range-val" id="toolGridRowsVal"><span class="ahp-range-val-num"><?= $gridRows ?></span><span class="ahp-range-val-unit"></span></span>
                            </div>
                            <div class="ahp-range-control">
                                <input type="range" id="toolGridRows" min="1" max="6" step="1" value="<?= $gridRows ?>" style="--ahp-range-pct:<?= $gridRowsPct ?>%" aria-valuemin="1" aria-valuemax="6" aria-valuenow="<?= $gridRows ?>">
                            </div>
                            <div class="ahp-range-scale"><span>1 row</span><span>6 rows</span></div>
                            <p class="ahp-tool-hint" style="margin-top:8px">Max card height in rows. Card editor width/height respect these limits.</p>
                        </div>
                        <div class="ahp-field ahp-range-field">
                            <div class="ahp-range-head">
                                <label for="toolGridRowHeight">Cell height</label>
                                <span class="ahp-range-val" id="toolGridRowHeightVal"><span class="ahp-range-val-num"><?= $gridRowHeight ?></span><span class="ahp-range-val-unit">px</span></span>
                            </div>
                            <div class="ahp-range-control">
                                <input type="range" id="toolGridRowHeight" min="72" max="140" step="1" value="<?= $gridRowHeight ?>" style="--ahp-range-pct:<?= $gridRowHeightPct ?>%" aria-valuemin="72" aria-valuemax="140" aria-valuenow="<?= $gridRowHeight ?>">
                            </div>
                            <div class="ahp-range-scale"><span>72px (compact)</span><span>140px (tall)</span></div>
                        </div>
                        <div class="ahp-field ahp-range-field">
                            <div class="ahp-range-head">
                                <label for="toolCardBorderRadius">Default box radius</label>
                                <span class="ahp-range-val" id="toolCardBorderRadiusVal"><span class="ahp-range-val-num"><?= $cardBorderRadius ?></span><span class="ahp-range-val-unit">px</span></span>
                            </div>
                            <div class="ahp-range-control">
                                <input type="range" id="toolCardBorderRadius" min="0" max="72" step="1" value="<?= $cardBorderRadius ?>" style="--ahp-range-pct:<?= $cardBorderRadiusPct ?>%" aria-valuemin="0" aria-valuemax="72" aria-valuenow="<?= $cardBorderRadius ?>">
                            </div>
                            <div class="ahp-range-scale"><span>0 (none)</span><span>72px</span></div>
                            <p class="ahp-tool-hint" style="margin-top:8px">New blocks start square. Per-card radius can override this in the card editor.</p>
                        </div>
                        <?php
                        $cardSpeed = (int) ($layout['theme']['cardSpeed'] ?? 16);
                        $faceSec = max(2, min(8, (int) round($cardSpeed / 4)));
                        $faceSecPct = max(0, min(100, round((($faceSec - 2) / 6) * 100)));
                        ?>
                        <div class="ahp-field ahp-range-field">
                            <div class="ahp-range-head">
                                <label for="toolCardFaceSec">Face display time</label>
                                <span class="ahp-range-val" id="toolCardFaceSecVal"><span class="ahp-range-val-num"><?= $faceSec ?></span><span class="ahp-range-val-unit"> sec</span></span>
                            </div>
                            <div class="ahp-range-control">
                                <input type="range" id="toolCardFaceSec" min="2" max="8" step="1" value="<?= $faceSec ?>" style="--ahp-range-pct:<?= $faceSecPct ?>%" aria-valuemin="2" aria-valuemax="8" aria-valuenow="<?= $faceSec ?>">
                            </div>
                            <div class="ahp-range-scale"><span>2s (fast)</span><span>8s (slow)</span></div>
                            <p class="ahp-tool-hint" style="margin-top:8px">Each card rotates through 4 faces as a 3D box. Cards are intentionally out of sync so the grid feels alive.</p>
                        </div>
                        <div class="ahp-field ahp-check-field">
                            <label><input type="checkbox" id="toolShowAlerts" <?= !empty($layout['theme']['showAlerts']) ? 'checked' : '' ?>> Show alerts strip</label>
                        </div>
                        <div class="ahp-field ahp-check-field">
                            <label><input type="checkbox" id="toolShowBottomNav" <?= !empty($layout['theme']['showBottomNav']) ? 'checked' : '' ?>> Show bottom navigation</label>
                        </div>
                        <div class="ahp-style-section-title" style="margin-top:14px">Alerts strip</div>
                        <div class="ahp-field">
                            <label for="toolAlertCount">Alert count</label>
                            <input type="number" id="toolAlertCount" step="0.1" value="<?= ahp_h((string) ($layout['alerts']['count'] ?? 0)) ?>">
                        </div>
                        <div class="ahp-field">
                            <label for="toolAlertUsers">New users subtitle</label>
                            <input type="number" id="toolAlertUsers" value="<?= (int) ($layout['alerts']['newUsers'] ?? 0) ?>" placeholder="New users count">
                        </div>
                        <div class="ahp-field">
                            <label for="toolAlertBadge">Badge text</label>
                            <input type="text" id="toolAlertBadge" value="<?= ahp_h($layout['alerts']['badgeText'] ?? 'real-time') ?>">
                        </div>
                        <div class="ahp-field ahp-check-field">
                            <label><input type="checkbox" id="toolShowAlertBadge" <?= !isset($layout['alerts']['showBadge']) || !empty($layout['alerts']['showBadge']) ? 'checked' : '' ?>> Show real-time badge</label>
                        </div>
                        <input type="hidden" id="toolWelcomeGreet" value="<?= ahp_h($layout['welcome']['greeting'] ?? '') ?>">
                        <input type="hidden" id="toolWelcomeEmail" value="<?= ahp_h($layout['welcome']['email'] ?? $userEmail) ?>">
                        <input type="hidden" id="toolLogoIcon" value="<?= ahp_h($layout['theme']['logoIcon'] ?? 'fa-crosshairs') ?>">
                        <button type="button" class="ahp-btn ahp-btn-ghost" id="ahpResetLayout" style="width:100%;margin-top:8px">
                            <i class="fas fa-undo"></i> Reset to Default
                        </button>
                    </div>

                    <div class="ahp-tool-section" data-tool="blocks">
                        <div class="ahp-tool-title">Add Blocks</div>
                        <p class="ahp-tool-hint">Click a block type to add it to the next free grid slot. Drag the grip icon on a card to reorder.</p>
                        <p class="ahp-block-count" id="ahpBlockCount">0 blocks · scrollable</p>
                        <button type="button" class="ahp-tool-btn ahp-tool-btn-danger" id="ahpClearAllCardsBtn" style="margin-bottom:12px">
                            <i class="fas fa-trash-alt"></i> Remove all cards
                        </button>
                        <div class="ahp-block-list">
                            <div class="ahp-block-item" data-type="stat"><i class="fas fa-chart-bar"></i> Stat Block</div>
                            <div class="ahp-block-item" data-type="link"><i class="fas fa-link"></i> Link Block</div>
                            <div class="ahp-block-item" data-type="image"><i class="fas fa-image"></i> Image Block</div>
                            <div class="ahp-block-item" data-type="add"><i class="fas fa-plus"></i> Action Block</div>
                        </div>
                    </div>

                    <div class="ahp-tool-section" data-tool="cards">
                        <div class="ahp-tool-title">Edit Cards</div>
                        <p class="ahp-tool-hint">Pick a card, click a face in the preview, then edit label, subtitle, navigation URL, colors, and typography.</p>
                        <div class="ahp-field">
                            <label for="ahpCardSelect">Select card</label>
                            <select id="ahpCardSelect" class="ahp-card-select">
                                <option value="">Choose a card to edit…</option>
                            </select>
                        </div>
                        <div id="ahpCardEditor" class="ahp-card-editor-panel"></div>
                    </div>

                    <div class="ahp-tool-section" data-tool="theme">
                        <div class="ahp-tool-title">Theme</div>
                        <p class="ahp-tool-hint">Pick a mobile theme preset or fine-tune individual colors. Changes apply instantly in the preview.</p>
                        <div class="ahp-field">
                            <label for="toolBgStyle">Background preset</label>
                            <select id="toolBgStyle">
                                <option value="light" <?= ($layout['theme']['bgStyle'] ?? 'light') === 'light' ? 'selected' : '' ?>>Light SmartHome</option>
                                <option value="dark" <?= ($layout['theme']['bgStyle'] ?? '') === 'dark' ? 'selected' : '' ?>>Dark</option>
                                <option value="midnight" <?= ($layout['theme']['bgStyle'] ?? '') === 'midnight' ? 'selected' : '' ?>>Midnight</option>
                                <option value="slate" <?= ($layout['theme']['bgStyle'] ?? '') === 'slate' ? 'selected' : '' ?>>Slate</option>
                                <option value="ocean" <?= ($layout['theme']['bgStyle'] ?? '') === 'ocean' ? 'selected' : '' ?>>Ocean</option>
                                <option value="purple" <?= ($layout['theme']['bgStyle'] ?? '') === 'purple' ? 'selected' : '' ?>>Purple</option>
                                <option value="forest" <?= ($layout['theme']['bgStyle'] ?? '') === 'forest' ? 'selected' : '' ?>>Forest</option>
                                <option value="rose" <?= ($layout['theme']['bgStyle'] ?? '') === 'rose' ? 'selected' : '' ?>>Rose</option>
                                <option value="aurora" <?= ($layout['theme']['bgStyle'] ?? '') === 'aurora' ? 'selected' : '' ?>>Aurora</option>
                                <option value="ember" <?= ($layout['theme']['bgStyle'] ?? '') === 'ember' ? 'selected' : '' ?>>Ember</option>
                                <option value="graphite" <?= ($layout['theme']['bgStyle'] ?? '') === 'graphite' ? 'selected' : '' ?>>Graphite</option>
                                <option value="glass" <?= ($layout['theme']['bgStyle'] ?? '') === 'glass' ? 'selected' : '' ?>>Glass</option>
                            </select>
                        </div>
                        <div class="ahp-theme-presets" id="ahpThemePresets" aria-label="Theme preview presets"></div>
                        <div class="ahp-style-section-title">Custom colors</div>
                        <?= ahp_color_field('toolAccentColor', 'Accent / Active Color', $layout['theme']['accentColor'] ?? '#111827') ?>
                        <?= ahp_color_field('toolAccentGreen', 'Hero Value Green', $layout['theme']['accentGreen'] ?? '#22c55e') ?>
                        <?= ahp_color_field('toolSurfaceColor', 'Card Surface', $layout['theme']['surfaceColor'] ?? '#ffffff') ?>
                        <?= ahp_color_field('toolWelcomeStart', 'Avatar Gradient Start', $layout['theme']['welcomeStart'] ?? '#fbbf24') ?>
                        <?= ahp_color_field('toolWelcomeEnd', 'Avatar Gradient End', $layout['theme']['welcomeEnd'] ?? '#f97316') ?>
                        <?= ahp_color_field('toolPhoneBg', 'Phone Background', $layout['theme']['phoneBg'] ?? '#f3f4f6') ?>
                        <?= ahp_color_field('toolAlertBg', 'Alerts Background', $layout['theme']['alertBg'] ?? '#ffffff') ?>
                        <?= ahp_color_field('toolNavActiveColor', 'Nav Active Color', $layout['theme']['navActiveColor'] ?? '#111827') ?>
                        <?= ahp_color_field('toolTextPrimary', 'Primary Text Color', $layout['theme']['textPrimary'] ?? '#111827') ?>
                        <?= ahp_color_field('toolTextMuted', 'Muted Text Color', $layout['theme']['textMuted'] ?? '#6b7280') ?>
                        <?= ahp_color_field('toolNavBg', 'Bottom Nav Background', $layout['theme']['navBg'] ?? '#ffffff') ?>
                    </div>

                    <div class="ahp-tool-section" data-tool="profile">
                        <div class="ahp-tool-title">Profile Page</div>
                        <p class="ahp-tool-hint">Name, email, phone, and member ID are loaded from your account. Language and notifications can be customized here.</p>
                        <div class="ahp-field">
                            <label for="toolProfileName">Display name</label>
                            <input type="text" id="toolProfileName" class="ahp-readonly-field" value="<?= ahp_h($loginUserProfile['name'] ?? ($layout['profilePage']['name'] ?? 'Profile')) ?>" readonly>
                        </div>
                        <div class="ahp-field">
                            <label for="toolProfileEmail">Email</label>
                            <input type="email" id="toolProfileEmail" class="ahp-readonly-field" value="<?= ahp_h($loginUserProfile['email'] ?? ($layout['profilePage']['email'] ?? $userEmail)) ?>" readonly>
                        </div>
                        <div class="ahp-field">
                            <label for="toolProfilePhone">Phone</label>
                            <input type="text" id="toolProfilePhone" class="ahp-readonly-field" value="<?= ahp_h($loginUserProfile['phone'] ?? ($layout['profilePage']['phone'] ?? '')) ?>" readonly placeholder="Not set in account">
                        </div>
                        <div class="ahp-field">
                            <label for="toolProfileMemberId">Member ID</label>
                            <input type="text" id="toolProfileMemberId" class="ahp-readonly-field" value="<?= ahp_h($loginUserProfile['memberId'] ?? ($layout['profilePage']['memberId'] ?? ('EF-' . (int) $userId))) ?>" readonly>
                        </div>
                        <div class="ahp-field">
                            <label for="toolProfileLanguage">Language</label>
                            <select id="toolProfileLanguage">
                                <?php
                                $profileLang = $layout['profilePage']['language'] ?? 'English (India)';
                                $languages = array('English (India)', 'English (US)', 'Hindi', 'Arabic', 'French', 'Spanish');
                                foreach ($languages as $langOption):
                                ?>
                                <option value="<?= ahp_h($langOption) ?>" <?= $profileLang === $langOption ? 'selected' : '' ?>><?= ahp_h($langOption) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="ahp-field">
                            <label class="ahp-check-label">
                                <input type="checkbox" id="toolProfileNotifications" <?= !empty($layout['profilePage']['notificationsEnabled']) ? 'checked' : '' ?>>
                                Enable notifications
                            </label>
                        </div>
                    </div>

                    <div class="ahp-tool-section" data-tool="navigation">
                        <div class="ahp-tool-title">Bottom Navigation</div>
                        <p class="ahp-tool-hint">Pick a tab, edit its label, and choose an icon. Tapping tabs in the phone switches pages.</p>
                        <div class="ahp-field">
                            <label for="toolNavEditTab">Edit tab</label>
                            <select id="toolNavEditTab"></select>
                        </div>
                        <div class="ahp-field">
                            <label for="toolNavActive">Active tab</label>
                            <select id="toolNavActive"></select>
                        </div>
                        <div id="ahpNavEditor"></div>
                    </div>

                    <div class="ahp-tool-section" data-tool="schema">
                        <div class="ahp-tool-title">App Home Schema</div>
                        <p class="ahp-tool-hint">Click <strong>Save</strong> to publish Schema <strong>v3.0.0</strong> from the mobile preview. Fetch it via the API URL below.</p>
                        <div class="ahp-schema-box">
                            <div>
                                <label class="ahp-schema-label" for="ahpSchemaApiUrl">Schema API URL</label>
                                <input type="text" class="ahp-schema-api-url" id="ahpSchemaApiUrl" readonly value="<?= ahp_h($schemaApiUrl ?? '') ?>">
                            </div>
                            <div class="ahp-schema-api-actions">
                                <button type="button" class="ahp-tool-btn" id="ahpCopyApiUrlBtn"><i class="fas fa-copy"></i> Copy URL</button>
                                <button type="button" class="ahp-tool-btn" id="ahpCopySchemaBtn"><i class="fas fa-code"></i> Copy JSON</button>
                                <a class="ahp-tool-btn ahp-schema-action-btn" id="ahpOpenApiBtn" href="<?= ahp_h($schemaApiUrl ?? '#') ?>" target="_blank" rel="noopener">
                                    <i class="fas fa-external-link-alt"></i> Open
                                </a>
                            </div>
                            <div>
                                <label class="ahp-schema-label" for="ahpSchemaOutput">Generated schema (live preview)</label>
                                <textarea class="ahp-schema-output" id="ahpSchemaOutput" spellcheck="false" readonly></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        </div><!-- /.ahp-workspace -->
    </div>
</div>

<div class="ahp-toast" id="ahpToast"></div>

<script>
window.AHP_CONFIG = {
    layout: <?= $layoutJson ?>,
    defaultLayout: <?= $defaultLayoutJson ?>,
    hasSavedLayout: <?= !empty($settingsMeta['has_layout']) ? 'true' : 'false' ?>,
    settingsMeta: <?= json_encode($settingsMeta ?? array(), JSON_UNESCAPED_UNICODE) ?>,
    schema: <?= isset($schema) && is_array($schema) && !empty($schema) ? json_encode($schema, JSON_UNESCAPED_UNICODE) : 'null' ?>,
    userEmail: <?= json_encode($userEmail) ?>,
    userId: <?= (int) $userId ?>,
    loginUserProfile: <?= json_encode($loginUserProfile ?? array(), JSON_UNESCAPED_UNICODE) ?>,
    apiBase: <?= json_encode($apiBase) ?>,
    filesBase: <?= json_encode(rtrim(base_url('files'), '/')) ?>,
    schemaApiUrl: <?= json_encode($schemaApiUrl ?? '', JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
    iotApiUrl: <?= json_encode($iotApiUrl ?? '', JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
    iotAccessToken: <?= json_encode($iotAccessToken ?? '', JSON_UNESCAPED_UNICODE) ?>,
    dashboardUrl: <?= json_encode($dashboardUrl) ?>,
    logoutUrl: <?= json_encode(site_url('sd-logout')) ?>,
    welcomeUrl: <?= json_encode(site_url('welcome')) ?>,
    csrfName: <?= json_encode($csrfName) ?>,
    csrfHash: <?= json_encode($csrfHash) ?>,
    logoUrl: <?= json_encode(base_url('assets/images/efactor-logo.jpg')) ?>,
    phoneWidth: 375,
    phoneHeight: 812,
    iotSduiBundled: <?= $ahpIotSduiReadable ? 'true' : 'false' ?>
};
</script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<?= ahp_script_tag('assets/js/app_home_schema.js', 'ahpJsSchema') ?>
<?= ahp_script_tag('assets/js/app_home_iot_sdui.js', 'ahpJsIotSdui') ?>
<?= ahp_script_tag('assets/js/app_home_page.js', 'ahpJsApp') ?>
</body>
</html>
