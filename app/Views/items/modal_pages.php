<?php
$pageOptions = array();

if (!empty($page_names) && is_array($page_names)) {
    foreach ($page_names as $pageRow) {
        $rawName = trim((string) ($pageRow['page_names'] ?? ''));
        if ($rawName === '') {
            continue;
        }

        $names = strpos($rawName, ',') !== false
            ? explode(',', $rawName)
            : array($rawName);

        foreach ($names as $pageName) {
            $pageName = trim($pageName);
            if ($pageName !== '') {
                $pageOptions[] = $pageName;
            }
        }
    }
}

$pageOptions = array_values(array_unique($pageOptions));
$hasPages = !empty($pageOptions);

/*
 * NOTE: Kept the original backend value "paring" to avoid breaking saved data/routes.
 * Only the label is corrected to "Pairing" in the UI.
 */
$pageExtensionOptions = array(
    'test' => 'Test',
    'config' => 'Config',
    'paring' => 'Pairing',
    'operation' => 'Operation'
);

$pagesDataUrl = get_uri("items/modal_pages_data/" . $modal_id . "?modelno=" . rawurlencode($modelno));
$deletePageUrl = get_uri("items/delete_saved_page");
$reorderPageUrl = get_uri("items/reorder_saved_pages");
$deletePageUrlAlt = get_uri("modal_pages_controller/delete_saved_page");
$reorderPageUrlAlt = get_uri("modal_pages_controller/reorder_saved_pages");
$linkingPageUrl = get_uri("items/api_page_linking/" . (int) $modal_id . "?modelno=" . rawurlencode($modelno));
$backUrl = get_uri("items/viewapis/" . (int) $modal_id . "?modelno=" . rawurlencode((string) $modelno));
$viewApisUrl = get_uri("items/viewapis/" . (int) $modal_id . "?modelno=" . rawurlencode($modelno));
$csrfName = function_exists('csrf_token') ? csrf_token() : 'rise_csrf_token';
$csrfHash = function_exists('csrf_hash') ? csrf_hash() : '';
?>

<div class="page-config-shell">
    <header class="page-config-header">
        <div class="page-config-header-main">
            <a href="<?php echo esc($backUrl); ?>" id="backButton" class="btn page-back-button" aria-label="Go back">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
            </a>

            <div class="page-config-title">
                <div class="page-config-icon" aria-hidden="true">
                    <i class="fa fa-clone"></i>
                </div>
                <div class="page-config-title-text">
                    <div class="page-config-eyebrow">Model page configuration</div>
                    <h4><?php echo esc($modelno); ?></h4>
                    <p class="page-config-subtitle">Create, arrange, and connect pages for this product model.</p>
                </div>
            </div>
        </div>

        <div class="header-actions">
            <div class="page-count-pill" aria-label="Saved page count">
                <span class="page-count-icon"><i class="fa fa-file-o" aria-hidden="true"></i></span>
                <span class="page-count-copy">
                    <strong id="pageCount"><?php echo count($pageOptions); ?></strong>
                    <small>Saved pages</small>
                </span>
            </div>

            <a href="<?php echo $linkingPageUrl; ?>"
               id="link-pages"
               class="btn linking-page-button<?php echo $hasPages ? '' : ' disabled'; ?>"
               <?php echo $hasPages ? '' : 'aria-disabled="true" tabindex="-1"'; ?>>
                <span>Open Page Linking</span>
                <i class="fa fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </header>

    <section class="page-workflow" aria-label="Page configuration steps">
        <div class="workflow-step is-active">
            <span class="workflow-step-number">1</span>
            <span><strong>Create</strong><small>Add page name and type</small></span>
        </div>
        <div class="workflow-connector" aria-hidden="true"></div>
        <div class="workflow-step">
            <span class="workflow-step-number">2</span>
            <span><strong>Arrange</strong><small>Drag pages into order</small></span>
        </div>
        <div class="workflow-connector" aria-hidden="true"></div>
        <div class="workflow-step">
            <span class="workflow-step-number">3</span>
            <span><strong>Link</strong><small>Connect APIs to pages</small></span>
        </div>
    </section>

    <div id="pageStatus" class="page-status" aria-live="polite" style="display:none;"></div>

    <main class="page-config-grid">
        <section id="tab" class="page-panel saved-pages-panel">
            <div class="panel-header">
                <div class="panel-heading-copy">
                    <span class="panel-icon"><i class="fa fa-list-ol" aria-hidden="true"></i></span>
                    <div>
                        <h5>Saved Pages</h5>
                        <p>Drag a page to update its display sequence.</p>
                    </div>
                </div>
                <span class="auto-save-badge"><i class="fa fa-check-circle" aria-hidden="true"></i> Auto-saves order</span>
            </div>

            <div class="drag-help">
                <i class="fa fa-hand-grab-o" aria-hidden="true"></i>
                <span>Hold a page card and move it to the required position.</span>
            </div>

            <ul id="page-list" aria-label="Saved pages"></ul>
        </section>

        <?php echo form_open_multipart(get_uri("items/save_pages"), array("id" => "file-form2", "class" => "general-form page-form page-panel", "role" => "form")); ?>
            <input type="hidden" name="modelno" id="modelno" value="<?php echo esc($modelno); ?>" />
            <input type="hidden" name="user_id" id="user_id" value="<?php echo esc($user_id); ?>" />
            <input type="hidden" name="modal_id" id="modal_id" value="<?php echo esc($modal_id); ?>" />
            <input type="hidden" name="page_names_combined" id="page_names_combined" value="" />

            <div class="panel-header form-panel-header">
                <div class="panel-heading-copy">
                    <span class="panel-icon panel-icon-accent"><i class="fa fa-plus" aria-hidden="true"></i></span>
                    <div>
                        <h5>Add New Pages</h5>
                        <p>Add one page or multiple pages in a single save.</p>
                    </div>
                </div>

                <button type="button" id="add-more" class="btn add-row-button">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    <span>Add another</span>
                </button>
            </div>

            <div class="form-tip">
                <i class="fa fa-info-circle" aria-hidden="true"></i>
                <span>The selected extension is appended automatically, for example <strong>Home.config</strong>.</span>
            </div>

            <div id="form-items" class="page-form-list">
                <div class="page-row page-row-fixed">
                    <div class="row-number" aria-hidden="true">1</div>

                    <div class="form-group page-name-field">
                        <label for="input-box-1">Page name</label>
                        <div class="input-shell">
                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                            <input type="text" name="input_box[]" id="input-box-1" class="form-control" placeholder="Example: Home" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="form-group page-extension-field">
                        <label for="dropdown-1">Page type</label>
                        <div class="select-shell">
                            <select name="dropdown[]" id="dropdown-1" class="form-control" required>
                                <?php foreach ($pageExtensionOptions as $extensionValue => $extensionLabel): ?>
                                    <option value="<?php echo esc($extensionValue, 'attr'); ?>">.<?php echo esc($extensionLabel); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa fa-chevron-down" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <div class="save-note">
                    <i class="fa fa-database" aria-hidden="true"></i>
                    <span>Existing pages will remain unchanged.</span>
                </div>

                <button type="submit" class="btn primary-submit">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    <span>Save Pages</span>
                </button>
            </div>
        <?php echo form_close(); ?>
    </main>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
.page-config-shell,
.page-config-shell * {
    box-sizing: border-box;
}

/* .page-config-shell {
    --pc-primary: #2563eb;
    --pc-primary-dark: #1d4ed8;
    --pc-primary-soft: #eff6ff;
    --pc-accent: #0f766e;
    --pc-accent-soft: #ecfdf5;
    --pc-text: #172033;
    --pc-muted: #657188;
    --pc-border: #dfe5ef;
    --pc-border-strong: #cbd5e1;
    --pc-surface: #ffffff;
    --pc-surface-soft: #f8fafc;
    --pc-page: #f3f6fb;
    --pc-danger: #dc2626;
    --pc-danger-soft: #fef2f2;
    --pc-success: #15803d;
    --pc-success-soft: #f0fdf4;
    --pc-warning: #b45309;
    --pc-warning-soft: #fffbeb;
    --pc-shadow-sm: 0 5px 16px rgba(15, 23, 42, 0.06);
    --pc-shadow: 0 18px 48px rgba(15, 23, 42, 0.10);

    width: min(100%, 1240px);
    margin: 0 auto;
    padding: clamp(16px, 2.2vw, 28px);
    border: 1px solid #e7ebf2;
    border-radius: 24px;
    color: var(--pc-text);
    background:
        radial-gradient(circle at 6% 0%, rgba(37, 99, 235, 0.10), transparent 25%),
        radial-gradient(circle at 100% 10%, rgba(15, 118, 110, 0.08), transparent 24%),
        var(--pc-page);
    font-family: inherit;
} */

    .page-config-shell {
    --pc-primary: #0f766e;
    --pc-primary-dark: #115e59;
    --pc-primary-soft: #ecfdf5;

    --pc-accent: #15803d;
    --pc-accent-soft: #f0fdf4;

    --pc-text: #1f2937;
    --pc-muted: #6b7280;

    --pc-border: #d6d3d1;
    --pc-border-strong: #b8b2a9;

    --pc-surface: #ffffff;
    --pc-surface-soft: #fafaf9;
    --pc-page: #f5f5f4;

    --pc-danger: #dc2626;
    --pc-danger-soft: #fef2f2;

    --pc-success: #15803d;
    --pc-success-soft: #f0fdf4;

    --pc-warning: #b45309;
    --pc-warning-soft: #fffbeb;

    --pc-shadow-sm: 0 5px 16px rgba(0, 0, 0, 0.06);
    --pc-shadow: 0 18px 48px rgba(0, 0, 0, 0.10);

    width: min(100%, 1240px);
    margin: 0 auto;
    padding: clamp(16px, 2.2vw, 28px);
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    color: var(--pc-text);
    background:
        radial-gradient(circle at 6% 0%, rgba(15, 118, 110, 0.08), transparent 25%),
        radial-gradient(circle at 100% 10%, rgba(21, 128, 61, 0.06), transparent 24%),
        var(--pc-page);
    font-family: inherit;
}

.page-config-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    padding: clamp(18px, 2.3vw, 26px);
    border: 1px solid rgba(223, 229, 239, 0.95);
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.96);
    box-shadow: var(--pc-shadow);
}

.page-config-header-main,
.page-config-title,
.header-actions,
.page-count-pill,
.page-count-copy,
.panel-heading-copy,
.workflow-step,
.form-footer,
.save-note,
.drag-help,
.form-tip {
    display: flex;
    align-items: center;
}

.page-config-header-main {
    min-width: 0;
    gap: 16px;
}

.page-back-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    flex: 0 0 44px;
    padding: 0;
    border: 1px solid var(--pc-border);
    border-radius: 13px;
    color: #475569;
    background: var(--pc-surface);
    text-decoration: none;
    box-shadow: var(--pc-shadow-sm);
    transition: transform 0.18s ease, border-color 0.18s ease, color 0.18s ease, background 0.18s ease;
}

.page-back-button:hover,
.page-back-button:focus {
    color: var(--pc-primary);
    border-color: #bfdbfe;
    background: var(--pc-primary-soft);
    text-decoration: none;
    transform: translateX(-2px);
}

.page-config-title {
    min-width: 0;
    gap: 14px;
}

.page-config-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    flex: 0 0 52px;
    border-radius: 16px;
    color: #ffffff;
    background:linear-gradient(145deg, var(--pc-primary), #8ca894);
    /* background: linear-gradient(145deg, var(--pc-primary), #4f46e5); */
    /* box-shadow: 0 12px 26px rgba(37, 99, 235, 0.25); */
    box-shadow:0 12px 26px rgb(75 173 95 / 25%);
    font-size: 20px;
}

.page-config-title-text {
    min-width: 0;
}

.page-config-eyebrow {
    margin-bottom: 4px;
    color: var(--pc-primary);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.09em;
    line-height: 1.2;
    text-transform: uppercase;
}

.page-config-header h4,
.panel-header h5 {
    margin: 0;
    color: var(--pc-text);
    font-weight: 800;
}

.page-config-header h4 {
    font-size: clamp(20px, 2vw, 25px);
    line-height: 1.25;
    overflow-wrap: anywhere;
}

.page-config-subtitle {
    margin: 5px 0 0;
    color: var(--pc-muted);
    font-size: 13px;
    line-height: 1.45;
}

.header-actions {
    justify-content: flex-end;
    flex: 0 0 auto;
    gap: 12px;
}

.page-count-pill {
    gap: 10px;
    min-width: 142px;
    padding: 9px 13px;
    border: 1px solid var(--pc-border);
    border-radius: 14px;
    background: var(--pc-surface-soft);
}

.page-count-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    flex: 0 0 34px;
    border-radius: 10px;
    color: var(--pc-primary);
    background: var(--pc-primary-soft);
}

.page-count-copy {
    align-items: flex-start;
    flex-direction: column;
    gap: 1px;
}

.page-count-copy strong {
    color: var(--pc-text);
    font-size: 19px;
    font-weight: 900;
    line-height: 1;
}

.page-count-copy small {
    color: var(--pc-muted);
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}

.linking-page-button,
.add-row-button,
.primary-submit,
.remove-page-row {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    border-radius: 13px;
    font-weight: 800;
    line-height: 1.2;
    transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease, border-color 0.18s ease;
}

.linking-page-button {
    min-height: 46px;
    padding: 11px 16px;
    border: 1px solid var(--pc-primary);
    color: #ffffff;
    text-decoration:none;
    background: linear-gradient(135deg, var(--pc-primary), var(--pc-primary-dark));
    box-shadow: 0 10px 22px rgba(90, 157, 126, 0.22);
}

.linking-page-button:hover,
.linking-page-button:focus {
    color: #ffffff;
    background: linear-gradient(135deg, #38875c, var(--pc-primary));
    transform: translateY(-1px);
    box-shadow: 0 14px 28px rgba(54, 123, 80, 0.28);
}

.linking-page-button.disabled,
.linking-page-button[aria-disabled="true"] {
    pointer-events: none;
    color: #94a3b8;
    border-color: var(--pc-border);
    background: #eef2f7;
    box-shadow: none;
}

.page-workflow {
    display: grid;
    grid-template-columns: auto minmax(36px, 1fr) auto minmax(36px, 1fr) auto;
    align-items: center;
    gap: 10px;
    margin: 18px 4px;
    padding: 14px 18px;
    border: 1px solid rgba(223, 229, 239, 0.92);
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.82);
}

.workflow-step {
    gap: 9px;
    min-width: 0;
}

.workflow-step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    flex: 0 0 30px;
    border: 1px solid var(--pc-border-strong);
    border-radius: 50%;
    color: #64748b;
    background: #ffffff;
    font-size: 12px;
    font-weight: 900;
}

.workflow-step.is-active .workflow-step-number {
    color: #ffffff;
    border-color: var(--pc-primary);
    background: var(--pc-primary);
    box-shadow: 0 7px 16px rgba(37, 99, 235, 0.24);
}

.workflow-step > span:last-child {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.workflow-step strong {
    color: var(--pc-text);
    font-size: 12px;
    font-weight: 800;
}

.workflow-step small {
    color: var(--pc-muted);
    font-size: 10px;
    white-space: nowrap;
}

.workflow-connector {
    height: 1px;
    background: linear-gradient(90deg, var(--pc-border-strong), var(--pc-border));
}

.page-status {
    position: relative;
    margin: 0 0 18px;
    padding: 13px 15px 13px 44px;
    border-radius: 14px;
    font-size: 13px;
    font-weight: 750;
    line-height: 1.45;
    box-shadow: var(--pc-shadow-sm);
}

.page-status::before {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    font-family: FontAwesome;
    font-size: 15px;
}

.page-status.is-success {
    color: var(--pc-success);
    border: 1px solid #bbf7d0;
    background: var(--pc-success-soft);
}

.page-status.is-success::before {
    content: "\f058";
}

.page-status.is-error {
    color: var(--pc-danger);
    border: 1px solid #fecaca;
    background: var(--pc-danger-soft);
}

.page-status.is-error::before {
    content: "\f06a";
}

.page-status.is-info {
    color: #1e40af;
    border: 1px solid #bfdbfe;
    background: var(--pc-primary-soft);
}

.page-status.is-info::before {
    content: "\f05a";
}

.page-config-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.08fr) minmax(380px, 0.92fr);
    align-items: start;
    gap: 18px;
}

.page-panel {
    min-width: 0;
    border: 1px solid var(--pc-border);
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.98);
    box-shadow: var(--pc-shadow-sm);
    overflow: hidden;
}

.panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 18px;
    border-bottom: 1px solid var(--pc-border);
}

.panel-heading-copy {
    min-width: 0;
    gap: 12px;
}

.panel-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    flex: 0 0 40px;
    border-radius: 12px;
    color: var(--pc-primary);
    background: var(--pc-primary-soft);
    font-size: 16px;
}

.panel-icon-accent {
    color: var(--pc-accent);
    background: var(--pc-accent-soft);
}

.panel-header h5 {
    font-size: 16px;
    line-height: 1.25;
}

.panel-header p {
    margin: 4px 0 0;
    color: var(--pc-muted);
    font-size: 12px;
    line-height: 1.4;
}

.auto-save-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex: 0 0 auto;
    padding: 7px 9px;
    border: 1px solid #bbf7d0;
    border-radius: 999px;
    color: var(--pc-success);
    background: var(--pc-success-soft);
    font-size: 10px;
    font-weight: 800;
    white-space: nowrap;
}

.drag-help,
.form-tip {
    gap: 8px;
    color: var(--pc-muted);
    font-size: 12px;
    line-height: 1.45;
}

.drag-help {
    padding: 11px 18px;
    border-bottom: 1px solid var(--pc-border);
    background: var(--pc-surface-soft);
}

.drag-help i {
    color: var(--pc-primary);
}

#page-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    min-height: 160px;
    margin: 0;
    padding: 16px;
    list-style: none;
}

#page-list li {
    min-width: 0;
}

#page-list .page-list-item {
    position: relative;
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    align-items: center;
    gap: 11px;
    min-height: 72px;
    padding: 13px;
    border: 1px solid var(--pc-border);
    border-radius: 15px;
    background: #ffffff;
    box-shadow: 0 5px 14px rgba(15, 23, 42, 0.045);
    cursor: grab;
    transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease, background 0.18s ease;
}

#page-list .page-list-item:hover {
    z-index: 1;
    border-color: #bfdbfe;
    transform: translateY(-2px);
    box-shadow: 0 13px 26px rgba(15, 23, 42, 0.10);
}

#page-list .page-list-item.is-dragging {
    opacity: 0.45;
    cursor: grabbing;
    transform: scale(0.98);
}

#page-list .page-list-item.is-drag-over {
    border-color: var(--pc-primary);
    background: var(--pc-primary-soft);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
}

.page-list-leading {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    flex: 0 0 40px;
    border-radius: 12px;
    color: var(--pc-primary);
    background: var(--pc-primary-soft);
}

.page-list-index {
    position: absolute;
    right: -5px;
    top: -5px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 19px;
    height: 19px;
    padding: 0 4px;
    border: 2px solid #ffffff;
    border-radius: 999px;
    color: #ffffff;
    background: var(--pc-primary);
    font-size: 9px;
    font-weight: 900;
}

.page-list-content {
    min-width: 0;
}

.page-list-name {
    display: block;
    min-width: 0;
    color: var(--pc-text);
    font-size: 13px;
    font-weight: 850;
    line-height: 1.35;
    overflow-wrap: anywhere;
}

.page-list-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 5px;
}

.page-extension-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 7px;
    border-radius: 999px;
    color: #475569;
    background: #eef2f7;
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
}

.page-drag-handle {
    color: #94a3b8;
    font-size: 13px;
}

.page-delete-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    padding: 0;
    border: 1px solid transparent;
    border-radius: 11px;
    color: #ef4444;
    background: transparent;
    cursor: pointer;
    transition: color 0.18s ease, background 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
}

.page-delete-button:hover,
.page-delete-button:focus {
    color: var(--pc-danger);
    border-color: #fecaca;
    background: var(--pc-danger-soft);
    transform: scale(1.04);
}

.page-delete-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.empty-page-item {
    grid-column: 1 / -1;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    min-height: 150px;
    padding: 24px;
    border: 1px dashed var(--pc-border-strong);
    border-radius: 15px;
    color: var(--pc-muted);
    background: var(--pc-surface-soft);
    text-align: center;
}

.empty-page-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    margin-bottom: 10px;
    border-radius: 14px;
    color: #94a3b8;
    background: #e9eef5;
    font-size: 17px;
}

.empty-page-item strong {
    color: #475569;
    font-size: 13px;
}

.empty-page-item small {
    margin-top: 4px;
    font-size: 11px;
}

.page-form {
    padding: 0;
}

.form-panel-header {
    background: linear-gradient(180deg, #ffffff, #fbfdfd);
}

.add-row-button {
    min-height: 38px;
    padding: 9px 12px;
    border: 1px solid #a7f3d0;
    color: var(--pc-accent);
    background: var(--pc-accent-soft);
    font-size: 12px;
}

.add-row-button:hover,
.add-row-button:focus {
    color: #ffffff;
    border-color: var(--pc-accent);
    background: var(--pc-accent);
    transform: translateY(-1px);
}

.form-tip {
    margin: 14px 16px 0;
    padding: 10px 12px;
    border: 1px solid #dbeafe;
    border-radius: 12px;
    color: #475569;
    background: #f8fbff;
}

.form-tip i {
    color: var(--pc-primary);
}

.page-form-list {
    display: grid;
    gap: 11px;
    padding: 14px 16px 2px;
}

.page-row {
    display: grid;
    grid-template-columns: 30px minmax(0, 1fr) 150px 40px;
    align-items: end;
    gap: 10px;
    min-width: 0;
    padding: 13px;
    border: 1px solid var(--pc-border);
    border-radius: 14px;
    background: var(--pc-surface-soft);
    animation: pageRowEnter 0.22s ease-out;
}

.page-row-fixed {
    grid-template-columns: 30px minmax(0, 1fr) 150px;
}

@keyframes pageRowEnter {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

.row-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    margin-bottom: 7px;
    border-radius: 10px;
    color: #64748b;
    background: #e9eef5;
    font-size: 11px;
    font-weight: 900;
}

.form-group {
    min-width: 0;
    margin: 0;
}

.form-group label {
    display: block;
    margin: 0 0 7px;
    color: #475569;
    font-size: 11px;
    font-weight: 800;
    line-height: 1.2;
}

.input-shell,
.select-shell {
    position: relative;
}

.input-shell > i {
    position: absolute;
    z-index: 1;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
}

.input-shell .form-control {
    padding-left: 36px;
}

.select-shell > i {
    position: absolute;
    z-index: 1;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 10px;
    pointer-events: none;
}

.form-control {
    width: 100%;
    min-height: 43px;
    padding: 9px 12px;
    border: 1px solid var(--pc-border-strong);
    border-radius: 11px;
    color: var(--pc-text);
    background: #ffffff;
    font-size: 13px;
    line-height: 1.4;
    box-shadow: none;
    transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
}

select.form-control {
    padding-right: 34px;
    appearance: none;
    -webkit-appearance: none;
}

.form-control::placeholder {
    color: #a3adbd;
}

.form-control:hover {
    border-color: #94a3b8;
}

.form-control:focus {
    border-color: var(--pc-primary);
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    outline: none;
}

.remove-page-row {
    width: 40px;
    height: 43px;
    padding: 0;
    border: 1px solid #fecaca;
    color: var(--pc-danger);
    background: var(--pc-danger-soft);
}

.remove-page-row:hover,
.remove-page-row:focus {
    color: #ffffff;
    border-color: var(--pc-danger);
    background: var(--pc-danger);
}

.form-footer {
    justify-content: space-between;
    gap: 14px;
    margin-top: 14px;
    padding: 15px 16px;
    border-top: 1px solid var(--pc-border);
    background: #fbfcfe;
}

.save-note {
    min-width: 0;
    gap: 8px;
    color: var(--pc-muted);
    font-size: 11px;
}

.save-note i {
    color: var(--pc-accent);
}

.primary-submit {
    min-width: 144px;
    min-height: 44px;
    padding: 10px 17px;
    border: 1px solid var(--pc-accent);
    color: #ffffff;
    background: linear-gradient(135deg, #0f766e, #0d9488);
    box-shadow: 0 10px 22px rgba(15, 118, 110, 0.20);
}

.primary-submit:hover,
.primary-submit:focus {
    color: #ffffff;
    background: linear-gradient(135deg, #0d9488, #0f766e);
    transform: translateY(-1px);
    box-shadow: 0 14px 28px rgba(15, 118, 110, 0.25);
}

.primary-submit:disabled,
.add-row-button:disabled,
.remove-page-row:disabled {
    transform: none;
    cursor: not-allowed;
    opacity: 0.6;
    box-shadow: none;
}

.page-config-shell button:focus-visible,
.page-config-shell a:focus-visible,
.page-config-shell .form-control:focus-visible {
    outline: 3px solid rgba(37, 99, 235, 0.20);
    outline-offset: 2px;
}

@media (max-width: 1040px) {
    .page-config-grid {
        grid-template-columns: 1fr;
    }

    .saved-pages-panel {
        order: 2;
    }

    .page-form {
        order: 1;
    }

    #page-list {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 820px) {
    .page-config-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .header-actions {
        width: 100%;
    }

    .page-count-pill {
        flex: 1 1 auto;
    }

    .linking-page-button {
        flex: 0 0 auto;
    }

    .page-workflow {
        grid-template-columns: 1fr;
        gap: 9px;
    }

    .workflow-connector {
        width: 1px;
        height: 16px;
        margin-left: 14px;
        background: var(--pc-border-strong);
    }

    .workflow-step small {
        white-space: normal;
    }

    #page-list {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 620px) {
    .page-config-shell {
        padding: 12px;
        border-radius: 18px;
    }

    .page-config-header {
        padding: 16px;
        border-radius: 16px;
    }

    .page-config-header-main {
        align-items: flex-start;
    }

    .page-config-title {
        align-items: flex-start;
    }

    .page-config-icon {
        width: 46px;
        height: 46px;
        flex-basis: 46px;
    }

    .header-actions {
        align-items: stretch;
        flex-direction: column;
    }

    .page-count-pill,
    .linking-page-button {
        width: 100%;
    }

    .panel-header,
    .form-footer {
        align-items: stretch;
        flex-direction: column;
    }

    .auto-save-badge,
    .add-row-button,
    .primary-submit {
        width: 100%;
    }

    #page-list {
        grid-template-columns: 1fr;
    }

    .page-row,
    .page-row-fixed {
        grid-template-columns: 1fr;
    }

    .row-number {
        margin-bottom: 0;
    }

    .remove-page-row {
        width: 100%;
    }

    .save-note {
        justify-content: center;
        text-align: center;
    }
}

@media (max-width: 420px) {
    .page-config-header-main {
        gap: 10px;
    }

    .page-back-button {
        width: 40px;
        height: 40px;
        flex-basis: 40px;
    }

    .page-config-icon {
        display: none;
    }

    .page-config-subtitle {
        font-size: 12px;
    }

    .panel-heading-copy {
        align-items: flex-start;
    }
}
</style>

<script>
(function() {
    function startPageConfig($) {
        var pages_names = <?php echo json_encode($pageOptions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?> || [];
        var pagesDataUrl = <?php echo json_encode($pagesDataUrl, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        var deletePageUrl = <?php echo json_encode($deletePageUrl, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        var reorderPageUrl = <?php echo json_encode($reorderPageUrl, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        var deletePageUrlAlt = <?php echo json_encode($deletePageUrlAlt, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        var reorderPageUrlAlt = <?php echo json_encode($reorderPageUrlAlt, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        var csrfName = <?php echo json_encode($csrfName, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        var csrfHash = <?php echo json_encode($csrfHash, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        var pageExtensionOptions = <?php echo json_encode($pageExtensionOptions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?> || {};
        var draggedPageName = '';

        function ajaxPayload(extra) {
            var data = extra || {};
            data.modal_id = $('#modal_id').val();
            data.modelno = $('#modelno').val();
            data.user_id = $('#user_id').val();
            if (csrfName && csrfHash) {
                data[csrfName] = csrfHash;
            }
            return data;
        }

        function ajaxHeaders() {
            var headers = {};
            if (csrfHash) {
                headers['X-CSRF-TOKEN'] = csrfHash;
            }
            return headers;
        }

        function postJson(url, altUrl, data, onSuccess, onError) {
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                headers: ajaxHeaders(),
                data: data,
                success: onSuccess,
                error: function(xhr) {
                    if (altUrl && altUrl !== url && xhr && (xhr.status === 404 || xhr.status === 405)) {
                        $.ajax({
                            url: altUrl,
                            type: 'POST',
                            dataType: 'json',
                            headers: ajaxHeaders(),
                            data: data,
                            success: onSuccess,
                            error: onError
                        });
                        return;
                    }
                    if (typeof onError === 'function') {
                        onError(xhr);
                    }
                }
            });
        }

        function cleanText(value) {
            return String(value || '').replace(/\s+/g, ' ').trim();
        }

        function splitPageName(pageName) {
            pageName = cleanText(pageName);

            var separatorIndex = pageName.lastIndexOf('.');
            if (separatorIndex <= 0 || separatorIndex === pageName.length - 1) {
                return {
                    name: pageName,
                    extension: 'PAGE'
                };
            }

            return {
                name: pageName.substring(0, separatorIndex),
                extension: pageName.substring(separatorIndex + 1)
            };
        }

        function uniquePages(pages) {
            var seen = {};
            var cleanPages = [];

            $.each(pages || [], function(_, pageName) {
                pageName = cleanText(pageName);

                if (pageName && !seen[pageName]) {
                    seen[pageName] = true;
                    cleanPages.push(pageName);
                }
            });

            return cleanPages;
        }

        function getEnteredPages() {
            var enteredPages = [];

            $('#form-items .page-row').each(function() {
                var pageName = cleanText($(this).find('input[name="input_box[]"]').val());
                var extension = cleanText($(this).find('select[name="dropdown[]"]').val());

                if (pageName && extension) {
                    enteredPages.push(pageName + '.' + extension);
                }
            });

            return enteredPages;
        }

        function refreshPageCount() {
            $('#pageCount').text(pages_names.length);
        }

        function showStatus(message, type) {
            var status = $('#pageStatus');

            if (!message) {
                status.hide().removeClass('is-success is-error is-info').text('');
                return;
            }

            status
                .removeClass('is-success is-error is-info')
                .addClass('is-' + (type || 'info'))
                .text(message)
                .show();
        }

        function renderPageList() {
            var list = $('#page-list');
            list.empty();

            if (!pages_names.length) {
                list.append(
                    $('<li>', { 'class': 'empty-page-item' })
                        .append($('<span>', { 'class': 'empty-page-icon' }).append($('<i>', { 'class': 'fa fa-files-o', 'aria-hidden': 'true' })))
                        .append($('<strong>', { text: 'No pages saved yet' }))
                        .append($('<small>', { text: 'Use the form to create the first page for this model.' }))
                );
                return;
            }

            $.each(pages_names, function(index, pageName) {
                var pageParts = splitPageName(pageName);

                var leading = $('<span>', { 'class': 'page-list-leading', 'aria-hidden': 'true' })
                    .append($('<i>', { 'class': 'fa fa-file-text-o' }))
                    .append($('<span>', { 'class': 'page-list-index', text: index + 1 }));

                var content = $('<span>', { 'class': 'page-list-content' })
                    .append($('<span>', { 'class': 'page-list-name', text: pageParts.name }))
                    .append(
                        $('<span>', { 'class': 'page-list-meta' })
                            .append($('<span>', { 'class': 'page-extension-badge', text: '.' + pageParts.extension }))
                            .append($('<span>', { 'class': 'page-drag-handle', title: 'Drag to reorder' }).append($('<i>', { 'class': 'fa fa-arrows-v', 'aria-hidden': 'true' })))
                    );

                var deleteButton = $('<button>', {
                    type: 'button',
                    'class': 'page-delete-button',
                    'aria-label': 'Delete saved page ' + pageName,
                    title: 'Delete saved page'
                }).append($('<i>', { 'class': 'fa fa-trash-o', 'aria-hidden': 'true' }));

                list.append(
                    $('<li>', {
                        'class': 'page-list-item',
                        'data-page-name': pageName,
                        draggable: true
                    })
                        .append(leading)
                        .append(content)
                        .append(deleteButton)
                );
            });
        }

        function getCurrentPageOrderFromList() {
            var orderedPages = [];

            $('#page-list .page-list-item').each(function() {
                var pageName = cleanText($(this).attr('data-page-name'));

                if (pageName) {
                    orderedPages.push(pageName);
                }
            });

            return orderedPages;
        }

        function updatePageListIndexes() {
            $('#page-list .page-list-item').each(function(index) {
                $(this).find('.page-list-index').text(index + 1);
            });
        }

        function saveDraggedPageOrder() {
            var orderedPages = getCurrentPageOrderFromList();

            if (!orderedPages.length) {
                return;
            }

            pages_names = orderedPages;
            updatePageListIndexes();
            refreshPageCount();
            refreshSequenceOptions();
            showStatus('Saving page order...', 'info');

            postJson(reorderPageUrl, reorderPageUrlAlt, ajaxPayload({
                pages: JSON.stringify(orderedPages)
            }), function(response) {
                if (response && response.success) {
                    pages_names = uniquePages(response.pages && response.pages.length ? response.pages : orderedPages);
                    renderPageList();
                    refreshPageCount();
                    refreshSequenceOptions();
                    showStatus(response.message || 'Page order saved successfully.', 'success');
                } else {
                    fetchSavedPages();
                    showStatus((response && response.message) || 'Unable to save page order.', 'error');
                }
            }, function() {
                fetchSavedPages();
                showStatus('Unable to save page order. Please try again.', 'error');
            });
        }

        function refreshSequenceOptions() {
            pages_names = uniquePages(pages_names);

            $('#link-pages')
                .toggleClass('disabled', !pages_names.length)
                .attr('aria-disabled', pages_names.length ? 'false' : 'true')
                .attr('tabindex', pages_names.length ? '0' : '-1');
        }

        function syncCombinedPagesInput() {
            $('#page_names_combined').val(uniquePages(getEnteredPages()).join(', '));
        }

        function fetchSavedPages(callback) {
            $.ajax({
                url: pagesDataUrl,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response && response.success) {
                        pages_names = uniquePages(response.pages || []);
                        refreshPageCount();
                        refreshSequenceOptions();
                        renderPageList();
                    }

                    if (typeof callback === 'function') {
                        callback(response);
                    }
                },
                error: function() {
                    showStatus('Unable to load saved pages. Please try again.', 'error');

                    if (typeof callback === 'function') {
                        callback(null);
                    }
                }
            });
        }

        function buildExtensionOptions() {
            var html = '';

            $.each(pageExtensionOptions, function(value, label) {
                html += '<option value="' + $('<div>').text(value).html() + '">.' + $('<div>').text(label).html() + '</option>';
            });

            return html;
        }

        function buildPageRow(rowNumber, removable) {
            var removeButton = removable
                ? '<button type="button" class="btn remove-page-row" aria-label="Remove page row" title="Remove row"><i class="fa fa-trash-o" aria-hidden="true"></i></button>'
                : '';

            return '' +
                '<div class="page-row ' + (removable ? 'page-row-removable' : 'page-row-fixed') + '">' +
                    '<div class="row-number" aria-hidden="true">' + rowNumber + '</div>' +

                    '<div class="form-group page-name-field">' +
                        '<label for="input-box-' + rowNumber + '">Page name</label>' +
                        '<div class="input-shell">' +
                            '<i class="fa fa-file-text-o" aria-hidden="true"></i>' +
                            '<input type="text" name="input_box[]" id="input-box-' + rowNumber + '" class="form-control" placeholder="Example: Home" autocomplete="off" required>' +
                        '</div>' +
                    '</div>' +

                    '<div class="form-group page-extension-field">' +
                        '<label for="dropdown-' + rowNumber + '">Page type</label>' +
                        '<div class="select-shell">' +
                            '<select name="dropdown[]" id="dropdown-' + rowNumber + '" class="form-control" required>' +
                                buildExtensionOptions() +
                            '</select>' +
                            '<i class="fa fa-chevron-down" aria-hidden="true"></i>' +
                        '</div>' +
                    '</div>' +

                    removeButton +
                '</div>';
        }

        function refreshFormRowNumbers() {
            $('#form-items .page-row').each(function(index) {
                $(this).find('.row-number').text(index + 1);
            });
        }

        function clearAddPageForm() {
            $('#form-items').html(buildPageRow(1, false));
            $('#page_names_combined').val('');
            refreshFormRowNumbers();
        }

        $(document).ready(function() {
            var counter = $('#form-items .page-row').length || 1;

            fetchSavedPages();

            $('#file-form2').off('submit.pageConfig').on('submit.pageConfig', function(event) {
                event.preventDefault();

                var form = $(this);
                var submitButton = form.find('.primary-submit');
                var submittedPages = uniquePages(getEnteredPages());

                if (!submittedPages.length) {
                    showStatus('Please add at least one page.', 'error');
                    return;
                }

                syncCombinedPagesInput();
                submitButton.prop('disabled', true);
                showStatus('Saving pages...', 'info');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    dataType: 'json',
                    data: form.serialize(),
                    success: function(response) {
                        if (response && response.success) {
                            pages_names = uniquePages(pages_names.concat(submittedPages).concat(response.pages || []));
                            clearAddPageForm();
                            counter = 1;
                            renderPageList();
                            refreshPageCount();
                            refreshSequenceOptions();
                            $('#tab').show();
                            showStatus(response.message || 'Pages saved successfully.', 'success');
                        } else {
                            showStatus((response && response.message) || 'Error saving data to database.', 'error');
                        }
                    },
                    error: function() {
                        showStatus('Error saving data to database.', 'error');
                    },
                    complete: function() {
                        submitButton.prop('disabled', false);
                    }
                });
            });

            $('#add-more').off('click.pageConfig').on('click.pageConfig', function() {
                counter++;
                $('#form-items').append(buildPageRow(counter, true));
                refreshFormRowNumbers();
                $('#input-box-' + counter).focus();
                syncCombinedPagesInput();
            });

            $('#link-pages').off('click.pageConfig').on('click.pageConfig', function(event) {
                if ($(this).hasClass('disabled') || $(this).attr('aria-disabled') === 'true') {
                    event.preventDefault();
                    showStatus('Create at least one page before opening the linking page.', 'info');
                }
            });

            $('#page-list').off('click.pageConfig', '.page-delete-button').on('click.pageConfig', '.page-delete-button', function() {
                var pageName = $(this).closest('.page-list-item').attr('data-page-name');
                var button = $(this);

                if (!pageName) {
                    return;
                }

                if (!confirm('Delete saved page "' + pageName + '"? This action cannot be undone.')) {
                    return;
                }

                button.prop('disabled', true);
                showStatus('Deleting "' + pageName + '"...', 'info');

                postJson(deletePageUrl, deletePageUrlAlt, ajaxPayload({
                    page_name: pageName
                }), function(response) {
                    if (response && response.success) {
                        pages_names = uniquePages(response.pages || []);
                        refreshPageCount();
                        refreshSequenceOptions();
                        renderPageList();
                        showStatus(response.message || 'Saved page deleted successfully.', 'success');
                    } else {
                        showStatus((response && response.message) || 'Unable to delete saved page.', 'error');
                    }
                    button.prop('disabled', false);
                }, function() {
                    showStatus('Unable to delete saved page. Please try again.', 'error');
                    button.prop('disabled', false);
                });
            });

            $('#page-list')
                .off('dragstart.pageConfig', '.page-list-item')
                .on('dragstart.pageConfig', '.page-list-item', function(event) {
                    draggedPageName = cleanText($(this).attr('data-page-name'));
                    $(this).addClass('is-dragging');

                    if (event.originalEvent && event.originalEvent.dataTransfer) {
                        event.originalEvent.dataTransfer.effectAllowed = 'move';
                        event.originalEvent.dataTransfer.setData('text/plain', draggedPageName);
                    }
                })
                .off('dragover.pageConfig', '.page-list-item')
                .on('dragover.pageConfig', '.page-list-item', function(event) {
                    event.preventDefault();

                    var target = $(this);
                    var draggingItem = $('#page-list .page-list-item.is-dragging');

                    if (!draggingItem.length || target.is(draggingItem)) {
                        return;
                    }

                    $('#page-list .page-list-item').removeClass('is-drag-over');
                    target.addClass('is-drag-over');

                    var targetTop = target.offset().top;
                    var targetMiddle = targetTop + (target.outerHeight() / 2);
                    var pointerY = event.originalEvent ? event.originalEvent.pageY : event.pageY;

                    if (pointerY > targetMiddle) {
                        target.after(draggingItem);
                    } else {
                        target.before(draggingItem);
                    }

                    updatePageListIndexes();
                })
                .off('drop.pageConfig', '.page-list-item')
                .on('drop.pageConfig', '.page-list-item', function(event) {
                    event.preventDefault();
                    $('#page-list .page-list-item').removeClass('is-drag-over is-dragging');
                    saveDraggedPageOrder();
                    draggedPageName = '';
                })
                .off('dragend.pageConfig', '.page-list-item')
                .on('dragend.pageConfig', '.page-list-item', function() {
                    $('#page-list .page-list-item').removeClass('is-drag-over is-dragging');
                    if (draggedPageName) {
                        saveDraggedPageOrder();
                    }
                    draggedPageName = '';
                });

            $('#form-items')
                .off('click.pageConfig', '.remove-page-row')
                .on('click.pageConfig', '.remove-page-row', function() {
                    $(this).closest('.page-row').remove();
                    refreshFormRowNumbers();
                    syncCombinedPagesInput();
                });

            $('#form-items')
                .off('input.pageConfig change.pageConfig', 'input[name="input_box[]"], select[name="dropdown[]"]')
                .on('input.pageConfig change.pageConfig', 'input[name="input_box[]"], select[name="dropdown[]"]', syncCombinedPagesInput);

            refreshPageCount();
            refreshSequenceOptions();
            renderPageList();
            refreshFormRowNumbers();
            syncCombinedPagesInput();
        });
    }

    if (window.jQuery) {
        startPageConfig(window.jQuery);
        return;
    }

    var jqueryScript = document.createElement('script');
    jqueryScript.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
    jqueryScript.onload = function() {
        startPageConfig(window.jQuery);
    };
    jqueryScript.onerror = function() {
        console.error('jQuery could not be loaded for the page configuration UI.');
    };
    document.head.appendChild(jqueryScript);
})();

</script>
