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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Home Page — eFactor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/app_home_page.css') ?>?v=34">
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
                <button type="button" class="ahp-menu-item" data-menu="components">
                    <i class="fas fa-puzzle-piece"></i>
                    <span>Components<small>Welcome and alerts</small></span>
                </button>
            </nav>
            <p class="ahp-nav-divider">Content</p>
            <nav class="ahp-menu ahp-menu-secondary">
                <button type="button" class="ahp-menu-item" data-menu="pages">
                    <i class="fas fa-file-alt"></i>
                    <span>Pages<small>Page linking</small></span>
                </button>
                <button type="button" class="ahp-menu-item" data-menu="blocks">
                    <i class="fas fa-cubes"></i>
                    <span>Blocks<small>Add new blocks</small></span>
                </button>
                <button type="button" class="ahp-menu-item" data-menu="cards">
                    <i class="fas fa-clone"></i>
                    <span>Cards<small>Edit card faces</small></span>
                </button>
                <button type="button" class="ahp-menu-item" data-menu="devices">
                    <i class="fas fa-th"></i>
                    <span>Devices<small>IoT device cubes</small></span>
                </button>
                <button type="button" class="ahp-menu-item" data-menu="navigation">
                    <i class="fas fa-compass"></i>
                    <span>Navigation<small>Bottom nav bar</small></span>
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
                    <div class="ahp-phone" id="ahpPhone">
                        <div class="ahp-phone-notch"></div>
                        <div class="ahp-phone-screen">
                            <div class="ahp-phone-scroll" id="ahpPhoneScroll">
                                <!-- Home page -->
                                <div class="ahp-mobile ahp-page" id="ahpHomePage" data-page="home">
                                    <header class="ahp-m-header">
                                        <div class="ahp-m-brand">
                                            <div class="ahp-m-logo" id="ahpLogoIcon"><i class="fas fa-crosshairs"></i></div>
                                            <div>
                                                <h1 id="ahpHeaderTitle"><?= ahp_h($layout['header']['title'] ?? 'eFactor · IoE') ?></h1>
                                                <p id="ahpHeaderSub"><?= ahp_h($layout['header']['subtitle'] ?? 'Smart insights & tools') ?></p>
                                            </div>
                                        </div>
                                        <div class="ahp-m-more"><i class="fas fa-ellipsis-h"></i></div>
                                    </header>

                                    <div class="ahp-welcome" id="ahpWelcome">
                                        <div class="ahp-welcome-text">
                                            <h2 id="ahpWelcomeGreet"><?= ahp_h($layout['welcome']['greeting'] ?? '👋 Welcome back') ?></h2>
                                            <div class="email" id="ahpWelcomeEmail"><?= ahp_h($layout['welcome']['email'] ?? $userEmail) ?></div>
                                            <div class="caption" id="ahpWelcomeCaption"><?= ahp_h($layout['welcome']['caption'] ?? 'Manage your ecosystem') ?></div>
                                        </div>
                                        <div class="ahp-welcome-icon"><i class="fas fa-project-diagram"></i></div>
                                    </div>

                                    <div class="ahp-alerts" id="ahpAlerts">
                                        <div class="ahp-alerts-icon"><i class="fas fa-bell"></i></div>
                                        <div class="ahp-alerts-text">
                                            <h3 id="ahpAlertCount">Live Alerts <?= (int) ($layout['alerts']['count'] ?? 0) ?></h3>
                                            <p id="ahpAlertMeta">Sat, 27 Jun 2026 · <?= (int) ($layout['alerts']['newUsers'] ?? 0) ?> new users</p>
                                        </div>
                                        <div class="ahp-alerts-badge" id="ahpAlertsBadge"><span class="dot"></span> <span id="ahpBadgeText">real-time</span></div>
                                    </div>

                                    <div class="ahp-grid" id="ahpGrid"></div>
                                </div>

                                <!-- Devices page -->
                                <div class="ahp-mobile ahp-page ahp-is-hidden" id="ahpDevicesPage" data-page="devices">
                                    <header class="ahp-dev-header">
                                        <button type="button" class="ahp-dev-header-btn" id="ahpDevLeftIcon" aria-label="Scan"><i class="fas fa-qrcode"></i></button>
                                        <div class="ahp-dev-header-text">
                                            <h1 id="ahpDevTitle">devices · iot</h1>
                                            <p id="ahpDevSub">Tap a cube to control.</p>
                                        </div>
                                        <button type="button" class="ahp-dev-header-btn" id="ahpDevRightIcon" aria-label="More"><i class="fas fa-ellipsis-h"></i></button>
                                    </header>
                                    <div class="ahp-dev-status" id="ahpDevStatus">
                                        <span class="ahp-dev-pill ahp-dev-pill-online" id="ahpDevOnline"><i class="fas fa-circle"></i> <span>11 online</span></span>
                                        <span class="ahp-dev-pill ahp-dev-pill-offline" id="ahpDevOffline"><i class="fas fa-circle"></i> <span>1 offline</span></span>
                                    </div>
                                    <div class="ahp-dev-grid" id="ahpDevicesGrid"></div>
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
                        <div class="ahp-tool-title">Layout Settings</div>
                        <p class="ahp-tool-hint">Drag cards in the preview to reorder. Each card rotates its own 4 faces independently.</p>
                        <div class="ahp-field">
                            <label>Header Title</label>
                            <input type="text" id="toolHeaderTitle" value="<?= ahp_h($layout['header']['title'] ?? '') ?>">
                        </div>
                        <div class="ahp-field">
                            <label>Header Subtitle</label>
                            <input type="text" id="toolHeaderSub" value="<?= ahp_h($layout['header']['subtitle'] ?? '') ?>">
                        </div>
                        <div class="ahp-field">
                            <label>Logo Icon (FA class)</label>
                            <input type="text" id="toolLogoIcon" value="<?= ahp_h($layout['theme']['logoIcon'] ?? 'fa-crosshairs') ?>">
                        </div>
                        <div class="ahp-field ahp-range-field">
                            <div class="ahp-range-head">
                                <label for="toolGridGap">Grid Gap</label>
                                <span class="ahp-range-val" id="toolGridGapVal"><?= (int) ($layout['theme']['gridGap'] ?? 12) ?>px</span>
                            </div>
                            <div class="ahp-range-control">
                                <input type="range" id="toolGridGap" min="6" max="24" step="1" value="<?= (int) ($layout['theme']['gridGap'] ?? 12) ?>">
                            </div>
                        </div>
                        <div class="ahp-field">
                            <label>Card Rotation Speed</label>
                            <select id="toolCardSpeed">
                                <option value="8" <?= ((int)($layout['theme']['cardSpeed'] ?? 12) === 8) ? 'selected' : '' ?>>Fast (8s cycle)</option>
                                <option value="12" <?= ((int)($layout['theme']['cardSpeed'] ?? 12) === 12) ? 'selected' : '' ?>>Normal (12s cycle)</option>
                                <option value="16" <?= ((int)($layout['theme']['cardSpeed'] ?? 12) === 16) ? 'selected' : '' ?>>Slow (16s cycle)</option>
                            </select>
                        </div>
                        <div class="ahp-field ahp-check-field">
                            <label><input type="checkbox" id="toolShowWelcome" <?= !empty($layout['theme']['showWelcome']) ? 'checked' : '' ?>> Show Welcome Banner</label>
                        </div>
                        <div class="ahp-field ahp-check-field">
                            <label><input type="checkbox" id="toolShowAlerts" <?= !empty($layout['theme']['showAlerts']) ? 'checked' : '' ?>> Show Live Alerts Bar</label>
                        </div>
                        <div class="ahp-field ahp-check-field">
                            <label><input type="checkbox" id="toolShowBottomNav" <?= !empty($layout['theme']['showBottomNav']) ? 'checked' : '' ?>> Show Bottom Navigation</label>
                        </div>
                        <button type="button" class="ahp-btn ahp-btn-ghost" id="ahpResetLayout" style="width:100%;margin-top:8px">
                            <i class="fas fa-undo"></i> Reset to Default
                        </button>
                    </div>

                    <div class="ahp-tool-section" data-tool="components">
                        <div class="ahp-tool-title">Components</div>
                        <p class="ahp-tool-hint">Manage welcome banner, alerts bar, and grid cards.</p>
                        <div class="ahp-field">
                            <label>Welcome Greeting</label>
                            <input type="text" id="toolWelcomeGreet" value="<?= ahp_h($layout['welcome']['greeting'] ?? '') ?>">
                        </div>
                        <div class="ahp-field">
                            <label>User Email</label>
                            <input type="text" id="toolWelcomeEmail" value="<?= ahp_h($layout['welcome']['email'] ?? $userEmail) ?>">
                        </div>
                        <div class="ahp-field">
                            <label>Welcome Caption</label>
                            <input type="text" id="toolWelcomeCaption" value="<?= ahp_h($layout['welcome']['caption'] ?? '') ?>">
                        </div>
                        <div class="ahp-field">
                            <label>Alert Count</label>
                            <input type="number" id="toolAlertCount" value="<?= (int) ($layout['alerts']['count'] ?? 0) ?>">
                        </div>
                        <div class="ahp-field">
                            <label>New Users</label>
                            <input type="number" id="toolAlertUsers" value="<?= (int) ($layout['alerts']['newUsers'] ?? 0) ?>">
                        </div>
                        <div class="ahp-field">
                            <label>Badge Text</label>
                            <input type="text" id="toolAlertBadge" value="<?= ahp_h($layout['alerts']['badgeText'] ?? 'real-time') ?>">
                        </div>
                        <div class="ahp-field ahp-check-field">
                            <label><input type="checkbox" id="toolShowAlertBadge" <?= !isset($layout['alerts']['showBadge']) || !empty($layout['alerts']['showBadge']) ? 'checked' : '' ?>> Show Real-time Badge</label>
                        </div>
                    </div>

                    <div class="ahp-tool-section" data-tool="pages">
                        <div class="ahp-tool-title">Pages</div>
                        <p class="ahp-tool-hint">Link cards to app pages.</p>
                        <div class="ahp-card-picker" id="ahpPageCardPicker"></div>
                        <div class="ahp-field" style="margin-top:12px">
                            <label>Page Route</label>
                            <input type="text" id="toolPageRoute" placeholder="/dashboard, /devices, etc.">
                        </div>
                        <div class="ahp-field">
                            <label>Page Label</label>
                            <input type="text" id="toolPageLabel" placeholder="Display name">
                        </div>
                    </div>

                    <div class="ahp-tool-section" data-tool="blocks">
                        <div class="ahp-tool-title">Add Blocks</div>
                        <p class="ahp-tool-hint">Click a block type to add it to the next free grid slot. Drag the grip icon on a card to reorder.</p>
                        <p class="ahp-block-count" id="ahpBlockCount">0 blocks · scrollable</p>
                        <div class="ahp-block-list">
                            <div class="ahp-block-item" data-type="stat"><i class="fas fa-chart-bar"></i> Stat Block</div>
                            <div class="ahp-block-item" data-type="link"><i class="fas fa-link"></i> Link Block</div>
                            <div class="ahp-block-item" data-type="image"><i class="fas fa-image"></i> Image Block</div>
                            <div class="ahp-block-item" data-type="add"><i class="fas fa-plus"></i> Action Block</div>
                        </div>
                    </div>

                    <div class="ahp-tool-section" data-tool="cards">
                        <div class="ahp-tool-title">Edit Cards</div>
                        <p class="ahp-tool-hint">Select a card, then pick Face 1–4 to style color, icon, text, fonts, and border radius per face. Click <strong>Save</strong> to keep changes.</p>
                        <div class="ahp-card-picker" id="ahpCardPicker"></div>
                        <div id="ahpCardEditor" style="margin-top:12px"></div>
                    </div>

                    <div class="ahp-tool-section" data-tool="theme">
                        <div class="ahp-tool-title">Theme</div>
                        <p class="ahp-tool-hint">Pick a color swatch or type a hex code (e.g. #7c3aed). Changes apply instantly.</p>
                        <div class="ahp-field">
                            <label>Background Preset</label>
                            <select id="toolBgStyle">
                                <option value="dark" <?= ($layout['theme']['bgStyle'] ?? 'dark') === 'dark' ? 'selected' : '' ?>>Dark (Default)</option>
                                <option value="midnight" <?= ($layout['theme']['bgStyle'] ?? '') === 'midnight' ? 'selected' : '' ?>>Midnight</option>
                                <option value="slate" <?= ($layout['theme']['bgStyle'] ?? '') === 'slate' ? 'selected' : '' ?>>Slate</option>
                            </select>
                        </div>
                        <?= ahp_color_field('toolAccentColor', 'Accent Color', $layout['theme']['accentColor'] ?? '#7c3aed') ?>
                        <?= ahp_color_field('toolWelcomeStart', 'Welcome Gradient Start', $layout['theme']['welcomeStart'] ?? '#7c3aed') ?>
                        <?= ahp_color_field('toolWelcomeEnd', 'Welcome Gradient End', $layout['theme']['welcomeEnd'] ?? '#4f46e5') ?>
                        <?= ahp_color_field('toolPhoneBg', 'Phone Background', $layout['theme']['phoneBg'] ?? '#0b0f1a') ?>
                        <?= ahp_color_field('toolAlertBg', 'Alerts Bar Background', $layout['theme']['alertBg'] ?? '#151b2e') ?>
                        <?= ahp_color_field('toolNavActiveColor', 'Nav Active Color', $layout['theme']['navActiveColor'] ?? '#3b82f6') ?>
                    </div>

                    <div class="ahp-tool-section" data-tool="devices">
                        <div class="ahp-tool-title">Devices Page</div>
                        <p class="ahp-tool-hint">Customize the Devices IoT screen. Tap Devices in the bottom nav to preview. Click Save to keep changes.</p>
                        <div class="ahp-field">
                            <label for="toolDevTitle">Page title</label>
                            <input type="text" id="toolDevTitle" value="">
                        </div>
                        <div class="ahp-field">
                            <label for="toolDevSub">Subtitle</label>
                            <input type="text" id="toolDevSub" value="">
                        </div>
                        <div class="ahp-field">
                            <label for="toolDevOnline">Online count</label>
                            <input type="number" id="toolDevOnline" min="0" value="0">
                        </div>
                        <div class="ahp-field">
                            <label for="toolDevOffline">Offline count</label>
                            <input type="number" id="toolDevOffline" min="0" value="0">
                        </div>
                        <div class="ahp-style-section-title" style="margin-top:12px">Devices</div>
                        <p class="ahp-block-count" id="ahpDeviceCount">0 devices</p>
                        <div class="ahp-card-picker" id="ahpDevicePicker"></div>
                        <button type="button" class="ahp-tool-btn" id="ahpAddDeviceBtn" style="margin:10px 0"><i class="fas fa-plus"></i> Add device</button>
                        <div id="ahpDeviceEditor"></div>
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
                        <p class="ahp-tool-hint">Open this API after Save to fetch the saved App Home schema JSON.</p>
                        <div class="ahp-schema-box">
                            <label class="ahp-schema-label" for="ahpSchemaApiUrl">Schema API</label>
                            <input type="text" id="ahpSchemaApiUrl" class="ahp-schema-api-url" readonly value="<?= ahp_h($schemaApiUrl ?? '') ?>">
                            <div class="ahp-schema-api-actions">
                                <button type="button" class="ahp-tool-btn ahp-schema-action-btn" id="ahpCopyApiUrlBtn">
                                    <i class="fas fa-link"></i>
                                    <span>Copy URL</span>
                                </button>
                                <a class="ahp-tool-btn ahp-schema-action-btn" id="ahpOpenApiBtn" href="<?= ahp_h($schemaApiUrl ?? '#') ?>" target="_blank" rel="noopener">
                                    <i class="fas fa-external-link-alt"></i>
                                    <span>Open</span>
                                </a>
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
    schema: <?= isset($schema) && is_array($schema) && !empty($schema) ? json_encode($schema, JSON_UNESCAPED_UNICODE) : 'null' ?>,
    userEmail: <?= json_encode($userEmail) ?>,
    userId: <?= (int) $userId ?>,
    apiBase: <?= json_encode($apiBase) ?>,
    schemaApiUrl: <?= json_encode($schemaApiUrl ?? '', JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
    dashboardUrl: <?= json_encode($dashboardUrl) ?>,
    phoneWidth: 375,
    phoneHeight: 812
};
</script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="<?= base_url('assets/js/app_home_page.js') ?>?v=33"></script>
</body>
</html>
