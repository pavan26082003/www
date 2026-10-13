<?php
date_default_timezone_set('Asia/Kolkata');
// Use different variable names for constants to avoid conflicts
$cmd_frametype_const ='COMMAND';
$resp_frametype_const ='RESPONSE';
$error_frametype_const ='ERROR';

// Initialize variables to prevent undefined variable errors
$productname = $productname ?? '';
$brandname = $brandname ?? '';
$modelname = $modelname ?? '';
$date = $date ?? date('Y-m-d');
$numberofpages = $numberofpages ?? '';
$Sequence = $Sequence ?? '';
$pagenames = $pagenames ?? '';
$product_description = $product_description ?? '';

$cmd_parametername = $cmd_parametername ?? '';
// Use the same parameter name for all frames for consistency
$resp_parametername = $resp_parametername ?? $cmd_parametername ?? '';
$error_parametername = $error_parametername ?? $cmd_parametername ?? '';

$cmd_dataflow = $cmd_dataflow ?? '';
$cmd_frametype = $cmd_frametype ?? $cmd_frametype_const;
$cmd_framesizeinbytes = $cmd_framesizeinbytes ?? '';
$cmd_noofrows = $cmd_noofrows ?? 0;
$cmd_frametrigger = $cmd_frametrigger ?? '';
$cmd_multipleui = $cmd_multipleui ?? '';
$cmd_triggerui = $cmd_triggerui ?? '';
$cmd_voicecommand = $cmd_voicecommand ?? '';
$cmd_appnotification = $cmd_appnotification ?? '';

// Arrays for command frame table
$startBytePosition_cmd = $startBytePosition_cmd ?? [];
$endBytePosition_cmd = $endBytePosition_cmd ?? [];
$fieldDescription_cmd = $fieldDescription_cmd ?? [];
$numberOfBytes_cmd = $numberOfBytes_cmd ?? [];
$bitSize_cmd = $bitSize_cmd ?? [];
$dataType_cmd = $dataType_cmd ?? [];
$valueDefinition_cmd = $valueDefinition_cmd ?? [];
$displayValueMin_cmd = $displayValueMin_cmd ?? [];
$displayValueMax_cmd = $displayValueMax_cmd ?? [];
$valueInHexMin_cmd = $valueInHexMin_cmd ?? [];
$valueInHexMax_cmd = $valueInHexMax_cmd ?? [];
$uiType_cmd = $uiType_cmd ?? [];
$uiId_cmd = $uiId_cmd ?? [];
$uiPageNo_cmd = $uiPageNo_cmd ?? [];
$cmd_asperui = $cmd_asperui ?? [];
$cmd_sno_asperui = $cmd_sno_asperui ?? [];
$cmd_asperuivalue = $cmd_asperuivalue ?? [];
$uiDescriptionInOut_cmd = $uiDescriptionInOut_cmd ?? [];
$sliderStepValue_cmd = $sliderStepValue_cmd ?? [];
$defaultvalue_cmd = $defaultvalue_cmd ?? [];
$developerNotes_cmd = $developerNotes_cmd ?? [];

$resp_dataflow = $resp_dataflow ?? '';
$resp_frametype = $resp_frametype ?? $resp_frametype_const;
$resp_framesizeinbytes = $resp_framesizeinbytes ?? '';
$resp_noofrows = $resp_noofrows ?? 0;
$resp_frametrigger = $resp_frametrigger ?? '';
$resp_multipleui = $resp_multipleui ?? '';
$resp_triggerui = $resp_triggerui ?? '';
$resp_voicecommand = $resp_voicecommand ?? '';
$resp_appnotification = $resp_appnotification ?? '';

// Arrays for response frame table
$startBytePosition_resp = $startBytePosition_resp ?? [];
$endBytePosition_resp = $endBytePosition_resp ?? [];
$fieldDescription_resp = $fieldDescription_resp ?? [];
$numberOfBytes_resp = $numberOfBytes_resp ?? [];
$bitSize_resp = $bitSize_resp ?? [];
$dataType_resp = $dataType_resp ?? [];
$valueDefinition_resp = $valueDefinition_resp ?? [];
$displayValueMin_resp = $displayValueMin_resp ?? [];
$displayValueMax_resp = $displayValueMax_resp ?? [];
$valueInHexMin_resp = $valueInHexMin_resp ?? [];
$valueInHexMax_resp = $valueInHexMax_resp ?? [];
$uiType_resp = $uiType_resp ?? [];
$uiId_resp = $uiId_resp ?? [];
$uiPageNo_resp = $uiPageNo_resp ?? [];
$resp_asperui = $resp_asperui ?? [];
$resp_sno_asperui = $resp_sno_asperui ?? [];
$resp_asperuivalue = $resp_asperuivalue ?? [];
$uiDescriptionInOut_resp = $uiDescriptionInOut_resp ?? [];
$sliderStepValue_resp = $sliderStepValue_resp ?? [];
$defaultvalue_resp = $defaultvalue_resp ?? [];
$developerNotes_resp = $developerNotes_resp ?? [];

$error_dataflow = $error_dataflow ?? '';
$error_frametype = $error_frametype ?? $error_frametype_const;
$error_framesizeinbytes = $error_framesizeinbytes ?? '';
$error_noofrows = $error_noofrows ?? 0;
$error_frametrigger = $error_frametrigger ?? '';
$error_multipleui = $error_multipleui ?? '';
$error_triggerui = $error_triggerui ?? '';
$error_voicecommand = $error_voicecommand ?? '';
$error_appnotification = $error_appnotification ?? '';

// Arrays for error frame table
$startBytePosition_error = $startBytePosition_error ?? [];
$endBytePosition_error = $endBytePosition_error ?? [];
$fieldDescription_error = $fieldDescription_error ?? [];
$numberOfBytes_error = $numberOfBytes_error ?? [];
$bitSize_error = $bitSize_error ?? [];
$dataType_error = $dataType_error ?? [];
$valueDefinition_error = $valueDefinition_error ?? [];
$displayValueMin_error = $displayValueMin_error ?? [];
$displayValueMax_error = $displayValueMax_error ?? [];
$valueInHexMin_error = $valueInHexMin_error ?? [];
$valueInHexMax_error = $valueInHexMax_error ?? [];
$uiType_error = $uiType_error ?? [];
$uiId_error = $uiId_error ?? [];
$uiPageNo_error = $uiPageNo_error ?? [];
$error_asperui = $error_asperui ?? [];
$error_sno_asperui = $error_sno_asperui ?? [];
$error_asperuivalue = $error_asperuivalue ?? [];
$uiDescriptionInOut_error = $uiDescriptionInOut_error ?? [];
$sliderStepValue_error = $sliderStepValue_error ?? [];
$defaultvalue_error = $defaultvalue_error ?? [];
$developerNotes_error = $developerNotes_error ?? [];

// Dummy function for form_open_multipart and get_uri if not defined
if (!function_exists('form_open_multipart')) {
    function form_open_multipart($action, $attributes) {
        $attr_str = '';
        foreach ($attributes as $key => $value) {
            $attr_str .= " {$key}=\"{$value}\"";
        }
        return "<form action=\"{$action}\" method=\"post\" enctype=\"multipart/form-data\"{$attr_str}>";
    }
}
if (!function_exists('form_close')) {
    function form_close() {
        return "</form>";
    }
}
if (!function_exists('get_uri')) {
    function get_uri($path) {
        return "/{$path}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit API Configuration</title>
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
      display: none; 
    }
    
    .form-step.active { 
      display: block; 
    }
    
    .btn {
      padding: 12px 24px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      margin: 10px 5px;
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
      background-color: #28a745;
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
    
    /* Action buttons styles */
    .sno-column {
      width: 100px;
      background: #f8f9fa;
      font-weight: bold;
      text-align: center;
      position: sticky;
      left: 0;
      z-index: 5;
    }
    
    .remove-btn {
      background: #dc3545;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 11px;
      width: 100%;
      margin-bottom: 4px;
    }
    
    .remove-btn:hover {
      background: #c82333;
    }
    
    .add-row-btn {
      background: #28a745;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 11px;
      width: 100%;
    }
    
    .add-row-btn:hover {
      background: #218838;
    }
    
    .table-header-btn {
      background: #28a745;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      margin: 15px 5px;
    }
    
    .table-header-btn:hover {
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
      margin-top: 30px;
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
    
    /* Modal specific styles */
    .modal-body {
      padding: 20px;
      max-height: 80vh;
      overflow-y: auto;
    }
    
    .modal-dialog {
      max-width: 95%;
      width: 95%;
    }
    
    .modal-content {
      border-radius: 16px;
      overflow: hidden;
    }

.form-navigation-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 30px;
    padding: 15px;
    background: #ffffff;
    border-top: 1px solid #eee;
}

.page-number-scroll-box {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    max-width: 150px; /* Limits width to show about 3 boxes */
    padding: 5px;
    scrollbar-width: thin;
}

.page-box {
    min-width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #007bff;
    border-radius: 4px; /* Box Shape */
    background: #fff;
    color: #007bff;
    cursor: pointer;
    font-weight: bold;
    flex-shrink: 0;
    transition: 0.2s;
}

.page-box.active {
    background: #007bff;
    color: #fff;
}

/* Scrollbar for Chrome/Safari */
.page-number-scroll-box::-webkit-scrollbar {
    height: 4px;
}
.page-number-scroll-box::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

/* ===== Bitwise Modal ===== */
/* Modal background */
.bitwise-modal {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.7);
  display: none;
  z-index: 999999;
}

/* Modal box */
.bitwise-modal-content {
  background: #fff;
  width: 95%;
  height: 85%;
  margin: 3% auto;
  padding: 15px;
  display: flex;
  flex-direction: column;
}

/* Scroll container for table */
.bitwise-table-wrapper {
  flex: 1;
  overflow: auto;          /* BOTH scrollbars */
  border: 1px solid #ccc;
}

/* Table */
#bitwiseTable {
  border-collapse: collapse;
  width: max-content;      /* allow horizontal scroll */
  min-width: 1800px;
}

#bitwiseTable th,
#bitwiseTable td {
  border: 1px solid #ccc;
  padding: 6px;
  text-align: center;
  white-space: nowrap;
}

/* Inputs big like normal fields */
#bitwiseTable input,
#bitwiseTable select,
#bitwiseTable textarea {
  width: 130px;
  height: 32px;
  font-size: 14px;
  padding: 4px 6px;
  box-sizing: border-box;
}

/* Notification message wider */
#bitwiseTable textarea {
  width: 200px;
  height: 32px;
  resize: none;
}

/* Buttons */
.modal-actions {
  margin-top: 10px;
  text-align: right;
}

.modal-actions {
  margin-top: 10px;
  display: flex;
  justify-content: flex-start; /* left */
  gap: 10px;                   /* space between buttons */
}

/* + - buttons */
.action-btn {
  width: 28px;
  height: 28px;
  font-size: 16px;
  cursor: pointer;
}
/* Add this to your CSS file or <style> tag */

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

<?php echo form_open_multipart(get_uri("items/saveapi_edit"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>

<!-- Hidden fields for readonly inputs -->
<input type="hidden" name="modelname" value="<?php echo htmlspecialchars($modelname); ?>">
<input type="hidden" name="productname" value="<?php echo htmlspecialchars($productname); ?>">
<input type="hidden" name="brandname" value="<?php echo htmlspecialchars($brandname); ?>">






<div id="step-1" class="form-step active">
  <div class="container">
    <h2>PRODUCT INFORMATION</h2>
    
    <div id="step1-error" class="step-error"></div>

    <div class="form-group">
      <label for="par_parametername" class="required">Parameter Names</label>
      <input type="text" name="cmd_parametername" id="par_parametername" 
             value="<?php echo htmlspecialchars($cmd_parametername ?? ''); ?>" 
             placeholder="Enter parameter name" oninput="validateParamLive(); syncParameterNameToAll(this.value)" />
    </div>

    <div class="form-row">
      <div class="form-col">
        <div class="form-group">
          <label for="productname">Product Name</label>
          <input type="text" class="form-control" value="<?php echo htmlspecialchars($productname ?? ''); ?>" id="productname" readonly>
        </div>
      </div>
      <div class="form-col">
        <div class="form-group">
          <label for="brandname">Brand Name</label>
          <input type="text" class="form-control" value="<?php echo htmlspecialchars($brandname ?? ''); ?>" id="brandname" readonly>
        </div>
      </div>
    </div>

    <div class="form-row">
      <div class="form-col">
        <div class="form-group">
          <label for="modelname">Model Name</label>
          <input type="text" class="form-control" value="<?php echo htmlspecialchars($modelname ?? ''); ?>" id="modelname" readonly>
        </div>
      </div>
      <div class="form-col">
        <div class="form-group">
          <label for="date">Date</label>
          <input type="date" name="date" value="<?php echo htmlspecialchars($date ?? date('Y-m-d')); ?>" />
        </div>
      </div>
    </div>

    <div class="form-row">
      <div class="form-col">
        <div class="form-group">
          <label for="numberofpage_input" class="required">Page Number</label>
          <input type="text" name="numberofpages_val" id="numberofpage_input" 
                 value="<?php echo htmlspecialchars($numberofpages ?? ''); ?>"
                 placeholder="e.g., 1" />
        </div>
      </div>
      <div class="form-col">
        <div class="form-group">
          <label for="Sequence" class="required">Sequence No</label>
          <input type="text" name="Sequence" id="Sequence" 
                 value="<?php echo htmlspecialchars($Sequence ?? ''); ?>"
                 placeholder="e.g., 1" />
        </div>
      </div>
    </div>

    <div class="form-group">
        <label for="numberofpages" class="required">Number of Command Frames</label>
        <input type="number" name="total_command_frames" id="numberofpages" 
               value="<?php echo htmlspecialchars($total_command_frames ?? ''); ?>" 
               placeholder="e.g., 1" min="1" oninput="generateCommandFrames()" />
    </div>

    <div class="form-group">
        <label for="page" class="required">Number of Response Frames</label>
        <input type="number" name="total_response_frames" id="page" 
               value="<?php echo htmlspecialchars($total_response_frames ?? ''); ?>" 
               placeholder="e.g., 1" min="1" oninput="generateResponseFrames()"/>
    </div>

    <div class="form-group">
        <label for="pages" class="required">Number of Error Frames</label>
        <input type="number" name="total_error_frames" id="pages" 
               value="<?php echo htmlspecialchars($total_error_frames ?? ''); ?>" 
               placeholder="e.g., 1" min="1" oninput="generateErrorFrames()" />
    </div>

    <div class="form-group">
      <label for="pagenames" class="required">Page Names</label>
      <input type="text" name="pagenames" id="pagenames" 
             value="<?php echo htmlspecialchars($pagenames ?? ''); ?>"
             placeholder="e.g., Fan Page" onblur="validatePageName()" />
    </div>

    <div class="form-group">
      <label for="product_description">Product Description</label>
      <textarea name="product_description" id="product_description" rows="4" 
                placeholder="Describe the product..." 
                oninput="checkWordCount(this)"><?php echo htmlspecialchars($product_description ?? ''); ?></textarea>
    </div>

    <div class="form-navigation">
      <div></div>
      <button class="btn" type="button" onclick="goToNextStep(1)">Next</button>
    </div>
  </div>
</div>


//

<!-- Step 2: Command Frame -->
<div id="step-2" class="form-step">
  <div class="container">
    <h2 id="cmdFrameTitle">COMMAND FRAME (1)</h2>
    
    <div id="step2-error" class="step-error"></div>

    <!-- Parameter Name -->
    <div class="form-group">
      <label for="com_parametername">Parameter Name</label>
      <input type="text" name="cmd_parametername" id="com_parametername" 
             value="<?php echo htmlspecialchars($cmd_parametername); ?>" readonly />
    </div>

    <!-- Data Flow Direction -->
    <!--div class="form-group">
      <label for="cmd_data_flow_direction" class="required">Data Flow Direction</label>
      <input type="text" name="cmd_dataflow" id="cmd_data_flow_direction"
             value="<?php echo htmlspecialchars($cmd_dataflow); ?>" />
    </div-->

    <!-- Change from input to select for Data Flow Direction -->
<div class="form-group">
  <label for="cmd_data_flow_direction" class="required">Data Flow Direction</label>
  <select name="cmd_dataflow" id="cmd_data_flow_direction">
    <option value="App/Server to Device" <?php echo ($cmd_dataflow == 'App/Server to Device') ? 'selected' : ''; ?>>App/Server to Device</option>
    <option value="Device to App/Server" <?php echo ($cmd_dataflow == 'Device to App/Server') ? 'selected' : ''; ?>>Device to App/Server</option>
  </select>
</div>


    <!-- Frame Type -->
    <div class="form-group">
      <label for="cmd_frametype" class="required">Frame Type</label>
      <input type="text" name="cmd_frametype" id="cmd_frametype"
             value="<?php echo htmlspecialchars($cmd_frametype_const); ?>" />
    </div>

    <div class="form-row">
      <div class="form-col">
        <div class="form-group">
          <label for="cmd_framesizeinbytes" class="required">Frame size in bytes</label>
          <input type="number" id="cmd_framesizeinbytes" name="cmd_framesizeinbytes" 
                 value="<?php echo htmlspecialchars($cmd_framesizeinbytes); ?>"
                 placeholder="Enter number" min="0" oninput="updateRowsFromFrameSize('cmd')" />
        </div>
      </div>
      <div class="form-col">
        <div class="form-group">
          <label for="cmd_noofrows" class="required">No of rows</label>
          <input type="number" id="cmd_noofrows" name="cmd_noofrows" 
                 value="<?php echo htmlspecialchars($cmd_noofrows); ?>"
                 readonly />
        </div>
      </div>
    </div>

   <!-- Frame Trigger -->
    <div class="form-group">
      <label for="frame_trigger">Frame Trigger</label>

        <select name="frame_trigger_options" id="frame_trigger_options">
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
      <label for="cmd_multipleui">Multiple UI</label>
      <input type="text" name="cmd_multipleui" id="cmd_multipleui"
             value="<?php echo htmlspecialchars($cmd_multipleui); ?>" />
    </div>

    <!-- Trigger UI -->
    <div class="form-group">
      <label for="cmd_triggerui">Trigger UI</label>
      <input type="text" name="cmd_triggerui" id="cmd_triggerui"
             value="<?php echo htmlspecialchars($cmd_triggerui); ?>" />
    </div>

    <!-- Voice Command -->
    <div class="form-group">
      <label for="cmd_voicecommand">Voice Command</label>
      <input type="text" id="cmd_voicecommand" name="cmd_voicecommand" 
             value="<?php echo htmlspecialchars($cmd_voicecommand); ?>"
             placeholder="Yes/No" />
    </div>

    <div class="form-group">
      <label for="cmd_voice_control_instruction_text">Voice Control Instruction Command</label>
      <input type="file" id="cmd_voice_control_instruction_text" name="cmd_voice_control_instruction_text" accept="image/*" />
      <small>Upload image for voice control instruction</small>
    </div>

    <div class="form-group">
      <label for="cmd_appnotification">Remarks/App Notification</label>
      <textarea id="cmd_appnotification" name="cmd_appnotification" 
                placeholder="Enter your remarks here..." rows="3"><?php echo htmlspecialchars($cmd_appnotification); ?></textarea>
    </div>

    <!-- Command Frame Table -->
    <div class="table-container">
      <div class="table-header">
        <h3 style="margin: 0; color: #333;">Command Frame Configuration</h3>
        <div>
          <button type="button" class="table-header-btn" onclick="addRow('cmd')">+ Add Row</button>
          <button type="button" class="table-header-btn" onclick="clearTable('cmd')">Clear Table</button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="dynamic-table" id="additionalTable">
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
              <th>UI Page No</th>
              <th>AS PER UI</th>
              <th>UI Serial No</th>
              <th>UI Values</th>
              <th>UI In/Out</th>
              <th>Slider Step</th>
              <th>Default Value</th>
              <th>Dev Notes</th>
              <th>Notification Enable</th>
             <th>Notification Message</th>

              <th>Actions</th>
              </tr>
          </thead>


          <tbody id="additionalTableBody">
            <?php 
$initial_rows = 1; // always show only 1 row initially

if (!empty($startBytePosition_cmd)) {
    $initial_rows = count($startBytePosition_cmd);
}

for ($i = 0; $i < $initial_rows; $i++) { 
?>
            <tr id="row_cmd_<?php echo $i + 1; ?>" data-row="<?php echo $i + 1; ?>">
              <td class="sno-column"><?php echo $i + 1; ?></td>
              <td><input type="number" name="startBytePosition_cmd[]" 
                         value="<?php echo htmlspecialchars($startBytePosition_cmd[$i] ?? ''); ?>"
                         class="compact-input" oninput="calculateByteValues(this, 'cmd', <?php echo $i + 1; ?>)"></td>
              <td><input type="number" name="endBytePosition_cmd[]" 
                         value="<?php echo htmlspecialchars($endBytePosition_cmd[$i] ?? ''); ?>"
                         class="compact-input" oninput="calculateByteValues(this, 'cmd', <?php echo $i + 1; ?>)"></td>
              
            <td>
  <div style="display:flex; gap:6px; align-items:center;">
    
    <!-- Field Description -->
    <select name="fieldDescription_cmd[]" 
            class="compact-select field-desc"
            onchange="handleFieldDescriptionChange(this)">
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

    

    <!-- Bitwise / Bytewise -->
    <select class="compact-select bit-byte-select"
            style="display:none"
            onchange="handleBitByteChange(this)">
      <option value="">Mode</option>
      <option value="Bytewise">Bytewise</option>
      <option value="Bitwise">Bitwise</option>
    </select>

    <!-- Arrow -->
    <button type="button"
        class="arrow-btn"
        style="display:none"
        onclick="openBitwiseConfig(this.closest('tr'))">
▶
</button>



  </div>
</td>


           
              <td><input type="number" name="numberOfBytes_cmd[]" 
                         value="<?php echo htmlspecialchars($numberOfBytes_cmd[$i] ?? ''); ?>"
                         class="compact-input auto-calculated" readonly></td>
              <td><input type="number" name="bitSize_cmd[]" 
                         value="<?php echo htmlspecialchars($bitSize_cmd[$i] ?? ''); ?>"
                         class="compact-input auto-calculated" readonly></td>
              <td>
                <select name="dataType_cmd[]" class="compact-select" onchange="calculateByteValues(null, 'cmd', <?php echo $i + 1; ?>)">
                  <?php
                  $options = ["Select", "ASCII", "Binary", "SignedInt", "Float", "UnsignedInt", "String","Double","String","Bollean","Hex","Enum","Timestamp","None"];
                  foreach ($options as $option) {
                    $selected = (($dataType_cmd[$i] ?? '') == $option) ? "selected" : "";
                    echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                  }
                  ?>
                </select>
              </td>
              <td>
                <select name="valueDefinition_cmd[]" class="compact-select">
                  <?php
                  $options = ["Select","Fixed Value", "Range Value", "Dynamic Value","Enum Value", "Calculated Value","None"];
                  foreach ($options as $option) {
                    $selected = (($valueDefinition_cmd[$i] ?? '') == $option) ? "selected" : "";
                    echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                  }
                  ?>
                </select>
              </td>
              <td><input type="text" name="displayValueMin_cmd[]" 
                         value="<?php echo htmlspecialchars($displayValueMin_cmd[$i] ?? ''); ?>"
                         class="compact-input auto-calculated" readonly></td>
              <td><input type="text" name="displayValueMax_cmd[]" 
                         value="<?php echo htmlspecialchars($displayValueMax_cmd[$i] ?? ''); ?>"
                         class="compact-input auto-calculated" readonly></td>
              <td><input type="text" name="valueInHexMin_cmd[]" 
                         value="<?php echo htmlspecialchars($valueInHexMin_cmd[$i] ?? ''); ?>"
                         class="compact-input"></td>
              <td><input type="text" name="valueInHexMax_cmd[]" 
                         value="<?php echo htmlspecialchars($valueInHexMax_cmd[$i] ?? ''); ?>"
                         class="compact-input"></td>
              <td>
                <select name="uiType_cmd[]" class="compact-select" onchange="applyUIAutoFill(this, 'cmd', <?php echo $i + 1; ?>)">
                  <?php
                  $options = ["None", "Text", "Toggle Switch", "Notification", "console", "Enter Text", "Slider", "Brightness Slider", "Airflow Slider", "CCT Slider", "Saturation Slider", "Hue Slider", "Hue Slider Custom", "Hue Circle", "Push Button", "Drop Down", "Trigger", "Display", "Button", "Toast Display", "Auto Trigger with Timer"];
                  foreach ($options as $option) {
                    $selected = (($uiType_cmd[$i] ?? '') == $option) ? "selected" : "";
                    echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                  }
                  ?>
                </select>
              </td>
              <td><input type="text" name="uiId_cmd[]" 
                         value="<?php echo htmlspecialchars($uiId_cmd[$i] ?? ''); ?>"
                         class="compact-input"></td>
              <td>
                <select name="uiPageNo_cmd[]" class="compact-select">
                  <?php
                  $options = ["None", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10"];
                  foreach ($options as $option) {
                    $selected = (($uiPageNo_cmd[$i] ?? '') == $option) ? "selected" : "";
                    echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                  }
                  ?>
                </select>
              </td>
              <td><input type="text" name="cmd_asperui[]" 
                         value="<?php echo htmlspecialchars(implode(',', explode('][', trim($cmd_asperui[$i] ?? '', '[]')))); ?>"
                         class="compact-input"></td>
              <td><input type="text" name="cmd_sno_asperui[]" 
                         value="<?php echo htmlspecialchars(implode(',', explode('][', trim($cmd_sno_asperui[$i] ?? '', '[]')))); ?>"
                         class="compact-input"></td>
              <td><input type="text" name="cmd_asperuivalue[]" 
                         value="<?php echo htmlspecialchars(implode(',', explode('][', trim($cmd_asperuivalue[$i] ?? '', '[]')))); ?>"
                         class="compact-input"></td>
              <td>
                <select name="uiDescriptionInOut_cmd[]" class="compact-select">
                  <?php
                  $options = ["None", "Read/Write", "Read", "Write"];
                  foreach ($options as $option) {
                    $selected = (($uiDescriptionInOut_cmd[$i] ?? '') == $option) ? "selected" : "";
                    echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                  }
                  ?>
                </select>
              </td>
              <td><input type="number" name="sliderStepValue_cmd[]" 
                         value="<?php echo htmlspecialchars($sliderStepValue_cmd[$i] ?? ''); ?>"
                         class="compact-input"></td>
              <td><input type="text" name="defaultvalue_cmd[]" 
                         value="<?php echo htmlspecialchars($defaultvalue_cmd[$i] ?? ''); ?>"
                         class="compact-input"></td>
              <td><input type="text" name="developerNotes_cmd[]" 
                         value="<?php echo htmlspecialchars($developerNotes_cmd[$i] ?? ''); ?>"
                         class="compact-input"></td>
                      <td>
  <select name="notifEnable_cmd[]" class="compact-select">
    <option value="No" <?php echo (($notifEnable_cmd[$i] ?? '') == 'No') ? 'selected' : ''; ?>>No</option>
    <option value="Yes" <?php echo (($notifEnable_cmd[$i] ?? '') == 'Yes') ? 'selected' : ''; ?>>Yes</option>
  </select>
</td>

<td>
  <input type="text" name="notifMsg_cmd[]" 
         value="<?php echo htmlspecialchars($notifMsg_cmd[$i] ?? ''); ?>"
         class="compact-input"
         placeholder="Enter notification message">
</td>




<td>
                    
      
               <div style="display: flex; flex-direction: column; gap: 4px;">
                  <button type="button" class="remove-btn" onclick="removeRow(this, 'cmd')" style="background: #dc3545;">Remove</button>
                  <button type="button" class="add-row-btn" onclick="addRowAtPosition(this, 'cmd')" style="background: #28a745; padding: 6px 12px; font-size: 11px;">Add Row Here</button>
                </div>
              </td>
            </tr>
            <?php } ?>

          </tbody>
        </table>
      </div>
    </div>
     
 <div class="form-navigation">
    <button class="btn" type="button" onclick="goToPreviousStep(2)">Back Step</button>

    <button class="btn" type="button" onclick="prevCommandFrame()">◀ Prev Command</button>
    <span id="cmdFrameIndicator" style="padding:0 10px;font-weight:bold;"></span>
    <button class="btn" type="button" onclick="nextCommandFrame()">Next Command ▶</button>

    <button class="btn" type="button" onclick="goToNextStep(2)">Next Step</button>
  </div>
</div>
</div>





<!-- Step 3: Response Frame -->
<div id="step-3" class="form-step">
  <div class="container">
   <div id="responseFramesContainer"></div>


    
    <div id="step3-error" class="step-error"></div>


    <div class="form-group">
      <label for="resp_parametername">Parameter Name</label>
      <input type="text" name="resp_parametername" id="resp_parametername" 
             value="<?php echo htmlspecialchars($resp_parametername); ?>" readonly />
    </div>

    <!-- Data Flow Direction -->
    <!--div class="form-group">
      <label for="resp_data_flow_direction" class="required">Data Flow Direction</label>
      <input type="text" name="resp_dataflow" id="resp_data_flow_direction"
             value="<?php echo htmlspecialchars($resp_dataflow); ?>" />
    </div-->


    <!-- Change from input to select for Data Flow Direction -->
<div class="form-group">
  <label for="resp_data_flow_direction" class="required">Data Flow Direction</label>
  <select name="resp_dataflow" id="resp_data_flow_direction">
    <option value="App/Server to Device" <?php echo ($resp_dataflow == 'App/Server to Device') ? 'selected' : ''; ?>>App/Server to Device</option>
    <option value="Device to App/Server" <?php echo ($resp_dataflow == 'Device to App/Server') ? 'selected' : ''; ?>>Device to App/Server</option>
  </select>
</div>




    <!-- Frame Type -->
    <div class="form-group">
      <label for="resp_frametype" class="required">Frame Type</label>
      <input type="text" name="resp_frametype" id="resp_frametype"
             value="<?php echo htmlspecialchars($resp_frametype_const); ?>" />
    </div>

    <div class="form-row">
      <div class="form-col">
        <div class="form-group">
          <label for="resp_framesizeinbytes" class="required">Frame size in bytes</label>
          <input type="number" id="resp_framesizeinbytes" name="resp_framesizeinbytes" 
                 value="<?php echo htmlspecialchars($resp_framesizeinbytes); ?>"
                 placeholder="Enter number" min="0" oninput="updateRowsFromFrameSize('resp')" />
        </div>
      </div>
      <div class="form-col">
        <div class="form-group">
          <label for="resp_noofrows" class="required">No of rows</label>
          <input type="number" id="resp_noofrows" name="resp_noofrows" 
                 value="<?php echo htmlspecialchars($resp_noofrows); ?>"
                 readonly />
        </div>
      </div>
    </div>
    
 <!-- Frame Trigger -->
    <div class="form-group">
      <label for="frame_trigger">Frame Trigger</label>
      
      <select name="frame_trigger_options" id="frame_trigger_options">
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
      <label for="resp_multipleui">Multiple UI</label>
      <input type="text" name="resp_multipleui" id="resp_multipleui"
             value="<?php echo htmlspecialchars($resp_multipleui); ?>" />
    </div>

    <!-- Trigger UI -->
    <div class="form-group">
      <label for="resp_triggerui">Trigger UI</label>
      <input type="text" name="resp_triggerui" id="resp_triggerui"
             value="<?php echo htmlspecialchars($resp_triggerui); ?>" />
    </div>

    <!-- Voice Command -->
    <div class="form-group">
      <label for="resp_voicecommand">Voice Command</label>
      <input type="text" id="resp_voicecommand" name="resp_voicecommand" 
             value="<?php echo htmlspecialchars($resp_voicecommand); ?>"
             placeholder="Yes/No" />
    </div>

    <div class="form-group">
      <label for="resp_voice_control_instruction_text">Voice Control Instruction Command</label>
      <input type="file" id="resp_voice_control_instruction_text" name="resp_voice_control_instruction_text" accept="image/*" />
      <small>Upload image for voice control instruction</small>
    </div>

    <div class="form-group">
      <label for="resp_appnotification">Remarks/App Notification</label>
      <textarea id="resp_appnotification" name="resp_appnotification" 
                placeholder="Enter your remarks here..." rows="3"><?php echo htmlspecialchars($resp_appnotification); ?></textarea>
    </div>

    <!-- Response Frame Table -->
    <div class="table-container">
      <div class="table-header">
        <h3 style="margin: 0; color: #333;">Response Frame Configuration</h3>
        <div>
          <button type="button" class="table-header-btn" onclick="addRow('resp')">+ Add Row</button>
          <button type="button" class="table-header-btn" onclick="clearTable('resp')">Clear Table</button>
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
              <th>UI Page No</th>
              <th>AS PER UI</th>
              <th>UI Serial No</th>
              <th>UI Values</th>
              <th>UI In/Out</th>
              <th>Slider Step</th>
              <th>Default Value</th>
              <th>Dev Notes</th>
              <th>Notification Enable</th>
              <th>Notification Message</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="responseTableBody">
            <?php for ($i = 0; $i < $resp_noofrows; $i++) { ?>
            <tr id="row_resp_<?php echo $i + 1; ?>" data-row="<?php echo $i + 1; ?>">
              <td class="sno-column"><?php echo $i + 1; ?></td>
              <td><input type="number" name="startBytePosition_resp[]" 
                         value="<?php echo htmlspecialchars($startBytePosition_resp[$i] ?? ''); ?>"
                         class="compact-input" oninput="calculateByteValues(this, 'resp', <?php echo $i + 1; ?>)"></td>
              <td><input type="number" name="endBytePosition_resp[]" 
                         value="<?php echo htmlspecialchars($endBytePosition_resp[$i] ?? ''); ?>"
                         class="compact-input" oninput="calculateByteValues(this, 'resp', <?php echo $i + 1; ?>)"></td>
              
    <td>
  <div style="display:flex; gap:6px; align-items:center;">
    
    <!-- Field Description -->
    <select name="fieldDescription_cmd[]" 
            class="compact-select field-desc"
            onchange="handleFieldDescriptionChange(this)">
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

    

    <!-- Bitwise / Bytewise -->
    <select class="compact-select bit-byte-select"
            style="display:none"
            onchange="handleBitByteChange(this)">
      <option value="">Mode</option>
      <option value="Bytewise">Bytewise</option>
      <option value="Bitwise">Bitwise</option>
    </select>

    <!-- Arrow -->
    <button type="button"
        class="arrow-btn"
        style="display:none"
        onclick="openBitwiseConfig(this.closest('tr'))">
▶
</button>



  </div>
</td>


              <td><input type="number" name="numberOfBytes_resp[]" 
                         value="<?php echo htmlspecialchars($numberOfBytes_resp[$i] ?? ''); ?>"
                         class="compact-input auto-calculated" readonly></td>
              <td><input type="number" name="bitSize_resp[]" 
                         value="<?php echo htmlspecialchars($bitSize_resp[$i] ?? ''); ?>"
                         class="compact-input auto-calculated" readonly></td>
              <td>
                <select name="dataType_resp[]" class="compact-select" onchange="calculateByteValues(null, 'resp', <?php echo $i + 1; ?>)">
                  <?php
                   $options = ["Select", "ASCII", "Binary", "SignedInt", "Float", "UnsignedInt", "String","Double","String","Bollean","Hex","Enum","Timestamp","None"];
                  foreach ($options as $option) {
                    $selected = (($dataType_resp[$i] ?? '') == $option) ? "selected" : "";
                    echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                  }
                  ?>
                </select>
              </td>
              <td>
                <select name="valueDefinition_resp[]" class="compact-select">
                  <?php
                  $options = ["Select","Fixed Value", "Range Value", "Dynamic Value","Enum Value", "Calculated Value","None"];
                 
                  foreach ($options as $option) {
                    $selected = (($valueDefinition_resp[$i] ?? '') == $option) ? "selected" : "";
                    echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                  }
                  ?>
                </select>
              </td>
              <td><input type="text" name="displayValueMin_resp[]" 
                         value="<?php echo htmlspecialchars($displayValueMin_resp[$i] ?? ''); ?>"
                         class="compact-input auto-calculated" readonly></td>
              <td><input type="text" name="displayValueMax_resp[]" 
                         value="<?php echo htmlspecialchars($displayValueMax_resp[$i] ?? ''); ?>"
                         class="compact-input auto-calculated" readonly></td>
              <td><input type="text" name="valueInHexMin_resp[]" 
                         value="<?php echo htmlspecialchars($valueInHexMin_resp[$i] ?? ''); ?>"
                         class="compact-input"></td>
              <td><input type="text" name="valueInHexMax_resp[]" 
                         value="<?php echo htmlspecialchars($valueInHexMax_resp[$i] ?? ''); ?>"
                         class="compact-input"></td>
              <td>
                <select name="uiType_resp[]" class="compact-select" onchange="applyUIAutoFill(this, 'resp', <?php echo $i + 1; ?>)">
                  <?php
                  $options = ["None", "Text", "Toggle Switch", "Notification", "console", "Enter Text", "Slider", "Brightness Slider", "Airflow Slider", "CCT Slider", "Saturation Slider", "Hue Slider", "Hue Circle", "Hue Slider Custom", "Push Button", "Drop Down", "Trigger", "Display", "Button", "Toast Display", "Auto Trigger with Timer"];
                  foreach ($options as $option) {
                    $selected = (($uiType_resp[$i] ?? '') == $option) ? "selected" : "";
                    echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                  }
                  ?>
                </select>
              </td>
              <td><input type="text" name="uiId_resp[]" 
                         value="<?php echo htmlspecialchars($uiId_resp[$i] ?? ''); ?>"
                         class="compact-input"></td>
              <td>
                <select name="uiPageNo_resp[]" class="compact-select">
                  <?php
                  $options = ["None", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10"];
                  foreach ($options as $option) {
                    $selected = (($uiPageNo_resp[$i] ?? '') == $option) ? "selected" : "";
                    echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                  }
                  ?>
                </select>
              </td>
              <td><input type="text" name="resp_asperui[]" 
                         value="<?php echo htmlspecialchars(implode(',', explode('][', trim($resp_asperui[$i] ?? '', '[]')))); ?>"
                         class="compact-input"></td>
              <td><input type="text" name="resp_sno_asperui[]" 
                         value="<?php echo htmlspecialchars(implode(',', explode('][', trim($resp_sno_asperui[$i] ?? '', '[]')))); ?>"
                         class="compact-input"></td>
              <td><input type="text" name="resp_asperuivalue[]" 
                         value="<?php echo htmlspecialchars(implode(',', explode('][', trim($resp_asperuivalue[$i] ?? '', '[]')))); ?>"
                         class="compact-input"></td>
              <td>
                <select name="uiDescriptionInOut_resp[]" class="compact-select">
                  <?php
                  $options = ["None", "Read/Write", "Read", "Write"];
                  foreach ($options as $option) {
                    $selected = (($uiDescriptionInOut_resp[$i] ?? '') == $option) ? "selected" : "";
                    echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                  }
                  ?>
                </select>
              </td>
              <td><input type="number" name="sliderStepValue_resp[]" 
                         value="<?php echo htmlspecialchars($sliderStepValue_resp[$i] ?? ''); ?>"
                         class="compact-input"></td>
              <td><input type="text" name="defaultvalue_resp[]" 
                         value="<?php echo htmlspecialchars($defaultvalue_resp[$i] ?? ''); ?>"
                         class="compact-input"></td>
              <td><input type="text" name="developerNotes_resp[]" 
                         value="<?php echo htmlspecialchars($developerNotes_resp[$i] ?? ''); ?>"
                         class="compact-input"></td>
               
           <td>
  <select name="notifEnable_resp[]" class="compact-select">
    <option value="No" <?php echo (($notifEnable_resp[$i] ?? '') == 'No') ? 'selected' : ''; ?>>No</option>
    <option value="Yes" <?php echo (($notifEnable_resp[$i] ?? '') == 'Yes') ? 'selected' : ''; ?>>Yes</option>
  </select>
</td>

<td>
  <input type="text" name="notifMsg_resp[]" 
         value="<?php echo htmlspecialchars($notifMsg_resp[$i] ?? ''); ?>"
         class="compact-input"
         placeholder="Enter notification message">
</td>


              <td>
                <div style="display: flex; flex-direction: column; gap: 4px;">
                  <button type="button" class="remove-btn" onclick="removeRow(this, 'resp')" style="background: #dc3545;">Remove</button>
                  <button type="button" class="add-row-btn" onclick="addRowAtPosition(this, 'resp')" style="background: #28a745; padding: 6px 12px; font-size: 11px;">Add Row Here</button>
                </div>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
     
    
  <div class="form-navigation">
  <button class="btn" type="button" onclick="goToPreviousStep(3)">Back Step</button>

  <button class="btn" type="button" onclick="prevResponseFrame()">◀ Prev Response</button>
  <span id="respFrameIndicator" style="padding:0 10px;font-weight:bold;"></span>
  <button class="btn" type="button" onclick="nextResponseFrame()">Next Response ▶</button>

  <button class="btn" type="button" onclick="goToNextStep(3)">Next Step</button>
</div>

  </div>
</div>

<!-- Step 4: Error Frame -->
<div id="step-4" class="form-step">
  <div class="container">
      <div id="errorFramesContainer"></div>

    
        
    <div class="form-group">
      <label for="error_parametername">Parameter Name</label>
      <input type="text" name="error_parametername" id="error_parametername" 
             value="<?php echo htmlspecialchars($error_parametername); ?>" readonly />
    </div>

     <!-- Data Flow Direction -->
    <!--div class="form-group">
      <label for="error_data_flow_direction" class="required">Data Flow Direction</label>
      <input type="text" name="error_dataflow" id="error_data_flow_direction"
             value="<?php echo htmlspecialchars($error_dataflow); ?>" />
    </div-->

   <div class="form-group">
  <label for="error_data_flow_direction" class="required">Data Flow Direction</label>
  <select name="error_dataflow" id="error_data_flow_direction">
    <option value="App/Server to Device" <?php echo ($error_dataflow == 'App/Server to Device') ? 'selected' : ''; ?>>App/Server to Device</option>
    <option value="Device to App/Server" <?php echo ($error_dataflow == 'Device to App/Server') ? 'selected' : ''; ?>>Device to App/Server</option>
  </select>
</div>

    <!-- Frame Type -->
    <div class="form-group">
      <label for="error_frametype" class="required">Frame Type</label>
      <input type="text" name="error_frametype" id="error_frametype"
             value="<?php echo htmlspecialchars($error_frametype_const); ?>" />
    </div>

    <div class="form-row">
      <div class="form-col">
        <div class="form-group">
          <label for="error_framesizeinbytes" class="required">Frame size in bytes</label>
          <input type="number" id="error_framesizeinbytes" name="error_framesizeinbytes" 
                 value="<?php echo htmlspecialchars($error_framesizeinbytes); ?>"
                 placeholder="Enter number" min="0" oninput="updateRowsFromFrameSize('error')" />
        </div>
      </div>
      <div class="form-col">
        <div class="form-group">
          <label for="error_noofrows" class="required">No of rows</label>
          <input type="number" id="error_noofrows" name="error_noofrows" 
                 value="<?php echo htmlspecialchars($error_noofrows); ?>"
                 readonly />
        </div>
      </div>
    </div>

   <!-- Frame Trigger -->
    <div class="form-group">
      <label for="frame_trigger">Frame Trigger</label>
        <select name="frame_trigger_options" id="frame_trigger_options">
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
      <label for="error_multipleui">Multiple UI</label>
      <input type="text" name="error_multipleui" id="error_multipleui"
             value="<?php echo htmlspecialchars($error_multipleui); ?>" />
    </div>

    <!-- Trigger UI -->
    <div class="form-group">
      <label for="error_triggerui">Trigger UI</label>
      <input type="text" name="error_triggerui" id="error_triggerui"
             value="<?php echo htmlspecialchars($error_triggerui); ?>" />
    </div>

    <!-- Voice Command -->
    <div class="form-group">
      <label for="error_voicecommand">Voice Command</label>
      <input type="text" id="error_voicecommand" name="error_voicecommand" 
             value="<?php echo htmlspecialchars($error_voicecommand); ?>"
             placeholder="Yes/No" />
    </div>

    <div class="form-group">
      <label for="error_voice_control_instruction_text">Voice Control Instruction Command</label>
      <input type="file" id="error_voice_control_instruction_text" name="error_voice_control_instruction_text" accept="image/*" />
      <small>Upload image for voice control instruction</small>
    </div>

    <div class="form-group">
      <label for="error_appnotification">Remarks/App Notification</label>
      <textarea id="error_appnotification" name="error_appnotification" 
                placeholder="Enter your remarks here..." rows="3"><?php echo htmlspecialchars($error_appnotification); ?></textarea>
    </div>

    <!-- Error Frame Table -->
    <div class="table-container">
      <div class="table-header">
        <h3 style="margin: 0; color: #333;">Error Frame Configuration</h3>
        <div>
          <button type="button" class="table-header-btn" onclick="addRow('error')">+ Add Row</button>
          <button type="button" class="table-header-btn" onclick="clearTable('error')">Clear Table</button>
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
              <th>UI Page No</th>
              <th>AS PER UI</th>
              <th>UI Serial No</th>
              <th>UI Values</th>
              <th>UI In/Out</th>
              <th>Slider Step</th>
              <th>Default Value</th>
              <th>Dev Notes</th>
               <th>Notification Enable</th>
              <th>Notification Message</th>
              
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="errorTableBody">
            <?php for ($i = 0; $i < $error_noofrows; $i++) { ?>
            <tr id="row_error_<?php echo $i + 1; ?>" data-row="<?php echo $i + 1; ?>">
              <td class="sno-column"><?php echo $i + 1; ?></td>
              <td><input type="number" name="startBytePosition_error[]" 
                         value="<?php echo htmlspecialchars($startBytePosition_error[$i] ?? ''); ?>"
                         class="compact-input" oninput="calculateByteValues(this, 'error', <?php echo $i + 1; ?>)"></td>
              <td><input type="number" name="endBytePosition_error[]" 
                         value="<?php echo htmlspecialchars($endBytePosition_error[$i] ?? ''); ?>"
                         class="compact-input" oninput="calculateByteValues(this, 'error', <?php echo $i + 1; ?>)"></td>
             
<td>
  <div style="display:flex; gap:6px; align-items:center;">
    
    <!-- Field Description -->
    <select name="fieldDescription_cmd[]" 
            class="compact-select field-desc"
            onchange="handleFieldDescriptionChange(this)">
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

    

    <!-- Bitwise / Bytewise -->
    <select class="compact-select bit-byte-select"
            style="display:none"
            onchange="handleBitByteChange(this)">
      <option value="">Mode</option>
      <option value="Bytewise">Bytewise</option>
      <option value="Bitwise">Bitwise</option>
    </select>

    <!-- Arrow -->
    <button type="button"
        class="arrow-btn"
        style="display:none"
        onclick="openBitwiseConfig(this.closest('tr'))">
▶
</button>



  </div>
</td>

              <td><input type="number" name="numberOfBytes_error[]" 
                         value="<?php echo htmlspecialchars($numberOfBytes_error[$i] ?? ''); ?>"
                         class="compact-input auto-calculated" readonly></td>
              <td><input type="number" name="bitSize_error[]" 
                         value="<?php echo htmlspecialchars($bitSize_error[$i] ?? ''); ?>"
                         class="compact-input auto-calculated" readonly></td>
              <td>
                <select name="dataType_error[]" class="compact-select" onchange="calculateByteValues(null, 'error', <?php echo $i + 1; ?>)">
                  <?php
                  $options = ["Select", "ASCII", "Binary", "SignedInt", "Float", "UnsignedInt", "String","Double","String","Bollean","Hex","Enum","Timestamp","None"];
                  foreach ($options as $option) {
                    $selected = (($dataType_error[$i] ?? '') == $option) ? "selected" : "";
                    echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                  }
                  ?>
                </select>
              </td>
              <td>
                <select name="valueDefinition_error[]" class="compact-select">
                  <?php
                  $options = ["Select","Fixed Value", "Range Value", "Dynamic Value","Enum Value", "Calculated Value","None"];
                  
                  foreach ($options as $option) {
                    $selected = (($valueDefinition_error[$i] ?? '') == $option) ? "selected" : "";
                    echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                  }
                  ?>
                </select>
              </td>
              <td><input type="text" name="displayValueMin_error[]" 
                         value="<?php echo htmlspecialchars($displayValueMin_error[$i] ?? ''); ?>"
                         class="compact-input auto-calculated" readonly></td>
              <td><input type="text" name="displayValueMax_error[]" 
                         value="<?php echo htmlspecialchars($displayValueMax_error[$i] ?? ''); ?>"
                         class="compact-input auto-calculated" readonly></td>
              <td><input type="text" name="valueInHexMin_error[]" 
                         value="<?php echo htmlspecialchars($valueInHexMin_error[$i] ?? ''); ?>"
                         class="compact-input"></td>
              <td><input type="text" name="valueInHexMax_error[]" 
                         value="<?php echo htmlspecialchars($valueInHexMax_error[$i] ?? ''); ?>"
                         class="compact-input"></td>
              <td>
                <select name="uiType_error[]" class="compact-select" onchange="applyUIAutoFill(this, 'error', <?php echo $i + 1; ?>)">
                  <?php
                  $options = ["None", "Text", "Toggle Switch", "Notification", "console", "Enter Text", "Slider", "Brightness Slider", "Airflow Slider", "CCT Slider", "Saturation Slider", "Hue Slider Custom", "Hue Slider", "Hue Circle", "Push Button", "Drop Down", "Trigger", "Display", "Button", "Toast Display", "Auto Trigger with Timer"];
                  foreach ($options as $option) {
                    $selected = (($uiType_error[$i] ?? '') == $option) ? "selected" : "";
                    echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                  }
                  ?>
                </select>
              </td>
              <td><input type="text" name="uiId_error[]" 
                         value="<?php echo htmlspecialchars($uiId_error[$i] ?? ''); ?>"
                         class="compact-input"></td>
              <td>
                <select name="uiPageNo_error[]" class="compact-select">
                  <?php
                  $options = ["None", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10"];
                  foreach ($options as $option) {
                    $selected = (($uiPageNo_error[$i] ?? '') == $option) ? "selected" : "";
                    echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                  }
                  ?>
                </select>
              </td>
              <td><input type="text" name="error_asperui[]" 
                         value="<?php echo htmlspecialchars(implode(',', explode('][', trim($error_asperui[$i] ?? '', '[]')))); ?>"
                         class="compact-input"></td>
              <td><input type="text" name="error_sno_asperui[]" 
                         value="<?php echo htmlspecialchars(implode(',', explode('][', trim($error_sno_asperui[$i] ?? '', '[]')))); ?>"
                         class="compact-input"></td>
              <td><input type="text" name="error_asperuivalue[]" 
                         value="<?php echo htmlspecialchars(implode(',', explode('][', trim($error_asperuivalue[$i] ?? '', '[]')))); ?>"
                         class="compact-input"></td>
              <td>
                <select name="uiDescriptionInOut_error[]" class="compact-select">
                  <?php
                  $options = ["None", "Read/Write", "Read", "Write"];
                  foreach ($options as $option) {
                    $selected = (($uiDescriptionInOut_error[$i] ?? '') == $option) ? "selected" : "";
                    echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                  }
                  ?>
                </select>
              </td>
              <td><input type="number" name="sliderStepValue_error[]" 
                         value="<?php echo htmlspecialchars($sliderStepValue_error[$i] ?? ''); ?>"
                         class="compact-input"></td>
              <td><input type="text" name="defaultvalue_error[]" 
                         value="<?php echo htmlspecialchars($defaultvalue_error[$i] ?? ''); ?>"
                         class="compact-input"></td>
              <td><input type="text" name="developerNotes_error[]" 
                         value="<?php echo htmlspecialchars($developerNotes_error[$i] ?? ''); ?>"
                         class="compact-input"></td>


<td>
  <select name="notifEnable_error[]" class="compact-select">
    <option value="No" <?php echo (($notifEnable_resp[$i] ?? '') == 'No') ? 'selected' : ''; ?>>No</option>
    <option value="Yes" <?php echo (($notifEnable_resp[$i] ?? '') == 'Yes') ? 'selected' : ''; ?>>Yes</option>
  </select>
</td>

<td>
  <input type="text" name="notifMsg_error[]" 
         value="<?php echo htmlspecialchars($notifMsg_resp[$i] ?? ''); ?>"
         class="compact-input"
         placeholder="Enter notification message">
</td>

              <td>
                <div style="display: flex; flex-direction: column; gap: 4px;">
                  <button type="button" class="remove-btn" onclick="removeRow(this, 'error')" style="background: #dc3545;">Remove</button>
                  <button type="button" class="add-row-btn" onclick="addRowAtPosition(this, 'error')" style="background: #28a745; padding: 6px 12px; font-size: 11px;">Add Row Here</button>
                </div>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="form-navigation">
             <button class="btn" type="button" onclick="goToPreviousStep(4)">Back</button>

           <button class="btn" type="button" onclick="prevErrorFrame()">◀ Prev Error</button>
           <span id="errorFrameIndicator" style="font-weight:bold; display:block; margin-top:25px;"></span>
     
         <button class="btn" type="button" onclick="nextErrorFrame()">Next Error ▶</button>
    </div>


    <div class="save-btn-container">
      <button type="submit" class="save-btn" onclick="saveConfiguration()">Save Configuration</button>
    </div>
  </div>
</div>
<!-- ================= BITWISE MODAL (GLOBAL) ================= -->
   <div id="bitwiseConfigModal" class="bitwise-modal">
  <div class="bitwise-modal-content">
    <h2>Bitwise Configuration</h2>

    <div class="bitwise-table-wrapper">
      <table id="bitwiseTable">

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
      <button onclick="saveBitwiseConfig(event)">Save</button>
      <button onclick="closeBitwiseConfig(event)">Cancel</button>
    </div>

  </div>
</div>
     

      
<?php echo form_close(); ?>

<script>
/* ===========================
   JavaScript - main script
   =========================== */
let frameDataStorage = {
    cmd: {},   // Stores Command frames: { 0: [rows], 1: [rows] }
    resp: {},  // Stores Response frames
    error: {}  // Stores Error frames
};
let bitwiseStore = {};
const masterConfigStore = {
    cmd: {},   // Will look like { 0: [rows], 1: [rows] }
    resp: {},
    error: {}
};

// Global variables
let currentStep = 1;
const totalSteps = 4;

// Initialize form
document.addEventListener("DOMContentLoaded", function() {
  // Sync parameter name listeners
  syncParameterNameToAllSteps();
  updateProgressIndicator();

  // Initialize auto-calculations for existing rows
  initializeAutoCalculations();
  
  // Attach UI auto-fill handlers
  attachUIAutoFillHandlers();
  
  // Attach field description handlers
  attachFieldDescriptionHandlers();
  
  // Set up frame size listeners
  setupFrameSizeListeners();
  
  // Initialize existing rows with UI auto-fill if needed
  initializeExistingRows();
});




// Initialize existing rows with UI auto-fill
function initializeExistingRows() {
  // For all frame types
  const frameTypes = ['cmd', 'resp', 'error'];
  
  frameTypes.forEach(type => {
    const rowCount = type === 'cmd' ? <?php echo $cmd_noofrows; ?> : 
                    (type === 'resp' ? <?php echo $resp_noofrows; ?> : <?php echo $error_noofrows; ?>);
    
    for (let i = 1; i <= rowCount; i++) {
      const row = document.querySelector(`#row_${type}_${i}`);
      if (row) {
        const uiSelect = row.querySelector(`select[name="uiType_${type}[]"]`);
        if (uiSelect && uiSelect.value && uiSelect.value !== 'None') {
          // Apply UI auto-fill immediately
          applyUIAutoFill(uiSelect, type, i);
        }
      }
    }
  });
}


// Initialize auto-calculations
function initializeAutoCalculations() {
  // Calculate for command rows
  for (let i = 1; i <= <?php echo $cmd_noofrows; ?>; i++) {
    calculateByteValues(null, 'cmd', i);
  }
  // Calculate for response rows
  for (let i = 1; i <= <?php echo $resp_noofrows; ?>; i++) {
    calculateByteValues(null, 'resp', i);
  }
  // Calculate for error rows
  for (let i = 1; i <= <?php echo $error_noofrows; ?>; i++) {
    calculateByteValues(null, 'error', i);
  }
}

// Attach UI auto-fill handlers
function attachUIAutoFillHandlers() {
  // For all frame types
  const frameTypes = ['cmd', 'resp', 'error'];
  
  frameTypes.forEach(type => {
    const rowCount = type === 'cmd' ? <?php echo $cmd_noofrows; ?> : 
                    (type === 'resp' ? <?php echo $resp_noofrows; ?> : <?php echo $error_noofrows; ?>);
    
    for (let i = 1; i <= rowCount; i++) {
      const rowId = `row_${type}_${i}`;
      const row = document.getElementById(rowId);
      if (row) {
        const uiSelect = row.querySelector(`select[name="uiType_${type}[]"]`);
        if (uiSelect) {
          uiSelect.addEventListener('change', function() {
            applyUIAutoFill(this, type, i);
          });
        }
      }
    }
  });
}

// Attach field description handlers
function attachFieldDescriptionHandlers() {
  // For all frame types
  const frameTypes = ['cmd', 'resp', 'error'];
  
  frameTypes.forEach(type => {
    const rowCount = type === 'cmd' ? <?php echo $cmd_noofrows; ?> : 
                    (type === 'resp' ? <?php echo $resp_noofrows; ?> : <?php echo $error_noofrows; ?>);
    
    for (let i = 1; i <= rowCount; i++) {
      const rowId = `row_${type}_${i}`;
      const row = document.getElementById(rowId);
      if (row) {
        const fieldDescSelect = row.querySelector(`select[name="fieldDescription_${type}[]"]`);
        if (fieldDescSelect) {
          fieldDescSelect.addEventListener('change', function() {
            handleFieldDescriptionChange(this, type, i);
          });
          // Apply immediately if Terminator is already selected
          if (fieldDescSelect.value === 'Terminator') {
            handleFieldDescriptionChange(fieldDescSelect, type, i);
          }
        }
      }
    }
  });
}

// Setup frame size listeners
function setupFrameSizeListeners() {
  const frameSizeInput = document.getElementById('cmd_framesizeinbytes');
  const noOfRowsInput = document.getElementById('cmd_noofrows');

  if (frameSizeInput && noOfRowsInput) {
    frameSizeInput.addEventListener('input', () => {
      updateRowsFromFrameSize('cmd');
    });
  }

  const respFrameSizeInput = document.getElementById('resp_framesizeinbytes');
  const respNoOfRowsInput = document.getElementById('resp_noofrows');

  if (respFrameSizeInput && respNoOfRowsInput) {
    respFrameSizeInput.addEventListener('input', () => {
      updateRowsFromFrameSize('resp');
    });
  }

  const errorFrameSizeInput = document.getElementById('error_framesizeinbytes');
  const errorNoOfRowsInput = document.getElementById('error_noofrows');

  if (errorFrameSizeInput && errorNoOfRowsInput) {
    errorFrameSizeInput.addEventListener('input', () => {
      updateRowsFromFrameSize('error');
    });
  }
}

// Update rows based on frame size input
function updateRowsFromFrameSize(type) {
  const frameSizeInput = document.getElementById(`${type}_framesizeinbytes`);
  const noOfRowsInput = document.getElementById(`${type}_noofrows`);
  
  if (!frameSizeInput || !noOfRowsInput) return;
  
  const frameSize = parseInt(frameSizeInput.value) || 0;
  
  // Update the "No of rows" field
  noOfRowsInput.value = frameSize;
  
  // Update table rows
  updateTableRows(type, frameSize);
}

// Update table rows based on frame size
function updateTableRows(type, frameSize) {
  const tableId = type === 'cmd' ? 'additionalTableBody' : 
                 (type === 'resp' ? 'responseTableBody' : 'errorTableBody');
  const tableBody = document.getElementById(tableId);
  if (!tableBody) return;
  
  const currentRows = tableBody.rows.length;

  // Add rows if frameSize > currentRows
  if (frameSize > currentRows) {
    for (let i = currentRows; i < frameSize; i++) {
      addRow(type); // Always add at the end
    }
  }
  // Remove rows if frameSize < currentRows
  else if (frameSize < currentRows) {
    if (confirm(`Reducing frame size will remove ${currentRows - frameSize} row(s). Continue?`)) {
      for (let i = currentRows; i > frameSize; i--) {
        tableBody.deleteRow(i - 1);
      }
    } else {
      // Reset the frame size to current rows
      const frameSizeInput = document.getElementById(`${type}_framesizeinbytes`);
      const noOfRowsInput = document.getElementById(`${type}_noofrows`);
      if (frameSizeInput) frameSizeInput.value = currentRows;
      if (noOfRowsInput) noOfRowsInput.value = currentRows;
      return;
    }
  }

  updateSerialNumbers(tableId);
}

// Navigation functions
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

  // ✅ STEP 2 → CMD FRAME
  if (step === 2) {

    setTimeout(() => {

      // ensure at least 1 row exists
      const tbody = document.getElementById("additionalTableBody");

      if (tbody && tbody.rows.length === 0) {
        addRow("cmd");
      }

      loadCmdViewTable();
printStepSummary("COMMAND");


      printCmdFrameToConsole();

    }, 200);

  }

  // ✅ STEP 3 → RESPONSE FRAME
  if (step === 3) {

    setTimeout(() => {

      const tbody = document.getElementById("responseAdditionalTableBody");

      // IMPORTANT: create rows first
      if (tbody && tbody.rows.length === 0) {
        addRow("resp");
      }

      // now load values
      loadRespViewTable();
printStepSummary("COMMAND");
        printStepSummary("RESPONSE");
      

      // print console automatically
      printRespFrameToConsole();

    }, 200);

  }

  // ✅ STEP 4 → ERROR FRAME (Added)
if (step === 4) {
    setTimeout(() => {
      // Use the ID from your HTML: errorTableBody
      const tbody = document.getElementById("errorTableBody");

      // 1. Ensure at least one row exists if it's empty
      if (tbody && tbody.rows.length === 0) {
        addRow("error");
      }

      // 2. Load the stored data for the current frame
      // This function should handle filling the table with errorFrameData[currentErrorFrame]
      loadErrorViewTable(); 


      // 3. Print the frame data to console for debugging
      printErrorFrameToConsole();

    }, 200);
  }

  currentStep = step;
  updateProgressIndicator();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
function goToNextStep(current) {
  // Validate current step
  if (validateStep(current)) {
    if (current < totalSteps) {
      showStep(current + 1);
    }
  }
}

function goToPreviousStep(current) {
  if (current > 1) {
    showStep(current - 1);
  }
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

// Auto-calculation functions
function calculateByteValues(inputElement, prefix, index) {
  const rowId = `row_${prefix}_${index}`;
  const row = document.getElementById(rowId);
  if (!row) return;
  
  const startByteInput = row.querySelector(`input[name="startBytePosition_${prefix}[]"]`);
  const endByteInput = row.querySelector(`input[name="endBytePosition_${prefix}[]"]`);
  
  if (!startByteInput || !endByteInput) return;
  
  const startByte = parseInt(startByteInput.value) || 0;
  const endByte = parseInt(endByteInput.value) || 0;
  
  if (startByte >= 0 && endByte >= 0 && endByte >= startByte) {
    // Calculate number of bytes
    const numberOfBytes = (endByte - startByte) + 1;
    
    // Calculate bit size (8 bits per byte)
    const bitSize = numberOfBytes * 8;
    
    // Calculate display min and max based on data type
    const dataTypeSelect = row.querySelector(`select[name="dataType_${prefix}[]"]`);
    const dataType = dataTypeSelect ? dataTypeSelect.value : 'UnsignedInt';
    
    let displayMin = 0;
    let displayMax = 0;
    
    switch(dataType) {
      case 'UnsignedInt':
        displayMax = Math.pow(2, bitSize) - 1;
        break;
      case 'SignedInt':
        const halfRange = Math.pow(2, bitSize - 1);
        displayMin = -halfRange;
        displayMax = halfRange - 1;
        break;
      case 'Binary':
        displayMax = Math.pow(2, bitSize) - 1;
        break;
      case 'ASCII':
        displayMin = 0;
        displayMax = Math.pow(2, bitSize) - 1;
        break;
      case 'Float':
        displayMin = 0;
        displayMax = 100;
        break;
      case 'String':
        displayMin = 0;
        displayMax = numberOfBytes;
        break;
      default:
        displayMax = Math.pow(2, bitSize) - 1;
    }
    
    // Update the calculated fields
    const numberOfBytesInput = row.querySelector(`input[name="numberOfBytes_${prefix}[]"]`);
    const bitSizeInput = row.querySelector(`input[name="bitSize_${prefix}[]"]`);
    const displayMinInput = row.querySelector(`input[name="displayValueMin_${prefix}[]"]`);
    const displayMaxInput = row.querySelector(`input[name="displayValueMax_${prefix}[]"]`);
    
    if (numberOfBytesInput) {
      numberOfBytesInput.value = numberOfBytes;
      numberOfBytesInput.classList.add('auto-calculated', 'readonly-field');
      numberOfBytesInput.readOnly = true;
    }
    
    if (bitSizeInput) {
      bitSizeInput.value = bitSize;
      bitSizeInput.classList.add('auto-calculated', 'readonly-field');
      bitSizeInput.readOnly = true;
    }
    
    if (displayMinInput) {
      displayMinInput.value = displayMin;
      displayMinInput.classList.add('auto-calculated', 'readonly-field');
      displayMinInput.readOnly = true;
    }
    
    if (displayMaxInput) {
      displayMaxInput.value = displayMax;
      displayMaxInput.classList.add('auto-calculated', 'readonly-field');
      displayMaxInput.readOnly = true;
    }
    
    // Also calculate hex values
    const hexMinInput = row.querySelector(`input[name="valueInHexMin_${prefix}[]"]`);
    const hexMaxInput = row.querySelector(`input[name="valueInHexMax_${prefix}[]"]`);
    
    if (hexMinInput && !hexMinInput.value) {
      hexMinInput.value = '0x00';
    }
    
    if (hexMaxInput) {
      const hexValue = Math.min(255, displayMax).toString(16).toUpperCase().padStart(2, '0');
      hexMaxInput.value = `0x${hexValue}`;
    }
  }
}

/* ========== Auto-fill based on UI Type ========== */
function applyUIAutoFill(selectElement, prefix, index) {
  if (!selectElement) return;
  const uiType = selectElement.value;
  
  // Get the row
  const rowId = `row_${prefix}_${index}`;
  const row = document.getElementById(rowId);
  
  if (!row) {
    console.error(`Row not found: ${rowId}`);
    return;
  }

  // Find the row and inputs - FIXED: Using exact field names
  const valueDefSelect = row.querySelector(`select[name="valueDefinition_${prefix}[]"]`);
  const dataTypeSelect = row.querySelector(`select[name="dataType_${prefix}[]"]`);
  const asperUiInput = row.querySelector(`input[name="${prefix}_asperui[]"]`);
  const uiValuesInput = row.querySelector(`input[name="${prefix}_asperuivalue[]"]`);
  const uiSerialInput = row.querySelector(`input[name="${prefix}_sno_asperui[]"]`);

  if (!asperUiInput || !uiValuesInput || !uiSerialInput) {
    console.log(`Missing inputs for prefix ${prefix}, index ${index}`);
    console.log('asperUiInput:', asperUiInput);
    console.log('uiValuesInput:', uiValuesInput);
    console.log('uiSerialInput:', uiSerialInput);
    return;
  }

  // Normalize to help matching (lowercase for comparisons)
  const normalized = (uiType || '').toString().trim().toLowerCase();

  // Toggle Switch -> set AS PER UI as [false,true] and AS PER UI VALUES as [00,01]
  if (normalized === 'toggle switch') {
    // set dropdown to "Fixed Value"
    if (valueDefSelect) {
      valueDefSelect.value = 'Fixed Value';
      valueDefSelect.classList.add('auto-calculated');
    }
    if (dataTypeSelect) {
      dataTypeSelect.value = 'UnsignedInt';
      dataTypeSelect.classList.add('auto-calculated');
    }
    asperUiInput.value = '[false,true]';
    uiValuesInput.value = '[00,01]';
  }
  
// Slider-like UI
else if (normalized.indexOf('slider') !== -1 || normalized === 'hue circle') {

  if (valueDefSelect) {
    valueDefSelect.value = 'Range Value';
    valueDefSelect.classList.add('auto-calculated');
  }

  if (dataTypeSelect) {
    dataTypeSelect.value = 'UnsignedInt';
    dataTypeSelect.classList.add('auto-calculated');
  }

  // ✅ ONLY SET if empty (IMPORTANT FIX)
  if (!asperUiInput.value || asperUiInput.value.trim() === '') {
    asperUiInput.value = '[0,0]';
  }

  if (!uiValuesInput.value || uiValuesInput.value.trim() === '') {
    uiValuesInput.value = '';
  }
}


  // Drop Down or Notification - mark fields as required
  else if (normalized === 'drop down' || normalized === 'notification') {
    asperUiInput.required = true;
    uiValuesInput.required = true;
    uiSerialInput.required = true;
    asperUiInput.placeholder = "Required";
    uiValuesInput.placeholder = "Required";
    uiSerialInput.placeholder = "Required";
  }
  // Text types
  else if (normalized === 'text' || normalized === 'enter text') {
    if (valueDefSelect) {
      valueDefSelect.value = 'Range Value';
      valueDefSelect.classList.add('auto-calculated');
    }
    if (dataTypeSelect) {
      dataTypeSelect.value = 'UnsignedInt';
      dataTypeSelect.classList.add('auto-calculated');
    }
    asperUiInput.value = '';
    uiValuesInput.value = '';
  }
  else {
    // For other UI types we do not force anything
    asperUiInput.placeholder = "";
    uiValuesInput.placeholder = "";
    uiSerialInput.placeholder = "";
  }

  // Add visual markers to show these fields are auto-updated
  if (valueDefSelect) valueDefSelect.classList.add('auto-calculated');
  if (dataTypeSelect) dataTypeSelect.classList.add('auto-calculated');
  asperUiInput.classList.add('auto-calculated');
}

// Handle field description changes (for Terminator -> set Data to None)
function handleFieldDescriptionChange(selectElement, prefix, index) {
  if (!selectElement) return;
  const fieldDesc = selectElement.value;
  const rowId = `row_${prefix}_${index}`;
  const row = document.getElementById(rowId);
  if (!row) return;
  
  if (fieldDesc === 'Terminator') {
    // Set Data field to "None"
    const dataTypeSelect = row.querySelector(`select[name="dataType_${prefix}[]"]`);
    if (dataTypeSelect) {
      dataTypeSelect.value = 'None';
      dataTypeSelect.classList.add('auto-calculated');
    }
    
    // Also set valueDefinition to "None"
    const valueDefSelect = row.querySelector(`select[name="valueDefinition_${prefix}[]"]`);
    if (valueDefSelect) {
      valueDefSelect.value = 'None';
      valueDefSelect.classList.add('auto-calculated');
    }
  }
}




// Table Management Functions
function addRow(type, position = null) {
  const tableConfigs = {
    'cmd': { tableId: 'additionalTableBody', prefix: 'cmd' },
    'resp': { tableId: 'responseTableBody', prefix: 'resp' },
    'error': { tableId: 'errorTableBody', prefix: 'error' }
  };
  
  const config = tableConfigs[type];
  if (!config) return;
  
  const tableBody = document.getElementById(config.tableId);
  if (!tableBody) return;
  
  let newRow, newIndex;
  const currentRows = tableBody.rows.length;
  
  if (position === null || position >= currentRows) {
    
    newRow = tableBody.insertRow();
    newIndex = currentRows;
  } else {
    // Insert at specific position
    newRow = tableBody.insertRow(position);
    newIndex = position;
  }
  
  const rowId = `row_${type}_${newIndex}`;
  newRow.id = rowId;
  newRow.setAttribute('data-row', newIndex);
  
  // Generate HTML for new row
  newRow.innerHTML = getTableRowHTML(newIndex, config.prefix, newIndex);
  
  // Attach event handlers for the new row
  attachEventHandlersForNewRow(newRow, config.prefix, newIndex);
  
  // Update frame counts
  updateFrameCountsForPrefix(config.prefix);
  
  // Update serial numbers for the entire table
  updateSerialNumbers(config.tableId);
}



// New function to add row at specific position
function addRowAtPosition(button, type) {
  const row = button.closest('tr');
  if (!row) return;
  
  const tableBody = row.parentNode;
  const position = row.rowIndex; // Get the index of current row (0-based)
  
  // Add new row AFTER the current row (position)
  addRow(type, position);
}


function getTableRowHTML(index, prefix, rowNum) {
  return `
    <td class="sno-column">${rowNum}</td>
    <td><input type="number" name="startBytePosition_${prefix}[]" class="compact-input" oninput="calculateByteValues(this, '${prefix}', ${index})"></td>
    <td><input type="number" name="endBytePosition_${prefix}[]" class="compact-input" oninput="calculateByteValues(this, '${prefix}', ${index})"></td>
    
<!-- Field Description + Bitwise Logic -->
    <td>
      <div style="display:flex; gap:6px; align-items:center;">

        <select name="fieldDescription_${prefix}[]" 
                class="compact-select field-desc"
                onchange="handleFieldDescriptionChange(this)">
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

        <!-- Bitwise / Bytewise -->
        <select class="compact-select bit-byte-select"
                style="display:none"
                onchange="handleBitByteChange(this)">
          <option value="Bytewise">Bytewise</option>
          <option value="Bitwise">Bitwise</option>
        </select>

        <button type="button"
                class="arrow-btn"
                style="display:none"
                onclick="openBitwiseConfig(this.closest('tr'))">
          ▶
        </button>
      </div>
    </td>

    <td><input type="number" name="numberOfBytes_${prefix}[]" class="compact-input auto-calculated" readonly></td>
    <td><input type="number" name="bitSize_${prefix}[]" class="compact-input auto-calculated" readonly></td>
    <td>
      <select name="dataType_${prefix}[]" class="compact-select" onchange="calculateByteValues(null, '${prefix}', ${index})">
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
    <td>
      <select name="valueDefinition_${prefix}[]" class="compact-select">
         <option value="">Select</option>
    <option value="Fixed Value">Fixed Value</option>
    <option value="Range Value">Range Value</option>
    <option value="Dynamic Value">Dynamic Value</option>
    <option value="Enum Value">Enum Value</option>
    <option value="Calculated Value">Calculated Value</option>
    <option value="None">None</option>
      </select>
    </td>
    <td><input type="text" name="displayValueMin_${prefix}[]" class="compact-input auto-calculated" readonly></td>
    <td><input type="text" name="displayValueMax_${prefix}[]" class="compact-input auto-calculated" readonly></td>
    <td><input type="text" name="valueInHexMin_${prefix}[]" class="compact-input"></td>
    <td><input type="text" name="valueInHexMax_${prefix}[]" class="compact-input"></td>
    <td>
      <select name="uiType_${prefix}[]" class="compact-select" onchange="applyUIAutoFill(this, '${prefix}', ${index})">
        <option value="None">None</option>
        <option value="Text">Text</option>
        <option value="Toggle Switch">Toggle Switch</option>
        <option value="Notification">Notification</option>
        <option value="console">console</option>
        <option value="Enter Text">Enter Text</option>
        <option value="Slider">Slider</option>
        <option value="Brightness Slider">Brightness Slider</option>
        <option value="Airflow Slider">Airflow Slider</option>
        <option value="CCT Slider">CCT Slider</option>
        <option value="Saturation Slider">Saturation Slider</option>
        <option value="Hue Slider">Hue Slider</option>
        <option value="Hue Slider Custom">Hue Slider Custom</option>
        <option value="Hue Circle">Hue Circle</option>
        <option value="Push Button">Push Button</option>
        <option value="Drop Down">Drop Down</option>
        <option value="Trigger">Trigger</option>
        <option value="Display">Display</option>
        <option value="Button">Button (Big)</option>
        <option value="Toast Display">Toast Display</option>
        <option value="Auto Trigger with Timer">Auto Trigger with Timer</option>
      </select>
    </td>
    <td><input type="text" name="uiId_${prefix}[]" class="compact-input"></td>
    <td>
      <select name="uiPageNo_${prefix}[]" class="compact-select">
        <option value="None">None</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
      </select>
    </td>
    <td><input type="text" name="${prefix}_asperui[]" class="compact-input"></td>
    <td><input type="text" name="${prefix}_sno_asperui[]" class="compact-input"></td>
    <td><input type="text" name="${prefix}_asperuivalue[]" class="compact-input"></td>
    <td>
      <select name="uiDescriptionInOut_${prefix}[]" class="compact-select">
        <option value="None">None</option>
        <option value="Read/Write">Read/Write</option>
        <option value="Read">Read</option>
        <option value="Write">Write</option>
      </select>
    </td>
    <td><input type="number" name="sliderStepValue_${prefix}[]" class="compact-input"></td>
    <td><input type="text" name="defaultvalue_${prefix}[]" class="compact-input"></td>
    <td><input type="text" name="developerNotes_${prefix}[]" class="compact-input"></td>
       <td>
  <select name="notifEnable_${prefix}[]" class="compact-select">
    <option value="No">No</option>
    <option value="Yes">Yes</option>
  </select>
</td>
<td>
  <input type="text" 
         name="notifMsg_${prefix}[]" 
         class="compact-input"
         placeholder="Enter notification message">
</td>

  
    
      <div style="display: flex; flex-direction: column; gap: 4px;">
        <button type="button" class="remove-btn" onclick="removeRow(this, '${prefix}')" style="background: #dc3545;">Remove</button>
        <button type="button" class="add-row-btn" onclick="addRowAtPosition(this, '${prefix}')" style="background: #28a745; padding: 6px 12px; font-size: 11px;">Add Row Here</button>
      </div>
    </td>
  `;
}



// Attach event handlers for new row
function attachEventHandlersForNewRow(row, prefix, index) {
  if (!row) return;
  
  // Attach UI type change handler
  const uiSelect = row.querySelector(`select[name="uiType_${prefix}[]"]`);
  if (uiSelect) {
    uiSelect.addEventListener('change', function() {
      applyUIAutoFill(this, prefix, index);
    });
  }
  
  // Attach field description change handler
  const fieldDescSelect = row.querySelector(`select[name="fieldDescription_${prefix}[]"]`);
  if (fieldDescSelect) {
    fieldDescSelect.addEventListener('change', function() {
      handleFieldDescriptionChange(this, prefix, index);
    });
  }
  
  // Attach byte calculation handlers
  const startByteInput = row.querySelector(`input[name="startBytePosition_${prefix}[]"]`);
  const endByteInput = row.querySelector(`input[name="endBytePosition_${prefix}[]"]`);
  if (startByteInput && endByteInput) {
    startByteInput.addEventListener('input', function() {
      calculateByteValues(null, prefix, index);
    });
    endByteInput.addEventListener('input', function() {
      calculateByteValues(null, prefix, index);
    });
  }
  
  // Attach data type change handler for byte calculation
  const dataTypeSelect = row.querySelector(`select[name="dataType_${prefix}[]"]`);
  if (dataTypeSelect) {
    dataTypeSelect.addEventListener('change', function() {
      calculateByteValues(null, prefix, index);
    });
  }
}

function removeRow(button, type) {
  const row = button.closest('tr');
  if (!row) return;
  
  // Ask for confirmation before removing
  if (confirm('Are you sure you want to remove this row?')) {
    const tableBody = row.parentNode;
    const rowIndex = row.rowIndex;
    
    // Remove the row
    tableBody.removeChild(row);
    
    const tableId = tableBody ? tableBody.id : null;
    if (tableId) {
      // Update serial numbers
      updateSerialNumbers(tableId);
      
      // Update frame counts
      const prefix = tableId === 'additionalTableBody' ? 'cmd' : 
                    (tableId === 'responseTableBody' ? 'resp' : 'error');
      updateFrameCountsForPrefix(prefix);
    }
  }
}


function clearTable(type) {
  const tableConfigs = {
    'cmd': { tableId: 'additionalTableBody' },
    'resp': { tableId: 'responseTableBody' },
    'error': { tableId: 'errorTableBody' }
  };
  
  const config = tableConfigs[type];
  if (!config) return;
  
  // Ask for confirmation before clearing table
  if (confirm('Are you sure you want to clear the entire table? All data will be lost.')) {
    const tableBody = document.getElementById(config.tableId);
    if (!tableBody) return;
    tableBody.innerHTML = '';

    // Update frame size inputs to 0 for this prefix
    updateFrameCountsForPrefix(config.tableId === 'additionalTableBody' ? 'cmd' : 
                              (config.tableId === 'responseTableBody' ? 'resp' : 'error'));
  }
}




function updateSerialNumbers(tableBodyId) {
  const tableBody = document.getElementById(tableBodyId);
  if (!tableBody) return;
  
  const rows = tableBody.rows;
  
  // Determine prefix based on table ID
  let prefix = '';
  if (tableBodyId === 'additionalTableBody') prefix = 'cmd';
  else if (tableBodyId === 'responseTableBody') prefix = 'resp';
  else if (tableBodyId === 'errorTableBody') prefix = 'error';
  
  for (let i = 0; i < rows.length; i++) {
    const serialNumber = i + 1;
    rows[i].setAttribute('data-row', serialNumber);
    rows[i].id = `row_${prefix}_${serialNumber}`;
    
    // Update S.No cell
    if (rows[i].cells && rows[i].cells[0]) {
      rows[i].cells[0].innerHTML = serialNumber;
    }
    
    // Update oninput and onchange attributes for all inputs in this row
    updateRowAttributes(rows[i], prefix, serialNumber);
  }
}



function updateRowAttributes(row, prefix, index) {
  if (!row) return;
  
  // Update all inputs with oninput attributes
  const inputs = row.querySelectorAll('input');
  inputs.forEach(input => {
    if (input.name && input.name.includes('startBytePosition')) {
      input.setAttribute('oninput', `calculateByteValues(this, '${prefix}', ${index})`);
    }
    if (input.name && input.name.includes('endBytePosition')) {
      input.setAttribute('oninput', `calculateByteValues(this, '${prefix}', ${index})`);
    }
  });
  
  // Update all selects with onchange attributes
  const selects = row.querySelectorAll('select');
  selects.forEach(select => {
    if (select.name && select.name.includes('fieldDescription')) {
      select.setAttribute('onchange', `handleFieldDescriptionChange(this, '${prefix}', ${index})`);
    }
    if (select.name && select.name.includes('dataType')) {
      select.setAttribute('onchange', `calculateByteValues(null, '${prefix}', ${index})`);
    }
    if (select.name && select.name.includes('uiType')) {
      select.setAttribute('onchange', `applyUIAutoFill(this, '${prefix}', ${index})`);
    }
  });
  
  // Update remove button
  const removeBtn = row.querySelector('.remove-btn');
  if (removeBtn) {
    removeBtn.setAttribute('onclick', `removeRow(this, '${prefix}')`);
  }
  
  // Update add row button
  const addRowBtn = row.querySelector('.add-row-btn');
  if (addRowBtn) {
    addRowBtn.setAttribute('onclick', `addRowAtPosition(this, '${prefix}')`);
  }
}




function updateRowEventHandlers(row, prefix, index) {
  if (!row) return;
  
  // Update UI Type handler
  const uiSelect = row.querySelector(`select[name="uiType_${prefix}[]"]`);
  if (uiSelect) {
    uiSelect.setAttribute('onchange', `applyUIAutoFill(this, '${prefix}', ${index})`);
    // Remove existing event listeners and add new one
    const newUiSelect = uiSelect.cloneNode(true);
    uiSelect.parentNode.replaceChild(newUiSelect, uiSelect);
    newUiSelect.addEventListener('change', function() {
      applyUIAutoFill(this, prefix, index);
    });
  }
  
  // Update Field Description handler
  const fieldDescSelect = row.querySelector(`select[name="fieldDescription_${prefix}[]"]`);
  if (fieldDescSelect) {
    fieldDescSelect.setAttribute('onchange', `handleFieldDescriptionChange(this, '${prefix}', ${index})`);
    const newFieldDescSelect = fieldDescSelect.cloneNode(true);
    fieldDescSelect.parentNode.replaceChild(newFieldDescSelect, fieldDescSelect);
    newFieldDescSelect.addEventListener('change', function() {
      handleFieldDescriptionChange(this, prefix, index);
    });
  }
  
  // Update Byte calculation handlers
  const startByteInput = row.querySelector(`input[name="startBytePosition_${prefix}[]"]`);
  const endByteInput = row.querySelector(`input[name="endBytePosition_${prefix}[]"]`);
  
  if (startByteInput) {
    startByteInput.setAttribute('oninput', `calculateByteValues(this, '${prefix}', ${index})`);
    const newStartByteInput = startByteInput.cloneNode(true);
    startByteInput.parentNode.replaceChild(newStartByteInput, startByteInput);
    newStartByteInput.addEventListener('input', function() {
      calculateByteValues(null, prefix, index);
    });
  }
  
  if (endByteInput) {
    endByteInput.setAttribute('oninput', `calculateByteValues(this, '${prefix}', ${index})`);
    const newEndByteInput = endByteInput.cloneNode(true);
    endByteInput.parentNode.replaceChild(newEndByteInput, endByteInput);
    newEndByteInput.addEventListener('input', function() {
      calculateByteValues(null, prefix, index);
    });
  }
  
  // Update Data Type handler
  const dataTypeSelect = row.querySelector(`select[name="dataType_${prefix}[]"]`);
  if (dataTypeSelect) {
    dataTypeSelect.setAttribute('onchange', `calculateByteValues(null, '${prefix}', ${index})`);
    const newDataTypeSelect = dataTypeSelect.cloneNode(true);
    dataTypeSelect.parentNode.replaceChild(newDataTypeSelect, dataTypeSelect);
    newDataTypeSelect.addEventListener('change', function() {
      calculateByteValues(null, prefix, index);
    });
  }
  
  // Update Remove button
  const removeBtn = row.querySelector('.remove-btn');
  if (removeBtn) {
    removeBtn.setAttribute('onclick', `removeRow(this, '${prefix}')`);
  }
  
  // Update Add Row Here button
  const addRowBtn = row.querySelector('.add-row-btn');
  if (addRowBtn) {
    addRowBtn.setAttribute('onclick', `addRowAtPosition(this, '${prefix}')`);
  }
}




function updateFrameCountsForPrefix(prefix) {
  const tableId = prefix === 'cmd' ? 'additionalTableBody' : 
                 (prefix === 'resp' ? 'responseTableBody' : 'errorTableBody');
  const tableBody = document.getElementById(tableId);
  const count = tableBody ? tableBody.rows.length : 0;

  if (prefix === 'cmd') {
    const fs = document.getElementById('cmd_framesizeinbytes');
    const nr = document.getElementById('cmd_noofrows');
    if (fs) fs.value = count;
    if (nr) nr.value = count;
  } else if (prefix === 'resp') {
    const fs = document.getElementById('resp_framesizeinbytes');
    const nr = document.getElementById('resp_noofrows');
    if (fs) fs.value = count;
    if (nr) nr.value = count;
  } else if (prefix === 'error') {
    const fs = document.getElementById('error_framesizeinbytes');
    const nr = document.getElementById('error_noofrows');
    if (fs) fs.value = count;
    if (nr) nr.value = count;
  }
}

// Validation functions
function validateStep(step) {
  let isValid = false;
  
  switch(step) {
    case 1:
      isValid = validateStep1();
      break;
    case 2:
      isValid = validateStep2();
      break;
    case 3:
      isValid = validateStep3();
      break;
    case 4:
      isValid = validateStep4();
      break;
  }
  
  // Show error message if validation fails
  const errorElement = document.getElementById(`step${step}-error`);
  if (errorElement) {
    if (!isValid) {
      errorElement.textContent = 'Please fill all required fields correctly.';
      errorElement.style.display = 'block';
      // Scroll to error
      errorElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
      errorElement.style.display = 'none';
    }
  }
  
  return isValid;
}

function validateStep1() {
  let isValid = true;
  const errors = [];
  
  const paramName = document.getElementById('par_parametername');
  if (!paramName.value.trim()) {
    errors.push('Parameter Name is required');
    paramName.classList.add('error');
  } else {
    paramName.classList.remove('error');
  }
  
  const pageNames = document.getElementById('pagenames');
  if (!pageNames.value.trim()) {
    errors.push('Page Names is required');
    pageNames.classList.add('error');
  } else if (pageNames.value === "Page0") {
    errors.push('Page0 as Page name is invalid');
    pageNames.classList.add('error');
  } else {
    pageNames.classList.remove('error');
  }

  const numberOfPages = document.getElementById('numberofpages');
  if (!numberOfPages.value.trim()) {
    errors.push('Page Number is required');
    numberOfPages.classList.add('error');
  } else {
    numberOfPages.classList.remove('error');
  }

  const sequence = document.getElementById('Sequence');
  if (!sequence.value.trim()) {
    errors.push('Sequence Number is required');
    sequence.classList.add('error');
  } else {
    sequence.classList.remove('error');
  }
  
  // Show all errors
  if (errors.length > 0) {
    const errorElement = document.getElementById('step1-error');
    if (errorElement) {
      errorElement.innerHTML = errors.map(err => `<div>• ${err}</div>`).join('');
      errorElement.style.display = 'block';
    }
    isValid = false;
  }
  
  return isValid;
}

function validateStep2() {
  printCmdFrameToConsole(); 
  let isValid = true;
  const errors = [];
  
  const requiredFields = [
    { id: 'cmd_data_flow_direction', name: 'Data Flow Direction' },
    { id: 'cmd_frametype', name: 'Frame Type' },
    { id: 'cmd_framesizeinbytes', name: 'Frame size in bytes' },
    { id: 'cmd_noofrows', name: 'No of rows' }
  ];
  
  for (let field of requiredFields) {
    const element = document.getElementById(field.id);
    if (!element.value.trim()) {
      errors.push(`${field.name} is required`);
      element.classList.add('error');
    } else {
      element.classList.remove('error');
    }
  }
  
if (hasByteOverlap("additionalTableBody", "cmd")) {
        errors.push("❌ Byte positions overlap (e.g., 1-1 and 1-3 conflict).");
        forceMessage("❌ Error: Duplicate Byte Positions found in Command Frame!");
        isValid = false;
    }
 if (!validateNotificationMessages("additionalTableBody", "Command Frame")) {

    errors.push("Notification message required when Notification Enable is Yes.");

    isValid = false;
  }
if (!validateTableComplete("additionalTableBody", "Command Frame")) {
    errors.push("Please fill all required fields in Command Frame.");
    isValid = false;
}
  // Show all errors
  if (errors.length > 0) {
    const errorElement = document.getElementById('step2-error');
    if (errorElement) {
      errorElement.innerHTML = errors.map(err => `<div>• ${err}</div>`).join('');
      errorElement.style.display = 'block';
    }
    isValid = false;
  }
  
  return isValid;
}

function validateStep3() {
  let isValid = true;
  const errors = [];
  printRespFrameToConsole();
  
  const requiredFields = [
    { id: 'resp_data_flow_direction', name: 'Data Flow Direction' },
    { id: 'resp_frametype', name: 'Frame Type' },
    { id: 'resp_framesizeinbytes', name: 'Frame size in bytes' },
    { id: 'resp_noofrows', name: 'No of rows' }
  ];
  
  for (let field of requiredFields) {
    const element = document.getElementById(field.id);
    if (!element.value.trim()) {
      errors.push(`${field.name} is required`);
      element.classList.add('error');
    } else {
      element.classList.remove('error');
    }
  }
  if (hasByteOverlap("responseTableBody", "resp")) {
        errors.push("❌ Byte positions overlap in Response Frame.");
        forceMessage("❌ Error: Duplicate Byte Positions found in Response!");
        isValid = false;
    } 
if (!validateNotificationMessages("responseTableBody", "Response Frame")) {

  errors.push("Notification message required when Notification Enable is Yes.");

  isValid = false;

}
if (!validateTableComplete("responseTableBody", "Response Frame")) {
    errors.push("Please fill all required fields in Response Frame.");
    isValid = false;
}

  // Show all errors
  if (errors.length > 0) {
    const errorElement = document.getElementById('step3-error');
    if (errorElement) {
      errorElement.innerHTML = errors.map(err => `<div>• ${err}</div>`).join('');
      errorElement.style.display = 'block';
    }
    isValid = false;
  }
  
  return isValid;
}

function validateStep4() {
  let isValid = true;
  const errors = [];
  
  const requiredFields = [
    { id: 'error_data_flow_direction', name: 'Data Flow Direction' },
    { id: 'error_frametype', name: 'Frame Type' },
    { id: 'error_framesizeinbytes', name: 'Frame size in bytes' },
    { id: 'error_noofrows', name: 'No of rows' }
  ];
  
  for (let field of requiredFields) {
    const element = document.getElementById(field.id);
    if (!element.value.trim()) {
      errors.push(`${field.name} is required`);
      element.classList.add('error');
    } else {
      element.classList.remove('error');
    }
  }
 
if (hasByteOverlap("errorTableBody", "error")) {
        errors.push("❌ Byte positions overlap in Error Frame.");
        forceMessage("❌ Error: Duplicate Byte Positions found in Error Frame!");
        isValid = false;
    }  
// ADD THIS BLOCK inside validateStep4()

if (!validateNotificationMessages("errorTableBody", "Error Frame")) {

  errors.push("Notification message required when Notification Enable is Yes.");

  isValid = false;

}
if (!validateTableComplete("errorTableBody", "Error Frame")) {
    errors.push("Please fill all required fields in Error Frame.");
    isValid = false;
}
  // Show all errors
  if (errors.length > 0) {
    const errorElement = document.getElementById('step4-error');
    if (errorElement) {
      errorElement.innerHTML = errors.map(err => `<div>• ${err}</div>`).join('');
      errorElement.style.display = 'block';
    }
    isValid = false;
  }
  
  return isValid;
}


// Parameter Name Synchronization
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
  });

  // Also sync on page load (if main input already has value)
  const initialValue = mainInput.value;
  if (initialValue) {
    targetIDs.forEach(id => {
      const input = document.getElementById(id);
      if (input) input.value = initialValue;
    });
  }
}

// Convenience function to allow direct call from oninput attribute
function syncParameterNameToAll(value) {
  const targetIDs = ["com_parametername", "resp_parametername", "error_parametername"];
  targetIDs.forEach(id => {
    const input = document.getElementById(id);
    if (input) input.value = value;
  });
}

// Existing functions from your code
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

//
// State to track which sub-frame we are on for each step
function changeCommandPage(pageNumber) {
    // 1. Hide EVERY div that has the class "form-step"
    document.querySelectorAll('.form-step').forEach(step => {
        step.style.display = 'none';
    });

    // 2. Show only the specific Command Frame page we want
    const targetPage = document.getElementById('step-2-page-' + pageNumber);
    if (targetPage) {
        targetPage.style.display = 'block';
        window.scrollTo(0, 0);
    }
}

// 



let currentCmdFrame = 0;
let totalCmdFrames = 1;

function generateCommandFrames() {
    const count = parseInt(document.getElementById("numberofpages").value) || 1;
    totalCmdFrames = count;

    const container = document.getElementById("commandFrameContainer");
    container.innerHTML = "";

    const template = document.getElementById("commandFrameTemplate");

    for (let i = 0; i < count; i++) {
        const clone = template.content.cloneNode(true);
        const page = clone.querySelector(".command-frame-page");
        page.style.display = i === 0 ? "block" : "none";
        page.setAttribute("data-index", i);
        container.appendChild(clone);
    }

    currentCmdFrame = 0;
    updateCmdIndicator();
}
function showCommandFrame(index) {
    const pages = document.querySelectorAll(".command-frame-page");
    pages.forEach((p, i) => {
        p.style.display = i === index ? "block" : "none";
    });
    currentCmdFrame = index;
    updateCmdIndicator();
}

const commandFrameData = [];
function nextCommandFrame() {

    if (!validateStep(2)) return;
    if (!validateNotificationMessages("additionalTableBody", "Command Frame")) return;
    if (!validateTableComplete("additionalTableBody", "Command Frame")) return;

    // 1. Save current frame
    commandFrameData[currentCmdFrame] = readMainTable();

    if (currentCmdFrame < totalCmdFrames - 1) {

        // 2. Move index
        currentCmdFrame++;

        // 3. Show new frame UI
        showCommandFrame(currentCmdFrame);

        // 4. Restore safely AFTER UI ready
        setTimeout(() => {

            if (commandFrameData[currentCmdFrame]) {

                writeMainTable(commandFrameData[currentCmdFrame]);

            } else {

                resetMainTableUI();

            }

        }, 0);

    } else {

        forceMessage("This is the last Command Frame.");

    }

}

function prevCommandFrame() {

    // 1. Save current frame data
    commandFrameData[currentCmdFrame] = readMainTable();

    if (currentCmdFrame > 0) {

        // 2. Move index first
        currentCmdFrame--;

        // 3. Show frame UI
        showCommandFrame(currentCmdFrame);

        // 4. IMPORTANT: restore AFTER showCommandFrame
        setTimeout(() => {

            if (commandFrameData[currentCmdFrame]) {

                writeMainTable(commandFrameData[currentCmdFrame]);

            } else {

                resetMainTableUI();

            }

        }, 0);

    } else {

        forceMessage("This is the first Command Frame.");

    }

}

function writeMainTable(data) {

    const tableBody = document.getElementById("additionalTableBody");

    tableBody.innerHTML = "";

    data.forEach(rowData => {

        addRow("cmd");

        const row = tableBody.lastElementChild;

        row.querySelectorAll("input, select").forEach(el => {

            if (rowData.hasOwnProperty(el.name)) {

                el.value = rowData[el.name];

            }

        });

        row.bitwiseData = rowData.bitwiseData || null;

       if (row.bitwiseData) {
            applyBitwiseToMainRow(row, row.bitwiseData);
        }


    });
}
function readMainTable() {

    const tableBody = document.getElementById("additionalTableBody");

    if (!tableBody) return [];

    const rows = tableBody.querySelectorAll("tr");

    const tableData = [];

    rows.forEach(row => {

        const rowObj = {};

        row.querySelectorAll("input, select").forEach(el => {
            rowObj[el.name] = el.value;
        });

        rowObj.bitwiseData = row.bitwiseData || null;

        tableData.push(rowObj);

    });

    return tableData;
}

function resetMainTableUI() {

    const tableBody = document.getElementById("additionalTableBody");

    if (!tableBody) return;

    // Clear everything
    tableBody.innerHTML = "";

    // DO NOT add default row
    // leave table empty

}
function updateCmdIndicator() {
    const title = document.getElementById("cmdFrameTitle");
    title.innerText = `COMMAND FRAME (${currentCmdFrame + 1})`;

    document.getElementById("cmdFrameIndicator").innerText =
        `Command Frame ${currentCmdFrame + 1} of ${totalCmdFrames}`;
}

//
//
let currentRespFrame = 0;

function generateResponseFrames() {
    const count = parseInt(document.getElementById("page").value) || 1;
    const container = document.getElementById("responseFramesContainer");

    container.innerHTML = ""; // clear old

    for (let i = 0; i < count; i++) {
        const div = document.createElement("div");
        div.className = "response-frame-page";
        div.style.display = i === 0 ? "block" : "none";

        div.innerHTML = `
            <h2 id="respFrameTitle">RESPONSE FRAME (${i + 1})</h2>
            <!-- FULL RESPONSE TABLE HTML HERE -->
        `;

        container.appendChild(div);
    }

    currentRespFrame = 0;
    showResponseFrame(0);

}
function showResponseFrame(index) {
    const pages = document.querySelectorAll(".response-frame-page");
    pages.forEach((p, i) => {
        p.style.display = i === index ? "block" : "none";
    });
    currentRespFrame = index;
    updateRespIndicator();
   loadRespViewTable();
}

const responseFrameData = []; // Stores state for each Response page

function nextResponseFrame() {
    console.log("NEXT RESPONSE BUTTON CLICKED");
         
    if (!validateStep(3)) return;
    if (!validateNotificationMessages("responseTableBody", "Response Frame")) return;
    if (!validateTableComplete("responseTableBody", "Response Frame")) return;

    // --- FIX HERE: Use the universal reader ---
    responseFrameData[currentRespFrame] = readTableById("#responseTableBody");

    const pages = document.querySelectorAll(".response-frame-page");

    if (currentRespFrame < pages.length - 1) {
        currentRespFrame++;
        showResponseFrame(currentRespFrame);

        if (responseFrameData[currentRespFrame]) {
            // Use your existing loader/writer
            writeResponseTable(responseFrameData[currentRespFrame]);
        } else {
            resetResponseTableUI();
        }
    }
}

function prevResponseFrame() {
    // --- FIX HERE: Use the universal reader ---
    responseFrameData[currentRespFrame] = readTableById("#responseTableBody");

    if (currentRespFrame > 0) {
        currentRespFrame--;
        showResponseFrame(currentRespFrame);
        writeResponseTable(responseFrameData[currentRespFrame]);
    }
}

function readResponseTable() {
    const tableBody = document.getElementById("responseTableBody");

    if (!tableBody) {
        console.error("responseTableBody not found");
        return [];
    }

    const rows = tableBody.querySelectorAll("tr");
    const tableData = [];

    rows.forEach(row => {

        const rowObj = {};

        // ✅ Save using element name (NOT index)
        row.querySelectorAll("input, select").forEach(el => {
            rowObj[el.name] = el.value;
        });

        rowObj.bitwiseData = row.bitwiseData || null;

        tableData.push(rowObj);
    });

    return tableData;
}
function writeResponseTable(data) {

    if (!data) return;

    const tableBody = document.getElementById("responseTableBody");
    if (!tableBody) return;

    tableBody.innerHTML = "";

    data.forEach((rowData, r) => {

        addRow("resp");

        const row = tableBody.querySelectorAll("tr")[r];
        if (!row) return;

        // ✅ Restore using element name
        row.querySelectorAll("input, select").forEach(el => {

            if (rowData.hasOwnProperty(el.name)) {

                el.value = rowData[el.name];

            }

        });

        // restore bitwise safely
        if (rowData.bitwiseData) {

            row.bitwiseData = rowData.bitwiseData;

            applyBitwiseToResponseRow(row, rowData.bitwiseData);

        }
        


    });
}
function resetResponseTableUI() {
    const tableBody = document.getElementById("responseTableBody");
    if (tableBody) {
        tableBody.innerHTML = "";
    }
}

function updateRespIndicator() {
    const pages = document.querySelectorAll(".response-frame-page");
    document.getElementById("respFrameIndicator").innerText =
        `Response Frame ${currentRespFrame + 1} of ${pages.length}`;
}
document.addEventListener("DOMContentLoaded", function () {
    generateResponseFrames();
});
//
let currentErrorFrame = 0;
let totalErrorFrames = 1;
const errorFrameData = [];

/* GENERATE ERROR FRAMES */
function generateErrorFrames() {
    const count = parseInt(document.getElementById("numberofpages").value) || 1;
    totalErrorFrames = count;

    const container = document.getElementById("errorFrameContainer"); // Matches your HTML
    container.innerHTML = "";

    const template = document.getElementById("errorFrameTemplate");

    for (let i = 0; i < count; i++) {
        const clone = template.content.cloneNode(true);
        const page = clone.querySelector(".error-frame-page");
        
        page.style.display = i === 0 ? "block" : "none";
        page.setAttribute("data-index", i);
        
        container.appendChild(clone);
    }

    currentErrorFrame = 0;
    updateErrorIndicator();
}

/* NAVIGATION: NEXT */
function nextErrorFrame() {
    if (!validateStep(4)) return; 
    if (!validateNotificationMessages("errorTableBody", "Error Frame")) return;
    if (!validateTableComplete("errorTableBody", "Error Frame")) return;

    // CHANGE: Save using the ID-based reader to keep name attributes
    errorFrameData[currentErrorFrame] = readTableById("#errorTableBody");
    
    if (currentErrorFrame < totalErrorFrames - 1) {
        currentErrorFrame++;
        showErrorFrame(currentErrorFrame);

        if (errorFrameData[currentErrorFrame]) {
            // Note: Make sure writeErrorTable can handle the new object format
            writeErrorTable(errorFrameData[currentErrorFrame]);
        } else {
            resetErrorTableUI();
        }
    } else {
        forceMessage("This is the last Error Frame.");
    }
}

function prevErrorFrame() {
    // CHANGE: Save using the ID-based reader
    errorFrameData[currentErrorFrame] = readTableById("#errorTableBody");

    if (currentErrorFrame > 0) {
        currentErrorFrame--;
        showErrorFrame(currentErrorFrame);
        writeErrorTable(errorFrameData[currentErrorFrame]);
    }
}

/* SHOW FRAME */
function showErrorFrame(index) {
    const pages = document.querySelectorAll(".error-frame-page");
    pages.forEach((p, i) => {
        p.style.display = i === index ? "block" : "none";
    });
    currentErrorFrame = index;
    updateErrorIndicator();
}

/* READ TABLE */
function readErrorTable() {

    const tableBody = document.getElementById("errorTableBody");

    if (!tableBody) {
        console.error("errorTableBody not found");
        return [];
    }

    const rows = tableBody.querySelectorAll("tr");
    const tableData = [];

    rows.forEach(row => {

        const rowObj = {};

        // ✅ Save using name attribute (correct method)
        row.querySelectorAll("input, select").forEach(el => {
            rowObj[el.name] = el.value;
        });

        // Save bitwise
        rowObj.bitwiseData = row.bitwiseData || null;

        tableData.push(rowObj);

    });

    return tableData;
}

/* WRITE TABLE */
function writeErrorTable(data) {

    if (!data) return;

    const tableBody = document.getElementById("errorTableBody");
    if (!tableBody) return;

    tableBody.innerHTML = "";

    data.forEach((rowData, r) => {

        addRow("error");

        const row = tableBody.querySelectorAll("tr")[r];

        if (!row) return;

        // ✅ Restore using name attribute
        row.querySelectorAll("input, select").forEach(el => {

            if (rowData.hasOwnProperty(el.name)) {

                el.value = rowData[el.name];

            }

        });

        // Restore bitwise safely
        if (rowData.bitwiseData) {

            row.bitwiseData = rowData.bitwiseData;

        }
 

    });
}
/* RESET UI */
function resetErrorTableUI() {
    const tableBody = document.getElementById("errorTableBody");
    if (tableBody) tableBody.innerHTML = "";
}

/* INDICATOR */
function updateErrorIndicator() {
    const indicator = document.getElementById("errorFrameIndicator");
    if (indicator) {
        indicator.innerText = `Error Frame ${currentErrorFrame + 1} of ${totalErrorFrames}`;
    }
}
//

function handleFieldDescriptionChange(selectEl) {
    const row = selectEl.closest('tr');
    const modeSelect = row.querySelector('.bit-byte-select');
    const arrowBtn = row.querySelector('.arrow-btn');

    if (selectEl.value === 'Data') {   // ✅ THIS
        modeSelect.style.display = 'inline-block';
    } else {
        modeSelect.style.display = 'none';
        arrowBtn.style.display = 'none';
        modeSelect.value = "";
    }
}


function handleBitByteChange(selectEl) {
    const row = selectEl.closest('tr');
    const arrowBtn = row.querySelector('.arrow-btn');

    if (selectEl.value === 'Bitwise') {
        arrowBtn.style.display = 'inline-block';
    } else {
        arrowBtn.style.display = 'none';
    }
}

// Define the limit for rows
const MAX_ROWS = 8;

function openBitwiseConfig(rowElement) {
    window.currentBitwiseMainRow = rowElement;
    const modal = document.getElementById("bitwiseConfigModal");
    const tbody = document.getElementById("bitwiseTableBody");
    tbody.innerHTML = "";

    // Identify which row was clicked
    const rowAttr = rowElement.getAttribute("data-row");
    const cmdIndex = rowAttr ? parseInt(rowAttr) - 1 : 0;

    // 1️⃣ Get saved data if it exists
    const savedBits = rowElement.bitwiseData ?? [];

    // 2️⃣ --- ADD THE FALLBACK LOGIC HERE ---
    let cmdData = (typeof cmdViewData !== 'undefined') ? cmdViewData[cmdIndex] : null;

    // If specific row data (like Row 2) is missing, use Row 1 (Index 0) as the template
    if (!cmdData && typeof cmdViewData !== 'undefined' && cmdViewData.length > 0) {
        console.log("Specific row data missing, falling back to global template (Row 1)");
        cmdData = cmdViewData[0]; 
    }
    // ---------------------------------------

    // 3️⃣ Set total rows to display
    const rowLimit = (typeof MAX_ROWS !== 'undefined') ? MAX_ROWS : 5;
    const totalRows = Math.max(rowLimit, savedBits.length);

    for (let i = 0; i < totalRows; i++) {
        const tr = addBitwiseRow();
        if (!tr) continue;

        const savedBit = savedBits[i] ?? {};
        
        // Pick the bit template from cmdData (the fallback we just set)
        const bitTemplate = cmdData?.bits?.[i] ?? cmdData?.bits?.[0] ?? {};



            // 5️⃣ MAPPING (Ensuring every possible naming convention is caught)
            const bit = {
                startBit:     savedBit.startBit     ?? bitTemplate.startBit ?? "",
                endBit:       savedBit.endBit       ?? bitTemplate.endBit ?? "",
                noOfBits:     savedBit.noOfBits     ?? bitTemplate.bitSize ?? bitTemplate.noOfBits ?? "",
                dataType:     savedBit.dataType     ?? bitTemplate.dataType ?? "",
                valueDef:     savedBit.valueDef     ?? bitTemplate.valueDefinition ?? bitTemplate.valueDef ?? "",
                displayMin:   savedBit.displayMin   ?? bitTemplate.displayValueMin ?? bitTemplate.displayMin ?? "",
                displayMax:   savedBit.displayMax   ?? bitTemplate.displayValueMax ?? bitTemplate.displayMax ?? "",
                hexMin:       savedBit.hexMin       ?? bitTemplate.valueInHexMin ?? bitTemplate.hexMin ?? "",
                hexMax:       savedBit.hexMax       ?? bitTemplate.valueInHexMax ?? bitTemplate.hexMax ?? "",
                uiType:       savedBit.uiType       ?? bitTemplate.uiType ?? "",
                uiAsPer:      savedBit.uiAsPer      ?? bitTemplate.asperui ?? bitTemplate.uiAsPer ?? "",
                uiSerial:     savedBit.uiSerial     ?? bitTemplate.uiSerialNo ?? bitTemplate.uiSerial ?? "",
                uiId:         savedBit.uiId         ?? bitTemplate.uiId ?? "",
                uiValues:     savedBit.uiValues     ?? bitTemplate.uiValues ?? "",
                uiLength:     savedBit.uiLength     ?? bitTemplate.uiPageNo ?? bitTemplate.uiLength ?? "",
                uiInout:      savedBit.uiInout      ?? bitTemplate.uiDescriptionInOut ?? bitTemplate.uiInout ?? "",
                priority:     savedBit.priority     ?? bitTemplate.sliderStepValue ?? bitTemplate.priority ?? "",
                unit:         savedBit.unit         ?? bitTemplate.defaultvalue ?? bitTemplate.unit ?? "",
                description:  savedBit.description  ?? bitTemplate.developerNotes ?? bitTemplate.description ?? "",
                notifEnable:  savedBit.notifEnable  ?? bitTemplate.notificationEnable ?? "No",
                notifMessage: savedBit.notifMessage ?? bitTemplate.notificationMessage ?? ""
            };

            // 6️⃣ Populate inputs
            const fieldMap = {
                ".start-bit": bit.startBit,
                ".end-bit": bit.endBit,
                ".no-of-bits": bit.noOfBits,
                ".data-type": bit.dataType,
                ".display-min": bit.displayMin,
                ".display-max": bit.displayMax,
                ".hex-min": bit.hexMin,
                ".hex-max": bit.hexMax,
                ".ui-type": bit.uiType,
                ".ui-as-per": bit.uiAsPer,
                ".ui-serial": bit.uiSerial,
                ".ui-id": bit.uiId,
                ".ui-values": bit.uiValues,
                ".ui-length": bit.uiLength,
                ".ui-inout": bit.uiInout,
                ".priority": bit.priority,
                ".unit": bit.unit,
                ".description": bit.description,
                ".notif-enable": bit.notifEnable,
                ".notif-message": bit.notifMessage
            };

            Object.entries(fieldMap).forEach(([selector, value]) => {
                const el = tr.querySelector(selector);
                if (el) el.value = value;
            });

            const valDefEl = tr.querySelector(".value-def");
            if (valDefEl) {
                const map = { "Fixed Value": "Fixed", "Range Value": "Range", "Dynamic Value": "Dynamic", "Enum Value": "Enum", "Calculated Value": "Calculated", "None": "None" };
                valDefEl.value = map[bit.valueDef] ?? bit.valueDef;
            }
        }

        modal.style.display = "block";

    }
function addBitwiseRow() {
  const tbody = document.getElementById('bitwiseTableBody');

  if (tbody.rows.length >= MAX_ROWS) {
    alert("Maximum 8 rows allowed");
    return;
  }

  const tr = document.createElement('tr');

 tr.innerHTML = `
  <td class="col-serial"></td>

  <!-- Bit Range -->
  <td><input type="number" class="bitwise-input start-bit"></td>
  <td><input type="number" class="bitwise-input end-bit"></td>
  <td><input type="number" class="bitwise-input no-of-bits" readonly></td>

  <!-- Data Type -->
  <td>
    <select class="bitwise-select data-type">
      <option value="">Select</option>
      <option value="UnsignedInt">Unsigned Integer</option>
      <option value="SignedInt">Signed Integer</option>
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

  <!-- Value Definition -->
  <td>
    <select class="bitwise-select value-def">
      <option value="">Select</option>
      <option value="Fixed">Fixed Value</option>
      <option value="Range">Range Value</option>
      <option value="Dynamic">Dynamic Value</option>
      <option value="Enum">Enum Value</option>
      <option value="Calculated">Calculated Value</option>
      <option value="None">None</option>
    </select>
  </td>

  <!-- Display Range -->
  <td><input type="number" class="bitwise-input display-min" readonly></td>
  <td><input type="number" class="bitwise-input display-max" readonly></td>

  <!-- Hex Range -->
  <td><input type="text" class="bitwise-input hex-min"></td>
  <td><input type="text" class="bitwise-input hex-max"></td>

  <!-- UI Type -->
  <td>
    <select class="bitwise-select ui-type">
      <option value="">Select</option>
      <option value="Text">Text</option>
      <option value="Toggle Switch">Toggle Switch</option>
      <option value="Checkbox">Checkbox</option>
      <option value="Text Trigger">Text Trigger</option>
      <option value="Status LED">Status LED</option>
      <option value="Lock Icon">Lock Icon</option>
      <option value="Alaram Icon">Alaram Icon</option>
      <option value="Eye Icon">Eye Icon</option>
      <option value="Network Icon">Network Icon</option>
      <option value="Power Button">Power Button</option>
    </select>
  </td>

  <!-- UI Mapping -->
  <td><input type="text" class="bitwise-input ui-as-per"></td>
  <td><input type="text" class="bitwise-input ui-serial"></td>
  <td><input type="number" class="bitwise-input ui-id" readonly></td>
  <td><input type="text" class="bitwise-input ui-values"></td>
  <td><input type="number" class="bitwise-input ui-length"></td>

  <!-- In/Out -->
  <td>
    <select class="bitwise-select ui-inout">
      <option value="">Select</option>
      <option value="Read">Read</option>
      <option value="Write">Write</option>
      <option value="Read/Write">Read/Write</option>
    </select>
  </td>

  <!-- Meta -->
    <td><input type="number" class="bitwise-input priority"></td>
  <td><input type="text" class="bitwise-input unit"></td>
  <td><input type="text" class="bitwise-input description"></td>

  <!-- Notification -->
  <td>
    <select class="bitwise-select notif-enable">
      <option value="No">No</option>
      <option value="Yes">Yes</option>
    </select>
  </td>
  <td><input type="text" class="bitwise-input notif-message"></td>

  <!-- Actions -->
  <td class="bitwise-actions">
    <button type="button" onclick="addBitwiseRow()">+</button>
    <button type="button" onclick="removeBitwiseRow(this)">−</button>
  </td>
`;

  tbody.appendChild(tr);
 attachBitwiseLogic(tr); 
  refreshSerialNumbers();

   return tr;

}
function removeBitwiseRow(btn) {
  const tbody = document.getElementById('bitwiseTableBody');

  if (tbody.rows.length <= 1) {
    alert('At least one row is required.');
    return;
  }

  btn.closest('tr').remove();
  refreshSerialNumbers();
}



 
function saveBitwiseConfig(e) {

    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    const isBitwiseValid = validateBitwiseOverlap();

    if (!isBitwiseValid) {
        console.warn("Bitwise configuration contains overlaps. Save aborted.");
        return;
    }

    console.log("Bitwise Config is valid. Saving...");

    const rows = document.querySelectorAll('#bitwiseTableBody tr');
    const bitwiseData = [];

    rows.forEach(row => {

        const rowData = {

            startBit: row.querySelector('.start-bit')?.value || "",
            endBit: row.querySelector('.end-bit')?.value || "",
            noOfBits: row.querySelector('.no-of-bits')?.value || "",
            dataType: row.querySelector('.data-type')?.value || "",
            valueDef: row.querySelector('.value-def')?.value || "",
            displayMin: row.querySelector('.display-min')?.value || "",
            displayMax: row.querySelector('.display-max')?.value || "",
            hexMin: row.querySelector('.hex-min')?.value || "",
            hexMax: row.querySelector('.hex-max')?.value || "",
            uiType: row.querySelector('.ui-type')?.value || "",
            uiAsPer: row.querySelector('.ui-as-per')?.value || "",
            uiSerial: row.querySelector('.ui-serial')?.value || "",
            uiId: row.querySelector('.ui-id')?.value || "",
            uiValues: row.querySelector('.ui-values')?.value || "",
            uiLength: row.querySelector('.ui-length')?.value || "",
            uiInout: row.querySelector('.ui-inout')?.value || "",
            priority: row.querySelector('.priority')?.value || "",
            unit: row.querySelector('.unit')?.value || "",
            description: row.querySelector('.description')?.value || "",
            notifEnable: row.querySelector('.notif-enable')?.value || "No",
            notifMessage: row.querySelector('.notif-message')?.value || ""

        };

        if (rowData.startBit !== "" || rowData.endBit !== "") {
            bitwiseData.push(rowData);
        }

    });

    if (window.currentBitwiseMainRow) {
        // 1. Attach the data object to the row for storage
        window.currentBitwiseMainRow.bitwiseData = bitwiseData;
        console.log("Bitwise attached to main row:", bitwiseData);

        // 2. Identify which Table Body the row belongs to
        const isResponseRow = window.currentBitwiseMainRow.closest('#responseTableBody');
        const isErrorRow    = window.currentBitwiseMainRow.closest('#errorTableBody');

        if (isResponseRow) {
            console.log("Applying logic for RESPONSE frame");
            applyBitwiseToResponseRow(window.currentBitwiseMainRow, bitwiseData);
            printRespFrameToConsole();
        } 
        else if (isErrorRow) {
            console.log("Applying logic for ERROR frame");
            // ✅ Updated to call your function: applyBitwiseToErrorMainRow
            applyBitwiseToErrorMainRow(window.currentBitwiseMainRow, bitwiseData);
            // ✅ Updated to match your console printer name
            printErrorFrameToConsole();
        } 
        else {
            console.log("Applying logic for COMMAND frame");
            applyBitwiseToMainRow(window.currentBitwiseMainRow, bitwiseData);
            printCmdFrameToConsole();
        }
    }

    // Close modal
    document.getElementById("bitwiseConfigModal").style.display = "none";
}
 

 
    function closeBitwiseConfig(e) {
  if (e) e.preventDefault();
  if (e) e.stopPropagation();

  document.getElementById("bitwiseConfigModal").style.display = "none";
}
//
function loadBitwiseFromCmdView(cmdIndex = 0) {
        refreshSerialNumbers();


    const bits = cmdViewData[cmdIndex]?.bits || [];

    const rows = document.querySelectorAll('#bitwiseTableBody tr');

    rows.forEach((row, i) => {

        const bit = bits[i];
        if (!bit) return;

        row.querySelector('[name="startBit_cmd[]"]').value = bit.startBit ?? '';
        row.querySelector('[name="endBit_cmd[]"]').value = bit.endBit ?? '';
        row.querySelector('[name="noOfBits_cmd[]"]').value = bit.bitSize ?? '';

        row.querySelector('[name="dataType_cmd[]"]').value = bit.dataType ?? '';
        row.querySelector('[name="valueDefinition_cmd[]"]').value = bit.valueDefinition ?? '';

        row.querySelector('[name="displayValueMin_cmd[]"]').value = bit.displayValueMin ?? '';
        row.querySelector('[name="displayValueMax_cmd[]"]').value = bit.displayValueMax ?? '';

        row.querySelector('[name="valueInHexMin_cmd[]"]').value = bit.valueInHexMin ?? '';
        row.querySelector('[name="valueInHexMax_cmd[]"]').value = bit.valueInHexMax ?? '';

        row.querySelector('[name="uiType_cmd[]"]').value = bit.uiType ?? '';
        row.querySelector('[name="uiId_cmd[]"]').value = bit.uiId ?? '';
        row.querySelector('[name="cmd_sno_asperui[]"]').value = bit.uiSerialNo ?? '';
        row.querySelector('[name="cmd_asperuivalue[]"]').value = bit.uiValues ?? '';
        row.querySelector('[name="cmd_asperui[]"]').value = bit.asperui ?? '';

        row.querySelector('[name="uiPageNo_cmd[]"]').value = bit.uiPageNo ?? '';
        row.querySelector('[name="uiDescriptionInOut_cmd[]"]').value = bit.uiDescriptionInOut ?? '';
        row.querySelector('[name="sliderStepValue_cmd[]"]').value = bit.sliderStepValue ?? '';
        row.querySelector('[name="defaultvalue_cmd[]"]').value = bit.defaultvalue ?? '';
        row.querySelector('[name="developerNotes_cmd[]"]').value = bit.developerNotes ?? '';

        row.querySelector('[name="notifEnable_cmd[]"]').value = bit.notificationEnable ?? '';
        row.querySelector('[name="notifMsg_cmd[]"]').value = bit.notificationMessage ?? '';
    });
}





function attachBitwiseLogic(row) {

  const uiTypeSelect    = row.querySelector('.ui-type');
  const uiAsPerInput    = row.querySelector('.ui-as-per');

  const uiSerialInput   = row.querySelector('.ui-serial'); // USER FIELD (do not touch)
  const uiIdInput       = row.querySelector('.ui-id');     // SYSTEM FIELD ONLY

  const uiValuesInput   = row.querySelector('.ui-values');

  const valueDefSelect  = row.querySelector('.value-def');
  const dataTypeSelect  = row.querySelector('.data-type');
  const uiInOutSelect   = row.querySelector('.ui-inout');


    
  /* ---------------- UI ID (SYSTEM ONLY) ---------------- */

  

  /* ---------------- UI TYPE AUTO FILL ---------------- */

  if (uiTypeSelect) {

    uiTypeSelect.addEventListener('change', function () {

      const typeLower = (this.value || '').toLowerCase();

      const autoFillList = [
        'toggle switch','checkbox','power button','text trigger',
        'lock icon','alaram icon','eye icon','network icon','status led','text',
      ];

      if (autoFillList.includes(typeLower)) {

        if (valueDefSelect) valueDefSelect.value = "Fixed";
        if (dataTypeSelect) dataTypeSelect.value = "UnsignedInt";
         if (uiValuesInput) uiValuesInput.value = "1";

         // Display min/max
      const displayMinInput = row.querySelector('.display-min');
      const displayMaxInput = row.querySelector('.display-max');

      if (displayMinInput) displayMinInput.value = "0";
      if (displayMaxInput) displayMaxInput.value = "1";

      // Hex min/max
      const hexMinInput = row.querySelector('.hex-min');
      const hexMaxInput = row.querySelector('.hex-max');

      if (hexMinInput) hexMinInput.value = "0";
      if (hexMaxInput) hexMaxInput.value = "1";

        // IMPORTANT:
        // uiSerialInput is NOT used here at all
  

        if (uiInOutSelect) {
          uiInOutSelect.value =
            (typeLower === 'status led' || typeLower === 'network icon')
              ? "Read"
              : "Read/Write";
      }
}
  });
} 

if (dataTypeSelect) {
  dataTypeSelect.addEventListener('change', function () {

    const displayMinInput = row.querySelector('.display-min');
    const displayMaxInput = row.querySelector('.display-max');
    const hexMinInput     = row.querySelector('.hex-min');
    const hexMaxInput     = row.querySelector('.hex-max');

    if (displayMinInput) displayMinInput.value = "0";
    if (displayMaxInput) displayMaxInput.value = "1";
    if (hexMinInput)     hexMinInput.value = "0";
    if (hexMaxInput)     hexMaxInput.value = "1";
  });
}
   
  /* ---------------- BIT CALCULATION ---------------- */

  const startBit = row.querySelector('.start-bit');
  const endBit   = row.querySelector('.end-bit');
  const noOfBits = row.querySelector('.no-of-bits');

  if (startBit && endBit && noOfBits) {

    const recalc = () => {

      const s = parseInt(startBit.value, 10);
      const e = parseInt(endBit.value, 10);

      if (!isNaN(s) && !isNaN(e)) {
        noOfBits.value = (e >= s) ? (e - s) + 1 : 0;
      } else {
        noOfBits.value = '';
      }
    };

    startBit.addEventListener('input', recalc);
    endBit.addEventListener('input', recalc);
  }

}

// This function fixes the numbers when rows are added or deleted
function refreshSerialNumbers() {
  const tbody = document.getElementById('bitwiseTableBody');

  [...tbody.rows].forEach((row, index) => {
    // S.No still increments so you can track the rows (1, 2, 3...)
    row.cells[0].textContent = index + 1;

    // UI ID (12th column)
    const uiIdInput = row.cells[11].querySelector('input');

    if (uiIdInput) {
      // Change this from (index + 1) to 1
      uiIdInput.value = 1; 
    }
  });
}


//
function validateByteRangeOverlap(tableBodyId, prefix, errorId) {
    const rows = document.querySelectorAll(`#${tableBodyId} tr`);
    const usedBytes = {}; // Stores { byteNumber: rowIndex }
    let hasOverlap = false;

    // 1. Reset Styles
    rows.forEach(row => {
        const startInput = row.querySelector(`input[name^="startBytePosition_${prefix}_"]`);
        const endInput = row.querySelector(`input[name^="endBytePosition_${prefix}_"]`);
        if (startInput) {
            startInput.style.border = "1px solid #ccc";
            startInput.style.backgroundColor = "";
        }
        if (endInput) {
            endInput.style.border = "1px solid #ccc";
            endInput.style.backgroundColor = "";
        }
    });

    // 2. Check for Overlaps
    for (let i = 0; i < rows.length; i++) {
        const startInput = rows[i].querySelector(`input[name^="startBytePosition_${prefix}_"]`);
        const endInput = rows[i].querySelector(`input[name^="endBytePosition_${prefix}_"]`);

        if (!startInput || !endInput) continue;

        const s = parseInt(startInput.value, 10);
        const e = parseInt(endInput.value, 10);

        if (isNaN(s) || isNaN(e)) continue;

        // Validation: Start cannot be greater than End
        if (s > e) {
            startInput.style.border = "2px solid red";
            forceMessage(`❌ Error in Row ${i+1}: Start Byte cannot be greater than End Byte.`);
            return false;
        }

        // Check every byte in this range
        for (let b = s; b <= e; b++) {
            if (usedBytes[b] !== undefined) {
                hasOverlap = true;

                // Highlight Current Row
                startInput.style.border = "2px solid red";
                startInput.style.backgroundColor = "#ffe6e6";
                endInput.style.border = "2px solid red";
                endInput.style.backgroundColor = "#ffe6e6";

                // Highlight Previous Conflicting Row
                const prevRow = rows[usedBytes[b]];
                const pStart = prevRow.querySelector(`input[name^="startBytePosition_${prefix}_"]`);
                const pEnd = prevRow.querySelector(`input[name^="endBytePosition_${prefix}_"]`);
                if (pStart) { pStart.style.border = "2px solid red"; pStart.style.backgroundColor = "#ffe6e6"; }
                if (pEnd) { pEnd.style.border = "2px solid red"; pEnd.style.backgroundColor = "#ffe6e6"; }
            } else {
                usedBits[b] = i; // Map byte to this row index
            }
        }
    }

    if (hasOverlap) {
        const msg = "❌ Byte Range Error: Byte positions cannot be repeated or overlap with other rows.";
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
//
function hasByteOverlap(tableBodyId, prefix) {
    const rows = document.querySelectorAll(`#${tableBodyId} tr`);
    const usedBytes = new Set();
    let overlapFound = false;

    rows.forEach((row) => {
        // Matches your exact HTML: startBytePosition_cmd[], startBytePosition_resp[], etc.
        const startInput = row.querySelector(`input[name="startBytePosition_${prefix}[]"]`);
        const endInput = row.querySelector(`input[name="endBytePosition_${prefix}[]"]`);

        if (startInput && endInput) {
            const start = parseInt(startInput.value);
            const end = parseInt(endInput.value);

            // Reset styling at the start of validation
            startInput.style.border = "1px solid #ccc";
            endInput.style.border = "1px solid #ccc";

            if (!isNaN(start) && !isNaN(end)) {
                // Error check: Start cannot be greater than end
                if (start > end) {
                    startInput.style.border = "2px solid red";
                    overlapFound = true;
                }

                // Check every byte in the range
                for (let i = start; i <= end; i++) {
                    if (usedBytes.has(i)) {
                        startInput.style.border = "2px solid red";
                        endInput.style.border = "2px solid red";
                        overlapFound = true;
                    }
                    usedBytes.add(i);
                }
            }
        }
    });
    return overlapFound;
}
//
function forceMessage(msg) {
    let box = document.getElementById("customAlert");
    if (!box) {
        box = document.createElement("div");
        box.id = "customAlert";
        box.style.cssText = `
            position: fixed;
            top: 60%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #FFF5F5;
            color: #C53030;
            border: 2px solid #FC8181;
            padding: 20px 40px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            z-index: 999999;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 600;
            text-align: center;
            min-width: 300px;
            transition: all 0.3s ease-in-out;
            display: none;
        `;
        document.body.appendChild(box);
    }

    // Add an icon and the message for better UX
    box.innerHTML = `<span style="font-size: 24px; vertical-align: middle; margin-right: 10px;">⚠️</span> ${msg}`;
    
    // Show with a slight pop effect
    box.style.display = "block";
    box.style.opacity = "1";
    box.style.transform = "translate(-50%, -50%) scale(1.05)";

    // Reset scale after pop
    setTimeout(() => {
        box.style.transform = "translate(-50%, -50%) scale(1)";
    }, 150);

    // Auto-hide after 4 seconds (given more time for longer error messages)
    clearTimeout(box.timer); // Clear existing timer if called again quickly
    box.timer = setTimeout(() => {
        box.style.opacity = "0";
        setTimeout(() => { box.style.display = "none"; }, 300);
    }, 4000);
}
//
function validateBitwiseOverlap() {
    const tbody = document.getElementById('bitwiseTableBody');
    const rows = tbody.querySelectorAll('tr');
    const usedBits = new Set();
    let hasOverlap = false;

    rows.forEach((row, index) => {
        const startInput = row.querySelector('.start-bit');
        const endInput = row.querySelector('.end-bit');

        if (startInput && endInput) {
            const start = parseInt(startInput.value);
            const end = parseInt(endInput.value);

            // Reset borders
            startInput.style.border = "1px solid #ccc";
            endInput.style.border = "1px solid #ccc";

            if (!isNaN(start) && !isNaN(end)) {
                // Validation: Start bit cannot be greater than end bit
                if (start > end) {
                    startInput.style.border = "2px solid red";
                    forceMessage(`Row ${index + 1}: Start Bit cannot be greater than End Bit.`);
                    hasOverlap = true;
                }

                // Check every bit in the range (typically 0-7 for one byte)
                for (let i = start; i <= end; i++) {
                    if (usedBits.has(i)) {
                        startInput.style.border = "2px solid red";
                        endInput.style.border = "2px solid red";
                        hasOverlap = true;
                    }
                    usedBits.add(i);
                }
            }
        }
    });

    if (hasOverlap) {
        forceMessage("❌ Bit Overlap Error: Bits cannot be repeated or overlap between rows.");
        return false;
    }
    return true;
}
//
const cmdViewData = [
  {
    // BYTE DATA
    startByte: 1,
    endByte: 1,
    numberOfBytes: 1,
    bitSize: 8,
    fieldDescription: "Header",
    dataType: "UnsignedInt",
    valueDefinition: "Fixed Value",
    displayValueMin: 0,
    displayValueMax: 255,
    valueInHexMin: "0x00",
    valueInHexMax: "0xFF",
    uiType: "Toggle Switch",
    uiId: 1,
    uiSerialNo: "1",
    uiValues: "1",
    asperui: "false,true",
    uiPageNo: 1,
    uiDescriptionInOut: "Read/Write",
    sliderStepValue: 1,
    defaultvalue: 1,
    developerNotes: "1",
    notificationEnable: "Yes",
    notificationMessage: "message",

    // BIT DATA (ARRAY)
    bits: [
      {
        startBit: 1,
        endBit: 1,
        bitSize: 1,
        dataType: "UnsignedInt",
        valueDefinition: "Fixed Value",
        displayValueMin: 0,
        displayValueMax: 1,
        valueInHexMin: "0",
        valueInHexMax: "1",
        uiType: "Checkbox",
        uiId: 1,
        uiSerialNo: "1",
        uiValues: "1",
        asperui: "1",
        uiPageNo: 1,
        uiDescriptionInOut: "Read/Write",
        sliderStepValue: 1,
        defaultvalue: 1,
        developerNotes: "1",
        notificationEnable: "Yes",
        notificationMessage: "bit message"
      }
    ]
  }
];


function loadCmdViewTable() { 

  const rows = document.querySelectorAll("#additionalTableBody tr");
  if (!rows.length) return; 

  const data = cmdViewData[0];

  rows.forEach((row, rowIndex) => {

    const mode = row.getAttribute("data-mode");

    /* ================= BYTE WISE ================= */
    if (mode !== "bit") {

      row.querySelector('[name="startBytePosition_cmd[]"]').value = data.startByte ?? '';
      row.querySelector('[name="endBytePosition_cmd[]"]').value = data.endByte ?? '';
      row.querySelector('[name="numberOfBytes_cmd[]"]').value = data.numberOfBytes ?? '';
      row.querySelector('[name="bitSize_cmd[]"]').value = data.bitSize ?? '';
      row.querySelector('[name="fieldDescription_cmd[]"]').value = data.fieldDescription ?? '';
      row.querySelector('[name="dataType_cmd[]"]').value = data.dataType ?? '';
      row.querySelector('[name="valueDefinition_cmd[]"]').value = data.valueDefinition ?? '';
      row.querySelector('[name="displayValueMin_cmd[]"]').value = data.displayValueMin ?? '';
      row.querySelector('[name="displayValueMax_cmd[]"]').value = data.displayValueMax ?? '';
      row.querySelector('[name="valueInHexMin_cmd[]"]').value = data.valueInHexMin ?? '';
      row.querySelector('[name="valueInHexMax_cmd[]"]').value = data.valueInHexMax ?? '';
      row.querySelector('[name="uiType_cmd[]"]').value = data.uiType ?? '';
      row.querySelector('[name="uiId_cmd[]"]').value = data.uiId ?? '';
      row.querySelector('[name="cmd_asperui[]"]').value = data.asperui ?? '';
      row.querySelector('[name="cmd_sno_asperui[]"]').value = data.uiSerialNo ?? '';
      row.querySelector('[name="cmd_asperuivalue[]"]').value = data.uiValues ?? '';
      row.querySelector('[name="uiPageNo_cmd[]"]').value = data.uiPageNo ?? '';
      row.querySelector('[name="uiDescriptionInOut_cmd[]"]').value = data.uiDescriptionInOut ?? '';
      row.querySelector('[name="sliderStepValue_cmd[]"]').value = data.sliderStepValue ?? '';
      row.querySelector('[name="defaultvalue_cmd[]"]').value = data.defaultvalue ?? '';
      row.querySelector('[name="developerNotes_cmd[]"]').value = data.developerNotes ?? '';
      row.querySelector('[name="notifEnable_cmd[]"]').value = data.notificationEnable ?? '';
      row.querySelector('[name="notifMsg_cmd[]"]').value = data.notificationMessage ?? '';

    }

    /* ================= BITWISE MAIN TABLE ================= */
    if (mode === "bit") {

      const bitRows = row.querySelectorAll(".bit-row");
      const bitDataArr = data.bits || [];

      bitRows.forEach((bitRow, bitIndex) => {

        const bitData = bitDataArr[bitIndex];
        if (!bitData) return;

        setVal(bitRow, '[name="startBit_cmd[]"]', bitData.startBit);
        setVal(bitRow, '[name="endBit_cmd[]"]', bitData.endBit);
        setVal(bitRow, '[name="noOfBits_cmd[]"]', bitData.bitSize);

        setVal(bitRow, '[name="dataType_cmd[]"]', bitData.dataType);
        setVal(bitRow, '[name="valueDefinition_cmd[]"]', bitData.valueDefinition);

        setVal(bitRow, '[name="displayValueMin_cmd[]"]', bitData.displayValueMin);
        setVal(bitRow, '[name="displayValueMax_cmd[]"]', bitData.displayValueMax);

        setVal(bitRow, '[name="valueInHexMin_cmd[]"]', bitData.valueInHexMin);
        setVal(bitRow, '[name="valueInHexMax_cmd[]"]', bitData.valueInHexMax);

        setVal(bitRow, '[name="uiType_cmd[]"]', bitData.uiType);
        setVal(bitRow, '[name="uiId_cmd[]"]', bitData.uiId);

        setVal(bitRow, '[name="cmd_asperui[]"]', bitData.asperui);
        setVal(bitRow, '[name="cmd_sno_asperui[]"]', bitData.uiSerialNo);
        setVal(bitRow, '[name="cmd_asperuivalue[]"]', bitData.uiValues);

        setVal(bitRow, '[name="uiPageNo_cmd[]"]', bitData.uiPageNo);
        setVal(bitRow, '[name="uiDescriptionInOut_cmd[]"]', bitData.uiDescriptionInOut);

        setVal(bitRow, '[name="sliderStepValue_cmd[]"]', bitData.sliderStepValue);
        setVal(bitRow, '[name="defaultvalue_cmd[]"]', bitData.defaultvalue);

        setVal(bitRow, '[name="developerNotes_cmd[]"]', bitData.developerNotes);

        setVal(bitRow, '[name="notifEnable_cmd[]"]', bitData.notificationEnable);
        setVal(bitRow, '[name="notifMsg_cmd[]"]', bitData.notificationMessage);

      });

    }

  });

}


//
function printCmdFrameToConsole() {
    const rows = document.querySelectorAll("#additionalTableBody tr");
    const frameIndex = (typeof currentCmdFrame !== 'undefined') ? currentCmdFrame : 0;

    rows.forEach((row, rowIndex) => {
        const rowNo = rowIndex + 1;
        const bitwiseData = row.bitwiseData || [];
        const isBitwise = bitwiseData.length > 0;

        if (isBitwise) {
            // ==========================================
            // BITWISE MODE: Printing individual bit details
            // ==========================================
            console.log(`%c Cmd[${frameIndex}]-Row[${rowNo}] [MODE: BITWISE] `, 'background: #222; color: #bada55; font-weight: bold;');
            
            bitwiseData.forEach((bit, bitIndex) => {
                const bitNo = bitIndex + 1;
                const s = "_" + bitNo; 

                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-startBytePosition${s} =>`, bit.startBit ?? "");
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-endBytePosition${s} =>`, bit.endBit ?? "");
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-numberOfBytes${s} =>`, bit.noOfBits ?? "");
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-dataType${s} =>`, bit.dataType ?? "");
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-valueDefinition${s} =>`, bit.valueDef ?? "");
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-displayValueMin${s} =>`, bit.displayMin ?? "");
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-displayValueMax${s} =>`, bit.displayMax ?? "");
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-valueInHexMin${s} =>`, bit.hexMin ?? "");
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-valueInHexMax${s} =>`, bit.hexMax ?? "");
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-uiType${s} =>`, bit.uiType ?? "");
                
                // Corrected Swap Mapping
               // swap them in console also
console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-uiId${s} =>`, bit.uiAsPer ?? "");

console.log(`Cmd[${frameIndex}]-row[${rowNo}]-asperui${s} =>`, bit.uiId ?? "");
                
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-uiPageNo${s} =>`, bit.uiLength ?? "");
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-uiDescriptionInOut${s} =>`, bit.uiInout ?? "");
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-sliderStepValue${s} =>`, bit.priority ?? "");
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-defaultvalue${s} =>`, bit.unit ?? "");
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-developerNotes${s} =>`, bit.description ?? "");
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-notifEnable${s} =>`, bit.notifEnable ?? "");
                console.log(`Cmd[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-notifMsg${s} =>`, bit.notifMessage ?? "");
            });

        } else {
            // ==========================================
            // BYTEWISE MODE: Print Regular Main Row Data
            // ==========================================
            console.log(`Cmd[${frameIndex}]-Row[${rowNo}] [MODE: BYTEWISE]`);
            
            const getVal = (name) => {
                const el = row.querySelector(`[name="${name}"]`);
                return el ? el.value : "";
            };

            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-startBytePosition =>`, getVal("startBytePosition_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-endBytePosition =>`, getVal("endBytePosition_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-numberOfBytes =>`, getVal("numberOfBytes_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-fieldDescription =>`, getVal("fieldDescription_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-dataType =>`, getVal("dataType_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-valueDefinition =>`, getVal("valueDefinition_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-displayValueMin =>`, getVal("displayValueMin_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-displayValueMax =>`, getVal("displayValueMax_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-valueInHexMin =>`, getVal("valueInHexMin_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-valueInHexMax =>`, getVal("valueInHexMax_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-uiType =>`, getVal("uiType_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-uiId =>`, getVal("uiId_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-asperui =>`, getVal("cmd_asperui[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-uiPageNo =>`, getVal("uiPageNo_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-uiDescriptionInOut =>`, getVal("uiDescriptionInOut_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-sliderStepValue =>`, getVal("sliderStepValue_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-defaultvalue =>`, getVal("defaultvalue_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-developerNotes =>`, getVal("developerNotes_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-notificationEnable =>`, getVal("notifEnable_cmd[]"));
            console.log(`Cmd[${frameIndex}]-row[${rowNo}]-notificationMessage =>`, getVal("notifMsg_cmd[]"));
        }
        console.log("--------------------------------------------------");
    });
}

//
function applyBitwiseToMainRow(mainRow, bitwiseData) {

    if (!mainRow || !bitwiseData || bitwiseData.length === 0) return;

    // helper to convert array
    const formatArray = (key) =>
        `[${bitwiseData.map(b => b[key] ?? "").join(',')}]`;

    // helper safe setter
    const safeSet = (name, value) => {

        const el = mainRow.querySelector(`[name="${name}"]`);
        if (!el) return;

        if (el.tagName === "SELECT") {

            let opt = el.querySelector('option[data-bitwise="true"]');

            if (!opt) {
                opt = document.createElement("option");
                opt.dataset.bitwise = "true";
                el.appendChild(opt);
            }

            opt.value = value;
            opt.textContent = value;
            el.value = value;

        } else {

            if (el.type === "number")
                el.type = "text";

            el.value = value;
        }

        el.readOnly = true;
        el.style.background = "#e9ecef";

        el.dispatchEvent(new Event("change", { bubbles: true }));
    };

    // =========================
    // POSITION
    // =========================

    safeSet("startBytePosition_cmd[]", formatArray("startBit"));
    safeSet("endBytePosition_cmd[]", formatArray("endBit"));
    safeSet("numberOfBytes_cmd[]", formatArray("noOfBits"));

    // =========================
    // DATA TYPE
    // =========================

    safeSet("dataType_cmd[]", formatArray("dataType"));
    safeSet("valueDefinition_cmd[]", formatArray("valueDef"));

    // =========================
    // DISPLAY RANGE
    // =========================

    safeSet("displayValueMin_cmd[]", formatArray("displayMin"));
    safeSet("displayValueMax_cmd[]", formatArray("displayMax"));

    // =========================
    // HEX RANGE
    // =========================

    safeSet("valueInHexMin_cmd[]", formatArray("hexMin"));
    safeSet("valueInHexMax_cmd[]", formatArray("hexMax"));

    // =========================
    // UI TYPE
    // =========================

    safeSet("uiType_cmd[]", formatArray("uiType"));

    // =========================
    // ✅ UI ID (CORRECT)
    // =========================

    safeSet("cmd_asperui[]", formatArray("uiId"));
    // =========================
    // ✅ AS PER UI (CORRECT)
    // =========================

    safeSet("uiId_cmd[]", formatArray("uiAsPer"));
    // =========================
    // OTHER UI FIELDS
    // =========================

    safeSet("cmd_sno_asperui[]", formatArray("uiSerial"));

    safeSet("cmd_asperuivalue[]", formatArray("uiValues"));

    safeSet("uiPageNo_cmd[]", formatArray("uiLength"));

    safeSet("uiDescriptionInOut_cmd[]", formatArray("uiInout"));

    // =========================
    // OTHER VALUES
    // =========================

    safeSet("sliderStepValue_cmd[]", formatArray("priority"));

    safeSet("defaultvalue_cmd[]", formatArray("unit"));

    safeSet("developerNotes_cmd[]", formatArray("description"));

    safeSet("notifEnable_cmd[]", formatArray("notifEnable"));

    safeSet("notifMsg_cmd[]", formatArray("notifMessage"));

    // =========================
    // FIELD DESCRIPTION
    // =========================

   
}
//
function saveCurrentFrameToMemory(frameIdx) {
    const rows = document.querySelectorAll("#additionalTableBody tr");
    const frameData = [];

    rows.forEach((row) => {
        const rowObj = {};
        // Save all input values
        row.querySelectorAll("input, select").forEach(el => {
            rowObj[el.name] = el.value;
            // Also capture if it was disabled/readonly (Bitwise mode)
            rowObj[el.name + "_locked"] = el.disabled || el.readOnly;
        });
        // Save the hidden bitwise object if it exists
        rowObj.bitwiseData = row.bitwiseData ? [...row.bitwiseData] : null;
        
        frameData.push(rowObj);
    });

    allFramesData[frameIdx] = frameData;
}

function loadFrameFromMemory(frameIdx) {
    const tableBody = document.getElementById("additionalTableBody");
    const data = allFramesData[frameIdx];

    if (!data) {
        // If no data exists for this frame yet, just clear the table for fresh entry
        resetMainTableUI(); 
        return;
    }

    const rows = tableBody.querySelectorAll("tr");
    data.forEach((rowData, rowIndex) => {
        const row = rows[rowIndex];
        if (!row) return;

        // Restore bitwise object
        row.bitwiseData = rowData.bitwiseData;

        // Restore each field
        for (const [name, value] of Object.entries(rowData)) {
            if (name.endsWith("_locked")) continue; // Skip the lock flag

            const el = row.querySelector(`[name="${name}"]`);
            if (el) {
                el.value = value;
                
                // Handle the Read-Only/Disabled state (Bitwise Lock)
                const isLocked = rowData[name + "_locked"];
                if (el.tagName === "SELECT") {
                    el.disabled = isLocked;
                    // If it was bitwise, ensure the special option exists
                    if (isLocked && !el.querySelector('option[data-bitwise="true"]')) {
                        const opt = document.createElement("option");
                        opt.setAttribute('data-bitwise', 'true');
                        opt.value = value;
                        opt.text = "Bitwise Configured";
                        el.appendChild(opt);
                        el.value = value;
                    }
                } else {
                    el.readOnly = isLocked;
                }
                
                if (isLocked) el.style.backgroundColor = "#f0f0f0";
                else el.style.backgroundColor = "";
            }
        }
    });
}
//
const respViewData = [
  {
    startByte: 1,
    endByte: 1,
    numberOfBytes: 1,
    bitSize: 8,
   fieldDescription: "Header",    
    dataType: "UnsignedInt",
    valueDefinition: "Fixed Value",
    displayValueMin: 0,
    displayValueMax: 255,
    valueInHexMin: "0x00",
    valueInHexMax: "0xFF",
    uiType: "Toggle Switch",
    uiId: 1,
    uiSerialNo: "1",
    uiValues: "1",
    asperui: "false,true",
    uiPageNo: 1,
    uiDescriptionInOut: "Read",
    sliderStepValue: 1,
    defaultvalue: 1,
    developerNotes: "1",
    notificationEnable: "Yes",
    notificationMessage: "message",

    bits: [
      {
        startBit: 1,
        endBit: 1,
        bitSize: 1,
        dataType: "UnsignedInt",
        valueDefinition: "Fixed Value",
        displayValueMin: 0,
        displayValueMax: 1,
        valueInHexMin: "0",
        valueInHexMax: "1",
        uiType: "Checkbox",
        uiId: 1,
        uiSerialNo: "1",
        uiValues: "1",
        asperui: "1",
        uiPageNo: 1,
        uiDescriptionInOut: "Read",
        sliderStepValue: 1,
        defaultvalue: 1,
        developerNotes: " 1",
        notificationEnable: "Yes",
        notificationMessage:" message"
      }
    ]
  }
];
//
function loadRespViewTable() {

  const rows = document.querySelectorAll("#responseTableBody tr");
  if (!rows.length) return;

  const data = respViewData[0];

  rows.forEach((row, rowIndex) => {

    const mode = row.getAttribute("data-mode");

    /* BYTEWISE */
    if (mode !== "bit") {

      setVal(row, '[name="startBytePosition_resp[]"]', data.startByte);
      setVal(row, '[name="endBytePosition_resp[]"]', data.endByte);
      setVal(row, '[name="numberOfBytes_resp[]"]', data.numberOfBytes);
      setVal(row, '[name="bitSize_resp[]"]', data.bitSize);
      setVal(row, '[name="fieldDescription_cmd[]"]', data.fieldDescription);
      setVal(row, '[name="dataType_resp[]"]', data.dataType);
      setVal(row, '[name="valueDefinition_resp[]"]', data.valueDefinition);
      setVal(row, '[name="displayValueMin_resp[]"]', data.displayValueMin);
      setVal(row, '[name="displayValueMax_resp[]"]', data.displayValueMax);
      setVal(row, '[name="valueInHexMin_resp[]"]', data.valueInHexMin);
      setVal(row, '[name="valueInHexMax_resp[]"]', data.valueInHexMax);
      setVal(row, '[name="uiType_resp[]"]', data.uiType);
      setVal(row, '[name="uiId_resp[]"]', data.uiId);
      setVal(row, '[name="resp_asperui[]"]', data.asperui);
      setVal(row, '[name="resp_sno_asperui[]"]', data.uiSerialNo);
      setVal(row, '[name="resp_asperuivalue[]"]', data.uiValues);
      setVal(row, '[name="uiPageNo_resp[]"]', data.uiPageNo);
      setVal(row, '[name="uiDescriptionInOut_resp[]"]', data.uiDescriptionInOut);
      setVal(row, '[name="sliderStepValue_resp[]"]', data.sliderStepValue);
      setVal(row, '[name="defaultvalue_resp[]"]', data.defaultvalue);
      setVal(row, '[name="developerNotes_resp[]"]', data.developerNotes);
      setVal(row, '[name="notifEnable_resp[]"]', data.notificationEnable);
      setVal(row, '[name="notifMsg_resp[]"]', data.notificationMessage);

    }

    /* BITWISE */
    if (mode === "bit") {

      const bitRows = row.querySelectorAll(".bit-row");
      const bitDataArr = data.bits || [];

      bitRows.forEach((bitRow, bitIndex) => {

        const bitData = bitDataArr[bitIndex];
        if (!bitData) return;

        setVal(bitRow, '[name="startBit_resp[]"]', bitData.startBit);
        setVal(bitRow, '[name="endBit_resp[]"]', bitData.endBit);
        setVal(bitRow, '[name="noOfBits_resp[]"]', bitData.bitSize);
        setVal(bitRow, '[name="dataType_resp[]"]', bitData.dataType);
        setVal(bitRow, '[name="valueDefinition_resp[]"]', bitData.valueDefinition);
        setVal(bitRow, '[name="displayValueMin_resp[]"]', bitData.displayValueMin);
        setVal(bitRow, '[name="displayValueMax_resp[]"]', bitData.displayValueMax);
        setVal(bitRow, '[name="valueInHexMin_resp[]"]', bitData.valueInHexMin);
        setVal(bitRow, '[name="valueInHexMax_resp[]"]', bitData.valueInHexMax);
        setVal(bitRow, '[name="uiType_resp[]"]', bitData.uiType);
        setVal(bitRow, '[name="uiId_resp[]"]', bitData.uiId);
        setVal(bitRow, '[name="resp_asperui[]"]', bitData.asperui);
        setVal(bitRow, '[name="resp_sno_asperui[]"]', bitData.uiSerialNo);
        setVal(bitRow, '[name="resp_asperuivalue[]"]', bitData.uiValues);
        setVal(bitRow, '[name="uiPageNo_resp[]"]', bitData.uiPageNo);
        setVal(bitRow, '[name="uiDescriptionInOut_resp[]"]', bitData.uiDescriptionInOut);
        setVal(bitRow, '[name="sliderStepValue_resp[]"]', bitData.sliderStepValue);
        setVal(bitRow, '[name="defaultvalue_resp[]"]', bitData.defaultvalue);
        setVal(bitRow, '[name="developerNotes_resp[]"]', bitData.developerNotes);
        setVal(bitRow, '[name="notifEnable_resp[]"]', bitData.notificationEnable);
        setVal(bitRow, '[name="notifMsg_resp[]"]', bitData.notificationMessage);

      });

    }

  });

}
//
function setVal(parent, selector, value) {

    const el = parent.querySelector(selector);

    if (!el) return;

    if (el.tagName === "SELECT") {
        el.value = value ?? "";
        el.dispatchEvent(new Event("change")); // trigger dependent logic
    }
    else if (el.tagName === "INPUT" || el.tagName === "TEXTAREA") {
        el.value = value ?? "";
        el.dispatchEvent(new Event("input"));
    }

}

//
//
function printRespFrameToConsole() {
    console.log("%c --- PRINT FUNCTION CALLED --- ", 'background: #333; color: #fff; padding: 2px 5px;');

    // Corrected ID to your actual tbody ID
    const rows = document.querySelectorAll("#responseTableBody tr");
    const frameIndex = (typeof currentRespFrame !== 'undefined') ? currentRespFrame : 0;

    if (rows.length === 0) {
        console.warn("No rows found in #responseTableBody. Check if the table is populated.");
        return;
    }

    rows.forEach((row, rowIndex) => {
        const rowNo = rowIndex + 1;
        const bitwiseData = row.bitwiseData || [];
        const isBitwise = bitwiseData.length > 0;

        if (isBitwise) {
            // ==========================================
            // BITWISE MODE
            // ==========================================
            console.log(`%c Resp[${frameIndex}]-Row[${rowNo}] [MODE: BITWISE] `,
                'background: #222; color: #00bfff; font-weight: bold;');

            bitwiseData.forEach((bit, bitIndex) => {
                const bitNo = bitIndex + 1;
                const s = "_" + bitNo;

                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-startBit${s} =>`, bit.startBit ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-endBit${s} =>`, bit.endBit ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-numberOfBits${s} =>`, bit.noOfBits ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-dataType${s} =>`, bit.dataType ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-valueDefinition${s} =>`, bit.valueDef ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-displayValueMin${s} =>`, bit.displayMin ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-displayValueMax${s} =>`, bit.displayMax ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-valueInHexMin${s} =>`, bit.hexMin ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-valueInHexMax${s} =>`, bit.hexMax ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-uiType${s} =>`, bit.uiType ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-uiId${s} =>`, bit.uiAsPer ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-asperui${s} =>`, bit.uiId ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-uiPageNo${s} =>`, bit.uiLength ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-uiDescriptionInOut${s} =>`, bit.uiInout ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-sliderStepValue${s} =>`, bit.priority ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-defaultvalue${s} =>`, bit.unit ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-developerNotes${s} =>`, bit.description ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-notifEnable${s} =>`, bit.notifEnable ?? "");
                console.log(`Resp[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-notifMsg${s} =>`, bit.notifMessage ?? "");
            });
        } else {
            // ==========================================
            // BYTEWISE MODE
            // ==========================================
            console.log(`%c Resp[${frameIndex}]-Row[${rowNo}] [MODE: BYTEWISE] `, 
                'background: #222; color: #32cd32; font-weight: bold;');

            const getVal = (name) => {
                const el = row.querySelector(`[name="${name}"]`);
                return el ? el.value : "";
            };

            console.log(`Resp[${frameIndex}]-row[${rowNo}]-startBytePosition =>`, getVal("startBytePosition_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-endBytePosition => =>`, getVal("endBytePosition_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-numberOfBytes =>`, getVal("numberOfBytes_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-fieldDescription =>`, getVal("fieldDescription_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-dataType =>`, getVal("dataType_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-valueDefinition =>`, getVal("valueDefinition_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-displayValueMin =>`, getVal("displayValueMin_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-displayValueMax =>`, getVal("displayValueMax_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-valueInHexMin =>`, getVal("valueInHexMin_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-valueInHexMax =>`, getVal("valueInHexMax_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-uiType =>`, getVal("uiType_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-uiId =>`, getVal("uiId_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-asperui =>`, getVal("resp_asperui[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-uiPageNo =>`, getVal("uiPageNo_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-uiDescriptionInOut =>`, getVal("uiDescriptionInOut_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-sliderStepValue =>`, getVal("sliderStepValue_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-defaultvalue =>`, getVal("defaultvalue_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-developerNotes =>`, getVal("developerNotes_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-notificationEnable =>`, getVal("notifEnable_resp[]"));
            console.log(`Resp[${frameIndex}]-row[${rowNo}]-notificationMessage =>`, getVal("notifMsg_resp[]"));
        }

        console.log("--------------------------------------------------");
    });
}
function applyBitwiseToResponseRow(mainRow, bitwiseData) {

    if (!mainRow || !bitwiseData || bitwiseData.length === 0) return;

    // Helper to convert the array of objects into a bracketed string [val1,val2,...]
    const formatArray = (key) =>
        `[${bitwiseData.map(b => b[key] ?? "").join(',')}]`;

    // Helper to safely set values in the main table inputs
    const safeSet = (name, value) => {
        const el = mainRow.querySelector(`[name="${name}"]`);
        if (!el) return;

        if (el.tagName === "SELECT") {
            let opt = el.querySelector('option[data-bitwise="true"]');
            if (!opt) {
                opt = document.createElement("option");
                opt.dataset.bitwise = "true";
                el.appendChild(opt);
            }
            opt.value = value;
            opt.textContent = value;
            el.value = value;
        } else {
            // Change type to text if it was numeric to allow the "[...]" string format
            if (el.type === "number") el.type = "text";
            el.value = value;
        }

        el.readOnly = true;
        el.style.background = "#e9ecef"; // Grey out to indicate bitwise control
        el.dispatchEvent(new Event("change", { bubbles: true }));
    };

    // =========================
    // POSITION (mapped to _resp[])
    // =========================
    safeSet("startBytePosition_resp[]", formatArray("startBit"));
    safeSet("endBytePosition_resp[]", formatArray("endBit"));
    safeSet("numberOfBytes_resp[]", formatArray("noOfBits"));

    // =========================
    // DATA TYPE & FIELD DESC
    // =========================
    safeSet("dataType_resp[]", formatArray("dataType"));
    safeSet("valueDefinition_resp[]", formatArray("valueDef"));
    // Note: If you have a description field for the whole row:
    // safeSet("fieldDescription_resp[]", formatArray("description"));

    // =========================
    // DISPLAY RANGE
    // =========================
    safeSet("displayValueMin_resp[]", formatArray("displayMin"));
    safeSet("displayValueMax_resp[]", formatArray("displayMax"));

    // =========================
    // HEX RANGE
    // =========================
    safeSet("valueInHexMin_resp[]", formatArray("hexMin"));
    safeSet("valueInHexMax_resp[]", formatArray("hexMax"));

    // =========================
    // UI CONFIGURATION
    // =========================
    safeSet("uiType_resp[]", formatArray("uiType"));
    safeSet("resp_asperui[]", formatArray("uiId"));   // Matches your print script
    safeSet("uiId_resp[]", formatArray("uiAsPer"));  // Matches your print script

safeSet("resp_sno_asperui[]", formatArray("uiSerial"));
    safeSet("resp_asperuivalue[]", formatArray("uiValues"));
    
    safeSet("uiPageNo_resp[]", formatArray("uiLength"));
    safeSet("uiDescriptionInOut_resp[]", formatArray("uiInout"));

    // =========================
    // OTHER VALUES
    // =========================
    safeSet("sliderStepValue_resp[]", formatArray("priority"));
    safeSet("defaultvalue_resp[]", formatArray("unit"));
    safeSet("developerNotes_resp[]", formatArray("description"));

    // =========================
    // NOTIFICATIONS
    // =========================
    safeSet("notifEnable_resp[]", formatArray("notifEnable"));
    safeSet("notifMsg_resp[]", formatArray("notifMessage"));
}
//
const errorViewData = [
  {
    startByte: 1,
    endByte: 1,
    numberOfBytes: 1,
    bitSize: 8,
    fieldDescription: "Header",
    dataType: "UnsignedInt",
    valueDefinition: "Fixed Value",
    displayValueMin: 0,
    displayValueMax: 255,
    valueInHexMin: "0x00",
    valueInHexMax: "0xFF",
    uiType: "Toggle Switch",
    uiId: 1,
    uiSerialNo: "1",
    uiValues: "1",
    asperui: "false,true",
    uiPageNo: 1,
    uiDescriptionInOut: "Read/Write",
    sliderStepValue: 1,
    defaultvalue: 1,
    developerNotes: "1",
    notificationEnable: "Yes",
    notificationMessage: "message",

    bits: [
      {
        startBit: 1,
        endBit: 1,
        bitSize: 1,
        dataType: "UnsignedInt",
        valueDefinition: "Fixed Value",
        displayValueMin: 0,
        displayValueMax: 1,
        valueInHexMin: "0",
        valueInHexMax: "1",
        uiType: "Checkbox",
        uiId: 1,
        uiSerialNo: "1",
        uiValues: "1",
        asperui: "1",
        uiPageNo: 1,
        uiDescriptionInOut: "Read/Write",
        sliderStepValue: 1,
        defaultvalue: 1,
        developerNotes: "1",
        notificationEnable: "Yes",
        notificationMessage: "bit message"
      }
    ]
  }
];

function loadErrorViewTable() {

  const tableBody = document.getElementById("errorTableBody");
  if (!tableBody) {
    console.error("errorTableBody not found");
    return;
  }

  const rows = tableBody.querySelectorAll("tr");
  if (!rows.length) return;

  const data = errorViewData[0];
  if (!data) return;

  /* SAFE VALUE SETTER */
  const safeSet = (parent, selector, value) => {
    const el = parent.querySelector(selector);
    if (el) el.value = value ?? "";
  };

  rows.forEach((row, rowIndex) => {

    const mode = row.getAttribute("data-mode");

    /* ================= BYTEWISE ================= */
    if (mode !== "bit") {

      safeSet(row, '[name="startBytePosition_error[]"]', data.startByte);
      safeSet(row, '[name="endBytePosition_error[]"]', data.endByte);
      safeSet(row, '[name="numberOfBytes_error[]"]', data.numberOfBytes);
      safeSet(row, '[name="bitSize_error[]"]', data.bitSize);

      safeSet(row, '[name="fieldDescription_cmd[]"]', data.fieldDescription);

      safeSet(row, '[name="dataType_error[]"]', data.dataType);
      safeSet(row, '[name="valueDefinition_error[]"]', data.valueDefinition);

      safeSet(row, '[name="displayValueMin_error[]"]', data.displayValueMin);
      safeSet(row, '[name="displayValueMax_error[]"]', data.displayValueMax);

      safeSet(row, '[name="valueInHexMin_error[]"]', data.valueInHexMin);
      safeSet(row, '[name="valueInHexMax_error[]"]', data.valueInHexMax);

      safeSet(row, '[name="uiType_error[]"]', data.uiType);
      safeSet(row, '[name="uiId_error[]"]', data.uiId);

      safeSet(row, '[name="error_asperui[]"]', data.asperui);
      safeSet(row, '[name="error_sno_asperui[]"]', data.uiSerialNo);
      safeSet(row, '[name="error_asperuivalue[]"]', data.uiValues);

      safeSet(row, '[name="uiPageNo_error[]"]', data.uiPageNo);
      safeSet(row, '[name="uiDescriptionInOut_error[]"]', data.uiDescriptionInOut);

      safeSet(row, '[name="sliderStepValue_error[]"]', data.sliderStepValue);
      safeSet(row, '[name="defaultvalue_error[]"]', data.defaultvalue);

      safeSet(row, '[name="developerNotes_error[]"]', data.developerNotes);

      safeSet(row, '[name="notifEnable_error[]"]', data.notificationEnable);
      safeSet(row, '[name="notifMsg_error[]"]', data.notificationMessage);

    }

    /* ================= BITWISE ================= */
    if (mode === "bit") {

      const bitRows = row.querySelectorAll(".bit-row");
      const bitDataArr = data.bits || [];

      /* store for console + pagination */
      row.bitwiseData = bitDataArr.map(bit => ({
        startBit: bit.startBit,
        endBit: bit.endBit,
        noOfBits: bit.bitSize,
        dataType: bit.dataType,
        valueDef: bit.valueDefinition,
        displayMin: bit.displayValueMin,
        displayMax: bit.displayValueMax,
        hexMin: bit.valueInHexMin,
        hexMax: bit.valueInHexMax,
        uiType: bit.uiType,
        uiId: bit.uiId,
        uiSerial: bit.uiSerialNo,
        uiValues: bit.uiValues,
        uiLength: bit.uiPageNo,
        uiInout: bit.uiDescriptionInOut,
        priority: bit.sliderStepValue,
        unit: bit.defaultvalue,
        description: bit.developerNotes,
        notifEnable: bit.notificationEnable,
        notifMessage: bit.notificationMessage
      }));

      bitRows.forEach((bitRow, bitIndex) => {

        const bit = bitDataArr[bitIndex];
        if (!bit) return;

        safeSet(bitRow, '[name="startBit_error[]"]', bit.startBit);
        safeSet(bitRow, '[name="endBit_error[]"]', bit.endBit);
        safeSet(bitRow, '[name="noOfBits_error[]"]', bit.bitSize);

        safeSet(bitRow, '[name="dataType_error[]"]', bit.dataType);
        safeSet(bitRow, '[name="valueDefinition_error[]"]', bit.valueDefinition);

        safeSet(bitRow, '[name="displayValueMin_error[]"]', bit.displayValueMin);
        safeSet(bitRow, '[name="displayValueMax_error[]"]', bit.displayValueMax);

        safeSet(bitRow, '[name="valueInHexMin_error[]"]', bit.valueInHexMin);
        safeSet(bitRow, '[name="valueInHexMax_error[]"]', bit.valueInHexMax);

        safeSet(bitRow, '[name="uiType_error[]"]', bit.uiType);
        safeSet(bitRow, '[name="uiId_error[]"]', bit.uiId);

        safeSet(bitRow, '[name="error_asperui[]"]', bit.asperui);
        safeSet(bitRow, '[name="error_sno_asperui[]"]', bit.uiSerialNo);
        safeSet(bitRow, '[name="error_asperuivalue[]"]', bit.uiValues);

        safeSet(bitRow, '[name="uiPageNo_error[]"]', bit.uiPageNo);
        safeSet(bitRow, '[name="uiDescriptionInOut_error[]"]', bit.uiDescriptionInOut);

        safeSet(bitRow, '[name="sliderStepValue_error[]"]', bit.sliderStepValue);
        safeSet(bitRow, '[name="defaultvalue_error[]"]', bit.defaultvalue);

        safeSet(bitRow, '[name="developerNotes_error[]"]', bit.developerNotes);

        safeSet(bitRow, '[name="notifEnable_error[]"]', bit.notificationEnable);
        safeSet(bitRow, '[name="notifMsg_error[]"]', bit.notificationMessage);

      });

    }

  });

}
function printErrorFrameToConsole() {
    // Selects rows from the specific Error Table Body
    const rows = document.querySelectorAll("#errorTableBody tr");
    const frameIndex = (typeof currentErrorFrame !== 'undefined') ? currentErrorFrame : 0;

    rows.forEach((row, rowIndex) => {
        const rowNo = rowIndex + 1;
        const bitwiseData = row.bitwiseData || [];
        const isBitwise = bitwiseData.length > 0;

        if (isBitwise) {
            // ==========================================
            // BITWISE MODE: Printing individual bit details
            // ==========================================
            console.log(`%c Error[${frameIndex}]-Row[${rowNo}] [MODE: BITWISE] `, 'background: #222; color: #bada55; font-weight: bold;');
            
            bitwiseData.forEach((bit, bitIndex) => {
                const bitNo = bitIndex + 1;
                const s = "_" + bitNo; 

                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-startBit${s} =>`, bit.startBit ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-endBit${s} =>`, bit.endBit ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-noOfBits${s} =>`, bit.noOfBits ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-dataType${s} =>`, bit.dataType ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-valueDefinition${s} =>`, bit.valueDef ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-displayValueMin${s} =>`, bit.displayMin ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-displayValueMax${s} =>`, bit.displayMax ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-valueInHexMin${s} =>`, bit.hexMin ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-valueInHexMax${s} =>`, bit.hexMax ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-uiType${s} =>`, bit.uiType ?? "");
                
                // Matching your Cmd logic's mapping for consistency
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-uiId${s} =>`, bit.uiAsPer ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-asperui${s} =>`, bit.uiId ?? "");
                
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-uiPageNo${s} =>`, bit.uiLength ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-uiDescriptionInOut${s} =>`, bit.uiInout ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-sliderStepValue${s} =>`, bit.priority ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-defaultvalue${s} =>`, bit.unit ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-developerNotes${s} =>`, bit.description ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-notifEnable${s} =>`, bit.notifEnable ?? "");
                console.log(`Error[${frameIndex}]-row[${rowNo}]-bit[${bitNo}]-notifMsg${s} =>`, bit.notifMessage ?? "");
            });

        } else {
            // ==========================================
            // BYTEWISE MODE: Print Regular Main Row Data
            // ==========================================
            console.log(`Error[${frameIndex}]-Row[${rowNo}] [MODE: BYTEWISE]`);
            
            const getVal = (name) => {
                const el = row.querySelector(`[name="${name}"]`);
                return el ? el.value : "";
            };

            // Using the specific Error Input names from your HTML
            console.log(`Error[${frameIndex}]-row[${rowNo}]-startBytePosition =>`, getVal("startBytePosition_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-endBytePosition =>`, getVal("endBytePosition_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-numberOfBytes =>`, getVal("numberOfBytes_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-fieldDescription =>`, getVal("fieldDescription_cmd[]")); // Matches your HTML select name
            console.log(`Error[${frameIndex}]-row[${rowNo}]-dataType =>`, getVal("dataType_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-valueDefinition =>`, getVal("valueDefinition_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-displayValueMin =>`, getVal("displayValueMin_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-displayValueMax =>`, getVal("displayValueMax_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-valueInHexMin =>`, getVal("valueInHexMin_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-valueInHexMax =>`, getVal("valueInHexMax_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-uiType =>`, getVal("uiType_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-uiId =>`, getVal("uiId_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-asperui =>`, getVal("error_asperui[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-uiPageNo =>`, getVal("uiPageNo_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-uiDescriptionInOut =>`, getVal("uiDescriptionInOut_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-sliderStepValue =>`, getVal("sliderStepValue_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-defaultvalue =>`, getVal("defaultvalue_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-developerNotes =>`, getVal("developerNotes_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-notificationEnable =>`, getVal("notifEnable_error[]"));
            console.log(`Error[${frameIndex}]-row[${rowNo}]-notificationMessage =>`, getVal("notifMsg_error[]"));
        }
        console.log("--------------------------------------------------");
    });
}

function applyBitwiseToErrorMainRow(mainRow, bitwiseData) {

  if (!mainRow || !bitwiseData || bitwiseData.length === 0) return;

  /* FORMAT ARRAY LIKE: [1,2,3] */
  const formatArray = (key) => {
    return `[${bitwiseData.map(b => b[key] ?? "").join(",")}]`;
  };

  /* SAFE SET FUNCTION */
  const safeSet = (name, value) => {

    const el = mainRow.querySelector(`[name="${name}"]`);
    if (!el) return;

    if (el.tagName === "SELECT") {

      let opt = el.querySelector('option[data-bitwise="true"]');

      if (!opt) {
        opt = document.createElement("option");
        opt.dataset.bitwise = "true";
        el.appendChild(opt);
      }

      opt.value = value;
      opt.textContent = value;
      el.value = value;

    } else {

      if (el.type === "number") {
        el.type = "text";
      }

      el.value = value;
    }

    /* LOCK FIELD */
    el.readOnly = true;
    el.style.background = "#e9ecef";

    /* TRIGGER CHANGE EVENT */
    el.dispatchEvent(new Event("change", { bubbles: true }));
  };

  /* POSITION */
  safeSet("startBytePosition_error[]", formatArray("startBit"));
  safeSet("endBytePosition_error[]", formatArray("endBit"));
  safeSet("numberOfBytes_error[]", formatArray("noOfBits"));

  /* DATA TYPE */
  safeSet("dataType_error[]", formatArray("dataType"));
  safeSet("valueDefinition_error[]", formatArray("valueDef"));

  /* DISPLAY */
  safeSet("displayValueMin_error[]", formatArray("displayMin"));
  safeSet("displayValueMax_error[]", formatArray("displayMax"));

  /* HEX */
  safeSet("valueInHexMin_error[]", formatArray("hexMin"));
  safeSet("valueInHexMax_error[]", formatArray("hexMax"));

  /* UI TYPE */
  safeSet("uiType_error[]", formatArray("uiType"));

  /* UI MAPPING */
  safeSet("error_asperui[]", formatArray("uiId"));
  safeSet("uiId_error[]", formatArray("uiAsPer"));

  safeSet("error_sno_asperui[]", formatArray("uiSerial"));
  safeSet("error_asperuivalue[]", formatArray("uiValues"));

  safeSet("uiPageNo_error[]", formatArray("uiLength"));
  safeSet("uiDescriptionInOut_error[]", formatArray("uiInout"));

  /* OTHER */
  safeSet("sliderStepValue_error[]", formatArray("priority"));
  safeSet("defaultvalue_error[]", formatArray("unit"));
  safeSet("developerNotes_error[]", formatArray("description"));

  /* NOTIFICATION */
  safeSet("notifEnable_error[]", formatArray("notifEnable"));
  safeSet("notifMsg_error[]", formatArray("notifMessage"));

}
//
function validateNotificationMessages(tableBodyId, frameName = "Frame") {

    const tbody = document.getElementById(tableBodyId);

    if (!tbody) return true;

    const rows = tbody.querySelectorAll("tr");

    let hasError = false;
    let firstErrorRow = -1;

    rows.forEach((row, index) => {

        const notifEnable = row.querySelector('select[name^="notifEnable_"]');
        const notifMsg    = row.querySelector('input[name^="notifMsg_"]');

        if (!notifEnable || !notifMsg) return;

        // Reset border
        notifMsg.style.border = "1px solid #ccc";

        // Validation
        if (notifEnable.value === "Yes" && notifMsg.value.trim() === "") {

            notifMsg.style.border = "2px solid red";

            if (firstErrorRow === -1) {
                firstErrorRow = index + 1;
            }

            hasError = true;
        }

    });

    if (hasError) {

        forceMessage(
            `${frameName} Row ${firstErrorRow}: Notification Message is required`
        );

        return false;
    }

    return true;
}
//
function validateTableComplete(tableBodyId, frameName) {

    const tbody = document.getElementById(tableBodyId);
    if (!tbody) return true;

    const rows = tbody.querySelectorAll("tr");

    let isValid = true;

    rows.forEach((row, rowIndex) => {

        const inputs = row.querySelectorAll("input, select");

        let rowHasError = false;

        inputs.forEach(input => {

            if (
                input.type === "button" ||
                input.type === "hidden" ||
                input.disabled ||
                input.readOnly ||
                input.offsetParent === null   // ignore hidden fields
            ) return;

            const notifEnable = row.querySelector('[name^="notifEnable"]');
            const notifMsg = row.querySelector('[name^="notifMsg"]');

            // Skip notif message if enable = No
            if (
                notifEnable &&
                notifMsg &&
                input === notifMsg &&
                notifEnable.value === "No"
            ) {
                input.style.border = "1px solid #ccc";
                return;
            }

            const value = input.value;

            // Ignore selects that are auto-managed but visually filled
            if (input.tagName === "SELECT") {

                if (value === "" || value === null) {

                    console.error(
                        `❌ Missing field → ${frameName}, Row ${rowIndex + 1}, Field: ${input.className}`
                    );

                    input.style.border = "2px solid red";
                    rowHasError = true;
                    isValid = false;

                } else {

                    input.style.border = "1px solid #ccc";

                }

            }
            else {

                if (value.trim() === "") {

                    console.error(
                        `❌ Missing field → ${frameName}, Row ${rowIndex + 1}, Field: ${input.className}`
                    );

                    input.style.border = "2px solid red";
                    rowHasError = true;
                    isValid = false;

                } else {

                    input.style.border = "1px solid #ccc";

                }

            }

        });

    });

    if (!isValid) {

        forceMessage(`❌ ${frameName}: Please fill all required fields.`);

        return false;
    }

    return true;
}
//
function printStepSummary(frameLabel) {
    // 1. Determine which table we are reading based on the frameLabel
    let tbodyId = "";
    if (frameLabel === "COMMAND") tbodyId = "#additionalTableBody";
    else if (frameLabel === "RESPONSE") tbodyId = "#responseAdditionalTableBody";
    else if (frameLabel === "ERROR") tbodyId = "#errorTableBody";

    const rows = document.querySelectorAll(`${tbodyId} tr`);
    const summaryArray = [];

    rows.forEach((row, index) => {
        // Only collect data if the row has a description or start position
        const desc = row.querySelector(".field-description")?.value || row.querySelector(".description")?.value || "";
        
        if (desc !== "" || row.bitwiseData) {
            const rowData = {
                "Row": index + 1,
                "Description": desc,
                "Start": row.querySelector(".start-byte")?.value || row.querySelector(".start-bit")?.value || "",
                "End": row.querySelector(".end-byte")?.value || row.querySelector(".end-bit")?.value || "",
                "Type": row.querySelector(".data-type")?.value || "",
                "Value Def": row.querySelector(".value-def")?.value || "",
                "Bitwise": row.bitwiseData ? `Yes (${row.bitwiseData.length} bits)` : "No"
            };
            summaryArray.push(rowData);
        }
    });

    // 2. Print a clean, readable Table
    if (summaryArray.length > 0) {
        console.log(`%c >>> ${frameLabel} FRAME SUMMARY <<< `, "background: #222; color: #bada55; font-size: 12px; font-weight: bold;");
        console.table(summaryArray); 
    }
}
document.querySelector('.save-btn').addEventListener('click', function(e) {

  e.preventDefault(); // stops form submit

  saveConfiguration();

});
//






/***************************************************/


function saveConfiguration() {
    console.log("%c >>> STARTING SAVE CONFIGURATION <<< ",
        'background:#28a745; color:#fff; font-size:16px; font-weight:bold; padding:5px;');

    // First validate all steps
    if (!validateStep(1) || !validateStep(2) || !validateStep(3) || !validateStep(4)) {
        forceMessage("Please fix all validation errors before saving.");
        return false;
    }

    // Capture current frame data for all steps
    if (typeof currentCmdFrame !== 'undefined') {
        commandFrameData[currentCmdFrame] = readTableById("#additionalTableBody");
    }
    if (typeof currentRespFrame !== 'undefined') {
        responseFrameData[currentRespFrame] = readTableById("#responseTableBody");
    }
    if (typeof currentErrorFrame !== 'undefined') {
        errorFrameData[currentErrorFrame] = readTableById("#errorTableBody");
    }

    // Create FormData object
    const formData = new FormData();

    // ================= STEP 1: PRODUCT INFO =================
    formData.append('cmd_parametername', document.getElementById('par_parametername')?.value || '');
    formData.append('date', document.querySelector('input[name="date"]')?.value || '');
    formData.append('numberofpages_val', document.getElementById('numberofpage_input')?.value || '');
    formData.append('Sequence', document.getElementById('Sequence')?.value || '');
    formData.append('total_command_frames', document.getElementById('numberofpages')?.value || '0');
    formData.append('total_response_frames', document.getElementById('page')?.value || '0');
    formData.append('total_error_frames', document.getElementById('pages')?.value || '0');
    formData.append('pagenames', document.getElementById('pagenames')?.value || '');
    formData.append('product_description', document.getElementById('product_description')?.value || '');
    formData.append('modelname', document.querySelector('input[name="modelname"]')?.value || '');
    formData.append('productname', document.querySelector('input[name="productname"]')?.value || '');
    formData.append('brandname', document.querySelector('input[name="brandname"]')?.value || '');

    // ================= STEP 2: COMMAND FRAME =================
    formData.append('cmd_dataflow', document.getElementById('cmd_data_flow_direction')?.value || '');
    formData.append('cmd_frametype', document.getElementById('cmd_frametype')?.value || 'COMMAND');
    formData.append('cmd_framesizeinbytes', document.getElementById('cmd_framesizeinbytes')?.value || '0');
    formData.append('cmd_noofrows', document.getElementById('cmd_noofrows')?.value || '0');
    formData.append('cmd_frametrigger', document.getElementById('frame_trigger_options')?.value || '');
    formData.append('cmd_multipleui', document.getElementById('cmd_multipleui')?.value || '');
    formData.append('cmd_triggerui', document.getElementById('cmd_triggerui')?.value || '');
    formData.append('cmd_voicecommand', document.getElementById('cmd_voicecommand')?.value || '');
    formData.append('cmd_appnotification', document.getElementById('cmd_appnotification')?.value || '');

const cmdRows = document.querySelectorAll('#additionalTableBody tr');
const cmdTableData = [];

const cmdFields = [
    'startBytePosition_cmd[]', 'endBytePosition_cmd[]', 'fieldDescription_cmd[]',
    'numberOfBytes_cmd[]', 'bitSize_cmd[]', 'dataType_cmd[]', 'valueDefinition_cmd[]',
    'displayValueMin_cmd[]', 'displayValueMax_cmd[]', 'valueInHexMin_cmd[]', 'valueInHexMax_cmd[]',
    'uiType_cmd[]', 'uiId_cmd[]', 'uiPageNo_cmd[]', 'cmd_asperui[]', 'cmd_sno_asperui[]',
    'cmd_asperuivalue[]', 'uiDescriptionInOut_cmd[]', 'sliderStepValue_cmd[]', 'defaultvalue_cmd[]',
    'developerNotes_cmd[]', 'notifEnable_cmd[]', 'notifMsg_cmd[]'
];

cmdRows.forEach(row => {
    if (row.bitwiseData && row.bitwiseData.length > 0) {
        // Bitwise row — only push bitwise data
        cmdTableData.push({
            bitwiseData: row.bitwiseData
        });
    } else {
        // Normal bytewise row — dynamically gather all fields
        const rowData = { bitwiseData: [] }; // include bitwiseData key for consistency
        cmdFields.forEach(field => {
            // Remove [] from the name for the key
            const key = field.replace('[]', '');
            rowData[key] = row.querySelector(`[name="${field}"]`)?.value || '';
        });
        cmdTableData.push(rowData);
    }
});

    // ================= STEP 3: RESPONSE FRAME =================
    formData.append('resp_dataflow', document.getElementById('resp_data_flow_direction')?.value || '');
    formData.append('resp_frametype', document.getElementById('resp_frametype')?.value || 'RESPONSE');
    formData.append('resp_framesizeinbytes', document.getElementById('resp_framesizeinbytes')?.value || '0');
    formData.append('resp_noofrows', document.getElementById('resp_noofrows')?.value || '0');
    formData.append('resp_frametrigger', document.querySelector('#step-3 #frame_trigger_options')?.value || '');
    formData.append('resp_multipleui', document.getElementById('resp_multipleui')?.value || '');
    formData.append('resp_triggerui', document.getElementById('resp_triggerui')?.value || '');
    formData.append('resp_voicecommand', document.getElementById('resp_voicecommand')?.value || '');
    formData.append('resp_appnotification', document.getElementById('resp_appnotification')?.value || '');

   const respRows = document.querySelectorAll('#responseTableBody tr');
const respTableData = [];

const respFields = [
    'startBytePosition_resp[]', 'endBytePosition_resp[]', 'fieldDescription_resp[]',
    'numberOfBytes_resp[]', 'bitSize_resp[]', 'dataType_resp[]', 'valueDefinition_resp[]',
    'displayValueMin_resp[]', 'displayValueMax_resp[]', 'valueInHexMin_resp[]', 'valueInHexMax_resp[]',
    'uiType_resp[]', 'uiId_resp[]', 'uiPageNo_resp[]', 'resp_asperui[]', 'resp_sno_asperui[]',
    'resp_asperuivalue[]', 'uiDescriptionInOut_resp[]', 'sliderStepValue_resp[]', 'defaultvalue_resp[]',
    'developerNotes_resp[]', 'notifEnable_resp[]', 'notifMsg_resp[]'
];

respRows.forEach(row => {
    if (row.bitwiseData && row.bitwiseData.length > 0) {
        // Bitwise row — only push bitwise data
        respTableData.push({
            bitwiseData: row.bitwiseData
        });
    } else {
        // Normal bytewise row — dynamically gather all fields
        const rowData = { bitwiseData: [] }; // include bitwiseData key for consistency
        respFields.forEach(field => {
            // Remove [] from the name for the key
            const key = field.replace('[]', '');
            rowData[key] = row.querySelector(`[name="${field}"]`)?.value || '';
        });
        respTableData.push(rowData);
    }
});    


    // ================= STEP 4: ERROR FRAME =================
    formData.append('error_dataflow', document.getElementById('error_data_flow_direction')?.value || '');
    formData.append('error_frametype', document.getElementById('error_frametype')?.value || 'ERROR');
    formData.append('error_framesizeinbytes', document.getElementById('error_framesizeinbytes')?.value || '0');
    formData.append('error_noofrows', document.getElementById('error_noofrows')?.value || '0');
    formData.append('error_frametrigger', document.querySelector('#step-4 #frame_trigger_options')?.value || '');
    formData.append('error_multipleui', document.getElementById('error_multipleui')?.value || '');
    formData.append('error_triggerui', document.getElementById('error_triggerui')?.value || '');
    formData.append('error_voicecommand', document.getElementById('error_voicecommand')?.value || '');
    formData.append('error_appnotification', document.getElementById('error_appnotification')?.value || '');
    
   const errorRows = document.querySelectorAll('#errorTableBody tr');
const errorTableData = [];

const errorFields = [
    'startBytePosition_error[]', 'endBytePosition_error[]', 'fieldDescription_error[]',
    'numberOfBytes_error[]', 'bitSize_error[]', 'dataType_error[]', 'valueDefinition_error[]',
    'displayValueMin_error[]', 'displayValueMax_error[]', 'valueInHexMin_error[]', 'valueInHexMax_error[]',
    'uiType_error[]', 'uiId_error[]', 'uiPageNo_error[]', 'error_asperui[]', 'error_sno_asperui[]',
    'error_asperuivalue[]', 'uiDescriptionInOut_error[]', 'sliderStepValue_error[]', 'defaultvalue_error[]',
    'developerNotes_error[]', 'notifEnable_error[]', 'notifMsg_error[]'
];

errorRows.forEach(row => {
    if (row.bitwiseData && row.bitwiseData.length > 0) {
        // Bitwise row — only push bitwise data
        errorTableData.push({
            bitwiseData: row.bitwiseData
        });
    } else {
        // Normal bytewise row — dynamically gather all fields
        const rowData = { bitwiseData: [] }; // include bitwiseData key for consistency
        errorFields.forEach(field => {
            const key = field.replace('[]', '');
            rowData[key] = row.querySelector(`[name="${field}"]`)?.value || '';
        });
        errorTableData.push(rowData);
    }
});
    

    // ================= CLEAN TABLE DATA FOR JSON =================
    function cleanTableData(tableData) {
        return tableData.map(row => {
            const cleanRow = {};
            for (let key in row) {
                if (typeof row[key] !== 'function' && (typeof row[key] !== 'object' || Array.isArray(row[key]))) {
                    cleanRow[key] = row[key];
                } else {
                    cleanRow[key] = null;
                }
            }
            return cleanRow;
        });
    }

    const cleanedCmdTableData = cleanTableData(cmdTableData);
    const cleanedRespTableData = cleanTableData(respTableData);
    const cleanedErrorTableData = cleanTableData(errorTableData);

    // ================= STRUCTURED JSON =================
    const structuredData = {
        productInfo: { 
            brandname: document.querySelector('input[name="brandname"]')?.value || '',
            productname: document.querySelector('input[name="productname"]')?.value || '',
            modelname: document.querySelector('input[name="modelname"]')?.value || '',
            productdescription: document.getElementById('product_description')?.value || '',
            sequence: document.getElementById('Sequence')?.value || '',
            date: document.querySelector('input[name="date"]')?.value || '',
            pagenames: document.getElementById('pagenames')?.value || ''
        },
        commandFrames: cleanedCmdTableData.map((row, index) => ({ frameNo: index + 1, ...row })),
        responseFrames: cleanedRespTableData.map((row, index) => ({ frameNo: index + 1, ...row })),
        errorFrames: cleanedErrorTableData.map((row, index) => ({ frameNo: index + 1, ...row }))
    };

    formData.append('structured_frame_data', JSON.stringify(structuredData));

    // Add old raw frame data (optional, for backward compatibility)
    
    
// Replace old arrays with cleaned arrays
commandFrameData[currentCmdFrame] = cmdTableData;
responseFrameData[currentRespFrame] = respTableData;
errorFrameData[currentErrorFrame] = errorTableData;

// Now append to formData
formData.append('structured_frame_data', JSON.stringify({
    commandFrames: commandFrameData,
    responseFrames: responseFrameData,
    errorFrames: errorFrameData
}));




    // Show loading indicator
    const saveBtn = document.querySelector('.save-btn');
    const originalText = saveBtn ? saveBtn.textContent : 'Save';
    if (saveBtn) {
        saveBtn.textContent = 'Saving...';
        saveBtn.disabled = true;
    }

    // Send the data
    fetch('<?php echo get_uri("items/saveapi_edit"); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async response => {
        const text = await response.text();
        console.log('Response status:', response.status);
        console.log('Response:', text.substring(0, 500));
        
        try {
            return JSON.parse(text);
        } catch (e) {
            throw new Error('Server returned: ' + text.substring(0, 200));
        }
    })
    .then(data => {
        if (saveBtn) {
            saveBtn.textContent = originalText;
            saveBtn.disabled = false;
        }

       /* if (data && data.success) {
            forceMessage('✅ Configuration saved successfully!', 'success');
            if (data.redirect) {
                setTimeout(() => window.location.href = data.redirect, 1500);
            }
        } else {
            forceMessage('❌ Error: ' + (data?.message || 'Failed to save configuration'));
        }*/
        
        
        if (data && data.success) {
    forceMessage('✅ Configuration saved successfully!', 'success');
    
    // Check if the response contains HTML content to display
    if (data.html_content) {
        // Create a new window or div to display the HTML content
        const printWindow = window.open('', '_blank');
        printWindow.document.write(data.html_content);
        printWindow.document.close();
    } 
    // Check if there's print_r output to display
    else if (data.print_r_output) {
        // Create a new window with the print_r output
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Saved Configuration Data</title>
                <style>
                    body { 
                        font-family: monospace; 
                        padding: 20px; 
                        background: #f5f5f5;
                        white-space: pre-wrap;
                    }
                    pre { 
                        background: #272822; 
                        color: #f8f8f2; 
                        padding: 15px; 
                        border-radius: 5px;
                        overflow-x: auto;
                    }
                </style>
            </head>
            <body>
                <h2>Configuration Data</h2>
                <pre>${data.print_r_output}</pre>
                <button onclick="window.print()">Print</button>
                <button onclick="window.close()">Close</button>
            </body>
            </html>
        `);
        printWindow.document.close();
    }
    // Check if there's data to display
    else if (data.data) {
        // Create a new window with formatted JSON data
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Saved Configuration Data</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        padding: 20px; 
                        background: #f5f5f5;
                    }
                    pre { 
                        background: #272822; 
                        color: #f8f8f2; 
                        padding: 15px; 
                        border-radius: 5px;
                        overflow-x: auto;
                    }
                    .section {
                        margin-bottom: 20px;
                        background: white;
                        padding: 15px;
                        border-radius: 5px;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                    }
                    h2 { color: #667eea; }
                    button {
                        background: #667eea;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 5px;
                        cursor: pointer;
                        margin-right: 10px;
                    }
                    button:hover { background: #764ba2; }
                </style>
            </head>
            <body>
                <h1>✅ Configuration Saved Successfully</h1>
                <div class="section">
                    <h2>Item Data</h2>
                    <pre>${JSON.stringify(data.data.item_data || data.data, null, 2)}</pre>
                </div>
                ${data.data.command_data ? `
                <div class="section">
                    <h2>Command Frame Data</h2>
                    <pre>${JSON.stringify(data.data.command_data, null, 2)}</pre>
                </div>
                ` : ''}
                ${data.data.response_data ? `
                <div class="section">
                    <h2>Response Frame Data</h2>
                    <pre>${JSON.stringify(data.data.response_data, null, 2)}</pre>
                </div>
                ` : ''}
                ${data.data.error_data ? `
                <div class="section">
                    <h2>Error Frame Data</h2>
                    <pre>${JSON.stringify(data.data.error_data, null, 2)}</pre>
                </div>
                ` : ''}
                <div style="text-align: center; margin-top: 20px;">
                    <button onclick="window.print()">🖨️ Print</button>
                    <button onclick="window.close()">✖️ Close</button>
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
    }
    // If redirect exists, do redirect
    else if (data.redirect) {
        setTimeout(() => window.location.href = data.redirect, 1500);
    }
} else {
    forceMessage('❌ Error: ' + (data?.message || 'Failed to save configuration'));
}
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
    })
    .catch(error => {
        if (saveBtn) {
            saveBtn.textContent = originalText;
            saveBtn.disabled = false;
        }
        forceMessage('❌ Error: ' + error.message);
        console.error('Save error:', error);
    });

    return false;
}

/*************************************************/


/**
 * Helper to print data from memory in your specific format
 */

function printStoredFrame(rows, type, frameIdx) {
    // Converts 'Cmd' -> 'cmd', 'Resp' -> 'resp', etc.
    const sfx = type.toLowerCase(); 

    rows.forEach((row, rowIdx) => {
        const rowNo = rowIdx + 1;
        const prefix = `${type}[${frameIdx}]-row[${rowNo}]`;
        const isBitwise = row.bitwiseData && row.bitwiseData.length > 0;

        console.log(`%c ${prefix} [MODE: ${isBitwise ? 'BITWISE' : 'BYTEWISE'}] `, 'background: #333; color: #fff; font-weight: bold;');

        if (isBitwise) {
            // ==========================================
            // FULL BITWISE FIELD LIST
            // ==========================================
            row.bitwiseData.forEach((bit, bIdx) => {
                const bNo = bIdx + 1;
                const bPrefix = `${prefix}-bit[${bNo}]`;
                const s = "_" + bNo;
                
                console.log(`${bPrefix}-startBit${s} =>`, bit.startBit ?? "");
                console.log(`${bPrefix}-endBit${s} =>`, bit.endBit ?? "");
                console.log(`${bPrefix}-numberOfBits${s} =>`, bit.noOfBits ?? "");
                console.log(`${bPrefix}-dataType${s} =>`, bit.dataType ?? "");
                console.log(`${bPrefix}-valueDefinition${s} =>`, bit.valueDef ?? "");
                console.log(`${bPrefix}-displayValueMin${s} =>`, bit.displayMin ?? "");
                console.log(`${bPrefix}-displayValueMax${s} =>`, bit.displayMax ?? "");
                console.log(`${bPrefix}-valueInHexMin${s} =>`, bit.hexMin ?? "");
                console.log(`${bPrefix}-valueInHexMax${s} =>`, bit.hexMax ?? "");
                console.log(`${bPrefix}-uiType${s} =>`, bit.uiType ?? "");
                console.log(`${bPrefix}-uiId${s} =>`, bit.uiAsPer ?? ""); 
                console.log(`${bPrefix}-asperui${s} =>`, bit.uiId ?? "");   
                console.log(`${bPrefix}-uiPageNo${s} =>`, bit.uiLength ?? "");
                console.log(`${bPrefix}-uiDescriptionInOut${s} =>`, bit.uiInout ?? "");
                console.log(`${bPrefix}-sliderStepValue${s} =>`, bit.priority ?? "");
                console.log(`${bPrefix}-defaultvalue${s} =>`, bit.unit ?? "");
                console.log(`${bPrefix}-developerNotes${s} =>`, bit.description ?? "");
                console.log(`${bPrefix}-notifEnable${s} =>`, bit.notifEnable ?? "");
                console.log(`${bPrefix}-notifMsg${s} =>`, bit.notifMessage ?? "");
            });
        } else {
            // ==========================================
            // FULL BYTEWISE FIELD LIST
            // ==========================================
            console.log(`${prefix}-startBytePosition =>`, row[`startBytePosition_${sfx}[]`] || "");
            console.log(`${prefix}-endBytePosition =>`, row[`endBytePosition_${sfx}[]`] || "");
            console.log(`${prefix}-numberOfBytes =>`, row[`numberOfBytes_${sfx}[]`] || "");
           console.log(`${prefix}-bitSize =>`, row[`bitSize_${sfx}[]`] || "");
            
            // Note: In your code, fieldDescription often keeps the '_cmd[]' suffix in all tables
            console.log(`${prefix}-fieldDescription =>`, row[`fieldDescription_${sfx}[]`] || row[`fieldDescription_cmd[]`] || "");
            
            console.log(`${prefix}-dataType =>`, row[`dataType_${sfx}[]`] || "");
            console.log(`${prefix}-valueDefinition =>`, row[`valueDefinition_${sfx}[]`] || "");
            console.log(`${prefix}-displayValueMin =>`, row[`displayValueMin_${sfx}[]`] || "");
            console.log(`${prefix}-displayValueMax =>`, row[`displayValueMax_${sfx}[]`] || "");
            console.log(`${prefix}-valueInHexMin =>`, row[`valueInHexMin_${sfx}[]`] || "");
            console.log(`${prefix}-valueInHexMax =>`, row[`valueInHexMax_${sfx}[]`] || "");
            console.log(`${prefix}-uiType =>`, row[`uiType_${sfx}[]`] || "");
            console.log(`${prefix}-uiId =>`, row[`uiId_${sfx}[]`] || "");
            
            // UI Selection fields
            console.log(`${prefix}-asperui =>`, row[`${sfx}_asperui[]`] || "");
            console.log(`${prefix}-uiSerialNo =>`, row[`${sfx}_sno_asperui[]`] || "");
            console.log(`${prefix}-uiValues =>`, row[`${sfx}_asperuivalue[]`] || "");
            
            console.log(`${prefix}-uiPageNo =>`, row[`uiPageNo_${sfx}[]`] || "");
            console.log(`${prefix}-uiDescriptionInOut =>`, row[`uiDescriptionInOut_${sfx}[]`] || "");
            console.log(`${prefix}-sliderStepValue =>`, row[`sliderStepValue_${sfx}[]`] || "");
            console.log(`${prefix}-defaultvalue =>`, row[`defaultvalue_${sfx}[]`] || "");
            console.log(`${prefix}-developerNotes =>`, row[`developerNotes_${sfx}[]`] || "");
            
            // Notification fields
            console.log(`${prefix}-notificationEnable =>`, row[`notifEnable_${sfx}[]`] || "");
            console.log(`${prefix}-notificationMessage =>`, row[`notifMsg_${sfx}[]`] || "");
        }
        console.log("--------------------------------------------------");
    });
}

function readTableById(tableId) {
    // Ensure we add the # if it's missing
    const selector = tableId.startsWith('#') ? tableId : `#${tableId}`;
    const rows = document.querySelectorAll(`${selector} tr`);
    
    return Array.from(rows).map(row => {
        const rowData = {};
        const inputs = row.querySelectorAll("input, select");
        
        inputs.forEach(input => {
            if (input.name) {
                rowData[input.name] = input.value;
            }
        });

        // VERY IMPORTANT: Carry over the bitwise data object
        rowData.bitwiseData = row.bitwiseData || [];
        return rowData;
    });
}
function readMainTable() {
    const rows = document.querySelectorAll("#additionalTableBody tr");
    return Array.from(rows).map(row => {
        const rowData = {};
        
        // This finds every input and select in the current row
        const inputs = row.querySelectorAll("input, select");
        
        inputs.forEach(input => {
            // We use the 'name' attribute (e.g., "startBytePosition_cmd[]") as the key
            if (input.name) {
                rowData[input.name] = input.value;
            }
        });

        // Keep the bitwise data if it was already attached to the row
        rowData.bitwiseData = row.bitwiseData || [];
        return rowData;
    });
}
function changeErrorPage(targetPageIdx) {
    // STEP A: Save the data currently visible on the screen to the current Error index
    // Use the ID of your Error table body
    errorFrameData[currentErrorFrame] = readTableById("errorTableBody");

    // STEP B: Update the current index to the new page
    currentErrorFrame = targetPageIdx;

    // STEP C: Load the data for the new page from memory back into the table
    // This assumes you have a function to render the rows
    loadErrorViewTable(errorFrameData[currentErrorFrame]);
}
function changeResponsePage(targetPageIdx) {
    // STEP A: Save the data currently visible on the screen to Resp[current]
    responseFrameData[currentRespFrame] = readTableById("responseTableBody");

    // STEP B: Change the index
    currentRespFrame = targetPageIdx;

    // STEP C: Load the data for the new page into the table
    loadRespViewTable(responseFrameData[currentRespFrame]);
}

</script>
<script>
setTimeout(loadCmdViewTable, 300);

</script>


</body>
</html>
