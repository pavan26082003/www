
<div class="modal-center-box">
<div class="modal-box">
  <!-- all your form and content goes here -->
</div>

<button class="back-button" onclick="history.back()">← Back</button>
<div class="modal" id="errorModal">
    <!-- modal content -->
</div>



<style>

body {
  margin: 0;
  padding: 0;
  background: rgba(0, 0, 0, 0.35); /* dim background */
  backdrop-filter: blur(10px); /* blur effect */
  -webkit-backdrop-filter: blur(10px);
  height: 100vh;
  overflow: hidden;
  font-family: 'Segoe UI', sans-serif;
}
.modal-box {
  background-color: white;
  border-radius: 12px;
  padding: 30px;
  width: 80%;
  max-height: 90vh; /* Ensures it fits the screen */
  overflow-y: auto; /* Allows scrolling if content overflows */
  box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
}

.back-button {
    position: absolute;
    top: 20px;
    right: 30px;
    background-color: transparent;
    border: none;
    color: #007bff;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    z-index: 10000;
    transition: color 0.3s;
}

.back-button:hover {
    color: #0056b3;
}

/* Disable left sidebar (like Home, Mails, Products) while modal is active */
.app-sidebar, .sidebar, .main-sidebar {
    pointer-events: none !important;
    opacity: 0.4;
}

/* Disable navbar interactivity and push it behind modal */
#default-navbar {
    pointer-events: none !important;
    opacity: 0.5;
    z-index: 1 !important;
    position: relative;
}

/* Keep modal above everything */
.modal-center-box {
    z-index: 9999 !important;
    box-shadow: 0 0 40px rgba(0, 0, 0, 0.25);
    border: 2px solid white;

}

.modal-center-box {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  width: 95%;
  max-width: 900px;
  max-height: 90vh;
  padding: 40px;
  overflow-y: auto;
  border-radius: 20px;
  box-shadow: 0 0 30px rgba(0, 0, 0, 0.25);
}
</style>
<?php
// You can include processing logic or database handling here
?>


<style>
  body {
      background-color: #f4f6f8;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 30px;
      color: #333;
  }

  .container, .container-fluid, .form-container {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  }

  h1, h2, h4 {
      text-align: center;
      color: #0077cc;
      margin-bottom: 20px;
  }
</style>

                                                <div id="items-dropzone" class="post-dropzone">

                                                    <div class="modal-body clearfix">
                                                        <div class="container-fluid">
                                                            <input type="hidden" name="id" value="" />

                                                            <h4 style="text-align: center;"
                                                                style="font-size: 30px; color: green;">
                                                                ERROR
                                                                FRAME</h4>


                                                            <h2 style="text-align: center;" style="font-size: 25px;">Error Frame Information</h2>



                                                            <br><br>
                                                            <div class="commandname">

<style>
    .form-container {
        background: #ffffff;
        padding: 40px 50px; display: flex; flex-direction: column; justify-content: center; align-items: center;
        border-radius: 16px;
        max-width: 900px;
        margin: 40px auto;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease-in-out;
    
    
    align-items: center;
    }


    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 24px;
    }

    .form-group label {
        font-size: 16px;
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
    }

    .form-control,
    select {
        padding: 12px 14px;
        font-size: 15px;
        border-radius: 10px;
        border: 1px solid #ccd6dd;
        background-color: #f5f7fa;
        transition: 0.3s ease;
    }

    .form-control:focus,
    select:focus {
        border-color: #5b9bd5;
        background-color: #fff;
        box-shadow: 0 0 5px rgba(91, 155, 213, 0.5);
        outline: none;
    }

    textarea.form-control {
        min-height: 120px;
    }

    h2, h4 {
        font-size: 24px;
        color: #0077cc;
        
        margin-bottom: 20px;
    }

    .btn, button {
        background-color: #0077cc;
        color: #fff;
        padding: 12px 26px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn:hover, button:hover {
        background-color: #005fa3;
        transform: scale(1.05);
    }

    .table th {
        background-color: #f0f4f8;
        font-size: 14px;
        color: #333;
        padding: 12px;
    }

    .table td {
        padding: 10px;
        background-color: #ffffff;
        border-color: #e1e1e1;
    }

    .commandname {
        background-color: #fefefe;
        border-left: 4px solid #0077cc;
        padding: 20px;
        margin-top: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
    }

    #submitBtn {
        margin-top: 30px;
        display: block;
    }


@media (max-width: 768px) {
    .form-container {
        padding: 20px;
        width: 90%;
    }

    .form-control,
    select,
    textarea {
        width: 100% !important;
        font-size: 16px;
    }

    .table {
        font-size: 12px;
        overflow-x: auto;
        display: block;
    }

    .btn, button {
        width: 100%;
    }

    h2, h4 {
        font-size: 22px !important;
    }
}

<style>
/* Improved mobile responsiveness */
@media screen and (max-width: 768px) {
  .form-group {
    flex-direction: column;
    align-items: flex-start !important;
  }

  .form-group label {
    text-align: left !important;
    width: 100% !important;
    margin-bottom: 6px;
  }

  .form-group .col-sm-2,
  .form-group .col-sm-4,
  .form-group .col-sm-10 {
    width: 100% !important;
    margin: 0 !important;
  }

  .form-control,
  select,
  textarea {
    width: 100% !important;
    box-sizing: border-box;
  }

  .table-responsive {
    overflow-x: auto;
  }

  table {
    width: 100%;
    display: block;
    overflow-x: auto;
  }

  .btn, button {
    width: 100%;
    margin-bottom: 10px;
  }

  h2, h4 {
    font-size: 20px;
    text-align: center;
  }
}
</style>

</style>
     <form id="errorForm" enctype="multipart/form-data">

                                                           <!--     <div class="form-group">
                                                                    <label class=" col-sm-2" for="error_parametername">Parameter
                                                                        Name</label>
                                                                    <small>Ex : Speed,Power..</small>
                                                                    <div class="col-sm-4">
                                                                        <p class="error product-error"></p>
                                                                        <input type="text" class="form-control"
                                                                            id="error_parametername"
                                                                            name="error_parametername" value="Power" readonly>
                                                                    </div>
                                                                </div>-->
                                                                                                                            <div class="commandname">
                                                                                                                            



                                                                <div class="form-group">
                                                                    <label class=" col-sm-2" for="error_parametername">Parameter
                                                                        Name</label>
                                                                    <small>Ex : Speed,Power..</small>
                                                                    <div class="col-sm-4">
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
                                                                            <option value="Toggle Swne</option>
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


                                                                    
<div class="form-group">
  <label for="brandlogo">Upload Brand Logo</label>
  <input type="file" id="brandlogo" name="brandlogo" accept="image/*" onchange="showImagePreview(this)">
  <img id="brandlogo-preview" class="preview-img" style="display:none; margin-top:10px; max-width:200px;" />
</div>
<div style="text-align: center; margin-top: 20px;">
    
<input type="Upload" value="Upload" name="submit" class="btn" 
<input type="button" value="Upload" id="uploadBtn" class="btn btn-primary" style="padding: 4px 12px; font-size: 12px; background-color: blue; color: white; border: none; border-radius: 4px;" / style="padding: 6px 16px; font-size: 14px; background-color: blue; color: white; border: none; border-radius: 6px;" />
</div>-->


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
                                                           <button type="button" onclick="erroraddRow()">+</button>
                                                           </tr>

                                                                </div>
                                                            </div>
                                                        </div>





 
                                                        
                                                    </div>
                                                     
                    
                                               <div style="text-align: center; margin-top: 30px;">
    <button type="submit" name="action" value="save" class="btn btn-primary" style="width: 140px; margin: 10px;">
        Save
    </button>
<button type="button" id="finalSubmitBtn" class="btn btn-primary" style="width: 140px; margin: 10px; white-space: nowrap;">
    Final Submit
</button>

</div>

    </form>



                    <!--div class="modal-footer">   
            <button type="button" class="btn btn-primary" id="save-btn"><span data-feather="check-circle" class="icon-16"></span> </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div-->
        <script>
    var notificationOptions = {
        type: 'info',
        message: $('#error_appnotification').val() || 'No message provided',
        delay: 3000
    };
    <script>
    function disableForm() {
        const form = document.querySelector("form");
        const inputs = form.querySelectorAll("input, select, textarea, button");
        inputs.forEach(element => {
            element.disabled = true;
            element.style.backgroundColor = "#f0f0f0";
            element.style.cursor = "not-allowed";
        });

        // Optional: Overlay
        const overlay = document.createElement("div");
        overlay.style.position = "fixed";
        overlay.style.top = 0;
        overlay.style.left = 0;
        overlay.style.width = "100%";
        overlay.style.height = "100%";
        overlay.style.backgroundColor = "rgba(255, 255, 255, 0.6)";
        overlay.style.zIndex = 9999;
        document.body.appendChild(overlay);
    }

    // Attach to Save button
    document.addEventListener("DOMContentLoaded", function () {
        const saveBtn = document.querySelector("#saveBtn");
        if (saveBtn) {
            saveBtn.addEventListener("click", function () {
                disableForm();
                alert("Form saved successfully!");
            });
        }
    });
</script>

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



<script>
document.getElementById('uploadBtn').addEventListener('click', function() {
    const fileInput = document.getElementById('brandlogo');
    const formData = new FormData();
    formData.append('brandlogo', fileInput.files[0]);

    fetch('upload_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        alert("Logo uploaded successfully!");
        // optionally: document.getElementById('brandlogo-preview').src = ...
    })
    .catch(err => {
        alert("Upload failed!");
        console.error(err);
    });
});
</script>


<!--<script>
document.getElementById('finalSubmitBtn').addEventListener('click', function () {
    const form = document.getElementById('errorForm');
    const formData = new FormData(form);

    fetch('error_submit_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        alert("✅ Your form submitted successfully!");
        // Optionally: close modal or clear form
        // document.getElementById('errorForm').reset();
        // document.querySelector('.modal').style.display = 'none';
    })
    .catch(err => {
        alert("❌ Submission failed. Try again.");
        console.error(err);
    });
});
</script>-->

<script>
function showImagePreview(input) {
  const file = input.files[0];
  const preview = document.getElementById('brandlogo-preview');

  if (file && preview) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
}
</script>

</div>
<!--
<style>
html, body {
    height: auto !important;
    min-height: 100vh;
    overflow-y: auto !important;
}
.container, .container-fluid, .form-container {
    padding-bottom: 150px !important;
}
</style>z

<?php
// You can include processing logic or database handling here
?>


<style>
  body {
      background-color: #f4f6f8;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 30px;
      color: #333;
  }

  .container, .container-fluid, .form-container {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  }

  h1, h2, h4 {
      text-align: center;
      color: #0077cc;
      margin-bottom: 20px;
  }
</style>

                                                <div id="items-dropzone" class="post-dropzone">

                                                    <div class="modal-body clearfix">
                                                        <div class="container-fluid">
                                                            <input type="hidden" name="id" value="" />

                                                            <h4 style="text-align: center;"
                                                                style="font-size: 30px; color: green;">
                                                                ERROR
                                                                FRAME</h4>


                                                            <h2 style="text-align: center;" style="font-size: 25px;">Error Frame Information</h2>



                                                            <br><br>
                                                            <div class="commandname">

<style>
    .form-container {
        background: #ffffff;
        padding: 40px 50px; display: flex; flex-direction: column; justify-content: center; align-items: center;
        border-radius: 16px;
        max-width: 900px;
        margin: 40px auto;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease-in-out;
    
    
    align-items: center;
    }


    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 24px;
    }

    .form-group label {
        font-size: 16px;
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
    }

    .form-control,
    select {
        padding: 12px 14px;
        font-size: 15px;
        border-radius: 10px;
        border: 1px solid #ccd6dd;
        background-color: #f5f7fa;
        transition: 0.3s ease;
    }

    .form-control:focus,
    select:focus {
        border-color: #5b9bd5;
        background-color: #fff;
        box-shadow: 0 0 5px rgba(91, 155, 213, 0.5);
        outline: none;
    }

    textarea.form-control {
        min-height: 120px;
    }

    h2, h4 {
        font-size: 24px;
        color: #0077cc;
        
        margin-bottom: 20px;
    }

    .btn, button {
        background-color: #0077cc;
        color: #fff;
        padding: 12px 26px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn:hover, button:hover {
        background-color: #005fa3;
        transform: scale(1.05);
    }

    .table th {
        background-color: #f0f4f8;
        font-size: 14px;
        color: #333;
        padding: 12px;
    }

    .table td {
        padding: 10px;
        background-color: #ffffff;
        border-color: #e1e1e1;
    }

    .commandname {
        background-color: #fefefe;
        border-left: 4px solid #0077cc;
        padding: 20px;
        margin-top: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
    }

    #submitBtn {
        margin-top: 30px;
        display: block;
    }


@media (max-width: 768px) {
    .form-container {
        padding: 20px;
        width: 90%;
    }

    .form-control,
    select,
    textarea {
        width: 100% !important;
        font-size: 16px;
    }

    .table {
        font-size: 12px;
        overflow-x: auto;
        display: block;
    }

    .btn, button {
        width: 100%;
    }

    h2, h4 {
        font-size: 22px !important;
    }
}

<style>
/* Improved mobile responsiveness */
@media screen and (max-width: 768px) {
  .form-group {
    flex-direction: column;
    align-items: flex-start !important;
  }

  .form-group label {
    text-align: left !important;
    width: 100% !important;
    margin-bottom: 6px;
  }

  .form-group .col-sm-2,
  .form-group .col-sm-4,
  .form-group .col-sm-10 {
    width: 100% !important;
    margin: 0 !important;
  }

  .form-control,
  select,
  textarea {
    width: 100% !important;
    box-sizing: border-box;
  }

  .table-responsive {
    overflow-x: auto;
  }

  table {
    width: 100%;
    display: block;
    overflow-x: auto;
  }

  .btn, button {
    width: 100%;
    margin-bottom: 10px;
  }

  h2, h4 {
    font-size: 20px;
    text-align: center;
  }
}
</style>

</style>

                                                                <div class="form-group">
                                                                    <label class=" col-sm-2" for="error_parametername">Parameter
                                                                        Name</label>
                                                                    <small>Ex : Speed,Power..</small>
                                                                    <div class="col-sm-4">
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


                                                                    
<div class="form-group">
  <label for="brandlogo">Upload Brand Logo</label>
  <input type="file" id="brandlogo" name="brandlogo" accept="image/*" onchange="showImagePreview(this)">
  <img id="brandlogo-preview" class="preview-img" style="display:none; margin-top:10px; max-width:200px;" />
</div>
<div style="text-align: center; margin-top: 20px;">
    <input type="submit" value="Upload" name="submit" class="btn" style="padding: 4px 12px; font-size: 12px; background-color: blue; color: white; border: none; border-radius: 4px;" / style="padding: 6px 16px; font-size: 14px; background-color: blue; color: white; border: none; border-radius: 6px;" />
</div>


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
                                                                     <!--         <tr id="row_1">
                                                                           <!--       <!-- Individual fields with IDs -->
                                                                           <!--       <td id="sno_1">1</td>
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





 
                                                        
                                                    </div>
                                                     
                    
    
<div style="text-align: center; margin-top: 40px;">
        <button type="submit" class="btn">Final Submit</button>
    </div>
    </form>



                    <!--div class="modal-footer">   
            <button type="button" class="btn btn-primary" id="save-btn"><span data-feather="check-circle" class="icon-16"></span> </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div
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






<script>
function showImagePreview(input) {
  const file = input.files[0];
  const preview = document.getElementById('brandlogo-preview');

  if (file && preview) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
}
</script>
<!--<?php
// Optional PHP logic
?>
<!DOCTYPE html>
<html>
<head>

<style>
  body {
      background-color: #f4f6f8;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 30px;
      color: #333;
  }

  .container, .container-fluid, .form-container {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  }

  h1, h2, h4 {
      text-align: center;
      color: #0077cc;
      margin-bottom: 20px;
  }
</style>

                                                <div id="items-dropzone" class="post-dropzone">

                                                    <div class="modal-body clearfix">
                                                        <div class="container-fluid">
                                                            <input type="hidden" name="id" value="" />

                                                            <h4 style="text-align: center;"
                                                                style="font-size: 30px; color: green;">
                                                                ERROR
                                                                FRAME</h4>


                                                            <h2 style="text-align: center;" style="font-size: 25px;">Error Frame Information</h2>



                                                            <br><br>
                                                            <div class="commandname">

<style>
    .form-container {
        background: #ffffff;
        padding: 40px 50px; display: flex; flex-direction: column; justify-content: center; align-items: center;
        border-radius: 16px;
        max-width: 900px;
        margin: 40px auto;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease-in-out;
    
    
    align-items: center;
    }


    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 24px;
    }

    .form-group label {
        font-size: 16px;
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
    }

    .form-control,
    select {
        padding: 12px 14px;
        font-size: 15px;
        border-radius: 10px;
        border: 1px solid #ccd6dd;
        background-color: #f5f7fa;
        transition: 0.3s ease;
    }

    .form-control:focus,
    select:focus {
        border-color: #5b9bd5;
        background-color: #fff;
        box-shadow: 0 0 5px rgba(91, 155, 213, 0.5);
        outline: none;
    }

    textarea.form-control {
        min-height: 120px;
    }

    h2, h4 {
        font-size: 24px;
        color: #0077cc;
        
        margin-bottom: 20px;
    }

    .btn, button {
        background-color: #0077cc;
        color: #fff;
        padding: 12px 26px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn:hover, button:hover {
        background-color: #005fa3;
        transform: scale(1.05);
    }

    .table th {
        background-color: #f0f4f8;
        font-size: 14px;
        color: #333;
        padding: 12px;
    }

    .table td {
        padding: 10px;
        background-color: #ffffff;
        border-color: #e1e1e1;
    }

    .commandname {
        background-color: #fefefe;
        border-left: 4px solid #0077cc;
        padding: 20px;
        margin-top: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
    }

    #submitBtn {
        margin-top: 30px;
        display: block;
    }


@media (max-width: 768px) {
    .form-container {
        padding: 20px;
        width: 90%;
    }

    .form-control,
    select,
    textarea {
        width: 100% !important;
        font-size: 16px;
    }

    .table {
        font-size: 12px;
        overflow-x: auto;
        display: block;
    }

    .btn, button {
        width: 100%;
    }

    h2, h4 {
        font-size: 22px !important;
    }
}

<style>
/* Improved mobile responsiveness */
@media screen and (max-width: 768px) {
  .form-group {
    flex-direction: column;
    align-items: flex-start !important;
  }

  .form-group label {
    text-align: left !important;
    width: 100% !important;
    margin-bottom: 6px;
  }

  .form-group .col-sm-2,
  .form-group .col-sm-4,
  .form-group .col-sm-10 {
    width: 100% !important;
    margin: 0 !important;
  }

  .form-control,
  select,
  textarea {
    width: 100% !important;
    box-sizing: border-box;
  }

  .table-responsive {
    overflow-x: auto;
  }

  table {
    width: 100%;
    display: block;
    overflow-x: auto;
  }

  .btn, button {
    width: 100%;
    margin-bottom: 10px;
  }

  h2, h4 {
    font-size: 20px;
    text-align: center;
  }
}
</style>

</style>

                                                                <div class="form-group">
                                                                    <label class=" col-sm-2" for="error_parametername">Parameter
                                                                        Name</label>
                                                                    <small>Ex : Speed,Power..</small>
                                                                    <div class="col-sm-4">
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


                                                                    
<div class="form-group">
  <label for="brandlogo">Upload Brand Logo</label>
  <input type="file" id="brandlogo" name="brandlogo" accept="image/*" onchange="showImagePreview(this)">
  <img id="brandlogo-preview" class="preview-img" style="display:none; margin-top:10px; max-width:200px;" />
</div>
<div style="text-align: center; margin-top: 20px;">
    <input type="submit" value="Upload" name="submit" class="btn" style="padding: 4px 12px; font-size: 12px; background-color: blue; color: white; border: none; border-radius: 4px;" / style="padding: 6px 16px; font-size: 14px; background-color: blue; color: white; border: none; border-radius: 6px;" />
</div>


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
                                                                          <!--  <tr id="row_1">
                                                                                <!-- Individual fields with IDs -->
                                                                           <!--     <td id="sno_1">1</td>
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





 
                                                        
                                                    </div>
                                                     
                    
    
<div style="text-align: center; margin-top: 40px;">
        <button type="submit" class="btn">Final Submit</button>
    </div>
    </form>



                    <!--div class="modal-footer">   
            <button type="button" class="btn btn-primary" id="save-btn"><span data-feather="check-circle" class="icon-16"></span> </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div-
        <script>
<!--        function validatePageName() {
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






<script>
function showImagePreview(input) {
  const file = input.files[0];
  const preview = document.getElementById('brandlogo-preview');

  if (file && preview) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
}
</script>

</head>
<body>
</body>
</html>
<!--
<style>
  body {
      background-color: #f4f6f8;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 30px;
      color: #333;
  }

  .container {
      max-width: 700px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }
</style>
<style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f0f4f8, #d9e2ec);
      padding: 40px 20px;
    }
.modal-body {
  max-height: 85vh;
  overflow-y: auto;
  padding-right: 15px;
}a
    .form-wrapper {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px 50px;
      border-radius: 16px;
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
      max-width: 800px;
      width: 100%;
      backdrop-filter: blur(10px);
    }

    h1, h2 {
      text-align: center;
      color: blue;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 8px;
      color: #333;
    }

    input[type="text"],
    input[type="number"],
    input[type="file"],
    select,
    textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.3s ease;
    }

    input:focus,
    select:focus,
    textarea:focus {
      border-color: blue;
      box-shadow: 0 0 8px rgba(42, 127, 46, 0.2);
      outline: none;
    }
    #submitBtn {
    background-color: #1a73e8;
    padding: 12px 30px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    color: #fff;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
  }
    .submit-btn {
      display: block;
      width: 100%;
      padding: 14px;
      background-color:blue;
      color: #fff;
      border: none;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .submit-btn:hover {
      background-color: #256e27;
      transform: scale(1.03);
    }

    .preview-image {
      margin-top: 15px;
      border: 1px solid #ccc;
      padding: 10px;
      border-radius: 6px;
      width: 100%;
      max-width: 200px;
      display: none;
    }

    @media (max-width: 768px) {
      .form-wrapper {
        padding: 20px;
      }
    }
</style>
<style>
  body {
      background-color: #f4f6f8;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 30px;
      color: #333;
  }

  .container {
      max-width: 700px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }
</style>
<style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f0f4f8, #d9e2ec);
      padding: 40px 20px;
    }

    .form-wrapper {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px 50px;
      border-radius: 16px;
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
      max-width: 800px;
      width: 100%;
      backdrop-filter: blur(10px);
    }

    h1, h2 {
      text-align: center;
      color: blue;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 8px;
      color: #333;
    }

    input[type="text"],
    input[type="number"],
    input[type="file"],
    select,
    textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.3s ease;
    }

    input:focus,
    select:focus,
    textarea:focus {
      border-color: blue;
      box-shadow: 0 0 8px rgba(42, 127, 46, 0.2);
      outline: none;
    }

    .submit-btn {
      display: block;
      width: 100%;
      padding: 14px;
      background-color: blue;
      color: #fff;
      border: none;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .submit-btn:hover {
      background-color: #256e27;
      transform: scale(1.03);
    }

    .preview-image {
      margin-top: 15px;
      border: 1px solid #ccc;
      padding: 10px;
      border-radius: 6px;
      width: 100%;
      max-width: 200px;
      display: none;
    }

    @media (max-width: 768px) {
      .form-wrapper {
        padding: 20px;
      }
    }
</style>
<style>
  body {
      background-color: #f4f6f8;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 30px;
      color: #333;
  }

  .container {
      max-width: 700px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }
</style>
<style>
  @media (max-width: 768px) {
    .container {
      padding: 15px !important;
    }

    .form-group {
      flex-direction: column;
      align-items: flex-start;
    }

    label {
      width: 100%;
      text-align: left;
    }

    input, select, textarea {
      width: 100%;
    }

    h1 {
      font-size: 24px;
    }
  }
</style>
<style>
  body {
      background-color: #f4f6f8;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 30px;
      color: #333;
  }

  .container {
      max-width: 700px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }
</style>
<style>
  body {
    margin: 0;
    padding: 30px;
    background-color: #f1f3f5;
    font-family: 'Poppins', sans-serif;
  }

  .form-wrapper {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    padding: 30px 40px;
    max-width: 850px;
    margin: 0 auto;
  }

  h1, h2 {
    color: #003cff;
    text-align: center;
    margin-bottom: 20px;
  }

  @media (max-width: 768px) {
    .form-wrapper {
      padding: 20px;
    }
    h1 {
      font-size: 22px;
    }
  }
</style>
<script>
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
       }
     }
 
     function handleSelect(select) {
       const url = select.value;
       if (url) {
         window.location.href = url;
       }
     }
   </script>
<?php
// You can include processing logic or database handling here
?>




                                                <div id="items-dropzone" class="post-dropzone">

                                                    <div class="modal-body clearfix">
                                                        <div class="container-fluid">
                                                            <input type="hidden" name="id" value="" />

                                                            <h4 style="text-align: center;"
                                                                style="font-size: 30px; color: green;">
                                                                ERROR
                                                                FRAME</h4>


                                                            <h2 style="text-align: center;" style="font-size: 25px;">Error Frame Information</h2>



                                                            <br><br>
                                                            <div class="commandname">



</style>

                                                                <div class="form-group">
                                                                    <label class=" col-sm-2" for="error_parametername">Parameter
                                                                        Name</label>
                                                                    <small>Ex : Speed,Power..</small>
                                                                    <div class="col-sm-4">
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


                                                                    
<div class="form-group">
  <label for="brandlogo">Upload Brand Logo</label>
  <input type="file" id="brandlogo" name="brandlogo" accept="image/*" onchange="showImagePreview(this)">
  <img id="brandlogo-preview" class="preview-img" style="display:none; margin-top:10px; max-width:200px;" />
</div>
<div style="text-align: center; margin-top: 20px;">
    <input type="submit" value="Upload" name="submit" class="btn" style="padding: 4px 12px; font-size: 12px; background-color: blue; color: white; border: none; border-radius: 4px;" / style="padding: 6px 16px; font-size: 14px; background-color: blue; color: white; border: none; border-radius: 6px;" />
</div>


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
                                                                            <!- <tr id="row_1">
                                                                                <!-- Individual fields with IDs -->
                                                                            <!--    <td id="sno_1">1</td>
                                                                            <!--    <td><input type="text"
                                                                                        id="startBytePosition_error"
                                                                                        name="startBytePosition_error">
                                                                                </td>
                                                                                <td><input type="text"
                                                                                        id="endBytePosition_error"
                                                                                        name="endBytePosition_error">
                                                                                </td>


                                                                             - Initial row with input fields and select dropdowns -->
                                                                              <td>
                                                                               <!--     <select id="fieldDescription_error"
                                                                         <!--               name="fieldDescription_error">
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





 
                                                        
                                                    </div>
                                                     
                    
    
<div style="text-align: center; margin-top: 40px;">
        <button type="submit" class="btn">Final Submit</button>
    </div>
    </form>



                    <!--div class="modal-footer">   
            <button type="button" class="btn btn-primary" id="save-btn"><span data-feather="check-circle" class="icon-16"></span> </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div-->
        
    
    

                    
