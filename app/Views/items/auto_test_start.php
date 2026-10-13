<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Auto Test Runner | Parameter Selector</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: #f0f2f8;
            height: 100vh;
            overflow: hidden;
            color: #1e293b;
        }

        /* MAIN LAYOUT - SIDEBAR ONLY FOCUS */
        .test-runner {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* SIDEBAR - FULLY DEDICATED TO PARAMETERS */
        .test-sidebar {
            width: 380px;
            background: linear-gradient(145deg, #0f172a 0%, #111827 100%);
            color: #e2e8f0;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12);
            z-index: 20;
            overflow-y: auto;
            border-right: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-header {
            padding: 28px 24px 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            background: linear-gradient(135deg, #c084fc, #60a5fa);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.3px;
        }

        .sidebar-header h1 i {
            background: none;
            -webkit-background-clip: unset;
            background-clip: unset;
            color: #818cf8;
            font-size: 28px;
        }

        .sidebar-header p {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-top: 8px;
        }

        /* model badge */
        .model-badge {
            background: rgba(96, 165, 250, 0.15);
            border-radius: 40px;
            padding: 12px 18px;
            margin: 20px 20px 16px 20px;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(96, 165, 250, 0.3);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .model-badge i {
            font-size: 22px;
            color: #60a5fa;
        }

        .model-badge .model-text {
            font-weight: 600;
            font-size: 1rem;
            color: #f1f5f9;
        }

        .model-badge .model-value {
            font-family: monospace;
            background: #0f172a;
            padding: 4px 12px;
            border-radius: 24px;
            font-size: 0.85rem;
            color: #a5f3fc;
        }

        /* PARAMETER SECTION */
        .param-section-title {
            padding: 16px 24px 8px 24px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            color: #9ca3af;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .parameters-container {
            flex: 1;
            overflow-y: auto;
            padding: 4px 16px 20px 16px;
        }

        .parameter-card-side {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 18px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
            border: 1px solid rgba(255,255,255,0.06);
        }

        .parameter-card-side:hover {
            background: rgba(255, 255, 255, 0.09);
            border-color: rgba(96, 165, 250, 0.4);
        }

        .param-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            gap: 12px;
        }

        .param-name {
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #e2e8f0;
        }

        .param-name i {
            width: 24px;
            color: #818cf8;
            font-size: 1rem;
        }

        .select-badge {
            background: #1e293b;
            border-radius: 40px;
            padding: 6px 16px;
            font-size: 0.75rem;
            font-weight: 500;
            color: #cbd5e1;
            transition: all 0.2s;
        }

        .btn-select-param {
            background: rgba(79, 70, 229, 0.2);
            border: 1px solid #4f46e5;
            color: #a5b4fc;
            border-radius: 32px;
            padding: 6px 14px;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-select-param:hover {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
            transform: scale(0.97);
        }

        .instance-tag {
            background: #f59e0b20;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.7rem;
            color: #fcd34d;
            font-family: monospace;
        }

        /* SELECTED COUNTER */
        .selection-footer {
            margin: 20px 20px 24px 20px;
            background: #1e293b;
            border-radius: 24px;
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #334155;
        }

        .selected-counter {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #0f172a;
            padding: 6px 16px;
            border-radius: 40px;
        }

        .selected-counter i {
            color: #10b981;
        }

        .clear-selection-btn {
            background: none;
            border: none;
            color: #f87171;
            font-size: 0.8rem;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 30px;
            transition: 0.2s;
        }

        .clear-selection-btn:hover {
            background: #450a0a;
            color: #ff9e9e;
        }

        /* RIGHT MAIN AREA - MINIMALIST BUTTON CENTER */
        .start-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at 20% 30%, #f8fafc, #f1f5f9);
            position: relative;
            overflow-y: auto;
            padding: 32px;
        }

        .hero-start {
            text-align: center;
            max-width: 560px;
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(10px);
            border-radius: 64px;
            padding: 48px 40px;
            box-shadow: 0 20px 35px -12px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.8);
        }

        .start-icon {
            font-size: 72px;
            color: #4f46e5;
            background: #e0e7ff;
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 60px;
            margin: 0 auto 24px;
            box-shadow: 0 10px 20px -5px rgba(79,70,229,0.3);
        }

        .start-btn-main {
            background: linear-gradient(105deg, #10b981 0%, #059669 100%);
            border: none;
            padding: 18px 48px;
            font-size: 1.5rem;
            font-weight: 700;
            border-radius: 60px;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 14px;
            cursor: pointer;
            transition: 0.25s;
            box-shadow: 0 12px 28px -8px rgba(16,185,129,0.5);
            margin-top: 24px;
        }

        .start-btn-main:hover {
            transform: translateY(-3px);
            background: linear-gradient(105deg, #059669, #047857);
            box-shadow: 0 20px 30px -10px rgba(16,185,129,0.6);
        }

        .start-btn-main:active {
            transform: translateY(2px);
        }

        .selected-params-badge {
            margin-top: 32px;
            background: #eef2ff;
            padding: 12px 24px;
            border-radius: 80px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #1e293b;
            display: inline-block;
        }

        .empty-state-message {
            color: #64748b;
            margin-top: 24px;
            font-size: 0.9rem;
        }

        /* scroll custom */
        .test-sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .test-sidebar::-webkit-scrollbar-track {
            background: #0f172a;
        }
        .test-sidebar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 10px;
        }

        @media (max-width: 720px) {
            .test-sidebar {
                width: 300px;
            }
            .hero-start {
                padding: 32px 24px;
            }
            .start-btn-main {
                padding: 14px 32px;
                font-size: 1.2rem;
            }
        }

        /* notification */
        .toast-msg {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: #1e293b;
            color: white;
            padding: 12px 24px;
            border-radius: 40px;
            font-weight: 500;
            z-index: 200;
            backdrop-filter: blur(8px);
            font-size: 0.85rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            pointer-events: none;
            transition: 0.2s;
        }
        
        .param-stats {
            padding: 0 16px 12px 16px;
            font-size: 0.7rem;
            color: #94a3b8;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="test-runner">
    <!-- LEFT SIDEBAR : ONLY PARAMETERS + SELECTION -->
    <div class="test-sidebar">
        <div class="sidebar-header">
            <h1><i class="fas fa-play-circle"></i> Auto Test Start</h1>
            <p>Select parameters & launch validation</p>
        </div>

        <!-- Model display (from backend) -->
        <div class="model-badge">
            <i class="fas fa-microchip"></i>
            <span class="model-text">Device Model:</span>
            <span class="model-value" id="currentModelLabel"><?php echo htmlspecialchars($modelno ?? 'Not Selected'); ?></span>
        </div>

        <div class="param-section-title">
            <i class="fas fa-sliders-h"></i> AVAILABLE TEST PARAMETERS
        </div>

        <div class="parameters-container" id="parametersListContainer">
            <!-- dynamic parameter cards will be injected -->
            <div style="text-align:center; padding: 20px; color:#94a3b8;"><i class="fas fa-spinner fa-pulse"></i> Loading parameters...</div>
        </div>

        <div class="selection-footer">
            <div class="selected-counter">
                <i class="fas fa-check-circle"></i>
                <span id="totalSelectedCount">0</span> <span style="margin-left: 4px;">selected</span>
            </div>
            <button class="clear-selection-btn" id="clearAllSelectionsBtn">
                <i class="fas fa-trash-alt"></i> Clear all
            </button>
        </div>
    </div>

    <!-- RIGHT AREA: START BUTTON FOCUS -->
    <div class="start-area">
        <div class="hero-start">
            <div class="start-icon">
                <i class="fas fa-flask"></i>
            </div>
            <h2 style="font-size: 2rem; margin-bottom: 12px;">Ready to Test</h2>
            <p style="color: #475569; margin-bottom: 20px;">Choose parameters from the sidebar and hit start to execute automated test suite.</p>
            <div id="selectedPreview" class="selected-params-badge">
                <i class="fas fa-cog"></i> No parameters selected
            </div>
            <button class="start-btn-main" id="mainStartTestBtn">
                <i class="fas fa-play"></i> START TEST
            </button>
            <div class="empty-state-message" id="helperNote">
                <i class="fas fa-arrow-left"></i> Click on any parameter card to add
            </div>
        </div>
    </div>
</div>

<div id="globalToast" class="toast-msg" style="display: none;"></div>

<script>
    // --------------------------------------------------------------
    // BACKEND DATA: this will print real parameter names from PHP
    // If $parameters exists and has data, it will display exactly those.
    // --------------------------------------------------------------
    const serverParameters = <?php 
        // If $parameters is defined from PHP (like your original form) use it
        // otherwise fallback to demo parameters so the UI never stays empty.
        if(isset($parameters) && is_array($parameters) && count($parameters) > 0){
            echo json_encode($parameters, JSON_PRETTY_PRINT);
        } else {
            // Provide realistic parameter names for standalone / demo 
            // This ensures the sidebar shows actual parameter names.
            echo json_encode([
                "Get Status" => [
                    "cmd" => [["cmd_default" => "STATUS_READ", "cmd_startbyteposition" => "0x02", "cmd_min" => "0", "cmd_max" => "100"]],
                    "resp" => [["resp_default" => "STATUS_RESP", "resp_startbyteposition" => "0x10"]],
                    "error" => [["error_default" => "ERR_CODE"]]
                ],
                "Speed Control" => [
                    "cmd" => [["cmd_default" => "SPEED_SET", "cmd_startbyteposition" => "0x20", "cmd_min" => "0", "cmd_max" => "255"]],
                    "resp" => [["resp_default" => "SPEED_ACK"]],
                    "error" => []
                ],
                "Temperature Sensor" => [
                    "cmd" => [["cmd_default" => "TEMP_READ", "cmd_startbyteposition" => "0x30", "cmd_min" => "-40", "cmd_max" => "125"]],
                    "resp" => [["resp_default" => "TEMP_VAL"]],
                    "error" => [["error_default" => "TEMP_ERR"]]
                ],
                "Power State" => [
                    "cmd" => [["cmd_default" => "POWER_TOGGLE", "cmd_startbyteposition" => "0x40"]],
                    "resp" => [["resp_default" => "POWER_STATE"]],
                    "error" => []
                ],
                "Battery Level" => [
                    "cmd" => [["cmd_default" => "BATTERY_READ", "cmd_min" => "0", "cmd_max" => "100"]],
                    "resp" => [["resp_default" => "BATTERY_LEVEL"]],
                    "error" => []
                ],
                "Firmware Version" => [
                    "cmd" => [["cmd_default" => "FW_VERSION"]],
                    "resp" => [["resp_default" => "FW_RESPONSE"]],
                    "error" => []
                ]
            ]);
        }
    ?>;
    
    // model & IDs from PHP (safe fallback)
    const userId = <?php echo json_encode($user_id ?? 'demo_user'); ?>;
    const itemId = <?php echo json_encode($id ?? '0'); ?>;
    const modelNo = <?php echo json_encode($modelno ?? 'Generic Model'); ?>;
    
    // ---------- STATE MANAGEMENT ----------
    // Selected parameters list (each parameter can be selected once)
    let selectedParameters = [];   // array of { name: string, instanceId: string, selectedAt: timestamp }
    
    // DOM elements
    const parametersContainer = document.getElementById('parametersListContainer');
    const totalSelectedSpan = document.getElementById('totalSelectedCount');
    const selectedPreviewDiv = document.getElementById('selectedPreview');
    const clearAllBtn = document.getElementById('clearAllSelectionsBtn');
    const startTestBtn = document.getElementById('mainStartTestBtn');
    const currentModelSpan = document.getElementById('currentModelLabel');
    
    // set model text
    if(currentModelSpan) currentModelSpan.innerText = modelNo;
    
    // Helper: show toast
    function showMessage(message, isError = false) {
        const toast = document.getElementById('globalToast');
        toast.innerText = message;
        toast.style.backgroundColor = isError ? '#b91c1c' : '#0f172a';
        toast.style.display = 'block';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 2800);
    }
    
    // Update UI counters & preview text (only parameter names)
    function updateSelectionUI() {
        const count = selectedParameters.length;
        totalSelectedSpan.innerText = count;
        if(count === 0) {
            selectedPreviewDiv.innerHTML = `<i class="fas fa-cog"></i> No parameters selected`;
            document.getElementById('helperNote').innerHTML = `<i class="fas fa-arrow-left"></i> Click on any parameter card to add`;
        } else {
            const names = selectedParameters.map(p => p.name).join(', ');
            selectedPreviewDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${count} selected: ${names.length > 45 ? names.substring(0,45)+'…' : names}`;
            document.getElementById('helperNote').innerHTML = `<i class="fas fa-rocket"></i> Ready to start test suite`;
        }
    }
    
    // Render parameters list from serverParameters (print all parameter names exactly)
    function renderParametersList() {
        if(!parametersContainer) return;
        parametersContainer.innerHTML = '';
        
        const paramNames = Object.keys(serverParameters);
        if(paramNames.length === 0) {
            parametersContainer.innerHTML = '<div style="padding: 20px; text-align:center; color:#94a3b8;"><i class="fas fa-exclamation-triangle"></i> No parameters available from backend</div>';
            return;
        }
        
        // Loop through each parameter and create card
        paramNames.forEach(paramName => {
            const isSelected = selectedParameters.some(p => p.name === paramName);
            const paramData = serverParameters[paramName];
            
            // get count of commands/responses/errors for extra info
            const cmdCount = paramData.cmd?.length || 0;
            const respCount = paramData.resp?.length || 0;
            const errCount = paramData.error?.length || 0;
            
            const card = document.createElement('div');
            card.className = 'parameter-card-side';
            card.dataset.paramName = paramName;
            
            const rowDiv = document.createElement('div');
            rowDiv.className = 'param-row';
            
            // left side with parameter name (main focus)
            const nameDiv = document.createElement('div');
            nameDiv.className = 'param-name';
            nameDiv.innerHTML = `<i class="fas fa-microphone-alt"></i> ${paramName}`;
            
            // small tags for type info
            const tagsDiv = document.createElement('div');
            tagsDiv.style.display = 'flex';
            tagsDiv.style.gap = '6px';
            tagsDiv.style.alignItems = 'center';
            if(cmdCount > 0) tagsDiv.innerHTML += `<span class="instance-tag"><i class="fas fa-terminal"></i> cmd</span>`;
            if(respCount > 0) tagsDiv.innerHTML += `<span class="instance-tag"><i class="fas fa-reply-all"></i> resp</span>`;
            if(errCount > 0) tagsDiv.innerHTML += `<span class="instance-tag"><i class="fas fa-exclamation-triangle"></i> err</span>`;
            
            // select / deselect button
            const actionBtn = document.createElement('button');
            actionBtn.className = 'btn-select-param';
            if(isSelected) {
                actionBtn.innerHTML = '<i class="fas fa-minus-circle"></i> Remove';
                actionBtn.style.background = '#dc262620';
                actionBtn.style.borderColor = '#ef4444';
                actionBtn.style.color = '#fca5a5';
            } else {
                actionBtn.innerHTML = '<i class="fas fa-plus-circle"></i> Select';
                actionBtn.style.background = '#4f46e520';
            }
            
            actionBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleParameterSelection(paramName, actionBtn, card);
            });
            
            // also card click for easier selection
            card.addEventListener('click', (e) => {
                if(e.target !== actionBtn && !actionBtn.contains(e.target)) {
                    toggleParameterSelection(paramName, actionBtn, card);
                }
            });
            
            rowDiv.appendChild(nameDiv);
            rowDiv.appendChild(tagsDiv);
            rowDiv.appendChild(actionBtn);
            card.appendChild(rowDiv);
            
            // show min-max hint if available (just informative)
            let hintText = '';
            if(paramData.cmd && paramData.cmd[0] && (paramData.cmd[0].cmd_min || paramData.cmd[0].cmd_max)) {
                const minVal = paramData.cmd[0].cmd_min ?? '?';
                const maxVal = paramData.cmd[0].cmd_max ?? '?';
                hintText = `<i class="fas fa-chart-line"></i> range: ${minVal} ~ ${maxVal}`;
            } else if(paramData.resp && paramData.resp[0] && (paramData.resp[0].resp_min || paramData.resp[0].resp_max)) {
                const minVal = paramData.resp[0].resp_min ?? '?';
                const maxVal = paramData.resp[0].resp_max ?? '?';
                hintText = `<i class="fas fa-chart-line"></i> resp range: ${minVal} ~ ${maxVal}`;
            }
            
            if(hintText) {
                const hintDiv = document.createElement('div');
                hintDiv.className = 'param-stats';
                hintDiv.innerHTML = hintText;
                card.appendChild(hintDiv);
            }
            
            parametersContainer.appendChild(card);
        });
        
        // If after rendering we have selected items, highlight them
        if(selectedParameters.length > 0) {
            highlightSelectedCards();
        }
    }
    
    // highlight cards that are selected
    function highlightSelectedCards() {
        document.querySelectorAll('.parameter-card-side').forEach(card => {
            const paramName = card.dataset.paramName;
            const isSelected = selectedParameters.some(p => p.name === paramName);
            if(isSelected) {
                card.style.background = 'rgba(22, 163, 74, 0.15)';
                card.style.borderLeft = '3px solid #10b981';
            } else {
                card.style.background = 'rgba(255, 255, 255, 0.05)';
                card.style.borderLeft = 'none';
            }
        });
    }
    
    // Toggle selection logic
    function toggleParameterSelection(paramName, btnElement, cardElement) {
        const existingIndex = selectedParameters.findIndex(p => p.name === paramName);
        if(existingIndex !== -1) {
            // remove selection
            selectedParameters.splice(existingIndex, 1);
            if(btnElement) {
                btnElement.innerHTML = '<i class="fas fa-plus-circle"></i> Select';
                btnElement.style.background = '#4f46e520';
                btnElement.style.borderColor = '#4f46e5';
                btnElement.style.color = '#a5b4fc';
            }
            if(cardElement) {
                cardElement.style.background = 'rgba(255, 255, 255, 0.05)';
                cardElement.style.borderLeft = 'none';
            }
            showMessage(`❌ Removed "${paramName}" from test`, false);
        } else {
            // add new selection (only once per parameter)
            const newInstance = {
                name: paramName,
                instanceId: `${paramName}-${Date.now()}-${Math.random().toString(36).substr(2, 6)}`,
                selectedAt: Date.now()
            };
            selectedParameters.push(newInstance);
            if(btnElement) {
                btnElement.innerHTML = '<i class="fas fa-minus-circle"></i> Remove';
                btnElement.style.background = '#dc262620';
                btnElement.style.borderColor = '#ef4444';
                btnElement.style.color = '#fca5a5';
            }
            if(cardElement) {
                cardElement.style.background = 'rgba(22, 163, 74, 0.15)';
                cardElement.style.borderLeft = '3px solid #10b981';
            }
            showMessage(`✅ "${paramName}" added to test suite`, false);
        }
        updateSelectionUI();
    }
    
    // Clear all selections
    function clearAllSelections() {
        if(selectedParameters.length === 0) {
            showMessage('No parameters to clear', false);
            return;
        }
        selectedParameters = [];
        // re-render the whole list to reset all buttons and highlights
        renderParametersList();
        updateSelectionUI();
        showMessage('All test parameters cleared', false);
    }
    
    // ---------- START TEST LOGIC ----------
    function startAutoTest() {
        if(selectedParameters.length === 0) {
            showMessage('⚠️ Please select at least one parameter to start test', true);
            // shake start button
            const btn = document.getElementById('mainStartTestBtn');
            if(btn) {
                btn.style.transform = 'translateX(4px)';
                setTimeout(() => { btn.style.transform = ''; }, 200);
            }
            return;
        }
        
        // Prepare payload: exactly the selected parameters with full structure
        const testPayload = {
            user_id: userId,
            id: itemId,
            modelno: modelNo,
            timestamp: new Date().toISOString(),
            selected_parameters: selectedParameters.map(p => p.name),
            test_parameters_detail: {}
        };
        
        // For each selected parameter, attach its definition from serverParameters
        selectedParameters.forEach(param => {
            const paramDef = serverParameters[param.name];
            if(paramDef) {
                testPayload.test_parameters_detail[param.name] = {
                    cmd: paramDef.cmd || [],
                    resp: paramDef.resp || [],
                    error: paramDef.error || []
                };
            } else {
                testPayload.test_parameters_detail[param.name] = { cmd: [], resp: [], error: [] };
            }
        });
        
        // Show loading state on start button
        const startBtn = document.getElementById('mainStartTestBtn');
        const originalBtnHtml = startBtn.innerHTML;
        startBtn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Initializing...';
        startBtn.disabled = true;
        
        // Simulate API call (replace with actual backend endpoint)
        setTimeout(() => {
            console.log("Test Payload:", testPayload);
            showMessage(`🚀 Test launched with ${selectedParameters.length} parameter(s)! Check console.`, false);
            
            // Optional: open summary window / report (like original functionality)
            const printWindow = window.open('', '_blank');
            if(printWindow) {
                printWindow.document.write(`
                    <html>
                    <head>
                        <title>Auto Test Report | ${modelNo}</title>
                        <style>
                            body { font-family: 'Segoe UI', monospace; padding: 30px; background: #0f172a; color: #e2e8f0; }
                            pre { background: #1e293b; padding: 16px; border-radius: 12px; overflow-x: auto; color: #a5f3fc; }
                            h1 { color: #60a5fa; }
                            .badge { background: #10b981; display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; margin-left: 12px; }
                        </style>
                    </head>
                    <body>
                        <h1>⚡ Auto Test Execution Report <span class="badge">completed</span></h1>
                        <p><strong>Device Model:</strong> ${modelNo}</p>
                        <p><strong>Selected Parameters (${selectedParameters.length}):</strong> ${selectedParameters.map(p=>p.name).join(', ')}</p>
                        <p><strong>Timestamp:</strong> ${new Date().toLocaleString()}</p>
                        <h3>Full Configuration Payload:</h3>
                        <pre>${JSON.stringify(testPayload, null, 2)}</pre>
                        <button onclick="window.print()" style="margin-top:20px; padding:8px 20px; background:#4f46e5; color:white; border:none; border-radius:30px; cursor:pointer;">🖨️ Print Report</button>
                        <button onclick="window.close()" style="margin-left:12px; padding:8px 20px; background:#334155; color:white; border:none; border-radius:30px; cursor:pointer;">Close</button>
                    </body>
                    </html>
                `);
                printWindow.document.close();
            } else {
                alert("Test started successfully. (Popup blocked? please allow popups)");
            }
            
            startBtn.innerHTML = originalBtnHtml;
            startBtn.disabled = false;
        }, 800);
    }
    
    // Event listeners
    if(clearAllBtn) clearAllBtn.addEventListener('click', clearAllSelections);
    if(startTestBtn) startTestBtn.addEventListener('click', startAutoTest);
    
    // initial render of parameters - THIS WILL PRINT ALL PARAMETER NAMES
    renderParametersList();
    updateSelectionUI();
    
    // expose refresh if needed externally
    window.refreshParameterList = function() {
        renderParametersList();
        updateSelectionUI();
        showMessage("Parameter list refreshed", false);
    };
    
    console.log("✅ Auto Test Start Page Loaded | Parameters found:", Object.keys(serverParameters).length);
</script>
</body>
</html>
