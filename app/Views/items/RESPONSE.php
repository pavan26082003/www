

 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Page</title>
    <style>
 
/* Disable only interaction, keep it visible */
.sidebar,
.main-sidebar,
aside,
#sidebar,
.navbar,
header {
    pointer-events: none !important; /* Disable click */
    user-select: none !important;    /* Prevent text selection */
    opacity: 1 !important;           /* Keep visible */
}



        body {
            background-color: #f4f6f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px;
            color: #333;
        }
        h4 {
            text-align: center;
            font-size: 26px;
            color: #007bff;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            max-width: 400px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #fff;
        }
        textarea {
            resize: vertical;
        }
        .btn {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            background-color: #28a745;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #218838;
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
  background: rgba(0, 0, 0, 0.35); /* dim background */
  backdrop-filter: blur(10px); /* blur effect */
  -webkit-backdrop-filter: blur(10px);
  height: 100vh;
  overflow: hidden;
  font-family: 'Segoe UI', sans-serif;
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

.modal-center-box {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  width: 95%;
  max-width: 900px;
  max-height: 95vh;
  padding: 40px;
  overflow-y: auto;
  border-radius: 20px;
  box-shadow: 0 0 30px rgba(0, 0, 0, 0.25);
}
<style>
/* Force hide global layout elements */
.sidebar, .navbar, header, .topbar, .main-header, .main-sidebar, .content-header {
    display: none !important;
}

/* Expand modal form to center */
body {
    background: #f3f3f3;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

.modal-container {
    position: relative;
    top: 30px;
    margin: 0 auto;
    max-width: 900px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 0 25px rgba(0,0,0,0.1);
    padding: 30px;
    z-index: 9999;
}
</style>

</style>

</head>
<body>
<div class="modal-container"> 
<div style="margin-left: 0 !important;">

<div class="modal-center-box">
    <div class="container">
          <div id="items-dropzone" class="post-dropzone">

                                            <div class="modal-body clearfix">
                                                <div class="container-fluid">
                                                    <input type="hidden" name="id" value="" />

                                                    <h4 style="text-align: center;font-size: 30px; color:#007bff;">
                                                        Response
                                                        FRAME
                                                    </h4>


                                                    <h2 style="font-size: 25px;color:#007bff">Response Frame Information</h2>

                                                </style>
<style>
    /* Base form styling */
    .form-group {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .form-group label {
        font-weight: 600;
        margin-right: 10px;
        min-width: 200px;
        color: #333;
    }

    .form-group small {
        color: #777;
        font-size: 12px;
        margin-top: 4px;
        display: block;
        width: 100%;
    }

    .form-control {
        padding: 8px 10px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #ccc;
        width: 100%;
        max-width: 300px;
        box-sizing: border-box;
    }

    select.form-control {
        background-color: #fff;
        cursor: pointer;
    }

    textarea.form-control {
        width: 100%;
        max-width: 600px;
        resize: vertical;
    }

    /* Table styling */
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

    table.table thead {
        background-color: #f5f5f5;
    }

    table.table th,
    table.table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
    }

    table.table th {
        font-weight: 600;
        white-space: nowrap;
    }

    table.table input,
    table.table select {
        width: 140px;
        padding: 6px;
        font-size: 13px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    h4, h2 {
        font-weight: 700;
        color:blue;
        margin-top: 10px;
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
    /* Section title spacing */
    .commandname {
        margin-top: 25px;
        padding: 10px 15px;
        background: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
</style>




</style>
                                      <br><br>
                                      <button class="back-button" onclick="history.back()">← Back</button>

                                          <!--<div class="commandname">



                                                        <div class="form-group">
                                                            <label class=" col-sm-2" for="resp_parametername">Parameter Name</label>
                                                            <small>Ex : speed,power.....</small>
                                                            <div class="col-sm-4">
                                                                <p class="error product-error"></p>
                                                                <input type="text" class="form-control"
                                                                    id="resp_parametername" name="resp_parametername"value="Power" readonly>
                                                            </div>
                                                        </div>-->
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
                                                                    <option value="Notification">Notification</option>
                                                                    <option value="console">console</option>
                                                                        <option value="Enter Text">Enter Text</option>
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
                                                                                    <option value="Notification">Notification</option>
                                                                                    <option value="console">console</option>
                                                                        <option value="Enter Text">Enter Text</option>
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

                                                <center>
                                                   <div style="text-align: center; margin-top: 20px;">
 <!--   <button type="submit" class="btn" 
        style="background-color: #28a745; color: white; border: none; padding: 10px 25px; margin-right: 10px; width: 80px;">
        Save
    </button>-->

    <a href="ERROR" 
       class="btn" 
       style="background-color: #28a745; color: white; border: none; padding: 10px 25px; width: 80px; display: inline-block; text-align: center; text-decoration: none;">
        Next
    </a>
</div>

                                                    </center>
                                                    </form>
    </div>
</div>
</body>
</html>


</form>

<!--
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Page</title>
    <style>
        body {
            background-color: #f4f6f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px;
            color: #333;
        }
        h4 {
            text-align: center;
            font-size: 26px;
            color: #007bff;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            max-width: 400px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #fff;
        }
        textarea {
            resize: vertical;
        }
        .btn {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            background-color: #28a745;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #218838;
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
  font-family: 'Segoe UI', sans-serif;
  background: rgba(0, 0, 0, 0.2);
  backdrop-filter: blur(6px);
  height: 100vh;
  overflow: hidden;
}
.modal-center-box {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  max-height: 90vh;
  overflow-y: auto;
  width: 90%;
  max-width: 900px;
  border-radius: 16px;
  padding: 40px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}
</style>

</head>
<body>
<div class="modal-center-box">
    <div class="container">
          <div id="items-dropzone" class="post-dropzone">

                                            <div class="modal-body clearfix">
                                                <div class="container-fluid">
                                                    <input type="hidden" name="id" value="" />

                                                    <h4 style="text-align: center;font-size: 30px; color:blue;">
                                                        Response
                                                        FRAME
                                                    </h4>


                                                    <h2 style="font-size: 25px;">Response Frame Information</h2>

                                                </style>
<style>
    /* Base form styling */
    .form-group {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .form-group label {
        font-weight: 600;
        margin-right: 10px;
        min-width: 200px;
        color: #333;
    }

    .form-group small {
        color: #777;
        font-size: 12px;
        margin-top: 4px;
        display: block;
        width: 100%;
    }

    .form-control {
        padding: 8px 10px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #ccc;
        width: 100%;
        max-width: 300px;
        box-sizing: border-box;
    }

    select.form-control {
        background-color: #fff;
        cursor: pointer;
    }

    textarea.form-control {
        width: 100%;
        max-width: 600px;
        resize: vertical;
    }

    /* Table styling */
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

    table.table thead {
        background-color: #f5f5f5;
    }

    table.table th,
    table.table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
    }

    table.table th {
        font-weight: 600;
        white-space: nowrap;
    }

    table.table input,
    table.table select {
        width: 140px;
        padding: 6px;
        font-size: 13px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    h4, h2 {
        font-weight: 700;
        color:blue;
        margin-top: 10px;
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
    /* Section title spacing */
    .commandname {
        margin-top: 25px;
        padding: 10px 15px;
        background: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
</style>




</style>
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
                                                                    <option value="Notification">Notification</option>
                                                                    <option value="console">console</option>
                                                                        <option value="Enter Text">Enter Text</option>
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
                                                                        <th>Developer Notes (If Any)</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tbody>
                                                                    <!-- Initial row with input fields and select dropdowns -->
                                                                  <!--   <tr id="row_1">
                                                                        <!-- Individual fields with IDs -->
                                                                  <!--       <td id="sno_1">1</td>
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
                                                                                    <option value="Notification">Notification</option>
                                                                                    <option value="console">console</option>
                                                                        <option value="Enter Text">Enter Text</option>
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

                                                <center>
                                                    <button id="submitBtn" type="submit">Submit</button>
                                                    </center>
                                                    </form>
    </div>
</div>
</body>
</html>


</form>
<!--<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Page</title>
    

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
</head>
<body>
    <div class="container">
          <div id="items-dropzone" class="post-dropzone">

                                            <div class="modal-body clearfix">
                                                <div class="container-fluid">
                                                    <input type="hidden" name="id" value="" />

                                                    <h4 style="text-align: center;font-size: 30px; color:blue;">
                                                        Response
                                                        FRAME
                                                    </h4>


                                                    <h2 style="font-size: 25px;">Response Frame Information</h2>

                                                </style>





</style>
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
                                                                    <option value="Notification">Notification</option>
                                                                    <option value="console">console</option>
                                                                        <option value="Enter Text">Enter Text</option>
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
                                                                        <th>Developer Notes (If Any)</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tbody>
                                                                    <!-- Initial row with input fields and select dropdowns -->
                                                              <!--       <tr id="row_1">
                                                                        <!-- Individual fields with IDs -->
                                                                   <!--      <td id="sno_1">1</td>
                                                                  <!--       <td><input type="text"
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
                                                                                    <option value="Notification">Notification</option>
                                                                                    <option value="console">console</option>
                                                                        <option value="Enter Text">Enter Text</option>
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

                                                <center>
                                                    <button id="submitBtn" type="submit">Submit</button>
                                                    </center>
                                                    </form>
    </div>
</body>
</html>


</form>
<!--
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Page</title>
    <style>
        body {
            background-color: #f4f6f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px;
            color: #333;
        }
        h4 {
            text-align: center;
            font-size: 26px;
            color: #007bff;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            max-width: 400px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #fff;
        }
        textarea {
            resize: vertical;
        }
        .btn {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            background-color: #28a745;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #218838;
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
</head>
<body>
    <div class="container">
          <div id="items-dropzone" class="post-dropzone">

                                            <div class="modal-body clearfix">
                                                <div class="container-fluid">
                                                    <input type="hidden" name="id" value="" />

                                                    <h4 style="text-align: center;font-size: 30px; color:blue;">
                                                        Response
                                                        FRAME
                                                    </h4>


                                                    <h2 style="font-size: 25px;">Response Frame Information</h2>

                                                </style>
<style>
    /* Base form styling */
    .form-group {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .form-group label {
        font-weight: 600;
        margin-right: 10px;
        min-width: 200px;
        color: #333;
    }

    .form-group small {
        color: #777;
        font-size: 12px;
        margin-top: 4px;
        display: block;
        width: 100%;
    }

    .form-control {
        padding: 8px 10px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #ccc;
        width: 100%;
        max-width: 300px;
        box-sizing: border-box;
    }

    select.form-control {
        background-color: #fff;
        cursor: pointer;
    }

    textarea.form-control {
        width: 100%;
        max-width: 600px;
        resize: vertical;
    }

    /* Table styling */
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

    table.table thead {
        background-color: #f5f5f5;
    }

    table.table th,
    table.table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
    }

    table.table th {
        font-weight: 600;
        white-space: nowrap;
    }

    table.table input,
    table.table select {
        width: 140px;
        padding: 6px;
        font-size: 13px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    h4, h2 {
        font-weight: 700;
        color:blue;
        margin-top: 10px;
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
    /* Section title spacing */
    .commandname {
        margin-top: 25px;
        padding: 10px 15px;
        background: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
</style>




</style>
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
                                                                    <option value="Notification">Notification</option>
                                                                    <option value="console">console</option>
                                                                        <option value="Enter Text">Enter Text</option>
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
                                                                        <th>Developer Notes (If Any)</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tbody>
                                                                    <!-- Initial row with input fields and select dropdowns -->
                                                              <!--       <tr id="row_1">
                                                                        <!-- Individual fields with IDs -->
                                                                  <!--       <td id="sno_1">1</td>
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
                                                                                    <option value="Notification">Notification</option>
                                                                                    <option value="console">console</option>
                                                                        <option value="Enter Text">Enter Text</option>
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

                                                <center>
                                                    <button id="submitBtn" type="submit">Submit</button>
                                                    </center>
                                                    </form>
    </div>
</body>
</html>


</form>
