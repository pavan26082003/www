<?php
$pageOptions = [];

if (!empty($page_names) && is_array($page_names)) {
    foreach ($page_names as $pageRow) {
        if (empty($pageRow['page_names'])) {
            continue;
        }

        foreach (explode(',', $pageRow['page_names']) as $pageName) {
            $pageName = trim($pageName);
            if ($pageName !== '') {
                $pageOptions[] = $pageName;
            }
        }
    }
}

$pageOptions = array_values(array_unique($pageOptions));
$hasPages = !empty($pageOptions);
$pageExtensionOptions = array('test', 'config', 'paring', 'operation');
$pagesDataUrl = get_uri("items/modal_pages_data/" . $modal_id . "?modelno=" . rawurlencode($modelno));
?>


<div class="page-config-shell">
    <div class="page-config-header">
        <div class="page-config-title">
            <div class="page-config-icon"><i class="fa fa-files-o"></i></div>
            <div>
            <p class="page-config-eyebrow">Page Builder</p>
            <h4><?php echo esc($modelno); ?></h4>
            <span class="page-config-subtitle">Create model pages, review saved pages, and assign display order.</span>
            </div>
        </div>
        <div class="page-count-pill">
            <i class="fa fa-file-text-o"></i>
            <span id="pageCount"><?php echo count($pageOptions); ?></span>
            <small>Pages</small>
        </div>
    </div>

    <div class="page-config-actions">
        <button type="button" id="view" class="btn btn-secondary page-action-button">
            <i class="fa fa-eye"></i>
            <span>View Pages</span>
        </button>
        <button type="button" id="sequence" class="btn btn-secondary page-action-button" <?php echo $hasPages ? '' : 'disabled'; ?>>
            <i class="fa fa-sort-numeric-asc"></i>
            <span>Add Page Sequence</span>
        </button>
    </div>

    <div id="tab" class="tab-container" style="display:none;">
        <div class="tab-heading">
            <div>
                <h5>Saved Pages</h5>
                <p>Pages currently stored for this model.</p>
            </div>
            <i class="fa fa-database"></i>
        </div>
        <ul id="page-list"></ul>
    </div>

    <?php echo form_open_multipart(get_uri("items/save_pages"), array("id" => "file-form2", "class" => "general-form page-form", "role" => "form")); ?>
        <input type="hidden" name="modelno" id="modelno" value="<?php echo esc($modelno); ?>" />
        <input type="hidden" name="user_id" id="user_id" value="<?php echo esc($user_id); ?>" />
        <input type="hidden" name="modal_id" id="modal_id" value="<?php echo esc($modal_id); ?>" />
        <input type="hidden" name="page_names_combined" id="page_names_combined" value="" />

        <div class="form-toolbar">
            <div>
                <h5><i class="fa fa-plus-circle"></i> Add Pages</h5>
                <p>Add one or more pages. Existing saved pages stay in the database.</p>
            </div>
            <button type="button" id="add-more" class="btn btn-secondary compact-button">
                <i class="fa fa-plus"></i>
                <span>Add Row</span>
            </button>
        </div>

        <div id="form-items" class="page-form-list">
            <div class="page-row">
                <div class="form-group page-name-field">
                    <label for="input-box-1">Page Name</label>
                    <input type="text" name="input_box[]" id="input-box-1" class="form-control" placeholder="Example: Home" required>
                </div>

                <div class="form-group page-extension-field">
                    <label for="dropdown-1">Extension</label>
                    <select name="dropdown[]" id="dropdown-1" class="form-control" required>
                        <?php foreach ($pageExtensionOptions as $extensionOption): ?>
                            <option value="<?php echo esc($extensionOption, 'attr'); ?>">.<?php echo esc(ucfirst($extensionOption)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary primary-submit">
            <i class="fa fa-save"></i>
            <span>Save Pages</span>
        </button>
    <?php echo form_close(); ?>

    <?php echo form_open_multipart(get_uri("items/save_page_seq"), array("id" => "page-sequence-form", "class" => "general-form", "role" => "form")); ?>
        <input type="hidden" name="modelno" id="sequence_modelno" value="<?php echo esc($modelno); ?>" />
        <input type="hidden" name="user_id" id="sequence_user_id" value="<?php echo esc($user_id); ?>" />
        <input type="hidden" name="modal_id" id="sequence_modal_id" value="<?php echo esc($modal_id); ?>" />

        <div id="pageSequenceModal" class="modal" style="display:none;">
            <div class="modal-content page-sequence-card">
                <button type="button" class="close" aria-label="Close"><i class="fa fa-times"></i></button>
                <div class="sequence-modal-header">
                    <div>
                        <p class="page-config-eyebrow">Ordering</p>
                        <h4>Page Sequence</h4>
                    </div>
                    <i class="fa fa-list-ol"></i>
                </div>

                <div class="select-container">
                    <div class="sequence-field">
                        <label for="pageNamesDropdown">Page</label>
                        <select id="pageNamesDropdown" class="form-control">
                            <?php if ($hasPages): ?>
                                <?php foreach ($pageOptions as $page): ?>
                                    <option value="<?php echo esc($page, 'attr'); ?>"><?php echo esc($page); ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">No pages found</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="sequence-field">
                        <label for="sequenceDropdown">Sequence</label>
                        <select id="sequenceDropdown" class="form-control"></select>
                    </div>
                </div>

                <button type="button" id="addButton" class="btn btn-secondary compact-button" <?php echo $hasPages ? '' : 'disabled'; ?>>
                    <i class="fa fa-arrow-down"></i>
                    <span>Add to Sequence</span>
                </button>

                <label for="addedValuesInput" class="sequence-output-label">Selected Sequence</label>
                <textarea id="addedValuesInput" name="addedValuesInput" readonly placeholder="Selected page sequence will appear here"></textarea>

                <div class="modal-actions">
                    <button type="button" id="clearSequence" class="btn btn-secondary compact-button">
                        <i class="fa fa-eraser"></i>
                        <span>Clear</span>
                    </button>
                    <button type="submit" id="submitPage" class="btn btn-primary compact-button" disabled>
                        <i class="fa fa-check"></i>
                        <span>Save Sequence</span>
                    </button>
                </div>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- <style>
.page-config-shell {
    max-width: 760px;
    margin: 0 auto;
    color: #1f2937;
}

.page-config-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 20px;
    border: 1px solid #d9e2ec;
    border-radius: 8px;
    background: #ffffff;
}

.page-config-eyebrow {
    margin: 0 0 4px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
}

.page-config-header h4,
.form-toolbar h5,
.page-sequence-card h4 {
    margin: 0;
    font-weight: 700;
    color: #111827;
}

.page-count-pill {
    min-width: 82px;
    padding: 10px 12px;
    border-radius: 8px;
    background: #eef6ff;
    color: #0f4c81;
    text-align: center;
}

.page-count-pill span {
    display: block;
    font-size: 22px;
    font-weight: 800;
    line-height: 1;
}

.page-count-pill small {
    font-size: 12px;
    font-weight: 700;
}

.page-config-actions {
    display: flex;
    gap: 12px;
    margin: 16px 0;
}

.page-action-button,
.compact-button,
.primary-submit {
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 700;
    transition: background-color 0.2s ease, box-shadow 0.2s ease;
}

.page-action-button {
    flex: 1;
    min-height: 42px;
    background: #2563eb;
    color: #ffffff;
}

.page-action-button:hover,
.compact-button:hover,
.primary-submit:hover {
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.14);
}

.page-action-button:disabled,
.compact-button:disabled,
.primary-submit:disabled {
    cursor: not-allowed;
    background: #cbd5e1;
    box-shadow: none;
}

.tab-container {
    margin-bottom: 16px;
    padding: 12px;
    border: 1px solid #d9e2ec;
    border-radius: 8px;
    background: #f8fafc;
}

#page-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
}

#page-list li {
    padding: 9px 10px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    background: #ffffff;
    font-size: 14px;
    font-weight: 600;
}

.general-form.page-form {
    padding: 18px 20px;
    border: 1px solid #d9e2ec;
    border-radius: 8px;
    background: #ffffff;
}

.form-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 16px;
}

.form-toolbar p {
    margin: 4px 0 0;
    color: #64748b;
    font-size: 13px;
}

.page-form-list {
    display: grid;
    gap: 12px;
}

.page-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 180px auto;
    gap: 12px;
    align-items: end;
    padding: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #f8fafc;
}

.form-group {
    margin: 0;
}

.form-group label,
.sequence-field label,
.sequence-output-label {
    display: block;
    margin-bottom: 6px;
    font-size: 13px;
    font-weight: 700;
    color: #334155;
}

.form-control,
#addedValuesInput {
    width: 100%;
    min-height: 40px;
    padding: 9px 10px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background: #ffffff;
    color: #111827;
    font-size: 14px;
    box-sizing: border-box;
}

.form-control:focus,
#addedValuesInput:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.16);
}

.compact-button {
    min-height: 38px;
    padding: 8px 14px;
    background: #0f766e;
    color: #ffffff;
}

.remove-page-row {
    min-width: 38px;
    background: #fee2e2;
    color: #991b1b;
}

.primary-submit {
    width: 100%;
    min-height: 44px;
    margin-top: 16px;
    background: #2563eb;
    color: #ffffff;
}

.modal {
    position: fixed;
    inset: 0;
    z-index: 1050;
    padding: 24px;
    background: rgba(15, 23, 42, 0.45);
    overflow-y: auto;
}

.page-sequence-card {
    position: relative;
    max-width: 620px;
    margin: 8vh auto 0;
    padding: 22px;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 24px 70px rgba(15, 23, 42, 0.22);
}

.close {
    position: absolute;
    top: 12px;
    right: 14px;
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 50%;
    background: #f1f5f9;
    color: #334155;
    font-size: 22px;
    line-height: 1;
    cursor: pointer;
}

.select-container {
    display: grid;
    grid-template-columns: 1fr 140px;
    gap: 12px;
    margin: 18px 0 14px;
}

#addedValuesInput {
    min-height: 86px;
    resize: vertical;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 14px;
}

@media (max-width: 640px) {
    .page-config-header,
    .form-toolbar,
    .page-config-actions,
    .modal-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .page-row,
    .select-container {
        grid-template-columns: 1fr;
    }

    .page-count-pill {
        width: 100%;
    }
}
</style> -->



<style>
:root {
    --primary-color: #4BB4A1;
    --background: #EDF7F6;
    --card-bg: #FFFFFF;
    --text-primary: #0F2420;

    --green-accent: #2D6C60;
    --green-bg: #DBF0EC;

    --pink-btn: #6FC3B3;
    --hover-blue: #3C9080;

    --border-light: #B7E1D9;
    --muted-text: #3C9080;
}

.page-config-shell {
    max-width: 760px;
    margin: 0 auto;
    color: var(--text-primary);
    background: var(--background);
}

.page-config-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 20px;
    border: 1px solid var(--border-light);
    border-radius: 8px;
    background: var(--card-bg);
}

.page-config-eyebrow {
    margin: 0 0 4px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--muted-text);
}

.page-config-header h4,
.form-toolbar h5,
.page-sequence-card h4 {
    margin: 0;
    font-weight: 700;
    color: var(--text-primary);
}

.page-count-pill {
    min-width: 82px;
    padding: 10px 12px;
    border-radius: 8px;
    background: var(--green-bg);
    color: var(--green-accent);
    text-align: center;
}

.page-count-pill span {
    display: block;
    font-size: 22px;
    font-weight: 800;
    line-height: 1;
}

.page-count-pill small {
    font-size: 12px;
    font-weight: 700;
}

.page-config-actions {
    display: flex;
    gap: 12px;
    margin: 16px 0;
}

.page-action-button,
.compact-button,
.primary-submit {
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 700;
    transition: all 0.2s ease;
}

.page-action-button {
    flex: 1;
    min-height: 42px;
    background: var(--primary-color);
    color: white;
}

.page-action-button:hover,
.compact-button:hover,
.primary-submit:hover {
    background: var(--hover-blue);
    box-shadow: 0 6px 16px rgba(15, 36, 32, 0.14);
}

.page-action-button:disabled,
.compact-button:disabled,
.primary-submit:disabled {
    cursor: not-allowed;
    background: #cbd5e1;
    box-shadow: none;
}

.tab-container {
    margin-bottom: 16px;
    padding: 12px;
    border: 1px solid var(--border-light);
    border-radius: 8px;
    background: var(--green-bg);
}

#page-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
}

#page-list li {
    padding: 9px 10px;
    border: 1px solid var(--border-light);
    border-radius: 6px;
    background: var(--card-bg);
    font-size: 14px;
    font-weight: 600;
}

.general-form.page-form {
    padding: 18px 20px;
    border: 1px solid var(--border-light);
    border-radius: 8px;
    background: var(--card-bg);
}

.form-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 16px;
}

.form-toolbar p {
    margin: 4px 0 0;
    color: var(--muted-text);
    font-size: 13px;
}

.page-form-list {
    display: grid;
    gap: 12px;
}

.page-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 180px auto;
    gap: 12px;
    align-items: end;
    padding: 12px;
    border: 1px solid var(--border-light);
    border-radius: 8px;
    background: var(--background);
}

.form-group {
    margin: 0;
}

.form-group label,
.sequence-field label,
.sequence-output-label {
    display: block;
    margin-bottom: 6px;
    font-size: 13px;
    font-weight: 700;
    color: var(--green-accent);
}

.form-control,
#addedValuesInput {
    width: 100%;
    min-height: 40px;
    padding: 9px 10px;
    border: 1px solid var(--border-light);
    border-radius: 6px;
    background: white;
    color: var(--text-primary);
    font-size: 14px;
    box-sizing: border-box;
}

.form-control:focus,
#addedValuesInput:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(75, 180, 161, 0.18);
}

.compact-button {
    min-height: 38px;
    padding: 8px 14px;
    background: var(--green-accent);
    color: white;
}

.remove-page-row {
    min-width: 38px;
    background: #fbeaea;
    color: #991b1b;
}

.primary-submit {
    width: 100%;
    min-height: 44px;
    margin-top: 16px;
    background: var(--primary-color);
    color: white;
}

.modal {
    position: fixed;
    inset: 0;
    z-index: 1050;
    padding: 24px;
    background: rgba(15, 36, 32, 0.45);
    overflow-y: auto;
}

.page-sequence-card {
    position: relative;
    max-width: 620px;
    margin: 8vh auto 0;
    padding: 22px;
    border-radius: 8px;
    background: var(--card-bg);
    box-shadow: 0 24px 70px rgba(15, 36, 32, 0.22);
}

.close {
    position: absolute;
    top: 12px;
    right: 14px;
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 50%;
    background: var(--green-bg);
    color: var(--green-accent);
    font-size: 22px;
    line-height: 1;
    cursor: pointer;
}

.select-container {
    display: grid;
    grid-template-columns: 1fr 140px;
    gap: 12px;
    margin: 18px 0 14px;
}

#addedValuesInput {
    min-height: 86px;
    resize: vertical;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 14px;
}

@media (max-width: 640px) {
    .page-config-header,
    .form-toolbar,
    .page-config-actions,
    .modal-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .page-row,
    .select-container {
        grid-template-columns: 1fr;
    }

    .page-count-pill {
        width: 100%;
    }
}
</style>

<style>
.page-config-shell {
    max-width: 920px;
    padding: 18px;
    border-radius: 12px;
    background: #f6f8fb;
}

.page-config-header {
    padding: 18px 20px;
    border: 1px solid #d8e0e8;
    border-left: 5px solid #256f78;
    border-radius: 10px;
    background: #ffffff;
    box-shadow: 0 8px 24px rgba(31, 41, 55, 0.08);
}

.page-config-eyebrow {
    color: #7a4b00;
    letter-spacing: 0;
}

.page-config-header h4 {
    font-size: 20px;
    line-height: 1.25;
    color: #172033;
}

.page-count-pill {
    background: #fff4d6;
    color: #7a4b00;
    border: 1px solid #f3d48b;
}

.page-config-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.page-action-button {
    min-height: 48px;
    border: 1px solid #c7d5df;
    background: #ffffff;
    color: #172033;
    box-shadow: 0 5px 16px rgba(31, 41, 55, 0.08);
}

.page-action-button:first-child {
    border-color: #8cc4c4;
    background: #e9f7f6;
    color: #14565c;
}

.page-action-button:nth-child(2) {
    border-color: #e5c27b;
    background: #fff8e8;
    color: #7a4b00;
}

.page-action-button:hover,
.compact-button:hover,
.primary-submit:hover {
    transform: translateY(-1px);
}

.tab-container {
    border: 1px solid #d8e0e8;
    border-radius: 10px;
    background: #ffffff;
    box-shadow: 0 6px 18px rgba(31, 41, 55, 0.06);
}

#page-list {
    grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
}

#page-list li {
    border: 1px solid #d8e0e8;
    border-left: 4px solid #256f78;
    border-radius: 8px;
    background: #fbfcfd;
    color: #172033;
}

.general-form.page-form {
    border: 1px solid #d8e0e8;
    border-radius: 10px;
    background: #ffffff;
    box-shadow: 0 8px 24px rgba(31, 41, 55, 0.08);
}

.form-toolbar {
    padding-bottom: 14px;
    border-bottom: 1px solid #e6edf2;
}

.form-toolbar h5 {
    font-size: 18px;
    color: #172033;
}

.form-toolbar p {
    color: #65758b;
}

.page-form-list {
    gap: 10px;
}

.page-row {
    grid-template-columns: minmax(220px, 1fr) 170px 44px;
    border: 1px solid #dbe5eb;
    background: #f8fafc;
}

.page-row:first-child {
    grid-template-columns: minmax(220px, 1fr) 170px;
}

.form-group label,
.sequence-field label,
.sequence-output-label {
    color: #405166;
}

.form-control,
#addedValuesInput {
    border-color: #cbd7e1;
    border-radius: 8px;
}

.compact-button {
    border-radius: 8px;
    background: #256f78;
}

.remove-page-row {
    padding: 0;
    background: #f7d8d8;
    color: #8f1d1d;
}

.primary-submit {
    border-radius: 8px;
    background: #1f5f68;
}

.modal {
    background: rgba(23, 32, 51, 0.48);
}

.page-sequence-card {
    border: 1px solid #d8e0e8;
    border-radius: 12px;
}

.close {
    background: #eef3f6;
    color: #172033;
}

@media (max-width: 760px) {
    .page-config-shell {
        padding: 12px;
    }

    .page-config-actions,
    .page-row,
    .page-row:first-child,
    .select-container {
        grid-template-columns: 1fr;
    }

    .page-action-button,
    .compact-button,
    .primary-submit {
        width: 100%;
    }

    .form-toolbar {
        align-items: stretch;
    }

    #page-list {
        grid-template-columns: 1fr;
    }
}
</style>

<style>
.page-config-shell {
    max-width: 1040px;
    padding: 22px;
    border: 1px solid #dce3ec;
    border-radius: 14px;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(246, 248, 251, 0.96)),
        #f6f8fb;
    box-shadow: 0 18px 45px rgba(31, 41, 55, 0.12);
}

.page-config-header {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center;
    gap: 18px;
    padding: 20px;
    border: 1px solid #d8e0e8;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 10px 28px rgba(31, 41, 55, 0.08);
}

.page-config-title {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
}

.page-config-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    flex: 0 0 48px;
    border-radius: 12px;
    background: #e8f4ff;
    color: #225f95;
    font-size: 20px;
}

.page-config-eyebrow {
    margin: 0 0 4px;
    color: #8b5b13;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0;
    text-transform: uppercase;
}

.page-config-header h4 {
    margin: 0;
    color: #172033;
    font-size: 20px;
    line-height: 1.25;
    overflow-wrap: anywhere;
}

.page-config-subtitle {
    display: block;
    margin-top: 5px;
    color: #66758a;
    font-size: 13px;
}

.page-count-pill {
    display: grid;
    grid-template-columns: auto 1fr;
    grid-template-rows: auto auto;
    align-items: center;
    column-gap: 10px;
    min-width: 116px;
    padding: 12px 14px;
    border: 1px solid #e7c879;
    border-radius: 12px;
    background: #fff8e8;
    color: #805312;
}

.page-count-pill i {
    grid-row: 1 / 3;
    font-size: 20px;
}

.page-count-pill span {
    display: block;
    font-size: 24px;
    font-weight: 900;
    line-height: 1;
}

.page-count-pill small {
    font-size: 12px;
    font-weight: 800;
}

.page-config-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin: 16px 0;
}

.page-action-button,
.compact-button,
.primary-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    min-height: 44px;
    border: none;
    border-radius: 10px;
    font-weight: 800;
    cursor: pointer;
    transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
}

.page-action-button {
    border: 1px solid #d4dde7;
    background: #ffffff;
    color: #172033;
    box-shadow: 0 8px 20px rgba(31, 41, 55, 0.08);
}

.page-action-button:first-child {
    border-color: #a9d4e5;
    background: #eaf7fc;
    color: #15546d;
}

.page-action-button:nth-child(2) {
    border-color: #e6ca87;
    background: #fff8e8;
    color: #805312;
}

.page-action-button:hover,
.compact-button:hover,
.primary-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(31, 41, 55, 0.14);
}

.page-action-button:disabled,
.compact-button:disabled,
.primary-submit:disabled {
    transform: none;
    cursor: not-allowed;
    background: #d9e1ea;
    color: #758195;
    box-shadow: none;
}

.tab-container {
    margin-bottom: 16px;
    padding: 0;
    border: 1px solid #d8e0e8;
    border-radius: 12px;
    background: #ffffff;
    overflow: hidden;
    box-shadow: 0 10px 24px rgba(31, 41, 55, 0.08);
}

.tab-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 15px 16px;
    border-bottom: 1px solid #e5ecf2;
    background: #f9fbfd;
}

.tab-heading h5 {
    margin: 0;
    color: #172033;
    font-size: 16px;
}

.tab-heading p {
    margin: 4px 0 0;
    color: #66758a;
    font-size: 13px;
}

.tab-heading > i {
    color: #225f95;
    font-size: 19px;
}

#page-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
    gap: 10px;
    margin: 0;
    padding: 14px;
    list-style: none;
}

#page-list li {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    align-items: center;
    gap: 10px;
    min-height: 48px;
    padding: 10px 12px;
    border: 1px solid #dce5ed;
    border-left: 4px solid #225f95;
    border-radius: 10px;
    background: #fbfcfe;
    color: #172033;
    font-size: 14px;
    font-weight: 700;
}

#page-list li .page-list-name {
    overflow-wrap: anywhere;
}

.page-list-index {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: #e8f4ff;
    color: #225f95;
    font-size: 12px;
    font-weight: 900;
}

#page-list li > i {
    color: #8b5b13;
}

.empty-page-item {
    grid-column: 1 / -1;
    border-left-color: #e7c879 !important;
    color: #66758a !important;
}

.general-form.page-form {
    padding: 18px;
    border: 1px solid #d8e0e8;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 10px 28px rgba(31, 41, 55, 0.09);
}

.form-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 16px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e5ecf2;
}

.form-toolbar h5 {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    color: #172033;
    font-size: 18px;
}

.form-toolbar h5 i {
    color: #23684f;
}

.form-toolbar p {
    margin: 5px 0 0;
    color: #66758a;
    font-size: 13px;
}

.page-form-list {
    display: grid;
    gap: 11px;
}

.page-row {
    display: grid;
    grid-template-columns: minmax(240px, 1fr) 180px 44px;
    gap: 12px;
    align-items: end;
    padding: 13px;
    border: 1px solid #dce5ed;
    border-radius: 12px;
    background: #f8fafc;
}

.page-row:first-child {
    grid-template-columns: minmax(240px, 1fr) 180px;
}

.form-group {
    margin: 0;
}

.form-group label,
.sequence-field label,
.sequence-output-label {
    display: block;
    margin-bottom: 6px;
    color: #44566c;
    font-size: 13px;
    font-weight: 800;
}

.form-control,
#addedValuesInput {
    width: 100%;
    min-height: 42px;
    padding: 9px 11px;
    border: 1px solid #cbd7e1;
    border-radius: 10px;
    background: #ffffff;
    color: #172033;
    font-size: 14px;
    box-sizing: border-box;
}

.form-control:focus,
#addedValuesInput:focus {
    outline: none;
    border-color: #225f95;
    box-shadow: 0 0 0 3px rgba(34, 95, 149, 0.15);
}

.compact-button {
    padding: 8px 14px;
    background: #225f95;
    color: #ffffff;
}

.remove-page-row {
    width: 44px;
    min-width: 44px;
    padding: 0;
    background: #f8dddd;
    color: #8f1d1d;
}

.primary-submit {
    width: 100%;
    min-height: 48px;
    margin-top: 16px;
    background: #23684f;
    color: #ffffff;
}

.modal {
    position: fixed;
    inset: 0;
    z-index: 1050;
    padding: 24px;
    background: rgba(23, 32, 51, 0.5);
    overflow-y: auto;
}

.page-sequence-card {
    position: relative;
    max-width: 680px;
    margin: 8vh auto 0;
    padding: 22px;
    border: 1px solid #d8e0e8;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 28px 80px rgba(23, 32, 51, 0.28);
}

.sequence-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 16px;
    padding-right: 40px;
}

.sequence-modal-header h4 {
    margin: 0;
    color: #172033;
}

.sequence-modal-header > i {
    color: #225f95;
    font-size: 22px;
}

.close {
    position: absolute;
    top: 12px;
    right: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border: none;
    border-radius: 50%;
    background: #eef3f6;
    color: #172033;
    cursor: pointer;
}

.select-container {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 150px;
    gap: 12px;
    margin: 0 0 14px;
}

#addedValuesInput {
    min-height: 92px;
    resize: vertical;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 14px;
}

@media (max-width: 760px) {
    .page-config-shell {
        padding: 14px;
        border-radius: 12px;
    }

    .page-config-header,
    .page-config-actions,
    .page-row,
    .page-row:first-child,
    .select-container {
        grid-template-columns: 1fr;
    }

    .page-config-title,
    .form-toolbar,
    .modal-actions {
        align-items: stretch;
        flex-direction: column;
    }

    .page-config-icon {
        width: 42px;
        height: 42px;
        flex-basis: 42px;
    }

    .page-count-pill,
    .page-action-button,
    .compact-button,
    .primary-submit,
    .remove-page-row {
        width: 100%;
    }

    #page-list {
        grid-template-columns: 1fr;
    }
}
</style>

<style>
:root {
    --ink-black: #01161e;
    --dark-teal: #124559;
    --air-force-blue: #598392;
    --ash-grey: #aec3b0;
    --beige: #eff6e0;
    --card: #f8fbf5;
    --text: #ffffff;
    --muted-text: #d8e8e2;
}

.page-config-shell {
    background:
        radial-gradient(circle at top left, rgba(94, 135, 150, 0.12), transparent 35%),
        linear-gradient(180deg, var(--beige) 0%, #fcfff8 100%);
    border: 1px solid rgba(174, 195, 176, 0.55);
    box-shadow: 0 24px 70px rgba(1, 22, 30, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.5);
    max-width: 1040px;
    padding: 26px;
    border-radius: 22px;
}

.page-config-header,
.general-form.page-form,
.tab-container,
.page-sequence-card {
    border-color: rgba(174, 195, 176, 0.45);
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 16px 40px rgba(1, 22, 30, 0.09);
}

.page-config-header {
    border-left: 6px solid var(--dark-teal);
    padding: 22px 24px;
}

.page-config-icon,
.page-list-index {
    background: linear-gradient(135deg, rgba(89, 131, 146, 0.14), rgba(174, 195, 176, 0.18));
    color: var(--dark-teal);
    box-shadow: 0 6px 18px rgba(1, 22, 30, 0.12);
}

.page-config-eyebrow,
.page-config-header h4,
.form-toolbar h5,
.sequence-modal-header h4,
.tab-heading h5 {
    color: var(--ink-black);
}

.page-config-subtitle,
.form-toolbar p,
.tab-heading p {
    color: var(--dark-teal);
}

.page-count-pill {
    border-color: rgba(18, 69, 89, 0.25);
    background: rgba(255, 246, 224, 0.92);
    color: var(--dark-teal);
    box-shadow: 0 8px 22px rgba(1, 22, 30, 0.08);
}

.page-config-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    margin: 18px 0;
}

.page-action-button,
.compact-button,
.primary-submit {
    gap: 12px;
    border-radius: 14px;
    letter-spacing: 0.02em;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 700;
    transition: transform 0.22s ease, box-shadow 0.22s ease, background-color 0.22s ease, color 0.22s ease;
}

.page-action-button i,
.compact-button i,
.primary-submit i {
    font-size: 18px;
    line-height: 1;
    display: inline-block;
}

.page-action-button span,
.compact-button span,
.primary-submit span {
    display: inline-block;
}

.page-action-button:first-child {
    border: 1px solid rgba(89, 131, 146, 0.25);
    background: rgba(89, 131, 146, 0.12);
    color: var(--ink-black);
}

.page-action-button:nth-child(2) {
    border: 1px solid rgba(18, 69, 89, 0.25);
    background: rgba(18, 69, 89, 0.12);
    color: var(--ink-black);
}

.compact-button {
    background: var(--dark-teal);
    color: var(--beige);
    border: 1px solid rgba(18, 69, 89, 0.6);
}

.primary-submit {
    background: linear-gradient(135deg, var(--air-force-blue) 0%, var(--dark-teal) 100%);
    color: var(--beige);
    box-shadow: 0 18px 32px rgba(1, 22, 30, 0.22);
}

.page-action-button:hover,
.compact-button:hover,
.primary-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 36px rgba(1, 22, 30, 0.26);
}

.page-action-button:hover {
    background: rgba(89, 131, 146, 0.22);
    color: var(--ink-black);
}

.compact-button:hover,
.primary-submit:hover {
    background: var(--air-force-blue);
    color: var(--beige);
}

#page-list li {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    align-items: center;
    gap: 12px;
    min-height: 58px;
    padding: 14px 16px;
    border: 1px solid rgba(174, 195, 176, 0.6);
    border-left: 4px solid var(--dark-teal);
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.96);
    color: var(--ink-black);
    box-shadow: 0 10px 22px rgba(1, 22, 30, 0.05);
    transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease, background-color 0.22s ease;
}

#page-list li:hover {
    transform: translateY(-2px);
    box-shadow: 0 18px 34px rgba(1, 22, 30, 0.18);
    border-color: rgba(18, 69, 89, 0.85);
    background: rgba(239, 246, 224, 1);
}

.page-action-button:disabled,
.compact-button:disabled,
.primary-submit:disabled {
    background: rgba(174, 195, 176, 0.35);
    color: rgba(1, 22, 30, 0.45);
    box-shadow: none;
}

.tab-heading {
    background: rgba(239, 246, 224, 0.9);
    border-bottom-color: rgba(174, 195, 176, 0.45);
}

.tab-heading > i,
.sequence-modal-header > i,
#page-list li > i,
.form-toolbar h5 i {
    color: var(--dark-teal);
}

#page-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 14px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.page-config-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(180px, 1fr));
    gap: 14px;
    margin: 18px 0;
}

#page-list li {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    align-items: center;
    gap: 12px;
    min-height: 58px;
    padding: 14px 16px;
    border: 1px solid rgba(174, 195, 176, 0.6);
    border-left: 4px solid var(--dark-teal);
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.96);
    color: var(--ink-black);
    box-shadow: 0 10px 22px rgba(1, 22, 30, 0.05);
}

.page-list-index {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(89, 131, 146, 0.14);
    color: var(--dark-teal);
    font-weight: 800;
}

.page-list-name {
    font-weight: 700;
    color: var(--ink-black);
    overflow-wrap: anywhere;
}

#page-list li > i {
    color: var(--air-force-blue);
    font-size: 18px;
}

.page-row {
    border-color: rgba(174, 195, 176, 0.65);
    background: rgba(239, 246, 224, 0.9);
    padding: 16px;
}

.form-group label,
.sequence-field label,
.sequence-output-label {
    color: var(--ink-black);
}

.form-control,
#addedValuesInput {
    border-color: rgba(174, 195, 176, 0.75);
    color: var(--ink-black);
    background: #ffffff;
    border: 2px solid rgba(174, 195, 176, 0.85);
    transition: all 0.3s ease;
}

.form-control:focus,
#addedValuesInput:focus {
    border-color: var(--air-force-blue);
    box-shadow: 0 0 0 4px rgba(89, 131, 146, 0.16), inset 0 1px 3px rgba(1, 22, 30, 0.04);
    background: #f7f9f5;
}

.remove-page-row {
    background: rgba(255, 140, 113, 0.2);
    color: var(--ink-black);
    border: 1px solid rgba(174, 195, 176, 0.65);
}

.remove-page-row:hover {
    background: var(--dark-teal);
    color: var(--beige);
}

.modal {
    background: rgba(1, 22, 30, 0.65);
}

.close {
    background: rgba(239, 246, 224, 0.95);
    color: var(--ink-black);
}

.close:hover {
    background: var(--air-force-blue);
    color: var(--beige);
}

@media (max-width: 980px) {
    .page-config-actions {
        grid-template-columns: 1fr;
    }

    .tab-heading,
    .page-config-header,
    .general-form.page-form,
    .page-sequence-card {
        padding: 18px 18px 20px 18px;
    }

    .page-action-button,
    .compact-button,
    .primary-submit {
        width: 100%;
    }

    .page-row,
    .select-container {
        grid-template-columns: 1fr;
    }

    #page-list {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    #page-list li {
        grid-template-columns: auto 1fr;
    }

    .page-config-icon,
    .page-count-pill {
        width: 100%;
    }
}

@media (max-width: 640px) {
    .page-config-shell {
        padding: 18px;
    }

    .page-config-header,
    .page-config-actions,
    .form-toolbar,
    .modal-actions {
        grid-template-columns: 1fr;
    }

    .page-row,
    .select-container {
        gap: 10px;
    }

    .page-action-button,
    .compact-button,
    .primary-submit {
        width: 100%;
        font-size: 14px;
    }

    #page-list {
        grid-template-columns: 1fr;
    }

    #page-list li {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .page-list-index {
        margin-bottom: 0;
    }
}
</style>

<style>
.page-config-shell {
    width: 100% !important;
    max-width: 100% !important;
    padding: 18px 12px !important;
    box-sizing: border-box;
}

.page-config-actions {
    grid-template-columns: repeat(2, minmax(160px, 1fr)) !important;
    gap: 12px !important;
}

.page-action-button,
.compact-button,
.primary-submit {
    width: auto !important;
    min-width: 180px !important;
    max-width: 280px !important;
    padding: 12px 18px !important;
    justify-self: center !important;
}

.page-config-actions button {
    justify-self: center !important;
}

#page-list {
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)) !important;
    gap: 12px !important;
}

#page-list li {
    min-width: 0 !important;
}

@media (max-width: 980px) {
    .page-config-actions {
        grid-template-columns: 1fr !important;
    }

    .tab-heading,
    .page-config-header,
    .general-form.page-form,
    .page-sequence-card {
        padding: 16px 14px !important;
    }

    .page-row,
    .select-container {
        grid-template-columns: 1fr !important;
    }

    #page-list {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)) !important;
    }

    #page-list li {
        grid-template-columns: auto 1fr !important;
    }
}

@media (max-width: 640px) {
    .page-config-shell {
        padding: 14px 10px !important;
    }

    .page-config-header,
    .page-config-actions,
    .form-toolbar,
    .modal-actions {
        grid-template-columns: 1fr !important;
    }

    .page-row,
    .select-container {
        gap: 10px !important;
    }

    .page-action-button,
    .compact-button,
    .primary-submit {
        width: 100% !important;
        font-size: 13px !important;
        padding: 12px 14px !important;
        max-width: none !important;
    }

    #page-list {
        grid-template-columns: 1fr !important;
        gap: 10px !important;
    }

    #page-list li {
        grid-template-columns: 1fr !important;
        gap: 10px !important;
        padding: 14px 12px !important;
    }
}

@media (max-width: 480px) {
    .page-config-header {
        grid-template-columns: 1fr !important;
    }

    .page-config-actions {
        gap: 10px !important;
    }

    .page-config-actions button {
        padding: 12px 14px !important;
    }
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
var pages_names = <?php echo json_encode($pageOptions); ?>;
var pagesDataUrl = <?php echo json_encode($pagesDataUrl); ?>;

function uniquePages(pages) {
    var seen = {};
    var cleanPages = [];

    pages.forEach(function(pageName) {
        pageName = $.trim(pageName || '');

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
        var pageName = $.trim($(this).find('input[name="input_box[]"]').val());
        var extension = $(this).find('select[name="dropdown[]"]').val();

        if (pageName && extension) {
            enteredPages.push(pageName + '.' + extension);
        }
    });

    return enteredPages;
}

function refreshPageCount() {
    $('#pageCount').text(pages_names.length);
}

function renderPageList() {
    $('#page-list').empty();

    if (!pages_names.length) {
        $('#page-list').append($('<li class="empty-page-item">').html('<i class="fa fa-info-circle"></i><span>No pages saved yet</span>'));
        return;
    }

    pages_names.forEach(function(pageName, index) {
        $('#page-list').append(
            $('<li>').html(
                '<span class="page-list-index">' + (index + 1) + '</span>' +
                '<span class="page-list-name"></span>' +
                '<i class="fa fa-file-text-o"></i>'
            ).find('.page-list-name').text(pageName).end()
        );
    });
}

function refreshSequenceOptions() {
    var pageDropdown = $('#pageNamesDropdown');

    pageDropdown.empty();

    if (!pages_names.length) {
        pageDropdown.append($('<option>').val('').text('No pages found'));
        $('#sequence').prop('disabled', true);
        $('#addButton').prop('disabled', true);
    } else {
        pages_names.forEach(function(pageName) {
            pageDropdown.append($('<option>').val(pageName).text(pageName));
        });
        $('#sequence').prop('disabled', false);
        $('#addButton').prop('disabled', false);
    }

    populateSequenceDropdown();
}

function syncCombinedPagesInput() {
    $('#page_names_combined').val(getEnteredPages().join(', '));
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
            }

            if (typeof callback === 'function') {
                callback(response);
            }
        },
        error: function() {
            alert('Unable to load saved pages.');
        }
    });
}

function buildPageRow(rowNumber, removable) {
    var removeButton = removable ? '<button type="button" class="btn btn-secondary compact-button remove-page-row" aria-label="Remove page"><i class="fa fa-trash"></i></button>' : '';

    return `
        <div class="page-row">
            <div class="form-group page-name-field">
                <label for="input-box-${rowNumber}">Page Name</label>
                <input type="text" name="input_box[]" id="input-box-${rowNumber}" class="form-control" placeholder="Example: Home" required>
            </div>

            <div class="form-group page-extension-field">
                <label for="dropdown-${rowNumber}">Extension</label>
                <select name="dropdown[]" id="dropdown-${rowNumber}" class="form-control" required>
                    <option value="test">.Test</option>
                    <option value="config">.Config</option>
                    <option value="paring">.Pairing</option>
                    <option value="operation">.Operation</option>
                </select>
            </div>

            ${removeButton}
        </div>
    `;
}

function clearAddPageForm() {
    $('#form-items').html(buildPageRow(1, false));
    $('#page_names_combined').val('');
}

function populateSequenceDropdown() {
    var pageNamesDropdown = document.getElementById("pageNamesDropdown");
    var sequenceDropdown = document.getElementById("sequenceDropdown");
    var numPages = pageNamesDropdown.value ? pageNamesDropdown.options.length : 0;

    sequenceDropdown.innerHTML = "";

    if (!pageNamesDropdown.value) {
        return;
    }

    for (var i = 1; i <= numPages; i++) {
        var option = document.createElement("option");
        option.value = i;
        option.text = i;
        sequenceDropdown.appendChild(option);
    }
}

function refreshSequenceSubmit() {
    var input = document.getElementById("addedValuesInput");
    var submit = document.getElementById("submitPage");

    submit.disabled = !input.value.trim();
}

$(document).ready(function() {
    var modal = document.getElementById("pageSequenceModal");
    var counter = $('#form-items .page-row').length || 1;

    $('#view').click(function() {
        fetchSavedPages(function() {
            $('#tab').show();
            renderPageList();
        });
    });

    $('#sequence').click(function() {
        modal.style.display = "block";
        populateSequenceDropdown();
        refreshSequenceSubmit();
    });

    $('.close').click(function() {
        modal.style.display = "none";
    });

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };

    $('#addButton').click(function() {
        var selectedPage = $('#pageNamesDropdown').val();
        var selectedSequence = $('#sequenceDropdown').val();

        if (!selectedPage || !selectedSequence) {
            return;
        }

        var entry = selectedPage + " - Sequence: " + selectedSequence + "; ";
        var currentValue = $('#addedValuesInput').val();

        $('#addedValuesInput').val(currentValue + entry);
        refreshSequenceSubmit();
    });

    $('#clearSequence').click(function() {
        $('#addedValuesInput').val('');
        refreshSequenceSubmit();
    });

    $('#file-form2').submit(function(event) {
        event.preventDefault();

        var form = $(this);
        var submitButton = form.find('.primary-submit');
        var submittedPages = uniquePages(getEnteredPages());

        if (!submittedPages.length) {
            alert('Please add at least one page.');
            return;
        }

        syncCombinedPagesInput();
        submitButton.prop('disabled', true);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            dataType: 'json',
            data: form.serialize(),
            success: function(response) {
                if (response && response.success) {
                    pages_names = uniquePages(response.pages || submittedPages);
                    clearAddPageForm();
                    counter = 1;
                    renderPageList();
                    refreshPageCount();
                    refreshSequenceOptions();
                    alert(response.message || 'Data saved successfully!');
                } else {
                    alert((response && response.message) || 'Error saving data to database!');
                }
            },
            error: function() {
                alert('Error saving data to database!');
            },
            complete: function() {
                submitButton.prop('disabled', false);
            }
        });
    });

    $('#page-sequence-form').submit(function(event) {
        event.preventDefault();

        if (!$('#addedValuesInput').val().trim()) {
            refreshSequenceSubmit();
            return;
        }

        var form = $(this);
        var submitButton = $('#submitPage');

        submitButton.prop('disabled', true);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                alert($.trim(response) || 'Page sequence saved successfully!');
                $('#addedValuesInput').val('');
                refreshSequenceSubmit();
                modal.style.display = "none";
                fetchSavedPages();
            },
            error: function() {
                alert('Error saving page sequence!');
            },
            complete: function() {
                submitButton.prop('disabled', false);
                refreshSequenceSubmit();
            }
        });
    });

    $('#add-more').click(function() {
        counter++;
        var newFormGroup = buildPageRow(counter, true);

        $('#form-items').append(newFormGroup);
        $('#input-box-' + counter).focus();
        syncCombinedPagesInput();
    });

    $('#form-items').on('click', '.remove-page-row', function() {
        $(this).closest('.page-row').remove();
        syncCombinedPagesInput();
    });

    $('#form-items').on('input change', 'input[name="input_box[]"], select[name="dropdown[]"]', function() {
        syncCombinedPagesInput();
    });

    refreshPageCount();
    refreshSequenceOptions();
    syncCombinedPagesInput();
});
</script>
