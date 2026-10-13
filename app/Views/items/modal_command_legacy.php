<?php
date_default_timezone_set('Asia/Kolkata');
// Initialize variables to prevent undefined variable errors
// In a real application, these would be populated from a database or other source.
$productname = $productname ?? '';
$brandname = $brandname ?? '';
$modelname = $modelname ?? '';
$date = $date ?? date('Y-m-d'); // Default to current date
$numberofpages = $numberofpages ?? '';
$Sequence = $Sequence ?? '';
$pagenames = $pagenames ?? '';
$product_description = $product_description ?? '';

$cmd_parametername = $cmd_parametername ?? '';
$cmd_dataflow = $cmd_dataflow ?? '';
$cmd_frametype = $cmd_frametype ?? '';
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
$resp_frametype = $resp_frametype ?? '';
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
$error_frametype = $error_frametype ?? '';
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
        return "/{$path}"; // Simplified for example
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Multi-Step Form (Product + Command)</title>
  <style>
.table select {
    min-width: 140px;
    max-width: 180px;
}

  </style>
  

  <style>
    /* General styles */
    
    body {
      background: linear-gradient(135deg, #e0f7fa, #ffffff);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
    }
    /* .container {
      padding: 40px;
      max-width: 900px;
      margin: 50px auto;
      background: #ffffff;
      box-shadow: 0 15px 35px rgba(0,0,0,0.1);
      border-radius: 16px;
    } */
     .container {
  width: 100vw;
  min-height: 100vh;
  margin: 0;
  padding: 40px;
  background: #ffffff;
  box-shadow: none;
  border-radius: 0;
}

    h2 {
      text-align: center;
      color: #0056b3;
      font-size: 28px;
      margin-bottom: 30px;
    }
    h4 {
      text-align: center;
      color: green;
      margin-bottom: 20px;
    }

    /* Form step styles */
    .form-step {
      display: none; /* Hidden by default */
    }
    .form-step.active {
      display: block; /* Shown when active */
    }

    /* Button styles */
    .btn {
      padding: 12px 24px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      margin-top: 15px; /* Added for consistency */
    }
    .btn:hover {
      background-color: #0056b3;
    }

    /* Form group styles */
    .form-group {
      margin-bottom: 20px;
      display: flex; /* Use flexbox for label and input alignment */
      align-items: center; /* Vertically align items */
    }
    .form-group label {
      font-weight: 600;
      display: block;
      margin-bottom: 8px;
      flex: 0 0 150px; /* Fixed width for labels */
      margin-right: 20px; /* Space between label and input */
    }
    .form-group .col-sm-2, .form-group .col-sm-4 {
      flex-grow: 1; /* Allow input columns to take remaining space */
    }

    /* Input, select, textarea styles */
    input, select, textarea {
      width: 100%;
      padding: 14px;
      border-radius: 10px;
      border: 1px solid #ccc;
      background-color: #f9f9f9;
      font-size: 14px;
      transition: border-color 0.3s ease, background-color 0.3s ease;
    }
    input:focus, select:focus, textarea:focus {
      border-color: #007bff;
      background-color: #fff;
      outline: none;
    }
    textarea {
      resize: vertical; /* Allow vertical resizing */
    }

    /* Table styles */
    .table-responsive {
      overflow-x: auto;
      margin-top: 20px;
    }
    .table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 1rem;
      background-color: #fff;
    }
    .table th, .table td {
      padding: 0.75rem;
      vertical-align: top;
      border-top: 1px solid #dee2e6;
      white-space: nowrap; /* Prevent text wrapping in table headers/cells */
    }
    .table thead th {
      vertical-align: bottom;
      border-bottom: 2px solid #dee2e6;
    }
    .table tbody + tbody {
      border-top: 2px solid #dee2e6;
    }
    .table .form-control {
      padding: 0.5rem;
      font-size: 0.875rem;
    }
    .table small {
      font-size: 0.75rem;
      color: #dc3545; /* Red color for notes */
    }
    
  </style>
  <style>
/* ✅ Fullscreen fix */
body, html {
  margin: 0 !important;
  padding: 0 !important;
  width: 100vw !important;
  height: 100vh !important;
  overflow-x: hidden !important;
}

.general-form {
  width: 100vw !important;
  min-height: 100vh !important;
  background: #fff !important;
  padding: 40px !important;
  box-sizing: border-box !important;
  border-radius: 0 !important;
}

.form-step {
  width: 100% !important;
  min-height: 100vh !important;
}

.container, .container-fluid {
  max-width: 100% !important;
  width: 100% !important;
  padding: 0 20px !important;
  box-sizing: border-box !important;
}

/* ✅ Optional improvement */
.modal, .modal-content, .modal-dialog {
  width: 100% !important;
  max-width: 100% !important;
  height: 100vh !important;
  margin: 0 !important;
  border-radius: 0 !important;
  box-shadow: none !important;
}

.modal-backdrop {
  display: none !important;
}
</style>
</head>
<body>

<?php echo form_open_multipart(get_uri("items/saveapi_edit"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
<!-- <div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix"> -->
        <!-- <div class="container-fluid"> -->

            <!-- Step 1: Product Information -->
            <div id="step-1" class="form-step active">
                <h4 style="text-align: center; color: green;">PRODUCT  INFORMATION</h4>
                <br><br>
                <div class="commandname">
                    <div class="form-group">
                        <label class="col-sm-2" for="productname">Product Name:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="productname" value="<?php echo htmlspecialchars($productname); ?>" name="productname" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2" for="brandname">Brand Name:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="brandname" value="<?php echo htmlspecialchars($brandname); ?>" name="brandname" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2" for="modelname">Model Number:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="modelname" value="<?php echo htmlspecialchars($modelname); ?>" name="modelname" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2" for="date">Date:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="date" value="<?php echo htmlspecialchars($date); ?>" name="date">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2" for="numberofpages">Page Number:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="numberofpages" value="<?php echo htmlspecialchars($numberofpages); ?>" name="numberofpages">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2" for="Sequence">Sequence no:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="Sequence" value="<?php echo htmlspecialchars($Sequence); ?>" name="Sequence">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2" for="pagenames">Page names:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="pagenames" value="<?php echo htmlspecialchars($pagenames); ?>" name="pagenames">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2" for="product_description">Product Description:</label>
                        <div class="col-sm-4">
                            <textarea class="form-control" id="product_description" name="product_description" rows="4"><?php echo htmlspecialchars($product_description); ?></textarea>
                            <small class="text-muted">Max 200 words</small>
                        </div>
                    </div>
                    <button class="btn" type="button" onclick="nextStep()">Next</button>
                </div>
            </div>

            <!-- Step 2: Command Frame Information -->
            <div id="step-2" class="form-step">
                <div class="container">
                    <h4 style="text-align: center; color: green;">COMMAND FRAME</h4>
                    <h2>Command Frame Information</h2>
                    <br><br>
                    <div class="commandname">
                        <div class="form-group">
                            <label class="col-sm-2" for="cmd_parametername">Parameter Name:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="cmd_parametername" value="<?php echo htmlspecialchars($cmd_parametername); ?>" name="cmd_parametername" readonly>
                                <small class="text-muted" style="color: red !important;">Note: Please do not change the parameter name.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="cmd_data_flow_direction">Command Data Flow Direction:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="cmd_data_flow_direction" value="<?php echo htmlspecialchars($cmd_dataflow); ?>" name="cmd_data_flow_direction">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="cmd_frametype">Frame Type:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="cmd_frametype" name="cmd_frametype" value="<?php echo htmlspecialchars($cmd_frametype); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="cmd_framesizeinbytes">Frame size in bytes:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="cmd_framesizeinbytes" value="<?php echo htmlspecialchars($cmd_framesizeinbytes); ?>" name="cmd_framesizeinbytes">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="cmd_noofrows">Number of Rows:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="cmd_noofrows" value="<?php echo htmlspecialchars($cmd_noofrows); ?>" name="cmd_noofrows" oninput="updateTable()">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="cmd_frametrigger">Frame Trigger:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="cmd_frametrigger" value="<?php echo htmlspecialchars($cmd_frametrigger); ?>" name="cmd_frametrigger">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="cmd_multipleui">MultipleUI:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="cmd_multipleui" value="<?php echo htmlspecialchars($cmd_multipleui); ?>" name="cmd_multipleui">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="cmd_triggerui">Trigger UI:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="cmd_triggerui" value="<?php echo htmlspecialchars($cmd_triggerui); ?>" name="cmd_triggerui">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="cmd_voicecommand">Voice Command:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="cmd_voicecommand" name="cmd_voicecommand" value="<?php echo htmlspecialchars($cmd_voicecommand); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="cmd_voice_control_instruction_text">Voice control Instruction text:</label>
                            <div class="col-sm-2">
                                <input type="file" class="form-control" id="cmd_voice_control_instruction_text" name="cmd_voice_control_instruction_text" accept="image/*">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="cmd_appnotification">App Notification:</label>
                            <div class="col-sm-4">
                                <textarea class="form-control" id="cmd_appnotification" name="cmd_appnotification" rows="4"><?php echo htmlspecialchars($cmd_appnotification); ?></textarea>
                                <small class="text-muted">Max 200 words</small>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <div style="overflow-x: auto;">
                            <table id="additionalTable" class="table">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Start Byte position</th>
                                        <th>End Byte position</th>
                                        <th>Field Description</th>
                                        <th>Number of Bytes</th>
                                        <th>Bit Size</th>
                                        <th>Data Type</th>
                                        <th>Value Definition</th>
                                        <th>Display Value (Min)</th>
                                        <th>Display Value (Max)</th>
                                        <th>Value in Hex (Min)</th>
                                        <th>Value in Hex (Max)</th>
                                        <th>UI Type</th>
                                        <th>UI ID</th>
                                        <th>UI Page No</th>
                                        <th>AS PER UI</th>
                                        <th>Serial Number of UI<small style="color:red">(ENTER FROM ZERO(0))</small></th>
                                        <th>AS PER UI VALUES</th>
                                        <th>UI Description In/Out</th>
                                        <th>Slider Step Value</th>
                                        <th>Default Values</th>
                                        <th>Developer Notes (If Any)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for ($i = 0; $i < $cmd_noofrows; $i++) { ?>
                                        <tr id="row_<?php echo $i + 1; ?>">
                                            <td><?php echo $i + 1; ?></td>
                                            <td><input type="text" id="startBytePosition_cmd_<?php echo $i; ?>" name="startBytePosition_cmd[]" value="<?php echo htmlspecialchars($startBytePosition_cmd[$i] ?? ''); ?>"></td>
                                            <td><input type="text" id="endBytePosition_cmd_<?php echo $i; ?>" name="endBytePosition_cmd[]" value="<?php echo htmlspecialchars($endBytePosition_cmd[$i] ?? ''); ?>"></td>
                                            <td>
                                                <select id="fieldDescription_cmd_<?php echo $i; ?>" name="fieldDescription_cmd[]">
                                                    <?php
                                                    $options = ["None", "CommandId", "Function Code", "Checksum", "Terminator", "ActionId", "Data","DataSub Cmd","Error Code", "Operation Code", "Trigger"];
                                                    foreach ($options as $option) {
                                                        $selected = (($fieldDescription_cmd[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="text" id="numberOfBytes_cmd_<?php echo $i; ?>" name="numberOfBytes_cmd[]" value="<?php echo htmlspecialchars($numberOfBytes_cmd[$i] ?? ''); ?>"></td>
                                            <td><input type="text" id="bitSize_cmd_<?php echo $i; ?>" name="bitSize_cmd[]" value="<?php echo htmlspecialchars($bitSize_cmd[$i] ?? ''); ?>"></td>
                                            <td>
                                                <select id="dataType_cmd_<?php echo $i; ?>" name="dataType_cmd[]">
                                                    <?php
                                                    $options = ["None", "ASCII", "Binary", "SignedInt", "Float", "UnsignedInt", "String"];
                                                    foreach ($options as $option) {
                                                        $selected = (($dataType_cmd[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select id="valueDefinition_cmd_<?php echo $i; ?>" name="valueDefinition_cmd[]">
                                                    <?php
                                                    $options = ["None", "Fixed Value", "Range Value", "Mode Value", "ASCII Value"];
                                                    foreach ($options as $option) {
                                                        $selected = (($valueDefinition_cmd[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="text" id="displayValueMin_cmd_<?php echo $i; ?>" value="<?php echo htmlspecialchars($displayValueMin_cmd[$i] ?? ''); ?>" name="displayValueMin_cmd[]"></td>
                                            <td><input type="text" id="displayValueMax_cmd_<?php echo $i; ?>" value="<?php echo htmlspecialchars($displayValueMax_cmd[$i] ?? ''); ?>" name="displayValueMax_cmd[]"></td>
                                            <td><input type="text" id="valueInHexMin_cmd_<?php echo $i; ?>" value="<?php echo htmlspecialchars($valueInHexMin_cmd[$i] ?? ''); ?>" name="valueInHexMin_cmd[]"></td>
                                            <td><input type="text" id="valueInHexMax_cmd_<?php echo $i; ?>" value="<?php echo htmlspecialchars($valueInHexMax_cmd[$i] ?? ''); ?>" name="valueInHexMax_cmd[]"></td>
                                            <td>
                                                <select id="uiType_cmd_<?php echo $i; ?>" name="uiType_cmd[]">
                                                    <?php
                                                    $options = ["None", "Text", "Toggle Switch", "Notification", "console", "Enter Text", "Slider", "Brightness Slider", "Airflow Slider", "CCT Slider", "Saturation Slider", "Hue Slider", "Hue Slider Custom", "Hue Circle", "Push Button", "Drop Down", "Trigger", "Display", "Button", "Toast Display", "Auto Trigger with Timer"];
                                                    foreach ($options as $option) {
                                                        $selected = (($uiType_cmd[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="text" id="uiId_cmd_<?php echo $i; ?>" value="<?php echo htmlspecialchars($uiId_cmd[$i] ?? ''); ?>" name="uiId_cmd[]"></td>
                                            <td>
                                                <select id="uiPageNo_cmd_<?php echo $i; ?>" name="uiPageNo_cmd[]">
                                                    <?php
                                                    $options = ["None", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10"];
                                                    foreach ($options as $option) {
                                                        $selected = (($uiPageNo_cmd[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="text" id="cmd_asperui_<?php echo $i; ?>" value="<?php echo htmlspecialchars(implode(',', explode('][', trim($cmd_asperui[$i] ?? '', '[]')))); ?>" name="cmd_asperui[]"></td>
                                            <td><input type="text" id="cmd_sno_asperui_<?php echo $i; ?>" value="<?php echo htmlspecialchars(implode(',', explode('][', trim($cmd_sno_asperui[$i] ?? '', '[]')))); ?>" name="cmd_sno_asperui[]"></td>
                                            <td><input type="text" id="cmd_asperuivalue_<?php echo $i; ?>" value="<?php echo htmlspecialchars(implode(',', explode('][', trim($cmd_asperuivalue[$i] ?? '', '[]')))); ?>" name="cmd_asperuivalue[]"></td>
                                            <td>
                                                <select id="uiDescriptionInOut_cmd_<?php echo $i; ?>" name="uiDescriptionInOut_cmd[]">
                                                    <?php
                                                    $options = ["None", "Read/Write", "Read", "Write"];
                                                    foreach ($options as $option) {
                                                        $selected = (($uiDescriptionInOut_cmd[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="text" id="sliderStepValue_cmd_<?php echo $i; ?>" value="<?php echo htmlspecialchars($sliderStepValue_cmd[$i] ?? ''); ?>" name="sliderStepValue_cmd[]"></td>
                                            <td><input type="text" id="defaultvalue_cmd_<?php echo $i; ?>" value="<?php echo htmlspecialchars($defaultvalue_cmd[$i] ?? ''); ?>" name="defaultvalue_cmd[]"></td>
                                            <td><input type="text" id="developerNotes_cmd_<?php echo $i; ?>" value="<?php echo htmlspecialchars($developerNotes_cmd[$i] ?? ''); ?>" name="developerNotes_cmd[]"></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <button type="button" onclick="addRow()">+</button>
                        </div>
                    </div>
                </div>
                <button class="btn" type="button" onclick="prevStep()">Back</button>
                <button class="btn" type="button" onclick="nextStep()">Next</button>
            </div>

            <!-- Step 3: Response Frame Information -->
            <div id="step-3" class="form-step">
                <div class="container">
                    <h4 style="text-align: center; color: green;">RESPONSE FRAME</h4>
                    <h2>Response Frame Information</h2>
                    <br><br>
                    <div class="commandname">
                        <div class="form-group">
                            <label class="col-sm-2" for="resp_parametername">Parameter Name:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="resp_parametername" value="<?php echo htmlspecialchars($cmd_parametername); ?>" name="resp_parametername" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="resp_data_flow_direction">Response Data Flow Direction:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="resp_data_flow_direction" value="<?php echo htmlspecialchars($resp_dataflow); ?>" name="resp_data_flow_direction">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="resp_frametype">Frame Type:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="resp_frametype" name="resp_frametype" value="<?php echo htmlspecialchars($resp_frametype); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="resp_framesizeinbytes">Response Frame size in bytes:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="resp_framesizeinbytes" value="<?php echo htmlspecialchars($resp_framesizeinbytes); ?>" name="resp_framesizeinbytes">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="resp_noofrows">Response Frame no of rows:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="resp_noofrows" name="resp_noofrows" value="<?php echo htmlspecialchars($resp_noofrows); ?>" oninput="respupdateTable()">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="resp_frametrigger">Frame Trigger:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="resp_frametrigger" value="<?php echo htmlspecialchars($resp_frametrigger); ?>" name="resp_frametrigger">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="resp_multipleui">MultipleUI:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="resp_multipleui" value="<?php echo htmlspecialchars($resp_multipleui); ?>" name="resp_multipleui">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="resp_triggerui">Trigger UI:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="resp_triggerui" value="<?php echo htmlspecialchars($resp_triggerui); ?>" name="resp_triggerui">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="resp_voicecommand">Voice Command:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="resp_voicecommand" name="resp_voicecommand" value="<?php echo htmlspecialchars($resp_voicecommand); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="resp_voice_control_instruction_text">Voice control Instruction text:</label>
                            <div class="col-sm-2">
                                <input type="file" class="form-control" id="resp_voice_control_instruction_text" name="resp_voice_control_instruction_text" accept="image/*">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="resp_appnotification">App Notification:</label>
                            <div class="col-sm-4">
                                <textarea class="form-control" id="resp_appnotification" name="resp_appnotification" rows="4"><?php echo htmlspecialchars($resp_appnotification); ?></textarea>
                                <small class="text-muted">Max 200 words</small>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <div style="overflow-x: auto;">
                            <table id="responseTable" class="table">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Start Byte position</th>
                                        <th>End Byte position</th>
                                        <th>Field Description</th>
                                        <th>Number of Bytes</th>
                                        <th>Bit Size</th>
                                        <th>Data Type</th>
                                        <th>Value Definition</th>
                                        <th>Display Value (Min)</th>
                                        <th>Display Value (Max)</th>
                                        <th>Value in Hex (Min)</th>
                                        <th>Value in Hex (Max)</th>
                                        <th>UI Type</th>
                                        <th>UI ID</th>
                                        <th>UI Page No</th>
                                        <th>AS PER UI</th>
                                        <th>Serial Number of UI<small style="color:red">(ENTER FROM ZERO(0))</small></th>
                                        <th>AS PER UI VALUES</th>
                                        <th>UI Description In/Out</th>
                                        <th>Slider Step Value</th>
                                        <th>Default Values</th>
                                        <th>Developer Notes (If Any)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for ($i = 0; $i < $resp_noofrows; $i++) { ?>
                                        <tr id="row_resp_<?php echo $i + 1; ?>">
                                            <td><?php echo $i + 1; ?></td>
                                            <td><input type="text" id="startBytePosition_resp_<?php echo $i; ?>" name="startBytePosition_resp[]" value="<?php echo htmlspecialchars($startBytePosition_resp[$i] ?? ''); ?>"></td>
                                            <td><input type="text" id="endBytePosition_resp_<?php echo $i; ?>" name="endBytePosition_resp[]" value="<?php echo htmlspecialchars($endBytePosition_resp[$i] ?? ''); ?>"></td>
                                            <td>
                                                <select id="fieldDescription_resp_<?php echo $i; ?>" name="fieldDescription_resp[]">
                                                    <?php
                                                    $options = ["None", "CommandId", "Function Code", "Checksum", "Terminator", "ActionId", "Data","DataSub Cmd", "Error Code", "Operation Code", "Trigger"];
                                                    foreach ($options as $option) {
                                                        $selected = (($fieldDescription_resp[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="text" id="numberOfBytes_resp_<?php echo $i; ?>" name="numberOfBytes_resp[]" value="<?php echo htmlspecialchars($numberOfBytes_resp[$i] ?? ''); ?>"></td>
                                            <td><input type="text" id="bitSize_resp_<?php echo $i; ?>" name="bitSize_resp[]" value="<?php echo htmlspecialchars($bitSize_resp[$i] ?? ''); ?>"></td>
                                            <td>
                                                <select id="dataType_resp_<?php echo $i; ?>" name="dataType_resp[]">
                                                    <?php
                                                    $options = ["None", "ASCII", "Binary", "SignedInt", "Float", "UnsignedInt", "String"];
                                                    foreach ($options as $option) {
                                                        $selected = (($dataType_resp[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select id="valueDefinition_resp_<?php echo $i; ?>" name="valueDefinition_resp[]">
                                                    <?php
                                                    $options = ["None", "Fixed Value", "Range Value", "Mode Value", "ASCII Value"];
                                                    foreach ($options as $option) {
                                                        $selected = (($valueDefinition_resp[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="text" id="displayValueMin_resp_<?php echo $i; ?>" value="<?php echo htmlspecialchars($displayValueMin_resp[$i] ?? ''); ?>" name="displayValueMin_resp[]"></td>
                                            <td><input type="text" id="displayValueMax_resp_<?php echo $i; ?>" value="<?php echo htmlspecialchars($displayValueMax_resp[$i] ?? ''); ?>" name="displayValueMax_resp[]"></td>
                                            <td><input type="text" id="valueInHexMin_resp_<?php echo $i; ?>" value="<?php echo htmlspecialchars($valueInHexMin_resp[$i] ?? ''); ?>" name="valueInHexMin_resp[]"></td>
                                            <td><input type="text" id="valueInHexMax_resp_<?php echo $i; ?>" value="<?php echo htmlspecialchars($valueInHexMax_resp[$i] ?? ''); ?>" name="valueInHexMax_resp[]"></td>
                                            <td>
                                                <select id="uiType_resp_<?php echo $i; ?>" name="uiType_resp[]">
                                                    <?php
                                                    $options = ["None", "Text", "Toggle Switch", "Notification", "console", "Enter Text", "Slider", "Brightness Slider", "Airflow Slider", "CCT Slider", "Saturation Slider", "Hue Slider", "Hue Circle", "Hue Slider Custom", "Push Button", "Drop Down", "Trigger", "Display", "Button", "Toast Display", "Auto Trigger with Timer"];
                                                    foreach ($options as $option) {
                                                        $selected = (($uiType_resp[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="text" id="uiId_resp_<?php echo $i; ?>" value="<?php echo htmlspecialchars($uiId_resp[$i] ?? ''); ?>" name="uiId_resp[]"></td>
                                            <td>
                                                <select id="uiPageNo_resp_<?php echo $i; ?>" name="uiPageNo_resp[]">
                                                    <?php
                                                    $options = ["None", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10"];
                                                    foreach ($options as $option) {
                                                        $selected = (($uiPageNo_resp[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="text" id="resp_asperui_<?php echo $i; ?>" value="<?php echo htmlspecialchars(implode(',', explode('][', trim($resp_asperui[$i] ?? '', '[]')))); ?>" name="resp_asperui[]"></td>
                                            <td><input type="text" id="resp_sno_asperui_<?php echo $i; ?>" value="<?php echo htmlspecialchars(implode(',', explode('][', trim($resp_sno_asperui[$i] ?? '', '[]')))); ?>" name="resp_sno_asperui[]"></td>
                                            <td><input type="text" id="resp_asperuivalue_<?php echo $i; ?>" value="<?php echo htmlspecialchars(implode(',', explode('][', trim($resp_asperuivalue[$i] ?? '', '[]')))); ?>" name="resp_asperuivalue[]"></td>
                                            <td>
                                                <select id="uiDescriptionInOut_resp_<?php echo $i; ?>" name="uiDescriptionInOut_resp[]">
                                                    <?php
                                                    $options = ["None", "Read/Write", "Read", "Write"];
                                                    foreach ($options as $option) {
                                                        $selected = (($uiDescriptionInOut_resp[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="text" id="sliderStepValue_resp_<?php echo $i; ?>" value="<?php echo htmlspecialchars($sliderStepValue_resp[$i] ?? ''); ?>" name="sliderStepValue_resp[]"></td>
                                            <td><input type="text" id="defaultvalue_resp_<?php echo $i; ?>" value="<?php echo htmlspecialchars($defaultvalue_resp[$i] ?? ''); ?>" name="defaultvalue_resp[]"></td>
                                            <td><input type="text" id="developerNotes_resp_<?php echo $i; ?>" value="<?php echo htmlspecialchars($developerNotes_resp[$i] ?? ''); ?>" name="developerNotes_resp[]"></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <button type="button" onclick="respaddRow()">+</button>
                        </div>
                    </div>
                </div>
                <button class="btn" type="button" onclick="prevStep()">Back</button>
                <button class="btn" type="button" onclick="nextStep()">Next</button>
            </div>

            <!-- Step 4: Error Frame Information -->
            <div id="step-4" class="form-step">
                <div class="container">
                    <h4 style="text-align: center; color: green;">ERROR FRAME</h4>
                    <h2>Error Frame Information</h2>
                    <br><br>
                    <div class="commandname">
                        <div class="form-group">
                            <label class="col-sm-2" for="error_parametername">Parameter Name:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="error_parametername" name="error_parametername" value="<?php echo htmlspecialchars($cmd_parametername); ?>" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="error_data_flow_direction">Error Data Flow Direction:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="error_data_flow_direction" name="error_data_flow_direction" value="<?php echo htmlspecialchars($error_dataflow); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="error_frametype">Frame Type:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="error_frametype" name="error_frametype" value="<?php echo htmlspecialchars($error_frametype); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="error_framesizeinbytes">Error Frame size in bytes:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="error_framesizeinbytes" value="<?php echo htmlspecialchars($error_framesizeinbytes); ?>" name="error_framesizeinbytes">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="error_noofrows">Error Frame no of rows:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="error_noofrows" value="<?php echo htmlspecialchars($error_noofrows); ?>" name="error_noofrows" oninput="errorupdateTable()">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="error_frametrigger">Frame Trigger:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="error_frametrigger" name="error_frametrigger" value="<?php echo htmlspecialchars($error_frametrigger); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="error_multipleui">MultipleUI:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="error_multipleui" value="<?php echo htmlspecialchars($error_multipleui); ?>" name="error_multipleui">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="error_triggerui">Trigger UI:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="error_triggerui" value="<?php echo htmlspecialchars($error_triggerui); ?>" name="error_triggerui">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="error_voicecommand">Voice Command:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="error_voicecommand" name="error_voicecommand" value="<?php echo htmlspecialchars($error_voicecommand); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="error_voice_control_instruction_text">Voice control Instruction text:</label>
                            <div class="col-sm-2">
                                <input type="file" class="form-control" id="error_voice_control_instruction_text" name="error_voice_control_instruction_text" accept="image/*">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2" for="error_appnotification">App Notification:</label>
                            <div class="col-sm-4">
                                <textarea class="form-control" id="error_appnotification" name="error_appnotification" rows="4"><?php echo htmlspecialchars($error_appnotification); ?></textarea>
                                <small class="text-muted">Max 200 words</small>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <div style="overflow-x: auto;">
                            <table id="errorTable" class="table">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Start Byte position</th>
                                        <th>End Byte position</th>
                                        <th>Field Description</th>
                                        <th>Number of Bytes</th>
                                        <th>Bit Size</th>
                                        <th>Data Type</th>
                                        <th>Value Definition</th>
                                        <th>Display Value (Min)</th>
                                        <th>Display Value (Max)</th>
                                        <th>Value in Hex (Min)</th>
                                        <th>Value in Hex (Max)</th>
                                        <th>UI Type</th>
                                        <th>UI ID</th>
                                        <th>UI Page No</th>
                                        <th>AS PER UI</th>
                                        <th>Serial Number of UI<small style="color:red">(ENTER FROM ZERO(0))</small></th>
                                        <th>AS PER UI VALUES</th>
                                        <th>UI Description In/Out</th>
                                        <th>Slider Step Value</th>
                                        <th>Default Values</th>
                                        <th>Developer Notes (If Any)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for ($i = 0; $i < $error_noofrows; $i++) { ?>
                                        <tr id="row_error_<?php echo $i + 1; ?>">
                                            <td><?php echo $i + 1; ?></td>
                                            <td><input type="text" id="startBytePosition_error_<?php echo $i; ?>" name="startBytePosition_error[]" value="<?php echo htmlspecialchars($startBytePosition_error[$i] ?? ''); ?>"></td>
                                            <td><input type="text" id="endBytePosition_error_<?php echo $i; ?>" name="endBytePosition_error[]" value="<?php echo htmlspecialchars($endBytePosition_error[$i] ?? ''); ?>"></td>
                                            <td>
                                                <select id="fieldDescription_error_<?php echo $i; ?>" name="fieldDescription_error[]">
                                                    <?php
                                                    $options = ["None", "CommandId", "Function Code", "Checksum", "Terminator", "ActionId", "Data","DataSub Cmd","Error Code", "Operation Code", "Trigger"];
                                                    foreach ($options as $option) {
                                                        $selected = (($fieldDescription_error[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="text" id="numberOfBytes_error_<?php echo $i; ?>" name="numberOfBytes_error[]" value="<?php echo htmlspecialchars($numberOfBytes_error[$i] ?? ''); ?>"></td>
                                            <td><input type="text" id="bitSize_error_<?php echo $i; ?>" name="bitSize_error[]" value="<?php echo htmlspecialchars($bitSize_error[$i] ?? ''); ?>"></td>
                                            <td>
                                                <select id="dataType_error_<?php echo $i; ?>" name="dataType_error[]">
                                                    <?php
                                                    $options = ["None", "ASCII", "Binary", "SignedInt", "Float", "UnsignedInt", "String"];
                                                    foreach ($options as $option) {
                                                        $selected = (($dataType_error[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select id="valueDefinition_error_<?php echo $i; ?>" name="valueDefinition_error[]">
                                                    <?php
                                                    $options = ["None", "Fixed Value", "Range Value", "Mode Value", "ASCII Value"];
                                                    foreach ($options as $option) {
                                                        $selected = (($valueDefinition_error[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="text" id="displayValueMin_error_<?php echo $i; ?>" name="displayValueMin_error[]" value="<?php echo htmlspecialchars($displayValueMin_error[$i] ?? ''); ?>"></td>
                                            <td><input type="text" id="displayValueMax_error_<?php echo $i; ?>" name="displayValueMax_error[]" value="<?php echo htmlspecialchars($displayValueMax_error[$i] ?? ''); ?>"></td>
                                            <td><input type="text" id="valueInHexMin_error_<?php echo $i; ?>" name="valueInHexMin_error[]" value="<?php echo htmlspecialchars($valueInHexMin_error[$i] ?? ''); ?>"></td>
                                            <td><input type="text" id="valueInHexMax_error_<?php echo $i; ?>" name="valueInHexMax_error[]" value="<?php echo htmlspecialchars($valueInHexMax_error[$i] ?? ''); ?>"></td>
                                            <td>
                                                <select id="uiType_error_<?php echo $i; ?>" name="uiType_error[]">
                                                    <?php
                                                    $options = ["None", "Text", "Toggle Switch", "Notification", "console", "Enter Text", "Slider", "Brightness Slider", "Airflow Slider", "CCT Slider", "Saturation Slider", "Hue Slider Custom", "Hue Slider", "Hue Circle", "Push Button", "Drop Down", "Trigger", "Display", "Button", "Toast Display", "Auto Trigger with Timer"];
                                                    foreach ($options as $option) {
                                                        $selected = (($uiType_error[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="text" id="uiId_error_<?php echo $i; ?>" value="<?php echo htmlspecialchars($uiId_error[$i] ?? ''); ?>" name="uiId_error[]"></td>
                                            <td>
                                                <select id="uiPageNo_error_<?php echo $i; ?>" name="uiPageNo_error[]">
                                                    <?php
                                                    $options = ["None", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10"];
                                                    foreach ($options as $option) {
                                                        $selected = (($uiPageNo_error[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="text" id="error_asperui_<?php echo $i; ?>" value="<?php echo htmlspecialchars(implode(',', explode('][', trim($error_asperui[$i] ?? '', '[]')))); ?>" name="error_asperui[]"></td>
                                            <td><input type="text" id="error_sno_asperui_<?php echo $i; ?>" value="<?php echo htmlspecialchars(implode(',', explode('][', trim($error_sno_asperui[$i] ?? '', '[]')))); ?>" name="error_sno_asperui[]"></td>
                                            <td><input type="text" id="error_asperuivalue_<?php echo $i; ?>" value="<?php echo htmlspecialchars(implode(',', explode('][', trim($error_asperuivalue[$i] ?? '', '[]')))); ?>" name="error_asperuivalue[]"></td>
                                            <td>
                                                <select id="uiDescriptionInOut_error_<?php echo $i; ?>" name="uiDescriptionInOut_error[]">
                                                    <?php
                                                    $options = ["None", "Read/Write", "Read", "Write"];
                                                    foreach ($options as $option) {
                                                        $selected = (($uiDescriptionInOut_error[$i] ?? '') == $option) ? "selected" : "";
                                                        echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input type="text" id="sliderStepValue_error_<?php echo $i; ?>" name="sliderStepValue_error[]" value="<?php echo htmlspecialchars($sliderStepValue_error[$i] ?? ''); ?>"></td>
                                            <td><input type="text" id="defaultvalue_error_<?php echo $i; ?>" value="<?php echo htmlspecialchars($defaultvalue_error[$i] ?? ''); ?>" name="defaultvalue_error[]"></td>
                                            <td><input type="text" id="developerNotes_error_<?php echo $i; ?>" name="developerNotes_error[]" value="<?php echo htmlspecialchars($developerNotes_error[$i] ?? ''); ?>"></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <button type="button" onclick="erroraddRow()">+</button>
                        </div>
                    </div>
                </div>
                <button class="btn" type="button" onclick="prevStep()">Back</button>
                <center><button type="submit" class="btn">Save</button></center>
             
            </div>

        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
    let currentStep = 1;

    function showStep(step) {
        document.querySelectorAll('.form-step').forEach(div => div.classList.remove('active'));
        const targetStep = document.getElementById('step-' + step);
        if (targetStep) {
            targetStep.classList.add('active');
            currentStep = step;
            scrollToActiveStep();
            syncParameterNameAcrossSteps();
        }
    }

    function nextStep() {
        if (currentStep < 4) showStep(currentStep + 1);
    }

    function prevStep() {
        if (currentStep > 1) showStep(currentStep - 1);
    }

    function scrollToActiveStep() {
        const activeStep = document.querySelector('.form-step.active');
        const container = document.querySelector('#page-content') || document.querySelector('.modal-body');

        if (activeStep && container) {
            const offsetTop = activeStep.offsetTop;
            container.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    }

    function syncParameterNameAcrossSteps() {
        const mainInput = document.getElementById("cmd_parametername");
        const targetIDs = ["resp_parametername", "error_parametername"];
        if (!mainInput) return;
        const value = mainInput.value;
        targetIDs.forEach(id => {
            const input = document.getElementById(id);
            if (input) input.value = value;
        });
    }

    function checkWordCount(textarea) {
        const maxWords = 300;
        const words = textarea.value.trim().split(/\s+/).filter(w => w.length > 0);
        if (words.length > maxWords) {
            textarea.value = words.slice(0, maxWords).join(" ");
            alert("Maximum word limit exceeded. Content has been truncated.");
        }
    }

    function showImagePreview(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        const reader = new FileReader();
        reader.onloadend = () => {
            preview.src = reader.result;
            preview.style.display = 'block';
        };
        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    }

    function validateForm() {
        const modelName = document.getElementById('modelname');
        if (modelName && modelName.value.trim() === '') {
            alert('Please enter the Model Name.');
            return false;
        }
        return true;
    }

    document.addEventListener("DOMContentLoaded", function () {
        showStep(1);
        syncParameterNameAcrossSteps();

        const mainInput = document.getElementById("cmd_parametername");
        if (mainInput) mainInput.addEventListener("input", syncParameterNameAcrossSteps);

        const dateField = document.getElementById("date");
        if (dateField && !dateField.value) {
            dateField.value = new Date().toISOString().split('T')[0];
        }

        const pagenamesInput = document.getElementById("pagenames");
        if (pagenamesInput) {
            pagenamesInput.addEventListener("blur", function () {
                if (this.value.trim().toLowerCase() === "page0") {
                    alert("Page0 as Page name is invalid.");
                    this.value = "";
                    this.focus();
                }
            });
        }

        const textareas = ["product_description", "cmd_appnotification", "resp_appnotification", "error_appnotification"];
        textareas.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener("input", () => checkWordCount(el));
        });

        const form = document.getElementById('file-form');
        if (form) form.onsubmit = validateForm;
    });

    function saveForm() {
        $('#myModal').modal('hide');
    }
</script>

<?php echo form_close(); ?>