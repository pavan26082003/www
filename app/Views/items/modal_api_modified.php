 <!--
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Product Information</title>

  <!-- Fonts -->
  <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
  body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #dfe9f3, #ffffff);
  padding: 40px 20px;
  color: #333;
}

.container {
  max-width: 850px;
  margin: auto;
  background: #ffffff;
  padding: 45px 55px;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s ease;
  position: relative;
  z-index: 1;
}

.container:hover {
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

h1 {
  text-align: center;
  color: #0056b3;
  font-weight: 700;
  font-size: 28px;
  margin-bottom: 35px;
  letter-spacing: 1px;
}

.form-group {
  margin-bottom: 25px;
}

label {
  display: block;
  font-weight: 600;
  color: #444;
  margin-bottom: 10px;
}

input[type="text"],
input[type="number"],
input[type="date"],
input[type="file"],
select,
textarea {
  width: 100%;
  padding: 14px 16px;
  border: 1px solid #ced4da;
  border-radius: 10px;
  font-size: 15px;
  background-color: #f8f9fa;
  transition: all 0.3s ease;
}

input:focus,
select:focus,
textarea:focus {
  border-color: #007bff;
  box-shadow: 0 0 10px rgba(0, 123, 255, 0.2);
  outline: none;
  background-color: #ffffff;
}

textarea {
  resize: vertical;
}

.preview-img {
  margin-top: 15px;
  border: 1px solid #ccc;
  padding: 10px;
  border-radius: 8px;
  max-width: 100%;
  display: none;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

.btn {
  padding: 14px 32px;
  font-size: 16px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-success {
  background-color: #28a745;
  color: white;
}

.btn-success:hover {
  background-color: #218838;
  transform: scale(1.05);
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-primary:hover {
  background-color: #0056b3;
  transform: scale(1.05);
}

@media (max-width: 768px) {
  .container {
    padding: 25px;
  }

  h1 {
    font-size: 24px;
  }

  .btn {
    width: 100%;
  }
}
</style>
</head>
<body>

  <form class="my-form" enctype="multipart/form-data">
    <div class="container">
      <h1>PRODUCT INFORMATION</h1>

      <div class="form-group">
        <label for="frameType">Choose a Frame Type:</label>
        <select id="frameType" onchange="handleSelect(this)">
          <option selected disabled>-- Please choose an option --</option>
          <option value="">PRODUCT INFORMATION</option>
          <option value="commandframe">COMMAND FRAME</option>
          <option value="response">RESPONSE FRAME</option>
          <option value="ERROR">ERROR FRAME</option>
        </select>
      </div>

      <div class="form-group">
        <label for="productname">Parameter Name <span style="color: red">*</span></label>
        <input type="text" id="productname" name="productname" value="COOKER" readonly />
      </div>--
       <div class="commandname">



                            <div class="form-group">
                                <label class="col-sm-2" for="productname">Product Name:<span
                                        style="color: red;">*</span></label>
                                <small>Ex : Fan/Cooker.....</small>
                                <div class="col-sm-4">
                                    <span style="color: red;" id="producterror"></span>
                       <input type="text" class="form-control" value="<?php echo $category; ?>" id="productname" name="productname" readonly>
                                </div>
                            </div>

      <div class="form-group">
        <label for="brandname">Brand Name <span style="color: red">*</span></label>
        <input type="text" id="brandname" name="brandname" value="Efactor" readonly />
      </div>

      <div class="form-group">
        <label for="modelname">Model Name <span style="color: red">*</span></label>
        <input type="text" id="modelname" name="modelname" value="Efact V1.0" readonly />
      </div>

      <div class="form-group">
        <label for="date">Date <span style="color: red">*</span></label>
        <input type="date" id="date" name="date" />
      </div>

      <div class="form-group">
        <label for="numberofpages">Page Number <span style="color: red">*</span></label>
        <input type="text" id="numberofpages" name="numberofpages" placeholder="e.g., 1" />
      </div>

      <div class="form-group">
        <label for="Sequence">Sequence No <span style="color: red">*</span></label>
        <input type="text" id="Sequence" name="Sequence" placeholder="e.g., 1" />
      </div>

      <div class="form-group">
        <label for="pagenames">Page Names <span style="color: red">*</span></label>
        <input type="text" id="pagenames" name="pagenames" placeholder="e.g., Fan Page" />
      </div>

      <div class="form-group">
        <label for="brandlogo">Brand Logo</label>
        <input type="file" id="brandlogo" name="brandlogo" accept="image/*" onchange="showImagePreview(this)">
        <img id="brandlogo-preview" class="preview-img" />
      </div>

      <div class="form-group">
        <label for="productdescription">Product Description</label>
        <textarea id="productdescription" name="productdescription" rows="4" placeholder="Describe the product (max 300 words)..."></textarea>
      </div>

     <!-- <div style="display: flex; justify-content: center; gap: 20px; margin-top: 30px;">
        <button type="submit" class="btn btn-success">Save</button>--
        <button type="button" class="btn btn-primary" onclick="goToCommand()">Next</button>
      </div>-->
    </div>
  </form>


                  <!--            <?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
                             <style>
                             /* Container spacing */
                             .section-container {
    background: white;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
    border-radius: 8px;
}

.commandname {
  padding: 10px 20px;
}

/* Headings */
.commandname h2,
.commandname h4 {
  font-family: Arial, sans-serif;
  color: #0071ce;
  font-weight: bold;
  text-align: center;
  margin-bottom: 25px;
}

.commandname h2 {
  font-size: 20px;
  text-align: left;
  color: #222;
}

/* Labels */
.commandname label {
  font-size: 14px;
  font-weight: 500;
  display: block;
  margin-bottom: 6px;
  color: #333;
}

/* Help text */
.commandname small {
  font-size: 12px;
  color: #888;
  display: block;
  margin-top: -5px;
  margin-bottom: 10px;
}

/* Inputs and selects */
.commandname input[type="text"],
.commandname select {
  width: 100%;
  padding: 12px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 6px;
  background-color: #f9f9f9;
  margin-bottom: 20px;
}

/* Error messages */
.commandname .error.product-error {
  color: red;
  font-size: 12px;
  margin-bottom: 5px;
}

/* Responsive form structure using Bootstrap */
.commandname .form-group {
  margin-bottom: 25px;
  display: flex;
  flex-wrap: wrap;
}

.commandname .form-group > .col-sm-2 {
  flex: 0 0 20%;
  max-width: 20%;
  padding-top: 10px;
}

.commandname .form-group > .col-sm-4 {
  flex: 0 0 80%;
  max-width: 80%;
}
</style>
   <!--<div id="commandFrame" class="form-step" style="display: none;">  </div>                           
 <div id="items-dropzone" class="post-dropzone">-->

                                  <!--  <div class="modal-body clearfix">
                                        <div class="container-fluid">-->
                         <!--                <section id="commandframe">
   

                                       <div class="container"> <!-- Just one wrapper -->
    
                         <!--                    <input type="hidden" name="id" value="<?php echo $id; ?>" />

                                            <h4 style="text-align: center; font-size: 30px; color: #0056b3;">COMMAND FRAME
                                            </h4>


                                            <h2 style="font-size: 25px;color:#0056b3">Command Frame Information</h2>



                                            <br><br>
                                            <div class="commandname">

                                                <div class="form-group">
                                                    <label class=" col-sm-2" for="cmd_parametername">Parameter Name</label>
                                                    <small>Ex : Speed,Power....</small>
                                                    <div class="col-sm-4">
                                                        <p class="error product-error"></p>
                                                        <input type="text" class="form-control" id="cmd_parametername"
                                                            name="cmd_parametername">
                                                    </div>
                                                </div>



                                                <div class="form-group">
                                                    <label class="col-sm-2" for="pwd">Command Data Flow
                                                        Direction</label>
                                                    <div class="col-sm-4">
                                                        <select class="form-control" id="cmd_data_flow_direction"
                                                            name="cmd_data_flow_direction">
                                                            <option value="App/Server to Device" selected>App/Server to
                                                                Device
                                                            </option>
                                                            <option value="Device to App/Server">Device to App/Server
                                                            </option>
                                                        </select>
                                                    </div>
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
                                                            id="cmd_framesizeinbytes" name="cmd_framesizeinbytes">
                                                    </div>
                                                </div>


                                                <div class="form-group">
                                                    <label class=" col-sm-2" for="pwd">No of rows</label>
                                                    <small>Ex : 1,2,3,4....</small>
                                                    <div class="col-sm-2">
                                                        <input type="text" class="form-control" id="cmd_noofrows"
                                                            name="cmd_noofrows" oninput="updateTable()">
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
                                                    <label class="col-sm-2" for="pwd">Multiple UI</label>
                                                    <div class="col-sm-4">
                                                        <select class="form-control" id="cmd_multipleui"
                                                            name="cmd_multipleui">
                                                            <option value="false">false</option>
                                                            <option value="true">true</option>
                                                            
                                                        </select>
                                                    </div>
                                                </div>


 


                                               <div class="form-group">
                                                    <label class="col-sm-2" for="pwd">Trigger UI</label>
                                                    <div class="col-sm-4">
                                                        <select class="form-control" id="cmd_triggerui" name="cmd_triggerui">
                                                            <option value="false">false</option>
                                                            <option value="true">true</option>
                                                            
                                                        </select>
                                                    </div>
                                                </div>


          


                                                <div class="form-group">
                                                    <label class=" col-sm-2" for="pwd">Voice Command</label>
                                                    <small>Ex : Yes/No</small>
                                                    <div class="col-sm-2">
                                                        <input type="text" class="form-control" id="cmd_voicecommand"
                                                            name="cmd_voicecommand">
                                                    </div>
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
                                                        <tbody>
                                                        <tbody>
                                                            <!-- Initial row with input fields and select dropdowns -->
                                    <!--                         <tr id="row_1">
                                                                <!-- Individual fields with IDs -->
                                     <!--                            <td id="sno_1">1</td>
                                                                <td><input type="text" id="startBytePosition_cmd"
                                                                        name="startBytePosition_cmd"></td>
                                                                <td><input type="text" id="endBytePosition_cmd"
                                                                        name="endBytePosition_cmd"></td>


                                                                <td>
                                                                    <select id="fieldDescription_cmd"
                                                                        name="fieldDescription_cmd">
                                                                        <option value="None" selected>None</option>
                                                                        <option value="CommandId">CommandId</option>
                                                                        <option value="Function Code">Function Code</option>
                                                                        <option value="Checksum">Checksum</option>
                                                                        <option value="Terminator">Terminator</option>
                                                                        <option value="ActionId">ActionId</option>
                                                                        <option value="Data">Data</option>
                                                                        <option value="Error Code">Error Code</option>
                                                                        <option value="Error details">Error details</option>
                                                                        <option value="Operation Code">Operation Code</option>
                                                                        <option value="Trigger">Trigger</option>
                                                                        <option value="SUID">SUID</option>
                                                                        <option value="DUID">DUID</option>
                                                                        <option value="UDP CMD">UDP CMD</option>
                                                                        <option value="ACK">ACK</option>
                                                                        <option value="MEDIA">MEDIA</option>
                                                                        <option value="Frame">Frame</option>
                                                                        <option value="FID">FID</option>
                                                                        <option value="User ID">User ID</option>
                                                                        <option value="SDUID Enable">SDUID Enable</option>
                                                                    </select>
                                                                </td>


                                                                <td><input type="text" id="numberOfBytes_cmd"
                                                                        name="numberOfBytes_cmd"></td>

                                                                <td><input type="text" id="bitSize_cmd"
                                                                        name="bitSize_cmd">
                                                                </td>
                                                                <td>
                                                                    <select id="dataType_cmd" name="dataType_cmd">
                                                                        <option value="None" selected>None</option>
                                                                        <option value="ASCII">ASCII</option>
                                                                        <option value="Binary">Binary</option>
                                                                        <option value="SignedInt">Signed Int</option>
                                                                        <option value="Float">Float</option>
                                                                        <option value="UnsignedInt">Unsigned Int
                                                                        </option>
                                                                        <option value="String">String</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <select id="valueDefinition_cmd"
                                                                        name="valueDefinition_cmd">
                                                                        <option value="None" selected>None</option>
                                                                        <option value="Fixed value">Fixed Value
                                                                        </option>
                                                                        <option value="Range value">Range Value</option>
                                                                        <option value="Mode value">Mode Value</option>
                                                                        <option value="ASCII">ASCII</option>
                                                                    </select>
                                                                </td>



                                                                <td><input type="text" id="displayValueMin_cmd"
                                                                        name="displayValueMin_cmd"></td>
                                                                <td><input type="text" id="displayValueMax_cmd"
                                                                        name="displayValueMax_cmd"></td>
                                                                <td><input type="text" id="valueInHexMin_cmd"
                                                                        name="valueInHexMin_cmd"></td>
                                                                <td><input type="text" id="valueInHexMax_cmd"
                                                                        name="valueInHexMax_cmd"></td>
                                                                <td>
                                                                    <select id="uiType_cmd" name="uiType_cmd">
                                                                        <option value="None" selected>None</option>
                                                                        <option value="Text">Text
                                                                        </option>
                                                                        <option value="Toggle Switch">Toggle Switch
                                                                        </option>
                                                                        <option value="Slider">Slider</option>
                                                                        <option value="Brightness Slider">Brightness
                                                                            Slider
                                                                        </option>
                                                                        <option value="CCT Slider">CCT Slider</option>
                                                                        <option value="Saturation Slider">Saturation
                                                                            Slider
                                                                        </option>
                                                                        <option value="Airflow Slider">Airflow Slider</option>
                                                                        <option value="Hue Slider Custom">Hue Slider Custom</option>
                                                                        <option value="Hue Slider">Hue Slider</option>
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
                                                                </td>

                                                                <td><input type="text" id="uiId_cmd" name="uiId_cmd">
                                                                </td>

                                            <td><input type="text" id="asperui_cmd"      name="asperui_cmd"     ></td>
                                            <td><input type="text" id="sno_asperui_cmd"  name="sno_asperui_cmd" ></td>
                                            <td><input type="text" id="asperuivalue_cmd" name="asperuivalue_cmd"></td>


                                                                <td>
                                                                    <select id="uiPageNo_cmd" name="uiPageNo_cmd">
                                                                        <option value="">None</option>
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





                                                                <td>
                                                                    <select id="uiDescriptionInOut_cmd"
                                                                        name="uiDescriptionInOut_cmd">
                                                                        <option value="None" selected>None</option>
                                                                        <option value="Read/Write">Read/Write</option>
                                                                        <option value="Read">Read</option>
                                                                        <option value="Write">Write</option>
                                                                    </select>
                                                                </td>


                                   <td><input type="text" id="sliderStepValue_cmd" name="sliderStepValue_cmd"></td>
                                                                        
                                   <td><input type="text" id="defaultvalue_cmd" name="defaultvalue_cmd"></td>
                                 
                                   <td>
                                                                    <select id="notification_cmd"
                                                                        name="notification_cmd">
                                                                        <option value="Disable" selected>Disable</option>
                                                                        <option value="Enable">Enable</option> 
                                                                    </select>
                                                                </td>
                                   <td><input type="text" id="notification_data_cmd" name="notification_data_cmd"></td>
                                                                        
                                    <td><input type="text" id="developerNotes_cmd" name="developerNotes_cmd"></td>
                                    </tr>
                                                        </tbody>
                                                    </table>
                                                     <tr>
                                                           <button type="button" onclick="addRow()">+</button>
                                                           </tr>
                                                </div>
                                            </div>
                                        </div>
                                        </section>
                                      <!--    <div id="items-dropzone" class="post-dropzone">--

                                            <div class="modal-body clearfix">
                                               <!-- <div class="container-fluid">--
                                                <div class="frame-card">--
                                                <div class="section-container">-->
                              <!--                   <div class="container">
<div class="responsename">
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
  color: green;
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
                                                    <input type="hidden" name="id" value="<?php echo $id; ?>"

                                                    <h4 style="text-align: center;font-size: 30px; color:#0056b3;">
                                                        Response
                                                        FRAME
                                                    </h4>


                                                    <h2 style="font-size: 25px; color:#0056b3;">Response Frame Information</h2>



                                                    <br><br>
                                                    <div class="commandname">



                                                        <div class="form-group">
                                                            <label class=" col-sm-2" for="resp_parametername">Parameter Name</label>
                                                            <small>Ex : speed,power.....</small>
                                                            <div class="col-sm-4">
                                                                <p class="error product-error"></p>
                                                                <input type="text" class="form-control"
                                                                    id="resp_parametername" name="resp_parametername">
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

         

                                                       <div class="form-group">
                                                    <label class="col-sm-2" for="pwd">Multiple UI</label>
                                                    <div class="col-sm-4">
                                                        <select class="form-control" id="resp_multipleui"
                                                            name="resp_multipleui">
                                                            <option value="false">false</option>
                                                            <option value="true">true</option>
                                                            
                                                        </select>
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
                                                                <tbody>
                                                                <tbody>
                                                                    <!-- Initial row with input fields and select dropdowns -->
                                     <!--                                <tr id="row_1">
                                                                        <!-- Individual fields with IDs -->
                                       <!--                                  <td id="sno_1">1</td>
                                                                        <td><input type="text"
                                                                                id="startBytePosition_resp"
                                                                                name="startBytePosition_resp">
                                                                        </td>
                                                                        <td><input type="text" id="endBytePosition_resp"
                                                                                name="endBytePosition_resp">
                                                                        </td>


                                                                        <td>
                                                                            <select id="fieldDescription_resp"
                                                                                name="fieldDescription_resp">
                                                                                <option value="None" selected>None
                                                                                </option>
                                                                                <option value="CommandId">CommandId
                                                                                </option>
                                                                                <option value="Function Code">Function
                                                                                    Code
                                                                                </option>
                                                                                <option value="Checksum">Checksum
                                                                                </option>
                                                                                <option value="Terminator">Terminator
                                                                                </option>
                                                                                <option value="ActionId">ActionId
                                                                                </option>
                                                                                <option value="Data">Data</option>
                                                                                <option value="Error Code">Error Code
                                                                                </option>
                                                                                <option value="Error details">Error details</option>
                                                                                <option value="Operation Code">Operation
                                                                                    Code
                                                                                </option>
                                                                                <option value="Trigger">Trigger</option>
                                                                                <option value="SUID">SUID</option>
                                                                        <option value="DUID">DUID</option>
                                                                        <option value="UDP CMD">UDP CMD</option>
                                                                        <option value="ACK">ACK</option>
                                                                        <option value="MEDIA">MEDIA</option>
                                                                        <option value="Frame">Frame</option>
                                                                        <option value="FID">FID</option>
                                                                        <option value="User ID">User ID</option>
                                                                        <option value="SDUID Enable">SDUID Enable</option>
                                                                            </select>
                                                                        </td>
                                                                        <td><input type="text" id="numberOfBytes_resp"
                                                                                name="numberOfBytes_resp"></td>
                                                                        <td><input type="text" id="bitSize_resp"
                                                                                name="bitSize_resp"></td>
                                                                        <td>
                                                                            <select id="dataType_resp"
                                                                                name="dataType_resp">
                                                                                <option value="None" selected>None
                                                                                </option>
                                                                                <option value="ASCII">ASCII</option>
                                                                                <option value="Binary">Binary</option>
                                                                                <option value="SignedInt">Signed Int
                                                                                </option>
                                                                                <option value="Float">Float</option>
                                                                                <option value="UnsignedInt">Unsigned Int
                                                                                </option>
                                                                                <option value="String">String</option>
                                                                            </select>
                                                                        </td>

                                                                        <td>
                                                                            <select id="valueDefinition_resp"
                                                                                name="valueDefinition_resp">
                                                                                <option value="None" selected>None
                                                                                </option>
                                                                                <option value="Fixed value">Fixed Value
                                                                                </option>
                                                                                <option value="Range value">Range Value
                                                                                </option>
                                                                                <option value="Mode value">Mode Value
                                                                                </option>
                                                                                <option value="ASCII">ASCII</option>
                                                                            </select>
                                                                        </td>

                                                                        <td><input type="text" id="displayValueMin_resp"
                                                                                name="displayValueMin_resp">
                                                                        </td>
                                                                        <td><input type="text" id="displayValueMax_resp"
                                                                                name="displayValueMax_resp">
                                                                        </td>
                                                                        <td><input type="text" id="valueInHexMin_resp"
                                                                                name="valueInHexMin_resp"></td>
                                                                        <td><input type="text" id="valueInHexMax_resp"
                                                                                name="valueInHexMax_resp"></td>
                                                                        <td>
                                                                            <select id="uiType_resp" name="uiType_resp">
                                                                                <option value="None" selected>None
                                                                                </option>
                                                                                <option value="Text">
                                                                                    Text</option>
                                                                                <option value="Toggle Switch">Toggle
                                                                                    Switch</option>
                                                                                <option value="Slider">Slider</option>
                                                                                <option value="Brightness Slider">
                                                                                    Brightness
                                                                                    Slider</option>
                                                                           <option value="Airflow Slider">Airflow Slider</option>
                                                                                <option value="CCT Slider">CCT Slider
                                                                                </option>
                                                                                <option value="Saturation Slider">
                                                                                    Saturation
                                                                                    Slider</option>
                                                                                <option value="Hue Slider">Hue Slider
                                                                                </option>
                                                                         <option value="Hue Slider Custom">Hue Slider Custom</option>
                                                                                <option value="Hue Circle">Hue Circle
                                                                                </option>
                                                                                <option value="Push Button">Push Button
                                                                                    (Big)
                                                                                </option>
                                                                                <option value="Drop Down">Drop Down
                                                                                </option>
                                                                                <option value="Trigger">Trigger</option>
                                                                                <option value="Display">Display</option>
                                                                                <option value="Button">Button</option>
                                                                                <option value="Auto Trigger With Timer">Auto Trigger With Timer</option>
                                                                                <option value="Toast Display">Toast Display</option>

                                                                            </select>
                                                                        </td>


                                             <td><input type="text" id="uiId_resp"  name="uiId_resp">  </td>
                                            <td><input type="text" id="asperui_resp" name="asperui_resp"></td>
                                        <td><input type="text" id="sno_asperui_resp" name="sno_asperui_resp"></td>
                                        <td><input type="text" id="asperuivalue_resp" name="asperuivalue_resp"></td>



                                                                        <td>
                                                                            <select id="uiPageNo_resp"
                                                                                name="uiPageNo_resp">
                                                                                <option value="">None</option>
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





                                                                        <td>
                                                                            <select id="uiDescriptionInOut_resp"
                                                                                name="uiDescriptionInOut_resp">
                                                                                <option value="">None</option>
                                                                                <option value="Read/Write">Read/Write
                                                                                </option>
                                                                                <option value="Read">Read</option>
                                                                                <option value="Write">Write</option>
                                                                            </select>
                                                                        </td>


                                                                        <td><input type="text" id="sliderStepValue_resp"
                                                                                name="sliderStepValue_resp">
                                                                        </td>
                                             <td><input type="text" id="defaultvalue_resp" name="defaultvalue_resp"></td>

                                             <td>
                                                                    <select id="notification_resp"
                                                                        name="notification_resp">
                                                                        <option value="Disable" selected>Disable</option>
                                                                        <option value="Enable">Enable</option> 
                                                                    </select>
                                                                </td>

                                   <td><input type="text" id="notification_data_resp" name="notification_data_resp"></td>
                                                                        
                                                                        <td><input type="text" id="developerNotes_resp"
                                                                                name="developerNotes_resp"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
 <tr>
                                                           <button type="button" onclick="respaddRow()">+</button>
                                                           </tr>
                                                        </div>
                                                    </div>
                                                </div>
  <?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
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
    background-color: #f9f9f9;
}

h4, h2 {
    text-align: center;
    color: green;
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

/* Submit Button */
input[type="submit"] {
    padding: 10px 25px;
    font-size: 16px;
    background-color: #28a745;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 20px;
    display: block;
    margin-left: auto;
    margin-right: auto;
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
                                               <!-- <div id="items-dropzone" class="post-dropzone">

                                                    <div class="modal-body clearfix">
                                                        <div class="container-fluid">-->
                                   <!--                           <div class="container">
<div class="responsename">
 <div class="form-group">
                                                            <input type="hidden" name="id" value="<?php echo $id; ?>" 

                                                            <h4
                                                                style="text-align: center;font-size: 30px; color: #0056b3;">
                                                                ERROR
                                                                FRAME</h4>


                                                            <h2 style="text-align: center;font-size: 25px;color: #0056b3">Error Frame Information</h2>

                                          


                                                            <br><br>
                                                            <div class="commandname">



                                                                <div class="form-group">
                                                                    <label class=" col-sm-2" for="error_parametername">Parameter
                                                                        Name</label>
                                                                    <small>Ex : Speed,Power..</small>
                                                                   <!-- <div class="col-sm-4">-->
                                             <!--                            <p class="error product-error"></p>
                                                                        <input type="text" class="form-control"
                                                                            id="error_parametername"
                                                                            name="error_parametername">
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
                                                            <option value="false">false</option>
                                                            <option value="true">true</option>
                                                            
                                                        </select>
                                                    </div>
                                                </div>






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
                                                                        <tbody>
                                                                        <tbody>
                                                                            <!-- Initial row with input fields and select dropdowns -->
                                        <!--                                     <tr id="row_1">
                                                                                <!-- Individual fields with IDs -->
                                       <!--                                          <td id="sno_1">1</td>
                                                                                <td><input type="text"
                                                                                        id="startBytePosition_error"
                                                                                        name="startBytePosition_error">
                                                                                </td>
                                                                                <td><input type="text"
                                                                                        id="endBytePosition_error"
                                                                                        name="endBytePosition_error">
                                                                                </td>


                                                                                <td>
                                                                                    <select id="fieldDescription_error"
                                                                                        name="fieldDescription_error">
                                                                                        <option value="None" selected>
                                                                                            None
                                                                                        </option>
                                                                                        <option value="CommandId">
                                                                                            CommandId</option>
                                                                                        <option value="Function Code">
                                                                                            Function
                                                                                            Code</option>
                                                                                        <option value="Checksum">
                                                                                            Checksum
                                                                                        </option>
                                                                                        <option value="Terminator">
                                                                                            Terminator
                                                                                        </option>
                                                                                        <option value="ActionId">
                                                                                            ActionId
                                                                                        </option>
                                                                                        <option value="Data">Data
                                                                                        </option>
                                                                                        <option value="Error Code">Error
                                                                                            Code
                                                                                        </option>
                                                                                        <option value="Error details">Error details</option>
                                                                                        <option value="Operation Code">
                                                                                            Operation
                                                                                            Code</option>
                                                                                         <option value="Trigger">Trigger</option>
                                                                                    </select>
                                                                                </td>
                                                                                <td><input type="text"
                                                                                        id="numberOfBytes_error"
                                                                                        name="numberOfBytes_error"></td>
                                                                                <td><input type="text"
                                                                                        id="bitSize_error"
                                                                                        name="bitSize_error">
                                                                                </td>
                                                                                <td>
                                                                                    <select id="dataType_error"
                                                                                        name="dataType_error">
                                                                                        <option value="None" selected>
                                                                                            None
                                                                                        </option>
                                                                                        <option value="ASCII">ASCII
                                                                                        </option>
                                                                                        <option value="Binary">Binary
                                                                                        </option>
                                                                                        <option value="SignedInt">Signed
                                                                                            Int
                                                                                        </option>
                                                                                        <option value="Float">Float
                                                                                        </option>
                                                                                        <option value="UnsignedInt">
                                                                                            Unsigned
                                                                                            Int
                                                                                        </option>
                                                                                        <option value="String">String</option>
                                                                                    </select>
                                                                                </td>

                                                                                <td>
                                                                                    <select id="valueDefinition_error"
                                                                                        name="valueDefinition_error">
                                                                                        <option value="None" selected>
                                                                                            None
                                                                                        </option>
                                                                                        <option value="Fixed Value">
                                                                                            Fixed value</option>
                                                                                        <option value="Range Value">
                                                                                            Range
                                                                                            value
                                                                                        </option>
                                                                                        <option value="Mode Value">Mode
                                                                                            value
                                                                                        </option>
                                                                                        <option value="ASCII">ASCII
                                                                                        </option>
                                                                                    </select>
                                                                                </td>

                                                                                <td><input type="text"
                                                                                        id="displayValueMin_error"
                                                                                        name="displayValueMin_error">
                                                                                </td>
                                                                                <td><input type="text"
                                                                                        id="displayValueMax_error"
                                                                                        name="displayValueMax_error">
                                                                                </td>
                                                                                <td><input type="text"
                                                                                        id="valueInHexMin_error"
                                                                                        name="valueInHexMin_error"></td>
                                                                                <td><input type="text"
                                                                                        id="valueInHexMax_error"
                                                                                        name="valueInHexMax_error"></td>
                                                                                <td>
                                                                                    <select id="uiType_error"
                                                                                        name="uiType_error">
                                                                                        <option value="None" selected>
                                                                                            None
                                                                                        </option>
                                                                                        <option value="Text">
                                                                                            Text</option>
                                                                                        <option value="Toggle Switch">
                                                                                            Toggle Switch</option>
                                                                          <option value="Notification">Notification</option>
                                                                          <option value="console">console</option>
                                                                        <option value="Enter Text">Enter Text</option>
                                                                                        <option value="Slider">Slider
                                                                                        </option>
                                                                                        <option
                                                                                            value="Brightness Slider">
                                                                                            Brightness Slider</option>
                                                                          <option value="Airflow Slider">Airflow Slider</option>
                                                                                        <option value="CCT Slider">CCT
                                                                                            Slider
                                                                                        </option>
                                                                                        <option
                                                                                            value="Saturation Slider">
                                                                                            Saturation Slider</option>
                                                                                        <option value="Hue Slider">Hue
                                                                                            Slider
                                                                                        </option>
                                                                         <option value="Hue Slider Custom">Hue Slider Custom</option>
                                                                                        <option value="Hue Circle">Hue
                                                                                            Circle
                                                                                        </option>
                                                                                        <option value="Push Button">Push
                                                                                            Button
                                                                                            (Big)</option>
                                                                                        <option value="Drop Down">Drop
                                                                                            Down
                                                                                        </option>
                                                                                        <option value="Trigger">Trigger
                                                                                        </option>
                                                                                        <option value="Display">Display
                                                                                        </option>
                                                                                        <option value="Button">Button
                                                                                        </option>
                                                                                        <option value="Toast Display">Toast Display</option>

                                                                                    </select>
                                                                                </td>



              <td><input type="text" id="uiId_error"  name="uiId_error"></td>

                <td><input type="text"  id="asperui_error"  name="asperui_error"> </td>
                <td><input type="text"  id="sno_asperui_error"  name="sno_asperui_error"> </td>
                <td><input type="text" id="asperuivalue_error"  name="asperuivalue_error">  </td>

                                                                                <td>
                                                                                    <select id="uiPageNo_error"
                                                                                        name="uiPageNo_error">
                                                                                        <option value="">None</option>
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




                                                                                <td>
                                                                                    <select
                                                                                        id="uiDescriptionInOut_error"
                                                                                        name="uiDescriptionInOut_error">
                                                                                        <option value="None" selected>
                                                                                            None
                                                                                        </option>
                                                                                        <option value="Read/Write">
                                                                                            Read/Write</option>
                                                                                        <option value="Read">Read
                                                                                        </option>
                                                                                        <option value="Write">Write
                                                                                        </option>
                                                                                    </select>
                                                                                </td>


                                                                                <td><input type="text"
                                                                                        id="sliderStepValue_error"
                                                                                        name="sliderStepValue_error">
                                                                                </td>
                                                                                
                          <td><input type="text" id="defaultvalue_error" name="defaultvalue_error"></td>                            
                                                                                
                                                                                <td><input type="text"
                                                                                        id="developerNotes_error"
                                                                                        name="developerNotes_error">
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
 <tr>
                                                           <button type="button" onclick="erroraddRow()">+</button>
                                                           </tr>

                                                                </div>
                                                            </div>
                                                        </div>





 
                                                        <input style="margin-top: 20px; color: white; border: none; background-color: green;" type="submit" value="Submit" name="submit">
                                                    </div>
                                                     
                    </form>



                    <!--div class="modal-footer">   
            <button type="button" class="btn btn-primary" id="save-btn"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div--
        <script>
        
  function scrollToSection(commandframe) {
    if (commandframe) {
        document.getElementById(commandframe).scrollIntoView({ behavior: 'smooth' });
    }
}
   function handleSelect(select) {
    const value = select.value;

    // Hide all frame sections
    document.getElementById('COMMAND').style.display = 'none';
    document.getElementById('responseFrame').style.display = 'none';
    document.getElementById('errorFrame').style.display = 'none';

    // Show selected frame
    if (value === 'COMMAND') {
      document.getElementById('COMMAND').style.display = 'block';
    } else if (value === 'response') {
      document.getElementById('responseFrame').style.display = 'block';
    } else if (value === 'ERROR') {
      document.getElementById('errorFrame').style.display = 'block';
    }
  }

  // Optional: Reset to hidden on page load
  document.addEventListener('DOMContentLoaded', function () {
    handleSelect(document.getElementById('frameType'));
  });
</script>

        <script>
        function validatePageName() {
            var input = document.getElementById("pagenames").value;
            if (input === "Page0") {
                alert("Page0 as Page name is invalid");
            }
        }
    </script>
    
    <script> 
    document.getElementById("currentDate").value = new Date().toISOString().split('T')[0];
</script>

                    <script type="text/javascript">
 
    
 

                        /***************additional_table******************************************/


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

 



                        /******************************************************************/

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



                        /*
                        function respupdateTable() {
                        
                        clearTimeout(debounceTimer);
                        
                            var frameSize = parseInt(document.getElementById('resp_noofrows').value);
                            var table = document.getElementById('responseTable');
                        
                            debounceTimer = setTimeout(function() {
                                   
                            while (table.rows.length > 2) {
                                table.deleteRow(1);
                            }
                                for (var i = 0; i < frameSize; i++) {
                                respaddRow(i + 1);     }
                        
                                }, 500); 
                        }
                        */


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






                        /********************************************************************/
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





                        /*
                        function errorupdateTable() {
                        
                        clearTimeout(debounceTimer);
                        
                            var frameSize = parseInt(document.getElementById('error_noofrows').value);
                            var table = document.getElementById('errorTable');
                        
                            debounceTimer = setTimeout(function() {
                                   
                            while (table.rows.length > 2) {
                                table.deleteRow(1);
                            }
                                for (var i = 0; i < frameSize; i++) {
                                erroraddRow(i + 1);     }
                        
                                }, 500); 
                        }
                        */



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

                        function showImagePreview(input) {
                            var preview = document.getElementById('brandlogo-preview');
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







                    </script>


                 <!--   <?php echo form_close(); ?>-->

   <!--<script>
    function showImagePreview(input) {
      const preview = document.getElementById('brandlogo-preview');
      const file = input.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
      } else {
        preview.style.display = 'none';
        preview.src = '';
      }
    }

    function handleSelect(select) {
      const url = select.value;
      if (url) {
        window.location.href = url;
      }
    }

    function goToCommand() {
      window.location.href = "COMMAND"; // Adjust path as needed
    }
  </script>
   <?php echo form_close(); ?>
</body>
</html>-->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Multi-Step Form (Product + Command)</title>
  <style>
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
<!--<?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>-->
<?php echo form_open_multipart(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form")); ?>
<?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
<div id="step-1" class="form-step active">
  <div class="container">
    <h2>PRODUCT INFORMATION</h2>

      <div class="form-group">
      <label for="cmd_parametername">Parameter Name</label>
      <input type="text" name="cmd_parametername" />
    </div>

    <div class="form-group">
      <label for="productname">Product Name</label>
     <!-- <input type="text" name="productname" value="Fan/Cooker" readonly />
    </div>-->
    <input type="text" class="form-control" value="<?php echo $category; ?>" id="productname" name="productname" readonly>

  <!--  <div class="form-group">
      <label for="brandname">Brand Name</label>
      <input type="text" name="brandname" value="Efactor" readonly />
    </div>

   <div class="form-group">
      <label for="modelname">Model Name</label>
      <input type="text" name="modelname" value="Efact V1.0" readonly />
    </div>-->
    
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
                                    <input type="text" class="form-control"  value="<?php echo $modelno; ?>" id="modelname" name="modelname" readonly>
                                </div>
                            </div>

    <div class="form-group">
      <label for="date">Date</label>
      <input type="date" name="date" />
    </div>

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

    <div class="form-group">
      <label for="brandlogo">Brand Logo</label>
      <input type="file" name="brandlogo" accept="image/*" />
    </div>

    <div class="form-group">
      <label for="productdescription">Product Description</label>
      <textarea name="productdescription" rows="4" placeholder="Describe the product (max 300 words)..."></textarea>
    </div>

    <button class="btn" type="button" onclick="nextStep()" >Next</button>
  </div>
</div>

</body>
</html>


<!-- Step 2: Command Frame --
<?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
<div id="step-2" class="form-step">
  <div class="container">

    <input type="hidden" name="id" value="<?php echo $id; ?>" 
    <h2>COMMAND FRAME</h2>

    <div class="form-group">
      <label for="cmd_parametername">Parameter Name</label>
      <input type="text" name="cmd_parametername" />
    </div>

    <div class="form-group">
      <label for="cmd_data_flow_direction">Data Flow Direction</label>
      <select name="cmd_data_flow_direction">
        <option value="App/Server to Device">App/Server to Device</option>
        <option value="Device to App/Server">Device to App/Server</option>
      </select>
    </div>

    <div class="form-group">
      <label for="cmd_frametype">Frame Type</label>
      <select name="cmd_frametype">
        <option value="COMMAND">COMMAND</option>
        <option value="RESPONSE">RESPONSE</option>
        <option value="ERROR">ERROR</option>
      </select>
    </div>

    <div class="form-group">
      <label>Frame Size in Bytes</label>
      <input type="text" name="cmd_framesizeinbytes" />
    </div>

    <div class="form-group">
      <label>No of Rows</label>
      <input type="text" name="cmd_noofrows" />
    </div>

    <div class="form-group">
      <label>Frame Trigger</label>
      <select name="cmd_frametrigger">
        <option value="None">None</option>
        <option value="INCOMING FRAME">INCOMING FRAME</option>
        <option value="Toggle Switch">Toggle Switch</option>
        <!-- ... Other options omitted for brevity -->
      </select>
    </div>

  <!--  <div class="form-group">
      <label>Multiple UI</label>
      <select name="cmd_multipleui">
        <option value="false">false</option>
        <option value="true">true</option>
      </select>
    </div>

    <div class="form-group">
      <label>Trigger UI</label>
      <select name="cmd_triggerui">
        <option value="false">false</option>
        <option value="true">true</option>
      </select>
    </div>

    <div class="form-group">
      <label>Voice Command</label>
      <input type="text" name="cmd_voicecommand" placeholder="Yes/No" />
    </div>

    <div class="form-group">
      <label>Voice Instruction Text</label>
      <input type="text" name="cmd_voice_control_instruction_text" placeholder="e.g. turn on fan" />
    </div>

    <button class="btn" onclick="prevStep()">Back</button>
    <button class="btn" onclick="nextStep()">Next</button>
  </div>
</div>
<!-- Step 2: Command Frame -->
<!--<?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>-->
<div id="step-2" class="form-step">
  <div class="container">

    <input type="hidden" name="id" value="<?php echo $id; ?>" />

    <h2>COMMAND FRAME</h2>

    <div class="form-group">
      <label for="cmd_parametername">Parameter Name</label>
      <input type="text" name="cmd_parametername" />
    </div>

    <div class="form-group">
      <label for="cmd_data_flow_direction">Data Flow Direction</label>
      <select name="cmd_data_flow_direction">
        <option value="App/Server to Device">App/Server to Device</option>
        <option value="Device to App/Server">Device to App/Server</option>
      </select>
    </div>

    <div class="form-group">
      <label for="cmd_frametype">Frame Type</label>
      <select name="cmd_frametype">
        <option value="COMMAND">COMMAND</option>
        <option value="RESPONSE">RESPONSE</option>
        <option value="ERROR">ERROR</option>
      </select>
    </div>

  <!--  <div class="form-group">
      <label for="cmd_framesizeinbytes">Frame Size in Bytes</label>
      <input type="text" name="cmd_framesizeinbytes" />
    </div>

    <div class="form-group">
      <label for="cmd_noofrows">No of Rows</label>
      <input type="text" name="cmd_noofrows" />
    </div>-->
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
      <label for="cmd_frametrigger">Frame Trigger</label>
      <select name="cmd_frametrigger">
        <option value="None">None</option>
        <option value="INCOMING FRAME">INCOMING FRAME</option>
        <option value="Toggle Switch">Toggle Switch</option>
        <!-- Add other options if needed -->
      </select>
    </div>

    <div class="form-group">
      <label for="cmd_multipleui">Multiple UI</label>
      <select name="cmd_multipleui">
        <option value="false">false</option>
        <option value="true">true</option>
      </select>
    </div>

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
                                                                <th>Serial Number of UI<small style="color:red">(ENTER FROM ZERO(0))</small></th>
                                                                <th>AS PER UI VALUES</th>
                                                                <th>UI Page No</th>
                                                                <th>UI Description In/Out</th>
                                                                <th>Slider Step Value</th>
                                                                <th>Default Value</th>
                                                                <th>Developer Notes (If Any)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <!-- Initial row with input fields and select dropdowns -->
                                                            <tr id="row_1">
                                                                <!-- Individual fields with IDs -->
                                                                <td id="sno_1">1</td>
                                                                <td><input type="text" id="startBytePosition_cmd"
                                                                        name="startBytePosition_cmd"></td>
                                                                <td><input type="text" id="endBytePosition_cmd"
                                                                        name="endBytePosition_cmd"></td>


                                                                <td>
                                                                    <select id="fieldDescription_cmd"
                                                                        name="fieldDescription_cmd">
                                                                        <option value="None" selected>None</option>
                                                                        <option value="CommandId">CommandId</option>
                                                                        <option value="Function Code">Function Code</option>
                                                                        <option value="Checksum">Checksum</option>
                                                                        <option value="Terminator">Terminator</option>
                                                                        <option value="ActionId">ActionId</option>
                                                                        <option value="Data">Data</option>
                                                                        <option value="Error Code">Error Code</option>
                                                                        <option value="Error details">Error details</option>
                                                                        <option value="Operation Code">Operation Code</option>
                                                                        <option value="Trigger">Trigger</option>
                                                                    </select>
                                                                </td>


                                                                <td><input type="text" id="numberOfBytes_cmd"
                                                                        name="numberOfBytes_cmd"></td>

                                                                <td><input type="text" id="bitSize_cmd"
                                                                        name="bitSize_cmd">
                                                                </td>
                                                                <td>
                                                                    <select id="dataType_cmd" name="dataType_cmd">
                                                                        <option value="None" selected>None</option>
                                                                        <option value="ASCII">ASCII</option>
                                                                        <option value="Binary">Binary</option>
                                                                        <option value="SignedInt">Signed Int</option>
                                                                        <option value="Float">Float</option>
                                                                        <option value="UnsignedInt">Unsigned Int
                                                                        </option>
                                                                        <option value="String">String</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <select id="valueDefinition_cmd"
                                                                        name="valueDefinition_cmd">
                                                                        <option value="None" selected>None</option>
                                                                        <option value="Fixed value">Fixed Value
                                                                        </option>
                                                                        <option value="Range value">Range Value</option>
                                                                        <option value="Mode value">Mode Value</option>
                                                                        <option value="ASCII">ASCII</option>
                                                                    </select>
                                                                </td>



                                                                <td><input type="text" id="displayValueMin_cmd"
                                                                        name="displayValueMin_cmd"></td>
                                                                <td><input type="text" id="displayValueMax_cmd"
                                                                        name="displayValueMax_cmd"></td>
                                                                <td><input type="text" id="valueInHexMin_cmd"
                                                                        name="valueInHexMin_cmd"></td>
                                                                <td><input type="text" id="valueInHexMax_cmd"
                                                                        name="valueInHexMax_cmd"></td>
                                                                <td>
                                                                    <select id="uiType_cmd" name="uiType_cmd">
                                                                        <option value="None" selected>None</option>
                                                                        <option value="Text">Text
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
                                                                        <option value="CCT Slider">CCT Slider</option>
                                                                        <option value="Saturation Slider">Saturation
                                                                            Slider
                                                                        </option>
                                                                        <option value="Airflow Slider">Airflow Slider</option>
                                                                        <option value="Hue Slider Custom">Hue Slider Custom</option>
                                                                        <option value="Hue Slider">Hue Slider</option>
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
                                                                </td>

                                                                <td><input type="text" id="uiId_cmd" name="uiId_cmd">
                                                                </td>

                                            <td><input type="text" id="asperui_cmd"      name="asperui_cmd"     ></td>
                                            <td><input type="text" id="sno_asperui_cmd"  name="sno_asperui_cmd" ></td>
                                            <td><input type="text" id="asperuivalue_cmd" name="asperuivalue_cmd"></td>


                                                                <td>
                                                                    <select id="uiPageNo_cmd" name="uiPageNo_cmd">
                                                                        <option value="">None</option>
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

                                                                <td>
                                                                    <select id="uiDescriptionInOut_cmd"
                                                                        name="uiDescriptionInOut_cmd">
                                                                        <option value="None" selected>None</option>
                                                                        <option value="Read/Write">Read/Write</option>
                                                                        <option value="Read">Read</option>
                                                                        <option value="Write">Write</option>
                                                                    </select>
                                                                </td>


                                   <td><input type="text" id="sliderStepValue_cmd" name="sliderStepValue_cmd"></td>
                                                                        
                                   <td><input type="text" id="defaultvalue_cmd" name="defaultvalue_cmd"></td>
                                                                        
                                    <td><input type="text" id="developerNotes_cmd" name="developerNotes_cmd"></td>
                                    </tr>
                                                        </tbody>
                                                    </table>
                                                    </tr>
                                                           <button type="button" onclick="addRow()">+</button>
         </div>                                                 
    <button class="btn" type="button" onclick="prevStep()">Back</button>
    <button class="btn" type="button" onclick="nextStep()">Next</button>
</div>
  </div>
<!--<?php echo form_close(); ?>-->

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


           <!-- <h2 style="font-size: 25px; color:#0056b3;">Response Frame Information</h2>-->



            <br><br>
            <div class="commandname">



                <div class="form-group">
                    <label class=" col-sm-2" for="resp_parametername">Parameter Name</label>
                    <small>Ex : speed,power.....</small>
                    <div class="col-sm-4">
                        <p class="error product-error"></p>
                        <input type="text" class="form-control"
                            id="resp_parametername" name="resp_parametername">
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



               <div class="form-group">
            <label class="col-sm-2" for="pwd">Multiple UI</label>
            <div class="col-sm-4">
                <select class="form-control" id="resp_multipleui"
                    name="resp_multipleui">
                    <option value="false">false</option>
                    <option value="true">true</option>
                    
                </select>
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
                        <tbody>
                            <!-- Initial row with input fields and select dropdowns -->
                            <tr id="row_1">
                                <!-- Individual fields with IDs -->
                                <td id="sno_1">1</td>
                                <td><input type="text"
                                        id="startBytePosition_resp"
                                        name="startBytePosition_resp">
                                </td>
                                <td><input type="text" id="endBytePosition_resp"
                                        name="endBytePosition_resp">
                                </td>


                                <td>
                                    <select id="fieldDescription_resp"
                                        name="fieldDescription_resp">
                                        <option value="None" selected>None
                                        </option>
                                        <option value="CommandId">CommandId
                                        </option>
                                        <option value="Function Code">Function
                                            Code
                                        </option>
                                        <option value="Checksum">Checksum
                                        </option>
                                        <option value="Terminator">Terminator
                                        </option>
                                        <option value="ActionId">ActionId
                                        </option>
                                        <option value="Data">Data</option>
                                        <option value="Error Code">Error Code
                                        </option>
                                        <option value="Error details">Error details</option>
                                        <option value="Operation Code">Operation
                                            Code
                                        </option>
                                        <option value="Trigger">Trigger</option>
                                        <option value="SUID">SUID</option>
                                <option value="DUID">DUID</option>
                                <option value="UDP CMD">UDP CMD</option>
                                <option value="ACK">ACK</option>
                                <option value="MEDIA">MEDIA</option>
                                <option value="Frame">Frame</option>
                                <option value="FID">FID</option>
                                <option value="User ID">User ID</option>
                                <option value="SDUID Enable">SDUID Enable</option>
                                    </select>
                                </td>
                                <td><input type="text" id="numberOfBytes_resp"
                                        name="numberOfBytes_resp"></td>
                                <td><input type="text" id="bitSize_resp"
                                        name="bitSize_resp"></td>
                                <td>
                                    <select id="dataType_resp"
                                        name="dataType_resp">
                                        <option value="None" selected>None
                                        </option>
                                        <option value="ASCII">ASCII</option>
                                        <option value="Binary">Binary</option>
                                        <option value="SignedInt">Signed Int
                                        </option>
                                        <option value="Float">Float</option>
                                        <option value="UnsignedInt">Unsigned Int
                                        </option>
                                        <option value="String">String</option>
                                    </select>
                                </td>

                                <td>
                                    <select id="valueDefinition_resp"
                                        name="valueDefinition_resp">
                                        <option value="None" selected>None
                                        </option>
                                        <option value="Fixed value">Fixed Value
                                        </option>
                                        <option value="Range value">Range Value
                                        </option>
                                        <option value="Mode value">Mode Value
                                        </option>
                                        <option value="ASCII">ASCII</option>
                                    </select>
                                </td>

                                <td><input type="text" id="displayValueMin_resp"
                                        name="displayValueMin_resp">
                                </td>
                                <td><input type="text" id="displayValueMax_resp"
                                        name="displayValueMax_resp">
                                </td>
                                <td><input type="text" id="valueInHexMin_resp"
                                        name="valueInHexMin_resp"></td>
                                <td><input type="text" id="valueInHexMax_resp"
                                        name="valueInHexMax_resp"></td>
                                <td>
                                    <select id="uiType_resp" name="uiType_resp">
                                        <option value="None" selected>None
                                        </option>
                                        <option value="Text">
                                            Text</option>
                                        <option value="Toggle Switch">Toggle
                                            Switch</option>
                                        <option value="Slider">Slider</option>
                                        <option value="Brightness Slider">
                                            Brightness
                                            Slider</option>
                                   <option value="Airflow Slider">Airflow Slider</option>
                                        <option value="CCT Slider">CCT Slider
                                        </option>
                                        <option value="Saturation Slider">
                                            Saturation
                                            Slider</option>
                                        <option value="Hue Slider">Hue Slider
                                        </option>
                                 <option value="Hue Slider Custom">Hue Slider Custom</option>
                                        <option value="Hue Circle">Hue Circle
                                        </option>
                                        <option value="Push Button">Push Button
                                            (Big)
                                        </option>
                                        <option value="Drop Down">Drop Down
                                        </option>
                                        <option value="Trigger">Trigger</option>
                                        <option value="Display">Display</option>
                                        <option value="Button">Button</option>
                                        <option value="Auto Trigger With Timer">Auto Trigger With Timer</option>
                                        <option value="Toast Display">Toast Display</option>

                                    </select>
                                </td>


     <td><input type="text" id="uiId_resp"  name="uiId_resp">  </td>
    <td><input type="text" id="asperui_resp" name="asperui_resp"></td>
<td><input type="text" id="sno_asperui_resp" name="sno_asperui_resp"></td>
<td><input type="text" id="asperuivalue_resp" name="asperuivalue_resp"></td>



                                <td>
                                    <select id="uiPageNo_resp"
                                        name="uiPageNo_resp">
                                        <option value="">None</option>
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





                                <td>
                                    <select id="uiDescriptionInOut_resp"
                                        name="uiDescriptionInOut_resp">
                                        <option value="">None</option>
                                        <option value="Read/Write">Read/Write
                                        </option>
                                        <option value="Read">Read</option>
                                        <option value="Write">Write</option>
                                    </select>
                                </td>


                                <td><input type="text" id="sliderStepValue_resp"
                                        name="sliderStepValue_resp">
                                </td>
     <td><input type="text" id="defaultvalue_resp" name="defaultvalue_resp"></td>

     <td>
                            <select id="notification_resp"
                                name="notification_resp">
                                <option value="Disable" selected>Disable</option>
                                <option value="Enable">Enable</option> 
                            </select>
                        </td>

<td><input type="text" id="notification_data_resp" name="notification_data_resp"></td>
                                
                                <td><input type="text" id="developerNotes_resp"
                                        name="developerNotes_resp"></td>
                            </tr>
                        </tbody>
                    </table>
<tr>
    <!--<button type="button" onclick="addRow()">+</button><br><br>-->
                   <!--<button class="btn"type="button" onclick="prevStep()">Back</button>
                   <button class="btn" type="button" onclick="nextStep()" >Next</button>-->
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
  
  /* Submit Button *
 <!-- input[type="submit"] {
      padding: 10px 25px;
      font-size: 16px;
      background-color: #28a745;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 20px;
      display: block;
      margin-left: auto;
      margin-right: auto;
  }-->
  
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
                                                 <!-- <div id="items-dropzone" class="post-dropzone">
  
                                                      <div class="modal-body clearfix">
                                                          <div class="container-fluid">-->
                                                               <div class="container">
  <div class="responsename">
   <div class="form-group">
                                                           <!--   <input type="hidden" name="id" value="<?php echo $id; ?>" --
  
                                                              
                                                                 <h4 style="text-align: center;font-size: 30px; color: #0056b3">ERROR FRAME</h4>
                                                                  
  
  
                                                       <!--       <h2 style="text-align: center;font-size: 25px;color: #0056b3">Error Frame Information</h2>-->
  
                                            
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
                                                                              name="error_parametername">
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
  
  
                                                               <!--   <div class="form-group">
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
                                                                  </div>-->
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
                                                              <option value="false">false</option>
                                                              <option value="true">true</option>
                                                              
                                                          </select>
                                                      </div>
                                                  </div>
  
  
  
  
  
  
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
                                                                        
                                                                          <tbody>
                                                                              <!-- Initial row with input fields and select dropdowns -->
                                                                              <tr id="row_1">
                                                                                  <!-- Individual fields with IDs -->
                                                                                  <td id="sno_1">1</td>
                                                                                  <td><input type="text"
                                                                                          id="startBytePosition_error"
                                                                                          name="startBytePosition_error">
                                                                                  </td>
                                                                                  <td><input type="text"
                                                                                          id="endBytePosition_error"
                                                                                          name="endBytePosition_error">
                                                                                  </td>
  
  
                                                                                  <td>
                                                                                      <select id="fieldDescription_error"
                                                                                          name="fieldDescription_error">
                                                                                          <option value="None" selected>
                                                                                              None
                                                                                          </option>
                                                                                          <option value="CommandId">
                                                                                              CommandId</option>
                                                                                          <option value="Function Code">
                                                                                              Function
                                                                                              Code</option>
                                                                                          <option value="Checksum">
                                                                                              Checksum
                                                                                          </option>
                                                                                          <option value="Terminator">
                                                                                              Terminator
                                                                                          </option>
                                                                                          <option value="ActionId">
                                                                                              ActionId
                                                                                          </option>
                                                                                          <option value="Data">Data
                                                                                          </option>
                                                                                          <option value="Error Code">Error
                                                                                              Code
                                                                                          </option>
                                                                                          <option value="Error details">Error details</option>
                                                                                          <option value="Operation Code">
                                                                                              Operation
                                                                                              Code</option>
                                                                                           <option value="Trigger">Trigger</option>
                                                                                      </select>
                                                                                  </td>
                                                                                  <td><input type="text"
                                                                                          id="numberOfBytes_error"
                                                                                          name="numberOfBytes_error"></td>
                                                                                  <td><input type="text"
                                                                                          id="bitSize_error"
                                                                                          name="bitSize_error">
                                                                                  </td>
                                                                                  <td>
                                                                                      <select id="dataType_error"
                                                                                          name="dataType_error">
                                                                                          <option value="None" selected>
                                                                                              None
                                                                                          </option>
                                                                                          <option value="ASCII">ASCII
                                                                                          </option>
                                                                                          <option value="Binary">Binary
                                                                                          </option>
                                                                                          <option value="SignedInt">Signed
                                                                                              Int
                                                                                          </option>
                                                                                          <option value="Float">Float
                                                                                          </option>
                                                                                          <option value="UnsignedInt">
                                                                                              Unsigned
                                                                                              Int
                                                                                          </option>
                                                                                          <option value="String">String</option>
                                                                                      </select>
                                                                                  </td>
  
                                                                                  <td>
                                                                                      <select id="valueDefinition_error"
                                                                                          name="valueDefinition_error">
                                                                                          <option value="None" selected>
                                                                                              None
                                                                                          </option>
                                                                                          <option value="Fixed Value">
                                                                                              Fixed value</option>
                                                                                          <option value="Range Value">
                                                                                              Range
                                                                                              value
                                                                                          </option>
                                                                                          <option value="Mode Value">Mode
                                                                                              value
                                                                                          </option>
                                                                                          <option value="ASCII">ASCII
                                                                                          </option>
                                                                                      </select>
                                                                                  </td>
  
                                                                                  <td><input type="text"
                                                                                          id="displayValueMin_error"
                                                                                          name="displayValueMin_error">
                                                                                  </td>
                                                                                  <td><input type="text"
                                                                                          id="displayValueMax_error"
                                                                                          name="displayValueMax_error">
                                                                                  </td>
                                                                                  <td><input type="text"
                                                                                          id="valueInHexMin_error"
                                                                                          name="valueInHexMin_error"></td>
                                                                                  <td><input type="text"
                                                                                          id="valueInHexMax_error"
                                                                                          name="valueInHexMax_error"></td>
                                                                                  <td>
                                                                                      <select id="uiType_error"
                                                                                          name="uiType_error">
                                                                                          <option value="None" selected>
                                                                                              None
                                                                                          </option>
                                                                                          <option value="Text">
                                                                                              Text</option>
                                                                                          <option value="Toggle Switch">
                                                                                              Toggle Switch</option>
                                                                            <option value="Notification">Notification</option>
                                                                            <option value="console">console</option>
                                                                          <option value="Enter Text">Enter Text</option>
                                                                                          <option value="Slider">Slider
                                                                                          </option>
                                                                                          <option
                                                                                              value="Brightness Slider">
                                                                                              Brightness Slider</option>
                                                                            <option value="Airflow Slider">Airflow Slider</option>
                                                                                          <option value="CCT Slider">CCT
                                                                                              Slider
                                                                                          </option>
                                                                                          <option
                                                                                              value="Saturation Slider">
                                                                                              Saturation Slider</option>
                                                                                          <option value="Hue Slider">Hue
                                                                                              Slider
                                                                                          </option>
                                                                           <option value="Hue Slider Custom">Hue Slider Custom</option>
                                                                                          <option value="Hue Circle">Hue
                                                                                              Circle
                                                                                          </option>
                                                                                          <option value="Push Button">Push
                                                                                              Button
                                                                                              (Big)</option>
                                                                                          <option value="Drop Down">Drop
                                                                                              Down
                                                                                          </option>
                                                                                          <option value="Trigger">Trigger
                                                                                          </option>
                                                                                          <option value="Display">Display
                                                                                          </option>
                                                                                          <option value="Button">Button
                                                                                          </option>
                                                                                          <option value="Toast Display">Toast Display</option>
  
                                                                                      </select>
                                                                                  </td>
  
  
  
                <td><input type="text" id="uiId_error"  name="uiId_error"></td>
  
                  <td><input type="text"  id="asperui_error"  name="asperui_error"> </td>
                  <td><input type="text"  id="sno_asperui_error"  name="sno_asperui_error"> </td>
                  <td><input type="text" id="asperuivalue_error"  name="asperuivalue_error">  </td>
  
                                                                                  <td>
                                                                                      <select id="uiPageNo_error"
                                                                                          name="uiPageNo_error">
                                                                                          <option value="">None</option>
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
  
  
  
  
                                                                                  <td>
                                                                                      <select
                                                                                          id="uiDescriptionInOut_error"
                                                                                          name="uiDescriptionInOut_error">
                                                                                          <option value="None" selected>
                                                                                              None
                                                                                          </option>
                                                                                          <option value="Read/Write">
                                                                                              Read/Write</option>
                                                                                          <option value="Read">Read
                                                                                          </option>
                                                                                          <option value="Write">Write
                                                                                          </option>
                                                                                      </select>
                                                                                  </td>
  
  
                                                                                  <td><input type="text"
                                                                                          id="sliderStepValue_error"
                                                                                          name="sliderStepValue_error">
                                                                                  </td>
                                                                                  
                            <td><input type="text" id="defaultvalue_error" name="defaultvalue_error"></td>                            
                                                                                  
                                                                                  <td><input type="text"
                                                                                          id="developerNotes_error"
                                                                                          name="developerNotes_error">
                                                                                  </td>
                                                                              </tr>
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
        function validatePageName() {
            var input = document.getElementById("pagenames").value;
            if (input === "Page0") {
                alert("Page0 as Page name is invalid");
            }
        }
    </script>
    
    <script> 
    document.getElementById("currentDate").value = new Date().toISOString().split('T')[0];
</script>

                    <script type="text/javascript">
 
    
 

                        /***************additional_table******************************************/


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

 
















                        /******************************************************************/

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



                        /*
                        function respupdateTable() {
                        
                        clearTimeout(debounceTimer);
                        
                            var frameSize = parseInt(document.getElementById('resp_noofrows').value);
                            var table = document.getElementById('responseTable');
                        
                            debounceTimer = setTimeout(function() {
                                   
                            while (table.rows.length > 2) {
                                table.deleteRow(1);
                            }
                                for (var i = 0; i < frameSize; i++) {
                                respaddRow(i + 1);     }
                        
                                }, 500); 
                        }
                        */


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






                        /********************************************************************/
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





                        /*
                        function errorupdateTable() {
                        
                        clearTimeout(debounceTimer);
                        
                            var frameSize = parseInt(document.getElementById('error_noofrows').value);
                            var table = document.getElementById('errorTable');
                        
                            debounceTimer = setTimeout(function() {
                                   
                            while (table.rows.length > 2) {
                                table.deleteRow(1);
                            }
                                for (var i = 0; i < frameSize; i++) {
                                erroraddRow(i + 1);     }
                        
                                }, 500); 
                        }
                        */



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

                        function showImagePreview(input) {
                            var preview = document.getElementById('brandlogo-preview');
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
    if (currentStep < 4) showStep(currentStep + 1);
  }

  function prevStep() {
    if (currentStep > 1) showStep(currentStep - 1);
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
                        </script>

 <?php echo form_close(); ?>
</body>
</html>


          
