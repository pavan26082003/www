<?php
date_default_timezone_set('Asia/Kolkata');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Multi-Step Form (Product + Command)</title>
    <style>
        /* Base Styles */
        body {
            background: linear-gradient(135deg, #e0f7fa, #ffffff);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .form-step { 
           display:none; 
 }

        
        .form-step.active { 
            display: block; 
        }
        
                .btn {
    padding: 12px 24px;
    background-color: #0056b3;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
   margin:10px 5px 
    
}

     

        
        .btn:hover {
            background-color: #0056b3;
        }
        
        .btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        
        .container {
            padding: 40px;
            max-width: 1200px;
            margin: 50px auto;
            background: #ffffff;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border-radius: 16px;
        }
        
        h2 {
            text-align: center;
            color: #0056b3;
            font-size: 28px;
            margin-bottom: 30px;
        }
        
        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            font-size: 14px;
            transition: border-color 0.3s ease, background-color 0.3s ease;
            box-sizing: border-box;
        }
        
        input:focus, select:focus, textarea:focus {
            border-color: #007bff;
            background-color: #fff;
            outline: none;
        }
        
        input.error, select.error, textarea.error {
            border-color: #dc3545;
        }
        
        small {
            display: block;
            margin-top: 4px;
            color: #777;
        }
        
        small.error {
            color: #dc3545;
        }
        
        .required::after {
            content: " *";
            color: #dc3545;
        }

        /* Progress Indicator */
        .progress-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
            max-width: 1200px;
            margin: 0 auto 50px;
            padding: 0 40px;
        }
        
        .progress-bar::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e0e0e0;
            z-index: 1;
        }
        
        .progress-step {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }
        
        .progress-step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            color: #fff;
            font-weight: bold;
        }
        
        .progress-step.active .progress-step-circle {
            background-color: #007bff;
        }
        
        .progress-step.completed .progress-step-circle {
            background-color:#007bff ;
           
        }
        
        /* Table Styles */
        .table-container {
            margin-top: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table-header {
            background: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-responsive {
            overflow-x: auto !important;
            overflow-y: auto;
            max-width: 100%;
            width: 100%;
            display: block;
        }

        .dynamic-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            min-width: 2000px;
        }
        
        .dynamic-table th {
            background: #e9ecef;
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .dynamic-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
            text-align: center;
            vertical-align: middle;
        }
        
        .dynamic-table input,
        .dynamic-table select {
            width: 100%;
            padding: 6px 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 11px;
            background: white;
            min-height: 32px;
            box-sizing: border-box;
        }
        
        .dynamic-table input:focus,
        .dynamic-table select:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }
        
        /* Move buttons styles */
        .sno-column {
            width: 100px;
            background: #f8f9fa;
            font-weight: bold;
            text-align: center;
            position: sticky;
            left: 0;
            z-index: 5;
        }
        
        .move-column {
            width: 80px;
            background: #f8f9fa;
            text-align: center;
            position: sticky;
            left: 100px;
            z-index: 5;
        }
        
        .action-column {
            width: 120px;
            position: sticky;
            right: 0;
            background: #f8f9fa;
            z-index: 5;
        }
        
        .move-buttons {
            display: flex;
            flex-direction: column;
            gap: 2px;
            align-items: center;
        }
        
        .move-buttons-horizontal {
            display: flex;
            gap: 4px;
            justify-content: center;
            align-items: center;
        }
        
        .remove-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11px;
        }
        
        .remove-btn:hover {
            background: #c82333;
        }
        
        .add-row-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            margin: 15px 5px;
        }
        
        .add-row-btn:hover {
            background: #218838;
        }
        
        /* Navigation */
        .form-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
        
        .note-text {
            color: green;
            font-size: small;
            margin-top: 5px;
        }
        
        /* Compact form controls for table */
        .compact-input {
            padding: 4px 6px !important;
            font-size: 11px !important;
            min-height: 28px !important;
        }
        
        .compact-select {
            padding: 4px 6px !important;
            font-size: 11px !important;
            min-height: 28px !important;
        }
        
        /* Form Layout */
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .form-col {
            flex: 1;
        }
        
        /* Validation Styles */
        .validation-summary {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            display: none;
        }
        
        .validation-summary ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .validation-summary li {
            color: #721c24;
            margin-bottom: 5px;
        }
        
        /* Move instruction */
        .move-instruction {
            padding: 8px 15px;
            background: #e7f3ff;
            border-bottom: 1px solid #b3d9ff;
            font-size: 12px;
            color: #0066cc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .move-instruction-text {
            flex: 1;
        }
        
        .table-indicator {
            font-weight: bold;
            color: #0056b3;
        }

        /* Auto-calculated fields */
        .auto-calculated {
            background-color: #e8f5e8 !important;
            color: #155724;
            font-weight: bold;
        }

        .readonly-field {
            background-color: #f8f9fa !important;
            cursor: not-allowed;
        }

        /* Save button styling */
        .save-btn-container {
            text-align: center;
            margin-top: 50px; 
    padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .save-btn {
            background-color: #28a745;
            color: white;
            padding: 15px 40px;
         
            font-size: 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .save-btn:hover {
            background-color: #218838;
        }

        /* Step validation error */
        .step-error {
            color: #dc3545;
            font-size: 14px;
            margin-top: 10px;
            display: none;
        }

        .error-border {
            border: 2px solid red !important;
        }

        .step-error {
            color: red;
            display: none;
            margin-bottom: 10px;
        }

        /* --- NEW PINK HIGHLIGHT CSS --- */
        .pink-highlight {
            background-color: #ffc0cb !important;
        }
        .pink-highlight input {
            background-color: #ffc0cb !important;
            border: 1px solid #ff8095 !important;
        }

     .error {
    border: 2px solid #ff4d4d !important;
    background-color: #fff2f2 !important;
}

.error-message {
    background-color: #fce4e4;
    border: 1px solid #fcc;
    padding: 10px;
    margin: 10px 0;
    color: #cc0000;
    display: none;
    border-radius: 5px;
}

.auto-calculated {
    background-color: #e8f5e8 !important; /* Soft green */
    color: #155724;
    font-weight: bold;
    border: 1px solid #c3e6cb !important;
}
.field-desc-inline {
    display: flex;
    align-items: center;
    gap: 6px;
}

.bit-byte-select {
    width: 180px;
}

.arrow-btn {
    cursor: pointer;
}


.modal-actions {
    margin-top: 12px;
    text-align: right;
}
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    display: none;   /* IMPORTANT */
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.config-table {
    width: 250%;
    border-collapse: collapse;
    font-size: 12px;
}

.config-table th,
.config-table td {
    border: 1px solid #ccc;
    padding: 4px;
    text-align: center;
    white-space: nowrap;
}

.modal-box {
    max-width: 95%;
    overflow-x: auto;
background: #fff;

}
.ui-inout-select:disabled {
    background-color: #f2f2f2;
    cursor: not-allowed;
}
.btn-save {
    background-color: red;
    color: white;
    padding: 8px 20px;
    
   
    border: none;
    border-radius: 4px;
    cursor: pointer;
gap:5px;

 }
.modal-actions {
    display: flex;            /* Enables flexbox */
    justify-content: flex-start; /* Aligns buttons to the LEFT */
    gap: 5px;                /* Space between Save and Cancel buttons */
    padding: 15px;            /* Padding inside the modal footer */
    width: 100%;              /* Ensures it spans the whole modal width */
}
.current-frame-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid #007bff;
    background-color: #e7f1ff;
    color: #007bff;

    display: flex;
    align-items: center;
    justify-content: center;

    font-weight: bold;
    font-size: 16px;
    font-family: Arial, sans-serif;
}
.progress-circles {
    display: flex;
    gap: 8px;
}

.step-circle {
    width: 35px;
    height: 35px;
    min-width: 35px;
    border-radius: 50%;
    border: 2px solid #ccc;
    background-color: white;
    color: black;

    display: flex;
    justify-content: center;
    align-items: center;

    font-weight: bold;
    font-family: Arial, sans-serif;
    font-size: 16px;
    flex-shrink: 0;
}

/* 🔒 Pagination wrapper isolation */
.command-pagination-wrapper button {
    background-color: #ffffff !important;
    border: 1px solid #d1d5db !important;
    color: #374151 !important;
    box-shadow: none !important;
}

/* Hover (same subtle image effect) */
.command-pagination-wrapper button:hover {
    background-color: #f9fafb !important;
}

/* Arrow symbols bigger */
.command-pagination-wrapper .arrow {
    font-size: 22px;   /* bigger arrows */
    font-weight: 600;
    line-height: 1;
}

/* Pagination container */
.command-pagination-wrapper .progress-circles {
    display: flex;
    gap: 6px;
    align-items: center;
}

/* Square page boxes */
.command-pagination-wrapper .step-circle {
    width: 34px;
    height: 34px;
    border: 1px solid #d1d5db;
    background-color: #ffffff;
    color: #374151;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    cursor: pointer;
}

/* Active page (blue like image) */
.command-pagination-wrapper .step-circle.active {
    background-color: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
}
.command-progress-container {
    position: relative;
    /* optional: control overall spacing */
    margin-top: 20px;
}

.circles-scroll-wrapper {
    width: 140px;
    overflow-x: auto;
    padding: 10px;
    display: block;
    transform: translateX(150px); /* keeps boxes in same horizontal position */
}

.command-frame-title {
    font-size: 14px;
    font-weight: 600;
    margin-top: 6px;    /* space between boxes and title */
    margin-bottom: 4px;
    color: #333;

    /* Align with first box dynamically inside the container */
    display: inline-block;
     position: relative;  /* safely move right without affecting others */
    left: 150px; 
   white-space: nowrap;  
}


    .frame-tab {
        padding: 10px 30px;
        border: 2px solid #e0e0e0;
        background-color: #f9f9f9;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #666;
    }

    /* The Highlight Style */
    .frame-tab.active-tab {
        background-color: #007bff; /* Thick Blue */
        color: white;
        border-color: #0056b3;
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

//


    </style>
</head>
<body>

<!-- Progress Indicator -->
<div class="progress-bar">
    <div class="progress-step active" id="step-indicator-1">
        <div class="progress-step-circle">1</div>
        <span>Product Info</span>
    </div>
    <div class="progress-step" id="step-indicator-2">
        <div class="progress-step-circle">2</div>
        <span>Command Frame</span>
    </div>
    <div class="progress-step" id="step-indicator-3">
        <div class="progress-step-circle">3</div>
        <span>Response Frame</span>
    </div>
    <div class="progress-step" id="step-indicator-4">
        <div class="progress-step-circle">4</div>
        <span>Error Frame</span>
    </div>
</div>
<div id="modalContainer">
<div id="formWrapper">

<?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form")); ?>

<!-- Step 1: Product Information -->
<div id="step-1" class="form-step active">
    <div class="container">
        <h2>PRODUCT INFORMATION</h2>
        
        <div id="step1-error" class="step-error"></div>

        <div class="form-group">
            <label for="par_parametername" class="required">Parameter Names</label>
            <input type="text" name="parametername" id="par_parametername" 
                   placeholder="Enter parameter name" oninput="validateParamLive(); syncParameterNameToAll(this.value)" />
            <input type="hidden" id="default-param-list" value='<?= json_encode($paramNames ?? []); ?>'>
            <small id="duplicate-error" style="color:red; display:none;">
               ⚠️ Parameter already exists for this model!
            </small>
            <small id="char-error" style="color:red; display:none;">
                ❌ Special characters like ! @ # $ % ^ & * - + = / are not allowed (except _ and space)
            </small>
        </div>

        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <label for="productname">Product Name</label>
                    <input type="text" class="form-control" value="<?php echo $category; ?>" id="productname" name="productname" readonly>
                </div>
            </div>
            <div class="form-col">
                <div class="form-group">
                    <label for="brandname">Brand Name</label>
                    <input type="text" class="form-control" value="<?php echo $brand; ?>" id="brandname" name="brandname" readonly>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <label for="modelname">Model Name</label>
                    <input type="text" class="form-control" value="<?= esc($model_name) ?>" id="modelname" name="modelname" readonly>
                </div>
            </div>
            <div class="form-col">
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" />
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <label for="numberofpages" class="required">Page Number</label>
                    <input type="text" name="numberofpages" id="numberofpage" placeholder="e.g., 1" />
                </div>
            </div>
            <div class="form-col">
                <div class="form-group">
                    <label for="Sequence" class="required">Sequence No</label>
                    <input type="text" name="Sequence" id="Sequence" placeholder="e.g., 1" />
                </div>
            </div>
        </div>

         <div class="form-group">
    <label for="numberofpages" class="required">Number of Command Frames</label>
    <input type="number" name="numberofpages" id="numberofpages" placeholder="e.g., 1" min="1"  />
</div>
<div class="form-group">
    <label for="numberofpages" class="required">Number of Response Frames</label>
    <input type="number" name="numberofpages" id="page" placeholder="e.g., 1" min="1"  />
</div>
<div class="form-group">
    <label for="numberofpages" class="required">Number of Error Frames</label>
    <input type="number" name="numberofpages" id="pages" placeholder="e.g., 1" min="1"  />
</div>



        <div class="form-group">
            <label for="pagenames" class="required">Page Names</label>
            <input type="text" name="pagenames" id="pagenames" placeholder="e.g., Fan Page" onblur="validatePageName()" />
        </div>

        <div class="form-group">
            <label for="productdescription">Product Description</label>
            <textarea name="productdescription" id="productdescription" rows="4" 
                      placeholder="Describe the product (max 300 words)..." oninput="checkWordCount(this)"></textarea>
            <small>Max 300 words</small>
        </div>

        <div class="form-navigation">
            <div></div>
            <button class="btn" type="button" onclick="goToNextStep(1)">Next</button>
        </div>

        </div>
</div>

<!-- Step 2: Command Frame -->

<div id="step-2" class="form-step">
    <div class="container">
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        
        <h2 id="command-title-display">COMMAND FRAME (1)</h2>
        
        <div id="step2-error" class="step-error"></div>

        <div class="form-group">
            <label for="com_parametername">Parameter Name</label>
            <input type="text" name="cmd_parametername" id="com_parametername" readonly />
        </div>

        <div class="form-group">
            <label for="cmd_data_flow_direction" class="required">Data Flow Direction</label>
            <select name="cmd_data_flow_direction" id="cmd_data_flow_direction">
                <option value="">Select Direction</option>
                <option value="App/Server to Device">App/Server to Device</option>
                <option value="Device to App/Server">Device to App/Server</option>
            </select>
        </div>

        <div class="form-group">
            <label for="cmd_frametype" class="required">Frame Type</label>
            <select name="cmd_frametype" id="cmd_frametype">
                <option value="COMMAND">COMMAND</option>
            </select>
        </div>

        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <label for="cmd_framesizeinbytes" class="required">Frame size in bytes</label>
                    <input type="number" id="cmd_framesizeinbytes" name="cmd_framesizeinbytes" placeholder="Enter number" min="0" />
                </div>
            </div>
            <div class="form-col">
                <div class="form-group">
                    <label for="cmd_noofrows" class="required">No of rows</label>
                    <input type="number" id="cmd_noofrows" name="cmd_noofrows" readonly value="0" />
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="cmd_frame_trigger_options" class="required">Frame Trigger</label>
            <select name="cmd_frametrigger" id="cmd_frame_trigger_options">
                <option value="None">None</option>
                <option value="Text">Text</option>
                <option value="Toggle Switch">Toggle Switch</option>
                <option value="Notification">Notification</option>
                <option value="Console">Console</option>
                <option value="Enter Text">Enter Text</option>
                <option value="Slider">Slider</option>
                <option value="Brightness Slider">Brightness Slider</option>
                <option value="Airflow Slider">Airflow Slider</option>
                <option value="CCT Slider">CCT Slider</option>
                <option value="Saturation Slider">Saturation Slider</option>
                <option value="Hue Slider">Hue Slider</option>
                <option value="Hue Slider Custom">Hue Slider Custom</option>
                <option value="Hue Circle">Hue Circle</option>
                <option value="Push Button">Push Button (Big)</option>
                <option value="Drop Down">Drop Down</option>
                <option value="Trigger">Trigger</option>
                <option value="Display">Display</option>
                <option value="Button">Button (Big)</option>
                <option value="Auto Trigger With Timer">Auto Trigger With Timer</option>
                <option value="Toast Display">Toast Display</option>
            </select>
        </div>

        <div class="form-group">
            <label for="cmd_multiple_ui" class="required">Multiple UI</label>
            <select name="cmd_multipleui" id="cmd_multipleui">
                <option value="false">False</option>
                <option value="true">True</option>     
            </select>
        </div>

        <div class="form-group">
            <label for="cmd_trigger_ui" class="required">Trigger UI</label>
            <select name="cmd_triggerui" id="cmd_triggerui">
                <option value="false">False</option>
                <option value="true">True</option> 
            </select>
        </div>

        <div class="form-group">
            <label for="cmd_voice_command">Voice Command(optional)</label>
            <input type="text" id="cmd_voice_command" name="cmd_voice_command" placeholder="Yes/No" />
        </div>

        <div class="form-group">
            <label for="cmd_voice_control_instruction">Voice Control Instruction Command(optional)</label>
            <input type="text" id="cmd_voice_control_instruction" name="cmd_voice_control_instruction" placeholder="ex: Turn on Fan" />
        </div>

        <div class="form-group">
            <label for="cmd_remarks">Remarks (Optional)</label>
            <textarea id="cmd_remarks" name="cmd_remarks" placeholder="Enter your remarks here..." rows="3"></textarea>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3 style="margin: 0; color: #333;">Command Frame Configuration</h3>
                <div>
                    <button type="button" class="add-row-btn" onclick="addRow('cmd')">+ Add Row</button>
                    <button type="button" class="add-row-btn" onclick="clearTable('cmd')">Clear Table</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="dynamic-table" id="additionalTable">
                    <thead>
                        <tr>
                            <th>S.No</th><th>Start Byte</th><th>End Byte</th><th>Field Description</th>
                            <th>No of Bytes</th><th>Bit Size</th><th>Data Type</th><th>Value Definition</th>
                            <th>Display Min</th><th>Display Max</th><th>Hex Min</th><th>Hex Max</th>
                            <th>UI Type</th><th>UI ID</th><th>AS PER UI</th><th>UI Serial No</th>
                            <th>UI Values</th><th>UI Page No</th><th>UI In/Out</th><th>Slider Step</th>
                            <th>Default Value</th><th>Dev Notes</th><th>Notification Enable</th>
<th>Notification Message</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="additionalTableBody"></tbody>
                </table>
            </div>
        </div>
             <div class="form-navigation">
    <button class="btn" type="button" onclick="goToPreviousStep(2)" style="transform: translateY(195px);padding: 5px 15px;     /* very small vertical padding */
        line-height: 1.2;      /* tighter text */
        height: 48px;           /* fixed height */
       width:150px;
        box-sizing: border-box; /* include padding/border in height */
         background-color: #007bff; /* Thick Blue */
       
        border-color: #0056b3;
    
">Back </button>



   <div class="command-pagination-wrapper"style= width: 100%; ">

    <div style="display: flex; gap: 10px; align-items: center;transform: translateX(-160px);">

        <button id="back-command-btn"
                class="btn"
                type="button"
                onclick="handleBackCommand()"
              style="
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 20px;
                background-color: #ffffff;
                border: 2px solid #e0e0e0; /* Thicker light border */
                border-radius: 8px;
                color: #333333;
                font-weight: 700; /* Bold/Thick text */
                font-size: 16px; /* Increased font size */
                cursor: pointer;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05); /* Subtle professional lift */
                transition: all 0.2s ease;
            ">              
            <span class="arrow"style="
            font-size: 32px; 
            line-height: 0; 
            display: inline-block; 
            margin-top: -4px; 
            font-family: serif;">‹</span>
            <span>Previous</span>
        </button>
                           
       <div class="command-progress-container">
    <div class="circles-scroll-wrapper">
        <div id="command-progress-tracker" class="progress-circles"></div>
    </div>

    <div id="command-frame-title" class="command-frame-title">
        COMMAND FRAME (1)
    </div>
</div>
 



        <button class="btn pagination-next-btn"
                type="button"
           style="transform: translateX(350%); margin-left: auto; display: flex; align-items: center; gap: 5px;padding: 10px 20px;
                background-color: #ffffff;
                border: 2px solid #e0e0e0; /* Thicker light border */
                border-radius: 8px;
                color: #333333;
                font-weight: 700; /* Bold/Thick text */
                font-size: 16px; /* Increased font size */
                cursor: pointer;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05); /* Subtle professional lift */
                transition: all 0.2s ease;
             
"
                onclick="handleNextCommand()"
             
            "> 
             
            <span>Next</span>
            <span class="arrow"
           style="
            font-size: 32px; 
            line-height: 0; 
            display: inline-block; 
            margin-top: -4px; 
            font-family: serif;"> ›</span>

           </button>

    </div>

</div>
    
    <button class="btn" type="button" onclick="goToNextStep(2)"style="transform: translateY(195px); font-size: 14px;
        padding: 5px 15px;     /* very small vertical padding */
        line-height: 1.2;      /* tighter text */
        height: 48px;           /* fixed height */
        box-sizing: border-box; /* include padding/border in height */
       background-color: #007bff; /* Thick Blue */
       
        border-color: #0056b3;
        width:150px;

    
   ">Next (Response Frame)</button>
</div>
<div style="text-align: center; margin-bottom: 20px;">
   <button id="saveBtn" class="btn" type="button" 
        style="background-color: #007bff ; width: 200px; font-weight: bold;transform: translateY(-170px);" 
        onclick="saveCommandData()">
    Save Data
</button>
    </div>

     
<div class="frame-navigation-tabs" style="display: flex; justify-content: center; gap: 20px; margin-bottom: 20px;">
    <button id="tab-command" class="frame-tab active-tab" type="button" onclick="showFrame('command')">Command</button>
    <button id="tab-response" class="frame-tab" type="button" onclick="showFrame('response')">Response</button>
    <button id="tab-error" class="frame-tab" type="button" onclick="showFrame('error')">Error</button>
</div>


<div class="save-btn-container">
          <button type="button" class="save-btn" onclick="saveCommandConfiguration()">Save Configuration</button>
        </div>

     
</div>
</div>


   <!-- Step 3: Response Frame -->
<div id="step-3" class="form-step">
    <div class="container">
   
        <h2 id="response-title-display">RESPONSE FRAME (1)</h2>
        
        <div id="step3-error" class="step-error"></div>

        <div class="form-group">
            <label for="resp_parametername">Parameter Name</label>
            <input type="text" name="resp_parametername" id="resp_parametername" readonly />
        </div>

        <!-- Data Flow Direction -->
        <div class="form-group">
            <label for="resp_data_flow_direction" class="required">Data Flow Direction</label>
            <select name="resp_data_flow_direction" id="resp_data_flow_direction">
                <option value="">Select Direction</option>
                <option value="App/Server to Device">App/Server to Device</option>
                <option value="Device to App/Server">Device to App/Server</option>
            </select>
        </div>

        <!-- Frame Type -->
        <div class="form-group">
            <label for="resp_frametype" class="required">Frame Type</label>
            <select name="resp_frametype" id="resp_frametype">
                <option value="RESPONSE">RESPONSE</option>
            </select>
        </div>

        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <label for="resp_framesizeinbytes" >Frame size in bytes</label>
                    <input type="number" id="resp_framesizeinbytes" name="resp_framesizeinbytes" placeholder="Enter number" min="0"  required/>
                </div>
            </div>
            <div class="form-col">
                <div class="form-group">
                    <label for="resp_noofrows" >No of rows</label>
                    <input type="number" id="resp_noofrows" name="resp_noofrows" readonly value="0" />
                </div>
            </div>
        </div>

        <!-- Frame Trigger -->
        <div class="form-group">
            <label for="resp_frame_trigger" class="required">Frame Trigger</label>
            <select name="resp_frametrigger" id="resp_frametrigger">
                <option value="None">None</option>
                <option value="Text">Text</option>
                <option value="Toggle Switch">Toggle Switch</option>
                <option value="Notification">Notification</option>
                <option value="Console">Console</option>
                <option value="Enter Text">Enter Text</option>
                <option value="Slider">Slider</option>
                <option value="Brightness Slider">Brightness Slider</option>
                <option value="Airflow Slider">Airflow Slider</option>
                <option value="CCT Slider">CCT Slider</option>
                <option value="Saturation Slider">Saturation Slider</option>
                <option value="Hue Slider">Hue Slider</option>
                <option value="Hue Slider Custom">Hue Slider Custom</option>
                <option value="Hue Circle">Hue Circle</option>
                <option value="Push Button">Push Button (Big)</option>
                <option value="Drop Down">Drop Down</option>
                <option value="Trigger">Trigger</option>
                <option value="Display">Display</option>
                <option value="Button">Button (Big)</option>
                <option value="Auto Trigger With Timer">Auto Trigger With Timer</option>
                <option value="Toast Display">Toast Display</option>
            </select>
        </div>

        <!-- Multiple UI -->
        <div class="form-group">
            <label for="resp_multiple_ui" class="required">Multiple UI</label>
            <select name="resp_multipleui" id="resp_multipleui">
                <option value="false">False</option>
                <option value="true">True</option> 
            </select>
        </div>

        <!-- Trigger UI -->
        <div class="form-group">
            <label for="resp_trigger_ui" class="required">Trigger UI</label>
            <select name="resp_triggerui" id="resp_triggerui">
                <option value="false">False</option>
                <option value="true">True</option> 
            </select>
        </div>

        <!-- Voice Command -->
        <div class="form-group">
            <label for="resp_voice_command">Voice Command(optional)</label>
            <input type="text" id="resp_voice_command" name="resp_voice_command" placeholder="Yes/No" />
        </div>

        <div class="form-group">
            <label for="resp_voice_control_instruction">Voice Control Instruction Command(optional)</label>
            <input type="text" id="resp_voice_control_instruction" name="resp_voice_control_instruction" placeholder="ex: Turn on Fan" />
        </div>

        <div class="form-group">
            <label for="resp_remarks">Remarks(optional)</label>
            <textarea id="resp_remarks" name="resp_remarks" placeholder="Enter your remarks here..." rows="3"></textarea>
        </div>

        <!-- Response Frame Table -->
        <div class="table-container">
            <div class="table-header">
                <h3 style="margin: 0; color: #333;">Response Frame Configuration</h3>
                <div>
                    <button type="button" class="add-row-btn" onclick="addRow('resp')">+ Add Row</button>
                    <button type="button" class="add-row-btn" onclick="clearTable('resp')">Clear Table</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="dynamic-table" id="responseTable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Start Byte</th>
                            <th>End Byte</th>
                            <th>Field Description</th>
                            <th>No of Bytes</th>
                            <th>Bit Size</th>
                            <th>Data Type</th>
                            <th>Value Definition</th>
                            <th>Display Min</th>
                            <th>Display Max</th>
                            <th>Hex Min</th>
                            <th>Hex Max</th>
                            <th>UI Type</th>
                            <th>UI ID</th>
                            <th>AS PER UI</th>
                            <th>UI Serial No</th>
                            <th>UI Values</th>
                            <th>UI Page No</th>
                            <th>UI In/Out</th>
                            <th>Slider Step</th>
                            <th>Default Value</th>
                            <th>Dev Notes</th>
                            <th>Notification Enable</th>
                            <th>Notification Message</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="responseTableBody">
                        <!-- Rows will be dynamically inserted here -->
                    </tbody>
                </table>
            </div>
        </div>
          
<div class="form-navigation">
    <button class="btn" type="button" onclick="goToPreviousStep(3)"style="transform: translateY(195px);padding: 5px 15px;     /* very small vertical padding */
        line-height: 1.2;      /* tighter text */
        height: 48px;           /* fixed height */
       width:150px;
        box-sizing: border-box; /* include padding/border in height */
         background-color: #007bff; /* Thick Blue */
       
        border-color: #0056b3;
">
        Back to Commands
    </button>


<div class="command-pagination-wrapper"style= width: 100%; ">

    <div style="display: flex; gap: 10px; align-items: center;transform: translateX(-160px);">
        <button id="back-response-btn"
                class="btn"
                type="button"
                
                onclick="handleBackResponse()"
               style="
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 20px;
                background-color: #ffffff;
                border: 2px solid #e0e0e0; /* Thicker light border */
                border-radius: 8px;
                color: #333333;
                font-weight: 700; /* Bold/Thick text */
                font-size: 16px; /* Increased font size */
                cursor: pointer;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05); /* Subtle professional lift */
                transition: all 0.2s ease;
            ">              
            <span class="arrow"style="
            font-size: 32px; 
            line-height: 0; 
            display: inline-block; 
            margin-top: -4px; 
            font-family: serif;">‹</span>
            <span>Previous</span>
        </button>


 <div class="command-progress-container">
    <div class="circles-scroll-wrapper">
        <div id="response-progress-tracker" class="progress-circles"></div>
    </div>

    <div id="response-frame-title" class="command-frame-title">
        RESPONSE FRAME (1)
    </div>
</div>



        
        <button class="btn pagination-next-btn"
                type="button"
           style="transform: translateX(350%); margin-left: auto; display: flex; align-items: center; gap: 5px;padding: 10px 20px;
                background-color: #ffffff;
                border: 2px solid #e0e0e0; /* Thicker light border */
                border-radius: 8px;
                color: #333333;
                font-weight: 700; /* Bold/Thick text */
                font-size: 16px; /* Increased font size */
                cursor: pointer;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05); /* Subtle professional lift */
                transition: all 0.2s ease;
             
"
                onclick="handleNextResponse()"
             
            "> 
             
            <span>Next</span>
            <span class="arrow"
           style="
            font-size: 32px; 
            line-height: 0; 
            display: inline-block; 
            margin-top: -4px; 
            font-family: serif;"> ›</span>

           </button>

    </div>


    </div>
    
    <button class="btn" type="button" onclick="goToNextStep(3)"style="transform: translateY(195px); font-size: 14px;
        padding: 5px 15px;     /* very small vertical padding */
        line-height: 1.2;      /* tighter text */
        height: 48px;           /* fixed height */
        box-sizing: border-box; /* include padding/border in height */
       background-color: #007bff; /* Thick Blue */
       
        border-color: #0056b3;
        width:150px;

    
   ">Next (Error Frame)</button>
</div>


<div style="text-align: center; margin-bottom: 20px;">
   <button id="saveBtn" class="btn" type="button" 
        style="background-color: #007bff ; width: 200px; font-weight: bold;transform: translateY(-170px);" 
        onclick="saveResponseData()">
    Save Data
</button>
    </div>
       <div class="frame-navigation-tabs" style="display: flex; justify-content: center; gap: 20px; margin-bottom: 20px;">
    <button id="tab-command" class="frame-tab " type="button" onclick="showFrame('command')">Command</button>
    <button id="tab-response" class="frame-tab active-tab" type="button" onclick="showFrame('response')">Response</button>
    <button id="tab-error" class="frame-tab" type="button" onclick="showFrame('error')">Error</button>
</div>

      <div class="save-btn-container">
          <button type="button" class="save-btn" onclick="saveResponseConfiguration()">Save Configuration</button>
        </div>
    </div>
</div>

<!-- Step 4: Error Frame -->
<div id="step-4" class="form-step">
    <div class="container">
        <h2 id="error-title-display">ERROR FRAME (1)</h2>
        
        <div id="step4-error" class="step-error"></div>

        <div class="form-group">
            <label for="error_parametername">Parameter Name</label>
            <input type="text" name="error_parametername" id="error_parametername" readonly />
        </div>

        <!-- Data Flow Direction -->
        <div class="form-group">
            <label for="error_data_flow_direction" class="required">Data Flow Direction</label>
            <select name="error_data_flow_direction" id="error_data_flow_direction">
                <option value="">Select Direction</option>
                <option value="App/Server to Device">App/Server to Device</option>
                <option value="Device to App/Server">Device to App/Server</option>
            </select>
        </div>

        <!-- Frame Type -->
        <div class="form-group">
            <label for="error_frametype" class="required">Frame Type</label>
            <select name="error_frametype" id="error_frametype">
                <option value="ERROR">ERROR</option>
            </select>
        </div>

        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <label for="error_framesizeinbytes" >Frame size in bytes</label>
                    <input type="number" id="error_framesizeinbytes" name="error_framesizeinbytes" placeholder="Enter number" min="0" required/>
                </div>
            </div>
            <div class="form-col">
                <div class="form-group">
                    <label for="error_noofrows" >No of rows</label>
                    <input type="number" id="error_noofrows" name="error_noofrows" readonly value="0" />
                </div>
            </div>
        </div>

        <!-- Frame Trigger -->
        <div class="form-group">
            <label for="error_frame_trigger" class="required">Frame Trigger</label>
            <select name="error_frametrigger" id="error_frametrigger">
                <option value="None">None</option>
                <option value="Text">Text</option>
                <option value="Toggle Switch">Toggle Switch</option>
                <option value="Notification">Notification</option>
                <option value="Console">Console</option>
                <option value="Enter Text">Enter Text</option>
                <option value="Slider">Slider</option>
                <option value="Brightness Slider">Brightness Slider</option>
                <option value="Airflow Slider">Airflow Slider</option>
                <option value="CCT Slider">CCT Slider</option>
                <option value="Saturation Slider">Saturation Slider</option>
                <option value="Hue Slider">Hue Slider</option>
                <option value="Hue Slider Custom">Hue Slider Custom</option>
                <option value="Hue Circle">Hue Circle</option>
                <option value="Push Button">Push Button (Big)</option>
                <option value="Drop Down">Drop Down</option>
                <option value="Trigger">Trigger</option>
                <option value="Display">Display</option>
                <option value="Button">Button (Big)</option>
                <option value="Auto Trigger With Timer">Auto Trigger With Timer</option>
                <option value="Toast Display">Toast Display</option>
            </select>
        </div>

        <!-- Multiple UI -->
        <div class="form-group">
            <label for="error_multiple_ui" class="required">Multiple UI</label>
            <select name="error_multipleui" id="error_multipleui">
                <option value="false">False</option>
                <option value="true">True</option> 
            </select>
        </div>

        <!-- Trigger UI -->
        <div class="form-group">
            <label for="error_trigger_ui" class="required">Trigger UI</label>
            <select name="error_triggerui" id="error_triggerui">
                <option value="false">False</option>
                <option value="true">True</option> 
            </select>
        </div>

        <!-- Voice Command -->
        <div class="form-group">
            <label for="error_voice_command">Voice Command(optional)</label>
            <input type="text" id="error_voice_command" name="error_voice_command" placeholder="Yes/No" />
        </div>

        <div class="form-group">
            <label for="error_voice_control_instruction">Voice Control Instruction Command(optional)</label>
            <input type="text" id="error_voice_control_instruction" name="error_voice_control_instruction" placeholder="ex: Turn on Fan" />
        </div>

        <div class="form-group">
            <label for="error_remarks">Remarks(optional)</label>
            <textarea id="error_remarks" name="error_remarks" placeholder="Enter your remarks here..." rows="3"></textarea>
        </div>

        <!-- Error Frame Table -->
        <div class="table-container">
            <div class="table-header">
                <h3 style="margin: 0; color: #333;">Error Frame Configuration</h3>
                <div>
                    <button type="button" class="add-row-btn" onclick="addRow('error')">+ Add Row</button>
                    <button type="button" class="add-row-btn" onclick="clearTable('error')">Clear Table</button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="dynamic-table" id="errorTable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Start Byte</th>
                            <th>End Byte</th>
                            <th>Field Description</th>
                            <th>No of Bytes</th>
                            <th>Bit Size</th>
                            <th>Data Type</th>
                            <th>Value Definition</th>
                            <th>Display Min</th>
                            <th>Display Max</th>
                            <th>Hex Min</th>
                            <th>Hex Max</th>
                            <th>UI Type</th>
                            <th>UI ID</th>
                            <th>AS PER UI</th>
                            <th>UI Serial No</th>
                            <th>UI Values</th>
                            <th>UI Page No</th>
                            <th>UI In/Out</th>
                            <th>Slider Step</th>
                            <th>Default Value</th>
                            <th>Dev Notes</th>
                            <th>Notification Enable</th>
                            <th>Notification Message</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="errorTableBody">
                        <!-- Rows will be dynamically inserted here -->
                    </tbody>
                </table>
            </div>
        </div>

<div class="form-navigation">
    <button class="btn" type="button" onclick="goToPreviousStep(4)" 
            style="transform: translateY(195px); padding: 5px 15px; line-height: 1.2; height: 48px; width: 150px; box-sizing: border-box; background-color: #007bff; border-color: #0056b3; color: white;">
        Back to Response
    </button>

    <div class="command-pagination-wrapper" style="width: 100%;">
        <div style="display: flex; gap: 10px; align-items: center; transform: translateX(-100px);">
            
            <button id="back-error-btn" class="btn" type="button" onclick="handleBackErrorFrame()" 
                    style="display: flex; align-items: center; gap: 10px; padding: 10px 20px; background-color: #ffffff; border: 2px solid #e0e0e0; border-radius: 8px; color: #333333; font-weight: 700; font-size: 16px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: all 0.2s ease;">
                <span class="arrow" style="font-size: 32px; line-height: 0; display: inline-block; margin-top: -4px; font-family: serif;">‹</span>
                <span>Previous</span>
            </button>

            <div class="command-progress-container">
                <div class="circles-scroll-wrapper">
                    <div id="error-progress-tracker" class="progress-circles"></div>
                </div>
                <div id="error-frame-title" class="command-frame-title">
                    ERROR FRAME (1)
                </div>
            </div>

            <button class="btn pagination-next-btn" type="button" onclick="handleNextError()" 
                    style="transform: translateX(330%); margin-left: auto; display: flex; align-items: center; gap: 5px; padding: 10px 20px; background-color: #ffffff; border: 2px solid #e0e0e0; border-radius: 8px; color: #333333; font-weight: 700; font-size: 16px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: all 0.2s ease;">
                <span>Next</span>
                <span class="arrow" style="font-size: 32px; line-height: 0; display: inline-block; margin-top: -4px; font-family: serif;"> ›</span>
            </button>
        </div>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <button id="saveErrorBtn" class="btn" type="button" 
                style="background-color: #007bff; color: white; width: 200px; font-weight: bold; transform: translateY(-70px) translateX(-230px);" 
                onclick="saveErrorData()">
            Save Data
        </button>
    </div>
</div>


<div class="frame-navigation-tabs" style="display: flex; justify-content: center; gap: 20px; margin-bottom: 20px; margin-top: 40px;">
    <button id="tab-command" class="frame-tab" type="button" onclick="showFrame('command')">Command</button>
    <button id="tab-response" class="frame-tab" type="button" onclick="showFrame('response')">Response</button>
    <button id="tab-error" class="frame-tab active-tab" type="button" onclick="showFrame('error')">Error</button>
</div>

<div class="save-btn-container" style="text-align: center;">
    <button type="button" class="save-btn" onclick="saveErrorConfiguration()" 
            style="padding: 10px 30px; background-color: #28a745; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">
        Save Configuration
    </button>
</div>

    </div>
</div>

<div id="bitwiseConfigModal" class="modal-overlay">
    <div class="modal-box">

        <h3>Bitwise Configuration</h3>

        <table class="config-table">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Start Bit</th>
                    <th>End Bit</th>
                    <th>No. of Bits</th>
                    <th>Data Type</th>
                    <th>Value Definition</th>

                    <th>Display Min</th>
                    <th>Display Max</th>
                    <th>Hex Min</th>
                    <th>Hex Max</th>

                    <th>UI Type</th>
                    <th>UI ID</th>
                    <th>As Per UI</th>
                    <th>UI Serial No</th>
                    <th>UI Values</th>
                    <th>UI Page No</th>
                    <th>UI In/Out</th>

                    <th>Slider Step</th>
                    <th>Default Value</th>
                    <th>Dev Notes</th>
                    <th>Notification Enable</th>
        <th>Notification Message</th>
 
                   
                </tr>
            </thead>
             <tbody id="bitwiseTableBody"></tbody>
        </table>

        <div class="modal-actions">
            <button class="btn-save" onclick=" saveBitwiseConfig(event)">Save</button>
            <button class="btn-save"onclick="closeBitwiseConfig(event)">Cancel</button>
        </div>
             


    </div>
</div>


<?php echo form_close(); ?>
</div>
</div>

<script>
/* ===========================
   JavaScript - main script
   =========================== */


// Global variables

let currentStep = 1;
const totalSteps = 4;
let currentCommandSubStep = 1;
let totalCommandsToCreate = 2;
var isCommandSaved = false;
var isResponseSaved = false;
var isErrorSaved = false;
// Initialize form
document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM Loaded - Initializing form");
    
    // Sync parameter name listeners
    syncParameterNameToAllSteps();
    updateProgressIndicator();

    // Initialize tables (adds initial rows if none)
    initializeTables();

    // Attach handlers to any existing rows (if page was pre-populated)
    ['additionalTableBody','responseTableBody','errorTableBody'].forEach(id => {
        const tbody = document.getElementById(id);
        if (!tbody) return;
        const prefix = id === 'additionalTableBody' ? 'cmd' : (id === 'responseTableBody' ? 'resp' : 'error');
        console.log(`Setting up rows for ${prefix} table (${tbody.rows.length} rows)`);
        
        Array.from(tbody.rows).forEach((row, idx) => {
            attachUIChangeHandlerForRow(row, prefix, idx + 1);
            attachFieldDescriptionHandler(row, prefix, idx + 1);
            attachUIChangeValidationHandler(row, prefix, idx + 1);
            setupByteCalculation(row, prefix, idx + 1);
            attachFieldDescriptionChangeHandler(row, prefix, idx + 1); // NEW: Add field description change handler
        });
        // update frame size fields to reflect current rows
        updateFrameCountsForPrefix(prefix);
    });

    // Initialize cmd frame size based on existing rows
    updateFrameCountsForPrefix('cmd');
    updateFrameCountsForPrefix('resp');
    updateFrameCountsForPrefix('error');
    
    console.log("Initialization complete");
});

// Initialize all tables
function initializeTables() {
    console.log("Initializing tables");
    // If tables are empty, add two rows by default (only for fresh forms)
    const tables = [
        {id:'additionalTableBody', prefix:'cmd'}, 
        {id:'responseTableBody', prefix:'resp'}, 
        {id:'errorTableBody', prefix:'error'}
    ];
    
    tables.forEach(t => {
        const tbody = document.getElementById(t.id);
        if (!tbody) return;
        console.log(`${t.id}: ${tbody.rows.length} rows`);
        if (tbody.rows.length === 0) {
            console.log(`Adding 2 rows to ${t.id}`);
            for (let i = 0; i < 2; i++) {
                addRow(t.prefix);
            }
        } else {
            // ensure indices & handlers are correct
            updateSerialNumbers(t.id);
        }
    });
}

// ======================================================
// NEW FUNCTION: Update Default Value based on Field Description
// ======================================================
function updateDefaultValueBasedOnFieldDescription(selectElement, prefix, index) {
    if (!selectElement) return;
    
    const fieldDescription = selectElement.value;
    const row = selectElement.closest('tr');
    if (!row) return;
    
    const defaultValueInput = row.querySelector(`input[name="defaultValue_${prefix}_${index}"]`);
    const parameterName = document.getElementById('par_parametername').value;
    
    if (defaultValueInput) {
        if (fieldDescription === 'Data') {
            // Set Default Value to Parameter Name
            defaultValueInput.value = parameterName;
            defaultValueInput.classList.add('auto-calculated');
            defaultValueInput.readOnly = false;
        } else {
            // Clear Default Value
            defaultValueInput.value = '';
            defaultValueInput.classList.remove('auto-calculated');
            defaultValueInput.readOnly = false;
        }
    }
    
    // Also update the hidden field
    const hiddenField = row.querySelector(`input[name="${prefix}_defaultvalue_${index}"]`);
    if (hiddenField) {
        hiddenField.value = defaultValueInput ? defaultValueInput.value : '';
    }
}

// ======================================================
// NEW FUNCTION: Attach field description change handler
// ======================================================
function attachFieldDescriptionChangeHandler(row, prefix, index) {
    if (!row) return;
    
    const fieldDescSelect = row.querySelector(`select[name="fieldDescription_${prefix}_${index}"]`);
    if (fieldDescSelect) {
        // Remove existing event listener to avoid duplicates
        const newFieldDescSelect = fieldDescSelect.cloneNode(true);
        fieldDescSelect.parentNode.replaceChild(newFieldDescSelect, fieldDescSelect);
        
        // Add event listener for field description change
        newFieldDescSelect.addEventListener('change', function() {
            updateDefaultValueBasedOnFieldDescription(this, prefix, index);
        });
        
        // Also trigger on initial load if value is already "Data"
        if (newFieldDescSelect.value === 'Data') {
            updateDefaultValueBasedOnFieldDescription(newFieldDescSelect, prefix, index);
        }
    }
}

// ======================================================
// NAVIGATION FUNCTIONS
// ======================================================

function showStep(step) {
    // Hide all steps
    document.querySelectorAll('.form-step').forEach(div => {
        div.classList.remove('active');
    });
    
    // Show current step
    const el = document.getElementById('step-' + step);
    if (el) {
        el.classList.add('active');
    }
    
    currentStep = step;
    updateProgressIndicator();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goToNextStep(step) {
    // 1. If we are on Step 1, find out how many commands the user wants
    if (step === 1) {
        totalCommandsToCreate = parseInt(document.getElementById('commands').value) || 1;
        currentCommandSubStep = 1;
        prepareCommandFrame(currentCommandSubStep);
    }

    // 2. Logic for Step 2 (Command Frame)
    if (step === 2) {
        if (currentCommandSubStep < totalCommandsToCreate) {
            // Save current table data here if needed, then:
            currentCommandSubStep++;
            prepareCommandFrame(currentCommandSubStep);
            return; // EXIT HERE so we don't move to Step 3 yet
        }
    }

    // 3. Standard move to next main step (e.g. Step 2 to Step 3)
    document.getElementById(`step-${step}`).style.display = 'none';
    currentStep = step + 1;
    const nextStepEl = document.getElementById(`step-${currentStep}`);
    if (nextStepEl) nextStepEl.style.display = 'block';
    
    updateProgressIndicator();
}
function goToPreviousStep(step) {
    if (step === 2 && currentCommandSubStep > 1) {
        // Go to previous Command Frame
        currentCommandSubStep--;
        prepareCommandFramePage(currentCommandSubStep);
        return;
    }

    // Standard back transition
    document.getElementById(`step-${step}`).classList.remove('active');
    currentStep = step - 1;
    document.getElementById(`step-${currentStep}`).classList.add('active');
    updateProgressIndicator();
}


    function goToPreviousStep(step) {
    if (step === 2 && currentCommandSubStep > 1) {
        // Go to previous Command Frame
        currentCommandSubStep--;
        prepareCommandFramePage(currentCommandSubStep);
        return;
    }

    // Standard back transition
    document.getElementById(`step-${step}`).classList.remove('active');
    currentStep = step - 1;
    document.getElementById(`step-${currentStep}`).classList.add('active');
    updateProgressIndicator();
}

function prepareCommandFrame(index) {
    // Update the Title
    const title = document.querySelector('#step-2 h2');
    if (title) title.innerText = `COMMAND FRAME #${index}`;

    const tbody = document.getElementById('additionalTableBody');
    if (!tbody) return;

    // If table already has rows, DO NOTHING (keep user data)
    if (tbody.rows.length === 0) {
        // Only add rows if it's truly empty (first load)
        for (let i = 0; i < 2; i++) {
            addRow('cmd');
        }
    }

    // Only update counters, do not reset user data
    updateFrameCountsForPrefix('cmd');

    // Scroll to top so user sees the new header
    window.scrollTo(0, 0);
}



// Progress indicator
function updateProgressIndicator() {
    for (let i = 1; i <= totalSteps; i++) {
        const stepIndicator = document.getElementById('step-indicator-' + i);
        if (!stepIndicator) continue;
        
        stepIndicator.classList.remove('active', 'completed');
        
        if (i < currentStep) {
            stepIndicator.classList.add('completed');
        } else if (i === currentStep) {
            stepIndicator.classList.add('active');
        }
    }
}

// Update frame size and number of rows for a given prefix (cmd/resp/error)
function updateFrameCountsForPrefix(prefix) {
    const tableId = prefix === 'cmd' ? 'additionalTableBody' : 
                   (prefix === 'resp' ? 'responseTableBody' : 'errorTableBody');
    const tableBody = document.getElementById(tableId);
    const count = tableBody ? tableBody.rows.length : 0;

    // Helper to update both the .value and the HTML attribute
    const updateInput = (id) => {
        const el = document.getElementById(id);
        if (el) {
            el.value = count;
            el.setAttribute('value', count); // Mandatory for some form handlers
        }
    };

    if (prefix === 'cmd') {
        updateInput('cmd_framesizeinbytes');
        updateInput('cmd_noofrows');
    } else if (prefix === 'resp') {
        updateInput('resp_framesizeinbytes');
        updateInput('resp_noofrows');
    } else if (prefix === 'error') {
        updateInput('error_framesizeinbytes');
        updateInput('error_noofrows');
    }
}
// Frame size and rows management (user input of frame size will add/remove rows for cmd)
const frameSizeInput = document.getElementById('cmd_framesizeinbytes');
const noOfRowsInput = document.getElementById('cmd_noofrows');

if (frameSizeInput && noOfRowsInput) {
    frameSizeInput.addEventListener('input', () => {
        const frameSize = parseInt(frameSizeInput.value) || 0;
        noOfRowsInput.value = frameSize; // update read-only no of rows

        const tableBody = document.getElementById('additionalTableBody');
        if (!tableBody) return;
        const currentRows = tableBody.rows.length;

        // Add rows if frameSize > currentRows
        for (let i = currentRows; i < frameSize; i++) {
            addRow('cmd');
        }

        // Remove rows if frameSize < currentRows
        for (let i = currentRows; i > frameSize; i--) {
            tableBody.deleteRow(i - 1);
        }

        updateSerialNumbers('additionalTableBody');
        updateMoveButtons('additionalTableBody');
        updateFrameCountsForPrefix('cmd');
    });
}
// Inside your existing DOMContentLoaded listener
const proceedBtn = document.getElementById('proceedBtn'); // Your Step 1 button
if(proceedBtn) {
    proceedBtn.addEventListener('click', function() {
        const cmdCount = document.getElementById('commands').value;
        console.log(`User requested ${cmdCount} command frames.`);
        currentCommandSubStep = 1; 
        prepareCommandFramePage(1);
    });
}

// Response frame size listener
const respFrameSizeInput = document.getElementById('resp_framesizeinbytes');
const respNoOfRowsInput = document.getElementById('resp_noofrows');

if (respFrameSizeInput && respNoOfRowsInput) {
    respFrameSizeInput.addEventListener('input', () => {
        const frameSize = parseInt(respFrameSizeInput.value) || 0;
        respNoOfRowsInput.value = frameSize;

        const tableBody = document.getElementById('responseTableBody');
        if (!tableBody) return;
        const currentRows = tableBody.rows.length;

        // Add rows if frameSize > currentRows
        for (let i = currentRows; i < frameSize; i++) {
            addRow('resp');
        }

        // Remove rows if frameSize < currentRows
        for (let i = currentRows; i > frameSize; i--) {
            tableBody.deleteRow(i - 1);
        }

        updateSerialNumbers('responseTableBody');
        updateMoveButtons('responseTableBody');
        updateFrameCountsForPrefix('resp');
    });
}

// Error frame size listener
const errorFrameSizeInput = document.getElementById('error_framesizeinbytes');
const errorNoOfRowsInput = document.getElementById('error_noofrows');

if (errorFrameSizeInput && errorNoOfRowsInput) {
    errorFrameSizeInput.addEventListener('input', () => {
        const frameSize = parseInt(errorFrameSizeInput.value) || 0;
        errorNoOfRowsInput.value = frameSize;

        const tableBody = document.getElementById('errorTableBody');
        if (!tableBody) return;
        const currentRows = tableBody.rows.length;

        // Add rows if frameSize > currentRows
        for (let i = currentRows; i < frameSize; i++) {
            addRow('error');
        }

        // Remove rows if frameSize < currentRows
        for (let i = currentRows; i > frameSize; i--) {
            tableBody.deleteRow(i - 1);
        }

        updateSerialNumbers('errorTableBody');
        updateMoveButtons('errorTableBody');
        updateFrameCountsForPrefix('error');
    });
}

// ======================================================
// MAIN AUTO-CALCULATION FUNCTION
// ======================================================

function calculateByteValues(row, startByteInput, endByteInput, prefix, index, forceDisplay = false) {
          
         const startByte = parseInt(startByteInput.value) || 0;
    const endByte = parseInt(endByteInput.value) || 0;
    
    const numberOfBytes = (endByte >= startByte && startByte >= 0) ? (endByte - startByte) + 1 : 0;
    const bitSize = numberOfBytes * 8;

    // --- ALWAYS UPDATE THESE (Sizes and Hex) ---
    const noBytesIn = row.querySelector(`input[name="noOfBytes_${prefix}_${index}"]`);
    const bitSizeIn = row.querySelector(`input[name="bitSize_${prefix}_${index}"]`);
    const hMinIn = row.querySelector(`input[name="hexMin_${prefix}_${index}"]`);
    const hMaxIn = row.querySelector(`input[name="hexMax_${prefix}_${index}"]`);

    if (noBytesIn) noBytesIn.value = numberOfBytes;
    if (bitSizeIn) bitSizeIn.value = bitSize;

    if (numberOfBytes > 0) {
        if (hMinIn) hMinIn.value = `0x${"0".repeat(numberOfBytes * 2)}`;
        if (hMaxIn) {
            const maxDecimal = Math.pow(256, numberOfBytes) - 1;
            hMaxIn.value = `0x${maxDecimal.toString(16).toUpperCase().padStart(numberOfBytes * 2, "0")}`;
        }
    }

    // --- UPDATE DISPLAY MIN/MAX ONLY IF forceDisplay IS TRUE ---
    const dataTypeSelect = row.querySelector(`select[name="dataType_${prefix}_${index}"]`);
    const dMinIn = row.querySelector(`input[name="displayMin_${prefix}_${index}"]`);
    const dMaxIn = row.querySelector(`input[name="displayMax_${prefix}_${index}"]`);

    if (forceDisplay && dataTypeSelect) {
        const dataType = dataTypeSelect.value;
        let displayMin = 0;
        let displayMax = 0;

        // Math for Min/Max
        switch (dataType) {
            case "UnsignedInt": displayMax = Math.pow(2, bitSize) - 1; break;
            case "SignedInt": 
                const half = Math.pow(2, bitSize - 1);
                displayMin = -half; displayMax = half - 1; 
                break;
            case "Float": displayMin = 0; displayMax = 100; break;
            default: displayMax = Math.pow(2, bitSize) - 1;
        }

        if (dMinIn) dMinIn.value = displayMin;
        if (dMaxIn) dMaxIn.value = displayMax;

        // Sync hidden fields for Min/Max
        syncHiddenField(prefix, 'displayValueMin', index, displayMin);
        syncHiddenField(prefix, 'displayValueMax', index, displayMax);
    }


   // Calculate hex values
    let hexMinValue = "0x00";
    let hexMaxValue = "0x00";
    
   if (hexMinInput && numberOfBytes > 0) {

    // Hex MIN = 0x + (2 zeros per byte)
    const zeroCount = numberOfBytes * 2;
    const hexZeros = "0".repeat(zeroCount);

    hexMinValue = `0x${hexZeros}`;
    hexMinInput.value = hexMinValue;

    }
    
    if (hexMaxInput && numberOfBytes > 0) {

    // Max value = (256 ^ numberOfBytes) - 1
    const maxDecimal = Math.pow(256, numberOfBytes) - 1;

    let hexString = maxDecimal
        .toString(16)
        .toUpperCase()
        .padStart(numberOfBytes * 2, "0");


    hexMaxValue = `0x${hexString}`;
    hexMaxInput.value = hexMaxValue;
}

    // ======================================================
    // UPDATE HIDDEN FIELDS
    // ======================================================
    
    // Get other values from the row
    const fieldDescSelect = row.querySelector(`select[name="fieldDescription_${prefix}_${index}"]`);
    const fieldDescVal = fieldDescSelect ? fieldDescSelect.value : "None";
    
    const dataTypeVal = dataTypeSelect ? dataTypeSelect.value : "None";
    
    const valueDefSelect = row.querySelector(`select[name="valueDefinition_${prefix}_${index}"]`);
    const valueDefVal = valueDefSelect ? valueDefSelect.value : "None";
    
    const uiTypeSelect = row.querySelector(`select[name="uiType_${prefix}_${index}"]`);
    const uiTypeVal = uiTypeSelect ? uiTypeSelect.value : "None";
    
    const uiIdInput = row.querySelector(`input[name="uiId_${prefix}_${index}"]`);
    const uiIdVal = uiIdInput ? uiIdInput.value : "";
    
    const asperUiInput = row.querySelector(`input[name="asperui_${prefix}_${index}"]`);
    const asperUiVal = asperUiInput ? asperUiInput.value : "";
    
    const snoInput = row.querySelector(`input[name="sno_asperui_${prefix}_${index}"]`);
    const snoVal = snoInput ? snoInput.value : "";
    
    const asperValueInput = row.querySelector(`input[name="asperuivalue_${prefix}_${index}"]`);
    const asperValueVal = asperValueInput ? asperValueInput.value : "";
    
    const uiPageInput = row.querySelector(`input[name="uiPageNo_${prefix}_${index}"]`);
    const uiPageVal = uiPageInput ? uiPageInput.value : "";

// 1. Target the correct input (ensure name is lowercase)
// 1. Use lowercase 'uiinout' to match the HTML
// MUST use lowercase 'uiinout' to match your HTML
const uiInOutInput = row.querySelector(`input[name="uiinout_${prefix}_${index}"]`);
let uiInOutVal = (uiInOutInput && uiInOutInput.value.trim() !== "") ? uiInOutInput.value.trim() : "None";

// If the system thinks it's "1" or empty, don't let it save as "None" 
// if we already have data.
if (uiInOutVal === "1" || uiInOutVal === index.toString() || uiInOutVal === "") {
    uiInOutVal = "None";
}    
    const sliderStepInput = row.querySelector(`input[name="sliderStep_${prefix}_${index}"]`);
    const sliderStepVal = sliderStepInput ? sliderStepInput.value : "";
    
    const defaultValueInput = row.querySelector(`input[name="defaultValue_${prefix}_${index}"]`);
    const defaultValueVal = defaultValueInput ? defaultValueInput.value : "";
    
    const devNotesInput = row.querySelector(`input[name="developerNotes_${prefix}_${index}"]`);
    const devNotesVal = devNotesInput ? devNotesInput.value : "";

    // Define ALL hidden fields that should exist
    const allHiddenFields = [
        {name: `${prefix}_startBytePosition_${index}`, value: startByte},
        {name: `${prefix}_endBytePosition_${index}`, value: endByte},
        {name: `${prefix}_numberOfBytes_${index}`, value: numberOfBytes},
        {name: `${prefix}_bitSize_${index}`, value: bitSize},
        {name: `${prefix}_displayValueMin_${index}`, value: displayMin},
        {name: `${prefix}_displayValueMax_${index}`, value: displayMax},
        {name: `${prefix}_valueInHexMin_${index}`, value: hexMinValue},
        {name: `${prefix}_valueInHexMax_${index}`, value: hexMaxValue},
        {name: `${prefix}_fieldDescription_${index}`, value: fieldDescVal},
        {name: `${prefix}_dataType_${index}`, value: dataTypeVal},
        {name: `${prefix}_valueDefinition_${index}`, value: valueDefVal},
        {name: `${prefix}_uiType_${index}`, value: uiTypeVal},
        {name: `${prefix}_uiId_${index}`, value: uiIdVal},
        {name: `${prefix}_asperui_${index}`, value: asperUiVal},
        {name: `${prefix}_sno_asperui_${index}`, value: snoVal},
        {name: `${prefix}_asperuivalue_${index}`, value: asperValueVal},
        {name: `${prefix}_uiPageNo_${index}`, value: uiPageVal},
        {name: `${prefix}_uiDescriptionInOut_${index}`, value: uiInOutVal},
        {name: `${prefix}_sliderStepValue_${index}`, value: sliderStepVal},
        {name: `${prefix}_defaultvalue_${index}`, value: defaultValueVal},
        {name: `${prefix}_developerNotes_${index}`, value: devNotesVal}
          
    ];

    

    // Create or update ALL hidden fields
    // Create or update ALL hidden fields

allHiddenFields.forEach(field => {
    let hiddenField = row.querySelector(`input[name="${field.name}"]`);
    
    if (!hiddenField) {
        hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = field.name;
        hiddenField.value = field.value;
        row.appendChild(hiddenField);
    } else {
        // --- MULTI-FIELD SAFE GUARD ---
        const isProtected = field.name.includes("fieldDescription") || 
                            field.name.includes("uiId") || 
                            field.name.includes("uiDescriptionInOut");

        const hasExistingValue = hiddenField.value !== "" && hiddenField.value !== "None";
        const newValueIsEmpty = field.value === "" || field.value === "None" || field.value === undefined;

        if (isProtected && hasExistingValue && newValueIsEmpty) {
            console.log(`[PROTECTED] Prevented ${field.name} from being cleared.`);
        } else {
            hiddenField.value = field.value;
        }
    }
});


    // Final verification
    const verifyFields = [
        `${prefix}_numberOfBytes`,
        `${prefix}_bitSize`
    ];
    
    console.log(`[CALC] Final verification:`);
    verifyFields.forEach(fieldName => {
        const field = row.querySelector(`input[name="${fieldName}"]`);
        console.log(`[CALC]   ${fieldName}: ${field ? `"${field.value}"` : 'MISSING!'}`);
    });
  printHiddenFields(row, prefix, index);
}

// Helper function to create or update hidden field
// 1. The Helper (Stays separate)
function createOrUpdateHiddenInput(row, name, value) {
    let hiddenInput = row.querySelector(`input[name="${name}"]`);
    if (!hiddenInput) {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = name;
        row.appendChild(hiddenInput);
    }
    hiddenInput.value = value;
    return hiddenInput;
}

// 2. The Logic (Inside your main calculation function)
function printHiddenFields(row, prefix, index) {
     console.log('🔥 printHiddenFields CALLED');
    const hiddenInputs = row.querySelectorAll('input[type="hidden"]');

    if (!hiddenInputs.length) {
        console.warn(`❌ No hidden fields found for ${prefix} row ${index}`);
        return;
    }

    const data = {};

    hiddenInputs.forEach(input => {
        data[input.name] = input.value;
    });

    console.group(`🟢 Hidden fields → ${prefix} row ${index}`);
    console.table(data);
    console.groupEnd();
}

// ======================================================
// Setup auto-calculation for a row
// ======================================================
function setupByteCalculation(row, prefix, index) {
    if (!row) return;

    const startIn = row.querySelector(`input[name="startBytePosition_${prefix}_${index}"]`);
    const endIn = row.querySelector(`input[name="endBytePosition_${prefix}_${index}"]`);
    const dataSel = row.querySelector(`select[name="dataType_${prefix}_${index}"]`);

    // When typing Bytes: Pass FALSE (Keeps Min/Max empty)
    const onByteInput = () => calculateByteValues(row, startIn, endIn, prefix, index, false);
    
    // When changing Data Type: Pass TRUE (Shows Min/Max)
    const onTypeChange = () => calculateByteValues(row, startIn, endIn, prefix, index, true);

    startIn.addEventListener('input', onByteInput);
    endIn.addEventListener('input', onByteInput);
    
    if (dataSel) {
        dataSel.addEventListener('change', onTypeChange);
    }
}




// ======================================================
// AUTO-FILL BASED ON UI TYPE
// ======================================================
function applyUIAutoFill(selectElement, prefix, index) {
    if (!selectElement) return;

    const uiType = (selectElement.value || '').toLowerCase();
    const row = selectElement.closest('tr');
    if (!row) return;

    // --- Selectors ---
    const asperUiInput = row.querySelector(`input[name="asperui_${prefix}_${index}"]`);
    const uiValuesInput = row.querySelector(`input[name="asperuivalue_${prefix}_${index}"]`);
    const uiSerialInput = row.querySelector(`input[name="sno_asperui_${prefix}_${index}"]`);
    const valueDefSelect = row.querySelector(`select[name="valueDefinition_${prefix}_${index}"]`);
    const dataTypeSelect = row.querySelector(`select[name="dataType_${prefix}_${index}"]`);
    const uiInOutInput = row.querySelector(`input[name="uiinout_${prefix}_${index}"]`);

    if (!asperUiInput || !uiValuesInput || !uiSerialInput || !valueDefSelect || !dataTypeSelect) return;

    const setVal = (el, val) => el && (el.value = val);

    // --- Mapping logic for different UI Types ---
    if (uiType === 'toggle switch') {
        setVal(valueDefSelect, 'Fixed Value');
        setVal(dataTypeSelect, 'UnsignedInt');
        setVal(asperUiInput, 'false,true');
        setVal(uiValuesInput, '00,01');
        setVal(uiSerialInput, '1');
        setVal(uiInOutInput, 'Read/Write');
    } 
    else if (uiType.includes('slider') || uiType === 'hue circle') {
        setVal(valueDefSelect, 'Range Value');
        setVal(dataTypeSelect, 'UnsignedInt');
        setVal(asperUiInput, '0,0');
        setVal(uiValuesInput, '1');
        setVal(uiSerialInput, '1');
        setVal(uiInOutInput, 'Read/Write');
    } 
    else if (uiType === 'console' || uiType === 'entertext') {
        setVal(valueDefSelect, 'Fixed Value');
        setVal(dataTypeSelect, 'String');
        setVal(asperUiInput, '1');
        setVal(uiValuesInput, '1');
        setVal(uiSerialInput, '1');
        setVal(uiInOutInput, 'Read/Write');
    } 
    else if (uiType === 'dropdown') {
        setVal(valueDefSelect, 'Fixed Value');
        setVal(dataTypeSelect, 'UnsignedInt');
        setVal(asperUiInput, 'Option1,Option2'); // Default placeholder
        setVal(uiValuesInput, '00,01');
        setVal(uiSerialInput, '1');
        setVal(uiInOutInput, 'Read/Write');
    } 
    else if (uiType === 'trigger' || uiType === 'button big') {
        setVal(valueDefSelect, 'Fixed Value');
        setVal(dataTypeSelect, 'UnsignedInt');
        setVal(asperUiInput, 'true');
        setVal(uiValuesInput, '01');
        setVal(uiSerialInput, '1');
        setVal(uiInOutInput, 'Write');
    } 
    else {
        // Default for "None" or others
        setVal(asperUiInput, '1');
        setVal(uiValuesInput, '1');
        setVal(uiSerialInput, '1');
        setVal(uiInOutInput, 'None');
    }

    // --- Sync Hidden Fields (Ensures DB gets the data) ---
    syncHiddenField(prefix, 'asperui', index, asperUiInput.value);
    syncHiddenField(prefix, 'asperuivalue', index, uiValuesInput.value);
    syncHiddenField(prefix, 'sno_asperui', index, uiSerialInput.value);
    syncHiddenField(prefix, 'valueDefinition', index, valueDefSelect.value);
    syncHiddenField(prefix, 'dataType', index, dataTypeSelect.value);
    if (uiInOutInput) syncHiddenField(prefix, 'uiDescriptionInOut', index, uiInOutInput.value);

    // --- Trigger Min/Max Calculation ---
    const startIn = row.querySelector(`input[name="startBytePosition_${prefix}_${index}"]`);
    const endIn = row.querySelector(`input[name="endBytePosition_${prefix}_${index}"]`);
    if (startIn && endIn) {
        // We pass TRUE to force displayMin/Max to show up for these UI types
        calculateByteValues(row, startIn, endIn, prefix, index, true);
    }
}

/**
 * UPDATED HELPER: Blocks the "1" from overwriting text
 */

function syncHiddenField(prefix, fieldKey, index, value) {
    const hiddenName = `${prefix}_${fieldKey}_${index}`;
    const hiddenInput = document.getElementsByName(hiddenName)[0];
    
    if (!hiddenInput) return;

    if (fieldKey === 'uiDescriptionInOut') {
        const newVal = String(value).trim();

        if (!['None', 'Read', 'Write', 'Read/Write'].includes(newVal)) {
            return;
        }
    }

    hiddenInput.value = value;
    hiddenInput.setAttribute('value', value);
}

/**
 * INSTANT SYNC: Captures typing immediately
 */

document.addEventListener('input', function (e) {
    if (e.target && e.target.name && e.target.name.startsWith('uiinout_')) {
        const parts = e.target.name.split('_'); 
        const prefix = parts[1];
        const index = parts[2];
        let val = e.target.value.trim();

        // Standardize the input
        if (val.toLowerCase() === 'read') val = 'Read';
        if (val.toLowerCase() === 'write') val = 'Write';
        if (val.toLowerCase() === 'read/write') val = 'Read/Write';

        // If it's empty or the index number, it's 'None'
        if (val === "" || val === "1" || val === index.toString()) {
            val = "None";
        }

        syncHiddenField(prefix, 'uiDescriptionInOut', index, val);
    }
});
// Handle field description changes
function handleFieldDescriptionChange(selectElement, prefix, index) {
    if (!selectElement) return;
    const fieldDesc = selectElement.value;
    const row = selectElement.closest('tr');
    
    // Always sync the Field Description itself first
    syncHiddenField(prefix, 'fieldDescription', index, fieldDesc);

    if (fieldDesc === 'Terminator') {
        const dataTypeSelect = row.querySelector(`select[name="dataType_${prefix}_${index}"]`);
        const valueDefSelect = row.querySelector(`select[name="valueDefinition_${prefix}_${index}"]`);
        
        if (dataTypeSelect) {
            dataTypeSelect.value = 'None';
            dataTypeSelect.classList.add('auto-calculated');
            // FIX: Pass the index so it finds "resp_dataType_1"
            syncHiddenField(prefix, 'dataType', index, 'None');
        }
        
        if (valueDefSelect) {
            valueDefSelect.value = 'None';
            valueDefSelect.classList.add('auto-calculated');
            // FIX: Pass the index so it finds "resp_valueDefinition_1"
            syncHiddenField(prefix, 'valueDefinition', index, 'None');
        }
        
        // Trigger calculation to update hidden fields for bytes
        const startInput = row.querySelector(`input[name="startBytePosition_${prefix}_${index}"]`);
        const endInput = row.querySelector(`input[name="endBytePosition_${prefix}_${index}"]`);
        if (startInput && endInput) {
            calculateByteValues(row, startInput, endInput, prefix, index);
        }
    }
}

// Optimized helper to ensure API fields are updated
function syncHiddenField(prefix, fieldKey, index, value) {
    const hiddenName = `${prefix}_${fieldKey}_${index}`;
    const hiddenInput = document.getElementsByName(hiddenName)[0];
    
    if (hiddenInput) {
        // --- MULTI-ROW PROTECTION GATE ---
        if (fieldKey === 'uiDescriptionInOut') {
            const currentVal = hiddenInput.value; // What is already saved
            const newVal = String(value).trim();  // What the script is trying to save

            // If the row already has "Read" or "Write", 
            // and an automated script tries to set it to "None", "1", or "", BLOCK IT.
            const hasRealData = ["Read", "Write", "Read/Write"].includes(currentVal);
            const incomingIsBlank = ["None", "1", "", index.toString()].includes(newVal);

            if (hasRealData && incomingIsBlank) {
                // Ignore the update to keep the user's data safe
                return; 
            }
        }

        hiddenInput.value = value;
        hiddenInput.setAttribute('value', value);
}
}

// Attach UI change handler to row

function attachUIChangeHandlerForRow(row, prefix, index) {
    const uiSelect = row.querySelector(`select[name="uiType_${prefix}_${index}"]`);
    
    if (uiSelect) {
        uiSelect.addEventListener('change', function() {
            // 1. Get the current value from the hidden field IMMEDIATELY
            const hiddenInput = document.querySelector(`input[name="${prefix}_uiDescriptionInOut_${index}"]`);
            const currentValue = hiddenInput ? hiddenInput.value : "";

            // 2. Pass that value into the UI generator
            applyUIAutoFill(this, prefix, index, currentValue);
        });
    }
}
// Attach field description handler
function attachFieldDescriptionHandler(row, prefix, index) {
    if (!row) return;
    const fieldDescSelect = row.querySelector(`select[name="fieldDescription_${prefix}_${index}"]`);
    if (fieldDescSelect) {
        fieldDescSelect.setAttribute('onchange', `handleFieldDescriptionChange(this, '${prefix}', ${index})`);
        if (fieldDescSelect.value === 'Terminator') {
            handleFieldDescriptionChange(fieldDescSelect, prefix, index);
        }
    }
}

// Attach UI change validation handler
function attachUIChangeValidationHandler(row, prefix, index) {
    if (!row) return;
    const uiSelect = row.querySelector(`select[name="uiType_${prefix}_${index}"]`);
    const asperUiInput = row.querySelector(`input[name="asperui_${prefix}_${index}"]`);
    const uiValuesInput = row.querySelector(`input[name="asperuivalue_${prefix}_${index}"]`);
    const uiSerialInput = row.querySelector(`input[name="sno_asperui_${prefix}_${index}"]`);
    
    if (uiSelect && asperUiInput && uiValuesInput && uiSerialInput) {
        uiSelect.addEventListener('change', function() {
            const normalized = (this.value || '').toString().trim().toLowerCase();
            if (normalized === 'drop down' || normalized === 'notification') {
                asperUiInput.required = true;
                uiValuesInput.required = true;
                uiSerialInput.required = true;
            } else {
                asperUiInput.required = false;
                uiValuesInput.required = false;
                uiSerialInput.required = false;
            }
        });
    }
}

// ======================================================
// TABLE MANAGEMENT FUNCTIONS
// ======================================================
function addRow(type) {
    console.log(`[TABLE] Adding row to ${type} table`);
    
    const tableConfigs = {
        'cmd': { tableId: 'additionalTableBody', prefix: 'cmd' },
        'resp': { tableId: 'responseTableBody', prefix: 'resp' },
        'error': { tableId: 'errorTableBody', prefix: 'error' }
    };
    
    const config = tableConfigs[type];
    if (!config) return;
    
    const tableBody = document.getElementById(config.tableId);
    if (!tableBody) return;
    const rowCount = tableBody.rows.length;
    const newRow = tableBody.insertRow();
    
    newRow.innerHTML = getTableRowHTML(rowCount + 1, config.prefix);
    
    // Setup auto-calculation for the new row
    setupByteCalculation(newRow, config.prefix, rowCount + 1);
    
    // Attach UI onchange handler and run initial fill if needed
    attachUIChangeHandlerForRow(newRow, config.prefix, rowCount + 1);
    attachFieldDescriptionHandler(newRow, config.prefix, rowCount + 1);
    attachUIChangeValidationHandler(newRow, config.prefix, rowCount + 1);
    attachFieldDescriptionChangeHandler(newRow, config.prefix, rowCount + 1); // NEW: Add field description change handler
    
    updateSerialNumbers(config.tableId);
    updateMoveButtons(config.tableId);

    // Update frame counts
    updateFrameCountsForPrefix(config.prefix);
    
    console.log(`[TABLE] Added row ${rowCount + 1} to ${type} table`);
}



/*function removeRow(button, type) {
    const row = button.closest('tr');
    if (!row) return;
    const tableBody = row.parentNode;
    row.remove();
    
    const tableId = tableBody ? tableBody.id : null;
    if (tableId) {
        updateSerialNumbers(tableId);
        updateMoveButtons(tableId);

        const prefix = tableId === 'additionalTableBody' ? 'cmd' : 
                      (tableId === 'responseTableBody' ? 'resp' : 'error');
        updateSerialNumbers(tableBodyId);
        updateFrameCountsForPrefix(prefix);
    }
}*/

function removeRow(button, type) {
    console.log(`[TABLE] Removing row from ${type} table`);
    
    const row = button.closest('tr');
    if (!row) return;
    
    const tableConfigs = {
        'cmd': { tableId: 'additionalTableBody', prefix: 'cmd', countId: 'cmd_framesizeinbytes' },
        'resp': { tableId: 'responseTableBody', prefix: 'resp', countId: 'resp_framesizeinbytes' },
        'error': { tableId: 'errorTableBody', prefix: 'error', countId: 'error_framesizeinbytes' }
    };
    
    const config = tableConfigs[type];
    
    // 1. Remove all hidden fields in this row
    const hiddenInputs = row.querySelectorAll('input[type="hidden"]');
    hiddenInputs.forEach(hidden => hidden.remove());
    
    // 2. Remove the row from DOM
    row.remove();
    
    if (config) {
        // 3. Re-index remaining rows so S.No and Names are correct
        updateSerialNumbers(config.tableId);
        
        // 4. Update the actual Input Value (Force decrease)
        const tableBody = document.getElementById(config.tableId);
        const currentCount = tableBody.rows.length;
        
        const countInput = document.getElementById(config.countId);
        const noOfRowsInput = document.getElementById(type + '_noofrows');

        if (countInput) countInput.value = currentCount;
        if (noOfRowsInput) noOfRowsInput.value = currentCount;

        // 5. Run your existing count update logic
        updateFrameCountsForPrefix(type);
    }
    
    console.log(`[TABLE] Row removed. New count for ${type}: ${document.getElementById(config.tableId).rows.length}`);
}
function clearTable(type) {
    const tableConfigs = {
        'cmd': { tableId: 'additionalTableBody' },
        'resp': { tableId: 'responseTableBody' },
        'error': { tableId: 'errorTableBody' }
    };
    
    const config = tableConfigs[type];
    if (!config) return;
    
    const tableBody = document.getElementById(config.tableId);
    if (!tableBody) return;
    tableBody.innerHTML = '';

    updateFrameCountsForPrefix(config.tableId === 'additionalTableBody' ? 'cmd' : 
                              (config.tableId === 'responseTableBody' ? 'resp' : 'error'));
}


function updateSerialNumbers(tableBodyId) {
    const tableBody = document.getElementById(tableBodyId);
    if (!tableBody) return;
    
    const prefix = tableBodyId.includes('additional') ? 'cmd' : 
                   tableBodyId.includes('response') ? 'resp' : 'error';
    
    const rows = tableBody.rows;
    for (let i = 0; i < rows.length; i++) {
        const serialNumber = i + 1;
        const row = rows[i];
        
        // CHECK: Is this a newly inserted row?
        const isNew = row.getAttribute('data-is-new') === 'true';
        
        // 1. Update visual S.No (Column 0)
        if (row.cells && row.cells[0]) {
            row.cells[0].innerText = serialNumber;
        }
        
        // 2. Update UI ID (Toggle Logic)
        const uiIdInput = row.querySelector('.ui-id-field');
        if (uiIdInput) {
            const userChoice = uiIdInput.getAttribute('data-user-choice');
            if (userChoice === 'none') {
                uiIdInput.value = "None";
            } else {
                uiIdInput.value = serialNumber;
            }
            uiIdInput.setAttribute('onclick', `this.value = (this.value === 'None' ? ${serialNumber} : 'None'); this.setAttribute('data-user-choice', this.value === 'None' ? 'none' : 'id');`);
        }
        
        // 3. Rename attributes (Required for form submission)
        // We ALWAYS do this so the names like field_1, field_2 stay sequential
        if (typeof updateRowIndex === 'function') {
            updateRowIndex(row, prefix, serialNumber);
        }
        
        // 4. Re-bind handlers ONLY if it's the brand-new row
        // This prevents the "deleting" issue on existing rows
        if (isNew) {
            if (typeof setupByteCalculation === 'function') setupByteCalculation(row, prefix, serialNumber);
            if (typeof attachUIChangeHandlerForRow === 'function') attachUIChangeHandlerForRow(row, prefix, serialNumber);
            if (typeof attachFieldDescriptionHandler === 'function') attachFieldDescriptionHandler(row, prefix, serialNumber);
            if (typeof attachUIChangeValidationHandler === 'function') attachUIChangeValidationHandler(row, prefix, serialNumber);
            if (typeof attachFieldDescriptionChangeHandler === 'function') attachFieldDescriptionChangeHandler(row, prefix, serialNumber);
            
            // Remove the tag so it's treated as an "existing" row next time
            row.removeAttribute('data-is-new');
        }
    }

    if (typeof updateFrameCountsForPrefix === 'function') {
        updateFrameCountsForPrefix(prefix);
    }
}    


function updateRowIndex(row, prefix, newIndex) {
    if (!row) return;

    const inputs = row.querySelectorAll('input, select, textarea');
    const visibleData = {};

    // 1. CAPTURE data using the suffix-agnostic key
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        if (name && name.includes(`_${prefix}_`)) {
            // If name is "uiDescription_resp_1", key becomes "uidescription"
            const key = name.split(`_${prefix}_`)[0].toLowerCase(); 
            visibleData[key] = input.value;
        }
    });

    // 2. UPDATE Names and IDs
    inputs.forEach(input => {
        const oldName = input.getAttribute('name');
        if (!oldName) return;

        // Correctly replace the index at the end (e.g., name_1 to name_2)
        const newName = oldName.replace(/_\d+$/, `_${newIndex}`);
        input.setAttribute('name', newName);
        input.id = newName;

        // 3. SYNC values safely
        if (newName.startsWith(prefix)) {
            const fieldKey = newName.split('_')[1].toLowerCase();
            
            let foundValue = "";
            if (fieldKey === "uidescriptioninout") {
                foundValue = visibleData["uidescriptioninout"] || visibleData["uiinout"] || visibleData["uidescription"];
            } else if (fieldKey === "sliderstepvalue") {
                foundValue = visibleData["sliderstepvalue"] || visibleData["sliderstep"];
            } else {
                foundValue = visibleData[fieldKey];
            }

            // ONLY overwrite if we actually found data in visibleData
            // This prevents the "auto-delete" behavior
            if (foundValue !== undefined && foundValue !== null) {
                input.value = foundValue;
                input.setAttribute('value', foundValue);
            }
        } else {
            // For non-prefix fields (visible fields), just ensure the attribute matches the current value
            input.setAttribute('value', input.value);
        }
    });
}    

function removeRow(button, prefix) {
    const row = button.closest('tr');
    const tableBody = row.parentNode;
    const tableBodyId = tableBody.id;
    row.remove();
    updateSerialNumbers(tableBodyId);
}
function insertRowBelow(button, prefix) {
    const row = button.closest("tr");
    const tableBody = row.parentNode;
    const tableId = tableBody.id;
    const rows = Array.from(tableBody.rows);
    const rowIndex = rows.indexOf(row);

    const allStates = snapshotAllRows(tableId);

    const newRow = tableBody.insertRow(rowIndex + 1);
    // TAG THIS ROW AS NEW
    newRow.setAttribute('data-is-new', 'true'); 
    
    const nextIndex = rowIndex + 2;
    newRow.innerHTML = getTableRowHTML(nextIndex, prefix);

    restoreAllRows(tableId, allStates, rowIndex + 1);
    updateSerialNumbers(tableId);
}
function restoreAllRows(tableBodyId, states, insertIndex) {
    const tableBody = document.getElementById(tableBodyId);
    let stateIdx = 0;

    Array.from(tableBody.rows).forEach((row, currentRowIdx) => {
        // Skip the newly inserted row - it should stay empty/default
        if (currentRowIdx === insertIndex) return;

        const rowData = states[stateIdx++];
        if (!rowData) return;

        const inputs = row.querySelectorAll('input, select, textarea');
        inputs.forEach((el, inputIdx) => {
            // Restore by the order of inputs rather than the name attribute
            // because the name attribute was just changed by updateSerialNumbers
            if (rowData[inputIdx] !== undefined) {
                el.value = rowData[inputIdx];
            }
        });
    });
}
function snapshotAllRows(tableBodyId) {
    const tableBody = document.getElementById(tableBodyId);
    return Array.from(tableBody.rows).map(row => {
        return Array.from(row.querySelectorAll('input, select, textarea')).map(el => el.value);
    });
}

function resetRowToDefault(row) {
    row.querySelectorAll("input, select").forEach(el => {
        if (el.type === "hidden") return;
        if (el.classList.contains("ui-id-field")) return;

        // Reset value
        if (el.tagName === "SELECT") {
            el.selectedIndex = 0;
        } else {
            el.value = "";
        }

        // Reset disabled state
        el.disabled = false;
    });
}


// ======================================================
// TABLE ROW HTML GENERATOR
// ======================================================
function getTableRowHTML(index, prefix) {
    return `
        <tr>
            <td class="sno-column">${index}</td>

            <!-- Start Byte -->
            <td>
                <input type="number"
                       name="startBytePosition_${prefix}_${index}"
                       class="compact-input"
                       min="0"
                       oninput="calculateByteValues(this.closest('tr'), this,
                                this.closest('tr').querySelector('input[name=\\'endBytePosition_${prefix}_${index}\\']'),
                                '${prefix}', ${index})">
            </td>

            <!-- End Byte -->
            <td>
                <input type="number"
                       name="endBytePosition_${prefix}_${index}"
                       class="compact-input"
                       min="0"
                       oninput="calculateByteValues(this.closest('tr'),
                                this.closest('tr').querySelector('input[name=\\'startBytePosition_${prefix}_${index}\\']'),
                                this,
                                '${prefix}', ${index})">
            </td>

            <!-- Field Description + Bitwise -->
            <td>
                <div class="field-desc-inline">

                    <select name="fieldDescription_${prefix}_${index}"
                            class="compact-select field-description"
                            onchange="handleFieldDescriptionChange(this, '${prefix}', ${index})">
                        <option value="">Select</option>
                        <option value="Header">Header</option>
                        <option value="Data">Data</option>
                        <option value="Payload">Payload</option>
                        <option value="Checksum">Checksum</option>
                        <option value="Terminator">Terminator</option>
                        <option value="Command ID">Command ID</option>
                        <option value="Status">Status</option>
                        <option value="Error Code">Error Code</option>
                        <option value="Length">Length</option>
                        <option value="Version">Version</option>
                        <option value="Reserved">Reserved</option>
                        <option value="Timestamp">Timestamp</option>
                        <option value="Device ID">Device ID</option>
                    </select>

                    <select class="compact-select bit-byte-select"
                            name="bitByte_${prefix}_${index}"
                            style="display:none"
                            onchange="handleBitByteChange(this, '${prefix}', ${index})">
                        <option value="Bytewise">Bytewise</option>
                        <option value="Bitwise">Bitwise</option>
                    </select>

                    <button type="button"
                            class="arrow-btn"
                            style="display:none"
                            onclick="openBitwiseConfig(this.closest('tr'), '${prefix}', ${index})">
                        ▶
                    </button>
                </div>
            </td>

            <!-- No of Bytes -->
            <td>
                <input type="number"
                       name="noOfBytes_${prefix}_${index}"
                       class="compact-input auto-calculated"
                       readonly>
            </td>

            <!-- Bit Size -->
            <td>
                <input type="number"
                       name="bitSize_${prefix}_${index}"
                       class="compact-input auto-calculated"
                       readonly>
            </td>
        
    

    

    



    


        <!-- Data Type -->
        <td>
            <select name="dataType_${prefix}_${index}" class="compact-select">
                <option value="">Select</option>
                <option value="UnsignedInt">Unsigned Integer</option>
                <option value="SignedInt">Signed Integer</option>
                <option value="Float">Float</option>
                <option value="Double">Double</option>
                <option value="Binary">Binary</option>
                <option value="ASCII">ASCII</option>
                <option value="String">String</option>
                <option value="Boolean">Boolean</option>
                <option value="Hex">Hex</option>
                <option value="Enum">Enum</option>
                <option value="Timestamp">Timestamp</option>
                <option value="None">None</option>
            </select>
        </td>

        <!-- Value Definition -->
        <td>
            <select name="valueDefinition_${prefix}_${index}" class="compact-select">
                <option value="">Select</option>
                <option value="Fixed Value">Fixed Value</option>
                <option value="Range Value">Range Value</option>
                <option value="Dynamic Value">Dynamic Value</option>
                <option value="Enum Value">Enum Value</option>
                <option value="Calculated Value">Calculated Value</option>
                <option value="None">None</option>
            </select>
        </td>

        <!-- Display Min (auto-calculated) -->
        <td><input type="number" name="displayMin_${prefix}_${index}" class="compact-input auto-calculated" readonly></td>
        
        <!-- Display Max (auto-calculated) -->
        <td><input type="number" name="displayMax_${prefix}_${index}" class="compact-input auto-calculated" readonly></td>

       <!-- Hex Min -->
        <td><input type="text" name="hexMin_${prefix}_${index}" class="compact-input"></td>
        
        <!-- Hex Max -->
        <td><input type="text" name="hexMax_${prefix}_${index}" id="hexMax_${prefix}_${index}" class="compact-input" readonly></td>
        <!-- Hex Min -->


        <!-- UI Type -->
        <td>
            <select name="uiType_${prefix}_${index}" class="compact-select" onchange="handleUITypeChange(this, '${prefix}', ${index})">
                <option value="">Select</option>
                <option value="None">None</option>
                <option value="Text">Text</option>
                <option value="Toggle Switch">Toggle Switch</option>
                <option value="Notification">Notification</option>
                <option value="Console">Console</option>
                <option value="Enter Text">Enter Text</option>
                <option value="Slider">Slider</option>
                <option value="Brightness Slider">Brightness Slider</option>
                <option value="Airflow Slider">Airflow Slider</option>
                <option value="CCT Slider">CCT Slider</option>
                <option value="Saturation Slider">Saturation Slider</option>
                <option value="Hue Slider">Hue Slider</option>
                <option value="Hue Slider Custom">Hue Slider Custom</option>
                <option value="Hue Circle">Hue Circle</option>
                <option value="Push Button">Push Button (Big)</option>
                <option value="Drop Down">Drop Down</option>
                <option value="Trigger">Trigger</option>
                <option value="Display">Display</option>
                <option value="Button">Button (Big)</option>
                <option value="Auto Trigger With Timer">Auto Trigger With Timer</option>
                <option value="Toast Display">Toast Display</option>
            </select>
        </td>
     

        <!-- UI ID -->
        <td>
    <input type="text" 
           name="uiId_${prefix}_${index}" 
           id="uiId_${prefix}_${index}" 
           class="compact-input ui-id-field" 
           value="${index}" 
           readonly 
           style="cursor: pointer; text-align: center;" se
           onclick="this.value = (this.value === 'None' ? ${index} : 'None'); this.setAttribute('data-user-choice', this.value === 'None' ? 'none' : 'id');">
</td>              

        <!-- AS PER UI -->
        <td><input type="text" name="asperui_${prefix}_${index}" class="compact-input"></td>
        
        <!-- UI Serial No -->
        <td><input type="text" name="sno_asperui_${prefix}_${index}" class="compact-input"></td>
        
        <!-- UI Values -->
        <td><input type="text" name="asperuivalue_${prefix}_${index}" class="compact-input"></td>

        <!-- UI Page No -->
        <td><input type="text" name="uiPageNo_${prefix}_${index}" class="compact-input"></td>
        
        <!-- UI In/Out -->
        <td>
<select name="uiInOut_${prefix}_${index}" id="uiInOut_${prefix}_${index}" class="compact-input">
        <option value="NONE">NONE</option>
        <option value="Read">Read</option>
        <option value="Write">Write</option>
        <option value="Read/Write">Read/Write</option>
    </select>
</td>


        <!-- Slider Step -->
        <td><input type="number" name="sliderStep_${prefix}_${index}" class="compact-input" min="1"></td>
        
        <!-- Default Value -->
        <td><input type="text" name="defaultValue_${prefix}_${index}" class="compact-input"></td>

        <!-- Developer Notes -->
        <td><input type="text" name="developerNotes_${prefix}_${index}" class="compact-input"></td>
         <!--  Enable -->
       
<td>
    <input type="text" 
           name="enable_${prefix}_${index}" 
           list="enableOptions_${prefix}_${index}" 
           class="compact-input" 
           placeholder="No" 
           onclick="this.value=''" 
           onchange="handleEnableChange(this, '${prefix}', ${index})">
    <datalist id="enableOptions_${prefix}_${index}">
        <option value="No">
        <option value="Yes">
    </datalist>
</td>



    <!-- Message -->
<td>
    <input type="text" 
           name="message_${prefix}_${index}" 
           class="compact-input validate-field" 
           placeholder="Message..." 
           disabled>
</td>
         
   <!-- Action Buttons -->
        <td class="action-buttons">
            <button type="button" class="remove-btn" onclick="removeRow(this, '${prefix}')">Remove</button>
            <button type="button" class="add-btn" onclick="insertRowBelow(this, '${prefix}')">ADD+</button>
        </td>


        
        
   <!-- Hidden fields for server submission -->
<td class="hidden-container" style="display:none">
    <input type="hidden" name="${prefix}_startBytePosition_${index}" value="">
    <input type="hidden" name="${prefix}_endBytePosition_${index}" value="">
    <input type="hidden" name="${prefix}_numberOfBytes_${index}" value="">
    <input type="hidden" name="${prefix}_bitSize_${index}" value="">
    <input type="hidden" name="${prefix}_displayValueMin_${index}" value="">
    <input type="hidden" name="${prefix}_displayValueMax_${index}" value="">
    <input type="hidden" name="${prefix}_valueInHexMin_${index}" value="">
    <input type="hidden" name="${prefix}_valueInHexMax_${index}" value="">
    <input type="hidden" name="${prefix}_fieldDescription_${index}" value="">
    <input type="hidden" name="${prefix}_dataType_${index}" value="">
    <input type="hidden" name="${prefix}_valueDefinition_${index}" value="">
    <input type="hidden" name="${prefix}_uiType_${index}" value="">
    <input type="hidden" name="${prefix}_uiId_${index}" value="">
    <input type="hidden" name="${prefix}_asperui_${index}" value="">
    <input type="hidden" name="${prefix}_sno_asperui_${index}" value="">
    <input type="hidden" name="${prefix}_asperuivalue_${index}" value="">
    <input type="hidden" name="${prefix}_uiPageNo_${index}" value="">
    <input type="hidden" name="${prefix}_uiDescriptionInOut_${index}" value="">
    <input type="hidden" name="${prefix}_sliderStepValue_${index}" value="">
    <input type="hidden" name="${prefix}_defaultvalue_${index}" value="">
    <input type="hidden" name="${prefix}_developerNotes_${index}" value="">
</td>
</tr>


     `;
}




// ======================================================
// VALIDATION FUNCTIONS
// ======================================================
function validateStep(step,e) {
    let isValid = false;
    const errorDiv = document.getElementById(`step${step}-error`);
    if (errorDiv) errorDiv.style.display = "none";

    // --- STEP 1: General Info ---
  
   if (step === 1) {
        isValid = validateStep1();
        if (!isValid) {
            forceMessage("❌ Please fill the required Step 1 fields.");
        }
        return isValid;
    }

    // --- STEPS 2, 3, & 4: Frame Logic ---
    const prefix = (step === 2) ? 'cmd' : (step === 3 ? 'resp' : 'error');
    const direction = document.getElementById(prefix + '_data_flow_direction'); 
    const type = document.getElementById(prefix + '_frametype');
    const trigger = document.getElementById(prefix + '_frametrigger');

    let essentialValid = true;
    let missing = [];

    // Check Dropdowns
    if (direction && (!direction.value || direction.value.toLowerCase().includes("select"))) {
        direction.style.border = "2px solid red";
        missing.push("Data Flow Direction");
        essentialValid = false;
    }
    if (type && (!type.value || type.value.toLowerCase().includes("select"))) {
        type.style.border = "2px solid red";
        missing.push("Frame Type");
        essentialValid = false;
    }
    if (trigger && (!trigger.value || trigger.value.toLowerCase().includes("select"))) {
        trigger.style.border = "2px solid red";
        missing.push("Frame Trigger");
        essentialValid = false;
    }

    // If dropdowns are missing, show message and stop
    if (!essentialValid) {
        const msg = "❌ Please fill the following: " + missing.join(", ");
        if (errorDiv) {
            errorDiv.innerText = msg;
            errorDiv.style.display = "block";
        }
        forceMessage(msg); // Custom message only
        return false; 
    }

    // --- Specific Step Logic (Tables) ---
    switch(step) {
        case 2: isValid = validateStep2(); break;
        case 3: isValid = validateStep3(); break;
        case 4: isValid = validateStep4(); break;
        default: isValid = true;
    }
          // If table validation failed, validateTableStep already calls forceMessage.
    if (isValid) {
        console.log(`%c ✅ Step ${step} Validated.`, "color: green; font-weight: bold;");
        
        if (step === 2) {
            saveCommandConfiguration(); 
        } else if (step === 3) {
            saveResponseConfiguration();
        } else if (step === 4) {
            // --- CRITICAL CHANGE START ---
            
            console.log("%c STOP: API Prevented. Checking Console...", "background: orange; color: black;");
            
            saveErrorConfiguration(); // Pass the event to the logger
            
            return false; // Force the browser to stay on this page
            // --- CRITICAL CHANGE END ---
        }
    }
    return isValid;
}

 // Fixed Table Validator (Added forceMessage, Removed alert)
function validateTableStep(tableBodyId, errorId, isMandatory,prefix) {
    var isValid = true;
    var errorElement = document.getElementById(errorId);
    var rows = document.querySelectorAll('#' + tableBodyId + ' tr');

    if (rows.length === 0) return isMandatory ? false : true;

    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        row.classList.remove("pink-highlight"); 
        var fields = row.querySelectorAll('input:not([name*="developerNotes"]), select');
        var hasErrorInRow = false;

        for (var j = 0; j < fields.length; j++) {
            var field = fields[j];
            if (!field.value || field.value.trim() === "" || field.value === "Error") {
                field.style.border = "2px solid red";
                hasErrorInRow = true;
                isValid = false;
            } else {
                field.style.border = "1px solid #ccc";
            }
        }
        if (hasErrorInRow) row.classList.add("pink-highlight");
    }

    if (!isValid) {
        
        const msg = "❌ Please fill all required fields in the table.";
        if (errorElement) {
            errorElement.innerText = msg;
            errorElement.style.display = "block";
        }
        forceMessage(msg); // <--- Triggers the red box
    }
    return isValid;
}


function validateStep1() {
    let isValid = true;
    const errors = [];
    const fields = [
        { id: 'par_parametername', name: 'Parameter Name' },
        { id: 'pagenames', name: 'Page Names' },
        { id: 'numberofpages', name: 'Page Number' },
        { id: 'Sequence', name: 'Sequence Number' }
    ];

    fields.forEach(field => {
        const el = document.getElementById(field.id);
        if (el) {
            if (!el.value.trim()) {
                errors.push(`${field.name} is required`);
                el.classList.add('error');
                isValid = false;
            } else if (field.id === 'pagenames' && el.value === "Page0") {
                errors.push('Page0 as Page name is invalid');
                el.classList.add('error');
                isValid = false;
            } else {
                el.classList.remove('error');
            }
        }
    });
    
    if (!isValid) {
        const errorElement = document.getElementById('step1-error');
        const msg = "❌ Please fill the required Step 1 fields.";
        if (errorElement) {
            errorElement.innerHTML = errors.map(err => `<div>• ${err}</div>`).join('');
            errorElement.style.display = "block";
        }
        alert(msg); 
        forceMessage(msg); 
    }
    return isValid;
}

function validateStep2() {
    const frameSize = document.getElementById('cmd_framesizeinbytes');
    if (frameSize) {
        if (!frameSize.value || parseInt(frameSize.value) <= 0) {
            const msg = "❌ Frame Size in Bytes is mandatory for the Command Frame.";
            const err = document.getElementById('step2-error');
            if (err) {
                err.innerText = msg;
                err.style.display = "block";
            }
            alert(msg);
            forceMessage(msg);
            frameSize.classList.add('error');
            return false;
        }
        frameSize.classList.remove('error');
    }
    const isTableValid = validateTableStep("additionalTableBody", "step2-error", true);
    if (!isTableValid) return false;
    // 2. New Duplicate Check
    return validateDuplicateByteRange("additionalTableBody", "cmd", "step2-error");
 
    
    return validateTableStep("additionalTableBody", "step2-error", true);

    }

   // Your Force Message UI Function
function forceMessage(msg) {
    let box = document.getElementById("customAlert");
    if (!box) {
        box = document.createElement("div");
        box.id = "customAlert";
        box.style.cssText = `
            position: fixed;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -50%);
             background: #FFF5F5;          /* Very light red/white */
            color: #C53030;               /* Deep red text */
            border: 1px solid #FC8181;    /* Soft red border */          
             padding: 20px 40px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            z-index: 999999;
            font-family: sans-serif;
            font-weight: bold;
            text-align: center;
        `;
        document.body.appendChild(box);
    }
    box.innerText = msg;
    box.style.display = "block";
    setTimeout(() => { box.style.display = "none"; }, 3000);
}

    // Keep Step 3 and 4 as they call validateTableStep which now has the messages.

function validateStep3() { 
    // 1. Original Table Validation
    const isTableValid = validateTableStep("responseTableBody", "step3-error", false); 
    if (!isTableValid) return false;

    // 2. New Duplicate Check
    return validateDuplicateByteRange("responseTableBody", "resp", "step3-error");
}
function validateStep4() { 
    // 1. Original Table Validation
    const isTableValid = validateTableStep("errorTableBody", "step4-error", false); 
    if (!isTableValid) return false;

    // 2. New Duplicate Check
    return validateDuplicateByteRange("errorTableBody", "error", "step4-error");
}
// ======================================================
// OTHER FUNCTIONS
// ======================================================
function syncParameterNameToAllSteps() {
    const mainInput = document.getElementById("par_parametername");
    const targetIDs = ["com_parametername", "resp_parametername", "error_parametername"];

    if (!mainInput) return;

    mainInput.addEventListener("input", function() {
        const value = mainInput.value;
        targetIDs.forEach(id => {
            const input = document.getElementById(id);
            if (input) input.value = value;
        });
        
        // NEW: Also update Default Value in all rows where Field Description is "Data"
        updateDefaultValueForAllDataRows(value);
    });

    const initialValue = mainInput.value;
    if (initialValue) {
        targetIDs.forEach(id => {
            const input = document.getElementById(id);
            if (input) input.value = initialValue;
        });
        
        // NEW: Update Default Value in all rows where Field Description is "Data"
        updateDefaultValueForAllDataRows(initialValue);
    }
}

// NEW: Update Default Value in all rows where Field Description is "Data"
function updateDefaultValueForAllDataRows(parameterName) {
    const tables = [
        {id:'additionalTableBody', prefix:'cmd'}, 
        {id:'responseTableBody', prefix:'resp'}, 
        {id:'errorTableBody', prefix:'error'}
    ];
    
    tables.forEach(t => {
        const tbody = document.getElementById(t.id);
        if (!tbody) return;
        
        Array.from(tbody.rows).forEach((row, idx) => {
            const fieldDescSelect = row.querySelector(`select[name="fieldDescription_${t.prefix}_${idx + 1}"]`);
            if (fieldDescSelect && fieldDescSelect.value === 'Data') {
                const defaultValueInput = row.querySelector(`input[name="defaultValue_${t.prefix}_${idx + 1}"]`);
                if (defaultValueInput) {
                    defaultValueInput.value = parameterName;
                    defaultValueInput.classList.add('auto-calculated');
                    defaultValueInput.readOnly = true;
                    
                    // Also update hidden field
                    const hiddenField = row.querySelector(`input[name="${t.prefix}_defaultvalue_${idx + 1}"]`);
                    if (hiddenField) {
                        hiddenField.value = parameterName;
                    }
                }
            }
        });
    });
}

function syncParameterNameToAll(value) {
    const targetIDs = ["com_parametername", "resp_parametername", "error_parametername"];
    targetIDs.forEach(id => {
        const input = document.getElementById(id);
        if (input) input.value = value;
    });
    
    // NEW: Also update Default Value in all rows where Field Description is "Data"
    updateDefaultValueForAllDataRows(value);
}

function validateParamLive() {
    const input = document.getElementById("par_parametername");
    const hidden = document.getElementById("default-param-list");
    const dupError = document.getElementById("duplicate-error");
    const charError = document.getElementById("char-error");

    if (!input || !hidden || !dupError || !charError) return;

    let paramList = [];
    try {
        paramList = JSON.parse(hidden.value || "[]");
    } catch (e) {
        console.error("JSON Parse Error:", e);
    }

    const entered = input.value.trim();
    const enteredLower = entered.toLowerCase();
    const exists = paramList.some(p => p.toLowerCase() === enteredLower);

    const invalidChar = /[^a-zA-Z0-9_ ]/.test(entered);

    if (entered && exists) {
        dupError.style.display = "block";
        input.style.border = "1px solid red";
    } else {
        dupError.style.display = "none";
    }

    if (entered && invalidChar) {
        charError.style.display = "block";
        input.style.border = "1px solid red";
    } else {
        charError.style.display = "none";
    }

    if (!exists && !invalidChar) {
        input.style.border = "";
    }
}


function validatePageName() {
    const input = document.getElementById("pagenames");
    if (input.value === "Page0") {
        alert("Page0 as Page name is invalid");
        return false;
    }
    return true;
}

function checkWordCount(textarea) {
    const maxWords = 300;
    const wordCount = textarea.value.trim().split(/\s+/).length;

    if (wordCount > maxWords) {
        const words = textarea.value.trim().split(/\s+/);
        textarea.value = words.slice(0, maxWords).join(' ');
        alert('Maximum word limit exceeded. Content has been truncated.');
    }
}

// ======================================================
// FORM SUBMISSION VALIDATION
// ======================================================
const formEl = document.getElementById('file-form');
if (formEl) {
    formEl.addEventListener('submit', function(e) {
        console.log("=== FORM SUBMISSION STARTED ===");
        
        // Validate model name
        const modelNameEl = document.getElementById('modelname');
        const modelName = modelNameEl ? modelNameEl.value : '';
        if (modelName.trim() === '') {
            e.preventDefault();
            alert('Please enter the Model Name.');
            return;
        }

        let validationErrors = [];

        // Validate slider steps for slider UI types
        const uiSelects = document.querySelectorAll('select[name^="uiType_"]');
        for (let sel of uiSelects) {
            const uiValue = (sel.value || '').toLowerCase();
            
            // Slider validation
            if (uiValue.indexOf('slider') !== -1) {
                const name = sel.getAttribute('name');
                const parts = name.split('_');
                const prefix = parts[1];
                const index = parts[2];
                const sliderInput = document.querySelector(`input[name="sliderStep_${prefix}_${index}"]`);
                if (!sliderInput || !String(sliderInput.value).trim()) {
                    validationErrors.push(`Please enter Slider Step for row ${index} in ${prefix.toUpperCase()} table`);
                    if (sliderInput) sliderInput.style.border = '2px solid red';
                }
            }
            
            // Drop Down and Notification validation
            if (uiValue === 'drop down' || uiValue === 'notification') {
                const name = sel.getAttribute('name');
                const parts = name.split('_');
                const prefix = parts[1];
                const index = parts[2];
                
                const asperUiInput = document.querySelector(`input[name="asperui_${prefix}_${index}"]`);
                const uiValuesInput = document.querySelector(`input[name="asperuivalue_${prefix}_${index}"]`);
                const uiSerialInput = document.querySelector(`input[name="sno_asperui_${prefix}_${index}"]`);
                
                if (!asperUiInput || !String(asperUiInput.value).trim()) {
                    validationErrors.push(`Please enter AS PER UI for row ${index} in ${prefix.toUpperCase()} table (required for ${uiValue})`);
                    if (asperUiInput) asperUiInput.style.border = '2px solid red';
                }
                
                if (!uiValuesInput || !String(uiValuesInput.value).trim()) {
                    validationErrors.push(`Please enter UI VALUES for row ${index} in ${prefix.toUpperCase()} table (required for ${uiValue})`);
                    if (uiValuesInput) uiValuesInput.style.border = '2px solid red';
                }
                
                if (!uiSerialInput || !String(uiSerialInput.value).trim()) {
                    validationErrors.push(`Please enter UI SERIAL NO for row ${index} in ${prefix.toUpperCase()} table (required for ${uiValue})`);
                    if (uiSerialInput) uiSerialInput.style.border = '2px solid red';
                }
            }
        }

        if (validationErrors.length > 0) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + validationErrors.join('\n'));
            return;
        }

        console.log("=== FORM SUBMITTED SUCCESSFULLY ===");
       e.preventDefault(); 
        console.log("Form submission prevented so you can check the console!");
    });
}

function handleUITypeChange(selectElement, prefix, index) {
    const uiInOutSelect = document.getElementById(`uiInOut_${prefix}_${index}`);
    if (!uiInOutSelect) return;

    if (selectElement.value === "Toggle Switch") {
        // Change to the 3 options you requested
        uiInOutSelect.innerHTML = `
            <option value="NONE">NONE</option>
            <option value="Read">Read</option>
            <option value="Write">Write</option>
            <option value="Read/Write" selected>Read/Write</option>
        `;
    } else {
        // Reset to default for other UI types
        uiInOutSelect.innerHTML = `
            <option value="NONE">NONE</option>
            <option value="Read">Read</option>
            <option value="Write">Write</option>
           <option value="Read/Write">Read/Write</option>
        `;
    }
}

function calculateHexMax(prefix, index) {
    // Get the elements
    const startIn = document.querySelector(`input[name="startBytePosition_${prefix}_${index}"]`);
    const endIn = document.querySelector(`input[name="endBytePosition_${prefix}_${index}"]`);
    const hexMaxOut = document.getElementById(`hexMax_${prefix}_${index}`);

    if (startIn && endIn && hexMaxOut) {
        const start = parseInt(startIn.value);
        const end = parseInt(endIn.value);

        // Logic: Only calculate if numbers are valid and End is >= Start
        if (!isNaN(start) && !isNaN(end) && end >= start) {
            // 1. Calculate Byte Count (e.g., 0 to 2 is 3 bytes)
            const byteCount = end - start + 1;
            
            // 2. Calculate Max Decimal: (256 ^ byteCount) - 1
            // 3 bytes = (256^3) - 1 = 16,777,215
            const maxDec = Math.pow(256, byteCount) - 1;
            
            // 3. Convert to Hex String (ffffff)
            let hexRes = maxDec.toString(16).toUpperCase();
            
            // 4. Pad with '0' if the hex is too short (e.g., 0x01FFFF)
            while (hexRes.length < byteCount * 2) {
                hexRes = "0" + hexRes;
            }

            // 5. Set the value to 0xFFFFFF format
            hexMaxOut.value = "0x" + hexRes;
        } else {
            hexMaxOut.value = ""; // Clear if inputs are bad
        }
    }
}
// 1. These MUST be at the top of your file
function saveErrorFrame() {

    // 🔴 Stop save if validation fails

    if (!validateStep(4)) {

        return;

    }



    // ✅ Only submit if validation passes

    document.querySelector("form").submit();

}
//
//
/* ======================================================
   NAVIGATIONAL LOGIC (Step Switching)
   ====================================================== */

function goToNextStep(step) {
    // 1. RUN VALIDATION
    const isStepValid = validateStep(step);

    // 2. ONLY MOVE IF VALID
    // If validateStep returns false, this code won't run, and the page won't change.
    if (isStepValid === true) {
        const currentElem = document.getElementById('step-' + step);
        const nextElem = document.getElementById('step-' + (step + 1));

        if (currentElem && nextElem) {
            currentElem.style.display = 'none';
            nextElem.style.display = 'block';
            window.scrollTo(0, 0);
        }
    } else {
        console.log("Validation failed. Staying on step " + step);
    }
}
function goToPreviousStep(step) {
    if (step === 2 && currentCommandSubStep > 1) {
        currentCommandSubStep--;
        prepareCommandFrame(currentCommandSubStep);
        return; // Stay on Step 2
    }

    // Standard move back
    document.getElementById(`step-${step}`).style.display = 'none';
    currentStep = step - 1;
    document.getElementById(`step-${currentStep}`).style.display = 'block';
    updateProgressIndicator();
}


   /* ======================================================
   SAVE LOGIC (Generates the API Form View)
   ====================================================== */

function saveErrorFrame() {
    const frameSizeInput = document.getElementById('error_framesizeinbytes');
    const errorDiv = document.getElementById('step4-error');
    const form = document.getElementById('file-form'); 
    
    const frameSize = parseInt(frameSizeInput.value) || 0;

    // 1. Visual Warning for Error Frame
    if (frameSize <= 0) {
        if (errorDiv) {
            errorDiv.innerText = "Warning: Error Frame size is 0. Saving empty configuration.";
            errorDiv.style.color = "orange";
            errorDiv.style.display = "block";
        }
        if (frameSizeInput) frameSizeInput.style.border = "2px solid orange";
    } else {
        if (errorDiv) errorDiv.style.display = "none";
        if (frameSizeInput) frameSizeInput.style.border = "1px solid #ccc";
    }

    // 2. Form Submission
    // This is the part that makes it display like the "API Form" array
    if (form) {
        console.log("Submitting form to backend...");
        form.submit(); 
    } else {
        alert("Error: Form with ID 'file-form' not found. Check your HTML.");
    }
}

// Add this at the bottom of your script
['input', 'change'].forEach(evt => {
    document.addEventListener(evt, function (e) {
        const el = e.target;
        const name = el?.getAttribute('name');
        if (!name) return;

        // ==============================
        // UI IN / OUT FIELD (SPECIAL)
        // ==============================
        if (name.toLowerCase().startsWith('uiinout_')) {
            const remainder = name.replace(/uiinout_/i, '');
            const lastUnderscore = remainder.lastIndexOf('_');
            if (lastUnderscore === -1) return;

            const prefix = remainder.substring(0, lastUnderscore);
            const index = remainder.substring(lastUnderscore + 1);

            syncHiddenField(prefix, 'uiDescriptionInOut', index, el.value.trim());
            return; // 🚨 stop here, don't fall through
        }

        // ==============================
        // ALL OTHER FIELDS
        // ==============================
        if (name.includes('_cmd_') || name.includes('_resp_') || name.includes('_error_')) {
            const parts = name.split('_');
            const fieldName = parts[0];
            const prefix = parts[1];
            const index = parts[2];

            let hiddenName = `${prefix}_${fieldName}_${index}`;

            if (fieldName.toLowerCase() === "sliderstep") {
                hiddenName = `${prefix}_sliderStepValue_${index}`;
            }

            if (fieldName.toLowerCase() === "defaultvalue") {
                hiddenName = `${prefix}_defaultvalue_${index}`;
            }

            const hiddenInput = document.querySelector(`input[name="${hiddenName}"]`);
            if (hiddenInput) {
                hiddenInput.value = el.value;
                hiddenInput.setAttribute('value', el.value);
            }
        }
    });
});


function syncFrameTrigger(prefix, index) {
    // 1. Get the value from the visible UI Type dropdown
    const uiTypeSelect = document.querySelector(`select[name="uiType_${prefix}_${index}"]`);
    
    // 2. Find the hidden frame-level trigger input (e.g., cmd_frametrigger)
    const hiddenTrigger = document.getElementById(`${prefix}_frametrigger`);

    if (uiTypeSelect && hiddenTrigger) {
        hiddenTrigger.value = uiTypeSelect.value;
        hiddenTrigger.setAttribute('value', uiTypeSelect.value);
    }
}

//commond frame

let storedCommands = [];
function handleNextCommand() {
    const maxFramesField = document.getElementById('numberofpages');
    const maxFrames = parseInt(maxFramesField ? maxFramesField.value : 1) || 1;

    if (!validateDuplicateByteRange("additionalTableBody", "cmd", "step2-error")) {
        return; // Stop here if duplicates are found
    }

    // 1. SAFETY GATE
    if (commandCount >= maxFrames) {
        alert("Success: You have completed all " + maxFrames + " command frames.");
        if (typeof updateCommandBoxColors === "function") updateCommandBoxColors();
        return;
    }

    // 2. SAVE CURRENT STATE (✅ FIXED LINE)
    saveCurrentCommandState(commandCount - 1);

    // 3. INCREMENT
    commandCount++;
   currentPageIndex = commandCount - 1;
        console.log("Bytewise Count:", commandCount);
    console.log("Bitwise Page Index:", currentPageIndex);

console.log("Current commandCount:", commandCount);


    // 4. UPDATE PROGRESS
    if (typeof updateCommandBoxColors === "function") updateCommandBoxColors();

    // 5. UI TITLE
    updateCommandFrameTitle();

    const tbody = document.getElementById('additionalTableBody');

    // 6. LOAD OR RESET
    if (storedCommands[commandCount - 1]) {
        applyCommandFrameData(storedCommands[commandCount - 1]);
    } else {
        if (document.getElementById('cmd_data_flow_direction'))
            document.getElementById('cmd_data_flow_direction').value = "";

        if (document.getElementById('cmd_framesizeinbytes'))
            document.getElementById('cmd_framesizeinbytes').value = "";

        if (document.getElementById('cmd_frame_trigger_options'))
            document.getElementById('cmd_frame_trigger_options').value = "None";

        if (tbody) tbody.innerHTML = "";
        if (typeof addRow === "function") addRow('cmd');
    }

    // 7. BUTTON LABELS
    updateCommandButtonLabels(maxFrames);
    window.scrollTo(0, 0);
}
function handleBackCommand() {
    if (commandCount > 1) {
        // Save current page state into the correct index
        saveCurrentCommandState(commandCount - 1);

        // Decrement page
        commandCount--;
        console.log("Current commandCount:", commandCount);

        // Load previous saved data
        const prevSaved = storedCommands[commandCount - 1];
        if (prevSaved) {
            applyCommandFrameData(prevSaved);
        } else {
            const tbody = document.getElementById('additionalTableBody');
            document.getElementById('cmd_data_flow_direction').value = "";
            document.getElementById('cmd_framesizeinbytes').value = "";
            document.getElementById('cmd_frame_trigger_options').value = "None";
            if (tbody) tbody.innerHTML = "";
            if (typeof addRow === "function") addRow('cmd');
        }

        updateCommandFrameTitle();

        const maxFrames = parseInt(document.getElementById('numberofpages')?.value || 1);
        updateCommandButtonLabels(maxFrames);

        if (typeof updateCommandBoxColors === "function") updateCommandBoxColors();

        window.scrollTo(0, 0);
    }
}



//


//
//
function setField(tr, selectors, value) {
    const el = tr.querySelector(selectors);
    if (!el) return;

    if ("value" in el) {
        el.value = value || "";
    } else {
        el.textContent = value || "";
    }
}

function applyCommandFrameData(data) {
    if (!data) return;

    // Header
    document.getElementById('com_parametername').value = data.parameterName || "";
    document.getElementById('cmd_data_flow_direction').value = data.direction || "";
    document.getElementById('cmd_framesizeinbytes').value = data.size || "";
    document.getElementById('cmd_frame_trigger_options').value = data.triggerOptions || "";

    const tbody = document.getElementById('additionalTableBody');
    tbody.innerHTML = "";

    data.rows.forEach((rowData, index) => {   // <-- index ADDED
        addRow('cmd');
        const tr = tbody.lastElementChild;

        // ===== 1. Restore Field Description & trigger UI =====
        const descEl = tr.querySelector('[name^="fieldDescription_"]');
        if (descEl) {
            descEl.value = rowData.cmd_fieldDescription || "";
            handleFieldDescriptionChange(descEl, 'cmd', index);
        }

        // ===== 2. Restore Bit/Byte & trigger UI =====
        const bitByteEl = tr.querySelector('[name^="bitByte_"]');
        if (bitByteEl && rowData.cmd_bitwiseMode) {
            bitByteEl.value = rowData.cmd_bitwiseMode;
            handleBitByteChange(bitByteEl, 'cmd', index);
        }

        // ===== 3. Restore normal fields =====
        setField(tr, '[name*="startByte"]', rowData.cmd_startBytePosition);
        setField(tr, '[name*="endByte"]', rowData.cmd_endBytePosition);
        setField(tr, '[name*="noOfBytes"]', rowData.cmd_numberOfBytes);
        setField(tr, '[name*="bitSize"]', rowData.cmd_bitSize);

        setField(tr, '[name*="dataType"]', rowData.cmd_dataType);
        setField(tr, '[name*="valueDefinition"]', rowData.cmd_valueDefinition);
        setField(tr, '[name*="displayMin_"]', rowData.cmd_displayValueMin);
    setField(tr, '[name*="displayMax_"]', rowData.cmd_displayValueMax);        

         setField(tr, '[name*="hexMin"]', rowData.cmd_valueInHexMin);
        setField(tr, '[name*="hexMax"]', rowData.cmd_valueInHexMax);

        setField(tr, '[name*="uiType"]', rowData.cmd_uiType);
        setField(tr, '[name*="uiId"]', rowData.cmd_uiId);
        setField(tr, '[name*="asperui"]', rowData.cmd_asperui);
        setField(tr, '[name*="sno_asperui"]', rowData.cmd_sno_asperui);
        setField(tr, '[name*="asperuivalue"]', rowData.cmd_asperuivalue);
        setField(tr, '[name*="uiPageNo"]', rowData.cmd_uiPageNo);
        setField(tr, '[name*="uiDescriptionInOut"]', rowData.cmd_uiDescriptionInOut);
         setField(tr, '[name*="sliderStep_"]', rowData.cmd_sliderStepValue);        
        const defaultEl = tr.querySelector('[name*="defaultValue"]');
        if (defaultEl) defaultEl.value = rowData.cmd_defaultvalue || "";

        setField(tr, '[name*="developerNotes"]', rowData.cmd_developerNotes);
        setField(tr, '[name^="enable_"]', rowData.cmd_notificationEnable);
        setField(tr, '[name*="notificationMessage"]', rowData.cmd_notificationMessage);

        // ===== 4. Restore bitwise storage on row =====

 if (rowData.bitwise) {
    tr.setAttribute(
        'data-bitwise-storage',
        JSON.stringify(rowData.bitwise)
    );
    applyBitwiseToMainRow(tr, rowData.bitwise);
}
});
}




//

function loadCommandState(index) {
    const data = storedCommands[index];
    if (!data) return;

    // restore header
    document.getElementById('com_parametername').value = data.parameterName || "";
    document.getElementById('cmd_data_flow_direction').value = data.direction || "";
    document.getElementById('cmd_framesizeinbytes').value = data.size || "";

    const tbody = document.querySelector('#additionalTableBody');
    tbody.innerHTML = ""; // clear old rows

    data.rows.forEach(rowData => {
                addCommandRow(); // your existing function that adds a row
        
               const tr = tbody.lastElementChild;

        tr.querySelector('[name*="startByte"]').value = rowData.cmd_startBytePosition;
        tr.querySelector('[name*="endByte"]').value = rowData.cmd_endBytePosition;
       
        tr.querySelector('[name*="noOfBytes"]').value = rowData.cmd_numberOfBytes || "";

        tr.querySelector('[name*="bitSize"]').value = rowData.cmd_bitSize;
        tr.querySelector('[name*="Description"], [name*="description"]').value = rowData.cmd_fieldDescription;
        tr.querySelector('[name*="dataType"], [name*="DataType"]').value = rowData.cmd_dataType;
        tr.querySelector('[name*="valueDefinition"]').value = rowData.cmd_valueDefinition;
        tr.querySelector('[name*="displayValueMin"], [name*="Min"]').value = rowData.cmd_displayValueMin;
        tr.querySelector('[name*="displayValueMax"], [name*="Max"]').value = rowData.cmd_displayValueMax;
       
        tr.querySelector('[name*="hexMin"], [name*="Min"]').value = rowData.cmd_valueInHexMin;
        tr.querySelector('[name*="hexMax"], [name*="Max"]').value = rowData.cmd_valueInHexMax;


        tr.querySelector('[name*="uiType"]').value = rowData.cmd_uiType;
        tr.querySelector('[name*="uiId"]').value = rowData.cmd_uiId;
        tr.querySelector('[name*="asperui"]').value = rowData.cmd_asperui;
        tr.querySelector('[name*="sno_asperui"], [name*="SerialNo"]').value = rowData.cmd_sno_asperui;
        tr.querySelector('[name*="asperuivalue"], [name*="UiValue"]').value = rowData.cmd_asperuivalue;
        tr.querySelector('[name*="uiPageNo"]').value = rowData.cmd_uiPageNo;
        tr.querySelector('[name*="uiDescriptionInOut"], [name*="InOut"]').value = rowData.cmd_uiDescriptionInOut;
        tr.querySelector('[name*="sliderStepValue"], [name*="Step"]').value = rowData.cmd_sliderStepValue;
        tr.querySelector('[name*="defaultvalue"], [name*="Default"]').value = rowData.cmd_defaultvalue;


        
        tr.querySelector('[name*="developerNotes"], [name*="DevNotes"]').value = rowData.cmd_developerNotes;
        tr.querySelector('[name*="notificationEnable"]').value = rowData.cmd_notificationEnable;
        tr.querySelector('[name*="notificationMessage"]').value = rowData.cmd_notificationMessage;
       

        // restore bitwise
        if (rowData.bitwise) {
            tr.setAttribute('data-bitwise-storage', JSON.stringify(rowData.bitwise));
            renderBitwiseReadonly(tr);
        }
           });

    console.log(`Loaded storedCommands[${index}]`, storedCommands[index]);
}


//
function renderBitwiseReadonly(tr) {
    // 1. Get the data restored by loadCommandState
    const rawData = tr.getAttribute('data-bitwise-storage');
    if (!rawData) return;
    
    const savedData = JSON.parse(rawData);
    const columns = tr.cells;

    // 2. Loop through columns (starting after byte positions)
    for (let i = 3; i < columns.length; i++) {
        // Skip columns with action buttons
        if (columns[i].querySelector('button') || columns[i].classList.contains('action-buttons')) {
            continue;
        }

        // 3. WIPE the bytewise input/select
        columns[i].innerHTML = ''; 

        // 4. CREATE the readonly summary box
        const box = document.createElement('input');
        box.type = 'text';
        box.readOnly = true;
        
        // Apply gray styling
        box.style.width = '100%';
        box.style.backgroundColor = '#eeeeee';
        box.style.textAlign = 'center';
        box.style.border = '1px solid #ccc';

        // 5. FILL the value (The Critical Part)
        if (savedData && savedData.length > 0) {
            // Use the exact key from your saveBitwiseConfig
            box.value = savedData[0].cmd_cmd_defaultvalue_1 || "Bitwise Set";
        }

        columns[i].appendChild(box);
    }
}


//
//


//

function updateCommandButtonLabels(limit) {
    const nextBtn = document.getElementById('next-command-btn');
    if (!nextBtn) return;

    if (commandCount === limit) {
        nextBtn.innerText = "Finalize Command Frames";
        nextBtn.style.backgroundColor = "#28a745";
    } else {
        nextBtn.innerText = "Next Command Frame";
        nextBtn.style.backgroundColor = "";
    }

    const backBtn = document.getElementById('back-command-btn');
    if (backBtn) {
        backBtn.style.display = (commandCount > 1) ? 'inline-block' : 'none';
    }
}
function updateCommandFrameTitle() {
    const titleDisplay = document.getElementById('command-title-display');
    const titleHeader = document.getElementById('command-frame-title');
    const text = `COMMAND FRAME (${commandCount})`;

    if (titleDisplay) titleDisplay.innerText = text;
    if (titleHeader) titleHeader.innerText = text;
}
//

//

// --- 1. NEXT BUTTON LOGIC ---
// ================= RESPONSE FRAME =================
 let responseCount = 1;

let storedResponses = [];

let globalResponseName = "";



/**
 * Handle Next Button Click (Matches Error Frame logic)
 */
function handleNextResponse() {
    const maxFramesField = document.getElementById('page'); // Note: 'page' for Response
    const maxFrames = parseInt(maxFramesField ? maxFramesField.value : 1) || 1;

     if (!validateDuplicateByteRange("responseTableBody", "resp", "step3-error")) {
        return; // Stop here if duplicates are found
    }

    // 1. SAFETY GATE: Check if we are at the limit
    if (responseCount >= maxFrames) {
        alert("Success: You have completed all " + maxFrames + " response frames.");
        // Mark the final page as touched/completed
        responsePageTouched[responseCount - 1] = true;
        if (typeof updateResponseBoxColors === "function") updateResponseBoxColors();
        return; 
    }

    // 2. DATA SAVE: Save current state before moving forward
    saveCurrentResponseState(); 
    // Mark the page we just left as touched so the box turns Blue
    responsePageTouched[responseCount - 1] = true;

    // 3. INCREMENT
    responseCount++;
   // 🔹 Print current command count
        console.log("Current commandCount:", commandCount);


    // 4. UPDATE PROGRESS BOXES
    if (typeof updateResponseBoxColors === "function") updateResponseBoxColors();

    // 5. UI UPDATES
    updateResponseFrameTitle();

    const tbody = document.getElementById('responseTableBody');

    // 6. LOAD EXISTING OR START FRESH
    if (storedResponses[responseCount - 1]) {
    applyResponseFrameData(storedResponses[responseCount - 1]); 
} else {
    // 1. Reset Metadata Inputs
    if(document.getElementById('resp_data_flow_direction')) document.getElementById('resp_data_flow_direction').value = "";
    if(document.getElementById('resp_framesizeinbytes')) document.getElementById('resp_framesizeinbytes').value = "1";
    if(document.getElementById('resp_frametrigger')) document.getElementById('resp_frametrigger').value = "None";
    
    // 2. Clear the UI Table
    if (tbody) {
        tbody.innerHTML = ''; 
    }

    // 3. FORCE TABLE DISPLAY: Re-add the first row
    // If addRow('response') doesn't show the table, 
    // ensure the 'resp_noofrows' is set to 1 first.
    if(document.getElementById('resp_noofrows')) document.getElementById('resp_noofrows').value = "1";
    
    if (typeof addRow === "function") {
        addRow('response'); // This creates the <tr> and <td> elements
    }
}
    // 7. SYNC UI
    updateResponseButtonLabels(maxFrames);
    if (typeof updateResponseBackVisibility === "function") updateResponseBackVisibility();
    window.scrollTo(0, 0);
}

/**
 * Handle Back Button Click (Matches Error Frame logic)
 */

function handleBackResponse() {
    if (responseCount > 1) {
        saveCurrentResponseState(); 

        responseCount--;
       // 🔹 Print current command count
        console.log("Current commandCount:", commandCount);

        
        const prevSaved = storedResponses[responseCount - 1];
        if (prevSaved) {
            if(document.getElementById('res_data_flow_direction')) document.getElementById('res_data_flow_direction').value = prevSaved.direction || "";
            if(document.getElementById('res_framesizeinbytes')) document.getElementById('res_framesizeinbytes').value = prevSaved.size || "";
            const tbody = document.getElementById('responseTableBody');
            if (tbody) tbody.innerHTML = prevSaved.tableData || "";
        }

        // 🔹 UPDATE UI
        updateResponseFrameTitle();
        
        const maxFrames = parseInt(document.getElementById('page')?.value || 1);
        updateResponseButtonLabels(maxFrames);
        if (typeof updateResponseBackVisibility === "function") updateResponseBackVisibility();

        // 🔹 HIGHLIGHT BOXES
        if (typeof updateResponseBoxColors === "function") updateResponseBoxColors();
        
        window.scrollTo(0, 0);
    }
}

/**
 * Save Current Response Frame to Array
 */
let currentResponsePageIndex = 0; // Tracking the current response page

function saveCurrentResponseState(index) {
    // Default to the last response count if index is not provided
    if (index === undefined || index === null) index = responseCount - 1;

    const tableRows = document.querySelectorAll('#responseTableBody tr');
    const rows = [];

    tableRows.forEach((tr, rowIndex) => {
        // 1. CHECK IF ROW IS BITWISE
        const typeSelect = tr.querySelector('select[name*="Type"], .bit-byte-select');
        const isBitwise = typeSelect && typeSelect.value === "Bitwise";

        let rowData = {};

        if (isBitwise) {
            // --- PATH A: BITWISE MODE ---
            const existingBitwise = tr.getAttribute('data-bitwise-storage');
            rowData.bitwise = existingBitwise ? JSON.parse(existingBitwise) : [];
            
            // Note: Normal fields are implicitly left out or could be set to null
        } else {
            // --- PATH B: NORMAL MODE ---
            rowData.bitwise = null; 
            rowData.res_startBytePosition = tr.querySelector('[name*="startByte"]')?.value || "";
            rowData.res_endBytePosition = tr.querySelector('[name*="endByte"]')?.value || "";
            rowData.res_numberOfBytes = tr.querySelector('[name*="noOfBytes"]')?.value || "";
            rowData.res_fieldDescription = tr.querySelector('[name*="Description"]')?.value || "";
            rowData.res_dataType = tr.querySelector('[name*="dataType"]')?.value || "";
            rowData.res_valueDefinition = tr.querySelector('[name*="valueDefinition"]')?.value || "";
            rowData.res_displayValueMin = tr.querySelector('[name*="Min"]')?.value || "";
            rowData.res_displayValueMax = tr.querySelector('[name*="Max"]')?.value || "";
            rowData.res_valueInHexMin = tr.querySelector('[name*="hexMin"]')?.value || "";
            rowData.res_valueInHexMax = tr.querySelector('[name*="hexMax"]')?.value || "";
            rowData.res_uiType = tr.querySelector('[name*="uiType"]')?.value || "";
            rowData.res_uiId = tr.querySelector('[name*="uiId"]')?.value || "";
            rowData.res_asperui = tr.querySelector('[name*="asperui"]')?.value || "";
            rowData.res_sno_asperui = tr.querySelector('[name*="SerialNo"]')?.value || "";
            rowData.res_asperuivalue = tr.querySelector('[name*="UiValue"]')?.value || "";
            rowData.res_uiPageNo = tr.querySelector('[name*="uiPageNo"]')?.value || "";
            rowData.res_uiDescriptionInOut = tr.querySelector('[name*="InOut"]')?.value || "";
            rowData.res_sliderStepValue = tr.querySelector('[name*="Step"]')?.value || "";
            rowData.res_defaultvalue = tr.querySelector('[name*="defaultValue"]')?.value || "";
             // Inside Path B of saveCurrentResponseState
rowData.res_developerNotes = tr.querySelector('[name*="DevNotes" i], [name*="developerNotes" i]')?.value || "";
      
            rowData.res_notificationEnable = tr.querySelector('[name^="enable_"]')?.value || "No";
           rowData.res_notificationMessage = tr.querySelector('input[name*="message" i], textarea[name*="message" i], [name*="msg" i]')?.value || "";  
        }

        rows.push(rowData);
    });

    // Storing in a separate array: storedResponses
    storedResponses[index] = {
        parameterName: document.getElementById('res_parametername')?.value || "",
        direction: document.getElementById('res_data_flow_direction')?.value || "",
        size: document.getElementById('res_framesizeinbytes')?.value || "",
        rows: rows
    };

    // 🔹 Console Logs
    console.log(`Saved to storedResponses[${index}]`, storedResponses[index]);

    console.groupCollapsed("%cFULL storedResponses ARRAY", "color: blue; font-weight: bold;");
    storedResponses.forEach((res, i) => console.log(`Index ${i}:`, res));
    console.groupEnd();
}
/**
 * UI Sync: Titles
 */
function updateResponseFrameTitle() {
    const mainTitle = document.getElementById('response-title-display');
    const subTitle = document.getElementById('response-frame-title');
    const text = `RESPONSE FRAME (${responseCount})`;
    if (mainTitle) mainTitle.innerText = text;
    if (subTitle) subTitle.innerText = text;
}

/**
 * UI Sync: Button Labels
 */
function updateResponseButtonLabels(limit) {
    const nextBtn = document.querySelector('.pagination-next-btn');
    if (!nextBtn) return;

    if (responseCount === limit) {
        nextBtn.innerHTML = `<span>Finalize & Move to Error</span>`;
        nextBtn.style.backgroundColor = "#28a745"; 
        nextBtn.style.color = "#ffffff";
    } else {
        nextBtn.innerHTML = `<span>Next</span><span class="arrow"> ›</span>`;
        nextBtn.style.backgroundColor = "#ffffff";
        nextBtn.style.color = "#333333";
    }
}

//ERROR FRAME
let errorCount=1;
let storedErrors = [];
let errorCompletedPages = new Set(); 
let sharedErrorParameterName = ""; 

/**
 * Handle Next Button Click (Matches Command logic)
 */
function handleNextError() {
    const maxFramesField = document.getElementById('pages'); 
    const maxFrames = parseInt(maxFramesField ? maxFramesField.value : 1) || 1;

     if (!validateDuplicateByteRange("errorTableBody", "error", "step4-error")) {
        return; // Stop execution and stay on current frame if duplicates are found
    }
   
    // 1. SAFETY GATE: Check if we are at the limit
    if (errorCount >= maxFrames) {
        alert("Success: You have completed all " + maxFrames + " error frames.");
        errorCompletedPages.add(errorCount);
        if (typeof updateErrorBoxColors === "function") updateErrorBoxColors();
        return; 
    }

    // 2. DATA SAVE: Save current state before moving forward
    saveCurrentErrorState(); 

    // 3. INCREMENT
    errorCount++;

    // 4. UPDATE PROGRESS BOXES
    if (typeof updateErrorBoxColors === "function") updateErrorBoxColors();

    // 5. UI UPDATES
    updateErrorFrameTitle();

    const tbody = document.getElementById('errorTableBody');

    // 6. LOAD EXISTING OR START FRESH
    if (storedErrors[errorCount - 1]) {
        applyErrorFrameData(storedErrors[errorCount - 1]); 
    } else {
        // Reset inputs for a truly new page
        if(document.getElementById('error_data_flow_direction')) document.getElementById('error_data_flow_direction').value = "";
        if(document.getElementById('error_framesizeinbytes')) document.getElementById('error_framesizeinbytes').value = "";
        if(document.getElementById('error_frametrigger')) document.getElementById('error_frametrigger').value = "None";
        if (tbody) tbody.innerHTML = ''; 
        if (typeof addRow === "function") addRow('error'); 
    }

    // 7. SYNC UI
    updateErrorButtonLabels(maxFrames);
    window.scrollTo(0, 0);
}

/**
 * Handle Back Button Click (Matches Command logic)
 */
function handleBackErrorFrame() {
    if (errorCount > 1) {
        saveCurrentErrorState(); 

        errorCount--;
        
        const prevSaved = storedErrors[errorCount - 1];
        if (prevSaved) {
            applyErrorFrameData(prevSaved);
        }

        // 🔹 UPDATE UI
        updateErrorFrameTitle();
        
        const maxFrames = parseInt(document.getElementById('pages')?.value || 1);
        updateErrorButtonLabels(maxFrames);

        // 🔹 HIGHLIGHT CIRCLES
        if (typeof updateErrorBoxColors === "function") updateErrorBoxColors();
        
        window.scrollTo(0, 0);
    }
}

/**
 * Save Current Frame to Array
 */

function saveCurrentErrorState(index) {
    // Default to the last error count if index is not provided
    if (index === undefined || index === null) index = errorCount - 1;

    const tableRows = document.querySelectorAll('#errorTableBody tr');
    const rows = [];

    tableRows.forEach((tr, rowIndex) => {
        // 1. CHECK IF ROW IS BITWISE
        const typeSelect = tr.querySelector('select[name*="Type"], .bit-byte-select');
        const isBitwise = typeSelect && typeSelect.value === "Bitwise";

        let rowData = {};

        if (isBitwise) {
            // --- PATH A: BITWISE MODE ---
            const existingBitwise = tr.getAttribute('data-bitwise-storage');
            rowData.bitwise = existingBitwise ? JSON.parse(existingBitwise) : [];
            
            // Other fields remain undefined/excluded for a clean Bitwise object
        } else {
            // --- PATH B: NORMAL MODE ---
            rowData.bitwise = null; 
            rowData.err_startBytePosition = tr.querySelector('[name*="startByte"]')?.value || "";
            rowData.err_endBytePosition = tr.querySelector('[name*="endByte"]')?.value || "";
            rowData.err_numberOfBytes = tr.querySelector('[name*="noOfBytes"]')?.value || "";
            rowData.err_fieldDescription = tr.querySelector('[name*="Description"]')?.value || "";
            rowData.err_dataType = tr.querySelector('[name*="dataType"]')?.value || "";
            rowData.err_valueDefinition = tr.querySelector('[name*="valueDefinition"]')?.value || "";
            rowData.err_displayValueMin = tr.querySelector('[name*="Min"]')?.value || "";
            rowData.err_displayValueMax = tr.querySelector('[name*="Max"]')?.value || "";
            rowData.err_valueInHexMin = tr.querySelector('[name*="hexMin"]')?.value || "";
            rowData.err_valueInHexMax = tr.querySelector('[name*="hexMax"]')?.value || "";
            rowData.err_uiType = tr.querySelector('[name*="uiType"]')?.value || "";
            rowData.err_uiId = tr.querySelector('[name*="uiId"]')?.value || "";
            rowData.err_asperui = tr.querySelector('[name*="asperui"]')?.value || "";
            rowData.err_sno_asperui = tr.querySelector('[name*="SerialNo"]')?.value || "";
            rowData.err_asperuivalue = tr.querySelector('[name*="UiValue"]')?.value || "";
            rowData.err_uiPageNo = tr.querySelector('[name*="uiPageNo"]')?.value || "";
            rowData.err_uiDescriptionInOut = tr.querySelector('[name*="InOut"]')?.value || "";
            rowData.err_sliderStepValue = tr.querySelector('[name*="Step"]')?.value || "";
            rowData.err_defaultvalue = tr.querySelector('[name*="defaultValue"]')?.value || "";
           
           // Inside Path B of saveCurrentErrorState
rowData.err_developerNotes = tr.querySelector('[name*="DevNotes" i], [name*="developerNotes" i]')?.value || "";
            rowData.err_notificationEnable = tr.querySelector('[name^="enable_"]')?.value || "No";
           rowData.err_notificationMessage = tr.querySelector('input[name*="message" i], textarea[name*="message" i], [name*="msg" i]')?.value || "";

              
        }

        rows.push(rowData);
    });

    // Storing in a separate array: storedErrors
    storedErrors[index] = {
        parameterName: document.getElementById('error_parametername')?.value || "",
        direction: document.getElementById('error_data_flow_direction')?.value || "",
        size: document.getElementById('error_framesizeinbytes')?.value || "",
        rows: rows
    };

    // 🔹 Console Logs
    console.log(`Saved to storedErrors[${index}]`, storedErrors[index]);

    console.groupCollapsed("%cFULL storedErrors ARRAY", "color: red; font-weight: bold;");
    storedErrors.forEach((err, i) => console.log(`Index ${i}:`, err));
    console.groupEnd();
}
/**
 * Helper: Fill fields with saved data
 */
function applyErrorFrameData(data) {
    if(document.getElementById('error_parametername')) document.getElementById('error_parametername').value = sharedErrorParameterName || data.parameterName || "";
    if(document.getElementById('error_data_flow_direction')) document.getElementById('error_data_flow_direction').value = data.direction || "";
    if(document.getElementById('error_framesizeinbytes')) document.getElementById('error_framesizeinbytes').value = data.size || "";
    if(document.getElementById('error_frametrigger')) document.getElementById('error_frametrigger').value = data.trigger || "";
    
    const tableBody = document.getElementById('errorTableBody');
    if (tableBody) tableBody.innerHTML = data.tableData || "";
}

/**
 * Button Visuals & Title
 */
function updateErrorButtonLabels(limit) {
    const nextBtn = document.getElementById('next-error-page-btn');
    if (!nextBtn) return;

    if (errorCount === limit) {
        nextBtn.innerText = "Finalize Error Frames";
        nextBtn.style.backgroundColor = "#28a745"; 
    } else {
        nextBtn.innerText = "Next Error Frame";
        nextBtn.style.backgroundColor = ""; 
    }

    const backBtn = document.getElementById('back-error-page-btn');
    if (backBtn) {
        backBtn.style.display = (errorCount > 1) ? 'inline-block' : 'none';
    }
}

function updateErrorFrameTitle() {
    const titleDisplay = document.getElementById('error-title-display');
    const titleHeader = document.getElementById('error-frame-title');
    const text = `ERROR FRAME (${errorCount})`;
    if (titleDisplay) titleDisplay.innerText = text;
    if (titleHeader) titleHeader.innerText = text;
}
function updateErrorBackVisibility() {
    const backBtn = document.getElementById('back-error-page-btn');
    if (backBtn) {
        backBtn.style.display = (errorCount > 1) ? 'inline-block' : 'none';
    }
}

//save data


//
function saveCommandData() {
    saveCurrentCommandState();
    saveCurrentCommandFrame();

    const itemData = {
        brandname: document.getElementById('brandname')?.value || "efactor",
        productname: document.getElementById('productname')?.value || "COOKER",
        modelname: document.getElementById('modelname')?.value || "V1",
        productdescription: document.getElementById('productdescription')?.value || "des",
        id: document.getElementsByName('id')[0]?.value || "",
        cmd_parametername: document.getElementById('com_parametername')?.value || "",
        cmd_data_flow_direction: document.getElementById('cmd_data_flow_direction')?.value || "",
        cmd_frametype: document.getElementById('cmd_frametype')?.value || "",
        cmd_framesizeinbytes: document.getElementById('cmd_framesizeinbytes')?.value || "",
        cmd_noofrows: document.getElementById('cmd_noofrows')?.value || "0",
        cmd_frametrigger: document.getElementById('cmd_frame_trigger_options')?.value || "",
        cmd_multipleui: document.getElementById('cmd_multipleui')?.value || "",
        cmd_triggerui: document.getElementById('cmd_triggerui')?.value || "",
        cmd_voice_command: document.getElementById('cmd_voice_command')?.value || "",
        cmd_voice_control_instruction: document.getElementById('cmd_voice_control_instruction')?.value || "",
        cmd_remarks: document.getElementById('cmd_remarks')?.value || "",
        numberofpages: storedCommands.length,
        date: new Date().toISOString().split('T')[0],
        itemid: 153,
        cmd_frames: storedCommands,
    };

    console.log("--- COMMAND FRAME SAVE ---");

    // ✅ FULL ARRAY DISPLAY (no hiding)
    logFull("Item Data (FULL):", itemData);
    logFull("Command Frames (FULL):", itemData.cmd_frames);

    printCmdConsoleFromItemData(itemData);
    alert("✅ Command Frame data printed to console!");
}



//
let currentPageIndex = 0;


// Save current page's table data
function saveCurrentCommandState(index) {
    if (index === undefined || index === null) index = commandCount - 1;

    const tableRows = document.querySelectorAll('#additionalTableBody tr');
    const rows = [];

    tableRows.forEach((tr, rowIndex) => {
        // 1. CHECK IF ROW IS BITWISE
        // Check the dropdown value. Replace '[name*="bitByte"]' with your actual selector if different.
        const typeSelect = tr.querySelector('select[name*="Type"], .bit-byte-select');
        const isBitwise = typeSelect && typeSelect.value === "Bitwise";

        let rowData = {};

        if (isBitwise) {
            // --- PATH A: BITWISE MODE (Saves Bitwise Array, Ignores Inputs) ---
            const existingBitwise = tr.getAttribute('data-bitwise-storage');
            rowData.bitwise = existingBitwise ? JSON.parse(existingBitwise) : [];

            // Set normal fields to null so they are ignored by the console/logs
            

            // Add any other fields you want to suppress in the console here
        } else {
            // --- PATH B: NORMAL MODE (Current behavior) ---
            rowData.bitwise = null; 
            rowData.cmd_startBytePosition = tr.querySelector('[name*="startByte"]')?.value || "";
            rowData.cmd_endBytePosition = tr.querySelector('[name*="endByte"]')?.value || "";
            rowData.cmd_numberOfBytes = tr.querySelector('[name*="noOfBytes"]')?.value || "";
            rowData.cmd_fieldDescription = tr.querySelector('[name*="Description"]')?.value || "";
            rowData.cmd_dataType = tr.querySelector('[name*="dataType"]')?.value || "";
            rowData.cmd_valueDefinition = tr.querySelector('[name*="valueDefinition"]')?.value || "";
            rowData.cmd_displayValueMin = tr.querySelector('[name*="Min"]')?.value || "";
            rowData.cmd_displayValueMax = tr.querySelector('[name*="Max"]')?.value || "";
            rowData.cmd_valueInHexMin = tr.querySelector('[name*="hexMin"]')?.value || "";
            rowData.cmd_valueInHexMax = tr.querySelector('[name*="hexMax"]')?.value || "";
            rowData.cmd_uiType = tr.querySelector('[name*="uiType"]')?.value || "";
            rowData.cmd_uiId = tr.querySelector('[name*="uiId"]')?.value || "";
            rowData.cmd_asperui = tr.querySelector('[name*="asperui"]')?.value || "";
            rowData.cmd_sno_asperui = tr.querySelector('[name*="SerialNo"]')?.value || "";
            rowData.cmd_asperuivalue = tr.querySelector('[name*="UiValue"]')?.value || "";
            rowData.cmd_uiPageNo = tr.querySelector('[name*="uiPageNo"]')?.value || "";
            rowData.cmd_uiDescriptionInOut = tr.querySelector('[name*="InOut"]')?.value || "";
            rowData.cmd_sliderStepValue = tr.querySelector('[name*="Step"]')?.value || "";
            rowData.cmd_defaultvalue = tr.querySelector('[name*="defaultValue"]')?.value || "";
           
           // Inside Path B of saveCurrentCommandState
       rowData.cmd_developerNotes = tr.querySelector('[name*="DevNotes" i], [name*="developerNotes" i]')?.value || "";
 
            rowData.cmd_notificationEnable = tr.querySelector('[name^="enable_"]')?.value || "No";
            
           rowData.cmd_notificationMessage = tr.querySelector('input[name*="message" i], textarea[name*="message" i], [name*="msg" i]')?.value || ""; 
        }

        rows.push(rowData);
    });

    storedCommands[index] = {
        parameterName: document.getElementById('com_parametername')?.value || "",
        direction: document.getElementById('cmd_data_flow_direction')?.value || "",
        size: document.getElementById('cmd_framesizeinbytes')?.value || "",
        rows: rows
    };


    // 🔹 Old console logs (keep untouched)
    console.log(`Saved to storedCommands[${index}]`, storedCommands[index]);

    // 🔹 NEW: Full storedCommands log for inspection without disturbing old logs
    console.groupCollapsed("%cFULL storedCommands ARRAY", "color: green; font-weight: bold;");
    storedCommands.forEach((cmd, i) => console.log(`Index ${i}:`, cmd));
    console.groupEnd();
}

//
function printCmdConsoleFromItemData(itemData) {
    console.log("%c --- DATA SYNC REPORT ---", "color: white; background: #222; font-weight: bold;");

    // 1. Find where the frames/pages are stored (tries both common names)
    const frames = itemData.cmd_frames || itemData.commands || [];

    if (!Array.isArray(frames) || frames.length === 0) {
        console.warn("No command frames found in the data object.");
        return;
    }

    frames.forEach((page, cmdIndex) => {
        // 2. Find the rows array inside the page object
        const rowsToProcess = page.rows || (Array.isArray(page) ? page : []);

        if (rowsToProcess.length === 0) {
            console.log(`Cmd[${cmdIndex}] contains no row data.`);
            return;
        }

        rowsToProcess.forEach((row, rowIndex) => {
            const rowNo = rowIndex + 1;

            // 3. Handle Bitwise Data
            if (row.bitwise && Array.isArray(row.bitwise)) {
                row.bitwise.forEach((bit, bitIdx) => {
                    printFormattedEntries(bit, `Cmd[${cmdIndex}]-row[${rowNo}]-bit[${bitIdx + 1}]`);
                });
            }

            // 4. Handle Bytewise Data
            printFormattedEntries(row, `Cmd[${cmdIndex}]-row[${rowNo}]`);
        });
    });
}
// Helper function to clean keys and print
function printFormattedEntries(dataObj, prefix) {
    Object.entries(dataObj).forEach(([key, value]) => {
        // Skip nested objects/arrays to keep logs clean
        if (typeof value === 'object' && value !== null) return;

        let cleanKey = key
            .replace(/^cmd_cmd_/, '')
            .replace(/^cmd_/, '')
            .replace(/_\d+$/, '');

        console.log(`${prefix}-${cleanKey} => ${value}`);
    });
}

//

//


// Save current page's table data

// Submit all pages to backend
//
//

/**
 * Function specifically for the Step 3 Save Button
 */

function saveResponseData() {
    try {
        // 1. Sync the current UI state to the array
        saveCurrentResponseState();

        // 2. Map the "Item Data" (Matching Command Frame structure)
        const itemData = {
            brandname: document.getElementById('brandname')?.value || "efactor",
            productname: document.getElementById('productname')?.value || "COOKER",
            modelname: document.getElementById('modelname')?.value || "V1",
            productdescription: document.getElementById('productdescription')?.value || "",
            
            // Meta Information
            resp_parametername: document.getElementById('resp_parametername')?.value || "",
            resp_data_flow_direction: document.getElementById('resp_data_flow_direction')?.value || "",
            resp_frametype: document.getElementById('resp_frametype')?.value || "",
            resp_framesizeinbytes: document.getElementById('resp_framesizeinbytes')?.value || "",
            resp_noofrows: document.getElementById('resp_noofrows')?.value || "0",
            resp_frametrigger: document.getElementById('resp_frametrigger')?.value || "",
            resp_multipleui: document.getElementById('resp_multipleui')?.value || "",
            resp_triggerui: document.getElementById('resp_triggerui')?.value || "",
            resp_voice_command: document.getElementById('resp_voice_command')?.value || "",
            resp_voice_control_instruction: document.getElementById('resp_voice_control_instruction')?.value || "",
            resp_remarks: document.getElementById('resp_remarks')?.value || "",
            
            // Sync logic
            numberofpages: storedResponses.length,
            date: new Date().toISOString().split('T')[0],
            resp_frames: storedResponses // Similar to cmd_frames
        };

        console.log("--- RESPONSE FRAME SAVE ---");
        console.log("Item Data (raw):", itemData);

        // 3. Print to console using the shared formatter
        printRespConsoleFromItemData(itemData);

        alert("✅ Response Frame Data saved and printed!");

    } catch (err) {
        console.error("Critical Error in saveResponseData:", err);
    }
}

 //
function printRespConsoleFromItemData(itemData) {
    console.log("%c --- RESPONSE DATA SYNC REPORT ---", "color: white; background: #28a745; font-weight: bold;");

    const frames = itemData.resp_frames || [];

    frames.forEach((page, respIndex) => {
        const rowsToProcess = page.rows || [];

        rowsToProcess.forEach((row, rowIndex) => {
            const rowNo = rowIndex + 1;

            // Handle Bitwise
            if (row.bitwise && Array.isArray(row.bitwise)) {
                row.bitwise.forEach((bit, bitIdx) => {
                    printFormattedEntries(bit, `Resp[${respIndex}]-row[${rowNo}]-bit[${bitIdx + 1}]`);
                });
            }

            // Handle Bytewise
            printFormattedEntries(row, `Resp[${respIndex}]-row[${rowNo}]`);
        });
    });
}

// Enhanced helper to clean both cmd_ and resp_ prefixes
function printFormattedEntries(dataObj, prefix) {
    Object.entries(dataObj).forEach(([key, value]) => {
        if (typeof value === 'object' && value !== null) return;

        let cleanKey = key
            .replace(/^(cmd_|resp_)/g, '') // Removes cmd_ or resp_
            .replace(/^(cmd_|resp_)/g, '') // Double check for nested prefixes
            .replace(/_\d+$/, '');

        console.log(`${prefix}-${cleanKey} => ${value}`);
    });
}
function loadResponseState(index) {
    const data = storedResponses[index];
    if (!data || !data.rows) {
        // If no saved data exists for this page, clear the table
        document.getElementById('responseTableBody').innerHTML = "";
        return;
    }

    // 1. Restore Top Metadata
    document.getElementById('resp_parametername').value = data.parameterName || "";
    document.getElementById('resp_data_flow_direction').value = data.direction || "";
    document.getElementById('resp_framesizeinbytes').value = data.size || "";

    // 2. Reconstruct Table Rows
    const tableBody = document.getElementById('responseTableBody');
    tableBody.innerHTML = ""; // Clear current UI

    data.rows.forEach((row) => {
        const newRow = createResponseRow(); // Call your existing function that creates a blank <tr>
        
        // Fill the inputs in the new row
        // We use the 'resp_' prefix keys we saved earlier
        newRow.querySelector('[name*="startByte"]').value = row.resp_startBytePosition;
        newRow.querySelector('[name*="endByte"]').value = row.resp_endBytePosition;
        newRow.querySelector('[name*="numberOfBytes"]').value = row.resp_numberOfBytes;
        newRow.querySelector('[name*="bitSize"]').value = row.resp_bitSize;
        newRow.querySelector('[name*="Description"]').value = row.resp_fieldDescription;
        newRow.querySelector('[name*="dataType"]').value = row.resp_dataType;
        newRow.querySelector('[name*="valueDefinition"]').value = row.resp_valueDefinition;
        newRow.querySelector('[name*="displayValueMin"]').value = row.resp_displayValueMin;
        newRow.querySelector('[name*="displayValueMax"]').value = row.resp_displayValueMax;
        newRow.querySelector('[name*="uiType"]').value = row.resp_uiType;
        // ... add any other fields you need here ...

        // 3. Restore Bitwise Data attribute if it exists
        if (row.bitwise) {
            newRow.setAttribute('data-bitwise-storage', JSON.stringify(row.bitwise));
        }

        tableBody.appendChild(newRow);
    });
}
//ERROR
// ==========================================
// 2. THE SAVE FUNCTION
// ==========================================
// --- MANDATORY GLOBALS (Add these at the top) ---
function saveErrorData() {
    try {
        // 1. Ensure the current UI state is saved to the storedErrors array first
        saveCurrentErrorState();

        // 2. Map EVERY field into the final data object
        const itemData = {
            // --- Global Meta (Step 1) ---
            brandname: document.getElementById('brandname')?.value || "efactor",
            productname: document.getElementById('productname')?.value || "COOKER",
            modelname: document.getElementById('modelname')?.value || "V1",
            productdescription: document.getElementById('productdescription')?.value || "",
            id: document.getElementsByName('id')[0]?.value || "", // Database ID if exists
            itemid: 153, // Your static/system ID

            // --- Error Meta (Step 3 - Top Section) ---
            error_parametername: document.getElementById('error_parametername')?.value || "",
            error_data_flow_direction: document.getElementById('error_data_flow_direction')?.value || "",
            error_frametype: document.getElementById('error_frametype')?.value || "",
            error_framesizeinbytes: document.getElementById('error_framesizeinbytes')?.value || "",
            error_noofrows: document.getElementById('error_noofrows')?.value || "0",
            error_frametrigger: document.getElementById('error_frametrigger')?.value || "",
            error_multipleui: document.getElementById('error_multipleui')?.value || "",
            error_triggerui: document.getElementById('error_triggerui')?.value || "",
            error_voice_command: document.getElementById('error_voice_command')?.value || "",
            error_voice_control_instruction: document.getElementById('error_voice_control_instruction')?.value || "",
            error_remarks: document.getElementById('error_remarks')?.value || "",

            // --- Multi-Page Data ---
            numberofpages: (typeof storedErrors !== 'undefined') ? storedErrors.length : 1,
            error_frames: (typeof storedErrors !== 'undefined') ? storedErrors : [],

            // --- System Fields ---
            date: new Date().toISOString().split('T')[0],
            error_sequence: (typeof errorCount !== 'undefined') ? errorCount : 1,
            status: "saved"
        };

        // 3. Console Reporting
        console.log("%c --- FINAL ERROR DATA OBJECT --- ", "background: #dc3545; color: #ffffff; font-weight: bold;");
        console.log("Full Item Data:", itemData);

        // 4. Trigger your print report (if you have the function defined)
        if (typeof printErrConsoleFromItemData === "function") {
            printErrConsoleFromItemData(itemData);
        }

        alert("✅ All Error Frame fields (Metadata + Table Rows) have been saved.");

    } catch (err) {
        console.error("Critical Error in saveErrorData:", err);
    }
}

//
function printErrConsoleFromItemData(itemData) {
    console.log("%c --- ERROR DATA SYNC REPORT ---", "color: white; background: #dc3545; font-weight: bold;");

    const frames = itemData.error_frames || [];

    frames.forEach((page, errIndex) => {
        // Use the 'rows' array we created in saveCurrentErrorState
        const rowsToProcess = page.rows || [];

        rowsToProcess.forEach((row, rowIndex) => {
            const rowNo = rowIndex + 1;

            // Handle Bitwise (Error specific)
            if (row.bitwise && Array.isArray(row.bitwise)) {
                row.bitwise.forEach((bit, bitIdx) => {
                    printFormattedEntries(bit, `Err[${errIndex}]-row[${rowNo}]-bit[${bitIdx + 1}]`);
                });
            }

            // Handle Bytewise
            printFormattedEntries(row, `Err[${errIndex}]-row[${rowNo}]`);
        });
    });
}

// Reuse your existing printFormattedEntries helper—it already handles cleaning err_ prefixes!
//
function loadErrorState(index) {
    const data = storedErrors[index];
    if (!data) {
        // If no saved data exists for this page, clear the table
        const tbody = document.getElementById('errorTableBody');
        if (tbody) tbody.innerHTML = "";
        return;
    }

    // 1. Restore Top Metadata (All Fields)
    if(document.getElementById('error_parametername')) document.getElementById('error_parametername').value = data.error_parametername || "";
    if(document.getElementById('error_data_flow_direction')) document.getElementById('error_data_flow_direction').value = data.error_data_flow_direction || "";
    if(document.getElementById('error_frametype')) document.getElementById('error_frametype').value = data.error_frametype || "";
    if(document.getElementById('error_framesizeinbytes')) document.getElementById('error_framesizeinbytes').value = data.error_framesizeinbytes || "";
    if(document.getElementById('error_noofrows')) document.getElementById('error_noofrows').value = data.error_noofrows || "0";
    if(document.getElementById('error_frametrigger')) document.getElementById('error_frametrigger').value = data.error_frametrigger || "";
    if(document.getElementById('error_multipleui')) document.getElementById('error_multipleui').value = data.error_multipleui || "";
    if(document.getElementById('error_triggerui')) document.getElementById('error_triggerui').value = data.error_triggerui || "";
    if(document.getElementById('error_voice_command')) document.getElementById('error_voice_command').value = data.error_voice_command || "";
    if(document.getElementById('error_voice_control_instruction')) document.getElementById('error_voice_control_instruction').value = data.error_voice_control_instruction || "";
    if(document.getElementById('error_remarks')) document.getElementById('error_remarks').value = data.error_remarks || "";

    // 2. Reconstruct Table Rows from the saved 'rows' array
    const tableBody = document.getElementById('errorTableBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = ""; // Clear existing UI rows

    if (data.rows && Array.isArray(data.rows)) {
        data.rows.forEach((row) => {
            // Use your function that creates a blank row for the error table
            const newRow = createErrorRow(); 
            
            // Map saved values back into the row inputs
            newRow.querySelector('[name*="startByte"]').value = row.err_startBytePosition || "";
            newRow.querySelector('[name*="endByte"]').value = row.err_endBytePosition || "";
            newRow.querySelector('[name*="numberOfBytes"]').value = row.err_numberOfBytes || "";
            newRow.querySelector('[name*="bitSize"]').value = row.err_bitSize || "";
            newRow.querySelector('[name*="Description"]').value = row.err_fieldDescription || "";
            newRow.querySelector('[name*="dataType"]').value = row.err_dataType || "";
            newRow.querySelector('[name*="valueDefinition"]').value = row.err_valueDefinition || "";
            newRow.querySelector('[name*="displayValueMin"]').value = row.err_displayValueMin || "";
            newRow.querySelector('[name*="displayValueMax"]').value = row.err_displayValueMax || "";
            newRow.querySelector('[name*="valueInHexMin"]').value = row.err_valueInHexMin || "";
            newRow.querySelector('[name*="valueInHexMax"]').value = row.err_valueInHexMax || "";
            newRow.querySelector('[name*="uiType"]').value = row.err_uiType || "";
            newRow.querySelector('[name*="uiId"]').value = row.err_uiId || "";
            newRow.querySelector('[name*="asperui"]').value = row.err_asperui || "";
            newRow.querySelector('[name*="sno_asperui"]').value = row.err_sno_asperui || "";
            newRow.querySelector('[name*="asperuivalue"]').value = row.err_asperuivalue || "";
            newRow.querySelector('[name*="uiPageNo"]').value = row.err_uiPageNo || "";
            newRow.querySelector('[name*="uiDescriptionInOut"]').value = row.err_uiDescriptionInOut || "";
            newRow.querySelector('[name*="sliderStepValue"]').value = row.err_sliderStepValue || "";
            newRow.querySelector('[name*="defaultvalue"]').value = row.err_defaultvalue || "";
            newRow.querySelector('[name*="developerNotes"]').value = row.err_developerNotes || "";

            // 3. Restore Bitwise Data attribute for the modal
            if (row.bitwise) {
                newRow.setAttribute('data-bitwise-storage', JSON.stringify(row.bitwise));
            }

            tableBody.appendChild(newRow);
        });
    }
}
// ==========================================
// 3. THE "ADD ANOTHER" BUTTON LOGIC
// ==========================================
function handleAddAnotherError() {
    // 1. Save current page data to globalStoredErrors[0] (or current index)
    saveErrorData();

    // 2. Increment the global counter
    errorCount++;

    // 3. Clear UI for next page
    const tableBody = document.getElementById('errorTableBody');
    if (tableBody) tableBody.innerHTML = "";
    
    // Reset row count input for new page
    const rowCountInput = document.getElementById('error_noofrows');
    if (rowCountInput) rowCountInput.value = 0;

    // Keep the name consistent
    document.getElementById('error_parametername').value = sharedErrorParameterName;

    // Update the UI Title
    const title = document.getElementById('error-title-display');
    if (title) title.innerText = `ERROR FRAME (${errorCount})`;
    
    console.log(`Ready for Frame ${errorCount} at Index [${errorCount - 1}]`);
}

/**
 * Helper to sync the visible table rows into the storedErrors array.
 * This function should be called by your 'Add Another Error' button 
 * or a specific 'Save Table' button within the table area.
 */
function saveCurrentErrorFrame() {
    const rows = [];
    document.querySelectorAll('#errorTableBody tr').forEach(tr => {
        const rowData = {};
        tr.querySelectorAll('input, select').forEach(input => {
            const identifier = input.className.split(' ')[0] || input.name || "input";
            const key = "error_" + identifier;
            rowData[key] = input.value;
        });
        rows.push(rowData);
    });
    
    const idx = (typeof errorCount !== 'undefined') ? errorCount - 1 : 0;
    
    if (typeof storedErrors !== 'undefined') {
        storedErrors[idx] = rows;
    }
}


 // Capture Command Table rows into the array

// 1. THIS IS THE MISSING FUNCTION CAUSING YOUR ERROR


function saveCurrentCommandFrame() {
    const rows = [];
    document.querySelectorAll('#additionalTableBody tr').forEach(tr => {
        const rowData = {};
        // Find every input in this row and map to a 'cmd_' key
        tr.querySelectorAll('input, select').forEach(input => {
            // Use the class name or name attribute to determine the key
            const key = "cmd_" + (input.className.split(' ')[0] || input.name);
            rowData[key] = input.value;
        });
        rows.push(rowData);
    });
    
    const idx = (typeof commandCount !== 'undefined') ? commandCount - 1 : 0;
    if (typeof storedCommands !== 'undefined') storedCommands[idx] = rows;
}
  
/**
 * Capture Response Table rows into the array
 */
function saveCurrentResponseFrame() {
    const rows = [];
    document.querySelectorAll('#responseTableBody tr').forEach(tr => {
        const rowData = {};
        tr.querySelectorAll('input, select').forEach(input => {
            const key = "resp_" + (input.className.split(' ')[0] || input.name);
            rowData[key] = input.value;
        });
        rows.push(rowData);
    });
    
    const idx = (typeof responseCount !== 'undefined') ? responseCount - 1 : 0;
    if (typeof storedResponses !== 'undefined') storedResponses[idx] = rows;
}

/**
 * Formatter to mimic PHP print_r / Array() structure
 */
function formatAsPHPArray(obj, indent = "    ") {
    if (obj === null || obj === undefined) return "NULL";
    let output = "Array\n" + indent.substring(4) + "(\n";
    for (const key in obj) {
        if (obj.hasOwnProperty(key)) {
            const value = obj[key];
            output += `${indent}[${key}] => `;
            if (Array.isArray(value) || (typeof value === 'object' && value !== null)) {
                output += formatAsPHPArray(value, indent + "    ");
            } else {
                output += value;
            }
            output += "\n";
        }
    }
    output += indent.substring(4) + ")";
    return output;
}

// Dummy function to prevent the "ReferenceError" from your logs
function updateMoveButtons() { return true; }
//BITWISE
function toggleBitwiseFields(select, prefix, index) {
    const container = document.getElementById(`bitwiseContainer_${prefix}_${index}`);
    if (container) {
        if (select.value === "Data") {
            container.style.display = "block";
        } else {
            container.style.display = "none";
            // Clear values when hidden so validation doesn't get confused
            container.querySelectorAll('input').forEach(input => input.value = "");
        }
    }
}
//
function validateTableStep(tableBodyId, errorId, isMandatory) {
    let isValid = true;
    let globalBitwiseError = "";
    const rows = document.querySelectorAll(`#${tableBodyId} tr`);
    const errorElement = document.getElementById(errorId);

    // Track bit usage per byte: { "byteIndex": [bit0, bit1...bit7] }
    const byteMap = {};

    if (rows.length === 0) return isMandatory ? false : true;

    for (let rowIndex = 0; rowIndex < rows.length; rowIndex++) {
        const row = rows[rowIndex];
        const typeSelect = row.querySelector('select[name*="Type"], .bit-byte-select');
        const startByte = row.querySelector('[name*="startByte"]')?.value;
        const isBitwise = typeSelect?.value === "Bitwise";

        // 1. STANDARD FIELD VALIDATION (Empty checks)
        const inputs = row.querySelectorAll('input, select');
        inputs.forEach(field => {
            if (field.offsetParent !== null && !field.hasAttribute("readonly") && !field.disabled) {
                field.classList.remove("error");
                if (!field.value || !field.value.trim() || field.value === "Error") {
                    field.classList.add("error");
                    isValid = false;
                }
            }
        });

        // 2. CROSS-ROW BITWISE OVERLAP VALIDATION
        if (isBitwise && startByte) {
            const bitwiseDataRaw = row.getAttribute('data-bitwise-storage');
            if (bitwiseDataRaw) {
                const bitwiseEntries = JSON.parse(bitwiseDataRaw);

                // Initialize bit tracker for this specific byte if it doesn't exist
                if (!byteMap[startByte]) {
                    byteMap[startByte] = new Array(8).fill(false);
                }

                for (let entry of bitwiseEntries) {
                    let sBit = parseInt(entry.bitStart);
                    let eBit = parseInt(entry.bitEnd);

                    for (let b = sBit; b <= eBit; b++) {
                        if (byteMap[startByte][b]) {
                            // BIT IS ALREADY TAKEN BY A PREVIOUS ROW
                            isValid = false;
                            globalBitwiseError = `Overlap Error: Byte ${startByte}, Bit ${b} is repeated in Row ${rowIndex + 1}.`;
                            row.classList.add("error");
                            break; 
                        }
                        byteMap[startByte][b] = true;
                    }
                    if (!isValid) break;
                }
            }
        }
        if (!isValid && globalBitwiseError) break; // Exit loop on first bit error
    }

    // 3. UI ERROR DISPLAY
    if (errorElement) {
        if (!isValid) {
            errorElement.innerText = globalBitwiseError || "❌ Please fill all required fields.";
            errorElement.style.display = "block";
        } else {
            errorElement.innerText = "";
            errorElement.style.display = "none";
        }
    }

    return isValid;
}
//
// Store previous selection so we can revert if needed
// Store previous selection per row
let previousBitByteValue = {};

function handleFieldDescriptionChange(selectEl) {
    const container = selectEl.closest('.field-desc-inline');
    const bitByte = container.querySelector('.bit-byte-select');
    const arrow = container.querySelector('.arrow-btn');

    if (selectEl.value === 'Data') {
        bitByte.style.display = 'inline-block';
    } else {
        bitByte.style.display = 'none';
        arrow.style.display = 'none';
        bitByte.value = '';
    }
}

function handleBitByteChange(selectEl) {
    const container = selectEl.closest('.field-desc-inline');
    const arrow = container.querySelector('.arrow-btn');
    const row = selectEl.closest('tr');

    const currentValue = selectEl.value;
    const rowKey = row.rowIndex;
    const prevValue = previousBitByteValue[rowKey] || 'Bytewise';

    if (currentValue !== prevValue) {
        const confirmed = confirm(
            `Are you sure you want to switch from ${prevValue} to ${currentValue}?\nThis will reset Bitwise values.`
        );

        if (!confirmed) {
            selectEl.value = prevValue;
            return;
        }

        previousBitByteValue[rowKey] = currentValue;

        if (currentValue === 'Bitwise') {
            arrow.style.display = 'inline-block';
        } else {
            arrow.style.display = 'none';

            // ✅ THIS IS THE FIX
            const row = selectEl.closest('tr');
            
          restoreRowToBytewise(row);   
        }
    } else {
        arrow.style.display = currentValue === 'Bitwise' ? 'inline-block' : 'none';
    }
}

//
// Reset a row's Bitwise inputs back to editable Bytewise defaults
function restoreRowToBytewise(row) {
    if (!row) return;

    // 1️⃣ Restore original HTML (full bytewise UI)
    if (row.dataset.originalHtml) {
        row.innerHTML = row.dataset.originalHtml;
    }

    // 2️⃣ REMOVE bitwise stored data
    row.removeAttribute('data-bitwise-storage');

    // 3️⃣ Clear previous Bitwise tracking (VERY IMPORTANT)
    const rowIndex = row.rowIndex;
    if (typeof previousBitByteValue !== 'undefined') {
        delete previousBitByteValue[rowIndex];
    }
}

//


     
//
 let activeMainRow = null;
function openBitwiseConfig() {
   
 activeMainRow = document.activeElement
        ? document.activeElement.closest('tr')
        : activeMainRow;

    if (!activeMainRow) return;
 
       
    // 🔥 SAVE ORIGINAL BYTEWISE ROW ONLY ONCE
  
    
     if (!activeMainRow.dataset.originalHtml) {
        activeMainRow.dataset.originalHtml = activeMainRow.innerHTML;
    }


    // ------------------------
    // MAIN TABLE
    // ------------------------
    const columns = activeMainRow.cells;

    // Lock start and end byte inputs readonly and style them
    [1, 2].forEach(i => {
        const cell = columns[i];
        if (!cell) return;

        const input = cell.querySelector('input');
        if (input) {
            input.readOnly = true;
            input.style.backgroundColor = '#ddd';
            input.style.border = '1px solid #aaa';
            input.style.textAlign = 'center';
            input.style.color = '#555';
        }
    });

    // Show readonly boxes for other columns (index > 2 and no buttons)
    for (let i = 0; i < columns.length; i++) {
        if (i > 2 && !columns[i].querySelector('button')) {
            columns[i].style.visibility = 'visible';
            columns[i].innerHTML = '';
          
            
            // Show saved value if exists
            let valueToShow = '';
            const savedData = JSON.parse(activeMainRow.getAttribute('data-bitwise-storage') || '[]');
            if (savedData.length > 0) {
                valueToShow = savedData[0].cmd_cmd_defaultvalue_1 || '';
            }

            // Create readonly styled input box
            const box = document.createElement('input');
            box.type = 'text';
            box.readOnly = true;
            box.value = valueToShow;

            box.style.width = '50px';
            box.style.height = '25px';
            box.style.backgroundColor = '#ddd';
            box.style.border = '1px solid #aaa';
            box.style.textAlign = 'center';
            box.style.color = '#555';
            columns[i].appendChild(box);
        }
    }

    // ------------------------
    // MODAL TABLE: Load saved Bitwise values
    // ------------------------
    const tbody = document.getElementById('bitwiseTableBody');
    if (!tbody) {
        alert('Error: bitwiseTableBody element not found!');
        return;
    }
    tbody.innerHTML = ''; // clear old rows

    const savedData = JSON.parse(activeMainRow.getAttribute('data-bitwise-storage') || '[]');
    const rowsToCreate = Math.max(8, savedData.length);

    if (typeof addBitwiseRow !== 'function') {
        alert('Error: addBitwiseRow function is not defined!');
        return;
    }

    for (let i = 0; i < rowsToCreate; i++) {
        addBitwiseRow(); // your function that adds an empty row
    }

    // Fill modal table with saved data
      // Fill modal table with saved data
    const modalRows = tbody.querySelectorAll('tr');
    modalRows.forEach((row, rowIndex) => {
        const rowData = savedData[rowIndex];
        if (!rowData) return;

        const tds = row.querySelectorAll('td');
        if (!tds.length) return;

        // FIXED: Match the keys used in bitwiseStorage (no cmd_cmd_ prefix)
        if (tds[1]?.querySelector('input')) tds[1].querySelector('input').value = rowData.startBytePosition_1 || '';
        if (tds[2]?.querySelector('input')) tds[2].querySelector('input').value = rowData.endBytePosition_1 || '';
        if (tds[3]?.querySelector('input')) tds[3].querySelector('input').value = rowData.numberOfBytes_1 || '';
        if (tds[4]?.querySelector('select')) tds[4].querySelector('select').value = rowData.dataType_1 || '';
        if (tds[5]?.querySelector('select')) tds[5].querySelector('select').value = rowData.valueDefinition_1 || '';
        if (tds[6]?.querySelector('input')) tds[6].querySelector('input').value = rowData.displayValueMin_1 || '';
        if (tds[7]?.querySelector('input')) tds[7].querySelector('input').value = rowData.displayValueMax_1 || '';
        if (tds[8]?.querySelector('input')) tds[8].querySelector('input').value = rowData.valueInHexMin_1 || '';
        if (tds[9]?.querySelector('input')) tds[9].querySelector('input').value = rowData.valueInHexMax_1 || '';
        if (tds[10]?.querySelector('select')) tds[10].querySelector('select').value = rowData.uiType_1 || '';
        if (tds[11]?.querySelector('input')) tds[11].querySelector('input').value = rowData.uiId_1 || '';
        if (tds[12]?.querySelector('input')) tds[12].querySelector('input').value = rowData.asperui_1 || '';
        if (tds[13]?.querySelector('input')) tds[13].querySelector('input').value = rowData.sno_asperui_1 || '';
        if (tds[14]?.querySelector('input')) tds[14].querySelector('input').value = rowData.asperuivalue_1 || '';
        if (tds[15]?.querySelector('input')) tds[15].querySelector('input').value = rowData.uiPageNo_1 || '';
        if (tds[16]?.querySelector('select')) tds[16].querySelector('select').value = rowData.uiDescriptionInOut_1 || '';
        if (tds[17]?.querySelector('input')) tds[17].querySelector('input').value = rowData.sliderStepValue_1 || '';
        if (tds[18]?.querySelector('input')) tds[18].querySelector('input').value = rowData.defaultvalue_1 || '';
        if (tds[19]?.querySelector('input')) tds[19].querySelector('input').value = rowData.developerNotes_1 || '';
        // Add notification fields if they are in your UI
        if (tds[20]?.querySelector('select')) tds[20].querySelector('select').value = rowData.notifEnable_1 || 'No';
        if (tds[21]?.querySelector('input')) tds[21].querySelector('input').value = rowData.notifMsg_1 || '';
    });

const pageNumberElement = document.getElementById('bitwise-page-number');
if (pageNumberElement) {
    // We set the text here. Let's also set a data attribute as a backup.
    pageNumberElement.innerText = `Cmd[${currentPageIndex}]`; 
    pageNumberElement.setAttribute('data-value', currentPageIndex); 
    console.log("Setting Modal Page to:", currentPageIndex);
}


    // Show modal
    const modal = document.getElementById('bitwiseConfigModal');
    if (!modal) {
        alert('Error: bitwiseConfigModal element not found!');
        return;
    }
    modal.style.display = 'flex';
}
   
 //
/**
 * 3. CLOSE
 */
function closeBitwiseConfig(event) {
  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }
  document.getElementById('bitwiseConfigModal').style.display = 'none';
}

//


// Your existing function remains the same

function handleUiTypeChange(uiTypeSelect) {
    const row = uiTypeSelect.closest('tr');
    
    // Find the UI ID input in this specific row
       const uiInOutSelect = row.querySelector('.ui-inout-select');

    // SAFETY: If the UI ID already has a specific number (not 1 or empty), 
    // do not let the UI Type change it.
   
    // Handle the Read/Write logic as you did before
    if (uiInOutSelect) {
        if (uiTypeSelect.value === 'Status LED' || uiTypeSelect.value === 'Network Icon') {
            uiInOutSelect.value = 'Read';
            uiInOutSelect.disabled = true;
        } else {
            uiInOutSelect.disabled = false;
        }
    }
}

//save bitwise
function saveBitwiseConfig(event) {
    
      if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
const tbody = document.getElementById('bitwiseTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    let allValid = true;
    let overlapError = false;
    let errorMsg = "";

    // --- NEW: BIT TRACKER ---
    // This array represents bits 0 to 7 of the current byte
    const bitTracker = new Array(8).fill(false);


           
    // 1. Prepare storage for every single field
    const sync = {
        start: [], end: [], bits: [], type: [], def: [], 
        dMin: [], dMax: [], hMin: [], hMax: [], uiType: [], 
        uiId: [], asper: [], serial: [], vals: [], page: [], 
        inout: [], step: [], dflt: [], notes: [],notifEnable: [],          // ✅ ADD
    notifMsg: []   
    };

    // 🔹 ADDITION: row-wise storage for reopen
     
    
const bitwiseStorage = [];

    rows.forEach((row, idx) => {
        const getInput = (n) => row.querySelector(`td:nth-child(${n}) input`);
        const getSelect = (n) => row.querySelector(`td:nth-child(${n}) select`);
     

    const startBit = parseInt(getInput(2)?.value);
        const endBit = parseInt(getInput(3)?.value);

        // --- 1. OVERLAP VALIDATION ---
        if (!isNaN(startBit) && !isNaN(endBit)) {
            for (let i = startBit; i <= endBit; i++) {
                if (bitTracker[i]) {
                    overlapError = true;
                    errorMsg = `❌ Bit ${i} is repeated! Check Row ${idx + 1}.`;
                    getInput(2).style.border = "2px solid red";
                    getInput(3).style.border = "2px solid red";
                }
                bitTracker[i] = true;
            }
        }
        // Capture data from Modal
        const rowData = {
           startBit: getInput(2)?.value || "",
            endBit: getInput(3)?.value || "",
            noOfBits: getInput(4)?.value || "",
            dataType: getSelect(5)?.value || "",
            valueDefinition: getSelect(6)?.value || "",
            displayMin: getInput(7)?.value || "",
            displayMax: getInput(8)?.value || "",
            hexMin: getInput(9)?.value || "",
            hexMax: getInput(10)?.value || "",
            uiType: getSelect(11)?.value || "",
            uiId: getInput(12)?.value || "",
            asperUi: getInput(13)?.value || "",
            uiSerialNo: getInput(14)?.value || "",
            uiValues: getInput(15)?.value || "",
            uiPageNo: getInput(16)?.value || "",
            uiInOut: row.querySelector('.ui-inout-select')?.value || "",
            sliderStep: getInput(18)?.value || "",
            defaultValue: getInput(19)?.value || "",
            devNotes: getInput(20)?.value || "",
           

           };

const notifEnableEl = getSelect(21);
const notifMsgEl    = getInput(22);

rowData.notifEnable   = notifEnableEl?.value || "No";
rowData.notifMsgInput = notifMsgEl?.value || "";

// --- VALIDATION RULE ---
if (rowData.notifEnable === "Yes") {
    if (!rowData.notifMsgInput.trim()) {
        if (notifMsgEl) notifMsgEl.style.border = "2px solid red";
        allValid = false;
    } else {
        if (notifMsgEl) notifMsgEl.style.border = "1px solid #ccc";
    }
} else {
    if (notifMsgEl) notifMsgEl.style.border = "1px solid #ccc";
}
        // Fill sync arrays
        sync.start.push(rowData.startBit);
        sync.end.push(rowData.endBit);
        sync.bits.push(rowData.noOfBits);
        sync.type.push(rowData.dataType);
        sync.def.push(rowData.valueDefinition);
        sync.dMin.push(rowData.displayMin);
        sync.dMax.push(rowData.displayMax);
        sync.hMin.push(rowData.hexMin);
        sync.hMax.push(rowData.hexMax);
        sync.uiType.push(rowData.uiType);
        sync.uiId.push(rowData.uiId);
        sync.asper.push(rowData.asperUi);
        sync.serial.push(rowData.uiSerialNo);
        sync.vals.push(rowData.uiValues);
        sync.page.push(rowData.uiPageNo);
        sync.inout.push(rowData.uiInOut);
        sync.step.push(rowData.sliderStep);
        sync.dflt.push(rowData.defaultValue);
        sync.notes.push(rowData.devNotes);
        sync.notifEnable.push(rowData.notifEnable);
        
        sync.notifMsg.push(rowData.notifMsgInput || "");
       
        // 🔹 ADDITION: store per-row object for reopen
        bitwiseStorage.push({
            startBytePosition_1: rowData.startBit,
            endBytePosition_1: rowData.endBit,
            fieldDescription_1: rowData.fieldDescription, 
            numberOfBytes_1: rowData.noOfBits,
           
            dataType_1: rowData.dataType,
            valueDefinition_1: rowData.valueDefinition,
            displayValueMin_1: rowData.displayMin,
            displayValueMax_1: rowData.displayMax,
            valueInHexMin_1: rowData.hexMin,
            valueInHexMax_1: rowData.hexMax,
            uiType_1: rowData.uiType,
            uiId_1: rowData.uiId,
            asperui_1: rowData.asperUi,
            sno_asperui_1: rowData.uiSerialNo,
            asperuivalue_1: rowData.uiValues,
            uiPageNo_1: rowData.uiPageNo,
            uiDescriptionInOut_1: rowData.uiInOut,
            sliderStepValue_1: rowData.sliderStep,
            defaultvalue_1: rowData.defaultValue,
            developerNotes_1: rowData.devNotes,
            notifEnable_1: rowData.notifEnable,
            notifMsg_1: rowData.notifMsgInput?.value || "",
        });
    });

if (overlapError) {
        alert(errorMsg); // This will now trigger if you give 1,1 and 1,1
        return; // Stop the save
    }

    if (!allValid) {
        alert("❌ Please enter the Notification Message for all enabled rows.");
        return;
    }
const pageElem = document.getElementById('bitwise-page-number');
let actualPage = 0;

if (pageElem) {
    // This regex looks for any number inside the text "Cmd[2]" -> results in 2
    const match = pageElem.innerText.match(/\d+/);
    actualPage = match ? match[0] : 0;
}

if (!allValid) {
    forceMessage("❌ Please enter the Notification Message for all enabled rows.");
    return;
}

    // 🔹 ADDITION: save for openBitwiseConfig()
   // 🔹 ADDITION: save for openBitwiseConfig()
if (activeMainRow) {
    // Store for JS
    activeMainRow.setAttribute(
        'data-bitwise-storage',
        JSON.stringify(bitwiseStorage)
    );

    // Store for form submit
    let hidden = activeMainRow.querySelector('input[name="cmd_bitwise_json[]"]');
    if (!hidden) {
        hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'cmd_bitwise_json[]';
        activeMainRow.appendChild(hidden);
    }
    hidden.value = JSON.stringify(bitwiseStorage);
}
// 🔹 CRITICAL: merge bitwise into storedCommands

if (activeMainRow && storedCommands[actualPage]) {
    const rowIndex = activeMainRow.rowIndex - 1;
    storedCommands[actualPage].rows[rowIndex].bitwise = bitwiseStorage;
}

// 1. First, clear the previous logs to avoid confusion

console.log("%c --- FINAL BITWISE CONFIGURATION ---", "color: green; font-weight: bold;");

// 2. Iterate through the bitwiseStorage you just created

    bitwiseStorage.forEach((bitData, bitIdx) => {
        
        // Define the identifiers
        const cmdPage = currentPageIndex; // The Page/Cmd number
        const rowNo = activeMainRow.rowIndex; // The Row number in the main table
        const bitNo = bitIdx + 1; // The sequence of the bit (1, 2, 3...)

        // Create the prefix string
        const p = `Cmd[${cmdPage}]-row[${rowNo}]-bit[${bitNo}]`;


                // Print each value clearly
        // We use backticks and ${} to inject the values directly into the string
        console.log(`${p}-startByte => ${bitData.cmd_cmd_startBytePosition_1}`);
        console.log(`${p}-endByte => ${bitData.cmd_cmd_endBytePosition_1}`);
        console.log(`${p}-bitSize => ${bitData.cmd_cmd_numberOfBytes_1}`);
        console.log(`${p}-fieldDescription => ${bitData.cmd_cmd_valueDefinition_1}`);
        console.log(`${p}-dataType => ${bitData.cmd_cmd_dataType_1}`);
        console.log(`${p}-uiType => ${bitData.cmd_cmd_uiType_1}`);
        console.log(`${p}-uiPageNo => ${bitData.cmd_cmd_uiPageNo_1}`);
        console.log(`${p}-defaultValue => ${bitData.cmd_cmd_defaultvalue_1}`);

        // 🔹 NEW FIELDS ADDED BELOW 🔹
        console.log(`${p}-valueDefinition => ${bitData.cmd_cmd_valueDefinition_1}`);
        console.log(`${p}-displayMin => ${bitData.cmd_cmd_displayValueMin_1}`);
        console.log(`${p}-displayMax => ${bitData.cmd_cmd_displayValueMax_1}`);
        console.log(`${p}-hexMin => ${bitData.cmd_cmd_valueInHexMin_1}`);
        console.log(`${p}-hexMax => ${bitData.cmd_cmd_valueInHexMax_1}`);
        console.log(`${p}-uiId => ${bitData.cmd_cmd_uiId_1}`);
        console.log(`${p}-uiSerialNo => ${bitData.cmd_cmd_sno_asperui_1}`);
        console.log(`${p}-uiValues => ${bitData.cmd_cmd_asperuivalue_1}`);
        console.log(`${p}-uiInOut => ${bitData.cmd_cmd_uiDescriptionInOut_1}`);
        console.log(`${p}-sliderStep => ${bitData.cmd_cmd_sliderStepValue_1}`);
        console.log(`${p}-devNotes => ${bitData.cmd_cmd_developerNotes_1}`);
        
        // Visual separator for the next bit
        console.log("---------------------------------------");
    });

// Later, for printing
const bitwiseArray = JSON.parse(activeMainRow.getAttribute('data-bitwise-storage'));
console.log('Bitwise Array:', bitwiseArray); // Array type in console

    // 2. The Bridge to Main Table (UNCHANGED)
    
        if (activeMainRow) {
        const updateCell = (cellIdx, arr) => {
    const cell = activeMainRow.cells[cellIdx];
    if (cell) {
        let mainElement = cell.querySelector('input, select');
        const arrayString = `[${arr.join(',')}]`;
        
        if (mainElement) {
            // REMOVE validation limits if this is the UI ID column (13)
            if (cellIdx === 13) {
                mainElement.removeAttribute('min');
                mainElement.removeAttribute('max');
                mainElement.removeAttribute('pattern');
            }

            if (mainElement.tagName === 'SELECT') {
                const newInput = document.createElement('input');
                newInput.type = 'text';
                newInput.className = 'form-control';
                newInput.value = arrayString;
                mainElement.parentNode.replaceChild(newInput, mainElement);
            } else {
                mainElement.type = 'text';
                mainElement.value = arrayString;
            }
        } else {
            cell.textContent = arrayString;
        }
    }
};


        updateCell(1, sync.start);   
        updateCell(2, sync.end);
        updateCell(4, sync.bits);     
        updateCell(5, sync.bits);    
        updateCell(6, sync.type);    
        updateCell(7, sync.def);     
        updateCell(8, sync.dMin);    
        updateCell(9, sync.dMax); 
        updateCell(10, sync.hMin);
        updateCell(11, sync.hMax);   
        updateCell(12, sync.uiType);
        updateCell(13, sync.uiId);
        updateCell(14, sync.asper);
        updateCell(15, sync.serial); 
        updateCell(16, sync.vals);   
        updateCell(17, sync.page);   
        updateCell(18, sync.inout);  
        updateCell(19, sync.step);   
        updateCell(20, sync.dflt);   
        updateCell(21, sync.notes);  
         }

    alert("Full configuration applied to Command Frame.");
    closeBitwiseConfig(); 
}


//


  //


// Add this logic to your "onchange" for the dropdown
function handleSelectChange(selectElement) {
    if (selectElement.value === "Bitwise") {
        const parentRow = selectElement.closest('tr');
        // Clear the old flat values so they don't print
        parentRow.querySelectorAll('input[name^="cmd_cmd_"]').forEach(input => {
            if (!input.classList.contains('stay-active')) {
                input.value = ""; 
            }
        });
    }
}     


//

//
function addBitwiseRow(btn = null) {
    const tbody = document.getElementById('bitwiseTableBody');
    const rowCount = tbody.querySelectorAll('tr').length;

    if (rowCount >= 8) {
        alert("Maximum 8 rows allowed.");
        return; 
    }

    const i = rowCount + 1;
    const row = document.createElement('tr');

    row.innerHTML = `
        <td>${i}</td>
        <td><input type="number"></td>
        <td><input type="number"></td>
        <td><input type="number" readonly></td>
        <td>
            <select>
                <option value="">Select</option>
                <option value="UnsignedInteger">Unsigned Integer</option>
                <option value="SignedInteger">Signed Integer</option>
                <option value="Boolean">Boolean</option>
                <option value="Float">Float</option>
                <option value="Double">Double</option>
                <option value="Binary">Binary</option>
                <option value="ASCII">ASCII</option>
                <option value="String">String</option>
                <option value="Hex">Hex</option>
                <option value="Enum">Enum</option>
                <option value="Timestamp">Timestamp</option>
                <option value="None">None</option>
            </select>
        </td>
        <td>
            <select>
                <option value="">Select</option>
                <option value="Fixed">Fixed Value</option>
                <option value="Range">Range Value</option>
                <option value="Dynamic Value">Dynamic Value</option>
                <option value="Enum Value">Enum Value</option>
                <option value="Calculated Value">Calculated Value</option>
                <option value="None">None</option>
            </select>
        </td>
        <td><input type="number"></td>
        <td><input type="number"></td>
        <td><input type="text"></td>
        <td><input type="text"></td>
        <td>
            <select name="uiType_${i}">
                <option value="">Select</option>
                <option value="Toggle Switch">Toggle Switch</option>
                <option value="Checkbox">Checkbox</option>
                <option value="Power Button">Power Button</option>
                <option value="Text Trigger">Text Trigger</option>
                <option value="Status LED">Status LED</option>
                <option value="Lock Icon">LOCK Icon</option>
                <option value="Alaram Icon">Alaram Icon</option>
                <option value="Eye Icon">Eye Icon</option>
                <option value="Network Icon">Network Icon</option>
            </select>
        </td>
        <td><input type="number"></td> <td><input type="text"></td>   <td><input type="number"></td> <td><input type="text"></td>   <td><input type="number"></td> <td>
            <select class="ui-inout-select">
                <option value="">Select</option>
                <option value="Read">Read</option>
                <option value="Write">Write</option>
                <option value="Read/Write">Read/Write</option>
            </select>
        </td>
        <td><input type="number"></td>
        <td><input type="text"></td>
        <td><input type="text"></td>
        <td>
        <select class="notif-enable">
            <option value="No">No</option>
            <option value="Yes">Yes</option>
        </select>
    </td>
    <td><input type="text" class="notif-message" placeholder="Enter message..."></td>

        <td style="text-align:center;">
            <button type="button" onclick="addBitwiseRow(this)">➕</button>
            <button type="button" onclick="removeBitwiseRow(this)">❌</button>
        </td>
    `;

    if (btn && btn.tagName === 'BUTTON') {
        const currentRow = btn.closest('tr');
        currentRow.insertAdjacentElement('afterend', row);
    } else {
        tbody.appendChild(row);
    }

    // --- Locking System-Controlled Fields ---
    row.querySelector('td:nth-child(4) input').readOnly = true;  // No of Bits
    row.querySelector('td:nth-child(7) input').readOnly = true;  // Display Min
    row.querySelector('td:nth-child(8) input').readOnly = true;  // Display Max
    row.querySelector('td:nth-child(12) input').readOnly = true; // UI Serial / UI ID
    row.querySelector('td:nth-child(7) input').style.background = "#f2f2f2";  // Min
    row.querySelector('td:nth-child(8) input').style.background = "#f2f2f2";  // Max
    row.querySelector('td:nth-child(4) input').style.background = "#f2f2f2";  // No of Bits
    row.querySelector('td:nth-child(13) input').style.background = "#f2f2f2"; // UI ID

    // --- 1. UI Type Listener ---
    const uiSelect = row.querySelector(`select[name^="uiType_"]`);
    uiSelect.addEventListener('change', function() {
        const typeLower = (this.value || '').toLowerCase();
        const dataTypeSelect  = row.querySelector('td:nth-child(5) select');   
        const valueDefSelect  = row.querySelector('td:nth-child(6) select');   
        const displayMinInput = row.querySelector('td:nth-child(7) input');  
        const displayMaxInput = row.querySelector('td:nth-child(8) input');  
        const hexMinInput     = row.querySelector('td:nth-child(9) input');       
        const hexMaxInput     = row.querySelector('td:nth-child(10) input');     
        const uiInOutSelect   = row.querySelector('.ui-inout-select');
        const asperUiInput    = row.querySelector('td:nth-child(12) input');    
        const uiSerialInput   = row.querySelector('td:nth-child(13) input');   
        const uiValuesInput   = row.querySelector('td:nth-child(14) input');   

        const autoFillList = ['toggle switch','checkbox','power button','text trigger','lock icon','alaram icon','eye icon','network icon','status led'];

        if (autoFillList.includes(typeLower)) {
            dataTypeSelect.value = 'UnsignedInteger';
            valueDefSelect.value = 'Fixed';
            displayMinInput.value = 0;
            displayMaxInput.value = 1;
            hexMinInput.value = '0';
            hexMaxInput.value = '1';
            if (asperUiInput) asperUiInput.value = "false,true";
           if (uiSerialInput) uiSerialInput.value = "1";
            if (uiValuesInput) uiValuesInput.value = "0,1";

           
            uiInOutSelect.value = (typeLower === 'status led' || typeLower === 'network icon') ? 'Read' : 'Read/Write';
        }
    });

    // --- 2. Data Type Listener ---
    const dataTypeSelectForAuto = row.querySelector('td:nth-child(5) select');
    dataTypeSelectForAuto.addEventListener('change', function() {
        const displayMinInput = row.querySelector('td:nth-child(7) input');
        const displayMaxInput = row.querySelector('td:nth-child(8) input');
        const hexMinInput     = row.querySelector('td:nth-child(9) input');
        const hexMaxInput     = row.querySelector('td:nth-child(10) input');

        // This triggers for ANY data type selection
        if (this.value !== "") {
            displayMinInput.value = 0;
            displayMaxInput.value = 1;
            hexMinInput.value = '0';
            hexMaxInput.value = '1';
        }
    });

    // --- 3. Strict 0 or 1 Restriction (Universal) ---
    const minInput = row.querySelector('td:nth-child(7) input');
    const maxInput = row.querySelector('td:nth-child(8) input');

    [minInput, maxInput].forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key !== "0" && e.key !== "1") {
                e.preventDefault();
            }
        });
        input.addEventListener('input', function() {
            if (this.value !== "" && this.value !== "0" && this.value !== "1") {
                this.value = ""; 
            }
        });
    });

    // --- 4. Bit Calculation ---
    
// Use class selectors instead of nth-child to prevent column mismatch
const startBitInput = row.querySelector('td:nth-child(2) input');
    const endBitInput   = row.querySelector('td:nth-child(3) input');
    const noOfBitsInput = row.querySelector('td:nth-child(4) input');

    function recalcBits() {
        const start = parseInt(startBitInput.value);
        const end   = parseInt(endBitInput.value);
        if (!isNaN(start) && !isNaN(end) && end >= start) {
            noOfBitsInput.value = (end - start) + 1;
        } else {
            noOfBitsInput.value = '';
        }
    }

    startBitInput.addEventListener('input', recalcBits);
    endBitInput.addEventListener('input', recalcBits);

// 1. Target the UI ID input
// 1. Target the UI ID input
// Target Column 13 (UI ID)
const uiIdInput = row.querySelector('td:nth-child(12) input'); 

uiIdInput.addEventListener('click', function () {
    // Look at the first cell (S.No) to see what the row number is
    const sNo = row.querySelector('td:first-child').innerText;
    
    // If S.No is empty, fallback to counting previous rows
    const currentNumber = sNo || (Array.from(row.parentNode.children).indexOf(row) + 1);

    if (this.value !== "None" && this.value !== "") {
        this.value = "None";
    } else {
        this.value = currentNumber; // This will now correctly give 2, 3, 4...
    }
});

updateBitwiseRowNumbers();
}
// 2. Set the ID and "Lock" it into the element's memory
// We use PHP to pass the correct row number (i) into JavaScript
//
//
function removeBitwiseRow(btn) {
    const tbody = document.getElementById('bitwiseTableBody');

    if (tbody.querySelectorAll('tr').length === 1) {
        alert("At least one row is required");
        return;
    }

    btn.closest('tr').remove();
    updateBitwiseRowNumbers();
}
//
function updateBitwiseRowNumbers() {
    const rows = document.querySelectorAll('#bitwiseTableBody tr');
    rows.forEach((row, index) => {
        const newNum = index + 1;
        row.cells[0].innerText = newNum;
        
        const uiIdInput = row.querySelector('td:nth-child(12) input');
        if (uiIdInput && uiIdInput.value !== "None") {
            uiIdInput.value = newNum; // This will now correctly show 2, 3, 4...
        }
    });
}
//
 
// --- MUST GO AT THE VERY TOP OF YOUR FILE ---

// 🔄 RESET STORAGE ON PAGE LOAD (FIXES YOUR ISSUE)
window.addEventListener("load", () => {
    sessionStorage.removeItem('frame_command');
    sessionStorage.removeItem('frame_response');
    sessionStorage.removeItem('frame_error');
    console.log("System: Frame session reset");
});

// ✅ CHECK WHAT IS SAVED
function getRemainingFrames() {
    const isCommandDone = sessionStorage.getItem('frame_command') === 'true';
    const isResponseDone = sessionStorage.getItem('frame_response') === 'true';
    const isErrorDone = sessionStorage.getItem('frame_error') === 'true';

    let missing = [];
    if (!isCommandDone) missing.push("Command Frame");
    if (!isResponseDone) missing.push("Response Frame");
    if (!isErrorDone) missing.push("Error Frame");

    return missing;
}

// 🟦 COMMAND FRAME BUTTON

function saveCommandConfiguration() {
    // Capture latest table state
    saveCurrentCommandState(commandCount - 1);
const activeIdx = commandCount - 1; // Page 1 = 0, Page 2 = 1

    // 1. ENSURE OBJECT EXISTS IN ARRAY
    if (!storedCommands[activeIdx]) {
        storedCommands[activeIdx] = { rows: [], meta: {} };
    }

    // 2. SAVE TABLE STATE
    saveCurrentCommandState(activeIdx);
   
    
    // STEP 1 — PRODUCT INFO (Print this FIRST)
    const productInfo = {
        parameterName: document.getElementById("par_parametername")?.value || "",
        productName: document.getElementById("productname")?.value || "",
        brandName: document.getElementById("brandname")?.value || "",
        modelName: document.getElementById("modelname")?.value || "",
        date: document.querySelector("input[name='date']")?.value || "",
        sequence: document.getElementById("Sequence")?.value || "",
        totalCommandFrames: document.getElementById("numberofpages")?.value || "",
        totalResponseFrames: document.getElementById("page")?.value || "",
        totalErrorFrames: document.getElementById("pages")?.value || "",
        pageNames: document.getElementById("pagenames")?.value || "",
        productDescription: document.getElementById("productdescription")?.value || ""
    };

    console.log("%c========= 1. PRODUCT INFORMATION =========", "color:blue;font-weight:bold;font-size:12px;");
    Object.entries(productInfo).forEach(([k, v]) => console.log(`PRODUCT-${k} => ${v}`));

    // --- ADDED CODE: CAPTURE AND ATTACH META DATA ---
    const currentMeta = {
        parametername: document.getElementById('com_parametername')?.value || "",
        noofrows: document.getElementById('cmd_noofrows')?.value || "0",
        remarks: document.getElementById('cmd_remarks')?.value || "",
        dataflowdirection: document.getElementById('cmd_data_flow_direction')?.value || "",
        frametype: document.getElementById('cmd_frametype')?.value || "",
        framesize_bytes: document.getElementById('cmd_framesizeinbytes')?.value || "",
        frametrigger: document.getElementById('cmd_frame_trigger_options')?.value || "",
        multipleui: document.getElementById('cmd_multipleui')?.value || "",
        triggerui: document.getElementById('cmd_triggerui')?.value || "",
        voicecommand: document.getElementById('cmd_voice_command')?.value || "",
        voicecontrol: document.getElementById('cmd_voice_control_instruction')?.value || ""
    };

    // This ensures the Meta Data is stored for Page 1 (0), Page 2 (1), etc.
        // STEP 2 & 3 — LOOP THROUGH PAGES (Print Meta then Table)
    storedCommands.forEach((frame, fIndex) => {
        console.log(`%c\n========= PAGE DATA: Cmd[${fIndex}] =========`, "color:green;font-weight:bold;border-bottom:1px solid green;");

        // --- A. PRINT META DATA NEXT ---
        console.log(`%c[Cmd-${fIndex}] 2. META DATA`, "color:purple;font-weight:bold");
        
        // Use the meta stored in the frame (if it exists)
        const metaToPrint = frame.meta || currentMeta; 
        Object.entries(metaToPrint).forEach(([k, v]) => {
            console.log(`Cmd[${fIndex}]-meta-${k} => ${v}`);
        });

        // --- B. PRINT TABLE VALUES LAST ---
        console.log(`%c[Cmd-${fIndex}] 3. TABLE VALUES`, "color:brown;font-weight:bold");
        const rows = frame.rows || [];
        rows.forEach((row, rIndex) => {
            console.log(`%c--- Row[${rIndex + 1}] ---`, "color:gray;font-style:italic");
            
            for (const [key, value] of Object.entries(row)) {
                if (key !== "bitwise") {
                    const cleanKey = key.replace("cmd_", "");
                    console.log(`Cmd[${fIndex}]-row[${rIndex + 1}]-${cleanKey} => ${value}`);
                }
            }

            // --- BITWISE DATA (Inside Table) ---
            if (Array.isArray(row.bitwise)) {
                row.bitwise.forEach((bit, bIndex) => {
                    const p = `Cmd[${fIndex}]-row[${rIndex + 1}]-bit[${bIndex + 1}]`;
                    Object.entries(bit).forEach(([k, v]) => {
                        const clean = k.replace("cmd_cmd_", "");
                        console.log(`${p}-${clean} => ${v}`);
                    });
                });
            }
        });
    });

             sessionStorage.setItem('frame_command', 'true');
storedCommands[activeIdx].meta = currentMeta;
    
    if (typeof printMasterConsole === "function") printMasterConsole();
      showRemainingFramesAlert();
   
        alert("✅ Configuration saved! ");
}
      
//
// 🟩 RESPONSE FRAME BUTTON
function saveResponseConfiguration() {
    

    // 1. SAVE STATE: Capture current table rows into the array
    saveCurrentResponseState(responseCount - 1);
    const activeIdx = responseCount - 1;

    if (!storedResponses[activeIdx]) {
        storedResponses[activeIdx] = { rows: [], meta: {} };
    }

    saveCurrentResponseState(activeIdx);

    // 2. CAPTURE META: One proper meta object from the UI
    const currentMeta = {
        parametername: document.getElementById('resp_parametername')?.value || "",
        noofrows: document.getElementById('resp_noofrows')?.value || "0",
        remarks: document.getElementById('resp_remarks')?.value || "",
        dataflowdirection: document.getElementById('resp_data_flow_direction')?.value || "",
        frametype: document.getElementById('resp_frametype')?.value || "",
        framesize_bytes: document.getElementById('resp_framesizeinbytes')?.value || "",
        frametrigger: document.getElementById('resp_frametrigger')?.value || "",
        multipleui: document.getElementById('resp_multipleui')?.value || "",
        triggerui: document.getElementById('resp_triggerui')?.value || "",
        voicecommand: document.getElementById('resp_voice_command')?.value || "",
        voicecontrol: document.getElementById('resp_voice_control_instruction')?.value || ""
    };

    // 3. ATTACH META: Bind the meta data to the specific frame in storage
    if (storedResponses[responseCount - 1]) {
        storedResponses[responseCount - 1].meta = currentMeta;

    }

    // --- DISPLAY SEQUENCE ---

    // STEP 1: PRODUCT INFORMATION
    const productInfo = {
        parameterName: document.getElementById("par_parametername")?.value || "",
        productName: document.getElementById("productname")?.value || "",
        brandName: document.getElementById("brandname")?.value || "",
        modelName: document.getElementById("modelname")?.value || "",
                 date: document.querySelector("input[name='date']")?.value || "",
        sequence: document.getElementById("Sequence")?.value || "",

        totalCommandFrames: document.getElementById("numberofpages")?.value || "",
        totalResponseFrames: document.getElementById("page")?.value || "",
        totalErrorFrames: document.getElementById("pages")?.value || "",
        pageNames: document.getElementById("pagenames")?.value || "",
        productDescription: document.getElementById("productdescription")?.value || ""

    };

    console.log("%c========= 1. PRODUCT INFORMATION =========", "color:blue;font-weight:bold;font-size:12px;");
    Object.entries(productInfo).forEach(([k, v]) => console.log(`PRODUCT-${k} => ${v}`));

    // STEP 2 & 3: PAGE-WISE DATA
    storedResponses.forEach((frame, fIndex) => {
        console.log(`%c\n========= PAGE DATA: Res[${fIndex}] =========`, "color:darkgreen;font-weight:bold;");

        // --- STEP 2: META DATA ---
        console.log(`%c[Res-${fIndex}] 2. META DATA`, "color:purple;font-weight:bold");
        const metaToLog = frame.meta || currentMeta; // Use stored meta or current fallback
        Object.entries(metaToLog).forEach(([k, v]) => {
            console.log(`Res[${fIndex}]-meta-${k} => ${v}`);
        });

        // --- STEP 3: TABLE DATA ---
        console.log(`%c[Res-${fIndex}] 3. TABLE DATA`, "color:brown;font-weight:bold");
        const rows = frame.rows || [];
        rows.forEach((row, rIndex) => {
            console.log(`%c--- Row[${rIndex + 1}] ---`, "color:gray;font-style:italic");
            
            for (const [key, value] of Object.entries(row)) {
                if (key !== "bitwise") {
                    const cleanKey = key.replace("resp_", "");
                    console.log(`Res[${fIndex}]-row[${rIndex + 1}]-${cleanKey} => ${value}`);
                }
            }

            // --- BITWISE DATA ---
            if (Array.isArray(row.bitwise)) {
                row.bitwise.forEach((bit, bIndex) => {
                    const p = `Res[${fIndex}]-row[${rIndex + 1}]-bit[${bIndex + 1}]`;
                    Object.entries(bit).forEach(([k, v]) => {
                        const clean = k.replace("resp_resp_", "");
                        console.log(`${p}-${clean} => ${v}`);
                    });
                });
            }
        });
    });

    sessionStorage.setItem('frame_response', 'true');
    storedResponses[activeIdx].meta = currentMeta;
    
    // Check for other save flags (Cumulative printing)
    if (typeof printMasterConsole === "function") printMasterConsole();
    showRemainingFramesAlert();
    alert("✅ Configuration saved!");
}
// 🟥 ERROR FRAME BUTTON (FINAL STEP)
//

function saveErrorConfiguration() {
    // 1. Sync the current visible state
    const activeErrIdx = errorCount - 1;
    saveCurrentErrorState(activeErrIdx);

    // 2. CREATE A COMPLETE META OBJECT
    // This collects data from your inputs to apply to ALL frames
    
   const fullMeta = {
    parametername: document.getElementById('error_parametername')?.value || "",
    noofrows: document.getElementById('error_noofrows')?.value || "0",
    remarks: document.getElementById('error_remarks')?.value || "",
    dataflowdirection: document.getElementById('error_data_flow_direction')?.value || "",
    frametype: document.getElementById('error_frametype')?.value || "ERROR",
    framesize_bytes: document.getElementById('error_framesizeinbytes')?.value || "",
    frametrigger: document.getElementById('error_frametrigger')?.value || "",
    multipleui: document.getElementById('error_multipleui')?.value || "",
    triggerui: document.getElementById('error_triggerui')?.value || "",
    voicecommand: document.getElementById('error_voice_command')?.value || "",
    voicecontrol: document.getElementById('error_voice_control_instruction')?.value || ""
};

    // 3. FORCE META INTO EVERY STORED FRAME (Commands, Responses, and Errors)
    // This fixes the "No Meta Data" issue for previous pages
    if (typeof storedCommands !== 'undefined') {
        storedCommands.forEach(f => { if(!f.meta) f.meta = fullMeta; });
    }
    if (typeof storedResponses !== 'undefined') {
        storedResponses.forEach(f => { if(!f.meta) f.meta = fullMeta; });
    }
    if (typeof storedErrors !== 'undefined') {
        storedErrors.forEach(f => { if(!f.meta) f.meta = fullMeta; });
    }

    // 4. GATHER PRODUCT INFO FOR THE HEADER
    // Ensure these IDs match your HTML input IDs exactly
    const productInfo = {
        brandname: document.getElementById("brandname")?.value || "N/A",
        productname: document.getElementById("productname")?.value || "N/A",
        modelname: document.getElementById("modelname")?.value || "N/A",
        date: document.querySelector("input[name='date']")?.value || "N/A",
        parameter_name: document.getElementById('error_parametername')?.value || "N/A",
        page_number: document.getElementById("pages")?.value || "1", 
        sequence_number: document.getElementById("Sequence")?.value || "1",
        product_description: document.getElementById("error_remarks")?.value || "N/A"
    };

    const formData = new FormData();
    Object.entries(productInfo).forEach(([k, v]) => formData.append(k, v));

    const masterData = {
        commands: typeof storedCommands !== 'undefined' ? storedCommands : [],
        responses: typeof storedResponses !== 'undefined' ? storedResponses : [],
        errors: typeof storedErrors !== 'undefined' ? storedErrors : []
    };
    formData.append('full_config_json', JSON.stringify(masterData));

    // 5. SEND TO API
    fetch('<?php echo get_uri("items/saveapi"); ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showApiResponsePopup(data.print_r_output);
        } else {
            alert("❌ Error: " + data.message);
        }
    })
    .catch(error => alert("❌ API Connection Failed"));

    return false;
}

// Helper to show the form results on screen
function showApiResponsePopup(content) {
    const old = document.getElementById('api-overlay');
    if (old) old.remove();

    const overlay = document.createElement('div');
    overlay.id = 'api-overlay';
    overlay.style = "position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; display:flex; align-items:center; justify-content:center; padding:20px;";

    overlay.innerHTML = `
        <div style="background:#fff; width:95%; max-height:95%; padding:25px; border-radius:10px; overflow:auto; font-family:monospace; box-shadow:0 0 20px #000;">
            <div style="display:flex; justify-content:space-between; position:sticky; top:0; background:#fff; padding-bottom:10px; border-bottom:2px solid #333;">
                <h2 style="margin:0; color:#2c3e50;">📄 FULL API DATA PREVIEW</h2>
                <button onclick="document.getElementById('api-overlay').remove()" style="background:#e74c3c; color:white; border:none; padding:8px 15px; cursor:pointer; font-weight:bold; border-radius:5px;">CLOSE</button>
            </div>
            <pre style="margin-top:20px; font-size:13px; color:#2c3e50; line-height:1.4;">${content}</pre>
        </div>
    `;
    document.body.appendChild(overlay);
}

//

function printMasterConsole() {
    console.clear();
    console.log("%c==================== MASTER CUMULATIVE LOG ====================", "background: #222; color: #fff; font-size: 16px; font-weight: bold; padding: 5px;");

    // 1. PRODUCT INFO (Always prints first)
    console.log("%c========= 1. PRODUCT INFORMATION =========", "color:blue;font-weight:bold");
    const pInfo = {
        parameterName: document.getElementById("par_parametername")?.value || "",
        productName: document.getElementById("productname")?.value || "",
        brandName: document.getElementById("brandname")?.value || "",
        modelName: document.getElementById("modelname")?.value || "",
        date: document.querySelector("input[name='date']")?.value || "",
        sequence: document.getElementById("Sequence")?.value || "",
        totalCommandFrames: document.getElementById("numberofpages")?.value || "0",
        totalResponseFrames: document.getElementById("page")?.value || "0",
        totalErrorFrames: document.getElementById("pages")?.value || "0",
        pageNames: document.getElementById("pagenames")?.value || "",
        productDescription: document.getElementById("productdescription")?.value || ""
    };
    Object.entries(pInfo).forEach(([k, v]) => console.log(`PRODUCT-${k} => ${v}`));

    // 2. COMMANDS
    if (sessionStorage.getItem('frame_command') === 'true') {
        console.log("%c\n========= 2. COMMAND SECTION =========", "color: purple; font-weight: bold; text-decoration: underline;");
        storedCommands.forEach((frame, fIndex) => {
            logUnifiedFrame("Cmd", fIndex, frame, /cmd_|com_|com/);
        });
    }

    // 3. RESPONSES
    if (sessionStorage.getItem('frame_response') === 'true') {
        console.log("%c\n========= 3. RESPONSE SECTION =========", "color: orange; font-weight: bold; text-decoration: underline;");
        storedResponses.forEach((frame, fIndex) => {
            logUnifiedFrame("Res", fIndex, frame, /res_|resp_|resp/);
        });
    }

    // 4. ERRORS
    if (sessionStorage.getItem('frame_error') === 'true') {
        console.log("%c\n========= 4. ERROR SECTION =========", "color: red; font-weight: bold; text-decoration: underline;");
        storedErrors.forEach((frame, fIndex) => {
            logUnifiedFrame("Err", fIndex, frame, /err_|error_|error/);
        });
    }
}

// Helper to log Meta, then Rows, then Bits in proper sequence
function logUnifiedFrame(type, fIndex, frame, prefixRegex) {
    console.log(`%c>>> ${type}[${fIndex}] Configuration`, "font-weight:bold; color: #444;");

    // --- STEP A: PRINT META DATA FIRST ---
    // NEW: Define prefixes for live capture fallback
    const mPre = (type === "Cmd") ? "com" : (type === "Res" ? "resp" : "error");
    const sPre = (type === "Cmd") ? "cmd" : (type === "Res" ? "resp" : "error");

    // Use stored meta, OR if missing, capture live from UI
    const metaToPrint = frame.meta || {
        parametername: document.getElementById(`${mPre}_parametername`)?.value || "",
        noofrows: document.getElementById(`${sPre}_noofrows`)?.value || "0",
        remarks: document.getElementById(`${sPre}_remarks`)?.value || "",
        dataflowdirection: document.getElementById(`${sPre}_data_flow_direction`)?.value || "",
        frametype: document.getElementById(`${sPre}_frametype`)?.value || "",
        framesize_bytes: document.getElementById(`${sPre}_framesizeinbytes`)?.value || "",
        frametrigger: document.getElementById(`${sPre}_frametrigger`)?.value || document.getElementById('cmd_frame_trigger_options')?.value || "",
        multipleui: document.getElementById(`${sPre}_multipleui`)?.value || "",
        triggerui: document.getElementById(`${sPre}_triggerui`)?.value || "",
        voicecommand: document.getElementById(`${sPre}_voice_command`)?.value || "",
        voicecontrol: document.getElementById(`${sPre}_voice_control_instruction`)?.value || ""
    };

    console.log(`%c[${type}-${fIndex}] Metadata:`, "color: #800080; font-style: italic;");
    Object.entries(metaToPrint).forEach(([k, v]) => {
        const cleanKey = k.replace(prefixRegex, "");
        console.log(`${type}[${fIndex}]-meta-${cleanKey} => ${v}`);
    });

    // --- STEP B: PRINT TABLE ROWS NEXT ---
    const rows = frame.rows || [];
    if (rows.length > 0) {
        console.log(`%c[${type}-${fIndex}] Table Values:`, "color: #A52A2A; font-style: italic;");
        rows.forEach((row, rIndex) => {
            Object.entries(row).forEach(([rk, rv]) => {
                if (rk !== "bitwise") {
                    const cleanRowKey = rk.replace(prefixRegex, "");
                    console.log(`${type}[${fIndex}]-row[${rIndex+1}]-${cleanRowKey} => ${rv}`);
                }
            });

            if (Array.isArray(row.bitwise)) {
                row.bitwise.forEach((bit, bIndex) => {
                    Object.entries(bit).forEach(([bk, bv]) => {
                        const cleanBitKey = bk.replace(/cmd_cmd_|resp_resp_|error_error_|cmd_|resp_|error_/, "");
                        console.log(`${type}[${fIndex}]-row[${rIndex+1}]-bit[${bIndex+1}]-${cleanBitKey} => ${bv}`);
                    });
                });
            }
        });
    }
    console.log("%c------------------------------------------------", "color: #ccc;");
}
function checkPendingPages() {
    let pending = [];
    const validate = (id, storage, label) => {
        const expected = parseInt(document.getElementById(id)?.value || 0);
        for (let i = 0; i < expected; i++) {
            if (!storage[i] || !storage[i].rows || storage[i].rows.length === 0) {
                pending.push(`${label} Page ${i}`);
            }
        }
    };

    validate("numberofpages", storedCommands, "Command");
    validate("page", storedResponses, "Response");
    validate("pages", storedErrors, "Error");

    if (pending.length > 0) {
        alert("⚠️ PENDING PAGES:\n" + pending.join("\n"));
        return false;
    }
    return true;
}
//
function showRemainingFramesAlert() {
    const remaining = [];

    const getRemaining = (inputId, storage, label) => {
        const expected = parseInt(document.getElementById(inputId)?.value || 0);
        let filled = 0;

        for (let i = 0; i < expected; i++) {
            if (storage[i] && storage[i].rows && storage[i].rows.length > 0) {
                filled++;
            }
        }

        const left = expected - filled;
        if (left > 0) {
            remaining.push(`${label}: ${left} page${left > 1 ? "s" : ""} remaining`);
        }
    };

    getRemaining("numberofpages", storedCommands, "Command");
    getRemaining("page", storedResponses, "Response");
    getRemaining("pages", storedErrors, "Error");

    if (remaining.length === 0) {
        alert("🎉 All frames completed!");
    } else {
        alert("⚠ Remaining Frames:\n\n" + remaining.join("\n"));
    }
}


/**
 * 1. GENERATES COMMAND BOXES
 */
function generateCommandProgressBoxes() {
    const tracker = document.getElementById('command-progress-tracker');
    const inputField = document.getElementById('numberofpages'); // Command total pages

    if (!tracker || !inputField) return;

    let totalPages = parseInt(inputField.value) || 1;
    tracker.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const box = document.createElement('div');
        box.textContent = i;
        box.id = `box-command-step-${i}`;
        box.className = 'step-box';

        Object.assign(box.style, {
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            width: "35px",
            height: "35px",
            borderRadius: "4px",
            border: "2px solid #ccc",
            fontWeight: "bold",
            margin: "0 5px",
            flexShrink: "0",
            backgroundColor: "white",
            color: "black",
            transition: "all 0.3s ease"
        });

        tracker.appendChild(box);
    }

    updateCommandBoxColors();
}
let filledCommandPages = [];
let commandPageTouched = [];

function updateCommandBoxColors() {
    const tracker = document.getElementById('command-progress-tracker');
    if (!tracker) return;

    const boxes = tracker.children;

    for (let i = 0; i < boxes.length; i++) {
        const box = boxes[i];
        const pageNum = i + 1;
        const idx = pageNum - 1;

        // -------- CURRENT COMMAND PAGE --------
        if (pageNum === commandCount) {

            box.style.backgroundColor = '#cce5ff';
            box.style.color = '#0056b3';
            box.style.borderWidth = '2px';
            box.style.borderStyle = 'solid';
            box.style.borderColor = 'gold';

            if (commandPageTouched[idx] === true) {

                const dir = (document.getElementById('cmd_data_flow_direction')?.value || "").trim();
                const size = (document.getElementById('cmd_framesizeinbytes')?.value || "").trim();
                const trigger = (document.getElementById('cmd_frame_trigger_options')?.value || "").trim();

                const isDirFilled = dir !== "" && !dir.toLowerCase().includes("select");
                const isSizeFilled = size !== "" && size !== "0";
                const isTriggerFilled = trigger !== "" && trigger !== "None";

                let tableHasUserData = false;
                document.querySelectorAll('#additionalTableBody tr input').forEach(input => {
                    if (input.value.trim() !== "") tableHasUserData = true;
                });

                if (isDirFilled || isSizeFilled || isTriggerFilled || tableHasUserData) {
                    filledCommandPages[idx] = true;
                }
            }

            if (filledCommandPages[idx] === true) {
                box.style.backgroundColor = '#007bff';
                box.style.color = 'white';
                box.style.borderColor = 'gold';
            }

        }
        // -------- OTHER COMMAND PAGES --------
        else {
            if (filledCommandPages[idx] === true) {
                box.style.backgroundColor = '#007bff';
                box.style.borderColor = '#0056b3';
                box.style.color = 'white';
                box.style.borderWidth = '1px';
                box.style.borderStyle = 'solid';
            } else {
                box.style.backgroundColor = 'white';
                box.style.borderColor = '#ccc';
                box.style.color = 'black';
                box.style.borderWidth = '1px';
                box.style.borderStyle = 'solid';
            }
        }
    }
}
// When total pages change
document.getElementById('numberofpages').addEventListener('input', () => {
    commandCount = 1;
    generateCommandProgressBoxes();
    updateCommandFrameTitle();
});

// Initialize on load
window.addEventListener('DOMContentLoaded', generateCommandProgressBoxes);

// Track typing/input
document.addEventListener('input', (e) => {
    if (!e.target.closest('#command-form, #additionalTableBody')) return;

    commandPageTouched[commandCount - 1] = true;
    updateCommandBoxColors();
});

// Track dropdown changes
document.addEventListener('change', (e) => {
    if (!e.target.closest('#command-form, #additionalTableBody')) return;

    commandPageTouched[commandCount - 1] = true;
    updateCommandBoxColors();
});




//
// --- Response Frame State ---
 
/**
 * 1. GENERATES THE PROGRESS BOXES FOR RESPONSE
 */
function generateResponseProgressBoxes() {
    const tracker = document.getElementById('response-progress-tracker');
    const inputField = document.getElementById('page'); // Response total pages input ID
    
    if (!tracker || !inputField) return;

    let totalPages = parseInt(inputField.value) || 1;
    tracker.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const box = document.createElement('div');
        box.textContent = i;
        box.id = `box-response-step-${i}`;
        box.className = 'step-box';
        
        Object.assign(box.style, {
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            width: "35px",
            height: "35px",
            borderRadius: "4px", // Square style
            border: "2px solid #ccc",
            fontWeight: "bold",
            margin: "0 5px",
            flexShrink: "0",
            backgroundColor: "white",
            color: "black",
            transition: "all 0.3s ease" // Smooth color change
        });
        
        tracker.appendChild(box);
    }
    updateResponseBoxColors();
}

/**
 * 2. UPDATES COLORS FOR RESPONSE
 */
let filledResponsePages = [];
let responsePageTouched = [];

function updateResponseBoxColors() {
    const tracker = document.getElementById('response-progress-tracker');
    if (!tracker) return;

    const boxes = tracker.children;

    for (let i = 0; i < boxes.length; i++) {
        const box = boxes[i];
        const pageNum = i + 1;
        const idx = pageNum - 1;

        // -------- CURRENT RESPONSE PAGE --------
        if (pageNum === responseCount) {

            // Default style for current page
            box.style.backgroundColor = '#cce5ff'; // light blue
            box.style.color = '#0056b3';
            box.style.borderWidth = '2px';
            box.style.borderStyle = 'solid';
            box.style.borderColor = 'gold'; // 🔶 GOLD BORDER for current page

            // Only evaluate after user interaction
            if (responsePageTouched[idx] === true) {

                const dir = (document.getElementById('res_data_flow_direction')?.value || "").trim();
                const size = (document.getElementById('res_framesizeinbytes')?.value || "").trim();

                const isDirFilled = dir !== "" && !dir.toLowerCase().includes("select");
                const isSizeFilled = size !== "" && size !== "0";

                let tableHasUserData = false;
                document.querySelectorAll('#responseTableBody tr input').forEach(input => {
                    if (input.value.trim() !== "") tableHasUserData = true;
                });

                if (isDirFilled || isSizeFilled || tableHasUserData) {
                    filledResponsePages[idx] = true;
                }
            }

            // If current page already filled, make background dark blue
            if (filledResponsePages[idx] === true) {
                box.style.backgroundColor = '#007bff'; // dark blue
                box.style.color = 'white';
                box.style.borderColor = 'gold';
            }

        } 
        // -------- OTHER RESPONSE PAGES --------
        else {
            if (filledResponsePages[idx] === true) {
                box.style.backgroundColor = '#007bff'; // dark blue
                box.style.borderColor = '#0056b3';
                box.style.color = 'white';
                box.style.borderWidth = '1px';
                box.style.borderStyle = 'solid';
            } else {
                box.style.backgroundColor = 'white';
                box.style.borderColor = '#ccc';
                box.style.color = 'black';
                box.style.borderWidth = '1px';
                box.style.borderStyle = 'solid';
            }
        }
    }
}

// --- 3. Event Listeners for Response ---

// Triggered when the total number of pages input changes
document.getElementById('page').addEventListener('input', () => {
    responseCount = 1; // Reset to start when page count changes
    generateResponseProgressBoxes();
    updateResponseFrameTitle();
});

// Initialize boxes when page loads
window.addEventListener('DOMContentLoaded', generateResponseProgressBoxes);

// Track typing/input in Response Frame fields or table
document.addEventListener('input', (e) => {
    // Fire only if input is inside response frame or table
    if (!e.target.closest('#step-response, #responseTableBody')) return;

    responsePageTouched[responseCount - 1] = true;
    updateResponseBoxColors();
});

// Track dropdown changes
document.addEventListener('change', (e) => {
    if (!e.target.closest('#step-response, #responseTableBody')) return;

    responsePageTouched[responseCount - 1] = true;
    updateResponseBoxColors();
});

//--error frame--

/**
 * 1. GENERATES THE PROGRESS BOXES FOR ERROR
 */
function generateErrorProgressBoxes() {
    const tracker = document.getElementById('error-progress-tracker');
    const inputField = document.getElementById('pages'); // Error total pages input ID
    
    if (!tracker || !inputField) return;

    let totalPages = parseInt(inputField.value) || 1;
    tracker.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const box = document.createElement('div');
        box.textContent = i;
        box.id = `box-error-step-${i}`;
        box.className = 'step-box';
        
        Object.assign(box.style, {
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            width: "35px",
            height: "35px",
            borderRadius: "4px", // Square style
            border: "2px solid #ccc",
            fontWeight: "bold",
            margin: "0 5px",
            flexShrink: "0",
            backgroundColor: "white",
            color: "black",
            transition: "all 0.3s ease" // Smooth color change
        });
        
        tracker.appendChild(box);
    }
    updateErrorBoxColors();
}

/**
 * 2. UPDATES COLORS FOR ERROR
 */
let filledErrorPages = [];
let errorPageTouched = [];

function updateErrorBoxColors() {
    const tracker = document.getElementById('error-progress-tracker');
    if (!tracker) return;

    const boxes = tracker.children;

    for (let i = 0; i < boxes.length; i++) {
        const box = boxes[i];
        const pageNum = i + 1;
        const idx = pageNum - 1;

        // -------- CURRENT ERROR PAGE --------
        if (pageNum === errorCount) {

            // Default style for current page
            box.style.backgroundColor = '#cce5ff'; // light blue
            box.style.color = '#0056b3';
            box.style.borderWidth = '2px';
            box.style.borderStyle = 'solid';
            box.style.borderColor = 'gold'; // 🔶 GOLD BORDER for current page

            // Only evaluate after user interaction
            if (errorPageTouched[idx] === true) {

                const dir = (document.getElementById('error_data_flow_direction')?.value || "").trim();
                const size = (document.getElementById('error_framesizeinbytes')?.value || "").trim();
                const trigger = (document.getElementById('error_frametrigger')?.value || "").trim();

                const isDirFilled = dir !== "" && !dir.toLowerCase().includes("select");
                const isSizeFilled = size !== "" && size !== "0";
                const isTriggerFilled = trigger !== "" && trigger !== "None";

                let tableHasUserData = false;
                document.querySelectorAll('#errorTableBody tr input').forEach(input => {
                    if (input.value.trim() !== "") tableHasUserData = true;
                });

                if (isDirFilled || isSizeFilled || isTriggerFilled || tableHasUserData) {
                    filledErrorPages[idx] = true;
                }
            }

            // If current page already filled, make background dark blue
            if (filledErrorPages[idx] === true) {
                box.style.backgroundColor = '#007bff'; // dark blue
                box.style.color = 'white';
                box.style.borderColor = 'gold';
            }

        } 
        // -------- OTHER ERROR PAGES --------
        else {
            if (filledErrorPages[idx] === true) {
                box.style.backgroundColor = '#007bff'; // dark blue
                box.style.borderColor = '#0056b3';
                box.style.color = 'white';
                box.style.borderWidth = '1px';
                box.style.borderStyle = 'solid';
            } else {
                box.style.backgroundColor = 'white';
                box.style.borderColor = '#ccc';
                box.style.color = 'black';
                box.style.borderWidth = '1px';
                box.style.borderStyle = 'solid';
            }
        }
    }
}

// --- 3. Event Listeners for Error ---
document.getElementById('pages').addEventListener('input', () => {
    errorCount = 1; // Reset to start when page count changes
    generateErrorProgressBoxes();
    updateErrorFrameTitle();
});

// Initialize boxes when page loads
window.addEventListener('DOMContentLoaded', generateErrorProgressBoxes);

// Track typing/input in Error Frame fields or table
document.addEventListener('input', (e) => {
    // Only fire if the input is inside the error frame container or error table
    if (!e.target.closest('#step-error, #errorTableBody')) return;

    errorPageTouched[errorCount - 1] = true;
    updateErrorBoxColors();
});

// Track dropdown changes
document.addEventListener('change', (e) => {
    if (!e.target.closest('#step-error, #errorTableBody')) return;

    errorPageTouched[errorCount - 1] = true;
    updateErrorBoxColors();
});

//1st page 

//
//
function showFrame(frameType) {
    document.getElementById('step-command').style.display = 'none';
    document.getElementById('step-3').style.display = 'none';
    document.getElementById('step-error').style.display = 'none';

    if (frameType === 'command') {
        document.getElementById('step-command').style.display = 'block';
        generateCommandProgressBoxes();
    } 
    else if (frameType === 'response') {
        document.getElementById('step-3').style.display = 'block';
        generateResponseProgressBoxes();
    } 
    else if (frameType === 'error') {
        document.getElementById('step-error').style.display = 'block';
        generateErrorProgressBoxes();
    }

    highlightFrameTab(frameType);
}

//
function initForm() {

    responseCount = 1;
    errorCount = 1;

    responsePageTouched = [];
    errorPageTouched = [];
    filledErrorPages = [];

    document.querySelectorAll('#file-form input').forEach(i => i.value = '');
    document.querySelectorAll('#file-form select').forEach(s => s.selectedIndex = 0);

    bindFrameSizeListeners();
    bindRowCalculations();

    generateCommandProgressBoxes();
    generateResponseProgressBoxes();
    generateErrorProgressBoxes();

    showFrame('command');

    console.log("Form initialized clean");
}
//
function closeAllModalsAndReset() {
    const modal = document.getElementById("modalContainer");
    modal.style.display = "none";

    // reset to original state
    modal.innerHTML = originalModalHTML;
}

//
function openFormAgain() {
    const modal = document.getElementById("modalContainer");
    modal.style.display = "block";
}

//



//
document.addEventListener('click', function() {
    const commandStep = document.getElementById('step-command'); // Adjust to your actual Command container ID
    const responseStep = document.getElementById('step-response');       // Your Response container ID
    const errorStep = document.getElementById('step-error');     // Your Error container ID
    
    // Check which one is visible and highlight the corresponding tab
    if (responseStep && responseStep.style.display === 'block') {
        highlightFrameTab('response');
    } else if (commandStep && commandStep.style.display === 'block') {
        highlightFrameTab('command');
    } else if (errorStep && errorStep.style.display === 'block') {
        highlightFrameTab('error');
    }
});
//enablemsg
function handleEnableChange(inputElement, prefix, index) {
    const row = inputElement.closest('tr');
    const messageInput = row.querySelector(`input[name="message_${prefix}_${index}"]`);
    let val = inputElement.value.trim().toLowerCase();
    
    if (val === 'yes' || val === 'y') {
        inputElement.value = "Yes";
        messageInput.disabled = false;
        
        // Only set the error state if the box is actually empty
        if (messageInput.value.trim() === "" || messageInput.value === "Enter notification message") {
            messageInput.value = "Enter notification message";
            messageInput.style.color = "red";
            messageInput.classList.add("error");
            // DO NOT add pink-highlight here yet, 
            // wait until they try to click "Next"
        }
    } else {
        inputElement.value = "No";
        messageInput.value = "";
        messageInput.disabled = true;
        
        // Clean up only the message field
        messageInput.classList.remove("error");
        messageInput.style.color = "";
        
        // Only remove pink-highlight if NO OTHER field has an error
        const otherErrors = row.querySelectorAll('.error');
        if (otherErrors.length === 0) {
            row.classList.remove("pink-highlight");
        }
    }
    
    if (typeof syncHiddenField === "function") {
        syncHiddenField(prefix, 'enable', index, inputElement.value);
    }
}

//
// 1. THIS CLEARS RED COLOR WHILE TYPING
document.addEventListener('input', function(e) {
    // Only target message fields
    if (e.target && e.target.name && e.target.name.startsWith('message_')) {
        const field = e.target;
        const row = field.closest('tr');

        // If the user has typed actual content
        if (field.value.trim() !== "" && field.value !== "Enter notification message" && field.value !== "Error") {
            field.classList.remove("error");
            field.style.color = "black"; // Reset text to black
            field.style.borderColor = ""; // Reset border
            
            // Check if we can remove the pink highlight from the row
            const otherErrors = row.querySelectorAll('.error');
            if (otherErrors.length === 0) {
                row.classList.remove("pink-highlight");
            }
        }
    }
});

// 2. UPDATE YOUR FOCUSIN (To match "Enter notification message")
document.addEventListener('focusin', function(e) {
    if (e.target && e.target.name && e.target.name.startsWith('message_')) {
        // Clear both "Error" and your new placeholder text
        if (e.target.value === "Error" || e.target.value === "Enter notification message") {
            e.target.value = "";
            e.target.style.color = "black";
            e.target.classList.remove("error");
        }
    }
});

//
function printCmdConsole(storedCommands) {

    storedCommands.forEach((page, cmdIndex) => {
        if (!page || !page.rows) return;

        page.rows.forEach((row, rowIndex) => {
            const rowNo = rowIndex + 1;

            // ================= BITWISE =================
            if (Array.isArray(row.bitwise) && row.bitwise.length) {

                row.bitwise.forEach((bitRow, bitIndex) => {
                    Object.entries(bitRow).forEach(([key, value]) => {
                        console.log(
                          `Cmd[${cmdIndex}]-row[${rowNo}]-bit[${bitIndex + 1}]-${key} => ${value}`
                        );
                    });
                });

            }
            // ================= BYTEWISE =================
            else {
                Object.entries(row).forEach(([key, value]) => {
                    if (key === 'bitwise') return;

                    console.log(
                      `Cmd[${cmdIndex}]-row[${rowNo}]-${key} => ${value}`
                    );
                });
            }

        });
    });
}

//


</script>
<script>
function logFull(label, obj) {
    console.log(label, JSON.parse(JSON.stringify(obj)));
}
</script>
<script>
if (!window.originalModalHTML) {
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("modalContainer");
        if (modal) {
            window.originalModalHTML = modal.innerHTML;
            console.log("Snapshot stored");
        }
    });
}
</script>


<script>
function logAllRowCounts() {
    console.log("------ FORM STATE ------");
    console.log("Command rows:", document.querySelectorAll("#commandTableBody tr").length);
    console.log("Response rows:", document.querySelectorAll("#responseTableBody tr").length);
    console.log("Error rows:", document.querySelectorAll("#errorTableBody tr").length);
    console.log("------------------------");
}

</script>
<script>
(function() {
    const originalShow = Element.prototype.appendChild;
    Element.prototype.appendChild = function() {
        console.trace("APPEND CHILD CALLED");
        return originalShow.apply(this, arguments);
    };
})();

//
function validateDuplicateByteRange(tableBodyId, prefix, errorId) {
    const rows = document.querySelectorAll(`#${tableBodyId} tr`);
    const usedNumbers = {}; // Stores { number: inputElement }
    let hasDuplicate = false;

    // 1. Clear previous red borders from ALL inputs in this table
    rows.forEach(row => {
        row.querySelectorAll('input').forEach(input => {
            input.style.border = "1px solid #ccc"; // Reset to default
        });
    });

    rows.forEach((row, index) => {
        const startInput = row.querySelector(`input[name^="startBytePosition_${prefix}_"]`);
        const endInput   = row.querySelector(`input[name^="endBytePosition_${prefix}_"]`);

        if (!startInput || !endInput) return;

        const inputs = [startInput, endInput];

        inputs.forEach(currentInput => {
            const val = currentInput.value.trim();
            if (val === "") return;

            // Check if this number was used by an input in a DIFFERENT row
            if (usedNumbers[val] !== undefined) {
                const previousInput = usedNumbers[val];
                
                // Get the row index of the previous input to compare
                const prevRow = previousInput.closest('tr');
                const currRow = currentInput.closest('tr');

                if (prevRow !== currRow) {
                    // Highlight the current field
                    currentInput.style.border = "2px solid red";
                    currentInput.style.backgroundColor = "#ffe6e6";
                    
                    // Highlight the previous field that had this number
                    previousInput.style.border = "2px solid red";
                    previousInput.style.backgroundColor = "#ffe6e6";
                    
                    hasDuplicate = true;
                }
            } else {
                // First time seeing this number, store this specific input element
                usedNumbers[val] = currentInput;
            }
        });
    });

    if (hasDuplicate) {
        const msg = "❌ Duplicate Number: The highlighted byte positions are already used in another row.";
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.innerText = msg;
            errorElement.style.display = "block";
        }
        forceMessage(msg);
        return false;
    }
    return true;
}

</script>

</body>
</html>                                                                                                                                                                                                                                                                                                                                                                                                                                                             