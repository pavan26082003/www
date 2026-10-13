<?php

$predefinedFields = [
    "Title",
    "Serial No",
    "SL.No",
    "Product Name",
    "Company Name",
    "Model Name",
    "Batch No",
    "MRP",
    "Manufacture Date",
    "Expiry Date",
    "QRCode",
    "Barcode",
    "Capacity",
    "Output",
    "Working Range",
    "Category",
    "Discount",
    "Location",
    "Supplier",
    "Description",
    "Notes",
    "Status",
    "Customer Name",
    "Order No",
    "Invoice No",
    "Packing Type",
    "Delivery Date",
    "Address"
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <link rel="icon" href="data:,">
    <title>Form edit</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.2.4/fabric.min.js"></script>

    <style>
        /* Global */
        body {
            background: #f3f4f6;
            font-family: 'Inter', sans-serif;
            position: relative;
        }

        .modal {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 80px 20px;
        }

        /* Modal Card */
        .modal-content {
            width: 1000px;
            background: #ffffff;
            border-radius: 16px;
            padding: 40px;
            box-shadow:
                0 40px 80px rgba(0, 0, 0, 0.18),
                0 15px 30px rgba(0, 0, 0, 0.08);
            transform: translateY(40px) scale(0.95);
            opacity: 0;
            transition:
                transform 0.5s cubic-bezier(.16, 1, .3, 1),
                opacity 0.4s ease;
        }

        /* When modal opens */
        .modal.show .modal-content {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .modal-content input,
        .modal-content select {
            width: 100%;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .modal-content input:focus,
        .modal-content select:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
            outline: none;
        }

        /* Header */
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .modal-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            color: #1f2937;
        }

        .modal-badge {
            font-size: 12px;
            font-weight: 500;
            padding: 6px 14px;
            color: #6d28d9;
            background: linear-gradient(135deg, #f5f3ff, #ede9fe);
            border: 1px solid #ddd6fe;
            border-radius: 8px;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 12px rgba(109, 40, 217, 0.08);
        }

        /* Divider */
        .modal-divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 20px 0;
        }

        /* Button Row */
        .modal-button-row {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        /* Field Rows */
        .field-row {
            display: flex;
            gap: 12px;
            margin-bottom: 14px;
            align-items: center;
            padding: 10px;
            background: #fdfdfd;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: background 0.3s;
        }

        .field-row:hover {
            background: #f3faff;
        }

        .field-name,
        .field-value,
        .input-method,
        .keyword-type {
            flex: 1;
        }

        /* Remove Button */
        .btn-delete {
            background: transparent;
            border: none;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-delete:hover {
            color: #dc2626;
            background: rgba(220, 38, 38, 0.06);
        }

        .btn-delete:active {
            transform: scale(0.92);
        }

        /* Base Button */
        .btn {
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .action-button {
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* Primary */
        .btn-primary {
            background: #1a73e8;
            color: #fff;
        }

        .btn-primary:hover {
            background: #1669c1;
            box-shadow: 0 4px 12px rgba(26, 115, 232, 0.25);
        }

        /* Secondary */
        .btn-secondary {
            background: #f3f4f6;
            color: #1f2937;
            border: 1px solid #e5e7eb;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        /* Danger */
        .btn-danger {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .btn-danger:hover {
            background: #fecaca;
        }

        .print-button {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #ffffff;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.25);
        }

        .print-button:hover {
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.35);
        }

        /* Animation */
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .toast-container {
            position: fixed;
            bottom: 28px;
            right: 28px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            z-index: 99999;
        }

        .toast {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            width: auto;
            max-width: 160px;
            min-width: 120px;
            padding: 10px 14px;
            border-radius: 12px;
            background: #ffffff;
            box-shadow:
                0 12px 30px rgba(0, 0, 0, 0.08),
                0 4px 10px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f1f1;
            font-family: 'Inter', sans-serif;
            transform: translateX(30px);
            opacity: 0;
            transition: all 0.25s ease;
            overflow: hidden;
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast.hide {
            transform: translateX(30px);
            opacity: 0;
        }

        .toast-icon {
            font-size: 14px;
            font-weight: 600;
        }

        .toast-content {
            flex: 1;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
        }

        .toast-close {
            font-size: 14px;
            color: #9ca3af;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .toast-close:hover {
            color: #111827;
        }

        .toast.info .toast-icon {
            color: #3b82f6;
        }

        .toast.success .toast-icon {
            color: #16a34a;
        }

        .toast.error .toast-icon {
            color: #dc2626;
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            background: #f3f4f6;
        }

        .toast-progress-bar {
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, #2563eb, #6d28d9, #1f2937);
            transition: width linear;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            text-align: center;
            animation: fadeEmpty 0.4s ease;
        }

        .empty-state img {
            width: 120px;
            opacity: 0.8;
        }

        .empty-state p {
            font-size: 15px;
            font-weight: 500;
            color: #6b7280;
            letter-spacing: 0.3px;
            margin: 0;
        }

        @keyframes fadeEmpty {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <Style>
        /* =====================================================
   Premium Long Value Editor Modal
===================================================== */

        .value-editor-overlay {
            position: fixed;
            inset: 0;
            z-index: 999999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(15, 23, 42, 0.52);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .value-editor-overlay.show {
            display: flex;
            animation: valueEditorOverlayFade 0.18s ease forwards;
        }

        .value-editor-card {
            width: min(680px, 96vw);
            background: #ffffff;
            border-radius: 22px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow:
                0 34px 95px rgba(15, 23, 42, 0.32),
                0 14px 34px rgba(15, 23, 42, 0.16);
            overflow: hidden;
            transform: translateY(14px) scale(0.97);
            animation: valueEditorCardIn 0.24s cubic-bezier(.16, 1, .3, 1) forwards;
        }

        .value-editor-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            padding: 20px 24px;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.13), transparent 34%),
                linear-gradient(135deg, #f8fafc, #eef2ff);
            border-bottom: 1px solid #e5e7eb;
        }

        .value-editor-title {
            margin: 0;
            font-size: 18px;
            line-height: 1.2;
            font-weight: 750;
            color: #111827;
            letter-spacing: -0.02em;
        }

        .value-editor-subtitle {
            margin: 6px 0 0;
            font-size: 12.5px;
            line-height: 1.45;
            font-weight: 500;
            color: #64748b;
        }

        .value-editor-close {
            flex: 0 0 auto;
            width: 36px;
            height: 36px;
            border: 0;
            border-radius: 12px;
            background: rgba(15, 23, 42, 0.06);
            color: #475569;
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .value-editor-close:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            transform: rotate(4deg);
        }

        .value-editor-body {
            padding: 24px;
        }

        .value-editor-textarea {
            width: 100%;
            min-height: 230px;
            max-height: 48vh;
            resize: vertical;
            border: 1px solid #dbe3ef;
            border-radius: 16px;
            padding: 15px 16px;
            font-size: 15px;
            line-height: 1.58;
            color: #111827;
            background: #f8fafc;
            outline: none;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        .value-editor-textarea:focus {
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .value-editor-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 0 24px 24px;
        }

        .value-editor-count {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            white-space: nowrap;
        }

        .value-editor-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .value-editor-btn {
            border: 0;
            border-radius: 12px;
            padding: 10px 17px;
            font-size: 13px;
            font-weight: 750;
            cursor: pointer;
            transition: all 0.18s ease;
            font-family: inherit;
        }

        .value-editor-btn.cancel {
            background: #f1f5f9;
            color: #475569;
        }

        .value-editor-btn.cancel:hover {
            background: #e2e8f0;
        }

        .value-editor-btn.save {
            color: #ffffff;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 14px 26px rgba(37, 99, 235, 0.25);
        }

        .value-editor-btn.save:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 34px rgba(37, 99, 235, 0.32);
        }

        .modal-field-value {
            cursor: pointer !important;
            text-overflow: ellipsis;
        }

        .modal-field-value[readonly] {
            background: #ffffff;
        }

        @keyframes valueEditorOverlayFade {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes valueEditorCardIn {
            to {
                transform: translateY(0) scale(1);
            }
        }

        @media screen and (max-width: 640px) {
            .value-editor-overlay {
                padding: 14px;
            }

            .value-editor-header,
            .value-editor-body {
                padding: 18px;
            }

            .value-editor-footer {
                padding: 0 18px 18px;
                align-items: stretch;
                flex-direction: column;
            }

            .value-editor-actions {
                width: 100%;
            }

            .value-editor-btn {
                flex: 1;
            }

            .value-editor-textarea {
                min-height: 190px;
            }
        }
    </Style>




</head>

<body>

    <!-- Modal -->
    <div id="stickerModal" class="modal">
        <div class="modal-content">

            <div class="modal-header">
                <h1>Sticker Form</h1>
                <span class="modal-badge"><?= esc($modelname ?? '') ?></span>
            </div>

            <div id="modalFieldsContainer"></div>

            <!-- Empty State -->
            <div id="emptyState" class="empty-state" style="display:none;">
                <img src="<?= base_url('files/sticker-images/no_fields_img.svg/') ?>" alt="no fields image">
                <p>No fields yet, please add fields</p>
            </div>

            <hr class="modal-divider">

            <div class="modal-button-row">
                <button type="button" class="btn btn-secondary" onclick="addModalField()">+ Add Field</button>
                <button type="button" class="btn btn-primary" onclick="saveModalFields()">Save</button>
                <!-- Label Designer -->
                <a href="<?= site_url(
                                'items/sticker_print/' . $id .
                                    '?modelno=' . urlencode($modelno) .
                                    '&modelname=' . urlencode($modelname) .
                                    '&category=' . urlencode($category)
                            ) ?>"
                    class="action-button print-button"
                    id="stickerPrintBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                    </svg>
                    Label Designer
                </a>
            </div>

        </div>
    </div>

    <!-- Premium Long Field Value Editor -->
    <div id="valueEditorOverlay" class="value-editor-overlay" aria-hidden="true">
        <div class="value-editor-card" role="dialog" aria-modal="true" aria-labelledby="valueEditorTitle">
            <div class="value-editor-header">
                <div>
                    <h3 id="valueEditorTitle" class="value-editor-title">Edit Field Value</h3>
                    <p class="value-editor-subtitle">
                        Add long descriptions, specifications, address, output range, capacity, or product details.
                    </p>
                </div>

                <button type="button"
                    class="value-editor-close"
                    onclick="closeValueEditor()"
                    aria-label="Close value editor">
                    &times;
                </button>
            </div>

            <div class="value-editor-body">
                <textarea id="valueEditorTextarea"
                    class="value-editor-textarea"
                    maxlength="250"
                    placeholder="Enter field value here..."></textarea>
            </div>

            <div class="value-editor-footer">
                <span id="valueEditorCount" class="value-editor-count">0 / 250</span>

                <div class="value-editor-actions">
                    <button type="button" class="value-editor-btn cancel" onclick="closeValueEditor()">
                        Cancel
                    </button>

                    <button type="button" class="value-editor-btn save" onclick="saveValueEditor()">
                        Apply Value
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script>
        // ─── Toast System ──────────────────────────────────────────────────────────
        (function() {
            function createContainer() {
                let container = document.querySelector(".toast-container");
                if (!container) {
                    container = document.createElement("div");
                    container.className = "toast-container";
                    document.body.appendChild(container);
                }
                return container;
            }

            function getIcon(type) {
                switch (type) {
                    case "success":
                        return "✓";
                    case "error":
                        return "✕";
                    default:
                        return "i";
                }
            }

            window.showToast = function(message, type = "info", duration = 3000) {
                const container = createContainer();
                const toast = document.createElement("div");
                toast.className = `toast ${type}`;
                toast.innerHTML = `
                    <div class="toast-icon">${getIcon(type)}</div>
                    <div class="toast-content">${message}</div>
                    <div class="toast-close">&times;</div>
                    <div class="toast-progress">
                        <div class="toast-progress-bar"></div>
                    </div>
                `;
                container.appendChild(toast);

                const progressBar = toast.querySelector(".toast-progress-bar");
                setTimeout(() => {
                    toast.classList.add("show");
                    progressBar.style.transitionDuration = duration + "ms";
                    progressBar.style.width = "0%";
                }, 20);

                const timeout = setTimeout(() => removeToast(toast), duration);
                toast.querySelector(".toast-close").onclick = () => {
                    clearTimeout(timeout);
                    removeToast(toast);
                };
            };

            function removeToast(toast) {
                toast.classList.remove("show");
                toast.classList.add("hide");
                setTimeout(() => toast.remove(), 200);
            }
        })();

        // ─── Page Constants ────────────────────────────────────────────────────────
        const currentUserId = <?= json_encode(session()->get('user_id') ?? '') ?>;
        const currentModelName = <?= json_encode($modelname ?? '') ?>;
        const predefinedFields = <?= json_encode($predefinedFields) ?>;
        const maxFields = 20;

        const modalFieldsContainer = document.getElementById('modalFieldsContainer');

        // Prevent modal-content click from bubbling
        document.querySelector(".modal-content").addEventListener("click", (e) => e.stopPropagation());

        // ─── addModalField ─────────────────────────────────────────────────────────
        function addModalField(
            name = '',
            value = '',
            inputType = 'manual',
            keywordType = 'variable',
            keywordValueType = 'variable',
            counting = ''
        ) {
            const emptyState = document.getElementById("emptyState");
            emptyState.style.display = "none";

            if (modalFieldsContainer.children.length >= maxFields) {
                showToast(`⚠ Max ${maxFields} fields allowed`, "info");
                return;
            }

            const row = document.createElement('div');
            row.className = 'modal-field-row';
            row.style.cssText = `
                display:flex; align-items:center; gap:10px; margin-bottom:12px;
                background:#f7f7f7; padding:8px; border-radius:6px;
                box-shadow:0 1px 3px rgba(0,0,0,0.1);
            `;

            const displayName = name.trim() === '' ? 'Title' : name.trim();

            const selectedFields = Array.from(modalFieldsContainer.querySelectorAll('.modal-field-name'))
                .map(sel => sel.value)
                .filter(f => f !== 'Title');

            const optionsHTML = `<option value="">-- Select Field --</option><option value="other">Other</option>` +
                predefinedFields.map(f => {
                    if (
                        (f === 'Serial No' && selectedFields.includes('SL.No')) ||
                        (f === 'SL.No' && selectedFields.includes('Serial No'))
                    ) return `<option value="${f}" disabled>${f}</option>`;
                    if (selectedFields.includes(f)) return `<option value="${f}" disabled>${f}</option>`;
                    return `<option value="${f}" ${displayName === f ? 'selected' : ''}>${f}</option>`;
                }).join('');

            row.innerHTML = `
                <select class="modal-field-name"
                        onchange="handleModalOtherField(this)"
                        style="flex:1; padding:6px 8px; border:1px solid #ccc; border-radius:4px;">
                    ${optionsHTML}
                </select>

                <select class="modal-keyvalue-type"
                        onchange="handleModalKeywordValueType(this)"
                        style="flex:1; padding:6px 8px; border:1px solid #ccc; border-radius:4px;">
                    <option value="constant" ${keywordValueType === 'constant' ? 'selected' : ''}>Constant</option>
                    <option value="variable" ${keywordValueType === 'variable' ? 'selected' : ''}>Variable Sticker</option>
                </select>

                <input type="text" class="modal-field-value"
                       placeholder="Value" value="${value}"
                       style="flex:1; padding:6px 8px; border:1px solid #ccc; border-radius:4px;">

                <select class="modal-keyword-type"
                        onchange="handleModalKeywordType(this)"
                        style="flex:1; padding:6px 8px; border:1px solid #ccc; border-radius:4px;">
                    <option value="constant" ${keywordType === 'constant' ? 'selected' : ''}>Constant</option>
                    <option value="variable" ${keywordType === 'variable' ? 'selected' : ''}>Variable Sticker</option>
                </select>

                <input type="text" class="modal-field-counting"
                       placeholder="Counting" value="${counting}"
                       style="flex:1; padding:6px 8px; border:1px solid #ccc; border-radius:4px;">

                <select class="modal-input-type"
                        style="flex:1; padding:6px 8px; border:1px solid #ccc; border-radius:4px;">
                    <option value="manual"   ${inputType === 'manual'   ? 'selected' : ''}>Manual</option>
                    <option value="qr"       ${inputType === 'qr'       ? 'selected' : ''}>From QR</option>
                    <option value="datetime" ${inputType === 'datetime' ? 'selected' : ''}>Date/Time</option>
                </select>

                <button type="button" class="btn-delete">&times;</button>
            `;

            modalFieldsContainer.appendChild(row);
            prepareAllValueInputs();

            // Restore custom field names not in predefinedFields
            const isKnownField = predefinedFields.includes(name.trim());
            if (name.trim() !== '' && !isKnownField) {
                handleModalOtherField(row.querySelector('.modal-field-name'), name.trim(), true);
            }

            // Apply initial disabled states
            const kvTypeEl = row.querySelector('.modal-keyvalue-type');
            const kwTypeEl = row.querySelector('.modal-keyword-type');
            if (kvTypeEl) handleModalKeywordValueType(kvTypeEl);
            if (kwTypeEl) handleModalKeywordType(kwTypeEl);

            // Refresh dropdown mutual-exclusion
            refreshModalFieldOptions();

            // Remove button — UI only, no API call
            row.querySelector('.btn-delete').onclick = () => {
                row.remove();
                if (!modalFieldsContainer.querySelector('.modal-field-row')) {
                    document.getElementById("emptyState").style.display = "flex";
                }
                refreshModalFieldOptions();
            };
        }

        // ─── saveModalFields ───────────────────────────────────────────────────────
        async function saveModalFields() {
            const fieldRows = document.querySelectorAll('.modal-field-row');

            if (!fieldRows.length) {
                showToast("⚠ Add at least one field before saving!", "info");
                return;
            }

            const namesSet = new Set();
            const fieldsJson = [];

            for (const row of fieldRows) {
                const nameEl = row.querySelector('.modal-field-name');
                const name = nameEl ? nameEl.value.trim() : '';
                const value = row.querySelector('.modal-field-value')?.value?.trim() || '';
                const counting = row.querySelector('.modal-field-counting')?.value?.trim() || '';
                const inputType = row.querySelector('.modal-input-type')?.value || 'manual';
                const keywordType = row.querySelector('.modal-keyword-type')?.value || 'variable';
                const keywordValueType = row.querySelector('.modal-keyvalue-type')?.value || 'variable';

                if (!name) {
                    showToast("⚠ Field Name cannot be empty!", "error");
                    if (nameEl) nameEl.focus();
                    return;
                }

                if (name !== "Title" && namesSet.has(name)) {
                    showToast(`⚠ Duplicate field name: ${name}`, "error");
                    if (nameEl) nameEl.focus();
                    return;
                }

                if (name !== "Title") namesSet.add(name);

                fieldsJson.push({
                    name,
                    value,
                    counting,
                    input_type: inputType,
                    keyword_type: keywordType,
                    keyword_value_type: keywordValueType
                });
            }

            const payload = {
                modelname: currentModelName || '',
                user_id: currentUserId || null,
                form_data: {
                    version: 1,
                    fields: fieldsJson
                }
            };

            try {
                const res = await fetch("<?= site_url('items/saveFields') ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (data.status === "success") {
                    showToast("Sticker form saved successfully", "success");
                    setTimeout(() => {
                        window.location.href =
                            "<?= site_url('items/sticker_print?id=' . $id . '&modelno=' . $modelno . '&modelname=' . $modelname . '&category=' . $category) ?>";
                    }, 3000);
                } else {
                    showToast("❌ " + (data.message || "Save failed"), "error");
                }
            } catch (err) {
                console.error("Save error:", err);
                showToast("Server error while saving", "error");
            }
        }

        // ─── fetchStickerForm ──────────────────────────────────────────────────────
        async function fetchStickerForm() {
            modalFieldsContainer.innerHTML =
                '<p style="text-align:center;color:#555;">⏳ Loading...</p>';

            const emptyState = document.getElementById("emptyState");

            try {
                const res = await fetch("<?= site_url('items/getFields') ?>", {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                });

                if (!res.ok) {
                    modalFieldsContainer.innerHTML =
                        `<p style="color:red;text-align:center;">⚠ Server error ${res.status}</p>`;
                    return;
                }

                const data = await res.json();
                modalFieldsContainer.innerHTML = '';
                emptyState.style.display = "none";

                if (!data || data.status !== 'success' || !Array.isArray(data.fields)) {
                    emptyState.style.display = "flex";
                    return;
                }

                const form = data.fields.find(f => f.modelname === currentModelName);

                let fields = [];

                if (form && form.form_data) {
                    if (typeof form.form_data === 'object' && Array.isArray(form.form_data.fields)) {
                        fields = form.form_data.fields;
                    } else if (typeof form.form_data === 'string' && form.form_data.trim() !== '') {
                        try {
                            const parsed = JSON.parse(form.form_data);
                            fields = Array.isArray(parsed.fields) ? parsed.fields : [];
                        } catch (e) {
                            fields = [];
                        }
                    }
                }

                if (!fields.length) {
                    emptyState.style.display = "flex";
                    return;
                }

                fields.forEach(field => {
                    addModalField(
                        field.name || 'Title',
                        field.value || '',
                        field.input_type || 'manual',
                        field.keyword_type || 'variable',
                        field.keyword_value_type || 'variable',
                        field.counting || ''
                    );
                });

            } catch (err) {
                console.error("Fetch failed:", err);
                modalFieldsContainer.innerHTML =
                    '<p style="color:red;text-align:center;">⚠ Failed to load fields</p>';
            }
        }

        // ─── refreshModalFieldOptions ──────────────────────────────────────────────
        function refreshModalFieldOptions() {
            const rows = modalFieldsContainer.querySelectorAll('.modal-field-row');

            const selectedFields = Array.from(rows)
                .map(r => {
                    const el = r.querySelector('.modal-field-name');
                    return el ? el.value : '';
                })
                .filter(f => f !== 'Title' && f !== '' && f !== 'other');

            rows.forEach(row => {
                const nameEl = row.querySelector('.modal-field-name');
                if (!nameEl || nameEl.tagName === 'INPUT') return;

                const currentValue = nameEl.value;

                Array.from(nameEl.options).forEach(opt => {
                    if (opt.value === '' || opt.value === 'other') return;

                    if (opt.value === 'Title') {
                        opt.disabled = false;
                        return;
                    }

                    if (
                        (opt.value === 'Serial No' && selectedFields.includes('SL.No') && currentValue !== 'Serial No') ||
                        (opt.value === 'SL.No' && selectedFields.includes('Serial No') && currentValue !== 'SL.No')
                    ) {
                        opt.disabled = true;
                    } else if (selectedFields.includes(opt.value) && currentValue !== opt.value) {
                        opt.disabled = true;
                    } else {
                        opt.disabled = false;
                    }
                });
            });
        }

        // ─── handleModalOtherField ────────────────────────────────────────────────
        function handleModalOtherField(selectEl, presetValue = '', noFocus = false) {
            if (selectEl.value !== 'other' && !presetValue) return;

            const row = selectEl.closest('.modal-field-row');
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'modal-field-name';
            input.placeholder = "Enter custom field name";
            input.value = presetValue || '';
            input.style.cssText = "flex:1; padding:6px 8px; border:1px solid #ccc; border-radius:4px;";

            selectEl.replaceWith(input);

            const kvSelect = row.querySelector('.modal-keyvalue-type');
            if (kvSelect && kvSelect.value === 'constant') {
                input.setAttribute("disabled", "true");
                input.style.backgroundColor = "#e0e0e0";
            }

            if (!noFocus) input.focus();
        }

        // ─── handleModalKeywordType ────────────────────────────────────────────────
        // Controls VALUE input disabled state
        function handleModalKeywordType(selectEl) {
            const row = selectEl.closest('.modal-field-row');
            const inputValue = row.querySelector('.modal-field-value');
            if (!inputValue) return;

            if (selectEl.value === "constant") {
                inputValue.setAttribute("disabled", "true");
                inputValue.style.backgroundColor = "#e0e0e0";
            } else {
                inputValue.removeAttribute("disabled");
                inputValue.style.backgroundColor = "#fff";
            }
        }

        // ─── handleModalKeywordValueType ──────────────────────────────────────────
        // Controls NAME input disabled state
        function handleModalKeywordValueType(selectEl) {
            const row = selectEl.closest('.modal-field-row');
            const inputName = row.querySelector('.modal-field-name');
            if (!inputName) return;

            if (selectEl.value === "constant") {
                inputName.setAttribute("disabled", "true");
                inputName.style.backgroundColor = "#e0e0e0";
            } else {
                inputName.removeAttribute("disabled");
                inputName.style.backgroundColor = "#fff";
            }
        }

        // ─── Init ─────────────────────────────────────────────────────────────────
        window.addEventListener("DOMContentLoaded", () => {
            const modal = document.getElementById("stickerModal");
            setTimeout(() => modal.classList.add("show"), 100);
            fetchStickerForm();
        });





        /* =====================================================
            Premium Long Value Editor Modal
            ===================================================== */

        let activeValueInput = null;

        function getValueEditorParts() {
            return {
                overlay: document.getElementById('valueEditorOverlay'),
                textarea: document.getElementById('valueEditorTextarea'),
                count: document.getElementById('valueEditorCount')
            };
        }

        function updateValueEditorCount() {
            const {
                textarea,
                count
            } = getValueEditorParts();

            if (!textarea || !count) return;

            count.textContent = `${textarea.value.length} / ${textarea.maxLength}`;
        }

        function openValueEditor(inputEl) {
            if (!inputEl) return;

            activeValueInput = inputEl;

            const {
                overlay,
                textarea
            } = getValueEditorParts();

            if (!overlay || !textarea) return;

            textarea.value = inputEl.value || '';
            updateValueEditorCount();

            overlay.classList.add('show');
            overlay.setAttribute('aria-hidden', 'false');

            document.body.style.overflow = 'hidden';

            setTimeout(() => {
                textarea.focus();
                textarea.setSelectionRange(textarea.value.length, textarea.value.length);
            }, 40);
        }

        function closeValueEditor() {
            const {
                overlay,
                textarea
            } = getValueEditorParts();

            if (overlay) {
                overlay.classList.remove('show');
                overlay.setAttribute('aria-hidden', 'true');
            }

            if (textarea) {
                textarea.value = '';
            }

            document.body.style.overflow = '';

            activeValueInput = null;
        }

        function saveValueEditor() {
            const {
                textarea
            } = getValueEditorParts();

            if (!activeValueInput || !textarea) {
                closeValueEditor();
                return;
            }

            activeValueInput.value = textarea.value.trim();

            activeValueInput.dispatchEvent(new Event('input', {
                bubbles: true
            }));

            const counter = activeValueInput.parentElement?.querySelector('span');

            if (counter) {
                counter.textContent = `${activeValueInput.value.length}/250`;
            }

            closeValueEditor();
        }

        function prepareValueInput(inputEl) {
            if (!inputEl || inputEl.dataset.valueEditorReady === '1') return;

            inputEl.dataset.valueEditorReady = '1';
            inputEl.readOnly = true;
            inputEl.title = 'Click to edit value in a large box';

            if (!inputEl.placeholder || inputEl.placeholder === 'Field Value' || inputEl.placeholder === 'Value') {
                inputEl.placeholder = 'Click to edit value';
            }

            inputEl.style.cursor = 'pointer';
            inputEl.style.textOverflow = 'ellipsis';
        }

        function prepareAllValueInputs() {
            document.querySelectorAll('.modal-field-value').forEach(prepareValueInput);
        }

        /* Open editor for current and future .modal-field-value inputs */
        document.addEventListener('click', function(e) {
            const inputEl = e.target.closest('.modal-field-value');

            if (!inputEl) return;

            prepareValueInput(inputEl);
            openValueEditor(inputEl);
        });

        document.addEventListener('focusin', function(e) {
            if (!e.target.classList || !e.target.classList.contains('modal-field-value')) {
                return;
            }

            prepareValueInput(e.target);
            openValueEditor(e.target);
        });

        document.addEventListener('input', function(e) {
            if (e.target && e.target.id === 'valueEditorTextarea') {
                updateValueEditorCount();
            }
        });

        document.addEventListener('keydown', function(e) {
            const {
                overlay
            } = getValueEditorParts();

            if (!overlay || !overlay.classList.contains('show')) {
                return;
            }

            if (e.key === 'Escape') {
                closeValueEditor();
            }

            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                saveValueEditor();
            }
        });

        document.addEventListener('click', function(e) {
            const {
                overlay
            } = getValueEditorParts();

            if (overlay && overlay.classList.contains('show') && e.target === overlay) {
                closeValueEditor();
            }
        });

        /* Prepare inputs created on page load */
        document.addEventListener('DOMContentLoaded', function() {
            prepareAllValueInputs();
        });
    </script>



</body>

</html>
