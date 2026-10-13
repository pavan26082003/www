
<!-- Modal Start -->
<div class="modal fade show" style="display:block; background-color: rgba(0,0,0,0.5);">
  <div class="modal-dialog modal-lg" style="max-width: 65%;">
    <div class="modal-content p-4" style="border-radius: 10px;">
<!--    <button class="back-button" onclick="history.back()">← Back</button>
  <a href="<?php echo base_url('index.php/items/vendor#'); ?>" class="back-button">← Back</a>-->
  <a href="http://192.168.0.130/index.php/items/VENDOR" class="back-button">← Back</a>
  <form id="file-form">

  








<style>
  .container-fluid {
    max-width: 1000px;
    margin: 0 auto;
  }
.modal-body {
  max-height: 90vh;
  overflow-y: auto;
  padding-right: 15px;
}

  .form-group {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

  .form-group > label {
    width: 200px;
    text-align: right;
    font-weight: 600;
    color: #1a73e8;
    margin-right: 10px;
  }

  .form-group > .col-sm-2,
  .form-group > .col-sm-4,
  .form-group > .col-sm-10 {
    flex: 1;
    min-width: 300px;
    margin-right: 100px;
  }

  .form-control,
  select.form-control,
  textarea.form-control {
    background-color: #fff;
    color: #333;
    border: 1px solid #ccc;
    padding: 10px 14px;
    font-size: 14px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: border-color 0.3s, box-shadow 0.3s;
    text-align: left;
  }

  .form-control:focus,
  select.form-control:focus,
  textarea.form-control:focus {
    border-color: #1a73e8;
    box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
    background-color: #f5faff;
  }

  h2, h4 {
    color: #1a73e8 !important;
    text-align: center;
  }

  .commandname {
    border-left: 4px solid #1a73e8;
    padding-left: 20px;
    background-color: #f5faff;
    margin-bottom: 30px;
    border-radius: 8px;
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

  #submitBtn:hover {
    background-color: #135ecf;
    transform: scale(1.05);
  }

  #submitBtn:active {
    background-color: #0f4fc1;
    transform: scale(0.97);
  }

  small {
    color: #555;
    font-size: 12px;
    margin-left: 210px;
    display: block;
    margin-bottom: 4px;
  }
</style>

<style>
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

  label {
    display: block;
    font-weight: 600;
    color: #1a73e8; /* Blue color */
    margin-bottom: 6px;
    font-size: 15px;
    text-align: right;
    padding-right: 10px;
  }

  .form-group {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

  .form-group > label {
    width: 200px;
  }

  .form-group > .col-sm-2,
  .form-group > .col-sm-4,
  .form-group > .col-sm-10 {
    flex: 1;
    min-width: 200px;
  }

  .form-control,
  select.form-control,
  textarea.form-control {
    background-color: #fff;
    color: #333;
    border: 1px solid #ccc;
    padding: 10px 14px;
    font-size: 14px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: border-color 0.3s, box-shadow 0.3s;
  }

  .form-control:focus,
  select.form-control:focus,
  textarea.form-control:focus {
    border-color: #1a73e8;
    box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
    background-color: #f5faff;
  }

  h2, h4 {
    color: #1a73e8;
  }

  #submitBtn {
    background-color: #1a73e8;
  }

  #submitBtn:hover {
    background-color: #135ecf;
  }

  #submitBtn:active {
    background-color: #0f4fc1;
  }
</style>

<style>
  label {
    display: block;
    font-weight: 600;
    color: blue;
    margin-bottom: 6px;
    font-size: 15px;
  }

  .form-control,
  select.form-control,
  textarea.form-control {
    background-color: #fff;
    color: #333;
    border: 1px solid #ccc;
    padding: 10px 14px;
    font-size: 14px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: border-color 0.3s, box-shadow 0.3s;
  }

  .form-control:focus,
  select.form-control:focus,
  textarea.form-control:focus {
    border-color: blue;
    box-shadow: 0 0 0 2px rgba(42, 127, 46, 0.2);
    background-color: #f9fdf9;
  }

  .form-group {
    margin-bottom: 20px;
  }
</style>

<style>
  #submitBtn {
    background-color:blue;
    color: white;
    font-size: 16px;
    padding: 12px 28px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
  }

  #submitBtn:hover {
    background-color: #256f29;
    transform: scale(1.05);
  }

  #submitBtn:active {
    background-color: #1d5f20;
    transform: scale(0.98);
  }

  .form-group {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    margin-bottom: 20px;
    flex-wrap: nowrap;
  }

  .form-group label {
    width: 220px;
    margin-bottom: 0;
    font-weight: 600;
    color: #1a73e8;
    font-size: 15px;
    text-align: right;
    padding-right: 10px;
  }

  .form-group .form-control,
  .form-group select.form-control {
    flex: 1;
    min-width: 300px;
    padding: 10px 14px;
    font-size: 14px;
    border-radius: 6px;
    border: 1px solid #ccc;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    background-color: #fff;
    color: #333;
  }

  .form-group .form-control:focus {
    border-color: #1a73e8;
    box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
    background-color: #f5faff;
  }

  small {
    color: #555;
    font-size: 12px;
    margin-left: 230px;
    display: block;
    margin-top: 4px;
  }


</style>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <!--<link rel="icon" type="image/png" href="<?= base_url('assets/images/favicon.png'); ?>">-->

  <title>Product Information</title>
  <style>
    @import url("https://fonts.googleapis.com/css?family=Open+Sans:400,700");

    :root {
      --white: #000000; /* Now black text */
      --red: #e31b23;
      --bodyColor: #ffffff; /* Now white background */
      --borderFormEls: hsl(0, 0%, 80%);
      --bgFormEls: hsl(0, 0%, 96%);
      --bgFormElsFocus: hsl(0, 0%, 90%);
    }

    * {
      padding: 0;
      margin: 0;
      box-sizing: border-box;
      outline: none;
    }

    a {
      color: inherit;
    }

    input,
    select,
    textarea,
    button {
      font-family: inherit;
      font-size: 100%;
    }

    button,
    label {
      cursor: pointer;
    }

    select {
      appearance: none;
    }

    select::-ms-expand {
      display: none;
    }

    select:-moz-focusring {
      color: transparent !important;
      text-shadow: 0 0 0 var(--white);
    }

    textarea {
      resize: none;
    }

    ul {
      list-style: none;
    }

    body {
      font: 18px/1.5 "Open Sans", sans-serif;
      background: var(--bodyColor);
      color: var(--white);
      margin: 1.5rem 0;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 0 1.5rem;
    }

    .my-form h1 {
      margin-bottom: 1.5rem;
    }

    .my-form li,
    .my-form .grid > *:not(:last-child) {
      margin-bottom: 1.5rem;
    }

    .my-form select,
    .my-form input,
    .my-form textarea,
    .my-form button {
      width: 100%;
      line-height: 1.5;
      padding: 15px 10px;
      border: 1px solid var(--borderFormEls);
      color: var(--white); /* black text */
      background: var(--bgFormEls); /* white/light grey background */
      transition: background-color 0.3s cubic-bezier(0.57, 0.21, 0.69, 1.25),
        transform 0.3s cubic-bezier(0.57, 0.21, 0.69, 1.25);
    }

    .my-form textarea {
      height: 170px;
    }

    .my-form ::placeholder {
      color: #555;
      opacity: 1;
    }

    .my-form select:focus,
    .my-form input:focus,
    .my-form textarea:focus,
    .my-form button:enabled:hover,
    .my-form button:focus,
    .my-form input[type="checkbox"]:focus + label {
      background: var(--bgFormElsFocus);
    }

    .my-form select:focus,
    .my-form input:focus,
    .my-form textarea:focus {
      transform: scale(1.02);
    }

    .my-form *:required,
    .my-form select {
      background-repeat: no-repeat;
      background-position: center right 12px;
      background-size: 15px 15px;
    }

    .my-form *:required {
      background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/asterisk.svg);  
    }

    .my-form select {
      background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/down.svg);
    }

    .my-form *:disabled {
      cursor: default;
      filter: blur(2px);
    }

    .my-form .required-msg {
      display: none;
      background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/asterisk.svg)
        no-repeat center left / 15px 15px;
      padding-left: 20px;
    }

    .my-form .btn-grid {
      position: relative;
      overflow: hidden;
      transition: filter 0.2s;
    }

    .my-form button {
      font-weight: bold;
    }

    .my-form button > * {
      display: inline-block;
      width: 100%;
      transition: transform 0.4s ease-in-out;
    }

    .my-form button .back {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-110%, -50%);
    }

    .my-form button:enabled:hover .back,
    .my-form button:focus .back {
      transform: translate(-50%, -50%);
    }

    .my-form button:enabled:hover .front,
    .my-form button:focus .front {
      transform: translateX(110%);
    }

    .my-form input[type="checkbox"] {
      position: absolute;
      left: -9999px;
    }

    .my-form input[type="checkbox"] + label {
      position: relative;
      display: inline-block;
      padding-left: 2rem;
      transition: background 0.3s cubic-bezier(0.57, 0.21, 0.69, 1.25);
    }

    .my-form input[type="checkbox"] + label::before,
    .my-form input[type="checkbox"] + label::after {
      content: '';
      position: absolute;
    }

    .my-form input[type="checkbox"] + label::before {
      left: 0;
      top: 6px;
      width: 18px;
      height: 18px;
      border: 2px solid var(--white);
    }

    .my-form input[type="checkbox"]:checked + label::before {
      background: var(--red);
    }

    .my-form input[type="checkbox"]:checked + label::after {
      left: 7px;
      top: 7px;
      width: 6px;
      height: 14px;
      border-bottom: 2px solid var(--white);
      border-right: 2px solid var(--white);
      transform: rotate(45deg);
    }

    footer {
      font-size: 1rem;
      text-align: right;
      backface-visibility: hidden;
    }

    footer a {
      text-decoration: none;
    }

    footer span {
      color: var(--red);
    }

    @media screen and (min-width: 600px) {
      .my-form .grid {
        display: grid;
        grid-gap: 1.5rem;
      }

      .my-form .grid-2 {
        grid-template-columns: 1fr 1fr;
      }

      .my-form .grid-3 {
        grid-template-columns: auto auto auto;
        align-items: center;
      }

      .my-form .grid > *:not(:last-child) {
        margin-bottom: 0;
      }

      .my-form .required-msg {
        display: block;
      }
    }

    @media screen and (min-width: 541px) {
      .my-form input[type="checkbox"] + label::before {
        top: 50%;
        transform: translateY(-50%);
      }

      .my-form input[type="checkbox"]:checked + label::after {
        top: 3px;
      }
    }

    .my-form button#submitBtn {
      width: auto;
      padding: 8px 16px;
      font-size: 14px;
      background-color: var(--red);
      color: #fff;
      border: none;
      border-radius: 6px;
      text-transform: uppercase;
      transition: background 0.3s ease;
    }

    .my-form button#submitBtn:hover {
      background-color: #c4161c;
    }
   
 
/* Additional polish */
.my-form input:focus, .my-form select:focus, .my-form textarea:focus {
  outline: 2px solid blue;
  background: #e6f2ea;
}

.my-form .form-group {
  margin-bottom: 20px;
}

.table-responsive {
  margin-top: 30px;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
}

.table th {
  background-color: #d9f2dd;
  font-weight: 600;
}

button[type="button"] {
  background-color: blue;
  color: white;
  padding: 6px 14px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  margin: 10px 0;
}

button[type="button"]:hover {
  background-color: #256f29;
}

button[onclick="removeRow(this)"] {
  background-color: #d9534f;
}

button[onclick="removeRow(this)"]:hover {
  background-color: #c9302c;
}

</style>
 
 <div id="items-dropzone" class="post-dropzone">

                                    <div class="modal-body clearfix">
                                        <div class="container-fluid">
                                            <input type="hidden" name="id" value="{{id}}" />

                                            <h4 style="text-align: center; font-size: 30px; color: green;">COMMAND FRAME
                                            </h4>


                                            <h2 style="font-size: 25px;">Command Frame Information</h2>



                                            <br><br>
                                         <!--   <div class="commandname">

                                                <div class="form-group">
                                                    <label class=" col-sm-2" for="cmd_parametername">Parameter Name </label>
                                                    <small>Ex : Speed,Power....</small>
                                                    <div class="col-sm-4">
                                                        <p class="error product-error"></p>
                                                        <input type="text" class="form-control" id="cmd_parametername"
                                                            name="cmd_parametername" value="Power" readonly>
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
                                                                <th>Developer Notes (If Any)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
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
                                                     <tr>
                                                           <button type="button" onclick="addRow()">+</button>
                                                           </tr>
                                                </div>
                                            </div>
                                        </div>
<!--<script>
        function validatePageName() {
            var input = document.getElementById("pagenames").value;
            if (input === "Page0") {
                alert("Page0 as Page name is invalid");
            }
        }
    </script>
    
    <!--<script> 
    document.addEventListener("DOMContentLoaded", function () {
    var form = document.getElementById('file-form');
    if (form) {
        form.onsubmit = function () {
            var modelName = document.getElementById('modelname')?.value;
            if (!modelName || modelName.trim() === '') {
                alert('Please enter the Model Name.');
                return false;
            }
            return true;
        };
    }
});

    document.getElementById("currentDate").value = new Date().toISOString().split('T')[0];
</script>

                  <!--  <script type="text/javascript">
 
    
 
      document.getElementById('file-form').onsubmit = function() {
        var modelName = document.getElementById('modelname').value;
        if (modelName.trim() === '') {
            alert('Please enter the Model Name.');
            return false; 
        }
        return true; 
    };-->
    <script type="text/javascript">
    function changeImage(input) {f
        var file = input.files[0];f
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







              <!-- function showImagePreview(input) {
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
function showPreviousStep() {
    document.getElementById('step2').style.display = 'none';
    document.getElementById('step1').style.display = 'block';
}
 </script>-->
                    <?php echo form_close(); ?>
                    

<center style="margin-top: 40px;">
  <!--<div style="text-align: center; margin-top: 20px;">
    <button type="submit" class="btn" 
        style="background-color: #28a745; color: white; border: none; padding: 10px 25px; margin-right: 10px; width: 80px;">
        Save
    </button>-->

    <a href="<?php echo site_url('items/RESPONSE'); ?>" 
       class="btn" 
       style="background-color: #28a745; color: white; border: none; padding: 10px 25px; width: 80px; display: inline-block; text-align: center; text-decoration: none;">
        Next
    </a>
</div>

</center>

</body>

</html>

<!--
<style>
  .container-fluid {
    max-width: 1000px;
    margin: 0 auto;
  }

  .form-group {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

  .form-group > label {
    width: 200px;
    text-align: right;
    font-weight: 600;
    color: #1a73e8;
    margin-right: 10px;
  }

  .form-group > .col-sm-2,
  .form-group > .col-sm-4,
  .form-group > .col-sm-10 {
    flex: 1;
    min-width: 300px;
    margin-right: 100px;
  }

  .form-control,
  select.form-control,
  textarea.form-control {
    background-color: #fff;
    color: #333;
    border: 1px solid #ccc;
    padding: 10px 14px;
    font-size: 14px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: border-color 0.3s, box-shadow 0.3s;
    text-align: left;
  }

  .form-control:focus,
  select.form-control:focus,
  textarea.form-control:focus {
    border-color: #1a73e8;
    box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
    background-color: #f5faff;
  }

  h2, h4 {
    color: #1a73e8 !important;
    text-align: center;
  }

  .commandname {
    border-left: 4px solid #1a73e8;
    padding-left: 20px;
    background-color: #f5faff;
    margin-bottom: 30px;
    border-radius: 8px;
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

  #submitBtn:hover {
    background-color: #135ecf;
    transform: scale(1.05);
  }

  #submitBtn:active {
    background-color: #0f4fc1;
    transform: scale(0.97);
  }

  small {
    color: #555;
    font-size: 12px;
    margin-left: 210px;
    display: block;
    margin-bottom: 4px;
  }
</style>

<style>
  label {
    display: block;
    font-weight: 600;
    color: #1a73e8; /* Blue color */
    margin-bottom: 6px;
    font-size: 15px;
    text-align: right;
    padding-right: 10px;
  }

  .form-group {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

  .form-group > label {
    width: 200px;
  }

  .form-group > .col-sm-2,
  .form-group > .col-sm-4,
  .form-group > .col-sm-10 {
    flex: 1;
    min-width: 200px;
  }

  .form-control,
  select.form-control,
  textarea.form-control {
    background-color: #fff;
    color: #333;
    border: 1px solid #ccc;
    padding: 10px 14px;
    font-size: 14px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: border-color 0.3s, box-shadow 0.3s;
  }

  .form-control:focus,
  select.form-control:focus,
  textarea.form-control:focus {
    border-color: #1a73e8;
    box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
    background-color: #f5faff;
  }

  h2, h4 {
    color: #1a73e8;
  }

  #submitBtn {
    background-color: #1a73e8;
  }

  #submitBtn:hover {
    background-color: #135ecf;
  }

  #submitBtn:active {
    background-color: #0f4fc1;
  }
</style>

<style>
  label {
    display: block;
    font-weight: 600;
    color: blue;
    margin-bottom: 6px;
    font-size: 15px;
  }

  .form-control,
  select.form-control,
  textarea.form-control {
    background-color: #fff;
    color: #333;
    border: 1px solid #ccc;
    padding: 10px 14px;
    font-size: 14px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: border-color 0.3s, box-shadow 0.3s;
  }

  .form-control:focus,
  select.form-control:focus,
  textarea.form-control:focus {
    border-color: blue;
    box-shadow: 0 0 0 2px rgba(42, 127, 46, 0.2);
    background-color: #f9fdf9;
  }

  .form-group {
    margin-bottom: 20px;
  }
</style>

<style>
  #submitBtn {
    background-color:blue;
    color: white;
    font-size: 16px;
    padding: 12px 28px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
  }

  #submitBtn:hover {
    background-color: #256f29;
    transform: scale(1.05);
  }

  #submitBtn:active {
    background-color: #1d5f20;
    transform: scale(0.98);
  }
</style>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Product Information</title>
  <style>
    @import url("https://fonts.googleapis.com/css?family=Open+Sans:400,700");

    :root {
      --white: #000000; /* Now black text */
      --red: #e31b23;
      --bodyColor: #ffffff; /* Now white background */
      --borderFormEls: hsl(0, 0%, 80%);
      --bgFormEls: hsl(0, 0%, 96%);
      --bgFormElsFocus: hsl(0, 0%, 90%);
    }

    * {
      padding: 0;
      margin: 0;
      box-sizing: border-box;
      outline: none;
    }

    a {
      color: inherit;
    }

    input,
    select,
    textarea,
    button {
      font-family: inherit;
      font-size: 100%;
    }

    button,
    label {
      cursor: pointer;
    }

    select {
      appearance: none;
    }

    select::-ms-expand {
      display: none;
    }

    select:-moz-focusring {
      color: transparent !important;
      text-shadow: 0 0 0 var(--white);
    }

    textarea {
      resize: none;
    }

    ul {
      list-style: none;
    }

    body {
      font: 18px/1.5 "Open Sans", sans-serif;
      background: var(--bodyColor);
      color: var(--white);
      margin: 1.5rem 0;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 0 1.5rem;
    }

    .my-form h1 {
      margin-bottom: 1.5rem;
    }

    .my-form li,
    .my-form .grid > *:not(:last-child) {
      margin-bottom: 1.5rem;
    }

    .my-form select,
    .my-form input,
    .my-form textarea,
    .my-form button {
      width: 100%;
      line-height: 1.5;
      padding: 15px 10px;
      border: 1px solid var(--borderFormEls);
      color: var(--white); /* black text */
      background: var(--bgFormEls); /* white/light grey background */
      transition: background-color 0.3s cubic-bezier(0.57, 0.21, 0.69, 1.25),
        transform 0.3s cubic-bezier(0.57, 0.21, 0.69, 1.25);
    }

    .my-form textarea {
      height: 170px;
    }

    .my-form ::placeholder {
      color: #555;
      opacity: 1;
    }

    .my-form select:focus,
    .my-form input:focus,
    .my-form textarea:focus,
    .my-form button:enabled:hover,
    .my-form button:focus,
    .my-form input[type="checkbox"]:focus + label {
      background: var(--bgFormElsFocus);
    }

    .my-form select:focus,
    .my-form input:focus,
    .my-form textarea:focus {
      transform: scale(1.02);
    }

    .my-form *:required,
    .my-form select {
      background-repeat: no-repeat;
      background-position: center right 12px;
      background-size: 15px 15px;
    }

    .my-form *:required {
      background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/asterisk.svg);  
    }

    .my-form select {
      background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/down.svg);
    }

    .my-form *:disabled {
      cursor: default;
      filter: blur(2px);
    }

    .my-form .required-msg {
      display: none;
      background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/asterisk.svg)
        no-repeat center left / 15px 15px;
      padding-left: 20px;
    }

    .my-form .btn-grid {
      position: relative;
      overflow: hidden;
      transition: filter 0.2s;
    }

    .my-form button {
      font-weight: bold;
    }

    .my-form button > * {
      display: inline-block;
      width: 100%;
      transition: transform 0.4s ease-in-out;
    }

    .my-form button .back {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-110%, -50%);
    }

    .my-form button:enabled:hover .back,
    .my-form button:focus .back {
      transform: translate(-50%, -50%);
    }

    .my-form button:enabled:hover .front,
    .my-form button:focus .front {
      transform: translateX(110%);
    }

    .my-form input[type="checkbox"] {
      position: absolute;
      left: -9999px;
    }

    .my-form input[type="checkbox"] + label {
      position: relative;
      display: inline-block;
      padding-left: 2rem;
      transition: background 0.3s cubic-bezier(0.57, 0.21, 0.69, 1.25);
    }

    .my-form input[type="checkbox"] + label::before,
    .my-form input[type="checkbox"] + label::after {
      content: '';
      position: absolute;
    }

    .my-form input[type="checkbox"] + label::before {
      left: 0;
      top: 6px;
      width: 18px;
      height: 18px;
      border: 2px solid var(--white);
    }

    .my-form input[type="checkbox"]:checked + label::before {
      background: var(--red);
    }

    .my-form input[type="checkbox"]:checked + label::after {
      left: 7px;
      top: 7px;
      width: 6px;
      height: 14px;
      border-bottom: 2px solid var(--white);
      border-right: 2px solid var(--white);
      transform: rotate(45deg);
    }

    footer {
      font-size: 1rem;
      text-align: right;
      backface-visibility: hidden;
    }

    footer a {
      text-decoration: none;
    }

    footer span {
      color: var(--red);
    }

    @media screen and (min-width: 600px) {
      .my-form .grid {
        display: grid;
        grid-gap: 1.5rem;
      }

      .my-form .grid-2 {
        grid-template-columns: 1fr 1fr;
      }

      .my-form .grid-3 {
        grid-template-columns: auto auto auto;
        align-items: center;
      }

      .my-form .grid > *:not(:last-child) {
        margin-bottom: 0;
      }

      .my-form .required-msg {
        display: block;
      }
    }

    @media screen and (min-width: 541px) {
      .my-form input[type="checkbox"] + label::before {
        top: 50%;
        transform: translateY(-50%);
      }

      .my-form input[type="checkbox"]:checked + label::after {
        top: 3px;
      }
    }

    .my-form button#submitBtn {
      width: auto;
      padding: 8px 16px;
      font-size: 14px;
      background-color: var(--red);
      color: #fff;
      border: none;
      border-radius: 6px;
      text-transform: uppercase;
      transition: background 0.3s ease;
    }

    .my-form button#submitBtn:hover {
      background-color: #c4161c;
    }
   
 
/* Additional polish */
.my-form input:focus, .my-form select:focus, .my-form textarea:focus {
  outline: 2px solid blue;
  background: #e6f2ea;
}

.my-form .form-group {
  margin-bottom: 20px;
}

.table-responsive {
  margin-top: 30px;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
}

.table th {
  background-color: #d9f2dd;
  font-weight: 600;
}

button[type="button"] {
  background-color: blue;
  color: white;
  padding: 6px 14px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  margin: 10px 0;
}

button[type="button"]:hover {
  background-color: #256f29;
}

button[onclick="removeRow(this)"] {
  background-color: #d9534f;
}

button[onclick="removeRow(this)"]:hover {
  background-color: #c9302c;
}

</style>
 
 <div id="items-dropzone" class="post-dropzone">

                                    <div class="modal-body clearfix">
                                        <div class="container-fluid">
                                            <input type="hidden" name="id" value="{{id}}" />

                                            <h4 style="text-align: center; font-size: 30px; color: green;">COMMAND FRAME
                                            </h4>


                                            <h2 style="font-size: 25px;">Command Frame Information</h2>



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
                                                                <th>Developer Notes (If Any)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tbody>
                                                            <!-- Initial row with input fields and select dropdowns -->
                                                        <!--    <tr id="row_1">
                                                                <!-- Individual fields with IDs -->
                                                             <!--   <td id="sno_1">1</td>
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
                                                     <tr>
                                                           <button type="button" onclick="addRow()">+</button>
                                                           </tr>
                                                </div>
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


                    <?php echo form_close(); ?>
                    

<center style="margin-top: 40px;">
    <button type="submit" id="submitBtn">Submit</button>
</center>

</body>

</html>

    </div>
  </div>
</div>
<!-- Modal End -->
<!--
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Frame Modal Viewer</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      background-color: #f0f0f0;
      padding: 40px;
    }
  </style>
</head>
<body>

  <div class="container text-center">
    <h2 class="mb-4">Open Frame Modal</h2>
    <select id="frameSelector" class="form-control mx-auto mb-4" style="max-width:300px;">
      <option value="">-- Select a Frame --</option>
      <option value="commandModal">Command Frame</option>
      <option value="responseModal">Response Frame</option>
      <option value="errorModal">Error Frame</option>
    </select>
  </div>

  
<div class="modal fade" id="commandModal" tabindex="-1" role="dialog" aria-labelledby="commandModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content rounded-lg">
      <div class="modal-header">
        <h5 class="modal-title">Command Frame</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#commandModal').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-0" style="height: 90vh;">
        <iframe src="COMMAND.php" style="width:100%; height:100%; border:none;"></iframe>
      </div>
    </div>
  </div>
</div>

  
<div class="modal fade" id="responseModal" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content rounded-lg">
      <div class="modal-header">
        <h5 class="modal-title">Response Frame</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#responseModal').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-0" style="height: 90vh;">
        <iframe src="RESPONSE.php" style="width:100%; height:100%; border:none;"></iframe>
      </div>
    </div>
  </div>
</div>

  
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content rounded-lg">
      <div class="modal-header">
        <h5 class="modal-title">Error Frame</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#errorModal').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-0" style="height: 90vh;">
        <iframe src="ERROR.php" style="width:100%; height:100%; border:none;"></iframe>
      </div>
    </div>
  </div>
</div>


  <script>
    $('#frameSelector').on('change', function () {
      var selected = $(this).val();
      if (selected) {
        $('#' + selected).modal('show');
        $(this).val('');
      }
    });
  </script>

</body>
</html>-->
<!--<style>
  .container-fluid {
    max-width: 1000px;
    margin: 0 auto;
  }

  .form-group {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

  .form-group > label {
    width: 200px;
    text-align: right;
    font-weight: 600;
    color: #1a73e8;
    margin-right: 10px;
  }

  .form-group > .col-sm-2,
  .form-group > .col-sm-4,
  .form-group > .col-sm-10 {
    flex: 1;
    min-width: 300px;
    margin-right: 100px;
  }

  .form-control,
  select.form-control,
  textarea.form-control {
    background-color: #fff;
    color: #333;
    border: 1px solid #ccc;
    padding: 10px 14px;
    font-size: 14px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: border-color 0.3s, box-shadow 0.3s;
    text-align: left;
  }

  .form-control:focus,
  select.form-control:focus,
  textarea.form-control:focus {
    border-color: #1a73e8;
    box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
    background-color: #f5faff;
  }

  h2, h4 {
    color: #1a73e8 !important;
    text-align: center;
  }

  .commandname {
    border-left: 4px solid #1a73e8;
    padding-left: 20px;
    background-color: #f5faff;
    margin-bottom: 30px;
    border-radius: 8px;
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

  #submitBtn:hover {
    background-color: #135ecf;
    transform: scale(1.05);
  }

  #submitBtn:active {
    background-color: #0f4fc1;
    transform: scale(0.97);
  }

  small {
    color: #555;
    font-size: 12px;
    margin-left: 210px;
    display: block;
    margin-bottom: 4px;
  }
</style>

<style>
  label {
    display: block;
    font-weight: 600;
    color: #1a73e8; /* Blue color */
    margin-bottom: 6px;
    font-size: 15px;
    text-align: right;
    padding-right: 10px;
  }

  .form-group {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

  .form-group > label {
    width: 200px;
  }

  .form-group > .col-sm-2,
  .form-group > .col-sm-4,
  .form-group > .col-sm-10 {
    flex: 1;
    min-width: 200px;
  }

  .form-control,
  select.form-control,
  textarea.form-control {
    background-color: #fff;
    color: #333;
    border: 1px solid #ccc;
    padding: 10px 14px;
    font-size: 14px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: border-color 0.3s, box-shadow 0.3s;
  }

  .form-control:focus,
  select.form-control:focus,
  textarea.form-control:focus {
    border-color: #1a73e8;
    box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
    background-color: #f5faff;
  }

  h2, h4 {
    color: #1a73e8;
  }

  #submitBtn {
    background-color: #1a73e8;
  }

  #submitBtn:hover {
    background-color: #135ecf;
  }

  #submitBtn:active {
    background-color: #0f4fc1;
  }
</style>

<style>
  label {
    display: block;
    font-weight: 600;
    color: blue;
    margin-bottom: 6px;
    font-size: 15px;
  }

  .form-control,
  select.form-control,
  textarea.form-control {
    background-color: #fff;
    color: #333;
    border: 1px solid #ccc;
    padding: 10px 14px;
    font-size: 14px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: border-color 0.3s, box-shadow 0.3s;
  }

  .form-control:focus,
  select.form-control:focus,
  textarea.form-control:focus {
    border-color: blue;
    box-shadow: 0 0 0 2px rgba(42, 127, 46, 0.2);
    background-color: #f9fdf9;
  }

  .form-group {
    margin-bottom: 20px;
  }
</style>

<style>
  #submitBtn {
    background-color:blue;
    color: white;
    font-size: 16px;
    padding: 12px 28px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
  }

  #submitBtn:hover {
    background-color: #256f29;
    transform: scale(1.05);
  }

  #submitBtn:active {
    background-color: #1d5f20;
    transform: scale(0.98);
  }
</style>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Product Information</title>
  <style>
    @import url("https://fonts.googleapis.com/css?family=Open+Sans:400,700");

    :root {
      --white: #000000; /* Now black text */
      --red: #e31b23;
      --bodyColor: #ffffff; /* Now white background */
      --borderFormEls: hsl(0, 0%, 80%);
      --bgFormEls: hsl(0, 0%, 96%);
      --bgFormElsFocus: hsl(0, 0%, 90%);
    }

    * {
      padding: 0;
      margin: 0;
      box-sizing: border-box;
      outline: none;
    }

    a {
      color: inherit;
    }

    input,
    select,
    textarea,
    button {
      font-family: inherit;
      font-size: 100%;
    }

    button,
    label {
      cursor: pointer;
    }

    select {
      appearance: none;
    }

    select::-ms-expand {
      display: none;
    }

    select:-moz-focusring {
      color: transparent !important;
      text-shadow: 0 0 0 var(--white);
    }

    textarea {
      resize: none;
    }

    ul {
      list-style: none;
    }

    body {
      font: 18px/1.5 "Open Sans", sans-serif;
      background: var(--bodyColor);
      color: var(--white);
      margin: 1.5rem 0;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 0 1.5rem;
    }

    .my-form h1 {
      margin-bottom: 1.5rem;
    }

    .my-form li,
    .my-form .grid > *:not(:last-child) {
      margin-bottom: 1.5rem;
    }

    .my-form select,
    .my-form input,
    .my-form textarea,
    .my-form button {
      width: 100%;
      line-height: 1.5;
      padding: 15px 10px;
      border: 1px solid var(--borderFormEls);
      color: var(--white); /* black text */
      background: var(--bgFormEls); /* white/light grey background */
      transition: background-color 0.3s cubic-bezier(0.57, 0.21, 0.69, 1.25),
        transform 0.3s cubic-bezier(0.57, 0.21, 0.69, 1.25);
    }

    .my-form textarea {
      height: 170px;
    }

    .my-form ::placeholder {
      color: #555;
      opacity: 1;
    }

    .my-form select:focus,
    .my-form input:focus,
    .my-form textarea:focus,
    .my-form button:enabled:hover,
    .my-form button:focus,
    .my-form input[type="checkbox"]:focus + label {
      background: var(--bgFormElsFocus);
    }

    .my-form select:focus,
    .my-form input:focus,
    .my-form textarea:focus {
      transform: scale(1.02);
    }

    .my-form *:required,
    .my-form select {
      background-repeat: no-repeat;
      background-position: center right 12px;
      background-size: 15px 15px;
    }

    .my-form *:required {
      background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/asterisk.svg);  
    }

    .my-form select {
      background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/down.svg);
    }

    .my-form *:disabled {
      cursor: default;
      filter: blur(2px);
    }

    .my-form .required-msg {
      display: none;
      background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/asterisk.svg)
        no-repeat center left / 15px 15px;
      padding-left: 20px;
    }

    .my-form .btn-grid {
      position: relative;
      overflow: hidden;
      transition: filter 0.2s;
    }

    .my-form button {
      font-weight: bold;
    }

    .my-form button > * {
      display: inline-block;
      width: 100%;
      transition: transform 0.4s ease-in-out;
    }

    .my-form button .back {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-110%, -50%);
    }

    .my-form button:enabled:hover .back,
    .my-form button:focus .back {
      transform: translate(-50%, -50%);
    }

    .my-form button:enabled:hover .front,
    .my-form button:focus .front {
      transform: translateX(110%);
    }

    .my-form input[type="checkbox"] {
      position: absolute;
      left: -9999px;
    }

    .my-form input[type="checkbox"] + label {
      position: relative;
      display: inline-block;
      padding-left: 2rem;
      transition: background 0.3s cubic-bezier(0.57, 0.21, 0.69, 1.25);
    }

    .my-form input[type="checkbox"] + label::before,
    .my-form input[type="checkbox"] + label::after {
      content: '';
      position: absolute;
    }

    .my-form input[type="checkbox"] + label::before {
      left: 0;
      top: 6px;
      width: 18px;
      height: 18px;
      border: 2px solid var(--white);
    }

    .my-form input[type="checkbox"]:checked + label::before {
      background: var(--red);
    }

    .my-form input[type="checkbox"]:checked + label::after {
      left: 7px;
      top: 7px;
      width: 6px;
      height: 14px;
      border-bottom: 2px solid var(--white);
      border-right: 2px solid var(--white);
      transform: rotate(45deg);
    }

    footer {
      font-size: 1rem;
      text-align: right;
      backface-visibility: hidden;
    }

    footer a {
      text-decoration: none;
    }

    footer span {
      color: var(--red);
    }

    @media screen and (min-width: 600px) {
      .my-form .grid {
        display: grid;
        grid-gap: 1.5rem;
      }

      .my-form .grid-2 {
        grid-template-columns: 1fr 1fr;
      }

      .my-form .grid-3 {
        grid-template-columns: auto auto auto;
        align-items: center;
      }

      .my-form .grid > *:not(:last-child) {
        margin-bottom: 0;
      }

      .my-form .required-msg {
        display: block;
      }
    }

    @media screen and (min-width: 541px) {
      .my-form input[type="checkbox"] + label::before {
        top: 50%;
        transform: translateY(-50%);
      }

      .my-form input[type="checkbox"]:checked + label::after {
        top: 3px;
      }
    }

    .my-form button#submitBtn {
      width: auto;
      padding: 8px 16px;
      font-size: 14px;
      background-color: var(--red);
      color: #fff;
      border: none;
      border-radius: 6px;
      text-transform: uppercase;
      transition: background 0.3s ease;
    }

    .my-form button#submitBtn:hover {
      background-color: #c4161c;
    }
   
 
/* Additional polish */
.my-form input:focus, .my-form select:focus, .my-form textarea:focus {
  outline: 2px solid blue;
  background: #e6f2ea;
}

.my-form .form-group {
  margin-bottom: 20px;
}

.table-responsive {
  margin-top: 30px;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
}

.table th {
  background-color: #d9f2dd;
  font-weight: 600;
}

button[type="button"] {
  background-color: blue;
  color: white;
  padding: 6px 14px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  margin: 10px 0;
}

button[type="button"]:hover {
  background-color: #256f29;
}

button[onclick="removeRow(this)"] {
  background-color: #d9534f;
}

button[onclick="removeRow(this)"]:hover {
  background-color: #c9302c;
}

</style>
 
 <div id="items-dropzone" class="post-dropzone">

                                    <div class="modal-body clearfix">
                                        <div class="container-fluid">
                                            <input type="hidden" name="id" value="{{id}}" />

                                            <h4 style="text-align: center; font-size: 30px; color: green;">COMMAND FRAME
                                            </h4>


                                            <h2 style="font-size: 25px;">Command Frame Information</h2>



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
                                                                <th>Developer Notes (If Any)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tbody>
                                                            <!-- Initial row with input fields and select dropdowns -->
                                                        <!--    <tr id="row_1">
                                                                <!-- Individual fields with IDs -->
                                                             <!--   <td><input type="text" id="startBytePosition_cmd"
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
                                                     <tr>
                                                           <button type="button" onclick="addRow()">+</button>
                                                           </tr>
                                                </div>
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


                    <?php echo form_close(); ?>
                    

<center style="margin-top: 40px;">
    <button type="submit" id="submitBtn">Submit</button>
</center>

</body>

</html>
