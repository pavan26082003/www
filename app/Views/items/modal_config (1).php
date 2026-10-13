<?php
// Build models array from pageNames and nodeIds - FIXED
$models = [];
if (!empty($pageNames)) {
    foreach ($pageNames as $page) {
        if (isset($page['model_name']) && !empty($page['model_name']) && !in_array($page['model_name'], $models)) {
            $models[] = $page['model_name'];
        }
    }
}

if (!empty($nodeIds)) {
    foreach ($nodeIds as $node_id) {
        if (isset($node_id['model_number']) && !empty($node_id['model_number']) && !in_array($node_id['model_number'], $models)) {
            $models[] = $node_id['model_number'];
        }
    }
}

// Also ensure we have models from the selectedmodelNumber if it exists
if (!empty($selectedmodelNumber)) {
    if (is_array($selectedmodelNumber)) {
        foreach ($selectedmodelNumber as $model) {
            if (!empty($model) && !in_array($model, $models)) {
                $models[] = $model;
            }
        }
    } else if (!empty($selectedmodelNumber) && !in_array($selectedmodelNumber, $models)) {
        $models[] = $selectedmodelNumber;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EFACTOR | Professional Configuration Suite</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Choices.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --primary-light: #eff6ff;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #1e293b;
            --gray: #6b7280;
            --light-gray: #f1f5f9;
            --border: #e2e8f0;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            --command-bg: #f0fdf4;
            --command-border: #86efac;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            padding: 2rem;
            min-height: 100vh;
            color: var(--dark);
        }

        .config-wrapper {
            max-width: 1440px;
            margin: 0 auto;
        }

        .config-header {
            margin-bottom: 2rem;
        }

        .config-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.025em;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .config-header h1 i {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            font-size: 2rem;
        }

        .config-header p {
            color: var(--gray);
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }

        .card {
            background: white;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            background: white;
        }

        .card-header h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-header h3 i {
            color: var(--primary);
            font-size: 1.125rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--secondary);
            margin-bottom: 0.5rem;
        }

        .form-label i {
            margin-right: 0.375rem;
            font-size: 0.75rem;
        }

        .required::after {
            content: '*';
            color: var(--danger);
            margin-left: 0.25rem;
        }

        select, input[type="text"], input[type="number"] {
            width: 100%;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            font-family: 'Inter', monospace;
            border: 1.5px solid var(--border);
            border-radius: 0.5rem;
            background: white;
            transition: all 0.2s ease;
            color: var(--dark);
        }

        select:hover, input:hover {
            border-color: var(--primary);
        }

        select:focus, input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin: 1.5rem 0;
        }

        .param-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .param-item {
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background: var(--light-gray);
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-left: 3px solid var(--primary);
            transition: all 0.2s;
        }

        .param-item:hover {
            background: #e6edfb;
            transform: translateX(4px);
        }

        .param-item i {
            color: var(--primary);
            font-size: 0.875rem;
        }

        .config-items-container {
            max-height: 500px;
            overflow-y: auto;
        }

        .config-item {
            background: var(--light-gray);
            padding: 0.875rem;
            margin-bottom: 0.75rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.2s;
        }

        .config-item:hover {
            background: #f8fafc;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .config-item label {
            flex: 1;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .config-item input {
            flex: 2;
            margin: 0;
            font-size: 0.8125rem;
            padding: 0.5rem 0.75rem;
        }

        .command-item {
            background: var(--command-bg);
            padding: 1rem;
            margin-bottom: 0.875rem;
            border-radius: 0.5rem;
            border: 1.5px solid var(--command-border);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .command-item:hover {
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
            transform: translateX(4px);
        }

        .command-info {
            flex: 1;
            min-width: 200px;
        }

        .command-number {
            display: inline-block;
            background: var(--primary);
            color: white;
            font-size: 0.6875rem;
            font-weight: 700;
            padding: 0.125rem 0.5rem;
            border-radius: 0.375rem;
            margin-right: 0.5rem;
        }

        .command-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.375rem;
        }

        .command-name i {
            color: var(--success);
        }

        .command-hex {
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            color: var(--dark);
            background: white;
            padding: 0.375rem 0.875rem;
            border-radius: 0.375rem;
            display: inline-block;
            border: 1px solid var(--border);
            word-break: break-all;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        .command-hex i {
            color: var(--primary);
            margin-right: 0.375rem;
        }

        .command-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .btn-copy-command {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.8125rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .btn-copy-command:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .btn-copy-command:active {
            transform: translateY(0);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.625rem;
            border-radius: 2rem;
            font-size: 0.6875rem;
            font-weight: 500;
        }

        .badge-config {
            background: #d1fae5;
            color: #065f46;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--gray);
            font-size: 0.875rem;
        }

        .empty-state i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
            opacity: 0.5;
        }

        .pairing-section {
            background: linear-gradient(135deg, var(--primary-light) 0%, #f0f9ff 100%);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-top: 1rem;
        }

        .pairing-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .qr-container {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        #qrImage {
            width: 100px;
            height: 100px;
            display: none;
            border: 2px solid white;
            border-radius: 0.75rem;
            box-shadow: var(--card-shadow);
            background: white;
            padding: 0.5rem;
        }

        #inputBox {
            display: none;
            flex: 1;
            min-width: 250px;
        }

        .btn-submit {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .choices__inner {
            background-color: white;
            border: 1.5px solid var(--border);
            border-radius: 0.5rem;
            min-height: 42px;
            font-size: 0.875rem;
        }

        .choices__inner:focus {
            border-color: var(--primary);
        }

        .choices__list--multiple .choices__item {
            background-color: var(--primary);
            border-color: var(--primary-dark);
            border-radius: 0.375rem;
            font-size: 0.75rem;
            padding: 0.25rem 0.625rem;
        }

        .choices__input {
            background-color: transparent;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--light-gray);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray);
        }

        .hidden {
            display: none !important;
        }

        .commands-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border);
        }

        .commands-count {
            font-size: 0.8125rem;
            color: var(--gray);
            font-weight: 500;
        }

        .node-select-wrapper {
            margin-top: 1rem;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-content {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            text-align: center;
            max-width: 400px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .two-columns {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .command-item {
                flex-direction: column;
                align-items: stretch;
            }

            .command-actions {
                justify-content: flex-start;
            }

            .command-hex {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
                word-break: break-all;
            }
        }
    </style>
</head>
<body>
<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="spinner"></div>
        <h3>Sending Configuration</h3>
        <p style="color: var(--gray); margin-top: 0.5rem;">Please wait while the configuration is being sent to the device...</p>
    </div>
</div>

<div class="config-wrapper">
    <!-- Header -->
    <div class="config-header">
        <h1>
            <i class="fas fa-microchip"></i>
            Configuration Suite
        </h1>
        <p>Configure and manage your industrial devices with precision</p>
    </div>

    <!-- Main Form -->
    <form id="configForm" action="https://efactor.tech/v8/product_config.php" method="GET" target="_blank">
        <input type="hidden" name="action" value="Product_config">
        
        <!-- Basic Configuration Card -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h3><i class="fas fa-sliders-h"></i> Basic Configuration</h3>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label required"><i class="fas fa-cube"></i> Model Number</label>
                        <select id="modelSelect" name="model_number" onchange="onModelChange()">
                            <option value="">Select Model</option>
                            <?php if (!empty($models) && is_array($models)): ?>
                                <?php foreach ($models as $model): ?>
                                    <?php if (!empty($model) && is_string($model)): ?>
                                        <option value="<?php echo htmlspecialchars($model); ?>">
                                            <?php echo htmlspecialchars($model); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">No Models Available</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <?php if($pageNo != "Pairing"): ?>
                    <div class="form-group">
                        <label class="form-label required"><i class="fas fa-chart-line"></i> Operation Mode</label>
                        <select id="ModeSelect" name="mode" onchange="onModeChange()">
                            <option value="None">Select Mode</option>
                            <option value="Auto test">Auto Test</option>
                            <option value="Configuration">Configuration</option>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if($pageNo != "Pairing"): ?>
                <!-- Dynamic content based on mode -->
                <div id="modeContent">
                    <!-- Auto Test: Shows Page Names -->
                    <div id="autoTestContent" class="hidden">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label required"><i class="fas fa-file-alt"></i> Page Name</label>
                                <select id="pageSelect" name="page" onchange="loadParametersAndConfigs()">
                                    <option value="">Select Page</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Configuration: Shows Commands -->
                    <div id="configContent" class="hidden">
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-terminal"></i> Available Commands</label>
                            <div id="commandList" class="config-items-container">
                                <div class="empty-state">
                                    <i class="fas fa-code"></i>
                                    <p>Select a model to view available commands</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Common fields -->
                <div class="form-grid" style="margin-top: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-network-wired"></i> Router ID</label>
                        <select id="routerSelect" name="router_id">
                            <option value="">Select Router</option>
                            <?php if (!empty($router_Ids) && is_array($router_Ids)): ?>
                                <?php foreach ($router_Ids as $router): ?>
                                    <?php if(isset($router['node_id'])): ?>
                                        <option value="<?php echo htmlspecialchars($router['node_id']); ?>">
                                            <?php echo htmlspecialchars($router['node_id']); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-signal"></i> Device Status</label>
                        <select id="statusSelect" name="status">
                            <option value="None">Select Status</option>
                            <option value="Online">🟢 Online</option>
                            <option value="Peer to Peer">🔗 Peer to Peer</option>
                        </select>
                    </div>
                </div>

                <!-- Node IDs -->
                <div class="form-group node-select-wrapper">
                    <label class="form-label"><i class="fas fa-id-card"></i> Node IDs (Multiple Selection)</label>
                    <select id="nodeSelect" name="node_ids[]" multiple>
                        <option value=""></option>
                    </select>
                    <small style="color: var(--gray); font-size: 0.6875rem; margin-top: 0.375rem; display: block;">
                        <i class="fas fa-info-circle"></i> You can select multiple node IDs
                    </small>
                </div>
            </div>
        </div>

        <!-- Parameters & Configuration Section (Auto Test Only) -->
        <?php if($pageNo != "Pairing"): ?>
        <div id="autoTestPanel" class="two-columns hidden">
            <!-- Parameters Panel -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-tachometer-alt"></i> Parameters</h3>
                </div>
                <div class="card-body">
                    <div id="paramSelect" class="param-list">
                        <div class="empty-state">
                            <i class="fas fa-info-circle"></i>
                            <p>Select model and page to view parameters</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuration Panel -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-sliders-h"></i> Configuration Values</h3>
                </div>
                <div class="card-body">
                    <div id="configureSelect" class="config-items-container">
                        <div class="empty-state">
                            <i class="fas fa-edit"></i>
                            <p>Configuration fields will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pairing Section -->
        <?php if($pageNo == "Pairing"): ?>
        <div class="card" style="margin-top: 1.5rem;">
            <div class="card-header">
                <h3><i class="fas fa-link"></i> Device Pairing</h3>
            </div>
            <div class="card-body">
                <div class="pairing-section">
                    <div class="pairing-title">
                        <i class="fas fa-globe"></i>
                        Remote Address Configuration
                    </div>
                    <select id="remoteaddress" name="remote_method" onchange="handleSelectionChange()" style="width: 100%; max-width: 300px;">
                        <option value="None">Select Method</option>
                        <option value="qr">📱 Scan QR Code</option>
                        <option value="manual">✍️ Enter Manually</option>
                        <option value="deviceApi">🔌 Device API</option>
                    </select>
                    
                    <div class="qr-container">
                        <img id="qrImage" src="https://efactor.tech/files/home/QR.png" alt="QR Code">
                        <input type="text" id="inputBox" name="remote_address" placeholder="Enter address or API key...">
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Submit Button -->
        <button type="button" class="btn-submit" id="submitBtn" onclick="handleSubmit()">
            <i class="fas fa-paper-plane"></i>
            Apply Configuration
        </button>
    </form>
</div>

<script>
// Store PHP data
var pageNamesData = <?php echo json_encode(!empty($pageNames) ? $pageNames : []); ?>;
var paramsData = <?php echo json_encode(!empty($params) ? $params : []); ?>;
var defaultsData = <?php echo json_encode(!empty($defaults) ? $defaults : []); ?>;
var nodeIdsData = <?php echo json_encode(!empty($nodeIds) ? $nodeIds : []); ?>;
var pageNo = "<?php echo $pageNo; ?>";

var selectedMode = "";
var previousModel = "";
var choicesInstance = null;

function onModelChange() {
    var modelSelect = document.getElementById("modelSelect");
    var selectedModel = modelSelect.value;
    var modeSelect = document.getElementById("ModeSelect");
    var mode = modeSelect ? modeSelect.value : null;
    
    updateNodeDropdown();
    
    if (selectedModel && mode) {
        if (mode === "Auto test") {
            updatePageDropdown();
        } else if (mode === "Configuration") {
            loadCommands();
        }
    }
}

function onModeChange() {
    var modeSelect = document.getElementById("ModeSelect");
    if (!modeSelect) return;
    var selectedMode = modeSelect.value;
    
    var autoTestContent = document.getElementById("autoTestContent");
    var configContent = document.getElementById("configContent");
    var autoTestPanel = document.getElementById("autoTestPanel");
    
    if (autoTestContent) autoTestContent.classList.add("hidden");
    if (configContent) configContent.classList.add("hidden");
    if (autoTestPanel) autoTestPanel.classList.add("hidden");
    
    if (selectedMode === "Auto test") {
        if (autoTestContent) autoTestContent.classList.remove("hidden");
        var modelSelect = document.getElementById("modelSelect");
        if (modelSelect && modelSelect.value) {
            updatePageDropdown();
        }
    } else if (selectedMode === "Configuration") {
        if (configContent) configContent.classList.remove("hidden");
        var modelSelect = document.getElementById("modelSelect");
        if (modelSelect && modelSelect.value) {
            loadCommands();
        }
    }
}

function updatePageDropdown() {
    var modelSelect = document.getElementById("modelSelect");
    var pageSelect = document.getElementById("pageSelect");
    var selectedModel = modelSelect.value;
    
    if (!pageSelect) return;
    
    if (selectedModel !== previousModel) {
        pageSelect.innerHTML = "<option value=''>Select Page</option>";
        var addedPages = [];
        
        if (pageNamesData && pageNamesData.length > 0) {
            pageNamesData.forEach(function(page) {
                if (page.model_name === selectedModel) {
                    var pageName = page.page_names;
                    if (pageName && pageName.indexOf(pageNo) !== -1) {
                        if (addedPages.indexOf(pageName) === -1) {
                            addedPages.push(pageName);
                            var option = document.createElement("option");
                            option.value = pageName;
                            option.text = pageName;
                            pageSelect.appendChild(option);
                        }
                    }
                }
            });
        }
        
        previousModel = selectedModel;
    }
    
    if (pageSelect.value) {
        loadParametersAndConfigs();
    }
}

function loadParametersAndConfigs() {
    var selectedModel = document.getElementById("modelSelect").value;
    var selectedPage = document.getElementById("pageSelect").value;
    
    if (!selectedModel || !selectedPage) return;
    
    var autoTestPanel = document.getElementById("autoTestPanel");
    if (autoTestPanel) autoTestPanel.classList.remove("hidden");
    
    var paramContainer = document.getElementById("paramSelect");
    if (paramContainer) {
        var paramList = paramsData.filter(function(param) {
            return param.model_name === selectedModel && param.page_names === selectedPage;
        });
        
        if (paramList.length > 0) {
            var html = '';
            paramList.forEach(function(param) {
                html += '<div class="param-item">';
                html += '<i class="fas fa-cog"></i>';
                html += '<span>' + escapeHtml(param.parameter_name) + '</span>';
                html += '</div>';
            });
            paramContainer.innerHTML = html;
        } else {
            paramContainer.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>No parameters available</p></div>';
        }
    }
    
    var configContainer = document.getElementById("configureSelect");
    if (configContainer) {
        var defaultValues = defaultsData.filter(function(def) {
            return def.model_name === selectedModel && def.page_names === selectedPage;
        });
        
        var defaultValuesMap = {
            "Resistance": "2.2",
            "Imin": "2.5",
            "Imax": "3.5",
            "Frequence": "16000",
            "PolePairs": "8",
            "Open_Loop_Current": "1.5",
            "PowerLimit": "44",
            "TorqueLimit": "7000",
            "Speed1": "4",
            "Speed2": "7",
            "Speed3": "11",
            "Speed4": "17",
            "Speed5": "23",
            "Speed6": "26",
            "Speed7": "26"
        };
        
        if (defaultValues.length > 0) {
            var html = '';
            defaultValues.forEach(function(def) {
                if (def.cmd_defaultvalue) {
                    var valuesArray = def.cmd_defaultvalue.split(',').filter(v => v && v.trim());
                    
                    valuesArray.forEach(function(keyName) {
                        keyName = keyName.trim();
                        if (keyName) {
                            var presetVal = defaultValuesMap[keyName] || "";
                            html += `
                                <div class="config-item">
                                    <label>${escapeHtml(keyName)}</label>
                                    <input type="text" name="config_${escapeHtml(keyName)}" 
                                           value="${escapeHtml(presetVal)}" 
                                           placeholder="Enter value">
                                </div>
                            `;
                        }
                    });
                }
            });
            
            configContainer.innerHTML = html || '<div class="empty-state"><i class="fas fa-edit"></i><p>No configuration fields</p></div>';
        } else {
            configContainer.innerHTML = '<div class="empty-state"><i class="fas fa-info-circle"></i><p>Select model and page to configure</p></div>';
        }
    }
}

function loadCommands() {
    var commandContainer = document.getElementById("commandList");
    if (!commandContainer) return;
    
    // Single command only - 55AA0006000E0502000A01FF0000FFFFFF640A640A
    var singleCommand = {
        number: 1,
        name: "Device Configuration Command",
        hex: "55AA0006000E0502000A01FF0000FFFFFF640A640A"
    };
    
    var html = '';
    html += `
        <div class="commands-header">
            <span style="font-weight: 600; color: var(--dark);">
                <i class="fas fa-list"></i> Configuration Command
            </span>
            <span class="commands-count">
                <i class="fas fa-hashtag"></i> 1 Command
            </span>
        </div>
    `;
    
    html += `
        <div class="command-item">
            <div class="command-info">
                <div class="command-name">
                    <span class="command-number">#${singleCommand.number}</span>
                    <i class="fas fa-bolt"></i>
                    ${escapeHtml(singleCommand.name)}
                    <span class="badge badge-config">COMMAND</span>
                </div>
                <div class="command-hex">
                    <i class="fas fa-code"></i> ${escapeHtml(singleCommand.hex)}
                </div>
            </div>
            <div class="command-actions">
                <input type="hidden" name="command_${singleCommand.number}" value="${escapeHtml(singleCommand.hex)}">
                <button type="button" class="btn-copy-command" onclick="copyCommand('${escapeHtml(singleCommand.hex)}', this)">
                    <i class="fas fa-copy"></i> Copy
                </button>
            </div>
        </div>
    `;
    
    commandContainer.innerHTML = html;
}

function copyCommand(hex, buttonElement) {
    navigator.clipboard.writeText(hex).then(function() {
        if (buttonElement) {
            var originalText = buttonElement.innerHTML;
            buttonElement.innerHTML = '<i class="fas fa-check"></i> Copied!';
            buttonElement.style.background = '#10b981';
            setTimeout(function() {
                buttonElement.innerHTML = originalText;
                buttonElement.style.background = '';
            }, 2000);
        }
    }).catch(function() {
        alert('Failed to copy command');
    });
}

function updateNodeDropdown() {
    var modelSelect = document.getElementById("modelSelect");
    var selectedModel = modelSelect.value;
    
    if (choicesInstance) {
        choicesInstance.clearChoices();
        
        if (nodeIdsData && nodeIdsData.length > 0) {
            var choices = [];
            nodeIdsData.forEach(function(node) {
                if (node.model_number === selectedModel) {
                    choices.push({
                        value: node.node_id,
                        label: node.node_id
                    });
                }
            });
            
            if (choices.length > 0) {
                choicesInstance.setChoices(choices, 'value', 'label', false);
            }
        }
    }
}

// Handle form submission
function handleSubmit() {
    var modelSelect = document.getElementById("modelSelect");
    if (!modelSelect.value) {
        alert("Please select a model");
        return false;
    }
    
    var form = document.getElementById("configForm");
    var modeSelect = document.getElementById("ModeSelect");
    var mode = modeSelect ? modeSelect.value : null;
    
    // Collect config values for Auto Test mode
    if (mode === "Auto test") {
        var pageSelect = document.getElementById("pageSelect");
        if (!pageSelect.value) {
            alert("Please select a page name");
            return false;
        }
        
        var configItems = document.querySelectorAll('#configureSelect .config-item');
        var configKeys = [];
        var configValues = [];
        
        configItems.forEach(function(item) {
            var label = item.querySelector('label');
            var input = item.querySelector('input');
            if (label && input && input.value.trim()) {
                var key = label.textContent.trim().replace(/:$/, "").trim();
                configKeys.push(key);
                configValues.push(input.value.trim());
            }
        });
        
        // Add config keys and values as hidden inputs
        configKeys.forEach(function(key, index) {
            var keyInput = document.createElement('input');
            keyInput.type = 'hidden';
            keyInput.name = 'config_keys[]';
            keyInput.value = key;
            form.appendChild(keyInput);
            
            var valueInput = document.createElement('input');
            valueInput.type = 'hidden';
            valueInput.name = 'config_values[]';
            valueInput.value = configValues[index];
            form.appendChild(valueInput);
        });
    }
    
    // Add the single command to form
    var commandInput = document.createElement('input');
    commandInput.type = 'hidden';
    commandInput.name = 'command_hex';
    commandInput.value = '55AA0006000E0502000A01FF0000FFFFFF640A640A';
    form.appendChild(commandInput);
    
    // Collect node IDs
    var nodeSelect = document.getElementById('nodeSelect');
    var selectedNodes = [];
    if (nodeSelect && choicesInstance) {
        var choices = choicesInstance.getValue(true);
        if (choices && choices.length > 0) {
            selectedNodes = choices;
        }
    }
    
    // Add node_ids to form
    if (selectedNodes.length > 0) {
        var nodeInput = document.createElement('input');
        nodeInput.type = 'hidden';
        nodeInput.name = 'node_ids';
        nodeInput.value = selectedNodes.join(',');
        form.appendChild(nodeInput);
    }
    
    // Get router ID
    var routerSelect = document.getElementById('routerSelect');
    if (routerSelect && routerSelect.value) {
        var routerInput = document.createElement('input');
        routerInput.type = 'hidden';
        routerInput.name = 'router_id';
        routerInput.value = routerSelect.value;
        form.appendChild(routerInput);
    }
    
    // Get status
    var statusSelect = document.getElementById('statusSelect');
    if (statusSelect && statusSelect.value && statusSelect.value !== 'None') {
        var statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = statusSelect.value;
        form.appendChild(statusInput);
    }
    
    // Show loading overlay
    document.getElementById('loadingOverlay').classList.add('active');
    
    // Submit the form
    form.submit();
    
    // Open index.html for monitoring
    setTimeout(function() {
        window.open('https://efactor.tech/v8/index.html', '_blank');
        document.getElementById('loadingOverlay').classList.remove('active');
    }, 3000);
    
    return false;
}

function handleSelectionChange() {
    var selectedValue = document.getElementById('remoteaddress')?.value;
    var qrImage = document.getElementById('qrImage');
    var inputBox = document.getElementById('inputBox');
    
    if (qrImage) qrImage.style.display = 'none';
    if (inputBox) inputBox.style.display = 'none';
    
    if (selectedValue === 'qr' && qrImage) qrImage.style.display = 'block';
    if ((selectedValue === 'manual' || selectedValue === 'deviceApi') && inputBox) inputBox.style.display = 'block';
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    var nodeSelect = document.getElementById('nodeSelect');
    if (nodeSelect) {
        choicesInstance = new Choices(nodeSelect, {
            removeItemButton: true,
            searchEnabled: true,
            placeholder: true,
            placeholderValue: 'Search and select node IDs...',
            itemSelectText: '',
            noResultsText: 'No nodes found',
            noChoicesText: 'No nodes available'
        });
    }
    
    if (pageNo !== "Pairing") {
        onModeChange();
    }
    
    var modelSelect = document.getElementById("modelSelect");
    if (modelSelect) {
        modelSelect.addEventListener('change', function() {
            updateNodeDropdown();
            var mode = document.getElementById("ModeSelect");
            if (mode && mode.value === "Configuration") {
                loadCommands();
            }
        });
    }
});

if (document.getElementById('remoteaddress')) {
    handleSelectionChange();
}
</script>

</body>
</html>
