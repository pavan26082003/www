<?php
date_default_timezone_set('Asia/Kolkata');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Multi-Step Form (Product + Command)</title>
  <style>
     
    .form-step { display: none; }
    .form-step.active { display: block; }
    .btn { margin-top: 15px; padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; }
    .btn:hover { background: #0056b3; }

    body {
      background: linear-gradient(135deg, #e0f7fa, #ffffff);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
    }
    .form-step { display: none; }
    .form-step.active { display: block; }
    .btn {
      padding: 12px 24px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .btn:hover {
      background-color: #0056b3;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .container {
      padding: 40px;
      max-width: 900px;
      margin: 50px auto;
      background: #ffffff;
      box-shadow: 0 15px 35px rgba(0,0,0,0.1);
      border-radius: 16px;
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
    }
    input:focus, select:focus, textarea:focus {
      border-color: #007bff;
      background-color: #fff;
      outline: none;
    }
    h2 {
      text-align: center;
      color: #0056b3;
      font-size: 28px;
      margin-bottom: 30px;
    }
  </style>
</head>
<body>

<!-- Step 1: Product Information -->


<?php echo form_open(get_uri("items/saveapi_legacy"), array("id" => "file-form", "class" => "general-form")); ?>

<div id="step-1" class="form-step active">
  <div class="container">
    <h2>PRODUCT INFORMATION</h2>

     
<div class="form-group">
  <label for="par_parametername">Parameter Name</label>
  
  <input type="text" name="cmd_parametername" id="par_parametername"
         placeholder="Enter parameter name" oninput="validateParamLive()" />

  <!-- Hidden list from PHP -->
  <input type="hidden" id="default-param-list" value='<?= json_encode($paramNames ?? []); ?>'>

  <!-- Duplicate check error -->
  <small id="duplicate-error" style="color:red; display:none;">
    ⚠️ Parameter already exists for this model!
  </small>

  <!-- Special character error -->
  <small id="char-error" style="color:red; display:none;">
    ❌ Special characters like ! @ # $ % ^ & * - + = / are not allowed (except _ and space)
  </small>
</div>




    <div class="form-group">
      <label for="productname">Product Name</label>
     <!-- <input type="text" name="productname" value="Fan/Cooker" readonly />
    </div>-->
    <input type="text" class="form-control" value="<?php echo $category; ?>" id="productname" name="productname" readonly>

 
    
                            <div class="form-group">
                                <label class="col-sm-2" for="brandname">Brand Name:<span style="color: red;">*</span></label>
                                <small>Ex : Efactor</small>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control"  value="<?php echo $brand; ?>" id="brandname" name="brandname" readonly>
                                </div>
                            </div>
      <div class="form-group">
                                <label class="col-sm-2" for="modelname">Model Name:<span style="color: red;">*</span></label>
                                <small>Ex : Efact V1.0</small>
                                <div class="col-sm-2">
                                    <p class="error product-error"></p>
                                   <input type="text" class="form-control" value="<?= esc($model_name) ?>" id="modelname" name="modelname" readonly>
                                </div>
                            </div>

                          


    
<label for="date">Date:</label>
    <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" />

    <div class="form-group">
      <label for="numberofpages">Page Number</label>
      <input type="text" name="numberofpages" placeholder="e.g., 1" />
    </div>

    <div class="form-group">
      <label for="Sequence">Sequence No</label>
      <input type="text" name="Sequence" placeholder="e.g., 1" />
    </div>

    <div class="form-group">
      <label for="pagenames">Page Names</label>
      <input type="text" name="pagenames" placeholder="e.g., Fan Page" />
    </div>

    <!-- <div class="form-group">
      <label for="brandlogo">Brand Logo</label>
      <input type="file" name="brandlogo" accept="image/*" />
    </div> -->

    <div class="form-group">
      <label for="productdescription">Product Description</label>
      <textarea name="productdescription" rows="4" placeholder="Describe the product (max 300 words)..."></textarea>
    </div>

    <button class="btn" type="button" onclick="nextStep()" >Next</button>
  </div>
</div>

      </select>
    </div>

<!-- Step 2: Command Frame -->

<div id="step-2" class="form-step">
  <div class="container">

    <input type="hidden" name="id" value="<?php echo $id; ?>" />

    <h2>COMMAND FRAME</h2>

    <div class="form-group">
      <!-- <label for="cmd_parametername">Parameter Name</label>
      <input type="text" name="cmd_parametername"id="com_parametername"  /> -->
      <label for="cmd_parametername">Parameter Name</label>
      <input type="text" name="cmd_parametername"id="com_parametername"  />
      <!-- <input type="text" name="cmd_parametername" id="par_parametername" onblur="checkDuplicateParameter()" />
<input type="hidden" id="modelname" value="<?= esc($model_name ?? '') ?>"> -->

    </div>

    <div class="form-group">
      <label for="cmd_data_flow_direction">Data Flow Direction</label>
      <select name="cmd_data_flow_direction">
        <option value="App/Server to Device">App/Server to Device</option>
        <option value="Device to App/Server">Device to App/Server</option>
      </select>
    </div>

      <div class="form-group">
                    <label class="col-sm-2" for="pwd">Frame Type</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="cmd_frametype"
                            name="cmd_frametype">
                            <option value="COMMAND" selected>COMMAND</option>
                            <option value="RESPONSE">RESPONSE</option>
                            <option value="ERROR">ERROR</option>
                        </select>
                    </div>
                </div>

  
   <div class="form-group">
    <label class=" col-sm-2" for="pwd">Frame size in bytes</label>
    <small>Ex : 1,2,3,4....</small>
    <div class="col-sm-2">
        <input type="text" class="form-control"
            id="cmd_framesizeinbytes" name="cmd_framesizeinbytes" oninput="updateTable()" />
    </div>
</div>

<div class="form-group">
    <label class=" col-sm-2" for="pwd">No of rows</label>
    <small>Ex : 1,2,3,4....</small>
    <div class="col-sm-2">
        <input type="text" class="form-control"
            id="cmd_noofrows" name="cmd_noofrows" oninput="updateTable()" />
    </div>
</div>

                                                
    
    <div class="form-group">
                                                    <label class="col-sm-2" for="pwd">Frame Trigger</label>
                                                    <div class="col-sm-4">
                                                        <select class="form-control" id="cmd_frametrigger"
                                                            name="cmd_frametrigger">
                                                            <option value="None" selected>None</option>
                                                            <option value="INCOMING FRAME">INCOMING FRAME</option>
                                                            <option value="Toggle Switch">Toggle Switch</option>
                                                             <option value="Notification">Notification</option>
                                                             '<option value="console">console</option>'+
                                '<option value="Enter Text">Enter Text</option>'+
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
                                                            <option value="Button">Button</option>
                                                            <option value="Auto Trigger With Timer">Auto Trigger With Timer</option>
                                                            <option value="Toast Display">Toast Display</option>

                                                        </select>
                                                    </div>
                                                </div>

  
   <div class="form-group">
        <label for="cmd_multipleui">Multiple UI</label>
        <select name="cmd_multipleui" id="cmd_multipleui" onchange="showUImessage()">
            <option value="" disabled selected></option>
            <option value="false">false</option>
            <option value="true">true</option>
        </select>
        
        <!-- Note line now properly aligned to the left -->
        <div class="note-text">
            Note: (Single UI has value False and Multiple UI has value True)
        </div>
    </div>
      <style>
        .note-text {
           color: green;
            font-size: small;}
      </style>

    <div class="form-group">
      <label for="cmd_triggerui">Trigger UI</label>
      <select name="cmd_triggerui">
        <option value="false">false</option>
        <option value="true">true</option>
      </select>
    </div>

    <div class="form-group">
      <label for="cmd_voicecommand">Voice Command</label>
      <input type="text" name="cmd_voicecommand" placeholder="Yes/No" />
    </div>
    <div class="form-group">
                                                    <label class=" col-sm-2" for="pwd">Voice control Instruction
                                                        text</label>
                                                    <small>Ex : turn on Fan</small>
                                                    <div class="col-sm-2">
                                                        <input type="text" class="form-control"
                                                            id="cmd_voice_control_instruction_text"
                                                            name="cmd_voice_control_instruction_text">
                                                    </div>
                                                </div>


                                                <div class="form-group">
                                                    <label class="col-sm-2" for="productdescription">Remarks (if
                                                        any)</label>
                                                    <div class="col-sm-10">
                                                        <textarea class="form-control" id="cmd_appnotification"
                                                            placeholder="Product Description" name="cmd_appnotification"
                                                            rows="4"></textarea>
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
          <th>AS PER UI</th>
          <th>Serial Number of UI <small style="color:red">(ENTER FROM ZERO(0))</small></th>
          <th>AS PER UI VALUES</th>
          <th>UI Page No</th>
          <th>UI Description In/Out</th>
          <th>Slider Step Value</th>
          <th>Default Value</th>
          <th>Developer Notes (If Any)</th>
        </tr>
      </thead>
      <tbody id="additionalTableBody">
        <!-- Initially empty -->
      </tbody>
    </table>
     <!-- <button type="button" onclick="addRow()">+</button> -->
  </div>
</div>
<div>

                                                           <button type="button" onclick="addRow()">+</button>
         </div>                                                 
    <button class="btn" type="button" onclick="prevStep()">Back</button>
    <button class="btn" type="button" onclick="nextStep()">Next</button>
</div>
  </div>


<!-- Step 3: Response Frame -->
<div id="step-3" class="form-step">
<div class="container">
  <div class="form-group">
   
    <style>
        /* General container styling */
        .frame-card {
background: #fff;
padding: 30px;
border-radius: 10px;
box-shadow: 0 0 15px rgba(0,0,0,0.1);
max-width: 900px;
margin: 20px auto;
}

/* Don't repeat these styles again inside */
.inner-frame-card {
padding: 0;  /* Or minimal padding if needed */
box-shadow: none;
border-radius: 0;
background: none;
}

.post-dropzone {
background-color: #ffffff;
border: 1px solid #dee2e6;
padding: 30px;
border-radius: 10px;
box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
}

/* Headings */
.post-dropzone h4 {
font-size: 30px;
color:  #0056b3;
text-align: center;
margin-bottom: 20px;
font-weight: bold;
}

.post-dropzone h2 {
font-size: 22px;
color: #333;
font-weight: 600;
margin-bottom: 25px;
}

/* Form labels and inputs */
.form-group {
margin-bottom: 20px;
}

.form-group label {
font-weight: 500;
color: #333;
}

.form-control {
border-radius: 5px;
border: 1px solid #ccc;
padding: 8px 12px;
font-size: 14px;
}

/* Small help text */
small {
display: block;
margin-top: 4px;
color: #777;
}

/* Table styling */
.table-responsive table {
width: 100%;
border-collapse: collapse;
margin-top: 20px;
background-color: #fff;
}

.table-responsive th,
.table-responsive td {
border: 1px solid #ccc;
padding: 8px;
font-size: 13px;
text-align: left;
}

.table-responsive th {
background-color: #f5f5f5;
font-weight: 600;
}

.table-responsive input,
.table-responsive select {
width: 100%;
border-radius: 4px;
border: 1px solid #ccc;
padding: 6px 10px;
font-size: 13px;
}

/* Add Row button */
button[type="button"] {
margin-top: 15px;
padding: 8px 14px;
background-color: #28a745;
color: white;
border: none;
font-weight: 500;
border-radius: 5px;
cursor: pointer;
}

button[type="button"]:hover {
background-color: #218838;
}

/* Textarea */
textarea.form-control {
resize: vertical;
min-height: 100px;
}

/* Responsive fix for small screens */
@media (max-width: 768px) {
.form-group.row > label {
text-align: left;
margin-bottom: 5px;
}

.form-group.row {
flex-direction: column;
}
}

        </style>
           <!-- <input type="hidden" name="id" value="<?php echo $id; ?>"-->
            

            <h4 style="text-align: center;font-size: 30px; color:#0056b3;">
                Response
                FRAME
            </h4>


        


            <br><br>
            <div class="commandname">



                <div class="form-group">
                    <label class=" col-sm-2" for="resp_parametername">Parameter Name</label>
                    <small>Ex : speed,power.....</small>
                    <div class="col-sm-4">
                        <p class="error product-error"></p>
                        <input type="text" class="form-control"
                            id="resp_parametername" name="cmd_parametername"/>
                    </div>
                </div>



                <div class="form-group">
                    <label class="col-sm-2" for="pwd">Response Data Flow
                        Direction</label>
                    <div class="col-sm-4">
                        <select class="form-control"
                            id="resp_data_flow_direction"
                            name="resp_data_flow_direction">
                            <option value="App/Server to Device">App/Server to
                                Device
                            </option>
                            <option value="Device to App/Server">Device to
                                App/Server
                            </option>
                        </select>
                    </div>
                </div>




                <div class="form-group">
                    <label class="col-sm-2" for="pwd">Frame Type</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="resp_frametype"
                            name="resp_frametype">
                            <option value="COMMAND">COMMAND</option>
                            <option value="RESPONSE" selected>RESPONSE</option>
                            <option value="ERROR">ERROR</option>
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <label class=" col-sm-2" for="pwd">Response Frame size in
                        bytes</label>
                    <small>Ex : 1,2,3,4....</small>
                    <div class="col-sm-2">
                        <input type="text" class="form-control"
                            id="resp_framesizeinbytes"
                            name="resp_framesizeinbytes">
                    </div>
                </div>



                <div class="form-group">
                    <label class=" col-sm-2" for="pwd">Response No of
                        rows</label>
                    <small>Ex : 1,2,3,4....</small>
                    <div class="col-sm-2">
                        <input type="text" class="form-control"
                            id="resp_noofrows" name="resp_noofrows"
                            oninput="respupdateTable()">
                    </div>
                </div>
           
                <div class="form-group">
                    <label class="col-sm-2" for="pwd">Frame Trigger</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="resp_frametrigger"
                            name="resp_frametrigger">
                            <option value="None" selected>None</option>
                            <option value="INCOMING FRAME">INCOMING FRAME
                            </option>
                            <option value="Toggle Switch">Toggle Switch</option>
                            <option value="Slider">Slider</option>
                            <option value="Brightness Slider">Brightness Slider
                            </option>
                            <option value="Airflow Slider">Airflow Slider</option>
                            <option value="CCT Slider">CCT Slider</option>
                            <option value="Saturation Slider">Saturation Slider
                            </option>
                            <option value="Hue Slider">Hue Slider</option>
                            <option value="Hue Slider Custom">Hue Slider Custom</option>
                            <option value="Hue Circle">Hue Circle</option>
                            <option value="Push Button">Push Button (Big)
                            </option>
                            <option value="Drop Down">Drop Down</option>
                            <option value="Trigger">Trigger</option>
                            <option value="Display">Display</option>
                            <option value="Button">Button</option>
        <option value="Auto Trigger With Timer">Auto Trigger With Timer</option>
        <option value="Toast Display">Toast Display</option>
                        </select>
                    </div>
                </div>



              
        <div class="form-group row">
  <label class="col-sm-2 col-form-label" for="resp_multipleui">Multiple UI</label>
  
  <div class="col-sm-4">
    <select class="form-control" id="resp_multipleui" name="resp_multipleui">
      <option value="" disabled selected></option>
      <option value="false">false</option>
      <option value="true">true</option>
    </select>
  </div>

  <!-- force full width below -->
  <div class="col-sm-12" style="margin-top: 8px;">
    <small style="color: green;">
      Note: (Single UI has value False and Multiple UI has value True)
    </small>
  </div>
</div>





             <div class="form-group">
            <label class="col-sm-2" for="pwd">Trigger UI</label>
            <div class="col-sm-4">
                <select class="form-control" id="resp_triggerui" name="resp_triggerui">
                    <option value="false">false</option>
                    <option value="true">true</option>
                    
                </select>
            </div>
        </div>





                <div class="form-group">
                    <label class=" col-sm-2" for="pwd">Voice Command</label>
                    <small>Ex : Yes/No</small>
                    <div class="col-sm-2">
                        <input type="text" class="form-control"
                            id="resp_voicecommand" name="resp_voicecommand">
                    </div>
                </div>

                <div class="form-group">
                    <label class=" col-sm-2" for="pwd">Voice control Instruction
                        text</label>
                    <small>Ex : turn on Fan</small>
                    <div class="col-sm-2">
                        <input type="text" class="form-control"
                            id="resp_voice_control_instruction_text"
                            name="resp_voice_control_instruction_text">
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2" for="productdescription">Remarks (if
                        any)</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="resp_appnotification"
                            placeholder="Product Description"
                            name="resp_appnotification" rows="4"
                            oninput="checkWordCount(this)"></textarea>
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
                                <th>AS PER UI</th>
                                <th>Serial Number of UI<small style="color:red">(ENTER FROM ZERO(0))</small></th>
                                <th>AS PER UI VALUES</th>
                                <th>UI Page No</th>
                                <th>UI Description In/Out</th>
                                <th>Slider Step Value</th>
                                <th>Default Value</th>
                                <th>Notification</th>
                        <th>Notification Data</th>
                                <th>Developer Notes (If Any)</th>
                            </tr>
                        </thead>
                        <tbody id="responseTableBody">
    <!-- Rows will be dynamically inserted here -->
</tbody>
                    </table>
<tr>
  
                   </tr>
               <!--     <button type="button" onclick="addRow('responseTable')">+</button>-->
                       <button type="button" onclick="respaddRow()">+</button>
               <br><br>
                </div>
            </div>
        </div>
                    <button class="btn"type="button" onclick="prevStep()">Back</button>
                   <button class="btn" type="button" onclick="nextStep()" >Next</button>
  </div>
</div>
  <!-- Step 4: Error Frame -->
  <div id="step-4" class="form-step">
    <div class="container">
  <style>
    /* General Form Styling */
    /* For form card container */
  .modal-body {
      max-width: 800px;
      margin: 0 auto;
      width: 100%;
      background-color: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
  }
  
  /* Prevent infinite shrinking on nested containers */
  .modal-body * {
      max-width: 100%;
      box-sizing: border-box;
  }
  
  /* Optional: Remove weird inline/flex shrinkage */
  .form-group {
      flex-wrap: wrap;
      flex: 1 1 100%;
  }
  
  .modal-body {
      padding: 20px;
      background-color:white;
  }
  
  h4, h2 {
      text-align: center;
      color:  #0056b3;
      margin-bottom: 20px;
  }
  
  /* Form Labels and Inputs */
  .form-group {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      flex-wrap: wrap;
  }
  
  .form-group label {
      font-weight: 500;
      margin-bottom: 5px;
      width: 200px;
  }
  
  .form-group small {
      font-size: 11px;
      color: #888;
      margin-top: 4px;
      margin-left: 200px;
  }
  
  .form-group .form-control {
      min-width: 250px;
      flex-grow: 1;
      margin-top: 5px;
  }
  
  /* Textarea */
  textarea.form-control {
      width: 100%;
      resize: vertical;
  }
  
 
  /* Table Styling */
  .table-responsive {
      margin-top: 30px;
      overflow-x: auto;
  }
  
  table.table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
      background-color: #fff;
  }
  
  table.table th,
  table.table td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: left;
      min-width: 120px;
      vertical-align: middle;
  }
  
  table.table input,
  table.table select {
      width: 100%;
      padding: 4px;
      font-size: 13px;
  }
  
  /* + Button Styling */
  button[type="button"] {
      background-color: #007bff;
      border: none;
      padding: 6px 12px;
      font-size: 16px;
      color: white;
      border-radius: 4px;
      margin-top: 15px;
      cursor: pointer;
  }
  
  button[type="button"]:hover {
      background-color: #0056b3;
  }
  
  /* Scroll Fix on Smaller Screens */
  @media screen and (max-width: 768px) {
      .form-group {
          flex-direction: column;
          align-items: flex-start;
      }
  
      .form-group label {
          width: 100%;
      }
  
      .form-group small {
          margin-left: 0;
      }
  
      table.table th,
      table.table td {
          min-width: 150px;
      }
  }
  
    </style>
                                                
                                                               <div class="container">
  <div class="responsename">
   <div class="form-group">
                                                           
                                            
  <div style="text-align: center;">
  <h4 style="font-size: 30px; color: #0056b3; display: inline-block;">ERROR FRAME</h4>

</div>

  
                                                              <br><br>
                                                              <div class="commandname">
  
  
  
                                                                  <div class="form-group">
                                                                      <label class=" col-sm-2" for="error_parametername">Parameter
                                                                          Name</label>
                                                                      <small>Ex : Speed,Power..</small>
                                                                     <!-- <div class="col-sm-4">-->
                                                                          <p class="error product-error"></p>
                                                                          <input type="text" class="form-control"
                                                                              id="error_parametername"
                                                                              name="cmd_parametername"/>
                                                                      </div>
                                                                  </div>
  
  
                                                                  <div class="form-group">
                                                                      <label class="col-sm-2" for="pwd">Error Data Flow
                                                                          Direction</label>
                                                                      <div class="col-sm-4">
                                                                          <select class="form-control"
                                                                              id="error_data_flow_direction"
                                                                              name="error_data_flow_direction">
                                                                              <option value="App/Server to Device">
                                                                                  App/Server
                                                                                  to
                                                                                  Device</option>
                                                                              <option value="Device to App/Server">Device
                                                                                  to
                                                                                  App/Server</option>
                                                                          </select>
                                                                      </div>
                                                                  </div>
  
  
  
  
                                                                  <div class="form-group">
                                                                      <label class="col-sm-2" for="pwd">Frame Type</label>
                                                                      <div class="col-sm-4">
                                                                          <select class="form-control"
                                                                              id="error_frametype" name="error_frametype">
                                                                              <option value="COMMAND">COMMAND</option>
                                                                              <option value="RESPONSE">RESPONSE</option>
                                                                              <option value="ERROR" selected>ERROR
                                                                              </option>
                                                                          </select>
                                                                      </div>
                                                                  </div>
  
  
                                                            
                                                                 <div class="form-group">
                                                                    <label class=" col-sm-2" for="pwd">Error Frame size
                                                                        in
                                                                        bytes</label>
                                                                    <small>Ex : 1,2,3,4....</small>
                                                                    <div class="col-sm-2">
                                                                        <input type="text" class="form-control"
                                                                            id="error_framesizeinbytes"
                                                                            name="error_framesizeinbytes">
                                                                    </div>
                                                                </div>



                                                                <div class="form-group">
                                                                    <label class=" col-sm-2" for="pwd">Error No of
                                                                        rows</label>
                                                                    <small>Ex : 1,2,3,4....</small>
                                                                    <div class="col-sm-2">
                                                                        <input type="text" class="form-control"
                                                                            id="error_noofrows" name="error_noofrows"
                                                                            oninput="errorupdateTable()">
                                                                            
                                                                    </div>
                                                                </div>

  
    
  
  
  
  
  
                                                                  <div class="form-group">
                                                                      <label class="col-sm-2" for="pwd">Frame
                                                                          Trigger</label>
                                                                      <div class="col-sm-4">
                                                                          <select class="form-control"
                                                                              id="error_frametrigger"
                                                                              name="error_frametrigger">
                                                                              <option value="None" selected>None</option>
                                                                              <option value="INCOMING FRAME">INCOMING
                                                                                  FRAME
                                                                              </option>
                                                                              <option value="Toggle Switch">Toggle Switch
                                                                              </option>
                                                                              <option value="Notification">Notification</option>
                                                                              <option value="console">console</option>
                                                                          <option value="Enter Text">Enter Text</option>
                                                                              <option value="Slider">Slider</option>
                                                                              <option value="Brightness Slider">Brightness
                                                                                  Slider
                                                                              </option>
                                                                              <option value="Airflow Slider">Airflow Slider</option>
                                                                              <option value="CCT Slider">CCT Slider
                                                                              </option>
                                                                              <option value="Saturation Slider">Saturation
                                                                                  Slider
                                                                              </option>
                                                                              <option value="Hue Slider">Hue Slider
                                                                              </option>
                                                                              <option value="Hue Slider Custom">Hue Slider Custom</option>
                                                                              <option value="Hue Circle">Hue Circle
                                                                              </option>
                                                                              <option value="Push Button">Push Button
                                                                                  (Big)
                                                                              </option>
                                                                              <option value="Drop Down">Drop Down</option>
                                                                              <option value="Trigger">Trigger</option>
                                                                              <option value="Display">Display</option>
                                                                              <option value="Button">Button</option>
                                                           <option value="Auto Trigger With Timer">Auto Trigger With Timer</option>
                                                           <option value="Toast Display">Toast Display</option>
  
                                                                          </select>
                                                                      </div>
                                                                  </div>
  
  
                                                                  <div class="form-group">
                                                      <label class="col-sm-2" for="pwd">Multiple UI</label>
                                                      <div class="col-sm-4">
                                                          <select class="form-control" id="error_multipleui"
                                                              name="error_multipleui">
                                                              <option value="" disabled selected></option>
                                                              <option value="false">false</option>
                                                              <option value="true">true</option>
                                                              
                                                          </select>
                                                           <div class="note-text">
            Note: (Single UI has value False and Multiple UI has value True)
        </div>
    </div>
      <style>
        .note-text {
           color: green;
            font-size: small;}
      </style>

                                                      <!-- </div>
                                                  </div>
   -->
  
  
  
  
  
                                                          <div class="form-group">
                                                      <label class="col-sm-2" for="pwd">Trigger UI</label>
                                                      <div class="col-sm-4">
                                                          <select class="form-control" id="error_triggerui" name="error_triggerui">
                                                              <option value="false">false</option>
                                                              <option value="true">true</option>
                                                              
                                                          </select>
                                                      </div>
                                                  </div>
  
  
  
  
  
                                                                  <div class="form-group">
                                                                      <label class=" col-sm-2" for="pwd">Voice
                                                                          Command</label>
                                                                      <small>Ex : Yes/No</small>
                                                                      <div class="col-sm-2">
                                                                          <input type="text" class="form-control"
                                                                              id="error_voicecommand"
                                                                              name="error_voicecommand">
                                                                      </div>
                                                                  </div>
  
                                                                  <div class="form-group">
                                                                      <label class=" col-sm-2" for="pwd">Voice control
                                                                          Instruction
                                                                          text</label>
                                                                      <small>Ex : Turn on Fan</small>
                                                                      <div class="col-sm-2">
                                                                          <input type="text" class="form-control"
                                                                              id="error_voice_control_instruction_text"
                                                                              name="error_voice_control_instruction_text">
                                                                      </div>
                                                                  </div>
  
  
                                                                  <div class="form-group">
                                                                      <label class="col-sm-2"
                                                                          for="productdescription">Remarks
                                                                          (if
                                                                          any)</label>
                                                                      <div class="col-sm-10">
                                                                          <textarea class="form-control"
                                                                              id="error_appnotification"
                                                                              name="error_appnotification"
                                                                              rows="4"></textarea>
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
                                                                                  <th>AS PER UI</th>
                                                                                  <th>Serial Number of UI<small style="color:red">(ENTER FROM ZERO(0))</small></th>
                                                                                  <th>AS PER UI VALUES</th>
                                                                                  <th>UI Page No</th>
                                                                                  <th>UI Description In/Out</th>
                                                                                  <th>Slider Step Value</th>
                                                                                  <th>Default Value</th>
                                                                                  <th>Developer Notes (If Any)</th>
                                                                              </tr>
                                                                          </thead>
                                                                        
                                                                          <tbody id="errorTableBody">
    <!-- Rows will be dynamically inserted here -->
</tbody>
                                                                      </table>
                                                                      <tr>
                                        
                                                         <!--    <button type="button" onclick="addRow('errot')">+</button>-->
                                                          
                                                                    <button type="button" onclick="erroraddRow()">+</button>
  
                                                                  </div>
                                                              </div>
                                                          </div>
  
  
       
      <button class="btn"type="button" onclick="prevStep()">Back</button>
    
    <center>  <button type="submit" class="btn">Save</button>
      </form>
    </div>
  </div>
 
<script>
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
    console.error("❌ JSON Parse Error:", e);
  }

  const entered = input.value.trim();
  const enteredLower = entered.toLowerCase();
  const exists = paramList.some(p => p.toLowerCase() === enteredLower);

  // Special characters check (only allows letters, numbers, space, underscore)
  const invalidChar = /[^a-zA-Z0-9_ ]/.test(entered);

  // Handle duplicate error
  if (entered && exists) {
    dupError.style.display = "block";
    input.style.border = "1px solid red";
  } else {
    dupError.style.display = "none";
  }

  // Handle character error
  if (entered && invalidChar) {
    charError.style.display = "block";
    input.style.border = "1px solid red";
  } else {
    charError.style.display = "none";
  }

  // Reset border if no errors
  if (!exists && !invalidChar) {
    input.style.border = "";
  }
}
</script>

 
<script>
  function showUImessage() {
    const select = document.getElementById('cmd_multipleui');
    const message = document.getElementById('ui-message');
    const value = select.value;

    if (value === "true") {
      message.textContent = "Multiple UI selected (set to true)";
    } else if (value === "false") {
      message.textContent = "Single UI selected (set to false)";
    } else {
      message.textContent = "";
    }
  }

  // Optional: Show initial message if preselected
  window.onload = showUImessage;
</script>
  <script>
    

        function validatePageName() {
            var input = document.getElementById("pagenames").value;
            if (input === "Page0") {
                alert("Page0 as Page name is invalid");
            }
        }
    
    function scrollToActiveStep() {
      const activeStep = document.querySelector('.form-step.active');
      if (activeStep) {
        activeStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }



function syncParameterNameToAllSteps() {
  const mainInput = document.getElementById("par_parametername");
  const targetIDs = ["com_parametername", "resp_parametername", "error_parametername"];

  if (!mainInput) return;

  mainInput.addEventListener("input", function () {
    const value = mainInput.value;
    targetIDs.forEach(id => {
      const input = document.getElementById(id);
      if (input) input.value = value;
    });
  });
}

</script>
    <script>
  document.addEventListener("DOMContentLoaded", function () {
  syncParameterNameToAllSteps();
    const field = document.getElementById("currentDate");
    if (field && !field.value) {
      field.value = new Date().toISOString().split('T')[0];
    }
  });

    function scrollToActiveStep() {
      const activeStep = document.querySelector('.form-step.active');
      if (activeStep) {
        activeStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }

</script>
   

                    <script type="text/javascript">
 


  document.getElementById('file-form').onsubmit = function() {
        var modelName = document.getElementById('modelname').value;
        if (modelName.trim() === '') {
            alert('Please enter the Model Name.');
            return false; 
        }
        return true; 
    };
    
    function changeImage(input) {
        var file = input.files[0];
        var reader = new FileReader();
        
        reader.onload = function(e) {
            var img = document.getElementById('imageUpload-preview');
            img.src = e.target.result;
            img.style.display = 'block';
        };
        
        if (file) {
            reader.readAsDataURL(file);
        }
    }


 
function removeRow(button) {
    var row = button.parentNode.parentNode;
    row.parentNode.removeChild(row);
}

                        function addRow() {
    var table = document.getElementById("additionalTable");
    var index = table.rows.length; 
                            var newRow = table.insertRow(-1);  
                            var cells = [
                                '<td>' + index + '</td>', // S.No
                                '<td><input type="text" name="startBytePosition_cmd_' + index + '"></td>',
                                '<td><input type="text" name="endBytePosition_cmd_' + index + '"></td>',
                                '<td><select name="fieldDescription_cmd_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="CommandId">CommandId</option>' +
                                '<option value="Function Code">Function Code</option>' +
                                '<option value="Checksum">Checksum</option>' +
                                '<option value="Terminator">Terminator</option>' +
                                '<option value="ActionId">ActionId</option>' +
                                '<option value="Data">Data</option>' +
                                '<option value="DataSub Cmd">DataSub Cmd</option>' +
                                '<option value="Error Code">Error Code</option>' +
                                '<option value="Error details">Error details</option>'+
                                '<option value="Operation Code">Operation Code</option>' +
                                '<option value="Trigger">Trigger</option>' +
                                '</select></td>',
                                '<td><input type="text" name="numberOfBytes_cmd_' + index + '"></td>',
                                '<td><input type="text" name="bitSize_cmd_' + index + '"></td>',

                                '<td><select name="dataType_cmd_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="ASCII">ASCII</option>' +
                                '<option value="Binary">Binary</option>' +
                                '<option value="SignedInt">Signed Int</option>' +
                                '<option value="Float">Float</option>' +
                                '<option value="UnsignedInt">Unsigned Int</option>' +
                                '<option value="String">String</option>'+
                                '</select></td>',


                                '<td><select name="valueDefinition_cmd_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Fixed Value">Fixed Value</option>' +
                                '<option value="Range Value">Range Value</option>' +
                                '<option value="Mode Value">Mode Value</option>' +
                                '<option value="ASCII">ASCII</option>' +
                                '</select></td>',


                                '<td><input type="text" name="displayValueMin_cmd_' + index + '"></td>',
                                '<td><input type="text" name="displayValueMax_cmd_' + index + '"></td>',
                                '<td><input type="text" name="valueInHexMin_cmd_' + index + '"></td>',
                                '<td><input type="text" name="valueInHexMax_cmd_' + index + '"></td>',
                                '<td><select name="uiType_cmd_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Text">Text</option>' +
                                '<option value="Toggle Switch">Toggle Switch</option>' +
                                '<option value="Notification">Notification</option>'+ 
                                '<option value="console">console</option>'+
                                '<option value="Enter Text">Enter Text</option>'+
                                '<option value="Slider">Slider</option>' +
                                '<option value="Brightness Slider">Brightness Slider</option>' +
                                '<option value="Airflow Slider">Airflow Slider</option>' +
                                '<option value="CCT Slider">CCT Slider</option>' +
                                '<option value="Saturation Slider">Saturation Slider</option>' +
                                '<option value="Hue Slider">Hue Slider</option>' +
                                '<option value="Hue Slider Custom">Hue Slider Custom</option>' +
                                '<option value="Hue Circle">Hue Circle</option>' +
                                '<option value="Push Button">Push Button (Big)</option>' +
                                '<option value="Drop Down">Drop Down</option>' +
                                '<option value="Trigger">Trigger</option>' +
                                '<option value="Display">Display</option>' +
                                '<option value="Button">Button (Big)</option>' +
                                '<option value="Auto Trigger With Timer">Auto Trigger With Timer</option>'+
                                '<option value="Toast Display">Toast Display</option>'+


                                '</select></td>',
                                '<td><input type="text" name="uiId_cmd_' + index + '"></td>',
                                '<td><input type="text" name="asperui_cmd_' + index + '"></td>',
                                '<td><input type="text" name="sno_asperui_cmd' + index + '"></td>',
                                '<td><input type="text" name="asperuivalue_cmd_' + index + '"></td>',




                                '<td><select name="uiPageNo_cmd_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="0">0</option>' +
                                '<option value="1">1</option>' +
                                '<option value="2">2</option>' +
                                '<option value="3">3</option>' +
                                '<option value="4">4</option>' +
                                '<option value="5">5</option>' +
                                '<option value="6">6</option>' +
                                '<option value="7">7</option>' +
                                '<option value="8">8</option>' +
                                '<option value="9">9</option>' +
                                '<option value="10">10</option>' +
                                '</select></td>',




                                '<td><select name="uiDescriptionInOut_cmd_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Read/Write">Read/Write</option>' +
                                '<option value="Read">Read</option>' +
                                '<option value="Write">Write</option>' +
                                '</select></td>',


                                '<td><input type="text" name="sliderStepValue_cmd_' + index + '"></td>',
                                '<td><input type="text" name="defaultvalue_cmd_' + index + '"></td>',
                                '<td><input type="text" name="developerNotes_cmd_' + index + '"></td>'
                            ];
                              

                            // Insert cells into the new row
                            for (var i = 0; i < cells.length; i++) {
                                var cell = newRow.insertCell(i);
                                cell.innerHTML = cells[i];
                            }

                            // Add a remove button to delete the row if needed
                            var removeCell = newRow.insertCell(-1);
                            removeCell.innerHTML = '<button onclick="removeRow(this)">Remove</button>';
                        }




                        var debounceTimer;

                        function updateTable() {
                            clearTimeout(debounceTimer);

                            debounceTimer = setTimeout(function () {
                                var frameSizeInput = document.getElementById('cmd_noofrows');
                                var frameSize = parseInt(frameSizeInput.value) || 0; // Get the value or default to 0 if empty or NaN
                                var table = document.getElementById('additionalTable');

                                // Clear previous rows
                                while (table.rows.length > 1) {
                                    table.deleteRow(1);
                                }

                                // Add new rows
                                for (var i = 0; i < Math.max(1, frameSize); i++) {
                                    addRow(i + 1);
                                }
                            }, 500);
                        }

 

                        /*********response table*******************************************/
 function respaddRow() {
    var table = document.getElementById("responseTable");
    var index = table.rows.length;  
                            var newRow = table.insertRow(-1); // Insert at the end of the table
                            var cells = [
                                '<td>' + index + '</td>', // S.No
                                '<td><input type="text" name="startBytePosition_resp_' + index + '"></td>',
                                '<td><input type="text" name="endBytePosition_resp_' + index + '"></td>',
                                '<td><select name="fieldDescription_resp_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="CommandId">CommandId</option>' +
                                '<option value="Function Code">Function Code</option>' +
                                '<option value="Checksum">Checksum</option>' +
                                '<option value="Terminator">Terminator</option>' +
                                '<option value="ActionId">ActionId</option>' +
                                '<option value="Data">Data</option>' +
                                 '<option value="DataSub Cmd">DataSub Cmd</option>' +
                                '<option value="Error Code">Error Code</option>' +
                                '<option value="Error details">Error details</option>'+
                                '<option value="Operation Code">Operation Code</option>' +
                                '<option value="Trigger">Trigger</option>' +
                                '</select></td>',
                                '<td><input type="text" name="numberOfBytes_resp_' + index + '"></td>',
                                '<td><input type="text" name="bitSize_resp_' + index + '"></td>',
                                '<td><select name="dataType_resp_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="ASCII">ASCII</option>' +
                                '<option value="Binary">Binary</option>' +
                                '<option value="SignedInt">Signed Int</option>' +
                                '<option value="Float">Float</option>' +
                                '<option value="UnsignedInt">Unsigned Int</option>' +
                                '<option value="String">String</option>'+
                                '</select></td>',
                                

                                '<td><select name="valueDefinition_resp_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Fixed Value">Fixed Value</option>' +
                                '<option value="Range Value">Range Value</option>' +
                                '<option value="Mode Value">Mode Value</option>' +
                                '<option value="ASCII">ASCII</option>' +
                                '</select></td>',


                                '<td><input type="text" name="displayValueMin_resp_' + index + '"></td>',
                                '<td><input type="text" name="displayValueMax_resp_' + index + '"></td>',
                                '<td><input type="text" name="valueInHexMin_resp_' + index + '"></td>',
                                '<td><input type="text" name="valueInHexMax_resp_' + index + '"></td>',
                                '<td><select name="uiType_resp_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Text">Text</option>' +
                                '<option value="Toggle Switch">Toggle Switch</option>' +
                                '<option value="Notification">Notification</option>'+
                                '<option value="console">console</option>'+
                                '<option value="Enter Text">Enter Text</option>'+
                                '<option value="Slider">Slider</option>' +
                                '<option value="Brightness Slider">Brightness Slider</option>' +
                                '<option value="Airflow Slider">Airflow Slider</option>' +
                                '<option value="CCT Slider">CCT Slider</option>' +
                                '<option value="Saturation Slider">Saturation Slider</option>' +
                                '<option value="Hue Slider">Hue Slider</option>' +
                                '<option value="Hue Slider Custom">Hue Slider Custom</option>' +
                                '<option value="Hue Circle">Hue Circle</option>' +
                                '<option value="Push Button">Push Button (Big)</option>' +
                                '<option value="Drop Down">Drop Down</option>' +
                                '<option value="Trigger">Trigger</option>' +
                                '<option value="Display">Display</option>' +
                                '<option value="Button">Button (Big)</option>' +
                                '<option value="Toast Display">Toast Display</option>'+
                                '</select></td>',


                                '<td><input type="text" name="uiId_resp_' + index + '"></td>',
                                '<td><input type="text" name="asperui_resp_' + index + '"></td>',
                                '<td><input type="text" name="sno_asperui_resp' + index + '"></td>',
                                '<td><input type="text" name="asperuivalue_resp_' + index + '"></td>',




                                '<td><select name="uiPageNo_resp_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="0">0</option>' +
                                '<option value="1">1</option>' +
                                '<option value="2">2</option>' +
                                '<option value="3">3</option>' +
                                '<option value="4">4</option>' +
                                '<option value="5">5</option>' +
                                '<option value="6">6</option>' +
                                '<option value="7">7</option>' +
                                '<option value="8">8</option>' +
                                '<option value="9">9</option>' +
                                '<option value="10">10</option>' +
                                '</select></td>',



                                '<td><select name="uiDescriptionInOut_resp_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Read/Write">Read/Write</option>' +
                                '<option value="Read">Read</option>' +
                                '<option value="Write">Write</option>' +
                                '</select></td>',


                                '<td><input type="text" name="sliderStepValue_resp_' + index + '"></td>',
                                '<td><input type="text" name="defaultvalue_resp_' + index + '"></td>',
                                '<td><input type="text" name="developerNotes_resp_' + index + '"></td>'
                            ];
                             

                            // Insert cells into the new row
                            for (var i = 0; i < cells.length; i++) {
                                var cell = newRow.insertCell(i);
                                cell.innerHTML = cells[i];
                            }

                            // Add a remove button to delete the row if needed
                            var removeCell = newRow.insertCell(-1);
                            removeCell.innerHTML = '<button onclick="removeRow(this)">Remove</button>';
                        }




                        function respupdateTable() {
                            clearTimeout(debounceTimer);

                            debounceTimer = setTimeout(function () {
                                var frameSizeInput = document.getElementById('resp_noofrows');
                                var frameSize = parseInt(frameSizeInput.value) || 0; // Get the value or default to 0 if empty or NaN
                                var table = document.getElementById('responseTable');

                                // Clear previous rows
                                while (table.rows.length > 1) {
                                    table.deleteRow(1);
                                }

                                // Add new rows
                                for (var i = 0; i < Math.max(1, frameSize); i++) {
                                     respaddRow(i + 1);
                                }
                            }, 500);
                        }



                        /**************error table******************************************/
 function erroraddRow() {
    var table = document.getElementById("errorTable");
    var index = table.rows.length;  
                            var newRow = table.insertRow(-1); // Insert at the end of the table
                            var cells = [
                                '<td>' + index + '</td>', // S.No
                                '<td><input type="text" name="startBytePosition_error_' + index + '"></td>',
                                '<td><input type="text" name="endBytePosition_error_' + index + '"></td>',
                                '<td><select name="fieldDescription_error_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="CommandId">CommandId</option>' +
                                '<option value="Function Code">Function Code</option>' +
                                '<option value="Checksum">Checksum</option>' +
                                '<option value="Terminator">Terminator</option>' +
                                '<option value="ActionId">ActionId</option>' +
                                '<option value="Data">Data</option>' +
                                 '<option value="DataSub Cmd">DataSub Cmd</option>' +
                                '<option value="Error Code">Error Code</option>' +
                                '<option value="Error details">Error details</option>'+
                                '<option value="Operation Code">Operation Code</option>' +
                                '<option value="Trigger">Trigger</option>' +
                                '</select></td>',
                                '<td><input type="text" name="numberOfBytes_error_' + index + '"></td>',
                                '<td><input type="text" name="bitSize_error_' + index + '"></td>',
                                '<td><select name="dataType_error_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="ASCII">ASCII</option>' +
                                '<option value="Binary">Binary</option>' +
                                '<option value="SignedInt">Signed Int</option>' +
                                '<option value="Float">Float</option>' +
                                '<option value="UnsignedInt">Unsigned Int</option>' +
                                '<option value="String">String</option>'+
                                '</select></td>',


                                '<td><select name="valueDefinition_error_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Fixed Value">Fixed Value</option>' +
                                '<option value="Range Value">Range Value</option>' +
                                '<option value="Mode Value">Mode Value</option>' +
                                '<option value="ASCII">ASCII</option>' +
                                '</select></td>',

                                '<td><input type="text" name="displayValueMin_error_' + index + '"></td>',
                                '<td><input type="text" name="displayValueMax_error_' + index + '"></td>',
                                '<td><input type="text" name="valueInHexMin_error_' + index + '"></td>',
                                '<td><input type="text" name="valueInHexMax_error_' + index + '"></td>',
                                '<td><select name="uiType_error_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Text">Text</option>' +
                                '<option value="Toggle Switch">Toggle Switch</option>' +
                                '<option value="Notification">Notification</option>'+
                                '<option value="console">console</option>'+
                                '<option value="Enter Text">Enter Text</option>'+
                                '<option value="Slider">Slider</option>' +
                                '<option value="Brightness Slider">Brightness Slider</option>' +
                                '<option value="Airflow Slider">Airflow Slider</option>' +
                                '<option value="CCT Slider">CCT Slider</option>' +
                                '<option value="Saturation Slider">Saturation Slider</option>' +
                                '<option value="Hue Slider">Hue Slider</option>' +
                                '<option value="Hue Slider Custom">Hue Slider Custom</option>' +
                                '<option value="Hue Circle">Hue Circle</option>' +
                                '<option value="Push Button">Push Button (Big)</option>' +
                                '<option value="Drop Down">Drop Down</option>' +
                                '<option value="Trigger">Trigger</option>' +
                                '<option value="Display">Display</option>' +
                                '<option value="Button">Button (Big)</option>' +
                                '<option value="Toast Display">Toast Display</option>'+
                                '</select></td>',

                                '<td><input type="text" name="uiId_error_' + index + '"></td>',
                                '<td><input type="text" name="asperui_error_' + index + '"></td>',
                                '<td><input type="text" name="sno_asperui_error' + index + '"></td>',
                                '<td><input type="text" name="asperuivalue_error_' + index + '"></td>',



                                '<td><select name="uiPageNo_error_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="0">0</option>' +
                                '<option value="1">1</option>' +
                                '<option value="2">2</option>' +
                                '<option value="3">3</option>' +
                                '<option value="4">4</option>' +
                                '<option value="5">5</option>' +
                                '<option value="6">6</option>' +
                                '<option value="7">7</option>' +
                                '<option value="8">8</option>' +
                                '<option value="9">9</option>' +
                                '<option value="10">10</option>' +
                                '</select></td>',



                                '<td><select name="uiDescriptionInOut_error_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Read/Write">Read/Write</option>' +
                                '<option value="Read">Read</option>' +
                                '<option value="Write">Write</option>' +
                                '</select></td>',


                                '<td><input type="text" name="sliderStepValue_error_' + index + '"></td>',
                                '<td><input type="text" name="defaultvalue_error_' + index + '"></td>',
                                '<td><input type="text" name="developerNotes_error_' + index + '"></td>'
                            ];
                             

                            // Insert cells into the new row
                            for (var i = 0; i < cells.length; i++) {
                                var cell = newRow.insertCell(i);
                                cell.innerHTML = cells[i];
                            }

                            // Add a remove button to delete the row if needed
                            var removeCell = newRow.insertCell(-1);
                            removeCell.innerHTML = '<button onclick="removeRow(this)">Remove</button>';
                        }



                        function errorupdateTable() {
                            clearTimeout(debounceTimer);

                            debounceTimer = setTimeout(function () {
                                var frameSizeInput = document.getElementById('error_noofrows');
                                var frameSize = parseInt(frameSizeInput.value) || 0; // Get the value or default to 0 if empty or NaN
                                var table = document.getElementById('errorTable');

                                // Clear previous rows
                                while (table.rows.length > 1) {
                                    table.deleteRow(1);
                                }

                                // Add new rows
                                for (var i = 0; i < Math.max(1, frameSize); i++) {
                                     erroraddRow(i + 1);
                                }
                            }, 500);
                        }







                        /*****************************************************************/

                        // function showImagePreview(input) {
                        //     var preview = document.getElementById('brandlogo-preview');
                        //     var file = input.files[0];
                        //     var reader = new FileReader();

                        //     reader.onloadend = function () {
                        //         preview.src = reader.result;
                        //         preview.style.display = 'block'; // Show the preview image
                        //     }

                        //     if (file) {
                        //         reader.readAsDataURL(file);
                        //     } else {
                        //         preview.src = '#'; // Clear the preview image
                        //         preview.style.display = 'none'; // Hide the preview image
                        //     }
                        // }
                        
                        
                         function changeImage(input) {
                            var preview = document.getElementById('imageUpload-preview');
                            var file = input.files[0];
                            var reader = new FileReader();

                            reader.onloadend = function () {
                                preview.src = reader.result;
                                preview.style.display = 'block'; // Show the preview image
                            }

                            if (file) {
                                reader.readAsDataURL(file);
                            } else {
                                preview.src = '#'; // Clear the preview image
                                preview.style.display = 'none'; // Hide the preview image
                            }
                        }











                        function checkWordCount(textarea) {
                            var maxWords = 300;
                            var wordCount = textarea.value.trim().split(/\s+/).length;

                            if (wordCount > maxWords) {
                                // If the word count exceeds the limit, truncate the content
                                var words = textarea.value.trim().split(/\s+/);
                                textarea.value = words.slice(0, maxWords).join(' ');
                                alert('Maximum word limit exceeded. Content has been truncated.');
                            }
                        }
                        /******************************************************************/
                        // Flag to track if the scrollbar is being interacted with
                        var scrollbarInteracted = false;

                        // Check if the form has unsaved changes
                        function formIsDirty() {
                            // Implement your logic here to check if the form is dirty
                            // For example, you can compare current form values with initial form values
                            // If there are unsaved changes, return true; otherwise, return false
                        }

                        // Attach event handler to the modal close event
                        $('#myModal').on('hide.bs.modal', function (e) {
                            // Check if the form is dirty and the scrollbar is not being interacted with
                            if (formIsDirty() && !scrollbarInteracted) {
                                // Prevent the modal from closing
                                e.preventDefault();

                                // Display a confirmation dialog
                                if (confirm("You have unsaved changes. Are you sure you want to close the form?")) {
                                    // If the user confirms, close the modal
                                    $('#myModal').modal('hide');
                                }
                            }
                        });

                        // Function to save the form data
                        function saveForm() {
                            // Perform form validation and save data to the server
                            // After successful save, close the modal
                            $('#myModal').modal('hide');
                        }

                        // Attach event handlers to track scrollbar interactions
                        $('#myModal').on('mousedown', function () {
                            scrollbarInteracted = true;
                        });

                        $(window).on('mouseup', function () {
                            scrollbarInteracted = false;
                        });
                        /******************************************************************/






                    
    function scrollToActiveStep() {
      const activeStep = document.querySelector('.form-step.active');
      if (activeStep) {
        activeStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }

</script>

<script>

  let currentStep = 1;

  function showStep(step) {
    document.querySelectorAll('.form-step').forEach(div => div.classList.remove('active'));
    document.getElementById('step-' + step).classList.add('active');
    currentStep = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function nextStep() {
  scrollToActiveStep();
    if (currentStep < 4) showStep(currentStep + 1);
  }

  function prevStep() {
    if (currentStep > 1) showStep(currentStep - 1);
  }

    function scrollToActiveStep() {
      const activeStep = document.querySelector('.form-step.active');
      if (activeStep) {
        activeStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }

</script>
<script>
function saveForm() {
    var formData = $('#file-form').serialize();

    $.ajax({
        url: $('#file-form').attr('action'), // action from PHP form_open
        type: 'POST',
        data: formData,
        success: function(response) {
            $('#myModal').modal('hide');
            alert("Form saved successfully!");
        },
        error: function() {
            alert("Error while saving the form.");
        }
    });
}

<script>
 function saveForm() {
                            // Perform form validation and save data to the server
                            // After successful save, close the modal
                            $('#myModal').modal('hide');
                        }
                        
    function scrollToActiveStep() {
      const activeStep = document.querySelector('.form-step.active');
      if (activeStep) {
        activeStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }

</script>

 <?php echo form_close(); ?>

<script>

function copyCmdParameterName(step) {
  const sourceInput = document.querySelector('#step-1 input[name="cmd_parametername"]');
  const sourceValue = sourceInput ? sourceInput.value.trim() : '';

  const targets = document.querySelectorAll(`#step-${step} input[name="cmd_parametername"]`);

  targets.forEach(target => {
    if (target.value.trim() === '') {
      target.removeAttribute('readonly');
      target.setAttribute('value', sourceValue);
      target.value = sourceValue;
      target.setAttribute('readonly', true);
    }
  });
}

function showStep(step) {
  document.querySelectorAll('.form-step').forEach(div => div.classList.remove('active'));
  document.getElementById('step-' + step).classList.add('active');
  currentStep = step;
  copyCmdParameterName(step);
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

    function scrollToActiveStep() {
      const activeStep = document.querySelector('.form-step.active');
      if (activeStep) {
        activeStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }

</script>


<script>
function validateStep1() {
  const paramInput = document.querySelector('#step-1 input[name="cmd_parametername"]');
  if (!paramInput || paramInput.value.trim() === '') {
    alert('Please enter a value for Parameter Name before proceeding.');
    paramInput.focus();
    return false;
  }
  return true;
}

function nextStep() {
  scrollToActiveStep();
  if (currentStep === 1 && !validateStep1()) {
    return;
  }
  if (currentStep < 4) {
    showStep(currentStep + 1);
  }
}

    function scrollToActiveStep() {
      const activeStep = document.querySelector('.form-step.active');
      if (activeStep) {
        activeStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }

</script>


<script>
document.addEventListener("DOMContentLoaded", function () {
  syncParameterNameToAllSteps();
  function syncAllParameterFields() {
    const allParamFields = document.querySelectorAll('input[name="cmd_parametername"]');
    const editableSource = Array.from(allParamFields).find(el => !el.hasAttribute("readonly"));

    if (editableSource) {
      const sync = () => {
        const newValue = editableSource.value;
        allParamFields.forEach(el => {
          if (el !== editableSource) {
            el.value = newValue;
          }
        });
      };

      sync();
      editableSource.addEventListener("input", sync);
    }
  }

  const navButtons = document.querySelectorAll(".next-btn, .back-btn");
  navButtons.forEach(btn => {
    btn.addEventListener("click", () => setTimeout(syncAllParameterFields, 300));
  });

  syncAllParameterFields();
});
</script>


<script>
document.addEventListener("DOMContentLoaded", function () {
  syncParameterNameToAllSteps();
  const getParamFields = () =>
    document.querySelectorAll('input[name="cmd_parametername"]');

  const sourceField = Array.from(getParamFields()).find(f => !f.hasAttribute("readonly"));

  function syncValueToAll(newValue) {
    const allFields = getParamFields();
    console.log("Syncing value:", newValue);
    allFields.forEach((field, index) => {
      console.log(`Field ${index + 1}:`, field);
      if (field !== sourceField) {
        field.value = newValue;
      }
    });
  }

  if (sourceField) {
    console.log("Source field found:", sourceField);
    sourceField.addEventListener("input", function () {
      syncValueToAll(this.value);
    });

    document.querySelectorAll(".next-btn, .back-btn").forEach((btn, idx) => {
      console.log("Attaching step button:", btn);
      btn.addEventListener("click", () => {
        setTimeout(() => {
          console.log("Step navigation clicked. Resyncing...");
          syncValueToAll(sourceField.value);
        }, 250);
      });
    });
  } else {
    console.warn("No editable source input field found for cmd_parametername");
  }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  syncParameterNameToAllSteps();
    const mainID = "par_parametername";
    const allIDs = ["par_parametername", "com_parametername", "resp_parametername", "error_parametername"];

    // Make all other inputs readonly
    allIDs.forEach(id => {
        if (id !== mainID) {
            const input = document.getElementById(id);
            if (input) input.readOnly = true;
        }
    });

    function syncValues() {
        const source = document.getElementById(mainID);
        if (!source) return;
        allIDs.forEach(id => {
            if (id !== mainID) {
                const input = document.getElementById(id);
                if (input) input.value = source.value;
            }
        });
    }

    const sourceInput = document.getElementById(mainID);
    if (sourceInput) {
        sourceInput.addEventListener("input", syncValues);
    }

    // Initial sync on load
    syncValues();

    // Navigation logic
    let currentStep = 1;

    window.showStep = function(step) {
        document.querySelectorAll('.form-step').forEach(div => div.classList.remove('active'));
        const target = document.getElementById('step-' + step);
        if (target) {
            target.classList.add('active');
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            currentStep = step;
            syncValues(); // 👈 Force sync on every step change
        }
    };

    window.nextStep = function() {
        if (currentStep < 4) showStep(currentStep + 1);
    };

    window.prevStep = function() {
        if (currentStep > 1) showStep(currentStep - 1);
    };
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  syncParameterNameToAllSteps();
    const mainID = "par_parametername";
    const allIDs = ["par_parametername", "com_parametername", "resp_parametername", "error_parametername"];

    // Readonly setup
    allIDs.forEach(id => {
        if (id !== mainID) {
            const input = document.getElementById(id);
            if (input) input.readOnly = true;
        }
    });

    function syncValues() {
        const source = document.getElementById(mainID);
        if (!source) return;
        allIDs.forEach(id => {
            const input = document.getElementById(id);
            if (input && id !== mainID) input.value = source.value;
        });
    }

    const sourceInput = document.getElementById(mainID);
    if (sourceInput) {
        sourceInput.addEventListener("input", syncValues);
    }

    syncValues(); // initial

    let currentStep = 1;

    // ✅ Global so buttons can use it
    window.showStep = function(step) {
        document.querySelectorAll('.form-step').forEach(div => div.classList.remove('active'));
        const target = document.getElementById('step-' + step);
        if (target) {
            target.classList.add('active');
            currentStep = step;
            syncValues();
        }
    };

    window.nextStep = function() {
        if (currentStep < 4) showStep(currentStep + 1);
    };

    window.prevStep = function() {
        if (currentStep > 1) showStep(currentStep - 1);
    };
});

document.addEventListener("DOMContentLoaded", function () {
  const sourceId = "par_parametername";
  const targetIds = ["com_parametername", "resp_parametername", "error_parametername"];
  let syncedValue = "";

  function updateAllTargets() {
    const value = document.getElementById(sourceId)?.value || "";
    syncedValue = value;
    targetIds.forEach(id => {
      const target = document.getElementById(id);
      if (target) {
        target.value = syncedValue;
        target.readOnly = true;
      }
    });
  }

  // Initial binding on Step 1 input
  const sourceInput = document.getElementById(sourceId);
  if (sourceInput) {
    syncedValue = sourceInput.value;
    sourceInput.addEventListener("input", updateAllTargets);
  }

  // Run on step change (ensure late-loaded DOM gets updated)
  const originalShowStep = window.showStep;
  window.showStep = function(step) {
    if (typeof originalShowStep === "function") originalShowStep(step);
    else {
      document.querySelectorAll(".form-step").forEach(div => div.classList.remove("active"));
      const stepDiv = document.getElementById("step-" + step);
      if (stepDiv) {
        stepDiv.classList.add("active");
        currentStep = step;
      }
    }

    // Delay ensures elements are visible before we update
    setTimeout(() => updateAllTargets(), 50);
  };

  // Fallback: observer (optional if fields load async)
  const observer = new MutationObserver(updateAllTargets);
  observer.observe(document.body, { childList: true, subtree: true });

  // First time sync
  updateAllTargets();
});

</script>
<script>
// Function to update 'Parameter Name' across all steps
function updateParameterNameAcrossSteps() {
  // Get the input field from Step 1
  const step1ParamNameInput = document.querySelector('#step-1 input[name="cmd_parametername"]');

  if (step1ParamNameInput) {
    // Add an event listener to the Step 1 'Parameter Name' input
    step1ParamNameInput.addEventListener('input', function() {
      const newValue = this.value; // Get the current value from Step 1 input

      // Select all 'Parameter Name' input fields in all steps
      // Note: The name 'cmd_parametername' is used in step-1, step-2, step-3, and step-4.
      // This selector will find all of them.
      const allParamNameInputs = document.querySelectorAll('input[name="cmd_parametername"]');

      // Iterate through all found input fields and update their value
      allParamNameInputs.forEach(inputField => {
        // Ensure we don't update the Step 1 input with its own value unnecessarily
        if (inputField !== step1ParamNameInput) {
          inputField.value = newValue;
        }
      });
    });
  }
}

// Call the function when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', updateParameterNameAcrossSteps);

// Existing showStep function modified to also copy the parameter name
// when navigating between steps, ensuring consistency if the user
// navigates back and forth without typing in step 1.
function showStep(step) {
  document.querySelectorAll('.form-step').forEach(div => div.classList.remove('active'));
  document.getElementById('step-' + step).classList.add('active');
  currentStep = step;
  copyCmdParameterName(step); // Call the existing copy function
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Existing copyCmdParameterName function (already present in your code)
// This function is good for initial population or when navigating.
function copyCmdParameterName(step) {
  const sourceInput = document.querySelector('#step-1 input[name="cmd_parametername"]');
  const sourceValue = sourceInput ? sourceInput.value.trim() : '';

  // Select all 'Parameter Name' inputs in the current step being shown
  const targets = document.querySelectorAll(`#step-${step} input[name="cmd_parametername"]`);

  targets.forEach(target => {
    // Only update if the target is not the source input itself
    if (target !== sourceInput) {
      target.removeAttribute('readonly'); // Temporarily remove readonly to set value
      target.value = sourceValue;
      target.setAttribute('readonly', true); // Re-add readonly
    }
  });
}

// Existing validateStep1 and nextStep functions remain as is.
function validateStep1() {
  const paramInput = document.querySelector('#step-1 input[name="cmd_parametername"]');
  if (!paramInput || paramInput.value.trim() === '') {
    alert('Please enter a value for Parameter Name before proceeding.');
    paramInput.focus();
    return false;
  }
  return true;
}

function nextStep() {
  if (currentStep === 1 && !validateStep1()) {
    return;
  }
  if (currentStep < 4) {
    showStep(currentStep + 1);
  }
}
</script>



</body>
</html>


          
