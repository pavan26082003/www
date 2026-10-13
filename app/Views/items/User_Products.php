<?php
session_start();
$session_user_id = $login_user->id ?? null;
$_SESSION['user_id'] = $session_user_id;

$product_details = $product_details ?? [];
$api_info = $api_info ?? [];
$user_type = $user_type ?? '';
$node_id = $node_id ?? ($product_details['node_id'] ?? '');
$model_number = $model_number ?? ($product_details['model_number'] ?? '');
$related_user_id = $user_id ?? ($product_details['user_id'] ?? '');

$has_value = function ($value) {
    return $value !== null && $value !== '' && strtoupper(trim((string) $value)) !== 'NULL';
};

$clean_value = function ($value) {
    if (is_array($value) || is_object($value)) {
        $encoded = json_encode($value);
        return $encoded ?: '';
    }

    return (string) $value;
};

$format_label = function ($key) {
    $labels = [
        'RSSI' => 'RSSI',
        'FW_Version' => 'FW Version',
        'BaudRate' => 'Baud Rate',
        'node_id' => 'Node ID',
        'user_id' => 'User ID',
        'signin_latitude' => 'Sign-in Latitude',
        'signin_longitude' => 'Sign-in Longitude'
    ];

    if (isset($labels[$key])) {
        return $labels[$key];
    }

    return ucwords(str_replace('_', ' ', (string) $key));
};

$pick = function ($keys, $fallback = '') use ($product_details, $has_value) {
    foreach ($keys as $key) {
        if (array_key_exists($key, $product_details) && $has_value($product_details[$key])) {
            return $product_details[$key];
        }
    }

    return $fallback;
};

$main_keys = [
    'user',
    'auto_test',
    'mode',
    'node_id',
    'brand',
    'device_type',
    'model_number',
    'product_name',
    'user_id',
    'phone',
    'internet',
    'BaudRate',
    'RSSI',
    'FW_Version',
    'power',
    'latitude',
    'longitude',
    'signin_latitude',
    'signin_longitude'
];

$device_title = $pick(['product_name', 'current_devicetype', 'device_type', 'prod_category'], 'Device Details');
$device_type = $pick(['device_type', 'current_devicetype'], 'Device');
$internet_value = strtolower((string) $pick(['internet', 'mode', 'product_status'], ''));
$is_online = in_array($internet_value, ['online', 'true', '1', 'active'], true);
$status_text = $is_online ? 'Online' : ($internet_value ? 'Offline' : 'Status Unknown');
$encoded_model_number = rawurlencode((string) $model_number);
$query = http_build_query(['node_id' => $node_id, 'userid' => $related_user_id]);

$api_rows = [];
if (!empty($api_info)) {
    $first_api_row = $api_info[0] ?? $api_info;
    if (is_object($first_api_row)) {
        $api_rows = get_object_vars($first_api_row);
    } elseif (is_array($first_api_row)) {
        $api_rows = $first_api_row;
    }
}

$api_hidden_keys = [];

if ($user_type === "User") {
    $api_hidden_keys = array_merge($api_hidden_keys, [
        "TZ_properties", "BR_properties", "UMI_properties", "EC_properties", "CM_properties", "TU_properties",
        "LC_properties", "display_properties"
    ]);
}

foreach ($api_hidden_keys as $hidden_key) {
    unset($api_rows[$hidden_key]);
}

$api_visible_rows = [];
foreach ($api_rows as $key => $value) {
    $api_visible_rows[$key] = $value;
}
?>

<style>
    .modal-dialog {
        max-width: min(1180px, calc(100vw - 32px)) !important;
    }

    .modal-dialog .modal-content {
        border: 0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(15, 23, 42, .28);
    }

    .modal-dialog .modal-content .modal-header {
        background: linear-gradient(135deg, #373F51, #58A4B0) !important;
        border-bottom: 0 !important;
        color: #ffffff !important;
    }

    .modal-dialog .modal-content .modal-title {
        color: #ffffff !important;
        font-weight: 800;
    }

    .modal-dialog .modal-header .close,
    .modal-dialog .modal-header .btn-close {
        color: #ffffff;
        opacity: .92;
        filter: invert(1) grayscale(1);
    }

    .device-details-popup {
        --popup-bg: #D8DBE2;
        --popup-panel: #ffffff;
        --popup-soft: #eef2f6;
        --popup-text: #1B1B1E;
        --popup-muted: #526076;
        --popup-border: rgba(55, 63, 81, .18);
        --popup-graphite: #1B1B1E;
        --popup-graphite-2: #373F51;
        --popup-green: #58A4B0;
        --popup-green-2: #A9BCD0;
        --popup-amber: #D8DBE2;
        --popup-plum: #6f5c86;
        --popup-danger: #d95c59;
        background:
            linear-gradient(180deg, rgba(255,255,255,.95) 0%, rgba(216,219,226,.92) 100%),
            radial-gradient(at 18% 0%, rgba(88,164,176,.18), transparent 38%),
            radial-gradient(at 92% 16%, rgba(169,188,208,.48), transparent 34%);
        color: var(--popup-text);
        padding: 18px;
        border-radius: 8px;
        font-family: Roboto, Arial, sans-serif;
        max-width: 100%;
        overflow: hidden;
    }

    .device-details-popup *,
    .device-details-popup *::before,
    .device-details-popup *::after {
        box-sizing: border-box;
    }

    .device-details-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        padding: 18px;
        background:
            linear-gradient(135deg, rgba(55, 63, 81, .98), rgba(88, 164, 176, .92)),
            var(--popup-panel);
        border: 1px solid rgba(169, 188, 208, .34);
        border-radius: 8px;
        box-shadow: 0 12px 28px rgba(38, 50, 56, .08);
        margin-bottom: 14px;
    }

    .device-details-title-area {
        min-width: 0;
    }

    .device-details-title {
        margin: 0 0 10px;
        color: #ffffff;
        font-size: 24px;
        font-weight: 800;
        line-height: 1.2;
        overflow-wrap: anywhere;
    }

    .device-details-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .device-details-chip,
    .device-details-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-width: 0;
        max-width: 100%;
        min-height: 32px;
        padding: 7px 10px;
        border: 1px solid var(--popup-border);
        border-radius: 999px;
        background: rgba(255, 255, 255, .16);
        color: #ffffff;
        border-color: rgba(255, 255, 255, .22);
        font-size: 13px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .device-details-status {
        flex: 0 0 auto;
        text-transform: capitalize;
    }

    .device-status-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: var(--popup-danger);
        box-shadow: 0 0 0 4px rgba(217, 92, 89, .13);
    }

    .device-details-status.is-online .device-status-dot {
        background: var(--popup-green);
        box-shadow: 0 0 0 4px rgba(88, 164, 176, .18);
    }

    .device-details-actions {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 10px;
        padding: 12px;
        margin-bottom: 16px;
        background:
            linear-gradient(135deg, rgba(216, 219, 226, .92), rgba(169, 188, 208, .72)),
            radial-gradient(at 0% 0%, rgba(88, 164, 176, .18), transparent 42%);
        border: 1px solid rgba(169, 188, 208, .46);
        border-radius: 8px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .65), 0 12px 28px rgba(55, 63, 81, .1);
    }

    .device-details-actions a,
    .device-details-actions button {
        min-height: 42px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 10px 14px !important;
        border: 1px solid rgba(55, 63, 81, .14) !important;
        border-radius: 999px !important;
        background: linear-gradient(135deg, #ffffff, #eef2f6) !important;
        color: var(--popup-text) !important;
        font-size: 13px;
        font-weight: 800;
        line-height: 1.1;
        text-decoration: none !important;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
        box-shadow: 0 8px 18px rgba(55, 63, 81, .1), inset 0 1px 0 rgba(255, 255, 255, .72);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease, color .18s ease;
    }

    .device-details-actions a i,
    .device-details-actions button i,
    .device-details-action-icon {
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(55, 63, 81, .1);
        color: var(--popup-graphite);
        font-size: 13px;
        flex: 0 0 24px;
    }

    .device-details-action-icon svg {
        width: 14px;
        height: 14px;
        display: block;
        stroke: currentColor;
    }

    .device-details-actions a:hover,
    .device-details-actions button:hover,
    .device-details-actions button.active {
        transform: translateY(-1px);
        box-shadow: 0 14px 26px rgba(55, 63, 81, .18);
        border-color: rgba(88, 164, 176, .42) !important;
    }

    .device-details-actions .device-tab-btn[data-device-panel="product-panel"] {
        background: linear-gradient(135deg, #373F51, #58A4B0) !important;
        color: #ffffff !important;
        border-color: transparent !important;
        box-shadow: 0 12px 24px rgba(55, 63, 81, .18), inset 0 1px 0 rgba(255, 255, 255, .18) !important;
    }

    .device-details-actions .device-tab-btn[data-device-panel="api-panel"] {
        background: linear-gradient(135deg, #D8DBE2, #A9BCD0) !important;
        color: #1B1B1E !important;
        border-color: rgba(55, 63, 81, .08) !important;
        box-shadow: 0 12px 24px rgba(55, 63, 81, .12), inset 0 1px 0 rgba(255, 255, 255, .65) !important;
    }

    .device-tab-btn.active {
        outline: 3px solid rgba(88, 164, 176, .26);
        outline-offset: 2px;
        filter: saturate(1.12) contrast(1.04);
    }

    .device-tab-btn.active i {
        background: rgba(255, 255, 255, .18);
        color: #ffffff;
    }

    .device-details-actions .device-tab-btn[data-device-panel="api-panel"].active i {
        background: rgba(27, 27, 30, .12);
        color: #1B1B1E;
    }

    .device-details-actions .device-control-action,
    .device-details-actions .device-chat-action,
    .device-details-actions .device-fw-action {
        color: #ffffff !important;
        border-color: transparent !important;
        box-shadow: 0 12px 24px rgba(55, 63, 81, .18), inset 0 1px 0 rgba(255, 255, 255, .18) !important;
    }

    .device-details-actions .device-control-action {
        background: linear-gradient(135deg, #58A4B0, #373F51) !important;
    }

    .device-details-actions .device-chat-action {
        background: linear-gradient(135deg, #6f5c86, #373F51) !important;
    }

    .device-details-actions .device-fw-action {
        background: linear-gradient(135deg, #A9BCD0, #58A4B0) !important;
        color: #1B1B1E !important;
    }

    .device-details-actions .device-control-action:hover,
    .device-details-actions .device-chat-action:hover,
    .device-details-actions .device-fw-action:hover,
    .device-details-actions .device-tab-btn:hover {
        filter: saturate(1.16) contrast(1.04);
    }

    .device-details-actions .device-control-action i,
    .device-details-actions .device-chat-action i,
    .device-details-actions .device-fw-action i,
    .device-details-actions .device-control-action .device-details-action-icon,
    .device-details-actions .device-chat-action .device-details-action-icon,
    .device-details-actions .device-fw-action .device-details-action-icon {
        background: rgba(255, 255, 255, .2) !important;
        color: #ffffff !important;
    }

    .device-details-actions .device-control-action svg,
    .device-details-actions .device-chat-action svg,
    .device-details-actions .device-fw-action svg {
        stroke: currentColor !important;
    }

    .device-details-actions .device-fw-action .device-details-action-icon {
        background: rgba(27, 27, 30, .12) !important;
        color: #1B1B1E !important;
    }

    .device-api-action {
        color: var(--popup-plum) !important;
        border-color: rgba(111, 92, 134, .24) !important;
    }

    .device-api-action i {
        background: rgba(111, 92, 134, .12) !important;
        color: var(--popup-plum) !important;
    }

    .device-details-panel {
        display: none;
    }

    .device-details-panel.active {
        display: block;
    }

    .device-details-section {
        margin-top: 16px;
    }

    .device-details-section-title {
        margin: 0 0 10px;
        color: var(--popup-text);
        font-size: 17px;
        font-weight: 800;
    }

    .device-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 10px;
    }

    .device-detail-box {
        min-width: 0;
        padding: 12px;
        background: linear-gradient(180deg, #ffffff, #f8fbfd);
        border: 1px solid var(--popup-border);
        border-radius: 8px;
        box-shadow: 0 8px 20px rgba(38, 50, 56, .05);
        transition: transform .16s ease, border-color .16s ease, box-shadow .16s ease, background .16s ease;
    }

    .device-detail-box:hover {
        transform: translateY(-2px);
        border-color: rgba(88, 164, 176, .38);
        background: #ffffff;
        box-shadow: 0 14px 26px rgba(38, 50, 56, .12);
    }

    .device-detail-box:hover .device-detail-label {
        color: var(--popup-green);
    }

    .device-detail-label {
        margin-bottom: 6px;
        color: var(--popup-muted);
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .device-detail-value {
        color: var(--popup-text);
        font-size: 14px;
        font-weight: 700;
        line-height: 1.35;
        overflow-wrap: anywhere;
        white-space: pre-wrap;
    }

    .device-details-empty {
        padding: 22px;
        background: var(--popup-panel);
        border: 1px dashed var(--popup-border);
        border-radius: 8px;
        color: var(--popup-muted);
        text-align: center;
    }

    @media (max-width: 640px) {
        .modal-dialog {
            max-width: calc(100vw - 12px) !important;
            margin: 6px auto !important;
        }

        .device-details-popup {
            padding: 10px;
        }

        .device-details-header {
            flex-direction: column;
            gap: 12px;
            padding: 14px;
        }

        .device-details-title-area,
        .device-details-status {
            width: 100%;
        }

        .device-details-status {
            justify-content: center;
        }

        .device-details-chip {
            flex: 1 1 calc(50% - 8px);
            justify-content: center;
            text-align: center;
        }

        .device-details-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            padding: 10px;
        }

        .device-details-actions .device-tab-btn:first-child {
            grid-column: auto;
        }

        .device-details-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .device-detail-box {
            padding: 10px;
        }

        .device-detail-label {
            font-size: 11px;
        }

        .device-detail-value {
            font-size: 13px;
        }

        .device-details-title {
            font-size: 21px;
        }
    }

    @media (max-width: 380px) {
        .device-details-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 7px;
            padding: 8px;
        }

        .device-details-actions a,
        .device-details-actions button {
            min-height: 40px;
            justify-content: center;
            gap: 7px;
            padding: 8px 9px !important;
            font-size: 12px;
        }

        .device-details-action-icon,
        .device-details-actions a i,
        .device-details-actions button i {
            width: 22px;
            height: 22px;
            flex-basis: 22px;
        }

        .device-details-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .device-details-chip {
            flex-basis: 100%;
        }
    }

    @media (max-width: 330px) {
        .device-details-actions,
        .device-details-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="device-details-popup">
    <div class="device-details-header">
        <div class="device-details-title-area">
            <h2 class="device-details-title"><?= htmlspecialchars($clean_value($device_title)); ?></h2>
            <div class="device-details-meta">
                <span class="device-details-chip"><i class="fa fa-plug" aria-hidden="true"></i><?= htmlspecialchars($clean_value($device_type)); ?></span>
                <span class="device-details-chip"><i class="fa fa-sitemap" aria-hidden="true"></i>Node ID: <?= htmlspecialchars($clean_value($node_id ?: '--')); ?></span>
                <span class="device-details-chip"><i class="fa fa-barcode" aria-hidden="true"></i>Model: <?= htmlspecialchars($clean_value($model_number ?: '--')); ?></span>
            </div>
        </div>
        <div class="device-details-status <?= $is_online ? 'is-online' : 'is-offline'; ?>">
            <span class="device-status-dot"></span>
            <span><?= htmlspecialchars($status_text); ?></span>
        </div>
    </div>

    <div class="device-details-actions">
        <button type="button" class="device-tab-btn active" data-device-panel="product-panel">
            <i class="fa fa-id-card-o" aria-hidden="true"></i>
            User & Product
        </button>
        <button type="button" class="device-tab-btn device-api-action" data-device-panel="api-panel">
            <i class="fa fa-code" aria-hidden="true"></i>
            APIs Info
        </button>
        <a href="<?= get_uri("items/ChatDisplayFunction/" . $encoded_model_number . "?" . $query); ?>" class="device-chat-action" title="Chat">
            <span class="device-details-action-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5A8.5 8.5 0 0 1 21 11v.5z"></path>
                </svg>
            </span>
            <span>Chat</span>
        </a>
        <a href="<?= get_uri("items/controlDisplayFunction/" . $encoded_model_number . "?" . $query); ?>" class="device-control-action" title="Control">
            <span class="device-details-action-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" y1="21" x2="4" y2="14"></line>
                    <line x1="4" y1="10" x2="4" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12" y2="3"></line>
                    <line x1="20" y1="21" x2="20" y2="16"></line>
                    <line x1="20" y1="12" x2="20" y2="3"></line>
                    <line x1="1" y1="14" x2="7" y2="14"></line>
                    <line x1="9" y1="8" x2="15" y2="8"></line>
                    <line x1="17" y1="16" x2="23" y2="16"></line>
                </svg>
            </span>
            <span>Control</span>
        </a>
        <a href="<?= get_uri("items/Fw_Update/" . $encoded_model_number . "?" . $query); ?>" class="device-fw-action" title="FW Update" target="_blank">
            <span class="device-details-action-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 16l-4-4-4 4"></path>
                    <path d="M12 12v9"></path>
                    <path d="M20.4 18.5A5 5 0 0 0 18 9h-1.3A8 8 0 1 0 4 16.3"></path>
                    <path d="M16 21h1a4 4 0 0 0 0-8h-.3"></path>
                </svg>
            </span>
            <span>FW Update</span>
        </a>
    </div>

    <div id="product-panel" class="device-details-panel active">
        <?php if (empty($product_details)): ?>
            <div class="device-details-empty">Product details not found.</div>
        <?php else: ?>
            <section class="device-details-section">
                <h3 class="device-details-section-title">Hidden Card Parameters</h3>
                <div class="device-details-grid">
                    <?php foreach ($main_keys as $key): ?>
                        <?php if (array_key_exists($key, $product_details) && $has_value($product_details[$key])): ?>
                            <div class="device-detail-box">
                                <div class="device-detail-label"><?= htmlspecialchars($format_label($key)); ?></div>
                                <div class="device-detail-value"><?= htmlspecialchars($clean_value($product_details[$key])); ?></div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </section>

            <?php
            $other_items = [];
            foreach ($product_details as $key => $value) {
                if (!in_array($key, $main_keys, true) && $has_value($value)) {
                    $other_items[$key] = $value;
                }
            }
            ?>

            <?php if (!empty($other_items)): ?>
                <section class="device-details-section">
                    <h3 class="device-details-section-title">Other Product Details</h3>
                    <div class="device-details-grid">
                        <?php foreach ($other_items as $key => $value): ?>
                            <div class="device-detail-box">
                                <div class="device-detail-label"><?= htmlspecialchars($format_label($key)); ?></div>
                                <div class="device-detail-value"><?= htmlspecialchars($clean_value($value)); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div id="api-panel" class="device-details-panel">
        <section class="device-details-section">
            <h3 class="device-details-section-title">APIs Info</h3>
            <?php if (empty($api_visible_rows)): ?>
                <div class="device-details-empty">No API information available.</div>
            <?php else: ?>
                <div class="device-details-grid">
                    <?php foreach ($api_visible_rows as $key => $value): ?>
                        <div class="device-detail-box">
                            <div class="device-detail-label"><?= htmlspecialchars($format_label($key)); ?></div>
                            <div class="device-detail-value"><?= htmlspecialchars($has_value($value) ? $clean_value($value) : '--'); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<script>
$(function () {
    $(".device-details-popup .device-tab-btn").on("click", function () {
        const $popup = $(this).closest(".device-details-popup");
        const panelId = $(this).data("device-panel");
        $popup.find(".device-tab-btn").removeClass("active");
        $(this).addClass("active");
        $popup.find(".device-details-panel").removeClass("active");
        $popup.find("#" + panelId).addClass("active");
    });
});
</script>
