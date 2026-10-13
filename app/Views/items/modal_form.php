<?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>







<!-- =========================================================
PREMIUM INDUSTRIAL IoT UI THEME
ONLY UI COLORS / SHADOWS / HOVERS UPDATED
NO LAYOUT CHANGES
========================================================= -->




<style>

/* =========================================================
ROOT COLORS
========================================================= */

:root{
    --bg-main:#F8F5F2;
    --card-bg:#FFFFFF;

    --text-main:#1B1E2B;
    --text-secondary:#6B7280;

    --border-color:#E5DDD5;

    --graphite:#2B2D42;
    --graphite-2:#3A3F58;

    --copper:#C08457;
    --soft-copper:#D4A373;
    --metallic:#E6B980;

    --sage:#6B8F71;

    --hover-glow:rgba(192,132,87,.18);

    --shadow-sm:
        0 2px 8px rgba(43,45,66,.05),
        0 6px 20px rgba(43,45,66,.04);

    --shadow-md:
        0 10px 30px rgba(43,45,66,.08);

    --shadow-hover:
        0 12px 28px rgba(43,45,66,.10),
        0 0 0 4px rgba(192,132,87,.10);
}

/* =========================================================
BODY
========================================================= */

body{
    background: var(--bg-main) !important;
    color: var(--text-main) !important;
    font-family: "Inter", sans-serif;
}

/* =========================================================
CARDS / PANELS
========================================================= */

.post-dropzone,
.modal-body,
.commandname,
.table-responsive{
    background: var(--card-bg) !important;
    border: 1px solid var(--border-color) !important;
    border-radius: 16px !important;
    box-shadow: var(--shadow-sm);
    transition: all .28s ease;
    padding: 20px;
    margin-bottom: 20px;
}

.post-dropzone:hover,
.table-responsive:hover{
    box-shadow: var(--shadow-hover);
}

/* =========================================================
HEADINGS
========================================================= */

h2{
    color: var(--graphite) !important;
    font-weight: 700 !important;
    margin-bottom: 20px;
}

h4{
    color: var(--graphite) !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    padding-left: 18px;
    margin-bottom: 25px;
}

h4:before{
    content:"";
    width:8px;
    height:8px;
    border-radius:50%;
    background: var(--copper);
    position:absolute;
    left:0;
    top:50%;
    transform:translateY(-50%);
    box-shadow:0 0 0 5px rgba(192,132,87,.15);
}

/* =========================================================
LABELS
========================================================= */

label{
    color: var(--graphite) !important;
    font-weight: 600;
}

/* =========================================================
INPUTS
========================================================= */

.form-control,
input[type="text"],
input[type="file"],
select,
textarea{
    border:1px solid var(--border-color) !important;
    border-radius:10px !important;
    background:#fff !important;
    color:var(--text-main) !important;
    box-shadow:none !important;
    transition:all .25s ease;
}

.form-control:focus,
input[type="text"]:focus,
input[type="file"]:focus,
select:focus,
textarea:focus{
    border-color:var(--copper) !important;
    box-shadow:
        0 0 0 4px rgba(192,132,87,.12) !important;
    outline:none !important;
}

.form-control:hover,
input:hover,
select:hover,
textarea:hover{
    border-color: var(--soft-copper) !important;
}

/* =========================================================
PLACEHOLDER
========================================================= */

::placeholder{
    color: var(--text-secondary) !important;
}

/* =========================================================
TABLES
========================================================= */

.table{
    background:#fff;
    border-radius:14px;
    overflow:hidden;
}

.table thead tr{
    background:
        linear-gradient(
            135deg,
            var(--graphite),
            var(--graphite-2)
        );
}

.table thead th{
    color:#fff !important;
    border:none !important;
    font-size:13px;
    font-weight:600;
    padding:14px 10px !important;
    vertical-align:middle;
}

.table tbody tr{
    border-bottom:1px solid var(--border-color);
    transition:all .25s ease;
}

.table tbody tr:hover{
    background:rgba(192,132,87,.05);
}

.table tbody td{
    color:var(--text-main);
    vertical-align:middle !important;
    padding:10px !important;
}

/* =========================================================
BUTTONS
========================================================= */

.btn-primary{
    background:
        linear-gradient(
            135deg,
            var(--graphite),
            var(--graphite-2)
        ) !important;

    border:none !important;
    border-radius:10px !important;
    color:#fff !important;

    padding:10px 24px !important;

    font-weight:600;
    letter-spacing:.3px;

    transition:all .25s ease;

    box-shadow:
        0 6px 18px rgba(43,45,66,.12);
}

.btn-primary:hover{
    transform:translateY(-1px);

    box-shadow:
        0 12px 24px rgba(43,45,66,.16),
        0 0 0 4px rgba(192,132,87,.12);

    background:
        linear-gradient(
            135deg,
            var(--graphite-2),
            var(--graphite)
        ) !important;
}

/* =========================================================
ADD / REMOVE BUTTONS
========================================================= */

button{
    border:none;
    border-radius:8px;
    padding:8px 16px;
    transition:all .25s ease;
}

button:hover{
    transform:translateY(-1px);
}

button[onclick*="addRow"],
button[onclick*="respaddRow"],
button[onclick*="erroraddRow"]{
    background:var(--sage);
    color:#fff;
    box-shadow:
        0 6px 18px rgba(107,143,113,.18);
}

button[onclick*="addRow"]:hover,
button[onclick*="respaddRow"]:hover,
button[onclick*="erroraddRow"]:hover{
    box-shadow:
        0 10px 24px rgba(107,143,113,.24);
}

button[onclick*="removeRow"]{
    background:var(--copper);
    color:#fff;
    box-shadow:
        0 6px 18px rgba(192,132,87,.18);
}

button[onclick*="removeRow"]:hover{
    box-shadow:
        0 10px 24px rgba(192,132,87,.28);
}

/* =========================================================
SMALL TEXT
========================================================= */

small.text-muted{
    color:var(--text-secondary) !important;
}

/* =========================================================
IMAGE PREVIEW
========================================================= */

#brandlogo-preview{
    margin-top:10px;
    border-radius:12px;
    border:1px solid var(--border-color);
    padding:6px;
    background:#fff;
    box-shadow:var(--shadow-sm);
}

/* =========================================================
MODAL FOOTER
========================================================= */

.modal-footer{
    border-top:1px solid var(--border-color);
    background:transparent !important;
    padding-top:20px;
}

/* =========================================================
FORM SPACING
========================================================= */

.form-group{
    margin-bottom:24px;
}

/* =========================================================
SCROLLBAR
========================================================= */

::-webkit-scrollbar{
    width:10px;
    height:10px;
}

::-webkit-scrollbar-track{
    background:#EFE8E1;
}

::-webkit-scrollbar-thumb{
    background:var(--soft-copper);
    border-radius:20px;
}

::-webkit-scrollbar-thumb:hover{
    background:var(--copper);
}

/* =========================================================
BOOTSTRAP OVERRIDES
========================================================= */

.bg-success,
.badge-success{
    background:var(--sage) !important;
}

.bg-danger,
.badge-danger{
    background:var(--copper) !important;
}

.bg-primary,
.badge-primary{
    background:var(--graphite) !important;
}

/* =========================================================
QUICK ACTION STYLE (OPTIONAL)
========================================================= */

.quick-action-card{
    background:
        linear-gradient(
            135deg,
            var(--graphite),
            var(--graphite-2)
        );

    border:1px solid var(--border-color);

    border-radius:14px;

    box-shadow:
        0 8px 22px rgba(43,45,66,.10);

    transition:all .28s ease;
}

.quick-action-card:hover{
    transform:translateY(-2px);

    box-shadow:
        0 12px 30px rgba(43,45,66,.14),
        0 0 0 4px rgba(192,132,87,.12);
}

/* =========================================================
DOWNLOAD CARDS
========================================================= */

.api-card{
    background:
        linear-gradient(
            135deg,
            var(--graphite),
            var(--graphite-2)
        );
}

.library-card{
    background:
        linear-gradient(
            135deg,
            var(--sage),
            #84A98C
        );
}

.provision-card{
    background:
        linear-gradient(
            135deg,
            var(--copper),
            var(--soft-copper)
        );
}

.driver-card{
    background:
        linear-gradient(
            135deg,
            #232536,
            #2F344B
        );
}

/* =========================================================
STATUS CARDS
========================================================= */

.total-module-card{
    background:
        linear-gradient(
            135deg,
            var(--graphite),
            var(--graphite-2)
        );
    color:#fff;
}

.online-module-card{
    background:
        linear-gradient(
            135deg,
            #6B8F71,
            #7FA787
        );
    color:#fff;
}

.offline-module-card{
    background:
        linear-gradient(
            135deg,
            var(--copper),
            var(--soft-copper)
        );
    color:#fff;
}

/* =========================================================
INDUSTRIAL PREMIUM EFFECTS
========================================================= */

.table-responsive,
.commandname,
.post-dropzone{
    backdrop-filter: blur(2px);
}

/* =========================================================
END
========================================================= */

</style>


<div id="items-dropzone" class="post-dropzone">
<div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $id; ?>" />

            <h4 style="text-align: center; color: green;">PRODUCT INFORMATION</h4>
<br><br>
<div class="commandname">

    

   
     <div class="form-group">
    <label class="col-sm-2" for="email">Product Name:<span style="color: red;">*</span></label>
    <div class="col-sm-4">
        <input type="text" class="form-control" id="productname" name="productname">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2" for="pwd">Brand Name:<span style="color: red;">*</span></label>
    <div class="col-sm-4">
        <input type="text" class="form-control" id="brandname" name="brandname">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2" for="pwd">Date:<span style="color: red;">*</span></label>
    <div class="col-sm-2">          
        <input type="text" class="form-control" id="date" name="date">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2" for="pwd">Model Name:<span style="color: red;">*</span></label>
    <div class="col-sm-2">          
        <input type="text" class="form-control" id="modelname" name="modelname">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2" for="pwd">Number of pages:<span style="color: red;">*</span></label>
    <div class="col-sm-2">          
        <input type="text" class="form-control" id="numberofpages" name="numberofpages">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2" for="pwd">Page names:<span style="color: red;">*</span></label>
    <div class="col-sm-2">          
        <input type="text" class="form-control" id="pagenames" name="pagenames">
    </div>
</div>


       <div class="form-group">
    <label class="col-sm-2" for="pwd">Brand Logo:</label>
    <div class="col-sm-2">          
        <input type="file" class="form-control" id="brandlogo" name="brandlogo" accept="image/*" onchange="showImagePreview(this)">
        <img id="brandlogo-preview" src="#" alt="Brand Logo Preview" style="display: none; max-width: 100px; max-height: 100px;">
    </div>
</div>

       <div class="form-group">
    <label class="col-sm-2" for="productdescription">Product Description:</label>
    <div class="col-sm-10">
        <textarea class="form-control" id="productdescription" placeholder="Product Description" name="productdescription" rows="4"></textarea>
        <small class="text-muted">Max 300 words</small>
    </div>


 





<!--?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?-->
<div id="items-dropzone" class="post-dropzone">

    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $id; ?>" />

            <h4 style="text-align: center; color: green;">COMMAND FRAME</h4>


 <h2>Command Frame Information</h2>



<br><br>
<div class="commandname">
   
      <div class="form-group">
        <label class=" col-sm-2" for="email">Parameter Name</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="cmd_parametername" name="cmd_parametername">
        </div>
      </div>

      <div class="form-group">
        <label class=" col-sm-2" for="pwd">Command Data Flow Direction</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="cmd_data_flow_direction" name="cmd_data_flow_direction">
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Type</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="cmd_frametype" name="cmd_frametype">
        </div>
       </div>

        <div class="form-group">
    <label class=" col-sm-2" for="pwd">Frame size in bytes</label>
    <div class="col-sm-2">          
        <input type="text" class="form-control" id="cmd_framesizeinbytes" name="cmd_framesizeinbytes" oninput="updateTable()">
    </div>
</div>

      

       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Trigger</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="cmd_frametrigger" name="cmd_frametrigger">
        </div>
      </div>


       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Voice Command</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="cmd_voicecommand" name="cmd_voicecommand">
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Voice control Instruction text</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="cmd_voice_control_instruction_text" name="cmd_voice_control_instruction_text">
        </div>
      </div>


       <div class="form-group">
    <label class="col-sm-2" for="productdescription">Remarks (if any)</label>
    <div class="col-sm-10">
        <textarea class="form-control" id="cmd_appnotification" placeholder="Product Description" name="cmd_appnotification" rows="4"></textarea>
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
            <th>UI Description In/Out</th>
            <th>Slider Step Value</th>
            <th>Developer Notes (If Any)</th>
        </tr>
    </thead>
     <tbody>
        <tbody>
    <!-- Initial row with input fields and select dropdowns -->
    <tr id="row_1">
        <!-- Individual fields with IDs -->
        <td id="sno_1">1</td>
        <td><input type="text" id="startBytePosition_cmd" name="startBytePosition_cmd" placeholder="1"></td>
        <td><input type="text" id="endBytePosition_cmd" name="endBytePosition_cmd" placeholder="1"></td>
        

         <td>
            <select id="fieldDescription_cmd" name="fieldDescription_cmd">
                <option value="CommandId" selected>CommandId</option>
                <option value="Function Code" >Function Code</option>
                <option value="Checksum">Checksum</option>
                <option value="Terminator">Terminator</option>
                <option value="ActionId">ActionId</option>
                <option value="Data">Data</option>
                <option value="Error Code">Error Code</option>
                <option value="Operation Code">Operation Code</option>
            </select>
        </td>


        <td><input type="text" id="numberOfBytes_cmd" name="numberOfBytes_cmd" placeholder="1"></td>

        <td><input type="text" id="bitSize_cmd" name="bitSize_cmd" placeholder="1"></td>
        <td>
            <select id="dataType_cmd" name="dataType_cmd">
                <option value="ASCII" selected>ASCII</option>
                <option value="Binary" >Binary</option>
                <option value="SignedInt">Signed Int</option>
                <option value="Float">Float</option>
                <option value="UnsignedInt">Unsigned Int</option>
            </select>
        </td>
        <td>
            <select id="valueDefinition_cmd" name="valueDefinition_cmd">
                <option value="Fixed value" selected>Fixed value</option>
                <option value="Range value" >Range value</option>
                <option value="Mode value">Mode value</option>
                <option value="ASCII">ASCII</option>
         </select>
        </td>



        <td><input type="text" id="displayValueMin_cmd" name="displayValueMin_cmd" placeholder="1"></td>
        <td><input type="text" id="displayValueMax_cmd" name="displayValueMax_cmd" placeholder="1"></td>
        <td><input type="text" id="valueInHexMin_cmd" name="valueInHexMin_cmd" placeholder="1"></td>
        <td><input type="text" id="valueInHexMax_cmd" name="valueInHexMax_cmd" placeholder="1"></td>
        <td>
            <select id="uiType_cmd" name="uiType_cmd">
                <option value="Text(Default)" selected>Text(Default)</option>
                <option value="ToggleSwitch" selected>Toggle Switch</option>
                <option value="Slider">Slider</option>
                <option value="BrightnessSlider">Brightness Slider</option>
                <option value="CCTSlider">CCT Slider</option>
                <option value="SaturationSlider">Saturation Slider</option>
                <option value="HueSlider">Hue Slider</option>
                <option value="HueCircle">Hue Circle</option>
                <option value="PushButton">Push Button (Big)</option>
            </select>
        </td>
        <td><input type="text" id="uiId_cmd" name="uiId_cmd" placeholder="1"></td>
        <td><input type="text" id="uiPageNo_cmd" name="uiPageNo_cmd" placeholder="1"></td>
        <td><input type="text" id="uiDescriptionInOut_cmd" name="uiDescriptionInOut_cmd" placeholder="1"></td>

         <td>
            <select id="uiDescriptionInOut_cmd" name="uiDescriptionInOut_cmd">
                <option value="Read/Write" selected>Read/Write</option>
                <option value="Read" >Read</option>
                <option value="Write">Write</option>
            </select>
        </td>





        <td><input type="text" id="sliderStepValue_cmd" name="sliderStepValue_cmd" placeholder="1"></td>
        <td><input type="text" id="developerNotes_cmd" name="developerNotes_cmd" placeholder="1"></td>
    </tr>
    <tr>
    <td colspan="18"><button onclick="addRow(parseInt(document.getElementById('cmd_framesizeinbytes').value))">+</button></td>
</tr>
</tbody>
</table>
</div>
</div>
</div>



<!--?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?-->
<div id="items-dropzone" class="post-dropzone">

    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $id; ?>" />

            <h4 style="text-align: center; color: green;">Response FRAME</h4>


 <h2>Response Frame Information</h2>



<br><br>
<div class="commandname">

   
   
      <div class="form-group">
        <label class=" col-sm-2" for="email">Parameter Name</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="resp_parametername" name="resp_parametername">
        </div>
      </div>

      <div class="form-group">
        <label class=" col-sm-2" for="pwd">Response Data Flow Direction</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="resp_data_flow_direction" name="resp_data_flow_direction">
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Type</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="resp_frametype" name="resp_frametype">
        </div>
       </div>

        <div class="form-group">
    <label class=" col-sm-2" for="pwd">Response Frame size in bytes</label>
    <div class="col-sm-2">          
        <input type="text" class="form-control" id="resp_framesizeinbytes" name="resp_framesizeinbytes" oninput="respupdateTable()">
    </div>
</div>

      

       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Trigger</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="resp_frametrigger" name="resp_frametrigger">
        </div>
      </div>


       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Voice Command</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="resp_voicecommand" name="resp_voicecommand">
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Voice control Instruction text</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="resp_voice_control_instruction_text" name="resp_voice_control_instruction_text">
        </div>
      </div>


      <div class="form-group">
    <label class="col-sm-2" for="productdescription">Remarks (if any)</label>
    <div class="col-sm-10">
        <textarea class="form-control" id="resp_appnotification" placeholder="Product Description" name="resp_appnotification" rows="4" oninput="checkWordCount(this)"></textarea>
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
            <th>UI Description In/Out</th>
            <th>Slider Step Value</th>
            <th>Developer Notes (If Any)</th>
        </tr>
    </thead>
     <tbody>
        <tbody>
    <!-- Initial row with input fields and select dropdowns -->
    <tr id="row_1">
        <!-- Individual fields with IDs -->
        <td id="sno_1">1</td>
        <td><input type="text" id="startBytePosition_resp" name="startBytePosition_resp" placeholder="1"></td>
        <td><input type="text" id="endBytePosition_resp" name="endBytePosition_resp" placeholder="1"></td>
        

         <td>
            <select id="fieldDescription_resp" name="fieldDescription_resp">
                <option value="CommandId" selected>CommandId</option>
                <option value="Function Code" >Function Code</option>
                <option value="Checksum">Checksum</option>
                <option value="Terminator">Terminator</option>
                <option value="ActionId">ActionId</option>
                <option value="Data">Data</option>
                <option value="Error Code">Error Code</option>
                <option value="Operation Code">Operation Code</option>
            </select>
        </td>
        <td><input type="text" id="numberOfBytes_resp" name="numberOfBytes_resp" placeholder="1"></td>
        <td><input type="text" id="bitSize_resp" name="bitSize_resp" placeholder="1"></td>
        <td>
            <select id="dataType_resp" name="dataType_resp">
                <option value="ASCII" selected>ASCII</option>
                <option value="Binary" >Binary</option>
                <option value="SignedInt">Signed Int</option>
                <option value="Float">Float</option>
                <option value="UnsignedInt">Unsigned Int</option>
            </select>
        </td>

         <td>
            <select id="valueDefinition_resp" name="valueDefinition_resp">
                <option value="Fixed value" selected>Fixed value</option>
                <option value="Range value" >Range value</option>
                <option value="Mode value">Mode value</option>
                <option value="ASCII">ASCII</option>
         </select>
        </td>

               <td><input type="text" id="displayValueMin_resp" name="displayValueMin_resp" placeholder="1"></td>
        <td><input type="text" id="displayValueMax_resp" name="displayValueMax_resp" placeholder="1"></td>
        <td><input type="text" id="valueInHexMin_resp" name="valueInHexMin_resp" placeholder="1"></td>
        <td><input type="text" id="valueInHexMax_resp" name="valueInHexMax_resp" placeholder="1"></td>
        <td>
            <select id="uiType_resp" name="uiType_resp">
                <option value="Text(Default)" selected>Text(Default)</option>
                <option value="ToggleSwitch" selected>Toggle Switch</option>
                <option value="Slider">Slider</option>
                <option value="BrightnessSlider">Brightness Slider</option>
                <option value="CCTSlider">CCT Slider</option>
                <option value="SaturationSlider">Saturation Slider</option>
                <option value="HueSlider">Hue Slider</option>
                <option value="HueCircle">Hue Circle</option>
                <option value="PushButton">Push Button (Big)</option>
            </select>
        </td>
        <td><input type="text" id="uiId_resp" name="uiId_resp" placeholder="1"></td>
        <td><input type="text" id="uiPageNo_resp" name="uiPageNo_resp" placeholder="1"></td>



     <td>
            <select id="uiDescriptionInOut_resp" name="uiDescriptionInOut_resp">
                <option value="Read/Write" selected>Read/Write</option>
                <option value="Read" >Read</option>
                <option value="Write">Write</option>
            </select>
        </td>

       
        <td><input type="text" id="sliderStepValue_resp" name="sliderStepValue_resp" placeholder="1"></td>
        <td><input type="text" id="developerNotes_resp" name="developerNotes_resp" placeholder="1"></td>
    </tr>
    <tr>
        <td colspan="18"><button onclick="respaddRow(parseInt(document.getElementById('resp_framesizeinbytes').value))">+</button></td>
    </tr>
</tbody>
</table>



  

</div>
</div>
</div>





<!--?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?-->
<div id="items-dropzone" class="post-dropzone">

    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $id; ?>" />

            <h4 style="text-align: center; color: green;">ERROR FRAME</h4>


 <h2>Error Frame Information</h2>



<br><br>
<div class="commandname">

   
   
      <div class="form-group">
        <label class=" col-sm-2" for="email">Parameter Name</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="error_parametername" name="error_parametername">
        </div>
      </div>

      <div class="form-group">
        <label class=" col-sm-2" for="pwd">Command Data Flow Direction</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="error_data_flow_direction" name="error_data_flow_direction">
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Type</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="error_frametype" name="error_frametype">
        </div>
       </div>

        <div class="form-group">
    <label class=" col-sm-2" for="pwd">Error Frame size in bytes</label>
    <div class="col-sm-2">          
        <input type="text" class="form-control" id="error_framesizeinbytes" name="error_framesizeinbytes" oninput="errorupdateTable()">
    </div>
</div>

      

       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Trigger</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="error_frametrigger" name="error_frametrigger">
        </div>
      </div>


       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Voice Command</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="error_voicecommand" name="error_voicecommand">
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Voice control Instruction text</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="error_voice_control_instruction_text" name="error_voice_control_instruction_text">
        </div>
      </div>


       <div class="form-group">
    <label class="col-sm-2" for="productdescription">Remarks (if any)</label>
    <div class="col-sm-10">
        <textarea class="form-control" id="error_appnotification" name="error_appnotification" rows="4"></textarea>
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
            <th>UI Description In/Out</th>
            <th>Slider Step Value</th>
            <th>Developer Notes (If Any)</th>
        </tr>
    </thead>
     <tbody>
        <tbody>
    <!-- Initial row with input fields and select dropdowns -->
    <tr id="row_1">
        <!-- Individual fields with IDs -->
        <td id="sno_1">1</td>
        <td><input type="text" id="startBytePosition_error" name="startBytePosition_error" placeholder="1"></td>
        <td><input type="text" id="endBytePosition_error" name="endBytePosition_error" placeholder="1"></td>
        

         <td>
            <select id="fieldDescription_error" name="fieldDescription_error">
                <option value="CommandId" selected>CommandId</option>
                <option value="Function Code" >Function Code</option>
                <option value="Checksum">Checksum</option>
                <option value="Terminator">Terminator</option>
                <option value="ActionId">ActionId</option>
                <option value="Data">Data</option>
                <option value="Error Code">Error Code</option>
                <option value="Operation Code">Operation Code</option>
            </select>
        </td>
        <td><input type="text" id="numberOfBytes_error" name="numberOfBytes_error" placeholder="1"></td>
        <td><input type="text" id="bitSize_error" name="bitSize_error" placeholder="1"></td>
        <td>
            <select id="dataType_error" name="dataType_error">
                <option value="ASCII" selected>ASCII</option>
                <option value="Binary" >Binary</option>
                <option value="SignedInt">Signed Int</option>
                <option value="Float">Float</option>
                <option value="UnsignedInt">Unsigned Int</option>
            </select>
        </td>

         <td>
            <select id="valueDefinition_error" name="valueDefinition_error">
                <option value="Fixed value" selected>Fixed value</option>
                <option value="Range value" >Range value</option>
                <option value="Mode value">Mode value</option>
                <option value="ASCII">ASCII</option>
         </select>
        </td>

                <td><input type="text" id="displayValueMin_error" name="displayValueMin_error" placeholder="1"></td>
        <td><input type="text" id="displayValueMax_error" name="displayValueMax_error" placeholder="1"></td>
        <td><input type="text" id="valueInHexMin_error" name="valueInHexMin_error" placeholder="1"></td>
        <td><input type="text" id="valueInHexMax_error" name="valueInHexMax_error" placeholder="1"></td>
        <td>
            <select id="uiType_error" name="uiType_error">
                <option value="Text(Default)" selected>Text(Default)</option>
                <option value="ToggleSwitch" selected>Toggle Switch</option>
                <option value="Slider">Slider</option>
                <option value="BrightnessSlider">Brightness Slider</option>
                <option value="CCTSlider">CCT Slider</option>
                <option value="SaturationSlider">Saturation Slider</option>
                <option value="HueSlider">Hue Slider</option>
                <option value="HueCircle">Hue Circle</option>
                <option value="PushButton">Push Button (Big)</option>
            </select>
        </td>
        <td><input type="text" id="uiId_error" name="uiId_error" placeholder="1"></td>
        <td><input type="text" id="uiPageNo_error" name="uiPageNo_error" placeholder="1"></td>

  <td>
            <select id="uiDescriptionInOut_cmd" name="uiDescriptionInOut_cmd">
                <option value="Read/Write" selected>Read/Write</option>
                <option value="Read" >Read</option>
                <option value="Write">Write</option>
            </select>
        </td>


         <td><input type="text" id="sliderStepValue_error" name="sliderStepValue_error" placeholder="1"></td>
        <td><input type="text" id="developerNotes_error" name="developerNotes_error" placeholder="1"></td>
    </tr>
    <tr>
        <td colspan="18"><button onclick="erroraddRow(parseInt(document.getElementById('error_framesizeinbytes').value))">+</button></td>
    </tr>
</tbody>
</table>


</div>
</div>
</div>






<div class="modal-footer">   
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>




     
<script type="text/javascript">


/***************additional_table******************************************/


function addRow(index) {
    var table = document.getElementById("additionalTable");
    var newRow = table.insertRow(-1); // Insert at the end of the table
    var cells = [
        '<td>' + index + '</td>', // S.No
        '<td><input type="text" name="startBytePosition_cmd_' + index + '"></td>',
        '<td><input type="text" name="endBytePosition_cmd_' + index + '"></td>',
        '<td><select name="fieldDescription_cmd_' + index + '">' +
            '<option value="CommandId">CommandId</option>' +
            '<option value="Function Code">Function Code</option>' +
            '<option value="Checksum">Checksum</option>' +
            '<option value="Terminator">Terminator</option>' +
            '<option value="ActionId">ActionId</option>' +
            '<option value="Data">Data</option>' +
            '<option value="Error Code">Error Code</option>' +
            '<option value="Operation Code">Operation Code</option>' +
            '</select></td>',
        '<td><input type="text" name="numberOfBytes_cmd_' + index + '"></td>',
        '<td><input type="text" name="bitSize_cmd_' + index + '"></td>',

        '<td><select name="dataType_cmd_' + index + '">' +
            '<option value="ASCII">ASCII</option>' +
            '<option value="Binary">Binary</option>' +
            '<option value="SignedInt">Signed Int</option>' +
            '<option value="Float">Float</option>' +
            '<option value="UnsignedInt">Unsigned Int</option>' +
            '</select></td>',


           '<td><select name="valueDefinition_cmd_' + index + '">' +
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
             '<option value="Text(Default)">Text(Default)</option>' +
            '<option value="ToggleSwitch">Toggle Switch</option>' +
            '<option value="Slider">Slider</option>' +
            '<option value="BrightnessSlider">Brightness Slider</option>' +
            '<option value="CCTSlider">CCT Slider</option>' +
            '<option value="SaturationSlider">Saturation Slider</option>' +
            '<option value="HueSlider">Hue Slider</option>' +
            '<option value="HueCircle">Hue Circle</option>' +
            '<option value="PushButton">Push Button (Big)</option>' +
            '</select></td>',
        '<td><input type="text" name="uiId_cmd_' + index + '"></td>',
        '<td><input type="text" name="uiPageNo_cmd_' + index + '"></td>',


         '<td><select name="uiDescriptionInOut_cmd_' + index + '">' +
            '<option value="Read/Write">Read/Write</option>' +
            '<option value="Read">Read</option>' +
            '<option value="Write">Write</option>' +
           '</select></td>',


        '<td><input type="text" name="sliderStepValue_cmd_' + index + '"></td>',
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

    debounceTimer = setTimeout(function() {
        var frameSizeInput = document.getElementById('cmd_framesizeinbytes');
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

function respaddRow(index) {
    var table = document.getElementById("responseTable");
    var newRow = table.insertRow(-1); // Insert at the end of the table
    var cells = [
        '<td>' + index + '</td>', // S.No
        '<td><input type="text" name="startBytePosition_resp_' + index + '"></td>',
        '<td><input type="text" name="endBytePosition_resp_' + index + '"></td>',
        '<td><select name="fieldDescription_resp_' + index + '">' +
            '<option value="CommandId">CommandId</option>' +
            '<option value="Function Code">Function Code</option>' +
            '<option value="Checksum">Checksum</option>' +
            '<option value="Terminator">Terminator</option>' +
            '<option value="ActionId">ActionId</option>' +
            '<option value="Data">Data</option>' +
            '<option value="Error Code">Error Code</option>' +
            '<option value="Operation Code">Operation Code</option>' +
            '</select></td>',
        '<td><input type="text" name="numberOfBytes_resp_' + index + '"></td>',
        '<td><input type="text" name="bitSize_resp_' + index + '"></td>',
        '<td><select name="dataType_resp_' + index + '">' +
            '<option value="ASCII">ASCII</option>' +
            '<option value="Binary">Binary</option>' +
            '<option value="SignedInt">Signed Int</option>' +
            '<option value="Float">Float</option>' +
            '<option value="UnsignedInt">Unsigned Int</option>' +
            '</select></td>',

          '<td><select name="valueDefinition_resp_' + index + '">' +
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
            '<option value="Text(Default)">Text(Default)</option>' +
            '<option value="ToggleSwitch">Toggle Switch</option>' +
            '<option value="Slider">Slider</option>' +
            '<option value="BrightnessSlider">Brightness Slider</option>' +
            '<option value="CCTSlider">CCT Slider</option>' +
            '<option value="SaturationSlider">Saturation Slider</option>' +
            '<option value="HueSlider">Hue Slider</option>' +
            '<option value="HueCircle">Hue Circle</option>' +
            '<option value="PushButton">Push Button (Big)</option>' +
            '</select></td>',
        '<td><input type="text" name="uiId_resp_' + index + '"></td>',
        '<td><input type="text" name="uiPageNo_resp_' + index + '"></td>',


       '<td><select name="uiDescriptionInOut_resp_' + index + '">' +
            '<option value="Read/Write">Read/Write</option>' +
            '<option value="Read">Read</option>' +
            '<option value="Write">Write</option>' +
           '</select></td>',


        '<td><input type="text" name="sliderStepValue_resp_' + index + '"></td>',
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

    var frameSize = parseInt(document.getElementById('resp_framesizeinbytes').value);
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

    debounceTimer = setTimeout(function() {
        var frameSizeInput = document.getElementById('resp_framesizeinbytes');
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

function erroraddRow(index) {
    var table = document.getElementById("errorTable");
    var newRow = table.insertRow(-1); // Insert at the end of the table
    var cells = [
        '<td>' + index + '</td>', // S.No
        '<td><input type="text" name="startBytePosition_error_' + index + '"></td>',
        '<td><input type="text" name="endBytePosition_error_' + index + '"></td>',
        '<td><select name="fieldDescription_error_' + index + '">' +
            '<option value="CommandId">CommandId</option>' +
            '<option value="Function Code">Function Code</option>' +
            '<option value="Checksum">Checksum</option>' +
            '<option value="Terminator">Terminator</option>' +
            '<option value="ActionId">ActionId</option>' +
            '<option value="Data">Data</option>' +
            '<option value="Error Code">Error Code</option>' +
            '<option value="Operation Code">Operation Code</option>' +
            '</select></td>',
        '<td><input type="text" name="numberOfBytes_error_' + index + '"></td>',
        '<td><input type="text" name="bitSize_error_' + index + '"></td>',
        '<td><select name="dataType_error_' + index + '">' +
            '<option value="ASCII">ASCII</option>' +
            '<option value="Binary">Binary</option>' +
            '<option value="SignedInt">Signed Int</option>' +
            '<option value="Float">Float</option>' +
            '<option value="UnsignedInt">Unsigned Int</option>' +
            '</select></td>',


            '<td><select name="valueDefinition_error_' + index + '">' +
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
            '<option value="Text(Default)">Text(Default)</option>' +
            '<option value="ToggleSwitch">Toggle Switch</option>' +
            '<option value="Slider">Slider</option>' +
            '<option value="BrightnessSlider">Brightness Slider</option>' +
            '<option value="CCTSlider">CCT Slider</option>' +
            '<option value="SaturationSlider">Saturation Slider</option>' +
            '<option value="HueSlider">Hue Slider</option>' +
            '<option value="HueCircle">Hue Circle</option>' +
            '<option value="PushButton">Push Button (Big)</option>' +
            '</select></td>',
        '<td><input type="text" name="uiId_error' + index + '"></td>',
        '<td><input type="text" name="uiPageNo_error_' + index + '"></td>',

'<td><select name="uiDescriptionInOut_error_' + index + '">' +
            '<option value="Read/Write">Read/Write</option>' +
            '<option value="Read">Read</option>' +
            '<option value="Write">Write</option>' +
           '</select></td>',

        
        '<td><input type="text" name="sliderStepValue_error_' + index + '"></td>',
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

    var frameSize = parseInt(document.getElementById('error_framesizeinbytes').value);
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

    debounceTimer = setTimeout(function() {
        var frameSizeInput = document.getElementById('error_framesizeinbytes');
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

    reader.onloadend = function() {
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

</script>


<?php echo form_close(); ?>